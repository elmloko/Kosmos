<?php
/**
 * Advanced AMedia Carousel.
 */

namespace PremiumAddons\Widgets;

// Elementor Classes.
use Elementor\Utils;
use Elementor\Embed;
use Elementor\Repeater;
use Elementor\Widget_Base;
use Elementor\Icons_Manager;
use Elementor\Control_Media;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Background;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Css_Filter;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Text_Shadow;
use Elementor\Core\Kits\Documents\Tabs\Global_Colors;
use Elementor\Core\Kits\Documents\Tabs\Global_Typography;
use Elementor\Modules\DynamicTags\Module as TagsModule;

// PremiumAddons Classes.
use PremiumAddons\Includes\Helper_Functions;
use PremiumAddons\Includes\Premium_Template_Tags;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // If this file is called directly, abort.
}

/**
 * Class Premium_Media_wheel
 */
class Premium_Media_Wheel extends Widget_Base {

	/**
	 * Template Instance
	 *
	 * @var template_instance
	 */
	protected $template_instance;

	/**
	 * Check for Self Hosted Videos
	 *
	 * @var is_self_hosted
	 */
	private static $check_self_hosted = false;

	/**
	 * Retrieve Template Instance.
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
		return 'premium-media-wheel';
	}

	/**
	 * Retrieve Widget Title.
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function get_title() {
		return __( 'Advanced Carousel', 'premium-addons-for-elementor' );
	}

	/**
	 * Retrieve Widget Icon.
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function get_icon() {
		return 'pa-media-wheel';
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
	 * @return array CSS script handles.
	 */
	public function get_style_depends() {
		return array(
			'pa-prettyphoto',
			'pa-flipster',
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
			'pa-tweenmax',
			'elementor-waypoints',
			'mousewheel-js',
			'pa-flipster',
			'prettyPhoto-js',
			'premium-addons',
		);
	}

