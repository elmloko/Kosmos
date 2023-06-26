<?php

/**
 * The file that defines classes for widgets
 *
 * Class definitions that include functions used for widgets.
 *
 * @since    1.0
 *
 */

/**
 * Standard Widget class.
 *
 * This is used to define functions for Standard Sharing Widget.
 *
 * @since    1.0
 */
class Sassy_Social_Share_Standard_Widget extends WP_Widget { 
	
	/**
	 * Options saved in database.
	 *
	 * @since    1.0
	 */
	private $options;

	/**
	 * Member to assign object of Sassy_Social_Share_Public Class.
	 *
	 * @since    1.0
	 */
	private $public_class_object;

	/**
	 * Assign plugin options to private member $options and define widget title, description etc.
	 *
	 * @since    1.0
	 */
	public function __construct() { 
		
		global $heateor_sss;

		$this->options = $heateor_sss->options;

		$this->public_class_object = new Sassy_Social_Share_Public( $heateor_sss->options, HEATEOR_SSS_VERSION );

		parent::__construct( 
			'Heateor_SSS_Sharing', // unique id 
			__( 'Sassy Social Share - Standard Widget' ), // Widget title 
			array( 'description' => __( 'Standard sharing widget. Let your website users share content on popular Social networks like Facebook, Twitter, Tumblr, Whatsapp and many more', 'sassy-social-share' ) )
		); 
	}  

	/**
	 * Render widget at front-end
	 *
	 * @since    1.0
	 */
	public function widget( $args, $instance ) {
		// return if standard sharing is disabled
		if ( ! isset( $this->options['hor_enable'] ) ) {
			return;
		}
		extract( $args );
		if ( isset( $instance['hide_for_logged_in'] ) && is_user_logged_in() ) return;
		global $post;

		if ( isset( $this->options['js_when_needed'] ) && ! wp_script_is( 'heateor_sss_sharing_js' ) ) {
			$in_footer = isset( $this->options['footer_script'] ) ? true : false;
			$inline_script = 'function heateorSssLoadEvent(e) {var t=window.onload;if (typeof window.onload!="function") {window.onload=e}else{window.onload=function() {t();e()}}};';
			if ( $post ) {
				$inline_script .= 'var heateorSssInstagramUsername="' . ( $this->options['instagram_username'] != '' ? $this->options['instagram_username'] : $this->options['vertical_instagram_username'] ) . '",heateorSssYoutubeUsername="' . ( $this->options['youtube_username'] != '' ? $this->options['youtube_username'] : $this->options['vertical_youtube_username'] ) . '", heateorSssSharingAjaxUrl = \'' . get_admin_url() . 'admin-ajax.php\', heateorSssCloseIconPath = \'' . plugins_url( '../images/close.png', __FILE__ ) . '\', heateorSssPluginIconPath = \'' . plugins_url( '../images/logo.png', __FILE__ ) . '\', heateorSssHorizontalSharingCountEnable = '. ( isset( $this->options['hor_enable'] ) && ( isset( $this->options['horizontal_counts'] ) || isset( $this->options['horizontal_total_shares'] ) ) ? 1 : 0 ) . ', heateorSssVerticalSharingCountEnable = ' . ( isset( $this->options['vertical_enable'] ) && ( isset( $this->options['vertical_counts'] ) || isset( $this->options['vertical_total_shares'] ) ) ? 1 : 0 ) . ', heateorSssSharingOffset = ' . ( isset( $this->options['alignment'] ) && $this->options['alignment'] != '' && isset( $this->options[$this->options['alignment'] . '_offset'] ) && $this->options[$this->options['alignment'] . '_offset'] != '' ? $this->options[$this->options['alignment'] . '_offset'] : 0 ) . '; var heateorSssMobileStickySharingEnabled = ' . ( isset( $this->options['vertical_enable'] ) && isset( $this->options['bottom_mobile_sharing'] ) && $this->options['horizontal_screen_width'] != '' ? 1 : 0 ) . ';';
				$inline_script .= 'var heateorSssCopyLinkMessage = "' . htmlspecialchars( __( 'Link copied.', 'sassy-social-share' ), ENT_QUOTES ) . '";';
				if ( isset( $this->options['horizontal_counts'] ) && isset( $this->options['horizontal_counter_position'] ) ) {
					$inline_script .= in_array( $this->options['horizontal_counter_position'], array( 'inner_left', 'inner_right' ) ) ? 'var heateorSssReduceHorizontalSvgWidth = true;' : '';
					$inline_script .= in_array( $this->options['horizontal_counter_position'], array( 'inner_top', 'inner_bottom' ) ) ? 'var heateorSssReduceHorizontalSvgHeight = true;' : '';
				}
				if ( isset( $this->options['vertical_counts'] ) ) {
					$inline_script .= isset( $this->options['vertical_counter_position'] ) && in_array( $this->options['vertical_counter_position'], array( 'inner_left', 'inner_right' ) ) ? 'var heateorSssReduceVerticalSvgWidth = true;' : '';
					$inline_script .= ! isset( $this->options['vertical_counter_position'] ) || in_array( $this->options['vertical_counter_position'], array( 'inner_top', 'inner_bottom' ) ) ? 'var heateorSssReduceVerticalSvgHeight = true;' : '';
				}
				$inline_script .= 'var heateorSssUrlCountFetched = [], heateorSssSharesText = \''. htmlspecialchars(__( 'Shares', 'sassy-social-share' ), ENT_QUOTES ) . '\', heateorSssShareText = \'' . htmlspecialchars( __( 'Share', 'sassy-social-share' ), ENT_QUOTES ) . '\';';
				$inline_script .= 'function heateorSssPopup(e) {window.open(e,"popUpWindow","height=400,width=600,left=400,top=100,resizable,scrollbars,toolbar=0,personalbar=0,menubar=no,location=no,directories=no,status")}';
				if ( $this->public_class_object->facebook_like_recommend_enabled() || $this->public_class_object->facebook_share_enabled() ) {
					$inline_script .= 'function heateorSssInitiateFB() {FB.init({appId:"",channelUrl:"",status:!0,cookie:!0,xfbml:!0,version:"v17.0"})}window.fbAsyncInit=function() {heateorSssInitiateFB(),' . ( defined( 'HEATEOR_SOCIAL_SHARE_MYCRED_INTEGRATION_VERSION' ) && $this->facebook_like_recommend_enabled() ? 1 : 0 ) . '&&(FB.Event.subscribe("edge.create",function(e){heateorSsmiMycredPoints("Facebook_like_recommend","",e?e:"")}),FB.Event.subscribe("edge.remove",function(e){heateorSsmiMycredPoints("Facebook_like_recommend","",e?e:"","Minus point(s) for undoing Facebook like-recommend")}) ),' . ( defined( 'HEATEOR_SHARING_GOOGLE_ANALYTICS_VERSION' ) ? 1 : 0 ) . '&&(FB.Event.subscribe("edge.create",function(e) {heateorSssSsgaSocialPluginsTracking("Facebook","Like",e?e:"")}),FB.Event.subscribe("edge.remove",function(e) {heateorSssSsgaSocialPluginsTracking("Facebook","Unlike",e?e:"")}) )},function(e) {var n,i="facebook-jssdk",o=e.getElementsByTagName("script")[0];e.getElementById(i)||(n=e.createElement("script"),n.id=i,n.async=!0,n.src="//connect.facebook.net/' . ( $this->options['language'] ? $this->options['language'] : 'en_GB' ) . '/sdk.js",o.parentNode.insertBefore(n,o) )}(document);';
				}
				$inline_script .= '; var heateorSsmiAjaxUrl = "' . get_admin_url() . 'admin-ajax.php";';
				wp_enqueue_script( 'heateor_sss_sharing_js', plugins_url( '../public/js/sassy-social-share-public.js', __FILE__ ), array( 'jquery' ), $this->public_class_object->version, $in_footer );
				wp_add_inline_script( 'heateor_sss_sharing_js', $inline_script, $position = 'before' );
			}
		}
		
		if ( NULL === $post ) {
			$post_id = 0;
		} else {
			$post_id = $post->ID;
		}
		if ( isset( $instance['target_url'] ) ) {
			if ( $instance['target_url'] == 'default' ) {
				$sharing_url = esc_url_raw( $this->public_class_object->get_http_protocol() . $_SERVER["HTTP_HOST"] . $_SERVER["REQUEST_URI"] );
				if ( is_home() ) {
					$sharing_url = home_url();
					$post_id = 0;
				} elseif ( ! is_singular() ) {
					$post_id = 0;
				} elseif ( isset( $_SERVER['QUERY_STRING'] ) && $_SERVER['QUERY_STRING'] ) {
					$sharing_url = esc_url_raw( $this->public_class_object->get_http_protocol() . $_SERVER["HTTP_HOST"] . $_SERVER["REQUEST_URI"] );
				} elseif ( get_permalink( $post -> ID ) ) {
					$sharing_url = get_permalink( $post->ID );
				}
			} elseif ( $instance['target_url'] == 'homepage' ) {
				$sharing_url = home_url();
				$post_id = 0;
			} elseif ( $instance['target_url'] == 'custom' ) {
				$sharing_url = isset( $instance['target_url_custom'] ) ? trim( $instance['target_url_custom'] ) : get_permalink( $post->ID );
				$post_id = 0;
			}
		} else {
			$sharing_url = get_permalink( $post->ID );
		}
		$share_count_url = $sharing_url;
		if ( isset( $instance['target_url'] ) && $instance['target_url'] == 'default' && is_singular() ) {
			$share_count_url = get_permalink( $post -> ID );
		}
		$custom_post_url = $this->public_class_object->apply_target_share_url_filter( $sharing_url, 'horizontal', ! is_singular() ? true : false );
		if ( $custom_post_url != $sharing_url ) {
			$sharing_url = $custom_post_url;
			$share_count_url = $sharing_url;
		}
		// share count transient ID
		$this->public_class_object->share_count_transient_id = $this->public_class_object->get_share_count_transient_id( $sharing_url );
		$cached_share_count = $this->public_class_object->get_cached_share_count( $this->public_class_object->share_count_transient_id );
		$short_url = $this->public_class_object->get_short_url( $sharing_url, $post_id );
		
		echo $before_widget;

		$sharing_divs = $this->public_class_object->prepare_sharing_html( $short_url ? $short_url : $sharing_url, 'horizontal', isset( $instance['show_counts'] ), isset( $instance['total_shares'] ), ! is_singular() ? true : false );
		$sharing_divs = explode( '%instance%', $sharing_divs );
		if ( ! empty( $instance['before_widget_content'] ) ) { 
			echo '<div>' . esc_html( $instance['before_widget_content'] ) . '</div>'; 
		}

		foreach ( $sharing_divs as $sharing_div ) {
			if ( $sharing_div ) {
				global $heateor_sss_allowed_tags;
				echo wp_kses( "<div class='heateor_sss_sharing_container heateor_sss_horizontal_sharing' " . ( $this->public_class_object->is_amp_page() ? "" : "data-heateor-sss-href='" . ( isset( $share_count_url ) && $share_count_url ? $share_count_url : $sharing_url ) . "'" ) . ( ( $cached_share_count === false || $this->public_class_object->is_amp_page() ) ? "" : 'data-heateor-sss-no-counts="1"' ) . ">", $heateor_sss_allowed_tags );
				if ( ! empty( $instance['title'] ) ) { 
					$title = apply_filters( 'widget_title', $instance[ 'title' ] ); 
					echo $before_title . esc_html( $title ) . $after_title;
				}
				echo wp_kses( $sharing_div, $heateor_sss_allowed_tags );
				echo '</div>';
			}
		}

		if ( ! empty( $instance['after_widget_content'] ) ) { 
			echo '<div>' . esc_html( $instance['after_widget_content'] ) . '</div>'; 
		}
		
		if ( ( isset( $instance['show_counts'] ) || isset( $instance['total_shares'] ) ) && $cached_share_count === false ) {
			echo '<script>heateorSssLoadEvent(function(){heateorSssGetSharingCounts();});</script>';
		}
		echo $after_widget;
	} 

	/** 
	 * Everything which should happen when user edit widget at admin panel.
	 *
	 * @since    1.0
	 */
	public function update( $new_instance, $old_instance ) { 
		
		$instance = $old_instance; 

		$instance['title'] = isset( $new_instance['title'] ) ? strip_tags( $new_instance['title'] ) : ''; 
		if ( isset( $new_instance['show_counts'] ) ) {
			$instance['show_counts'] = $new_instance['show_counts'];
		} else {
			unset( $instance['show_counts'] );
		}
		if ( isset( $new_instance['total_shares'] ) ) {
			$instance['total_shares'] = $new_instance['total_shares'];
		} else {
			unset( $instance['total_shares'] );
		}
		$instance['target_url'] = isset( $new_instance['target_url'] ) ? $new_instance['target_url'] : ''; 
		$instance['target_url_custom'] = isset( $new_instance['target_url_custom'] ) ? $new_instance['target_url_custom'] : '';  
		$instance['before_widget_content'] = isset( $new_instance['before_widget_content'] ) ? $new_instance['before_widget_content'] : '';  
		$instance['after_widget_content'] = isset( $new_instance['after_widget_content'] ) ? $new_instance['after_widget_content'] : ''; 
		if ( isset( $new_instance['hide_for_logged_in'] ) ) {
			$instance['hide_for_logged_in'] = $new_instance['hide_for_logged_in'];
		} else {
			unset( $instance['hide_for_logged_in'] );
		} 

		return $instance; 

	}

	/** 
	 * Widget options form at admin panel.
	 *
	 * @since    1.0
	 */
	public function form( $instance ) { 
		
		// default widget settings
		$defaults = array( 'title' => 'Share the joy', 'show_counts' => '', 'total_shares' => '', 'target_url' => 'default', 'target_url_custom' => '', 'before_widget_content' => '', 'after_widget_content' => '', 'hide_for_logged_in' => '' );

		foreach ( $instance as $key => $value ) {
			if ( is_string( $value ) ) {
				$instance[ $key ] = esc_attr( $value );
			}
		}
		
		$instance = wp_parse_args( ( array ) $instance, $defaults );
		?> 
		<script type="text/javascript">
			function heateorSssToggleHorSharingTargetUrl(val) {
				if (val == 'custom' ) {
					jQuery( '.heateorSssHorSharingTargetUrl' ).css( 'display', 'block' );
				} else {
					jQuery( '.heateorSssHorSharingTargetUrl' ).css( 'display', 'none' );
				}
			}
		</script>
		<p> 
			<p><strong>Note:</strong> <?php _e( 'Make sure "Standard Sharing Interface" is enabled in "Standard Interface" section at "Sassy Social Share" page.', 'sassy-social-share' ) ?></p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>"><?php _e( 'Title:', 'sassy-social-share' ); ?></label> 
			<input style="width: 95%" class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>" type="text" value="<?php echo esc_attr( $instance['title'] ); ?>" /> <br/><br/>
			<label for="<?php echo esc_attr( $this->get_field_id( 'show_counts' ) ); ?>"><?php _e( 'Show individual share counts:', 'sassy-social-share' ); ?></label> 
			<input id="<?php echo esc_attr( $this->get_field_id( 'show_counts' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'show_counts' ) ); ?>" type="checkbox" value="1" <?php echo isset( $instance['show_counts'] ) && $instance['show_counts'] == 1 ? 'checked' : ''; ?> /><br/><br/>
			<label for="<?php echo esc_attr( $this->get_field_id( 'total_shares' ) ); ?>"><?php _e( 'Show total shares:', 'sassy-social-share' ); ?></label> 
			<input id="<?php echo esc_attr( $this->get_field_id( 'total_shares' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'total_shares' ) ); ?>" type="checkbox" value="1" <?php echo isset( $instance['total_shares'] ) && $instance['total_shares'] == 1 ? 'checked' : ''; ?> /><br/> <br/>
			<label for="<?php echo esc_attr( $this->get_field_id( 'target_url' ) ); ?>"><?php _e( 'Target Url:', 'sassy-social-share' ); ?></label> 
			<select style="width: 95%" onchange="heateorSssToggleHorSharingTargetUrl(this.value)" class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'target_url' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'target_url' ) ); ?>">
				<option value="">--<?php _e( 'Select', 'sassy-social-share' ) ?>--</option>
				<option value="default" <?php echo isset( $instance['target_url'] ) && $instance['target_url'] == 'default' ? 'selected' : '' ; ?>><?php _e( 'Url of the webpage where icons are located (default)', 'sassy-social-share' ) ?></option>
				<option value="homepage" <?php echo isset( $instance['target_url'] ) && $instance['target_url'] == 'homepage' ? 'selected' : '' ; ?>><?php _e( 'Url of the homepage of your website', 'sassy-social-share' ) ?></option>
				<option value="custom" <?php echo isset( $instance['target_url'] ) && $instance['target_url'] == 'custom' ? 'selected' : '' ; ?>><?php _e( 'Custom Url', 'sassy-social-share' ) ?></option>
			</select>
			<input placeholder="<?php _e( 'Custom Url', 'sassy-social-share' ) ?>" style="margin-top:5px; <?php echo ! isset( $instance['target_url'] ) || $instance['target_url'] != 'custom' ? 'display: none' : '' ; ?>" class="widefat heateorSssHorSharingTargetUrl" id="<?php echo esc_attr( $this->get_field_id( 'target_url_custom' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'target_url_custom' ) ); ?>" type="text" value="<?php echo isset( $instance['target_url_custom'] ) ? esc_url( $instance['target_url_custom'] ) : ''; ?>" />
			<label for="<?php echo esc_attr( $this->get_field_id( 'before_widget_content' ) ); ?>"><?php _e( 'Before widget content:', 'sassy-social-share' ); ?></label> 
			<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'before_widget_content' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'before_widget_content' ) ); ?>" type="text" value="<?php echo esc_attr( $instance['before_widget_content'] ); ?>" /> 
			<label for="<?php echo esc_attr( $this->get_field_id( 'after_widget_content' ) ); ?>"><?php _e( 'After widget content:', 'sassy-social-share' ); ?></label> 
			<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'after_widget_content' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'after_widget_content' ) ); ?>" type="text" value="<?php echo esc_attr( $instance['after_widget_content'] ); ?>" /> 
			<br /><br /><label for="<?php echo esc_attr( $this->get_field_id( 'hide_for_logged_in' ) ); ?>"><?php _e( 'Hide for logged in users:', 'sassy-social-share' ); ?></label> 
			<input type="checkbox" id="<?php echo esc_attr( $this->get_field_id( 'hide_for_logged_in' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'hide_for_logged_in' ) ); ?>" type="text" value="1" <?php if ( isset( $instance['hide_for_logged_in'] )  && $instance['hide_for_logged_in'] == 1 ) echo 'checked="checked"'; ?> /> 
		</p> 
	<?php 
    }

} 

