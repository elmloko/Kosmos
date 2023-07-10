<?php
/**
 * PA Premium Temlpate Tags.
 */

namespace PremiumAddons\Includes;

// Elementor Classes.
use Elementor\Plugin;
use Elementor\Group_Control_Image_Size;
use PremiumAddons\Includes\ACF_Helper;
use PremiumAddonsPro\Includes\Smart_Post_Listing_Helper as Posts_Helper;
use ElementorPro\Plugin as PluginPro;
use ElementorPro\Modules\LoopBuilder\Files\Css\Loop_Dynamic_CSS;


if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Premium_Template_Tags class defines all the query of options of select box
 *
 * Setting up the helper assets of the premium widgets
 *
 * @since 1.0.0
 */
class Premium_Template_Tags {

	/**
	 * Class instance
	 *
	 * @var instance
	 */
	protected static $instance;

	/**
	 * Settings
	 *
	 * @var settings
	 */
	public static $settings;

	/**
	 * Pages Limit
	 *
	 * @since 3.20.9
	 * @var integer $page_limit
	 */
	public static $page_limit;

	/**
	 * $options is option field of select
	 *
	 * @since 1.0.0
	 * @var integer $page_limit
	 */
	protected $options;

	/**
	 * Class contructor
	 */
	public function __construct() {

		add_action( 'pre_get_posts', array( $this, 'fix_query_offset' ), 1 );
		add_filter( 'found_posts', array( $this, 'fix_found_posts_query' ), 1, 2 );

		add_action( 'wp_ajax_pa_get_posts', array( $this, 'get_posts_query' ) );
		add_action( 'wp_ajax_nopriv_pa_get_posts', array( $this, 'get_posts_query' ) );

		add_action( 'wp_ajax_premium_update_filter', array( $this, 'get_posts_list' ) );
		add_action( 'wp_ajax_premium_update_tax', array( $this, 'get_related_tax' ) );

		add_action( 'wp_ajax_pa_acf_options', array( $this, 'get_acf_options' ) );
		add_action( 'wp_ajax_nopriv_pa_get_acf_options', array( $this, 'get_acf_options' ) );
	}

	/**
	 * Get instance of this class
	 */
	public static function getInstance() {

		if ( ! static::$instance ) {
			static::$instance = new self();
		}

		return static::$instance;
	}

	/**
	 * Get All Posts
	 *
	 * Returns an array of posts/pages
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return $options array posts/pages query
	 */
	public function get_all_posts() {

		$all_posts = get_posts(
			array(
				'posts_per_page'         => 100,
				'post_type'              => array( 'page', 'post' ),
				'update_post_term_cache' => false,
				'update_post_meta_cache' => false,
				'fields'                 => array( 'ids' ),
			)
		);

		if ( ! empty( $all_posts ) && ! is_wp_error( $all_posts ) ) {
			foreach ( $all_posts as $post ) {
				$this->options[ $post->ID ] = strlen( $post->post_title ) > 30 ? substr( $post->post_title, 0, 30 ) . '...' : $post->post_title;
			}
		}
		return $this->options;
	}

	/**
	 * Get ID By Title
	 *
	 * Get Elementor Template ID by title
	 *
	 * @since 3.6.0
	 * @access public
	 *
	 * @param string $title template title.
	 *
	 * @return string $template_id template ID.
	 */
	public function get_id_by_title( $title ) {

		$args = array(
			'post_type'      => 'elementor_library',
			'post_status'    => 'publish',
			'posts_per_page' => 1,
			'title'          => $title,
		);

		$query = new \WP_Query( $args );

		$post_id = '';

		if ( $query->have_posts() ) {
			while ( $query->have_posts() ) {
				$query->the_post();
				$post_id = get_the_ID();

			}
			wp_reset_postdata();
		}

		return $post_id;
	}

	/**
	 * Get Elementor Page List
	 *
	 * Returns an array of Elementor templates
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return $options array Elementor Templates
	 */
	public function get_elementor_page_list() {

		$pagelist = get_posts(
			array(
				'post_type' => 'elementor_library',
				'showposts' => 999,
			)
		);

		if ( ! empty( $pagelist ) && ! is_wp_error( $pagelist ) ) {

			foreach ( $pagelist as $post ) {
				$options[ $post->post_title ] = $post->post_title;
			}

			update_option( 'temp_count', $options );

			return $options;
		}
	}

	/**
	 * Get Elementor Template HTML Content
	 *
	 * @since 3.6.0
	 * @access public
	 *
	 * @param string|int $title   Template Title||id.
	 * @param bool       $id          indicates if $title is the template title or id.
	 *
	 * @return $template_content string HTML Markup of the selected template.
	 */
	public function get_template_content( $title, $id = false ) {

		$frontend = Plugin::$instance->frontend;

		$custom_temp = apply_filters( 'pa_temp_id', false );

		if ( $custom_temp ) {
			$id = $title = $custom_temp;
		}

		if ( ! $id ) {
			$id = $this->get_id_by_title( $title );

			$id = apply_filters( 'wpml_object_id', $id, 'elementor_library', true );
		} else {
			$id = $title;
		}

		$template_content = $frontend->get_builder_content_for_display( $id, false );

		return $template_content;

	}


	/**
	 * Get authors
	 *
	 * Get posts author array
	 *
	 * @since 3.20.3
	 * @access public
	 *
	 * @return array
	 */
	public static function get_authors() {
		$users = get_users( array( 'role__in' => array( 'administrator', 'editor', 'author', 'contributor' ) ) );

		$options = array();

		if ( ! empty( $users ) && ! is_wp_error( $users ) ) {
			foreach ( $users as $user ) {
				if ( 'wp_update_service' !== $user->display_name ) {
					$options[ $user->ID ] = $user->display_name;
				}
			}
		}

		return $options;
	}

	/**
	 * Get types
	 *
	 * Get posts tags array
	 *
	 * @since 3.20.3
	 * @access public
	 *
	 * @return array
	 */
	public static function get_posts_types() {

		$post_types = get_post_types(
			array(
				'public' => true,
			),
			'objects'
		);

		$options = array();

		foreach ( $post_types as $post_type ) {
			$options[ $post_type->name ] = $post_type->label;
		}

		$key = array_search( 'Media', $options, true );

		if ( 'attachment' === $key ) {
			unset( $options[ $key ] );
		}

		return $options;
	}

	/**
	 * Get posts list
	 *
	 * Get posts list array
	 *
	 * @since 4.2.8
	 * @access public
	 */
	public static function get_posts_list() {

		check_ajax_referer( 'pa-blog-widget-nonce', 'nonce' );

		$post_type = isset( $_POST['post_type'] ) ? sanitize_text_field( wp_unslash( $_POST['post_type'] ) ) : '';

		if ( empty( $post_type ) ) {
			wp_send_json_error( __( 'Empty Post Type.', 'premium-addons-for-elementor' ) );
		}

		$list = get_posts(
			array(
				'post_type'              => $post_type,
				'posts_per_page'         => -1,
				'update_post_term_cache' => false,
				'update_post_meta_cache' => false,
			)
		);

		$options = array();

		if ( ! empty( $list ) && ! is_wp_error( $list ) ) {
			foreach ( $list as $post ) {
				$options[ $post->ID ] = $post->post_title;
			}
		}

		wp_send_json_success( wp_json_encode( $options ) );

	}

	/**
	 * Get related taxonomy list
	 *
	 * Get related taxonomy list array
	 *
	 * @since 4.3.1
	 * @access public
	 */
	public static function get_related_tax() {

		check_ajax_referer( 'pa-blog-widget-nonce', 'nonce' );

		$post_type = isset( $_POST['post_type'] ) ? sanitize_text_field( wp_unslash( $_POST['post_type'] ) ) : '';

		if ( empty( $post_type ) ) {
			wp_send_json_error( __( 'Empty Post Type.', 'premium-addons-for-elementor' ) );
		}

		$taxonomy = self::get_taxnomies( $post_type );

		$related_tax = array();

		if ( ! empty( $taxonomy ) ) {

			foreach ( $taxonomy as $index => $tax ) {
				$related_tax[ $index ] = $tax->label;
			}
		}

		wp_send_json_success( wp_json_encode( $related_tax ) );

	}


	/**
	 * Get posts list
	 *
	 * Used to set Premium_Post_Filter control default settings.
	 *
	 * @param string $post_type  post type.
	 *
	 * @return array
	 */
	public static function get_default_posts_list( $post_type ) {
		$list = get_posts(
			array(
				'post_type'              => $post_type,
				'posts_per_page'         => -1,
				'update_post_term_cache' => false,
				'update_post_meta_cache' => false,
				'fields'                 => array( 'ids' ),
			)
		);

		$options = array();

		if ( ! empty( $list ) && ! is_wp_error( $list ) ) {
			foreach ( $list as $post ) {
				$options[ $post->ID ] = $post->post_title;
			}
		}

		return $options;

	}


	/**
	 * Get taxnomies.
	 *
	 * Get post taxnomies for post type
	 *
	 * @since 3.20.3
	 * @access public
	 *
	 * @param string $type Post type.
	 */
	public static function get_taxnomies( $type ) {

		$taxonomies = get_object_taxonomies( $type, 'objects' );
		$data       = array();

		foreach ( $taxonomies as $tax_slug => $tax ) {

			if ( ! $tax->public || ! $tax->show_ui ) {
				continue;
			}

			$data[ $tax_slug ] = $tax;
		}

		return $data;
	}

	/**
	 * Get query args
	 *
	 * Get query arguments array
	 *
	 * @since 3.20.3
	 * @access public
	 *
	 * @return array query args
	 */
	public static function get_query_args() {

		$settings = self::$settings;

		$paged     = self::get_paged();
		$tax_count = 0;

		$post_type = $settings['post_type_filter'];
		$post_id   = get_the_ID();

		$post_args = array(
			'post_type'        => $post_type,
			'posts_per_page'   => empty( $settings['premium_blog_number_of_posts'] ) ? 9999 : $settings['premium_blog_number_of_posts'],
			'paged'            => $paged,
			'post_status'      => 'publish',
			'suppress_filters' => false,
		);

		if ( 'related' === $post_type ) {
			$current_post_type      = get_post_type( $post_id );
			$post_args['post_type'] = $current_post_type;
		}

		$post_args['orderby'] = $settings['premium_blog_order_by'];
		$post_args['order']   = $settings['premium_blog_order'];

		if ( isset( $settings['posts_from'] ) ) {

			if ( '' !== $settings['posts_from'] ) {
				$last_time = strtotime( '-1 ' . $settings['posts_from'] );

				$start_date = date( 'Y-m-d', $last_time );
				$end_date   = date( 'Y-m-d' );

				$post_args['date_query'] = array(
					array(
						'after'     => $start_date,
						'before'    => $end_date,
						'inclusive' => true,
					),
				);

			}
		}

		$excluded_posts = array();

		if ( ! empty( $settings['premium_blog_posts_exclude'] ) && 'post' === $post_type ) {

			if ( 'post__in' === $settings['posts_filter_rule'] ) {
				$post_args['post__in'] = $settings['premium_blog_posts_exclude'];
			} else {
				$excluded_posts = $settings['premium_blog_posts_exclude'];
			}
		} elseif ( 'related' === $post_type ) {

			if ( 'product' === $current_post_type ) {

				$post_cats = self::get_product_cats_ids( $post_id );

				$post_args['tax_query'][] = array(
					'taxonomy' => 'product_cat',
					'field'    => 'term_id',
					'terms'    => $post_cats,
					'operator' => 'IN',
				);

			} else {
				$post_cats = wp_get_post_categories( $post_id );

				if ( ! empty( $post_cats ) ) {
					$post_args['category__in'] = $post_cats;
				}
			}
		} elseif ( ! empty( $settings['custom_posts_filter'] ) && ! in_array( $post_type, array( 'post', 'related' ), true ) ) {

			$keys = array_keys( self::get_default_posts_list( $post_type ) );

			if ( empty( array_diff( ( $settings['custom_posts_filter'] ), $keys ) ) ) {

				if ( 'post__in' === $settings['posts_filter_rule'] ) {
					$post_args['post__in'] = $settings['custom_posts_filter'];
				} else {
					$excluded_posts = $settings['custom_posts_filter'];
				}
			}
		}

		if ( ! empty( $settings['premium_blog_users'] ) ) {

			$post_args[ $settings['author_filter_rule'] ] = $settings['premium_blog_users'];
		}

		if ( 'related' !== $post_type ) {
			// Get all the taxanomies associated with the post type.
			$taxonomy = self::get_taxnomies( $post_type );

			if ( ! empty( $taxonomy ) && ! is_wp_error( $taxonomy ) ) {

				// Get all taxonomy values under the taxonomy.

				$tax_count = 0;
				foreach ( $taxonomy as $index => $tax ) {

					if ( ! empty( $settings[ 'tax_' . $index . '_' . $post_type . '_filter' ] ) ) {

						$operator = $settings[ $index . '_' . $post_type . '_filter_rule' ];

						$post_args['tax_query'][] = array(
							'taxonomy' => $index,
							'field'    => 'slug',
							'terms'    => $settings[ 'tax_' . $index . '_' . $post_type . '_filter' ],
							'operator' => $operator,
						);
						$tax_count++;
					}
				}
			}
		}

		// needs to be checked.
		if ( '' !== $settings['active_cat'] && '*' !== $settings['active_cat'] && 'related' !== $post_type ) {

			$filter_type = $settings['filter_tabs_type'];

			if ( 'tag' === $settings['filter_tabs_type'] ) {
				$filter_type = 'post_tag';
			}

			$post_args['tax_query'][] = array(
				'taxonomy' => $filter_type,
				'field'    => 'slug',
				'terms'    => $settings['active_cat'],
				'operator' => 'IN',
			);

		}

		if ( 0 < $settings['premium_blog_offset'] ) {

			/**
			 * Offset break the pagination. Using WordPress's work around
			 *
			 * @see https://codex.wordpress.org/Making_Custom_Queries_using_Offset_and_Pagination
			 */
			$post_args['offset_to_fix'] = $settings['premium_blog_offset'];

		}

		if ( 'yes' === $settings['ignore_sticky_posts'] ) {
			$excluded_posts = array_merge( $excluded_posts, get_option( 'sticky_posts' ) );
		}

		if ( 'yes' === $settings['query_exclude_current'] || 'related' === $post_type ) {
			array_push( $excluded_posts, $post_id );
		}

		$post_args['post__not_in'] = $excluded_posts;

		return $post_args;
	}

