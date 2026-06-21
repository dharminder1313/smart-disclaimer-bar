jQuery(function ($) {
  'use strict';

  /* ── Tab navigation ──────────────────────────────────── */
  $('.sdb-tab-link').on('click', function (e) {
    e.preventDefault();
    var target = $(this).attr('href');
    $('.sdb-tab-link').removeClass('is-active').attr('aria-selected', 'false');
    $(this).addClass('is-active').attr('aria-selected', 'true');
    $('.sdb-tab-panel').removeClass('is-active').prop('hidden', true);
    $(target).addClass('is-active').prop('hidden', false);
  });

  /* ── Colour pickers ──────────────────────────────────── */
  $('.sdb-color').wpColorPicker();

  /* ── Native multi-select sizing ──────────────────────── */
  $('.sdb-select2').attr('size', 8);

  /* ── Opacity range live display ──────────────────────── */
  $('#sdb-opacity').on('input', function () {
    $('#sdb-opacity-out').val($(this).val());
  });

  /* ── Position → show/hide custom hook field ──────────── */
  $('#sdb-position').on('change', function () {
    $('#sdb-row-custom-hook').toggle($(this).val() === 'custom_hook');
  });

  /* ── Scope → show/hide scope-specific rows ───────────── */
  var scopeRows = ['selected_pages', 'selected_posts', 'selected_post_types'];

  $('#sdb-scope').on('change', function () {
    var val = $(this).val();
    $('.sdb-scope-row').hide();
    if (scopeRows.indexOf(val) !== -1) {
      $('.sdb-scope-' + val).show();
    }
  });

  /* ── Dismissible toggle ──────────────────────────────── */
  $('#sdb-dismissible').on('change', function () {
    $('.sdb-dismiss-row').toggle($(this).is(':checked'));
  });

  /* ── Live preview ────────────────────────────────────── */
  $('#sdb-preview-btn').on('click', function () {
    // Sync TinyMCE content to textarea before collecting
    if (window.tinyMCE) {
      var ed = tinyMCE.get('sdb_wysiwyg_editor');
      if (ed) ed.save();
    }

    var formData = {};
    $('#sdb-form').serializeArray().forEach(function (item) {
      var m = item.name.match(/^sdb_settings\[([^\]]+)\](\[\])?$/);
      if (!m) return;
      var key = m[1];
      if (m[2]) {
        // array field
        if (!formData[key]) formData[key] = [];
        formData[key].push(item.value);
      } else {
        formData[key] = item.value;
      }
    });

    $.post(sdbAdmin.ajaxUrl, {
      action:   'sdb_preview',
      nonce:    sdbAdmin.nonce,
      settings: formData,
    }, function (res) {
      if (res.success) {
        $('#sdb-preview-container').html(res.data.html);
        $('#sdb-preview-wrap').show();
        $('html, body').animate({ scrollTop: $('#sdb-preview-wrap').offset().top - 60 }, 300);
      } else {
        alert(sdbAdmin.i18n.previewError);
      }
    }).fail(function () {
      alert(sdbAdmin.i18n.previewError);
    });
  });
});
