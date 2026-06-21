<?php
if ( ! defined( 'ABSPATH' ) ) exit;

class SDB_Admin {

	public function register( SDB_Loader $loader ): void {
		$loader->add_action( 'admin_menu',             [ $this, 'add_menu' ] );
		$loader->add_action( 'admin_init',             [ $this, 'register_settings' ] );
		$loader->add_action( 'admin_enqueue_scripts',  [ $this, 'enqueue_assets' ] );
		$loader->add_action( 'wp_ajax_sdb_preview',    [ $this, 'ajax_preview' ] );
	}

	public function add_menu(): void {
		add_options_page(
			__( 'Disclaimer Manager', 'smart-disclaimer-bar' ),
			__( 'Disclaimer Manager', 'smart-disclaimer-bar' ),
			'manage_options',
			'smart-disclaimer-bar',
			[ $this, 'render_page' ]
		);
	}

	public function register_settings(): void {
		register_setting( 'sdb_group', SDB_OPTION_KEY, [
			'sanitize_callback' => [ 'SDB_Settings', 'sanitize' ],
			'default'           => SDB_Settings::defaults(),
		] );
	}

	public function enqueue_assets( string $hook ): void {
		if ( $hook !== 'settings_page_smart-disclaimer-bar' ) return;

		// WordPress colour picker
		wp_enqueue_style( 'wp-color-picker' );
		wp_enqueue_script( 'wp-color-picker' );

		// TinyMCE / WP editor
		wp_enqueue_editor();

		$css = SDB_PLUGIN_DIR . 'admin/assets/css/admin.css';
		$js  = SDB_PLUGIN_DIR . 'admin/assets/js/admin.js';

		wp_enqueue_style(
			'sdb-admin',
			SDB_PLUGIN_URL . 'admin/assets/css/admin.css',
			[ 'wp-color-picker' ],
			file_exists( $css ) ? (string) filemtime( $css ) : SDB_VERSION
		);

		wp_enqueue_script(
			'sdb-admin',
			SDB_PLUGIN_URL . 'admin/assets/js/admin.js',
			[ 'jquery', 'wp-color-picker' ],
			file_exists( $js ) ? (string) filemtime( $js ) : SDB_VERSION,
			true
		);

		wp_localize_script( 'sdb-admin', 'sdbAdmin', [
			'ajaxUrl' => admin_url( 'admin-ajax.php' ),
			'nonce'   => wp_create_nonce( 'sdb_preview' ),
			'i18n'    => [
				'previewTitle' => __( 'Live Preview', 'smart-disclaimer-bar' ),
				'previewError' => __( 'Preview failed. Please save settings first.', 'smart-disclaimer-bar' ),
			],
		] );
	}

	public function render_page(): void {
		include SDB_PLUGIN_DIR . 'admin/settings-page.php';
	}

	public function ajax_preview(): void {
		check_ajax_referer( 'sdb_preview', 'nonce' );
		if ( ! current_user_can( 'manage_options' ) ) wp_die();

		$raw      = isset( $_POST['settings'] ) ? (array) map_deep( wp_unslash( $_POST['settings'] ), 'wp_kses_post' ) : [];
		$settings = wp_parse_args( SDB_Settings::sanitize( (array) $raw ), SDB_Settings::defaults() );

		ob_start();
		self::render_bar_html( $settings );
		$html = ob_get_clean();

		wp_send_json_success( [ 'html' => $html ] );
	}

	public static function render_bar_html( array $s ): void {
		$style   = sdb_build_inline_style( $s );
		$classes = implode( ' ', array_filter( [
			'sdb-bar',
			'sdb-preview-bar',
			'sdb-width-' . $s['width_type'],
			$s['animation'] !== 'none' ? 'sdb-anim-' . $s['animation'] : '',
		] ) );
		?>
		<div class="<?php echo esc_attr( $classes ); ?>" style="<?php echo esc_attr( $style ); ?>">
			<?php if ( $s['width_type'] === 'boxed' ) : ?><div class="sdb-container"><?php endif; ?>

			<div class="sdb-content"><?php echo wp_kses_post( $s['content'] ); ?></div>

			<?php if ( ! empty( $s['dismissible'] ) ) : ?>
			<button type="button" class="sdb-dismiss" aria-label="<?php esc_attr_e( 'Close disclaimer', 'smart-disclaimer-bar' ); ?>">
				<?php echo esc_html( $s['dismiss_text'] ?: '×' ); ?>
			</button>
			<?php endif; ?>

			<?php if ( $s['width_type'] === 'boxed' ) : ?></div><?php endif; ?>
		</div>
		<?php
	}
}
