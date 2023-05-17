<?php defined( 'ABSPATH' ) || exit; ?>
<div class="premium-live-editor-iframe-modal">
	<div class="dialog-widget dialog-lightbox-widget dialog-type-buttons dialog-type-lightbox elementor-templates-modal premium-dynamic-content-modal" id="elementor-template-pa-live-editor-modal-container" style="display:none">
		<div class="dialog-widget-content dialog-lightbox-widget-content">
			<div class="dialog-header dialog-lightbox-header">
				<div class="elementor-templates-modal__header">
					<div class="elementor-templates-modal__header__logo-area">
						<div class="elementor-templates-modal__header__logo">
							<span class="elementor-templates-modal__header__logo__icon-wrapper" id="pa-live-editor-logo">
								<span class="premium-template-modal-header-logo-icon">
									<img src="<?php echo esc_url( PREMIUM_ADDONS_URL . 'admin/images/pa-logo-symbol.png' ); ?>">
								</span>
							</span>
							<span class="elementor-templates-modal__header__logo__title"><?php esc_html_e( 'Template Editor', 'premium-addons-for-elementor' ); ?></span>
							<div class="premium-live-editor-title">
								<input type="text" id="premium-live-temp-title" name="premiumLiveTempTitle" placeholder="Enter template name...">
								<button id="pa-insert-live-temp" class="elementor-template-library-template-action premium-template-insert elementor-button elementor-button-success" ><?php esc_html_e( 'Save & Insert Template', 'premium-addons-for-elementor' ); ?></button>
								<span class="premium-live-temp-notice" ><?php esc_html_e( '(Make sure to click update button first)', 'premium-addons-for-elementor' ); ?></span>
							</div>
						</div>
					</div>
					<div class="elementor-templates-modal__header__items-area">

						<div class="elementor-templates-modal__header__close elementor-templates-modal__header__close--normal elementor-templates-modal__header__item">
							<i class="eicon-close" aria-hidden="true" title="<?php echo esc_attr__( 'Close', 'premium-addons-for-elementor' ); ?>"></i>
							<span class="elementor-screen-only"><?php esc_html_e( 'Close', 'premium-addons-for-elementor' ); ?></span>
						</div>
						<div class="elementor-templates-modal__header__expand  elementor-templates-modal__header__item premium-expand">
							<i class="eicon-frame-expand" aria-hidden="true" title="<?php echo esc_attr__( 'Expand', 'premium-addons-for-elementor' ); ?>"></i>
							<span class="elementor-screen-only"><?php esc_html_e( 'Expand', 'premium-addons-for-elementor' ); ?></span>
						</div>
					</div>
				</div>
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
