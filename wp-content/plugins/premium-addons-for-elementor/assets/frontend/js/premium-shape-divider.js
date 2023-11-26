(function ($) {
    $(window).on('elementor/frontend/init', function () {

        var premiumShapeDividerHandler = function ($scope, $) {

            if (!$scope.hasClass('premium-shape-divider-yes')) {
                return;
            }

            var id = $scope.data("id"),
                isEditMode = elementorFrontend.isEditMode(),
                isCustomSvg = $scope.hasClass('premium-shape-divider__custom'),
                hasCustomFill = $('#premium-shape-divider-fill-'+ id).length,
                hasWaveAnime = $scope.hasClass('premium-shape-divider-anime-yes') && $scope.hasClass('premium-shape22');

            if ( hasCustomFill ) {
                // set the fill viewBox
                var shapeViewBox = $('#premium-shape-divider-' + id + ' svg').attr('viewBox');

                $('#premium-shape-divider-fill-'+ id).attr('viewBox', shapeViewBox);
                // set the fill value
                $('#premium-shape-divider-' + id + ' svg').attr('fill', 'url(#pa-shape-divider-fill-' + id + ')');
            }

            if ( ! isEditMode ) {
                $scope.append($('#premium-shape-divider-fill-' + id ));
                $scope.append($('#premium-shape-divider-' + id ));
            }

            updateShapePosition();
            updateShapeSize();

            $scope.find('#premium-shape-divider-' + id).css({ visibility: 'inherit', opacity: 'inherit' });

            if ( hasWaveAnime ) {

                var wave1 = "M0,192L16,202.7C32,213,64,235,96,250.7C128,267,160,277,192,277.3C224,277,256,267,288,229.3C320,192,352,128,384,106.7C416,85,448,107,480,122.7C512,139,544,149,576,144C608,139,640,117,672,101.3C704,85,736,75,768,101.3C800,128,832,192,864,192C896,192,928,128,960,128C992,128,1024,192,1056,208C1088,224,1120,192,1152,154.7C1184,117,1216,75,1248,96C1280,117,1312,203,1344,208C1376,213,1408,139,1424,101.3L1440,64L1440,320L1424,320C1408,320,1376,320,1344,320C1312,320,1280,320,1248,320C1216,320,1184,320,1152,320C1120,320,1088,320,1056,320C1024,320,992,320,960,320C928,320,896,320,864,320C832,320,800,320,768,320C736,320,704,320,672,320C640,320,608,320,576,320C544,320,512,320,480,320C448,320,416,320,384,320C352,320,320,320,288,320C256,320,224,320,192,320C160,320,128,320,96,320C64,320,32,320,16,320L0,320Z",

                wave2= "M0,288L16,261.3C32,235,64,181,96,160C128,139,160,149,192,160C224,171,256,181,288,208C320,235,352,277,384,261.3C416,245,448,171,480,149.3C512,128,544,160,576,170.7C608,181,640,171,672,186.7C704,203,736,245,768,234.7C800,224,832,160,864,128C896,96,928,96,960,96C992,96,1024,96,1056,133.3C1088,171,1120,245,1152,240C1184,235,1216,149,1248,138.7C1280,128,1312,192,1344,218.7C1376,245,1408,235,1424,229.3L1440,224L1440,320L1424,320C1408,320,1376,320,1344,320C1312,320,1280,320,1248,320C1216,320,1184,320,1152,320C1120,320,1088,320,1056,320C1024,320,992,320,960,320C928,320,896,320,864,320C832,320,800,320,768,320C736,320,704,320,672,320C640,320,608,320,576,320C544,320,512,320,480,320C448,320,416,320,384,320C352,320,320,320,288,320C256,320,224,320,192,320C160,320,128,320,96,320C64,320,32,320,16,320L0,320Z",
                wave3 = "M0,224L16,192C32,160,64,96,96,96C128,96,160,160,192,197.3C224,235,256,245,288,224C320,203,352,149,384,149.3C416,149,448,203,480,197.3C512,192,544,128,576,96C608,64,640,64,672,85.3C704,107,736,149,768,144C800,139,832,85,864,101.3C896,117,928,203,960,213.3C992,224,1024,160,1056,154.7C1088,149,1120,203,1152,192C1184,181,1216,107,1248,69.3C1280,32,1312,32,1344,69.3C1376,107,1408,181,1424,218.7L1440,256L1440,320L1424,320C1408,320,1376,320,1344,320C1312,320,1280,320,1248,320C1216,320,1184,320,1152,320C1120,320,1088,320,1056,320C1024,320,992,320,960,320C928,320,896,320,864,320C832,320,800,320,768,320C736,320,704,320,672,320C640,320,608,320,576,320C544,320,512,320,480,320C448,320,416,320,384,320C352,320,320,320,288,320C256,320,224,320,192,320C160,320,128,320,96,320C64,320,32,320,16,320L0,320Z";

                var animeSettings = {
                    targets: '#premium-shape-divider-' + id + ' svg > path',
                    loop: true,
                    direction: 'alternate',
                    easing: 'linear',
                    duration: 7500,
                    d: [
                        { value: wave2 },
                        { value: wave3 },
                        { value: wave1 },
                    ],
                };

                $scope.find('#premium-shape-divider-' + id + ' svg').attr('viewBox', '0 0 1440 320');
                $('#premium-shape-divider-' + id + ' svg > path').attr('d', wave1);

                anime(animeSettings);
            }

            if ( isCustomSvg ) {
                $scope.find('#premium-shape-divider-' + id + ' svg').attr('preserveAspectRatio', 'none');
            }

            // add shape events.
            $(window).off('resize.paUpdateShapePos'); // to update the event while editing.
            $(window).on('resize.paUpdateShapePos', updateShapePosition);

            $(window).off('resize.paShapeResize');
            $(window).on('resize.paShapeResize', updateShapeSize);

            function updateShapePosition() {
                var shapePos = getComputedStyle($scope[0]).getPropertyValue('--pa-sh-divider-pos');
                $scope.removeClass('premium-shape-divider__top premium-shape-divider__bottom premium-shape-divider__right premium-shape-divider__left').addClass('premium-shape-divider__' + shapePos);
            }

            function updateShapeSize() {

                var adjustSize = $scope.hasClass('premium-shape-divider__left') || $scope.hasClass('premium-shape-divider__right');

                if ( adjustSize ) {
                    var containerHeight = $scope.outerHeight() + 'px';

                    $scope.find('#premium-shape-divider-' + id + ' svg').css({
                        'width': '103%',
                        'transform' : $scope.hasClass('premium-shape-divider__right') ? 'rotate(-90deg) translate(0,-100%)' : 'rotate(90deg) translate(0,-100%)',
                        'width': containerHeight,
                    }).get(0).style.setProperty("--premium-shape-divider-h", containerHeight); // this needs to add for each devices.

                } else {
                    $scope.find('#premium-shape-divider-' + id + ' svg').attr('style','');
                }
            }
        };

        elementorFrontend.hooks.addAction("frontend/element_ready/section", premiumShapeDividerHandler);
        elementorFrontend.hooks.addAction("frontend/element_ready/column", premiumShapeDividerHandler);
        elementorFrontend.hooks.addAction("frontend/element_ready/container", premiumShapeDividerHandler);
    });
})(jQuery);