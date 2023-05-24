jQuery(document).ready(function($) {
	function iframeheight()  {
		$('[data-pafe-video-playlist] iframe[src*="youtube"],[data-pafe-video-playlist] iframe[src*="vimeo"]').each(function(){
			var width = $(this).width();
			var height = width * 9/16;
			$(this).css({'height' : height + 'px'});
			$(this).closest('[data-pafe-video-playlist]').find('[data-pafe-video-playlist-list]').css({'height' : height + 'px'});
			var $heightTitle = $('[data-pafe-video-playlist-list-title]').height();
			var heightList = height - $heightTitle -30;
			$(this).closest('[data-pafe-video-playlist]').find('[data-pafe-video-playlist-list-video]').css({'max-height' : heightList + 'px'});
			
		});  
	}; 
	iframeheight(); 
	$(window).resize(function(event) {
		iframeheight();  
	});

	$('[data-pafe-video-playlist-item]').click(function() { 
		var videoHtml =  $(this).attr('data-pafe-video-playlist-item'),
			$videoSelector = $(this).closest('[data-pafe-video-playlist]').find('[data-pafe-video-playlist-video]'),
			$videoItems = $(this).closest('[data-pafe-video-playlist]').find('[data-pafe-video-playlist-item]');
		$videoItems.removeClass('active');
		$(this).addClass('active');
		$videoSelector.html(videoHtml);  
		iframeheight();   
 	});
	 
});   

 (function ($) {
	var WidgetPafeVideoListHandler = function ($scope, $) {
			function iframeheight()  {
			$('[data-pafe-video-playlist] iframe[src*="youtube"],[data-pafe-video-playlist] iframe[src*="vimeo"]').each(function(){
				var width = $(this).width();
				var height = width * 9/16;
				$(this).css({'height' : height + 'px'});
				$(this).closest('[data-pafe-video-playlist]').find('[data-pafe-video-playlist-list]').css({'height' : height + 'px'});
				var $heightTitle = $('[data-pafe-video-playlist-list-title]').height();
				var heightList = height - $heightTitle -30;
				$(this).closest('[data-pafe-video-playlist]').find('[data-pafe-video-playlist-list-video]').css({'max-height' : heightList + 'px'});
				
			});  
		}; 
		iframeheight(); 
		$(window).resize(function(event) {
			iframeheight();  
		});
		$item = $scope.find('[data-pafe-video-playlist-item]');
		$item.click(function() {  
			var videoHtml =  $(this).attr('data-pafe-video-playlist-item'),
				$videoSelector = $(this).closest('[data-pafe-video-playlist]').find('[data-pafe-video-playlist-video]'),
				$videoItems = $(this).closest('[data-pafe-video-playlist]').find('[data-pafe-video-playlist-item]');
			$videoItems.removeClass('active');
			$(this).addClass('active');
			$videoSelector.html(videoHtml);  
			iframeheight();   
	 	});
    };
	$(window).on('elementor/frontend/init', function () {
        elementorFrontend.hooks.addAction('frontend/element_ready/pafe-video-playlist.default', WidgetPafeVideoListHandler);
    });
})(jQuery);             