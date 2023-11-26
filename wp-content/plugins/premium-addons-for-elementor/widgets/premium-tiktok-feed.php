<?php
/**
 * Premium TikTok Feed.
 */

namespace PremiumAddons\Widgets;

// Elementor Classes.
use Elementor\Plugin;
use Elementor\Widget_Base;
use Elementor\Icons_Manager;
use Elementor\Control_Media;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Text_Shadow;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Background;
use Elementor\Group_Control_Css_Filter;
use Elementor\Core\Kits\Documents\Tabs\Global_Colors;
use Elementor\Core\Kits\Documents\Tabs\Global_Typography;

// PremiumAddons Classes.
use PremiumAddons\Includes\Helper_Functions;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // If this file is called directly, abort.
}

/**
 * Class Premium_Tiktok_Feed.
 */
class Premium_Tiktok_Feed extends Widget_Base {

	/**
	 * Retrieve Widget Name.
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function get_name() {
		return 'premium-tiktok-feed';
	}

	/**
	 * Retrieve Widget Title.
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function get_title() {
		return __( 'TikTok Feed', 'premium-addons-for-elementor' );
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
		return 'pa-tiktok';
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
			'elementor-common-css',
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

		return array(
			'tiktok-embed',
			'lottie-js',
			'imagesloaded',
			'isotope-js',
			'pa-slick',
			'premium-addons',
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
		return array( 'pa', 'premium', 'tiktok', 'feed', 'social', 'video' );
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
	 * Register Tiktok Feed controls.
	 *
	 * @since  1.0.0
	 * @access protected
	 */
	protected function register_controls() {

		$papro_activated = apply_filters( 'papro_activated', false );

		$this->add_login_controls();

		$this->add_query_controls();

		$this->add_general_controls();

		$this->add_feed_settings_controls();

		$this->add_video_settings_controls();

		$this->add_profile_controls();

		$this->add_carousel_section();

		$this->add_helpful_info_section();

		// style Controls.
		$this->add_video_style_controls();

		$this->add_info_style_controls();

		$this->add_feed_box_style_controls();

		$this->add_feed_lightbox_style_controls();

		if ( $papro_activated ) {
			do_action( 'pa_tiktok_profile_style', $this );
		}

		$this->add_carousel_style();

		if ( $papro_activated ) {
			do_action( 'pa_tiktok_loadmore_style', $this );
		}

		$this->add_share_btn_style();

		$this->add_share_links_style();

		$this->add_container_style_controls();
	}

	/** Login Controls. */
	private function add_login_controls() {

		$this->start_controls_section(
			'access_credentials_section',
			array(
				'label' => __( 'Access Credentials', 'premium-addons-for-elementor' ),
			)
		);

		$this->add_control(
			'tiktok_login',
			array(
				'type'        => Controls_Manager::RAW_HTML,
				'raw'         => '<form onsubmit="connectTiktokInit(this);" action="javascript:void(0);"><input type="submit" value="Log in with TikTok" class="elementor-button" style="background-color: #000; color: #fff;"></form>',
				'label_block' => true,
			)
		);

		$this->add_control(
			'access_token',
			array(
				'label'   => __( 'Access Token', 'premium-addons-for-elementor' ),
				'type'    => Controls_Manager::TEXTAREA,
				'dynamic' => array( 'active' => true ),
			)
		);

		$this->add_control(
			'reload',
			array(
				'label'   => __( 'Refresh Cached Data Once Every', 'premium-addons-for-elementor' ),
				'type'    => Controls_Manager::SELECT,
				'options' => array(
					'day'   => __( 'Day', 'premium-addons-for-elementor' ),
					'week'  => __( 'Week', 'premium-addons-for-elementor' ),
					'month' => __( 'Month', 'premium-addons-for-elementor' ),
					'year'  => __( 'Year', 'premium-addons-for-elementor' ),
				),
				'default' => 'week',
			)
		);

		$this->end_controls_section();
	}

