<?php
/**
 * Addons Integration.
 */

namespace PremiumAddons\Includes;

use PremiumAddons\Includes\Helper_Functions;
use PremiumAddons\Admin\Includes\Admin_Helper;
use PremiumAddons\Modules\Premium_Equal_Height\Module as Equal_Height;
use PremiumAddons\Modules\PA_Display_Conditions\Module as Display_Conditions;
use PremiumAddons\Modules\PremiumSectionFloatingEffects\Module as Floating_Effects;
use PremiumAddons\Modules\Woocommerce\Module as Woocommerce;
use PremiumAddons\Includes\Assets_Manager;
use PremiumAddons\Includes\Premium_Template_Tags;

if ( ! defined( 'ABSPATH' ) ) {
	exit();
}

/**
 * Class Addons_Integration.
 */
class Addons_Integration {

	/**
	 * Class instance
	 *
	 * @var instance
	 */
	private static $instance = null;

	/**
	 * Modules
	 *
	 * @var modules
	 */
	private static $modules = null;

	/**
	 * Maps Keys
	 *
	 * @var maps
	 */
	private static $maps = null;

	/**
	 * Template Instance
	 *
	 * @var template_instance
	 */
	protected $template_instance;

	/**
	 * Cross-Site CDN URL.
	 *
	 * @since  4.0.0
	 * @var (String) URL
	 */
	public $cdn_url;

	/**
	 * Initialize integration hooks
	 *
	 * @return void
	 */
	public function __construct() {

		self::$modules = Admin_Helper::get_enabled_elements();

		self::$maps = Admin_Helper::get_integrations_settings();

		$this->template_instance = Premium_Template_Tags::getInstance();

		add_action( 'elementor/editor/before_enqueue_styles', array( $this, 'enqueue_editor_styles' ) );

		add_action( 'elementor/editor/after_enqueue_styles', array( $this, 'load_live_editor_modal' ) );

		add_action( 'elementor/editor/after_enqueue_scripts', array( $this, 'live_editor_enqueue' ) );

		add_action( 'wp_ajax_handle_live_editor', array( $this, 'handle_live_editor' ) );

		add_action( 'wp_ajax_check_temp_validity', array( $this, 'check_temp_validity' ) );

		add_action( 'wp_ajax_update_template_title', array( $this, 'update_template_title' ) );

		add_action( 'elementor/editor/before_enqueue_scripts', array( $this, 'enqueue_editor_scripts' ) );

		add_action( 'elementor/preview/enqueue_styles', array( $this, 'enqueue_preview_styles' ) );

		add_action( 'elementor/frontend/after_register_styles', array( $this, 'register_frontend_styles' ) );

		add_action( 'elementor/frontend/after_register_scripts', array( $this, 'register_frontend_scripts' ) );

		add_action( 'wp_ajax_get_elementor_template_content', array( $this, 'get_template_content' ) );

		if ( defined( 'ELEMENTOR_VERSION' ) ) {
			if ( version_compare( ELEMENTOR_VERSION, '3.5.0', '>=' ) ) {
				add_action( 'elementor/controls/register', array( $this, 'init_pa_controls' ) );
				add_action( 'elementor/widgets/register', array( $this, 'widgets_area' ) );
			} else {
				add_action( 'elementor/controls/controls_registered', array( $this, 'init_pa_controls' ) );
				add_action( 'elementor/widgets/widgets_registered', array( $this, 'widgets_area' ) );
			}
		}

		add_action( 'elementor/editor/after_enqueue_scripts', array( $this, 'after_enqueue_scripts' ) );

		$this->load_pa_extensions();

		$cross_enabled = isset( self::$modules['premium-cross-domain'] ) ? self::$modules['premium-cross-domain'] : 1;

		if ( $cross_enabled ) {

			add_action( 'elementor/editor/before_enqueue_scripts', array( $this, 'enqueue_editor_cp_scripts' ), 99 );

			Addons_Cross_CP::get_instance();

		}

	}

	/**
	 * Live Editor Enqueue.
	 *
	 * @access public
	 * @since 4.8.10
	 */
	public function live_editor_enqueue() {

		wp_enqueue_script(
			'live-editor',
			PREMIUM_ADDONS_URL . 'assets/editor/js/live-editor.js',
			array( 'elementor-editor', 'jquery' ),
			PREMIUM_ADDONS_VERSION,
			true
		);

		$live_editor_data = array(
			'ajaxurl' => esc_url( admin_url( 'admin-ajax.php' ) ),
			'nonce'   => wp_create_nonce( 'pa-live-editor' ),
		);

		wp_localize_script( 'live-editor', 'liveEditor', $live_editor_data );

	}

