angular.module('symbbTemplateModule', []).
    factory('symbbTemplateHttpInterceptor', function($q, $injector, $timeout) {
        return {
            // On response success
            response: function(response) {
                $timeout(function(){
                    $('.symbb_post_embeded_image_link').magnificPopup({type:'image'});
                    $('.symbb_post_block').each(function(key, element){
                        $(element).find('.signature').each(function(key2, signature){
                            $(element).find('.infos').css('padding-bottom', $(signature).innerHeight() );
                        });
                        var left = $(element).find('.userinfo');
                        var right = $(element).find('.infos');
                        if($(left).innerHeight() > $(right).innerHeight()){
                            $(right).height($(left).innerHeight());
                        } else if($(left).innerHeight() < $(right).innerHeight()){
                            $(left).height($(right).innerHeight());
                        }

                    });
                }, 1);
                // Return the response or promise.
                return response || $q.when(response);
            }
        }
    }).config(['$httpProvider',
        function($httpProvider) {
            $httpProvider.interceptors.push('symbbTemplateHttpInterceptor');
        }]
    );