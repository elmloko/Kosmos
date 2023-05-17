<?php
	/**
	 * PA Menu Settings Popup.
	 *
	 * @package Templates
	 */

	defined( 'ABSPATH' ) || exit;
?>

<div class="premium-menu-settings-modal">
	<div class="dialog-widget dialog-lightbox-widget dialog-type-buttons dialog-type-lightbox elementor-templates-modal premium-dynamic-content-modal" id="elementor-template-nav-menu-modal-container" style="display:none">
		<div class="dialog-widget-content dialog-lightbox-widget-content">

			<div class="dialog-header dialog-lightbox-header">
				<div class="elementor-templates-modal__header">
					<div class="elementor-templates-modal__header__logo-area">
						<div class="elementor-templates-modal__header__logo">
							<span class="elementor-templates-modal__header__logo__icon-wrapper" id="pa-menu-logo">
								<span class="premium-template-modal-header-logo-icon">
									<img src="<?php echo esc_url( PREMIUM_ADDONS_URL . 'admin/images/pa-logo-symbol.png' ); ?>">
								</span>
							</span>
							<span class="elementor-templates-modal__header__logo__title"><?php esc_html_e( 'Premium Mega Menu Settings', 'premium-addons-for-elementor' ); ?></span>
						</div>
					</div>
					<div class="elementor-templates-modal__header__items-area">
						<div class="elementor-templates-modal__header__close elementor-templates-modal__header__close--normal elementor-templates-modal__header__item">
							<i class="eicon-close" aria-hidden="true" title="<?php echo esc_attr__( 'Close', 'premium-addons-for-elementor' ); ?>"></i>
							<span class="elementor-screen-only"><?php esc_html_e( 'Close', 'premium-addons-for-elementor' ); ?></span>
						</div>
					</div>
				</div>
			</div>

			<div class="dialog-message dialog-lightbox-message">
				<div class="dialog-content dialog-lightbox-content" style="display: block;">
					<div id="elementor-template-library-templates" data-template-source="remote">
						<div id="elementor-template-library-templates-container">
							<!-- mega content settings -->
							<div class="premium-megamenu-settings premium-setting-container">
								<div class="premium-menu-control-meta">
									<label for="pa-megamenu-switcher"><?php esc_html_e( 'Enable Mega Menu', 'premium-addons-for-elementor' ); ?></label>
								</div>
								<div class="premium-menu-control" id="pa-megamenu-switcher">
									<label class="switch">
										<input type="checkbox">
										<span class="slider round pa-control"></span>
									</label>
								</div>
							</div>

							<div class="premium-megamenu-settings premium-setting-container">
								<div class="premium-menu-control-meta">
									<label for="pa-megamenu-content"><?php esc_html_e( 'Create/Edit Mega Menu Content', 'premium-addons-for-elementor' ); ?></label>
								</div>
								<div class="premium-menu-control" id="pa-megamenu-content">
									<span class="premium-menu-btn"><?php esc_html_e( 'Edit Mega Content', 'premium-addons-for-elementor' ); ?></span>
								</div>
							</div>

							<div class="premium-megamenu-settings premium-setting-container">
								<div class="premium-menu-control-meta premium-has-desc">
									<label for="pa-megamenu-content-pos"><?php esc_html_e( 'Menu Content Position', 'premium-addons-for-elementor' ); ?></label>
									<div class="premium-megamenu-des"><?php esc_html_e( 'mega content parent position', 'premium-addons-for-elementor' ); ?></div>
								</div>
								<div class="premium-menu-control" id="pa-megamenu-content-pos">
									<select id="pa-megamenu-position">
										<option value="default">Default</option>
										<option value="relative">Relative</option>
									</select>
								</div>
							</div>

							<div class="premium-megamenu-settings premium-setting-container pa-depth-0-control premium-setting-hidden">
								<div class="premium-menu-control-meta premium-has-desc">
									<label for="pa-megamenu-content-pos"><?php esc_html_e( 'Full Width Content', 'premium-addons-for-elementor' ); ?></label>
									<div class="premium-megamenu-des"><?php esc_html_e( 'Works only on horizontal-layout menus', 'premium-addons-for-elementor' ); ?></div>
								</div>
								<div class="premium-menu-control" id="pa-full-width-switcher">
									<label class="switch">
										<input type="checkbox">
										<span class="slider round pa-control"></span>
									</label>
								</div>
							</div>

							<div class="premium-megamenu-settings premium-setting-container">
								<div class="premium-menu-control-meta premium-has-desc">
									<label for="pa-megamenu-content-width"><?php esc_html_e( 'Menu Content Width (PX)', 'premium-addons-for-elementor' ); ?></label>
									<div class="premium-megamenu-des"><?php esc_html_e( 'Default is 1170 px', 'premium-addons-for-elementor' ); ?></div>
								</div>
								<div class="premium-menu-control" id="pa-megamenu-content-width">
									<input type="number" id="pa-mega-content-width" min="1" max="2000">
								</div>
							</div>
							<!-- icon settings -->
							<div class="premium-icon-select premium-setting-container">
								<div class="premium-menu-control-meta">
									<label for="pa-item-icon-type"><?php esc_html_e( 'Icon Type', 'premium-addons-for-elementor' ); ?></label>
								</div>
								<div class="premium-menu-control" id="pa-item-icon-type">
									<select id="pa-megamenu-icon-type">
										<option value="icon">Icon</option>
										<option value="lottie">Lottie Animation</option>
									</select>
								</div>
							</div>

							<div class="premium-lottie-settings premium-setting-container">
								<div class="premium-menu-control-meta premium-has-desc">
									<label for="pa-item-lottie"><?php esc_html_e( 'Lottie URL', 'premium-addons-for-elementor' ); ?></label>
									<div class="premium-megamenu-des"><?php echo wp_kses_post( 'Get JSON code URL from <a href="https://lottiefiles.com/" target="_blank">here</a>', 'premium-addons-for-elementor' ); ?></div>
								</div>
								<div class="premium-menu-control" id="pa-item-lottie">
									<input type="text" id="premium-lottie-url" class="premium-icon-picker">
								</div>
							</div>

							<div class="premium-icon-settings premium-setting-container">
								<div class="premium-menu-control-meta">
									<label for="pa-item-icon-picker"><?php esc_html_e( 'Select Icon', 'premium-addons-for-elementor' ); ?></label>
								</div>
								<div class="premium-menu-control" id="pa-item-icon-picker">
									<input type="text" id="premium-icon-field" class="premium-icon-picker" >
								</div>
							</div>

							<div class="premium-icon-settings premium-setting-container">
								<div class="premium-menu-control-meta">
									<label for="pa-item-icon-color"><?php esc_html_e( 'Icon Color', 'premium-addons-for-elementor' ); ?></label>
								</div>
								<div class="premium-menu-control" id="pa-item-icon-color">
									<input type="text" id="premium-icon-color-field" class="premium-color-picker" value="#bada55">
								</div>
							</div>

							<div class="premium-badge-settings premium-setting-container">
								<div class="premium-menu-control-meta">
									<label for="premium-badge-text-field"><?php esc_html_e( 'Item Badge Text', 'premium-addons-for-elementor' ); ?></label>
								</div>
								<div class="premium-menu-control" id="pa-item-badge">
									<input type="text" id="premium-badge-text-field" class="premium-text-picker" placeholder="Badge Text">
								</div>
							</div>

							<div class="premium-badge-settings premium-setting-container">
								<div class="premium-menu-control-meta">
									<label for="premium-badge-color-field"><?php esc_html_e( 'Badge Color', 'premium-addons-for-elementor' ); ?></label>
								</div>
								<div class="premium-menu-control" id="pa-item-badge-color">
									<input type="text" id="premium-badge-color-field" class="premium-color-picker" value="#bada55">
								</div>
							</div>

							<div class="premium-badge-settings premium-setting-container">
								<div class="premium-menu-control-meta">
									<label for="premium-badge-bg-field"><?php esc_html_e( 'Badge Background', 'premium-addons-for-elementor' ); ?></label>
								</div>
								<div class="premium-menu-control" id="pa-item-badge-color">
									<input type="text" id="premium-badge-bg-field" class="premium-color-picker" value="#bada55">
								</div>
							</div>

						</div>
					</div>
				</div>
			</div>

			<div class="premium-menu-dialog-footer">
				<div class="premium-menu-save-btn">
					<button id="premium-menu-save" class="premium-menu-btn" type="button">
						<span>
							<?php esc_html_e( 'Save Settings', 'premium-addons-for-elementor' ); ?>
						</span>
						<i class="dashicons dashicons-admin-generic loader-hidden"></i>
					</span>
				</div>
			</div>
		</div>
	</div>
