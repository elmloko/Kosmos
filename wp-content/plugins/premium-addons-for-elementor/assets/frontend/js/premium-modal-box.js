(function ($) {

    var PremiumModalBoxHandler = function ($scope, $) {

        var $modalElem = $scope.find(".premium-modal-box-container"),
            settings = $modalElem.data("settings"),
            $modal = $modalElem.find(".premium-modal-box-modal-dialog");

        if (!settings) {
            return;
        }

        if (settings.trigger === "pageload") {
            $(document).ready(function ($) {
                setTimeout(function () {
                    $modalElem.find(".premium-modal-box-modal").modal();
                }, settings.delay * 1000);
            });
        }

        if ($modal.data("modal-animation") && " " != $modal.data("modal-animation")) {

            var animationDelay = $modal.data('delay-animation');

            new Waypoint({
                element: $modal,
                handler: function () {
                    setTimeout(function () {
                        $modal.css("opacity", "1").addClass("animated " + $modal.data("modal-animation"));
                    }, animationDelay * 1000);
                    this.destroy();
                },
                offset: Waypoint.viewportHeight() - 150,
            });
        }
    };

    $(window).on('elementor/frontend/init', function () {
        elementorFrontend.hooks.addAction('frontend/element_ready/premium-addon-modal-box.default', PremiumModalBoxHandler);
    });
 })(jQuery);

