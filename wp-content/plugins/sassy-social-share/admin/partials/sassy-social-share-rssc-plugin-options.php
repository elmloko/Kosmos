<?php defined( 'ABSPATH' ) or die( "Cheating........Uh!!" ); ?>
<h2>Recover Social Share Counts</h2>
<div class="metabox-holder columns-2" id="post-body">
<form action="options.php" method="post">
<?php settings_fields( 'heateor_sss_rssc_options' ); ?>
	<div class="heateor_sss_left_column">
		<div class="stuffbox" style="width:98.7%">
			<h3><label><?php _e( 'Master Control', 'sassy-social-share' );?></label></h3>
			<div class="inside" style="padding:5px;">
				<table width="100%" border="0" cellspacing="0" cellpadding="0" class="form-table editcomment menu_content_table">
					<tr>
						<th>
						<label><?php _e( "Yes, I want to recover shares", 'sassy-social-share' ); ?></label> <a href="https://www.heateor.com/comparison-between-sassy-social-share-pro-and-premium/" target="_blank">(<?php _e( 'Unlock', 'sassy-social-share' ) ?>)</a>
						<img id="heateor_sss_recover_shares_help" class="heateor_sss_help_bubble" src="<?php echo plugins_url('../../images/info.png', __FILE__) ?>" />
						</th>
						<td>
						<input type="checkbox" disabled />
						</td>
					</tr>

					<tr class="heateor_sss_help_content" id="heateor_sss_recover_shares_help_cont">
						<td colspan="2">
						<div>
						<?php _e( 'Check it if you want to recover lost social shares after enabling SSL or moving your website to another domain', 'sassy-social-share' ) ?>
						</div>
						</td>
					</tr>
				</table>
			</div>
		</div>

		<div class="stuffbox" id="heateor_sssc_master_control">
			<h3 class="hndle"><label><?php _e( 'Configuration', 'sassy-social-share' );?></label></h3>
			<div class="inside">
				<table width="100%" border="0" cellspacing="0" cellpadding="0" class="form-table editcomment menu_content_table">
					<tr>
						<th>
						<label><?php _e( "Recover social shares from 'http' webpages", 'sassy-social-share' ); ?></label>
						<img id="heateor_rssc_recover_http_shares_help" class="heateor_sss_help_bubble" src="<?php echo plugins_url( '../../images/info.png', __FILE__ ) ?>" />
						</th>
						<td>
						<input disabled type="checkbox" />
						</td>
					</tr>
					
					<tr class="heateor_sss_help_content" id="heateor_rssc_recover_http_shares_help_cont">
						<td colspan="2">
						<div>
						<?php _e( 'Recover social shares lost after moving your website to SSL/Https', 'sassy-social-share' ) ?>
						</div>
						</td>
					</tr>

					<tr>
						<th>
						<label><?php _e( "Old website domain to recover social shares for (only if changed website domain)", 'sassy-social-share' ); ?></label>
						<img id="heateor_rssc_list_id_help" class="heateor_sss_help_bubble" src="<?php echo plugins_url( '../../images/info.png', __FILE__ ) ?>" />
						</th>
						<td>
						<input disabled type="text" />
						</td>
					</tr>

					<tr class="heateor_sss_help_content" id="heateor_rssc_list_id_help_cont">
						<td colspan="2">
						<div>
						<?php _e( 'Specify the old domain of your website (without "http" and "https") for which you want to recover social shares', 'sassy-social-share' ) ?>
						</div>
						</td>
					</tr>
				</table>
			</div>
		</div>
	</div>
	<?php require 'sassy-social-share-about.php' ?>
<form>
</div>