	/**
	 * Retrieves the product's categories IDs.
	 *
	 * @access public
	 * @since 2.8.20
	 *
	 * @param int $prod_id  product id.
	 *
	 * @return array
	 */
	public static function get_product_cats_ids( $prod_id ) {

		$prod_cats = get_the_terms( $prod_id, 'product_cat' );
		$cats_ids  = array();

		foreach ( $prod_cats as $index => $cat ) {
			array_push( $cats_ids, $cat->term_id );
		}

		return $cats_ids;
	}

	/**
	 * Get query posts
	 *
	 * @since 3.20.3
	 * @access public
	 *
	 * @return array query args
	 */
	public function get_query_posts() {

		$post_args = $this->get_query_args();

		$defaults = array(
			'author'         => '',
			'category'       => '',
			'orderby'        => '',
			'posts_per_page' => 1,
		);

		$query_args = wp_parse_args( $post_args, $defaults );

		$query = new \WP_Query( $query_args );

		$total_pages = $query->max_num_pages;

		$this->set_pagination_limit( $total_pages );

		return $query;
	}


	/**
	 * Get paged
	 *
	 * Returns the paged number for the query.
	 *
	 * @since 3.20.0
	 * @return int
	 */
	public static function get_paged() {

		global $wp_the_query, $paged;

		$nonce = isset( $_POST['nonce'] ) ? sanitize_text_field( wp_unslash( $_POST['nonce'] ) ) : false;

		if ( $nonce && wp_verify_nonce( $nonce, 'pa-blog-widget-nonce' ) ) {
			if ( isset( $_POST['page_number'] ) && '' !== $_POST['page_number'] ) {
				return sanitize_text_field( wp_unslash( $_POST['page_number'] ) );
			}
		}

		// Check the 'paged' query var.
		$paged_qv = $wp_the_query->get( 'paged' );

		if ( is_numeric( $paged_qv ) ) {
			return $paged_qv;
		}

		// Check the 'page' query var.
		$page_qv = $wp_the_query->get( 'page' );

		if ( is_numeric( $page_qv ) ) {
			return $page_qv;
		}

		// Check the $paged global?
		if ( is_numeric( $paged ) ) {
			return $paged;
		}

		return 0;
	}

	/**
	 * Get Post Content
	 *
	 * @access public
	 * @since 3.20.3
	 *
	 * @param string  $source content source.
	 * @param integer $excerpt_length excerpt length.
	 * @param string  $cta_type call to action type.
	 * @param string  $read_more readmore text.
	 */
	public function render_post_content( $source, $excerpt_length, $cta_type, $read_more ) {

		$excerpt = '';

		if ( 'full' === $source ) {

			// Print post full content.
			the_content();

		} else {
			$excerpt = trim( get_the_excerpt() );

			$words = explode( ' ', $excerpt, $excerpt_length + 1 );

			if ( count( $words ) > $excerpt_length ) {

				if ( ! has_excerpt() ) {
					array_pop( $words );
					if ( 'dots' === $cta_type ) {
						array_push( $words, '…' );
					}
				}
			}

			$excerpt = implode( ' ', $words );
		}

		return $excerpt;

	}

	/**
	 * Get Post Excerpt Link
	 *
	 * @since 3.20.9
	 * @access public
	 *
	 * @param string $read_more read more text.
	 * @param string $post_target  link target value.
	 * @param string $link_class   link class
	 */
	public static function get_post_excerpt_link( $read_more, $post_target, $link_class_prefix ) {

		if ( empty( $read_more ) ) {
			return;
		}

		echo '<div class="' . $link_class_prefix . 'excerpt-link-wrap">';
			echo '<a href="' . esc_url( get_permalink() ) . '" target="' . esc_attr( $post_target ) . '" class="' . $link_class_prefix . 'excerpt-link elementor-button">';
				echo wp_kses_post( $read_more );
			echo '</a>';
		echo '</div>';

	}

	/**
	 * Set Widget Settings
	 *
	 * @since 3.20.8
	 * @access public
	 *
	 * @param object $settings widget settings.
	 * @param string $active_cat active category.
	 */
	public function set_widget_settings( $settings, $active_cat = '' ) {

		$settings['active_cat'] = $active_cat;
		self::$settings         = $settings;
	}

	/**
	 * Set Pagination Limit
	 *
	 * @since 3.20.8
	 * @access public
	 *
	 * @param integer $pages pages number.
	 */
	public function set_pagination_limit( $pages ) {
		self::$page_limit = $pages;
	}

	/**
	 * Get Post Thumbnail
	 *
	 * Renders HTML markup for post thumbnail
	 *
	 * @since 3.0.5
	 * @access protected
	 *
	 * @param string $target target.
	 */
	protected function get_post_thumbnail( $target, $widget = '' ) {

		$settings = self::$settings;

		$settings['featured_image'] = array(
			'id' => get_post_thumbnail_id(),
		);

		$thumbnail_html = Group_Control_Image_Size::get_attachment_image_html( $settings, 'featured_image' );

		if ( empty( $thumbnail_html ) ) {
			return;
		}

		if ( 'magazine' !== $widget ) {

			$skin = $settings['premium_blog_skin'];

			if ( in_array( $skin, array( 'modern', 'cards' ), true ) ) { ?>
				<a href="<?php esc_url( the_permalink() ); ?>" target="<?php echo esc_attr( $target ); ?>">
				<?php
			}

			echo wp_kses_post( $thumbnail_html );
			if ( in_array( $skin, array( 'modern', 'cards' ), true ) ) {
				?>
				</a>
				<?php
			}
		} else {
			echo wp_kses_post( $thumbnail_html );
		}
	}

	/**
	 * Render post title
	 *
	 * @since 3.4.4
	 * @access protected
	 *
	 * @param string $link_target target.
	 * @param string $key unique key.
	 * @param string $class title class.
	 */
	protected function render_post_title( $link_target, $key, $class ) {

		$settings = self::$settings;

		$this->add_render_attribute( $key . '_title', 'class', $class );

		$title_tag = Helper_Functions::validate_html_tag( $settings['premium_blog_title_tag'] );

		?>
		<<?php echo wp_kses_post( $title_tag . ' ' . $this->get_render_attribute_string( $key . '_title' ) ); ?>>
			<a href="<?php the_permalink(); ?>" target="<?php echo esc_attr( $link_target ); ?>">
				<?php esc_html( the_title() ); ?>
			</a>
		</<?php echo wp_kses_post( $title_tag ); ?>>
		<?php
	}

	/**
	 * Get Post Meta.
	 *
	 * @since 3.4.4
	 * @access protected
	 *
	 * @param string $link_target target.
	 */
	protected function get_post_meta( $link_target ) {

		$settings = self::$settings;

		$skin = $settings['premium_blog_skin'];

		$author_meta = $settings['premium_blog_author_meta'];

		$data_meta = $settings['premium_blog_date_meta'];

		$categories_meta = $settings['premium_blog_categories_meta'];

		$comments_meta = $settings['premium_blog_comments_meta'];

		if ( 'yes' === $data_meta ) {
			$date_format = get_option( 'date_format' );
		}

		if ( 'yes' === $comments_meta ) {

			$comments_strings = array(
				'no-comments'       => __( 'No Comments', 'premium-addons-for-elementor' ),
				'one-comment'       => __( '1 Comment', 'premium-addons-for-elementor' ),
				'multiple-comments' => __( '% Comments', 'premium-addons-for-elementor' ),
			);

		}

		?>
		<div class="premium-blog-entry-meta">
			<?php if ( 'yes' === $author_meta ) : ?>
				<div class="premium-blog-post-author premium-blog-meta-data">
					<i class="fa fa-user fa-fw" aria-hidden="true"></i>
					<?php the_author_posts_link(); ?>
				</div>
			<?php endif; ?>

			<?php if ( 'yes' === $data_meta ) { ?>
				<span class="premium-blog-meta-separator">•</span>
				<div class="premium-blog-post-time premium-blog-meta-data">
					<i class="fa fa-clock-o" aria-hidden="true"></i>
					<span><?php the_time( $date_format ); ?></span>
				</div>
			<?php } ?>

			<?php if ( 'yes' === $categories_meta && ! in_array( $skin, array( 'side', 'banner' ), true ) ) : ?>
				<span class="premium-blog-meta-separator">•</span>
				<div class="premium-blog-post-categories premium-blog-meta-data">
					<i class="fa fa-align-left fa-fw" aria-hidden="true"></i>
					<?php the_category( ', ' ); ?>
				</div>
			<?php endif; ?>

			<?php if ( 'yes' === $comments_meta ) : ?>
				<span class="premium-blog-meta-separator">•</span>
				<div class="premium-blog-post-comments premium-blog-meta-data">
					<i class="fa fa-comments-o fa-fw" aria-hidden="true"></i>
					<?php comments_popup_link( $comments_strings['no-comments'], $comments_strings['one-comment'], $comments_strings['multiple-comments'], '', $comments_strings['no-comments'] ); ?>
				</div>
			<?php endif; ?>
		</div>
		<?php
	}

	/**
	 * Renders post content.
	 *
	 * @since 3.0.5
	 * @access protected
	 *
	 * @param array $options  post content options.
	 */
	protected function get_post_content( $options ) {

		if ( 'yes' !== $options['excerpt'] || empty( $options['length'] ) ) {
			return;
		}

		$this->add_render_attribute( 'post-content-inner-' . get_the_ID(), 'class', $options['content_classes'] );

		?>
		<div <?php echo wp_kses_post( $this->get_render_attribute_string( 'post-content-inner-' . get_the_ID() ) ); ?>>
		<?php
		// Get post content.
		if ( 'excerpt' === $options['source'] ) :
			echo '<p class="' . $options['class'] . '">';
		endif;
			echo wp_kses_post( $this->render_post_content( $options['source'], $options['length'], $options['excerpt_type'], $options['excerpt_text'] ) );
		if ( 'excerpt' === $options['source'] ) :
			echo '</p>';
		endif;

		// Get post excerpt.
		if ( 'link' === $options['excerpt_type'] ) :
			$this->get_post_excerpt_link( $options['excerpt_text'], $options['target'], $options['excerpt_class_prefix'] );
		endif;

		?>
		 </div>
		 <?php
	}

