<?php
namespace Saltus\WP\Plugin\Saltus\InteractiveMaps\Plugin;

use Saltus\WP\Plugin\Saltus\InteractiveMaps\Core;

/**
 * Manage Assets like scripts and styles.
 */
class Assets {

	/**
	 * The plugin's instance.
	 *
	 * @var Core
	 */
	public $core;

	/**
	 * Options for Map CPT
	 */
	private $options;

	/**
	 * Define Assets
	 *
	 * @param Core $core This plugin's instance.
	 */
	public function __construct( Core $core ) {

		$options = get_option( 'interactive-maps' );

		$this->options = $options;

		$this->core = $core;

		// control minify plugins
		$this->exclude_from_minify();

	}

	/**
	 * Exclude plugin files from minify plugins
	 */
	public function exclude_from_minify() {
		// wp rocket
		add_filter(
			'rocket_exclude_defer_js', function( $excluded_files = array() ) {
				$excluded_files[] = '/wp-content/plugins/interactive-geo-maps/(.*).js';
				$excluded_files[] = 'https://cdn.amcharts.com/';
				return $excluded_files;
			}
		);

	}

	/**
	 * Load assets.
	 */
	public function load_assets() {

		add_action( 'admin_enqueue_scripts', array( $this, 'load_admin_styles' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'load_admin_scripts' ) );

	}

	/**
	 * Load assets
	 */
	public function load_admin_styles() {

		global $typenow;

		// we load the file across the admin, to remove the trial notices and so on
		// will need to be improved.


		// styles
		wp_register_style(
			$this->core->name . '_admin',
			plugins_url( 'assets/admin/css/admin-style.min.css', $this->core->file_path ),
			false,
			$this->core->version
		);

		wp_enqueue_style( $this->core->name . '_admin' );

		if ( $typenow === 'igmap' ) {
			// also add normal map styles
			$this->load_map_styles();
		}
	}

	public function load_admin_scripts() {

		global $typenow;
		global $post;
		global $pagenow;

		if ( $typenow === 'igmap' && ( $pagenow === 'post.php' || $pagenow === 'post-new.php' ) ) {

			if ( $pagenow === 'post.php' && $post ) {

				$atts = array(
					'id' => $post->ID,
				);

				// get current map initial meta
				$map = new Map( $this->core );
				$map->setup( $atts );
				$meta = $map->meta;

			} elseif ( $pagenow === 'post-new.php' ) {

				$options = $this->options;

				$meta = [
					'disabled'   => true,
					'container'  => 'map_preview_1',
					'zoomMaster' => isset( $options['zoomMaster'] ) ? $options['zoomMaster'] : false,
				];
			}

			if ( ! is_array( $meta ) ) {
				$meta = [];
			}

			// include meta value saying we're in the admin
			$meta['admin']                        = '1';
			$meta['container_info']               = 'map_visual_info';
			$meta['container_click_data']         = 'map_click_events_info';
			$meta['container_click_coordinates']  = 'map_click_events_coordinates';
			$meta['regionLabelCustomCoordinates'] = 'regionLabelCustomCoordinates';

			$this->load_map_scripts( $meta );

			$this->load_google_scripts();

			// jsonTree
			$this->load_json_tree();

			// autocomplete
			$this->load_autocomplete();

			// Admin Custom Javascript
			wp_register_script(
				$this->core->name . '_admin_build',
				plugins_url( 'assets/admin/js/admin-scripts.js', $this->core->file_path ),
				array(),
				$this->core->version,
				true
			);
			wp_enqueue_script( $this->core->name . '_admin_build' );

			$this->options['ajax_url']       = admin_url( 'admin-ajax.php' );
			$this->options['ajax_nonce']     = wp_create_nonce( 'igmaps_edit' );
			$this->options['max_input_vars'] = ini_get( 'max_input_vars' );
			$this->options['messages']       = [
				/* translators: region code refers to a alphanumeric code that represents a particular region */
				'regionCode'      => __( 'Region Code', 'interactive-geo-maps' ),
				'regionName'      => __( 'Region Name', 'interactive-geo-maps' ),
				'fullData'        => __( 'Full Available Data', 'interactive-geo-maps' ),
				'jsonError'       => __( 'Error: can\'t process data', 'interactive-geo-maps' ),
				'maxInputError'   => __( '<strong>Attention!</strong> Your map has too many entries for your server to handle when saving it. <br> To prevent loosing map data, avoid updating your map until this issue is solved. <a href="https://interactivegeomaps.com/docs/max-input-vars-issue" target="_blank">Read more about this issue here</a>.', 'interactive-geo-maps' ),
				'addToMap'        => __( 'Add this region to map', 'interactive-geo-maps' ),
                'regionCodeInfo'  => __('Click + to add region to the map', 'interactive-geo-maps' ),
                'resetAutoLabels' => __('Are you sure you want to reset the labels positions?', 'interactive-geo-maps' ),
			];

			wp_localize_script( $this->core->name . '_admin_build', 'iMapsOptions', $this->options );
		}

	}

