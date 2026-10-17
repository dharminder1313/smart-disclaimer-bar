<?php
if ( ! defined( 'ABSPATH' ) ) exit;

function evolnux_should_display( array $s ): bool {
	if ( empty( $s['enabled'] ) ) {
		return false;
	}

	if ( ! evolnux_within_schedule( $s ) ) {
		return false;
	}

	$scope = $s['display_scope'] ?? 'entire';

	if ( $scope === 'entire' ) {
		if ( ! empty( $s['excluded_pages'] ) && is_page( $s['excluded_pages'] ) ) {
			return false;
		}
		return true;
	}

	if ( $scope === 'homepage' ) {
		return is_front_page() || is_home();
	}

	if ( $scope === 'selected_pages' ) {
		return ! empty( $s['selected_pages'] ) && is_page( $s['selected_pages'] );
	}

	if ( $scope === 'selected_posts' ) {
		return ! empty( $s['selected_posts'] ) && is_single( $s['selected_posts'] );
	}

	if ( $scope === 'selected_post_types' ) {
		if ( empty( $s['selected_post_types'] ) ) {
			return false;
		}
		return in_array( get_post_type(), $s['selected_post_types'], true );
	}

	return false;
}

/**
 * Checks the configured schedule_start / schedule_end window against today's date
 * in the site's configured timezone. Empty bounds mean "no restriction" on that side.
 */
function evolnux_within_schedule( array $s ): bool {
	$today = current_time( 'Y-m-d' );

	if ( ! empty( $s['schedule_start'] ) && $today < $s['schedule_start'] ) {
		return false;
	}

	if ( ! empty( $s['schedule_end'] ) && $today > $s['schedule_end'] ) {
		return false;
	}

	return true;
}

function evolnux_build_inline_style( array $s ): string {
	$border = '';

	if ( ( $s['border_position'] ?? 'none' ) !== 'none' && (int) ( $s['border_width'] ?? 0 ) > 0 ) {
		$rule = (int) $s['border_width'] . 'px solid ' . $s['border_color'];
		switch ( $s['border_position'] ) {
			case 'top':    $border = 'border-top:' . $rule . ';'; break;
			case 'bottom': $border = 'border-bottom:' . $rule . ';'; break;
			case 'both':   $border = 'border-top:' . $rule . ';border-bottom:' . $rule . ';'; break;
		}
	}

	return sprintf(
		'background-color:%s;color:%s;opacity:%s;font-size:%dpx;font-weight:%s;text-align:%s;line-height:%s;padding:%dpx %dpx %dpx %dpx;margin-top:%dpx;margin-bottom:%dpx;%s',
		esc_attr( $s['bg_color'] ),
		esc_attr( $s['text_color'] ),
		esc_attr( $s['opacity'] ),
		(int) $s['font_size'],
		esc_attr( $s['font_weight'] ),
		esc_attr( $s['text_align'] ),
		esc_attr( $s['line_height'] ),
		(int) $s['padding_top'],
		(int) $s['padding_right'],
		(int) $s['padding_bottom'],
		(int) $s['padding_left'],
		(int) $s['margin_top'],
		(int) $s['margin_bottom'],
		$border
	);
}

/**
 * Builds the disclaimer bar markup shared by auto-placement (EVOLNUX_Frontend),
 * the admin live preview (EVOLNUX_Admin), and the [evolnux_disclaimer] shortcode.
 *
 * @param array    $s                 Sanitized settings (EVOLNUX_Settings::get()).
 * @param string[] $extra_classes     Extra CSS classes to append (e.g. 'evolnux-preview-bar').
 * @param bool     $include_position  Whether to add the evolnux-pos-* class (fixed/top/bottom placement).
 *                                    Disabled for the shortcode so a manually placed bar never becomes
 *                                    position:fixed by surprise.
 * @param bool     $include_id        Whether to add id="evolnux-bar". Only the single auto-placed
 *                                    instance should carry this id — duplicate ids are invalid HTML.
 */
function evolnux_render_markup( array $s, array $extra_classes = [], bool $include_position = true, bool $include_id = true ): string {
	$style = evolnux_build_inline_style( $s );

	$fixed_style = '';
	if ( $include_position && in_array( $s['position'], [ 'fixed_top', 'fixed_bottom' ], true ) ) {
		$fixed_style = sprintf( 'z-index:%d;', (int) $s['z_index'] );
	}

	$classes = implode( ' ', array_filter( array_merge( [
		'evolnux-bar',
		$include_position ? 'evolnux-pos-' . sanitize_html_class( $s['position'] ) : '',
		'evolnux-w-'    . sanitize_html_class( $s['width_type'] ),
		'evolnux-resp-' . sanitize_html_class( $s['responsive'] ),
		$s['animation'] !== 'none' ? 'evolnux-anim-' . sanitize_html_class( $s['animation'] ) : '',
	], $extra_classes ) ) );

	ob_start();
	?>
	<div class="<?php echo esc_attr( $classes ); ?>"
	     <?php echo $include_id ? 'id="evolnux-bar"' : ''; ?>
	     role="region"
	     aria-label="<?php esc_attr_e( 'Disclaimer', 'evolnux-disclaimer-bar' ); ?>"
	     style="<?php echo esc_attr( $style . $fixed_style ); ?>">

		<?php if ( $s['width_type'] === 'boxed' ) : ?>
		<div class="evolnux-inner">
		<?php endif; ?>

		<div class="evolnux-content"><?php echo wp_kses_post( $s['content'] ); ?></div>

		<?php if ( ! empty( $s['dismissible'] ) ) : ?>
		<button type="button" class="evolnux-close" aria-label="<?php esc_attr_e( 'Close disclaimer', 'evolnux-disclaimer-bar' ); ?>">
			<?php echo esc_html( $s['dismiss_text'] ?: '×' ); ?>
		</button>
		<?php endif; ?>

		<?php if ( $s['width_type'] === 'boxed' ) : ?>
		</div>
		<?php endif; ?>

	</div>
	<?php
	return ob_get_clean();
}
