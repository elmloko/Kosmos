<?php

/**
 * Contains functions responsible for the functionality at the front-end
 *
 * @since      1.0
 *
 */

/**
 * This class defines all the code necessary for functionality at the front-end
 *
 * @since      1.0
 *
 */
class Sassy_Social_Share_Public {

	/**
	 * Options saved in database.
	 *
	 * @since    1.0
	 */
	private $options;

	/**
	 * Current version of the plugin.
	 *
	 * @since    1.0
	 */
	public $version;

	/**
	 * Variable to track number of times 'the_content' hook called at homepage.
	 *
	 * @since    1.0
	 */
	private $vertical_home_count = 0;

	/**
	 * Variable to track number of times 'the_content' hook called at excerpts.
	 *
	 * @since    1.0
	 */
	private $vertical_excerpt_count = 0;

	/**
	 * Short urls calculated for current webpage.
	 *
	 * @since    1.0
	 */
	private $short_urls = array();

	/**
	 * Share Count Transient ID
	 *
	 * @since    1.7
	 */
	public $share_count_transient_id = '';

	/**
	 * Get saved options.
	 *
	 * @since    1.0
     * @param    array     $options    Plugin options saved in database
     * @param    string    $version    Current version of the plugin
	 */
	public function __construct( $options, $version ) {

		$this->options = $options;
		$this->version = $version;

	}

	/**
	 * Hook the plugin function on 'init' event.
	 *
	 * @since    1.0
	 */
	public function init() {

		// Javascript for front-end of website
		add_action( 'wp_enqueue_scripts', array( $this, 'frontend_scripts' ) );
		// inline style for front-end of website
		add_action( 'wp_enqueue_scripts', array( $this, 'frontend_inline_style' ) );
		// stylesheet files for front-end of website
		add_action( 'wp_enqueue_scripts', array( $this, 'frontend_css' ) );

		if ( isset( $this->options['amp_enable'] ) && function_exists( 'is_amp_endpoint' ) ) {
			add_action( 'wp_print_styles', array( $this, 'frontend_amp_css' ) );
        	add_action( 'amp_post_template_css', array( $this, 'frontend_amp_css' ) );
    	}
	
	}

	/**
	 * Javascript files to load at front end.
	 *
	 * @since    1.0
	 */
	public function frontend_scripts() {

		if ( ! ( function_exists( 'is_amp_endpoint' ) && is_amp_endpoint() ) && ! ( function_exists( 'ampforwp_is_amp_endpoint' ) && ampforwp_is_amp_endpoint() ) ) {
			$in_footer = isset( $this->options['footer_script'] ) ? true : false;
			$inline_script = 'function heateorSssLoadEvent(e) {var t=window.onload;if (typeof window.onload!="function") {window.onload=e}else{window.onload=function() {t();e()}}};	';
			global $post;
			if ( $post ) {
				$sharing_meta = get_post_meta( $post->ID, '_heateor_sss_meta', true );
				if ( is_front_page() || ! isset( $sharing_meta['sharing'] ) || $sharing_meta['sharing'] != 1 || ! isset( $sharing_meta['vertical_sharing'] ) || $sharing_meta['vertical_sharing'] != 1 ) {
					if ( ! isset( $this->options['js_when_needed'] ) || ( ( ( isset( $this->options['home'] ) || isset( $this->options['vertical_home'] ) ) && is_front_page() ) || ( ( isset( $this->options['category'] ) || isset($this->options['vertical_category'] ) ) && is_category() ) ||( ( isset( $this->options['archive'] ) || isset( $this->options['vertical_archive'] ) ) && is_archive() ) || ( ( isset( $this->options['post'] ) || isset( $this->options['vertical_post'] ) ) && is_single() && isset( $post->post_type ) && $post->post_type == 'post' ) || ( ( isset( $this->options['page'] ) || isset( $this->options['vertical_page'] ) ) && is_page() && isset( $post->post_type ) && $post->post_type == 'page' ) || ( ( isset( $this->options['excerpt'] ) || isset( $this->options['vertical_excerpt'] ) ) && ( is_home() || current_filter() == 'the_excerpt' ) ) || (  ( isset( $this->options['bb_forum'] ) || isset( $this->options['vertical_bb_forum'] ) ) && current_filter() == 'bbp_template_before_single_forum' ) || ( ( isset( $this->options['bb_topic'] ) || isset( $this->options['vertical_bb_topic'] ) ) && in_array( current_filter(), array( 'bbp_template_before_single_topic', 'bbp_template_before_lead_topic' ) ) ) || ( current_filter() == 'bp_before_group_header' && ( isset( $this->options['bp_group'] ) || isset( $this->options['vertical_bb_group'] ) ) ) ) ) {
						$inline_script .= 'var heateorSssSharingAjaxUrl = \''. get_admin_url() .'admin-ajax.php\', heateorSssCloseIconPath = \''. plugins_url( '../images/close.png', __FILE__ ) .'\', heateorSssPluginIconPath = \''. plugins_url( '../images/logo.png', __FILE__ ) .'\', heateorSssHorizontalSharingCountEnable = '. ( isset( $this->options['hor_enable'] ) && ( isset( $this->options['horizontal_counts'] ) || isset( $this->options['horizontal_total_shares'] ) ) ? 1 : 0 ) .', heateorSssVerticalSharingCountEnable = '. ( isset( $this->options['vertical_enable'] ) && ( isset( $this->options['vertical_counts'] ) || isset( $this->options['vertical_total_shares'] ) ) ? 1 : 0 ) .', heateorSssSharingOffset = '. ( isset( $this->options['alignment'] ) && $this->options['alignment'] != '' && isset( $this->options[$this->options['alignment'].'_offset'] ) && $this->options[$this->options['alignment'].'_offset'] != '' ? $this->options[$this->options['alignment'].'_offset'] : 0 ) . '; var heateorSssMobileStickySharingEnabled = ' . ( isset( $this->options['vertical_enable'] ) && isset( $this->options['bottom_mobile_sharing'] ) && $this->options['horizontal_screen_width'] != '' ? 1 : 0 ) . ';';
						$inline_script .= 'var heateorSssCopyLinkMessage = "' . htmlspecialchars( __( 'Link copied.', 'sassy-social-share' ), ENT_QUOTES ) . '";';
						if ( isset( $this->options['horizontal_counts'] ) && isset( $this->options['horizontal_counter_position'] ) ) {
							$inline_script .= in_array( $this->options['horizontal_counter_position'], array( 'inner_left', 'inner_right' ) ) ? 'var heateorSssReduceHorizontalSvgWidth = true;' : '';
							$inline_script .= in_array( $this->options['horizontal_counter_position'], array( 'inner_top', 'inner_bottom' ) ) ? 'var heateorSssReduceHorizontalSvgHeight = true;' : '';
						}
						if ( isset( $this->options['vertical_counts'] ) ) {
							$inline_script .= isset( $this->options['vertical_counter_position'] ) && in_array( $this->options['vertical_counter_position'], array( 'inner_left', 'inner_right' ) ) ? 'var heateorSssReduceVerticalSvgWidth = true;' : '';
							$inline_script .= ! isset( $this->options['vertical_counter_position'] ) || in_array( $this->options['vertical_counter_position'], array( 'inner_top', 'inner_bottom' ) ) ? 'var heateorSssReduceVerticalSvgHeight = true;' : '';
						}
						$inline_script .= 'var heateorSssUrlCountFetched = [], heateorSssSharesText = \''. htmlspecialchars(__('Shares', 'sassy-social-share'), ENT_QUOTES) .'\', heateorSssShareText = \''. htmlspecialchars(__('Share', 'sassy-social-share'), ENT_QUOTES) .'\';';
						$inline_script .= 'function heateorSssPopup(e) {window.open(e,"popUpWindow","height=400,width=600,left=400,top=100,resizable,scrollbars,toolbar=0,personalbar=0,menubar=no,location=no,directories=no,status")}';
						if ( $this->facebook_like_recommend_enabled() || $this->facebook_share_enabled() ) {
							$inline_script .= 'function heateorSssInitiateFB() {FB.init({appId:"",channelUrl:"",status:!0,cookie:!0,xfbml:!0,version:"v17.0"})}window.fbAsyncInit=function() {heateorSssInitiateFB(),' . ( defined( 'HEATEOR_SOCIAL_SHARE_MYCRED_INTEGRATION_VERSION' ) && $this->facebook_like_recommend_enabled() ? 1 : 0 ) . '&&(FB.Event.subscribe("edge.create",function(e) {heateorSsmiMycredPoints("Facebook_like_recommend","",e?e:"")}),FB.Event.subscribe("edge.remove",function(e) {heateorSsmiMycredPoints("Facebook_like_recommend","",e?e:"","Minus point(s) for undoing Facebook like-recommend")}) ),'. ( defined( 'HEATEOR_SHARING_GOOGLE_ANALYTICS_VERSION' ) ? 1 : 0 ) .'&&(FB.Event.subscribe("edge.create",function(e) {heateorSsgaSocialPluginsTracking("Facebook","Like",e?e:"")}),FB.Event.subscribe("edge.remove",function(e) {heateorSsgaSocialPluginsTracking("Facebook","Unlike",e?e:"")}) )},function(e) {var n,i="facebook-jssdk",o=e.getElementsByTagName("script")[0];e.getElementById(i)||(n=e.createElement("script"),n.id=i,n.async=!0,n.src="//connect.facebook.net/'. ( $this->options['language'] ? $this->options['language'] : 'en_GB' ) .'/sdk.js",o.parentNode.insertBefore(n,o) )}(document);';
						}
						wp_enqueue_script( 'heateor_sss_sharing_js', plugins_url( 'js/sassy-social-share-public.js', __FILE__ ), array( 'jquery' ), $this->version, $in_footer );
						wp_add_inline_script( 'heateor_sss_sharing_js', $inline_script, $position = 'before' );
					}
				}
			}
		}
	}

	/**
	 * Check if webpage is being visited in a mobile device
	 *
	 * @since    3.3.9
	 */
	public function check_if_mobile() {

		if ( isset( $_SERVER['HTTP_USER_AGENT'] ) ) {
			// detect the device for Whatsapp share API
			$iphone = strpos( $_SERVER['HTTP_USER_AGENT'], "iPhone" );
			$android = strpos( $_SERVER['HTTP_USER_AGENT'], "Android" );
			$palmpre = strpos( $_SERVER['HTTP_USER_AGENT'], "webOS" );
			$berry = strpos( $_SERVER['HTTP_USER_AGENT'], "BlackBerry" );
			$ipod = strpos( $_SERVER['HTTP_USER_AGENT'], "iPod" );
			// check if it's a mobile
			if ( $iphone || $android || $palmpre || $ipod || $berry == true ) {
				return true;
			}
		}

		return false;

	}

	/**
	 * Check if Facebook Like/Recommend is enabled
	 *
	 * @since    1.0
	 */
	public function facebook_like_recommend_enabled() {
		
		if ( ( isset( $this->options['hor_enable'] ) && isset( $this->options['horizontal_re_providers'] ) && ( in_array( 'facebook_like', $this->options['horizontal_re_providers'] ) || in_array( 'facebook_recommend', $this->options['horizontal_re_providers'] ) ) ) || ( isset( $this->options['vertical_enable'] ) && isset( $this->options['vertical_re_providers'] ) && ( in_array( 'facebook_like', $this->options['vertical_re_providers'] ) || in_array( 'facebook_recommend', $this->options['vertical_re_providers'] ) ) ) ) {
			return true;
		}

		return false;

	}

	/**
	 * Check if Facebook Share is enabled
	 *
	 * @since    2.4
	 */
	public function facebook_share_enabled() {
		
		if ( ( isset( $this->options['hor_enable'] ) && isset( $this->options['horizontal_re_providers'] ) && ( in_array( 'facebook_share', $this->options['horizontal_re_providers'] ) ) ) || ( isset( $this->options['vertical_enable'] ) && isset( $this->options['vertical_re_providers'] ) && ( in_array( 'facebook_share', $this->options['vertical_re_providers'] ) ) ) ) {
			return true;
		}

		return false;

	}

