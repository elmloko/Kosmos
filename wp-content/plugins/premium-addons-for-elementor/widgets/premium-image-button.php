<?php
/**
 * Premium Image Button.
 */

namespace PremiumAddons\Widgets;

// Elementor Classes.
use Elementor\Icons_Manager;
use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Core\Kits\Documents\Tabs\Global_Colors;
use Elementor\Core\Kits\Documents\Tabs\Global_Typography;
use Elementor\Group_Control_Border;
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
 * Class Premium_Image_Button
 */
class Premium_Image_Button extends Widget_Base {

	/**
	 * Check Icon Draw Option.
	 *
	 * @since 4.9.26
	 * @access public
	 */
	public function check_icon_draw() {
		$is_enabled = Admin_Helper::check_svg_draw( 'premium-image-button' );
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
		return 'premium-addon-image-button';
	}

	/**
	 * Retrieve Widget Title.
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function get_title() {
		return __( 'Image Button', 'premium-addons-for-elementor' );
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
				'lottie-js',
				'premium-addons',
			)
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
		return 'pa-image-button';
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
		return array( 'pa', 'premium', 'cta', 'call', 'link', 'btn' );
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
	 * Register Image  Button controls.
	 *
	 * @since 1.0.0
	 * @access protected
	 */
	protected function register_controls() { // phpcs:ignore PSR2.Methods.MethodDeclaration.Underscore

		$draw_icon = $this->check_icon_draw();

		$this->start_controls_section(
			'premium_image_button_general_section',
			array(
				'label' => __( 'Button', 'premium-addons-for-elementor' ),
			)
		);

		$this->add_control(
			'premium_image_button_text',
			array(
				'label'       => __( 'Text', 'premium-addons-for-elementor' ),
				'type'        => Controls_Manager::TEXT,
				'dynamic'     => array( 'active' => true ),
				'default'     => __( 'Premium Addons', 'premium-addons-for-elementor' ),
				'label_block' => true,
			)
		);

		$this->add_control(
			'premium_image_button_link_selection',
			array(
				'label'       => __( 'Link Type', 'premium-addons-for-elementor' ),
				'type'        => Controls_Manager::SELECT,
				'options'     => array(
					'url'  => __( 'URL', 'premium-addons-for-elementor' ),
					'link' => __( 'Existing Page', 'premium-addons-for-elementor' ),
				),
				'default'     => 'url',
				'label_block' => true,
			)
		);

		$this->add_control(
			'premium_image_button_link',
			array(
				'label'       => __( 'Link', 'premium-addons-for-elementor' ),
				'type'        => Controls_Manager::URL,
				'dynamic'     => array( 'active' => true ),
				'default'     => array(
					'url' => '#',
				),
				'placeholder' => 'https://premiumaddons.com/',
				'label_block' => true,
				'condition'   => array(
					'premium_image_button_link_selection' => 'url',
				),
			)
		);

		$this->add_control(
			'premium_image_button_existing_link',
			array(
				'label'       => __( 'Existing Page', 'premium-addons-for-elementor' ),
				'type'        => Controls_Manager::SELECT2,
				'options'     => $this->getTemplateInstance()->get_all_posts(),
				'condition'   => array(
					'premium_image_button_link_selection' => 'link',
				),
				'multiple'    => false,
				'label_block' => true,
			)
		);

		$this->add_control(
			'premium_image_button_hover_effect',
			array(
				'label'       => __( 'Hover Effect', 'premium-addons-for-elementor' ),
				'type'        => Controls_Manager::SELECT,
				'default'     => 'none',
				'options'     => array(
					'none'   => __( 'None', 'premium-addons-for-elementor' ),
					'style1' => __( 'Background Slide', 'premium-addons-for-elementor' ),
					'style3' => __( 'Diagonal Slide', 'premium-addons-for-elementor' ),
					'style4' => __( 'Icon Slide', 'premium-addons-for-elementor' ),
					'style5' => __( 'Overlap', 'premium-addons-for-elementor' ),
					'style6' => __( 'Grow', 'premium-addons-for-elementor' ),
				),
				'separator'   => 'before',
				'label_block' => true,
			)
		);

		$this->add_control(
			'premium_image_button_style1_dir',
			array(
				'label'       => __( 'Slide Direction', 'premium-addons-for-elementor' ),
				'type'        => Controls_Manager::SELECT,
				'default'     => 'bottom',
				'options'     => array(
					'bottom' => __( 'Top to Bottom', 'premium-addons-for-elementor' ),
					'top'    => __( 'Bottom to Top', 'premium-addons-for-elementor' ),
					'left'   => __( 'Right to Left', 'premium-addons-for-elementor' ),
					'right'  => __( 'Left to Right', 'premium-addons-for-elementor' ),
				),
				'label_block' => true,
				'condition'   => array(
					'premium_image_button_hover_effect' => 'style1',
				),
			)
		);

		$this->add_control(
			'premium_image_button_style3_dir',
			array(
				'label'       => __( 'Slide Direction', 'premium-addons-for-elementor' ),
				'type'        => Controls_Manager::SELECT,
				'default'     => 'bottom',
				'options'     => array(
					'top'    => __( 'Bottom Left to Top Right', 'premium-addons-for-elementor' ),
					'bottom' => __( 'Top Right to Bottom Left', 'premium-addons-for-elementor' ),
					'left'   => __( 'Top Left to Bottom Right', 'premium-addons-for-elementor' ),
					'right'  => __( 'Bottom Right to Top Left', 'premium-addons-for-elementor' ),
				),
				'condition'   => array(
					'premium_image_button_hover_effect' => 'style3',
				),
				'label_block' => true,
			)
		);

		$this->add_control(
			'premium_image_button_style4_dir',
			array(
				'label'       => __( 'Slide Direction', 'premium-addons-for-elementor' ),
				'type'        => Controls_Manager::SELECT,
				'default'     => 'bottom',
				'options'     => array(
					'top'    => __( 'Bottom to Top', 'premium-addons-for-elementor' ),
					'bottom' => __( 'Top to Bottom', 'premium-addons-for-elementor' ),
					'left'   => __( 'Left to Right', 'premium-addons-for-elementor' ),
					'right'  => __( 'Right to Left', 'premium-addons-for-elementor' ),
				),
				'label_block' => true,
				'condition'   => array(
					'premium_image_button_hover_effect' => 'style4',
				),
			)
		);

		$this->add_control(
			'premium_image_button_style5_dir',
			array(
				'label'       => __( 'Overlap Direction', 'premium-addons-for-elementor' ),
				'type'        => Controls_Manager::SELECT,
				'default'     => 'horizontal',
				'options'     => array(
					'horizontal' => __( 'Horizontal', 'premium-addons-for-elementor' ),
					'vertical'   => __( 'Vertical', 'premium-addons-for-elementor' ),
				),
				'label_block' => true,
				'condition'   => array(
					'premium_image_button_hover_effect' => 'style5',
				),
			)
		);

		$this->add_responsive_control(
			'grow_size',
			array(
				'label'     => __( 'Grow Layer Size', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::SLIDER,
				'range'     => array(
					'px' => array(
						'min' => 0,
						'max' => 500,
					),
				),
				'condition' => array(
					'premium_image_button_hover_effect' => 'style6',
					'mouse_detect!'                     => 'yes',
				),
				'selectors' => array(
					'{{WRAPPER}} .premium-image-button.premium-button-style6:before' => 'width: {{SIZE}}px; height: {{SIZE}}px',
				),
			)
		);

		$this->add_responsive_control(
			'grow_speed',
			array(
				'label'     => __( 'Grow Animation Speed (Sec)', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::SLIDER,
				'range'     => array(
					'px' => array(
						'min'  => 0,
						'max'  => 3,
						'step' => 0.1,
					),
				),
				'condition' => array(
					'premium_image_button_hover_effect' => 'style6',
					'mouse_detect!'                     => 'yes',
				),
				'selectors' => array(
					'{{WRAPPER}} .premium-image-button.premium-button-style6:before' => 'transition-duration: {{SIZE}}s',
				),
			)
		);

		$this->add_control(
			'mouse_detect',
			array(
				'label'        => __( 'Detect Mouse Position', 'premium-addons-for-elementor' ),
				'type'         => Controls_Manager::SWITCHER,
				'prefix_class' => 'premium-mouse-detect-',
				'render_type'  => 'template',
				'separator'    => 'after',
				'condition'    => array(
					'premium_image_button_hover_effect' => 'style6',
				),
			)
		);

		$this->add_control(
			'premium_image_button_icon_switcher',
			array(
				'label'       => __( 'Icon', 'premium-addons-for-elementor' ),
				'type'        => Controls_Manager::SWITCHER,
				'description' => __( 'Enable or disable button icon', 'premium-addons-for-elementor' ),
				'condition'   => array(
					'premium_image_button_hover_effect!' => 'style4',
				),
			)
		);

		$common_conditions = array(
			'premium_image_button_icon_switcher' => 'yes',
			'premium_image_button_hover_effect!' => 'style4',
		);

		$this->add_control(
			'icon_type',
			array(
				'label'       => __( 'Icon Type', 'premium-addons-for-elementor' ),
				'type'        => Controls_Manager::SELECT,
				'options'     => array(
					'icon'      => __( 'Icon', 'premium-addons-for-elementor' ),
					'animation' => __( 'Lottie Animation', 'premium-addons-for-elementor' ),
					'svg'       => __( 'SVG Code', 'premium-addons-for-elementor' ),
				),
				'default'     => 'icon',
				'label_block' => true,
				'condition'   => $common_conditions,
			)
		);

		$this->add_control(
			'premium_image_button_icon_selection_updated',
			array(
				'label'            => __( 'Icon', 'premium-addons-for-elementor' ),
				'type'             => Controls_Manager::ICONS,
				'fa4compatibility' => 'premium_image_button_icon_selection',
				'default'          => array(
					'value'   => 'fas fa-star',
					'library' => 'fa-solid',
				),
				'condition'        => array_merge(
					$common_conditions,
					array(
						'icon_type' => 'icon',
					)
				),
				'label_block'      => true,
			)
		);

		$this->add_control(
			'custom_svg',
			array(
				'label'       => __( 'SVG Code', 'premium-addons-for-elementor' ),
				'type'        => Controls_Manager::TEXTAREA,
				'description' => 'You can use these sites to create SVGs: <a href="https://danmarshall.github.io/google-font-to-svg-path/" target="_blank">Google Fonts</a> and <a href="https://boxy-svg.com/" target="_blank">Boxy SVG</a>',
				'condition'   => array_merge(
					$common_conditions,
					array(
						'icon_type' => 'svg',
					)
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
				'condition'   => array_merge(
					$common_conditions,
					array(
						'icon_type' => 'animation',
					)
				),
			)
		);

		$animation_conds = array(
			'terms' => array(
				array(
					'name'  => 'premium_image_button_icon_switcher',
					'value' => 'yes',
				),
				array(
					'name'     => 'premium_image_button_hover_effect',
					'operator' => '!==',
					'value'    => 'style4',
				),
				array(
					'relation' => 'or',
					'terms'    => array(
						array(
							'name'  => 'icon_type',
							'value' => 'animation',
						),
						array(
							'terms' => array(
								array(
									'relation' => 'or',
									'terms'    => array(
										array(
											'name'  => 'icon_type',
											'value' => 'icon',
										),
										array(
											'name'  => 'icon_type',
											'value' => 'svg',
										),
									),
								),
								array(
									'name'  => 'draw_svg',
									'value' => 'yes',
								),
							),
						),
					),
				),
			),
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
						'premium_image_button_icon_selection_updated[library]!' => 'svg',
					)
				),
			)
		);

		if ( $draw_icon ) {

			$this->add_control(
				'stroke_width',
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
						'{{WRAPPER}} .premium-image-button-text-icon-wrapper svg *' => 'stroke-width: {{SIZE}}',
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
		} else {

			Helper_Functions::get_draw_svg_notice(
				$this,
				'button',
				array_merge(
					$common_conditions,
					array(
						'icon_type' => array( 'icon', 'svg' ),
						'premium_image_button_icon_selection_updated[library]!' => 'svg',
					)
				)
			);

		}

		$this->add_control(
			'lottie_loop',
			array(
				'label'        => __( 'Loop', 'premium-addons-for-elementor' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'true',
				'default'      => 'true',
				'conditions'   => $animation_conds,
			)
		);

		$this->add_control(
			'lottie_reverse',
			array(
				'label'        => __( 'Reverse', 'premium-addons-for-elementor' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'true',
				'conditions'   => $animation_conds,
			)
		);

		if ( $draw_icon ) {
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
							'icon_type'       => array( 'icon', 'svg' ),
							'draw_svg'        => 'yes',
							'lottie_reverse!' => 'true',
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
							'icon_type'      => array( 'icon', 'svg' ),
							'draw_svg'       => 'yes',
							'lottie_reverse' => 'true',
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
							'icon_type'   => array( 'icon', 'svg' ),
							'draw_svg'    => 'yes',
							'lottie_loop' => 'true',
						)
					),
				)
			);
		}

