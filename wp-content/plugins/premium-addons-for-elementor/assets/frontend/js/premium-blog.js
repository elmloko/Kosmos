

(function ($) {
    $(window).on('elementor/frontend/init', function () {

        var PremiumBlogHandler = elementorModules.frontend.handlers.Base.extend({

            settings: {},

            getDefaultSettings: function () {
                return {
                    selectors: {
                        user: '.fa-user',
                        activeCat: '.category.active',
                        loading: '.premium-loading-feed',
                        blogElement: '.premium-blog-wrap',
                        blogFilterTabs: '.premium-blog-filter',
                        contentWrapper: '.premium-blog-content-wrapper',
                        blogPost: '.premium-blog-post-outer-container',
                        metaSeparators: '.premium-blog-meta-separator',
                        filterLinks: '.premium-blog-filters-container li a',
                        currentPage: '.premium-blog-pagination-container .page-numbers.current',
                        activeElememnt: '.premium-blog-filters-container li .active',
                    }
                }
            },

            getDefaultElements: function () {
                var selectors = this.getSettings('selectors'),
                    elements = {
                        $blogElement: this.$element.find(selectors.blogElement),
                        $blogFilterTabs: this.$element.find(selectors.blogFilterTabs),
                        $activeCat: this.$element.find(selectors.activeCat),
                        $filterLinks: this.$element.find(selectors.filterLinks),
                        $blogPost: this.$element.find(selectors.blogPost),
                        $contentWrapper: this.$element.find(selectors.contentWrapper)
                    };

                return elements;
            },

            bindEvents: function () {
                this.setLayoutSettings();
                this.removeMetaSeparators();
                this.run();
            },

            setLayoutSettings: function () {

                var settings = this.getElementSettings(),
                    $blogPost = this.elements.$blogPost;

                var layoutSettings = {
                    pageNumber: 1,
                    isLoaded: true,
                    count: 2,
                    equalHeight: settings.force_height,
                    layout: settings.premium_blog_layout,
                    carousel: 'yes' === settings.premium_blog_carousel ? true : false,
                    infinite: 'yes' === settings.premium_blog_infinite_scroll ? true : false,
                    scrollAfter: 'yes' === settings.scroll_to_offset ? true : false,
                    grid: 'yes' === settings.premium_blog_grid ? true : false,
                    total: $blogPost.data('total'),
                };


                if (layoutSettings.carousel) {

                    layoutSettings.slidesToScroll = settings.slides_to_scroll;
                    layoutSettings.spacing = parseInt(settings.premium_blog_carousel_spacing);
                    layoutSettings.autoPlay = 'yes' === settings.premium_blog_carousel_play ? true : false;
                    layoutSettings.arrows = 'yes' === settings.premium_blog_carousel_arrows ? true : false;
                    layoutSettings.fade = 'yes' === settings.premium_blog_carousel_fade ? true : false;
                    layoutSettings.center = 'yes' === settings.premium_blog_carousel_center ? true : false;
                    layoutSettings.dots = 'yes' === settings.premium_blog_carousel_dots ? true : false;
                    layoutSettings.speed = '' !== settings.carousel_speed ? parseInt(settings.carousel_speed) : 300;
                    layoutSettings.autoplaySpeed = '' !== settings.premium_blog_carousel_autoplay_speed ? parseInt(settings.premium_blog_carousel_autoplay_speed) : 5000;

                }

                this.settings = layoutSettings;

            },

            removeMetaSeparators: function () {

                var selectors = this.getSettings('selectors'),
                    $blogPost = this.$element.find(selectors.blogPost);

                var $metaSeparators = $blogPost.first().find(selectors.metaSeparators),
                    $user = $blogPost.find(selectors.user);

                if (1 === $metaSeparators.length) {
                    //If two meta only are enabled. One of them is author meta.
                    if (!$user.length) {
                        $blogPost.find(selectors.metaSeparators).remove();
                    }

                } else {
                    if (!$user.length) {
                        $blogPost.each(function (index, post) {
                            $(post).find(selectors.metaSeparators).first().remove();
                        });
                    }
                }

            },
            run: function () {

                var _this = this,
                    $blogElement = this.elements.$blogElement,
                    $activeCategory = this.elements.$activeCat.data('filter'),
                    $filterTabs = this.elements.$blogFilterTabs.length,
                    pagination = $blogElement.data("pagination");

                this.settings.activeCategory = $activeCategory;
                this.settings.filterTabs = $filterTabs;



                if (this.settings.filterTabs) {
                    this.filterTabs();
                }

                if (!this.settings.filterTabs || "*" === this.settings.activeCategory) {
                    if ("masonry" === this.settings.layout && !this.settings.carousel) {
                        $blogElement.imagesLoaded(function () {
                            $blogElement.isotope(_this.getIsoTopeSettings());
                        });
                    }
                } else {
                    //If `All` categories not exist, then we need to get posts through AJAX.
                    // this.getPostsByAjax(false);
                }

                if (this.settings.carousel) {
                    $blogElement.slick(this.getSlickSettings());

                    $blogElement.removeClass("premium-carousel-hidden");
                }

                if ("even" === this.settings.layout && this.settings.equalHeight) {
                    $blogElement.imagesLoaded(function () {
                        _this.forceEqualHeight();
                    });
                }

                if (pagination) {
                    this.paginate();
                }

                if (this.settings.infinite && $blogElement.is(":visible")) {
                    this.getInfiniteScrollPosts();
                }

            },

            paginate: function () {
                var _this = this,
                    $scope = this.$element,
                    selectors = this.getSettings('selectors');

                $scope.on('click', '.premium-blog-pagination-container .page-numbers', function (e) {

                    e.preventDefault();

                    if ($(this).hasClass("current")) return;

                    var currentPage = parseInt($scope.find(selectors.currentPage).html());

                    if ($(this).hasClass('next')) {
                        _this.settings.pageNumber = currentPage + 1;
                    } else if ($(this).hasClass('prev')) {
                        _this.settings.pageNumber = currentPage - 1;
                    } else {
                        _this.settings.pageNumber = $(this).html();
                    }

                    _this.getPostsByAjax(_this.settings.scrollAfter);

                })
            },

            forceEqualHeight: function () {
                var heights = new Array(),
                    contentWrapper = this.getSettings('selectors').contentWrapper,
                    $blogWrapper = this.$element.find(contentWrapper);

                $blogWrapper.each(function (index, post) {

                    var height = $(post).outerHeight();

                    heights.push(height);
                });

                var maxHeight = Math.max.apply(null, heights);

                $blogWrapper.css("height", maxHeight + "px");
            },

            getSlickSettings: function () {

                var settings = this.settings,
                    slickCols = settings.grid ? this.getSlickCols() : null,
                    cols = settings.grid ? slickCols.cols : 1,
                    colsTablet = settings.grid ? slickCols.colsTablet : 1,
                    colsMobile = settings.grid ? slickCols.colsMobile : 1,
                    prevArrow = settings.arrows ? '<a type="button" data-role="none" class="carousel-arrow carousel-prev" aria-label="Previous" role="button" style=""><i class="fas fa-angle-left" aria-hidden="true"></i></a>' : '',
                    nextArrow = settings.arrows ? '<a type="button" data-role="none" class="carousel-arrow carousel-next" aria-label="Next" role="button" style=""><i class="fas fa-angle-right" aria-hidden="true"></i></a>' : '';

                return {
                    infinite: true,
                    slidesToShow: cols,
                    slidesToScroll: settings.slidesToScroll || cols,
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
                    autoplay: settings.autoPlay,
                    rows: 0,
                    speed: settings.speed,
                    autoplaySpeed: settings.autoplaySpeed,
                    nextArrow: nextArrow,
                    prevArrow: prevArrow,
                    fade: settings.fade,
                    centerMode: settings.center,
                    centerPadding: settings.spacing + "px",
                    draggable: true,
                    dots: settings.dots,
                    customPaging: function () {
                        return '<i class="fas fa-circle"></i>';
                    }
                }

            },

            getSlickCols: function () {
                var slickCols = this.getElementSettings(),
                    cols = slickCols.premium_blog_columns_number,
                    colsTablet = slickCols.premium_blog_columns_number_tablet,
                    colsMobile = slickCols.premium_blog_columns_number_mobile;

                return {
                    cols: parseInt(100 / cols.substr(0, cols.indexOf('%'))),
                    colsTablet: parseInt(100 / colsTablet.substr(0, colsTablet.indexOf('%'))),
                    colsMobile: parseInt(100 / colsMobile.substr(0, colsMobile.indexOf('%'))),
                }

            },

            getIsoTopeSettings: function () {
                return {
                    itemSelector: ".premium-blog-post-outer-container",
                    percentPosition: true,
                    filter: this.settings.activeCategory,
                    animationOptions: {
                        duration: 750,
                        easing: "linear",
                        queue: false
                    }
                }
            },

            filterTabs: function () {

                var _this = this,
                    selectors = this.getSettings('selectors'),
                    $filterLinks = this.elements.$filterLinks;

                $filterLinks.click(function (e) {

                    e.preventDefault();

                    _this.$element.find(selectors.activeElememnt).removeClass("active");

                    $(this).addClass("active");

                    //Get clicked tab slug
                    _this.settings.activeCategory = $(this).attr("data-filter");

                    _this.settings.pageNumber = 1;

                    if (_this.settings.infinite) {
                        _this.getPostsByAjax(false);
                        _this.settings.count = 2;
                        _this.getInfiniteScrollPosts();
                    } else {
                        //Make sure to reset pagination before sending our AJAX request
                        _this.getPostsByAjax(_this.settings.scrollAfter);
                    }

                });
            },

            getPostsByAjax: function (shouldScroll) {

                //If filter tabs is not enabled, then always set category to all.
                if ('undefined' === typeof this.settings.activeCategory) {
                    this.settings.activeCategory = '*';
                }

                var _this = this,
                    $blogElement = this.elements.$blogElement,
                    selectors = this.getSettings('selectors');

                $.ajax({
                    url: PremiumSettings.ajaxurl,
                    dataType: 'json',
                    type: 'POST',
                    data: {
                        action: 'pa_get_posts',
                        page_id: $blogElement.data('page'),
                        widget_id: _this.$element.data('id'),
                        page_number: _this.settings.pageNumber,
                        category: _this.settings.activeCategory,
                        nonce: PremiumSettings.nonce,
                    },
                    beforeSend: function () {

                        $blogElement.append('<div class="premium-loading-feed"><div class="premium-loader"></div></div>');

                        if (shouldScroll) {
                            $('html, body').animate({
                                scrollTop: (($blogElement.offset().top) - 50)
                            }, 'slow');
                        }

                    },
                    success: function (res) {
                        if (!res.data)
                            return;

                        $blogElement.find(selectors.loading).remove();

                        var posts = res.data.posts,
                            paging = res.data.paging;

                        if (_this.settings.infinite) {
                            _this.settings.isLoaded = true;
                            if (_this.settings.filterTabs && _this.settings.pageNumber === 1) {
                                $blogElement.html(posts);
                            } else {
                                $blogElement.append(posts);
                            }
                        } else {
                            //Render the new markup into the widget
                            $blogElement.html(posts);

                            _this.$element.find(".premium-blog-footer").html(paging);
                        }

                        _this.removeMetaSeparators();

                        //Make sure grid option is enabled.
                        if (_this.settings.layout) {
                            if ("even" === _this.settings.layout) {
                                if (_this.settings.equalHeight)
                                    _this.forceEqualHeight();

                            } else {

                                $blogElement.imagesLoaded(function () {

                                    $blogElement.isotope('reloadItems');
                                    $blogElement.isotope({
                                        itemSelector: ".premium-blog-post-outer-container",
                                        animate: false
                                    });
                                });
                            }
                        }

                    },
                    error: function (err) {
                        console.log(err);
                    }

                });
            },

            getInfiniteScrollPosts: function () {
                var windowHeight = jQuery(window).outerHeight() / 1.25,
                    _this = this;

                $(window).scroll(function () {

                    if (_this.settings.filterTabs) {
                        $blogPost = _this.elements.$blogElement.find(".premium-blog-post-outer-container");
                        _this.settings.total = $blogPost.data('total');
                    }

                    if (_this.settings.count <= _this.settings.total) {
                        if (($(window).scrollTop() + windowHeight) >= (_this.$element.find('.premium-blog-post-outer-container:last').offset().top)) {
                            if (true == _this.settings.isLoaded) {
                                _this.settings.pageNumber = _this.settings.count;
                                _this.getPostsByAjax(false);
                                _this.settings.count++;
                                _this.settings.isLoaded = false;
                            }

                        }
                    }
                });
            },

        });

        elementorFrontend.elementsHandler.attachHandler('premium-addon-blog', PremiumBlogHandler);
    });

})(jQuery);