	/** Content Controls. */
	private function add_query_controls() {

		$papro_activated = apply_filters( 'papro_activated', false );

		$this->start_controls_section(
			'pa_tiktok_query_sec',
			array(
				'label' => __( 'Query', 'premium-addons-for-elementor' ),
			)
		);

		$this->add_control(
			'scheme',
			array(
				'label'        => __( 'Scheme', 'premium-addons-for-elementor' ),
				'type'         => Controls_Manager::SELECT,
				'prefix_class' => 'premium-tiktok-feed__scheme-',
				'options'      => array(
					'light' => __( 'Light', 'premium-addons-for-elementor' ),
					'dark'  => __( 'Dark', 'premium-addons-for-elementor' ),
				),
				'default'      => 'light',
			)
		);

		$this->add_control(
			'show_feed',
			array(
				'label'     => __( 'Videos', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::SWITCHER,
				'label_on'  => __( 'Show', 'premium-addons-for-elementor' ),
				'label_off' => __( 'Hide', 'premium-addons-for-elementor' ),
				'default'   => 'yes',
			)
		);

		$this->add_control(
			'sort',
			array(
				'label'       => __( 'Order By Date', 'premium-addons-for-elementor' ),
				'type'        => Controls_Manager::SELECT,
				'label_block' => true,
				'options'     => array(
					'default' => __( 'Descending', 'premium-addons-for-elementor' ),
					'reverse' => __( 'Ascending', 'premium-addons-for-elementor' ),
				),
				'default'     => 'default',
				'render_type' => 'template',
				'condition'   => array(
					'match_id'  => '',
					'show_feed' => 'yes',
				),
			)
		);

		$this->add_control(
			'match_id',
			array(
				'label'       => apply_filters( 'pa_pro_label', __( 'Filter By ID (PRO)', 'premium-addons-for-elementor' ) ),
				'type'        => Controls_Manager::TEXT,
				'label_block' => true,
				'description' => 'Enter the video ID(s) you want to display separated by ",", leave it empty to display all the available items.',
				'dynamic'     => array( 'active' => true ),
				'render_type' => 'template',
				'condition'   => array(
					'show_feed' => 'yes',
				),
			)
		);

		$this->add_control(
			'exclude_id',
			array(
				'label'       => apply_filters( 'pa_pro_label', __( 'Exclude IDs (PRO)', 'premium-addons-for-elementor' ) ),
				'type'        => Controls_Manager::TEXT,
				'label_block' => true,
				'description' => 'Enter the video IDs you want to ecxclude separated by ","',
				'dynamic'     => array( 'active' => true ),
				'render_type' => 'template',
				'condition'   => array(
					'match_id'  => '',
					'show_feed' => 'yes',
				),
			)
		);

		if ( ! $papro_activated ) {
			$get_pro = Helper_Functions::get_campaign_link( 'https://premiumaddons.com/pro', 'editor-page', 'wp-editor', 'get-pro' );

			$this->add_control(
				'query_notice',
				array(
					'type'            => Controls_Manager::RAW_HTML,
					'raw'             => __( 'This option is available in Premium Addons Pro. ', 'premium-addons-for-elementor' ) . '<a href="' . esc_url( $get_pro ) . '" target="_blank">' . __( 'Upgrade now!', 'premium-addons-for-elementor' ) . '</a>',
					'content_classes' => 'papro-upgrade-notice',
					'conditions'      => array(
						'relation' => 'or',
						'terms'    => array(
							array(
								'name'     => 'match_id',
								'operator' => '!==',
								'value'    => '',
							),
							array(
								'name'     => 'exclude_id',
								'operator' => '!==',
								'value'    => '',
							),
						),
					),
				)
			);

		}

		$this->end_controls_section();
	}

	private function add_profile_controls() {

		$papro_activated = apply_filters( 'papro_activated', false );

		$this->start_controls_section(
			'pa_tiktok_profile_sec',
			array(
				'label' => __( 'Profile Header', 'premium-addons-for-elementor' ),
			)
		);

		$this->add_control(
			'profile_header',
			array(
				'label'     => __( 'Profile Header', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::SWITCHER,
				'label_on'  => __( 'Show', 'premium-addons-for-elementor' ),
				'label_off' => __( 'Hide', 'premium-addons-for-elementor' ),
			)
		);

		if ( ! $papro_activated ) {
			$get_pro = Helper_Functions::get_campaign_link( 'https://premiumaddons.com/pro', 'editor-page', 'wp-editor', 'get-pro' );

			$this->add_control(
				'profile_header_notice',
				array(
					'type'            => Controls_Manager::RAW_HTML,
					'raw'             => __( 'This option is available in Premium Addons Pro. ', 'premium-addons-for-elementor' ) . '<a href="' . esc_url( $get_pro ) . '" target="_blank">' . __( 'Upgrade now!', 'premium-addons-for-elementor' ) . '</a>',
					'content_classes' => 'papro-upgrade-notice',
					'condition'       => array(
						'profile_header' => 'yes',
					),
				)
			);

		} else {
			do_action( 'pa_tiktok_profile_controls', $this );
		}

		$this->end_controls_section();
	}

	private function add_feed_settings_controls() {

		$this->start_controls_section(
			'pa_tiktok_vid_sec',
			array(
				'label'     => __( 'Feed Settings', 'premium-addons-for-elementor' ),
				'condition' => array(
					'show_feed' => 'yes',
				),
			)
		);

		$options = apply_filters(
			'pa_tiktok_options',
			array(
				'layouts' => array(
					'layout-1' => __( 'Card', 'premium-addons-for-elementor' ),
					'layout-2' => __( 'Banner (Pro)', 'premium-addons-for-elementor' ),
					'layout-3' => __( 'On Side (Pro)', 'premium-addons-for-elementor' ),
				),
			)
		);

		$this->add_control(
			'vid_layout',
			array(
				'label'        => __( 'Skin', 'premium-addons-for-elementor' ),
				'type'         => Controls_Manager::SELECT,
				'prefix_class' => 'premium-tiktok-feed__vid-',
				'render_type'  => 'template',
				'default'      => 'layout-1',
				'options'      => $options['layouts'],
			)
		);

		$this->add_responsive_control(
			'info_order',
			array(
				'label'     => __( 'Info Order', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::CHOOSE,
				'toggle'    => false,
				'options'   => array(
					'0' => array(
						'title' => __( 'Before Video', 'premium-addons-for-elementor' ),
						'icon'  => 'eicon-order-start',
					),
					'2' => array(
						'title' => __( 'After Video', 'premium-addons-for-elementor' ),
						'icon'  => 'eicon-order-end',
					),
				),
				'default'   => '2',
				'selectors' => array(
					'{{WRAPPER}}.premium-tiktok-feed__vid-layout-1 .premium-tiktok-feed__vid-desc,
                    {{WRAPPER}}.premium-tiktok-feed__vid-layout-3 .premium-tiktok-feed__vid-meta-wrapper' => 'order: {{VALUE}}',
				),
				'condition' => array(
					'vid_layout!' => 'layout-2',
				),
			)
		);

		$this->add_control(
			'vid_settings_heading',
			array(
				'label'     => esc_html__( 'Display Options', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			)
		);

		$this->add_control(
			'vid_tiktok_icon',
			array(
				'label'     => __( 'TikTok Icon', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::SWITCHER,
				'label_on'  => __( 'Show', 'premium-addons-for-elementor' ),
				'label_off' => __( 'Hide', 'premium-addons-for-elementor' ),
				'default'   => 'yes',
			)
		);

		$this->add_responsive_control(
			'tiktok_icon_size',
			array(
				'label'      => __( 'Icon Size', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', 'em' ),
				'separator'  => 'before',
				'selectors'  => array(
					'{{WRAPPER}} .premium-tiktok-feed__vid-meta-wrapper .premium-tiktok-feed__tiktok-icon svg'  => 'width: {{SIZE}}px; height: {{SIZE}}px;',
				),
				'condition'  => array(
					'vid_tiktok_icon' => 'yes',
				),
			)
		);

		$this->add_control(
			'vid_username',
			array(
				'label'     => __( 'Username', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::SWITCHER,
				'label_on'  => __( 'Show', 'premium-addons-for-elementor' ),
				'label_off' => __( 'Hide', 'premium-addons-for-elementor' ),
				'default'   => 'yes',
			)
		);

		$this->add_control(
			'vid_desc',
			array(
				'label'     => __( 'Description', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::SWITCHER,
				'label_on'  => __( 'Show', 'premium-addons-for-elementor' ),
				'label_off' => __( 'Hide', 'premium-addons-for-elementor' ),
				'default'   => 'yes',
			)
		);

		$this->add_control(
			'vid_desc_len',
			array(
				'label'     => __( 'Description Length (Word)', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::NUMBER,
				'default'   => 8,
				'condition' => array(
					'vid_desc' => 'yes',
				),
			)
		);

		$this->add_control(
			'vid_desc_postfix',
			array(
				'label'       => __( 'Postfix', 'premium-addons-for-elementor' ),
				'type'        => Controls_Manager::SELECT,
				'options'     => array(
					'dots' => __( 'Dots', 'premium-addons-for-elementor' ),
					'link' => __( 'Link', 'premium-addons-for-elementor' ),
				),
				'default'     => 'dots',
				'label_block' => true,
				'condition'   => array(
					'vid_desc'      => 'yes',
					'vid_desc_len!' => '',
				),
			)
		);

		$this->add_control(
			'vid_desc_postfix_txt',
			array(
				'label'     => __( 'Read More Text', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::TEXT,
				'default'   => __( 'Read More »', 'premium-addons-for-elementor' ),
				'condition' => array(
					'vid_desc'         => 'yes',
					'vid_desc_len!'    => '',
					'vid_desc_postfix' => 'link',
				),
			)
		);

		$this->add_control(
			'create_time',
			array(
				'label'     => __( 'Date', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::SWITCHER,
				'default'   => 'yes',
				'label_on'  => __( 'Show', 'premium-addons-for-elementor' ),
				'label_off' => __( 'Hide', 'premium-addons-for-elementor' ),
			)
		);

		$this->add_control(
			'date_format',
			array(
				'label'       => __( 'Date Format', 'premium-addons-for-elementor' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => 'yes',
				'label_block' => true,
				'description' => __( 'Know more abour date format from ', 'premium-addons-for-elementor' ) . '<a href="https://wordpress.org/documentation/article/customize-date-and-time-format/" target="_blank">here</a>',
				'default'     => 'F j, Y',
				'condition'   => array(
					'create_time' => 'yes',
				),
			)
		);

		$this->add_control(
			'like_count',
			array(
				'label'     => __( 'Likes Count', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::SWITCHER,
				'default'   => 'yes',
				'label_on'  => __( 'Show', 'premium-addons-for-elementor' ),
				'label_off' => __( 'Hide', 'premium-addons-for-elementor' ),
			)
		);

		$this->add_control(
			'comment_count',
			array(
				'label'     => __( 'Comments Count', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::SWITCHER,
				'default'   => 'yes',
				'label_on'  => __( 'Show', 'premium-addons-for-elementor' ),
				'label_off' => __( 'Hide', 'premium-addons-for-elementor' ),
			)
		);

		$this->add_control(
			'view_count',
			array(
				'label'     => __( 'View Count', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::SWITCHER,
				'default'   => 'yes',
				'label_on'  => __( 'Show', 'premium-addons-for-elementor' ),
				'label_off' => __( 'Hide', 'premium-addons-for-elementor' ),
			)
		);

		$this->add_control(
			'share_count',
			array(
				'label'     => __( 'Share Button', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::SWITCHER,
				'separator' => 'before',
				'default'   => 'yes',
				'label_on'  => __( 'Show', 'premium-addons-for-elementor' ),
				'label_off' => __( 'Hide', 'premium-addons-for-elementor' ),
			)
		);

		$this->add_control(
			'counters_alignment',
			array(
				'label'     => __( 'Counters Alignment', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::CHOOSE,
				'options'   => array(
					'flex-start' => array(
						'title' => __( 'Start', 'premium-addons-for-elementor' ),
						'icon'  => 'eicon-order-start',
					),
					'center'     => array(
						'title' => __( 'Center', 'premium-addons-for-elementor' ),
						'icon'  => 'eicon-h-align-center',
					),
					'flex-end'   => array(
						'title' => __( 'End', 'premium-addons-for-elementor' ),
						'icon'  => 'eicon-order-end',
					),
				),
				'selectors' => array(
					'{{WRAPPER}} .premium-tiktok-feed__video-counts' => 'justify-content: {{VALUE}};',
					'{{WRAPPER}} .premium-tiktok-feed__shares' => 'margin-left: unset !important;',
				),
				'condition' => array(
					'vid_layout' => 'layout-3',
				),
			)
		);

		$this->end_controls_section();
	}

	private function add_video_settings_controls() {

		$this->start_controls_section(
			'videos_settings_section',
			array(
				'label'     => __( 'Video Settings', 'premium-addons-for-elementor' ),
				'condition' => array(
					'show_feed' => 'yes',
				),
			)
		);

		$this->add_responsive_control(
			'pa_pin_img_height',
			array(
				'label'      => __( 'Video Height', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', 'custom' ),
				'range'      => array(
					'px' => array(
						'min' => 0,
						'max' => 1000,
					),
				),
				'selectors'  => array(
					'{{WRAPPER}} .premium-tiktok-feed__video-media'  => 'height: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'object_fit',
			array(
				'label'     => __( 'Object Fit', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::SELECT,
				'options'   => array(
					''        => __( 'Default', 'premium-addons-for-elementor' ),
					'fill'    => __( 'Fill', 'premium-addons-for-elementor' ),
					'cover'   => __( 'Cover', 'premium-addons-for-elementor' ),
					'contain' => __( 'Contain', 'premium-addons-for-elementor' ),
				),
				'default'   => 'cover',
				'selectors' => array(
					'{{WRAPPER}} .premium-tiktok-feed__video-media video' => 'object-fit: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'autoplay_all',
			array(
				'label' => __( 'Autoplay All Videos', 'premium-addons-for-elementor' ),
				'type'  => Controls_Manager::SWITCHER,
			)
		);

		$this->add_control(
			'autoplay_first',
			array(
				'label'     => apply_filters( 'pa_pro_label', __( 'Autoplay First Video (PRO)', 'premium-addons-for-elementor' ) ),
				'type'      => Controls_Manager::SWITCHER,
				'condition' => array(
					'autoplay_all!' => 'yes',
				),
			)
		);

		$this->add_control(
			'autoplay_hover',
			array(
				'label'     => apply_filters( 'pa_pro_label', __( 'Autoplay On Hover (PRO)', 'premium-addons-for-elementor' ) ),
				'type'      => Controls_Manager::SWITCHER,
				'condition' => array(
					'autoplay_all!' => 'yes',
					'onclick!'      => 'play',
				),
			)
		);

		$this->add_control(
			'mute_videos',
			array(
				'label' => __( 'Mute Videos', 'premium-addons-for-elementor' ),
				'type'  => Controls_Manager::SWITCHER,
			)
		);

		$this->add_control(
			'loop_videos',
			array(
				'label' => __( 'Loop Videos', 'premium-addons-for-elementor' ),
				'type'  => Controls_Manager::SWITCHER,
			)
		);

		$this->add_control(
			'onclick',
			array(
				'label'     => __( 'On Click', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::SELECT,
				'options'   => array(
					'default'  => __( 'Redirect To TikTok', 'premium-addons-for-elementor' ),
					'play'     => __( 'Play Video', 'premium-addons-for-elementor' ),
					'lightbox' => __( 'Lightbox', 'premium-addons-for-elementor' ),
				),
				'default'   => 'play',
				'separator' => 'before',
			)
		);

		$this->add_control(
			'vid_play_icon',
			array(
				'label'     => __( 'Play Icon', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::SWITCHER,
				'separator' => 'before',
				'label_on'  => __( 'Show', 'premium-addons-for-elementor' ),
				'label_off' => __( 'Hide', 'premium-addons-for-elementor' ),
				'default'   => 'yes',
				'separator' => 'before',
				'condition' => array(
					'autoplay_all!' => 'yes',
				),
			)
		);

		$this->add_control(
			'play_icon_color',
			array(
				'label'     => __( 'Color', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .premium-tiktok-feed__video-media .premium-tiktok-feed__play-icon i'  => 'color: {{VALUE}};',
				),
				'condition' => array(
					'vid_play_icon' => 'yes',
					'autoplay_all!' => 'yes',
				),
			)
		);

		$this->add_responsive_control(
			'play_icon_size',
			array(
				'label'      => __( 'Icon Size', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', 'em' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-tiktok-feed__video-media .premium-tiktok-feed__play-icon i'  => 'font-size: {{SIZE}}px;',
				),
				'condition'  => array(
					'vid_play_icon' => 'yes',
					'autoplay_all!' => 'yes',
				),
			)
		);

		$this->add_responsive_control(
			'play_icon_hor',
			array(
				'label'      => __( 'Horizontal Position', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-tiktok-feed__video-media .premium-tiktok-feed__play-icon'  => 'right: {{SIZE}}px;',
				),
				'condition'  => array(
					'vid_play_icon' => 'yes',
					'autoplay_all!' => 'yes',
				),
			)
		);

		$this->add_responsive_control(
			'play_icon_ver',
			array(
				'label'      => __( 'Vertical Position', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-tiktok-feed__video-media .premium-tiktok-feed__play-icon'  => 'top: {{SIZE}}px;',
				),
				'condition'  => array(
					'vid_play_icon' => 'yes',
					'autoplay_all!' => 'yes',
				),
			)
		);

		$this->end_controls_section();
	}

	private function add_general_controls() {

		$papro_activated = apply_filters( 'papro_activated', false );

		$this->start_controls_section(
			'pa_gen_section',
			array(
				'label'     => __( 'General Settings', 'premium-addons-for-elementor' ),
				'condition' => array(
					'show_feed' => 'yes',
				),
			)
		);

		$this->add_control(
			'outer_layout',
			array(
				'label'        => __( 'Layout', 'premium-addons-for-elementor' ),
				'type'         => Controls_Manager::SELECT,
				'prefix_class' => 'premium-tiktok-feed__',
				'render_type'  => 'template',
				'label_block'  => true,
				'options'      => array(
					'grid'    => __( 'Grid', 'premium-addons-for-elementor' ),
					'masonry' => __( 'Masonry', 'premium-addons-for-elementor' ),
				),
				'default'      => 'grid',
			)
		);

		$this->add_responsive_control(
			'pa_tiktok_cols',
			array(
				'label'          => __( 'Number of Columns', 'premium-addons-for-elementor' ),
				'type'           => Controls_Manager::SELECT,
				'options'        => array(
					'1' => __( '1 Column', 'premium-addons-for-elementor' ),
					'2' => __( '2 Columns', 'premium-addons-for-elementor' ),
					'3' => __( '3 Columns', 'premium-addons-for-elementor' ),
					'4' => __( '4 Columns', 'premium-addons-for-elementor' ),
					'5' => __( '5 Columns', 'premium-addons-for-elementor' ),
					'6' => __( '6 Columns', 'premium-addons-for-elementor' ),
				),
				'default'        => '3',
				'tablet_default' => '2',
				'mobile_default' => '1',
				'render_type'    => 'template',
				'label_block'    => true,
				'selectors'      => array(
					'{{WRAPPER}} .premium-tiktok-feed__video-outer-wrapper'  => 'width: calc( 100% / {{VALUE}} );',
				),
			)
		);

		$this->add_responsive_control(
			'no_of_posts',
			array(
				'label'       => __( 'Videos Per Page', 'premium-addons-for-elementor' ),
				'description' => __( 'Set the number of Videos per page', 'premium-addons-for-elementor' ),
				'type'        => Controls_Manager::NUMBER,
				'min'         => 1,
				'default'     => 4,
				'condition'   => array(
					'match_id'  => '',
					'show_feed' => 'yes',
				),
			)
		);

		$this->add_control(
			'loading_animation',
			array(
				'label'        => __( 'Loading Animation', 'premium-addons-for-elementor' ),
				'type'         => Controls_Manager::SELECT,
				'prefix_class' => 'premium-loading-animation__slide-',
				'options'      => array(
					'none'  => __( 'None', 'premium-addons-for-elementor' ),
					'up'    => __( 'Slide Up', 'premium-addons-for-elementor' ),
					'down'  => __( 'Slide Down', 'premium-addons-for-elementor' ),
					'left'  => __( 'Slide Left', 'premium-addons-for-elementor' ),
					'right' => __( 'Slide Right', 'premium-addons-for-elementor' ),
				),
				'default'      => 'up',
				'condition'    => array(
					'load_more_btn' => 'yes',
					'carousel!'     => 'yes',
				),
			)
		);

		$this->add_responsive_control(
			'pa_tiktok_spacing',
			array(
				'label'      => __( 'Spacing', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'separator'  => 'before',
				'selectors'  => array(
					'{{WRAPPER}} .premium-tiktok-feed__video-outer-wrapper'  => 'padding: {{SIZE}}px;',
				),
			)
		);

		$this->add_control(
			'load_more_btn',
			array(
				'label'       => __( 'Load More Button', 'premium-addons-for-elementor' ),
				'type'        => Controls_Manager::SWITCHER,
				'render_type' => 'template',
				'condition'   => array(
					'carousel!' => 'yes',
				),
			)
		);

		if ( $papro_activated ) {
			do_action( 'pa_tiktok_load_more_options', $this );
		}

		$this->end_controls_section();
	}

	private function add_carousel_section() {

		$this->start_controls_section(
			'pa_tiktok_carousel_settings',
			array(
				'label'     => __( 'Carousel', 'premium-addons-for-elementor' ),
				'condition' => array(
					'show_feed' => 'yes',
				),
			)
		);

		$this->add_control(
			'carousel',
			array(
				'label'        => __( 'Enable Carousel', 'premium-addons-for-elementor' ),
				'type'         => Controls_Manager::SWITCHER,
				'prefix_class' => 'premium-carousel-',
				'render_type'  => 'template',
			)
		);

		$this->add_control(
			'fade',
			array(
				'label'     => __( 'Fade', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::SWITCHER,
				'condition' => array(
					'carousel'       => 'yes',
					'pa_tiktok_cols' => '1',
				),
			)
		);

		$this->add_control(
			'auto_play',
			array(
				'label'     => __( 'Auto Play', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::SWITCHER,
				'condition' => array(
					'carousel' => 'yes',
				),
			)
		);

		$this->add_control(
			'autoplay_speed',
			array(
				'label'       => __( 'Autoplay Speed', 'premium-addons-for-elementor' ),
				'description' => __( 'Autoplay Speed means at which time the next slide should come. Set a value in milliseconds (ms)', 'premium-addons-for-elementor' ),
				'type'        => Controls_Manager::NUMBER,
				'default'     => 5000,
				'condition'   => array(
					'carousel'  => 'yes',
					'auto_play' => 'yes',
				),
			)
		);

		$this->add_control(
			'slides_to_scroll',
			array(
				'label'     => __( 'Slides To Scroll', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::NUMBER,
				'condition' => array(
					'carousel' => 'yes',
				),
			)
		);

		$this->add_control(
			'carousel_speed',
			array(
				'label'              => __( 'Transition Speed (ms)', 'premium-addons-for-elementor' ),
				'description'        => __( 'Set the speed of the carousel animation in milliseconds (ms)', 'premium-addons-for-elementor' ),
				'type'               => Controls_Manager::NUMBER,
				'default'            => 300,
				'render_type'        => 'template',
				'selectors'          => array(
					'{{WRAPPER}} .premium-tiktok-feed__videos-wrapper .slick-slide' => 'transition: all {{VALUE}}ms !important',
				),
				'condition'          => array(
					'carousel' => 'yes',
				),
				'frontend_available' => true,
			)
		);

		$this->add_control(
			'carousel_center',
			array(
				'label'     => __( 'Center Mode', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::SWITCHER,
				'condition' => array(
					'carousel' => 'yes',
				),
			)
		);

		$this->add_control(
			'carousel_spacing',
			array(
				'label'       => __( 'Slides\' Spacing', 'premium-addons-for-elementor' ),
				'description' => __( 'Set a spacing value in pixels (px)', 'premium-addons-for-elementor' ),
				'type'        => Controls_Manager::NUMBER,
				'default'     => '15',
				'condition'   => array(
					'carousel' => 'yes',
				),
			)
		);

		$this->add_control(
			'carousel_dots',
			array(
				'label'     => __( 'Navigation Dots', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::SWITCHER,
				'condition' => array(
					'carousel' => 'yes',
				),
			)
		);

		$this->add_control(
			'carousel_arrows',
			array(
				'label'     => __( 'Navigation Arrows', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::SWITCHER,
				'default'   => 'yes',
				'condition' => array(
					'carousel' => 'yes',
				),
			)
		);

		$this->add_responsive_control(
			'carousel_arrows_pos',
			array(
				'label'      => __( 'Arrows Position', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', 'em' ),
				'range'      => array(
					'px' => array(
						'min' => -100,
						'max' => 100,
					),
					'em' => array(
						'min' => -10,
						'max' => 10,
					),
				),
				'condition'  => array(
					'carousel'        => 'yes',
					'carousel_arrows' => 'yes',
				),
				'selectors'  => array(
					'{{WRAPPER}} .premium-tiktok-feed__videos-wrapper a.carousel-arrow.carousel-next' => 'right: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .premium-tiktok-feed__videos-wrapper a.carousel-arrow.carousel-prev' => 'left: {{SIZE}}{{UNIT}};',
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
			'https://premiumaddons.com/docs/elementor-tiktok-feed-widget/' => __( 'Getting started »', 'premium-addons-for-elementor' ),
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

	/** Style Controls. */
	private function add_feed_box_style_controls() {

		$this->start_controls_section(
			'pa_feedbox_style_sec',
			array(
				'label'     => __( 'Feed Box', 'premium-addons-for-elementor' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => array(
					'show_feed' => 'yes',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name'     => 'feed_box_shadow',
				'selector' => '{{WRAPPER}} .premium-tiktok-feed__video-wrapper',
			)
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			array(
				'name'     => 'feed_box_background',
				'types'    => array( 'classic', 'gradient' ),
				'selector' => '{{WRAPPER}} .premium-tiktok-feed__video-wrapper',
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'     => 'feed_box_border',
				'selector' => '{{WRAPPER}} .premium-tiktok-feed__video-wrapper',
			)
		);

		$this->add_control(
			'feed_box_border_rad',
			array(
				'label'      => __( 'Border Radius', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', '%', 'em' ),
				'default'    => array(
					'size' => 15,
					'unit' => 'px',
				),
				'selectors'  => array(
					'{{WRAPPER}} .premium-tiktok-feed__video-wrapper' => 'border-radius: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'feed_box_padding',
			array(
				'label'      => __( 'Padding', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-tiktok-feed__video-wrapper' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_section();
	}

	private function add_feed_lightbox_style_controls() {

		$this->start_controls_section(
			'pa_lightbox_style_sec',
			array(
				'label'     => __( 'Lightbox', 'premium-addons-for-elementor' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => array(
					'show_feed' => 'yes',
					'onclick'   => 'lightbox',
				),
			)
		);

		$this->add_control(
			'lightbox_color',
			array(
				'label'     => __( 'Background Color', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .premium-tiktok-feed-modal-iframe-modal' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'close_icon_color',
			array(
				'label'     => __( 'Close Icon Color', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .premium-tiktok-temp-close' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'close_icon_color_hov',
			array(
				'label'     => __( 'Close Icon Hover Color', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .premium-tiktok-temp-close:hover' => 'color: {{VALUE}};',
				),
			)
		);

		$this->end_controls_section();
	}


	private function add_container_style_controls() {

		$this->start_controls_section(
			'pa_feed_cont_style_sec',
			array(
				'label'     => __( 'Feed Container', 'premium-addons-for-elementor' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => array(
					'show_feed' => 'yes',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name'     => 'feed_cont_shadow',
				'selector' => '{{WRAPPER}} .premium-tiktok-feed__videos-wrapper',
			)
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			array(
				'name'     => 'feed_cont_background',
				'types'    => array( 'classic', 'gradient' ),
				'selector' => '{{WRAPPER}} .premium-tiktok-feed__videos-wrapper',
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'     => 'feed_cont_border',
				'selector' => '{{WRAPPER}} .premium-tiktok-feed__videos-wrapper',
			)
		);

		$this->add_control(
			'feed_cont_border_rad',
			array(
				'label'      => __( 'Border Radius', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', '%', 'em' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-tiktok-feed__videos-wrapper' => 'border-radius: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'feed_cont_padding',
			array(
				'label'      => __( 'Padding', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-tiktok-feed__videos-wrapper' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'feed_cont_margin',
			array(
				'label'      => __( 'Margin', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-tiktok-feed__videos-wrapper' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_section();
	}

	private function add_video_style_controls() {

		$this->start_controls_section(
			'pa_tiktok_video_style_section',
			array(
				'label'     => __( 'Video Style', 'premium-addons-for-elementor' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => array(
					'show_feed' => 'yes',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			array(
				'name'      => 'feed_box_overlay',
				'label'     => __( 'Overlay' ),
				'types'     => array( 'classic', 'gradient' ),
				'selector'  => '{{WRAPPER}} .premium-tiktok-feed__vid-meta-wrapper',
				'condition' => array(
					'vid_layout' => 'layout-2',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Css_Filter::get_type(),
			array(
				'name'     => 'css_filters',
				'selector' => '{{WRAPPER}} .premium-tiktok-feed__video-media video',
			)
		);

		$this->add_group_control(
			Group_Control_Css_Filter::get_type(),
			array(
				'name'     => 'hover_css_filters',
				'label'    => __( 'Hover CSS Filters', 'premium-addons-for-elementor' ),
				'selector' => '{{WRAPPER}} .premium-tiktok-feed__video-wrapper:hover video',
			)
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name'      => 'feed_img_shadow',
				'separator' => 'before',
				'selector'  => '{{WRAPPER}} .premium-tiktok-feed__video-media',
				'condition' => array(
					'vid_layout!' => 'layout-2',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'      => 'feed_img_border',
				'selector'  => '{{WRAPPER}} .premium-tiktok-feed__video-media',
				'condition' => array(
					'vid_layout!' => 'layout-2',
				),
			)
		);

		$this->add_control(
			'feed_img_radius',
			array(
				'label'      => __( 'Border Radius', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-tiktok-feed__video-media' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}',
				),
				'condition'  => array(
					'vid_layout!' => 'layout-2',
				),
			)
		);

		$this->end_controls_section();
	}

	private function add_info_style_controls() {

		$counters_cond = array(
			'relation' => 'or',
			'terms'    => array(
				array(
					'name'  => 'like_count',
					'value' => 'yes',
				),
				array(
					'name'  => 'comment_count',
					'value' => 'yes',
				),
				array(
					'name'  => 'share_count',
					'value' => 'yes',
				),
				array(
					'name'  => 'view_count',
					'value' => 'yes',
				),
			),
		);

		$this->start_controls_section(
			'pa_tiktok_info_style_section',
			array(
				'label'     => __( 'Feed Info', 'premium-addons-for-elementor' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => array(
					'show_feed' => 'yes',
				),
			)
		);

		$this->add_control(
			'username_heading',
			array(
				'label'     => esc_html__( 'Username', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::HEADING,
				'condition' => array(
					'vid_username' => 'yes',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'      => 'vid_username_typo',
				'selector'  => '{{WRAPPER}} .premium-tiktok-feed__vid-creator a',
				'condition' => array(
					'vid_username' => 'yes',
				),
			)
		);

		$this->add_control(
			'vid_username_color',
			array(
				'label'     => __( 'Color', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .premium-tiktok-feed__vid-creator a' => 'color: {{VALUE}};',
				),
				'condition' => array(
					'vid_username' => 'yes',
				),
			)
		);

		$this->add_responsive_control(
			'vid_username_margin',
			array(
				'label'      => __( 'Margin', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-tiktok-feed__vid-creator a' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
				'condition'  => array(
					'vid_username' => 'yes',
				),
			)
		);

		$this->add_control(
			'date_heading',
			array(
				'label'     => esc_html__( 'Date', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
				'condition' => array(
					'create_time' => 'yes',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'      => 'date_typo',
				'selector'  => '{{WRAPPER}} .premium-tiktok-feed__created-at',
				'condition' => array(
					'create_time' => 'yes',
				),
			)
		);

		$this->add_control(
			'date_color',
			array(
				'label'     => __( 'Color', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .premium-tiktok-feed__created-at' => 'color: {{VALUE}};',
				),
				'condition' => array(
					'create_time' => 'yes',
				),
			)
		);

		$this->add_responsive_control(
			'date_username_padding',
			array(
				'label'      => __( 'Username/Date Margin', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em' ),
				'separator'  => 'before',
				'selectors'  => array(
					'{{WRAPPER}} .premium-tiktok-feed__vid-meta-wrapper' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
				'default'    => array(
					'top'    => 15,
					'right'  => 15,
					'bottom' => 15,
					'left'   => 15,
					'unit'   => 'px',
				),
				'conditions' => array(
					'relation' => 'or',
					'terms'    => array(
						array(
							'name'  => 'vid_username',
							'value' => 'yes',
						),
						array(
							'name'  => 'create_time',
							'value' => 'yes',
						),
					),
				),
			)
		);

		$this->add_control(
			'desc_heading',
			array(
				'label'     => esc_html__( 'Description', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
				'condition' => array(
					'vid_desc' => 'yes',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'      => 'vid_desc_typo',
				'selector'  => '{{WRAPPER}} .premium-tiktok-feed__vid-desc',
				'condition' => array(
					'vid_desc' => 'yes',
				),
			)
		);

		$this->add_control(
			'vid_desc_color',
			array(
				'label'     => __( 'Color', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .premium-tiktok-feed__vid-desc' => 'color: {{VALUE}};',
				),
				'condition' => array(
					'vid_desc' => 'yes',
				),
			)
		);

		$this->add_responsive_control(
			'vid_desc_margin',
			array(
				'label'      => __( 'Margin', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em' ),
				'default'    => array(
					'top'    => 15,
					'right'  => 15,
					'bottom' => 15,
					'left'   => 15,
					'unit'   => 'px',
				),
				'selectors'  => array(
					'{{WRAPPER}} .premium-tiktok-feed__vid-desc' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
				'condition'  => array(
					'vid_desc' => 'yes',
				),
			)
		);

		$this->add_control(
			'read_more_heading',
			array(
				'label'     => esc_html__( 'Read More', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
				'condition' => array(
					'vid_desc'         => 'yes',
					'vid_desc_len!'    => '',
					'vid_desc_postfix' => 'link',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'      => 'read_more_typo',
				'selector'  => '{{WRAPPER}} .premium-read-more',
				'condition' => array(
					'vid_desc'         => 'yes',
					'vid_desc_len!'    => '',
					'vid_desc_postfix' => 'link',
				),
			)
		);

		$this->add_control(
			'read_more_color',
			array(
				'label'     => __( 'Color', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .premium-read-more' => 'color: {{VALUE}};',
				),
				'condition' => array(
					'vid_desc'         => 'yes',
					'vid_desc_len!'    => '',
					'vid_desc_postfix' => 'link',
				),
			)
		);

		$this->add_control(
			'read_more_color_hov',
			array(
				'label'     => __( 'Hover Color', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .premium-read-more:hover' => 'color: {{VALUE}};',
				),
				'condition' => array(
					'vid_desc'         => 'yes',
					'vid_desc_len!'    => '',
					'vid_desc_postfix' => 'link',
				),
			)
		);

		$this->add_responsive_control(
			'read_more_margin',
			array(
				'label'      => __( 'Margin', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-read-more' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
				'condition'  => array(
					'vid_desc'         => 'yes',
					'vid_desc_len!'    => '',
					'vid_desc_postfix' => 'link',
				),
			)
		);

		$this->add_control(
			'tiktok_icon_heading',
			array(
				'label'     => __( 'Tiktok Icon', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
				'condition' => array(
					'vid_tiktok_icon' => 'yes',
				),
			)
		);

		$this->add_control(
			'tiktok_icon_color',
			array(
				'label'     => __( 'Color', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .premium-tiktok-icon-video svg circle'  => 'fill: {{VALUE}};',
				),
				'condition' => array(
					'vid_tiktok_icon' => 'yes',
				),
			)
		);

		$this->add_control(
			'tiktok_icon_back',
			array(
				'label'     => __( 'Background Color', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .premium-tiktok-icon-video'  => 'background-color: {{VALUE}};',
				),
				'condition' => array(
					'vid_tiktok_icon' => 'yes',
				),
			)
		);

		$this->add_control(
			'tiktok_icon_radius',
			array(
				'label'      => __( 'Border Radius', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-tiktok-icon-video' => 'border-radius: {{SIZE}}{{UNIT}};',
				),
				'condition'  => array(
					'vid_tiktok_icon' => 'yes',
				),
			)
		);

		$this->add_responsive_control(
			'tiktok_icon_padding',
			array(
				'label'      => __( 'Padding', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-tiktok-icon-video' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
				'condition'  => array(
					'vid_tiktok_icon' => 'yes',
				),
			)
		);

		$this->add_responsive_control(
			'tiktok_icon_margin',
			array(
				'label'      => __( 'Margin', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-tiktok-icon-video' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
				'condition'  => array(
					'vid_tiktok_icon' => 'yes',
				),
			)
		);

		$this->add_control(
			'counters_heading',
			array(
				'label'      => esc_html__( 'Counters', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::HEADING,
				'separator'  => 'before',
				'conditions' => $counters_cond,
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'       => 'counters_typo',
				'selector'   => '{{WRAPPER}} .premium-tiktok-feed__video-counts > span:not(.premium-tiktok-feed__shares)',
				'conditions' => $counters_cond,
			)
		);

		$this->add_control(
			'counters_icon_size',
			array(
				'label'      => __( 'Icon Size', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', '%', 'em' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-tiktok-feed__video-counts > span > i' => 'font-size: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .premium-tiktok-feed__video-counts > span > svg' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};',
				),
				'conditions' => $counters_cond,
			)
		);

		$this->add_control(
			'counter_color_color',
			array(
				'label'      => __( 'Color', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::COLOR,
				'selectors'  => array(
					'{{WRAPPER}} .premium-tiktok-feed__video-counts > span:not(.premium-tiktok-feed__shares)' => 'color: {{VALUE}};',
				),
				'conditions' => $counters_cond,
			)
		);

		$this->add_control(
			'counter_icon_color',
			array(
				'label'      => __( 'Icon Color', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::COLOR,
				'selectors'  => array(
					'{{WRAPPER}} .premium-tiktok-feed__video-counts > span > i' => 'color: {{VALUE}};',
					'{{WRAPPER}} .premium-tiktok-feed__video-counts > span > svg, {{WRAPPER}} .premium-tiktok-feed__video-counts > span > svg *' => 'fill: {{VALUE}};',

				),
				'conditions' => $counters_cond,
			)
		);

		$this->add_group_control(
			Group_Control_Text_Shadow::get_type(),
			array(
				'name'       => 'counters_text_shadow',
				'selector'   => '{{WRAPPER}} .premium-tiktok-feed__video-counts > span:not(.premium-tiktok-feed__shares)',
				'conditions' => $counters_cond,
			)
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			array(
				'name'       => 'counters_bg',
				'types'      => array( 'classic', 'gradient' ),
				'selector'   => '{{WRAPPER}} .premium-tiktok-feed__video-counts',
				'conditions' => $counters_cond,
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'       => 'counters_border',
				'selector'   => '{{WRAPPER}} .premium-tiktok-feed__video-counts',
				'conditions' => $counters_cond,
			)
		);

		$this->add_control(
			'counters_rad',
			array(
				'label'      => __( 'Border Radius', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%', 'em' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-tiktok-feed__video-counts' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
				'conditions' => $counters_cond,
			)
		);

		$this->add_responsive_control(
			'counters_padding',
			array(
				'label'      => __( 'Padding', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-tiktok-feed__video-counts' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
				'conditions' => $counters_cond,
			)
		);

		$this->add_responsive_control(
			'counters_margin',
			array(
				'label'      => __( 'Margin', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em' ),
				'default'    => array(
					'top'    => 15,
					'right'  => 15,
					'bottom' => 15,
					'left'   => 15,
					'unit'   => 'px',
				),
				'selectors'  => array(
					'{{WRAPPER}} .premium-tiktok-feed__video-counts' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
				// 'conditions' => $counters_cond,
				'conditions' => array(
					'terms' => array(
						array(
							'name'     => 'vid_layout',
							'operator' => '!==',
							'value'    => 'layout-3',
						),
						$counters_cond,
					),
				),
			)
		);

		$this->add_control(
			'counters_spacing',
			array(
				'label'      => __( 'Spacing', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', '%', 'em' ),
				'separator'  => 'before',
				'selectors'  => array(
					'{{WRAPPER}} .premium-tiktok-feed__video-counts' => 'column-gap: {{SIZE}}{{UNIT}};',
				),
				'conditions' => $counters_cond,
			)
		);

		$this->add_control(
			'counters_icon_spacing',
			array(
				'label'      => __( 'Icon Spacing', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', '%', 'em' ),
				'separtor'   => 'before',
				'selectors'  => array(
					'{{WRAPPER}} .premium-tiktok-feed__video-counts > span > i, {{WRAPPER}} .premium-tiktok-feed__video-counts > span > svg ' => 'margin: 0 {{SIZE}}{{UNIT}};',
				),
				'conditions' => $counters_cond,
			)
		);

		$this->end_controls_section();
	}

	private function add_carousel_style() {

		$this->start_controls_section(
			'carousel_dots_style',
			array(
				'label'     => __( 'Carousel Dots', 'premium-addons-for-elementor' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => array(
					'carousel'      => 'yes',
					'carousel_dots' => 'yes',
				),
			)
		);

		$this->add_control(
			'dot_color',
			array(
				'label'     => __( 'Color', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'global'    => array(
					'default' => Global_Colors::COLOR_SECONDARY,
				),
				'selectors' => array(
					'{{WRAPPER}} ul.slick-dots li' => 'color: {{VALUE}}',
				),
			)
		);

		$this->add_control(
			'dot_color_act',
			array(
				'label'     => __( 'Active Color', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'global'    => array(
					'default' => Global_Colors::COLOR_PRIMARY,
				),
				'selectors' => array(
					'{{WRAPPER}} ul.slick-dots li.slick-active' => 'color: {{VALUE}}',
				),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'carousel_arrows_style',
			array(
				'label'     => __( 'Carousel Arrows', 'premium-addons-for-elementor' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => array(
					'carousel'        => 'yes',
					'carousel_arrows' => 'yes',
				),
			)
		);

		$this->add_control(
			'arrow_color',
			array(
				'label'     => __( 'Color', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'global'    => array(
					'default' => Global_Colors::COLOR_PRIMARY,
				),
				'selectors' => array(
					'{{WRAPPER}} .premium-tiktok-feed__videos-wrapper .slick-arrow' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_responsive_control(
			'arrow_size',
			array(
				'label'      => __( 'Size', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', '%', 'em' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-tiktok-feed__videos-wrapper .slick-arrow i' => 'font-size: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'arrow_bg',
			array(
				'label'     => __( 'Background Color', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'global'    => array(
					'default' => Global_Colors::COLOR_SECONDARY,
				),
				'selectors' => array(
					'{{WRAPPER}} .premium-tiktok-feed__videos-wrapper .slick-arrow' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'border_radius',
			array(
				'label'      => __( 'Border Radius', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', '%', 'em' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-tiktok-feed__videos-wrapper .slick-arrow' => 'border-radius: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'arrow_padding',
			array(
				'label'      => __( 'Padding', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', '%', 'em' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-tiktok-feed__videos-wrapper .slick-arrow' => 'padding: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_section();
	}

	private function add_share_btn_style() {

		$icon_spacing = is_rtl() ? 'left' : 'right';

		$this->start_controls_section(
			'pa_tiktok_Sb_style',
			array(
				'label'     => __( 'Share Button', 'premium-addons-for-elementor' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => array(
					'share_count' => 'yes',
				),
			)
		);

		$this->add_responsive_control(
			'pa_share_btn_icon_size',
			array(
				'label'      => __( 'Icon Size', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', 'em', '%' ),
				'range'      => array(
					'px' => array(
						'min' => 0,
						'max' => 50,
					),
					'em' => array(
						'min' => 1,
						'max' => 100,
					),
				),
				'default'    => array(
					'size' => 17,
					'unit' => 'px',
				),
				'selectors'  => array(
					'{{WRAPPER}} .fa.custom-fa' => 'font-size: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'pa_share_btn_typo',
				'selector' => '{{WRAPPER}} .premium-tiktok-sharer',
			)
		);

		$this->add_control(
			'pa_share_btn_bg',
			array(
				'label'     => __( 'Background Color', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .premium-tiktok-share-container' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_responsive_control(
			'pa_share_btn_spacing',
			array(
				'label'      => __( 'Icon Spacing', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', 'em', '%' ),
				'range'      => array(
					'px' => array(
						'min' => 0,
						'max' => 50,
					),
					'em' => array(
						'min' => 1,
						'max' => 100,
					),
				),
				'default'    => array(
					'size' => 10,
					'unit' => 'px',
				),
				'selectors'  => array(
					'{{WRAPPER}} .fa.custom-fa' => 'margin-' . $icon_spacing . ': {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->start_controls_tabs( 'pa_share_buttons' );

		$this->start_controls_tab(
			'pa_sb_normal',
			array(
				'label' => __( 'Normal', 'premium-addons-for-elementor' ),
			)
		);

		$this->add_control(
			'pa_share_btn_icon_color',
			array(
				'label'     => __( 'Icon Color', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .fa.custom-fa' => '-webkit-text-stroke-color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'pa_share_btn_color',
			array(
				'label'     => __( 'Text Color', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .premium-tiktok-sharer' => 'color: {{VALUE}};',
				),
			)
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'pa_sb_hover',
			array(
				'label' => __( 'Hover', 'premium-addons-for-elementor' ),
			)
		);

		$this->add_control(
			'pa_share_btn_Icolor_hov',
			array(
				'label'     => __( 'Icon Color', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .premium-tiktok-share-button:hover .fa.custom-fa' => '-webkit-text-stroke-color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'pa_share_btn_color_hov',
			array(
				'label'     => __( 'Text Color', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .premium-tiktok-share-button:hover .premium-tiktok-sharer' => 'color: {{VALUE}};',
				),
			)
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'      => 'pa_share_btn_border',
				'selector'  => '{{WRAPPER}} .premium-tiktok-share-container',
				'separator' => 'before',
			)
		);

		$this->add_control(
			'pa_share_btn_border_radius',
			array(
				'label'      => __( 'Border Radius', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', '%', 'em' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-tiktok-share-container' => 'border-radius: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'pa_share_btn_margin',
			array(
				'label'      => __( 'Margin', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-tiktok-share-container' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'pa_share_btn_padding',
			array(
				'label'      => __( 'Padding', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-tiktok-share-container' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_section();
	}

	private function add_share_links_style() {

		$icon_spacing = is_rtl() ? 'left' : 'right';

		$this->start_controls_section(
			'pa_tiktok_Sl_style',
			array(
				'label'     => __( 'Share Links', 'premium-addons-for-elementor' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => array(
					'share_count' => 'yes',
				),
			)
		);

		$this->add_control(
			'pa_sl_menu_bg',
			array(
				'label'     => __( 'Menu Background Color', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .premium-tiktok-share-menu' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'pa_sl_typo',
				'selector' => '{{WRAPPER}} .premium-tiktok-share-text',
			)
		);

		$this->add_responsive_control(
			'pa_sl_icon_size',
			array(
				'label'      => __( 'Icon Size', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', 'em', '%' ),
				'range'      => array(
					'px' => array(
						'min' => 0,
						'max' => 50,
					),
					'em' => array(
						'min' => 1,
						'max' => 100,
					),
				),
				'default'    => array(
					'size' => 17,
					'unit' => 'px',
				),
				'selectors'  => array(
					'{{WRAPPER}} .premium-tiktok-share-item i' => 'font-size: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'pa_sl_border_radius',
			array(
				'label'      => __( 'Border Radius', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', '%', 'em' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-tiktok-share-menu' => 'border-radius: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'pa_sl_spacing',
			array(
				'label'      => __( 'Icon Spacing', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', 'em', '%' ),
				'range'      => array(
					'px' => array(
						'min' => 0,
						'max' => 50,
					),
					'em' => array(
						'min' => 1,
						'max' => 100,
					),
				),
				'default'    => array(
					'size' => 3,
					'unit' => 'px',
				),
				'selectors'  => array(
					'{{WRAPPER}} .premium-tiktok-share-item i' => 'margin-' . $icon_spacing . ': {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'pa_sl_spacing_ver',
			array(
				'label'      => __( 'Vertical Spacing', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', 'em', '%' ),
				'range'      => array(
					'px' => array(
						'min' => 0,
						'max' => 50,
					),
					'em' => array(
						'min' => 1,
						'max' => 100,
					),
				),
				'selectors'  => array(
					'{{WRAPPER}} .premium-tiktok-share-item' => 'margin-top: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .premium-tiktok-share-item' => 'margin-bottom: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->start_controls_tabs( 'pa_share_links' );

		$this->start_controls_tab(
			'pa_sl_normal',
			array(
				'label' => __( 'Normal', 'premium-addons-for-elementor' ),
			)
		);

		$this->add_control(
			'pa_sl_color',
			array(
				'label'     => __( 'Text Color', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .premium-tiktok-share-text' => 'color: {{VALUE}};',
				),
			)
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'pa_sl_hover',
			array(
				'label' => __( 'Hover', 'premium-addons-for-elementor' ),
			)
		);

		$this->add_control(
			'pa_sl_color_hov',
			array(
				'label'     => __( 'Text Color', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .premium-tiktok-share-item:hover .premium-tiktok-share-text' => 'color: {{VALUE}};',
				),
			)
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

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

		$papro_activated = apply_filters( 'papro_activated', false );

		if ( ! $papro_activated || version_compare( PREMIUM_PRO_ADDONS_VERSION, '2.9.4', '<' ) ) {

			if ( 'layout-1' !== $settings['vid_layout'] || 'yes' === $settings['load_more_btn'] || 'yes' === $settings['profile_header'] || 'yes' === $settings['autoplay_hover'] || 'yes' === $settings['autoplay_first'] || ! empty( $settings['match_id'] || ! empty( $settings['exclude_id'] ) ) ) {

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
		}

		$widget_id = $this->get_id();

		$access_token = $settings['access_token'];

		if ( empty( $access_token ) ) {
			?>
			<div class="premium-error-notice">
				<?php echo esc_html( __( 'Please fill the required fields: Access Token', 'premium-addons-for-elementor' ) ); ?>
			</div>
			<?php
			return;
		}

		$shortened_token = substr( $access_token, -8 );
		$is_valid        = get_transient( 'pa_tiktok_token_' . $shortened_token );

		if ( false === $is_valid ) {

			$refreshed_token = refresh_tiktok_token( $shortened_token );

			$settings['access_token'] = $refreshed_token;
		} else {
			$settings['access_token'] = $is_valid;
		}

		$show_feed    = 'yes' === $settings['show_feed'];
		$show_profile = 'yes' === $settings['profile_header'];
		$profile_data = get_tiktok_profile_data( $widget_id, $settings );

		if ( 'error' === $profile_data ) {
			return;
		}

		if ( $show_feed ) {

			$tiktok_feed = get_tiktok_data( $widget_id, $settings );

			$videos_urls = get_tiktok_videos_urls( $settings );

			$user_info = array(
				'username'    => $profile_data['display_name'],
				'url'         => $profile_data['profile_deep_link'],
				'is_verified' => $profile_data['is_verified'],
			);

			$carousel      = 'yes' === $settings['carousel'];
			$load_more_btn = ! $carousel && 'yes' === $settings['load_more_btn'];

			$tiktok_settings = array(
				'carousel'    => $carousel,
				'layout'      => $settings['outer_layout'],
				'playOnHover' => 'yes' === $settings['autoplay_hover'],
				'onClick'     => $settings['onclick'],
			);

			if ( $carousel ) {

				$carousel_settings = array(
					'slidesToScroll'     => $settings['slides_to_scroll'],
					'slidesToShow'       => empty( $settings['pa_tiktok_cols'] ) ? 4 : $settings['pa_tiktok_cols'],
					'slidesToShowTab'    => isset( $settings['pa_tiktok_cols_tablet'] ) ? $settings['pa_tiktok_cols_tablet'] : 1,
					'slidesToShowMobile' => isset( $settings['pa_tiktok_cols_mobile'] ) ? $settings['pa_tiktok_cols_mobile'] : 1,
					'fade'               => 'yes' === $settings['fade'],
					'autoPlay'           => 'yes' === $settings['auto_play'],
					'autoplaySpeed'      => $settings['autoplay_speed'],
					'centerMode'         => 'yes' === $settings['carousel_center'],
					'centerPadding'      => $settings['carousel_spacing'],
					'arrows'             => 'yes' === $settings['carousel_arrows'],
					'dots'               => 'yes' === $settings['carousel_dots'],
					'speed'              => $settings['carousel_speed'],
				);

				$this->add_render_attribute( 'outer_container', 'data-pa-carousel', wp_json_encode( $carousel_settings ) );
			}

			if ( $load_more_btn ) {
				$load_more_count                  = empty( $settings['no_per_load'] ) ? 3 : $settings['no_per_load'];
				$tiktok_settings['loadMore']      = true;
				$tiktok_settings['loadMoreCount'] = empty( $settings['no_per_load'] ) ? 3 : $settings['no_per_load'];

				$this->add_render_attribute( 'outer_container', 'data-pa-load-bookmark', $settings['pa_tiktok_cols'] );
			}

			$this->add_render_attribute( 'videos_container', 'class', 'premium-tiktok-feed__videos-wrapper' );

			$this->add_render_attribute( 'outer_container', 'data-pa-tiktok-settings', wp_json_encode( $tiktok_settings ) );
		}

		$this->add_render_attribute( 'outer_container', 'class', 'premium-tiktok-feed__outer-wrapper' );

		?>
		<div <?php echo wp_kses_post( $this->get_render_attribute_string( 'outer_container' ) ); ?>>
			<?php
			if ( $show_profile ) {
				?>
					<div class="premium-tiktok-feed__profile-header"> <?php $this->render_user_profile( $widget_id, $settings, $profile_data ); ?> </div>
				<?php
			}

			if ( $show_feed ) {
				$this->render_tiktok_videos( $tiktok_feed, $settings, $user_info, $videos_urls );

				if ( $load_more_btn ) {
					?>
						<div class="premium-tiktok-feed__load-more-wrapper">
							<a type="button" data-role="none" role="button" class="premium-tiktok-feed__load-more-btn"><?php echo esc_html( $settings['more_btn_txt'] ); ?></a>
						</div>
					<?php
				}
			}
			?>
		</div>
		<?php

		if ( Plugin::instance()->editor->is_edit_mode() ) {

			if ( 'masonry' === $settings['outer_layout'] && 'yes' !== $settings['carousel'] ) {
				$this->render_editor_script();
			}
		}
	}

	/**
	 * Renders TikTok Videos.
	 *
	 * @access private
	 * @since 4.10.3
	 *
	 * @param array $tiktok_feed  videos feed.
	 * @param array $settings  widget settings.
	 * @param array $user_info  user info.
	 */
	private function render_tiktok_videos( $tiktok_feed, $settings, $user_info, $videos_urls ) {

		$vid_layout      = $settings['vid_layout'];
		$exclude_arr     = array();
		$load_more_btn   = 'yes' !== $settings['carousel'] && 'yes' === $settings['load_more_btn'];
		$load_more_count = 3;

		$vid_settings = array(
			'tiktok_icon' => 'yes' === $settings['vid_tiktok_icon'],
			'username'    => 'yes' === $settings['vid_username'],
			'desc'        => 'yes' === $settings['vid_desc'],
			'date'        => 'yes' === $settings['create_time'],
			'likes'       => 'yes' === $settings['like_count'],
			'comments'    => 'yes' === $settings['comment_count'],
			'shares'      => 'yes' === $settings['share_count'],
			'views'       => 'yes' === $settings['view_count'],
		);

		$show_vid_counters = $vid_settings['likes'] || $vid_settings['comments'] || $vid_settings['shares'] || $vid_settings['views'];

		if ( $load_more_btn ) {
			$load_more_count = empty( $settings['no_per_load'] ) ? $load_more_count : $settings['no_per_load'];
		}

		if ( empty( $settings['match_id'] ) ) {

			if ( 'reverse' === $settings['sort'] ) {
				$tiktok_feed = array_reverse( $tiktok_feed );
			}

			if ( ! empty( $settings['exclude_id'] ) ) {
				$exclude_arr = explode( ',', $settings['exclude_id'] );
			}
		}
		?>
			<div <?php echo wp_kses_post( $this->get_render_attribute_string( 'videos_container' ) ); ?>>
				<?php
				foreach ( $tiktok_feed as $index => $feed ) {

					$video_url = $videos_urls[ $feed['id'] ];

					$video_html = '<video src="' . $video_url . '#t=0.001" playsinline';

					$play_icon = 'yes' === $settings['vid_play_icon'] ? 'premium-tiktok-feed__play-icon ' : '';
					if ( 'yes' === $settings['autoplay_all'] || ( 0 == $index && 'yes' === $settings['autoplay_first'] ) ) {

						$video_html .= ' preload="auto"';

						$video_html .= ' autoplay';

						$video_html .= ' class="video-playing"';

						if ( ! empty( $play_icon ) ) {
							if ( 'yes' !== $settings['autoplay_hover'] || ( 0 == $index && 'yes' === $settings['autoplay_first'] ) ) {
								$play_icon .= 'premium-addons__v-hidden';
							}
						}
					}

					if ( 'yes' === $settings['mute_videos'] ) {
						$video_html .= ' muted';
					}

					if ( 'yes' === $settings['autoplay_hover'] ) {
						$video_html .= ' preload="auto"';
					}

					if ( 'yes' === $settings['loop_videos'] ) {
						$video_html .= ' loop';
					}

					$video_html .= '></video>';

					if ( count( $exclude_arr ) && in_array( $feed['id'], $exclude_arr, true ) ) {
						continue;
					}

					$this->add_render_attribute( 'vid_outer_container' . $index, 'class', 'premium-tiktok-feed__video-outer-wrapper' );

					if ( $load_more_btn && $index >= $settings['pa_tiktok_cols'] ) {
						$this->add_render_attribute( 'vid_outer_container' . $index, 'class', 'premium-display-none' );
					}
					?>
					<div <?php echo wp_kses_post( $this->get_render_attribute_string( 'vid_outer_container' . $index ) ); ?>>
						<div class="premium-tiktok-feed__video-wrapper">
						<?php if ( 'layout-1' === $vid_layout ) { ?>

							<div class="premium-tiktok-feed__vid-meta-wrapper">
								<?php
								if ( $vid_settings['username'] || $vid_settings['date'] ) {
									$this->get_video_meta( $settings, $vid_settings, $feed, $user_info );
								}
								?>

								<?php if ( $vid_settings['tiktok_icon'] ) : ?>
									<?php $this->render_tiktok_icon(); ?>
								<?php endif; ?>
							</div>
							<?php if ( $vid_settings['desc'] ) : ?>
								<div class="premium-tiktok-feed__vid-desc"><?php $this->render_feed_desc( $feed['video_description'], $feed['share_url'], $settings ); ?> </div>
							<?php endif; ?>

							<div class="premium-tiktok-feed__video-media" data-pa-tiktok-user="<?php echo esc_attr( $user_info['username'] ); ?>" data-pa-tiktok-embed="<?php echo esc_attr( $feed['id'] ); ?>" data-pa-tiktok-embed-width="<?php echo esc_attr( $feed['width'] ); ?>" data-pa-tiktok-embed-height="<?php echo esc_attr( $feed['height'] ); ?>">
								<?php if ( ! empty( $play_icon ) ) : ?>
									<span class="<?php echo esc_attr( $play_icon ); ?>">
										<i class="fas fa-play" aria-hidden="true"></i>
									</span>
								<?php endif; ?>
								<?php // echo $feed['embed_html']; ?>

								<?php echo $video_html; ?>

								<?php if ( 'default' === $settings['onclick'] ) : ?>
									<a class="premium-tiktok-feed__video-link" href="<?php echo esc_attr( $feed['share_url'] ); ?>" target="_blank"></a>
								<?php endif; ?>
							</div>

							<?php
							if ( $show_vid_counters ) {
								$this->get_video_counters( $vid_settings, $feed );
							}
							?>
						<?php } elseif ( 'layout-2' === $vid_layout ) { ?>
							<div class="premium-tiktok-feed__vid-meta-wrapper">
								<div class="premium-tiktok-feed__vid-inner-meta">
									<?php
									if ( $vid_settings['username'] || $vid_settings['date'] ) {
										$this->get_video_meta( $settings, $vid_settings, $feed, $user_info );
									}
									?>

									<?php if ( $vid_settings['tiktok_icon'] ) : ?>
										<?php $this->render_tiktok_icon(); ?>
									<?php endif; ?>
								</div>

								<?php if ( $vid_settings['desc'] ) : ?>
									<div class="premium-tiktok-feed__vid-desc"><?php $this->render_feed_desc( $feed['video_description'], $feed['share_url'], $settings ); ?></div>
								<?php endif; ?>

								<?php
								if ( $show_vid_counters ) {
									$this->get_video_counters( $vid_settings, $feed );
								}
								?>
							</div>
							<div class="premium-tiktok-feed__video-media" data-pa-tiktok-user="<?php echo esc_attr( $user_info['username'] ); ?>" data-pa-tiktok-embed="<?php echo esc_attr( $feed['id'] ); ?>" data-pa-tiktok-embed-width="<?php echo esc_attr( $feed['width'] ); ?>" data-pa-tiktok-embed-height="<?php echo esc_attr( $feed['height'] ); ?>">
								<?php if ( ! empty( $play_icon ) ) : ?>
									<span class="<?php echo esc_attr( $play_icon ); ?>">
										<i class="fas fa-play" aria-hidden="true"></i>
									</span>
								<?php endif; ?>

								<?php echo $video_html; ?>

								<?php if ( 'default' === $settings['onclick'] ) : ?>
									<a class="premium-tiktok-feed__video-link" href="<?php echo esc_attr( $feed['share_url'] ); ?>" target="_blank"></a>
								<?php endif; ?>
							</div>
						<?php } else { ?>

							<div class="premium-tiktok-feed__vid-meta-wrapper">

								<?php if ( $vid_settings['tiktok_icon'] ) : ?>
									<?php $this->render_tiktok_icon(); ?>
								<?php endif; ?>

								<?php if ( $vid_settings['desc'] ) : ?>
									<div class="premium-tiktok-feed__vid-desc"><?php $this->render_feed_desc( $feed['video_description'], $feed['share_url'], $settings ); ?> </div>
								<?php endif; ?>

								<?php
								if ( $vid_settings['username'] || $vid_settings['date'] ) {
									$this->get_video_meta( $settings, $vid_settings, $feed, $user_info );
								}

								if ( $show_vid_counters ) {
									$this->get_video_counters( $vid_settings, $feed );
								}
								?>
							</div>
							<div class="premium-tiktok-feed__video-media" data-pa-tiktok-user="<?php echo esc_attr( $user_info['username'] ); ?>" data-pa-tiktok-embed="<?php echo esc_attr( $feed['id'] ); ?>" data-pa-tiktok-embed-width="<?php echo esc_attr( $feed['width'] ); ?>" data-pa-tiktok-embed-height="<?php echo esc_attr( $feed['height'] ); ?>">

								<?php if ( ! empty( $play_icon ) ) : ?>
									<span class="<?php echo esc_attr( $play_icon ); ?>">
										<i class="fas fa-play" aria-hidden="true"></i>
									</span>
								<?php endif; ?>

								<?php echo $video_html; ?>

								<?php if ( 'default' === $settings['onclick'] ) : ?>
									<a class="premium-tiktok-feed__video-link" href="<?php echo esc_attr( $feed['share_url'] ); ?>" target="_blank"></a>
								<?php endif; ?>
							</div>
						<?php } ?>
						</div>
					</div>
					<?php
				}
				?>
			</div>
			<div class="premium-tiktok-feed-modal-iframe-modal">
				<div class="premium-tiktok-temp-close">
					<i class="eicon-close"></i>
				</div>

				<div class="premium-tiktok-feed__video-content">
					<iframe id="pa-tiktok-vid-control-iframe"></iframe>
				</div>
			</div>
			<script async src='https://www.tiktok.com/embed.js'></script>
		<?php
	}

	/**
	 * Renders user profile.
	 *
	 * @access private
	 * @since 4.10.3
	 *
	 * @param string $id widget id.
	 * @param array  $settings  widget settings.
	 * @param array  $profile_data   profile data.
	 */
	private function render_user_profile( $id, $settings, $profile_data ) {

		$show_counts     = 'yes' === $settings['following_count'] || 'yes' === $settings['follower_count'] || 'yes' === $settings['likes_count'];
		$show_desc       = 'yes' === $settings['bio_description'];
		$show_follow_btn = 'yes' === $settings['follow_button'];
		$is_inline       = 'row' === $settings['profile_basic_display'];

		$show_avatar   = 'yes' === $settings['avatar_url'];
		$show_username = 'yes' === $settings['username'];

		if ( $show_follow_btn ) {
			$follow_url = $profile_data['profile_deep_link'];
		}

		?>
			<div class="premium-tiktok-feed__user-info-wrapper">

				<?php if ( $show_avatar || $show_username ) : ?>
					<div class="premium-tiktok-feed__user-info">
						<?php if ( $show_avatar ) : ?>
							<div class="premium-tiktok-feed__avatar">
								<a href="<?php echo esc_url( $follow_url ); ?>" target="_blank">
									<img src="<?php echo esc_url( $profile_data['avatar_url'] ); ?>" alt="<?php echo esc_attr( $profile_data['display_name'] ); ?>" >
								</a>
							</div>
						<?php endif; ?>

						<?php if ( $show_username ) : ?>
							<span class="premium-tiktok-feed__username">
								<a href="<?php echo esc_url( $follow_url ); ?>" target="_blank"> <?php echo esc_html( $profile_data['display_name'] ); ?> </a>
							</span>
						<?php endif; ?>
					</div>
				<?php endif; ?>
				<?php
				if ( $show_follow_btn && $is_inline ) {
					?>
					<a class="premium-tiktok-feed__follow-button" href="<?php echo esc_url( $follow_url ); ?>" target="_blank">
						<?php $this->render_tiktok_icon( 'follow' ); ?>
						<span class="premium-tiktok-feed__follow-text"><?php echo esc_html__( 'Follow', 'premium-addons-for-elementor' ); ?></span>
					</a>

				<?php } ?>
			</div>

			<?php if ( $show_counts ) : ?>
				<div class="premium-tiktok-feed__profile-counts"> <?php $this->get_counters( $settings, $profile_data ); ?></div>
			<?php endif; ?>
			<?php if ( $show_desc ) : ?>
				<div class="premium-tiktok-feed__profile-desc"><?php echo esc_html( $profile_data['bio_description'] ); ?></div>
			<?php endif; ?>
			<?php
			if ( $show_follow_btn && ! $is_inline ) {

				?>
					<a class="premium-tiktok-feed__follow-button" href="<?php echo esc_url( $follow_url ); ?>" target="_blank">
						<?php $this->render_tiktok_icon( 'follow' ); ?>
						<span class="premium-tiktok-feed__follow-text"><?php echo esc_html__( 'Follow', 'premium-addons-for-elementor' ); ?></span>
					</a>

				<?php
			}
			?>
		<?php

	}

	/**
	 * Render tiktok Icon
	 *
	 * @access private
	 * @since 4.10.3
	 *
	 * @param boolean $from is follow button.
	 */
	private function render_tiktok_icon( $from = 'video' ) {

		$follow_class = 'premium-tiktok-icon-' . $from;
		?>
			<span class="premium-tiktok-feed__tiktok-icon <?php echo esc_attr( $follow_class ); ?>">
				<svg id="TikTok" xmlns="http://www.w3.org/2000/svg" width="18.72" height="18.72" viewBox="0 0 18.72 18.72"><defs><style>.premium-tiktok-icon-1{fill:#25f4ee;}.premium-tiktok-icon-1,.premium-tiktok-icon-2,.premium-tiktok-icon-3{fill-rule:evenodd;}.premium-tiktok-icon-2{fill:#fff;}.premium-tiktok-icon-3{fill:#ff1753;}</style></defs><circle cx="9.36" cy="9.36" r="9.36"/><path class="premium-tiktok-icon-3" d="m11.91,7.68c.76.56,1.69.89,2.71.9l.03-1.95c-.19,0-.38-.03-.57-.07l-.02,1.53c-1.01-.01-1.95-.35-2.71-.9l-.05,3.98c-.03,1.99-1.66,3.58-3.65,3.56-.74,0-1.43-.24-2-.64.65.68,1.55,1.1,2.56,1.12,1.99.03,3.63-1.57,3.65-3.56l.05-3.98h0Zm.73-1.96c-.39-.43-.64-.99-.68-1.6v-.25s-.54,0-.54,0c.13.78.58,1.45,1.22,1.86h0Zm-5.72,6.86c-.21-.29-.33-.64-.32-1,.01-.91.76-1.64,1.67-1.63.17,0,.34.03.5.08l.03-1.99c-.19-.03-.38-.04-.57-.04l-.02,1.55c-.16-.05-.33-.08-.5-.08-.91-.01-1.66.72-1.67,1.63,0,.64.35,1.21.89,1.48Z"/><path class="premium-tiktok-icon-2" d="m11.35,7.19c.76.56,1.69.89,2.71.9l.02-1.53c-.56-.13-1.06-.43-1.43-.85-.64-.41-1.1-1.08-1.22-1.86l-1.42-.02-.1,7.79c-.01.91-.76,1.63-1.67,1.62-.54,0-1.01-.27-1.3-.67-.53-.28-.9-.84-.89-1.48.01-.91.76-1.64,1.67-1.63.17,0,.34.03.5.08l.02-1.55c-1.96.01-3.55,1.59-3.58,3.56-.01.98.37,1.87.99,2.53.57.39,1.26.63,2,.64,1.99.03,3.63-1.57,3.65-3.56l.05-3.98Z"/><path class="premium-tiktok-icon-1" d="m14.08,6.56v-.41c-.5,0-1-.16-1.43-.43.38.42.88.72,1.43.85Zm-2.65-2.7c-.01-.07-.02-.15-.03-.22v-.25s-1.96-.03-1.96-.03l-.1,7.79c-.01.91-.76,1.63-1.67,1.62-.27,0-.52-.07-.74-.19.3.4.77.66,1.3.67.91.01,1.66-.71,1.67-1.62l.1-7.79,1.42.02Zm-3.19,4.14v-.44c-.16-.02-.32-.04-.49-.04-1.99-.03-3.63,1.57-3.65,3.56-.02,1.25.6,2.35,1.56,3.01-.63-.66-1.01-1.55-.99-2.53.03-1.96,1.62-3.54,3.58-3.56h0Z"/></svg>
			</span>
		<?php
	}

	/**
	 * Displays profile counters.
	 *
	 * @access private
	 * @since 4.10.3
	 *
	 * @param array $settings  widget settings.
	 * @param array $data   feed data.
	 */
	private function get_counters( $settings, $data ) {
		?>
		<?php if ( $settings['follower_count'] && ! is_null( $data['follower_count'] ) ) : ?>
			<span class="premium-tiktok-feed__followers" title="Followers">
				<span class="premium-tiktok-feed__count"> <?php echo esc_html( Helper_Functions::premium_format_numbers( $data['follower_count'] ) ); ?></span>
				<span>Followers</span>
			</span>
		<?php endif; ?>

		<?php if ( $settings['following_count'] && ! is_null( $data['following_count'] ) ) : ?>
			<span class="premium-tiktok-feed__followings" title="Following">
				<span class="premium-tiktok-feed__count"><?php echo esc_html( Helper_Functions::premium_format_numbers( $data['following_count'] ) ); ?></span>
				<span>Following</span>
			</span>
		<?php endif; ?>

		<?php if ( $settings['likes_count'] && ! is_null( $data['likes_count'] ) ) : ?>
			<span class="premium-tiktok-feed__views"  title="Likes">
				<span class="premium-tiktok-feed__count"><?php echo esc_html( Helper_Functions::premium_format_numbers( $data['likes_count'] ) ); ?></span>
				<span>Likes</span>
			</span>
		<?php endif; ?>
		<?php
	}

	/**
	 * Displays Video Likes, Comments, Views, Shares counters.
	 *
	 * @access private
	 * @since 4.10.3
	 *
	 * @param array $vid_settings  video settings.
	 * @param array $feed   videos feed.
	 */
	private function get_video_counters( $vid_settings, $feed ) {

		$settings = $this->get_settings_for_display();

		$layout = $settings['vid_layout'];

		?>
		<div class="premium-tiktok-feed__video-counts">

			<?php if ( $vid_settings['likes'] ) : ?>
				<span class="premium-tiktok-feed__likes" title="Likes">
					<i class="far fa-heart"></i>
					<span class="premium-tiktok-feed__count"> <?php echo esc_html( Helper_Functions::premium_format_numbers( $feed['like_count'] ) ); ?></span>
				</span>
			<?php endif; ?>

			<?php if ( $vid_settings['comments'] ) : ?>
				<span class="premium-tiktok-feed__comments" title="Comments">
					<i class="far fa-comment"></i>
					<span class="premium-tiktok-feed__count"><?php echo esc_html( Helper_Functions::premium_format_numbers( $feed['comment_count'] ) ); ?></span>
				</span>
			<?php endif; ?>

			<?php if ( $vid_settings['views'] ) : ?>
				<span class="premium-tiktok-feed__views"  title="Views">
					<i class="far fa-eye"></i>
					<span class="premium-tiktok-feed__count"><?php echo esc_html( Helper_Functions::premium_format_numbers( $feed['view_count'] ) ); ?></span>
				</span>
			<?php endif; ?>

			<?php if ( $vid_settings['shares'] ) : ?>
				<span class="premium-tiktok-feed__shares" title="Shares">
					<?php $this->render_share_button( $feed['share_url'] ); ?>
				</span>
			<?php endif; ?>

			<?php if ( 'layout-2' !== $layout ) : ?>
				<a class="premium-tiktok-feed__video-link" href="<?php echo esc_attr( $feed['share_url'] ); ?>" target="_blank"></a>
			<?php endif; ?>

		</div>
		<?php
	}

	/**
	 * Displays Video Meta Info.
	 *
	 * @access private
	 * @since 4.10.3
	 *
	 * @param array $settings  widget settings.
	 * @param array $vid_settings  video settings.
	 * @param array $feed   video obj.
	 * @param array $user_info   user info.
	 */
	private function get_video_meta( $settings, $vid_settings, $feed, $user_info ) {
		?>
		<div class="premium-tiktok-feed__meta">

			<?php if ( $vid_settings['username'] ) : ?>
				<span class="premium-tiktok-feed__vid-creator">
					<a href="<?php echo esc_url( $user_info['url'] ); ?>" target="_blank"><?php echo esc_html( $user_info['username'] ); ?></a>
					<?php if ( $user_info['is_verified'] ) : ?>
						<i class="far fa-check-circle"></i>
					<?php endif; ?>
				</span>
			<?php endif; ?>

			<?php if ( $vid_settings['date'] ) : ?>
				<span class="premium-tiktok-feed__created-at"><?php echo esc_html( date( $settings['date_format'], $feed['create_time'] ) ); ?></span>
			<?php endif; ?>

		</div>
		<?php
	}

	/**
	 * Render Feed Description.
	 *
	 * @access private
	 * @since 4.10.3
	 *
	 * @param string $desc  feed description.
	 * @param string $url  feed URL.
	 * @param array  $settings  widget settings.
	 */
	private function render_feed_desc( $desc, $url, $settings ) {

		$len = $settings['vid_desc_len'];

		if ( ! empty( $len ) ) {

			$desc = $this->get_feed_desc( $desc, $len, $url, $settings );
		}

		echo wp_kses_post( $desc );
	}

	/**
	 * Render Feed Description.
	 *
	 * @access private
	 * @since 4.10.3
	 *
	 * @param string $desc  feed description.
	 * @param string $len  description length.
	 * @param string $url  feed URL.
	 * @param array  $settings  widget settings.
	 *
	 * @return string
	 */
	private function get_feed_desc( $desc, $len, $url, $settings ) {

		$desc = trim( $desc );

		$words = explode( ' ', $desc, $len + 1 );

		$postfix_type = $settings['vid_desc_postfix'];

		if ( count( $words ) > $len ) {

			array_pop( $words );

			$desc = implode( ' ', $words );

			if ( 'dots' === $postfix_type ) {
				$desc .= '...';
			} else {
				$desc .= '<a class="premium-read-more" target="_blank" href="' . $url . '">' . $settings['vid_desc_postfix_txt'] . '</a>';
			}
		}

		return $desc;
	}

	/**
	 * Render share button.
	 *
	 * @access private
	 * @since 4.10.3
	 *
	 * @param string $link feed link.
	 */
	private function render_share_button( $link ) {
		?>
		<div class="premium-tiktok-feed__share-outer">
			<div class="premium-tiktok-share-container">
				<span class="premium-tiktok-share-button">
					<i class="fa fa-share custom-fa" aria-hidden="true"></i>
					<span class="premium-tiktok-sharer">Share</span>
					<div class="premium-tiktok-share-menu">
						<a data-pa-link="<?php echo $link; ?>" href="javascript:;" class="premium-tiktok-share-item premium-copy-link">
							<i class="fas fa-link if-link"></i>
							<span class="premium-tiktok-share-text pre-infs-fb">Copy to Clipboard</span>
						</a>
						<a data-pa-link="https://www.facebook.com/sharer/sharer.php?u=<?php echo $link; ?>" href="javascript:;" class="premium-tiktok-share-item">
							<i class="fab fa-facebook-f if-fb"></i>
							<span class="premium-tiktok-share-text pre-infs-fb">Share on Facebook</span>
						</a>
						<a data-pa-link="https://twitter.com/intent/tweet?text=tweet&url=<?php echo $link; ?>" href="javascript:;" class="premium-tiktok-share-item">
							<i class="fab fa-twitter if-tw"></i>
							<span class="premium-tiktok-share-text pre-infs-tw">Share on Twitter</span>
						</a>
						<a data-pin-do="buttonPin" data-pa-link="https://www.pinterest.com/pin/create/button/?url=<?php echo $link; ?>" href="javascript:;" class="premium-tiktok-share-item">
							<i class="fab fa-pinterest-p if-pi"></i>
							<span class="premium-tiktok-share-text pre-infs-pi">Share on Pinterest</span>
						</a>
					</div>
				</span>
			</div>
		</div>
		<?php
	}

	/**
	 * Render Editor Masonry Script.
	 *
	 * @since 3.12.3
	 * @access protected
	 */
	protected function render_editor_script() {

		?>
		<script type="text/javascript">
			jQuery( document ).ready( function( $ ) {

				$( '.premium-tiktok-feed__videos-wrapper' ).each( function() {

					var $node_id 	= '<?php echo esc_attr( $this->get_id() ); ?>',
						scope 		= $( '[data-id="' + $node_id + '"]' ),
						selector 	= $(this);


					if ( selector.closest( scope ).length < 1 ) {
						return;
					}


					var masonryArgs = {
						itemSelector	: '.premium-tiktok-feed__video-outer-wrapper',
						percentPosition : true,
						layoutMode		: 'masonry',
					};

					var $isotopeObj = {};

					selector.imagesLoaded( function() {

						$isotopeObj = selector.isotope( masonryArgs );

						$isotopeObj.imagesLoaded().progress(function() {
							$isotopeObj.isotope("layout");
						});

						selector.find('.premium-tiktok-feed__video-outer-wrapper').resize( function() {
							$isotopeObj.isotope( 'layout' );
						});
					});

				});
			});
		</script>
		<?php
	}
}
