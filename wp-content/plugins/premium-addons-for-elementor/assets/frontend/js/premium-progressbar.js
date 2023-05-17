(function ($) {

    var PremiumProgressBarWidgetHandler = function ($scope, trigger) {

        var $progressbarElem = $scope.find(".premium-progressbar-container"),
            settings = $progressbarElem.data("settings"),
            length = settings.progress_length,
            speed = settings.speed,
            type = settings.type,
            mScroll = settings.mScroll;

        if ("line" === type) {

            var $progressbar = $progressbarElem.find(".premium-progressbar-bar");

            if (settings.gradient)
                $progressbar.css("background", "linear-gradient(-45deg, " + settings.gradient + ")");

            if ('yes' !== mScroll) {
                $progressbar.animate({
                    width: length + "%"
                }, speed);
            }

        } else if ("circle" === type || "half-circle" === type) {
            if (length > 100)
                length = 100;

            var degreesFactor = 1.8 * (elementorFrontend.config.is_rtl ? -1 : 1);

            if ('yes' !== mScroll) {
                $progressbarElem.find(".premium-progressbar-hf-circle-progress").css({
                    transform: "rotate(" + length * degreesFactor + "deg)",
                });
            }

            $progressbarElem.prop({
                'counter': 0
            }).animate({
                counter: length
            }, {
                duration: speed,
                easing: 'linear',
                step: function (counter) {
                    var rotate = (counter * 3.6);

                    if ('yes' !== mScroll) {

                        $progressbarElem.find(".premium-progressbar-right-label").text(Math.ceil(counter) + "%");

                        $progressbarElem.find(".premium-progressbar-circle-left").css('transform', "rotate(" + rotate + "deg)");
                    }

                    if ('circle' === type && rotate > 180) {

                        $progressbarElem.find(".premium-progressbar-circle").css({
                            '-webkit-clip-path': 'inset(0)',
                            'clip-path': 'inset(0)',
                        });

                        $progressbarElem.find(".premium-progressbar-circle-right").css('visibility', 'visible');
                    }
                }
            });

        } else {

            var $progressbar = $progressbarElem.find(".premium-progressbar-bar-wrap"),
                width = $progressbarElem.outerWidth(),
                dotSize = settings.dot || 25,
                dotSpacing = settings.spacing || 10,
                numberOfCircles = Math.ceil(width / (dotSize + dotSpacing)),
                circlesToFill = numberOfCircles * (length / 100),
                numberOfTotalFill = Math.floor(circlesToFill),
                fillPercent = 100 * (circlesToFill - numberOfTotalFill);

            $progressbar.attr('data-circles', numberOfCircles);
            $progressbar.attr('data-total-fill', numberOfTotalFill);
            $progressbar.attr('data-partial-fill', fillPercent);

            var className = "progress-segment";
            for (var i = 0; i < numberOfCircles; i++) {
                className = "progress-segment";
                var innerHTML = '';

                if (i < numberOfTotalFill) {
                    innerHTML = "<div class='segment-inner'></div>";
                } else if (i === numberOfTotalFill) {

                    innerHTML = "<div class='segment-inner'></div>";
                }

                $progressbar.append("<div class='" + className + "'>" + innerHTML + "</div>");

            }

            if ("frontend" !== trigger) {
                PremiumProgressDotsHandler($scope);
            }

        }

    };

    var PremiumProgressDotsHandler = function ($scope) {

        var $progressbarElem = $scope.find(".premium-progressbar-container"),
            settings = $progressbarElem.data("settings"),
            $progressbar = $scope.find(".premium-progressbar-bar-wrap"),
            data = $progressbar.data(),
            speed = settings.speed,
            increment = 0;

        var numberOfTotalFill = data.totalFill,
            numberOfCircles = data.circles,
            fillPercent = data.partialFill;

        dotIncrement(increment);

        function dotIncrement(inc) {

            var $dot = $progressbar.find(".progress-segment").eq(inc),
                dotWidth = 100;

            if (inc === numberOfTotalFill)
                dotWidth = fillPercent

            $dot.find(".segment-inner").animate({
                width: dotWidth + '%'
            }, speed / numberOfCircles, function () {
                increment++;
                if (increment <= numberOfTotalFill) {
                    dotIncrement(increment);
                }

            });
        }
    };

    var PremiumProgressBarScrollWidgetHandler = function ($scope, $) {

        var $progressbarElem = $scope.find(".premium-progressbar-container"),
            settings = $progressbarElem.data("settings"),
            type = settings.type;

        if ("dots" === type) {
            PremiumProgressBarWidgetHandler($scope, "frontend");
        }

        elementorFrontend.waypoint($scope, function () {
            if ("dots" !== type) {
                PremiumProgressBarWidgetHandler($(this));
            } else {
                PremiumProgressDotsHandler($(this));
            }

        });
    };

    $(window).on('elementor/frontend/init', function () {
        elementorFrontend.hooks.addAction('frontend/element_ready/premium-addon-progressbar.default', PremiumProgressBarScrollWidgetHandler);
    });
})(jQuery);