/**
 * Floating Widget class
 *
 * This is used to define functions for Floating Sharing Widget.
 *
 * @since    1.0
 */
class Sassy_Social_Share_Floating_Widget extends WP_Widget { 
	
	/**
	 * Options saved in database.
	 *
	 * @since    1.0
	 */
	private $options;

	/**
	 * Member to assign object of Sassy_Social_Share_Public Class.
	 *
	 * @since    1.0
	 */
	private $public_class_object;

	/**
	 * Assign plugin options to private member $options and define widget title, description etc.
	 *
	 * @since    1.0
	 */
	public function __construct() { 
		
		global $heateor_sss;

		$this->options = $heateor_sss->options;

		$this->public_class_object = new Sassy_Social_Share_Public( $heateor_sss->options, HEATEOR_SSS_VERSION );

		parent::__construct( 
			'heateor_sss_Floating_Sharing', // unique id 
			'Sassy Social Share - Floating Widget', // widget title 
			// additional parameters 
			array(
				'description' => __( 'Floating sharing widget. Let your website users share content on popular Social networks like Facebook, Twitter, Tumblr, Whatsapp and many more', 'sassy-social-share' ) ) 
			); 
	}  

	/**
	 * Render widget at front-end
	 *
	 * @since    1.0
	 */
	public function widget( $args, $instance ) { 
		
		$amp_options = array();
		if ( class_exists( 'AMP_Options_Manager' ) && null !== AMP_Options_Manager::OPTION_NAME ) {
			$amp_options = get_option( AMP_Options_Manager::OPTION_NAME );
		}
		// return if vertical sharing is disabled
		if ( ! isset( $this->options['vertical_enable'] ) || ( isset( $this->options['amp_enable'] ) && $amp_options['theme_support'] == 'reader' ) ) {
			return;
		}
		extract( $args );
		if ( isset( $instance['hide_for_logged_in'] ) && is_user_logged_in() ) return;
		
		global $post;

		if ( isset( $this->options['js_when_needed'] ) && ! wp_script_is( 'heateor_sss_sharing_js' ) ) {
			$in_footer = isset( $this->options['footer_script'] ) ? true : false;
			$inline_script = 'function heateorSssLoadEvent(e) {var t=window.onload;if (typeof window.onload!="function") {window.onload=e}else{window.onload=function() {t();e()}}};';
			if ( $post ) {
				$inline_script .= 'var heateorSssInstagramUsername="' . ( $this->options['instagram_username'] != '' ? $this->options['instagram_username'] : $this->options['vertical_instagram_username'] ) . '",heateorSssYoutubeUsername="' . ( $this->options['youtube_username'] != '' ? $this->options['youtube_username'] : $this->options['vertical_youtube_username'] ) . '", heateorSssSharingAjaxUrl = \'' . get_admin_url() . 'admin-ajax.php\', heateorSssCloseIconPath = \'' . plugins_url( '../images/close.png', __FILE__ ) . '\', heateorSssPluginIconPath = \'' . plugins_url( '../images/logo.png', __FILE__ ) . '\', heateorSssHorizontalSharingCountEnable = '. ( isset( $this->options['hor_enable'] ) && ( isset( $this->options['horizontal_counts'] ) || isset( $this->options['horizontal_total_shares'] ) ) ? 1 : 0 ) . ', heateorSssVerticalSharingCountEnable = ' . ( isset( $this->options['vertical_enable'] ) && ( isset( $this->options['vertical_counts'] ) || isset( $this->options['vertical_total_shares'] ) ) ? 1 : 0 ) . ', heateorSssSharingOffset = ' . ( isset( $this->options['alignment'] ) && $this->options['alignment'] != '' && isset( $this->options[$this->options['alignment'] . '_offset'] ) && $this->options[$this->options['alignment'] . '_offset'] != '' ? $this->options[$this->options['alignment'] . '_offset'] : 0 ) . '; var heateorSssMobileStickySharingEnabled = ' . ( isset( $this->options['vertical_enable'] ) && isset( $this->options['bottom_mobile_sharing'] ) && $this->options['horizontal_screen_width'] != '' ? 1 : 0 ) . ';';
				$inline_script .= 'var heateorSssCopyLinkMessage = "' . htmlspecialchars( __( 'Link copied.', 'sassy-social-share' ), ENT_QUOTES ) . '";';
				if ( isset( $this->options['horizontal_counts'] ) && isset( $this->options['horizontal_counter_position'] ) ) {
					$inline_script .= in_array( $this->options['horizontal_counter_position'], array( 'inner_left', 'inner_right' ) ) ? 'var heateorSssReduceHorizontalSvgWidth = true;' : '';
					$inline_script .= in_array( $this->options['horizontal_counter_position'], array( 'inner_top', 'inner_bottom' ) ) ? 'var heateorSssReduceHorizontalSvgHeight = true;' : '';
				}
				if ( isset( $this->options['vertical_counts'] ) ) {
					$inline_script .= isset( $this->options['vertical_counter_position'] ) && in_array( $this->options['vertical_counter_position'], array( 'inner_left', 'inner_right' ) ) ? 'var heateorSssReduceVerticalSvgWidth = true;' : '';
					$inline_script .= ! isset( $this->options['vertical_counter_position'] ) || in_array( $this->options['vertical_counter_position'], array( 'inner_top', 'inner_bottom' ) ) ? 'var heateorSssReduceVerticalSvgHeight = true;' : '';
				}
				$inline_script .= 'var heateorSssUrlCountFetched = [], heateorSssSharesText = \''. htmlspecialchars(__( 'Shares', 'sassy-social-share' ), ENT_QUOTES ) . '\', heateorSssShareText = \'' . htmlspecialchars( __( 'Share', 'sassy-social-share' ), ENT_QUOTES ) . '\';';
				$inline_script .= 'function heateorSssPopup(e) {window.open(e,"popUpWindow","height=400,width=600,left=400,top=100,resizable,scrollbars,toolbar=0,personalbar=0,menubar=no,location=no,directories=no,status")}';
				if ( $this->public_class_object->facebook_like_recommend_enabled() || $this->public_class_object->facebook_share_enabled() ) {
					$inline_script .= 'function heateorSssInitiateFB() {FB.init({appId:"",channelUrl:"",status:!0,cookie:!0,xfbml:!0,version:"v17.0"})}window.fbAsyncInit=function() {heateorSssInitiateFB(),' . ( defined( 'HEATEOR_SOCIAL_SHARE_MYCRED_INTEGRATION_VERSION' ) && $this->facebook_like_recommend_enabled() ? 1 : 0 ) . '&&(FB.Event.subscribe("edge.create",function(e){heateorSsmiMycredPoints("Facebook_like_recommend","",e?e:"")}),FB.Event.subscribe("edge.remove",function(e){heateorSsmiMycredPoints("Facebook_like_recommend","",e?e:"","Minus point(s) for undoing Facebook like-recommend")}) ),' . ( defined( 'HEATEOR_SHARING_GOOGLE_ANALYTICS_VERSION' ) ? 1 : 0 ) . '&&(FB.Event.subscribe("edge.create",function(e) {heateorSssSsgaSocialPluginsTracking("Facebook","Like",e?e:"")}),FB.Event.subscribe("edge.remove",function(e) {heateorSssSsgaSocialPluginsTracking("Facebook","Unlike",e?e:"")}) )},function(e) {var n,i="facebook-jssdk",o=e.getElementsByTagName("script")[0];e.getElementById(i)||(n=e.createElement("script"),n.id=i,n.async=!0,n.src="//connect.facebook.net/' . ( $this->options['language'] ? $this->options['language'] : 'en_GB' ) . '/sdk.js",o.parentNode.insertBefore(n,o) )}(document);';
				}
				$inline_script .= '; var heateorSsmiAjaxUrl = "' . get_admin_url() . 'admin-ajax.php";';
				wp_enqueue_script( 'heateor_sss_sharing_js', plugins_url( '../public/js/sassy-social-share-public.js', __FILE__ ), array( 'jquery' ), $this->public_class_object->version, $in_footer );
				wp_add_inline_script( 'heateor_sss_sharing_js', $inline_script, $position = 'before' );
			}
		}

		$post_id = $post->ID;
		if ( isset( $instance['target_url'] ) ) {
			if ( $instance['target_url'] == 'default' ) {
				$sharing_url = esc_url_raw( $this->public_class_object->get_http_protocol() . $_SERVER["HTTP_HOST"] . $_SERVER["REQUEST_URI"] );
				if ( is_home() ) {
					$sharing_url = home_url();
					$post_id = 0;
				}  elseif ( ! is_singular() ) {
					$post_id = 0;
				} elseif ( isset( $_SERVER['QUERY_STRING'] ) && $_SERVER['QUERY_STRING'] ) {
					$sharing_url = esc_url_raw( $this->public_class_object->get_http_protocol() . $_SERVER["HTTP_HOST"] . $_SERVER["REQUEST_URI"] );
				} elseif ( get_permalink( $post->ID ) ) {
					$sharing_url = get_permalink( $post->ID );
				}
			} elseif ( $instance['target_url'] == 'homepage' ) {
				$sharing_url = home_url();
				$post_id = 0;
			} elseif ( $instance['target_url'] == 'custom' ) {
				$sharing_url = isset( $instance['target_url_custom'] ) ? trim( $instance['target_url_custom'] ) : get_permalink( $post->ID );
				$post_id = 0;
			}
		} else {
			$sharing_url = get_permalink( $post->ID );
		}
		$share_count_url = $sharing_url;
		if ( isset( $instance['target_url'] ) && $instance['target_url'] == 'default' && is_singular() ) {
			$share_count_url = get_permalink( $post->ID );
		}
		$custom_post_url = $this->public_class_object->apply_target_share_url_filter( $sharing_url, 'vertical', false );
		if ( $custom_post_url != $sharing_url ) {
			$sharing_url = $custom_post_url;
			$share_count_url = $sharing_url;
		}
		$ss_offset = 0;
		if ( isset( $instance['alignment'] ) && isset( $instance[$instance['alignment'] . '_offset'] ) ) {
			$ss_offset = $instance[$instance['alignment'] . '_offset'];
		}
		
		// share count transient ID
		$this->public_class_object->share_count_transient_id = $this->public_class_object->get_share_count_transient_id( $sharing_url );
		$cached_share_count = $this->public_class_object->get_cached_share_count( $this->public_class_object->share_count_transient_id );

		echo $before_widget;

		global $heateor_sss_allowed_tags;
		echo wp_kses( "<div class='heateor_sss_sharing_container heateor_sss_vertical_sharing" . ( isset( $this->options['hide_mobile_sharing'] ) ? ' heateor_sss_hide_sharing' : '' ) . ( isset( $this->options['bottom_mobile_sharing'] ) ? ' heateor_sss_bottom_sharing' : '' ) . "' data-heateor-ss-offset='" . $ss_offset . "' style='width:" . ( ( $this->options['vertical_sharing_size'] ? $this->options['vertical_sharing_size'] : 35 ) + 4 ) . "px;" . ( isset( $instance['alignment'] ) && $instance['alignment'] != '' && isset( $instance[$instance['alignment'] . '_offset'] ) ? $instance['alignment'] . ': ' . ( $instance[$instance['alignment'].'_offset'] == '' ? 0 : $instance[$instance['alignment'] . '_offset'] ) . 'px;' : '' ) . ( isset( $instance['top_offset'] ) ? 'top: ' . ( $instance['top_offset'] == '' ? 0 : $instance['top_offset'] ) . 'px;' : '' ) . ( isset( $instance['vertical_bg'] ) && $instance['vertical_bg'] != '' ? 'background-color:' . $instance['vertical_bg'] . ';' : '-webkit-box-shadow:none;box-shadow:none;' ) . "' " . ( $this->public_class_object->is_amp_page() ? "" : "data-heateor-sss-href='" . ( isset( $share_count_url ) && $share_count_url ? $share_count_url : $sharing_url ) . "'" ) . ( ( $cached_share_count === false || $this->public_class_object->is_amp_page() ) ? "" : 'data-heateor-sss-no-counts="1"' ) . ">", $heateor_sss_allowed_tags );
		
		$short_url = $this->public_class_object->get_short_url( $sharing_url, $post_id );
		
		global $heateor_sss_allowed_tags;
		echo wp_kses( $this->public_class_object->prepare_sharing_html( $short_url ? $short_url : $sharing_url, 'vertical', isset( $instance['show_counts'] ), isset( $instance['total_shares'] ) ), $heateor_sss_allowed_tags );
		echo '</div>';
		if ( ( isset( $instance['show_counts'] ) || isset( $instance['total_shares'] ) ) && $cached_share_count === false ) {
			echo '<script>heateorSssLoadEvent(
		function() {
			heateorSssGetSharingCounts();
		}
	);</script>';
		}

