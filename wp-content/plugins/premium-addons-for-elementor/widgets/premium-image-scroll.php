<?php
/**
 * Premium Image Scroll.
 */

namespace PremiumAddons\Widgets;

// Elementor Classes.
use Elementor\Widget_Base;
use Elementor\Utils;
use Elementor\Control_Media;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Image_Size;
use Elementor\Group_Control_Css_Filter;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Text_Shadow;


// PremiumAddons Classes.
use PremiumAddons\Includes\Premium_Template_Tags;
use PremiumAddons\Includes\Helper_Functions;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class Premium_Image_Scroll
 */
class Premium_Image_Scroll extends Widget_Base {

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
		return $this->template_instance = Premium_Template_Tags::getInstance();
	}

	/**
	 * Retrieve Widget Name.
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function get_name() {
		return 'premium-image-scroll';
	}

	/**
	 * Retrieve Widget Title.
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function get_title() {
		return __( 'Image Scroll', 'premium-addons-for-elementor' );
	}

	/**
	 * Widget preview refresh button.
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function is_reload_preview_required() {
		return true;
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
		return 'pa-image-scroll';
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
		return array( 'pa', 'premium', 'link', 'cta', 'animation' );
	}

	/**
	 * Retrieve Widget Categories.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return string Widget Categories.
	 */
	public function get_categories() {
		return array( 'premium-elements' );
	}

	/**
	 * Retrieve Widget Dependent CSS.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return array CSS style handles.
	 */
	public function get_style_depends() {
		return array(
			'premium-addons',
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
			'imagesloaded',
			'premium-addons',
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
	 * Register Image Scroll controls.
	 *
	 * @since 1.0.0
	 * @access protected
	 */
	protected function register_controls() { // phpcs:ignore PSR2.Methods.MethodDeclaration.Underscore

		$this->start_controls_section(
			'general_settings',
			array(
				'label' => __( 'Image Settings', 'premium-addons-for-elementor' ),
			)
		);

		$this->add_control(
			'image',
			array(
				'label'       => __( 'Image', 'premium-addons-for-elementor' ),
				'type'        => Controls_Manager::MEDIA,
				'dynamic'     => array( 'active' => true ),
				'default'     => array(
					'url' => Utils::get_placeholder_image_src(),
				),
				'description' => __( 'Choose the scroll image', 'premium-addons-for-elementor' ),
				'label_block' => true,
			)
		);

		$this->add_group_control(
			Group_Control_Image_Size::get_type(),
			array(
				'name'      => 'thumbnail',
				'default'   => 'full',
				'separator' => 'none',
			)
		);

		$this->add_responsive_control(
			'image_height',
			array(
				'label'      => __( 'Image Height', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', 'em', 'vh', 'custom' ),
				'default'    => array(
					'unit' => 'px',
					'size' => 300,
				),
				'range'      => array(
					'px' => array(
						'min' => 200,
						'max' => 800,
					),
					'em' => array(
						'min' => 1,
						'max' => 50,
					),
				),
				'selectors'  => array(
					'{{WRAPPER}} .premium-image-scroll-container' => 'height: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'link_switcher',
			array(
				'label'       => __( 'Link', 'premium-addons-for-elementor' ),
				'type'        => Controls_Manager::SWITCHER,
				'description' => __( 'Add a custom link or select an existing page link', 'premium-addons-for-elementor' ),
			)
		);

		$this->add_control(
			'link_type',
			array(
				'label'       => __( 'Link/URL', 'premium-addons-for-elementor' ),
				'type'        => Controls_Manager::SELECT,
				'options'     => array(
					'url'  => __( 'URL', 'premium-addons-for-elementor' ),
					'link' => __( 'Existing Page', 'premium-addons-for-elementor' ),
				),
				'default'     => 'url',
				'condition'   => array(
					'link_switcher' => 'yes',
				),
				'label_block' => true,
			)
		);

		$this->add_control(
			'link',
			array(
				'label'       => __( 'URL', 'premium-addons-for-elementor' ),
				'type'        => Controls_Manager::URL,
				'dynamic'     => array( 'active' => true ),
				'placeholder' => 'https://premiumaddons.com/',
				'label_block' => true,
				'condition'   => array(
					'link_switcher' => 'yes',
					'link_type'     => 'url',
				),
			)
		);

		$this->add_control(
			'existing_page',
			array(
				'label'       => __( 'Existing Page', 'premium-addons-for-elementor' ),
				'type'        => Controls_Manager::SELECT2,
				'options'     => $this->getTemplateInstance()->get_all_posts(),
				'condition'   => array(
					'link_switcher' => 'yes',
					'link_type'     => 'link',
				),
				'label_block' => true,
			)
		);

		$this->add_control(
			'mask_image_scroll_switcher',
			array(
				'label'     => esc_html__( 'Mask Image Shape', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::SWITCHER,
				'separator' => 'before',
			)
		);

		$this->add_control(
			'mask_shape_image_scroll',
			array(
				'label'       => esc_html__( 'Mask Image', 'premium-addons-for-elementor' ),
				'type'        => Controls_Manager::MEDIA,
				'default'     => array(
					'url' => '',
				),
				'description' => esc_html__( 'Use PNG image with the shape you want to mask around feature image.', 'premium-addons-for-elementor' ),
				'selectors'   => array(
					'{{WRAPPER}} .premium-image-scroll-mask-media ' => 'mask-image: url("{{URL}}");-webkit-mask-image: url("{{URL}}");',
				),
				'condition'   => array(
					'mask_image_scroll_switcher' => 'yes',
				),
			)
		);

		$this->add_control(
			'mask_size',
			array(
				'label'     => __( 'Mask Size', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::SELECT,
				'options'   => array(
					'contain' => __( 'Contain', 'premium-addons-for-elementor' ),
					'cover'   => __( 'Cover', 'premium-addons-for-elementor' ),
				),
				'default'   => 'contain',
				'selectors' => array(
					'{{WRAPPER}} .premium-image-scroll-mask-media' => 'mask-size: {{VALUE}};-webkit-mask-size: {{VALUE}};',
				),
				'condition' => array(
					'mask_image_scroll_switcher' => 'yes',
				),
			)
		);

		$this->add_control(
			'mask_position_cover',
			array(
				'label'     => __( 'Mask Position', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::SELECT,
				'options'   => array(
					'center center' => __( 'Center Center', 'premium-addons-for-elementor' ),
					'center left'   => __( 'Center Left', 'premium-addons-for-elementor' ),
					'center right'  => __( 'Center Right', 'premium-addons-for-elementor' ),
					'top center'    => __( 'Top Center', 'premium-addons-for-elementor' ),
					'top left'      => __( 'Top Left', 'premium-addons-for-elementor' ),
					'top right'     => __( 'Top Right', 'premium-addons-for-elementor' ),
					'bottom center' => __( 'Bottom Center', 'premium-addons-for-elementor' ),
					'bottom left'   => __( 'Bottom Left', 'premium-addons-for-elementor' ),
					'bottom right'  => __( 'Bottom Right', 'premium-addons-for-elementor' ),
				),
				'default'   => 'center center',
				'selectors' => array(
					'{{WRAPPER}} .premium-image-scroll-mask-media' => 'mask-position: {{VALUE}};-webkit-mask-position: {{VALUE}}',
				),
				'condition' => array(
					'mask_image_scroll_switcher' => 'yes',
					'mask_size'                  => 'cover',
				),
			)
		);

		$this->add_control(
			'mask_position_contain',
			array(
				'label'     => __( 'Mask Position', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::SELECT,
				'options'   => array(
					'center center' => __( 'Center Center', 'premium-addons-for-elementor' ),
					'top center'    => __( 'Top Center', 'premium-addons-for-elementor' ),
					'bottom center' => __( 'Bottom Center', 'premium-addons-for-elementor' ),
				),
				'default'   => 'center center',
				'selectors' => array(
					'{{WRAPPER}} .premium-image-scroll-mask-media' => 'mask-position: {{VALUE}};-webkit-mask-position: {{VALUE}}',
				),
				'condition' => array(
					'mask_image_scroll_switcher' => 'yes',
					'mask_size'                  => 'contain',
				),
			)
		);

		$this->add_control(
			'image_scroll_shadow',
			array(
				'label'        => __( 'Image Shadow', 'premium-addons-for-elementor' ),
				'type'         => Controls_Manager::POPOVER_TOGGLE,
				'condition'    => array(
					'mask_image_scroll_switcher' => 'yes',
				),
				'return_value' => 'yes',
				'render_type'  => 'template',
			)
		);

		$this->start_popover();

		$this->add_control(
			'image_scroll_shadow_color',
			array(
				'label'   => __( 'Color', 'premium-addons-for-elementor' ),
				'type'    => Controls_Manager::COLOR,
				'default' => 'rgba(0,0,0,0.5)',
			)
		);

		$this->add_control(
			'image_scroll_shadow_h',
			array(
				'label'   => __( 'Horizontal', 'premium-addons-for-elementor' ),
				'type'    => Controls_Manager::SLIDER,
				'range'   => array(
					'px' => array(
						'min'  => 0,
						'max'  => 100,
						'step' => 1,
					),
				),
				'default' => array(
					'size' => 0,
					'unit' => 'px',
				),
			)
		);

		$this->add_control(
			'image_scroll_shadow_v',
			array(
				'label'   => __( 'Vertical', 'premium-addons-for-elementor' ),
				'type'    => Controls_Manager::SLIDER,
				'range'   => array(
					'px' => array(
						'min'  => 0,
						'max'  => 100,
						'step' => 1,
					),
				),
				'default' => array(
					'size' => 0,
					'unit' => 'px',
				),
			)
		);

		$this->add_control(
			'image_scroll_shadow_blur',
			array(
				'label'   => __( 'Blur', 'premium-addons-for-elementor' ),
				'type'    => Controls_Manager::SLIDER,
				'range'   => array(
					'px' => array(
						'min'  => 0,
						'max'  => 100,
						'step' => 1,
					),
				),
				'default' => array(
					'size' => 10,
					'unit' => 'px',
				),
			)
		);

		$this->end_popover();

		$this->end_controls_section();

		$this->start_controls_section(
			'advanced_settings',
			array(
				'label' => __( 'Advanced Settings', 'premium-addons-for-elementor' ),
			)
		);

		$this->add_control(
			'direction_type',
			array(
				'label'       => __( 'Direction', 'premium-addons-for-elementor' ),
				'description' => __( 'Select Scroll Direction', 'premium-addons-for-elementor' ),
				'type'        => Controls_Manager::SELECT,
				'options'     => array(
					'horizontal' => __( 'Horizontal', 'premium-addons-for-elementor' ),
					'vertical'   => __( 'Vertical', 'premium-addons-for-elementor' ),
				),
				'default'     => 'vertical',
			)
		);

		$this->add_control(
			'image_fit',
			array(
				'label'     => __( 'Image Fit', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::SELECT,
				'options'   => array(
					'fill'  => __( 'Fill', 'premium-addons-for-elementor' ),
					'cover' => __( 'Cover', 'premium-addons-for-elementor' ),
				),
				'condition' => array(
					'direction_type' => 'horizontal',
				),
				'default'   => 'fill',
				'selectors' => array(
					'{{WRAPPER}} .premium-image-scroll-container .premium-image-scroll-horizontal img ' => 'object-fit:{{VALUE}};',
				),
			)
		);

		$this->add_control(
			'reverse',
			array(
				'label'     => __( 'Reverse Direction', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::SWITCHER,
				'condition' => array(
					'trigger_type' => 'hover',
				),
			)
		);

		$this->add_control(
			'trigger_type',
			array(
				'label'   => __( 'Trigger', 'premium-addons-for-elementor' ),
				'type'    => Controls_Manager::SELECT,
				'options' => array(
					'hover'  => __( 'Hover', 'premium-addons-for-elementor' ),
					'scroll' => __( 'Mouse Scroll', 'premium-addons-for-elementor' ),
				),
				'default' => 'hover',
			)
		);

		$this->add_control(
			'duration_speed',
			array(
				'label'       => __( 'Speed', 'premium-addons-for-elementor' ),
				'description' => __( 'Set the scroll speed value. The value will be counted in seconds (s)', 'premium-addons-for-elementor' ),
				'type'        => Controls_Manager::NUMBER,
				'default'     => 3,
				'condition'   => array(
					'trigger_type' => 'hover',
				),
				'selectors'   => array(
					'{{WRAPPER}} .premium-image-scroll-container img'   => 'transition-duration: {{Value}}s',
				),
			)
		);

		$this->add_control(
			'icon_switcher',
			array(
				'label' => __( 'Icon', 'premium-addons-for-elementor' ),
				'type'  => Controls_Manager::SWITCHER,
			)
		);

		$this->add_control(
			'icon_size',
			array(
				'label'      => __( 'Icon Size', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', 'em' ),
				'default'    => array(
					'size' => 30,
				),
				'range'      => array(
					'px' => array(
						'min' => 5,
						'max' => 100,
					),
				),
				'selectors'  => array(
					'{{WRAPPER}} .premium-image-scroll-icon' => 'font-size: {{SIZE}}{{UNIT}};',
				),
				'condition'  => array(
					'icon_switcher' => 'yes',
				),
			)
		);

		$this->add_control(
			'overlay',
			array(
				'label'     => __( 'Overlay', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::SWITCHER,
				'label_on'  => __( 'Show', 'premium-addons-for-elementor' ),
				'label_off' => __( 'Hide', 'premium-addons-for-elementor' ),

			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_pa_docs',
			array(
				'label' => __( 'Helpful Documentations', 'premium-addons-for-elementor' ),
			)
		);

		$doc1_url = Helper_Functions::get_campaign_link( 'https://premiumaddons.com/docs/image-scroll-widget-tutorial/', 'editor-page', 'wp-editor', 'get-support' );

		$this->add_control(
			'doc_1',
			array(
				'type'            => Controls_Manager::RAW_HTML,
				'raw'             => sprintf( '<a href="%s" target="_blank">%s</a>', $doc1_url, __( 'Gettings started »', 'premium-addons-for-elementor' ) ),
				'content_classes' => 'editor-pa-doc',
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'image_style',
			array(
				'label' => __( 'Image', 'premium-addons-for-elementor' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_control(
			'icon_color',
			array(
				'label'     => __( 'Icon Color', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .premium-image-scroll-icon'  => 'color: {{VALUE}};',
				),
				'condition' => array(
					'icon_switcher' => 'yes',
				),
			)
		);

		$this->add_control(
			'overlay_background',
			array(
				'label'     => __( 'Overlay Color', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .premium-image-scroll-overlay'  => 'background: {{VALUE}};',
				),
				'condition' => array(
					'overlay' => 'yes',
				),
			)
		);

		$this->start_controls_tabs( 'image_style_tabs' );

		$this->start_controls_tab(
			'image_style_tab_normal',
			array(
				'label' => __( 'Normal', 'premium-addons-for-elementor' ),
			)
		);

		$this->add_control(
			'opacity',
			array(
				'label'     => __( 'Opacity', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::SLIDER,
				'range'     => array(
					'px' => array(
						'max'  => 1,
						'min'  => 0.10,
						'step' => 0.01,
					),
				),
				'selectors' => array(
					'{{WRAPPER}} img' => 'opacity: {{SIZE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Css_Filter::get_type(),
			array(
				'name'     => 'css_filters',
				'selector' => '{{WRAPPER}} .premium-image-scroll-container img',
			)
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'image_style_tab_hover',
			array(
				'label' => __( 'Hover', 'premium-addons-for-elementor' ),
			)
		);

		$this->add_control(
			'hover_opacity',
			array(
				'label'     => __( 'Opacity', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::SLIDER,
				'range'     => array(
					'px' => array(
						'max'  => 1,
						'min'  => 0.10,
						'step' => 0.01,
					),
				),
				'selectors' => array(
					'{{WRAPPER}} .premium-image-scroll-section:hover img' => 'opacity: {{SIZE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Css_Filter::get_type(),
			array(
				'name'     => 'css_filters_hover',
				'selector' => '{{WRAPPER}} .premium-image-scroll-container img:hover',
			)
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->add_control(
			'blend_mode',
			array(
				'label'     => __( 'Blend Mode', 'elementor' ),
				'type'      => Controls_Manager::SELECT,
				'options'   => array(
					''            => __( 'Normal', 'elementor' ),
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
				'separator' => 'before',
				'selectors' => array(
					'{{WRAPPER}} .premium-image-scroll-container img' => 'mix-blend-mode: {{VALUE}}',
				),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'container_style',
			array(
				'label' => __( 'Container', 'premium-addons-for-elementor' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->start_controls_tabs( 'container_style_tabs' );

		$this->start_controls_tab(
			'container_style_normal',
			array(
				'label' => __( 'Normal', 'premium-addons-for-elementor' ),
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'     => 'container_border',
				'selector' => '{{WRAPPER}} .premium-image-scroll-section',
			)
		);

		$this->add_control(
			'container_border_radius',
			array(
				'label'      => __( 'Border Radius', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', '%', 'em' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-image-scroll-section, {{WRAPPER}} .premium-container-scroll' => 'border-radius: {{SIZE}}{{UNIT}}',
				),
				'condition'  => array(
					'container_adv_radius!' => 'yes',
				),
			)
		);

		$this->add_control(
			'container_adv_radius',
			array(
				'label'       => __( 'Advanced Border Radius', 'premium-addons-for-elementor' ),
				'type'        => Controls_Manager::SWITCHER,
				'description' => __( 'Apply custom radius values. Get the radius value from ', 'premium-addons-for-elementor' ) . '<a href="https://9elements.github.io/fancy-border-radius/" target="_blank">here</a>',
			)
		);

		$this->add_control(
			'container_adv_radius_value',
			array(
				'label'     => __( 'Border Radius', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::TEXT,
				'dynamic'   => array( 'active' => true ),
				'selectors' => array(
					'{{WRAPPER}} .premium-image-scroll-section, {{WRAPPER}} .premium-container-scroll' => 'border-radius: {{VALUE}};',
				),
				'condition' => array(
					'container_adv_radius' => 'yes',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name'     => 'container_box_shadow',
				'selector' => '{{WRAPPER}} .premium-image-scroll-section',
			)
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'container_style_hover',
			array(
				'label' => __( 'Hover', 'premium-addons-for-elementor' ),
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'     => 'container_border_hover',
				'selector' => '{{WRAPPER}} .premium-image-scroll-section:hover',
			)
		);

		$this->add_control(
			'container_border_radius_hover',
			array(
				'label'      => __( 'Border Radius', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', '%', 'em' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-image-scroll-section:hover, {{WRAPPER}} .premium-container-scroll:hover' => 'border-radius: {{SIZE}}{{UNIT}}',
				),
				'condition'  => array(
					'container_hover_adv_radius!' => 'yes',
				),
			)
		);

		$this->add_control(
			'container_hover_adv_radius',
			array(
				'label'       => __( 'Advanced Border Radius', 'premium-addons-for-elementor' ),
				'type'        => Controls_Manager::SWITCHER,
				'description' => __( 'Apply custom radius values. Get the radius value from ', 'premium-addons-for-elementor' ) . '<a href="https://9elements.github.io/fancy-border-radius/" target="_blank">here</a>',
			)
		);

		$this->add_control(
			'container_hover_adv_radius_value',
			array(
				'label'     => __( 'Border Radius', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::TEXT,
				'dynamic'   => array( 'active' => true ),
				'selectors' => array(
					'{{WRAPPER}} .premium-image-scroll-section:hover, {{WRAPPER}} .premium-container-scroll:hover' => 'border-radius: {{VALUE}};',
				),
				'condition' => array(
					'container_hover_adv_radius' => 'yes',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name'     => 'container_box_shadow_hover',
				'selector' => '{{WRAPPER}} .premium-image-scroll-section:hover',
			)
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();

	}

	/**
	 * Render Image Scroll widget output on the frontend.
	 *
	 * Written in PHP and used to generate the final HTML.
	 *
	 * @since 1.0.0
	 * @access protected
	 */
	protected function render() {

		$settings = $this->get_settings_for_display();

		$link_type = $settings['link_type'];

		if ( 'yes' === $settings['link_switcher'] ) {

			$link_url = 'url' === $link_type ? $settings['link'] : get_permalink( $settings['existing_page'] );

			$this->add_render_attribute( 'link', 'class', 'premium-image-scroll-link' );

			if ( 'url' === $link_type ) {
				$this->add_link_attributes( 'link', $link_url );
			} else {
				$this->add_render_attribute( 'link', 'href', $link_url );
			}
		}

		if ( $settings['icon_switcher'] ) {
			$icon_type = sprintf( 'pa-%s-mouse-scroll', $settings['direction_type'] );
		}

		$image_scroll = array(
			'trigger'   => $settings['trigger_type'],
			'direction' => $settings['direction_type'],
			'reverse'   => $settings['reverse'],
		);

		$this->add_render_attribute(
			'container',
			array(
				'class'         => 'premium-image-scroll-container',
				'data-settings' => wp_json_encode( $image_scroll ),
			)
		);

		$this->add_render_attribute( 'direction_type', 'class', 'premium-image-scroll-' . $settings['direction_type'] );

		if ( ! empty( $settings['mask_image_scroll_switcher'] ) && 'yes' === $settings['mask_image_scroll_switcher'] ) {
			$this->add_render_attribute( 'direction_type', 'class', 'premium-image-scroll-mask-media' );
		}

		$image_html = '';
		if ( ! empty( $settings['image']['url'] ) ) {

			$this->add_render_attribute(
				'image',
				array(
					'src'   => $settings['image']['url'],
					'alt'   => Control_Media::get_image_alt( $settings['image'] ),
					'title' => Control_Media::get_image_title( $settings['image'] ),
				)
			);

			$image_html = Group_Control_Image_Size::get_attachment_image_html( $settings, 'thumbnail', 'image' );

		}
		if ( 'yes' === $settings['mask_image_scroll_switcher'] && '' !== $settings['mask_shape_image_scroll']['url'] && 'yes' === $settings['image_scroll_shadow'] ) {
			$this->add_render_attribute( 'shadow', 'style', 'filter: drop-shadow(' . $settings['image_scroll_shadow_color'] . ' ' . $settings['image_scroll_shadow_h']['size'] . 'px ' . $settings['image_scroll_shadow_v']['size'] . 'px ' . $settings['image_scroll_shadow_blur']['size'] . 'px )' );
		}

		?>
			<div class="premium-image-scroll-section" <?php echo wp_kses_post( $this->get_render_attribute_string( 'shadow' ) ); ?>>
				<div <?php echo wp_kses_post( $this->get_render_attribute_string( 'container' ) ); ?>>
					<?php if ( 'yes' === $settings['icon_switcher'] ) : ?>
						<div class="premium-image-scroll-content">
							<i class="premium-image-scroll-icon <?php echo esc_attr( $icon_type ); ?>"></i>
						</div>
					<?php endif; ?>
					<div <?php echo wp_kses_post( $this->get_render_attribute_string( 'direction_type' ) ); ?>>
						<?php if ( 'yes' === $settings['overlay'] ) : ?>
							<div class="premium-image-scroll-overlay">
							<?php
						endif;
						if ( 'yes' === $settings['link_switcher'] ) :
							?>
								<a <?php echo wp_kses_post( $this->get_render_attribute_string( 'link' ) ); ?>></a>
							<?php
						endif;
						if ( 'yes' === $settings['overlay'] ) :
							?>
							</div>
							<?php
							endif;
							echo wp_kses_post( $image_html );
						?>
					</div>
				</div>
			</div>
		<?php

	}

	/**
	 * Render Image Scroll widget output in the editor.
	 *
	 * Written as a Backbone JavaScript template and used to generate the live preview.
	 *
	 * @since 1.0.0
	 * @access protected
	 */
	protected function content_template() {
		?>
		<#
			var linkType = settings.link_type,
				trigger = settings.trigger_type,
				direction = settings.direction_type,
				reverse = settings.reverse,
				url;

			var scrollSettings = {};

			scrollSettings.trigger = trigger;
			scrollSettings.direction = direction,
			scrollSettings.reverse  = reverse;

			if ( 'yes' == settings.icon_switcher ) {
				var iconClass = 'pa-' + direction + '-mouse-scroll';
			}

			if ( 'yes' == settings.link_switcher ) {

				view.addRenderAttribute( 'link', 'class', 'premium-image-scroll-link' );

				url = 'url' == linkType ? settings.link.url : settings.existing_page;

				view.addRenderAttribute( 'link', 'href',  url );

			}

			view.addRenderAttribute( 'container', 'class', 'premium-image-scroll-container' );

			view.addRenderAttribute( 'container', 'data-settings', JSON.stringify(scrollSettings) );

			view.addRenderAttribute( 'direction_type', 'class', 'premium-image-scroll-' + direction );

			view.addRenderAttribute( 'image', 'src', settings.image.url );

			if( settings.mask_image_scroll_switcher && 'yes' === settings.mask_image_scroll_switcher ) {
				view.addRenderAttribute( 'direction_type', 'class', 'premium-image-scroll-mask-media');
			}

			var imageHtml = '';
			if ( settings.image.url ) {
			var image = {
				id: settings.image.id,
				url: settings.image.url,
				size: settings.thumbnail_size,
				dimension: settings.thumbnail_custom_dimension,
				model: view.getEditModel()
			};

			var image_url = elementor.imagesManager.getImageUrl( image );

			imageHtml = '<img src="' + image_url + '"/>';

		}
		if( 'yes' === settings.mask_image_scroll_switcher && settings.mask_shape_image_scroll.url !== '' && 'yes' === settings.image_scroll_shadow ) {
			view.addRenderAttribute( 'shadow', 'style', 'filter: drop-shadow('+settings.image_scroll_shadow_color +' '+ settings.image_scroll_shadow_h.size +'px '+ settings.image_scroll_shadow_v.size +'px '+ settings.image_scroll_shadow_blur.size+'px '+')' );
		}

		#>
		<div class="premium-image-scroll-section" {{{ view.getRenderAttributeString('shadow') }}}>
			<div {{{ view.getRenderAttributeString('container') }}}>
				<# if (  'yes' == settings.icon_switcher ) { #>
					<div class="premium-image-scroll-content">
						<i class="premium-image-scroll-icon {{ iconClass }}"></i>
					</div>
				<# } #>
				<div {{{ view.getRenderAttributeString('direction_type') }}}>
					<# if( 'yes' == settings.overlay ) { #>
						<div class="premium-image-scroll-overlay">
					<# }
					if ( 'yes' == settings.link_switcher && '' !=  url ) { #>
						<a {{{ view.getRenderAttributeString('link') }}}></a>
					<# }
					if( 'yes' == settings.overlay ) { #>
						</div>
					<# } #>

					{{{ imageHtml }}}

				</div>
			</div>
		</div>
		<?php
	}

}
