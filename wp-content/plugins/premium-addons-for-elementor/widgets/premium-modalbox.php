<?php
/**
 * Premium Modal Box.
 */

namespace PremiumAddons\Widgets;

// Elementor Classes.
use Elementor\Icons_Manager;
use Elementor\Widget_Base;
use Elementor\Utils;
use Elementor\Control_Media;
use Elementor\Controls_Manager;
use Elementor\Core\Kits\Documents\Tabs\Global_Colors;
use Elementor\Core\Kits\Documents\Tabs\Global_Typography;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Css_Filter;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Text_Shadow;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Background;

// PremiumAddons Classes.
use PremiumAddons\Admin\Includes\Admin_Helper;
use PremiumAddons\Includes\Helper_Functions;
use PremiumAddons\Includes\Premium_Template_Tags;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // If this file is called directly, abort.
}

/**
 * Class Premium_Modalbox
 */
class Premium_Modalbox extends Widget_Base {

	/**
	 * Check Icon Draw Option.
	 *
	 * @since 4.9.26
	 * @access public
	 */
	public function check_icon_draw() {
		$is_enabled = Admin_Helper::check_svg_draw( 'premium-modalbox' );
		return $is_enabled;
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
		return $this->template_instance = Premium_Template_Tags::getInstance();
	}

