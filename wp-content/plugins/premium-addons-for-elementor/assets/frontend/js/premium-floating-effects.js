(function ($) {

    if ('undefined' == typeof window.paCheckSafari) {
        window.paCheckSafari = checkSafariBrowser();

        function checkSafariBrowser() {

            var iOS = /iP(hone|ad|od)/i.test(navigator.userAgent) && !window.MSStream;

            if (iOS) {
                var allowedBrowser = /(Chrome|CriOS|OPiOS|FxiOS)/.test(navigator.userAgent);

                if (!allowedBrowser) {
                    var isFireFox = '' === navigator.vendor;
                    allowedBrowser = allowedBrowser || isFireFox;
                }

                var isSafari = /WebKit/i.test(navigator.userAgent) && !allowedBrowser;

            } else {
                var isSafari = /^((?!chrome|android).)*safari/i.test(navigator.userAgent);
            }

            if (isSafari) {
                return true;
            }

            return false;
        }

    }

    $(window).on('elementor/frontend/init', function () {

        var PremiumFloatingEffectsHandler = function ($scope) {

            if (!$scope.hasClass("premium-floating-effects-yes")) {
                return;
            }

            if ($scope.hasClass("premium-disable-fe-yes")) {
                if (window.paCheckSafari)
                    return;
            }

            var target = $scope,
                widgetId = target.data("model-cid"),
                settings = {},
                $widgetContainer = $scope.find('.elementor-widget-container')[0],
                isInnerSection = $scope.closest('.elementor-section').hasClass('elementor-inner-section'),
                sectionId = (isInnerSection) ? $scope.closest('.elementor-inner-section').data("model-cid") : getSectionID(),
                colId = (isInnerSection) ? $scope.closest('.elementor-inner-column').data("model-cid") : getColumnID(),
                editMode = elementorFrontend.isEditMode() && !target.data('general_settings');

            if (editMode) {
                settings = generateEditorSettings(sectionId);
            } else {
                settings = generatePreviewSettings();
            }

            if (!settings) {
                return false;
            }

            elementorFrontend.waypoint(
                $scope,
                function () {
                    applyEffects();
                }
            );


            function generateEditorSettings() {

                var editorElements = null;

                if (!window.elementor.hasOwnProperty("elements")) {
                    return false;
                }

                editorElements = window.elementor.elements.models;

                if (isInnerSection) {
                    var innerSecInfo = {
                        'col': $scope.closest('.elementor-top-column'),
                        'colId': $scope.closest('.elementor-top-column').data('model-cid'),
                        'sec': $scope.closest('.elementor-top-section'),
                        'secId': $scope.closest('.elementor-top-section').data('model-cid'),
                    },
                        sectionModels = getModelsArray(editorElements, innerSecInfo.secId),
                        columnModels = getModelsArray(sectionModels, innerSecInfo.colId);

                    editorElements = columnModels;
                }

                if (!editorElements) {
                    return false;
                }

                var cols = getModelsArray(editorElements, sectionId),
                    widgetcol = getModelsArray(cols, colId),
                    widgetData = getWidgetData(widgetcol, widgetId);

                if (!widgetData)
                    return false;

                if ('yes' !== widgetData.premium_fe_switcher || 0 === widgetData.length) {
                    return false;
                }

                var $easing = widgetData.premium_fe_easing;

                if (widgetData.premium_fe_easing === 'steps') {
                    $easing = 'steps(' + widgetData.premium_fe_ease_step + ')';
                }

                var general_settings = {
                    'target': widgetData.premium_fe_target,
                    'direction': widgetData.premium_fe_direction,
                    'loop': (widgetData.premium_fe_loop === 'default') ? true : widgetData.premium_fe_loop_number,
                    'easing': $easing,
                };

                settings.general = general_settings;

                if ('yes' === widgetData.premium_fe_translate_switcher) {

                    var translate_settings = {
                        'x_param_from': widgetData.premium_fe_Xtranslate.sizes.from,
                        'x_param_to': widgetData.premium_fe_Xtranslate.sizes.to,
                        'y_param_from': widgetData.premium_fe_Ytranslate.sizes.from,
                        'y_param_to': widgetData.premium_fe_Ytranslate.sizes.to,
                        'duration': widgetData.premium_fe_trans_duration.size,
                        'delay': widgetData.premium_fe_trans_delay.size,
                    };

                    settings.translate = translate_settings;
                }

                if ('yes' === widgetData.premium_fe_rotate_switcher) {

                    var rotate_settings = {
                        'x_param_from': widgetData.premium_fe_Xrotate.sizes.from,
                        'x_param_to': widgetData.premium_fe_Xrotate.sizes.to,
                        'y_param_from': widgetData.premium_fe_Yrotate.sizes.from,
                        'y_param_to': widgetData.premium_fe_Yrotate.sizes.to,
                        'z_param_from': widgetData.premium_fe_Zrotate.sizes.from,
                        'z_param_to': widgetData.premium_fe_Zrotate.sizes.to,
                        'duration': widgetData.premium_fe_rotate_duration.size,
                        'delay': widgetData.premium_fe_rotate_delay.size,
                    };

                    settings.rotate = rotate_settings;
                }

                if ('yes' === widgetData.premium_fe_scale_switcher) {

                    var scale_settings = {
                        'x_param_from': widgetData.premium_fe_Xscale.sizes.from,
                        'x_param_to': widgetData.premium_fe_Xscale.sizes.to,
                        'y_param_from': widgetData.premium_fe_Yscale.sizes.from,
                        'y_param_to': widgetData.premium_fe_Yscale.sizes.to,
                        'duration': widgetData.premium_fe_scale_duration.size,
                        'delay': widgetData.premium_fe_scale_delay.size,
                    };

                    settings.scale = scale_settings;
                }

                if ('yes' === widgetData.premium_fe_skew_switcher) {

                    var skew_settings = {
                        'x_param_from': widgetData.premium_fe_Xskew.sizes.from,
                        'x_param_to': widgetData.premium_fe_Xskew.sizes.to,
                        'y_param_from': widgetData.premium_fe_Yskew.sizes.from,
                        'y_param_to': widgetData.premium_fe_Yskew.sizes.to,
                        'duration': widgetData.premium_fe_trans_duration.size,
                        'delay': widgetData.premium_fe_trans_delay.size,
                    };

                    settings.skew = skew_settings;
                }

                if (PremiumFESettings.papro_installed) {
                    if ('yes' === widgetData.premium_fe_opacity_switcher) {

                        var opacity_settings = {
                            'from': widgetData.premium_fe_opacity.sizes.from / 100,
                            'to': widgetData.premium_fe_opacity.sizes.to / 100,
                            'duration': widgetData.premium_fe_opacity_duration.size,
                            'delay': widgetData.premium_fe_opacity_delay.size,
                        };

                        settings.opacity = opacity_settings;
                    }

                    if ('yes' === widgetData.premium_fe_bg_color_switcher) {

                        var bg_color_settings = {
                            'from': widgetData.premium_fe_bg_color_from,
                            'to': widgetData.premium_fe_bg_color_to,
                            'duration': widgetData.premium_fe_bg_color_duration.size,
                            'delay': widgetData.premium_fe_bg_color_delay.size,
                        };

                        settings.bg_color = bg_color_settings;
                    }

                    if ('yes' === widgetData.premium_fe_blur_switcher) {

                        var blur_settings = {
                            'from': 'blur(' + widgetData.premium_fe_blur_val.sizes.from + 'px)',
                            'to': 'blur(' + widgetData.premium_fe_blur_val.sizes.to + 'px)',
                            'duration': widgetData.premium_fe_blur_duration.size,
                            'delay': widgetData.premium_fe_blur_delay.size,
                        };

                        settings.blur = blur_settings;
                    }

                    if ('yes' === widgetData.premium_fe_contrast_switcher) {

                        var contrast_settings = {
                            'from': 'contrast(' + widgetData.premium_fe_contrast_val.sizes.from + '%)',
                            'to': 'contrast(' + widgetData.premium_fe_contrast_val.sizes.to + '%)',
                            'duration': widgetData.premium_fe_contrast_duration.size,
                            'delay': widgetData.premium_fe_contrast_delay.size,
                        };

                        settings.contrast = contrast_settings;
                    }

                    if ('yes' === widgetData.premium_fe_gScale_switcher) {

                        var gScale_settings = {
                            'from': 'grayscale(' + widgetData.premium_fe_gScale_val.sizes.from + '%)',
                            'to': 'grayscale(' + widgetData.premium_fe_gScale_val.sizes.to + '%)',
                            'duration': widgetData.premium_fe_gScale_duration.size,
                            'delay': widgetData.premium_fe_gScale_delay.size,
                        };

                        settings.gScale = gScale_settings;
                    }

                    if ('yes' === widgetData.premium_fe_hue_switcher) {

                        var hue_settings = {
                            'from': 'hue-rotate(' + widgetData.premium_fe_hue_val.sizes.from + 'deg)',
                            'to': 'hue-rotate(' + widgetData.premium_fe_hue_val.sizes.to + 'deg)',
                            'duration': widgetData.premium_fe_hue_duration.size,
                            'delay': widgetData.premium_fe_hue_delay.size,
                        };

                        settings.hue = hue_settings;
                    }

                    if ('yes' === widgetData.premium_fe_brightness_switcher) {

                        var brightnses_settings = {
                            'from': 'brightness(' + widgetData.premium_fe_brightness_val.sizes.from + '%)',
                            'to': 'brightness(' + widgetData.premium_fe_brightness_val.sizes.to + '%)',
                            'duration': widgetData.premium_fe_brightness_duration.size,
                            'delay': widgetData.premium_fe_brightness_delay.size,
                        };

                        settings.bright = brightnses_settings;
                    }

                    if ('yes' === widgetData.premium_fe_saturate_switcher) {

                        var saturate_settings = {
                            'from': 'saturate(' + widgetData.premium_fe_saturate_val.sizes.from + '%)',
                            'to': 'saturate(' + widgetData.premium_fe_saturate_val.sizes.to + '%)',
                            'duration': widgetData.premium_fe_saturate_duration.size,
                            'delay': widgetData.premium_fe_saturate_delay.size,
                        };

                        settings.saturate = saturate_settings;
                    }

                }

                if (0 !== Object.keys(settings).length) {
                    return settings;
                }

                return false;
            }

            function getSectionID() {

                var sectionID = null;

                if (elementorFrontend.config.experimentalFeatures.container) {
                    sectionID = $scope.closest('.e-con').data("model-cid");
                }

                if (!sectionID)
                    sectionID = $scope.closest('.elementor-top-section').data("model-cid");

                return sectionID;

            }

            function getColumnID() {

                var columnID = null;

                if (elementorFrontend.config.experimentalFeatures.container) {
                    columnID = $scope.closest('.e-container--column').data("model-cid");
                }

                if (!columnID)
                    columnID = $scope.closest('.elementor-top-column').data("model-cid");

                return columnID;

            }

            /**
             * @param array         $arr    array to search in
             * @param string        $index  model-cid to match
             *
             * @return Array        contains section models aka cols
             */
            function getModelsArray($arr, $index) {

                if (!$arr)
                    return;

                var widgetIndex = $arr.findIndex(function (element) {
                    return (element.cid == $index);
                });

                if (!$arr[widgetIndex])
                    return;

                return $arr[widgetIndex].attributes.elements.models;
            }

            /**
             * @param array         $arr
             * @param string        $index
             *
             * @return object       contains widget settings
             */
            function getWidgetData($arr, $index) {

                if (!$arr)
                    return;

                var widgetIndex = $arr.findIndex(function (element) {
                    return (element.cid === $index);
                });

                if (!$arr[widgetIndex])
                    return;

                return $arr[widgetIndex].attributes.settings.attributes;
            }

            function generatePreviewSettings() {

                var generalSettings = target.data("general_settings");

                var effectSettings = {
                    translateSettings: target.data("translate_effect"),
                    rotateSettings: target.data("rotate_effect"),
                    scaleSettings: target.data("scale_effect"),
                    skewSettings: target.data("skew_effect"),
                    opacitySettings: target.data("opacity_effect"),
                    bgColorSettings: target.data("bg_color_effect"),
                    bRadiusSettings: target.data("b_radius_effect"),
                    hueSettings: target.data("hue_effect"),
                    gScaleSettings: target.data("gray_effect"),
                    contrastSettings: target.data("contrast_effect"),
                    blurSettings: target.data("blur_effect"),
                    brightSettings: target.data("brightness_effect"),
                    saturateSettings: target.data("saturate_effect")
                }

                //make sure that at least 1 setting exists
                var settingVals = Object.values(effectSettings);

                var safe = settingVals.findIndex(function (element) {
                    return (element !== undefined);
                });

                if (-1 === safe) {
                    return false;
                }

                settings.general = generalSettings;
                settings.translate = effectSettings.translateSettings;
                settings.rotate = effectSettings.rotateSettings;
                settings.scale = effectSettings.scaleSettings;
                settings.skew = effectSettings.skewSettings;
                settings.opacity = effectSettings.opacitySettings;
                settings.bg_color = effectSettings.bgColorSettings;
                settings.blur = effectSettings.blurSettings;
                settings.hue = effectSettings.hueSettings;
                settings.gScale = effectSettings.gScaleSettings;
                settings.contrast = effectSettings.contrastSettings;
                settings.bright = effectSettings.brightSettings;
                settings.saturate = effectSettings.saturateSettings;

                if (0 !== Object.keys(settings).length) {
                    return settings;
                }
            }

            function applyEffects() {

                //If the selector does not exists in the current widget, then search in the whole page.
                if (settings.general.target) {
                    var targetSelector = settings.general.target;

                    $widgetContainer = target.find(targetSelector).length > 0 ? '.elementor-element-' + target.data('id') + ' ' + targetSelector : targetSelector;
                }

                var animeSettings = {
                    targets: $widgetContainer,
                    loop: settings.general.loop,
                    direction: settings.general.direction,
                    easing: settings.general.easing,
                };

                if (settings.translate) {
                    var data = settings.translate;
                    x_translate = {
                        value: [data.x_param_from || 0, data.x_param_to || 0],
                        duration: data.duration,
                        delay: data.delay || 0
                    }

                    y_translate = {
                        value: [data.y_param_from || 0, data.y_param_to || 0],
                        duration: data.duration,
                        delay: data.delay || 0,
                    }

                    animeSettings.translateX = x_translate;
                    animeSettings.translateY = y_translate;
                }

                if (settings.rotate) {
                    var data = settings.rotate;
                    x_rotate = {
                        duration: data.duration,
                        delay: data.delay || 0,
                        value: [data.x_param_from || 0, data.x_param_to || 0],
                    }

                    y_rotate = {
                        duration: data.duration,
                        delay: data.delay || 0,
                        value: [data.y_param_from || 0, data.y_param_to || 0],
                    }

                    z_rotate = {
                        duration: data.duration,
                        delay: data.delay || 0,
                        value: [data.z_param_from || 0, data.z_param_to || 0],
                    }

                    animeSettings.rotateX = x_rotate;
                    animeSettings.rotateY = y_rotate;
                    animeSettings.rotateZ = z_rotate;
                }

                if (settings.scale) {
                    var data = settings.scale;
                    x_scale = {
                        value: [data.x_param_from || 0, data.x_param_to || 0],
                        duration: data.duration,
                        delay: data.delay || 0
                    }

                    y_scale = {
                        value: [data.y_param_from || 0, data.y_param_to || 0],
                        duration: data.duration,
                        delay: data.delay || 0,
                    }

                    animeSettings.scaleX = x_scale;
                    animeSettings.scaleY = y_scale;
                }

                if (settings.skew) {
                    var data = settings.skew;
                    x_skew = {
                        value: [data.x_param_from || 0, data.x_param_to || 0],
                        duration: data.duration,
                        delay: data.delay || 0
                    }

                    y_skew = {
                        value: [data.y_param_from || 0, data.y_param_to || 0],
                        duration: data.duration,
                        delay: data.delay || 0,
                    }

                    animeSettings.skewX = x_skew;
                    animeSettings.skewY = y_skew;
                }

                if (settings.opacity) {
                    var data = settings.opacity;

                    animeSettings.opacity = {
                        value: [data.from || 0, data.to || 0],
                        duration: data.duration,
                        delay: data.delay || 0
                    };
                }

                if (settings.bg_color) {
                    var data = settings.bg_color;

                    animeSettings.backgroundColor = {
                        value: [data.from || 0, data.to || 0],
                        duration: data.duration,
                        delay: data.delay || 0
                    };
                }

                var filter_arr = [];

                if (settings.blur) {
                    var data = settings.blur,
                        blurEffect = {
                            value: [data.from || 0, data.to || 0],
                            duration: data.duration,
                            delay: data.delay || 0
                        };

                    filter_arr.push(blurEffect);
                }

                if (settings.gScale) {
                    var data = settings.gScale,
                        gscaleEffect = {
                            value: [data.from || 0, data.to || 0],
                            duration: data.duration,
                            delay: data.delay || 0
                        };

                    filter_arr.push(gscaleEffect);
                }

                if (settings.hue) {
                    var data = settings.hue,
                        hueEffect = {
                            value: [data.from || 0, data.to || 0],
                            duration: data.duration,
                            delay: data.delay || 0
                        };

                    filter_arr.push(hueEffect);
                }

                if (settings.contrast) {
                    var data = settings.contrast,
                        conEffect = {
                            value: [data.from || 0, data.to || 0],
                            duration: data.duration,
                            delay: data.delay || 0
                        };

                    filter_arr.push(conEffect);
                }

                if (settings.bright) {
                    var data = settings.bright,
                        brightness = {
                            value: [data.from || 0, data.to || 0],
                            duration: data.duration,
                            delay: data.delay || 0
                        };

                    filter_arr.push(brightness);
                }

                if (settings.saturate) {
                    var data = settings.saturate,
                        saturateEffect = {
                            value: [data.from || 0, data.to || 0],
                            duration: data.duration,
                            delay: data.delay || 0
                        };

                    filter_arr.push(saturateEffect);
                }

                //add filter settings to animation settings
                if (filter_arr.length !== 0) {
                    animeSettings.filter = filter_arr;
                }

                anime(animeSettings);
            }
        };

        elementorFrontend.hooks.addAction("frontend/element_ready/widget", PremiumFloatingEffectsHandler);

    });

})(jQuery);