<?php
/**
 * Premium Woo Products.
 */

namespace PremiumAddons\Modules\Woocommerce\Widgets;

// Elementor Classes.
use Elementor\Widget_Base;
use Elementor\Repeater;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Background;
use Elementor\Group_Control_Image_Size;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Css_Filter;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Text_Shadow;

use Elementor\Core\Kits\Documents\Tabs\Global_Colors;
use Elementor\Core\Kits\Documents\Tabs\Global_Typography;

// Premium Addons Classes.
use PremiumAddons\Includes\Helper_Functions;
use PremiumAddons\Includes\Premium_Template_Tags;
use PremiumAddons\Modules\Woocommerce\Skins;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // If this file is called directly, abort.
}

/**
 * Class Woo_Products
 */
class Woo_Products extends Widget_Base {

	/**
	 * Template Instance
	 *
	 * @var template_instance
	 */
	protected $template_instance;

	/**
	 * Query object
	 *
	 * @since 1.5.0
	 * @var object $query
	 */
	public static $query;

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
	 * Whether the widget has content.
	 *
	 * Used in cases where the widget has no content. When widgets uses only
	 * skins to display dynamic content generated on the server.
	 *
	 * @access protected
	 *
	 * @var bool
	 */
	protected $_has_template_content = false;

	/**
	 * Retrieve Widget Name.
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function get_name() {
		return 'premium-woo-products';
	}

	/**
	 * Retrieve Widget Title.
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function get_title() {
		return __( 'Woo Products', 'premium-addons-for-elementor' );
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
			'pa-slick',
			'woocommerce-general',
			'premium-addons',
			'premium-pro',
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
		return array( 'pa', 'premium', 'posts', 'grid', 'item', 'loop', 'woocommerce', 'listing' );
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
			'isotope-js',
			'flexslider',
			'pa-slick',
			'imagesloaded',
			'premium-woocommerce',
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
		return 'pa-woo-products';
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
	 * Register Skins.
	 * Registers woo products widget's skins.
	 *
	 * @access protected
	 */
	protected function register_skins() {
		// FREE SKINS.
		$this->add_skin( new Skins\Skin_1( $this ) );
		$this->add_skin( new Skins\Skin_2( $this ) );
		$this->add_skin( new Skins\Skin_3( $this ) );
		$this->add_skin( new Skins\Skin_4( $this ) );
		// $this->add_skin( new Skins\Skin_5( $this ) );
		$this->add_skin( new Skins\Skin_6( $this ) );

		// PRO SKINS.
		$this->add_skin( new Skins\Skin_7( $this ) );
		$this->add_skin( new Skins\Skin_8( $this ) );
		$this->add_skin( new Skins\Skin_9( $this ) );
		$this->add_skin( new Skins\Skin_10( $this ) );
		$this->add_skin( new Skins\Skin_11( $this ) );
	}

	/**
	 * Register Woo Products controls.
	 *
	 * @since 1.0.0
	 * @access protected
	 */
	protected function register_controls() {

		$papro_activated = apply_filters( 'papro_activated', false );

		$pro_skins = ! $papro_activated ? array( 'grid-7', 'grid-8', 'grid-9', 'grid-10', 'grid-11' ) : array( '' );

		// content tab.
		$this->register_content_general_section( $papro_activated, $pro_skins );
		$this->register_content_grid_section( $pro_skins );
		$this->register_content_carousel_section( $pro_skins );
		$this->register_content_query_section( $pro_skins );
		$this->register_content_pagination_section( $pro_skins );
		$this->register_content_ribbon_section( $pro_skins );

		// style tab.
		$this->register_style_general_section( $pro_skins );
		$this->register_style_image_section( $pro_skins );
		$this->register_style_pagination_section( $pro_skins );
		$this->register_style_loadmore_section( $pro_skins );
		$this->register_style_carousel_section( $pro_skins );
		$this->register_style_sold_out_controls( $pro_skins );
	}

