<?php
/**
 * Premium Addons Base Skin Style.
 */

namespace PremiumAddons\Modules\Woocommerce\TemplateBlocks;

use Elementor\Group_Control_Image_Size;
use Elementor\Utils;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // If this file is called directly, abort.
}

/**
 * Class Skin_Style
 */
abstract class Skin_Style {

	/**
	 * Query object
	 *
	 * @since 4.7.0
	 * @var object $query
	 */
	public static $query;

	/**
	 * Query args
	 *
	 * @since 4.9.14
	 * @var object $query_args
	 */
	public static $query_args;

	/**
	 * Settings
	 *
	 * @since 4.7.0
	 * @var object $settings
	 */
	public static $settings;

	/**
	 * Skin
	 *
	 * @since 4.7.0
	 * @var object $skin
	 */
	public static $skin;

	/**
	 * Node ID of element
	 *
	 * @since 4.7.0
	 * @var object $node_id
	 */
	public static $node_id;

	/**
	 * Rendered Settings
	 *
	 * @since 4.7.0
	 * @var object $_render_attributes
	 */
	public $_render_attributes;

	/**
	 * Change pagination arguments based on settings.
	 *
	 * @since 4.7.0
	 * @access protected
	 * @param string $located location.
	 * @param string $template_name template name.
	 * @param array  $args arguments.
	 * @param string $template_path path.
	 * @param string $default_path default path.
	 * @return string template location
	 */
	public function woo_pagination_template( $located, $template_name, $args, $template_path, $default_path ) {

		if ( 'loop/pagination.php' === $template_name ) {
			$located = PREMIUM_ADDONS_PATH . 'modules/woocommerce/templates/loop/pagination.php';
		}

		return $located;
	}

	/**
	 * Change pagination arguments based on settings.
	 *
	 * @since 4.7.0
	 * @access protected
	 * @param array $args pagination args.
	 * @return array
	 */
	public function get_pagination_args( $args ) {

		$settings = self::$settings;

		$pagination_arrow = false;

		if ( 'numbers_arrow' === $settings['pagination_type'] ) {
			$pagination_arrow = true;
		}

		$args['prev_next'] = $pagination_arrow;

		if ( '' !== $settings['prev_string'] ) {
			$args['prev_text'] = $settings['prev_string'];
		}

		if ( '' !== $settings['next_string'] ) {
			$args['next_text'] = $settings['next_string'];
		}

		return $args;
	}

	/**
	 * Get Wrapper Classes.
	 *
	 * @since 4.7.0
	 * @access public
	 */
	public function set_slider_attr() {

		$settings = self::$settings;

		if ( 'carousel' !== $settings['layout_type'] ) {
			return;
		}

		$is_rtl = is_rtl();
		$dots   = 'yes' === $settings['dots'] ? true : false;
		$arrows = 'yes' === $settings['arrows'] ? true : false;

		$slick_options = array(
			'slidesToShow'   => ( $settings['products_show'] ) ? absint( $settings['products_show'] ) : 4,
			'slidesToScroll' => ( $settings['products_on_scroll'] ) ? absint( $settings['products_on_scroll'] ) : 1,
			'autoplaySpeed'  => ( $settings['autoplay_speed'] ) ? absint( $settings['autoplay_speed'] ) : 5000,
			'autoplay'       => ( 'yes' === $settings['autoplay_slides'] ),
			'infinite'       => ( 'yes' === $settings['infinite_loop'] ),
			'pauseOnHover'   => ( 'yes' === $settings['hover_pause'] ),
			'speed'          => ( $settings['speed'] ) ? absint( $settings['speed'] ) : 500,
			'arrows'         => $arrows,
			'dots'           => $dots,
			'rtl'            => $is_rtl,
			'prevArrow'      => '<a type="button" data-role="none" class="carousel-arrow carousel-prev" aria-label="Next" role="button" style=""><i class="fas fa-angle-left" aria-hidden="true"></i></a>',
			'nextArrow'      => '<a type="button" data-role="none" class="carousel-arrow carousel-next" aria-label="Next" role="button" style=""><i class="fas fa-angle-right" aria-hidden="true"></i></a>',
		);

		if ( $settings['products_show_tablet'] || $settings['products_show_mobile'] ) {

			$slick_options['responsive'] = array();

			if ( $settings['products_show_tablet'] ) {

				$tablet_show   = absint( $settings['products_show_tablet'] );
				$tablet_scroll = ( $settings['products_on_scroll_tablet'] ) ? absint( $settings['products_on_scroll_tablet'] ) : $tablet_show;

				$slick_options['responsive'][] = array(
					'breakpoint' => 1024,
					'settings'   => array(
						'slidesToShow'   => $tablet_show,
						'slidesToScroll' => $tablet_scroll,
					),
				);
			}

			if ( $settings['products_show_mobile'] ) {

				$mobile_show   = absint( $settings['products_show_mobile'] );
				$mobile_scroll = ( $settings['products_on_scroll_mobile'] ) ? absint( $settings['products_on_scroll_mobile'] ) : $mobile_show;

				$slick_options['responsive'][] = array(
					'breakpoint' => 767,
					'settings'   => array(
						'slidesToShow'   => $mobile_show,
						'slidesToScroll' => $mobile_scroll,
					),
				);
			}
		}

		$this->add_render_attribute(
			'wrapper',
			array(
				'class'             => 'premium-carousel-hidden',
				'data-woo_carousel' => wp_json_encode( $slick_options ),
			)
		);
	}

