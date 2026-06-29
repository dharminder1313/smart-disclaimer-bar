<?php
if ( ! defined( 'ABSPATH' ) ) exit;

function evolnux_should_display( array $s ): bool {
	if ( empty( $s['enabled'] ) ) {
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