	/**
	 * Generate bitly short url for sharing buttons
	 *
	 * @since    1.0
	 */
	public function generate_bitly_url( $url, $post_id = 0 ) {
	    
	    $bitlyUrl = get_post_meta( $post_id, '_heateor_sss_bitly_url', true );
	    if ( $bitlyUrl ) {
	    	return $bitlyUrl;
	    } else {
	    	$generic_access_token = $this->options['bitly_access_token'];
		    // generate the bit.ly URL
		    $bitly_api = 'https://api-ssl.bitly.com/v4/bitlinks';
		    $payload = wp_json_encode( array( 'long_url' => $url ) );

		    $response = wp_remote_post( $bitly_api, array(
			    'body'    => $payload,
			    'headers' => array(
	            	'Authorization' => 'Bearer ' . $generic_access_token,
	                'Content-Type' => 'application/json',
	                'Content-Length' => strlen( $payload )
	            ),
	            'timeout'     => 60,
	            'httpversion' => '1.0',
        		'sslverify' => false,
        		'data_format' => 'body'
			) );

			if ( ! is_wp_error( $response ) ) {
				$short_url_object = json_decode( wp_remote_retrieve_body( $response ) );
				if ( isset( $short_url_object->link ) ) {
					$short_url = esc_url_raw( $short_url_object->link );
					update_post_meta( $post_id, '_heateor_sss_bitly_url', $short_url );
					return $short_url;
				}
			}
		}
		return false;

	}

	/**
	 * Get short url.
	 *
	 * @since    1.0
	 */
	public function get_short_url( $url, $post_id ) {

		$short_url = '';
		
		if ( isset( $this->short_urls[$url] ) ) {
			// short url already calculated for this post ID
			$short_url = $this->short_urls[$url];
		} elseif ( isset( $this->options['use_shortlinks'] ) && function_exists( 'wp_get_shortlink' ) ) {
			$short_url = wp_get_shortlink();
			if ( $short_url ) {
				$this->short_urls[$url] = $short_url;
			}
			// if bit.ly integration enabled, generate bit.ly short url
		} elseif ( isset( $this->options['bitly_enable'] ) && $this->options['bitly_access_token'] != '' ) {
			$short_url = $this->generate_bitly_url( $url, $post_id );
			if ( $short_url ) {
				$this->short_urls[$url] = $short_url;
			}
		}

		return $short_url;

	}

	/**
	 * Check if current page is AMP
	 *
	 * @since    2.1
	 */
	public function is_amp_page() {

		if ( ( function_exists( 'is_amp_endpoint' ) && is_amp_endpoint() ) || ( function_exists( 'ampforwp_is_amp_endpoint' ) && ampforwp_is_amp_endpoint() ) ) {
			return true;
		}
		return false;

	}

	/**
	 * Sanitize post title
	 *
	 * @since    2.5.1
	 */
	public function sanitize_post_title( $post_title ) {

		$post_title = html_entity_decode( $post_title, ENT_QUOTES, 'UTF-8' );
	    $post_title = rawurlencode( $post_title );
	    $post_title = str_replace( '#', '%23', $post_title );
	    $post_title = esc_html( $post_title );

	    return $post_title;

	}

	/**
	 * Get Yoast SEO post meta Twitter title
	 *
	 * @since    2.5.1
	 */
	public function wpseo_twitter_title( $post ) {

		if ( $post && ( $this->is_plugin_active( 'wordpress-seo/wp-seo.php' ) || $this->is_plugin_active( 'wordpress-seo-premium/wp-seo.php' ) ) && ( $wpseo_twitter_title = WPSEO_Meta::get_value( 'twitter-title', $post->ID ) ) ) {
			return $wpseo_twitter_title;
		}
		return '';

	}

	/**
	 * Render sharing interface html
	 *
	 * @since    1.0
	 */
	public function prepare_sharing_html( $post_url, $sharing_type, $display_count, $total_shares, $standard_widget = false ) {
	
		global $post;

		if ( NULL === $post ) {
	        $post = get_post( $this->share_count_transient_id );
		}

		if ( ! is_object( $post ) ) {
	        return '';
		}

		if ( ( $sharing_type == 'vertical' && ! is_singular() ) || $standard_widget ) {
			$post_title = get_bloginfo( 'name' ) . " - " . get_bloginfo( 'description' );
			if ( is_category() ) {
				$post_title = esc_attr( wp_strip_all_tags( stripslashes( single_cat_title( '', false ) ), true ) );
			} elseif ( is_tag() ) {
				$post_title = esc_attr( wp_strip_all_tags( stripslashes( single_tag_title( '', false ) ), true ) );
			} elseif ( is_tax() ) {
				$post_title = esc_attr( wp_strip_all_tags( stripslashes( single_term_title( '', false ) ), true ) );
			} elseif ( is_search() ) {
				$post_title = esc_attr( wp_strip_all_tags( stripslashes( __( 'Search for' ) .' "' .get_search_query() .'"' ), true ) );
			} elseif ( is_author() ) {
				$post_title = esc_attr( wp_strip_all_tags( stripslashes( get_the_author_meta( 'display_name', get_query_var( 'author' ) ) ), true ) );
			} elseif ( is_archive() ) {
				if ( is_day() ) {
					$post_title = esc_attr( wp_strip_all_tags( stripslashes( get_query_var( 'day' ) . ' ' .single_month_title( ' ', false ) . ' ' . __( 'Archives' ) ), true ) );
				} elseif ( is_month() ) {
					$post_title = esc_attr( wp_strip_all_tags( stripslashes( single_month_title( ' ', false ) . ' ' . __( 'Archives' ) ), true ) );
				} elseif ( is_year() ) {
					$post_title = esc_attr( wp_strip_all_tags( stripslashes( get_query_var( 'year' ) . ' ' . __( 'Archives' ) ), true ) );
				}
			}
		} else {
			$post_title = $post->post_title;
		}

		$original_post_title = html_entity_decode( $post_title, ENT_QUOTES, 'UTF-8' );
		$post_title = $this->sanitize_post_title( $post_title );

		$output = apply_filters( 'heateor_sss_sharing_interface_filter', '', $this, $post_title, $original_post_title, $post_url, $sharing_type, $this->options, $post, $display_count, $total_shares );
		if ( $output != '' ) {
			return $output;
		}
		$html = '';
		$sharing_meta = get_post_meta( $post->ID, '_heateor_sss_meta', true );

		if ( isset( $this->options[$sharing_type.'_re_providers'] ) ) {

			$sharing_networks_object = new Sassy_Social_Share_Sharing_Networks( $this->options );
			if ( $this->is_amp_page() ) {
				$sharing_networks = $sharing_networks_object->fetch_amp_sharing_networks();
			} else {
				$sharing_networks = $sharing_networks_object->fetch_sharing_networks( $sharing_type );
			}

			$icon_height = $this->options[$sharing_type . '_sharing_shape'] != 'rectangle' ? $this->options[$sharing_type . '_sharing_size'] : $this->options[$sharing_type . '_sharing_height'];
			$style = 'style="width:' . ( $this->options[$sharing_type . '_sharing_shape'] != 'rectangle' ? $this->options[$sharing_type . '_sharing_size'] : $this->options[$sharing_type . '_sharing_width'] ) . 'px;height:' . $icon_height . 'px;';
			$counter_container_init_html = '<span class="heateor_sss_square_count';
			$counter_container_end_html = '</span>';
			$inner_style = 'display:block;';
			$li_class = 'heateorSssSharingRound';
			if ( $this->options[$sharing_type . '_sharing_shape'] == 'round' ) {
				$style .= 'border-radius:999px;';
				$inner_style .= 'border-radius:999px;';
			} elseif ( $this->options[$sharing_type . '_border_radius'] != '' ) {
				$style .= 'border-radius:' . $this->options[$sharing_type . '_border_radius'] . 'px;';
			}
			if ( $sharing_type == 'vertical' && $this->options[$sharing_type . '_sharing_shape'] == 'square' ) {
				$style .= 'margin:0;';
				$li_class = '';
			}
			$style .= '"';
			$li_items = '';
			$language = $this->options['language'] != '' ? $this->options['language'] : '';
			$like_button_count_container = '';
			if ( $display_count ) {
				$like_button_count_container = $counter_container_init_html . '">&nbsp;' . $counter_container_end_html;
			}

			// share count
			if ( $saved_share_count = $this->get_saved_share_counts( $this->share_count_transient_id, $post_url ) ) {
			    $share_counts = $saved_share_count;
			} elseif ( false !== ( $cached_share_count = $this->get_cached_share_count( $this->share_count_transient_id ) ) ) {
			    $share_counts = $cached_share_count;
			} else {
				$share_counts = '&nbsp;';
			}

			$counter_placeholder = '';
			$counter_placeholder_value = '';
			$inner_style_conditional = '';

			if ( $display_count ) {
				if ( ! isset( $this->options[$sharing_type . '_counter_position'] ) ) {
					$counter_position = $sharing_type == 'horizontal' ? 'top' : 'inner_top';
				} else {
					$counter_position = $this->options[$sharing_type . '_counter_position'];
				}
				
				switch ( $counter_position ) {
					case 'left':
						$inner_style_conditional = 'display:block;';
						$counter_placeholder = '><span class="heateor_sss_svg';
						break;
					case 'top':
						$counter_placeholder = '><span class="heateor_sss_svg';
						break;
					case 'right':
						$inner_style_conditional = 'display:block;';
						$counter_placeholder = 'span><';
						break;
					case 'bottom':
						$inner_style_conditional = 'display:block;';
						$counter_placeholder = 'span><';
						break;
					case 'inner_left':
						$inner_style_conditional = 'float:left;';
						$counter_placeholder = '><svg';
						break;
					case 'inner_top':
						$inner_style_conditional = 'margin-top:0;';
						$counter_placeholder = '><svg';
						break;
					case 'inner_right':
						$inner_style_conditional = 'float:left;';
						$counter_placeholder = 'svg><';
						break;
					case 'inner_bottom':
						$inner_style_conditional = 'margin-top:0;';
						$counter_placeholder = 'svg><';
						break;
					default:
				}

				$counter_placeholder_value = str_replace( '>', '>' . $counter_container_init_html . ' heateor_sss_%network%_count">&nbsp;' . $counter_container_end_html, $counter_placeholder );
			}
			
			$twitter_username = $this->options['twitter_username'] != '' ? $this->options['twitter_username'] : '';
			$total_share_count = 0;
			
			$share_count = array();
			$to_be_replaced = array();
			$replace_by = array();
			if ( $this->is_amp_page() ) {
				$icon_width = $this->options[$sharing_type . '_sharing_shape'] != 'rectangle' ? $this->options[$sharing_type . '_sharing_size'] : $this->options[$sharing_type . '_sharing_width'];

				$to_be_replaced[] = '%img_url%';
				$to_be_replaced[] = '%width%';
				$to_be_replaced[] = '%height%';

				$replace_by[] = plugins_url( '../images/amp', __FILE__ );
				$replace_by[] = $icon_width;
				$replace_by[] = $icon_height;
			}
			
			$wpseo_post_title = $post_title;
			$decoded_post_title = esc_html( str_replace( array( '%23', '%27', '%22', '%21', '%3A' ), array( '#', "'", '"', '!', ':' ), urlencode( $original_post_title ) ) );
			if ( $wpseo_twitter_title = $this->wpseo_twitter_title( $post ) ) {
				$wpseo_post_title = $this->sanitize_post_title( $wpseo_twitter_title );
				$decoded_post_title = esc_html( str_replace( array( '%23', '%27', '%22', '%21', '%3A' ), array( '#', "'", '"', '!', ':' ), urlencode( html_entity_decode( $wpseo_twitter_title, ENT_QUOTES, 'UTF-8' ) ) ) );
			}

			$logo_color = '#fff';
			if ( $sharing_type == 'horizontal' && $this->options['horizontal_font_color_default'] ) {
				$logo_color = $this->options['horizontal_font_color_default'];
			} elseif ( $sharing_type == 'vertical' && $this->options['vertical_font_color_default'] ) {
				$logo_color = $this->options['vertical_font_color_default'];
				
			}
			$this->logo_color = $logo_color;

			$ul = '<div class="heateor_sss_sharing_ul">';
			foreach ( $this->options[$sharing_type.'_re_providers'] as $provider ) {
				$share_count[$provider] = $share_counts == '&nbsp;' ? '' : ( isset( $share_counts[$provider] ) ? $share_counts[$provider] : '' );
				$isset_starting_share_count = isset( $sharing_meta[$provider . '_' . $sharing_type . '_count'] ) && $sharing_meta[$provider . '_' . $sharing_type . '_count'] != '' ? true : false;
				$total_share_count += intval( $share_count[$provider] ) + ( $isset_starting_share_count ? $sharing_meta[$provider . '_' . $sharing_type . '_count'] : 0) ;
				$sharing_networks[$provider] = str_replace( $to_be_replaced, $replace_by, $sharing_networks[$provider] );
				$li_items .= str_replace(
					array(
						'%padding%',
						'%network%',
						'%ucfirst_network%',
						'%like_count_container%',
						'%post_url%',
						'%encoded_post_url%',
						'%post_title%',
						'%wpseo_post_title%',
						'%decoded_post_title%',
						'%twitter_username%',
						'%via_twitter_username%',
						'%language%',
						'%style%',
						'%inner_style%',
						'%li_class%',
						$counter_placeholder,
						'%title%',
						'%amp_email%',
						'%amp_whatsapp%',
						'%anchor_style%',
						'%span_style%',
						'%span2_style%',
						'%logo_color%'
					),
					array(
						( $this->options[$sharing_type . '_sharing_shape'] == 'rectangle' ? $this->options[$sharing_type . '_sharing_height'] : $this->options[$sharing_type . '_sharing_size'] ) * 21/100,
						$provider,
						ucfirst( str_replace( array( ' ', '_', '.' ), '', $provider ) ),
						$like_button_count_container,
						$post_url,
						urlencode( $post_url ),
						$post_title,
						$wpseo_post_title,
						$decoded_post_title,
						$twitter_username,
						$twitter_username ? 'via=' . $twitter_username . '&' : '',
						$language,
						$style,
						$inner_style . ( $share_count[$provider] || ( $isset_starting_share_count && $share_counts != '&nbsp;' ) ? $inner_style_conditional : '' ),
						$li_class,
						str_replace( '%network%', $provider, $isset_starting_share_count ? str_replace( '>&nbsp;', ' data-heateor-sss-st-count="' . $sharing_meta[$provider . '_' . $sharing_type . '_count'] . '"' . ( $share_counts == '&nbsp;' ? '>&nbsp;' : ' style="visibility:visible;' . ( $inner_style_conditional ? 'display:block;' : '' ) . '">' . $this->round_off_counts( intval( $share_count[$provider] ) + $sharing_meta[$provider . '_' . $sharing_type . '_count'] ) ) , $counter_placeholder_value ) : str_replace( '>&nbsp;', $share_count[$provider] ? ' style="visibility:visible;' . ( $inner_style_conditional ? 'display:block;' : '' ) . '">' . $this->round_off_counts( intval( $share_count[$provider] ) ) : '>&nbsp;', $counter_placeholder_value ) ),
						ucfirst( str_replace( '_', ' ', $provider ) ),
						'',
						'',
						'style="font-size:32px!important;box-shadow:none;display:inline-block;vertical-align:middle"',
						str_replace( array( 'style="', ';"' ), array( '', ';display:inline-block;opacity:1;float:left;font-size:32px;box-shadow:none;display:inline-block;font-size:16px;padding:0 4px;vertical-align:middle;background-repeat:repeat;overflow:hidden;padding:0;cursor:pointer;box-sizing:content-box' ), $style ),
						"line-height:32px;display:inline-block;opacity:1;float:left;font-size:32px;display:inline-block;border:0;box-shadow:none;font-size:16px;padding:0 4px;vertical-align:middle;line-height:16px;position:absolute;clip:rect(1px,1px,1px,1px);-webkit-clip-path:polygon(0 0,0 0,0 0);overflow:hidden;",
						$logo_color
					),
					$sharing_networks[$provider]
				);
			}
			
			if ( isset( $this->options[$sharing_type . '_more'] ) && ! $this->is_amp_page() ) {
				$li_items .= '<a class="heateor_sss_more" title="More" rel="nofollow noopener" style="font-size: 32px!important;border:0;box-shadow:none;display:inline-block!important;font-size:16px;padding:0 4px;vertical-align: middle;display:inline;" href="' . htmlentities( addslashes( esc_url( $post_url ) ), ENT_QUOTES ) . '" onclick="event.preventDefault()">';
				if ( $display_count ) {
					$li_items .= $counter_container_init_html . '">&nbsp;' . $counter_container_end_html;
				}
				$li_items .= '<span class="heateor_sss_svg" style="background-color:#ee8e2d;' . str_replace( array( 'style="', ';"' ), array( '', ';display:inline-block!important;opacity:1;float:left;font-size:32px!important;box-shadow:none;display:inline-block;font-size:16px;padding:0 4px;vertical-align:middle;display:inline;background-repeat:repeat;overflow:hidden;padding:0;cursor:pointer;box-sizing:content-box;' ), $style ) . '" onclick="heateorSssMoreSharingPopup(this, \'' . htmlentities( addslashes( esc_url( $post_url ) ), ENT_QUOTES ) . '\', \'' . $post_title . '\', \'' . $this->sanitize_post_title( $this->wpseo_twitter_title( $post ) ) . '\' )">';
				if ( $this->is_amp_page() ) {
					$li_items .= '<i title="More" class="heateorSssSharing heateorSssMoreBackground"><i class="heateorSssSharingSvg heateorSssMoreSvg"></i></i></li>';
				} else {
					$li_items .= '<svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" viewBox="-.3 0 32 32" version="1.1" width="100%" height="100%" style="' . $inner_style . '" xml:space="preserve"><g><path fill="' . $this->logo_color . '" d="M18 14V8h-4v6H8v4h6v6h4v-6h6v-4h-6z" fill-rule="evenodd"></path></g></svg></span></a>';
				}
			}
			
			$total_shares_html = '';
			if ( $total_shares && ! $this->is_amp_page() ) {
				if ( $sharing_type == 'horizontal' ) {
					$add_style = ';margin-left:9px !important;';
				} else {
					$add_style = ';margin-bottom:9px !important;';
				}
				$add_style .= ( $total_share_count && $share_counts != '&nbsp;' ? 'visibility:visible;' : '' ) . '"';
				$style = str_replace( ';"', $add_style, $style );
				$total_shares_html = '<a style="font-size:32px!important;box-shadow: none;display: inline-block!important;font-size: 16px;padding: 0 4px;vertical-align: middle;display:inline;" class="' . $li_class . '">';
				if ( $display_count) {
					$total_shares_html .= $counter_container_init_html . '">&nbsp;' . $counter_container_end_html;
				}
				$total_shares_html .= '<div ' . $style . ' title="' . __( 'Total Shares', 'sassy-social-share' ) . '" class="heateorSssSharing heateorSssTCBackground">' . ( $total_share_count ? '<div class="heateorSssTotalShareCount" style="font-size:' . ( $icon_height * 62/100 ) . 'px">' . $this->round_off_counts( intval( $total_share_count ) ) . '</div><div class="heateorSssTotalShareText" style="font-size: ' . ( $icon_height * 38/100 ) . 'px">' . ( $total_share_count < 2 ? __( 'Share', 'sassy-social-share' ) : __( 'Shares', 'sassy-social-share' ) ) . '</div>' : '' ) . '</div></a>';
			}

			if ( $sharing_type == 'vertical' ) {
				$html .= $ul . $total_shares_html . $li_items;
			} else {
				$html .= $ul . $li_items . $total_shares_html;
			}

			$html .= '</div>';

			$html .= '<div class="heateorSssClear"></div>';
		}
		
		return $html;

	}

