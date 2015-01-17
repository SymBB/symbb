symbbControllers.controller('SiteListCtrl', ['$scope', '$http', '$routeParams', '$anchorScroll', '$route',
    function ($scope, $http, $routeParams, $anchorScroll, $route) {
        var pattern = $route.current.$$route.originalPath;
        var routingKey = angularConfig.getRoutingKeyBasedOnPattern(pattern);
        if (routingKey) {
            var route = angularConfig.getSymfonyApiRoute(routingKey, $routeParams);
            if (route) {
                $http.get(route).success(function (data) {
                    $.each(data, function (key, value) {
                        $scope[key] = value;
                    });
                    $scope.showSite = [];
                    $scope.showSite[0] = 1;
                    $scope.addSite = function () {
                        var site = {};
                        site.id = 0;
                        site.name = "";
                        $scope.data[$scope.data.length] = site;
                    }
                    $scope.siteLoading = [];
                    $scope.saveSite = function (site) {
                        $scope.siteLoading[site.id] = true;
                        routeParams = {
                            _locale: $routeParams._locale,
                            site: site.id
                        };
                        var route = angularConfig.getSymfonyRoute('symbb_backend_api_site_save', routeParams);
                        if (route) {
                            $http.post(route, {data: site}).success(function (data) {
                                $scope.siteLoading[site.id] = false;
                                if (data.success) {
                                    var newSites = [];
                                    $.each($scope.data, function (key, value) {
                                        console.debug(value);
                                        if (value.id == 0) {
                                            value = site;
                                        }
                                        newSites[newSites.length] = value;
                                    });
                                    $scope.data = newSites;
                                }
                            });
                        } else {
                            console.debug('No Api Route found for: symbb_backend_api_site_save')
                        }
                    }
                    $scope.deleteSite = function (site) {
                        $scope.siteLoading[site.id] = true;
                        routeParams = {
                            _locale: $routeParams._locale,
                            site: site.id
                        };
                        var route = angularConfig.getSymfonyRoute('symbb_backend_api_site_delete', routeParams);
                        if (route) {
                            $http.delete(route).success(function (data) {
                                $scope.siteLoading[site.id] = false;
                                if (data.success) {
                                    var newSites = [];
                                    $.each($scope.data, function (key, value) {
                                        if (value.id != site.id) {
                                            newSites[newSites.length] = value;
                                        }
                                    });
                                    $scope.data = newSites;
                                }
                            });
                        } else {
                            console.debug('No Api Route found for: symbb_backend_api_site_save')
                        }
                    }
                });
                $anchorScroll();
            } else {
                console.debug('No Api Route found for: ' + routingKey)
            }
        } else {
            console.debug('No configured angular route found for: ' + pattern)
        }
    }
]);
