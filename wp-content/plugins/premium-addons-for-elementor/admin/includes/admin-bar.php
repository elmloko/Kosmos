<?php
/**
 * PA Admin Bar
 */

namespace PremiumAddons\Admin\Includes;

use PremiumAddons\Includes\Helper_Functions;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class Admin_Bar
 */
class Admin_Bar {

	/**
	 * Class instance
	 *
	 * @var instance
	 */
	private static $instance = null;

	/**
	 * Constructor for the class
	 */
	public function __construct() {

		add_action( 'admin_bar_menu', array( $this, 'add_toolbar_items' ), 500 );

		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_assets' ) );

		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_assets' ) );

	}

	public function enqueue_assets() {

		$suffix = is_rtl() ? '-rtl' : '';

		$action = isset( $_SERVER['REQUEST_URI'] ) ? sanitize_text_field( wp_unslash( $_SERVER['REQUEST_URI'] ) ) : '';

		if ( false === strpos( $action, 'action=architect' ) ) {

			wp_enqueue_style(
				'pa-admin',
				PREMIUM_ADDONS_URL . 'admin/assets/css/admin' . $suffix . '.css',
				array(),
				PREMIUM_ADDONS_VERSION,
				'all'
			);

		}

		wp_enqueue_script(
			'pa-admin-bar',
			PREMIUM_ADDONS_URL . 'admin/assets/js/admin-bar.js',
			array( 'jquery' ),
			PREMIUM_ADDONS_VERSION,
			true
		);

		wp_localize_script(
			'pa-admin-bar',
			'PaDynamicAssets',
			array(
				'nonce'   => wp_create_nonce( 'pa-generate-nonce' ),
				'post_id' => get_queried_object_id(),
				'ajaxurl' => admin_url( 'admin-ajax.php' ),
			)
		);
	}

	public function add_toolbar_items( \WP_Admin_Bar $admin_bar ) {

		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}

		$icon = '<i class="dashicons dashicons-update-alt"></i> ';

		$admin_bar->add_menu(
			array(
				'id'    => 'premium-addons',
				'title' => $icon . __( ' PA Assets', 'premium-addons-for-elementor' ),
				'href'  => $this->get_dashboard_widgets_link(),
				'meta'  => array(
					'title' => __( 'Premium Addons', 'premium-addons-for-elementor' ),
				),
			)
		);

		if ( is_singular() ) {
			$admin_bar->add_menu(
				array(
					'id'     => 'pa-clear-page-cache',
					'parent' => 'premium-addons',
					'title'  => $icon . __( 'Clear Page Generated Assets', 'premium-addons-for-elementor' ),
					'href'   => '#',
					'meta'   => array(
						'class' => 'pa-clear-cache pa-clear-page-cache',
					),
				)
			);
		}

		$admin_bar->add_menu(
			array(
				'id'     => 'pa-clear-all-cache',
				'parent' => 'premium-addons',
				'title'  => $icon . __( 'Clear All Generated Assets', 'premium-addons-for-elementor' ),
				'href'   => '#',
				'meta'   => array(
					'class' => 'pa-clear-cache pa-clear-all-cache',
				),
			)
		);

		$doc_icon = '<i class="dashicons dashicons-editor-help"></i> ';

		$admin_bar->add_menu(
			array(
				'id'     => 'pa-feature-doc',
				'parent' => 'premium-addons',
				'title'  => $doc_icon . __( 'Learn More', 'premium-addons-for-elementor' ),
				'href'   => 'https://premiumaddons.com/docs/dynamic-assets-generate-loading-for-elementor/',
				'meta'   => array(
					'target' => '_blank',
				),
			)
		);
	}

	/**
	 * Get Dashboard Widgets Link
	 *
	 * Returns links for Widgets & Addons dashboard tab.
	 *
	 * @since 4.9.4
	 * @access private
	 *
	 * @return string tab link.
	 */
	private function get_dashboard_widgets_link() {

		return add_query_arg(
			array(
				'page' => 'premium-addons#tab=elements',
			),
			esc_url( admin_url( 'admin.php' ) )
		);

	}

	/**
	 * Creates and returns an instance of the class
	 *
	 * @since 3.20.9
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
