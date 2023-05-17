<?php
/**
 * Premium Progress Bar.
 */

namespace PremiumAddons\Widgets;

// Elementor Classes.
use Elementor\Widget_Base;
use Elementor\Utils;
use Elementor\Controls_Manager;
use Elementor\Icons_Manager;
use Elementor\Repeater;
use Elementor\Core\Kits\Documents\Tabs\Global_Colors;
use Elementor\Core\Kits\Documents\Tabs\Global_Typography;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Background;
use Elementor\Group_Control_Border;

// PremiumAddons Classes.
use PremiumAddons\Admin\Includes\Admin_Helper;
use PremiumAddons\Includes\Helper_Functions;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // If this file is called directly, abort.
}

/**
 * Class Premium_Progressbar
 */
class Premium_Progressbar extends Widget_Base {

	/**
	 * Check Icon Draw Option.
	 *
	 * @since 4.9.26
	 * @access public
	 */
	public function check_icon_draw() {
		$is_enabled = Admin_Helper::check_svg_draw( 'premium-progressbar' );
		return $is_enabled;
	}

	/**
	 * Retrieve Widget Name.
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function get_name() {
		return 'premium-addon-progressbar';
	}

	/**
	 * Retrieve Widget Title.
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function get_title() {
		return __( 'Progress Bar', 'premium-addons-for-elementor' );
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
		return 'pa-progress-bar';
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
		return array( 'pa', 'premium', 'circle', 'chart', 'line', 'graph', 'percent' );
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
	 * Register Progress Bar controls.
	 *
	 * @since 1.0.0
	 * @access protected
	 */
	protected function register_controls() { // phpcs:ignore PSR2.Methods.MethodDeclaration.Underscore

		$draw_icon = $this->check_icon_draw();

		$this->start_controls_section(
			'premium_progressbar_labels',
			array(
				'label' => __( 'Progress Bar Settings', 'premium-addons-for-elementor' ),
			)
		);

		$this->add_control(
			'layout_type',
			array(
				'label'       => __( 'Type', 'premium-addons-for-elementor' ),
				'type'        => Controls_Manager::SELECT,
				'options'     => array(
					'line'        => __( 'Line', 'premium-addons-for-elementor' ),
					'half-circle' => __( 'Half Circle', 'premium-addons-for-elementor' ),
					'circle'      => __( 'Circle', 'premium-addons-for-elementor' ),
					'dots'        => __( 'Dots', 'premium-addons-for-elementor' ),
				),
				'default'     => 'line',
				'label_block' => true,
			)
		);

		$this->add_responsive_control(
			'dot_size',
			array(
				'label'       => __( 'Dot Size', 'premium-addons-for-elementor' ),
				'type'        => Controls_Manager::SLIDER,
				'range'       => array(
					'px' => array(
						'min' => 1,
						'max' => 60,
					),
				),
				'default'     => array(
					'size' => 25,
					'unit' => 'px',
				),
				'condition'   => array(
					'layout_type' => 'dots',
				),
				'render_type' => 'template',
				'selectors'   => array(
					'{{WRAPPER}} .progress-segment' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}}',
				),
			)
		);

		$this->add_responsive_control(
			'dot_spacing',
			array(
				'label'       => __( 'Spacing', 'premium-addons-for-elementor' ),
				'type'        => Controls_Manager::SLIDER,
				'range'       => array(
					'px' => array(
						'min' => 1,
						'max' => 10,
					),
				),
				'default'     => array(
					'size' => 8,
					'unit' => 'px',
				),
				'condition'   => array(
					'layout_type' => 'dots',
				),
				'render_type' => 'template',
				'selectors'   => array(
					'{{WRAPPER}} .progress-segment:not(:first-child):not(:last-child)' => 'margin-right: calc( {{SIZE}}{{UNIT}}/2 ); margin-left: calc( {{SIZE}}{{UNIT}}/2 )',
					'{{WRAPPER}} .progress-segment:first-child' => 'margin-right: calc( {{SIZE}}{{UNIT}}/2 )',
					'{{WRAPPER}} .progress-segment:last-child' => 'margin-left: calc( {{SIZE}}{{UNIT}}/2 )',
				),
			)
		);

		$this->add_responsive_control(
			'circle_size',
			array(
				'label'     => __( 'Size', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::SLIDER,
				'range'     => array(
					'px' => array(
						'min'  => 50,
						'max'  => 500,
						'step' => 1,
					),
				),
				'default'   => array(
					'size' => 200,
				),
				'selectors' => array(
					'{{WRAPPER}} .premium-progressbar-circle-wrap, {{WRAPPER}} .premium-progressbar-hf-container' => 'width: {{SIZE}}px; height: {{SIZE}}px',
					'{{WRAPPER}} .premium-progressbar-hf-circle-wrap' => 'width: {{SIZE}}{{UNIT}}; height: calc({{SIZE}} / 2 * 1{{UNIT}});',
					'{{WRAPPER}} .premium-progressbar-hf-labels' => 'width: {{SIZE}}{{UNIT}};',
				),
				'condition' => array(
					'layout_type' => array( 'half-circle', 'circle' ),
				),
			)
		);

		$this->add_control(
			'premium_progressbar_select_label',
			array(
				'label'       => __( 'Labels', 'premium-addons-for-elementor' ),
				'type'        => Controls_Manager::SELECT,
				'default'     => 'left_right_labels',
				'options'     => array(
					'left_right_labels' => __( 'Left & Right Labels', 'premium-addons-for-elementor' ),
					'more_labels'       => __( 'Multiple Labels', 'premium-addons-for-elementor' ),
				),
				'label_block' => true,
				'condition'   => array(
					'layout_type!' => array( 'half-circle', 'circle' ),
				),
			)
		);

		$this->add_control(
			'premium_progressbar_left_label',
			array(
				'label'       => __( 'Left Label', 'premium-addons-for-elementor' ),
				'type'        => Controls_Manager::TEXT,
				'dynamic'     => array( 'active' => true ),
				'default'     => __( 'My Skill', 'premium-addons-for-elementor' ),
				'label_block' => true,
				'condition'   => array(
					'premium_progressbar_select_label' => 'left_right_labels',
				),
			)
		);

		$this->add_responsive_control(
			'title_width',
			array(
				'label'      => __( 'Title Max Width', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', '%', 'em' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-progressbar-left-label' => 'max-width: {{SIZE}}{{UNIT}}',
				),
				'condition'  => array(
					'layout_type' => array( 'half-circle', 'circle' ),
				),
			)
		);

		$this->add_control(
			'premium_progressbar_right_label',
			array(
				'label'       => __( 'Right Label', 'premium-addons-for-elementor' ),
				'type'        => Controls_Manager::TEXT,
				'dynamic'     => array( 'active' => true ),
				'default'     => __( '50%', 'premium-addons-for-elementor' ),
				'label_block' => true,
				'condition'   => array(
					'premium_progressbar_select_label' => 'left_right_labels',
					'layout_type!'                     => array( 'half-circle', 'circle' ),
				),
			)
		);

		$common_conditions = array(
			'layout_type' => array( 'half-circle', 'circle' ),
		);

		$this->add_control(
			'icon_type',
			array(
				'label'     => __( 'Icon Type', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::SELECT,
				'options'   => array(
					'icon'      => __( 'Icon', 'premium-addons-for-elementor' ),
					'image'     => __( 'Custom Image', 'premium-addons-for-elementor' ),
					'animation' => __( 'Lottie Animation', 'premium-addons-for-elementor' ),
					'svg'       => __( 'SVG Code', 'premium-addons-for-elementor' ),
				),
				'default'   => 'icon',
				'separator' => 'before',
				'condition' => $common_conditions,
			)
		);

		$this->add_control(
			'icon_select',
			array(
				'label'     => __( 'Select an Icon', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::ICONS,
				'condition' => array_merge(
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

		$this->add_control(
			'draw_svg',
			array(
				'label'     => __( 'Draw Icon', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::SWITCHER,
				'classes'   => $draw_icon ? '' : 'editor-pa-control-disabled',
				'condition' => array_merge(
					$common_conditions,
					array(
						'icon_type'             => array( 'icon', 'svg' ),
						'icon_select[library]!' => 'svg',
					)
				),
			)
		);

		$animation_conds = array(
			'terms' => array(
				array(
					'relation' => 'or',
					'terms'    => array(
						array(
							'name'  => 'layout_type',
							'value' => 'half-circle',
						),
						array(
							'name'  => 'layout_type',
							'value' => 'circle',
						),
					),
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
						'{{WRAPPER}} .premium-progressbar-circle-content svg *' => 'stroke-width: {{SIZE}}',
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
				'bar',
				array_merge(
					$common_conditions,
					array(
						'icon_type'             => array( 'icon', 'svg' ),
						'icon_select[library]!' => 'svg',
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
			'icon_size',
			array(
				'label'     => __( 'Icon Size', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::SLIDER,
				'condition' => array_merge(
					$common_conditions,
					array(
						'icon_type!' => 'svg',
					)
				),
				'default'   => array(
					'unit' => 'px',
					'size' => 30,
				),
				'selectors' => array(
					'{{WRAPPER}} .premium-progressbar-circle-content i' => 'font-size: {{SIZE}}px',
					'{{WRAPPER}} .premium-progressbar-circle-content svg, {{WRAPPER}} .premium-progressbar-circle-content img' => 'width: {{SIZE}}px !important; height: {{SIZE}}px !important',
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
				'condition'  => array_merge(
					$common_conditions,
					array(
						'icon_type' => 'svg',
					)
				),
				'selectors'  => array(
					'{{WRAPPER}} .premium-progressbar-circle-content svg' => 'width: {{SIZE}}{{UNIT}};',
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
				'condition'  => array_merge(
					$common_conditions,
					array(
						'icon_type' => 'svg',
					)
				),
				'selectors'  => array(
					'{{WRAPPER}} .premium-progressbar-circle-content svg' => 'height: {{SIZE}}{{UNIT}}',
				),
			)
		);

		$this->add_control(
			'show_percentage_value',
			array(
				'label'     => __( 'Show Percentage Value', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::SWITCHER,
				'default'   => 'yes',
				'separator' => 'before',
				'condition' => array(
					'layout_type' => array( 'half-circle', 'circle' ),
				),
			)
		);

		$repeater = new REPEATER();

		$repeater->add_control(
			'text',
			array(
				'label'       => __( 'Label', 'premium-addons-for-elementor' ),
				'type'        => Controls_Manager::TEXT,
				'dynamic'     => array( 'active' => true ),
				'label_block' => true,
				'placeholder' => __( 'label', 'premium-addons-for-elementor' ),
				'default'     => __( 'label', 'premium-addons-for-elementor' ),
			)
		);

		$repeater->add_control(
			'number',
			array(
				'label'   => __( 'Percentage', 'premium-addons-for-elementor' ),
				'dynamic' => array( 'active' => true ),
				'type'    => Controls_Manager::TEXT,
				'default' => 50,
			)
		);

		$this->add_control(
			'premium_progressbar_multiple_label',
			array(
				'label'     => __( 'Label', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::REPEATER,
				'default'   => array(
					array(
						'text'   => __( 'Label', 'premium-addons-for-elementor' ),
						'number' => 50,
					),
				),
				'fields'    => $repeater->get_controls(),
				'condition' => array(
					'premium_progressbar_select_label' => 'more_labels',
					'layout_type!'                     => 'circle',
				),
			)
		);

		$this->add_control(
			'premium_progress_bar_space_percentage_switcher',
			array(
				'label'       => __( 'Enable Percentage', 'premium-addons-for-elementor' ),
				'type'        => Controls_Manager::SWITCHER,
				'default'     => 'yes',
				'description' => __( 'Enable percentage for labels', 'premium-addons-for-elementor' ),
				'condition'   => array(
					'premium_progressbar_select_label' => 'more_labels',
					'layout_type!'                     => 'circle',
				),
			)
		);

		$this->add_control(
			'premium_progressbar_select_label_icon',
			array(
				'label'     => __( 'Labels Indicator', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'line_pin',
				'options'   => array(
					''         => __( 'None', 'premium-addons-for-elementor' ),
					'line_pin' => __( 'Pin', 'premium-addons-for-elementor' ),
					'arrow'    => __( 'Arrow', 'premium-addons-for-elementor' ),
				),
				'condition' => array(
					'premium_progressbar_select_label' => 'more_labels',
					'layout_type!'                     => 'circle',
				),
			)
		);

		$this->add_control(
			'premium_progressbar_more_labels_align',
			array(
				'label'     => __( 'Labels Alignment', 'premuim-addons-for-elementor' ),
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
				'condition' => array(
					'premium_progressbar_select_label' => 'more_labels',
					'layout_type!'                     => 'circle',
				),
			)
		);

		$this->add_control(
			'premium_progressbar_progress_percentage',
			array(
				'label'   => __( 'Value', 'premium-addons-for-elementor' ),
				'type'    => Controls_Manager::TEXT,
				'dynamic' => array( 'active' => true ),
				'default' => 50,
			)
		);

		$this->add_control(
			'premium_progressbar_progress_style',
			array(
				'label'     => __( 'Style', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'solid',
				'options'   => array(
					'solid'    => __( 'Solid', 'premium-addons-for-elementor' ),
					'stripped' => __( 'Striped', 'premium-addons-for-elementor' ),
					'gradient' => __( 'Animated Gradient', 'premium-addons-for-elementor' ),
				),
				'condition' => array(
					'layout_type' => 'line',
				),
			)
		);

		$this->add_control(
			'premium_progressbar_speed',
			array(
				'label' => __( 'Speed (milliseconds)', 'premium-addons-for-elementor' ),
				'type'  => Controls_Manager::NUMBER,
			)
		);

		$this->add_control(
			'premium_progressbar_progress_animation',
			array(
				'label'     => __( 'Animated', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::SWITCHER,
				'condition' => array(
					'premium_progressbar_progress_style' => 'stripped',
					'layout_type'                        => 'line',
				),
			)
		);

		$this->add_control(
			'gradient_colors',
			array(
				'label'       => __( 'Gradient Colors', 'premium-addons-for-elementor' ),
				'type'        => Controls_Manager::TEXT,
				'description' => __( 'Enter Colors separated with \' , \'.', 'premium-addons-for-elementor' ),
				'default'     => '#6EC1E4,#54595F',
				'label_block' => true,
				'condition'   => array(
					'layout_type'                        => 'line',
					'premium_progressbar_progress_style' => 'gradient',
				),
			)
		);

		$this->add_control(
			'half_prefix_label',
			array(
				'label'     => __( 'Prefix Label', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::TEXT,
				'dynamic'   => array( 'active' => true ),
				'default'   => __( '0', 'premium-addons-for-elementor' ),
				'condition' => array(
					'layout_type' => 'half-circle',
				),
				'separator' => 'before',
			)
		);

		$this->add_control(
			'half_suffix_label',
			array(
				'label'     => __( 'Suffix Label', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::TEXT,
				'dynamic'   => array( 'active' => true ),
				'default'   => __( '100%', 'premium-addons-for-elementor' ),
				'condition' => array(
					'layout_type' => 'half-circle',
				),
			)
		);

		$this->add_control(
			'magic_scroll',
			array(
				'label'       => __( 'Use With Magic Scroll', 'premium-addons-for-elementor' ),
				'type'        => Controls_Manager::SWITCHER,
				'description' => __( 'Enable this option if you want to animate the progress bar using ', 'premium-addons-for-elementor' ) . '<a href="https://premiumaddons.com/elementor-magic-scroll-global-addon/" target="_blank">Magic Scroll addon.</a>',
				'condition'   => array(
					'layout_type!' => 'dots',
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

		$doc1_url = Helper_Functions::get_campaign_link( 'https://premiumaddons.com/docs/premium-progress-bar-widget/', 'editor-page', 'wp-editor', 'get-support' );

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
			'premium_progressbar_progress_bar_settings',
			array(
				'label' => __( 'Progress Bar', 'premium-addons-for-elementor' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_control(
			'premium_progressbar_progress_bar_height',
			array(
				'label'       => __( 'Height', 'premium-addons-for-elementor' ),
				'type'        => Controls_Manager::SLIDER,
				'default'     => array(
					'size' => 25,
				),
				'label_block' => true,
				'selectors'   => array(
					'{{WRAPPER}} .premium-progressbar-bar-wrap, {{WRAPPER}} .premium-progressbar-bar' => 'height: {{SIZE}}px;',
				),
				'condition'   => array(
					'layout_type' => 'line',
				),
			)
		);

		$this->add_control(
			'premium_progressbar_progress_bar_radius',
			array(
				'label'      => __( 'Border Radius', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', '%', 'em' ),
				'range'      => array(
					'px' => array(
						'min' => 0,
						'max' => 60,
					),
				),
				'selectors'  => array(
					'{{WRAPPER}} .premium-progressbar-bar-wrap, {{WRAPPER}} .premium-progressbar-bar, {{WRAPPER}} .progress-segment' => 'border-radius: {{SIZE}}{{UNIT}};',
				),
				'condition'  => array(
					'layout_type!' => array( 'half-circle', 'circle' ),
				),
			)
		);

		$this->add_control(
			'circle_border_width',
			array(
				'label'     => __( 'Border Width', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::SLIDER,
				'selectors' => array(
					'{{WRAPPER}} .premium-progressbar-circle-base, {{WRAPPER}} .premium-progressbar-circle div, {{WRAPPER}} .premium-progressbar-circle-inner, {{WRAPPER}} .premium-progressbar-hf-circle-progress' => 'border-width: {{SIZE}}{{UNIT}}',
					'{{WRAPPER}} .premium-progressbar-hf-label-left' => 'transform: translateX( calc( {{SIZE}} / 4 * 1{{UNIT}} ) )',
				),
				'condition' => array(
					'layout_type' => array( 'half-circle', 'circle' ),
				),
			)
		);

		$this->add_control(
			'circle_base_border_color',
			array(
				'label'     => __( 'Border Color', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'global'    => array(
					'default' => Global_Colors::COLOR_PRIMARY,
				),
				'selectors' => array(
					'{{WRAPPER}} .premium-progressbar-circle-base, {{WRAPPER}} .premium-progressbar-circle-inner' => 'border-color: {{VALUE}};',
				),
				'condition' => array(
					'layout_type' => array( 'half-circle', 'circle' ),
				),
			)
		);

		$this->add_control(
			'fill_colors_title',
			array(
				'label' => __( 'Fill', 'premium-addons-for-elementor' ),
				'type'  => Controls_Manager::HEADING,
			)
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			array(
				'name'      => 'premium_progressbar_progress_color',
				'types'     => array( 'classic', 'gradient' ),
				'selector'  => '{{WRAPPER}} .premium-progressbar-bar, {{WRAPPER}} .segment-inner',
				'condition' => array(
					'layout_type!' => array( 'half-circle', 'circle' ),
				),
			)
		);

		$this->add_control(
			'circle_fill_color',
			array(
				'label'     => __( 'Select Color', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'global'    => array(
					'default' => Global_Colors::COLOR_SECONDARY,
				),
				'condition' => array(
					'layout_type' => array( 'half-circle', 'circle' ),
				),
				'selectors' => array(
					'{{WRAPPER}} .premium-progressbar-circle div, {{WRAPPER}} .premium-progressbar-hf-circle-progress' => 'border-color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'base_colors_title',
			array(
				'label' => __( 'Base', 'premium-addons-for-elementor' ),
				'type'  => Controls_Manager::HEADING,
			)
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			array(
				'name'     => 'premium_progressbar_background',
				'types'    => array( 'classic', 'gradient' ),
				'selector' => '{{WRAPPER}} .premium-progressbar-bar-wrap:not(.premium-progressbar-dots), {{WRAPPER}} .premium-progressbar-circle-base, {{WRAPPER}} .progress-segment, {{WRAPPER}} .premium-progressbar-circle-inner',
			)
		);

		$this->add_responsive_control(
			'premium_progressbar_container_margin',
			array(
				'label'      => __( 'Margin', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-progressbar-bar-wrap' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
				'condition'  => array(
					'layout_type!' => array( 'half-circle', 'circle' ),
				),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'premium_progressbar_labels_section',
			array(
				'label'     => __( 'Labels', 'premium-addons-for-elementor' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => array(
					'premium_progressbar_select_label' => 'left_right_labels',
				),
			)
		);

		$this->add_control(
			'premium_progressbar_left_label_hint',
			array(
				'label' => __( 'Title', 'premium-addons-for-elementor' ),
				'type'  => Controls_Manager::HEADING,
			)
		);

		$this->add_control(
			'premium_progressbar_left_label_color',
			array(
				'label'     => __( 'Color', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'global'    => array(
					'default' => Global_Colors::COLOR_PRIMARY,
				),
				'selectors' => array(
					'{{WRAPPER}} .premium-progressbar-left-label' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'left_label_typography',
				'global'   => array(
					'default' => Global_Typography::TYPOGRAPHY_PRIMARY,
				),
				'selector' => '{{WRAPPER}} .premium-progressbar-left-label',
			)
		);

		$this->add_responsive_control(
			'premium_progressbar_left_label_margin',
			array(
				'label'      => __( 'Margin', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-progressbar-left-label' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'premium_progressbar_right_label_hint',
			array(
				'label'     => __( 'Percentage', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			)
		);

		$this->add_control(
			'premium_progressbar_right_label_color',
			array(
				'label'     => __( 'Color', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'global'    => array(
					'default' => Global_Colors::COLOR_PRIMARY,
				),
				'selectors' => array(
					'{{WRAPPER}} .premium-progressbar-right-label' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'right_label_typography',
				'global'   => array(
					'default' => Global_Typography::TYPOGRAPHY_PRIMARY,
				),
				'selector' => '{{WRAPPER}} .premium-progressbar-right-label',
			)
		);

		$this->add_responsive_control(
			'premium_progressbar_right_label_margin',
			array(
				'label'      => __( 'Margin', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-progressbar-right-label' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'icon_style',
			array(
				'label'     => __( 'Icon', 'premium-addons-for-elementor' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => array(
					'layout_type' => array( 'half-circle', 'circle' ),
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
					'{{WRAPPER}} .premium-progressbar-circle-icon' => 'color: {{VALUE}}',
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
		}

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

		$this->add_control(
			'icon_background_color',
			array(
				'label'     => __( 'Background Color', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .premium-progressbar-circle-icon, {{WRAPPER}} .premium-progressbar-circle-content svg' => 'background-color: {{VALUE}};',
				),
				'condition' => array(
					'icon_type!' => 'image',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'     => 'icon_border',
				'selector' => '{{WRAPPER}} .premium-progressbar-circle-icon, {{WRAPPER}} .premium-progressbar-circle-content svg',
			)
		);

		$this->add_responsive_control(
			'icon_border_radius',
			array(
				'label'      => __( 'Border Radius', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-progressbar-circle-icon, {{WRAPPER}} .premium-progressbar-circle-content svg' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}',
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
					'{{WRAPPER}} .premium-progressbar-circle-icon' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
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
					'{{WRAPPER}} .premium-progressbar-circle-icon, {{WRAPPER}} .premium-progressbar-circle-content svg' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'premium_progressbar_multiple_labels_section',
			array(
				'label'     => __( 'Multiple Labels', 'premium-addons-for-elementor' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => array(
					'premium_progressbar_select_label' => 'more_labels',
				),
			)
		);

		$this->add_control(
			'premium_progressbar_multiple_label_color',
			array(
				'label'     => __( 'Labels\' Color', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'global'    => array(
					'default' => Global_Colors::COLOR_PRIMARY,
				),
				'selectors' => array(
					'{{WRAPPER}} .premium-progressbar-center-label' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'label'    => __( 'Labels\' Typography', 'premium-addons-for-elementor' ),
				'name'     => 'more_label_typography',
				'global'   => array(
					'default' => Global_Typography::TYPOGRAPHY_PRIMARY,
				),
				'selector' => '{{WRAPPER}} .premium-progressbar-center-label',
			)
		);

		$this->add_control(
			'premium_progressbar_value_label_color',
			array(
				'label'     => __( 'Percentage Color', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'global'    => array(
					'default' => Global_Colors::COLOR_PRIMARY,
				),
				'condition' => array(
					'premium_progress_bar_space_percentage_switcher' => 'yes',
				),
				'selectors' => array(
					'{{WRAPPER}} .premium-progressbar-percentage' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'label'     => __( 'Percentage Typography', 'premium-addons-for-elementor' ),
				'name'      => 'percentage_typography',
				'condition' => array(
					'premium_progress_bar_space_percentage_switcher' => 'yes',
				),
				'global'    => array(
					'default' => Global_Typography::TYPOGRAPHY_PRIMARY,
				),
				'selector'  => '{{WRAPPER}} .premium-progressbar-percentage',
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'premium_progressbar_multiple_labels_arrow_section',
			array(
				'label'     => __( 'Arrow', 'premium-addons-for-elementor' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => array(
					'premium_progressbar_select_label' => 'more_labels',
					'premium_progressbar_select_label_icon' => 'arrow',
				),
			)
		);

		$this->add_control(
			'premium_progressbar_arrow_color',
			array(
				'label'     => __( 'Color', 'premium_elementor' ),
				'type'      => Controls_Manager::COLOR,
				'global'    => array(
					'default' => Global_Colors::COLOR_PRIMARY,
				),
				'condition' => array(
					'premium_progressbar_select_label_icon' => 'arrow',
				),
				'selectors' => array(
					'{{WRAPPER}} .premium-progressbar-arrow' => 'color: {{VALUE}}',
				),
			)
		);

		$this->add_responsive_control(
			'premium_arrow_size',
			array(
				'label'      => __( 'Size', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', 'em' ),
				'condition'  => array(
					'premium_progressbar_select_label_icon' => 'arrow',
				),
				'selectors'  => array(
					'{{WRAPPER}} .premium-progressbar-arrow' => 'border-width: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'premium_progressbar_multiple_labels_pin_section',
			array(
				'label'     => __( 'Indicator', 'premium-addons-for-elementor' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => array(
					'premium_progressbar_select_label' => 'more_labels',
					'premium_progressbar_select_label_icon' => 'line_pin',
				),
			)
		);

		$this->add_control(
			'premium_progressbar_line_pin_color',
			array(
				'label'     => __( 'Color', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'global'    => array(
					'default' => Global_Colors::COLOR_SECONDARY,
				),
				'condition' => array(
					'premium_progressbar_select_label_icon' => 'line_pin',
				),
				'selectors' => array(
					'{{WRAPPER}} .premium-progressbar-pin' => 'border-color: {{VALUE}};',
				),
			)
		);

		$this->add_responsive_control(
			'premium_pin_size',
			array(
				'label'      => __( 'Size', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', 'em' ),
				'condition'  => array(
					'premium_progressbar_select_label_icon' => 'line_pin',
				),
				'selectors'  => array(
					'{{WRAPPER}} .premium-progressbar-pin' => 'border-left-width: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'premium_pin_height',
			array(
				'label'      => __( 'Height', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', 'em' ),
				'condition'  => array(
					'premium_progressbar_select_label_icon' => 'line_pin',
				),
				'selectors'  => array(
					'{{WRAPPER}} .premium-progressbar-pin' => 'height: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'prefix_suffix_style',
			array(
				'label'     => __( 'Prefix & Suffix', 'premium-addons-for-elementor' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => array(
					'layout_type' => 'half-circle',
				),
			)
		);

		$this->add_responsive_control(
			'labels_top_spacing',
			array(
				'label'     => __( 'Top Spacing', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::SLIDER,
				'range'     => array(
					'px' => array(
						'min' => 1,
						'max' => 300,
					),
				),
				'selectors' => array(
					'{{WRAPPER}} .premium-progressbar-hf-labels' => 'margin-top: {{SIZE}}px;',
				),
			)
		);

		$this->add_control(
			'prefix_title',
			array(
				'label' => __( 'Prefix', 'premium-addons-for-elementor' ),
				'type'  => Controls_Manager::HEADING,
			)
		);

		$this->add_control(
			'prefix_label_color',
			array(
				'label'     => __( 'Color', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'global'    => array(
					'default' => Global_Colors::COLOR_PRIMARY,
				),
				'selectors' => array(
					'{{WRAPPER}} .premium-progressbar-hf-label-left' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'prefix_label_typo',
				'global'   => array(
					'default' => Global_Typography::TYPOGRAPHY_PRIMARY,
				),
				'selector' => '{{WRAPPER}} .premium-progressbar-hf-label-left',
			)
		);

		$pfx_direction = is_rtl() ? 'right' : 'left';
		$sfx_direction = is_rtl() ? 'left' : 'right';

		$this->add_responsive_control(
			'prefix_spacing',
			array(
				'label'     => __( 'Spacing', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::SLIDER,
				'selectors' => array(
					'{{WRAPPER}} .premium-progressbar-hf-label-left' => 'margin-' . $pfx_direction . ': {{SIZE}}px;',
				),
			)
		);

		$this->add_control(
			'suffix_title',
			array(
				'label' => __( 'Suffix', 'premium-addons-for-elementor' ),
				'type'  => Controls_Manager::HEADING,
			)
		);

		$this->add_control(
			'suffix_label_color',
			array(
				'label'     => __( 'Color', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'separator' => 'before',
				'global'    => array(
					'default' => Global_Colors::COLOR_PRIMARY,
				),
				'selectors' => array(
					'{{WRAPPER}} .premium-progressbar-hf-label-right' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'suffix_label_typo',
				'global'   => array(
					'default' => Global_Typography::TYPOGRAPHY_PRIMARY,
				),
				'selector' => '{{WRAPPER}} .premium-progressbar-hf-label-right',
			)
		);

		$this->add_responsive_control(
			'suffix_spacing',
			array(
				'label'     => __( 'Spacing', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::SLIDER,
				'selectors' => array(
					'{{WRAPPER}} .premium-progressbar-hf-label-right' => 'margin-' . $sfx_direction . ': {{SIZE}}px;',
				),
			)
		);

		$this->end_controls_section();
	}

	/**
	 * Render Progress Bar widget output on the frontend.
	 *
	 * Written in PHP and used to generate the final HTML.
	 *
	 * @since 1.0.0
	 * @access protected
	 */
	protected function render() {

		$settings = $this->get_settings_for_display();

		$this->add_inline_editing_attributes( 'premium_progressbar_left_label' );
		$this->add_render_attribute( 'premium_progressbar_left_label', 'class', 'premium-progressbar-left-label' );

		$this->add_inline_editing_attributes( 'premium_progressbar_right_label' );
		$this->add_render_attribute( 'premium_progressbar_right_label', 'class', 'premium-progressbar-right-label' );

		$length = isset( $settings['premium_progressbar_progress_percentage']['size'] ) ? $settings['premium_progressbar_progress_percentage']['size'] : $settings['premium_progressbar_progress_percentage'];

		$style = $settings['premium_progressbar_progress_style'];
		$type  = $settings['layout_type'];

		$progressbar_settings = array(
			'progress_length' => $length,
			'speed'           => ! empty( $settings['premium_progressbar_speed'] ) ? $settings['premium_progressbar_speed'] : 1000,
			'type'            => $type,
			'mScroll'         => $settings['magic_scroll'],
		);

		if ( 'dots' === $type ) {
			$progressbar_settings['dot']     = $settings['dot_size']['size'];
			$progressbar_settings['spacing'] = $settings['dot_spacing']['size'];
		}

		$this->add_render_attribute( 'progressbar', 'class', 'premium-progressbar-container' );

		if ( 'stripped' === $style ) {
			$this->add_render_attribute( 'progressbar', 'class', 'premium-progressbar-striped' );
		} elseif ( 'gradient' === $style ) {
			$this->add_render_attribute( 'progressbar', 'class', 'premium-progressbar-gradient' );
			$progressbar_settings['gradient'] = $settings['gradient_colors'];
		}

		if ( 'yes' === $settings['premium_progressbar_progress_animation'] ) {
			$this->add_render_attribute( 'progressbar', 'class', 'premium-progressbar-active' );
		}

		$this->add_render_attribute( 'progressbar', 'data-settings', wp_json_encode( $progressbar_settings ) );

		if ( 'circle' !== $type && 'half-circle' !== $type ) {
			$this->add_render_attribute( 'wrap', 'class', 'premium-progressbar-bar-wrap' );

			if ( 'dots' === $type ) {
				$this->add_render_attribute( 'wrap', 'class', 'premium-progressbar-dots' );
			}
		} else {

			$class = 'half-circle' === $type ? '-hf' : '';

			$this->add_render_attribute( 'wrap', 'class', 'premium-progressbar' . $class . '-circle-wrap' );

		}

		if ( 'yes' === $settings['draw_svg'] ) {

			$this->add_render_attribute(
				'progressbar',
				'class',
				array(
					'elementor-invisible',
					'premium-drawer-hover',
				)
			);
		}

		?>

		<div <?php echo wp_kses_post( $this->get_render_attribute_string( 'progressbar' ) ); ?>>

			<?php if ( 'left_right_labels' === $settings['premium_progressbar_select_label'] ) : ?>
				<p <?php echo wp_kses_post( $this->get_render_attribute_string( 'premium_progressbar_left_label' ) ); ?>>
					<?php echo wp_kses_post( $settings['premium_progressbar_left_label'] ); ?>
				</p>
				<p <?php echo wp_kses_post( $this->get_render_attribute_string( 'premium_progressbar_right_label' ) ); ?>>
					<?php echo wp_kses_post( 'yes' !== $settings['magic_scroll'] ? $settings['premium_progressbar_right_label'] : '0%' ); ?>
				</p>
			<?php endif; ?>

			<?php if ( 'more_labels' === $settings['premium_progressbar_select_label'] ) : ?>
				<div class="premium-progressbar-container-label" style="position:relative;">
				<?php
				$direction = is_rtl() ? 'right' : 'left';

				foreach ( $settings['premium_progressbar_multiple_label'] as $item ) {
					if ( 'center' === $this->get_settings( 'premium_progressbar_more_labels_align' ) ) {
						if ( 'yes' === $settings['premium_progress_bar_space_percentage_switcher'] ) {
							if ( 'arrow' === $settings['premium_progressbar_select_label_icon'] ) {
								echo '<div class="premium-progressbar-multiple-label" style="' . esc_attr( $direction . ':' . $item['number'] ) . '%;"><p class = "premium-progressbar-center-label" style="transform:translateX(-45%);">' . esc_attr( $item['text'] ) . ' <span class="premium-progressbar-percentage">' . esc_attr( $item['number'] ) . '%</span></p><p class="premium-progressbar-arrow" style="' . esc_attr( $direction . ':' . $item['number'] ) . '%; transform:translateX(50%);"></p></div>';
							} elseif ( 'line_pin' === $settings['premium_progressbar_select_label_icon'] ) {
								echo '<div class="premium-progressbar-multiple-label" style="' . esc_attr( $direction . ':' . $item['number'] ) . '%;"><p class = "premium-progressbar-center-label" style="transform:translateX(-45%);">' . esc_attr( $item['text'] ) . ' <span class="premium-progressbar-percentage">' . esc_attr( $item['number'] ) . '%</span></p><p class="premium-progressbar-pin" style="' . esc_attr( $direction . ':' . $item['number'] ) . '%; transform:translateX(50%);"></p></div>';
							} else {
								echo '<div class="premium-progressbar-multiple-label" style="' . esc_attr( $direction . ':' . $item['number'] ) . '%;"><p class = "premium-progressbar-center-label" style="transform:translateX(-45%);">' . esc_attr( $item['text'] ) . ' <span class="premium-progressbar-percentage">' . esc_attr( $item['number'] ) . '%</span></p></div>';
							}
						} else {
							if ( 'arrow' === $settings['premium_progressbar_select_label_icon'] ) {
								echo '<div class="premium-progressbar-multiple-label" style="' . esc_attr( $direction . ':' . $item['number'] ) . '%;"><p class = "premium-progressbar-center-label" style="transform:translateX(-45%);">' . esc_attr( $item['text'] ) . '</p><p class="premium-progressbar-arrow" style="' . esc_attr( $direction . ':' . $item['number'] ) . '%; transform:translateX(50%);"></p></div>';
							} elseif ( 'line_pin' === $settings['premium_progressbar_select_label_icon'] ) {
								echo '<div class="premium-progressbar-multiple-label" style="' . esc_attr( $direction . ':' . $item['number'] ) . '%;"><p class = "premium-progressbar-center-label" style="transform:translateX(-45%)">' . esc_attr( $item['text'] ) . '</p><p class="premium-progressbar-pin" style="' . esc_attr( $direction . ':' . $item['number'] ) . '%; transform:translateX(50%);"></p></div>';
							} else {
								echo '<div class="premium-progressbar-multiple-label" style="' . esc_attr( $direction . ':' . $item['number'] ) . '%;"><p class = "premium-progressbar-center-label" style="transform:translateX(-45%);">' . esc_attr( $item['text'] ) . '</p></div>';
							}
						}
					} elseif ( 'left' === $this->get_settings( 'premium_progressbar_more_labels_align' ) ) {
						if ( 'yes' === $settings['premium_progress_bar_space_percentage_switcher'] ) {
							if ( 'arrow' === $settings['premium_progressbar_select_label_icon'] ) {
								echo '<div class="premium-progressbar-multiple-label" style="' . esc_attr( $direction . ':' . $item['number'] ) . '%;"><p class = "premium-progressbar-center-label" style="transform:translateX(-10%);">' . esc_attr( $item['text'] ) . ' <span class="premium-progressbar-percentage">' . esc_attr( $item['number'] ) . '%</span></p><p class="premium-progressbar-arrow" style="' . esc_attr( $direction . ':' . $item['number'] ) . '%; transform:translateX(50%);"></p></div>';
							} elseif ( 'line_pin' === $settings['premium_progressbar_select_label_icon'] ) {
								echo '<div class="premium-progressbar-multiple-label" style="' . esc_attr( $direction . ':' . $item['number'] ) . '%;"><p class = "premium-progressbar-center-label" style="transform:translateX(-2%);">' . esc_attr( $item['text'] ) . ' <span class="premium-progressbar-percentage">' . esc_attr( $item['number'] ) . '%</span></p><p class="premium-progressbar-pin" style="' . esc_attr( $direction . ':' . $item['number'] ) . '%; transform:translateX(50%);"></p></div>';
							} else {
								echo '<div class="premium-progressbar-multiple-label" style="' . esc_attr( $direction . ':' . $item['number'] ) . '%;"><p class = "premium-progressbar-center-label" style="transform:translateX(-2%);">' . esc_attr( $item['text'] ) . ' <span class="premium-progressbar-percentage">' . esc_attr( $item['number'] ) . '%</span></p></div>';
							}
						} else {
							if ( 'arrow' === $settings['premium_progressbar_select_label_icon'] ) {
								echo '<div class="premium-progressbar-multiple-label" style="' . esc_attr( $direction . ':' . $item['number'] ) . '%;"><p class = "premium-progressbar-center-label" style="transform:translateX(-10%);">' . esc_attr( $item['text'] ) . '</p><p class="premium-progressbar-arrow" style="' . esc_attr( $direction . ':' . $item['number'] ) . '%; transform:translateX(50%);"></p></div>';
							} elseif ( 'line_pin' === $settings['premium_progressbar_select_label_icon'] ) {
								echo '<div class="premium-progressbar-multiple-label" style="' . esc_attr( $direction . ':' . $item['number'] ) . '%;"><p class = "premium-progressbar-center-label" style="transform:translateX(-2%);">' . esc_attr( $item['text'] ) . '</p><p class="premium-progressbar-pin" style="' . esc_attr( $direction . ':' . $item['number'] ) . '%; transform:translateX(50%);"></p></div>';
							} else {
								echo '<div class="premium-progressbar-multiple-label" style="' . esc_attr( $direction . ':' . $item['number'] ) . '%;"><p class = "premium-progressbar-center-label" style="transform:translateX(-2%);">' . esc_attr( $item['text'] ) . '</p></div>';
							}
						}
					} else {
						if ( 'yes' === $settings['premium_progress_bar_space_percentage_switcher'] ) {
							if ( 'arrow' === $settings['premium_progressbar_select_label_icon'] ) {
								echo '<div class="premium-progressbar-multiple-label" style="' . esc_attr( $direction . ':' . $item['number'] ) . '%;"><p class = "premium-progressbar-center-label" style="transform:translateX(-82%);">' . esc_attr( $item['text'] ) . ' <span class="premium-progressbar-percentage">' . esc_attr( $item['number'] ) . '%</span></p><p class="premium-progressbar-arrow" style="' . esc_attr( $direction . ':' . $item['number'] ) . '%; transform:translateX(50%);"></p></div>';
							} elseif ( 'line_pin' === $settings['premium_progressbar_select_label_icon'] ) {
								echo '<div class="premium-progressbar-multiple-label" style="' . esc_attr( $direction . ':' . $item['number'] ) . '%;"><p class = "premium-progressbar-center-label" style="transform:translateX(-95%);">' . esc_attr( $item['text'] ) . ' <span class="premium-progressbar-percentage">' . esc_attr( $item['number'] ) . '%</span></p><p class="premium-progressbar-pin" style="' . esc_attr( $direction . ':' . $item['number'] ) . '%; transform:translateX(50%);"></p></div>';
							} else {
								echo '<div class="premium-progressbar-multiple-label" style="' . esc_attr( $direction . ':' . $item['number'] ) . '%;"><p class = "premium-progressbar-center-label" style="transform:translateX(-96%);">' . esc_attr( $item['text'] ) . ' <span class="premium-progressbar-percentage">' . esc_attr( $item['number'] ) . '%</span></p></div>';
							}
						} else {
							if ( 'arrow' === $settings['premium_progressbar_select_label_icon'] ) {
								echo '<div class="premium-progressbar-multiple-label" style="' . esc_attr( $direction . ':' . $item['number'] ) . '%;"><p class = "premium-progressbar-center-label" style="transform:translateX(-71%);">' . esc_attr( $item['text'] ) . '</p><p class="premium-progressbar-arrow" style="' . esc_attr( $direction . ':' . $item['number'] ) . '%; transform:translateX(50%);"></p></div>';
							} elseif ( 'line_pin' === $settings['premium_progressbar_select_label_icon'] ) {
								echo '<div class="premium-progressbar-multiple-label" style="' . esc_attr( $direction . ':' . $item['number'] ) . '%;"><p class = "premium-progressbar-center-label" style="transform:translateX(-97%);">' . esc_attr( $item['text'] ) . '</p><p class="premium-progressbar-pin" style="' . esc_attr( $direction . ':' . $item['number'] ) . '%; transform:translateX(50%);"></p></div>';
							} else {
								echo '<div class="premium-progressbar-multiple-label" style="' . esc_attr( $direction . ':' . $item['number'] ) . '%;"><p class = "premium-progressbar-center-label" style="transform:translateX(-96%);">' . esc_attr( $item['text'] ) . '</p></div>';
							}
						}
					}
				}
				?>
				</div>
			<?php endif; ?>

			<?php if ( 'circle' !== $type ) : ?>
				<div class="clearfix"></div>
			<?php endif; ?>

			<div <?php echo wp_kses_post( $this->get_render_attribute_string( 'wrap' ) ); ?>>
				<?php if ( 'line' === $type ) : ?>
					<div class="premium-progressbar-bar" role="progressbar" aria-valuemin="0" aria-valuemax="100"></div>
				<?php elseif ( 'circle' === $type ) : ?>

					<div class="premium-progressbar-circle-base"></div>
					<div class="premium-progressbar-circle">
						<div class="premium-progressbar-circle-left"></div>
						<div class="premium-progressbar-circle-right"></div>
					</div>

					<?php $this->render_progressbar_content(); ?>

				<?php elseif ( 'half-circle' === $type ) : ?>

					<div class="premium-progressbar-hf-container">
						<div class="premium-progressbar-hf-circle">
							<div class="premium-progressbar-hf-circle-progress"></div>
						</div>

						<div class="premium-progressbar-circle-inner"></div>
					</div>

					<?php $this->render_progressbar_content(); ?>

				<?php endif; ?>
			</div>

			<?php if ( 'half-circle' === $type ) : ?>
				<div class="premium-progressbar-hf-labels">
					<span class="premium-progressbar-hf-label-left">
						<?php echo wp_kses_post( $settings['half_prefix_label'] ); ?>
					</span>
					<span class="premium-progressbar-hf-label-right">
						<?php echo wp_kses_post( $settings['half_suffix_label'] ); ?>
					</span>
				</div>
			<?php endif; ?>

		</div>

		<?php
	}

	/**
	 * Get Progressbar Content
	 *
	 * @since 4.9.13
	 * @access private
	 */
	private function render_progressbar_content() {

		$settings = $this->get_settings_for_display();

		$icon_type = $settings['icon_type'];

		if ( 'icon' === $icon_type || 'svg' === $icon_type ) {

			$this->add_render_attribute( 'icon', 'class', 'premium-drawable-icon' );

			if ( ( 'yes' === $settings['draw_svg'] && 'icon' === $icon_type ) || 'svg' === $icon_type ) {
				$this->add_render_attribute( 'icon', 'class', 'premium-progressbar-circle-icon' );
			}

			if ( 'yes' === $settings['draw_svg'] ) {

				if ( 'icon' === $icon_type ) {

					$this->add_render_attribute( 'icon', 'class', $settings['icon_select']['value'] );

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
				'progress_lottie',
				array(
					'class'               => array(
						'premium-progressbar-circle-icon',
						'premium-lottie-animation',
					),
					'data-lottie-url'     => $settings['lottie_url'],
					'data-lottie-loop'    => $settings['lottie_loop'],
					'data-lottie-reverse' => $settings['lottie_reverse'],
				)
			);
		}

		?>

			<div class="premium-progressbar-circle-content">

				<?php
				if ( 'icon' === $icon_type ) :
					if ( 'yes' !== $settings['draw_svg'] ) :
						Icons_Manager::render_icon(
							$settings['icon_select'],
							array(
								'class'       => array( 'premium-progressbar-circle-icon', 'premium-svg-nodraw', 'premium-drawable-icon' ),
								'aria-hidden' => 'true',
							)
						);
					else :
						?>
							<i <?php echo wp_kses_post( $this->get_render_attribute_string( 'icon' ) ); ?>></i>
						<?php
					endif;

				elseif ( 'svg' === $icon_type ) :
					?>
					<div <?php echo wp_kses_post( $this->get_render_attribute_string( 'icon' ) ); ?>>
						<?php $this->print_unescaped_setting( 'custom_svg' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
					</div>
					<?php
				elseif ( 'image' === $icon_type ) :
					?>
					<img class="premium-progressbar-circle-icon" src="<?php echo esc_attr( $settings['image_upload']['url'] ); ?>">
				<?php else : ?>
					<div <?php echo wp_kses_post( $this->get_render_attribute_string( 'progress_lottie' ) ); ?>></div>
				<?php endif; ?>

				<p <?php echo wp_kses_post( $this->get_render_attribute_string( 'premium_progressbar_left_label' ) ); ?>>
					<?php echo wp_kses_post( $settings['premium_progressbar_left_label'] ); ?>
				</p>
				<?php if ( 'yes' === $settings['show_percentage_value'] ) : ?>
					<?php if ( 'yes' === $settings['show_percentage_value'] ) : ?>
						<p <?php echo wp_kses_post( $this->get_render_attribute_string( 'premium_progressbar_right_label' ) ); ?>>0%</p>
					<?php endif; ?>
				<?php endif; ?>
			</div>

		<?php

	}
}
