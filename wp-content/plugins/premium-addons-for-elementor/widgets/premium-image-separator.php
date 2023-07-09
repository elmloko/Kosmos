<?php
/**
 * Premium Image Separator.
 */

namespace PremiumAddons\Widgets;

// Elementor Classes.
use Elementor\Modules\DynamicTags\Module as TagsModule;
use Elementor\Widget_Base;
use Elementor\Utils;
use Elementor\Control_Media;
use Elementor\Controls_Manager;
use Elementor\Core\Kits\Documents\Tabs\Global_Colors;
use Elementor\Icons_Manager;
use Elementor\Group_Control_Css_Filter;
use Elementor\Group_Control_Text_Shadow;

// PremiumAddons Classes.
use PremiumAddons\Admin\Includes\Admin_Helper;
use PremiumAddons\Includes\Helper_Functions;
use PremiumAddons\Includes\Premium_Template_Tags;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // If this file is called directly, abort.
}

/**
 * Class Premium_Image_Separator
 */
class Premium_Image_Separator extends Widget_Base {

	/**
	 * Check Icon Draw Option.
	 *
	 * @since 4.9.26
	 * @access public
	 */
	public function check_icon_draw() {
		$is_enabled = Admin_Helper::check_svg_draw( 'premium-image-separator' );
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
		return 'premium-addon-image-separator';
	}

