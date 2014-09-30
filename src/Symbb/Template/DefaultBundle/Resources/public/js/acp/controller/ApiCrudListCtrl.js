symbbControllers.controller('ApiCrudListCtrl', ['$scope', '$http', '$routeParams', '$anchorScroll', '$route',
    function($scope, $http, $routeParams, $anchorScroll, $route) {
        var pattern = $route.current.$$route.originalPath;
        var routingKey = angularConfig.getRoutingKeyBasedOnPattern(pattern);
        if(routingKey){
            var route = angularConfig.getSymfonyApiRoute(routingKey, $routeParams);
            if(route){
                $scope.query = function(page){
                    $http.get(route).success(function(response){
                        if(response.success){
                            $.each(response, function(key, value) {
                                $scope[key] = value;
                            });
                        }
                    });
                };
                $scope.query(1);
                $anchorScroll();
            } else {
                console.debug('No Api Route found for: '+routingKey)
            }
        } else {
            console.debug('No configured angular route found for: '+pattern)
        }
    }
]);
