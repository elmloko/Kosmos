(function ($) {

    $(window).on('elementor/frontend/init', function () {

        var PremiumEqualHeightHandler = function ($scope) {

            if (!$scope.hasClass("premium-equal-height-yes"))
                return;

            premiumEqHeightHandler($scope);
        }

        function premiumEqHeightHandler($scope) {

            var section = $scope,
                editMode = elementorFrontend.isEditMode(),
                dataHolder = (editMode) ? section.find('#premium-temp-equal-height-' + section.data('id')) : section,
                addonSettings = dataHolder.data('pa-eq-height');

            if (!addonSettings)
                return;

            var enableOn = addonSettings.enableOn;

            if (0 === Object.keys(addonSettings).length) {
                return false;
            }

            triggerEqualHeight();

            function matchHeight(selector) {
                var $targets = section.find(selector),
                    heights = [];

                section.find(selector).css('minHeight', 'unset');

                jQuery.each($targets, function (key, valueObj) {
                    heights.push($(valueObj).outerHeight(true));
                });

                section.find(selector).css('minHeight', Math.max.apply(null, heights));
            }

            function triggerEqualHeight() {
                if (enableOn.includes(elementorFrontend.getCurrentDeviceMode()) && 0 !== addonSettings.target.length) {

                    addonSettings.target.forEach(function (target) {
                        matchHeight(target);
                    });
                } else {
                    section.find(addonSettings.target).css('minHeight', 'unset');
                }
            }

            window.onresize = triggerEqualHeight;
        };

        elementorFrontend.hooks.addAction("frontend/element_ready/section", PremiumEqualHeightHandler);
        elementorFrontend.hooks.addAction("frontend/element_ready/container", PremiumEqualHeightHandler);

    });

})(jQuery);