(function ($) {

    var PremiumCountDownHandler = function ($scope, $) {

        var $countDownElement = $scope.find(".premium-countdown"),
            settings = $countDownElement.data("settings"),
            id = $scope.data('id'),
            label1 = settings.label1,
            label2 = settings.label2,
            newLabe1 = label1.split(","),
            newLabel2 = label2.split(","),
            timerType = settings.timerType,
            until = 'evergreen' === timerType ? settings.until.date.replace(/ /g, "T") : settings.until,
            layout = '',
            map = {
                y: { index: 0, oldVal: '' },
                o: { index: 1, oldVal: '' },
                w: { index: 2, oldVal: '' },
                d: { index: 3, oldVal: '' },
                h: { index: 4, oldVal: '' },
                m: { index: 5, oldVal: '' },
                s: { index: 6, oldVal: '' }
            };

        if ($countDownElement.find('#countdown-' + id).hasClass('premium-countdown-flip')) {
            settings.format.split('').forEach(function (unit) {
                var lowercased = unit.toLowerCase();

                layout += '<div class="premium-countdown-block premium-countdown-' + lowercased + '"><div class="pre_time-mid"> <div class="premium-countdown-figure"><span class="top">{' + lowercased + 'nn}</span><span class="top-back"><span>{' + lowercased + 'nn}</span></span><span class="bottom">{' + lowercased + 'nn}</span><span class="bottom-back"><span>{' + lowercased + 'nn}</span></span></div><span class="premium-countdown-label">{' + lowercased + 'l}</span></div><span class="countdown_separator">{sep}</span></div>';
            });
        }

        $countDownElement.find('#countdown-' + id).countdown({
            layout: layout,
            labels: newLabel2,
            labels1: newLabe1,
            until: new Date(until),
            format: settings.format,
            padZeroes: true,
            timeSeparator: settings.separator,
            onTick: function (periods) {

                equalWidth();

                if ($countDownElement.find('#countdown-' + id).hasClass('premium-countdown-flip')) {
                    animateFigure(periods, map);
                }
            },
            onExpiry: function () {
                if ('onExpiry' === settings.event) {
                    $countDownElement.find('#countdown-' + id).html(settings.text);
                }
            },
            serverSync: function () {
                return new Date(settings.serverSync);
            }
        });

        if (settings.reset) {
            $countDownElement.find('.premium-countdown-init').countdown('option', 'until', new Date(until));
        }

        if ('expiryUrl' === settings.event) {
            $countDownElement.find('#countdown-' + id).countdown('option', 'expiryUrl', (elementorFrontend.isEditMode()) ? '' : settings.text);
        }

        function equalWidth() {
            var width = 0;
            $countDownElement.find('#countdown-' + id + ' .countdown-amount').each(function (index, slot) {
                if (width < $(slot).outerWidth()) {
                    width = $(slot).outerWidth();
                }
            });

            $countDownElement.find('#countdown-' + id + ' .countdown-amount').css('width', width);
        }

        function animateFigure(periods, map) {
            settings.format.split('').forEach(function (unit) {

                var lowercased = unit.toLowerCase(),
                    index = map[lowercased].index,
                    oldVal = map[lowercased].oldVal;

                if (periods[index] !== oldVal) {

                    map[lowercased].oldVal = periods[index];

                    var $top = $('#countdown-' + id).find('.premium-countdown-' + lowercased + ' .top'),
                        $back_top = $('#countdown-' + id).find('.premium-countdown-' + lowercased + ' .top-back');

                    TweenMax.to($top, 0.8, {
                        rotationX: '-180deg',
                        transformPerspective: 300,
                        ease: Quart.easeOut,
                        onComplete: function () {
                            TweenMax.set($top, { rotationX: 0 });
                        }
                    });

                    TweenMax.to($back_top, 0.8, {
                        rotationX: 0,
                        transformPerspective: 300,
                        ease: Quart.easeOut,
                        clearProps: 'all'
                    });
                }
            });
        }

        times = $countDownElement.find('#countdown-' + id).countdown("getTimes");

        function runTimer(el) {
            return el == 0;
        }

        if (times.every(runTimer)) {

            if ('onExpiry' === settings.event) {
                $countDownElement.find('#countdown-' + id).html(settings.text);
            } else if ('expiryUrl' === settings.event && !elementorFrontend.isEditMode()) {
                var editMode = $('body').find('#elementor').length;
                if (0 < editMode) {
                    $countDownElement.find('#countdown-' + id).html(
                        "<h1>You can not redirect url from elementor Editor!!</h1>");
                } else {
                    if (!elementorFrontend.isEditMode()) {
                        window.location.href = settings.text;
                    }
                }

            }
        }

    };

    $(window).on('elementor/frontend/init', function () {
        elementorFrontend.hooks.addAction('frontend/element_ready/premium-countdown-timer.default', PremiumCountDownHandler);
    });
 })(jQuery);

