(function ($) {
    $(window).on('elementor/frontend/init', function () {

        var PremiumTestimonialsHandler = elementorModules.frontend.handlers.Base.extend({

            getDefaultSettings: function () {

                return {
                    slick: {
                        infinite: true,
                        rows: 0,
                        prevArrow: '<a type="button" data-role="none" class="carousel-arrow carousel-prev" aria-label="Next" role="button" style=""><i class="fas fa-angle-left" aria-hidden="true"></i></a>',
                        nextArrow: '<a type="button" data-role="none" class="carousel-arrow carousel-next" aria-label="Next" role="button" style=""><i class="fas fa-angle-right" aria-hidden="true"></i></a>',
                        draggable: true,
                        pauseOnHover: true,
                    },
                    selectors: {
                        multipleTestimonials: '.multiple-testimonials',
                        testimonials: '.premium-testimonial-container',
                        testimonialCarousel: '.premium-testimonial-container.slick-active',
                        testimonialImg: '.premium-testimonial-img-wrapper img',

                    }
                }
            },

            getDefaultElements: function () {

                var selectors = this.getSettings('selectors');

                return {
                    $multipleTestimonials: this.$element.find(selectors.multipleTestimonials),
                    $testimonials: this.$element.find(selectors.testimonials),
                    $testimonialImg: this.$element.find(selectors.testimonialImg),
                }

            },
            bindEvents: function () {
                this.run();
            },
            getSlickSettings: function () {

                var settings = this.getElementSettings(),
                    rtl = this.elements.$multipleTestimonials.data("rtl"),
                    colsNumber = 'skin4' !== settings.skin ? parseInt(100 / settings.testimonials_per_row.substr(0, settings.testimonials_per_row.indexOf('%'))) : 1,
                    colsTablet = 'skin4' !== settings.skin ? parseInt(100 / settings.testimonials_per_row_tablet.substr(0, settings.testimonials_per_row_tablet.indexOf('%'))) : 1,
                    colsMobile = 'skin4' !== settings.skin ? parseInt(100 / settings.testimonials_per_row_mobile.substr(0, settings.testimonials_per_row_mobile.indexOf('%'))) : 1;

                return Object.assign(this.getSettings('slick'), {

                    slide: '.premium-testimonial-container',
                    slidesToShow: colsNumber,
                    slidesToScroll: colsNumber,
                    responsive: [{
                        breakpoint: 1025,
                        settings: {
                            slidesToShow: colsTablet,
                            slidesToScroll: 1
                        }
                    },
                    {
                        breakpoint: 768,
                        settings: {
                            slidesToShow: colsMobile,
                            slidesToScroll: 1
                        }
                    }
                    ],
                    autoplay: settings.carousel_play,
                    autoplaySpeed: settings.speed || 5000,
                    rtl: rtl ? true : false,
                    speed: 500,
                    arrows: 'skin4' !== settings.skin ? true : false,
                    fade: 'skin4' === settings.skin ? true : false

                });


            },

            runEqualHeight: function () {

                var $testimonials = this.elements.$testimonials;

                var heights = new Array();

                $testimonials.each(function (index, testimonial) {

                    var height = $(testimonial).outerHeight();

                    heights.push(height);

                });

                var maxHeight = Math.max.apply(null, heights);
                $testimonials.find('.premium-testimonial-content-wrapper').css("height", maxHeight + "px");

            },

            run: function () {

                var $multipleTestimonials = this.elements.$multipleTestimonials;

                if (!$multipleTestimonials.length) return;

                if (this.$element.hasClass("premium-testimonial__equal-yes")) {
                    this.runEqualHeight();
                }

                var settings = this.getElementSettings(),
                    skin = settings.skin,
                    carousel = 'skin4' !== skin ? settings.carousel : true;

                if (carousel) {

                    // var $testimonials = this.elements.$testimonials;

                    // if ('skin1' === skin) {
                    //     var imgPosition = settings.img_position;

                    //     if ('absolute' === imgPosition) {
                    //         $testimonials.css('margin-top', '80px')
                    //     }
                    // } else if ('skin4' === skin) {
                    //     $testimonials.css('margin-bottom', '20px');
                    // }

                    var slickSettings = this.getSlickSettings();

                    if ('skin4' === skin)
                        slickSettings.infinite = false;

                    $multipleTestimonials.slick(slickSettings);

                    if ('skin4' === skin) {

                        var $skinCarousel = this.$element.find('.premium-testimonial__carousel');

                        $skinCarousel.slick({
                            slidesToScroll: 1,
                            slidesToShow: 3,
                            arrows: false,
                            centerMode: true,
                            centerPadding: 0,
                            infinite: false,
                            speed: 500,
                            autoplay: settings.carousel_play,
                            autoplaySpeed: settings.speed || 5000,
                        });

                        $multipleTestimonials.slick('slickGoTo', 1);
                        $skinCarousel.slick('slickGoTo', 1);

                        this.$element.find('.premium-testimonial__carousel-img').on('click', function () {

                            var slideIndex = $(this).data("index");

                            $multipleTestimonials.slick('slickGoTo', slideIndex);
                            $skinCarousel.slick('slickGoTo', slideIndex);
                        });

                        this.$element.hover(function () {
                            $skinCarousel.slick('slickPause');
                            $multipleTestimonials.slick('slickPause');
                        }, function () {
                            $skinCarousel.slick('slickPlay');
                            $multipleTestimonials.slick('slickPlay');
                        });
                    }

                }

            }

        });

        elementorFrontend.elementsHandler.attachHandler('premium-addon-testimonials', PremiumTestimonialsHandler);
    });
})(jQuery);