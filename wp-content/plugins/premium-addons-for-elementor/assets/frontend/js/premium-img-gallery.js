/****** Premium Media Grid Handler ******/
(function ($) {
    $(window).on('elementor/frontend/init', function () {

        var PremiumGridWidgetHandler = elementorModules.frontend.handlers.Base.extend({

            settings: {},

            getDefaultSettings: function () {
                return {
                    selectors: {
                        galleryElement: '.premium-gallery-container',
                        filters: '.premium-gallery-cats-container li',
                        gradientLayer: '.premium-gallery-gradient-layer',
                        loadMore: '.premium-gallery-load-more',
                        loadMoreDiv: '.premium-gallery-load-more div',
                        vidWrap: '.premium-gallery-video-wrap',
                    }
                }
            },

            getDefaultElements: function () {

                var selectors = this.getSettings('selectors'),
                    elements = {
                        $galleryElement: this.$element.find(selectors.galleryElement),
                        $filters: this.$element.find(selectors.filters),
                        $gradientLayer: this.$element.find(selectors.gradientLayer),
                        $vidWrap: this.$element.find(selectors.vidWrap)
                    };

                elements.$loadMore = elements.$galleryElement.parent().find(selectors.loadMore)
                elements.$loadMoreDiv = elements.$galleryElement.parent().find(selectors.loadMoreDiv)

                return elements;
            },

            bindEvents: function () {
                this.getGlobalSettings();
                this.run();

                var self = this;
                $(document).on('elementor/popup/show', function () {
                    self.run();
                });
            },

            getGlobalSettings: function () {
                var $galleryElement = this.elements.$galleryElement,
                    settings = $galleryElement.data('settings');

                this.settings = {
                    layout: settings.img_size,
                    loadMore: settings.load_more,
                    columnWidth: null,
                    filter: null,
                    isFilterClicked: false,
                    minimum: settings.minimum,
                    imageToShow: settings.click_images,
                    counter: settings.minimum,
                    ltrMode: settings.ltr_mode,
                    shuffle: settings.shuffle,
                    active_cat: settings.active_cat,
                    theme: settings.theme,
                    overlay: settings.overlay,
                    sort_by: settings.sort_by,
                    light_box: settings.light_box,
                    flag: settings.flag,
                    lightbox_type: settings.lightbox_type
                }
            },

            updateCounter: function () {

                if (this.settings.isFilterClicked) {

                    this.settings.counter = this.settings.minimum;

                    this.settings.isFilterClicked = false;

                } else {
                    this.settings.counter = this.settings.counter;
                }

                this.settings.counter = this.settings.counter + this.settings.imageToShow;
            },

            updateGrid: function (gradHeight, $isotopeGallery, $loadMoreDiv) {
                $.ajax({
                    url: this.appendItems(this.settings.counter, gradHeight, $isotopeGallery),
                    beforeSend: function () {
                        $loadMoreDiv.removeClass("premium-gallery-item-hidden");
                    },
                    success: function () {
                        $loadMoreDiv.addClass("premium-gallery-item-hidden");
                    }
                });
            },

            loadMore: function (gradHeight, $isotopeGallery) {

                var $galleryElement = this.elements.$galleryElement,
                    $loadMoreDiv = this.elements.$loadMoreDiv,
                    $loadMore = this.elements.$loadMore,
                    _this = this;

                $loadMoreDiv.addClass("premium-gallery-item-hidden");

                if ($galleryElement.find(".premium-gallery-item").length > this.settings.minimum) {

                    $loadMore.removeClass("premium-gallery-item-hidden");

                    $galleryElement.parent().on("click", ".premium-gallery-load-less", function () {
                        _this.settings.counter = _this.settings.counter - _this.settings.imageToShow;
                    });

                    $galleryElement.parent().on("click", ".premium-gallery-load-more-btn:not(.premium-gallery-load-less)", function () {
                        _this.updateCounter();
                        _this.updateGrid(gradHeight, $isotopeGallery, $loadMoreDiv);
                    });

                }

            },

            getItemsToHide: function (instance, imagesToShow) {
                var items = instance.filteredItems.slice(imagesToShow, instance
                    .filteredItems.length).map(function (item) {
                        return item.element;
                    });

                return items;
            },

            appendItems: function (imagesToShow, gradHeight, $isotopeGallery) {

                var $galleryElement = this.elements.$galleryElement,
                    $gradientLayer = this.elements.$gradientLayer,
                    instance = $galleryElement.data("isotope"),
                    itemsToHide = this.getItemsToHide(instance, imagesToShow);

                $gradientLayer.outerHeight(gradHeight);

                $galleryElement.find(".premium-gallery-item-hidden").removeClass("premium-gallery-item-hidden");

                $galleryElement.parent().find(".premium-gallery-load-more").removeClass("premium-gallery-item-hidden");

                $(itemsToHide).addClass("premium-gallery-item-hidden");

                $isotopeGallery.isotope("layout");

                if (0 == itemsToHide) {

                    $gradientLayer.addClass("premium-gallery-item-hidden");

                    $galleryElement.parent().find(".premium-gallery-load-more").addClass("premium-gallery-item-hidden");
                }
            },

            triggerFilerTabs: function (url) {
                var filterIndex = url.searchParams.get(this.settings.flag),
                    $filters = this.elements.$filters;

                if (filterIndex) {

                    var $targetFilter = $filters.eq(filterIndex).find("a");

                    $targetFilter.trigger('click');

                }
            },

            onReady: function ($isotopeGallery) {
                var _this = this;

                $isotopeGallery.isotope("layout");

                // $isotopeGallery.isotope({
                //     filter: _this.settings.active_cat
                // });

                var url = new URL(window.location.href);

                if (url)
                    _this.triggerFilerTabs(url);

                //Show the widget after making sure everything is ready.
                _this.$element.find(".category.active").trigger('click');
                _this.$element.find(".elementor-invisible").removeClass("elementor-invisible");

            },

            onResize: function ($isotopeGallery) {
                var _this = this;

                _this.setMetroLayout();

                $isotopeGallery.isotope({
                    itemSelector: ".premium-gallery-item",
                    masonry: {
                        columnWidth: _this.settings.columnWidth
                    },
                });

            },

            lightBoxDisabled: function () {
                var _this = this,
                    $vidWrap = this.elements.$vidWrap;

                $vidWrap.each(function (index, item) {
                    var type = $(item).data("type");

                    $(".pa-gallery-video-icon").keypress(function () {
                        $(this).closest(".premium-gallery-item").trigger('click');
                    });

                    $(item).closest(".premium-gallery-item").on("click", function () {

                        var $this = $(this);

                        $this.find(".pa-gallery-img-container").css("background", "#000");

                        $this.find("img, .pa-gallery-icons-caption-container, .pa-gallery-icons-wrapper").css("visibility", "hidden");

                        if ("style3" !== _this.settings.skin)
                            $this.find(".premium-gallery-caption").css("visibility", "hidden");

                        if ("hosted" !== type) {
                            _this.playVid($this);
                        } else {
                            _this.playHostedVid(item);
                        }
                    });
                });

            },

            playVid: function ($this) {
                var $iframeWrap = $this.find(".premium-gallery-iframe-wrap"),
                    src = $iframeWrap.data("src");

                src = src.replace("&mute", "&autoplay=1&mute");

                var $iframe = $("<iframe/>");

                $iframe.attr({
                    "src": src,
                    "frameborder": "0",
                    "allowfullscreen": "1",
                    "allow": "autoplay;encrypted-media;"
                });

                $iframeWrap.html($iframe);

                $iframe.css("visibility", "visible");
            },

            playHostedVid: function (item) {
                var $video = $(item).find("video");

                $video.get(0).play();
                $video.css("visibility", "visible");
            },

            run: function () {

                var $galleryElement = this.elements.$galleryElement,
                    $vidWrap = this.elements.$vidWrap,
                    $filters = this.elements.$filters,
                    _this = this;

                if ('metro' === this.settings.layout) {

                    this.setMetroLayout();

                    this.settings.layout = "masonry";

                    $(window).resize(function () { _this.onResize($isotopeGallery); });
                }

                var $isotopeGallery = $galleryElement.isotope(this.getIsoTopeSettings());

                $isotopeGallery.imagesLoaded().progress(function () {
                    $isotopeGallery.isotope("layout");
                });

                $(document).ready(function () { _this.onReady($isotopeGallery); });

                if (this.settings.loadMore) {

                    var $gradientLayer = this.elements.$gradientLayer,
                        gradHeight = null;

                    setTimeout(function () {
                        gradHeight = $gradientLayer.outerHeight();
                    }, 200);

                    this.loadMore(gradHeight, $isotopeGallery);
                }

                if ("yes" !== this.settings.light_box)
                    this.lightBoxDisabled();

                $filters.find("a").click(function (e) {
                    e.preventDefault();

                    _this.isFilterClicked = true;

                    $filters.find(".active").removeClass("active");

                    $(this).addClass("active");

                    _this.settings.filter = $(this).attr("data-filter");

                    $isotopeGallery.isotope({
                        filter: _this.settings.filter
                    });

                    if (_this.settings.shuffle) $isotopeGallery.isotope("shuffle");

                    if (_this.settings.loadMore) _this.appendItems(_this.settings.minimum, gradHeight, $isotopeGallery);

                    return false;
                });

                if ("default" === this.settings.lightbox_type)
                    this.$element.find(".premium-img-gallery a[data-rel^='prettyPhoto']").prettyPhoto(this.getPrettyPhotoSettings());
            },

            getPrettyPhotoSettings: function () {
                return {
                    theme: this.settings.theme,
                    hook: "data-rel",
                    opacity: 0.7,
                    show_title: false,
                    deeplinking: false,
                    overlay_gallery: this.settings.overlay,
                    custom_markup: "",
                    default_width: 900,
                    default_height: 506,
                    social_tools: ""
                }
            },

            getIsoTopeSettings: function () {
                return {
                    itemSelector: '.premium-gallery-item',
                    percentPosition: true,
                    animationOptions: {
                        duration: 750,
                        easing: 'linear'
                    },
                    filter: this.settings.active_cat,
                    layoutMode: this.settings.layout,
                    originLeft: this.settings.ltrMode,
                    masonry: {
                        columnWidth: this.settings.columnWidth
                    },
                    sortBy: this.settings.sort_by
                }
            },

            getRepeaterSettings: function () {
                return this.getElementSettings('premium_gallery_img_content');
            },

            setMetroLayout: function () {

                var $galleryElement = this.elements.$galleryElement,
                    gridWidth = $galleryElement.width(),
                    cellSize = Math.floor(gridWidth / 12),
                    deviceType = elementorFrontend.getCurrentDeviceMode(),
                    suffix = 'desktop' === deviceType ? '' : '_' + deviceType,
                    repeater = this.getRepeaterSettings();

                $galleryElement.find(".premium-gallery-item").each(function (index, item) { //should be added to selectors and elements

                    var cells = repeater[index]['premium_gallery_image_cell' + suffix].size,
                        vCells = repeater[index]['premium_gallery_image_vcell' + suffix].size;

                    if ("" === cells || undefined == cells) {
                        cells = repeater[index].premium_gallery_image_cell;
                    }

                    if ("" === vCells || undefined == vCells) {
                        vCells = repeater[index].premium_gallery_image_vcell;
                    }

                    $(item).css({
                        width: Math.ceil(cells * cellSize),
                        height: Math.ceil(vCells * cellSize)
                    });
                });

                this.settings.columnWidth = cellSize;
            }

        });

        elementorFrontend.elementsHandler.attachHandler('premium-img-gallery', PremiumGridWidgetHandler);

    });
})(jQuery);