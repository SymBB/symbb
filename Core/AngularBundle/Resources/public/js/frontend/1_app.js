
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
    'ui.bootstrap.datetimepicker',
    'symbbTemplateModule'
]);

app.config(['$routeProvider', '$interpolateProvider', '$httpProvider', '$locationProvider',
    function($routeProvider, $interpolateProvider, $httpProvider, $locationProvider) {

        //changeing because of twig
        $interpolateProvider.startSymbol('[[').endSymbol(']]');
        //html5 pushState
        $locationProvider.html5Mode(true);

        angularConfig.createAngularRouting($routeProvider);
        //angularConfig.configHook($routeProvider, $interpolateProvider, $httpProvider, $locationProvider);

        // Add the interceptor to the $httpProvider.
        $httpProvider.interceptors.push('symbbApiHttpInterceptor');

    }]
);

app.factory('symbbApiHttpInterceptor', function($q, $injector) {
    return {
        // On request success
        request: function(config) {
            $('.symbb_body').remove();
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

//default controller
symbbControllers.controller('DefaultApiCtrl', ['$scope', '$http', '$routeParams', '$anchorScroll',
    function($scope, $http, $routeParams, $anchorScroll) {
        routingKey = '';
        console.debug('Currently not working, routingKey is missing');
        var route = angularConfig.getSymfonyRoute(routingKey, $routeParams);
        $http.get(route).success(function(data) {
            $.each(data, function(key, value) {
                $scope[key] = value;
            });
        });
        $anchorScroll.scroll();
    }
]);
