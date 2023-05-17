<?php
/**
 * Template Library Header Tabs
 */


?>

<# _.each( tabs, function( args, tab ) { var activeClass = args.active ? 'elementor-active' : ''; #>
	<div class="elementor-component-tab elementor-template-library-menu-item {{activeClass}}" data-tab="{{{ args.slug }}}">{{{ args.title }}}</div>
<# } ); #>