	/**
	 * Roud off share counts
	 *
	 * @since    1.7
	 * @param    integer    $sharingCount    Share counts
	 */
	public function round_off_counts( $sharing_count ) {

		if ( $sharing_count > 999 && $sharing_count < 10000 ) {
			$sharing_count = round( $sharing_count/1000, 1 ) . 'K';
		} elseif ( $sharing_count > 9999 && $sharing_count < 100000 ) {
			$sharing_count = round( $sharing_count/1000, 1 ) . 'K';
		} else if ( $sharing_count > 99999 && $sharing_count < 1000000 ) {
			$sharing_count = round( $sharing_count/1000, 1 ) . 'K';
		} else if ( $sharing_count > 999999 ) {
			$sharing_count = round( $sharing_count/1000000, 1 ) . 'M';
		}

		return $sharing_count;
	
	}

	/**
	 * Get cached share counts for given post ID
	 *
	 * @since    1.7
	 * @param    integer    $post_id    ID of the post to fetch cached share counts for
	 */
	public function get_cached_share_count( $post_id ) {

		$share_count_transient = get_transient( 'heateor_sss_share_count_' . $post_id );
		do_action( 'heateor_sss_share_count_transient_hook', $share_count_transient, $post_id );
		return $share_count_transient;
	
	}

	/**
	 * Get saved share counts for given post ID
	 *
	 * @since    1.3.1
	 */
	public function get_saved_share_counts( $post_id, $post_url ) {
		
		$share_counts = false;

		if ( $post_id == 'custom' ) {
			$share_counts = maybe_unserialize( get_option( 'heateor_sss_custom_url_shares' ) );
		} elseif ( $post_url == home_url() ) {
			$share_counts = maybe_unserialize( get_option( 'heateor_sss_homepage_shares' ) );
		} elseif ( $post_id > 0 ) {
			$share_counts = get_post_meta( $post_id, '_heateor_sss_shares_meta', true );
		}
		
		return $share_counts;
	
	}

	/**
	 * Get http/https protocol at the website
	 *
	 * @since    1.0
	 */
	public function get_http_protocol() {
		
		if ( isset( $_SERVER['HTTPS'] ) && ! empty( $_SERVER['HTTPS'] ) && $_SERVER['HTTPS'] != 'off' ) {
			return "https://";
		} else {
			return "http://";
		}
	
	}

	/**
	 * Apply share url filter to customize share target url
	 *
	 * @since    3.0
	 */
	public function apply_target_share_url_filter( $post_url, $sharing_type = 'horizontal', $standard_widget = false ) {
		
		$post_url = apply_filters( 'heateor_sss_target_share_url_filter', $post_url, $sharing_type, $standard_widget );

		return $post_url;

	}

