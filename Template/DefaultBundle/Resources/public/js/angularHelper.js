// class to manage Routings based on the Configuration of the Backend
// some helper methods
var angularConfig = {

    goTo: function($timeout, $location, route, params, urlKey){
        var routing = this.getAngularRoute(route, params, urlKey, true);
        $timeout(function() {
            $location.path(routing);
        }, 0 );
        //$scope = angular.element(document).scope(); // this is the came as $rootScope
        //$scope.$apply(); // so this also has no effect
    },

    angularRoutes: [],

    getSymfonyRoute: function(route, params){
        if(!params){
            params = {};
        }
        params._locale = symbbUser.lang;
        var routePath =  Routing.generate(route, params);
        return routePath;
    },

    getSymfonyApiRoute: function(route, params){
        var routePath =  '';
        if(this.angularRoutes[route] && this.angularRoutes[route]['api']){
            if(!params){
                params = {};
            }
            var realParams = this.angularRoutes[route]['api']['params'];
            if(!realParams){
                realParams = {};
            }
            $.each(params, function(key, value){
                realParams[key] = value;
            });
            realParams._locale = symbbUser.lang;
            routePath = Routing.generate(this.angularRoutes[route]['api']['route'], realParams);
        }
        return routePath;
    },

    getSymfonyTemplateRoute: function(route, params){
        var routePath =  '';
        if(this.angularRoutes[route] && this.angularRoutes[route]['template']){
            if(!params){
                params = {};
            }
            var realParams = this.angularRoutes[route]['template']['params'];
            if(!realParams){
                realParams = {};
            }
            $.each(params, function(key, value){
                realParams[key] = value;
            });
            realParams._locale = symbbUser.lang;
            routePath = Routing.generate(this.angularRoutes[route]['template']['route'], realParams);
        }
        return routePath;
    },

    getAngularController: function(route){
        return this.angularRoutes[route]['controller'];
    },

    getAngularRoute: function(route, params, urlKey, removeHost){

        if(!params){
            params = {};
        }

        params._locale = symbbUser.lang;

        if(!urlKey){
            urlKey = 0;
        }
        if(this.angularRoutes[route]){
            var routePath = this.angularRoutes[route]['pattern'];
            routePath = routePath[urlKey];
            $.each(this.angularRoutes[route]['defaults'], function(key, value){
                if(!params[key]){
                    params[key] = value;
                }
            });
            $.each(params, function(key, value){
                routePath = routePath.replace(':'+key, value);
            });
            if(!removeHost){
                routePath = 'http://'+window.location.host+routePath;
            }
        } else {
            console.debug('Route not found! -> '+route);
            console.debug(this.angularRoutes);
        }


        return routePath;
    },

    createAngularRouting: function($routeProvider){

        $.each(this.angularRoutes, function(key, value){
            if(value.pattern){
                $.each(value.pattern, function(urlKey, urlValue){
                    if(value.controller){
                        $routeProvider.when(urlValue, {
                            templateUrl: angularConfig.getSymfonyTemplateRoute(key),
                            controller: angularConfig.getAngularController(key)
                        });
                    }
                });
            }
        });

    },

    getRoutingKeyBasedOnPattern: function(pattern){
        var finalKey = '';
        $.each(this.angularRoutes, function(key, value){
            if(value.pattern){
                $.each(value.pattern, function(urlKey, urlValue){
                    if( urlValue == pattern){
                        finalKey = key;
                    }
                });
            }
        });
        return finalKey;
    }
};


// Change Template Symbold
// create dynamicly the Routing based on the provided Data
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

// check every Request for API Errors/Messages
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


// Default Controller for Api and co
var symbbControllers = angular.module('symbbControllers', []);

//default controller
symbbControllers.controller('DefaultApiCtrl', ['$scope', '$http', '$routeParams', '$anchorScroll', '$route',
    function($scope, $http, $routeParams, $anchorScroll, $route) {
        var pattern = $route.current.$$route.originalPath;
        var routingKey = angularConfig.getRoutingKeyBasedOnPattern(pattern);
        if(routingKey){
            var route = angularConfig.getSymfonyApiRoute(routingKey, $routeParams);
            if(route){
                $http.get(route).success(function(data) {
                    $.each(data, function(key, value) {
                        $scope[key] = value;
                    });
                });
                $anchorScroll();
            } else {
                console.debug('No Api Route found for: '+routingKey)
            }
        } else {
            console.debug('No configured angular route found for: '+pattern)
        }
    }
]);