	/**
	 * Retrieve Widget Keywords.
	 *
	 * @access public
	 *
	 * @return array JS script handles.
	 */
	public function get_keywords() {
		return array( 'youtube', 'vimeo', 'self', 'hosted', 'scroll', 'image scroll', 'carousel', 'flip', 'coverflow', 'media' );
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
	 * Widget preview refresh button.
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function is_reload_preview_required() {
		return true;
	}

	/**
	 * Register Advanced Media Carousel controls.
	 *
	 * @since 1.0.0
	 * @access protected
	 */
	protected function register_controls() {

		$this->add_items_controls();
		$this->add_advanced_controls();
		$this->add_light_box_controls();

		// style controls.
		$this->add_img_style_controls();
		$this->add_icon_style_controls();
		$this->add_info_style_controls();
		$this->add_items_style_controls();
		$this->add_navigation_style();
		$this->add_light_box_icon_style();
		$this->add_light_box_style();
	}

	private function add_items_controls() {

		$this->start_controls_section(
			'item_section',
			array(
				'label' => __( 'Items', 'premium-addons-for-elementor' ),
			)
		);

		$repeater = new Repeater();

		$repeater->add_control(
			'pa_media_type',
			array(
				'label'   => __( 'Media Type', 'premium-addons-for-elementor' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'image',
				'options' => array(
					'image'    => __( 'Image', 'premium-addons-for-elementor' ),
					'video'    => __( 'Video', 'premium-addons-for-elementor' ),
					'template' => __( 'Elementor Template', 'premium-addons-for-elementor' ),
				),
			)
		);

		$repeater->add_control(
			'media_wheel_img',
			array(
				'label'     => __( 'Choose Image', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::MEDIA,
				'dynamic'   => array( 'active' => true ),
				'default'   => array(
					'url' => Utils::get_placeholder_image_src(),
				),
				'condition' => array(
					'pa_media_type!' => 'template',
				),
			)
		);

		$repeater->add_responsive_control(
			'mw_image_fit',
			array(
				'label'     => __( 'Image Fit', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::SELECT,
				'options'   => array(
					'cover'   => __( 'Cover', 'premium-addons-for-elementor' ),
					'fill'    => __( 'Fill', 'premium-addons-for-elementor' ),
					'contain' => __( 'Contain', 'premium-addons-for-elementor' ),
				),
				'default'   => 'cover',
				'selectors' => array(
					'{{WRAPPER}} {{CURRENT_ITEM}} .premium-adv-carousel__item-img, {{WRAPPER}} {{CURRENT_ITEM}} .vid-overlay' => 'object-fit: {{VALUE}}',
				),
				'condition' => array(
					'pa_media_type!' => 'template',
				),
			)
		);

		$repeater->add_responsive_control(
			'mw_image_obj_pos',
			array(
				'label'     => __( 'Image Position', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::SELECT,
				'options'   => array(
					''              => __( 'Default', 'premium-addons-for-elementor' ),
					'center center' => __( 'Center Center', 'premium-addons-for-elementor' ),
					'center left'   => __( 'Center Left', 'premium-addons-for-elementor' ),
					'center right'  => __( 'Center Right', 'premium-addons-for-elementor' ),
					'top center'    => __( 'Top Center', 'premium-addons-for-elementor' ),
					'top left'      => __( 'Top Left', 'premium-addons-for-elementor' ),
					'top right'     => __( 'Top Right', 'premium-addons-for-elementor' ),
					'bottom center' => __( 'Bottom Center', 'premium-addons-for-elementor' ),
					'bottom left'   => __( 'Bottom Left', 'premium-addons-for-elementor' ),
					'bottom right'  => __( 'Bottom Right', 'premium-addons-for-elementor' ),
					'initial'       => __( 'Custom', 'premium-addons-for-elementor' ),
				),
				'default'   => '',
				'selectors' => array(
					'{{WRAPPER}} {{CURRENT_ITEM}} .premium-adv-carousel__item-img, {{WRAPPER}} {{CURRENT_ITEM}} .vid-overlay' => 'object-position: {{VALUE}}',
				),
				'condition' => array(
					'pa_media_type!' => 'template',
				),
			)
		);

		$repeater->add_responsive_control(
			'mw_image_obj_pos_x',
			array(
				'label'          => __( 'X Position', 'premium-addons-for-elementor' ),
				'type'           => Controls_Manager::SLIDER,
				'size_units'     => array( 'px', '%' ),
				'range'          => array(
					'px' => array(
						'min' => -800,
						'max' => 800,
					),
					'%'  => array(
						'min' => -100,
						'max' => 100,
					),
				),
				'default'        => array(
					'size' => 0,
				),
				'tablet_default' => array(
					'size' => 0,
				),
				'mobile_default' => array(
					'size' => 0,
				),
				'selectors'      => array(
					'{{WRAPPER}} {{CURRENT_ITEM}} .premium-adv-carousel__item-img, {{WRAPPER}} {{CURRENT_ITEM}} .vid-overlay' => 'object-position: {{SIZE}}{{UNIT}} {{mw_image_obj_pos_y.SIZE}}{{mw_image_obj_pos_y.UNIT}};',
				),
				'condition'      => array(
					'pa_media_type!'   => 'template',
					'mw_image_obj_pos' => 'initial',
				),
			)
		);

		$repeater->add_responsive_control(
			'mw_image_obj_pos_y',
			array(
				'label'          => __( 'Y Position', 'premium-addons-for-elementor' ),
				'type'           => Controls_Manager::SLIDER,
				'size_units'     => array( 'px', '%' ),
				'range'          => array(
					'px' => array(
						'min' => -800,
						'max' => 800,
					),
					'%'  => array(
						'min' => -100,
						'max' => 100,
					),
				),
				'default'        => array(
					'size' => 0,
				),
				'tablet_default' => array(
					'size' => 0,
				),
				'mobile_default' => array(
					'size' => 0,
				),
				'selectors'      => array(
					'{{WRAPPER}} {{CURRENT_ITEM}} .premium-adv-carousel__item-img, {{WRAPPER}} {{CURRENT_ITEM}} .vid-overlay' => 'object-position: {{mw_image_obj_pos_x.SIZE}}{{mw_image_obj_pos_x.UNIT}} {{SIZE}}{{UNIT}};',
				),
				'condition'      => array(
					'pa_media_type!'   => 'template',
					'mw_image_obj_pos' => 'initial',
				),
			)
		);

		$repeater->add_control(
			'media_wheel_video_type',
			array(
				'label'       => __( 'Type', 'premium-addons-for-elementor' ),
				'type'        => Controls_Manager::SELECT,
				'options'     => array(
					'youtube' => __( 'YouTube', 'premium-addons-for-elementor' ),
					'vimeo'   => __( 'Vimeo', 'premium-addons-for-elementor' ),
					'hosted'  => __( 'Self Hosted', 'premium-addons-for-elementor' ),
				),
				'label_block' => true,
				'default'     => 'youtube',
				'condition'   => array(
					'pa_media_type' => 'video',
				),
			)
		);

		$repeater->add_control(
			'media_wheel_video_link',
			array(
				'label'       => __( 'Video Link', 'premium-addons-for-elementor' ),
				'type'        => Controls_Manager::TEXT,
				'label_block' => true,
				'dynamic'     => array(
					'active'     => true,
					'categories' => array(
						TagsModule::POST_META_CATEGORY,
						TagsModule::URL_CATEGORY,
					),
				),
				'condition'   => array(
					'pa_media_type'           => 'video',
					'media_wheel_video_type!' => 'hosted',
				),
			)
		);

		$repeater->add_control(
			'media_wheel_video_self',
			array(
				'label'      => __( 'Select Video', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::MEDIA,
				'dynamic'    => array(
					'active'     => true,
					'categories' => array(
						TagsModule::POST_META_CATEGORY,
						TagsModule::URL_CATEGORY,
					),
				),
				'media_type' => 'video',
				'condition'  => array(
					'pa_media_type'          => 'video',
					'media_wheel_video_type' => 'hosted',
				),
			)
		);

		$repeater->add_control(
			'media_wheel_video_self_link',
			array(
				'label'       => __( 'Remote Video Link', 'premium-addons-for-elementor' ),
				'type'        => Controls_Manager::TEXT,
				'dynamic'     => array(
					'active' => true,
				),
				'label_block' => true,
				'condition'   => array(
					'pa_media_type'          => 'video',
					'media_wheel_video_type' => 'hosted',
				),
			)
		);

		$repeater->add_responsive_control(
			'mw_hosted_vid_fit',
			array(
				'label'     => __( 'Video Fit', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::SELECT,
				'options'   => array(
					'cover'   => __( 'Cover', 'premium-addons-for-elementor' ),
					'fill'    => __( 'Fill', 'premium-addons-for-elementor' ),
					'contain' => __( 'Contain', 'premium-addons-for-elementor' ),
				),
				'default'   => 'cover',
				'selectors' => array(
					'{{WRAPPER}} {{CURRENT_ITEM}} .premium-adv-carousel__video-wrap video' => 'object-fit: {{VALUE}}',
				),
				'condition' => array(
					'pa_media_type'          => 'video',
					'media_wheel_video_type' => 'hosted',
				),
			)
		);

		$repeater->add_control(
			'media_wheel_yt_thumbnail_size',
			array(
				'label'       => __( 'Thumbnail Size', 'premium-addons-for-elementor' ),
				'type'        => Controls_Manager::SELECT,
				'options'     => array(
					'maxresdefault' => __( 'Maximum Resolution', 'premium-addons-for-elementor' ),
					'hqdefault'     => __( 'High Quality', 'premium-addons-for-elementor' ),
					'mqdefault'     => __( 'Medium Quality', 'premium-addons-for-elementor' ),
					'sddefault'     => __( 'Standard Quality', 'premium-addons-for-elementor' ),
				),
				'default'     => 'maxresdefault',
				'condition'   => array(
					'pa_media_type'          => 'video',
					'media_wheel_video_type' => 'youtube',
				),
				'render_type' => 'template',
			)
		);

		$repeater->add_control(
			'media_wheel_video_controls',
			array(
				'label'     => __( 'Controls', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::SWITCHER,
				'condition' => array(
					'pa_media_type' => 'video',
				),
			)
		);

		$repeater->add_control(
			'media_wheel_video_mute',
			array(
				'label'     => __( 'Mute', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::SWITCHER,
				'default'   => 'yes',
				'condition' => array(
					'pa_media_type' => 'video',
				),
			)
		);

		$repeater->add_control(
			'media_wheel_video_loop',
			array(
				'label'        => __( 'Loop', 'premium-addons-for-elementor' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'true',
				'condition'    => array(
					'pa_media_type'          => 'video',
					'media_wheel_video_type' => 'hosted',
				),
			)
		);

		$repeater->add_control(
			'media_wheel_video_icon_switcher',
			array(
				'label'     => __( 'Show Play Icon', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::SWITCHER,
				'default'   => 'yes',
				'condition' => array(
					'pa_media_type' => 'video',
				),

			)
		);

		$repeater->add_control(
			'media_wheel_videos_icon',
			array(
				'label'     => __( 'Video Play Icon', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::ICONS,
				'default'   => array(
					'library' => 'fa-solid',
					'value'   => 'fas fa-play',
				),
				'condition' => array(
					'media_wheel_video_icon_switcher' => 'yes',
					'pa_media_type'                   => 'video',
				),
			)
		);

		$repeater->add_responsive_control(
			'media_wheel_vIcon_size',
			array(
				'label'      => __( 'Icon Size', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array(
					'px' => array(
						'min'  => 0,
						'max'  => 500,
						'step' => 1,
					),
				),
				'default'    => array(
					'unit' => 'px',
					'size' => 25,
				),
				'selectors'  => array(
					'{{WRAPPER}} {{CURRENT_ITEM}} .pa-media-icons-wrapper .premium-adv-carousel__video-icon i'       => 'font-size: {{SIZE}}{{UNIT}}; line-height: {{SIZE}}{{UNIT}}',
					'{{WRAPPER}} {{CURRENT_ITEM}} .pa-media-icons-wrapper .premium-adv-carousel__video-icon svg'       => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}}',
				),
				'condition'  => array(
					'pa_media_type'                   => 'video',
					'media_wheel_video_icon_switcher' => 'yes',
				),
			)
		);

		$repeater->add_control(
			'media_wheel_vIcon_color',
			array(
				'label'     => __( 'Icon Color', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} {{CURRENT_ITEM}} .pa-media-icons-wrapper .premium-adv-carousel__video-icon i' => 'color: {{VALUE}}',
					'{{WRAPPER}} {{CURRENT_ITEM}} .pa-media-icons-wrapper .premium-adv-carousel__video-icon svg' => 'fill: {{VALUE}}',
				),
				'condition' => array(
					'pa_media_type'                   => 'video',
					'media_wheel_video_icon_switcher' => 'yes',
				),
			)
		);

		$repeater->add_control(
			'live_temp_content',
			array(
				'label'       => __( 'Template Title', 'premium-addons-for-elementor' ),
				'type'        => Controls_Manager::TEXT,
				'label_block' => true,
				'classes'     => 'premium-live-temp-title control-hidden',
				'condition'   => array(
					'pa_media_type' => 'template',
				),
			)
		);

		$repeater->add_control(
			'section_template_live',
			array(
				'type'        => Controls_Manager::BUTTON,
				'label_block' => true,
				'button_type' => 'default papro-btn-block',
				'text'        => __( 'Create / Edit Template', 'premium-addons-for-elementor' ),
				'event'       => 'createLiveTemp',
				'condition'   => array(
					'pa_media_type' => 'template',
				),
			)
		);

		$repeater->add_control(
			'section_template',
			array(
				'label'       => __( 'Elementor Template', 'premium-addons-pro' ),
				'type'        => Controls_Manager::SELECT2,
				'options'     => $this->getTemplateInstance()->get_elementor_page_list(),
				'multiple'    => false,
				'label_block' => true,
				'condition'   => array(
					'pa_media_type' => 'template',
				),
			)
		);

		$repeater->add_control(
			'media_wheel_link_switcher',
			array(
				'label'     => __( 'Link', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::SWITCHER,
				'separator' => 'before',
				'condition' => array(
					'pa_media_type!' => 'video',
				),
			)
		);

		$repeater->add_control(
			'media_wheel_link_new_tab',
			array(
				'label'     => __( 'Open In New Tab', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::SWITCHER,
				'condition' => array(
					'pa_media_type!'            => 'video',
					'media_wheel_link_switcher' => 'yes',
				),
			)
		);

		$repeater->add_control(
			'media_wheel_link_type',
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
					'media_wheel_link_switcher' => 'yes',
					'pa_media_type!'            => 'video',
				),
			)
		);

		$repeater->add_control(
			'media_wheel_custom_link',
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
					'media_wheel_link_switcher' => 'yes',
					'media_wheel_link_type'     => 'url',
					'pa_media_type!'            => 'video',
				),
			)
		);

		$repeater->add_control(
			'media_wheel_existing_link',
			array(
				'label'       => __( 'Existing Page', 'premium-addons-for-elementor' ),
				'type'        => Controls_Manager::SELECT2,
				'options'     => $this->getTemplateInstance()->get_all_posts(),
				'condition'   => array(
					'media_wheel_link_switcher' => 'yes',
					'media_wheel_link_type'     => 'link',
					'pa_media_type!'            => 'video',
				),
				'multiple'    => false,
				'label_block' => true,
			)
		);

		$repeater->add_control(
			'media_info',
			array(
				'label'     => __( 'Media Info', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::SWITCHER,
				'separator' => 'before',
				'condition' => array(
					'pa_media_type' => 'image',
				),
			)
		);

		$repeater->add_control(
			'media_title',
			array(
				'label'       => __( 'Title', 'premium-addons-for-elementor' ),
				'type'        => Controls_Manager::TEXT,
				'dynamic'     => array( 'active' => true ),
				'default'     => 'Premium Addons',
				'label_block' => true,
				'condition'   => array(
					'media_info'    => 'yes',
					'pa_media_type' => 'image',
				),
			)
		);

		$repeater->add_control(
			'media_desc',
			array(
				'label'       => __( 'Description', 'premium-addons-for-elementor' ),
				'type'        => Controls_Manager::TEXTAREA,
				'dynamic'     => array( 'active' => true ),
				'label_block' => true,
				'condition'   => array(
					'media_info'    => 'yes',
					'pa_media_type' => 'image',
				),
			)
		);

		$repeater->add_responsive_control(
			'media_wheel_item_width',
			array(
				'label'      => __( 'Width', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', '%', 'em' ),
				'separator'  => 'before',
				'range'      => array(
					'px' => array(
						'min'  => 0,
						'max'  => 1000,
						'step' => 1,
					),
				),
				'selectors'  => array(
					'{{WRAPPER}}.premium-adv-carousel__infinite {{CURRENT_ITEM}}.premium-adv-carousel__item' => 'width: {{SIZE}}{{UNIT}}',
				),
			)
		);

		$repeater->add_responsive_control(
			'media_wheel_item_height',
			array(
				'label'      => __( 'Height', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', '%', 'em', 'custom' ),
				'range'      => array(
					'px' => array(
						'min'  => 0,
						'max'  => 1000,
						'step' => 1,
					),
				),
				'selectors'  => array(
					'{{WRAPPER}}.premium-adv-carousel__infinite {{CURRENT_ITEM}}.premium-adv-carousel__item .premium-adv-carousel__media-wrap' => 'height: {{SIZE}}{{UNIT}}',
				),
			)
		);

		$repeater->add_control(
			'media_items_notice',
			array(
				'type'            => Controls_Manager::RAW_HTML,
				'raw'             => __( 'Note:The above <b>Width and Height</b> controls will only work if the animation type is set to <b>Infinite</b> to override the global settings.', 'premium-addons-for-elementor' ),
				'content_classes' => 'elementor-panel-alert elementor-panel-alert-info',
			)
		);

		$repeater->add_group_control(
			Group_Control_Background::get_type(),
			array(
				'name'     => 'media_wheel_card_bg_color',
				'types'    => array( 'classic', 'gradient' ),
				'selector' => '{{WRAPPER}} {{CURRENT_ITEM}}.premium-adv-carousel__item',
			)
		);

		$repeater->add_control(
			'media_wheel_item_border_radius',
			array(
				'label'      => __( 'Border Radius', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', '%' ),
				'range'      => array(
					'px' => array(
						'min'  => 0,
						'max'  => 100,
						'step' => 1,
					),
				),
				'selectors'  => array(
					'{{WRAPPER}} {{CURRENT_ITEM}}.premium-adv-carousel__item' => 'border-radius: {{SIZE}}{{UNIT}}',
				),
			)
		);

		$repeater->add_responsive_control(
			'media_wheel_item_padding',
			array(
				'label'      => __( 'Padding', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px' ),
				'range'      => array(
					'px' => array(
						'min'  => 0,
						'max'  => 500,
						'step' => 1,
					),
				),
				'default'    => array(
					'unit' => 'px',
					'size' => 0,
				),
				'selectors'  => array(
					'{{WRAPPER}} {{CURRENT_ITEM}}.premium-adv-carousel__item' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$repeater->add_responsive_control(
			'media_wheel_item_margin',
			array(
				'label'      => __( 'Margin', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px' ),
				'range'      => array(
					'px' => array(
						'min'  => 0,
						'max'  => 500,
						'step' => 1,
					),
				),
				'default'    => array(
					'unit' => 'px',
					'size' => 0,
				),
				'selectors'  => array(
					'{{WRAPPER}} {{CURRENT_ITEM}}.premium-adv-carousel__item' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'media_wheel_repeater',
			array(
				'label'       => __( 'Carousel Cards', 'premium-addons-for-elementor' ),
				'type'        => Controls_Manager::REPEATER,
				'fields'      => $repeater->get_controls(),
				'render_type' => 'template',
				'default'     => array(
					array(
						'pa_media_type' => 'image',
					),
					array(
						'pa_media_type' => 'image',
					),
					array(
						'pa_media_type' => 'image',
					),
					array(
						'pa_media_type' => 'image',
					),
					array(
						'pa_media_type' => 'image',
					),
				),
				'title_field' => '{{{ pa_media_type }}}',
			)
		);

		$this->add_responsive_control(
			'media_wheel_item_width_gen',
			array(
				'label'      => __( 'Width', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', '%', 'em' ),
				'separator'  => 'before',
				'range'      => array(
					'px' => array(
						'min'  => 0,
						'max'  => 1000,
						'step' => 1,
					),
				),
				'selectors'  => array(
					'{{WRAPPER}} .premium-adv-carousel__item' => 'width: {{SIZE}}{{UNIT}}',
				),
			)
		);

		$this->add_responsive_control(
			'media_wheel_item_height_gen',
			array(
				'label'      => __( 'Height', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', '%', 'em', 'custom' ),
				'range'      => array(
					'px' => array(
						'min'  => 0,
						'max'  => 1000,
						'step' => 1,
					),
				),
				'selectors'  => array(
					'{{WRAPPER}} .premium-adv-carousel__item .premium-adv-carousel__media-wrap' => 'height: {{SIZE}}{{UNIT}}',
				),
			)
		);

		$this->add_responsive_control(
			'pa_media_alignment',
			array(
				'label'     => __( 'Alignment', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::CHOOSE,
				'options'   => array(
					'flex-start' => array(
						'title' => __( 'Start', 'premium-addons-for-elementor' ),
						'icon'  => 'eicon-h-align-left',
					),
					'center'     => array(
						'title' => __( 'Center', 'premium-addons-for-elementor' ),
						'icon'  => 'eicon-h-align-center',
					),
					'flex-end'   => array(
						'title' => __( 'End', 'premium-addons-for-elementor' ),
						'icon'  => 'eicon-h-align-right',
					),
				),
				'default'   => 'flex-start',
				'toggle'    => false,
				'selectors' => array(
					'{{WRAPPER}} .premium-adv-carousel__items' => 'align-items: {{VALUE}}',
				),
				'condition' => array(
					'media_wheel_animation' => 'infinite',
				),
			)
		);

		$this->add_responsive_control(
			'pa_media_spacing',
			array(
				'label'      => __( 'Spacing', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array(
					'px' => array(
						'min'  => 0,
						'max'  => 500,
						'step' => 1,
					),
				),
				'default'    => array(
					'unit' => 'px',
					'size' => 5,
				),
				'selectors'  => array(
					'{{WRAPPER}}.premium-adv-carousel__horizontal .premium-adv-carousel__items'       => 'column-gap: {{SIZE}}{{UNIT}}',
					'{{WRAPPER}}.premium-adv-carousel__vertical .premium-adv-carousel__items'       => 'row-gap: {{SIZE}}{{UNIT}}',
					'{{WRAPPER}}.premium-adv-carousel__horizontal'       => '--pa-wheel-spacing: {{SIZE}}',
				),
				'condition'  => array(
					'media_wheel_animation' => 'infinite',
				),
			)
		);

		$this->end_controls_section();
	}

	private function add_advanced_controls() {

		$papro_activated = apply_filters( 'papro_activated', false );

		$this->start_controls_section(
			'advanced_settings_section',
			array(
				'label' => __( 'Advanced Settings', 'premium-addons-for-elementor' ),
			)
		);

		$this->add_control(
			'media_wheel_animation',
			array(
				'label'        => __( 'Animation', 'premium-addons-for-elementor' ),
				'type'         => Controls_Manager::SELECT,
				'prefix_class' => 'premium-adv-carousel__',
				'default'      => 'horizontal',
				'options'      => array(
					'infinite'  => __( 'Infinite', 'premium-addons-for-elementor' ),
					'coverflow' => apply_filters( 'pa_pro_label', __( 'Coverflow (Pro)', 'premium-addons-for-elementor' ) ),
					'carousel'  => apply_filters( 'pa_pro_label', __( 'Flip (Pro)', 'premium-addons-for-elementor' ) ),
					'flat'      => apply_filters( 'pa_pro_label', __( 'Flat (Pro)', 'premium-addons-for-elementor' ) ),
				),
				'default'      => 'infinite',
				'render_type'  => 'template',
			)
		);

		$this->add_control(
			'media_wheel_direction',
			array(
				'label'        => __( 'Layout', 'premium-addons-for-elementor' ),
				'type'         => Controls_Manager::SELECT,
				'prefix_class' => 'premium-adv-carousel__',
				'default'      => 'horizontal',
				'options'      => array(
					'vertical'   => __( 'Vertical', 'premium-addons-for-elementor' ),
					'horizontal' => __( 'Horizontal', 'premium-addons-for-elementor' ),
				),
				'render_type'  => 'template',
				'condition'    => array(
					'media_wheel_animation' => 'infinite',
				),
			)
		);

		$this->add_responsive_control(
			'media_wheel_height',
			array(
				'label'      => __( 'Height', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array(
					'px' => array(
						'min'  => 0,
						'max'  => 1000,
						'step' => 1,
					),
				),
				'selectors'  => array(
					'{{WRAPPER}} .premium-adv-carousel__inner-container'       => 'height: {{SIZE}}{{UNIT}};',
				),
				'condition'  => array(
					'media_wheel_animation' => 'infinite',
				),
			)
		);

		$this->add_control(
			'media_wheel_speed',
			array(
				'label'       => __( 'Speed', 'premium-addons-for-elementor' ),
				'type'        => Controls_Manager::NUMBER,
				'description' => __( 'The smaller the value, the faster the animation.', 'premium-addons-for-elementor' ),
				'default'     => 50,
				'condition'   => array(
					'media_wheel_animation' => 'infinite',
				),
				'selectors'   => array(
					'{{WRAPPER}} .premium-adv-carousel__items' => 'animation-duration: calc( {{VALUE}} * 1000ms ) !important',
				),
			)
		);

		$this->add_control(
			'media_wheel_reverse',
			array(
				'label'       => __( 'Animation Direction', 'premium-addons-for-elementor' ),
				'type'        => Controls_Manager::SELECT,
				'default'     => 'normal',
				'options'     => array(
					'normal'  => __( 'Normal', 'premium-addons-for-elementor' ),
					'reverse' => __( 'Reverse', 'premium-addons-for-elementor' ),
				),
				'render_type' => 'template',
				'selectors'   => array(
					'{{WRAPPER}} .premium-adv-carousel__items' => 'animation-direction: {{VALUE}} !important',
				),
				'condition'   => array(
					'media_wheel_animation' => 'infinite',
				),
			)
		);

		if ( $papro_activated ) {

			do_action( 'pa_adv_carousel_options', $this );

		} else {

			$get_pro = Helper_Functions::get_campaign_link( 'https://premiumaddons.com/pro', 'editor-page', 'wp-editor', 'get-pro' );

			$this->add_control(
				'effect_notice',
				array(
					'type'            => Controls_Manager::RAW_HTML,
					'raw'             => __( 'This option is available in Premium Addons Pro. ', 'premium-addons-for-elementor' ) . '<a href="' . esc_url( $get_pro ) . '" target="_blank">' . __( 'Upgrade now!', 'premium-addons-for-elementor' ) . '</a>',
					'content_classes' => 'papro-upgrade-notice',
					'condition'       => array(
						'media_wheel_animation!' => 'infinite',
					),
				)
			);

		}

		$this->add_control(
			'media_wheel_scroll',
			array(
				'label'     => __( 'Animate By Mousewheel', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::SWITCHER,
				'default'   => 'yes',
				'condition' => array(
					'media_wheel_animation!' => 'infinite',
					// 'media_wheel_scroll'     => 'yes',
					// 'media_wheel_autoplay'   => 'yes',
				),
			)
		);

		$this->add_control(
			'pause_on_hover',
			array(
				'label'   => __( 'Pause on Hover', 'premium-addons-for-elementor' ),
				'type'    => Controls_Manager::SWITCHER,
				'default' => 'yes',
			)
		);

		if ( $papro_activated ) {

			do_action( 'pa_adv_carousel_navigation', $this );

		}

		$this->add_control(
			'media_wheel_fading_switcher',
			array(
				'label'     => __( 'Enable Fading Color', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::SWITCHER,
				'condition' => array(
					'media_wheel_animation' => 'infinite',
				),
			)
		);

		$this->add_control(
			'media_wheel_fading_color',
			array(
				'label'     => __( 'Fading Color', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'condition' => array(
					'media_wheel_animation'       => 'infinite',
					'media_wheel_fading_switcher' => 'yes',
				),
				'selectors' => array(
					'{{WRAPPER}}.premium-adv-carousel__vertical .premium-adv-carousel__container:after' => 'background:linear-gradient(to bottom, {{VALUE}}, #F291D800 10%, #F291D800 90%, {{VALUE}}) !important',
					'{{WRAPPER}}.premium-adv-carousel__horizontal .premium-adv-carousel__container:after' => 'background:linear-gradient(to right, {{VALUE}}, #F291D800 10%, #F291D800 90%, {{VALUE}}) !important',
				),
			)
		);

		$this->end_controls_section();
	}

	private function add_light_box_controls() {
		$this->start_controls_section(
			'media_lightbox_section',
			array(
				'label' => __( 'Lightbox', 'premium-addons-for-elementor' ),
			)
		);

		$this->add_control(
			'media_light_box',
			array(
				'label'     => __( 'Lightbox', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::SWITCHER,
				'separator' => 'before',
			)
		);

		$this->add_control(
			'media_lightbox_type',
			array(
				'label'     => __( 'Lightbox Style', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'default',
				'options'   => array(
					'default' => __( 'PrettyPhoto', 'premium-addons-for-elementor' ),
					'yes'     => __( 'Elementor', 'premium-addons-for-elementor' ),
					'no'      => __( 'Other Lightbox Plugin', 'premium-addons-for-elementor' ),
				),
				'condition' => array(
					'media_light_box' => 'yes',
				),
			)
		);

		$this->add_control(
			'lightbox_show_title',
			array(
				'label'     => __( 'Show Image Title', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::SWITCHER,
				'default'   => 'yes',
				'condition' => array(
					'media_light_box'     => 'yes',
					'media_lightbox_type' => 'yes',
				),
			)
		);

		$this->add_control(
			'media_lightbox_theme',
			array(
				'label'     => __( 'Lightbox Theme', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::SELECT,
				'options'   => array(
					'pp_default'    => __( 'Default', 'premium-addons-for-elementor' ),
					'light_rounded' => __( 'Light Rounded', 'premium-addons-for-elementor' ),
					'dark_rounded'  => __( 'Dark Rounded', 'premium-addons-for-elementor' ),
					'light_square'  => __( 'Light Square', 'premium-addons-for-elementor' ),
					'dark_square'   => __( 'Dark Square', 'premium-addons-for-elementor' ),
					'facebook'      => __( 'Facebook', 'premium-addons-for-elementor' ),
				),
				'default'   => 'pp_default',
				'condition' => array(
					'media_light_box'     => 'yes',
					'media_lightbox_type' => 'default',
				),
			)
		);

		$this->add_control(
			'media_overlay_gallery',
			array(
				'label'     => __( 'Overlay Gallery Images', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::SWITCHER,
				'condition' => array(
					'media_light_box'     => 'yes',
					'media_lightbox_type' => 'default',
				),
			)
		);

		$this->add_control(
			'media_lightbox_icon',
			array(
				'label'     => __( 'Lightbox Icon', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::ICONS,
				'default'   => array(
					'library' => 'fa-solid',
					'value'   => 'fas fa-search',
				),
				'condition' => array(
					'media_light_box' => 'yes',
				),
			)
		);

		$this->end_controls_section();
	}

	private function add_img_style_controls() {

		$this->start_controls_section(
			'media_img_style_sec',
			array(
				'label' => __( 'Images', 'premium-addons-for-elementor' ),
				'tab'   => Controls_Manager::TAB_STYLE,
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
				'selector' => '{{WRAPPER}} .premium-adv-carousel__vid-overlay, {{WRAPPER}} .premium-adv-carousel__media-wrap img',
			)
		);

		$this->add_group_control(
			Group_Control_Css_Filter::get_type(),
			array(
				'label'    => __( 'Hover CSS Filters', 'premium-addons-for-elementor' ),
				'name'     => 'hover_css_filters',
				'selector' => '{{WRAPPER}} .premium-adv-carousel__vid-overlay:hover, {{WRAPPER}} .premium-adv-carousel__media-wrap:hover img',
			)
		);

		$this->add_control(
			'media_wheel_img_border_radius',
			array(
				'label'      => __( 'Border Radius', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', '%' ),
				'range'      => array(
					'px' => array(
						'min'  => 0,
						'max'  => 100,
						'step' => 1,
					),
				),
				'selectors'  => array(
					'{{WRAPPER}} .premium-adv-carousel__media-wrap' => 'border-radius: {{SIZE}}{{UNIT}}',
				),
			)
		);

		$this->end_controls_section();
	}

	private function add_icon_style_controls() {

		$this->start_controls_section(
			'media_icon_style_sec',
			array(
				'label' => __( 'Play Icon', 'premium-addons-for-elementor' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_control(
			'play_icons_color',
			array(
				'label'     => __( 'Icon Color', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .pa-media-icons-wrapper .premium-adv-carousel__video-icon i' => 'color: {{VALUE}}',
					'{{WRAPPER}} .pa-media-icons-wrapper .premium-adv-carousel__video-icon svg' => 'fill: {{VALUE}}',
				),
			)
		);

		$this->add_control(
			'play_icon_bg',
			array(
				'label'     => __( 'Background Color', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .pa-media-icons-wrapper .premium-adv-carousel__video-icon' => 'background-color: {{VALUE}}',
				),
			)
		);

		$this->add_control(
			'play_icon_rad',
			array(
				'label'      => __( 'Border Radius', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', '%' ),
				'range'      => array(
					'px' => array(
						'min'  => 0,
						'max'  => 100,
						'step' => 1,
					),
				),
				'selectors'  => array(
					'{{WRAPPER}} .pa-media-icons-wrapper .premium-adv-carousel__video-icon' => 'border-radius: {{SIZE}}{{UNIT}}',
				),
			)
		);

		$this->add_responsive_control(
			'play_icon_padding',
			array(
				'label'      => __( 'Padding', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px' ),
				'range'      => array(
					'px' => array(
						'min'  => 0,
						'max'  => 500,
						'step' => 1,
					),
				),
				'default'    => array(
					'unit' => 'px',
					'size' => 0,
				),
				'selectors'  => array(
					'{{WRAPPER}} .pa-media-icons-wrapper .premium-adv-carousel__video-icon' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_section();
	}

	private function add_info_style_controls() {

		$this->start_controls_section(
			'media_ifno_style_sec',
			array(
				'label' => __( 'Media Info', 'premium-addons-for-elementor' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_control(
			'media_title_style',
			array(
				'label' => __( 'Title', 'premium-addons-for-elementor' ),
				'type'  => Controls_Manager::HEADING,
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'title_typography',
				'selector' => '{{WRAPPER}} .premium-adv-carousel__media-title',
			)
		);

		$this->add_control(
			'title_color',
			array(
				'label'     => __( 'Color', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'global'    => array(
					'default' => Global_Colors::COLOR_PRIMARY,
				),
				'selectors' => array(
					'{{WRAPPER}} .premium-adv-carousel__media-title' => 'color: {{VALUE}}',
				),
			)
		);

		$this->add_group_control(
			Group_Control_text_Shadow::get_type(),
			array(
				'name'     => 'title_shadow',
				'selector' => '{{WRAPPER}} .premium-adv-carousel__media-title',
			)
		);

		$this->add_responsive_control(
			'title_margin',
			array(
				'label'      => __( 'Margin', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%', 'em' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-adv-carousel__media-title' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'media_desc_style',
			array(
				'label'     => __( 'Description', 'premium-addons-for-elementor' ),
				'separator' => 'before',
				'type'      => Controls_Manager::HEADING,
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'desc_typography',
				'selector' => '{{WRAPPER}} .premium-adv-carousel__media-desc',
			)
		);

		$this->add_control(
			'desc_color',
			array(
				'label'     => __( 'Color', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'global'    => array(
					'default' => Global_Colors::COLOR_PRIMARY,
				),
				'selectors' => array(
					'{{WRAPPER}} .premium-adv-carousel__media-desc' => 'color: {{VALUE}}',
				),
			)
		);

		$this->add_group_control(
			Group_Control_text_Shadow::get_type(),
			array(
				'name'     => 'desc_shadow',
				'selector' => '{{WRAPPER}} .premium-adv-carousel__media-desc',
			)
		);

		$this->add_responsive_control(
			'desc_margin',
			array(
				'label'      => __( 'Margin', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%', 'em' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-adv-carousel__media-desc' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'media_info_gen_style',
			array(
				'label'     => __( 'Container', 'premium-addons-for-elementor' ),
				'separator' => 'before',
				'type'      => Controls_Manager::HEADING,
			)
		);

		$this->add_control(
			'media_info_align',
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
				'default'   => 'flex-start',
				'toggle'    => false,
				'selectors' => array(
					'{{WRAPPER}} .premium-adv-carousel__media-info-wrap' => 'text-align: {{VALUE}}',
				),
			)
		);

		$this->add_control(
			'media_info_pos',
			array(
				'label'                => __( 'Placement', 'premium-addons-for-elementor' ),
				'type'                 => Controls_Manager::CHOOSE,
				'options'              => array(
					'before'  => array(
						'title' => __( 'Before Image', 'premium-addons-for-elementor' ),
						'icon'  => 'eicon-v-align-top',
					),
					'overlay' => array(
						'title' => __( 'Overlay', 'premium-addons-for-elementor' ),
						'icon'  => 'eicon-copy',
					),
					'after'   => array(
						'title' => __( 'After Image', 'premium-addons-for-elementor' ),
						'icon'  => 'eicon-v-align-bottom',
					),
				),
				'default'              => 'center',
				'selectors_dictionary' => array(
					'before'  => 'order:0',
					'overlay' => 'position: absolute; bottom: 0px; left: 0px; width: 100%',
					'right'   => 'order: 2',
				),
				'toggle'               => false,
				'selectors'            => array(
					'{{WRAPPER}} .premium-adv-carousel__media-info-wrap' => '{{VALUE}}',
				),
			)
		);

		$this->add_control(
			'media_info_gutter',
			array(
				'label'      => __( 'Position', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', '%' ),
				'range'      => array(
					'px' => array(
						'min'  => 0,
						'max'  => 100,
						'step' => 1,
					),
				),
				'selectors'  => array(
					'{{WRAPPER}} .premium-adv-carousel__media-info-wrap' => 'transform: translateY( {{SIZE}}{{UNIT}} )',
				),
				'condition'  => array(
					'media_info_pos' => 'overlay',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			array(
				'name'     => 'media_info_bg',
				'types'    => array( 'classic', 'gradient' ),
				'selector' => '.premium-adv-carousel__media-info-wrap',
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'     => 'media_info_border',
				'label'    => __( 'Border', 'premium-addons-for-elementor' ),
				'selector' => '{{WRAPPER}} .premium-adv-carousel__media-info-wrap',
			)
		);

		$this->add_control(
			'media_info_rad',
			array(
				'label'      => __( 'Border Radius', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', '%' ),
				'range'      => array(
					'px' => array(
						'min'  => 0,
						'max'  => 100,
						'step' => 1,
					),
				),
				'selectors'  => array(
					'{{WRAPPER}} .premium-adv-carousel__media-info-wrap' => 'border-radius: {{SIZE}}{{UNIT}}',
				),
			)
		);

		$this->add_responsive_control(
			'media_info_padding',
			array(
				'label'      => __( 'Padding', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%', 'em' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-adv-carousel__media-info-wrap' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'media_info_margin',
			array(
				'label'      => __( 'Margin', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%', 'em' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-adv-carousel__media-info-wrap' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_section();
	}

	private function add_items_style_controls() {

		$this->start_controls_section(
			'media_wheel_items_style_tab',
			array(
				'label' => __( 'Items', 'premium-addons-for-elementor' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_group_control(
			Group_Control_Css_Filter::get_type(),
			array(
				'name'      => 'item_css_filters',
				'selector'  => '{{WRAPPER}} .premium-adv-carousel__item-outer-wrapper.flipster__item--current',
				'condition' => array(
					'media_wheel_animation!' => 'infinite',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name'     => 'media_wheel_item_shadow',
				'label'    => __( 'Box Shadow', 'premium-addons-for-elementor' ),
				'selector' => '{{WRAPPER}}.premium-adv-carousel__infinite .premium-adv-carousel__item, {{WRAPPER}}:not(.premium-adv-carousel__infinite) .flipster__item--current .premium-adv-carousel__item',
			)
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			array(
				'name'     => 'item_bg',
				'types'    => array( 'classic', 'gradient' ),
				'selector' => '{{WRAPPER}} .premium-adv-carousel__item',
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'     => 'media_wheel_item_border',
				'label'    => __( 'Border', 'premium-addons-for-elementor' ),
				'selector' => '{{WRAPPER}}.premium-adv-carousel__infinite .premium-adv-carousel__item, {{WRAPPER}}:not(.premium-adv-carousel__infinite) .flipster__item--current .premium-adv-carousel__item',
			)
		);

		$this->add_control(
			'item_border_rad',
			array(
				'label'      => __( 'Border Radius', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', '%' ),
				'range'      => array(
					'px' => array(
						'min'  => 0,
						'max'  => 100,
						'step' => 1,
					),
				),
				'selectors'  => array(
					'{{WRAPPER}} .premium-adv-carousel__item' => 'border-radius: {{SIZE}}{{UNIT}}',
				),
			)
		);

		$this->add_responsive_control(
			'item_padding',
			array(
				'label'      => __( 'Padding', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px' ),
				'range'      => array(
					'px' => array(
						'min'  => 0,
						'max'  => 500,
						'step' => 1,
					),
				),
				'default'    => array(
					'unit' => 'px',
					'size' => 0,
				),
				'selectors'  => array(
					'{{WRAPPER}} .premium-adv-carousel__item' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'item_margin',
			array(
				'label'      => __( 'Margin', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px' ),
				'range'      => array(
					'px' => array(
						'min'  => 0,
						'max'  => 500,
						'step' => 1,
					),
				),
				'default'    => array(
					'unit' => 'px',
					'size' => 0,
				),
				'selectors'  => array(
					'{{WRAPPER}} .premium-adv-carousel__item' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'non_active_item_style',
			array(
				'label'     => __( 'Non-active Items', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
				'condition' => array(
					'media_wheel_animation!' => 'infinite',
				),
			)
		);

		$this->add_control(
			'item_opacity_switched',
			array(
				'label'      => __( 'Opacity', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array(
					'px' => array(
						'min'  => 0,
						'max'  => 1,
						'step' => 0.1,
					),
				),
				'selectors'  => array(
					'{{WRAPPER}} .premium-adv-carousel__item-outer-wrapper:not(.flipster__item--current)' => 'opacity: {{SIZE}}',
				),
				'conditions' => array(
					'relation' => 'or',
					'terms'    => array(
						array(
							'name'     => 'media_wheel_animation',
							'operator' => 'in',
							'value'    => array( 'coverflow', 'carousel' ),
						),
						array(
							'terms' => array(
								array(
									'name'     => 'media_wheel_animation',
									'operator' => '===',
									'value'    => 'flat',
								),
								array(
									'name'     => 'gradual_scale_effect',
									'operator' => '!==',
									'value'    => 'yes',
								),
							),
						),
					),
				),
			)
		);

		$this->add_group_control(
			Group_Control_Css_Filter::get_type(),
			array(
				'name'      => 'item_css_filters_switched',
				'selector'  => '{{WRAPPER}} .premium-adv-carousel__item-outer-wrapper:not(.flipster__item--current)',
				'condition' => array(
					'media_wheel_animation!' => 'infinite',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'      => 'media_wheel_item_border_switched',
				'label'     => __( 'Border', 'premium-addons-for-elementor' ),
				'selector'  => '{{WRAPPER}} .premium-adv-carousel__item-outer-wrapper:not(.flipster__item--current) .premium-adv-carousel__item',
				'condition' => array(
					'media_wheel_animation!' => 'infinite',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name'      => 'media_wheel_item_shadow_switched',
				'label'     => __( 'Box Shadow', 'premium-addons-for-elementor' ),
				'selector'  => '{{WRAPPER}} .premium-adv-carousel__item-outer-wrapper:not(.flipster__item--current) .premium-adv-carousel__item',
				'condition' => array(
					'media_wheel_animation!' => 'infinite',
				),
			)
		);

		$this->end_controls_section();
	}

	private function add_navigation_style() {

		$this->start_controls_section(
			'pa_nav_style',
			array(
				'label'     => __( 'Arrows', 'premium-addons-for-elementor' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => array(
					'media_wheel_animation!' => 'infinite',
					'arrows'                 => 'yes',
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
					'{{WRAPPER}} .flipster__button i'   => 'font-size: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .flipster__button svg' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};',
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
					'default' => Global_Colors::COLOR_PRIMARY,
				),
				'selectors' => array(
					'{{WRAPPER}} .flipster__button i' => 'color: {{VALUE}};',
					'{{WRAPPER}} .flipster__button svg, {{WRAPPER}} .flipster__button svg *' => 'fill: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'pa_nav_opacity',
			array(
				'label'      => __( 'Opacity', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array(
					'px' => array(
						'min'  => 0,
						'max'  => 1,
						'step' => 0.1,
					),
				),
				'selectors'  => array(
					'{{WRAPPER}} .flipster__button' => 'opacity: {{SIZE}}',
				),
			)
		);

		$this->add_control(
			'pa_nav_bg',
			array(
				'label'     => __( 'Background Color', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'global'    => array(
					'default' => Global_Colors::COLOR_SECONDARY,
				),
				'selectors' => array(
					'{{WRAPPER}} .flipster__button' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'     => 'pa_nav_border',
				'selector' => '{{WRAPPER}} .flipster__button',
			)
		);

		$this->add_control(
			'pa_nav_border_radius',
			array(
				'label'      => __( 'Border Radius', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', '%', 'em' ),
				'selectors'  => array(
					'{{WRAPPER}} .flipster__button' => 'border-radius: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'pa_nav_hover',
			array(
				'label' => __( 'Hover', 'premium-addons-for-elementor' ),

			)
		);

		$this->add_control(
			'pa_nav_color_hov',
			array(
				'label'     => __( 'Color', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .flipster__button:hover i' => 'color: {{VALUE}};',
					'{{WRAPPER}} .flipster__button:hover svg, {{WRAPPER}} .flipster__button:hover svg *' => 'fill: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'pa_nav_opacity_hov',
			array(
				'label'      => __( 'Opacity', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array(
					'px' => array(
						'min'  => 0,
						'max'  => 1,
						'step' => 0.1,
					),
				),
				'selectors'  => array(
					'{{WRAPPER}} .flipster__button:hover' => 'opacity: {{SIZE}}',
				),
			)
		);

		$this->add_control(
			'pa_nav_bg_hov',
			array(
				'label'     => __( 'Background Color', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .flipster__button:hover' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'     => 'pa_nav_border_hov',
				'selector' => '{{WRAPPER}} .flipster__button:hover',
			)
		);

		$this->add_control(
			'pa_nav_border_radius_hov',
			array(
				'label'      => __( 'Border Radius', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', '%', 'em' ),
				'selectors'  => array(
					'{{WRAPPER}} .flipster__button:hover' => 'border-radius: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->add_responsive_control(
			'pa_nav_pos_hor',
			array(
				'label'      => __( 'Horizontal Position', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array(
					'px' => array(
						'min'  => -500,
						'max'  => 500,
						'step' => 1,
					),
				),
				'separator'  => 'before',
				'selectors'  => array(
					'{{WRAPPER}} .flipster__button.flipster__button--prev'  => 'left: {{SIZE}}px;',
					'{{WRAPPER}} .flipster__button.flipster__button--next'  => 'right: {{SIZE}}px;',
				),
			)
		);

		$this->add_responsive_control(
			'pa_nav_pos_ver',
			array(
				'label'      => __( 'Vertical Position', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', '%' ),
				'range'      => array(
					'px' => array(
						'min'  => -500,
						'max'  => 500,
						'step' => 1,
					),
					'%'  => array(
						'min'  => -100,
						'max'  => 100,
						'step' => 1,
					),
				),
				'selectors'  => array(
					'{{WRAPPER}} .flipster__button' => 'top: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'pa_nav_padding',
			array(
				'label'      => __( 'Padding', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array(
					'px' => array(
						'min'  => 0,
						'max'  => 500,
						'step' => 1,
					),
				),
				'selectors'  => array(
					'{{WRAPPER}} .flipster__button' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_section();
	}

	private function add_light_box_icon_style() {
		$this->start_controls_section(
			'lighbox_icon_style',
			array(
				'label' => __( 'Lightbox Icon', 'premium-addons-for-elementor' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_responsive_control(
			'lighbox_icon_position',
			array(
				'label'       => __( 'Position', 'premium-addons-for-elementor' ),
				'type'        => Controls_Manager::SLIDER,
				'size_units'  => array( 'px', '%', 'em', 'custom' ),
				'range'       => array(
					'px' => array(
						'min' => 0,
						'max' => 300,
					),
				),
				'label_block' => true,
				'selectors'   => array(
					'{{WRAPPER}} .premium-adv-carousel__item .pa-media-icons-inner-container' => 'top: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'lighbox_icon_size',
			array(
				'label'       => __( 'Size', 'premium-addons-for-elementor' ),
				'type'        => Controls_Manager::SLIDER,
				'size_units'  => array( 'px', 'em' ),
				'range'       => array(
					'px' => array(
						'min' => 1,
						'max' => 50,
					),
				),
				'default'     => array(
					'size' => 20,
					'unit' => 'px',
				),
				'label_block' => true,
				'selectors'   => array(
					'{{WRAPPER}} .pa-media-icons-inner-container i' => 'font-size: {{SIZE}}{{UNIT}}',
					'{{WRAPPER}} .pa-media-icons-inner-container svg' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}}',
				),
			)
		);

		$this->add_responsive_control(
			'lighbox_icon_style_padding',
			array(
				'label'      => __( 'Padding', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .pa-media-magnific-image span' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->start_controls_tabs( 'lighbox_icon_style_tabs' );

		$this->start_controls_tab(
			'lighbox_icon_style_normal',
			array(
				'label' => __( 'Normal', 'premium-addons-for-elementor' ),
			)
		);

		$this->add_control(
			'lighbox_icon_style_color',
			array(
				'label'     => __( 'Color', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'global'    => array(
					'default' => Global_Colors::COLOR_PRIMARY,
				),
				'selectors' => array(
					'{{WRAPPER}} .pa-media-magnific-image i' => 'color: {{VALUE}};',
					'{{WRAPPER}} .pa-media-magnific-image svg' => 'fill: {{VALUE}}; color: {{VALUE}}',
				),
			)
		);

		$this->add_control(
			'lighbox_icon_style_background',
			array(
				'label'     => __( 'Background Color', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'global'    => array(
					'default' => Global_Colors::COLOR_SECONDARY,
				),
				'selectors' => array(
					'{{WRAPPER}} .pa-media-magnific-image span' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'     => 'lighbox_icon_style_border',
				'selector' => '{{WRAPPER}} .pa-media-magnific-image span',
			)
		);

		$this->add_control(
			'lighbox_icon_style_border_radius',
			array(
				'label'      => __( 'Border Radius', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .pa-media-magnific-image span' => 'border-radius: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'label'    => __( 'Shadow', 'premium-addons-for-elementor' ),
				'name'     => 'lighbox_icon_style_shadow',
				'selector' => '{{WRAPPER}} .pa-media-magnific-image span',
			)
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'lighbox_icon_style_hover',
			array(
				'label' => __( 'Hover', 'premium-addons-for-elementor' ),
			)
		);

		$this->add_control(
			'lighbox_icon_style_color_hover',
			array(
				'label'     => __( 'Color', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'global'    => array(
					'default' => Global_Colors::COLOR_PRIMARY,
				),
				'selectors' => array(
					'{{WRAPPER}} .pa-media-magnific-image:hover i' => 'color: {{VALUE}}',
					'{{WRAPPER}} .pa-media-magnific-image:hover svg' => 'fill: {{VALUE}}; color: {{VALUE}}',
				),
			)
		);

		$this->add_control(
			'lighbox_icon_style_background_hover',
			array(
				'label'     => __( 'Background Color', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'global'    => array(
					'default' => Global_Colors::COLOR_SECONDARY,
				),
				'selectors' => array(
					'{{WRAPPER}} .pa-media-magnific-image:hover span' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'     => 'lighbox_icon_style_border_hover',
				'selector' => '{{WRAPPER}} .pa-media-magnific-image:hover span',
			)
		);

		$this->add_control(
			'lighbox_icon_style_border_radius_hover',
			array(
				'label'      => __( 'Border Radius', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .pa-media-magnific-image:hover span' => 'border-radius: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'label'    => __( 'Shadow', 'premium-addons-for-elementor' ),
				'name'     => 'lighbox_icon_style_shadow_hover',
				'selector' => '{{WRAPPER}} .pa-media-magnific-image:hover span',
			)
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();
	}

	private function add_light_box_style() {

		$this->start_controls_section(
			'section_lightbox_style',
			array(
				'label'     => __( 'Lightbox', 'premium-addons-for-elementor' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => array(
					'media_lightbox_type' => 'yes',
				),
			)
		);

		$this->add_control(
			'lightbox_color',
			array(
				'label'     => __( 'Background Color', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'#elementor-lightbox-slideshow-{{ID}}, #elementor-lightbox-{{ID}}' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'lightbox_ui_color',
			array(
				'label'     => __( 'UI Color', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'#elementor-lightbox-slideshow-{{ID}} .dialog-lightbox-close-button, #elementor-lightbox-{{ID}} .dialog-lightbox-close-button' => 'color: {{VALUE}};',
					'#elementor-lightbox-slideshow-{{ID}} svg, #elementor-lightbox-{{ID}} svg' => 'fill: {{VALUE}}',
				),
			)
		);

		$this->add_control(
			'lightbox_ui_hover_color',
			array(
				'label'     => __( 'UI Hover Color', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'#elementor-lightbox-slideshow-{{ID}} .dialog-lightbox-close-button:hover, #elementor-lightbox-{{ID}} .dialog-lightbox-close-button:hover' => 'color: {{VALUE}};',
					'#elementor-lightbox-slideshow-{{ID}} svg:hover, #elementor-lightbox-{{ID}} svg:hover' => 'fill: {{VALUE}}',
				),
			)
		);

		$this->end_controls_section();
	}

	/**
	 * Render Advanced Media widِget output on the frontend.
	 *
	 * Written in PHP and used to generate the final HTML.
	 *
	 * @access protected
	 */
	protected function render() {

		$settings = $this->get_settings_for_display();

		$papro_activated = apply_filters( 'papro_activated', false );

		if ( ! $papro_activated || version_compare( PREMIUM_PRO_ADDONS_VERSION, '2.9.6', '<' ) ) {

			if ( 'infinite' !== $settings['media_wheel_animation'] ) {

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

		$items = $settings['media_wheel_repeater'];

		$direction = $settings['media_wheel_direction'];

		$type = $settings['media_wheel_animation'];

		$wheel_settings = array(
			'type'         => $type,
			'scroll'       => 'yes' === $settings['media_wheel_scroll'],
			'speed'        => '' === $settings['media_wheel_speed'] ? 50 : $settings['media_wheel_speed'],
			'pauseOnHover' => 'yes' === $settings['media_wheel_scroll'] ? true : 'yes' === $settings['pause_on_hover'],
		);

		if ( 'infinite' === $type ) {
			$wheel_settings['dir']     = $direction;
			$wheel_settings['reverse'] = $settings['media_wheel_reverse'];
		} else {
			$wheel_settings['loop'] = $settings['media_wheel_loop'];
			$auto_play              = 'yes' === $settings['media_wheel_autoplay'];
			$arrows                 = 'yes' === $settings['arrows'];

			if ( $auto_play ) {
				$wheel_settings['autoPlay'] = '' === $settings['media_wheel_autoplay_speed'] ? 3000 : $settings['media_wheel_autoplay_speed'];
			} else {
				$wheel_settings['autoPlay'] = false;
			}

			$wheel_settings['click']    = 'yes' === $settings['nav_on_click'];
			$wheel_settings['keyboard'] = 'yes' === $settings['nav_by_keyboard'];
			$wheel_settings['touch']    = 'yes' === $settings['nav_by_touch'];
			$wheel_settings['buttons']  = $arrows;
			$wheel_settings['spacing']  = floatval( $settings['media_wheel_spacing']['size'] );
			$wheel_settings['start']    = '' === $settings['media_whee_start'] ? 'center' : $settings['media_whee_start'];

		}

		$this->add_render_attribute( 'inner', 'style', 'visibility:hidden;' );

		$lightbox = $settings['media_light_box'];

		$lightbox_type = $settings['media_lightbox_type'];

		if ( 'yes' === $lightbox ) {

			$wheel_settings = array_merge(
				$wheel_settings,
				array(
					'light_box'     => $lightbox,
					'lightbox_type' => $lightbox_type,
					'overlay'       => 'yes' === $settings['media_overlay_gallery'] ? true : false,
					'theme'         => $settings['media_lightbox_theme'],
				)
			);

		} else {
			$this->add_render_attribute(
				'wheel',
				array(
					'class' => array(
						'premium-adv-carousel-no-lightbox',
					),
				)
			);
		}

		$this->add_render_attribute(
			'wheel',
			array(
				'class'         => 'premium-adv-carousel__container',
				'data-settings' => wp_json_encode( $wheel_settings ),
			)
		);

		$this->add_render_attribute( 'inner', 'class', 'premium-adv-carousel__inner-container' );

		?>
		<div <?php echo wp_kses_post( $this->get_render_attribute_string( 'wheel' ) ); ?>>

			<div <?php echo wp_kses_post( $this->get_render_attribute_string( 'inner' ) ); ?>>

				<div class="premium-adv-carousel__items">

					<?php
						$this->render_wheel_Items( $items );
					if ( 'infinite' !== $type && $arrows ) {
						?>
								<div class="premium-adv-carousel__icons-holder premium-adv-carousel__prev-icon">
								<?php
									Icons_Manager::render_icon(
										$settings['prev_arrow'],
										array(
											'aria-hidden' => 'true',
										)
									);
								?>
								</div>
								<div class="premium-adv-carousel__icons-holder premium-adv-carousel__next-icon">
								<?php
									Icons_Manager::render_icon(
										$settings['next_arrow'],
										array(
											'aria-hidden' => 'true',
										)
									);
								?>
								</div>
							<?php
					}
					?>
				</div>
			</div>
		</div>
		<?php

	}

	/**
	 * Get video thumbnail
	 *
	 * @access public
	 *
	 * @param string $video_id video ID.
	 */
	private function get_thumbnail( $settings, $video_id = '' ) {

		$type = $settings['media_wheel_video_type'];

		$thumbnail_src = $settings['media_wheel_img']['url'];

		if ( ! empty( $thumbnail_src ) || 'self' === $type ) {
			return $thumbnail_src;
		}

		// Check thumbnail size option only for Youtube videos.
		$size = '';
		if ( 'youtube' === $type ) {
			$size = $settings['media_wheel_yt_thumbnail_size'];
		}

		$thumbnail_src = Helper_Functions::get_video_thumbnail( $video_id, $type, $size );

		return $thumbnail_src;
	}

	private function render_wheel_Items( $items ) {

		$widget_settings = $this->get_settings_for_display();

		$papro_activated = apply_filters( 'papro_activated', false );

		$lightbox = $widget_settings['media_light_box'];

		if ( $papro_activated ) {

			$hover_effect = 'premium-hover-effects__' . $widget_settings['image_hover_effect'];

		} else {
			$hover_effect = '';
		}

		foreach ( $items as $index => $item ) {

			$media_type = $item['pa_media_type'];

			$image_id = apply_filters( 'wpml_object_id', $item['media_wheel_img']['id'], 'elementor_library', true );

			$image_by_id = get_post( $image_id );

			$alt = '';

			if ( isset( $image_by_id->post_title ) ) {
				$alt = apply_filters( 'pa_media_alt', get_post( $image_id )->post_title );
			}

			if ( 'template' === $media_type ) {
				$hover_effect = '';
			}

			$media_info = 'image' === $media_type && 'yes' === $item['media_info'];

			$this->add_render_attribute(
				'wheel_item' . $index,
				array(
					'class' => array(
						'elementor-repeater-item-' . $item['_id'],
						'premium-adv-carousel__item',
						'premium-adv-carousel__item-' . $media_type,
					),
				)
			);

			?>
			<div class="premium-adv-carousel__item-outer-wrapper">
				<div <?php echo wp_kses_post( $this->get_render_attribute_string( 'wheel_item' . $index ) ); ?>>
					<div class="premium-adv-carousel__media-wrap">
						<?php
						if ( 'image' === $media_type ) {

							$this->add_render_attribute(
								'wheel_img' . $index,
								array(
									'src'   => esc_url( $item['media_wheel_img']['url'] ),
									'alt'   => esc_attr( Control_Media::get_image_alt( $item['media_wheel_img'] ) ),
									'class' => 'premium-adv-carousel__item-img ' . $hover_effect,
								)
							);

							?>
								<img <?php echo wp_kses_post( $this->get_render_attribute_string( 'wheel_img' . $index ) ); ?>>
							<?php
						} elseif ( 'video' === $media_type ) {
								$video_data         = $this->render_carousel_video( $item, $index, $hover_effect );
								$item['video_link'] = $video_data;
						} else {
							$template_name = empty( $item['section_template'] ) ? $item['live_temp_content'] : $item['section_template'];
							?>
								<div class="premium-adv-carousel__template-wrapper">
									<?php echo ( $this->getTemplateInstance()->get_template_content( $template_name ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
								</div>
							<?php
						}

						if ( 'yes' === $item['media_wheel_link_switcher'] ) {
							$link = '';

							$target = ( 'yes' === $item['media_wheel_link_new_tab'] ) ? 'target=_blank' : '';

							if ( 'link' === $item['media_wheel_link_type'] ) {
								$link = get_permalink( $item['media_wheel_existing_link'] );
							} else {
								$link = $item['media_wheel_custom_link']['url'];
							}

							echo '<a class="premium-adv-carousel__item-link" href="' . esc_url( $link ) . '" ' . esc_html( $target ) . '></a>';
						}

						?>
					</div>

					<?php
					if ( 'yes' === $lightbox ) {
						?>
						<div class="pa-media-icons-wrapper">
							<div class="pa-media-icons-inner-container">
								<?php $this->render_icons( $item, $index, $alt ); ?>
							</div>
						</div>
						<?php
					}
					?>

					<?php
					if ( $media_info ) {
						?>
						<div class="premium-adv-carousel__media-info-wrap">
							<?php
							if ( ! empty( $item['media_title'] ) ) {
								?>
									<span class="premium-adv-carousel__media-title"><?php echo esc_html( $item['media_title'] ); ?></span>
								<?php
							}

							if ( ! empty( $item['media_desc'] ) ) {
								?>
									<span class="premium-adv-carousel__media-desc"><?php echo esc_html( $item['media_desc'] ); ?></span>
								<?php
							}
							?>
						</div>
						<?php
					}
					?>
				</div>
			</div>
			<?php
		}
	}

	/**
	 * Render carousel Videos
	 *
	 * Written in PHP and used to generate the final HTML for video items.
	 *
	 * @access private
	 *
	 * @param array  $item  repeater item.
	 * @param number $index item index.
	 */
	private function render_carousel_video( $item, $index, $hover_effect ) {

		$videoType = $item['media_wheel_video_type'];

		$video_thumb = $item['media_wheel_img']['url'];

		$video_alt = Control_Media::get_image_alt( $item['media_wheel_img'] );

		$params = $this->get_embed_params( $item );

		$link = Embed::get_embed_url( $item['media_wheel_video_link'], $params );

		if ( 'hosted' !== $videoType ) {

			$video_props = Embed::get_video_properties( $link );
			$id          = $video_props['video_id'];
			$type        = $video_props['provider'];
			$size        = '';
			$thumbnail   = $this->get_thumbnail( $item, $id );

		} else {
			self::$check_self_hosted = true;
			$params                  = $this->get_hosted_params( $item );
			$thumbnail               = '';
		}

		$thumbnail = empty( $thumbnail ) ? $video_thumb : $thumbnail;

		?>
			<div class="premium-adv-carousel__video-wrap"  data-type="<?php echo esc_html( $item['media_wheel_video_type'] ); ?>" <?php echo wp_kses_post( $this->get_render_attribute_string( 'video_container' ) ); ?>>
				<?php if ( 'hosted' !== $item['media_wheel_video_type'] ) : ?>
					<div class="premium-adv-carousel__iframe-wrap" data-src="<?php echo esc_url( $link ); ?>"></div>
					<?php
				else :
					$link = empty( $item['media_wheel_video_self_url'] ) ? $item['media_wheel_video_self']['url'] : $item['media_wheel_video_self_url'];

					?>
					<video src="<?php echo esc_url( $link ); ?>" <?php echo wp_kses_post( Utils::render_html_attributes( $params ) ); ?>></video>
				<?php endif; ?>
				<img  class="premium-adv-carousel__vid-overlay <?php echo $hover_effect; ?>" src="<?php echo esc_url( $thumbnail ); ?>" alt="<?php echo esc_attr( $video_alt ); ?>">

			</div>
		<?php

		return ( isset( $link ) && ! empty( $link ) ) ? $link : false;
	}

	/**
	 * Get embeded videos parameters
	 *
	 * @access private
	 *
	 * @param array $item  repeater item.
	 */
	private function get_embed_params( $item ) {

		$video_params = array();

		$video_params['controls'] = $item['media_wheel_video_controls'] ? '1' : '0';

		$key = 'youtube' === $item['media_wheel_video_type'] ? 'mute' : 'muted';

		$video_params[ $key ] = $item['media_wheel_video_mute'] ? '1' : '0';

		if ( 'vimeo' === $item['media_wheel_video_type'] ) {
			$video_params['autopause'] = '0';
		}

		return $video_params;
	}

	/**
	 * Get Hosted Videos Parameters
	 *
	 * @access private
	 *
	 * @param array $item repeater item.
	 */
	private function get_hosted_params( $item ) {

		$video_params = array();

		if ( $item['media_wheel_video_controls'] ) {
			$video_params['controls'] = '';
		}

		if ( $item['media_wheel_video_mute'] ) {
			$video_params['muted'] = 'muted';
		}

		if ( $item['media_wheel_video_loop'] ) {
			$video_params['loop'] = '';
		}

		return $video_params;
	}

	/**
	 * Render Icons
	 *
	 * Render Lightbox and URL Icons HTML
	 *
	 * @since 3.6.4
	 * @access protected
	 *
	 * @param array   $item grid image repeater item.
	 * @param integer $index item index.
	 * @param string  $alt image alternative text.
	 */
	private function render_icons( $item, $index, $alt ) {

		$settings = $this->get_settings_for_display();

		$lightbox_key = 'image_lightbox_' . $index;

		$link_key = 'image_link_' . $index;

		$href = $item['media_wheel_img']['url'];

		$lightbox = $settings['media_light_box'];

		$lightbox_type = $settings['media_lightbox_type'];

		$media_type = $item['pa_media_type'];

		$id = $this->get_id();

		$play_icon_enabled = 'yes' === $item['media_wheel_video_icon_switcher'];

		if ( 'video' === $media_type ) {

			$type = $item['media_wheel_video_type'];

			$this->add_render_attribute(
				$lightbox_key,
				array(
					'class'    => array(
						'pa-media-lightbox-wrap',
						'pa-media-magnific-image',
						'pa-media-video-icon',
					),
					'tabindex' => 0,
				)
			);

			$lightbox_options = array(
				'privacy' => 'yes',
			);

			if ( 'default' !== $lightbox_type ) {

				if ( 1 === count( $settings['media_wheel_repeater'] ) || self::$check_self_hosted ) {

						$lightbox_options = array(
							'type'         => 'video',
							'videoType'    => $item['media_wheel_video_type'],
							'url'          => $item['video_link'],
							'modalOptions' => array(
								'id'               => 'elementor-lightbox-' . $id,
								'videoAspectRatio' => '169',
							),
						);

						if ( 'hosted' === $type ) {
							$lightbox_options['videoParams'] = $this->get_hosted_params( $item );
						}
				}

				$this->add_render_attribute(
					$lightbox_key,
					array(
						'data-elementor-open-lightbox'  => 'yes',
						'data-elementor-lightbox'       => wp_json_encode( $lightbox_options ),
						'data-elementor-lightbox-video' => $item['video_link'],
					)
				);

				// Make sure videos slideshow is enabled only when there are no self hosted videos
				// Self hosted videos causes issue with slideshow
				if ( ! self::$check_self_hosted ) {
					$this->add_render_attribute( $lightbox_key, 'data-elementor-lightbox-slideshow', count( $settings['media_wheel_repeater'] ) > 1 ? $this->get_id() : false );
				}
			} else {

				$rel = sprintf( 'prettyPhoto[premium-media-%s]', $this->get_id() );

				$link = ( 'hosted' === $type ) ? $item['video_link'] : $item['video_link'] . '&iframe=true';

				$this->add_render_attribute(
					$lightbox_key,
					array(
						'href'     => $link,
						'data-rel' => $rel,
					)
				);
			}

			if ( $play_icon_enabled ) {
				?>
				<div>
					<a <?php echo wp_kses_post( $this->get_render_attribute_string( $lightbox_key ) ); ?>>
						<span class="premium-adv-carousel__video-icon">
						<?php
							Icons_Manager::render_icon( $item['media_wheel_videos_icon'], array( 'aria-hidden' => 'true' ) );
						?>
						</span>
					</a>
				</div>
				<?php
			}

			if ( 'yes' !== $item['media_wheel_video_icon_switcher'] ) {
				?>
				<div>
					<a <?php echo wp_kses_post( $this->get_render_attribute_string( $lightbox_key ) ); ?>>
						<span>
							<?php
							Icons_Manager::render_icon( $settings['media_lightbox_icon'], array( 'aria-hidden' => 'true' ) );
							?>
						</span>
					</a>
				</div>
				<?php
			}
			return;
		}

		if ( 'yes' !== $item['media_wheel_link_switcher'] ) {

			$this->add_render_attribute(
				$lightbox_key,
				array(
					'class' => 'pa-media-magnific-image',
					'href'  => $href,
				)
			);

			if ( 'default' !== $lightbox_type ) {

				$this->add_render_attribute(
					$lightbox_key,
					array(
						'data-elementor-open-lightbox' => $lightbox_type,
						'data-elementor-lightbox-slideshow' => count( $settings['media_wheel_repeater'] ) > 1 ? $id : false,
					)
				);

				if ( 'yes' === $settings['lightbox_show_title'] ) {

					$this->add_render_attribute( $lightbox_key, 'data-elementor-lightbox-title', $alt );

				}
			} else {

				$rel = sprintf( 'prettyPhoto[premium-media-%s]', $this->get_id() );

				$this->add_render_attribute(
					$lightbox_key,
					array(
						'data-rel' => $rel,
					)
				);

			}

			?>

				<a <?php echo wp_kses_post( $this->get_render_attribute_string( $lightbox_key ) ); ?>>
					<span>
						<?php
						Icons_Manager::render_icon( $settings['media_lightbox_icon'], array( 'aria-hidden' => 'true' ) );
						?>
					</span>
				</a>
			<?php
		}

	}

}