	/**
	 * Render Query.
	 *
	 * @since 1.1.0
	 */
	public function render_query( $ajax = false ) {

		$this->query_posts( $ajax );
	}

	/**
	 * Get query products based on settings.
	 *
	 * Written in PHP and used to generate the final HTML.
	 *
	 * @since 4.7.0
	 * @access public
	 */
	public function query_posts( $ajax ) {

		$settings = self::$settings;

		if ( 'main' === $settings['query_type'] ) {

			if ( $ajax ) {

				$query_args = array(
					'post_type'      => 'product',
					'post_status'    => 'publish',
					'posts_per_page' => -1,
					'paged'          => 1,
				);

				if ( $settings['products_numbers'] > 0 ) {
					$query_args['posts_per_page'] = $settings['products_numbers'];
				}

				$paged = ( get_query_var( 'paged' ) ) ? get_query_var( 'paged' ) : '1';

				$nonce = isset( $_POST['nonce'] ) ? sanitize_text_field( wp_unslash( $_POST['nonce'] ) ) : false;

				$orderby = 'menu_order title';

				if ( $nonce && wp_verify_nonce( $nonce, 'pa-woo-products-nonce' ) ) {
					if ( isset( $_POST['page_number'] ) && '' !== $_POST['page_number'] ) {
						$paged = sanitize_text_field( wp_unslash( $_POST['page_number'] ) );
					}

					if ( isset( $_POST['orderBy'] ) && '' !== $_POST['orderBy'] ) {
						$orderby = sanitize_text_field( wp_unslash( $_POST['orderBy'] ) );
					}
				}

				$query_args['paged']   = $paged;
				$query_args['orderby'] = $orderby;

				if ( isset( $_POST['category'] ) && '' !== $_POST['category'] ) {
					$query_args['product_cat'] = sanitize_text_field( wp_unslash( $_POST['category'] ) );
				}

				$query_args['order'] = 'ASC';

				self::$query_args = $query_args;

				self::$query = new \WP_Query( $query_args );

			} else {

				global $wp_query;

				$main_query = clone $wp_query;

				self::$query = $main_query;

				self::$query_args = $main_query->query_vars;

			}
		} elseif ( 'related' === $settings['query_type'] ) {

			if ( is_product() ) {

				global $product;

				$product_id                  = $product->get_id();
				$product_visibility_term_ids = wc_get_product_visibility_term_ids();

				$query_args = array(
					'post_type'      => 'product',
					'post_status'    => 'publish',
					'posts_per_page' => -1,
					'paged'          => 1,
					'post__not_in'   => array(),
				);

				if ( 'grid' === $settings['layout_type'] || 'metro' === $settings['layout_type'] ) {

					if ( $settings['products_numbers'] > 0 ) {
						$query_args['posts_per_page'] = $settings['products_numbers'];
					}

					if ( 'yes' === $settings['pagination'] || 'yes' === $settings['load_more'] ) {

						$paged = ( get_query_var( 'paged' ) ) ? get_query_var( 'paged' ) : '1';

						$nonce = isset( $_POST['nonce'] ) ? sanitize_text_field( wp_unslash( $_POST['nonce'] ) ) : false;

						if ( $nonce && wp_verify_nonce( $nonce, 'pa-woo-products-nonce' ) ) {
							if ( isset( $_POST['page_number'] ) && '' !== $_POST['page_number'] ) {
								$paged = sanitize_text_field( wp_unslash( $_POST['page_number'] ) );
							}
						}

						$query_args['paged'] = $paged;
					}
				} else {

					if ( $settings['total_carousel_products'] > 0 ) {
						$query_args['posts_per_page'] = $settings['total_carousel_products'];
					}
				}

				// Get current post categories and pass to filter.
				$product_cat = array();

				$product_categories = wp_get_post_terms( $product_id, 'product_cat' );

				if ( ! empty( $product_categories ) ) {

					foreach ( $product_categories as $key => $category ) {

						$product_cat[] = $category->slug;
					}
				}

				if ( ! empty( $product_cat ) ) {

					$query_args['tax_query'][] = array(
						'taxonomy' => 'product_cat',
						'field'    => 'slug',
						'terms'    => $product_cat,
						'operator' => 'IN',
					);
				}

				// Exclude current product.
				$query_args['post__not_in'][] = $product_id;

				if ( 'yes' === get_option( 'woocommerce_hide_out_of_stock_items' ) ) {

					$query_args['tax_query'][] = array(
						'taxonomy' => 'product_visibility',
						'field'    => 'term_taxonomy_id',
						'terms'    => $product_visibility_term_ids['outofstock'],
						'operator' => 'NOT IN',
					);
				}

				if ( ! empty( $product_visibility_term_ids['exclude-from-catalog'] ) ) {

					$query_args['tax_query'][] = array(
						'taxonomy' => 'product_visibility',
						'field'    => 'term_taxonomy_id',
						'terms'    => $product_visibility_term_ids['exclude-from-catalog'],
						'operator' => 'NOT IN',
					);
				}

				$query_args = apply_filters( 'premium_woo_products_query_args', $query_args, $settings );

				self::$query = new \WP_Query( $query_args );

			} else {

				$query_args = array(
					'post_type'      => 'product',
					'post_status'    => 'publish',
					'posts_per_page' => -1,
					'paged'          => 1,
					'post__in'       => array( 0 ),
				);

				$query_args = apply_filters( 'premium_woo_products_query_args', $query_args, $settings );

				self::$query = new \WP_Query( $query_args );
			}
		} elseif ( 'cross-sells' === $settings['query_type'] ) {

			$cross_sells_ids = $this->get_cross_sells_ids();

			$product_visibility_term_ids = wc_get_product_visibility_term_ids();

			if ( ! $cross_sells_ids ) {
				$cross_sells_ids = array( 0 );
			}

			$query_args = array(
				'post_type'      => 'product',
				'post_status'    => 'publish',
				'posts_per_page' => -1,
				'paged'          => 1,
				'post__in'       => $cross_sells_ids,
			);

			/**
			 * Filters.
			 */

			// carousel.
			if ( 'grid' === $settings['layout_type'] || 'metro' === $settings['layout_type'] ) {

				if ( $settings['products_numbers'] > 0 ) {
					$query_args['posts_per_page'] = $settings['products_numbers'];
				}
			} else {

				if ( $settings['total_carousel_products'] > 0 ) {
					$query_args['posts_per_page'] = $settings['total_carousel_products'];
				}
			}

			// Default ordering args.
			$ordering_args = WC()->query->get_catalog_ordering_args( $settings['orderby'], $settings['order'] );

			$query_args['orderby'] = $ordering_args['orderby'];
			$query_args['order']   = $ordering_args['order'];

			if ( 'yes' === get_option( 'woocommerce_hide_out_of_stock_items' ) ) {

				$query_args['tax_query'][] = array(
					'taxonomy' => 'product_visibility',
					'field'    => 'term_taxonomy_id',
					'terms'    => $product_visibility_term_ids['outofstock'],
					'operator' => 'NOT IN',
				);
			}

			if ( ! empty( $product_visibility_term_ids['exclude-from-catalog'] ) ) {

				$query_args['tax_query'][] = array(
					'taxonomy' => 'product_visibility',
					'field'    => 'term_taxonomy_id',
					'terms'    => $product_visibility_term_ids['exclude-from-catalog'],
					'operator' => 'NOT IN',
				);
			}

			$query_args = apply_filters( 'premium_woo_products_query_args', $query_args, $settings );

			self::$query = new \WP_Query( $query_args );

		} elseif ( 'up-sells' === $settings['query_type'] ) {

			/**
			 * Up-sells are products that you recommend instead of the currently viewed product.
			 */
			if ( is_product() ) {

				global $product;

				$product_upsell = 0 === count( $product->get_upsell_ids() ) ? array( 0 ) : $product->get_upsell_ids();

				$product_visibility_term_ids = wc_get_product_visibility_term_ids();

				$query_args = array(
					'post_type'      => 'product',
					'post_status'    => 'publish',
					'posts_per_page' => -1,
					'paged'          => 1,
					'post__in'       => $product_upsell,
				);

				/**
				 * Filters.
				 */

				// carousel.
				if ( 'grid' === $settings['layout_type'] || 'metro' === $settings['layout_type'] ) {

					if ( $settings['products_numbers'] > 0 ) {
						$query_args['posts_per_page'] = $settings['products_numbers'];
					}
				} else {

					if ( $settings['total_carousel_products'] > 0 ) {
						$query_args['posts_per_page'] = $settings['total_carousel_products'];
					}
				}

				// Default ordering args.
				$ordering_args = WC()->query->get_catalog_ordering_args( $settings['orderby'], $settings['order'] );

				$query_args['orderby'] = $ordering_args['orderby'];
				$query_args['order']   = $ordering_args['order'];

				if ( 'yes' === get_option( 'woocommerce_hide_out_of_stock_items' ) ) {

					$query_args['tax_query'][] = array(
						'taxonomy' => 'product_visibility',
						'field'    => 'term_taxonomy_id',
						'terms'    => $product_visibility_term_ids['outofstock'],
						'operator' => 'NOT IN',
					);
				}

				if ( ! empty( $product_visibility_term_ids['exclude-from-catalog'] ) ) {

					$query_args['tax_query'][] = array(
						'taxonomy' => 'product_visibility',
						'field'    => 'term_taxonomy_id',
						'terms'    => $product_visibility_term_ids['exclude-from-catalog'],
						'operator' => 'NOT IN',
					);
				}

				$query_args = apply_filters( 'premium_woo_products_query_args', $query_args, $settings );

				self::$query = new \WP_Query( $query_args );

			} else {

				$query_args = array(
					'post_type'      => 'product',
					'post_status'    => 'publish',
					'posts_per_page' => -1,
					'paged'          => 1,
					'post__in'       => array( 0 ),
				);

				$query_args = apply_filters( 'premium_woo_products_query_args', $query_args, $settings );

				self::$query = new \WP_Query( $query_args );
			}
		} else {

			global $post;

			$product_visibility_term_ids = wc_get_product_visibility_term_ids();

			$query_args = array(
				'post_type'      => 'product',
				'post_status'    => 'publish',
				'posts_per_page' => -1,
				'paged'          => 1,
				'post__not_in'   => array(),
			);

			if ( 'grid' === $settings['layout_type'] || 'metro' === $settings['layout_type'] ) {

				if ( $settings['products_numbers'] > 0 ) {
					$query_args['posts_per_page'] = $settings['products_numbers'];
				}

				if ( 'yes' === $settings['pagination'] || 'yes' === $settings['load_more'] ) {

					$paged = ( get_query_var( 'paged' ) ) ? get_query_var( 'paged' ) : '1';

					$nonce = isset( $_POST['nonce'] ) ? sanitize_text_field( wp_unslash( $_POST['nonce'] ) ) : false;

					if ( $nonce && wp_verify_nonce( $nonce, 'pa-woo-products-nonce' ) ) {
						if ( isset( $_POST['page_number'] ) && '' !== $_POST['page_number'] ) {
							$paged = sanitize_text_field( wp_unslash( $_POST['page_number'] ) );
						}
					}

					$query_args['paged'] = $paged;
				}
			} else {

				if ( $settings['total_carousel_products'] > 0 ) {
					$query_args['posts_per_page'] = $settings['total_carousel_products'];
				}
			}

			// Default ordering args.
			$ordering_args = WC()->query->get_catalog_ordering_args( $settings['orderby'], $settings['order'] );

			$query_args['orderby'] = $ordering_args['orderby'];
			$query_args['order']   = $ordering_args['order'];

			if ( $ordering_args['meta_key'] ) {
				$query_args['meta_key'] = $ordering_args['meta_key'];
			}

			if ( 'sale' === $settings['filter_by'] ) {

				$query_args['post__in'] = array_merge( array( 0 ), wc_get_product_ids_on_sale() );
			} elseif ( 'featured' === $settings['filter_by'] ) {

				$query_args['tax_query'][] = array(
					'taxonomy' => 'product_visibility',
					'field'    => 'term_taxonomy_id',
					'terms'    => $product_visibility_term_ids['featured'],
				);
			}

			if ( 'custom' === $settings['query_type'] ) {

				if ( ! empty( $settings['categories'] ) ) {

					$cat_rule = $settings['categories_filter_rule'];

					$query_args['tax_query'][] = array(
						'taxonomy' => 'product_cat',
						'field'    => 'slug',
						'terms'    => $settings['categories'],
						'operator' => $cat_rule,
					);
				}

				if ( ! empty( $settings['tags'] ) ) {

					$tag_rule = $settings['tags_filter_rule'];

					$query_args['tax_query'][] = array(
						'taxonomy' => 'product_tag',
						'field'    => 'slug',
						'terms'    => $settings['tags'],
						'operator' => $tag_rule,
					);
				}

				if ( 0 < $settings['offset'] ) {

					$query_args['offset_to_fix'] = $settings['offset'];
				}
			}

			if ( 'manual' === $settings['query_type'] ) {

				$manual_ids = $settings['query_manual_ids'];

				$query_args['post__in'] = $manual_ids;
			}

			if ( 'manual' !== $settings['query_type'] && 'main' !== $settings['query_type'] ) {

				// if ( '' !== $settings['exclude_products'] ) {

				// $exclude_ids = $settings['exclude_products'];

				// $query_args['post__not_in'] = $exclude_ids;
				// }

				if ( 'yes' === $settings['exclude_current_product'] ) {

					$query_args['post__not_in'][] = $post->ID;
				}
			}

			if ( 'yes' === get_option( 'woocommerce_hide_out_of_stock_items' ) ) {

				$query_args['tax_query'][] = array(
					'taxonomy' => 'product_visibility',
					'field'    => 'term_taxonomy_id',
					'terms'    => $product_visibility_term_ids['outofstock'],
					'operator' => 'NOT IN',
				);
			}

			if ( ! empty( $product_visibility_term_ids['exclude-from-catalog'] ) ) {

				$query_args['tax_query'][] = array(
					'taxonomy' => 'product_visibility',
					'field'    => 'term_taxonomy_id',
					'terms'    => $product_visibility_term_ids['exclude-from-catalog'],
					'operator' => 'NOT IN',
				);
			}

			$query_args = apply_filters( 'premium_woo_products_query_args', $query_args, $settings );

			self::$query_args = $query_args;

			self::$query = new \WP_Query( $query_args );

		}
	}

