(function () {
    var $ = jQuery;

    var pinterestToken = null;

    elementor.channels.editor.on('change', function (view) {
        var changed = view.elementSettingsModel.changed;

        if (changed.access_token) {
            if (changed.access_token.startsWith('pina_'))
                pinterestToken = changed.access_token;
        }
    });

    function onSectionActivate(sectionName) {

        if ('access_credentials_section' === sectionName) {

            setTimeout(function () {

                var accessToken = jQuery('.elementor-control-access_token textarea').val();

                pinterestToken = accessToken;

            }, 100);

        }


    }


    elementor.channels.editor.on('section:activated', onSectionActivate);

    var selectOptions = elementor.modules.controls.Select2.extend({

        isUpdated: false,

        onReady: function () {

            var options = (0 === this.model.get('options').length);

            if (this.container && "widget" === this.container.type && 'board_id' === this.model.get('name')) {
                if (options) {

                    var _this = this;

                    if (pinterestToken) {

                        jQuery.ajax({
                            type: "GET",
                            url: PremiumSettings.ajaxurl,
                            dataType: "JSON",
                            data: {
                                action: "get_pinterest_boards",
                                nonce: PremiumSettings.nonce,
                                token: pinterestToken
                            },
                            success: function (res) {

                                if (res.data) {

                                    var options = JSON.parse(res.data);

                                    _this.model.set("options", options);

                                    _this.isUpdated = false;

                                    _this.render();

                                }
                            },
                            error: function (err) {
                                console.log(err);
                            }
                        });
                    }

                    elementor.channels.editor.on('change', function (view) {
                        var changed = view.elementSettingsModel.changed;

                        if (undefined !== changed.board_id && !_this.isUpdated) {
                            _this.isUpdated = true;
                        }
                    });

                }
            }
        },

        onBeforeRender: function () {

            if (this.container && ("section" === this.container.type || "container" === this.container.type)) {
                var widgetObj = elementor.widgetsCache || elementor.config.widgets,
                    optionsToUpdate = {};

                var _this = this;
                this.container.children.forEach(function (child) {

                    if ("container" === _this.container.type) {

                        if (child.view.$childViewContainer) {
                            getInnerWidgets(child);
                        } else {
                            //Get Flex Container widgets when no columns are added.
                            var name = child.view.$el.data("widget_type").split('.')[0];

                            if ('undefined' !== typeof widgetObj[name]) {
                                optionsToUpdate[".elementor-widget-" + widgetObj[name].widget_type + " .elementor-widget-container"] = widgetObj[name].title;
                            }
                        }

                    } else if ("section" === _this.container.type) {
                        getInnerWidgets(child);
                    }

                });

                function getInnerWidgets(child) {
                    child.view.$childViewContainer.children("[data-widget_type]").each(function (index, widget) {
                        var name = $(widget).data("widget_type").split('.')[0];

                        if ('undefined' !== typeof widgetObj[name]) {
                            optionsToUpdate[".elementor-widget-" + widgetObj[name].widget_type + " .elementor-widget-container"] = widgetObj[name].title;
                        }
                    });

                }

                this.model.set("options", optionsToUpdate);
            }
        },
    });

    elementor.addControlView("premium-select", selectOptions);

    var filterOptions = elementor.modules.controls.Select2.extend({

        isUpdated: false,

        onReady: function () {
            var self = this,
                type = self.options.elementSettingsModel.attributes.post_type_filter;

            if ('post' !== type) {
                var options = (0 === this.model.get('options').length);

                if (options) {
                    self.fetchData(type);
                }
            }

            elementor.channels.editor.on('change', function (view) {
                var changed = view.elementSettingsModel.changed;

                if (undefined !== changed.post_type_filter && 'post' !== changed.post_type_filter && !self.isUpdated) {
                    self.isUpdated = true;
                    self.fetchData(changed.post_type_filter);
                }
            });
        },

        fetchData: function (type) {
            var self = this;
            $.ajax({
                url: PremiumSettings.ajaxurl,
                dataType: 'json',
                type: 'POST',
                data: {
                    nonce: PremiumSettings.nonce,
                    action: 'premium_update_filter',
                    post_type: type
                },
                success: function (res) {
                    self.updateFilterOptions(JSON.parse(res.data));
                    self.isUpdated = false;

                    self.render();
                },
                error: function (err) {
                    console.log(err);
                },
            });
        },

        updateFilterOptions: function (options) {
            this.model.set("options", options);
        },

        onBeforeDestroy: function () {
            if (this.ui.select.data('select2')) {
                // this.ui.select.select2('destroy');
            }

            this.$el.remove();
        }
    });

    elementor.addControlView("premium-post-filter", filterOptions);

    var taxOptions = elementor.modules.controls.Select.extend({

        isUpdated: false,

        onReady: function () {
            var self = this,
                type = self.options.elementSettingsModel.attributes.post_type_filter,
                options = (0 === this.model.get('options').length);

            if (options) {
                self.fetchData(type);
            }

            elementor.channels.editor.on('change', function (view) {
                var changed = view.elementSettingsModel.changed;

                if (undefined !== changed.post_type_filter && !self.isUpdated) {
                    self.isUpdated = true;
                    self.fetchData(changed.post_type_filter);
                }
            });
        },

        fetchData: function (type) {
            var self = this;
            $.ajax({
                url: PremiumSettings.ajaxurl,
                dataType: 'json',
                type: 'POST',
                data: {
                    nonce: PremiumSettings.nonce,
                    action: 'premium_update_tax',
                    post_type: type
                },
                success: function (res) {

                    var options = JSON.parse(res.data);
                    self.updateTaxOptions(options);
                    self.isUpdated = false;

                    if (0 !== options.length) {

                        self.$el.removeClass('elementor-hidden-control');

                        $('.premium-live-temp-title').addClass('control-hidden');

                        // var $tax = Object.keys(options);
                        // self.container.settings.setExternalChange({ 'filter_tabs_type': $tax[0] });
                        self.container.render();
                        self.render();
                    } else {
                        self.$el.addClass('elementor-hidden-control');

                        $('.premium-live-temp-title.control-hidden').removeClass('control-hidden');
                    }
                },
                error: function (err) {
                    console.log(err);
                },
            });
        },

        updateTaxOptions: function (options) {

            this.model.set("options", options);

        },
    });

    elementor.addControlView("premium-tax-filter", taxOptions);

    var acfOptions = elementor.modules.controls.Select2.extend({

        isUpdated: false,

        onReady: function () {
            var self = this;

            if (!self.isUpdated) {
                self.fetchData();
            }
        },

        fetchData: function () {
            var self = this;

            $.ajax({
                url: PremiumSettings.ajaxurl,
                dataType: 'json',
                type: 'POST',
                data: {
                    nonce: PremiumSettings.nonce,
                    action: 'pa_acf_options',
                    query_options: self.model.get('query_options'),
                },
                success: function (res) {
                    self.isUpdated = true;
                    self.updateAcfOptions(JSON.parse(res.data));
                    self.render();
                },
                error: function (err) {
                    console.log(err);
                },
            });
        },

        updateAcfOptions: function (options) {
            this.model.set("options", options);
        },

        onBeforeDestroy: function () {
            if (this.ui.select.data('select2')) {
                this.ui.select.select2('destroy');
            }

            this.$el.remove();
        }
    });

    elementor.addControlView("premium-acf-selector", acfOptions);

    elementor.hooks.addFilter("panel/elements/regionViews", function (panel) {

        if (PremiumPanelSettings.papro_installed || PremiumPanelSettings.papro_widgets.length <= 0)
            return panel;


        var paWidgetsPromoHandler, proCategoryIndex,
            elementsView = panel.elements.view,
            categoriesView = panel.categories.view,
            widgets = panel.elements.options.collection,
            categories = panel.categories.options.collection,
            premiumProCategory = [];

        _.each(PremiumPanelSettings.papro_widgets, function (widget, index) {
            widgets.add({
                name: widget.key,
                title: wp.i18n.__('Premium ', 'premium-addons-for-elementor') + widget.title,
                icon: widget.icon,
                categories: ["premium-elements-pro"],
                editable: false
            })
        });

        widgets.each(function (widget) {
            "premium-elements-pro" === widget.get("categories")[0] && premiumProCategory.push(widget)
        });

        proCategoryIndex = categories.findIndex({
            name: "premium-elements"
        });

        proCategoryIndex && categories.add({
            name: "premium-elements-pro",
            title: "Premium Addons Pro",
            defaultActive: !1,
            items: premiumProCategory
        }, {
            at: proCategoryIndex + 1
        });


        paWidgetsPromoHandler = {
            className: function () {

                var className = 'elementor-element-wrapper';

                if (!this.isEditable()) {
                    className += ' elementor-element--promotion';
                }

                if (this.model.get("name")) {
                    if (0 === this.model.get("name").indexOf("premium-"))
                        className += ' premium-promotion-element';
                }

                return className;

            },

            isPremiumWidget: function () {
                return 0 === this.model.get("name").indexOf("premium-");
            },

            getElementObj: function (key) {

                var widgetObj = PremiumPanelSettings.papro_widgets.find(function (widget, index) {
                    if (widget.key == key)
                        return true;
                });

                return widgetObj;

            },

            onMouseDown: function () {

                if (!this.isPremiumWidget())
                    return;

                void this.constructor.__super__.onMouseDown.call(this);

                var widgetObject = this.getElementObj(this.model.get("name")),
                    actionURL = widgetObject.action_url;

                elementor.promotion.showDialog({
                    title: sprintf(wp.i18n.__('%s', 'elementor'), this.model.get("title")),
                    content: sprintf(wp.i18n.__('Use %s widget and dozens more pro features to extend your toolbox and build sites faster and better.', 'elementor'), this.model.get("title")),
                    top: "-7",
                    targetElement: this.$el,
                    actionButton: {
                        url: actionURL,
                        text: wp.i18n.__('See Demo', 'elementor')
                    }
                })
            }
        }


        panel.elements.view = elementsView.extend({
            childView: elementsView.prototype.childView.extend(paWidgetsPromoHandler)
        });

        panel.categories.view = categoriesView.extend({
            childView: categoriesView.prototype.childView.extend({
                childView: categoriesView.prototype.childView.prototype.childView.extend(paWidgetsPromoHandler)
            })
        });

        return panel;


    });

    var onNavigatorInit = function () {

        elementor.navigator.indicators.paDisConditions = {
            icon: 'preview-medium',
            settingKeys: ['pa_display_conditions_switcher'],
            title: wp.i18n.__('Display Conditions', 'premium-addons-for-elementor'),
            section: 'section_pa_display_conditions'
        };
    }

    elementor.on('navigator:init', onNavigatorInit);


    var e = elementor.modules.controls.BaseData,
        imageChoose = e.extend({
            ui: function () {
                var t = e.prototype.ui.apply(this, arguments);
                return t.inputs = '[type="radio"]', t
            },
            events: function () {
                return _.extend(e.prototype.events.apply(this, arguments), {
                    "mousedown label": "onMouseDownLabel",
                    "click @ui.inputs": "onClickInput",
                    "change @ui.inputs": "onBaseInputChange"
                })
            },

            onMouseDownLabel: function (e) {
                var t = this.$(e.currentTarget),
                    o = this.$("#" + t.attr("for"));

                $('.elementor-control-form_insert .elementor-button').css('background-color', '#252c59');
                o.data("checked", o.prop("checked")), this.ui.inputs.removeClass("checked"), o.data("checked", o.addClass("checked"))
            },

            onClickInput: function (e) {
                if (this.model.get("toggle")) {
                    var t = this.$(e.currentTarget);
                    t.data("checked") && t.prop("checked", !1).trigger("change")
                }
            },

            onRender: function () {
                e.prototype.onRender.apply(this, arguments);
                var t = this.getControlValue();
                t && (this.ui.inputs.filter('[value="' + t + '"]').prop("checked", !0), this.ui.inputs.filter('[value="' + t + '"]').addClass("checked"))
            },
            onReady: function() {
                if ( 'premium_gdivider_defaults' === this.model.attributes.name) {
                    const choicesContainer = $(this.el).find('.elementor-image-choices')[0];
                    new PerfectScrollbar (  choicesContainer, {
                        suppressScrollX: true,
                    });

                }
            }

        }, {
            onPasteStyle: function (e, t) {
                return "" === t || undefined !== e.options[t]
            }
        });

    elementor.addControlView("premium-image-choose", imageChoose)

})(jQuery);