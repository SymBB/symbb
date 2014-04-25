var app = angular.module('app', [
    'ngRoute',
    'ngCookies',
    'ngAnimate',
    'ngSanitize',
    'chieffancypants.loadingBar',
    'infinite-scroll',
    'symbbControllers',
    'angularFileUpload',
    'angularTumb',
    'ui.bootstrap.datetimepicker'
]);

app.config(['$routeProvider', '$interpolateProvider', '$httpProvider',
    function($routeProvider, $interpolateProvider, $httpProvider) {
        
        //changeing because of twig
        $interpolateProvider.startSymbol('[[').endSymbol(']]');
        
        $.each(angularConfig.routingListener, function(key, listener){
            listener($routeProvider);
        });
        
            // Intercept http calls.
        

        // Add the interceptor to the $httpProvider.
        $httpProvider.interceptors.push('symbbApiHttpInterceptor');
        
    }]
);

app.factory('symbbApiHttpInterceptor', function($q, $injector) {
    return {
        // On request success
        request: function(config) {
            // console.log(config); // Contains the data about the request before it is sent.

            // Return the config or wrap it in a promise if blank.
            return config || $q.when(config);
        },
        // On request failure
        requestError: function(rejection) {
            // console.log(rejection); // Contains the data about the error on the request.
            // Return the promise rejection.
            return $q.reject(rejection);
        },
        // On response success
        response: function(response) {
            if(typeof response.data  === 'object'){
                response.data = symbbAngularUtils.checkResponse(response.data, $injector);
            }
            // Return the response or promise.
            return response || $q.when(response);
        },
        // On response failture
        responseError: function(rejection) {
            // console.log(rejection); // Contains the data about the error.
            // Return the promise rejection.
            return $q.reject(rejection);
        }
    };
});

var symbbControllers = angular.module('symbbControllers', []);