	/**
	 * Update Template Title.
	 *
	 * @access public
	 * @since 4.8.10
	 */
	public function update_template_title() {
		check_ajax_referer( 'pa-live-editor', 'security' );

		if ( ! isset( $_POST['title'] ) || ! isset( $_POST['id'] ) ) {
			wp_send_json_error();
		}

		$res = wp_update_post(
			array(
				'ID'         => sanitize_text_field( wp_unslash( $_POST['id'] ) ),
				'post_title' => sanitize_text_field( wp_unslash( $_POST['title'] ) ),
			)
		);

		wp_send_json_success( $res );
	}

	/**
	 * Check Temp Validity.
	 * Checks if the template is valid ( has content) or not,
	 * And DELETE the post if it's invalid.
	 *
	 * @access public
	 * @since 4.9.1
	 */
	public function check_temp_validity() {

		check_ajax_referer( 'pa-live-editor', 'security' );

		if ( ! isset( $_POST['templateID'] ) ) {
			wp_send_json_error( 'template ID is not set' );
		}

		$temp_id = sanitize_text_field( wp_unslash( $_POST['templateID'] ) );

		$template_content = $this->template_instance->get_template_content( $temp_id, true );

		if ( empty( $template_content ) || ! isset( $template_content ) ) {

			$res = wp_delete_post( $temp_id, true );

			if ( ! is_wp_error( $res ) ) {
				$res = 'Template Deleted.';
			}
		} else {
			$res = 'Template Has Content.';
		}

		wp_send_json_success( $res );
	}

	/**
	 * Handle Live Editor Modal.
	 *
	 * @access public
	 * @since 4.8.10
	 */
	public function handle_live_editor() {

		check_ajax_referer( 'pa-live-editor', 'security' );

		if ( ! isset( $_POST['key'] ) ) {
			wp_send_json_error();
		}

		$post_name  = 'pa-dynamic-temp-' . sanitize_text_field( wp_unslash( $_POST['key'] ) );
		$post_title = '';
		$args       = array(
			'post_type'              => 'elementor_library',
			'name'                   => $post_name,
			'post_status'            => 'publish',
			'update_post_term_cache' => false,
			'update_post_meta_cache' => false,
			'posts_per_page'         => 1,
		);

		$post = get_posts( $args );

		if ( empty( $post ) ) { // create a new one.

			$key        = sanitize_text_field( wp_unslash( $_POST['key'] ) );
			$post_title = 'PA Template | #' . substr( md5( $key ), 0, 4 );

			$params = array(
				'post_content' => '',
				'post_type'    => 'elementor_library',
				'post_title'   => $post_title,
				'post_name'    => $post_name,
				'post_status'  => 'publish',
				'meta_input'   => array(
					'_elementor_edit_mode'     => 'builder',
					'_elementor_template_type' => 'page',
					'_wp_page_template'        => 'elementor_canvas',
				),
			);

			$post_id = wp_insert_post( $params );

		} else { // edit post.
			$post_id    = $post[0]->ID;
			$post_title = $post[0]->post_title;
		}

		$edit_url = get_admin_url() . '/post.php?post=' . $post_id . '&action=elementor';

		$result = array(
			'url'   => $edit_url,
			'id'    => $post_id,
			'title' => $post_title,
		);

		wp_send_json_success( $result );
	}

	/**
	 * Load Live Editor Modal.
	 * Puts live editor popup html into the editor.
	 *
	 * @access public
	 * @since 4.8.10
	 */
	public function load_live_editor_modal() {
		ob_start();
		include_once PREMIUM_ADDONS_PATH . 'includes/live-editor-modal.php';
		$output = ob_get_contents();
		ob_end_clean();
		echo $output; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	}


	/**
	 * After Enquque Scripts
	 *
	 * Loads editor scripts for our controls.
	 *
	 * @access public
	 * @return void
	 */
	public function after_enqueue_scripts() {

		wp_enqueue_script(
			'pa-eq-editor',
			PREMIUM_ADDONS_URL . 'assets/editor/js/editor.js',
			array( 'elementor-editor', 'jquery' ),
			PREMIUM_ADDONS_VERSION,
			true
		);

		if ( self::$modules['premium-blog'] || self::$modules['pa-display-conditions'] ) {
			wp_localize_script(
				'pa-eq-editor',
				'PremiumSettings',
				array(
					'ajaxurl' => esc_url( admin_url( 'admin-ajax.php' ) ),
					'nonce'   => wp_create_nonce( 'pa-blog-widget-nonce' ),
				)
			);
		}

		wp_localize_script(
			'pa-eq-editor',
			'PremiumPanelSettings',
			array(
				'papro_installed' => Helper_Functions::check_papro_version(),
				'papro_widgets'   => Admin_Helper::get_pro_elements(),
			)
		);

	}

