(function ($) {
    var PremiumVideoBoxWidgetHandler = function ($scope, $) {

        var $videoBoxElement = $scope.find(".premium-video-box-container"),
            $videoListElement = $scope.find(".premium-video-box-playlist-container"),
            $videoContainer = $videoBoxElement.find(".premium-video-box-video-container"), //should be clicked
            $videoInnerContainer = $videoBoxElement.find('.premium-video-box-inner-wrap'),
            $videoImageContainer = $videoInnerContainer.find('.premium-video-box-image-container'),
            type = $videoBoxElement.data("type"),
            thumbnail = $videoBoxElement.data("thumbnail"),
            sticky = $videoBoxElement.data('sticky'),
            stickyOnPlay = $videoBoxElement.data('sticky-play'),
            hoverEffect = $videoBoxElement.data('hover'),
            $lighboxContainer = $videoListElement.length ? $videoListElement : $videoBoxElement,
            lightBox = $lighboxContainer.data('lightbox') ? $lighboxContainer.data('lightbox') : false,
            video, vidSrc;

        if (lightBox) {

            if ('prettyphoto' === lightBox.type) {
                $lighboxContainer.find(".premium-vid-lightbox-container[data-rel^='prettyPhoto']").prettyPhoto(getPrettyPhotoSettings(lightBox.theme));
            }

            $lighboxContainer.find('.premium-video-box-image-container, .premium-video-box-play-icon-container').on('click', function (e) {
                triggerLightbox($(this).closest('.premium-video-box-container'), lightBox.type);
            });

        } else {
            // Youtube playlist option.
            if ($videoListElement.length) {

                //Make sure that video were pulled from the API.
                if (!$videoContainer.length)
                    return;

                $videoContainer.each(function (index, item) {

                    var vidSrc,
                        $videoContainer = $(item),
                        $videoBoxElement = $videoContainer.closest(".premium-video-box-container"),
                        $trigger = $videoContainer.closest(".premium-video-box-trigger");

                    vidSrc = $videoContainer.data("src");
                    vidSrc = vidSrc + "&autoplay=1";

                    $trigger.on("click", function () {

                        var $iframe = $("<iframe/>");

                        $iframe.attr({
                            "src": vidSrc,
                            "frameborder": "0",
                            "allowfullscreen": "1",
                            "allow": "autoplay;encrypted-media;"
                        });
                        $videoContainer.css("background", "#000");
                        $videoContainer.html($iframe);

                        $videoBoxElement.find(
                            ".premium-video-box-image-container, .premium-video-box-play-icon-container"
                        ).remove();

                    });

                });

                return;
            }

            if ("self" === type) {

                video = $videoContainer.find("video");
                vidSrc = video.attr("src");

                if ($videoBoxElement.data("play-viewport")) {
                    elementorFrontend.waypoint($videoBoxElement, function () {
                        playVideo();
                    }, {
                        offset: 'top-in-view',
                        triggerOnce: false
                    });

                    if ($videoBoxElement.data("play-reset")) {
                        elementorFrontend.waypoint($videoBoxElement, function (direction) {

                            if ('up' === direction)
                                restartVideo();
                        }, {
                            offset: "100%",
                            triggerOnce: false
                        });
                    }
                }

            } else {

                vidSrc = $videoContainer.data("src");

                if (!thumbnail || -1 !== vidSrc.indexOf("autoplay=1")) {

                    //Check if Autoplay on viewport option is enabled
                    if ($videoBoxElement.data("play-viewport")) {
                        elementorFrontend.waypoint($videoBoxElement, function () {
                            playVideo();
                        }, {
                            offset: 'top-in-view'
                        });
                    } else {
                        playVideo();
                    }

                } else {
                    vidSrc = vidSrc + "&autoplay=1";
                }

            }

            $videoBoxElement.on("click", function () {
                playVideo();
            });

            if ("yes" !== sticky || "yes" === stickyOnPlay)
                return;

            stickyOption();
        }

        function playVideo() {

            if ($videoBoxElement.hasClass("playing")) return;

            $videoBoxElement.addClass("playing");

            if (stickyOnPlay === 'yes')
                stickyOption();

            if ("self" === type) {

                $(video).get(0).play();

                $videoContainer.css({
                    opacity: "1",
                    visibility: "visible"
                });

            } else {

                var $iframe = $("<iframe/>");

                $iframe.attr({
                    "src": vidSrc,
                    "frameborder": "0",
                    "allowfullscreen": "1",
                    "allow": "autoplay;encrypted-media;"
                });
                $videoContainer.css("background", "#000");
                $videoContainer.html($iframe);
            }

            $videoBoxElement.find(
                ".premium-video-box-image-container, .premium-video-box-play-icon-container, .premium-video-box-description-container"
            ).remove();

            if ("vimeo" === type)
                $videoBoxElement.find(".premium-video-box-vimeo-wrap").remove();
        }

        function restartVideo() {

            $videoBoxElement.removeClass("playing");

            $(video).get(0).pause();
            $(video).get(0).currentTime = 0;

        }

        function triggerLightbox($container, type) {
            if ('elementor' === type) {
                $container.find('.premium-video-box-video-container').trigger('click');
            } else {
                $container.find(".premium-vid-lightbox-container[data-rel^='prettyPhoto']").trigger('click');
            }
        }

        function stickyOption() {

            var stickyDesktop = $videoBoxElement.data('hide-desktop'),
                stickyTablet = $videoBoxElement.data('hide-tablet'),
                stickyMobile = $videoBoxElement.data('hide-mobile'),
                stickyMargin = $videoBoxElement.data('sticky-margin');

            $videoBoxElement.off('click').on('click', function (e) {
                // if ('yes' === sticky) {
                var stickyTarget = e.target.className;
                if ((stickyTarget.toString().indexOf('premium-video-box-sticky-close') >= 0) || (stickyTarget.toString().indexOf('premium-video-box-sticky-close') >= 0)) {
                    return false;
                }
                // }
                playVideo();

            });

            //Make sure Elementor Waypoint is defined
            if (typeof elementorFrontend.waypoint !== 'undefined') {

                var stickyWaypoint = elementorFrontend.waypoint(
                    $videoBoxElement,
                    function (direction) {
                        if ('down' === direction) {

                            $videoBoxElement.removeClass('premium-video-box-sticky-hide').addClass('premium-video-box-sticky-apply premium-video-box-filter-sticky');

                            //Fix conflict with Elementor motion effects
                            if ($scope.hasClass("elementor-motion-effects-parent")) {
                                $scope.removeClass("elementor-motion-effects-perspective").find(".elementor-widget-container").addClass("premium-video-box-transform");
                            }

                            if ($videoBoxElement.data("mask")) {
                                //Fix Sticky position issue when drop-shadow is applied
                                $scope.find(".premium-video-box-mask-filter").removeClass("premium-video-box-mask-filter");

                                $videoBoxElement.find(':first-child').removeClass('premium-video-box-mask-media');

                                $videoImageContainer.removeClass(hoverEffect).removeClass('premium-video-box-mask-media').css({
                                    'transition': 'width 0.2s, height 0.2s',
                                    '-webkit-transition': 'width 0.2s, height 0.2s'
                                });
                            }

                            $(document).trigger('premium_after_sticky_applied', [$scope]);

                            // Entrance Animation Option
                            if ($videoInnerContainer.data("video-animation") && " " != $videoInnerContainer.data("video-animation")) {
                                $videoInnerContainer.css("opacity", "0");
                                var animationDelay = $videoInnerContainer.data('delay-animation');
                                setTimeout(function () {

                                    $videoInnerContainer.css("opacity", "1").addClass("animated " + $videoInnerContainer.data("video-animation"));

                                }, animationDelay * 1000);
                            }

                        } else {

                            $videoBoxElement.removeClass('premium-video-box-sticky-apply  premium-video-box-filter-sticky').addClass('premium-video-box-sticky-hide');

                            //Fix conflict with Elementor motion effects
                            if ($scope.hasClass("elementor-motion-effects-parent")) {
                                $scope.addClass("elementor-motion-effects-perspective").find(".elementor-widget-container").removeClass("premium-video-box-transform");
                            }

                            if ($videoBoxElement.data("mask")) {
                                //Fix Sticky position issue when drop-shadow is applied
                                $videoBoxElement.parent().addClass("premium-video-box-mask-filter");

                                $videoBoxElement.find(':first-child').eq(0).addClass('premium-video-box-mask-media');
                                $videoImageContainer.addClass('premium-video-box-mask-media');
                            }

                            $videoImageContainer.addClass(hoverEffect).css({
                                'transition': 'all 0.2s',
                                '-webkit-transition': 'all 0.2s'
                            });

                            $videoInnerContainer.removeClass("animated " + $videoInnerContainer.data("video-animation"));
                        }
                    }, {
                    offset: 0 + '%',
                    triggerOnce: false
                }
                );
            }

            var closeBtn = $scope.find('.premium-video-box-sticky-close');

            closeBtn.off('click.closetrigger').on('click.closetrigger', function (e) {
                e.stopPropagation();
                stickyWaypoint[0].disable();

                $videoBoxElement.removeClass('premium-video-box-sticky-apply premium-video-box-sticky-hide');

                //Fix conflict with Elementor motion effects
                if ($scope.hasClass("elementor-motion-effects-parent")) {
                    $scope.addClass("elementor-motion-effects-perspective").find(".elementor-widget-container").removeClass("premium-video-box-transform");
                }

                if ($videoBoxElement.data("mask")) {
                    //Fix Sticky position issue when drop-shadow is applied
                    $videoBoxElement.parent().addClass("premium-video-box-mask-filter");

                    //Necessary classes for mask shape option
                    $videoBoxElement.find(':first-child').eq(0).addClass('premium-video-box-mask-media');
                    $videoImageContainer.addClass('premium-video-box-mask-media');
                }


            });

            checkResize(stickyWaypoint);

            checkScroll();

            window.addEventListener("scroll", checkScroll);

            $(window).resize(function (e) {
                checkResize(stickyWaypoint);
            });

            function checkResize(stickyWaypoint) {
                var currentDeviceMode = elementorFrontend.getCurrentDeviceMode();

                if ('' !== stickyDesktop && currentDeviceMode == stickyDesktop) {
                    disableSticky(stickyWaypoint);
                } else if ('' !== stickyTablet && currentDeviceMode == stickyTablet) {
                    disableSticky(stickyWaypoint);
                } else if ('' !== stickyMobile && currentDeviceMode == stickyMobile) {
                    disableSticky(stickyWaypoint);
                } else {
                    stickyWaypoint[0].enable();
                }
            }

            function disableSticky(stickyWaypoint) {
                stickyWaypoint[0].disable();
                $videoBoxElement.removeClass('premium-video-box-sticky-apply premium-video-box-sticky-hide');
            }

            function checkScroll() {
                if ($videoBoxElement.hasClass('premium-video-box-sticky-apply')) {
                    $videoInnerContainer.draggable({
                        start: function () {
                            $(this).css({
                                transform: "none",
                                top: $(this).offset().top + "px",
                                left: $(this).offset().left + "px"
                            });
                        },
                        containment: 'window'
                    });
                }
            }

            $(document).on('premium_after_sticky_applied', function (e, $scope) {
                var infobar = $scope.find('.premium-video-box-sticky-infobar');

                if (0 !== infobar.length) {
                    var infobarHeight = infobar.outerHeight();

                    if ($scope.hasClass('premium-video-sticky-center-left') || $scope.hasClass('premium-video-sticky-center-right')) {
                        infobarHeight = Math.ceil(infobarHeight / 2);
                        $videoInnerContainer.css('top', 'calc( 50% - ' + infobarHeight + 'px )');
                    }

                    if ($scope.hasClass('premium-video-sticky-bottom-left') || $scope.hasClass('premium-video-sticky-bottom-right')) {
                        if ('' !== stickyMargin) {
                            infobarHeight = Math.ceil(infobarHeight);
                            var stickBottom = infobarHeight + stickyMargin;
                            $videoInnerContainer.css('bottom', stickBottom);
                        }
                    }
                }
            });

        }

        function getPrettyPhotoSettings(theme) {
            return {
                theme: theme,
                hook: "data-rel",
                opacity: 0.7,
                show_title: false,
                deeplinking: false,
                overlay_gallery: true,
                custom_markup: "",
                default_width: 900,
                default_height: 506,
                social_tools: ""
            };
        }
    };

    $(window).on('elementor/frontend/init', function () {
        elementorFrontend.hooks.addAction('frontend/element_ready/premium-addon-video-box.default', PremiumVideoBoxWidgetHandler);
    });
})(jQuery);

