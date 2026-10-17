(function () {
  'use strict';

  var cfg = window.evolnuxCfg || {};
  var STORAGE_KEY = 'evolnux_dismissed';

  function getBars() {
    // The auto-placed bar carries id="evolnux-bar"; [evolnux_disclaimer] shortcode
    // copies share the same .evolnux-bar class but no id (ids must be unique).
    return Array.prototype.slice.call(document.querySelectorAll('.evolnux-bar'));
  }

  /* ── Cookie helpers ──────────────────────────────────── */
  function setCookie(name, value, days) {
    var expires = '';
    if (days > 0) {
      var d = new Date();
      d.setTime(d.getTime() + days * 864e5);
      expires = '; expires=' + d.toUTCString();
    }
    document.cookie = name + '=' + value + expires + '; path=/; SameSite=Lax';
  }

  function getCookie(name) {
    var v = document.cookie.match('(^|;) ?' + name + '=([^;]*)(;|$)');
    return v ? v[2] : null;
  }

  /* ── Dismissal persistence ───────────────────────────── */
  function isDismissed() {
    var expiry = parseInt(cfg.expiry, 10) || 0;
    if (expiry > 0) {
      return getCookie(STORAGE_KEY) === '1' || localStorage.getItem(STORAGE_KEY) === '1';
    }
    return sessionStorage.getItem(STORAGE_KEY) === '1';
  }

  function markDismissed() {
    var expiry = parseInt(cfg.expiry, 10) || 0;
    if (expiry > 0) {
      setCookie(STORAGE_KEY, '1', expiry);
      try { localStorage.setItem(STORAGE_KEY, '1'); } catch (e) {}
    } else {
      try { sessionStorage.setItem(STORAGE_KEY, '1'); } catch (e) {}
    }
  }

  /* ── Reposition bar for below_header ────────────────── */
  function repositionBelowHeader(bar) {
    var header = document.querySelector(
      'header.site-header, header#site-header, header#masthead, ' +
      '#header, .site-header, header[role="banner"], header'
    );
    if (header && header.parentNode) {
      header.parentNode.insertBefore(bar, header.nextSibling);
    }
  }

  /* ── Reposition top_bar when output via footer fallback  */
  function repositionTopBar(bar) {
    var body = document.body;
    if (body && body.firstChild !== bar) {
      body.insertBefore(bar, body.firstChild);
    }
  }

  function hideBar(bar) {
    bar.style.transition = 'opacity .3s';
    bar.style.opacity = '0';
    setTimeout(function () { bar.style.display = 'none'; }, 320);
  }

  /* ── Main init ───────────────────────────────────────── */
  function init() {
    var bars = getBars();
    if (!bars.length) return;

    // Dismissed already? Hide every instance immediately (one disclaimer, one
    // dismiss state — a shortcode copy on another page shouldn't reappear).
    var dismissedAlready = cfg.dismissible && isDismissed();

    bars.forEach(function (bar) {
      if (dismissedAlready) {
        bar.style.display = 'none';
        return;
      }

      // DOM reposition only applies to the single auto-placed instance;
      // shortcode-rendered copies stay exactly where the user put them.
      if (bar.id === 'evolnux-bar') {
        var pos = cfg.position || '';
        if (pos === 'below_header') {
          repositionBelowHeader(bar);
        } else if (pos === 'top_bar') {
          repositionTopBar(bar);
        }
      }

      if (cfg.dismissible) {
        var btn = bar.querySelector('.evolnux-close');
        if (btn) {
          btn.addEventListener('click', function () {
            bars.forEach(hideBar);
            markDismissed();
          });
        }
      }
    });
  }

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', init);
  } else {
    init();
  }
})();