	/**
	 * Enable sharing interface at selected areas
	 *
	 * @since    1.0
	 */
	public function render_sharing( $content ) {
		
		// if sharing is disabled on AMP, return content as is
		if ( ! isset( $this->options['amp_enable'] ) && $this->is_amp_page() ) {
			return $content;
		}

		global $post;

		if ( ! is_object( $post ) ) {
			return $content;
		}

		// hook to bypass sharing
		$disable_sharing = apply_filters( 'heateor_sss_disable_sharing', $post, $content );
		// if $disable_sharing value is 1, return content without sharing interface
		if ( $disable_sharing === 1 ) {
			return $content;
		}
		$sharing_meta = get_post_meta( $post->ID, '_heateor_sss_meta', true );
		
		$sharing_bp_activity = false;

		if ( current_filter() == 'bp_activity_entry_meta' ) {
			if ( isset( $this->options['bp_activity'] ) ) {
				$sharing_bp_activity = true;
			}
		}
		
		$post_types = get_post_types( array( 'public' => true ), 'names', 'and' );
		$post_types = array_diff( $post_types, array( 'post', 'page' ) );

		// sharing interface
		if ( isset( $this->options['hor_enable'] ) && ! ( isset( $sharing_meta['sharing'] ) && $sharing_meta['sharing'] == 1 && ( ! is_front_page() || ( is_front_page() && 'page' == get_option( 'show_on_front' ) ) ) ) ) {
			$post_id = $post->ID;
			// default post url
			$post_url = get_permalink( $post->ID );
			$share_count_url = $post_url;
			if ( $sharing_bp_activity ) {
				$post_url = bp_get_activity_thread_permalink();
				$share_count_url = $post_url;
				$post_id = 0;
			} else {
				if ( $this->options['horizontal_target_url'] == 'default' ) {
					if ( ( isset( $_SERVER['QUERY_STRING'] ) && $_SERVER['QUERY_STRING'] ) || $post_url == '' ) {
						$post_url = esc_url_raw( str_replace( array( '%3Cscript%3E', '%3C/script%3E' ), '', $this->get_http_protocol() . $_SERVER["HTTP_HOST"] . $_SERVER["REQUEST_URI"] ) );
					}
					if ( $share_count_url == '' ) {
						$share_count_url = $post_url;
					}
				} elseif ( $this->options['horizontal_target_url'] == 'home' ) {
					$post_url = home_url();
					$share_count_url = $post_url;
					$post_id = 0;
				} elseif ( $this->options['horizontal_target_url'] == 'custom' && $this->options['horizontal_target_url_custom'] ) {
					$post_url = esc_url_raw( $this->options['horizontal_target_url_custom'] );
					$share_count_url = $post_url;
					$post_id = 0;
				}
			}

			$custom_post_url = $this->apply_target_share_url_filter( $post_url, 'horizontal', false );
			if ( $custom_post_url != $post_url ) {
				$post_url = $custom_post_url;
				$share_count_url = $post_url;
			}

			$sharing_url = $this->get_short_url( $post_url, $post->ID );
			// share count transient ID
			$this->share_count_transient_id = $this->get_share_count_transient_id( $post_url );
			$sharing_div = $this->prepare_sharing_html( $sharing_url ? $sharing_url : $post_url, 'horizontal', isset( $this->options['horizontal_counts'] ), isset( $this->options['horizontal_total_shares'] ) );
			$sharing_container_style = '';
			$sharing_title_style = 'style="font-weight:bold"';
			
			if ( $this->options['hor_sharing_alignment'] == 'right' ) {
				$sharing_container_style = 'style="float:right"';
			}
			
			$horizontal_div = "<div class='heateorSssClear'></div><div " . $sharing_container_style . " class='heateor_sss_sharing_container heateor_sss_horizontal_sharing' " . ( $this->is_amp_page() ? "" : "data-heateor-sss-href='" . esc_url( isset( $share_count_url ) && $share_count_url ? $share_count_url : $post_url ) . "'" ) . ( ( $this->get_cached_share_count( $this->share_count_transient_id ) === false || $this->is_amp_page() ) ? '' : ' data-heateor-sss-no-counts="1"' ) . "><div class='heateor_sss_sharing_title' " . $sharing_title_style . " >" . esc_html( ucfirst( get_option( 'heateor_sss' )['title'] ) ) . "</div>" . $sharing_div . "</div><div class='heateorSssClear'></div>";
			if ( $sharing_bp_activity ) {
				global $heateor_sss_allowed_tags;
				echo wp_kses( $horizontal_div, $heateor_sss_allowed_tags );
			}
			// show horizontal sharing
			if ( ( isset( $this->options['home'] ) && is_front_page() ) || ( isset( $this->options['category'] ) && is_category() ) || ( isset( $this->options['archive'] ) && is_archive() ) || ( isset( $this->options['post'] ) && is_single() && isset( $post->post_type ) && $post->post_type == 'post' ) || ( isset( $this->options['page'] ) && is_page() && isset( $post->post_type ) && $post->post_type == 'page' ) || ( isset( $this->options['excerpt'] ) && (is_home() || current_filter() == 'the_excerpt' ) ) || ( isset( $this->options['bb_reply'] ) && current_filter() == 'bbp_get_reply_content' ) || ( isset( $this->options['bb_forum'] ) && ( isset( $this->options['top'] ) && current_filter() == 'bbp_template_before_single_forum' || isset( $this->options['bottom'] ) && current_filter() == 'bbp_template_after_single_forum' ) ) || ( isset( $this->options['bb_topic'] ) && ( isset( $this->options['top'] ) && in_array( current_filter(), array( 'bbp_template_before_single_topic', 'bbp_template_before_lead_topic' ) ) || isset( $this->options['bottom'] ) && in_array( current_filter(), array( 'bbp_template_after_single_topic', 'bbp_template_after_lead_topic' ) ) ) ) || ( isset( $this->options['woocom_shop'] ) && current_filter() == 'woocommerce_after_shop_loop_item' ) || ( isset( $this->options['woocom_product'] ) && current_filter() == 'woocommerce_share' ) || ( isset( $this->options['woocom_thankyou'] ) && current_filter() == 'woocommerce_thankyou' ) || (current_filter() == 'bp_before_group_header' && isset( $this->options['bp_group'] ) ) ) {
				if ( in_array( current_filter(), array( 'bbp_template_before_single_topic', 'bbp_template_before_lead_topic', 'bbp_template_before_single_forum', 'bbp_template_after_single_topic', 'bbp_template_after_lead_topic', 'bbp_template_after_single_forum', 'woocommerce_after_shop_loop_item', 'woocommerce_share', 'woocommerce_thankyou', 'bp_before_group_header' ) ) ) {
					global $heateor_sss_allowed_tags;
					echo wp_kses( '<div class="heateorSssClear"></div>' . $horizontal_div . '<div class="heateorSssClear"></div>', $heateor_sss_allowed_tags );
				} else {
					if ( isset( $this->options['top'] ) && isset( $this->options['bottom'] ) ) {
						$content = $horizontal_div . '<br/>' . $content . '<br/>' . $horizontal_div;
					} else {
						if ( isset( $this->options['top'] ) ) {
							$content = $horizontal_div . $content;
						} elseif ( isset( $this->options['bottom'] ) ) {
							$content = $content . $horizontal_div;
						}
					}
				}
			} elseif ( count( $post_types ) ) {
				foreach ( $post_types as $post_type ) {
					if ( isset( $this->options[$post_type] ) && ( is_single() || is_page() ) && isset( $post->post_type ) && $post->post_type == $post_type ) {
						if ( isset( $this->options['top'] ) && isset( $this->options['bottom'] ) ) {
							$content = $horizontal_div . '<br/>' . $content.'<br/>'.$horizontal_div;
						} else {
							if ( isset( $this->options['top'] ) ) {
								$content = $horizontal_div.$content;
							} elseif ( isset( $this->options['bottom'] ) ) {
								$content = $content.$horizontal_div;
							}
						}
					}
				}
			}
		}
		if ( isset( $this->options['vertical_enable'] ) && ! ( isset( $sharing_meta['vertical_sharing'] ) && $sharing_meta['vertical_sharing'] == 1 && ( ! is_front_page() || ( is_front_page() && 'page' == get_option( 'show_on_front' ) ) ) ) ) {
			$post_id = $post->ID;
			$post_url = get_permalink( $post->ID );
			$share_count_url = $post_url;
			
			if ( $this->options['vertical_target_url'] == 'default' ) {
				$post_url = get_permalink( $post->ID );
				if ( ! is_singular() ) {
					$post_url = esc_url_raw( str_replace( array( '%3Cscript%3E', '%3C/script%3E' ), '', $this->get_http_protocol() . $_SERVER["HTTP_HOST"] . $_SERVER["REQUEST_URI"] ) );
					$post_id = 0;
					$share_count_url = $post_url;
				} elseif ( ( isset( $_SERVER['QUERY_STRING'] ) && $_SERVER['QUERY_STRING'] ) || $post_url == '' ) {
					$post_url = esc_url_raw( str_replace( array( '%3Cscript%3E', '%3C/script%3E' ), '', $this->get_http_protocol() . $_SERVER["HTTP_HOST"] . $_SERVER["REQUEST_URI"] ) );
				}
				if ( $share_count_url == '' ) {
					$share_count_url = $post_url;
				}
			} elseif ( $this->options['vertical_target_url'] == 'home' ) {
				$post_url = home_url();
				$share_count_url = $post_url;
				$post_id = 0;
			} elseif ( $this->options['vertical_target_url'] == 'custom' && $this->options['vertical_target_url_custom'] ) {
				$post_url = esc_url_raw( $this->options['vertical_target_url_custom'] );
				$share_count_url = $post_url;
				$post_id = 0;
			}
			
			$custom_post_url = $this->apply_target_share_url_filter( $post_url, 'vertical', false );
			if ( $custom_post_url != $post_url ) {
				$post_url = $custom_post_url;
				$share_count_url = $post_url;
			}

			$sharing_url = $this->get_short_url( $post_url, $post->ID );

			$vertical_sharing_width = ( $this->options['vertical_sharing_shape'] == 'rectangle' ? $this->options['vertical_sharing_width'] : $this->options['vertical_sharing_size'] );
			if ( isset( $this->options['vertical_counts'] ) && isset( $this->options['vertical_counter_position'] ) && in_array( $this->options['vertical_counter_position'], array( 'left', 'right' ) ) ) {
				$vertical_sharing_width += $vertical_sharing_width*60/100;
			}
			// share count transient ID
			$this->share_count_transient_id = $this->get_share_count_transient_id( $post_url );
			$sharing_div = $this->prepare_sharing_html( $sharing_url ? $sharing_url : $post_url, 'vertical', isset( $this->options['vertical_counts'] ), isset( $this->options['vertical_total_shares'] ) );
			$offset = ( $this->options['alignment'] != '' && $this->options[$this->options['alignment'].'_offset'] != '' ? esc_html( $this->options['alignment'] ) . ': ' . intval( $this->options[$this->options['alignment'] . '_offset'] ) . 'px;' : '' ) . ( $this->options['top_offset'] != '' ? 'top: ' . intval( $this->options['top_offset'] ) . 'px;' : '' );
			$vertical_div = "<div class='heateor_sss_sharing_container heateor_sss_vertical_sharing" . ( isset( $this->options['bottom_mobile_sharing'] ) ? ' heateor_sss_bottom_sharing' : '' ) . "' style='width:" . intval( $vertical_sharing_width + 4 ) . "px;" . $offset . ( $this->options['vertical_bg'] != '' ? 'background-color: ' . esc_html( $this->options['vertical_bg'] ) : '-webkit-box-shadow:none;box-shadow:none;' ) . "' " . ( $this->is_amp_page() ? "" : "data-heateor-sss-href='" . esc_url( isset( $share_count_url ) && $share_count_url ? $share_count_url : $post_url ) . "'" ) . ( ( $this->get_cached_share_count( $this->share_count_transient_id ) === false || $this->is_amp_page() ) ? "" : ' data-heateor-sss-no-counts="1"' ) . ">" . $sharing_div . "</div>";
			// show vertical sharing
			if ( ( isset( $this->options['vertical_home'] ) && is_front_page() ) || ( isset( $this->options['vertical_category'] ) && is_category() ) || ( isset( $this->options['vertical_archive'] ) && is_archive() ) || ( isset( $this->options['vertical_post'] ) && is_single() && isset( $post->post_type ) && $post->post_type == 'post' ) || ( isset( $this->options['vertical_page'] ) && is_page() && isset( $post->post_type ) && $post->post_type == 'page' ) || ( isset( $this->options['vertical_excerpt'] ) && (is_home() || current_filter() == 'the_excerpt' ) ) || ( isset( $this->options['vertical_bb_forum'] ) && current_filter() == 'bbp_template_before_single_forum' ) || ( isset( $this->options['vertical_bb_topic'] ) && in_array( current_filter(), array( 'bbp_template_before_single_topic', 'bbp_template_before_lead_topic' ) ) ) || (current_filter() == 'bp_before_group_header' && isset( $this->options['vertical_bp_group'] ) ) ) {
				if ( in_array( current_filter(), array( 'bbp_template_before_single_topic', 'bbp_template_before_lead_topic', 'bbp_template_before_single_forum', 'bp_before_group_header' ) ) ) {
					global $heateor_sss_allowed_tags;
					echo wp_kses( $vertical_div, $heateor_sss_allowed_tags );
				} else {
					if ( is_front_page() ) {
						if ( current_filter() == 'the_content' ) {
							$var = $this->vertical_home_count;
						} elseif ( is_home() || current_filter() == 'the_excerpt' ) {
							$var = $this->vertical_excerpt_count;
						}
						if ( $var == 0 ) {
							if ( $this->options['vertical_target_url'] == 'default' ) {
								$post_url = home_url();
								$share_count_url = $post_url;
								$custom_post_url = $this->apply_target_share_url_filter( $post_url, 'vertical', false );
								if ( $custom_post_url != $post_url ) {
									$post_url = $custom_post_url;
									$share_count_url = $post_url;
								}
								$sharing_url = $this->get_short_url( $post_url, 0 );
								// share count transient ID
								$this->share_count_transient_id = 0;
								$sharing_div = $this->prepare_sharing_html( $sharing_url ? $sharing_url : $post_url, 'vertical', isset( $this->options['vertical_counts'] ), isset( $this->options['vertical_total_shares'] ) );
								$vertical_div = "<div class='heateor_sss_sharing_container heateor_sss_vertical_sharing" . ( isset( $this->options['bottom_mobile_sharing'] ) ? ' heateor_sss_bottom_sharing' : '' ) . "' style='width:" . ( $vertical_sharing_width + 4 ) . "px;" . $offset . ( $this->options['vertical_bg'] != '' ? 'background-color: ' . $this->options['vertical_bg'] : '-webkit-box-shadow:none;box-shadow:none;' ) . "' " . ( $this->is_amp_page() ? "" : "data-heateor-sss-href='" . ( isset( $share_count_url ) && $share_count_url ? $share_count_url : $post_url ) . "'" ) . ( ( $this->get_cached_share_count( 0 ) === false || $this->is_amp_page() ) ? "" : ' data-heateor-sss-no-counts="1"' ) . ">" . $sharing_div . "</div>";
							}
							$content = $content . $vertical_div;
							if ( current_filter() == 'the_content' ) {
								$this->vertical_home_count++;
							} elseif ( is_home() || current_filter() == 'the_excerpt' ) {
								$this->vertical_excerpt_count++;
							}
						}
					} else {
						$content = $content . $vertical_div;
					}
				}
			} elseif ( count( $post_types ) ) {
				foreach ( $post_types as $post_type ) {
					if ( isset( $this->options['vertical_' . $post_type] ) && ( is_single() || is_page() ) && isset( $post->post_type ) && $post->post_type == $post_type ) {
						$content = $content . $vertical_div;
					}
				}
			}
		}
		return $content;
	}

