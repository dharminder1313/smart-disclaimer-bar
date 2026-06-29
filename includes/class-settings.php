<?php
if ( ! defined( 'ABSPATH' ) ) exit;

class EVOLNUX_Settings {

	public static function defaults(): array {
		return [
			// General
			'enabled'             => false,
			'content'             => '<strong>Disclaimer:</strong> The information provided on this website is for general informational purposes only and does not constitute professional advice.',

			// Display
			'position'            => 'top_bar',
			'custom_hook'         => '',
			'display_scope'       => 'entire',
			'selected_pages'      => [],
			'selected_posts'      => [],
			'selected_post_types' => [],
			'excluded_pages'      => [],
			'responsive'          => 'both',

			// Typography
			'font_size'           => '14',
			'font_weight'         => '400',
			'text_align'          => 'center',
			'line_height'         => '1.6',

			// Colors
			'text_color'          => '#5a4b00',
			'bg_color'            => '#fff8e1',
			'border_color'        => '#ffc107',
			'border_position'     => 'bottom',
			'border_width'        => '3',
			'opacity'             => '1',

			// Spacing
			'padding_top'         => '10',
			'padding_bottom'      => '10',
			'padding_left'        => '20',
			'padding_right'       => '20',
			'margin_top'          => '0',
			'margin_bottom'       => '0',

			// Layout
			'width_type'          => 'full',

			// Advanced
			'dismissible'         => false,
			'dismiss_text'        => '×',
			'dismiss_expiry'      => '7',
			'animation'           => 'fade_in',
			'z_index'             => '9999',
		];
	}

	public static function get(): array {
		return wp_parse_args( (array) get_option( EVOLNUX_OPTION_KEY, [] ), self::defaults() );
	}

	public static function sanitize( array $raw ): array {
		$d = self::defaults();
		$c = [];

		$c['enabled'] = ! empty( $raw['enabled'] );
		$c['content'] = wp_kses_post( $raw['content'] ?? $d['content'] );

		$valid_positions = [ 'top_bar', 'below_header', 'above_footer', 'fixed_bottom', 'fixed_top', 'custom_hook' ];
		$c['position']   = in_array( $raw['position'] ?? '', $valid_positions, true ) ? $raw['position'] : $d['position'];
		$c['custom_hook'] = sanitize_key( $raw['custom_hook'] ?? '' );

		$valid_scopes    = [ 'entire', 'homepage', 'selected_pages', 'selected_posts', 'selected_post_types' ];
		$c['display_scope'] = in_array( $raw['display_scope'] ?? '', $valid_scopes, true ) ? $raw['display_scope'] : $d['display_scope'];

		$c['selected_pages']      = array_map( 'absint', (array) ( $raw['selected_pages']      ?? [] ) );
		$c['selected_posts']      = array_map( 'absint', (array) ( $raw['selected_posts']      ?? [] ) );
		$c['selected_post_types'] = array_map( 'sanitize_key', (array) ( $raw['selected_post_types'] ?? [] ) );
		$c['excluded_pages']      = array_map( 'absint', (array) ( $raw['excluded_pages']      ?? [] ) );

		$valid_resp   = [ 'both', 'desktop', 'mobile' ];
		$c['responsive'] = in_array( $raw['responsive'] ?? '', $valid_resp, true ) ? $raw['responsive'] : $d['responsive'];

		// Typography
		$c['font_size']   = min( 64, max( 8, absint( $raw['font_size']  ?? $d['font_size'] ) ) );
		$valid_fw = [ '100','200','300','400','500','600','700','800','900','normal','bold' ];
		$c['font_weight'] = in_array( $raw['font_weight'] ?? '', $valid_fw, true ) ? $raw['font_weight'] : $d['font_weight'];
		$valid_ta = [ 'left', 'center', 'right', 'justify' ];
		$c['text_align']  = in_array( $raw['text_align'] ?? '', $valid_ta, true ) ? $raw['text_align'] : $d['text_align'];
		$c['line_height'] = (string) round( min( 4, max( 1, (float) ( $raw['line_height'] ?? $d['line_height'] ) ) ), 2 );

		// Colors
		$c['text_color']      = sanitize_hex_color( $raw['text_color']   ?? $d['text_color'] )   ?: $d['text_color'];
		$c['bg_color']        = sanitize_hex_color( $raw['bg_color']      ?? $d['bg_color'] )     ?: $d['bg_color'];
		$c['border_color']    = sanitize_hex_color( $raw['border_color']  ?? $d['border_color'] ) ?: $d['border_color'];
		$valid_bp = [ 'none', 'top', 'bottom', 'both' ];
		$c['border_position'] = in_array( $raw['border_position'] ?? '', $valid_bp, true ) ? $raw['border_position'] : $d['border_position'];
		$c['border_width']    = min( 20, max( 0, absint( $raw['border_width'] ?? $d['border_width'] ) ) );
		$c['opacity']         = (string) round( min( 1, max( 0.1, (float) ( $raw['opacity'] ?? $d['opacity'] ) ) ), 2 );

		// Spacing
		$c['padding_top']    = min( 200, max( 0, absint( $raw['padding_top']    ?? $d['padding_top'] ) ) );
		$c['padding_bottom'] = min( 200, max( 0, absint( $raw['padding_bottom'] ?? $d['padding_bottom'] ) ) );
		$c['padding_left']   = min( 300, max( 0, absint( $raw['padding_left']   ?? $d['padding_left'] ) ) );
		$c['padding_right']  = min( 300, max( 0, absint( $raw['padding_right']  ?? $d['padding_right'] ) ) );
		$c['margin_top']     = min( 200, max( 0, absint( $raw['margin_top']     ?? $d['margin_top'] ) ) );
		$c['margin_bottom']  = min( 200, max( 0, absint( $raw['margin_bottom']  ?? $d['margin_bottom'] ) ) );

		$c['width_type'] = in_array( $raw['width_type'] ?? '', [ 'full', 'boxed' ], true ) ? $raw['width_type'] : $d['width_type'];

		// Advanced
		$c['dismissible']    = ! empty( $raw['dismissible'] );
		$c['dismiss_text']   = sanitize_text_field( $raw['dismiss_text'] ?? $d['dismiss_text'] );
		$c['dismiss_expiry'] = min( 365, max( 0, absint( $raw['dismiss_expiry'] ?? $d['dismiss_expiry'] ) ) );

		$valid_anim  = [ 'none', 'fade_in', 'slide_down', 'slide_up' ];
		$c['animation'] = in_array( $raw['animation'] ?? '', $valid_anim, true ) ? $raw['animation'] : $d['animation'];
		$c['z_index']   = min( 999999, max( 1, absint( $raw['z_index'] ?? $d['z_index'] ) ) );

		return $c;
	}
}
