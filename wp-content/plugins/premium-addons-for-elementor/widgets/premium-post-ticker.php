<?php
/**
 * Premium Post Ticker.
 */

namespace PremiumAddons\Widgets;

// Elementor Classes.
use Elementor\Utils;
use Elementor\Repeater;
use Elementor\Widget_Base;
use Elementor\Icons_Manager;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Image_Size;
use Elementor\Group_Control_Background;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Css_Filter;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Text_Shadow;
use Elementor\Core\Kits\Documents\Tabs\Global_Colors;
use Elementor\Core\Kits\Documents\Tabs\Global_Typography;


// PremiumAddons Classes.
use PremiumAddons\Admin\Includes\Admin_Helper;
use PremiumAddons\Includes\Helper_Functions;
use PremiumAddons\Includes\Controls\Premium_Post_Filter;
use PremiumAddons\Includes\Premium_Template_Tags as Posts_Helper;

// PremiumAddonsPro Classes
use PremiumAddonsPro\Includes\Pa_Post_Ticker_Helper as API_Handler;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // If this file is called directly, abort.
}

/**
 * Class Premium_Post_Ticker.
 */
class Premium_Post_Ticker extends Widget_Base {

	/**
	 * Options
	 *
	 * @var options
	 */
	private $options = null;

	/**
	 * Check Icon Draw Option.
	 *
	 * @since 4.9.26
	 * @access public
	 */
	public function check_icon_draw() {
		$is_enabled = Admin_Helper::check_svg_draw( 'premium-post-ticker' );
		return $is_enabled;
	}

	/**
	 * Retrieve Widget Name.
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function get_name() {
		return 'premium-post-ticker';
	}

	/**
	 * Retrieve Widget Title.
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function get_title() {
		return __( 'News Ticker', 'premium-addons-for-elementor' );
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
		return 'pa-ticker';
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
		return array( 'pa', 'premium', 'magazine', 'news', 'posts', 'listing', 'ticker', 'grid', 'blog' );
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
	 * Widget preview refresh button.
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function is_reload_preview_required() {
		return true;
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
			'font-awesome-5-all',
			'pa-slick',
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
				'imagesloaded',
				'isotope-js',
				'pa-slick',
				'lottie-js',
				'premium-addons',
			)
		);
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
	 * Register Smart Post Listing controls.
	 *
	 * @since 1.0.0
	 * @access protected
	 */
	protected function register_controls() {

		$this->options = apply_filters(
			'pa_ticker_options',
			array(
				'layouts'          => array(
					'layout-1' => __( 'Layout 1', 'premium-addons-for-elementor' ),
					'layout-2' => __( 'Layout 2', 'premium-addons-for-elementor' ),
					'layout-3' => __( 'Layout 3 (Pro)', 'premium-addons-for-elementor' ),
					'layout-4' => __( 'Layout 4 (Pro)', 'premium-addons-for-elementor' ),
				),
				'layout_condition' => array( 'layout-3', 'layout-4' ),
			)
		);

		$this->register_content_tab_controls();
		$this->register_style_tab_controls();
	}

	/**
	 * Adds content tab controls.
	 *
	 * @access private
	 * @since 4.9.37
	 */
	private function register_content_tab_controls() {

		$this->add_general_section_controls();
		$this->add_query_section_controls();
		$this->add_posts_section_controls();

		$papro_activated = apply_filters( 'papro_activated', false );

		if ( $papro_activated ) {
			do_action( 'pa_ticker_stock_controls', $this );
		}

		$this->add_slider_section_controls();

		$this->add_helpful_info_section();
	}

	/**
	 * Adds style tab controls.
	 *
	 * @access private
	 * @since 4.9.37
	 */
	private function register_style_tab_controls() {

		$this->add_ticker_title_style();

		$this->add_ticker_date_style();

		$this->add_posts_style();

		$this->add_posts_container_style();

		$this->add_navigation_style();

		$this->add_separator_style();
	}

