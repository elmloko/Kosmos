(function ($) {

    var PremiumPinterestHandler = function ($scope, $) {

        var $outerWrapper = $scope.find('.premium-pinterest-feed__outer-wrapper'),
            settings = $outerWrapper.data('pa-pinterest-settings');

        if (!settings) {
            return;
        }

        var isBoardQuery = 'boards/' === settings.query,
            loadBoardPins = isBoardQuery && 'pins' === settings.onClick,
            $pinsWrapper = $scope.find('.premium-pinterest-feed__pins-wrapper'),
            $boardWrapper = $scope.find('.premium-pinterest-feed__board-wrapper');

        if ( loadBoardPins ) {

            $boardWrapper.on('click.paLoadPins', function() {

                var id = $(this).data('board-id');
                $boardWrapper.hide();

                $('#premium-board-content-' + id).show();

                if ("masonry" === settings.layout) {
                    setTimeout(function()  {
                        $('#premium-board-content-' + id + ' .premium-pinterest-feed__pins-wrapper').isotope(getIsoTopeSettings());
                    }, 100);
                }
            });

            $scope.find('.premium-pinterest-feed__board-trigger').on('click.paHidePins', function() {
                $boardWrapper.show();
                $scope.find('.premium-pinterest-feed__content-wrapper').hide();
            });
        }

        if ( ! isBoardQuery ) {

            if ("masonry" === settings.layout && !settings.carousel) {

                $pinsWrapper.imagesLoaded(function () {
                    $pinsWrapper.isotope(getIsoTopeSettings());
                });
            }

            if (settings.loadMore && !settings.carousel) {

                window.paLoadMoreBookmark = $outerWrapper.data('pa-load-bookmark');
                window.paHiddenPins = $scope.find('.premium-pinterest-feed__pin-outer-wrapper.premium-display-none').length;

                $scope.find('.premium-pinterest-feed__load-more-btn').on('click.paLoadMorePins', function(e) {

                    var bookmark = window.paLoadMoreBookmark,
                        count = settings.loadMoreCount;

                    for ( var i = 0; i < count; i ++ ) {
                        $scope.find('.premium-pinterest-feed__pin-outer-wrapper').eq( bookmark + i ).show().addClass('premium-pin-shown');
                    }

                    window.paLoadMoreBookmark = bookmark + count;
                    window.paHiddenPins -= count;

                    if ( 0 >= window.paHiddenPins ) {
                        $scope.find('.premium-pinterest-feed__load-more-btn').remove();
                    }
                });
            }

            if ( settings.carousel ) {

                var carouselSettings = $outerWrapper.data('pa-carousel');
                $pinsWrapper.addClass('premium-addons__v-hidden').slick( getSlickSettings(carouselSettings) );

                $pinsWrapper.removeClass('premium-addons__v-hidden');
            }
        }

        if ( 'layout-2' === settings.pinLayout ) {
            $scope.find('.premium-pinterest-feed__pin-meta-wrapper').on('click.paPinTrigger', function(e){

                if ( e.target === this) {
                    $(this).siblings('.premium-pinterest-feed__overlay')[0].click();
                }
            });
        }

        // copy to clipbaord
        $scope.find('.premium-copy-link').on('click.paCopyLink', function() {
            $scope.find('.premium-pinterest-share-menu').css('visibility', 'hidden')

            var txt = $(this).data('pa-link');
            navigator.clipboard.writeText(txt);
        });

        $scope.find('.premium-pinterest-share-item:not(.premium-copy-link)').on('click.paShare', function() {

            var link = $(this).data('pa-link');
            window.open(link,'popup','width=600,height=600');
        });

        function getSlickSettings( settings ) {

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

        function getIsoTopeSettings(){

            return {
                itemSelector: ".premium-pinterest-feed__pin-outer-wrapper",
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
        elementorFrontend.hooks.addAction('frontend/element_ready/premium-pinterest-feed.default', PremiumPinterestHandler);
    });
 })(jQuery);