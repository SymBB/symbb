symbbControllers.controller('SiteNavigationListCtrl', ['$scope', '$http', '$routeParams', '$anchorScroll', '$route',
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
