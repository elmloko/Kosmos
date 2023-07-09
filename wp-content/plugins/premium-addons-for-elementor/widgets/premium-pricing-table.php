<?php
/**
 * Premium Pricing Table.
 */

namespace PremiumAddons\Widgets;

// Elementor Classes.
use Elementor\Modules\DynamicTags\Module as TagsModule;
use Elementor\Icons_Manager;
use Elementor\Utils;
use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Control_Media;
use Elementor\Repeater;
use Elementor\Core\Kits\Documents\Tabs\Global_Colors;
use Elementor\Core\Kits\Documents\Tabs\Global_Typography;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Typography;
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
 * Class Premium_Pricing_Table
 */
class Premium_Pricing_Table extends Widget_Base {

	/**
	 * Check Icon Draw Option.
	 *
	 * @since 4.9.26
	 * @access public
	 */
	public function check_icon_draw() {
		$is_enabled = Admin_Helper::check_svg_draw( 'premium-pricing-table' );
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
		return 'premium-addon-pricing-table';
	}

	/**
	 * Retrieve Widget Title.
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function get_title() {
		return __( 'Pricing Table', 'premium-addons-for-elementor' );
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
		return 'pa-pricing-table';
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
			)
		);
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
	 * Retrieve Widget Keywords.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return string Widget keywords.
	 */
	public function get_keywords() {
		return array( 'pa', 'premium', 'price', 'feature', 'list', 'bullet', 'cta' );
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
	 * Register Pricing Table controls.
	 *
	 * @since  1.0.0
	 * @access protected
	 */
	protected function register_controls() { // phpcs:ignore PSR2.Methods.MethodDeclaration.Underscore

		$draw_icon = $this->check_icon_draw();

		$this->start_controls_section(
			'premium_pricing_table_icon_section',
			array(
				'label'     => __( 'Icon', 'premium-addons-for-elementor' ),
				'condition' => array(
					'premium_pricing_table_icon_switcher' => 'yes',
				),
			)
		);

		$this->add_control(
			'icon_type',
			array(
				'label'   => __( 'Icon Type', 'premium-addons-for-elementor' ),
				'type'    => Controls_Manager::SELECT,
				'options' => array(
					'icon'      => __( 'Icon', 'premium-addons-for-elementor' ),
					'image'     => __( 'Image', 'premium-addons-for-elementor' ),
					'animation' => __( 'Lottie Animation', 'premium-addons-for-elementor' ),
					'svg'       => __( 'SVG Code', 'premium-addons-for-elementor' ),
				),
				'default' => 'icon',
			)
		);

		$this->add_control(
			'premium_pricing_table_icon_selection_updated',
			array(
				'label'            => __( 'Select an Icon', 'premium-addons-for-elementor' ),
				'type'             => Controls_Manager::ICONS,
				'fa4compatibility' => 'premium_pricing_table_icon_selection',
				'default'          => array(
					'value'   => 'fas fa-star',
					'library' => 'fa-solid',
				),
				'condition'        => array(
					'icon_type' => 'icon',
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
					'icon_type' => 'animation',
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
					'icon_type' => 'svg',
				),
			)
		);

		$this->add_control(
			'draw_svg',
			array(
				'label'     => __( 'Draw Icon', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::SWITCHER,
				'classes'   => $draw_icon ? '' : 'editor-pa-control-disabled',
				'condition' => array(
					'icon_type' => array( 'icon', 'svg' ),
					'premium_pricing_table_icon_selection_updated[library]!' => 'svg',
				),
			)
		);

		$animation_conds = array(

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
					'condition' => array(
						'icon_type' => array( 'icon', 'svg' ),
					),
					'selectors' => array(
						'{{WRAPPER}} .premium-pricing-icon-container svg *' => 'stroke-width: {{SIZE}}',
					),
				)
			);

			$this->add_control(
				'svg_sync',
				array(
					'label'     => __( 'Draw All Paths Together', 'premium-addons-for-elementor' ),
					'type'      => Controls_Manager::SWITCHER,
					'condition' => array(
						'icon_type' => array( 'icon', 'svg' ),
						'draw_svg'  => 'yes',
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
					'condition'   => array(
						'icon_type' => array( 'icon', 'svg' ),
						'draw_svg'  => 'yes',
					),
				)
			);
		} else {

			Helper_Functions::get_draw_svg_notice(
				$this,
				'pricing',
				array(
					'icon_type' => array( 'icon', 'svg' ),
					'premium_pricing_table_icon_selection_updated[library]!' => 'svg',
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
					'condition'   => array(
						'icon_type'       => array( 'icon', 'svg' ),
						'draw_svg'        => 'yes',
						'lottie_reverse!' => 'true',
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
					'condition'   => array(
						'icon_type'      => array( 'icon', 'svg' ),
						'draw_svg'       => 'yes',
						'lottie_reverse' => 'true',
					),
				)
			);

			$this->add_control(
				'svg_hover',
				array(
					'label'        => __( 'Only Play on Hover', 'premium-addons-for-elementor' ),
					'type'         => Controls_Manager::SWITCHER,
					'return_value' => 'true',
					'condition'    => array(
						'icon_type' => array( 'icon', 'svg' ),
						'draw_svg'  => 'yes',
					),
				)
			);

			$this->add_control(
				'svg_yoyo',
				array(
					'label'     => __( 'Yoyo Effect', 'premium-addons-for-elementor' ),
					'type'      => Controls_Manager::SWITCHER,
					'condition' => array(
						'icon_type'   => array( 'icon', 'svg' ),
						'draw_svg'    => 'yes',
						'lottie_loop' => 'true',
					),
				)
			);
		}

		$this->add_control(
			'premium_pricing_table_image',
			array(
				'label'     => __( 'Choose Image', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::MEDIA,
				'default'   => array(
					'url' => Utils::get_placeholder_image_src(),
				),
				'condition' => array(
					'icon_type' => 'image',
				),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'premium_pricing_table_title_section',
			array(
				'label'     => __( 'Title', 'premium-addons-for-elementor' ),
				'condition' => array(
					'premium_pricing_table_title_switcher' => 'yes',
				),
			)
		);

		$this->add_control(
			'premium_pricing_table_title_text',
			array(
				'label'       => __( 'Text', 'premium-addons-for-elementor' ),
				'default'     => __( 'Pricing Table', 'premium-addons-for-elementor' ),
				'type'        => Controls_Manager::TEXT,
				'dynamic'     => array( 'active' => true ),
				'label_block' => true,
			)
		);

		$this->add_control(
			'premium_pricing_table_title_size',
			array(
				'label'       => __( 'HTML Tag', 'premium-addons-for-elementor' ),
				'description' => __( 'Select HTML tag for the title', 'premium-addons-for-elementor' ),
				'type'        => Controls_Manager::SELECT,
				'default'     => 'h3',
				'options'     => array(
					'h1'   => 'H1',
					'h2'   => 'H2',
					'h3'   => 'H3',
					'h4'   => 'H4',
					'h5'   => 'H5',
					'h6'   => 'H6',
					'div'  => 'div',
					'span' => 'span',
					'p'    => 'p',
				),
				'label_block' => true,
			)
		);

		$this->end_controls_section();

		/*Price Content Section*/
		$this->start_controls_section(
			'premium_pricing_table_price_section',
			array(
				'label'     => __( 'Price', 'premium-addons-for-elementor' ),
				'condition' => array(
					'premium_pricing_table_price_switcher' => 'yes',
				),
			)
		);

		/*Price Value*/
		$this->add_control(
			'premium_pricing_table_slashed_price_value',
			array(
				'label'       => __( 'Slashed Price', 'premium-addons-for-elementor' ),
				'type'        => Controls_Manager::TEXT,
				'dynamic'     => array( 'active' => true ),
				'label_block' => true,
			)
		);

		/*Price Currency*/
		$this->add_control(
			'premium_pricing_table_price_currency',
			array(
				'label'       => __( 'Currency', 'premium-addons-for-elementor' ),
				'type'        => Controls_Manager::TEXT,
				'dynamic'     => array( 'active' => true ),
				'default'     => '$',
				'label_block' => true,
			)
		);

		/*Price Value*/
		$this->add_control(
			'premium_pricing_table_price_value',
			array(
				'label'       => __( 'Price', 'premium-addons-for-elementor' ),
				'type'        => Controls_Manager::TEXT,
				'dynamic'     => array( 'active' => true ),
				'default'     => '25',
				'label_block' => true,
			)
		);

		/*Price Separator*/
		$this->add_control(
			'premium_pricing_table_price_separator',
			array(
				'label'       => __( 'Divider', 'premium-addons-for-elementor' ),
				'type'        => Controls_Manager::TEXT,
				'dynamic'     => array( 'active' => true ),
				'default'     => '/',
				'label_block' => true,
			)
		);

		/*Price Duration*/
		$this->add_control(
			'premium_pricing_table_price_duration',
			array(
				'label'       => __( 'Duration', 'premium-addons-for-elementor' ),
				'type'        => Controls_Manager::TEXT,
				'dynamic'     => array( 'active' => true ),
				'default'     => 'm',
				'label_block' => true,
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'premium_pricing_table_list_section',
			array(
				'label'     => __( 'Feature List', 'premium-addons-for-elementor' ),
				'condition' => array(
					'premium_pricing_table_list_switcher' => 'yes',
				),
			)
		);

		$repeater = new REPEATER();

		$repeater->add_control(
			'premium_pricing_list_item_text',
			array(
				'label'       => __( 'Text', 'premium-addons-for-elementor' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => __( 'Feature Title', 'premium-addons-for-elementor' ),
				'dynamic'     => array( 'active' => true ),
				'label_block' => true,
			)
		);

		$repeater->add_control(
			'icon_type',
			array(
				'label'   => __( 'Icon Type', 'premium-addons-for-elementor' ),
				'type'    => Controls_Manager::SELECT,
				'options' => array(
					'icon'      => __( 'Icon', 'premium-addons-for-elementor' ),
					'image'     => __( 'Image', 'premium-addons-for-elementor' ),
					'animation' => __( 'Lottie Animation', 'premium-addons-for-elementor' ),
					'svg'       => __( 'SVG Code', 'premium-addons-for-elementor' ),
				),
				'default' => 'icon',
			)
		);

		$repeater->add_control(
			'premium_pricing_list_item_icon_updated',
			array(
				'label'            => __( 'Icon', 'premium-addons-for-elementor' ),
				'type'             => Controls_Manager::ICONS,
				'fa4compatibility' => 'premium_pricing_list_item_icon',
				'default'          => array(
					'value'   => 'fas fa-check',
					'library' => 'fa-solid',
				),
				'condition'        => array(
					'icon_type' => 'icon',
				),
			)
		);

		$repeater->add_control(
			'custom_svg',
			array(
				'label'       => __( 'SVG Code', 'premium-addons-for-elementor' ),
				'type'        => Controls_Manager::TEXTAREA,
				'description' => 'You can use these sites to create SVGs: <a href="https://danmarshall.github.io/google-font-to-svg-path/" target="_blank">Google Fonts</a> and <a href="https://boxy-svg.com/" target="_blank">Boxy SVG</a>',
				'condition'   => array(
					'icon_type' => 'svg',
				),

			)
		);

		$repeater->add_control(
			'lottie_url',
			array(
				'label'       => __( 'Animation JSON URL', 'premium-addons-for-elementor' ),
				'type'        => Controls_Manager::TEXT,
				'dynamic'     => array( 'active' => true ),
				'description' => 'Get JSON code URL from <a href="https://lottiefiles.com/" target="_blank">here</a>',
				'label_block' => true,
				'condition'   => array(
					'icon_type' => 'animation',
				),
			)
		);

		$repeater->add_control(
			'draw_svg',
			array(
				'label'     => __( 'Draw Icon', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::SWITCHER,
				'classes'   => $draw_icon ? '' : 'editor-pa-control-disabled',
				'condition' => array(
					'icon_type' => array( 'icon', 'svg' ),
					'premium_pricing_list_item_icon_updated[library]!' => 'svg',
				),
			)
		);

		if ( $draw_icon ) {
			$repeater->add_control(
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
					'condition' => array(
						'icon_type' => array( 'icon', 'svg' ),
					),
					'selectors' => array(
						'{{WRAPPER}} {{CURRENT_ITEM}}.premium-pricing-list-item svg *' => 'stroke-width: {{SIZE}}',
					),
				)
			);

			$repeater->add_control(
				'svg_sync',
				array(
					'label'     => __( 'Draw All Paths Together', 'premium-addons-for-elementor' ),
					'type'      => Controls_Manager::SWITCHER,
					'condition' => array(
						'icon_type' => array( 'icon', 'svg' ),
						'draw_svg'  => 'yes',
					),
				)
			);

			$repeater->add_control(
				'frames',
				array(
					'label'       => __( 'Speed', 'premium-addons-for-elementor' ),
					'type'        => Controls_Manager::NUMBER,
					'description' => __( 'Larger value means longer animation duration.', 'premium-addons-for-elementor' ),
					'default'     => 5,
					'min'         => 1,
					'max'         => 100,
					'condition'   => array(
						'icon_type' => array( 'icon', 'svg' ),
						'draw_svg'  => 'yes',
					),
				)
			);

		} else {

			Helper_Functions::get_draw_svg_notice(
				$repeater,
				'pricing',
				array(
					'icon_type' => array( 'icon', 'svg' ),
					'premium_pricing_list_item_icon_updated[library]!' => 'svg',
				)
			);

		}

		$repeater->add_control(
			'lottie_loop',
			array(
				'label'        => __( 'Loop', 'premium-addons-for-elementor' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'true',
				'default'      => 'true',
				'conditions'   => $animation_conds,
			)
		);

		if ( $draw_icon ) {
			$repeater->add_control(
				'svg_notice',
				array(
					'raw'             => __( 'Loop and Speed options are overriden when Draw SVGs in Sequence option is enabled.', 'premium-addons-for-elementor' ),
					'type'            => Controls_Manager::RAW_HTML,
					'content_classes' => 'elementor-panel-alert elementor-panel-alert-warning',
					'condition'       => array(
						'icon_type' => array( 'icon', 'svg' ),
						'draw_svg'  => 'yes',
					),
				)
			);
		}

		$repeater->add_control(
			'lottie_reverse',
			array(
				'label'        => __( 'Reverse', 'premium-addons-for-elementor' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'true',
				'conditions'   => $animation_conds,
			)
		);

		if ( $draw_icon ) {
			$repeater->add_control(
				'start_point',
				array(
					'label'       => __( 'Start Point (%)', 'premium-addons-for-elementor' ),
					'type'        => Controls_Manager::SLIDER,
					'description' => __( 'Set the point that the SVG should start from.', 'premium-addons-for-elementor' ),
					'default'     => array(
						'unit' => '%',
						'size' => 0,
					),
					'condition'   => array(
						'icon_type'       => array( 'icon', 'svg' ),
						'draw_svg'        => 'yes',
						'lottie_reverse!' => 'true',
					),
				)
			);

			$repeater->add_control(
				'end_point',
				array(
					'label'       => __( 'End Point (%)', 'premium-addons-for-elementor' ),
					'type'        => Controls_Manager::SLIDER,
					'description' => __( 'Set the point that the SVG should end at.', 'premium-addons-for-elementor' ),
					'default'     => array(
						'unit' => '%',
						'size' => 0,
					),
					'condition'   => array(
						'icon_type'      => array( 'icon', 'svg' ),
						'draw_svg'       => 'yes',
						'lottie_reverse' => 'true',
					),
				)
			);

			$repeater->add_control(
				'svg_hover',
				array(
					'label'        => __( 'Only Play on Hover', 'premium-addons-for-elementor' ),
					'type'         => Controls_Manager::SWITCHER,
					'return_value' => 'true',
					'condition'    => array(
						'icon_type' => array( 'icon', 'svg' ),
						'draw_svg'  => 'yes',
					),
				)
			);

			$repeater->add_control(
				'svg_yoyo',
				array(
					'label'     => __( 'Yoyo Effect', 'premium-addons-for-elementor' ),
					'type'      => Controls_Manager::SWITCHER,
					'condition' => array(
						'icon_type'   => array( 'icon', 'svg' ),
						'draw_svg'    => 'yes',
						'lottie_loop' => 'true',
					),
				)
			);
		}

		$repeater->add_control(
			'premium_pricing_list_image',
			array(
				'label'     => __( 'Choose Image', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::MEDIA,
				'default'   => array(
					'url' => Utils::get_placeholder_image_src(),
				),
				'condition' => array(
					'icon_type' => 'image',
				),
			)
		);

		$repeater->add_control(
			'premium_pricing_table_item_tooltip',
			array(
				'label' => __( 'Tooltip', 'premium-addons-for-elementor' ),
				'type'  => Controls_Manager::SWITCHER,
			)
		);

		$repeater->add_control(
			'premium_pricing_table_item_tooltip_text',
			array(
				'label'     => __( 'Tooltip Text', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::TEXTAREA,
				'dynamic'   => array( 'active' => true ),
				'default'   => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit',
				'condition' => array(
					'premium_pricing_table_item_tooltip' => 'yes',
				),
			)
		);

		$repeater->add_control(
			'list_item_icon_color',
			array(
				'label'     => __( 'Icon Color', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} {{CURRENT_ITEM}} .premium-pricing-feature-icon' => 'color: {{VALUE}}',
					'{{WRAPPER}} {{CURRENT_ITEM}}.premium-drawable-icon *, {{WRAPPER}} {{CURRENT_ITEM}}.premium-pricing-list-item svg:not([class*="premium-"])' => 'fill: {{VALUE}} !important',
				),
				'condition' => array(
					'icon_type' => 'icon',
				),
			)
		);

		if ( $draw_icon ) {
			$repeater->add_control(
				'stroke_color',
				array(
					'label'     => __( 'Stroke Color', 'premium-addons-for-elementor' ),
					'type'      => Controls_Manager::COLOR,
					'default'   => '#61CE70',
					'condition' => array(
						'icon_type' => array( 'icon', 'svg' ),
					),
					'selectors' => array(
						'{{WRAPPER}} {{CURRENT_ITEM}}.premium-drawable-icon *, {{WRAPPER}} {{CURRENT_ITEM}}.premium-pricing-list-item svg:not([class*="premium-"])' => 'stroke: {{VALUE}};',
					),
				)
			);

			$repeater->add_control(
				'svg_color',
				array(
					'label'     => __( 'After Draw Fill Color', 'premium-addons-for-elementor' ),
					'type'      => Controls_Manager::COLOR,
					'global'    => false,
					'condition' => array(
						'icon_type' => array( 'icon', 'svg' ),
						'draw_svg'  => 'yes',
					),
				)
			);
		}

		$repeater->add_control(
			'list_item_text_color',
			array(
				'label'     => __( 'Text Color', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} {{CURRENT_ITEM}} .premium-pricing-list-span'  => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'premium_fancy_text_list_items',
			array(
				'label'       => __( 'Features', 'premium-addons-for-elementor' ),
				'type'        => Controls_Manager::REPEATER,
				'default'     => array(
					array(
						'premium_pricing_list_item_icon_updated' => array(
							'value'   => 'fas fa-check',
							'library' => 'fa-solid',
						),
						'premium_pricing_list_item_text' => __( 'List Item #1', 'premium-addons-for-elementor' ),
					),
					array(
						'premium_pricing_list_item_icon_updated' => array(
							'value'   => 'fas fa-check',
							'library' => 'fa-solid',
						),
						'premium_pricing_list_item_text' => __( 'List Item #2', 'premium-addons-for-elementor' ),
					),
					array(
						'premium_pricing_list_item_icon_updated' => array(
							'value'   => 'fas fa-check',
							'library' => 'fa-solid',
						),
						'premium_pricing_list_item_text' => __( 'List Item #3', 'premium-addons-for-elementor' ),
					),
				),
				'fields'      => $repeater->get_controls(),
				'title_field' => '{{{ elementor.helpers.renderIcon( this, premium_pricing_list_item_icon_updated, {}, "i", "panel" ) || \'<i class="{{ premium_pricing_list_item_icon }}" aria-hidden="true"></i>\' }}} {{{ premium_pricing_list_item_text }}}',
			)
		);

		if ( $draw_icon ) {
			$this->add_control(
				'draw_svgs_sequence',
				array(
					'label'        => __( 'Draw SVGs In Sequence', 'premium-addons-for-elementor' ),
					'type'         => Controls_Manager::SWITCHER,
					'prefix_class' => 'pa-svg-draw-seq-',
					'render_type'  => 'template',
				)
			);

			$this->add_control(
				'draw_svgs_loop',
				array(
					'label'        => __( 'Loop', 'premium-addons-for-elementor' ),
					'type'         => Controls_Manager::SWITCHER,
					'prefix_class' => 'pa-svg-draw-loop-',
					'render_type'  => 'template',
					'condition'    => array(
						'draw_svgs_sequence' => 'yes',
					),
				)
			);

			$this->add_control(
				'list_frames',
				array(
					'label'       => __( 'Speed', 'premium-addons-for-elementor' ),
					'type'        => Controls_Manager::NUMBER,
					'description' => __( 'Larger value means longer animation duration.', 'premium-addons-for-elementor' ),
					'default'     => 5,
					'min'         => 1,
					'max'         => 100,
					'condition'   => array(
						'draw_svgs_sequence' => 'yes',
					),
				)
			);

			$this->add_control(
				'list_svg_yoyo',
				array(
					'label'        => __( 'Yoyo Animation', 'premium-addons-for-elementor' ),
					'type'         => Controls_Manager::SWITCHER,
					'prefix_class' => 'pa-svg-draw-yoyo-',
					'render_type'  => 'template',
					'condition'    => array(
						'draw_svgs_sequence' => 'yes',
						'draw_svgs_loop'     => 'yes',
					),
				)
			);
		}

		$this->add_responsive_control(
			'premium_pricing_table_list_align',
			array(
				'label'                => __( 'Alignment', 'premium-addons-for-elementor' ),
				'type'                 => Controls_Manager::CHOOSE,
				'options'              => array(
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
				'prefix_class'         => 'premium-pricing-features-',
				'selectors_dictionary' => array(
					'left'   => 'flex-start',
					'center' => 'center',
					'right'  => 'flex-end',
				),
				'toggle'               => false,
				'selectors'            => array(
					'{{WRAPPER}} .premium-pricing-list .premium-pricing-list-item' => 'justify-content: {{VALUE}}',
				),
				'default'              => 'center',
			)
		);

		$this->end_controls_section();

		/*Description Content Section*/
		$this->start_controls_section(
			'premium_pricing_table_description_section',
			array(
				'label'     => __( 'Description', 'premium-addons-for-elementor' ),
				'condition' => array(
					'premium_pricing_table_description_switcher'  => 'yes',
				),
			)
		);

		$this->add_control(
			'premium_pricing_table_description_text',
			array(
				'label'   => __( 'Description', 'premium-addons-for-elementor' ),
				'type'    => Controls_Manager::WYSIWYG,
				'dynamic' => array( 'active' => true ),
				'default' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit',
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'premium_pricing_table_button_section',
			array(
				'label'     => __( 'Button', 'premium-addons-for-elementor' ),
				'condition' => array(
					'premium_pricing_table_button_switcher'  => 'yes',
				),
			)
		);

		$this->add_control(
			'premium_pricing_table_button_text',
			array(
				'label'       => __( 'Text', 'premium-addons-for-elementor' ),
				'default'     => __( 'Get Started', 'premium-addons-for-elementor' ),
				'type'        => Controls_Manager::TEXT,
				'dynamic'     => array( 'active' => true ),
				'label_block' => true,
			)
		);

		$this->add_control(
			'premium_pricing_table_button_url_type',
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
			'premium_pricing_table_button_link',
			array(
				'label'       => __( 'Link', 'premium-addons-for-elementor' ),
				'type'        => Controls_Manager::URL,
				'dynamic'     => array( 'active' => true ),
				'condition'   => array(
					'premium_pricing_table_button_url_type'     => 'url',
				),
				'label_block' => true,
			)
		);

		$this->add_control(
			'premium_pricing_table_button_link_existing_content',
			array(
				'label'       => __( 'Existing Page', 'premium-addons-for-elementor' ),
				'type'        => Controls_Manager::SELECT2,
				'options'     => $this->getTemplateInstance()->get_all_posts(),
				'condition'   => array(
					'premium_pricing_table_button_url_type'     => 'link',
				),
				'multiple'    => false,
				'label_block' => true,
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'premium_pricing_table_ribbon_section',
			array(
				'label'     => __( 'Ribbon', 'premium-addons-for-elementor' ),
				'condition' => array(
					'premium_pricing_table_badge_switcher' => 'yes',
				),
			)
		);

		$this->add_control(
			'ribbon_type',
			array(
				'label'   => __( 'Type', 'premium-addons-for-elementor' ),
				'type'    => Controls_Manager::SELECT,
				'options' => array(
					'triangle' => __( 'Triangle', 'premium-addons-for-elementor' ),
					'circle'   => __( 'Circle', 'premium-addons-for-elementor' ),
					'stripe'   => __( 'Stripe', 'premium-addons-for-elementor' ),
					'flag'     => __( 'Flag', 'premium-addons-for-elementor' ),
				),
				'default' => 'triangle',
			)
		);

		$this->add_control(
			'premium_pricing_table_badge_text',
			array(
				'label'       => __( 'Text', 'premium-addons-for-elementor' ),
				'default'     => __( 'NEW', 'premium-addons-for-elementor' ),
				'type'        => Controls_Manager::TEXT,
				'dynamic'     => array( 'active' => true ),
				'label_block' => true,
			)
		);

		$this->add_responsive_control(
			'premium_pricing_table_badge_left_size',
			array(
				'label'     => __( 'Size', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::SLIDER,
				'range'     => array(
					'px' => array(
						'min' => 1,
						'max' => 300,
					),
				),
				'selectors' => array(
					'{{WRAPPER}} .premium-badge-triangle.premium-badge-left .corner' => 'border-top-width: {{SIZE}}px; border-bottom-width: {{SIZE}}px; border-right-width: {{SIZE}}px;',
				),
				'condition' => array(
					'ribbon_type'                          => 'triangle',
					'premium_pricing_table_badge_position' => 'left',
				),
			)
		);

		$this->add_responsive_control(
			'premium_pricing_table_badge_right_size',
			array(
				'label'     => __( 'Size', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::SLIDER,
				'range'     => array(
					'px' => array(
						'min' => 1,
						'max' => 300,
					),
				),
				'selectors' => array(
					'{{WRAPPER}} .premium-badge-triangle.premium-badge-right .corner' => 'border-right-width: {{SIZE}}px; border-bottom-width: {{SIZE}}px; border-left-width: {{SIZE}}px;',
				),
				'condition' => array(
					'ribbon_type'                          => 'triangle',
					'premium_pricing_table_badge_position' => 'right',
				),
			)
		);

		$this->add_responsive_control(
			'circle_ribbon_size',
			array(
				'label'     => __( 'Size', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::SLIDER,
				'range'     => array(
					'px' => array(
						'min' => 1,
						'max' => 10,
					),
				),
				'selectors' => array(
					'{{WRAPPER}} .premium-badge-circle' => 'min-width: {{SIZE}}em; min-height: {{SIZE}}em; line-height: {{SIZE}}',
				),
				'condition' => array(
					'ribbon_type' => 'circle',
				),
			)
		);

		if ( ! is_rtl() ) {

			$this->add_control(
				'premium_pricing_table_badge_position',
				array(
					'label'   => __( 'Position', 'premium-addons-for-elementor' ),
					'type'    => Controls_Manager::CHOOSE,
					'toggle'  => false,
					'options' => array(
						'left'  => array(
							'title' => __( 'Left', 'premium-addons-for-elementor' ),
							'icon'  => 'eicon-h-align-left',
						),
						'right' => array(
							'title' => __( 'Right', 'premium-addons-for-elementor' ),
							'icon'  => 'eicon-h-align-right',
						),
					),
					'default' => 'right',
				)
			);

		} else {
			$this->add_control(
				'premium_pricing_table_badge_position',
				array(
					'label'     => __( 'Position', 'premium-addons-for-elementor' ),
					'type'      => Controls_Manager::CHOOSE,
					'toggle'    => false,
					'options'   => array(
						'left'  => array(
							'title' => __( 'Left', 'premium-addons-for-elementor' ),
							'icon'  => 'eicon-h-align-left',
						),
						'right' => array(
							'title' => __( 'Right', 'premium-addons-for-elementor' ),
							'icon'  => 'eicon-h-align-right',
						),
					),
					'default'   => 'right',
					'condition' => array(
						'ribbon_type!' => 'flag',
					),
				)
			);
		}

		$this->add_responsive_control(
			'premium_pricing_table_badge_right_right',
			array(
				'label'      => __( 'Horizontal Offset', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', 'em', '%' ),
				'range'      => array(
					'px' => array(
						'min' => 1,
						'max' => 170,
					),
					'em' => array(
						'min' => 1,
						'max' => 30,
					),
				),
				'selectors'  => array(
					'{{WRAPPER}} .premium-badge-right .corner span' => 'right: {{SIZE}}{{UNIT}}',
					'{{WRAPPER}} .premium-badge-circle' => 'right: {{SIZE}}{{UNIT}}',
				),
				'condition'  => array(
					'ribbon_type!'                         => array( 'stripe', 'flag' ),
					'premium_pricing_table_badge_position' => 'right',
				),
			)
		);

		$this->add_responsive_control(
			'premium_pricing_table_badge_right_left',
			array(
				'label'      => __( 'Horizontal Offset', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', 'em', '%' ),
				'range'      => array(
					'px' => array(
						'min' => 1,
						'max' => 170,
					),
					'em' => array(
						'min' => 1,
						'max' => 30,
					),
				),
				'selectors'  => array(
					'{{WRAPPER}} .premium-badge-left .corner span' => 'left: {{SIZE}}{{UNIT}}',
					'{{WRAPPER}} .premium-badge-circle' => 'left: {{SIZE}}{{UNIT}}',
				),
				'condition'  => array(
					'ribbon_type!'                         => array( 'stripe', 'flag' ),
					'premium_pricing_table_badge_position' => 'left',
				),
			)
		);

		$this->add_responsive_control(
			'premium_pricing_table_badge_right_top',
			array(
				'label'      => __( 'Vertical Offset', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', 'em', '%' ),
				'range'      => array(
					'px' => array(
						'min' => 1,
						'max' => 200,
					),
					'em' => array(
						'min' => 1,
						'max' => 20,
					),
				),
				'condition'  => array(
					'ribbon_type!' => 'stripe',
				),
				'selectors'  => array(
					'{{WRAPPER}} .premium-pricing-badge-container .corner span' => 'top: {{SIZE}}{{UNIT}}',
					'{{WRAPPER}} .premium-badge-circle, {{WRAPPER}} .premium-badge-flag .corner' => 'top: {{SIZE}}{{UNIT}}',
				),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'premium_pricing_table_title',
			array(
				'label' => __( 'Display Options', 'premium-addons-for-elementor' ),
			)
		);

		$this->add_control(
			'premium_pricing_table_icon_switcher',
			array(
				'label' => __( 'Icon', 'premium-addons-for-elementor' ),
				'type'  => Controls_Manager::SWITCHER,
			)
		);

		$this->add_control(
			'premium_pricing_table_title_switcher',
			array(
				'label'   => __( 'Title', 'premium-addons-for-elementor' ),
				'type'    => Controls_Manager::SWITCHER,
				'default' => 'yes',
			)
		);

		$this->add_control(
			'premium_pricing_table_price_switcher',
			array(
				'label'   => __( 'Price', 'premium-addons-for-elementor' ),
				'type'    => Controls_Manager::SWITCHER,
				'default' => 'yes',
			)
		);

		$this->add_control(
			'premium_pricing_table_list_switcher',
			array(
				'label'   => __( 'Features', 'premium-addons-for-elementor' ),
				'type'    => Controls_Manager::SWITCHER,
				'default' => 'yes',
			)
		);

		$this->add_control(
			'premium_pricing_table_description_switcher',
			array(
				'label' => __( 'Description', 'premium-addons-for-elementor' ),
				'type'  => Controls_Manager::SWITCHER,
			)
		);

		$this->add_control(
			'premium_pricing_table_button_switcher',
			array(
				'label'   => __( 'Button', 'premium-addons-for-elementor' ),
				'type'    => Controls_Manager::SWITCHER,
				'default' => 'yes',
			)
		);

		$this->add_control(
			'premium_pricing_table_badge_switcher',
			array(
				'label'   => __( 'Ribbon', 'premium-addons-for-elementor' ),
				'type'    => Controls_Manager::SWITCHER,
				'default' => 'yes',
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'premium_pricing_icon_style_settings',
			array(
				'label'     => __( 'Icon', 'premium-addons-for-elementor' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => array(
					'premium_pricing_table_icon_switcher' => 'yes',
				),
			)
		);

		$this->add_control(
			'premium_pricing_icon_color',
			array(
				'label'     => __( 'Color', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'global'    => array(
					'default' => Global_Colors::COLOR_SECONDARY,
				),
				'selectors' => array(
					'{{WRAPPER}} .premium-pricing-icon-container i'  => 'color: {{VALUE}};',
					'{{WRAPPER}} .premium-pricing-icon-container .premium-drawable-icon *, {{WRAPPER}} .premium-pricing-icon-container svg:not([class*="premium-"])'  => 'fill: {{VALUE}};',
				),
				'condition' => array(
					'icon_type' => 'icon',
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
						'{{WRAPPER}} .premium-pricing-icon-container .premium-drawable-icon *, {{WRAPPER}} .premium-pricing-icon-container svg:not([class*="premium-"])' => 'stroke: {{VALUE}};',
					),
				)
			);

			$this->add_control(
				'svg_color',
				array(
					'label'     => __( 'After Draw Fill Color', 'premium-addons-for-elementor' ),
					'type'      => Controls_Manager::COLOR,
					'global'    => false,
					'condition' => array(
						'icon_type' => array( 'icon', 'svg' ),
						'draw_svg'  => 'yes',
					),
				)
			);
		}

		$this->add_responsive_control(
			'premium_pricing_icon_size',
			array(
				'label'      => __( 'Size', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', '%', 'em' ),
				'default'    => array(
					'size' => 25,
					'unit' => 'px',
				),
				'condition'  => array(
					'icon_type!' => 'svg',
				),
				'selectors'  => array(
					'{{WRAPPER}} .premium-pricing-icon-container i' => 'font-size: {{SIZE}}{{UNIT}}',
					'{{WRAPPER}} .premium-pricing-icon-container svg, {{WRAPPER}} .premium-pricing-icon-container img' => 'width: {{SIZE}}{{UNIT}} !important; height: {{SIZE}}{{UNIT}} !important',
				),
			)
		);

		$this->add_responsive_control(
			'svg_icon_width',
			array(
				'label'      => __( 'Width', 'premium-addons-for-elementor' ),
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
					'icon_type' => 'svg',
				),
				'selectors'  => array(
					'{{WRAPPER}} .premium-pricing-icon-container svg' => 'width: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'svg_icon_height',
			array(
				'label'      => __( 'Height', 'premium-addons-for-elementor' ),
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
					'icon_type' => 'svg',
				),
				'selectors'  => array(
					'{{WRAPPER}} .premium-pricing-icon-container svg' => 'height: {{SIZE}}{{UNIT}}',
				),
			)
		);

		$this->add_control(
			'premium_pricing_icon_back_color',
			array(
				'label'     => __( 'Background Color', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'global'    => array(
					'default' => Global_Colors::COLOR_PRIMARY,
				),
				'selectors' => array(
					'{{WRAPPER}} .premium-pricing-icon-container i, {{WRAPPER}} .premium-pricing-icon, {{WRAPPER}} .premium-pricing-image' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_responsive_control(
			'premium_pricing_icon_inner_padding',
			array(
				'label'      => __( 'Padding', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', 'em' ),
				'default'    => array(
					'size' => 10,
					'unit' => 'px',
				),
				'selectors'  => array(
					'{{WRAPPER}} .premium-pricing-icon-container i, {{WRAPPER}} .premium-pricing-icon, {{WRAPPER}} .premium-pricing-image' => 'padding: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'     => 'premium_pricing_icon_inner_border',
				'selector' => '{{WRAPPER}} .premium-pricing-icon-container i, {{WRAPPER}} .premium-pricing-icon, {{WRAPPER}} .premium-pricing-image',
			)
		);

		$this->add_control(
			'premium_pricing_icon_inner_radius',
			array(
				'label'      => __( 'Border Radius', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', '%', 'em' ),
				'default'    => array(
					'size' => 100,
					'unit' => 'px',
				),
				'selectors'  => array(
					'{{WRAPPER}} .premium-pricing-icon-container i, {{WRAPPER}} .premium-pricing-icon, {{WRAPPER}} .premium-pricing-image' => 'border-radius: {{SIZE}}{{UNIT}};',
				),
				'separator'  => 'after',
			)
		);

		$this->add_control(
			'premium_pricing_icon_container_heading',
			array(
				'label' => __( 'Container', 'premium-addons-for-elementor' ),
				'type'  => Controls_Manager::HEADING,
			)
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			array(
				'name'     => 'premium_pricing_table_icon_background',
				'types'    => array( 'classic', 'gradient' ),
				'selector' => '{{WRAPPER}} .premium-pricing-icon-container',
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'     => 'premium_pricing_icon_border',
				'selector' => '{{WRAPPER}} .premium-pricing-icon-container',
			)
		);

		$this->add_control(
			'premium_pricing_icon_border_radius',
			array(
				'label'      => __( 'Border Radius', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', '%', 'em' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-pricing-icon-container' => 'border-radius: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'premium_pricing_icon_margin',
			array(
				'label'      => __( 'Margin', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'default'    => array(
					'top'    => 50,
					'right'  => 0,
					'bottom' => 20,
					'left'   => 0,
					'unit'   => 'px',
				),
				'selectors'  => array(
					'{{WRAPPER}} .premium-pricing-icon-container' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		/*Icon Padding*/
		$this->add_responsive_control(
			'premium_pricing_icon_padding',
			array(
				'label'      => __( 'Padding', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'default'    => array(
					'top'    => 0,
					'right'  => 0,
					'bottom' => 0,
					'left'   => 0,
					'unit'   => 'px',
				),
				'selectors'  => array(
					'{{WRAPPER}} .premium-pricing-icon-container' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'premium_pricing_title_style_settings',
			array(
				'label'     => __( 'Title', 'premium-addons-for-elementor' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => array(
					'premium_pricing_table_title_switcher' => 'yes',
				),
			)
		);

		$this->add_control(
			'premium_pricing_title_color',
			array(
				'label'     => __( 'Color', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'global'    => array(
					'default' => Global_Colors::COLOR_PRIMARY,
				),
				'selectors' => array(
					'{{WRAPPER}} .premium-pricing-table-title'  => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'title_typo',
				'global'   => array(
					'default' => Global_Typography::TYPOGRAPHY_PRIMARY,
				),
				'selector' => '{{WRAPPER}} .premium-pricing-table-title',
			)
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			array(
				'name'     => 'premium_pricing_table_title_background',
				'types'    => array( 'classic', 'gradient' ),
				'selector' => '{{WRAPPER}} .premium-pricing-table-title',
			)
		);

		$this->add_responsive_control(
			'premium_pricing_title_margin',
			array(
				'label'      => __( 'Margin', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'default'    => array(
					'top'    => 0,
					'right'  => 0,
					'bottom' => 0,
					'left'   => 0,
					'unit'   => 'px',
				),
				'selectors'  => array(
					'{{WRAPPER}} .premium-pricing-table-title' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'premium_pricing_title_padding',
			array(
				'label'      => __( 'Padding', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'default'    => array(
					'top'    => 0,
					'right'  => 0,
					'bottom' => 20,
					'left'   => 0,
					'unit'   => 'px',
				),
				'selectors'  => array(
					'{{WRAPPER}} .premium-pricing-table-title' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'premium_pricing_price_style_settings',
			array(
				'label'     => __( 'Price', 'premium-addons-for-elementor' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => array(
					'premium_pricing_table_price_switcher' => 'yes',
				),
			)
		);

		$this->add_control(
			'premium_pricing_slashed_price_heading',
			array(
				'label' => __( 'Slashed Price', 'premium-addons-for-elementor' ),
				'type'  => Controls_Manager::HEADING,
			)
		);

		$this->add_control(
			'premium_pricing_slashed_price_color',
			array(
				'label'     => __( 'Color', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'global'    => array(
					'default' => Global_Colors::COLOR_PRIMARY,
				),
				'selectors' => array(
					'{{WRAPPER}} .premium-pricing-slashed-price-value'  => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'slashed_price_typo',
				'global'   => array(
					'default' => Global_Typography::TYPOGRAPHY_PRIMARY,
				),
				'selector' => '{{WRAPPER}} .premium-pricing-slashed-price-value',
			)
		);

		$this->add_responsive_control(
			'premium_pricing_slashed_price_margin',
			array(
				'label'      => __( 'Margin', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-pricing--slashed-price-value' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'premium_pricing_currency_heading',
			array(
				'label' => __( 'Currency', 'premium-addons-for-elementor' ),
				'type'  => Controls_Manager::HEADING,
			)
		);

		$this->add_control(
			'premium_pricing_currency_color',
			array(
				'label'     => __( 'Color', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'global'    => array(
					'default' => Global_Colors::COLOR_PRIMARY,
				),
				'selectors' => array(
					'{{WRAPPER}} .premium-pricing-price-currency'  => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'label'    => __( 'Typography', 'premium-addons-for-elementor' ),
				'name'     => 'currency_typo',
				'global'   => array(
					'default' => Global_Typography::TYPOGRAPHY_PRIMARY,
				),
				'selector' => '{{WRAPPER}} .premium-pricing-price-currency',
			)
		);

		$this->add_responsive_control(
			'premium_pricing_currency_align',
			array(
				'label'       => __( 'Vertical Align', 'premium-addons-for-elementor' ),
				'type'        => Controls_Manager::CHOOSE,
				'options'     => array(
					'top'    => array(
						'title' => __( 'Top', 'premium-addons-for-elementor' ),
						'icon'  => 'eicon-arrow-up',
					),
					'unset'  => array(
						'title' => __( 'Unset', 'premium-addons-for-elementor' ),
						'icon'  => 'eicon-text-align-justify',
					),
					'bottom' => array(
						'title' => __( 'Bottom', 'premium-addons-for-elementor' ),
						'icon'  => 'eicon-arrow-down',
					),
				),
				'default'     => 'unset',
				'toggle'      => false,
				'selectors'   => array(
					'{{WRAPPER}} .premium-pricing-price-currency' => 'vertical-align: {{VALUE}};',
				),
				'label_block' => false,
			)
		);

		$this->add_responsive_control(
			'premium_pricing_currency_margin',
			array(
				'label'      => __( 'Margin', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-pricing-price-currency' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					'separator' => 'after',
				),
			)
		);

		$this->add_control(
			'premium_pricing_price_heading',
			array(
				'label' => __( 'Price', 'premium-addons-for-elementor' ),
				'type'  => Controls_Manager::HEADING,
			)
		);

		/*Price Color*/
		$this->add_control(
			'premium_pricing_price_color',
			array(
				'label'     => __( 'Color', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'global'    => array(
					'default' => Global_Colors::COLOR_PRIMARY,
				),
				'selectors' => array(
					'{{WRAPPER}} .premium-pricing-price-value'  => 'color: {{VALUE}};',
				),
				'separator' => 'before',
			)
		);

		/*Price Typo*/
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'label'    => __( 'Typography', 'premium-addons-for-elementor' ),
				'name'     => 'price_typo',
				'global'   => array(
					'default' => Global_Typography::TYPOGRAPHY_PRIMARY,
				),
				'selector' => '{{WRAPPER}} .premium-pricing-price-value',
			)
		);

		$this->add_responsive_control(
			'premium_pricing_price_margin',
			array(
				'label'      => __( 'Margin', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-pricing-price-value' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'premium_pricing_sep_heading',
			array(
				'label' => __( 'Divider', 'premium-addons-for-elementor' ),
				'type'  => Controls_Manager::HEADING,
			)
		);

		/*Separator Color*/
		$this->add_control(
			'premium_pricing_sep_color',
			array(
				'label'     => __( 'Color', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'global'    => array(
					'default' => Global_Colors::COLOR_PRIMARY,
				),
				'selectors' => array(
					'{{WRAPPER}} .premium-pricing-price-separator'  => 'color: {{VALUE}};',
				),
				'separator' => 'before',
			)
		);

		/*Separator Typo*/
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'label'    => __( 'Typography', 'premium-addons-for-elementor' ),
				'name'     => 'separator_typo',
				'global'   => array(
					'default' => Global_Typography::TYPOGRAPHY_PRIMARY,
				),
				'selector' => '{{WRAPPER}} .premium-pricing-price-separator',
			)
		);

		$this->add_responsive_control(
			'premium_pricing_sep_margin',
			array(
				'label'      => __( 'Margin', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'default'    => array(
					'top'    => 0,
					'right'  => 0,
					'bottom' => 20,
					'left'   => -15,
					'unit'   => 'px',
				),
				'selectors'  => array(
					'{{WRAPPER}} .premium-pricing-price-separator' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'premium_pricing_dur_heading',
			array(
				'label' => __( 'Duration', 'premium-addons-for-elementor' ),
				'type'  => Controls_Manager::HEADING,
			)
		);

		/*Duration Color*/
		$this->add_control(
			'premium_pricing_dur_color',
			array(
				'label'     => __( 'Color', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'global'    => array(
					'default' => Global_Colors::COLOR_PRIMARY,
				),
				'selectors' => array(
					'{{WRAPPER}} .premium-pricing-price-duration'  => 'color: {{VALUE}};',
				),
				'separator' => 'before',
			)
		);

		/*Duration Typography*/
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'label'    => __( 'Typography', 'premium-addons-for-elementor' ),
				'name'     => 'duration_typo',
				'global'   => array(
					'default' => Global_Typography::TYPOGRAPHY_PRIMARY,
				),
				'selector' => '{{WRAPPER}} .premium-pricing-price-duration',
			)
		);

		$this->add_responsive_control(
			'premium_pricing_dur_margin',
			array(
				'label'      => __( 'Margin', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-pricing-price-duration' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					'separator' => 'after',
				),
			)
		);

		$this->add_control(
			'premium_pricing_price_container_heading',
			array(
				'label' => __( 'Container', 'premium-addons-for-elementor' ),
				'type'  => Controls_Manager::HEADING,
			)
		);

		/*Price Background*/
		$this->add_group_control(
			Group_Control_Background::get_type(),
			array(
				'name'     => 'premium_pricing_table_price_background',
				'types'    => array( 'classic', 'gradient' ),
				'selector' => '{{WRAPPER}} .premium-pricing-price-container',
			)
		);

		/*Price Margin*/
		$this->add_responsive_control(
			'premium_pricing_price_container_margin',
			array(
				'label'      => __( 'Margin', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'default'    => array(
					'top'    => 16,
					'right'  => 0,
					'bottom' => 16,
					'left'   => 0,
					'unit'   => 'px',
				),
				'selectors'  => array(
					'{{WRAPPER}} .premium-pricing-price-container' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		/*Price Padding*/
		$this->add_responsive_control(
			'premium_pricing_price_padding',
			array(
				'label'      => __( 'Padding', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-pricing-price-container' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'premium_pricing_list_style_settings',
			array(
				'label'     => __( 'Features', 'premium-addons-for-elementor' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => array(
					'premium_pricing_table_list_switcher' => 'yes',
				),
			)
		);

		$this->add_control(
			'premium_pricing_features_text_heading',
			array(
				'label' => __( 'Text', 'premium-addons-for-elementor' ),
				'type'  => Controls_Manager::HEADING,
			)
		);

		$this->add_control(
			'premium_pricing_list_text_color',
			array(
				'label'     => __( 'Color', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'global'    => array(
					'default' => Global_Colors::COLOR_SECONDARY,
				),
				'selectors' => array(
					'{{WRAPPER}} .premium-pricing-list-span'  => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'list_typo',
				'global'   => array(
					'default' => Global_Typography::TYPOGRAPHY_PRIMARY,
				),
				'selector' => '{{WRAPPER}} .premium-pricing-list .premium-pricing-list-span',
			)
		);

		$this->add_control(
			'premium_pricing_features_icon_heading',
			array(
				'label'     => __( 'Icon', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			)
		);

		$this->add_control(
			'premium_pricing_list_icon_color',
			array(
				'label'     => __( 'Color', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'global'    => array(
					'default' => Global_Colors::COLOR_PRIMARY,
				),
				'selectors' => array(
					'{{WRAPPER}} .premium-pricing-feature-icon'  => 'color: {{VALUE}}',
					'{{WRAPPER}} .premium-pricing-list-item .premium-drawable-icon *, {{WRAPPER}} .premium-pricing-list-item svg:not([class*="premium-"])'  => 'fill: {{VALUE}};',
				),
			)
		);

		$this->add_responsive_control(
			'premium_pricing_list_icon_size',
			array(
				'label'     => __( 'Size', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::SLIDER,
				'default'   => array(
					'unit' => 'px',
					'size' => 30,
				),
				'selectors' => array(
					'{{WRAPPER}} .premium-pricing-list i' => 'font-size: {{SIZE}}px',
					'{{WRAPPER}} .premium-pricing-list svg, {{WRAPPER}} .premium-pricing-list img' => 'width: {{SIZE}}px !important; height: {{SIZE}}px !important',
				),
			)
		);

		$features_spacing = is_rtl() ? 'left' : 'right';

		$this->add_responsive_control(
			'premium_pricing_list_icon_spacing',
			array(
				'label'     => __( 'Spacing', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::SLIDER,
				'default'   => array(
					'size' => 5,
				),
				'selectors' => array(
					'{{WRAPPER}} .premium-pricing-feature-icon, {{WRAPPER}} .premium-pricing-list-item > svg' => 'margin-' . $features_spacing . ': {{SIZE}}px',
				),
			)
		);

		$this->add_responsive_control(
			'premium_pricing_list_item_margin',
			array(
				'label'     => __( 'Vertical Spacing', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::SLIDER,
				'selectors' => array(
					'{{WRAPPER}} .premium-pricing-list .premium-pricing-list-item' => 'margin-bottom: {{SIZE}}px;',
				),
				'separator' => 'after',
			)
		);

		$this->add_control(
			'premium_pricing_features_container_heading',
			array(
				'label' => __( 'Container', 'premium-addons-for-elementor' ),
				'type'  => Controls_Manager::HEADING,
			)
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			array(
				'name'     => 'premium_pricing_list_background',
				'types'    => array( 'classic', 'gradient' ),
				'selector' => '{{WRAPPER}} .premium-pricing-list',
			)
		);

		/*List Border*/
		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'     => 'premium_pricing_list_border',
				'selector' => '{{WRAPPER}} .premium-pricing-list',
			)
		);

		/*List Border Radius*/
		$this->add_control(
			'premium_pricing_list_border_radius',
			array(
				'label'      => __( 'Border Radius', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-pricing-list' => 'border-radius: {{SIZE}}{{UNIT}};',
				),
			)
		);

		/*List Margin*/
		$this->add_responsive_control(
			'premium_pricing_list_margin',
			array(
				'label'      => __( 'Margin', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'default'    => array(
					'top'    => 30,
					'right'  => 0,
					'bottom' => 30,
					'left'   => 0,
					'unit'   => 'px',
				),
				'selectors'  => array(
					'{{WRAPPER}} .premium-pricing-list' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		/*List Padding*/
		$this->add_responsive_control(
			'premium_pricing_list_padding',
			array(
				'label'      => __( 'Padding', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-pricing-list' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'tooltips_style',
			array(
				'label'     => __( 'Tooltips', 'premium-addons-for-elementor' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => array(
					'premium_pricing_table_list_switcher' => 'yes',
				),
			)
		);

		$this->add_responsive_control(
			'tooltips_align',
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
					'{{WRAPPER}} .premium-pricing-list-tooltip' => 'text-align: {{VALUE}}',
				),
			)
		);

		$this->add_responsive_control(
			'tooltips_width',
			array(
				'label'     => __( 'Width', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::SLIDER,
				'range'     => array(
					'px' => array(
						'min' => 1,
						'max' => 400,
					),
				),
				'selectors' => array(
					'{{WRAPPER}} .premium-pricing-list-tooltip' => 'min-width: {{SIZE}}px;',
				),
			)
		);

		$this->add_control(
			'tooltips_color',
			array(
				'label'     => __( 'Color', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .premium-pricing-list-tooltip'  => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'tooltips_typo',
				'global'   => array(
					'default' => Global_Typography::TYPOGRAPHY_PRIMARY,
				),
				'selector' => '{{WRAPPER}} .premium-pricing-list-tooltip',
			)
		);

		$this->add_control(
			'tooltips_background_color',
			array(
				'label'     => __( 'Background Color', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .premium-pricing-list-tooltip'  => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'tooltips_border_color',
			array(
				'label'     => __( 'Border Color', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .list-item-tooltip' => 'border-color: {{VALUE}};',
				),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'premium_pricing_description_style_settings',
			array(
				'label'     => __( 'Description', 'premium-addons-for-elementor' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => array(
					'premium_pricing_table_description_switcher'  => 'yes',
				),
			)
		);

		$this->add_control(
			'premium_pricing_desc_text_heading',
			array(
				'label' => __( 'Text', 'premium-addons-for-elementor' ),
				'type'  => Controls_Manager::HEADING,
			)
		);

		$this->add_control(
			'premium_pricing_desc_color',
			array(
				'label'     => __( 'Color', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'global'    => array(
					'default' => Global_Colors::COLOR_SECONDARY,
				),
				'selectors' => array(
					'{{WRAPPER}} .premium-pricing-description-container'  => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'description_typo',
				'global'   => array(
					'default' => Global_Typography::TYPOGRAPHY_PRIMARY,
				),
				'selector' => '{{WRAPPER}} .premium-pricing-description-container',
			)
		);

		$this->add_control(
			'premium_pricing_desc_container_heading',
			array(
				'label' => __( 'Container', 'premium-addons-for-elementor' ),
				'type'  => Controls_Manager::HEADING,
			)
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			array(
				'name'     => 'premium_pricing_table_desc_background',
				'types'    => array( 'classic', 'gradient' ),
				'selector' => '{{WRAPPER}} .premium-pricing-description-container',
			)
		);

		$this->add_responsive_control(
			'premium_pricing_desc_margin',
			array(
				'label'      => __( 'Margin', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'default'    => array(
					'top'    => 16,
					'right'  => 0,
					'bottom' => 16,
					'left'   => 0,
					'unit'   => 'px',
				),
				'selectors'  => array(
					'{{WRAPPER}} .premium-pricing-description-container' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'premium_pricing_desc_padding',
			array(
				'label'      => __( 'Padding', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-pricing-description-container' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'premium_pricing_button_style_settings',
			array(
				'label'     => __( 'Button', 'premium-addons-for-elementor' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => array(
					'premium_pricing_table_button_switcher'  => 'yes',
				),
			)
		);

		$this->add_control(
			'premium_pricing_button_color',
			array(
				'label'     => __( 'Color', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'global'    => array(
					'default' => Global_Colors::COLOR_SECONDARY,
				),
				'selectors' => array(
					'{{WRAPPER}} .premium-pricing-price-button'  => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'premium_pricing_button_hover_color',
			array(
				'label'     => __( 'Hover Text Color', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'global'    => array(
					'default' => Global_Colors::COLOR_SECONDARY,
				),
				'selectors' => array(
					'{{WRAPPER}} .premium-pricing-price-button:hover'  => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'button_typo',
				'global'   => array(
					'default' => Global_Typography::TYPOGRAPHY_PRIMARY,
				),
				'selector' => '{{WRAPPER}} .premium-pricing-price-button',
			)
		);

		$this->start_controls_tabs( 'premium_pricing_table_button_style_tabs' );

		$this->start_controls_tab(
			'premium_pricing_table_button_style_normal',
			array(
				'label' => __( 'Normal', 'premium-addons-for-elementor' ),
			)
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			array(
				'name'     => 'premium_pricing_table_button_background',
				'types'    => array( 'classic', 'gradient' ),
				'selector' => '{{WRAPPER}} .premium-pricing-price-button',
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'     => 'premium_pricing_table_button_border',
				'selector' => '{{WRAPPER}} .premium-pricing-price-button',
			)
		);

		$this->add_control(
			'premium_pricing_table_box_button_radius',
			array(
				'label'      => __( 'Border Radius', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-pricing-price-button' => 'border-radius: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'label'    => __( 'Shadow', 'premium-addons-for-elementor' ),
				'name'     => 'premium_pricing_table_button_box_shadow',
				'selector' => '{{WRAPPER}} .premium-pricing-price-button',
			)
		);

		$this->add_responsive_control(
			'premium_pricing_button_margin',
			array(
				'label'      => __( 'Margin', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-pricing-price-button' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'premium_pricing_button_padding',
			array(
				'label'      => __( 'Padding', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'default'    => array(
					'top'    => 20,
					'right'  => 0,
					'bottom' => 20,
					'left'   => 0,
					'unit'   => 'px',
				),
				'selectors'  => array(
					'{{WRAPPER}} .premium-pricing-price-button' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'premium_pricing_table_button_style_hover',
			array(
				'label' => __( 'Hover', 'premium-addons-for-elementor' ),
			)
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			array(
				'name'     => 'premium_pricing_table_button_background_hover',
				'types'    => array( 'classic', 'gradient' ),
				'selector' => '{{WRAPPER}} .premium-pricing-price-button:hover',
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'     => 'premium_pricing_table_button_border_hover',
				'selector' => '{{WRAPPER}} .premium-pricing-price-button:hover',
			)
		);

		$this->add_control(
			'premium_pricing_table_button_border_radius_hover',
			array(
				'label'      => __( 'Border Radius', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-pricing-price-button:hover' => 'border-radius: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'label'    => __( 'Shadow', 'premium-addons-for-elementor' ),
				'name'     => 'premium_pricing_table_button_shadow_hover',
				'selector' => '{{WRAPPER}} .premium-pricing-price-button:hover',
			)
		);

		$this->add_responsive_control(
			'premium_pricing_button_margin_hover',
			array(
				'label'      => __( 'Margin', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-pricing-price-button:hover' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'premium_pricing_button_padding_hover',
			array(
				'label'      => __( 'Padding', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'default'    => array(
					'top'    => 20,
					'right'  => 0,
					'bottom' => 20,
					'left'   => 0,
					'unit'   => 'px',
				),
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-pricing-price-button:hover' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();

		$this->start_controls_section(
			'premium_pricing_table_badge_style',
			array(
				'label'     => __( 'Ribbon', 'premium-addons-for-elementor' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => array(
					'premium_pricing_table_badge_switcher' => 'yes',
				),
			)
		);

		$this->add_control(
			'premium_pricing_badge_text_color',
			array(
				'label'     => __( 'Text Color', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'global'    => array(
					'default' => Global_Colors::COLOR_SECONDARY,
				),
				'selectors' => array(
					'{{WRAPPER}} .premium-pricing-badge-container .corner span'  => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'badge_text_typo',
				'global'   => array(
					'default' => Global_Typography::TYPOGRAPHY_PRIMARY,
				),
				'selector' => '{{WRAPPER}} .premium-pricing-badge-container .corner span',
			)
		);

		$this->add_control(
			'premium_pricing_badge_left_color',
			array(
				'label'     => __( 'Background Color', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'global'    => array(
					'default' => Global_Colors::COLOR_PRIMARY,
				),
				'selectors' => array(
					'{{WRAPPER}} .premium-badge-triangle.premium-badge-left .corner'  => 'border-top-color: {{VALUE}}',
					'{{WRAPPER}} .premium-badge-triangle.premium-badge-right .corner'  => 'border-right-color: {{VALUE}}',
				),
				'condition' => array(
					'ribbon_type' => 'triangle',
				),
			)
		);

		$this->add_control(
			'ribbon_background',
			array(
				'label'     => __( 'Background Color', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'global'    => array(
					'default' => Global_Colors::COLOR_PRIMARY,
				),
				'selectors' => array(
					'{{WRAPPER}} .premium-badge-circle, {{WRAPPER}} .premium-badge-stripe .corner, {{WRAPPER}} .premium-badge-flag .corner'  => 'background-color: {{VALUE}}',
					'{{WRAPPER}} .premium-badge-flag.premium-badge-right .corner::before'  => 'border-left: 8px solid {{VALUE}}',
					'{{WRAPPER}} .premium-badge-flag.premium-badge-left .corner::after'  => 'border-right: 8px solid {{VALUE}}',
				),
				'condition' => array(
					'ribbon_type!' => 'triangle',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name'      => 'ribbon_shadow',
				'selector'  => '{{WRAPPER}} .premium-badge-circle, {{WRAPPER}} .premium-badge-stripe .corner, {{WRAPPER}} .premium-badge-flag .corner',
				'condition' => array(
					'ribbon_type!' => 'triangle',
				),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'premium_pricing_box_style_settings',
			array(
				'label' => __( 'Box Settings', 'premium-addons-for-elementor' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->start_controls_tabs( 'premium_pricing_table_box_style_tabs' );

		$this->start_controls_tab(
			'premium_pricing_table_box_style_normal',
			array(
				'label' => __( 'Normal', 'premium-addons-for-elementor' ),
			)
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			array(
				'name'     => 'premium_pricing_table_box_background',
				'types'    => array( 'classic', 'gradient' ),
				'selector' => '{{WRAPPER}} .premium-pricing-table-container',
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'     => 'premium_pricing_table_box_border',
				'selector' => '{{WRAPPER}} .premium-pricing-table-container',
			)
		);

		$this->add_control(
			'premium_pricing_table_box_border_radius',
			array(
				'label'      => __( 'Border Radius', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', '%', 'em' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-pricing-table-container' => 'border-radius: {{SIZE}}{{UNIT}};',
				),
				'condition'  => array(
					'table_adv_radius!' => 'yes',
				),
			)
		);

		$this->add_control(
			'table_adv_radius',
			array(
				'label'       => __( 'Advanced Border Radius', 'premium-addons-for-elementor' ),
				'type'        => Controls_Manager::SWITCHER,
				'description' => __( 'Apply custom radius values. Get the radius value from ', 'premium-addons-for-elementor' ) . '<a href="https://9elements.github.io/fancy-border-radius/" target="_blank">here</a>',
			)
		);

		$this->add_control(
			'table_adv_radius_value',
			array(
				'label'     => __( 'Border Radius', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::TEXT,
				'dynamic'   => array( 'active' => true ),
				'selectors' => array(
					'{{WRAPPER}} .premium-pricing-table-container' => 'border-radius: {{VALUE}};',
				),
				'condition' => array(
					'table_adv_radius' => 'yes',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'label'    => __( 'Shadow', 'premium-addons-for-elementor' ),
				'name'     => 'premium_pricing_table_box_shadow',
				'selector' => '{{WRAPPER}} .premium-pricing-table-container',
			)
		);

		$this->add_responsive_control(
			'premium_pricing_box_margin',
			array(
				'label'      => __( 'Margin', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-pricing-table-container' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'premium_pricing_box_padding',
			array(
				'label'      => __( 'Padding', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'default'    => array(
					'top'    => 40,
					'right'  => 0,
					'bottom' => 0,
					'left'   => 0,
					'unit'   => 'px',
				),
				'selectors'  => array(
					'{{WRAPPER}} .premium-pricing-table-container' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'premium_pricing_table_box_style_hover',
			array(
				'label' => __( 'Hover', 'premium-addons-for-elementor' ),
			)
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			array(
				'name'     => 'premium_pricing_table_box_background_hover',
				'types'    => array( 'classic', 'gradient' ),
				'selector' => '{{WRAPPER}} .premium-pricing-table-container:hover',
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'     => 'premium_pricing_table_box_border_hover',
				'selector' => '{{WRAPPER}} .premium-pricing-table-container:hover',
			)
		);

		$this->add_control(
			'premium_pricing_table_box_border_radius_hover',
			array(
				'label'      => __( 'Border Radius', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-pricing-table-container:hover' => 'border-radius: {{SIZE}}{{UNIT}};',
				),
				'condition'  => array(
					'table_hover_adv_radius' => 'yes',
				),
			)
		);

		$this->add_control(
			'table_hover_adv_radius',
			array(
				'label'       => __( 'Advanced Border Radius', 'premium-addons-for-elementor' ),
				'type'        => Controls_Manager::SWITCHER,
				'description' => __( 'Apply custom radius values. Get the radius value from ', 'premium-addons-for-elementor' ) . '<a href="https://9elements.github.io/fancy-border-radius/" target="_blank">here</a>',
			)
		);

		$this->add_control(
			'table_hover_adv_radius_value',
			array(
				'label'     => __( 'Border Radius', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::TEXT,
				'dynamic'   => array( 'active' => true ),
				'selectors' => array(
					'{{WRAPPER}} .premium-pricing-table-container:hover' => 'border-radius: {{VALUE}};',
				),
				'condition' => array(
					'table_hover_adv_radius' => 'yes',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'label'    => __( 'Shadow', 'premium-addons-for-elementor' ),
				'name'     => 'premium_pricing_table_box_shadow_hover',
				'selector' => '{{WRAPPER}} .premium-pricing-table-container:hover',
			)
		);

		$this->add_responsive_control(
			'premium_pricing_box_margin_hover',
			array(
				'label'      => __( 'Margin', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-pricing-table-container:hover' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'premium_pricing_box_padding_hover',
			array(
				'label'      => __( 'Padding', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'default'    => array(
					'top'    => 40,
					'right'  => 0,
					'bottom' => 0,
					'left'   => 0,
					'unit'   => 'px',
				),
				'selectors'  => array(
					'{{WRAPPER}} .premium-pricing-table-container:hover' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();

	}

	/**
	 * Render Pricing Table widget output on the frontend.
	 *
	 * Written in PHP and used to generate the final HTML.
	 *
	 * @since  1.0.0
	 * @access protected
	 */
	protected function render() {

		$settings = $this->get_settings_for_display();

		$this->add_inline_editing_attributes( 'premium_pricing_table_title_text' );
		$this->add_render_attribute( 'premium_pricing_table_title_text', 'class', 'premium-pricing-table-title' );

		$this->add_inline_editing_attributes( 'premium_pricing_table_description_text', 'advanced' );
		$this->add_render_attribute( 'premium_pricing_table_description_text', 'class', 'premium-pricing-description-container' );

		$title_tag = Helper_Functions::validate_html_tag( $settings['premium_pricing_table_title_size'] );

		$link_type = $settings['premium_pricing_table_button_url_type'];

		if ( 'yes' === $settings['premium_pricing_table_badge_switcher'] ) {
			$badge_position = 'premium-badge-' . $settings['premium_pricing_table_badge_position'];

			$badge_style = 'premium-badge-' . $settings['ribbon_type'];

			$this->add_inline_editing_attributes( 'premium_pricing_table_badge_text' );

			// if ( 'premium-badge-flag' === $badge_style ) {
			// $badge_position = '';
			// }
		}

		if ( 'link' === $link_type ) {
			$link_url = get_permalink( $settings['premium_pricing_table_button_link_existing_content'] );
		} elseif ( 'url' === $link_type ) {
			$link_url = $settings['premium_pricing_table_button_link'];
		}

		if ( ! empty( $link_url ) ) {
			$this->add_render_attribute( 'button', 'class', 'premium-pricing-price-button' );
			if ( 'link' === $link_type ) {
				$this->add_render_attribute( 'button', 'href', $link_url );
			} else {
				$this->add_link_attributes( 'button', $link_url );
			}
		}

		if ( 'yes' === $settings['premium_pricing_table_icon_switcher'] ) {
			$icon_type = $settings['icon_type'];

			if ( 'icon' === $icon_type || 'svg' === $icon_type ) {

				$this->add_render_attribute( 'icon', 'class', 'premium-drawable-icon' );

				if ( 'icon' === $icon_type ) {
					if ( ! empty( $settings['premium_pricing_table_icon_selection'] ) ) {
						$this->add_render_attribute(
							'icon',
							array(
								'class'       => $settings['premium_pricing_table_icon_selection'],
								'aria-hidden' => 'true',
							)
						);

					}

					$migrated = isset( $settings['__fa4_migrated']['premium_pricing_table_icon_selection_updated'] );
					$is_new   = empty( $settings['premium_pricing_table_icon_selection'] ) && Icons_Manager::is_migration_allowed();

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

						$this->add_render_attribute( 'icon', 'class', $settings['premium_pricing_table_icon_selection_updated']['value'] );

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
					'pricing_lottie',
					array(
						'class'               => array(
							'premium-pricing-icon',
							'premium-lottie-animation',
						),
						'data-lottie-url'     => $settings['lottie_url'],
						'data-lottie-loop'    => $settings['lottie_loop'],
						'data-lottie-reverse' => $settings['lottie_reverse'],
					)
				);
			} else {
				$this->add_render_attribute(
					'pricing_img',
					array(
						'src' => $settings['premium_pricing_table_image']['url'],
						'alt' => Control_Media::get_image_alt( $settings['premium_pricing_table_image'] ),
					)
				);
			}
		}

		$this->add_render_attribute( 'container', 'class', 'premium-pricing-table-container' );

		$draw_icon = $this->check_icon_draw();
		if ( $draw_icon && 'yes' === $settings['draw_svgs_sequence'] ) {
			$this->add_render_attribute( 'container', 'data-speed', $settings['list_frames'] );
		}

		?>

	<div <?php echo wp_kses_post( $this->get_render_attribute_string( 'container' ) ); ?>>
		<?php if ( 'yes' === $settings['premium_pricing_table_badge_switcher'] ) : ?>
			<div class="premium-pricing-badge-container <?php echo esc_attr( $badge_position . ' ' . $badge_style ); ?>">
				<div class="corner">
					<span <?php echo wp_kses_post( $this->get_render_attribute_string( 'premium_pricing_table_badge_text' ) ); ?>>
						<?php echo wp_kses_post( $settings['premium_pricing_table_badge_text'] ); ?>
					</span>
				</div>
			</div>
			<?php
		endif;
		if ( 'yes' === $settings['premium_pricing_table_icon_switcher'] ) :
			?>
			<div class="premium-pricing-icon-container">
						<?php if ( 'icon' === $icon_type ) : ?>
							<?php
							if ( ( $is_new || $migrated ) && 'yes' !== $settings['draw_svg'] ) :
								Icons_Manager::render_icon(
									$settings['premium_pricing_table_icon_selection_updated'],
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
				<?php elseif ( 'animation' === $icon_type ) : ?>
					<div <?php echo wp_kses_post( $this->get_render_attribute_string( 'pricing_lottie' ) ); ?>></div>
				<?php else : ?>
					<div class='premium-pricing-image'>
						<img <?php echo wp_kses_post( $this->get_render_attribute_string( 'pricing_img' ) ); ?> />
					</div>
				<?php endif; ?>
			</div>
			<?php
		endif;

		if ( 'yes' === $settings['premium_pricing_table_title_switcher'] ) :
			?>
			<<?php echo wp_kses_post( $title_tag ) . ' ' . wp_kses_post( $this->get_render_attribute_string( 'premium_pricing_table_title_text' ) ); ?>>
				<?php echo wp_kses_post( $settings['premium_pricing_table_title_text'] ); ?>
			</<?php echo wp_kses_post( $title_tag ); ?>>
		<?php endif; ?>
		<?php if ( 'yes' === $settings['premium_pricing_table_price_switcher'] ) : ?>
		<div class="premium-pricing-price-container">

			<?php if ( ! empty( $settings['premium_pricing_table_slashed_price_value'] ) ) : ?>
				<strike class="premium-pricing-slashed-price-value">
					<?php echo wp_kses_post( $settings['premium_pricing_table_slashed_price_value'] ); ?>
				</strike>
			<?php endif; ?>

			<?php if ( ! empty( $settings['premium_pricing_table_price_currency'] ) ) : ?>
				<span class="premium-pricing-price-currency">
					<?php echo wp_kses_post( $settings['premium_pricing_table_price_currency'] ); ?>
				</span>
			<?php endif; ?>

			<?php if ( ! empty( $settings['premium_pricing_table_price_value'] ) ) : ?>
				<span class="premium-pricing-price-value">
					<?php echo wp_kses_post( $settings['premium_pricing_table_price_value'] ); ?>
				</span>
			<?php endif; ?>

			<?php if ( ! empty( $settings['premium_pricing_table_price_separator'] ) ) : ?>
				<span class="premium-pricing-price-separator">
					<?php echo wp_kses_post( $settings['premium_pricing_table_price_separator'] ); ?>
				</span>
			<?php endif; ?>

			<?php if ( ! empty( $settings['premium_pricing_table_price_duration'] ) ) : ?>
				<span class="premium-pricing-price-duration">
					<?php echo wp_kses_post( $settings['premium_pricing_table_price_duration'] ); ?>
				</span>
			<?php endif; ?>
		</div>
			<?php
		endif;
		if ( 'yes' === $settings['premium_pricing_table_list_switcher'] ) :
			?>
			<ul class="premium-pricing-list">
						<?php
						foreach ( $settings['premium_fancy_text_list_items'] as $index => $item ) :

							$key = 'pricing_list_item_' . $index;

							if ( 'icon' === $item['icon_type'] ) :
								$icon_migrated = isset( $item['__fa4_migrated']['premium_pricing_list_item_icon_updated'] );
								$icon_new      = empty( $item['premium_pricing_list_item_icon'] ) && Icons_Manager::is_migration_allowed();
							endif;

							$this->add_render_attribute(
								$key,
								array(
									'class' => array(
										'elementor-repeater-item-' . $item['_id'],
										'premium-pricing-list-item',
									),
								)
							);

							if ( 'icon' === $item['icon_type'] || 'svg' === $item['icon_type'] ) {

								$this->add_render_attribute( $key, 'class', 'premium-drawable-icon' );

								if ( 'yes' === $item['draw_svg'] ) {

									$this->add_render_attribute(
										$key,
										array(
											'class' => array( 'premium-svg-drawer', 'elementor-invisible' ),
											'data-svg-reverse' => $item['lottie_reverse'],
											'data-svg-loop' => $item['lottie_loop'],
											'data-svg-hover' => $item['svg_hover'],
											'data-svg-sync' => $item['svg_sync'],
											'data-svg-fill' => $item['svg_color'],
											'data-svg-frames' => $item['frames'],
											'data-svg-yoyo' => $item['svg_yoyo'],
											'data-svg-point' => $item['lottie_reverse'] ? $item['end_point']['size'] : $item['start_point']['size'],
										)
									);

								} else {
									$this->add_render_attribute( $key, 'class', 'premium-svg-nodraw' );
								}
							}

							?>
					<li <?php echo wp_kses_post( $this->get_render_attribute_string( $key ) ); ?>>
							<?php if ( 'icon' === $item['icon_type'] ) : ?>
								<?php

								if ( ( $icon_new || $icon_migrated ) && 'yes' !== $item['draw_svg'] ) :
									Icons_Manager::render_icon(
										$item['premium_pricing_list_item_icon_updated'],
										array(
											'class'       => array( 'premium-pricing-feature-icon', 'premium-svg-nodraw', 'premium-drawable-icon' ),
											'aria-hidden' => 'true',
										)
									);
								else :
									?>
									<i class="premium-pricing-feature-icon <?php echo esc_attr( $item['premium_pricing_list_item_icon_updated']['value'] ); ?>"></i>
								<?php endif; ?>
							<?php elseif ( 'svg' === $item['icon_type'] ) : ?>
								<?php echo $this->print_unescaped_setting( 'custom_svg', 'premium_fancy_text_list_items', $index ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
								<?php
							elseif ( 'animation' === $item['icon_type'] ) :
								$lottie_key = 'pricing_item_lottie_' . $index;
								$this->add_render_attribute(
									$lottie_key,
									array(
										'class'            => array(
											'premium-pricing-feature-icon',
											'premium-lottie-animation',
										),
										'data-lottie-url'  => $item['lottie_url'],
										'data-lottie-loop' => $item['lottie_loop'],
										'data-lottie-reverse' => $item['lottie_reverse'],
									)
								);
								?>
									<div <?php echo wp_kses_post( $this->get_render_attribute_string( $lottie_key ) ); ?>></div>
								<?php
								else :
									$img_key = 'pricing_list_img' . $index;
									$this->add_render_attribute(
										$img_key,
										array(
											'src' => $item['premium_pricing_list_image']['url'],
											'alt' => Control_Media::get_image_alt( $item['premium_pricing_list_image'] ),
										)
									);
									?>
							<div class='premium-pricing-list-image premium-pricing-feature-icon'>
								<img <?php echo wp_kses_post( $this->get_render_attribute_string( $img_key ) ); ?> />
							</div>
								<?php endif; ?>

							<?php
							if ( ! empty( $item['premium_pricing_list_item_text'] ) ) :
								$item_class = 'yes' === $item['premium_pricing_table_item_tooltip'] ? ' list-item-tooltip' : '';
								?>
							<span class="premium-pricing-list-span<?php echo esc_attr( $item_class ); ?>">
								<?php
								echo wp_kses_post( $item['premium_pricing_list_item_text'] );
								if ( 'yes' === $item['premium_pricing_table_item_tooltip'] && ! empty( $item['premium_pricing_table_item_tooltip_text'] ) ) :
									?>
									<span class="premium-pricing-list-tooltip"><?php echo wp_kses_post( $item['premium_pricing_table_item_tooltip_text'] ); ?></span>
								<?php endif; ?>
							</span>
						<?php endif; ?>
					</li>
						<?php endforeach; ?>
			</ul>
		<?php endif; ?>
		<?php if ( 'yes' === $settings['premium_pricing_table_description_switcher'] ) : ?>
			<div <?php echo wp_kses_post( $this->get_render_attribute_string( 'premium_pricing_table_description_text' ) ); ?>>
				<?php echo $this->parse_text_editor( $settings['premium_pricing_table_description_text'] ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
			</div>
		<?php endif; ?>
		<?php if ( 'yes' === $settings['premium_pricing_table_button_switcher'] ) : ?>
		<div class="premium-pricing-button-container">
			<a <?php echo wp_kses_post( $this->get_render_attribute_string( 'button' ) ); ?>>
				<?php echo wp_kses_post( $settings['premium_pricing_table_button_text'] ); ?>
			</a>
		</div>
		<?php endif; ?>
	</div>

		<?php
	}

	/**
	 * Render Pricing Table widget output in the editor.
	 *
	 * Written as a Backbone JavaScript template and used to generate the live preview.
	 *
	 * @since  1.0.0
	 * @access protected
	 */
	protected function content_template() {
		?>
		<#

		view.addInlineEditingAttributes('premium_pricing_table_title_text');
		view.addRenderAttribute( 'premium_pricing_table_title_text', 'class', 'premium-pricing-table-title');

		view.addInlineEditingAttributes('premium_pricing_table_description_text', 'advanced');
		view.addRenderAttribute( 'premium_pricing_table_description_text', 'class', 'premium-pricing-description-container');

		var titleTag = elementor.helpers.validateHTMLTag( settings.premium_pricing_table_title_size ),
			linkType = settings.premium_pricing_table_button_url_type,
			linkURL = 'link' === linkType ? settings.premium_pricing_table_button_link_existing_content : settings.premium_pricing_table_button_link;

		if( 'yes' === settings.premium_pricing_table_icon_switcher ) {

			var iconType = settings.icon_type;

			if( 'icon' === iconType || 'svg' === iconType ) {

				view.addRenderAttribute( 'icon', 'class', 'premium-drawable-icon' );

				if( 'icon' === iconType ) {
					var iconHTML = 'yes' !== settings.draw_svg ? elementor.helpers.renderIcon( view, settings.premium_pricing_table_icon_selection_updated, { 'class': ['premium-svg-nodraw', 'premium-drawable-icon'], 'aria-hidden': true }, 'i' , 'object' ) : false,
						migrated = elementor.helpers.isIconMigrated( settings, 'premium_pricing_table_icon_selection_updated' );
				}

				if ( 'yes' === settings.draw_svg ) {

					view.addRenderAttribute( 'container', 'class', 'premium-drawer-hover' );

					if ( 'icon' === iconType ) {

						view.addRenderAttribute( 'icon', 'class', settings.premium_pricing_table_icon_selection_updated.value );

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

			} else if ( 'animation' === iconType ) {

				view.addRenderAttribute( 'pricing_lottie', {
					'class': [
						'premium-pricing-icon',
						'premium-lottie-animation'
					],
					'data-lottie-url': settings.lottie_url,
					'data-lottie-loop': settings.lottie_loop,
					'data-lottie-reverse': settings.lottie_reverse,
				});

			} else {
				view.addRenderAttribute( 'pricing_img', {
					'src' : settings.premium_pricing_table_image.url,
				});
			}

		}

		if( 'yes' === settings.premium_pricing_table_badge_switcher ) {
			var badgePosition   = 'premium-badge-'  + settings.premium_pricing_table_badge_position,
				badgeStyle      = 'premium-badge-'  + settings.ribbon_type;

			view.addInlineEditingAttributes('premium_pricing_table_badge_text');

		}

		view.addRenderAttribute( 'container', 'class', 'premium-pricing-table-container' );

		if ( 'yes' === settings.draw_svgs_sequence ) {
			view.addRenderAttribute( 'container', 'data-speed', settings.list_frames );
		}

		#>

		<div {{{ view.getRenderAttributeString( 'container' ) }}}>
			<# if('yes' === settings.premium_pricing_table_badge_switcher ) { #>
				<div class="premium-pricing-badge-container {{ badgePosition }} {{ badgeStyle }}">
					<div class="corner"><span {{{ view.getRenderAttributeString('premium_pricing_table_badge_text') }}}>{{{ settings.premium_pricing_table_badge_text }}}</span></div>
				</div>
			<# } #>
			<# if( 'yes' === settings.premium_pricing_table_icon_switcher ) { #>
				<div class="premium-pricing-icon-container">
				<# if( 'icon' === iconType ) { #>
					<# if ( iconHTML && iconHTML.rendered && ( ! settings.premium_pricing_table_icon_selection || migrated ) ) { #>
						{{{ iconHTML.value }}}
					<# } else { #>
						<i {{{ view.getRenderAttributeString( 'icon' ) }}}></i>
					<# } #>
				<# } else if( 'svg' === iconType ) { #>
					<div {{{ view.getRenderAttributeString('icon') }}}>
						{{{ settings.custom_svg }}}
					</div>
				<# } else if( 'animation' === iconType ) { #>
					<div {{{ view.getRenderAttributeString('pricing_lottie') }}}></div>
				<# } else { #>
					<div class="premium-pricing-image">
						<img {{{ view.getRenderAttributeString('pricing_img') }}} />
					</div>
				<# } #>
				</div>
			<# } #>
			<# if('yes' === settings.premium_pricing_table_title_switcher ) { #>
				<{{{titleTag}}} {{{ view.getRenderAttributeString('premium_pricing_table_title_text') }}}>{{{ settings.premium_pricing_table_title_text }}}</{{{titleTag}}}>
			<# } #>

			<# if('yes' === settings.premium_pricing_table_price_switcher ) { #>
				<div class="premium-pricing-price-container">
					<strike class="premium-pricing-slashed-price-value">{{{ settings.premium_pricing_table_slashed_price_value }}}</strike>
					<span class="premium-pricing-price-currency">{{{ settings.premium_pricing_table_price_currency }}}</span>
					<span class="premium-pricing-price-value">{{{ settings.premium_pricing_table_price_value }}}</span>
					<span class="premium-pricing-price-separator">{{{ settings.premium_pricing_table_price_separator }}}</span>
					<span class="premium-pricing-price-duration">{{{ settings.premium_pricing_table_price_duration }}}</span>
				</div>
			<# } #>
			<# if('yes' === settings.premium_pricing_table_list_switcher ) { #>
				<ul class="premium-pricing-list">
					<# _.each( settings.premium_fancy_text_list_items, function( item, index ) {

						var key = 'pricing_list_item_' + index;

						view.addRenderAttribute( key, 'class', [ 'elementor-repeater-item-' + item._id, 'premium-pricing-list-item' ] );

						if( 'icon' === item.icon_type ) {
							var listIconHTML = 'yes' !== item.draw_svg ? elementor.helpers.renderIcon( view, item.premium_pricing_list_item_icon_updated, { 'class': [ 'premium-pricing-feature-icon', 'premium-svg-nodraw', 'premium-drawable-icon' ] , 'aria-hidden': true }, 'i' , 'object' ) : false,
								listIconMigrated = elementor.helpers.isIconMigrated( item, 'premium_pricing_list_item_icon_updated' );
						}


						if ( 'icon' === item.icon_type || 'svg' === item.icon_type ) {

							view.addRenderAttribute( key, 'class', 'premium-drawable-icon' );

							if ( 'yes' === item.draw_svg ) {

								view.addRenderAttribute(
									key,
									{
										'class' : [ 'premium-svg-drawer', 'elementor-invisible' ],
										'data-svg-reverse' : item.lottie_reverse,
										'data-svg-loop' : item.lottie_loop,
										'data-svg-hover' : item.svg_hover,
										'data-svg-sync' : item.svg_sync,
										'data-svg-fill' : item.svg_color,
										'data-svg-frames' : item.frames,
										'data-svg-yoyo' : item.svg_yoyo,
										'data-svg-point' : item.lottie_reverse ? item.end_point.size : item.start_point.size,
									}
								);

							} else {
								view.addRenderAttribute( key, 'class', 'premium-svg-nodraw' );
							}
						}

					#>
						<li {{{ view.getRenderAttributeString( key ) }}}>
							<# if( 'icon' === item.icon_type ) { #>
								<# if ( listIconHTML && listIconHTML.rendered && ( ! item.premium_pricing_list_item_icon || listIconMigrated ) ) { #>
									{{{ listIconHTML.value }}}
								<# } else { #>
									<i class="premium-pricing-feature-icon {{ item.premium_pricing_list_item_icon_updated.value }}" aria-hidden="true"></i>
								<# } #>
							<# } else if( 'svg' === item.icon_type ) { #>
								{{{ item.custom_svg }}}
							<# } else if( 'animation' === item.icon_type ) {
								var lottieKey = 'pricing_item_lottie_' + index;

								view.addRenderAttribute( lottieKey, {
									'class': [
										'premium-pricing-feature-icon',
										'premium-lottie-animation'
									],
									'data-lottie-url': item.lottie_url,
									'data-lottie-loop': item.lottie_loop,
									'data-lottie-reverse': item.lottie_reverse,
								});

							#>
								<div {{{ view.getRenderAttributeString( lottieKey ) }}}></div>
							<# } else {
								var imgKey = 'pricing_list_img' + index;

								view.addRenderAttribute( imgKey, {
									'src' : item.premium_pricing_list_image.url
								});
							#>
								<div class='premium-pricing-list-image premium-pricing-feature-icon'>
									<img  {{{ view.getRenderAttributeString( imgKey ) }}} />
								</div>
							<# } #>

							<# if ( '' !== item.premium_pricing_list_item_text ) {
								var itemClass = 'yes' === item.premium_pricing_table_item_tooltip ? 'list-item-tooltip' : '';
							#>
								<span class="premium-pricing-list-span {{itemClass}}">{{{ item.premium_pricing_list_item_text }}}
								<# if ( 'yes' === item.premium_pricing_table_item_tooltip && '' !== item.premium_pricing_table_item_tooltip_text ) { #>
									<span class="premium-pricing-list-tooltip">{{{ item.premium_pricing_table_item_tooltip_text }}}</span>
								<# } #>
								</span>
							<# } #>
						</li>
					<# } ); #>
				</ul>

			<# } #>
			<# if('yes' === settings.premium_pricing_table_description_switcher ) { #>
				<div {{{ view.getRenderAttributeString('premium_pricing_table_description_text') }}}>
					{{{ settings.premium_pricing_table_description_text }}}
				</div>
			<# } #>
			<# if('yes' === settings.premium_pricing_table_button_switcher ) { #>
				<div class="premium-pricing-button-container">
					<a class="premium-pricing-price-button" target="_{{ settings.premium_pricing_table_button_link_target }}" href="{{ linkURL }}">
						{{{ settings.premium_pricing_table_button_text }}}
					</a>
				</div>
			<# } #>
		</div>
		<?php
	}
}
