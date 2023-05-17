(function ($) {

    $(window).on('elementor/frontend/init', function () {

        var PremiumSVGDrawerHandler = elementorModules.frontend.handlers.Base.extend({

            bindEvents: function () {

                ScrollTrigger.config({
                    limitCallbacks: true,
                    ignoreMobileResize: true
                });

                this.run();
            },

            run: function () {

                gsap.registerPlugin(ScrollTrigger);

                var $scope = this.$element;

                $scope.find(".elementor-invisible").removeClass("elementor-invisible");

                //remove title HTML tag
                $scope.find("title").remove();

                if (!$scope.hasClass("premium-svg-animated-yes"))
                    return;

                var elemID = $scope.data("id"),
                    settings = this.getElementSettings(),
                    scrollAction = settings.scroll_action,
                    scrollTrigger = null;

                if ('automatic' === scrollAction) {

                    scrollTrigger = 'custom' !== settings.animate_trigger ? settings.animate_trigger : settings.animate_offset.size + "%";

                    var animRev = settings.anim_rev ? 'pause play reverse' : 'none',
                        timeLine = new TimelineMax({
                            repeat: settings.loop ? -1 : 0,
                            yoyo: settings.yoyo ? true : false,
                            scrollTrigger: {
                                trigger: '.elementor-element-' + elemID,
                                toggleActions: "play " + animRev,
                                start: "top " + scrollTrigger, //when the top of the element hits that offset of the viewport.
                            }
                        });


                } else {

                    var timeLine = new TimelineMax({
                        repeat: ('hover' === scrollAction && settings.loop) ? -1 : 0,
                        yoyo: ('hover' === scrollAction && settings.yoyo) ? true : false,
                    });

                    if ('viewport' === scrollAction)
                        scrollTrigger = settings.animate_offset.size / 100;
                }

                var fromOrTo = !$scope.hasClass("premium-svg-anim-rev-yes") ? 'from' : 'to',
                    $paths = $scope.find("path, circle, rect, square, ellipse, polyline, polygon, line"),
                    lastPathIndex = 0,
                    startOrEndPoint = 'from' === fromOrTo ? settings.animate_start_point.size : settings.animate_end_point.size;

                $paths.each(function (pathIndex, path) {

                    var $path = $(path);

                    $path.attr("fill", "transparent");

                    if ($scope.hasClass("premium-svg-sync-yes"))
                        pathIndex = 0;

                    lastPathIndex = pathIndex;

                    timeLine[fromOrTo]($path, 1, {
                        PaSvgDrawer: (startOrEndPoint || 0) + "% 0",
                    }, pathIndex);

                });

                if ('yes' === settings.svg_fill) {
                    if (lastPathIndex == 0)
                        lastPathIndex = 1;

                    timeLine.to($paths, 1, {
                        fill: settings.svg_color,
                        stroke: settings.svg_stroke
                    }, lastPathIndex);
                }


                if ('viewport' === scrollAction) {

                    var controller = new ScrollMagic.Controller(),
                        scene = new ScrollMagic.Scene({
                            triggerElement: '.elementor-element-' + elemID,
                            triggerHook: scrollTrigger,
                            duration: settings.draw_speed ? settings.draw_speed.size * 1000 : "150%"
                        })

                    scene.setTween(timeLine).addTo(controller);

                } else {

                    if (settings.frames)
                        timeLine.duration(settings.frames);

                    if ('hover' === scrollAction) {
                        timeLine.pause();

                        $scope.find("svg").hover(
                            function () {
                                timeLine.play();
                            },
                            function () {
                                timeLine.pause();
                            });
                    }

                }

            }


        });

        elementorFrontend.elementsHandler.attachHandler('premium-svg-drawer', PremiumSVGDrawerHandler);

    });
})(jQuery);