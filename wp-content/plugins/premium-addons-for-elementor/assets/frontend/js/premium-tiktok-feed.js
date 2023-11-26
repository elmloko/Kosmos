(function ($) {

    var PremiumTiktokHandler = function ($scope, $) {

        var widgetID = $scope.data('id'),
            $outerWrapper = $scope.find('.premium-tiktok-feed__outer-wrapper'),
            settings = $outerWrapper.data('pa-tiktok-settings');

        if (!settings) {
            return;
        }

        var $videosWrapper = $scope.find('.premium-tiktok-feed__videos-wrapper');

        if ("masonry" === settings.layout && !settings.carousel) {
            $videosWrapper.imagesLoaded(function () {
                $videosWrapper.isotope(getIsoTopeSettings());
            });
        }

        if (settings.loadMore && !settings.carousel) {

            window['paLoadMoreBookmark' + widgetID] = $outerWrapper.data('pa-load-bookmark');
            window['paHiddenPins' + widgetID] = $scope.find('.premium-tiktok-feed__video-outer-wrapper.premium-display-none').length;

            $scope.find('.premium-tiktok-feed__load-more-btn').on('click.paLoadMoreVids', function (e) {

                var bookmark = window['paLoadMoreBookmark' + widgetID],
                    count = settings.loadMoreCount;

                for (var i = 0; i < count; i++) {
                    // $scope.find('.premium-tiktok-feed__video-outer-wrapper').eq( bookmark + i ).show().addClass('premium-pin-shown');
                    $scope.find('.premium-tiktok-feed__video-outer-wrapper').eq(bookmark + i).show(0, function () {
                        var _this = this;
                        setTimeout(function () {
                            $(_this).removeClass('premium-display-none'); // fix: share menu not fully displayed.
                        }, 400);
                    });
                }

                window['paLoadMoreBookmark' + widgetID] = bookmark + count;
                window['paHiddenPins' + widgetID] -= count;

                if (0 >= window['paHiddenPins' + widgetID]) {
                    $scope.find('.premium-tiktok-feed__load-more-btn').remove();
                }
            });
        }

        if (settings.carousel) {

            var carouselSettings = $outerWrapper.data('pa-carousel');

            $videosWrapper.addClass('premium-addons__v-hidden').slick(getSlickSettings(carouselSettings));

            $videosWrapper.removeClass('premium-addons__v-hidden');
        }

        // share links
        $scope.find('.premium-copy-link').on('click.paTiktokCopyLink', function () {
            $scope.find('.premium-tiktok-share-menu').css('visibility', 'hidden')

            var txt = $(this).data('pa-link');
            navigator.clipboard.writeText(txt);
        });

        $scope.find('.premium-tiktok-share-item:not(.premium-copy-link)').on('click.paTiktokShare', function () {

            var link = $(this).data('pa-link');
            window.open(link, 'popup', 'width=600,height=600');
        });

        if ('lightbox' === settings.onClick) {
            initFeedPopupEvents($scope);
        } else if ('play' === settings.onClick) {

            if ($scope.hasClass('premium-tiktok-feed__vid-layout-2')) {
                $scope.find('.premium-tiktok-feed__vid-meta-wrapper').on('click', function () {
                    $(this).next().trigger('click');
                });
            }

            $scope.find('.premium-tiktok-feed__video-media').on('click', function () {
                var $video = $(this).find('video');

                $(this).find('.premium-tiktok-feed__play-icon').toggleClass('premium-addons__v-hidden');

                if (!$video.hasClass('video-playing')) {
                    $video.get(0).play();
                } else {
                    $video.get(0).pause();
                }

                $video.toggleClass('video-playing');

            });
        }

        if (settings.playOnHover) {

            $scope.find('.premium-tiktok-feed__video-media').hover(function () {

                $scope.find('.premium-tiktok-feed__play-icon').removeClass('premium-addons__v-hidden');
                $scope.find('video').get(0).pause();

                $(this).find('.premium-tiktok-feed__play-icon').addClass('premium-addons__v-hidden');
                $(this).find('video').get(0).play();

            }, function () {
                $(this).find('.premium-tiktok-feed__play-icon').removeClass('premium-addons__v-hidden');
                $(this).find('video').get(0).pause();
            })
        }


        function closeModal() {
            $('.premium-tiktok-feed-modal-iframe-modal').css('display', 'none');

            $(".premium-tiktok-feed-modal-iframe-modal #pa-tiktok-vid-control-iframe").attr({
                'src': ''
            });
        }

        function initFeedPopupEvents($scope) {

            var isBanner = $scope.hasClass('premium-tiktok-feed__vid-layout-2');

            if (isBanner) {
                $scope.find('.premium-tiktok-feed__vid-meta-wrapper').on('click.paTriggerModal', function () {
                    $scope.find('.premium-tiktok-feed__video-media').trigger('click');
                });
            }

            $scope.find('.premium-tiktok-feed__video-media').on('click.paTiktokModal', function () {
                var embedLink = $(this).data('pa-tiktok-embed'),
                    $modalContainer = $('.premium-tiktok-feed-modal-iframe-modal'),
                    paIframe = $modalContainer.find("#pa-tiktok-vid-control-iframe");

                $modalContainer.css('display', 'flex');
                paIframe.css("z-index", "-1");

                paIframe.attr("src", 'https://www.tiktok.com/embed/v2/' + embedLink);
                paIframe.show();
                paIframe.css("z-index", "1");
            });

            // close model events.
            $('.eicon-close').on('click.paClosePopup', closeModal);

            $(document).on('click.paClosePopup', '.premium-tiktok-feed-modal-iframe-modal', function (e) {
                if ($(e.target).closest(".premium-tiktok-feed__video-content").length < 1) {
                    closeModal();
                }
            });
        }

        function getSlickSettings(settings) {

            var prevArrow = settings.arrows ? '<a type="button" data-role="none" class="carousel-arrow carousel-prev" aria-label="Previous" role="button" style=""><i class="fas fa-angle-left" aria-hidden="true"></i></a>' : '',
                nextArrow = settings.arrows ? '<a type="button" data-role="none" class="carousel-arrow carousel-next" aria-label="Next" role="button" style=""><i class="fas fa-angle-right" aria-hidden="true"></i></a>' : '';

            return {
                infinite: true,
                draggable: true,
                rows: 0,
                slidesToShow: settings.slidesToShow,
                slidesToScroll: settings.slidesToScroll || 1,
                responsive: [
                    {
                        breakpoint: 1025,
                        settings: {
                            slidesToShow: settings.slidesToShowTab,
                            slidesToScroll: 1
                        }
                    },
                    {
                        breakpoint: 768,
                        settings: {
                            slidesToShow: settings.slidesToShowMobile,
                            slidesToScroll: 1
                        }
                    }
                ],
                autoplay: settings.autoPlay,
                speed: settings.speed || 300,
                autoplaySpeed: settings.autoplaySpeed || 5000,
                fade: settings.fade,
                centerMode: settings.centerMode,
                centerPadding: settings.centerPadding + "px",
                nextArrow: nextArrow,
                prevArrow: prevArrow,
                dots: settings.dots,
                customPaging: function () {
                    return '<i class="fas fa-circle"></i>';
                }
            };
        };

        function getIsoTopeSettings() {

            return {
                itemSelector: ".premium-tiktok-feed__video-outer-wrapper",
                percentPosition: true,
                animationOptions: {
                    duration: 750,
                    easing: "linear",
                    queue: false
                }
            }
        };
    };

    $(window).on('elementor/frontend/init', function () {
        elementorFrontend.hooks.addAction('frontend/element_ready/premium-tiktok-feed.default', PremiumTiktokHandler);
    });
})(jQuery);