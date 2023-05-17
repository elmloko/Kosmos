(function ($) {

    var PremiumMaskHandler = function ($scope, $) {
        var mask = $scope.hasClass('premium-mask-yes');

        if (!mask) return;

        if ('premium-addon-title.default' === $scope.data('widget_type')) {
            var target = '.premium-title-header';
            $scope.find(target).find('.premium-title-icon, .premium-title-img').addClass('premium-mask-span');
        } else {
            var target = '.premium-dual-header-first-header';
        }

        $scope.find(target).find('span:not(.premium-title-style7-stripe-wrap):not(.premium-title-img)').each(function (index, span) {
            var html = '';

            $(this).text().split(' ').forEach(function (item) {
                if ('' !== item) {
                    html += ' <span class="premium-mask-span">' + item + '</span>';
                }
            });

            $(this).text('').append(html);
        });

        elementorFrontend.waypoint($scope, function () {
            $($scope).addClass('premium-mask-active');
        });
    };

    $(window).on('elementor/frontend/init', function () {
        elementorFrontend.hooks.addAction('frontend/element_ready/premium-addon-dual-header.default', PremiumMaskHandler);
    });
})(jQuery);

