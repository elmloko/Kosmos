<?php
/**
 * Premium Dual Heading.
 */

namespace PremiumAddons\Widgets;

// Elementor Classes.
use PremiumAddons\Includes;
use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Core\Kits\Documents\Tabs\Global_Colors;
use Elementor\Core\Kits\Documents\Tabs\Global_Typography;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Text_Shadow;
use Elementor\Group_Control_Background;

// PremiumAddons Classes.
use PremiumAddons\Includes\Helper_Functions;
use PremiumAddons\Includes\Premium_Template_Tags;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // If this file is called directly, abort.
}

/**
 * Class Premium_Dual_Header
 */
class Premium_Dual_Header extends Widget_Base {

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
		return 'premium-addon-dual-header';
	}

	/**
	 * Retrieve Widget Title.
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function get_title() {
		return __( 'Dual Heading', 'premium-addons-for-elementor' );
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
			'premium-addons',
			'elementor-waypoints',
		);
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
		return 'pa-dual-header';
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
		return array( 'pa', 'premium', 'advanced', 'title', 'heading', 'multi', 'text' );
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
	 * Register Dual Heading controls.
	 *
	 * @since 1.0.0
	 * @access protected
	 */
	protected function register_controls() { // phpcs:ignore PSR2.Methods.MethodDeclaration.Underscore

		/*Start General Section*/
		$this->start_controls_section(
			'premium_dual_header_general_settings',
			array(
				'label' => __( 'Dual Heading', 'premium-addons-for-elementor' ),
			)
		);

		/*First Header*/
		$this->add_control(
			'premium_dual_header_first_header_text',
			array(
				'label'       => __( 'First Heading', 'premium-addons-for-elementor' ),
				'type'        => Controls_Manager::TEXT,
				'dynamic'     => array( 'active' => true ),
				'default'     => __( 'Premium', 'premium-addons-for-elementor' ),
				'label_block' => true,
			)
		);

		/*Second Header*/
		$this->add_control(
			'premium_dual_header_second_header_text',
			array(
				'label'       => __( 'Second Heading', 'premium-addons-for-elementor' ),
				'type'        => Controls_Manager::TEXT,
				'dynamic'     => array( 'active' => true ),
				'default'     => __( 'Addons', 'premium-addons-for-elementor' ),
				'label_block' => true,
			)
		);

		/*Title Tag*/
		$this->add_control(
			'premium_dual_header_first_header_tag',
			array(
				'label'       => __( 'HTML Tag', 'premium-addons-for-elementor' ),
				'type'        => Controls_Manager::SELECT,
				'default'     => 'h2',
				'options'     => array(
					'h1'   => 'H1',
					'h2'   => 'H2',
					'h3'   => 'H3',
					'h4'   => 'H4',
					'h5'   => 'H5',
					'h6'   => 'H6',
					'p'    => 'p',
					'span' => 'span',
				),
				'label_block' => true,
			)
		);

		$this->add_responsive_control(
			'premium_dual_header_position',
			array(
				'label'        => __( 'Display', 'premium-addons-for-elementor' ),
				'type'         => Controls_Manager::SELECT,
				'options'      => array(
					'inline' => __( 'Inline', 'premium-addons-for-elementor' ),
					'block'  => __( 'Block', 'premium-addons-for-elementor' ),
				),
				'default'      => 'inline',
				'prefix_class' => 'premium-header-',
				'selectors'    => array(
					'{{WRAPPER}} .premium-dual-header-first-header  span' => 'display: {{VALUE}}',
				),
				'label_block'  => true,
			)
		);

		$this->add_control(
			'premium_dual_header_link_switcher',
			array(
				'label'       => __( 'Link', 'premium-addons-for-elementor' ),
				'type'        => Controls_Manager::SWITCHER,
				'description' => __( 'Enable or disable link', 'premium-addons-for-elementor' ),
			)
		);

		$this->add_control(
			'premium_dual_heading_link_selection',
			array(
				'label'       => __( 'Link Type', 'premium-addons-for-elementor' ),
				'type'        => Controls_Manager::SELECT,
				'options'     => array(
					'url'  => __( 'URL', 'premium-addons-for-elementor' ),
					'link' => __( 'Existing Page', 'premium-addons-for-elementor' ),
				),
				'default'     => 'url',
				'label_block' => true,
				'condition'   => array(
					'premium_dual_header_link_switcher' => 'yes',
				),
			)
		);

		$this->add_control(
			'premium_dual_heading_link',
			array(
				'label'       => __( 'Link', 'premium-addons-for-elementor' ),
				'type'        => Controls_Manager::URL,
				'dynamic'     => array( 'active' => true ),
				'default'     => array(
					'url' => '#',
				),
				'placeholder' => 'https://premiumaddons.com/',
				'label_block' => true,
				'separator'   => 'after',
				'condition'   => array(
					'premium_dual_header_link_switcher'   => 'yes',
					'premium_dual_heading_link_selection' => 'url',
				),
			)
		);

		$this->add_control(
			'premium_dual_heading_existing_link',
			array(
				'label'       => __( 'Existing Page', 'premium-addons-for-elementor' ),
				'type'        => Controls_Manager::SELECT2,
				'options'     => $this->getTemplateInstance()->get_all_posts(),
				'condition'   => array(
					'premium_dual_header_link_switcher'   => 'yes',
					'premium_dual_heading_link_selection' => 'link',
				),
				'multiple'    => false,
				'separator'   => 'after',
				'label_block' => true,
			)
		);

		$this->add_responsive_control(
			'premium_dual_header_text_align',
			array(
				'label'     => __( 'Alignment', 'premium-addons-for-elementor' ),
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
					'{{WRAPPER}} .elementor-widget-container' => 'text-align: {{VALUE}};',
				),
			)
		);

		$this->add_responsive_control(
			'rotate',
			array(
				'label'     => __( 'Rotation (degrees)', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::NUMBER,
				'min'       => -180,
				'max'       => 180,
				'selectors' => array(
					'{{WRAPPER}} .elementor-widget-container' => 'transform: rotate({{VALUE}}deg);',
				),
			)
		);

		$this->add_responsive_control(
			'transform_origin_x',
			array(
				'label'       => __( 'X Anchor Point', 'premium-addons-for-elementor' ),
				'type'        => Controls_Manager::CHOOSE,
				'default'     => 'center',
				'options'     => array(
					'left'   => array(
						'title' => __( 'Left', 'premium-addons-for-elementor' ),
						'icon'  => 'eicon-h-align-left',
					),
					'center' => array(
						'title' => __( 'Center', 'premium-addons-for-elementor' ),
						'icon'  => 'eicon-h-align-center',
					),
					'right'  => array(
						'title' => __( 'Right', 'premium-addons-for-elementor' ),
						'icon'  => 'eicon-h-align-right',
					),
				),
				'label_block' => false,
				'toggle'      => false,
				'render_type' => 'ui',
				'condition'   => array(
					'rotate!' => '',
				),
			)
		);

		$this->add_responsive_control(
			'transform_origin_y',
			array(
				'label'       => __( 'Y Anchor Point', 'premium-addons-for-elementor' ),
				'type'        => Controls_Manager::CHOOSE,
				'default'     => 'center',
				'options'     => array(
					'top'    => array(
						'title' => __( 'Top', 'premium-addons-for-elementor' ),
						'icon'  => 'eicon-v-align-top',
					),
					'center' => array(
						'title' => __( 'Center', 'premium-addons-for-elementor' ),
						'icon'  => 'eicon-v-align-middle',
					),
					'bottom' => array(
						'title' => __( 'Bottom', 'premium-addons-for-elementor' ),
						'icon'  => 'eicon-v-align-bottom',
					),
				),
				'selectors'   => array(
					'{{WRAPPER}} .elementor-widget-container' => 'transform-origin: {{transform_origin_x.VALUE}} {{VALUE}}',
				),
				'label_block' => false,
				'toggle'      => false,
				'condition'   => array(
					'rotate!' => '',
				),
			)
		);

		$this->add_control(
			'background_text_switcher',
			array(
				'label'        => __( 'Background Text', 'premium-addons-for-elementor' ),
				'type'         => Controls_Manager::SWITCHER,
				'separator'    => 'before',
				'prefix_class' => 'premium-bg-text-',
			)
		);

		$this->add_control(
			'background_text',
			array(
				'label'     => __( 'Text', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::TEXT,
				'default'   => __( 'Awesome Title', 'premium-addons-for-elementor' ),
				'condition' => array(
					'background_text_switcher' => 'yes',
				),
				'selectors' => array(
					'{{WRAPPER}}.premium-bg-text-yes .elementor-widget-container:before' => 'content: "{{VALUE}}"',
				),

			)
		);

		$this->add_control(
			'background_text_width',
			array(
				'label'       => __( 'Width', 'premium-addons-for-elementor' ),
				'type'        => Controls_Manager::SELECT,
				'options'     => array(
					'auto' => __( 'Auto', 'premium-addons-for-elementor' ),
					'100%' => __( 'Full Width', 'premium-addons-for-elementor' ),
				),
				'default'     => 'auto',
				'label_block' => true,
				'selectors'   => array(
					'{{WRAPPER}}.premium-bg-text-yes .elementor-widget-container:before' => 'width: {{VALUE}}',
				),
				'condition'   => array(
					'background_text_switcher' => 'yes',
				),
			)
		);

		$this->add_responsive_control(
			'background_text_left',
			array(
				'label'      => __( 'Horizontal Offset', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', 'em', '%', 'custom' ),
				'range'      => array(
					'px' => array(
						'min' => -500,
						'max' => 500,
					),
					'em' => array(
						'min' => -50,
						'max' => 50,
					),
					'%'  => array(
						'min' => -100,
						'max' => 100,
					),
				),
				'selectors'  => array(
					'{{WRAPPER}}.premium-bg-text-yes .elementor-widget-container:before' => 'left: {{SIZE}}{{UNIT}}',
				),
				'condition'  => array(
					'background_text_switcher' => 'yes',
				),
			)
		);

		$this->add_responsive_control(
			'background_text_top',
			array(
				'label'      => __( 'Vertical Offset', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', 'em', '%', 'custom' ),
				'range'      => array(
					'px' => array(
						'min' => -500,
						'max' => 500,
					),
					'em' => array(
						'min' => -50,
						'max' => 50,
					),
					'%'  => array(
						'min' => -100,
						'max' => 100,
					),
				),
				'selectors'  => array(
					'{{WRAPPER}}.premium-bg-text-yes .elementor-widget-container:before' => 'top: {{SIZE}}{{UNIT}}',
				),
				'condition'  => array(
					'background_text_switcher' => 'yes',
				),
			)
		);

		$this->add_responsive_control(
			'background_text_align',
			array(
				'label'     => __( 'Alignment', 'premium-addons-for-elementor' ),
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
				'default'   => 'left',
				'selectors' => array(
					'{{WRAPPER}}.premium-bg-text-yes .elementor-widget-container:before' => 'text-align: {{VALUE}};',
				),
				'condition' => array(
					'background_text_switcher' => 'yes',
				),
			)
		);

		$this->add_responsive_control(
			'background_text_rotate',
			array(
				'label'      => __( 'Rotate (degrees)', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'deg' ),
				'default'    => array(
					'unit' => 'deg',
					'size' => 0,
				),
				'selectors'  => array(
					'{{WRAPPER}}.premium-bg-text-yes .elementor-widget-container:before' => 'transform: rotate({{SIZE}}{{UNIT}})',
				),
				'condition'  => array(
					'background_text_switcher' => 'yes',
				),
			)
		);

		$this->add_control(
			'mask_switcher',
			array(
				'label'        => __( 'Minimal Mask Effect', 'premium-addons-for-elementor' ),
				'type'         => Controls_Manager::SWITCHER,
				'render_type'  => 'template',
				'prefix_class' => 'premium-mask-',
				'description'  => __( 'Please note That this effect takes place once the element is in the viewport', 'premium-addons-for-elementor' ),
			)
		);

		$this->add_control(
			'mask_color',
			array(
				'label'       => __( 'Mask Color', 'premium-addons-for-elementor' ),
				'type'        => Controls_Manager::COLOR,
				'render_type' => 'template',
				'selectors'   => array(
					'{{WRAPPER}}.premium-mask-yes .premium-dual-header-first-header span::after'   => 'background: {{VALUE}};',
				),
				'condition'   => array(
					'mask_switcher' => 'yes',
				),
			)
		);

		$this->add_control(
			'premium_mask_dir',
			array(
				'label'        => __( 'Direction', 'premium-addons-for-elementor' ),
				'type'         => Controls_Manager::SELECT,
				'default'      => 'tr',
				'prefix_class' => 'premium-mask-',
				'render_type'  => 'template',
				'options'      => array(
					'tr' => __( 'To Right', 'premium-addons-for-elementor' ),
					'tl' => __( 'To Left', 'premium-addons-for-elementor' ),
					'tt' => __( 'To Top', 'premium-addons-for-elementor' ),
					'tb' => __( 'To Bottom', 'premium-addons-for-elementor' ),
				),
				'condition'    => array(
					'mask_switcher' => 'yes',
				),
			)
		);

		$this->add_responsive_control(
			'mask_padding',
			array(
				'label'      => __( 'Words Padding', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-mask-span' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
				'condition'  => array(
					'mask_switcher' => 'yes',
				),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_pa_docs',
			array(
				'label' => __( 'Helpful Documentations', 'premium-addons-for-elementor' ),
			)
		);

		$doc1_url = Helper_Functions::get_campaign_link( 'https://premiumaddons.com/docs/dual-heading-widget-tutorial', 'editor-page', 'wp-editor', 'get-support' );

		$this->add_control(
			'doc_1',
			array(
				'type'            => Controls_Manager::RAW_HTML,
				'raw'             => sprintf( '<a href="%s" target="_blank">%s</a>', $doc1_url, __( 'Gettings started »', 'premium-addons-for-elementor' ) ),
				'content_classes' => 'editor-pa-doc',
			)
		);

		$doc2_url = Helper_Functions::get_campaign_link( 'https://premiumaddons.com/docs/how-to-add-an-outlined-heading-to-my-website', 'editor-page', 'wp-editor', 'get-support' );

		$this->add_control(
			'doc_2',
			array(
				'type'            => Controls_Manager::RAW_HTML,
				'raw'             => sprintf( '<a href="%s" target="_blank">%s</a>', $doc2_url, __( 'How to add an outlined heading using Dual Heading widget »', 'premium-addons-for-elementor' ) ),
				'content_classes' => 'editor-pa-doc',
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'premium_dual_header_first_style',
			array(
				'label' => __( 'First Heading', 'premium-addons-for-elementor' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'first_header_typography',
				'global'   => array(
					'default' => Global_Typography::TYPOGRAPHY_PRIMARY,
				),
				'selector' => '{{WRAPPER}} .premium-dual-header-first-span',
			)
		);

		$this->add_control(
			'premium_dual_header_first_animated',
			array(
				'label' => __( 'Animated Background', 'premium-addons-for-elementor' ),
				'type'  => Controls_Manager::SWITCHER,
			)
		);

		$this->add_control(
			'premium_dual_header_first_back_clip',
			array(
				'label'       => __( 'Background Style', 'premium-addons-for-elementor' ),
				'type'        => Controls_Manager::SELECT,
				'default'     => 'color',
				'description' => __( 'Choose ‘Normal’ style to put a background behind the text. Choose ‘Clipped’ style so the background will be clipped on the text.', 'premium-addons-for-elementor' ),
				'options'     => array(
					'color'   => __( 'Normal', 'premium-addons-for-elementor' ),
					'clipped' => __( 'Clipped', 'premium-addons-for-elementor' ),
				),
				'label_block' => true,
			)
		);

		$this->add_control(
			'premium_dual_header_first_color',
			array(
				'label'     => __( 'Text Color', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'global'    => array(
					'default' => Global_Colors::COLOR_PRIMARY,
				),
				'condition' => array(
					'premium_dual_header_first_back_clip' => 'color',
				),
				'selectors' => array(
					'{{WRAPPER}} .premium-dual-header-first-span'   => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			array(
				'name'      => 'premium_dual_header_first_background',
				'types'     => array( 'classic', 'gradient' ),
				'condition' => array(
					'premium_dual_header_first_back_clip' => 'color',
				),
				'selector'  => '{{WRAPPER}} .premium-dual-header-first-span',
			)
		);

		$this->add_control(
			'premium_dual_header_first_stroke',
			array(
				'label'     => __( 'Stroke', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::SWITCHER,
				'condition' => array(
					'premium_dual_header_first_back_clip' => 'clipped',
				),
			)
		);

		$this->add_control(
			'premium_dual_header_first_stroke_text_color',
			array(
				'label'     => __( 'Stroke Text Color', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'condition' => array(
					'premium_dual_header_first_back_clip' => 'clipped',
					'premium_dual_header_first_stroke'    => 'yes',
				),
				'selectors' => array(
					'{{WRAPPER}} .premium-dual-header-first-clip.stroke .premium-dual-header-first-span'   => '-webkit-text-stroke-color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'premium_dual_header_first_stroke_color',
			array(
				'label'     => __( 'Stroke Fill Color', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'condition' => array(
					'premium_dual_header_first_back_clip' => 'clipped',
					'premium_dual_header_first_stroke'    => 'yes',
				),
				'selectors' => array(
					'{{WRAPPER}} .premium-dual-header-first-clip.stroke .premium-dual-header-first-span'   => '-webkit-text-fill-color: {{VALUE}};',
				),
			)
		);

		$this->add_responsive_control(
			'premium_dual_header_first_stroke_width',
			array(
				'label'     => __( 'Stroke Fill Width', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::SLIDER,
				'condition' => array(
					'premium_dual_header_first_back_clip' => 'clipped',
					'premium_dual_header_first_stroke'    => 'yes',
				),
				'selectors' => array(
					'{{WRAPPER}} .premium-dual-header-first-clip.stroke .premium-dual-header-first-span'   => '-webkit-text-stroke-width: {{SIZE}}px;',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			array(
				'name'      => 'premium_dual_header_first_clipped_background',
				'types'     => array( 'classic', 'gradient' ),
				'condition' => array(
					'premium_dual_header_first_back_clip' => 'clipped',
					'premium_dual_header_first_stroke!'   => 'yes',
				),
				'selector'  => '{{WRAPPER}} .premium-dual-header-first-span',
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'      => 'first_header_border',
				'selector'  => '{{WRAPPER}} .premium-dual-header-first-span',
				'separator' => 'before',
			)
		);

		$this->add_control(
			'premium_dual_header_first_border_radius',
			array(
				'label'      => __( 'Border Radius', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', '%', 'em' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-dual-header-first-span' => 'border-radius: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Text_Shadow::get_type(),
			array(
				'label'    => __( 'Shadow', 'premium-addons-for-elementor' ),
				'name'     => 'premium_dual_header_first_text_shadow',
				'selector' => '{{WRAPPER}} .premium-dual-header-first-span',
			)
		);

		$this->add_responsive_control(
			'premium_dual_header_first_margin',
			array(
				'label'      => __( 'Margin', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'separator'  => 'before',
				'selectors'  => array(
					'{{WRAPPER}} .premium-dual-header-first-span' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}',
				),
			)
		);

		$this->add_responsive_control(
			'premium_dual_header_first_padding',
			array(
				'label'      => __( 'Padding', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-dual-header-first-span' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}',
				),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'premium_dual_header_second_style',
			array(
				'label' => __( 'Second Heading', 'premium-addons-for-elementor' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'second_header_typography',
				'global'   => array(
					'default' => Global_Typography::TYPOGRAPHY_PRIMARY,
				),
				'selector' => '{{WRAPPER}} .premium-dual-header-second-header',
			)
		);

		$this->add_control(
			'premium_dual_header_second_animated',
			array(
				'label' => __( 'Animated Background', 'premium-addons-for-elementor' ),
				'type'  => Controls_Manager::SWITCHER,
			)
		);

		$this->add_control(
			'premium_dual_header_second_back_clip',
			array(
				'label'       => __( 'Background Style', 'premium-addons-for-elementor' ),
				'type'        => Controls_Manager::SELECT,
				'default'     => 'color',
				'description' => __( 'Choose ‘Normal’ style to put a background behind the text. Choose ‘Clipped’ style so the background will be clipped on the text.', 'premium-addons-for-elementor' ),
				'options'     => array(
					'color'   => __( 'Normal', 'premium-addons-for-elementor' ),
					'clipped' => __( 'Clipped', 'premium-addons-for-elementor' ),
				),
				'label_block' => true,
			)
		);

		$this->add_control(
			'premium_dual_header_second_color',
			array(
				'label'     => __( 'Text Color', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'global'    => array(
					'default' => Global_Colors::COLOR_SECONDARY,
				),
				'condition' => array(
					'premium_dual_header_second_back_clip' => 'color',
				),
				'selectors' => array(
					'{{WRAPPER}} .premium-dual-header-second-header'   => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			array(
				'name'      => 'premium_dual_header_second_background',
				'types'     => array( 'classic', 'gradient' ),
				'condition' => array(
					'premium_dual_header_second_back_clip' => 'color',
				),
				'selector'  => '{{WRAPPER}} .premium-dual-header-second-header',
			)
		);

		$this->add_control(
			'premium_dual_header_second_stroke',
			array(
				'label'     => __( 'Stroke', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::SWITCHER,
				'condition' => array(
					'premium_dual_header_second_back_clip' => 'clipped',
				),
			)
		);

		$this->add_control(
			'premium_dual_header_second_stroke_text_color',
			array(
				'label'     => __( 'Stroke Text Color', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'condition' => array(
					'premium_dual_header_second_back_clip' => 'clipped',
					'premium_dual_header_second_stroke'    => 'yes',
				),
				'selectors' => array(
					'{{WRAPPER}} .premium-dual-header-second-clip.stroke'   => '-webkit-text-stroke-color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'premium_dual_header_second_stroke_color',
			array(
				'label'     => __( 'Stroke Fill Color', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'condition' => array(
					'premium_dual_header_second_back_clip' => 'clipped',
					'premium_dual_header_second_stroke'    => 'yes',
				),
				'selectors' => array(
					'{{WRAPPER}} .premium-dual-header-second-clip.stroke'   => '-webkit-text-fill-color: {{VALUE}};',
				),
			)
		);

		$this->add_responsive_control(
			'premium_dual_header_second_stroke_width',
			array(
				'label'     => __( 'Stroke Fill Width', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::SLIDER,
				'condition' => array(
					'premium_dual_header_second_back_clip' => 'clipped',
					'premium_dual_header_second_stroke'    => 'yes',
				),
				'selectors' => array(
					'{{WRAPPER}} .premium-dual-header-second-clip.stroke'   => '-webkit-text-stroke-width: {{SIZE}}px;',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			array(
				'name'      => 'premium_dual_header_second_clipped_background',
				'types'     => array( 'classic', 'gradient' ),
				'condition' => array(
					'premium_dual_header_second_back_clip' => 'clipped',
					'premium_dual_header_second_stroke!'   => 'yes',
				),
				'selector'  => '{{WRAPPER}} .premium-dual-header-second-header',
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'      => 'second_header_border',
				'selector'  => '{{WRAPPER}} .premium-dual-header-second-header',
				'separator' => 'before',
			)
		);

		$this->add_control(
			'premium_dual_header_second_border_radius',
			array(
				'label'      => __( 'Border Radius', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', '%', 'em' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-dual-header-second-header' => 'border-radius: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Text_Shadow::get_type(),
			array(
				'label'    => __( 'Shadow', 'premium-addons-for-elementor' ),
				'name'     => 'premium_dual_header_second_text_shadow',
				'selector' => '{{WRAPPER}} .premium-dual-header-second-header',
			)
		);

		$this->add_responsive_control(
			'premium_dual_header_second_margin',
			array(
				'label'      => __( 'Margin', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'separator'  => 'before',
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-dual-header-second-header' => 'margin : {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}',
				),
			)
		);

		$this->add_responsive_control(
			'premium_dual_header_second_padding',
			array(
				'label'      => __( 'Padding', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-dual-header-second-header' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}',
				),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'background_text_style_section',
			array(
				'label'     => __( 'Background Text', 'premium-addons-for-elementor' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => array(
					'background_text_switcher' => 'yes',
				),
			)
		);

		$this->add_control(
			'background_text_color',
			array(
				'label'     => __( 'Color', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'global'    => array(
					'default' => Global_Colors::COLOR_PRIMARY,
				),
				'selectors' => array(
					'{{WRAPPER}}.premium-bg-text-yes .elementor-widget-container:before' => 'color: {{VALUE}}',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'background_text_typography',
				'global'   => array(
					'default' => Global_Typography::TYPOGRAPHY_PRIMARY,
				),
				'selector' => '{{WRAPPER}}.premium-bg-text-yes .elementor-widget-container:before',
			)
		);

		$this->add_group_control(
			Group_Control_Text_Shadow::get_type(),
			array(
				'name'     => 'background_text_shadow',
				'selector' => '{{WRAPPER}}.premium-bg-text-yes .elementor-widget-container:before',
			)
		);

		$this->add_control(
			'background_text_mix_blend',
			array(
				'label'     => __( 'Blend Mode', 'elementor' ),
				'type'      => Controls_Manager::SELECT,
				'options'   => array(
					''            => __( 'Normal', 'elementor' ),
					'multiply'    => 'Multiply',
					'screen'      => 'Screen',
					'overlay'     => 'Overlay            ',
					'darken'      => 'Darken',
					'lighten'     => 'Lighten',
					'color-dodge' => 'Color Dodge',
					'saturation'  => 'Saturation',
					'color'       => 'Color',
					'luminosity'  => 'Luminosity',
				),
				'separator' => 'before',
				'selectors' => array(
					'{{WRAPPER}}.premium-bg-text-yes .elementor-widget-container:before' => 'mix-blend-mode: {{VALUE}}',
				),
			)
		);

		$this->add_control(
			'background_text_zindex',
			array(
				'label'     => __( 'z-Index', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::NUMBER,
				'min'       => -10,
				'max'       => 20,
				'step'      => 1,
				'selectors' => array(
					'{{WRAPPER}}.premium-bg-text-yes .elementor-widget-container:before' => 'z-index: {{VALUE}}',
				),
			)
		);

		$this->end_controls_section();

	}

	/**
	 * Render Dual Heading widget output on the frontend.
	 *
	 * Written in PHP and used to generate the final HTML.
	 *
	 * @since 1.0.0
	 * @access protected
	 */
	protected function render() {

		$settings = $this->get_settings_for_display();

		$this->add_inline_editing_attributes( 'premium_dual_header_first_header_text' );

		$this->add_inline_editing_attributes( 'premium_dual_header_second_header_text' );

		$first_title_tag = Helper_Functions::validate_html_tag( $settings['premium_dual_header_first_header_tag'] );

		$first_title_text = $settings['premium_dual_header_first_header_text'] . ' ';

		$second_title_text = $settings['premium_dual_header_second_header_text'];

		$first_clip = '';

		$second_clip = '';

		$first_stroke = '';

		$second_stroke = '';

		if ( 'clipped' === $settings['premium_dual_header_first_back_clip'] ) {
			$first_clip = 'premium-dual-header-first-clip';
		}

		if ( 'clipped' === $settings['premium_dual_header_second_back_clip'] ) {
			$second_clip = 'premium-dual-header-second-clip';
		}

		if ( ! empty( $first_clip ) && 'yes' === $settings['premium_dual_header_first_stroke'] ) {
			$first_stroke = ' stroke';
		}

		if ( ! empty( $second_clip ) && 'yes' === $settings['premium_dual_header_second_stroke'] ) {
			$second_stroke = ' stroke';
		}

		$first_grad = 'yes' === $settings['premium_dual_header_first_animated'] ? ' gradient' : '';

		$second_grad = 'yes' === $settings['premium_dual_header_second_animated'] ? ' gradient' : '';

		$full_title = '<' . $first_title_tag . ' class="premium-dual-header-first-header ' . $first_clip . $first_stroke . $first_grad . '"><span class="premium-dual-header-first-span">' . $first_title_text . '</span>';

		if ( ! empty( $second_title_text ) ) {
			$full_title .= '<span class="premium-dual-header-second-header ' . $second_clip . $second_stroke . $second_grad . '">' . $second_title_text . '</span>';
		}

		$full_title .= '</' . $first_title_tag . '> ';

		if ( 'yes' === $settings['premium_dual_header_link_switcher'] ) {

			if ( 'url' === $settings['premium_dual_heading_link_selection'] ) {

				$this->add_link_attributes( 'link', $settings['premium_dual_heading_link'] );
			} else {

				$this->add_render_attribute( 'link', 'href', get_permalink( $settings['premium_dual_heading_existing_link'] ) );
			}
		}

		$this->add_render_attribute( 'container', 'class', 'premium-dual-header-container' );

		if ( 'yes' === $settings['background_text_switcher'] ) {
			$this->add_render_attribute(
				'container',
				array(
					'class'           => 'premium-title-bg-text',
					'data-background' => $settings['background_text'],
				)
			);
		}

		?>

		<?php if ( 'yes' === $settings['premium_dual_header_link_switcher'] ) : ?>
		<a <?php echo wp_kses_post( $this->get_render_attribute_string( 'link' ) ); ?>>
		<?php endif; ?>
			<?php echo wp_kses_post( $full_title ); ?>
		<?php if ( 'yes' === $settings['premium_dual_header_link_switcher'] ) : ?>
		</a>
		<?php endif; ?>

		<?php
	}

	/**
	 * Render Dual Heading widget output in the editor.
	 *
	 * Written as a Backbone JavaScript template and used to generate the live preview.
	 *
	 * @since 1.0.0
	 * @access protected
	 */
	protected function content_template() {
		?>
		<#

			view.addInlineEditingAttributes('premium_dual_header_first_header_text');

			view.addInlineEditingAttributes('premium_dual_header_second_header_text');

			var firstTag = elementor.helpers.validateHTMLTag( settings.premium_dual_header_first_header_tag ),

			firstText = settings.premium_dual_header_first_header_text + ' ',

			secondText = settings.premium_dual_header_second_header_text,

			firstClip = '',

			secondClip = '',

			firstStroke = '',

			secondStroke = '';

			if( 'clipped' === settings.premium_dual_header_first_back_clip )
				firstClip = "premium-dual-header-first-clip";

			if( 'clipped' === settings.premium_dual_header_second_back_clip )
				secondClip = "premium-dual-header-second-clip";

			if( 'yes' === settings.premium_dual_header_first_stroke )
				firstStroke = "stroke";

			if( 'yes' === settings.premium_dual_header_second_stroke )
				secondStroke = "stroke";

			var firstGrad = 'yes' === settings.premium_dual_header_first_animated  ? ' gradient' : '',

				secondGrad = 'yes' === settings.premium_dual_header_second_animated ? ' gradient' : '';

				view.addRenderAttribute('first_title', 'class', ['premium-dual-header-first-header', firstClip, firstGrad, firstStroke ] );
				view.addRenderAttribute('second_title', 'class', ['premium-dual-header-second-header', secondClip, secondGrad, secondStroke ] );

			var link = '';
			if( 'yes' === settings.premium_dual_header_link_switcher ) {

				if( 'link' === settings.premium_dual_heading_link_selection ) {

					link = settings.premium_dual_heading_existing_link;

				} else {

					link = settings.premium_dual_heading_link.url;

				}
			}

			view.addRenderAttribute('container', 'class', 'premium-dual-header-container' );

			if( 'yes' === settings.background_text_switcher ) {
				view.addRenderAttribute( 'container', {
					'class': 'premium-title-bg-text',
					'data-background': settings.background_text
				});
			}


		#>

		<# if( 'yes' === settings.premium_dual_header_link_switcher && '' !== link ) { #>
			<a href="{{ link }}">
		<# } #>
		<{{{firstTag}}} {{{ view.getRenderAttributeString('first_title') }}}>
			<span class="premium-dual-header-first-span">{{{ firstText }}}</span>
			<# if ( '' != secondText ) { #>
				<span {{{ view.getRenderAttributeString('second_title') }}}>{{{ secondText }}}</span>
			<# } #>
		</{{{firstTag}}}>
		<# if( 'yes' == settings.premium_dual_header_link_switcher && '' !== link ) { #>
			</a>
		<# } #>

		<?php
	}
}