	/**
	 * Renders post skin
	 *
	 * @since 3.0.5
	 * @access protected
	 */
	public function get_post_layout() {

		$settings = self::$settings;

		$post_tag = 'yes' === $settings['premium_blog_article_tag_switcher'] ? 'article' : 'div';

		$image_effect = $settings['premium_blog_hover_image_effect'];

		$post_effect = $settings['premium_blog_hover_color_effect'];

		$total = self::$page_limit;

		$target = 'yes' === $settings['premium_blog_new_tab'] ? '_blank' : '_self';

		$content_options = array(
			'excerpt'              => $settings['premium_blog_excerpt'],
			'length'               => $settings['premium_blog_excerpt_length'],
			'target'               => $target,
			'source'               => $settings['content_source'],
			'excerpt_type'         => $settings['premium_blog_excerpt_type'],
			'excerpt_text'         => $settings['premium_blog_excerpt_text'],
			'class'                => 'premium-blog-post-content',
			'excerpt_class_prefix' => 'premium-blog-',
			'content_classes'      => array( 'premium-blob-content-inner-wrapper' ),
		);

		$skin = $settings['premium_blog_skin'];

		$post_id = get_the_ID();

		$widget_id = $settings['widget_id'];

		$key = sprintf( 'post_%s_%s', $widget_id, $post_id );

		$tax_key = sprintf( '%s_tax', $key );

		$wrap_key = sprintf( '%s_wrap', $key );

		$content_key = sprintf( '%s_content', $key );

		$post_type = $settings['post_type_filter'];

		$this->add_render_attribute(
			$tax_key,
			array(
				'class'      => 'premium-blog-post-outer-container',
				'data-total' => $total,
			)
		);

		$this->add_render_attribute(
			$wrap_key,
			'class',
			array(
				'premium-blog-post-container',
				'premium-blog-skin-' . $skin,
			)
		);

		$thumb = ( ! has_post_thumbnail() || 'yes' !== $settings['show_featured_image'] ) ? 'empty-thumb' : '';

		if ( 'yes' === $settings['premium_blog_cat_tabs'] && 'yes' !== $settings['premium_blog_carousel'] ) {

			$filter_rule = $settings['filter_tabs_type'];

			$taxonomies = 'category' === $filter_rule ? get_the_category( $post_id ) : get_the_tags( $post_id );

			if ( ! empty( $taxonomies ) ) {
				foreach ( $taxonomies as $index => $taxonomy ) {

					$taxonomy_key = 'category' === $filter_rule ? $taxonomy->slug : $taxonomy->name;

					$attr_key = str_replace( ' ', '-', $taxonomy_key );

					$this->add_render_attribute( $tax_key, 'class', strtolower( $attr_key ) );
				}
			}
		}

		$this->add_render_attribute(
			$content_key,
			'class',
			array(
				'premium-blog-content-wrapper',
				$thumb,
			)
		);

		?>
		<<?php echo wp_kses_post( $post_tag . ' ' . $this->get_render_attribute_string( $tax_key ) ); ?>>
			<div <?php echo wp_kses_post( $this->get_render_attribute_string( $wrap_key ) ); ?>>
				<?php if ( empty( $thumb ) ) : ?>
					<div class="premium-blog-thumb-effect-wrapper">
						<div class="premium-blog-thumbnail-container <?php echo esc_attr( 'premium-blog-' . $image_effect . '-effect' ); ?>">
							<?php
								$this->get_post_thumbnail( $target );
							if ( 'none' !== $settings['shape_divider'] ) {
								$this->render_mask_html( $settings['shape_divider'] );
							}
							?>
						</div>
						<?php if ( in_array( $skin, array( 'modern', 'cards' ), true ) ) : ?>
							<div class="premium-blog-effect-container <?php echo esc_attr( 'premium-blog-' . $post_effect . '-effect' ); ?>">
								<a class="premium-blog-post-link" href="<?php the_permalink(); ?>" target="<?php echo esc_attr( $target ); ?>"><span><?php esc_html( the_title() ); ?></span></a>
								<?php if ( 'squares' === $settings['premium_blog_hover_color_effect'] ) { ?>
									<div class="premium-blog-squares-square-container"></div>
								<?php } ?>
							</div>
						<?php else : ?>
							<div class="premium-blog-thumbnail-overlay">
								<a class="elementor-icon" href="<?php the_permalink(); ?>" target="<?php echo esc_attr( $target ); ?>" aria-hidden="true"><span><?php esc_html( the_title() ); ?></span></a>
							</div>
						<?php endif; ?>
					</div>
				<?php endif; ?>
				<?php if ( 'cards' === $skin ) : ?>
					<?php if ( 'yes' === $settings['premium_blog_author_img_switcher'] ) : ?>
						<div class="premium-blog-author-thumbnail">
							<?php echo get_avatar( get_the_author_meta( 'ID' ), 128, '', get_the_author_meta( 'display_name' ) ); ?>
						</div>
					<?php endif; ?>
				<?php endif; ?>
				<div <?php echo wp_kses_post( $this->get_render_attribute_string( $content_key ) ); ?>>

					<div class="premium-blog-inner-container">

						<?php if ( in_array( $skin, array( 'side', 'banner' ), true ) && 'yes' === $settings['premium_blog_categories_meta'] ) { ?>
							<div class="premium-blog-cats-container">
								<ul class="post-categories">
									<?php
										$post_cats     = get_the_category();
										$cats_repeater = $settings['categories_repeater'];
									if ( count( $post_cats ) ) {
										foreach ( $post_cats as $index => $cat ) {
											$class = isset( $cats_repeater[ $index ] ) ? 'elementor-repeater-item-' . $cats_repeater[ $index ]['_id'] : '';
											echo wp_kses_post( sprintf( '<li><a href="%s" class="%s">%s</a></li>', get_category_link( $cat->cat_ID ), $class, $cat->name ) );
										}
									}

									?>
								</ul>
							</div>
						<?php } ?>
						<?php
							$this->render_post_title( $target, $key, 'premium-blog-entry-title' );
						if ( 'cards' !== $skin ) {
							$this->get_post_meta( $target );
						}

						?>

					</div>

					<?php

						do_action( 'pa_before_post_content' );

						$this->get_post_content( $content_options );

					if ( 'cards' === $skin ) {
						$this->get_post_meta( $target );
					}

						do_action( 'pa_after_post_content' );

					?>
					<?php if ( 'yes' === $settings['premium_blog_tags_meta'] && has_tag() ) : ?>
						<div class="premium-blog-post-tags-container">
								<i class="fa fa-tags fa-fw" aria-hidden="true"></i>
								<?php the_tags( ' ', apply_filters( 'pa_post_tags', ', ' ) ); ?>
						</div>
					<?php endif; ?>

				</div>
			</div>
		</<?php echo wp_kses_post( $post_tag ); ?>>

		<?php
	}

	/**
	 * Render Posts.
	 *
	 * @since 3.20.9
	 * @access public
	 */
	public function render_posts() {

		$query = $this->get_query_posts();

		$posts = $query->posts;

		if ( count( $posts ) ) {
			global $post;

			foreach ( $posts as $post ) {
				setup_postdata( $post );
				$this->get_post_layout();
			}
		}

		wp_reset_postdata();

	}

	/**
	 * Inner Render
	 *
	 * @since 3.20.9
	 * @access public
	 *
	 * @param object $widget widget.
	 * @param string $active_cat active category.
	 */
	public function inner_render( $widget, $active_cat, $page_num, $req_type ) {

		ob_start();

		$settings = $widget->get_settings();

		$settings['widget_id'] = $widget->get_id();

		$widget_name = $widget->get_name();

		if ( 'premium-smart-post-listing' === $widget_name ) {
			$settings['widget_type'] = 'premium-smart-listing';
		} else {
			$settings['widget_type'] = $widget_name;
		}

		$this->set_widget_settings( $settings, $active_cat );

		if ( 'premium-smart-post-listing' === $widget_name ) {
			$this->render_smart_posts( $page_num, $req_type );
		} else {
			$this->render_posts();
		}

		return ob_get_clean();
	}

	/**
	 * Get Empty Query Message.
	 *
	 * @since 3.20.3
	 * @access protected
	 *
	 * @param string $notice empty query notice.
	 */
	public function get_empty_query_message( $notice ) {

		if ( empty( $notice ) ) {
			$notice = __( 'The current query has no posts. Please make sure you have published items matching your query.', 'premium-addons-for-elementor' );
		}

		?>
	   <div class="premium-error-notice">
		   <?php echo wp_kses_post( $notice ); ?>
	   </div>
		<?php
	}

	/**
	 * Render Pagination
	 *
	 * Written in PHP and used to generate the final HTML for pagination
	 *
	 * @since 3.20.3
	 * @access protected
	 */
	public function render_pagination() {

		$settings = self::$settings;

		if ( 'yes' !== $settings['premium_blog_paging'] ) {
			return;
		}

		$pages = self::$page_limit;

		if ( ! empty( $settings['max_pages'] ) ) {
			$pages = min( $settings['max_pages'], $pages );
		}

		$paged = $this->get_paged();

		$current_page = $paged;

		if ( ! $current_page ) {
			$current_page = 1;
		}

		if ( isset( $settings['pagination_type'] ) && 'default' === $settings['pagination_type'] ) {

			$prev_disabled = 1 == $current_page ? 'disabled' : '';
			$next_disabled = $current_page == $pages ? 'disabled' : '';

			$this->add_navigation_arrows( $prev_disabled, $next_disabled );

		} else {

			$container_class = 'premium-addon-blog' !== $settings['widget_type'] ? $settings['widget_type'] . '__pagination-container' : 'premium-blog-pagination-container';
			$nav_links       = paginate_links(
				array(
					'current'   => $current_page,
					'total'     => $pages,
					'prev_next' => 'yes' === $settings['pagination_strings'] ? true : false,
					'prev_text' => sprintf( '« %s', $settings['premium_blog_prev_text'] ),
					'next_text' => sprintf( '%s »', $settings['premium_blog_next_text'] ),
					'type'      => 'array',
				)
			);

			if ( ! is_array( $nav_links ) ) {
				return;
			}

			?>
			<nav class="<?php echo esc_attr( $container_class ); ?>" role="navigation" aria-label="<?php echo esc_attr( __( 'Pagination', 'premium-addons-for-elementor' ) ); ?>">
				<?php echo wp_kses_post( implode( PHP_EOL, $nav_links ) ); ?>
			</nav>
			<?php
		}

	}

	public function add_navigation_arrows( $prev_disabled = '', $next_disabled = '' ) {
		?>
		<nav class="premium-smart-listing__pagination-container" role="navigation" aria-label="<?php echo esc_attr( __( 'Pagination', 'premium-addons-for-elementor' ) ); ?>">
			<button class="prev page-numbers" aria-label="<?php echo esc_attr( __( 'Previous', 'premium-addons-for-elementor' ) ); ?>" <?php echo esc_attr( $prev_disabled ); ?>>
				<i class="fa fa-angle-left"></i>
			</button><button class="next page-numbers" aria-label="<?php echo esc_attr( __( 'Next', 'premium-addons-for-elementor' ) ); ?>" <?php echo esc_attr( $next_disabled ); ?>>
				<i class="fa fa-angle-right"></i>
			</button>
		</nav>
		<?php
	}

	/**
	 * Inner Pagination Render
	 *
	 * Used to generate the pagination to be used with the AJAX call
	 *
	 * @since 3.20.3
	 * @access protected
	 */
	public function inner_pagination_render() {

		ob_start();

		$this->render_pagination();

		return ob_get_clean();

	}

	/**
	 * Get Posts Query
	 *
	 * Get posts using AJAX
	 *
	 * @since 3.20.9
	 * @access public
	 */
	public function get_posts_query() {

		check_ajax_referer( 'pa-blog-widget-nonce', 'nonce' );

		if ( ! isset( $_POST['page_id'] ) || ! isset( $_POST['widget_id'] ) ) {
			return;
		}

		$doc_id     = isset( $_POST['page_id'] ) ? sanitize_text_field( wp_unslash( $_POST['page_id'] ) ) : '';
		$elem_id    = isset( $_POST['widget_id'] ) ? sanitize_text_field( wp_unslash( $_POST['widget_id'] ) ) : '';
		$active_cat = isset( $_POST['category'] ) ? wp_unslash( $_POST['category'] ) : ''; // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
		$page_num   = isset( $_POST['page_number'] ) ? sanitize_text_field( wp_unslash( $_POST['page_number'] ) ) : '';
		$req_type   = isset( $_POST['req_type'] ) ? sanitize_text_field( wp_unslash( $_POST['req_type'] ) ) : '';

		$elementor = Plugin::$instance;
		$meta      = $elementor->documents->get( $doc_id )->get_elements_data();

		$widget_data = $this->find_element_recursive( $meta, $elem_id );

		$data = array(
			'ID'     => '',
			'posts'  => '',
			'paging' => '',
		);

		if ( null !== $widget_data ) {

			$widget = $elementor->elements_manager->create_element_instance( $widget_data );

			$posts = $this->inner_render( $widget, $active_cat, $page_num, $req_type );

			$pagination = $this->inner_pagination_render();

			$data['paging'] = $pagination;

			$data['ID']    = $widget->get_id();
			$data['posts'] = $posts;
		}

		wp_send_json_success( $data );

	}

	/**
	 * Get Acf Options.
	 *
	 * Get options using AJAX.
	 *
	 * @since 4.4.8
	 * @access public
	 */
	public function get_acf_options() {

		check_ajax_referer( 'pa-blog-widget-nonce', 'nonce' );

		$query_options = isset( $_POST['query_options'] ) ? array_map( 'strip_tags', $_POST['query_options'] ) : ''; // phpcs:ignore

		$query = new \WP_Query(
			array(
				'post_type'      => 'acf-field',
				'posts_per_page' => -1,
			)
		);

		$results = ACF_Helper::format_acf_query_result( $query->posts, $query_options );

		wp_send_json_success( wp_json_encode( $results ) );
	}


	/**
	 * Get Current Product Swap Image.
	 *
	 * @since 3.4.0
	 * @access public
	 *
	 * @param string $size image size.
	 */
	public static function get_current_product_swap_image( $size ) {

		global $product;

		$attachment_ids = $product->get_gallery_image_ids();

		if ( $attachment_ids ) {

			$image_size = apply_filters( 'single_product_archive_thumbnail_size', $size );

			echo wp_kses_post( apply_filters( 'pa_woo_product_swap_image', wp_get_attachment_image( reset( $attachment_ids ), $image_size, false, array( 'class' => 'premium-woo-product__on_hover' ) ) ) );
		}
	}

