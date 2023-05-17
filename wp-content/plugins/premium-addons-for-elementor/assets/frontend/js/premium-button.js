(function ($) {

    var PremiumButtonHandler = function ($scope, $) {

        var $btnGrow = $scope.find('.premium-button-style6-bg');

        if ($btnGrow.length !== 0 && $scope.hasClass('premium-mouse-detect-yes')) {
            $scope.on('mouseenter mouseleave', '.premium-button-style6', function (e) {

                var parentOffset = $(this).offset(),
                    left = e.pageX - parentOffset.left,
                    top = e.pageY - parentOffset.top;

                $btnGrow.css({
                    top: top,
                    left: left,
                });

            });
        }

    };

    $(window).on('elementor/frontend/init', function () {
        elementorFrontend.hooks.addAction('frontend/element_ready/premium-addon-button.default', PremiumButtonHandler);
    });
 })(jQuery);