	/**
	 * Adds General controls.
	 *
	 * @access private
	 * @since 4.9.37
	 */
	private function add_general_section_controls() {

		$draw_icon = $this->check_icon_draw();

		$this->start_controls_section(
			'pa_ticker_general_section',
			array(
				'label' => __( 'General', 'premium-addons-for-elementor' ),
			)
		);

		$this->add_control(
			'layout',
			array(
				'label'        => __( 'Layout', 'premium-addons-for-elementor' ),
				'type'         => Controls_Manager::SELECT,
				'prefix_class' => 'premium-post-ticker__',
				'render_type'  => 'template',
				'label_block'  => true,
				'options'      => $this->options['layouts'],
				'default'      => 'layout-1',
			)
		);

		$papro_activated = apply_filters( 'papro_activated', false );

		if ( ! $papro_activated ) {

			$get_pro = Helper_Functions::get_campaign_link( 'https://premiumaddons.com/pro', 'editor-page', 'wp-editor', 'get-pro' );

			$this->add_control(
				'ticker_notice',
				array(
					'type'            => Controls_Manager::RAW_HTML,
					'raw'             => __( 'This option is available in Premium Addons Pro. ', 'premium-addons-for-elementor' ) . '<a href="' . esc_url( $get_pro ) . '" target="_blank">' . __( 'Upgrade now!', 'premium-addons-for-elementor' ) . '</a>',
					'content_classes' => 'papro-upgrade-notice',
					'condition'       => array(
						'layout' => $this->options['layout_condition'],
					),
				)
			);

		}

		$this->add_control(
			'ticker_title',
			array(
				'label'       => __( 'Ticker Title', 'premium-addons-for-elementor' ),
				'type'        => Controls_Manager::TEXT,
				'label_block' => true,
				'default'     => __( 'TRENDING', 'premium-addons-for-elementor' ),
				'dynamic'     => array( 'active' => true ),
			)
		);

		$this->add_control(
			'ticker_icon_sw',
			array(
				'label'       => __( 'Icon', 'premium-addons-for-elementor' ),
				'type'        => Controls_Manager::SWITCHER,
				'render_type' => 'template',
				'condition'   => array(
					'ticker_title!' => '',
				),
			)
		);

		$this->add_control(
			'icon_type',
			array(
				'label'       => __( 'Icon Type', 'premium-addons-for-elementor' ),
				'type'        => Controls_Manager::SELECT,
				'render_type' => 'template',
				'options'     => array(
					'icon'   => __( 'Icon', 'premium-addons-for-elementor' ),
					'lottie' => __( 'Lottie Animation', 'premium-addons-for-elementor' ),
					'image'  => __( 'Image', 'premium-addons-for-elementor' ),
					'svg'    => __( 'SVG Code', 'premium-addons-for-elementor' ),
				),
				'default'     => 'icon',
				'condition'   => array(
					'ticker_icon_sw' => 'yes',
					'ticker_title!'  => '',
				),
			)
		);

		$common_conditions = array(
			'ticker_icon_sw' => 'yes',
			'ticker_title!'  => '',
		);

		$this->add_control(
			'pa_ticker_icon',
			array(
				'label'       => __( 'Choose Icon', 'premium-addons-for-elementor' ),
				'type'        => Controls_Manager::ICONS,
				'label_block' => false,
				'skin'        => 'inline',
				'default'     => array(
					'value'   => 'fas fa-star',
					'library' => 'fa-solid',
				),
				'condition'   => array(
					'ticker_icon_sw' => 'yes',
					'ticker_title!'  => '',
					'icon_type'      => 'icon',
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
					'ticker_icon_sw' => 'yes',
					'ticker_title!'  => '',
					'icon_type'      => 'svg',
				),
			)
		);

		$this->add_control(
			'image',
			array(
				'label'       => __( 'Image', 'premium-addons-for-elementor' ),
				'type'        => Controls_Manager::MEDIA,
				'media_types' => array( 'image' ),
				'dynamic'     => array( 'active' => true ),
				'condition'   => array(
					'ticker_icon_sw' => 'yes',
					'ticker_title!'  => '',
					'icon_type'      => 'image',
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
					'ticker_icon_sw' => 'yes',
					'ticker_title!'  => '',
					'icon_type'      => 'lottie',
				),
			)
		);

		$animation_conds = array(
			'terms' => array(
				array(
					'name'  => 'ticker_icon_sw',
					'value' => 'yes',
				),
				array(
					'name'     => 'ticker_title',
					'operator' => '!==',
					'value'    => '',
				),
				array(
					'relation' => 'or',
					'terms'    => array(
						array(
							'name'  => 'icon_type',
							'value' => 'lottie',
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
						'icon_type'                => array( 'icon', 'svg' ),
						'pa_ticker_icon[library]!' => 'svg',
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
						'{{WRAPPER}} .premium-post-ticker__icon-wrapper:not(.premium-repeater-item) svg *' => 'stroke-width: {{SIZE}}',
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
				'ticker',
				array_merge(
					$common_conditions,
					array(
						'icon_type'                => array( 'icon', 'svg' ),
						'pa_ticker_icon[library]!' => 'svg',
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
			'icon_order',
			array(
				'label'     => __( 'Icon Order', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::CHOOSE,
				'toggle'    => false,
				'options'   => array(
					'0' => array(
						'title' => __( 'Before Title', 'premium-addons-for-elementor' ),
						'icon'  => 'eicon-order-start',
					),
					'2' => array(
						'title' => __( 'After Title', 'premium-addons-for-elementor' ),
						'icon'  => 'eicon-order-end',
					),
				),
				'separator' => 'before',
				'default'   => '0',
				'condition' => array(
					'ticker_icon_sw' => 'yes',
					'ticker_title!'  => '',
				),
				'selectors' => array(
					'{{WRAPPER}} .premium-post-ticker__icon-wrapper:not(.premium-repeater-item)' => 'order: {{VALUE}}',
				),
			)
		);

		$this->add_responsive_control(
			'icon_size',
			array(
				'label'      => __( 'Icon Size (px)', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array(
					'px' => array(
						'min' => 0,
						'max' => 500,
					),
				),
				'selectors'  => array(
					'{{WRAPPER}} .premium-post-ticker__icon-wrapper:not(.premium-repeater-item) i'  => 'font-size: {{SIZE}}px;',
					'{{WRAPPER}} .premium-post-ticker__icon-wrapper:not(.premium-repeater-item) img'  => 'width: {{SIZE}}px',
					'{{WRAPPER}} .premium-post-ticker__icon-wrapper:not(.premium-repeater-item) > svg,
                    {{WRAPPER}} .premium-post-ticker__icon-wrapper:not(.premium-repeater-item) .premium-lottie-animation,
                    {{WRAPPER}} .premium-post-ticker__icon-wrapper:not(.premium-repeater-item) .premium-drawable-icon'  => 'width: {{SIZE}}px; height: {{SIZE}}px; line-height: {{SIZE}}px;',
				),
				'condition'  => array(
					'ticker_icon_sw' => 'yes',
					'ticker_title!'  => '',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Image_Size::get_type(),
			array(
				'name'      => 'thumbnail',
				'default'   => 'full',
				'condition' => array(
					'ticker_icon_sw' => 'yes',
					'ticker_title!'  => '',
					'icon_type'      => 'image',
				),
			)
		);

		$this->add_responsive_control(
			'icon_spacing',
			array(
				'label'      => __( 'Spacing (px)', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-post-ticker__title-wrapper'  => 'column-gap: {{SIZE}}px',
				),
				'condition'  => array(
					'ticker_icon_sw' => 'yes',
					'ticker_title!'  => '',
				),
			)
		);

		$this->add_control(
			'hide_title_on',
			array(
				'label'       => __( 'Hide Title On', 'premium-addons-for-elementor' ),
				'type'        => Controls_Manager::SELECT2,
				'options'     => Helper_Functions::get_all_breakpoints(),
				'separator'   => 'after',
				'multiple'    => true,
				'label_block' => true,
				'default'     => array(),
				'condition'   => array(
					'ticker_title!' => '',
				),
			)
		);

		$this->add_control(
			'show_date',
			array(
				'label'   => __( 'Show Current Date', 'premium-addons-for-elementor' ),
				'type'    => Controls_Manager::SWITCHER,
				'default' => 'yes',
			)
		);

		$this->add_control(
			'ticker_date_pos',
			array(
				'label'     => __( 'Position', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::CHOOSE,
				'toggle'    => false,
				'options'   => array(
					'flex-start' => array(
						'title' => __( 'Start', 'premium-addons-for-elementor' ),
						'icon'  => 'eicon-order-start',
					),
					'flex-end'   => array(
						'title' => __( 'End', 'premium-addons-for-elementor' ),
						'icon'  => 'eicon-order-end',
					),
				),
				'default'   => 'flex-start',
				'condition' => array(
					'show_date' => 'yes',
					'layout'    => 'layout-1',
				),
				'selectors' => array(
					'{{WRAPPER}}.premium-post-ticker__layout-1 .premium-post-ticker__header-wrapper' => 'align-self: {{VALUE}}',
				),
			)
		);

		$this->add_control(
			'ticker_date_order',
			array(
				'label'     => __( 'Order', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::CHOOSE,
				'toggle'    => false,
				'options'   => array(
					'0' => array(
						'title' => __( 'Before Title', 'premium-addons-for-elementor' ),
						'icon'  => 'eicon-order-start',
					),
					'1' => array(
						'title' => __( 'After Title', 'premium-addons-for-elementor' ),
						'icon'  => 'eicon-order-end',
					),
				),
				'default'   => '0',
				'condition' => array(
					'show_date' => 'yes',
					'layout'    => 'layout-4',
				),
				'selectors' => array(
					'{{WRAPPER}}.premium-post-ticker__layout-4 .premium-post-ticker__date-wrapper' => 'order: {{VALUE}}',
				),
			)
		);

		$this->add_control(
			'date_format',
			array(
				'label'       => __( 'Date Format', 'premium-addons-for-elementor' ),
				'type'        => Controls_Manager::TEXT,
				'label_block' => true,
				'description' => __( 'Know more abour date format from ', 'premium-addons-for-elementor' ) . '<a href="https://wordpress.org/documentation/article/customize-date-and-time-format/" target="_blank">here</a>',
				'default'     => get_option( 'date_format' ),
				'condition'   => array(
					'show_date' => 'yes',
				),
			)
		);

		$this->add_control(
			'hide_date_on',
			array(
				'label'       => __( 'Hide Date On', 'premium-addons-for-elementor' ),
				'type'        => Controls_Manager::SELECT2,
				'options'     => Helper_Functions::get_all_breakpoints(),
				'separator'   => 'after',
				'multiple'    => true,
				'label_block' => true,
				'default'     => array(),
				'condition'   => array(
					'show_date' => 'yes',
				),
			)
		);

		$this->add_control(
			'premium_blog_number_of_posts',
			array(
				'label'     => __( 'Posts To Load', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::NUMBER,
				'min'       => 1,
				'default'   => 4,
				'condition' => array(
					'post_type_filter!' => array( 'stock', 'gold', 'text' ),
				),
			)
		);

		$this->add_control(
			'additional_heading',
			array(
				'label'     => __( 'Additional Settings', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			)
		);

		$this->add_control(
			'reverse',
			array(
				'label'        => __( 'Reverse Direction', 'premium-addons-for-elementor' ),
				'type'         => Controls_Manager::SWITCHER,
				'prefix_class' => 'premium-reversed-',
				'render_type'  => 'template',
			)
		);

		$this->add_control(
			'infinite',
			array(
				'label'   => __( 'Marquee Effect', 'premium-addons-for-elementor' ),
				'type'    => Controls_Manager::SWITCHER,
				'default' => 'yes',
			)
		);

		$this->add_control(
			'separator',
			array(
				'label'     => __( 'Separator', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::SWITCHER,
				'condition' => array(
					'infinite' => 'yes',
					'layout!'  => 'layout-4',
				),
			)
		);

		$this->add_control(
			'typing',
			array(
				'label'        => __( 'Typing Effect', 'premium-addons-for-elementor' ),
				'type'         => Controls_Manager::SWITCHER,
				'render_type'  => 'template',
				'prefix_class' => 'premium-typing-',
				'description'  => __( 'Note: Set the<b> Animaiton Speed </b>option to 0 for better visual.', 'premium-addons-for-elementor' ),
				'condition'    => array(
					'post_type_filter!' => array( 'stock', 'gold' ),
					'infinite!'         => 'yes',
					'layout!'           => 'layout-4',
				),
			)
		);

		$this->add_control(
			'cursor',
			array(
				'label'       => __( 'Typing Cursor', 'premium-addons-for-elementor' ),
				'type'        => Controls_Manager::TEXT,
				'label_block' => true,
				'default'     => '_',
				'selectors'   => array(
					'{{WRAPPER}} .premium-text-typing::after'  => 'content: "{{VALUE}}";',
				),
				'condition'   => array(
					'post_type_filter!' => array( 'stock', 'gold' ),
					'infinite!'         => 'yes',
					'typing'            => 'yes',
					'layout!'           => 'layout-4',
				),
			)
		);

		$this->add_control(
			'entrance_animation',
			array(
				'label'       => __( 'Entrance Animation', 'premium-addons-for-elementor' ),
				'type'        => Controls_Manager::ANIMATION,
				'render_type' => 'template',
				'default'     => '',
				'label_block' => true,
				'condition'   => array(
					'infinite!' => 'yes',
					'layout!'   => 'layout-4',
					'typing!'   => 'yes',
				),
			)
		);

		$this->add_control(
			'ticker_pointer',
			array(
				'label'        => __( 'Title Pointer', 'premium-addons-for-elementor' ),
				'type'         => Controls_Manager::SWITCHER,
				'prefix_class' => 'premium-ticker-pointer-',
				'condition'    => array(
					'ticker_title!' => '',
					'layout'        => array( 'layout-1', 'layout-2' ),
				),
			)
		);

		$this->add_control(
			'ticker_pointer_color',
			array(
				'label'     => __( 'Pointer Color', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'separator' => 'before',
				'selectors' => array(
					'{{WRAPPER}}.premium-ticker-pointer-yes:not(.premium-reversed-yes) .premium-post-ticker__content > div:first-child::after'  => 'border-left-color: {{VALUE}};',
					'{{WRAPPER}}.premium-ticker-pointer-yes.premium-reversed-yes  .premium-post-ticker__content > div:first-child::after'  => 'border-right-color: {{VALUE}};',
				),
				'global'    => array(
					'default' => Global_Colors::COLOR_PRIMARY,
				),
				'condition' => array(
					'ticker_title!'  => '',
					'ticker_pointer' => 'yes',
					'layout'         => array( 'layout-1', 'layout-2' ),
				),
			)
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			array(
				'name'           => 'pointer_bg',
				'description'    => __( 'Pointer Color', 'premium-addons-for-elementor' ),
				'types'          => array( 'classic', 'gradient' ),
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
				'selector'       => '{{WRAPPER}}.premium-post-ticker__layout-3 .premium-post-ticker__header-wrapper::after',
				'condition'      => array(
					'layout'       => 'layout-3',
					'ticker_title' => '',
				),
			)
		);

		$this->add_responsive_control(
			'pointer_width',
			array(
				'label'      => __( 'Pointer Width (px)', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'selectors'  => array(
					'{{WRAPPER}}.premium-ticker-pointer-yes .premium-post-ticker__content > div:first-child::after'  => 'border-top-width: {{SIZE}}px; border-bottom-width: {{SIZE}}px;',
				),
				'condition'  => array(
					'ticker_title!'  => '',
					'ticker_pointer' => 'yes',
					'layout'         => array( 'layout-1', 'layout-2' ),
				),
			)
		);

		$this->add_responsive_control(
			'pointer_height',
			array(
				'label'      => __( 'Pointer Height (px)', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'selectors'  => array(
					'{{WRAPPER}}.premium-ticker-pointer-yes:not(.premium-reversed-yes) .premium-post-ticker__content > div:first-child::after'  => 'border-left-width: {{SIZE}}px;',
					'{{WRAPPER}}.premium-ticker-pointer-yes.premium-reversed-yes .premium-post-ticker__content > div:first-child::after'  => 'border-right-width: {{SIZE}}px;',
				),
				'condition'  => array(
					'ticker_title!'  => '',
					'ticker_pointer' => 'yes',
					'layout'         => array( 'layout-1', 'layout-2' ),
				),
			)
		);

		$this->add_control(
			'ticker_title_tag',
			array(
				'label'       => __( 'Title HTML Tag', 'premium-addons-for-elementor' ),
				'type'        => Controls_Manager::SELECT,
				'default'     => 'h4',
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
				'condition'   => array(
					'ticker_title!' => '',
				),
			)
		);

		$this->end_controls_section();
	}

	/**
	 * Adds query controls.
	 *
	 * @access private
	 * @since 4.9.37
	 */
	private function add_query_section_controls() {

		$this->start_controls_section(
			'pa_spl_query_section',
			array(
				'label' => __( 'Query', 'premium-addons-for-elementor' ),
			)
		);

		$post_types = Posts_Helper::get_posts_types();

		$post_types = array_merge(
			$post_types,
			array(
				'related' => __( 'Related', 'premium-addons-for-elementor' ),
				'stock'   => __( 'Stock Prices', 'premium-addons-for-elementor' ),
				'gold'    => __( 'Gold Prices', 'premium-addons-for-elementor' ),
				'text'    => __( 'Text Content', 'premium-addons-for-elementor' ),
			)
		);

		foreach ( $post_types as $id => $label ) {

			if ( ! in_array( $id, array( 'post', 'text' ), true ) ) {
				$post_types[ $id ] .= apply_filters( 'pa_pro_label', __( ' (Pro)', 'premium-addons-for-elementor' ) );
			}
		}

		$this->add_control(
			'post_type_filter',
			array(
				'label'       => __( 'Source', 'premium-addons-for-elementor' ),
				'type'        => Controls_Manager::SELECT,
				'label_block' => true,
				'options'     => $post_types,
				'default'     => 'post',
			)
		);

		foreach ( $post_types as $key => $type ) {

			// Get all the taxonomies associated with the selected post type.
			$taxonomy = Posts_Helper::get_taxnomies( $key );

			if ( ! empty( $taxonomy ) ) {

				// Get all taxonomy values under the taxonomy.
				foreach ( $taxonomy as $index => $tax ) {

					$terms = get_terms( $index, array( 'hide_empty' => false ) );

					$related_tax = array();

					if ( ! empty( $terms ) ) {

						foreach ( $terms as $t_index => $t_obj ) {

							$related_tax[ $t_obj->slug ] = $t_obj->name;
						}

						// Add filter rule for the each taxonomy.
						$this->add_control(
							$index . '_' . $key . '_filter_rule',
							array(
								/* translators: %s Taxnomy Label */
								'label'       => sprintf( __( '%s Filter Rule', 'premium-addons-for-elementor' ), $tax->label ),
								'type'        => Controls_Manager::SELECT,
								'default'     => 'IN',
								'label_block' => true,
								'options'     => array(
									/* translators: %s: Taxnomy Label */
									'IN'     => sprintf( __( 'Match %s', 'premium-addons-for-elementor' ), $tax->label ),
									/* translators: %s: Taxnomy Label */
									'NOT IN' => sprintf( __( 'Exclude %s', 'premium-addons-for-elementor' ), $tax->label ),
								),
								'condition'   => array(
									'post_type_filter' => $key,
								),
							)
						);

						// Add select control for each taxonomy.
						$this->add_control(
							'tax_' . $index . '_' . $key . '_filter',
							array(
								/* translators: %s Taxnomy Label */
								'label'       => sprintf( __( '%s Filter', 'premium-addons-for-elementor' ), $tax->label ),
								'type'        => Controls_Manager::SELECT2,
								'default'     => '',
								'multiple'    => true,
								'label_block' => true,
								'options'     => $related_tax,
								'condition'   => array(
									'post_type_filter' => $key,
								),
							)
						);

					}
				}
			}
		}

		$this->add_control(
			'author_filter_rule',
			array(
				'label'       => __( 'Filter By Author Rule', 'premium-addons-for-elementor' ),
				'type'        => Controls_Manager::SELECT,
				'default'     => 'author__in',
				'label_block' => true,
				'options'     => array(
					'author__in'     => __( 'Match Authors', 'premium-addons-for-elementor' ),
					'author__not_in' => __( 'Exclude Authors', 'premium-addons-for-elementor' ),
				),
				'condition'   => array(
					'post_type_filter!' => array( 'stock', 'gold', 'text' ),
				),
			)
		);

		$this->add_control(
			'premium_blog_users',
			array(
				'label'       => __( 'Authors', 'premium-addons-for-elementor' ),
				'type'        => Controls_Manager::SELECT2,
				'label_block' => true,
				'multiple'    => true,
				'options'     => Posts_Helper::get_authors(),
				'condition'   => array(
					'post_type_filter!' => array( 'stock', 'gold', 'text' ),
				),
			)
		);

		$this->add_control(
			'posts_filter_rule',
			array(
				'label'       => __( 'Filter By Post Rule', 'premium-addons-for-elementor' ),
				'type'        => Controls_Manager::SELECT,
				'default'     => 'post__not_in',
				'label_block' => true,
				'options'     => array(
					'post__in'     => __( 'Match Post', 'premium-addons-for-elementor' ),
					'post__not_in' => __( 'Exclude Post', 'premium-addons-for-elementor' ),
				),
				'condition'   => array(
					'post_type_filter!' => array( 'stock', 'gold', 'related', 'text' ),
				),
			)
		);

		$this->add_control(
			'premium_blog_posts_exclude',
			array(
				'label'       => __( 'Posts', 'premium-addons-for-elementor' ),
				'type'        => Controls_Manager::SELECT2,
				'label_block' => true,
				'multiple'    => true,
				'options'     => Posts_Helper::get_default_posts_list( 'post' ),
				'condition'   => array(
					'post_type_filter' => 'post',
				),
			)
		);

		$this->add_control(
			'custom_posts_filter',
			array(
				'label'              => __( 'Posts', 'premium-addons-for-elementor' ),
				'type'               => Premium_Post_Filter::TYPE,
				'render_type'        => 'template',
				'label_block'        => true,
				'multiple'           => true,
				'frontend_available' => true,
				'condition'          => array(
					'post_type_filter!' => array( 'post', 'related', 'stock', 'gold', 'text' ),
				),

			)
		);

		$this->add_control(
			'ignore_sticky_posts',
			array(
				'label'     => __( 'Ignore Sticky Posts', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::SWITCHER,
				'label_on'  => __( 'Yes', 'premium-addons-for-elementor' ),
				'label_off' => __( 'No', 'premium-addons-for-elementor' ),
				'default'   => 'yes',
				'condition' => array(
					'post_type_filter!' => array( 'stock', 'gold', 'text' ),
				),
			)
		);

		$this->add_control(
			'premium_blog_offset',
			array(
				'label'       => __( 'Offset', 'premium-addons-for-elementor' ),
				'description' => __( 'This option is used to exclude number of initial posts from being display.', 'premium-addons-for-elementor' ),
				'type'        => Controls_Manager::NUMBER,
				'default'     => '0',
				'min'         => '0',
				'condition'   => array(
					'post_type_filter!' => array( 'stock', 'gold', 'text' ),
				),
			)
		);

		$this->add_control(
			'query_exclude_current',
			array(
				'label'       => __( 'Exclude Current Post', 'premium-addons-for-elementor' ),
				'type'        => Controls_Manager::SWITCHER,
				'description' => __( 'This option will remove the current post from the query.', 'premium-addons-for-elementor' ),
				'label_on'    => __( 'Yes', 'premium-addons-for-elementor' ),
				'label_off'   => __( 'No', 'premium-addons-for-elementor' ),
				'condition'   => array(
					'post_type_filter!' => array( 'stock', 'gold', 'related', 'text' ),
				),
			)
		);

		$this->add_control(
			'premium_blog_order_by',
			array(
				'label'       => __( 'Order By', 'premium-addons-for-elementor' ),
				'type'        => Controls_Manager::SELECT,
				'label_block' => true,
				'options'     => array(
					'none'          => __( 'None', 'premium-addons-for-elementor' ),
					'ID'            => __( 'ID', 'premium-addons-for-elementor' ),
					'author'        => __( 'Author', 'premium-addons-for-elementor' ),
					'title'         => __( 'Title', 'premium-addons-for-elementor' ),
					'name'          => __( 'Name', 'premium-addons-for-elementor' ),
					'date'          => __( 'Date', 'premium-addons-for-elementor' ),
					'modified'      => __( 'Last Modified', 'premium-addons-for-elementor' ),
					'rand'          => __( 'Random', 'premium-addons-for-elementor' ),
					'comment_count' => __( 'Number of Comments', 'premium-addons-for-elementor' ),
				),
				'default'     => 'date',
				'condition'   => array(
					'post_type_filter!' => array( 'stock', 'gold', 'text' ),
				),
			)
		);

		$this->add_control(
			'premium_blog_order',
			array(
				'label'       => __( 'Order', 'premium-addons-for-elementor' ),
				'type'        => Controls_Manager::SELECT,
				'label_block' => true,
				'options'     => array(
					'DESC' => __( 'Descending', 'premium-addons-for-elementor' ),
					'ASC'  => __( 'Ascending', 'premium-addons-for-elementor' ),
				),
				'default'     => 'DESC',
				'condition'   => array(
					'post_type_filter!' => array( 'stock', 'gold', 'text' ),
				),
			)
		);

		$this->add_control(
			'empty_query_text',
			array(
				'label'       => __( 'Empty Query Text', 'premium-addons-for-elementor' ),
				'type'        => Controls_Manager::TEXT,
				'label_block' => true,
				'dynamic'     => array( 'active' => true ),
				'condition'   => array(
					'post_type_filter!' => array( 'stock', 'gold', 'text' ),
				),
			)
		);

		$this->add_control(
			'txt_icon_order',
			array(
				'label'     => __( 'Icon Order', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::CHOOSE,
				'toggle'    => false,
				'options'   => array(
					'0' => array(
						'title' => __( 'After Title', 'premium-addons-for-elementor' ),
						'icon'  => 'eicon-order-start',
					),
					'2' => array(
						'title' => __( 'Before Title', 'premium-addons-for-elementor' ),
						'icon'  => 'eicon-order-end',
					),
				),
				'separator' => 'before',
				'default'   => '0',
				'condition' => array(
					'post_type_filter' => 'text',
				),
				'selectors' => array(
					'{{WRAPPER}} .premium-post-ticker__post-wrapper .premium-post-ticker__post-title' => 'order: {{VALUE}}',
				),
			)
		);

		$this->add_responsive_control(
			'txt_icon_size',
			array(
				'label'      => __( 'Icon Size (px)', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array(
					'px' => array(
						'min' => 0,
						'max' => 500,
					),
				),
				'selectors'  => array(
					'{{WRAPPER}} .premium-post-ticker__icon-wrapper.premium-repeater-item i'  => 'font-size: {{SIZE}}px;',
					'{{WRAPPER}} .premium-post-ticker__icon-wrapper.premium-repeater-item img'  => 'width: {{SIZE}}px',
					'{{WRAPPER}} .premium-post-ticker__icon-wrapper.premium-repeater-item > svg,
                    {{WRAPPER}} .premium-post-ticker__icon-wrapper.premium-repeater-item .premium-lottie-animation,
                    {{WRAPPER}} .premium-post-ticker__icon-wrapper.premium-repeater-item .premium-drawable-icon'  => 'width: {{SIZE}}px; height: {{SIZE}}px; line-height: {{SIZE}}px;',
				),
				'condition'  => array(
					'post_type_filter' => 'text',
				),
			)
		);

		// $this->add_group_control(
		// Group_Control_Image_Size::get_type(),
		// array(
		// 'name'      => 'txt_thumbnail',
		// 'default'   => 'full',
		// 'condition' => array(
		// 'post_type_filter' => 'text',
		// ),
		// )
		// );

		$this->add_responsive_control(
			'txt_icon_spacing',
			array(
				'label'      => __( 'Spacing (px)', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-post-ticker__post-wrapper'  => 'column-gap: {{SIZE}}px',
				),
				'condition'  => array(
					'post_type_filter' => 'text',
				),
			)
		);

		$draw_icon = $this->check_icon_draw();

		$text_repeater = new Repeater();

		$text_repeater->add_control(
			'text',
			array(
				'label'       => __( 'Text', 'premium-addons-pro' ),
				'type'        => Controls_Manager::TEXT,
				'dynamic'     => array( 'active' => true ),
				'label_block' => true,
			)
		);

		$text_repeater->add_control(
			'item_link',
			array(
				'label'       => __( 'Link', 'premium-addons-for-elementor' ),
				'type'        => Controls_Manager::URL,
				'dynamic'     => array( 'active' => true ),
				'default'     => array(
					'url' => '#',
				),
				'placeholder' => 'https://premiumaddons.com/',
				'label_block' => true,
			)
		);

		$text_repeater->add_control(
			'txt_icon_sw',
			array(
				'label'       => __( 'Text Icon', 'premium-addons-for-elementor' ),
				'type'        => Controls_Manager::SWITCHER,
				'render_type' => 'template',
			)
		);

		$text_repeater->add_control(
			'icon_type',
			array(
				'label'       => __( 'Icon Type', 'premium-addons-for-elementor' ),
				'type'        => Controls_Manager::SELECT,
				'render_type' => 'template',
				'options'     => array(
					'icon'   => __( 'Icon', 'premium-addons-for-elementor' ),
					'lottie' => __( 'Lottie Animation', 'premium-addons-for-elementor' ),
					'image'  => __( 'Image', 'premium-addons-for-elementor' ),
					'svg'    => __( 'SVG Code', 'premium-addons-for-elementor' ),
				),
				'default'     => 'icon',
				'condition'   => array(
					'txt_icon_sw' => 'yes',
				),
			)
		);

		$common_conditions = array(
			'txt_icon_sw' => 'yes',
		);

		$text_repeater->add_control(
			'pa_ticker_icon',
			array(
				'label'       => __( 'Choose Icon', 'premium-addons-for-elementor' ),
				'type'        => Controls_Manager::ICONS,
				'label_block' => false,
				'skin'        => 'inline',
				'default'     => array(
					'value'   => 'fas fa-star',
					'library' => 'fa-solid',
				),
				'condition'   => array(
					'txt_icon_sw' => 'yes',
					'icon_type'   => 'icon',
				),
			)
		);

		$text_repeater->add_control(
			'custom_svg',
			array(
				'label'       => __( 'SVG Code', 'premium-addons-for-elementor' ),
				'type'        => Controls_Manager::TEXTAREA,
				'description' => 'You can use these sites to create SVGs: <a href="https://danmarshall.github.io/google-font-to-svg-path/" target="_blank">Google Fonts</a> and <a href="https://boxy-svg.com/" target="_blank">Boxy SVG</a>',
				'condition'   => array(
					'txt_icon_sw' => 'yes',
					'icon_type'   => 'svg',
				),
			)
		);

		$text_repeater->add_control(
			'image',
			array(
				'label'       => __( 'Image', 'premium-addons-for-elementor' ),
				'type'        => Controls_Manager::MEDIA,
				'media_types' => array( 'image' ),
				'dynamic'     => array( 'active' => true ),
				'condition'   => array(
					'txt_icon_sw' => 'yes',
					'icon_type'   => 'image',
				),
			)
		);

		$text_repeater->add_control(
			'lottie_url',
			array(
				'label'       => __( 'Animation JSON URL', 'premium-addons-for-elementor' ),
				'type'        => Controls_Manager::TEXT,
				'dynamic'     => array( 'active' => true ),
				'description' => 'Get JSON code URL from <a href="https://lottiefiles.com/" target="_blank">here</a>',
				'label_block' => true,
				'condition'   => array(
					'txt_icon_sw' => 'yes',
					'icon_type'   => 'lottie',
				),
			)
		);

		$animation_conds = array(
			'terms' => array(
				array(
					'name'  => 'txt_icon_sw',
					'value' => 'yes',
				),
				array(
					'relation' => 'or',
					'terms'    => array(
						array(
							'name'  => 'icon_type',
							'value' => 'lottie',
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

		$text_repeater->add_control(
			'draw_svg',
			array(
				'label'     => __( 'Draw Icon', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::SWITCHER,
				'classes'   => $draw_icon ? '' : 'editor-pa-control-disabled',
				'condition' => array(
					'txt_icon_sw'              => 'yes',
					'icon_type'                => array( 'icon', 'svg' ),
					'pa_ticker_icon[library]!' => 'svg',
				),
			)
		);

		if ( $draw_icon ) {

			$text_repeater->add_control(
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
						'txt_icon_sw' => 'yes',
						'icon_type'   => array( 'icon', 'svg' ),
					),
					'selectors' => array(
						'{{WRAPPER}} {{CURRENT_ITEM}} .premium-post-ticker__icon-wrapper svg *' => 'stroke-width: {{SIZE}}',
					),
				)
			);

		} else {
			Helper_Functions::get_draw_svg_notice(
				$text_repeater,
				'ticker',
				array(
					'txt_icon_sw'              => 'yes',
					'icon_type'                => array( 'icon', 'svg' ),
					'pa_ticker_icon[library]!' => 'svg',
				)
			);
		}

		$text_repeater->add_control(
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
			'text_content',
			array(
				'label'         => __( 'Text', 'premium-addons-pro' ),
				'type'          => Controls_Manager::REPEATER,
				'default'       => array(
					array(
						'text' => 'Premium News Ticker',
					),
					array(
						'text' => 'Premium Addons For Elementor',
					),
				),
				'fields'        => $text_repeater->get_controls(),
				'title_field'   => '{{{ text }}}',
				'prevent_empty' => false,
				'condition'     => array(
					'post_type_filter' => 'text',
				),
			)
		);

		$papro_activated = apply_filters( 'papro_activated', false );

		if ( $papro_activated ) {

			do_action( 'pa_ticker_stock_query', $this );

		}

		$this->end_controls_section();
	}

	/**
	 * Adds Posts controls.
	 *
	 * @access private
	 * @since 4.9.37
	 */
	private function add_posts_section_controls() {

		$this->start_controls_section(
			'pa_ticker_posts_section',
			array(
				'label'     => __( 'Post Options', 'premium-addons-for-elementor' ),
				'condition' => array(
					'post_type_filter!' => array( 'stock', 'gold', 'text' ),
				),
			)
		);

		$this->add_control(
			'new_tab',
			array(
				'label'   => __( 'Open Post Link in New Tab', 'premium-addons-for-elementor' ),
				'type'    => Controls_Manager::SWITCHER,
				'default' => 'yes',
			)
		);

		$this->add_control(
			'article_tag_switcher',
			array(
				'label' => __( 'Change Post HTML Tag To Article', 'premium-addons-for-elementor' ),
				'type'  => Controls_Manager::SWITCHER,
			)
		);

		$this->add_control(
			'content_length',
			array(
				'label'       => __( 'Content Length (words)', 'premium-addons-for-elementor' ),
				'description' => __( 'Set the number of words of the content, leave it empty to display the full length.', 'premium-addons-for-elementor' ),
				'type'        => Controls_Manager::NUMBER,
				'min'         => '0',
			)
		);

		$this->add_control(
			'post_img',
			array(
				'label' => __( 'Show Post Thumbnail', 'premium-addons-for-elementor' ),
				'type'  => Controls_Manager::SWITCHER,
			)
		);

		$this->add_group_control(
			Group_Control_Image_Size::get_type(),
			array(
				'name'      => 'image',
				'default'   => 'thumbnail',
				'condition' => array(
					'post_img' => 'yes',
				),
			)
		);

		$this->add_responsive_control(
			'img_width',
			array(
				'label'      => __( 'Width (px)', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', 'em' ),
				'range'      => array(
					'px' => array(
						'min' => 0,
						'max' => 500,
					),
				),
				'selectors'  => array(
					'{{WRAPPER}} .premium-post-ticker__thumbnail-wrapper img'  => 'width: {{SIZE}}{{UNIT}};',
				),
				'condition'  => array(
					'post_img' => 'yes',
				),
			)
		);

		$this->add_control(
			'hide_thumb_on',
			array(
				'label'       => __( 'Hide Thumbnail On', 'premium-addons-for-elementor' ),
				'type'        => Controls_Manager::SELECT2,
				'options'     => Helper_Functions::get_all_breakpoints(),
				'separator'   => 'after',
				'multiple'    => true,
				'label_block' => true,
				'default'     => array(),
				'condition'   => array(
					'post_img' => 'yes',
				),
			)
		);

		$this->add_control(
			'author_meta',
			array(
				'label' => __( 'Author Meta', 'premium-addons-for-elementor' ),
				'type'  => Controls_Manager::SWITCHER,
			)
		);

		$this->add_control(
			'hide_author_on',
			array(
				'label'       => __( 'Hide Author On', 'premium-addons-for-elementor' ),
				'type'        => Controls_Manager::SELECT2,
				'options'     => Helper_Functions::get_all_breakpoints(),
				'separator'   => 'after',
				'multiple'    => true,
				'label_block' => true,
				'default'     => array(),
				'condition'   => array(
					'author_meta' => 'yes',
				),
			)
		);

		$this->add_control(
			'date_meta',
			array(
				'label'   => __( 'Date Meta', 'premium-addons-for-elementor' ),
				'type'    => Controls_Manager::SWITCHER,
				'default' => 'yes',
			)
		);

		$this->add_control(
			'post_date_format',
			array(
				'label'       => __( 'Date Format', 'premium-addons-for-elementor' ),
				'type'        => Controls_Manager::TEXT,
				'label_block' => true,
				'description' => __( 'Know more abour date format from ', 'premium-addons-for-elementor' ) . '<a href="https://wordpress.org/documentation/article/customize-date-and-time-format/" target="_blank">here</a>',
				'default'     => get_option( 'date_format' ),
				'condition'   => array(
					'date_meta' => 'yes',
				),
			)
		);

		$this->add_control(
			'hide_post_date_on',
			array(
				'label'       => __( 'Hide Date On', 'premium-addons-for-elementor' ),
				'type'        => Controls_Manager::SELECT2,
				'options'     => Helper_Functions::get_all_breakpoints(),
				'multiple'    => true,
				'label_block' => true,
				'default'     => array(),
				'condition'   => array(
					'date_meta' => 'yes',
				),
			)
		);

		$this->end_controls_section();
	}

	private function add_slider_section_controls() {

		$this->start_controls_section(
			'slider_section_tab',
			array(
				'label' => __( 'Animation Settings', 'premium-addons-for-elementor' ),
			)
		);

		$this->add_control(
			'slides_to_show',
			array(
				'label'     => __( 'Slides To Show', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::NUMBER,
				'default'   => 3,
				'condition' => array(
					'layout' => 'layout-4',
				),
			)
		);

		$this->add_control(
			'vertical',
			array(
				'label'     => __( 'Vertical', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::SWITCHER,
				'condition' => array(
					'fade!'   => 'yes',
					'layout!' => 'layout-4',
				),
			)
		);

		$this->add_control(
			'fade',
			array(
				'label'        => __( 'Fade', 'premium-addons-for-elementor' ),
				'type'         => Controls_Manager::SWITCHER,
				'prefix_class' => 'premium-fade-',
				'render_type'  => 'template',
				'condition'    => array(
					'layout!'   => 'layout-4',
					'infinite!' => 'yes',
					'typing!'   => 'yes',
				),
			)
		);

		$this->add_control(
			'auto_play',
			array(
				'label'     => __( 'Autoplay', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::SWITCHER,
				'default'   => 'yes',
				'condition' => array(
					'infinite!' => 'yes',
				),
			)
		);

		$this->add_control(
			'autoplay_speed',
			array(
				'label'     => __( 'Autoplay Speed (ms)', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::NUMBER,
				'default'   => 5000,
				'condition' => array(
					'auto_play' => 'yes',
					'infinite!' => 'yes',
				),
			)
		);

		$this->add_control(
			'speed',
			array(
				'label'       => __( 'Animation Speed (ms)', 'premium-addons-for-elementor' ),
				'description' => __( 'Set the speed of the animation in milliseconds (ms)', 'premium-addons-for-elementor' ),
				'type'        => Controls_Manager::NUMBER,
				'default'     => 5000,
				'render_type' => 'template',
			)
		);

		$this->add_control(
			'carousel_arrows',
			array(
				'label'     => __( 'Navigation Arrows', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::SWITCHER,
				'default'   => 'yes',
				'condition' => array(
					'infinite!' => 'yes',
				),
			)
		);

		$this->add_control(
			'pause_on_hover',
			array(
				'label'      => __( 'Pause On Hover', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::SWITCHER,
				'default'    => 'yes',
				'conditions' => array(
					'relation' => 'or',
					'terms'    => array(
						array(
							'name'  => 'auto_play',
							'value' => 'yes',
						),
						array(
							'name'  => 'infinite',
							'value' => 'yes',
						),
					),
				),
			)
		);

		$this->end_controls_section();
	}

	private function add_helpful_info_section() {

		$this->start_controls_section(
			'section_pa_docs',
			array(
				'label' => __( 'Helpful Documentations', 'premium-addons-for-elementor' ),
			)
		);

		$docs = array(
			'https://premiumaddons.com/docs/elementor-news-ticker-widget/' => __( 'Getting started »', 'premium-addons-for-elementor' ),
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

	}

	/** Style Controls.*/

	/**
	 * Adds posts title style controls.
	 *
	 * @access private
	 * @since 4.9.37
	 */
	private function add_ticker_title_style() {

		$draw_icon = $this->check_icon_draw();

		$this->start_controls_section(
			'pa_ticker_title_tab',
			array(
				'label'     => __( 'Title', 'premium-addons-for-elementor' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => array(
					'ticker_title!' => '',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'ticker_title_typo',
				'selector' => '{{WRAPPER}} .premium-post-ticker__title',
			)
		);

		$this->add_control(
			'ticker_title_color',
			array(
				'label'     => __( 'Color', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .premium-post-ticker__title' => 'color: {{VALUE}}',
				),
			)
		);

		$this->add_control(
			'title_icon_color',
			array(
				'label'     => __( 'Icon Color', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .premium-post-ticker__icon-wrapper:not(.premium-repeater-item) i' => 'color: {{VALUE}}',
					'{{WRAPPER}} .premium-post-ticker__icon-wrapper:not(.premium-repeater-item) .premium-drawable-icon *,
                    {{WRAPPER}} .premium-post-ticker__icon-wrapper:not(.premium-repeater-item) svg:not([class*="premium-"])' => 'fill: {{VALUE}};',
				),
				'condition' => array(
					'ticker_icon_sw' => 'yes',
					'icon_type'      => 'icon',
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
						'{{WRAPPER}} .premium-post-ticker__icon-wrapper:not(.premium-repeater-item) .premium-drawable-icon *,
                        {{WRAPPER}} .premium-post-ticker__icon-wrapper:not(.premium-repeater-item) svg:not([class*="premium-"])' => 'stroke: {{VALUE}};',
					),
				)
			);
		}

		$this->add_group_control(
			Group_Control_Text_Shadow::get_type(),
			array(
				'name'     => 'ticker_title_text_shadow',
				'selector' => '{{WRAPPER}} .premium-post-ticker__title',
			)
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			array(
				'name'           => 'ticker_title_bg',
				'types'          => array( 'classic', 'gradient' ),
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
				'selector'       => '{{WRAPPER}} .premium-post-ticker__title-wrapper, {{WRAPPER}}.premium-post-ticker__layout-2 .premium-post-ticker__title-wrapper::after, {{WRAPPER}}.premium-post-ticker__layout-3 .premium-post-ticker__header-wrapper::after',
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'      => 'ticker_title_border',
				'selector'  => '{{WRAPPER}} .premium-post-ticker__title-wrapper',
				'condition' => array(
					'layout!' => 'layout-3',
				),
			)
		);

		$this->add_control(
			'ticker_title_border_radius',
			array(
				'label'      => __( 'Border Radius', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-post-ticker__title-wrapper'  => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
				'condition'  => array(
					'layout!'           => 'layout-3',
					'title_adv_radius!' => 'yes',
				),
			)
		);

		$this->add_control(
			'title_adv_radius',
			array(
				'label'       => __( 'Advanced Border Radius', 'premium-addons-for-elementor' ),
				'type'        => Controls_Manager::SWITCHER,
				'description' => __( 'Apply custom radius values. Get the radius value from ', 'premium-addons-for-elementor' ) . '<a href="https://9elements.github.io/fancy-border-radius/" target="_blank">here</a>',
			)
		);

		$this->add_control(
			'title_adv_radius_value',
			array(
				'label'     => __( 'Border Radius', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::TEXT,
				'dynamic'   => array( 'active' => true ),
				'selectors' => array(
					'{{WRAPPER}} .premium-post-ticker__title-wrapper' => 'border-radius: {{VALUE}};',
				),
				'condition' => array(
					'title_adv_radius' => 'yes',
				),
			)
		);

		$this->add_responsive_control(
			'ticker_title_padding',
			array(
				'label'      => __( 'Padding', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-post-ticker__title-wrapper' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_section();
	}

	private function add_ticker_date_style() {

		$this->start_controls_section(
			'pa_ticker_date_tab',
			array(
				'label'     => __( 'Date', 'premium-addons-for-elementor' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => array(
					'show_date' => 'yes',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'date_typo',
				'selector' => '{{WRAPPER}} .premium-post-ticker__date-wrapper',
			)
		);

		$this->add_control(
			'date_color',
			array(
				'label'     => __( 'Color', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .premium-post-ticker__date' => 'color: {{VALUE}}',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Text_Shadow::get_type(),
			array(
				'name'     => 'date_text_shadow',
				'selector' => '{{WRAPPER}} .premium-post-ticker__date-wrapper',
			)
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			array(
				'name'     => 'date_bg',
				'types'    => array( 'classic', 'gradient' ),
				'selector' => '{{WRAPPER}} .premium-post-ticker__date-wrapper',
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'      => 'date_border',
				'selector'  => '{{WRAPPER}} .premium-post-ticker__date-wrapper',
				'condition' => array(
					'layout!' => 'layout-3',
				),
			)
		);

		$this->add_control(
			'date_border_radius',
			array(
				'label'      => __( 'Border Radius', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-post-ticker__date-wrapper'  => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
				'condition'  => array(
					'layout!' => 'layout-3',
				),
			)
		);

		$this->add_responsive_control(
			'date_padding',
			array(
				'label'      => __( 'Padding', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-post-ticker__date-wrapper' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'date_margin',
			array(
				'label'      => __( 'Margin', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-post-ticker__date-wrapper' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
				'condition'  => array(
					'layout!' => 'layout-3',
				),
			)
		);

		$this->end_controls_section();
	}

	private function add_posts_style() {

		$draw_icon = $this->check_icon_draw();

		$this->start_controls_section(
			'pa_posts_style',
			array(
				'label' => __( 'Ticker Element', 'premium-addons-for-elementor' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		// content.
		$this->add_control(
			'pa_post_title_heading',
			array(
				'label'     => __( 'Content', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
				'condition' => array(
					'post_type_filter!' => array( 'gold', 'stock' ),
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'      => 'pa_post_title_typo',
				'selector'  => '{{WRAPPER}} .premium-post-ticker__post-title a',
				'condition' => array(
					'post_type_filter!' => array( 'gold', 'stock' ),
				),
			)
		);

		$this->add_control(
			'pa_post_title_color',
			array(
				'label'     => __( 'Color', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .premium-post-ticker__post-title a, {{WRAPPER}} .premium-text-typing::after'  => 'color: {{VALUE}};',
				),
				'condition' => array(
					'post_type_filter!' => array( 'gold', 'stock' ),
				),
			)
		);

		$this->add_control(
			'pa_post_title_color_hov',
			array(
				'label'     => __( 'Hover Color', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .premium-post-ticker__post-title:hover a, {{WRAPPER}} .premium-text-typing:hover::after'  => 'color: {{VALUE}};',
				),
				'condition' => array(
					'post_type_filter!' => array( 'gold', 'stock' ),
				),
			)
		);

		$this->add_control(
			'text_icon_color',
			array(
				'label'     => __( 'Icon Color', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .premium-post-ticker__icon-wrapper.premium-repeater-item i' => 'color: {{VALUE}}',
					'{{WRAPPER}} .premium-post-ticker__icon-wrapper.premium-repeater-item .premium-drawable-icon *,
                    {{WRAPPER}} .premium-post-ticker__icon-wrapper.premium-repeater-item svg:not([class*="premium-"])' => 'fill: {{VALUE}};',
				),
				'condition' => array(
					'post_type_filter' => 'text',
				),
			)
		);

		if ( $draw_icon ) {
			$this->add_control(
				'text_stroke_color',
				array(
					'label'     => __( 'Stroke Color', 'premium-addons-for-elementor' ),
					'type'      => Controls_Manager::COLOR,
					'global'    => array(
						'default' => Global_Colors::COLOR_ACCENT,
					),
					'condition' => array(
						'post_type_filter' => 'text',
					),
					'selectors' => array(
						'{{WRAPPER}} .premium-drawable-icon *, {{WRAPPER}} svg:not([class*="premium-"])' => 'stroke: {{VALUE}};',
					),
				)
			);
		}

		// Date.
		$this->add_control(
			'pa_post_date_heading',
			array(
				'label'     => __( 'Date', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
				'condition' => array(
					'date_meta'         => 'yes',
					'post_type_filter!' => array( 'gold', 'stock', 'text' ),
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'      => 'pa_post_date_typo',
				'global'    => array(
					'default' => Global_Typography::TYPOGRAPHY_SECONDARY,
				),
				'selector'  => '{{WRAPPER}} .premium-post-ticker__post-date span',
				'condition' => array(
					'date_meta'         => 'yes',
					'post_type_filter!' => array( 'gold', 'stock', 'text' ),
				),
			)
		);

		$this->add_control(
			'pa_post_date_color',
			array(
				'label'     => __( 'Color', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .premium-post-ticker__post-date span'  => 'color: {{VALUE}};',
				),
				'condition' => array(
					'date_meta'         => 'yes',
					'post_type_filter!' => array( 'gold', 'stock', 'text' ),
				),
			)
		);

		// Author.
		$this->add_control(
			'pa_author_heading',
			array(
				'label'     => __( 'Author', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
				'condition' => array(
					'author_meta'       => 'yes',
					'post_type_filter!' => array( 'gold', 'stock', 'text' ),
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'           => 'pa_author_typo',
				'global'         => array(
					'default' => Global_Typography::TYPOGRAPHY_SECONDARY,
				),
				'fields_options' => array(
					'font_size' => array(
						'selectors' => array(
							'{{WRAPPER}} .premium-post-ticker__post-author a, {{WRAPPER}} .premium-post-ticker__post-author i' => 'font-size: {{SIZE}}{{UNIT}}',
						),
					),
				),
				'selector'       => '{{WRAPPER}} .premium-post-ticker__post-author a',
				'condition'      => array(
					'author_meta'       => 'yes',
					'post_type_filter!' => array( 'gold', 'stock', 'text' ),
				),
			)
		);

		$this->add_control(
			'pa_author_color',
			array(
				'label'     => __( 'Color', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .premium-post-ticker__post-author *'  => 'color: {{VALUE}};',
				),
				'condition' => array(
					'author_meta'       => 'yes',
					'post_type_filter!' => array( 'gold', 'stock', 'text' ),
				),
			)
		);

		$this->add_control(
			'pa_author_color_hov',
			array(
				'label'     => __( 'Hover Color', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .premium-post-ticker__post-author:hover *'  => 'color: {{VALUE}};',
				),
				'condition' => array(
					'author_meta'       => 'yes',
					'post_type_filter!' => array( 'gold', 'stock', 'text' ),
				),
			)
		);

		// featured image.
		$this->add_control(
			'pa_post_img',
			array(
				'label'     => __( 'Featured Image', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
				'condition' => array(
					'post_img'          => 'yes',
					'post_type_filter!' => array( 'gold', 'stock', 'text' ),
				),
			)
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name'      => 'pa_post_img_shadow',
				'selector'  => '{{WRAPPER}} .premium-post-ticker__thumbnail-wrapper',
				'condition' => array(
					'post_img'          => 'yes',
					'post_type_filter!' => array( 'gold', 'stock', 'text' ),
				),
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'      => 'pa_post_img_border',
				'selector'  => '{{WRAPPER}} .premium-post-ticker__thumbnail-wrapper',
				'condition' => array(
					'post_img'          => 'yes',
					'post_type_filter!' => array( 'gold', 'stock', 'text' ),
				),
			)
		);

		$this->add_control(
			'pa_post_img_border_radius',
			array(
				'label'      => __( 'Border Radius', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-post-ticker__thumbnail-wrapper, {{WRAPPER}} .premium-post-ticker__thumbnail-wrapper *'  => 'border-radius: {{SIZE}}{{UNIT}};',
				),
				'condition'  => array(
					'post_img'          => 'yes',
					'post_type_filter!' => array( 'gold', 'stock', 'text' ),
				),
			)
		);

		$this->add_responsive_control(
			'pa_post_img_margin',
			array(
				'label'      => __( 'Margin', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-post-ticker__thumbnail-wrapper' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
				'condition'  => array(
					'post_img'          => 'yes',
					'post_type_filter!' => array( 'gold', 'stock', 'text' ),
				),
			)
		);

		$papro_activated = apply_filters( 'papro_activated', false );

		if ( $papro_activated ) {
			do_action( 'pa_ticker_stock_style', $this );
		}

		// box / container.
		$this->add_control(
			'pa_post_box_heading',
			array(
				'label'     => __( 'Container', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			)
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name'     => 'pa_post_box_shadow',
				'selector' => '{{WRAPPER}} .premium-post-ticker__post-wrapper',
			)
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			array(
				'name'     => 'pa_post_box_bg',
				'types'    => array( 'classic', 'gradient' ),
				'selector' => '{{WRAPPER}} .premium-post-ticker__post-wrapper, {{WRAPPER}}:not(.premium-post-ticker__layout-4) .premium-post-ticker__arrows, {{WRAPPER}}.premium-post-ticker__layout-3 .premium-post-ticker__content',
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'     => 'pa_post_box_border',
				'selector' => '{{WRAPPER}} .premium-post-ticker__post-wrapper',
			)
		);

		$this->add_control(
			'pa_post_box_border_radius',
			array(
				'label'      => __( 'Border Radius', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-post-ticker__post-wrapper'  => 'border-radius: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'pa_post_box_padding',
			array(
				'label'      => __( 'Padding', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-post-ticker__post-wrapper' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'pa_post_box_magin',
			array(
				'label'      => __( 'Margin', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-post-ticker__post-wrapper' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_section();
	}

	private function add_posts_container_style() {

		$this->start_controls_section(
			'posts_container_style',
			array(
				'label' => __( 'Ticker Elements Container', 'premium-addons-for-elementor' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			array(
				'name'     => 'posts_container_background',
				'types'    => array( 'classic', 'gradient' ),
				'selector' => '{{WRAPPER}} .premium-post-ticker__content',
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'     => 'posts_container_border',
				'selector' => '{{WRAPPER}} .premium-post-ticker__content',
			)
		);

		$this->add_control(
			'posts_container_border_radius',
			array(
				'label'      => __( 'Border Radius', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', '%', 'em' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-post-ticker__content'     => 'border-radius: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name'     => 'posts_container_shadow',
				'selector' => '{{WRAPPER}} .premium-post-ticker__content',
			)
		);

		$this->add_responsive_control(
			'posts_container_margin',
			array(
				'label'      => __( 'Margin', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-post-ticker__content' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}',
				),
			)
		);

		$this->add_responsive_control(
			'posts_container_padding',
			array(
				'label'      => __( 'Padding', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-post-ticker__content' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}',
				),
			)
		);

		$this->end_controls_section();

	}

	/**
	 * Adds pagination style controls.
	 *
	 * @access private
	 * @since 4.9.37
	 */
	private function add_navigation_style() {

		$this->start_controls_section(
			'pa_nav_style',
			array(
				'label'     => __( 'Arrows', 'premium-addons-for-elementor' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => array(
					'carousel_arrows' => 'yes',
				),
			)
		);

		$this->add_responsive_control(
			'nav_icon_size',
			array(
				'label'      => __( 'Icon Size', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', '%', 'em' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-post-ticker__arrows i' => 'font-size: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .premium-post-ticker__arrows a' => 'line-height: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->start_controls_tabs( 'nav_colors' );

		$this->start_controls_tab(
			'pa_nav_nomral',
			array(
				'label' => __( 'Normal', 'premium-addons-for-elementor' ),
			)
		);

		$this->add_control(
			'pa_nav_color',
			array(
				'label'     => __( 'Color', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'global'    => array(
					'default' => Global_Colors::COLOR_SECONDARY,
				),
				'selectors' => array(
					'{{WRAPPER}} .premium-post-ticker__arrows a' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'pa_nav_bg',
			array(
				'label'     => __( 'Background Color', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .premium-post-ticker__arrows a' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'     => 'pa_nav_border',
				'selector' => '{{WRAPPER}} .premium-post-ticker__arrows a',
			)
		);

		$this->add_control(
			'pa_nav_border_radius',
			array(
				'label'      => __( 'Border Radius', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', '%', 'em' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-post-ticker__arrows a' => 'border-radius: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'premium_blog_pa_nav_hover',
			array(
				'label' => __( 'Hover', 'premium-addons-for-elementor' ),

			)
		);

		$this->add_control(
			'pa_nav_color_hov',
			array(
				'label'     => __( 'Color', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'global'    => array(
					'default' => Global_Colors::COLOR_PRIMARY,
				),
				'selectors' => array(
					'{{WRAPPER}} .premium-post-ticker__arrows a:hover' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'pa_nav_bg_hov',
			array(
				'label'     => __( 'Background Color', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'global'    => array(
					'default' => Global_Colors::COLOR_SECONDARY,
				),
				'selectors' => array(
					'{{WRAPPER}} .premium-post-ticker__arrows a:hover' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'     => 'pa_nav_border_hov',
				'selector' => '{{WRAPPER}} .premium-post-ticker__arrows a:hover',
			)
		);

		$this->add_control(
			'pa_nav_border_radius_hov',
			array(
				'label'      => __( 'Border Radius', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', '%', 'em' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-post-ticker__arrows a:hover' => 'border-radius: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->add_responsive_control(
			'pa_nav_margin',
			array(
				'label'      => __( 'Spacing', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'separator'  => 'before',
				'selectors'  => array(
					'{{WRAPPER}} .premium-post-ticker__arrows'  => 'column-gap: {{SIZE}}px;',
				),
			)
		);

		$this->add_responsive_control(
			'pa_nav_padding',
			array(
				'label'      => __( 'Padding', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-post-ticker__arrows a'  => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'arrows_container',
			array(
				'label'     => __( 'Outer Container', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'     => 'arrow_container_border',
				'selector' => '{{WRAPPER}} .premium-post-ticker__arrows',
			)
		);

		$this->add_control(
			'arrow_container_rad',
			array(
				'label'      => __( 'Border Radius', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', '%', 'em' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-post-ticker__arrows' => 'border-radius: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'arrow_container_padding',
			array(
				'label'      => __( 'Padding', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-post-ticker__arrows'  => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'arrow_container_margin',
			array(
				'label'      => __( 'Margin', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-post-ticker__arrows'  => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_section();
	}

	/**
	 * Adds separator style controls.
	 *
	 * @access private
	 * @since 4.9.37
	 */
	private function add_separator_style() {

		$this->start_controls_section(
			'pa_separator_style',
			array(
				'label'     => __( 'Separator', 'premium-addons-for-elementor' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => array(
					'infinite'  => 'yes',
					'separator' => 'yes',
					'layout!'   => 'layout-4',
				),
			)
		);

		$this->add_responsive_control(
			'separator_position',
			array(
				'label'      => __( 'Position', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', 'em', '%', 'custom' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-post-ticker__separator'    => 'right: {{SIZE}}{{UNIT}}',
				),
			)
		);

		$this->add_responsive_control(
			'separator_width',
			array(
				'label'      => __( 'Width', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', 'em', 'custom' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-post-ticker__separator'    => 'width: {{SIZE}}{{UNIT}}',
				),
			)
		);

		$this->add_responsive_control(
			'separator_height',
			array(
				'label'      => __( 'Height', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', 'em', 'custom' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-post-ticker__separator'    => 'height: {{SIZE}}{{UNIT}} !important',
				),
			)
		);

		$this->add_control(
			'separator_color',
			array(
				'label'     => __( 'Color', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .premium-post-ticker__separator' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'separator_rad',
			array(
				'label'      => __( 'Border Radius', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', '%', 'em' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-post-ticker__separator' => 'border-radius: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_section();
	}

	/**
	 * Render post ticker widget output on the frontend.
	 * Written in PHP and used to generate the final HTML.
	 *
	 * @since 1.0.0
	 * @access protected
	 */
	protected function render() {

		$settings = $this->get_settings_for_display();

		$papro_activated = apply_filters( 'papro_activated', false );

		if ( ! $papro_activated && ( in_array( $settings['layout'], array( 'layout-3', 'layout-4' ), true ) || ! in_array( $settings['post_type_filter'], array( 'post', 'text' ), true ) ) ) {
			?>
			<div class="premium-error-notice">
				<?php
					$message = __( 'This option is available in <b>Premium Addons Pro</b>.', 'premium-addons-for-elementor' );
					echo wp_kses_post( $message );
				?>
			</div>
			<?php
			return false;
		}

		$id = $this->get_id();

		$settings['widget_id']   = $id;
		$settings['widget_type'] = 'premium-post-ticker';

		$source = $settings['post_type_filter'];

		if ( 'stock' === $source ) {

			$api_key = apply_filters( 'pa_stock_api', $settings['api_key'] );

			if ( empty( $api_key ) ) {

				$notice = __( 'Please enter a valid API key.', 'premium-addons-for-elementor' );
				?>
					<div class="premium-error-notice">
						<?php echo wp_kses_post( $notice ); ?>
					</div>
				<?php
				return;
			}

			$function         = $settings['req_function'];
			$symbol           = 'CURRENCY_EXCHANGE_RATE' === $function ? $settings['from_currency'] : $settings['symbol'];
			$to_currency      = '';
			$show_curr_change = false;

			if ( empty( $symbol ) ) {
				$notice = __( 'Please Enter symbols/tokens to query of your choice.', 'premium-addons-for-elementor' );
				?>
					<div class="premium-error-notice">
						<?php echo wp_kses_post( $notice ); ?>
					</div>
				<?php
				return;
			}

			if ( 'CURRENCY_EXCHANGE_RATE' === $function ) {
				$to_currency      = $settings['to_currency'];
				$show_curr_change = 'yes' === $settings['curr_change'] ? true : false;

				if ( empty( $to_currency ) ) {
					$notice = __( 'Please Enter symbols/tokens to exchange to.', 'premium-addons-for-elementor' );
					?>
						<div class="premium-error-notice">
							<?php echo wp_kses_post( $notice ); ?>
						</div>
					<?php
					return;
				}
			}

			$transient_name = sprintf( 'papro_%s_%s_%s_%s_%s', $id, $api_key, $source, $symbol, $to_currency );

			$req_data = get_transient( $transient_name );

			if ( ! $req_data ) {

				sleep( 2 );

				$api_settings = array(
					'id'          => $id,
					'api_key'     => $api_key,
					'source'      => $source,
					'function'    => $function,
					'symbols'     => $this->extract_stock_symbols( $symbol ),
					'to_currency' => $this->extract_stock_symbols( $to_currency ),
				);

				if ( $show_curr_change ) {
					$api_settings['old_data_key'] = $transient_name . '_old';
				}

				$api_handler = new API_Handler( $api_settings );

				$req_data = $api_handler::get_req_data( $api_settings );

				if ( ! $req_data ) {
					return;
				}

				$expire_time = HOUR_IN_SECONDS * apply_filters( 'pa_ticker_reload_' . $id, $settings['reload'] );

				$api_handler::delete_existing_transient();

				set_transient( $transient_name, $req_data, $expire_time );

				if ( $show_curr_change ) {
					update_option( $transient_name . '_old', $req_data );
				}
			}
		} elseif ( 'gold' === $source ) {

			$api_key = apply_filters( 'pa_gold_api', $settings['gold_api_key'] );

			if ( empty( $api_key ) ) {

				$notice = __( 'Please enter a valid API key.', 'premium-addons-for-elementor' );
				?>
					<div class="premium-error-notice">
						<?php echo wp_kses_post( $notice ); ?>
					</div>
				<?php
				return;
			}

			$currencies = $settings['currencies'];

			if ( empty( $currencies ) ) {
				$notice = __( 'Please Enter Currencies of your choice to query.', 'premium-addons-for-elementor' );
				?>
					<div class="premium-error-notice">
						<?php echo wp_kses_post( $notice ); ?>
					</div>
				<?php
				return;
			}

			$transient_name = sprintf( 'papro_%s_%s_%s_%s', $id, $api_key, $source, $currencies );

			$req_data = get_transient( $transient_name );

			if ( ! $req_data ) {

				sleep( 2 );

				$api_settings = array(
					'id'          => $id,
					'api_key'     => $api_key,
					'to_currency' => $currencies,
				);

				$api_handler = new API_Handler( $api_settings );

				$will_alter = false;

				$req_data = $api_handler::get_gold_data( $api_settings, $will_alter );

				if ( isset( $req_data['is_error_msg'] ) && $req_data['is_error_msg'] ) {

					if ( ! empty( $settings['alter_api_key'] ) ) {

						$will_alter = true;

						$api_settings['api_key'] = $settings['alter_api_key'];

						$req_data = $api_handler::get_gold_data( $api_settings, $will_alter );

						if ( ! $req_data ) {
							return;
						}
					} else {

						$err_msg = sprintf( 'Something went wrong: %s', $req_data['error_msg'] );
						?>
							<div class="premium-error-notice">
								<?php echo wp_kses_post( $err_msg ); ?>
							</div>
						<?php
						return;
					}
				}

				$expire_time = HOUR_IN_SECONDS * apply_filters( 'pa_ticker_reload_' . $id, $settings['gold_reload'] );

				$api_handler::delete_existing_transient();

				set_transient( $transient_name, $req_data, $expire_time );
			}
		} elseif ( 'text' === $source ) {

			$text_content = $settings['text_content'];

		} else {
			$posts_helper = Posts_Helper::getInstance();

			$posts_helper->set_widget_settings( $settings, '*' );

			$query = $posts_helper->get_query_posts();

			if ( ! $query->have_posts() ) {

				$query_notice = $settings['empty_query_text'];

				$posts_helper->get_empty_query_message( $query_notice );
				return;
			}
		}

		$layout       = $settings['layout'];
		$title        = ! empty( $settings['ticker_title'] ) ? $settings['ticker_title'] : false;
		$current_date = 'yes' === $settings['show_date'] ? true : false;

		// slider settings.
		$infinite  = 'yes' === $settings['infinite'];
		$auto_play = 'yes' === $settings['auto_play'];
		$arrows    = 'yes' === $settings['carousel_arrows'];
		$fade      = 'yes' === $settings['fade'] && 'layout-4' !== $layout ? true : false;
		$typing    = ! in_array( $source, array( 'stock', 'gold' ), true ) && 'layout-4' !== $settings['layout'] && 'yes' === $settings['typing'] ? true : false;
		$vertical  = 'yes' === $settings['vertical'];
		$speed     = $settings['speed'];

		if ( $fade ) {
			$vertical = false;
		}

		if ( $typing ) {
			$vertical = false;
			$fade     = false;
		}

		if ( $infinite ) {
			$fade      = false;
			$auto_play = true;
			$arrows    = false;
			$typing    = false;
		}

		$slider_settings = array(
			'layout'       => $settings['layout'],
			'typing'       => $typing,
			'fade'         => $fade,
			'arrows'       => $arrows,
			'infinite'     => $infinite,
			'autoPlay'     => $auto_play,
			'vertical'     => $vertical,
			'speed'        => $speed,
			'slidesToShow' => $settings['slides_to_show'],
			'pauseOnHover' => 'yes' === $settings['pause_on_hover'] ? true : false,
			'animation'    => ! $infinite && ! $typing ? $settings['entrance_animation'] : '',
		);

		if ( $auto_play ) {
			$slider_settings['autoplaySpeed'] = $settings['autoplay_speed'];
		}

		$this->add_render_attribute(
			'outer-wrapper',
			array(
				'class'                => 'premium-post-ticker__outer-wrapper premium-post-ticker__hidden ',
				'data-ticker-settings' => wp_json_encode( $slider_settings ),
			)
		);

		$this->add_render_attribute( 'inner-wrapper', 'class', 'premium-post-ticker__posts-wrapper' );

		if ( 'yes' === $settings['reverse'] && 'layout-4' !== $layout && ! $typing && ! $fade ) {
			$this->add_render_attribute( 'inner-wrapper', 'dir', 'rtl' );
		}

		?>
			<div <?php echo wp_kses_post( $this->get_render_attribute_string( 'outer-wrapper' ) ); ?>>
				<?php if ( 'layout-1' === $layout ) { ?>
					<?php if ( $current_date ) : ?>
					<div class="premium-post-ticker__header-wrapper">
						<?php $this->render_ticker_date( $settings ); ?>
					</div>
					<?php endif; ?>

					<div class="premium-post-ticker__content">
						<?php
						if ( $title ) {
							$this->render_ticker_title( $settings );
						}
						?>

						<div <?php echo wp_kses_post( $this->get_render_attribute_string( 'inner-wrapper' ) ); ?>>
							<?php
							if ( in_array( $source, array( 'stock', 'gold' ), true ) ) {
								$this->render_detailed_stock_element( $req_data, $settings );
							} elseif ( 'text' === $source ) {
								$this->render_ticker_text_content( $text_content, $settings );
							} else {
								$this->render_ticker_post( $query, $settings, $posts_helper );
							}
							?>
						</div>

						<?php
						if ( $arrows ) {
							$this->render_ticker_arrows( $settings );
						}
						?>
					</div>
				<?php } elseif ( 'layout-2' === $layout ) { ?>

					<?php if ( $title ) : ?>
					<div class="premium-post-ticker__header-wrapper">
						<?php $this->render_ticker_title( $settings ); ?>
					</div>
					<?php endif; ?>

					<div class="premium-post-ticker__content">
						<?php
						if ( $current_date ) {
							$this->render_ticker_date( $settings );
						}
						?>

						<div <?php echo wp_kses_post( $this->get_render_attribute_string( 'inner-wrapper' ) ); ?>>
							<?php
							if ( in_array( $source, array( 'stock', 'gold' ), true ) ) {
								$this->render_detailed_stock_element( $req_data, $settings );
							} elseif ( 'text' === $source ) {
								$this->render_ticker_text_content( $text_content, $settings );
							} else {
								$this->render_ticker_post( $query, $settings, $posts_helper );
							}
							?>
						</div>

						<?php
						if ( $arrows ) {
							$this->render_ticker_arrows( $settings );
						}
						?>
					</div>

				<?php } elseif ( 'layout-3' === $layout ) { ?>
					<?php if ( $title || $current_date ) : ?>
					<div class="premium-post-ticker__header-wrapper">
						<?php
						if ( $current_date ) {
							$this->render_ticker_date( $settings );
						}

						if ( $title ) {
							$this->render_ticker_title( $settings );
						}
						?>
					</div>
					<?php endif; ?>

					<div class="premium-post-ticker__content">
						<div <?php echo wp_kses_post( $this->get_render_attribute_string( 'inner-wrapper' ) ); ?>>
							<?php
							if ( in_array( $source, array( 'stock', 'gold' ), true ) ) {
								$this->render_detailed_stock_element( $req_data, $settings );
							} elseif ( 'text' === $source ) {
								$this->render_ticker_text_content( $text_content, $settings );
							} else {
								$this->render_ticker_post( $query, $settings, $posts_helper );
							}
							?>
						</div>

						<?php
						if ( $arrows ) {
							$this->render_ticker_arrows( $settings );
						}
						?>
					</div>
				<?php } else { ?>
					<?php if ( $title || $current_date || $arrows ) : ?>
					<div class="premium-post-ticker__header-wrapper">
						<?php
						if ( $current_date ) {
							$this->render_ticker_date( $settings );
						}

						if ( $title ) {
							$this->render_ticker_title( $settings );
						}

						if ( $arrows ) {
							$this->render_ticker_arrows( $settings );
						}
						?>
					</div>
					<?php endif; ?>

					<div class="premium-post-ticker__content">
						<div <?php echo wp_kses_post( $this->get_render_attribute_string( 'inner-wrapper' ) ); ?>>
							<?php
							if ( in_array( $source, array( 'stock', 'gold' ), true ) ) {
								$this->render_detailed_stock_element( $req_data, $settings );
							} elseif ( 'text' === $source ) {
								$this->render_ticker_text_content( $text_content, $settings );
							} else {
								$this->render_ticker_post( $query, $settings, $posts_helper );
							}
							?>
						</div>
					</div>
				<?php } ?>
			</div>
		<?php
	}

	/**
	 * Extracts Stock Symbols from a string separated by ",".
	 *
	 * @access public
	 * @since 2.8.23
	 *
	 * @param string $symbol_str symbols strings.
	 *
	 * @return array
	 */
	private function extract_stock_symbols( $symbol_str ) {

		$symbols = explode( ',', $symbol_str );

		$symbols = array_slice( $symbols, 0, 5 );

		return $symbols;
	}

	/**
	 * Render stock elements.
	 *
	 * @access private
	 * @since 2.8.22
	 *
	 * @param array $stock_symbols   stock symbols data.
	 * @param array $settings  widget settings.
	 */
	private function render_detailed_stock_element( $stock_symbols, $settings ) {

		$is_stock_element = 'stock' === $settings['post_type_filter'];
		$function         = $settings['req_function'];
		$is_equity        = $is_stock_element && 'GLOBAL_QUOTE' === $function;

		$symbols_names = false;
		$show_symbol   = true;
		$show_price    = 'yes' === $settings['show_price'];

		// $show_symbol      = $is_stock_element || 'yes' === $settings['show_symbol'];
		$show_change     = 'yes' === $settings['show_change'];
		$show_change_per = 'yes' === $settings['show_change_per'];

		if ( $is_equity ) {
			if ( 'yes' === $settings['symbol_names_sw'] && ! empty( $settings['symbol_name'] ) ) {
				$symbols_names = $this->extract_stock_symbols( $settings['symbol_name'] );
			}

			if ( 'yes' !== $settings['show_symbol'] ) {
				$show_symbol = false;
			}
		}

		if ( $is_stock_element && 'CURRENCY_EXCHANGE_RATE' === $function ) {
			if ( 'yes' !== $settings['curr_change'] ) {
				$show_change     = false;
				$show_change_per = false;
			}
		}

		if ( $show_price || $show_change || $show_change_per ) {
			$change_indicator = $settings['change_indicator'];
			$decimal_places   = empty( $settings['decimal_places'] ) ? 0 : $settings['decimal_places'];
		}

		foreach ( $stock_symbols as $symbol => $data ) {

			$name = false;

			if ( $show_change || $show_change_per ) {
				$dir_cls = '';

				if ( 0 < $data['change'] ) {
					$dir_cls = 'up';
				} elseif ( 0 > $data['change'] ) {
					$dir_cls = 'down';
				}
			}

			if ( $show_price || $show_change || $show_change_per ) {

				if ( $show_price ) {
					$price = number_format( (float) $data['price'], $decimal_places, '.', ',' );

					if ( $is_equity ) {
						$price = '&#36;' . $price;
					}
				}

				if ( $show_change ) {
					$change = 'sign' === $change_indicator ? $data['change'] : abs( $data['change'] );
					$change = number_format( (float) $change, $decimal_places, '.', ',' );
				}

				if ( $show_change_per ) {

					$percent_change = str_replace( '%', '', $data['percent_change'] );

					$change_percent = 'sign' === $change_indicator ? $percent_change : abs( $percent_change );

					$change_percent = number_format( (float) str_replace( '%', '', $change_percent ), $decimal_places, '.', ',' );
				}
			}

			if ( false !== $symbols_names && isset( $symbols_names[ $symbol ] ) ) {
				$name = $symbols_names[ $symbol ];
			}

			if ( 'yes' === $settings['show_symbol_icon'] ) {

				$icons_repeater = $settings['symbol_icons_repeater'];

				$currency_symbol = 'CURRENCY_EXCHANGE_RATE' === $function ? substr( $data['symbol'], 0, 3 ) : $data['symbol'];

				$custom_icon = '';

				if ( count( $icons_repeater ) > 0 ) {
					array_map(
						function( $repeater_item ) use ( $currency_symbol, &$custom_icon ) {
							if ( $repeater_item['symbol_name'] === $currency_symbol ) {
								$custom_icon = $repeater_item['symbol_img']['url'];
							}},
						$icons_repeater
					);
				}

				if ( ! empty( $custom_icon ) ) {
					$data['icon_src'] = $custom_icon;
				} else {

					$currency_symbol = strtolower( $currency_symbol );
					if ( 'CURRENCY_EXCHANGE_RATE' === $function ) {

						$data['icon_src']         = sprintf( 'https://assets.coincap.io/assets/icons/%s@2x.png', $currency_symbol );
						$data['icon_alternative'] = $data['icon_src'];

					} else {

						$data['icon_src']         = sprintf( 'https://eodhistoricaldata.com/img/logos/US/%s.png', $data['symbol'] );
						$data['icon_alternative'] = sprintf( 'https://eodhistoricaldata.com/img/logos/US/%s.png', $currency_symbol );

					}
				}
			}

			?>
				<div class="premium-post-ticker__post-wrapper premium-post-sticker__stock-element-wrapper">

					<?php if ( 'yes' === $settings['show_symbol_icon'] ) : ?>
						<img class='premium-post-ticker__symbol-icon' src='<?php echo esc_attr( $data['icon_src'] ); ?>' alt='<?php echo esc_attr( $currency_symbol ); ?>' onerror="<?php echo 'CURRENCY_EXCHANGE_RATE' === $function ? '' : esc_attr( 'this.src="' . $data['icon_alternative'] . '"' ); ?>">
					<?php endif; ?>

					<?php if ( false !== $name ) : ?>
						<span class='premium-post-ticker__symbol-name' title='Name' aria-label='<?php echo esc_attr__( $name, 'premium-addons-for-elementor' ); ?>'><?php echo esc_html( $name ); ?></span>
					<?php endif; ?>

					<?php if ( $show_symbol ) : ?>
						<span class='premium-post-ticker__symbol' title='Symbol' aria-label='<?php echo esc_attr( $data['symbol'] ); ?>'> <?php echo esc_html( $data['symbol'] ); ?></span>
					<?php endif; ?>

					<span class="premium-post-ticker__change-wrapper">

						<?php if ( $show_price ) : ?>
							<span class="premium-post-ticker__price" title="Price
							<?php
							if ( ! $is_stock_element ) {
								echo 'Per Ounce'; }
							?>
							 " aria-label="<?php echo esc_attr__( $price, 'premium-addons-for-elementor' ); ?>"><?php echo $price; ?></span>
						<?php endif; ?>

						<?php if ( $show_change ) : ?>
							<span class="premium-post-ticker__change <?php echo esc_attr( $dir_cls ); ?>" title="Change" aria-label="<?php echo esc_attr__( $data['change'], 'premium-addons-for-elementor' ); ?>"><?php echo esc_html( $change ); ?></span>
						<?php endif; ?>

						<?php if ( $show_change_per ) : ?>
						<span class="premium-post-ticker__change-percent <?php echo esc_attr( $dir_cls ); ?>" title="Change Percent" aria-label="<?php echo esc_attr__( $data['percent_change'], 'premium-addons-for-elementor' ); ?>">
							<?php
								echo esc_html( $change_percent . '%' );

							if ( 'arrow' === $change_indicator ) {
								if ( 0 < $data['change'] ) {
									?>
										<i class="<?php echo $settings['arrow_style']; ?>-up" aria-hidden="true"></i>
									<?php
								} elseif ( 0 > $data['change'] ) {
									?>
										<i class="<?php echo $settings['arrow_style']; ?>-down" aria-hidden="true"></i>
									<?php
								}
							}
							?>
						</span>
						<?php endif; ?>

					</span>
				</div>
			<?php
		}
	}

	/**
	 * Render Ticker Title.
	 *
	 * @access private
	 * @since 2.8.22
	 *
	 * @param array $settings  widget settings.
	 */
	private function render_ticker_title( $settings ) {

		$title = ! empty( $settings['ticker_title'] ) ? $settings['ticker_title'] : false;

		$title_tag     = $settings['ticker_title_tag'];
		$title_classes = ! $title ? array() : Helper_Functions::get_element_classes( $settings['hide_title_on'], array( 'premium-post-ticker__title-wrapper' ) );

		$icon_enabled = 'yes' === $settings['ticker_icon_sw'] ? true : false;

		$this->add_render_attribute( 'title', 'class', $title_classes );

		?>
		<div <?php echo wp_kses_post( $this->get_render_attribute_string( 'title' ) ); ?>>
			<?php
			if ( $icon_enabled ) {
				$this->render_ticker_icon( $settings );
			}
			?>

			<<?php echo wp_kses_post( $title_tag ); ?> class="premium-post-ticker__title"> <?php echo esc_html( $title ); ?> </<?php echo wp_kses_post( $title_tag ); ?>>
		</div>
		<?php
	}

	/**
	 * Render Ticker Data.
	 *
	 * @access private
	 * @since 2.8.22
	 *
	 * @param array $settings  widget settings.
	 */
	private function render_ticker_date( $settings ) {

		$current_date = 'yes' === $settings['show_date'] ? true : false;
		$date_format  = ! empty( $settings['date_format'] ) ? $settings['date_format'] : get_option( 'date_format' );
		$date_classes = ! $current_date ? array() : Helper_Functions::get_element_classes( $settings['hide_date_on'], array( 'premium-post-ticker__date-wrapper' ) );

		$this->add_render_attribute( 'date', 'class', $date_classes );

		?>
		<div <?php echo wp_kses_post( $this->get_render_attribute_string( 'date' ) ); ?>>
			<span class="premium-post-ticker__date"> <?php echo esc_html( date_i18n( $date_format ) ); ?></span>
		</div>
		<?php
	}

	/**
	 * Render Ticker Arrows.
	 *
	 * @access private
	 * @since 2.8.22
	 *
	 * @param array $settings  widget settings.
	 */
	private function render_ticker_arrows( $settings ) {

		$prev = 'layout-4' === $settings['layout'] ? 'eicon-arrow-down' : 'fas fa-angle-left';
		$next = 'layout-4' === $settings['layout'] ? 'eicon-arrow-up' : 'fas fa-angle-right';
		?>
		<div class="premium-post-ticker__arrows">
			<a class="prev-arrow" type="button" role="button" aria-label="Previous">
				<i class="<?php echo esc_attr( $prev ); ?>"></i>
			</a>
			<a class="next-arrow" type="button" role="button" aria-label="Next">
				<i class="<?php echo esc_attr( $next ); ?>"></i>
			</a>
		</div>
		<?php
	}

	/**
	 * Render Ticker Icon.
	 *
	 * @access private
	 * @since 2.8.22
	 *
	 * @param array $settings  widget settings.
	 */
	private function render_ticker_icon( $settings, $is_repeater_item = false ) {
		$index = $is_repeater_item ? $settings['_id'] : '';
		?>
			<div class="premium-post-ticker__icon-wrapper <?php echo $is_repeater_item ? 'premium-repeater-item' : ''; ?>">
				<?php
					$icon_type = $settings['icon_type'];

				if ( 'icon' === $icon_type || 'svg' === $icon_type ) {

					if ( 'icon' === $icon_type && 'yes' !== $settings['draw_svg'] ) {

						Icons_Manager::render_icon(
							$settings['pa_ticker_icon'],
							array(
								'class'       => array( 'premium-svg-nodraw', 'premium-drawable-icon' ),
								'aria-hidden' => 'true',
							)
						);

					} else {

						if ( 'yes' === $settings['draw_svg'] ) {

							$this->add_render_attribute( 'icon' . $index, 'class', 'premium-drawable-icon' );

							$this->add_render_attribute( 'outer-wrapper' . $index, 'class', 'elementor-invisible' );

							if ( 'icon' === $icon_type ) {

								$this->add_render_attribute( 'icon' . $index, 'class', $settings['pa_ticker_icon']['value'] );

							}

							$this->add_render_attribute(
								'icon' . $index,
								array(
									'class'            => 'premium-svg-drawer',
									'data-svg-reverse' => $is_repeater_item ? 'false' : $settings['lottie_reverse'],
									'data-svg-loop'    => $settings['lottie_loop'],
									'data-svg-sync'    => $is_repeater_item ? 'true' : $settings['svg_sync'],
									'data-svg-fill'    => $is_repeater_item ? $settings['text_icon_color'] : $settings['title_icon_color'],
									'data-svg-frames'  => $is_repeater_item ? '5' : $settings['frames'],
									'data-svg-yoyo'    => $is_repeater_item ? 'false' : $settings['svg_yoyo'],
								)
							);

							if ( $is_repeater_item ) {
								$this->add_render_attribute( 'icon' . $index, 'data-svg-point', '0' );
							} else {
								$this->add_render_attribute( 'icon' . $index, 'data-svg-point', $settings['lottie_reverse'] ? $settings['end_point']['size'] : $settings['start_point']['size'] );
							}
						} else {
							$this->add_render_attribute( 'icon' . $index, 'class', 'premium-svg-nodraw' );
						}

						if ( 'icon' === $icon_type ) {
							?>
							<i <?php echo wp_kses_post( $this->get_render_attribute_string( 'icon' . $index ) ); ?>></i>
							<?php
						}
					}

					if ( 'svg' === $icon_type ) {
						?>
						<div <?php echo wp_kses_post( $this->get_render_attribute_string( 'icon' . $index ) ); ?>>
						<?php
						if ( $is_repeater_item ) {
							$this->print_unescaped_setting( 'custom_svg', 'text_content', $settings['index'] ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
						} else {
							$this->print_unescaped_setting( 'custom_svg' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
						}
						?>
						</div>
						<?php
					}
				} elseif ( 'image' === $icon_type ) {

					if ( ! empty( $settings['image']['url'] ) ) {
						$image_html = Group_Control_Image_Size::get_attachment_image_html( $settings, 'thumbnail', 'image' );
						echo wp_kses_post( $image_html );
					}
				} elseif ( 'lottie' === $icon_type ) {

					$this->add_render_attribute(
						'pa_ticker_lottie',
						array(
							'class'               => array(
								'premium-lottie-animation',
							),
							'data-lottie-url'     => $settings['lottie_url'],
							'data-lottie-loop'    => $settings['lottie_loop'],
							'data-lottie-reverse' => $is_repeater_item ? false : $settings['lottie_reverse'],
						)
					);
					?>
							<div <?php echo wp_kses_post( $this->get_render_attribute_string( 'pa_ticker_lottie' ) ); ?>></div>
						<?php
				}

				?>
			</div>
		<?php
	}

	/**
	 * Render Ticker Post.
	 *
	 * @access private
	 * @since 2.8.22
	 *
	 * @param object $query    query results.
	 * @param array  $settings  widget settings.
	 */
	private function render_ticker_post( $query, $settings ) {

		$posts = $query->posts;

		global $post;

		foreach ( $posts as $post ) {

			setup_postdata( $post );

			$this->get_ticker_post_layout( $settings );
		}

		wp_reset_postdata();
	}

	/**
	 * Render Ticker Text Content.
	 *
	 * @access private
	 * @since 2.8.22
	 *
	 * @param object $content    text content.
	 * @param array  $settings   widget settings.
	 */
	private function render_ticker_text_content( $content, $settings ) {

		$typing_enabled = 'yes' === $settings['typing'] && 'layout-4' !== $settings['layout'] ? true : false;

		foreach ( $content as $index => $item ) {

			$txt_id = $item['_id'];

			$this->add_render_attribute( 'post-title' . $txt_id, 'class', 'premium-post-ticker__post-title' );

			$this->add_render_attribute( 'post-wrapper' . $txt_id, 'class', array( 'premium-post-ticker__post-wrapper', 'elementor-repeater-item-' . $txt_id ) );

			$this->add_link_attributes( 'post-link' . $txt_id, $item['item_link'] );

			if ( $typing_enabled ) {
				$this->add_render_attribute( 'post-link' . $txt_id, 'data-typing', esc_attr( $item['text'] ) );
			}

			if ( '' !== $settings['entrance_animation'] ) {
				$this->add_render_attribute( 'post-wrapper' . $txt_id, 'class', 'animated ' . $settings['entrance_animation'] );
			}

			?>
			<div <?php echo wp_kses_post( $this->get_render_attribute_string( 'post-wrapper' . $txt_id ) ); ?>>
				<div <?php echo wp_kses_post( $this->get_render_attribute_string( 'post-title' . $txt_id ) ); ?>>
					<a <?php echo wp_kses_post( $this->get_render_attribute_string( 'post-link' . $txt_id ) ); ?>>
						<?php echo $item['text']; ?>
					</a>
				</div>
				<?php
				if ( 'yes' === $item['txt_icon_sw'] ) {
					$item['text_icon_color'] = $settings['text_icon_color'];
					$item['index']           = $index;
					$this->render_ticker_icon( $item, true );
				}
				?>
				<?php if ( 'yes' === $settings['separator'] && 'yes' === $settings['infinite'] && 'layout-4' !== $settings['layout'] ) : ?>
					<div class="premium-post-ticker__separator"></div>
				<?php endif; ?>
			</div>
			<?php
		}
	}

	/**
	 * Render Post Layout.
	 *
	 * @access private
	 * @since 2.8.22
	 *
	 * @param array $settings  widget settings.
	 */
	private function get_ticker_post_layout( $settings ) {

		$post_id = get_the_ID();

		$show_thumbnail = 'yes' === $settings['post_img'] ? true : false;

		$show_author = 'yes' === $settings['author_meta'] ? true : false;

		$show_date = 'yes' === $settings['date_meta'] ? true : false;

		$title_tag = $settings['ticker_title_tag'];

		$link_target = 'yes' === $settings['new_tab'] ? '_blank' : '_self';

		$post_tag = 'yes' === $settings['article_tag_switcher'] ? 'article' : 'div';

		$content_length = ! empty( $settings['content_length'] ) ? $settings['content_length'] : false;

		$typing_enabled = 'yes' === $settings['typing'] && 'layout-4' !== $settings['layout'] ? true : false;

		if ( ! $content_length ) {
			$title = the_title( '', '', false );
		} else {
			$title = implode( ' ', array_slice( explode( ' ', the_title( '', '', false ) ), 0, $content_length ) ) . '...';
		}

		if ( $show_thumbnail ) {

			$settings['image'] = array(
				'id' => get_post_thumbnail_id(),
			);

			$thumbnail_html = Group_Control_Image_Size::get_attachment_image_html( $settings, 'image' );

			$author_classes = Helper_Functions::get_element_classes( $settings['hide_thumb_on'], array( 'premium-post-ticker__thumbnail-wrapper' ) );

			$this->add_render_attribute( 'thumbnail' . $post_id, 'class', $author_classes );
		}

		if ( $show_author ) {
			$author_classes = Helper_Functions::get_element_classes( $settings['hide_author_on'], array( 'premium-post-ticker__post-author' ) );

			$this->add_render_attribute( 'author' . $post_id, 'class', $author_classes );
		}

		if ( $show_date ) {

			$date_format = ! empty( $settings['post_date_format'] ) ? $settings['post_date_format'] : get_option( 'date_format' );

			$author_classes = Helper_Functions::get_element_classes( $settings['hide_post_date_on'], array( 'premium-post-ticker__post-date' ) );

			$this->add_render_attribute( 'post-date' . $post_id, 'class', $author_classes );
		}

		$this->add_render_attribute( 'post-title' . $post_id, 'class', 'premium-post-ticker__post-title' );

		$this->add_render_attribute( 'post-wrapper' . $post_id, 'class', 'premium-post-ticker__post-wrapper' );

		$this->add_render_attribute(
			'post-link' . $post_id,
			array(
				'href'   => esc_url( get_permalink() ),
				'target' => esc_attr( $link_target ),
			)
		);

		if ( $typing_enabled ) {
			$this->add_render_attribute( 'post-link' . $post_id, 'data-typing', esc_attr( $title ) );
		}

		if ( '' !== $settings['entrance_animation'] ) {
			$this->add_render_attribute( 'post-wrapper' . $post_id, 'class', 'animated ' . $settings['entrance_animation'] );
		}

		?>

		<<?php echo wp_kses_post( $post_tag . ' ' . $this->get_render_attribute_string( 'post-wrapper' . $post_id ) ); ?>>

			<?php if ( $show_thumbnail ) : ?>
				<div <?php echo wp_kses_post( $this->get_render_attribute_string( 'thumbnail' . $post_id ) ); ?>>
					<a href="<?php the_permalink(); ?>" target="<?php echo esc_attr( $link_target ); ?>">
						<?php echo $thumbnail_html; ?>
					</a>
				</div>
			<?php endif; ?>

			<?php if ( $show_author ) : ?>
				<div>
					<span <?php echo wp_kses_post( $this->get_render_attribute_string( 'author' . $post_id ) ); ?>>
						<i class="fa fa-user fa-fw" aria-hidden="true"></i>
						<?php the_author_posts_link(); ?>
					</span>
				</div>
			<?php endif; ?>

			<div>
				<<?php echo wp_kses_post( $title_tag . ' ' . $this->get_render_attribute_string( 'post-title' . $post_id ) ); ?>>
					<a <?php echo wp_kses_post( $this->get_render_attribute_string( 'post-link' . $post_id ) ); ?>>
						<?php echo $title; ?>
					</a>
				</<?php echo wp_kses_post( $title_tag ); ?>>
			</div>

			<?php if ( $show_date ) : ?>
				<div>
					<span <?php echo wp_kses_post( $this->get_render_attribute_string( 'post-date' . $post_id ) ); ?>>
						<span><?php the_time( $date_format ); ?></span>
					</span>
				</div>
			<?php endif; ?>

			<?php if ( 'yes' === $settings['separator'] && 'yes' === $settings['infinite'] && 'layout-4' !== $settings['layout'] ) : ?>
				<div class="premium-post-ticker__separator"></div>
			<?php endif; ?>

		</<?php echo wp_kses_post( $post_tag ); ?>>

		<?php
	}
}
