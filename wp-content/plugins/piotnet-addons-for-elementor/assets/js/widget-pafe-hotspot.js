jQuery(document).ready(function( $ ) {

		$(document).on('click','.pafe-hotspot__marker-icon',function() {
			if ($(this).attr("data-pafe-hotspot-trigger") == 'click'){
				$(this).children('.pafe-hotspot__tooltip').toggleClass("active");
			};
		});

		// $(document).on('hover','.pafe-hotspot__marker-icon',function() {
		// 	if ($(this).attr("data-pafe-hotspot-trigger") == 'hover'){
		// 		$(this).children('.pafe-hotspot__tooltip').toggleClass("active");
		// 	};
		// });
		$('.pafe-hotspot__marker-icon' ).hover(function() {
			if ($(this).attr("data-pafe-hotspot-trigger") == 'hover'){
				$(this).children('.pafe-hotspot__tooltip').toggleClass("active");
			};
		});
});