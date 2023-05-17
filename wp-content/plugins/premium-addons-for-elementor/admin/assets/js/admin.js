(function ($) {

    "use strict";

    var redHadfontLink = document.createElement('link');
    redHadfontLink.rel = 'stylesheet';
    redHadfontLink.href = 'https://fonts.googleapis.com/css?family=Red Hat Display:100,100italic,200,200italic,300,300italic,400,400italic,500,500italic,600,600italic,700,700italic,800,800italic,900,900italic';
    redHadfontLink.type = 'text/css';
    document.head.appendChild(redHadfontLink);

    var poppinsfontLink = document.createElement('link');
    poppinsfontLink.rel = 'stylesheet';
    poppinsfontLink.href = 'https://fonts.googleapis.com/css?family=Poppins:100,100italic,200,200italic,300,300italic,400,400italic,500,500italic,600,600italic,700,700italic,800,800italic,900,900italic';
    poppinsfontLink.type = 'text/css';
    document.head.appendChild(poppinsfontLink);

    var settings = premiumAddonsSettings.settings;

    window.PremiumAddonsNavigation = function () {

        var self = this,
            $tabs = $(".pa-settings-tab"),
            $elementsTabs = $(".pa-elements-tab");

        self.init = function () {

            if (!$tabs.length) {
                return;
            }

            self.genButtonDisplay();

            self.initNavTabs($tabs);

            self.initElementsTabs($elementsTabs);

            if (settings.isTrackerAllowed) {
                self.getUnusedWidget();
            }

            self.handleElementsActions();

            self.handleSearchField();

            self.handleSettingsSave();

            self.handleRollBack();

            self.handleNewsLetterForm();

            self.handlePaproActions();

        };

        // Handle settings form submission
        self.handleSettingsSave = function () {

            $("#pa-features .pa-section-info-cta input, #pa-modules .pa-switcher input, #pa-modules .pa-section-info-cta input").on(
                'change',
                function () {
                    self.saveElementsSettings('elements');
                }
            )

            $("#pa-ver-control input, #pa-integrations input, #pa-ver-control input, #pa-integrations select").change(
                function () {
                    self.saveElementsSettings('additional');
                }
            );

            $("#pa-integrations input[type=text]").on(
                'keyup',
                function () {
                    self.saveElementsSettings('additional');
                }
            )

        };

        //get unused widgets.
        self.getUnusedWidget = function () {

            if ($(".pa-btn-group .pa-btn-disable").hasClass("active")) {
                $(".pa-btn-group .pa-btn-unused").addClass("dimmed");
            }

            $.ajax(
                {
                    url: settings.ajaxurl,
                    type: 'POST',
                    data: {
                        action: 'pa_get_unused_widgets',
                        security: settings.nonce,
                    },
                    success: function (response) {
                        console.log('unused widgets retrieved');
                        self.unusedElements = response.data;
                    },
                    error: function (err) {
                        console.log(err);
                    }
                }
            );
        };

        // Handle global enable/disable buttons
        self.handleElementsActions = function () {

            $(".pa-elements-filter select").on(
                'change',
                function () {
                    var filter = $(this).val(),
                        $activeTab = $(".pa-switchers-container").not(".hidden");

                    $activeTab.find(".pa-switcher").removeClass("hidden");

                    if ('free' === filter) {
                        $activeTab.find(".pro-element").addClass("hidden");
                    } else if ('pro' === filter) {
                        $activeTab.find(".pa-switcher").not(".pro-element").addClass("hidden");
                    }
                }
            );

            $(".pa-elements-filter input").on(
                'keyup',
                function () {
                    var filter = $(this).val(),
                        $activeTab = $(".pa-switchers-container").not(".hidden"),
                        currentQuerySwitchers = $activeTab.find(".pa-switcher");

                    currentQuerySwitchers.addClass("hidden");
                    var searchResults = currentQuerySwitchers.filter(function (index, switcher) {
                        var elementName = $(switcher).find(".pa-element-name").text().toLowerCase();

                        return -1 != elementName.indexOf(filter.toLowerCase()) ? $(switcher) : '';
                    });

                    searchResults.removeClass("hidden");
                }
            );

            // Enable/Disable all widgets
            $(".pa-btn-group").on(
                "click",
                '.pa-btn',
                function () {

                    var $btn = $(this),
                        isChecked = $btn.hasClass("pa-btn-enable");

                    if (!$btn.hasClass("active")) {
                        $(".pa-btn-group .pa-btn").removeClass("active");
                        $btn.addClass("active");

                        $.ajax(
                            {
                                url: settings.ajaxurl,
                                type: 'POST',
                                data: {
                                    action: 'pa_save_global_btn',
                                    security: settings.nonce,
                                    isGlobalOn: isChecked
                                }
                            }
                        );

                    }

                    if (isChecked) {
                        $(".pa-btn-group .pa-btn-unused").removeClass("dimmed");
                    } else {
                        $(".pa-btn-group .pa-btn-unused").addClass("dimmed");
                    }

                    $("#pa-modules .pa-switcher input").prop("checked", isChecked);

                    self.saveElementsSettings('elements');

                }
            );

            //Disable unused widgets.
            $(".pa-btn-group").on(
                "click",
                '.pa-btn-unused',
                function () {

                    $.each(self.unusedElements, function (index, selector) {
                        $('#pa-modules .pa-switcher.' + selector).find('input').prop('checked', false);
                    });

                    $(this).addClass('dimmed');

                    self.saveElementsSettings('elements');
                }
            );

            $("#pa-modules .pa-switcher input").on(
                'change',
                function () {
                    var $this = $(this),
                        id = $this.attr('id'),
                        isChecked = $this.prop('checked');

                    $("input[name='" + id + "']").prop('checked', isChecked);
                }
            )

            //Disable unused widgets.
            $(".pa-section-info-cta").on(
                "click",
                '.pa-btn-regenerate',
                function () {

                    var _this = $(this);
                    _this.addClass("loading");

                    $.ajax(
                        {
                            url: settings.ajaxurl,
                            type: 'POST',
                            data: {
                                action: 'pa_clear_cached_assets',
                                security: settings.generate_nonce,
                            },
                            success: function (response) {

                                swal.fire({
                                    title: 'Generated Assets Cleared!',
                                    text: 'Click OK to continue',
                                    type: 'success',
                                    timer: 1500
                                });

                                _this.removeClass("loading");

                            },
                        }
                    );
                }
            );

        };

        self.handleSearchField = function () {

            var urlString = window.location.href,
                url = new URL(urlString);

            var searchInput = url.searchParams.get("search");

            if (!searchInput)
                return;

            $(".pa-elements-filter input").val(searchInput).trigger("keyup");


        }

        // Handle Tabs Elements
        self.initElementsTabs = function ($elem) {

            var $links = $elem.find('a'),
                $sections = $(".pa-switchers-container");

            $sections.eq(0).removeClass("hidden");
            $links.eq(0).addClass("active");

            $links.on(
                'click',
                function (e) {

                    e.preventDefault();

                    var $link = $(this),
                        href = $link.attr('href');

                    // Set this tab to active
                    $links.removeClass("active");
                    $link.addClass("active");

                    // Navigate to tab section
                    $sections.addClass("hidden");
                    $("#" + href).removeClass("hidden");

                }
            );
        };

        // Handle settings tabs
        self.initNavTabs = function ($elem) {

            var $links = $elem.find('a'),
                $lastSection = null;

            $(window).on(
                'hashchange',
                function () {

                    var hash = window.location.hash.match(new RegExp('tab=([^&]*)')),
                        slug = hash ? hash[1] : $links.first().attr('href').replace('#tab=', ''),
                        $link = $('#pa-tab-link-' + slug);

                    if (!$link.length) {
                        return

                    }
                    $links.removeClass('pa-section-active');
                    $link.addClass('pa-section-active');

                    // Hide the last active section
                    if ($lastSection) {
                        $lastSection.hide();
                    }

                    var $section = $('#pa-section-' + slug);
                    $section.css(
                        {
                            display: 'block'
                        }
                    );

                    $lastSection = $section;

                }
            ).trigger('hashchange');

        };

        self.handleRollBack = function () {

            // Rollback button
            $('.pa-rollback-button').on(
                'click',
                function (event) {

                    event.preventDefault();

                    var $this = $(this),
                        href = $this.attr('href');

                    if (!href) {
                        return;
                    }

                    // Show PAPRO stable version if PAPRO Rollback is clicked
                    var isPAPRO = '';
                    if (-1 !== href.indexOf('papro_rollback')) {
                        isPAPRO = 'papro_';
                    }

                    var premiumRollBackConfirm = premiumAddonsSettings.premiumRollBackConfirm;

                    var dialogsManager = new DialogsManager.Instance();

                    dialogsManager.createWidget(
                        'confirm',
                        {
                            headerMessage: premiumRollBackConfirm.i18n.rollback_to_previous_version,
                            message: premiumRollBackConfirm['i18n'][isPAPRO + 'rollback_confirm'],
                            strings: {
                                cancel: premiumRollBackConfirm.i18n.cancel,
                                confirm: premiumRollBackConfirm.i18n.yes,
                            },
                            onConfirm: function () {

                                $this.addClass('loading');

                                location.href = $this.attr('href');

                            }
                        }
                    ).show();
                }
            );

        };

        self.saveElementsSettings = function (action) { //save elements settings changes

            var $form = null;

            if ('elements' === action) {
                $form = $('form#pa-settings, form#pa-features');
                action = 'pa_elements_settings';
            } else {
                $form = $('form#pa-ver-control, form#pa-integrations');
                action = 'pa_additional_settings';
            }

            $.ajax(
                {
                    url: settings.ajaxurl,
                    type: 'POST',
                    data: {
                        action: action,
                        security: settings.nonce,
                        fields: $form.serialize(),
                    },
                    success: function (response) {
                        console.log('settings saved');

                        self.genButtonDisplay();
                    },
                    error: function (err) {
                        console.log(err);
                    }
                }
            );
        }

        self.genButtonDisplay = function () {
            var $form = $('form#pa-settings'),
                searchTerm = 'premium-assets-generator=on',
                indexOfFirst = $form.serialize().indexOf(searchTerm);

            if (indexOfFirst !== -1) {
                $('.pa-btn-generate').show();
            } else {
                $('.pa-btn-generate').hide();
            }
        };

        self.handlePaproActions = function () {

            // Trigger SWAL for PRO elements
            $(".pro-slider").on(
                'click',
                function () {

                    var redirectionLink = " https://premiumaddons.com/pro/?utm_source=wp-menu&utm_medium=wp-dash&utm_campaign=get-pro&utm_term=";

                    Swal.fire(
                        {
                            title: '<span class="pa-swal-head">Get PRO Widgets & Addons<span>',
                            html: 'Supercharge your Elementor with PRO widgets and addons that you won’t find anywhere else.',
                            type: 'warning',
                            showCloseButton: true,
                            showCancelButton: true,
                            cancelButtonText: "More Info",
                            focusConfirm: true,
                            customClass: 'pa-swal',
                        }
                    ).then(
                        function (res) {
                            // Handle More Info button
                            if (res.dismiss === 'cancel') {
                                window.open(redirectionLink + settings.theme, '_blank');
                            }

                        }
                    );
                }
            );

            // Trigger SWAL for White Labeling
            $(".premium-white-label-form.pro-inactive").on(
                'submit',
                function (e) {

                    e.preventDefault();

                    var redirectionLink = " https://premiumaddons.com/pro/?utm_source=wp-menu&utm_medium=wp-dash&utm_campaign=get-pro&utm_term=";

                    Swal.fire(
                        {
                            title: '<span class="pa-swal-head">Enable White Labeling Options<span>',
                            html: 'Premium Addons can be completely re-branded with your own brand name and author details. Your clients will never know what tools you are using to build their website and will think that this is your own tool set. White-labeling works as long as your license is active.',
                            type: 'warning',
                            showCloseButton: true,
                            showCancelButton: true,
                            cancelButtonText: "More Info",
                            focusConfirm: true
                        }
                    ).then(
                        function (res) {
                            // Handle More Info button
                            if (res.dismiss === 'cancel') {
                                window.open(redirectionLink + settings.theme, '_blank');
                            }

                        }
                    );
                }
            );

        };

        self.handleNewsLetterForm = function () {

            $('.pa-newsletter-form').on('submit', function (e) {
                e.preventDefault();

                var email = $("#pa_news_email").val();

                if (checkEmail(email)) {
                    $.ajax(
                        {
                            url: settings.ajaxurl,
                            type: 'POST',
                            data: {
                                action: 'subscribe_newsletter',
                                security: settings.nonce,
                                email: email
                            },
                            beforeSend: function () {
                                console.log("Adding user to subscribers list");
                            },
                            success: function (response) {
                                if (response.data) {
                                    var status = response.data.status;
                                    if (status) {
                                        console.log("User added to subscribers list");
                                        swal.fire({
                                            title: 'Thanks for subscribing!',
                                            text: 'Click OK to continue',
                                            type: 'success',
                                            timer: 1000
                                        });
                                    }

                                }

                            },
                            error: function (err) {
                                console.log(err);
                            }
                        }
                    );
                } else {
                    Swal.fire({
                        type: 'error',
                        title: 'Invalid Email Address...',
                        text: 'Please enter a valid email address!'
                    });
                }

            })

        };

        function checkEmail(emailAddress) {
            var pattern = new RegExp(/^(("[\w-+\s]+")|([\w-+]+(?:\.[\w-+]+)*)|("[\w-+\s]+")([\w-+]+(?:\.[\w-+]+)*))(@((?:[\w-+]+\.)*\w[\w-+]{0,66})\.([a-z]{2,6}(?:\.[a-z]{2})?)$)|(@\[?((25[0-5]\.|2[0-4][\d]\.|1[\d]{2}\.|[\d]{1,2}\.))((25[0-5]|2[0-4][\d]|1[\d]{2}|[\d]{1,2})\.){2}(25[0-5]|2[0-4][\d]|1[\d]{2}|[\d]{1,2})\]?$)/i);
            return pattern.test(emailAddress);
        }

    };

    var instance = new PremiumAddonsNavigation();

    instance.init();

})(jQuery);
