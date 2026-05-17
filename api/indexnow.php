<?php
/**
 * Mulligan's Grille - IndexNow API Endpoint
 *
 * Submits URLs to IndexNow for instant search engine indexing.
 * Supports Bing, Yandex, and other IndexNow-compatible search engines.
 *
 * Required Environment Variables:
 * - INDEXNOW_KEY: The key file name (without .txt extension)
 * - INDEXNOW_SECRET: Secret token for API authentication
 *
 * Security Features:
 * - Secret token authentication via X-IndexNow-Secret header
 * - IP-based rate limiting (10 requests per hour)
 * - Input validation
 * - JSON logging
 */

// CORS configuration
$allowedOrigins = [
    'https://mulligans-grille.com',
    'https://www.mulligans-grille.com',
    'http://localhost:8000',
    'http://localhost:3000'
];

$origin = isset($_SERVER['HTTP_ORIGIN']) ? $_SERVER['HTTP_ORIGIN'] : '';
if (in_array($origin, $allowedOrigins)) {
    header('Access-Control-Allow-Origin: ' . $origin);
} else {
    header('Access-Control-Allow-Origin: https://mulligans-grille.com');
}

header('Content-Type: application/json');
header('Access-Control-Allow-Methods: POST, OPTIONS, GET');
header('Access-Control-Allow-Headers: Content-Type, X-IndexNow-Secret');

// Rate limiting configuration
define('RATE_LIMIT_REQUESTS', 10);
define('RATE_LIMIT_WINDOW', 3600); // 1 hour in seconds
define('RATE_LIMIT_FILE', sys_get_temp_dir() . '/mulligans-grille.com_indexnow_ratelimit.json');
define('LOG_FILE', sys_get_temp_dir() . '/mulligans-grille.com_indexnow_log.json');
define('SITE_HOST', 'mulligans-grille.com');

/**
 * Check and enforce rate limiting by IP address
 * Uses file locking to prevent race conditions
 */
function checkRateLimit($ip) {
    $fp = fopen(RATE_LIMIT_FILE, 'c+');
    if (!$fp) {
        // If we can't open the file, allow the request but log the error
        error_log('Rate limit: Unable to open rate limit file');
        return true;
    }

    // Acquire exclusive lock for entire read-modify-write cycle
    if (!flock($fp, LOCK_EX)) {
        fclose($fp);
        error_log('Rate limit: Unable to acquire lock');
        return true;
    }

    // Read existing data
    $content = '';
    $stat = fstat($fp);
    if ($stat['size'] > 0) {
        $content = fread($fp, $stat['size']);
    }
    $data = json_decode($content, true) ?: [];

    $now = time();
    // Clean up expired entries
    foreach ($data as $addr => $timestamps) {
        $data[$addr] = array_filter($timestamps, function($ts) use ($now) {
            return ($now - $ts) < RATE_LIMIT_WINDOW;
        });
        if (empty($data[$addr])) {
            unset($data[$addr]);
        }
    }

    // Check current IP
    $ipRequests = isset($data[$ip]) ? $data[$ip] : [];
    if (count($ipRequests) >= RATE_LIMIT_REQUESTS) {
        flock($fp, LOCK_UN);
        fclose($fp);
        return false;
    }

    // Add this request
    $ipRequests[] = $now;
    $data[$ip] = $ipRequests;

    // Write back and release lock
    ftruncate($fp, 0);
    rewind($fp);
    fwrite($fp, json_encode($data));
    fflush($fp);
    flock($fp, LOCK_UN);
    fclose($fp);

    return true;
}

/**
 * Log IndexNow submission
 */
function logSubmission($data) {
    $logs = [];
    if (file_exists(LOG_FILE)) {
        $content = file_get_contents(LOG_FILE);
        $logs = json_decode($content, true) ?: [];
    }

    // Keep only last 100 entries
    if (count($logs) >= 100) {
        $logs = array_slice($logs, -99);
    }

    $logs[] = array_merge(['timestamp' => date('c')], $data);
    file_put_contents(LOG_FILE, json_encode($logs, JSON_PRETTY_PRINT), LOCK_EX);
}

/**
 * Load environment variables from .env file
 */
function loadEnv() {
    $envFile = dirname(__DIR__) . '/.env';
    if (file_exists($envFile)) {
        $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        foreach ($lines as $line) {
            if (strpos($line, '#') === 0) continue;
            if (strpos($line, '=') !== false) {
                list($key, $value) = explode('=', $line, 2);
                $key = trim($key);
                $value = trim($value, " \t\n\r\0\x0B\"'");
                $_ENV[$key] = $value;
            }
        }
    }
}

/**
 * Parse URLs from sitemap.xml
 */
