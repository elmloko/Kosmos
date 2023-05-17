<?php defined( 'ABSPATH' ) or die( "Cheating........Uh!!" ); ?>
<h2>Social Share Buttons</h2>
<form action="options.php" method="post">
<?php settings_fields( 'heateor_ssb_options' ); ?>
<div class="metabox-holder columns-2" id="post-body">
	<div class="heateor_sss_left_column">
		<div class="stuffbox" style="width:98.7%">
			<h3><label><?php _e( 'Master Control', 'sassy-social-share' );?></label></h3>
			<div class="inside" style="padding:5px;">
				<table width="100%" border="0" cellspacing="0" cellpadding="0" class="form-table editcomment menu_content_table">
					<tr>
						<th>
						<label><?php _e( "Use Premium Social Share Buttons", 'sassy-social-share' ); ?><a href="https://www.heateor.com/comparison-between-sassy-social-share-pro-and-premium/" target="_blank"> (<?php _e( 'Unlock', 'sassy-social-share' ) ?>)</a></label>
						<img id="heateor_ssb_enable_help" class="heateor_sss_help_bubble" src="<?php echo plugins_url( '../../images/info.png', __FILE__ ) ?>" />
						</th>
						<td>
						<input type="checkbox" disabled />
						</td>
					</tr>
					<tr class="heateor_sss_help_content" id="heateor_ssb_enable_help_cont">
						<td colspan="2">
						<div>
						<?php _e( 'Check if you want to use the premium buttons instead of the default ones', 'sassy-social-share' ) ?>
						</div>
						</td>
					</tr>
				</table>
			</div>
		</div>
		<div id="heateor_ssb_master_control">
			<div class="stuffbox">
				<h3 class="hndle"><label><?php _e( 'Themes For Standard Icons', 'sassy-social-share' );?></label> <a href="https://www.heateor.com/comparison-between-sassy-social-share-pro-and-premium/" target="_blank">(<?php _e( 'Unlock', 'sassy-social-share' ) ?>)</a></h3>
				<div class="inside">
					<table width="100%" border="0" cellspacing="0" cellpadding="0" class="form-table editcomment menu_content_table">
						<tr>
							<th>
							<label><?php _e( "Keep existing theme", 'sassy-social-share' ); ?></label>
							<img id="heateor_ssb_standard_enable_help" class="heateor_sss_help_bubble" src="<?php echo plugins_url( '../../images/info.png', __FILE__ ) ?>" />
							</th>
							<td>
							<input disabled type="checkbox" />
							</td>
						</tr>
						<tr class="heateor_sss_help_content" id="heateor_ssb_standard_enable_help_cont">
							<td colspan="2">
							<div>
							<?php _e( 'Check if you want to keep using the current theme', 'sassy-social-share' ) ?>
							</div>
							</td>
						</tr>
						<tbody id="heateor_ssb_standard_theme">
							<tr>
								<th colspan="2">
								<label><?php _e( "Choose a theme", 'sassy-social-share' ); ?></label>
								<img id="heateor_ssb_standard_theme_help" class="heateor_sss_help_bubble" src="<?php echo plugins_url( '../../images/info.png', __FILE__ ) ?>" />
								</th>
							</tr>

							<tr class="heateor_sss_help_content" id="heateor_ssb_standard_theme_help_cont">
								<td colspan="2">
								<div>
								<?php _e( 'Choose a theme for Standard Social Share icons', 'sassy-social-share' ) ?>
								</div>
								</td>
							</tr>

							<tr id="heateor_ssb_standard_icon_themes">
								<td>
								<input disabled type="radio" /><br/>
								<label style="float:left"><img height="94" width="343" src="<?php echo plugins_url( '../../images/themes/standard1.png', __FILE__ ); ?>" /></label>
								</td>

								<td>
								<input style="float:left" type="radio" disabled /><br/>
								<label style="float:left"><img height="94" width="343" src="<?php echo plugins_url( '../../images/themes/standard3.png', __FILE__ ); ?>" /></label>
								</td>
							</tr>

							<tr id="heateor_ssb_standard_icon_themes">
								<td>
								<input style="float:left" disabled type="radio" /><br/>
								<label style="float:left"><img height="94" width="343" src="<?php echo plugins_url( '../../images/themes/standard4.png', __FILE__ ); ?>" /></label>
								</td>
							</tr>
							<tr>
								<th>
								<label><?php _e( "Apply on Follow icons too", 'sassy-social-share' ); ?></label>
								<img id="heateor_ssb_apply_theme_on_standard_follow_icon_help" class="heateor_sss_help_bubble" src="<?php echo plugins_url( '../../images/info.png', __FILE__ ) ?>" />
								</th>
								<td>
								<input disabled type="checkbox" value="1" />
								</td>
							</tr>
							<tr class="heateor_sss_help_content" id="heateor_ssb_apply_theme_on_standard_follow_icon_help_cont">
								<td colspan="2">
								<div>
								<?php _e( 'Apply this theme on follow icons too', 'sassy-social-share' ) ?>
								</div>
								</td>
							</tr>
						</tbody>
					</table>
				</div>
			</div>
			<div class="stuffbox">
				<h3 class="hndle"><label><?php _e( 'Themes For Floating Icons', 'sassy-social-share' );?></label> <a href="https://www.heateor.com/comparison-between-sassy-social-share-pro-and-premium/" target="_blank">(<?php _e( 'Unlock', 'sassy-social-share' ) ?>)</a></h3>
				<div class="inside">
					<table width="100%" border="0" cellspacing="0" cellpadding="0" class="form-table editcomment menu_content_table">
					<tr>
						<th>
						<label><?php _e( "Keep existing theme", 'sassy-social-share' ); ?></label>
						<img id="heateor_ssb_floating_enable_help" class="heateor_sss_help_bubble" src="<?php echo plugins_url( '../../images/info.png', __FILE__ ) ?>" />
						</th>
						<td>
						<input disabled type="checkbox" />
						</td>
					</tr>
					<tr class="heateor_sss_help_content" id="heateor_ssb_floating_enable_help_cont">
						<td colspan="2">
						<div>
						<?php _e( 'Check if you want to keep using the current theme', 'sassy-social-share' ) ?>
						</div>
						</td>
					</tr>

					<tbody id="heateor_ssb_floating_theme">
						<tr>
							<th colspan="2">
							<label><?php _e( "Choose a theme", 'sassy-social-share' ); ?></label>
							<img id="heateor_ssb_floating_theme_help" class="heateor_sss_help_bubble" src="<?php echo plugins_url( '../../images/info.png', __FILE__ ) ?>" />
							</th>
						</tr>
						<tr class="heateor_sss_help_content" id="heateor_ssb_floating_theme_help_cont">
							<td colspan="2">
							<div>
							<?php _e( 'Choose a theme for Floating Social Share icons', 'sassy-social-share' ) ?>
							</div>
							</td>
						</tr>
						<tr id="heateor_ssb_floating_icon_themes">
							<td>
							<input style="float:left" disabled type="radio" /><br/>
							<label style="float:left"><img height="350" width="70" src="<?php echo plugins_url( '../../images/themes/floating1.png', __FILE__ ); ?>" /></label>
							</td>

							<td>
							<input style="float:left" disabled type="radio" /><br/>
							<label style="float:left"><img height="350" width="70" src="<?php echo plugins_url( '../../images/themes/floating3.png', __FILE__ ); ?>" /></label>
							</td>

							<td>
							<input style="float:left" type="radio" disabled /><br/>
							<label style="float:left"><img height="350" width="70" src="<?php echo plugins_url( '../../images/themes/floating4.png', __FILE__ ); ?>" /></label>
							</td>
						</tr>
						<tr>
							<th>
							<label><?php _e( "Apply on Follow icons too", 'sassy-social-share' ); ?></label>
							<img id="heateor_ssb_apply_floating_on_follow_icon_help" class="heateor_sss_help_bubble" src="<?php echo plugins_url( '../../images/info.png', __FILE__ ) ?>" />
							</th>
							<td>
							<input disabled type="checkbox" value="1" />
							</td>
						</tr>
						<tr class="heateor_sss_help_content" id="heateor_ssb_apply_floating_on_follow_icon_help_cont">
							<td colspan="2">
							<div>
							<?php _e( 'Apply this theme on follow icons too', 'sassy-social-share' ) ?>
							</div>
							</td>
						</tr>
					</tbody>
					</table>
				</div>
			</div>
			<div class="stuffbox">
				<h3><label><?php _e( 'Animations for Standard Social Share Icons', 'sassy-social-share' ) ?> <a href="https://www.heateor.com/comparison-between-sassy-social-share-pro-and-premium/" target="_blank">(<?php _e( 'Unlock', 'sassy-social-share' ) ?>)</a></label></h3>
				<div class="inside">
					<table width="100%" border="0" cellspacing="0" cellpadding="0" class="form-table editcomment menu_content_table">
						<tr>
							<th>
							<label><?php _e( "Select an animation", 'sassy-social-share' ); ?></label>
							</th>
							<td>
							<select>
								<option value=""><?php _e( 'None', 'sassy-social-share' ) ?></option>
								<option value="shadow"><?php _e( 'Shadow', 'sassy-social-share' ) ?></option>
								<option value="popup"><?php _e( 'Pop up', 'sassy-social-share' ) ?></option>
								<option value="360rotate"><?php _e( '360 Rotate', 'sassy-social-share' ) ?></option>
								<option value="shake"><?php _e( 'Shake', 'sassy-social-share' ) ?></option>
								<option value="pulse"><?php _e( 'Pulse', 'sassy-social-share' ) ?></option>
							</select>
							</td>
						</tr>
						<tr>
							<th>
							<label><?php _e( "Apply on Follow icons too", 'sassy-social-share' ); ?></label>
							<img id="heateor_ssb_apply_on_standard_follow_icon_help" class="heateor_sss_help_bubble" src="<?php echo plugins_url( '../../images/info.png', __FILE__ ) ?>" />
							</th>
							<td>
							<input type="checkbox" disabled />
							</td>
						</tr>
						<tr class="heateor_sss_help_content" id="heateor_ssb_apply_on_standard_follow_icon_help_cont">
							<td colspan="2">
							<div>
							<?php _e( 'Apply this animation on follow icons too', 'sassy-social-share' ) ?>
							</div>
							</td>
						</tr>
					</table>
				</div>
			</div>
			<div class="stuffbox">
				<h3><label><?php _e( 'Animations for Floating Social Share Icons', 'sassy-social-share' ) ?> <a href="https://www.heateor.com/comparison-between-sassy-social-share-pro-and-premium/" target="_blank">(<?php _e( 'Unlock', 'sassy-social-share' ) ?>)</a></label></h3>
				<div class="inside">
					<table width="100%" border="0" cellspacing="0" cellpadding="0" class="form-table editcomment menu_content_table">
						<tr>
							<th>
							<label><?php _e( "Select an animation", 'sassy-social-share' ); ?></label>
							</th>
							<td>
							<select>
								<option value=""><?php _e( 'None', 'sassy-social-share' ) ?></option>
								<option value="shadow"><?php _e( 'Shadow', 'sassy-social-share' ) ?></option>
								<option value="popup"><?php _e( 'Pop up', 'sassy-social-share' ) ?></option>
								<option value="360rotate"><?php _e( '360 Rotate', 'sassy-social-share' ) ?></option>
								<option value="stretch"><?php _e( 'Stretch', 'sassy-social-share' ) ?></option>
								<option value="shake"><?php _e( 'Shake', 'sassy-social-share' ) ?></option>
								<option value="pulse"><?php _e( 'Pulse', 'sassy-social-share' ) ?></option>
							</select>
							</td>
						</tr>
						<tr>
							<th>
							<label><?php _e( "Apply on Follow icons too", 'sassy-social-share' ); ?></label>
							<img id="heateor_ssb_apply_on_floating_follow_icon_help" class="heateor_sss_help_bubble" src="<?php echo plugins_url( '../../images/info.png', __FILE__ ) ?>" />
							</th>
							<td>
							<input type="checkbox" disabled value="1" />
							</td>
						</tr>
						<tr class="heateor_sss_help_content" id="heateor_ssb_apply_on_floating_follow_icon_help_cont">
							<td colspan="2">
							<div>
							<?php _e( 'Apply this animation on follow icons too', 'sassy-social-share' ) ?>
							</div>
							</td>
						</tr>
					</table>
				</div>
			</div>
			<div class="stuffbox">
				<h3><label><?php _e( 'Customization for Standard Icons', 'sassy-social-share' ) ?></label> <a href="https://www.heateor.com/comparison-between-sassy-social-share-pro-and-premium/" target="_blank">(<?php _e( 'Unlock', 'sassy-social-share' ) ?>)</a></h3>
				<div class="inside">
					<table width="100%" border="0" cellspacing="0" cellpadding="0" class="form-table editcomment menu_content_table">
						<tr>
							<th>
								<label><?php _e( "Space between Icons", 'sassy-social-share' ); ?></label>
							</th>
							<td>
								<input style="width:50px" disabled type="text" />px
							</td>
						</tr>
						<tr class="heateor_sss_help_content" id="heateor_ssb_space_between_icon_standard_help_cont">
							<td colspan="2">
							<div>
							<?php _e( 'Specify a value for space between Standard icons', 'sassy-social-share' ) ?>
							</div>
							</td>
						</tr>
						<tr>
							<th>
								<label><?php _e( "Space above the icons", 'sassy-social-share' ); ?></label>
							</th>
							<td>
								<input style="width:50px" disabled type="text" />px
							</td>
						</tr>
						<tr class="heateor_sss_help_content" id="heateor_ssb_space_above_the_icon_standard_help_cont">
							<td colspan="2">
							<div>
							<?php _e( 'Specify a value in pixels for space above the Standard icons', 'sassy-social-share' ) ?>
							</div>
							</td>
						</tr>
						<tr>
							<th>
								<label><?php _e( "Space below the icons", 'sassy-social-share' ); ?></label>
							</th>
							<td>
								<input style="width:50px" type="text" disabled />px
							</td>
						</tr>
						<tr class="heateor_sss_help_content" id="heateor_ssb_space_below_the_icon_standard_help_cont">
							<td colspan="2">
							<div>
							<?php _e( 'Specify a value in pixels for space below the standard icons', 'sassy-social-share' ) ?>
							</div>
							</td>
						</tr>
						<tr>
							<th>
							<label><?php _e( "Apply on Follow icons too", 'sassy-social-share' ); ?></label>
							<img id="heateor_ssb_space_between_standard_follow_icons_also_help" class="heateor_sss_help_bubble" src="<?php echo plugins_url( '../../images/info.png', __FILE__ ) ?>" />
							</th>
							<td>
							<input disabled type="checkbox" />
							</td>
						</tr>
						<tr class="heateor_sss_help_content" id="heateor_ssb_space_between_standard_follow_icons_also_help_cont">
							<td colspan="2">
							<div>
							<?php _e( 'Apply space on follow icons too', 'sassy-social-share' ) ?>
							</div>
							</td>
						</tr>
					</table>
				</div>
			</div>
			<div class="stuffbox">
				<h3><label><?php _e( 'Customization for Floating Icons', 'sassy-social-share' ) ?></label> <a href="https://www.heateor.com/comparison-between-sassy-social-share-pro-and-premium/" target="_blank">(<?php _e( 'Unlock', 'sassy-social-share' ) ?>)</a></h3>
				<div class="inside">
					<table width="100%" border="0" cellspacing="0" cellpadding="0" class="form-table editcomment menu_content_table">
						<tr>
							<th>
								<label><?php _e( "Space between icons", 'sassy-social-share' ); ?></label>
							</th>
							<td>
								<input style="width:50px" disabled type="text" />px
							</td>
						</tr>
						<tr class="heateor_sss_help_content" id="heateor_ssb_space_between_icon_floating_help_cont">
							<td colspan="2">
							<div>
							<?php _e( 'Specify a value in pixels for space between floating icons', 'sassy-social-share' ) ?>
							</div>
							</td>
						</tr>
						<tr>
							<th>
							<label><?php _e( "Apply on Follow icons too", 'sassy-social-share' ); ?></label>
							<img id="heateor_ssb_space_between_floating_follow_icons_also_help" class="heateor_sss_help_bubble" src="<?php echo plugins_url( '../../images/info.png', __FILE__ ) ?>" />
							</th>
							<td>
							<input disabled type="checkbox" value="1" />
							</td>
						</tr>
						<tr class="heateor_sss_help_content" id="heateor_ssb_space_between_floating_follow_icons_also_help_cont">
							<td colspan="2">
							<div>
							<?php _e( 'Apply space on follow icons too', 'sassy-social-share' ) ?>
							</div>
							</td>
						</tr>
					</table>
				</div>
			</div>
		</div>
	</div>
	<?php require 'sassy-social-share-about.php' ?>
</div>
</form>