(function () {
  'use strict';

  var cfg = window.sdbCfg || {};
  var STORAGE_KEY = 'sdb_dismissed';

  function getBar() {
    return document.getElementById('sdb-bar');
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

  /* ── Main init ───────────────────────────────────────── */
  function init() {
    var bar = getBar();
    if (!bar) return;

    // Dismissed? Hide immediately.
    if (cfg.dismissible && isDismissed()) {
      bar.style.display = 'none';
      return;
    }

    // DOM reposition
    var pos = cfg.position || '';
    if (pos === 'below_header') {
      repositionBelowHeader(bar);
    } else if (pos === 'top_bar') {
      repositionTopBar(bar);
    }

    // Dismiss button
    if (cfg.dismissible) {
      var btn = bar.querySelector('.sdb-close');
      if (btn) {
        btn.addEventListener('click', function () {
          bar.style.transition = 'opacity .3s';
          bar.style.opacity = '0';
          setTimeout(function () { bar.style.display = 'none'; }, 320);
          markDismissed();
        });
      }
    }
  }

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', init);
  } else {
    init();
  }
})();