		echo $after_widget;
	}  

	/** 
	 * Everything which should happen when user edit widget at admin panel.
	 *
	 * @since    1.0
	 */
	public function update( $new_instance, $old_instance ) { 
		
		$instance = $old_instance; 
		$instance['target_url'] = $new_instance['target_url'];
		$instance['show_counts'] = $new_instance['show_counts']; 
		$instance['total_shares'] = $new_instance['total_shares']; 
		$instance['target_url_custom'] = $new_instance['target_url_custom'];
		$instance['alignment'] = $new_instance['alignment'];
		$instance['left_offset'] = $new_instance['left_offset'];
		$instance['right_offset'] = $new_instance['right_offset'];
		$instance['top_offset'] = $new_instance['top_offset'];
		$instance['vertical_bg'] = $new_instance['vertical_bg'];
		$instance['hide_for_logged_in'] = $new_instance['hide_for_logged_in'];  

		return $instance; 

	}  

	/** 
	 * Widget options form at admin panel.
	 *
	 * @since    1.0
	 */
	public function form( $instance ) { 
		
		/* Set up default widget settings. */ 
		$defaults = array( 'alignment' => 'left', 'show_counts' => '', 'total_shares' => '', 'left_offset' => '40', 'right_offset' => '0', 'target_url' => 'default', 'target_url_custom' => '', 'top_offset' => '100', 'vertical_bg' => '', 'hide_for_logged_in' => '' );

		foreach ( $instance as $key => $value ) {
			if ( is_string( $value ) ) {
				$instance[ $key ] = esc_attr( $value );
			}
		}
		
		$instance = wp_parse_args( ( array ) $instance, $defaults ); 
		?> 
		<p> 
			<script>
			function heateorSssToggleSharingOffset(alignment) {
				if (alignment == 'left' ) {
					jQuery( '.heateorSssSharingLeftOffset' ).css( 'display', 'block' );
					jQuery( '.heateorSssSharingRightOffset' ).css( 'display', 'none' );
				} else {
					jQuery( '.heateorSssSharingLeftOffset' ).css( 'display', 'none' );
					jQuery( '.heateorSssSharingRightOffset' ).css( 'display', 'block' );
				}
			}
			function heateorSssToggleVerticalSharingTargetUrl(val) {
				if (val == 'custom' ) {
					jQuery( '.heateorSssVerticalSharingTargetUrl' ).css( 'display', 'block' );
				} else {
					jQuery( '.heateorSssVerticalSharingTargetUrl' ).css( 'display', 'none' );
				}
			}
			</script>
			<p><strong>Note:</strong> <?php _e( 'Make sure "Floating Interface" is enabled in "Floating Interface" section at "Sassy Social Share" page.', 'sassy-social-share' ) ?></p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'show_counts' ) ); ?>"><?php _e( 'Show individual share counts:', 'sassy-social-share' ); ?></label>
			<input id="<?php echo esc_attr( $this->get_field_id( 'show_counts' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'show_counts' ) ); ?>" type="checkbox" value="1" <?php echo isset( $instance['show_counts'] ) && $instance['show_counts'] == 1 ? 'checked' : ''; ?> /><br/><br/> 
			<label for="<?php echo esc_attr( $this->get_field_id( 'total_shares' ) ); ?>"><?php _e( 'Show total shares:', 'sassy-social-share' ); ?></label> 
			<input id="<?php echo esc_attr( $this->get_field_id( 'total_shares' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'total_shares' ) ); ?>" type="checkbox" value="1" <?php echo isset( $instance['total_shares'] ) && $instance['total_shares'] == 1 ? 'checked' : ''; ?> /><br/> <br/>
			<label for="<?php echo esc_attr( $this->get_field_id( 'target_url' ) ); ?>"><?php _e( 'Target Url:', 'sassy-social-share' ); ?></label> 
			<select style="width: 95%" onchange="heateorSssToggleVerticalSharingTargetUrl(this.value)" class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'target_url' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'target_url' ) ); ?>">
				<option value="">--<?php _e( 'Select', 'sassy-social-share' ) ?>--</option>
				<option value="default" <?php echo isset( $instance['target_url'] ) && $instance['target_url'] == 'default' ? 'selected' : '' ; ?>><?php _e( 'Url of the webpage where icons are located (default)', 'sassy-social-share' ) ?></option>
				<option value="homepage" <?php echo isset( $instance['target_url'] ) && $instance['target_url'] == 'homepage' ? 'selected' : '' ; ?>><?php _e( 'Url of the homepage of your website', 'sassy-social-share' ) ?></option>
				<option value="custom" <?php echo isset( $instance['target_url'] ) && $instance['target_url'] == 'custom' ? 'selected' : '' ; ?>><?php _e( 'Custom Url', 'sassy-social-share' ) ?></option>
			</select>
			<input placeholder="Custom url" style="width:95%; margin-top: 5px; <?php echo ! isset( $instance['target_url'] ) || $instance['target_url'] != 'custom' ? 'display: none' : '' ; ?>" class="widefat heateorSssVerticalSharingTargetUrl" id="<?php echo esc_attr( $this->get_field_id( 'target_url_custom' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'target_url_custom' ) ); ?>" type="text" value="<?php echo isset( $instance['target_url_custom'] ) ? esc_url( $instance['target_url_custom'] ) : ''; ?>" /> 
			<label for="<?php echo esc_attr( $this->get_field_id( 'alignment' ) ); ?>"><?php _e( 'Alignment', 'sassy-social-share' ); ?></label> 
			<select onchange="heateorSssToggleSharingOffset(this.value)" style="width: 95%" class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'alignment' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'alignment' ) ); ?>">
				<option value="left" <?php echo ! isset( $instance['alignment'] ) || $instance['alignment'] == 'left' ? 'selected' : ''; ?>><?php _e( 'Left', 'sassy-social-share' ) ?></option>
				<option value="right" <?php echo $instance['alignment'] == 'right' ? 'selected' : ''; ?>><?php _e( 'Right', 'sassy-social-share' ) ?></option>
			</select>
			<div class="heateorSssSharingLeftOffset" <?php echo isset( $instance['alignment'] ) && $instance['alignment'] == 'right' ? 'style="display: none"' : ''; ?>>
				<label for="<?php echo esc_attr( $this->get_field_id( 'left_offset' ) ); ?>"><?php _e( 'Left Offset', 'sassy-social-share' ); ?></label> 
				<input style="width: 95%" class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'left_offset' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'left_offset' ) ); ?>" type="text" value="<?php echo esc_attr( $instance['left_offset'] ); ?>" />px<br/>
			</div>
			<div class="heateorSssSharingRightOffset" <?php echo ! isset( $instance['alignment'] ) || $instance['alignment'] == 'left' ? 'style="display: none"' : ''; ?>>
				<label for="<?php echo esc_attr( $this->get_field_id( 'right_offset' ) ); ?>"><?php _e( 'Right Offset', 'sassy-social-share' ); ?></label> 
				<input style="width: 95%" class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'right_offset' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'right_offset' ) ); ?>" type="text" value="<?php echo esc_attr( $instance['right_offset'] ); ?>" />px<br/>
			</div>
			<label for="<?php echo esc_attr( $this->get_field_id( 'top_offset' ) ); ?>"><?php _e( 'Top Offset', 'sassy-social-share' ); ?></label> 
			<input style="width: 95%" class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'top_offset' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'top_offset' ) ); ?>" type="text" value="<?php echo esc_attr( $instance['top_offset'] ); ?>" />px<br/>
			<label for="<?php echo esc_attr( $this->get_field_id( 'vertical_bg' ) ); ?>"><?php _e( 'Background Color', 'sassy-social-share' ); ?></label> 
			<input style="width: 95%" class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'vertical_bg' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'vertical_bg' ) ); ?>" type="text" value="<?php echo esc_attr( $instance['vertical_bg'] ); ?>" />
			<br /><br /><label for="<?php echo esc_attr( $this->get_field_id( 'hide_for_logged_in' ) ); ?>"><?php _e( 'Hide for logged in users:', 'sassy-social-share' ); ?></label> 
			<input type="checkbox" id="<?php echo esc_attr( $this->get_field_id( 'hide_for_logged_in' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'hide_for_logged_in' ) ); ?>" type="text" value="1" <?php if ( isset( $instance['hide_for_logged_in'] )  && $instance['hide_for_logged_in'] == 1 ) echo 'checked="checked"'; ?> /> 
		</p>
		<?php
    }
}

/**
 * Follow Icons Widget class
 *
 * This is used to define functions for Follow Icons Widget
 *
 * @since    1.0
 */
class Sassy_Social_Share_Follow_Widget extends WP_Widget { 
	/**
	 * Member to assign object of Sassy_Social_Share_Public Class.
	 *
	 * @since    1.0
	 */
	private $public_class_object;
	/**
	 * Member to assign object of Sassy_Social_Share_Public Class.
	 *
	 * @since    1.0
	 */
	private $options;

	/**
	 * Assign plugin options to private member $options and define widget title, description etc.
	 *
	 * @since    1.0
	 */
	public function __construct() {

		global $heateor_sss;
		$this->options = $heateor_sss->options;

		$this->public_class_object = new Sassy_Social_Share_Public( $heateor_sss->options, HEATEOR_SSS_VERSION );
		parent::__construct( 
			'heateor_sss_Follow', // unique id 
			__( 'Sassy Social Share - Follow Icons' ), // Widget title 
			array( 'description' => __( 'These icons link to your Social Media accounts', 'sassy-social-share' ) )
		);

	}  

	private function folow_icons_amp( $instance){
		$html = '';
		$icon_style = 'width:'. $instance['size'] .'px;height:'. $instance['size'] .'px;'. ( $instance['icon_shape'] == 'round' ? 'border-radius:999px;' : '' );
		$html .= '<div class="heateor_sss_follow_ul">';
		if ( isset( $instance['facebook'] ) && $instance['facebook'] ) {
			$html .= '<a class="heateor_sss_amp heateor_sss_amp_facebook" href="'.$instance['facebook'].'" rel="nofollow noopener" target="_blank"><amp-img src="%img_url%/facebook.svg" width="%width%" height="%height%" alt="Facebook" class="amp-wp-enforced-sizes" style="width: %width%px;"></amp-img></a>';
		}
		if ( isset( $instance['twitter'] ) && $instance['twitter'] ) {
			$html .= '<a class="heateor_sss_amp heateor_sss_amp_twitter" href="'.$instance['twitter'].'" title="Twitter" rel="nofollow noopener" target="_blank"><amp-img src="%img_url%/twitter.svg" width="%width%" height="%height%" alt="Twitter" class="amp-wp-enforced-sizes" style="width: %width%px;"></amp-img></a>';
		}
		if ( isset( $instance['instagram'] ) && $instance['instagram'] ) {
			$html .= '<a class="heateor_sss_amp heateor_sss_amp_instagram" href="'.$instance['instagram'].'" target="_blank"><amp-img src="%img_url%/instagram.svg" width="%width%" height="%height%" alt="Instagram" class="amp-wp-enforced-sizes" style="width: %width%px;"></amp-img></a>';
		}
		if ( isset( $instance['pinterest'] ) && $instance['pinterest'] ) {
			$html .= '<a class="heateor_sss_amp heateor_sss_amp_pinterest" href="'.$instance['pinterest'].'" title="Pinterest" rel="nofollow noopener" target="_blank"><amp-img src="%img_url%/pinterest.svg" width="%width%" height="%height%" alt="Pinterest" class="amp-wp-enforced-sizes" style="width: %width%px;"></amp-img></a>';
		}
		
		if ( isset( $instance['flickr'] ) && $instance['flickr'] ) {
			$html .= '<a class="heateor_sss_amp heateor_sss_amp_flickr" href="'.$instance['flickr'].'" title="Flickr" rel="nofollow noopener" target="_blank"><amp-img src="%img_url%/flickr.svg" width="%width%" height="%height%" alt="Flickr" class="amp-wp-enforced-sizes" style="width: %width%px;"></amp-img></a>';
		}
		if ( isset( $instance['foursquare'] ) && $instance['foursquare'] ) {
			$html .= '<a class="heateor_sss_amp heateor_sss_amp_foursquare" href="'.$instance['foursquare'].'" title="Foursquare" rel="nofollow noopener" target="_blank"><amp-img src="%img_url%/flickr.svg" width="%width%" height="%height%" alt="Foursquare" class="amp-wp-enforced-sizes" style="width: %width%px;"></amp-img></a>';
		}
		if ( isset( $instance['github'] ) && $instance['github'] ) {
			$html .= '<a class="heateor_sss_amp heateor_sss_amp_github" href="'.$instance['github'].'" title="Github" rel="nofollow noopener" target="_blank"><amp-img src="%img_url%/github.svg" width="%width%" height="%height%" alt="Github" class="amp-wp-enforced-sizes" style="width: %width%px;"></amp-img></a>';
		}
		if ( isset( $instance['linkedin'] ) && $instance['linkedin'] ) {
			$html .= '<a class="heateor_sss_amp heateor_sss_amp_linkedin" href="'.$instance['linkedin'].'" rel="nofollow noopener" target="_blank"><amp-img src="%img_url%/linkedin.svg" width="%width%" height="%height%" alt="Linkedin" class="amp-wp-enforced-sizes" style="width: %width%px;"></amp-img></a>';
		}
		if ( isset( $instance['linkedin_company'] ) && $instance['linkedin_company'] ) {
			$html .= '<a class="heateor_sss_amp heateor_sss_amp_linkedin" href="'.$instance['linkedin_company'].'" title="Linkedin Company" rel="nofollow noopener" target="_blank"><amp-img src="%img_url%/linkedin.svg" width="%width%" height="%height%" alt="Linkedin" class="amp-wp-enforced-sizes" style="width: %width%px;"></amp-img></a>';
		}
		if ( isset( $instance['medium'] ) && $instance['medium'] ) {
			$html .= '<a class="heateor_sss_amp heateor_sss_amp_medium" href="'.$instance['medium'].'" title="Medium" rel="nofollow noopener" target="_blank"><amp-img src="%img_url%/medium.svg" width="%width%" height="%height%" alt="Medium" class="amp-wp-enforced-sizes" style="width: %width%px;"></amp-img></a>';
		}
		if ( isset( $instance['behance'] ) && $instance['behance'] ) {
			$html .= '<a class="heateor_sss_amp heateor_sss_amp_behance" href="'.$instance['behance'].'" title="Behance" rel="nofollow noopener" target="_blank"><amp-img src="%img_url%/behance.svg" width="%width%" height="%height%" alt="Behance" class="amp-wp-enforced-sizes" style="width: %width%px;"></amp-img></a>';
		}
		if ( isset( $instance['mewe'] ) && $instance['mewe'] ) {
			$html .= '<a class="heateor_sss_amp heateor_sss_amp_mewe" href="'.$instance['mewe'].'" title="Mewe" rel="nofollow noopener" target="_blank"><amp-img src="%img_url%/mewe.svg" width="%width%" height="%height%" alt="Mewe" class="amp-wp-enforced-sizes" style="width: %width%px;"></amp-img></a>';
		}
		if ( isset( $instance['odnoklassniki'] ) && $instance['odnoklassniki'] ) {
			$html .= '<a class="heateor_sss_amp heateor_sss_amp_behance" href="'.$instance['odnoklassniki'].'" title="Odnoklassniki" rel="nofollow noopener" target="_blank"><amp-img src="%img_url%/odnoklassniki.svg" width="%width%" height="%height%" alt="Odnoklassniki" class="amp-wp-enforced-sizes" style="width: %width%px;"></amp-img></a>';
		}
		if ( isset( $instance['telegram'] ) && $instance['telegram'] ) {
			$html .= '<a class="heateor_sss_amp heateor_sss_amp_telegram" href="'.$instance['telegram'].'" title="Telegram" rel="nofollow noopener" target="_blank"><amp-img src="%img_url%/telegram.svg" width="%width%" height="%height%" alt="Telegram" class="amp-wp-enforced-sizes" style="width: %width%px;"></amp-img></a>';
		}
		if ( isset( $instance['tumblr'] ) && $instance['tumblr'] ) {
			$html .= '<a class="heateor_sss_amp heateor_sss_amp_tumblr" href="'.$instance['tumblr'].'" title="Tumblr" rel="nofollow noopener" target="_blank"><amp-img src="%img_url%/behance.svg" width="%width%" height="%height%" alt="Tumblr" class="amp-wp-enforced-sizes" style="width: %width%px;"></amp-img></a>';
		}
		if ( isset( $instance['vimeo'] ) && $instance['vimeo'] ) {
			$html .= '<a class="heateor_sss_amp heateor_sss_amp_vimeo" href="'.$instance['vimeo'].'" title="Vimeo" rel="nofollow noopener" target="_blank"><amp-img src="%img_url%/vimeo.svg" width="%width%" height="%height%" alt="Vimeo" class="amp-wp-enforced-sizes" style="width: %width%px;"></amp-img></a>';
		}
		if ( isset( $instance['vkontakte'] ) && $instance['vkontakte'] ) {
			$html .= '<a class="heateor_sss_amp heateor_sss_amp_vkontakte" href="'.$instance['vkontakte'].'" title="Vkontakte" rel="nofollow noopener" target="_blank"><amp-img src="%img_url%/vkontakte.svg" width="%width%" height="%height%" alt="Vkontakte" class="amp-wp-enforced-sizes" style="width: %width%px;"></amp-img></a>';
		}
		if ( isset( $instance['whatsapp'] ) && $instance['whatsapp'] ) {
			$html .= '<a class="heateor_sss_amp heateor_sss_amp_whatsapp" href="'.$instance['whatsapp'].'" title="Whatsapp" rel="nofollow noopener" target="_blank"><amp-img src="%img_url%/whatsapp.svg" width="%width%" height="%height%" alt="Whatsapp" class="amp-wp-enforced-sizes" style="width: %width%px;"></amp-img></a>';
		}
		if ( isset( $instance['xing'] ) && $instance['xing'] ) {
			$html .= '<a class="heateor_sss_amp heateor_sss_amp_xing" href="'.$instance['xing'].'" title="Behance" rel="nofollow noopener" target="_blank"><amp-img src="%img_url%/xing.svg" width="%width%" height="%height%" alt="Xing" class="amp-wp-enforced-sizes" style="width: %width%px;"></amp-img></a>';
		}
		if ( isset( $instance['youtube'] ) && $instance['youtube'] ) {
			$html .= '<a class="heateor_sss_amp heateor_sss_amp_behance" href="'.$instance['youtube'].'" title="youtube" rel="nofollow noopener" target="_blank"><amp-img src="%img_url%/youtube.svg" width="%width%" height="%height%" alt="Youtube" class="amp-wp-enforced-sizes" style="width: %width%px;"></amp-img></a>';
		}
		if ( isset( $instance['youtube_channel'] ) && $instance['youtube_channel'] ) {
			$html .= '<a class="heateor_sss_amp heateor_sss_amp_youtube" href="'.$instance['youtube_channel'].'" title="Youtube Channel" rel="nofollow noopener" target="_blank"><amp-img src="%img_url%/youtube.svg" width="%width%" height="%height%" alt="Youtube Channel" class="amp-wp-enforced-sizes" style="width: %width%px;"></amp-img></a>';
		}
		if ( isset( $instance['rss_feed'] ) && $instance['rss_feed'] ) {
			$html .= '<a class="heateor_sss_amp heateor_sss_amp_rss_feed_url" href="'.$instance['rss_feed_url'].'" title="RSS Feed Url" rel="nofollow noopener" target="_blank"><amp-img src="%img_url%/rss_feed.svg" width="%width%" height="%height%" alt="RSS Feed" class="amp-wp-enforced-sizes" style="width: %width%px;"></amp-img></a>';
		}

		if ( isset( $instance['parler'] ) && $instance['parler'] ) {
			$html .= '<a class="heateor_sss_amp heateor_sss_amp_parler_url" href="'.$instance['parler_url'].'" title="Parler" rel="nofollow noopener" target="_blank"><amp-img src="%img_url%/parler.svg" width="%width%" height="%height%" alt="Parler" class="amp-wp-enforced-sizes" style="width: %width%px;"></amp-img></a>';
		}

		if ( isset( $instance['gab'] ) && $instance['gab'] ) {
			$html .= '<a class="heateor_sss_amp heateor_sss_amp_gab_url" href="'.$instance['gab_url'].'" title="Gab" rel="nofollow noopener" target="_blank"><amp-img src="%img_url%/gab.svg" width="%width%" height="%height%" alt="Gab" class="amp-wp-enforced-sizes" style="width: %width%px;"></amp-img></a>';
		}

		if ( isset( $instance['google_news'] ) && $instance['google_news'] ) {
			$html .= '<a class="heateor_sss_amp heateor_sss_amp_google_news_url" href="'.$instance['google_news_url'].'" title="Google News" rel="nofollow noopener" target="_blank"><amp-img src="%img_url%/google_news.svg" width="%width%" height="%height%" alt="Google News" class="amp-wp-enforced-sizes" style="width: %width%px;"></amp-img></a>';
		}

		$html = apply_filters( 'heateor_sss_follow_icons', $html, $instance, $icon_style );
		$html .= '</div>';

		return $html;	
	}

