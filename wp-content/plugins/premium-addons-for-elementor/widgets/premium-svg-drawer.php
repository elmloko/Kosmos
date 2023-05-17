<?php
/**
 * Class: Premium_SVG_Drawer
 * Name: SVG Draw
 * Slug: premium-svg-drawer
 */

namespace PremiumAddons\Widgets;

// Elementor Classes.
use Elementor\Widget_Base;
use Elementor\Icons_Manager;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Background;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Text_Shadow;

// PremiumAddons Classes.
use PremiumAddons\Includes\Helper_Functions;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class Premium_SVG_Drawer
 */
class Premium_SVG_Drawer extends Widget_Base {

	/**
	 * Retrieve Widget Name.
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function get_name() {
		return 'premium-svg-drawer';
	}

	/**
	 * Retrieve Widget Title.
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function get_title() {
		return __( 'SVG Draw', 'premium-addons-for-elementor' );
	}

	/**
	 * Retrieve Widget Icon.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return string widget icon.
	 */
	public function get_icon() {
		return 'pa-pro-svg-drawer';
	}

	/**
	 * Retrieve Widget Categories.
	 *
	 * @since 1.5.1
	 * @access public
	 *
	 * @return array Widget categories.
	 */
	public function get_categories() {
		return array( 'premium-elements' );
	}

	/**
	 * Retrieve Widget Keywords.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return string Widget keywords.
	 */
	public function get_keywords() {
		return array( 'pa', 'premium', 'icon', 'animate', 'custom', 'library', 'animation' );
	}

	/**
	 * Retrieve Widget Dependent CSS.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return array CSS script handles.
	 */
	public function get_style_depends() {
		return array(
			'premium-pro',
		);
	}

	/**
	 * Retrieve Widget Dependent JS.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return array JS script handles.
	 */
	public function get_script_depends() {
		return array(
			'pa-tweenmax',
			'pa-scrolltrigger',
			'pa-gsap',
			'premium-addons',
			'pa-fontawesome-all',
			'pa-motionpath',
		);
	}

	/**
	 * Retrieve Widget Support URL.
	 *
	 * @access public
	 *
	 * @return string support URL.
	 */
	public function get_custom_help_url() {
		return 'https://premiumaddons.com/support/';
	}

