<?php
/**
 * Premium Recent Posts Notifications.
 */

namespace PremiumAddons\Widgets;

// Elementor Classes.
use Elementor\Icons_Manager;
use Elementor\Widget_Base;
use Elementor\Utils;
use Elementor\Control_Media;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Image_Size;
use Elementor\Repeater;
use Elementor\Group_Control_Css_Filter;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Typography;
use Elementor\Core\Kits\Documents\Tabs\Global_Typography;
use Elementor\Core\Kits\Documents\Tabs\Global_Colors;
use Elementor\Group_Control_Background;
use Elementor\Group_Control_Text_Shadow;


// PremiumAddons Classes.
use PremiumAddons\Admin\Includes\Admin_Helper;
use PremiumAddons\Includes\Premium_Template_Tags as Blog_Helper;
use PremiumAddons\Includes\Controls\Premium_Post_Filter;
use PremiumAddons\Includes\Helper_Functions;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class Premium_Notifications
 */
class Premium_Notifications extends Widget_Base {

	/**
	 * Blog Helper
	 *
	 * @var blog_helper
	 */
	private $blog_helper = array();

	/**
	 * Get Blog Helper Instance.
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function get_blog_helper() {
		return $this->blog_helper = Blog_Helper::getInstance();
	}


	/**
	 * Query Posts
	 *
	 * @var query_posts
	 */
	private $query_posts = array();

	/**
	 * Check Icon Draw Option.
	 *
	 * @since 4.9.26
	 * @access public
	 */
	public function check_icon_draw() {
		$is_enabled = Admin_Helper::check_svg_draw( 'premium-notifications' );
		return $is_enabled;
	}

