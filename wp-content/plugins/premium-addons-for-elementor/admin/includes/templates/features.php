<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use PremiumAddons\Includes\Helper_Functions;

$prefix = Helper_Functions::get_prefix();

// Get elements settings
$enabled_elements = self::get_enabled_elements();

$elements = self::get_elements_list();

$features = $elements['cat-11']['elements'];

?>

<div class="pa-section-content">
	<div class="row">
		<div class="col-full">
			<form action="" method="POST" id="pa-features" name="pa-features" class="pa-settings-form">
			<div id="pa-features-settings" class="pa-settings-tab">

				<div class="pa-section-outer-wrap">
					<div class="pa-section-info-wrap">
						<div class="pa-section-info">
						<h4><?php echo esc_html( __( 'Magic Scroll', 'premium-addons-for-elementor' ) ); ?></h4>
							<p><?php echo esc_html( __( 'Create sophisticated animations with dozens of customization options.', 'premium-addons-for-elementor' ) ); ?></p>
						</div>

						<?php

							$status         = ( isset( $features[10]['is_pro'] ) && ! Helper_Functions::check_papro_version() ) ? 'disabled' : checked( 1, $enabled_elements['premium-mscroll'], false );
							$class          = ( isset( $features[10]['is_pro'] ) && ! Helper_Functions::check_papro_version() ) ? 'pro-' : '';
							$switcher_class = $class . 'slider round pa-control';

						?>

						<div class="pa-section-info-cta">
							<label class="switch">
								<input type="checkbox" id="premium-mscroll" name="premium-mscroll" <?php echo esc_attr( $status ); ?>>
									<span class="<?php echo esc_attr( $switcher_class ); ?>"></span>
								</label>
							</p>
						</div>
					</div>
					<a href="<?php echo esc_url( $features[10]['demo'] ); ?>" target="_blank"></a>
				</div>

				<div class="pa-section-outer-wrap">
					<div class="pa-section-info-wrap">
						<div class="pa-section-info">
							<h4><?php echo sprintf( '%1$s %2$s', esc_html( $prefix ), esc_html( __( 'Templates', 'premium-addons-for-elementor' ) ) ); ?></h4>
							<p><?php echo esc_html( __( 'Build Professional Website in Minutes Using Our Pre-Made Premium Elementor Templates.', 'premium-addons-for-elementor' ) ); ?></p>
						</div>

						<div class="pa-section-info-cta">
							<label class="switch">
								<input type="checkbox" id="premium-templates" name="premium-templates" <?php echo checked( 1, $enabled_elements['premium-templates'], false ); ?>>
									<span class="slider round pa-control"></span>
								</label>
							</p>
						</div>
					</div>
					<a href="<?php echo esc_url( $features[0]['demo'] ); ?>" target="_blank"></a>
				</div>

				<div class="pa-section-outer-wrap">
					<div class="pa-section-info-wrap">
						<div class="pa-section-info">
						<h4><?php echo esc_html( __( 'Equal Height', 'premium-addons-for-elementor' ) ); ?></h4>
							<p><?php echo esc_html( __( 'Make your widgets the same height with just ONE click.', 'premium-addons-for-elementor' ) ); ?></p>
						</div>

						<div class="pa-section-info-cta">
							<label class="switch">
								<input type="checkbox" id="premium-equal-height" name="premium-equal-height" <?php echo checked( 1, $enabled_elements['premium-equal-height'], false ); ?>>
									<span class="slider round pa-control"></span>
								</label>
							</p>
						</div>
					</div>
					<a href="<?php echo esc_url( $features[1]['demo'] ); ?>" target="_blank"></a>
				</div>

				<div class="pa-section-outer-wrap">
					<div class="pa-section-info-wrap">
						<div class="pa-section-info">
						<h4><?php echo esc_html( __( 'Custom Mouse Cursor', 'premium-addons-for-elementor' ) ); ?></h4>
							<p><?php echo esc_html( __( 'Change the default mouse cursor with icon, image, or Lottie animation for any Elementor section, container, column, or widget.', 'premium-addons-for-elementor' ) ); ?></p>
						</div>
						<?php

						$status         = ( isset( $features[3]['is_pro'] ) && ! Helper_Functions::check_papro_version() ) ? 'disabled' : checked( 1, $enabled_elements['premium-global-cursor'], false );
						$class          = ( isset( $features[3]['is_pro'] ) && ! Helper_Functions::check_papro_version() ) ? 'pro-' : '';
						$switcher_class = $class . 'slider round pa-control';

						?>
						<div class="pa-section-info-cta">
							<label class="switch">
								<input type="checkbox" id="premium-global-cursor" name="premium-global-cursor" <?php echo esc_attr( $status ); ?>>
									<span class="<?php echo esc_attr( $switcher_class ); ?>"></span>
								</label>
							</p>
						</div>
					</div>
					<a href="<?php echo esc_url( $features[3]['demo'] ); ?>" target="_blank"></a>
				</div>

				<div class="pa-section-outer-wrap">
					<div class="pa-section-info-wrap">
						<div class="pa-section-info">
							<h4><?php echo esc_html( __( 'Global Badge', 'premium-addons-for-elementor' ) ); ?></h4>
							<p><?php echo esc_html( __( 'Add icon, image, Lottie, or SVG blob shape badge to any Elementor section, container, column, or widget.', 'premium-addons-for-elementor' ) ); ?></p>
						</div>
						<?php

						$status         = ( isset( $features[4]['is_pro'] ) && ! Helper_Functions::check_papro_version() ) ? 'disabled' : checked( 1, $enabled_elements['premium-global-badge'], false );
						$class          = ( isset( $features[4]['is_pro'] ) && ! Helper_Functions::check_papro_version() ) ? 'pro-' : '';
						$switcher_class = $class . 'slider round pa-control';

						?>
						<div class="pa-section-info-cta">
							<label class="switch">
								<input type="checkbox" id="premium-global-badge" name="premium-global-badge" <?php echo esc_attr( $status ); ?>>
									<span class="<?php echo esc_attr( $switcher_class ); ?>"></span>
								</label>
							</p>
						</div>
					</div>
					<a href="<?php echo esc_url( $features[4]['demo'] ); ?>" target="_blank"></a>
				</div>

				<div class="pa-section-outer-wrap">
					<div class="pa-section-info-wrap">
						<div class="pa-section-info">
							<h4><?php echo esc_html( __( 'Animated Shape Divider', 'premium-addons-for-elementor' ) ); ?></h4>
							<p><?php echo esc_html( __( 'Add icon, image, Lottie, or SVG blob shape badge to any Elementor section, container, column, or widget.', 'premium-addons-for-elementor' ) ); ?></p>
						</div>
												<div class="pa-section-info-cta">
							<label class="switch">
								<input type="checkbox" id="premium-shape-divider" name="premium-shape-divider" <?php echo checked( 1, $enabled_elements['premium-shape-divider'], false ); ?>>
									<span class="slider round pa-control"></span>
								</label>
							</p>
						</div>
					</div>
					<a href="<?php echo esc_url( $features[5]['demo'] ); ?>" target="_blank"></a>
				</div>

				<div class="pa-section-outer-wrap">
					<div class="pa-section-info-wrap">
						<div class="pa-section-info">
							<h4><?php echo esc_html( __( 'Global Tooltips', 'premium-addons-for-elementor' ) ); ?></h4>
							<p><?php echo esc_html( __( 'Add icon, text, Lottie or Elementor template tooltip to any Elementor section, container, column, or widget.', 'premium-addons-for-elementor' ) ); ?></p>
						</div>
												<div class="pa-section-info-cta">
							<label class="switch">
								<input type="checkbox" id="premium-global-tooltips" name="premium-global-tooltips" <?php echo checked( 1, $enabled_elements['premium-global-tooltips'], false ); ?>>
									<span class="slider round pa-control"></span>
								</label>
							</p>
						</div>
					</div>
					<a href="<?php echo esc_url( $features[11]['demo'] ); ?>" target="_blank"></a>
				</div>

				<div class="pa-section-outer-wrap">
					<div class="pa-section-info-wrap">
						<div class="pa-section-info">
						<h4><?php echo esc_html( __( 'Display Conditions', 'premium-addons-for-elementor' ) ); ?></h4>
							<p><?php echo esc_html( __( 'Show/hide content dynamically based on location, browser, operating system, user role, device type, Woocommerce, ACF, etc.', 'premium-addons-for-elementor' ) ); ?></p>
						</div>

						<div class="pa-section-info-cta">
							<label class="switch">
								<input type="checkbox" id="pa-display-conditions" name="pa-display-conditions" <?php echo checked( 1, $enabled_elements['pa-display-conditions'], false ); ?>>
									<span class="slider round pa-control"></span>
								</label>
							</p>
						</div>
					</div>
					<a href="<?php echo esc_url( $features[2]['demo'] ); ?>" target="_blank"></a>
				</div>

				<div class="pa-section-outer-wrap">
					<div class="pa-section-info-wrap">
						<div class="pa-section-info">
						<h4><?php echo esc_html( __( 'Floating Effects', 'premium-addons-for-elementor' ) ); ?></h4>
							<p><?php echo esc_html( __( 'Apply advanced floating effects on any Elementor element or a custom CSS selector.', 'premium-addons-for-elementor' ) ); ?></p>
						</div>

						<div class="pa-section-info-cta">
							<label class="switch">
								<input type="checkbox" id="premium-floating-effects" name="premium-floating-effects" <?php echo checked( 1, $enabled_elements['premium-floating-effects'], false ); ?>>
									<span class="slider round pa-control"></span>
								</label>
							</p>
						</div>
					</div>
					<a href="<?php echo esc_url( $features[6]['demo'] ); ?>" target="_blank"></a>
				</div>

				<div class="pa-section-outer-wrap">
					<div class="pa-section-info-wrap">
						<div class="pa-section-info">
						<h4><?php echo esc_html( __( 'Cross-Domain Copy N’ Paste', 'premium-addons-for-elementor' ) ); ?></h4>
							<p><?php echo esc_html( __( 'Copy any Elementor content from site to another in just ONE click.', 'premium-addons-for-elementor' ) ); ?></p>
						</div>

						<div class="pa-section-info-cta">
							<label class="switch">
								<input type="checkbox" id="premium-cross-domain" name="premium-cross-domain" <?php echo checked( 1, $enabled_elements['premium-cross-domain'], false ); ?>>
									<span class="slider round pa-control"></span>
								</label>
							</p>
						</div>
					</div>
					<a href="<?php echo esc_url( $features[7]['demo'] ); ?>" target="_blank"></a>
				</div>

				<div class="pa-section-outer-wrap">
					<div class="pa-section-info-wrap">
						<div class="pa-section-info">
							<h4><?php echo esc_html( __( 'Duplicator', 'premium-addons-for-elementor' ) ); ?></h4>
							<p><?php echo esc_html( __( 'Duplicate any post, page or template on your website.', 'premium-addons-for-elementor' ) ); ?></p>
						</div>

						<div class="pa-section-info-cta">
							<label class="switch">
								<input type="checkbox" id="premium-duplicator" name="premium-duplicator" <?php echo checked( 1, $enabled_elements['premium-duplicator'], false ); ?>>
									<span class="slider round pa-control"></span>
								</label>
							</p>
						</div>
					</div>
				</div>

				<div class="pa-section-outer-wrap">
					<div class="pa-section-info-wrap">
						<div class="pa-section-info">
						<h4><?php echo esc_html( __( 'Wrapper Link', 'premium-addons-for-elementor' ) ); ?></h4>
							<p><?php echo esc_html( __( 'Add links to Elementor flexbox container, section, column or widgets.', 'premium-addons-for-elementor' ) ); ?></p>
						</div>

						<div class="pa-section-info-cta">
							<label class="switch">
								<input type="checkbox" id="premium-wrapper-link" name="premium-wrapper-link" <?php echo checked( 1, $enabled_elements['premium-wrapper-link'], false ); ?>>
									<span class="slider round pa-control"></span>
								</label>
							</p>
						</div>
					</div>
					<a href="<?php echo esc_url( $features[12]['demo'] ); ?>" target="_blank"></a>
				</div>

			</div>
			</form> <!-- End Form -->
		</div>
	</div>
</div> <!-- End Section Content -->
