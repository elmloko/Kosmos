function openFbPopup(url, width, height, callBack) {
    var top = top || screen.height / 2 - height / 2,
        left = left || screen.width / 2 - width / 2,
        win = window.open(
            url,
            "",
            "location=1,status=1,resizable=yes,width=" +
            width +
            ",height=" +
            height +
            ",top=" +
            top +
            ",left=" +
            left
        );

    function check() {
        if (!win || win.closed != false) {
            callBack();
        } else {
            setTimeout(check, 100);
        }
    }

    setTimeout(check, 100);
}

function connectPinterest(obj) {

    var url = "https://appfb.premiumaddons.com/auth/pinterest";

    openFbPopup(
        url, 670, 520,
        function () {
            jQuery.ajax({
                type: "GET",
                url: paEditorSettings.ajaxurl,
                dataType: "JSON",
                data: {
                    action: "get_pinterest_token",
                    security: paEditorSettings.nonce
                },
                success: function (res) {

                    if (res.success) {

                        var accessToken = res.data;

                        pinterestToken = accessToken;

                        jQuery(obj)
                            .parents(".elementor-control-pinterest_login")
                            .nextAll(".elementor-control-access_token")
                            .find("textarea")
                            .val(accessToken)
                            .trigger("input");

                    }
                },
                error: function (err) {
                    console.log(err);
                }
            });
        }
    );

    return false;
}

function connectTiktok(obj) {

    var url = "https://appfb.premiumaddons.com/auth/tiktok";

    openFbPopup(
        url, 670, 520,
        function () {
            jQuery.ajax({
                type: "GET",
                url: paEditorSettings.ajaxurl,
                dataType: "JSON",
                data: {
                    action: "get_tiktok_token",
                    security: paEditorSettings.nonce
                },
                success: function (res) {

                    if (res.success) {

                        var accessToken = res.data;

                        jQuery(obj)
                            .parents(".elementor-control-tiktok_login")
                            .nextAll(".elementor-control-access_token")
                            .find("textarea")
                            .val(accessToken)
                            .trigger("input");
                    }
                },
                error: function (err) {
                    console.log(err);
                }
            });
        }
    );

    return false;
}

function createInsertCfForm(obj) {

    // console.log(obj);

    var targetPreset = jQuery(obj).parents(".elementor-control-form_insert").prevAll(".elementor-control-presets").find('.checked').attr('value');

    console.log(targetPreset);

    if (!targetPreset)
        return;

    jQuery.ajax({
        type: "GET",
        url: paEditorSettings.ajaxurl,
        dataType: "JSON",
        data: {
            action: "insert_cf_form",
            security: paEditorSettings.nonce,
            preset: targetPreset
        },
        success: function (res) {

            if (res.success) {

                var formID = res.data;

                jQuery(obj)
                    .parents(".elementor-control-form_insert")
                    .nextAll(".elementor-control-form_id")
                    .find("input")
                    .val(formID)
                    .trigger("input");

                jQuery(obj).find('.elementor-button').css('background-color', '#D8D8D8');

            }
        },
        error: function (err) {
            console.log(err);
        }
    });

}

function connectPinterestInit(obj) {

    if (!obj) return;

    connectPinterest(obj);
}

function connectTiktokInit(obj) {

    if (!obj) return;

    connectTiktok(obj);
}

function createCfForm(obj) {

    if (!obj) return;

    createInsertCfForm(obj);
}