	/**
	 * Get cross-sells products' ids.
	 * returns single product's cross-sells, or return all the cross-sell products based on cart items.
	 * Cross-sells are products that you promote in the cart, based on the current product.
	 *
	 * @access public
	 * @since 4.9.24
	 *
	 * @return array
	 */
	public function get_cross_sells_ids() {

		$cross_sells_ids = array();

		if ( is_product() ) {

			global $product;

			$cross_sells_ids = $product->get_cross_sell_ids();

		} else {

			$cart = WC()->cart;

			if ( ! $cart ) {
				return false;
			}

			$cross_sells_ids = WC()->cart->get_cross_sells();
		}

		$cross_sells_ids = 0 === count( $cross_sells_ids ) ? false : $cross_sells_ids;

		return $cross_sells_ids;
	}

	/**
	 * Render loop required arguments.
	 *
	 * @since 1.1.0
	 */
	public function set_query_args() {

		$query = $this->get_query();

		global $woocommerce_loop;

		$settings = self::$settings;

		if ( 'grid' === $settings['layout_type'] || 'metro' === $settings['layout_type'] ) {

			if ( 'grid' === $settings['layout_type'] ) {

				$woocommerce_loop['columns'] = intval( 100 / substr( $settings['columns'], 0, strpos( $settings['columns'], '%' ) ) );

				if ( '16.667%' === $settings['columns'] ) {
					$woocommerce_loop['columns'] = 6;
				}
			}

			// if ( 'main' !== $settings['query_type'] ) {
			if ( 0 < $settings['products_numbers'] && '' !== $settings['pagination'] ) {
				/* Pagination */
				$paged = ( get_query_var( 'paged' ) ) ? get_query_var( 'paged' ) : 1;

				$nonce = isset( $_POST['nonce'] ) ? sanitize_text_field( wp_unslash( $_POST['nonce'] ) ) : false;

				if ( $nonce && wp_verify_nonce( $nonce, 'pa-woo-products-nonce' ) ) {
					if ( isset( $_POST['page_number'] ) && '' !== $_POST['page_number'] ) {
						$paged = sanitize_text_field( wp_unslash( $_POST['page_number'] ) );
					}
				}

				$woocommerce_loop['paged']        = $paged;
				$woocommerce_loop['total']        = $query->found_posts;
				$woocommerce_loop['post_count']   = $query->post_count;
				$woocommerce_loop['per_page']     = $settings['products_numbers'];
				$woocommerce_loop['total_pages']  = ceil( $query->found_posts / $settings['products_numbers'] );
				$woocommerce_loop['current_page'] = $paged;
			}

			$divider = $this->get_option_value( 'divider' );

			if ( 'yes' === $divider && 'grid' === $settings['layout_type'] ) {
				$this->add_render_attribute( 'wrapper', 'class', 'premium-woo-grid-' . $woocommerce_loop['columns'] );
			} else {
				$this->add_render_attribute( 'wrapper', 'class', 'premium-woo-metro-' . $settings['metro_style'] );
			}

			// }

			// } else {
			// if ( in_array( $settings['navigation'], array( 'dots', 'both' ), true ) ) {

			// $this->add_render_attribute(
			// 'inner',
			// array(
			// 'class' => array(
			// 'premium-addons-for-elementor-slick-dotted',
			// ),
			// )
			// );
			// }
		}
	}

