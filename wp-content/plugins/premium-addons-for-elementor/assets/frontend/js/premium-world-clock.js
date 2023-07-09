(function ($) {

    var PremiumWorldClockHandler = function ($scope, $) {
        var _ = luxon,
            id = $scope.data('id'),
            settings = $scope.find('.premium-world-clock__clock-wrapper').data('settings'),
            isParentHotspot = $scope.closest('#tooltip_content').length > 0,
            analogclocks = ['skin-1', 'skin-5', 'skin-6', 'skin-7'],
            inc = isParentHotspot ? 300 : 1000;

        if (!settings)
            return;

        window['clockInterval-' + id];

        if (window['clockInterval-' + id]) {
            clearInterval(window['clockInterval-' + id]);
        }

        if (analogclocks.includes(settings.skin) && settings.showClockNum) {
            window['clockNumbers-' + id] = false;
            drawClockNumbers($scope);
        }

        window['clockInterval-' + id] = setInterval(clockInit, inc, settings, $scope, id);

        function clockInit(settings, $scope, id) {

            var isInHotspots = $('.elementor-element-' + id).closest('.premium-tooltipster-base').length > 0;

            if (isInHotspots) {
                $scope = $('.elementor-element-' + id);

                settings = $scope.find('.premium-world-clock__clock-wrapper').data('settings');

                if (!window['clockNumbers-' + id] && analogclocks.includes(settings.skin) && settings.showClockNum) {
                    drawClockNumbers($scope);
                    window['clockNumbers-' + id] = true;
                }
            }

            var time = getTimeComponents(settings);

            if (!time) {
                var htmlNotice = '<div class="premium-error-notice">This Is An Invalid Timezone Name. Please Enter a Valid Timezone Name</div>';
                $scope.find('.premium-world-clock__clock-wrapper').html(htmlNotice);
            }

            if (['skin-1', 'skin-5', 'skin-6', 'skin-7'].includes(settings.skin)) {

                $scope.find('.premium-world-clock__hours').css('transform', 'rotate(' + ((time.hours * 30) + (time.minutes * 6 / 12)) + 'deg)').text('');
                $scope.find('.premium-world-clock__minutes').css('transform', 'rotate(' + time.minutes * 6 + 'deg)').text('');
                $scope.find('.premium-world-clock__seconds').css('transform', 'rotate(' + time.seconds * 6 + 'deg)').text('');

                if (settings.showMeridiem) {
                    $scope.find('.premium-world-clock__meridiem').text(time.meridiem);
                }

            } else {

                $scope.find('.premium-world-clock__hours').text(time.hours);
                $scope.find('.premium-world-clock__minutes').text(time.minutes);

                if (settings.showSeconds) {
                    $scope.find('.premium-world-clock__seconds').text(time.seconds);
                }

                if (settings.showMeridiem) {
                    var type = settings.meridiemType;

                    if ('text' === type) {

                        $scope.find('.premium-world-clock__meridiem').text(time.meridiem);

                    } else {
                        var meridiemIcons = {
                            'AM': '<svg id="Weather_Icons" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"><defs><style>.cls-1{fill:#333;}</style></defs><g id="Clear_Sky"><circle class="cls-1" cx="12" cy="12" r="5.5"/><path class="cls-1" d="m21.76,12.74h-1.95c-.98,0-.98-1.47,0-1.47h1.95c.98,0,.98,1.47,0,1.47Z"/><path class="cls-1" d="m19.39,5.62l-1.38,1.38c-.29.29-.75.29-1.04,0-.29-.29-.29-.75,0-1.04l1.38-1.38c.29-.28.75-.28,1.04,0,.28.29.28.75,0,1.04Z"/><path class="cls-1" d="m12.74,2.24v1.95c0,.4-.33.73-.73.73s-.74-.33-.74-.73v-1.95c0-.41.33-.74.74-.74s.73.33.73.74Z"/><path class="cls-1" d="m5.96,7.03l-1.38-1.38c-.32-.31-.29-.75,0-1.04s.72-.31,1.03,0l1.38,1.38c.69.69-.34,1.73-1.03,1.04Z"/><path class="cls-1" d="m4.19,12.74h-1.95c-.98,0-.98-1.47,0-1.47h1.95c.98,0,.98,1.47,0,1.47Z"/><path class="cls-1" d="m7.02,18.04l-1.38,1.38c-.31.31-.75.29-1.04,0s-.31-.72,0-1.03l1.38-1.38c.32-.31.75-.29,1.04,0,.29.28.31.72,0,1.03Z"/><path class="cls-1" d="m12.74,19.82v1.95c0,.98-1.47.98-1.47,0v-1.95c0-.98,1.47-.98,1.47,0Z"/><path class="cls-1" d="m19.43,19.4c-.29.28-.73.31-1.04,0l-1.38-1.39c-.31-.31-.29-.75,0-1.03.28-.29.72-.31,1.03,0l1.39,1.38c.31.31.28.75,0,1.04Z"/></g></svg>',
                            'PM': '<svg id="Weather_Icons" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"><defs><style>.cls-1{fill:#333;}</style></defs><path id="Moon" class="cls-1" d="m21.93,17.23c-1.89,3.24-5.4,5.27-9.26,5.27-5.89,0-10.67-4.7-10.67-10.51S6.37,1.87,11.95,1.5c.4-.02.67.41.46.76-.83,1.42-1.28,3.04-1.28,4.73,0,5.25,4.33,9.51,9.68,9.51.22,0,.44,0,.65-.02.4-.03.67.4.47.75Z"/></svg>'
                        },
                            currentIcon = (6 <= time.hoursNotPadded <= 17) ? meridiemIcons['AM'] : meridiemIcons['PM'];

                        $scope.find('.premium-world-clock__meridiem').html(currentIcon);
                    }
                }

                if (settings.equalWidth) {
                    equalWidth();
                }
            }

            if (settings.date) {

                if ('skin-3' === settings.skin) {

                    var daysNum = settings.daysNum,
                        currentDay = time.date.d.toLowerCase(),
                        $daysWrapper = $scope.find('.premium-world-clock__days-wrapper'),
                        daysMap = {
                            0: 'mon',
                            1: 'tue',
                            2: 'wed',
                            3: 'thu',
                            4: 'fri',
                            5: 'sat',
                            6: 'sun',
                        },
                        currentDayOrder = parseInt(time.date.order) - 1;

                    $daysWrapper.html('<span class="premium-world-clock__day-name current-day">' + currentDay + '</span>');

                    for (var i = 1; i <= parseInt(daysNum); i++) {

                        var dayBefore = currentDayOrder - i,
                            dayAfter = currentDayOrder + i;

                        if (dayBefore < 0) {
                            dayBefore += 7;
                        }

                        if (dayAfter > 6) {
                            dayAfter -= 7;
                        }

                        $daysWrapper.prepend('<span class="premium-world-clock__day-name">' + daysMap[dayBefore] + '</span>');
                        $daysWrapper.append('<span class="premium-world-clock__day-name">' + daysMap[dayAfter] + '</span>');
                    }

                    $scope.find('.premium-world-clock__month').text(time.date.m);
                    $scope.find('.premium-world-clock__day').text(time.date.dn);

                } else if ('skin-4' === settings.skin) {

                    $scope.find('.premium-world-clock__date-wrapper').html('');

                    settings.dateFormat.forEach(function (format) {
                        if (time.date[format] !== undefined && time.date[format] !== null) {
                            var html = '<span class="premium-world-clock__date-segment">' + time.date[format] + '</span>';
                            $scope.find('.premium-world-clock__date-wrapper').append(html);
                        }
                    });

                } else {
                    $scope.find('.premium-world-clock__date').text(time.date);
                }
            }

            if (settings.gmtOffset) {
                var offset = 'Z' === settings.offsetFormat ? time.offset + 'HRS' : time.offset;
                $scope.find('.premium-world-clock__gmt-offset').text(offset);
            }

            $scope.find('.premium-addons__v-hidden').removeClass('premium-addons__v-hidden');
        }

        function getTimeComponents(settings) {

            var skin = settings.skin,
                showDate = settings.date,
                showGmtOffset = settings.gmtOffset,
                time = {
                    hours: '',
                    minutes: '',
                    seconds: '',
                    meridiem: '',
                    date: '',
                },
                dateTime = _.DateTime.local().setZone(settings.timezone);

            if (!dateTime.isValid) {
                return false;
            }

            time.hours = dateTime.toFormat(settings.format);
            time.minutes = dateTime.toFormat('mm');
            time.seconds = dateTime.toFormat('ss');

            if (showDate) {
                if ('skin-3' === skin || 'skin-4' === skin) {
                    time.date = {
                        d: dateTime.toFormat('ccc'),
                        dn: dateTime.toFormat('dd'),
                        m: dateTime.toFormat('LLL'),
                        order: dateTime.toFormat('c')
                    };

                } else {
                    time.date = dateTime.toFormat(settings.dateFormat);
                }
            }

            if (showGmtOffset) {
                time.offset = dateTime.toFormat(settings.offsetFormat);
            }

            if (settings.showMeridiem) {
                time.meridiem = dateTime.toFormat('a');
                time.hoursNotPadded = parseInt(dateTime.toFormat('H'));
            }

            return time;
        }

        function equalWidth(skin) {
            var width = 0,
                selector = 'skin-3' === skin ? '.premium-world-clock__hand:not(.premium-world-clock__seconds)' : '.premium-world-clock__hand';

            $scope.find(selector).each(function (index, slot) {
                if (width < $(slot).outerWidth()) {
                    width = $(slot).outerWidth();
                }
            });

            $scope.find(selector).css('min-width', width);
        }

        function drawClockNumbers($scope) {

            var $clockNumbers = $scope.find('.premium-world-clock__clock-number');

            for (var i = 0; i < 12; i++) {
                var point = getCirclePoint(50, i * 30, 50, 50);

                $($clockNumbers[i]).css('left', point.x + '%');
                $($clockNumbers[i]).css('top', point.y + '%');
            }
        }

        function getCirclePoint(r, degrees, cx, cy) {
            var angleInRadians = degrees * (Math.PI / 180);
            var xp = cx + r * Math.cos(angleInRadians);
            var yp = cy + r * Math.sin(angleInRadians);

            return {
                x: xp,
                y: yp
            };
        }
    };

    $(window).on('elementor/frontend/init', function () {
        elementorFrontend.hooks.addAction('frontend/element_ready/premium-world-clock.default', PremiumWorldClockHandler);
    });

})(jQuery);
