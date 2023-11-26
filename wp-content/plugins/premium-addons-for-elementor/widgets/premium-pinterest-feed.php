<?php
/**
 * Premium Pinterest Feed.
 */

namespace PremiumAddons\Widgets;

// Elementor Classes.
use Elementor\Plugin;
use Elementor\Widget_Base;
use Elementor\Icons_Manager;
use Elementor\Controls_Manager;
use Elementor\Control_Media;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Text_Shadow;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Background;
use Elementor\Group_Control_Css_Filter;
use Elementor\Core\Kits\Documents\Tabs\Global_Colors;
use Elementor\Core\Kits\Documents\Tabs\Global_Typography;

// PremiumAddons Classes.
use PremiumAddons\Includes\Controls\Premium_Select;
use PremiumAddons\Includes\Helper_Functions;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // If this file is called directly, abort.
}

/**
 * Class Premium_pinterest_Feed.
 */
class Premium_Pinterest_Feed extends Widget_Base {

	/**
	 * Retrieve Widget Name.
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function get_name() {
		return 'premium-pinterest-feed';
	}

	private $common_conds = array(
		'terms' => array(
			array(
				'name'  => 'show_feed',
				'value' => 'yes',
			),
			array(
				'relation' => 'or',
				'terms'    => array(
					array(
						'name'  => 'endpoint',
						'value' => 'pins/',
					),
					array(
						'terms' => array(
							array(
								'name'  => 'endpoint',
								'value' => 'boards/',
							),
							array(
								'name'  => 'boards_onclick',
								'value' => 'pins',
							),
						),
					),
				),
			),
		),
	);

	/**
	 * Retrieve Widget Title.
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function get_title() {
		return __( 'Pinterest Feed', 'premium-addons-for-elementor' );
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
		return 'pa-pinterest';
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

		return array(
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
		return array( 'pa', 'premium', 'social', 'pin' );
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
	 * Register World Clock controls.
	 *
	 * @since  1.0.0
	 * @access protected
	 */
	protected function register_controls() {

		$papro_activated = apply_filters( 'papro_activated', false );

		$this->add_login_controls();

		$this->add_query_controls();

		$this->add_general_controls(); // has extra controls.

		$this->add_pin_settings_controls();

		if ( $papro_activated ) {
			do_action( 'pa_pinterest_board_controls', $this );
		}

		$this->add_profile_controls();

		$this->add_carousel_section();

		$this->add_helpful_info_section();

		// style Controls.
		$this->add_image_style_controls();

		$this->add_info_style_controls();

		$this->add_feed_box_style_controls();

		if ( $papro_activated ) {
			do_action( 'pa_pinterest_board_style', $this );
		}

		$this->add_cta_style();

		$this->add_carousel_style();

		if ( $papro_activated ) {
			do_action( 'pa_pinterest_profile_style', $this );
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
			'pinterest_login',
			array(
				'type'        => Controls_Manager::RAW_HTML,
				'raw'         => '<form onsubmit="connectPinterestInit(this);" action="javascript:void(0);"><input type="submit" value="Log in with Pinterest" class="elementor-button" style="background-color: rgba(230, 0, 35, 0.73); color: #fff;"></form>',
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
					'hour'  => __( 'Hour', 'premium-addons-for-elementor' ),
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
			'pa_pinterest_query_sec',
			array(
				'label' => __( 'Query', 'premium-addons-for-elementor' ),
			)
		);

		$this->add_control(
			'show_feed',
			array(
				'label'     => __( 'Pins/Boards', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::SWITCHER,
				'label_on'  => __( 'Show', 'premium-addons-for-elementor' ),
				'label_off' => __( 'Hide', 'premium-addons-for-elementor' ),
				'default'   => 'yes',
			)
		);

		$this->add_control(
			'endpoint',
			array(
				'label'       => __( 'Source', 'premium-addons-for-elementor' ),
				'type'        => Controls_Manager::SELECT,
				'label_block' => true,
				'options'     => array(
					'pins/'   => __( 'Pins', 'premium-addons-for-elementor' ),
					'boards/' => __( 'Boards', 'premium-addons-for-elementor' ),
				),
				'default'     => 'pins/',
				'render_type' => 'template',
				'condition'   => array(
					'show_feed' => 'yes',
				),
			)
		);

		if ( ! $papro_activated ) {
			$get_pro = Helper_Functions::get_campaign_link( 'https://premiumaddons.com/pro', 'editor-page', 'wp-editor', 'get-pro' );

			$this->add_control(
				'endpoint_notice',
				array(
					'type'            => Controls_Manager::RAW_HTML,
					'raw'             => __( 'This option is available in Premium Addons Pro.', 'premium-addons-for-elementor' ) . '<a href="' . esc_url( $get_pro ) . '" target="_blank">' . __( 'Upgrade now!', 'premium-addons-for-elementor' ) . '</a>',
					'content_classes' => 'papro-upgrade-notice',
					'condition'       => array(
						'endpoint' => 'boards/',
					),
				)
			);

		}

		$this->add_control(
			'pins_onclick',
			array(
				'label'     => __( 'On Click', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::SELECT,
				'options'   => array(
					'lightbox' => __( 'Lightbox', 'premium-addons-for-elementor' ),
					'default'  => __( 'Redirect To Pinterest', 'premium-addons-for-elementor' ),
				),
				'default'   => 'default',
				'condition' => array(
					'endpoint'  => 'pins/',
					'show_feed' => 'yes',
				),
			)
		);

		$this->add_control(
			'boards_onclick',
			array(
				'label'     => __( 'On Click', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::SELECT,
				'options'   => array(
					'pins'    => __( 'Load Pins', 'premium-addons-for-elementor' ),
					'default' => __( 'Redirect To Pinterest', 'premium-addons-for-elementor' ),
				),
				'default'   => 'default',
				'condition' => array(
					'endpoint'  => 'boards/',
					'show_feed' => 'yes',
				),
			)
		);

		$this->add_control(
			'match_id',
			array(
				'label'       => __( 'Filter By ID', 'premium-addons-for-elementor' ),
				'type'        => Controls_Manager::TEXT,
				'label_block' => true,
				'description' => 'Enter the item ID you want to display, leave it empty to display all the available items.',
				'dynamic'     => array( 'active' => true ),
				'render_type' => 'template',
				'condition'   => array(
					'show_feed' => 'yes',
					'endpoint'  => 'pins/',
				),
			)
		);

		$this->add_control(
			'board_id',
			array(
				'label'              => __( 'Specific Board(s)', 'premium-addons-for-elementor' ),
				'type'               => Premium_Select::TYPE,
				'render_type'        => 'template',
				'label_block'        => true,
				'multiple'           => true,
				'default'            => array(),
				'frontend_available' => true,
				'condition'          => array(
					'show_feed' => 'yes',
					'match_id'  => '',
				),
			)
		);

		$this->add_control(
			'board_id_notice',
			array(
				'type'            => Controls_Manager::RAW_HTML,
				'raw'             => __( 'If you selected multiple boards, then you may need to increase the value added for Items Per Page option', 'premium-addons-for-elementor' ),
				'content_classes' => 'elementor-panel-alert elementor-panel-alert-info',
				'condition'       => array(
					'show_feed' => 'yes',
					'match_id'  => '',
				),
			)
		);

		$this->add_control(
			'sort',
			array(
				'label'       => __( 'Order By Date', 'premium-addons-for-elementor' ),
				'type'        => Controls_Manager::SELECT,
				'label_block' => true,
				'options'     => array(
					'reverse' => __( 'Descending', 'premium-addons-for-elementor' ),
					'default' => __( 'Ascending', 'premium-addons-for-elementor' ),
				),
				'default'     => 'reverse',
				'render_type' => 'template',
				'condition'   => array(
					'match_id'  => '',
					'show_feed' => 'yes',
				),
			)
		);

		$this->add_control(
			'exclude_id',
			array(
				'label'       => __( 'Exclude IDs', 'premium-addons-for-elementor' ),
				'type'        => Controls_Manager::TEXT,
				'label_block' => true,
				'description' => 'Enter the item IDs you want to ecxclude separated by ","',
				'dynamic'     => array( 'active' => true ),
				'render_type' => 'template',
				'condition'   => array(
					'match_id'  => '',
					'show_feed' => 'yes',
					'endpoint'  => 'pins/',
				),
			)
		);

		$this->add_responsive_control(
			'pins_per_board',
			array(
				'label'       => __( 'Pins Per Board', 'premium-addons-for-elementor' ),
				'description' => __( 'Set the number of pins', 'premium-addons-for-elementor' ),
				'type'        => Controls_Manager::NUMBER,
				'min'         => 1,
				'default'     => 4,
				'condition'   => array(
					'show_feed'      => 'yes',
					'endpoint'       => 'boards/',
					'boards_onclick' => 'pins',
				),
			)
		);

		$this->add_responsive_control(
			'no_of_posts',
			array(
				'label'       => __( 'Items Per Page', 'premium-addons-for-elementor' ),
				'description' => __( 'Set the number of items per page', 'premium-addons-for-elementor' ),
				'type'        => Controls_Manager::NUMBER,
				'min'         => 1,
				'default'     => 4,
				'condition'   => array(
					'show_feed' => 'yes',
				),
			)
		);

		$this->end_controls_section();
	}

	private function add_profile_controls() {

		$papro_activated = apply_filters( 'papro_activated', false );

		$this->start_controls_section(
			'pa_pinterest_profile_sec',
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
			do_action( 'pa_pinterest_profile_controls', $this );
		}

		$this->end_controls_section();
	}

	private function add_pin_settings_controls() {

		$this->start_controls_section(
			'pa_pinterest_pin_sec',
			array(
				'label'      => __( 'Pin Settings', 'premium-addons-for-elementor' ),
				'conditions' => $this->common_conds,
			)
		);

		$options = apply_filters(
			'pa_pinterest_layouts',
			array(
				'layouts' => array(
					'layout-1' => __( 'Card', 'premium-addons-for-elementor' ),
					'layout-2' => __( 'Banner (Pro)', 'premium-addons-for-elementor' ),
					'layout-3' => __( 'On Side (Pro)', 'premium-addons-for-elementor' ),
					'layout-4' => __( 'Slide (Pro)', 'premium-addons-for-elementor' ),
				),
			)
		);

		$this->add_control(
			'pin_layout',
			array(
				'label'        => __( 'Skin', 'premium-addons-for-elementor' ),
				'type'         => Controls_Manager::SELECT,
				'prefix_class' => 'premium-pinterest-feed__pin-',
				'render_type'  => 'template',
				'default'      => 'layout-1',
				'options'      => $options['layouts'],
			)
		);

		$this->add_responsive_control(
			'info_order',
			array(
				'label'     => __( 'Pin Info Order', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::CHOOSE,
				'toggle'    => false,
				'options'   => array(
					'0' => array(
						'title' => __( 'Before Pin', 'premium-addons-for-elementor' ),
						'icon'  => 'eicon-order-start',
					),
					'2' => array(
						'title' => __( 'After Pin', 'premium-addons-for-elementor' ),
						'icon'  => 'eicon-order-end',
					),
				),
				'default'   => '2',
				'selectors' => array(
					'{{WRAPPER}}.premium-pinterest-feed__pin-layout-1 .premium-pinterest-feed__pin-desc,
                {{WRAPPER}}.premium-pinterest-feed__pin-layout-3 .premium-pinterest-feed__pin-meta-wrapper' => 'order: {{VALUE}}',
				),
				'condition' => array(
					'pin_layout!' => array( 'layout-2', 'layout-4' ),
				),
			)
		);

		$this->add_control(
			'pin_settings_heading',
			array(
				'label'     => __( 'Display Options', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			)
		);

		$this->add_control(
			'pin_pinterest_icon',
			array(
				'label'     => __( 'Pinterest Icon', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::SWITCHER,
				'label_on'  => __( 'Show', 'premium-addons-for-elementor' ),
				'label_off' => __( 'Hide', 'premium-addons-for-elementor' ),
				'default'   => 'yes',
			)
		);

		$this->add_responsive_control(
			'pinterest_icon_size',
			array(
				'label'      => __( 'Icon Size', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'default'    => array(
					'size' => 25,
					'unit' => 'px',
				),
				'selectors'  => array(
					'{{WRAPPER}} .premium-pinterest-icon-pin svg'  => 'width: {{SIZE}}px; height: {{SIZE}}px;',
				),
				'condition'  => array(
					'pin_pinterest_icon' => 'yes',
				),
			)
		);

		$this->add_control(
			'pin_username',
			array(
				'label'     => __( 'Username', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::SWITCHER,
				'label_on'  => __( 'Show', 'premium-addons-for-elementor' ),
				'label_off' => __( 'Hide', 'premium-addons-for-elementor' ),
				'default'   => 'yes',
			)
		);

		$this->add_control(
			'pin_desc',
			array(
				'label'     => __( 'Description', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::SWITCHER,
				'label_on'  => __( 'Show', 'premium-addons-for-elementor' ),
				'label_off' => __( 'Hide', 'premium-addons-for-elementor' ),
				'default'   => 'yes',
			)
		);

		$this->add_control(
			'pin_desc_len',
			array(
				'label'     => __( 'Description Length (Word)', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::NUMBER,
				'condition' => array(
					'pin_desc' => 'yes',
				),
				'default'   => 10,
			)
		);

		$this->add_control(
			'pin_desc_postfix',
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
					'pin_desc'      => 'yes',
					'pin_desc_len!' => '',
				),
			)
		);

		$this->add_control(
			'pin_desc_postfix_txt',
			array(
				'label'     => __( 'Read More Text', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::TEXT,
				'default'   => __( 'Read More »', 'premium-addons-for-elementor' ),
				'condition' => array(
					'pin_desc'         => 'yes',
					'pin_desc_len!'    => '',
					'pin_desc_postfix' => 'link',
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
				'default'     => 'F j',
				'condition'   => array(
					'create_time' => 'yes',
				),
			)
		);

		$this->add_control(
			'share_pin',
			array(
				'label'     => __( 'Share Button', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::SWITCHER,
				'label_on'  => __( 'Show', 'premium-addons-for-elementor' ),
				'label_off' => __( 'Hide', 'premium-addons-for-elementor' ),
			)
		);

		$this->add_control(
			'img_sizes',
			array(
				'label'       => __( 'Image Size', 'premium-addons-for-elementor' ),
				'type'        => Controls_Manager::SELECT,
				'separator'   => 'before',
				'render_type' => 'template',
				'label_block' => true,
				'options'     => array(
					'150x150' => __( 'Thumbnail', 'premium-addons-for-elementor' ),
					'400x300' => __( 'Medium', 'premium-addons-for-elementor' ),
					'600x'    => __( 'Large', 'premium-addons-for-elementor' ),
					'1200x'   => __( 'Full', 'premium-addons-for-elementor' ),
				),
				'default'     => '1200x',
			)
		);

		$this->add_responsive_control(
			'pa_pin_img_height',
			array(
				'label'      => __( 'Image Height', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', 'custom' ),
				'range'      => array(
					'px' => array(
						'min' => 0,
						'max' => 1000,
					),
				),
				'selectors'  => array(
					'{{WRAPPER}} .premium-pinterest-feed__pin-media'  => 'height: {{SIZE}}{{UNIT}};',
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
					'{{WRAPPER}} .premium-pinterest-feed__pin-media img' => 'object-fit: {{VALUE}};',
				),
			)
		);

		$papro_activated = apply_filters( 'papro_activated', false );

		if ( $papro_activated ) {
			do_action( 'pa_pinterest_slide_align', $this );
		}

		$this->end_controls_section();

	}

	private function add_general_controls() {

		$this->start_controls_section(
			'pa_gen_section',
			array(
				'label'      => __( 'Pins General Settings', 'premium-addons-for-elementor' ),
				'conditions' => $this->common_conds,
			)
		);

		$this->add_control(
			'outer_layout',
			array(
				'label'        => __( 'Layout', 'premium-addons-for-elementor' ),
				'type'         => Controls_Manager::SELECT,
				'prefix_class' => 'premium-pinterest-feed__',
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
			'pa_pinterest_cols',
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
					'{{WRAPPER}} .premium-pinterest-feed__pin-outer-wrapper'  => 'width: calc( 100% / {{VALUE}} );',
				),
			)
		);

		$this->add_control(
			'loading_animation',
			array(
				'label'        => __( 'Loading Animation', 'premium-addons-for-elementor' ),
				'type'         => Controls_Manager::SELECT,
				'prefix_class' => 'premium-loading-animatin__slide-',
				'options'      => array(
					'none'  => __( 'None', 'premium-addons-for-elementor' ),
					'up'    => __( 'Slide Up', 'premium-addons-for-elementor' ),
					'down'  => __( 'Slide Down', 'premium-addons-for-elementor' ),
					'left'  => __( 'Slide Left', 'premium-addons-for-elementor' ),
					'right' => __( 'Slide Right', 'premium-addons-for-elementor' ),
				),
				'default'      => 'up',
				'conditions'   => array(
					'terms' => array(
						array(
							'name'     => 'carousel',
							'operator' => '!==',
							'value'    => 'yes',
						),
						array(
							'relation' => 'or',
							'terms'    => array(
								array(
									'terms' => array(
										array(
											'name'  => 'endpoint',
											'value' => 'pins/',
										),
										array(
											'name'  => 'load_more_btn',
											'value' => 'yes',
										),
									),
								),
								array(
									'terms' => array(
										array(
											'name'  => 'endpoint',
											'value' => 'boards/',
										),
										array(
											'name'  => 'boards_onclick',
											'value' => 'pins',
										),
									),
								),
							),
						),
					),
				),
			)
		);

		$this->add_responsive_control(
			'pa_pinterest_spacing',
			array(
				'label'      => __( 'Spacing', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'separator'  => 'before',
				'default'    => array(
					'size' => 2,
					'unit' => 'px',
				),
				'selectors'  => array(
					'{{WRAPPER}} .premium-pinterest-feed__pin-outer-wrapper'  => 'padding: {{SIZE}}px;',
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
					'endpoint'  => 'pins/',
					'carousel!' => 'yes',
				),
			)
		);

		$this->add_control(
			'no_per_load',
			array(
				'label'       => __( 'Pins On Load More', 'premium-addons-for-elementor' ),
				'description' => __( 'Number of pins to load', 'premium-addons-for-elementor' ),
				'type'        => Controls_Manager::NUMBER,
				'default'     => 3,
				'condition'   => array(
					'endpoint'      => 'pins/',
					'carousel!'     => 'yes',
					'load_more_btn' => 'yes',
				),
			)
		);

		$this->add_control(
			'more_btn_txt',
			array(
				'label'     => __( 'Load More Text', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::TEXT,
				'default'   => __( 'Load More', 'premium-addons-for-elementor' ),
				'condition' => array(
					'endpoint'      => 'pins/',
					'carousel!'     => 'yes',
					'load_more_btn' => 'yes',
				),
			)
		);

		$this->end_controls_section();
	}

	private function add_carousel_section() {

		$this->start_controls_section(
			'pa_pinterest_carousel_settings',
			array(
				'label'     => __( 'Carousel', 'premium-addons-for-elementor' ),
				'condition' => array(
					'show_feed' => 'yes',
					'endpoint'  => 'pins/',
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
					'carousel'          => 'yes',
					'pa_pinterest_cols' => '1',
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
					'{{WRAPPER}} .premium-pinterest-feed__pins-wrapper .slick-slide' => 'transition: all {{VALUE}}ms !important',
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
					'{{WRAPPER}} .premium-pinterest-feed__pins-wrapper a.carousel-arrow.carousel-next' => 'right: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .premium-pinterest-feed__pins-wrapper a.carousel-arrow.carousel-prev' => 'left: {{SIZE}}{{UNIT}};',
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
			'https://premiumaddons.com/docs/elementor-pinterest-feed/' => __( 'Getting started »', 'premium-addons-for-elementor' ),
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

		$papro_activated = apply_filters( 'papro_activated', false );

		$this->start_controls_section(
			'pa_feedbox_style_sec',
			array(
				'label'      => __( 'Feed Box', 'premium-addons-for-elementor' ),
				'tab'        => Controls_Manager::TAB_STYLE,
				'conditions' => $this->common_conds,
			)
		);

		if ( $papro_activated ) {
			do_action( 'pa_pinterest_dots_style', $this );
		}

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name'      => 'feed_box_shadow',
				'selector'  => '{{WRAPPER}} .premium-pinterest-feed__pin-wrapper',
				'condition' => array(
					'pin_layout!' => 'layout-4',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			array(
				'name'           => 'feed_box_background',
				'types'          => array( 'classic', 'gradient' ),
				'fields_options' => array(
					'background' => array(
						'default' => 'classic',
					),
					'color'      => array(
						'default' => '#eee',
					),
				),
				'selector'       => '{{WRAPPER}}:not(.premium-pinterest-feed__pin-layout-4) .premium-pinterest-feed__pin-wrapper, {{WRAPPER}}.premium-pinterest-feed__pin-layout-4 .premium-pinterest-feed__pin-meta-wrapper',
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'     => 'feed_box_border',
				'selector' => '{{WRAPPER}}:not(.premium-pinterest-feed__pin-layout-4) .premium-pinterest-feed__pin-wrapper, {{WRAPPER}}.premium-pinterest-feed__pin-layout-4 .premium-pinterest-feed__pin-meta-wrapper',
			)
		);

		$this->add_control(
			'feed_box_border_rad',
			array(
				'label'      => __( 'Border Radius', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', '%' ),
				'default'    => array(
					'size' => 15,
					'unit' => 'px',
				),
				'selectors'  => array(
					'{{WRAPPER}}:not(.premium-pinterest-feed__pin-layout-4) .premium-pinterest-feed__pin-wrapper, {{WRAPPER}}.premium-pinterest-feed__pin-layout-2 .premium-pinterest-feed__pin-media img, {{WRAPPER}}.premium-pinterest-feed__pin-layout-4 .premium-pinterest-feed__pin-meta-wrapper' => 'border-radius: {{SIZE}}{{UNIT}};',
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
					'{{WRAPPER}}:not(.premium-pinterest-feed__pin-layout-4) .premium-pinterest-feed__pin-wrapper, {{WRAPPER}}.premium-pinterest-feed__pin-layout-4 .premium-pinterest-feed__pin-meta-wrapper' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_section();
	}

	private function add_share_btn_style() {

		$icon_spacing = is_rtl() ? 'left' : 'right';

		$this->start_controls_section(
			'pa_pinterest_Sb_style',
			array(
				'label'     => __( 'Share Button', 'premium-addons-for-elementor' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => array(
					'share_pin' => 'yes',
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
				'global'   => array(
					'default' => Global_Typography::TYPOGRAPHY_PRIMARY,
				),
				'selector' => '{{WRAPPER}} .premium-pinterest-sharer',
			)
		);

		$this->add_control(
			'pa_share_btn_bg',
			array(
				'label'     => __( 'Background Color', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .premium-pinterest-share-container' => 'background-color: {{VALUE}};',
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
					'{{WRAPPER}} .premium-pinterest-sharer' => 'color: {{VALUE}};',
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
					'{{WRAPPER}} .premium-pinterest-share-button:hover .fa.custom-fa' => '-webkit-text-stroke-color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'pa_share_btn_color_hov',
			array(
				'label'     => __( 'Text Color', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .premium-pinterest-share-button:hover .premium-pinterest-sharer' => 'color: {{VALUE}};',
				),
			)
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'      => 'pa_share_btn_border',
				'selector'  => '{{WRAPPER}} .premium-pinterest-share-container',
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
					'{{WRAPPER}} .premium-pinterest-share-container' => 'border-radius: {{SIZE}}{{UNIT}};',
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
					'{{WRAPPER}} .premium-pinterest-share-container' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
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
					'{{WRAPPER}} .premium-pinterest-share-container' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_section();
	}

	private function add_share_links_style() {

		$icon_spacing = is_rtl() ? 'left' : 'right';

		$this->start_controls_section(
			'pa_pinterest_Sl_style',
			array(
				'label'     => __( 'Share Links', 'premium-addons-for-elementor' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => array(
					'share_pin' => 'yes',
				),
			)
		);

		$this->add_control(
			'pa_sl_menu_bg',
			array(
				'label'     => __( 'Menu Background Color', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .premium-pinterest-share-menu' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'pa_sl_typo',
				'global'   => array(
					'default' => Global_Typography::TYPOGRAPHY_PRIMARY,
				),
				'selector' => '{{WRAPPER}} .premium-pinterest-share-text',
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
					'{{WRAPPER}} .premium-pinterest-share-item i' => 'font-size: {{SIZE}}{{UNIT}};',
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
					'{{WRAPPER}} .premium-pinterest-share-menu' => 'border-radius: {{SIZE}}{{UNIT}};',
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
					'{{WRAPPER}} .premium-pinterest-share-item i' => 'margin-' . $icon_spacing . ': {{SIZE}}{{UNIT}};',
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
					'{{WRAPPER}} .premium-pinterest-share-item' => 'margin-top: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .premium-pinterest-share-item' => 'margin-bottom: {{SIZE}}{{UNIT}};',
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
					'{{WRAPPER}} .premium-pinterest-share-text' => 'color: {{VALUE}};',
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
					'{{WRAPPER}} .premium-pinterest-share-item:hover .premium-pinterest-share-text' => 'color: {{VALUE}};',
				),
			)
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();
	}

	private function add_container_style_controls() {

		$this->start_controls_section(
			'pa_feed_cont_style_sec',
			array(
				'label'      => __( 'Feeds Container', 'premium-addons-for-elementor' ),
				'tab'        => Controls_Manager::TAB_STYLE,
				'conditions' => $this->common_conds,
			)
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name'     => 'feed_cont_shadow',
				'selector' => '{{WRAPPER}} .premium-pinterest-feed__pins-wrapper',
			)
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			array(
				'name'     => 'feed_cont_background',
				'types'    => array( 'classic', 'gradient' ),
				'selector' => '{{WRAPPER}} .premium-pinterest-feed__pins-wrapper',
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'     => 'feed_cont_border',
				'selector' => '{{WRAPPER}} .premium-pinterest-feed__pins-wrapper',
			)
		);

		$this->add_control(
			'feed_cont_border_rad',
			array(
				'label'      => __( 'Border Radius', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-pinterest-feed__pins-wrapper' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
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
					'{{WRAPPER}} .premium-pinterest-feed__pins-wrapper' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
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
					'{{WRAPPER}} .premium-pinterest-feed__pins-wrapper' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_section();
	}

	private function add_image_style_controls() {

		$this->start_controls_section(
			'pa_pinterest_image_style_section',
			array(
				'label'     => __( 'Feed Image', 'premium-addons-for-elementor' ),
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
				'label'     => __( 'Image Overlay' ),
				'types'     => array( 'classic', 'gradient' ),
				'selector'  => '{{WRAPPER}} .premium-pinterest-feed__pin-meta-wrapper',
				'condition' => array(
					'pin_layout' => array( 'layout-2', 'layout-4' ),
				),
			)
		);

		$papro_activated = apply_filters( 'papro_activated', false );

		if ( $papro_activated ) {
			do_action( 'pa_image_hover_effects', $this );
		}

		$this->add_group_control(
			Group_Control_Css_Filter::get_type(),
			array(
				'name'     => 'css_filters',
				'selector' => '{{WRAPPER}} .premium-pinterest-feed__pin-media img, {{WRAPPER}} .premium-pinterest-feed__board-cover',
			)
		);

		$this->add_group_control(
			Group_Control_Css_Filter::get_type(),
			array(
				'name'     => 'hover_css_filters',
				'label'    => __( 'Hover CSS Filters', 'premium-addons-for-elementor' ),
				'selector' => '{{WRAPPER}} .premium-pinterest-feed__pin-wrapper:hover img, {{WRAPPER}} .premium-pinterest-feed__board-wrapper:hover .premium-pinterest-feed__board-cover',
			)
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name'      => 'feed_img_shadow',
				'separator' => 'before',
				'selector'  => '{{WRAPPER}} .premium-pinterest-feed__pin-media',
				'condition' => array(
					'endpoint'    => 'pins/',
					'pin_layout!' => 'layout-2',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'      => 'feed_img_border',
				'selector'  => '{{WRAPPER}} .premium-pinterest-feed__pin-media',
				'condition' => array(
					'endpoint'    => 'pins/',
					'pin_layout!' => 'layout-2',
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
					'{{WRAPPER}} .premium-pinterest-feed__pin-media' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}',
				),
				'condition'  => array(
					'endpoint'    => 'pins/',
					'pin_layout!' => 'layout-2',
				),
			)
		);

		$this->end_controls_section();
	}

	private function add_info_style_controls() {

		$this->start_controls_section(
			'pa_pinterest_info_style_section',
			array(
				'label'      => __( 'Feed Info', 'premium-addons-for-elementor' ),
				'tab'        => Controls_Manager::TAB_STYLE,
				'conditions' => $this->common_conds,
			)
		);

		$this->add_control(
			'username_heading',
			array(
				'label'     => __( 'Username', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::HEADING,
				'condition' => array(
					'pin_username' => 'yes',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'      => 'pin_username_typo',
				'selector'  => '{{WRAPPER}} .premium-pinterest-feed__pin-creator a',
				'condition' => array(
					'pin_username' => 'yes',
				),
			)
		);

		$this->add_control(
			'pin_username_color',
			array(
				'label'     => __( 'Color', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'global'    => array(
					'default' => Global_Colors::COLOR_SECONDARY,
				),
				'selectors' => array(
					'{{WRAPPER}} .premium-pinterest-feed__pin-creator a' => 'color: {{VALUE}};',
				),
				'condition' => array(
					'pin_username' => 'yes',
				),
			)
		);

		$this->add_responsive_control(
			'pin_username_margin',
			array(
				'label'      => __( 'Margin', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-pinterest-feed__pin-creator a' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
				'condition'  => array(
					'pin_username' => 'yes',
				),
			)
		);

		$this->add_control(
			'date_heading',
			array(
				'label'     => __( 'Date', 'premium-addons-for-elementor' ),
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
				'selector'  => '{{WRAPPER}} .premium-pinterest-feed__created-at',
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
				'global'    => array(
					'default' => Global_Colors::COLOR_SECONDARY,
				),
				'selectors' => array(
					'{{WRAPPER}} .premium-pinterest-feed__created-at' => 'color: {{VALUE}};',
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
					'{{WRAPPER}}:not(.premium-pinterest-feed__pin-layout-4) .premium-pinterest-feed__pin-meta-wrapper' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					'{{WRAPPER}}.premium-pinterest-feed__pin-layout-4 .premium-pinterest-feed__meta' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
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
							'name'  => 'pin_username',
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
				'label'     => __( 'Description', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
				'condition' => array(
					'pin_desc' => 'yes',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'      => 'pin_desc_typo',
				'selector'  => '{{WRAPPER}} .premium-pinterest-feed__pin-desc',
				'condition' => array(
					'pin_desc' => 'yes',
				),
			)
		);

		$this->add_control(
			'pin_desc_color',
			array(
				'label'     => __( 'Color', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .premium-pinterest-feed__pin-desc' => 'color: {{VALUE}};',
				),
				'condition' => array(
					'pin_desc' => 'yes',
				),
			)
		);

		// $this->add_responsive_control(
		// 'pin_desc_padding',
		// array(
		// 'label'      => __( 'Padding', 'premium-addons-for-elementor' ),
		// 'type'       => Controls_Manager::DIMENSIONS,
		// 'size_units' => array( 'px', 'em' ),
		// 'selectors'  => array(
		// '{{WRAPPER}} .premium-pinterest-feed__pin-desc' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
		// ),
		// 'condition'  => array(
		// 'pin_desc' => 'yes',
		// ),
		// )
		// );

		$this->add_responsive_control(
			'pin_desc_margin',
			array(
				'label'      => __( 'Margin', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-pinterest-feed__pin-desc' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
				'default'    => array(
					'top'    => 15,
					'right'  => 15,
					'bottom' => 15,
					'left'   => 15,
					'unit'   => 'px',
				),
				'condition'  => array(
					'pin_desc'    => 'yes',
					'pin_layout!' => 'layout-4',
				),
			)
		);

		$this->add_control(
			'read_more_heading',
			array(
				'label'     => __( 'Read More', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
				'condition' => array(
					'pin_desc'         => 'yes',
					'pin_desc_len!'    => '',
					'pin_desc_postfix' => 'link',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'      => 'read_more_typo',
				'selector'  => '{{WRAPPER}} .premium-pinterest-feed__pin-desc .premium-read-more',
				'condition' => array(
					'pin_desc'         => 'yes',
					'pin_desc_len!'    => '',
					'pin_desc_postfix' => 'link',
				),
			)
		);

		$this->add_control(
			'read_more_color',
			array(
				'label'     => __( 'Color', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .premium-pinterest-feed__pin-desc .premium-read-more' => 'color: {{VALUE}};',
				),
				'condition' => array(
					'pin_desc'         => 'yes',
					'pin_desc_len!'    => '',
					'pin_desc_postfix' => 'link',
				),
			)
		);

		$this->add_control(
			'read_more_color_hov',
			array(
				'label'     => __( 'Hover Color', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .premium-pinterest-feed__pin-desc .premium-read-more:hover' => 'color: {{VALUE}};',
				),
				'condition' => array(
					'pin_desc'         => 'yes',
					'pin_desc_len!'    => '',
					'pin_desc_postfix' => 'link',
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
					'{{WRAPPER}} .premium-pinterest-feed__pin-desc .premium-read-more' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
				'condition'  => array(
					'pin_desc'         => 'yes',
					'pin_desc_len!'    => '',
					'pin_desc_postfix' => 'link',
				),
			)
		);

		$this->add_control(
			'pinterest_icon_heading',
			array(
				'label'     => __( 'Pinterest Icon', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
				'condition' => array(
					'pin_pinterest_icon' => 'yes',
				),
			)
		);

		$this->add_control(
			'pinterest_icon_color',
			array(
				'label'     => __( 'Color', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .premium-pinterest-icon-pin svg *'  => 'fill: {{VALUE}};',
				),
				'condition' => array(
					'pin_pinterest_icon' => 'yes',
				),
			)
		);

		$this->add_control(
			'pinterest_icon_back',
			array(
				'label'     => __( 'Background Color', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .premium-pinterest-icon-pin'  => 'background-color: {{VALUE}};',
				),
				'condition' => array(
					'pin_pinterest_icon' => 'yes',
				),
			)
		);

		$this->add_control(
			'pinterest_icon_radius',
			array(
				'label'      => __( 'Border Radius', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-pinterest-icon-pin' => 'border-radius: {{SIZE}}{{UNIT}};',
				),
				'condition'  => array(
					'pin_pinterest_icon' => 'yes',
				),
			)
		);

		$this->add_responsive_control(
			'pinterest_icon_padding',
			array(
				'label'      => __( 'Padding', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-pinterest-icon-pin' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
				'condition'  => array(
					'pin_pinterest_icon' => 'yes',
				),
			)
		);

		$this->add_responsive_control(
			'pinterest_icon_margin',
			array(
				'label'      => __( 'Margin', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-pinterest-icon-pin' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
				'condition'  => array(
					'pin_pinterest_icon' => 'yes',
				),
			)
		);

		$this->end_controls_section();
	}

	private function add_cta_style() {

		$this->start_controls_section(
			'board_trigger_style',
			array(
				'label'      => __( 'CTA Button', 'premium-addons-for-elementor' ),
				'tab'        => Controls_Manager::TAB_STYLE,
				'conditions' => array(
					'terms' => array(
						array(
							'name'  => 'show_feed',
							'value' => 'yes',
						),
						array(
							'relation' => 'or',
							'terms'    => array(
								array(
									'terms' => array(
										array(
											'name'  => 'endpoint',
											'value' => 'pins/',
										),
										array(
											'name'  => 'load_more_btn',
											'value' => 'yes',
										),
									),
								),
								array(
									'terms' => array(
										array(
											'name'  => 'endpoint',
											'value' => 'boards/',
										),
										array(
											'name'  => 'boards_onclick',
											'value' => 'pins',
										),
									),
								),
							),
						),
					),
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'cta_typography',
				'selector' => '{{WRAPPER}} .premium-pinterest-feed__board-trigger, {{WRAPPER}} .premium-pinterest-feed__load-more-btn',
				'global'   => array(
					'default' => Global_Typography::TYPOGRAPHY_ACCENT,
				),
			)
		);

		$this->add_responsive_control(
			'cta_padding',
			array(
				'label'      => __( 'Padding', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-pinterest-feed__board-trigger, {{WRAPPER}} .premium-pinterest-feed__load-more-btn' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'cta_margin',
			array(
				'label'      => __( 'Margin', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-pinterest-feed__board-trigger, {{WRAPPER}} .premium-pinterest-feed__load-more-btn' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->start_controls_tabs( 'cta_style_tabs' );

		$this->start_controls_tab(
			'cta_style_tab_normal',
			array(
				'label' => __( 'Normal', 'premium-addons-for-elementor' ),
			)
		);

		$this->add_control(
			'cta_color',
			array(
				'label'     => __( 'Color', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .premium-pinterest-feed__board-trigger, {{WRAPPER}} .premium-pinterest-feed__load-more-btn' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			array(
				'name'     => 'cta_background',
				'types'    => array( 'classic', 'gradient' ),
				'selector' => '{{WRAPPER}} .premium-pinterest-feed__board-trigger, {{WRAPPER}} .premium-pinterest-feed__load-more-btn',
			)
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name'     => 'cta_shadow',
				'selector' => '{{WRAPPER}} .premium-pinterest-feed__board-trigger, {{WRAPPER}} .premium-pinterest-feed__load-more-btn',
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'     => 'cta_border',
				'selector' => '{{WRAPPER}} .premium-pinterest-feed__board-trigger, {{WRAPPER}} .premium-pinterest-feed__load-more-btn',
			)
		);

		$this->add_control(
			'cta_radius',
			array(
				'label'      => __( 'Border Radius', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-pinterest-feed__board-trigger, {{WRAPPER}} .premium-pinterest-feed__load-more-btn' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}',
				),
			)
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'cta_style_tab_hover',
			array(
				'label' => __( 'Hover', 'premium-addons-for-elementor' ),
			)
		);

		$this->add_control(
			'cta_color_hover',
			array(
				'label'     => __( 'Color', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .premium-pinterest-feed__board-trigger:hover, {{WRAPPER}} .premium-pinterest-feed__load-more-btn:hover' => 'color: {{VALUE}}',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			array(
				'name'     => 'cta_background_hover',
				'types'    => array( 'classic', 'gradient' ),
				'selector' => '{{WRAPPER}} .premium-pinterest-feed__board-trigger:hover, {{WRAPPER}} .premium-pinterest-feed__load-more-btn:hover',
			)
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name'     => 'cta_shadow_hover',
				'selector' => '{{WRAPPER}} .premium-pinterest-feed__board-trigger:hover, {{WRAPPER}} .premium-pinterest-feed__load-more-btn:hover',
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'     => 'cta_border_hover',
				'selector' => '{{WRAPPER}} .premium-pinterest-feed__board-trigger:hover, {{WRAPPER}} .premium-pinterest-feed__load-more-btn:hover',
			)
		);

		$this->add_control(
			'cta_radius_hover',
			array(
				'label'      => __( 'Border Radius', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%', 'em' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-pinterest-feed__board-trigger:hover, {{WRAPPER}} .premium-pinterest-feed__load-more-btn:hover' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}',
				),
			)
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

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
					'show_feed'     => 'yes',
					'endpoint'      => 'pins/',
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

		$this->add_responsive_control(
			'dots_margin',
			array(
				'label'      => __( 'Margin', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em' ),
				'selectors'  => array(
					'{{WRAPPER}} ul.slick-dots' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
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
					'show_feed'       => 'yes',
					'endpoint'        => 'pins/',
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
					'{{WRAPPER}} .premium-pinterest-feed__pins-wrapper .slick-arrow' => 'color: {{VALUE}};',
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
					'{{WRAPPER}} .premium-pinterest-feed__pins-wrapper .slick-arrow i' => 'font-size: {{SIZE}}{{UNIT}};',
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
					'{{WRAPPER}} .premium-pinterest-feed__pins-wrapper .slick-arrow' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'border_radius',
			array(
				'label'      => __( 'Border Radius', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-pinterest-feed__pins-wrapper .slick-arrow' => 'border-radius: {{SIZE}}{{UNIT}};',
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
					'{{WRAPPER}} .premium-pinterest-feed__pins-wrapper .slick-arrow' => 'padding: {{SIZE}}{{UNIT}};',
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

		$papro_activated = apply_filters( 'papro_activated', false );

		if ( ! $papro_activated || version_compare( PREMIUM_PRO_ADDONS_VERSION, '2.9.2', '<' ) ) {

			$settings['image_hover_effect'] = '';

			if ( 'layout-1' !== $settings['pin_layout'] || 'boards/' === $settings['endpoint'] || 'yes' === $settings['profile_header'] ) {

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

		$settings['access_token'] = apply_filters( 'pa_pinterest_token_' . $widget_id, $settings['access_token'] );

		$access_token = $settings['access_token'];

		if ( empty( $access_token ) ) {
			?>
			<div class="premium-error-notice">
				<?php echo esc_html( __( 'Please fill the required fields: Access Token', 'premium-addons-for-elementor' ) ); ?>
			</div>
			<?php
			return;
		}

		$show_feed    = 'yes' === $settings['show_feed'];
		$show_profile = 'yes' === $settings['profile_header'];

		if ( $show_feed ) {

			$board_query = 'boards/' === $settings['endpoint'];
			$onclick     = $board_query ? $settings['boards_onclick'] : $settings['pins_onclick'];
			$load_pins   = $board_query && 'pins' == $onclick;

			$pinterest_settings = array(
				'query'   => $settings['endpoint'],
				'onClick' => $onclick,
			);

			if ( $board_query ) {
				$boards_feed = get_pinterest_data( $widget_id, $settings, 'boards/' );
				$carousel    = false;
			} else {

				$pinterest_feed = get_pinterest_data( $widget_id, $settings, 'pins/' );

				$carousel = 'yes' === $settings['carousel'];

				$load_more_btn = ! $carousel && 'yes' === $settings['load_more_btn'];

				if ( $carousel ) {

					$carousel_settings = array(
						'slidesToScroll'     => $settings['slides_to_scroll'],
						'slidesToShow'       => empty( $settings['pa_pinterest_cols'] ) ? 4 : $settings['pa_pinterest_cols'],
						'slidesToShowTab'    => isset( $settings['pa_pinterest_cols_tablet'] ) ? $settings['pa_pinterest_cols_tablet'] : 1,
						'slidesToShowMobile' => isset( $settings['pa_pinterest_cols_mobile'] ) ? $settings['pa_pinterest_cols_mobile'] : 1,
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
					$load_more_count                     = empty( $settings['no_per_load'] ) ? 3 : $settings['no_per_load'];
					$pinterest_settings['loadMore']      = true;
					$pinterest_settings['loadMoreCount'] = empty( $settings['no_per_load'] ) ? 3 : $settings['no_per_load'];

					$this->add_render_attribute( 'outer_container', 'data-pa-load-bookmark', $settings['pa_pinterest_cols'] );
				}
			}

			if ( ! $board_query || $load_pins ) {

				$pinterest_settings['layout']    = $settings['outer_layout'];
				$pinterest_settings['pinLayout'] = $settings['pin_layout'];
			}

			$pinterest_settings['carousel'] = $carousel;

			$this->add_render_attribute( 'pins_container', 'class', 'premium-pinterest-feed__pins-wrapper' );

			$this->add_render_attribute( 'outer_container', 'data-pa-pinterest-settings', wp_json_encode( $pinterest_settings ) );
		}

		$this->add_render_attribute( 'outer_container', 'class', 'premium-pinterest-feed__outer-wrapper' );

		?>
		<div <?php echo wp_kses_post( $this->get_render_attribute_string( 'outer_container' ) ); ?>>
			<?php
			if ( $show_profile ) {
				?>
					<div class="premium-pinterest-feed__profile-header"><?php $this->render_user_profile( $widget_id, $settings ); ?></div>
				<?php
			}

			if ( $board_query ) {
				if ( 'pins' === $onclick ) {
					?>
					<div class="premium-pinterest-feed__board-trigger-wrapper">
						<a type="button" data-role="none" role="button" class="premium-pinterest-feed__board-trigger">Boards</a>
					</div>
					<?php
				}

				$this->render_boards( $boards_feed, $settings, $widget_id );
			}

			if ( $show_feed && ! $board_query ) {
				$this->render_pins( $pinterest_feed, $settings );
			}

			if ( ! $board_query && $load_more_btn ) {
				?>
				<div class="premium-pinterest-feed__load-more-wrapper">
					<a type="button" data-role="none" role="button" class="premium-pinterest-feed__load-more-btn"><?php echo esc_html( $settings['more_btn_txt'] ); ?></a>
				</div>
				<?php
			}

			?>
		</div>
		<?php

		if ( $show_feed && Plugin::instance()->editor->is_edit_mode() ) {

			if ( 'masonry' === $settings['outer_layout'] && 'yes' !== $settings['carousel'] ) {

				$this->render_editor_script();
			}
		}
	}

	private function render_boards( $boards_feed, $settings, $widget_id ) {

		$show_desc        = 'yes' === $settings['board_desc'];
		$pin_count        = 'yes' === $settings['board_pin_count'];
		$board_layout     = $settings['board_layout'];
		$onclick_redirect = 'default' === $settings['boards_onclick'];

		?>
			<div class="premium-pinterest-feed__boards-wrapper">
				<?php

				if ( 'reverse' === $settings['sort'] ) {
					$boards_feed = array_reverse( $boards_feed );
				}

				foreach ( $boards_feed as $index => $feed ) {

					if ( 1 < count( $settings['board_id'] ) && ! in_array( $feed['id'], $settings['board_id'], true ) ) {
						continue;
					}

					$this->add_render_attribute(
						'board_wrap' . $feed['id'],
						array(
							'class'         => 'premium-pinterest-feed__board-wrapper',
							'data-board-id' => $feed['id'],
						)
					);

					$this->add_render_attribute(
						'board_cover' . $feed['id'],
						array(
							'class' => array( 'premium-pinterest-feed__board-cover', 'premium-hover-effects__' . $settings['image_hover_effect'] ),
						)
					);

					?>
					<div <?php echo wp_kses_post( $this->get_render_attribute_string( 'board_wrap' . $feed['id'] ) ); ?>>

						<?php if ( $onclick_redirect ) : ?>
							<a class="premium-pinterest-feed__board-link" target="_blank" href="https://www.pinterest.com/<?php echo esc_attr( $feed['owner']['username'] ); ?>/_saved/"></a>
						<?php endif; ?>

						<div class="premium-pinterest-feed__cover_wrap">
							<div <?php echo wp_kses_post( $this->get_render_attribute_string( 'board_cover' . $feed['id'] ) ); ?>>
								<?php
								if ( 'layout-cover' === $board_layout ) {
									?>
										<img src="<?php echo esc_url( $feed['media']['image_cover_url'] ); ?>" alt="Cover Image">
									<?php
								} elseif ( 'layout-2' === $board_layout ) {
									foreach ( $feed['media']['pin_thumbnail_urls'] as $id => $url ) {
										?>
										<img src="<?php echo esc_url( $url ); ?>" alt="Cover Image">
										<?php
									}
								} else {
									$col_left = $feed['media']['pin_thumbnail_urls'][0];

									array_pop( $feed['media']['pin_thumbnail_urls'] );

									$col_right = array_slice( $feed['media']['pin_thumbnail_urls'], 1 );
									?>
									<img src="<?php echo esc_url( $col_left ); ?>" alt="Cover Image">
									<div class="premium-cover-divider">
									<?php
									foreach ( $col_right as $id => $url ) {
										?>
										<img src="<?php echo esc_url( $url ); ?>" alt="Cover Image">
										<?php
									}
									?>
									</div>
									<?php
								}
								?>
							</div>
						</div>

						<span class="premium-pinterest-feed__board-title"><?php echo esc_html( $feed['name'] ); ?></span>

						<?php if ( $show_desc && ! is_null( $feed['description'] ) && ! empty( $feed['description'] ) ) : ?>
							<span class="premium-pinterest-feed__board-desc">
								<?php $this->render_feed_desc( $feed, $settings, 'board' ); ?>
							</span>
						<?php endif; ?>

						<?php if ( $pin_count ) : ?>
							<span class="premium-pinterest-feed__board-pins-number"><?php echo esc_html( $feed['pin_count'] ); ?> Pins</span>
						<?php endif; ?>

						<?php if ( 'yes' === $settings['board_pinterest_icon'] ) : ?>
							<?php $this->render_pinterest_icon( 'board' ); ?>
						<?php endif; ?>


					</div>
					<?php

					if ( ! $onclick_redirect ) {

						$pinterest_feed = get_board_pins( $widget_id, $settings, $feed['id'] );

						$this->add_render_attribute(
							'board_content_container' . $feed['id'],
							array(
								'class' => array( 'premium-display-none', 'premium-pinterest-feed__content-wrapper', 'premium-smart-listing__slide-up' ),
								'id'    => 'premium-board-content-' . $feed['id'],
							)
						);
						?>
							<div <?php echo wp_kses_post( $this->get_render_attribute_string( 'board_content_container' . $feed['id'] ) ); ?>>
						<?php
						$this->render_pins( $pinterest_feed, $settings, false );
						?>
						</div>
						<?php

					}
				}
				?>
			</div>
		<?php
	}

	/**
	 * Renders Pins.
	 *
	 * @param array  $pinterest_feed  pins feed.
	 * @param array  $settings  widget_settings.
	 * @param string $default  false when rendering board pins.
	 */
	private function render_pins( $pinterest_feed, $settings, $default = true ) {

		$pin_layout    = $settings['pin_layout'];
		$board_query   = 'boards/' === $settings['endpoint'];
		$load_more_btn = 'yes' !== $settings['carousel'] && 'yes' === $settings['load_more_btn'];
		$exclude_arr   = array();
		$lighbox       = 'lightbox' === $settings['pins_onclick'];
		$pin_settings  = array(
			'pinterest_icon' => 'yes' === $settings['pin_pinterest_icon'],
			'username'       => 'yes' === $settings['pin_username'],
			'desc'           => 'yes' === $settings['pin_desc'],
			'date'           => 'yes' === $settings['create_time'],
			'share'          => 'yes' === $settings['share_pin'],
		);

		$load_more_count = $load_more_btn && empty( $settings['no_per_load'] ) ? 3 : $settings['no_per_load'];

		if ( $default && empty( $settings['match_id'] ) ) {

			if ( 'reverse' === $settings['sort'] ) {
				$pinterest_feed = array_reverse( $pinterest_feed );
			}

			if ( ! empty( $settings['exclude_id'] ) ) {
				$exclude_arr = explode( ',', $settings['exclude_id'] );
			}
		}

		?>
			<div <?php echo wp_kses_post( $this->get_render_attribute_string( 'pins_container' ) ); ?>>
				<?php
				foreach ( $pinterest_feed as $index => $feed ) {

					if ( $default && ! $board_query && count( $exclude_arr ) && in_array( $feed['id'], $exclude_arr, true ) ) {
						continue;
					}

					if ( $default && 1 < count( $settings['board_id'] ) && ! in_array( $feed['board_id'], $settings['board_id'], true ) ) {
						continue;
					}

					$this->add_render_attribute( 'pin_outer_container' . $index, 'class', 'premium-pinterest-feed__pin-outer-wrapper' );

					if ( $default && $load_more_btn && $index >= $settings['pa_pinterest_cols'] ) {
						$this->add_render_attribute( 'pin_outer_container' . $index, 'class', 'premium-display-none' );
					}

					?>
					<div <?php echo wp_kses_post( $this->get_render_attribute_string( 'pin_outer_container' . $index ) ); ?>>
						<div class="premium-pinterest-feed__pin-wrapper">
						<?php if ( 'layout-1' === $pin_layout ) { ?>
							<div class="premium-pinterest-feed__pin-meta-wrapper">
								<?php
								if ( $pin_settings['username'] || $pin_settings['date'] ) {
									$this->get_pin_meta( $settings, $pin_settings, $feed );
								}
								?>

								<?php if ( $pin_settings['pinterest_icon'] ) : ?>
									<?php $this->render_pinterest_icon( 'pin' ); ?>
								<?php endif; ?>
							</div>
							<?php if ( $pin_settings['desc'] ) : ?>
								<div class="premium-pinterest-feed__pin-desc"><?php $this->render_feed_desc( $feed, $settings ); ?> </div>
							<?php endif; ?>

							<?php
								$href = ! $lighbox ? 'https://www.pinterest.com/pin/' . $feed['id'] : $this->render_pin_image( $feed['media'], $settings['img_sizes'], '', '', '', true );

								$this->add_render_attribute(
									'pin_link' . $feed['id'],
									array(
										'href'  => $href,
										'class' => 'premium-pinterest-feed__overlay',
									)
								);

							if ( $lighbox ) {
								$this->add_render_attribute(
									'pin_link' . $feed['id'],
									array(
										'data-elementor-open-lightbox' => 'yes',
										'data-elementor-lightbox-slideshow' => 1 < count( $pinterest_feed ) ? $this->get_id() : false,
									)
								);
							} else {
								$this->add_render_attribute( 'pin_link' . $feed['id'], 'target', '_blank' );
							}
							?>
							<div class="premium-pinterest-feed__pin-media">
							<a <?php echo wp_kses_post( $this->get_render_attribute_string( 'pin_link' . $feed['id'] ) ); ?> ></a>
								<?php $this->render_pin_image( $feed['media'], $settings['img_sizes'], $feed['alt_text'], $feed['title'], $settings['image_hover_effect'] ); ?>
							</div>

							<?php
							if ( $pin_settings['share'] ) {
								$this->render_share_button( 'https://www.pinterest.com/pin/' . $feed['id'] );
							}
							?>
						<?php } elseif ( 'layout-2' === $pin_layout ) { ?>
							<div class="premium-pinterest-feed__pin-meta-wrapper">
								<div class="premium-pinterest-feed__pin-inner-meta">
									<?php
									if ( $pin_settings['username'] || $pin_settings['date'] ) {
										$this->get_pin_meta( $settings, $pin_settings, $feed );
									}
									?>

									<?php if ( $pin_settings['pinterest_icon'] ) : ?>
										<?php $this->render_pinterest_icon( 'pin' ); ?>
									<?php endif; ?>
								</div>

								<?php if ( $pin_settings['desc'] ) : ?>
									<div class="premium-pinterest-feed__pin-desc"><?php $this->render_feed_desc( $feed, $settings ); ?> </div>
								<?php endif; ?>

								<?php
								if ( $pin_settings['share'] ) {
									$this->render_share_button( 'https://www.pinterest.com/pin/' . $feed['id'] );
								}
								?>
							</div>

							<?php
								$href = ! $lighbox ? 'https://www.pinterest.com/pin/' . $feed['id'] : $this->render_pin_image( $feed['media'], $settings['img_sizes'], '', '', '', true );

								$this->add_render_attribute(
									'pin_link' . $feed['id'],
									array(
										'href'  => $href,
										'class' => 'premium-pinterest-feed__overlay',
									)
								);

							if ( $lighbox ) {
								$this->add_render_attribute(
									'pin_link' . $feed['id'],
									array(
										'data-elementor-open-lightbox' => 'yes',
										'data-elementor-lightbox-slideshow' => 1 < count( $pinterest_feed ) ? $this->get_id() : false,
									)
								);
							} else {
								$this->add_render_attribute( 'pin_link' . $feed['id'], 'target', '_blank' );
							}
							?>
							<a <?php echo wp_kses_post( $this->get_render_attribute_string( 'pin_link' . $feed['id'] ) ); ?> ></a>

							<div class="premium-pinterest-feed__pin-media">
								<?php $this->render_pin_image( $feed['media'], $settings['img_sizes'], $feed['alt_text'], $feed['title'], $settings['image_hover_effect'] ); ?>
							</div>
						<?php } elseif ( 'layout-3' === $pin_layout ) { ?>

							<div class="premium-pinterest-feed__pin-meta-wrapper">

								<?php if ( $pin_settings['pinterest_icon'] ) : ?>
									<?php $this->render_pinterest_icon( 'pin' ); ?>
								<?php endif; ?>

								<?php if ( $pin_settings['desc'] ) : ?>
									<div class="premium-pinterest-feed__pin-desc"><?php $this->render_feed_desc( $feed, $settings ); ?> </div>
								<?php endif; ?>

								<?php
								if ( $pin_settings['username'] || $pin_settings['date'] ) {
									$this->get_pin_meta( $settings, $pin_settings, $feed );
								}

								if ( $pin_settings['share'] ) {
									$this->render_share_button( 'https://www.pinterest.com/pin/' . $feed['id'] );
								}
								?>
							</div>

							<?php
								$href = ! $lighbox ? 'https://www.pinterest.com/pin/' . $feed['id'] : $this->render_pin_image( $feed['media'], $settings['img_sizes'], '', '', '', true );

								$this->add_render_attribute(
									'pin_link' . $feed['id'],
									array(
										'href'  => $href,
										'class' => 'premium-pinterest-feed__overlay',
									)
								);

							if ( $lighbox ) {
								$this->add_render_attribute(
									'pin_link' . $feed['id'],
									array(
										'data-elementor-open-lightbox' => 'yes',
										'data-elementor-lightbox-slideshow' => 1 < count( $pinterest_feed ) ? $this->get_id() : false,
									)
								);
							} else {
								$this->add_render_attribute( 'pin_link' . $feed['id'], 'target', '_blank' );
							}
							?>
							<div class="premium-pinterest-feed__pin-media">
								<a <?php echo wp_kses_post( $this->get_render_attribute_string( 'pin_link' . $feed['id'] ) ); ?> ></a>
								<?php $this->render_pin_image( $feed['media'], $settings['img_sizes'], $feed['alt_text'], $feed['title'], $settings['image_hover_effect'] ); ?>
							</div>
						<?php } else { ?>
							<div class="premium-pinterest-feed__pin-media">
								<?php $this->render_pin_image( $feed['media'], $settings['img_sizes'], $feed['alt_text'], $feed['title'], $settings['image_hover_effect'] ); ?>
							</div>

							<?php if ( $pin_settings['pinterest_icon'] ) : ?>
								<?php $this->render_pinterest_icon( 'pin' ); ?>
							<?php endif; ?>

							<div class="premium-pinterest-feed__pin-meta-wrapper">

								<?php do_action( 'pa_pinterest_render_dots' ); ?>

								<?php if ( $pin_settings['desc'] ) : ?>
									<div class="premium-pinterest-feed__pin-desc"><?php $this->render_feed_desc( $feed, $settings ); ?> </div>
								<?php endif; ?>

								<?php
								if ( $pin_settings['username'] || $pin_settings['date'] ) {
									$this->get_pin_meta( $settings, $pin_settings, $feed );
								}
								?>
								<?php
								if ( $pin_settings['share'] ) {
									$this->render_share_button( 'https://www.pinterest.com/pin/' . $feed['id'] );
								}
								?>
							</div>

							<?php
								$href = ! $lighbox ? 'https://www.pinterest.com/pin/' . $feed['id'] : $this->render_pin_image( $feed['media'], $settings['img_sizes'], '', '', '', true );

								$this->add_render_attribute(
									'pin_link' . $feed['id'],
									array(
										'href'  => $href,
										'class' => 'premium-pinterest-feed__overlay',
									)
								);

							if ( $lighbox ) {
								$this->add_render_attribute(
									'pin_link' . $feed['id'],
									array(
										'data-elementor-open-lightbox' => 'yes',
										'data-elementor-lightbox-slideshow' => 1 < count( $pinterest_feed ) ? $this->get_id() : false,
									)
								);
							} else {
								$this->add_render_attribute( 'pin_link' . $feed['id'], 'target', '_blank' );
							}
							?>
							<a <?php echo wp_kses_post( $this->get_render_attribute_string( 'pin_link' . $feed['id'] ) ); ?> ></a>

						<?php } ?>
						</div>
					</div>
					<?php
				}
				?>
			</div>
		<?php
	}

	private function render_user_profile( $id, $settings ) {

		$profile_data    = get_profile_data( $id, $settings );
		$show_counts     = 'yes' === $settings['following_count'] || 'yes' === $settings['follower_count'] || 'yes' === $settings['view_count'];
		$show_desc       = 'yes' === $settings['bio_description'];
		$show_follow_btn = 'yes' === $settings['follow_button'];
		$is_inline       = 'row' === $settings['profile_basic_display'];

		if ( $show_follow_btn ) {
			$follow_url = sprintf( 'https://www.pinterest.com/%s/', $profile_data['username'] );
		}

		?>
			<div class="premium-pinterest-feed__user-info-wrapper">
				<div class="premium-pinterest-feed__user-info">
					<div class="premium-pinterest-feed__avatar">
						<a href="https://www.pinterest.com/<?php echo esc_html( $profile_data['username'] ); ?>" target="_blank">
							<img src="<?php echo esc_url( $profile_data['profile_image'] ); ?>" alt="" srcset="">
						</a>
					</div>
					<span class="premium-pinterest-feed__username">
						<a href="https://www.pinterest.com/<?php echo esc_html( $profile_data['username'] ); ?>" target="_blank"> <?php echo esc_html( $profile_data['username'] ); ?> </a>
					</span>
				</div>
				<?php
				if ( $show_follow_btn && $is_inline ) {
					?>
					<a class="premium-pinterest-feed__follow-button" href="<?php echo esc_url( $follow_url ); ?>" target="_blank">
						<?php $this->render_pinterest_icon( 'follow' ); ?>
						<span class="premium-pinterest-feed__follow-text"><?php echo __( 'Follow', 'premium-addons-for-elementor' ); ?></span>
					</a>

				<?php } ?>
			</div>

			<?php if ( $show_counts ) : ?>
				<div class="premium-pinterest-feed__profile-counts"> <?php $this->get_pin_counters( $settings, $profile_data ); ?></div>
			<?php endif; ?>
			<?php if ( $show_desc ) : ?>
				<div class="premium-pinterest-feed__profile-desc"><?php echo esc_html( $profile_data['about'] ); ?></div>
			<?php endif; ?>
			<?php
			if ( $show_follow_btn && ! $is_inline ) {

				?>
					<a class="premium-pinterest-feed__follow-button" href="<?php echo esc_url( $follow_url ); ?>" target="_blank">
						<?php $this->render_pinterest_icon( 'follow' ); ?>
						<span class="premium-pinterest-feed__follow-text"><?php echo __( 'Follow', 'premium-addons-for-elementor' ); ?></span>
					</a>

				<?php
			}
			?>
		<?php

	}

	/**
	 * Render pin's image.
	 *
	 * @access private
	 * @since
	 *
	 * @param array  $media  pin feed media array.
	 * @param string $size  media size.
	 * @param string $alt   alt text.
	 * @param string $title pin title.
	 * @param string $hover pin image hover effect.
	 */
	private function render_pin_image( $media, $size, $alt, $title, $hover, $return_url = false ) {

		$type = $media['media_type'];

		switch ( $type ) {

			case 'image':
			case 'video':
				$url = $media['images'][ $size ]['url'];
				break;

			case 'multiple_images':
				$url = $media['items'][0]['images'][ $size ]['url'];
				break;

			case 'multiple_videos':
				$url = $media['items'][0]['cover_image_url'];
				break;

			case 'multiple_mixed':
				$source = $media['items'][0];

				if ( isset( $source['images'] ) ) {
					$url = $source['images'][ $size ]['url'];

				} else {
					$url = $source['cover_image_url'];
				}

				break;
		}

		if ( $return_url ) {
			return $url;
		}

		$alt = empty( $alt ) ? $title : $alt;

		?>
		<img class="<?php echo esc_attr( 'premium-hover-effects__' . $hover ); ?>" src="<?php echo esc_url( $url ); ?>" alt="<?php echo esc_attr( $alt ); ?>">
		<?php
	}

	/**
	 * Displays pin Likes, Comments, Views, Shares counters.
	 *
	 * @access private
	 * @since
	 *
	 * @param array $pin_settings  pin settings.
	 * @param array $counter   counters
	 */
	private function get_pin_counters( $settings, $data ) {
		?>
		<?php if ( $settings['follower_count'] && ! is_null( $data['follower_count'] ) ) : ?>
			<span class="premium-pinterest-feed__followers" title="Followers">
				<span class="premium-pinterest-feed__count"> <?php echo esc_html( Helper_Functions::premium_format_numbers( $data['follower_count'] ) ); ?></span>
				<span>Followers</span>
			</span>
		<?php endif; ?>

		<?php if ( $settings['following_count'] && ! is_null( $data['following_count'] ) ) : ?>
			<span class="premium-pinterest-feed__followings" title="Following">
				<span class="premium-pinterest-feed__count"><?php echo esc_html( Helper_Functions::premium_format_numbers( $data['following_count'] ) ); ?></span>
				<span>Following</span>
			</span>
		<?php endif; ?>

		<?php if ( $settings['view_count'] && ! is_null( $data['monthly_views'] ) ) : ?>
			<span class="premium-pinterest-feed__views"  title="Monthly Views">
				<span class="premium-pinterest-feed__count"><?php echo esc_html( Helper_Functions::premium_format_numbers( $data['monthly_views'] ) ); ?></span>
				<span>Monthly Views</span>
			</span>
		<?php endif; ?>
		<?php
	}

	/**
	 * Displays pin Likes, Comments, Views, Shares counters.
	 *
	 * @access private
	 * @since
	 *
	 * @param array  $settings  widget settings.
	 * @param array  $pin_settings  pin settings.
	 * @param object $feed   pin obj
	 */
	private function get_pin_meta( $settings, $pin_settings, $feed ) {
		?>
		<div class="premium-pinterest-feed__meta">

			<?php if ( $pin_settings['username'] ) : ?>
				<span class="premium-pinterest-feed__pin-creator">
					<a href="<?php echo esc_url( 'https://www.pinterest.com/' . $feed['board_owner']['username'] ); ?>" target="_blank"><?php echo esc_html( $feed['board_owner']['username'] ); ?></a>
				</span>
			<?php endif; ?>

			<?php if ( $pin_settings['date'] ) : ?>
				<span class="premium-pinterest-feed__created-at"><?php echo esc_html( date( $settings['date_format'], strtotime( $feed['created_at'] ) ) ); ?></span>
			<?php endif; ?>

		</div>
		<?php
	}

	/**
	 * Render Feed Description.
	 *
	 * @access private
	 * @since
	 *
	 * @param array  $feed  feed array.
	 * @param array  $settings  widget settings.
	 * @param string $type  feed type.
	 *
	 * @return string
	 */
	private function render_feed_desc( $feed, $settings, $type = 'pin' ) {

		$len = $settings[ $type . '_desc_len' ];

		if ( ! empty( $len ) ) {

			$desc = $this->get_feed_desc( $feed, $len, $settings, $type );
		}

		echo wp_kses_post( $desc );
	}

	/**
	 * Render Feed Description.
	 *
	 * @access private
	 * @since
	 *
	 * @param array  $feed  feed array.
	 * @param string $len  description length.
	 * @param array  $settings  widget settings.
	 * @param string $type  feed type.
	 *
	 * @return string
	 */
	private function get_feed_desc( $feed, $len, $settings, $type = 'pin' ) {

		$desc = trim( $feed['description'] );

		$words = explode( ' ', $desc, $len + 1 );

		$postfix_type = $settings[ $type . '_desc_postfix' ];

		if ( count( $words ) > $len ) {

			array_pop( $words );

			$desc = implode( ' ', $words );

			if ( 'dots' === $postfix_type ) {
				$desc .= '...';
			} else {

				if ( 'board' === $type ) {
					$url = 'https://www.pinterest.com/' . $feed['owner']['username'] . '/_saved';

				} else {
					$url = 'https://www.pinterest.com/pin/' . $feed['id'];
				}

				$desc .= '<a class="premium-read-more" target="_blank" href="' . $url . '">' . $settings[ $type . '_desc_postfix_txt' ] . '</a>';
			}
		}

		return $desc;
	}

	/**
	 * Render Pinterest Icon
	 *
	 * @since 4.10.2
	 *
	 * @param boolean $is_follow is follow button.
	 */
	private function render_pinterest_icon( $from ) {

		$follow_class = 'premium-pinterest-icon-' . $from;

		?>

			<span class="premium-pinterest-feed__pinterest-icon <?php echo esc_attr( $follow_class ); ?>">
				<svg id="Layer_1" xmlns="http://www.w3.org/2000/svg" width="45.1" height="45.17" viewBox="0 0 45.1 45.17"><defs><style>.premium-pinterest-icon-1{fill:#e60023;}</style></defs><path id="Pinterest" class="premium-pinterest-icon-1" d="m22.57,0C10.1,0,0,10.1,0,22.57c0,9.57,5.94,17.74,14.34,21.03-.2-1.78-.37-4.53.07-6.48.41-1.76,2.64-11.22,2.64-11.22,0,0-.67-1.36-.67-3.34,0-3.14,1.82-5.48,4.09-5.48,1.93,0,2.86,1.45,2.86,3.18,0,1.93-1.23,4.83-1.88,7.52-.54,2.25,1.13,4.09,3.34,4.09,4.01,0,7.1-4.24,7.1-10.33,0-5.41-3.88-9.18-9.44-9.18-6.43,0-10.2,4.81-10.2,9.79,0,1.93.74,4.01,1.67,5.15.19.22.2.43.15.65-.17.71-.56,2.25-.63,2.56-.09.41-.33.5-.76.3-2.82-1.32-4.59-5.42-4.59-8.75,0-7.11,5.16-13.65,14.92-13.65,7.82,0,13.91,5.57,13.91,13.04s-4.9,14.04-11.7,14.04c-2.28,0-4.44-1.19-5.16-2.6,0,0-1.13,4.31-1.41,5.37-.5,1.97-1.88,4.42-2.8,5.93,2.12.65,4.35,1,6.69,1,12.46,0,22.57-10.1,22.57-22.57.04-12.5-10.07-22.61-22.53-22.61Z"/></svg>
			</span>

		<?php

	}

	private function render_share_button( $link ) {
		?>
		<div class="premium-pinterest-feed__share-outer">
			<div class="premium-pinterest-share-container">
				<span class="premium-pinterest-share-button">
					<i class="fa fa-share custom-fa" aria-hidden="true"></i>
					<span class="premium-pinterest-sharer">Share</span>
					<div class="premium-pinterest-share-menu">
						<a data-pa-link="<?php echo $link; ?>" class="premium-pinterest-share-item premium-copy-link">
							<i class="fas fa-link"></i>
							<span class="premium-pinterest-share-text pre-infs-fb">Copy To Clipboard</span>
						</a>
						<a data-pa-link="https://www.facebook.com/sharer/sharer.php?u=<?php echo $link; ?>" class="premium-pinterest-share-item">
							<i class="fab fa-facebook-f if-fb"></i>
							<span class="premium-pinterest-share-text pre-infs-fb">Share on Facebook</span>
						</a>
						<a data-pa-link="https://twitter.com/intent/tweet?text=tweet&url=<?php echo $link; ?>" class="premium-pinterest-share-item">
							<i class="fab fa-twitter if-tw"></i>
							<span class="premium-pinterest-share-text pre-infs-tw">Share on Twitter</span>
						</a>
						<a data-pin-do="buttonPin" data-pa-link="https://www.pinterest.com/pin/create/button/?url=<?php echo $link; ?>" class="premium-pinterest-share-item">
							<i class="fab fa-pinterest-p if-pi"></i>
							<span class="premium-pinterest-share-text pre-infs-pi">Share on Pinterest</span>
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

				$( '.premium-pinterest-feed__pins-wrapper' ).each( function() {

					var $node_id 	= '<?php echo esc_attr( $this->get_id() ); ?>',
						scope 		= $( '[data-id="' + $node_id + '"]' ),
						selector 	= $(this);


					if ( selector.closest( scope ).length < 1 ) {
						return;
					}


					var masonryArgs = {
						itemSelector	: '.premium-pinterest-feed__pin-outer-wrapper',
						percentPosition : true,
						layoutMode		: 'masonry',
					};

					var $isotopeObj = {};

					selector.imagesLoaded( function() {

						$isotopeObj = selector.isotope( masonryArgs );

						$isotopeObj.imagesLoaded().progress(function() {
							$isotopeObj.isotope("layout");
						});

						selector.find('.premium-pinterest-feed__pin-outer-wrapper').resize( function() {
							$isotopeObj.isotope( 'layout' );
						});
					});

				});
			});
		</script>
		<?php
	}
}