	/**
	 * Render widget at front-end
	 *
	 * @since    1.0
	 */
	public function widget( $args, $instance ) {

		if ( $this->public_class_object->is_amp_page() ) {
			return;
		}

		extract( $args );
		
		echo $before_widget;

		if ( ! empty( $instance['before_widget_content'] ) ) { 
			echo '<div>' . esc_html( $instance['before_widget_content'] ) . '</div>'; 
		}
		$check_theme = '';
		if ( $instance['custom_color'] == '' ) {
			$check_theme = '';
		} elseif ( $instance['custom_color'] == 'standard' ) {
			$check_theme = 'standard_';
		} elseif ( $instance['custom_color'] == 'floating' ) {
			$check_theme = 'floating_';
		}
		
		$style = '';
		if ( $instance['type'] == 'floating' ) {
			$style = 'position:fixed;top:' . ( isset( $instance['top_offset'] ) ? intval( $instance['top_offset'] ) : 100 ) . 'px;' . ( isset( $instance['alignment'] ) && $instance['alignment'] == 'left' ? 'left' : 'right' ) . ':' . ( isset( $instance['alignment_value'] ) ? intval( $instance['alignment_value'] ) : 100 ) . 'px;width:' . ( isset( $instance['size'] ) ? intval( $instance['size'] ) : 32 ) . 'px;';
		}
		global $heateor_sss_allowed_tags;
		echo wp_kses( '<div style="' . $style . '" class="heateor_sss_' . $check_theme . 'follow_icons_container' . ( isset( $instance['bottom_mobile_sharing'] ) ? ' heateor_sss_bottom_follow' : '' ) . '">', $heateor_sss_allowed_tags );
		if ( ! empty( $instance['title'] ) ) { 
			$title = apply_filters( 'widget_title', $instance['title'] ); 
			if ( $instance['type'] != 'floating' ) {
				echo $before_title;
			}
			if ( $instance['type'] == 'floating' ) {
				echo wp_kses( '<div class="heateor_sss_follow_icons_title" style="text-align:center;font-size:' . intval( $instance['size'] )*30/100 . 'px">', $heateor_sss_allowed_tags );
			}
			echo esc_html( $title );
			if ( $instance['type'] == 'floating' ) {
				echo '</div>';
			}
			if ( $instance['type'] != 'floating' ) {
				echo $after_title;
			}
		}

		echo wp_kses( $this->follow_icons( $instance ), $heateor_sss_allowed_tags );

		echo '<div style="clear:both"></div>';
		echo '</div>';

		if ( ! empty( $instance['after_widget_content'] ) ) { 
			echo '<div>' . esc_html( $instance['after_widget_content'] ) . '</div>'; 
		}

		echo $after_widget;

	}

