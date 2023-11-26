function initMap() {
    console.log('Google API Loaded');
}

jQuery(window).on("elementor/frontend/init", function () {

    var PremiumMapsHandler = function ($scope, $) {

        var $carouselWidgets = null,
            mapElement = $scope.find(".premium_maps_map_height"),
            mapSettings = mapElement.data("settings"),
            mapStyle = mapElement.data("style"),
            premiumMapMarkers = [],
            premiumMapPopups = [];

        if (mapSettings.loadScroll) {

            var $closestSection = $scope.closest('.elementor-top-section');

            elementorFrontend.waypoint($closestSection, function () {
                premiumMap = newMap(mapElement, mapSettings, mapStyle);
            }, {
                offset: '70%'
            });

        } else {
            premiumMap = newMap(mapElement, mapSettings, mapStyle);
        }


        function newMap(map, settings, mapStyle) {

            var scrollwheel = settings.scrollwheel,
                streetViewControl = settings.streetViewControl,
                fullscreenControl = settings.fullScreen,
                zoomControl = settings.zoomControl,
                mapTypeControl = settings.typeControl,
                centerLat = JSON.parse(settings.centerlat),
                centerLong = JSON.parse(settings.centerlong),
                autoOpen = settings.automaticOpen,
                hoverOpen = settings.hoverOpen,
                hoverClose = settings.hoverClose,
                args = {
                    zoom: settings["zoom"],
                    mapTypeId: settings["maptype"],
                    center: { lat: centerLat, lng: centerLong },
                    scrollwheel: scrollwheel,
                    streetViewControl: streetViewControl,
                    fullscreenControl: fullscreenControl,
                    zoomControl: zoomControl,
                    mapTypeControl: mapTypeControl,
                    styles: mapStyle
                };

            if ("yes" === mapSettings.drag)
                args.gestureHandling = "none";

            var markers = map.find(".premium-pin");

            var map = new google.maps.Map(map[0], args);

            map.markers = [];

            $carouselWidgets = $(".premium-carousel-wrapper");
            // add markers
            markers.each(function (index) {
                addMarker(jQuery(this), map, autoOpen, hoverOpen, hoverClose, index);
            });

            if ($scope.hasClass('pa-maps-carousel')) {
                $carouselWidgets.map(function (index, item) {

                    $(item).find(".premium-carousel-inner").on("afterChange", function (event, slick, currentSlide) {


                        premiumMapPopups.map(function (popup, index) {

                            popup.close();
                        });

                        if (premiumMapPopups[currentSlide])
                            premiumMapPopups[currentSlide].open(map, map.markers[currentSlide]);

                    });

                });

            }

            if (mapSettings.cluster && MarkerClusterer) {

                new MarkerClusterer(map, premiumMapMarkers, {
                    imagePath: '' != mapSettings.cluster_icon ? mapSettings.cluster_icon : "https://developers.google.com/maps/documentation/javascript/examples/markerclusterer/m"
                });
            }

            return map;
        }

        var activeInfoWindow = null;
        function addMarker(pin, map, autoOpen, hoverOpen, hoverClose, zIndex) {

            var latlng = new google.maps.LatLng(pin.data("lat"), pin.data("lng")),
                iconImg = pin.data("icon"),
                maxWidth = pin.data("max-width"),
                customID = pin.data("id"),
                isActive = pin.data("activated"),
                iconSize = parseInt(pin.data("icon-size"));

            if ('' != iconImg) {
                var icon = {
                    url: iconImg
                };

                if (iconSize) {
                    icon.scaledSize = new google.maps.Size(iconSize, iconSize);
                    icon.origin = new google.maps.Point(0, 0);
                    icon.anchor = new google.maps.Point(iconSize / 2, iconSize);
                }
            }

            // create marker
            var marker = new google.maps.Marker({
                position: latlng,
                map: map,
                icon: icon,
                zIndex: zIndex
            });


            //Used with Carousel Custom Navigation option
            if (customID) {

                google.maps.event.addListener(marker, "click", function () {

                    if ($carouselWidgets.length > 0) {
                        $carouselWidgets.map(function (index, item) {
                            var carouselSettings = $(item).data("settings");

                            if (carouselSettings.navigation) {

                                if (-1 != carouselSettings.navigation.indexOf("#" + customID)) {
                                    var slideIndex = carouselSettings.navigation.indexOf("#" + customID);
                                    $(item).find(".premium-carousel-inner").slick("slickGoTo", slideIndex);
                                }
                            }
                        })

                    }

                });
            }

            // if marker contains HTML, add it to an infoWindow
            if (pin.find(".premium-maps-info-title").html() || pin.find(".premium-maps-info-desc").html()) {
                // create info window

                var infowindow = new google.maps.InfoWindow({
                    maxWidth: maxWidth,
                    content: pin.html()
                });

                //Opened by default.
                if (autoOpen || isActive) {
                    infowindow.open(map, marker);
                }

                //Open on hover.
                if (hoverOpen) {

                    var isTouch = checkTouchDevice(),
                        triggerEvent = isTouch ? "click" : "mouseover";

                    google.maps.event.addListener(marker, triggerEvent, function () {

                        if (activeInfoWindow)
                            activeInfoWindow.close();

                        activeInfoWindow = infowindow;

                        infowindow.open(map, marker);

                    });

                    //Close on mouse out.
                    if (hoverClose && !isTouch) {
                        google.maps.event.addListener(marker, "mouseout", function () {
                            infowindow.close(map, marker);
                        });
                    }
                }

                // Show info window when marker is clicked
                google.maps.event.addListener(marker, "click", function () {

                    if (activeInfoWindow)
                        activeInfoWindow.close();

                    //Used with Carousel Custom Navigation option
                    if (customID) {

                        if ($carouselWidgets.length) {
                            $carouselWidgets.map(function (index, item) {
                                var carouselSettings = $(item).data("settings");

                                if (carouselSettings.navigation) {
                                    if (-1 != carouselSettings.navigation.indexOf("#" + customID)) {
                                        var slideIndex = carouselSettings.navigation.indexOf("#" + customID);
                                        $carouselWidgets.find(".premium-carousel-inner").slick("slickGoTo", slideIndex);
                                    }
                                }
                            })

                        }

                    }

                    activeInfoWindow = infowindow;

                    infowindow.open(map, marker);

                });

                infowindow.addListener('visible', function () {

                    if (pin.find('.advanced-pin').length > 0)
                        $('.gm-ui-hover-effect').remove();

                    $scope.find('.premium-maps-info-close').on('click', function () {
                        infowindow.close(map, marker);
                    })
                })


                if ($scope.hasClass('pa-maps-carousel'))
                    premiumMapPopups.push(infowindow);


            }

            // add to array
            map.markers.push(marker);
            premiumMapMarkers.push(marker);

        }

        function checkTouchDevice() {

            var currentDevice = elementorFrontend.getCurrentDeviceMode();

            return !['desktop', 'widescreen', 'laptop'].includes(currentDevice);

        }

    };

    elementorFrontend.hooks.addAction("frontend/element_ready/premium-addon-maps.default", PremiumMapsHandler);

});
