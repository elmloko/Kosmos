<?php
/**
 * Class: Module
 * Name : Premium Display Conditions
 * Slug : pa-display-conditions
 */

namespace PremiumAddons\Modules\PA_Display_Conditions;

// PremiumAddons Classes.
use PremiumAddons\Includes\Helper_Functions;
use PremiumAddons\Includes\PA_Controls_Handler;

// Elementor Classes.
use Elementor\Repeater;
use Elementor\Controls_Manager;

// Includes.
require_once PREMIUM_ADDONS_PATH . 'includes/pa-display-conditions/pa-controls-handler.php';

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class Module For Premium Display Conditions addon.
 */
class Module {

	/**
	 * Load Script
	 *
	 * @var $load_script
	 */
	private static $load_script = null;

	/**
	 * Class object
	 *
	 * @var instance
	 */
	private static $instance = null;

	/**
	 * Display conditions.
	 *
	 * Class Constructor Funcion.
	 */
	public function __construct() {

		// Enqueue the required JS file.
		add_action( 'elementor/preview/enqueue_scripts', array( $this, 'enqueue_scripts' ) );

		add_action( 'elementor/element/section/section_advanced/after_section_end', array( $this, 'register_controls' ), 10 );
		add_action( 'elementor/element/column/section_advanced/after_section_end', array( $this, 'register_controls' ), 10 );
		add_action( 'elementor/element/common/_section_style/after_section_end', array( $this, 'register_controls' ), 10 );

		add_action( 'elementor/frontend/before_render', array( $this, 'check_script_enqueue' ) );

		if ( Helper_Functions::check_elementor_experiment( 'container' ) ) {
			add_action( 'elementor/element/container/section_layout/after_section_end', array( $this, 'register_controls' ), 10 );
		}

	}

	/**
	 * Enqueue scripts.
	 *
	 * Registers required dependencies for the extension and enqueues them.
	 *
	 * @since 4.9.21
	 * @access public
	 */
	public function enqueue_scripts() {

		if ( ! wp_script_is( 'pa-dis-conditions', 'enqueued' ) ) {
			wp_enqueue_script( 'pa-dis-conditions' );
		}

	}