	/**
	 * Get Current Product Images
	 *
	 * Gets current product images
	 *
	 * @since 3.4.0
	 * @access public
	 *
	 * @param string $size image size.
	 */
	public static function get_current_product_gallery_images( $size ) {

		global $product;

		$attachment_ids = $product->get_gallery_image_ids();

		if ( $attachment_ids ) {

			$image_size = apply_filters( 'single_product_archive_thumbnail_size', $size );

			foreach ( $attachment_ids as $index => $id ) {
				if ( $index > 2 ) {
					break;
				}

				echo wp_kses_post( apply_filters( 'pa_woo_product_gallery_image', wp_get_attachment_image( $id, $image_size, false, array( 'class' => 'premium-woo-product__gallery_image' ) ) ) );
			}
		}
	}

	/**
	 * Get Current Product Images
	 *
	 * Gets current product images
	 *
	 * @since 3.4.0
	 * @access public
	 *
	 * @param string $size image size.
	 */
	public static function get_current_product_linked_images( $size ) {

		global $product;

		$attachment_ids = $product->get_gallery_image_ids();

		if ( $attachment_ids ) {

			$image_size = apply_filters( 'single_product_archive_thumbnail_size', $size );

			foreach ( $attachment_ids as $index => $id ) {
				if ( $index > 2 ) {
					break;
				}

				woocommerce_template_loop_product_link_open();

				echo wp_kses_post( apply_filters( 'pa_woo_product_gallery_image', wp_get_attachment_image( $id, $image_size, false, array( 'class' => 'premium-woo-product__gallery_image' ) ) ) );

				woocommerce_template_loop_product_link_close(); // closes product anchor tag.

			}
		}
	}


	/**
	 * Get Current Product Category
	 *
	 * @since 3.4.0
	 * @access public
	 */
	public static function get_current_product_category() {
		if ( apply_filters( 'pa_woo_product_parent_category', true ) ) :
			?>
			<span class="premium-woo-product-category">
				<?php
					global $product;
					$product_categories = function_exists( 'wc_get_product_category_list' ) ? wc_get_product_category_list( get_the_ID(), '&', '', '' ) : $product->get_categories( '&', '', '' );

					$product_categories = wp_strip_all_tags( $product_categories );

				if ( $product_categories ) {
					list( $parent_cat ) = explode( '&', $product_categories );

					echo esc_html( $parent_cat );
				}
				?>
			</span>
			<?php
		endif;
	}

	/**
	 * Get Product Short Description
	 *
	 * @since 3.4.0
	 * @access public
	 *
	 * @param integer $length excerpt length.
	 */
	public static function get_product_excerpt( $length ) {

		if ( has_excerpt() ) {

			$excerpt = trim( get_the_excerpt() );

			if ( ! empty( $length ) ) {

				$words = explode( ' ', $excerpt, $length + 1 );

				if ( count( $words ) > $length ) {

					array_pop( $words );

					array_push( $words, '…' );

				}

				$excerpt = implode( ' ', $words );

			}

			echo '<div class="premium-woo-product-desc">';
				echo wp_kses_post( $excerpt );
			echo '</div>';
		}

	}


	/**
	 * Get Widget Setting data.
	 *
	 * @since 1.7.0
	 * @access public
	 * @param array  $elements Element array.
	 * @param string $id Element ID.
	 * @return Boolean True/False.
	 */
	public function find_element_recursive( $elements, $id ) {

		foreach ( $elements as $element ) {
			if ( $id === $element['id'] ) {
				return $element;
			}

			if ( ! empty( $element['elements'] ) ) {
				$element = $this->find_element_recursive( $element['elements'], $id );

				if ( $element ) {
					return $element;
				}
			}
		}

		return false;
	}

	/**
	 * Add render attribute.
	 *
	 * Used to add attributes to a specific HTML element.
	 *
	 * The HTML tag is represented by the element parameter, then you need to
	 * define the attribute key and the attribute key. The final result will be:
	 * `<element attribute_key="attribute_value">`.
	 *
	 * Example usage:
	 *
	 * `$this->add_render_attribute( 'wrapper', 'class', 'custom-widget-wrapper-class' );`
	 * `$this->add_render_attribute( 'widget', 'id', 'custom-widget-id' );`
	 * `$this->add_render_attribute( 'button', [ 'class' => 'custom-button-class', 'id' => 'custom-button-id' ] );`
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param array|string $element   The HTML element.
	 * @param array|string $key       Optional. Attribute key. Default is null.
	 * @param array|string $value     Optional. Attribute value. Default is null.
	 * @param bool         $overwrite Optional. Whether to overwrite existing
	 *                                attribute. Default is false, not to overwrite.
	 *
	 * @return Element_Base Current instance of the element.
	 */
	public function add_render_attribute( $element, $key = null, $value = null, $overwrite = false ) {
		if ( is_array( $element ) ) {
			foreach ( $element as $element_key => $attributes ) {
				$this->add_render_attribute( $element_key, $attributes, null, $overwrite );
			}

			return $this;
		}

		if ( is_array( $key ) ) {
			foreach ( $key as $attribute_key => $attributes ) {
				$this->add_render_attribute( $element, $attribute_key, $attributes, $overwrite );
			}

			return $this;
		}

		if ( empty( $this->_render_attributes[ $element ][ $key ] ) ) {
			$this->_render_attributes[ $element ][ $key ] = array();
		}

		settype( $value, 'array' );

		if ( $overwrite ) {
			$this->_render_attributes[ $element ][ $key ] = $value;
		} else {
			$this->_render_attributes[ $element ][ $key ] = array_merge( $this->_render_attributes[ $element ][ $key ], $value );
		}

		return $this;
	}

	/**
	 * Get render attribute string.
	 *
	 * Used to retrieve the value of the render attribute.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param array|string $element The element.
	 *
	 * @return string Render attribute string, or an empty string if the attribute
	 *                is empty or not exist.
	 */
	public function get_render_attribute_string( $element ) {

		if ( empty( $this->_render_attributes[ $element ] ) ) {
			return '';
		}

		$render_attributes = $this->_render_attributes[ $element ];

		$attributes = array();

		foreach ( $render_attributes as $attribute_key => $attribute_values ) {
			$attributes[] = sprintf( '%1$s="%2$s"', $attribute_key, esc_attr( implode( ' ', $attribute_values ) ) );
		}

		return implode( ' ', $attributes );
	}

	/**
	 * Fix Query Offset.
	 *
	 * @since 4.0.8
	 * @access public
	 *
	 * @param object $query query object.
	 */
	public function fix_query_offset( &$query ) {

		if ( ! empty( $query->query_vars['offset_to_fix'] ) ) {
			if ( $query->is_paged ) {
				$query->query_vars['offset'] = $query->query_vars['offset_to_fix'] + ( ( $query->query_vars['paged'] - 1 ) * $query->query_vars['posts_per_page'] );
			} else {
				$query->query_vars['offset'] = $query->query_vars['offset_to_fix'];
			}
		}
	}

	/**
	 * Fix Found Posts Query
	 *
	 * @since 4.0.8
	 * @access public
	 *
	 * @param int    $found_posts found posts.
	 * @param object $query query object.
	 */
	public function fix_found_posts_query( $found_posts, $query ) {

		$offset_to_fix = $query->get( 'offset_to_fix' );

		if ( $offset_to_fix ) {
			$found_posts -= $offset_to_fix;
		}

		return $found_posts;
	}

