var mod = angular.module('symbbTemplateModule', [])


mod.factory('symbbTemplateHttpInterceptor', function($q, $injector, $timeout) {
    return {
        // On response success
        response: function(response) {
            $timeout(function(){
                $('.symbb_post_embeded_image_link').magnificPopup({type:'image'});
            }, 1);
            // Return the response or promise.
            return response || $q.when(response);
        }
    }
});

mod.config(['$httpProvider',
    function($httpProvider) {
        $httpProvider.interceptors.push('symbbTemplateHttpInterceptor');
    }]
);