	/**
	 * Register PA Display Conditions controls.
	 *
	 * @access public
	 * @param object $element for current element.
	 */
	public function register_controls( $element ) {

		$element->start_controls_section(
			'section_pa_display_conditions',
			array(
				'label' => sprintf( '<i class="pa-extension-icon pa-dash-icon"></i> %s', __( 'Display Conditions', 'premium-addons-for-elementor' ) ),
				'tab'   => Controls_Manager::TAB_ADVANCED,
			)
		);

		$controls_obj = new PA_Controls_Handler();

		$options = $controls_obj::$conditions;

		if ( class_exists( 'ACF' ) ) {
			$options = array_merge(
				array(
					'acf' => array(
						'label'   => __( 'ACF (PRO)', 'premium-addons-for-elementor' ),
						'options' => array(
							'acf_choice'  => __( 'Choice', 'premium-addons-for-elementor' ),
							'acf_text'    => __( 'Text', 'premium-addons-for-elementor' ),
							'acf_boolean' => __( 'True/False', 'premium-addons-for-elementor' ),
						),
					),
				),
				$options
			);
		}

		if ( class_exists( 'woocommerce' ) ) {
			$options = array_merge(
				$options,
				array(
					'woocommerce' => array(
						'label'   => __( 'WooCommerce (PRO)', 'premium-addons-for-elementor' ),
						'options' => array(
							'woo_cat_page'      => __( 'Current Category Page', 'premium-addons-for-elementor' ),
							'woo_product_cat'   => __( 'Current Product Category', 'premium-addons-for-elementor' ),
							'woo_product_price' => __( 'Current Product Price', 'premium-addons-for-elementor' ),
							'woo_product_stock' => __( 'Current Product Stock', 'premium-addons-for-elementor' ),
							'woo_orders'        => __( 'Purchased/In Cart Orders', 'premium-addons-for-elementor' ),
							'woo_category'      => __( 'Purchased/In Cart Categories', 'premium-addons-for-elementor' ),
							'woo_last_purchase' => __( 'Last Purchase In Cart', 'premium-addons-for-elementor' ),
							'woo_total_price'   => __( 'Amount In Cart', 'premium-addons-for-elementor' ),
							'woo_cart_products' => __( 'Products In Cart', 'premium-addons-for-elementor' ),
							'woo_purchase_products' => __( 'Purchased Products', 'premium-addons-for-elementor' ),
						),
					),
				)
			);
		}

		$options = apply_filters( 'pa_display_conditions', $options );

		$element->add_control(
			'pa_display_conditions_switcher',
			array(
				'label'              => __( 'Enable Display Conditions', 'premium-addons-for-elementor' ),
				'type'               => Controls_Manager::SWITCHER,
				'return_value'       => 'yes',
				'render_type'        => 'template',
				'prefix_class'       => 'pa-display-conditions-',
				'frontend_available' => true,
			)
		);

		$element->add_control(
			'pa_display_action',
			array(
				'label'     => __( 'Action', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'show',
				'options'   => array(
					'show' => __( 'Show Element', 'premium-addons-for-elementor' ),
					'hide' => __( 'Hide Element', 'premium-addons-for-elementor' ),
				),
				'condition' => array(
					'pa_display_conditions_switcher' => 'yes',
				),
			)
		);

		$element->add_control(
			'pa_display_when',
			array(
				'label'     => __( 'Display When', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'any',
				'options'   => array(
					'all' => __( 'All Conditions Are Met', 'premium-addons-for-elementor' ),
					'any' => __( 'Any Condition is Met', 'premium-addons-for-elementor' ),
				),
				'condition' => array(
					'pa_display_conditions_switcher' => 'yes',
				),
			)
		);

		$repeater = new Repeater();

		$repeater->add_control(
			'pa_condition_key',
			array(
				'label'       => __( 'Type', 'premium-addons-for-elementor' ),
				'type'        => Controls_Manager::SELECT,
				'groups'      => $options,
				'default'     => 'browser',
				'label_block' => true,
			)
		);

		$options_conditions = apply_filters(
			'pa_pro_display_conditions',
			array(
				'url_string',
				'url_referer',
				'shortcode',
				'woo_orders',
				'woo_cat_page',
				'woo_category',
				'woo_product_price',
				'woo_product_stock',
				'woo_product_cat',
				'woo_last_purchase',
				'woo_total_price',
				'woo_purchase_products',
				'woo_cart_products',
				'acf_choice',
				'acf_text',
				'acf_boolean',
			)
		);

		$get_pro = Helper_Functions::get_campaign_link( 'https://premiumaddons.com/pro', 'editor-page', 'wp-editor', 'get-pro' );

		$repeater->add_control(
			'display_conditions_notice',
			array(
				'type'            => Controls_Manager::RAW_HTML,
				'raw'             => __( 'This option is available in Premium Addons Pro.', 'premium-addons-for-elementor' ) . '<a href="' . esc_url( $get_pro ) . '" target="_blank">' . __( 'Upgrade now!', 'premium-addons-for-elementor' ) . '</a>',
				'content_classes' => 'papro-upgrade-notice',
				'condition'       => array(
					'pa_condition_key' => $options_conditions,
				),
			)
		);

		$controls_obj->add_repeater_source_controls( $repeater );

		$repeater->add_control(
			'pa_condition_operator',
			array(
				'type'        => Controls_Manager::SELECT,
				'default'     => 'is',
				'label_block' => true,
				'options'     => array(
					'is'  => __( 'Is', 'premium-addons-for-elementor' ),
					'not' => __( 'Is Not', 'premium-addons-for-elementor' ),
				),
				'condition'   => array(
					'pa_condition_key!' => $options_conditions,
				),
			)
		);

		$controls_obj->add_repeater_compare_controls( $repeater );

		$repeater->add_control(
			'pa_condition_timezone',
			array(
				'label'       => 'Timezone',
				'type'        => Controls_Manager::SELECT,
				'default'     => 'server',
				'label_block' => true,
				'options'     => array(
					'local'  => __( 'Local Time', 'premium-addons-for-elementor' ),
					'server' => __( 'Server Timezone', 'premium-addons-for-elementor' ),
				),
				'condition'   => array(
					'pa_condition_key' => array( 'date_range', 'time_range', 'date', 'day' ),
				),
			)
		);

		$repeater->add_control(
			'pa_condition_loc_method',
			array(
				'label'       => __( 'Location Detect Method', 'premium-addons-for-elementor' ),
				'type'        => Controls_Manager::SELECT,
				'default'     => 'old',
				'label_block' => true,
				'options'     => array(
					'old' => __( 'Old', 'premium-addons-for-elementor' ),
					'new' => __( 'New', 'premium-addons-for-elementor' ),
				),
				'condition'   => array(
					'pa_condition_key' => 'ip_location',
				),
			)
		);

		$should_apply = apply_filters( 'pa_display_conditions_values', true );

		$values = $repeater->get_controls();

		if ( $should_apply ) {
			// $values = array_values( $values );
		}

		$element->add_control(
			'pa_condition_repeater',
			array(
				'label'         => __( 'Conditions', 'premium-addons-for-elementor' ),
				'type'          => Controls_Manager::REPEATER,
				'label_block'   => true,
				'fields'        => $values,
				'title_field'   => '<# print( pa_condition_key.replace(/_/g, " ").split(" ").map((s) => s.charAt(0).toUpperCase() + s.substring(1)).join(" ")) #>',
				'prevent_empty' => false,
				'condition'     => array(
					'pa_display_conditions_switcher' => 'yes',
				),
			)
		);

		$this->add_helpful_information( $element );

		$element->end_controls_section();
	}

	/**
	 * Add Helpful Information
	 *
	 * @since 4.9.39
	 * @access private
	 * @param object $element for current element.
	 */
	private function add_helpful_information( $element ) {

		$element->add_control(
			'pa_condition_info',
			array(
				'label'     => __( 'Helpful Information', 'premium-addons-for-elementor' ),
				'separator' => 'before',
				'type'      => Controls_Manager::HEADING,
				'condition' => array(
					'pa_display_conditions_switcher' => 'yes',
				),
			)
		);

		$docs = array(
			'https://premiumaddons.com/docs/elementor-display-conditions-tutorial/' => __( 'Getting started »', 'premium-addons-for-elementor' ),
			'https://premiumaddons.com/docs/elementor-editor-not-loading-with-display-conditions/' => __( 'Fix editor not loading with Display Conditions enabled »', 'premium-addons-for-elementor' ),
            'https://premiumaddons.com/docs/how-to-show-hide-element-based-on-browser-elementor-display-conditions/' => __( 'Show/Hide Element Based on Browser »', 'premium-addons-for-elementor' ),
            'https://premiumaddons.com/docs/how-to-show-hide-element-on-specific-time-range-elementor-display-conditions/' => __( 'Show/Hide Element Based on Time Range »', 'premium-addons-for-elementor' ),
            'https://premiumaddons.com/docs/how-to-show-hide-element-with-location-elementor-display-conditions/' => __( 'Show/Hide Element Based on Location »', 'premium-addons-for-elementor' ),
		);

		$doc_index = 1;
		foreach ( $docs as $url => $title ) {

			$doc_url = Helper_Functions::get_campaign_link( $url, 'editor-page', 'wp-editor', 'get-support' );

			$element->add_control(
				'pa_condition_doc_' . $doc_index,
				array(
					'type'            => Controls_Manager::RAW_HTML,
					'raw'             => sprintf( '<a href="%s" target="_blank">%s</a>', $doc_url, $title ),
					'content_classes' => 'editor-pa-doc',
					'condition'       => array(
						'pa_display_conditions_switcher' => 'yes',
					),
				)
			);

			$doc_index++;

		}

	}

	/**
	 * Check Script Enqueue
	 *
	 * Check if the script files should be loaded.
	 *
	 * @since 4.9.21
	 * @access public
	 *
	 * @param object $element for current element.
	 */
	public function check_script_enqueue( $element ) {

		if ( self::$load_script ) {
			return;
		}

		if ( 'yes' === $element->get_settings_for_display( 'pa_display_conditions_switcher' ) ) {
			$this->enqueue_scripts();

			self::$load_script = true;

			remove_action( 'elementor/frontend/before_render', array( $this, 'check_script_enqueue' ) );
		}

	}

	/**
	 * Returns an instance of this class.
	 *
	 * @access public
	 */
	public static function get_instance() {

		if ( ! isset( self::$instance ) ) {
			self::$instance = new self();
		}

		return self::$instance;
	}
}
