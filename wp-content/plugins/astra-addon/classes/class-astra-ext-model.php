<?php
/**
 * Astra Extension Model Class
 *
 * @package Astra Addon
 */

/**
 * Provide Extension related data.
 *
 * @since 1.0
 */
// @codingStandardsIgnoreStart
final class Astra_Ext_Model {
	// @codingStandardsIgnoreEnd

	/**
	 * Construct
	 */
	public function __construct() {

		if ( class_exists( 'Astra_Customizer' ) ) {
			$this->load_extensions();
		}

		if ( Astra_Ext_Extension::is_active( 'advanced-headers' ) || Astra_Ext_Extension::is_active( 'advanced-hooks' ) ) {
			add_action( 'admin_bar_menu', array( $this, 'add_admin_menu' ), 90 );
			add_action( 'wp_head', array( $this, 'print_style' ) );
		}
	}

	/**
	 * Load Extensions
	 *
	 * @return void
	 */
	public function load_extensions() {

		$enabled_extension  = Astra_Ext_Extension::get_enabled_addons();
		$default_extensions = Astra_Ext_Extension::get_default_addons();
		$enabled_extension  = $enabled_extension + $default_extensions;

		if ( 0 < count( $enabled_extension ) ) {

			if ( isset( $enabled_extension['all'] ) ) {
				unset( $enabled_extension['all'] );
			}

			foreach ( $enabled_extension as $slug => $value ) {

				if ( false == $value ) {
					continue;
				}

				$extension_path = ASTRA_EXT_DIR . 'addons/' . esc_attr( $slug ) . '/class-astra-ext-' . esc_attr( $slug ) . '.php';
				$extension_path = apply_filters( 'astra_addon_path', $extension_path, $slug );

				// Check for the extension.
				if ( file_exists( $extension_path ) ) {
					require_once $extension_path;
				}
			}
		}
	}

	/**
	 * Add Admin menu item
	 *
	 * @param object WP_Admin_Bar $admin_bar Admin bar.
	 * @return void
	 * @since 4.0.0
	 */
	public function add_admin_menu( $admin_bar ) {
		if ( is_admin() ) {
			return;
		}

		// Check if current user can have edit access.
		if ( ! current_user_can( 'edit_posts' ) ) {
			return;
		}

		$custom_layout_addon_active = Astra_Ext_Extension::is_active( 'advanced-hooks' ) ? true : false;
		$page_headers_addon_active  = Astra_Ext_Extension::is_active( 'advanced-headers' ) ? true : false;
		$post_id                    = get_the_ID() ? get_the_ID() : 0;
		$current_post               = $post_id ? get_post( $post_id, OBJECT ) : false;
		$has_shortcode              = ( is_object( $current_post ) && has_shortcode( $current_post->post_content, 'astra_custom_layout' ) ) ? true : false;

		if ( $custom_layout_addon_active || $page_headers_addon_active || $has_shortcode ) {
			$custom_layouts = false;
			if ( $custom_layout_addon_active ) {
				$option = array(
					'location'  => 'ast-advanced-hook-location',
					'exclusion' => 'ast-advanced-hook-exclusion',
					'users'     => 'ast-advanced-hook-users',
				);

				$custom_layouts = Astra_Target_Rules_Fields::get_instance()->get_posts_by_conditions( ASTRA_ADVANCED_HOOKS_POST_TYPE, $option );
			}

			$page_headers = false;
			if ( $page_headers_addon_active ) {
				$option = array(
					'location'  => 'ast-advanced-headers-location',
					'exclusion' => 'ast-advanced-headers-exclusion',
					'users'     => 'ast-advanced-headers-users',
					'page_meta' => 'adv-header-id-meta',
				);

				$page_headers = Astra_Target_Rules_Fields::get_instance()->get_posts_by_conditions( 'astra_adv_header', $option );
			}

			if ( $custom_layouts || $page_headers || $has_shortcode ) {
				$admin_bar->add_node(
					array(
						'id'    => 'astra-advanced-layouts',
						'title' => '<span class="ab-item astra-admin-logo"></span>',
					)
				);
			}

			if ( is_array( $custom_layouts ) && ! empty( $custom_layouts ) ) {
				$admin_bar->add_group(
					array(
						'id'     => 'ast_custom_layouts_group',
						'parent' => 'astra-advanced-layouts',
					)
				);
				$admin_bar->add_node(
					array(
						'id'     => 'custom-layout-title',
						'parent' => 'ast_custom_layouts_group',
						'title'  => esc_html__( 'Edit Custom Layout', 'astra-addon' ),
					)
				);

				// Add dynamic layouts assigned on current location.
				foreach ( $custom_layouts as $post_id => $post_data ) {
					$post_type = get_post_type();
					if ( ASTRA_ADVANCED_HOOKS_POST_TYPE != $post_type ) {
						$layout_title = get_the_title( $post_id );
						$admin_bar->add_node(
							array(
								'id'     => 'edit-custom-layout-' . esc_attr( $post_id ),
								'href'   => esc_url( get_edit_post_link( $post_id ) ),
								'title'  => esc_attr( $layout_title ),
								'parent' => 'ast_custom_layouts_group',
							)
						);
					}
				}
			}

			if ( is_array( $page_headers ) && ! empty( $page_headers ) ) {
				$admin_bar->add_group(
					array(
						'id'     => 'ast_page_headers_group',
						'parent' => 'astra-advanced-layouts',
					)
				);
				$admin_bar->add_node(
					array(
						'id'     => 'page-headers-title',
						'parent' => 'ast_page_headers_group',
						'title'  => esc_html__( 'Edit Page Header', 'astra-addon' ),
					)
				);

				// Add dynamic headers assigned on current location.
				foreach ( $page_headers as $post_id => $post_data ) {
					$post_type = get_post_type();
					if ( 'astra_adv_header' != $post_type ) {
						$layout_title = get_the_title( $post_id );
						$admin_bar->add_node(
							array(
								'id'     => 'edit-page-header-' . esc_attr( $post_id ),
								'href'   => esc_url( get_edit_post_link( $post_id ) ),
								'title'  => esc_attr( $layout_title ),
								'parent' => 'ast_page_headers_group',
							)
						);
					}
				}
			}

			if ( $has_shortcode ) {
				$pattern = get_shortcode_regex( array( 'astra_custom_layout' ) );

				if ( preg_match_all( '/' . $pattern . '/s', $current_post->post_content, $matches ) ) {
					$output = array();

					foreach ( $matches[0] as $key => $value ) {
						$as_string = str_replace( ' ', '&', trim( $matches[3][ $key ] ) ); // $matches[3] return the shortcode attribute as string & replace space with '&' for parse_str() function.
						$as_string = str_replace( '"', '', $as_string );
						parse_str( $as_string, $sub_attrs );

						$output[] = $sub_attrs; // Get all shortcode attribute keys.
					}

					if ( ! empty( $output ) ) {
						$admin_bar->add_group(
							array(
								'id'     => 'ast_cl_shortcode_group',
								'parent' => 'astra-advanced-layouts',
							)
						);
						$admin_bar->add_node(
							array(
								'id'     => 'cl-shortcode-title',
								'parent' => 'ast_cl_shortcode_group',
								'title'  => esc_html__( 'Edit Shortcode Layouts', 'astra-addon' ),
							)
						);
						foreach ( $output as $key => $value ) {
							foreach ( $value as $attr_key => $attr_val ) {
								$cl_layout_id = absint( $attr_val );
								$layout_title = get_the_title( $cl_layout_id );
								$admin_bar->add_node(
									array(
										'id'     => 'edit-cl-shortcode-layout-' . esc_attr( $cl_layout_id ),
										'href'   => esc_url( get_edit_post_link( $cl_layout_id ) ),
										'title'  => esc_attr( $layout_title ),
										'parent' => 'ast_cl_shortcode_group',
									)
								);
							}
						}
					}
				}
			}
		}
	}