	/**
	 * Retrieve Widget Name.
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function get_name() {
		return 'premium-notifications';
	}

	/**
	 * Retrieve Widget Title.
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function get_title() {
		return __( 'Recent Posts Notification', 'premium-addons-for-elementor' );
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
		return 'pa-post-notifications';
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
		return array( 'pa', 'premium', 'posts', 'alert', 'recent', 'query', 'box', 'cpt', 'custom' );
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
				'pa-notifications',
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
	 * Register Image Scroll controls.
	 *
	 * @since 1.0.0
	 * @access protected
	 */
	protected function register_controls() { // phpcs:ignore PSR2.Methods.MethodDeclaration.Underscore

		$papro_activated = apply_filters( 'papro_activated', false );

		$draw_icon = $this->check_icon_draw();

		$options = apply_filters(
			'pa_notification_options',
			array(
				'skins'            => array(
					'classic' => __( 'Classic', 'premium-addons-for-elementor' ),
					'modern'  => __( 'Modern', 'premium-addons-for-elementor' ),
					'cards'   => __( 'Cards (Pro)', 'premium-addons-for-elementor' ),
					'banner'  => __( 'Banner (Pro)', 'premium-addons-for-elementor' ),
				),
				'skin_condition'   => array( 'cards', 'banner' ),
				'source_condition' => array(
					'post_type_filter' => 'post',
				),
			)
		);

		$this->start_controls_section(
			'icon_settings',
			array(
				'label' => __( 'Notification Icon', 'premium-addons-for-elementor' ),
			)
		);

		$this->add_control(
			'icon_type',
			array(
				'label'              => __( 'Icon Type', 'premium-addons-for-elementor' ),
				'type'               => Controls_Manager::SELECT,
				'default'            => 'icon',
				'options'            => array(
					'icon'      => __( 'Icon', 'premium-addons-for-elementor' ),
					'image'     => __( 'Image', 'premium-addons-for-elementor' ),
					'text'      => __( 'Text', 'premium-addons-for-elementor' ),
					'animation' => __( 'Lottie Animation', 'premium-addons-for-elementor' ),
					'svg'       => __( 'SVG Code', 'premium-addons-for-elementor' ),
				),
				'frontend_available' => true,
			)
		);

		$this->add_control(
			'icon',
			array(
				'label'     => __( 'Icon', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::ICONS,
				'default'   => array(
					'value'   => 'far fa-bell',
					'library' => 'fa-regular',
				),
				'condition' => array(
					'icon_type' => 'icon',
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
					'icon_type' => 'image',
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

		$animation_conds = array(
			'terms' => array(
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
				'condition' => array(
					'icon_type'      => array( 'icon', 'svg' ),
					'icon[library]!' => 'svg',
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
					'condition' => array(
						'icon_type' => array( 'icon', 'svg' ),
					),
					'selectors' => array(
						'{{WRAPPER}} .pa-rec-not-icon-wrap svg *' => 'stroke-width: {{SIZE}}',
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
				'recent',
				array(
					'icon_type'      => array( 'icon', 'svg' ),
					'icon[library]!' => 'svg',
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

		$this->add_responsive_control(
			'icon_size',
			array(
				'label'      => __( 'Icon Size', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', 'em', '%', 'custom' ),
				'selectors'  => array(
					'{{WRAPPER}} .pa-rec-not-icon-wrap img' => 'width: {{SIZE}}{{UNIT}}',
					// !important to override Elementor Pro lottie width/height on editor page.
					'{{WRAPPER}} .pa-rec-not-icon-wrap svg' => 'width: {{SIZE}}{{UNIT}} !important; height: {{SIZE}}{{UNIT}} !important',
					'{{WRAPPER}} .pa-rec-not-icon-wrap i' => 'font-size: {{SIZE}}{{UNIT}}',
				),
				'condition'  => array(
					'icon_type!' => 'text',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Image_Size::get_type(),
			array(
				'name'      => 'thumbnail',
				'default'   => 'full',
				'condition' => array(
					'icon_type' => 'image',
				),
			)
		);

		$this->add_control(
			'text',
			array(
				'label'     => __( 'Text', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::TEXT,
				'default'   => __( 'What is new?', 'premium-addons-for-elementor' ),
				'dynamic'   => array( 'active' => true ),
				'condition' => array(
					'icon_type' => 'text',
				),
			)
		);

        $this->add_control(
			'add_icon_with_no_posts',
			array(
				'label'              => __( 'Add Different Icon with no posts', 'premium-addons-for-elementor' ),
				'type'               => Controls_Manager::SWITCHER,
				'frontend_available' => true,
			)
		);

		$this->add_control(
			'icon_with_no_posts',
			array(
				'label'              => __( 'Icon', 'premium-addons-for-elementor' ),
				'type'               => Controls_Manager::ICONS,
				'condition'          => array(
					'add_icon_with_no_posts' => 'yes',
					'icon_type!'              => array( 'image' ),
				),
				'frontend_available' => true,
			)
		);

		$this->add_control(
			'image_with_no_posts',
			array(
				'label'       => __( 'Image', 'premium-addons-for-elementor' ),
				'type'        => Controls_Manager::MEDIA,
				'media_types' => array( 'image' ),
				'dynamic'     => array( 'active' => true ),
				'condition'   => array(
					'icon_type'              => 'image',
					'add_icon_with_no_posts' => 'yes',
				),
			)
		);

		$this->add_responsive_control(
			'icon_align',
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
					'{{WRAPPER}} .pa-recent-notification' => 'text-align: {{VALUE}};',
				),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'number_settings',
			array(
				'label' => __( 'Posts Number', 'premium-addons-for-elementor' ),
			)
		);

		$this->add_control(
			'posts_number',
			array(
				'label'     => __( 'Number', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::NUMBER,
				'default'   => 3,
				'condition' => array(
					'cookies!' => 'yes',
				),
			)
		);

		$this->add_control(
			'cookies',
			array(
				'label'              => __( 'Use Cookies to Get Unseen Posts', 'premium-addons-for-elementor' ),
				'type'               => Controls_Manager::SWITCHER,
				'frontend_available' => true,
			)
		);

		$this->add_control(
			'cookies_notice',
			array(
				'raw'             => __( 'Use cookies option works only when logged out.', 'premium-addons-for-elementor' ),
				'type'            => Controls_Manager::RAW_HTML,
				'content_classes' => 'elementor-panel-alert elementor-panel-alert-info',
				'condition'       => array(
					'cookies' => 'yes',
				),
			)
		);

		$this->add_control(
			'cookies_interval',
			array(
				'label'              => __( 'Expiration Time (days)', 'premium-addons-for-elementor' ),
				'type'               => Controls_Manager::NUMBER,
				'description'        => __( 'How many days before removing cookie, set the value in days, default is: 1 day', 'premium-addons-for-elementor' ),
				'default'            => 1,
				'min'                => 0,
				'condition'          => array(
					'cookies' => 'yes',
				),
				'frontend_available' => true,
			)
		);

		$this->add_responsive_control(
			'number_box_size',
			array(
				'label'      => __( 'Numbers Box Size', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', 'em', '%', 'custom' ),
				'selectors'  => array(
					'{{WRAPPER}} .pa-rec-not-number' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}}',
				),
			)
		);

		$this->add_responsive_control(
			'number_box_h',
			array(
				'label'       => __( 'Horizontal Position', 'premium-addons-for-elementor' ),
				'type'        => Controls_Manager::SLIDER,
				'size_units'  => array( 'px', 'em', '%' ),
				'label_block' => true,
				'selectors'   => array(
					'{{WRAPPER}} .pa-rec-not-number' => 'right: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'number_box_v',
			array(
				'label'       => __( 'Vertical Position', 'premium-addons-for-elementor' ),
				'type'        => Controls_Manager::SLIDER,
				'size_units'  => array( 'px', 'em', '%' ),
				'label_block' => true,
				'selectors'   => array(
					'{{WRAPPER}} .pa-rec-not-number' => 'top: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'posts_box_settings',
			array(
				'label' => __( 'Posts Box', 'premium-addons-for-elementor' ),
			)
		);

		$this->add_responsive_control(
			'posts_box_width',
			array(
				'label'              => __( 'Width', 'premium-addons-for-elementor' ),
				'type'               => Controls_Manager::SLIDER,
				'size_units'         => array( 'px', '%', 'vw', 'custom' ),
				'range'              => array(
					'px' => array(
						'min' => 1,
						'max' => 1000,
					),
				),
				'selectors'          => array(
					'{{WRAPPER}} .pa-rec-posts-container' => 'width: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}}'                         => '--pa-recent-posts-width: {{SIZE}}{{UNIT}}',
				),
				'frontend_available' => true,
			)
		);

		$this->add_responsive_control(
			'posts_box_height',
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
					'{{WRAPPER}} .pa-rec-posts-body' => 'max-height: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'posts_box_position',
			array(
				'label'              => __( 'Position', 'premium-addons-for-elementor' ),
				'type'               => Controls_Manager::SELECT,
				'prefix_class'       => 'pa-container-',
				'options'            => array(
					'left'  => __( 'Left', 'premium-addons-for-elementor' ),
					'right' => __( 'Right', 'premium-addons-for-elementor' ),
				),
				'default'            => 'right',
				'render_type'        => 'template',
				'frontend_available' => true,
			)
		);

		$this->add_control(
			'posts_animation',
			array(
				'label'              => __( 'Posts Entrance Animation', 'premium-addons-for-elementor' ),
				'type'               => Controls_Manager::ANIMATION,
				'default'            => 'fadeInUp',
				'label_block'        => true,
				'frontend_available' => true,

			)
		);

		$this->add_control(
			'posts_animation_individial',
			array(
				'label'              => __( 'Apply Animation on Posts Individually', 'premium-addons-for-elementor' ),
				'type'               => Controls_Manager::SWITCHER,
				'condition'          => array(
					'posts_animation!' => '',
				),
				'frontend_available' => true,
			)
		);

		$this->add_control(
			'overlay',
			array(
				'label'              => __( 'Show Overlay', 'premium-addons-for-elementor' ),
				'type'               => Controls_Manager::SWITCHER,
				'frontend_available' => true,
			)
		);

		$this->add_control(
			'shown_content',
			array(
				'label'     => __( 'What To Show When No Posts', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::SELECT,
				'options'   => array(
					'posts'    => __( 'Same Posts', 'premium-addons-for-elementor' ),
					'template' => __( 'Elementor Template', 'premium-addons-for-elementor' ),
				),
				'default'   => 'posts',
				'condition' => array(
					'cookies' => 'yes',
				),
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
					'cookies'       => 'yes',
					'shown_content' => 'template',
				),
			)
		);

		$this->add_control(
			'content_temp_live_btn',
			array(
				'type'        => Controls_Manager::BUTTON,
				'label_block' => true,
				'button_type' => 'default papro-btn-block',
				'text'        => __( 'Create / Edit Template', 'premium-addons-for-elementor' ),
				'event'       => 'createLiveTemp',
				'condition'   => array(
					'cookies'       => 'yes',
					'shown_content' => 'template',
				),
			)
		);

		$this->add_control(
			'content_temp',
			array(
				'label'       => __( 'OR Select Existing Template', 'premium-addons-for-elementor' ),
				'type'        => Controls_Manager::SELECT2,
				'label_block' => true,
				'classes'     => 'premium-live-temp-label',
				'options'     => $this->get_blog_helper()->get_elementor_page_list(),
				'condition'   => array(
					'cookies'       => 'yes',
					'shown_content' => 'template',
				),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'header_settings',
			array(
				'label' => __( 'Posts Box Header', 'premium-addons-for-elementor' ),
			)
		);

		$this->add_control(
			'header_text',
			array(
				'label'       => __( 'Title Text', 'premium-addons-for-elementor' ),
				'type'        => Controls_Manager::TEXT,
				'dynamic'     => array( 'active' => true ),
				'default'     => __( 'What\'s New?', 'premium-addons-for-elementor' ),
				'label_block' => true,
			)
		);

		$this->add_control(
			'header_animation',
			array(
				'label'              => __( 'Title Entrance Animation', 'premium-addons-for-elementor' ),
				'type'               => Controls_Manager::ANIMATION,
				'default'            => 'fadeInDown',
				'label_block'        => true,
				'frontend_available' => true,

			)
		);

		$this->add_control(
			'header_size',
			array(
				'label'       => __( 'HTML Tag', 'premium-addons-for-elementor' ),
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

		$this->add_control(
			'header_icon_sw',
			array(
				'label'              => __( 'Icon', 'premium-addons-for-elementor' ),
				'type'               => Controls_Manager::SWITCHER,
				'frontend_available' => true,
			)
		);

		$this->add_control(
			'header_icon_type',
			array(
				'label'     => __( 'Icon Type', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'icon',
				'options'   => array(
					'icon'      => __( 'Icon', 'premium-addons-for-elementor' ),
					'image'     => __( 'Image', 'premium-addons-for-elementor' ),
					'animation' => __( 'Lottie Animation', 'premium-addons-for-elementor' ),
					'svg'       => __( 'SVG Code', 'premium-addons-for-elementor' ),
				),
				'condition' => array(
					'header_icon_sw' => 'yes',
				),
			)
		);

		$this->add_control(
			'header_icon',
			array(
				'label'     => __( 'Icon', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::ICONS,
				'default'   => array(
					'value'   => 'far fa-bell',
					'library' => 'fa-regular',
				),
				'condition' => array(
					'header_icon_sw'   => 'yes',
					'header_icon_type' => 'icon',
				),
			)
		);

		$this->add_control(
			'header_image',
			array(
				'label'       => __( 'Image', 'premium-addons-for-elementor' ),
				'type'        => Controls_Manager::MEDIA,
				'media_types' => array( 'image' ),
				'dynamic'     => array( 'active' => true ),
				'condition'   => array(
					'header_icon_sw'   => 'yes',
					'header_icon_type' => 'image',
				),
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
					'header_icon_sw'   => 'yes',
					'header_icon_type' => 'animation',
				),
			)
		);

		$this->add_control(
			'header_custom_svg',
			array(
				'label'       => __( 'SVG Code', 'premium-addons-for-elementor' ),
				'type'        => Controls_Manager::TEXTAREA,
				'description' => 'You can use these sites to create SVGs: <a href="https://danmarshall.github.io/google-font-to-svg-path/" target="_blank">Google Fonts</a> and <a href="https://boxy-svg.com/" target="_blank">Boxy SVG</a>',
				'condition'   => array(
					'header_icon_sw'   => 'yes',
					'header_icon_type' => 'svg',
				),
			)
		);

		$header_animation_conds = array(
			'terms' => array(
				array(
					'name'  => 'header_icon_sw',
					'value' => 'yes',
				),
				array(
					'relation' => 'or',
					'terms'    => array(
						array(
							'name'  => 'header_icon_type',
							'value' => 'animation',
						),
						array(
							'terms' => array(
								array(
									'relation' => 'or',
									'terms'    => array(
										array(
											'name'  => 'header_icon_type',
											'value' => 'icon',
										),
										array(
											'name'  => 'header_icon_type',
											'value' => 'svg',
										),
									),
								),
								array(
									'name'  => 'header_draw_svg',
									'value' => 'yes',
								),
							),
						),
					),
				),
			),
		);

		$this->add_control(
			'header_draw_svg',
			array(
				'label'     => __( 'Draw Icon', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::SWITCHER,
				'classes'   => $draw_icon ? '' : 'editor-pa-control-disabled',
				'condition' => array(
					'header_icon_sw'        => 'yes',
					'header_icon_type'      => array( 'icon', 'svg' ),
					'header_icon[library]!' => 'svg',
				),
			)
		);

		if ( $draw_icon ) {

			$this->add_control(
				'header_path_width',
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
						'header_icon_sw'   => 'yes',
						'header_icon_type' => array( 'icon', 'svg' ),
					),
					'selectors' => array(
						'{{WRAPPER}} .pa-rec-title-icon-wrap svg *' => 'stroke-width: {{SIZE}}',
					),
				)
			);

			$this->add_control(
				'header_svg_sync',
				array(
					'label'     => __( 'Draw All Paths Together', 'premium-addons-for-elementor' ),
					'type'      => Controls_Manager::SWITCHER,
					'condition' => array(
						'header_icon_sw'   => 'yes',
						'header_icon_type' => array( 'icon', 'svg' ),
						'header_draw_svg'  => 'yes',
					),
				)
			);

			$this->add_control(
				'header_frames',
				array(
					'label'       => __( 'Speed', 'premium-addons-for-elementor' ),
					'type'        => Controls_Manager::NUMBER,
					'description' => __( 'Larger value means longer animation duration.', 'premium-addons-for-elementor' ),
					'default'     => 5,
					'min'         => 1,
					'max'         => 100,
					'condition'   => array(
						'header_icon_sw'   => 'yes',
						'header_icon_type' => array( 'icon', 'svg' ),
						'header_draw_svg'  => 'yes',
					),
				)
			);

		} else {

			Helper_Functions::get_draw_svg_notice(
				$this,
				'recent',
				array(
					'header_icon_sw'        => 'yes',
					'header_icon_type'      => array( 'icon', 'svg' ),
					'header_icon[library]!' => 'svg',
				)
			);

		}

		$this->add_control(
			'header_lottie_loop',
			array(
				'label'        => __( 'Loop', 'premium-addons-for-elementor' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'true',
				'default'      => 'true',
				'conditions'   => $header_animation_conds,
			)
		);

		if ( $draw_icon ) {

			$this->add_control(
				'header_svg_yoyo',
				array(
					'label'     => __( 'Yoyo Effect', 'premium-addons-for-elementor' ),
					'type'      => Controls_Manager::SWITCHER,
					'condition' => array(
						'header_icon_sw'     => 'yes',
						'header_icon_type'   => array( 'icon', 'svg' ),
						'header_draw_svg'    => 'yes',
						'header_lottie_loop' => 'true',
					),
				)
			);
		}

		$this->add_responsive_control(
			'header_icon_size',
			array(
				'label'      => __( 'Icon Size', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', 'em', '%', 'custom' ),
				'selectors'  => array(
					'{{WRAPPER}} .pa-rec-title-icon-wrap img' => 'width: {{SIZE}}{{UNIT}}',
					// !important to override Elementor Pro lottie width/height on editor page.
					'{{WRAPPER}} .pa-rec-title-icon-wrap svg' => 'width: {{SIZE}}{{UNIT}} !important; height: {{SIZE}}{{UNIT}} !important',
					'{{WRAPPER}} .pa-rec-title-icon-wrap i' => 'font-size: {{SIZE}}{{UNIT}}',
				),
				'condition'  => array(
					'header_icon_sw' => 'yes',
				),
			)
		);

		$this->add_responsive_control(
			'icon_spacing',
			array(
				'label'     => __( 'Spacing (px)', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::SLIDER,
				'selectors' => array(
					'{{WRAPPER}} .pa-rec-title-wrap' => 'column-gap: {{SIZE}}px',
				),
				'condition' => array(
					'header_icon_sw' => 'yes',
				),
			)
		);

		$this->add_responsive_control(
			'close_v',
			array(
				'label'     => __( 'Close Icon Position', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::CHOOSE,
				'options'   => array(
					'flex-start' => array(
						'title' => __( 'Top', 'premium-addons-for-elementor' ),
						'icon'  => 'eicon-arrow-up',
					),
					'center'     => array(
						'title' => __( 'Middle', 'premium-addons-for-elementor' ),
						'icon'  => 'eicon-text-align-justify',
					),
					'flex-end'   => array(
						'title' => __( 'Bottom', 'premium-addons-for-elementor' ),
						'icon'  => 'eicon-arrow-down',
					),
				),
				'separator' => 'before',
				'default'   => 'flex-start',
				'toggle'    => false,
				'selectors' => array(
					'{{WRAPPER}} .pa-rec-posts-close' => 'align-self: {{VALUE}};',
				),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_query_options',
			array(
				'label' => __( 'Query', 'premium-addons-for-elementor' ),
			)
		);

		$post_types = Blog_Helper::get_posts_types();

		foreach ( $post_types as $id => $label ) {

			if ( 'post' !== $id ) {
				$post_types[ $id ] .= apply_filters( 'pa_pro_label', __( ' (Pro)', 'premium-addons-for-elementor' ) );
			}
		}

		$this->add_control(
			'post_type_filter',
			array(
				'label'       => __( 'Post Type', 'premium-addons-for-elementor' ),
				'type'        => Controls_Manager::SELECT,
				'label_block' => true,
				'options'     => $post_types,
				'default'     => 'post',
				'separator'   => 'after',
			)
		);

		$this->add_control(
			'premium_blog_number_of_posts',
			array(
				'label'     => __( 'Posts to Load', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::NUMBER,
				'min'       => 1,
				'default'   => 5,
				'condition' => $options['source_condition'],
			)
		);

		foreach ( $post_types as $key => $type ) {

			// Get all the taxanomies associated with the selected post type.
			$taxonomy = Blog_Helper::get_taxnomies( $key );

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
								'separator'   => 'after',
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
				'separator'   => 'before',
				'label_block' => true,
				'options'     => array(
					'author__in'     => __( 'Match Authors', 'premium-addons-for-elementor' ),
					'author__not_in' => __( 'Exclude Authors', 'premium-addons-for-elementor' ),
				),
				'condition'   => $options['source_condition'],
			)
		);

		$this->add_control(
			'premium_blog_users',
			array(
				'label'       => __( 'Authors', 'premium-addons-for-elementor' ),
				'type'        => Controls_Manager::SELECT2,
				'label_block' => true,
				'multiple'    => true,
				'options'     => Blog_Helper::get_authors(),
				'condition'   => $options['source_condition'],
			)
		);

		$this->add_control(
			'posts_filter_rule',
			array(
				'label'       => __( 'Filter By Post Rule', 'premium-addons-for-elementor' ),
				'type'        => Controls_Manager::SELECT,
				'default'     => 'post__not_in',
				'separator'   => 'before',
				'label_block' => true,
				'options'     => array(
					'post__in'     => __( 'Match Post', 'premium-addons-for-elementor' ),
					'post__not_in' => __( 'Exclude Post', 'premium-addons-for-elementor' ),
				),
				'condition'   => $options['source_condition'],
			)
		);

		$this->add_control(
			'premium_blog_posts_exclude',
			array(
				'label'       => __( 'Posts', 'premium-addons-for-elementor' ),
				'type'        => Controls_Manager::SELECT2,
				'label_block' => true,
				'multiple'    => true,
				'options'     => Blog_Helper::get_default_posts_list( 'post' ),
				'condition'   => $options['source_condition'],
			)
		);

		if ( $papro_activated ) {

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
						'post_type_filter!' => 'post',
					),
				)
			);

		}

		$this->add_control(
			'ignore_sticky_posts',
			array(
				'label'     => __( 'Ignore Sticky Posts', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::SWITCHER,
				'label_on'  => __( 'Yes', 'premium-addons-for-elementor' ),
				'label_off' => __( 'No', 'premium-addons-for-elementor' ),
				'default'   => 'yes',
				'condition' => $options['source_condition'],
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
				'condition'   => $options['source_condition'],
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
				'condition'   => $options['source_condition'],
			)
		);

		$this->add_control(
			'posts_from',
			array(
				'label'     => __( 'Get Posts From', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::SELECT,
				'options'   => array(
					''      => __( 'All Time', 'premium-addons-for-elementor' ),
					'day'   => __( 'Day', 'premium-addons-for-elementor' ),
					'week'  => __( 'Week', 'premium-addons-for-elementor' ),
					'month' => __( 'Month', 'premium-addons-for-elementor' ),
					'year'  => __( 'Year', 'premium-addons-for-elementor' ),
				),
				'condition' => $options['source_condition'],
			)
		);

		$this->add_control(
			'premium_blog_order_by',
			array(
				'label'       => __( 'Order By', 'premium-addons-for-elementor' ),
				'type'        => Controls_Manager::SELECT,
				'separator'   => 'before',
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
				'condition'   => $options['source_condition'],
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
				'condition'   => $options['source_condition'],
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'post_settings',
			array(
				'label' => __( 'Post Options', 'premium-addons-for-elementor' ),
			)
		);

		$this->add_control(
			'premium_blog_article_tag_switcher',
			array(
				'label'   => __( 'Change Post Html Tag To Article', 'premium-addons-for-elementor' ),
				'type'    => Controls_Manager::SWITCHER,
				'default' => 'yes',
			)
		);

		$this->add_control(
			'premium_blog_new_tab',
			array(
				'label'   => __( 'Open Links in New Tab', 'premium-addons-for-elementor' ),
				'type'    => Controls_Manager::SWITCHER,
				'default' => 'yes',
			)
		);

		$this->add_control(
			'premium_blog_skin',
			array(
				'label'       => __( 'Skin', 'premium-addons-for-elementor' ),
				'type'        => Controls_Manager::SELECT,
				'options'     => $options['skins'],
				'default'     => 'classic',
				'label_block' => true,
			)
		);

		$get_pro = Helper_Functions::get_campaign_link( 'https://premiumaddons.com/pro', 'editor-page', 'wp-editor', 'get-pro' );

		$this->add_control(
			'notification_notice',
			array(
				'type'            => Controls_Manager::RAW_HTML,
				'raw'             => __( 'This option is available in Premium Addons Pro. ', 'premium-addons-for-elementor' ) . '<a href="' . esc_url( $get_pro ) . '" target="_blank">' . __( 'Upgrade now!', 'premium-addons-for-elementor' ) . '</a>',
				'content_classes' => 'papro-upgrade-notice',
				'condition'       => array(
					'premium_blog_skin' => $options['skin_condition'],
				),
			)
		);

		$this->add_control(
			'premium_blog_author_img_switcher',
			array(
				'label'     => __( 'Show Author Image', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::SWITCHER,
				'default'   => 'yes',
				'condition' => array(
					'premium_blog_skin' => 'cards',
				),
			)
		);

		$this->add_responsive_control(
			'content_offset',
			array(
				'label'     => __( 'Content Offset', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::SLIDER,
				'range'     => array(
					'px' => array(
						'min' => -100,
						'max' => 100,
					),
				),
				'condition' => array(
					'premium_blog_skin' => 'modern',
				),
				'selectors' => array(
					'{{WRAPPER}} .premium-blog-skin-modern .premium-blog-content-wrapper' => 'top: {{SIZE}}{{UNIT}}',
				),
			)
		);

		$this->add_control(
			'premium_blog_title_tag',
			array(
				'label'       => __( 'Title HTML Tag', 'premium-addons-for-elementor' ),
				'description' => __( 'Select a heading tag for the post title.', 'premium-addons-for-elementor' ),
				'type'        => Controls_Manager::SELECT,
				'default'     => 'h2',
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

		$this->add_control(
			'premium_blog_excerpt',
			array(
				'label'   => __( 'Show Post Content', 'premium-addons-for-elementor' ),
				'type'    => Controls_Manager::SWITCHER,
				'default' => 'yes',
			)
		);

		$this->add_control(
			'content_source',
			array(
				'label'       => __( 'Get Content From', 'premium-addons-for-elementor' ),
				'type'        => Controls_Manager::SELECT,
				'options'     => array(
					'excerpt' => __( 'Post Excerpt', 'premium-addons-for-elementor' ),
					'full'    => __( 'Post Full Content', 'premium-addons-for-elementor' ),
				),
				'default'     => 'excerpt',
				'label_block' => true,
				'condition'   => array(
					'premium_blog_excerpt' => 'yes',
				),
			)
		);

		$this->add_control(
			'premium_blog_excerpt_length',
			array(
				'label'     => __( 'Excerpt Length', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::NUMBER,
				'default'   => 22,
				'condition' => array(
					'premium_blog_excerpt' => 'yes',
					'content_source'       => 'excerpt',
				),
			)
		);

		$this->add_control(
			'premium_blog_excerpt_type',
			array(
				'label'       => __( 'Excerpt Type', 'premium-addons-for-elementor' ),
				'type'        => Controls_Manager::SELECT,
				'options'     => array(
					'dots' => __( 'Dots', 'premium-addons-for-elementor' ),
					'link' => __( 'Link', 'premium-addons-for-elementor' ),
				),
				'default'     => 'dots',
				'label_block' => true,
				'condition'   => array(
					'premium_blog_excerpt' => 'yes',
				),
			)
		);

		$this->add_control(
			'read_more_full_width',
			array(
				'label'        => __( 'Full Width', 'premium-addons-for-elementor' ),
				'type'         => Controls_Manager::SWITCHER,
				'prefix_class' => 'premium-blog-cta-full-',
				'condition'    => array(
					'premium_blog_excerpt'      => 'yes',
					'premium_blog_excerpt_type' => 'link',
				),
			)
		);

		$this->add_control(
			'premium_blog_excerpt_text',
			array(
				'label'     => __( 'Read More Text', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::TEXT,
				'default'   => __( 'Read More »', 'premium-addons-for-elementor' ),
				'condition' => array(
					'premium_blog_excerpt'      => 'yes',
					'premium_blog_excerpt_type' => 'link',
				),
			)
		);

		$this->add_control(
			'premium_blog_author_meta',
			array(
				'label'   => __( 'Author Meta', 'premium-addons-for-elementor' ),
				'type'    => Controls_Manager::SWITCHER,
				'default' => 'yes',
			)
		);

		$this->add_control(
			'premium_blog_date_meta',
			array(
				'label'   => __( 'Date Meta', 'premium-addons-for-elementor' ),
				'type'    => Controls_Manager::SWITCHER,
				'default' => 'yes',
			)
		);

		$this->add_control(
			'premium_blog_categories_meta',
			array(
				'label'       => __( 'Categories Meta', 'premium-addons-for-elementor' ),
				'type'        => Controls_Manager::SWITCHER,
				'description' => __( 'Display or hide categories meta', 'premium-addons-for-elementor' ),
				'default'     => 'yes',
			)
		);

		$this->add_control(
			'premium_blog_comments_meta',
			array(
				'label'       => __( 'Comments Meta', 'premium-addons-for-elementor' ),
				'description' => __( 'Display or hide comments meta', 'premium-addons-for-elementor' ),
				'type'        => Controls_Manager::SWITCHER,
				'default'     => 'yes',
			)
		);

		$this->add_control(
			'premium_blog_tags_meta',
			array(
				'label'       => __( 'Tags Meta', 'premium-addons-for-elementor' ),
				'type'        => Controls_Manager::SWITCHER,
				'description' => __( 'Display or hide post tags', 'premium-addons-for-elementor' ),
			)
		);

		$this->add_responsive_control(
			'post_text_align',
			array(
				'label'        => __( 'Alignment', 'premium-addons-for-elementor' ),
				'type'         => Controls_Manager::CHOOSE,
				'options'      => array(
					'left'    => array(
						'title' => __( 'Left', 'premium-addons-for-elementor' ),
						'icon'  => 'eicon-text-align-left',
					),
					'center'  => array(
						'title' => __( 'Center', 'premium-addons-for-elementor' ),
						'icon'  => 'eicon-text-align-center',
					),
					'right'   => array(
						'title' => __( 'Right', 'premium-addons-for-elementor' ),
						'icon'  => 'eicon-text-align-right',
					),
					'justify' => array(
						'title' => __( 'Justify', 'premium-addons-for-elementor' ),
						'icon'  => 'eicon-text-align-justify',
					),
				),
				'toggle'       => false,
				'default'      => 'left',
				'prefix_class' => 'premium-blog-align-',
				'selectors'    => array(
					'{{WRAPPER}} .premium-blog-content-wrapper' => 'text-align: {{VALUE}};',
				),
			)
		);

		$this->add_responsive_control(
			'post_bottom_spacing',
			array(
				'label'      => __( 'Bottom Spacing', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', '%', 'em' ),
				'range'      => array(
					'px' => array(
						'min' => 1,
						'max' => 200,
					),
				),
				'default'    => array(
					'size' => 5,
					'unit' => 'px',
				),
				'selectors'  => array(
					'{{WRAPPER}} .premium-blog-post-outer-container' => 'margin-bottom: {{SIZE}}{{UNIT}}',
				),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'premium_blog_general_settings',
			array(
				'label' => __( 'Featured Image', 'premium-addons-for-elementor' ),
			)
		);

		$this->add_control(
			'show_featured_image',
			array(
				'label'     => __( 'Show Featured Image', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::SWITCHER,
				'default'   => 'yes',
				'condition' => array(
					'premium_blog_skin!' => 'banner',
				),
			)
		);

		$featured_image_conditions = array(
			'show_featured_image' => 'yes',
		);

		$this->add_group_control(
			Group_Control_Image_Size::get_type(),
			array(
				'name'      => 'featured_image',
				'default'   => 'full',
				'condition' => $featured_image_conditions,
			)
		);

		$this->add_control(
			'premium_blog_hover_color_effect',
			array(
				'label'       => __( 'Overlay Effect', 'premium-addons-for-elementor' ),
				'type'        => Controls_Manager::SELECT,
				'description' => __( 'Choose an overlay color effect', 'premium-addons-for-elementor' ),
				'options'     => array(
					'none'     => __( 'None', 'premium-addons-for-elementor' ),
					'framed'   => __( 'Framed', 'premium-addons-for-elementor' ),
					'diagonal' => __( 'Diagonal', 'premium-addons-for-elementor' ),
					'bordered' => __( 'Bordered', 'premium-addons-for-elementor' ),
					'squares'  => __( 'Squares', 'premium-addons-for-elementor' ),
				),
				'default'     => 'framed',
				'label_block' => true,
				'condition'   => array_merge(
					$featured_image_conditions,
					array(
						'premium_blog_skin' => array( 'modern', 'cards' ),
					)
				),
			)
		);

		$this->add_control(
			'premium_blog_hover_image_effect',
			array(
				'label'       => __( 'Hover Effect', 'premium-addons-for-elementor' ),
				'type'        => Controls_Manager::SELECT,
				'description' => __( 'Choose a hover effect for the image', 'premium-addons-for-elementor' ),
				'options'     => array(
					'none'    => __( 'None', 'premium-addons-for-elementor' ),
					'zoomin'  => __( 'Zoom In', 'premium-addons-for-elementor' ),
					'zoomout' => __( 'Zoom Out', 'premium-addons-for-elementor' ),
					'scale'   => __( 'Scale', 'premium-addons-for-elementor' ),
					'gray'    => __( 'Grayscale', 'premium-addons-for-elementor' ),
					'blur'    => __( 'Blur', 'premium-addons-for-elementor' ),
					'bright'  => __( 'Bright', 'premium-addons-for-elementor' ),
					'sepia'   => __( 'Sepia', 'premium-addons-for-elementor' ),
					'trans'   => __( 'Translate', 'premium-addons-for-elementor' ),
				),
				'default'     => 'zoomin',
				'label_block' => true,
				'condition'   => $featured_image_conditions,
			)
		);

		$this->add_responsive_control(
			'premium_blog_thumb_min_height',
			array(
				'label'      => __( 'Height', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', 'em', 'custom' ),
				'range'      => array(
					'px' => array(
						'min' => 0,
						'max' => 600,
					),
					'em' => array(
						'min' => 1,
						'max' => 60,
					),
				),
				'condition'  => array_merge( $featured_image_conditions ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-blog-thumbnail-container img' => 'height: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'premium_blog_thumbnail_fit',
			array(
				'label'     => __( 'Thumbnail Fit', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::SELECT,
				'options'   => array(
					'cover'   => __( 'Cover', 'premium-addons-for-elementor' ),
					'fill'    => __( 'Fill', 'premium-addons-for-elementor' ),
					'contain' => __( 'Contain', 'premium-addons-for-elementor' ),
				),
				'default'   => 'cover',
				'selectors' => array(
					'{{WRAPPER}} .premium-blog-thumbnail-container img' => 'object-fit: {{VALUE}}',
				),
				'condition' => array_merge( $featured_image_conditions ),
			)
		);

		$this->add_control(
			'shape_divider',
			array(
				'label'       => __( 'Shape Divider', 'premium-addons-for-elementor' ),
				'type'        => Controls_Manager::SELECT,
				'options'     => array(
					'none'             => __( 'None', 'premium-addons-for-elementor' ),
					'arrow'            => __( 'Arrow', 'premium-addons-for-elementor' ),
					'book'             => __( 'Book', 'premium-addons-for-elementor' ),
					'cloud'            => __( 'Clouds', 'premium-addons-for-elementor' ),
					'curve'            => __( 'Curve', 'premium-addons-for-elementor' ),
					'curve-asymmetric' => __( 'Curve Asymmetric', 'premium-addons-for-elementor' ),
					'drops'            => __( 'Drop', 'premium-addons-for-elementor' ),
					'fan'              => __( 'Fan', 'premium-addons-for-elementor' ),
					'mountain'         => __( 'Mountains', 'premium-addons-for-elementor' ),
					'pyramids'         => __( 'Pyramids', 'premium-addons-for-elementor' ),
					'split'            => __( 'Split', 'premium-addons-for-elementor' ),
					'triangle'         => __( 'Triangle', 'premium-addons-for-elementor' ),
					'tri_asymmetric'   => __( 'Asymmetric Triangle', 'premium-addons-for-elementor' ),
					'tilt'             => __( 'Tilt', 'premium-addons-for-elementor' ),
					'tilt-opacity'     => __( 'Tilt Opacity', 'premium-addons-for-elementor' ),
					'waves'            => __( 'Wave', 'premium-addons-for-elementor' ),
					'waves-brush'      => __( 'Waves Brush', 'premium-addons-for-elementor' ),
					'waves-pattern'    => __( 'Waves Pattern', 'premium-addons-for-elementor' ),
					'zigzag'           => __( 'Zigzag', 'premium-addons-for-elementor' ),
				),
				'default'     => 'none',
				'label_block' => true,
				'condition'   => array(
					'show_featured_image' => 'yes',
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
			'https://premiumaddons.com/docs/elementor-recent-posts-notification-widget/' => __( 'Getting started »', 'premium-addons-for-elementor' ),
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
			'icon_style',
			array(
				'label'     => __( 'Icon Style', 'premium-addons-for-elementor' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => array(
					'premium_blog_skin!' => $options['skin_condition'],
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'      => 'text_typography',
				'selector'  => '{{WRAPPER}} .premium-not-icon-text',
				'condition' => array(
					'icon_type' => 'text',
				),
			)
		);

		$this->start_controls_tabs( 'icon_style_tabs' );

		$this->start_controls_tab(
			'icon_style_normal',
			array(
				'label' => __( 'Normal', 'premium-addons-for-elementor' ),
			)
		);

		$this->add_control(
			'icon_color',
			array(
				'label'     => __( 'Icon/Text Color', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .pa-rec-not-icon-wrap .premium-drawable-icon *, {{WRAPPER}} .pa-rec-not-icon-wrap svg:not([class*="premium-"])' => 'fill: {{VALUE}};',
					'{{WRAPPER}} .premium-not-icon-text' => 'color: {{VALUE}};',
				),
				'condition' => array(
					'icon_type!' => array( 'image', 'animation' ),
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
						'{{WRAPPER}} .pa-rec-not-icon-wrap .premium-drawable-icon *, {{WRAPPER}} .pa-rec-not-icon-wrap svg:not([class*="premium-"])' => 'stroke: {{VALUE}};',
					),
				)
			);
		}

		$this->add_control(
			'icon_back_color',
			array(
				'label'     => __( 'Background Color', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .pa-rec-not-icon-wrap' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'     => 'icon_border',
				'selector' => '{{WRAPPER}} .pa-rec-not-icon-wrap',
			)
		);

		$this->add_control(
			'icon_border_radius',
			array(
				'label'      => __( 'Border Radius', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', '%', 'em' ),
				'selectors'  => array(
					'{{WRAPPER}} .pa-rec-not-icon-wrap' => 'border-radius: {{SIZE}}{{UNIT}};',
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
					'{{WRAPPER}} .pa-rec-not-icon-wrap' => 'border-radius: {{VALUE}};',
				),
				'condition' => array(
					'icon_adv_radius' => 'yes',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Text_Shadow::get_type(),
			array(
				'name'      => 'text_shadow_normal',
				'selector'  => '{{WRAPPER}} .premium-not-icon-text',
				'condition' => array(
					'icon_type' => 'text',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name'     => 'box_shadow_normal',
				'selector' => '{{WRAPPER}} .pa-rec-not-icon-wrap',
			)
		);

		$this->add_responsive_control(
			'icon_margin',
			array(
				'label'      => __( 'Margin', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .pa-rec-not-icon-wrap' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
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
					'{{WRAPPER}} .pa-rec-not-icon-wrap' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'icon_style_hover',
			array(
				'label' => __( 'Hover', 'premium-addons-for-elementor' ),
			)
		);

		$this->add_control(
			'icon_color_hover',
			array(
				'label'     => __( 'Icon/Text Color', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .pa-rec-not-icon-wrap:hover *, {{WRAPPER}} .pa-rec-not-icon-wrap:hover svg:not([class*="premium-"])' => 'fill: {{VALUE}};',
					'{{WRAPPER}} .pa-rec-not-icon-wrap:hover .premium-not-icon-text' => 'color: {{VALUE}};',
				),
				'condition' => array(
					'icon_type!' => array( 'image', 'animation' ),
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
						'icon_type' => array( 'icon', 'svg' ),
					),
					'selectors' => array(
						'{{WRAPPER}} .pa-rec-not-icon-wrap:hover .premium-drawable-icon *, {{WRAPPER}} .pa-rec-not-icon-wrap:hover svg:not([class*="premium-"])' => 'stroke: {{VALUE}};',
					),
				)
			);
		}

		$this->add_control(
			'icon_back_color_hover',
			array(
				'label'     => __( 'Background Color', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .pa-rec-not-icon-wrap:hover' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'     => 'icon_border_hover',
				'selector' => '{{WRAPPER}} .pa-rec-not-icon-wrap:hover',
			)
		);

		$this->add_control(
			'icon_border_radius_Hover',
			array(
				'label'      => __( 'Border Radius', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', '%', 'em' ),
				'selectors'  => array(
					'{{WRAPPER}} .pa-rec-not-icon-wrap:hover' => 'border-radius: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'icon_adv_radius_hover',
			array(
				'label'       => __( 'Advanced Border Radius', 'premium-addons-for-elementor' ),
				'type'        => Controls_Manager::SWITCHER,
				'description' => __( 'Apply custom radius values. Get the radius value from ', 'premium-addons-for-elementor' ) . '<a href="https://9elements.github.io/fancy-border-radius/" target="_blank">here</a>',
			)
		);

		$this->add_control(
			'icon_adv_radius_value_hover',
			array(
				'label'     => __( 'Border Radius', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::TEXT,
				'dynamic'   => array( 'active' => true ),
				'selectors' => array(
					'{{WRAPPER}} .pa-rec-not-icon-wrap:hover' => 'border-radius: {{VALUE}};',
				),
				'condition' => array(
					'icon_adv_radius_hover' => 'yes',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Text_Shadow::get_type(),
			array(
				'name'      => 'text_shadow_hover',
				'selector'  => '{{WRAPPER}} .pa-rec-not-icon-wrap:hover .premium-not-icon-text',
				'condition' => array(
					'icon_type' => 'text',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name'     => 'box_shadow_hover',
				'selector' => '{{WRAPPER}} .pa-rec-not-icon-wrap:hover',
			)
		);

		$this->add_responsive_control(
			'icon_margin_hover',
			array(
				'label'      => __( 'Margin', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .pa-rec-not-icon-wrap:hover' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'icon_padding_hover',
			array(
				'label'      => __( 'Padding', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .pa-rec-not-icon-wrap:hover' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();

		$this->start_controls_section(
			'number_style',
			array(
				'label'     => __( 'Number Style', 'premium-addons-for-elementor' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => array(
					'premium_blog_skin!' => $options['skin_condition'],
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'number_typography',
				'selector' => '{{WRAPPER}} .pa-rec-not-number span',
			)
		);

		$this->start_controls_tabs( 'number_style_tabs' );

		$this->start_controls_tab(
			'number_style_normal',
			array(
				'label' => __( 'Normal', 'premium-addons-for-elementor' ),
			)
		);

		$this->add_control(
			'number_color',
			array(
				'label'     => __( 'Color', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .pa-rec-not-number' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'number_back_color',
			array(
				'label'     => __( 'Background Color', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .pa-rec-not-number' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'     => 'number_border',
				'selector' => '{{WRAPPER}} .pa-rec-not-number',
			)
		);

		$this->add_control(
			'number_border_radius',
			array(
				'label'      => __( 'Border Radius', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', '%', 'em' ),
				'selectors'  => array(
					'{{WRAPPER}} .pa-rec-not-number' => 'border-radius: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'number_adv_radius',
			array(
				'label'       => __( 'Advanced Border Radius', 'premium-addons-for-elementor' ),
				'type'        => Controls_Manager::SWITCHER,
				'description' => __( 'Apply custom radius values. Get the radius value from ', 'premium-addons-for-elementor' ) . '<a href="https://9elements.github.io/fancy-border-radius/" target="_blank">here</a>',
			)
		);

		$this->add_control(
			'number_adv_radius_value',
			array(
				'label'     => __( 'Border Radius', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::TEXT,
				'dynamic'   => array( 'active' => true ),
				'selectors' => array(
					'{{WRAPPER}} .pa-rec-not-number' => 'border-radius: {{VALUE}};',
				),
				'condition' => array(
					'number_adv_radius' => 'yes',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Text_Shadow::get_type(),
			array(
				'name'     => 'number_shadow_normal',
				'selector' => '{{WRAPPER}} .pa-rec-not-number',
			)
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name'     => 'number_box_shadow_normal',
				'selector' => '{{WRAPPER}} .pa-rec-not-number',
			)
		);

		$this->add_responsive_control(
			'number_margin',
			array(
				'label'      => __( 'Margin', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .pa-rec-not-number' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'number_padding',
			array(
				'label'      => __( 'Padding', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .pa-rec-not-number' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'number_style_hover',
			array(
				'label' => __( 'Hover', 'premium-addons-for-elementor' ),
			)
		);

		$this->add_control(
			'number_color_hover',
			array(
				'label'     => __( 'Color', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .pa-rec-not-icon-wrap:hover .pa-rec-not-number' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'number_back_color_hover',
			array(
				'label'     => __( 'Background Color', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .pa-rec-not-icon-wrap:hover .pa-rec-not-number' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'     => 'number_border_hover',
				'selector' => '{{WRAPPER}} .pa-rec-not-icon-wrap:hover .pa-rec-not-number',
			)
		);

		$this->add_control(
			'number_border_radius_Hover',
			array(
				'label'      => __( 'Border Radius', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', '%', 'em' ),
				'selectors'  => array(
					'{{WRAPPER}} .pa-rec-not-icon-wrap:hover .pa-rec-not-number' => 'border-radius: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'number_adv_radius_hover',
			array(
				'label'       => __( 'Advanced Border Radius', 'premium-addons-for-elementor' ),
				'type'        => Controls_Manager::SWITCHER,
				'description' => __( 'Apply custom radius values. Get the radius value from ', 'premium-addons-for-elementor' ) . '<a href="https://9elements.github.io/fancy-border-radius/" target="_blank">here</a>',
			)
		);

		$this->add_control(
			'number_adv_radius_value_hover',
			array(
				'label'     => __( 'Border Radius', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::TEXT,
				'dynamic'   => array( 'active' => true ),
				'selectors' => array(
					'{{WRAPPER}} .pa-rec-not-icon-wrap:hover .pa-rec-not-number' => 'border-radius: {{VALUE}};',
				),
				'condition' => array(
					'number_adv_radius_hover' => 'yes',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Text_Shadow::get_type(),
			array(
				'name'     => 'number_shadow_hover',
				'selector' => '{{WRAPPER}} .pa-rec-not-icon-wrap:hover .pa-rec-not-number',
			)
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name'     => 'number_box_shadow_hover',
				'selector' => '{{WRAPPER}} .pa-rec-not-icon-wrap:hover .pa-rec-not-number',
			)
		);

		$this->add_responsive_control(
			'number_margin_hover',
			array(
				'label'      => __( 'Margin', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .pa-rec-not-icon-wrap:hover .pa-rec-not-number' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'number_padding_hover',
			array(
				'label'      => __( 'Padding', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .pa-rec-not-icon-wrap:hover .pa-rec-not-number' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();

		$this->start_controls_section(
			'posts_box_style',
			array(
				'label'     => __( 'Posts Box Style', 'premium-addons-for-elementor' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => array(
					'premium_blog_skin!' => $options['skin_condition'],
				),
			)
		);

		$this->add_control(
			'overlay_color',
			array(
				'label'     => __( 'Overlay Color', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .pa-rec-posts-overlay' => 'background-color: {{VALUE}};',
				),
				'condition' => array(
					'overlay' => 'yes',
				),
			)
		);

		$this->add_control(
			'posts_box_back',
			array(
				'label'     => __( 'Background Color', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .pa-rec-posts-container' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name'     => 'posts_box_shadow',
				'selector' => '{{WRAPPER}} .pa-rec-posts-container',
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'     => 'posts_box_border',
				'selector' => '{{WRAPPER}} .pa-rec-posts-container',
			)
		);

		$this->add_control(
			'posts_box_border_radius',
			array(
				'label'      => __( 'Border Radius', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', '%', 'em' ),
				'selectors'  => array(
					'{{WRAPPER}} .pa-rec-posts-container' => 'border-radius: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'posts_box_padding',
			array(
				'label'      => __( 'Padding', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .pa-rec-posts-container' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'header_style',
			array(
				'label'     => __( 'Header Style', 'premium-addons-for-elementor' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => array(
					'premium_blog_skin!' => $options['skin_condition'],
				),
			)
		);

		$this->start_controls_tabs( 'header_style_tabs' );

		$this->start_controls_tab(
			'header_title_tab',
			array(
				'label' => __( 'Title', 'premium-addons-for-elementor' ),
			)
		);

		$this->add_control(
			'header_title_color',
			array(
				'label'     => __( 'Color', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .pa-rec-title' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'header_icon_color',
			array(
				'label'     => __( 'Icon Color', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .pa-rec-title-icon-wrap .premium-drawable-icon *, {{WRAPPER}} .pa-rec-title-icon-wrap svg:not([class*="premium-"])' => 'fill: {{VALUE}};',
				),
				'condition' => array(
					'header_icon_sw'    => 'yes',
					'header_icon_type!' => array( 'image', 'animation' ),
				),
			)
		);

		if ( $draw_icon ) {
			$this->add_control(
				'header_stroke_color',
				array(
					'label'     => __( 'Stroke Color', 'premium-addons-for-elementor' ),
					'type'      => Controls_Manager::COLOR,
					'global'    => array(
						'default' => Global_Colors::COLOR_ACCENT,
					),
					'condition' => array(
						'header_icon_sw'   => 'yes',
						'header_icon_type' => array( 'icon', 'svg' ),
					),
					'selectors' => array(
						'{{WRAPPER}} .pa-rec-title-icon-wrap .premium-drawable-icon *, {{WRAPPER}} .pa-rec-title-icon-wrap svg:not([class*="premium-"])' => 'stroke: {{VALUE}};',
					),
				)
			);
		}

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'header_title_typography',
				'selector' => '{{WRAPPER}} .pa-rec-title',
			)
		);

		$this->add_group_control(
			Group_Control_Text_Shadow::get_type(),
			array(
				'name'     => 'header_title_shadow',
				'selector' => '{{WRAPPER}} .pa-rec-title',
			)
		);

		$this->add_responsive_control(
			'header_title_margin',
			array(
				'label'      => __( 'Margin', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .pa-rec-title' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'close_icon_tab',
			array(
				'label' => __( 'Close Icon', 'premium-addons-for-elementor' ),
			)
		);

		$this->add_control(
			'close_icon_size',
			array(
				'label'      => __( 'Size', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', '%', 'em' ),
				'selectors'  => array(
					'{{WRAPPER}} .pa-rec-posts-close-icon' => 'font-size: {{SIZE}}{{UNIT}}; width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};',
					// '{{WRAPPER}} .pa-rec-posts-close'      => '',
				),
			)
		);

		$this->add_control(
			'close_icon_color',
			array(
				'label'     => __( 'Color', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .pa-rec-posts-close-icon' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'close_icon_backcolor',
			array(
				'label'     => __( 'Background Color', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .pa-rec-posts-close' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Text_Shadow::get_type(),
			array(
				'label'    => __( 'Shadow', 'premium-addons-for-elementor' ),
				'name'     => 'close_icon_shadow',
				'selector' => '{{WRAPPER}} .pa-rec-posts-close-icon',
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'     => 'close_icon_border',
				'selector' => '{{WRAPPER}} .pa-rec-posts-close',
			)
		);

		$this->add_control(
			'close_icon_border_radius',
			array(
				'label'      => __( 'Border Radius', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', '%', 'em' ),
				'selectors'  => array(
					'{{WRAPPER}} .pa-rec-posts-close' => 'border-radius: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'close_icon_margin',
			array(
				'label'      => __( 'Margin', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .pa-rec-posts-close' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'close_icon_padding',
			array(
				'label'      => __( 'Padding', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .pa-rec-posts-close' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->add_control(
			'header_back',
			array(
				'label'     => __( 'Background Color', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'separator' => 'before',
				'selectors' => array(
					'{{WRAPPER}} .pa-rec-posts-header' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'     => 'header_border',
				'selector' => '{{WRAPPER}} .pa-rec-posts-header',
			)
		);

		$this->add_control(
			'header_border_radius',
			array(
				'label'      => __( 'Border Radius', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', '%', 'em' ),
				'selectors'  => array(
					'{{WRAPPER}} .pa-rec-posts-header' => 'border-radius: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'header_margin',
			array(
				'label'      => __( 'Margin', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .pa-rec-posts-header' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'header_padding',
			array(
				'label'      => __( 'Padding', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .pa-rec-posts-header' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'image_style_section',
			array(
				'label'     => __( 'Post Image', 'premium-addons-for-elementor' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => array(
					'show_featured_image' => 'yes',
					'premium_blog_skin!'  => $options['skin_condition'],
				),
			)
		);

		$this->add_control(
			'plus_color',
			array(
				'label'     => __( 'Plus Sign Color', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .premium-blog-thumbnail-container:before, {{WRAPPER}} .premium-blog-thumbnail-container:after' => 'background-color: {{VALUE}} !important',
				),
				'condition' => array(
					'premium_blog_skin' => array( 'modern', 'cards' ),
				),
			)
		);

		$this->add_control(
			'thumbnail_overlay_color',
			array(
				'label'     => __( 'Overlay Color', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .premium-blog-framed-effect, {{WRAPPER}} .premium-blog-bordered-effect,{{WRAPPER}} .premium-blog-squares-effect:before, {{WRAPPER}} .premium-blog-squares-effect:after, {{WRAPPER}} .premium-blog-squares-square-container:before, {{WRAPPER}} .premium-blog-squares-square-container:after, {{WRAPPER}} .premium-blog-thumbnail-overlay' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'border_effect_color',
			array(
				'label'     => __( 'Border Color', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'global'    => array(
					'default' => Global_Colors::COLOR_SECONDARY,
				),
				'condition' => array(
					'premium_blog_hover_color_effect' => 'bordered',
				),
				'selectors' => array(
					'{{WRAPPER}} .premium-blog-post-link:before, {{WRAPPER}} .premium-blog-post-link:after' => 'border-color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Css_Filter::get_type(),
			array(
				'name'     => 'css_filters',
				'selector' => '{{WRAPPER}} .premium-blog-thumbnail-container img',
			)
		);

		$this->add_group_control(
			Group_Control_Css_Filter::get_type(),
			array(
				'name'     => 'hover_css_filters',
				'label'    => __( 'Hover CSS Filters', 'premium-addons-for-elementor' ),
				'selector' => '{{WRAPPER}} .premium-blog-post-container:hover .premium-blog-thumbnail-container img',
			)
		);

		$this->add_control(
			'divider_heading',
			array(
				'label'     => __( 'Shape Divider', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
				'condition' => array(
					'show_featured_image' => 'yes',
					'shape_divider!'      => 'none',
				),
			)
		);

		$this->add_control(
			'divider_fill_color',
			array(
				'label'     => __( 'Color', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .premium-blog-masked .premium-blog-thumbnail-container svg' => 'fill: {{VALUE}}',
				),
				'condition' => array(
					'show_featured_image' => 'yes',
					'shape_divider!'      => 'none',
				),
			)
		);

		$this->add_responsive_control(
			'divider_width',
			array(
				'label'      => __( 'Width', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', 'em', '%' ),
				'range'      => array(
					'px' => array(
						'min' => 0,
						'max' => 1000,
					),
				),
				'selectors'  => array(
					'{{WRAPPER}} .premium-blog-masked .premium-blog-thumbnail-container svg'  => 'width: {{SIZE}}{{UNIT}}',
				),
				'condition'  => array(
					'show_featured_image' => 'yes',
					'shape_divider!'      => 'none',
				),
			)
		);

		$this->add_responsive_control(
			'divider_height',
			array(
				'label'      => __( 'Height', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', 'em', '%' ),
				'range'      => array(
					'px' => array(
						'min' => 0,
						'max' => 300,
					),
				),
				'selectors'  => array(
					'{{WRAPPER}} .premium-blog-masked .premium-blog-thumbnail-container svg'  => 'height: {{SIZE}}{{UNIT}}',
				),
				'condition'  => array(
					'show_featured_image' => 'yes',
					'shape_divider!'      => 'none',
				),
			)
		);

		$is_rtl = is_rtl() ? 'right' : 'left';

		$this->add_responsive_control(
			'divider_horizontal',
			array(
				'label'      => __( 'Horizontal Postion', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', 'em', '%' ),
				'range'      => array(
					'px' => array(
						'min' => 0,
						'max' => 300,
					),
				),
				'selectors'  => array(
					'{{WRAPPER}} .premium-blog-masked .premium-blog-thumbnail-container svg'  => $is_rtl . ': {{SIZE}}{{UNIT}}',
				),
				'condition'  => array(
					'show_featured_image' => 'yes',
					'shape_divider!'      => 'none',
				),
			)
		);

		$this->add_responsive_control(
			'divider_vertical',
			array(
				'label'      => __( 'Vertical Postion', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', 'em', '%' ),
				'range'      => array(
					'px' => array(
						'min' => -50,
						'max' => 300,
					),
				),
				'selectors'  => array(
					'{{WRAPPER}} .premium-blog-masked .premium-blog-thumbnail-container svg'  => 'bottom: {{SIZE}}{{UNIT}}',
				),
				'condition'  => array(
					'show_featured_image' => 'yes',
					'shape_divider!'      => 'none',
				),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'post_title_style',
			array(
				'label'     => __( 'Title', 'premium-addons-for-elementor' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => array(
					'premium_blog_skin!' => $options['skin_condition'],
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'post_title_typo',
				'global'   => array(
					'default' => Global_Typography::TYPOGRAPHY_PRIMARY,
				),
				'selector' => '{{WRAPPER}} .premium-blog-entry-title, {{WRAPPER}} .premium-blog-entry-title a',
			)
		);

		$this->add_control(
			'post_title_color',
			array(
				'label'     => __( 'Color', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'global'    => array(
					'default' => Global_Colors::COLOR_SECONDARY,
				),
				'selectors' => array(
					'{{WRAPPER}} .premium-blog-entry-title a'  => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'post_title_hover_color',
			array(
				'label'     => __( 'Hover Color', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'global'    => array(
					'default' => Global_Colors::COLOR_PRIMARY,
				),
				'selectors' => array(
					'{{WRAPPER}} .premium-blog-entry-title:hover a'  => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_responsive_control(
			'post_title_spacing',
			array(
				'label'      => __( 'Bottom Spacing', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-blog-entry-title'  => 'margin-bottom: {{SIZE}}{{UNIT}}',
				),
			)
		);

		$this->end_controls_section();

		if ( $papro_activated ) {
			do_action( 'pa_notification_cats_controls', $this );
		}

		$this->start_controls_section(
			'meta_style_section',
			array(
				'label'     => __( 'Metadata', 'premium-addons-for-elementor' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => array(
					'premium_blog_skin!' => $options['skin_condition'],
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'meta_typo',
				'global'   => array(
					'default' => Global_Typography::TYPOGRAPHY_SECONDARY,
				),
				'selector' => '{{WRAPPER}} .premium-blog-meta-data',
			)
		);

		$this->add_control(
			'meta_color',
			array(
				'label'     => __( 'Metadata Color', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .premium-blog-meta-data > *'  => 'color: {{VALUE}}',
				),
			)
		);

		$this->add_control(
			'meta_hover_color',
			array(
				'label'     => __( 'Links Hover Color', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .premium-blog-meta-data:not(.premium-blog-post-time):hover > *'  => 'color: {{VALUE}}',
				),
			)
		);

		$this->add_control(
			'separator_color',
			array(
				'label'     => __( 'Separator Color', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'separator' => 'before',
				'selectors' => array(
					'{{WRAPPER}} .premium-blog-meta-separator'  => 'color: {{VALUE}}',
				),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'premium_blog_content_style_section',
			array(
				'label'     => __( 'Content Box', 'premium-addons-for-elementor' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => array(
					'premium_blog_skin!' => $options['skin_condition'],
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'      => 'post_content_typo',
				'selector'  => '{{WRAPPER}} .premium-blog-post-content',
				'condition' => array(
					'content_source' => 'excerpt',
				),
			)
		);

		$this->add_control(
			'post_content_color',
			array(
				'label'     => __( 'Text Color', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'global'    => array(
					'default' => Global_Colors::COLOR_TEXT,
				),
				'selectors' => array(
					'{{WRAPPER}} .premium-blog-post-content'  => 'color: {{VALUE}};',
				),
				'condition' => array(
					'content_source' => 'excerpt',
				),
			)
		);

		$this->add_responsive_control(
			'excerpt_text_margin',
			array(
				'label'      => __( 'Text Margin', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-blog-post-content' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
				'condition'  => array(
					'content_source' => 'excerpt',
				),
			)
		);

		$this->add_control(
			'post_background_color',
			array(
				'label'     => __( 'Background Color', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'separator' => 'before',
				'selectors' => array(
					'{{WRAPPER}} .premium-blog-content-wrapper'  => 'background-color: {{VALUE}};',
				),
				'condition' => array(
					'premium_blog_skin!' => 'banner',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			array(
				'name'      => 'post_background_color',
				'types'     => array( 'classic', 'gradient' ),
				'selector'  => '{{WRAPPER}} .premium-blog-content-wrapper',
				'condition' => array(
					'premium_blog_skin' => 'banner',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name'     => 'post_box_shadow',
				'selector' => '{{WRAPPER}} .premium-blog-content-wrapper',
			)
		);

		$this->add_responsive_control(
			'post_content_margin',
			array(
				'label'      => __( 'Margin', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-blog-content-wrapper' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'post_content_padding',
			array(
				'label'      => __( 'Padding', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-blog-content-wrapper' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'read_more_style',
			array(
				'label'     => __( 'Button', 'premium-addons-for-elementor' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => array(
					'premium_blog_excerpt'      => 'yes',
					'premium_blog_excerpt_type' => 'link',
					'premium_blog_skin!'        => $options['skin_condition'],
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'read_more_typo',
				'selector' => '{{WRAPPER}} .premium-blog-excerpt-link',
			)
		);

		$this->add_responsive_control(
			'read_more_spacing',
			array(
				'label'     => __( 'Spacing', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::SLIDER,
				'selectors' => array(
					'{{WRAPPER}} .premium-blog-excerpt-link'  => 'margin-top: {{SIZE}}px',
				),
			)
		);

		$this->start_controls_tabs( 'read_more_style_tabs' );

		$this->start_controls_tab(
			'read_more_tab_normal',
			array(
				'label' => __( 'Normal', 'premium-addons-for-elementor' ),

			)
		);

		$this->add_control(
			'read_more_color',
			array(
				'label'     => __( 'Color', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .premium-blog-excerpt-link'  => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'read_more_background_color',
			array(
				'label'     => __( 'Background Color', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .premium-blog-excerpt-link'  => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'     => 'read_more_border',
				'selector' => '{{WRAPPER}} .premium-blog-excerpt-link',
			)
		);

		$this->add_control(
			'read_more_border_radius',
			array(
				'label'      => __( 'Border Radius', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-blog-excerpt-link'  => 'border-radius: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'read_more_tab_hover',
			array(
				'label' => __( 'Hover', 'premium-addons-for-elementor' ),
			)
		);

		$this->add_control(
			'premium_blog_read_more_hover_color',
			array(
				'label'     => __( 'Color', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .premium-blog-excerpt-link:hover'  => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'read_more_hover_background_color',
			array(
				'label'     => __( 'Background Color', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .premium-blog-excerpt-link:hover'  => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'     => 'read_more_hover_border',
				'selector' => '{{WRAPPER}} .premium-blog-excerpt-link:hover',
			)
		);

		$this->add_control(
			'read_more_hover_border_radius',
			array(
				'label'      => __( 'Border Radius', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-blog-excerpt-link:hover'  => 'border-radius: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->add_responsive_control(
			'read_more_padding',
			array(
				'label'      => __( 'Padding', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'separator'  => 'before',
				'selectors'  => array(
					'{{WRAPPER}} .premium-blog-excerpt-link' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'tags_style_section',
			array(
				'label'     => __( 'Tags', 'premium-addons-for-elementor' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => array(
					'premium_blog_tags_meta' => 'yes',
					'premium_blog_skin!'     => $options['skin_condition'],
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'tags_typo',
				'global'   => array(
					'default' => Global_Typography::TYPOGRAPHY_SECONDARY,
				),
				'selector' => '{{WRAPPER}} .premium-blog-post-tags-container',
			)
		);

		$this->add_control(
			'tags_color',
			array(
				'label'     => __( 'Color', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'global'    => array(
					'default' => Global_Colors::COLOR_SECONDARY,
				),
				'selectors' => array(
					'{{WRAPPER}} .premium-blog-post-tags-container'  => 'color: {{VALUE}}',
				),
			)
		);

		$this->add_control(
			'tags_hover_color',
			array(
				'label'     => __( 'Hover Color', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .premium-blog-post-tags-container a:hover'  => 'color: {{VALUE}}',
				),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'box_style_section',
			array(
				'label' => __( 'Box', 'premium-addons-for-elementor' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_control(
			'box_background_color',
			array(
				'label'     => __( 'Background Color', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .premium-blog-post-container'  => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'     => 'box_border',
				'selector' => '{{WRAPPER}} .premium-blog-post-container',
			)
		);

		$this->add_control(
			'box_border_radius',
			array(
				'label'      => __( 'Border Radius', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-blog-post-container' => 'border-radius: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name'     => 'outer_box_shadow',
				'selector' => '{{WRAPPER}} .premium-blog-post-container',
			)
		);

		$this->add_responsive_control(
			'box_padding',
			array(
				'label'      => __( 'Spacing', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-blog-post-outer-container' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'inner_box_padding',
			array(
				'label'      => __( 'Padding', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-blog-post-container' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

	}

	/**
	 * Render Image Scroll widget output on the frontend.
	 *
	 * Written in PHP and used to generate the final HTML.
	 *
	 * @since 1.0.0
	 * @access protected
	 */
	protected function render() {

		$settings = $this->get_settings();

		$papro_activated = apply_filters( 'papro_activated', false );

		if ( ! $papro_activated && ( in_array( $settings['premium_blog_skin'], array( 'cards', 'banner' ), true ) || 'post' !== $settings['post_type_filter'] ) ) {
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

		$settings['widget_id']             = $this->get_id();
		$settings['premium_blog_cat_tabs'] = '';

		$blog_helper = $this->get_blog_helper();

		$blog_helper->set_widget_settings( $settings );

		$icon_type = $settings['icon_type'];

		if ( 'image' === $icon_type ) {

			if ( ! empty( $settings['image']['url'] ) ) {
				$image_html = Group_Control_Image_Size::get_attachment_image_html( $settings, 'thumbnail', 'image' );
			}

			if ( ! empty( $settings['image_with_no_posts']['url'] ) ) {
				$image_html_with_no_post = Group_Control_Image_Size::get_attachment_image_html( $settings, 'thumbnail', 'image_with_no_posts' );
			}
		}

		$this->add_render_attribute( 'icon_wrap', 'class', 'pa-rec-not-icon-wrap' );

		$this->add_render_attribute( 'number', 'class', 'pa-rec-not-number' );

		$this->add_render_attribute( 'posts_container', 'class', array( 'pa-rec-posts-container', 'elementor-invisible' ) );

		$this->add_render_attribute( 'title_wrap', 'class', 'pa-rec-title-wrap' );

		$data = $this->get_posts_number();

		$number = $data['number'];

		if ( $settings['cookies'] ) {

			$id = $this->get_id();

			$stored_posts = isset( $_COOKIE[ 'paRecentPosts' . $id ] ) && ! empty( $_COOKIE[ 'paRecentPosts' . $id ] ) ?
			sanitize_text_field( wp_unslash( $_COOKIE[ 'paRecentPosts' . $id ] ) ) : 'new';

			if ( 'new' !== $stored_posts ) {

				$difference = array_diff( explode( ',', $stored_posts ), explode( ',', $data['posts'] ) );

				$number = count( $difference );

			}
		}

		$this->add_render_attribute(
			'wrap',
			array(
				'class'       => 'pa-recent-notification',
				'data-recent' => $data['posts'],
			)
		);

		if ( 'animation' === $icon_type ) {

			$this->add_render_attribute(
				'lottie_icon',
				array(
					'class'               => array( 'premium-lottie-animation', 'premium-notification-icon' ),
					'data-lottie-url'     => $settings['lottie_url'],
					'data-lottie-loop'    => $settings['lottie_loop'],
					'data-lottie-reverse' => $settings['lottie_reverse'],
				)
			);

		} elseif ( 'icon' === $icon_type || 'svg' === $icon_type ) {

			$this->add_render_attribute( 'icon', 'class', array( 'premium-drawable-icon', 'premium-notification-icon' ) );

			if ( ( 'yes' === $settings['draw_svg'] && 'icon' === $icon_type ) || 'svg' === $icon_type ) {
				$this->add_render_attribute( 'icon', 'class', 'premium-not-icon' );
			}

			if ( 'yes' === $settings['draw_svg'] ) {
				$this->add_render_attribute( 'wrap', 'class', 'elementor-invisible' );

				if ( 'icon' === $icon_type ) {
					$this->add_render_attribute( 'icon', 'class', $settings['icon']['value'] );
				}

				$this->add_render_attribute(
					'icon',
					array(
						'class'            => 'premium-svg-drawer',
						'data-svg-reverse' => $settings['lottie_reverse'],
						'data-svg-loop'    => $settings['lottie_loop'],
						'data-svg-sync'    => $settings['svg_sync'],
						'data-svg-frames'  => $settings['frames'],
						'data-svg-yoyo'    => $settings['svg_yoyo'],
						'data-svg-point'   => $settings['lottie_reverse'] ? $settings['end_point']['size'] : $settings['start_point']['size'],
					)
				);

			} else {
				$this->add_render_attribute( 'icon', 'class', 'premium-svg-nodraw' );
			}
		}

		$header_icon_type = $settings['header_icon_type'];

		if ( 'yes' === $settings['header_icon_sw'] ) {

			$this->add_render_attribute( 'header_icon_wrap', 'class', 'pa-rec-title-icon-wrap' );

			if ( 'animation' === $header_icon_type ) {

				$this->add_render_attribute(
					'header_lottie_icon',
					array(
						'class'            => 'premium-lottie-animation',
						'data-lottie-url'  => $settings['header_lottie_url'],
						'data-lottie-loop' => $settings['header_lottie_loop'],
					)
				);

			} elseif ( 'icon' === $header_icon_type || 'svg' === $header_icon_type ) {

				$this->add_render_attribute( 'header_icon', 'class', 'premium-drawable-icon' );

				// if ( ( 'yes' === $settings['header_draw_svg'] && 'icon' === $header_icon_type ) || 'svg' === $header_icon_type ) {
				// $this->add_render_attribute( 'header_icon', 'class', 'premium-not-icon' );
				// }

				if ( 'yes' === $settings['header_draw_svg'] ) {

					if ( 'icon' === $header_icon_type ) {
						$this->add_render_attribute( 'header_icon', 'class', $settings['header_icon']['value'] );
					}

					$this->add_render_attribute(
						'header_icon',
						array(
							'class'            => 'premium-svg-drawer',
							'data-svg-reverse' => false,
							'data-svg-loop'    => $settings['header_lottie_loop'],
							'data-svg-sync'    => $settings['header_svg_sync'],
							'data-svg-frames'  => $settings['header_frames'],
							'data-svg-yoyo'    => $settings['header_svg_yoyo'],
						)
					);

				} else {
					$this->add_render_attribute( 'header_icon', 'class', 'premium-svg-nodraw' );
				}
			}
		}

		?>

		<div <?php echo wp_kses_post( $this->get_render_attribute_string( 'wrap' ) ); ?>>

			<div <?php echo wp_kses_post( $this->get_render_attribute_string( 'icon_wrap' ) ); ?>>
				<?php if ( 'image' === $icon_type ) : ?>

					<?php echo wp_kses_post( $image_html ); ?>

				<?php elseif ( 'animation' === $icon_type ) : ?>

					<div <?php echo wp_kses_post( $this->get_render_attribute_string( 'lottie_icon' ) ); ?>></div>

				<?php elseif ( 'icon' === $icon_type ) : ?>
					<?php
					if ( 'yes' !== $settings['draw_svg'] ) :
						Icons_Manager::render_icon(
							$settings['icon'],
							array(
								'class'       => array( 'premium-not-icon', 'premium-svg-nodraw', 'premium-drawable-icon', 'premium-notification-icon' ),
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
					<p class="premium-not-icon-text premium-notification-icon">
						<?php echo wp_kses_post( $settings['text'] ); ?>
					</p>

				<?php endif; ?>

				<?php if ( 'image' === $icon_type ) : ?>
					<?php echo wp_kses_post( $image_html_with_no_post ); ?>

				<?php else : ?>
					<?php
						Icons_Manager::render_icon(
							$settings['icon_with_no_posts'],
							array(
								'class'       => array( 'premium-not-icon', 'premium-svg-nodraw', 'premium-drawable-icon', 'premium-icon-with-no-post' ),
								'aria-hidden' => 'true',
							)
						);
					?>
				<?php endif; ?>

				<?php if ( $number > 0 ) : ?>
					<div <?php echo wp_kses_post( $this->get_render_attribute_string( 'number' ) ); ?>>
						<span><?php echo wp_kses_post( $number ); ?></span>
					</div>
				<?php endif; ?>

			</div>

		</div>

		<div <?php echo wp_kses_post( $this->get_render_attribute_string( 'posts_container' ) ); ?>>
			<div class="pa-rec-posts-header">

				<div <?php echo wp_kses_post( $this->get_render_attribute_string( 'title_wrap' ) ); ?>>

					<?php if ( 'yes' === $settings['header_icon_sw'] ) : ?>

						<div <?php echo wp_kses_post( $this->get_render_attribute_string( 'header_icon_wrap' ) ); ?>>
							<?php if ( 'image' === $header_icon_type ) : ?>

								<?php $img_src = wp_get_attachment_image_src( $settings['header_image']['id'], 'thumbnail' ); ?>

								<img src="<?php echo esc_url( $img_src[0] ); ?>" alt="<?php echo esc_attr( $settings['header_image']['alt'] ); ?>">
							<?php elseif ( 'animation' === $header_icon_type ) : ?>

								<div <?php echo wp_kses_post( $this->get_render_attribute_string( 'header_lottie_icon' ) ); ?>></div>

							<?php elseif ( 'icon' === $header_icon_type ) : ?>
								<?php
								if ( 'yes' !== $settings['header_draw_svg'] ) :
									Icons_Manager::render_icon(
										$settings['header_icon'],
										array(
											'class'       => array( 'premium-svg-nodraw', 'premium-drawable-icon' ),
											'aria-hidden' => 'true',
										)
									);
								else :
									?>
									<i <?php echo wp_kses_post( $this->get_render_attribute_string( 'header_icon' ) ); ?>></i>
								<?php endif; ?>

							<?php else : ?>
								<div <?php echo wp_kses_post( $this->get_render_attribute_string( 'header_icon' ) ); ?>>
									<?php $this->print_unescaped_setting( 'header_custom_svg' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
								</div>
							<?php endif; ?>

						</div>

					<?php endif; ?>

					<<?php echo wp_kses_post( $settings['header_size'] ); ?> class="pa-rec-title">
						<?php echo wp_kses_post( $settings['header_text'] ); ?>
					</<?php echo wp_kses_post( $settings['header_size'] ); ?>>


				</div>

				<div class="pa-rec-posts-close">
					<i class="pa-rec-posts-close-icon fa fa-close"></i>
				</div>
			</div>

			<div class="pa-rec-posts-body">
				<?php

				if ( 'yes' === $settings['cookies'] && 0 == $number && 'template' === $settings['shown_content'] ) {
					$template = empty( $settings['content_temp'] ) ? $settings['live_temp_content'] : $settings['content_temp'];
					echo $this->get_blog_helper()->get_template_content( $template ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
				} else {
					$id = $this->get_id();
					$blog_helper->render_posts();
				}

				?>
			</div>


		</div>

		<?php if ( 'yes' === $settings['overlay'] ) : ?>
			<div class="pa-rec-posts-overlay"></div>
		<?php endif; ?>

		<?php

	}

	/**
	 * Get Posts Number
	 *
	 * Used to get the number of posts to check.
	 *
	 * @access protected
	 *
	 * @return integer $number posts number.
	 */
	protected function get_posts_number() {

		$settings = $this->get_settings_for_display();

		$queried_posts_ids = array();

		if ( 'yes' !== $settings['cookies'] || is_user_logged_in() ) {
			// if ( 'yes' !== $settings['cookies'] ) {
			$number = ! empty( $settings['posts_number'] ) ? $settings['posts_number'] : 3;
		} else {

			$blog_helper = $this->get_blog_helper();

			$query = $blog_helper->get_query_posts();

			$posts = $query->posts;

			$this->query_posts = $posts;

			foreach ( $posts as $post ) {
				$queried_posts_ids[] = $post->ID;
			}

			$number = count( $posts ) ? count( $posts ) : 0;

			if ( count( $posts ) ) {

				// setcookie( 'username', 'john_doe', time() + 3600 );
				$queried_posts_ids = implode( ',', $queried_posts_ids );

				// global $post;

				// foreach ( $posts as $post ) {
				// setup_postdata( $post );
				// $this->get_post_layout();
				// }
			} else {
				$queried_posts_ids = '';
			}

			// wp_reset_postdata();

		}

		return array(
			'number' => $number,
			'posts'  => $queried_posts_ids,
		);

	}

}