	/**
	 * Render Mask HTML
	 *
	 * Renders markup for featured image mask.
	 *
	 * @since 4.2.6
	 * @access public
	 *
	 * @param string $mask mask type.
	 */
	public function render_mask_html( $mask ) {

		$mask_array = array(
			'arrow'            => '<svg class="premium-blog-shape-divider-svg" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 700 10" preserveAspectRatio="none"><path d="M350,10L340,0h20L350,10z"/></svg>',

			'book'             => '<svg class="premium-blog-shape-divider-svg" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1000 100" preserveAspectRatio="none"><path class="elementor-shape-fill" d="M194,99c186.7,0.7,305-78.3,306-97.2c1,18.9,119.3,97.9,306,97.2c114.3-0.3,194,0.3,194,0.3s0-91.7,0-100c0,0,0,0,0-0 L0,0v99.3C0,99.3,79.7,98.7,194,99z"/></svg>',

			'cloud'            => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 283.5 27.8" preserveAspectRatio="xMidYMax slice"><path d="M265.8 3.5c-10.9 0-15.9 6.2-15.9 6.2s-3.6-3.5-9.2-.9c-9.1 4.1-4.4 13.4-4.4 13.4s-1.2.2-1.9.9c-.6.7-.5 1.9-.5 1.9s-1-.5-2.3-.2c-1.3.3-1.6 1.4-1.6 1.4s.4-3.4-1.5-5c-3.9-3.4-8.3-.2-8.3-.2s-.6-.7-.9-.9c-.4-.2-1.2-.2-1.2-.2s-4.4-3.6-11.5-2.6-10.4 7.9-10.4 7.9-.5-3.3-3.9-4.9c-4.8-2.4-7.4 0-7.4 0s2.4-4.1-1.9-6.4-6.2 1.2-6.2 1.2-.9-.5-2.1-.5-2.3 1.1-2.3 1.1.1-.7-1.1-1.1c-1.2-.4-2 0-2 0s3.6-6.8-3.5-8.9c-6-1.8-7.9 2.6-8.4 4-.1-.3-.4-.7-.9-1.1-1-.7-1.3-.5-1.3-.5s1-4-1.7-5.2c-2.7-1.2-4.2 1.1-4.2 1.1s-3.1-1-5.7 1.4-2.1 5.5-2.1 5.5-.9 0-2.1.7-1.4 1.7-1.4 1.7-1.7-1.2-4.3-1.2c-2.6 0-4.5 1.2-4.5 1.2s-.7-1.5-2.8-2.4c-2.1-.9-4 0-4 0s2.6-5.9-4.7-9c-7.3-3.1-12.6 3.3-12.6 3.3s-.9 0-1.9.2c-.9.2-1.5.9-1.5.9S99.4 3 94.9 3.9c-4.5.9-5.7 5.7-5.7 5.7s-2.8-5-12.3-3.9-11.1 6-11.1 6-1.2-1.4-4-.7c-.8.2-1.3.5-1.8.9-.9-2.1-2.7-4.9-6.2-4.4-3.2.4-4 2.2-4 2.2s-.5-.7-1.2-.7h-1.4s-.5-.9-1.7-1.4-2.4 0-2.4 0-2.4-1.2-4.7 0-3.1 4.1-3.1 4.1-1.7-1.4-3.6-.7c-1.9.7-1.9 2.8-1.9 2.8s-.5-.5-1.7-.2c-1.2.2-1.4.7-1.4.7s-.7-2.3-2.8-2.8c-2.1-.5-4.3.2-4.3.2s-1.7-5-11.1-6c-3.8-.4-6.6.2-8.5 1v21.2h283.5V11.1c-.9.2-1.6.4-1.6.4s-5.2-8-16.1-8z"/></svg>',

			'curve'            => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 400 35" preserveAspectRatio="none"><path class="st0" d="M0,33.6C63.8,11.8,130.8,0.2,200,0.2s136.2,11.6,200,33.4v1.2H0V33.6z"/></svg>',

			'curve-asymmetric' => '<svg class="premium-blog-shape-divider-svg" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1000 100" preserveAspectRatio="none"><path class="elementor-shape-fill" d="M0,0c0,0,0,6,0,6.7c0,18,240.2,93.6,615.2,92.6C989.8,98.5,1000,25,1000,6.7c0-0.7,0-6.7,0-6.7H0z"/></svg>',

			'drops'            => '<svg class="premium-blog-shape-divider-svg" xmlns="http://www.w3.org/2000/svg" height="100%" viewBox="0 0 283.5 27.8" preserveAspectRatio="xMidYMax slice"><path d="M0 0v1.4c.6.7 1.1 1.4 1.4 2 2 3.8 2.2 6.6 1.8 10.8-.3 3.3-2.4 9.4 0 12.3 1.7 2 3.7 1.4 4.6-.9 1.4-3.8-.7-8.2-.6-12 .1-3.7 3.2-5.5 6.9-4.9 4 .6 4.8 4 4.9 7.4.1 1.8-1.1 7 0 8.5.6.8 1.6 1.2 2.4.5 1.4-1.1.1-5.4.1-6.9.1-3.7.3-8.6 4.1-10.5 5-2.5 6.2 1.6 5.4 5.6-.4 1.7-1 9.2 2.9 6.3 1.5-1.1.7-3.5.5-4.9-.4-2.4-.4-4.3 1-6.5.9-1.4 2.4-3.1 4.2-3 2.4.1 2.7 2.2 4 3.7 1.5 1.8 1.8 2.2 3 .1 1.1-1.9 1.2-2.8 3.6-3.3 1.3-.3 4.8-1.4 5.9-.5 1.5 1.1.6 2.8.4 4.3-.2 1.1-.6 4 1.8 3.4 1.7-.4-.3-4.1.6-5.6 1.3-2.2 5.8-1.4 7 .5 1.3 2.1.5 5.8.1 8.1s-1.2 5-.6 7.4c1.3 5.1 4.4.9 4.3-2.4-.1-4.4-2-8.8-.5-13 .9-2.4 4.6-6.6 7.7-4.5 2.7 1.8.5 7.8.2 10.3-.2 1.7-.8 4.6.2 6.2.9 1.4 2 1.5 2.6-.3.5-1.5-.9-4.5-1-6.1-.2-1.7-.4-3.7.2-5.4 1.8-5.6 3.5 2.4 6.3.6 1.4-.9 4.3-9.4 6.1-3.1.6 2.2-1.3 7.8.7 8.9 4.2 2.3 1.5-7.1 2.2-8 3.1-4 4.7 3.8 6.1 4.1 3.1.7 2.8-7.9 8.1-4.5 1.7 1.1 2.9 3.3 3.2 5.2.4 2.2-1 4.5-.6 6.6 1 4.3 4.4 1.5 4.4-1.7 0-2.7-3-8.3 1.4-9.1 4.4-.9 7.3 3.5 7.8 6.9.3 2-1.5 10.9 1.3 11.3 4.1.6-3.2-15.7 4.8-15.8 4.7-.1 2.8 4.1 3.9 6.6 1 2.4 2.1 1 2.3-.8.3-1.9-.9-3.2 1.3-4.3 5.9-2.9 5.9 5.4 5.5 8.5-.3 2-1.7 8.4 2 8.1 6.9-.5-2.8-16.9 4.8-18.7 4.7-1.2 6.1 3.6 6.3 7.1.1 1.7-1.2 8.1.6 9.1 3.5 2 1.9-7 2-8.4.2-4 1.2-9.6 6.4-9.8 4.7-.2 3.2 4.6 2.7 7.5-.4 2.2 1.3 8.6 3.8 4.4 1.1-1.9-.3-4.1-.3-6 0-1.7.4-3.2 1.3-4.6 1-1.6 2.9-3.5 5.1-2.9 2.5.6 2.3 4.1 4.1 4.9 1.9.8 1.6-.9 2.3-2.1 1.2-2.1 2.1-2.1 4.4-2.4 1.4-.2 3.6-1.5 4.9-.5 2.3 1.7-.7 4.4.1 6.5.6 1.5 2.1 1.7 2.8.3.7-1.4-1.1-3.4-.3-4.8 1.4-2.5 6.2-1.2 7.2 1 2.3 4.8-3.3 12-.2 16.3 3 4.1 3.9-2.8 3.8-4.8-.4-4.3-2.1-8.9 0-13.1 1.3-2.5 5.9-5.7 7.9-2.4 2 3.2-1.3 9.8-.8 13.4.5 4.4 3.5 3.3 2.7-.8-.4-1.9-2.4-10 .6-11.1 3.7-1.4 2.8 7.2 6.5.4 2.2-4.1 4.9-3.1 5.2 1.2.1 1.5-.6 3.1-.4 4.6.2 1.9 1.8 3.7 3.3 1.3 1-1.6-2.6-10.4 2.9-7.3 2.6 1.5 1.6 6.5 4.8 2.7 1.3-1.5 1.7-3.6 4-3.7 2.2-.1 4 2.3 4.8 4.1 1.3 2.9-1.5 8.4.9 10.3 4.2 3.3 3-5.5 2.7-6.9-.6-3.9 1-7.2 5.5-5 4.1 2.1 4.3 7.7 4.1 11.6 0 .8-.6 9.5 2.5 5.2 1.2-1.7-.1-7.7.1-9.6.3-2.9 1.2-5.5 4.3-6.2 4.5-1 7.7 1.5 7.4 5.8-.2 3.5-1.8 7.7-.5 11.1 1 2.7 3.6 2.8 5 .2 1.6-3.1 0-8.3-.4-11.6-.4-4.2-.2-7 1.8-10.8 0 0-.1.1-.1.2-.2.4-.3.7-.4.8v.1c-.1.2-.1.2 0 0v-.1l.4-.8c0-.1.1-.1.1-.2.2-.4.5-.8.8-1.2V0H0zM282.7 3.4z"/></svg>',

			'fan'              => '<svg class="premium-blog-shape-divider-svg" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 283.5 19.6" preserveAspectRatio="none"><path  style="opacity:0.33" d="M0 0L0 18.8 141.8 4.1 283.5 18.8 283.5 0z"/><path  style="opacity:0.33" d="M0 0L0 12.6 141.8 4 283.5 12.6 283.5 0z"/><path  style="opacity:0.33" d="M0 0L0 6.4 141.8 4 283.5 6.4 283.5 0z"/><path  d="M0 0L0 1.2 141.8 4 283.5 1.2 283.5 0z"/></svg>',

			'mountain'         => '<svg class="premium-blog-shape-divider-svg" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1000 100" preserveAspectRatio="none"><path opacity="0.33" d="M473,67.3c-203.9,88.3-263.1-34-320.3,0C66,119.1,0,59.7,0,59.7V0h1000v59.7 c0,0-62.1,26.1-94.9,29.3c-32.8,3.3-62.8-12.3-75.8-22.1C806,49.6,745.3,8.7,694.9,4.7S492.4,59,473,67.3z"></path><path opacity="0.66" d="M734,67.3c-45.5,0-77.2-23.2-129.1-39.1c-28.6-8.7-150.3-10.1-254,39.1 s-91.7-34.4-149.2,0C115.7,118.3,0,39.8,0,39.8V0h1000v36.5c0,0-28.2-18.5-92.1-18.5C810.2,18.1,775.7,67.3,734,67.3z"></path><path d="M766.1,28.9c-200-57.5-266,65.5-395.1,19.5C242,1.8,242,5.4,184.8,20.6C128,35.8,132.3,44.9,89.9,52.5C28.6,63.7,0,0,0,0 h1000c0,0-9.9,40.9-83.6,48.1S829.6,47,766.1,28.9z"></path></svg>',

			'pyramids'         => '<svg class="premium-blog-shape-divider-svg" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1000 100" preserveAspectRatio="none"><path  d="M761.9,44.1L643.1,27.2L333.8,98L0,3.8V0l1000,0v3.9"/></svg>',

			'split'            => '<svg class="premium-blog-shape-divider-svg" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1000 20" preserveAspectRatio="none"><path class="elementor-shape-fill" d="M0,0v3c0,0,393.8,0,483.4,0c9.2,0,16.6,7.4,16.6,16.6c0-9.1,7.4-16.6,16.6-16.6C606.2,3,1000,3,1000,3V0H0z"/></svg>',

			'triangle'         => '<svg class="premium-blog-shape-divider-svg" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1000 100" preserveAspectRatio="none"><path  d="M500,98.9L0,6.1V0h1000v6.1L500,98.9z"/></svg>',

			'tri_asymmetric'   => '<svg class="premium-blog-shape-divider-svg" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1000 100" preserveAspectRatio="none"><path  d="M738,99l262-93V0H0v5.6L738,99z"/></svg>',

			'tilt'             => '<svg class="premium-blog-shape-divider-svg" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1000 100" preserveAspectRatio="none"><path class="elementor-shape-fill" d="M0,6V0h1000v100L0,6z"/></svg>',

			'tilt-opacity'     => '<svg class="premium-blog-shape-divider-svg" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 2600 131.1" preserveAspectRatio="none"><path  d="M0 0L2600 0 2600 69.1 0 0z"/><path  style="opacity:0.5" d="M0 0L2600 0 2600 69.1 0 69.1z"/><path  style="opacity:0.25" d="M2600 0L0 0 0 130.1 2600 69.1z"/></svg>',

			'waves'            => '<svg class="premium-blog-shape-divider-svg"  xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1000 100" preserveAspectRatio="none"><path  d="M421.9,6.5c22.6-2.5,51.5,0.4,75.5,5.3c23.6,4.9,70.9,23.5,100.5,35.7c75.8,32.2,133.7,44.5,192.6,49.7
			c23.6,2.1,48.7,3.5,103.4-2.5c54.7-6,106.2-25.6,106.2-25.6V0H0v30.3c0,0,72,32.6,158.4,30.5c39.2-0.7,92.8-6.7,134-22.4
			c21.2-8.1,52.2-18.2,79.7-24.2C399.3,7.9,411.6,7.5,421.9,6.5z"/></svg>',

			'waves-brush'      => '<svg class="premium-blog-shape-divider-svg" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 283.5 27.8" preserveAspectRatio="none"><path  d="M283.5,9.7c0,0-7.3,4.3-14,4.6c-6.8,0.3-12.6,0-20.9-1.5c-11.3-2-33.1-10.1-44.7-5.7	s-12.1,4.6-18,7.4c-6.6,3.2-20,9.6-36.6,9.3C131.6,23.5,99.5,7.2,86.3,8c-1.4,0.1-6.6,0.8-10.5,2c-3.8,1.2-9.4,3.8-17,4.7	c-3.2,0.4-8.3,1.1-14.2,0.9c-1.5-0.1-6.3-0.4-12-1.6c-5.7-1.2-11-3.1-15.8-3.7C6.5,9.2,0,10.8,0,10.8V0h283.5V9.7z M260.8,11.3	c-0.7-1-2-0.4-4.3-0.4c-2.3,0-6.1-1.2-5.8-1.1c0.3,0.1,3.1,1.5,6,1.9C259.7,12.2,261.4,12.3,260.8,11.3z M242.4,8.6	c0,0-2.4-0.2-5.6-0.9c-3.2-0.8-10.3-2.8-15.1-3.5c-8.2-1.1-15.8,0-15.1,0.1c0.8,0.1,9.6-0.6,17.6,1.1c3.3,0.7,9.3,2.2,12.4,2.7	C239.9,8.7,242.4,8.6,242.4,8.6z M185.2,8.5c1.7-0.7-13.3,4.7-18.5,6.1c-2.1,0.6-6.2,1.6-10,2c-3.9,0.4-8.9,0.4-8.8,0.5	c0,0.2,5.8,0.8,11.2,0c5.4-0.8,5.2-1.1,7.6-1.6C170.5,14.7,183.5,9.2,185.2,8.5z M199.1,6.9c0.2,0-0.8-0.4-4.8,1.1	c-4,1.5-6.7,3.5-6.9,3.7c-0.2,0.1,3.5-1.8,6.6-3C197,7.5,199,6.9,199.1,6.9z M283,6c-0.1,0.1-1.9,1.1-4.8,2.5s-6.9,2.8-6.7,2.7	c0.2,0,3.5-0.6,7.4-2.5C282.8,6.8,283.1,5.9,283,6z M31.3,11.6c0.1-0.2-1.9-0.2-4.5-1.2s-5.4-1.6-7.8-2C15,7.6,7.3,8.5,7.7,8.6	C8,8.7,15.9,8.3,20.2,9.3c2.2,0.5,2.4,0.5,5.7,1.6S31.2,11.9,31.3,11.6z M73,9.2c0.4-0.1,3.5-1.6,8.4-2.6c4.9-1.1,8.9-0.5,8.9-0.8	c0-0.3-1-0.9-6.2-0.3S72.6,9.3,73,9.2z M71.6,6.7C71.8,6.8,75,5.4,77.3,5c2.3-0.3,1.9-0.5,1.9-0.6c0-0.1-1.1-0.2-2.7,0.2	C74.8,5.1,71.4,6.6,71.6,6.7z M93.6,4.4c0.1,0.2,3.5,0.8,5.6,1.8c2.1,1,1.8,0.6,1.9,0.5c0.1-0.1-0.8-0.8-2.4-1.3	C97.1,4.8,93.5,4.2,93.6,4.4z M65.4,11.1c-0.1,0.3,0.3,0.5,1.9-0.2s2.6-1.3,2.2-1.2s-0.9,0.4-2.5,0.8C65.3,10.9,65.5,10.8,65.4,11.1	z M34.5,12.4c-0.2,0,2.1,0.8,3.3,0.9c1.2,0.1,2,0.1,2-0.2c0-0.3-0.1-0.5-1.6-0.4C36.6,12.8,34.7,12.4,34.5,12.4z M152.2,21.1	c-0.1,0.1-2.4-0.3-7.5-0.3c-5,0-13.6-2.4-17.2-3.5c-3.6-1.1,10,3.9,16.5,4.1C150.5,21.6,152.3,21,152.2,21.1z"/><path  d="M269.6,18c-0.1-0.1-4.6,0.3-7.2,0c-7.3-0.7-17-3.2-16.6-2.9c0.4,0.3,13.7,3.1,17,3.3	C267.7,18.8,269.7,18,269.6,18z"/><path  d="M227.4,9.8c-0.2-0.1-4.5-1-9.5-1.2c-5-0.2-12.7,0.6-12.3,0.5c0.3-0.1,5.9-1.8,13.3-1.2	S227.6,9.9,227.4,9.8z"/><path  d="M204.5,13.4c-0.1-0.1,2-1,3.2-1.1c1.2-0.1,2,0,2,0.3c0,0.3-0.1,0.5-1.6,0.4	C206.4,12.9,204.6,13.5,204.5,13.4z"/><path  d="M201,10.6c0-0.1-4.4,1.2-6.3,2.2c-1.9,0.9-6.2,3.1-6.1,3.1c0.1,0.1,4.2-1.6,6.3-2.6	S201,10.7,201,10.6z"/><path  d="M154.5,26.7c-0.1-0.1-4.6,0.3-7.2,0c-7.3-0.7-17-3.2-16.6-2.9c0.4,0.3,13.7,3.1,17,3.3	C152.6,27.5,154.6,26.8,154.5,26.7z"/><path  d="M41.9,19.3c0,0,1.2-0.3,2.9-0.1c1.7,0.2,5.8,0.9,8.2,0.7c4.2-0.4,7.4-2.7,7-2.6	c-0.4,0-4.3,2.2-8.6,1.9c-1.8-0.1-5.1-0.5-6.7-0.4S41.9,19.3,41.9,19.3z"/><path  d="M75.5,12.6c0.2,0.1,2-0.8,4.3-1.1c2.3-0.2,2.1-0.3,2.1-0.5c0-0.1-1.8-0.4-3.4,0	C76.9,11.5,75.3,12.5,75.5,12.6z"/><path  d="M15.6,13.2c0-0.1,4.3,0,6.7,0.5c2.4,0.5,5,1.9,5,2c0,0.1-2.7-0.8-5.1-1.4	C19.9,13.7,15.7,13.3,15.6,13.2z"/></svg>',

			'waves-pattern'    => '<svg class="premium-blog-shape-divider-svg" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1047.1 3.7" preserveAspectRatio="xMidYMin slice"><path  d="M1047.1,0C557,0,8.9,0,0,0v1.6c0,0,0.6-1.5,2.7-0.3C3.9,2,6.1,4.1,8.3,3.5c0.9-0.2,1.5-1.9,1.5-1.9	s0.6-1.5,2.7-0.3C13.8,2,16,4.1,18.2,3.5c0.9-0.2,1.5-1.9,1.5-1.9s0.6-1.5,2.7-0.3C23.6,2,25.9,4.1,28,3.5c0.9-0.2,1.5-1.9,1.5-1.9	c0,0,0.6-1.5,2.7-0.3c1.2,0.7,3.5,2.8,5.6,2.2c0.9-0.2,1.5-1.9,1.5-1.9c0,0,0.6-1.5,2.7-0.3c1.2,0.7,3.5,2.8,5.6,2.2	c0.9-0.2,1.5-1.9,1.5-1.9s0.6-1.5,2.7-0.3c1.2,0.7,3.5,2.8,5.6,2.2c0.9-0.2,1.5-1.9,1.5-1.9s0.6-1.5,2.7-0.3C63,2,65.3,4.1,67.4,3.5	C68.3,3.3,69,1.6,69,1.6s0.6-1.5,2.7-0.3c1.2,0.7,3.5,2.8,5.6,2.2c0.9-0.2,1.5-1.9,1.5-1.9c0,0,0.6-1.5,2.7-0.3	C82.7,2,85,4.1,87.1,3.5c0.9-0.2,1.5-1.9,1.5-1.9s0.6-1.5,2.7-0.3C92.6,2,94.8,4.1,97,3.5c0.9-0.2,1.5-1.9,1.5-1.9s0.6-1.5,2.7-0.3	c1.2,0.7,3.5,2.8,5.6,2.2c0.9-0.2,1.5-1.9,1.5-1.9s0.6-1.5,2.7-0.3c1.2,0.7,3.5,2.8,5.6,2.2c0.9-0.2,1.5-1.9,1.5-1.9	c0,0,0.6-1.5,2.7-0.3c1.2,0.7,3.5,2.8,5.6,2.2c0.9-0.2,1.5-1.9,1.5-1.9c0,0,0.6-1.5,2.7-0.3c1.2,0.7,3.5,2.8,5.6,2.2	c0.9-0.2,1.5-1.9,1.5-1.9s0.6-1.5,2.7-0.3c1.2,0.7,3.5,2.8,5.6,2.2c0.9-0.2,1.5-1.9,1.5-1.9s0.6-1.5,2.7-0.3	c1.2,0.7,3.5,2.8,5.6,2.2c0.9-0.2,1.5-1.9,1.5-1.9s0.6-1.5,2.7-0.3c1.2,0.7,3.5,2.8,5.6,2.2c0.9-0.2,1.5-1.9,1.5-1.9	c0,0,0.6-1.5,2.7-0.3c1.2,0.7,3.5,2.8,5.6,2.2c0.9-0.2,1.5-1.9,1.5-1.9s0.6-1.5,2.7-0.3c1.2,0.7,3.5,2.8,5.6,2.2	c0.9-0.2,1.5-1.9,1.5-1.9s0.6-1.5,2.7-0.3c1.2,0.7,3.5,2.8,5.6,2.2c0.9-0.2,1.5-1.9,1.5-1.9s0.6-1.5,2.7-0.3	c1.2,0.7,3.5,2.8,5.6,2.2c0.9-0.2,1.5-1.9,1.5-1.9c0,0,0.6-1.5,2.7-0.3c1.2,0.7,3.5,2.8,5.6,2.2c0.9-0.2,1.5-1.9,1.5-1.9	c0,0,0.6-1.5,2.7-0.3c1.2,0.7,3.5,2.8,5.6,2.2c0.9-0.2,1.5-1.9,1.5-1.9s0.6-1.5,2.7-0.3c1.2,0.7,3.5,2.8,5.6,2.2	c0.9-0.2,1.5-1.9,1.5-1.9s0.6-1.5,2.7-0.3c1.2,0.7,3.5,2.8,5.6,2.2c0.9-0.2,1.5-1.9,1.5-1.9s0.6-1.5,2.7-0.3	c1.2,0.7,3.5,2.8,5.6,2.2c0.9-0.2,1.5-1.9,1.5-1.9c0,0,0.6-1.5,2.7-0.3c1.2,0.7,3.5,2.8,5.6,2.2c0.9-0.2,1.5-1.9,1.5-1.9	s0.6-1.5,2.7-0.3c1.2,0.7,3.5,2.8,5.6,2.2c0.9-0.2,1.5-1.9,1.5-1.9s0.6-1.5,2.7-0.3c1.2,0.7,3.5,2.8,5.6,2.2	c0.9-0.2,1.5-1.9,1.5-1.9s0.6-1.5,2.7-0.3c1.2,0.7,3.5,2.8,5.6,2.2c0.9-0.2,1.5-1.9,1.5-1.9c0,0,0.6-1.5,2.7-0.3	c1.2,0.7,3.5,2.8,5.6,2.2c0.9-0.2,1.5-1.9,1.5-1.9c0,0,0.6-1.5,2.7-0.3c1.2,0.7,3.5,2.8,5.6,2.2c0.9-0.2,1.5-1.9,1.5-1.9	s0.6-1.5,2.7-0.3c1.2,0.7,3.5,2.8,5.6,2.2c0.9-0.2,1.5-1.9,1.5-1.9s0.6-1.5,2.7-0.3c1.2,0.7,3.5,2.8,5.6,2.2	c0.9-0.2,1.5-1.9,1.5-1.9s0.6-1.5,2.7-0.3c1.2,0.7,3.5,2.8,5.6,2.2c0.9-0.2,1.5-1.9,1.5-1.9c0,0,0.6-1.5,2.7-0.3	c1.2,0.7,3.5,2.8,5.6,2.2c0.9-0.2,1.5-1.9,1.5-1.9s0.6-1.5,2.7-0.3c1.2,0.7,3.5,2.8,5.6,2.2c0.9-0.2,1.5-1.9,1.5-1.9	s0.6-1.5,2.7-0.3c1.2,0.7,3.5,2.8,5.6,2.2c0.9-0.2,1.5-1.9,1.5-1.9s0.6-1.5,2.7-0.3c1.2,0.7,3.5,2.8,5.6,2.2	c0.9-0.2,1.5-1.9,1.5-1.9c0,0,0.6-1.5,2.7-0.3c1.2,0.7,3.5,2.8,5.6,2.2c0.9-0.2,1.5-1.9,1.5-1.9c0,0,0.6-1.5,2.7-0.3	c1.2,0.7,3.5,2.8,5.6,2.2c0.9-0.2,1.5-1.9,1.5-1.9s0.6-1.5,2.7-0.3c1.2,0.7,3.5,2.8,5.6,2.2c0.9-0.2,1.5-1.9,1.5-1.9	s0.6-1.5,2.7-0.3c1.2,0.7,3.5,2.8,5.6,2.2c0.9-0.2,1.5-1.9,1.5-1.9s0.6-1.5,2.7-0.3c1.2,0.7,3.5,2.8,5.6,2.2	c0.9-0.2,1.5-1.9,1.5-1.9c0,0,0.6-1.5,2.7-0.3c1.2,0.7,3.5,2.8,5.6,2.2c0.9-0.2,1.5-1.9,1.5-1.9s0.6-1.5,2.7-0.3	c1.2,0.7,3.5,2.8,5.6,2.2c0.9-0.2,1.5-1.9,1.5-1.9s0.6-1.5,2.7-0.3c1.2,0.7,3.5,2.8,5.6,2.2c0.9-0.2,1.5-1.9,1.5-1.9	s0.6-1.5,2.7-0.3c1.2,0.7,3.5,2.8,5.6,2.2c0.9-0.2,1.5-1.9,1.5-1.9c0,0,0.6-1.5,2.7-0.3c1.2,0.7,3.5,2.8,5.6,2.2	c0.9-0.2,1.5-1.9,1.5-1.9c0,0,0.6-1.5,2.7-0.3c1.2,0.7,3.5,2.8,5.6,2.2c0.9-0.2,1.5-1.9,1.5-1.9s0.6-1.5,2.7-0.3	c1.2,0.7,3.5,2.8,5.6,2.2c0.9-0.2,1.5-1.9,1.5-1.9s0.6-1.5,2.7-0.3c1.2,0.7,3.5,2.8,5.6,2.2c0.9-0.2,1.5-1.9,1.5-1.9	s0.6-1.5,2.7-0.3c1.2,0.7,3.5,2.8,5.6,2.2c0.9-0.2,1.5-1.9,1.5-1.9c0,0,0.6-1.5,2.7-0.3c1.2,0.7,3.5,2.8,5.6,2.2	c0.9-0.2,1.5-1.9,1.5-1.9s0.6-1.5,2.7-0.3c1.2,0.7,3.5,2.8,5.6,2.2c0.9-0.2,1.5-1.9,1.5-1.9s0.6-1.5,2.7-0.3	c1.2,0.7,3.5,2.8,5.6,2.2c0.9-0.2,1.5-1.9,1.5-1.9s0.6-1.5,2.7-0.3c1.2,0.7,3.5,2.8,5.6,2.2c0.9-0.2,1.5-1.9,1.5-1.9	c0,0,0.6-1.5,2.7-0.3c1.2,0.7,3.5,2.8,5.6,2.2c0.9-0.2,1.5-1.9,1.5-1.9c0,0,0.6-1.5,2.7-0.3c1.2,0.7,3.5,2.8,5.6,2.2	c0.9-0.2,1.5-1.9,1.5-1.9s0.6-1.5,2.7-0.3c1.2,0.7,3.5,2.8,5.6,2.2c0.9-0.2,1.5-1.9,1.5-1.9s0.6-1.5,2.7-0.3	c1.2,0.7,3.5,2.8,5.6,2.2c0.9-0.2,1.5-1.9,1.5-1.9s0.6-1.5,2.7-0.3c1.2,0.7,3.5,2.8,5.6,2.2c0.9-0.2,1.5-1.9,1.5-1.9	c0,0,0.6-1.5,2.7-0.3c1.2,0.7,3.5,2.8,5.6,2.2c0.9-0.2,1.5-1.9,1.5-1.9s0.6-1.5,2.7-0.3c1.2,0.7,3.5,2.8,5.6,2.2	c0.9-0.2,1.5-1.9,1.5-1.9s0.6-1.5,2.7-0.3c1.2,0.7,3.5,2.8,5.6,2.2c0.9-0.2,1.5-1.9,1.5-1.9s0.6-1.5,2.7-0.3	c1.2,0.7,3.5,2.8,5.6,2.2c0.9-0.2,1.5-1.9,1.5-1.9c0,0,0.6-1.5,2.7-0.3c1.2,0.7,3.5,2.8,5.6,2.2c0.9-0.2,1.5-1.9,1.5-1.9	c0,0,0.6-1.5,2.7-0.3c1.2,0.7,3.5,2.8,5.6,2.2c0.9-0.2,1.5-1.9,1.5-1.9s0.6-1.5,2.7-0.3c1.2,0.7,3.5,2.8,5.6,2.2	c0.9-0.2,1.5-1.9,1.5-1.9s0.6-1.5,2.7-0.3c1.2,0.7,3.5,2.8,5.6,2.2c0.9-0.2,1.5-1.9,1.5-1.9s0.6-1.5,2.7-0.3	c1.2,0.7,3.5,2.8,5.6,2.2c0.9-0.2,1.5-1.9,1.5-1.9c0,0,0.6-1.5,2.7-0.3c1.2,0.7,3.5,2.8,5.6,2.2c0.9-0.2,1.5-1.9,1.5-1.9	s0.6-1.5,2.7-0.3c1.2,0.7,3.5,2.8,5.6,2.2c0.9-0.2,1.5-1.9,1.5-1.9s0.6-1.5,2.7-0.3c1.2,0.7,3.5,2.8,5.6,2.2	c0.9-0.2,1.5-1.9,1.5-1.9s0.6-1.5,2.7-0.3c1.2,0.7,3.5,2.8,5.6,2.2c0.9-0.2,1.5-1.9,1.5-1.9c0,0,0.6-1.5,2.7-0.3	c1.2,0.7,3.5,2.8,5.6,2.2c0.9-0.2,1.5-1.9,1.5-1.9c0,0,0.6-1.5,2.7-0.3c1.2,0.7,3.5,2.8,5.6,2.2c0.9-0.2,1.5-1.9,1.5-1.9	s0.6-1.5,2.7-0.3c1.2,0.7,3.5,2.8,5.6,2.2c0.9-0.2,1.5-1.9,1.5-1.9s0.6-1.5,2.7-0.3c1.2,0.7,3.5,2.8,5.6,2.2	c0.9-0.2,1.5-1.9,1.5-1.9s0.6-1.5,2.7-0.3c1.2,0.7,3.5,2.8,5.6,2.2c0.9-0.2,1.5-1.9,1.5-1.9c0,0,0.6-1.5,2.7-0.3	c1.2,0.7,3.5,2.8,5.6,2.2c0.9-0.2,1.5-1.9,1.5-1.9s0.6-1.5,2.7-0.3c1.2,0.7,3.5,2.8,5.6,2.2c0.9-0.2,1.5-1.9,1.5-1.9	s0.6-1.5,2.7-0.3c1.2,0.7,3.5,2.8,5.6,2.2c0.9-0.2,1.5-1.9,1.5-1.9s0.6-1.5,2.7-0.3c1.2,0.7,3.5,2.8,5.6,2.2	c0.9-0.2,1.5-1.9,1.5-1.9c0,0,0.6-1.5,2.7-0.3c1.2,0.7,3.5,2.8,5.6,2.2c0.9-0.2,1.5-1.9,1.5-1.9c0,0,0.6-1.5,2.7-0.3	c1.2,0.7,3.5,2.8,5.6,2.2c0.9-0.2,1.5-1.9,1.5-1.9s0.6-1.5,2.7-0.3c1.2,0.7,3.5,2.8,5.6,2.2c0.9-0.2,1.5-1.9,1.5-1.9	s0.6-1.5,2.7-0.3c1.2,0.7,3.5,2.8,5.6,2.2c0.9-0.2,1.5-1.9,1.5-1.9s0.6-1.5,2.7-0.3c1.2,0.7,3.5,2.8,5.6,2.2	c0.9-0.2,1.5-1.9,1.5-1.9c0,0,0.6-1.5,2.7-0.3c1.2,0.7,3.5,2.8,5.6,2.2c0.9-0.2,1.5-1.9,1.5-1.9s0.6-1.5,2.7-0.3	c1.2,0.7,3.5,2.8,5.6,2.2c0.9-0.2,1.5-1.9,1.5-1.9s0.6-1.5,2.7-0.3c1.2,0.7,3.5,2.8,5.6,2.2c0.9-0.2,1.5-1.9,1.5-1.9	s0.6-1.5,2.7-0.3c1.2,0.7,3.5,2.8,5.6,2.2c0.9-0.2,1.5-1.9,1.5-1.9c0,0,0.6-1.5,2.7-0.3c1.2,0.7,3.5,2.8,5.6,2.2	c0.9-0.2,1.5-1.9,1.5-1.9c0,0,0.6-1.5,2.7-0.3c1.2,0.7,3.5,2.8,5.6,2.2c0.9-0.2,1.5-1.9,1.5-1.9s0.6-1.5,2.7-0.3	c1.2,0.7,3.5,2.8,5.6,2.2c0.9-0.2,1.5-1.9,1.5-1.9s0.6-1.5,2.7-0.3c1.2,0.7,3.5,2.8,5.6,2.2c0.9-0.2,1.5-1.9,1.5-1.9	s0.6-1.5,2.7-0.3c1.2,0.7,3.5,2.8,5.6,2.2c0.9-0.2,1.5-1.9,1.5-1.9c0,0,0.6-1.5,2.7-0.3c1.2,0.7,3.5,2.8,5.6,2.2	c0.9-0.2,1.5-1.9,1.5-1.9s0.6-1.5,2.7-0.3c1.2,0.7,3.5,2.8,5.6,2.2c0.9-0.2,1.5-1.9,1.5-1.9s0.6-1.5,2.7-0.3	c1.2,0.7,3.5,2.8,5.6,2.2c0.9-0.2,1.5-1.9,1.5-1.9s0.6-1.5,2.7-0.3c1.2,0.7,3.5,2.8,5.6,2.2c0.9-0.2,1.5-1.9,1.5-1.9	c0,0,0.6-1.5,2.7-0.3c1.2,0.7,3.5,2.8,5.6,2.2c0.9-0.2,1.5-1.9,1.5-1.9c0,0,0.6-1.5,2.7-0.3c1.2,0.7,3.5,2.8,5.6,2.2	c0.9-0.2,1.5-1.9,1.5-1.9s0.6-1.5,2.7-0.3c1.2,0.7,3.5,2.8,5.6,2.2c0.9-0.2,1.5-1.9,1.5-1.9s0.6-1.5,2.7-0.3	c1.2,0.7,3.5,2.8,5.6,2.2c0.9-0.2,1.5-1.9,1.5-1.9s0.6-1.5,2.6-0.4V0z M2.5,1.2C2.5,1.2,2.5,1.2,2.5,1.2C2.5,1.2,2.5,1.2,2.5,1.2z M2.7,1.4c0.1,0,0.1,0.1,0.1,0.1C2.8,1.4,2.8,1.4,2.7,1.4z"/></svg>',

			'zigzag'           => '<svg class="premium-blog-shape-divider-svg" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1800 5.8" preserveAspectRatio="none"><path  d="M5.4.4l5.4 5.3L16.5.4l5.4 5.3L27.5.4 33 5.7 38.6.4l5.5 5.4h.1L49.9.4l5.4 5.3L60.9.4l5.5 5.3L72 .4l5.5 5.3L83.1.4l5.4 5.3L94.1.4l5.5 5.4h.2l5.6-5.4 5.5 5.3 5.6-5.3 5.4 5.3 5.6-5.3 5.5 5.3 5.6-5.3 5.5 5.4h.2l5.6-5.4 5.4 5.3L161 .4l5.4 5.3L172 .4l5.5 5.3 5.6-5.3 5.4 5.3 5.7-5.3 5.4 5.4h.2l5.6-5.4 5.5 5.3 5.6-5.3 5.5 5.3 5.6-5.3 5.4 5.3 5.6-5.3 5.5 5.4h.2l5.6-5.4 5.5 5.3L261 .4l5.4 5.3L272 .4l5.5 5.3 5.6-5.3 5.5 5.3 5.6-5.3 5.5 5.4h.1l5.7-5.4 5.4 5.3 5.6-5.3 5.5 5.3 5.6-5.3 5.4 5.3 5.7-5.3 5.4 5.4h.2l5.6-5.4 5.5 5.3L361 .4l5.5 5.3 5.6-5.3 5.4 5.3 5.6-5.3 5.5 5.3 5.6-5.3 5.5 5.4h.1l5.7-5.4 5.4 5.3 5.6-5.3 5.5 5.3 5.6-5.3 5.5 5.3 5.6-5.3 5.5 5.4h.1l5.6-5.4 5.5 5.3L461 .4l5.5 5.3 5.6-5.3 5.4 5.3 5.7-5.3 5.4 5.3 5.6-5.3 5.5 5.4h.2l5.6-5.4 5.5 5.3 5.6-5.3 5.4 5.3 5.6-5.3 5.5 5.3 5.6-5.3 5.5 5.4h.1L550 .4l5.4 5.3L561 .4l5.5 5.3 5.6-5.3 5.5 5.3 5.6-5.3 5.4 5.3 5.6-5.3 5.5 5.4h.2l5.6-5.4 5.5 5.3 5.6-5.3 5.4 5.3 5.7-5.3 5.4 5.3 5.6-5.3 5.5 5.4h.2L650 .4l5.5 5.3 5.6-5.3 5.4 5.3 5.6-5.3 5.5 5.3 5.6-5.3 5.5 5.3 5.6-5.3 5.4 5.4h.2l5.6-5.4 5.5 5.3 5.6-5.3 5.5 5.3 5.6-5.3 5.4 5.3 5.6-5.3 5.5 5.4h.2L750 .4l5.5 5.3 5.6-5.3 5.4 5.3 5.7-5.3 5.4 5.3 5.6-5.3 5.5 5.3 5.6-5.3 5.5 5.4h.1l5.7-5.4 5.4 5.3 5.6-5.3 5.5 5.3 5.6-5.3 5.5 5.3 5.6-5.3 5.4 5.4h.2L850 .4l5.5 5.3 5.6-5.3 5.5 5.3 5.6-5.3 5.4 5.3 5.6-5.3 5.5 5.3 5.6-5.3 5.5 5.4h.2l5.6-5.4 5.4 5.3 5.7-5.3 5.4 5.3 5.6-5.3 5.5 5.3 5.6-5.3 5.5 5.4h.1l5.7-5.4 5.4 5.3 5.6-5.3 5.5 5.3 5.6-5.3 5.5 5.3 5.6-5.3 5.4 5.3 5.6-5.3 5.5 5.4h.2l5.6-5.4 5.5 5.3 5.6-5.3 5.4 5.3 5.6-5.3 5.5 5.3 5.6-5.3 5.5 5.4h.2l5.6-5.4 5.4 5.3 5.7-5.3 5.4 5.3 5.6-5.3 5.5 5.3 5.6-5.3 5.5 5.3 5.6-5.3 5.4 5.4h.2l5.6-5.4 5.5 5.3 5.6-5.3 5.5 5.3 5.6-5.3 5.4 5.3 5.6-5.3 5.5 5.4h.2l5.6-5.4 5.5 5.3 5.6-5.3 5.4 5.3 5.6-5.3 5.5 5.3 5.6-5.3 5.5 5.3 5.6-5.3 5.5 5.4h.1l5.7-5.4 5.4 5.3 5.6-5.3 5.5 5.3 5.6-5.3 5.5 5.3 5.6-5.3 5.4 5.4h.2l5.6-5.4 5.5 5.3 5.6-5.3 5.5 5.3 5.6-5.3 5.4 5.3 5.6-5.3 5.5 5.3 5.6-5.3 5.5 5.4h.2l5.6-5.4 5.4 5.3 5.6-5.3 5.5 5.3 5.6-5.3 5.5 5.3 5.6-5.3 5.5 5.4h.1l5.7-5.4 5.4 5.3 5.6-5.3 5.5 5.3 5.6-5.3 5.5 5.3 5.6-5.3 5.4 5.3 5.6-5.3 5.5 5.4h.2l5.6-5.4 5.5 5.3 5.6-5.3 5.4 5.3 5.6-5.3 5.5 5.3 5.6-5.3 5.5 5.4h.2l5.6-5.4 5.4 5.3 5.6-5.3 5.5 5.3 5.6-5.3 5.5 5.3 5.6-5.3 5.4 5.3 5.7-5.3 5.4 5.4h.2l5.6-5.4 5.5 5.3 5.6-5.3 5.5 5.3 5.6-5.3 5.4 5.3 5.6-5.3 5.5 5.4h.2l5.6-5.4 5.5 5.3 5.6-5.3 5.4 5.3 5.6-5.3 5.5 5.3 5.6-5.3 5.5 5.3 5.6-5.3 5.5 5.4h.1l5.6-5.4 5.5 5.3 5.6-5.3 5.5 5.3 5.6-5.3 5.4 5.3 5.7-5.3 5.4 5.4h.2l5.6-5.4 5.5 5.3 5.6-5.3 5.5 5.3 5.6-5.3 5.4 5.3 5.6-5.3 5.5 5.3 5.6-5.3 5.5 5.4h.1l5.7-5.4 5.4 5.3 5.6-5.3 5.5 5.3 5.6-5.3 5.5 5.3 5.6-5.3 5.5 5.4h.1l5.6-5.4 5.5 5.3 5.6-5.3 5.5 5.3 5.6-5.3 5.4 5.3 5.7-5.3 5.4 5.3 5.6-5.3 5.5 5.4V0H-.2v5.8z"/></svg>',

		);

		echo $mask_array[ $mask ]; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	}

