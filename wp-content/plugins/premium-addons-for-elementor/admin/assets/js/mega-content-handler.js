(function ($) {
    'use strict';

    $(document).ready(function() {

        window.PaMegaContentHandler = {

            initEvents: function () {
                let _this = this;

                $('.premium-live-editor-iframe-modal .eicon-close').on('click', this.closeModal);

                $(document).on('click', '.premium-live-editor-iframe-modal', function (e) {
                    if ($(e.target).closest(".dialog-lightbox-widget-content").length < 1) {
                        _this.closeModal();
                    }
                });

                $('#pa-megamenu-content .premium-menu-btn').on('click', function (e) {
                    _this.handleMegaContent(e);
                });
            },

            handleMegaContent: function (e) {
                var widgetId = window.PremiumNavMenuSettings.currentItemId,
                    $modalContainer = $('.premium-live-editor-iframe-modal'),
                    paIframe = $modalContainer.find("#pa-live-editor-control-iframe"),
                    lightboxType = $modalContainer.find(".dialog-type-lightbox");

                $('.elementor-loader-wrapper').hide();
                lightboxType.show();
                $modalContainer.show();
                paIframe.css("z-index", "-1");

                $.ajax({
                    type: 'POST',
                    url: paMegaContent.ajaxurl,
                    dataType: 'JSON',
                    data: {
                        action: 'handle_live_editor',
                        security: paMegaContent.nonce,
                        key: widgetId,
                    },
                    success: function (res) {
                        paIframe.attr("src", res.data.url);
                        paIframe.attr("data-premium-temp-id", res.data.id);

                        window.PaMegaContentHandler.saveMegaContentId( res.data.id, widgetId );

                        paIframe.on("load", function () {
                            paIframe.show();
                            paIframe.css("z-index", "1");
                        });
                    },
                    error: function (err) {
                        console.log(err);
                    }
                });
            },

            saveMegaContentId: function ( tempID, itemID ) {

                $.ajax({
                    type: 'POST',
                    url: paMegaContent.ajaxurl,
                    dataType: 'JSON',
                    data: {
                        action: 'save_pa_mega_item_content',
                        security: paMegaContent.nonce,
                        template_id: tempID,
                        menu_item_id: itemID
                    },
                    success: function (res) {
                        console.log(res);
                    },
                    error: function (err) {
                        console.log(err);
                    }
                });
            },

            closeModal: function (inserted = false) {

                $('.premium-live-editor-iframe-modal').css('display', 'none');

                if (!inserted) {
                    var tempId = $(".premium-live-editor-iframe-modal #pa-live-editor-control-iframe").attr('data-premium-temp-id');

                    if (undefined !== tempId && '' !== tempId) {
                        window.PaMegaContentHandler.checkTempValidity(tempId);
                    }
                }

                // reset temp id/src attribute.
                $(".premium-live-editor-iframe-modal #pa-live-editor-control-iframe").attr({
                    'data-premium-temp-id': '',
                    'src': ''
                });
            },

            checkTempValidity: function (tempID) {

                if ('' !== tempID) {
                    $.ajax({
                        type: 'POST',
                        url: paMegaContent.ajaxurl,
                        dataType: 'JSON',
                        data: {
                            action: 'check_temp_validity',
                            security: paMegaContent.nonce,
                            templateID: tempID,
                        },
                        success: function (res) {
                            console.log(res.data);
                        },
                        error: function (err) {
                            console.log(err);
                        }
                    });
                }
            },
        };

        window.PaMegaContentHandler.initEvents();
    });

})(jQuery);