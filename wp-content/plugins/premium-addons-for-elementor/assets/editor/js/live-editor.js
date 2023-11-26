(function () {
    var $ = jQuery;

    function handleLiveEditor() {

        // close model events.
        $('.eicon-close').on('click', closeModal);

        $('#pa-insert-live-temp').on('click', function () {
            $('body').attr('data-pa-liveeditor-load', 'true');
            closeModal(true);
        });

        $(document).on('click', '.premium-live-editor-iframe-modal', function (e) {
            if ($(e.target).closest(".dialog-lightbox-widget-content").length < 1) {
                closeModal();
            }
        });

        // resize model event.
        $('.premium-live-editor-iframe-modal .premium-expand').on('click', function () {

            if ($(this).find(' > i').hasClass('eicon-frame-expand')) {
                $(this).find('i.eicon-frame-expand').removeClass('eicon-frame-expand').addClass('eicon-frame-minimize').attr('title', 'Minimize');
                $('.premium-live-editor-iframe-modal').addClass('premium-modal-expanded');

            } else {
                minimizeModal(this);
            }
        });

        elementor.channels.editor.on('createLiveTemp', function (e) {

            var widgetId = getTemplateKey(e),
                tempType = getTemplateType(e),
                $modalContainer = $('.premium-live-editor-iframe-modal'),
                paIframe = $modalContainer.find("#pa-live-editor-control-iframe"),
                $lightboxLoading = $modalContainer.find(".dialog-lightbox-loading"),
                lightboxType = $modalContainer.find(".dialog-type-lightbox"),
                tempSelectorId = e.model.attributes.name.split('_live')[0],
                liveTempId = ['premium_content_toggle_second_content_templates', 'fixed_template', 'right_side_template'].includes(tempSelectorId) ? 'live_temp_content_extra' : 'live_temp_content',
                settingsToChange = {};

            // multiscroll has two temps in each repeater item => both temps will have the same id so we need to distinguish one of them.
            if ('right_side_template' === tempSelectorId) {
                widgetId += '2';
            }

            // show modal.
            lightboxType.show();
            $modalContainer.show();
            $lightboxLoading.show();
            paIframe.contents().find("#elementor-loading").show();
            paIframe.css("z-index", "-1");

            $.ajax({
                type: 'POST',
                url: liveEditor.ajaxurl,
                dataType: 'JSON',
                data: {
                    action: 'handle_live_editor',
                    security: liveEditor.nonce,
                    key: widgetId,
                    type: tempType
                },
                success: function (res) {

                    paIframe.attr("src", res.data.url);
                    paIframe.attr("data-premium-temp-id", res.data.id);

                    if ('loop' === tempType) {
                        paIframe.attr("data-premium-temp-type", tempType);
                    }

                    $('#premium-live-temp-title').val(res.data.title);

                    paIframe.on("load", function () {
                        $lightboxLoading.hide();
                        paIframe.show();
                        $modalContainer.find('.premium-live-editor-title').css('display', 'flex');
                        paIframe.contents().find("#elementor-loading").hide();
                        paIframe.css("z-index", "1");
                    });

                    clearInterval(window.paLiveEditorInterval);

                    window.paLiveEditorInterval = setInterval(function () {

                        var loadTemplate = $('body').attr('data-pa-liveeditor-load');

                        if ('true' === loadTemplate) {
                            $('body').attr('data-pa-liveeditor-load', 'false');

                            settingsToChange[tempSelectorId] = '';
                            settingsToChange[liveTempId] = $('#premium-live-temp-title').val();

                            if (['loop', 'grid'].includes(tempType)) {
                                settingsToChange['pa_' + tempType + '_live_temp_id'] = res.data.id;
                            }

                            $(".premium-live-temp-title").removeClass("control-hidden");
                            $e.run('document/elements/settings', { container: e.container, settings: settingsToChange, options: { external: !0 } });

                            var tempTitle = $('#premium-live-temp-title').val();

                            if (tempTitle && tempTitle !== res.data.title) {
                                updateTemplateTitle(tempTitle, res.data.id);
                            }
                        }
                    }, 1000);
                },
                error: function (err) {
                    console.log(err);
                }
            });
        });
    }

    /**
     * Helper Funcitons.
     */

    function checkTempValidity(tempID, tempType) {

        if ('' !== tempID) {
            $.ajax({
                type: 'POST',
                url: liveEditor.ajaxurl,
                dataType: 'JSON',
                data: {
                    action: 'check_temp_validity',
                    security: liveEditor.nonce,
                    templateID: tempID,
                    tempType: tempType
                },
                success: function (res) {
                    console.log(res.data);
                },
                error: function (err) {
                    console.log(err);
                }
            });
        }
    }

    /**
     * Generate the temp key
     * @param {Object} e click event
     * @return {string}
     */
    function getTemplateKey(e) {
        var widget = e.options.container.view.$el,
            // control_id = e._parent.model.attributes._id ? e._parent.model.attributes._id : e.model.cid;
            control_id = e._parent.model.attributes._id ? e._parent.model.attributes._id : '';

        return widget.data('id') + control_id;
    }

    /**
     * Check the template type.
     * returns 'loop' if the button type contains "loop-item" to indicate we're creating/editing a loop template.
     *
     * @param {Object} e click event
     * @return {string}
     */
    function getTemplateType(e) {
        var classes = e.model.attributes.button_type;

        if (classes.includes('loop-temp')) {
            return 'loop';
        } else if (classes.includes('grid-temp')) {
            return 'grid';
        }
        // return classes.includes('loop-temp') ? 'loop' : '';
    }

    function minimizeModal(_this) {

        $(_this).find('i.eicon-frame-minimize').removeClass('eicon-frame-minimize').addClass('eicon-frame-expand').attr('title', 'Expand');
        $('.premium-live-editor-iframe-modal').removeClass('premium-modal-expanded');
    }

    function updateTemplateTitle(title, id) {

        $.ajax({
            type: 'POST',
            url: liveEditor.ajaxurl,
            dataType: 'JSON',
            data: {
                action: 'update_template_title',
                security: liveEditor.nonce,
                title: title,
                id: id
            },
            success: function (res) {
                console.log('Template Title Updated.');
            },
            error: function (err) {
                console.log(err);
            }
        });
    }

    function closeModal(inserted = false) {

        $('.premium-live-editor-iframe-modal').css('display', 'none');

        $(".premium-live-temp-title input").attr('disabled', 'true');

        minimizeModal($('.premium-live-editor-iframe-modal .premium-expand'));

        if (!inserted) {
            var tempId = $(".premium-live-editor-iframe-modal #pa-live-editor-control-iframe").attr('data-premium-temp-id'),
                tempType = $(".premium-live-editor-iframe-modal #pa-live-editor-control-iframe").attr('data-premium-temp-type');

            if (undefined !== tempId && '' !== tempId) {
                checkTempValidity(tempId, tempType);
            }
        }

        // reset temp id/src attribute.
        $(".premium-live-editor-iframe-modal #pa-live-editor-control-iframe").attr({
            'data-premium-temp-id': '',
            'data-premium-temp-type': '',
            'src': ''
        });
    }

    function checkLiveTemplateControl() {

        setTimeout(function () {

            $(".premium-live-temp-title input").each(function (index, input) {
                $(input).attr('disabled', 'true');
                if ('' != $(input).val()) {
                    $(input).closest(".premium-live-temp-title").removeClass("control-hidden");
                }
            });

            $('.premium-cf-form-id input').attr('disabled', 'true');

        }, 500);
    }

    elementor.channels.editor.on('section:activated', checkLiveTemplateControl);

    $(window).on('elementor:init', handleLiveEditor);

})(jQuery);