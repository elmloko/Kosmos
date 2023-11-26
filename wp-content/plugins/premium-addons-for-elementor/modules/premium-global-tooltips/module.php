<?php
/**
 * Class: Module
 * Name: Global Tooltips
 * Slug: premium-gloabal-tooltips
 */

namespace PremiumAddons\Modules\PremiumGlobalTooltips;

// Elementor Classes.
use Elementor\Utils;
use Elementor\Repeater;
use Elementor\Icons_Manager;
use Elementor\Control_Media;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Background;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Text_Shadow;

// Premium Addons Classes.
use PremiumAddons\Admin\Includes\Admin_Helper;
use PremiumAddons\Includes\Helper_Functions;
use PremiumAddons\Includes\Premium_Template_Tags;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // If this file is called directly, abort.
}

/**
 * Class Module For Premium Global Tooltips Addon.
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
	 * Class Constructor Funcion.
	 */
	public function __construct() {

		$modules = Admin_Helper::get_enabled_elements();

		// Checks if Global Tooltips addon is enabled.
		$global_tooltip = $modules['premium-global-tooltips'];

		if ( ! $global_tooltip ) {
			return;
		}

		// Enqueue the required JS file.
		add_action( 'elementor/preview/enqueue_scripts', array( $this, 'enqueue_scripts' ) );
		add_action( 'elementor/preview/enqueue_styles', array( $this, 'enqueue_styles' ) );

		// Creates Premium Global Tooltips tab at the end of layout/content tab.
		add_action( 'elementor/element/section/section_layout/after_section_end', array( $this, 'register_controls' ), 10 );
		add_action( 'elementor/element/column/section_advanced/after_section_end', array( $this, 'register_controls' ), 10 );
		add_action( 'elementor/element/common/_section_style/after_section_end', array( $this, 'register_controls' ), 10 );

		// Editor Hooks.
		add_action( 'elementor/section/print_template', array( $this, 'print_template' ), 10, 2 );
		add_action( 'elementor/column/print_template', array( $this, 'print_template' ), 10, 2 );
		add_action( 'elementor/widget/print_template', array( $this, 'print_template' ), 10, 2 );

		// Frontend Hooks.
		add_action( 'elementor/frontend/section/before_render', array( $this, 'before_render' ) );
		add_action( 'elementor/frontend/column/before_render', array( $this, 'before_render' ) );
		add_action( 'elementor/widget/before_render_content', array( $this, 'before_render' ), 10, 1 );

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

		if ( ! wp_script_is( 'elementor-waypoints', 'enqueued' ) ) {
			wp_enqueue_script( 'elementor-waypoints' );
		}

		if ( ! wp_script_is( 'lottie-js', 'enqueued' ) ) {
			wp_enqueue_script( 'lottie-js' );
		}

		if ( ! wp_script_is( 'pa-tweenmax', 'enqueued' ) ) {
			wp_enqueue_script( 'pa-tweenmax' );
		}

		if ( ! wp_script_is( 'tooltipster-bundle', 'enqueued' ) ) {
			wp_enqueue_script( 'tooltipster-bundle' );
		}

		if ( ! wp_script_is( 'pa-gTooltips', 'enqueued' ) ) {
			wp_enqueue_script( 'pa-gTooltips' );
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

		if ( ! wp_style_is( 'tooltipster', 'enqueued' ) ) {
			wp_enqueue_style( 'tooltipster' );
		}

		if ( ! wp_style_is( 'pa-gTooltips', 'enqueued' ) ) {
			wp_enqueue_style( 'pa-gTooltips' );
		}
	}

	/**
	 * Register Global Tooltip controls.
	 *
	 * @since 1.0.0
	 * @access public
	 * @param object $element for current element.
	 */
	public function register_controls( $element ) {

		$tab = 'common' !== $element->get_name() ? Controls_Manager::TAB_LAYOUT : Controls_Manager::TAB_CONTENT;

		$element->start_controls_section(
			'section_premium_global_tooltip',
			array(
				'label' => sprintf( '<i class="pa-extension-icon pa-dash-icon"></i> %s', __( 'Global Tooltip', 'premium-addons-for-elementor' ) ),
				'tab'   => $tab,
			)
		);

		$element->add_control(
			'premium_tooltip_update',
			array(
				'label' => '<div class="elementor-update-preview" style="background-color: #fff;"><div class="elementor-update-preview-title">Update changes to page</div><div class="elementor-update-preview-button-wrapper"><button class="elementor-update-preview-button elementor-button elementor-button-success">Apply</button></div></div>',
				'type'  => Controls_Manager::RAW_HTML,
			)
		);

		$element->add_control(
			'premium_tooltip_switcher',
			array(
				'label'        => __( 'Enable Global Tooltip', 'premium-addons-for-elementor' ),
				'type'         => Controls_Manager::SWITCHER,
				'render_type'  => 'template',
				'return_value' => 'yes',
				'prefix_class' => 'premium-global-tooltips-',
			)
		);

		$element->add_control(
			'pa_tooltip_target',
			array(
				'label'     => __( 'CSS Selector', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::TEXT,
				'dynamic'   => array(
					'active' => true,
				),
				'condition' => array(
					'premium_tooltip_switcher' => 'yes',
				),
			)
		);

		$element->add_control(
			'premium_tooltip_notice',
			array(
				'raw'             => __( '<strong>Please Note!:</strong><ul><li> It\'s not recommended to use <b>Premium Global Tooltips</b> on a parent and a child elements at the same time.</li></ul>', 'premium-addons-for-elementor' ),
				'type'            => Controls_Manager::RAW_HTML,
				'content_classes' => 'elementor-panel-alert elementor-panel-alert-info',
				'condition'       => array(
					'premium_tooltip_switcher' => 'yes',
				),
			)
		);

		$element->start_controls_tabs(
			'premium_tooltip_tabs'
		);

		$this->add_tooltips_controls( $element );

		$this->add_tooltips_style_controls( $element );

		$this->add_tooltips_settings_controls( $element );

		$element->end_controls_tabs();

		$element->end_controls_section();

	}

	/**
	 * Add tooltips content controls.
	 *
	 * @access private
	 * @since 4.10.4
	 *
	 * @param object $element for current element.
	 */
	private function add_tooltips_controls( $element ) {

		$papro_activated = apply_filters( 'papro_activated', false );

		$element->start_controls_tab(
			'premium_tooltip_content_tab',
			array(
				'label'     => __( 'Content', 'premium-addons-for-elementor' ),
				'condition' => array(
					'premium_tooltip_switcher' => 'yes',
				),
			)
		);

		$element->add_control(
			'premium_tooltip_type',
			array(
				'label'       => __( 'Content', 'premium-addons-for-elementor' ),
				'type'        => Controls_Manager::SELECT,
				'render_type' => 'template',
				'options'     => array(
					'text'     => __( 'Text', 'premium-addons-for-elementor' ),
					'lottie'   => __( 'Lottie', 'premium-addons-for-elementor' ),
					'gallery'  => apply_filters( 'pa_pro_label', __( 'Gallery (PRO)', 'premium-addons-for-elementor' ) ),
					'template' => apply_filters( 'pa_pro_label', __( 'Elemenor Template (PRO)', 'premium-addons-for-elementor' ) ),
				),
				'default'     => 'text',
				'condition'   => array(
					'premium_tooltip_switcher' => 'yes',
				),
			)
		);

		$element->add_control(
			'premium_tooltip_text',
			array(
				'label'       => __( 'Content Text', 'premium-addons-for-elementor' ),
				'type'        => Controls_Manager::TEXTAREA,
				'render_type' => 'template',
				'default'     => __( 'Hi, I\'m a global tooltip.', 'premium-addons-for-elementor' ),
				'placeholder' => __( 'Hi, I\'m a global tooltip', 'premium-addons-for-elementor' ),
				'condition'   => array(
					'premium_tooltip_switcher' => 'yes',
					'premium_tooltip_type'     => 'text',
				),
			)
		);

		$element->add_control(
			'premium_tooltip_icon_switcher',
			array(
				'label'        => __( 'Add Icon', 'premium-addons-for-elementor' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'prefix_class' => 'premium-global-tooltip-',
				'render_type'  => 'template',
				'condition'    => array(
					'premium_tooltip_switcher' => 'yes',
					'premium_tooltip_type'     => 'text',
				),
			)
		);

		$element->add_control(
			'premium_tooltip_icon',
			array(
				'label'        => __( 'Icon', 'premium-addons-for-elementor' ),
				'type'         => Controls_Manager::ICONS,
				'default'      => array(
					'value'   => 'fas fa-star',
					'library' => 'fa-solid',
				),
				'return_value' => 'yes',
				'render_type'  => 'template',
				'condition'    => array(
					'premium_tooltip_switcher'      => 'yes',
					'premium_tooltip_type'          => 'text',
					'premium_tooltip_icon_switcher' => 'yes',
				),
			)
		);

		$element->add_control(
			'premium_tooltip_lottie_url',
			array(
				'label'       => __( 'Animation JSON URL', 'premium-addons-for-elementor' ),
				'type'        => Controls_Manager::TEXT,
				'dynamic'     => array( 'active' => true ),
				'description' => 'Get JSON code URL from <a href="https://lottiefiles.com/" target="_blank">here</a>',
				'label_block' => true,
				'render_type' => 'template',
				'condition'   => array(
					'premium_tooltip_switcher' => 'yes',
					'premium_tooltip_type'     => 'lottie',
				),
			)
		);

		$element->add_control(
			'premium_tooltip_lottie_loop',
			array(
				'label'        => __( 'Loop', 'premium-addons-for-elementor' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'true',
				'default'      => 'true',
				'render_type'  => 'template',
				'condition'    => array(
					'premium_tooltip_switcher' => 'yes',
					'premium_tooltip_type'     => 'lottie',
				),
			)
		);

		$element->add_control(
			'premium_tooltip_lottie_reverse',
			array(
				'label'        => __( 'Reverse', 'premium-addons-for-elementor' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'true',
				'render_type'  => 'template',
				'condition'    => array(
					'premium_tooltip_switcher' => 'yes',
					'premium_tooltip_type'     => 'lottie',
				),
			)
		);

		if ( ! $papro_activated ) {
			$get_pro = Helper_Functions::get_campaign_link( 'https://premiumaddons.com/pro', 'editor-page', 'wp-editor', 'get-pro' );

			$element->add_control(
				'pro_notice',
				array(
					'type'            => Controls_Manager::RAW_HTML,
					'raw'             => __( 'This option is available in Premium Addons Pro. ', 'premium-addons-for-elementor' ) . '<a href="' . esc_url( $get_pro ) . '" target="_blank">' . __( 'Upgrade now!', 'premium-addons-for-elementor' ) . '</a>',
					'content_classes' => 'papro-upgrade-notice',
					'condition'       => array(
						'premium_tooltip_switcher' => 'yes',
						'premium_tooltip_type'     => array( 'gallery', 'template' ),
					),
				)
			);

		} else {
			do_action( 'pa_tooltips_type_controls', $element );
		}

		$element->add_control(
			'hide_tooltip_on',
			array(
				'label'       => __( 'Hide Tooltip On', 'premium-addons-for-elementor' ),
				'type'        => Controls_Manager::SELECT2,
				'options'     => Helper_Functions::get_all_breakpoints(),
				'separator'   => 'before',
				'render'      => 'template',
				'multiple'    => true,
				'label_block' => true,
				'default'     => array(),
				'condition'   => array(
					'premium_tooltip_switcher' => 'yes',
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
	private function add_tooltips_style_controls( $element ) {

		/** Start Style Tab. */
		$element->start_controls_tab(
			'premium_tooltip_style_tab',
			array(
				'label'     => __( 'Style', 'premium-addons-for-elementor' ),
				'condition' => array(
					'premium_tooltip_switcher' => 'yes',
				),
			)
		);

		/** Content Style */
		$element->add_control(
			'premium_tooltip_content_heading',
			array(
				'label'     => __( 'Content', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
				'condition' => array(
					'premium_tooltip_switcher' => 'yes',
					'premium_tooltip_type!'    => 'template',
				),
			)
		);

		/**Text Style */
		$element->add_control(
			'premium_tooltip_text_color',
			array(
				'label'     => __( 'Text Color', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'.tooltipster-sidetip .tooltipster-box.tooltipster-box-{{ID}} .premium-tooltip-content-wrapper-{{ID}} .premium-tootltip-text' => 'color: {{VALUE}};',
				),
				'condition' => array(
					'premium_tooltip_switcher' => 'yes',
					'premium_tooltip_type'     => 'text',
				),
			)
		);

		$element->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'      => 'premium_tooltip_text_typo',
				'selector'  => '.tooltipster-box.tooltipster-box-{{ID}} .premium-tooltip-content-wrapper-{{ID}} .premium-tootltip-text',
				'condition' => array(
					'premium_tooltip_switcher' => 'yes',
					'premium_tooltip_type'     => 'text',
				),
			)
		);

		$element->add_group_control(
			Group_Control_Text_Shadow::get_type(),
			array(
				'name'      => 'premium_tooltip_text_shadow',
				'selector'  => '.tooltipster-box.tooltipster-box-{{ID}} .premium-tootltip-text',
				'condition' => array(
					'premium_tooltip_switcher' => 'yes',
					'premium_tooltip_type'     => 'text',
				),
			)
		);

		/**Icon Style */
		$element->add_control(
			'premium_tooltip_icon_heading',
			array(
				'label'     => __( 'Icon', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
				'condition' => array(
					'premium_tooltip_switcher'      => 'yes',
					'premium_tooltip_type'          => 'text',
					'premium_tooltip_icon_switcher' => 'yes',
				),
			)
		);

		$element->add_control(
			'premium_tooltip_icon_color',
			array(
				'label'     => __( 'Icon Color', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'.tooltipster-sidetip .tooltipster-box.tooltipster-box-{{ID}} .premium-tootltip-icon i' => 'color: {{VALUE}};',
					'.tooltipster-sidetip .tooltipster-box.tooltipster-box-{{ID}} .premium-tootltip-icon svg, .tooltipster-sidetip .tooltipster-box.tooltipster-box-{{ID}} .premium-tootltip-icon svg *' => 'fill: {{VALUE}};',
				),
				'condition' => array(
					'premium_tooltip_switcher'      => 'yes',
					'premium_tooltip_type'          => 'text',
					'premium_tooltip_icon_switcher' => 'yes',

				),
			)
		);

		$element->add_responsive_control(
			'premium_tooltip_icon_size',
			array(
				'label'     => __( 'Icon Size', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::SLIDER,
				'range'     => array(
					'px' => array(
						'min'  => 0,
						'max'  => 2000,
						'step' => 1,
					),
				),
				'selectors' => array(
					'.tooltipster-sidetip .tooltipster-box.tooltipster-box-{{ID}} .premium-tootltip-icon i' => 'font-size: {{SIZE}}px;',
					'.tooltipster-sidetip .tooltipster-box.tooltipster-box-{{ID}} .premium-tootltip-icon svg' => 'width: {{SIZE}}px; height:{{SIZE}}px',
				),
				'condition' => array(
					'premium_tooltip_switcher'      => 'yes',
					'premium_tooltip_type'          => 'text',
					'premium_tooltip_icon_switcher' => 'yes',
				),
			)
		);

		$element->add_group_control(
			Group_Control_Text_Shadow::get_type(),
			array(
				'name'      => 'premium_tooltip_icon_shadow',
				'selector'  => '.tooltipster-box.tooltipster-box-{{ID}} .premium-tootltip-icon',
				'condition' => array(
					'premium_tooltip_switcher'       => 'yes',
					'premium_tooltip_icon_switcher'  => 'yes',
					'premium_tooltip_type'           => 'text',
					'premium_tooltip_icon[library]!' => 'svg',
				),
			)
		);

		/** Gallery Style */
		$element->add_responsive_control(
			'premium_tooltip_img_size',
			array(
				'label'     => __( 'Size (PX)', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::SLIDER,
				'range'     => array(
					'px' => array(
						'min'  => 0,
						'max'  => 2000,
						'step' => 1,
					),
				),
				'default'   => array(
					'unit' => 'px',
					'size' => 100,
				),
				'selectors' => array(
					'.tooltipster-sidetip .tooltipster-box.tooltipster-box-{{ID}} .premium-tooltip-gallery,
                    .tooltipster-sidetip .tooltipster-box.tooltipster-box-{{ID}} .premium-tooltip-content-wrapper-{{ID}},
                    .tooltipster-sidetip .tooltipster-box.tooltipster-box-{{ID}} .premium-tooltip-lottie ' => 'width: {{SIZE}}px; height:{{SIZE}}px',
				),
				'condition' => array(
					'premium_tooltip_switcher' => 'yes',
					'premium_tooltip_type'     => array( 'gallery', 'lottie' ),
				),
			)
		);

		$element->add_responsive_control(
			'premium_tooltip_img_fit',
			array(
				'label'     => __( 'Image Fit', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::SELECT,
				'options'   => array(
					'cover'   => __( 'Cover', 'premium-addons-for-elementor' ),
					'fill'    => __( 'Fill', 'premium-addons-for-elementor' ),
					'contain' => __( 'Contain', 'premium-addons-for-elementor' ),
				),
				'default'   => 'cover',
				'selectors' => array(
					'.tooltipster-sidetip .tooltipster-box.tooltipster-box-{{ID}} .premium-tooltip-gallery img' => 'object-fit: {{VALUE}};',
				),
				'condition' => array(
					'premium_tooltip_switcher' => 'yes',
					'premium_tooltip_type'     => 'gallery',
				),
			)
		);

		/**Start Container Style */
		$element->add_control(
			'premium_tooltip_container_heading',
			array(
				'label'     => __( 'Container', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
				'condition' => array(
					'premium_tooltip_switcher' => 'yes',
				),
			)
		);

		$element->add_group_control(
			Group_Control_Background::get_type(),
			array(
				'name'      => 'premium_tooltip_container_bg',
				'types'     => array( 'classic', 'gradient' ),
				'condition' => array(
					'premium_tooltip_switcher' => 'yes',
				),
				'selector'  => '.tooltipster-box.tooltipster-box-{{ID}}',
			)
		);

		$element->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'     => 'premium_tooltip_container_border',
				'selector' => '.tooltipster-box.tooltipster-box-{{ID}}',
			)
		);

		$element->add_control(
			'premium_tooltip_container_border_radius',
			array(
				'label'      => __( 'Border Radius', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'.tooltipster-box.tooltipster-box-{{ID}}'   => 'border-radius: {{SIZE}}{{UNIT}}',
				),
			)
		);

		$element->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name'     => 'premium_tooltip_container_box_shadow',
				'selector' => '.tooltipster-sidetip .tooltipster-box.tooltipster-box-{{ID}}',
			)
		);

		$element->add_responsive_control(
			'premium_tooltip_container_padding',
			array(
				'label'      => __( 'Padding', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'.tooltipster-sidetip div.tooltipster-box-{{ID}} ' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		/** Arrow Style */
		$element->add_control(
			'premium_tooltip_arrow_heading',
			array(
				'label'     => __( 'Arrow', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
				'condition' => array(
					'premium_tooltip_switcher' => 'yes',
					'premium_tooltip_arrow'    => 'yes',
				),
			)
		);

		$element->add_control(
			'premium_tooltip_arrow_color',
			array(
				'label'     => __( 'Arrow Color', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'.premium-tooltipster-base.tooltipster-top .tooltipster-arrow-{{ID}} .tooltipster-arrow-background' => 'border-top-color: {{VALUE}};',
					'.premium-tooltipster-base.tooltipster-bottom .tooltipster-arrow-{{ID}} .tooltipster-arrow-background' => 'border-bottom-color: {{VALUE}};',
					'.premium-tooltipster-base.tooltipster-right .tooltipster-arrow-{{ID}} .tooltipster-arrow-background' => 'border-right-color: {{VALUE}};',
					'.premium-tooltipster-base.tooltipster-left .tooltipster-arrow-{{ID}} .tooltipster-arrow-background' => 'border-left-color: {{VALUE}};',
				),
				'condition' => array(
					'premium_tooltip_switcher' => 'yes',
					'premium_tooltip_arrow'    => 'yes',
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
	private function add_tooltips_settings_controls( $element ) {

		$element->start_controls_tab(
			'premium_tooltip_settings_tab',
			array(
				'label'     => __( 'Settings', 'premium-addons-for-elementor' ),
				'condition' => array(
					'premium_tooltip_switcher' => 'yes',
				),
			)
		);

		$element->add_control(
			'premium_tooltip_mouse_follow',
			array(
				'label'       => apply_filters( 'pa_pro_label', __( 'Mouse Follow (PRO)', 'premium-addons-for-elementor' ) ),
				'type'        => Controls_Manager::SWITCHER,
				'render_type' => 'template',
				'condition'   => array(
					'premium_tooltip_switcher' => 'yes',
				),
			)
		);

		$element->add_control(
			'premium_tooltip_interactive',
			array(
				'label'       => __( 'Interactive', 'premium-addons-for-elementor' ),
				'type'        => Controls_Manager::SWITCHER,
				'description' => __( 'Give users the possibility to interact with the content of the tooltip', 'premium-addons-for-elementor' ),
				'default'     => 'yes',
				'render_type' => 'template',
				'condition'   => array(
					'premium_tooltip_switcher' => 'yes',
				),
			)
		);

		$element->add_control(
			'premium_tooltip_arrow',
			array(
				'label'       => __( 'Show Arrow', 'premium-addons-for-elementor' ),
				'type'        => Controls_Manager::SWITCHER,
				'label_on'    => __( 'Show', 'premium-addons-for-elementor' ),
				'label_off'   => __( 'Hide', 'premium-addons-for-elementor' ),
				'render_type' => 'template',
				'condition'   => array(
					'premium_tooltip_switcher' => 'yes',
				),
			)
		);

		$element->add_control(
			'premium_tooltip_trigger',
			array(
				'label'       => __( 'Trigger', 'premium-addons-for-elementor' ),
				'type'        => Controls_Manager::SELECT,
				'options'     => array(
					'click'    => __( 'Click', 'premium-addons-for-elementor' ),
					'hover'    => __( 'Hover', 'premium-addons-for-elementor' ),
					'viewport' => __( 'On Viewport', 'premium-addons-for-elementor' ),
				),
				'default'     => 'hover',
				'render_type' => 'template',
				'condition'   => array(
					'premium_tooltip_switcher' => 'yes',
				),
			)
		);

		$element->add_control(
			'premium_tooltip_position',
			array(
				'label'       => __( 'Positon', 'premium-addons-for-elementor' ),
				'type'        => Controls_Manager::TEXT,
				'description' => __( 'Sets the side of the tooltip. The value may one of the following: \'top\', \'bottom\', \'left\', \'right\'. It may also be an array containing one or more of these values. When using an array, the order of values is taken into account as order of fallbacks and the absence of a side disables it', 'premium-addons-for-elementor' ),
				'default'     => 'top,bottom',
				'label_block' => true,
				'render_type' => 'template',
				'condition'   => array(
					'premium_tooltip_switcher' => 'yes',
				),
			)
		);

		$element->add_control(
			'premium_tooltip_distance_position',
			array(
				'label'       => __( 'Spacing', 'premium-addons-for-elementor' ),
				'type'        => Controls_Manager::NUMBER,
				'title'       => __( 'The distance between the origin and the tooltip in pixels, default is 6', 'premium-addons-for-elementor' ),
				'default'     => 6,
				'render_type' => 'template',
				'condition'   => array(
					'premium_tooltip_switcher' => 'yes',
				),
			)
		);

		$element->add_responsive_control(
			'premium_tooltip_min_width',
			array(
				'label'       => __( 'Min Width', 'premium-addons-for-elementor' ),
				'type'        => Controls_Manager::SLIDER,
				'render_type' => 'template',
				'range'       => array(
					'px' => array(
						'min' => 0,
						'max' => 800,
					),
				),
				'description' => __( 'Set a minimum width for the tooltip in pixels, default: 0 (auto width)', 'premium-addons-for-elementor' ),
				'condition'   => array(
					'premium_tooltip_switcher' => 'yes',
				),
			)
		);

		$element->add_responsive_control(
			'premium_tooltip_max_width',
			array(
				'label'       => __( 'Max Width', 'premium-addons-for-elementor' ),
				'type'        => Controls_Manager::SLIDER,
				'render_type' => 'template',
				'range'       => array(
					'px' => array(
						'min' => 0,
						'max' => 800,
					),
				),
				'description' => __( 'Set a maximum width for the tooltip in pixels, default: null (no max width)', 'premium-addons-for-elementor' ),
				'condition'   => array(
					'premium_tooltip_switcher' => 'yes',
				),
			)
		);

		$element->add_responsive_control(
			'premium_tooltip_height',
			array(
				'label'       => __( 'Height', 'premium-addons-for-elementor' ),
				'type'        => Controls_Manager::SLIDER,
				'size_units'  => array( 'px', 'em', '%' ),
				'render_type' => 'template',
				'range'       => array(
					'px' => array(
						'min' => 0,
						'max' => 500,
					),
					'em' => array(
						'min' => 0,
						'max' => 20,
					),
				),
				'label_block' => true,
				'selectors'   => array(
					'.tooltipster-box.tooltipster-box-{{ID}} .tooltipster-content .premium-tooltip-content-wrapper-{{ID}} ' => 'height: {{SIZE}}{{UNIT}};',
				),
				'condition'   => array(
					'premium_tooltip_switcher' => 'yes',
				),
			)
		);

		$element->add_control(
			'premium_tooltip_anime',
			array(
				'label'       => __( 'Animation', 'premium-addons-for-elementor' ),
				'type'        => Controls_Manager::SELECT,
				'options'     => array(
					'fade'  => __( 'Fade', 'premium-addons-for-elementor' ),
					'grow'  => __( 'Grow', 'premium-addons-for-elementor' ),
					'swing' => __( 'Swing', 'premium-addons-for-elementor' ),
					'slide' => __( 'Slide', 'premium-addons-for-elementor' ),
					'fall'  => __( 'Fall', 'premium-addons-for-elementor' ),
				),
				'default'     => 'fade',
				'render_type' => 'template',
				'label_block' => true,
				'condition'   => array(
					'premium_tooltip_switcher' => 'yes',
				),
			)
		);

		$element->add_control(
			'premium_tooltip_anime_dur',
			array(
				'label'       => __( 'Animation Duration', 'premium-addons-for-elementor' ),
				'type'        => Controls_Manager::NUMBER,
				'title'       => __( 'Set the animation duration in milliseconds, default is 350', 'premium-addons-for-elementor' ),
				'default'     => 350,
				'render_type' => 'template',
				'condition'   => array(
					'premium_tooltip_switcher' => 'yes',
				),
			)
		);

		$element->add_control(
			'premium_tooltip_delay',
			array(
				'label'       => __( 'Delay', 'premium-addons-for-elementor' ),
				'type'        => Controls_Manager::NUMBER,
				'title'       => __( 'Set the animation delay in milliseconds, default is 10' ),
				'default'     => 10,
				'render_type' => 'template',
				'condition'   => array(
					'premium_tooltip_switcher' => 'yes',
				),
			)
		);

		$element->add_control(
			'pa_tooltip_zindex',
			array(
				'label'       => __( 'Z-Index', 'premium-addons-for-elementor' ),
				'type'        => Controls_Manager::NUMBER,
				'description' => __( 'Set the z-index of the tooltip. Default is 9999999' ),
				'render_type' => 'template',
				'condition'   => array(
					'premium_tooltip_switcher' => 'yes',
				),
			)
		);

		$element->end_controls_tab();
	}

	/**
	 * Render Global badge output in the editor.
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
			var isEnabled = 'yes' === settings.premium_tooltip_switcher ? true : false;

			if ( isEnabled ) {

				var type = settings.premium_tooltip_type,
					content = {};

					view.addRenderAttribute( 'gTooltipshtml', {
						id: 'tooltip_content-' + view.getID(),
						class: 'premium-global-tooltip-content premium-tooltip-content-wrapper-' + view.getID(),
					});

					view.addRenderAttribute( 'gTooltipTemp', {
						class: 'premium-gtooltips-temp premium-global-tooltips-wrapper-temp-' + view.getID(),
						style: 'display: none'
					});

					if ( 'template' === type ) {
                        var templateTitle = '' === settings.premium_tooltip_template ? settings.live_temp_content : settings.premium_tooltip_template;

						view.addRenderAttribute( 'gTooltipshtml', {
							'data-template-id': templateTitle,
						});
					}

				#>
					<div {{{ view.getRenderAttributeString( 'gTooltipTemp' ) }}} >
						<div {{{ view.getRenderAttributeString( 'gTooltipshtml' ) }}}>
							<#
								switch ( type ) {
									case 'text':
										#>
										<span class="premium-tootltip-text">
											{{{settings.premium_tooltip_text}}}
											<#
												if ( 'yes' === settings.premium_tooltip_icon_switcher ) {
													var tooltipIconHTML = elementor.helpers.renderIcon( view, settings.premium_tooltip_icon, { 'aria-hidden': true }, 'i' , 'object');

													view.addRenderAttribute( 'gTooltipsIconHolder', {
														class: 'premium-tootltip-icon',
													});
													#>
														<span {{{ view.getRenderAttributeString( 'gTooltipsIconHolder' ) }}} > {{{tooltipIconHTML.value}}}</span>
													<#
												}
											#>
										</span>
										<#
										break;

									case 'gallery':
										var gallery = settings.premium_tooltip_gallery;

										if( ! gallery[0] )
											break;

										content = gallery;
										view.addRenderAttribute( 'gTooltipsGallery', {
											src: gallery[0]['url'],
										});
										#>
										<span class="premium-tooltip-gallery"><img {{{ view.getRenderAttributeString( 'gTooltipsGallery' ) }}}></span>
										<#
										break;

									case 'lottie':
										view.addRenderAttribute( 'gTooltipsLottie', {
											class: 'premium-lottie-animation premium-tooltip-lottie',
											'data-lottie-url': settings.premium_tooltip_lottie_url,
											'data-lottie-loop': settings.premium_tooltip_lottie_loop,
											'data-lottie-reverse': settings.premium_tooltip_lottie_reverse,
										});
										#>
											<div {{{ view.getRenderAttributeString( 'gTooltipsLottie' ) }}} ></div>
										<#
										break;
								}
							#>
						</div>
					</div>
				<#

				var maxWidth = {
					desktop: '' !== settings.premium_tooltip_max_width.size ? settings.premium_tooltip_max_width.size : null,
					mobile: '' !== settings.premium_tooltip_max_width_mobile.size ? settings.premium_tooltip_max_width_mobile.size : null,
					tablet: '' !== settings.premium_tooltip_max_width_tablet.size ? settings.premium_tooltip_max_width_tablet.size : null,
				},
				minWidth = {
					desktop: '' !== settings.premium_tooltip_min_width.size ? settings.premium_tooltip_min_width.size : 0,
					mobile: '' !== settings.premium_tooltip_min_width_mobile.size ? settings.premium_tooltip_min_width_mobile.size : 0,
					tablet: '' !== settings.premium_tooltip_min_width_tablet.size ? settings.premium_tooltip_min_width_tablet.size : 0,
				},
				tooltip_settings = {
					type: type,
					content: content,
					minWidth: minWidth,
					maxWidth: maxWidth,
					zindex: settings.pa_tooltip_zindex,
					target: settings.pa_tooltip_target,
					anime: settings.premium_tooltip_anime,
					trigger: settings.premium_tooltip_trigger,
					side: settings.premium_tooltip_position,
					follow_mouse: '' === settings.pa_tooltip_class && 'yes' === settings.premium_tooltip_mouse_follow,
					arrow: 'yes' === settings.premium_tooltip_arrow ? true : false,
					distance: '' !== settings.premium_tooltip_distance_position ? settings.premium_tooltip_distance_position : 6,
					duration: '' !== settings.premium_tooltip_anime_dur ? settings.premium_tooltip_anime_dur : 350,
					delay: '' !== settings.premium_tooltip_anime_delay ? settings.premium_tooltip_anime_delay : 10,
					hideOn: settings.hide_tooltip_on,
					uniqueClass: settings.pa_tooltip_class
				};

				tooltip_settings.interactive = tooltip_settings.follow_mouse ? false : 'yes' === settings.premium_tooltip_interactive;


				if ( '' !== settings.pa_tooltip_class ) {
					tooltip_settings.isTourStarter = 'yes' === settings.is_tour_starter;
				}

				view.addRenderAttribute( 'gTooltips', {
					'id': 'premium-global-tooltips-' + view.getID(),
					'class': 'premium-global-tooltips-wrapper',
					'data-tooltip_settings': JSON.stringify( tooltip_settings )
				});
		#>
				<div {{{ view.getRenderAttributeString( 'gTooltips' ) }}}></div>
		<#
			}
		#>

		<?php

			$slider_content = ob_get_contents();
			ob_end_clean();
			$template = $slider_content . $old_template;
			return $template;
	}

	/**
	 * Render Global Tooltip output on the frontend.
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

		$papro_activated = apply_filters( 'papro_activated', false );

		if ( ! $papro_activated && ( 'gallery' === $settings['premium_tooltip_type'] || 'template' === $settings['premium_tooltip_type'] || 'yes' === $settings['premium_tooltip_mouse_follow'] ) ) {
			return;
		}

		$tooltips_enabled = $settings['premium_tooltip_switcher'];

		if ( 'yes' === $tooltips_enabled ) {
			$type    = $settings['premium_tooltip_type'];
			$content = '';

			?>
				<div class="premium-gtooltips-temp premium-global-tooltips-wrapper-temp-<?php echo esc_attr( $id ); ?>" style="display: none;">
					<div id="tooltip_content-<?php echo esc_attr( $id ); ?>" class="premium-global-tooltip-content premium-tooltip-content-wrapper-<?php echo esc_attr( $id ); ?>">
						<?php
						switch ( $type ) {

							case 'text':
								?>
									<span class="premium-tootltip-text">
									<?php
										echo esc_html( $settings['premium_tooltip_text'] );

									if ( 'yes' === $settings['premium_tooltip_icon_switcher'] ) {
										$icon = $settings['premium_tooltip_icon'];
										?>
												<span class="premium-tootltip-icon">
											<?php
												Icons_Manager::render_icon(
													$icon,
													array(
														'aria-hidden' => 'true',
													)
												);
											?>
												</span>
											<?php
									}
									?>
									</span>
									<?php
								break;

							case 'gallery':
								$gallery = $settings['premium_tooltip_gallery'];
								$content = $gallery;
								?>
									<span class="premium-tooltip-gallery"><img src="<?php echo esc_url( $gallery[0]['url'] ); ?>"></span>
									<?php
								break;

							case 'lottie':
								$lottie = array(
									'url'     => esc_url( $settings['premium_tooltip_lottie_url'] ),
									'loop'    => $settings['premium_tooltip_lottie_loop'],
									'reverse' => $settings['premium_tooltip_lottie_reverse'],
								);

								?>
									<div class="premium-lottie-animation premium-tooltip-lottie" data-lottie-url="<?php echo esc_attr( $lottie['url'] ); ?>" data-lottie-loop="<?php echo esc_attr( $lottie['loop'] ); ?>" data-lottie-reverse="<?php echo esc_attr( $lottie['reverse'] ); ?>"></div>
									<?php
								break;

							default:
                                $content = empty( $settings['premium_tooltip_template'] ) ? $settings['live_temp_content'] : $settings['premium_tooltip_template'];
								echo $this->getTemplateInstance()->get_template_content( $content ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
						}
						?>
					</div>
				</div>
			<?php

			$min_width = array(
				'desktop' => ! empty( $settings['premium_tooltip_min_width']['size'] ) ? $settings['premium_tooltip_min_width']['size'] : 0,
				'mobile'  => ! empty( $settings['premium_tooltip_min_width_mobile']['size'] ) ? $settings['premium_tooltip_min_width_mobile']['size'] : 0,
				'tablet'  => ! empty( $settings['premium_tooltip_min_width_tablet']['size'] ) ? $settings['premium_tooltip_min_width_tablet']['size'] : 0,
			);

			$max_width = array(
				'desktop' => ! empty( $settings['premium_tooltip_max_width']['size'] ) ? $settings['premium_tooltip_max_width']['size'] : null,
				'mobile'  => ! empty( $settings['premium_tooltip_max_width_mobile']['size'] ) ? $settings['premium_tooltip_max_width_mobile']['size'] : null,
				'tablet'  => ! empty( $settings['premium_tooltip_max_width_tablet']['size'] ) ? $settings['premium_tooltip_max_width_tablet']['size'] : null,
			);

			$tooltip_settings = array(
				'type'         => $type,
				'content'      => $content,
				'min_width'    => $min_width,
				'max_width'    => $max_width,
				'zindex'       => $settings['pa_tooltip_zindex'],
				'target'       => $settings['pa_tooltip_target'],
				'anime'        => $settings['premium_tooltip_anime'],
				'trigger'      => $settings['premium_tooltip_trigger'],
				'side'         => $settings['premium_tooltip_position'],
				'follow_mouse' => empty( $settings['pa_tooltip_class'] ) && 'yes' === $settings['premium_tooltip_mouse_follow'],
				'arrow'        => 'yes' === $settings['premium_tooltip_arrow'],
				'distance'     => ! empty( $settings['premium_tooltip_distance_position'] ) ? $settings['premium_tooltip_distance_position'] : 6,
				'duration'     => ! empty( $settings['premium_tooltip_anime_dur'] ) ? $settings['premium_tooltip_anime_dur'] : 350,
				'delay'        => ! empty( $settings['premium_tooltip_anime_delay'] ) ? $settings['premium_tooltip_anime_delay'] : 10,
				'hideOn'       => $settings['hide_tooltip_on'],
				'uniqueClass'  => $settings['pa_tooltip_class'],
			);

			$tooltip_settings['interactive'] = $tooltip_settings['follow_mouse'] ? false : 'yes' === $settings['premium_tooltip_interactive'];

			if ( ! empty( $settings['pa_tooltip_class'] ) ) {
				$tooltip_settings['isTourStarter'] = 'yes' === $settings['is_tour_starter'];
			}

			$element->add_render_attribute( '_wrapper', 'data-tooltip_settings', wp_json_encode( $tooltip_settings ) );

			if ( 'widget' === $element_type && \Elementor\Plugin::instance()->editor->is_edit_mode() ) {
				?>
				<div id='premium-global-tooltips-temp-<?php echo esc_html( $id ); ?>' data-tooltip_settings='<?php echo wp_json_encode( $tooltip_settings ); ?>'></div>
				<?php
			}
		}
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

		if ( 'yes' === $element->get_settings_for_display( 'premium_tooltip_switcher' ) ) {

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
