<?php
/**
 * Premium Title.
 */

namespace PremiumAddons\Widgets;

// Elementor Classes.
use Elementor\Widget_Base;
use Elementor\Utils;
use Elementor\Icons_Manager;
use Elementor\Controls_Manager;
use Elementor\Control_Media;
use Elementor\Core\Kits\Documents\Tabs\Global_Colors;
use Elementor\Core\Kits\Documents\Tabs\Global_Typography;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Text_Shadow;
use Elementor\Group_Control_Background;

// PremiumAddons Classes.
use PremiumAddons\Admin\Includes\Admin_Helper;
use PremiumAddons\Includes\Helper_Functions;
use PremiumAddons\Includes\Premium_Template_Tags;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // If this file is called directly, abort.
}

/**
 * Class Premium_Title
 */
class Premium_Title extends Widget_Base {

	/**
	 * Template Instance
	 *
	 * @var template_instance
	 */
	protected $template_instance;

	/**
	 * Check Icon Draw Option.
	 *
	 * @since 4.9.26
	 * @access public
	 */
	public function check_icon_draw() {
		$is_enabled = Admin_Helper::check_svg_draw( 'premium-title' );
		return $is_enabled;
	}

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
		return 'premium-addon-title';
	}

	/**
	 * Retrieve Widget Title.
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function get_title() {
		return __( 'Heading', 'premium-addons-for-elementor' );
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
		return 'pa-title';
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
				'premium-addons',
				'elementor-waypoints',
				'lottie-js',
			)
		);

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
	 * Retrieve Widget Dependent CSS.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return array CSS style handles.
	 */
	public function get_keywords() {
		return array( 'pa', 'premium', 'title', 'text', 'headline' );
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
	 * Register Title controls.
	 *
	 * @since  1.0.0
	 * @access protected
	 */
	protected function register_controls() { // phpcs:ignore PSR2.Methods.MethodDeclaration.Underscore

		$draw_icon = $this->check_icon_draw();

		$this->start_controls_section(
			'premium_title_content',
			array(
				'label' => __( 'Title', 'premium-addons-for-elementor' ),
			)
		);

		$this->add_control(
			'premium_title_text',
			array(
				'label'       => __( 'Title', 'premium-addons-for-elementor' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => __( 'Premium Title', 'premium-addons-for-elementor' ),
				'label_block' => true,
				'dynamic'     => array( 'active' => true ),
			)
		);

		$this->add_control(
			'premium_title_style',
			array(
				'label'       => __( 'Style', 'premium-addons-for-elementor' ),
				'type'        => Controls_Manager::SELECT,
				'default'     => 'style1',
				'options'     => array(
					'style1' => __( 'Style 1', 'premium-addons-for-elementor' ),
					'style2' => __( 'Style 2', 'premium-addons-for-elementor' ),
					'style3' => __( 'Style 3', 'premium-addons-for-elementor' ),
					'style4' => __( 'Style 4', 'premium-addons-for-elementor' ),
					'style5' => __( 'Style 5', 'premium-addons-for-elementor' ),
					'style6' => __( 'Style 6', 'premium-addons-for-elementor' ),
					'style7' => __( 'Style 7', 'premium-addons-for-elementor' ),
					'style8' => __( 'Style 8', 'premium-addons-for-elementor' ),
					'style9' => __( 'Style 9', 'premium-addons-for-elementor' ),
				),
				'label_block' => true,
			)
		);

		$this->add_control(
			'premium_title_icon_switcher',
			array(
				'label' => __( 'Icon', 'premium-addons-for-elementor' ),
				'type'  => Controls_Manager::SWITCHER,
			)
		);

		$this->add_control(
			'icon_type',
			array(
				'label'     => __( 'Icon Type', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::SELECT,
				'options'   => array(
					'icon'      => __( 'Icon', 'premium-addons-for-elementor' ),
					'image'     => __( 'Image', 'premium-addons-for-elementor' ),
					'animation' => __( 'Lottie Animation', 'premium-addons-for-elementor' ),
					'svg'       => __( 'SVG Code', 'premium-addons-for-elementor' ),
				),
				'default'   => 'icon',
				'condition' => array(
					'premium_title_icon_switcher' => 'yes',
				),
			)
		);

		$common_conditions = array(
			'premium_title_icon_switcher' => 'yes',
		);

		$this->add_control(
			'premium_title_icon_updated',
			array(
				'label'            => __( 'Font Awesome Icon', 'premium-addons-for-elementor' ),
				'type'             => Controls_Manager::ICONS,
				'fa4compatibility' => 'premium_title_icon',
				'default'          => array(
					'value'   => 'fas fa-bars',
					'library' => 'fa-solid',
				),
				'label_block'      => true,
				'condition'        => array_merge(
					$common_conditions,
					array(
						'icon_type' => 'icon',
					)
				),
			)
		);

		$this->add_control(
			'image_upload',
			array(
				'label'     => __( 'Upload Image', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::MEDIA,
				'default'   => array(
					'url' => Utils::get_placeholder_image_src(),
				),
				'condition' => array_merge(
					$common_conditions,
					array(
						'icon_type' => 'image',
					)
				),
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
					'name'  => 'premium_title_icon_switcher',
					'value' => 'yes',
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
						'premium_title_icon_updated[library]!' => 'svg',
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
						'{{WRAPPER}} .premium-title-header svg *' => 'stroke-width: {{SIZE}}',
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
				'heading',
				array_merge(
					$common_conditions,
					array(
						'icon_type' => array( 'icon', 'svg' ),
						'premium_title_icon_updated[library]!' => 'svg',
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

		$this->add_responsive_control(
			'icon_position',
			array(
				'label'        => __( 'Icon Position', 'premium-addons-for-elementor' ),
				'type'         => Controls_Manager::SELECT,
				'options'      => array(
					'row'            => __( 'Before', 'premium-addons-for-elementor' ),
					'row-reverse'    => __( 'After', 'premium-addons-for-elementor' ),
					'column'         => __( 'Top', 'premium-addons-for-elementor' ),
					'column-reverse' => __( 'Bottom', 'premium-addons-for-elementor' ),
				),
				'default'      => 'row',
				'toggle'       => false,
				'render_type'  => 'template',
				'prefix_class' => 'premium-title-icon-',
				'selectors'    => array(
					'{{WRAPPER}} .premium-title-header:not(.premium-title-style7), {{WRAPPER}} .premium-title-style7-inner' => 'flex-direction: {{VALUE}}',
				),
				'condition'    => array(
					'premium_title_icon_switcher' => 'yes',
				),
			)
		);

		$this->add_responsive_control(
			'top_icon_align',
			array(
				'label'     => __( 'Icon Alignment', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::CHOOSE,
				'options'   => array(
					'flex-start' => array(
						'title' => __( 'Left', 'premium-addons-for-elementor' ),
						'icon'  => 'eicon-text-align-left',
					),
					'center'     => array(
						'title' => __( 'Center', 'premium-addons-for-elementor' ),
						'icon'  => 'eicon-text-align-center',
					),
					'flex-end'   => array(
						'title' => __( 'Right', 'premium-addons-for-elementor' ),
						'icon'  => 'eicon-text-align-right',
					),
				),
				'default'   => 'center',
				'toggle'    => false,
				'selectors' => array(
					'{{WRAPPER}}.premium-title-icon-column .premium-title-header:not(.premium-title-style7)' => 'align-items: {{VALUE}}',
					'{{WRAPPER}}.premium-title-icon-column .premium-title-style7 .premium-title-icon'      => 'align-self: {{VALUE}}',
				),
				'condition' => array(
					'premium_title_icon_switcher' => 'yes',
					'icon_position'               => 'column',
					'premium_title_style!'        => array( 'style3', 'style4' ),
				),
			)
		);

		$this->add_control(
			'premium_title_tag',
			array(
				'label'     => __( 'HTML Tag', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'h2',
				'options'   => array(
					'h1'   => 'H1',
					'h2'   => 'H2',
					'h3'   => 'H3',
					'h4'   => 'H4',
					'h5'   => 'H5',
					'h6'   => 'H6',
					'div'  => 'div',
					'span' => 'span',
				),
				'separator' => 'before',
			)
		);

		$inline_flex = array( 'style1', 'style2', 'style5', 'style6', 'style7', 'style8', 'style9' );

		$this->add_responsive_control(
			'premium_title_align',
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
				'toggle'    => false,
				'selectors' => array(
					'{{WRAPPER}} .premium-title-container' => 'text-align: {{VALUE}};',
				),
				'condition' => array(
					'premium_title_style' => $inline_flex,
				),
			)
		);

		$this->add_control(
			'premium_title_align_flex',
			array(
				'label'        => __( 'Alignment', 'premium-addons-for-elementor' ),
				'type'         => Controls_Manager::CHOOSE,
				'prefix_class' => 'premium-title-',
				'options'      => array(
					'flex-start' => array(
						'title' => __( 'Left', 'premium-addons-for-elementor' ),
						'icon'  => 'eicon-text-align-left',
					),
					'center'     => array(
						'title' => __( 'Center', 'premium-addons-for-elementor' ),
						'icon'  => 'eicon-text-align-center',
					),
					'flex-end'   => array(
						'title' => __( 'Right', 'premium-addons-for-elementor' ),
						'icon'  => 'eicon-text-align-right',
					),
				),
				'default'      => 'flex-start',
				'selectors'    => array(
					'{{WRAPPER}}:not(.premium-title-icon-column) .premium-title-header' => 'justify-content: {{VALUE}}',
					'{{WRAPPER}}.premium-title-icon-column .premium-title-header' => 'align-items: {{VALUE}}',
				),
				'toggle'       => false,
				'condition'    => array(
					'premium_title_style' => array( 'style3', 'style4' ),
				),
			)
		);

		$this->add_control(
			'alignment_notice',
			array(
				'raw'             => __( 'Please note that left/right alignment is reversed when Icon Position is set to After.', 'premium-addons-for-elementor' ),
				'type'            => Controls_Manager::RAW_HTML,
				'content_classes' => 'elementor-panel-alert elementor-panel-alert-info',
				'condition'       => array(
					'premium_title_icon_switcher' => 'yes',
					'icon_position'               => 'row-reverse',
					'premium_title_style'         => array( 'style3', 'style4' ),
				),
			)
		);

		$this->add_control(
			'premium_title_stripe_pos',
			array(
				'label'                => __( 'Stripe Position', 'premium-addons-for-elementor' ),
				'type'                 => Controls_Manager::SELECT,
				'options'              => array(
					'top'    => __( 'Top', 'premium-addons-for-elementor' ),
					'bottom' => __( 'Bottom', 'premium-addons-for-elementor' ),
				),
				'selectors_dictionary' => array(
					'top'    => 'initial',
					'bottom' => '2',
				),
				'default'              => 'top',
				'label_block'          => true,
				'separator'            => 'before',
				'condition'            => array(
					'premium_title_style' => 'style7',
				),
				'selectors'            => array(
					'{{WRAPPER}} .premium-title-style7-stripe-wrap' => 'order: {{VALUE}}',
				),
			)
		);

		$this->add_responsive_control(
			'premium_title_style7_strip_width',
			array(
				'label'       => __( 'Stripe Width (PX)', 'premium-addons-for-elementor' ),
				'type'        => Controls_Manager::SLIDER,
				'size_units'  => array( 'px', '%', 'em' ),
				'default'     => array(
					'unit' => 'px',
					'size' => '120',
				),
				'selectors'   => array(
					'{{WRAPPER}} .premium-title-style7-stripe' => 'width: {{SIZE}}{{UNIT}};',
				),
				'label_block' => true,
				'condition'   => array(
					'premium_title_style' => 'style7',
				),
			)
		);

		$this->add_responsive_control(
			'premium_title_style7_strip_height',
			array(
				'label'       => __( 'Stripe Height (PX)', 'premium-addons-for-elementor' ),
				'type'        => Controls_Manager::SLIDER,
				'size_units'  => array( 'px', 'em' ),
				'default'     => array(
					'unit' => 'px',
					'size' => '5',
				),
				'label_block' => true,
				'selectors'   => array(
					'{{WRAPPER}} .premium-title-style7-stripe' => 'height: {{SIZE}}{{UNIT}};',
				),
				'condition'   => array(
					'premium_title_style' => 'style7',
				),
			)
		);

		$this->add_responsive_control(
			'premium_title_style7_strip_top_spacing',
			array(
				'label'       => __( 'Stripe Top Spacing (PX)', 'premium-addons-for-elementor' ),
				'type'        => Controls_Manager::SLIDER,
				'size_units'  => array( 'px', '%', 'em' ),
				'selectors'   => array(
					'{{WRAPPER}} .premium-title-style7-stripe-wrap' => 'margin-top: {{SIZE}}{{UNIT}};',
				),
				'label_block' => true,
				'condition'   => array(
					'premium_title_style' => 'style7',
				),
			)
		);

		$this->add_responsive_control(
			'premium_title_style7_strip_bottom_spacing',
			array(
				'label'       => __( 'Stripe Bottom Spacing (PX)', 'premium-addons-for-elementor' ),
				'type'        => Controls_Manager::SLIDER,
				'size_units'  => array( 'px', '%', 'em' ),
				'label_block' => true,
				'selectors'   => array(
					'{{WRAPPER}} .premium-title-style7-stripe-wrap' => 'margin-bottom: {{SIZE}}{{UNIT}};',
				),
				'condition'   => array(
					'premium_title_style' => 'style7',
				),
			)
		);

		$this->add_responsive_control(
			'premium_title_style7_strip_align',
			array(
				'label'     => __( 'Stripe Alignment', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::CHOOSE,
				'options'   => array(
					'flex-start' => array(
						'title' => __( 'Left', 'premium-addons-for-elementor' ),
						'icon'  => 'eicon-text-align-left',
					),
					'center'     => array(
						'title' => __( 'Center', 'premium-addons-for-elementor' ),
						'icon'  => 'eicon-text-align-center',
					),
					'flex-end'   => array(
						'title' => __( 'Right', 'premium-addons-for-elementor' ),
						'icon'  => 'eicon-text-align-right',
					),
				),
				'toggle'    => false,
				'default'   => 'center',
				'selectors' => array(
					'{{WRAPPER}}:not(.premium-title-icon-column) .premium-title-style7-stripe-wrap' => 'justify-content: {{VALUE}}',
					'{{WRAPPER}}.premium-title-icon-column .premium-title-style7-stripe-wrap' => 'align-self: {{VALUE}}',
				),
				'condition' => array(
					'premium_title_style' => 'style7',
				),
			)
		);

		$this->add_control(
			'link_switcher',
			array(
				'label' => __( 'Link', 'premium-addons-for-elementor' ),
				'type'  => Controls_Manager::SWITCHER,
			)
		);

		$this->add_control(
			'link_selection',
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
					'link_switcher' => 'yes',
				),
			)
		);

		$this->add_control(
			'custom_link',
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
					'link_switcher'  => 'yes',
					'link_selection' => 'url',
				),
			)
		);

		$this->add_control(
			'existing_link',
			array(
				'label'       => __( 'Existing Page', 'premium-addons-for-elementor' ),
				'type'        => Controls_Manager::SELECT2,
				'options'     => $this->getTemplateInstance()->get_all_posts(),
				'condition'   => array(
					'link_switcher'  => 'yes',
					'link_selection' => 'link',
				),
				'multiple'    => false,
				'label_block' => true,
			)
		);

		$this->add_control(
			'background_text_switcher',
			array(
				'label' => __( 'Background Text', 'premium-addons-for-elementor' ),
				'type'  => Controls_Manager::SWITCHER,
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
					'{{WRAPPER}} .premium-title-bg-text:before' => 'width: {{VALUE}}',
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
					'{{WRAPPER}} .premium-title-bg-text:before' => 'left: {{SIZE}}{{UNIT}}',
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
					'{{WRAPPER}} .premium-title-bg-text:before' => 'top: {{SIZE}}{{UNIT}}',
				),
				'condition'  => array(
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
					'{{WRAPPER}} .premium-title-bg-text:before' => 'transform: rotate({{SIZE}}{{UNIT}})',
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
				'description'  => __( 'Note: This effect takes place once the element is in the viewport', 'premium-addons-for-elementor' ),
				'render_type'  => 'template',
				'prefix_class' => 'premium-mask-',
				'condition'    => array(
					'premium_title_style!' => 'style9',
				),
			)
		);

		$this->add_control(
			'mask_title_color',
			array(
				'label'       => __( 'Mask Color', 'premium-addons-for-elementor' ),
				'type'        => Controls_Manager::COLOR,
				'render_type' => 'template',
				'selectors'   => array(
					'{{WRAPPER}}.premium-mask-yes .premium-mask-span::after'   => 'background: {{VALUE}};',
				),
				'condition'   => array(
					'mask_switcher'        => 'yes',
					'premium_title_style!' => 'style9',
				),
			)
		);

		$this->add_control(
			'mask_title_dir',
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
					'mask_switcher'        => 'yes',
					'premium_title_style!' => 'style9',
				),
			)
		);

		$this->add_responsive_control(
			'mask_title_padding',
			array(
				'label'      => __( 'Words Padding', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-mask-span' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
				'condition'  => array(
					'mask_switcher'        => 'yes',
					'premium_title_style!' => 'style9',
				),
			)
		);

		$this->add_control(
			'gradient_text_switcher',
			array(
				'label'        => __( 'Animated Gradient', 'premium-addons-for-elementor' ),
				'type'         => Controls_Manager::SWITCHER,
				'prefix_class' => 'premium-title-gradient-',
				'separator'    => 'before',
				'condition'    => array(
					'premium_title_style!' => array( 'style8', 'style9' ),
					'mask_switcher!'       => 'yes',
					'stroke_switcher!'     => 'yes',
					'background_style'     => 'color',
				),
			)
		);

		$this->add_control(
			'animation_transition_speed',
			array(
				'label'     => __( 'Animation Speed (sec)', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::SLIDER,
				'range'     => array(
					'px' => array(
						'min'  => 0,
						'max'  => 10,
						'step' => .1,
					),
				),
				'selectors' => array(
					'{{WRAPPER}}.premium-title-gradient-yes .premium-title-text ,{{WRAPPER}}.premium-title-gradient-yes .premium-title-icon' => 'animation-duration: {{SIZE}}s ',
				),
				'condition' => array(
					'gradient_text_switcher' => 'yes',
					'premium_title_style!'   => array( 'style8', 'style9' ),
					'mask_switcher!'         => 'yes',
					'stroke_switcher!'       => 'yes',
					'background_style'       => 'color',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			array(
				'name'      => 'text_gradient',
				'types'     => array( 'gradient' ),
				'selector'  => '{{WRAPPER}}.premium-title-gradient-yes .premium-title-text, {{WRAPPER}}.premium-title-gradient-yes .premium-title-icon',
				'condition' => array(
					'gradient_text_switcher' => 'yes',
					'premium_title_style!'   => array( 'style8', 'style9' ),
					'mask_switcher!'         => 'yes',
					'stroke_switcher!'       => 'yes',
					'background_style'       => 'color',
				),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'premium_title_style_section',
			array(
				'label' => __( 'Title', 'premium-addons-for-elementor' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_control(
			'premium_title_color',
			array(
				'label'     => __( 'Color', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'global'    => array(
					'default' => Global_Colors::COLOR_PRIMARY,
				),
				'selectors' => array(
					'{{WRAPPER}} .premium-title-header' => 'color: {{VALUE}}',
					'{{WRAPPER}}.premium-title-stroke-yes .premium-title-text' => '-webkit-text-fill-color: {{VALUE}}',
					'{{WRAPPER}} .premium-title-style8 .premium-title-text[data-animation="shiny"]' => '--base-color: {{VALUE}}',
				),
			)
		);

		$this->add_control(
			'premium_title_blur_color',
			array(
				'label'     => __( 'Blur Color', 'booster-addons' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#000',
				'selectors' => array( '{{WRAPPER}} .premium-title-header' => '--shadow-color: {{VALUE}};' ),
				'condition' => array(
					'premium_title_style' => 'style9',
				),
			)
		);

		$this->add_control(
			'shining_shiny_color_title',
			array(
				'label'     => __( 'Shiny Color', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#fff',
				'condition' => array(
					'premium_title_style' => 'style8',
				),
				'selectors' => array( '{{WRAPPER}} .premium-title-style8 .premium-title-text[data-animation="shiny"]' => '--shiny-color: {{VALUE}}' ),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'title_typography',
				'global'   => array(
					'default' => Global_Typography::TYPOGRAPHY_PRIMARY,
				),
				'selector' => '{{WRAPPER}} .premium-title-header',
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'           => 'style_one_border',
				'fields_options' => array(
					'border' => array(
						'default' => 'solid',
					),
				),
				'selector'       => '{{WRAPPER}} .premium-title-style1',
				'condition'      => array(
					'premium_title_style' => 'style1',
				),
			)
		);

		$this->add_control(
			'background_style',
			array(
				'label'        => __( 'Background Style', 'premium-addons-for-elementor' ),
				'type'         => Controls_Manager::SELECT,
				'description'  => __( 'Choose ‘Normal’ to add a background behind the text and ‘Clipped’ so the background will be clipped on the text.', 'premium-addons-for-elementor' ),
				'options'      => array(
					'color'   => __( 'Normal', 'premium-addons-for-elementor' ),
					'clipped' => __( 'Clipped', 'premium-addons-for-elementor' ),
				),
				'prefix_class' => 'premium-title-',
				'label_block'  => true,
				'separator'    => 'before',
				'condition'    => array(
					'premium_title_style!' => array( 'style8', 'style9' ),
				),
			)
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			array(
				'name'      => 'title_background',
				'types'     => array( 'classic', 'gradient' ),
				'condition' => array(
					'premium_title_style!' => array( 'style8', 'style9' ),
					'background_style!'    => '',
				),
				'selector'  => '{{WRAPPER}}:not(.premium-title-clipped) .premium-title-header, {{WRAPPER}}.premium-title-clipped .premium-title-text',
			)
		);

		$this->add_control(
			'premium_title_style2_background_color',
			array(
				'label'     => __( 'Background Color', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'global'    => array(
					'default' => Global_Colors::COLOR_SECONDARY,
				),
				'selectors' => array(
					'{{WRAPPER}}:not(.premium-title-clipped) .premium-title-style2, {{WRAPPER}}.premium-title-clipped .premium-title-text' => 'background-color: {{VALUE}};',
				),
				'condition' => array(
					'premium_title_style' => 'style2',
					'background_style'    => '',
				),
			)
		);

		$this->add_control(
			'premium_title_style3_background_color',
			array(
				'label'     => __( 'Background Color', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'global'    => array(
					'default' => Global_Colors::COLOR_SECONDARY,
				),
				'selectors' => array(
					'{{WRAPPER}}:not(.premium-title-clipped) .premium-title-style3, {{WRAPPER}}.premium-title-clipped .premium-title-text' => 'background-color: {{VALUE}};',
				),
				'condition' => array(
					'premium_title_style' => 'style3',
					'background_style'    => '',
				),
			)
		);

		$this->add_control(
			'premium_title_style5_header_line_color',
			array(
				'label'     => __( 'Line Color', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'global'    => array(
					'default' => Global_Colors::COLOR_PRIMARY,
				),
				'selectors' => array(
					'{{WRAPPER}} .premium-title-style5' => 'border-bottom: 2px solid {{VALUE}};',
				),
				'condition' => array(
					'premium_title_style' => 'style5',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'      => 'style_five_border',
				'selector'  => '{{WRAPPER}} .premium-title-container',
				'condition' => array(
					'premium_title_style' => array( 'style2', 'style4', 'style5', 'style6' ),
				),
			)
		);

		$this->add_control(
			'premium_title_style6_header_line_color',
			array(
				'label'     => __( 'Line Color', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'global'    => array(
					'default' => Global_Colors::COLOR_PRIMARY,
				),
				'selectors' => array(
					'{{WRAPPER}} .premium-title-style6' => 'border-bottom: 2px solid {{VALUE}};',
				),
				'condition' => array(
					'premium_title_style' => 'style6',
				),
			)
		);

		$this->add_control(
			'premium_title_style6_triangle_color',
			array(
				'label'     => __( 'Triangle Color', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'global'    => array(
					'default' => Global_Colors::COLOR_PRIMARY,
				),
				'selectors' => array(
					'{{WRAPPER}} .premium-title-style6:before' => 'border-bottom-color: {{VALUE}};',
				),
				'condition' => array(
					'premium_title_style' => 'style6',
				),
			)
		);

		$this->add_control(
			'premium_title_style7_strip_color',
			array(
				'label'     => __( 'Stripe Color', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'global'    => array(
					'default' => Global_Colors::COLOR_PRIMARY,
				),
				'selectors' => array(
					'{{WRAPPER}} .premium-title-style7-stripe' => 'background-color: {{VALUE}};',
				),
				'condition' => array(
					'premium_title_style' => 'style7',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Text_Shadow::get_type(),
			array(
				'label'    => __( 'Shadow', 'premium-addons-for-elementor' ),
				'name'     => 'premium_title_text_shadow',
				'selector' => '{{WRAPPER}} .premium-title-header',
			)
		);

		$this->add_control(
			'premium_title_shadow_value',
			array(
				'label'     => esc_html__( 'Blur Shadow Value (px)', 'booster-addons' ),
				'type'      => Controls_Manager::NUMBER,
				'min'       => '10',
				'max'       => '500',
				'step'      => '10',
				'dynamic'   => array( 'active' => true ),
				'selectors' => array( '{{WRAPPER}} .premium-title-header' => '--shadow-value: {{VALUE}}px;' ),
				'default'   => '120',
				'condition' => array(
					'premium_title_style' => 'style9',
				),
			)
		);

		$this->add_control(
			'premium_title_delay',
			array(
				'label'       => esc_html__( 'Animation Delay (s)', 'premium-addons-for-elementor' ),
				'type'        => Controls_Manager::NUMBER,
				'min'         => '1',
				'max'         => '30',
				'step'        => 0.5,
				'condition'   => array(
					'premium_title_style' => array( 'style8', 'style9' ),
				),
				'render_type' => 'template',
				'dynamic'     => array( 'active' => true ),
				'default'     => '2',
			)
		);

		$this->add_control(
			'shining_animation_duration',
			array(
				'label'              => __( 'Animation Duration (s)', 'premium-addons-for-elementor' ),
				'type'               => Controls_Manager::NUMBER,
				'default'            => '1',
				'step'               => 0.5,
				'condition'          => array(
					'premium_title_style' => 'style8',
				),
				'frontend_available' => true,
				'render_type'        => 'template',
				'selectors'          => array(
					'{{WRAPPER}} .premium-title-style8 .premium-title-text[data-animation="shiny"]' => '--animation-speed: {{VALUE}}s ',
				),
			)
		);

		$this->add_responsive_control(
			'premium_title_margin',
			array(
				'label'      => __( 'Margin', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-title-text' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'premium_title_padding',
			array(
				'label'      => __( 'Padding', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-title-text' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'stroke_switcher',
			array(
				'label'        => __( 'Stroke', 'premium-addons-for-elementor' ),
				'type'         => Controls_Manager::SWITCHER,
				'prefix_class' => 'premium-title-stroke-',
				'condition'    => array(
					'premium_title_style!' => 'style9',
					'background_style'     => 'color',
				),
			)
		);

		$this->add_control(
			'stroke_text_color',
			array(
				'label'     => __( 'Stroke Color', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'condition' => array(
					'stroke_switcher'      => 'yes',
					'background_style'     => 'color',
					'premium_title_style!' => 'style9',
				),
				'selectors' => array(
					'{{WRAPPER}} .premium-title-text' => '-webkit-text-stroke-color: {{VALUE}};',
				),
			)
		);

		$this->add_responsive_control(
			'stroke_width',
			array(
				'label'     => __( 'Stroke Fill Width', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::SLIDER,
				'condition' => array(
					'stroke_switcher'      => 'yes',
					'background_style'     => 'color',
					'premium_title_style!' => 'style9',
				),
				'default'   => array(
					'size' => 1,
					'unit' => 'px',
				),
				'selectors' => array(
					'{{WRAPPER}} .premium-title-text' => '-webkit-text-stroke-width: {{SIZE}}px',
				),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'premium_title_icon_style_section',
			array(
				'label'     => __( 'Icon', 'premium-addons-for-elementor' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => array(
					'premium_title_icon_switcher' => 'yes',
				),
			)
		);

		$this->add_control(
			'premium_title_icon_color',
			array(
				'label'     => __( 'Color', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'global'    => array(
					'default' => Global_Colors::COLOR_PRIMARY,
				),
				'selectors' => array(
					'{{WRAPPER}} .premium-title-icon' => 'color: {{VALUE}}',
					'{{WRAPPER}} .premium-drawable-icon *, {{WRAPPER}} svg:not([class*="premium-"])' => 'fill: {{VALUE}};',
				),
				'condition' => array(
					'icon_type' => array( 'icon', 'svg' ),
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
						'icon_type' => array( 'icon', 'svg' ),
					),
					'selectors' => array(
						'{{WRAPPER}} .premium-drawable-icon *, {{WRAPPER}} svg:not([class*="premium-"])' => 'stroke: {{VALUE}};',
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
						'icon_type' => array( 'icon', 'svg' ),
						'draw_svg'  => 'yes',
					),
				)
			);
		}

		$this->add_responsive_control(
			'premium_title_icon_size',
			array(
				'label'      => __( 'Size', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', 'em', '%' ),
				'range'      => array(
					'px' => array(
						'min' => 10,
						'max' => 300,
					),
					'em' => array(
						'min' => 1,
						'max' => 30,
					),
				),
				'condition'  => array(
					'icon_type!' => 'svg',
				),
				'selectors'  => array(
					'{{WRAPPER}} .premium-title-header i' => 'font-size: {{SIZE}}{{UNIT}}',
					'{{WRAPPER}} .premium-title-header svg, {{WRAPPER}} .premium-title-header img' => 'width: {{SIZE}}{{UNIT}} !important; height: {{SIZE}}{{UNIT}} !important',
				),
			)
		);

		$this->add_responsive_control(
			'svg_icon_width',
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
					'icon_type' => 'svg',
				),
				'selectors'  => array(
					'{{WRAPPER}} .premium-title-header svg' => 'width: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'svg_icon_height',
			array(
				'label'      => __( 'Height', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', 'em', 'custom' ),
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
					'icon_type' => 'svg',
				),
				'selectors'  => array(
					'{{WRAPPER}} .premium-title-header svg' => 'height: {{SIZE}}{{UNIT}}',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			array(
				'name'     => 'premium_title_icon_background',
				'types'    => array( 'classic', 'gradient' ),
				'selector' => '{{WRAPPER}} .premium-title-icon',
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'     => 'premium_title_icon_border',
				'selector' => '{{WRAPPER}} .premium-title-icon',
			)
		);

		$this->add_control(
			'premium_title_icon_border_radius',
			array(
				'label'      => __( 'Border Radius', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', '%', 'em' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-title-icon' => 'border-radius: {{SIZE}}{{UNIT}};',
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
					'{{WRAPPER}} .premium-title-icon' => 'border-radius: {{VALUE}};',
				),
				'condition' => array(
					'icon_adv_radius' => 'yes',
				),
			)
		);

		$this->add_responsive_control(
			'premium_title_icon_margin',
			array(
				'label'      => __( 'Margin', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-title-icon' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}',
				),
			)
		);

		$this->add_responsive_control(
			'premium_title_icon_padding',
			array(
				'label'      => __( 'Padding', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-title-icon' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Text_Shadow::get_type(),
			array(
				'label'     => __( 'Icon Shadow', 'premium-addons-for-elementor' ),
				'name'      => 'premium_title_icon_text_shadow',
				'selector'  => '{{WRAPPER}} .premium-title-icon',
				'condition' => array(
					'icon_type' => 'icon',
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
					'{{WRAPPER}} .premium-title-bg-text:before' => 'color: {{VALUE}}',
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
				'selector' => '{{WRAPPER}} .premium-title-bg-text:before',
			)
		);

		$this->add_group_control(
			Group_Control_Text_Shadow::get_type(),
			array(
				'name'     => 'background_text_shadow',
				'selector' => '{{WRAPPER}} .premium-title-bg-text:before',
			)
		);

		$this->add_control(
			'bg_stroke_switcher',
			array(
				'label'        => __( 'Stroke', 'premium-addons-for-elementor' ),
				'type'         => Controls_Manager::SWITCHER,
				'prefix_class' => 'premium-title-bg-stroke-',
			)
		);

		$this->add_control(
			'bg_stroke_text_color',
			array(
				'label'     => __( 'Stroke Color', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'condition' => array(
					'bg_stroke_switcher' => 'yes',
				),
				'selectors' => array(
					'{{WRAPPER}} .premium-title-bg-text::before' => '-webkit-text-stroke-color: {{VALUE}};',
				),
			)
		);

		$this->add_responsive_control(
			'bg_stroke_width',
			array(
				'label'     => __( 'Stroke Fill Width', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::SLIDER,
				'default'   => array(
					'size' => 1,
					'unit' => 'px',
				),
				'condition' => array(
					'bg_stroke_switcher' => 'yes',
				),
				'selectors' => array(
					'{{WRAPPER}} .premium-title-bg-text::before' => '-webkit-text-stroke-width: {{SIZE}}px',
				),
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
					'{{WRAPPER}} .premium-title-bg-text:before' => 'mix-blend-mode: {{VALUE}}',
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
					'{{WRAPPER}} .premium-title-bg-text:before' => 'z-index: {{VALUE}}',
				),
			)
		);

		$this->end_controls_section();

	}

	/**
	 * Render title widget output on the frontend.
	 *
	 * Written in PHP and used to generate the final HTML.
	 *
	 * @since  1.0.0
	 * @access protected
	 */
	protected function render() {

		$settings = $this->get_settings_for_display();

		$this->add_inline_editing_attributes( 'premium_title_text', 'none' );

		$this->add_render_attribute( 'premium_title_text', 'class', 'premium-title-text' );

		$title_tag = Helper_Functions::validate_html_tag( $settings['premium_title_tag'] );

		$selected_style = $settings['premium_title_style'];

		$this->add_render_attribute( 'container', 'class', array( 'premium-title-container', $selected_style ) );

		$this->add_render_attribute( 'title', 'class', array( 'premium-title-header', 'premium-title-' . $selected_style ) );

		if ( 'style8' === $selected_style ) {

			$this->add_render_attribute(
				'premium_title_text',
				array(
					'data-shiny-delay' => $settings['premium_title_delay'],
					'data-shiny-dur'   => $settings['shining_animation_duration'],
				)
			);

		} elseif ( 'style9' === $selected_style ) {

			$this->add_render_attribute( 'title', 'data-blur-delay', $settings['premium_title_delay'] );

		}

		$icon_position = '';

		if ( 'yes' === $settings['premium_title_icon_switcher'] ) {

			$icon_type = $settings['icon_type'];

			$icon_position = $settings['icon_position'];

			if ( 'icon' === $icon_type || 'svg' === $icon_type ) {

				$this->add_render_attribute( 'icon', 'class', 'premium-drawable-icon' );

				if ( 'icon' === $icon_type ) {

					if ( ! empty( $settings['premium_title_icon'] ) ) {

						$this->add_render_attribute(
							'icon',
							array(
								'class'       => array(
									'premium-title-icon',
									$settings['premium_title_icon'],
								),
								'aria-hidden' => 'true',
							)
						);

					}

					$migrated = isset( $settings['__fa4_migrated']['premium_title_icon_updated'] );
					$is_new   = empty( $settings['premium_title_icon'] ) && Icons_Manager::is_migration_allowed();

				}

				if ( ( 'yes' === $settings['draw_svg'] && 'icon' === $icon_type ) || 'svg' === $icon_type ) {
					$this->add_render_attribute( 'icon', 'class', 'premium-title-icon' );
				}

				if ( 'yes' === $settings['draw_svg'] ) {

					$this->add_render_attribute(
						'container',
						'class',
						array(
							'elementor-invisible',
							'premium-drawer-hover',
						)
					);

					if ( 'icon' === $icon_type ) {

						$this->add_render_attribute( 'icon', 'class', $settings['premium_title_icon_updated']['value'] );

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
			} elseif ( 'animation' === $icon_type ) {
				$this->add_render_attribute(
					'title_lottie',
					array(
						'class'               => array(
							'premium-title-icon',
							'premium-lottie-animation',
						),
						'data-lottie-url'     => $settings['lottie_url'],
						'data-lottie-loop'    => $settings['lottie_loop'],
						'data-lottie-reverse' => $settings['lottie_reverse'],
					)
				);
			} else {

				$src = $settings['image_upload']['url'];

				$alt = Control_Media::get_image_alt( $settings['image_upload'] );

				$this->add_render_attribute(
					'title_img',
					array(
						'class' => 'premium-title-icon',
						'src'   => $src,
						'alt'   => $alt,
					)
				);
			}
		}

		if ( 'yes' === $settings['link_switcher'] ) {

			if ( 'link' === $settings['link_selection'] ) {

				$this->add_render_attribute( 'link', 'href', get_permalink( $settings['existing_link'] ) );

			} else {

				$this->add_link_attributes( 'link', $settings['custom_link'] );

			}
		}

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

	<div <?php echo wp_kses_post( $this->get_render_attribute_string( 'container' ) ); ?>>
		<<?php echo wp_kses_post( $title_tag . ' ' . $this->get_render_attribute_string( 'title' ) ); ?>>
			<?php if ( 'style7' === $selected_style ) : ?>
				<?php if ( 'column' !== $icon_position ) : ?>
					<span class="premium-title-style7-stripe-wrap">
						<span class="premium-title-style7-stripe"></span>
					</span>
				<?php endif; ?>
				<div class="premium-title-style7-inner">
			<?php endif; ?>

			<?php if ( 'yes' === $settings['premium_title_icon_switcher'] ) : ?>
				<?php if ( 'icon' === $icon_type ) : ?>
					<?php
					if ( ( $is_new || $migrated ) && 'yes' !== $settings['draw_svg'] ) :
						Icons_Manager::render_icon(
							$settings['premium_title_icon_updated'],
							array(
								'class'       => array( 'premium-title-icon', 'premium-svg-nodraw', 'premium-drawable-icon' ),
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
				<?php elseif ( 'animation' === $icon_type ) : ?>
					<div <?php echo wp_kses_post( $this->get_render_attribute_string( 'title_lottie' ) ); ?>></div>
				<?php else : ?>
					<?php if ( 'yes' === $settings['mask_switcher'] ) : ?>
						<span class="premium-title-img">
					<?php endif; ?>
						<img <?php echo wp_kses_post( $this->get_render_attribute_string( 'title_img' ) ); ?>></span>
					<?php if ( 'yes' === $settings['mask_switcher'] ) : ?>
						</span>
					<?php endif; ?>
				<?php endif; ?>
			<?php endif; ?>

			<?php if ( 'style7' === $selected_style ) : ?>
				<?php if ( 'column' === $icon_position ) : ?>
					<span class="premium-title-style7-stripe-wrap">
						<span class="premium-title-style7-stripe"></span>
					</span>
				<?php endif; ?>
			<?php endif; ?>
			<?php if ( 'style9' !== $selected_style ) : ?>
			<span <?php echo wp_kses_post( $this->get_render_attribute_string( 'premium_title_text' ) ); ?> >
				<?php echo wp_kses_post( $settings['premium_title_text'] ); ?>
			</span>
				<?php
			else :
					$letters_html = '<span class="premium-letters-container"' . $this->get_render_attribute_string( 'premium_title_text' ) . '>';
					$title_array  = preg_split( '//u', $settings['premium_title_text'], null, PREG_SPLIT_NO_EMPTY );
				foreach ( $title_array as $key => $letter ) :
					$key           = $key++;
					$letters_html .= '<span class="premium-title-style9-letter" data-letter-index="' . esc_attr( $key + 1 ) . '" data-letter="' . esc_attr( $letter ) . '">' . $letter . '</span>';
				endforeach;
					$the_title = $letters_html . '</span>';
					echo wp_kses_post( $the_title );
				?>
			<?php endif; ?>

			<?php if ( 'style7' === $selected_style ) : ?>
				</div>
			<?php endif; ?>
			<?php if ( 'yes' === $settings['link_switcher'] ) : ?>
				<a <?php echo wp_kses_post( $this->get_render_attribute_string( 'link' ) ); ?>></a>
			<?php endif; ?>
		</<?php echo wp_kses_post( $title_tag ); ?>>
	</div>

		<?php
	}

	/**
	 * Render Title widget output in the editor.
	 *
	 * Written as a Backbone JavaScript template and used to generate the live preview.
	 *
	 * @since  1.0.0
	 * @access protected
	 */
	protected function content_template() {
		?>
		<#

			view.addInlineEditingAttributes('premium_title_text', 'none');

			view.addRenderAttribute('premium_title_text', 'class', 'premium-title-text');

			var titleTag = elementor.helpers.validateHTMLTag( settings.premium_title_tag ),

			selectedStyle = settings.premium_title_style,

			titleIcon = settings.premium_title_icon,

			titleText = settings.premium_title_text;

			view.addRenderAttribute( 'container', 'class', [ 'premium-title-container', selectedStyle ] );

			view.addRenderAttribute( 'premium_title', 'class', [ 'premium-title-header', 'premium-title-' + selectedStyle ] );

			if ( selectedStyle === 'style9' ) {
				view.addRenderAttribute( 'premium_title', 'data-blur-delay', settings.premium_title_delay );
			} else if( selectedStyle  === 'style8') {
				view.addRenderAttribute( 'premium_title_text', 'data-shiny-delay', settings.premium_title_delay );
				view.addRenderAttribute( 'premium_title_text', 'data-shiny-dur', settings.shining_animation_duration );
			}

			view.addRenderAttribute( 'icon', 'class', [ 'premium-title-icon', titleIcon ] );

			var iconPosition = '';

			if( 'yes' === settings.premium_title_icon_switcher ) {

				var iconType = settings.icon_type;

				iconPosition = settings.icon_position;

				if( 'icon' === iconType || 'svg' === iconType ) {

					view.addRenderAttribute( 'icon', 'class', 'premium-drawable-icon' );

					if( 'icon' === iconType ) {

						var iconHTML = 'yes' !== settings.draw_svg ? elementor.helpers.renderIcon( view, settings.premium_title_icon_updated, { 'class': ['premium-title-icon', 'premium-svg-nodraw', 'premium-drawable-icon'], 'aria-hidden': true }, 'i' , 'object' ) : false,
							migrated = elementor.helpers.isIconMigrated( settings, 'premium_title_icon_updated' );

					}

					if( ( 'yes' === settings.draw_svg && 'icon' === iconType ) || 'svg' === iconType ) {
						view.addRenderAttribute( 'icon', 'class',  'premium-title-icon' );
					}

					if ( 'yes' === settings.draw_svg ) {

						view.addRenderAttribute( 'container', 'class', 'premium-drawer-hover' );

						if ( 'icon' === iconType ) {

							view.addRenderAttribute( 'icon', 'class', settings.premium_title_icon_updated.value );

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

				} else if( 'animation' === iconType ) {

					view.addRenderAttribute( 'title_lottie', {
						'class': [
							'premium-title-icon',
							'premium-lottie-animation'
						],
						'data-lottie-url': settings.lottie_url,
						'data-lottie-loop': settings.lottie_loop,
						'data-lottie-reverse': settings.lottie_reverse
					});

				} else {

					view.addRenderAttribute( 'title_img', {
						'class': 'premium-title-icon',
						'src': settings.image_upload.url
					});

				}

			}

			if( 'yes' === settings.link_switcher ) {

				var link = '';

				if( settings.link_selection === 'link' ) {

					link = settings.existing_link;

				} else {

					link = settings.custom_link.url;

				}

				view.addRenderAttribute( 'link', 'href', link );

			}

			if( 'yes' === settings.background_text_switcher ) {
				view.addRenderAttribute( 'container', {
					'class': 'premium-title-bg-text',
					'data-background': settings.background_text
				});
			}

		#>
		<div {{{ view.getRenderAttributeString('container') }}}>
			<{{{titleTag}}} {{{view.getRenderAttributeString('premium_title')}}}>
				<# if( 'style7' === selectedStyle ) { #>
					<# if( 'column' !== iconPosition ) { #>
						<span class="premium-title-style7-stripe-wrap">
							<span class="premium-title-style7-stripe"></span>
						</span>
					<# } #>
					<div class="premium-title-style7-inner">
				<# }
					if( 'yes' === settings.premium_title_icon_switcher ) { #>
						<# if( 'icon' === iconType ) { #>
							<# if ( iconHTML && iconHTML.rendered && ( ! settings.premium_title_icon || migrated ) ) { #>
								{{{ iconHTML.value }}}
							<# } else { #>
								<i {{{ view.getRenderAttributeString( 'icon' ) }}}></i>
							<# } #>

						<# } else if( 'svg' === iconType ) { #>
							<div {{{ view.getRenderAttributeString('icon') }}}>
								{{{ settings.custom_svg }}}
							</div>
						<# } else if( 'animation' === iconType ) { #>
							<div {{{ view.getRenderAttributeString('title_lottie') }}}></div>
						<# } else { #>
							<span class="premium-title-img"><img {{{ view.getRenderAttributeString('title_img') }}}></span>
						<# } #>
					<# } #>
				<# if( 'style7' === selectedStyle ) { #>
					<# if( 'column' === iconPosition ) { #>
						<span class="premium-title-style7-stripe-wrap">
							<span class="premium-title-style7-stripe"></span>
						</span>
					<# } #>
				<# } #>
				<# if( selectedStyle !== 'style9' ) {#>
				<span {{{ view.getRenderAttributeString('premium_title_text') }}} >{{{ titleText }}}</span>
				<# } else {
						lettersHtml = '<span class="premium-letters-container"'+ view.getRenderAttributeString('premium_title_text') +'>';
						text =  titleText;
						titleArray = text.split('');
						key = 0;
						titleArray.forEach(function (item) {
							key = key + 1;
							lettersHtml +='<span class="premium-title-style9-letter" data-letter-index="'+(key+1)+'" data-letter="'+(item)+'">'+item+'</span>';
					});
					theTitle = lettersHtml + '</span>'; #>
						{{{theTitle}}}
						<#
					}
				#>
				<# if( 'style7' === selectedStyle ) { #>
					</div>
				<# } #>
				<# if( 'yes' === settings.link_switcher && '' !== link ) { #>
					<a {{{ view.getRenderAttributeString('link') }}}></a>
				<# } #>
			</{{{titleTag}}}>
		</div>

		<?php
	}
}
