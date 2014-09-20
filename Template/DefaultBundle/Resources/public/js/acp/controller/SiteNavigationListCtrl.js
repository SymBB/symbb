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

                $scope.changeSelectedSite = function(){
                    $scope.currSiteData = null;
                    $.each($scope.data, function(key, elm){
                        if(parseInt(elm.id) == parseInt($scope.currSite)){
                            $scope.currSiteData = elm;
                        }
                    });
                };

                $scope.newNavigation = function(){
                    $scope.$parent.navigationForm = {key: 'main', 'site': $scope.currSite};
                    console.debug($scope.currSite);
                    console.debug($scope.$parent.currSite);
                    $('#naviForm').find('.modal').modal('show');
                    $('#naviForm').find('.modal-button').click(function(){
                        $scope.saveNavigation();
                    });
                };
                $scope.editNavigation = function(navi){
                    navi.site = $scope.currSite;
                    $scope.$parent.navigationForm = navi;
                    $('#naviForm').find('.modal').modal('show');
                    $('#naviForm').find('.modal-button').click(function(){
                        $scope.saveNavigation();
                    });
                };
                $scope.deleteNavigation = function(navi){

                };

                $scope.newItem = function(item){

                };
                $scope.editItem = function(item){

                };
                $scope.deleteItem = function(item){

                };

                $scope.saveNavigation = function(){
                    console.debug($scope.$parent.navigationForm.site);
                    if(parseInt($scope.$parent.navigationForm.site) > 0){
                        var route = angularConfig.getSymfonyRoute('symbb_backend_api_site_navigation_save', $routeParams);
                        $http.post(route, {data: $scope.$parent.navigationForm}).success(function(data) {
                            console.debug(data);
                            if(data.success){
                                $scope.data[$scope.data.length] = $scope.$parent.navigationForm;
                            }
                        });
                    }
                };

                $scope.saveItem = function(){

                }

                $anchorScroll();
            } else {
                console.debug('No Api Route found for: '+routingKey)
            }
        } else {
            console.debug('No configured angular route found for: '+pattern)
        }
    }
]);