	/**
	 * Loads plugin icons font
	 *
	 * @since 1.0.0
	 * @access public
	 * @return void
	 */
	public function enqueue_editor_styles() {

		$theme = Helper_Functions::get_elementor_ui_theme();

		wp_enqueue_style(
			'pa-editor',
			PREMIUM_ADDONS_URL . 'assets/editor/css/style.css',
			array(),
			PREMIUM_ADDONS_VERSION
		);

		// Enqueue required style for Elementor dark UI Theme.
		if ( 'dark' === $theme ) {

			wp_add_inline_style(
				'pa-editor',
				'.elementor-panel .elementor-control-section_pa_docs .elementor-panel-heading-title.elementor-panel-heading-title,
				.elementor-control-raw-html.editor-pa-doc a {
					color: #e0e1e3 !important;
				}
				[class^="pa-"]::after,
				[class*=" pa-"]::after {
					color: #aaa;
                    opacity: 1 !important;
				}
                .premium-promotion-dialog .premium-promotion-btn {
                    background-color: #202124 !important
                }'
			);

		}

		$badge_text = Helper_Functions::get_badge();

		$dynamic_css = sprintf( '#elementor-panel [class^="pa-"]::after, #elementor-panel [class*=" pa-"]::after { content: "%s"; }', $badge_text );

		wp_add_inline_style( 'pa-editor', $dynamic_css );

	}

	/**
	 * Register Frontend CSS files
	 *
	 * @since 2.9.0
	 * @access public
	 */
	public function register_frontend_styles() {

		$dir    = Helper_Functions::get_styles_dir();
		$suffix = Helper_Functions::get_assets_suffix();

		$is_rtl = is_rtl() ? '-rtl' : '';

		wp_register_style(
			'font-awesome-5-all',
			ELEMENTOR_ASSETS_URL . 'lib/font-awesome/css/all.min.css',
			false,
			PREMIUM_ADDONS_VERSION
		);

		wp_register_style(
			'pa-prettyphoto',
			PREMIUM_ADDONS_URL . 'assets/frontend/' . $dir . '/prettyphoto' . $is_rtl . $suffix . '.css',
			array(),
			PREMIUM_ADDONS_VERSION,
			'all'
		);

		wp_register_style(
			'pa-slick',
			PREMIUM_ADDONS_URL . 'assets/frontend/' . $dir . '/slick' . $is_rtl . $suffix . '.css',
			array(),
			PREMIUM_ADDONS_VERSION,
			'all'
		);

		$assets_gen_enabled = self::$modules['premium-assets-generator'] ? true : false;

		$type = get_post_type();

		if ( $assets_gen_enabled && ( 'page' === $type || 'post' === $type ) ) {

			$css_path = '/pa-frontend' . $is_rtl . '-' . Assets_Manager::$post_id . $suffix . '.css';

			if ( Assets_Manager::$is_updated && file_exists( PREMIUM_ASSETS_PATH . $css_path ) ) {
				wp_enqueue_style(
					'pa-frontend',
					PREMIUM_ASSETS_URL . $css_path,
					array(),
					time(),
					'all'
				);
			}
		}

		// If the assets are not ready, or file does not exist for any reson.
		if ( ! wp_style_is( 'pa-frontend', 'enqueued' ) ) {
			$this->register_old_styles( $dir, $is_rtl, $suffix );
		}

	}

	/**
	 * Register Old Styles
	 *
	 * @since 4.9.0
	 * @access public
	 *
	 * @param string $directory style directory.
	 * @param string $is_rtl page direction.
	 * @param string $suffix file suffix.
	 */
	public function register_old_styles( $directory, $is_rtl, $suffix ) {

		wp_register_style(
			'premium-addons',
			PREMIUM_ADDONS_URL . 'assets/frontend/' . $directory . '/premium-addons' . $is_rtl . $suffix . '.css',
			array(),
			PREMIUM_ADDONS_VERSION,
			'all'
		);

	}

