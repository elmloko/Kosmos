(function ($) {
    $(window).on('elementor/frontend/init', function () {

        var premiumWrapperLinkHandler = function ($scope) {

            $scope.on('click.onWrapperLink', function () {

                var settings = $scope.data('premium-element-link');

                if (!settings)
                    return;

                var id = $scope.data('id'),
                    anchor = document.createElement('a'),
                    anchorReal,
                    timeout;

                anchor.id = 'premium-wrapper-link-' + id;
                anchor.href = settings.type === 'url' ? settings.link.url : settings.existingPage;
                anchor.target = settings.type === 'url' ? settings.link.is_external ? '_blank' : '_self' : '';
                anchor.rel = settings.type === 'url' ? settings.link.nofollow ? 'nofollow noreferer' : '' : '';
                anchor.style.display = 'none';

                document.body.appendChild(anchor);

                anchorReal = document.getElementById(anchor.id);
                anchorReal.click();

                timeout = setTimeout(function () {
                    anchorReal.remove();
                    clearTimeout(timeout);
                });
            });

        };

        elementorFrontend.hooks.addAction("frontend/element_ready/global", premiumWrapperLinkHandler);


    });
})(jQuery);