	public function render_custom_featured_post( $page_num, $req_type ) {

		$settings = self::$settings;

		$source = $settings['post_type_filter'];

		$post_id = 'post' === $source ? $settings['featured_post_default'] : $settings['featured_post'];

		if ( empty( $post_id ) ) {
			return false;
		}

		$general    = in_array( $settings['active_cat'], array( '*', '' ) );
		$first_page = in_array( $page_num, array( 1, '' ) );

		if ( $general && $first_page ) {
			return true;
		}
	}

	/**
	 * Renders the custom grid.
	 *
	 * @access public
	 * @since 4.9.48
	 *
	 * @param string $grid_template_id  grid template id.
	 */
	public function render_custom_grid_posts( $grid_template_id ) {

		$settings = self::$settings;

		/**
		 * added by the grid item widget used in the premium grid template.
		 *
		 * @see premium-addons-pro/includes/grid-builder/widgets/premium-grid.php.
		*/
		$pattern = '/\[papro_grid_item (.+?)\]/';

		$grid_template_content = '<div class="premium-smart-listing__custom-grid-wrapper">' . $this->get_template_content( $grid_template_id, true ) . '</div>';

		$grid_items_templates = $this->get_grid_items_templates( $pattern, $grid_template_content );

		$query = $this->get_query_posts();

		$posts = $query->posts;

		// Holds the items ( loop ) templates ids.
		$items_templates_container = array();

		$template_output = '';

		$total = self::$page_limit;

		if ( count( $posts ) ) {

			global $post;

			foreach ( $posts as $index => $post ) {

				/**
				 * Add a new grid template to the output if:
				 * 1- first render.
				 * 2- we looped through all the templates ids choosen by the user and the queried posts are > the loop templates ids.
				 */
				if ( empty( $items_templates_container ) ) {

					$template_output .= $grid_template_content; // add a new grid template.

					$items_templates_container = $grid_items_templates; // refill the templates container to start over.
				}

				// remove one id at a time and use it.
				$item_template_id = array_shift( $items_templates_container );

				setup_postdata( $post );

				if ( empty( $item_template_id ) ) {

					$notice = __( 'Please make sure to choose a loop template for this item.', 'premium-addons-for-elementor' );

					$item_template_content = '<div class="premium-error-notice">' . $notice . '</div>';

				} else {
					// render the post skin.
					$item_template_content = $this->get_template_content( $item_template_id, true );

					$item_template_content = ! empty( $item_template_content ) ? '<div class="premium-smart-listing__post-wrapper premium-smart-listing__grid-item" data-total="' . $total . '">' . $item_template_content . '</div>' : '';
				}

				// we limit what we replace to only one match to render each template correctly.
				$template_output = preg_replace( $pattern, $item_template_content, $template_output, 1 );

				wp_reset_postdata();
			}

			/**
			 * Replace the extra items by an invisible div to be removed later via JS.
			 * Case: We have a grid template with fewer items that the queried post number, especially if the user added style to the items' containing columns.
			 * Example: No.of posts per page is 4, and the grid templates has 3 columns each contains one item.
			 */
			$template_output = preg_replace( $pattern, '<div class="premium-extra-item"></div>', $template_output );

			echo '<div class="premium-smart-listing__posts-wrapper">' . $template_output . '</div>';
		} else {
			// display a message here.
		}

	}