	/**
	 * Registers required JS files
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function register_frontend_scripts() {

		$maps_settings = self::$maps;

		$dir    = Helper_Functions::get_scripts_dir();
		$suffix = Helper_Functions::get_assets_suffix();

		$locale             = isset( $maps_settings['premium-map-locale'] ) ? $maps_settings['premium-map-locale'] : 'en';
		$assets_gen_enabled = self::$modules['premium-assets-generator'] ? true : false;

		$type = get_post_type();

		if ( $assets_gen_enabled && ( 'page' === $type || 'post' === $type ) ) {

			// If the elemens are cached and ready to generate.
			if ( Assets_Manager::$is_updated ) {
				Assets_Manager::generate_asset_file( 'js' );
				Assets_Manager::generate_asset_file( 'css' );
			}

			$js_path = '/pa-frontend-' . Assets_Manager::$post_id . $suffix . '.js';

			if ( file_exists( PREMIUM_ASSETS_PATH . $js_path ) ) {

				wp_enqueue_script(
					'pa-frontend',
					PREMIUM_ASSETS_URL . $js_path,
					array( 'jquery' ),
					time(),
					true
				);

				wp_localize_script(
					'pa-frontend',
					'PremiumSettings',
					array(
						'ajaxurl' => esc_url( admin_url( 'admin-ajax.php' ) ),
						'nonce'   => wp_create_nonce( 'pa-blog-widget-nonce' ),
					)
				);

				if ( class_exists( 'woocommerce' ) ) {
					wp_localize_script(
						'pa-frontend',
						'PremiumWooSettings',
						array(
							'ajaxurl'        => esc_url( admin_url( 'admin-ajax.php' ) ),
							'products_nonce' => wp_create_nonce( 'pa-woo-products-nonce' ),
							'qv_nonce'       => wp_create_nonce( 'pa-woo-qv-nonce' ),
							'cta_nonce'      => wp_create_nonce( 'pa-woo-cta-nonce' ),
							'woo_cart_url'   => get_permalink( wc_get_page_id( 'cart' ) ),
						)
					);
				}
			}
		}

		// If the assets are not ready, or file does not exist for any reson.
		if ( ! wp_script_is( 'pa-frontend', 'enqueued' ) ) {
			$this->register_old_scripts( $dir, $suffix );
		}

		wp_register_script(
			'prettyPhoto-js',
			PREMIUM_ADDONS_URL . 'assets/frontend/' . $dir . '/prettyPhoto' . $suffix . '.js',
			array( 'jquery' ),
			PREMIUM_ADDONS_VERSION,
			true
		);

		wp_register_script(
			'pa-vticker',
			PREMIUM_ADDONS_URL . 'assets/frontend/' . $dir . '/vticker' . $suffix . '.js',
			array( 'jquery' ),
			PREMIUM_ADDONS_VERSION,
			true
		);

		wp_register_script(
			'pa-typed',
			PREMIUM_ADDONS_URL . 'assets/frontend/' . $dir . '/typed' . $suffix . '.js',
			array( 'jquery' ),
			PREMIUM_ADDONS_VERSION,
			true
		);

		wp_register_script(
			'pa-countdown',
			PREMIUM_ADDONS_URL . 'assets/frontend/' . $dir . '/jquery-countdown' . $suffix . '.js',
			array( 'jquery' ),
			PREMIUM_ADDONS_VERSION,
			true
		);

		wp_register_script(
			'isotope-js',
			PREMIUM_ADDONS_URL . 'assets/frontend/' . $dir . '/isotope' . $suffix . '.js',
			array( 'jquery' ),
			PREMIUM_ADDONS_VERSION,
			true
		);

		wp_register_script(
			'pa-modal',
			PREMIUM_ADDONS_URL . 'assets/frontend/' . $dir . '/modal' . $suffix . '.js',
			array( 'jquery' ),
			PREMIUM_ADDONS_VERSION,
			true
		);

		wp_register_script(
			'pa-maps',
			PREMIUM_ADDONS_URL . 'assets/frontend/' . $dir . '/premium-maps' . $suffix . '.js',
			array( 'jquery' ),
			PREMIUM_ADDONS_VERSION,
			true
		);

		wp_register_script(
			'pa-vscroll',
			PREMIUM_ADDONS_URL . 'assets/frontend/' . $dir . '/premium-vscroll' . $suffix . '.js',
			array( 'jquery' ),
			PREMIUM_ADDONS_VERSION,
			true
		);

		wp_register_script(
			'pa-slimscroll',
			PREMIUM_ADDONS_URL . 'assets/frontend/' . $dir . '/jquery-slimscroll' . $suffix . '.js',
			array( 'jquery' ),
			PREMIUM_ADDONS_VERSION,
			true
		);

		wp_register_script(
			'pa-iscroll',
			PREMIUM_ADDONS_URL . 'assets/frontend/' . $dir . '/iscroll' . $suffix . '.js',
			array( 'jquery' ),
			PREMIUM_ADDONS_VERSION,
			true
		);

		wp_register_script(
			'pa-tilt',
			PREMIUM_ADDONS_URL . 'assets/frontend/' . $dir . '/universal-tilt' . $suffix . '.js',
			array( 'jquery' ),
			PREMIUM_ADDONS_VERSION,
			true
		);

		wp_register_script(
			'lottie-js',
			PREMIUM_ADDONS_URL . 'assets/frontend/' . $dir . '/lottie' . $suffix . '.js',
			array(
				'jquery',
				'elementor-waypoints',
			),
			PREMIUM_ADDONS_VERSION,
			true
		);

		wp_register_script(
			'pa-tweenmax',
			PREMIUM_ADDONS_URL . 'assets/frontend/' . $dir . '/TweenMax' . $suffix . '.js',
			array( 'jquery' ),
			PREMIUM_ADDONS_VERSION,
			true
		);

		wp_register_script(
			'pa-headroom',
			PREMIUM_ADDONS_URL . 'assets/frontend/' . $dir . '/headroom' . $suffix . '.js',
			array( 'jquery' ),
			PREMIUM_ADDONS_VERSION
		);

		wp_register_script(
			'pa-menu',
			PREMIUM_ADDONS_URL . 'assets/frontend/' . $dir . '/premium-nav-menu' . $suffix . '.js',
			array( 'jquery' ),
			PREMIUM_ADDONS_VERSION,
			true
		);

		if ( $maps_settings['premium-map-cluster'] ) {
			wp_register_script(
				'pa-maps-cluster',
				PREMIUM_ADDONS_URL . 'assets/frontend/' . $dir . '/markerclusterer' . $suffix . '.js',
				array(),
				'1.0.1',
				false
			);
		}

		if ( $maps_settings['premium-map-disable-api'] && '1' !== $maps_settings['premium-map-api'] ) {
			$api = sprintf( 'https://maps.googleapis.com/maps/api/js?key=%1$s&callback=initMap&language=%2$s', $maps_settings['premium-map-api'], $locale );
			wp_register_script(
				'pa-maps-api',
				$api,
				array(),
				PREMIUM_ADDONS_VERSION,
				true
			);
		}

		wp_register_script(
			'pa-slick',
			PREMIUM_ADDONS_URL . 'assets/frontend/' . $dir . '/slick' . $suffix . '.js',
			array( 'jquery' ),
			PREMIUM_ADDONS_VERSION,
			true
		);

		wp_register_script(
			'pa-anime',
			PREMIUM_ADDONS_URL . 'assets/frontend/' . $dir . '/anime' . $suffix . '.js',
			array( 'jquery' ),
			PREMIUM_ADDONS_VERSION,
			true
		);

		wp_register_script(
			'pa-feffects',
			PREMIUM_ADDONS_URL . 'assets/frontend/' . $dir . '/premium-floating-effects' . $suffix . '.js',
			array( 'jquery' ),
			PREMIUM_ADDONS_VERSION,
			true
		);

		wp_localize_script(
			'premium-addons',
			'PremiumSettings',
			array(
				'ajaxurl' => esc_url( admin_url( 'admin-ajax.php' ) ),
				'nonce'   => wp_create_nonce( 'pa-blog-widget-nonce' ),
			)
		);

		wp_register_script(
			'pa-eq-height',
			PREMIUM_ADDONS_URL . 'assets/frontend/' . $dir . '/premium-eq-height' . $suffix . '.js',
			array( 'jquery' ),
			PREMIUM_ADDONS_VERSION,
			true
		);

		wp_register_script(
			'pa-dis-conditions',
			PREMIUM_ADDONS_URL . 'assets/frontend/' . $dir . '/premium-dis-conditions' . $suffix . '.js',
			array( 'jquery' ),
			PREMIUM_ADDONS_VERSION,
			true
		);

		wp_register_script(
			'pa-gsap',
			PREMIUM_ADDONS_URL . 'assets/frontend/' . $dir . '/pa-gsap' . $suffix . '.js',
			array( 'jquery' ),
			PREMIUM_ADDONS_VERSION,
			true
		);

		wp_register_script(
			'pa-motionpath',
			PREMIUM_ADDONS_URL . 'assets/frontend/' . $dir . '/motionpath' . $suffix . '.js',
			array(
				'elementor-waypoints',
				'jquery',
			),
			PREMIUM_ADDONS_VERSION,
			true
		);

		wp_register_script(
			'pa-fontawesome-all',
			PREMIUM_ADDONS_URL . 'assets/frontend/' . $dir . '/fontawesome-all' . $suffix . '.js',
			array( 'jquery' ),
			PREMIUM_ADDONS_VERSION,
			true
		);

		wp_register_script(
			'pa-scrolltrigger',
			PREMIUM_ADDONS_URL . 'assets/frontend/' . $dir . '/scrollTrigger' . $suffix . '.js',
			array( 'jquery' ),
			PREMIUM_ADDONS_VERSION,
			true
		);

		// Localize jQuery with required data for Global Add-ons.
		if ( self::$modules['premium-floating-effects'] ) {
			wp_localize_script(
				'pa-feffects',
				'PremiumFESettings',
				array(
					'papro_installed' => Helper_Functions::check_papro_version(),
				)
			);
		}

	}

	/**
	 * Register Old Scripts
	 *
	 * @since 4.9.0
	 * @access public
	 *
	 * @param string $directory script directory.
	 * @param string $suffix file suffix.
	 */
	public function register_old_scripts( $directory, $suffix ) {

		wp_register_script(
			'premium-addons',
			PREMIUM_ADDONS_URL . 'assets/frontend/' . $directory . '/premium-addons' . $suffix . '.js',
			array( 'jquery' ),
			PREMIUM_ADDONS_VERSION,
			true
		);

		// We need to make sure premium-woocommerce.js will not be loaded twice if assets are generated.
		if ( class_exists( 'woocommerce' ) ) {
			wp_register_script(
				'premium-woocommerce',
				PREMIUM_ADDONS_URL . 'assets/frontend/' . $directory . '/premium-woo-products' . $suffix . '.js',
				array( 'jquery' ),
				PREMIUM_ADDONS_VERSION,
				true
			);

			wp_localize_script(
				'premium-woocommerce',
				'PremiumWooSettings',
				array(
					'ajaxurl'        => esc_url( admin_url( 'admin-ajax.php' ) ),
					'products_nonce' => wp_create_nonce( 'pa-woo-products-nonce' ),
					'qv_nonce'       => wp_create_nonce( 'pa-woo-qv-nonce' ),
					'cta_nonce'      => wp_create_nonce( 'pa-woo-cta-nonce' ),
					'woo_cart_url'   => get_permalink( wc_get_page_id( 'cart' ) ),
				)
			);
		}

	}