	/**
	 * Print style.
	 *
	 * Adds custom CSS to the HEAD html tag. The CSS for admin bar Astra's trigger.
	 *
	 * Fired by `wp_head` filter.
	 *
	 * @since 4.0.0
	 */
	public function print_style() {
		$branding_logo = 'data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iMTQiIGhlaWdodD0iMTQiIHZpZXdCb3g9IjAgMCAxOCAxOCIgZmlsbD0iI2E3YWFhZCIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj4KPHBhdGggZmlsbC1ydWxlPSJldmVub2RkIiBjbGlwLXJ1bGU9ImV2ZW5vZGQiIGQ9Ik05IDE4QzEzLjk3MDcgMTggMTggMTMuOTcwNyAxOCA5QzE4IDQuMDI5MyAxMy45NzA3IDAgOSAwQzQuMDI5MyAwIDAgNC4wMjkzIDAgOUMwIDEzLjk3MDcgNC4wMjkzIDE4IDkgMThaTTQgMTIuOTk4TDguMzk2IDRMOS40NDE0MSA2LjAzMTI1TDUuODgzNzkgMTIuOTk4SDRaTTguNTM0NjcgMTEuMzc1TDEwLjM0OTEgNy43MjA3TDEzIDEzSDEwLjk3NzFMMTAuMjc5MyAxMS40NDM0SDguNTM0NjdIOC41TDguNTM0NjcgMTEuMzc1WiIgZmlsbD0iI2E3YWFhZCIvPgo8L3N2Zz4K';

		if ( false !== Astra_Ext_White_Label_Markup::get_whitelabel_string( 'astra', 'icon' ) ) {
			$branding_logo = Astra_Ext_White_Label_Markup::get_whitelabel_string( 'astra', 'icon' );
		}

		?>
			<style>
				#wp-admin-bar-astra-advanced-layouts .astra-admin-logo {
					float: left;
					width: 20px;
					height: 100%;
					cursor: pointer;
					background-repeat: no-repeat;
					background-position: center;
					background-size: 16px auto;
					color: #a7aaad;
					background-image: url( <?php echo esc_attr( $branding_logo ); ?> );
				}
				#wpadminbar .quicklinks #wp-admin-bar-astra-advanced-layouts .ab-empty-item {
					padding: 0 5px;
				}
				#wpadminbar #wp-admin-bar-astra-advanced-layouts .ab-submenu {
					padding: 5px 10px;
				}
				#wpadminbar .quicklinks #wp-admin-bar-astra-advanced-layouts li {
					clear: both;
				}
				#wp-admin-bar-ast_page_headers_group:before {
					border-bottom: 1px solid hsla(0,0%,100%,.2);
					display: block;
					float: left;
					content: "";
					margin-bottom: 10px;
					width: 100%;
				}
				#wpadminbar #wp-admin-bar-ast_custom_layouts_group li a:before,
				#wpadminbar #wp-admin-bar-ast_cl_shortcode_group li a:before,
				#wpadminbar #wp-admin-bar-ast_page_headers_group li a:before {
					content: "\21B3";
					margin-right: 0.5em;
					opacity: 0.5;
					font-size: 13px;
				}
			</style>
		<?php
	}
}

new Astra_Ext_Model();

