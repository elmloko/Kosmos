<?php defined( 'ABSPATH' ) or die( "Cheating........Uh!!" ); ?>
<h2>Social Sharing Analytics</h2>
<div class="metabox-holder columns-2" id="post-body">
<form action="options.php" method="post">
<?php settings_fields( 'heateor_sss_ssga_options' ); ?>
	<div class="heateor_sss_left_column">
		<div class="stuffbox" style="width:98.7%">
			<h3><label><?php _e( 'Master Control', 'sassy-social-share' );?></label></h3>
			<div class="inside" style="padding:5px;">
				<table width="100%" border="0" cellspacing="0" cellpadding="0" class="form-table editcomment menu_content_table">
					<tr>
						<th>
						<label for="heateor_ssga_analytics_enable"><?php _e( "Track Social Shares via Google Analytics", 'sassy-social-share' ); ?></label> <a href="https://www.heateor.com/comparison-between-sassy-social-share-pro-and-premium/" target="_blank">(<?php _e( 'Unlock', 'sassy-social-share' ) ?>)</a>
						<img id="heateor_ssga_analytics_enable_help" class="heateor_sss_help_bubble" src="<?php echo plugins_url( '../../images/info.png', __FILE__ ) ?>" />
						</th>
						<td>
						<input type="checkbox" value="1" disabled />
						</td>
					</tr>

					<tr class="heateor_sss_help_content" id="heateor_ssga_analytics_enable_help_cont">
						<td colspan="2">
						<div>
						<?php _e( 'Check it if you want to track social shares in your Google analytics dashboard', 'sassy-social-share' ) ?>
						</div>
						</td>
					</tr>
				</table>
			</div>
		</div>

		<div class="stuffbox" id="heateor_ssga_master_control">
			<h3 class="hndle"><label><?php _e( 'Configuration', 'sassy-social-share' );?></label></h3>
			<div class="inside">
				<table width="100%" border="0" cellspacing="0" cellpadding="0" class="form-table editcomment menu_content_table">
						<tr>
							<th>
							<label><?php _e( "Already using Google Analytics?", 'sassy-social-share' ); ?></label>
							<img id="heateor_ssga_analytics_preinstalled_help" class="heateor_sss_help_bubble" src="<?php echo plugins_url( '../../images/info.png', __FILE__ ) ?>" />
							</th>
							<td>
							<label><input type="radio" disabled /><?php _e( 'Yes, I am using ', 'sassy-social-share' ) ?><a href="https://wordpress.org/plugins/google-analytics-for-wordpress/" target="_blank"><?php _e( 'Google Analytics by MonsterInsights', 'sassy-social-share' ) ?></a></label><br/>
							<label><input type="radio" disabled /><?php _e( 'Yes, I am using ', 'sassy-social-share' ) ?><a href="http://codecanyon.net/item/actionable-google-analytics-for-woocommerce/9899552" target="_blank"><?php _e( 'Actionable Google Analytics by Tatvic', 'sassy-social-share' ) ?></a></label><br/>
							<label><input type="radio" disabled /><?php _e( 'Yes, I am using ', 'sassy-social-share' ) ?><a href="https://wordpress.org/plugins/google-analytics-dashboard-for-wp/" target="_blank"><?php _e( 'Google Analytics Dashboard for WP', 'sassy-social-share' ) ?></a></label><br/>
							<label><input type="radio" disabled /><?php _e( 'Yes, I am using Google Tag Manager', 'sassy-social-share' ) ?></label><br/>
							<small><?php echo sprintf(__('Follow the documentation at <a href="%s" target="_blank">this link</a> to track shares using Google Tag Manager', 'sassy-social-share' ), 'http://support.heateor.com/track-social-share-google-tag-manager/') ?></small><br/>
							<label><input type="radio" disabled /><?php _e( 'Yes, I am using other plugin/functionality', 'sassy-social-share' ) ?></label><br/>
							<label><input type="radio" disabled /><?php _e( 'No', 'sassy-social-share' ) ?></label>
							</td>
						</tr>

						<tr class="heateor_sss_help_content" id="heateor_ssga_analytics_preinstalled_help_cont">
							<td colspan="2">
							<div>
							<?php _e( 'Have you already enabled Google Analytics at your website?', 'sassy-social-share' ) ?>
							</div>
							</td>
						</tr>

						<tbody id="heateor_ssga_tracking_id_row">
						<tr>
							<th>
							<label><?php _e( "Google Analytics Tracking ID/GA 4 Measurement ID", 'sassy-social-share' ); ?></label>
							<img id="heateor_ssga_analytics_id_help" class="heateor_sss_help_bubble" src="<?php echo plugins_url('../../images/info.png', __FILE__) ?>" />
							</th>
							<td>
							<input type="text" disabled />
							</td>
						</tr>

						<tr class="heateor_sss_help_content" id="heateor_ssga_analytics_id_help_cont">
							<td colspan="2">
							<div>
							<?php _e( 'Follow <a href="https://support.google.com/analytics/answer/1032385" target="_blank">this</a> link  to get tracking ID and <a href="https://support.google.com/analytics/answer/9539598#find-G-ID" target="_blank">this</a> link to get GA 4 Measurement ID', 'sassy-social-share' ) ?>
							</div>
							</td>
						</tr>

						<tr>
							<td colspan="2">
							<p><?php _e( '<strong>Note:</strong> Social Shares can be tracked in realtime in the <strong>Event count by Event name</strong> panel in the <strong>Realtime</strong> section in Google Analytics dashboard. This data may take 24-48 hours to appear in the <strong>Engagement > Events</strong> section in the Google Analytics dashboard.', 'sassy-social-share' ) ?></p>
							</td>
						</tr>
					</tbody>
				</table>
			</div>
		</div>
	</div>
	<?php
	require 'sassy-social-share-about.php'
	?>
</form>
</div>