	/**
	 * Enqueue Preview CSS files
	 *
	 * @since 2.9.0
	 * @access public
	 */
	public function enqueue_preview_styles() {

		wp_enqueue_style( 'pa-prettyphoto' );

		wp_enqueue_style( 'premium-addons' );

		wp_enqueue_style( 'pa-slick' );

	}

	/**
	 * Load widgets require function
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function widgets_area() {
		$this->widgets_register();
	}

	/**
	 * Requires widgets files
	 *
	 * @since 1.0.0
	 * @access private
	 */
	private function widgets_register() {

		$enabled_elements = self::$modules;

		foreach ( glob( PREMIUM_ADDONS_PATH . 'widgets/*.php' ) as $file ) {

			$slug = basename( $file, '.php' );

			// Fixes the conflict between Lottie widget/addon keys.
			if ( 'premium-lottie' === $slug ) {

				// Check if Lottie widget switcher value was saved before.
				$saved_options = get_option( 'pa_save_settings' );

				$slug = 'premium-lottie-widget';

			}

			$enabled = isset( $enabled_elements[ $slug ] ) ? $enabled_elements[ $slug ] : '';

			if ( filter_var( $enabled, FILTER_VALIDATE_BOOLEAN ) || ! $enabled_elements ) {
				$this->register_addon( $file );
			}
		}

	}

