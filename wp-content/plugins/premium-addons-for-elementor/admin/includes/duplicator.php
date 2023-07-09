<?php
/**
 * PA Duplicator.
 */

namespace PremiumAddons\Admin\Includes;

use Elementor\Core\Files\CSS\Post as Post_CSS;

if ( ! defined( 'ABSPATH' ) ) {
	exit();
}

/**
 * Class Duplicator
 */
class Duplicator {

	/**
	 * PA Duplicator action.
	 */
	const DUPLICATE_ACTION = 'pa_duplicator';

	/**
	 * Class object
	 *
	 * @var instance
	 */
	private static $instance = null;

	/**
	 * Elementor slug
	 *
	 * @var elementor
	 */
	private static $elementor = 'elementor';

	/**
	 * PAPRO Slug
	 *
	 * @var papro
	 */
	private static $papro = 'premium-addons-pro';

	/**
	 * Constructor for the class
	 */
	public function __construct() {

		add_action( 'admin_action_' . self::DUPLICATE_ACTION, array( $this, 'duplicate_post' ) );

		add_filter( 'post_row_actions', array( $this, 'add_duplicator_actions' ), 10, 2 );

		add_filter( 'page_row_actions', array( $this, 'add_duplicator_actions' ), 10, 2 );

	}

	/**
	 * Add Duplicator Actions
	 *
	 * Add duplicator action links to posts/pages
	 *
	 * @access public
	 * @since 3.9.7
	 *
	 * @param array  $actions row actions.
	 * @param object $post \WP_Post.
	 * @return array
	 */
	public function add_duplicator_actions( $actions, $post ) {

		if ( current_user_can( 'edit_posts' ) && post_type_supports( $post->post_type, 'elementor' ) ) {

			$actions[ self::DUPLICATE_ACTION ] = sprintf(
				'<a href="%1$s" title="%2$s"><span class="screen-reader-text">%2$s</span>%3$s</a>',
				esc_url( self::get_duplicate_url( $post->ID ) ),
				/* translators: %s: Post Title */
				sprintf( esc_attr__( 'Duplicate - %s', 'premium-addons-for-elementor' ), esc_attr( $post->post_title ) ),
				__( 'PA Duplicate', 'premium-addons-for-elementor' )
			);

		}

		return $actions;
	}

	/**
	 * Get duplicate url
	 *
	 * @access public
	 * @since 3.9.7
	 *
	 * @param integer $post_id item ID.
	 * @return string
	 */
	public static function get_duplicate_url( $post_id ) {

		$paged = ( get_query_var( 'paged' ) ) ? get_query_var( 'paged' ) : 1;

		return wp_nonce_url(
			add_query_arg(
				array(
					'action'  => self::DUPLICATE_ACTION,
					'post_id' => $post_id,
					'paged'   => $paged,
				),
				admin_url( 'admin.php' )
			),
			self::DUPLICATE_ACTION
		);
	}

	/**
	 * Duplicate required post/page
	 *
	 * @access public
	 * @since 3.9.7
	 *
	 * @return void
	 */
	public function duplicate_post() {

		if ( ! current_user_can( 'edit_posts' ) ) {
			return;
		}

		$nonce   = isset( $_GET['_wpnonce'] ) ? sanitize_text_field( wp_unslash( $_GET['_wpnonce'] ) ) : '';
		$post_id = isset( $_GET['post_id'] ) ? absint( $_GET['post_id'] ) : 0;
		$paged   = isset( $_GET['paged'] ) ? absint( $_GET['paged'] ) : 1;

		if ( ! wp_verify_nonce( $nonce, self::DUPLICATE_ACTION ) ) {
			return;
		}

		$post = get_post( $post_id );
		if ( is_null( $post ) ) {
			return;
		}

		$post = sanitize_post( $post, 'db' );

		$duplicated_post_id = self::insert_post( $post );

		$redirect = add_query_arg(
			array(
				'post_type' => $post->post_type,
				'paged'     => $paged,
			),
			admin_url( 'edit.php' )
		);

		if ( ! is_wp_error( $duplicated_post_id ) ) {

			self::duplicate_post_taxonomies( $post, $duplicated_post_id );
			self::duplicate_post_meta_data( $post_id, $duplicated_post_id );

			$css = Post_CSS::create( $duplicated_post_id );
			$css->update();

		}

		wp_safe_redirect( $redirect );
		die();
	}

	/**
	 * Duplicate required post/page
	 *
	 * @access public
	 * @since 3.9.7
	 *
	 * @param object $post WP_Post.
	 */
	protected static function insert_post( $post ) {

		$current_user = wp_get_current_user();

		$post_meta = get_post_meta( $post->ID );

		$duplicated_post_args = array(
			'post_status'    => 'draft',
			'post_type'      => $post->post_type,
			'post_parent'    => $post->post_parent,
			'post_content'   => $post->post_content,
			'menu_order'     => $post->menu_order,
			'ping_status'    => $post->ping_status,
			'post_excerpt'   => $post->post_excerpt,
			'post_password'  => $post->post_password,
			'comment_status' => $post->comment_status,
			'to_ping'        => $post->to_ping,
			'post_author'    => $current_user->ID,
			'post_title'     => sprintf(
				/* translators: 1: Post Title, 2: Post ID */
				__( '%1$s - Duplicate - [#%2$d]', 'premium-addons-for-elementor' ),
				$post->post_title,
				$post->ID
			),
		);

		if ( isset( $post_meta['_elementor_edit_mode'][0] ) ) {

			$data = array(
				'meta_input' => array(
					'_elementor_edit_mode'     => $post_meta['_elementor_edit_mode'][0],
					'_elementor_template_type' => $post_meta['_elementor_template_type'][0],
				),
			);

			$duplicated_post_args = array_merge( $duplicated_post_args, $data );

		}

		return wp_insert_post( $duplicated_post_args );
	}

	/**
	 * Add post taxonomies to the cloned version
	 *
	 * @access public
	 * @since 3.9.7
	 *
	 * @param object  $post WP_Post.
	 * @param integer $id item ID.
	 */
	public static function duplicate_post_taxonomies( $post, $id ) {

		$taxonomies = array_map( 'sanitize_text_field', get_object_taxonomies( $post->post_type ) );

		if ( ! empty( $taxonomies ) && is_array( $taxonomies ) ) {
			foreach ( $taxonomies as $taxonomy ) {
				$terms = wp_get_object_terms( $post->ID, $taxonomy, array( 'fields' => 'slugs' ) );
				wp_set_object_terms( $id, $terms, $taxonomy, false );
			}
		}
	}

	/**
	 * Add post meta data to the cloned version
	 *
	 * @access public
	 * @since 3.9.7
	 *
	 * @param object  $post WP_Post.
	 * @param integer $id item ID.
	 */
	public static function duplicate_post_meta_data( $old_id, $new_id ) {

		$post_meta_keys = get_post_custom_keys( $old_id );

		if ( ! empty( $post_meta_keys ) ) {

			foreach ( $post_meta_keys as $meta_key ) {

				$meta_values = get_post_custom_values( $meta_key, $old_id );

				foreach ( $meta_values as $meta_value ) {

					$meta_value = maybe_unserialize( $meta_value );

					update_post_meta( $new_id, $meta_key, wp_slash( $meta_value ) );

				}
			}
		}

	}


	/**
	 * Creates and returns an instance of the class
	 *
	 * @since 3.20.9
	 * @access public
	 *
	 * @return object
	 */
	public static function get_instance() {

		if ( ! isset( self::$instance ) ) {

			self::$instance = new self();

		}

		return self::$instance;
	}

}
