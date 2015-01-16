angular.module('symbbTemplateModule', []).
    factory('symbbTemplateHttpInterceptor', function ($q, $injector, $timeout) {
        return {
            // On response success
            response: function (response) {
                $timeout(function () {
                    $('.symbb_post_embeded_image_link').magnificPopup({type: 'image'});
                    $('.symbb_forum_row').each(function (key, element) {
                        $(element).find('.signature').each(function (key2, signature) {
                            $(element).find('.infos').css('padding-bottom', ( $(signature).innerHeight() + 5));
                        });
                        var left = $(element).find('.userinfo');
                        var right = $(element).find('.infos');
                        if ($(left).innerHeight() > $(right).innerHeight()) {
                            $(right).css('min-height', $(left).innerHeight());
                        } else if ($(left).innerHeight() < $(right).innerHeight()) {
                            $(left).css('min-height', $(right).innerHeight());
                        }

                    });
                }, 1);
                // Return the response or promise.
                return response || $q.when(response);
            }
        }
    }).config(['$httpProvider',
        function ($httpProvider) {
            $httpProvider.interceptors.push('symbbTemplateHttpInterceptor');
        }]
);