	/**
	 * Register Woocommerce Widgets.
	 *
	 * @since 4.0.0
	 * @access private
	 */
	private function woo_widgets_register() {

		$enabled_elements = self::$modules;

		foreach ( glob( PREMIUM_ADDONS_PATH . 'modules/woocommerce/widgets/*.php' ) as $file ) {

			$slug = basename( $file, '.php' );

			$enabled = isset( $enabled_elements[ $slug ] ) ? $enabled_elements[ $slug ] : '';

			if ( filter_var( $enabled, FILTER_VALIDATE_BOOLEAN ) || ! $enabled_elements ) {

				$this->register_addon( $file );
			}
		}
	}

	/**
	 * Enqueue editor scripts
	 *
	 * @since 3.2.5
	 * @access public
	 */
	public function enqueue_editor_scripts() {

		$map_enabled = isset( self::$modules['premium-maps'] ) ? self::$modules['premium-maps'] : 1;

		if ( $map_enabled ) {

			$premium_maps_api = self::$maps['premium-map-api'];

			$locale = isset( self::$maps['premium-map-locale'] ) ? self::$maps['premium-map-locale'] : 'en';

			$disable_api = self::$maps['premium-map-disable-api'];

			if ( $disable_api && '1' !== $premium_maps_api ) {

				$api = sprintf( 'https://maps.googleapis.com/maps/api/js?key=%1$s&language=%2$s', $premium_maps_api, $locale );
				wp_enqueue_script(
					'pa-maps-api',
					$api,
					array(),
					PREMIUM_ADDONS_VERSION,
					false
				);

			}

			wp_enqueue_script(
				'pa-maps-finder',
				PREMIUM_ADDONS_URL . 'assets/editor/js/pa-maps-finder.js',
				array( 'jquery' ),
				PREMIUM_ADDONS_VERSION,
				true
			);

		}

	}

