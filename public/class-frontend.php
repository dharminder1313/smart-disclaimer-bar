<?php
if ( ! defined( 'ABSPATH' ) ) exit;

class EVOLNUX_Frontend {

	private array $settings = [];

	public function register( EVOLNUX_Loader $loader ): void {
		// Delay display logic until query is ready (conditional tags need it)
		$loader->add_action( 'wp', [ $this, 'maybe_init' ] );
		add_shortcode( 'evolnux_disclaimer', [ $this, 'render_shortcode' ] );
	}

	public function maybe_init(): void {
		$this->settings = EVOLNUX_Settings::get();

		$auto_display = evolnux_should_display( $this->settings );

		// Assets are needed both for the position-based auto bar and for a manually
		// placed [evolnux_disclaimer] shortcode, so load them whenever the feature is
		// enabled and within its scheduled window — not only when auto-placement fires.
		// This must happen on the wp_enqueue_scripts hook (not inside the shortcode
		// callback) so the stylesheet is still queued in time to print in wp_head.
		if ( $auto_display || ( ! empty( $this->settings['enabled'] ) && evolnux_within_schedule( $this->settings ) ) ) {
			add_action( 'wp_enqueue_scripts', [ $this, 'enqueue_assets' ] );
		}

		if ( ! $auto_display ) {
			return;
		}

		switch ( $this->settings['position'] ) {
			case 'top_bar':
				// wp_body_open fires right after <body> on WP 5.2+ themes
				add_action( 'wp_body_open', [ $this, 'render_bar' ], 1 );
				// Fallback: inject via wp_footer + JS to prepend to body
				add_action( 'wp_footer',    [ $this, 'maybe_fallback_render' ], 1 );
				break;

			case 'below_header':
				// Output early in body; JS repositions after <header>
				add_action( 'wp_body_open', [ $this, 'render_bar' ], 5 );
				add_action( 'wp_footer',    [ $this, 'maybe_fallback_render' ], 1 );
				break;

			case 'above_footer':
				add_action( 'wp_footer', [ $this, 'render_bar' ], 1 );
				break;

			case 'fixed_bottom':
			case 'fixed_top':
				// Fixed elements can live anywhere in DOM
				add_action( 'wp_footer', [ $this, 'render_bar' ], 99 );
				break;

			case 'custom_hook':
				$hook = sanitize_key( $this->settings['custom_hook'] );
				if ( $hook ) {
					add_action( $hook, [ $this, 'render_bar' ] );
				}
				break;
		}
	}

	public function enqueue_assets( ?array $s = null ): void {
		$s = $s ?? $this->settings;

		$css = EVOLNUX_PLUGIN_DIR . 'public/assets/css/disclaimer-bar.css';
		$js  = EVOLNUX_PLUGIN_DIR . 'public/assets/js/disclaimer-bar.js';

		wp_enqueue_style(
			'evolnux-bar',
			EVOLNUX_PLUGIN_URL . 'public/assets/css/disclaimer-bar.css',
			[],
			file_exists( $css ) ? (string) filemtime( $css ) : EVOLNUX_VERSION
		);

		wp_enqueue_script(
			'evolnux-bar',
			EVOLNUX_PLUGIN_URL . 'public/assets/js/disclaimer-bar.js',
			[],
			file_exists( $js ) ? (string) filemtime( $js ) : EVOLNUX_VERSION,
			true
		);

		wp_localize_script( 'evolnux-bar', 'evolnuxCfg', [
			'position'    => $s['position'],
			'animation'   => $s['animation'],
			'responsive'  => $s['responsive'],
			'dismissible' => (bool) $s['dismissible'],
			'expiry'      => (int) $s['dismiss_expiry'],
			'zIndex'      => (int) $s['z_index'],
		] );
	}

	// Tracks whether render_bar has already fired (avoids double output via fallback)
	private bool $rendered = false;

	public function render_bar(): void {
		$this->rendered = true;
		echo evolnux_render_markup( $this->settings );
	}

	/**
	 * [evolnux_disclaimer] shortcode — lets the bar be placed manually in content,
	 * widgets, or template code, independent of the Position/Display Scope settings.
	 * It still respects the master Enabled toggle and the schedule window, but ignores
	 * fixed-position styling (a manually placed bar shouldn't jump to the viewport edge).
	 */
	public function render_shortcode(): string {
		$s = $this->settings ?: EVOLNUX_Settings::get();

		if ( empty( $s['enabled'] ) || ! evolnux_within_schedule( $s ) ) {
			return '';
		}

		return evolnux_render_markup( $s, [ 'evolnux-shortcode-bar' ], false, false );
	}

	public function maybe_fallback_render(): void {
		if ( ! $this->rendered ) {
			// Theme didn't call wp_body_open — render now via footer (JS will reposition)
			$this->render_bar();
		}
	}
}
