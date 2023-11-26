<?php
/**
 * PA Assets Manager.
 */

namespace PremiumAddons\Includes;

use Elementor\Plugin;
use PremiumAddons\Includes\Helper_Functions;
use PremiumAddons\Admin\Includes\Admin_Helper;

require_once PREMIUM_ADDONS_PATH . 'widgets/dep/urlopen.php';

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * PA Assets Manager Class.
 */
class Assets_Manager {

	/**
	 * Class Instance.
	 *
	 * @var object|null instance.
	 */
	private static $instance = null;

	/**
	 * Post Id.
	 * Option Id.
	 *
	 * @var string|null post_id.
	 */
	public static $post_id = null;

	/**
	 * Templates ids loaded in a post.
	 *
	 * @var array temp_ids.
	 */
	public static $temp_ids = array();

	/**
	 * All elements loaded in a post.
	 *
	 * @var array temp_elements.
	 */
	public static $temp_elements = array();

	/**
	 * Is page assets updated.
	 *
	 * @var boolean is_updated.
	 */
	public static $is_updated = null;

	/**
	 * Class Constructor.
	 */
	public function __construct() {

		add_action( 'elementor/editor/after_save', array( $this, 'handle_post_save' ), 10, 2 );

		// Check if the elments are cached.
		add_action( 'wp', array( $this, 'set_assets_vars' ) );

		// Save the elements on the current page.
		add_filter( 'elementor/frontend/builder_content_data', array( $this, 'manage_post_data' ), 10, 2 );

		add_action( 'wp_footer', array( $this, 'cache_post_assets' ) );

		add_action( 'wp_trash_post', array( $this, 'delete_cached_options' ) );

	}

	/**
	 * Sets Edit Time upon editor save.
	 *
	 * @access public
	 * @since 4.6.1
	 */
	public function handle_post_save( $post_id ) {

		if ( wp_doing_cron() ) {
			return;
		}

		// The post is saved, then we need to remove the assets related to it.
		$this->set_post_id( $post_id );
		self::remove_files();

		update_option( 'pa_edit_time', strtotime( 'now' ) );
	}

	/**
	 * Mange Post Data.
	 *
	 * @access public
	 * @since 4.6.1
	 *
	 * @param array      $data  post data.
	 * @param int|string $post_id  post id.
	 *
	 * @return array
	 */
	public function manage_post_data( $data, $post_id ) {

		if ( ! self::$is_updated ) {
			$pa_elems = $this->extract_pa_elements( $data );

			self::$temp_ids[]    = $post_id;
			self::$temp_elements = array_unique( array_merge( self::$temp_elements, $pa_elems ) );
		}

		return $data;
	}

	/**
	 * Set post unique id.
	 *
	 * @access public
	 * @since 4.6.1
	 *
	 * @param int|string $id  post id.
	 */
	public function set_post_id( $id = 'default' ) {

		$post_id = 'default' === $id ? 'pa_assets_' . get_queried_object_id() : 'pa_assets_' . $id;

		if ( null === self::$post_id ) {
			self::$post_id = Helper_Functions::generate_unique_id( $post_id );
		}
	}

	/**
	 * Extracts PA Elements.
	 *
	 * @access public
	 * @since 4.6.1
	 *
	 * @param array $data  post data.
	 *
	 * @return array
	 */
	public function extract_pa_elements( $data ) {

		if ( empty( $data ) ) {
			return array();
		}

		$pa_names = Admin_Helper::get_pa_elements_names();

		$social_revs = array(
			'premium-yelp-reviews',
			'premium-google-reviews',
			'premium-facebook-reviews',
		);

		$pa_elems = array();

		Plugin::$instance->db->iterate_data(
			$data,
			function ( $element ) use ( &$pa_elems, $pa_names, $social_revs ) {

				if ( isset( $element['elType'] ) ) {

					if ( 'widget' === $element['elType'] && isset( $element['widgetType'] ) ) {

						$widget_type = ( 'global' === $element['widgetType'] && ! empty( $element['templateID'] ) ) ? $this->get_global_widget_type( $element['templateID'] ) : $element['widgetType'];

						if ( in_array( $widget_type, $pa_names, true ) && ! in_array( $widget_type, $pa_elems, true ) ) {

							$widget_type = in_array( $widget_type, $social_revs, true ) ? 'premium-reviews' : $widget_type;

							if ( in_array( $widget_type, array( 'premium-twitter-feed', 'premium-facebook-feed' ), true ) && ! in_array( 'social-common', $pa_elems, true ) ) {
								array_push( $pa_elems, 'social-common' );
							}

							array_push( $pa_elems, $widget_type );

							if ( 'premium-woo-products' === $widget_type ) {
								$papro_activated = apply_filters( 'papro_activated', false );

								if ( $papro_activated ) {
									array_push( $pa_elems, 'premium-woo-products-pro' );
								}
							}
						}
					}
				}
			}
		);

		return $pa_elems;
	}

