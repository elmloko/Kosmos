jQuery(document).ready(function($) {
	$('[data-pafe-image-accordion-list]').each(function() { 
		var option = $(this).attr('data-pafe-image-accordion-option');
			$item = $(this).find('[data-pafe-image-accordion-item]');
		if ( option == "hover" ) {
			$item.hover(function() {
				$(this).closest('[data-pafe-image-accordion-list]').find('[data-pafe-image-accordion-item]').removeClass('active');
				$(this).addClass('active');		
			});	
		} else {
			$item.click(function() {
				$(this).closest('[data-pafe-image-accordion-list]').find('[data-pafe-image-accordion-item]').removeClass('active');
				$(this).addClass('active');		
			});	
		}
	}); 
});   
(function ($) {
	var WidgetPafeImageAccordionHandler = function ($scope, $) {
		$listItem = $scope.find('[data-pafe-image-accordion-list]');
		$listItem.each(function() { 
			var option = $(this).attr('data-pafe-image-accordion-option');
				$item = $(this).find('[data-pafe-image-accordion-item]');
			if ( option == "hover" ) {
				$item.hover(function() {
					$(this).closest('[data-pafe-image-accordion-list]').find('[data-pafe-image-accordion-item]').removeClass('active');
					$(this).addClass('active');		
				});	
			} else {
				$item.click(function() {
					$(this).closest('[data-pafe-image-accordion-list]').find('[data-pafe-image-accordion-item]').removeClass('active');
					$(this).addClass('active');		
				});	
			}
		});

	};
	$(window).on('elementor/frontend/init', function () {
        elementorFrontend.hooks.addAction('frontend/element_ready/pafe-image-accordion.default', WidgetPafeImageAccordionHandler);
    });
})(jQuery);	