	/**
	 * Return ajax response
	 *
	 * @since    1.0
	 */
	private function ajax_response( $response ) {
		
		$response = apply_filters( 'heateor_sss_ajax_response_filter', $response );
		header( 'Content-Type: application/json' );
		die( json_encode( $response ) );

	}

	/**
	 * Check if the passed URL is valid
	 *
	 * @since    3.3.40
	 */
	private function validate_url( $url ) {

		return filter_var( trim( $url ), FILTER_VALIDATE_URL );
	
	}

	/**
	 * Sanitize the URLs of passed array
	 *
	 * @since    3.3.41
	 */
	private function sanitize_url_array( $url ) {

		if ( $this->validate_url( $url ) !== false ) {
			return esc_url_raw( $url );
		}
	
	}

	/**
	 * Get share counts for sharing networks
	 *
	 * @since    1.0
	 */
	public function fetch_share_counts() {

		if ( isset( $_GET['urls'] ) && is_array( $_GET['urls'] ) && count( $_GET['urls'] ) > 0 ) {
			$target_urls = array_map( array( $this, 'sanitize_url_array' ), array_unique( $_GET['urls'] ) );
			if ( ! is_array( $target_urls ) ) {
				$target_urls = array();
			}
		} else {
			$this->ajax_response( array( 'status' => 0, 'message' => __( 'Invalid request' ) ) );
		}
		$horizontal_sharing_networks = isset( $this->options['horizontal_re_providers'] ) ? $this->options['horizontal_re_providers'] : array();
		$vertical_sharing_networks = isset( $this->options['vertical_re_providers'] ) ? $this->options['vertical_re_providers'] : array();
		$sharing_networks = array_unique( array_merge( $horizontal_sharing_networks, $vertical_sharing_networks ) );
		if ( count( $sharing_networks ) == 0 ) {
			$this->ajax_response( array( 'status' => 0, 'message' => __( 'Providers not selected' ) ) );
		}
		
		$response_data = array();
		$ajax_response = array();

		$multiplier = 60;
		if ( $this->options['share_count_cache_refresh_count'] != '' ) {
			switch ( $this->options['share_count_cache_refresh_unit'] ) {
				case 'seconds':
					$multiplier = 1;
					break;

				case 'minutes':
					$multiplier = 60;
					break;
				
				case 'hours':
					$multiplier = 3600;
					break;

				case 'days':
					$multiplier = 3600*24;
					break;

				default:
					$multiplier = 60;
					break;
			}
			$transient_expiration_time = $multiplier * $this->options['share_count_cache_refresh_count'];
		}

		$target_urls_array = array();
		$target_urls_array[] = $target_urls;
		$target_urls_array = apply_filters( 'heateor_sss_target_share_urls', $target_urls_array );
		$share_count_transient_array = array();
		if ( in_array( 'facebook', $sharing_networks ) ) {
			$ajax_response['facebook_urls'] = $target_urls_array;
		}
		
		foreach ( $target_urls_array as $target_urls ) {
			$share_count_transients = array();
			foreach ( $target_urls as $target_url ) {
				$share_count_transient = array();
				foreach ( $sharing_networks as $provider ) {
					switch ( $provider ) {
						case 'twitter':
							$url = "https://counts.twitcount.com/counts.php?url=" . $target_url;
							break;
						case 'reddit':
							$url = 'https://www.reddit.com/api/info.json?url=' . $target_url;
							break;
						case 'pinterest':
							$url = 'https://api.pinterest.com/v1/urls/count.json?callback=heateorSss&url=' . $target_url;
							break;
						case 'buffer':
							$url = 'https://api.bufferapp.com/1/links/shares.json?url=' . $target_url;
							break;
						case 'vkontakte':
							$url = 'https://vk.com/share.php?act=count&url=' . $target_url;
							break;
						case 'Odnoklassniki':
							$url = 'https://connect.ok.ru/dk?st.cmd=extLike&tp=json&ref=' . $target_url;
							break;
						case 'Fintel':
							$url = 'https://fintel.io/api/pageStats?url=' . $target_url;
							break;
						default:
							$url = '';
					}
					if ( $url == '' ) { continue; }
					$response = wp_remote_get( $url,  array( 'timeout' => 15, 'user-agent'  => 'Sassy-Social-Share' ) );
					if ( ! is_wp_error( $response ) && isset( $response['response']['code'] ) && 200 === $response['response']['code'] ) {
						$body = wp_remote_retrieve_body( $response );
						if ( $provider == 'pinterest' ) {
							$body = str_replace( array( 'heateorSss(', ')' ), '', $body );
						}
						if ( $provider != 'vkontakte' ) {
							$body = json_decode( $body );
						}
						switch ( $provider ) {
							case 'facebook':
								if ( ! empty( $body->engagement ) && isset( $body->engagement->share_count ) ) {
									$share_count_transient['facebook'] = ( isset( $body->engagement->reaction_count ) ? $body->engagement->reaction_count : 0 ) + ( isset( $body->engagement->comment_count ) ? $body->engagement->comment_count : 0 ) + $body->engagement->share_count;
								} else {
									$share_count_transient['facebook'] = 0;
								}
								break;
							case 'twitter':
								if ( ! empty( $body->count ) ) {
									$share_count_transient['twitter'] = $body->count;
								} else {
									$share_count_transient['twitter'] = 0;
								}
								break;
							case 'linkedin':
								if ( ! empty( $body->count ) ) {
									$share_count_transient['linkedin'] = $body->count;
								} else {
									$share_count_transient['linkedin'] = 0;
								}
								break;
							case 'reddit':
								$share_count_transient['reddit'] = 0;
								if ( ! empty( $body->data->children ) ) {
									$children = $body->data->children;
									$ups = $downs = 0;
									foreach ( $children as $child ) {
						                $ups += ( int ) $child->data->ups;
						                $downs += ( int ) $child->data->downs;
						            }
						            $score = $ups - $downs;
						            if ( $score < 0 ) {
						            	$score = 0;
						            }
									$share_count_transient['reddit'] = $score;
								}
								break;
							case 'pinterest':
								if ( ! empty( $body->count ) ) {
									$share_count_transient['pinterest'] = $body->count;
								} else {
									$share_count_transient['pinterest'] = 0;
								}
								break;
							case 'buffer':
								if ( ! empty( $body->shares ) ) {
									$share_count_transient['buffer'] = $body->shares;
								} else {
									$share_count_transient['buffer'] = 0;
								}
								break;
							case 'vkontakte':
								if ( ! empty( $body ) ) {
									$share_count_transient['vkontakte'] = (int) str_replace( array( 'VK.Share.count(0, ', ' );' ), '', $body );
								} else {
									$share_count_transient['vkontakte'] = 0;
								}
								break;
							case 'Odnoklassniki':
								if ( ! empty( $body ) && isset( $body->count ) ) {
									$share_count_transient['Odnoklassniki'] = $body->count;
								} else {
									$share_count_transient['Odnoklassniki'] = 0;
								}
								break;
							case 'Fintel':
								if ( ! empty( $body ) && isset( $body->points ) ) {
									$share_count_transient['Fintel'] = $body->points;
								} else {
									$share_count_transient['Fintel'] = 0;
								}
								break;
						}
					} else {
						$share_count_transient[$provider] = 0;
					}
				}
				$share_count_transients[] = $share_count_transient;
			}
			$share_count_transient_array[] = $share_count_transients;
		}
		$final_share_count_transient = array();
		for ( $i = 0; $i < count( $target_urls_array[0] ); $i++ ) {
			$final_share_count_transient = $share_count_transient_array[0][$i];
			for ( $j = 1; $j < count( $share_count_transient_array ); $j++ ) {
				foreach ( $final_share_count_transient as $key => $val ) {
					$final_share_count_transient[$key] += $share_count_transient_array[$j][$i][$key];
				}
			}
			$response_data[$target_urls_array[0][$i]] = $final_share_count_transient;
			if ( $this->options['share_count_cache_refresh_count'] != '' ) {
				set_transient( 'heateor_sss_share_count_' . $this->get_share_count_transient_id( $target_urls_array[0][$i] ), $final_share_count_transient, $transient_expiration_time );
				// update share counts saved in the database
				$this->update_share_counts( $target_urls_array[0][$i], $final_share_count_transient );
			}
		}
		do_action( 'heateor_sss_share_count_ajax_hook', $response_data );
		
		$ajax_response['status'] = 1;
		$ajax_response['message'] = $response_data;

		$this->ajax_response( $ajax_response );

	}

	/**
	 * Save share counts in post-meta
	 *
	 * @since    3.1.3
	 */
	public function update_share_counts( $target_url, $share_counts ) {
		
		$post_id = $this->get_share_count_transient_id( $target_url );
		
		if ( ! isset( $share_counts['facebook'] ) ) {
			$saved_share_count = $this->get_saved_share_counts( $post_id, $target_url );
			if ( is_array( $saved_share_count ) && isset( $saved_share_count['facebook'] ) ) {
				$share_counts['facebook'] = $saved_share_count['facebook'];
			}
		}
		
		if ( $post_id == 'custom' ) {
			update_option( 'heateor_sss_custom_url_shares', maybe_serialize( $share_counts ) );
		} elseif ( $target_url == home_url() ) {
			update_option( 'heateor_sss_homepage_shares', maybe_serialize( $share_counts ) );
		} elseif ( $post_id > 0 ) {
			update_post_meta( $post_id, '_heateor_sss_shares_meta', $share_counts );
		}

	}