	/**
	 * Get Global Wiget Type.
	 *
	 * @access public
	 * @since 4.6.1
	 * @link https://code.elementor.com/methods/elementor-templatelibrary-manager-get_template_data/
	 * @param int $temp_id  template it.
	 *
	 * @return string|void
	 */
	public function get_global_widget_type( $temp_id ) {

		$temp_data = Plugin::$instance->templates_manager->get_template_data(
			array(
				'source'      => 'local',
				'template_id' => $temp_id,
			)
		);

		if ( is_wp_error( $temp_data ) || ! $temp_data || empty( $temp_data ) ) {
			return;
		}

		if ( ! isset( $temp_data['content'] ) || empty( $temp_data['content'] ) ) {
			return;
		}

		return $temp_data['content'][0]['widgetType'];
	}

	/**
	 * Sets Assets Variables.
	 * Sets Post ID & Is_updated Flag.
	 *
	 * @access public
	 * @since 4.6.1
	 */
	public function set_assets_vars() {

		$is_edit_mode = Helper_Functions::is_edit_mode();

		if ( ! $this->is_built_with_elementor() || $is_edit_mode ) {
			return;
		}

		$this->set_post_id();

		self::$is_updated = self::is_ready_for_generate();
	}

	/**
	 * Is Built With Elementor.
	 *
	 * @access public
	 * @since 4.6.1
	 *
	 * @return boolean
	 */
	public function is_built_with_elementor() {

		if ( ! class_exists( 'Elementor\Plugin' ) ) {
			return false;
		}

		$type = get_post_type();

		if ( 'page' !== $type && 'post' !== $type ) {
			return false;
		}

		$current_id = get_the_ID();

		if ( ! $current_id || $current_id < 0 ) {
			return false;
		}

		return Plugin::$instance->documents->get( get_the_ID() )->is_built_with_elementor();
	}

	/**
	 * Check if assets is updated.
	 *
	 * @access public
	 * @since 4.6.1
	 *
	 * @return boolean
	 */
	public static function is_ready_for_generate() {

		$editor_time = get_option( 'pa_edit_time', false );

		// If no post/page was saved after the feature is enabled.
		if ( ! $editor_time ) {
			update_option( 'pa_edit_time', strtotime( 'now' ) );
		}

		$post_edit_time = get_option( 'pa_edit_' . self::$post_id, false );

		// If the time of the last update is not equal to the time the current post was last changed. This means another post was saved, then load the default assets.
		// In this case, we need to load the default assets until the elements in the page needs to be cached first.
		if ( ! $post_edit_time || (int) $editor_time !== (int) $post_edit_time ) {
			// A change was made in the page elements, then we need to force the assets to be regenerated
			self::remove_files();
			return false;
		}

		return true;
	}

	/**
	 * Cached post assets.
	 *
	 * Update post options in db on page load.
	 *
	 * @access public
	 * @since 4.6.1
	 */
	public function cache_post_assets() {

		$is_edit_mode = Helper_Functions::is_edit_mode();
		$cond         = $this->is_built_with_elementor() && ! $is_edit_mode;

		if ( ! self::$is_updated && $cond ) {
			update_option( 'pa_elements_' . self::$post_id, self::$temp_elements, false );
			update_option( 'pa_edit_' . self::$post_id, get_option( 'pa_edit_time' ), false );
		}
	}

	/**
	 * Delete Cached Options.
	 * Delete post options from db on post delete.
	 *
	 * @access public
	 * @since 4.6.1
	 *
	 * @param int $post_id  post id.
	 */
	public function delete_cached_options( $post_id ) {

		$id = substr( md5( 'pa_assets_' . $post_id ), 0, 9 );

		delete_option( 'pa_elements_' . $id );
		delete_option( 'pa_edit_' . $id );

	}

