<?php
if ( ! defined( 'ABSPATH' ) ) exit;

class SDB_Frontend {

	private array $settings = [];

	public function register( SDB_Loader $loader ): void {
		// Delay display logic until query is ready (conditional tags need it)
		$loader->add_action( 'wp', [ $this, 'maybe_init' ] );
	}

	public function maybe_init(): void {
		$this->settings = SDB_Settings::get();

		if ( ! sdb_should_display( $this->settings ) ) {
			return;
		}

		add_action( 'wp_enqueue_scripts', [ $this, 'enqueue_assets' ] );

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

	public function enqueue_assets(): void {
		$css = SDB_PLUGIN_DIR . 'public/assets/css/disclaimer-bar.css';
		$js  = SDB_PLUGIN_DIR . 'public/assets/js/disclaimer-bar.js';

		wp_enqueue_style(
			'sdb-bar',
			SDB_PLUGIN_URL . 'public/assets/css/disclaimer-bar.css',
			[],
			file_exists( $css ) ? (string) filemtime( $css ) : SDB_VERSION
		);

		wp_enqueue_script(
			'sdb-bar',
			SDB_PLUGIN_URL . 'public/assets/js/disclaimer-bar.js',
			[],
			file_exists( $js ) ? (string) filemtime( $js ) : SDB_VERSION,
			true
		);

		wp_localize_script( 'sdb-bar', 'sdbCfg', [
			'position'    => $this->settings['position'],
			'animation'   => $this->settings['animation'],
			'responsive'  => $this->settings['responsive'],
			'dismissible' => (bool) $this->settings['dismissible'],
			'expiry'      => (int) $this->settings['dismiss_expiry'],
			'zIndex'      => (int) $this->settings['z_index'],
		] );
	}

	// Tracks whether render_bar has already fired (avoids double output via fallback)
	private bool $rendered = false;

	public function render_bar(): void {
		$this->rendered = true;
		$s = $this->settings;
		$style = sdb_build_inline_style( $s );

		$fixed_style = '';
		if ( in_array( $s['position'], [ 'fixed_top', 'fixed_bottom' ], true ) ) {
			$fixed_style = sprintf( 'z-index:%d;', (int) $s['z_index'] );
		}

		$classes = implode( ' ', array_filter( [
			'sdb-bar',
			'sdb-pos-' . sanitize_html_class( $s['position'] ),
			'sdb-w-'   . sanitize_html_class( $s['width_type'] ),
			'sdb-resp-' . sanitize_html_class( $s['responsive'] ),
			$s['animation'] !== 'none' ? 'sdb-anim-' . sanitize_html_class( $s['animation'] ) : '',
		] ) );
		?>
		<div class="<?php echo esc_attr( $classes ); ?>"
		     id="sdb-bar"
		     role="complementary"
		     aria-label="<?php esc_attr_e( 'Disclaimer', 'smart-disclaimer-bar' ); ?>"
		     style="<?php echo esc_attr( $style . $fixed_style ); ?>">

			<?php if ( $s['width_type'] === 'boxed' ) : ?>
			<div class="sdb-inner">
			<?php endif; ?>

			<div class="sdb-content"><?php echo wp_kses_post( $s['content'] ); ?></div>

			<?php if ( ! empty( $s['dismissible'] ) ) : ?>
			<button type="button" class="sdb-close" aria-label="<?php esc_attr_e( 'Close disclaimer', 'smart-disclaimer-bar' ); ?>">
				<?php echo esc_html( $s['dismiss_text'] ?: '×' ); ?>
			</button>
			<?php endif; ?>

			<?php if ( $s['width_type'] === 'boxed' ) : ?>
			</div>
			<?php endif; ?>

		</div>
		<?php
	}

	public function maybe_fallback_render(): void {
		if ( ! $this->rendered ) {
			// Theme didn't call wp_body_open — render now via footer (JS will reposition)
			$this->render_bar();
		}
	}
}