	/**
	 * Render follow icons
	 *
	 * @since    1.0
	 */
	private function follow_icons( $instance ) {

		$logo_color = '#fff';
		$html = '';
		if ( isset( $instance['type'] ) && $instance['type'] == 'standard' && isset( $instance['hor_alignment'] ) && $instance['hor_alignment'] == "center" ) {
			$html .= '<style>div.heateor_sss_follow_ul{width:100%;text-align:center;}.widget_heateor_sss_follow div.heateor_sss_follow_ul a{float:none!important;display:inline-block;}</style>';
		}
		if ( isset( $instance['hide_mobile_sharing'] ) && $instance['vertical_screen_width'] != '' ) {
			$html .= '<style>@media screen and (max-width:' . $instance['vertical_screen_width'] . 'px) {.heateor_sss_floating_follow_icons_container{display:none!important}}</style>';
		}
		if ( $instance['custom_color'] == 'standard' ) {
			if ( $this->options['horizontal_font_color_default'] ) {
				$logo_color = $this->options['horizontal_font_color_default'];
			}
			if ( $this->options['horizontal_font_color_hover'] ) {
				$html .= "<style>.widget_heateor_sss_follow span.heateor_sss_svg svg:hover path:not(.heateor_sss_no_fill),.widget_heateor_sss_follow span.heateor_sss_svg svg:hover ellipse, .widget_heateor_sss_follow span.heateor_sss_svg svg:hover circle,.widget_heateor_sss_follow span.heateor_sss_svg svg:hover polygon{
			        fill: " . $this->options['horizontal_font_color_hover'] . ";
			    }
			    .widget_heateor_sss_follow span.heateor_sss_svg svg:hover span.heateor_sss_s_digg path{
			    	stroke: " . $this->options['horizontal_font_color_hover'] . ";
			    }
			    .widget_heateor_sss_follow span.heateor_sss_svg svg:hover span.heateor_sss_s_whatsapp path.heateor_sss_no_fill{
			    	fill: " . $this->options['horizontal_font_color_hover'] . "!important;
			    }</style>";
			}	
		} elseif ( $instance['custom_color'] == 'floating' ) { 
			if ( $this->options['vertical_font_color_default'] ) {
				$logo_color = $this->options['vertical_font_color_default'];
			}
			if ( $this->options['vertical_font_color_hover'] ) {
				$html .= "<style>.widget_heateor_sss_follow span.heateor_sss_svg svg:hover path:not(.heateor_sss_no_fill),.widget_heateor_sss_follow span.heateor_sss_svg svg:hover ellipse, .widget_heateor_sss_follow span.heateor_sss_svg svg:hover circle,.widget_heateor_sss_follow span.heateor_sss_svg svg:hover polygon{
			        fill:". $this->options['vertical_font_color_hover'] .";
			    }
			    .widget_heateor_sss_follow span.heateor_sss_svg svg:hover span.heateor_sss_s_digg path{
			    	stroke:" . $this->options['vertical_font_color_hover'] . ";
			    }
			    .widget_heateor_sss_follow span.heateor_sss_svg svg:hover span.heateor_sss_s_whatsapp path.heateor_sss_no_fill{
			    	fill:" . $this->options['vertical_font_color_hover'] . "!important;
			    }</style>";
			}
		}
		$bottom_sharing_alignment = ! isset( $instance['bottom_sharing_alignment'] ) || $instance['bottom_sharing_alignment'] == 'left' ? 'left' : 'right';
		$bottom_sharing_alignment_inverse = $bottom_sharing_alignment == 'left' ? 'right' : 'left';
		$bottom_sharing_responsive_css = '';
		if ( isset( $instance['type'] ) && $instance['type'] == 'floating' && isset( $instance['bottom_sharing_position_radio'] ) && $instance['bottom_sharing_position_radio'] == 'responsive' ) {
			$vertical_sharing_icon_height = $this->options['vertical_sharing_shape'] == 'rectangle' ? $this->options['vertical_sharing_height'] : $this->options['vertical_sharing_size'];
			$num_sharing_icons = isset( $this->options['vertical_re_providers'] ) ? count( $this->options['vertical_re_providers'] ) : 0;
			$bottom_sharing_responsive_css = 'div.heateor_sss_bottom_follow{width:100%!important;left:0!important;}div.heateor_sss_bottom_follow a{width:' . ( 100/( $num_sharing_icons ) ) . '%!important;}div.heateor_sss_bottom_follow .heateor_sss_svg{width:100%!important;}';
		}
		if ( isset( $instance['type'] ) && $instance['type'] == 'floating' && isset( $instance['bottom_mobile_sharing'] ) && $instance['horizontal_screen_width'] != '' && isset( $instance['bottom_sharing_position_radio'] ) && $instance['bottom_sharing_position_radio'] == 'responsive' ) {
			$bottom_sharing_position = isset( $instance['bottom_sharing_position'] ) ? $instance['bottom_sharing_position'] : '0';
			$html .= '<style>div.heateor_sss_mobile_footer{display:none;}@media screen and (max-width:' . ( isset( $instance['horizontal_screen_width'] ) ? intval( $instance['horizontal_screen_width'] ) : 786 ) . 'px){' . $bottom_sharing_responsive_css . 'div.heateor_sss_mobile_footer{display:block;height:' . ( $this->options['vertical_sharing_shape'] == 'rectangle' ? $this->options['vertical_sharing_height'] : $this->options['vertical_sharing_size'] ) . 'px;}.heateor_sss_bottom_follow{padding:0!important;' . ( isset( $instance['bottom_sharing_position_radio'] ) && $instance['bottom_sharing_position_radio'] == 'nonresponsive' ? $bottom_sharing_alignment . ':' . $bottom_sharing_position . 'px!important;' . $bottom_sharing_alignment_inverse . ':auto!important;' : '' ) . 'display:block!important;width:auto!important;bottom:' . ( isset( $this->options['vertical_total_shares'] ) ? '-10' : '-2' ) . 'px!important;top:auto!important;}}</style>';
		}

		$icon_style = 'width:' . $instance['size'] . 'px;height:' . $instance['size'] . 'px;' . ( $instance['icon_shape'] == 'round' ? 'border-radius:999px;' : '' );
		$html .= '<div class="heateor_sss_follow_ul" ' . ( isset( $instance['hor_alignment'] ) && $instance['hor_alignment'] == 'right' ? ' style="float:right"' : '' ) . '>';
		if ( isset( $instance['facebook'] ) && $instance['facebook'] ) {
			$html .= '<a class="heateor_sss_facebook" href="'. $instance['facebook'] .'" title="Facebook" rel="nofollow noopener" target="_blank" style="font-size:32px!important;box-shadow:none;display:inline-block;vertical-align:middle;"><span style="background-color:#3c589a;display:inline-block;opacity:1;float:left;font-size:32px;box-shadow:none;display:inline-block;font-size:16px;padding:0 4px;vertical-align:middle;background-repeat:repeat;overflow:hidden;padding:0;cursor:pointer;'. $icon_style .'" class="heateor_sss_svg"><svg focusable="false" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="100%" height="100%" viewBox="-5 -5 42 42"><path d="M17.78 27.5V17.008h3.522l.527-4.09h-4.05v-2.61c0-1.182.33-1.99 2.023-1.99h2.166V4.66c-.375-.05-1.66-.16-3.155-.16-3.123 0-5.26 1.905-5.26 5.405v3.016h-3.53v4.09h3.53V27.5h4.223z" fill="'. $logo_color .'"></path></svg></span></a>';
		}
		if ( isset( $instance['twitter'] ) && $instance['twitter'] ) {
			$html .= '<a class="heateor_sss_twitter" href="'. $instance['twitter'] .'" title="Twitter" rel="nofollow noopener" target="_blank" style="font-size:32px!important;box-shadow:none;display:inline-block;vertical-align:middle;"><span style="background-color:#55acee;display:inline-block;opacity:1;float:left;font-size:32px;box-shadow:none;display:inline-block;font-size:16px;padding:0 4px;vertical-align:middle;background-repeat:repeat;overflow:hidden;padding:0;cursor:pointer;'. $icon_style .'" class="heateor_sss_svg"><svg focusable="false" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="100%" height="100%" viewBox="-4 -4 39 39"><path d="M28 8.557a9.913 9.913 0 0 1-2.828.775 4.93 4.93 0 0 0 2.166-2.725 9.738 9.738 0 0 1-3.13 1.194 4.92 4.92 0 0 0-3.593-1.55 4.924 4.924 0 0 0-4.794 6.049c-4.09-.21-7.72-2.17-10.15-5.15a4.942 4.942 0 0 0-.665 2.477c0 1.71.87 3.214 2.19 4.1a4.968 4.968 0 0 1-2.23-.616v.06c0 2.39 1.7 4.38 3.952 4.83-.414.115-.85.174-1.297.174-.318 0-.626-.03-.928-.086a4.935 4.935 0 0 0 4.6 3.42 9.893 9.893 0 0 1-6.114 2.107c-.398 0-.79-.023-1.175-.068a13.953 13.953 0 0 0 7.55 2.213c9.056 0 14.01-7.507 14.01-14.013 0-.213-.005-.426-.015-.637.96-.695 1.795-1.56 2.455-2.55z" fill="'. $logo_color .'"></path></svg></span></a>';
		}
		if ( isset( $instance['instagram'] ) && $instance['instagram'] ) {
			$html .= '<a class="heateor_sss_instagram" href="'. $instance['instagram'] .'" title="Instagram" rel="nofollow noopener" target="_blank" style="font-size:32px!important;box-shadow:none;display:inline-block;vertical-align:middle;"><span style="background-color:#53beee;display:inline-block;opacity:1;float:left;font-size:32px;box-shadow:none;display:inline-block;font-size:16px;padding:0 4px;vertical-align:middle;background-repeat:repeat;overflow:hidden;padding:0;cursor:pointer;'. $icon_style .'" class="heateor_sss_svg"><svg height="100%" width="100%" version="1.1" viewBox="-10 -10 148 148" xml:space="preserve" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink"><g><g><path d="M86,112H42c-14.336,0-26-11.663-26-26V42c0-14.337,11.664-26,26-26h44c14.337,0,26,11.663,26,26v44    C112,100.337,100.337,112,86,112z M42,24c-9.925,0-18,8.074-18,18v44c0,9.925,8.075,18,18,18h44c9.926,0,18-8.075,18-18V42    c0-9.926-8.074-18-18-18H42z" fill="'. $logo_color .'"></path></g><g><path d="M64,88c-13.234,0-24-10.767-24-24c0-13.234,10.766-24,24-24s24,10.766,24,24C88,77.233,77.234,88,64,88z M64,48c-8.822,0-16,7.178-16,16s7.178,16,16,16c8.822,0,16-7.178,16-16S72.822,48,64,48z" fill="'. $logo_color .'"></path></g><g><circle cx="89.5" cy="38.5" fill="'. $logo_color .'" r="5.5"></circle></g></g></svg></span></a>';
		}
		if ( isset( $instance['parler'] ) && $instance['parler'] ) {
			$html .= '<a class="heateor_sss_parler" href="'. $instance['parler'] .'" title="Parler" rel="nofollow noopener" target="_blank" style="font-size:32px!important;box-shadow:none;display:inline-block;vertical-align:middle;"><span style="background-color:#892E5E;display:inline-block;opacity:1;float:left;font-size:32px;box-shadow:none;display:inline-block;font-size:16px;padding:0 4px;vertical-align:middle;background-repeat:repeat;overflow:hidden;padding:0;cursor:pointer;'. $icon_style .'" class="heateor_sss_svg"><svg version="1.1" width="100%" height="100%" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" viewBox="-213 20 900 500" style="enable-background:new 0 0 500 500;" xml:space="preserve"><g><path fill="'. $logo_color .'" d="M200,300v-50.3h100.1c27.5,0,49.9-22.3,49.9-49.9v0c0-27.5-22.3-49.9-49.9-49.9H0v0C0,67.2,67.2,0,150,0l150,0 c110.5,0,200,89.5,200,200v0c0,110.5-89.5,200-200,200h0C244.8,400,200,355.2,200,300z M150,350V200h0C67.2,200,0,267.2,0,350v150 h0C82.8,500,150,432.8,150,350z"/></g></svg></span></a>';
		}
		if ( isset( $instance['pinterest'] ) && $instance['pinterest'] ) {
			$html .= '<a class="heateor_sss_pinterest" href="'. $instance['pinterest'] .'" title="Pinterest" rel="nofollow noopener" target="_blank" style="font-size:32px!important;box-shadow:none;display:inline-block;vertical-align:middle;"><span style="background-color:#cc2329;display:inline-block;opacity:1;float:left;font-size:32px;box-shadow:none;display:inline-block;font-size:16px;padding:0 4px;vertical-align:middle;background-repeat:repeat;overflow:hidden;padding:0;cursor:pointer;'. $icon_style .'" class="heateor_sss_svg"><svg focusable="false" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="100%" height="100%" viewBox="-2 -2 35 35"><path fill="'. $logo_color .'" d="M16.539 4.5c-6.277 0-9.442 4.5-9.442 8.253 0 2.272.86 4.293 2.705 5.046.303.125.574.005.662-.33.061-.231.205-.816.27-1.06.088-.331.053-.447-.191-.736-.532-.627-.873-1.439-.873-2.591 0-3.338 2.498-6.327 6.505-6.327 3.548 0 5.497 2.168 5.497 5.062 0 3.81-1.686 7.025-4.188 7.025-1.382 0-2.416-1.142-2.085-2.545.397-1.674 1.166-3.48 1.166-4.689 0-1.081-.581-1.983-1.782-1.983-1.413 0-2.548 1.462-2.548 3.419 0 1.247.421 2.091.421 2.091l-1.699 7.199c-.505 2.137-.076 4.755-.039 5.019.021.158.223.196.314.077.13-.17 1.813-2.247 2.384-4.324.162-.587.929-3.631.929-3.631.46.876 1.801 1.646 3.227 1.646 4.247 0 7.128-3.871 7.128-9.053.003-3.918-3.317-7.568-8.361-7.568z"/></svg></span></a>';
		}
		if ( isset( $instance['behance'] ) && $instance['behance'] ) {
			$html .= '<a class="heateor_sss_behance" href="'. $instance['behance'] .'" title="Behance" rel="nofollow noopener" target="_blank" style="font-size:32px!important;box-shadow:none;display:inline-block;vertical-align:middle;"><span style="background-color:#053eff;display:inline-block;opacity:1;float:left;font-size:32px;box-shadow:none;display:inline-block;font-size:16px;padding:0 4px;vertical-align:middle;background-repeat:repeat;overflow:hidden;padding:0;cursor:pointer;'. $icon_style .'" class="heateor_sss_svg"><svg xmlns="http://www.w3.org/2000/svg" viewBox="-2 -2 36 36"><path d="M3.862 8.136h5.66c1.377 0 3.19 0 4.13.566a3.705 3.705 0 0 1 1.837 3.26c0 1.66-.88 2.905-2.32 3.494v.042c1.924.397 2.97 1.838 2.97 3.76 0 2.297-1.636 4.483-4.743 4.483H3.86V8.14zm2.078 6.71h4.152c2.36 0 3.322-.856 3.322-2.493 0-2.16-1.53-2.468-3.322-2.468H5.94v4.96zm0 7.144h5.2c1.792 0 2.93-1.09 2.93-2.797 0-2.03-1.64-2.598-3.388-2.598H5.94v5.395zm22.017-1.833C27.453 22.65 25.663 24 23.127 24c-3.607 0-5.31-2.49-5.422-5.944 0-3.386 2.23-5.878 5.31-5.878 4 0 5.225 3.74 5.116 6.47h-8.455c-.067 1.966 1.05 3.716 3.52 3.716 1.53 0 2.6-.742 2.928-2.206h1.838zm-1.793-3.15c-.088-1.77-1.42-3.19-3.256-3.19-1.946 0-3.106 1.466-3.236 3.19h6.492zM20.614 8h4.935v1.68h-4.94z" fill="'. $logo_color .'"></path></svg></span></a>';
		}
		if ( isset( $instance['flickr'] ) && $instance['flickr'] ) {
			$html .= '<a class="heateor_sss_flickr" href="'. $instance['flickr'] .'" title="Flickr" rel="nofollow noopener" target="_blank" style="font-size:32px!important;box-shadow:none;display:inline-block;vertical-align:middle;"><span style="background-color:#ff0084;display:inline-block;opacity:1;float:left;font-size:32px;box-shadow:none;display:inline-block;font-size:16px;padding:0 4px;vertical-align:middle;background-repeat:repeat;overflow:hidden;padding:0;cursor:pointer;'. $icon_style .'" class="heateor_sss_svg"><svg xmlns="http://www.w3.org/2000/svg" viewBox="-2 -2 36 36"><g fill="'. $logo_color .'"><circle cx="23" cy="16" r="6"></circle><circle cx="9" cy="16" r="6"></circle></g></svg></span></a>';
		}
		if ( isset( $instance['foursquare'] ) && $instance['foursquare'] ) {
			$html .= '<a class="heateor_sss_foursquare" href="'. $instance['foursquare'] .'" title="Foursquare" rel="nofollow noopener" target="_blank" style="font-size:32px!important;box-shadow:none;display:inline-block;vertical-align:middle;"><span style="background-color:#f94877;display:inline-block;opacity:1;float:left;font-size:32px;box-shadow:none;display:inline-block;font-size:16px;padding:0 4px;vertical-align:middle;background-repeat:repeat;overflow:hidden;padding:0;cursor:pointer;'. $icon_style .'" class="heateor_sss_svg"><svg xmlns="http://www.w3.org/2000/svg" viewBox="-4 -4 40 40"><path fill="'. $logo_color .'" d="M21.516 3H7.586C5.66 3 5 4.358 5 5.383v21.995c0 1.097.65 1.407.958 1.53.31.126 1.105.206 1.676-.36l6.72-7.455c.105-.12.49-.284.552-.284h4.184c1.79 0 1.81-1.45 1.997-2.206.157-.63 1.946-9.57 2.58-12.395.523-2.32-.104-3.21-2.15-3.21zM20.2 9.682c-.07.33-.368.66-.75.693h-5.44c-.61-.034-1.108.422-1.108 1.032v.665c0 .61.5 1.24 1.108 1.24h4.607c.43 0 .794.276.7.737-.093.46-.573 2.82-.627 3.07-.052.254-.282.764-.716.764h-3.62c-.682 0-1.36-.008-1.816.56-.458.573-4.534 5.293-4.534 5.293V6.403c0-.438.31-.746.715-.74h11.274c.41-.006.915.41.834 1L20.2 9.68z"></path></svg></span></a>';
		}
		if ( isset( $instance['github'] ) && $instance['github'] ) {
			$html .= '<a class="heateor_sss_github" href="'. $instance['github'] .'" title="Github" rel="nofollow noopener" target="_blank" style="font-size:32px!important;box-shadow:none;display:inline-block;vertical-align:middle;"><span style="background-color:#2a2a2a;display:inline-block;opacity:1;float:left;font-size:32px;box-shadow:none;display:inline-block;font-size:16px;padding:0 4px;vertical-align:middle;background-repeat:repeat;overflow:hidden;padding:0;cursor:pointer;'. $icon_style .'" class="heateor_sss_svg"><svg xmlns="http://www.w3.org/2000/svg" viewBox="-2 -2 36 36"><path fill="'. $logo_color .'" d="M16 3.32c-7.182 0-13 5.82-13 13 0 5.754 3.72 10.612 8.89 12.335.65.114.893-.276.893-.617 0-.31-.016-1.333-.016-2.42-3.266.6-4.11-.797-4.37-1.53-.147-.373-.78-1.527-1.334-1.835-.455-.244-1.105-.845-.016-.86 1.024-.017 1.755.942 2 1.332 1.17 1.966 3.038 1.414 3.785 1.073.114-.845.455-1.414.83-1.74-2.893-.324-5.916-1.445-5.916-6.418 0-1.414.504-2.584 1.333-3.494-.13-.325-.59-1.657.13-3.445 0 0 1.085-.34 3.57 1.337 1.04-.293 2.146-.44 3.25-.44s2.21.147 3.25.44c2.49-1.69 3.58-1.337 3.58-1.337.714 1.79.26 3.12.13 3.446.828.91 1.332 2.064 1.332 3.494 0 4.99-3.04 6.094-5.93 6.42.47.405.876 1.185.876 2.404 0 1.74-.016 3.136-.016 3.575 0 .34.244.743.894.613C25.28 26.933 29 22.053 29 16.32c0-7.182-5.817-13-13-13z"></path></svg></span></a>';
		}
		if ( isset( $instance['linkedin'] ) && $instance['linkedin'] ) {
			$html .= '<a class="heateor_sss_linkedin" href="'. $instance['linkedin'] .'" title="Linkedin" rel="nofollow noopener" target="_blank" style="font-size:32px!important;box-shadow:none;display:inline-block;vertical-align:middle;"><span style="background-color:#0077b5;display:inline-block;opacity:1;float:left;font-size:32px;box-shadow:none;display:inline-block;font-size:16px;padding:0 4px;vertical-align:middle;background-repeat:repeat;overflow:hidden;padding:0;cursor:pointer;'. $icon_style .'" class="heateor_sss_svg"><svg focusable="false" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="100%" height="100%" viewBox="0 0 32 32"><path d="M6.227 12.61h4.19v13.48h-4.19V12.61zm2.095-6.7a2.43 2.43 0 0 1 0 4.86c-1.344 0-2.428-1.09-2.428-2.43s1.084-2.43 2.428-2.43m4.72 6.7h4.02v1.84h.058c.56-1.058 1.927-2.176 3.965-2.176 4.238 0 5.02 2.792 5.02 6.42v7.395h-4.183v-6.56c0-1.564-.03-3.574-2.178-3.574-2.18 0-2.514 1.7-2.514 3.46v6.668h-4.187V12.61z" fill="'. $logo_color .'"></path></svg></span></a>';
		}
		if ( isset( $instance['linkedin_company'] ) && $instance['linkedin_company'] ) {
			$html .= '<a class="heateor_sss_linkedin_company" href="'. $instance['linkedin_company'] .'" title="linkedinCompany" rel="nofollow noopener" target="_blank" style="font-size:32px!important;box-shadow:none;display:inline-block;vertical-align:middle;"><span style="background-color:#0077b5;display:inline-block;opacity:1;float:left;font-size:32px;box-shadow:none;display:inline-block;font-size:16px;padding:0 4px;vertical-align:middle;background-repeat:repeat;overflow:hidden;padding:0;cursor:pointer;'. $icon_style .'" class="heateor_sss_svg"><svg focusable="false" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="100%" height="100%" viewBox="0 0 32 32"><path d="M6.227 12.61h4.19v13.48h-4.19V12.61zm2.095-6.7a2.43 2.43 0 0 1 0 4.86c-1.344 0-2.428-1.09-2.428-2.43s1.084-2.43 2.428-2.43m4.72 6.7h4.02v1.84h.058c.56-1.058 1.927-2.176 3.965-2.176 4.238 0 5.02 2.792 5.02 6.42v7.395h-4.183v-6.56c0-1.564-.03-3.574-2.178-3.574-2.18 0-2.514 1.7-2.514 3.46v6.668h-4.187V12.61z" fill="'. $logo_color .'"></path></svg></span></a>';
		}
		if ( isset( $instance['medium'] ) && $instance['medium'] ) {
			$html .= '<a class="heateor_sss_medium" href="'. $instance['medium'] .'" title="medium" rel="nofollow noopener" target="_blank" style="font-size:32px!important;box-shadow:none;display:inline-block;vertical-align:middle;"><span style="background-color:#2a2a2a;display:inline-block;opacity:1;float:left;font-size:32px;box-shadow:none;display:inline-block;font-size:16px;padding:0 4px;vertical-align:middle;background-repeat:repeat;overflow:hidden;padding:0;cursor:pointer;'. $icon_style .'" class="heateor_sss_svg"><svg focusable="false" xmlns="http://www.w3.org/2000/svg" viewBox="-2 -2 36 36"><path d="M7.8 11a.8.8 0 0 0-.27-.7l-2-2.42v-.41h6.23L16.57 18l4.24-10.53h5.94v.36L25 9.47a.5.5 0 0 0-.19.48v12.1a.5.5 0 0 0 .19.48l1.68 1.64v.36h-8.4v-.36L20 22.49c.18-.17.18-.22.18-.49v-9.77l-4.82 12.26h-.65L9.09 12.23v8.22a1.09 1.09 0 0 0 .31.94l2.25 2.74v.36h-6.4v-.36l2.26-2.74a1.09 1.09 0 0 0 .29-.94z" fill="'. $logo_color .'"></path></svg></span></a>';
		}
		if ( isset( $instance['mewe'] ) && $instance['mewe'] ) {
			$html .= '<a class="heateor_sss_mewe" href="'. $instance['mewe'] .'" title="Mewe" rel="nofollow noopener" target="_blank" style="font-size:32px!important;box-shadow:none;display:inline-block;vertical-align:middle;"><span style="background-color:#007da1;display:inline-block;opacity:1;float:left;font-size:32px;box-shadow:none;display:inline-block;font-size:16px;padding:0 4px;vertical-align:middle;background-repeat:repeat;overflow:hidden;padding:0;cursor:pointer;'. $icon_style .'" class="heateor_sss_svg"><svg xmlns="http://www.w3.org/2000/svg" viewBox="-3 -3 38 38"><g fill="'. $logo_color .'"><path d="M9.636 10.427a1.22 1.22 0 1 1-2.44 0 1.22 1.22 0 1 1 2.44 0zM15.574 10.431a1.22 1.22 0 0 1-2.438 0 1.22 1.22 0 1 1 2.438 0zM22.592 10.431a1.221 1.221 0 1 1-2.443 0 1.221 1.221 0 0 1 2.443 0zM29.605 10.431a1.221 1.221 0 1 1-2.442 0 1.221 1.221 0 0 1 2.442 0zM3.605 13.772c0-.471.374-.859.859-.859h.18c.374 0 .624.194.789.457l2.935 4.597 2.95-4.611c.18-.291.43-.443.774-.443h.18c.485 0 .859.387.859.859v8.113a.843.843 0 0 1-.859.845.857.857 0 0 1-.845-.845V16.07l-2.366 3.559c-.18.276-.402.443-.72.443-.304 0-.526-.167-.706-.443l-2.354-3.53V21.9c0 .471-.374.83-.845.83a.815.815 0 0 1-.83-.83v-8.128h-.001zM14.396 14.055a.9.9 0 0 1-.069-.333c0-.471.402-.83.872-.83.415 0 .735.263.845.624l2.23 6.66 2.187-6.632c.139-.402.428-.678.859-.678h.124c.428 0 .735.278.859.678l2.187 6.632 2.23-6.675c.126-.346.415-.609.83-.609.457 0 .845.361.845.817a.96.96 0 0 1-.083.346l-2.867 8.032c-.152.43-.471.706-.887.706h-.165c-.415 0-.721-.263-.872-.706l-2.161-6.328-2.16 6.328c-.152.443-.47.706-.887.706h-.165c-.415 0-.72-.263-.887-.706l-2.865-8.032z"></path></g></svg></span></a>';
		}
		if ( isset( $instance['odnoklassniki'] ) && $instance['odnoklassniki'] ) {
			$html .= '<a class="heateor_sss_odnoklassniki" href="'. $instance['odnoklassniki'] .'" title="Odnoklassniki" rel="nofollow noopener" target="_blank" style="font-size:32px!important;box-shadow:none;display:inline-block;vertical-align:middle;"><span style="background-color:#f2720c;display:inline-block;opacity:1;float:left;font-size:32px;box-shadow:none;display:inline-block;font-size:16px;padding:0 4px;vertical-align:middle;background-repeat:repeat;overflow:hidden;padding:0;cursor:pointer;'. $icon_style .'" class="heateor_sss_svg"><svg focusable="false" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="100%" height="100%" viewBox="0 0 32 32"><path fill="'. $logo_color .'" d="M16 16.16a6.579 6.579 0 0 1-6.58-6.58A6.578 6.578 0 0 1 16 3a6.58 6.58 0 1 1 .002 13.16zm0-9.817a3.235 3.235 0 0 0-3.236 3.237 3.234 3.234 0 0 0 3.237 3.236 3.236 3.236 0 1 0 .004-6.473zm7.586 10.62c.647 1.3-.084 1.93-1.735 2.99-1.395.9-3.313 1.238-4.564 1.368l1.048 1.05 3.877 3.88c.59.59.59 1.543 0 2.133l-.177.18c-.59.59-1.544.59-2.134 0l-3.88-3.88-3.877 3.88c-.59.59-1.543.59-2.135 0l-.176-.18a1.505 1.505 0 0 1 0-2.132l3.88-3.877 1.042-1.046c-1.25-.127-3.19-.465-4.6-1.37-1.65-1.062-2.38-1.69-1.733-2.99.37-.747 1.4-1.367 2.768-.29C13.035 18.13 16 18.13 16 18.13s2.968 0 4.818-1.456c1.368-1.077 2.4-.457 2.768.29z"></path></svg></span></a>';
		}
		if ( isset( $instance['telegram'] ) && $instance['telegram'] ) {
			$html .= '<a class="heateor_sss_telegram" href="'. $instance['telegram'] .'" title="Telegram" rel="nofollow noopener" target="_blank" style="font-size:32px!important;box-shadow:none;display:inline-block;vertical-align:middle;"><span style="background-color:#3da5f1;display:inline-block;opacity:1;float:left;font-size:32px;box-shadow:none;display:inline-block;font-size:16px;padding:0 4px;vertical-align:middle;background-repeat:repeat;overflow:hidden;padding:0;cursor:pointer;'. $icon_style .'" class="heateor_sss_svg"><svg focusable="false" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="100%" height="100%" viewBox="0 0 32 32"><path fill="'. $logo_color .'" d="M25.515 6.896L6.027 14.41c-1.33.534-1.322 1.276-.243 1.606l5 1.56 1.72 5.66c.226.625.115.873.77.873.506 0 .73-.235 1.012-.51l2.43-2.363 5.056 3.734c.93.514 1.602.25 1.834-.863l3.32-15.638c.338-1.363-.52-1.98-1.41-1.577z"></path></svg></span></a>';
		}
		if ( isset( $instance['tumblr'] ) && $instance['tumblr'] ) {
			$html .= '<a class="heateor_sss_tumblr" href="'. $instance['tumblr'] .'" title="Tumblr" rel="nofollow noopener" target="_blank" style="font-size:32px!important;box-shadow:none;display:inline-block;vertical-align:middle;"><span style="background-color:#29435d;display:inline-block;opacity:1;float:left;font-size:32px;box-shadow:none;display:inline-block;font-size:16px;padding:0 4px;vertical-align:middle;background-repeat:repeat;overflow:hidden;padding:0;cursor:pointer;'. $icon_style .'" class="heateor_sss_svg"><svg focusable="false" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="100%" height="100%" viewBox="-2 -2 36 36"><path fill="'. $logo_color .'" d="M20.775 21.962c-.37.177-1.08.33-1.61.345-1.598.043-1.907-1.122-1.92-1.968v-6.217h4.007V11.1H17.26V6.02h-2.925s-.132.044-.144.15c-.17 1.556-.895 4.287-3.923 5.378v2.578h2.02v6.522c0 2.232 1.647 5.404 5.994 5.33 1.467-.025 3.096-.64 3.456-1.17l-.96-2.846z"/></svg></span></a>';
		}
		if ( isset( $instance['vimeo'] ) && $instance['vimeo'] ) {
			$html .= '<a class="heateor_sss_vimeo" href="'. $instance['vimeo'] .'" title="Vimeo" rel="nofollow noopener" target="_blank" style="font-size:32px!important;box-shadow:none;display:inline-block;vertical-align:middle;"><span style="background-color:#1ab7ea;display:inline-block;opacity:1;float:left;font-size:32px;box-shadow:none;display:inline-block;font-size:16px;padding:0 4px;vertical-align:middle;background-repeat:repeat;overflow:hidden;padding:0;cursor:pointer;'. $icon_style .'" class="heateor_sss_svg"><svg xmlns="http://www.w3.org/2000/svg" viewBox="-2 -2 36 36"><path fill="'. $logo_color .'" d="M26.926 10.627c-.103 2.25-1.675 5.332-4.716 9.245C19.066 23.957 16.406 26 14.23 26c-1.348 0-2.49-1.244-3.42-3.732l-1.867-6.844C8.25 12.937 7.51 11.69 6.715 11.69c-.173 0-.778.365-1.815 1.09l-1.088-1.4a300.012 300.012 0 0 0 3.374-3.01c1.522-1.315 2.666-2.007 3.427-2.076 1.8-.173 2.907 1.057 3.322 3.69.45 2.84.76 4.608.935 5.3.52 2.356 1.09 3.534 1.713 3.534.483 0 1.21-.764 2.18-2.294.97-1.528 1.488-2.692 1.558-3.49.14-1.32-.38-1.98-1.553-1.98-.554 0-1.125.126-1.712.378 1.137-3.722 3.308-5.53 6.513-5.426 2.378.068 3.498 1.61 3.36 4.62z"></path></svg></span></a>';
		}
		if ( isset( $instance['vkontakte'] ) && $instance['vkontakte'] ) {
			$html .= '<a class="heateor_sss_vkontakte" href="'. $instance['vkontakte'] .'" title="Vkontakte" rel="nofollow noopener" target="_blank" style="font-size:32px!important;box-shadow:none;display:inline-block;vertical-align:middle;"><span style="background-color:#cc2329;display:inline-block;opacity:1;float:left;font-size:32px;box-shadow:none;display:inline-block;font-size:16px;padding:0 4px;vertical-align:middle;background-repeat:repeat;overflow:hidden;padding:0;cursor:pointer;'. $icon_style .'" class="heateor_sss_svg"><svg focusable="false" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="100%" height="100%" viewBox="-1 -2 34 34"><path fill-rule="evenodd" clip-rule="evenodd" fill="'. $logo_color .'" d="M15.764 22.223h1.315s.394-.044.6-.262c.184-.2.18-.574.18-.574s-.03-1.764.79-2.023c.81-.255 1.844 1.705 2.942 2.46.832.57 1.464.445 1.464.445l2.936-.04s1.538-.097.81-1.304c-.06-.1-.426-.894-2.186-2.526-1.843-1.71-1.594-1.434.624-4.39 1.353-1.804 1.893-2.902 1.724-3.374-.16-.45-1.153-.33-1.153-.33l-3.306.02s-.247-.034-.428.074c-.178.108-.293.356-.293.356s-.522 1.394-1.223 2.58c-1.47 2.5-2.06 2.633-2.3 2.476-.563-.36-.42-1.454-.42-2.23 0-2.423.365-3.435-.72-3.696-.357-.085-.623-.143-1.544-.15-1.182-.014-2.18.003-2.743.28-.378.185-.667.595-.49.62.218.027.713.13.975.49.34.46.33 1.496.33 1.496s.193 2.852-.46 3.206c-.442.245-1.056-.252-2.37-2.52-.67-1.163-1.18-2.446-1.18-2.446s-.1-.24-.273-.37c-.212-.155-.506-.204-.506-.204l-3.145.02s-.473.015-.647.22c-.154.183-.01.56-.01.56s2.46 5.757 5.245 8.657c2.553 2.66 5.454 2.485 5.454 2.485z"/></svg></span></a>';
		}
		if ( isset( $instance['whatsapp'] ) && $instance['whatsapp'] ) {
			$html .= '<a class="heateor_sss_whatsapp" href="'. $instance['whatsapp'] .'" title="Whatsapp" rel="nofollow noopener" target="_blank" style="font-size:32px!important;box-shadow:none;display:inline-block;vertical-align:middle;"><span style="background-color:#55eb4c;display:inline-block;opacity:1;float:left;font-size:32px;box-shadow:none;display:inline-block;font-size:16px;padding:0 4px;vertical-align:middle;background-repeat:repeat;overflow:hidden;padding:0;cursor:pointer;'. $icon_style .'" class="heateor_sss_svg"><svg focusable="false" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="100%" height="100%" viewBox="-5 -5 40 40"><path id="arc1" stroke="'. $logo_color .'" stroke-width="2" fill="none" d="M 11.579798566743314 24.396926207859085 A 10 10 0 1 0 6.808479557110079 20.73576436351046"></path><path d="M 7 19 l -1 6 l 6 -1" stroke="'. $logo_color .'" stroke-width="2" fill="none"></path><path d="M 10 10 q -1 8 8 11 c 5 -1 0 -6 -1 -3 q -4 -3 -5 -5 c 4 -2 -1 -5 -1 -4" fill="'. $logo_color .'"></path></svg></span></a>';
		}
		if ( isset( $instance['xing'] ) && $instance['xing'] ) {
			$html .= '<a class="heateor_sss_xing" href="'. $instance['xing'] .'" title="Xing" rel="nofollow noopener" target="_blank" style="font-size:32px!important;box-shadow:none;display:inline-block;vertical-align:middle;"><span style="background-color:#00797d;display:inline-block;opacity:1;float:left;font-size:32px;box-shadow:none;display:inline-block;font-size:16px;padding:0 4px;vertical-align:middle;background-repeat:repeat;overflow:hidden;padding:0;cursor:pointer;'. $icon_style .'" class="heateor_sss_svg"><svg xmlns="http://www.w3.org/2000/svg" width="100%" height="100%" viewBox="-6 -6 42 42"><path d="M 6 9 h 5 l 4 4 l -5 7 h -5 l 5 -7 z m 15 -4 h 5 l -9 13 l 4 8 h -5 l -4 -8 z" fill="'. $logo_color .'"></path></svg></span></a>';
		}
		if ( isset( $instance['youtube'] ) && $instance['youtube'] ) {
			$html .= '<a class="heateor_sss_facebook" href="'. $instance['facebook'] .'" title="Facebook" rel="nofollow noopener" target="_blank" style="font-size:32px!important;box-shadow:none;display:inline-block;vertical-align:middle;"><span style="background-color:red;display:inline-block;opacity:1;float:left;font-size:32px;box-shadow:none;display:inline-block;font-size:16px;padding:0 4px;vertical-align:middle;background-repeat:repeat;overflow:hidden;padding:0;cursor:pointer;'. $icon_style .'" class="heateor_sss_svg"><svg focusable="false" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="100%" height="100%" viewBox="0 0 32 32"><path fill="'. $logo_color .'" d="M26.78 11.6s-.215-1.515-.875-2.183c-.837-.876-1.774-.88-2.204-.932-3.075-.222-7.693-.222-7.693-.222h-.01s-4.618 0-7.697.222c-.43.05-1.368.056-2.205.932-.66.668-.874 2.184-.874 2.184S5 13.386 5 15.166v1.67c0 1.78.22 3.56.22 3.56s.215 1.516.874 2.184c.837.875 1.936.85 2.426.94 1.76.17 7.48.22 7.48.22s4.623-.007 7.7-.23c.43-.05 1.37-.056 2.205-.932.66-.668.875-2.184.875-2.184s.22-1.78.22-3.56v-1.67c0-1.78-.22-3.56-.22-3.56zm-13.052 7.254v-6.18l5.944 3.1-5.944 3.08z"></path></svg></span></a>';
		}
		if ( isset( $instance['youtube_channel'] ) && $instance['youtube_channel'] ) {
			$html .= '<a class="heateor_sss_youtube_channel" href="'. $instance['youtube_channel'] .'" title="Youtubechannel" rel="nofollow noopener" target="_blank" style="font-size:32px!important;box-shadow:none;display:inline-block;vertical-align:middle;"><span style="background-color:red;display:inline-block;opacity:1;float:left;font-size:32px;box-shadow:none;display:inline-block;font-size:16px;padding:0 4px;vertical-align:middle;background-repeat:repeat;overflow:hidden;padding:0;cursor:pointer;'. $icon_style .'" class="heateor_sss_svg"><svg focusable="false" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="100%" height="100%" viewBox="0 0 32 32"><path fill="'. $logo_color .'" d="M26.78 11.6s-.215-1.515-.875-2.183c-.837-.876-1.774-.88-2.204-.932-3.075-.222-7.693-.222-7.693-.222h-.01s-4.618 0-7.697.222c-.43.05-1.368.056-2.205.932-.66.668-.874 2.184-.874 2.184S5 13.386 5 15.166v1.67c0 1.78.22 3.56.22 3.56s.215 1.516.874 2.184c.837.875 1.936.85 2.426.94 1.76.17 7.48.22 7.48.22s4.623-.007 7.7-.23c.43-.05 1.37-.056 2.205-.932.66-.668.875-2.184.875-2.184s.22-1.78.22-3.56v-1.67c0-1.78-.22-3.56-.22-3.56zm-13.052 7.254v-6.18l5.944 3.1-5.944 3.08z"></path></svg></span></a>';
		}
		if ( isset( $instance['rss_feed'] ) && $instance['rss_feed'] ) {
			$html .= '<a class="heateor_sss_rss_feed" href="'. $instance['rss_feed'] .'" title="Rssfeed" rel="nofollow noopener" target="_blank" style="font-size:32px!important;box-shadow:none;display:inline-block;vertical-align:middle;"><span style="background-color:#e3702d;display:inline-block;opacity:1;float:left;font-size:32px;box-shadow:none;display:inline-block;font-size:16px;padding:0 4px;vertical-align:middle;background-repeat:repeat;overflow:hidden;padding:0;cursor:pointer;'. $icon_style .'" class="heateor_sss_svg"><svg xmlns="http://www.w3.org/2000/svg" viewBox="-4 -4 40 40"><g fill="'. $logo_color .'"><ellipse cx="7.952" cy="24.056" rx="2.952" ry="2.944"></ellipse><path d="M5.153 16.625c2.73 0 5.295 1.064 7.22 2.996a10.2 10.2 0 0 1 2.996 7.255h4.2c0-7.962-6.47-14.44-14.42-14.44v4.193zm.007-7.432c9.724 0 17.636 7.932 17.636 17.682H27C27 14.812 17.203 5 5.16 5v4.193z"></path></g></svg></span></a>';
		}
		if ( isset( $instance['gab'] ) && $instance['gab'] ) {
			$html .= '<a class="heateor_sss_gab" href="'. $instance['gab'] .'" title="gab" rel="nofollow noopener" target="_blank" style="font-size:32px!important;box-shadow:none;display:inline-block;vertical-align:middle;"><span style="background-color:#25CC80;display:inline-block;opacity:1;float:left;font-size:32px;box-shadow:none;display:inline-block;font-size:16px;padding:0 4px;vertical-align:middle;background-repeat:repeat;overflow:hidden;padding:0;cursor:pointer;'. $icon_style .'" class="heateor_sss_svg"><svg version="1.1" xmlns="http://www.w3.org/2000/svg" width="100%" height="100%" viewBox="-14.5 3.5 46 30" xml:space="preserve"><g><path fill="'. $logo_color .'" d="M13.8,7.6h-2.4v0.7V9l-0.4-0.3C10.2,7.8,9,7.2,7.7,7.2c-0.2,0-0.4,0-0.4,0c-0.1,0-0.3,0-0.5,0 c-5.6,0.3-8.7,7.2-5.4,12.1c2.3,3.4,7.1,4.1,9.7,1.5l0.3-0.3l0,0.7c0,1-0.1,1.5-0.4,2.2c-1,2.4-4.1,3-6.8,1.3 c-0.2-0.1-0.4-0.2-0.4-0.2c-0.1,0.1-1.9,3.5-1.9,3.6c0,0.1,0.5,0.4,0.8,0.6c2.2,1.4,5.6,1.7,8.3,0.8c2.7-0.9,4.5-3.2,5-6.4 c0.2-1.1,0.2-0.8,0.2-8.4l0-7.1H13.8z M9.7,17.6c-2.2,1.2-4.9-0.4-4.9-2.9C4.8,12.6,7,11,9,11.6C11.8,12.4,12.3,16.1,9.7,17.6z"></path></g></svg></span></a>';
		}
		if ( isset( $instance['google_news'] ) && $instance['google_news'] ) {
			$html .= '<a class="heateor_sss_google_news" href="'. $instance['google_news'] .'" title="Google News" rel="nofollow noopener" target="_blank" style="font-size:32px!important;box-shadow:none;display:inline-block;vertical-align:middle;"><span style="background-color:#4285F4;display:inline-block;opacity:1;float:left;font-size:32px;box-shadow:none;display:inline-block;font-size:16px;padding:0 4px;vertical-align:middle;background-repeat:repeat;overflow:hidden;padding:0;cursor:pointer;'. $icon_style .'" class="heateor_sss_svg"><svg viewBox="35 45 80 80" xmlns="http://www.w3.org/2000/svg" width="100%" height="100%"><path fill="'. $logo_color .'" d="M99.6,77.8H78.2v-5.6h21.4c0.6,0,1.1,0.5,1.1,1.1v3.4C100.7,77.3,100.2,77.8,99.6,77.8z"></path><path fill="'. $logo_color .'" d="M99.6,99.2H78.2v-5.6h21.4c0.6,0,1.1,0.5,1.1,1.1v3.4C100.7,98.7,100.2,99.2,99.6,99.2z"></path><path fill="'. $logo_color .'" d="M103,88.5H78.2v-5.6H103c0.6,0,1.1,0.5,1.1,1.1v3.4C104.1,88,103.6,88.5,103,88.5z"></path><path fill="'. $logo_color .'" d="M59.1,83.4v5.1h7.3c-0.6,3.1-3.3,5.3-7.3,5.3c-4.4,0-8-3.7-8-8.2c0-4.4,3.6-8.2,8-8.2c2,0,3.8,0.7,5.2,2v0 l3.9-3.9c-2.3-2.2-5.4-3.5-9-3.5c-7.5,0-13.5,6-13.5,13.5c0,7.5,6,13.5,13.5,13.5C66.9,99.2,72,93.7,72,86c0-0.9-0.1-1.7-0.2-2.6 H59.1z"></path></svg></span></a>';
		}
		$html = apply_filters( 'heateor_sss_follow_icons', $html, $instance, $icon_style );
		$html .= '</div>';

		return $html;

	}

	/**
	 * Validate the widget options
	 *
	 * @since    1.0
	 */ 
	public function update( $new_instance, $old_instance ) {

		$instance = $old_instance;

		$instance['title'] = strip_tags( $new_instance['title'] );
		$instance['mobile_sharing'] = $new_instance['mobile_sharing'];
		$instance['hor_alignment'] = $new_instance['hor_alignment'];
		$instance['bottom_mobile_sharing'] = $new_instance['bottom_mobile_sharing'];
		$instance['vertical_screen_width'] = $new_instance['vertical_screen_width'];
		$instance['horizontal_screen_width'] = $new_instance['horizontal_screen_width'];
		$instance['bottom_sharing_position_radio'] = $new_instance['bottom_sharing_position_radio'];
		$instance['bottom_sharing_position'] = $new_instance['bottom_sharing_position'];
		$instance['bottom_sharing_alignment'] = $new_instance['bottom_sharing_alignment'];
		$instance['type'] = $new_instance['type'];
		$instance['top_offset'] = $new_instance['top_offset'];
		$instance['alignment_value'] = $new_instance['alignment_value'];
		$instance['alignment'] = $new_instance['alignment'];
		$instance['size'] = intval( $new_instance['size'] );
		$instance['icon_shape'] = $new_instance['icon_shape'];
		$instance['custom_color'] = $new_instance['custom_color'];
		$instance['facebook'] = $new_instance['facebook'];
		$instance['twitter'] = $new_instance['twitter'];
		$instance['instagram'] = $new_instance['instagram'];
		$instance['parler'] = $new_instance['parler'];
		$instance['pinterest'] = $new_instance['pinterest'];
		$instance['behance'] = $new_instance['behance'];
		$instance['flickr'] = $new_instance['flickr'];
		$instance['foursquare'] = $new_instance['foursquare'];
		$instance['github'] = $new_instance['github'];
		$instance['gitlab'] = $new_instance['gitlab'];
		$instance['linkedin'] = $new_instance['linkedin'];
		$instance['linkedin_company'] = $new_instance['linkedin_company'];
		$instance['medium'] = $new_instance['medium'];
		$instance['mewe'] = $new_instance['mewe'];
		$instance['odnoklassniki'] = $new_instance['odnoklassniki'];
		$instance['telegram'] = $new_instance['telegram'];
		$instance['tumblr'] = $new_instance['tumblr'];
		$instance['vimeo'] = $new_instance['vimeo'];
		$instance['vkontakte'] = $new_instance['vkontakte'];
		$instance['whatsapp'] = $new_instance['whatsapp'];
		$instance['xing'] = $new_instance['xing'];
		$instance['youtube'] = $new_instance['youtube'];
		$instance['youtube_channel'] = $new_instance['youtube_channel'];
		$instance['rss_feed'] = $new_instance['rss_feed'];
		$instance['gab'] = $new_instance['gab'];
		$instance['google_news'] = $new_instance['google_news'];
		$instance['before_widget_content'] = $new_instance['before_widget_content']; 
		$instance['after_widget_content'] = $new_instance['after_widget_content'];

		return $instance;

	}

	/** 
	 * Widget options form
	 *
	 * @since    1.0
	 */
	public function form( $instance ) { 
		
		/* default widget settings. */ 
		$defaults = array( 'hor_alignment' => 'left', 'title' => '', 'type' => 'standard', 'alignment' => 'right', 'size' => '35', 'icon_shape' => 'round', 'custom_color' => '', 'facebook' => '', 'twitter' => '', 'instagram' => '', 'parler' => '', 'pinterest' => '', 'behance' => '', 'flickr' => '', 'foursquare' => '', 'github' => '', 'gitlab' => '', 'linkedin' => '', 'linkedin_company' => '', 'medium' => '', 'mewe' => '', 'odnoklassniki' => '', 'telegram' => '', 'tumblr' => '', 'vimeo' => '', 'vkontakte' => '', 'whatsapp' => '', 'xing' => '', 'youtube' => '', 'youtube_channel' => '', 'rss_feed' => '', 'gab' => '', 'google_news' => '', 'before_widget_content' => '', 'after_widget_content' => '', 'top_offset' => '200', 'alignment_value' => '0', 'mobile_sharing' => '1', 'bottom_mobile_sharing' => '1', 'vertical_screen_width' => '783', 'horizontal_screen_width' => '783', 'bottom_sharing_alignment' => 'left', 'bottom_sharing_position_radio' => 'responsive', 'bottom_sharing_position' => '0' );

		foreach ( $instance as $key => $value ) {
			if ( is_string( $value ) ) {
				$instance[ $key ] = esc_attr( $value );
			}
		}
		
		$instance = wp_parse_args( ( array ) $instance, $defaults );
		?>
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'before_widget_content' ) ); ?>"><?php _e( 'Before widget content:', 'sassy-social-share' ); ?></label> 
			<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'before_widget_content' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'before_widget_content' ) ); ?>" type="text" value="<?php echo esc_attr( $instance['before_widget_content'] ); ?>" /><br/><br/>
			<label for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>"><?php _e( 'Title', 'sassy-social-share' ); ?></label> 
			<input style="width: 95%" class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>" type="text" value="<?php echo esc_attr( $instance['title'] ); ?>" /><br/><br/><br/>
			<label for="<?php echo esc_attr( $this->get_field_id( 'hor_alignment' ) ); ?>"><?php _e( 'Alignment', 'sassy-social-share' ); ?></label><br/>
			<select style="width: 95%" class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'hor_alignment' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'hor_alignment' ) ); ?>">
				<option value="" <?php echo ! isset( $instance['hor_alignment'] ) || $instance['hor_alignment'] == 'left' ? 'selected' : '' ; ?>><?php _e( 'Left', 'sassy-social-share' ); ?></option>
				<option value="center" <?php echo isset( $instance['hor_alignment'] ) && $instance['hor_alignment'] == 'center' ? 'selected' : '' ; ?>><?php _e( 'Center', 'sassy-social-share' ); ?></option>
				<option value="right" <?php echo isset( $instance['hor_alignment'] ) && $instance['hor_alignment'] == 'right' ? 'selected' : '' ; ?>><?php _e( 'Right', 'sassy-social-share' ); ?></option>
			</select><br/><br/>
			<label for="<?php echo esc_attr( $this->get_field_id( 'mode_standard' ) ); ?>"><?php _e( 'Type:', 'sassy-social-share' ) ?></label><br>
			<input id="<?php echo esc_attr( $this->get_field_id( 'mode_standard' ) ); ?>" type="radio" onclick="jQuery( '.heateorSssFloatingAlignment' ).css( 'display', 'none' );" name="<?php echo esc_attr( $this->get_field_name( 'type' ) ); ?>" value="standard" <?php if ( ! isset( $instance['type'] ) || $instance['type'] == 'standard' ) {
				echo "checked";
			} ?>><label for="<?php echo esc_attr( $this->get_field_id( 'mode_standard' ) ); ?>"> <?php _e( 'Standard', 'sassy-social-share' ) ?></label><br>
 			<input id="<?php echo esc_attr( $this->get_field_id( 'mode_floating' ) ); ?>" type="radio" name="<?php echo esc_attr( $this->get_field_name( 'type' ) ); ?>" onclick="jQuery( '.heateorSssFloatingAlignment' ).css( 'display', 'block' );" value="floating" <?php if( $instance['type'] == 'floating' ) {
				echo "checked";
			}?>><label for="<?php echo esc_attr( $this->get_field_id( 'mode_floating' ) ); ?>"> <?php _e( 'Floating', 'sassy-social-share' ) ?></label><br><br>

			<div class="heateorSssFloatingAlignment"
				<?php echo $instance['type'] == 'standard' ? "style='display:none'" : "style='display:block'" ?>>
				<label for="<?php echo esc_attr( $this->get_field_id( 'top_offset' ) ); ?>">
				<?php _e( 'Top offset:', 'sassy-social-share' ) ?>
				</label>
				<input id="<?php echo esc_attr( $this->get_field_id('top_offset' ) ); ?>" type="text" name="<?php echo esc_attr( $this->get_field_name( 'top_offset' ) ); ?>" value="<?php echo isset( $instance['top_offset'] ) ? esc_attr( $instance['top_offset'] ) : ''; ?>"/>px<br><br>
				<label for="<?php echo esc_attr( $this->get_field_id( 'floating_left' ) ); ?>">
				<?php _e( 'Alignment:', 'sassy-social-share' ) ?>
				</label>
				<input id="<?php echo esc_attr( $this->get_field_id( 'floating_left' ) ); ?>" type="radio" name="<?php echo esc_attr( $this->get_field_name( 'alignment' ) ); ?>" value="left" 
				<?php if ( ! isset( $instance['alignment'] ) || $instance['alignment'] == 'left' ) {
				echo 'checked';
				} ?>>
				<label for="<?php echo esc_attr( $this->get_field_id( 'floating_left' ) ); ?>"> 
				<?php _e( 'Left', 'sassy-social-share' ) ?>
				</label>
				<input id="<?php echo esc_attr( $this->get_field_id( 'floating_right' ) ); ?>" type="radio" name="<?php echo esc_attr( $this->get_field_name( 'alignment' ) ); ?>" value="right"
				<?php if ( $instance['alignment'] == 'right' ) {
				echo 'checked';
				} ?> />
				<label for="<?php echo esc_attr( $this->get_field_id( 'floating_right' ) ); ?>" > 
				<?php _e( 'Right', 'sassy-social-share' ) ?>
				</label>
				<br>
				<br>
				<label id="<?php echo esc_attr( $this->get_field_id( 'alignment_value_label' ) ); ?>" for="<?php echo esc_attr( $this->get_field_id( 'alignment_value' ) ); ?>"><?php _e( 'Offset', 'sassy-social-share' ); ?></label>
				<br>
				<input id='<?php echo esc_attr( $this->get_field_id( 'alignment_value' ) ); ?>' type="text" name="<?php echo esc_attr( $this->get_field_name( 'alignment_value' ) ); ?>" value="<?php echo isset( $instance['alignment_value'] ) ? esc_attr( $instance['alignment_value'] ) : ''; ?>" />px<br><br>
				<input id="<?php echo esc_attr( $this->get_field_id( 'mobile_sharing' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'mobile_sharing' ) ); ?>" type="checkbox" <?php echo isset( $instance['mobile_sharing'] ) ? 'checked = "checked"' : ''; ?> value="1" />
				<label><?php echo sprintf( __( 'Display vertical interface only when screen is wider than %s pixels', 'sassy-social-share' ), '<input style="width:46px" name="' . esc_attr( $this->get_field_name( 'vertical_screen_width' ) ) . '" type="text" value="' . ( isset( $instance['vertical_screen_width'] ) ? esc_attr( $instance['vertical_screen_width'] ) : '' ) . '" />' ) ?></label>
				<br><br>
				<input id="<?php echo esc_attr( $this->get_field_id('mobile_sharing_bottom' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'bottom_mobile_sharing' ) ); ?>" type="checkbox" <?php echo isset( $instance['bottom_mobile_sharing'] ) ? 'checked = "checked"' : '';?> value="1" />

				<label><?php echo sprintf( __( 'Stick vertical floating interface horizontally at bottom only when screen is narrower than %s pixels', 'sassy-social-share' ), '<input style="width:46px" name="' . esc_attr( $this->get_field_name( 'horizontal_screen_width' ) ) . '" type="text" value="' . ( isset( $instance['horizontal_screen_width'] ) ? esc_attr( $instance['horizontal_screen_width'] ) : '' ) . '" />' ) ?></label>

				<br><br>

				<input type="radio" id="<?php echo esc_attr( $this->get_field_id( 'bottom_sharing_position_radio_nonresponsive' ) ); ?>" <?php echo isset( $instance['bottom_sharing_position_radio'] ) && $instance['bottom_sharing_position_radio'] == 'nonresponsive' ? 'checked' : ''; ?> name="<?php echo esc_attr( $this->get_field_name( 'bottom_sharing_position_radio' ) ); ?>" value="nonresponsive" />

				<label for="<?php echo esc_attr( $this->get_field_id( 'bottom_sharing_position_radio_nonresponsive' ) ); ?>"><?php echo sprintf( __( '%s pixels from %s', 'sassy-social-share' ), '<input id="'. esc_attr( $this->get_field_id('mobile_sharing_position' ) ) . '" style="width:46px" name="'.$this->get_field_name( 'bottom_sharing_position' ).'" type="text" value="' . ( isset( $instance['bottom_sharing_position'] ) ? $instance['bottom_sharing_position'] : '' ) . '" />', '<select style="width:63px" name="' . esc_attr( $this->get_field_name( 'bottom_sharing_alignment' ) ) . '"><option value="right" ' . ( ! isset( $instance['bottom_sharing_alignment'] ) || $instance['bottom_sharing_alignment'] == 'right' ? 'selected' : '' ) . '>' . __( 'right', 'sassy-social-share' ) . '</option><option value="left" ' . ( isset( $instance['bottom_sharing_alignment'] ) && $instance['bottom_sharing_alignment'] == 'left' ? 'selected' : '' ) . '>' . __( 'left', 'sassy-social-share' ) . '</option></select>' ) ?></label>
				<br/>

				<input type="radio" id="<?php echo esc_attr( $this->get_field_id( 'bottom_sharing_position_radio_responsive' ) ); ?>" <?php echo ! isset( $instance['bottom_sharing_position_radio'] ) || $instance['bottom_sharing_position_radio'] == 'responsive' ? 'checked' : ''; ?> name="<?php echo esc_attr( $this->get_field_name( 'bottom_sharing_position_radio' ) ); ?>" value="responsive" /><label for="<?php echo esc_attr( $this->get_field_id( 'bottom_sharing_position_radio_responsive' ) ); ?>"><?php _e( 'Auto-adjust according to the screen-width (responsive)', 'sassy-social-share' ); ?></label>
				<br>
				<br>
			</div>
	 					
			<label for="<?php echo esc_attr( $this->get_field_id( 'size' ) ); ?>"><?php _e( 'Size of icons', 'sassy-social-share' ); ?></label> 
			<input style="width:82%" class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'size' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'size' ) ); ?>" type="text" value="<?php echo isset( $instance['size'] ) ? esc_attr( $instance['size'] ) : 32; ?>" />px<br/><br/>
			<label for="<?php echo esc_attr( $this->get_field_id( 'icon_shape' ) ); ?>"><?php _e( 'Icon Shape', 'sassy-social-share' ); ?></label> 
			<select style="width: 95%" class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'icon_shape' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'icon_shape' ) ); ?>">
				<option value="round" <?php echo ! isset( $instance['icon_shape'] ) || $instance['icon_shape'] == 'round' ? 'selected' : '' ; ?>><?php _e( 'Round', 'sassy-social-share' ); ?></option>
				<option value="square" <?php echo isset( $instance['icon_shape'] ) && $instance['icon_shape'] == 'square' ? 'selected' : '' ; ?>><?php _e( 'Square', 'sassy-social-share' ); ?></option>
			</select><br/><br/>
			<script type="text/javascript">
			function heateorSssFloatingAlignment(val) {
				if (val == 'floating' ) {
					jQuery( '.heateorSssFloatingAlignment' ).css( 'display', 'block' );
				} else {
					jQuery( '.heateorSssFloatingAlignment' ).css( 'display', 'none' );
				}
			}
			function heateorSssAlignmentOffsetLabel(val) {
				if (val == 'left' ) {
					jQuery("label:contains('<?php _e( 'Right offset', 'sassy-social-share' ) ?>')").text('<?php _e( 'Left offset', 'sassy-social-share' ) ?>' );
				} else {
					jQuery("label:contains('<?php _e( 'Left offset', 'sassy-social-share' ) ?>')").text('<?php _e( 'Right offset', 'sassy-social-share' ) ?>' );
				}
			}
			jQuery(function(){
				heateorSssFloatingAlignment('<?php echo isset( $instance['type'] ) ? esc_js( $instance['type'] ) : 'standard'; ?>');
				heateorSssAlignmentOffsetLabel('<?php echo isset( $instance['alignment'] ) ? esc_js( $instance['alignment'] ) : 'left'; ?>');
			});
			</script>
			<label for="<?php echo esc_attr( $this->get_field_id( 'custom_color' ) ); ?>"><?php _e( 'Apply icon color and background color from Theme Selection section:', 'sassy-social-share' ); ?></label> 
			<select style="width: 95%" class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'custom_color' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'custom_color' ) ); ?>">
				<option value="" <?php echo ! isset( $instance['custom_color'] ) || $instance['custom_color'] == '' ? 'selected' : '' ; ?>><?php _e( 'No', 'sassy-social-share' ); ?></option>
				<option value="standard" <?php echo isset( $instance['custom_color'] ) && $instance['custom_color'] == 'standard' ? 'selected' : '' ; ?>><?php _e( 'Yes, Standard Interface Theme', 'sassy-social-share' ); ?></option>
				<option value="floating" <?php echo isset( $instance['custom_color'] ) && $instance['custom_color'] == 'floating' ? 'selected' : '' ; ?>><?php _e( 'Yes, Floating Interface Theme', 'sassy-social-share' ); ?></option>
			</select><br/><br/>
			<label for="<?php echo esc_attr( $this->get_field_id( 'facebook' ) ); ?>"><?php _e( 'Facebook URL:', 'sassy-social-share' ); ?></label> 
			<input style="width: 95%" class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'facebook' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'facebook' ) ); ?>" type="text" value="<?php echo esc_attr( $instance['facebook'] ); ?>" /><br/>
			<span>https://www.facebook.com/ID</span><br/><br/>
			<label for="<?php echo esc_attr( $this->get_field_id( 'twitter' ) ); ?>"><?php _e( 'Twitter URL:', 'sassy-social-share' ); ?></label> 
			<input style="width: 95%" class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'twitter' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'twitter' ) ); ?>" type="text" value="<?php echo esc_attr( $instance['twitter'] ); ?>" /><br/>
			<span>https://twitter.com/ID</span><br/><br/>
			<label for="<?php echo esc_attr( $this->get_field_id( 'instagram' ) ); ?>"><?php _e( 'Instagram URL:', 'sassy-social-share' ); ?></label> 
			<input style="width: 95%" class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'instagram' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'instagram' ) ); ?>" type="text" value="<?php echo esc_attr( $instance['instagram'] ); ?>" /><br/>
			<span>https://www.instagram.com/ID</span><br/><br/>
			<label for="<?php echo esc_attr( $this->get_field_id( 'parler' ) ); ?>"><?php _e( 'Parler URL:', 'sassy-social-share' ); ?></label> 
			<input style="width: 95%" class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'parler' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'parler' ) ); ?>" type="text" value="<?php echo esc_attr( $instance['parler'] ); ?>" /><br/>
			<span>https://parler.com/profile/ID</span><br/><br/>
			<label for="<?php echo esc_attr( $this->get_field_id( 'pinterest' ) ); ?>"><?php _e( 'Pinterest URL:', 'sassy-social-share' ); ?></label> 
			<input style="width: 95%" class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'pinterest' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'pinterest' ) ); ?>" type="text" value="<?php echo esc_attr( $instance['pinterest'] ); ?>" /><br/>
			<span>https://www.pinterest.com/ID</span><br/><br/>
			<label for="<?php echo esc_attr( $this->get_field_id( 'behance' ) ); ?>"><?php _e( 'Behance URL:', 'sassy-social-share' ); ?></label> 
			<input style="width: 95%" class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'behance' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'behance' ) ); ?>" type="text" value="<?php echo esc_attr( $instance['behance'] ); ?>" /><br/>
			<span>https://www.behance.net/ID</span><br/><br/>
			<label for="<?php echo esc_attr( $this->get_field_id( 'flickr' ) ); ?>"><?php _e( 'Flickr URL:', 'sassy-social-share' ); ?></label> 
			<input style="width: 95%" class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'flickr' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'flickr' ) ); ?>" type="text" value="<?php echo esc_attr( $instance['flickr'] ); ?>" /><br/>
			<span>https://www.flickr.com/photos/ID</span><br/><br/>
			<label for="<?php echo esc_attr( $this->get_field_id( 'foursquare' ) ); ?>"><?php _e( 'Foursquare URL:', 'sassy-social-share' ); ?></label> 
			<input style="width: 95%" class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'foursquare' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'foursquare' ) ); ?>" type="text" value="<?php echo esc_attr( $instance['foursquare'] ); ?>" /><br/>
			<span>https://foursquare.com/ID</span><br/><br/>
			<label for="<?php echo esc_attr( $this->get_field_id( 'github' ) ); ?>"><?php _e( 'Github URL:', 'sassy-social-share' ); ?></label> 
			<input style="width: 95%" class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'github' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'github' ) ); ?>" type="text" value="<?php echo esc_attr( $instance['github'] ); ?>" /><br/>
			<span>https://github.com/ID</span><br/><br/>
			<label for="<?php echo esc_attr( $this->get_field_id( 'linkedin' ) ); ?>"><?php _e( 'Linkedin URL:', 'sassy-social-share' ); ?></label> 
			<input style="width: 95%" class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'linkedin' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'linkedin' ) ); ?>" type="text" value="<?php echo esc_attr( $instance['linkedin'] ); ?>" /><br/>
			<span>https://www.linkedin.com/in/ID</span><br/><br/>
			<label for="<?php echo esc_attr( $this->get_field_id( 'linkedin_company' ) ); ?>"><?php _e( 'Linkedin Company URL:', 'sassy-social-share' ); ?></label> 
			<input style="width: 95%" class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'linkedin_company' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'linkedin_company' ) ); ?>" type="text" value="<?php echo esc_attr( $instance['linkedin_company'] ); ?>" /><br/>
			<span>https://www.linkedin.com/company/ID</span><br/><br/>
			<label for="<?php echo esc_attr( $this->get_field_id( 'medium' ) ); ?>"><?php _e( 'Medium URL:', 'sassy-social-share' ); ?></label> 
			<input style="width: 95%" class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'medium' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'medium' ) ); ?>" type="text" value="<?php echo esc_attr( $instance['medium'] ); ?>" /><br/>
			<span>https://medium.com/@ID</span><br/><br/>
			<label for="<?php echo esc_attr( $this->get_field_id( 'mewe' ) ); ?>"><?php _e( 'MeWe URL:', 'sassy-social-share' ); ?></label> 
			<input style="width: 95%" class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'mewe' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'mewe' ) ); ?>" type="text" value="<?php echo esc_attr( $instance['mewe'] ); ?>" /><br/>
			<span>https://mewe.com/profile/ID</span><br/><br/>
			<label for="<?php echo esc_attr( $this->get_field_id( 'odnoklassniki' ) ); ?>"><?php _e( 'Odnoklassniki URL:', 'sassy-social-share' ); ?></label> 
			<input style="width: 95%" class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'odnoklassniki' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'odnoklassniki' ) ); ?>" type="text" value="<?php echo esc_attr( $instance['odnoklassniki'] ); ?>" /><br/>
			<span>https://ok.ru/profile/ID</span><br/><br/>
			<label for="<?php echo esc_attr( $this->get_field_id( 'telegram' ) ); ?>"><?php _e( 'Telegram URL:', 'sassy-social-share' ); ?></label> 
			<input style="width: 95%" class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'telegram' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'telegram' ) ); ?>" type="text" value="<?php echo esc_attr( $instance['telegram'] ); ?>" /><br/>
			<span>https://t.me/username</span><br/><br/>
			<label for="<?php echo esc_attr( $this->get_field_id( 'tumblr' ) ); ?>"><?php _e( 'Tumblr URL:', 'sassy-social-share' ); ?></label> 
			<input style="width: 95%" class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'tumblr' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'tumblr' ) ); ?>" type="text" value="<?php echo esc_attr( $instance['tumblr'] ); ?>" /><br/>
			<span>https://ID.tumblr.com</span><br/><br/>
			<label for="<?php echo esc_attr( $this->get_field_id( 'vimeo' ) ); ?>"><?php _e( 'Vimeo URL:', 'sassy-social-share' ); ?></label> 
			<input style="width: 95%" class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'vimeo' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'vimeo' ) ); ?>" type="text" value="<?php echo esc_attr( $instance['vimeo'] ); ?>" /><br/>
			<span>https://vimeo.com/ID</span><br/><br/>
			<label for="<?php echo esc_attr( $this->get_field_id( 'vkontakte' ) ); ?>"><?php _e( 'Vkontakte URL:', 'sassy-social-share' ); ?></label> 
			<input style="width: 95%" class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'vkontakte' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'vkontakte' ) ); ?>" type="text" value="<?php echo esc_attr( $instance['vkontakte'] ); ?>" /><br/>
			<span>https://vk.com/ID</span><br/><br/>
			<label for="<?php echo esc_attr( $this->get_field_id( 'whatsapp' ) ); ?>"><?php _e( 'Whatsapp URL:', 'sassy-social-share' ); ?></label> 
			<input style="width: 95%" class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'whatsapp' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'whatsapp' ) ); ?>" type="text" value="<?php echo esc_attr( $instance['whatsapp'] ); ?>" /><br/>
			<span>https://wa.me/PHONE_NUMBER</span><br/><br/>
			<label for="<?php echo esc_attr( $this->get_field_id( 'xing' ) ); ?>"><?php _e( 'Xing URL:', 'sassy-social-share' ); ?></label> 
			<input style="width: 95%" class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'xing' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'xing' ) ); ?>" type="text" value="<?php echo esc_attr( $instance['xing'] ); ?>" /><br/>
			<span>https://www.xing.com/profile/ID</span><br/><br/>
			<label for="<?php echo esc_attr( $this->get_field_id( 'youtube' ) ); ?>"><?php _e( 'Youtube URL:', 'sassy-social-share' ); ?></label> 
			<input style="width: 95%" class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'youtube' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'youtube' ) ); ?>" type="text" value="<?php echo esc_attr( $instance['youtube'] ); ?>" /><br/>
			<span>https://www.youtube.com/user/ID</span><br/><br/>
			<label for="<?php echo esc_attr( $this->get_field_id( 'youtube_channel' ) ); ?>"><?php _e( 'Youtube Channel URL:', 'sassy-social-share' ); ?></label>
			<input style="width: 95%" class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'youtube_channel' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'youtube_channel' ) ); ?>" type="text" value="<?php echo esc_attr( $instance['youtube_channel'] ); ?>" /><br/>
			<span>https://www.youtube.com/channel/ID</span><br/><br/>
			<label for="<?php echo esc_attr( $this->get_field_id( 'rss_feed' ) ); ?>"><?php _e( 'RSS Feed URL:', 'sassy-social-share' ); ?></label> 
			<input style="width: 95%" class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'rss_feed' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'rss_feed' ) ); ?>" type="text" value="<?php echo esc_attr( $instance['rss_feed'] ); ?>" /><br/>
			<span>http://www.example.com/feed/</span><br/><br/>
			<label for="<?php echo esc_attr( $this->get_field_id( 'gab' ) ); ?>"><?php _e( 'Gab.com URL:', 'sassy-social-share' ); ?></label> 
			<input style="width: 95%" class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'gab' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'gab' ) ); ?>" type="text" value="<?php echo esc_attr( $instance['gab'] ); ?>" /><br/>
			<span>https://gab.com/ID</span><br/><br/><br/>
			<label for="<?php echo esc_attr( $this->get_field_id( 'google_news' ) ); ?>"><?php _e( 'Google News URL:', 'sassy-social-share' ); ?></label> 
			<input style="width: 95%" class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'google_news' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'google_news' ) ); ?>" type="text" value="<?php echo esc_attr( $instance['google_news'] ); ?>" /><br/><br/>
			<label for="<?php echo esc_attr( $this->get_field_id( 'after_widget_content' ) ); ?>"><?php _e( 'After widget content:', 'sassy-social-share' ); ?></label> 
			<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'after_widget_content' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'after_widget_content' ) ); ?>" type="text" value="<?php echo esc_attr( $instance['after_widget_content'] ); ?>" /> 
		</p>
		<?php
    }
}