/****** Premium Fancy Text Handler ******/
(function ($) {
    var PremiumFancyTextHandler = function ($scope, $) {

        var $elem = $scope.find(".premium-fancy-text-wrapper"),
            settings = $elem.data("settings"),
            loadingSpeed = settings.delay || 2500,
            itemCount = $elem.find('.premium-fancy-list-items').length,
            loopCount = ('' === settings.count && !['typing', 'slide', 'autofade'].includes(settings.effect)) ? 'infinite' : (settings.count * itemCount);

        function escapeHtml(unsafe) {
            return unsafe.replace(/&/g, "&amp;").replace(/</g, "&lt;").replace(/>/g, "&gt;").replace(
                /"/g, "&quot;");
        }

        if ("typing" === settings.effect) {

            var fancyStrings = [];

            settings.strings.forEach(function (item) {
                fancyStrings.push(escapeHtml(item));
            });
            var fancyTextID = '#' + $elem.find('.premium-fancy-text').attr('id');
            var typedInstance = new Typed(fancyTextID, {
                strings: fancyStrings,
                typeSpeed: settings.typeSpeed,
                backSpeed: settings.backSpeed,
                startDelay: settings.startDelay,
                backDelay: settings.backDelay,
                showCursor: settings.showCursor,
                cursorChar: settings.cursorChar,
                loop: settings.loop
            });

            //To start/stop programmatically.
            if ($scope.hasClass("fancy-text-stop")) {
                typedInstance.stop();
            }

            $(".fancy-text-stop").bind("fancy-text-start", function () {
                typedInstance.start();
            });


        } else if ("slide" === settings.effect) {
            loadingSpeed = settings.pause;

            $elem.find(".premium-fancy-text").vTicker({
                speed: settings.speed,
                showItems: settings.showItems,
                pause: settings.pause,
                mousePause: settings.mousePause,
                direction: "up"
            });

        } else if ('auto-fade' === settings.effect) {
            var $items = $elem.find(".premium-fancy-list-items"),
                len = $items.length;

            if (0 === len) {
                return;
            }

            var delay = settings.duration / len,
                itemDelay = 0;

            loadingSpeed = delay;

            $items.each(function ($index, $item) {
                $item.style.animationDelay = itemDelay + 'ms';
                itemDelay += delay;
            });

        } else {

            setFancyAnimation();

            function setFancyAnimation() {

                var $item = $elem.find(".premium-fancy-list-items"),
                    current = 1;

                //Get effect settings
                var delay = settings.delay || 2500,
                    loopCount = settings.count;

                //If Loop Count option is set
                if (loopCount) {
                    var currentLoop = 1,
                        fancyStringsCount = $elem.find(".premium-fancy-list-items").length;
                }

                var loopInterval = setInterval(function () {

                    var animationClass = "";

                    //Add animation class
                    if (settings.effect === "custom")
                        animationClass = "animated " + settings.animation;

                    //Show current active item
                    $item.eq(current).addClass("premium-fancy-item-visible " + animationClass).removeClass("premium-fancy-item-hidden");

                    var $inactiveItems = $item.filter(function (index) {
                        return index !== current;
                    });

                    //Hide inactive items
                    $inactiveItems.addClass("premium-fancy-item-hidden").removeClass("premium-fancy-item-visible " + animationClass);

                    current++;

                    //Restart loop
                    if ($item.length === current)
                        current = 0;

                    //Increment interval and check if loop count is reached
                    if (loopCount) {
                        currentLoop++;

                        if ((fancyStringsCount * loopCount) === currentLoop)
                            clearInterval(loopInterval);
                    }


                }, delay);

            }
        }

        //Show the strings after the layout is set.
        if ("typing" !== settings.effect) {
            setTimeout(function () {
                $elem.find(".premium-fancy-text").css('opacity', '1');
            }, 500);

        }

        if ('loading' === settings.loading && 'typing' !== settings.effect) {
            $scope.find('.premium-fancy-text').append('<span class="premium-loading-bar"></span>');
            $scope.find('.premium-loading-bar').css({
                'animation-iteration-count': loopCount,
                'animation-duration': loadingSpeed + 'ms'
            });
        }

    };

    $(window).on('elementor/frontend/init', function () {
        elementorFrontend.hooks.addAction('frontend/element_ready/premium-addon-fancy-text.default', PremiumFancyTextHandler);
    });
})(jQuery);