	/**
	 * Load Cross Domain Copy Paste JS Files.
	 *
	 * @since 3.21.1
	 */
	public function enqueue_editor_cp_scripts() {

		wp_enqueue_script(
			'premium-xdlocalstorage',
			PREMIUM_ADDONS_URL . 'assets/editor/js/xdlocalstorage.js',
			null,
			PREMIUM_ADDONS_VERSION,
			true
		);

		wp_enqueue_script(
			'premium-cross-cp',
			PREMIUM_ADDONS_URL . 'assets/editor/js/premium-cross-cp.js',
			array( 'jquery', 'elementor-editor', 'premium-xdlocalstorage' ),
			PREMIUM_ADDONS_VERSION,
			true
		);

		// Check for required Compatible Elementor version.
		if ( ! version_compare( ELEMENTOR_VERSION, '3.1.0', '>=' ) ) {
			$elementor_old = true;
		} else {
			$elementor_old = false;
		}

		wp_localize_script(
			'jquery',
			'premium_cross_cp',
			array(
				'ajax_url'            => admin_url( 'admin-ajax.php' ),
				'nonce'               => wp_create_nonce( 'premium_cross_cp_import' ),
				'elementorCompatible' => $elementor_old,
			)
		);
	}

	/**
	 * Get Template Content
	 *
	 * Get Elementor template HTML content.
	 *
	 * @since 3.2.6
	 * @access public
	 */
	public function get_template_content() {

		$template = isset( $_GET['templateID'] ) ? sanitize_text_field( wp_unslash( $_GET['templateID'] ) ) : '';

		if ( empty( $template ) ) {
			wp_send_json_error( '' );
		}

		$template_content = $this->template_instance->get_template_content( $template );

		if ( empty( $template_content ) || ! isset( $template_content ) ) {
			wp_send_json_error( '' );
		}

		$data = array(
			'template_content' => $template_content,
		);

		wp_send_json_success( $data );
	}

	/**
	 *
	 * Register addon by file name.
	 *
	 * @access public
	 *
	 * @param  string $file            File name.
	 *
	 * @return void
	 */
	public function register_addon( $file ) {

		$widgets_manager = \Elementor\Plugin::instance()->widgets_manager;

		$base  = basename( str_replace( '.php', '', $file ) );
		$class = ucwords( str_replace( '-', ' ', $base ) );
		$class = str_replace( ' ', '_', $class );
		$class = sprintf( 'PremiumAddons\Widgets\%s', $class );

		if ( 'PremiumAddons\Widgets\Premium_Contactform' !== $class ) {
			require $file;
		} else {
			if ( function_exists( 'wpcf7' ) ) {
				require $file;
			}
		}

		if ( 'PremiumAddons\Widgets\Premium_Videobox' === $class ) {
			require_once PREMIUM_ADDONS_PATH . 'widgets/dep/urlopen.php';
		}

		if ( class_exists( $class, false ) ) {
			if ( defined( 'ELEMENTOR_VERSION' ) && version_compare( ELEMENTOR_VERSION, '3.5.0', '>=' ) ) {
				$widgets_manager->register( new $class() );
			} else {
				$widgets_manager->register_widget_type( new $class() );
			}
		}

	}