	/**
	 * Save Facebook share counts in transient
	 *
	 * @since    2.4.2
	 */
	public function save_facebook_shares() {
		
		if ( isset( $_GET['share_counts'] ) && is_array( $_GET['share_counts'] ) && count( $_GET['share_counts'] ) > 0 ) {
			$target_urls = array_map( 'intval', $_GET['share_counts'] );
		} else {
			$this->ajax_response( array( 'status' => 0, 'message' => __( 'Invalid request' ) ) );
		}

		$multiplier = 60;
		if ( $this->options['share_count_cache_refresh_count'] != '' ) {
			switch ( $this->options['share_count_cache_refresh_unit'] ) {
				case 'seconds':
					$multiplier = 1;
					break;

				case 'minutes':
					$multiplier = 60;
					break;
				
				case 'hours':
					$multiplier = 3600;
					break;

				case 'days':
					$multiplier = 3600*24;
					break;

				default:
					$multiplier = 60;
					break;
			}
			$transient_expiration_time = $multiplier * $this->options['share_count_cache_refresh_count'];
		}

		foreach ( $target_urls as $key => $value ) {
			$transient_id = $this->get_share_count_transient_id( $key );
			$share_count_transient = get_transient( 'heateor_sss_share_count_' . $transient_id );
			if ( $share_count_transient !== false ) {
				$share_count_transient['facebook'] = $value;
				if ( $this->options['share_count_cache_refresh_count'] != '' ) {
					$saved_share_count = $this->get_saved_share_counts( $transient_id, $key );
					$saved_share_count['facebook'] = $value;
					set_transient( 'heateor_sss_share_count_' . $transient_id, $share_count_transient, $transient_expiration_time );
					$this->update_share_counts( $key, $saved_share_count );
				}
			}
		}
		die;

	}

	/**
	 * Get ID of the share count transient
	 *
	 * @since    1.0
	 */
	public function get_share_count_transient_id( $target_url ) {

		if ( $this->options['horizontal_target_url_custom'] == $target_url || $this->options['vertical_target_url_custom'] == $target_url ) {
			$post_id = 'custom';
		} else {
			$post_id = url_to_postid( $target_url );
		}
		return $post_id;

	}

