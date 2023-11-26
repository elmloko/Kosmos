(function ($) {

    var PremiumWooProductsHandler = function ($scope, $) {
        var instance = null;

        instance = new premiumWooProducts($scope);
        instance.init();
    };

    window.premiumWooProducts = function ($scope) {

        var self = this,
            $elem = $scope.find(".premium-woocommerce"),
            skin = $scope.find('.premium-woocommerce').data('skin'),
            html = null,
            canLoadMore = true;

        //Check Quick View
        var isQuickView = $elem.data("quick-view");

        if ("yes" === isQuickView) {

            var widgetID = $scope.data("id"),
                $modal = $elem.siblings(".premium-woo-quick-view-" + widgetID),
                $qvModal = $modal.find('#premium-woo-quick-view-modal'),
                $contentWrap = $qvModal.find('#premium-woo-quick-view-content'),
                $wrapper = $qvModal.find('.premium-woo-content-main-wrapper'),
                $backWrap = $modal.find('.premium-woo-quick-view-back'),
                $qvLoader = $modal.find('.premium-woo-quick-view-loader');

        }

        self.init = function () {

            self.handleProductsCarousel();

            if ("yes" === isQuickView) {
                self.handleProductQuickView();
            }

            self.handleProductPagination();

            self.handleLoadMore();

            self.handleAddToCart();

            if ("grid_6" === skin) {
                self.handleGalleryImages();
            }

            if (["grid_7", "grid_11"].includes(skin)) {

                self.handleGalleryCarousel(skin);

                if ("grid_11" === skin) {
                    self.handleGalleryNav();
                }
            }

            if ($elem.hasClass("premium-woo-products-masonry")) {

                self.handleGridMasonry();

                $(window).on("resize", self.handleGridMasonry);

            }

            // place product title above thumbnail.
            if ($scope.hasClass('premium-woo-title-above-yes')) {

                self.handleTitlePos();
            }

        };

        self.getIsoTopeSettings = function () {
            return {
                itemSelector: "li.product",
                percentPosition: true,
                animationOptions: {
                    duration: 750,
                    easing: "linear",
                    queue: false
                },
                layoutMode: "masonry",
            }
        };

        self.handleTitlePos = function () {

            var hasTitle = $elem.find('.woocommerce-loop-product__title').length > 0 ? true : false,
                hasImg = $elem.find('.premium-woo-product-thumbnail .woocommerce-loop-product__link img').length > 0 ? true : false;

            if (!hasTitle || !hasImg) {
                return;
            }

            var $products = $elem.find('li.product');

            $products.each(function (index, product) {

                var $title = $(product).find('.woocommerce-loop-product__title').parent(),
                    $thumbnail = $(product).find('.premium-woo-product-thumbnail');

                $title.insertBefore($thumbnail);

            });

            $elem.find(".premium-woo-product__link").css("opacity", 1);

        };

        self.handleProductsCarousel = function () {

            var carousel = $elem.data("woo_carousel");

            if (!carousel)
                return;

            var $products = $elem.find('ul.products');

            carousel['customPaging'] = function () {
                return '<i class="fas fa-circle"></i>';
            };

            $products.on("init", function (event) {
                setTimeout(function () {
                    $elem.removeClass("premium-carousel-hidden");
                }, 100);

            });

            if ($products.find('li.product').length < carousel.slidesToShow) {
                $elem.removeClass("premium-carousel-hidden");
                $products.find('li.product').css('width', (100 / carousel.slidesToShow) + '%');
                return;
            }


            $products.slick(carousel);



        };

        self.handleGridMasonry = function () {

            var $products = $elem.find("ul.products");

            $products
                .imagesLoaded(function () { })
                .done(
                    function () {
                        $products.isotope({
                            itemSelector: "li.product",
                            percentPosition: true,
                            animationOptions: {
                                duration: 750,
                                easing: "linear",
                                queue: false
                            },
                            layoutMode: "masonry",
                            // masonry: {
                            //     columnWidth: cellSize
                            // }
                        });
                    });
        };

        self.handleProductQuickView = function () {
            $modal.appendTo(document.body);

            $elem.on('click', '.premium-woo-qv-btn, .premium-woo-qv-data', self.triggerQuickViewModal);

            window.addEventListener("resize", function () {
                self.updateQuickViewHeight();
            });

        };

        self.triggerQuickViewModal = function (event) {
            event.preventDefault();

            var $this = $(this),
                productID = $this.data('product-id');

            if (!$qvModal.hasClass('loading'))
                $qvModal.addClass('loading');

            if (!$backWrap.hasClass('premium-woo-quick-view-active'))
                $backWrap.addClass('premium-woo-quick-view-active');

            self.getProductByAjax(productID);

            self.addCloseEvents();
        };

        self.getProductByAjax = function (itemID) {

            $.ajax({
                url: PremiumWooSettings.ajaxurl,
                data: {
                    action: 'get_woo_product_qv',
                    product_id: itemID,
                    security: PremiumWooSettings.qv_nonce
                },
                dataType: 'html',
                type: 'GET',
                beforeSend: function () {

                    $qvLoader.append('<div class="premium-loading-feed"><div class="premium-loader"></div></div>');

                },
                success: function (data) {

                    $qvLoader.find('.premium-loading-feed').remove();

                    //Insert the product content in the quick view modal.
                    $contentWrap.html(data);
                    self.handleQuickViewModal();
                },
                error: function (err) {
                    console.log(err);
                }
            });

        };

        self.addCloseEvents = function () {

            var $closeBtn = $qvModal.find('#premium-woo-quick-view-close');

            $(document).keyup(function (e) {
                if (e.keyCode === 27)
                    self.closeModal();
            });

            $closeBtn.on('click', function (e) {
                e.preventDefault();
                self.closeModal();
            });

            $wrapper.on('click', function (e) {

                if (this === e.target)
                    self.closeModal();

            });
        };

        self.handleQuickViewModal = function () {

            $contentWrap.imagesLoaded(function () {
                self.handleQuickViewSlider();
            });

        };

        self.getBarWidth = function () {

            var div = $('<div style="width:50px;height:50px;overflow:hidden;position:absolute;top:-200px;left:-200px;"><div style="height:100px;"></div>');
            // Append our div, do our calculation and then remove it
            $('body').append(div);
            var w1 = $('div', div).innerWidth();
            div.css('overflow-y', 'scroll');
            var w2 = $('div', div).innerWidth();
            $(div).remove();

            return (w1 - w2);
        };

        self.handleQuickViewSlider = function () {

            var $productSlider = $qvModal.find('.premium-woo-qv-image-slider');

            if ($productSlider.find('li').length > 1) {

                $productSlider.flexslider({
                    animation: "slide",
                    start: function (slider) {
                        setTimeout(function () {
                            self.updateQuickViewHeight(true, true);
                        }, 300);
                    },
                });

            } else {
                setTimeout(function () {
                    self.updateQuickViewHeight(true);
                }, 300);
            }

            if (!$qvModal.hasClass('active')) {

                setTimeout(function () {
                    $qvModal.removeClass('loading').addClass('active');

                    var barWidth = self.getBarWidth();

                    $("html").css('margin-right', barWidth);
                    $("html").addClass('premium-woo-qv-opened');
                }, 350);

            }

        };

        self.updateQuickViewHeight = function (update_css, isCarousel) {
            var $quickView = $contentWrap,
                imgHeight = $quickView.find('.product .premium-woo-qv-image-slider').first().height(),
                summary = $quickView.find('.product .summary.entry-summary'),
                content = summary.css('content');

            if ('undefined' != typeof content && 544 == content.replace(/[^0-9]/g, '') && 0 != imgHeight && null !== imgHeight) {
                summary.css('height', imgHeight);
            } else {
                summary.css('height', '');
            }

            if (true === update_css)
                $qvModal.css('opacity', 1);

            //Make sure slider images have same height as summary.
            if (isCarousel)
                $quickView.find('.product .premium-woo-qv-image-slider img').height(summary.outerHeight());

        };

        self.closeModal = function () {

            $backWrap.removeClass('premium-woo-quick-view-active');

            $qvModal.removeClass('active').removeClass('loading');

            $('html').removeClass('premium-woo-qv-opened');

            $('html').css('margin-right', '');

            setTimeout(function () {
                $contentWrap.html('');
            }, 600);

        };

        self.handleAddToCart = function () {

            $elem
                .on('click', '.instock .premium-woo-cart-btn.product_type_simple', self.onAddCartBtnClick).on('premium_product_add_to_cart', self.handleAddCartBtnClick)
                .on('click', '.instock .premium-woo-atc-button .button.product_type_simple', self.onAddCartBtnClick).on('premium_product_add_to_cart', self.handleAddCartBtnClick);

        };

        self.onAddCartBtnClick = function (event) {

            var $this = $(this);

            var productID = $this.data('product_id'),
                quantity = 1;


            //If current product has no defined ID.
            if (!productID)
                return;

            if ($this.parent().data("variations"))
                return;

            if (!$this.data("added-to-cart")) {
                event.preventDefault();
            } else {
                return;
            }

            $this.removeClass('added').addClass('adding');

            if (!$this.hasClass('premium-woo-cart-btn')) {
                $this.append('<span class="premium-woo-cart-loader fas fa-cog"></span>')
            }

            $.ajax({
                url: PremiumWooSettings.ajaxurl,
                type: 'POST',
                data: {
                    action: 'premium_woo_add_cart_product',
                    nonce: PremiumWooSettings.cta_nonce,
                    product_id: productID,
                    quantity: quantity,
                },
                success: function () {
                    $(document.body).trigger('wc_fragment_refresh');
                    $elem.trigger('premium_product_add_to_cart', [$this]);

                    if ('grid_10' === skin || !$this.hasClass('premium-woo-cart-btn')) {
                        setTimeout(function () {

                            var viewCartTxt = $this.siblings('.added_to_cart').text();

                            if ('' == viewCartTxt)
                                viewCartTxt = 'View Cart';

                            $this.removeClass('add_to_cart_button').attr('href', PremiumWooSettings.woo_cart_url).text(viewCartTxt);

                            $this.attr('data-added-to-cart', true);
                        }, 200);

                    }

                }
            });

        };

        self.handleAddCartBtnClick = function (event, $btn) {

            if (!$btn)
                return;

            $btn.removeClass('adding').addClass('added');

        };

        self.handleGalleryImages = function () {

            $elem.on('click', '.premium-woo-product__gallery_image', function () {
                var $thisImg = $(this),
                    $closestThumb = $thisImg.closest(".premium-woo-product-thumbnail"),
                    imgSrc = $thisImg.attr('src');

                if ($closestThumb.find(".premium-woo-product__on_hover").length < 1) {
                    $closestThumb.find(".woocommerce-loop-product__link img").replaceWith($thisImg.clone(true));
                } else {
                    $closestThumb.find(".premium-woo-product__on_hover").attr('src', imgSrc);
                }

            });

        };

        self.handleGalleryNav = function () {

            $elem.on('click', '.premium-woo-product-gallery-images .premium-woo-product__gallery_image', function () {

                var imgParent = $(this).parentsUntil(".premium-woo-product-wrapper")[2],
                    slickContainer = $(imgParent).siblings('.premium-woo-product-thumbnail').find('.premium-woo-product-thumbnail-wrapper'),
                    imgIndex = $(this).index() + 1;

                slickContainer.slick('slickGoTo', imgIndex);
            });
        };

        self.handleGalleryCarousel = function (skin) {

            var products = $elem.find('.premium-woo-product-thumbnail-wrapper'),
                prevArrow = '<a type="button" data-role="none" class="carousel-arrow carousel-prev" aria-label="Previous" role="button" style=""><i class="fas fa-angle-left" aria-hidden="true"></i></a>',
                nextArrow = '<a type="button" data-role="none" class="carousel-arrow carousel-next" aria-label="Next" role="button" style=""><i class="fas fa-angle-right" aria-hidden="true"></i></a>',
                infinite = 'grid_11' === skin ? false : true,
                slickSettings = {
                    infinite: infinite,
                    slidesToShow: 1,
                    slidesToScroll: 1,
                    draggable: true,
                    autoplay: false,
                    rtl: elementorFrontend.config.is_rtl,
                };

            if ('grid_11' !== skin) {
                slickSettings.nextArrow = nextArrow;
                slickSettings.prevArrow = prevArrow;
            } else {
                slickSettings.arrows = false;
            }

            products.each(function (index, product) {
                $imgs = $(product).find('a').length;

                if ($imgs > 1) {
                    $(product).not('.slick-initialized').slick(slickSettings);
                }
            });
        }

        self.handleLoadMore = function () {

            var $loadMoreBtn = $elem.find(".premium-woo-load-more-btn"),
                page_number = 2,
                pageID = $elem.data('page-id');

            if ($loadMoreBtn.length < 1)
                return;

            $loadMoreBtn.on('click', function (e) {

                if (!canLoadMore)
                    return;

                canLoadMore = false;

                $elem.find('ul.products').after('<div class="premium-loading-feed"><div class="premium-loader"></div></div>');

                $loadMoreBtn.css("opacity", 0.3);

                $.ajax({
                    url: PremiumWooSettings.ajaxurl,
                    data: {
                        action: 'get_woo_products',
                        pageID: pageID,
                        elemID: $scope.data('id'),
                        category: $loadMoreBtn.data("tax"),
                        orderBy: $loadMoreBtn.data("order"),
                        skin: skin,
                        page_number: page_number,
                        nonce: PremiumWooSettings.products_nonce,
                    },
                    dataType: 'json',
                    type: 'POST',
                    success: function (data) {
                        html = data.data.html;

                        //If the number of coming products is 0, then remove the button.
                        var newProductsLength = $loadMoreBtn.data("products") - html.match(/<li/g).length;
                        if (newProductsLength < 1)
                            $loadMoreBtn.remove();

                        canLoadMore = true;

                        $elem.find('.premium-loading-feed').remove();
                        $loadMoreBtn.css("opacity", 1);

                        var $currentProducts = $elem.find('ul.products');

                        //Remove the wrapper <ul>
                        html = html.replace(html.substring(0, html.indexOf('>') + 1), '');
                        html = html.replace("</ul>", "");

                        $loadMoreBtn.find(".premium-woo-products-num").text("(" + newProductsLength + ")");

                        $loadMoreBtn.data("products", newProductsLength);

                        $currentProducts.append(html);

                        if ($elem.hasClass("premium-woo-products-masonry")) {

                            $currentProducts.isotope('reloadItems')

                            $currentProducts.isotope({
                                itemSelector: "li.product",
                                percentPosition: true,
                                layoutMode: "masonry",
                            });

                        }

                        // //Trigger carousel for products in the next pages.
                        if ("grid_7" === skin || "grid_11" === skin) {
                            self.handleGalleryCarousel(skin);
                        }

                        page_number++;

                    },
                    error: function (err) {
                        console.log(err);
                    }
                });


            });
        }

        self.handleProductPagination = function () {

            $elem.on('click', '.premium-woo-products-pagination a.page-numbers', function (e) {

                var $targetPage = $(this);

                if ($elem.hasClass('premium-woo-query-main'))
                    return;

                e.preventDefault();

                $elem.find('ul.products').after('<div class="premium-loading-feed"><div class="premium-loader"></div></div>');

                var pageID = $elem.data('page-id'),
                    currentPage = parseInt($elem.find('.page-numbers.current').html()),
                    page_number = 1;

                if ($targetPage.hasClass('next')) {
                    page_number = currentPage + 1;
                } else if ($targetPage.hasClass('prev')) {
                    page_number = currentPage - 1;
                } else {
                    page_number = $targetPage.html();
                }

                $.ajax({
                    url: PremiumWooSettings.ajaxurl,
                    data: {
                        action: 'get_woo_products',
                        pageID: pageID,
                        elemID: $scope.data('id'),
                        category: '',
                        skin: skin,
                        page_number: page_number,
                        nonce: PremiumWooSettings.products_nonce,
                    },
                    dataType: 'json',
                    type: 'POST',
                    success: function (data) {

                        $elem.find('.premium-loading-feed').remove();

                        $('html, body').animate({
                            scrollTop: (($scope.find('.premium-woocommerce').offset().top) - 100)
                        }, 'slow');

                        var $currentProducts = $elem.find('ul.products');

                        $currentProducts.replaceWith(data.data.html);

                        $elem.find('.premium-woo-products-pagination').replaceWith(data.data.pagination);

                        //Trigger carousel for products in the next pages.
                        if ("grid_7" === skin || "grid_11" === skin) {
                            self.handleGalleryCarousel(skin);
                        }

                        if ($elem.hasClass("premium-woo-products-masonry"))
                            self.handleGridMasonry();

                    },
                    error: function (err) {
                        console.log(err);
                    }
                });

            });

        };
    };


    //Elementor JS Hooks.
    $(window).on("elementor/frontend/init", function () {
        elementorFrontend.hooks.addAction("frontend/element_ready/premium-woo-products.grid-1", PremiumWooProductsHandler);
        elementorFrontend.hooks.addAction("frontend/element_ready/premium-woo-products.grid-2", PremiumWooProductsHandler);
        elementorFrontend.hooks.addAction("frontend/element_ready/premium-woo-products.grid-3", PremiumWooProductsHandler);
        elementorFrontend.hooks.addAction("frontend/element_ready/premium-woo-products.grid-4", PremiumWooProductsHandler);
        elementorFrontend.hooks.addAction("frontend/element_ready/premium-woo-products.grid-5", PremiumWooProductsHandler);
        elementorFrontend.hooks.addAction("frontend/element_ready/premium-woo-products.grid-6", PremiumWooProductsHandler);
        elementorFrontend.hooks.addAction("frontend/element_ready/premium-woo-products.grid-7", PremiumWooProductsHandler);
        elementorFrontend.hooks.addAction("frontend/element_ready/premium-woo-products.grid-8", PremiumWooProductsHandler);
        elementorFrontend.hooks.addAction("frontend/element_ready/premium-woo-products.grid-9", PremiumWooProductsHandler);
        elementorFrontend.hooks.addAction("frontend/element_ready/premium-woo-products.grid-10", PremiumWooProductsHandler);
        elementorFrontend.hooks.addAction("frontend/element_ready/premium-woo-products.grid-11", PremiumWooProductsHandler);
    });
})(jQuery);