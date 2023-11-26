<?php
/**
 * Premium Carousel.
 */

namespace PremiumAddons\Widgets;

// Elementor Classes.
use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Icons_Manager;
use Elementor\Repeater;
use Elementor\Core\Kits\Documents\Tabs\Global_Colors;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Background;

use Elementor\Core\Kits\Documents\Tabs\Global_Typography;

// PremiumAddons Classes.
use PremiumAddons\Includes\Helper_Functions;
use PremiumAddons\Includes\Premium_Template_Tags;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // No access of directly access.
}

/**
 * Class Premium_Carousel
 */
class Premium_Carousel extends Widget_Base {


	/**
	 * Template Instance
	 *
	 * @var template_instance
	 */
	protected $template_instance;

	/**
	 * Get Elementor Helper Instance.
	 *
	 * @since  1.0.0
	 * @access public
	 */
	public function getTemplateInstance() {
		 return $this->template_instance = Premium_Template_Tags::getInstance();
	}

	/**
	 * Retrieve Widget Name.
	 *
	 * @since  1.0.0
	 * @access public
	 */
	public function get_name() {
		return 'premium-carousel-widget';
	}

	/**
	 * Retrieve Widget Title.
	 *
	 * @since  1.0.0
	 * @access public
	 */
	public function get_title() {
		return __( 'Carousel', 'premium-addons-for-elementor' );
	}

	/**
	 * Retrieve Widget Icon.
	 *
	 * @since  1.0.0
	 * @access public
	 *
	 * @return string widget icon.
	 */
	public function get_icon() {
		return 'pa-carousel';
	}

	/**
	 * Widget preview refresh button.
	 *
	 * @since  1.0.0
	 * @access public
	 */
	public function is_reload_preview_required() {
		return true;
	}

	/**
	 * Retrieve Widget Dependent CSS.
	 *
	 * @since  1.0.0
	 * @access public
	 *
	 * @return array CSS style handles.
	 */
	public function get_style_depends() {
		return array(
			'font-awesome-5-all',
			'pa-slick',
			'premium-addons',
		);
	}

	/**
	 * Retrieve Widget Dependent JS.
	 *
	 * @since  1.0.0
	 * @access public
	 *
	 * @return array JS script handles.
	 */
	public function get_script_depends() {
		return array(
			'pa-slick',
			'premium-addons',
		);
	}

	/**
	 * Retrieve Widget Keywords.
	 *
	 * @since  1.0.0
	 * @access public
	 *
	 * @return string Widget keywords.
	 */
	public function get_keywords() {
		return array( 'pa', 'premium', 'slider', 'advanced', 'testimonial' );
	}

