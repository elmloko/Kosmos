(function () {

    function a(b) {
        return b.forEach(function (b) {
            b.id = elementorCommon.helpers.getUniqueId(), 0 < b.elements.length && a(b.elements)
        }), b
    }
    PACopyPasteHandler = {

        b: function (b, c) {
            var d = c,
                e = c.model.get("elType"),
                f = b.elecode.elType,
                g = b.elecode,
                h = JSON.stringify(g);

            var i = /\.(jpg|png|jpeg|gif|svg)/gi.test(h),
                j = {
                    elType: f,
                    settings: g.settings
                },
                k = null,
                l = {
                    index: 0
                };

            switch (f) {
                case "section":
                case "container":
                    j.elements = a(g.elements), k = elementor.getPreviewContainer();
                    break;
                case "column":
                    j.elements = a(g.elements);
                    ("section" === e || "container" === e) ? k = d.getContainer() : "column" === e ? (k = d.getContainer().parent, l.index = d.getOption("_index") + 1) : "widget" === e ? (k = d.getContainer().parent.parent, l.index = d.getContainer().parent.view.getOption("_index") + 1) : void 0;
                    break;
                case "widget":
                    j.widgetType = b.eletype, k = d.getContainer();
                    ("section" === e) ? k = d.children.findByIndex(0).getContainer() : "column" === e ? k = d.getContainer() : "widget" === e ? (k = d.getContainer().parent, e.index = d.getOption("_index") + 1, l.index = d.getOption("_index") + 1) : void 0;
            }
            var m = $e.run("document/elements/create", {
                model: j,
                container: k,
                options: l
            });
            i && jQuery.ajax({
                url: premium_cross_cp.ajax_url,
                method: "POST",
                data: {
                    nonce: premium_cross_cp.nonce,
                    action: "premium_cross_cp_import",
                    copy_content: h
                }
            }).done(function (a) {
                if (a.success) {
                    var b = a.data[0];
                    j.elType = b.elType, j.settings = b.settings, "widget" === j.elType ? j.widgetType = b.widgetType : j.elements = b.elements, $e.run("document/elements/delete", {
                        container: m
                    }), $e.run("document/elements/create", {
                        model: j,
                        container: k,
                        options: l
                    })
                }
            })
        },
        pasteAll: function (allSections) {
            jQuery.ajax({
                url: premium_cross_cp.ajax_url,
                method: "POST",
                data: {
                    nonce: premium_cross_cp.nonce,
                    action: "premium_cross_cp_import",
                    copy_content: allSections
                },
            }).done(function (e) {
                if (e.success) {
                    var data = e.data[0];
                    if (premium_cross_cp.elementorCompatible) {
                        elementor.sections.currentView.addChildModel(data)
                    } else {
                        elementor.previewView.addChildModel(data)
                    }
                    elementor.notifications.showToast({
                        message: elementor.translate('Content Pasted. Have Fun ;)')
                    });

                }
            }).fail(function () {
                elementor.notifications.showToast({
                    message: elementor.translate('Something went wrong!')
                });
            })
        }

    }

    xdLocalStorage.init({
        iframeUrl: "https://leap13.github.io/pa-cdcp/",
        initCallback: function () { }
    });
    var c = ["section", "column", "widget", "container"],
        d = [];

    elementor.on('preview:loaded', function () {

        c.forEach(function (a, e) {
            elementor.hooks.addFilter("elements/" + c[e] + "/contextMenuGroups", function (a, f) {
                return d.push(f), a.push({
                    name: "premium_" + c[e],
                    actions: [{
                        name: "premium_addons_copy",
                        title: "PA | Copy Element",
                        icon: "pa-dash-icon",
                        callback: function () {
                            var a = {};

                            a.eletype = "widget" == c[e] ? f.model.get("widgetType") : null;
                            a.elecode = f.model.toJSON();

                            xdLocalStorage.setItem("premium-c-p-element", JSON.stringify(a), function () {
                                elementor.notifications.showToast({
                                    message: elementor.translate('Copied')
                                });
                            });


                        }
                    }, {
                        name: "premium_addons_paste",
                        title: "PA | Paste Element",
                        icon: "pa-dash-icon",
                        callback: function () {
                            xdLocalStorage.getItem("premium-c-p-element", function (a) {
                                PACopyPasteHandler.b(JSON.parse(a.value), f)
                            })
                        }
                    },
                    {
                        name: "premium_addons_copy_all",
                        title: "PA | Copy All Content",
                        icon: "pa-dash-icon",
                        callback: function () {
                            var copiedSections = Object.values(elementor.getPreviewView().children._views).map(function (e) {
                                return e.getContainer();
                            });
                            var allSections = copiedSections.map(function (e) {
                                return e.model.toJSON();
                            });
                            xdLocalStorage.setItem('premium-c-p-all', JSON.stringify(allSections), function (a) {
                                elementor.notifications.showToast({
                                    message: elementor.translate('Copied')
                                });
                            });
                        }
                    },
                    {
                        name: "premium_addons_paste_all",
                        title: "PA | Paste All Content",
                        icon: "pa-dash-icon",
                        callback: function () {
                            var allSections = '';
                            xdLocalStorage.getItem('premium-c-p-all', function (a) {
                                allSections = JSON.parse(a.value);
                                PACopyPasteHandler.pasteAll(JSON.stringify(allSections));
                            });
                        }
                    },
                    ]
                }), a
            })
        })

    });
})(jQuery);