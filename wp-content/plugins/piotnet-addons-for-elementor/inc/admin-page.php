<div class="wrap">
	<div class="pafe-header">
		<div class="pafe-header__left">
			<div class="pafe-header__logo">
				<img src="<?php echo plugin_dir_url( __FILE__ ) . '../assets/images/piotnet-logo.png'; ?>" alt="">
			</div>
			<h2 class="pafe-header__headline">Piotnet Addons For Elementor Settings (PAFE PRO)</h2>
		</div>
		<div class="pafe-header__right">
			<a class="pafe-header__button pafe-header__button--gradient" href="https://pafe.piotnet.com/" target="_blank">GO PRO NOW</a>
		</div>
	</div>
	<div class="pafe-wrap">
		<form method="post" action="options.php" data-pafe-features>
	    <?php settings_fields( 'piotnet-addons-for-elementor-features-settings-group' ); ?>
	    <?php do_settings_sections( 'piotnet-addons-for-elementor-features-settings-group' ); ?>
	    <div class="pafe-toggle-features">
	    	<br>
	    	<br>
	    	<div class="pafe-toggle-features__button" data-pafe-toggle-features-enable>Enable All</div>
	    	<div class="pafe-toggle-features__button pafe-toggle-features__button--disable" data-pafe-toggle-features-disable>Disable All</div>
	    	<div class="pafe-toggle-features__button" data-pafe-features-save><?php _e('Save Settings','pafe'); ?></div>
	    	<br>
	    </div>
	    <?php
	    	if ( !defined('PAFE_VERSION') ) :
	    ?>
	    	<p>Please Install or Active Free Version on Wordpress Repository to Enable Free Features <a href="https://wordpress.org/plugins/piotnet-addons-for-elementor">https://wordpress.org/plugins/piotnet-addons-for-elementor</a></p>
		<?php endif; ?>
			<ul class="pafe-features">
				<?php
					require_once( __DIR__ . '/features.php' );
					$features_free = json_decode( PAFE_FEATURES_FREE, true );
					$features_pro = json_decode( PAFE_FEATURES_PRO, true );
					$features = $features_free + $features_pro;

					foreach ($features as $feature) :

						$feature_disable = '';

						if ( defined('PAFE_VERSION') && !$feature['pro'] || defined('PAFE_PRO_VERSION') && $feature['pro'] ) {
							$feature_enable = esc_attr( get_option($feature['option'], 2) );
							if ( $feature_enable == 2 ) {
								$feature_enable = 1;
							}
						}

						if ( !defined('PAFE_VERSION') && !$feature['pro'] || !defined('PAFE_PRO_VERSION') && $feature['pro'] ) {
							$feature_enable = 0;
							$feature_disable = 1;
						}
						
				?>
					<li>
						<label class="pafe-switch">
							<input type="checkbox"<?php if( empty( $feature_disable ) ) : ?> name="<?php echo $feature['option']; ?>"<?php endif; ?> value="1" <?php checked( $feature_enable, 1 ); ?><?php if( !empty( $feature_disable ) ) { echo ' disabled'; } ?>>
							<span class="pafe-slider round"></span>
						</label>
						<a href="<?php echo $feature['url']; ?>" target="_blank"><?php echo $feature['name']; ?><?php if( $feature['pro'] ) : ?><span class="pafe-pro-version"></span><?php endif; ?></a>
					</li>
				<?php endforeach; ?>
			</ul>
			<div class="pafe-toggle-features">
		    	<br>
		    	<br>
		    	<div class="pafe-toggle-features__button" data-pafe-toggle-features-enable>Enable All</div>
		    	<div class="pafe-toggle-features__button pafe-toggle-features__button--disable" data-pafe-toggle-features-disable>Disable All</div>
		    	<div class="pafe-toggle-features__button" data-pafe-features-save><?php _e('Save Settings','pafe'); ?></div>
		    	<br>
		    </div>
		</form>
		<hr>
		<div class="pafe-bottom">
			<div class="pafe-bottom__left">
				<h3>Tutorials</h3>
				<a href="https://pafe.piotnet.com/?wpam_id=1" target="_blank">https://pafe.piotnet.com/?wpam_id=1</a>
				<h3>Support</h3>
				<a href="mailto:support@piotnet.com">support@piotnet.com</a>
				<h3>Reviews</h3>
				<a href="https://wordpress.org/support/plugin/piotnet-addons-for-elementor/reviews/?filter=5#new-post" target="_blank">https://wordpress.org/plugins/piotnet-addons-for-elementor/#reviews</a>
			</div>
			<div class="pafe-bottom__right">
				<div class="pafe-license">
					<h3>Activate License</h3>
					<?php
						$domain = get_option('siteurl'); 
						$domain = str_replace('http://', '', $domain);
						$domain = str_replace('https://', '', $domain);
						$domain = str_replace('www', '', $domain); 

						$response = wp_remote_post( 'https://pafe.piotnet.com/login2.php', array(
							'method' => 'POST',
							'timeout' => 45,
							'redirection' => 5,
							'httpversion' => '1.0',
							'blocking' => true,
							'headers' => array(),
							'body' => array( 'username' => get_option('piotnet-addons-for-elementor-pro-username'), 'password' => get_option('piotnet-addons-for-elementor-pro-password'), 'dm' => $domain ),
							'cookies' => array(),
							'sslverify' => false,
						    )
						);
					?>
					<div class="pafe-license__description"><?php _e('Enter Your Account at','pafe'); ?> <a href="https://pafe.piotnet.com/my-account/" target="_blank">https://pafe.piotnet.com/my-account/</a> <?php _e('to enable all features and receive new updates. Status: ','pafe'); ?>
						<?php 
							if ( is_wp_error( $response ) ) {
							    $error_message = $response->get_error_message();
							    echo "Something went wrong: $error_message";
							} else {
							    $response_body = wp_remote_retrieve_body( $response );
							    $response_body_trim = trim($response_body);
							    echo $response_body;

							   	if ($response_body_trim == 'Logged in successfully.') {
									update_option( '_site_transient_update_plugins', '' );
							   	}
							}
						?>
					</div>
					<form method="post" action="options.php">
					    <?php settings_fields( 'piotnet-addons-for-elementor-pro-settings-group' ); ?>
					    <?php do_settings_sections( 'piotnet-addons-for-elementor-pro-settings-group' ); ?>
					    <table class="form-table">
					        <tr valign="top">
					        <th scope="row">Username</th>
					        <td><input type="text" name="piotnet-addons-for-elementor-pro-username" value="<?php echo esc_attr( get_option('piotnet-addons-for-elementor-pro-username') ); ?>" class="regular-text"/></td>
					        </tr>
					        <tr valign="top">
					        <th scope="row">Password</th>
					        <td><input type="Password" name="piotnet-addons-for-elementor-pro-password" value="<?php echo esc_attr( get_option('piotnet-addons-for-elementor-pro-password') ); ?>" class="regular-text"/></td>
					        </tr>
					        <tr valign="top">
					        <th scope="row">Remove License</th>
					        <td><input type="checkbox" name="piotnet-addons-for-elementor-pro-remove-license" class="regular-text"/></td>
					        </tr>
					    </table>
					    <?php submit_button(); ?>
					</form>
				</div>
			</div>
		</div>
	</div>
</div>