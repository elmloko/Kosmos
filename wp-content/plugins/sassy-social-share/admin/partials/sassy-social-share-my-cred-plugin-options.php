<?php defined( 'ABSPATH' ) or die( "Cheating........Uh!!" ); ?>
<h2>Social Share myCRED Integration</h2>
<div class="metabox-holder columns-2" id="post-body">
	<form action="options.php" method="post">
	<?php settings_fields( 'heateor_sss_mycred_options' ); ?>
	<div class="heateor_sss_left_column">
		<div class="stuffbox" style="width:98.7%">
			<h3><label><?php _e( 'Master Control', 'sassy-social-share' );?></label></h3>
			<div class="inside" style="padding:5px;">
				<table width="100%" border="0" cellspacing="0" cellpadding="0" class="form-table editcomment menu_content_table">
					<tr>
						<th>
						<label for="heateor_mycred_enable"><a href="https://www.heateor.com/comparison-between-sassy-social-share-pro-and-premium/" target="_blank"><?php _e( "Reward myCRED points for social share", 'sassy-social-share' ); ?></a></label>
						<img id="heateor_mycred_enable_help" class="heateor_sss_help_bubble" src="<?php echo plugins_url( '../../images/info.png', __FILE__ ) ?>" />
						</th>
						<td>
						<input disabled type="checkbox" />
						</td>
					</tr>

					<tr class="heateor_sss_help_content" id="heateor_mycred_enable_help_cont">
						<td colspan="2">
						<div>
						<?php _e( 'Check it if you want to reward myCRED points for social share', 'sassy-social-share' ) ?>
						</div>
						</td>
					</tr>
				</table>
			</div>
		</div>

		<div class="stuffbox" id="heateor_mycred_master_control">
			<h3 class="hndle"><label><?php _e( 'Configuration', 'sassy-social-share' );?></label></h3>
			<div class="inside" style="padding:7px">
				<p>
					<?php echo sprintf( __( 'Install, activate <a href="%s" target="_blank">myCRED</a> plugin. Navigate to <a href="%s">Points > Hooks</a> page from left sidebar and activate "Points for Social Sharing" hook.', 'sassy-social-share' ), 'https://wordpress.org/plugins/mycred/', 'admin.php?page=mycred-hooks', 'admin.php?page=myCRED_page_hooks' ); ?>
				</p>
			</div>
		</div>
	</div>
	<?php
	require 'sassy-social-share-about.php'
	?>
	</form>
</div>