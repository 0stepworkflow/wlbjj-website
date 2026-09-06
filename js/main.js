// Washington Luis BJJ — shared site behavior
// Lightweight, dependency-free: no animation library, IntersectionObserver only.
(function () {
  'use strict';

  // --- Hero background video: respect reduced motion, degrade gracefully if autoplay is blocked ---
  var heroVideo = document.getElementById('hero-video');
  if (heroVideo) {
    var reduceMotion = window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches;
    if (reduceMotion) {
      heroVideo.removeAttribute('autoplay');
      heroVideo.pause();
    } else {
      var playAttempt = heroVideo.play();
      if (playAttempt && typeof playAttempt.catch === 'function') {
        playAttempt.catch(function () {
          // Autoplay blocked (rare with muted video) — the poster frame stays visible, no broken UI.
        });
      }
    }
  }

  // --- Mobile nav toggle ---
  var toggle = document.querySelector('.nav-toggle');
  var mobileNav = document.querySelector('.mobile-nav');

  if (toggle && mobileNav) {
    toggle.addEventListener('click', function () {
      var isOpen = mobileNav.classList.toggle('is-open');
      toggle.setAttribute('aria-expanded', String(isOpen));
      document.body.style.overflow = isOpen ? 'hidden' : '';
    });

    mobileNav.querySelectorAll('a').forEach(function (link) {
      link.addEventListener('click', function () {
        mobileNav.classList.remove('is-open');
        toggle.setAttribute('aria-expanded', 'false');
        document.body.style.overflow = '';
      });
    });
  }

  // --- Header background state on scroll (subtle, cheap) ---
  var header = document.querySelector('.site-header');
  if (header) {
    var onScroll = function () {
      header.style.borderBottomColor = window.scrollY > 8
        ? 'var(--color-border-strong)'
        : 'var(--color-border)';
    };
    window.addEventListener('scroll', onScroll, { passive: true });
    onScroll();
  }

  // --- Scroll reveal ---
  var revealEls = document.querySelectorAll('[data-reveal]');
  if (revealEls.length) {
    if ('IntersectionObserver' in window) {
      var observer = new IntersectionObserver(
        function (entries) {
          entries.forEach(function (entry) {
            if (entry.isIntersecting) {
              entry.target.classList.add('is-visible');
              observer.unobserve(entry.target);
            }
          });
        },
        { threshold: 0.15, rootMargin: '0px 0px -40px 0px' }
      );

      revealEls.forEach(function (el, i) {
        var group = el.closest('[data-reveal-group]');
        if (group) {
          var siblings = Array.prototype.indexOf.call(group.children, el);
          el.style.setProperty('--stagger-i', String(siblings % 8));
        }
        observer.observe(el);
      });
    } else {
      // No IO support: show content immediately, no degraded UX.
      revealEls.forEach(function (el) { el.classList.add('is-visible'); });
    }
  }

  // --- Active nav link (aria-current) based on current path ---
  var path = window.location.pathname.split('/').pop() || 'index.html';
  document.querySelectorAll('.nav-links a, .mobile-nav a').forEach(function (link) {
    var href = link.getAttribute('href');
    if (href === path || (path === '' && href === 'index.html')) {
      link.setAttribute('aria-current', 'page');
    }
  });

  // --- Current year in footer ---
  var yearEl = document.getElementById('year');
  if (yearEl) yearEl.textContent = String(new Date().getFullYear());

  // --- "Visit Us" link: send iOS users to Apple Maps, everyone else to Google Maps ---
  var visitLink = document.getElementById('visit-us-link');
  if (visitLink && /iPad|iPhone|iPod/.test(navigator.userAgent)) {
    visitLink.href = 'https://maps.apple.com/?address=4202+Center+St,+Deer+Park,+TX+77536';
  }
})();
