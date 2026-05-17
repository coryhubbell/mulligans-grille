<?php
/**
 * Mulligan's Grille - Contact Form API Endpoint
 *
 * Receives form submissions and sends via Postmark API.
 *
 * Required Environment Variables:
 * - POSTMARK_SERVER_TOKEN: Server API token from Postmark dashboard
 * - POSTMARK_FROM_EMAIL: Verified sender email (e.g., noreply@mulligans-grille.com)
 * - CONTACT_TO_EMAIL: Recipient email (e.g., {{CONTACT_EMAIL}})
 *
 * Security Features:
 * - CSRF token validation
 * - IP-based rate limiting (5 requests per 15 minutes)
 * - Honeypot spam detection
 * - Input sanitization and validation
 */

// Configure secure session cookies before starting
session_set_cookie_params([
    'lifetime' => 3600,
    'path' => '/',
    'secure' => true,
    'httponly' => true,
    'samesite' => 'Strict'
]);

// Start session for CSRF token
session_start();

// CORS configuration - allow both www and non-www, plus localhost for development
$allowedOrigins = [
    'https://mulligans-grille.com',
    'https://www.mulligans-grille.com',
    'http://localhost:8000',
    'http://localhost:3000',
    'http://127.0.0.1:8000'
];

// Check for custom allowed origin from environment
$customOrigin = getenv('CORS_ALLOWED_ORIGIN');
if ($customOrigin) {
    $allowedOrigins[] = $customOrigin;
}

// Get the requesting origin
$origin = isset($_SERVER['HTTP_ORIGIN']) ? $_SERVER['HTTP_ORIGIN'] : '';

// Set CORS headers based on origin
if (in_array($origin, $allowedOrigins)) {
    header('Access-Control-Allow-Origin: ' . $origin);
} else {
    // Default to production domain
    header('Access-Control-Allow-Origin: https://mulligans-grille.com');
}

// Set response headers
header('Content-Type: application/json');
header('Access-Control-Allow-Methods: POST, OPTIONS, GET');
header('Access-Control-Allow-Headers: Content-Type, X-CSRF-Token');
header('Access-Control-Allow-Credentials: true');

// Rate limiting configuration
define('RATE_LIMIT_REQUESTS', 5);
define('RATE_LIMIT_WINDOW', 900); // 15 minutes in seconds
define('RATE_LIMIT_FILE', sys_get_temp_dir() . '/mulligans-grille.com_contact_ratelimit.json');

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
        return false; // Rate limited
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
 * Generate CSRF token
 */
function generateCsrfToken() {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        $_SESSION['csrf_token_time'] = time();
    }
    return $_SESSION['csrf_token'];
}

/**
 * Validate CSRF token
 */
function validateCsrfToken($token) {
    if (empty($_SESSION['csrf_token']) || empty($token)) {
        return false;
    }
    // Token expires after 1 hour
    if (time() - $_SESSION['csrf_token_time'] > 3600) {
        unset($_SESSION['csrf_token'], $_SESSION['csrf_token_time']);
        return false;
    }
    return hash_equals($_SESSION['csrf_token'], $token);
}

// Handle GET request - return CSRF token
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $token = generateCsrfToken();
    echo json_encode(['csrf_token' => $token]);
    exit;
}

// Handle preflight OPTIONS request
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

// Only allow POST requests for form submission
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'error' => 'Method not allowed']);
    exit;
}

// Get client IP (handle proxies)
$clientIp = $_SERVER['HTTP_X_FORWARDED_FOR'] ?? $_SERVER['HTTP_X_REAL_IP'] ?? $_SERVER['REMOTE_ADDR'] ?? 'unknown';
if (strpos($clientIp, ',') !== false) {
    $clientIp = trim(explode(',', $clientIp)[0]);
}

// Check rate limit
if (!checkRateLimit($clientIp)) {
    http_response_code(429);
    echo json_encode(['success' => false, 'error' => 'Too many requests. Please try again later.']);
    exit;
}

// Load environment variables from .env file if it exists
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

// Get configuration from environment (check $_ENV first for .env file values, then system env)
$postmarkToken = $_ENV['POSTMARK_SERVER_TOKEN'] ?? getenv('POSTMARK_SERVER_TOKEN');
$fromEmail = $_ENV['POSTMARK_FROM_EMAIL'] ?? getenv('POSTMARK_FROM_EMAIL') ?: 'noreply@mulligans-grille.com';
$toEmail = $_ENV['CONTACT_TO_EMAIL'] ?? getenv('CONTACT_TO_EMAIL') ?: '{{CONTACT_EMAIL}}';

// Validate configuration
if (empty($postmarkToken)) {
    error_log('POSTMARK_SERVER_TOKEN not configured');
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'Server configuration error']);
    exit;
}

// Parse input
$contentType = isset($_SERVER['CONTENT_TYPE']) ? $_SERVER['CONTENT_TYPE'] : '';
if (strpos($contentType, 'application/json') !== false) {
    $input = json_decode(file_get_contents('php://input'), true);
} else {
    $input = $_POST;
}