	/**
	 * Retrieve Widget Name.
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function get_name() {
		return 'premium-addon-modal-box';
	}

	/**
	 * Check RTL
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function check_rtl() {
		return is_rtl();
	}

	/**
	 * Retrieve Widget Title.
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function get_title() {
		return __( 'Modal Box', 'premium-addons-for-elementor' );
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
		return 'pa-modal-box';
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

		$draw_scripts = $this->check_icon_draw() ? array(
			'pa-fontawesome-all',
			'pa-tweenmax',
			'pa-motionpath',
		) : array();

		return array_merge(
			$draw_scripts,
			array(
				'pa-modal',
				'lottie-js',
				'premium-addons',
			)
		);
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
		return array( 'pa', 'premium', 'popup', 'lightbox', 'advanced', 'embed' );
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
	 * Register Modal Box controls.
	 *
	 * @since 1.0.0
	 * @access protected
	 */
	protected function register_controls() { // phpcs:ignore PSR2.Methods.MethodDeclaration.Underscore

		$draw_icon = $this->check_icon_draw();

		$this->start_controls_section(
			'premium_modal_box_selector_content_section',
			array(
				'label' => __( 'Content', 'premium-addons-for-elementor' ),
			)
		);

		$this->add_control(
			'premium_modal_box_header_switcher',
			array(
				'label'       => __( 'Header', 'premium-addons-for-elementor' ),
				'type'        => Controls_Manager::SWITCHER,
				'label_on'    => 'show',
				'label_off'   => 'hide',
				'default'     => 'yes',
				'description' => __( 'Enable or disable modal header', 'premium-addons-for-elementor' ),
			)
		);

		$this->add_control(
			'premium_modal_box_icon_selection',
			array(
				'label'       => __( 'Icon Type', 'premium-addons-for-elementor' ),
				'type'        => Controls_Manager::SELECT,
				'options'     => array(
					'noicon'    => __( 'None', 'premium-addons-for-elementor' ),
					'fonticon'  => __( 'Icon', 'premium-addons-for-elementor' ),
					'image'     => __( 'Custom Image', 'premium-addons-for-elementor' ),
					'animation' => __( 'Lottie Animation', 'premium-addons-for-elementor' ),
				),
				'default'     => 'noicon',
				'condition'   => array(
					'premium_modal_box_header_switcher' => 'yes',
				),
				'label_block' => true,
			)
		);

		$this->add_control(
			'premium_modal_box_font_icon_updated',
			array(
				'label'            => __( 'Select Icon', 'premium-addons-for-elementor' ),
				'type'             => Controls_Manager::ICONS,
				'fa4compatibility' => 'premium_modal_box_font_icon',
				'condition'        => array(
					'premium_modal_box_icon_selection'  => 'fonticon',
					'premium_modal_box_header_switcher' => 'yes',
				),
				'label_block'      => true,
			)
		);

		$this->add_control(
			'premium_modal_box_image_icon',
			array(
				'label'       => __( 'Upload Image', 'premium-addons-for-elementor' ),
				'type'        => Controls_Manager::MEDIA,
				'dynamic'     => array( 'active' => true ),
				'default'     => array(
					'url' => Utils::get_placeholder_image_src(),
				),
				'condition'   => array(
					'premium_modal_box_icon_selection'  => 'image',
					'premium_modal_box_header_switcher' => 'yes',
				),
				'label_block' => true,
			)
		);

		$this->add_control(
			'header_lottie_url',
			array(
				'label'       => __( 'Animation JSON URL', 'premium-addons-for-elementor' ),
				'type'        => Controls_Manager::TEXT,
				'dynamic'     => array( 'active' => true ),
				'description' => 'Get JSON code URL from <a href="https://lottiefiles.com/" target="_blank">here</a>',
				'label_block' => true,
				'condition'   => array(
					'premium_modal_box_icon_selection'  => 'animation',
					'premium_modal_box_header_switcher' => 'yes',
				),
			)
		);

		$this->add_control(
			'header_lottie_loop',
			array(
				'label'        => __( 'Loop', 'premium-addons-for-elementor' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'true',
				'default'      => 'true',
				'condition'    => array(
					'premium_modal_box_icon_selection'  => 'animation',
					'premium_modal_box_header_switcher' => 'yes',
				),
			)
		);

		$this->add_control(
			'header_lottie_reverse',
			array(
				'label'        => __( 'Reverse', 'premium-addons-for-elementor' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'true',
				'condition'    => array(
					'premium_modal_box_icon_selection'  => 'animation',
					'premium_modal_box_header_switcher' => 'yes',
				),
			)
		);

		$this->add_responsive_control(
			'premium_modal_box_font_icon_size',
			array(
				'label'      => __( 'Icon Size', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', 'em' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-modal-box-modal-title i' => 'font-size: {{SIZE}}{{UNIT}}',
					'{{WRAPPER}} .premium-modal-box-modal-title img' => 'width: {{SIZE}}{{UNIT}}',
					'{{WRAPPER}} .premium-modal-box-modal-title svg' => 'width: {{SIZE}}{{UNIT}} !important; height: {{SIZE}}{{UNIT}} !important',
				),
				'condition'  => array(
					'premium_modal_box_icon_selection!' => 'noicon',
					'premium_modal_box_header_switcher' => 'yes',
				),
			)
		);

		$this->add_control(
			'premium_modal_box_title',
			array(
				'label'       => __( 'Title', 'premium-addons-for-elementor' ),
				'type'        => Controls_Manager::TEXT,
				'dynamic'     => array( 'active' => true ),
				'description' => __( 'Add a title for the modal box', 'premium-addons-for-elementor' ),
				'default'     => 'Modal Box Title',
				'condition'   => array(
					'premium_modal_box_header_switcher' => 'yes',
				),
				'label_block' => true,
			)
		);

		$this->add_control(
			'premium_modal_box_content_type',
			array(
				'label'       => __( 'Content to Show', 'premium-addons-for-elementor' ),
				'type'        => Controls_Manager::SELECT,
				'options'     => array(
					'editor'   => __( 'Text Editor', 'premium-addons-for-elementor' ),
					'template' => __( 'Elementor Template', 'premium-addons-for-elementor' ),
				),
				'default'     => 'editor',
				'separator'   => 'before',
				'label_block' => true,
			)
		);

		$this->add_control(
			'live_temp_content',
			array(
				'label'       => __( 'Template Title', 'premium-addons-for-elementor' ),
				'type'        => Controls_Manager::TEXT,
				'classes'     => 'premium-live-temp-title control-hidden',
				'label_block' => true,
				'condition'   => array(
					'premium_modal_box_content_type' => 'template',
				),
			)
		);

		$this->add_control(
			'modal_temp_live_btn',
			array(
				'type'        => Controls_Manager::BUTTON,
				'label_block' => true,
				'button_type' => 'default papro-btn-block',
				'text'        => __( 'Create / Edit Template', 'premium-addons-for-elementor' ),
				'event'       => 'createLiveTemp',
				'condition'   => array(
					'premium_modal_box_content_type' => 'template',
				),
			)
		);

		$this->add_control(
			'premium_modal_box_content_temp',
			array(
				'label'       => __( 'OR Select Existing Template', 'premium-addons-for-elementor' ),
				'type'        => Controls_Manager::SELECT2,
				'label_block' => true,
				'classes'     => 'premium-live-temp-label',
				'options'     => $this->getTemplateInstance()->get_elementor_page_list(),
				'condition'   => array(
					'premium_modal_box_content_type' => 'template',
				),
			)
		);

		$this->add_control(
			'premium_modal_box_content',
			array(
				'type'       => Controls_Manager::WYSIWYG,
				'default'    => 'Modal Box Content',
				'selector'   => '{{WRAPPER}} .premium-modal-box-modal-body',
				'dynamic'    => array( 'active' => true ),
				'condition'  => array(
					'premium_modal_box_content_type' => 'editor',
				),
				'show_label' => false,
			)
		);

		$this->add_control(
			'premium_modal_box_upper_close',
			array(
				'label'     => __( 'Upper Close Button', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::SWITCHER,
				'separator' => 'before',
				'default'   => 'yes',
				'condition' => array(
					'premium_modal_box_header_switcher' => 'yes',
				),
			)
		);

		$this->add_control(
			'premium_modal_box_lower_close',
			array(
				'label'   => __( 'Lower Close Button', 'premium-addons-for-elementor' ),
				'type'    => Controls_Manager::SWITCHER,
				'default' => 'yes',
			)
		);

		$this->add_control(
			'premium_modal_close_text',
			array(
				'label'       => __( 'Text', 'premium-addons-for-elementor' ),
				'default'     => __( 'Close', 'premium-addons-for-elementor' ),
				'type'        => Controls_Manager::TEXT,
				'dynamic'     => array( 'active' => true ),
				'label_block' => true,
				'condition'   => array(
					'premium_modal_box_lower_close' => 'yes',
				),
			)
		);

		$this->add_control(
			'premium_modal_box_animation',
			array(
				'label'              => __( 'Entrance Animation', 'premium-addons-for-elementor' ),
				'type'               => Controls_Manager::ANIMATION,
				'default'            => 'fadeInDown',
				'label_block'        => true,
				'frontend_available' => true,
				'render_type'        => 'template',

			)
		);

		$this->add_control(
			'premium_modal_box_animation_duration',
			array(
				'label'     => __( 'Animation Duration', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'fast',
				'options'   => array(
					'slow' => __( 'Slow', 'premium-addons-for-elementor' ),
					''     => __( 'Normal', 'premium-addons-for-elementor' ),
					'fast' => __( 'Fast', 'premium-addons-for-elementor' ),
				),
				'condition' => array(
					'premium_modal_box_animation!' => '',
				),
			)
		);

		$this->add_control(
			'premium_modal_box_animation_delay',
			array(
				'label'              => __( 'Animation Delay', 'premium-addons-for-elementor' ) . ' (s)',
				'type'               => Controls_Manager::NUMBER,
				'default'            => '',
				'step'               => 0.1,
				'condition'          => array(
					'premium_modal_box_animation!' => '',
				),
				'frontend_available' => true,
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'premium_modal_box_content_section',
			array(
				'label' => __( 'Trigger Options', 'premium-addons-for-elementor' ),
			)
		);

		$this->add_control(
			'premium_modal_box_display_on',
			array(
				'label'       => __( 'Trigger', 'premium-addons-for-elementor' ),
				'type'        => Controls_Manager::SELECT,
				'description' => __( 'Choose where would you like the modal box appear on', 'premium-addons-for-elementor' ),
				'options'     => array(
					'button'    => __( 'Button', 'premium-addons-for-elementor' ),
					'image'     => __( 'Image', 'premium-addons-for-elementor' ),
					'text'      => __( 'Text', 'premium-addons-for-elementor' ),
					'animation' => __( 'Lottie Animation', 'premium-addons-for-elementor' ),
					'pageload'  => __( 'On Page Load', 'premium-addons-for-elementor' ),
				),
				'label_block' => true,
				'default'     => 'button',
			)
		);

		$this->add_control(
			'premium_modal_box_button_text',
			array(
				'label'       => __( 'Button Text', 'premium-addons-for-elementor' ),
				'default'     => __( 'Premium Addons', 'premium-addons-for-elementor' ),
				'type'        => Controls_Manager::TEXT,
				'dynamic'     => array( 'active' => true ),
				'label_block' => true,
				'condition'   => array(
					'premium_modal_box_display_on' => 'button',
				),
			)
		);

		$this->add_control(
			'premium_modal_box_icon_switcher',
			array(
				'label'       => __( 'Icon', 'premium-addons-for-elementor' ),
				'type'        => Controls_Manager::SWITCHER,
				'condition'   => array(
					'premium_modal_box_display_on' => 'button',
				),
				'description' => __( 'Enable or disable button icon', 'premium-addons-for-elementor' ),
			)
		);

		$this->add_control(
			'icon_type',
			array(
				'label'     => __( 'Icon Type', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::SELECT,
				'options'   => array(
					'icon' => __( 'Font Awesome Icon', 'premium-addons-for-elementor' ),
					'svg'  => __( 'SVG Code', 'premium-addons-for-elementor' ),
				),
				'default'   => 'icon',
				'condition' => array(
					'premium_modal_box_display_on' => 'button',
				),
			)
		);

		$this->add_control(
			'premium_modal_box_button_icon_selection_updated',
			array(
				'label'            => __( 'Icon', 'premium-addons-for-elementor' ),
				'type'             => Controls_Manager::ICONS,
				'fa4compatibility' => 'premium_modal_box_button_icon_selection',
				'default'          => array(
					'value'   => 'fas fa-star',
					'library' => 'fa-solid',
				),
				'label_block'      => true,
				'condition'        => array(
					'premium_modal_box_display_on'    => 'button',
					'premium_modal_box_icon_switcher' => 'yes',
					'icon_type'                       => 'icon',
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
					'premium_modal_box_display_on'    => 'button',
					'premium_modal_box_icon_switcher' => 'yes',
					'icon_type'                       => 'svg',
				),
			)
		);

		$common_conditions = array(
			'premium_modal_box_display_on'    => 'button',
			'premium_modal_box_icon_switcher' => 'yes',
		);

		$this->add_control(
			'draw_svg',
			array(
				'label'     => __( 'Draw Icon', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::SWITCHER,
				'classes'   => $draw_icon ? '' : 'editor-pa-control-disabled',
				'condition' => array_merge(
					$common_conditions,
					array(
						'icon_type' => array( 'icon', 'svg' ),
						'premium_modal_box_button_icon_selection_updated[library]!' => 'svg',
					)
				),
			)
		);

		if ( $draw_icon ) {
			$this->add_control(
				'path_width',
				array(
					'label'     => __( 'Path Thickness', 'premium-addons-for-elementor' ),
					'type'      => Controls_Manager::SLIDER,
					'range'     => array(
						'px' => array(
							'min'  => 0,
							'max'  => 50,
							'step' => 0.1,
						),
					),
					'condition' => array_merge(
						$common_conditions,
						array(
							'icon_type' => array( 'icon', 'svg' ),
						)
					),
					'selectors' => array(
						'{{WRAPPER}} .premium-modal-trigger-btn svg *' => 'stroke-width: {{SIZE}}',
					),
				)
			);

			$this->add_control(
				'svg_sync',
				array(
					'label'     => __( 'Draw All Paths Together', 'premium-addons-for-elementor' ),
					'type'      => Controls_Manager::SWITCHER,
					'condition' => array_merge(
						$common_conditions,
						array(
							'icon_type' => array( 'icon', 'svg' ),
							'draw_svg'  => 'yes',
						)
					),
				)
			);

			$this->add_control(
				'frames',
				array(
					'label'       => __( 'Speed', 'premium-addons-for-elementor' ),
					'type'        => Controls_Manager::NUMBER,
					'description' => __( 'Larger value means longer animation duration.', 'premium-addons-for-elementor' ),
					'default'     => 5,
					'min'         => 1,
					'max'         => 100,
					'condition'   => array_merge(
						$common_conditions,
						array(
							'icon_type' => array( 'icon', 'svg' ),
							'draw_svg'  => 'yes',
						)
					),
				)
			);

			$this->add_control(
				'svg_loop',
				array(
					'label'        => __( 'Loop', 'premium-addons-for-elementor' ),
					'type'         => Controls_Manager::SWITCHER,
					'return_value' => 'true',
					'default'      => 'true',
					'condition'    => array_merge(
						$common_conditions,
						array(
							'icon_type' => array( 'icon', 'svg' ),
							'draw_svg'  => 'yes',
						)
					),
				)
			);

			$this->add_control(
				'svg_reverse',
				array(
					'label'        => __( 'Reverse', 'premium-addons-for-elementor' ),
					'type'         => Controls_Manager::SWITCHER,
					'return_value' => 'true',
					'condition'    => array_merge(
						$common_conditions,
						array(
							'icon_type' => array( 'icon', 'svg' ),
							'draw_svg'  => 'yes',
						)
					),
				)
			);

			$this->add_control(
				'start_point',
				array(
					'label'       => __( 'Start Point (%)', 'premium-addons-for-elementor' ),
					'type'        => Controls_Manager::SLIDER,
					'description' => __( 'Set the point that the SVG should start from.', 'premium-addons-for-elementor' ),
					'default'     => array(
						'unit' => '%',
						'size' => 0,
					),
					'condition'   => array_merge(
						$common_conditions,
						array(
							'icon_type'    => array( 'icon', 'svg' ),
							'draw_svg'     => 'yes',
							'svg_reverse!' => 'true',
						)
					),
				)
			);

			$this->add_control(
				'end_point',
				array(
					'label'       => __( 'End Point (%)', 'premium-addons-for-elementor' ),
					'type'        => Controls_Manager::SLIDER,
					'description' => __( 'Set the point that the SVG should end at.', 'premium-addons-for-elementor' ),
					'default'     => array(
						'unit' => '%',
						'size' => 0,
					),
					'condition'   => array_merge(
						$common_conditions,
						array(
							'icon_type'   => array( 'icon', 'svg' ),
							'draw_svg'    => 'yes',
							'svg_reverse' => 'true',
						)
					),

				)
			);

			$this->add_control(
				'svg_hover',
				array(
					'label'        => __( 'Only Play on Hover', 'premium-addons-for-elementor' ),
					'type'         => Controls_Manager::SWITCHER,
					'return_value' => 'true',
					'condition'    => array_merge(
						$common_conditions,
						array(
							'icon_type' => array( 'icon', 'svg' ),
							'draw_svg'  => 'yes',
						)
					),
				)
			);

			$this->add_control(
				'svg_yoyo',
				array(
					'label'     => __( 'Yoyo Effect', 'premium-addons-for-elementor' ),
					'type'      => Controls_Manager::SWITCHER,
					'condition' => array_merge(
						$common_conditions,
						array(
							'icon_type' => array( 'icon', 'svg' ),
							'draw_svg'  => 'yes',
							'svg_loop'  => 'true',
						)
					),
				)
			);
		} else {

			Helper_Functions::get_draw_svg_notice(
				$this,
				'modal',
				array_merge(
					$common_conditions,
					array(
						'icon_type' => array( 'icon', 'svg' ),
						'premium_modal_box_button_icon_selection_updated[library]!' => 'svg',
					)
				)
			);

		}

		$this->add_control(
			'premium_modal_box_icon_position',
			array(
				'label'       => __( 'Icon Position', 'premium-addons-for-elementor' ),
				'type'        => Controls_Manager::SELECT,
				'default'     => 'before',
				'options'     => array(
					'before' => __( 'Before', 'premium-addons-for-elementor' ),
					'after'  => __( 'After', 'premium-addons-for-elementor' ),
				),
				'label_block' => true,
				'condition'   => array(
					'premium_modal_box_display_on'    => 'button',
					'premium_modal_box_icon_switcher' => 'yes',
				),
			)
		);

		$this->add_control(
			'premium_modal_box_icon_before_size',
			array(
				'label'     => __( 'Icon Size', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::SLIDER,
				'selectors' => array(
					'{{WRAPPER}} .premium-modal-trigger-btn i' => 'font-size: {{SIZE}}px',
					'{{WRAPPER}} .premium-modal-trigger-btn svg' => 'width: {{SIZE}}px !important; height: {{SIZE}}px !important',
				),
				'condition' => array(
					'premium_modal_box_display_on'    => 'button',
					'icon_type'                       => 'icon',
					'premium_modal_box_icon_switcher' => 'yes',
				),
			)
		);

		$this->add_responsive_control(
			'svg_icon_width',
			array(
				'label'      => __( 'Icon Width', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', 'em', '%' ),
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
					'premium_modal_box_display_on'    => 'button',
					'premium_modal_box_icon_switcher' => 'yes',
					'icon_type'                       => 'svg',
				),
				'selectors'  => array(
					'{{WRAPPER}} .premium-modal-trigger-btn svg' => 'width: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'svg_icon_height',
			array(
				'label'      => __( 'Icon Height', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', 'em' ),
				'range'      => array(
					'px' => array(
						'min' => 1,
						'max' => 300,
					),
					'em' => array(
						'min' => 1,
						'max' => 30,
					),
				),
				'condition'  => array(
					'premium_modal_box_display_on'    => 'button',
					'premium_modal_box_icon_switcher' => 'yes',
					'icon_type'                       => 'svg',
				),
				'selectors'  => array(
					'{{WRAPPER}} .premium-modal-trigger-btn svg' => 'height: {{SIZE}}{{UNIT}}',
				),
			)
		);

		if ( ! $this->check_rtl() ) {
			$this->add_control(
				'premium_modal_box_icon_before_spacing',
				array(
					'label'     => __( 'Icon Spacing', 'premium-addons-for-elementor' ),
					'type'      => Controls_Manager::SLIDER,
					'condition' => array(
						'premium_modal_box_display_on'    => 'button',
						'premium_modal_box_icon_switcher' => 'yes',
						'premium_modal_box_icon_position' => 'before',
					),
					'default'   => array(
						'size' => 15,
					),
					'selectors' => array(
						'{{WRAPPER}} .premium-modal-trigger-btn i, {{WRAPPER}} .premium-modal-trigger-btn svg' => 'margin-right: {{SIZE}}px',
					),
					'separator' => 'after',
				)
			);

			$this->add_control(
				'premium_modal_box_icon_after_spacing',
				array(
					'label'     => __( 'Icon Spacing', 'premium-addons-for-elementor' ),
					'type'      => Controls_Manager::SLIDER,
					'default'   => array(
						'size' => 15,
					),
					'selectors' => array(
						'{{WRAPPER}} .premium-modal-trigger-btn i, {{WRAPPER}} .premium-modal-trigger-btn svg' => 'margin-left: {{SIZE}}px',
					),
					'separator' => 'after',
					'condition' => array(
						'premium_modal_box_display_on'    => 'button',
						'premium_modal_box_icon_switcher' => 'yes',
						'premium_modal_box_icon_position' => 'after',
					),
				)
			);
		}

		if ( $this->check_rtl() ) {
			$this->add_control(
				'premium_modal_box_icon_rtl_before_spacing',
				array(
					'label'     => __( 'Icon Spacing', 'premium-addons-for-elementor' ),
					'type'      => Controls_Manager::SLIDER,
					'condition' => array(
						'premium_modal_box_display_on'    => 'button',
						'premium_modal_box_icon_switcher' => 'yes',
						'premium_modal_box_icon_position' => 'before',
					),
					'default'   => array(
						'size' => 15,
					),
					'selectors' => array(
						'{{WRAPPER}} .premium-modal-trigger-btn i, {{WRAPPER}} .premium-modal-trigger-btn svg' => 'margin-left: {{SIZE}}px',
					),
					'separator' => 'after',
				)
			);

			$this->add_control(
				'premium_modal_box_icon_rtl_after_spacing',
				array(
					'label'     => __( 'Icon Spacing', 'premium-addons-for-elementor' ),
					'type'      => Controls_Manager::SLIDER,
					'default'   => array(
						'size' => 15,
					),
					'selectors' => array(
						'{{WRAPPER}} .premium-modal-trigger-btn i, {{WRAPPER}} .premium-modal-trigger-btn svg' => 'margin-right: {{SIZE}}px',
					),
					'separator' => 'after',
					'condition' => array(
						'premium_modal_box_display_on'    => 'button',
						'premium_modal_box_icon_switcher' => 'yes',
						'premium_modal_box_icon_position' => 'after',
					),
				)
			);
		}

		$this->add_control(
			'premium_modal_box_button_size',
			array(
				'label'       => __( 'Button Size', 'premium-addons-for-elementor' ),
				'type'        => Controls_Manager::SELECT,
				'options'     => array(
					'sm'    => __( 'Small', 'premium-addons-for-elementor' ),
					'md'    => __( 'Medium', 'premium-addons-for-elementor' ),
					'lg'    => __( 'Large', 'premium-addons-for-elementor' ),
					'block' => __( 'Block', 'premium-addons-for-elementor' ),
				),
				'label_block' => true,
				'default'     => 'lg',
				'condition'   => array(
					'premium_modal_box_display_on' => 'button',
				),
			)
		);

		$this->add_control(
			'premium_modal_box_image_src',
			array(
				'label'       => __( 'Image', 'premium-addons-for-elementor' ),
				'type'        => Controls_Manager::MEDIA,
				'dynamic'     => array( 'active' => true ),
				'default'     => array(
					'url' => Utils::get_placeholder_image_src(),
				),
				'label_block' => true,
				'condition'   => array(
					'premium_modal_box_display_on' => 'image',
				),
			)
		);

		$this->add_control(
			'premium_modal_box_selector_text',
			array(
				'label'       => __( 'Text', 'premium-addons-for-elementor' ),
				'type'        => Controls_Manager::TEXT,
				'dynamic'     => array( 'active' => true ),
				'label_block' => true,
				'default'     => __( 'Premium Addons', 'premium-addons-for-elementor' ),
				'condition'   => array(
					'premium_modal_box_display_on' => 'text',
				),
			)
		);

		$this->add_control(
			'lottie_url',
			array(
				'label'       => __( 'Animation JSON URL', 'premium-addons-for-elementor' ),
				'type'        => Controls_Manager::TEXT,
				'dynamic'     => array( 'active' => true ),
				'description' => 'Get JSON code URL from <a href="https://lottiefiles.com/" target="_blank">here</a>',
				'label_block' => true,
				'condition'   => array(
					'premium_modal_box_display_on' => 'animation',
				),
			)
		);

		$this->add_control(
			'lottie_loop',
			array(
				'label'        => __( 'Loop', 'premium-addons-for-elementor' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'true',
				'default'      => 'true',
				'condition'    => array(
					'premium_modal_box_display_on' => 'animation',
				),
			)
		);

		$this->add_control(
			'lottie_reverse',
			array(
				'label'        => __( 'Reverse', 'premium-addons-for-elementor' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'true',
				'condition'    => array(
					'premium_modal_box_display_on' => 'animation',
				),
			)
		);

		$this->add_control(
			'lottie_hover',
			array(
				'label'        => __( 'Only Play on Hover', 'premium-addons-for-elementor' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'true',
				'condition'    => array(
					'premium_modal_box_display_on' => 'animation',
				),
			)
		);

		$this->add_responsive_control(
			'trigger_image_animation_size',
			array(
				'label'      => __( 'Size', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', 'em', '%' ),
				'range'      => array(
					'px' => array(
						'min' => 1,
						'max' => 800,
					),
					'em' => array(
						'min' => 1,
						'max' => 30,
					),
				),
				'selectors'  => array(
					'{{WRAPPER}} .premium-modal-trigger-img, {{WRAPPER}} .premium-modal-trigger-animation'    => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}}',
				),
				'condition'  => array(
					'premium_modal_box_display_on' => array( 'image', 'animation' ),
				),
			)
		);

		$this->add_control(
			'premium_modal_box_popup_delay',
			array(
				'label'       => __( 'Delay in Popup Display (Sec)', 'premium-addons-for-elementor' ),
				'type'        => Controls_Manager::NUMBER,
				'description' => __( 'When should the popup appear during page load? The value are counted in seconds', 'premium-addons-for-elementor' ),
				'default'     => 1,
				'condition'   => array(
					'premium_modal_box_display_on' => 'pageload',
				),
			)
		);

		$this->add_responsive_control(
			'premium_modal_box_selector_align',
			array(
				'label'      => __( 'Alignment', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::CHOOSE,
				'options'    => array(
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
				'toggle'     => false,
				'default'    => 'center',
				'selectors'  => array(
					'{{WRAPPER}} .premium-modal-trigger-container' => 'text-align: {{VALUE}};',
				),
				'conditions' => array(
					'relation' => 'or',
					'terms'    => array(
						array(
							'terms' => array(
								array(
									'name'     => 'premium_modal_box_display_on',
									'operator' => '!=',
									'value'    => 'button',
								),
								array(
									'name'     => 'premium_modal_box_display_on',
									'operator' => '!=',
									'value'    => 'pageload',
								),
							),
						),
						array(
							'relation' => 'and',
							'terms'    => array(
								array(
									'name'  => 'premium_modal_box_display_on',
									'value' => 'button',
								),
								array(
									'name'     => 'premium_modal_box_button_size',
									'operator' => '!=',
									'value'    => 'block',
								),
							),
						),
					),
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

		$docs = array(
			'https://premiumaddons.com/docs/modal-box-widget-tutorial/' => __( 'Getting started »', 'premium-addons-for-elementor' ),
			'https://premiumaddons.com/docs/how-to-create-elementor-template-to-be-used-with-premium-addons' => __( 'How to create an Elementor template to be used in Modal Box widget »', 'premium-addons-for-elementor' ),
			'https://premiumaddons.com/docs/how-can-i-insert-a-video-box-inside-premium-modal-box-widget/' => __( 'How Can I Insert a Video Box inside Premium Modal Box Widget »', 'premium-addons-for-elementor' ),
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
			'premium_modal_box_selector_style_section',
			array(
				'label'     => __( 'Trigger', 'premium-addons-for-elementor' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => array(
					'premium_modal_box_display_on!' => 'pageload',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Css_Filter::get_type(),
			array(
				'name'      => 'trigger_css_filters',
				'selector'  => '{{WRAPPER}} .premium-modal-trigger-animation',
				'condition' => array(
					'premium_modal_box_display_on' => 'animation',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Css_Filter::get_type(),
			array(
				'name'      => 'trigger_hover_css_filters',
				'label'     => __( 'Hover CSS Filters', 'premium-addons-for-elementor' ),
				'selector'  => '{{WRAPPER}} .premium-modal-trigger-animation:hover',
				'condition' => array(
					'premium_modal_box_display_on' => 'animation',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'      => 'selectortext',
				'global'    => array(
					'default' => Global_Typography::TYPOGRAPHY_PRIMARY,
				),
				'selector'  => '{{WRAPPER}} .premium-modal-trigger-btn, {{WRAPPER}} .premium-modal-trigger-text',
				'condition' => array(
					'premium_modal_box_display_on' => array( 'button', 'text' ),
				),
			)
		);

		$this->add_control(
			'svg_color',
			array(
				'label'     => __( 'After Draw Fill Color', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'global'    => false,
				'separator' => 'after',
				'condition' => array(
					'premium_modal_box_icon_switcher' => 'yes',
					'premium_modal_box_display_on'    => 'button',
					'icon_type'                       => array( 'icon', 'svg' ),
					'draw_svg'                        => 'yes',
				),
			)
		);

		$this->start_controls_tabs( 'premium_modal_box_button_style' );

		$this->start_controls_tab(
			'premium_modal_box_tab_selector_normal',
			array(
				'label'     => __( 'Normal', 'premium-addons-for-elementor' ),
				'condition' => array(
					'premium_modal_box_display_on' => array( 'button', 'text', 'image' ),
				),
			)
		);

		$this->add_control(
			'premium_modal_box_button_text_color',
			array(
				'label'     => __( 'Color', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'global'    => array(
					'default' => Global_Colors::COLOR_SECONDARY,
				),
				'selectors' => array(
					'{{WRAPPER}} .premium-modal-trigger-btn, {{WRAPPER}} .premium-modal-trigger-text' => 'color:{{VALUE}};',
				),
				'condition' => array(
					'premium_modal_box_display_on' => array( 'button', 'text' ),
				),
			)
		);

		$this->add_control(
			'premium_modal_box_button_icon_color',
			array(
				'label'     => __( 'Icon Color', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'global'    => array(
					'default' => Global_Colors::COLOR_SECONDARY,
				),
				'selectors' => array(
					'{{WRAPPER}} .premium-modal-trigger-btn i' => 'color:{{VALUE}}',
					'{{WRAPPER}} .premium-modal-trigger-btn svg, {{WRAPPER}} .premium-modal-trigger-btn svg *' => 'fill: {{VALUE}};',
				),
				'condition' => array(
					'premium_modal_box_icon_switcher' => 'yes',
					'premium_modal_box_display_on'    => 'button',
				),
			)
		);

		if ( $draw_icon ) {
			$this->add_control(
				'stroke_color',
				array(
					'label'     => __( 'Stroke Color', 'premium-addons-for-elementor' ),
					'type'      => Controls_Manager::COLOR,
					'global'    => array(
						'default' => Global_Colors::COLOR_ACCENT,
					),
					'condition' => array(
						'premium_modal_box_icon_switcher' => 'yes',
						'premium_modal_box_display_on'    => 'button',
						'icon_type'                       => array( 'icon', 'svg' ),
					),
					'selectors' => array(
						'{{WRAPPER}} .premium-modal-trigger-btn svg *' => 'stroke: {{VALUE}};',
					),
				)
			);
		}

		$this->add_control(
			'premium_modal_box_selector_background',
			array(
				'label'     => __( 'Background Color', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'global'    => array(
					'default' => Global_Colors::COLOR_PRIMARY,
				),
				'selectors' => array(
					'{{WRAPPER}} .premium-modal-trigger-btn'   => 'background-color: {{VALUE}};',
				),
				'condition' => array(
					'premium_modal_box_display_on' => 'button',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'      => 'selector_border',
				'selector'  => '{{WRAPPER}} .premium-modal-trigger-btn,{{WRAPPER}} .premium-modal-trigger-text, {{WRAPPER}} .premium-modal-trigger-img',
				'condition' => array(
					'premium_modal_box_display_on' => array( 'button', 'text', 'image' ),
				),
			)
		);

		$this->add_control(
			'premium_modal_box_selector_border_radius',
			array(
				'label'      => __( 'Border Radius', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', '%', 'em' ),
				'default'    => array(
					'size' => 0,
				),
				'selectors'  => array(
					'{{WRAPPER}} .premium-modal-trigger-btn, {{WRAPPER}} .premium-modal-trigger-text, {{WRAPPER}} .premium-modal-trigger-img'     => 'border-radius:{{SIZE}}{{UNIT}};',
				),
				'condition'  => array(
					'premium_modal_box_display_on' => array( 'button', 'text', 'image' ),
				),
			)
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'label'     => __( 'Shadow', 'premium-addons-for-elementor' ),
				'name'      => 'premium_modal_box_selector_box_shadow',
				'selector'  => '{{WRAPPER}} .premium-modal-trigger-btn, {{WRAPPER}} .premium-modal-trigger-img',
				'condition' => array(
					'premium_modal_box_display_on' => array( 'button', 'image' ),
				),
			)
		);

		$this->add_group_control(
			Group_Control_Text_Shadow::get_type(),
			array(
				'name'      => 'premium_modal_box_selector_text_shadow',
				'selector'  => '{{WRAPPER}} .premium-modal-trigger-text',
				'condition' => array(
					'premium_modal_box_display_on' => 'text',
				),
			)
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'premium_modal_box_tab_selector_hover',
			array(
				'label'     => __( 'Hover', 'premium-addons-for-elementor' ),
				'condition' => array(
					'premium_modal_box_display_on' => array( 'button', 'text', 'image' ),
				),
			)
		);

		$this->add_control(
			'premium_modal_box_button_text_color_hover',
			array(
				'label'     => __( 'Color', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'global'    => array(
					'default' => Global_Colors::COLOR_PRIMARY,
				),
				'selectors' => array(
					'{{WRAPPER}} .premium-modal-trigger-btn:hover, {{WRAPPER}} .premium-modal-trigger-text:hover' => 'color:{{VALUE}};',
				),
				'condition' => array(
					'premium_modal_box_display_on' => array( 'button', 'text' ),
				),
			)
		);

		$this->add_control(
			'premium_modal_box_button_icon_hover_color',
			array(
				'label'     => __( 'Icon Color', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'global'    => array(
					'default' => Global_Colors::COLOR_PRIMARY,
				),
				'selectors' => array(
					'{{WRAPPER}} .premium-modal-trigger-btn:hover i' => 'color: {{VALUE}}',
					'{{WRAPPER}} .premium-modal-trigger-btn:hover svg, {{WRAPPER}} .premium-modal-trigger-btn:hover svg *' => 'fill: {{VALUE}};',
				),
				'condition' => array(
					'premium_modal_box_icon_switcher' => 'yes',
					'premium_modal_box_display_on'    => 'button',
				),
			)
		);

		if ( $draw_icon ) {
			$this->add_control(
				'stroke_color_hover',
				array(
					'label'     => __( 'Stroke Color', 'premium-addons-for-elementor' ),
					'type'      => Controls_Manager::COLOR,
					'global'    => array(
						'default' => Global_Colors::COLOR_ACCENT,
					),
					'condition' => array(
						'premium_modal_box_icon_switcher' => 'yes',
						'premium_modal_box_display_on'    => 'button',
						'icon_type'                       => array( 'icon', 'svg' ),
					),
					'selectors' => array(
						'{{WRAPPER}} .premium-modal-trigger-btn:hover svg *' => 'stroke: {{VALUE}};',
					),
				)
			);
		}

		$this->add_control(
			'premium_modal_box_selector_hover_background',
			array(
				'label'     => __( 'Background Color', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'global'    => array(
					'default' => Global_Colors::COLOR_TEXT,
				),
				'selectors' => array(
					'{{WRAPPER}} .premium-modal-trigger-btn:hover' => 'background: {{VALUE}};',
				),
				'condition' => array(
					'premium_modal_box_display_on' => 'button',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'      => 'selector_border_hover',
				'selector'  => '{{WRAPPER}} .premium-modal-trigger-btn:hover,
                    {{WRAPPER}} .premium-modal-trigger-text:hover, {{WRAPPER}} .premium-modal-trigger-img:hover',
				'condition' => array(
					'premium_modal_box_display_on' => array( 'button', 'text', 'image' ),
				),
			)
		);

		$this->add_control(
			'premium_modal_box_selector_border_radius_hover',
			array(
				'label'      => __( 'Border Radius', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', '%', 'em' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-modal-trigger-btn:hover,{{WRAPPER}} .premium-modal-trigger-text:hover, {{WRAPPER}} .premium-modal-trigger-img:hover'     => 'border-radius:{{SIZE}}{{UNIT}};',
				),
				'condition'  => array(
					'premium_modal_box_display_on' => array( 'button', 'text', 'image' ),
				),
			)
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'label'     => __( 'Shadow', 'premium-addons-for-elementor' ),
				'name'      => 'premium_modal_box_selector_box_shadow_hover',
				'selector'  => '{{WRAPPER}} .premium-modal-trigger-btn:hover, {{WRAPPER}} .premium-modal-trigger-text:hover, {{WRAPPER}} .premium-modal-trigger-img:hover',
				'condition' => array(
					'premium_modal_box_display_on' => array( 'button', 'text', 'image' ),
				),
			)
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->add_responsive_control(
			'premium_modal_box_selector_padding',
			array(
				'label'      => __( 'Padding', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'separator'  => 'before',
				'selectors'  => array(
					'{{WRAPPER}} .premium-modal-trigger-btn, {{WRAPPER}} .premium-modal-trigger-text' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}',
				),
				'condition'  => array(
					'premium_modal_box_display_on' => array( 'button', 'text' ),
				),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'premium_modal_box_header_settings',
			array(
				'label'     => __( 'Header', 'premium-addons-for-elementor' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => array(
					'premium_modal_box_header_switcher' => 'yes',
				),
			)
		);

		$this->add_control(
			'premium_modal_box_header_text_color',
			array(
				'label'     => __( 'Color', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .premium-modal-box-modal-title' => 'color: {{VALUE}}',
					'{{WRAPPER}} .premium-modal-box-modal-title svg' => 'fill: {{VALUE}}',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'headertext',
				'global'   => array(
					'default' => Global_Typography::TYPOGRAPHY_PRIMARY,
				),
				'selector' => '{{WRAPPER}} .premium-modal-box-modal-title',
			)
		);

		$this->add_control(
			'premium_modal_box_header_background',
			array(
				'label'     => __( 'Background Color', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .premium-modal-box-modal-header'  => 'background: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'     => 'premium_modal_header_border',
				'selector' => '{{WRAPPER}} .premium-modal-box-modal-header',
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'premium_modal_box_upper_close_button_section',
			array(
				'label'     => __( 'Upper Close Button', 'premium-addons-for-elementor' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => array(
					'premium_modal_box_upper_close'     => 'yes',
					'premium_modal_box_header_switcher' => 'yes',
				),
			)
		);

		$this->add_responsive_control(
			'premium_modal_box_upper_close_button_size',
			array(
				'label'      => __( 'Size', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', '%', 'em' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-modal-box-modal-header button' => 'font-size: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->start_controls_tabs( 'premium_modal_box_upper_close_button_style' );

		$this->start_controls_tab(
			'premium_modal_box_upper_close_button_normal',
			array(
				'label' => __( 'Normal', 'premium-addons-for-elementor' ),
			)
		);

		$this->add_control(
			'premium_modal_box_upper_close_button_normal_color',
			array(
				'label'     => __( 'Color', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .premium-modal-box-modal-close' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'premium_modal_box_upper_close_button_background_color',
			array(
				'label'     => __( 'Background Color', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .premium-modal-box-modal-close' => 'background:{{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'     => 'premium_modal_upper_border',
				'selector' => '{{WRAPPER}} .premium-modal-box-modal-close',
			)
		);

		$this->add_control(
			'premium_modal_upper_border_radius',
			array(
				'label'      => __( 'Border Radius', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', '%', 'em' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-modal-box-modal-close'     => 'border-radius:{{SIZE}}{{UNIT}};',
				),
				'separator'  => 'after',
			)
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'premium_modal_box_upper_close_button_hover',
			array(
				'label' => __( 'Hover', 'premium-addons-for-elementor' ),
			)
		);

		$this->add_control(
			'premium_modal_box_upper_close_button_hover_color',
			array(
				'label'     => __( 'Color', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .premium-modal-box-modal-close:hover' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'premium_modal_box_upper_close_button_background_color_hover',
			array(
				'label'     => __( 'Background Color', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .premium-modal-box-modal-close:hover' => 'background:{{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'     => 'premium_modal_upper_border_hover',
				'selector' => '{{WRAPPER}} .premium-modal-box-modal-close:hover',
			)
		);

		$this->add_control(
			'premium_modal_upper_border_radius_hover',
			array(
				'label'      => __( 'Border Radius', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', '%', 'em' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-modal-box-modal-close:hover'     => 'border-radius:{{SIZE}}{{UNIT}};',
				),
				'separator'  => 'after',
			)
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->add_responsive_control(
			'premium_modal_box_upper_close_button_padding',
			array(
				'label'      => __( 'Padding', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-modal-box-modal-close' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}',
				),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'premium_modal_box_lower_close_button_section',
			array(
				'label'     => __( 'Lower Close Button', 'premium-addons-for-elementor' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => array(
					'premium_modal_box_lower_close' => 'yes',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'lowerclose',
				'global'   => array(
					'default' => Global_Typography::TYPOGRAPHY_PRIMARY,
				),
				'selector' => '{{WRAPPER}} .premium-modal-box-modal-lower-close',
			)
		);

		$this->add_responsive_control(
			'premium_modal_box_lower_close_button_width',
			array(
				'label'      => __( 'Width', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', '%', 'em' ),
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
				'selectors'  => array(
					'{{WRAPPER}} .premium-modal-box-modal-lower-close' => 'min-width: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->start_controls_tabs( 'premium_modal_box_lower_close_button_style' );

		$this->start_controls_tab(
			'premium_modal_box_lower_close_button_normal',
			array(
				'label' => __( 'Normal', 'premium-addons-for-elementor' ),
			)
		);

		$this->add_control(
			'premium_modal_box_lower_close_button_normal_color',
			array(
				'label'     => __( 'Color', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'global'    => array(
					'default' => Global_Colors::COLOR_SECONDARY,
				),
				'selectors' => array(
					'{{WRAPPER}} .premium-modal-box-modal-lower-close' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'premium_modal_box_lower_close_button_background_normal_color',
			array(
				'label'     => __( 'Background Color', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'global'    => array(
					'default' => Global_Colors::COLOR_PRIMARY,
				),
				'selectors' => array(
					'{{WRAPPER}} .premium-modal-box-modal-lower-close' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'     => 'premium_modal_box_lower_close_border',
				'selector' => '{{WRAPPER}} .premium-modal-box-modal-lower-close',
			)
		);

		$this->add_control(
			'premium_modal_box_lower_close_border_radius',
			array(
				'label'      => __( 'Border Radius', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', '%', 'em' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-modal-box-modal-lower-close' => 'border-radius: {{SIZE}}{{UNIT}};',
				),
				'separator'  => 'after',
			)
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'premium_modal_box_lower_close_button_hover',
			array(
				'label' => __( 'Hover', 'premium-addons-for-elementor' ),
			)
		);

		$this->add_control(
			'premium_modal_box_lower_close_button_hover_color',
			array(
				'label'     => __( 'Color', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'global'    => array(
					'default' => Global_Colors::COLOR_PRIMARY,
				),
				'selectors' => array(
					'{{WRAPPER}} .premium-modal-box-modal-lower-close:hover' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'premium_modal_box_lower_close_button_background_hover_color',
			array(
				'label'     => __( 'Background Color', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'global'    => array(
					'default' => Global_Colors::COLOR_SECONDARY,
				),
				'selectors' => array(
					'{{WRAPPER}} .premium-modal-box-modal-lower-close:hover' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'     => 'premium_modal_box_lower_close_border_hover',
				'selector' => '{{WRAPPER}} .premium-modal-box-modal-lower-close:hover',
			)
		);

		$this->add_control(
			'premium_modal_box_lower_close_border_radius_hover',
			array(
				'label'      => __( 'Border Radius', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', '%', 'em' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-modal-box-modal-lower-close:hover' => 'border-radius: {{SIZE}}{{UNIT}};',
				),
				'separator'  => 'after',
			)
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->add_responsive_control(
			'premium_modal_box_lower_close_button_padding',
			array(
				'label'      => __( 'Padding', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-modal-box-modal-lower-close' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}',
				),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'premium_modal_box_style',
			array(
				'label' => __( 'Modal Box', 'premium-addons-for-elementor' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_control(
			'text_content_color',
			array(
				'label'     => __( 'Text Color', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .premium-modal-box-modal-body'  => 'color: {{VALUE}}',
				),
				'condition' => array(
					'premium_modal_box_content_type' => 'editor',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'      => 'content_typography',
				'selector'  => '{{WRAPPER}} .premium-modal-box-modal-body',
				'condition' => array(
					'premium_modal_box_content_type' => 'editor',
				),
			)
		);

		$this->add_control(
			'premium_modal_box_content_background',
			array(
				'label'     => __( 'Content Background Color', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .premium-modal-box-modal-body'  => 'background: {{VALUE}};',
				),
			)
		);

		$this->add_responsive_control(
			'premium_modal_box_modal_size',
			array(
				'label'       => __( 'Width', 'premium-addons-for-elementor' ),
				'type'        => Controls_Manager::SLIDER,
				'size_units'  => array( 'px', '%', 'em', 'custom' ),
				'range'       => array(
					'px' => array(
						'min' => 50,
						'max' => 1500,
					),
					'em' => array(
						'min' => 1,
						'max' => 50,
					),
				),
				'separator'   => 'before',
				'label_block' => true,
				'selectors'   => array(
					'{{WRAPPER}} .premium-modal-box-modal-dialog'  => 'width: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'premium_modal_box_modal_max_height',
			array(
				'label'       => __( 'Max Height', 'premium-addons-for-elementor' ),
				'type'        => Controls_Manager::SLIDER,
				'size_units'  => array( 'px', 'em', 'vh', 'custom' ),
				'range'       => array(
					'px' => array(
						'min' => 50,
						'max' => 1500,
					),
					'em' => array(
						'min' => 1,
						'max' => 50,
					),
				),
				'label_block' => true,
				'selectors'   => array(
					'{{WRAPPER}} .premium-modal-box-modal-dialog'  => 'max-height: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'content_overflow',
			array(
				'label'     => __( 'Overflow', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::SELECT,
				'options'   => array(
					'auto'    => __( 'Auto', 'premium-addons-for-elementor' ),
					'visible' => __( 'Visible', 'premium-addons-for-elementor' ),
					'hidden'  => __( 'Hidden', 'premium-addons-for-elementor' ),
					'scroll'  => __( 'Scroll', 'premium-addons-for-elementor' ),
				),
				'default'   => 'auto',
				'selectors' => array(
					'{{WRAPPER}} .premium-modal-box-modal-dialog'   => 'overflow: {{VALUE}}',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			array(
				'name'     => 'premium_modal_box_modal_background',
				'types'    => array( 'classic', 'gradient' ),
				'selector' => '{{WRAPPER}} .premium-modal-box-modal',
			)
		);

		$this->add_control(
			'premium_modal_box_footer_background',
			array(
				'label'     => __( 'Footer Background Color', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .premium-modal-box-modal-footer'  => 'background: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'     => 'contentborder',
				'selector' => '{{WRAPPER}} .premium-modal-box-modal-dialog',
			)
		);

		$this->add_control(
			'premium_modal_box_border_radius',
			array(
				'label'      => __( 'Border Radius', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', '%', 'em' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-modal-box-modal-dialog'     => 'border-radius: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name'     => 'premium_modal_box_shadow',
				'selector' => '{{WRAPPER}} .premium-modal-box-modal-dialog',
			)
		);

		$this->add_responsive_control(
			'premium_modal_box_margin',
			array(
				'label'      => __( 'Margin', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-modal-box-modal-dialog' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}',
				),
			)
		);

		$this->add_responsive_control(
			'premium_modal_box_padding',
			array(
				'label'      => __( 'Padding', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-modal-box-modal-body' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}',
				),
			)
		);

		$this->end_controls_section();
	}


	/**
	 * Render Header Icon
	 *
	 * Render HTML markup for modal header icon.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param boolean $new new icon.
	 * @param boolean $migrate icon migrated.
	 */
	protected function render_header_icon( $new, $migrate ) {

		$settings = $this->get_settings_for_display();

		$header_icon = $settings['premium_modal_box_icon_selection'];

		if ( 'fonticon' === $header_icon ) {
			if ( $new || $migrate ) :
				Icons_Manager::render_icon( $settings['premium_modal_box_font_icon_updated'], array( 'aria-hidden' => 'true' ) );
			else : ?>
				<i <?php echo wp_kses_post( $this->get_render_attribute_string( 'title_icon' ) ); ?>></i>
				<?php
			endif;
		} elseif ( 'image' === $header_icon ) {
			?>
			<img <?php echo wp_kses_post( $this->get_render_attribute_string( 'title_icon' ) ); ?>>
			<?php
		} elseif ( 'animation' === $header_icon ) {
			$this->add_render_attribute(
				'header_lottie',
				array(
					'class'               => array(
						'premium-modal-header-lottie',
						'premium-lottie-animation',
					),
					'data-lottie-url'     => $settings['header_lottie_url'],
					'data-lottie-loop'    => $settings['header_lottie_loop'],
					'data-lottie-reverse' => $settings['header_lottie_reverse'],
				)
			);
			?>
				<div <?php echo wp_kses_post( $this->get_render_attribute_string( 'header_lottie' ) ); ?>></div>
			<?php
		}
	}

	/**
	 * Render Modal Box widget output on the frontend.
	 *
	 * Written in PHP and used to generate the final HTML.
	 *
	 * @since 1.0.0
	 * @access protected
	 */
	protected function render() {

		$settings = $this->get_settings_for_display();

		$trigger = $settings['premium_modal_box_display_on'];

		$this->add_inline_editing_attributes( 'premium_modal_box_selector_text' );

		$this->add_render_attribute(
			'trigger',
			array(
				'data-toggle' => 'premium-modal',
				'data-target' => '#premium-modal-' . $this->get_id(),
			)
		);

		if ( 'button' === $trigger ) {

			$icon_type = $settings['icon_type'];

			if ( 'icon' === $icon_type ) {

				if ( ! empty( $settings['premium_modal_box_button_icon_selection'] ) ) {
					$this->add_render_attribute(
						'icon',
						array(
							'class'       => $settings['premium_modal_box_button_icon_selection'],
							'aria-hidden' => 'true',
						)
					);
				}

				$migrated = isset( $settings['__fa4_migrated']['premium_modal_box_button_icon_selection_updated'] );
				$is_new   = empty( $settings['premium_modal_box_button_icon_selection'] ) && Icons_Manager::is_migration_allowed();

			}

			if ( 'yes' === $settings['draw_svg'] ) {

				$this->add_render_attribute(
					'modal',
					'class',
					array(
						'elementor-invisible',
						'premium-drawer-hover',
					)
				);

				if ( 'icon' === $icon_type ) {

					$this->add_render_attribute( 'icon', 'class', $settings['premium_modal_box_button_icon_selection_updated']['value'] );

				}

				$this->add_render_attribute(
					'icon',
					array(
						'class'            => 'premium-svg-drawer',
						'data-svg-reverse' => $settings['svg_reverse'],
						'data-svg-loop'    => $settings['svg_loop'],
						'data-svg-sync'    => $settings['svg_sync'],
						'data-svg-hover'   => $settings['svg_hover'],
						'data-svg-fill'    => $settings['svg_color'],
						'data-svg-frames'  => $settings['frames'],
						'data-svg-yoyo'    => $settings['svg_yoyo'],
						'data-svg-point'   => $settings['svg_reverse'] ? $settings['end_point']['size'] : $settings['start_point']['size'],
					)
				);

			} else {
				$this->add_render_attribute( 'icon', 'class', 'premium-svg-nodraw' );
			}

			$this->add_render_attribute(
				'trigger',
				array(
					'type'  => 'button',
					'class' => array(
						'premium-modal-trigger-btn',
						'premium-btn-' . $settings['premium_modal_box_button_size'],
					),
				)
			);

		} elseif ( 'image' === $trigger ) {

			$alt = Control_Media::get_image_alt( $settings['premium_modal_box_image_src'] );

			$this->add_render_attribute(
				'trigger',
				array(
					'class' => 'premium-modal-trigger-img',
					'src'   => $settings['premium_modal_box_image_src']['url'],
					'alt'   => $alt,
				)
			);

		} elseif ( 'text' === $trigger ) {
			$this->add_render_attribute( 'trigger', 'class', 'premium-modal-trigger-text' );
		} elseif ( 'animation' === $trigger ) {

			$this->add_render_attribute(
				'trigger',
				array(
					'class'               => array(
						'premium-modal-trigger-animation',
						'premium-lottie-animation',
					),
					'data-lottie-url'     => $settings['lottie_url'],
					'data-lottie-loop'    => $settings['lottie_loop'],
					'data-lottie-reverse' => $settings['lottie_reverse'],
					'data-lottie-hover'   => $settings['lottie_hover'],
				)
			);

		}

		if ( 'template' === $settings['premium_modal_box_content_type'] ) {
			$template = empty( $settings['premium_modal_box_content_temp'] ) ? $settings['live_temp_content'] : $settings['premium_modal_box_content_temp'];
		}

		if ( 'yes' === $settings['premium_modal_box_header_switcher'] ) {

			$header_icon = $settings['premium_modal_box_icon_selection'];

			$header_migrated = false;
			$header_new      = false;

			if ( 'fonticon' === $header_icon ) {

				if ( ! empty( $settings['premium_modal_box_font_icon'] ) ) {
					$this->add_render_attribute(
						'title_icon',
						array(
							'class'       => $settings['premium_modal_box_font_icon'],
							'aria-hidden' => 'true',
						)
					);
				}

				$header_migrated = isset( $settings['__fa4_migrated']['premium_modal_box_font_icon_updated'] );
				$header_new      = empty( $settings['premium_modal_box_font_icon'] ) && Icons_Manager::is_migration_allowed();
			} elseif ( 'image' === $header_icon ) {

				$alt = Control_Media::get_image_alt( $settings['premium_modal_box_image_icon'] );

				$this->add_render_attribute(
					'title_icon',
					array(
						'src' => $settings['premium_modal_box_image_icon']['url'],
						'alt' => $alt,
					)
				);

			}
		}

		$modal_settings = array(
			'trigger' => $trigger,
		);

		if ( 'pageload' === $trigger ) {
			$modal_settings['delay'] = $settings['premium_modal_box_popup_delay'];
		}

		$this->add_render_attribute(
			'modal',
			array(
				'class'         => 'premium-modal-box-container',
				'data-settings' => wp_json_encode( $modal_settings ),
			)
		);

		$animation_class = $settings['premium_modal_box_animation'];
		if ( '' !== $settings['premium_modal_box_animation_duration'] ) {
			$animation_dur = 'animated-' . $settings['premium_modal_box_animation_duration'];
		} else {
			$animation_dur = 'animated-';
		}

		$this->add_render_attribute(
			'dialog',
			array(
				'class'                => 'premium-modal-box-modal-dialog',
				'data-delay-animation' => $settings['premium_modal_box_animation_delay'],
				'data-modal-animation' => array(
					$animation_class,
					$animation_dur,
				),
			)
		);

		?>

		<div <?php echo wp_kses_post( $this->get_render_attribute_string( 'modal' ) ); ?>>
			<div class="premium-modal-trigger-container">
				<?php
				if ( 'button' === $trigger ) :
					?>
					<button <?php echo wp_kses_post( $this->get_render_attribute_string( 'trigger' ) ); ?>>
						<?php
						if ( 'yes' === $settings['premium_modal_box_icon_switcher'] && 'before' === $settings['premium_modal_box_icon_position'] ) :
							if ( 'icon' === $icon_type ) :
								if ( ( $is_new || $migrated ) && 'yes' !== $settings['draw_svg'] ) :
									Icons_Manager::render_icon(
										$settings['premium_modal_box_button_icon_selection_updated'],
										array(
											'class'       => 'premium-svg-nodraw',
											'aria-hidden' => 'true',
										)
									);
								else :
									?>
										<i <?php echo wp_kses_post( $this->get_render_attribute_string( 'icon' ) ); ?>></i>
									<?php
								endif;
							else :
								?>
								<div <?php echo wp_kses_post( $this->get_render_attribute_string( 'icon' ) ); ?>>
									<?php $this->print_unescaped_setting( 'custom_svg' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
								</div>
								<?php
							endif;
						endif;
						?>
						<span><?php echo wp_kses_post( $settings['premium_modal_box_button_text'] ); ?></span>
						<?php
						if ( 'yes' === $settings['premium_modal_box_icon_switcher'] && 'after' === $settings['premium_modal_box_icon_position'] ) :
							if ( 'icon' === $icon_type ) :
								if ( ( $is_new || $migrated ) && 'yes' !== $settings['draw_svg'] ) :
									Icons_Manager::render_icon(
										$settings['premium_modal_box_button_icon_selection_updated'],
										array(
											'class'       => 'premium-svg-nodraw',
											'aria-hidden' => 'true',
										)
									);
								else :
									?>
										<i <?php echo wp_kses_post( $this->get_render_attribute_string( 'icon' ) ); ?>></i>
									<?php
								endif;
							else :
								?>
								<div <?php echo wp_kses_post( $this->get_render_attribute_string( 'icon' ) ); ?>>
									<?php $this->print_unescaped_setting( 'custom_svg' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
								</div>
								<?php
							endif;
						endif;
						?>
					</button>
				<?php elseif ( 'image' === $trigger ) : ?>
					<img <?php echo wp_kses_post( $this->get_render_attribute_string( 'trigger' ) ); ?>>
				<?php elseif ( 'text' === $trigger ) : ?>
					<span <?php echo wp_kses_post( $this->get_render_attribute_string( 'trigger' ) ); ?>>
						<div <?php echo wp_kses_post( $this->get_render_attribute_string( 'premium_modal_box_selector_text' ) ); ?>>
							<?php echo wp_kses_post( $settings['premium_modal_box_selector_text'] ); ?>
						</div>
					</span>
				<?php elseif ( 'animation' === $trigger ) : ?>
					<div <?php echo wp_kses_post( $this->get_render_attribute_string( 'trigger' ) ); ?>></div>
				<?php endif; ?>
			</div>

			<div id="premium-modal-<?php echo esc_attr( $this->get_id() ); ?>" class="premium-modal-box-modal" role="dialog" style="display: none">
				<div <?php echo wp_kses_post( $this->get_render_attribute_string( 'dialog' ) ); ?>>
					<?php if ( 'yes' === $settings['premium_modal_box_header_switcher'] ) : ?>
						<div class="premium-modal-box-modal-header">
							<?php if ( ! empty( $settings['premium_modal_box_title'] ) ) : ?>
								<h3 class="premium-modal-box-modal-title">
									<?php
										$this->render_header_icon( $header_new, $header_migrated );
										echo wp_kses_post( $settings['premium_modal_box_title'] );
									?>
								</h3>
							<?php endif; ?>
							<?php if ( 'yes' === $settings['premium_modal_box_upper_close'] ) : ?>
								<div class="premium-modal-box-close-button-container">
									<button type="button" class="premium-modal-box-modal-close" data-dismiss="premium-modal">&times;</button>
								</div>
							<?php endif; ?>
						</div>
					<?php endif; ?>
					<div class="premium-modal-box-modal-body">
						<?php
						if ( 'editor' === $settings['premium_modal_box_content_type'] ) :
							echo $this->parse_text_editor( $settings['premium_modal_box_content'] ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
						else :
							echo $this->getTemplateInstance()->get_template_content( $template ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
						endif;
						?>
					</div>
					<?php if ( 'yes' === $settings['premium_modal_box_lower_close'] ) : ?>
						<div class="premium-modal-box-modal-footer">
							<button type="button" class="premium-modal-box-modal-lower-close" data-dismiss="premium-modal">
								<?php echo wp_kses_post( $settings['premium_modal_close_text'] ); ?>
							</button>
						</div>
					<?php endif; ?>
				</div>
			</div>
		</div>

		<?php
	}
}