	/**
	 * Gets Grid Items Custom Skins/Templates.
	 *
	 * @access public
	 * @since 3.2.9
	 *
	 * @param string  $pattern  premium grid widget's shortcode pattern.
	 * @param content $string   custom grid template content.
	 *
	 * @return array  the items custom skins/templates IDs.
	 */
	public function get_grid_items_templates( $pattern, $content ) {

		$matches = array();

		preg_match_all( $pattern, $content, $matches );

		$matches = array_pop( $matches ); // fetch last array element to get the ids.

		$templates = array();

		foreach ( $matches as $match ) {

			list($key, $val) = explode( '=', $match );

			$templates[] = trim( $val, '"' );
		}

		return $templates;
	}

	/**
	 * Render Posts
	 *
	 * @since 3.20.9
	 * @access public
	 */
	public function render_smart_posts( $page_num = '', $req_type = '' ) {

		$settings = self::$settings;

		$is_custom_grid = 'custom' === $settings['pa_spl_skin'] ? true : false;

		if ( $is_custom_grid ) {

			$grid_template_id = empty( $settings['pa_grid_template_id'] ) ? $settings['pa_grid_live_temp_id'] : $settings['pa_grid_template_id'];

			if ( ! empty( $grid_template_id ) ) {
				$this->render_custom_grid_posts( $grid_template_id );
			} else {
				$notice = __( 'Please choose your custom premium grid template.', 'premium-addons-for-elementor' );
				$this->get_empty_query_message( $notice );
			}
		} else {

			$display_featured_posts = 'yes' === $settings['display_featured_posts'] ? true : false;

			$post_id = '';

			if ( $display_featured_posts && 'infinite' !== $req_type ) {

				$source = $settings['post_type_filter'];

				$post_id = 'post' === $source ? $settings['featured_post_default'] : $settings['featured_post'];

				$render_custom_featured_post = $this->render_custom_featured_post( $page_num, $req_type );

				if ( $render_custom_featured_post ) {

					$featured_post = get_post( $post_id, OBJECT );

					global $post;

					$post = $featured_post;

					setup_postdata( $post );

					$this->render_featured_posts( $featured_post, 'custom' );

					wp_reset_postdata();
				}
			}

			$query = $this->get_query_posts();

			$posts = $query->posts;

			$flag = true;

			if ( count( $posts ) ) {

				global $post;

				foreach ( $posts as $index => $post ) {

					setup_postdata( $post ); // setup global post data.

					if ( empty( $post_id ) && ( $display_featured_posts && 0 == $index ) ) {
						if ( 'infinite' !== $req_type ) {
							$this->render_featured_posts( $post ); // render the first one by default for now.
						}
					} else {

						if ( $flag ) {
							?>
							<div class="premium-smart-listing__posts-wrapper">
							<?php
							$flag = false;
						}

						$this->get_magazine_post_layout();
					}
				}

				?>

				<?php if ( ! $flag ) : ?>
					</div>
				<?php endif; ?>

				<?php
			}

			wp_reset_postdata(); // After looping through a separate query, this function restores the $post global to the current post in the main query.
		}
	}