function getSitemapUrls() {
    $sitemapPath = dirname(__DIR__) . '/sitemap.xml';
    if (!file_exists($sitemapPath)) {
        return [];
    }

    // Disable external entity loading to prevent XXE attacks
    $previousValue = libxml_disable_entity_loader(true);
    $xml = simplexml_load_file($sitemapPath);
    libxml_disable_entity_loader($previousValue);

    if (!$xml) {
        return [];
    }

    $urls = [];
    foreach ($xml->url as $urlEntry) {
        $urls[] = (string)$urlEntry->loc;
    }

    return $urls;
}

/**
 * Validate URL format
 */
function isValidUrl($url) {
    if (!filter_var($url, FILTER_VALIDATE_URL)) {
        return false;
    }

    $parsed = parse_url($url);
    if (!isset($parsed['host'])) {
        return false;
    }

    // Must be our domain
    $host = strtolower($parsed['host']);
    return ($host === SITE_HOST || $host === 'www.' . SITE_HOST);
}

/**
 * Submit URLs to IndexNow API (batch)
 */
function submitBatchUrls($urls, $key) {
    $payload = [
        'host' => SITE_HOST,
        'key' => $key,
        'keyLocation' => 'https://' . SITE_HOST . '/' . $key . '.txt',
        'urlList' => array_slice($urls, 0, 10000) // IndexNow limit
    ];

    $ch = curl_init('https://api.indexnow.org/indexnow');
    curl_setopt_array($ch, [
        CURLOPT_POST => true,
        CURLOPT_POSTFIELDS => json_encode($payload),
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_HTTPHEADER => ['Content-Type: application/json; charset=utf-8'],
        CURLOPT_TIMEOUT => 30
    ]);

    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $curlError = curl_error($ch);
    curl_close($ch);

    return [
        'success' => in_array($httpCode, [200, 202]),
        'httpCode' => $httpCode,
        'response' => $response,
        'error' => $curlError ?: null
    ];
}

/**
 * Submit single URL to IndexNow API
 */
function submitSingleUrl($url, $key) {
    $queryParams = http_build_query([
        'url' => $url,
        'key' => $key
    ]);

    $ch = curl_init('https://api.indexnow.org/indexnow?' . $queryParams);
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_TIMEOUT => 30
    ]);

    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $curlError = curl_error($ch);
    curl_close($ch);

    return [
        'success' => in_array($httpCode, [200, 202]),
        'httpCode' => $httpCode,
        'response' => $response,
        'error' => $curlError ?: null
    ];
}

// Load environment variables
loadEnv();

// Get configuration
$indexNowKey = $_ENV['INDEXNOW_KEY'] ?? getenv('INDEXNOW_KEY');
$indexNowSecret = $_ENV['INDEXNOW_SECRET'] ?? getenv('INDEXNOW_SECRET');

// Handle preflight OPTIONS request
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

// Handle GET request - return status and available actions
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $sitemapUrls = getSitemapUrls();
    echo json_encode([
        'status' => 'ok',
        'service' => 'IndexNow',
        'host' => SITE_HOST,
        'keyConfigured' => !empty($indexNowKey),
        'sitemapUrlCount' => count($sitemapUrls),
        'availableModes' => [
            'all' => 'Submit all sitemap URLs',
            'batch' => 'Submit specific URLs array',
            'single' => 'Submit one URL'
        ],
        'usage' => [
            'headers' => ['X-IndexNow-Secret: your-secret'],
            'body' => '{"mode": "all"} or {"mode": "single", "url": "..."} or {"mode": "batch", "urls": [...]}'
        ]
    ]);
    exit;
}

// Only allow POST requests for submissions
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'error' => 'Method not allowed']);
    exit;
}

// Validate configuration
if (empty($indexNowKey)) {
    error_log('INDEXNOW_KEY not configured');
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'Server configuration error: key not set']);
    exit;
}

if (empty($indexNowSecret)) {
    error_log('INDEXNOW_SECRET not configured');
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'Server configuration error: secret not set']);
    exit;
}

// Validate secret token
$providedSecret = $_SERVER['HTTP_X_INDEXNOW_SECRET'] ?? '';
if (!hash_equals($indexNowSecret, $providedSecret)) {
    http_response_code(401);
    echo json_encode(['success' => false, 'error' => 'Invalid or missing authentication token']);
    exit;
}

// Get client IP
$clientIp = $_SERVER['HTTP_X_FORWARDED_FOR'] ?? $_SERVER['HTTP_X_REAL_IP'] ?? $_SERVER['REMOTE_ADDR'] ?? 'unknown';
if (strpos($clientIp, ',') !== false) {
    $clientIp = trim(explode(',', $clientIp)[0]);
}

