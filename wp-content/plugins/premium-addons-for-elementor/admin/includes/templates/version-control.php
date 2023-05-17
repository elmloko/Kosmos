<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use PremiumAddons\Includes\Helper_Functions;

// Get settings.
$settings = self::get_integrations_settings();

?>

<div class="pa-section-content">
	<div class="row">
		<div class="col-full">
			<form action="" method="POST" id="pa-ver-control" name="pa-ver-control" class="pa-settings-form">
			<div id="pa-ver-control-settings" class="pa-settings-tab">

				<div class="pa-section-info-wrap">
					<div class="pa-section-info">
						<h4><?php echo esc_html_e( 'Rollback to Previous Version', 'premium-addons-for-elementor' ); ?></h4>
						<p><?php echo esc_html_e( sprintf( __( 'Experiencing an issue with Premium Addons for Elementor version %s? Rollback to a previous version before the issue appeared.', 'premium-addons-for-elementor' ), esc_attr( PREMIUM_ADDONS_VERSION ) ) ); ?></p>
					</div>

					<div class="pa-section-info-cta">
						<a target="_blank" href="<?php echo esc_url( wp_nonce_url( admin_url( 'admin-post.php?action=premium_addons_rollback' ), 'premium_addons_rollback' ) ); ?>" class="button pa-btn pa-rollback-button">
							<?php echo esc_html_e( 'Rollback to Version ' . PREMIUM_ADDONS_STABLE_VERSION, 'premium-addons-for-elementor' ); ?>
						</a>
						<span class="pa-section-info-warning">
							<i class="dashicons dashicons-info-outline"></i>
							<?php echo esc_html_e( 'Warning: Please backup your database before making the rollback.', 'premium-addons-for-elementor' ); ?>
						</span>
						</p>
					</div>
				</div>

				<div class="pa-section-info-wrap">
					<div class="pa-section-info">
						<h4><?php echo esc_html_e( 'Become a Beta Tester', 'premium-addons-for-elementor' ); ?></h4>
						<p><?php echo esc_html_e( 'Turn-on Beta Tester, to get notified when a new beta version of Premium Addons for Elementor. The Beta version will not install automatically. You always have the option to ignore it.', 'premium-addons-for-elementor' ); ?></p>
					</div>

					<div class="pa-section-info-cta">
						<input name="is-beta-tester" id="is-beta-tester" type="checkbox" <?php checked( 1, $settings['is-beta-tester'], true ); ?>>
						<label for="is-beta-tester"></label>
						<span class="pa-section-info-label">
							<?php echo esc_html_e( 'Check this box to get updates for beta versions', 'premium-addons-for-elementor' ); ?>
						</span>
						<span class="pa-section-info-warning">
							<i class="dashicons dashicons-info-outline"></i>
							<?php echo esc_html_e( 'We do not recommend updating to a beta version on production sites.', 'premium-addons-for-elementor' ); ?>
						</span>
						</p>
					</div>
				</div>

			</div>
			</form> <!-- End Form -->
		</div>
	</div>
</div> <!-- End Section Content -->
