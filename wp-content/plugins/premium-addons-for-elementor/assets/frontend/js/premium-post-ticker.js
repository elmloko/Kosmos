(function ($) {

    var PremiumPostTickerHandler = function ($scope, $) {

        var timer = null,
            $postsWrapper = $scope.find('.premium-post-ticker__posts-wrapper'),
            settings = $scope.find('.premium-post-ticker__outer-wrapper').data('ticker-settings');

        if (!settings)
            return;

        if ('' !== settings.animation && 'layout-4' !== settings.layout) {
            $postsWrapper.on("init", function (event) {
                resetAnimations("init");
            });
        }

        if (settings.typing) {
            $postsWrapper.on('init', function (event, slick) {
                var $currentTyping = $postsWrapper.find('[data-slick-index="' + slick.currentSlide + '"] .premium-post-ticker__post-title a');

                typeTitle($currentTyping);
            });

            $postsWrapper.on('beforeChange', function (event, slick, currentSlide, nextSlide) {

                var $typedItem = $postsWrapper.find('[data-slick-index="' + currentSlide + '"] .premium-post-ticker__post-title'),
                    $currentTyping = $postsWrapper.find('[data-slick-index="' + currentSlide + '"] .premium-post-ticker__post-title a'),
                    $nextTyping = $postsWrapper.find('[data-slick-index="' + nextSlide + '"] .premium-post-ticker__post-title a');

                clearInterval(timer);
                $typedItem.removeClass('premium-text-typing');
                $currentTyping.text('');

                typeTitle($nextTyping);
            });
        }

        $postsWrapper.slick(getSlickSettings());

        if ('' !== settings.animation && 'layout-4' !== settings.layout) {

            $postsWrapper.on("beforeChange", function () {
                resetAnimations();
            });

            $postsWrapper.on("afterChange", function () {
                triggerAnimation();
            });
        }

        if (settings.arrows) {

            $scope.find('.premium-post-ticker__arrows a').on('click.paTickerNav', function () {

                if ($(this).hasClass('prev-arrow')) {

                    $postsWrapper.slick('slickPrev');

                } else if ($(this).hasClass('next-arrow')) {

                    $postsWrapper.slick('slickNext');

                }
            });
        }

        $scope.find('.premium-post-ticker__outer-wrapper').removeClass('premium-post-ticker__hidden');

        function getSlickSettings() {

            $postsWrapper.off('mouseenter.paTickerPause');

            var closestTab = $scope.closest('.premium-tabs-content-section'),
                autoPlay = settings.autoPlay;

            //If there is a parent tab and it's not active, then autoplay should not be true.
            if (closestTab.length > 0) {
                if (!closestTab.hasClass('content-current'))
                    autoPlay = false;
            }

            var slickSetting = {
                infinite: true,
                slidesToShow: 1,
                slidesToScroll: 1,
                arrows: false,
                autoplay: autoPlay,
                rows: 0,
                speed: settings.speed,
                fade: settings.fade,
                draggable: true,
                pauseOnHover: settings.pauseOnHover,
                vertical: settings.vertical
            };

            if (settings.autoPlay) {
                slickSetting.autoplaySpeed = settings.autoplaySpeed;
            }

            if (settings.infinite) {
                slickSetting.autoplaySpeed = 0;
                slickSetting.cssEase = 'linear';

                slickSetting.useCSS = false;

                if ('layout-4' !== settings.layout && !settings.vertical) {
                    slickSetting.variableWidth = true; // this only is required if the slider is horizontal
                }
            }

            if ('layout-4' === settings.layout) {
                slickSetting.vertical = true;
                slickSetting.slidesToShow = settings.slidesToShow || 1;
            }

            if ( $scope.hasClass('premium-reversed-yes') && 'layout-4' !== settings.layout && !settings.vertical && !settings.typing && !settings.fade) {

                slickSetting.rtl = true;
            }

            return slickSetting;
        }

        function resetAnimations() {

            var $slides = $postsWrapper.find(".slick-slide").not(".slick-current");

            $slides.each(function (index, elem) {
                $(elem).removeClass("animated " + settings.animation).addClass("elementor-invisible");
            });
        };

        function triggerAnimation() {

            $postsWrapper.find(".slick-active.elementor-invisible").each(function (index, elem) {

                $(elem).removeClass("elementor-invisible").addClass(settings.animation + ' animated');

            });
        }

        function typeTitle($tickerItem) {

            if (!$tickerItem.length) {
                return;
            }

            var typingCounter = 0,
                $typedItem = $tickerItem.closest('.premium-post-ticker__post-title'),
                typingText = $tickerItem.data('typing'),
                typingTextLength = typingText.length;

            $typedItem.addClass('premium-text-typing');
            $tickerItem.text(typingText.substr(0, typingCounter++));

            timer = setInterval(function () {
                if (typingCounter <= typingTextLength) {
                    $tickerItem.text(typingText.substr(0, typingCounter++));
                } else {
                    clearInterval(timer);
                    $typedItem.removeClass('premium-text-typing'); // have the '_' after.
                }
            }, 40);
        }
    };

    $(window).on('elementor/frontend/init', function () {
        elementorFrontend.hooks.addAction('frontend/element_ready/premium-post-ticker.default', PremiumPostTickerHandler);
    });

})(jQuery);
