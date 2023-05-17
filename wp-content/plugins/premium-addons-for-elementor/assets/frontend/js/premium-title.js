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

    var PremiumTitleHandler = function ($scope, $) {

        var $titleContainer = $scope.find(".premium-title-container"),
            $titleElement = $titleContainer.find('.premium-title-text');

        if ($titleContainer.hasClass('style9')) {
            var $style9 = $scope.find(".premium-title-style9");

            $style9.each(function () {
                var elm = $(this);
                var holdTime = elm.attr('data-blur-delay') * 1000;
                elm.attr('data-animation-blur', 'process')
                elm.find('.premium-title-style9-letter').each(function (index, letter) {
                    index += 1;
                    var animateDelay;
                    if ($('body').hasClass('rtl')) {
                        animateDelay = 0.2 / index + 's';
                    } else {
                        animateDelay = index / 20 + 's';
                    }
                    $(letter).css({
                        '-webkit-animation-delay': animateDelay,
                        'animation-delay': animateDelay
                    });
                })
                setInterval(function () {
                    elm.attr('data-animation-blur', 'done')
                    setTimeout(function () {
                        elm.attr('data-animation-blur', 'process')
                    }, 150);
                }, holdTime);
            });
        }


        if ($titleContainer.hasClass('style8')) {

            var holdTime = $titleElement.attr('data-shiny-delay') * 1000,
                duration = $titleElement.attr('data-shiny-dur') * 1000;

            function shinyEffect() {
                $titleElement.get(0).setAttribute('data-animation', 'shiny');
                setTimeout(function () {
                    $titleElement.removeAttr('data-animation')
                }, duration);
            }

            (function repeat() {
                shinyEffect();
                setTimeout(repeat, holdTime);
            })();
        }

    };

    $(window).on('elementor/frontend/init', function () {
        elementorFrontend.hooks.addAction('frontend/element_ready/premium-addon-title.default',  PremiumTitleHandler);
        elementorFrontend.hooks.addAction('frontend/element_ready/premium-addon-title.default',  PremiumMaskHandler);
    });
 })(jQuery);

