

(function ($) {
    $(window).on('elementor/frontend/init', function () {

        var PremiumTermsCloud = elementorModules.frontend.handlers.Base.extend({

            getDefaultSettings: function () {

                return {
                    selectors: {
                        container: '.premium-tcloud-container',
                        canvas: '.premium-tcloud-canvas',
                        termWrap: '.premium-tcloud-term',
                    },
                }

            },

            getDefaultElements: function () {

                var selectors = this.getSettings('selectors');

                return {
                    $container: this.$element.find(selectors.container),
                    $canvas: this.$element.find(selectors.canvas),
                    $termWrap: this.$element.find(selectors.termWrap),
                }

            },

            bindEvents: function () {
                this.run();
            },

            run: function () {

                var widgetSettings = this.getElementSettings(),
                    $container = this.elements.$container,
                    _this = this,
                    $canvas = this.elements.$canvas;



                if (['shape', 'sphere'].includes(widgetSettings.words_order)) {

                    var computedStyle = getComputedStyle($canvas[0]);

                    $canvas.attr({
                        width: computedStyle.getPropertyValue('--pa-tcloud-width'),
                        height: computedStyle.getPropertyValue('--pa-tcloud-height')
                    });
                }

                setTimeout(function () {

                    if ('shape' === widgetSettings.words_order) {

                        elementorFrontend.waypoint($canvas, function () {
                            _this.renderWordCloud();
                        });

                    } else if ('sphere' === widgetSettings.words_order) {

                        _this.renderWordSphere();

                    } else {
                        _this.handleTermsGrid();
                    }

                    $container.removeClass('premium-tcloud-hidden');
                }, 500);

            },

            renderWordSphere: function () {

                var widgetID = this.getID(),
                    widgetSettings = this.getElementSettings(),
                    $termWrap = this.elements.$termWrap,
                    _this = this;

                var colorScheme = widgetSettings.colors_select;

                if ('custom' === colorScheme && widgetSettings.words_colors) {
                    var colors = widgetSettings.words_colors.split("\n");
                }

                $termWrap.map(function (index, term) {

                    var generatedColor = null;


                    if ('custom' !== colorScheme) {

                        generatedColor = _this.genRandomColor(colorScheme);

                    } else if (widgetSettings.words_colors) {
                        generatedColor = Math.floor(Math.random() * colors.length);

                        generatedColor = colors[generatedColor];
                    }

                    if (generatedColor)
                        $(term).find('.premium-tcloud-term-link').css(('background' === widgetSettings.colors_target ? 'background-' : '') + 'color', generatedColor);

                });

                setTimeout(function () {

                    $('#premium-tcloud-canvas-' + widgetID).tagcanvas({

                        decel: 'yes' === widgetSettings.stop_onDrag ? 0.95 : 1,

                        overlap: false,
                        textColour: null,

                        weight: 'yes' === widgetSettings.sphere_weight,
                        weightFrom: 'data-weight',
                        weightSizeMin: 'yes' === widgetSettings.sphere_weight ? widgetSettings.weight_min.size : 10,
                        weightSizeMax: 'yes' === widgetSettings.sphere_weight ? widgetSettings.weight_max.size : 20,

                        textHeight: widgetSettings.text_height || 15,
                        textFont: widgetSettings.font_family,
                        textWeight: widgetSettings.font_weight,

                        wheelZoom: 'yes' === widgetSettings.wheel_zoom,
                        reverse: 'yes' === widgetSettings.reverse,
                        dragControl: 'yes' === widgetSettings.drag_control,
                        initial: [widgetSettings.start_xspeed.size, widgetSettings.start_yspeed.size],

                        bgColour: 'tag',

                        padding: 'background' === widgetSettings.colors_target ? widgetSettings.sphere_term_padding.size : 0,
                        bgRadius: 'background' === widgetSettings.colors_target ? widgetSettings.sphere_term_radius.size : 0,

                        outlineColour: 'rgba(2,2,2,0)',
                        maxSpeed: 0.03,
                        depth: 0.75

                    }, 'premium-tcloud-terms-container-' + widgetID);
                }, 100);

            },

            handleTermsGrid: function () {

                var widgetSettings = this.getElementSettings(),
                    $termWrap = this.elements.$termWrap,
                    _this = this;

                var colorScheme = widgetSettings.colors_select;

                if ('custom' === colorScheme && widgetSettings.words_colors) {
                    var colors = widgetSettings.words_colors.split("\n");
                }

                $termWrap.map(function (index, term) {

                    var generatedColor = null,
                        fontSize = $(term).find('.premium-tcloud-term-link').css('font-size').replace('px', '');

                    if (widgetSettings.fsize_scale.size > 0)
                        fontSize = parseFloat(fontSize) + ($(term).find('.premium-tcloud-term-link').data('weight') * widgetSettings.fsize_scale.size);

                    if ('custom' !== colorScheme) {
                        generatedColor = _this.genRandomColor(colorScheme, 'grid');

                        var opacities = {
                            original: 'random-light' === colorScheme ? '0.15)' : '80%)',
                            replaced: 'random-light' === colorScheme ? '0.3)' : '100%)'
                        };

                        $(term).get(0).style.setProperty("--tag-hover-color", generatedColor.replace(opacities.original, opacities.replaced));
                        $(term).get(0).style.setProperty("--tag-text-color", 'random-dark' === colorScheme ? '#fff' : generatedColor.replace('42%,0.15)', '35%,100%)'));

                    } else if (widgetSettings.words_colors) {

                        generatedColor = Math.floor(Math.random() * colors.length);

                        generatedColor = colors[generatedColor];

                        $(term).get(0).style.setProperty("--tag-hover-color", generatedColor);

                    }

                    $(term).get(0).style.setProperty("--tag-color", generatedColor);

                    if (widgetSettings.fsize_scale.size > 0)
                        $(term).find('.premium-tcloud-term-link').css('font-size', Math.ceil(fontSize) + 'px');


                    if ('ribbon' === widgetSettings.words_order) {
                        $(term).get(0).style.setProperty("--tag-ribbon-size", (Math.ceil($(term).outerHeight(false)) / 2) + 'px');
                    }


                })



            },

            renderWordCloud: function () {

                var widgetID = this.getID(),
                    widgetSettings = this.getElementSettings(),
                    $container = this.elements.$container,
                    settings = $container.data("chart");

                var wordsArr = settings.wordsArr,
                    colors = [],
                    rotationRatio = rotationSteps = null,
                    minRot = -90 * (Math.PI / 180),
                    maxRot = 90 * (Math.PI / 180);


                switch (widgetSettings.rotation_select) {

                    case 'horizontal':
                        rotationRatio = 0;
                        rotationSteps = 0;
                        break;

                    case 'vertical':
                        rotationRatio = 1;
                        rotationSteps = 2;
                        break;

                    case 'hv':
                        rotationRatio = 0.5;
                        rotationSteps = 2;
                        break;

                    case 'custom':
                        rotationRatio = widgetSettings.rotation.size || 0.3;

                        minRot = widgetSettings.degrees.size * (Math.PI / 180) || 45;
                        maxRot = widgetSettings.degrees.size * (Math.PI / 180) || 45;

                        break;

                    case 'random':
                        rotationRatio = Math.random();
                        rotationSteps = 0;

                        break;

                    default:
                        rotationRatio = 0.3;
                        break;
                }


                if ('custom' === widgetSettings.colors_select) {
                    colors = widgetSettings.words_colors.split("\n");
                }

                WordCloud(document.getElementById('premium-tcloud-canvas-' + widgetID),
                    {

                        backgroundColor: 'rgba(0, 0, 0, 0)',
                        shuffle: false,

                        list: wordsArr,
                        shape: widgetSettings.shape,
                        color: widgetSettings.colors_select,
                        wordsColors: colors,

                        wait: (widgetSettings.interval.size * 1000) || 0,

                        gridSize: widgetSettings.grid_size.size || 8,

                        weightFactor: widgetSettings.weight_scale || 5,

                        minRotation: minRot,
                        maxRotation: maxRot,

                        rotateRatio: rotationRatio,
                        rotationSteps: rotationSteps,

                        fontFamily: widgetSettings.font_family || 'Arial',
                        fontWeight: widgetSettings.font_weight,

                        click: function (item) {

                            if (!elementorFrontend.isEditMode()) {
                                var link = item[2];

                                window.open(link, 'yes' === widgetSettings.new_tab ? '_blank' : '_top');
                            }
                        }

                        // minSize: 10
                        // rotationSteps: 90
                    }
                );



            },

            genRandomColor: function (scheme, shape) {

                var min = 50,
                    max = 90;

                if ('random-dark' === scheme) {
                    min = 10;
                    max = 50;
                }

                var lightandOpacity = (Math.random() * (max - min) + min).toFixed() + '%, 100%';
                if (shape) {
                    lightandOpacity = '42%,' + ('random-dark' === scheme ? '80%' : '0.15');
                }


                return 'hsla(' +
                    (Math.random() * 360).toFixed() + ',' +
                    '100%,' +
                    lightandOpacity + ')';

            },

            genRandomRotate: function () {

                return Math.floor(Math.random() * 361);

            },


        });

        elementorFrontend.elementsHandler.attachHandler('premium-tcloud', PremiumTermsCloud);
    });

})(jQuery);
