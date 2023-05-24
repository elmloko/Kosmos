jQuery(document).ready(function( $ ) {

	$(document).on('change','[data-pafe-switch-content-button-checkbox]', function() {
		var $contentPrimary = $(this).closest('[data-pafe-switch-content]').find('[data-pafe-switch-content-primary]');
		var $contentSecondary = $(this).closest('[data-pafe-switch-content]').find('[data-pafe-switch-content-secondary]');
		
	    if ($(this).is(':checked')) {
	        $contentPrimary.hide(0);
	        $contentSecondary.show(0);
	    } else { 
	        $contentPrimary.show(0);
	        $contentSecondary.hide(0);
		}
	});
});  
 
(function ($) {

	var WidgetPafeSwitchContentHandler = function ($scope, $) {

		$switch = $scope.find('[data-pafe-switch-content-button-checkbox]');

		$switch.on('change', function() {
			var $contentPrimary = $(this).closest('[data-pafe-switch-content]').find('[data-pafe-switch-content-primary]');
			var $contentSecondary = $(this).closest('[data-pafe-switch-content]').find('[data-pafe-switch-content-secondary]');
			
		    if ($(this).is(':checked')) {
		        $contentPrimary.hide(0);
		        $contentSecondary.show(0);
		    } else { 
		        $contentPrimary.show(0);
		        $contentSecondary.hide(0);
			}
		});

    };

	$(window).on('elementor/frontend/init', function () {

        elementorFrontend.hooks.addAction('frontend/element_ready/pafe-switch-content.default', WidgetPafeSwitchContentHandler);

    });

})(jQuery);        