	/**
	 * Pagination Structure.
	 *
	 * @since 1.1.0
	 */
	public function render_pagination_structure() {

		$settings          = self::$settings;
		$is_recommendation = in_array( $settings['query_type'], array( 'cross-sells', 'up- sells' ), true ) ? true : false;

		if ( 'yes' === $settings['pagination'] && ! $is_recommendation ) {
			add_filter( 'wc_get_template', array( $this, 'woo_pagination_template' ), 10, 5 );
			add_filter( 'premium_woo_pagination_args', array( $this, 'get_pagination_args' ) );

			woocommerce_pagination();

			remove_filter( 'premium_woo_pagination_args', array( $this, 'get_pagination_args' ) );
			remove_filter( 'wc_get_template', array( $this, 'woo_pagination_template' ), 10, 5 );
		}
	}

	/**
	 * Render Load More Button
	 *
	 * @since 4.9.11
	 */
	public function render_load_more_button() {

		$settings = self::$settings;

		if ( 'yes' !== $settings['load_more'] || 'grid' !== $settings['layout_type'] ) {
			return;
		}

		$posts_per_page = self::$query_args['posts_per_page'];

		$args    = self::$query_args;
		$orderby = $args['orderby'];

		if ( 'main' === $settings['query_type'] ) {

			$args = array(
				'post_type'   => 'product',
				'product_cat' => $args['product_cat'],
			);

		}

		$args['posts_per_page'] = -1;

		$all_products = new \WP_Query( $args );

		if ( ! isset( $all_products->found_posts ) ) {
			return;
		}

		$more_products = $all_products->found_posts - $posts_per_page;

		$category = isset( $args['product_cat'] ) && ! empty( $args['product_cat'] ) ? $args['product_cat'] : '';

		if ( $more_products < 1 ) {
			return;
		}

		?>
			<div class="premium-woo-load-more">
				<button class="premium-woo-load-more-btn" data-products="<?php echo esc_attr( $more_products ); ?>" data-order="<?php echo esc_attr( $orderby ); ?>" data-tax="<?php echo esc_attr( $category ); ?>">
					<span><?php echo wp_kses_post( $settings['load_more_text'] ); ?></span>
					<span class="premium-woo-products-num">(<?php echo wp_kses_post( $more_products ); ?>)</span>
				</button>
			</div>
		<?php

	}