	/**
	 * Generate Assets files.
	 * Adds assets into pa-frontend(|-rtl).min.(js|css).
	 *
	 * @access public
	 * @since 4.6.1
	 *
	 * @param string $ext  assets extensions (js|css).
	 */
	public static function generate_asset_file( $ext ) {

		$direction      = is_rtl() && 'css' === $ext ? 'rtl-' : '';
		$main_file_name = Helper_Functions::get_safe_path( PREMIUM_ASSETS_PATH . '/pa-frontend-' . $direction . self::$post_id . '.min.' . $ext );

		// If the file already exists, then there is no need to regenerate a new one.
		if ( file_exists( $main_file_name ) ) {
			return;
		}

		$content = self::get_asset_file_content( $ext );

		// If no premium elements exist on the page, then don't generate files
		if ( empty( $content ) ) {
			return;
		}

		if ( 'css' === $ext && is_rtl() ) {
			$rtl_file_name = Helper_Functions::get_safe_path( PREMIUM_ASSETS_PATH . '/pa-frontend-rtl-' . self::$post_id . '.min.css' );
		}

		if ( ! file_exists( PREMIUM_ASSETS_PATH ) ) {
			wp_mkdir_p( PREMIUM_ASSETS_PATH );
		}

		if ( 'css' === $ext ) {

			if ( is_rtl() ) {
				// Make sure to delete the file before creating the new one.
                file_put_contents( $rtl_file_name, '@charset "UTF-8";' . $content['rtl'] );  // phpcs:ignore
			} else {
                file_put_contents( $main_file_name, '@charset "UTF-8";' . $content['main'] ); // phpcs:ignore
			}
		} else {
			file_put_contents( $main_file_name, $content );  // phpcs:ignore
		}
	}


	/**
	 * Clear cached file.
	 * Delete file if it exists.
	 *
	 * @access public
	 * @since 4.6.1
	 *
	 * @param string $file_name  file name.
	 */
	public static function clear_cached_file( $file_name ) {

		if ( file_exists( $file_name ) ) {
			unlink( $file_name );
		}
	}

	/**
	 * Remove files
	 *
	 * @since 4.6.1
	 */
	public static function remove_files() {

		$ext = array( 'css', 'js' );

		foreach ( $ext as $e ) {

			$path = PREMIUM_ASSETS_PATH . '/pa-frontend-' . self::$post_id . '.min.' . $e;

			if ( 'css' === $e ) {
				$rtl_path = PREMIUM_ASSETS_PATH . '/pa-frontend-rtl-' . self::$post_id . '.min.' . $e;
				self::clear_cached_file( $rtl_path );
			}

			self::clear_cached_file( $path );
		}

	}

	/**
	 * Get Asset File Content.
	 *
	 * Collects pa/papro widgets assets.
	 *
	 * @access public
	 * @since 4.6.1
	 *
	 * @param string $ext js|css.
	 *
	 * @return string|array $content
	 */
	public static function get_asset_file_content( $ext ) {

		// Get the cached elements of the current post/page.
		$pa_elements = get_option( 'pa_elements_' . self::$post_id, array() );

		if ( empty( $pa_elements ) ) {
			return '';
		}

		$content = '';

		if ( 'css' === $ext ) {
			$rtl_content = '';
		}

		$pa_elements = self::prepare_pa_elements( $pa_elements, $ext );

		foreach ( $pa_elements as $element ) {

			$path = self::get_file_path( $element, $ext );

			if ( ! $path ) {
				continue;
			}

			$content .= self::get_file_content( $path );

			if ( 'css' === $ext && is_rtl() ) {
				$rtl_path     = self::get_file_path( $element, $ext, '-rtl' );
				$rtl_content .= self::get_file_content( $rtl_path );
			}
		}

		if ( 'css' === $ext ) {

			$content = array(
				'main' => $content,
				'rtl'  => $rtl_content,
			);

			// Fix: at-rule or selector expected css error.
			$content = str_replace( '@charset "UTF-8";', '', $content );
		}

		return $content;
	}

	/**
	 * Prepare PA Elements.
	 *
	 * @access public
	 * @since 4.6.1
	 *
	 * @param array  $elements  post elements.
	 * @param string $ext  js|css.
	 *
	 * @return array
	 */
	public static function prepare_pa_elements( $elements, $ext ) {

		if ( 'css' === $ext ) {
			$common_assets = self::has_free_elements( $elements ) ? array( 'common' ) : array();
			$common_assets = self::has_pro_elements( $elements ) ? array_merge( $common_assets, array( 'common-pro' ) ) : $common_assets;

			$elements = array_merge( $elements, $common_assets );
            $indep_elements = array(
                'premium-world-clock'
            );

		} else {
			$indep_elements = array(
				'social-common',
				'premium-hscroll',
				'premium-facebook-feed',
				'premium-behance-feed',
				'premium-lottie',
				'premium-vscroll',
				'premium-hscroll',
				'premium-nav-menu',
				'premium-addon-maps',
				'premium-woo-products-pro',
				// 'premium-addon-testimonials',
				'premium-smart-post-listing',
				'premium-addon-pricing-table',
				'premium-addon-image-separator',
				'premium-notifications',
			);

		}

        $elements = array_diff( $elements, $indep_elements );

		return $elements;
	}

