<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use PremiumAddons\Includes\Helper_Functions;

// Premium Addons Pro Classes
use PremiumAddonsPro\Includes\White_Label\Helper;

// Get settings
$class = 'premium-white-label-form';

if ( Helper_Functions::check_papro_version() ) {

	$settings = Helper::get_white_labeling_settings();

	$hide_notifications = isset( $settings['premium-wht-lbl-not'] ) ? $settings['premium-wht-lbl-not'] : false;

} else {

	$hide_notifications = false;

	$class .= ' pro-inactive';

	$settings = array(
		'premium-wht-lbl-name'            => '',
		'premium-wht-lbl-url'             => '',
		'premium-wht-lbl-plugin-name'     => '',
		'premium-wht-lbl-short-name'      => '',
		'premium-wht-lbl-desc'            => '',
		'premium-wht-lbl-row'             => '',
		'premium-wht-lbl-name-pro'        => '',
		'premium-wht-lbl-url-pro'         => '',
		'premium-wht-lbl-plugin-name-pro' => '',
		'premium-wht-lbl-desc-pro'        => '',
		'premium-wht-lbl-changelog'       => '',
		'premium-wht-lbl-option'          => '',
		'premium-wht-lbl-rate'            => '',
		'premium-wht-lbl-about'           => '',
		'premium-wht-lbl-license'         => '',
		'premium-wht-lbl-logo'            => '',
		'premium-wht-lbl-version'         => '',
		'premium-wht-lbl-prefix'          => '',
		'premium-wht-lbl-badge'           => '',
	);
}

if ( ! Helper_Functions::check_papro_version() ) {

	$campaign = Helper_Functions::get_campaign_link( 'https://premiumaddons.com/pro/', 'whitelabel-page', 'wp-dash', 'dashboard' );

	?>
	<div class="pa-white-label-notice">
		<div class="pa-white-label-notice-content">
			<div class="pa-white-label-notice-logo">
				<img src="<?php echo esc_url( PREMIUM_ADDONS_URL ) . 'admin/images/pa-logo-symbol.png'; ?>" alt="Premium Addons White Labeling Notice">
			</div>
			<h2><?php esc_html_e( 'Get Premium Addons <span>Pro</span> to Enable White Labeling Options', 'premium-addons-for-elementor' ); ?></h2>
			<p><?php esc_html_e( 'Premium Addons can be completely re-branded with your own brand name and author details. Your clients will never know what tools you are using to build their website and will think that this is your own tool set. White-labeling works as long as your license is active.', 'premium-addons-for-elementor' ); ?></p>
			<a class="pa-btn pa-get-pro" href="<?php echo esc_attr( $campaign ); ?>" target="_blank"><?php esc_html_e( 'Get PRO', 'premium-addons-for-elementor' ); ?></a>
		</div>
	</div>
	<?php
}

?>

