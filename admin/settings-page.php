<?php
if ( ! defined( 'ABSPATH' ) ) exit;
if ( ! current_user_can( 'manage_options' ) ) return;

$s = EVOLNUX_Settings::get();

$pages      = get_pages( [ 'sort_column' => 'post_title', 'sort_order' => 'asc' ] );
$posts      = get_posts( [ 'numberposts' => 300, 'orderby' => 'title', 'order' => 'ASC', 'post_status' => 'publish' ] );
$post_types = get_post_types( [ 'public' => true ], 'objects' );
unset( $post_types['attachment'] );
?>
<div class="wrap evolnux-wrap">
  <h1 class="evolnux-page-title">
    <span class="dashicons dashicons-info-outline" style="font-size:28px;width:28px;height:28px;margin-right:8px;vertical-align:middle;color:#0073aa;"></span>
    <?php esc_html_e( 'Disclaimer Manager', 'evolnux-disclaimer-bar' ); ?>
  </h1>

  <?php settings_errors(); ?>

  <div id="evolnux-preview-wrap" style="display:none;" aria-live="polite">
    <h3><?php esc_html_e( 'Live Preview', 'evolnux-disclaimer-bar' ); ?></h3>
    <div id="evolnux-preview-container"></div>
    <p class="description"><?php esc_html_e( 'This preview reflects styling only. Position and scope behaviour applies on the frontend.', 'evolnux-disclaimer-bar' ); ?></p>
  </div>

  <form method="post" action="options.php" id="evolnux-form">
    <?php settings_fields( 'evolnux_group' ); ?>

    <div class="evolnux-tabs">

      <ul class="evolnux-tab-nav" role="tablist">
        <?php foreach ( [
          'general'  => [ 'dashicons-edit',          __( 'General',  'evolnux-disclaimer-bar' ) ],
          'display'  => [ 'dashicons-visibility',     __( 'Display',  'evolnux-disclaimer-bar' ) ],
          'design'   => [ 'dashicons-art',            __( 'Design',   'evolnux-disclaimer-bar' ) ],
          'advanced' => [ 'dashicons-admin-settings', __( 'Advanced', 'evolnux-disclaimer-bar' ) ],
        ] as $id => [ $icon, $label ] ) : ?>
        <li role="presentation">
          <a href="#evolnux-tab-<?php echo esc_attr( $id ); ?>" class="evolnux-tab-link<?php echo $id === 'general' ? ' is-active' : ''; ?>"
             role="tab" aria-controls="evolnux-tab-<?php echo esc_attr( $id ); ?>" aria-selected="<?php echo $id === 'general' ? 'true' : 'false'; ?>">
            <span class="dashicons <?php echo esc_attr( $icon ); ?>"></span>
            <?php echo esc_html( $label ); ?>
          </a>
        </li>
        <?php endforeach; ?>
      </ul>

      <!-- ── TAB: GENERAL ─────────────────────────────────── -->
      <div id="evolnux-tab-general" class="evolnux-tab-panel is-active" role="tabpanel">
        <table class="form-table" role="presentation">
          <tr>
            <th scope="row"><?php esc_html_e( 'Enable Disclaimer Bar', 'evolnux-disclaimer-bar' ); ?></th>
            <td>
              <label class="evolnux-toggle" title="<?php esc_attr_e( 'Enable / Disable', 'evolnux-disclaimer-bar' ); ?>">
                <input type="checkbox" name="evolnux_settings[enabled]" value="1" <?php checked( $s['enabled'] ); ?> />
                <span class="evolnux-slider" aria-hidden="true"></span>
              </label>
              <span class="description"><?php esc_html_e( 'Toggle to show or hide the disclaimer bar site-wide (subject to scope rules).', 'evolnux-disclaimer-bar' ); ?></span>
            </td>
          </tr>
          <tr>
            <th scope="row"><?php esc_html_e( 'Disclaimer Content', 'evolnux-disclaimer-bar' ); ?></th>
            <td>
              <?php
              wp_editor( $s['content'], 'evolnux_wysiwyg_editor', [
                'textarea_name' => 'evolnux_settings[content]',
                'textarea_rows' => 6,
                'media_buttons' => false,
                'teeny'         => true,
              ] );
              ?>
              <p class="description"><?php esc_html_e( 'HTML is allowed. Accepts links, bold, italic, etc.', 'evolnux-disclaimer-bar' ); ?></p>
            </td>
          </tr>
        </table>
      </div>

      <!-- ── TAB: DISPLAY ─────────────────────────────────── -->
      <div id="evolnux-tab-display" class="evolnux-tab-panel" role="tabpanel" hidden>
        <table class="form-table" role="presentation">

          <tr>
            <th scope="row"><?php esc_html_e( 'Position', 'evolnux-disclaimer-bar' ); ?></th>
            <td>
              <select name="evolnux_settings[position]" id="evolnux-position">
                <?php foreach ( [
                  'top_bar'      => __( 'Top Bar — above header',       'evolnux-disclaimer-bar' ),
                  'below_header' => __( 'Below Header',                  'evolnux-disclaimer-bar' ),
                  'above_footer' => __( 'Above Footer',                  'evolnux-disclaimer-bar' ),
                  'fixed_bottom' => __( 'Fixed Bottom Bar',              'evolnux-disclaimer-bar' ),
                  'fixed_top'    => __( 'Fixed Top Bar',                 'evolnux-disclaimer-bar' ),
                  'custom_hook'  => __( 'Custom Action Hook (advanced)', 'evolnux-disclaimer-bar' ),
                ] as $v => $l ) : ?>
                <option value="<?php echo esc_attr( $v ); ?>" <?php selected( $s['position'], $v ); ?>><?php echo esc_html( $l ); ?></option>
                <?php endforeach; ?>
              </select>
            </td>
          </tr>

          <tr id="evolnux-row-custom-hook" <?php echo $s['position'] !== 'custom_hook' ? 'style="display:none"' : ''; ?>>
            <th scope="row"><?php esc_html_e( 'Custom Hook Name', 'evolnux-disclaimer-bar' ); ?></th>
            <td>
              <input type="text" name="evolnux_settings[custom_hook]" value="<?php echo esc_attr( $s['custom_hook'] ); ?>" class="regular-text" placeholder="e.g. wp_body_open" />
              <p class="description"><?php esc_html_e( 'WordPress action hook at which the bar will be output.', 'evolnux-disclaimer-bar' ); ?></p>
            </td>
          </tr>

          <tr>
            <th scope="row"><?php esc_html_e( 'Display Scope', 'evolnux-disclaimer-bar' ); ?></th>
            <td>
              <select name="evolnux_settings[display_scope]" id="evolnux-scope">
                <?php foreach ( [
                  'entire'              => __( 'Entire Website',       'evolnux-disclaimer-bar' ),
                  'homepage'            => __( 'Homepage Only',         'evolnux-disclaimer-bar' ),
                  'selected_pages'      => __( 'Selected Pages',        'evolnux-disclaimer-bar' ),
                  'selected_posts'      => __( 'Selected Posts',        'evolnux-disclaimer-bar' ),
                  'selected_post_types' => __( 'Selected Post Types',   'evolnux-disclaimer-bar' ),
                ] as $v => $l ) : ?>
                <option value="<?php echo esc_attr( $v ); ?>" <?php selected( $s['display_scope'], $v ); ?>><?php echo esc_html( $l ); ?></option>
                <?php endforeach; ?>
              </select>
            </td>
          </tr>

          <?php
          // Scope-specific rows
          $scope_rows = [
            'selected_pages'      => [ __( 'Select Pages', 'evolnux-disclaimer-bar' ), $pages, 'selected_pages', 'post_title', 'ID' ],
            'selected_posts'      => [ __( 'Select Posts', 'evolnux-disclaimer-bar' ), $posts, 'selected_posts', 'post_title', 'ID' ],
          ];
          foreach ( $scope_rows as $scope => [ $label, $items, $key, $title_field, $id_field ] ) : ?>
          <tr class="evolnux-scope-row evolnux-scope-<?php echo esc_attr( $scope ); ?>" <?php echo $s['display_scope'] !== $scope ? 'style="display:none"' : ''; ?>>
            <th scope="row"><?php echo esc_html( $label ); ?></th>
            <td>
              <select name="evolnux_settings[<?php echo esc_attr( $key ); ?>][]" class="evolnux-select2" multiple>
                <?php foreach ( $items as $item ) :
                  $item_id = is_object( $item ) ? $item->$id_field : $item[ $id_field ];
                  $item_title = is_object( $item ) ? $item->$title_field : $item[ $title_field ];
                ?>
                <option value="<?php echo absint( $item_id ); ?>" <?php echo in_array( $item_id, (array) $s[ $key ], true ) ? 'selected' : ''; ?>>
                  <?php echo esc_html( $item_title ); ?>
                </option>
                <?php endforeach; ?>
              </select>
            </td>
          </tr>
          <?php endforeach; ?>

          <tr class="evolnux-scope-row evolnux-scope-selected_post_types" <?php echo $s['display_scope'] !== 'selected_post_types' ? 'style="display:none"' : ''; ?>>
            <th scope="row"><?php esc_html_e( 'Select Post Types', 'evolnux-disclaimer-bar' ); ?></th>
            <td>
              <select name="evolnux_settings[selected_post_types][]" class="evolnux-select2" multiple>
                <?php foreach ( $post_types as $pt ) : ?>
                <option value="<?php echo esc_attr( $pt->name ); ?>" <?php echo in_array( $pt->name, (array) $s['selected_post_types'], true ) ? 'selected' : ''; ?>>
                  <?php echo esc_html( $pt->labels->singular_name ); ?>
                </option>
                <?php endforeach; ?>
              </select>
            </td>
          </tr>

          <tr>
            <th scope="row"><?php esc_html_e( 'Exclude Pages', 'evolnux-disclaimer-bar' ); ?></th>
            <td>
              <select name="evolnux_settings[excluded_pages][]" class="evolnux-select2" multiple>
                <?php foreach ( $pages as $page ) : ?>
                <option value="<?php echo absint( $page->ID ); ?>" <?php echo in_array( $page->ID, (array) $s['excluded_pages'], true ) ? 'selected' : ''; ?>>
                  <?php echo esc_html( $page->post_title ); ?>
                </option>
                <?php endforeach; ?>
              </select>
              <p class="description"><?php esc_html_e( 'These pages are always excluded, even when scope is "Entire Website".', 'evolnux-disclaimer-bar' ); ?></p>
            </td>
          </tr>

          <tr>
            <th scope="row"><?php esc_html_e( 'Responsive Visibility', 'evolnux-disclaimer-bar' ); ?></th>
            <td>
              <select name="evolnux_settings[responsive]">
                <option value="both"    <?php selected( $s['responsive'], 'both' );    ?>><?php esc_html_e( 'Desktop & Mobile', 'evolnux-disclaimer-bar' ); ?></option>
                <option value="desktop" <?php selected( $s['responsive'], 'desktop' ); ?>><?php esc_html_e( 'Desktop Only (≥768px)', 'evolnux-disclaimer-bar' ); ?></option>
                <option value="mobile"  <?php selected( $s['responsive'], 'mobile' );  ?>><?php esc_html_e( 'Mobile Only (<768px)', 'evolnux-disclaimer-bar' ); ?></option>
              </select>
            </td>
          </tr>

        </table>
      </div>

      <!-- ── TAB: DESIGN ──────────────────────────────────── -->
      <div id="evolnux-tab-design" class="evolnux-tab-panel" role="tabpanel" hidden>
        <table class="form-table" role="presentation">

          <tr>
            <th scope="row"><?php esc_html_e( 'Background Color', 'evolnux-disclaimer-bar' ); ?></th>
            <td><input type="text" name="evolnux_settings[bg_color]" value="<?php echo esc_attr( $s['bg_color'] ); ?>" class="evolnux-color" /></td>
          </tr>
          <tr>
            <th scope="row"><?php esc_html_e( 'Text Color', 'evolnux-disclaimer-bar' ); ?></th>
            <td><input type="text" name="evolnux_settings[text_color]" value="<?php echo esc_attr( $s['text_color'] ); ?>" class="evolnux-color" /></td>
          </tr>
          <tr>
            <th scope="row"><?php esc_html_e( 'Border Color', 'evolnux-disclaimer-bar' ); ?></th>
            <td><input type="text" name="evolnux_settings[border_color]" value="<?php echo esc_attr( $s['border_color'] ); ?>" class="evolnux-color" /></td>
          </tr>
          <tr>
            <th scope="row"><?php esc_html_e( 'Border Position', 'evolnux-disclaimer-bar' ); ?></th>
            <td>
              <select name="evolnux_settings[border_position]">
                <?php foreach ( [ 'none' => 'None', 'top' => 'Top', 'bottom' => 'Bottom', 'both' => 'Top & Bottom' ] as $v => $l ) : ?>
                <option value="<?php echo esc_attr( $v ); ?>" <?php selected( $s['border_position'], $v ); ?>><?php echo esc_html( $l ); ?></option>
                <?php endforeach; ?>
              </select>
              <label style="margin-left:12px;"><?php esc_html_e( 'Width', 'evolnux-disclaimer-bar' ); ?>
                <input type="number" name="evolnux_settings[border_width]" value="<?php echo absint( $s['border_width'] ); ?>" min="0" max="20" class="small-text" /> px
              </label>
            </td>
          </tr>
          <tr>
            <th scope="row"><?php esc_html_e( 'Opacity', 'evolnux-disclaimer-bar' ); ?></th>
            <td>
              <input type="range" name="evolnux_settings[opacity]" id="evolnux-opacity" value="<?php echo esc_attr( $s['opacity'] ); ?>" min="0.1" max="1" step="0.05" style="width:160px;vertical-align:middle;" />
              <output for="evolnux-opacity" id="evolnux-opacity-out"><?php echo esc_html( $s['opacity'] ); ?></output>
            </td>
          </tr>

          <tr><th colspan="2" class="evolnux-section-head"><?php esc_html_e( 'Typography', 'evolnux-disclaimer-bar' ); ?></th></tr>

          <tr>
            <th scope="row"><?php esc_html_e( 'Font Size', 'evolnux-disclaimer-bar' ); ?></th>
            <td><input type="number" name="evolnux_settings[font_size]" value="<?php echo absint( $s['font_size'] ); ?>" min="8" max="64" class="small-text" /> px</td>
          </tr>
          <tr>
            <th scope="row"><?php esc_html_e( 'Font Weight', 'evolnux-disclaimer-bar' ); ?></th>
            <td>
              <select name="evolnux_settings[font_weight]">
                <?php foreach ( [ '300' => 'Light (300)', '400' => 'Normal (400)', '500' => 'Medium (500)', '600' => 'Semi-bold (600)', '700' => 'Bold (700)' ] as $v => $l ) : ?>
                <option value="<?php echo esc_attr( $v ); ?>" <?php selected( $s['font_weight'], $v ); ?>><?php echo esc_html( $l ); ?></option>
                <?php endforeach; ?>
              </select>
            </td>
          </tr>
          <tr>
            <th scope="row"><?php esc_html_e( 'Text Alignment', 'evolnux-disclaimer-bar' ); ?></th>
            <td>
              <select name="evolnux_settings[text_align]">
                <?php foreach ( [ 'left' => 'Left', 'center' => 'Center', 'right' => 'Right', 'justify' => 'Justify' ] as $v => $l ) : ?>
                <option value="<?php echo esc_attr( $v ); ?>" <?php selected( $s['text_align'], $v ); ?>><?php echo esc_html( $l ); ?></option>
                <?php endforeach; ?>
              </select>
            </td>
          </tr>
          <tr>
            <th scope="row"><?php esc_html_e( 'Line Height', 'evolnux-disclaimer-bar' ); ?></th>
            <td><input type="number" name="evolnux_settings[line_height]" value="<?php echo esc_attr( $s['line_height'] ); ?>" min="1" max="4" step="0.1" class="small-text" /></td>
          </tr>

          <tr><th colspan="2" class="evolnux-section-head"><?php esc_html_e( 'Spacing', 'evolnux-disclaimer-bar' ); ?></th></tr>

          <tr>
            <th scope="row"><?php esc_html_e( 'Padding (px)', 'evolnux-disclaimer-bar' ); ?></th>
            <td>
              <div class="evolnux-spacing">
                <?php foreach ( [ 'padding_top' => 'Top', 'padding_right' => 'Right', 'padding_bottom' => 'Bottom', 'padding_left' => 'Left' ] as $k => $l ) : ?>
                <label><?php echo esc_html( $l ); ?><br><input type="number" name="evolnux_settings[<?php echo esc_attr( $k ); ?>]" value="<?php echo absint( $s[ $k ] ); ?>" min="0" max="200" class="small-text" /></label>
                <?php endforeach; ?>
              </div>
            </td>
          </tr>
          <tr>
            <th scope="row"><?php esc_html_e( 'Margin (px)', 'evolnux-disclaimer-bar' ); ?></th>
            <td>
              <div class="evolnux-spacing">
                <?php foreach ( [ 'margin_top' => 'Top', 'margin_bottom' => 'Bottom' ] as $k => $l ) : ?>
                <label><?php echo esc_html( $l ); ?><br><input type="number" name="evolnux_settings[<?php echo esc_attr( $k ); ?>]" value="<?php echo absint( $s[ $k ] ); ?>" min="0" max="200" class="small-text" /></label>
                <?php endforeach; ?>
              </div>
            </td>
          </tr>
          <tr>
            <th scope="row"><?php esc_html_e( 'Width', 'evolnux-disclaimer-bar' ); ?></th>
            <td>
              <label><input type="radio" name="evolnux_settings[width_type]" value="full"  <?php checked( $s['width_type'], 'full' );  ?>> <?php esc_html_e( 'Full Width', 'evolnux-disclaimer-bar' ); ?></label>
              &nbsp;&nbsp;
              <label><input type="radio" name="evolnux_settings[width_type]" value="boxed" <?php checked( $s['width_type'], 'boxed' ); ?>> <?php esc_html_e( 'Boxed Container (max 1200px)', 'evolnux-disclaimer-bar' ); ?></label>
            </td>
          </tr>

          <tr><th colspan="2" class="evolnux-section-head"><?php esc_html_e( 'Animation', 'evolnux-disclaimer-bar' ); ?></th></tr>
          <tr>
            <th scope="row"><?php esc_html_e( 'Entrance Animation', 'evolnux-disclaimer-bar' ); ?></th>
            <td>
              <select name="evolnux_settings[animation]">
                <?php foreach ( [ 'none' => 'None', 'fade_in' => 'Fade In', 'slide_down' => 'Slide Down', 'slide_up' => 'Slide Up' ] as $v => $l ) : ?>
                <option value="<?php echo esc_attr( $v ); ?>" <?php selected( $s['animation'], $v ); ?>><?php echo esc_html( $l ); ?></option>
                <?php endforeach; ?>
              </select>
            </td>
          </tr>

        </table>
      </div>

      <!-- ── TAB: ADVANCED ────────────────────────────────── -->
      <div id="evolnux-tab-advanced" class="evolnux-tab-panel" role="tabpanel" hidden>
        <table class="form-table" role="presentation">

          <tr>
            <th scope="row"><?php esc_html_e( 'Dismissible', 'evolnux-disclaimer-bar' ); ?></th>
            <td>
              <label class="evolnux-toggle">
                <input type="checkbox" name="evolnux_settings[dismissible]" value="1" id="evolnux-dismissible" <?php checked( $s['dismissible'] ); ?> />
                <span class="evolnux-slider" aria-hidden="true"></span>
              </label>
              <span class="description"><?php esc_html_e( 'Show a close button so visitors can dismiss the bar.', 'evolnux-disclaimer-bar' ); ?></span>
            </td>
          </tr>

          <tr class="evolnux-dismiss-row" <?php echo empty( $s['dismissible'] ) ? 'style="display:none"' : ''; ?>>
            <th scope="row"><?php esc_html_e( 'Close Button Text', 'evolnux-disclaimer-bar' ); ?></th>
            <td>
              <input type="text" name="evolnux_settings[dismiss_text]" value="<?php echo esc_attr( $s['dismiss_text'] ); ?>" class="small-text" />
              <p class="description"><?php esc_html_e( 'Default: × (multiplication sign). Can also be a word like "Close".', 'evolnux-disclaimer-bar' ); ?></p>
            </td>
          </tr>

          <tr class="evolnux-dismiss-row" <?php echo empty( $s['dismissible'] ) ? 'style="display:none"' : ''; ?>>
            <th scope="row"><?php esc_html_e( 'Dismissal Expiry', 'evolnux-disclaimer-bar' ); ?></th>
            <td>
              <input type="number" name="evolnux_settings[dismiss_expiry]" value="<?php echo absint( $s['dismiss_expiry'] ); ?>" min="0" max="365" class="small-text" />
              <span><?php esc_html_e( 'days', 'evolnux-disclaimer-bar' ); ?></span>
              <p class="description"><?php esc_html_e( 'Cookie/localStorage lifetime. Set to 0 for session-only dismissal (reappears on browser close).', 'evolnux-disclaimer-bar' ); ?></p>
            </td>
          </tr>

          <tr>
            <th scope="row"><?php esc_html_e( 'Z-Index', 'evolnux-disclaimer-bar' ); ?></th>
            <td>
              <input type="number" name="evolnux_settings[z_index]" value="<?php echo absint( $s['z_index'] ); ?>" min="1" max="999999" class="small-text" />
              <p class="description"><?php esc_html_e( 'Stacking order — increase if the bar appears behind other sticky elements.', 'evolnux-disclaimer-bar' ); ?></p>
            </td>
          </tr>

        </table>
      </div>

    </div><!-- /.evolnux-tabs -->

    <div class="evolnux-footer-bar">
      <button type="button" id="evolnux-preview-btn" class="button button-secondary">
        <span class="dashicons dashicons-visibility" style="vertical-align:middle;margin-top:-2px;"></span>
        <?php esc_html_e( 'Preview', 'evolnux-disclaimer-bar' ); ?>
      </button>
      <?php submit_button( __( 'Save Settings', 'evolnux-disclaimer-bar' ), 'primary', 'submit', false ); ?>
    </div>

  </form>
</div>
