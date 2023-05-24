(function ($) {
    var WidgetPafePostsListHandler = function ($scope, $) {
        $items = $scope.find('[data-pafe-posts-list-filter-item]');
        $items.click(function() { 
            var $wrapper = $(this).closest('[data-pafe-posts-list]'),
                $result = $wrapper.find('[data-pafe-tab-content-post]');          
            var options = JSON.parse($wrapper.attr('data-pafe-posts-list-option')),
                term_id = $(this).attr('data-id');
                post_type = options.post_type,
                taxonomy = options.taxonomy,
                posts_per_page = options.post_per_pages,
                author = options.author,
                date = options.date,
                excerpt = options.excerpt, 
                layout = options.layout_type; 
            $result.css('opacity', '0.5');             
            var data = {  
                'action': 'pafe_posts_list', 
                'post_type': post_type,  
                'taxonomy': taxonomy,  
                'posts_per_page': posts_per_page,
                'term_id': term_id, 
                'author': author,
                'date': date,
                'excerpt': excerpt,
                'layout': layout,
            };
            $(this).addClass('actives');
            $('[data-pafe-posts-list-filter-item]').not(this).removeClass('actives');
            $.post($('[data-pafe-ajax-url]').data('pafe-ajax-url'),data, function(response) {
                $result.html(response);
                $result.css('opacity', '1');     
            });   
        }); 
    };
    $(window).on('elementor/frontend/init', function () {
        elementorFrontend.hooks.addAction('frontend/element_ready/pafe-posts-list.default', WidgetPafePostsListHandler);
    });
})(jQuery);           