	/**
	 * Render wrapper start.
	 *
	 * @since 1.1.0
	 */
	public function start_loop_wrapper() {

		$settings = self::$settings;

		$quick_view = $this->get_option_value( 'quick_view' );

		$skin_slug = str_replace( '_', '-', self::$skin );

		$page_id = 0;

		if ( null !== \Elementor\Plugin::$instance->documents->get_current() ) {
			$page_id = \Elementor\Plugin::$instance->documents->get_current()->get_main_id();
		}

		$this->set_slider_attr();

		$this->add_render_attribute(
			'wrapper',
			array(
				'class'           => array(
					'premium-woocommerce',
					'premium-woo-products-' . $settings['layout_type'],
					'premium-woo-skin-' . $skin_slug,
					'premium-woo-query-' . $settings['query_type'],
				),
				'data-page-id'    => $page_id,
				'data-skin'       => self::$skin,
				'data-quick-view' => $quick_view,
			)
		);

		if ( 'metro' === $settings['layout_type'] ) {

			$this->add_render_attribute( 'wrapper', 'data-metro-style', $settings['metro_style'] );

			if ( 'custom' === $settings['metro_style'] ) {

				$metro_settings = array(
					'wPattern'        => $settings['width_pattern'],
					'hPattern'        => $settings['height_pattern'],
					'wPattern_tablet' => $settings['width_pattern_tablet'],
					'hPattern_tablet' => $settings['height_pattern_tablet'],
					'wPattern_mobile' => $settings['width_pattern_mobile'],
					'hPattern_mobile' => $settings['height_pattern_mobile'],
				);

				// $metro_settings = [
				// 'scaled'        => $settings['scaled_products'],
				// 'scaledCellsW'  => $settings['scaled_cells_width'],
				// 'scaledCellsH'  => $settings['scaled_cells_height'],
				// 'normalCellsW'  => $settings['normal_cells_width'],
				// 'normalCellsH'  => $settings['normal_cells_height']
				// ];

				$this->add_render_attribute( 'wrapper', 'data-metro', wp_json_encode( $metro_settings ) );
			}
		}

		echo '<div ' . wp_kses_post( $this->get_render_attribute_string( 'wrapper' ) ) . '>';
	}

