(function ($) {
    /****** Premium Vertical Scroll Handler ******/
    var PremiumVerticalScrollHandler = function ($scope, $) {

        var deviceType = elementorFrontend.getCurrentDeviceMode();

        var hiddenClass = "elementor-hidden-" + deviceType;

        if ("mobile" === deviceType)
            hiddenClass = "elementor-hidden-phone";

        if ($scope.closest("section.elementor-element").hasClass(hiddenClass)) {
            return
        }

        var $vScrollElem = $scope.find(".premium-vscroll-wrap"),
            instance = null,
            vScrollSettings = $vScrollElem.data("settings");

        vScrollSettings.deviceType = deviceType;

        instance = new premiumVerticalScroll($vScrollElem, vScrollSettings);
        instance.init();

    };

    window.premiumVerticalScroll = function ($selector, settings) {
        var self = this,
            $window = $(window),
            currentDevice = elementorFrontend.getCurrentDeviceMode(),
            isTouch = !['desktop', 'widescreen', 'laptop'].includes(currentDevice),
            $instance = $selector,
            checkTemps = $selector.find(".premium-vscroll-sections-wrap").length,
            $htmlBody = $("html, body"),
            $itemsList = $(".premium-vscroll-dot-item", $instance),
            $menuItems = $(".premium-vscroll-nav-item", $instance),
            defaultSettings = {
                speed: 700,
                offset: 0,
                fullSection: true
            },
            settings = $.extend({}, defaultSettings, settings),
            sections = {},
            currentSection = null,
            isScrolling = false,
            inScope = true,
            scrollings = [],
            touchStartY = 0,
            touchEndY = 0;

        //Extend jQuery default easing.
        jQuery.extend(jQuery.easing, {
            easeInOutCirc: function (x, t, b, c, d) {
                if ((t /= d / 2) < 1)
                    return (-c / 2) * (Math.sqrt(1 - t * t) - 1) + b;
                return (c / 2) * (Math.sqrt(1 - (t -= 2) * t) + 1) + b;
            }
        });

        self.init = function () {

            if (settings.fullTouch || (!isTouch && settings.fullSection)) {

                if (settings.fullCheckOverflow) {

                    self.setSectionsOverflow();
                }
            }

            self.setSectionsData();

            self.vscrollEffects();

            $itemsList.on("click.premiumVerticalScroll", self.onNavDotChange);
            $menuItems.on("click.premiumVerticalScroll", self.onNavDotChange);

            $itemsList.on("mouseenter.premiumVerticalScroll", self.onNavDotEnter);

            $itemsList.on("mouseleave.premiumVerticalScroll", self.onNavDotLeave);

            if ("desktop" === settings.deviceType) {
                $window.on("scroll.premiumVerticalScroll", self.onWheel);
            }

            $window.on("resize.premiumVerticalScroll orientationchange.premiumVerticalScroll", self.debounce(50, self.onResize));

            $(document).ready(function () {

                self.setSectionsData();

                //Handle Full Section Scroll.
                if (settings.fullTouch || (!isTouch && settings.fullSection))
                    self.sectionsOverflowRefresh();

                self.checkCurrentActive();

            });

            self.keyboardHandler();

            self.scrollHandler();

            if (settings.fullSection) {

                self.fullSectionHandler();
            }

            if (settings.animation) {
                $instance.find(".premium-vscroll-dots").removeClass("elementor-invisible").addClass("animated " + settings.animation + " animated-" + settings.duration);
            }


        };

        self.checkCurrentActive = function () {

            var firstSection = Object.keys(sections)[0];

            //Get first section offset
            var firstSectionOffset = sections[firstSection].offset;

            //If page scroll is lower than first section offset, then set current active to 1
            if (firstSectionOffset >= $window.scrollTop() && firstSectionOffset - $window.scrollTop() < 200) {
                currentSection = 1;
                $itemsList.removeClass("active");
                $($itemsList[0]).addClass("active");
            }

            //If current active section is defined, then show the dots
            if (currentSection)
                $instance.find(".premium-vscroll-dots").removeClass("premium-vscroll-dots-hide");

        };

        /**
         * Sets the section's overflow scroll.
         * checks if the section's content height is greater
         * than the window height and init the section scroller.
         */
        self.setSectionsOverflow = function () {

            $itemsList.each(function () {

                var $this = $(this),
                    sectionId = $this.data("menuanchor"),
                    animeType = $instance.find('.premium-vscroll-sections-wrap').data('animation'),
                    $section = $("#" + sectionId),
                    height = animeType ? $section.find('> div').outerHeight() : $section.outerHeight();

                if (height > $window.outerHeight() && height - $window.outerHeight() >= 50) {

                    $section.find(checkTemps ? ".elementor" : ".elementor-container").first().wrapInner("<div id='scroller-" + sectionId + "'></div>");

                    var isSafari = 'mobile' === elementorFrontend.getCurrentDeviceMode() && /^((?!chrome|android).)*safari/i.test(navigator.userAgent),
                        slimHeight = isSafari ? $window.outerHeight() + 100 + 'px' : $window.outerHeight();

                    $("#scroller-" + sectionId).slimScroll({
                        height: slimHeight,
                        railVisible: false,
                        touchScrollStep: 60
                    });

                    var iScrollInstance = new IScroll("#scroller-" + sectionId, {
                        mouseWheel: true,
                        scrollbars: true,
                        hideScrollbars: true,
                        fadeScrollbars: false,
                        disableMouse: true,
                        interactiveScrollbars: false
                    });

                    $("#scroller-" + sectionId).data('iscrollInstance', iScrollInstance);

                    setTimeout(function () {
                        iScrollInstance.refresh();
                    }, 1500);
                }
            });
        };

        self.sectionsOverflowRefresh = function () {

            $itemsList.each(function () {
                var $this = $(this),
                    sectionId = $this.data("menuanchor");

                var $section = $("#scroller-" + sectionId);

                var scroller = $section.data('iscrollInstance');

                if (scroller) {
                    scroller.refresh();
                }

            });

        };

        self.setSectionsData = function () {

            $itemsList.each(function () {
                var $this = $(this),
                    sectionId = $this.data("menuanchor"),
                    $section = $("#" + sectionId),
                    height = $section.outerHeight();

                //Make sure that section exists in the DOM
                if ($section[0]) {

                    sections[sectionId] = {
                        selector: $section,
                        offset: Math.round($section.offset().top),
                        height: height
                    };
                }
            });
        };

        self.fullSectionHandler = function () {

            var vSection = document.getElementById($instance.attr("id"));

            if (!isTouch || !settings.fullTouch) {

                if (checkTemps) {

                    document.addEventListener ?
                        vSection.addEventListener("wheel", self.onWheel, {
                            passive: false
                        }) :
                        vSection.attachEvent("onmousewheel", self.onWheel);

                } else {

                    document.addEventListener ?
                        document.addEventListener("wheel", self.onWheel, {
                            passive: false
                        }) :
                        document.attachEvent("onmousewheel", self.onWheel);

                }

            } else {
                document.addEventListener("touchstart", self.onTouchStart);
                document.addEventListener("touchmove", self.onTouchMove, {
                    passive: false
                });

            }

        };

        self.scrollHandler = function () {

            var index = 0;

            for (var section in sections) {

                var $section = sections[section].selector;

                elementorFrontend.waypoint(
                    $section,
                    function () {

                        var $this = $(this),
                            sectionId = $this.attr("id");

                        if (!isScrolling) {

                            currentSection = sectionId;

                            $itemsList.removeClass("active");
                            $menuItems.removeClass("active");

                            $("[data-menuanchor=" + sectionId + "]", $instance).addClass("active");

                        }
                    }, {
                    offset: 0 !== index ? "0%" : "-1%",
                    triggerOnce: false
                }
                );
                index++;
            }

        };

        self.keyboardHandler = function () {
            $(document).keydown(function (event) {
                if (38 == event.keyCode) {
                    self.onKeyUp(event, "up");
                }

                if (40 == event.keyCode) {
                    self.onKeyUp(event, "down");
                }
            });
        };

        self.isScrolled = function (sectionID, direction) {

            var $section = $("#scroller-" + sectionID);

            var scroller = $section.data('iscrollInstance');

            if (scroller) {
                if ('down' === direction) {
                    return (0 - scroller.y) + $section.scrollTop() + 1 + $section.innerHeight() >= $section[0].scrollHeight;
                } else if ('up' === direction) {
                    return scroller.y >= 0 && !$section.scrollTop();
                }

            } else {
                return true;
            }

        };

        self.getEventsPage = function (e) {

            var events = [];

            events.y = (typeof e.pageY !== 'undefined' && (e.pageY || e.pageX) ? e.pageY : e.touches[0].pageY);
            events.x = (typeof e.pageX !== 'undefined' && (e.pageY || e.pageX) ? e.pageX : e.touches[0].pageX);

            if (isTouch && typeof e.touches !== 'undefined') {
                events.y = e.touches[0].pageY;
                events.x = e.touches[0].pageX;
            }

            return events;

        };

        self.onTouchStart = function (e) {

            //Prevent page scroll if scrolled down below the last of our sections.
            inScope = true;

            var touchEvents = self.getEventsPage(e);
            touchStartY = touchEvents.y;

        };

        self.onTouchMove = function (e) {

            if (inScope) {
                self.preventDefault(e);
            }

            if (isScrolling) {
                self.preventDefault(e);
                return false;
            }

            var touchEvents = self.getEventsPage(e);

            touchEndY = touchEvents.y;

            var $target = $(e.target),
                sectionSelector = checkTemps ? ".premium-vscroll-temp" : ".elementor-top-section, .e-con",
                $section = $target.parents(sectionSelector).length > 1 ? $target.parents(sectionSelector).last() : $target.closest(sectionSelector),
                sectionId = $section.attr("id"),
                newSectionId = false,
                prevSectionId = false,
                nextSectionId = false,
                direction = false,
                windowScrollTop = $window.scrollTop();

            $(".premium-vscroll-tooltip").hide();

            if (self.beforeCheck()) {
                sectionId = self.getFirstSection(sections);
            }

            if (self.afterCheck()) {
                sectionId = self.getLastSection(sections);
            }

            var curTime = new Date().getTime();

            if (scrollings.length > 149) {
                scrollings.shift();
            }

            //keeping record of the previous scrollings
            scrollings.push(Math.abs(touchEndY));

            //time difference between the last scroll and the current one
            var timeDiff = curTime - prevTime;
            prevTime = curTime;

            //haven't they scrolled in a while?
            //(enough to be consider a different scrolling action to scroll another section)
            if (timeDiff > 200) {
                //emptying the array, we dont care about old scrollings for our averages
                scrollings = [];
            }

            if (touchStartY > touchEndY) {
                direction = 'down';
            } else if (touchEndY > touchStartY) {
                direction = 'up';
            }

            if (sectionId && sections.hasOwnProperty(sectionId)) {

                prevSectionId = self.checkPrevSection(sections, sectionId);
                nextSectionId = self.checkNextSection(sections, sectionId);

                if ("up" === direction) {

                    if (!nextSectionId && sections[sectionId].offset < windowScrollTop) {
                        newSectionId = sectionId;
                    } else {
                        newSectionId = prevSectionId;
                    }
                }

                if ("down" === direction) {

                    if (!prevSectionId && sections[sectionId].offset - settings.offset > windowScrollTop + 5) {
                        newSectionId = sectionId;
                    } else {
                        newSectionId = nextSectionId;
                    }
                }

                var averageEnd = self.getAverage(scrollings, 10);
                var averageMiddle = self.getAverage(scrollings, 70);
                var isAccelerating = averageEnd >= averageMiddle;

                if (newSectionId) {
                    inScope = true;
                    $instance.find(".premium-vscroll-dots, .premium-vscroll-nav-menu").removeClass("premium-vscroll-dots-hide");

                    if (!self.isScrolled(sectionId, direction)) {
                        return;
                    }

                    if (isAccelerating && !isScrolling) {
                        self.onAnchorChange(newSectionId);
                    }

                } else {

                    //Make sure the scroll is done.
                    if (averageEnd <= 5) {
                        inScope = false;
                    }

                    var $lastselector = checkTemps ? $instance : $("#" + sectionId);

                    if ("down" === direction) {

                        if ($lastselector.offset().top + $lastselector.innerHeight() - $(document).scrollTop() > 600) {

                            $instance.find(".premium-vscroll-dots, .premium-vscroll-nav-menu").addClass("premium-vscroll-dots-hide");

                        }

                    } else if ("up" === direction) {

                        if ($lastselector.offset().top - $(document).scrollTop() > 200) {

                            $instance.find(".premium-vscroll-dots, .premium-vscroll-nav-menu").addClass("premium-vscroll-dots-hide");

                        }

                    }
                }

            } else {
                inScope = false;
            }

        };

        self.scrollStop = function () {
            $htmlBody.stop(true);
        };

        self.checkNextSection = function (object, key) {
            var keys = Object.keys(object),
                idIndex = keys.indexOf(key),
                nextIndex = (idIndex += 1);

            if (nextIndex >= keys.length) {
                return false;
            }

            var nextKey = keys[nextIndex];

            return nextKey;
        };

        self.checkPrevSection = function (object, key) {
            var keys = Object.keys(object),
                idIndex = keys.indexOf(key),
                prevIndex = (idIndex -= 1);

            if (0 > idIndex) {
                return false;
            }

            var prevKey = keys[prevIndex];

            return prevKey;
        };

        self.debounce = function (threshold, callback) {
            var timeout;

            return function debounced($event) {
                function delayed() {
                    callback.call(this, $event);
                    timeout = null;
                }

                if (timeout) {
                    clearTimeout(timeout);
                }

                timeout = setTimeout(delayed, threshold);
            };
        };

        self.visible = function (selector, partial, hidden) {
            var s = selector.get(0),
                vpHeight = $window.outerHeight(),
                clientSize =
                    hidden === true ? s.offsetWidth * s.offsetHeight : true;
            if (typeof s.getBoundingClientRect === "function") {
                var rec = s.getBoundingClientRect();
                var tViz = rec.top >= 0 && rec.top < vpHeight,
                    bViz = rec.bottom > 0 && rec.bottom <= vpHeight,
                    vVisible = partial ? tViz || bViz : tViz && bViz,
                    vVisible =
                        rec.top < 0 && rec.bottom > vpHeight ? true : vVisible;
                return clientSize && vVisible;
            } else {
                var viewTop = 0,
                    viewBottom = viewTop + vpHeight,
                    position = $window.position(),
                    _top = position.top,
                    _bottom = _top + $window.height(),
                    compareTop = partial === true ? _bottom : _top,
                    compareBottom = partial === true ? _top : _bottom;
                return (
                    !!clientSize &&
                    (compareBottom <= viewBottom && compareTop >= viewTop)
                );
            }
        };

        self.onNavDotEnter = function () {
            var $this = $(this),
                index = $this.data("index");

            if (settings.tooltips) {
                // make sure only one tool tip is showing.
                $(".premium-vscroll-tooltip").remove();

                $(
                    '<div class="premium-vscroll-tooltip"><span>' +
                    settings.dotsText[index] +
                    "</span></div>"
                )
                    .hide()
                    .appendTo($this)
                    .fadeIn(200);
            }
        };

        self.onNavDotLeave = function () {
            $(".premium-vscroll-tooltip").fadeOut(200, function () {
                $(this).remove();
            });
        };

        self.onNavDotChange = function (event) {
            var $this = $(this),
                index = $this.index(),
                sectionId = $this.data("menuanchor"),
                offset = null;

            if (!sections.hasOwnProperty(sectionId)) {
                return false;
            }

            offset = sections[sectionId].offset - settings.offset;

            if (offset < 0)
                offset = sections[sectionId].offset;

            if (!isScrolling) {
                isScrolling = true;

                currentSection = sectionId;
                $menuItems.removeClass("active");
                $itemsList.removeClass("active");

                if ($this.hasClass("premium-vscroll-nav-item")) {
                    $($itemsList[index]).addClass("active");
                } else {
                    $($menuItems[index]).addClass("active");
                }

                $this.addClass("active");

                $htmlBody
                    .stop()
                    .clearQueue()
                    .animate({
                        scrollTop: offset
                    },
                        settings.speed,
                        "easeInOutCirc",
                        function () {
                            isScrolling = false;
                        }
                    );
            }
        };

        self.preventDefault = function (event) {

            if (event.preventDefault) {

                event.preventDefault();

            } else {

                event.returnValue = false;

            }
        };

        self.onAnchorChange = function (sectionId) {

            var $this = $("[data-menuanchor=" + sectionId + "]", $instance),
                offset = null;

            if (!sections.hasOwnProperty(sectionId)) {
                return false;
            }

            offset = sections[sectionId].offset - settings.offset;

            if (offset < 0)
                offset = sections[sectionId].offset;

            if (!isScrolling) {
                isScrolling = true;

                if (settings.addToHistory) {
                    window.history.pushState(null, null, "#" + sectionId);
                }

                currentSection = sectionId;

                $itemsList.removeClass("active");
                $menuItems.removeClass("active");

                $this.addClass("active");

                $htmlBody.animate({ scrollTop: offset }, settings.speed, "easeInOutCirc");

                setTimeout(function () {
                    isScrolling = false;
                }, settings.speed < 700 ? 700 : settings.speed);
            }
        };

        self.onKeyUp = function (event, direction) {

            //If keyboard is triggered before scroll
            if (currentSection === 1) {
                currentSection = $itemsList.eq(0).data("menuanchor");
            }

            var direction = direction || "up",
                nextItem = $(".premium-vscroll-dot-item[data-menuanchor=" + currentSection + "]", $instance).next(),
                prevItem = $(".premium-vscroll-dot-item[data-menuanchor=" + currentSection + "]", $instance).prev();

            event.preventDefault();

            if (isScrolling) {
                return false;
            }

            var $vTarget = self.visible($instance, true, false),
                dotIndex = $(".premium-vscroll-dot-item.active").index(),
                animationType = $instance.find('.premium-vscroll-sections-wrap').data('animation');

            if ("up" === direction) {
                if (prevItem[0]) {
                    prevItem.trigger("click.premiumVerticalScroll");
                    if (dotIndex === $itemsList.length - 1 && !$vTarget) {
                        prevItem = $(".premium-vscroll-dot-item[data-menuanchor=" + currentSection + "]", $instance);
                    } else if (dotIndex === $itemsList.length - 1 && ($instance.offset().top + $instance.innerHeight() - $(document).scrollTop() < 600)) {
                        prevItem = $(".premium-vscroll-dot-item[data-menuanchor=" + currentSection + "]", $instance);
                    } else {
                        $instance.find('.premium-vscroll-sections-wrap[data-animation=' + animationType + '] .premium-vscroll-temp:last-of-type>div').removeClass("premium-vscroll-parallax-last");
                        $instance.find('.premium-vscroll-sections-wrap[data-animation=' + animationType + '] .premium-vscroll-temp>div').removeClass("premium-vscroll-parallax-position");
                        // prevItem = $(".premium-vscroll-dot-item[data-menuanchor=" + currentSection + "]", $instance).prev(),
                    }

                }
            } else {
                if (nextItem[0]) {
                    nextItem.trigger("click.premiumVerticalScroll");
                    if ($instance.offset().top - $(document).scrollTop() > 200) {
                        nextItem = $(".premium-vscroll-dot-item[data-menuanchor=" + currentSection + "]", $instance);
                    }
                    // else {
                    //     // nextItem = $(".premium-vscroll-dot-item[data-menuanchor=" + currentSection + "]", $instance).next(),
                    // }
                }
            }
        };

        self.getFirstSection = function (object) {
            return Object.keys(object)[0];
        }

        self.getLastSection = function (object) {
            return Object.keys(object)[Object.keys(object).length - 1];
        }

        function getScrollData(e) {
            e = e || window.event;

            var t = e.wheelDelta || -e.deltaY || -e.detail;

            return t;
        }

        var prevTime = new Date().getTime();

        //Used to unset position CSS property for vertical scroll sections becuase it causes position issue for the content below the widget.
        function parallaxLastSection() {
            var $target = $(event.target),
                sectionSelector = checkTemps ? ".premium-vscroll-temp" : ".elementor-top-section, .e-con",
                $section = $target.parents(sectionSelector).length > 1 ? $target.parents(sectionSelector).last() : $target.closest(sectionSelector),
                sectionId = $section.attr("id"),
                $lastselector = checkTemps ? $instance : $("#" + sectionId),
                animationType = $instance.find('.premium-vscroll-sections-wrap').data('animation');

            if (animationType) {

                if ($lastselector.offset().top + $lastselector.innerHeight() - $(document).scrollTop() + settings.offset < $window.outerHeight()) {
                    $instance.find('.premium-vscroll-sections-wrap[data-animation=' + animationType + '] .premium-vscroll-temp:last-of-type > div').addClass("premium-vscroll-parallax-last");
                    $instance.find('.premium-vscroll-sections-wrap[data-animation=' + animationType + '] .premium-vscroll-temp>div').addClass("premium-vscroll-parallax-position");

                } else {
                    $instance.find('.premium-vscroll-sections-wrap[data-animation=' + animationType + '] .premium-vscroll-temp:last-of-type > div').removeClass("premium-vscroll-parallax-last");
                    $instance.find('.premium-vscroll-sections-wrap[data-animation=' + animationType + '] .premium-vscroll-temp > div').removeClass("premium-vscroll-parallax-position");
                }
            }
        }

        self.onWheel = function (event) {

            if (inScope && !isTouch) {
                self.preventDefault(event);
            }

            var $target = $(event.target),
                sectionSelector = checkTemps ? ".premium-vscroll-temp" : ".elementor-top-section, .e-con",
                $section = $target.parents(sectionSelector).length > 1 ? $target.parents(sectionSelector).last() : $target.closest(sectionSelector),
                sectionId = $section.attr("id"),
                $vTarget = self.visible($instance, true, false),
                newSectionId = false,
                prevSectionId = false,
                nextSectionId = false,
                scrollData = getScrollData(event),
                delta = Math.max(-1, Math.min(1, scrollData)),
                direction = 0 > delta ? "down" : "up",
                windowScrollTop = $window.scrollTop(),
                dotIndex = $(".premium-vscroll-dot-item.active").index();

            if ($target.closest('.premium_maps_map_height').length > 0) {

                var $closestMapSettings = $target.closest('.premium_maps_map_height').data('settings');

                if ($closestMapSettings.scrollwheel)
                    return;
            }


            var curTime = new Date().getTime();

            if (scrollings.length > 149) {
                scrollings.shift();
            }

            //keeping record of the previous scrollings
            scrollings.push(Math.abs(scrollData));

            //time difference between the last scroll and the current one
            var timeDiff = curTime - prevTime;
            prevTime = curTime;

            //haven't they scrolled in a while?
            //(enough to be consider a different scrolling action to scroll another section)
            if (timeDiff > 200) {
                //emptying the array, we dont care about old scrollings for our averages
                scrollings = [];
            }

            parallaxLastSection();

            if (isTouch) {

                $(".premium-vscroll-tooltip").hide();

                if (dotIndex === $itemsList.length - 1 && !$vTarget) {
                    $instance.find(".premium-vscroll-dots, .premium-vscroll-nav-menu").addClass("premium-vscroll-dots-hide");
                } else if (dotIndex === 0 && !$vTarget) {
                    if ($instance.offset().top - $(document).scrollTop() > 200) {
                        $instance.find(".premium-vscroll-dots, .premium-vscroll-nav-menu").addClass("premium-vscroll-dots-hide");
                    }
                } else {
                    $instance.find(".premium-vscroll-dots, .premium-vscroll-nav-menu").removeClass("premium-vscroll-dots-hide");
                }
            }

            if (self.beforeCheck()) {
                sectionId = self.getFirstSection(sections);
            }

            if (self.afterCheck()) {
                sectionId = self.getLastSection(sections);
            }

            if (sectionId && sections.hasOwnProperty(sectionId)) {

                prevSectionId = self.checkPrevSection(sections, sectionId);
                nextSectionId = self.checkNextSection(sections, sectionId);

                if ("up" === direction) {
                    if (!nextSectionId && sections[sectionId].offset < windowScrollTop) {
                        newSectionId = sectionId;
                    } else {
                        newSectionId = prevSectionId;
                    }
                } else {
                    if (!prevSectionId && sections[sectionId].offset - settings.offset > windowScrollTop + 5) {
                        newSectionId = sectionId;
                    } else {
                        newSectionId = nextSectionId;
                    }
                }

                var averageEnd = self.getAverage(scrollings, 10);
                var averageMiddle = self.getAverage(scrollings, 70);
                var isAccelerating = averageEnd >= averageMiddle;

                if (newSectionId) {
                    inScope = true;
                    if (!self.isScrolled(sectionId, direction) && !isTouch) {
                        return;
                    }

                    $instance.find(".premium-vscroll-dots, .premium-vscroll-nav-menu").removeClass("premium-vscroll-dots-hide");

                    var iScrollInstance = $("#scroller-" + newSectionId).data('iscrollInstance');

                    if (isAccelerating && !isScrolling) {
                        self.onAnchorChange(newSectionId);
                        //Prevent overflow sections from scrolling.
                        // if (iScrollInstance)
                        // iScrollInstance.disable();
                    } else {
                        //Enable overflow sections scroll after 2s.
                        // if (iScrollInstance) {
                        //     setTimeout(function () {
                        //         iScrollInstance.enable();
                        //     }, settings.speed);
                        // }
                    }

                } else {
                    //Make sure the scroll is done.
                    if (averageEnd <= 5) {
                        inScope = false;
                    }

                    var $lastselector = checkTemps ? $instance : $("#" + sectionId);

                    if ("down" === direction) {
                        if (
                            $lastselector.offset().top +
                            $lastselector.innerHeight() -
                            $(document).scrollTop() >
                            600
                        ) {
                            $instance.find(".premium-vscroll-dots, .premium-vscroll-nav-menu").addClass("premium-vscroll-dots-hide");
                        }
                    } else if ("up" === direction) {
                        $instance.find(".premium-vscroll-dots, .premium-vscroll-nav-menu").addClass("premium-vscroll-dots-hide");
                    }
                }
            }
        };

        self.beforeCheck = function () {
            var windowScrollTop = $window.scrollTop(),
                firstSectionId = self.getFirstSection(sections),
                offset = sections[firstSectionId].offset,
                topBorder = windowScrollTop + $window.outerHeight(),
                visible = self.visible($instance, true, false);

            if (topBorder > offset) {
                return false;
            } else if (visible) {
                return true;
            }
            return false;
        }

        self.afterCheck = function () {
            var windowScrollTop = $window.scrollTop(),
                lastSectionId = self.getLastSection(sections),
                bottomBorder =
                    sections[lastSectionId].offset +
                    sections[lastSectionId].height,
                visible = self.visible($instance, true, false);

            if (windowScrollTop < bottomBorder) {
                return false;
            } else if (visible) {
                return true;
            }

            return false;
        }

        self.onResize = function () {
            self.setSectionsData();
            self.sectionsOverflowRefresh();
        };

        self.getAverage = function (elements, number) {
            var sum = 0;

            //taking `number` elements from the end to make the average, if there are not enought, 1
            var lastElements = elements.slice(Math.max(elements.length - number, 1));

            for (var i = 0; i < lastElements.length; i++) {
                sum = sum + lastElements[i];
            }

            return Math.ceil(sum / number);
        };

        self.vscrollEffects = function () {

            var animationType = $instance.find('.premium-vscroll-sections-wrap').data('animation');

            if (animationType) {

                var sectionsAvailable = $instance.find('.premium-vscroll-temp');

                //bind the animation to the window scroll event, arrows click and keyboard.
                scrollAnimation();

                $(window).on('scroll', scrollAnimation);

                function scrollAnimation() {

                    //We don't want scroll functions to be triggered if behance project lightbox is opened.
                    if ($(".eb-project-overlay").length > 0)
                        return;

                    //normal scroll - use requestAnimationFrame (if defined) to optimize performance.
                    (!window.requestAnimationFrame) ? animateSection() : window.requestAnimationFrame(animateSection);
                }

                function animateSection() {

                    var scrollTop = $(window).scrollTop(),
                        windowHeight = $(window).height();

                    sectionsAvailable.each(function () {
                        var actualBlock = $(this),
                            offset = scrollTop - actualBlock.offset().top;

                        // according to animation type and window scroll, define animation parameters.
                        var animationValues = setSectionAnimation(offset, windowHeight, animationType);

                        transformSection(actualBlock.children('div'), animationValues[0], animationValues[1], animationValues[2], animationValues[3]);

                        (offset >= 0 && offset < windowHeight) ? actualBlock.addClass('visible') : actualBlock.removeClass('visible');
                    });
                }

                function transformSection(element, translateY, rotateXValue, opacityValue, scaleValue) {

                    element.css({
                        transform: 'translateY(' + translateY + 'vh) rotateX(' + rotateXValue + ') scale(' + scaleValue + ')',
                        // rotateX: rotateXValue,
                        opacity: opacityValue
                    });
                }

                function setSectionAnimation(sectionOffset, windowHeight, animationName) {

                    // select section animation - normal scroll
                    var translateY = 100,
                        rotateX = '0deg',
                        opacity = 1,
                        scale = 1,
                        boxShadowBlur = 0;

                    if (sectionOffset >= -windowHeight && sectionOffset <= 0) {
                        // section entering the viewport.
                        translateY = (-sectionOffset) * 100 / windowHeight;

                        if ('rotate' === animationName) {
                            translateY = 0;
                            rotateX = '0deg';
                        } else if ('scaleDown' === animationName) {
                            scale = 1;
                            opacity = 1;
                        }

                    } else if (sectionOffset > 0 && sectionOffset <= windowHeight) {
                        //section leaving the viewport - still has the '.visible' class.
                        if ('rotate' === animationName) {
                            opacity = (1 - (sectionOffset / windowHeight)).toFixed(5);
                            rotateX = sectionOffset * 100 / windowHeight + 'deg';
                            translateY = 0;
                        } else if ('scaleDown' === animationName) {
                            scale = (1 - (sectionOffset * 0.3 / windowHeight)).toFixed(5);
                            opacity = (1 - (sectionOffset / windowHeight)).toFixed(5);
                            translateY = 0;
                            boxShadowBlur = 40 * (sectionOffset / windowHeight);

                        } else { //parallax
                            translateY = (-sectionOffset) * 50 / windowHeight;
                        }

                    } else if (sectionOffset < -windowHeight) {
                        //section not yet visible.
                        translateY = 100;

                        if ('scaleDown' === animationName) {
                            scale = 1;
                            opacity = 1;
                        }

                    } else {
                        //section not visible anymore.
                        if ('rotate' === animationName) {
                            translateY = 0;
                            rotateX = '90deg';

                        } else if ('scaleDown' === animationName) {
                            scale = 0;
                            opacity = 0.7;
                            translateY = 0;

                        } else {
                            translateY = -50;
                        }
                    }

                    return [translateY, rotateX, opacity, scale];
                }

            }
        }
    };

    $(window).on("elementor/frontend/init", function () {
        elementorFrontend.hooks.addAction(
            "frontend/element_ready/premium-vscroll.default",
            PremiumVerticalScrollHandler
        );
    });
})(jQuery);