	/**
	 * Load jsonTree script
	 *
	 * @return void
	 */
	public function load_json_tree() {
		wp_register_script(
			$this->core->name . '_jsontree',
			plugins_url( 'assets/admin/vendor/jsonTree/jsonTree.js', $this->core->file_path ),
			array(),
			$this->core->version,
			true
		);
		wp_enqueue_script( $this->core->name . '_jsontree' );

		// styles
		wp_register_style(
			$this->core->name . '_jsontree',
			plugins_url( 'assets/admin/vendor/jsonTree/jsonTree.css', $this->core->file_path ),
			false,
			$this->core->version
		);

		wp_enqueue_style( $this->core->name . '_jsontree' );
	}

	public function load_autocomplete() {
		wp_register_script(
			$this->core->name . '_autocomplete',
			plugins_url( 'assets/admin/vendor/autocomplete/autocomplete.js', $this->core->file_path ),
			array(),
			$this->core->version,
			true
		);
		wp_enqueue_script( $this->core->name . '_autocomplete' );

		// styles
		wp_register_style(
			$this->core->name . '_autocomplete',
			plugins_url( 'assets/admin/vendor/autocomplete/autocomplete.css', $this->core->file_path ),
			false,
			$this->core->version
		);

		wp_enqueue_style( $this->core->name . '_autocomplete' );
	}

	public function load_google_scripts() {

		if ( $this->options['googleApiKey'] && $this->options['googleApiKey'] !== '' ) {
			wp_register_script(
				$this->core->name . '_google_geocoding',
				sprintf( 'https://maps.googleapis.com/maps/api/js?key=%s&libraries=places', $this->options['googleApiKey'] ),
				array(),
				$this->core->version,
				true
			);
			wp_enqueue_script( $this->core->name . '_google_geocoding' );
		}

	}

	/**
	 * Load assets
	 */
	public function load_map_styles() {

		$hasfilter = has_filter( 'igm_public_assets_url' );
		if ( $hasfilter ) {
			$url = apply_filters( 'igm_public_assets_url', 'css/styles.min.css' );
		} else {
			$url = plugins_url( 'assets/public/css/styles.min.css', $this->core->file_path );
		}

		// styles
		wp_register_style(
			$this->core->name . '_main',
			$url,
			false,
			$this->core->version
		);

		wp_enqueue_style( $this->core->name . '_main' );

		if ( '' !== $this->core->extra_styles ) {

			// since some code might be sanitized but we still need these operators in javascript
			$css = $this->core->extra_styles;
			$css = str_replace('&gt;','>',$css);
			$css = str_replace('&lt;','<',$css);
			$css = str_replace('&amp;','&',$css);

			wp_add_inline_style( $this->core->name . '_main', $css );
		}

	}