	/**
	 * Register SVG Draw controls.
	 *
	 * @since 1.0.0
	 * @access protected
	 */
	protected function register_controls() { // phpcs:ignore PSR2.Methods.MethodDeclaration.Underscore

		$this->start_controls_section(
			'general_section',
			array(
				'label' => __( 'General', 'premium-addons-for-elementor' ),
			)
		);

		$this->add_control(
			'icon_type',
			array(
				'label'       => __( 'SVG Type', 'premium-addons-for-elementor' ),
				'type'        => Controls_Manager::SELECT,
				'options'     => array(
					'icon'   => __( 'Font Awesome Icon', 'premium-addons-for-elementor' ),
					'custom' => __( 'Custom SVG', 'premium-addons-for-elementor' ),
				),
				'default'     => 'icon',
				'label_block' => true,
			)
		);

		$this->add_control(
			'font_icon',
			array(
				'label'                  => __( 'Select Icon', 'premium-addons-for-elementor' ),
				'type'                   => Controls_Manager::ICONS,
				'skin'                   => 'inline',
				'classes'                => 'editor-pa-icon-control',
				'exclude_inline_options' => array( 'svg' ),
				'default'                => array(
					'value'   => 'fas fa-star',
					'library' => 'fa-solid',
				),
				'label_block'            => false,
				'condition'              => array(
					'icon_type' => 'icon',
				),
			)
		);

		$this->add_control(
			'custom_svg',
			array(
				'label'       => __( 'SVG Code', 'premium-addons-for-elementor' ),
				'type'        => Controls_Manager::TEXTAREA,
				'description' => 'You can use these sites to create SVGs: <a href="https://danmarshall.github.io/google-font-to-svg-path/" target="_blank">Google Fonts</a> and <a href="https://boxy-svg.com/" target="_blank">Boxy SVG</a>',
				'condition'   => array(
					'icon_type' => 'custom',
				),
			)
		);

		$this->add_control(
			'custom_svg_note',
			array(
				'raw'             => __( 'Please note that your SVG code must contain a path element so it can be animated. For example, path, circle, square, etc.', 'premium-addons-for-elementor' ),
				'type'            => Controls_Manager::RAW_HTML,
				'content_classes' => 'elementor-panel-alert elementor-panel-alert-warning',
				'condition'       => array(
					'icon_type' => 'custom',
				),
			)
		);

		$this->add_responsive_control(
			'icon_width',
			array(
				'label'      => __( 'Width', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', 'em', '%', 'custom' ),
				'range'      => array(
					'px' => array(
						'min' => 1,
						'max' => 600,
					),
					'em' => array(
						'min' => 1,
						'max' => 30,
					),
				),
				'default'    => array(
					'size' => 100,
					'unit' => 'px',
				),
				'condition'  => array(
					'icon_type' => 'custom',
				),
				'selectors'  => array(
					'{{WRAPPER}} .premium-svg-draw svg' => 'width: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'icon_height',
			array(
				'label'      => __( 'Height', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', 'em', 'custom' ),
				'range'      => array(
					'px' => array(
						'min' => 1,
						'max' => 600,
					),
					'em' => array(
						'min' => 1,
						'max' => 30,
					),
				),
				'default'    => array(
					'size' => 100,
					'unit' => 'px',
				),
				'condition'  => array(
					'icon_type' => 'custom',
				),
				'selectors'  => array(
					'{{WRAPPER}} .premium-svg-draw svg' => 'height: {{SIZE}}{{UNIT}}',
				),
			)
		);

		$this->add_responsive_control(
			'icon_size',
			array(
				'label'      => __( 'Size', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', 'em' ),
				'range'      => array(
					'px' => array(
						'min' => 1,
						'max' => 500,
					),
					'em' => array(
						'min' => 1,
						'max' => 30,
					),
				),
				'default'    => array(
					'size' => 250,
					'unit' => 'px',
				),
				'condition'  => array(
					'icon_type' => 'icon',
				),
				'selectors'  => array(
					'{{WRAPPER}} .premium-svg-draw svg' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}}',
				),
			)
		);

		$this->add_responsive_control(
			'icon_align',
			array(
				'label'     => __( 'Alignment', 'premuim_elementor' ),
				'type'      => Controls_Manager::CHOOSE,
				'options'   => array(
					'left'   => array(
						'title' => __( 'Left', 'premium-addons-for-elementor' ),
						'icon'  => 'eicon-text-align-left',
					),
					'center' => array(
						'title' => __( 'Center', 'premium-addons-for-elementor' ),
						'icon'  => 'eicon-text-align-center',
					),
					'right'  => array(
						'title' => __( 'Right', 'premium-addons-for-elementor' ),
						'icon'  => 'eicon-text-align-right',
					),
				),
				'default'   => 'center',
				'selectors' => array(
					'{{WRAPPER}} .premium-svg-draw' => 'text-align: {{VALUE}};',
				),
				'toggle'    => false,
			)
		);

		$this->add_control(
			'animate_icon',
			array(
				'label'        => __( 'Draw SVG', 'premium-addons-for-elementor' ),
				'type'         => Controls_Manager::SWITCHER,
				'prefix_class' => 'premium-svg-animated-',
				'render_type'  => 'template',
				'separator'    => 'before',
				'default'      => 'yes',
				'condition'    => array(
					'magic_scroll!' => 'yes',
				),
			)
		);

		$this->add_control(
			'animation_reverse',
			array(
				'label'        => __( 'Reverse Animation', 'premium-addons-for-elementor' ),
				'type'         => Controls_Manager::SWITCHER,
				'prefix_class' => 'premium-svg-anim-rev-',
				'render_type'  => 'template',
				'separator'    => 'before',
				'condition'    => array(
					'animate_icon'  => 'yes',
					'magic_scroll!' => 'yes',
				),
			)
		);

		$this->add_control(
			'animate_start_point',
			array(
				'label'              => __( 'Start Point (%)', 'premium-addons-for-elementor' ),
				'type'               => Controls_Manager::SLIDER,
				'description'        => __( 'Set the point that the SVG should start from.', 'premium-addons-for-elementor' ),
				'default'            => array(
					'unit' => '%',
					'size' => 0,
				),
				'condition'          => array(
					'animate_icon'       => 'yes',
					'magic_scroll!'      => 'yes',
					'animation_reverse!' => 'yes',
				),
				'frontend_available' => true,
			)
		);

		$this->add_control(
			'animate_end_point',
			array(
				'label'              => __( 'End Point (%)', 'premium-addons-for-elementor' ),
				'type'               => Controls_Manager::SLIDER,
				'description'        => __( 'Set the point that the SVG should end at.', 'premium-addons-for-elementor' ),
				'default'            => array(
					'unit' => '%',
					'size' => 0,
				),
				'condition'          => array(
					'animate_icon'      => 'yes',
					'magic_scroll!'     => 'yes',
					'animation_reverse' => 'yes',
				),
				'frontend_available' => true,
			)
		);

		$this->add_control(
			'scroll_action',
			array(
				'label'              => __( 'How this animation should work?', 'premium-addons-for-elementor' ),
				'type'               => Controls_Manager::SELECT,
				'options'            => array(
					'automatic' => __( 'Complete Draw When Visible On Viewport', 'premium-addons-for-elementor' ),
					'viewport'  => __( 'Draw On Scroll', 'premium-addons-for-elementor' ),
					'hover'     => __( 'Draw On Hover', 'premium-addons-for-elementor' ),
				),
				'default'            => 'viewport',
				'label_block'        => true,
				'condition'          => array(
					'animate_icon'  => 'yes',
					'magic_scroll!' => 'yes',
				),
				'frontend_available' => true,
			)
		);

		$this->add_control(
			'frames',
			array(
				'label'              => __( 'Speed', 'premium-addons-for-elementor' ),
				'type'               => Controls_Manager::NUMBER,
				'description'        => __( 'Larger value means longer drawing duration', 'premium-addons-for-elementor' ),
				'default'            => 5,
				'min'                => 1,
				'max'                => 100,
				'condition'          => array(
					'animate_icon'   => 'yes',
					'scroll_action!' => 'viewport',
					'magic_scroll!'  => 'yes',
				),
				'frontend_available' => true,
			)
		);

		$this->add_control(
			'loop',
			array(
				'label'              => __( 'Loop', 'premium-addons-for-elementor' ),
				'type'               => Controls_Manager::SWITCHER,
				'condition'          => array(
					'animate_icon'   => 'yes',
					'scroll_action!' => 'viewport',
					'magic_scroll!'  => 'yes',
				),
				'return_value'       => 'true',
				'frontend_available' => true,
			)
		);

		$this->add_control(
			'yoyo',
			array(
				'label'              => __( 'Yoyo Effect', 'premium-addons-for-elementor' ),
				'type'               => Controls_Manager::SWITCHER,
				'condition'          => array(
					'animate_icon'   => 'yes',
					'scroll_action!' => 'viewport',
					'magic_scroll!'  => 'yes',
					'loop'           => 'true',
				),
				'frontend_available' => true,
			)
		);

		$this->add_control(
			'animate_trigger',
			array(
				'label'              => __( 'When the draw should start?', 'premium-addons-for-elementor' ),
				'type'               => Controls_Manager::SELECT,
				'options'            => array(
					'top'    => __( 'Top of Viewport Hits The Widget', 'premium-addons-for-elementor' ),
					'center' => __( 'Center of Viewport Hits The Widget', 'premium-addons-for-elementor' ),
					'custom' => __( 'Custom Offset', 'premium-addons-for-elementor' ),
				),
				'default'            => 'center',
				'label_block'        => true,
				'condition'          => array(
					'animate_icon'  => 'yes',
					'scroll_action' => 'automatic',
					'magic_scroll!' => 'yes',
				),
				'frontend_available' => true,
			)
		);

		$this->add_control(
			'animate_offset',
			array(
				'label'              => __( 'Offset (%)', 'premium-addons-for-elementor' ),
				'type'               => Controls_Manager::SLIDER,
				'default'            => array(
					'size' => 50,
					'unit' => '%',
				),
				'frontend_available' => true,
				'conditions'         => array(
					'terms' => array(
						array(
							'name'  => 'animate_icon',
							'value' => 'yes',
						),
						array(
							'name'     => 'magic_scroll',
							'operator' => '!=',
							'value'    => 'yes',
						),
						array(
							'name'     => 'scroll_action',
							'operator' => '!=',
							'value'    => 'hover',
						),
						array(
							'relation' => 'or',
							'terms'    => array(
								array(
									'name'  => 'scroll_action',
									'value' => 'viewport',
								),
								array(
									'name'  => 'animate_trigger',
									'value' => 'custom',
								),
							),
						),
					),
				),
			)
		);

		$this->add_control(
			'draw_speed',
			array(
				'label'              => __( 'Decrease Draw Speed By', 'premium-addons-for-elementor' ),
				'type'               => Controls_Manager::SLIDER,
				'description'        => __( 'The larger the value you set the slower the SVG is drawn.', 'premium-addons-for-elementor' ),
				'range'              => array(
					'px' => array(
						'min'  => 0,
						'max'  => 1,
						'step' => 0.1,
					),
				),
				'default'            => array(
					'size' => 0.3,
					'unit' => 'px',
				),
				'condition'          => array(
					'animate_icon'  => 'yes',
					'scroll_action' => 'viewport',
					'magic_scroll!' => 'yes',
				),
				'frontend_available' => true,
			)
		);

		$this->add_control(
			'animation_sync',
			array(
				'label'        => __( 'Draw All Paths Together', 'premium-addons-for-elementor' ),
				'type'         => Controls_Manager::SWITCHER,
				'prefix_class' => 'premium-svg-sync-',
				'separator'    => 'before',
				'render_type'  => 'template',
				'condition'    => array(
					'animate_icon'  => 'yes',
					'magic_scroll!' => 'yes',
				),
			)
		);

		$this->add_control(
			'anim_rev',
			array(
				'label'              => __( 'Restart Animation on Scroll Up', 'premium-addons-for-elementor' ),
				'type'               => Controls_Manager::SWITCHER,
				'render_type'        => 'template',
				'default'            => 'yes',
				'condition'          => array(
					'animate_icon'  => 'yes',
					'scroll_action' => 'automatic',
					'magic_scroll!' => 'yes',
				),
				'frontend_available' => true,
			)
		);

		$this->add_control(
			'svg_fill',
			array(
				'label'              => __( 'Fill Color After Draw', 'premium-addons-for-elementor' ),
				'type'               => Controls_Manager::SWITCHER,
				'condition'          => array(
					'animate_icon'  => 'yes',
					'magic_scroll!' => 'yes',
				),
				'frontend_available' => true,
			)
		);

		$this->add_control(
			'link',
			array(
				'label'       => __( 'Link', 'premium-addons-for-elementor' ),
				'type'        => Controls_Manager::URL,
				'dynamic'     => array( 'active' => true ),
				'placeholder' => 'https://premiumaddons.com/',
				'label_block' => true,
				'separator'   => 'before',
			)
		);

		$this->add_control(
			'magic_scroll',
			array(
				'label'       => __( 'Use With Magic Scroll', 'premium-addons-for-elementor' ),
				'type'        => Controls_Manager::SWITCHER,
				'description' => __( 'Enable this option if you want to draw the SVG using ', 'premium-addons-for-elementor' ) . '<a href="https://premiumaddons.com/elementor-magic-scroll-global-addon/" target="_blank">Magic Scroll addon.</a>',
				'render_type' => 'template',
				'separator'   => 'before',
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_pa_docs',
			array(
				'label' => __( 'Helpful Documentations', 'premium-addons-for-elementor' ),
			)
		);

		$docs = array(
			'https://premiumaddons.com/docs/elementor-svg-draw-widget/' => __( 'Getting started »', 'premium-addons-for-elementor' ),
		);

		$doc_index = 1;
		foreach ( $docs as $url => $title ) {

			$doc_url = Helper_Functions::get_campaign_link( $url, 'editor-page', 'wp-editor', 'get-support' );

			$this->add_control(
				'doc_' . $doc_index,
				array(
					'type'            => Controls_Manager::RAW_HTML,
					'raw'             => sprintf( '<a href="%s" target="_blank">%s</a>', $doc_url, $title ),
					'content_classes' => 'editor-pa-doc',
				)
			);

			$doc_index++;

		}

		$this->end_controls_section();

		/*Start Icon Style*/
		$this->start_controls_section(
			'icon_style',
			array(
				'label' => __( 'Icon', 'premium-addons-for-elementor' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_control(
			'icon_color',
			array(
				'label'     => __( 'Stroke Color', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#6EC1E4',
				'selectors' => array(
					'{{WRAPPER}} .premium-svg-draw svg'   => 'color: {{VALUE}};',
					'{{WRAPPER}} .premium-svg-draw svg *' => 'stroke: {{VALUE}}',
				),
			)
		);

		$this->add_control(
			'fill_color',
			array(
				'label'     => __( 'Fill Color', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .premium-svg-draw svg path, {{WRAPPER}} .premium-svg-draw svg circle, {{WRAPPER}} .premium-svg-draw svg square, {{WRAPPER}} .premium-svg-draw svg ellipse, {{WRAPPER}} .premium-svg-draw svg rect, {{WRAPPER}} .premium-svg-draw svg polyline, {{WRAPPER}} .premium-svg-draw svg line' => 'fill: {{VALUE}}',
				),
			)
		);

		$this->add_control(
			'svg_stroke',
			array(
				'label'              => __( 'After Draw Stroke Color', 'premium-addons-for-elementor' ),
				'type'               => Controls_Manager::COLOR,
				'global'             => false,
				'condition'          => array(
					'animate_icon'  => 'yes',
					'svg_fill'      => 'yes',
					'magic_scroll!' => 'yes',
				),
				'frontend_available' => true,
			)
		);

		$this->add_control(
			'svg_color',
			array(
				'label'              => __( 'After Draw Fill Color', 'premium-addons-for-elementor' ),
				'type'               => Controls_Manager::COLOR,
				'global'             => false,
				'condition'          => array(
					'animate_icon'  => 'yes',
					'svg_fill'      => 'yes',
					'magic_scroll!' => 'yes',
				),
				'frontend_available' => true,
			)
		);

		$this->add_responsive_control(
			'path_width',
			array(
				'label'     => __( 'Path Thickness', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::SLIDER,
				'range'     => array(
					'px' => array(
						'min'  => 0,
						'max'  => 20,
						'step' => 0.1,
					),
				),
				'default'   => array(
					'size' => 3,
					'unit' => 'px',
				),
				// 'condition' => array(
				// 'icon_type' => 'custom',
				// ),
				'selectors' => array(
					'{{WRAPPER}} .premium-svg-draw svg path, {{WRAPPER}} .premium-svg-draw svg circle, {{WRAPPER}} .premium-svg-draw svg square, {{WRAPPER}} .premium-svg-draw svg ellipse, {{WRAPPER}} .premium-svg-draw svg rect, {{WRAPPER}} .premium-svg-draw svg polyline, {{WRAPPER}} .premium-svg-draw svg line' => 'stroke-width: {{SIZE}}',
				),
			)
		);

		$this->add_control(
			'path_dashes',
			array(
				'label'     => __( 'Space Between Dashes', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::SLIDER,
				'range'     => array(
					'px' => array(
						'min'  => 0,
						'max'  => 10,
						'step' => 0.1,
					),
				),
				'condition' => array(
					'animate_icon!' => 'yes',
				),
				'selectors' => array(
					'{{WRAPPER}} .premium-svg-draw svg path, {{WRAPPER}} .premium-svg-draw svg circle, {{WRAPPER}} .premium-svg-draw svg square, {{WRAPPER}} .premium-svg-draw svg ellipse, {{WRAPPER}} .premium-svg-draw svg rect, {{WRAPPER}} .premium-svg-draw svg polyline, {{WRAPPER}} .premium-svg-draw svg line' => 'stroke-dasharray: {{SIZE}}',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			array(
				'name'     => 'icon_background',
				'types'    => array( 'classic', 'gradient' ),
				'selector' => '{{WRAPPER}} .premium-svg-draw svg',
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'     => 'icon_box',
				'selector' => '{{WRAPPER}} .premium-svg-draw svg',
			)
		);

		$this->add_control(
			'icon_radius',
			array(
				'label'      => __( 'Border Radius', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', '%', 'em' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-svg-draw svg' => 'border-radius: {{SIZE}}{{UNIT}};',
				),
				'condition'  => array(
					'icon_adv_radius!' => 'yes',
				),
			)
		);

		$this->add_control(
			'icon_adv_radius',
			array(
				'label'       => __( 'Advanced Border Radius', 'premium-addons-for-elementor' ),
				'type'        => Controls_Manager::SWITCHER,
				'description' => __( 'Apply custom radius values. Get the radius value from ', 'premium-addons-for-elementor' ) . '<a href="https://9elements.github.io/fancy-border-radius/" target="_blank">here</a>',
			)
		);

		$this->add_control(
			'icon_adv_radius_value',
			array(
				'label'     => __( 'Border Radius', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::TEXT,
				'dynamic'   => array( 'active' => true ),
				'selectors' => array(
					'{{WRAPPER}} .premium-svg-draw svg' => 'border-radius: {{VALUE}};',
				),
				'condition' => array(
					'icon_adv_radius' => 'yes',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Text_Shadow::get_type(),
			array(
				'label'    => __( 'Shadow', 'premium-addons-for-elementor' ),
				'name'     => 'icon_shadow',
				'selector' => '{{WRAPPER}} .premium-svg-draw svg',
			)
		);

		$this->add_control(
			'blend_mode',
			array(
				'label'     => __( 'Blend Mode', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::SELECT,
				'options'   => array(
					''            => __( 'Normal', 'premium-addons-for-elementor' ),
					'multiply'    => 'Multiply',
					'screen'      => 'Screen',
					'overlay'     => 'Overlay',
					'darken'      => 'Darken',
					'lighten'     => 'Lighten',
					'color-dodge' => 'Color Dodge',
					'saturation'  => 'Saturation',
					'color'       => 'Color',
					'luminosity'  => 'Luminosity',
				),
				'selectors' => array(
					'{{WRAPPER}} .premium-svg-draw' => 'mix-blend-mode: {{VALUE}}',
				),
			)
		);

		$this->add_responsive_control(
			'icon_padding',
			array(
				'label'      => __( 'Padding', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-svg-draw svg' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_section();

	}

	/**
	 * Render SVG Draw widget output on the frontend.
	 *
	 * Written in PHP and used to generate the final HTML.
	 *
	 * @since 1.0.0
	 * @access protected
	 */
	protected function render() {

		$settings = $this->get_settings_for_display();

		$type = $settings['icon_type'];

		if ( ! empty( $settings['link']['url'] ) ) {
			$this->add_link_attributes( 'link', $settings['link'] );
		}

		?>

			<div class="premium-svg-draw elementor-invisible">

				<?php if ( ! empty( $settings['link']['url'] ) ) : ?>
					<a <?php echo wp_kses_post( $this->get_render_attribute_string( 'link' ) ); ?>>
				<?php endif; ?>

				<?php if ( 'icon' === $type ) : ?>

					<?php
					$this->add_render_attribute(
						'fa_icon',
						array(
							'id'          => 'premium-svg-icon-' . $this->get_id(),
							'class'       => array(
								'premium-svg-icon',
								$settings['font_icon']['value'],
							),
							'aria-hidden' => 'true',
							'data-start'  => 'manual',
						)
					);

					// Icons_Manager::render_icon(
					// $settings['font_icon'],
					// array(
					// 'class'       => array(
					// 'premium-svg-icon',
					// ),
					// 'aria-hidden' => 'true',
					// )
					// );
					?>
					<i <?php echo wp_kses_post( $this->get_render_attribute_string( 'fa_icon' ) ); ?>></i>
				<?php else : ?>

					<?php $this->print_unescaped_setting( 'custom_svg' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>

				<?php endif; ?>

				<?php if ( ! empty( $settings['link']['url'] ) ) : ?>
					</a>
				<?php endif; ?>

			</div>

		<?php

	}

}
