(function ($) {

    var PremiumWeatherHandler = function ($scope, $) {

        var id = $scope.data('id'),
            isInHotspots = $('.elementor-element-' + id).closest('.premium-tooltipster-base').length > 0;

        if (isInHotspots) {
            $scope = $('.elementor-element-' + id);
        }

        if ( !elementorFrontend.isEditMode() ) {
            $scope.find('.premium-weather__outer-wrapper').css({ visibility: 'visible', opacity: 1 });
        }

        var settings = $scope.find('.premium-weather__outer-wrapper').data('pa-weather-settings');

        if ( ! settings ) {
            return;
        }

        var forecastHeight = $scope.find('.premium-weather__outer-wrapper').data('pa-height'),
            $forecastSlider = 'layout-2' === settings.layout ? $scope.find('.premium-weather__extra-outer-wrapper') : $scope.find('.premium-weather__hourly-forecast-wrapper'),
            forecastTabs = $scope.hasClass('premium-forecast-tabs-yes') ? true : false,
            dailyForecastCarousel = $scope.hasClass('premium-forecast-carousel-yes') ? true : false,
            dailyEqWidth = !forecastTabs && !dailyForecastCarousel & !$scope.hasClass('premium-daily-forecast__style-4') ? true : false;


        if ( $forecastSlider.length ) {
            $forecastSlider.addClass('premium-addons__v-hidden').slick(getSlickSettings(settings, settings.layout, false));
        }

        if ( dailyForecastCarousel ) {
            var dailyCarouselSettings = $scope.find('.premium-weather__outer-wrapper').data('pa-daily-settings');

            $scope.find('.premium-weather__forecast').slick( getSlickSettings(dailyCarouselSettings, '', true) );
        }

        // append the arrows here.
        if ( 'layout-2' !== settings.layout && 'vertical' === settings.hourlyLayout ) {
            var prevArrow = '<a type="button" data-role="none" class="carousel-arrow carousel-prev" aria-label="Next" role="button" style=""><i class="fas fa-chevron-left" aria-hidden="true"></i></a>',
                nextArrow = '<a type="button" data-role="none" class="carousel-arrow carousel-next" aria-label="Next" role="button" style=""><i class="fas fa-chevron-right" aria-hidden="true"></i></a>';

            $forecastSlider.append( prevArrow + nextArrow );

            $scope.find('a.carousel-arrow').on('click.paWeatherNav', function() {

                if ( $(this).hasClass('carousel-prev') ) {
                    $forecastSlider.slick('slickPrev');
                } else if ( $(this).hasClass('carousel-next') ) {
                    $forecastSlider.slick('slickNext');
                }

            });
        }

        if ( $forecastSlider.length ) {
            $forecastSlider.removeClass('premium-addons__v-hidden');
        }

        $(window).trigger('resize');

        if ( forecastTabs ) {

            var $tabs_headers = $scope.find('.premium-weather__tab-header');

            $tabs_headers.on('click.paWeatherTabs', function() {
                $scope.find('.current').removeClass('current');

                $(this).addClass('current');
                $scope.find( $(this).data('content-id') ).addClass('current');
            });
        }

        if ( '' !== forecastHeight ) {
            $scope.find('.premium-weather__forecast').slimScroll({
                color: '#00000033',
                height: forecastHeight,
            });

            $scope.find('.slimScrollDiv').css('overflow', '');
        }

        if ( dailyEqWidth ) {
            equalWidth( $scope );
        }

        function getSlickSettings(settings, widgetLayout, dailyForecast) {

            var slickSetting = {
                infinite: false,
                arrows: true,
                autoplay: false,
                draggable: true,
            };

            if ( ! dailyForecast && 'layout-2' !== widgetLayout && 'vertical' === settings.hourlyLayout ) {
                slickSetting.slidesToShow = 1;
                slickSetting.slidesToScroll = 1;
                slickSetting.rows = settings.slidesToShow;
                slickSetting.arrows = false;
                slickSetting.responsive = [
                    {
                        breakpoint: 1025,
                        settings: {
                            slidesToShow: 1,
                            slidesToScroll: 1,
                            rows: settings.slidesToShowTab || 1
                        }
                    },
                    {
                        breakpoint: 768,
                        settings: {
                            slidesToShow:  1,
                            slidesToScroll:  1,
                            rows: settings.slidesToShowMobile || 1
                        }
                    }
                ];
            } else {

                var prevArrow = '<a type="button" data-role="none" class="carousel-arrow carousel-prev" aria-label="Next" role="button" style=""><i class="fas fa-chevron-left" aria-hidden="true"></i></a>',
                    nextArrow = '<a type="button" data-role="none" class="carousel-arrow carousel-next" aria-label="Next" role="button" style=""><i class="fas fa-chevron-right" aria-hidden="true"></i></a>';

                slickSetting.slidesToScroll = settings.slidesToScroll || 1;
                slickSetting.slidesToShow = settings.slidesToShow;
                slickSetting.rows = 0;
                slickSetting.nextArrow = nextArrow;
                slickSetting.prevArrow = prevArrow;

                slickSetting.responsive = [
                    {
                        breakpoint: 1025,
                        settings: {
                            slidesToShow: settings.slidesToShowTab || 1,
                            slidesToScroll: settings.slidesToScrollTab || 1
                        }
                    },
                    {
                        breakpoint: 768,
                        settings: {
                            slidesToShow: settings.slidesToShowMobile || 1,
                            slidesToScroll: settings.slidesToScrollMobile || 1
                        }
                    }
                ];
            }

            return slickSetting;
        }

        function equalWidth($scope) {
            var width = 0,
                selector = '.premium-weather__forecast .premium-weather__forecast-item';

            $scope.find(selector).each(function (index, slot) {
                if (width < $(slot).outerWidth()) {
                    width = $(slot).outerWidth();
                }
            });

            $scope.find(selector).css('min-width', width);
        }
    };

    $(window).on('elementor/frontend/init', function () {
        elementorFrontend.hooks.addAction('frontend/element_ready/premium-weather.default',  PremiumWeatherHandler);
    });

})(jQuery);
