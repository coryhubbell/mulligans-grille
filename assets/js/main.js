/**
 * Mulligan's Grille — main.js
 * Mobile nav, scroll header state, today highlight, reveal-on-scroll, footer year, SW cleanup.
 */
(function () {
  'use strict';

  var reduceMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

  function initMobileNav() {
    var header = document.getElementById('site-header');
    var toggle = document.querySelector('.z1-mobile-toggle');
    var nav = document.getElementById('primary-nav');
    if (!header || !toggle || !nav) return;

    function close() {
      header.classList.remove('is-nav-open');
      toggle.setAttribute('aria-expanded', 'false');
      toggle.setAttribute('aria-label', 'Open navigation');
    }

    toggle.addEventListener('click', function () {
      var open = header.classList.toggle('is-nav-open');
      if (open) header.classList.remove('is-hidden'); // reveal a hidden header when opening the menu
      toggle.setAttribute('aria-expanded', open ? 'true' : 'false');
      toggle.setAttribute('aria-label', open ? 'Close navigation' : 'Open navigation');
    });

    nav.querySelectorAll('a').forEach(function (link) {
      link.addEventListener('click', close);
    });

    document.addEventListener('keydown', function (e) {
      if (e.key === 'Escape' && header.classList.contains('is-nav-open')) {
        close();
        toggle.focus();
      }
    });
  }

  function initScrollHeader() {
    var header = document.getElementById('site-header');
    if (!header) return;

    initHideOnScroll(header);

    var sentinel = document.getElementById('header-sentinel');
    if (sentinel && 'IntersectionObserver' in window) {
      // Sentinel sits at y=60 in the document. While it intersects the
      // viewport, the page is near the top; when it leaves, we've scrolled
      // past — no scroll listener, no layout read.
      var io = new IntersectionObserver(function (entries) {
        header.classList.toggle('is-scrolled', !entries[0].isIntersecting);
      });
      io.observe(sentinel);
      return;
    }

    // Fallback for very old browsers — rAF-throttled scroll listener
    var scrolled = false;
    var ticking = false;
    function update() {
      ticking = false;
      var next = window.scrollY > 60;
      if (next === scrolled) return;
      scrolled = next;
      header.classList.toggle('is-scrolled', scrolled);
    }
    function onScroll() {
      if (ticking) return;
      ticking = true;
      requestAnimationFrame(update);
    }
    update();
    window.addEventListener('scroll', onScroll, { passive: true });
  }

  // Hide-on-scroll-down / show-on-scroll-up. Slides the whole header up out
  // of view when scrolling down and back in when scrolling up. Gated on
  // reduced motion, rAF-throttled, suppressed near the top of the page and
  // while the mobile menu is open.
  function initHideOnScroll(header) {
    if (reduceMotion) return;

    var DELTA = 8;        // ignore sub-pixel / jitter scrolls
    var TOP_GUARD = 120;  // always show within this many px of the top
    var lastY = window.scrollY || 0;
    var dirTicking = false;

    function updateDir() {
      dirTicking = false;
      var y = window.scrollY || 0;

      // Never hide while the mobile menu is open or near the top.
      if (header.classList.contains('is-nav-open') || y <= TOP_GUARD) {
        header.classList.remove('is-hidden');
        lastY = y;
        return;
      }

      var diff = y - lastY;
      if (Math.abs(diff) < DELTA) return;  // below threshold — keep state

      header.classList.toggle('is-hidden', diff > 0); // down = hide, up = show
      lastY = y;
    }

    function onDirScroll() {
      if (dirTicking) return;
      dirTicking = true;
      requestAnimationFrame(updateDir);
    }

    window.addEventListener('scroll', onDirScroll, { passive: true });
  }

  function highlightToday() {
    var rows = document.querySelectorAll('.hours-table tr[data-day]');
    if (!rows.length) return;
    var today = String(new Date().getDay());
    rows.forEach(function (tr) {
      if (tr.getAttribute('data-day') === today) tr.classList.add('is-today');
    });
  }

  function initReveals() {
    var els = document.querySelectorAll('.reveal');
    if (!els.length) return;
    if (reduceMotion || !('IntersectionObserver' in window)) {
      els.forEach(function (el) { el.classList.add('is-visible'); });
      return;
    }
    var io = new IntersectionObserver(function (entries) {
      entries.forEach(function (entry) {
        if (entry.isIntersecting) {
          entry.target.classList.add('is-visible');
          io.unobserve(entry.target);
        }
      });
    }, { threshold: 0.12, rootMargin: '0px 0px -40px 0px' });
    els.forEach(function (el) { io.observe(el); });
  }

  function updateFooterYear() {
    var year = new Date().getFullYear();
    document.querySelectorAll('.js-footer-year').forEach(function (el) {
      el.textContent = year;
    });
  }

  function cleanupServiceWorker() {
    // Service worker was retired — sw.js is now a self-destruct shim. As a
    // belt-and-suspenders backup, also unregister from the client side so
    // any user whose browser doesn't pick up the new sw.js still loses the
    // stale-cache flash on their next visit.
    if (!('serviceWorker' in navigator)) return;
    navigator.serviceWorker.getRegistrations().then(function (regs) {
      regs.forEach(function (reg) { reg.unregister(); });
    }).catch(function () { /* noop */ });
  }

  function initScrollTop() {
    var btn = document.querySelector('.scroll-top');
    var footer = document.querySelector('footer');
    if (!btn || !footer) return;
    btn.hidden = false;

    btn.addEventListener('click', function () {
      window.scrollTo({ top: 0, behavior: reduceMotion ? 'auto' : 'smooth' });
    });

    // IntersectionObserver avoids per-scroll getBoundingClientRect() reads
    // (forced reflow). Button becomes visible as the footer enters viewport.
    if ('IntersectionObserver' in window) {
      var io = new IntersectionObserver(function (entries) {
        btn.classList.toggle('is-visible', entries[0].isIntersecting);
      });
      io.observe(footer);
      return;
    }

    // Fallback: rAF-throttled scroll listener for very old browsers
    var ticking = false;
    function update() {
      ticking = false;
      var footerTop = footer.getBoundingClientRect().top;
      btn.classList.toggle('is-visible', footerTop <= window.innerHeight);
    }
    function onScroll() {
      if (ticking) return;
      ticking = true;
      requestAnimationFrame(update);
    }
    update();
    window.addEventListener('scroll', onScroll, { passive: true });
    window.addEventListener('resize', onScroll, { passive: true });
  }

  function initStoryToggle() {
    var btn = document.querySelector('.story-toggle');
    var panel = document.getElementById('story-buddy');
    if (!btn || !panel) return;
    panel.classList.add('is-collapsed'); // collapse only once JS is present (no-JS shows full text)
    btn.addEventListener('click', function () {
      var open = panel.classList.toggle('is-collapsed') === false;
      btn.setAttribute('aria-expanded', open ? 'true' : 'false');
      btn.textContent = open ? 'Read less' : 'Read more';
    });
  }

  function init() {
    initMobileNav();
    initScrollHeader();
    highlightToday();
    initReveals();
    initStoryToggle();
    initScrollTop();
    updateFooterYear();
    cleanupServiceWorker();
  }

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', init);
  } else {
    init();
  }
})();