	/**
	 * Check if plugin is active
	 *
	 * @since    2.5.1
	 */
	private function is_plugin_active( $plugin_file ) {

		return in_array( $plugin_file, apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) );
		
	}

	/**
	 * Inline style to load at front end.
	 *
	 * @since    2.0
	 */
	public function frontend_inline_style() {
		
		?>
		<style type="text/css">
		<?php
		if ( isset( $this->options['plain_instagram_bg'] ) ) {
			?>
			.heateor_sss_button_instagram span.heateor_sss_svg{background-color:#527fa4}
			<?php
		} else {
			?>
			.heateor_sss_button_instagram span.heateor_sss_svg,a.heateor_sss_instagram span.heateor_sss_svg{background:radial-gradient(circle at 30% 107%,#fdf497 0,#fdf497 5%,#fd5949 45%,#d6249f 60%,#285aeb 90%)}
			<?php
		}
		if ( $this->options['horizontal_bg_color_default'] != '' ) { ?>
			div.heateor_sss_horizontal_sharing a.heateor_sss_button_instagram span{background:<?php echo esc_html( $this->options['horizontal_bg_color_default'] ) ?>!important;}div.heateor_sss_standard_follow_icons_container a.heateor_sss_button_instagram span{background:<?php echo esc_html( $this->options['horizontal_bg_color_default'] ) ?>;}
		<?php } ?>
		<?php if ( $this->options['horizontal_bg_color_hover'] != '' ) { ?>
			div.heateor_sss_horizontal_sharing a.heateor_sss_button_instagram span:hover{background:<?php echo esc_html( $this->options['horizontal_bg_color_hover'] ) ?>!important;}div.heateor_sss_standard_follow_icons_container a.heateor_sss_button_instagram span:hover{background:<?php echo esc_html( $this->options['horizontal_bg_color_hover'] ) ?>;}
		<?php } ?>
		<?php if ( $this->options['vertical_bg_color_default'] != '' ) { ?>
			div.heateor_sss_vertical_sharing  a.heateor_sss_button_instagram span{background:<?php echo esc_html( $this->options['vertical_bg_color_default'] ) ?>!important;}div.heateor_sss_floating_follow_icons_container a.heateor_sss_button_instagram span{background:<?php echo esc_html( $this->options['vertical_bg_color_default'] ) ?>;}
		<?php } ?>
		<?php if ( $this->options['vertical_bg_color_hover'] != '' ) { ?>
			div.heateor_sss_vertical_sharing a.heateor_sss_button_instagram span:hover{background:<?php echo esc_html( $this->options['vertical_bg_color_hover'] ) ?>!important;}div.heateor_sss_floating_follow_icons_container a.heateor_sss_button_instagram span:hover{background:<?php echo esc_html( $this->options['vertical_bg_color_hover'] ) ?>;}
		<?php } ?>
		.heateor_sss_horizontal_sharing .heateor_sss_svg,.heateor_sss_standard_follow_icons_container .heateor_sss_svg{
			<?php if ( $this->options['horizontal_bg_color_default'] != '' ) { ?>
				background-color: <?php echo esc_html( $this->options['horizontal_bg_color_default'] ) ?>!important;
				background: <?php echo esc_html( $this->options['horizontal_bg_color_default'] ) ?>!important;
			<?php  } ?>
				color: <?php echo $this->options['horizontal_font_color_default'] ? esc_html( $this->options['horizontal_font_color_default'] ) : '#fff' ?>;
			<?php
			$border_width = 0;
			if ( $this->options['horizontal_border_width_default'] != '' ) {
				$border_width = $this->options['horizontal_border_width_default'];
			} elseif ( $this->options['horizontal_border_width_hover'] != '' ) {
				$border_width = $this->options['horizontal_border_width_hover'];
			}
			?>
			border-width: <?php echo esc_html( $border_width ) . 'px' ?>;
			border-style: solid;
			border-color: <?php echo $this->options['horizontal_border_color_default'] != '' ? esc_html( $this->options['horizontal_border_color_default'] ) : 'transparent'; ?>;
		}
		<?php if ( $this->options['horizontal_font_color_default'] == '' ) {
			?>
			.heateor_sss_horizontal_sharing .heateorSssTCBackground{
				color:#666;
			}
			<?php
		}
		if ( $this->options['horizontal_font_color_hover'] != '' ) { ?>
			div.heateor_sss_horizontal_sharing span.heateor_sss_svg svg:hover path:not(.heateor_sss_no_fill),div.heateor_sss_horizontal_sharing span.heateor_sss_svg svg:hover ellipse, div.heateor_sss_horizontal_sharing span.heateor_sss_svg svg:hover circle, div.heateor_sss_horizontal_sharing span.heateor_sss_svg svg:hover polygon, div.heateor_sss_horizontal_sharing span.heateor_sss_svg svg:hover rect:not(.heateor_sss_no_fill){
		        fill: <?php echo esc_html( $this->options['horizontal_font_color_hover'] ) ?>;
		    }
		    div.heateor_sss_horizontal_sharing span.heateor_sss_svg svg:hover path.heateor_sss_svg_stroke, div.heateor_sss_horizontal_sharing span.heateor_sss_svg svg:hover rect.heateor_sss_svg_stroke{
		    	stroke: <?php echo esc_html( $this->options['horizontal_font_color_hover'] ) ?>;
		    }
		<?php } ?>
		.heateor_sss_horizontal_sharing span.heateor_sss_svg:hover,.heateor_sss_standard_follow_icons_container span.heateor_sss_svg:hover{
			<?php if ( $this->options['horizontal_bg_color_hover'] != '' ) { ?>
				background-color: <?php echo esc_html( $this->options['horizontal_bg_color_hover'] ) ?>!important;
				background: <?php echo esc_html( $this->options['horizontal_bg_color_hover'] ) ?>!important;
			<?php }
			if ( $this->options['horizontal_font_color_hover'] != '' ) { ?>
				color: <?php echo esc_html( $this->options['horizontal_font_color_hover'] ) ?>;
			<?php  } ?>
			border-color: <?php echo $this->options['horizontal_border_color_hover'] != '' ? esc_html( $this->options['horizontal_border_color_hover'] ) : 'transparent'; ?>;
		}
		.heateor_sss_vertical_sharing span.heateor_sss_svg,.heateor_sss_floating_follow_icons_container span.heateor_sss_svg{
			<?php if ( $this->options['vertical_bg_color_default'] != '' ) { ?>
				background-color: <?php echo esc_html( $this->options['vertical_bg_color_default'] ) ?>!important;
				background: <?php echo esc_html( $this->options['vertical_bg_color_default'] ) ?>!important;
			<?php } ?>
				color: <?php echo $this->options['vertical_font_color_default'] ? esc_html( $this->options['vertical_font_color_default'] ) : '#fff' ?>;
			<?php
			$vertical_border_width = 0;
			if ( $this->options['vertical_border_width_default'] != '' ) {
				$vertical_border_width = $this->options['vertical_border_width_default'];
			} elseif ( $this->options['vertical_border_width_hover'] != '' ) {
				$vertical_border_width = $this->options['vertical_border_width_hover'];
			}
			?>
			border-width: <?php echo esc_html( $vertical_border_width ) ?>px;
			border-style: solid;
			border-color: <?php echo $this->options['vertical_border_color_default'] != '' ? esc_html( $this->options['vertical_border_color_default'] ) : 'transparent'; ?>;
		}
		<?php if ( $this->options['horizontal_font_color_default'] == '' ) { ?>
		.heateor_sss_vertical_sharing .heateorSssTCBackground{
			color:#666;
		}
		<?php } ?>
		<?php if ( $this->options['vertical_font_color_hover'] != '' ) { ?>
		    div.heateor_sss_vertical_sharing span.heateor_sss_svg svg:hover path:not(.heateor_sss_no_fill),div.heateor_sss_vertical_sharing span.heateor_sss_svg svg:hover ellipse, div.heateor_sss_vertical_sharing span.heateor_sss_svg svg:hover circle, div.heateor_sss_vertical_sharing span.heateor_sss_svg svg:hover polygon{
		        fill:<?php echo esc_html( $this->options['vertical_font_color_hover'] ) ?>;
		    }
		    div.heateor_sss_vertical_sharing span.heateor_sss_svg svg:hover path.heateor_sss_svg_stroke{
		    	stroke:<?php echo esc_html( $this->options['vertical_font_color_hover'] ) ?>;
		    }
		<?php } ?>
		.heateor_sss_vertical_sharing span.heateor_sss_svg:hover,.heateor_sss_floating_follow_icons_container span.heateor_sss_svg:hover{
			<?php if ( $this->options['vertical_bg_color_hover'] != '' ) { ?>
				background-color: <?php echo esc_html( $this->options['vertical_bg_color_hover'] ) ?>!important;
				background: <?php echo esc_html( $this->options['vertical_bg_color_hover'] ) ?>!important;
			<?php }
			if ( $this->options['vertical_font_color_hover'] != '' ) { ?>
				color: <?php echo esc_html( $this->options['vertical_font_color_hover'] ) ?>;
			<?php  } ?>
			border-color: <?php echo $this->options['vertical_border_color_hover'] != '' ? esc_html( $this->options['vertical_border_color_hover'] ) : 'transparent'; ?>;
		}
		<?php
		if ( isset( $this->options['horizontal_counts'] ) ) {
			$svg_height = $this->options['horizontal_sharing_shape'] == 'rectangle' ? $this->options['horizontal_sharing_height'] : $this->options['horizontal_sharing_size'];
			if ( isset( $this->options['horizontal_counter_position'] ) && in_array( $this->options['horizontal_counter_position'], array( 'inner_top', 'inner_bottom' ) ) ) {
				$line_height_percent = $this->options['horizontal_counter_position'] == 'inner_top' ? 38 : 19;
				?>
				div.heateor_sss_horizontal_sharing svg{height:70%;margin-top:<?php echo esc_html( $svg_height )*15/100 ?>px}div.heateor_sss_horizontal_sharing .heateor_sss_square_count{line-height:<?php echo esc_html( $svg_height*$line_height_percent )/100 ?>px;}
				<?php
			} elseif ( isset( $this->options['horizontal_counter_position'] ) && in_array( $this->options['horizontal_counter_position'], array( 'inner_left', 'inner_right' ) ) ) { ?>
				div.heateor_sss_horizontal_sharing svg{width:50%;margin:auto;}div.heateor_sss_horizontal_sharing .heateor_sss_square_count{float:left;width:50%;line-height:<?php echo esc_html( $svg_height - 2 * $border_width ); ?>px;}
				<?php
			} elseif ( isset( $this->options['horizontal_counter_position'] ) && in_array( $this->options['horizontal_counter_position'], array( 'left', 'right' ) ) ) { ?>
				div.heateor_sss_horizontal_sharing .heateor_sss_square_count{float:<?php echo esc_html( $this->options['horizontal_counter_position'] ) ?>;margin:0 8px;line-height:<?php echo esc_html( $svg_height ); ?>px;}
				<?php
			} elseif ( ! isset( $this->options['horizontal_counter_position'] ) || $this->options['horizontal_counter_position'] == 'top' ) { ?>
				div.heateor_sss_horizontal_sharing .heateor_sss_square_count{display: block}
				<?php
			}
		}
		if ( isset( $this->options['vertical_counts'] ) ) {
			$vertical_svg_height = $this->options['vertical_sharing_shape'] == 'rectangle' ? $this->options['vertical_sharing_height'] : $this->options['vertical_sharing_size'];
			$vertical_svg_width = $this->options['vertical_sharing_shape'] == 'rectangle' ? $this->options['vertical_sharing_width'] : $this->options['vertical_sharing_size'];
			if ( ( isset( $this->options['vertical_counter_position'] ) && in_array( $this->options['vertical_counter_position'], array( 'inner_top', 'inner_bottom' ) ) ) || ! isset( $this->options['vertical_counter_position'] ) ) {
				$vertical_line_height_percent = ! isset( $this->options['vertical_counter_position'] ) || $this->options['vertical_counter_position'] == 'inner_top' ? 38 : 19;
				?>
				div.heateor_sss_vertical_sharing svg{height:70%;margin-top:<?php echo esc_html( $vertical_svg_height )*15/100 ?>px}div.heateor_sss_vertical_sharing .heateor_sss_square_count{line-height:<?php echo esc_html( $vertical_svg_height*$vertical_line_height_percent )/100; ?>px;}
				<?php
			} elseif ( isset( $this->options['vertical_counter_position'] ) && in_array( $this->options['vertical_counter_position'], array( 'inner_left', 'inner_right' ) ) ) { ?>
				div.heateor_sss_vertical_sharing svg{width:50%;margin:auto;}div.heateor_sss_vertical_sharing .heateor_sss_square_count{float:left;width:50%;line-height:<?php echo esc_html( $vertical_svg_height ); ?>px;}
				<?php
			}  elseif ( isset( $this->options['vertical_counter_position'] ) && in_array( $this->options['vertical_counter_position'], array( 'left', 'right' ) ) ) { ?>
				div.heateor_sss_vertical_sharing .heateor_sss_square_count{float:<?php echo esc_html( $this->options['vertical_counter_position'] ) ?>;margin:0 8px;line-height:<?php echo esc_html( $vertical_svg_height ); ?>px; <?php echo $this->options['vertical_counter_position'] == 'left' ? 'min-width:' . esc_html( $vertical_svg_width )*30/100 . 'px;display: block' : '';?>}
				<?php
			} elseif ( isset( $this->options['vertical_counter_position'] ) && $this->options['vertical_counter_position'] == 'top' ) { ?>
				div.heateor_sss_vertical_sharing .heateor_sss_square_count{display:block}
				<?php
			}
		}
		echo isset( $this->options['hide_mobile_sharing'] ) && $this->options['vertical_screen_width'] != '' ? '@media screen and (max-width:' . intval( $this->options['vertical_screen_width'] ) . 'px) {.heateor_sss_vertical_sharing{display:none!important}}' : '';
		
		$bottom_sharing_postion_inverse = $this->options['bottom_sharing_alignment'] == 'left' ? 'right' : 'left';
		$bottom_sharing_responsive_css = '';
		if ( isset( $this->options['vertical_enable'] ) && $this->options['bottom_sharing_position_radio'] == 'responsive' ) {
			$vertical_sharing_icon_height = $this->options['vertical_sharing_shape'] == 'rectangle' ? $this->options['vertical_sharing_height'] : $this->options['vertical_sharing_size'];
			$num_sharing_icons = isset($this->options['vertical_re_providers']) ? count($this->options['vertical_re_providers']) : 0;
			$total_share_count_enabled = isset($this->options['vertical_total_shares']) ? 1 : 0;
			$more_icon_enabled = isset($this->options['vertical_more']) ? 1 : 0;
			$bottom_sharing_responsive_css = 'div.heateor_sss_bottom_sharing{width:100%!important;left:0!important;}div.heateor_sss_bottom_sharing a{width:'.(100/($num_sharing_icons+$total_share_count_enabled+$more_icon_enabled)).'% !important;}div.heateor_sss_bottom_sharing .heateor_sss_svg{width: 100% !important;}div.heateor_sss_bottom_sharing div.heateorSssTotalShareCount{font-size:1em!important;line-height:' . ( $vertical_sharing_icon_height*70/100 ) . 'px!important}div.heateor_sss_bottom_sharing div.heateorSssTotalShareText{font-size:.7em!important;line-height:0px!important}';
		}
		echo isset( $this->options['vertical_enable'] ) && isset( $this->options['bottom_mobile_sharing'] ) && $this->options['horizontal_screen_width'] != '' ? 'div.heateor_sss_mobile_footer{display:none;}@media screen and (max-width:' . intval( $this->options['horizontal_screen_width'] ) . 'px){div.heateor_sss_bottom_sharing .heateorSssTCBackground{background-color:white}' . $bottom_sharing_responsive_css . 'div.heateor_sss_mobile_footer{display:block;height:' . esc_html( $this->options['vertical_sharing_shape'] == 'rectangle' ? $this->options['vertical_sharing_height'] : $this->options['vertical_sharing_size'] ) . 'px;}.heateor_sss_bottom_sharing{padding:0!important;' . esc_html( $this->options['bottom_sharing_position_radio'] == 'nonresponsive' && $this->options['bottom_sharing_position'] != '' ? esc_html( $this->options['bottom_sharing_alignment'] ) . ':' . esc_html( $this->options['bottom_sharing_position'] ) . 'px!important;' . esc_html( $bottom_sharing_postion_inverse ) . ':auto!important;' : '' ) . 'display:block!important;width:auto!important;bottom:' . ( isset( $this->options['vertical_total_shares'] ) && ! $this->is_amp_page() ? '-5' : '-2' ) . 'px!important;top: auto!important;}.heateor_sss_bottom_sharing .heateor_sss_square_count{line-height:inherit;}.heateor_sss_bottom_sharing .heateorSssSharingArrow{display:none;}.heateor_sss_bottom_sharing .heateorSssTCBackground{margin-right:1.1em!important}}' : '';
		echo esc_html( $this->options['custom_css'] );
		echo isset( $this->options['hide_slider'] ) ? 'div.heateorSssSharingArrow{display:none}' : '';
		if ( isset( $this->options['hor_enable'] ) && $this->options['hor_sharing_alignment'] == "center" ) {
			echo 'div.heateor_sss_sharing_title{text-align:center}div.heateor_sss_sharing_ul{width:100%;text-align:center;}div.heateor_sss_horizontal_sharing div.heateor_sss_sharing_ul a{float:none!important;display:inline-block;}';
		}
		?>
		</style>
		<?php

	}

	/**
	 * Stylesheets to load at front end.
	 *
	 * @since    1.0
	 */
	public function frontend_css() {
		
		wp_enqueue_style( 'heateor_sss_frontend_css', plugins_url( 'css/sassy-social-share-public.css', __FILE__ ), false, $this->version );
	
	}

	/**
	 * Stylesheets to load at front end for AMP.
	 *
	 * @since    2.0
	 */
	public function frontend_amp_css() {
		
		if ( ! $this->is_amp_page() ) {
			return;
		}

		$css = '';

		if ( current_action() == 'wp_print_styles' ) {
			echo '<style type="text/css">';
		}
		// background color of amp icons
		$css .= 'a.heateor_sss_amp{padding:0 4px;}div.heateor_sss_horizontal_sharing a amp-img{display:inline-block;}.heateor_sss_amp_gab img{background-color:#25CC80}.heateor_sss_amp_parler img{background-color:#892E5E}.heateor_sss_amp_gettr img{background-color:#E50000}.heateor_sss_amp_instagram img{background-color:#624E47}.heateor_sss_amp_yummly img{background-color:#E16120}.heateor_sss_amp_youtube img{background-color:#ff0000}.heateor_sss_amp_rutube img{background-color:#14191f}.heateor_sss_amp_buffer img{background-color:#000}.heateor_sss_amp_delicious img{background-color:#53BEEE}.heateor_sss_amp_rss img{background-color:#e3702d}.heateor_sss_amp_facebook img{background-color:#3C589A}.heateor_sss_amp_digg img{background-color:#006094}.heateor_sss_amp_email img{background-color:#649A3F}.heateor_sss_amp_float_it img{background-color:#53BEEE}.heateor_sss_amp_linkedin img{background-color:#0077B5}.heateor_sss_amp_pinterest img{background-color:#CC2329}.heateor_sss_amp_print img{background-color:#FD6500}.heateor_sss_amp_reddit img{background-color:#FF5700}.heateor_sss_amp_mastodon img{background-color:#2b90d9}.heateor_sss_amp_stocktwits img{background-color: #40576F}.heateor_sss_amp_mewe img{background-color:#007da1}.heateor_sss_amp_mix img{background-color:#ff8226}.heateor_sss_amp_tumblr img{background-color:#29435D}.heateor_sss_amp_twitter img{background-color:#55acee}.heateor_sss_amp_vkontakte img{background-color:#0077FF}.heateor_sss_amp_yahoo img{background-color:#8F03CC}.heateor_sss_amp_xing img{background-color:#00797D}.heateor_sss_amp_instagram img{background-color:#527FA4}.heateor_sss_amp_whatsapp img{background-color:#55EB4C}.heateor_sss_amp_aim img{background-color: #10ff00}.heateor_sss_amp_amazon_wish_list img{background-color: #ffe000}.heateor_sss_amp_aol_mail img{background-color: #2A2A2A}.heateor_sss_amp_app_net img{background-color: #5D5D5D}.heateor_sss_amp_balatarin img{background-color: #fff}.heateor_sss_amp_bibsonomy img{background-color: #000}.heateor_sss_amp_bitty_browser img{background-color: #EFEFEF}.heateor_sss_amp_blinklist img{background-color: #3D3C3B}.heateor_sss_amp_blogger_post img{background-color: #FDA352}.heateor_sss_amp_blogmarks img{background-color: #535353}.heateor_sss_amp_bookmarks_fr img{background-color: #E8EAD4}.heateor_sss_amp_box_net img{background-color: #1A74B0}.heateor_sss_amp_buddymarks img{background-color: #ffd400}.heateor_sss_amp_care2_news img{background-color: #6EB43F}.heateor_sss_amp_comment img{background-color: #444}.heateor_sss_amp_diary_ru img{background-color: #E8D8C6}.heateor_sss_amp_diaspora img{background-color: #2E3436}.heateor_sss_amp_dihitt img{background-color: #FF6300}.heateor_sss_amp_diigo img{background-color: #4A8BCA}.heateor_sss_amp_douban img{background-color: #497700}.heateor_sss_amp_draugiem img{background-color: #ffad66}.heateor_sss_amp_evernote img{background-color: #8BE056}.heateor_sss_amp_facebook_messenger img{background-color: #0084FF}.heateor_sss_amp_fark img{background-color: #555}.heateor_sss_amp_fintel img{background-color: #087515}.heateor_sss_amp_flipboard img{background-color: #CC0000}.heateor_sss_amp_folkd img{background-color: #0F70B2}.heateor_sss_amp_google_news img{background-color: #4285F4}.heateor_sss_amp_google_classroom img{background-color: #FFC112}.heateor_sss_amp_google_gmail img{background-color: #E5E5E5}.heateor_sss_amp_hacker_news img{background-color: #F60}.heateor_sss_amp_hatena img{background-color: #00A6DB}.heateor_sss_amp_instapaper img{background-color: #EDEDED}.heateor_sss_amp_jamespot img{background-color: #FF9E2C}.heateor_sss_amp_kakao img{background-color: #FCB700}.heateor_sss_amp_kik img{background-color: #2A2A2A}.heateor_sss_amp_kindle_it img{background-color: #2A2A2A}.heateor_sss_amp_known img{background-color: #fff101}.heateor_sss_amp_line img{background-color: #00C300}.heateor_sss_amp_livejournal img{background-color: #EDEDED}.heateor_sss_amp_mail_ru img{background-color: #356FAC}.heateor_sss_amp_mendeley img{background-color: #A70805}.heateor_sss_amp_meneame img{background-color: #FF7D12}.heateor_sss_amp_mixi img{background-color: #EDEDED}.heateor_sss_amp_myspace img{background-color: #2A2A2A}.heateor_sss_amp_netlog img{background-color: #2A2A2A}.heateor_sss_amp_netvouz img{background-color: #c0ff00}.heateor_sss_amp_newsvine img{background-color: #055D00}.heateor_sss_amp_nujij img{background-color: #D40000}.heateor_sss_amp_odnoklassniki img{background-color: #F2720C}.heateor_sss_amp_oknotizie img{background-color: #fdff88}.heateor_sss_amp_outlook_com img{background-color: #0072C6}.heateor_sss_amp_papaly img{background-color: #3AC0F6}.heateor_sss_amp_pinboard img{background-color: #1341DE}.heateor_sss_amp_plurk img{background-color: #CF682F}.heateor_sss_amp_pocket img{background-color: #ee4056}.heateor_sss_amp_polyvore img{background-color: #2A2A2A}.heateor_sss_amp_printfriendly img{background-color: #61D1D5}.heateor_sss_amp_protopage_bookmarks img{background-color: #413FFF}.heateor_sss_amp_pusha img{background-color: #0072B8}.heateor_sss_amp_qzone img{background-color: #2B82D9}.heateor_sss_amp_refind img{background-color: #1492ef}.heateor_sss_amp_rediff_mypage img{background-color: #D20000}.heateor_sss_amp_renren img{background-color: #005EAC}.heateor_sss_amp_segnalo img{background-color: #fdff88}.heateor_sss_amp_sina_weibo img{background-color: #ff0}.heateor_sss_amp_sitejot img{background-color: #ffc800}.heateor_sss_amp_skype img{background-color: #00AFF0}.heateor_sss_amp_sms img{background-color: #6ebe45}.heateor_sss_amp_slashdot img{background-color: #004242}.heateor_sss_amp_stumpedia img{background-color: #EDEDED}.heateor_sss_amp_svejo img{background-color: #fa7aa3}.heateor_sss_amp_symbaloo_feeds img{background-color: #6DA8F7}.heateor_sss_amp_telegram img{background-color: #3DA5f1}.heateor_sss_amp_trello img{background-color: #1189CE}.heateor_sss_amp_tuenti img{background-color: #0075C9}.heateor_sss_amp_twiddla img{background-color: #EDEDED}.heateor_sss_amp_typepad_post img{background-color: #2A2A2A}.heateor_sss_amp_viadeo img{background-color: #2A2A2A}.heateor_sss_amp_viber img{background-color: #8B628F}.heateor_sss_amp_webnews img{background-color: #CC2512}.heateor_sss_amp_wordpress img{background-color: #464646}.heateor_sss_amp_wykop img{background-color: #367DA9}.heateor_sss_amp_yahoo_mail img{background-color: #400090}.heateor_sss_amp_yahoo_messenger img{background-color: #400090}.heateor_sss_amp_yoolink img{background-color: #A2C538}.heateor_sss_amp_youmob img{background-color: #3B599D}.heateor_sss_amp_gentlereader img{background-color: #46aecf}.heateor_sss_amp_threema img{background-color: #2A2A2A}';

		// css for horizontal sharing bar
		if ( $this->options['horizontal_sharing_shape'] == 'round' ) {
			$css .= '.heateor_sss_amp amp-img{border-radius:999px;}';
		} elseif ( $this->options['horizontal_border_radius'] != '' ) {
			$css .= '.heateor_sss_amp amp-img{border-radius:' . $this->options['horizontal_border_radius'] . 'px;}';
		}

		echo esc_html( $css );

		if ( current_action() == 'wp_print_styles' ) {
			echo '</style>';
		}
	
	}

	/**
	 * Append myCRED referral ID to share and like button urls
	 *
	 * @since    3.0
	 */
	public function append_mycred_referral_id( $post_url, $sharing_type, $standard_widget ) {
		
		$mycred_referral_id = do_shortcode( '[mycred_affiliate_id]' );
		if ( $mycred_referral_id ) {
			$connector = strpos( urldecode( $post_url ), '?' ) === false ? '?' : '&';
			$post_url .= $connector . 'mref=' . $mycred_referral_id;
		}

		return $post_url;

	}

	/**
	 * Add safe styles to the existing list
	 *
	 * @since    3.3.34
	 */
	public function add_safe_styles( $styles ) {
		
		$styles[] = 'display';
		$styles[] = 'position';
		$styles[] = 'left';
		$styles[] = 'right';
		$styles[] = 'top';
		$styles[] = 'box-shadow';
		$styles[] = 'opacity';
		$styles[] = 'background-repeat';
		$styles[] = 'box-sizing';
		$styles[] = '-webkit-clip-path';
		$styles[] = 'clip';
		$styles[] = 'visibility';

		return $styles;

	}

	/**
	 * Make Mastodon share work in AMP
	 *
	 * @since    3.3.49
	 */
	public function mastodon_share_amp() {

		if ( isset( $_GET['heateor_mastodon_share'] ) && trim( $_GET['heateor_mastodon_share'] ) ) {
			?>
			<style type="text/css">#heateor_sss_sharing_more_providers{position:fixed;top:50%;left:47%;background:#fafafa;width:650px;margin:-180px 0 0 -300px;z-index:10000000;text-shadow:none!important;height:308px}#heateor_sss_mastodon_popup_bg,#heateor_sss_popup_bg{background:url(<?php echo plugins_url( '../images/transparent_bg.png', __FILE__ ) ?>);bottom:0;display:block;left:0;position:fixed;right:0;top:0;z-index:10000}#heateor_sss_sharing_more_providers .title{font-size:14px!important;height:auto!important;background:#58b8f8!important;border-bottom:1px solid #d7d7d7!important;color:#fff;font-weight:700;letter-spacing:inherit;line-height:34px!important;padding:0!important;text-align:center;text-transform:none;margin:0!important;text-shadow:none!important;width:100%}#heateor_sss_sharing_more_providers *{font-family:Arial,Helvetica,sans-serif}#heateor_sss_sharing_more_providers #heateor_sss_sharing_more_content{background:#fafafa;border-radius:4px;color:#555;height:auto;width:100%}#heateor_sss_sharing_more_providers .filter{margin:0;padding:10px 0 0;position:relative;width:100%}#heateor_sss_sharing_more_providers .all-services{clear:both;height:250px;overflow:auto}#heateor_sss_sharing_more_content .all-services ul{margin:10px!important;overflow:hidden;list-style:none;padding-left:0!important;position:static!important;width:auto!important}#heateor_sss_sharing_more_content .all-services ul li{margin:0;background:0 0!important;float:left;width:33.3333%!important;text-align:left!important}#heateor_sss_sharing_more_providers .close-button img{margin:0}#heateor_sss_sharing_more_providers .close-button.separated{background:0 0!important;border:none!important;box-shadow:none!important;width:auto!important;height:auto!important;z-index:1000}#heateor_sss_sharing_more_providers .close-button{height:auto!important;width:auto!important;left:auto!important;display:block!important;color:#555!important;cursor:pointer!important;font-size:29px!important;line-height:29px!important;margin:0!important;padding:0!important;position:absolute;right:-13px;top:-11px}#heateor_sss_sharing_more_providers .filter input.search{width:94%;display:block;float:none;font-family:"open sans","helvetica neue",helvetica,arial,sans-serif;font-weight:300;height:auto;line-height:inherit;margin:0 auto;padding:5px 8px 5px 10px;border:1px solid #ccc!important;color:#000;background:#fff!important;font-size:16px!important;text-align:left!important}#heateor_sss_sharing_more_providers .footer-panel{background:#fff;border-top:1px solid #d7d7d7;padding:6px 0;width:100%;color:#fff}#heateor_sss_sharing_more_providers .footer-panel p{background-color:transparent;top:0;text-align:left!important;color:#000;font-family:'helvetica neue',arial,helvetica,sans-serif;font-size:12px;line-height:1.2;margin:0!important;padding:0 6px!important;text-indent:0!important}#heateor_sss_sharing_more_providers .footer-panel a{color:#fff;text-decoration:none;font-weight:700;text-indent:0!important}#heateor_sss_sharing_more_providers .all-services ul li a span{width:51%}#heateor_sss_sharing_more_providers .all-services ul li a{border-radius:3px;color:#666!important;display:block;font-size:18px;height:auto;line-height:28px;overflow:hidden;padding:8px;text-decoration:none!important;text-overflow:ellipsis;white-space:nowrap;border:none!important;text-indent:0!important;background:0 0!important;text-shadow:none}.heateor_sss_mastodon_popup_button{background:linear-gradient(#ec1b23,#d43116);padding:8px 0 10px;font-size:18px;border:0;color:#fff;border-radius:8px;margin:4px auto;font-weight:bolder;width:35%;cursor:pointer;border-bottom-style:groove;border-bottom-width:5px;border-bottom-color: rgb(0,0,0,.2)}@media screen and (max-width:783px){#heateor_sss_sharing_more_providers{width:80%;left:60%;margin-left:-50%;text-shadow:none!important}#heateor_sss_sharing_more_providers .filter input.search{border:1px solid #ccc;width:92%}}</style>
			<div id="heateor_sss_sharing_more_providers" style="height: auto;"><button id="heateor_sss_mastodon_popup_close" class="close-button separated"><img src="https://pyrovolt.com/wp-content/plugins/sassy-social-share/public/../images/close.png"></button><div id="heateor_sss_sharing_more_content"><div class="all-services" style="height:auto"><div class="filter"><center><?php _e( 'Your Mastodon Instance', 'sassy-social-share' ); ?></center><input type="text" id="heateor_sss_mastodon_instance" placeholder="https://mastodon.social" class="search"><center><input type="button" class="heateor_sss_mastodon_popup_button" value="<?php _e( 'Submit', 'sassy-social-share' ); ?>" onclick="var heateorMastodonInstance = document.getElementById('heateor_sss_mastodon_instance').value.trim(), heateorMastodonAnchor = '<?php echo esc_js( urldecode( trim( $_GET['heateor_mastodon_share'] ) ) ) ?>'; heateorMastShareURL = heateorMastodonInstance ? heateorMastodonAnchor.replace('https://mastodon.social', heateorMastodonInstance) : heateorMastodonAnchor;location.href = heateorMastShareURL;"></center></div></div><div class="footer-panel"></div></div></div><div id="heateor_sss_mastodon_popup_bg"></div>
			<?php
			exit();
		}

	}

}