	/**
	 * Registers Premium Addons Custom Controls.
	 *
	 * @since 4.2.5
	 * @access public
	 *
	 * @return void
	 */
	public function init_pa_controls() {

		if ( self::$modules['premium-equal-height'] || self::$modules['premium-blog'] || self::$modules['pa-display-conditions'] ) {

			$control_manager = \Elementor\Plugin::instance();

			if ( version_compare( ELEMENTOR_VERSION, '3.5.0', '>=' ) ) {

				// TODO: NEEDS TO BE DYNAMIC.
				if ( self::$modules['premium-equal-height'] ) {

					require_once PREMIUM_ADDONS_PATH . 'includes/controls/premium-select.php';
					$premium_select = __NAMESPACE__ . '\Controls\Premium_Select';
					$control_manager->controls_manager->register( new $premium_select() );

				}

				if ( self::$modules['premium-blog'] ) {

					require_once PREMIUM_ADDONS_PATH . 'includes/controls/premium-post-filter.php';
					require_once PREMIUM_ADDONS_PATH . 'includes/controls/premium-tax-filter.php';

					$premium_post_filter = __NAMESPACE__ . '\Controls\Premium_Post_Filter';
					$premium_tax_filter  = __NAMESPACE__ . '\Controls\Premium_Tax_Filter';

					$control_manager->controls_manager->register( new $premium_post_filter() );
					$control_manager->controls_manager->register( new $premium_tax_filter() );

				}

				if ( self::$modules['pa-display-conditions'] ) {

					require_once PREMIUM_ADDONS_PATH . 'includes/controls/premium-acf-selector.php';
					$premium_acf_selector = __NAMESPACE__ . '\Controls\Premium_Acf_Selector';
					$control_manager->controls_manager->register( new $premium_acf_selector() );

				}
			} else {
				if ( self::$modules['premium-equal-height'] ) {

					require_once PREMIUM_ADDONS_PATH . 'includes/controls/premium-select.php';
					$premium_select = __NAMESPACE__ . '\Controls\Premium_Select';
					$control_manager->controls_manager->register_control( $premium_select::TYPE, new $premium_select() );

				}

				if ( self::$modules['premium-blog'] ) {

					require_once PREMIUM_ADDONS_PATH . 'includes/controls/premium-post-filter.php';
					require_once PREMIUM_ADDONS_PATH . 'includes/controls/premium-tax-filter.php';

					$premium_post_filter = __NAMESPACE__ . '\Controls\Premium_Post_Filter';
					$premium_tax_filter  = __NAMESPACE__ . '\Controls\Premium_Tax_Filter';

					$control_manager->controls_manager->register_control( $premium_post_filter::TYPE, new $premium_post_filter() );
					$control_manager->controls_manager->register_control( $premium_tax_filter::TYPE, new $premium_tax_filter() );

				}

				if ( self::$modules['pa-display-conditions'] ) {

					require_once PREMIUM_ADDONS_PATH . 'includes/controls/premium-acf-selector.php';
					$premium_acf_selector = __NAMESPACE__ . '\Controls\Premium_Acf_Selector';
					$control_manager->controls_manager->register_control( $premium_acf_selector::TYPE, new $premium_acf_selector() );

				}
			}
		}

	}

	/**
	 * Load PA Extensions
	 *
	 * @since 4.7.0
	 * @access public
	 */
	public function load_pa_extensions() {

		if ( self::$modules['premium-equal-height'] ) {
			Equal_Height::get_instance();
		}

		if ( self::$modules['pa-display-conditions'] ) {
			require_once PREMIUM_ADDONS_PATH . 'widgets/dep/urlopen.php';
			Display_Conditions::get_instance();
		}

		if ( self::$modules['premium-floating-effects'] ) {
			Floating_Effects::get_instance();
		}

		if ( class_exists( 'woocommerce' ) && self::$modules['woo-products'] ) {
			Woocommerce::get_instance();
		}

	}

	/**
	 *
	 * Creates and returns an instance of the class
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return object
	 */
	public static function get_instance() {

		if ( ! isset( self::$instance ) ) {

			self::$instance = new self();

		}

		return self::$instance;
	}
}
