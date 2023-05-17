<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use PremiumAddons\Includes\Helper_Functions;

$docs_url = Helper_Functions::get_campaign_link( 'https://premiumaddons.com/docs/', 'about-page', 'wp-dash', 'dashboard' );

$pa_news = self::get_pa_news();

?>

<div class="pa-section-content">
	<div class="row">
		<div id="pa-general-settings" class="pa-settings-tab">
			<div class="pa-dash-block col-3">
				<div class="pa-section-info-wrap">

					<div class="pa-section-info">
						<i class="pa-element-icon dashicons dashicons-facebook"></i>
						<h4>
							<?php esc_html_e( 'Join Our Facebook Group', 'premium-addons-for-elementor' ); ?>
						</h4>
						<p><?php esc_html_e( 'Join our Facebook group and be a part of the community, you are more than welcome.', 'premium-addons-for-elementor' ); ?></p>
						<a class="pa-btn" href="https://facebook.com/groups/PremiumAddons" target="_blank"><?php esc_html_e( 'Join Group', 'premium-addons-for-elementor' ); ?></a>
					</div>
				</div>
			</div>

			<div class="pa-dash-block col-3">
				<div class="pa-section-info-wrap">
					<div class="pa-section-info">
						<i class="pa-element-icon dashicons dashicons-youtube"></i>
						<h4>
							<?php esc_html_e( 'Subscribe to Our YouTube Channel', 'premium-addons-for-elementor' ); ?>
						</h4>
						<p><?php esc_html_e( 'Subscribe to our YouTube channel. We are committed to adding video tutorials regularly.', 'premium-addons-for-elementor' ); ?></p>
						<a class="pa-btn" href="https://www.youtube.com/channel/UCXcJ9BeO2sKKHor7Q9VglTQ?sub_confirmation=1" target="_blank"><?php esc_html_e( 'Subscribe', 'premium-addons-for-elementor' ); ?></a>
					</div>
				</div>
			</div>

			<div class="pa-dash-block col-3">
				<div class="pa-section-info-wrap">

					<div class="pa-section-info">
						<i class="pa-element-icon dashicons dashicons-email"></i>
						<h4>
							<?php esc_html_e( 'Subscribe to Our Newsletter', 'premium-addons-for-elementor' ); ?>
						</h4>
						<p><?php esc_html_e( 'Enter your email address and be the first to know the latest features, offers, and updates.', 'premium-addons-for-elementor' ); ?></p>
						<form class="pa-newsletter-form">
							<input id="pa_news_email" type="email" placeholder="<?php esc_attr_e( 'Email', 'premium-addons-for-elementor' ); ?>">
							<button type="submit" class="pa-btn"><?php esc_html_e( 'Submit', 'premium-addons-for-elementor' ); ?></button>
						</form>

					</div>
				</div>
			</div>

			<?php if ( is_array( $pa_news ) ) : ?>
				<div class="pa-dash-block col-6">
					<div class="pa-section-info-wrap">

						<div class="pa-section-info pa-news-section">
							<h4>
								<i class="pa-element-icon dashicons dashicons-admin-post icon-inline"></i>
								<?php esc_html_e( 'Latest News', 'premium-addons-for-elementor' ); ?>
							</h4>

							<div class="pa-news-grid">
								<?php foreach ( $pa_news as $index => $post ) : ?>
									<div class="pa-news-post">
										<div class="pa-post-img-container">
											<img src="<?php echo esc_url( $post['featured_img_url'] ); ?>">
										</div>
										<p><?php echo wp_kses_post( $post['title']['rendered'] ); ?></p>
										<p><?php echo wp_kses_post( date( 'j F, Y', strtotime( $post['date'] ) ) ); ?></p>
										<a href="<?php echo esc_url( Helper_Functions::get_campaign_link( $post['link'], 'about-page', 'wp-dash', 'dashboard' ) ); ?>" target="_blank"></a>
									</div>
								<?php endforeach; ?>
							</div>

						</div>
					</div>
				</div>
			<?php endif; ?>

			<div class="pa-dash-block col-3">
				<div class="pa-section-info-wrap">
				<div class="pa-section-info pa-support-section">
						<h4>
							<i class="pa-element-icon dashicons dashicons-sos icon-inline"></i>
							<?php esc_html_e( 'Docs and Support', 'premium-addons-for-elementor' ); ?>
						</h4>
						<p><?php echo esc_html( __( 'It’s highly recommended to check our documentation and FAQs before using this plugin. ', 'premium-addons-for-elementor' ) ); ?></p>
						<ul class="pa-support-list">
							<li><a href="<?php echo esc_url( $docs_url ); ?>" target="_blank"><?php esc_html_e( '> Documentation.', 'premium-addons-for-elementor' ); ?></a></li>
							<li><a href="https://my.leap13.com/contact-support" target="_blank"><?php esc_html_e( '> Support Tickets.', 'premium-addons-for-elementor' ); ?></a></li>
							<li><a href="https://my.leap13.com/forums/forum/premium-addons-for-elementor-plugin-community-support/" target="_blank"><?php esc_html_e( '> Community Forums.', 'premium-addons-for-elementor' ); ?></a></li>
						</ul>
					</div>
				</div>
			</div>

		</div>
	</div>
</div> <!-- End Section Content -->
