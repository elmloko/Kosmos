<?php
/**
 * Premium Vertical Scroll.
 */

namespace PremiumAddons\Widgets;

// Elementor Classes.
use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Repeater;
use Elementor\Core\Kits\Documents\Tabs\Global_Colors;
use Elementor\Core\Kits\Documents\Tabs\Global_Typography;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Box_Shadow;

// PremiumAddons Classes.
use PremiumAddons\Includes\Helper_Functions;
use PremiumAddons\Includes\Premium_Template_Tags;

if ( ! defined( 'ABSPATH' ) ) {
	exit(); // If this file is called directly, abort.
}

/**
 * Class Premium_Vscroll
 */
class Premium_Vscroll extends Widget_Base {

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
		return 'premium-vscroll';
	}

	/**
	 * Retrieve Widget Title.
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function get_title() {
		return __( 'Vertical Scroll', 'premium-addons-for-elementor' );
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
		return 'pa-vscroll';
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
		return array( 'pa', 'premium', 'full', 'section', 'navigation', 'one', 'page' );
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
			'pa-iscroll',
			'pa-slimscroll',
			'pa-vscroll',
		);
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
	 * Register Video Box controls.
	 *
	 * @since 2.7.4
	 * @access protected
	 */
	protected function register_controls() {  // phpcs:ignore PSR2.Methods.MethodDeclaration.Underscore

		$this->start_controls_section(
			'content_templates',
			array(
				'label' => __( 'Content', 'premium-addons-for-elementor' ),
			)
		);

		$this->add_control(
			'template_height_hint',
			array(
				'label' => '<span style="line-height: 1.4em;"><b>Important<br></b></span><ul style="line-height: 1.2"><li>1- Section Height needs to be set to default.</li><li>2- It\'s recommended that templates be the same height.</li><li>3- For navigation menu, you will need to add navigation menu items first</li></ul>',
				'type'  => Controls_Manager::RAW_HTML,

			)
		);

		$this->add_control(
			'content_type',
			array(
				'label'       => __( 'Content Type', 'premium-addons-for-elementor' ),
				'type'        => Controls_Manager::SELECT,
				'description' => __( 'Choose which method you prefer to insert sections.', 'premium-addons-for-elementor' ),
				'options'     => array(
					'templates' => __( 'Elementor Templates', 'premium-addons-for-elementor' ),
					'ids'       => __( 'Section ID', 'premium-addons-for-elementor' ),
				),
				'default'     => 'templates',
				'label_block' => true,
			)
		);

		$temp_repeater = new REPEATER();

		$temp_repeater->add_control(
			'live_temp_content',
			array(
				'label'       => __( 'Template Title', 'premium-addons-for-elementor' ),
				'type'        => Controls_Manager::TEXT,
				'classes'     => 'premium-live-temp-title control-hidden',
				'label_block' => true,
			)
		);

		$temp_repeater->add_control(
			'section_template_live',
			array(
				'type'        => Controls_Manager::BUTTON,
				'label_block' => true,
				'button_type' => 'default papro-btn-block',
				'text'        => __( 'Create / Edit Template', 'premium-addons-for-elementor' ),
				'event'       => 'createLiveTemp',
			)
		);

		$temp_repeater->add_control(
			'section_template',
			array(
				'label'       => __( 'OR Select Existing Template', 'premium-addons-for-elementor' ),
				'type'        => Controls_Manager::SELECT2,
				'classes'     => 'premium-live-temp-label',
				'options'     => $this->getTemplateInstance()->get_elementor_page_list(),
				'multiple'    => false,
				'label_block' => true,
			)
		);

		$temp_repeater->add_control(
			'template_id',
			array(
				'label'       => __( 'Section ID', 'premium-addons-for-elementor' ),
				'type'        => Controls_Manager::TEXT,
				'description' => __( 'Use this option to add unique ID to your template section', 'premium-addons-for-elementor' ),
				'dynamic'     => array( 'active' => true ),
			)
		);

		$this->add_control(
			'section_repeater',
			array(
				'label'       => __( 'Sections', 'premium-addons-for-elementor' ),
				'type'        => Controls_Manager::REPEATER,
				'fields'      => $temp_repeater->get_controls(),
				'condition'   => array(
					'content_type' => 'templates',
				),
				'title_field' => '{{{  "" !== section_template ? section_template : "Live Template" }}}',
			)
		);

		$id_repeater = new REPEATER();

		$id_repeater->add_control(
			'section_id',
			array(
				'label'   => __( 'Section ID', 'premium-addons-for-elementor' ),
				'type'    => Controls_Manager::TEXT,
				'dynamic' => array( 'active' => true ),
			)
		);

		$this->add_control(
			'id_repeater',
			array(
				'label'       => __( 'Sections', 'premium-addons-for-elementor' ),
				'type'        => Controls_Manager::REPEATER,
				'fields'      => $id_repeater->get_controls(),
				'condition'   => array(
					'content_type' => 'ids',
				),
				'title_field' => '{{{ section_id }}}',
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'nav_menu',
			array(
				'label' => __( 'Navigation', 'premium-addons-for-elementor' ),
			)
		);

		$this->add_control(
			'nav_menu_switch',
			array(
				'label'       => __( 'Navigation Menu', 'premium-addons-for-elementor' ),
				'type'        => Controls_Manager::SWITCHER,
				'description' => __( 'This option works only on the frontend', 'premium-addons-for-elementor' ),
			)
		);

		$this->add_control(
			'navigation_menu_pos',
			array(
				'label'     => __( 'Position', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::SELECT,
				'options'   => array(
					'left'  => __( 'Left', 'premium-addons-for-elementor' ),
					'right' => __( 'Right', 'premium-addons-for-elementor' ),
				),
				'default'   => 'left',
				'condition' => array(
					'nav_menu_switch' => 'yes',
				),
			)
		);

		$this->add_responsive_control(
			'navigation_menu_pos_offset_top',
			array(
				'label'      => __( 'Offset Top', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', '%', 'em' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-vscroll-nav-menu' => 'top: {{SIZE}}{{UNIT}};',
				),
				'condition'  => array(
					'nav_menu_switch' => 'yes',
				),
			)
		);

		$this->add_responsive_control(
			'navigation_menu_pos_offset_left',
			array(
				'label'      => __( 'Offset Left', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', '%', 'em' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-vscroll-nav-menu.left' => 'left: {{SIZE}}{{UNIT}};',
				),
				'condition'  => array(
					'nav_menu_switch'     => 'yes',
					'navigation_menu_pos' => 'left',
				),
			)
		);

		$this->add_responsive_control(
			'navigation_menu_pos_offset_right',
			array(
				'label'      => __( 'Offset Right', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', '%', 'em' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-vscroll-nav-menu.right' => 'right: {{SIZE}}{{UNIT}};',
				),
				'condition'  => array(
					'nav_menu_switch'     => 'yes',
					'navigation_menu_pos' => 'right',
				),
			)
		);

		$nav_repeater = new REPEATER();

		$nav_repeater->add_control(
			'nav_menu_item',
			array(
				'label'   => __( 'List Item', 'premium-addons-for-elementor' ),
				'type'    => Controls_Manager::TEXT,
				'dynamic' => array( 'active' => true ),
			)
		);

		$this->add_control(
			'nav_menu_repeater',
			array(
				'label'       => __( 'Menu Items', 'premium-addons-for-elementor' ),
				'type'        => Controls_Manager::REPEATER,
				'fields'      => $nav_repeater->get_controls(),
				'title_field' => '{{{ nav_menu_item }}}',
				'condition'   => array(
					'nav_menu_switch' => 'yes',
				),
			)
		);

		$this->add_control(
			'navigation_dots',
			array(
				'label'        => __( 'Navigation Dots', 'premium-addons-for-elementor' ),
				'type'         => Controls_Manager::SWITCHER,
				'default'      => 'yes',
				'separator'    => 'before',
				'prefix_class' => 'premium-vscroll-nav-dots-',
			)
		);

		$this->add_control(
			'navigation_dots_pos',
			array(
				'label'     => __( 'Horizontal Position', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::SELECT,
				'options'   => array(
					'left'  => __( 'Left', 'premium-addons-for-elementor' ),
					'right' => __( 'Right', 'premium-addons-for-elementor' ),
				),
				'default'   => 'right',
				'condition' => array(
					'navigation_dots' => 'yes',
				),
			)
		);

		$this->add_control(
			'navigation_dots_v_pos',
			array(
				'label'     => __( 'Vertical Position', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::SELECT,
				'options'   => array(
					'top'    => __( 'Top', 'premium-addons-for-elementor' ),
					'middle' => __( 'Middle', 'premium-addons-for-elementor' ),
					'bottom' => __( 'Bottom', 'premium-addons-for-elementor' ),
				),
				'default'   => 'middle',
				'condition' => array(
					'navigation_dots' => 'yes',
				),
			)
		);

		$this->add_control(
			'dots_shape',
			array(
				'label'     => __( 'Shape', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::SELECT,
				'options'   => array(
					'circ'  => __( 'Circles', 'premium-addons-for-elementor' ),
					'lines' => __( 'Lines', 'premium-addons-for-elementor' ),
				),
				'default'   => 'circ',
				'condition' => array(
					'navigation_dots' => 'yes',
				),
			)
		);

		$this->add_control(
			'dots_tooltips_switcher',
			array(
				'label'     => __( 'Tooltips Text', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::SWITCHER,
				'default'   => 'yes',
				'condition' => array(
					'navigation_dots' => 'yes',
				),
			)
		);

		$this->add_control(
			'dots_tooltips',
			array(
				'label'       => __( 'Dots Tooltips Text', 'premium-addons-for-elementor' ),
				'type'        => Controls_Manager::TEXT,
				'dynamic'     => array( 'active' => true ),
				'description' => __( 'Add text for each navigation dot separated by \',\'', 'premium-addons-for-elementor' ),
				'condition'   => array(
					'navigation_dots'        => 'yes',
					'dots_tooltips_switcher' => 'yes',
				),
			)
		);

		$this->add_control(
			'dots_animation',
			array(
				'label'              => __( 'Entrance Animation', 'premium-addons-for-elementor' ),
				'type'               => Controls_Manager::ANIMATION,
				'frontend_available' => true,
				'render_type'        => 'template',
				'condition'          => array(
					'navigation_dots' => 'yes',
				),
			)
		);

		$this->add_control(
			'dots_animation_duration',
			array(
				'label'     => __( 'Animation Duration', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => '',
				'options'   => array(
					'slow' => __( 'Slow', 'premium-addons-for-elementor' ),
					''     => __( 'Normal', 'premium-addons-for-elementor' ),
					'fast' => __( 'Fast', 'premium-addons-for-elementor' ),
				),
				'condition' => array(
					'navigation_dots' => 'yes',
					'dots_animation!' => '',
				),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'advanced_settings',
			array(
				'label' => __( 'Scroll Settings', 'premium-addons-for-elementor' ),
			)
		);

		$this->add_control(
			'scroll_effect',
			array(
				'label'   => __( 'Scroll Effect', 'premium-addons-for-elementor' ),
				'type'    => Controls_Manager::SELECT,
				'options' => array(
					'default'   => __( 'Default', 'premium-addons-for-elementor' ),
					'parallax'  => __( 'Parallax', 'premium-addons-for-elementor' ),
					'scaleDown' => __( 'Zoomed Parallax', 'premium-addons-for-elementor' ),
					'rotate'    => __( 'Cube', 'premium-addons-for-elementor' ),
				),
				'default' => 'default',
			)
		);

		$this->add_control(
			'cube_effect_note',
			array(
				'raw'             => __( 'Full Section scroll option is enabled by default for Cube effect for better UX.', 'premium-addons-for-elemeentor' ),
				'type'            => Controls_Manager::RAW_HTML,
				'content_classes' => 'elementor-panel-alert elementor-panel-alert-info',
				'condition'       => array(
					'scroll_effect' => 'rotate',
				),
			)
		);

		$this->add_control(
			'new_effect_note',
			array(
				'raw'             => __(
					'Please note that the
				animation will automatically be changed to default on touch devices for better performance.',
					'premium-addons-for-elemeentor'
				),
				'type'            => Controls_Manager::RAW_HTML,
				'content_classes' => 'elementor-panel-alert elementor-panel-alert-info',
				'condition'       => array(
					'scroll_effect!' => 'default',
				),
			)
		);

		$this->add_control(
			'scroll_speed',
			array(
				'label'       => __( 'Scroll Speed (sec)', 'premium-addons-for-elementor' ),
				'type'        => Controls_Manager::NUMBER,
				'description' => __( 'Set scolling speed in seconds, default: 0.7', 'premium-addons-for-elementor' ),
			)
		);

		$this->add_control(
			'scroll_offset',
			array(
				'label'     => __( 'Scroll Offset', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::NUMBER,
				'condition' => array(
					'scroll_effect!' => array( 'rotate', 'scaleDown' ),
				),
			)
		);

		$this->add_control(
			'full_section',
			array(
				'label'     => __( 'Full Section Scroll', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::SWITCHER,
				'default'   => 'yes',
				'condition' => array(
					'scroll_effect!' => 'rotate',
				),
			)
		);

		$this->add_control(
			'save_state',
			array(
				'label'       => __( 'Save to Browser History', 'premium-addons-for-elementor' ),
				'type'        => Controls_Manager::SWITCHER,
				'description' => __( 'Enabling this option will save the current section ID to the browser history', 'premium-addons-for-elementor' ),
				'default'     => 'yes',
			)
		);

		$this->add_control(
			'full_section_touch',
			array(
				'label'     => __( 'Enable Full Section Scroll on Touch Devices', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::SWITCHER,
				'condition' => array(
					'full_section'   => 'yes',
					'scroll_effect!' => 'rotate',
				),
			)
		);

		$this->add_control(
			'full_section_overflow',
			array(
				'label'        => __( 'Check Content Overflow', 'premium-addons-for-elementor' ),
				'type'         => Controls_Manager::SWITCHER,
				'description'  => __( 'Enable this option to check if sections height is larger than screen height and add a scroll bar for the content', 'premium-addons-for-elementor' ),
				'condition'    => array(
					'full_section'   => 'yes',
					'scroll_effect!' => 'rotate',
				),
				'separator'    => 'before',
				'default'      => 'true',
				'return_value' => 'true',
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_pa_docs',
			array(
				'label' => __( 'Helpful Documentations', 'premium-addons-for-elementor' ),
			)
		);

		$doc_url = Helper_Functions::get_campaign_link( 'https://premiumaddons.com/docs/how-to-create-elementor-template-to-be-used-with-premium-addons', 'editor-page', 'wp-editor', 'get-support' );
		$title   = __( 'How to create an Elementor template to be used in Premium Vertical Scroll »', 'premium-addons-for-elementor' );

		$this->add_control(
			'doc_1',
			array(
				'type'            => Controls_Manager::RAW_HTML,
				'raw'             => sprintf( '<a href="%s" target="_blank">%s</a>', $doc_url, $title ),
				'content_classes' => 'editor-pa-doc',
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'navigation_style',
			array(
				'label'     => __( 'Navigation Dots', 'premium-addons-for-elementor' ),
				'tab'       => CONTROLS_MANAGER::TAB_STYLE,
				'condition' => array(
					'navigation_dots' => 'yes',
				),
			)
		);

		$this->start_controls_tabs( 'navigation_style_tabs' );

		$this->start_controls_tab(
			'tooltips_style_tab',
			array(
				'label'     => __( 'Tooltips', 'premium-addons-for-elementor' ),
				'condition' => array(
					'dots_tooltips_switcher' => 'yes',
				),
			)
		);

		$this->add_control(
			'tooltips_color',
			array(
				'label'     => __( 'Tooltips Text Color', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'global'    => array(
					'default' => Global_Colors::COLOR_PRIMARY,
				),
				'selectors' => array(
					'{{WRAPPER}} .premium-vscroll-tooltip' => 'color: {{VALUE}};',
				),
				'condition' => array(
					'dots_tooltips_switcher' => 'yes',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'      => 'tooltips_typography',
				'global'    => array(
					'default' => Global_Typography::TYPOGRAPHY_PRIMARY,
				),
				'selector'  => '{{WRAPPER}} .premium-vscroll-tooltip span',
				'condition' => array(
					'dots_tooltips_switcher' => 'yes',
				),
			)
		);

		$this->add_control(
			'tooltips_background',
			array(
				'label'     => __( 'Tooltips Background', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'global'    => array(
					'default' => Global_Colors::COLOR_PRIMARY,
				),
				'selectors' => array(
					'{{WRAPPER}} .premium-vscroll-tooltip' => 'background-color: {{VALUE}};',
					'{{WRAPPER}} .premium-vscroll-inner .premium-vscroll-dots.right .premium-vscroll-tooltip::after' => 'border-left-color: {{VALUE}}',
					'{{WRAPPER}} .premium-vscroll-inner .premium-vscroll-dots.left .premium-vscroll-tooltip::after' => 'border-right-color: {{VALUE}}',
				),
				'condition' => array(
					'dots_tooltips_switcher' => 'yes',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'      => 'tooltips_border',
				'selector'  => '{{WRAPPER}} .premium-vscroll-tooltip',
				'condition' => array(
					'dots_tooltips_switcher' => 'yes',
				),
			)
		);

		$this->add_control(
			'tooltips_border_radius',
			array(
				'label'      => __( 'Border Radius', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-vscroll-tooltip' => 'border-radius: {{SIZE}}{{UNIT}};',
				),
				'condition'  => array(
					'dots_tooltips_switcher' => 'yes',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name'      => 'tooltips_shadow',
				'selector'  => '{{WRAPPER}} .premium-vscroll-tooltip',
				'condition' => array(
					'dots_tooltips_switcher' => 'yes',
				),
			)
		);

		$this->add_responsive_control(
			'tooltips_margin',
			array(
				'label'      => __( 'Margin', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-vscroll-tooltip' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
				'condition'  => array(
					'dots_tooltips_switcher' => 'yes',
				),
			)
		);

		$this->add_responsive_control(
			'tooltips_padding',
			array(
				'label'      => __( 'Padding', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-vscroll-tooltip' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
				'condition'  => array(
					'dots_tooltips_switcher' => 'yes',
				),
			)
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'dots_style_tab',
			array(
				'label' => __( 'Dots', 'premium-addons-for-elementor' ),
			)
		);

		$this->add_control(
			'dots_color',
			array(
				'label'     => __( 'Dots Color', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'global'    => array(
					'default' => Global_Colors::COLOR_PRIMARY,
				),
				'selectors' => array(
					'{{WRAPPER}} .premium-vscroll-dots .premium-vscroll-nav-link span'  => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'active_dot_color',
			array(
				'label'     => __( 'Active Dot Color', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'global'    => array(
					'default' => Global_Colors::COLOR_SECONDARY,
				),
				'selectors' => array(
					'{{WRAPPER}} .premium-vscroll-dots li.active .premium-vscroll-nav-link span'  => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'dots_border_color',
			array(
				'label'     => __( 'Dots Border Color', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'global'    => array(
					'default' => Global_Colors::COLOR_SECONDARY,
				),
				'selectors' => array(
					'{{WRAPPER}} .premium-vscroll-dots .premium-vscroll-nav-link span'  => 'border-color: {{VALUE}};',
				),
			)
		);

		$this->add_responsive_control(
			'dots_border_radius',
			array(
				'label'      => __( 'Border Radius', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-vscroll-dots .premium-vscroll-nav-link span' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}',
				),
			)
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'container_style_tab',
			array(
				'label' => __( 'Container', 'premium-addons-for-elementor' ),
			)
		);

		$this->add_control(
			'navigation_background',
			array(
				'label'     => __( 'Background Color', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'global'    => array(
					'default' => Global_Colors::COLOR_PRIMARY,
				),
				'selectors' => array(
					'{{WRAPPER}} .premium-vscroll-dots' => 'background-color: {{VALUE}}',
				),
			)
		);

		$this->add_control(
			'navigation_border_radius',
			array(
				'label'      => __( 'Border Radius', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-vscroll-dots' => 'border-radius: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'label'    => __( 'Shadow', 'premium-addons-for-elementor' ),
				'name'     => 'navigation_box_shadow',
				'selector' => '{{WRAPPER}} .premium-vscroll-dots',
			)
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();

		$this->start_controls_section(
			'navigation_menu_style',
			array(
				'label'     => __( 'Navigation Menu', 'premium-addons-for-elementor' ),
				'tab'       => CONTROLS_MANAGER::TAB_STYLE,
				'condition' => array(
					'nav_menu_switch' => 'yes',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'navigation_items_typography',
				'selector' => '{{WRAPPER}} .premium-vscroll-nav-menu .premium-vscroll-nav-item .premium-vscroll-nav-link',
			)
		);

		$this->start_controls_tabs( 'navigation_menu_style_tabs' );

		$this->start_controls_tab(
			'normal_style_tab',
			array(
				'label' => __( 'Normal', 'premium-addons-for-elementor' ),
			)
		);

		$this->add_control(
			'normal_color',
			array(
				'label'     => __( 'Text Color', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'global'    => array(
					'default' => Global_Colors::COLOR_PRIMARY,
				),
				'selectors' => array(
					'{{WRAPPER}} .premium-vscroll-nav-menu .premium-vscroll-nav-item .premium-vscroll-nav-link'  => 'color: {{VALUE}}',
				),
			)
		);

		$this->add_control(
			'normal_hover_color',
			array(
				'label'     => __( 'Text Hover Color', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'global'    => array(
					'default' => Global_Colors::COLOR_PRIMARY,
				),
				'selectors' => array(
					'{{WRAPPER}} .premium-vscroll-nav-menu .premium-vscroll-nav-item .premium-vscroll-nav-link:hover'  => 'color: {{VALUE}}',
				),
			)
		);

		$this->add_control(
			'normal_background',
			array(
				'label'     => __( 'Background Color', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'global'    => array(
					'default' => Global_Colors::COLOR_SECONDARY,
				),
				'selectors' => array(
					'{{WRAPPER}} .premium-vscroll-nav-menu .premium-vscroll-nav-item'  => 'background-color: {{VALUE}}',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'label'    => __( 'Shadow', 'premium-addons-for-elementor' ),
				'name'     => 'normal_shadow',
				'selector' => '{{WRAPPER}} .premium-vscroll-nav-menu .premium-vscroll-nav-item',
			)
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'active_style_tab',
			array(
				'label' => __( 'Active', 'premium-addons-for-elementor' ),
			)
		);

		$this->add_control(
			'active_color',
			array(
				'label'     => __( 'Text Color', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'global'    => array(
					'default' => Global_Colors::COLOR_SECONDARY,
				),
				'selectors' => array(
					'{{WRAPPER}} .premium-vscroll-nav-menu .premium-vscroll-nav-item.active .premium-vscroll-nav-link'  => 'color: {{VALUE}}',
				),
			)
		);

		$this->add_control(
			'active_hover_color',
			array(
				'label'     => __( 'Text Hover Color', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'global'    => array(
					'default' => Global_Colors::COLOR_SECONDARY,
				),
				'selectors' => array(
					'{{WRAPPER}} .premium-vscroll-nav-menu .premium-vscroll-nav-item.active .premium-vscroll-nav-link:hover'  => 'color: {{VALUE}}',
				),
			)
		);

		$this->add_control(
			'active_background',
			array(
				'label'     => __( 'Background Color', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'global'    => array(
					'default' => Global_Colors::COLOR_PRIMARY,
				),
				'selectors' => array(
					'{{WRAPPER}} .premium-vscroll-nav-menu .premium-vscroll-nav-item.active'  => 'background-color: {{VALUE}}',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'label'    => __( 'Shadow', 'premium-addons-for-elementor' ),
				'name'     => 'active_shadow',
				'selector' => '{{WRAPPER}} .premium-vscroll-nav-menu .premium-vscroll-nav-item.active',
			)
		);

		$this->end_controls_tabs();

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'      => 'navigation_items_border',
				'selector'  => '{{WRAPPER}} .premium-vscroll-nav-menu .premium-vscroll-nav-item',
				'separator' => 'before',
			)
		);

		$this->add_control(
			'navigation_items_border_radius',
			array(
				'label'      => __( 'Border Radius', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-vscroll-nav-menu .premium-vscroll-nav-item'  => 'border-radius: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'navigation_items_margin',
			array(
				'label'      => __( 'Margin', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-vscroll-nav-menu .premium-vscroll-nav-item' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'navigation_items_padding',
			array(
				'label'      => __( 'Padding', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-vscroll-nav-menu .premium-vscroll-nav-item .premium-vscroll-nav-link' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_section();

	}

	/**
	 * Render vertical scroll widget output on the frontend.
	 *
	 * Written in PHP and used to generate the final HTML.
	 *
	 * @since 2.7.4
	 * @access protected
	 */
	protected function render() {

		$settings = $this->get_settings_for_display();

		$id = $this->get_id();

		$dots_text = explode( ',', $settings['dots_tooltips'] );

		$this->add_render_attribute(
			'vscroll_wrapper',
			array(
				'class' => 'premium-vscroll-wrap',
				'id'    => 'premium-vscroll-wrap-' . $id,
			)
		);

		$this->add_render_attribute(
			'vscroll_inner',
			array(
				'class' => 'premium-vscroll-inner',
				'id'    => 'premium-vscroll-' . $id,
			)
		);

		$this->add_render_attribute(
			'vertical_scroll_dots',
			'class',
			array(
				'premium-vscroll-dots',
				'premium-vscroll-dots-hide',
				$settings['navigation_dots_pos'],
				$settings['navigation_dots_v_pos'],
				$settings['dots_shape'],
			)
		);

		if ( '' !== $settings['dots_animation'] ) {
			$this->add_render_attribute( 'vertical_scroll_dots', 'class', 'elementor-invisible' );
		}

		$this->add_render_attribute( 'vscroll_dots_list', 'class', array( 'premium-vscroll-dots-list' ) );

		$this->add_render_attribute(
			'vertical_scroll_menu',
			array(
				'id'    => 'premium-vscroll-nav-menu-' . $id,
				'class' => array(
					'premium-vscroll-nav-menu',
					$settings['navigation_menu_pos'],
				),
			)
		);

		$this->add_render_attribute(
			'vscroll_sections_wrap',
			array(
				'class' => 'premium-vscroll-sections-wrap',
				'id'    => 'premium-vscroll-sections-wrap-' . $id,
			)
		);

		if ( 'default' !== $settings['scroll_effect'] && ! wp_is_mobile() ) {

			$this->add_render_attribute(
				'vscroll_sections_wrap',
				array(
					'data-animation' => $settings['scroll_effect'],
					'data-hijacking' => 'off',
				)
			);

		}

		$vscroll_settings = array(
			'id'                => $id,
			'speed'             => ! empty( $settings['scroll_speed'] ) ? $settings['scroll_speed'] * 1000 : 700,
			'offset'            => ! empty( $settings['scroll_offset'] ) ? $settings['scroll_offset'] : 0,
			'tooltips'          => 'yes' === $settings['dots_tooltips_switcher'] ? true : false,
			'dotsText'          => $dots_text,
			'dotsPos'           => $settings['navigation_dots_pos'],
			'dotsVPos'          => $settings['navigation_dots_v_pos'],
			'fullSection'       => 'rotate' === $settings['scroll_effect'] || 'yes' === $settings['full_section'] ? true : false,
			'fullTouch'         => 'rotate' === $settings['scroll_effect'] || 'yes' === $settings['full_section_touch'] ? true : false,
			'fullCheckOverflow' => 'rotate' === $settings['scroll_effect'] || $settings['full_section_overflow'],
			'addToHistory'      => 'yes' === $settings['save_state'] ? true : false,
			'animation'         => $settings['dots_animation'],
			'duration'          => $settings['dots_animation_duration'],
		);

		$templates = 'templates' === $settings['content_type'] ? $settings['section_repeater'] : $settings['id_repeater'];

		$nav_items = $settings['nav_menu_repeater'];

		$this->add_render_attribute( 'vscroll_wrapper', 'data-settings', wp_json_encode( $vscroll_settings ) );

		?>

		<div <?php echo wp_kses_post( $this->get_render_attribute_string( 'vscroll_wrapper' ) ); ?>>
			<?php if ( 'yes' === $settings['nav_menu_switch'] ) : ?>
				<ul <?php echo wp_kses_post( $this->get_render_attribute_string( 'vertical_scroll_menu' ) ); ?>>
					<?php
					foreach ( $nav_items as $index => $item ) :
						$section_id = $this->get_template_id( $index );
						?>
						<li class="premium-vscroll-nav-item" data-menuanchor="<?php echo esc_attr( $section_id ); ?>">
							<div class="premium-vscroll-nav-link">
								<?php echo wp_kses_post( $item['nav_menu_item'] ); ?>
							</div>
						</li>
					<?php endforeach; ?>
				</ul>
			<?php endif; ?>
			<div <?php echo wp_kses_post( $this->get_render_attribute_string( 'vscroll_inner' ) ); ?>>
				<div <?php echo wp_kses_post( $this->get_render_attribute_string( 'vertical_scroll_dots' ) ); ?>>
					<ul <?php echo wp_kses_post( $this->get_render_attribute_string( 'vscroll_dots_list' ) ); ?>>
						<?php
						foreach ( $templates as $index => $section ) :
							$section_id = $this->get_template_id( $index );
							?>
							<li data-index="<?php echo esc_attr( $index ); ?>" data-menuanchor="<?php echo esc_attr( $section_id ); ?>" class="premium-vscroll-dot-item">
								<div class="premium-vscroll-nav-link"><span></span></div>
							</li>
						<?php endforeach; ?>
					</ul>
				</div>
				<?php if ( 'templates' === $settings['content_type'] ) : ?>
					<div <?php echo wp_kses_post( $this->get_render_attribute_string( 'vscroll_sections_wrap' ) ); ?>>

						<?php
						$temp_id = '';
						foreach ( $templates as $index => $section ) :
							$section_id = $this->get_template_id( $index );

							$this->add_render_attribute(
								'section_' . $index,
								array(
									'id'    => $section_id,
									'class' => array(
										'premium-vscroll-temp',
										'premium-vscroll-temp-' . $id,
									),
								)
							);

							if ( 0 === $index && 'rotate' === $settings['scroll_effect'] ) {
								$this->add_render_attribute( 'section_' . $index, 'class', 'visible' );
							}

							?>
							<div <?php echo wp_kses_post( $this->get_render_attribute_string( 'section_' . $index ) ); ?>>
								<?php
									$temp_id = empty( $section['section_template'] ) ? $section['live_temp_content'] : $section['section_template'];
									echo $this->getTemplateInstance()->get_template_content( $temp_id ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
								?>
							</div>
						<?php endforeach; ?>
					</div>
				<?php endif; ?>
			</div>
		</div>

		<?php
	}

	/**
	 * Get template ID
	 *
	 * @since 3.21.0
	 * @access protected
	 *
	 * @param string $index template index.
	 *
	 * @return string $id template ID
	 */
	protected function get_template_id( $index ) {

		$settings = $this->get_settings_for_display();

		$check_type = 'templates' === $settings['content_type'] ? true : false;

		$templates = $check_type ? $settings['section_repeater'] : $settings['id_repeater'];

		if ( ! $check_type ) {

			$id = $templates[ $index ]['section_id'];

			return $id;
		}

		$widget_id = $this->get_id();

		$id = 'section_' . $widget_id . $index;

		if ( ! empty( $templates[ $index ]['template_id'] ) ) {
			$id = $templates[ $index ]['template_id'];
		}

		return $id;

	}
}
