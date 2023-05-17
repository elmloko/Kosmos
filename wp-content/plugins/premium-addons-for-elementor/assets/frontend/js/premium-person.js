(function ($) {
    $(window).on('elementor/frontend/init', function () {

        var PremiumTeamMembersHandler = elementorModules.frontend.handlers.Base.extend({

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
                        multiplePersons: '.multiple-persons',
                        person: '.premium-person-container',
                        personCarousel: '.premium-person-container.slick-active',
                        personImg: '.premium-person-image-container img',

                    }
                }
            },

            getDefaultElements: function () {

                var selectors = this.getSettings('selectors');

                return {
                    $multiplePersons: this.$element.find(selectors.multiplePersons),
                    $persons: this.$element.find(selectors.person),
                    $personImg: this.$element.find(selectors.personImg),
                }

            },
            bindEvents: function () {
                this.run();
            },
            getSlickSettings: function () {

                var settings = this.getElementSettings(),
                    rtl = this.elements.$multiplePersons.data("rtl"),
                    colsNumber = settings.persons_per_row,
                    colsTablet = settings.persons_per_row_tablet,
                    colsMobile = settings.persons_per_row_mobile;

                return Object.assign(this.getSettings('slick'), {

                    slidesToShow: parseInt(100 / colsNumber.substr(0, colsNumber.indexOf('%'))),
                    slidesToScroll: parseInt(100 / colsNumber.substr(0, colsNumber.indexOf('%'))),
                    responsive: [{
                        breakpoint: 1025,
                        settings: {
                            slidesToShow: parseInt(100 / colsTablet.substr(0, colsTablet.indexOf('%'))),
                            slidesToScroll: 1
                        }
                    },
                    {
                        breakpoint: 768,
                        settings: {
                            slidesToShow: parseInt(100 / colsMobile.substr(0, colsMobile.indexOf('%'))),
                            slidesToScroll: 1
                        }
                    }
                    ],
                    autoplay: settings.carousel_play,
                    rtl: rtl ? true : false,
                    autoplaySpeed: settings.speed || 5000,

                });


            },

            runEqualHeight: function () {

                var $persons = this.elements.$persons,
                    $personImg = this.elements.$personImg;

                var selectors = this.getSettings('selectors'),
                    carousel = this.getElementSettings('carousel'),
                    heights = new Array();

                if (carousel) {
                    $persons = this.$element.find(selectors.personCarousel);
                }

                $persons.each(function (index, person) {
                    $(person).imagesLoaded(function () { }).done(function () {

                        var imageHeight = $(person).find(selectors.personImg).outerHeight();

                        heights.push(imageHeight);
                    });
                });

                $persons.imagesLoaded(function () { }).done(function () {
                    var maxHeight = Math.max.apply(null, heights);
                    $personImg.css("height", maxHeight + "px");
                });

            },

            run: function () {

                var $multiplePersons = this.elements.$multiplePersons,
                    _this = this;

                if (!$multiplePersons.length) return;

                if ("yes" === $multiplePersons.data("persons-equal")) {
                    this.runEqualHeight();
                }

                var carousel = this.getElementSettings('carousel');

                if (carousel)
                    $multiplePersons.slick(this.getSlickSettings());

            }

        });

        elementorFrontend.elementsHandler.attachHandler('premium-addon-person', PremiumTeamMembersHandler);
    });
})(jQuery);