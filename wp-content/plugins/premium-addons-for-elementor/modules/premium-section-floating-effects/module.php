<?php
/**
 * Class: Module
 * Name: Floating Effects
 * Slug: premium-floating-effects
 */

namespace PremiumAddons\Modules\PremiumSectionFloatingEffects;

// PremiumAddons Classes.
use PremiumAddons\Admin\Includes\Admin_Helper;
use PremiumAddons\Includes\Helper_Functions;

// Elementor Classes.
use Elementor\Repeater;
use Elementor\Controls_Manager;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class Module For Premium Floating Effects Addon.
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
	 * Class Constructor Function
	 */
	public function __construct() {

		$modules = Admin_Helper::get_enabled_elements();

		// Checks if Floating Effects is enabled.
		$floating_effect = $modules['premium-floating-effects'];

		if ( ! $floating_effect ) {
			return;
		}

		// Enqueue the required JS file.
		add_action( 'elementor/preview/enqueue_scripts', array( $this, 'enqueue_scripts' ) );

		// Creates Premium Floating Effects tab at the end of advanced tab.
		add_action( 'elementor/element/common/_section_style/after_section_end', array( $this, 'register_controls' ), 10 );

		// Insert data before widget rendering.
		add_action( 'elementor/frontend/widget/before_render', array( $this, 'before_render' ) );

		// Check if scripts should be loaded.
		add_action( 'elementor/frontend/widget/before_render', array( $this, 'check_script_enqueue' ) );

	}

	/**
	 * Enqueue scripts.
	 *
	 * Registers required dependencies for the extension and enqueues them.
	 *
	 * @since 1.6.5
	 * @access public
	 */
	public function enqueue_scripts() {

		if ( ! wp_script_is( 'elementor-waypoints', 'enqueued' ) ) {
			wp_enqueue_script( 'elementor-waypoints' );
		}

		if ( ! wp_script_is( 'pa-anime', 'enqueued' ) ) {
			wp_enqueue_script( 'pa-anime' );
		}

		if ( ! wp_script_is( 'pa-feffects', 'enqueued' ) ) {
			wp_enqueue_script( 'pa-feffects' );
		}

	}


	/**
	 * Register Floating Effects controls.
	 *
	 * @since 1.0.0
	 * @access public
	 * @param object $element for current element.
	 */
	public function register_controls( $element ) {

		$element->start_controls_section(
			'section_premium_fe',
			array(
				'label' => sprintf( '<i class="pa-extension-icon pa-dash-icon"></i> %s', __( 'Floating Effects', 'premium-addons-for-elementor' ) ),
				'tab'   => Controls_Manager::TAB_ADVANCED,
			)
		);

		$element->add_control(
			'premium_fe_switcher',
			array(
				'label'        => __( 'Enable Floating Effects', 'premium-addons-for-elementor' ),
				'type'         => Controls_Manager::SWITCHER,
				'prefix_class' => 'premium-floating-effects-',
				'render_type'  => 'template',
			)
		);

		$doc_link = Helper_Functions::get_campaign_link( 'https://premiumaddons.com/docs/elementor-floating-effects-tutorial/', 'editor-page', 'wp-editor', 'get-support' );

		$element->add_control(
			'pa_floating_effects_notice',
			array(
				'type'            => Controls_Manager::RAW_HTML,
				'raw'             => '<a href="' . esc_url( $doc_link ) . '" target="_blank">' . __( 'How to use Premium Floating Effects for Elementor »', 'premium-addons-for-elementor' ) . '</a>',
				'content_classes' => 'elementor-panel-alert elementor-panel-alert-info',
				'condition'       => array(
					'premium_fe_switcher' => 'yes',
				),
			)
		);

		$element->add_control(
			'premium_fe_target',
			array(
				'label'       => __( 'Custom CSS Selector', 'premium-addons-for-elementor' ),
				'type'        => Controls_Manager::TEXT,
				'description' => __( 'Set this option if you want to apply floating effects on specfic selector inside your widget. For example, .premium-dual-header-container', 'premium-addons-for-elementor' ),
				'dynamic'     => array( 'active' => true ),
				'label_block' => true,
				'render_type' => 'template',
				'condition'   => array(
					'premium_fe_switcher' => 'yes',
				),
			)
		);

		$get_pro = Helper_Functions::get_campaign_link( 'https://premiumaddons.com/pro', 'editor-page', 'wp-editor', 'get-pro' );

		$papro_activated = apply_filters( 'papro_activated', false );

		if ( ! $papro_activated ) {
			$element->add_control(
				'floating_effects_notice',
				array(
					'type'            => Controls_Manager::RAW_HTML,
					'raw'             => __( 'The options in Style and Filters tabs are available in Premium Addons Pro.', 'premium-addons-for-elementor' ) . '<a href="' . esc_url( $get_pro ) . '" target="_blank">' . __( 'Upgrade now!', 'premium-addons-for-elementor' ) . '</a>',
					'content_classes' => 'papro-upgrade-notice',
				)
			);
		}

		$element->start_controls_tabs( 'effects_tabs' );

		$element->start_controls_tab(
			'motion_effects_tab',
			array(
				'label'     => __( 'Motion', 'premium-addons-for-elementor' ),
				'condition' => array(
					'premium_fe_switcher' => 'yes',
				),
			)
		);

		/**--------Translate Effect Controls---------*/
		$element->add_control(
			'premium_fe_translate_switcher',
			array(
				'label'     => __( 'Translate', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::SWITCHER,
				'condition' => array(
					'premium_fe_switcher' => 'yes',
				),
			)
		);

		$element->add_responsive_control(
			'premium_fe_Xtranslate',
			array(
				'label'     => __( 'Translate X', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::SLIDER,
				'default'   => array(
					'sizes' => array(
						'from' => 0,
						'to'   => 5,
					),
					'unit'  => 'px',
				),
				'range'     => array(
					'px' => array(
						'min'  => -150,
						'max'  => 150,
						'step' => 1,
					),
				),
				'labels'    => array(
					__( 'From', 'premium-addons-for-elementor' ),
					__( 'To', 'premium-addons-for-elementor' ),
				),
				'scales'    => 1,
				'handles'   => 'range',
				'condition' => array(
					'premium_fe_switcher'           => 'yes',
					'premium_fe_translate_switcher' => 'yes',
				),
			)
		);

		$element->add_responsive_control(
			'premium_fe_Ytranslate',
			array(
				'label'     => __( 'Translate Y', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::SLIDER,
				'default'   => array(
					'sizes' => array(
						'from' => 0,
						'to'   => 5,
					),
					'unit'  => 'px',
				),
				'range'     => array(
					'px' => array(
						'min'  => -150,
						'max'  => 150,
						'step' => 1,
					),
				),
				'labels'    => array(
					__( 'From', 'premium-addons-for-elementor' ),
					__( 'To', 'premium-addons-for-elementor' ),
				),
				'scales'    => 1,
				'handles'   => 'range',
				'condition' => array(
					'premium_fe_switcher'           => 'yes',
					'premium_fe_translate_switcher' => 'yes',
				),
			)
		);

		$element->add_control(
			'premium_fe_trans_duration',
			array(
				'label'     => __( 'Duration', 'premium-addons-for-elementor' ) . ' (ms)',
				'type'      => Controls_Manager::SLIDER,
				'range'     => array(
					'px' => array(
						'min'  => 0,
						'max'  => 10000,
						'step' => 100,
					),
				),
				'default'   => array(
					'unit' => 'px',
					'size' => 1000,
				),
				'condition' => array(
					'premium_fe_switcher'           => 'yes',
					'premium_fe_translate_switcher' => 'yes',
				),
			)
		);

		$element->add_control(
			'premium_fe_trans_delay',
			array(
				'label'     => __( 'Delay', 'premium-addons-for-elementor' ) . ' (ms)',
				'type'      => Controls_Manager::SLIDER,
				'range'     => array(
					'px' => array(
						'min'  => 0,
						'max'  => 10000,
						'step' => 100,
					),
				),
				'condition' => array(
					'premium_fe_switcher'           => 'yes',
					'premium_fe_translate_switcher' => 'yes',
				),

			)
		);

		/**--------Rotate Effect Controls---------*/
		$element->add_control(
			'premium_fe_rotate_switcher',
			array(
				'label'     => __( 'Rotate', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::SWITCHER,
				'condition' => array(
					'premium_fe_switcher' => 'yes',
				),
			)
		);

		$element->add_responsive_control(
			'premium_fe_Xrotate',
			array(
				'label'     => __( 'Rotate X', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::SLIDER,
				'default'   => array(
					'sizes' => array(
						'from' => 0,
						'to'   => 45,
					),
					'unit'  => 'deg',
				),
				'range'     => array(
					'deg' => array(
						'min' => -180,
						'max' => 180,
					),
				),
				'labels'    => array(
					__( 'From', 'premium-addons-for-elementor' ),
					__( 'To', 'premium-addons-for-elementor' ),
				),
				'scales'    => 1,
				'handles'   => 'range',
				'condition' => array(
					'premium_fe_switcher'        => 'yes',
					'premium_fe_rotate_switcher' => 'yes',
				),
			)
		);

		$element->add_responsive_control(
			'premium_fe_Yrotate',
			array(
				'label'     => __( 'Rotate Y', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::SLIDER,
				'default'   => array(
					'sizes' => array(
						'from' => 0,
						'to'   => 45,
					),
					'unit'  => 'deg',
				),
				'range'     => array(
					'deg' => array(
						'min' => -180,
						'max' => 180,
					),
				),
				'labels'    => array(
					__( 'From', 'premium-addons-for-elementor' ),
					__( 'To', 'premium-addons-for-elementor' ),
				),
				'scales'    => 1,
				'handles'   => 'range',
				'condition' => array(
					'premium_fe_switcher'        => 'yes',
					'premium_fe_rotate_switcher' => 'yes',
				),
			)
		);

		$element->add_responsive_control(
			'premium_fe_Zrotate',
			array(
				'label'     => __( 'Rotate Z', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::SLIDER,
				'default'   => array(
					'sizes' => array(
						'from' => 0,
						'to'   => 45,
					),
					'unit'  => 'deg',
				),
				'range'     => array(
					'deg' => array(
						'min' => -180,
						'max' => 180,
					),
				),
				'labels'    => array(
					__( 'From', 'premium-addons-for-elementor' ),
					__( 'To', 'premium-addons-for-elementor' ),
				),
				'scales'    => 1,
				'handles'   => 'range',
				'condition' => array(
					'premium_fe_switcher'        => 'yes',
					'premium_fe_rotate_switcher' => 'yes',
				),
			)
		);

		$element->add_control(
			'premium_fe_rotate_duration',
			array(
				'label'     => __( 'Duration', 'premium-addons-for-elementor' ) . ' (ms)',
				'type'      => Controls_Manager::SLIDER,
				'range'     => array(
					'px' => array(
						'min'  => 0,
						'max'  => 10000,
						'step' => 100,
					),
				),
				'default'   => array(
					'unit' => 'px',
					'size' => 1000,
				),
				'condition' => array(
					'premium_fe_switcher'        => 'yes',
					'premium_fe_rotate_switcher' => 'yes',
				),
			)
		);

		$element->add_control(
			'premium_fe_rotate_delay',
			array(
				'label'     => __( 'Delay', 'premium-addons-for-elementor' ) . ' (ms)',
				'type'      => Controls_Manager::SLIDER,
				'range'     => array(
					'px' => array(
						'min'  => 0,
						'max'  => 10000,
						'step' => 100,
					),
				),
				'condition' => array(
					'premium_fe_switcher'        => 'yes',
					'premium_fe_rotate_switcher' => 'yes',
				),

			)
		);

		/**--------Scale Effect Controls---------*/
		$element->add_control(
			'premium_fe_scale_switcher',
			array(
				'label'     => __( 'Scale', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::SWITCHER,
				'condition' => array(
					'premium_fe_switcher' => 'yes',
				),
			)
		);

		$element->add_responsive_control(
			'premium_fe_Xscale',
			array(
				'label'     => __( 'Scale X', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::SLIDER,
				'default'   => array(
					'sizes' => array(
						'from' => 1,
						'to'   => 1.2,
					),
					'unit'  => 'px',
				),
				'range'     => array(
					'px' => array(
						'min'  => 0,
						'max'  => 2,
						'step' => 0.1,
					),
				),
				'labels'    => array(
					__( 'From', 'premium-addons-for-elementor' ),
					__( 'To', 'premium-addons-for-elementor' ),
				),
				'scales'    => 1,
				'handles'   => 'range',
				'condition' => array(
					'premium_fe_switcher'       => 'yes',
					'premium_fe_scale_switcher' => 'yes',
				),
			)
		);

		$element->add_responsive_control(
			'premium_fe_Yscale',
			array(
				'label'     => __( 'Scale Y', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::SLIDER,
				'default'   => array(
					'sizes' => array(
						'from' => 1,
						'to'   => 1.2,
					),
					'unit'  => 'px',
				),
				'range'     => array(
					'px' => array(
						'min'  => 0,
						'max'  => 2,
						'step' => 0.1,
					),
				),
				'labels'    => array(
					__( 'From', 'premium-addons-for-elementor' ),
					__( 'To', 'premium-addons-for-elementor' ),
				),
				'scales'    => 1,
				'handles'   => 'range',
				'condition' => array(
					'premium_fe_switcher'       => 'yes',
					'premium_fe_scale_switcher' => 'yes',
				),
			)
		);

		$element->add_control(
			'premium_fe_scale_duration',
			array(
				'label'     => __( 'Duration', 'premium-addons-for-elementor' ) . ' (ms)',
				'type'      => Controls_Manager::SLIDER,
				'range'     => array(
					'px' => array(
						'min'  => 0,
						'max'  => 10000,
						'step' => 100,
					),
				),
				'default'   => array(
					'unit' => 'px',
					'size' => 1000,
				),
				'condition' => array(
					'premium_fe_switcher'       => 'yes',
					'premium_fe_scale_switcher' => 'yes',
				),
			)
		);

		$element->add_control(
			'premium_fe_scale_delay',
			array(
				'label'     => __( 'Delay', 'premium-addons-for-elementor' ) . ' (ms)',
				'type'      => Controls_Manager::SLIDER,
				'range'     => array(
					'px' => array(
						'min'  => 0,
						'max'  => 10000,
						'step' => 100,
					),
				),
				'condition' => array(
					'premium_fe_switcher'       => 'yes',
					'premium_fe_scale_switcher' => 'yes',
				),

			)
		);

		/**--------Skew Effect Controls---------*/
		$element->add_control(
			'premium_fe_skew_switcher',
			array(
				'label'     => __( 'Skew', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::SWITCHER,
				'condition' => array(
					'premium_fe_switcher' => 'yes',
				),
			)
		);

		$element->add_responsive_control(
			'premium_fe_Xskew',
			array(
				'label'     => __( 'Skew X', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::SLIDER,
				'default'   => array(
					'sizes' => array(
						'from' => 0,
						'to'   => 20,
					),
					'unit'  => 'deg',
				),
				'range'     => array(
					'deg' => array(
						'min' => -180,
						'max' => 180,
					),
				),
				'labels'    => array(
					__( 'From', 'premium-addons-for-elementor' ),
					__( 'To', 'premium-addons-for-elementor' ),
				),
				'scales'    => 1,
				'handles'   => 'range',
				'condition' => array(
					'premium_fe_switcher'      => 'yes',
					'premium_fe_skew_switcher' => 'yes',
				),
			)
		);

		$element->add_responsive_control(
			'premium_fe_Yskew',
			array(
				'label'     => __( 'Skew Y', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::SLIDER,
				'default'   => array(
					'sizes' => array(
						'from' => 0,
						'to'   => 20,
					),
					'unit'  => 'deg',
				),
				'range'     => array(
					'deg' => array(
						'min' => -180,
						'max' => 180,
					),
				),
				'labels'    => array(
					__( 'From', 'premium-addons-for-elementor' ),
					__( 'To', 'premium-addons-for-elementor' ),
				),
				'scales'    => 1,
				'handles'   => 'range',
				'condition' => array(
					'premium_fe_switcher'      => 'yes',
					'premium_fe_skew_switcher' => 'yes',
				),
			)
		);

		$element->add_control(
			'premium_fe_skew_duration',
			array(
				'label'     => __( 'Duration', 'premium-addons-for-elementor' ) . ' (ms)',
				'type'      => Controls_Manager::SLIDER,
				'range'     => array(
					'px' => array(
						'min'  => 0,
						'max'  => 10000,
						'step' => 100,
					),
				),
				'default'   => array(
					'unit' => 'px',
					'size' => 1000,
				),
				'condition' => array(
					'premium_fe_switcher'      => 'yes',
					'premium_fe_skew_switcher' => 'yes',
				),
			)
		);

		$element->add_control(
			'premium_fe_skew_delay',
			array(
				'label'     => __( 'Delay', 'premium-addons-for-elementor' ) . ' (ms)',
				'type'      => Controls_Manager::SLIDER,
				'range'     => array(
					'px' => array(
						'min'  => 0,
						'max'  => 10000,
						'step' => 100,
					),
				),
				'condition' => array(
					'premium_fe_switcher'      => 'yes',
					'premium_fe_skew_switcher' => 'yes',
				),

			)
		);

		$element->end_controls_tab();

		$element->start_controls_tab(
			'css_effects_tab',
			array(
				'label'     => __( 'Style', 'premium-addons-for-elementor' ),
				'condition' => array(
					'premium_fe_switcher' => 'yes',
				),
			)
		);

		/**--------CSS Properties Effect Controls---------*/

		/**--------Opacity Effect Controls---------*/
		$element->add_control(
			'premium_fe_opacity_switcher',
			array(
				'label'     => __( 'Opacity', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::SWITCHER,
				'condition' => array(
					'premium_fe_switcher' => 'yes',
				),
			)
		);

		do_action( 'pa_floating_opacity_controls', $element );

		/**--------Background Color Effect Controls---------*/
		$element->add_control(
			'premium_fe_bg_color_switcher',
			array(
				'label'     => __( 'Background Color', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::SWITCHER,
				'separator' => 'before',
				'condition' => array(
					'premium_fe_switcher' => 'yes',
				),
			)
		);

		do_action( 'pa_floating_bg_controls', $element );

		$element->end_controls_tab();

		$element->start_controls_tab(
			'filters_effects_tab',
			array(
				'label'     => __( 'Filters', 'premium-addons-for-elementor' ),
				'condition' => array(
					'premium_fe_switcher' => 'yes',
				),
			)
		);

		/**--------  CSS Filter Blur Controls---------*/
		$element->add_control(
			'premium_fe_blur_switcher',
			array(
				'label'     => __( 'Blur', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::SWITCHER,
				'separator' => 'before',
				'condition' => array(
					'premium_fe_switcher' => 'yes',
				),
			)
		);

		do_action( 'pa_floating_blur_controls', $element );

		/**--------  CSS Filter Contrast Controls---------*/
		$element->add_control(
			'premium_fe_contrast_switcher',
			array(
				'label'     => __( 'Contrast', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::SWITCHER,
				'separator' => 'before',
				'condition' => array(
					'premium_fe_switcher' => 'yes',
				),
			)
		);

		do_action( 'pa_floating_contrast_controls', $element );

		/**--------  CSS Filter grayscale Controls---------*/
		$element->add_control(
			'premium_fe_gScale_switcher',
			array(
				'label'     => __( 'Grayscale', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::SWITCHER,
				'separator' => 'before',
				'condition' => array(
					'premium_fe_switcher' => 'yes',
				),
			)
		);

		do_action( 'pa_floating_gs_controls', $element );

		/**--------  CSS Filter Hue Controls---------*/
		$element->add_control(
			'premium_fe_hue_switcher',
			array(
				'label'     => __( 'Hue', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::SWITCHER,
				'separator' => 'before',
				'condition' => array(
					'premium_fe_switcher' => 'yes',
				),
			)
		);

		do_action( 'pa_floating_hue_controls', $element );

		/**--------  CSS Filter Brightness Controls---------*/
		$element->add_control(
			'premium_fe_brightness_switcher',
			array(
				'label'     => __( 'Brightness', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::SWITCHER,
				'separator' => 'before',
				'condition' => array(
					'premium_fe_switcher' => 'yes',
				),
			)
		);

		do_action( 'pa_floating_brightness_controls', $element );

		/**--------  CSS Filter Saturation Controls---------*/
		$element->add_control(
			'premium_fe_saturate_switcher',
			array(
				'label'     => __( 'Saturation ', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::SWITCHER,
				'separator' => 'before',
				'condition' => array(
					'premium_fe_switcher' => 'yes',
				),
			)
		);

		do_action( 'pa_floating_saturation_controls', $element );

		$element->end_controls_tab();

		$element->end_controls_tabs();

		/**-------- General Settings Controls---------*/
		$element->add_control(
			'premium_fe_general_settings_heading',
			array(
				'label'     => __( 'General Settings', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
				'condition' => array(
					'premium_fe_switcher' => 'yes',
				),
			)
		);

		$element->add_control(
			'premium_fe_direction',
			array(
				'label'     => __( 'Direction', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'alternate',
				'options'   => array(
					'normal'    => __( 'Normal', 'premium-addons-for-elementor' ),
					'reverse'   => __( 'Reverse', 'premium-addons-for-elementor' ),
					'alternate' => __( 'Alternate', 'premium-addons-for-elementor' ),
				),
				'condition' => array(
					'premium_fe_switcher' => 'yes',
				),
			)
		);

		$element->add_control(
			'premium_fe_loop',
			array(
				'label'     => __( 'Loop', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'default',
				'options'   => array(
					'default' => __( 'Infinite', 'premium-addons-for-elementor' ),
					'number'  => __( 'Custom', 'premium-addons-for-elementor' ),
				),
				'condition' => array(
					'premium_fe_switcher' => 'yes',
				),
			)
		);

		$element->add_control(
			'premium_fe_loop_number',
			array(
				'label'     => __( 'Number', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::NUMBER,
				'default'   => 3,
				'condition' => array(
					'premium_fe_switcher' => 'yes',
					'premium_fe_loop'     => 'number',
				),
			)
		);

		$element->add_control(
			'premium_fe_easing',
			array(
				'label'     => __( 'Easing', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'easeInOutSine',
				'options'   => array(
					'linear'                  => __( 'Linear', 'premium-addons-for-elementor' ),
					'easeInOutSine'           => __( 'easeInOutSine', 'premium-addons-for-elementor' ),
					'easeInOutExpo'           => __( 'easeInOutExpo', 'premium-addons-for-elementor' ),
					'easeInOutQuart'          => __( 'easeInOutQuart', 'premium-addons-for-elementor' ),
					'easeInOutCirc'           => __( 'easeInOutCirc', 'premium-addons-for-elementor' ),
					'easeInOutBack'           => __( 'easeInOutBack', 'premium-addons-for-elementor' ),
					'steps'                   => __( 'Steps', 'premium-addons-for-elementor' ),
					'easeInElastic(1, .6)'    => __( 'Elastic In', 'premium-addons-for-elementor' ),
					'easeOutElastic(1, .6)'   => __( 'Elastic Out', 'premium-addons-for-elementor' ),
					'easeInOutElastic(1, .6)' => __( 'Elastic In Out', 'premium-addons-for-elementor' ),
				),
				'condition' => array(
					'premium_fe_switcher' => 'yes',
				),

			)
		);

		$element->add_control(
			'premium_fe_ease_step',
			array(
				'label'     => __( 'Steps', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::NUMBER,
				'default'   => 5,
				'condition' => array(
					'premium_fe_switcher' => 'yes',
					'premium_fe_easing'   => 'steps',
				),
			)
		);

		$element->add_control(
			'premium_fe_disable_safari',
			array(
				'label'        => __( 'Disable Floating Effects On Safari', 'premium-addons-for-elementor' ),
				'type'         => Controls_Manager::SWITCHER,
				'prefix_class' => 'premium-disable-fe-',
				'separator'    => 'before',
				'condition'    => array(
					'premium_fe_switcher' => 'yes',
				),
			)
		);

		$element->end_controls_section();

	}

	/**
	 * Render Floating Effects output on the frontend.
	 *
	 * Written in PHP and used to collect cursor settings and add it as an element attribute.
	 *
	 * @access public
	 * @param object $element for current element.
	 */
	public function before_render( $element ) {

		$data = $element->get_data();

		$type = $data['elType'];

		$settings = $element->get_settings_for_display();

		if ( 'widget' === $type && 'yes' === $settings['premium_fe_switcher'] ) {

			$easing = ( 'steps' === $settings['premium_fe_easing'] ) ? 'steps(' . $settings['premium_fe_ease_step'] . ')' : $settings['premium_fe_easing'];

			$general_settings = array(
				'direction' => $settings['premium_fe_direction'],
				'loop'      => ( 'default' === $settings['premium_fe_loop'] ) ? true : $settings['premium_fe_loop_number'],
				'easing'    => $easing,
				'target'    => ! empty( $settings['premium_fe_target'] ) ? $settings['premium_fe_target'] : '',
			);

			$element->add_render_attribute( '_wrapper', 'data-general_settings', wp_json_encode( $general_settings ) );

			if ( 'yes' === $settings['premium_fe_translate_switcher'] ) {

				$translate_settings = array(
					'x_param_from' => $settings['premium_fe_Xtranslate']['sizes']['from'],
					'x_param_to'   => $settings['premium_fe_Xtranslate']['sizes']['to'],
					'y_param_from' => $settings['premium_fe_Ytranslate']['sizes']['from'],
					'y_param_to'   => $settings['premium_fe_Ytranslate']['sizes']['to'],
					'duration'     => $settings['premium_fe_trans_duration']['size'],
					'delay'        => $settings['premium_fe_trans_delay']['size'],
				);

				$element->add_render_attribute( '_wrapper', 'data-translate_effect', wp_json_encode( $translate_settings ) );
			}

			if ( 'yes' === $settings['premium_fe_rotate_switcher'] ) {

				$rotate_settings = array(
					'x_param_from' => $settings['premium_fe_Xrotate']['sizes']['from'],
					'x_param_to'   => $settings['premium_fe_Xrotate']['sizes']['to'],
					'y_param_from' => $settings['premium_fe_Yrotate']['sizes']['from'],
					'y_param_to'   => $settings['premium_fe_Yrotate']['sizes']['to'],
					'z_param_from' => $settings['premium_fe_Zrotate']['sizes']['from'],
					'z_param_to'   => $settings['premium_fe_Zrotate']['sizes']['to'],
					'duration'     => $settings['premium_fe_rotate_duration']['size'],
					'delay'        => $settings['premium_fe_rotate_delay']['size'],
				);

				$element->add_render_attribute( '_wrapper', 'data-rotate_effect', wp_json_encode( $rotate_settings ) );
			}

			if ( 'yes' === $settings['premium_fe_scale_switcher'] ) {

				$scale_settings = array(
					'x_param_from' => $settings['premium_fe_Xscale']['sizes']['from'],
					'x_param_to'   => $settings['premium_fe_Xscale']['sizes']['to'],
					'y_param_from' => $settings['premium_fe_Yscale']['sizes']['from'],
					'y_param_to'   => $settings['premium_fe_Yscale']['sizes']['to'],
					'duration'     => $settings['premium_fe_scale_duration']['size'],
					'delay'        => $settings['premium_fe_scale_delay']['size'],
				);

				$element->add_render_attribute( '_wrapper', 'data-scale_effect', wp_json_encode( $scale_settings ) );
			}

			if ( 'yes' === $settings['premium_fe_skew_switcher'] ) {

				$skew_settings = array(
					'x_param_from' => $settings['premium_fe_Xskew']['sizes']['from'],
					'x_param_to'   => $settings['premium_fe_Xskew']['sizes']['to'],
					'y_param_from' => $settings['premium_fe_Yskew']['sizes']['from'],
					'y_param_to'   => $settings['premium_fe_Yskew']['sizes']['to'],
					'duration'     => $settings['premium_fe_skew_duration']['size'],
					'delay'        => $settings['premium_fe_skew_delay']['size'],
				);

				$element->add_render_attribute( '_wrapper', 'data-skew_effect', wp_json_encode( $skew_settings ) );
			}

			if ( apply_filters( 'papro_activated', false ) ) {

				if ( 'yes' === $settings['premium_fe_opacity_switcher'] ) {

					$opacity_settings = array(
						'from'     => $settings['premium_fe_opacity']['sizes']['from'] / 100,
						'to'       => $settings['premium_fe_opacity']['sizes']['to'] / 100,
						'duration' => $settings['premium_fe_opacity_duration']['size'],
						'delay'    => $settings['premium_fe_opacity_delay']['size'],
					);

					$element->add_render_attribute( '_wrapper', 'data-opacity_effect', wp_json_encode( $opacity_settings ) );
				}

				if ( 'yes' === $settings['premium_fe_bg_color_switcher'] ) {

					$bg_color_settings = array(
						'from'     => $settings['premium_fe_bg_color_from'],
						'to'       => $settings['premium_fe_bg_color_to'],
						'duration' => $settings['premium_fe_bg_color_duration']['size'],
						'delay'    => $settings['premium_fe_bg_color_delay']['size'],
					);

					$element->add_render_attribute( '_wrapper', 'data-bg_color_effect', wp_json_encode( $bg_color_settings ) );
				}

				if ( 'yes' === $settings['premium_fe_blur_switcher'] ) {

					$blur_settings = array(
						'from'     => 'blur(' . $settings['premium_fe_blur_val']['sizes']['from'] . 'px)',
						'to'       => 'blur(' . $settings['premium_fe_blur_val']['sizes']['to'] . 'px)',
						'duration' => $settings['premium_fe_blur_duration']['size'],
						'delay'    => $settings['premium_fe_blur_delay']['size'],
					);

					$element->add_render_attribute( '_wrapper', 'data-blur_effect', wp_json_encode( $blur_settings ) );
				}

				if ( 'yes' === $settings['premium_fe_contrast_switcher'] ) {

					$contrast_settings = array(
						'from'     => 'contrast(' . $settings['premium_fe_contrast_val']['sizes']['from'] . '%)',
						'to'       => 'contrast(' . $settings['premium_fe_contrast_val']['sizes']['to'] . '%)',
						'duration' => $settings['premium_fe_contrast_duration']['size'],
						'delay'    => $settings['premium_fe_contrast_delay']['size'],
					);

					$element->add_render_attribute( '_wrapper', 'data-contrast_effect', wp_json_encode( $contrast_settings ) );
				}

				if ( 'yes' === $settings['premium_fe_gScale_switcher'] ) {

					$grey_scale_settings = array(
						'from'     => 'grayscale(' . $settings['premium_fe_gScale_val']['sizes']['from'] . '%)',
						'to'       => 'grayscale(' . $settings['premium_fe_gScale_val']['sizes']['to'] . '%)',
						'duration' => $settings['premium_fe_gScale_duration']['size'],
						'delay'    => $settings['premium_fe_gScale_delay']['size'],
					);

					$element->add_render_attribute( '_wrapper', 'data-gray_effect', wp_json_encode( $grey_scale_settings ) );
				}

				if ( 'yes' === $settings['premium_fe_hue_switcher'] ) {

					$hue_settings = array(
						'from'     => 'hue-rotate(' . $settings['premium_fe_hue_val']['sizes']['from'] . 'deg)',
						'to'       => 'hue-rotate(' . $settings['premium_fe_hue_val']['sizes']['to'] . 'deg)',
						'duration' => $settings['premium_fe_hue_duration']['size'],
						'delay'    => $settings['premium_fe_hue_delay']['size'],
					);

					$element->add_render_attribute( '_wrapper', 'data-hue_effect', wp_json_encode( $hue_settings ) );
				}

				if ( 'yes' === $settings['premium_fe_brightness_switcher'] ) {

					$brightness_settings = array(
						'from'     => 'brightness(' . $settings['premium_fe_brightness_val']['sizes']['from'] . '%)',
						'to'       => 'brightness(' . $settings['premium_fe_brightness_val']['sizes']['to'] . '%)',
						'duration' => $settings['premium_fe_brightness_duration']['size'],
						'delay'    => $settings['premium_fe_brightness_delay']['size'],
					);

					$element->add_render_attribute( '_wrapper', 'data-brightness_effect', wp_json_encode( $brightness_settings ) );
				}

				if ( 'yes' === $settings['premium_fe_saturate_switcher'] ) {

					$saturate_settings = array(
						'from'     => 'saturate(' . $settings['premium_fe_saturate_val']['sizes']['from'] . '%)',
						'to'       => 'saturate(' . $settings['premium_fe_saturate_val']['sizes']['to'] . '%)',
						'duration' => $settings['premium_fe_saturate_duration']['size'],
						'delay'    => $settings['premium_fe_saturate_delay']['size'],
					);

					$element->add_render_attribute( '_wrapper', 'data-saturate_effect', wp_json_encode( $saturate_settings ) );
				}
			}
		}

	}

	/**
	 * Check Script Enqueue
	 *
	 * Check if the script files should be loaded.
	 *
	 * @since 4.7.4
	 * @access public
	 *
	 * @param object $element for current element.
	 */
	public function check_script_enqueue( $element ) {

		if ( self::$load_script ) {
			return;
		}

		if ( 'yes' === $element->get_settings_for_display( 'premium_fe_switcher' ) ) {
			$this->enqueue_scripts();

			self::$load_script = true;

			remove_action( 'elementor/frontend/widget/before_render', array( $this, 'check_script_enqueue' ) );
		}

	}

	/**
	 * Creates and returns an instance of the class
	 *
	 * @since 4.2.5
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
