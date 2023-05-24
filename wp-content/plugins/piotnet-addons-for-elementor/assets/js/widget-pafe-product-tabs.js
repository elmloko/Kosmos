(function ($) {
    var WidgetPafeProductTabsHandler = function ($scope, $) {
        $items = $scope.find('[data-pafe-product-tabs-filter-item]');
        $items.click(function() { 
            var $wrapper = $(this).closest('[data-pafe-product-tabs]'),
                $result = $wrapper.find('[data-pafe-product-tabs-result]');          
            var options = JSON.parse($wrapper.attr('data-pafe-product-tabs-option')),
                terms_id = $(this).attr('data-pafe-product-tabs-id');        
                posts_per_page = options.post_per_pages,
                console.log(terms_id);
            $result.css('opacity', '0.5');             
            var data = {   
                'action': 'pafe_product_tabs', 
                'posts_per_page': posts_per_page, 
                'term_id': terms_id, 
            };
            $(this).addClass('actives');
            $('[pafe-product-tabs__filter-item]').not(this).removeClass('actives');
            $.post($('[data-pafe-ajax-url]').data('pafe-ajax-url'),data, function(response) {
                $result.html(response);
                $result.css('opacity', '1');     
            });   
        }); 
    };
    $(window).on('elementor/frontend/init', function () {
        elementorFrontend.hooks.addAction('frontend/element_ready/pafe-product-tabs.default', WidgetPafeProductTabsHandler);
    });
})(jQuery);      