	public function render_custom_loop_temp() {

		$settings = self::$settings;

		$loop_item_id = get_the_ID();

		/** @var LoopDocument $document */
		$document = PluginPro::elementor()->documents->get( $settings['pa_loop_template_id'] );

		if ( ! $document ) {
			return;
		}

		$this->print_dynamic_css( $loop_item_id, $settings['pa_loop_template_id'] );
		$document->print_content();

	}

	protected function print_dynamic_css( $post_id, $post_id_for_data ) {

		$css_file = Loop_Dynamic_CSS::create( $post_id, $post_id_for_data );
		$post_css = $css_file->get_content();

		if ( empty( $post_css ) ) {
			return;
		}

		$css = '';
		$css = str_replace( '.elementor-' . $post_id, '.e-loop-item-' . $post_id, $post_css );
		$css = sprintf( '<style id="%s">%s</style>', 'loop-dynamic-' . $post_id_for_data, $css );

		echo $css; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	}


	/**
	 * Render Featured Posts.
	 */
	public function render_featured_posts( $post, $mode = '' ) {

		$settings = self::$settings;

		$post_id = get_the_ID();

		$widget_id = $settings['widget_id'];

		$key = sprintf( 'post_%s_%s_%s', $widget_id, $post_id, $mode );

		$wrap_key = sprintf( '%s_wrap', $key );

		$post_tag = 'yes' === $settings['article_tag_switcher'] ? 'article' : 'div';

		$target = 'yes' === $settings['new_tab'] ? '_blank' : '_self';

		$meta_key = sprintf( '%s_meta', $key );

		$meta_order_class = 'yes' === $settings['pa_featured_meta_above_title'] ? 'premium-order-0' : '';

		$content_options = array(
			'excerpt'              => $settings['pa_featured_excerpt'],
			'length'               => $settings['pa_featured_excerpt_length'],
			'target'               => $target,
			'source'               => $settings['pa_featured_content_source'],
			'excerpt_type'         => $settings['pa_featured_excerpt_type'],
			'excerpt_text'         => $settings['pa_featured_excerpt_text'],
			'class'                => 'premium-smart-listing__post-content',
			'content_classes'      => Helper_Functions::get_element_classes( $settings['pa_featured_hide_content'], array( 'premium-smart-listing__post-content-inner', 'premium-addons-element' ) ),
			'excerpt_class_prefix' => 'premium-smart-listing__',
		);

		$this->add_render_attribute(
			$meta_key,
			'class',
			array(
				'premium-smart-listing__post-meta-container',
				$meta_order_class,
			)
		);

		$thumbnail_src = wp_get_attachment_image_src( get_post_thumbnail_id(), $settings['featured_image_size'] );

		$this->add_render_attribute(
			$wrap_key,
			'class',
			array(
				'premium-smart-listing__featured-post-wrapper',
				'premium-smart-listing__grid-item',
			)
		);

		?>
		<div class="premium-smart-listing__featured-posts-wrapper">
			<<?php echo wp_kses_post( $post_tag . ' ' . $this->get_render_attribute_string( $wrap_key ) ); ?>>
				<div class="premium-smart-listing__post-thumbnail-wrapper">
					<?php
					if ( 'yes' === $settings['pa_featured_categories_meta'] ) {
						Posts_Helper::get_post_categories( $settings, $post_id );
					}
					?>
					<div class="premium-smart-listing__thumbnail-container" style="background-image: url('<?php echo $thumbnail_src[0]; ?>');">
					<?php // $this->get_post_thumbnail( $target, 'magazine' ); ?>
					</div>
					<div class="premium-smart-listing__thumbnail-overlay">
						<a class="elementor-icon" href="<?php the_permalink(); ?>" target="<?php echo esc_attr( $target ); ?>" aria-hidden="true"></a>
					</div>
				</div>
				<div class="premium-smart-listing__post-content-wrapper">
					<div class="premium-smart-listing__post-title-wrapper"> <?php $this->render_post_title( $target, $key, 'premium-smart-listing__post-title' ); ?> </div>
					<div <?php echo wp_kses_post( $this->get_render_attribute_string( $meta_key ) ); ?>> <?php Posts_Helper::render_smart_post_meta( 'featured', $settings, $post_id ); ?> </div>
					<?php $this->get_post_content( $content_options ); ?>
				</div>
			</<?php echo wp_kses_post( $post_tag ); ?>>
		</div>

		<?php
	}

	public function get_magazine_post_layout() {

		$settings = self::$settings;

		$post_tag = 'yes' === $settings['article_tag_switcher'] ? 'article' : 'div';

		$post_id = get_the_ID();

		$widget_id = $settings['widget_id'];

		$total = self::$page_limit;

		$key = sprintf( 'post_%s_%s', $widget_id, $post_id );

		$wrap_key = sprintf( '%s_wrap', $key );

		$target = 'yes' === $settings['new_tab'] ? '_blank' : '_self';

		$show_thumbnail = 'yes' === $settings['post_img'] ? 'true' : false;

		$meta_key = sprintf( '%s_meta', $key );

		$meta_order_class = 'yes' === $settings['meta_above_title'] ? 'premium-order-0' : '';

		$content_options = array(
			'excerpt'              => $settings['post_excerpt'],
			'length'               => $settings['post_excerpt_length'],
			'target'               => $target,
			'source'               => $settings['content_source'],
			'excerpt_type'         => $settings['post_excerpt_type'],
			'excerpt_text'         => $settings['post_excerpt_text'],
			'class'                => 'premium-smart-listing__post-content',
			'excerpt_class_prefix' => 'premium-smart-listing__',
			'content_classes'      => Helper_Functions::get_element_classes( $settings['hide_content'], array( 'premium-smart-listing__post-content-inner', 'premium-addons-element' ) ),
		);

		$this->add_render_attribute(
			$meta_key,
			'class',
			array(
				'premium-smart-listing__post-meta-container',
				$meta_order_class,
			)
		);

		$thumbnail_src = wp_get_attachment_image_src( get_post_thumbnail_id(), $settings['image_size'] );

		$this->add_render_attribute(
			$wrap_key,
			array(
				'class'      => array( 'premium-smart-listing__post-wrapper', 'premium-smart-listing__grid-item' ),
				'data-total' => $total,
			)
		);
		?>
			<<?php echo wp_kses_post( $post_tag . ' ' . $this->get_render_attribute_string( $wrap_key ) ); ?>>
				<?php
				if ( $show_thumbnail ) :
					?>
					<div class="premium-smart-listing__post-thumbnail-wrapper">
						<div class="premium-smart-listing__thumbnail-container" style="background-image:url('<?php echo $thumbnail_src[0]; ?>')">
						<?php // $this->get_post_thumbnail( '_blank', 'magazine' ); ?>
						</div>
						<div class="premium-smart-listing__thumbnail-overlay">
							<a class="elementor-icon" href="<?php the_permalink(); ?>" target="<?php echo esc_attr( $target ); ?>" aria-hidden="true"></a>
						</div>
					</div>
					<?php endif; ?>
					<div class="premium-smart-listing__post-content-wrapper">
						<?php
						if ( 'yes' === $settings['categories_meta'] ) {
							Posts_Helper::get_post_categories( $settings, $post_id );
						}
						?>
						<div class="premium-smart-listing__post-title-wrapper"> <?php $this->render_post_title( $target, $key, 'premium-smart-listing__post-title' ); ?>  </div>
						<div <?php echo wp_kses_post( $this->get_render_attribute_string( $meta_key ) ); ?>> <?php Posts_Helper::render_smart_post_meta( 'post', $settings, $post_id ); ?> </div>
						<?php $this->get_post_content( $content_options ); ?>
					</div>
			</<?php echo wp_kses_post( $post_tag ); ?>>

		<?php
	}
}
