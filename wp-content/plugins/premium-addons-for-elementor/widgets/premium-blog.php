<?php
/**
 * Premium Blog.
 */

namespace PremiumAddons\Widgets;

// Elementor Classes.
use Elementor\Plugin;
use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Image_Size;
use Elementor\Repeater;
use Elementor\Core\Kits\Documents\Tabs\Global_Colors;
use Elementor\Core\Kits\Documents\Tabs\Global_Typography;
use Elementor\Group_Control_Background;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Css_Filter;
use Elementor\Group_Control_Box_Shadow;

// PremiumAddons Classes.
use PremiumAddons\Includes\Premium_Template_Tags as Blog_Helper;
use PremiumAddons\Includes\Helper_Functions;
use PremiumAddons\Includes\Controls\Premium_Post_Filter;
use PremiumAddons\Includes\Controls\Premium_Tax_Filter;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // If this file is called directly, abort.
}

/**
 * Class Premium_Blog
 */
class Premium_Blog extends Widget_Base {

	/**
	 * Retrieve Widget Name.
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function get_name() {
		return 'premium-addon-blog';
	}

	/**
	 * Retrieve Widget Title.
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function get_title() {
		return __( 'Blog', 'premium-addons-for-elementor' );
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
		return array(
			'imagesloaded',
			'isotope-js',
			'pa-slick',
			'premium-addons',
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
		return 'pa-blog';
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
		return array( 'pa', 'premium', 'posts', 'grid', 'item', 'loop', 'query', 'portfolio', 'cpt', 'custom' );
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
	 * Register Blog controls.
	 *
	 * @since 1.0.0
	 * @access protected
	 */
	protected function register_controls() {  // phpcs:ignore PSR2.Methods.MethodDeclaration.Underscore

		$this->start_controls_section(
			'general_settings_section',
			array(
				'label' => __( 'General', 'premium-addons-for-elementor' ),
			)
		);

		$this->add_control(
			'premium_blog_skin',
			array(
				'label'       => __( 'Skin', 'premium-addons-for-elementor' ),
				'type'        => Controls_Manager::SELECT,
				'options'     => array(
					'classic' => __( 'Classic', 'premium-addons-for-elementor' ),
					'modern'  => __( 'Modern', 'premium-addons-for-elementor' ),
					'cards'   => __( 'Cards', 'premium-addons-for-elementor' ),
					'side'    => __( 'On Side', 'premium-addons-for-elementor' ),
					'banner'  => __( 'Banner', 'premium-addons-for-elementor' ),
				),
				'default'     => 'classic',
				'label_block' => true,
			)
		);

		$this->add_control(
			'banner_skin_notice',
			array(
				'raw'             => __( 'If content height is larger than image height, then you may need to increase image height from Featured Image tab', 'premium-addons-for-elemeentor' ),
				'type'            => Controls_Manager::RAW_HTML,
				'content_classes' => 'elementor-panel-alert elementor-panel-alert-info',
				'condition'       => array(
					'premium_blog_skin' => 'banner',
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
			'premium_blog_grid',
			array(
				'label'              => __( 'Grid', 'premium-addons-for-elementor' ),
				'type'               => Controls_Manager::SWITCHER,
				'default'            => 'yes',
				'frontend_available' => true,
			)
		);

		$this->add_control(
			'premium_blog_layout',
			array(
				'label'              => __( 'Layout', 'premium-addons-for-elementor' ),
				'type'               => Controls_Manager::SELECT,
				'options'            => array(
					'even'    => __( 'Even', 'premium-addons-for-elementor' ),
					'masonry' => __( 'Masonry', 'premium-addons-for-elementor' ),
				),
				'default'            => 'even',
				'condition'          => array(
					'premium_blog_grid' => 'yes',
				),
				'frontend_available' => true,
			)
		);

		$this->add_control(
			'force_height',
			array(
				'label'              => __( 'Equal Height', 'premium-addons-for-elementor' ),
				'type'               => Controls_Manager::SWITCHER,
				'return_value'       => 'true',
				'condition'          => array(
					'premium_blog_grid'   => 'yes',
					'premium_blog_layout' => 'even',
				),
				'frontend_available' => true,
			)
		);

		$this->add_control(
			'force_height_notice',
			array(
				'raw'             => __( 'Equal Height option uses JS to force all content boxes to take the same height, so you will need to make sure all featured images are the same height. You can set that from Featured Image tab.', 'premium-addons-for-elemeentor' ),
				'type'            => Controls_Manager::RAW_HTML,
				'content_classes' => 'elementor-panel-alert elementor-panel-alert-info',
				'condition'       => array(
					'premium_blog_grid'   => 'yes',
					'premium_blog_layout' => 'even',
					'force_height'        => 'true',
				),
			)
		);

		$this->add_responsive_control(
			'premium_blog_columns_number',
			array(
				'label'              => __( 'Number of Columns', 'premium-addons-for-elementor' ),
				'type'               => Controls_Manager::SELECT,
				'options'            => array(
					'100%'   => __( '1 Column', 'premium-addons-for-elementor' ),
					'50%'    => __( '2 Columns', 'premium-addons-for-elementor' ),
					'33.33%' => __( '3 Columns', 'premium-addons-for-elementor' ),
					'25%'    => __( '4 Columns', 'premium-addons-for-elementor' ),
					'20%'    => __( '5 Columns', 'premium-addons-for-elementor' ),
					'16.66%' => __( '6 Columns', 'premium-addons-for-elementor' ),
				),
				'default'            => '50%',
				'tablet_default'     => '50%',
				'mobile_default'     => '100%',
				'render_type'        => 'template',
				'label_block'        => true,
				'condition'          => array(
					'premium_blog_grid' => 'yes',
				),
				'selectors'          => array(
					'{{WRAPPER}} .premium-blog-post-outer-container'  => 'width: {{VALUE}}',
				),
				'frontend_available' => true,
			)
		);

		$this->add_control(
			'premium_blog_number_of_posts',
			array(
				'label'       => __( 'Posts Per Page', 'premium-addons-for-elementor' ),
				'description' => __( 'Set the number of per page', 'premium-addons-for-elementor' ),
				'type'        => Controls_Manager::NUMBER,
				'min'         => 1,
				'default'     => 4,
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

		$this->add_control(
			'post_type_filter',
			array(
				'label'       => __( 'Source', 'premium-addons-for-elementor' ),
				'type'        => Controls_Manager::SELECT,
				'label_block' => true,
				'options'     => $post_types,
				'default'     => 'post',
				'separator'   => 'after',
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
					'post_type_filter!' => 'post',
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
					'menu_order'    => __( 'Menu Order', 'premium-addons-for-elementor' ),
					'comment_count' => __( 'Number of Comments', 'premium-addons-for-elementor' ),
				),
				'default'     => 'date',
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
			)
		);

		$this->add_control(
			'empty_query_text',
			array(
				'label'       => __( 'Empty Query Text', 'premium-addons-for-elementor' ),
				'type'        => Controls_Manager::TEXT,
				'label_block' => true,
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
			'thumb_width',
			array(
				'label'     => __( 'Width (%)', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::SLIDER,
				'default'   => array(
					'size' => '25',
				),
				'condition' => array_merge(
					$featured_image_conditions,
					array(
						'premium_blog_skin' => 'side',
					)
				),
				'selectors' => array(
					'{{WRAPPER}} .premium-blog-thumb-effect-wrapper' => 'flex-basis: {{SIZE}}%',
				),
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
			'premium_blog_content_settings',
			array(
				'label' => __( 'Display Options', 'premium-addons-for-elementor' ),
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
			'author_img_position',
			array(
				'label'      => __( 'Author Image Position', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array(
					'px' => array(
						'min' => -100,
						'max' => 100,
					),
				),
				'selectors'  => array(
					'{{WRAPPER}} .premium-blog-author-thumbnail' => 'top: {{SIZE}}{{UNIT}}',
				),
				'condition'  => array(
					'premium_blog_skin' => 'cards',
				),
			)
		);

		$this->add_responsive_control(
			'premium_blog_posts_columns_spacing',
			array(
				'label'       => __( 'Rows Spacing', 'premium-addons-for-elementor' ),
				'type'        => Controls_Manager::SLIDER,
				'size_units'  => array( 'px', '%', 'em' ),
				'range'       => array(
					'px' => array(
						'min' => 1,
						'max' => 200,
					),
				),
				'default'     => array(
					'size' => 5,
					'unit' => 'px',
				),
				'render_type' => 'template',
				'selectors'   => array(
					'{{WRAPPER}} .premium-blog-post-outer-container' => 'margin-bottom: {{SIZE}}{{UNIT}}',
				),
			)
		);

		$this->add_responsive_control(
			'premium_blog_posts_spacing',
			array(
				'label'     => __( 'Columns Spacing', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::SLIDER,
				'default'   => array(
					'size' => 5,
				),
				'range'     => array(
					'px' => array(
						'min' => 0,
						'max' => 50,
					),
				),
				'selectors' => array(
					'{{WRAPPER}} .premium-blog-post-outer-container' => 'padding-right: calc( {{SIZE}}{{UNIT}}/2 ); padding-left: calc( {{SIZE}}{{UNIT}}/2 )',
					'{{WRAPPER}} .premium-blog-wrap' => 'margin-left: calc( -{{SIZE}}{{UNIT}}/2 ); margin-right: calc( -{{SIZE}}{{UNIT}}/2 );',
				),
				'condition' => array(
					'premium_blog_grid' => 'yes',
				),
			)
		);

		$this->add_responsive_control(
			'premium_flip_text_align',
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
			'content_vertical_alignment',
			array(
				'label'     => __( 'Vertical Alignment', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::CHOOSE,
				'options'   => array(
					'flex-start' => array(
						'title' => __( 'Top', 'premium-addons-for-elementor' ),
						'icon'  => 'eicon-arrow-up',
					),
					'center'     => array(
						'title' => __( 'Center', 'premium-addons-for-elementor' ),
						'icon'  => 'eicon-text-align-justify',
					),
					'flex-end'   => array(
						'title' => __( 'Bottom', 'premium-addons-for-elementor' ),
						'icon'  => 'eicon-arrow-down',
					),
				),
				'default'   => 'flex-end',
				'toggle'    => false,
				'condition' => array(
					'premium_blog_skin' => 'banner',
				),
				'selectors' => array(
					'{{WRAPPER}} .premium-blog-content-wrapper' => 'justify-content: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'scroll_to_offset',
			array(
				'label'              => __( 'Scroll After Pagination/Filter', 'premium-addons-for-elementor' ),
				'type'               => Controls_Manager::SWITCHER,
				'description'        => __( 'Enable this option to scroll to top offset of the widget after click pagination or filter tabs.', 'premium-addons-for-ele,entor' ),
				'default'            => 'yes',
				'conditions'         => array(
					'relation' => 'or',
					'terms'    => array(
						array(
							'name'  => 'premium_blog_cat_tabs',
							'value' => 'yes',
						),
						array(
							'name'  => 'premium_blog_paging',
							'value' => 'yes',
						),
					),
				),
				'frontend_available' => true,
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'premium_blog_posts_options',
			array(
				'label' => __( 'Post Options', 'premium-addons-for-elementor' ),
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

		$this->end_controls_section();

		$this->start_controls_section(
			'premium_blog_advanced_settings',
			array(
				'label' => __( 'Advanced Settings', 'premium-addons-for-elementor' ),
			)
		);

		$this->add_control(
			'premium_blog_infinite_scroll',
			array(
				'label'              => __( 'Load More Posts On Scroll', 'premium-addons-for-elementor' ),
				'type'               => Controls_Manager::SWITCHER,
				'condition'          => array(
					'premium_blog_carousel!' => 'yes',
					'premium_blog_paging!'   => 'yes',
				),
				'frontend_available' => true,
			)
		);

		$this->add_control(
			'premium_blog_cat_tabs',
			array(
				'label'     => __( 'Filter Tabs', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::SWITCHER,
				'condition' => array(
					'premium_blog_carousel!' => 'yes',
				),
			)
		);

		$this->add_control(
			'premium_blog_article_tag_switcher',
			array(
				'label' => __( 'Change Post Html Tag To Article', 'premium-addons-for-elementor' ),
				'type'  => Controls_Manager::SWITCHER,
			)
		);

		$this->add_control(
			'filter_tabs_type',
			array(
				'label'     => __( 'Get Tabs From', 'premium-addons-for-elementor' ),
				'type'      => Premium_Tax_Filter::TYPE,
				'default'   => 'category',
				'condition' => array(
					'premium_blog_cat_tabs'  => 'yes',
					'premium_blog_carousel!' => 'yes',
				),
			)
		);

		$this->add_control(
			'filter_tabs_notice',
			array(
				'raw'             => __( 'Please make sure to select the categories/tags you need to show from Query tab.', 'premium-addons-for-elemeentor' ),
				'type'            => Controls_Manager::RAW_HTML,
				'content_classes' => 'elementor-panel-alert elementor-panel-alert-info',
				'condition'       => array(
					'premium_blog_cat_tabs'  => 'yes',
					'premium_blog_carousel!' => 'yes',
				),
			)
		);

		$this->add_control(
			'premium_blog_tab_label',
			array(
				'label'     => __( 'First Tab Label', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::TEXT,
				'default'   => __( 'All', 'premium-addons-for-elementor' ),
				'condition' => array(
					'premium_blog_cat_tabs'  => 'yes',
					'premium_blog_carousel!' => 'yes',
				),
			)
		);

		$this->add_responsive_control(
			'premium_blog_filter_align',
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
				'condition' => array(
					'premium_blog_cat_tabs'  => 'yes',
					'premium_blog_carousel!' => 'yes',
				),
				'selectors' => array(
					'{{WRAPPER}} .premium-blog-filter' => 'justify-content: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'premium_blog_new_tab',
			array(
				'label'       => __( 'Links in New Tab', 'premium-addons-for-elementor' ),
				'type'        => Controls_Manager::SWITCHER,
				'description' => __( 'Enable links to be opened in a new tab', 'premium-addons-for-elementor' ),
				'default'     => 'yes',
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'premium_blog_carousel_settings',
			array(
				'label'     => __( 'Carousel', 'premium-addons-for-elementor' ),
				'condition' => array(
					'premium_blog_infinite_scroll!' => 'yes',
					'premium_blog_paging!'          => 'yes',
				),
			)
		);

		$this->add_control(
			'premium_blog_carousel',
			array(
				'label'              => __( 'Enable Carousel', 'premium-addons-for-elementor' ),
				'type'               => Controls_Manager::SWITCHER,
				'frontend_available' => true,
			)
		);

		$this->add_control(
			'premium_blog_carousel_fade',
			array(
				'label'              => __( 'Fade', 'premium-addons-for-elementor' ),
				'type'               => Controls_Manager::SWITCHER,
				'condition'          => array(
					'premium_blog_carousel'       => 'yes',
					'premium_blog_columns_number' => '100%',
				),
				'frontend_available' => true,
			)
		);

		$this->add_control(
			'premium_blog_carousel_play',
			array(
				'label'              => __( 'Auto Play', 'premium-addons-for-elementor' ),
				'type'               => Controls_Manager::SWITCHER,
				'condition'          => array(
					'premium_blog_carousel' => 'yes',
				),
				'frontend_available' => true,
			)
		);

		$this->add_control(
			'slides_to_scroll',
			array(
				'label'              => __( 'Slides To Scroll', 'premium-addons-for-elementor' ),
				'type'               => Controls_Manager::NUMBER,
				'condition'          => array(
					'premium_blog_carousel' => 'yes',
				),
				'frontend_available' => true,
			)
		);

		$this->add_control(
			'premium_blog_carousel_autoplay_speed',
			array(
				'label'              => __( 'Autoplay Speed', 'premium-addons-for-elementor' ),
				'description'        => __( 'Autoplay Speed means at which time the next slide should come. Set a value in milliseconds (ms)', 'premium-addons-for-elementor' ),
				'type'               => Controls_Manager::NUMBER,
				'default'            => 5000,
				'condition'          => array(
					'premium_blog_carousel'      => 'yes',
					'premium_blog_carousel_play' => 'yes',
				),
				'frontend_available' => true,
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
					'{{WRAPPER}} .premium-blog-wrap .slick-slide' => 'transition: all {{VALUE}}ms !important',
				),
				'condition'          => array(
					'premium_blog_carousel' => 'yes',
				),
				'frontend_available' => true,
			)
		);

		$this->add_control(
			'premium_blog_carousel_center',
			array(
				'label'              => __( 'Center Mode', 'premium-addons-for-elementor' ),
				'type'               => Controls_Manager::SWITCHER,
				'condition'          => array(
					'premium_blog_carousel' => 'yes',
				),
				'frontend_available' => true,
			)
		);

		$this->add_control(
			'premium_blog_carousel_spacing',
			array(
				'label'              => __( 'Slides\' Spacing', 'premium-addons-for-elementor' ),
				'description'        => __( 'Set a spacing value in pixels (px)', 'premium-addons-for-elementor' ),
				'type'               => Controls_Manager::NUMBER,
				'default'            => '15',
				'condition'          => array(
					'premium_blog_carousel' => 'yes',
				),
				'frontend_available' => true,
			)
		);

		$this->add_control(
			'premium_blog_carousel_dots',
			array(
				'label'              => __( 'Navigation Dots', 'premium-addons-for-elementor' ),
				'type'               => Controls_Manager::SWITCHER,
				'condition'          => array(
					'premium_blog_carousel' => 'yes',
				),
				'frontend_available' => true,
			)
		);

		$this->add_control(
			'premium_blog_carousel_arrows',
			array(
				'label'              => __( 'Navigation Arrows', 'premium-addons-for-elementor' ),
				'type'               => Controls_Manager::SWITCHER,
				'default'            => 'yes',
				'condition'          => array(
					'premium_blog_carousel' => 'yes',
				),
				'frontend_available' => true,
			)
		);

		$this->add_responsive_control(
			'premium_blog_carousel_arrows_pos',
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
					'premium_blog_carousel'        => 'yes',
					'premium_blog_carousel_arrows' => 'yes',
				),
				'selectors'  => array(
					'{{WRAPPER}} .premium-blog-wrap a.carousel-arrow.carousel-next' => 'right: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .premium-blog-wrap a.carousel-arrow.carousel-prev' => 'left: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'premium_blog_pagination_section',
			array(
				'label'     => __( 'Pagination', 'premium-addons-for-elementor' ),
				'condition' => array(
					'premium_blog_carousel!'        => 'yes',
					'premium_blog_infinite_scroll!' => 'yes',
				),
			)
		);

		$this->add_control(
			'premium_blog_paging',
			array(
				'label'       => __( 'Enable Pagination', 'premium-addons-for-elementor' ),
				'type'        => Controls_Manager::SWITCHER,
				'description' => __( 'Pagination is the process of dividing the posts into discrete pages', 'premium-addons-for-elementor' ),
			)
		);

		$this->add_control(
			'max_pages',
			array(
				'label'     => __( 'Page Limit', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::NUMBER,
				'default'   => 5,
				'condition' => array(
					'premium_blog_paging' => 'yes',
				),
			)
		);

		$this->add_control(
			'pagination_strings',
			array(
				'label'     => __( 'Enable Pagination Next/Prev Strings', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::SWITCHER,
				'default'   => 'yes',
				'condition' => array(
					'premium_blog_paging' => 'yes',
				),
			)
		);

		$this->add_control(
			'premium_blog_prev_text',
			array(
				'label'     => __( 'Previous Page String', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::TEXT,
				'default'   => __( 'Previous', 'premium-addons-for-elementor' ),
				'condition' => array(
					'premium_blog_paging' => 'yes',
					'pagination_strings'  => 'yes',
				),
			)
		);

		$this->add_control(
			'premium_blog_next_text',
			array(
				'label'     => __( 'Next Page String', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::TEXT,
				'default'   => __( 'Next', 'premium-addons-for-elementor' ),
				'condition' => array(
					'premium_blog_paging' => 'yes',
					'pagination_strings'  => 'yes',
				),
			)
		);

		$this->add_responsive_control(
			'premium_blog_pagination_align',
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
				'default'   => 'right',
				'toggle'    => false,
				'condition' => array(
					'premium_blog_paging' => 'yes',
				),
				'selectors' => array(
					'{{WRAPPER}} .premium-blog-pagination-container' => 'text-align: {{VALUE}}',
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
			'https://premiumaddons.com/docs/elementor-blog-widget-tutorial/' => __( 'Getting started »', 'premium-addons-for-elementor' ),
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
			'premium_blog_filter_style',
			array(
				'label'     => __( 'Filter', 'premium-addons-for-elementor' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => array(
					'premium_blog_cat_tabs' => 'yes',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'premium_blog_filter_typo',
				'global'   => array(
					'default' => Global_Typography::TYPOGRAPHY_PRIMARY,
				),
				'selector' => '{{WRAPPER}} .premium-blog-filters-container li a.category',
			)
		);

		$this->start_controls_tabs( 'tabs_filter' );

		$this->start_controls_tab(
			'tab_filter_normal',
			array(
				'label' => __( 'Normal', 'premium-addons-for-elementor' ),
			)
		);

		$this->add_control(
			'premium_blog_filter_color',
			array(
				'label'     => __( 'Color', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'global'    => array(
					'default' => Global_Colors::COLOR_SECONDARY,
				),
				'selectors' => array(
					'{{WRAPPER}} .premium-blog-filters-container li a.category' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'premium_blog_background_color',
			array(
				'label'     => __( 'Background Color', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'global'    => array(
					'default' => Global_Colors::COLOR_PRIMARY,
				),
				'selectors' => array(
					'{{WRAPPER}} .premium-blog-filters-container li a.category' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'     => 'premium_blog_filter_border',
				'selector' => '{{WRAPPER}} .premium-blog-filters-container li a.category',
			)
		);

		$this->add_control(
			'premium_blog_filter_border_radius',
			array(
				'label'      => __( 'Border Radius', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-blog-filters-container li a.category'  => 'border-radius: {{SIZE}}{{UNIT}};',
				),
				'separator'  => 'after',
			)
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'tab_filter_active',
			array(
				'label' => __( 'Active', 'premium-addons-for-elementor' ),
			)
		);

		$this->add_control(
			'premium_blog_filter_active_color',
			array(
				'label'     => __( 'Color', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'global'    => array(
					'default' => Global_Colors::COLOR_PRIMARY,
				),
				'selectors' => array(
					'{{WRAPPER}} .premium-blog-filters-container li a.active' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'premium_blog_background_active_color',
			array(
				'label'     => __( 'Background Color', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'global'    => array(
					'default' => Global_Colors::COLOR_SECONDARY,
				),
				'selectors' => array(
					'{{WRAPPER}} .premium-blog-filters-container li a.active' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'     => 'filter_active_border',
				'selector' => '{{WRAPPER}} .premium-blog-filters-container li a.active',
			)
		);

		$this->add_control(
			'filter_active_border_radius',
			array(
				'label'      => __( 'Border Radius', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-blog-filters-container li a.active'  => 'border-radius: {{SIZE}}{{UNIT}}',
				),
				'separator'  => 'after',
			)
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name'     => 'premium_blog_filter_shadow',
				'selector' => '{{WRAPPER}} .premium-blog-filters-container li a.category',
			)
		);

		$this->add_responsive_control(
			'premium_blog_filter_margin',
			array(
				'label'      => __( 'Margin', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-blog-filters-container li a.category' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'premium_blog_filter_padding',
			array(
				'label'      => __( 'Padding', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-blog-filters-container li a.category' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'premium_blog_image_style_section',
			array(
				'label'     => __( 'Image', 'premium-addons-for-elementor' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => $featured_image_conditions,
			)
		);

		$this->add_control(
			'premium_blog_plus_color',
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
			'premium_blog_overlay_color',
			array(
				'label'     => __( 'Overlay Color', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .premium-blog-framed-effect, {{WRAPPER}} .premium-blog-bordered-effect,{{WRAPPER}} .premium-blog-squares-effect:before,{{WRAPPER}} .premium-blog-squares-effect:after,{{WRAPPER}} .premium-blog-squares-square-container:before,{{WRAPPER}} .premium-blog-squares-square-container:after, {{WRAPPER}} .premium-blog-thumbnail-overlay' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'premium_blog_border_effect_color',
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
			'premium_blog_title_style_section',
			array(
				'label' => __( 'Title', 'premium-addons-for-elementor' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'premium_blog_title_typo',
				'global'   => array(
					'default' => Global_Typography::TYPOGRAPHY_PRIMARY,
				),
				'selector' => '{{WRAPPER}} .premium-blog-entry-title, {{WRAPPER}} .premium-blog-entry-title a',
			)
		);

		$this->add_control(
			'premium_blog_title_color',
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
			'premium_blog_title_hover_color',
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
			'title_spacing',
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

		$this->start_controls_section(
			'post_categories_style_section',
			array(
				'label'     => __( 'Categories', 'premium-addons-for-elementor' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => array(
					'premium_blog_skin'            => array( 'side', 'banner' ),
					'premium_blog_categories_meta' => 'yes',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'category_typography',
				'global'   => array(
					'default' => Global_Typography::TYPOGRAPHY_SECONDARY,
				),
				'selector' => '{{WRAPPER}} .premium-blog-cats-container a',
			)
		);

		$repeater = new REPEATER();

		$repeater->add_control(
			'category_color',
			array(
				'label'     => __( 'Color', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} {{CURRENT_ITEM}}' => 'color: {{VALUE}}',
				),
			)
		);

		$repeater->add_control(
			'category_hover_color',
			array(
				'label'     => __( 'Hover Color', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} {{CURRENT_ITEM}}:hover' => 'color: {{VALUE}}',
				),
			)
		);

		$repeater->add_control(
			'category_background_color',
			array(
				'label'     => __( 'Background Color', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} {{CURRENT_ITEM}}' => 'background-color: {{VALUE}}',
				),
			)
		);

		$repeater->add_control(
			'category_hover_background_color',
			array(
				'label'     => __( 'Hover Background Color', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} {{CURRENT_ITEM}}:hover' => 'background-color: {{VALUE}}',
				),
			)
		);

		$repeater->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'     => 'category_border',
				'selector' => '{{WRAPPER}} {{CURRENT_ITEM}}',
			)
		);

		$repeater->add_control(
			'category_border_radius',
			array(
				'label'      => __( 'Border Radius', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} {{CURRENT_ITEM}}' => 'border-radius: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'categories_repeater',
			array(
				'label'       => __( 'Categories', 'premium-addons-for-elementor' ),
				'type'        => Controls_Manager::REPEATER,
				'fields'      => $repeater->get_controls(),
				'default'     => array(
					array(
						'category_background_color' => '',
					),
				),
				'render_type' => 'ui',
				'condition'   => array(
					'premium_blog_skin'            => array( 'side', 'banner' ),
					'premium_blog_categories_meta' => 'yes',
				),
			)
		);

		$this->add_responsive_control(
			'categories_margin',
			array(
				'label'      => __( 'Margin', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-blog-cats-container a' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'categories_padding',
			array(
				'label'      => __( 'Padding', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-blog-cats-container a' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'premium_blog_meta_style_section',
			array(
				'label' => __( 'Metadata', 'premium-addons-for-elementor' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'premium_blog_meta_typo',
				'global'   => array(
					'default' => Global_Typography::TYPOGRAPHY_SECONDARY,
				),
				'selector' => '{{WRAPPER}} .premium-blog-meta-data',
			)
		);

		$this->add_control(
			'premium_blog_meta_color',
			array(
				'label'     => __( 'Metadata Color', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .premium-blog-meta-data > *'  => 'color: {{VALUE}}',
				),
			)
		);

		$this->add_control(
			'premium_blog_meta_hover_color',
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
				'label' => __( 'Content Box', 'premium-addons-for-elementor' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'      => 'premium_blog_content_typo',
				'selector'  => '{{WRAPPER}} .premium-blog-post-content',
				'condition' => array(
					'content_source' => 'excerpt',
				),
			)
		);

		$this->add_control(
			'premium_blog_post_content_color',
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
			'premium_blog_content_background_color',
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
				'name'      => 'premium_blog_content_background_color',
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
				'name'     => 'premium_blog_box_shadow',
				'selector' => '{{WRAPPER}} .premium-blog-content-wrapper',
			)
		);

		$this->add_responsive_control(
			'prmeium_blog_content_margin',
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
			'prmeium_blog_content_padding',
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
			'premium_blog_read_more_style',
			array(
				'label'     => __( 'Button', 'premium-addons-for-elementor' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => array(
					'premium_blog_excerpt'      => 'yes',
					'premium_blog_excerpt_type' => 'link',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'premium_blog_read_more_typo',
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
			'premium_blog_read_more_color',
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
			'premium_blog_tags_style_section',
			array(
				'label'     => __( 'Tags', 'premium-addons-for-elementor' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => array(
					'premium_blog_tags_meta' => 'yes',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'premium_blog_tags_typo',
				'global'   => array(
					'default' => Global_Typography::TYPOGRAPHY_SECONDARY,
				),
				'selector' => '{{WRAPPER}} .premium-blog-post-tags-container',
			)
		);

		$this->add_control(
			'premium_blog_tags_color',
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
			'premium_blog_tags_hoer_color',
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
			'premium_blog_box_style_section',
			array(
				'label' => __( 'Box', 'premium-addons-for-elementor' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_control(
			'premium_blog_box_background_color',
			array(
				'label'     => __( 'Background Color', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#f5f5f5',
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
				'condition'  => array(
					'box_adv_radius!' => 'yes',
				),
			)
		);

		$this->add_control(
			'box_adv_radius',
			array(
				'label'       => __( 'Advanced Border Radius', 'premium-addons-for-elementor' ),
				'type'        => Controls_Manager::SWITCHER,
				'description' => __( 'Apply custom radius values. Get the radius value from ', 'premium-addons-for-elementor' ) . '<a href="https://9elements.github.io/fancy-border-radius/" target="_blank">here</a>',
			)
		);

		$this->add_control(
			'box_adv_radius_value',
			array(
				'label'     => __( 'Border Radius', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::TEXT,
				'dynamic'   => array( 'active' => true ),
				'selectors' => array(
					'{{WRAPPER}} .premium-blog-post-container' => 'border-radius: {{VALUE}};',
				),
				'condition' => array(
					'box_adv_radius' => 'yes',
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
			'prmeium_blog_box_padding',
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
			'prmeium_blog_inner_box_padding',
			array(
				'label'      => __( 'Padding', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-blog-post-container' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'premium_blog_pagination_Style',
			array(
				'label'     => __( 'Pagination', 'premium-addons-for-elementor' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => array(
					'premium_blog_paging' => 'yes',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'premium_blog_pagination_typo',
				'selector' => '{{WRAPPER}} .premium-blog-pagination-container > .page-numbers',
			)
		);

		$this->start_controls_tabs( 'premium_blog_pagination_colors' );

		$this->start_controls_tab(
			'premium_blog_pagination_nomral',
			array(
				'label' => __( 'Normal', 'premium-addons-for-elementor' ),

			)
		);

		$this->add_control(
			'prmeium_blog_pagination_color',
			array(
				'label'     => __( 'Color', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'global'    => array(
					'default' => Global_Colors::COLOR_SECONDARY,
				),
				'selectors' => array(
					'{{WRAPPER}} .premium-blog-pagination-container .page-numbers' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'prmeium_blog_pagination_back_color',
			array(
				'label'     => __( 'Background Color', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'global'    => array(
					'default' => Global_Colors::COLOR_PRIMARY,
				),
				'selectors' => array(
					'{{WRAPPER}} .premium-blog-pagination-container .page-numbers' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'      => 'navigation_border',
				'separator' => 'before',
				'selector'  => '{{WRAPPER}} .premium-blog-pagination-container .page-numbers',
			)
		);

		$this->add_control(
			'navigation_border_radius',
			array(
				'label'      => __( 'Border Radius', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', '%', 'em' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-blog-pagination-container .page-numbers' => 'border-radius: {{SIZE}}{{UNIT}};',
				),
				'condition'  => array(
					'navigation_adv_radius!' => 'yes',
				),
			)
		);

		$this->add_control(
			'navigation_adv_radius',
			array(
				'label'       => __( 'Advanced Border Radius', 'premium-addons-for-elementor' ),
				'type'        => Controls_Manager::SWITCHER,
				'description' => __( 'Apply custom radius values. Get the radius value from ', 'premium-addons-for-elementor' ) . '<a href="https://9elements.github.io/fancy-border-radius/" target="_blank">here</a>',
			)
		);

		$this->add_control(
			'navigation_adv_radius_value',
			array(
				'label'     => __( 'Border Radius', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::TEXT,
				'dynamic'   => array( 'active' => true ),
				'selectors' => array(
					'{{WRAPPER}} .premium-blog-pagination-container .page-numbers' => 'border-radius: {{VALUE}};',
				),
				'condition' => array(
					'navigation_adv_radius' => 'yes',
				),
			)
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'premium_blog_pagination_hover',
			array(
				'label' => __( 'Hover', 'premium-addons-for-elementor' ),

			)
		);

		$this->add_control(
			'prmeium_blog_pagination_hover_color',
			array(
				'label'     => __( 'Color', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'global'    => array(
					'default' => Global_Colors::COLOR_PRIMARY,
				),
				'selectors' => array(
					'{{WRAPPER}} .premium-blog-pagination-container .page-numbers:hover' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'prmeium_blog_pagination_back_hover_color',
			array(
				'label'     => __( 'Background Color', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'global'    => array(
					'default' => Global_Colors::COLOR_SECONDARY,
				),
				'selectors' => array(
					'{{WRAPPER}} .premium-blog-pagination-container .page-numbers:hover' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'      => 'hover_navigation_border',
				'separator' => 'before',
				'selector'  => '{{WRAPPER}} .premium-blog-pagination-container .page-numbers:hover',
			)
		);

		$this->add_control(
			'hover_navigation_border_radius',
			array(
				'label'      => __( 'Border Radius', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', '%', 'em' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-blog-pagination-container .page-numbers:hover' => 'border-radius: {{SIZE}}{{UNIT}};',
				),
				'condition'  => array(
					'hover_navigation_adv_radius!' => 'yes',
				),
			)
		);

		$this->add_control(
			'hover_navigation_adv_radius',
			array(
				'label'       => __( 'Advanced Border Radius', 'premium-addons-for-elementor' ),
				'type'        => Controls_Manager::SWITCHER,
				'description' => __( 'Apply custom radius values. Get the radius value from ', 'premium-addons-for-elementor' ) . '<a href="https://9elements.github.io/fancy-border-radius/" target="_blank">here</a>',
			)
		);

		$this->add_control(
			'hover_navigation_adv_radius_value',
			array(
				'label'     => __( 'Border Radius', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::TEXT,
				'dynamic'   => array( 'active' => true ),
				'selectors' => array(
					'{{WRAPPER}} .premium-blog-pagination-container .page-numbers:hover' => 'border-radius: {{VALUE}};',
				),
				'condition' => array(
					'hover_navigation_adv_radius' => 'yes',
				),
			)
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'premium_blog_pagination_active',
			array(
				'label' => __( 'Active', 'premium-addons-for-elementor' ),

			)
		);

		$this->add_control(
			'prmeium_blog_pagination_active_color',
			array(
				'label'     => __( 'Color', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'global'    => array(
					'default' => Global_Colors::COLOR_PRIMARY,
				),
				'selectors' => array(
					'{{WRAPPER}} .premium-blog-pagination-container span.current' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'prmeium_blog_pagination_back_active_color',
			array(
				'label'     => __( 'Background Color', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'global'    => array(
					'default' => Global_Colors::COLOR_SECONDARY,
				),
				'selectors' => array(
					'{{WRAPPER}} .premium-blog-pagination-container span.current' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'      => 'active_navigation_border',
				'separator' => 'before',
				'selector'  => '{{WRAPPER}} .premium-blog-pagination-container span.current',
			)
		);

		$this->add_control(
			'active_navigation_border_radius',
			array(
				'label'      => __( 'Border Radius', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', '%', 'em' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-blog-pagination-container span.current' => 'border-radius: {{SIZE}}{{UNIT}};',
				),
				'condition'  => array(
					'active_navigation_adv_radius!' => 'yes',
				),
			)
		);

		$this->add_control(
			'active_navigation_adv_radius',
			array(
				'label'       => __( 'Advanced Border Radius', 'premium-addons-for-elementor' ),
				'type'        => Controls_Manager::SWITCHER,
				'description' => __( 'Apply custom radius values. Get the radius value from ', 'premium-addons-for-elementor' ) . '<a href="https://9elements.github.io/fancy-border-radius/" target="_blank">here</a>',
			)
		);

		$this->add_control(
			'active_navigation_adv_radius_value',
			array(
				'label'     => __( 'Border Radius', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::TEXT,
				'dynamic'   => array( 'active' => true ),
				'selectors' => array(
					'{{WRAPPER}} .premium-blog-pagination-container span.current' => 'border-radius: {{VALUE}};',
				),
				'condition' => array(
					'active_navigation_adv_radius' => 'yes',
				),
			)
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->add_responsive_control(
			'prmeium_blog_pagination_margin',
			array(
				'label'      => __( 'Margin', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'separator'  => 'before',
				'selectors'  => array(
					'{{WRAPPER}} .premium-blog-pagination-container .page-numbers' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'prmeium_blog_pagination_padding',
			array(
				'label'      => __( 'Padding', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-blog-pagination-container .page-numbers' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'pagination_overlay_color',
			array(
				'label'     => __( 'Overlay Color', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'separator' => 'before',
				'selectors' => array(
					'{{WRAPPER}} .premium-loading-feed' => 'background-color: {{VALUE}}',
				),
			)
		);

		$this->add_control(
			'spinner_color',
			array(
				'label'     => __( 'Spinner Color', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .premium-loader' => 'border-color: {{VALUE}}',
				),
			)
		);

		$this->add_control(
			'spinner_fill_color',
			array(
				'label'     => __( 'Fill Color', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .premium-loader' => 'border-top-color: {{VALUE}}',
				),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'carousel_dots_style',
			array(
				'label'     => __( 'Carousel Dots', 'premium-addons-for-elementor' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => array(
					'premium_blog_carousel'      => 'yes',
					'premium_blog_carousel_dots' => 'yes',
				),
			)
		);

		$this->add_control(
			'carousel_dot_navigation_color',
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
			'carousel_dot_navigation_active_color',
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
					'premium_blog_carousel'        => 'yes',
					'premium_blog_carousel_arrows' => 'yes',
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
					'{{WRAPPER}} .premium-blog-wrap .slick-arrow' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_responsive_control(
			'premium_blog_carousel_arrow_size',
			array(
				'label'      => __( 'Size', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', '%', 'em' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-blog-wrap .slick-arrow i' => 'font-size: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'premium_blog_carousel_arrow_background',
			array(
				'label'     => __( 'Background Color', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'global'    => array(
					'default' => Global_Colors::COLOR_SECONDARY,
				),
				'selectors' => array(
					'{{WRAPPER}} .premium-blog-wrap .slick-arrow' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'premium_blog_carousel_border_radius',
			array(
				'label'      => __( 'Border Radius', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', '%', 'em' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-blog-wrap .slick-arrow' => 'border-radius: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'premium_blog_carousel_arrow_padding',
			array(
				'label'      => __( 'Padding', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', '%', 'em' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-blog-wrap .slick-arrow' => 'padding: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_section();

	}


	/**
	 * Get Filter Array.
	 *
	 * Returns an array of filters
	 *
	 * @since 3.20.8
	 * @access protected
	 *
	 * @param string $filter filter rule.
	 *
	 * @return array
	 */
	public function get_filter_array( $filter ) {

		$settings = $this->get_settings();

		$current_language = apply_filters( 'wpml_current_language', '-' );

		$post_type = $settings['post_type_filter'];

		if ( 'tag' === $filter ) {
			$filter = 'post_tag';
		}

		$filter_rule = isset( $settings[ $filter . '_' . $post_type . '_filter_rule' ] ) ? $settings[ $filter . '_' . $post_type . '_filter_rule' ] : '';

		// Fix: Make sure there is a value set for the current tax control.
		if ( empty( $filter_rule ) ) {
			return;
		}

		$filters = $settings[ 'tax_' . $filter . '_' . $post_type . '_filter' ];

		// Get the categories based on filter source.
		$taxs = get_terms( $filter );

		$tabs_array = array();

		if ( is_wp_error( $taxs ) ) {
			return array();
		}

		if ( empty( $filters ) || '' === $filters ) {

			$tabs_array = $taxs;

		} else {

			foreach ( $taxs as $key => $value ) {

				$slug = str_replace( '-' . $current_language, '', $value->slug );

				$if_empty = empty( array_intersect( array( $slug, $value->slug ), $filters ) );

				if ( 'IN' === $filter_rule ) {

					if ( ! $if_empty ) {

						$tabs_array[] = $value;
					}
				} else {

					if ( $if_empty ) {

						$tabs_array[] = $value;
					}
				}
			}
		}

		return $tabs_array;
	}

	/**
	 * Get Filter Tabs Markup
	 *
	 * @since 3.11.2
	 * @access protected
	 */
	protected function get_filter_tabs_markup( $filters ) {

		$settings = $this->get_settings();

		if ( empty( $filters ) ) {
			return;
		}

		?>
		<div class="premium-blog-filter">
			<ul class="premium-blog-filters-container">
				<?php if ( ! empty( $settings['premium_blog_tab_label'] ) ) : ?>
					<li>
						<a href="javascript:;" class="category active" data-filter="*">
							<?php echo esc_html( $settings['premium_blog_tab_label'] ); ?>
						</a>
					</li>
				<?php endif; ?>
				<?php
				foreach ( $filters as $index => $filter ) {
					$key = 'blog_category_' . $index;

					$this->add_render_attribute( $key, 'class', 'category' );

					if ( empty( $settings['premium_blog_tab_label'] ) && 0 === $index ) {
						$this->add_render_attribute( $key, 'class', 'active' );
					}
					?>
						<li>
							<a href="javascript:;" <?php echo wp_kses_post( $this->get_render_attribute_string( $key ) ); ?> data-filter="<?php echo esc_attr( $filter->slug ); ?>">
								<?php echo wp_kses_post( $filter->name ); ?>
							</a>
						</li>
				<?php } ?>
			</ul>
		</div>
		<?php
	}

	/**
	 * Render Blog output on the frontend.
	 *
	 * Written in PHP and used to generate the final HTML.
	 *
	 * @since 0.0.1
	 * @access protected
	 */
	protected function render() {

		$settings = $this->get_settings();

		$settings['widget_type'] = 'premium-addon-blog';
		$settings['widget_id']   = $this->get_id();
		$settings['active_cat']  = '';

		$blog_helper = Blog_Helper::getInstance();

		if ( 'yes' === $settings['premium_blog_cat_tabs'] && 'yes' !== $settings['premium_blog_carousel'] ) {

			$filter_rule = $settings['filter_tabs_type'];

			$filters = $this->get_filter_array( $filter_rule );

			if ( empty( $settings['premium_blog_tab_label'] ) ) {
				$settings['active_cat'] = $filters[0]->slug;
			}
		}

		$blog_helper->set_widget_settings( $settings, $settings['active_cat'] );

		$query = $blog_helper->get_query_posts();

		if ( ! $query->have_posts() ) {

			$query_notice = $settings['empty_query_text'];

			$this->get_empty_query_message( $query_notice );
			return;
		}

		if ( 'yes' === $settings['premium_blog_paging'] ) {

			$total_pages = $query->max_num_pages;

			if ( ! empty( $settings['max_pages'] ) ) {
				$total_pages = min( $settings['max_pages'], $total_pages );
			}
		}

		$masked = 'none' !== $settings['shape_divider'] ? 'premium-blog-masked' : '';

		$this->add_render_attribute( 'blog', 'class', array( 'premium-blog-wrap', $masked ) );

		if ( 'yes' === $settings['premium_blog_grid'] ) {

			$this->add_render_attribute( 'blog', 'class', 'premium-blog-' . $settings['premium_blog_layout'] );

		} else {

			$this->add_render_attribute( 'blog', 'class', 'premium-blog-list' );

		}

		if ( 'yes' === $settings['premium_blog_carousel'] ) {
			$this->add_render_attribute( 'blog', 'class', 'premium-carousel-hidden' );
		}

		// Add page ID to be used later to get posts by AJAX.
		$page_id = '';
		if ( null !== Plugin::$instance->documents->get_current() ) {
			$page_id = Plugin::$instance->documents->get_current()->get_main_id();
		}
		$this->add_render_attribute( 'blog', 'data-page', $page_id );

		if ( 'yes' === $settings['premium_blog_paging'] && $total_pages > 1 ) {

			$this->add_render_attribute( 'blog', 'data-pagination', 'true' );

		}

		?>

		<?php if ( 'yes' === $settings['premium_blog_cat_tabs'] && 'yes' !== $settings['premium_blog_carousel'] ) : ?>
			<?php $this->get_filter_tabs_markup( $filters ); ?>
		<?php endif; ?>
		<div <?php echo wp_kses_post( $this->get_render_attribute_string( 'blog' ) ); ?>>
			<?php
				$id = $this->get_id();
				$blog_helper->render_posts();
			?>
		</div>

		<?php if ( 'yes' === $settings['premium_blog_paging'] && $total_pages > 1 ) : ?>
		<div class="premium-blog-footer">
			<?php $blog_helper->render_pagination(); ?>
		</div>
			<?php
		endif;

		if ( Plugin::instance()->editor->is_edit_mode() ) {

			if ( 'yes' === $settings['premium_blog_grid'] ) {
				if ( 'masonry' === $settings['premium_blog_layout'] && 'yes' !== $settings['premium_blog_carousel'] ) {
					$this->render_editor_script();
				}
			}
		}

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

				$( '.premium-blog-wrap' ).each( function() {

					var $node_id 	= '<?php echo esc_attr( $this->get_id() ); ?>',
						scope 		= $( '[data-id="' + $node_id + '"]' ),
						selector 	= $(this);


					if ( selector.closest( scope ).length < 1 ) {
						return;
					}


					var masonryArgs = {
						itemSelector	: '.premium-blog-post-outer-container',
						percentPosition : true,
						layoutMode		: 'masonry',
					};

					var $isotopeObj = {};

					selector.imagesLoaded( function() {

						$isotopeObj = selector.isotope( masonryArgs );

						$isotopeObj.imagesLoaded().progress(function() {
							$isotopeObj.isotope("layout");
						});

						selector.find('.premium-blog-post-outer-container').resize( function() {
							$isotopeObj.isotope( 'layout' );
						});
					});

				});
			});
		</script>
		<?php
	}

	/**
	 * Get Empty Query Message
	 *
	 * Written in PHP and used to generate the final HTML when the query is empty
	 *
	 * @since 3.20.3
	 * @access protected
	 *
	 * @param string $notice empty query notice.
	 */
	protected function get_empty_query_message( $notice ) {

		if ( empty( $notice ) ) {
			$notice = __( 'The current query has no posts. Please make sure you have published items matching your query.', 'premium-addons-for-elementor' );
		}

		?>
		<div class="premium-error-notice">
			<?php echo wp_kses_post( $notice ); ?>
		</div>
		<?php
	}
}
