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
                    console.debug(urlValue+' == '+pattern);
                    if( urlValue == pattern){
                        finalKey = key;
                    }
                });
            }
        });
        return finalKey;
    }
};