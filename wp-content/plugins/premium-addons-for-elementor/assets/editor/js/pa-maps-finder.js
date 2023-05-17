
var $ = jQuery;

function alertError(err) {
    alert("Geocode was not successful for the following reason: " + err);
}

function getAddress(ob) {

    var address = $(ob).parent().find("input").val(),
        geocoder = new google.maps.Geocoder(),
        $noticeMsg = $(ob).parents(".elementor-control-premium_map_notice");

    if (!address)
        return;

    geocoder.geocode({
        address: address
    }, function (results, status) {

        if (status == google.maps.GeocoderStatus.OK) {

            var latiude = results[0].geometry.location.lat(),
                longitude = results[0].geometry.location.lng();

            $noticeMsg.nextAll(".elementor-control-premium_maps_center_lat").find("input").val(latiude).trigger("input");

            $noticeMsg.nextAll(".elementor-control-premium_maps_center_long").find("input").val(longitude).trigger("input");

        } else {

            alertError(status);

        }
    });

}

function getPinAddress(ob) {

    var address = $(ob).parent().find("input").val(),
        geocoder = new google.maps.Geocoder(),
        $noticeMsg = $(ob).parents(".elementor-control-premium_map_pin_notice");

    if (!address)
        return;

    geocoder.geocode({
        address: address
    }, function (results, status) {

        if (status == google.maps.GeocoderStatus.OK) {

            var latiude = results[0].geometry.location.lat(),
                longitude = results[0].geometry.location.lng();

            $noticeMsg.nextAll(".elementor-control-map_latitude").find("input").val(latiude).trigger("input");

            $noticeMsg.nextAll(".elementor-control-map_longitude").find("input").val(longitude).trigger("input");

        } else {

            alertError(status);

        }
    });

}