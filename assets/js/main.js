/**
 * Mulligan's Grille — main.js
 * Mobile nav, scroll header state, today highlight, reveal-on-scroll, footer year, SW.
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
    }

    toggle.addEventListener('click', function () {
      var open = header.classList.toggle('is-nav-open');
      toggle.setAttribute('aria-expanded', open ? 'true' : 'false');
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

  function registerServiceWorker() {
    if (!('serviceWorker' in navigator)) return;
    // Skip SW on localhost / 127.0.0.1 — keeps local dev free of stale caches
    var host = location.hostname;
    if (host === 'localhost' || host === '127.0.0.1' || host.endsWith('.local')) {
      // Also unregister any existing SW from a prior session so refreshes hit disk
      navigator.serviceWorker.getRegistrations().then(function (regs) {
        regs.forEach(function (reg) { reg.unregister(); });
      }).catch(function () {});
      return;
    }
    window.addEventListener('load', function () {
      navigator.serviceWorker.register('/sw.js').catch(function () { /* noop */ });
    });
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

  function init() {
    initMobileNav();
    initScrollHeader();
    highlightToday();
    initReveals();
    initScrollTop();
    updateFooterYear();
    registerServiceWorker();
  }

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', init);
  } else {
    init();
  }
})();