	/**
	 * Get File Content.
	 *
	 * @param string $path file path.
	 *
	 * @return string
	 */
	public static function get_file_content( $path ) {

		$file_content = rplg_urlopen( $path );

		if ( isset( $file_content['code'] ) ) {
			if ( in_array( $file_content['code'], array( 404, 401 ), true ) ) {
				return '';
			}
		}

		return self::clean_content( $file_content['data'] );
	}

	/**
	 * Clean Content
	 * Removes Page Html if it's returned as result.
	 *
	 * @param string $content file content.
	 *
	 * @return string
	 */
	public static function clean_content( $content ) {

		if ( strpos( $content, '<!DOCTYPE html>' ) ) {
			$content = explode( '<!DOCTYPE html>', $content )[0];
		}

		if ( strpos( $content, '<!doctype html>' ) ) {
			$content = explode( '<!doctype html>', $content )[0];
		}

		return $content;
	}

	/**
	 * Get File Path.
	 * Construct file path.
	 *
	 * @param string $element  pa element name.
	 * @param string $ext      file extension ( js|css).
	 * @param string $dir      post dir (-rtl|'').
	 *
	 * @return string file path.
	 */
	public static function get_file_path( $element, $ext, $dir = '' ) {

		$is_pro = self::is_pro_widget( $element );

		$papro_activated = apply_filters( 'papro_activated', false ) && version_compare( PREMIUM_PRO_ADDONS_VERSION, '2.7.1', '>' );

		if ( ! $papro_activated && $is_pro ) {
			return false;
		}

		$element = str_replace( '-addon', '', $element );

		$path = $is_pro ? PREMIUM_PRO_ADDONS_URL : PREMIUM_ADDONS_URL;

		return $path . 'assets/frontend/min-' . $ext . '/' . $element . $dir . '.min.' . $ext;
	}

	/**
	 * Is Pro Widget.
	 * Checks if the widget is pro.
	 *
	 * @access public
	 * @since 4.6.1
	 *
	 * @param string $widget  widget name.
	 *
	 * @return bool
	 */
	public static function is_pro_widget( $widget ) {

		$pro_names = array_merge( array( 'common-pro', 'premium-reviews', 'premium-woo-products-pro', 'social-common' ), self::get_pro_widgets_names() );

		return in_array( $widget, $pro_names, true );
	}

	/**
	 * Has Pro Elements.
	 * Check if the post has pa pro elements.
	 *
	 * @access public
	 * @since 4.6.1
	 *
	 * @param array $post_elems post elements.
	 *
	 * @return boolean
	 */
	public static function has_pro_elements( $post_elems ) {

		$papro_elems = self::get_pro_widgets_names();
		$has_pro     = array_intersect( $post_elems, $papro_elems ) ? true : false;

		return $has_pro;
	}

	/**
	 * Has Free Elements.
	 * Check if the post has pa elements.
	 *
	 * @access public
	 * @since 4.6.1
	 *
	 * @param array $post_elems post elements.
	 *
	 * @return boolean
	 */
	public static function has_free_elements( $post_elems ) {

		$pa_elems = Admin_Helper::get_free_widgets_names();

		// add smart post listing
		$pa_elems[] = 'premium-smart-post-listing';
		$pa_elems[] = 'premium-addon-instagram-feed';

		$has_free = array_intersect( $post_elems, $pa_elems ) ? true : false;

        return $has_free;
	}

	/**
	 * Get Pro Widgets Names.
	 *
	 * @access public
	 * @since 4.6.1
	 *
	 * @return array
	 */
	public static function get_pro_widgets_names() {

		$pro_elems = Admin_Helper::get_pro_elements();
		$pro_names = array();

		foreach ( $pro_elems as $element ) {
			if ( isset( $element['name'] ) ) {
				array_push( $pro_names, $element['name'] );
			}
		}

		return $pro_names;
	}

	/**
	 * Creates and returns an instance of the class.
	 *
	 * @since  4.6.1
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