(function ($) {

    var PremiumBulletListHandler = function ($scope, $) {

        var $listItems = $scope.find(".premium-bullet-list-box"),
            items = $listItems.find(".premium-bullet-list-content");

        items.each(function (index, item) {

            if ($listItems.data("list-animation") && " " != $listItems.data("list-animation")) {
                elementorFrontend.waypoint($(item), function () {

                    var element = $(item),
                        delay = element.data('delay');

                    setTimeout(function () {
                        element.next('.premium-bullet-list-divider , .premium-bullet-list-divider-inline').css("opacity", "1");
                        element.next('.premium-bullet-list-divider-inline , .premium-bullet-list-divider').addClass("animated " + $listItems.data("list-animation"));

                        element.css("opacity", "1").addClass("animated " + $listItems.data("list-animation"));
                    }, delay);

                });
            }

        });
    };

    $(window).on('elementor/frontend/init', function () {
        elementorFrontend.hooks.addAction('frontend/element_ready/premium-icon-list.default', PremiumBulletListHandler);
    });
 })(jQuery);