	/**
	 * Render wrapper end.
	 *
	 * @since 1.1.0
	 */
	public function end_loop_wrapper() {
		echo '</div>';
	}

	/**
	 * Render inner container start.
	 *
	 * @since 1.1.0
	 */
	public function start_loop_inner() {

		$settings = self::$settings;

		$this->add_render_attribute(
			'inner',
			array(
				'class' => array(
					'premium-woo-products-inner',
				),
			)
		);

		if ( '' !== $settings['hover_style'] ) {
			$this->add_render_attribute(
				'inner',
				array(
					'class' => array(
						'premium-woo-product__hover-' . $settings['hover_style'],
					),
				)
			);
		}

		echo '<div ' . wp_kses_post( $this->get_render_attribute_string( 'inner' ) ) . '>';
	}

	/**
	 * Render inner container end.
	 *
	 * @since 1.1.0
	 */
	public function end_loop_inner() {
		echo '</div>';
	}

	/**
	 * Render woo loop.
	 *
	 * @since 1.1.0
	 */
	public function render_woo_products() {

		$query = $this->get_query();

		woocommerce_product_loop_start();

		while ( $query->have_posts() ) :
			$query->the_post();
			$this->render_product_template();
		endwhile;

		woocommerce_product_loop_end();
	}

	/**
	 * Render reset loop.
	 *
	 * @since 1.1.0
	 */
	public function render_reset_loop() {

		woocommerce_reset_loop();

		wp_reset_postdata();
	}

