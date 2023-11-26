

(function ($) {
    $(window).on('elementor/frontend/init', function () {

        var PremiumNotifications = elementorModules.frontend.handlers.Base.extend({

            getDefaultSettings: function () {

                return {
                    selectors: {
                        user: '.fa-user',
                        outerWrap: '.pa-recent-notification',
                        iconWrap: '.pa-rec-not-icon-wrap',
                        postsContainer: '.pa-rec-posts-container',
                        number: '.pa-rec-not-number',
                        closeButton: '.pa-rec-posts-close',
                        metaSeparators: '.premium-blog-meta-separator',
                    },
                    isHidden: true
                }

            },

            getDefaultElements: function () {

                var selectors = this.getSettings('selectors');

                return {
                    $outerWrap: this.$element.find(selectors.outerWrap),
                    $iconWrap: this.$element.find(selectors.iconWrap),
                    $postsContainer: this.$element.find(selectors.postsContainer),
                    $number: this.$element.find(selectors.number),
                    $closeButton: this.$element.find(selectors.closeButton)
                }

            },

            bindEvents: function () {
                this.removeMetaSeparators();
                this.run();
            },

            removeMetaSeparators: function () {

                var selectors = this.getSettings('selectors'),
                    $blogPost = this.$element.find('.premium-blog-post-outer-container');

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

            addIconForNoPosts: function () {

                var settings = this.getElementSettings(),
                    iconWithNoPosts = settings.add_icon_with_no_posts,
                    iconType = settings.icon_type,
                    $iconWrap = this.elements.$iconWrap;

                if ('yes' === iconWithNoPosts) {

                    if (iconType === 'image') {
                        $($iconWrap[0].children[1]).css('display', 'block');
                        $($iconWrap[0].children[0]).css('display', 'none');
                    } else {
                        $('.premium-notification-icon').css('display', 'none');
                        $('.premium-icon-with-no-post').css('display', 'block');
                    }

                }
            },

            run: function () {

                var $iconWrap = this.elements.$iconWrap,
                    $postsContainer = this.elements.$postsContainer,
                    $closeButton = this.elements.$closeButton,
                    $number = this.elements.$number,
                    settings = this.getElementSettings(),
                    isHidden = this.getSettings('isHidden'),
                    _this = this,
                    widgetID = this.$element.data('id'),
                    computedStyle = getComputedStyle(this.$element[0]);

                var boxWidth = computedStyle.getPropertyValue('--pa-recent-posts-width'),
                    direction = settings.posts_box_position,
                    cookies = settings.cookies;

                if ('yes' === settings.add_icon_with_no_posts && $number.length === 0) {

                    $($iconWrap[0].children[1]).css('display', 'none');

                    this.addIconForNoPosts();
                }

                this.hideAnimationElements();

                if (!boxWidth)
                    boxWidth = '30vw'

                $postsContainer.css(direction, '-' + boxWidth);

                $postsContainer.removeClass('elementor-invisible');

                $iconWrap.on('click', function () {

                    if (isHidden) {

                        _this.addIconForNoPosts();

                        if ('yes' === cookies) {

                            var isSecured = (document.location.protocol === 'https:') ? 'secure' : '',
                                recentPosts = _this.elements.$outerWrap.data('recent');

                            document.cookie = "cookieName=paRecentPosts" + widgetID + ";expires=Thu, 01 Jan 1970 00:00:00 UTC;";
                            document.cookie = "paRecentPosts" + widgetID + "=" + recentPosts + ";SameSite=Strict;" + isSecured;
                        }

                        //If other box is open, close it.
                        var currentBoxID = widgetID;
                        if (window.OpenedpostsBox && currentBoxID !== window.OpenedpostsBox)
                            _this.$element.trigger('click');

                        $number.remove();

                        $('.pa-rec-posts-overlay').css('display', 'block').animate({
                            opacity: 1
                        }, 500);

                        $postsContainer.animate({
                            [direction]: 0
                        }, 500, "swing", function () {

                            _this.triggerAnimations();

                            _this.setSettings({
                                isHidden: false
                            });

                            window.OpenedpostsBox = widgetID;

                        });

                    }

                });

                $closeButton.on('click', function () {
                    _this.hideBox(boxWidth, direction);
                })

                //When click outside, close all boxes.
                $("body").on("click", function (event) {

                    var postsContent = ".pa-rec-posts-container, .pa-rec-posts-container *, .pa-rec-not-icon-wrap, .pa-rec-not-icon-wrap *, .premium-tabs-nav-list-item";

                    if (!$(event.target).is($(postsContent))) {
                        _this.hideBox(boxWidth, direction);
                    }

                });


            },

            hideBox: function (width, dir) {

                var $postsContainer = this.elements.$postsContainer,
                    _this = this;

                $('.pa-rec-posts-overlay').animate({
                    opacity: 0
                }, 500, function () {
                    $('.pa-rec-posts-overlay').css('display', 'none')
                });

                $postsContainer.animate({
                    [dir]: '-' + width
                }, 500, "swing", function () {

                    _this.setSettings({
                        isHidden: true
                    });

                    _this.removeAnimations();
                });

            },

            triggerAnimations: function () {

                var $headerTitle = this.$element.find('.pa-rec-title'),
                    $postsBox = this.$element.find('.pa-rec-posts-body'),
                    settings = this.getElementSettings();


                if (settings.header_animation) {
                    $headerTitle.removeClass('elementor-invisible').addClass('animated ' + settings.header_animation).attr('data-e-animation', settings.header_animation);
                }

                if (settings.posts_animation && $postsBox.find('.premium-blog-post-outer-container').length > 0) {

                    if ('yes' !== settings.posts_animation_individial) {

                        $postsBox.removeClass('elementor-invisible').addClass('animated ' + settings.posts_animation).attr('data-e-animation', settings.posts_animation);

                    } else {

                        $postsBox = $postsBox.find('.premium-blog-post-outer-container');

                        var timeOut = 250;
                        $postsBox.map(function (index, elem) {

                            setTimeout(function () {
                                $(elem).removeClass('elementor-invisible').addClass('animated ' + settings.posts_animation).attr('data-e-animation', settings.posts_animation);
                            }, 0 == index ? 0 : timeOut);

                        })

                    }


                }


            },

            removeAnimations: function () {

                var $postsContainer = this.elements.$postsContainer;

                $postsContainer.find(".animated").each(function (index, elem) {

                    var animation = $(elem).data('e-animation');

                    $(elem).removeClass("animated " + animation).addClass("elementor-invisible");
                });

            },

            hideAnimationElements: function () {

                var $headerTitle = this.$element.find('.pa-rec-title'),
                    $postsBox = this.$element.find('.pa-rec-posts-body'),
                    settings = this.getElementSettings();

                if ('yes' === settings.posts_animation_individial) {
                    $postsBox = $postsBox.find('.premium-blog-post-outer-container');
                }

                if (settings.header_animation) {
                    $headerTitle.addClass('elementor-invisible');
                }

                if (settings.posts_animation && this.$element.find('.premium-blog-post-outer-container').length > 0) {
                    $postsBox.addClass('elementor-invisible');
                }


            }

        });

        elementorFrontend.elementsHandler.attachHandler('premium-notifications', PremiumNotifications);
    });

})(jQuery);
