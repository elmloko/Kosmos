<?php
/**
 * Template Insert Button
 */

?>
<# if ( 'valid' === window.PremiumTempsData.license.status || ! pro ) { #>

	<button class="elementor-template-library-template-action premium-template-insert-no-media elementor-button elementor-button-warning">
		<i class="eicon-file-download"></i><span class="elementor-button-title">
		<?php
			echo wp_kses_post( __( 'INSERT w/o Images', 'premium-addons-for-elementor' ) );
		?>
		</span>
	</button>

	<button class="elementor-template-library-template-action premium-template-insert elementor-button elementor-button-success">
		<i class="eicon-file-download"></i><span class="elementor-button-title">
		<?php
			echo wp_kses_post( __( 'Insert Template', 'premium-addons-for-elementor' ) );
		?>
		</span>
	</button>
<# } else { #>
<a class="template-library-activate-license elementor-button elementor-button-go-pro" href="{{{ window.PremiumTempsData.license.activateLink }}}" target="_blank">
	<i class="fa fa-external-link" aria-hidden="true"></i>
	{{{ window.PremiumTempsData.license.proMessage }}}
</a>
<# } #>