	public function render_product_template() {

		$settings = self::$settings;

		include PREMIUM_ADDONS_PATH . 'modules/woocommerce/templates/product-1.php';
	}

	/**
	 * Quick View.
	 *
	 * @since 4.7.0
	 * @access public
	 */
	public function quick_view_modal() {

		$quick_view = $this->get_option_value( 'quick_view' );

		if ( 'yes' === $quick_view ) {
			wp_enqueue_script( 'wc-add-to-cart-variation' );
			// wp_enqueue_script( 'flexslider' );

			$widget_id = self::$node_id;

			include PREMIUM_ADDONS_PATH . 'modules/woocommerce/templates/quick-view-modal.php';
		}
	}

	/**
	 * Register Get Query.
	 *
	 * @since 4.7.0
	 * @access protected
	 */
	public function get_query() {
		return self::$query;
	}

	/**
	 * Get empty products found message.
	 *
	 * Returns the no products found message HTML.
	 *
	 * @since 1.10.0
	 * @access public
	 */
	public function render_empty() {
		$settings = self::$settings;
		?>
		<div class="premium-woo-empty">
			<p><?php echo esc_html( $settings['empty_products_msg'] ); ?></p>
		</div>
		<?php
	}

	/**
	 * Render output on the frontend.
	 *
	 * Written in PHP and used to generate the final HTML.
	 *
	 * @param string $style Skin ID.
	 * @param array  $settings Settings Object.
	 * @param string $node_id Node ID.
	 * @since 4.7.0
	 * @access public
	 */
	public function render( $style, $settings, $node_id ) {

		self::$settings = $settings;
		self::$skin     = str_replace( '-', '_', $style );
		self::$node_id  = $node_id;

		$this->render_query();

		$query = self::$query;

		if ( ! $query->have_posts() ) {
			$this->render_empty();
			return;
		}

		$this->set_query_args();

		$this->start_loop_wrapper();

		$this->start_loop_inner();

		$this->render_woo_products();

		$this->render_pagination_structure();

		$this->render_load_more_button();

		$this->render_reset_loop();

		$this->end_loop_inner();

		$this->end_loop_wrapper();

		$this->quick_view_modal();

	}

