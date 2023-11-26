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
use PremiumAddons\Modules\PremiumGlobalTooltips\Module as GlobalTooltips;
use PremiumAddons\Modules\PremiumShapeDivider\Module as Shape_Divider;
use PremiumAddons\Modules\PremiumWrapperLink\Module as Wrapper_Link;
use PremiumAddons\Includes\Assets_Manager;
use PremiumAddons\Includes\Premium_Template_Tags;
use ElementorPro\Plugin as PluginPro;

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

		add_action( 'wp_ajax_get_pinterest_token', array( $this, 'get_pinterest_token' ) );
		add_action( 'wp_ajax_get_pinterest_boards', array( $this, 'get_pinterest_boards' ) );
		add_action( 'wp_ajax_insert_cf_form', array( $this, 'insert_cf_form' ) );

		add_action( 'wp_ajax_get_tiktok_token', array( $this, 'get_tiktok_token' ) );

		add_action( 'elementor/editor/before_enqueue_scripts', array( $this, 'enqueue_editor_scripts' ) );

		add_action( 'elementor/preview/enqueue_styles', array( $this, 'enqueue_preview_styles' ) );

		add_action( 'elementor/frontend/after_register_styles', array( $this, 'register_frontend_styles' ) );

		add_action( 'elementor/frontend/after_register_scripts', array( $this, 'register_frontend_scripts' ) );

		add_action( 'wp_ajax_get_elementor_template_content', array( $this, 'get_template_content' ) );

		if ( defined( 'ELEMENTOR_VERSION' ) ) {

			add_action( 'elementor/controls/register', array( $this, 'init_pa_controls' ) );
			add_action( 'elementor/widgets/register', array( $this, 'widgets_area' ) );

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

		$temp_id   = sanitize_text_field( wp_unslash( $_POST['templateID'] ) );
		$temp_type = sanitize_text_field( wp_unslash( $_POST['tempType'] ) );

		if ( 'loop' === $temp_type ) {
			/** @var LoopDocument $document */
			$template_content = PluginPro::elementor()->documents->get( $temp_id );

		} else {
			$template_content = $this->template_instance->get_template_content( $temp_id, true );

		}

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
		$temp_type  = isset( $_POST['type'] ) ? sanitize_text_field( wp_unslash( $_POST['type'] ) ) : false;
		$meta_input = array(
			'_elementor_edit_mode'     => 'builder',
			'_elementor_template_type' => 'page',
			'_wp_page_template'        => 'elementor_canvas',
		);

		if ( 'loop' === $temp_type ) {
			$meta_input = array(
				'_elementor_edit_mode'     => 'builder',
				'_elementor_template_type' => 'loop-item',
			);
		} elseif ( 'grid' === $temp_type ) {
			$meta_input = array(
				'_elementor_edit_mode'     => 'builder',
				'_elementor_template_type' => 'premium-grid',
			);
		}

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
				'meta_input'   => $meta_input,
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

		$modules = array(
			self::$modules['premium-blog'],
			self::$modules['pa-display-conditions'],
			self::$modules['premium-smart-post-listing'],
			self::$modules['premium-post-ticker'],
			self::$modules['premium-notifications'],
			self::$modules['premium-tcloud'],
			self::$modules['premium-pinterest-feed'],
		);

		$localize_settings = in_array( true, $modules, true );

		if ( $localize_settings ) {
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

		$pinterest_enabled = isset( self::$modules['premium-pinterest-feed'] ) ? self::$modules['premium-pinterest-feed'] : 1;
		$tiktok_enabled    = isset( self::$modules['premium-tiktok-feed'] ) ? self::$modules['premium-tiktok-feed'] : 1;

		$cf_enabled = isset( self::$modules['premium-contactform'] ) ? self::$modules['premium-contactform'] : 1;

		if ( $cf_enabled || $pinterest_enabled || $tiktok_enabled ) {

			$data = array(
				'ajaxurl' => esc_url( admin_url( 'admin-ajax.php' ) ),
				'nonce'   => wp_create_nonce( 'pa-editor' ),
			);

			wp_enqueue_script(
				'pa-editor-handler',
				PREMIUM_ADDONS_URL . 'assets/editor/js/editor-handler.js',
				array( 'elementor-editor' ),
				PREMIUM_ADDONS_VERSION,
				true
			);

			wp_localize_script( 'pa-editor-handler', 'paEditorSettings', $data );

		}

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
			'pa-flipster',
			PREMIUM_ADDONS_URL . 'assets/frontend/' . $dir . '/flipster' . $suffix . '.css',
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

		wp_register_style(
			'pa-world-clock',
			PREMIUM_ADDONS_URL . 'assets/frontend/' . $dir . '/premium-world-clock' . $suffix . '.css',
			array(),
			PREMIUM_ADDONS_VERSION,
			'all'
		);

		wp_register_style(
			'tooltipster',
			PREMIUM_ADDONS_URL . 'assets/frontend/' . $dir . '/tooltipster.min.css',
			array(),
			PREMIUM_ADDONS_VERSION,
			'all'
		);

		wp_register_style(
			'pa-gTooltips',
			PREMIUM_ADDONS_URL . 'assets/frontend/' . $dir . '/premium-global-tooltips' . $suffix . '.css',
			array(),
			PREMIUM_ADDONS_VERSION,
			'all'
		);

        wp_register_style(
			'pa-shape-divider',
			PREMIUM_ADDONS_URL . 'assets/frontend/' . $dir . '/premium-shape-divider' . $suffix . '.css',
			array(),
			PREMIUM_ADDONS_VERSION,
			'all'
		);

		$assets_gen_enabled = self::$modules['premium-assets-generator'] ? true : false;

		$type = get_post_type();

		// If dynamic assets is disabled.
		if ( ! $assets_gen_enabled || ( 'page' !== $type && 'post' !== $type ) ) {
			$this->enqueue_old_styles( $dir, $is_rtl, $suffix );
		} else {

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

			$pa_elements = get_option( 'pa_elements_' . Assets_Manager::$post_id, array() );

			// If the assets are not updated, or they are updated but the dynamic CSS file has not been loaded for any reason.
			if ( ! Assets_Manager::$is_updated || ( ! empty( $pa_elements ) && ! wp_style_is( 'pa-frontend', 'enqueued' ) ) ) {
				$this->enqueue_old_styles( $dir, $is_rtl, $suffix );
			}
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
	public function enqueue_old_styles( $directory, $is_rtl, $suffix ) {

		wp_enqueue_style(
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

		wp_register_script( 'tiktok-embed', 'https://www.tiktok.com/embed.js', array(), false, true );

		wp_register_script(
			'prettyPhoto-js',
			PREMIUM_ADDONS_URL . 'assets/frontend/' . $dir . '/prettyPhoto' . $suffix . '.js',
			array( 'jquery' ),
			PREMIUM_ADDONS_VERSION,
			true
		);

		wp_register_script(
			'tooltipster-bundle',
			PREMIUM_ADDONS_URL . 'assets/frontend/' . $dir . '/tooltipster' . $suffix . '.js',
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
			'pa-flipster',
			PREMIUM_ADDONS_URL . 'assets/frontend/' . $dir . '/flipster' . $suffix . '.js',
			array( 'jquery' ),
			PREMIUM_ADDONS_VERSION
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

		wp_register_script(
			'pa-gTooltips',
			PREMIUM_ADDONS_URL . 'assets/frontend/' . $dir . '/premium-global-tooltips' . $suffix . '.js',
			array( 'jquery' ),
			PREMIUM_ADDONS_VERSION,
			true
		);

		wp_register_script(
			'pa-shape-divider',
			PREMIUM_ADDONS_URL . 'assets/frontend/' . $dir . '/premium-shape-divider' . $suffix . '.js',
			array( 'jquery' ),
			PREMIUM_ADDONS_VERSION,
			true
		);

		wp_localize_script(
			'pa-gTooltips',
			'PremiumSettings',
			array(
				'ajaxurl' => esc_url( admin_url( 'admin-ajax.php' ) ),
				'nonce'   => wp_create_nonce( 'pa-blog-widget-nonce' ),
			)
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
			PREMIUM_ADDONS_URL . 'assets/frontend/min-js/fontawesome-all.min.js',
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

		wp_register_script(
			'pa-notifications',
			PREMIUM_ADDONS_URL . 'assets/frontend/' . $dir . '/premium-notifications' . $suffix . '.js',
			array( 'jquery' ),
			PREMIUM_ADDONS_VERSION,
			true
		);

		wp_register_script(
			'pa-luxon',
			PREMIUM_ADDONS_URL . 'assets/frontend/' . $dir . '/luxon' . $suffix . '.js',
			array( 'jquery' ),
			PREMIUM_ADDONS_VERSION,
			true
		);

		wp_register_script(
			'mousewheel-js',
			PREMIUM_ADDONS_URL . 'assets/frontend/' . $dir . '/jquery-mousewheel' . $suffix . '.js',
			array( 'jquery' ),
			PREMIUM_ADDONS_VERSION,
			true
		);

		wp_register_script(
			'pa-wrapper-link',
			PREMIUM_ADDONS_URL . 'assets/frontend/' . $dir . '/premium-wrapper-link' . $suffix . '.js',
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

		$custom_css = '
		.e-preview--show-hidden-elements[data-elementor-device-mode="mobile"] .elementor-edit-area-active .elementor-hidden-mobile.premium-addons-element {
			display: none;
		}

		.e-preview--show-hidden-elements[data-elementor-device-mode="tablet"] .elementor-edit-area-active .elementor-hidden-tablet.premium-addons-element {
			display: none;
		}

		.e-preview--show-hidden-elements[data-elementor-device-mode="mobile_extra"] .elementor-edit-area-active .elementor-hidden-mobile_extra.premium-addons-element {
			display: none;
		}

		.e-preview--show-hidden-elements[data-elementor-device-mode="tablet_extra"] .elementor-edit-area-active .elementor-hidden-tablet_extra.premium-addons-element {
			display: none;
		}

		.e-preview--show-hidden-elements[data-elementor-device-mode="widescreen"] .elementor-edit-area-active .elementor-hidden-widescreen.premium-addons-element {
			display: none;
		}

		.e-preview--show-hidden-elements[data-elementor-device-mode="desktop"] .elementor-edit-area-active .elementor-hidden-desktop.premium-addons-element {
			display: none;
		}';

		wp_enqueue_style( 'pa-prettyphoto' );

		wp_enqueue_style( 'premium-addons' );

		wp_add_inline_style( 'premium-addons', $custom_css );

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
	 * Get Pinterest account token for Pinterest Feed widget
	 *
	 * @since 4.10.2
	 * @access public
	 *
	 * @return void
	 */
	public function get_pinterest_token() {

		check_ajax_referer( 'pa-editor', 'security' );

		$api_url = 'https://appfb.premiumaddons.com/wp-json/fbapp/v2/pinterest';

		$response = wp_remote_get(
			$api_url,
			array(
				'timeout'   => 60,
				'sslverify' => false,
			)
		);

		$body = wp_remote_retrieve_body( $response );
		$body = json_decode( $body, true );

		// $transient_name = 'pa_pinterest_token_' . $body;

		// $expire_time = 29 * DAY_IN_SECONDS;

		// set_transient( $transient_name, true, $expire_time );

		wp_send_json_success( $body );

	}

	/**
	 * Get Pinterest account token for Pinterest Feed widget
	 *
	 * @since 4.10.2
	 * @access public
	 *
	 * @return void
	 */
	public function get_pinterest_boards() {

		check_ajax_referer( 'pa-blog-widget-nonce', 'nonce' );

		if ( ! isset( $_GET['token'] ) ) {
			wp_send_json_error();
		}

		$token = sanitize_text_field( wp_unslash( $_GET['token'] ) );

		$transient_name = 'pa_pinterest_boards_' . substr( $token, 0, 15 );

		$body = get_transient( $transient_name );

		if ( false === $body ) {

			$api_url = 'https://api.pinterest.com/v5/boards?page_size=60';

			$response = wp_remote_get(
				$api_url,
				array(
					'headers' => array(
						'Authorization' => 'Bearer ' . $token,
					),
				)
			);

			$body = wp_remote_retrieve_body( $response );
			$body = json_decode( $body, true );

			set_transient( $transient_name, $body, 30 * MINUTE_IN_SECONDS );

		}

		$boards = array();

		foreach ( $body['items'] as $index => $board ) {
			$boards[ $board['id'] ] = $board['name'];
		}

		wp_send_json_success( wp_json_encode( $boards ) );

	}

	/**
	 * Get Pinterest account token for Pinterest Feed widget
	 *
	 * @since 4.10.2
	 * @access public
	 *
	 * @return void
	 */
	public function get_tiktok_token() {

		check_ajax_referer( 'pa-editor', 'security' );

		$api_url = 'https://appfb.premiumaddons.com/wp-json/fbapp/v2/tiktok';

		$response = wp_remote_get(
			$api_url,
			array(
				'timeout'   => 60,
				'sslverify' => false,
			)
		);

		$body = wp_remote_retrieve_body( $response );
		$body = json_decode( $body, true );

		// $transient_name = 'pa_tiktok_token_' . $body;

		// $expire_time = 29 * DAY_IN_SECONDS;

		// set_transient( $transient_name, true, $expire_time );

		wp_send_json_success( $body );

	}

	/**
	 * Insert Contact Form 7 Form
	 *
	 * @since 4.10.2
	 * @access public
	 *
	 * @return void
	 */
	public function insert_cf_form() {

		check_ajax_referer( 'pa-editor', 'security' );

		if ( ! isset( $_GET['preset'] ) ) {
			wp_send_json_error();
		}

		$preset = sanitize_text_field( wp_unslash( $_GET['preset'] ) );

		$current_user = wp_get_current_user();

		$props = array(
			'form'                => Helper_Functions::get_cf_form_body( $preset ),
			'mail'                => array(
				'active'             => 1,
				'subject'            => '[_site_title] "[your-subject]"',
				'sender'             => '[_site_title]',
				'recipient'          => '[_site_admin_email]',
				'body'               => 'From: [your-name] [your-email]' . PHP_EOL .
						'Subject: [your-subject]' . PHP_EOL . PHP_EOL .
						'Message Body:' . PHP_EOL . '[your-message]' . PHP_EOL . PHP_EOL .
						'--' . PHP_EOL .
						'This e-mail was sent from a contact form on [_site_title] ([_site_url])',
				'additional_headers' => 'Reply-To: [your-email]',
				'attachments'        => '',
				'use_html'           => '',
				'exclude_blank'      => '',
			),
			'mail_2'              => array(
				'active'             => '',
				'subject'            => '[_site_title] "[your-subject]"',
				'sender'             => '[_site_title]',
				'recipient'          => '[your-email]',
				'body'               => 'Message Body:' . PHP_EOL . '[your-message]' . PHP_EOL . PHP_EOL .
						'--' . PHP_EOL .
						'This e-mail was sent from a contact form on [_site_title] ([_site_url])',
				'additional_headers' => 'Reply-To: [_site_admin_email]',
				'attachments'        => '',
				'use_html'           => '',
				'exclude_blank'      => '',
			),
			'messages'            => array(
				'mail_sent_ok'             => 'Thank you for your message. It has been sent.',
				'mail_sent_ng'             => 'There was an error trying to send your message. Please try again later.',
				'validation_error'         => 'One or more fields have an error. Please check and try again.',
				'spam'                     => 'There was an error trying to send your message. Please try again later.',
				'accept_terms'             => 'You must accept the terms and conditions before sending your message.',
				'invalid_required'         => 'Please fill out this field.',
				'invalid_too_long'         => 'This field has a too long input.',
				'invalid_too_short'        => 'This field has a too short input.',
				'upload_failed'            => 'There was an unknown error uploading the file.',
				'upload_file_type_invalid' => 'You are not allowed to upload files of this type.',
				'upload_file_too_large'    => 'The uploaded file is too large.',
				'upload_failed_php_error'  => 'There was an error uploading the file.',
				'invalid_date'             => 'Please enter a date in YYYY-MM-DD format.',
				'date_too_early'           => 'This field has a too early date.',
				'date_too_late'            => 'This field has a too late date.',
				'invalid_number'           => 'Please enter a number.',
				'number_too_small'         => 'This field has a too small number.',
				'number_too_large'         => 'This field has a too large number.',
				'quiz_answer_not_correct'  => 'The answer to the quiz is incorrect.',
				'invalid_email'            => 'Please enter an email address.',
				'invalid_url'              => 'Please enter a URL.',
				'invalid_tel'              => 'Please enter a telephone number.',
			),
			'additional_settings' => '',
		);

		$post_content = implode( "\n", wpcf7_array_flatten( $props ) );

		$args = array(
			'post_status'  => 'publish',
			'post_type'    => 'wpcf7_contact_form',
			'post_content' => $post_content,
			'post_author'  => $current_user->ID,
			'post_title'   => sprintf(
				__( 'Form | %s', 'premium-addons-for-elementor' ),
				date( 'Y-m-d H:i' )
			),
		);

		$post_id = wp_insert_post( $args );

		foreach ( $props as $prop => $value ) {
			update_post_meta(
				$post_id,
				'_' . $prop,
				wpcf7_normalize_newline_deep( $value )
			);
		}

		$form_id = wpcf7_generate_contact_form_hash( $post_id );

		add_post_meta( $post_id, '_hash', $form_id, true );

		wp_send_json_success( substr( $form_id, 0, 7 ) );

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
			wp_send_json_error( 'Empty Template ID' );
		}

		$template_content = $this->template_instance->get_template_content( $template );

		if ( empty( $template_content ) || ! isset( $template_content ) ) {
			wp_send_json_error( 'Empty Content' );
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

		if ( 'PremiumAddons\Widgets\Premium_Videobox' === $class || 'PremiumAddons\Widgets\Premium_Weather' === $class ) {
			require_once PREMIUM_ADDONS_PATH . 'widgets/dep/urlopen.php';
		}

		if ( 'PremiumAddons\Widgets\Premium_Weather' === $class ) {
			require_once PREMIUM_ADDONS_PATH . 'widgets/dep/pa-weather-handler.php';
		}

		if ( in_array( $class, array( 'PremiumAddons\Widgets\Premium_Pinterest_Feed', 'PremiumAddons\Widgets\Premium_Tiktok_Feed' ), true ) ) {
			require_once PREMIUM_ADDONS_PATH . 'includes/pa-display-conditions/mobile-detector.php';

			if ( 'PremiumAddons\Widgets\Premium_Pinterest_Feed' == $class ) {
				require_once PREMIUM_ADDONS_PATH . 'widgets/dep/pa-pins-handler.php';
			}

			if ( 'PremiumAddons\Widgets\Premium_Tiktok_Feed' == $class ) {
				require_once PREMIUM_ADDONS_PATH . 'widgets/dep/pa-tiktok-handler.php';
			}
		}

		if ( class_exists( $class, false ) ) {

			$widgets_manager->register( new $class() );

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

		/**
		 * List of Modules that need a custom control.
		 *
		 * @var array
		 */
		$modules = array(
			self::$modules['premium-blog'],
			self::$modules['premium-equal-height'],
			self::$modules['pa-display-conditions'],
			self::$modules['premium-smart-post-listing'],
			self::$modules['premium-post-ticker'],
			self::$modules['premium-tcloud'],
			self::$modules['premium-notifications'],
			self::$modules['premium-pinterest-feed'],
			self::$modules['premium-contactform'],
		);

		$blog_modules = array(
			self::$modules['premium-blog'],
			self::$modules['premium-smart-post-listing'],
			self::$modules['premium-post-ticker'],
			self::$modules['premium-notifications'],
		);

		$load_controls = in_array( true, $modules, true );

		$load_blog_controls = in_array( true, $blog_modules, true );

		$control_manager = \Elementor\Plugin::instance();

		if ( $load_controls ) {

			if ( self::$modules['premium-equal-height'] || self::$modules['premium-pinterest-feed'] ) {

				require_once PREMIUM_ADDONS_PATH . 'includes/controls/premium-select.php';
				$premium_select = __NAMESPACE__ . '\Controls\Premium_Select';
				$control_manager->controls_manager->register( new $premium_select() );

			}

			if ( $load_blog_controls ) {

				require_once PREMIUM_ADDONS_PATH . 'includes/controls/premium-post-filter.php';

				$premium_post_filter = __NAMESPACE__ . '\Controls\Premium_Post_Filter';

				$control_manager->controls_manager->register( new $premium_post_filter() );
			}

			if ( self::$modules['premium-blog'] || self::$modules['premium-smart-post-listing'] || self::$modules['premium-tcloud'] ) {

				require_once PREMIUM_ADDONS_PATH . 'includes/controls/premium-tax-filter.php';

				$premium_tax_filter = __NAMESPACE__ . '\Controls\Premium_Tax_Filter';

				$control_manager->controls_manager->register( new $premium_tax_filter() );
			}

			if ( self::$modules['pa-display-conditions'] ) {

				require_once PREMIUM_ADDONS_PATH . 'includes/controls/premium-acf-selector.php';
				$premium_acf_selector = __NAMESPACE__ . '\Controls\Premium_Acf_Selector';
				$control_manager->controls_manager->register( new $premium_acf_selector() );

			}
		}

		if ( self::$modules['premium-contactform'] || self::$modules['premium-shape-divider'] ) {

			require_once PREMIUM_ADDONS_PATH . 'includes/controls/pa-image-choose.php';
			$premium_image_choose = __NAMESPACE__ . '\Controls\Premium_Image_Choose';
			$control_manager->controls_manager->register( new $premium_image_choose() );

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

		if ( self::$modules['premium-global-tooltips'] ) {
			GlobalTooltips::get_instance();
		}

        if ( self::$modules['premium-shape-divider'] ) {
			Shape_Divider::get_instance();
		}

		if ( self::$modules['premium-wrapper-link'] ) {
			Wrapper_Link::get_instance();
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