</div>
<!-- End of nav settings popup -->
<!-- Start Live Editor popup -->
<?php defined( 'ABSPATH' ) || exit; ?>
<div class="premium-live-editor-iframe-modal">
	<div class="dialog-widget dialog-lightbox-widget dialog-type-buttons dialog-type-lightbox elementor-templates-modal premium-dynamic-content-modal" id="elementor-template-pa-live-editor-modal-container" style="display:none">
		<div class="dialog-widget-content dialog-lightbox-widget-content">
			<div class="premium-menu-temp-close">
				<i class="eicon-close"></i>
			</div>
			<div class="dialog-message dialog-lightbox-message">
				<div class="dialog-content dialog-lightbox-content" style="display: block;">
					<div id="elementor-template-library-templates" data-template-source="remote">

						<div id="elementor-template-library-templates-container">
							<iframe id="pa-live-editor-control-iframe"></iframe>
						</div>
					</div>
				</div>
				<div class="dialog-loading dialog-lightbox-loading" style="display: block;">
					<div id="elementor-template-library-loading">
						<div class="elementor-loader-wrapper">
							<div class="elementor-loader">
								<div class="elementor-loader-boxes">
									<div class="elementor-loader-box"></div>
									<div class="elementor-loader-box"></div>
									<div class="elementor-loader-box"></div>
									<div class="elementor-loader-box"></div>
								</div>
							</div>
							<div class="elementor-loading-title"><?php esc_html_e( 'Loading', 'premium-addons-for-elementor' ); ?></div>
						</div>
					</div>
				</div>
			</div>
			<div class="dialog-buttons-wrapper dialog-lightbox-buttons-wrapper"></div>
		</div>
	</div>
</div>