	/**
	 * Render settings array for selected skin
	 *
	 * @since 4.7.0
	 * @param string $control_base_id Skin ID.
	 * @access public
	 */
	public function get_option_value( $control_id ) {

		$skin_control_id = sprintf( '%s_%s', self::$skin, $control_id );

		if ( isset( self::$settings[ $skin_control_id ] ) ) {
			return self::$settings[ $skin_control_id ];
		} else {
			return null;
		}
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
	 * Render post HTML via AJAX call.
	 *
	 * @param array|string $style_id  The style ID.
	 * @param array|string $widget    Widget object.
	 * @since 4.7.0
	 * @access public
	 */
	public function inner_render( $style_id, $widget, $is_ajax ) {

		ob_start();

		// $category = ( isset( $_POST['category'] ) ) ? $_POST['category'] : '';

		self::$settings = $widget->get_settings();
		self::$skin     = $style_id;

		$this->render_query( $is_ajax );

		$query    = self::$query;
		$settings = self::$settings;

		$this->set_query_args();

		$this->render_woo_products();

		return ob_get_clean();
	}

	/**
	 * Render post pagination HTML via AJAX call.
	 *
	 * @param array|string $style_id  The style ID.
	 * @param array|string $widget    Widget object.
	 * @since 4.7.0
	 * @access public
	 */
	public function page_render( $style_id, $widget ) {

		ob_start();

		// $category = ( isset( $_POST['category'] ) ) ? $_POST['category'] : '';

		self::$settings = $widget->get_settings();
		self::$skin     = $style_id;
		$this->render_query();
		$query       = self::$query;
		$settings    = self::$settings;
		$is_featured = false;

		$this->render_pagination_structure();

		return ob_get_clean();
	}
}