	/**
	 * Retrieve Widget Categories.
	 *
	 * @since  1.5.1
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
	 * Register Carousel controls.
	 *
	 * @since  1.0.0
	 * @access protected
	 */
	protected function register_controls() { // phpcs:ignore PSR2.Methods.MethodDeclaration.Underscore

		$this->start_controls_section(
			'premium_carousel_global_settings',
			array(
				'label' => __( 'Carousel', 'premium-addons-for-elementor' ),
			)
		);

		$this->add_control(
			'premium_carousel_content_type',
			array(
				'label'       => __( 'Content Type', 'premium-addons-for-elementor' ),
				'description' => __( 'How templates are selected', 'premium-addons-for-elementor' ),
				'type'        => Controls_Manager::SELECT,
				'options'     => array(
					'select'   => __( 'Select Field', 'premium-addons-for-elementor' ),
					'repeater' => __( 'Repeater', 'premium-addons-for-elementor' ),
				),
				'default'     => 'select',
			)
		);

		$this->add_control(
			'premium_carousel_slider_content',
			array(
				'label'       => __( 'Templates', 'premium-addons-for-elementor' ),
				'description' => __( 'Slider content is a template which you can choose from Elementor library. Each template will be a slider content', 'premium-addons-for-elementor' ),
				'type'        => Controls_Manager::SELECT2,
				'options'     => $this->getTemplateInstance()->get_elementor_page_list(),
				'multiple'    => true,
				'label_block' => true,
				'condition'   => array(
					'premium_carousel_content_type' => 'select',
				),
			)
		);

		$repeater = new REPEATER();

		$repeater->add_control(
			'live_temp_content',
			array(
				'label'       => __( 'Template Title', 'premium-addons-for-elementor' ),
				'type'        => Controls_Manager::TEXT,
				'classes'     => 'premium-live-temp-title control-hidden',
				'label_block' => true,
			)
		);

		$repeater->add_control(
			'premium_carousel_repeater_item_live',
			array(
				'type'        => Controls_Manager::BUTTON,
				'label_block' => true,
				'button_type' => 'default papro-btn-block',
				'text'        => __( 'Create / Edit Template', 'premium-addons-for-elementor' ),
				'event'       => 'createLiveTemp',
			)
		);

		$repeater->add_control(
			'premium_carousel_repeater_item',
			array(
				'label'       => __( 'OR Select Existing Template', 'premium-addons-for-elementor' ),
				'type'        => Controls_Manager::SELECT2,
				'classes'     => 'premium-live-temp-label',
				'label_block' => true,
				'separator'   => 'after',
				'options'     => $this->getTemplateInstance()->get_elementor_page_list(),
			)
		);

		$repeater->add_control(
			'custom_navigation',
			array(
				'label'       => __( 'Custom Navigation Element Selector', 'premium-addons-for-elementor' ),
				'type'        => Controls_Manager::TEXT,
				'label_block' => true,
				'description' => __( 'Use this to add an element selector to be used to navigate to this slide. For example #slide-1', 'premium-addons-for-elementor' ),
			)
		);

		$this->add_control(
			'premium_carousel_templates_repeater',
			array(
				'label'       => __( 'Templates', 'premium-addons-for-elementor' ),
				'type'        => Controls_Manager::REPEATER,
				'fields'      => $repeater->get_controls(),
				'condition'   => array(
					'premium_carousel_content_type' => 'repeater',
				),
				'title_field' => 'Template: {{{  "" !== premium_carousel_repeater_item ? premium_carousel_repeater_item : "Live Template" }}}',
			)
		);

		$this->add_control(
			'premium_carousel_slider_type',
			array(
				'label'       => __( 'Type', 'premium-addons-for-elementor' ),
				'description' => __( 'Set a navigation type', 'premium-addons-for-elementor' ),
				'type'        => Controls_Manager::SELECT,
				'options'     => array(
					'horizontal' => __( 'Horizontal', 'premium-addons-for-elementor' ),
					'vertical'   => __( 'Vertical', 'premium-addons-for-elementor' ),
				),
				'default'     => 'horizontal',
			)
		);

		$this->add_control(
			'premium_carousel_slides_to_show',
			array(
				'label'     => __( 'Appearance', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'all',
				'separator' => 'before',
				'options'   => array(
					'all'    => __( 'All visible', 'premium-addons-for-elementor' ),
					'single' => __( 'One at a time', 'premium-addons-for-elementor' ),
				),
			)
		);

		$this->add_control(
			'premium_carousel_responsive_desktop',
			array(
				'label'   => __( 'Desktop Slides', 'premium-addons-for-elementor' ),
				'type'    => Controls_Manager::NUMBER,
				'default' => 1,
			)
		);

		$this->add_control(
			'premium_carousel_responsive_tabs',
			array(
				'label'   => __( 'Tabs Slides', 'premium-addons-for-elementor' ),
				'type'    => Controls_Manager::NUMBER,
				'default' => 1,
			)
		);

		$this->add_control(
			'premium_carousel_responsive_mobile',
			array(
				'label'   => __( 'Mobile Slides', 'premium-addons-for-elementor' ),
				'type'    => Controls_Manager::NUMBER,
				'default' => 1,
			)
		);

		$this->add_control(
			'mscroll',
			array(
				'label'       => __( 'Use With Magic Scroll', 'premium-addons-for-elementor' ),
				'type'        => Controls_Manager::SWITCHER,
				'description' => __( 'Enable this option if you want to animate the carousel using ', 'premium-addons-for-elementor' ) . '<a href="https://premiumaddons.com/elementor-magic-scroll-global-addon/" target="_blank">Magic Scroll addon.</a>',
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'premium_carousel_nav_section',
			array(
				'label'     => __( 'Navigation', 'premium-addons-for-elementor' ),
				'condition' => array(
					'mscroll!' => 'yes',
				),
			)
		);

		$this->add_control(
			'premium_carousel_nav_options',
			array(
				'label'   => __( 'Navigation', 'premium-addons-for-elementor' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'dots',
				'options' => array(
					'none'        => __( 'None', 'premium-addons-for-elementor' ),
					'dots'        => __( 'Dots', 'premium-addons-for-elementor' ),
					'fraction'    => __( 'Slide Index', 'premium-addons-for-elementor' ),
					'progressbar' => __( 'Progress Bar', 'premium-addons-for-elementor' ),
					'progress'    => __( 'Animated Progress Bar', 'premium-addons-for-elementor' ),
				),
			)
		);

		$this->add_control(
			'premium_carousel_dot_position',
			array(
				'label'     => __( 'Position', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'below',
				'options'   => array(
					'below' => __( 'Below Slides', 'premium-addons-for-elementor' ),
					'above' => __( 'On Slides', 'premium-addons-for-elementor' ),
				),
				'condition' => array(
					'premium_carousel_nav_options' => 'dots',
				),
			)
		);

		$this->add_responsive_control(
			'premium_carousel_dot_offset',
			array(
				'label'      => __( 'Horizontal Offset', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-carousel-dots-above ul.slick-dots' => 'left: {{SIZE}}{{UNIT}}',
				),
				'condition'  => array(
					'premium_carousel_nav_options'  => 'dots',
					'premium_carousel_dot_position' => 'above',
				),
			)
		);

		$this->add_responsive_control(
			'premium_carousel_dot_voffset',
			array(
				'label'      => __( 'Vertical Offset', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-carousel-dots-above ul.slick-dots' => 'top: {{SIZE}}{{UNIT}}',
					'{{WRAPPER}} .premium-carousel-dots-below ul.slick-dots' => 'bottom: {{SIZE}}{{UNIT}}',
					'{{WRAPPER}} .premium-carousel-nav-fraction' => 'bottom: {{SIZE}}{{UNIT}}',
				),
				'condition'  => array(
					'premium_carousel_nav_options!' => array( 'none', 'progress' ),
				),
			)
		);

		$this->add_control(
			'premium_carousel_navigation_effect',
			array(
				'label'        => __( 'Ripple Effect', 'premium-addons-for-elementor' ),
				'description'  => __( 'Enable a ripple effect when the active dot is hovered/clicked', 'premium-addons-for-elementor' ),
				'type'         => Controls_Manager::SWITCHER,
				'prefix_class' => 'premium-carousel-ripple-',
				'condition'    => array(
					'premium_carousel_nav_options' => 'dots',
				),
			)
		);

		$this->add_control(
			'premium_carousel_navigation_show',
			array(
				'label'       => __( 'Arrows', 'premium-addons-for-elementor' ),
				'description' => __( 'Enable or disable navigation arrows', 'premium-addons-for-elementor' ),
				'type'        => Controls_Manager::SWITCHER,
				'separator'   => 'before',
				'default'     => 'yes',
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'premium_carousel_slides_settings',
			array(
				'label'     => __( 'Slides Settings', 'premium-addons-for-elementor' ),
				'condition' => array(
					'mscroll!' => 'yes',
				),
			)
		);

		$this->add_control(
			'premium_carousel_loop',
			array(
				'label'       => __( 'Infinite Loop', 'premium-addons-for-elementor' ),
				'type'        => Controls_Manager::SWITCHER,
				'description' => __( 'Restart the slider automatically as it passes the last slide', 'premium-addons-for-elementor' ),
				'default'     => 'yes',
			)
		);

		$this->add_control(
			'premium_carousel_fade',
			array(
				'label'       => __( 'Fade', 'premium-addons-for-elementor' ),
				'type'        => Controls_Manager::SWITCHER,
				'description' => __( 'Enable fade transition between slides', 'premium-addons-for-elementor' ),
				'condition'   => array(
					'premium_carousel_slider_type' => 'horizontal',
				),
			)
		);

		$this->add_control(
			'premium_carousel_zoom',
			array(
				'label'     => __( 'Zoom Effect', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::SWITCHER,
				'condition' => array(
					'premium_carousel_fade'        => 'yes',
					'premium_carousel_slider_type' => 'horizontal',
				),
			)
		);

		$this->add_control(
			'premium_carousel_speed',
			array(
				'label'       => __( 'Transition Speed (ms)', 'premium-addons-for-elementor' ),
				'description' => __( 'Set a navigation speed value. The value will be counted in milliseconds (ms)', 'premium-addons-for-elementor' ),
				'type'        => Controls_Manager::NUMBER,
				'default'     => 300,
				'render_type' => 'template',
				'selectors'   => array(
					'{{WRAPPER}} .premium-carousel-scale .slick-slide' => 'transition: all {{VALUE}}ms !important',
					'{{WRAPPER}} .premium-carousel-nav-progressbar-fill' => 'transition-duration: {{VALUE}}ms !important',
				),
			)
		);

		$this->add_control(
			'premium_carousel_autoplay',
			array(
				'label'       => __( 'Autoplay Slides', 'premium-addons-for-elementor' ),
				'description' => __( 'Slide will start automatically', 'premium-addons-for-elementor' ),
				'type'        => Controls_Manager::SWITCHER,
				'default'     => 'yes',
			)
		);

		$this->add_control(
			'premium_carousel_autoplay_speed',
			array(
				'label'       => __( 'Autoplay Speed', 'premium-addons-for-elementor' ),
				'description' => __( 'Autoplay Speed means at which time the next slide should come. Set a value in milliseconds (ms)', 'premium-addons-for-elementor' ),
				'type'        => Controls_Manager::NUMBER,
				'default'     => 5000,
				'condition'   => array(
					'premium_carousel_autoplay' => 'yes',
				),
			)
		);

		$this->add_control(
			'premium_carousel_animation_list',
			array(
				'label'       => __( 'Animations', 'premium-addons-for-elementor' ),
				'type'        => Controls_Manager::HIDDEN,
				'render_type' => 'template',
			)
		);

		$this->add_control(
			'premium_carousel_extra_class',
			array(
				'label'       => __( 'Extra Class', 'premium-addons-for-elementor' ),
				'type'        => Controls_Manager::TEXT,
				'description' => __( 'Add extra class name that will be applied to the carousel, and you can use this class for your customizations.', 'premium-addons-for-elementor' ),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'premium-carousel-advance-settings',
			array(
				'label'     => __( 'Advanced Settings', 'premium-addons-for-elementor' ),
				'condition' => array(
					'mscroll!' => 'yes',
				),
			)
		);

		$this->add_control(
			'premium_carousel_draggable_effect',
			array(
				'label'       => __( 'Draggable Effect', 'premium-addons-for-elementor' ),
				'description' => __( 'Allow the slides to be dragged by mouse click', 'premium-addons-for-elementor' ),
				'type'        => Controls_Manager::SWITCHER,
				'default'     => 'yes',
			)
		);

		$this->add_control(
			'premium_carousel_touch_move',
			array(
				'label'       => __( 'Touch Move', 'premium-addons-for-elementor' ),
				'description' => __( 'Enable slide moving with touch', 'premium-addons-for-elementor' ),
				'type'        => Controls_Manager::SWITCHER,
				'default'     => 'yes',
			)
		);

		$this->add_control(
			'premium_carousel_RTL_Mode',
			array(
				'label'       => __( 'RTL Mode', 'premium-addons-for-elementor' ),
				'description' => __( 'Turn on RTL mode if your language starts from right to left', 'premium-addons-for-elementor' ),
				'type'        => Controls_Manager::SWITCHER,
				'condition'   => array(
					'premium_carousel_slider_type!' => 'vertical',
				),
			)
		);

		$this->add_control(
			'variable_width',
			array(
				'label'       => __( 'Variable Width', 'premium-addons-for-elementor' ),
				'description' => __( 'Allows each slide to have a different width.', 'premium-addons-for-elementor' ),
				'type'        => Controls_Manager::SWITCHER,
			)
		);

		$this->add_control(
			'premium_carousel_adaptive_height',
			array(
				'label'       => __( 'Adaptive Height', 'premium-addons-for-elementor' ),
				'description' => __( 'Adaptive height setting gives each slide a fixed height to avoid huge white space gaps', 'premium-addons-for-elementor' ),
				'type'        => Controls_Manager::SWITCHER,
			)
		);

		$this->add_control(
			'premium_carousel_pausehover',
			array(
				'label'       => __( 'Pause on Hover', 'premium-addons-for-elementor' ),
				'description' => __( 'Pause the slider when mouse hover', 'premium-addons-for-elementor' ),
				'type'        => Controls_Manager::SWITCHER,
			)
		);

		$this->add_control(
			'premium_carousel_center_mode',
			array(
				'label'       => __( 'Center Mode', 'premium-addons-for-elementor' ),
				'description' => __( 'Center mode enables a centered view with partial next/previous slides. Animations and all visible scroll type doesn\'t work with this mode', 'premium-addons-for-elementor' ),
				'type'        => Controls_Manager::SWITCHER,
			)
		);

		$this->add_responsive_control(
			'premium_carousel_space_btw_items',
			array(
				'label'       => __( 'Slides\' Spacing', 'premium-addons-for-elementor' ),
				'description' => __( 'Set a spacing value in pixels (px)', 'premium-addons-for-elementor' ),
				'type'        => Controls_Manager::NUMBER,
				'default'     => '15',
				'selectors'   => array(
					'{{WRAPPER}}' => '--pa-carousel-center-padding: {{VALUE}}',
				),
			)
		);

		$this->add_control(
			'premium_carousel_tablet_breakpoint',
			array(
				'label'       => __( 'Tablet Breakpoint', 'premium-addons-for-elementor' ),
				'description' => __( 'Sets the breakpoint between desktop and tablet devices. Below this breakpoint tablet layout will appear (Default: 1025px).', 'premium-addons-for-elementor' ),
				'type'        => Controls_Manager::NUMBER,
				'default'     => 1025,
			)
		);

		$this->add_control(
			'premium_carousel_mobile_breakpoint',
			array(
				'label'       => __( 'Mobile Breakpoint', 'premium-addons-for-elementor' ),
				'description' => __( 'Sets the breakpoint between tablet and mobile devices. Below this breakpoint mobile layout will appear (Default: 768px).', 'premium-addons-for-elementor' ),
				'type'        => Controls_Manager::NUMBER,
				'default'     => 768,
			)
		);

		$this->add_control(
			'linear_ease',
			array(
				'label' => __( 'Linear Easing', 'premium-addons-for-elementor' ),
				'type'  => Controls_Manager::SWITCHER,
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
			'https://premiumaddons.com/docs/carousel-widget-tutorial/' => __( 'Getting started »', 'premium-addons-for-elementor' ),
			'https://premiumaddons.com/docs/i-can-see-the-first-slide-only-in-carousel-widget' => __( 'Issue: I can see the first slide only »', 'premium-addons-for-elementor' ),
			'https://premiumaddons.com/docs/how-to-create-elementor-template-to-be-used-with-premium-addons' => __( 'How to create an Elementor template to be used in Carousel widget »', 'premium-addons-for-elementor' ),
			'https://premiumaddons.com/docs/why-im-not-able-to-see-elementor-font-awesome-5-icons-in-premium-add-ons/' => __( 'I\'m not able to see Font Awesome icons in the widget »', 'premium-addons-for-elementor' ),
			'https://premiumaddons.com/docs/how-to-add-entrance-animations-to-elementor-elements-in-premium-carousel-widget/' => __( 'How to add entrance animations to the elements inside Premium Carousel Widget »', 'premium-addons-for-elementor' ),
			'https://premiumaddons.com/docs/how-to-use-elementor-widgets-to-navigate-through-carousel-widget-slides/' => __( 'How To Use Elementor Widgets To Navigate Through Carousel Widget Slides »', 'premium-addons-for-elementor' ),
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

		$this->start_controls_section(
			'premium_carousel_navigation_arrows',
			array(
				'label'     => __( 'Navigation Arrows', 'premium-addons-for-elementor' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => array(
					'premium_carousel_navigation_show' => 'yes',
				),
			)
		);

		$this->add_control(
			'custom_left_arrow',
			array(
				'label' => __( 'Custom Previous Icon', 'premium-addons-for-elementor' ),
				'type'  => Controls_Manager::SWITCHER,
			)
		);

		$this->add_control(
			'custom_left_arrow_select',
			array(
				'label'       => __( 'Select Icon', 'premium-addons-for-elementor' ),
				'type'        => Controls_Manager::ICONS,
				'default'     => array(
					'value'   => 'fas fa-arrow-alt-circle-left',
					'library' => 'fa-solid',
				),
				'skin'        => 'inline',
				'condition'   => array(
					'custom_left_arrow' => 'yes',
				),
				'label_block' => false,
			)
		);

		$this->add_control(
			'premium_carousel_arrow_icon_prev_ver',
			array(
				'label'     => __( 'Top Icon', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::CHOOSE,
				'options'   => array(
					'left_arrow_bold'        => array(
						'icon' => 'fas fa-arrow-up',
					),
					'left_arrow_long'        => array(
						'icon' => 'fas fa-long-arrow-alt-up',
					),
					'left_arrow_long_circle' => array(
						'icon' => 'fas fa-arrow-circle-up',
					),
					'left_arrow_angle'       => array(
						'icon' => 'fas fa-angle-up',
					),
					'left_arrow_chevron'     => array(
						'icon' => 'fas fa-chevron-up',
					),
				),
				'default'   => 'left_arrow_angle',
				'condition' => array(
					'premium_carousel_slider_type' => 'vertical',
					'custom_left_arrow!'           => 'yes',
				),
				'toggle'    => false,
			)
		);

		$this->add_control(
			'premium_carousel_arrow_icon_prev',
			array(
				'label'     => __( 'Left Icon', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::CHOOSE,
				'options'   => array(
					'left_arrow_bold'        => array(
						'icon' => 'fas fa-arrow-left',
					),
					'left_arrow_long'        => array(
						'icon' => 'fas fa-long-arrow-alt-left',
					),
					'left_arrow_long_circle' => array(
						'icon' => 'fas fa-arrow-circle-left',
					),
					'left_arrow_angle'       => array(
						'icon' => 'fas fa-angle-left',
					),
					'left_arrow_chevron'     => array(
						'icon' => 'fas fa-chevron-left',
					),
				),
				'default'   => 'left_arrow_angle',
				'condition' => array(
					'premium_carousel_slider_type!' => 'vertical',
					'custom_left_arrow!'            => 'yes',
				),
				'toggle'    => false,
			)
		);

		$this->add_control(
			'custom_right_arrow',
			array(
				'label'     => __( 'Custom Next Icon', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::SWITCHER,
				'separator' => 'before',
			)
		);

		$this->add_control(
			'custom_right_arrow_select',
			array(
				'label'       => __( 'Select Icon', 'premium-addons-for-elementor' ),
				'type'        => Controls_Manager::ICONS,
				'default'     => array(
					'value'   => 'fas fa-arrow-alt-circle-right',
					'library' => 'fa-solid',
				),
				'skin'        => 'inline',
				'condition'   => array(
					'custom_right_arrow' => 'yes',
				),
				'label_block' => false,
			)
		);

		$this->add_control(
			'premium_carousel_arrow_icon_next',
			array(
				'label'     => __( 'Right Icon', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::CHOOSE,
				'options'   => array(
					'right_arrow_bold'        => array(
						'icon' => 'fas fa-arrow-right',
					),
					'right_arrow_long'        => array(
						'icon' => 'fas fa-long-arrow-alt-right',
					),
					'right_arrow_long_circle' => array(
						'icon' => 'fas fa-arrow-circle-right',
					),
					'right_arrow_angle'       => array(
						'icon' => 'fas fa-angle-right',
					),
					'right_arrow_chevron'     => array(
						'icon' => 'fas fa-chevron-right',
					),
				),
				'default'   => 'right_arrow_angle',
				'condition' => array(
					'premium_carousel_slider_type!' => 'vertical',
					'custom_right_arrow!'           => 'yes',
				),
				'toggle'    => false,
			)
		);

		$this->add_control(
			'premium_carousel_arrow_icon_next_ver',
			array(
				'label'     => __( 'Bottom Icon', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::CHOOSE,
				'options'   => array(
					'right_arrow_bold'        => array(
						'icon' => 'fas fa-arrow-down',
					),
					'right_arrow_long'        => array(
						'icon' => 'fas fa-long-arrow-alt-down',
					),
					'right_arrow_long_circle' => array(
						'icon' => 'fas fa-arrow-circle-down',
					),
					'right_arrow_angle'       => array(
						'icon' => 'fas fa-angle-down',
					),
					'right_arrow_chevron'     => array(
						'icon' => 'fas fa-chevron-down',
					),
				),
				'default'   => 'right_arrow_angle',
				'condition' => array(
					'premium_carousel_slider_type' => 'vertical',
					'custom_right_arrow!'          => 'yes',
				),
				'toggle'    => false,
			)
		);

		$this->add_responsive_control(
			'premium_carousel_arrow_size',
			array(
				'label'      => __( 'Size', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', 'em', 'vw' ),
				'default'    => array(
					'size' => 14,
					'unit' => 'px',
				),
				'range'      => array(
					'px' => array(
						'min' => 0,
						'max' => 60,
					),
				),
				'separator'  => 'before',
				'selectors'  => array(
					'{{WRAPPER}} .premium-carousel-wrapper .slick-arrow' => 'font-size: {{SIZE}}{{UNIT}}',
					'{{WRAPPER}} .premium-carousel-wrapper .slick-arrow svg' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}}',
				),
			)
		);

		$this->add_responsive_control(
			'premium_carousel_arrow_position',
			array(
				'label'     => __( 'Position (PX)', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::SLIDER,
				'range'     => array(
					'px' => array(
						'min' => -100,
						'max' => 100,
					),
				),
				'selectors' => array(
					'{{WRAPPER}} a.carousel-arrow.carousel-next' => 'right: {{SIZE}}px',
					'{{WRAPPER}} a.carousel-arrow.carousel-prev' => 'left: {{SIZE}}px',
					'{{WRAPPER}} a.ver-carousel-arrow.carousel-next' => 'bottom: {{SIZE}}px',
					'{{WRAPPER}} a.ver-carousel-arrow.carousel-prev' => 'top: {{SIZE}}px',
				),
			)
		);

		$this->start_controls_tabs( 'premium_button_style_tabs' );

		$this->start_controls_tab(
			'premium_button_style_normal',
			array(
				'label' => __( 'Normal', 'premium-addons-for-elementor' ),
			)
		);

		$this->add_control(
			'premium_carousel_arrow_color',
			array(
				'label'     => __( 'Color', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'global'    => array(
					'default' => Global_Colors::COLOR_SECONDARY,
				),
				'selectors' => array(
					'{{WRAPPER}} .premium-carousel-wrapper .slick-arrow' => 'color: {{VALUE}};',
					'{{WRAPPER}} .premium-carousel-wrapper .slick-arrow svg' => 'fill: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'premium_carousel_arrow_bg_color',
			array(
				'label'     => __( 'Background Color', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} a.carousel-next, {{WRAPPER}} a.carousel-prev' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'     => 'premium_carousel_arrows_border_normal',
				'selector' => '{{WRAPPER}} .slick-arrow',
			)
		);

		$this->add_control(
			'premium_carousel_arrows_radius_normal',
			array(
				'label'      => __( 'Border Radius', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', '%', 'em' ),
				'selectors'  => array(
					'{{WRAPPER}} .slick-arrow' => 'border-radius: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'premium_carousel_arrows_hover',
			array(
				'label' => __( 'Hover', 'premium-addons-for-elementor' ),
			)
		);

		$this->add_control(
			'premium_carousel_hover_arrow_color',
			array(
				'label'     => __( 'Color', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'global'    => array(
					'default' => Global_Colors::COLOR_SECONDARY,
				),
				'selectors' => array(
					'{{WRAPPER}} .premium-carousel-wrapper .slick-arrow:hover' => 'color: {{VALUE}};',
					'{{WRAPPER}} .premium-carousel-wrapper .slick-arrow:hover svg' => 'fill: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'premium_carousel_arrow_hover_bg_color',
			array(
				'label'     => __( 'Background Color', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} a.carousel-next:hover, {{WRAPPER}} a.carousel-prev:hover' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'     => 'premium_carousel_arrows_border_hover',
				'selector' => '{{WRAPPER}} .slick-arrow:hover',
			)
		);

		$this->add_control(
			'premium_carousel_arrows_radius_hover',
			array(
				'label'      => __( 'Border Radius', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', '%', 'em' ),
				'selectors'  => array(
					'{{WRAPPER}} .slick-arrow:hover' => 'border-radius: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();

		$this->start_controls_section(
			'premium_carousel_navigation',
			array(
				'label' => __( 'Navigation', 'premium-addons-for-elementor' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_control(
			'premium_carousel_dot_icon',
			array(
				'label'     => __( 'Icon', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::CHOOSE,
				'options'   => array(
					'square_white' => array(
						'icon' => 'far fa-square',
					),
					'square_black' => array(
						'icon' => 'fas fa-square',
					),
					'circle_white' => array(
						'icon' => 'fas fa-circle',
					),
					'circle_thin'  => array(
						'icon' => 'far fa-circle',
					),
				),
				'default'   => 'circle_white',
				'condition' => array(
					'custom_pagination_icon!'      => 'yes',
					'premium_carousel_nav_options' => 'dots',
				),
				'toggle'    => false,
			)
		);

		$this->add_control(
			'custom_pagination_icon',
			array(
				'label'     => __( 'Custom Icon', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::SWITCHER,
				'condition' => array(
					'premium_carousel_nav_options' => 'dots',
				),
			)
		);

		$this->add_control(
			'custom_pagination_icon_select',
			array(
				'label'       => __( 'Select Icon', 'premium-addons-for-elementor' ),
				'type'        => Controls_Manager::ICONS,
				'default'     => array(
					'value'   => 'fas fa-dot-circle',
					'library' => 'fa-solid',
				),
				'skin'        => 'inline',
				'condition'   => array(
					'custom_pagination_icon'       => 'yes',
					'premium_carousel_nav_options' => 'dots',
				),
				'label_block' => false,
			)
		);

		$this->add_responsive_control(
			'dot_size',
			array(
				'label'      => __( 'Size', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', 'em' ),
				'selectors'  => array(
					'{{WRAPPER}} ul.slick-dots li, {{WRAPPER}} ul.slick-dots li svg' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}}; font-size: calc( {{SIZE}}{{UNIT}} / 2 )',
				),
				'condition'  => array(
					'premium_carousel_nav_options' => 'dots',
				),
			)
		);

		$this->add_responsive_control(
			'progress_height',
			array(
				'label'     => __( 'Height', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::SLIDER,
				'selectors' => array(
					'{{WRAPPER}} .premium-carousel-nav-progressbar' => 'height: {{SIZE}}px;',
					'{{WRAPPER}} .premium-carousel-nav-progress' => 'height: {{SIZE}}px;',
				),
				'condition' => array(
					'premium_carousel_nav_options' => array( 'progressbar', 'progress' ),
				),
			)
		);

		$this->add_responsive_control(
			'separator_spacing',
			array(
				'label'      => __( 'Separator Spacing', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', 'em' ),
				'selectors'  => array(
					'{{WRAPPER}} .fraction-pagination-separator' => 'margin-left: {{SIZE}}{{UNIT}}; margin-right: {{SIZE}}{{UNIT}};',
				),
				'condition'  => array(
					'premium_carousel_nav_options' => 'fraction',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'      => 'premium_navigation_typography',
				'selector'  => '{{WRAPPER}} .premium-carousel-nav-fraction',
				'global'    => array(
					'default' => Global_Typography::TYPOGRAPHY_PRIMARY,
				),
				'condition' => array(
					'premium_carousel_nav_options' => 'fraction',
				),
			)
		);

		$this->add_control(
			'premium_carousel_dot_navigation_color',
			array(
				'label'     => __( 'Color', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'global'    => array(
					'default' => Global_Colors::COLOR_SECONDARY,
				),
				'selectors' => array(
					'{{WRAPPER}} ul.slick-dots li'     => 'color: {{VALUE}}',
					'{{WRAPPER}} ul.slick-dots li svg' => 'fill: {{VALUE}}',
					'{{WRAPPER}} .fraction-pagination-total' => 'color: {{VALUE}}',
				),
				'condition' => array(
					'premium_carousel_nav_options' => array( 'dots', 'fraction' ),
				),
			)
		);

		$this->add_control(
			'premium_carousel_navigation_separator_color',
			array(
				'label'     => __( 'Separator Color', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'global'    => array(
					'default' => Global_Colors::COLOR_SECONDARY,
				),
				'selectors' => array(
					'{{WRAPPER}} .fraction-pagination-separator' => 'color: {{VALUE}}',
				),
				'condition' => array(
					'premium_carousel_nav_options' => 'fraction',
				),
			)
		);

		$this->add_control(
			'premium_carousel_dot_navigation_active_color',
			array(
				'label'     => __( 'Active Color', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'global'    => array(
					'default' => Global_Colors::COLOR_PRIMARY,
				),
				'selectors' => array(
					'{{WRAPPER}} ul.slick-dots li.slick-active' => 'color: {{VALUE}}',
					'{{WRAPPER}} ul.slick-dots li.slick-active svg' => 'fill: {{VALUE}}',
					'{{WRAPPER}} .fraction-pagination-current' => 'color: {{VALUE}}',
				),
				'condition' => array(
					'premium_carousel_nav_options' => array( 'dots', 'fraction' ),
				),
			)
		);

		$this->add_control(
			'fill_colors_title',
			array(
				'label'     => __( 'Fill', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::HEADING,
				'condition' => array(
					'premium_carousel_nav_options' => array( 'progressbar', 'progress' ),
				),
			)
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			array(
				'name'      => 'premium_progressbar_progress_color',
				'types'     => array( 'classic', 'gradient' ),
				'selector'  =>
					'{{WRAPPER}} .premium-carousel-nav-progressbar-fill ,{{WRAPPER}} .premium-carousel-nav-progress-fill',
				'condition' => array(
					'premium_carousel_nav_options' => array( 'progressbar', 'progress' ),
				),
			)
		);

		$this->add_control(
			'base_colors_title',
			array(
				'label'     => __( 'Base', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::HEADING,
				'condition' => array(
					'premium_carousel_nav_options' => array( 'progressbar', 'progress' ),
				),
			)
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			array(
				'name'      => 'premium_progressbar_background',
				'types'     => array( 'classic', 'gradient' ),
				'selector'  =>
					'{{WRAPPER}} .premium-carousel-nav-progressbar , {{WRAPPER}} .premium-carousel-nav-progress',
				'condition' => array(
					'premium_carousel_nav_options' => array( 'progressbar', 'progress' ),
				),
			)
		);

		$this->add_control(
			'premium_carousel_ripple_active_color',
			array(
				'label'     => __( 'Active Ripple Color', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'condition' => array(
					'premium_carousel_navigation_effect' => 'yes',
					'premium_carousel_nav_options'       => 'dots',
				),
				'selectors' => array(
					'{{WRAPPER}}.premium-carousel-ripple-yes ul.slick-dots li.slick-active:hover:before' => 'background-color: {{VALUE}}',
				),
			)
		);

		$this->add_control(
			'premium_carousel_ripple_color',
			array(
				'label'     => __( 'Inactive Ripple Color', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'condition' => array(
					'premium_carousel_navigation_effect' => 'yes',
					'premium_carousel_nav_options'       => 'dots',
				),
				'selectors' => array(
					'{{WRAPPER}}.premium-carousel-ripple-yes ul.slick-dots li:hover:before' => 'background-color: {{VALUE}}',
				),
			)
		);

		$this->end_controls_section();

	}

	/**
	 * Render Carousel widget output on the frontend.
	 *
	 * Written in PHP and used to generate the final HTML.
	 *
	 * @since  1.0.0
	 * @access protected
	 */
	protected function render() {
		$settings = $this->get_settings();

		$content_type = $settings['premium_carousel_content_type'];

		$templates = array();

		$templates_count = 0;

		if ( 'select' === $content_type ) {
			$templates       = $settings['premium_carousel_slider_content'];
			$templates_count = ! empty( $templates ) ? count( $templates ) : 0;

		} else {
			$custom_navigation = array();
			$temp_id           = '';

			foreach ( $settings['premium_carousel_templates_repeater'] as $template ) {
				$temp_id = empty( $template['premium_carousel_repeater_item'] ) ? $template['live_temp_content'] : $template['premium_carousel_repeater_item'];
				array_push( $templates, $temp_id );
				array_push( $custom_navigation, $template['custom_navigation'] );
				$templates_count = count( $templates );
			}
		}

		if ( empty( $templates ) ) {
			return;
		}

		$vertical = 'vertical' === $settings['premium_carousel_slider_type'] ? true : false;

		$slides_on_desk = $settings['premium_carousel_responsive_desktop'];
		if ( 'all' === $settings['premium_carousel_slides_to_show'] ) {
			$slides_scroll = ! empty( $slides_on_desk ) ? $slides_on_desk : 1;
		} else {
			$slides_scroll = 1;
		}

		$slides_show = ! empty( $slides_on_desk ) ? $slides_on_desk : 1;

		$slides_on_tabs = $settings['premium_carousel_responsive_tabs'];
		$slides_on_mob  = $settings['premium_carousel_responsive_mobile'];

		if ( empty( $settings['premium_carousel_responsive_tabs'] ) ) {
			$slides_on_tabs = $slides_on_desk;
		}

		if ( empty( $settings['premium_carousel_responsive_mobile'] ) ) {
			$slides_on_mob = $slides_on_desk;
		}

		$infinite = 'yes' === $settings['premium_carousel_loop'] ? true : false;

		$fade = 'yes' !== $settings['mscroll'] && 'yes' === $settings['premium_carousel_fade'] ? true : false;

		$speed = ! empty( $settings['premium_carousel_speed'] ) ? $settings['premium_carousel_speed'] : '';

		$autoplay = 'yes' !== $settings['mscroll'] && 'yes' === $settings['premium_carousel_autoplay'] ? true : false;

		$autoplay_speed = ! empty( $settings['premium_carousel_autoplay_speed'] ) ? $settings['premium_carousel_autoplay_speed'] : '';

		$draggable = 'yes' !== $settings['mscroll'] && 'yes' === $settings['premium_carousel_draggable_effect'] ? true : false;

		$touch_move = 'yes' !== $settings['mscroll'] && 'yes' === $settings['premium_carousel_touch_move'] ? true : false;

		$dir = '';
		$rtl = false;

		if ( 'yes' === $settings['premium_carousel_RTL_Mode'] ) {
			$rtl = true;
			$dir = 'dir="rtl"';
		}

		$variable_width = ( 'yes' !== $settings['mscroll'] && 'yes' === $settings['variable_width'] ) ? true : false;

		$adaptive_height = 'yes' === $settings['premium_carousel_adaptive_height'] ? true : false;

		$linear = 'yes' === $settings['linear_ease'] ? true : false;

		$pause_hover = 'yes' === $settings['premium_carousel_pausehover'] ? true : false;

		$center_mode = 'yes' === $settings['premium_carousel_center_mode'] ? true : false;

		// Navigation arrow setting setup.
		if ( 'yes' !== $settings['mscroll'] && 'yes' === $settings['premium_carousel_navigation_show'] ) {
			$arrows = true;

			if ( 'vertical' === $settings['premium_carousel_slider_type'] ) {
				$vertical_alignment = 'ver-carousel-arrow';
			} else {
				$vertical_alignment = 'carousel-arrow';
			}

			if ( 'vertical' === $settings['premium_carousel_slider_type'] ) {

				if ( 'yes' !== $settings['custom_left_arrow'] ) {
					$icon_prev = $settings['premium_carousel_arrow_icon_prev_ver'];
					if ( 'left_arrow_bold' === $icon_prev ) {
						$icon_prev_class = 'fas fa-arrow-up';
					}
					if ( 'left_arrow_long' === $icon_prev ) {
						$icon_prev_class = 'fas fa-long-arrow-alt-up';
					}
					if ( 'left_arrow_long_circle' === $icon_prev ) {
						$icon_prev_class = 'fas fa-arrow-circle-up';
					}
					if ( 'left_arrow_angle' === $icon_prev ) {
						$icon_prev_class = 'fas fa-angle-up';
					}
					if ( 'left_arrow_chevron' === $icon_prev ) {
						$icon_prev_class = 'fas fa-chevron-up';
					}
				}

				if ( 'yes' !== $settings['custom_right_arrow'] ) {
					$icon_next = $settings['premium_carousel_arrow_icon_next_ver'];
					if ( 'right_arrow_bold' === $icon_next ) {
						$icon_next_class = 'fas fa-arrow-down';
					}
					if ( 'right_arrow_long' === $icon_next ) {
						$icon_next_class = 'fas fa-long-arrow-alt-down';
					}
					if ( 'right_arrow_long_circle' === $icon_next ) {
						$icon_next_class = 'fas fa-arrow-circle-down';
					}
					if ( 'right_arrow_angle' === $icon_next ) {
						$icon_next_class = 'fas fa-angle-down';
					}
					if ( 'right_arrow_chevron' === $icon_next ) {
						$icon_next_class = 'fas fa-chevron-down';
					}
				}
			} else {

				if ( 'yes' !== $settings['custom_left_arrow'] ) {
					$icon_prev = $settings['premium_carousel_arrow_icon_prev'];
					if ( 'left_arrow_bold' === $icon_prev ) {
						$icon_prev_class = 'fas fa-arrow-left';
					}
					if ( 'left_arrow_long' === $icon_prev ) {
						$icon_prev_class = 'fas fa-long-arrow-alt-left';
					}
					if ( 'left_arrow_long_circle' === $icon_prev ) {
						$icon_prev_class = 'fas fa-arrow-circle-left';
					}
					if ( 'left_arrow_angle' === $icon_prev ) {
						$icon_prev_class = 'fas fa-angle-left';
					}
					if ( 'left_arrow_chevron' === $icon_prev ) {
						$icon_prev_class = 'fas fa-chevron-left';
					}
				}

				if ( 'yes' !== $settings['custom_right_arrow'] ) {
					$icon_next = $settings['premium_carousel_arrow_icon_next'];
					if ( 'right_arrow_bold' === $icon_next ) {
						$icon_next_class = 'fas fa-arrow-right';
					}
					if ( 'right_arrow_long' === $icon_next ) {
						$icon_next_class = 'fas fa-long-arrow-alt-right';
					}
					if ( 'right_arrow_long_circle' === $icon_next ) {
						$icon_next_class = 'fas fa-arrow-circle-right';
					}
					if ( 'right_arrow_angle' === $icon_next ) {
						$icon_next_class = 'fas fa-angle-right';
					}
					if ( 'right_arrow_chevron' === $icon_next ) {
						$icon_next_class = 'fas fa-chevron-right';
					}
				}
			}
		} else {
			$arrows = false;
		}

		if ( 'yes' !== $settings['mscroll'] && 'dots' === $settings['premium_carousel_nav_options'] ) {
			$dots = true;
			if ( 'yes' !== $settings['custom_pagination_icon'] ) {
				if ( 'square_white' === $settings['premium_carousel_dot_icon'] ) {
					$dot_icon = 'far fa-square';
				}
				if ( 'square_black' === $settings['premium_carousel_dot_icon'] ) {
					$dot_icon = 'fas fa-square';
				}
				if ( 'circle_white' === $settings['premium_carousel_dot_icon'] ) {
					$dot_icon = 'fas fa-circle';
				}
				if ( 'circle_thin' === $settings['premium_carousel_dot_icon'] ) {
					$dot_icon = 'far fa-circle-thin';
				}
				$custom_paging = $dot_icon;
			}
		} else {
			$dots = false;
		}

		$carouselNavigation = $settings['premium_carousel_nav_options'];
		$extra_class        = ! empty( $settings['premium_carousel_extra_class'] ) ? ' ' . $settings['premium_carousel_extra_class'] : '';

		$animation_class = $settings['premium_carousel_animation_list'];

		$animation = ! empty( $animation_class ) ? 'animated ' . $animation_class : 'null';

		$tablet_breakpoint = ! empty( $settings['premium_carousel_tablet_breakpoint'] ) ? $settings['premium_carousel_tablet_breakpoint'] : 1025;

		$mobile_breakpoint = ! empty( $settings['premium_carousel_mobile_breakpoint'] ) ? $settings['premium_carousel_mobile_breakpoint'] : 768;

		$carousel_settings = array(
			'vertical'           => $vertical,
			'slidesToScroll'     => $slides_scroll,
			'slidesToShow'       => $slides_show,
			'infinite'           => $infinite,
			'speed'              => $speed,
			'fade'               => $fade,
			'autoplay'           => $autoplay,
			'autoplaySpeed'      => $autoplay_speed,
			'draggable'          => $draggable,
			'touchMove'          => $touch_move,
			'rtl'                => $rtl,
			'adaptiveHeight'     => $adaptive_height,
			'variableWidth'      => $variable_width,
			'cssEase'            => $linear ? 'linear' : 'ease',
			'pauseOnHover'       => $pause_hover,
			'centerMode'         => $center_mode,
			'arrows'             => $arrows,
			'dots'               => $dots,
			'slidesDesk'         => $slides_on_desk,
			'slidesTab'          => $slides_on_tabs,
			'slidesMob'          => $slides_on_mob,
			'animation'          => $animation,
			'tabletBreak'        => $tablet_breakpoint,
			'mobileBreak'        => $mobile_breakpoint,
			'navigation'         => 'repeater' === $content_type ? $custom_navigation : array(),
			'carouselNavigation' => $carouselNavigation,
			'templatesNumber'    => $templates_count,
		);

		$this->add_render_attribute( 'carousel', 'id', 'premium-carousel-wrapper-' . esc_attr( $this->get_id() ) );

		$this->add_render_attribute(
			'carousel',
			'class',
			array(
				'premium-carousel-wrapper',
				'premium-carousel-hidden',
				'carousel-wrapper-' . esc_attr( $this->get_id() ),
				$extra_class,
				$dir,
			)
		);

		if ( 'dots' === $settings['premium_carousel_nav_options'] ) {
			$this->add_render_attribute( 'carousel', 'class', 'premium-carousel-dots-' . $settings['premium_carousel_dot_position'] );

		}

		if ( 'fraction' === $settings['premium_carousel_nav_options'] ) {
			$this->add_render_attribute( 'carousel', 'class', 'premium-carousel-fraction' );
		}

		if ( 'progressbar' === $settings['premium_carousel_nav_options'] ) {
			$this->add_render_attribute( 'carousel', 'class', 'premium-carousel-progressbar' );
		}

		if ( 'yes' === $settings['premium_carousel_fade'] && 'yes' === $settings['premium_carousel_zoom'] ) {
			$this->add_render_attribute( 'carousel', 'class', 'premium-carousel-scale' );
		}

		$this->add_render_attribute( 'carousel', 'data-settings', wp_json_encode( $carousel_settings ) );

		?>

		<div <?php echo wp_kses_post( $this->get_render_attribute_string( 'carousel' ) ); ?>>
		<?php if ( 'dots' === $settings['premium_carousel_nav_options'] ) { ?>
				<div class="premium-carousel-nav-dot">
			<?php if ( 'yes' !== $settings['custom_pagination_icon'] ) { ?>
					<i class="<?php echo esc_attr( $custom_paging ); ?>" aria-hidden="true"></i>
				<?php
			} else {
				Icons_Manager::render_icon( $settings['custom_pagination_icon_select'], array( 'aria-hidden' => 'true' ) );
			}
			?>
				</div>
		<?php } ?>
		<?php if ( 'yes' === $settings['premium_carousel_navigation_show'] ) { ?>
				<div class="premium-carousel-nav-arrow-prev">
					<a type="button" data-role="none" class="<?php echo esc_attr( $vertical_alignment ); ?> carousel-prev" aria-label="Previous" role="button">
			<?php if ( 'yes' !== $settings['custom_left_arrow'] ) { ?>
							<i class="<?php echo esc_attr( $icon_prev_class ); ?>" aria-hidden="true"></i>
				<?php
			} else {
				Icons_Manager::render_icon( $settings['custom_left_arrow_select'], array( 'aria-hidden' => 'true' ) );
			}
			?>
					</a>
				</div>
				<div class="premium-carousel-nav-arrow-next">
					<a type="button" data-role="none" class="<?php echo esc_attr( $vertical_alignment ); ?> carousel-next" aria-label="Next" role="button">
			<?php if ( 'yes' !== $settings['custom_right_arrow'] ) { ?>
							<i class="<?php echo esc_attr( $icon_next_class ); ?>" aria-hidden="true"></i>
				<?php
			} else {
				Icons_Manager::render_icon( $settings['custom_right_arrow_select'], array( 'aria-hidden' => 'true' ) );
			}
			?>
					</a>
				</div>
		<?php } ?>
			<div id="premium-carousel-<?php echo esc_attr( $this->get_id() ); ?>" class="premium-carousel-inner">
		<?php
		foreach ( $templates as $template_title ) :
			if ( ! empty( $template_title ) ) :
				?>
				<div class="premium-carousel-template item-wrapper">
					<?php echo $this->getTemplateInstance()->get_template_content( $template_title ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
					<?php if ( 'progress' === $settings['premium_carousel_nav_options'] ) { ?>
						<div class="premium-carousel-nav-progress">
							<span class="premium-carousel-nav-progress-fill"></span>
						</div>
					<?php } ?>
				</div>
				<?php
			endif;
		endforeach;
		?>
			</div>
			<?php if ( 'fraction' === $settings['premium_carousel_nav_options'] ) { ?>
				<div class="premium-carousel-nav-fraction">

					<span id="currentSlide" class="fraction-pagination-current">1</span>
					<span class="fraction-pagination-separator">/</span>
					<span class="fraction-pagination-total">
						<?php echo esc_attr( $templates_count ); ?>
					</span>
				</div>
			<?php } elseif ( 'progressbar' === $settings['premium_carousel_nav_options'] ) { ?>
				<div class="premium-carousel-nav-progressbar">
					<span class="premium-carousel-nav-progressbar-fill"></span>
				</div>
			<?php } ?>
		</div>
		<?php
	}

}
