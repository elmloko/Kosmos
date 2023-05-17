<?php
/**
 * Premium Counter.
 */

namespace PremiumAddons\Widgets;

// Elementor Classes.
use Elementor\Widget_Base;
use Elementor\Utils;
use Elementor\Icons_Manager;
use Elementor\Control_Media;
use Elementor\Controls_Manager;
use Elementor\Core\Kits\Documents\Tabs\Global_Colors;
use Elementor\Core\Kits\Documents\Tabs\Global_Typography;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Text_Shadow;

// PremiumAddons Classes.
use PremiumAddons\Admin\Includes\Admin_Helper;
use PremiumAddons\Includes\Helper_Functions;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // No access of directly access.
}

/**
 * Class Premium_Counter
 */
class Premium_Counter extends Widget_Base {

	/**
	 * Check Icon Draw Option.
	 *
	 * @since 4.9.26
	 * @access public
	 */
	public function check_icon_draw() {
		$is_enabled = Admin_Helper::check_svg_draw( 'premium-counter' );
		return $is_enabled;
	}

	/**
	 * Retrieve Widget Name.
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function get_name() {
		return 'premium-counter';
	}

	/**
	 * Retrieve Widget Title.
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function get_title() {
		return __( 'Counter', 'premium-addons-for-elementor' );
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
		return 'pa-counter';
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
				'jquery-numerator',
				'elementor-waypoints',
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
		return array( 'pa', 'premium', 'time', 'number' );
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
	 * Register Counter controls.
	 *
	 * @since 1.0.0
	 * @access protected
	 */
	protected function register_controls() { // phpcs:ignore PSR2.Methods.MethodDeclaration.Underscore

		$draw_icon = $this->check_icon_draw();

		$this->start_controls_section(
			'premium_counter_global_settings',
			array(
				'label' => __( 'Counter', 'premium-addons-for-elementor' ),
			)
		);

		$this->add_control(
			'premium_counter_title',
			array(
				'label'       => __( 'Title', 'premium-addons-for-elementor' ),
				'type'        => Controls_Manager::TEXT,
				'dynamic'     => array( 'active' => true ),
				'description' => __( 'Enter title for stats counter block', 'premium-addons-for-elementor' ),
			)
		);

		$this->add_control(
			'premium_counter_desc',
			array(
				'label'       => __( 'Description', 'premium-addons-for-elementor' ),
				'type'        => Controls_Manager::TEXTAREA,
				'rows'        => 2,
				'label_block' => true,
				'condition'   => array(
					'premium_counter_title!' => '',
				),
			)
		);

		$this->add_control(
			'premium_counter_start_value',
			array(
				'label'   => __( 'Starting Number', 'premium-addons-for-elementor' ),
				'type'    => Controls_Manager::NUMBER,
				'dynamic' => array( 'active' => true ),
				'default' => 0,
			)
		);

		$this->add_control(
			'premium_counter_end_value',
			array(
				'label'   => __( 'Final Number', 'premium-addons-for-elementor' ),
				'type'    => Controls_Manager::NUMBER,
				'dynamic' => array( 'active' => true ),
				'default' => 500,
			)
		);

		$this->add_control(
			'premium_counter_t_separator',
			array(
				'label'       => __( 'Thousands Separator', 'premium-addons-for-elementor' ),
				'type'        => Controls_Manager::TEXT,
				'dynamic'     => array( 'active' => true ),
				'description' => __( 'Separator converts 125000 into 125,000', 'premium-addons-for-elementor' ),
				'default'     => ',',
			)
		);

		$this->add_control(
			'premium_counter_d_after',
			array(
				'label'   => __( 'Digits After Decimal Point', 'premium-addons-for-elementor' ),
				'type'    => Controls_Manager::NUMBER,
				'default' => 0,
			)
		);

		$this->add_control(
			'premium_counter_preffix',
			array(
				'label'       => __( 'Value Prefix', 'premium-addons-for-elementor' ),
				'type'        => Controls_Manager::TEXT,
				'dynamic'     => array( 'active' => true ),
				'description' => __( 'Enter prefix for counter value', 'premium-addons-for-elementor' ),
			)
		);

		$this->add_control(
			'premium_counter_suffix',
			array(
				'label'       => __( 'Value Suffix', 'premium-addons-for-elementor' ),
				'type'        => Controls_Manager::TEXT,
				'dynamic'     => array( 'active' => true ),
				'description' => __( 'Enter suffix for counter value', 'premium-addons-for-elementor' ),
			)
		);

		$this->add_control(
			'premium_counter_speed',
			array(
				'label'       => __( 'Counting Time', 'premium-addons-for-elementor' ),
				'type'        => Controls_Manager::NUMBER,
				'description' => __( 'How long should it take to complete the digit?', 'premium-addons-for-elementor' ),
				'default'     => 3,
			)
		);

		$this->add_responsive_control(
			'counter_align',
			array(
				'label'     => __( 'Alignment', 'premium-addons-for-elementor' ),
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
					'{{WRAPPER}} .premium-counter:not(.top)' => 'justify-content: {{VALUE}}',
					'{{WRAPPER}} .premium-counter.top' => 'align-items: {{VALUE}}',
				),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'premium_counter_display_options',
			array(
				'label' => __( 'Display Options', 'premium-addons-for-elementor' ),
			)
		);

		$this->add_control(
			'icon_switcher',
			array(
				'label'   => __( 'Icon', 'premium-addons-for-elementor' ),
				'type'    => Controls_Manager::SWITCHER,
				'default' => 'yes',
			)
		);

		$this->add_control(
			'premium_counter_icon_image',
			array(
				'label'       => __( 'Icon Type', 'premium-addons-for-elementor' ),
				'type'        => Controls_Manager::SELECT,
				'description' => __( 'Use a font awesome icon or upload a custom image', 'premium-addons-for-elementor' ),
				'options'     => array(
					'icon'      => __( 'Icon', 'premium-addons-for-elementor' ),
					'custom'    => __( 'Image', 'premium-addons-for-elementor' ),
					'animation' => __( 'Lottie Animation', 'premium-addons-for-elementor' ),
					'svg'       => __( 'SVG Code', 'premium-addons-for-elementor' ),
				),
				'default'     => 'icon',
				'condition'   => array(
					'icon_switcher' => 'yes',
				),
			)
		);

		$common_conditions = array(
			'icon_switcher' => 'yes',
		);

		$this->add_control(
			'premium_counter_icon_updated',
			array(
				'label'            => __( 'Select an Icon', 'premium-addons-for-elementor' ),
				'type'             => Controls_Manager::ICONS,
				'fa4compatibility' => 'premium_counter_icon',
				'default'          => array(
					'value'   => 'fas fa-clock',
					'library' => 'fa-solid',
				),
				'condition'        => array_merge(
					$common_conditions,
					array(
						'premium_counter_icon_image' => 'icon',
					)
				),
			)
		);

		$this->add_control(
			'premium_counter_image_upload',
			array(
				'label'     => __( 'Upload Image', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::MEDIA,
				'default'   => array(
					'url' => Utils::get_placeholder_image_src(),
				),
				'condition' => array(
					'premium_counter_icon_image' => 'custom',
					'icon_switcher'              => 'yes',
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
						'premium_counter_icon_image' => 'svg',
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
				'condition'   => array(
					'premium_counter_icon_image' => 'animation',
					'icon_switcher'              => 'yes',
				),
			)
		);

		$animation_conds = array(
			'terms' => array(
				array(
					'name'  => 'icon_switcher',
					'value' => 'yes',
				),
				array(
					'relation' => 'or',
					'terms'    => array(
						array(
							'name'  => 'premium_counter_icon_image',
							'value' => 'animation',
						),
						array(
							'terms' => array(
								array(
									'relation' => 'or',
									'terms'    => array(
										array(
											'name'  => 'premium_counter_icon_image',
											'value' => 'icon',
										),
										array(
											'name'  => 'premium_counter_icon_image',
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
						'premium_counter_icon_image' => array( 'icon', 'svg' ),
						'premium_counter_icon_updated[library]!' => 'svg',
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
							'premium_counter_icon_image' => array( 'icon', 'svg' ),
						)
					),
					'selectors' => array(
						'{{WRAPPER}} .premium-counter-area .premium-counter-icon .icon svg *' => 'stroke-width: {{SIZE}}',
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
							'premium_counter_icon_image' => array( 'icon', 'svg' ),
							'draw_svg'                   => 'yes',
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
							'premium_counter_icon_image' => array( 'icon', 'svg' ),
							'draw_svg'                   => 'yes',
						)
					),
				)
			);

		} else {

			Helper_Functions::get_draw_svg_notice(
				$this,
				'counter',
				array_merge(
					$common_conditions,
					array(
						'premium_counter_icon_image' => array( 'icon', 'svg' ),
						'premium_counter_icon_updated[library]!' => 'svg',
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
							'premium_counter_icon_image' => array( 'icon', 'svg' ),
							'draw_svg'                   => 'yes',
							'lottie_reverse!'            => 'true',
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
							'premium_counter_icon_image' => array( 'icon', 'svg' ),
							'draw_svg'                   => 'yes',
							'lottie_reverse'             => 'true',
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
							'premium_counter_icon_image' => array( 'icon', 'svg' ),
							'draw_svg'                   => 'yes',
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
							'premium_counter_icon_image' => array( 'icon', 'svg' ),
							'draw_svg'                   => 'yes',
							'lottie_loop'                => 'true',
						)
					),
				)
			);

		}

		$this->add_control(
			'premium_counter_icon_position',
			array(
				'label'       => __( 'Icon Position', 'premium-addons-for-elementor' ),
				'type'        => Controls_Manager::SELECT,
				'description' => __( 'Choose a position for your icon', 'premium-addons-for-elementor' ),
				'options'     => array(
					'top'   => __( 'Top', 'premium-addons-for-elementor' ),
					'right' => __( 'Right', 'premium-addons-for-elementor' ),
					'left'  => __( 'Left', 'premium-addons-for-elementor' ),
				),
				'default'     => 'top',
				'separator'   => 'after',
				'condition'   => array(
					'icon_switcher' => 'yes',
				),
			)
		);

		$this->add_control(
			'premium_counter_icon_animation',
			array(
				'label'       => __( 'Animations', 'premium-addons-for-elementor' ),
				'type'        => Controls_Manager::ANIMATION,
				'render_type' => 'template',
			)
		);

		$this->add_responsive_control(
			'title_display',
			array(
				'label'     => __( 'Title Display', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::SELECT,
				'options'   => array(
					'row'    => __( 'Row', 'premium-addons-for-elementor' ),
					'column' => __( 'Column', 'premium-addons-for-elementor' ),
				),
				'default'   => 'column',
				'toggle'    => false,
				'selectors' => array(
					'{{WRAPPER}} .premium-init-wrapper' => 'flex-direction: {{VALUE}}',
				),
			)
		);

		$start = is_rtl() ? 'flex-end' : 'flex-start';
		$end   = is_rtl() ? 'flex-start' : 'flex-end';

		$this->add_responsive_control(
			'value_align',
			array(
				'label'     => __( 'Value Alignment', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::CHOOSE,
				'options'   => array(
					$start   => array(
						'title' => __( 'Left', 'premium-addons-for-elementor' ),
						'icon'  => 'eicon-text-align-left',
					),
					'center' => array(
						'title' => __( 'Center', 'premium-addons-for-elementor' ),
						'icon'  => 'eicon-text-align-center',
					),
					$end     => array(
						'title' => __( 'Right', 'premium-addons-for-elementor' ),
						'icon'  => 'eicon-text-align-right',
					),
				),
				'default'   => 'center',
				'toggle'    => false,
				'selectors' => array(
					'{{WRAPPER}} .premium-counter-value-wrap' => 'align-self: {{VALUE}}',
				),
				'condition' => array(
					'title_display' => 'column',
				),
			)
		);

		$this->add_responsive_control(
			'title_align',
			array(
				'label'     => __( 'Title Alignment', 'premium-addons-for-elementor' ),
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
					'{{WRAPPER}} .premium-counter-title' => 'text-align: {{VALUE}};',
				),
				'condition' => array(
					'premium_counter_title!' => '',
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

		$doc1_url = Helper_Functions::get_campaign_link( 'https://premiumaddons.com/docs/counter-widget-tutorial/', 'editor-page', 'wp-editor', 'get-support' );

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
			'premium_counter_icon_style_tab',
			array(
				'label'     => __( 'Icon', 'premium-addons-for-elementor' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => array(
					'icon_switcher' => 'yes',
				),
			)
		);

		$this->add_control(
			'premium_counter_icon_color',
			array(
				'label'     => __( 'Color', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'global'    => array(
					'default' => Global_Colors::COLOR_PRIMARY,
				),
				'selectors' => array(
					'{{WRAPPER}} .premium-counter-area .premium-counter-icon .icon i' => 'color: {{VALUE}}',
					'{{WRAPPER}} .premium-drawable-icon *, {{WRAPPER}} .premium-counter-icon svg:not([class*="premium-"])' => 'fill: {{VALUE}};',
				),
				'condition' => array(
					'premium_counter_icon_image' => array( 'icon', 'svg' ),
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
						'icon_switcher'              => 'yes',
						'premium_counter_icon_image' => array( 'icon', 'svg' ),
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
						'icon_switcher'              => 'yes',
						'premium_counter_icon_image' => array( 'icon', 'svg' ),
						'draw_svg'                   => 'yes',
					),
				)
			);
		}

		$this->add_responsive_control(
			'premium_counter_icon_size',
			array(
				'label'      => __( 'Size', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', 'em', '%' ),
				'default'    => array(
					'size' => 70,
				),
				'range'      => array(
					'px' => array(
						'min' => 10,
						'max' => 200,
					),
					'em' => array(
						'min' => 1,
						'max' => 20,
					),
				),
				'selectors'  => array(
					'{{WRAPPER}} .premium-counter-area .premium-counter-icon .icon' => 'font-size: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .premium-counter-area .premium-counter-icon svg' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}}',
				),
				'condition'  => array(
					'premium_counter_icon_image' => 'icon',
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
					'premium_counter_icon_image' => 'svg',
				),
				'selectors'  => array(
					'{{WRAPPER}} .premium-counter-area .premium-counter-icon svg' => 'width: {{SIZE}}{{UNIT}};',
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
					'premium_counter_icon_image' => 'svg',
				),
				'selectors'  => array(
					'{{WRAPPER}} .premium-counter-area .premium-counter-icon svg' => 'height: {{SIZE}}{{UNIT}}',
				),
			)
		);

		$this->add_responsive_control(
			'premium_counter_image_size',
			array(
				'label'      => __( 'Size', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', 'em', '%' ),
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
				'selectors'  => array(
					'{{WRAPPER}} .premium-counter-area .premium-counter-icon img.custom-image' => 'width: {{SIZE}}{{UNIT}}',
					'{{WRAPPER}} .premium-counter-area .premium-counter-icon svg' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}}',
				),
				'condition'  => array(
					'premium_counter_icon_image!' => array( 'icon', 'svg' ),
				),
			)
		);

		$this->add_control(
			'premium_counter_icon_style',
			array(
				'label'       => __( 'Style', 'premium-addons-for-elementor' ),
				'type'        => Controls_Manager::SELECT,
				'description' => __( 'We are giving you three quick preset if you are in a hurry. Otherwise, create your own with various options', 'premium-addons-for-elementor' ),
				'options'     => array(
					'simple' => __( 'Simple', 'premium-addons-for-elementor' ),
					'circle' => __( 'Circle Background', 'premium-addons-for-elementor' ),
					'square' => __( 'Square Background', 'premium-addons-for-elementor' ),
					'design' => __( 'Custom', 'premium-addons-for-elementor' ),
				),
				'default'     => 'simple',
			)
		);

		$this->add_control(
			'premium_counter_icon_bg',
			array(
				'label'     => __( 'Background Color', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'global'    => array(
					'default' => Global_Colors::COLOR_SECONDARY,
				),
				'condition' => array(
					'premium_counter_icon_style!' => 'simple',
				),
				'selectors' => array(
					'{{WRAPPER}} .premium-counter-area .premium-counter-icon .icon-bg' => 'background: {{VALUE}};',
				),
			)
		);

		$this->add_responsive_control(
			'premium_counter_icon_bg_size',
			array(
				'label'     => __( 'Background size', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::SLIDER,
				'default'   => array(
					'size' => 150,
				),
				'range'     => array(
					'px' => array(
						'min' => 1,
						'max' => 600,
					),
				),
				'condition' => array(
					'premium_counter_icon_style!' => 'simple',
				),
				'selectors' => array(
					'{{WRAPPER}} .premium-counter-area .premium-counter-icon span.icon' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'premium_counter_icon_v_align',
			array(
				'label'     => __( 'Vertical Alignment', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::SLIDER,
				'default'   => array(
					'size' => 150,
				),
				'range'     => array(
					'px' => array(
						'min' => 1,
						'max' => 600,
					),
				),
				'condition' => array(
					'premium_counter_icon_style!' => 'simple',
				),
				'selectors' => array(
					'{{WRAPPER}} .premium-counter-area .premium-counter-icon span.icon' => 'line-height: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'      => 'premium_icon_border',
				'selector'  => '{{WRAPPER}} .premium-counter-area .premium-counter-icon .design',
				'condition' => array(
					'premium_counter_icon_style' => 'design',
				),
			)
		);

		$this->add_control(
			'premium_icon_border_radius',
			array(
				'label'      => __( 'Border Radius', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', '%', 'em' ),
				'default'    => array(
					'unit' => 'px',
					'size' => 0,
				),
				'selectors'  => array(
					'{{WRAPPER}} .premium-counter-area .premium-counter-icon .design' => 'border-radius: {{SIZE}}{{UNIT}};',
				),
				'condition'  => array(
					'premium_counter_icon_style' => 'design',
					'icon_adv_radius!'           => 'yes',
				),
			)
		);

		$this->add_control(
			'icon_adv_radius',
			array(
				'label'       => __( 'Advanced Border Radius', 'premium-addons-for-elementor' ),
				'type'        => Controls_Manager::SWITCHER,
				'description' => __( 'Apply custom radius values. Get the radius value from ', 'premium-addons-for-elementor' ) . '<a href="https://9elements.github.io/fancy-border-radius/" target="_blank">here</a>',
				'condition'   => array(
					'premium_counter_icon_style' => 'design',
				),
			)
		);

		$this->add_control(
			'icon_adv_radius_value',
			array(
				'label'     => __( 'Border Radius', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::TEXT,
				'dynamic'   => array( 'active' => true ),
				'selectors' => array(
					'{{WRAPPER}} .premium-counter-area .premium-counter-icon .design' => 'border-radius: {{VALUE}};',
				),
				'condition' => array(
					'premium_counter_icon_style' => 'design',
					'icon_adv_radius'            => 'yes',
				),
			)
		);

		$this->add_responsive_control(
			'icon_margin',
			array(
				'label'      => __( 'Margin', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-counter-icon' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'premium_counter_title_style',
			array(
				'label' => __( 'Title & Description', 'premium-addons-for-elementor' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_control(
			'premium_counter_title_color',
			array(
				'label'     => __( 'Title Color', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'global'    => array(
					'default' => Global_Colors::COLOR_PRIMARY,
				),
				'selectors' => array(
					'{{WRAPPER}} .premium-counter-title .premium-counter-title-val' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'premium_counter_title_typho',
				'label'    => __( 'Title Typography', 'premium-addons-for-elementor' ),
				'global'   => array(
					'default' => Global_Typography::TYPOGRAPHY_PRIMARY,
				),
				'selector' => '{{WRAPPER}} .premium-counter-title .premium-counter-title-val',
			)
		);

		$this->add_control(
			'counter_desc_color',
			array(
				'label'     => __( 'Description Color', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'separator' => 'before',
				'global'    => array(
					'default' => Global_Colors::COLOR_SECONDARY,
				),
				'selectors' => array(
					'{{WRAPPER}} .premium-counter-desc' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'counter_desc_typo',
				'label'    => __( 'Description Typography', 'premium-addons-for-elementor' ),
				'global'   => array(
					'default' => Global_Typography::TYPOGRAPHY_SECONDARY,
				),
				'selector' => '{{WRAPPER}} .premium-counter-desc',
			)
		);

		$this->add_control(
			'title_background',
			array(
				'label'     => __( 'Background Color', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'separator' => 'before',
				'selectors' => array(
					'{{WRAPPER}} .premium-counter-title' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'     => 'title_border',
				'selector' => '{{WRAPPER}} .premium-counter-title',
			)
		);

		$this->add_control(
			'title_border_radius',
			array(
				'label'      => __( 'Border Radius', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', '%', 'em' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-counter-title' => 'border-radius: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Text_Shadow::get_type(),
			array(
				'name'     => 'premium_counter_title_shadow',
				'selector' => '{{WRAPPER}} .premium-counter-area .premium-counter-title',
			)
		);

		$this->add_responsive_control(
			'title_margin',
			array(
				'label'      => __( 'Margin', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-counter-title' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'title_padding',
			array(
				'label'      => __( 'Padding', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-counter-title' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'premium_counter_value_style',
			array(
				'label' => __( 'Value', 'premium-addons-for-elementor' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_control(
			'premium_counter_value_color',
			array(
				'label'     => __( 'Color', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'global'    => array(
					'default' => Global_Colors::COLOR_PRIMARY,
				),
				'selectors' => array(
					'{{WRAPPER}} .premium-counter-area .premium-counter-init' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'      => 'premium_counter_value_typho',
				'global'    => array(
					'default' => Global_Typography::TYPOGRAPHY_PRIMARY,
				),
				'selector'  => '{{WRAPPER}} .premium-counter-area .premium-counter-init',
				'separator' => 'after',
			)
		);

		$this->add_control(
			'value_background',
			array(
				'label'     => __( 'Background Color', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .premium-counter-init' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'     => 'value_border',
				'selector' => '{{WRAPPER}} .premium-counter-init',
			)
		);

		$this->add_control(
			'value_border_radius',
			array(
				'label'      => __( 'Border Radius', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', '%', 'em' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-counter-init' => 'border-radius: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'value_margin',
			array(
				'label'      => __( 'Margin', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-counter-init' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'value_padding',
			array(
				'label'      => __( 'Padding', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-counter-init' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'premium_counter_suffix_prefix_style',
			array(
				'label' => __( 'Prefix & Suffix', 'premium-addons-for-elementor' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_control(
			'premium_counter_prefix_color',
			array(
				'label'     => __( 'Prefix Color', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'global'    => array(
					'default' => Global_Colors::COLOR_PRIMARY,
				),
				'selectors' => array(
					'{{WRAPPER}} .premium-counter-area span#prefix' => 'color: {{VALUE}}',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'      => 'premium_counter_prefix_typo',
				'global'    => array(
					'default' => Global_Typography::TYPOGRAPHY_PRIMARY,
				),
				'selector'  => '{{WRAPPER}} .premium-counter-area span#prefix',
				'separator' => 'after',
			)
		);

		$this->add_control(
			'premium_counter_suffix_color',
			array(
				'label'     => __( 'Suffix Color', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'global'    => array(
					'default' => Global_Colors::COLOR_PRIMARY,
				),
				'selectors' => array(
					'{{WRAPPER}} .premium-counter-area span#suffix' => 'color: {{VALUE}}',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'      => 'premium_counter_suffix_typo',
				'global'    => array(
					'default' => Global_Typography::TYPOGRAPHY_PRIMARY,
				),
				'selector'  => '{{WRAPPER}} .premium-counter-area span#suffix',
				'separator' => 'after',
			)
		);

		$this->end_controls_section();

	}

	/**
	 * Render Counter Content
	 *
	 * Used to render the HTML markup for counter strings
	 *
	 * @since 0.0.1
	 * @access public
	 */
	public function render_counter_content() {

		$settings = $this->get_settings_for_display();

		$start_value = $settings['premium_counter_start_value'];

		$this->add_render_attribute( 'content_wrapper', 'class', array( 'premium-init-wrapper', $settings['title_display'] ) );

		?>

		<div <?php echo wp_kses_post( $this->get_render_attribute_string( 'content_wrapper' ) ); ?>>

			<div class="premium-counter-value-wrap">
				<?php if ( ! empty( $settings['premium_counter_preffix'] ) ) : ?>
					<span id="prefix" class="counter-su-pre"><?php echo wp_kses_post( $settings['premium_counter_preffix'] ); ?></span>
				<?php endif; ?>

				<span class="premium-counter-init" id="counter-<?php echo esc_attr( $this->get_id() ); ?>"><?php echo wp_kses_post( $start_value ); ?></span>

				<?php if ( ! empty( $settings['premium_counter_suffix'] ) ) : ?>
					<span id="suffix" class="counter-su-pre"><?php echo wp_kses_post( $settings['premium_counter_suffix'] ); ?></span>
				<?php endif; ?>
			</div>

			<?php if ( ! empty( $settings['premium_counter_title'] ) ) : ?>
				<div class="premium-counter-title">
					<p <?php echo wp_kses_post( $this->get_render_attribute_string( 'premium_counter_title' ) ); ?>>
						<?php echo wp_kses_post( $settings['premium_counter_title'] ); ?>
					</p>
					<?php if ( ! empty( $settings['premium_counter_desc'] ) ) : ?>
						<p class="premium-counter-desc"> <?php echo wp_kses_post( $settings['premium_counter_desc'] ); ?></p>
					<?php endif; ?>
				</div>
			<?php endif; ?>
		</div>

		<?php
	}

	/**
	 * Render Counter Icon
	 *
	 * Used to render the HTML markup for the counter icon
	 *
	 * @since 0.0.1
	 * @access public
	 */
	public function render_counter_icon() {

		$settings = $this->get_settings_for_display();

		$icon_style = 'simple' !== $settings['premium_counter_icon_style'] ? ' icon-bg ' . $settings['premium_counter_icon_style'] : '';

		$animation = $settings['premium_counter_icon_animation'];

		$icon_type = $settings['premium_counter_icon_image'];

		$flex_width = '';

		if ( 'icon' === $icon_type || 'svg' === $icon_type ) {

			$this->add_render_attribute( 'icon', 'class', 'premium-drawable-icon' );

			if ( 'icon' === $icon_type ) {

				if ( ! empty( $settings['premium_counter_icon'] ) ) {
					$this->add_render_attribute(
						'icon',
						array(
							'class'       => $settings['premium_counter_icon'],
							'aria-hidden' => 'true',
						)
					);

				}

				$migrated = isset( $settings['__fa4_migrated']['premium_counter_icon_updated'] );
				$is_new   = empty( $settings['premium_counter_icon'] ) && Icons_Manager::is_migration_allowed();

			}

			if ( 'yes' === $settings['draw_svg'] ) {

				if ( 'icon' === $icon_type ) {

					$this->add_render_attribute( 'icon', 'class', $settings['premium_counter_icon_updated']['value'] );

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
		} elseif ( 'custom' === $icon_type ) {
			$alt = esc_attr( Control_Media::get_image_alt( $settings['premium_counter_image_upload'] ) );

			$this->add_render_attribute(
				'image',
				array(
					'class' => 'custom-image',
					'src'   => $settings['premium_counter_image_upload']['url'],
					'alt'   => $alt,
				)
			);

			if ( 'simple' === $settings['premium_counter_icon_style'] ) {
				$flex_width = ' flex-width ';
			}
		} else {

			$this->add_render_attribute(
				'counter_lottie',
				array(
					'class'               => array(
						'premium-counter-animation',
						'premium-lottie-animation',
					),
					'data-lottie-url'     => $settings['lottie_url'],
					'data-lottie-loop'    => $settings['lottie_loop'],
					'data-lottie-reverse' => $settings['lottie_reverse'],
				)
			);

		}

		?>

		<div class="premium-counter-icon">

			<span class="icon<?php echo esc_attr( $flex_width ); ?><?php echo esc_attr( $icon_style ); ?>" data-animation="<?php echo esc_attr( $animation ); ?>">

				<?php if ( 'icon' === $icon_type && ( ! empty( $settings['premium_counter_icon_updated']['value'] ) || ! empty( $settings['premium_counter_icon'] ) ) ) : ?>
					<?php
					if ( ( $is_new || $migrated ) && 'yes' !== $settings['draw_svg'] ) :
						Icons_Manager::render_icon(
							$settings['premium_counter_icon_updated'],
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
				<?php elseif ( 'custom' === $icon_type && ! empty( $settings['premium_counter_image_upload']['url'] ) ) : ?>
					<img <?php echo wp_kses_post( $this->get_render_attribute_string( 'image' ) ); ?>>
				<?php else : ?>
					<div <?php echo wp_kses_post( $this->get_render_attribute_string( 'counter_lottie' ) ); ?>></div>
				<?php endif; ?>

			</span>
		</div>

		<?php
	}

	/**
	 * Render Counter widget output on the frontend.
	 *
	 * Written in PHP and used to generate the final HTML.
	 *
	 * @since 1.0.0
	 * @access protected
	 */
	protected function render() {

		$settings = $this->get_settings_for_display();

		$this->add_inline_editing_attributes( 'premium_counter_title' );
		$this->add_render_attribute( 'premium_counter_title', 'class', 'premium-counter-title-val' );

		$position = $settings['premium_counter_icon_position'];

		$this->add_render_attribute(
			'counter',
			array(
				'class'           => array( 'premium-counter', 'premium-counter-area', $position ),
				'data-duration'   => $settings['premium_counter_speed'] * 1000,
				'data-from-value' => $settings['premium_counter_start_value'],
				'data-to-value'   => $settings['premium_counter_end_value'],
				'data-delimiter'  => $settings['premium_counter_t_separator'],
				'data-rounding'   => empty( $settings['premium_counter_d_after'] ) ? 0 : $settings['premium_counter_d_after'],
			)
		);

		if ( 'yes' === $settings['draw_svg'] ) {

			$this->add_render_attribute(
				'counter',
				'class',
				array(
					'elementor-invisible',
					'premium-drawer-hover',
				)
			);

		}

		?>

		<div <?php echo wp_kses_post( $this->get_render_attribute_string( 'counter' ) ); ?>>
			<?php
			if ( 'yes' === $settings['icon_switcher'] ) {
				$this->render_counter_icon();
			}
				$this->render_counter_content();
			?>
		</div>

		<?php
	}

	/**
	 * Render Counter widget output in the editor.
	 *
	 * Written as a Backbone JavaScript template and used to generate the live preview.
	 *
	 * @since 1.0.0
	 * @access protected
	 */
	protected function content_template() {
		?>
		<#

			var iconImage,
				position;

			view.addInlineEditingAttributes('title');
			view.addRenderAttribute('title', 'class', 'premium-counter-title-val');

			position = settings.premium_counter_icon_position;

			var delimiter = settings.premium_counter_t_separator,
				round     = '' === settings.premium_counter_d_after ? 0 : settings.premium_counter_d_after;

			view.addRenderAttribute( 'counter', {
				'class': [ 'premium-counter', 'premium-counter-area', position ],
				'data-duration': settings.premium_counter_speed * 1000,
				'data-from-value': settings.premium_counter_start_value,
				'data-to-value': settings.premium_counter_end_value,
				'data-delimiter': delimiter,
				'data-rounding': round
			});

			if ( 'yes' === settings.draw_svg ) {

				view.addRenderAttribute( 'counter', 'class', 'premium-drawer-hover' );

			}

			function getCounterContent() {

				var startValue = settings.premium_counter_start_value;

				view.addRenderAttribute( 'counter_wrap', 'class', ['premium-init-wrapper', settings.title_display ]);

				view.addRenderAttribute( 'value', 'id', 'counter-' + view.getID() );

				view.addRenderAttribute( 'value', 'class', 'premium-counter-init' );

			#>

				<div {{{ view.getRenderAttributeString('counter_wrap') }}}>

					<div class="premium-counter-value-wrap">
						<# if ( '' !== settings.premium_counter_preffix ) { #>
							<span id="prefix" class="counter-su-pre">{{{ settings.premium_counter_preffix }}}</span>
						<# } #>

						<span {{{ view.getRenderAttributeString('value') }}}>{{{ startValue }}}</span>

						<# if ( '' !== settings.premium_counter_suffix ) { #>
							<span id="suffix" class="counter-su-pre">{{{ settings.premium_counter_suffix }}}</span>
						<# } #>
					</div>

					<# if ( '' !== settings.premium_counter_title ) { #>
						<div class="premium-counter-title">
							<p {{{ view.getRenderAttributeString('title') }}}>
								{{{ settings.premium_counter_title }}}
							</p>
							<# if ( '' !== settings.premium_counter_desc ) { #>
								<p class="premium-counter-desc"> {{{ settings.premium_counter_desc }}}</p>
							<# } #>
						</div>
					<# } #>
				</div>

			<#
			}

			function renderCounterIcon() {

				var iconStyle = 'simple' !== settings.premium_counter_icon_style ? ' icon-bg ' + settings.premium_counter_icon_style : '',
					animation = settings.premium_counter_icon_animation,
					flexWidth = '';

				var iconType = settings.premium_counter_icon_image;

				if( 'icon' === iconType || 'svg' === iconType ) {

					view.addRenderAttribute( 'icon', 'class', 'premium-drawable-icon' );

					if( 'icon' === iconType ) {

						var iconHTML = 'yes' !== settings.draw_svg ? elementor.helpers.renderIcon( view, settings.premium_counter_icon_updated, { 'class': ['premium-svg-nodraw', 'premium-drawable-icon'], 'aria-hidden': true }, 'i' , 'object' ) : false,
							migrated = elementor.helpers.isIconMigrated( settings, 'premium_counter_icon_updated' );

					}

					if ( 'yes' === settings.draw_svg ) {

						if ( 'icon' === iconType ) {

							view.addRenderAttribute( 'icon', 'class', settings.premium_counter_icon_updated.value );

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

				} else if( 'custom' === iconType ) {

					if( 'simple' ===  settings.premium_counter_icon_style ) {
						flexWidth = ' flex-width ';
					}

				} else {

					view.addRenderAttribute( 'counter_lottie', {
						'class': [
							'premium-counter-animation',
							'premium-lottie-animation'
						],
						'data-lottie-url': settings.lottie_url,
						'data-lottie-loop': settings.lottie_loop,
						'data-lottie-reverse': settings.lottie_reverse
					});

				}

				view.addRenderAttribute( 'icon_wrap', 'class', 'premium-counter-icon' );

				var iconClass = 'icon' + flexWidth + iconStyle;

			#>

			<div {{{ view.getRenderAttributeString('icon_wrap') }}}>
				<span data-animation="{{ animation }}" class="{{ iconClass }}">
					<# if( 'icon' === iconType ) {
						if ( iconHTML && iconHTML.rendered && ( ! settings.premium_counter_icon || migrated ) ) { #>
							{{{ iconHTML.value }}}
						<# } else { #>
							<i {{{ view.getRenderAttributeString('icon') }}}></i>
						<# }

					} else if( 'svg' === iconType ) { #>
						<div {{{ view.getRenderAttributeString('icon') }}}>
							{{{ settings.custom_svg }}}
						</div>
					<# } else if( 'custom' === iconType && '' !== settings.premium_counter_image_upload.url ) { #>
						<img class="custom-image" src="{{ settings.premium_counter_image_upload.url }}">
					<# } else { #>
						<div {{{ view.getRenderAttributeString('counter_lottie') }}}></div>
					<# } #>
				</span>
			</div>

			<#
			}

		#>

		<div {{{ view.getRenderAttributeString('counter') }}}>
			<#

				if( 'yes' === settings.icon_switcher ) {
					renderCounterIcon();
				}

				getCounterContent();
			#>
		</div>

		<?php
	}
}
