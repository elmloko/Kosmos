<?php
/**
 * Class: Module
 * Name: Global Shape Divider
 * Slug: premium-shape-divider
 */

namespace PremiumAddons\Modules\PremiumShapeDivider;

// Elementor Classes.
use Elementor\Utils;
use Elementor\Icons_Manager;
use Elementor\Control_Media;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Background;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Text_Shadow;

// Premium Addons Classes.
use PremiumAddons\Includes\Helper_Functions;
use PremiumAddons\Admin\Includes\Admin_Helper;
use PremiumAddons\Includes\Premium_Template_Tags;
use PremiumAddons\Includes\Controls\Premium_Image_Choose;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // If this file is called directly, abort.
}

/**
 * Class Module For Premium Global Divider Addon.
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
	 * Class object
	 *
	 * @var instance
	 */
	private $svg_shapes = null;

	/**
	 * Class Constructor Funcion.
	 */
	public function __construct() {

		$modules = Admin_Helper::get_enabled_elements();

		// Checks if Global Divider addon is enabled.
		$global_divider = $modules['premium-shape-divider'];

		if ( ! $global_divider ) {
			return;
		}

		// Enqueue the required JS file.
		add_action( 'elementor/preview/enqueue_scripts', array( $this, 'enqueue_scripts' ) );
		add_action( 'elementor/preview/enqueue_styles', array( $this, 'enqueue_styles' ) );

		// Creates Premium Global Divider tab at the end of layout/content tab.
		add_action( 'elementor/element/section/section_layout/after_section_end', array( $this, 'register_controls' ), 10 );
		add_action( 'elementor/element/column/section_advanced/after_section_end', array( $this, 'register_controls' ), 10 );

		// Editor Hooks.
		add_action( 'elementor/section/print_template', array( $this, 'print_template' ), 10, 2 );
		add_action( 'elementor/column/print_template', array( $this, 'print_template' ), 10, 2 );

		// Frontend Hooks.
		add_action( 'elementor/frontend/section/before_render', array( $this, 'before_render' ) );
		add_action( 'elementor/frontend/column/before_render', array( $this, 'before_render' ) );

		add_action( 'elementor/frontend/before_render', array( $this, 'check_script_enqueue' ) );

		if ( Helper_Functions::check_elementor_experiment( 'container' ) ) {
			add_action( 'elementor/element/container/section_layout/after_section_end', array( $this, 'register_controls' ), 10 );
			add_action( 'elementor/container/print_template', array( $this, 'print_template' ), 10, 2 );
			add_action( 'elementor/frontend/container/before_render', array( $this, 'before_render' ) );
		}

	}

	/**
	 * Template Instance
	 *
	 * @var template_instance
	 */
	protected $template_instance;

	/**
	 * Get Elementor Helper Instance.
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function getTemplateInstance() {

		$this->template_instance = Premium_Template_Tags::getInstance();

		return $this->template_instance;
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

		if ( ! wp_script_is( 'pa-anime', 'enqueued' ) ) {
			wp_enqueue_script( 'pa-anime' );
		}

		if ( ! wp_script_is( 'pa-shape-divider', 'enqueued' ) ) {
			wp_enqueue_script( 'pa-shape-divider' );
		}
	}

	/**
	 * Enqueue styles.
	 *
	 * Registers required dependencies for the extension and enqueues them.
	 *
	 * @since 2.6.5
	 * @access public
	 */
	public function enqueue_styles() {

		if ( ! wp_style_is( 'pa-shape-divider', 'enqueued' ) ) {
			wp_enqueue_style( 'pa-shape-divider' );
		}
	}

	/**
	 * Register Shape Divider controls.
	 *
	 * @since 1.0.0
	 * @access public
	 * @param object $element for current element.
	 */
	public function register_controls( $element ) {

		$element->start_controls_section(
			'section_premium_global_divider',
			array(
				'label' => sprintf( '<i class="pa-extension-icon pa-dash-icon"></i> %s', __( 'Animated Shape Divider', 'premium-addons-for-elementor' ) ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$element->add_control(
			'premium_global_divider_sw',
			array(
				'label'        => __( 'Enable Animated Shape Divider', 'premium-addons-for-elementor' ),
				'type'         => Controls_Manager::SWITCHER,
				'render_type'  => 'template',
				'prefix_class' => 'premium-shape-divider-',
			)
		);

		$element->start_controls_tabs(
			'premium_gdivider_tabs'
		);

		$element->add_control(
			'premium_shapes_data',
			array(
				'label'   => __( 'Shapes Data', 'premium-addons-for-elementor' ),
				'type'    => Controls_Manager::HIDDEN,
				'default' => Helper_Functions::get_svg_shapes(),
			)
		);

		$this->add_divider_content_controls( $element );

		$this->add_divider_style_controls( $element );

		$element->end_controls_tabs();

		$element->end_controls_section();

	}

	/**
	 * Add divider content controls.
	 *
	 * @access private
	 * @since 4.10.4
	 *
	 * @param object $element for current element.
	 */
	private function add_divider_content_controls( $element ) {

		$papro_activated = apply_filters( 'papro_activated', false );

		$not_flip = array( 'shape4' );

		$element->start_controls_tab(
			'premium_divider_content_tab',
			array(
				'label'     => __( 'Content', 'premium-addons-for-elementor' ),
				'condition' => array(
					'premium_global_divider_sw' => 'yes',
				),
			)
		);

		$element->add_control(
			'premium_gdivider_source',
			array(
				'label'        => __( 'Source', 'premium-addons-for-elementor' ),
				'type'         => Controls_Manager::SELECT,
				'render_type'  => 'template',
				'prefix_class' => 'premium-shape-divider__',
				'options'      => array(
					'default' => __( 'Default', 'premium-addons-for-elementor' ),
					'custom'  => __( 'Custom SVG', 'premium-addons-for-elementor' ),
				),
				'default'      => 'default',
				'condition'    => array(
					'premium_global_divider_sw' => 'yes',
				),
			)
		);

		$element->add_control(
			'premium_gdivider_defaults',
			array(
				'label'        => __( 'Shapes', 'premium-addons-for-elementor' ),
				'type'         => Premium_Image_Choose::TYPE,
				'options'      => Helper_Functions::get_svg_shapes(),
				'render_type'  => 'template',
				'prefix_class' => 'premium-',
				'default'      => 'shape22',
				'condition'    => array(
					'premium_global_divider_sw' => 'yes',
					'premium_gdivider_source'   => 'default',
				),
			)
		);

		$element->add_responsive_control(
			'premium_gdivider_pos',
			array(
				'label'       => __( 'Position', 'premium-addons-for-elementor' ),
				'type'        => Controls_Manager::CHOOSE,
				'render_type' => 'template',
				'options'     => array(
					'top'    => array(
						'title' => __( 'Top', 'premium-addons-for-elementor' ),
						'icon'  => 'eicon-arrow-up',
					),
					'bottom' => array(
						'title' => __( 'Bottom', 'premium-addons-for-elementor' ),
						'icon'  => 'eicon-arrow-down',
					),
					'left'   => array(
						'title' => __( 'Left', 'premium-addons-for-elementor' ),
						'icon'  => 'eicon-arrow-left',
					),
					'right'  => array(
						'title' => __( 'Right', 'premium-addons-for-elementor' ),
						'icon'  => 'eicon-arrow-right',
					),
				),
				'default'     => 'bottom',
				'toggle'      => false,
				'condition'   => array(
					'premium_global_divider_sw' => 'yes',
				),
				'selectors'   => array(
					'{{WRAPPER}}' => '--pa-sh-divider-pos:{{VALUE}}',
				),
			)
		);

		if ( ! $papro_activated ) {

			$get_pro = Helper_Functions::get_campaign_link( 'https://premiumaddons.com/pro', 'editor-page', 'wp-editor', 'get-pro' );

			$pro_shapes = array();

			for ( $x = 26; $x <= 55; $x++ ) {
				array_push( $pro_shapes, 'shape' . $x );
			}

			$element->add_control(
				'pro_options_notice',
				array(
					'type'            => Controls_Manager::RAW_HTML,
					'raw'             => __( 'This option is available in Premium Addons Pro. ', 'premium-addons-for-elementor' ) . '<a href="' . esc_url( $get_pro ) . '" target="_blank">' . __( 'Upgrade now!', 'premium-addons-for-elementor' ) . '</a>',
					'content_classes' => 'papro-upgrade-notice',
					'conditions'      => array(
						'terms' => array(
							array(
								'name'  => 'premium_global_divider_sw',
								'value' => 'yes',
							),
							array(
								'relation' => 'or',
								'terms'    => array(
									array(
										'terms' => array(
											array(
												'name'  => 'premium_gdivider_source',
												'value' => 'default',
											),
											array(
												'name'     => 'premium_gdivider_defaults',
												'operator' => 'in',
												'value'    => $pro_shapes,
											),
										),
									),
									array(
										'terms' => array(
											array(
												'name'     => 'premium_gdivider_pos',
												'operator' => 'in',
												'value'    => array( 'left', 'right' ),
											),
										),
									),
									array(
										'terms' => array(
											array(
												'name'     => 'premium_gdivider_source',
												'operator' => '===',
												'value'    => 'custom',
											),
										),
									),

								),
							),
						),
					),
				)
			);

		} else {
			do_action( 'pa_divider_custom_svg', $element );
		}

		$element->add_responsive_control(
			'premium_gdivider_height',
			array(
				'label'       => __( 'Short Axis (PX)', 'premium-addons-for-elementor' ),
				'type'        => Controls_Manager::SLIDER,
				'description' => __( 'Use this option to change the height of the divider', 'premium-addons-pro' ),
				'range'       => array(
					'px' => array(
						'min'  => 0,
						'max'  => 2000,
						'step' => 1,
					),
				),
				'default'     => array(
					'unit' => 'px',
					'size' => 150,
				),
				'selectors'   => array(
					'{{WRAPPER}} #premium-shape-divider-{{ID}} svg' => 'height:{{SIZE}}px',
				),
				'condition'   => array(
					'premium_global_divider_sw' => 'yes',
				),
			)
		);

		$element->add_responsive_control(
			'premium_gdivider_width',
			array(
				'label'       => __( 'Long Axis', 'premium-addons-for-elementor' ),
				'type'        => Controls_Manager::SLIDER,
				'description' => __( 'Use this option to change the width of the divider.', 'premium-addons-pro' ),
				'range'       => array(
					'px' => array(
						'min'  => 100,
						'max'  => 1000,
						'step' => 1,
					),
				),
				'default'     => array(
					'unit' => 'px',
					'size' => 100,
				),
				'selectors'   => array(
					'{{WRAPPER}}.premium-shape-divider__top #premium-shape-divider-{{ID}} svg, {{WRAPPER}}.premium-shape-divider__bottom #premium-shape-divider-{{ID}} svg' => 'width: calc( {{SIZE}}% + 2px );',
					'{{WRAPPER}}.premium-shape-divider__right #premium-shape-divider-{{ID}} svg, {{WRAPPER}}.premium-shape-divider__left #premium-shape-divider-{{ID}} svg' => 'width: calc( var(--premium-shape-divider-h) + {{SIZE}}px ) !important;',
				),
				'condition'   => array(
					'premium_global_divider_sw' => 'yes',
					'premium_gdivider_animate!' => 'yes',
				),
			)
		);

		$element->add_responsive_control(
			'premium_gdivider_scale',
			array(
				'label'       => __( 'Long Axis', 'premium-addons-for-elementor' ),
				'type'        => Controls_Manager::SLIDER,
				'description' => __( 'Use this option to change the width of the divider.', 'premium-addons-pro' ),
				'range'       => array(
					'px' => array(
						'min'  => 1.1,
						'max'  => 20,
						'step' => 1,
					),
				),
				'default'     => array(
					'unit' => 'px',
					'size' => 4,
				),
				'selectors'   => array(
					'{{WRAPPER}}.premium-shape-divider__bottom:not(.premium-sh-no-stretch-yes) #premium-shape-divider-{{ID}}' => 'transform: scaleX({{SIZE}}); --pa-divider-scale: {{SIZE}}',
					'{{WRAPPER}}.premium-shape-divider__top:not(.premium-sh-no-stretch-yes) #premium-shape-divider-{{ID}}' => 'transform: scaleX({{SIZE}}) rotateX(180deg); --pa-divider-scale: {{SIZE}}',

					'{{WRAPPER}}.premium-shape-divider__bottom.premium-sh-no-stretch-yes #premium-shape-divider-{{ID}}' => 'transform: scale({{SIZE}}); --pa-divider-scale: {{SIZE}}',
					'{{WRAPPER}}.premium-shape-divider__top.premium-sh-no-stretch-yes #premium-shape-divider-{{ID}}' => 'transform: scale({{SIZE}}) rotateX(180deg); --pa-divider-scale: {{SIZE}}',

					'{{WRAPPER}}.premium-shape-divider__right:not(.premium-sh-no-stretch-yes) #premium-shape-divider-{{ID}}, {{WRAPPER}}.premium-shape-divider__left:not(.premium-sh-no-stretch-yes) #premium-shape-divider-{{ID}}' => 'transform: scaleY({{SIZE}}); --pa-divider-scale: {{SIZE}}',

					'{{WRAPPER}}.premium-shape-divider__right.premium-sh-no-stretch-yes #premium-shape-divider-{{ID}}, {{WRAPPER}}.premium-shape-divider__left.premium-sh-no-stretch-yes #premium-shape-divider-{{ID}}' => 'transform: scale({{SIZE}}); --pa-divider-scale: {{SIZE}}',
				),
				'condition'   => array(
					'premium_global_divider_sw' => 'yes',
					'premium_gdivider_animate'  => 'yes',
				),
			)
		);

		$element->add_responsive_control(
			'premium_gdivider_offset',
			array(
				'label'     => __( 'Offset', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::SLIDER,
				'range'     => array(
					'px' => array(
						'min'  => -200,
						'max'  => 200,
						'step' => 1,
					),
				),
				'selectors' => array(
					'{{WRAPPER}} #premium-shape-divider-{{ID}}' => '{{premium_gdivider_pos.VALUE}}: {{SIZE}}px;',
				),
				'condition' => array(
					'premium_global_divider_sw' => 'yes',
					// 'premium_gdivider_animate!' => 'yes',
				),
			)
		);

		$no_anime = array( 'shape2', 'shape3', 'shape4', 'shape14', 'shape18', 'shape19' );

		$element->add_control(
			'premium_gdivider_animate',
			array(
				'label'        => __( 'Animate', 'premium-addons-for-elementor' ),
				'type'         => Controls_Manager::SWITCHER,
				'prefix_class' => 'premium-shape-divider-anime-',
				'render_type'  => 'template',
				'default'      => 'yes',
				'condition'    => array(
					'premium_global_divider_sw' => 'yes',
				),
			)
		);

		$element->add_control(
			'premium_gdivider_no_stretch',
			array(
				'label'        => __( 'Prevent Stretch', 'premium-addons-for-elementor' ),
				'type'         => Controls_Manager::SWITCHER,
                'description'=> __('This option is used if you want to animate the divider without stretching the SVG.', 'premium-addons-pro'),
				'prefix_class' => 'premium-sh-no-stretch-',
				'default'      => 'yes',
				'render_type'  => 'template',
				'condition'    => array(
					'premium_global_divider_sw'  => 'yes',
					'premium_gdivider_animate'   => 'yes',
					'premium_gdivider_defaults!' => 'shape22',
				),
			)
		);

		$element->add_control(
			'animation_notice',
			array(
				'raw'             => __( 'Important: You may need to change Long Axis option value to fit your needs.', 'premium-addons-for-elementor' ),
				'type'            => Controls_Manager::RAW_HTML,
				'content_classes' => 'elementor-panel-alert elementor-panel-alert-warning',
				'condition'       => array(
					'premium_global_divider_sw' => 'yes',
					'premium_gdivider_animate'  => 'yes',
				),
			)
		);

		$element->add_responsive_control(
			'premium_gdivider_anime_speed',
			array(
				'label'     => __( 'Animation Speed (sec)', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::SLIDER,
				'range'     => array(
					'px' => array(
						'min'  => 0,
						'max'  => 20,
						'step' => 0.1,
					),
				),
				'default'   => array(
					'size' => 10,
					'unit' => 'px',
				),
				'selectors' => array(
					'{{WRAPPER}}.premium-shape-divider-anime-yes:not(.premium-shape22) #premium-shape-divider-{{ID}}' => 'animation-duration: {{SIZE}}s;',
				),
				'condition' => array(
					'premium_global_divider_sw'  => 'yes',
					'premium_gdivider_animate'   => 'yes',
					'premium_gdivider_defaults!' => 'shape22',
				),
			)
		);

		$element->add_control(
			'premium_gdivider_anime_dir',
			array(
				'label'     => __( 'Animation Direction', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::SELECT,
				'options'   => array(
					'noraml'    => __( 'Normal', 'premium-addons-for-elementor' ),
					'reverse'   => __( 'Reverse', 'premium-addons-for-elementor' ),
					'alternate' => __( 'Alternate', 'premium-addons-for-elementor' ),
				),
				'default'   => 'alternate',
				'condition' => array(
					'premium_global_divider_sw'  => 'yes',
					'premium_gdivider_animate'   => 'yes',
					'premium_gdivider_defaults!' => 'shape22',
				),
				'selectors' => array(
					'{{WRAPPER}}.premium-shape-divider-anime-yes:not(.premium-shape22) #premium-shape-divider-{{ID}}' => 'animation-direction: {{VALUE}};',
				),
			)
		);

		$element->add_control(
			'premium_gdivider_flip',
			array(
				'label'        => __( 'Horizontal Flip', 'premium-addons-for-elementor' ),
				'type'         => Controls_Manager::SWITCHER,
				'render_type'  => 'template',
				'prefix_class' => 'premium-sh-divider-hflip-',
				'condition'    => array(
					'premium_global_divider_sw' => 'yes',
					'premium_gdivider_animate!' => 'yes',
					'premium_gdivider_pos'      => array( 'top', 'bottom' ),
				),
			)
		);

		$element->add_control(
			'premium_gdivider_flip_x',
			array(
				'label'        => __( 'Vertical Flip', 'premium-addons-for-elementor' ),
				'type'         => Controls_Manager::SWITCHER,
				'prefix_class' => 'premium-sh-divider-vflip-',
				'render_type'  => 'template',
				'condition'    => array(
					'premium_global_divider_sw' => 'yes',
					'premium_gdivider_animate!' => 'yes',
				),
			)
		);

		$element->add_control(
			'premium_gdivider_hide',
			array(
				'label'       => __( 'Hide Shape On', 'premium-addons-for-elementor' ),
				'type'        => Controls_Manager::SELECT2,
				'options'     => Helper_Functions::get_all_breakpoints(),
				'separator'   => 'after',
				'multiple'    => true,
				'label_block' => true,
				'default'     => array(),
				'condition'   => array(
					'premium_global_divider_sw' => 'yes',
				),
			)
		);

		$element->end_controls_tab();
	}

	/**
	 * Add tooltips style controls.
	 *
	 * @access private
	 * @since 4.10.4
	 *
	 * @param object $element for current element.
	 */
	private function add_divider_style_controls( $element ) {

		$element->start_controls_tab(
			'premium_gdivider_style_tab',
			array(
				'label'     => __( 'Style', 'premium-addons-for-elementor' ),
				'condition' => array(
					'premium_global_divider_sw' => 'yes',
				),
			)
		);

		$papro_activated = apply_filters( 'papro_activated', false );

		$element->add_control(
			'premium_gdivider_bg_type',
			array(
				'label'       => __( 'Fill', 'premium-addons-pro' ),
				'type'        => Controls_Manager::SELECT,
				'render_type' => 'template',
				'default'     => 'color',
				'options'     => array(
					'color'    => __( 'Color', 'premium-addons-pro' ),
					'image'    => __( 'Image', 'premium-addons-pro' ),
					'gradient' => __( 'Gradient', 'premium-addons-pro' ),
				),
				'condition'   => array(
					'premium_global_divider_sw' => 'yes',
				),
			)
		);

		$element->add_control(
			'premium_gdivider_fill',
			array(
				'label'     => __( 'Color', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#afafaf',
				'selectors' => array(
					'{{WRAPPER}} #premium-shape-divider-{{ID}} svg,
                    {{WRAPPER}} #premium-shape-divider-{{ID}} svg *' => 'fill: {{VALUE}};',
				),
				'condition' => array(
					'premium_global_divider_sw' => 'yes',
					'premium_gdivider_bg_type'  => 'color',
				),
			)
		);

		if ( ! $papro_activated ) {

			$get_pro = Helper_Functions::get_campaign_link( 'https://premiumaddons.com/pro', 'editor-page', 'wp-editor', 'get-pro' );

			$element->add_control(
				'divider_fill_notice',
				array(
					'type'            => Controls_Manager::RAW_HTML,
					'raw'             => __( 'This option is available in Premium Addons Pro. ', 'premium-addons-for-elementor' ) . '<a href="' . esc_url( $get_pro ) . '" target="_blank">' . __( 'Upgrade now!', 'premium-addons-for-elementor' ) . '</a>',
					'content_classes' => 'papro-upgrade-notice',
					'condition'       => array(
						'premium_global_divider_sw' => 'yes',
						'premium_gdivider_bg_type!' => 'color',
					),
				)
			);

		} else {
			do_action( 'pa_divider_fill_controls', $element );
		}

		$element->add_responsive_control(
			'premium_gdivider_stroke_width',
			array(
				'label'     => __( 'Stroke Width', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::SLIDER,
				'range'     => array(
					'px' => array(
						'min'  => 0,
						'max'  => 10,
						'step' => 0.1,
					),
				),
				'separator' => 'before',
				'selectors' => array(
					'{{WRAPPER}} #premium-shape-divider-{{ID}} svg' => 'stroke-width: {{SIZE}}px;',
				),
				'condition' => array(
					'premium_global_divider_sw' => 'yes',
				),
			)
		);

		$element->add_control(
			'premium_gdivider_stroke',
			array(
				'label'     => __( 'Stroke Color', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} #premium-shape-divider-{{ID}} svg' => 'stroke: {{VALUE}};',
				),
				'condition' => array(
					'premium_global_divider_sw' => 'yes',
				),
			)
		);

		$element->add_responsive_control(
			'premium_gdivider_opacity',
			array(
				'label'     => __( 'Opacity', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::SLIDER,
				'range'     => array(
					'px' => array(
						'min'  => 0,
						'max'  => 1,
						'step' => 0.1,
					),
				),
				'default'   => array(
					'unit' => 'px',
					'size' => 0.3,
				),
				'selectors' => array(
					'{{WRAPPER}} #premium-shape-divider-{{ID}} svg' => 'opacity: {{SIZE}};',
				),
				'condition' => array(
					'premium_global_divider_sw' => 'yes',
				),
			)
		);

		$element->add_control(
			'premium_gdivider_zindex',
			array(
				'label'     => __( 'Z-Index', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::NUMBER,
				'selectors' => array(
					'{{WRAPPER}} #premium-shape-divider-{{ID}} svg' => 'z-index: {{VALUE}};',
				),
				'condition' => array(
					'premium_global_divider_sw' => 'yes',
				),
			)
		);

		$element->end_controls_tab();
	}

	/**
	 * Render Global divider output in the editor.
	 *
	 * Written as a Backbone JavaScript template and used to generate the live preview.
	 *
	 * @since 2.2.8
	 * @access public
	 *
	 * @param object $template for current template.
	 * @param object $element for current element.
	 */
	public function print_template( $template, $element ) {

		if ( ! $template && 'widget' === $element->get_type() ) {
			return;
		}

		$old_template = $template;
		ob_start();
		?>
		<#
			var isEnabled = 'yes' === settings.premium_global_divider_sw ? true : false;

			if ( isEnabled && settings.premium_shapes_data ) {

				var source = settings.premium_gdivider_source,
					dividerSettings = {},
					shapesData = settings.premium_shapes_data,
					shapeHTML = '',
					customFill = 'color' !== settings.premium_gdivider_bg_type;

				if ( customFill ) {
					var imgFill =  'image' === settings.premium_gdivider_bg_type;

					view.addRenderAttribute( 'fill_data', {
						'xmlns': 'http://www.w3.org/2000/svg',
						'id': 'premium-shape-divider-fill-' + view.getID(),
						'width': '0px',
						'height': '0px',
						'preserveAspectRatio': 'none'
					});

					if ( imgFill && settings.premium_gdivider_image ) {

						var fillHtml = "",
							imgSrc = settings.premium_gdivider_image.url,
							imgOptions = {
								'width': '100%',
								'height': '100%',
								'xpos': 0,
								'ypos': 0 ,
								'aspect': ' preserveAspectRatio="none"'
							};

						view.addRenderAttribute( 'pattern', {
							'id': 'pa-shape-divider-fill-' + view.getID(),
							'width': '100%',
							'height': '100%',
							'patternUnits': 'userSpaceOnUse'
						});

						view.addRenderAttribute( 'pattern_img', {
							'href': imgSrc,
							'width': imgOptions.width,
							'height': imgOptions.height,
							'x': imgOptions.xpos,
							'y': imgOptions.ypos
						});

						#>
							<svg {{{ view.getRenderAttributeString( 'fill_data' ) }}} >
								<defs>
									<pattern {{{view.getRenderAttributeString( 'pattern' ) }}} >
										<image {{{view.getRenderAttributeString( 'pattern_img' ) }}} {{{imgOptions.aspect}}}/>
									</pattern>
								</defs>
							</svg>
						<#
					} else if( settings.premium_gdivider_grad_xpos ) {
						var gradType = settings.premium_gdivider_grad_type,
							gradPos = 'linear' === gradType ? settings.premium_gdivider_grad_angle.size : [settings.premium_gdivider_grad_xpos.size, settings.premium_gdivider_grad_ypos.size ],
                            gradUnit = 'linear' === gradType ? 'deg' : '',
                            gradOptions = {
								'gradType'  : gradType,
								'firstColor': settings.premium_gdivider_grad_firstcolor,
								'secColor'  : settings.premium_gdivider_grad_secondcolor,
								'firstLoc'  : settings.premium_gdivider_grad_firstloc.size,
								'secLoc'    : settings.premium_gdivider_grad_secondloc.size,
								'pos'       : gradPos
							};

						view.addRenderAttribute( 'grad_data', {
							'id': 'pa-shape-divider-fill-' + view.getID(),
							'gradientUnits': 'objectBoundingBox'
						});

						view.addRenderAttribute( 'grad_color', {
							'offset': gradOptions.firstLoc + '%',
							'stop-color': gradOptions.firstColor
						});

						view.addRenderAttribute( 'grad_color_sec', {
							'offset': gradOptions.secLoc + '%',
							'stop-color': gradOptions.secColor
						});

						#>
							<svg {{{ view.getRenderAttributeString( 'fill_data' ) }}} >
								<defs>
									<#
										if ( 'linear' === gradOptions.gradType ) {
											view.addRenderAttribute( 'grad_data', {
												'gradientTransform': 'rotate(' + gradOptions.pos + gradUnit + ')'
											});
											#>
											<linearGradient {{{view.getRenderAttributeString( 'grad_data' ) }}}>
												<stop {{{view.getRenderAttributeString( 'grad_color' ) }}} ></stop>
												<stop {{{view.getRenderAttributeString( 'grad_color_sec' ) }}} ></stop>
											</linearGradient>
											<#
										} else {
											view.addRenderAttribute( 'grad_data', {
												'cx': gradOptions.pos[0] + '%',
												'cy': gradOptions.pos[1] + '%'
											});
											#>
											<radialGradient {{{view.getRenderAttributeString( 'grad_data' ) }}}>
												<stop {{{view.getRenderAttributeString( 'grad_color' ) }}} ></stop>
												<stop {{{view.getRenderAttributeString( 'grad_color_sec' ) }}} ></stop>
											</radialGradient>
											<#
										}
									#>
								</defs>
							</svg>
						<#
					}

				}

				if ( 'default' !== source ) {
					shapeHTML = settings.premium_gdivider_custom;
				} else {
					shapeHTML = '' !== settings.premium_gdivider_defaults ? shapesData[ settings.premium_gdivider_defaults ]['imagesmall'] : '';
				}

				function getContainerClasses() {
					var classes = 'premium-shape-divider__shape-container',
						hiddenDevices = settings.premium_gdivider_hide;

					if ( hiddenDevices.length ) {
						hiddenDevices.forEach(function(device) {
							classes += ' elementor-hidden-' + device;
						});

						classes += ' premium-addons-element';
					}

					return classes;
				}

				if ( '' !== shapeHTML ) {
					view.addRenderAttribute( 'paShapeDivider', {
						'id': 'premium-shape-divider-' + view.getID(),
						'class': getContainerClasses(),
						'style': 'visibility:hidden; opacity:0;'
					});
					#>
						<div {{{ view.getRenderAttributeString( 'paShapeDivider' ) }}} >{{{shapeHTML}}}</div>
					<#
				}
			}

		#>

		<?php
			$slider_content = ob_get_contents();
			ob_end_clean();
			$template = $slider_content . $old_template;
			return $template;
	}

	/**
	 * Render Global divider output on the frontend.
	 *
	 * Written in PHP and used to generate the final HTML.
	 *
	 * @since 1.0.0
	 * @access public
	 * @param object $element for current element.
	 */
	public function before_render( $element ) {

		$element_type = $element->get_type();

		$id = $element->get_id();

		$settings = $element->get_settings_for_display();

		$divider_enabled = $settings['premium_global_divider_sw'];

		if ( 'yes' === $divider_enabled ) {

			$papro_activated = apply_filters( 'papro_activated', false );

			if ( ! $papro_activated || version_compare( PREMIUM_PRO_ADDONS_VERSION, '2.9.8', '<' ) ) {

				$is_pro_shape = 'default' === $settings['premium_gdivider_source'] && str_replace( 'shape', '', $settings['premium_gdivider_defaults'] ) > 25;

				if ( $is_pro_shape || 'custom' === $settings['premium_gdivider_source'] || 'color' !== $settings['premium_gdivider_bg_type'] || in_array( $settings['premium_gdivider_pos'], array( 'left', 'right' ) ) ) {

					?>
					<div class="premium-error-notice">
						<?php
							$message = __( 'This option is available in <b>Premium Addons Pro</b>.', 'premium-addons-for-elementor' );
							echo wp_kses_post( $message );
						?>
					</div>
					<?php
					return false;

				}
			}

			$source           = $settings['premium_gdivider_source'];
			$divider_settings = array();
			$is_edit_mode     = \Elementor\Plugin::$instance->editor->is_edit_mode();
			$hidden_style     = 'visibility:hidden; position: absolute; opacity:0;';
			$shape            = '';
			$custom_fill      = 'color' !== $settings['premium_gdivider_bg_type'];
			$shape_classes    = Helper_Functions::get_element_classes( $settings['premium_gdivider_hide'], array( 'premium-shape-divider__shape-container' ) );

			if ( 'default' !== $source ) {
				$shape = $settings['premium_gdivider_custom'];
			} else {
				$shape = '' !== $settings['premium_gdivider_defaults'] ? Helper_Functions::get_svg_shapes( $settings['premium_gdivider_defaults'] ) : '';
			}

			if ( $custom_fill ) {
				$this->add_custom_fill( $id, $settings );
			}

			$element->add_render_attribute(
				'shape_divider_cont' . $id,
				array(
					'class' => $shape_classes,
					'id'    => 'premium-shape-divider-' . esc_attr( $id ),
					'style' => $hidden_style,
				)
			);

			?>
				<div <?php echo wp_kses_post( $element->get_render_attribute_string( 'shape_divider_cont' . $id ) ); ?>>
					<?php echo $shape; ?>
				</div>
			<?php
		}
	}

	public function add_custom_fill( $id, $settings ) {

		$img_fill = 'image' === $settings['premium_gdivider_bg_type'];

		$svg_html = '<svg xmlns="http://www.w3.org/2000/svg" preserveAspectRatio="none" id="premium-shape-divider-fill-' . $id . '" width="0px" height="0px"><defs>';

		if ( $img_fill ) {
			$img_src     = $settings['premium_gdivider_image']['url'];
			$img_options = array(
				'width'  => '100%',
				'height' => '100%',
				'xpos'   => 0,
				'ypos'   => 0,
				'aspect' => 'preserveAspectRatio="none"',
			);

			$svg_html .= '<pattern id="pa-shape-divider-fill-' . $id . '" patternUnits="userSpaceOnUse" width="100%" height="100%">' .

			'<image href="' . $img_src . '" x="' . $img_options['xpos'] . '" y="' . $img_options['ypos'] . '" width="' . $img_options['width'] . '" height="' . $img_options['height'] . '" ' . $img_options['aspect'] . '" />' .

			'</pattern>';
		} else {
			// gradient
			$gradient_type = $settings['premium_gdivider_grad_type'];
			$grad_pos      = 'linear' === $gradient_type ? $settings['premium_gdivider_grad_angle']['size'] : array( $settings['premium_gdivider_grad_xpos']['size'], $settings['premium_gdivider_grad_ypos']['size'] );
            $grad_unit = 'linear' === $gradient_type ? 'deg' : '';

			$grad_options = array(
				'gradType'   => $gradient_type,
				'firstColor' => $settings['premium_gdivider_grad_firstcolor'],
				'secColor'   => $settings['premium_gdivider_grad_secondcolor'],
				'firstLoc'   => $settings['premium_gdivider_grad_firstloc']['size'],
				'secLoc'     => $settings['premium_gdivider_grad_secondloc']['size'],
				'pos'        => $grad_pos,
			);

			if ( 'linear' === $grad_options['gradType'] ) {
				$tag_close = '</linearGradient>';
				$svg_html .= '<linearGradient id="pa-shape-divider-fill-' . $id . '" gradientUnits="objectBoundingBox"  gradientTransform="rotate(' . $grad_options['pos'] . $grad_unit.  ')">';
			} else {
				$tag_close = '</radialGradient>';
				$svg_html .= '<radialGradient id="pa-shape-divider-fill-' . $id . '" gradientUnits="objectBoundingBox" cx="' . $grad_options['pos'][0] . '%" cy="' . $grad_options['pos'][1] . '%">';
			}

			$svg_html .= '<stop offset="' . $grad_options['firstLoc'] . '%" stop-color="' . $grad_options['firstColor'] . '" />' .
				'<stop offset="' . $grad_options['secLoc'] . '%" stop-color="' . $grad_options['secColor'] . '" />';

			$svg_html .= $tag_close;
		}

		$svg_html .= '</defs></svg>';
		echo $svg_html;
	}

	/**
	 * Check Script Enqueue
	 *
	 * Check if the script files should be loaded.
	 *
	 * @since 2.6.3
	 * @access public
	 *
	 * @param object $element for current element.
	 */
	public function check_script_enqueue( $element ) {

		if ( self::$load_script ) {
			return;
		}

		if ( 'yes' === $element->get_settings_for_display( 'premium_global_divider_sw' ) ) {

			$this->enqueue_styles();
			$this->enqueue_scripts();

			self::$load_script = true;

			remove_action( 'elementor/frontend/before_render', array( $this, 'check_script_enqueue' ) );
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