// Check rate limit
if (!checkRateLimit($clientIp)) {
    http_response_code(429);
    echo json_encode(['success' => false, 'error' => 'Rate limit exceeded. Maximum 10 requests per hour.']);
    exit;
}

// Parse input
$input = json_decode(file_get_contents('php://input'), true);
if (!$input) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Invalid JSON body']);
    exit;
}

$mode = $input['mode'] ?? '';

// Process based on mode
switch ($mode) {
    case 'all':
        // Submit all sitemap URLs
        $urls = getSitemapUrls();
        if (empty($urls)) {
            http_response_code(400);
            echo json_encode(['success' => false, 'error' => 'No URLs found in sitemap']);
            exit;
        }

        $result = submitBatchUrls($urls, $indexNowKey);
        logSubmission([
            'mode' => 'all',
            'urlCount' => count($urls),
            'httpCode' => $result['httpCode'],
            'success' => $result['success'],
            'ip' => $clientIp
        ]);

        if ($result['success']) {
            echo json_encode([
                'success' => true,
                'message' => 'Submitted ' . count($urls) . ' URLs to IndexNow',
                'urlCount' => count($urls),
                'httpCode' => $result['httpCode']
            ]);
        } else {
            http_response_code(502);
            echo json_encode([
                'success' => false,
                'error' => 'IndexNow API error',
                'httpCode' => $result['httpCode'],
                'details' => getHttpCodeMessage($result['httpCode'])
            ]);
        }
        break;

    case 'batch':
        // Submit specific URLs
        $urls = $input['urls'] ?? [];
        if (!is_array($urls) || empty($urls)) {
            http_response_code(400);
            echo json_encode(['success' => false, 'error' => 'URLs array is required for batch mode']);
            exit;
        }

        // Validate URLs
        $invalidUrls = [];
        foreach ($urls as $url) {
            if (!isValidUrl($url)) {
                $invalidUrls[] = $url;
            }
        }
        if (!empty($invalidUrls)) {
            http_response_code(422);
            echo json_encode([
                'success' => false,
                'error' => 'Invalid URLs detected',
                'invalidUrls' => $invalidUrls
            ]);
            exit;
        }

        $result = submitBatchUrls($urls, $indexNowKey);
        logSubmission([
            'mode' => 'batch',
            'urlCount' => count($urls),
            'httpCode' => $result['httpCode'],
            'success' => $result['success'],
            'ip' => $clientIp
        ]);

        if ($result['success']) {
            echo json_encode([
                'success' => true,
                'message' => 'Submitted ' . count($urls) . ' URLs to IndexNow',
                'urlCount' => count($urls),
                'httpCode' => $result['httpCode']
            ]);
        } else {
            http_response_code(502);
            echo json_encode([
                'success' => false,
                'error' => 'IndexNow API error',
                'httpCode' => $result['httpCode'],
                'details' => getHttpCodeMessage($result['httpCode'])
            ]);
        }
        break;

    case 'single':
        // Submit single URL
        $url = $input['url'] ?? '';
        if (empty($url)) {
            http_response_code(400);
            echo json_encode(['success' => false, 'error' => 'URL is required for single mode']);
            exit;
        }

        if (!isValidUrl($url)) {
            http_response_code(422);
            echo json_encode(['success' => false, 'error' => 'Invalid URL format or domain']);
            exit;
        }

        $result = submitSingleUrl($url, $indexNowKey);
        logSubmission([
            'mode' => 'single',
            'url' => $url,
            'httpCode' => $result['httpCode'],
            'success' => $result['success'],
            'ip' => $clientIp
        ]);

        if ($result['success']) {
            echo json_encode([
                'success' => true,
                'message' => 'Submitted URL to IndexNow',
                'url' => $url,
                'httpCode' => $result['httpCode']
            ]);
        } else {
            http_response_code(502);
            echo json_encode([
                'success' => false,
                'error' => 'IndexNow API error',
                'httpCode' => $result['httpCode'],
                'details' => getHttpCodeMessage($result['httpCode'])
            ]);
        }
        break;

    default:
        http_response_code(400);
        echo json_encode([
            'success' => false,
            'error' => 'Invalid mode. Use "all", "batch", or "single"'
        ]);
}

/**
 * Get human-readable message for HTTP code
 */
function getHttpCodeMessage($code) {
    $messages = [
        200 => 'OK - URL submitted successfully',
        202 => 'Accepted - URL received and pending processing',
        400 => 'Bad Request - Invalid format',
        403 => 'Forbidden - Key file not found or invalid',
        422 => 'Unprocessable Entity - Invalid URL format',
        429 => 'Too Many Requests - Rate limited by IndexNow'
    ];
    return $messages[$code] ?? 'Unknown response code';
}