	/**
	 * Register content general section.
	 *
	 * @access public
	 * @since 4.8.3
	 *
	 * @param boolean $papro_activated  true|false.
	 * @param array   $pro_skins   pro skins.
	 */
	public function register_content_general_section( $papro_activated, $pro_skins ) {

		$this->start_controls_section(
			'general_section',
			array(
				'label' => __( 'General', 'premium-addons-for-elementor' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			)
		);

		if ( ! $papro_activated ) {

			$get_pro = Helper_Functions::get_campaign_link( 'https://premiumaddons.com/elementor-woocommerce-products/', 'editor-page', 'wp-editor', 'get-pro' );

			$this->add_control(
				'woo_products_notice',
				array(
					'type'            => Controls_Manager::RAW_HTML,
					'raw'             => __( 'This skin is available in ', 'premium-addons-for-elementor' ) . '<b>Premium Addons Pro. </b>' . '<a href="' . esc_url( $get_pro ) . '" target="_blank">' . __( 'Upgrade now!', 'premium-addons-for-elementor' ) . '</a>',
					'content_classes' => 'papro-upgrade-notice',
					'condition'       => array(
						'_skin' => $pro_skins,
					),
				)
			);
		}

		$this->add_control(
			'layout_type',
			array(
				'label'     => __( 'Layout', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'grid',
				'options'   => array(
					'grid'     => __( 'Grid', 'premium-addons-for-elementor' ),
					// 'metro'    => __( 'Metro', 'premium-addons-for-elementor' ),
					'carousel' => __( 'Carousel', 'premium-addons-for-elementor' ),
				),
				'condition' => array(
					'_skin!' => $pro_skins,
				),
			)
		);

		$this->add_group_control(
			Group_Control_Image_Size::get_type(),
			array(
				'name'      => 'featured_image',
				'default'   => 'full',
				'condition' => array(
					'_skin!' => $pro_skins,
				),
			)
		);

		$this->end_controls_section();
	}

	/**
	 * Register content grid section.
	 *
	 * @access public
	 * @since 4.8.3
	 *
	 * @param array $pro_skins   pro skins.
	 */
	public function register_content_grid_section( $pro_skins ) {
		$this->start_controls_section(
			'section_grid_options',
			array(
				'label'     => __( 'Grid', 'premium-addons-for-elementor' ),
				'condition' => array(
					'layout_type' => array( 'grid', 'metro' ),
					'_skin!'      => $pro_skins,
				),
			)
		);

		$this->add_responsive_control(
			'columns',
			array(
				'label'          => __( 'Products Per Row', 'premium-addons-for-elementor' ),
				'type'           => Controls_Manager::SELECT,
				'options'        => array(
					'100%'    => __( '1 Column', 'premium-addons-for-elementor' ),
					'50%'     => __( '2 Columns', 'premium-addons-for-elementor' ),
					'33.33%'  => __( '3 Columns', 'premium-addons-for-elementor' ),
					'25%'     => __( '4 Columns', 'premium-addons-for-elementor' ),
					'20%'     => __( '5 Columns', 'premium-addons-for-elementor' ),
					'16.667%' => __( '6 Columns', 'premium-addons-for-elementor' ),
				),
				'default'        => '33.33%',
				'tablet_default' => '50%',
				'mobile_default' => '100%',
				'render_type'    => 'template',
				'selectors'      => array(
					'{{WRAPPER}} .premium-woo-products-inner li.product' => 'width: {{VALUE}}',
				),
				'condition'      => array(
					'layout_type' => 'grid',
				),
			)
		);

		$this->add_control(
			'metro_style',
			array(
				'label'       => __( 'Metro Style', 'premium-addons-for-elementor' ),
				'type'        => Controls_Manager::SELECT,
				'options'     => array(
					'style1' => __( 'Style 1', 'premium-addons-for-elementor' ),
					'style2' => __( 'style 2', 'premium-addons-for-elementor' ),
					'style3' => __( 'style 4', 'premium-addons-for-elementor' ),
					'custom' => __( 'Custom', 'premium-addons-for-elementor' ),
				),
				'default'     => 'style1',
				'render_type' => 'template',
				'condition'   => array(
					'layout_type' => 'metro',
				),
			)
		);

		$this->add_responsive_control(
			'width_pattern',
			array(
				'label'       => __( 'Width Pattern', 'premium-addons-for-elementor' ),
				'description' => __( 'Each row is divided into 12 cells. Add widths for the products separated by comma, for example: 6,3,3', 'premium-addons-for-elementor' ),
				'default'     => '6,3,3',
				'type'        => Controls_Manager::TEXT,
				'condition'   => array(
					'layout_type' => 'metro',
					'metro_style' => 'custom',
				),
			)
		);

		$this->add_responsive_control(
			'height_pattern',
			array(
				'label'       => __( 'Height Pattern', 'premium-addons-for-elementor' ),
				'description' => __( 'Each row is divided into 12 cells. Add heights for the products separated by comma, for example: 6,3,3', 'premium-addons-for-elementor' ),
				'default'     => '6,3,3',
				'type'        => Controls_Manager::TEXT,
				'condition'   => array(
					'layout_type' => 'metro',
					'metro_style' => 'custom',
				),
			)
		);

		$this->add_control(
			'products_numbers',
			array(
				'label'       => __( 'Products Per Page', 'premium-addons-for-elementor' ),
				'description' => __( 'Choose how many products do you want to be displayed per page', 'premium-addons-for-elementor' ),
				'type'        => Controls_Manager::NUMBER,
				'min'         => 1,
				'default'     => 6,
			)
		);

		$this->add_control(
			'load_more',
			array(
				'label'     => __( 'Load More Button', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::SWITCHER,
				'condition' => array(
					'pagination!'  => 'yes',
					'layout_type!' => 'carousel',
					'query_type!'  => array( 'cross-sells', 'up-sells' ),
				),
			)
		);

		$this->add_control(
			'load_more_text',
			array(
				'label'     => __( 'Button Text', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::TEXT,
				'default'   => __( 'Load More', 'premium-addons-for-elementor' ),
				'dynamic'   => array( 'active' => true ),
				'condition' => array(
					'load_more'   => 'yes',
					'pagination!' => 'yes',
					'query_type!' => array( 'cross-sells', 'up-sells' ),
				),
			)
		);

		$this->add_responsive_control(
			'load_more_align',
			array(
				'label'     => __( 'Button Alignment', 'premium-addons-for-elementor' ),
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
				'selectors' => array(
					'{{WRAPPER}} .premium-woo-load-more' => 'text-align: {{VALUE}};',
				),
				'condition' => array(
					'load_more'   => 'yes',
					'pagination!' => 'yes',
					'query_type!' => array( 'cross-sells', 'up-sells' ),
				),
			)
		);

		$this->end_controls_section();

	}

	/**
	 * Register content carousel section.
	 *
	 * @access public
	 * @since 4.8.3
	 *
	 * @param array $pro_skins   pro skins.
	 */
	public function register_content_carousel_section( $pro_skins ) {
		$this->start_controls_section(
			'section_carousel_options',
			array(
				'label'     => __( 'Carousel Options', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::SECTION,
				'condition' => array(
					'layout_type' => 'carousel',
					'_skin!'      => $pro_skins,
				),
			)
		);

		$this->add_control(
			'arrows',
			array(
				'label'   => __( 'Show Arrows', 'premium-addons-for-elementor' ),
				'type'    => Controls_Manager::SWITCHER,
				'default' => 'yes',
			)
		);

		$this->add_responsive_control(
			'arrows_pos',
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
					'arrows' => 'yes',
				),
				'selectors'  => array(
					'{{WRAPPER}} .premium-woocommerce a.carousel-arrow.carousel-next' => 'right: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .premium-woocommerce a.carousel-arrow.carousel-prev' => 'left: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'dots',
			array(
				'label'   => __( 'Show Dots', 'premium-addons-for-elementor' ),
				'type'    => Controls_Manager::SWITCHER,
				'default' => 'yes',
			)
		);

		// $this->add_control(
		// 'dots_position',
		// array(
		// 'label'     => __( 'Position', 'premium-addons-for-elementor' ),
		// 'type'      => Controls_Manager::SELECT,
		// 'default'   => 'below',
		// 'options'   => array(
		// 'below' => __( 'Below Slides', 'premium-addons-for-elementor' ),
		// 'above' => __( 'On Slides', 'premium-addons-for-elementor' ),
		// ),
		// 'condition' => array(
		// 'dots' => 'yes',
		// ),
		// )
		// );

		$this->add_responsive_control(
			'dots_hoffset',
			array(
				'label'      => __( 'Horizontal Offset', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-woo-dots-above ul.slick-dots' => 'left: {{SIZE}}{{UNIT}}',
				),
				'condition'  => array(
					'dots'          => 'yes',
					'dots_position' => 'above',
				),
			)
		);

		$this->add_responsive_control(
			'dots_voffset',
			array(
				'label'      => __( 'Vertical Offset', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', 'em', '%' ),
				'default'    => array(
					'unit' => '%',
					'size' => 50,
				),
				'selectors'  => array(
					'{{WRAPPER}} .premium-woo-dots-above ul.slick-dots' => 'top: {{SIZE}}{{UNIT}}',
				),
				'condition'  => array(
					'dots'          => 'yes',
					'dots_position' => 'above',
				),
			)
		);

		$this->add_control(
			'total_carousel_products',
			array(
				'label'     => __( 'Number of Products', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::NUMBER,
				'default'   => '8',
				'condition' => array(
					'query_type!' => 'main',
				),
			)
		);

		$this->add_responsive_control(
			'products_show',
			array(
				'label'              => __( 'Products to Show', 'premium-addons-for-elementor' ),
				'type'               => Controls_Manager::NUMBER,
				'description'        => __( 'Make sure to have the number of products larger than the number of products to show', 'premium-addons-for-elementor' ),
				'default'            => 3,
				'tablet_default'     => 2,
				'mobile_default'     => 1,
				'frontend_available' => true,
			)
		);

		$this->add_responsive_control(
			'products_on_scroll',
			array(
				'label'              => __( 'Products to Scroll', 'premium-addons-for-elementor' ),
				'type'               => Controls_Manager::NUMBER,
				'default'            => 1,
				'frontend_available' => true,
			)
		);

		$this->add_control(
			'autoplay_slides',
			array(
				'label'     => __( 'Autoplay Slides', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::SWITCHER,
				'separator' => 'before',
			)
		);

		$this->add_control(
			'autoplay_speed',
			array(
				'label'     => __( 'Autoplay Interval', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::NUMBER,
				'default'   => 3000,
				'condition' => array(
					'autoplay_slides' => 'yes',
				),
			)
		);

		$this->add_control(
			'hover_pause',
			array(
				'label'     => __( 'Pause on Hover', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::SWITCHER,
				'condition' => array(
					'autoplay_slides' => 'yes',
				),
			)
		);

		$this->add_control(
			'infinite_loop',
			array(
				'label'     => __( 'Infinite Loop', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::SWITCHER,
				'default'   => 'yes',
				'separator' => 'before',
			)
		);

		$this->add_control(
			'speed',
			array(
				'label'     => __( 'Autoplay Speed', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::NUMBER,
				'default'   => 500,
				'condition' => array(
					'autoplay_slides' => 'yes',
				),
			)
		);

		$this->end_controls_section();

	}

	/**
	 * Register content query section.
	 *
	 * @access public
	 * @since 4.8.3
	 *
	 * @param array $pro_skins   pro skins.
	 */
	public function register_content_query_section( $pro_skins ) {
		$this->start_controls_section(
			'section_query_settings',
			array(
				'label'     => __( 'Query', 'premium-addons-for-elementor' ),
				'condition' => array(
					'_skin!' => $pro_skins,
				),
			)
		);

		$this->add_control(
			'query_type',
			array(
				'label'   => __( 'Source', 'premium-addons-for-elementor' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'all',
				'options' => $this->get_queries(),
			)
		);

		$this->add_control(
			'woo_upsells_notice',
			array(
				'type'            => Controls_Manager::RAW_HTML,
				'raw'             => __( 'Upsells query can be used only on single product template.', 'premium-addons-for-elementor' ),
				'content_classes' => 'elementor-panel-alert elementor-panel-alert-info papro-upgrade-notice',
				'condition'       => array(
					'query_type' => 'up-sells',
				),
			)
		);

		$this->add_control(
			'categories_filter_rule',
			array(
				'label'     => __( 'Category Filter Rule', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'IN',
				'options'   => array(
					'IN'     => __( 'Match Categories', 'premium-addons-for-elementor' ),
					'NOT IN' => __( 'Exclude Categories', 'premium-addons-for-elementor' ),
				),
				'condition' => array(
					'query_type' => 'custom',
				),
			)
		);

		$this->add_control(
			'categories',
			array(
				'label'     => __( 'Select Categories', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::SELECT2,
				'multiple'  => true,
				'options'   => Helper_Functions::get_woo_categories(),
				'condition' => array(
					'query_type' => 'custom',
				),
			)
		);

		$this->add_control(
			'tags_filter_rule',
			array(
				'label'     => __( 'Tag Filter Rule', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'IN',
				'options'   => array(
					'IN'     => __( 'Match Tags', 'premium-addons-for-elementor' ),
					'NOT IN' => __( 'Exclude Tags', 'premium-addons-for-elementor' ),
				),
				'condition' => array(
					'query_type' => 'custom',
				),
			)
		);

		$this->add_control(
			'tags',
			array(
				'label'     => __( 'Select Tags', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::SELECT2,
				'multiple'  => true,
				'options'   => $this->get_woo_tags(),
				'condition' => array(
					'query_type' => 'custom',
				),
			)
		);

		$this->add_control(
			'offset',
			array(
				'label'       => __( 'Offset', 'premium-addons-for-elementor' ),
				'type'        => Controls_Manager::NUMBER,
				'default'     => 0,
				'description' => __( 'Set the starting index.', 'premium-addons-for-elementor' ),
				'condition'   => array(
					'query_type' => 'custom',
				),
			)
		);

		$this->add_control(
			'exclude_current_product',
			array(
				'label'       => __( 'Exclude Current Product', 'premium-addons-for-elementor' ),
				'type'        => Controls_Manager::SWITCHER,
				'label_on'    => __( 'Yes', 'premium-addons-for-elementor' ),
				'label_off'   => __( 'No', 'premium-addons-for-elementor' ),
				'description' => __( 'This option will remove the current from the query.', 'premium-addons-for-elementor' ),
				'condition'   => array(
					'query_type' => array( 'all', 'custom' ),
				),
			)
		);

		$this->add_control(
			'advanced_query_heading',
			array(
				'label'     => __( 'Advanced', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
				'condition' => array(
					'query_type!' => array( 'main', 'related' ),
				),
			)
		);

		$this->add_control(
			'filter_by',
			array(
				'label'     => __( 'Filter By', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => '',
				'options'   => array(
					''         => __( 'None', 'premium-addons-for-elementor' ),
					'featured' => __( 'Featured', 'premium-addons-for-elementor' ),
					'sale'     => __( 'Sale', 'premium-addons-for-elementor' ),
				),
				'condition' => array(
					'query_type!' => array( 'main', 'related', 'cross-sells', 'up-sells' ),
				),
			)
		);

		$this->add_control(
			'orderby',
			array(
				'label'     => __( 'Order by', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'date',
				'options'   => array(
					'title'      => __( 'Title', 'premium-addons-for-elementor' ),
					'date'       => __( 'Date', 'premium-addons-for-elementor' ),
					'popularity' => __( 'Popularity', 'premium-addons-for-elementor' ),
					'price'      => __( 'Price', 'premium-addons-for-elementor' ),
					'rating'     => __( 'Rating', 'premium-addons-for-elementor' ),
					'rand'       => __( 'Random', 'premium-addons-for-elementor' ),
					'menu_order' => __( 'Menu Order', 'premium-addons-for-elementor' ),
				),
				'condition' => array(
					'query_type!' => array( 'main', 'related' ),
				),
			)
		);

		$this->add_control(
			'order',
			array(
				'label'     => __( 'Order', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'desc',
				'options'   => array(
					'desc' => __( 'Descending', 'premium-addons-for-elementor' ),
					'asc'  => __( 'Ascending', 'premium-addons-for-elementor' ),
				),
				'condition' => array(
					'query_type!' => array( 'main', 'related' ),
				),
			)
		);

		$this->add_control(
			'empty_products_msg',
			array(
				'label'       => __( 'Empty Query Message', 'premium-addons-for-elementor' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => __( 'No products were found for this query.', 'premium-addons-for-elementor' ),
				'label_block' => true,
				'condition'   => array(
					'query_type!' => array( 'cross-sells', 'up-sells' ),
				),
			)
		);

		$this->end_controls_section();

	}

	/**
	 * Register content pagination section.
	 *
	 * @access public
	 * @since 4.8.3
	 *
	 * @param array $pro_skins   pro skins.
	 */
	public function register_content_pagination_section( $pro_skins ) {
		$this->start_controls_section(
			'section_pagination_options',
			array(
				'label'     => __( 'Pagination', 'premium-addons-for-elementor' ),
				'condition' => array(
					'layout_type' => array( 'grid', 'metro' ),
					'load_more!'  => 'yes',
					'_skin!'      => $pro_skins,
					'query_type!' => array( 'cross-sells', 'up-sells' ),
				),
			)
		);

		$this->add_control(
			'pagination',
			array(
				'label' => __( 'Enable Pagination', 'premium-addons-for-elementor' ),
				'type'  => Controls_Manager::SWITCHER,
			)
		);

		$this->add_control(
			'pagination_type',
			array(
				'label'     => __( 'Type', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::SELECT,
				'options'   => array(
					'numbers'       => __( 'Numbers', 'premium-addons-for-elementor' ),
					'numbers_arrow' => __( 'Numbers + Pre/Next Arrow', 'premium-addons-for-elementor' ),
				),
				'default'   => 'numbers',
				'condition' => array(
					'pagination' => 'yes',
				),
			)
		);

		$this->add_control(
			'prev_string',
			array(
				'label'     => __( 'Previous Page String', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::TEXT,
				'default'   => __( '« Previous', 'premium-addons-for-elementor' ),
				'condition' => array(
					'pagination'      => 'yes',
					'pagination_type' => 'numbers_arrow',
				),
			)
		);

			$this->add_control(
				'next_string',
				array(
					'label'     => __( 'Next Page String', 'premium-addons-for-elementor' ),
					'type'      => Controls_Manager::TEXT,
					'default'   => __( 'Next »', 'premium-addons-for-elementor' ),
					'condition' => array(
						'pagination'      => 'yes',
						'pagination_type' => 'numbers_arrow',
					),
				)
			);

		$this->add_responsive_control(
			'pagination_align',
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
				'selectors' => array(
					'{{WRAPPER}} .premium-woo-products-pagination .page-numbers'  => 'justify-content: {{VALUE}}',
				),
				'toggle'    => false,
				'condition' => array(
					'pagination' => 'yes',
				),
			)
		);

		$this->end_controls_section();

	}

	/**
	 * Register content ribbons section.
	 * Adds sale/fearured ribbon controls
	 *
	 * @access public
	 * @since 4.8.3
	 *
	 * @param array $pro_skins   pro skins.
	 */
	public function register_content_ribbon_section( $pro_skins ) {

		$this->start_controls_section(
			'section_ribbons_settings',
			array(
				'label'     => __( 'Sale/Featured Ribbons', 'premium-addons-for-elementor' ),
				'condition' => array(
					'_skin!' => $pro_skins,
				),

			)
		);

		$this->add_control(
			'sale',
			array(
				'label'   => __( 'Show Sale Ribbon', 'premium-addons-for-elementor' ),
				'type'    => Controls_Manager::SWITCHER,
				'default' => 'yes',
			)
		);

		$this->add_control(
			'sale_type',
			array(
				'label'     => __( 'Type', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::SELECT,
				'options'   => array(
					''       => __( 'Default', 'premium-addons-for-elementor' ),
					'custom' => __( 'Custom', 'premium-addons-for-elementor' ),
				),
				'default'   => '',
				'condition' => array(
					'sale' => 'yes',
				),
			)
		);

		$this->add_control(
			'sale_string',
			array(
				'label'       => __( 'String', 'premium-addons-for-elementor' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => '[value]%',
				'description' => __( 'Use [value] to get the discount in perecentage, or [sale] to get the absolute value of the discount.', 'premium-addons-for-elementor' ),
				'condition'   => array(
					'sale'      => 'yes',
					'sale_type' => 'custom',
				),
			)
		);

		$this->add_control(
			'featured',
			array(
				'label' => __( 'Show Featured Ribbon', 'premium-addons-for-elementor' ),
				'type'  => Controls_Manager::SWITCHER,
			)
		);

		$this->add_control(
			'featured_string',
			array(
				'label'     => __( 'String', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::TEXT,
				'default'   => __( 'Hot', 'premium-addons-for-elementor' ),
				'condition' => array(
					'featured' => 'yes',
				),
			)
		);

		$dir = is_rtl() ? 'right' : 'left';

		$this->add_responsive_control(
			'ribbons_hor',
			array(
				'label'      => __( 'Horizontal Offset', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', '%' ),
				'range'      => array(
					'px' => array(
						'min' => 0,
						'max' => 500,
					),
				),
				'conditions' => array(
					'relation' => 'and',
					'terms'    => array(
						array(
							'name'     => '_skin',
							'operator' => '===',
							'value'    => 'grid-9',
						),
						array(
							'relation' => 'or',
							'terms'    => array(
								array(
									'name'  => 'sale',
									'value' => 'yes',
								),
								array(
									'name'  => 'featured',
									'value' => 'yes',
								),
							),
						),
					),
				),
				'selectors'  => array(
					'{{WRAPPER}} .premium-woo-ribbon-container' => $dir . ': {{SIZE}}{{UNIT}}; transform: translateX(0)',
				),
			)
		);

		$this->add_responsive_control(
			'ribbons_ver',
			array(
				'label'      => __( 'Vertical Offset', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', '%' ),
				'range'      => array(
					'px' => array(
						'min' => 0,
						'max' => 500,
					),
				),
				'conditions' => array(
					'relation' => 'and',
					'terms'    => array(
						array(
							'name'     => '_skin',
							'operator' => '===',
							'value'    => 'grid-9',
						),
						array(
							'relation' => 'or',
							'terms'    => array(
								array(
									'name'  => 'sale',
									'value' => 'yes',
								),
								array(
									'name'  => 'featured',
									'value' => 'yes',
								),
							),
						),
					),
				),
				'selectors'  => array(
					'{{WRAPPER}} .premium-woo-ribbon-container' => 'top: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'ribbons_spacing',
			array(
				'label'      => __( 'Spacing', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', '%' ),
				'range'      => array(
					'px' => array(
						'min' => -10,
						'max' => 200,
					),
				),
				'conditions' => array(
					'relation' => 'and',
					'terms'    => array(
						array(
							'name'  => '_skin',
							'value' => 'grid-9',
						),
						array(
							'name'  => 'sale',
							'value' => 'yes',
						),
						array(
							'name'  => 'featured',
							'value' => 'yes',
						),
					),
				),
				'selectors'  => array(
					'{{WRAPPER}} .premium-woo-product-featured-wrap' => 'margin-top: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_sold_out_settings',
			array(
				'label'     => __( 'Out Of Stock Ribbon', 'premium-addons-for-elementor' ),
				'condition' => array(
					'_skin!' => $pro_skins,
				),

			)
		);

		$this->add_control(
			'sold_out',
			array(
				'label' => __( 'Show Ribbon', 'premium-addons-for-elementor' ),
				'type'  => Controls_Manager::SWITCHER,
			)
		);

		$this->add_control(
			'sold_out_string',
			array(
				'label'     => __( 'String', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::TEXT,
				'default'   => __( 'Out Of Stock', 'premium-addons-for-elementor' ),
				'condition' => array(
					'sold_out' => 'yes',
				),
			)
		);

		$this->end_controls_section();
	}

	/**
	 * Register style general section.
	 *
	 * @access public
	 * @since 4.8.3
	 *
	 * @param array $pro_skins   pro skins.
	 */
	public function register_style_general_section( $pro_skins ) {

		$this->start_controls_section(
			'section_pa_docs',
			array(
				'label' => __( 'Helpful Documentations', 'premium-addons-for-elementor' ),
			)
		);

		$docs = array(
			'https://premiumaddons.com/docs/elementor-woocommerce-products-listing-tutorial/' => __( 'Getting started »', 'premium-addons-for-elementor' ),
			'https://premiumaddons.com/docs/elementor-woocommerce-display-conditions-tutorial/' => __( 'WooCommerce Display Conditions »', 'premium-addons-for-elementor' ),
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
			'section_design_layout',
			array(
				'label'     => __( 'General', 'premium-addons-for-elementor' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => array(
					'_skin!' => $pro_skins,
				),
			)
		);

		$this->add_responsive_control(
			'rows_spacing',
			array(
				'label'       => __( 'Rows Spacing', 'premium-addons-for-elementor' ),
				'type'        => Controls_Manager::SLIDER,
				'range'       => array(
					'px' => array(
						'min' => 0,
						'max' => 100,
					),
				),
				'render_type' => 'template',
				'selectors'   => array(
					'{{WRAPPER}} .premium-woocommerce li.product' => 'margin-bottom: {{SIZE}}{{UNIT}};',
				),
				'condition'   => array(
					'layout_type' => array( 'grid', 'metro' ),
				),
			)
		);

		$this->add_responsive_control(
			'columns_spacing',
			array(
				'label'     => __( 'Columns Spacing', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::SLIDER,
				'default'   => array(
					'size' => 10,
				),
				'range'     => array(
					'px' => array(
						'min' => 0,
						'max' => 100,
					),
				),
				'selectors' => array(
					'{{WRAPPER}} .premium-woocommerce li.product' => 'padding-right: calc( {{SIZE}}{{UNIT}}/2 ); padding-left: calc( {{SIZE}}{{UNIT}}/2 );',
					'{{WRAPPER}} .premium-woocommerce ul.products' => 'margin-left: calc( -{{SIZE}}{{UNIT}}/2 ); margin-right: calc( -{{SIZE}}{{UNIT}}/2 );',
				),
			)
		);

		$this->start_controls_tabs( 'product_style_tabs' );

		$this->start_controls_tab(
			'product_style_normal',
			array(
				'label' => __( 'Normal', 'premium-addons-for-elementor' ),
			)
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name'     => 'product_shadow',
				'selector' => '{{WRAPPER}} .premium-woo-product-wrapper',
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'     => 'product_border',
				'selector' => '{{WRAPPER}} .premium-woo-product-wrapper',
			)
		);

		$this->add_control(
			'product_radius',
			array(
				'label'      => __( 'Border Radius', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-woo-product-wrapper' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}',
				),
			)
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'product_style_hover',
			array(
				'label' => __( 'Hover', 'premium-addons-for-elementor' ),
			)
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name'     => 'product_hover_shadow',
				'selector' => '{{WRAPPER}} .premium-woo-product-wrapper:hover',
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'     => 'product_hover_border',
				'selector' => '{{WRAPPER}} .premium-woo-product-wrapper:hover',
			)
		);

		$this->add_control(
			'product_hover_radius',
			array(
				'label'      => __( 'Border Radius', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-woo-product-wrapper:hover' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}',
				),
			)
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();

	}

	/**
	 * Register style image section.
	 *
	 * @access public
	 * @since 4.8.3
	 *
	 * @param array $pro_skins   pro skins.
	 */
	public function register_style_image_section( $pro_skins ) {

		$this->start_controls_section(
			'section_image_style',
			array(
				'label'     => __( 'Image', 'premium-addons-for-elementor' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => array(
					'_skin!' => $pro_skins,
				),
			)
		);

		$this->add_control(
			'hover_style',
			array(
				'label'   => __( 'Image Hover Style', 'premium-addons-for-elementor' ),
				'type'    => Controls_Manager::SELECT,
				'options' => array(
					''        => __( 'None', 'premium-addons-for-elementor' ),
					'swap'    => __( 'Swap Images', 'premium-addons-for-elementor' ),
					'zoomin'  => __( 'Zoom In', 'premium-addons-for-elementor' ),
					'zoomout' => __( 'Zoom Out', 'premium-addons-for-elementor' ),
					'scale'   => __( 'Scale', 'premium-addons-for-elementor' ),
					'gray'    => __( 'Grayscale', 'premium-addons-for-elementor' ),
					'bright'  => __( 'Bright', 'premium-addons-for-elementor' ),
					'sepia'   => __( 'Sepia', 'premium-addons-for-elementor' ),
					'trans'   => __( 'Translate', 'premium-addons-for-elementor' ),
					'custom'  => __( 'Custom', 'premium-addons-for-elementor' ),
				),
				'default' => 'swap',
			)
		);

		$this->add_group_control(
			Group_Control_Css_Filter::get_type(),
			array(
				'name'      => 'hover_css_filters',
				'selector'  => '{{WRAPPER}} li:hover .premium-woo-product-thumbnail img',
				'condition' => array(
					'hover_style' => 'custom',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'     => 'product_img_border',
				'selector' => '{{WRAPPER}} .woocommerce-loop-product__link, {{WRAPPER}} .premium-woo-product-gallery-images img',
			)
		);

		$this->add_responsive_control(
			'product_img_height',
			array(
				'label'      => __( 'Height', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', 'em' ),
				'range'      => array(
					'px' => array(
						'min' => 0,
						'max' => 1000,
					),
				),
				'selectors'  => array(
					'{{WRAPPER}} .premium-woocommerce .woocommerce-loop-product__link img' => 'height: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'product_img_fit',
			array(
				'label'     => __( 'Image Fit', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'cover',
				'options'   => array(
					'fill'    => __( 'Fill', 'premium-addons-for-elementor' ),
					'cover'   => __( 'Cover', 'premium-addons-for-elementor' ),
					'contain' => __( 'Contain', 'premium-addons-for-elementor' ),
				),
				'selectors' => array(
					'{{WRAPPER}} .premium-woocommerce .woocommerce-loop-product__link img' => 'object-fit: {{VALUE}};',
				),
			)
		);

		$this->end_controls_section();
	}

	public function register_style_loadmore_section( $pro_skins ) {

		$this->start_controls_section(
			'button_style_settings',
			array(
				'label'     => __( 'Load More Button', 'premium-addons-for-elementor' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => array(
					'layout_type' => array( 'grid', 'metro' ),
					'load_more'   => 'yes',
					'pagination!' => 'yes',
					'_skin!'      => $pro_skins,
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
				'selector' => '{{WRAPPER}} .premium-woo-load-more-btn',
			)
		);

		$this->start_controls_tabs( 'button_style_tabs' );

		$this->start_controls_tab(
			'button_style_normal',
			array(
				'label' => __( 'Normal', 'premium-addons-for-elementor' ),
			)
		);

		$this->add_control(
			'button_color',
			array(
				'label'     => __( 'Text Color', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'global'    => array(
					'default' => Global_Colors::COLOR_SECONDARY,
				),
				'selectors' => array(
					'{{WRAPPER}} .premium-woo-load-more-btn'  => 'color: {{VALUE}};',
					'{{WRAPPER}} .premium-woo-load-more-btn .premium-loader'  => 'border-color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Text_Shadow::get_type(),
			array(
				'name'     => 'button_text_shadow',
				'selector' => '{{WRAPPER}} .premium-woo-load-more-btn',
			)
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			array(
				'name'           => 'button_background',
				'types'          => array( 'classic', 'gradient' ),
				'selector'       => '{{WRAPPER}} .premium-woo-load-more-btn',
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
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'     => 'button_border',
				'selector' => '{{WRAPPER}} .premium-woo-load-more-btn',
			)
		);

		$this->add_control(
			'button_border_radius',
			array(
				'label'      => __( 'Border Radius', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-woo-load-more-btn' => 'border-radius: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name'     => 'button_box_shadow',
				'selector' => '{{WRAPPER}} .premium-woo-load-more-btn',
			)
		);

		$this->add_responsive_control(
			'button_margin',
			array(
				'label'      => __( 'Margin', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-woo-load-more' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'button_padding',
			array(
				'label'      => __( 'Padding', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-woo-load-more-btn' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'button_style_hover',
			array(
				'label' => __( 'Hover', 'premium-addons-for-elementor' ),
			)
		);

		$this->add_control(
			'button_hover_color',
			array(
				'label'     => __( 'Text Hover Color', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .premium-woo-load-more-btn:hover'  => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Text_Shadow::get_type(),
			array(
				'name'     => 'button_text_shadow_hover',
				'selector' => '{{WRAPPER}} .premium-woo-load-more-btn:hover',
			)
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			array(
				'name'     => 'button_background_hover',
				'types'    => array( 'classic', 'gradient' ),
				'selector' => '{{WRAPPER}} .premium-woo-load-more-btn:hover',
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'     => 'button_border_hover',
				'selector' => '{{WRAPPER}} .premium-woo-load-more-btn:hover',
			)
		);

		$this->add_control(
			'button_border_radius_hover',
			array(
				'label'      => __( 'Border Radius', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-woo-load-more-btn:hover' => 'border-radius: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name'     => 'button_shadow_hover',
				'selector' => '{{WRAPPER}} .premium-woo-load-more-btn:hover',
			)
		);

		$this->add_responsive_control(
			'button_margin_hover',
			array(
				'label'      => __( 'Margin', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-woo-load-more-btn:hover' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'button_padding_hover',
			array(
				'label'      => __( 'Padding', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-woo-load-more-btn:hover' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();

	}

	/**
	 * Register style pagination section.
	 *
	 * @access public
	 * @since 4.8.3
	 *
	 * @param array $pro_skins   pro skins.
	 */
	public function register_style_pagination_section( $pro_skins ) {
		$this->start_controls_section(
			'section_pagination_style',
			array(
				'label'     => __( 'Pagination', 'premium-addons-for-elementor' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => array(
					'layout_type' => array( 'grid', 'metro' ),
					'load_more!'  => 'yes',
					'pagination'  => 'yes',
					'_skin!'      => $pro_skins,
					'query_type!' => array( 'cross-sells', 'up-sells' ),
				),
			)
		);

		$this->add_responsive_control(
			'pagination_padding',
			array(
				'label'      => __( 'Padding', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-woo-products-pagination ul li .page-numbers' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'pagination_typography',
				'global'   => array(
					'default' => Global_Typography::TYPOGRAPHY_ACCENT,
				),
				'selector' => '{{WRAPPER}} .premium-woo-products-pagination ul li > .page-numbers',
			)
		);

		$this->start_controls_tabs( 'pagination_style_tabs' );

		$this->start_controls_tab(
			'pagination_style_normal',
			array(
				'label' => __( 'Normal', 'premium-addons-for-elementor' ),
			)
		);

		$this->add_control(
			'pagination_color',
			array(
				'label'     => __( 'Color', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .premium-woo-products-pagination ul li .page-numbers' => 'color: {{VALUE}}',
				),
			)
		);

		$this->add_control(
			'pagination_background',
			array(
				'label'     => __( 'Background Color', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .premium-woo-products-pagination ul li .page-numbers' => 'background-color: {{VALUE}}',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'     => 'pagination_border',
				'selector' => '{{WRAPPER}} .premium-woo-products-pagination ul li .page-numbers',
			)
		);

		$this->add_control(
			'pagination_radius',
			array(
				'label'      => __( 'Border Radius', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-woo-products-pagination ul li .page-numbers' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}',
				),
			)
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'pagination_style_hover',
			array(
				'label' => __( 'Hover', 'premium-addons-for-elementor' ),
			)
		);

		$this->add_control(
			'pagination_hover_color',
			array(
				'label'     => __( 'Color', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .premium-woo-products-pagination ul li .page-numbers:hover' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'pagination_hover_background',
			array(
				'label'     => __( 'Background Color', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .premium-woo-products-pagination ul li .page-numbers:hover' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'     => 'pagination_hover_border',
				'selector' => '{{WRAPPER}} .premium-woo-products-pagination ul li .page-numbers:hover',
			)
		);

		$this->add_control(
			'pagination_hover_radius',
			array(
				'label'      => __( 'Border Radius', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-woo-products-pagination ul li .page-numbers:hover' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}',
				),
			)
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'pagination_style_active',
			array(
				'label' => __( 'Active', 'premium-addons-for-elementor' ),
			)
		);

		$this->add_control(
			'pagination_active_color',
			array(
				'label'     => __( 'Color', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .premium-woo-products-pagination ul li span.current' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'pagination_active_background',
			array(
				'label'     => __( 'Background Color', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .premium-woo-products-pagination ul li span.current' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'     => 'pagination_active_border',
				'selector' => '{{WRAPPER}} .premium-woo-products-pagination ul li span.current',
			)
		);

		$this->add_control(
			'pagination_active_radius',
			array(
				'label'      => __( 'Border Radius', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-woo-products-pagination ul li span.current' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}',
				),
			)
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();

	}

	/**
	 * Register style carousel section.
	 *
	 * @access public
	 * @since 4.8.3
	 *
	 * @param array $pro_skins   pro skins.
	 */
	public function register_style_carousel_section( $pro_skins ) {

		$this->start_controls_section(
			'section_carousel_style',
			array(
				'label'     => __( 'Carousel', 'premium-addons-for-elementor' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => array(
					'layout_type' => 'carousel',
					'_skin!'      => $pro_skins,
				),
			)
		);

		$this->add_control(
			'content_carousel_arrows',
			array(
				'label'     => esc_html__( 'Arrows', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::HEADING,
				'condition' => array(
					'arrows' => 'yes',
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
					'{{WRAPPER}} .premium-woocommerce .slick-arrow' => 'color: {{VALUE}};',
				),
				'condition' => array(
					'arrows' => 'yes',
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
					'{{WRAPPER}} .premium-woocommerce .slick-arrow i' => 'font-size: {{SIZE}}{{UNIT}};',
				),
				'condition'  => array(
					'arrows' => 'yes',
				),
			)
		);

		$this->add_control(
			'arrow_background',
			array(
				'label'     => __( 'Background Color', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'global'    => array(
					'default' => Global_Colors::COLOR_SECONDARY,
				),
				'selectors' => array(
					'{{WRAPPER}} .premium-woocommerce .slick-arrow' => 'background-color: {{VALUE}};',
				),
				'condition' => array(
					'arrows' => 'yes',
				),
			)
		);

		$this->add_control(
			'arrow_border_radius',
			array(
				'label'      => __( 'Border Radius', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', '%', 'em' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-woocommerce .slick-arrow' => 'border-radius: {{SIZE}}{{UNIT}};',
				),
				'condition'  => array(
					'arrows' => 'yes',
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
					'{{WRAPPER}} .premium-woocommerce .slick-arrow' => 'padding: {{SIZE}}{{UNIT}};',
				),
				'condition'  => array(
					'arrows' => 'yes',
				),
			)
		);

		$this->add_control(
			'content_carousel_dots',
			array(
				'label'     => esc_html__( 'Dots', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::HEADING,
				'condition' => array(
					'arrows' => 'yes',
				),
			)
		);

		$this->add_control(
			'dot_color',
			array(
				'label'     => __( 'Color', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'global'    => array(
					'default' => Global_Colors::COLOR_PRIMARY,
				),
				'selectors' => array(
					'{{WRAPPER}} ul.slick-dots li' => 'color: {{VALUE}}',
				),
				'separator' => 'before',
				'condition' => array(
					'dots' => 'yes',
				),
			)
		);

		$this->add_control(
			'active_dot_color',
			array(
				'label'     => __( 'Active Color', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'global'    => array(
					'default' => Global_Colors::COLOR_SECONDARY,
				),
				'selectors' => array(
					'{{WRAPPER}} .premium-woocommerce ul.slick-dots li.slick-active' => 'color: {{VALUE}}',
				),
				'condition' => array(
					'dots' => 'yes',
				),
			)
		);

		$this->end_controls_section();
	}


	/**
	 * Register style sold out section.
	 *
	 * @access public
	 * @since 4.8.3
	 *
	 * @param array $pro_skins   pro skins.
	 */
	public function register_style_sold_out_controls( $pro_skins ) {

		$this->start_controls_section(
			'section_sold_out_style',
			array(
				'label'     => __( 'Out Of Stock Ribbon', 'premium-addons-for-elementor' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => array(
					'sold_out' => 'yes',
					'_skin!'   => $pro_skins,
				),
			)
		);

		$this->add_responsive_control(
			'sold_out_width',
			array(
				'label'      => __( 'Size', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', 'em', '%' ),
				'range'      => array(
					'px' => array(
						'min' => 20,
						'max' => 200,
					),
				),
				'selectors'  => array(
					'{{WRAPPER}} .pa-out-of-stock' => 'width: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'sold_out_hor',
			array(
				'label'      => __( 'Horizontal Offset', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', '%' ),
				'range'      => array(
					'px' => array(
						'min' => 0,
						'max' => 200,
					),
				),
				'selectors'  => array(
					'{{WRAPPER}} .pa-out-of-stock' => 'left: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'sold_out_ver',
			array(
				'label'      => __( 'Vertical Offset', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', '%' ),
				'range'      => array(
					'px' => array(
						'min' => 0,
						'max' => 200,
					),
				),
				'selectors'  => array(
					'{{WRAPPER}} .pa-out-of-stock' => 'top: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'sold_out_typography',
				'global'   => array(
					'default' => Global_Typography::TYPOGRAPHY_TEXT,
				),
				'selector' => '{{WRAPPER}} .pa-out-of-stock',
			)
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name'     => 'sold_out_shadow',
				'selector' => '{{WRAPPER}} .pa-out-of-stock',
			)
		);

		$this->add_control(
			'sold_out_color',
			array(
				'label'     => __( 'Color', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .pa-out-of-stock' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'sold_out_background',
			array(
				'label'     => __( 'Background Color', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'global'    => array(
					'default' => Global_Colors::COLOR_SECONDARY,
				),
				'selectors' => array(
					'{{WRAPPER}} .pa-out-of-stock' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_responsive_control(
			'sold_out_radius',
			array(
				'label'      => __( 'Border Radius', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .pa-out-of-stock' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}',
				),
			)
		);

		$this->add_responsive_control(
			'sold_out_padding',
			array(
				'label'      => __( 'Padding', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .pa-out-of-stock' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_section();
	}

	/**
	 * Get queries.
	 *
	 * @access public
	 *
	 * @return array
	 */
	public function get_queries() {

		$query_type = array(
			'all'    => __( 'All Products', 'premium-addons-for-elementor' ),
			'custom' => __( 'Custom Query', 'premium-addons-for-elementor' ),
			'main'   => __( 'Main Query', 'premium-addons-for-elementor' ),
		);

		if ( defined( 'ELEMENTOR_PRO_VERSION' ) ) {
			$query_type['related']     = __( 'Related Products', 'premium-addons-for-elementor' );
			$query_type['cross-sells'] = __( 'Cross-Sells', 'premium-addons-for-elementor' );
			$query_type['up-sells']    = __( 'Upsells', 'premium-addons-for-elementor' );
		}

		return $query_type;
	}

	/**
	 * Get woo tags.
	 *
	 * @access protected
	 *
	 * @return array
	 */
	protected function get_woo_tags() {

		$product_tag = array();

		$tag_args = array(
			'orderby'    => 'name',
			'order'      => 'asc',
			'hide_empty' => false,
		);

		$terms = get_terms( 'product_tag', $tag_args );

		if ( ! empty( $terms ) ) {

			foreach ( $terms as $key => $tag ) {

				$product_tag[ $tag->slug ] = $tag->name;
			}
		}

		return $product_tag;
	}

}
