<?php
/**
 * Templates Keywords Filter
 */

?>
<#
	if ( ! _.isEmpty( keywords ) ) {
#>
<div>
	<label><?php echo wp_kses_post( __( 'Filter by Widget / Addon', 'premium-addons-for-elementor' ) ); ?></label>
	<select id="elementor-template-library-filter-subtype" class="elementor-template-library-filter-select premium-library-keywords" data-elementor-filter="subtype">
		<option value=""><?php echo wp_kses_post( __( 'All Widgets/Addons', 'premium-addons-for-elementor' ) ); ?></option>
		<# _.each( keywords, function( title, slug ) { #>
		<option value="{{ slug }}">{{ title }}</option>
		<# } ); #>
	</select>
</div>

<div class="premium-templates-notice">
	<p><?php echo wp_kses_post( __( 'Having an issue inserting <b>Magic Scroll addon</b> templates? Check this ', 'premium-addons-for-elementor' ) ); ?><a href='<?php echo esc_attr( 'https://premiumaddons.com/docs/settings-not-saved-in-elementor-magic-scroll-addon/' ); ?>' target='_blank'><?php echo wp_kses_post( __( '<b>article</b>', 'premium-addons-for-elementor' ) ); ?></a></p>
</div>
<#
	}
#>