<div class="pa-section-content">
	<div class="row">
		<div class="col-full">
		<form action="" method="POST" id="pa-white-label" class="<?php echo esc_attr( $class ); ?>" name="pa-white-label-settings">
			<div id="pa-white-label" class="pa-settings-tab pa-wht-lbl-settings">
				<div class="pa-row">
					<div class="pa-wht-lbl-settings-wrap">
						<h3 class="pa-wht-lbl-title pa-wht-lbl-head"><?php echo esc_html_e( 'Free Version', 'premium-addons-for-elementor' ); ?></h3>
						<div class="pa-wht-lbl-group-wrap">
							<!-- Author Name -->
							<label for="premium-wht-lbl-name" class="pa-input-label"><?php echo esc_html_e( 'Author Name', 'premium-addons-for-elementor' ); ?></label>
							<input name="premium-wht-lbl-name" id="premium-wht-lbl-name" type="text" placeholder="Leap13" value="<?php echo esc_attr( $settings['premium-wht-lbl-name'] ); ?>">
							<!-- Author URL -->
							<label for="premium-wht-lbl-url" class="pa-input-label"><?php echo esc_html_e( 'Author URL', 'premium-addons-for-elementor' ); ?></label>
							<input name="premium-wht-lbl-url" id="premium-wht-lbl-url" type="text" placeholder="https://premiumaddons.com" value="<?php echo esc_attr( $settings['premium-wht-lbl-url'] ); ?>">
							<!-- Plugin Name -->
							<label for="premium-wht-lbl-plugin-name" class="pa-input-label"><?php echo esc_html_e( 'Plugin Name', 'premium-addons-for-elementor' ); ?></label>
							<input name="premium-wht-lbl-plugin-name" id="premium-wht-lbl-plugin-name" type="text" placeholder="Premium Addons for Elementor" value="<?php echo esc_attr( $settings['premium-wht-lbl-plugin-name'] ); ?>">

							<!-- Plugin Description -->
							<label for="premium-wht-lbl-desc" class="pa-input-label"><?php echo esc_html_e( 'Plugin Description', 'premium-addons-for-elementor' ); ?></label>
							<input name="premium-wht-lbl-desc" id="premium-wht-lbl-desc" type="text" placeholder="Premium Addons for Elementor plugin includes widgets and addons.." value="<?php echo esc_attr( $settings['premium-wht-lbl-desc'] ); ?>">

							<p class="pa-input-label"><?php echo esc_html_e( 'Hide Plugin Row Meta Links', 'premium-addons-for-elementor' ); ?></p>
							<input name="premium-wht-lbl-row" id="premium-wht-lbl-row" type="checkbox" <?php checked( 1, $settings['premium-wht-lbl-row'], true ); ?>>
							<label for="premium-wht-lbl-row"></label>
							<span><?php echo esc_html_e( 'This will hide Docs, FAQs, and Video Tutorials links located on the plugins page.', 'premium-addons-for-elementor' ); ?></span>
						</div>
					</div>

					<div class="pa-wht-lbl-settings-wrap">
						<h3 class="pa-wht-lbl-title pa-wht-lbl-head"><?php echo esc_html_e( 'PRO Version', 'premium-addons-for-elementor' ); ?></h3>
						<div class="pa-wht-lbl-group-wrap">

							<label for="premium-wht-lbl-name-pro" class="pa-input-label"><?php echo esc_html_e( 'Author Name', 'premium-addons-for-elementor' ); ?></label>
							<input name="premium-wht-lbl-name-pro" id="premium-wht-lbl-name-pro" type="text" placeholder="Leap13" value="<?php echo esc_attr( $settings['premium-wht-lbl-name-pro'] ); ?>">

							<label for="premium-wht-lbl-url-pro" class="pa-input-label"><?php echo esc_html_e( 'Author URL', 'premium-addons-for-elementor' ); ?></label>
							<input name="premium-wht-lbl-url-pro" id="premium-wht-lbl-url-pro" type="text" placeholder="https://premiumaddons.com" value="<?php echo esc_attr( $settings['premium-wht-lbl-url-pro'] ); ?>">

							<label for="premium-wht-lbl-plugin-name-pro" class="pa-input-label"><?php echo esc_html_e( 'Plugin Name', 'premium-addons-for-elementor' ); ?></label>
							<input name="premium-wht-lbl-plugin-name-pro" id="premium-wht-lbl-plugin-name-pro" type="text" placeholder="Premium Addons PRO for Elementor" value="<?php echo esc_attr( $settings['premium-wht-lbl-plugin-name-pro'] ); ?>">

							<label for="premium-wht-lbl-desc-rpo" class="pa-input-label"><?php echo esc_html_e( 'Plugin Description', 'premium-addons-for-elementor' ); ?></label>
							<input name="premium-wht-lbl-desc-pro" id="premium-wht-lbl-desc-pro" type="text" placeholder="Premium Addons PRO Plugin Includes 33+ premium widgets & addons..." value="<?php echo esc_attr( $settings['premium-wht-lbl-desc-pro'] ); ?>">

							<p class="pa-input-label"><?php echo esc_html_e( 'Hide Plugin Changelog Link', 'premium-addons-for-elementor' ); ?></p>
							<input name="premium-wht-lbl-changelog" id="premium-wht-lbl-changelog" type="checkbox" <?php checked( 1, $settings['premium-wht-lbl-changelog'], true ); ?>>
							<label for="premium-wht-lbl-changelog"></label>
							<span><?php echo esc_html_e( 'This will hide the Changelog link located on the plugins page.', 'premium-addons-for-elementor' ); ?></span>

						</div>
					</div>
					<div class="pa-wht-lbl-settings-wrap">
						<h3 class="pa-wht-lbl-title pa-wht-lbl-head"><?php echo esc_html_e( 'General Options', 'premium-addons-for-elementor' ); ?></h3>
						<div class="pa-wht-lbl-group-wrap">
							<!-- Widgets Category Name -->
							<label for="premium-wht-lbl-short-name" class="pa-input-label"><?php echo esc_html_e( 'Widgets Category Name', 'premium-addons-for-elementor' ); ?></label>
							<input name="premium-wht-lbl-short-name" id="premium-wht-lbl-short-name" type="text" placeholder="Premium Addons" value="<?php echo esc_attr( $settings['premium-wht-lbl-short-name'] ); ?>">
							<!-- Widgets Prefix -->
							<label for="premium-wht-lbl-prefix" class="pa-input-label"><?php echo esc_html_e( 'Widgets Prefix', 'premium-addons-for-elementor' ); ?></label>
							<input name="premium-wht-lbl-prefix" id="premium-wht-lbl-prefix" type="text" placeholder="Premium" value="<?php echo esc_attr( $settings['premium-wht-lbl-prefix'] ); ?>">
							<!-- Widgets Badge -->
							<label for="premium-wht-lbl-badge" class="pa-input-label"><?php echo esc_html_e( 'Widgets Badge', 'premium-addons-for-elementor' ); ?></label>
							<input name="premium-wht-lbl-badge" id="premium-wht-lbl-badge" type="text" placeholder="PA" value="<?php echo esc_attr( $settings['premium-wht-lbl-badge'] ); ?>">
						</div>
					</div>
				</div>

				<div class="pa-wht-lbl-admin">
					<div class="pa-wht-lbl-settings-wrap">
						<h3 class="pa-wht-lbl-title pa-wht-lbl-head"><?php echo esc_html_e( 'Admin Settings', 'premium-addons-for-elementor' ); ?></h3>
						<div class="pa-wht-lbl-group-wrap">
							<!-- Hide General Tab-->
							<p class="pa-input-label"><?php echo esc_html_e( 'General Tab', 'premium-addons-for-elementor' ); ?></p>
							<input name="premium-wht-lbl-about" id="premium-wht-lbl-about" type="checkbox" <?php checked( 1, $settings['premium-wht-lbl-about'], true ); ?>>
							<label for="premium-wht-lbl-about"></label>
							<span><?php echo esc_html_e( 'This will hide the General tab', 'premium-addons-for-elementor' ); ?></span>

							<!-- Hide Version Control Tab-->
							<p class="pa-input-label"><?php echo esc_html_e( 'Version Control Tab', 'premium-addons-for-elementor' ); ?></p>
							<input name="premium-wht-lbl-version" id="premium-wht-lbl-version" type="checkbox" <?php checked( 1, $settings['premium-wht-lbl-version'], true ); ?>>
							<label for="premium-wht-lbl-version"></label>
							<span><?php echo esc_html_e( 'This will hide the Version Control tab.', 'premium-addons-for-elementor' ); ?></span>

							<!-- Hide Logo-->
							<p class="pa-input-label"><?php echo esc_html_e( 'Hide Premium Addons Logo', 'premium-addons-for-elementor' ); ?></p>
							<input name="premium-wht-lbl-logo" id="premium-wht-lbl-logo" type="checkbox" <?php checked( 1, $settings['premium-wht-lbl-logo'], true ); ?>>
							<label for="premium-wht-lbl-logo"></label>
							<span><?php echo esc_html_e( 'This will hide Premium Addons logo located on the dashboard.', 'premium-addons-for-elementor' ); ?></span>

							<!-- Hide License Tab-->
							<p class="pa-input-label"><?php echo esc_html_e( 'License Tab', 'premium-addons-for-elementor' ); ?></p>
							<input name="premium-wht-lbl-license" id="premium-wht-lbl-license" type="checkbox" <?php checked( 1, $settings['premium-wht-lbl-license'], true ); ?>>
							<label for="premium-wht-lbl-license"></label>
							<span><?php echo esc_html_e( 'This will hide the License tab.', 'premium-addons-for-elementor' ); ?></span>

							<!-- Hide Future Notifications-->
							<p class="pa-input-label"><?php echo esc_html_e( 'Future Notifications', 'premium-addons-for-elementor' ); ?></p>
							<input name="premium-wht-lbl-not" id="premium-wht-lbl-not" type="checkbox" <?php checked( 1, $hide_notifications, true ); ?>>
							<label for="premium-wht-lbl-not"></label>
							<span><?php echo esc_html_e( 'This will hide the future notifications shown on dashboard when a new feature is released.', 'premium-addons-for-elementor' ); ?></span>

							<!-- Hide White Labeling Tab-->

							<p class="pa-input-label"><?php echo esc_html_e( 'White Labeling Tab', 'premium-addons-for-elementor' ); ?></p>
							<input name="premium-wht-lbl-option" id="premium-wht-lbl-option" type="checkbox" <?php checked( 1, $settings['premium-wht-lbl-option'], true ); ?>>
							<label for="premium-wht-lbl-option"></label>
							<span><?php echo esc_html_e( 'This will hide the White Labeling tab options.', 'premium-addons-for-elementor' ); ?></span>

							<p>
								<strong><?php esc_html_e( 'NOTE: ', 'premium-addons-for-elementor' ); ?></strong>
								<?php echo esc_html_e( 'You will need to reactivate Premium Addons PRO for Elementor plugin to be able to reset White Labeling tab options.', 'premium-addons-for-elementor' ); ?>
							</p>
						</div>
					</div>
				</div>
				<div class="clearfix"></div>
				</div>
			</form>
		</div>
	</div>
</div> <!-- End Section Content -->