	/**
	 * Retrieve Widget Title.
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function get_title() {
		return __( 'Image Separator', 'premium-addons-for-elementor' );
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
	 * Retrieve Widget Icon.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return string widget icon.
	 */
	public function get_icon() {
		return 'pa-image-separator';
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
		return array( 'pa', 'premium', 'divider', 'section', 'shape' );
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
	 * Register Image Controls controls.
	 *
	 * @since 1.0.0
	 * @access protected
	 */
	protected function register_controls() { // phpcs:ignore PSR2.Methods.MethodDeclaration.Underscore

		$draw_icon = $this->check_icon_draw();

		$this->start_controls_section(
			'premium_image_separator_general_settings',
			array(
				'label' => __( 'Image Settings', 'premium-addons-for-elementor' ),
			)
		);

		$this->add_control(
			'separator_type',
			array(
				'label'   => __( 'Separator Type', 'premium-addons-for-elementor' ),
				'type'    => Controls_Manager::SELECT,
				'options' => array(
					'icon'      => __( 'Icon', 'premium-addons-for-elementor' ),
					'image'     => __( 'Image', 'premium-addons-for-elementor' ),
					'animation' => __( 'Lottie Animation', 'premium-addons-for-elementor' ),
					'svg'       => __( 'SVG Code', 'premium-addons-for-elementor' ),
				),
				'default' => 'image',
			)
		);

		$this->add_control(
			'separator_icon',
			array(
				'label'     => __( 'Select an Icon', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::ICONS,
				'default'   => array(
					'value'   => 'fas fa-star',
					'library' => 'fa-solid',
				),
				'condition' => array(
					'separator_type' => 'icon',
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
					'separator_type' => 'svg',
				),
			)
		);

		$this->add_control(
			'premium_image_separator_image',
			array(
				'label'       => __( 'Image', 'premium-addons-for-elementor' ),
				'description' => __( 'Choose the separator image', 'premium-addons-for-elementor' ),
				'type'        => Controls_Manager::MEDIA,
				'dynamic'     => array( 'active' => true ),
				'default'     => array(
					'url' => Utils::get_placeholder_image_src(),
				),
				'label_block' => true,
				'condition'   => array(
					'separator_type' => 'image',
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
					'separator_type' => 'animation',
				),
			)
		);

		$this->add_responsive_control(
			'premium_image_separator_image_size',
			array(
				'label'      => __( 'Width/Size', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', 'em', '%', 'custom' ),
				'default'    => array(
					'unit' => 'px',
					'size' => 200,
				),
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
					'{{WRAPPER}} .premium-image-separator-container img'    => 'width: {{SIZE}}{{UNIT}}',
					'{{WRAPPER}} .premium-image-separator-container i'      => 'font-size: {{SIZE}}{{UNIT}}',
					'{{WRAPPER}} .premium-image-separator-container svg'    => 'width: {{SIZE}}{{UNIT}} !important; height: {{SIZE}}{{UNIT}} !important;',
				),
			)
		);

		$this->add_responsive_control(
			'premium_image_separator_image_height',
			array(
				'label'      => __( 'Height', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', 'em', 'custom' ),
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
					'{{WRAPPER}} .premium-image-separator-container img'    => 'height: {{SIZE}}{{UNIT}} !important',
				),
				'condition'  => array(
					'separator_type' => 'image',
				),
			)
		);

		$this->add_responsive_control(
			'image_fit',
			array(
				'label'     => __( 'Image Fit', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::SELECT,
				'options'   => array(
					'cover'   => __( 'Cover', 'premium-addons-for-elementor' ),
					'fill'    => __( 'Fill', 'premium-addons-for-elementor' ),
					'contain' => __( 'Contain', 'premium-addons-for-elementor' ),
				),
				'default'   => 'fill',
				'selectors' => array(
					'{{WRAPPER}} .premium-image-separator-container img' => 'object-fit: {{VALUE}}',
				),
				'condition' => array(
					'separator_type' => 'image',
				),
			)
		);

		$animation_conds = array(
			'relation' => 'or',
			'terms'    => array(
				array(
					'name'  => 'separator_type',
					'value' => 'animation',
				),
				array(
					'terms' => array(
						array(
							'relation' => 'or',
							'terms'    => array(
								array(
									'name'  => 'separator_type',
									'value' => 'icon',
								),
								array(
									'name'  => 'separator_type',
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

		$this->add_control(
			'draw_svg',
			array(
				'label'     => __( 'Draw Icon', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::SWITCHER,
				'classes'   => $draw_icon ? '' : 'editor-pa-control-disabled',
				'condition' => array(
					'separator_type'           => array( 'icon', 'svg' ),
					'separator_icon[library]!' => 'svg',
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
					'condition' => array(
						'separator_type' => array( 'icon', 'svg' ),
					),
					'selectors' => array(
						'{{WRAPPER}} .premium-image-separator-container svg *' => 'stroke-width: {{SIZE}}',
					),
				)
			);

			$this->add_control(
				'svg_sync',
				array(
					'label'     => __( 'Draw All Paths Together', 'premium-addons-for-elementor' ),
					'type'      => Controls_Manager::SWITCHER,
					'condition' => array(
						'separator_type' => array( 'icon', 'svg' ),
						'draw_svg'       => 'yes',
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
						'separator_type' => array( 'icon', 'svg' ),
						'draw_svg'       => 'yes',
					),
				)
			);
		} else {

			Helper_Functions::get_draw_svg_notice(
				$this,
				'separator',
				array(
					'separator_type'           => array( 'icon', 'svg' ),
					'separator_icon[library]!' => 'svg',
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

		$this->add_control(
			'lottie_hover',
			array(
				'label'        => __( 'Only Play on Hover', 'premium-addons-for-elementor' ),
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
						'separator_type'  => array( 'icon', 'svg' ),
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
						'separator_type' => array( 'icon', 'svg' ),
						'draw_svg'       => 'yes',
						'lottie_reverse' => 'true',
					),

				)
			);

			$this->add_control(
				'svg_yoyo',
				array(
					'label'     => __( 'Yoyo Effect', 'premium-addons-for-elementor' ),
					'type'      => Controls_Manager::SWITCHER,
					'condition' => array(
						'separator_type' => array( 'icon', 'svg' ),
						'draw_svg'       => 'yes',
						'lottie_loop'    => 'true',
					),
				)
			);
		}

		$this->add_responsive_control(
			'premium_image_separator_image_gutter',
			array(
				'label'       => __( 'Gutter (%)', 'premium-addons-for-elementor' ),
				'type'        => Controls_Manager::NUMBER,
				'default'     => -50,
				'separator'   => 'before',
				'description' => __( '-50% is default. Increase to push the image outside or decrease to pull the image inside.', 'premium-addons-for-elementor' ),
				'selectors'   => array(
					'{{WRAPPER}} .premium-image-separator-container' => 'transform: translateY( {{VALUE}}% );',
				),
			)
		);

		$this->add_control(
			'premium_image_separator_image_align',
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
				'toggle'    => false,
				'selectors' => array(
					'{{WRAPPER}} .premium-image-separator-container'   => 'text-align: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'premium_image_separator_link_switcher',
			array(
				'label'       => __( 'Link', 'premium-addons-for-elementor' ),
				'type'        => Controls_Manager::SWITCHER,
				'description' => __( 'Add a custom link or select an existing page link', 'premium-addons-for-elementor' ),
			)
		);

		$this->add_control(
			'premium_image_separator_link_type',
			array(
				'label'       => __( 'Link/URL', 'premium-addons-for-elementor' ),
				'type'        => Controls_Manager::SELECT,
				'options'     => array(
					'url'  => __( 'URL', 'premium-addons-for-elementor' ),
					'link' => __( 'Existing Page', 'premium-addons-for-elementor' ),
				),
				'default'     => 'url',
				'label_block' => true,
				'condition'   => array(
					'premium_image_separator_link_switcher'  => 'yes',
				),
			)
		);

		$this->add_control(
			'premium_image_separator_existing_page',
			array(
				'label'       => __( 'Existing Page', 'premium-addons-for-elementor' ),
				'type'        => Controls_Manager::SELECT2,
				'options'     => $this->getTemplateInstance()->get_all_posts(),
				'multiple'    => false,
				'label_block' => true,
				'condition'   => array(
					'premium_image_separator_link_switcher' => 'yes',
					'premium_image_separator_link_type' => 'link',
				),
			)
		);

		$this->add_control(
			'premium_image_separator_image_link',
			array(
				'label'       => __( 'URL', 'premium-addons-for-elementor' ),
				'type'        => Controls_Manager::URL,
				'dynamic'     => array( 'active' => true ),
				'label_block' => true,
				'condition'   => array(
					'premium_image_separator_link_switcher' => 'yes',
					'premium_image_separator_link_type' => 'url',
				),
			)
		);

		$this->add_control(
			'mask_switcher',
			array(
				'label'     => __( 'Mask Image Shape', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::SWITCHER,
				'condition' => array(
					'separator_type!' => 'icon',
				),
			)
		);

		$this->add_control(
			'mask_image',
			array(
				'label'       => __( 'Mask Image', 'premium-addons-for-elementor' ),
				'type'        => Controls_Manager::MEDIA,
				'description' => __( 'Use PNG image with the shape you want to mask around feature image.', 'premium-addons-for-elementor' ),
				'selectors'   => array(
					'{{WRAPPER}} .premium-image-separator-container img, {{WRAPPER}} .premium-image-separator-container svg' => 'mask-image: url("{{URL}}"); -webkit-mask-image: url("{{URL}}");',
				),
				'condition'   => array(
					'separator_type!' => 'icon',
					'mask_switcher'   => 'yes',
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
					'{{WRAPPER}} .premium-image-separator-container img, .premium-image-separator-container svg' => 'mask-size: {{VALUE}}; -webkit-mask-size: {{VALUE}}',
				),
				'condition' => array(
					'separator_type!' => 'icon',
					'mask_switcher'   => 'yes',
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
					'{{WRAPPER}} .premium-image-separator-container img, .premium-image-separator-container svg' => 'mask-position: {{VALUE}}; -webkit-mask-position: {{VALUE}}',
				),
				'condition' => array(
					'separator_type!' => 'icon',
					'mask_switcher'   => 'yes',
					'mask_size'       => 'cover',
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
					'{{WRAPPER}} .premium-image-separator-container img, .premium-image-separator-container svg' => 'mask-position: {{VALUE}}; -webkit-mask-position: {{VALUE}}',
				),
				'condition' => array(
					'separator_type!' => 'icon',
					'mask_switcher'   => 'yes',
					'mask_size'       => 'contain',
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

		$title = __( 'Getting started »', 'premium-addons-for-elementor' );

		$doc_url = Helper_Functions::get_campaign_link( 'https://premiumaddons.com/docs/image-separator-widget-tutorial/', 'editor-page', 'wp-editor', 'get-support' );

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
			'premium_image_separator_style',
			array(
				'label' => __( 'Separator', 'premium-addons-for-elementor' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_control(
			'svg_color',
			array(
				'label'     => __( 'After Draw Fill Color', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'global'    => false,
				'condition' => array(
					'separator_type' => array( 'icon', 'svg' ),
					'draw_svg'       => 'yes',
				),
			)
		);

		$this->add_control(
			'icon_color',
			array(
				'label'     => __( 'Color', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'global'    => array(
					'default' => Global_Colors::COLOR_PRIMARY,
				),
				'selectors' => array(
					'{{WRAPPER}} .premium-image-separator-container i' => 'color: {{VALUE}}',
					'{{WRAPPER}} .premium-drawable-icon *, {{WRAPPER}} svg:not([class*="premium-"])' => 'fill: {{VALUE}};',
				),
				'condition' => array(
					'separator_type' => array( 'icon', 'svg' ),
					'draw_svg!'      => 'yes',
				),
			)
		);

		$this->add_control(
			'icon_hover_color',
			array(
				'label'     => __( 'Hover Color', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'global'    => array(
					'default' => Global_Colors::COLOR_PRIMARY,
				),
				'selectors' => array(
					'{{WRAPPER}} .premium-image-separator-container i:hover' => 'color: {{VALUE}}',
					'{{WRAPPER}} .premium-drawable-icon:hover *, {{WRAPPER}} svg:not([class*="premium-"]):hover' => 'fill: {{VALUE}};',
				),
				'condition' => array(
					'separator_type' => array( 'icon', 'svg' ),
					'draw_svg!'      => 'yes',
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
						'separator_type' => array( 'icon', 'svg' ),
					),
					'selectors' => array(
						'{{WRAPPER}} .premium-drawable-icon *, {{WRAPPER}} svg:not([class*="premium-"])' => 'stroke: {{VALUE}};',
					),
				)
			);
		}

		$this->add_control(
			'icon_background_color',
			array(
				'label'     => __( 'Background Color', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'global'    => array(
					'default' => Global_Colors::COLOR_SECONDARY,
				),
				'selectors' => array(
					'{{WRAPPER}} .premium-image-separator-container i, {{WRAPPER}} .premium-image-separator-container > svg' => 'background-color: {{VALUE}}',
				),
				'condition' => array(
					'separator_type' => 'icon',
				),
			)
		);

		$this->add_control(
			'icon_hover_background_color',
			array(
				'label'     => __( 'Hover Background Color', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'global'    => array(
					'default' => Global_Colors::COLOR_SECONDARY,
				),
				'selectors' => array(
					'{{WRAPPER}} .premium-image-separator-container i:hover, {{WRAPPER}} .premium-image-separator-container > svg:hover' => 'background-color: {{VALUE}}',
				),
				'condition' => array(
					'separator_type' => 'icon',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Css_Filter::get_type(),
			array(
				'name'      => 'css_filters',
				'selector'  => '{{WRAPPER}} .premium-image-separator-container',
				'condition' => array(
					'separator_type!' => 'icon',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Css_Filter::get_type(),
			array(
				'name'      => 'hover_css_filters',
				'label'     => __( 'Hover CSS Filters', 'premium-addons-for-elementor' ),
				'selector'  => '{{WRAPPER}} .premium-image-separator-container:hover',
				'condition' => array(
					'separator_type!' => 'icon',
				),
			)
		);

		$this->add_responsive_control(
			'separator_border_radius',
			array(
				'label'      => __( 'Border Radius', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-image-separator-container i, {{WRAPPER}} .premium-image-separator-container img' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}',
				),
				'condition'  => array(
					'separator_adv_radius!' => 'yes',
					'separator_type!'       => 'animation',
				),
			)
		);

		$this->add_control(
			'separator_adv_radius',
			array(
				'label'       => __( 'Advanced Border Radius', 'premium-addons-for-elementor' ),
				'type'        => Controls_Manager::SWITCHER,
				'description' => __( 'Apply custom radius values. Get the radius value from ', 'premium-addons-for-elementor' ) . '<a href="https://9elements.github.io/fancy-border-radius/" target="_blank">here</a>',
				'condition'   => array(
					'separator_type!' => 'animation',
				),
			)
		);

		$this->add_control(
			'separator_adv_radius_value',
			array(
				'label'     => __( 'Border Radius', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::TEXT,
				'dynamic'   => array( 'active' => true ),
				'selectors' => array(
					'{{WRAPPER}} .premium-image-separator-container i, {{WRAPPER}} .premium-image-separator-container img' => 'border-radius: {{VALUE}};',
				),
				'condition' => array(
					'separator_adv_radius' => 'yes',
					'separator_type!'      => 'animation',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Text_Shadow::get_type(),
			array(
				'name'      => 'separator_shadow',
				'label'     => __( 'Icon Shadow', 'premium-addons-for-elementor' ),
				'selector'  => '{{WRAPPER}} .premium-image-separator-container i',
				'condition' => array(
					'separator_type' => 'icon',
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
					'{{WRAPPER}} .premium-image-separator-container i, {{WRAPPER}} .premium-image-separator-container svg' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}',
				),
				'condition'  => array(
					'separator_type' => 'icon',
				),
			)
		);

		$this->end_controls_section();

	}

	/**
	 * Render Image Separator widget output on the frontend.
	 *
	 * Written in PHP and used to generate the final HTML.
	 *
	 * @since 1.0.0
	 * @access protected
	 */
	protected function render() {

		$settings = $this->get_settings_for_display();

		$type = $settings['separator_type'];

		if ( 'yes' === $settings['premium_image_separator_link_switcher'] ) {
			$link_type = $settings['premium_image_separator_link_type'];

			if ( 'url' === $link_type ) {
				$this->add_link_attributes( 'link', $settings['premium_image_separator_image_link'] );
			} else {
				$this->add_render_attribute( 'link', 'href', get_permalink( $settings['premium_image_separator_existing_page'] ) );
			}

			$this->add_render_attribute( 'link', 'class', 'premium-image-separator-link' );

		}

		if ( 'image' === $type ) {
			$alt = esc_attr( Control_Media::get_image_alt( $settings['premium_image_separator_image'] ) );
		} elseif ( 'animation' === $type ) {
			$this->add_render_attribute(
				'separator_lottie',
				array(
					'class'               => 'premium-lottie-animation',
					'data-lottie-url'     => $settings['lottie_url'],
					'data-lottie-loop'    => $settings['lottie_loop'],
					'data-lottie-reverse' => $settings['lottie_reverse'],
					'data-lottie-hover'   => $settings['lottie_hover'],
				)
			);
		} else {

			$this->add_render_attribute( 'icon', 'class', 'premium-drawable-icon' );

			if ( 'yes' === $settings['draw_svg'] ) {
				$this->add_render_attribute( 'container', 'class', 'elementor-invisible' );

				if ( 'icon' === $type ) {
					$this->add_render_attribute( 'icon', 'class', $settings['separator_icon']['value'] );
				}

				$this->add_render_attribute(
					'icon',
					array(
						'class'            => 'premium-svg-drawer',
						'data-svg-reverse' => $settings['lottie_reverse'],
						'data-svg-loop'    => $settings['lottie_loop'],
						'data-svg-hover'   => $settings['lottie_hover'],
						'data-svg-sync'    => $settings['svg_sync'],
						'data-svg-fill'    => $settings['svg_color'],
						'data-svg-frames'  => $settings['frames'],
						'data-svg-yoyo'    => $settings['svg_yoyo'],
						'data-svg-point'   => $settings['lottie_reverse'] ? $settings['end_point']['size'] : $settings['start_point']['size'],
					)
				);

			} else {
				$this->add_render_attribute( 'icon', 'class', 'premium-svg-nodraw' );
			}
		}

		$this->add_render_attribute( 'container', 'class', 'premium-image-separator-container' );

		?>

	<div <?php echo wp_kses_post( $this->get_render_attribute_string( 'container' ) ); ?>>
		<?php if ( 'image' === $type ) : ?>
			<img src="<?php echo esc_attr( $settings['premium_image_separator_image']['url'] ); ?>" alt="<?php echo esc_attr( $alt ); ?>">
			<?php
		elseif ( 'icon' === $type ) :
			if ( 'yes' !== $settings['draw_svg'] ) :
				Icons_Manager::render_icon(
					$settings['separator_icon'],
					array(
						'class'       => array( 'premium-svg-nodraw', 'premium-drawable-icon' ),
						'aria-hidden' => 'true',
					)
				);
			else :
				?>
				<i <?php echo wp_kses_post( $this->get_render_attribute_string( 'icon' ) ); ?>></i>
			<?php endif; ?>
		<?php elseif ( 'svg' === $type ) : ?>
			<div <?php echo wp_kses_post( $this->get_render_attribute_string( 'icon' ) ); ?>>
				<?php $this->print_unescaped_setting( 'custom_svg' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
			</div>
		<?php else : ?>
			<div <?php echo wp_kses_post( $this->get_render_attribute_string( 'separator_lottie' ) ); ?>></div>
		<?php endif; ?>

		<?php if ( 'yes' === $settings['premium_image_separator_link_switcher'] ) : ?>
			<a <?php echo wp_kses_post( $this->get_render_attribute_string( 'link' ) ); ?>></a>
		<?php endif; ?>
	</div>
		<?php
	}

	/**
	 * Render Image Separtor widget output in the editor.
	 *
	 * Written as a Backbone JavaScript template and used to generate the live preview.
	 *
	 * @since 1.0.0
	 * @access protected
	 */
	protected function content_template() {
		?>
		<#
			var type        = settings.separator_type,
				linkSwitch  = settings.premium_image_separator_link_switcher;

			if( 'image' === type ) {
				var imgUrl = settings.premium_image_separator_image.url;

			} else if ( 'icon' === type || 'svg' === type ) {

				view.addRenderAttribute( 'icon', 'class', 'premium-drawable-icon' );

				if( 'icon' === type && 'yes' !== settings.draw_svg ) {
					var iconHTML = elementor.helpers.renderIcon( view, settings.separator_icon, { 'class': [ 'premium-svg-nodraw', 'premium-drawable-icon' ], 'aria-hidden': true }, 'i' , 'object' );
				}


				if ( 'yes' === settings.draw_svg ) {

					view.addRenderAttribute( 'container', 'class', 'elementor-invisible' );

					if ( 'icon' === type ) {

						view.addRenderAttribute( 'icon', 'class', settings.separator_icon.value );

					}

					view.addRenderAttribute(
						'icon',
						{
							'class'            : 'premium-svg-drawer',
							'data-svg-reverse' : settings.lottie_reverse,
							'data-svg-loop'    : settings.lottie_loop,
							'data-svg-hover'   : settings.lottie_hover,
							'data-svg-sync'    : settings.svg_sync,
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

				view.addRenderAttribute( 'separator_lottie', {
					'class': 'premium-lottie-animation',
					'data-lottie-url': settings.lottie_url,
					'data-lottie-loop': settings.lottie_loop,
					'data-lottie-reverse': settings.lottie_reverse,
					'data-lottie-hover': settings.lottie_hover
				});

			}

			if( 'yes' === linkSwitch ) {
				var linkType = settings.premium_image_separator_link_type,
					linkUrl = ( 'url' == linkType ) ? settings.premium_image_separator_image_link.url : settings.premium_image_separator_existing_page;

				view.addRenderAttribute( 'link', 'class', 'premium-image-separator-link' );
				view.addRenderAttribute( 'link', 'href', linkUrl );

			}

		#>

		<div class="premium-image-separator-container">
			<# if( 'image' === type ) { #>
				<img alt="image separator" src="{{ imgUrl }}">
			<# } else if( 'icon' === type ) {
				if( 'yes' !== settings.draw_svg ) { #>
					{{{ iconHTML.value }}}
				<# } else { #>
					<i {{{ view.getRenderAttributeString('icon') }}}></i>
				<# }
			} else if( 'svg' === type ) { #>
				<div {{{ view.getRenderAttributeString('icon') }}}>
					{{{ settings.custom_svg }}}
				</div>
			<# } else { #>
				<div {{{ view.getRenderAttributeString('separator_lottie') }}}></div>
			<# }

			if( 'yes' === linkSwitch ) { #>
				<a {{{ view.getRenderAttributeString( 'link' ) }}}></a>
			<# } #>
		</div>

		<?php
	}
}
