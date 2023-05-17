(function ($) {
    'use strict';

    var poppinsfontLink = document.createElement('link');
    poppinsfontLink.rel = 'stylesheet';
    poppinsfontLink.href = 'https://fonts.googleapis.com/css?family=Poppins:100,100italic,200,200italic,300,300italic,400,400italic,500,500italic,600,600italic,700,700italic,800,800italic,900,900italic';
    poppinsfontLink.type = 'text/css';
    document.head.appendChild(poppinsfontLink);

    $(document).ready(function () {

        window.PremiumNavMenuSettings = {

            itemsData: {},

            currentItemId: null,

            currenItemDepth: null,

            init: function () {
                this.initControls();
                this.addSettingsTriggers();
                this.initEvents();
            },

            initControls: function () {
                // Color Controls.
                $('#premium-icon-color-field, #premium-badge-color-field, #premium-badge-bg-field').wpColorPicker();

                // Icon Picker.
                this.iconPicker = $('#premium-icon-field').fontIconPicker({
                    source: PremiumIconsList,
                    hasSearch: true,
                    emptyIcon: true,
                });
            },

            addSettingsTriggers: function () {
                var _this = this,
                    pos = $('body').hasClass('rtl') ? 'right' : 'left';

                $('#menu-to-edit .menu-item').each(function () {

                    var itemTrigger = _this.getTriggerHtml(this);

                    $(this).addClass('premium-menu-item');
                    $(this).append(itemTrigger);

                    $(this).find('.premium-menu-item-settings').css(pos, $(this).find('.menu-item-handle').outerWidth() + 10 + 'px');
                });
            },

            initEvents: function () {
                var _this = this;

                $('.premium-menu-item-settings').on('click', function (e) {
                    _this.triggerSettingsPopup(_this, e);
                });

                $('#premium-menu-save').on('click', function () {
                    var $button = $(this);
                    _this.saveItemSettings(_this, $button);
                });

                $('.premium-menu-settings-modal .eicon-close').on('click', this.closeModal);

                $(document).on('click', '.premium-menu-settings-modal', function (e) {
                    if ($(e.target).closest(".dialog-lightbox-widget-content").length < 1) {
                        window.PremiumNavMenuSettings.closeModal();
                    }
                });
            },

            triggerSettingsPopup: function (_this, e) {

                _this.currentItemId = $(e.target).data('id');
                _this.currenItemDepth = $(e.target).data('item-depth');

                _this.handlePopupControls(_this);

                $(".premium-menu-btn i").addClass("loader-hidden dashicons-admin-generic").removeClass("dashicons-yes");
                $(".premium-menu-btn span").text('Save Settings');

                // show depth-0 controls
                if (0 == _this.currenItemDepth) {
                    $('.premium-setting-container.pa-depth-0-control').removeClass('premium-setting-hidden');
                }
                // $('#elementor-template-nav-menu-modal-container').show();
                // show the container and show a spinner till controls are prepared.
            },

            handlePopupControls: function (_this) {

                if (_this.itemsData[_this.currentItemId]) {
                    _this.setControlsVal(_this.itemsData[_this.currentItemId]);

                } else {
                    $.ajax({
                        url: paMenuSettings.ajaxurl,
                        type: 'POST',
                        data: {
                            action: 'get_pa_menu_item_settings',
                            security: paMenuSettings.nonce,
                            item_id: _this.currentItemId
                        },
                        success: function (res) {
                            _this.itemsData[_this.currentItemId] = res.data;
                            _this.setControlsVal(res.data);
                        },
                        error: function (error) {
                            console.log(error);
                        }
                    });
                }
            },

            setControlsVal: function (settings) {

                var _this = this;

                if (settings) {

                    var isChecked = 'true' == settings.mega_content_enabled ? true : false,
                        isFullWidth = 'true' == settings.full_width_mega_content ? true : false;

                    $('#pa-megamenu-icon-type').val(settings.item_icon_type)
                    _this.iconPicker.val(settings.item_icon);
                    _this.iconPicker.refreshPicker();
                    $('#premium-lottie-url').val(settings.item_lottie_url);
                    $('#premium-badge-text-field').val(settings.item_badge);
                    $('#premium-badge-bg-field').wpColorPicker("color", settings.item_badge_bg);
                    $('#pa-megamenu-position').val(settings.mega_content_pos);
                    $('#premium-icon-color-field').wpColorPicker("color", settings.item_icon_color);
                    $('#premium-badge-color-field').wpColorPicker("color", settings.item_badge_color);
                    $('#pa-mega-content-width').val(settings.mega_content_width.replace('px', ''));
                    $('#pa-megamenu-switcher input').prop('checked', isChecked);
                    $('#pa-full-width-switcher input').prop('checked', isFullWidth);

                } else {
                    $('#pa-megamenu-icon-type').val('icon');
                    _this.iconPicker.val('');
                    _this.iconPicker.refreshPicker();
                    $('#premium-lottie-url').val('');
                    $('#premium-badge-text-field').val('');
                    $('#premium-badge-bg-field').wpColorPicker("color", '#bada55');
                    $('#pa-megamenu-position').val('default');
                    $('#premium-icon-color-field').wpColorPicker("color", '#bada55');
                    $('#premium-badge-color-field').wpColorPicker("color", '#bada55');
                    $('#pa-mega-content-width').val('');
                    $('#pa-megamenu-switcher input').prop('checked', false);
                    $('#pa-full-width-switcher input').prop('checked', false);
                }

                this.checkIconType();

                $("#pa-megamenu-icon-type").on('change', function () {
                    _this.checkIconType();
                });

                $('#elementor-template-nav-menu-modal-container').show();
            },

            checkIconType: function () {

                if ('icon' === $("#pa-megamenu-icon-type").val()) {
                    $(".premium-lottie-settings").addClass("premium-setting-hidden");
                    $(".premium-icon-settings").removeClass("premium-setting-hidden");
                } else {
                    $(".premium-lottie-settings").removeClass("premium-setting-hidden");
                    $(".premium-icon-settings").addClass("premium-setting-hidden");
                }

            },
            saveItemSettings: function (_this, $btn) {

                var $btnIcon = $btn.find("i");
                if (!$btnIcon.hasClass("loader-hidden"))
                    return;

                $btnIcon.addClass("loading").removeClass("loader-hidden");

                var itemSettings = {
                    item_id: _this.currentItemId,
                    item_depth: _this.currenItemDepth,
                    item_icon_type: $('#pa-megamenu-icon-type').val(),
                    item_icon: $('#premium-icon-field').val(),
                    item_lottie_url: $('#premium-lottie-url').val(),
                    item_badge: $('#premium-badge-text-field').val(),
                    item_badge_bg: $('#premium-badge-bg-field').val(),
                    mega_content_pos: $('#pa-megamenu-position').val(),
                    item_icon_color: $('#premium-icon-color-field').val(),
                    item_badge_color: $('#premium-badge-color-field').val(),
                    mega_content_enabled: $('#pa-megamenu-switcher input').prop('checked'),
                    full_width_mega_content: $('#pa-full-width-switcher input').prop('checked'),
                    mega_content_width: '' === $('#pa-mega-content-width').val() ? '1170px' : $('#pa-mega-content-width').val() + 'px',
                };

                _this.itemsData[_this.currentItemId] = itemSettings;

                $.ajax({
                    url: paMenuSettings.ajaxurl,
                    type: 'POST',
                    data: {
                        action: 'save_pa_menu_item_settings',
                        security: paMenuSettings.nonce,
                        settings: itemSettings
                    },
                    success: function (res) {
                        console.log(res)
                        $btnIcon.removeClass("loading dashicons-admin-generic").addClass("dashicons-yes");

                        $btn.find("span").text('Settings Saved');

                        setTimeout(function () {
                            $btnIcon.addClass("loader-hidden dashicons-admin-generic").removeClass("dashicons-yes");
                            $btn.find("span").text('Save Settings');
                        }, 2000);
                    },
                    error: function (error) {
                        console.log(error);
                    }
                });
            },

            closeModal: function () {
                $('#elementor-template-nav-menu-modal-container').hide();

                // hide depth-0 controls
                $('.premium-setting-container.pa-depth-0-control').addClass('premium-setting-hidden');
            },

            getItemId: function ($item) {
                var id = $($item).attr('id').replace('menu-item-', '');

                return id;
            },

            getItemDepth: function ($item) {
                var depth = $($item).attr('class').match(/menu-item-depth-\d/);

                if (depth.length) {
                    return depth[0].replace('menu-item-depth-', '');
                } else {
                    return 0;
                }
            },

            getTriggerHtml: function ($item) {
                var itemId = this.getItemId($item),
                    itemDepth = this.getItemDepth($item);

                return '<span class="premium-menu-item-settings" data-id="' + itemId + '" data-item-depth="' + itemDepth + '">Premium Menu</span>';

            },
        }

        window.PremiumNavMenuSettings.init();
    });

})(jQuery);