// Validate CSRF token (from header or body)
$csrfToken = $_SERVER['HTTP_X_CSRF_TOKEN'] ?? $input['csrf_token'] ?? '';
if (!validateCsrfToken($csrfToken)) {
    http_response_code(403);
    echo json_encode(['success' => false, 'error' => 'Invalid or expired security token. Please refresh the page and try again.']);
    exit;
}

// Honeypot spam check - if filled, silently "succeed"
if (!empty($input['website']) || !empty($input['url'])) {
    // Bot detected - fake success response
    echo json_encode(['success' => true, 'message' => 'Thank you for your message!']);
    exit;
}

// Extract and sanitize form fields
$name = isset($input['name']) ? trim(strip_tags($input['name'])) : '';
$email = isset($input['email']) ? trim(filter_var($input['email'], FILTER_SANITIZE_EMAIL)) : '';
$phone = isset($input['phone']) ? trim(strip_tags($input['phone'])) : '';
$message = isset($input['message']) ? trim(strip_tags($input['message'])) : '';

// Validate required fields
$errors = [];
if (empty($name)) {
    $errors[] = 'Name is required';
}
if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $errors[] = 'Valid email is required';
}
if (empty($message)) {
    $errors[] = 'Message is required';
}
if (strlen($name) > 100) {
    $errors[] = 'Name is too long';
}
if (strlen($message) > 5000) {
    $errors[] = 'Message is too long';
}

if (!empty($errors)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => implode(', ', $errors)]);
    exit;
}

// Build email content with HTML encoding for defense in depth
$nameHtml = htmlspecialchars($name, ENT_QUOTES, 'UTF-8');
$emailHtml = htmlspecialchars($email, ENT_QUOTES, 'UTF-8');
$phoneDisplay = !empty($phone) ? htmlspecialchars($phone, ENT_QUOTES, 'UTF-8') : 'Not provided';
$messageHtml = nl2br(htmlspecialchars($message, ENT_QUOTES, 'UTF-8'));
$emailSubject = "New Contact Form Submission from $name";
$emailBody = <<<HTML
<!DOCTYPE html>
<html>
<head>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background: #1a1a2e; color: white; padding: 20px; text-align: center; }
        .content { padding: 20px; background: #f9f9f9; }
        .field { margin-bottom: 15px; }
        .label { font-weight: bold; color: #555; }
        .value { margin-top: 5px; }
        .message { background: white; padding: 15px; border-left: 4px solid #6c5ce7; }
        .footer { text-align: center; padding: 15px; font-size: 12px; color: #888; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1 style="margin: 0;">New Contact Form Submission</h1>
        </div>
        <div class="content">
            <div class="field">
                <div class="label">Name:</div>
                <div class="value">{$nameHtml}</div>
            </div>
            <div class="field">
                <div class="label">Email:</div>
                <div class="value"><a href="mailto:{$emailHtml}">{$emailHtml}</a></div>
            </div>
            <div class="field">
                <div class="label">Phone:</div>
                <div class="value">{$phoneDisplay}</div>
            </div>
            <div class="field">
                <div class="label">Message:</div>
                <div class="message">{$messageHtml}</div>
            </div>
        </div>
        <div class="footer">
            Sent from mulligans-grille.com contact form
        </div>
    </div>
</body>
</html>
HTML;

$textBody = <<<TEXT
New Contact Form Submission

Name: {$name}
Email: {$email}
Phone: {$phoneDisplay}

Message:
{$message}

---
Sent from mulligans-grille.com contact form
TEXT;

// Send via Postmark API
$postmarkData = [
    'From' => $fromEmail,
    'To' => $toEmail,
    'ReplyTo' => $email,
    'Subject' => $emailSubject,
    'HtmlBody' => $emailBody,
    'TextBody' => $textBody,
    'MessageStream' => 'outbound'
];

$ch = curl_init('https://api.postmarkapp.com/email');
curl_setopt_array($ch, [
    CURLOPT_POST => true,
    CURLOPT_POSTFIELDS => json_encode($postmarkData),
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_HTTPHEADER => [
        'Accept: application/json',
        'Content-Type: application/json',
        'X-Postmark-Server-Token: ' . $postmarkToken
    ],
    CURLOPT_TIMEOUT => 30
]);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$curlError = curl_error($ch);
curl_close($ch);

// Handle response
if ($curlError) {
    error_log("Postmark cURL error: $curlError");
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'Failed to send message. Please try again.']);
    exit;
}

$result = json_decode($response, true);

if ($httpCode >= 200 && $httpCode < 300 && isset($result['MessageID'])) {
    echo json_encode([
        'success' => true,
        'message' => 'Thank you for your message! We\'ll be in touch soon.'
    ]);
} else {
    $errorMessage = isset($result['Message']) ? $result['Message'] : 'Unknown error';
    error_log("Postmark API error ($httpCode): $errorMessage");
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'Failed to send message. Please try again.']);
}