		$this->add_control(
			'slide_icon_type',
			array(
				'label'       => __( 'Icon Type', 'premium-addons-for-elementor' ),
				'type'        => Controls_Manager::SELECT,
				'options'     => array(
					'icon'      => __( 'Icon', 'premium-addons-for-elementor' ),
					'animation' => __( 'Lottie Animation', 'premium-addons-for-elementor' ),
				),
				'default'     => 'icon',
				'label_block' => true,
				'condition'   => array(
					'premium_image_button_hover_effect' => 'style4',
				),
			)
		);

		$this->add_control(
			'premium_image_button_style4_icon_selection_updated',
			array(
				'label'            => __( 'Icon', 'premium-addons-for-elementor' ),
				'type'             => Controls_Manager::ICONS,
				'fa4compatibility' => 'premium_image_button_style4_icon_selection',
				'default'          => array(
					'value'   => 'fas fa-star',
					'library' => 'fa-solid',
				),
				'label_block'      => true,
				'condition'        => array(
					'slide_icon_type'                   => 'icon',
					'premium_image_button_hover_effect' => 'style4',
				),
			)
		);

		$this->add_control(
			'slide_lottie_url',
			array(
				'label'       => __( 'Animation JSON URL', 'premium-addons-for-elementor' ),
				'type'        => Controls_Manager::TEXT,
				'dynamic'     => array( 'active' => true ),
				'description' => 'Get JSON code URL from <a href="https://lottiefiles.com/" target="_blank">here</a>',
				'label_block' => true,
				'condition'   => array(
					'slide_icon_type'                   => 'animation',
					'premium_image_button_hover_effect' => 'style4',
				),
			)
		);

