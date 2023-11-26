
(function ($) {
    $(window).on('elementor/frontend/init', function () {

        var PremiumBulletListHandler = elementorModules.frontend.handlers.Base.extend({


            getDefaultSettings: function () {

                return {
                    selectors: {
                        listItems: '.premium-bullet-list-box',
                        items: '.premium-bullet-list-content',
                    }
                }

            },

            getDefaultElements: function () {

                var selectors = this.getSettings('selectors'),
                    elements = {
                        $listItems: this.$element.find(selectors.listItems),
                        $items: this.$element.find(selectors.items)
                    };

                return elements;
            },

            bindEvents: function () {
                this.run();

                this.addRandomBadges();

                var self = this;
                if (!this.$element.is(':visible') && this.$element.closest('.premium-mega-nav-item').length > 0)
                    this.$element.closest('.premium-mega-nav-item').find('.premium-menu-link').on('click', function () {
                        self.addRandomBadges();
                    });

            },

            run: function () {

                var $listItems = this.elements.$listItems,
                    $items = this.elements.$items;

                $items.each(function (index, item) {

                    if ($listItems.data("list-animation") && " " != $listItems.data("list-animation")) {
                        elementorFrontend.waypoint($(item), function () {

                            var element = $(item),
                                delay = element.data('delay');

                            setTimeout(function () {
                                element.next('.premium-bullet-list-divider , .premium-bullet-list-divider-inline').css("opacity", "1");
                                element.next('.premium-bullet-list-divider-inline , .premium-bullet-list-divider').addClass("animated " + $listItems.data("list-animation"));

                                element.css("opacity", "1").addClass("animated " + $listItems.data("list-animation"));
                            }, delay);

                        });
                    }

                });

            },

            addRandomBadges: function () {
                var settings = this.getElementSettings();

                if (settings.rbadges_repeater.length < 1)
                    return;

                var $currentList = $('.elementor-element-' + this.$element.data('id'));

                if (!$currentList.is(':visible') || this.$element.hasClass('randomb-applied'))
                    return;

                var randomBadges = settings.rbadges_repeater;

                randomBadges.forEach(function (badge, index) {

                    if ('' != badge.rbadge_selector) {

                        var notBadgedItems = $(badge.rbadge_selector).find('.premium-bullet-list-text').filter(':not(:has(+ .premium-bullet-list-badge))');

                        var badgeText = '<div class="premium-bullet-list-badge elementor-repeater-item-' + badge._id + '"><span>' + badge.badge_title + '</span></div>';

                        var numOfApplies = Math.floor(Math.random() * (badge.rbadge_max - badge.rbadge_min + 1)) + badge.rbadge_min;

                        // Get a random number of elements from the list.
                        for (var i = 0; i < numOfApplies; i++) {

                            // notBadgedItems = $(badge.rbadge_selector).find('.premium-bullet-list-text').filter(':not(:has(+ .premium-bullet-list-badge))');

                            var randomIndex = Math.floor(Math.random() * notBadgedItems.length),
                                wasBadgedBefore = $(notBadgedItems[randomIndex]).siblings('.premium-bullet-list-badge').length > 0;


                            if (!wasBadgedBefore) {
                                $(notBadgedItems[randomIndex]).after(badgeText);
                            }


                        }

                    }
                })

                this.$element.addClass('randomb-applied');
            }



        });

        elementorFrontend.elementsHandler.attachHandler('premium-icon-list', PremiumBulletListHandler);
    });

})(jQuery);


