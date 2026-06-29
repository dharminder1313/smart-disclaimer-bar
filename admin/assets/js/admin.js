jQuery(function ($) {
  'use strict';

  /* ── Tab navigation ──────────────────────────────────── */
  $('.evolnux-tab-link').on('click', function (e) {
    e.preventDefault();
    var target = $(this).attr('href');
    $('.evolnux-tab-link').removeClass('is-active').attr('aria-selected', 'false');
    $(this).addClass('is-active').attr('aria-selected', 'true');
    $('.evolnux-tab-panel').removeClass('is-active').prop('hidden', true);
    $(target).addClass('is-active').prop('hidden', false);
  });

  /* ── Colour pickers ──────────────────────────────────── */
  $('.evolnux-color').wpColorPicker();

  /* ── Native multi-select sizing ──────────────────────── */
  $('.evolnux-select2').attr('size', 8);

  /* ── Opacity range live display ──────────────────────── */
  $('#evolnux-opacity').on('input', function () {
    $('#evolnux-opacity-out').val($(this).val());
  });

  /* ── Position → show/hide custom hook field ──────────── */
  $('#evolnux-position').on('change', function () {
    $('#evolnux-row-custom-hook').toggle($(this).val() === 'custom_hook');
  });

  /* ── Scope → show/hide scope-specific rows ───────────── */
  var scopeRows = ['selected_pages', 'selected_posts', 'selected_post_types'];

  $('#evolnux-scope').on('change', function () {
    var val = $(this).val();
    $('.evolnux-scope-row').hide();
    if (scopeRows.indexOf(val) !== -1) {
      $('.evolnux-scope-' + val).show();
    }
  });

  /* ── Dismissible toggle ──────────────────────────────── */
  $('#evolnux-dismissible').on('change', function () {
    $('.evolnux-dismiss-row').toggle($(this).is(':checked'));
  });

  /* ── Live preview ────────────────────────────────────── */
  $('#evolnux-preview-btn').on('click', function () {
    // Sync TinyMCE content to textarea before collecting
    if (window.tinyMCE) {
      var ed = tinyMCE.get('evolnux_wysiwyg_editor');
      if (ed) ed.save();
    }

    var formData = {};
    $('#evolnux-form').serializeArray().forEach(function (item) {
      var m = item.name.match(/^evolnux_settings\[([^\]]+)\](\[\])?$/);
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

    $.post(evolnuxAdmin.ajaxUrl, {
      action:   'evolnux_preview',
      nonce:    evolnuxAdmin.nonce,
      settings: formData,
    }, function (res) {
      if (res.success) {
        $('#evolnux-preview-container').html(res.data.html);
        $('#evolnux-preview-wrap').show();
        $('html, body').animate({ scrollTop: $('#evolnux-preview-wrap').offset().top - 60 }, 300);
      } else {
        alert(evolnuxAdmin.i18n.previewError);
      }
    }).fail(function () {
      alert(evolnuxAdmin.i18n.previewError);
    });
  });
});