		$this->add_control(
			'slide_lottie_loop',
			array(
				'label'        => __( 'Loop', 'premium-addons-for-elementor' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'true',
				'default'      => 'true',
				'condition'    => array(
					'slide_icon_type'                   => 'animation',
					'premium_image_button_hover_effect' => 'style4',
				),
			)
		);

		$this->add_control(
			'slide_lottie_reverse',
			array(
				'label'        => __( 'Reverse', 'premium-addons-for-elementor' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'true',
				'condition'    => array(
					'slide_icon_type'                   => 'animation',
					'premium_image_button_hover_effect' => 'style4',
				),
			)
		);

		$this->add_control(
			'premium_image_button_icon_position',
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
					'premium_image_button_icon_switcher' => 'yes',
					'premium_image_button_hover_effect!' => 'style4',
				),
			)
		);

		$this->add_responsive_control(
			'premium_image_button_icon_before_size',
			array(
				'label'     => __( 'Icon Size', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::SLIDER,
				'range'     => array(
					'px' => array(
						'min' => 0,
						'max' => 200,
					),
				),
				'condition' => array(
					'premium_image_button_icon_switcher' => 'yes',
					'premium_image_button_hover_effect!' => 'style4',
				),
				'selectors' => array(
					'{{WRAPPER}} .premium-image-button-text-icon-wrapper i' => 'font-size: {{SIZE}}px',
					'{{WRAPPER}} .premium-image-button-text-icon-wrapper svg' => 'width: {{SIZE}}px !important; height: {{SIZE}}px !important',
				),
			)
		);

		$this->add_responsive_control(
			'premium_image_button_icon_style4_size',
			array(
				'label'     => __( 'Icon Size', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::SLIDER,
				'condition' => array(
					'premium_image_button_hover_effect' => 'style4',
				),
				'selectors' => array(
					'{{WRAPPER}} .premium-image-button-style4-icon-wrapper i' => 'font-size: {{SIZE}}px',
					'{{WRAPPER}} .premium-image-button-style4-icon-wrapper svg' => 'width: {{SIZE}}px !important; height: {{SIZE}}px !important',
				),
			)
		);

		$icon_spacing = is_rtl() ? 'left' : 'right';

		$icon_spacing_after = is_rtl() ? 'right' : 'left';

		$this->add_responsive_control(
			'premium_image_button_icon_before_spacing',
			array(
				'label'     => __( 'Icon Spacing', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::SLIDER,
				'default'   => array(
					'size' => 15,
				),
				'selectors' => array(
					'{{WRAPPER}} .premium-image-button-text-icon-wrapper i, {{WRAPPER}} .premium-image-button-text-icon-wrapper svg' => 'margin-' . $icon_spacing . ': {{SIZE}}px',
				),
				'separator' => 'after',
				'condition' => array(
					'premium_image_button_icon_switcher' => 'yes',
					'premium_image_button_icon_position' => 'before',
					'premium_image_button_hover_effect!' => 'style4',
				),
			)
		);

		$this->add_responsive_control(
			'premium_image_button_icon_after_spacing',
			array(
				'label'     => __( 'Icon Spacing', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::SLIDER,
				'default'   => array(
					'size' => 15,
				),
				'selectors' => array(
					'{{WRAPPER}} .premium-image-button-text-icon-wrapper i, {{WRAPPER}} .premium-image-button-text-icon-wrapper svg' => 'margin-' . $icon_spacing_after . ': {{SIZE}}px',
				),
				'separator' => 'after',
				'condition' => array(
					'premium_image_button_icon_switcher' => 'yes',
					'premium_image_button_icon_position' => 'after',
					'premium_image_button_hover_effect!' => 'style4',
				),
			)
		);

		$this->add_control(
			'premium_image_button_size',
			array(
				'label'       => __( 'Size', 'premium-addons-for-elementor' ),
				'type'        => Controls_Manager::SELECT,
				'default'     => 'lg',
				'options'     => array(
					'sm'    => __( 'Small', 'premium-addons-for-elementor' ),
					'md'    => __( 'Medium', 'premium-addons-for-elementor' ),
					'lg'    => __( 'Large', 'premium-addons-for-elementor' ),
					'block' => __( 'Block', 'premium-addons-for-elementor' ),
				),
				'label_block' => true,
				'separator'   => 'before',
			)
		);

		$this->add_responsive_control(
			'premium_image_button_align',
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
				'selectors' => array(
					'{{WRAPPER}} .elementor-widget-container' => 'text-align: {{VALUE}}',
				),
				'toggle'    => false,
				'default'   => 'center',
				'condition' => array(
					'premium_image_button_size!' => 'block',
				),
			)
		);

		$this->add_control(
			'premium_image_button_event_switcher',
			array(
				'label'     => __( 'onclick Event', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::SWITCHER,
				'separator' => 'before',
			)
		);

		$this->add_control(
			'premium_image_button_event_function',
			array(
				'label'     => __( 'Example: myFunction();', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::CODE,
				'dynamic'   => array( 'active' => true ),
				'condition' => array(
					'premium_image_button_event_switcher' => 'yes',
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
			'https://premiumaddons.com/docs/image-button-widget-tutorial' => __( 'Getting started »', 'premium-addons-for-elementor' ),
			'https://premiumaddons.com/docs/how-can-i-open-an-elementor-popup-using-premium-button' => __( 'How to open an Elementor popup using Image Button widget »', 'premium-addons-for-elementor' ),
			'https://premiumaddons.com/docs/how-to-play-pause-a-soundtrack-using-premium-button-widget' => __( 'How to play/pause a soundtrack using Image Button widget »', 'premium-addons-for-elementor' ),
			'https://premiumaddons.com/docs/how-to-use-elementor-widgets-to-navigate-through-carousel-widget-slides/' => __( 'How To Use Image Button To Navigate Through Carousel Widget Slides »', 'premium-addons-for-elementor' ),
		);

		$doc_index = 1;
		foreach ( $docs as $url => $title ) {

			$doc_url = Helper_Functions::get_campaign_link( $url, 'editor-page', 'wp-editor', 'get-support' );

			$this->add_control(
				'doc_' . $doc_index,
				array(
					'type'            => Controls_Manager::RAW_HTML,
					'raw'             => sprintf( '<a href="%s" target="_blank">%s</a>', $doc_url, $title, 'premium-addons-for-elementor' ),
					'content_classes' => 'editor-pa-doc',
				)
			);

			$doc_index++;

		}

		$this->end_controls_section();

		$this->start_controls_section(
			'premium_image_button_style_section',
			array(
				'label' => __( 'Button', 'premium-addons-for-elementor' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_control(
			'button_width',
			array(
				'label'      => __( 'Width', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', '%', 'custom' ),
				'range'      => array(
					'px' => array(
						'min' => 1,
						'max' => 500,
					),
				),
				'selectors'  => array(
					'{{WRAPPER}} .premium-image-button' => 'width: {{SIZE}}{{UNIT}};',
				),
				'condition'  => array(
					'premium_image_button_size!' => 'block',
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
					'premium_image_button_icon_switcher' => 'yes',
					'icon_type'                          => array( 'icon', 'svg' ),
					'premium_image_button_hover_effect!' => 'style4',
					'draw_svg'                           => 'yes',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'premium_image_button_typo',
				'global'   => array(
					'default' => Global_Typography::TYPOGRAPHY_PRIMARY,
				),
				'selector' => '{{WRAPPER}} .premium-image-button',
			)
		);

		$this->start_controls_tabs( 'premium_image_button_style_tabs' );

		$this->start_controls_tab(
			'premium_image_button_style_normal',
			array(
				'label' => __( 'Normal', 'premium-addons-for-elementor' ),
			)
		);

		$this->add_control(
			'premium_image_button_text_color_normal',
			array(
				'label'     => __( 'Text Color', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'global'    => array(
					'default' => Global_Colors::COLOR_SECONDARY,
				),
				'selectors' => array(
					'{{WRAPPER}} .premium-image-button .premium-image-button-text-icon-wrapper span'   => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'premium_image_button_icon_color_normal',
			array(
				'label'     => __( 'Icon Color', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'global'    => array(
					'default' => Global_Colors::COLOR_SECONDARY,
				),
				'selectors' => array(
					'{{WRAPPER}} .premium-image-button-text-icon-wrapper i' => 'color: {{VALUE}}',
					'{{WRAPPER}} .premium-drawable-icon, {{WRAPPER}} svg:not([class*="premium-"])' => 'fill: {{VALUE}};',
				),
				'condition' => array(
					'premium_image_button_icon_switcher' => 'yes',
					'icon_type'                          => 'icon',
					'premium_image_button_hover_effect!' => 'style4',
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
						'premium_image_button_icon_switcher' => 'yes',
						'icon_type' => array( 'icon', 'svg' ),
						'premium_image_button_hover_effect!' => array( 'style3', 'style4' ),
					),
					'selectors' => array(
						'{{WRAPPER}} .premium-drawable-icon *, {{WRAPPER}} svg:not([class*="premium-"])' => 'stroke: {{VALUE}};',
					),
				)
			);
		}

		$this->add_group_control(
			Group_Control_Background::get_type(),
			array(
				'name'           => 'premium_image_button_background',
				'types'          => array( 'classic', 'gradient' ),
				'selector'       => '{{WRAPPER}} .premium-image-button',
				'fields_options' => array(
					'background' => array(
						'default' => 'classic',
					),
					'color'      => array(
						'global' => array(
							'default' => Global_Colors::COLOR_PRIMARY,
						),
					),
				),
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'     => 'premium_image_button_border_normal',
				'selector' => '{{WRAPPER}} .premium-image-button',
			)
		);

		$this->add_control(
			'premium_image_button_border_radius_normal',
			array(
				'label'      => __( 'Border Radius', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', '%', 'em' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-image-button' => 'border-radius: {{SIZE}}{{UNIT}};',
				),
				'condition'  => array(
					'button_adv_radius!' => 'yes',
				),
			)
		);

		$this->add_control(
			'button_adv_radius',
			array(
				'label'       => __( 'Advanced Border Radius', 'premium-addons-for-elementor' ),
				'type'        => Controls_Manager::SWITCHER,
				'description' => __( 'Apply custom radius values. Get the radius value from ', 'premium-addons-for-elementor' ) . '<a href="https://9elements.github.io/fancy-border-radius/" target="_blank">here</a>',
			)
		);

		$this->add_control(
			'button_adv_radius_value',
			array(
				'label'     => __( 'Border Radius', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::TEXT,
				'dynamic'   => array( 'active' => true ),
				'selectors' => array(
					'{{WRAPPER}} .premium-image-button' => 'border-radius: {{VALUE}};',
				),
				'condition' => array(
					'button_adv_radius' => 'yes',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Text_Shadow::get_type(),
			array(
				'label'     => __( 'Icon Shadow', 'premium-addons-for-elementor' ),
				'name'      => 'premium_image_button_icon_shadow_normal',
				'selector'  => '{{WRAPPER}} .premium-image-button-text-icon-wrapper i',
				'condition' => array(
					'premium_image_button_icon_switcher' => 'yes',
					'icon_type'                          => 'icon',
					'premium_image_button_hover_effect!' => 'style4',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Text_Shadow::get_type(),
			array(
				'label'    => __( 'Text Shadow', 'premium-addons-for-elementor' ),
				'name'     => 'premium_image_button_text_shadow_normal',
				'selector' => '{{WRAPPER}} .premium-image-button-text-icon-wrapper span',
			)
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'label'    => __( 'Button Shadow', 'premium-addons-for-elementor' ),
				'name'     => 'premium_image_button_box_shadow_normal',
				'selector' => '{{WRAPPER}} .premium-image-button',
			)
		);

		$this->add_responsive_control(
			'premium_image_button_margin_normal',
			array(
				'label'      => __( 'Margin', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-image-button' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'premium_image_button_padding_normal',
			array(
				'label'      => __( 'Padding', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-image-button, {{WRAPPER}} .premium-image-button-effect-container' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'premium_image_button_style_hover',
			array(
				'label' => __( 'Hover', 'premium-addons-for-elementor' ),
			)
		);

		$this->add_control(
			'grow_effect_notice',
			array(
				'raw'             => __( 'It\'s not recommened to set a hover image background with Grow effect.', 'premium-addons-for-elementor' ),
				'type'            => Controls_Manager::RAW_HTML,
				'content_classes' => 'elementor-panel-alert elementor-panel-alert-info',
				'condition'       => array(
					'premium_image_button_hover_effect' => 'style6',
				),
			)
		);

		$this->add_control(
			'premium_image_button_text_color_hover',
			array(
				'label'     => __( 'Text Color', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'global'    => array(
					'default' => Global_Colors::COLOR_SECONDARY,
				),
				'selectors' => array(
					'{{WRAPPER}} .premium-image-button:hover .premium-image-button-text-icon-wrapper span'   => 'color: {{VALUE}};',
				),
				'condition' => array(
					'premium_image_button_hover_effect!' => 'style4',
				),
			)
		);

		$this->add_control(
			'premium_image_button_icon_color_hover',
			array(
				'label'     => __( 'Icon Color', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'global'    => array(
					'default' => Global_Colors::COLOR_SECONDARY,
				),
				'selectors' => array(
					'{{WRAPPER}} .premium-image-button:hover .premium-image-button-text-icon-wrapper i'   => 'color: {{VALUE}}',
					'{{WRAPPER}} .premium-image-button:hover .premium-drawable-icon, {{WRAPPER}} .premium-image-button:hover svg:not([class*="premium-"])'   => 'fill: {{VALUE}};',
				),
				'condition' => array(
					'premium_image_button_icon_switcher' => 'yes',
					'icon_type'                          => 'icon',
					'premium_image_button_hover_effect!' => 'style4',
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
						'premium_image_button_icon_switcher' => 'yes',
						'icon_type' => array( 'icon', 'svg' ),
						'premium_image_button_hover_effect!' => 'style4',
					),
					'selectors' => array(
						'{{WRAPPER}} .premium-image-button:hover .premium-drawable-icon *, {{WRAPPER}} .premium-image-button:hover svg:not([class*="premium-"])' => 'stroke: {{VALUE}};',
					),
				)
			);
		}

		$this->add_control(
			'premium_image_button_style4_icon_color',
			array(
				'label'     => __( 'Icon Color', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'global'    => array(
					'default' => Global_Colors::COLOR_PRIMARY,
				),
				'selectors' => array(
					'{{WRAPPER}} .premium-image-button-style4-icon-wrapper'   => 'color: {{VALUE}}',
					'{{WRAPPER}} .premium-image-button-style4-icon-wrapper svg'   => 'fill: {{VALUE}}',
				),
				'condition' => array(
					'premium_image_button_hover_effect' => 'style4',
					'slide_icon_type'                   => 'icon',
				),
			)
		);

		$this->add_control(
			'premium_image_button_diagonal_overlay_color',
			array(
				'label'     => __( 'Overlay Color', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'global'    => array(
					'default' => Global_Colors::COLOR_PRIMARY,
				),
				'selectors' => array(
					'{{WRAPPER}} .premium-image-button-style3:before'   => 'background-color: {{VALUE}};',
				),
				'condition' => array(
					'premium_image_button_hover_effect' => 'style3',
				),
			)
		);

		$this->add_control(
			'premium_image_button_overlap_overlay_color',
			array(
				'label'     => __( 'Overlay Color', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'global'    => array(
					'default' => Global_Colors::COLOR_PRIMARY,
				),
				'selectors' => array(
					'{{WRAPPER}} .premium-image-button-overlap-effect-horizontal:before, {{WRAPPER}} .premium-image-button-overlap-effect-vertical:before'   => 'background-color: {{VALUE}};',
				),
				'condition' => array(
					'premium_image_button_hover_effect' => 'style5',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			array(
				'name'           => 'premium_image_button_background_hover',
				'types'          => array( 'classic', 'gradient' ),
				'selector'       => '{{WRAPPER}} .premium-image-button-none:hover, {{WRAPPER}} .premium-image-button-style4-icon-wrapper, {{WRAPPER}} .premium-image-button-style1:before, {{WRAPPER}} .premium-image-button-style3:hover, {{WRAPPER}} .premium-image-button-overlap-effect-horizontal:hover, {{WRAPPER}} .premium-image-button-overlap-effect-vertical:hover, {{WRAPPER}} .premium-button-style6-bg, {{WRAPPER}} .premium-button-style6:before',
				'fields_options' => array(
					'background' => array(
						'default' => 'classic',
					),
					'color'      => array(
						'global' => array(
							'default' => Global_Colors::COLOR_TEXT,
						),
					),
				),
			)
		);

		$this->add_control(
			'premium_image_button_overlay_color',
			array(
				'label'     => __( 'Overlay Color', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'global'    => array(
					'default' => Global_Colors::COLOR_PRIMARY,
				),
				'condition' => array(
					'premium_image_button_overlay_switcher' => 'yes',
				),
				'selectors' => array(
					'{{WRAPPER}} .premium-image-button-squares-effect:before, {{WRAPPER}} .premium-image-button-squares-effect:after,{{WRAPPER}} .premium-image-button-squares-square-container:before, {{WRAPPER}} .premium-image-button-squares-square-container:after' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'     => 'premium_image_button_border_hover',
				'selector' => '{{WRAPPER}} .premium-image-button:hover',
			)
		);

		$this->add_control(
			'premium_image_button_border_radius_hover',
			array(
				'label'      => __( 'Border Radius', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', '%', 'em' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-image-button:hover' => 'border-radius: {{SIZE}}{{UNIT}};',
				),
				'condition'  => array(
					'button_hover_adv_radius!' => 'yes',
				),
			)
		);

		$this->add_control(
			'button_hover_adv_radius',
			array(
				'label'       => __( 'Advanced Border Radius', 'premium-addons-for-elementor' ),
				'type'        => Controls_Manager::SWITCHER,
				'description' => __( 'Apply custom radius values. Get the radius value from ', 'premium-addons-for-elementor' ) . '<a href="https://9elements.github.io/fancy-border-radius/" target="_blank">here</a>',
			)
		);

		$this->add_control(
			'button_hover_adv_radius_value',
			array(
				'label'     => __( 'Border Radius', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::TEXT,
				'dynamic'   => array( 'active' => true ),
				'selectors' => array(
					'{{WRAPPER}} .premium-image-button:hover' => 'border-radius: {{VALUE}};',
				),
				'condition' => array(
					'button_hover_adv_radius' => 'yes',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Text_Shadow::get_type(),
			array(
				'label'     => __( 'Icon Shadow', 'premium-addons-for-elementor' ),
				'name'      => 'premium_image_button_icon_shadow_hover',
				'selector'  => '{{WRAPPER}} .premium-image-button:hover .premium-image-button-text-icon-wrapper i',
				'condition' => array(
					'premium_image_button_icon_switcher' => 'yes',
					'icon_type'                          => 'icon',
					'premium_image_button_hover_effect!' => 'style4',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Text_Shadow::get_type(),
			array(
				'label'     => __( 'Icon Shadow', 'premium-addons-for-elementor' ),
				'name'      => 'premium_image_button_style4_icon_shadow_hover',
				'selector'  => '{{WRAPPER}} .premium-image-button:hover .premium-image-button-style4-icon-wrapper i',
				'condition' => array(
					'premium_image_button_hover_effect' => 'style4',
					'slide_icon_type'                   => 'icon',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Text_Shadow::get_type(),
			array(
				'label'     => __( 'Text Shadow', 'premium-addons-for-elementor' ),
				'name'      => 'premium_image_button_text_shadow_hover',
				'selector'  => '{{WRAPPER}}  .premium-image-button:hover .premium-image-button-text-icon-wrapper span',
				'condition' => array(
					'premium_image_button_hover_effect!' => 'style4',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'label'    => __( 'Button Shadow', 'premium-addons-for-elementor' ),
				'name'     => 'premium_image_button_box_shadow_hover',
				'selector' => '{{WRAPPER}} .premium-image-button:hover',
			)
		);

		$this->add_responsive_control(
			'premium_image_button_margin_hover',
			array(
				'label'      => __( 'Margin', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-image-button:hover' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'premium_image_button_padding_hover',
			array(
				'label'      => __( 'Padding', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-image-button:hover' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();
	}

	/**
	 * Render Image Button widget output on the frontend.
	 *
	 * Written in PHP and used to generate the final HTML.
	 *
	 * @since 1.0.0
	 * @access protected
	 */
	protected function render() {

		$settings = $this->get_settings_for_display();

		$this->add_inline_editing_attributes( 'premium_image_button_text' );

		if ( 'url' === $settings['premium_image_button_link_selection'] ) {
			$image_link = $settings['premium_image_button_link'];
		} else {
			$image_link = get_permalink( $settings['premium_image_button_existing_link'] );
		}

		$button_text = $settings['premium_image_button_text'];

		$button_size = 'premium-btn-' . $settings['premium_image_button_size'];

		$image_event = $settings['premium_image_button_event_function'];

		if ( 'yes' === $settings['premium_image_button_icon_switcher'] ) {

			$icon_type = $settings['icon_type'];

			if ( 'icon' === $icon_type || 'svg' === $icon_type ) {

				$this->add_render_attribute( 'icon', 'class', 'premium-drawable-icon' );

				if ( 'icon' === $icon_type ) {

					if ( ! empty( $settings['premium_image_button_icon_selection'] ) ) {
						$this->add_render_attribute(
							'icon',
							array(
								'class'       => $settings['premium_image_button_icon_selection'],
								'aria-hidden' => 'true',
							)
						);

					}

					$migrated = isset( $settings['__fa4_migrated']['premium_image_button_icon_selection_updated'] );
					$is_new   = empty( $settings['premium_image_button_icon_selection'] ) && Icons_Manager::is_migration_allowed();

				}

				if ( 'yes' === $settings['draw_svg'] ) {

					$this->add_render_attribute(
						'button',
						'class',
						array(
							'elementor-invisible',
							'premium-drawer-hover',
						)
					);

					if ( 'icon' === $icon_type ) {

						$this->add_render_attribute( 'icon', 'class', $settings['premium_image_button_icon_selection_updated']['value'] );

					}

					$this->add_render_attribute(
						'icon',
						array(
							'class'            => 'premium-svg-drawer',
							'data-svg-reverse' => $settings['lottie_reverse'],
							'data-svg-loop'    => $settings['lottie_loop'],
							'data-svg-sync'    => $settings['svg_sync'],
							'data-svg-hover'   => $settings['svg_hover'],
							'data-svg-fill'    => $settings['svg_color'],
							'data-svg-frames'  => $settings['frames'],
							'data-svg-yoyo'    => $settings['svg_yoyo'],
							'data-svg-point'   => $settings['lottie_reverse'] ? $settings['end_point']['size'] : $settings['start_point']['size'],
						)
					);

				} else {
					$this->add_render_attribute( 'icon', 'class', 'premium-svg-nodraw' );
				}
			} else {
				$this->add_render_attribute(
					'lottie',
					array(
						'class'               => 'premium-lottie-animation',
						'data-lottie-url'     => $settings['lottie_url'],
						'data-lottie-loop'    => $settings['lottie_loop'],
						'data-lottie-reverse' => $settings['lottie_reverse'],
					)
				);
			}
		}

		if ( 'none' === $settings['premium_image_button_hover_effect'] ) {
			$style_dir = 'premium-image-button-none';
		} elseif ( 'style1' === $settings['premium_image_button_hover_effect'] ) {
			$style_dir = 'premium-image-button-style1-' . $settings['premium_image_button_style1_dir'];
		} elseif ( 'style3' === $settings['premium_image_button_hover_effect'] ) {
			$style_dir = 'premium-image-button-diagonal-' . $settings['premium_image_button_style3_dir'];
		} elseif ( 'style4' === $settings['premium_image_button_hover_effect'] ) {
			$style_dir = 'premium-image-button-style4-' . $settings['premium_image_button_style4_dir'];

			$slide_icon_type = $settings['slide_icon_type'];

			if ( 'icon' === $slide_icon_type ) {

				if ( ! empty( $settings['premium_image_button_style4_icon_selection'] ) ) {
					$this->add_render_attribute( 'slide_icon', 'class', $settings['premium_image_button_style4_icon_selection'] );
					$this->add_render_attribute( 'slide_icon', 'aria-hidden', 'true' );
				}

				$slide_migrated = isset( $settings['__fa4_migrated']['premium_image_button_style4_icon_selection_updated'] );
				$slide_is_new   = empty( $settings['premium_image_button_style4_icon_selection'] ) && Icons_Manager::is_migration_allowed();

			} else {

				$this->add_render_attribute(
					'slide_lottie',
					array(
						'class'               => 'premium-lottie-animation',
						'data-lottie-url'     => $settings['slide_lottie_url'],
						'data-lottie-loop'    => $settings['slide_lottie_loop'],
						'data-lottie-reverse' => $settings['slide_lottie_reverse'],
					)
				);

			}
		} elseif ( 'style5' === $settings['premium_image_button_hover_effect'] ) {
			$style_dir = 'premium-image-button-overlap-effect-' . $settings['premium_image_button_style5_dir'];
		} elseif ( 'style6' === $settings['premium_image_button_hover_effect'] ) {
			$style_dir    = 'premium-button-style6';
			$mouse_detect = $settings['mouse_detect'];
			$this->add_render_attribute( 'style6', 'class', 'premium-button-style6-bg' );
		}

		$this->add_render_attribute(
			'button',
			'class',
			array(
				'premium-image-button',
				'premium-image-button-' . $settings['premium_image_button_hover_effect'],
				$button_size,
				$style_dir,
			)
		);

		if ( ! empty( $image_link ) ) {

			if ( 'url' === $settings['premium_image_button_link_selection'] ) {
				$this->add_link_attributes( 'button', $image_link );
			} else {
				$this->add_render_attribute( 'button', 'href', $image_link );
			}
		}

		if ( 'yes' === $settings['premium_image_button_event_switcher'] && ! empty( $image_event ) ) {
			$this->add_render_attribute( 'button', 'onclick', $image_event );
		}

		?>

		<a <?php echo wp_kses_post( $this->get_render_attribute_string( 'button' ) ); ?>>
			<div class="premium-image-button-text-icon-wrapper">
			<?php if ( 'yes' === $settings['premium_image_button_icon_switcher'] ) : ?>
				<?php if ( 'style4' !== $settings['premium_image_button_hover_effect'] && 'before' === $settings['premium_image_button_icon_position'] ) : ?>
					<?php if ( 'icon' === $icon_type ) : ?>
						<?php
						if ( ( $is_new || $migrated ) && 'yes' !== $settings['draw_svg'] ) :
							Icons_Manager::render_icon(
								$settings['premium_image_button_icon_selection_updated'],
								array(
									'class'       => array( 'premium-svg-nodraw', 'premium-drawable-icon' ),
									'aria-hidden' => 'true',
								)
							);
						else :
							?>
							<i <?php echo wp_kses_post( $this->get_render_attribute_string( 'icon' ) ); ?>></i>
						<?php endif; ?>
					<?php elseif ( 'svg' === $icon_type ) : ?>
						<div <?php echo wp_kses_post( $this->get_render_attribute_string( 'icon' ) ); ?>>
							<?php $this->print_unescaped_setting( 'custom_svg' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
						</div>
					<?php else : ?>
						<div <?php echo wp_kses_post( $this->get_render_attribute_string( 'lottie' ) ); ?>></div>
					<?php endif; ?>
				<?php endif; ?>
			<?php endif; ?>

			<span <?php echo wp_kses_post( $this->get_render_attribute_string( 'premium_image_button_text' ) ); ?>>
				<?php echo wp_kses_post( $button_text ); ?>
			</span>
			<?php if ( 'yes' === $settings['premium_image_button_icon_switcher'] ) : ?>
				<?php if ( 'style4' !== $settings['premium_image_button_hover_effect'] && 'after' === $settings['premium_image_button_icon_position'] ) : ?>
					<?php if ( 'icon' === $icon_type ) : ?>
						<?php
						if ( ( $is_new || $migrated ) && 'yes' !== $settings['draw_svg'] ) :
							Icons_Manager::render_icon(
								$settings['premium_image_button_icon_selection_updated'],
								array(
									'class'       => array( 'premium-svg-nodraw', 'premium-drawable-icon' ),
									'aria-hidden' => 'true',
								)
							);
						else :
							?>
							<i <?php echo wp_kses_post( $this->get_render_attribute_string( 'icon' ) ); ?>></i>
						<?php endif; ?>
					<?php elseif ( 'svg' === $icon_type ) : ?>
						<div <?php echo wp_kses_post( $this->get_render_attribute_string( 'icon' ) ); ?>>
							<?php $this->print_unescaped_setting( 'custom_svg' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
						</div>
					<?php else : ?>
						<div <?php echo wp_kses_post( $this->get_render_attribute_string( 'lottie' ) ); ?>></div>
					<?php endif; ?>
				<?php endif; ?>
			<?php endif; ?>
		</div>
		<?php if ( 'style4' === $settings['premium_image_button_hover_effect'] ) : ?>
			<div class="premium-image-button-style4-icon-wrapper <?php echo esc_attr( $settings['premium_image_button_style4_dir'] ); ?>">
				<?php if ( 'icon' === $slide_icon_type ) : ?>
					<?php
					if ( $slide_is_new || $slide_migrated ) :
						Icons_Manager::render_icon( $settings['premium_image_button_style4_icon_selection_updated'], array( 'aria-hidden' => 'true' ) );
					else :
						?>
						<i <?php echo wp_kses_post( $this->get_render_attribute_string( 'slide_icon' ) ); ?>></i>
					<?php endif; ?>
				<?php else : ?>
					<div <?php echo wp_kses_post( $this->get_render_attribute_string( 'slide_lottie' ) ); ?>></div>
				<?php endif; ?>
			</div>
		<?php endif; ?>
		<?php if ( 'style6' === $settings['premium_image_button_hover_effect'] && 'yes' === $mouse_detect ) : ?>
			<span <?php echo wp_kses_post( $this->get_render_attribute_string( 'style6' ) ); ?>></span>
		<?php endif; ?>
		</a>

		<?php
	}

	/**
	 * Render Image Button widget output in the editor.
	 *
	 * Written as a Backbone JavaScript template and used to generate the live preview.
	 *
	 * @since 1.0.0
	 * @access protected
	 */
	protected function content_template() {
		?>
		<#
		view.addInlineEditingAttributes( 'premium_image_button_text' );
		var buttonText = settings.premium_image_button_text,
			buttonUrl,
			styleDir,
			slideIcon,
			mouseDetect,
			buttonSize = 'premium-btn-' + settings.premium_image_button_size,
			buttonEvent = settings.premium_image_button_event_function,
			buttonIcon = settings.premium_image_button_icon_selection,
			hoverEffect = settings.premium_image_button_hover_effect,
			changeToScope = '';

		if( 'url' === settings.premium_image_button_link_selection ) {
			buttonUrl = settings.premium_image_button_link.url;
		} else {
			buttonUrl = settings.premium_image_button_existing_link;
		}

		if ( 'none' === hoverEffect ) {
			styleDir = 'premium-button-none';
		} else if( 'style1' === hoverEffect ) {
			styleDir = 'premium-image-button-style1-' + settings.premium_image_button_style1_dir;
		} else if ( 'style3' === hoverEffect ) {
			styleDir = 'premium-image-button-diagonal-' + settings.premium_image_button_style3_dir;
		} else if ( 'style4' === hoverEffect ) {
			styleDir = 'premium-image-button-style4-' + settings.premium_image_button_style4_dir;

			var slideIconType = settings.slide_icon_type;

			if( 'icon' === slideIconType ) {
				slideIcon = settings.premium_image_button_style4_icon_selection;
				var slideIconHTML = elementor.helpers.renderIcon( view, settings.premium_image_button_style4_icon_selection_updated, { 'aria-hidden': true }, 'i' , 'object' ),
					slideMigrated = elementor.helpers.isIconMigrated( settings, 'premium_image_button_style4_icon_selection_updated' );

			} else {

				view.addRenderAttribute( 'slide_lottie', {
					'class': 'premium-lottie-animation',
					'data-lottie-url': settings.slide_lottie_url,
					'data-lottie-loop': settings.slide_lottie_loop,
					'data-lottie-reverse': settings.slide_lottie_reverse
				});
			}
		} else if ( 'style5' === hoverEffect ){
			styleDir = 'premium-image-button-overlap-effect-' + settings.premium_image_button_style5_dir;
		} else if ( 'style6' === hoverEffect ) {
			styleDir = 'premium-button-style6';
			mouseDetect = settings.mouse_detect;
			view.addRenderAttribute( 'style6','class' , 'premium-button-style6-bg');
		}

		if( 'yes' === settings.premium_image_button_icon_switcher ) {

			var iconType = settings.icon_type;

			if( 'icon' === iconType || 'svg' === iconType ) {

				view.addRenderAttribute( 'icon', 'class', 'premium-drawable-icon' );

				if( 'icon' === iconType ) {

					var iconHTML = 'yes' !== settings.draw_svg ?  elementor.helpers.renderIcon( view, settings.premium_image_button_icon_selection_updated, { 'class': ['premium-svg-nodraw', 'premium-drawable-icon'], 'aria-hidden': true }, 'i' , 'object' ) : false,
						migrated = elementor.helpers.isIconMigrated( settings, 'premium_image_button_icon_selection_updated' );

				}

				if ( 'yes' === settings.draw_svg ) {

					changeToScope = 'premium-drawer-hover';

					if ( 'icon' === iconType ) {

						view.addRenderAttribute( 'icon', 'class', settings.premium_image_button_icon_selection_updated.value );

					}

					view.addRenderAttribute(
						'icon',
						{
							'class'            : 'premium-svg-drawer',
							'data-svg-reverse' : settings.lottie_reverse,
							'data-svg-loop'    : settings.lottie_loop,
							'data-svg-sync'    : settings.svg_sync,
							'data-svg-hover'   : settings.svg_hover,
							'data-svg-fill'    : settings.svg_color,
							'data-svg-frames'  : settings.frames,
							'data-svg-yoyo'    : settings.svg_yoyo,
							'data-svg-point'   : settings.lottie_reverse ? settings.end_point.size : settings.start_point.size,
						}
					);

				} else {
					view.addRenderAttribute( 'icon', 'class', 'premium-svg-nodraw' );
				}



			} else {

				view.addRenderAttribute( 'lottie', {
					'class': 'premium-lottie-animation',
					'data-lottie-url': settings.lottie_url,
					'data-lottie-loop': settings.lottie_loop,
					'data-lottie-reverse': settings.lottie_reverse
				});
			}

		}


		#>

		<a class="premium-image-button  {{ buttonSize }} {{ styleDir }} {{ changeToScope }} premium-image-button-{{ hoverEffect }}" href="{{ buttonUrl }}" onclick="{{ buttonEvent }}">
			<div class="premium-image-button-text-icon-wrapper">
				<# if ('yes' === settings.premium_image_button_icon_switcher ) {
					if( 'before' === settings.premium_image_button_icon_position &&  'style4' !== hoverEffect ) {
						if( 'icon' === iconType ) {
							if ( iconHTML && iconHTML.rendered && ( ! buttonIcon || migrated ) ) { #>
								{{{ iconHTML.value }}}
							<# } else { #>
								<i {{{ view.getRenderAttributeString('icon') }}}></i>
							<# }
						} else if( 'svg' === iconType ) { #>
							<div {{{ view.getRenderAttributeString('icon') }}}>
								{{{ settings.custom_svg }}}
							</div>
						<# } else { #>
							<div {{{ view.getRenderAttributeString('lottie') }}}></div>
						<# }
					}
				} #>
				<span {{{ view.getRenderAttributeString('premium_image_button_text') }}}>{{{ buttonText }}}</span>
				<# if ('yes' === settings.premium_image_button_icon_switcher ) {
					if( 'after' === settings.premium_image_button_icon_position && 'style4' !== hoverEffect ) {
						if( 'icon' === iconType ) {
							if ( iconHTML && iconHTML.rendered && ( ! buttonIcon || migrated ) ) { #>
								{{{ iconHTML.value }}}
							<# } else { #>
								<i {{{ view.getRenderAttributeString('icon') }}}></i>
							<# }
						} else if( 'svg' === iconType ) { #>
							<div {{{ view.getRenderAttributeString('icon') }}}>
								{{{ settings.custom_svg }}}
							</div>
						<# } else { #>
							<div {{{ view.getRenderAttributeString('lottie') }}}></div>
						<# }
					}
				} #>
			</div>
			<# if( 'style4' === hoverEffect ) { #>
				<div class="premium-image-button-style4-icon-wrapper {{ settings.premium_image_button_style4_dir }}">
					<# if ( 'icon' === slideIconType ) { #>
						<# if ( slideIconHTML && slideIconHTML.rendered && ( ! slideIcon || slideMigrated ) ) { #>
							{{{ slideIconHTML.value }}}
						<# } else { #>
							<i class="{{ slideIcon }}" aria-hidden="true"></i>
						<# } #>
					<# } else { #>
						<div {{{ view.getRenderAttributeString('slide_lottie') }}}></div>
					<# } #>
				</div>
			<# } #>
			<# if( 'style6' === hoverEffect && 'yes' === mouseDetect) { #>
				<span  {{{ view.getRenderAttributeString('style6') }}}></span>
			<# } #>
		</a>


		<?php
	}
}