	public function load_map_scripts( $localize = false ) {

		$deps       = array();
		$async_urls = array();

		// options to localize script - make data available to the script
		$options = $this->options;

		$localize_options = array(
			'animations' => isset( $options['animations'] ) ? $options['animations'] : true,
			'lazyLoad'   => isset( $options['lazyLoad'] ) ? $options['lazyLoad'] : false,
			'async'      => isset( $options['async'] ) ? $options['async'] : false,
			'hold'       => isset( $options['hold'] ) ? $options['hold'] : false,
			'locale'     => isset( $options['amcharts_locale'] ) && trim( $options['amcharts_locale'] ) !== '' ? trim( $options['amcharts_locale'] ) : false,
            'lang'       => isset( $options['amcharts_lang'] ) && trim( $options['amcharts_lang'] ) !== '' ? trim( $options['amcharts_lang'] ) : false,
		);

		if ( is_admin() ) {
			// in admin, let's not use async for now
			$localize_options['async'] = false;
			// if we're in the admin area, do not hold maps rendering
			$localize_options['hold'] = false;
		}

		// $version  = '4'; //'version/4.7.18';
		$version = apply_filters( 'igm_amcharts_version', 'version/4.10.29' );

		if ( isset( $options['amcharts_version'] ) && trim( $options['amcharts_version'] ) !== '' ) {
			$version = apply_filters( 'igm_amcharts_version', 'version/' . $options['amcharts_version'] );
		}

		$url_path = apply_filters( 'igm_amcharts_lib_url', 'https://cdn.amcharts.com/lib/' );

		if ( $localize ) {

			if ( ! empty( $localize ) ) {

				$this->core->add_localize_data( $localize );

			}
		}

		// if we load async
		if ( isset( $localize_options['async'] ) && $localize_options['async'] ) {
			array_push( $async_urls, $url_path . $version . '/core.js' );
			array_push( $async_urls, $url_path . $version . '/maps.js' );
			if ( isset( $localize_options['animations'] ) && $localize_options['animations'] ) {
				array_push( $async_urls, $url_path . $version . '/themes/animated.js' );
			}
			if ( isset( $options['amcharts_locale'] ) && trim( $options['amcharts_locale'] ) !== '' ) {
				array_push( $async_urls, $url_path . $version . '/lang/' . $options['amcharts_locale'] . '.js' );
			}

            if ( isset( $options['amcharts_lang'] ) && trim( $options['amcharts_lang'] ) !== '' ) {
				array_push( $async_urls, $url_path . $version . '/geodata/lang/' . strtoupper( $options['amcharts_locale'] ) . '.js' );
			}

			// buil proper dependencies
			foreach ( $this->core->script_localize_data as $mapsdata => $mapdata ) {
				if ( ! isset( $mapdata['urls'] ) ) {
					continue;
				}
				foreach ( $mapdata['urls'] as $k => $url ) {
					array_push( $async_urls, $url );
				}
			}
		}

		// if we won't be using async loading
		else {

			wp_register_script(
				$this->core->name . '_amcharts_core',
				$url_path . $version . '/core.js',
				array(),
				$this->core->version,
				true
			);
			wp_enqueue_script( $this->core->name . '_amcharts_core' );

			$this->modify_script_tag( $this->core->name . '_amcharts_core', $this->options );

			wp_register_script(
				$this->core->name . '_amcharts_maps',
				$url_path . $version . '/maps.js',
				array( $this->core->name . '_amcharts_core' ),
				$this->core->version,
				true
			);
			wp_enqueue_script( $this->core->name . '_amcharts_maps' );

			array_push( $deps, $this->core->name . '_amcharts_maps' );

			$this->modify_script_tag( $this->core->name . '_amcharts_maps' );

			if ( isset( $localize_options['animations'] ) && $localize_options['animations'] ) {

				wp_register_script(
					$this->core->name . '_amcharts_animated',
					$url_path . $version . '/themes/animated.js',
					array( $this->core->name . '_amcharts_maps' ),
					$this->core->version,
					true
				);
				wp_enqueue_script( $this->core->name . '_amcharts_animated' );
				array_push( $deps, $this->core->name . '_amcharts_animated' );

				$this->modify_script_tag( $this->core->name . '_amcharts_animated' );

			}

			if ( isset( $options['amcharts_locale'] ) && trim( $options['amcharts_locale'] ) !== '' ) {
				wp_register_script(
					$this->core->name . '_amcharts_locale',
					$url_path . $version . '/lang/' . $options['amcharts_locale'] . '.js',
					array( $this->core->name . '_amcharts_maps' ),
					$this->core->version,
					true
				);
				wp_enqueue_script( $this->core->name . '_amcharts_locale' );
				array_push( $deps, $this->core->name . '_amcharts_locale' );

				$this->modify_script_tag( $this->core->name . '_amcharts_locale' );
			}

            if ( isset( $options['amcharts_lang'] ) && trim( $options['amcharts_lang'] ) !== '' ) {
				wp_register_script(
					$this->core->name . '_amcharts_lang',
					$url_path . '4' . '/geodata/lang/' . strtoupper( $options['amcharts_lang'] ) . '.js',
					array( $this->core->name . '_amcharts_maps' ),
					$this->core->version,
					true
				);
				wp_enqueue_script( $this->core->name . '_amcharts_lang' );
				array_push( $deps, $this->core->name . '_amcharts_lang' );

				$this->modify_script_tag( $this->core->name . '_amcharts_lang' );
			}

			// buil proper dependencies
			foreach ( $this->core->script_localize_data as $key => $mapdata ) {
				if ( ! isset( $mapdata['urls'] ) ) {
					continue;
				}
				foreach ( $mapdata['urls'] as $k => $url ) {
					wp_register_script(
						$this->core->name . '_' . $k,
						$url,
						$this->core->name . '_amcharts_maps',
						$this->core->version,
						true
					);
					array_push( $deps, $this->core->name . '_' . $k );

					$this->modify_script_tag( $this->core->name . '_' . $k );
				}
			}
		}

		// we remove script, to reset the localized data, just in case
		wp_deregister_script( $this->core->name . '_map_service' );


		//public asset

		$hasfilter = has_filter( 'igm_public_assets_url' );
		if ( $hasfilter ) {
			$service_url = apply_filters( 'igm_public_assets_url', 'map-service/app.min.js' );
		} else {
			$url = defined('WP_DEBUG') && true === WP_DEBUG ? 'assets/public/map-service/app.js' : 'assets/public/map-service/app.min.js';
			$service_url = plugins_url( $url, $this->core->file_path );
		}

		wp_register_script(
			$this->core->name . '_map_service',
			$service_url,
			$deps,
			$this->core->version,
			true
		);

		wp_enqueue_script( $this->core->name . '_map_service' );

		if ( $this->core->extra_scripts !== '' ) {
			wp_add_inline_script( $this->core->name . '_map_service', $this->core->extra_scripts );
		}

		if ( isset( $localize_options['async'] ) && $localize_options['async'] ) {
			$this->async_script_tag( $this->core->name . '_map_service' );
		} else {
			$this->modify_script_tag( $this->core->name . '_map_service' );
		}

		$final_data = [
			'options' => $localize_options,
			'data'    => $this->core->script_localize_data,
			'async'   => array_values( array_unique( $async_urls ) ),
			'version' => $this->core->version,
		];

		wp_localize_script( $this->core->name . '_map_service', 'iMapsData', $final_data );

	}

	/**
	 * Add the script id to the script tag and hopefully remove async attribute added by other plugins
	 *
	 * @param string $script_id
	 * @return void
	 */
	public function modify_script_tag( $script_id ) {
		add_filter(
			'script_loader_tag',
			function( $tag, $handle, $src ) use ( $script_id ) {

				// check against our registered script handle
				if ( $script_id === $handle ) {
					// add attributes of your choice
					$tag = str_replace( 'async=\'async\'', '', $tag );
				}
				return $tag;
			},
			31, // async for WordPress plugin uses 20
			3
		);
	}

	/**
	 * Add the async attribute to script tag
	 *
	 * @param string $script_id
	 * @return void
	 */
	public function async_script_tag( $script_id ) {
		add_filter(
			'script_loader_tag',
			function( $tag, $handle, $src ) use ( $script_id ) {

				// check against our registered script handle
				if ( $script_id === $handle ) {

					if ( strpos( $tag, 'async' ) === false ) {
						// add attributes of your choice
						$tag = str_replace( '<script ', '<script async=\'async\' ', $tag );
					}
				}
				return $tag;
			},
			31, // async for WordPress plugin uses 20
			3
		);
	}
}
