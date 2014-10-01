(function(window, angular, undefined) {
    'use strict';
    var symbbRestCrudMod;
    angular.module('symbb-rest-crud', ['ng', 'ngRoute', 'ngResource']).
        factory('$symbbRestCrud', ["$http", "$routeParams", "$anchorScroll", "$route",
        function ($http, $routeParams, $anchorScroll, $route) {
            var service = {
                routingIdField: 'id'
            };
            service.init = function ($scope) {
                var pattern = $route.current.$$route.originalPath;
                var routingKey = angularConfig.getRoutingKeyBasedOnPattern(pattern);
                if (routingKey) {
                    var apiRoutingKey = angularConfig.getSymfonyApiRouteKey(routingKey, $routeParams);
                    var route = angularConfig.getSymfonyRoute(apiRoutingKey, $routeParams);
                    if (route) {

                        $scope.restCrudLoading = false;
                        $scope.restCrudSaving = false;

                        $scope.query = function (page) {
                            $scope.restCrudLoading = true;
                            $http.get(route).success(function (response) {
                                $scope.assignData(response);
                                $scope.restCrudLoading = false;
                            });
                        };

                        $scope.create = function () {
                            $scope.edit();
                        };

                        $scope.edit = function (entry) {
                            $scope.formEntry = {};
                            if (entry) {
                                $scope.formEntry = entry;
                            }
                            $('#restCurdForm').find('.modal').modal('show');
                            $('#restCurdForm').find('.modal-button').unbind("click");
                            $('#restCurdForm').find('.modal-button').click(function () {
                                $scope.save($scope.formEntry);
                            });
                        };

                        $scope.delete = function (entry) {
                            var deleteRoutingKey = apiRoutingKey;
                            deleteRoutingKey = deleteRoutingKey.replace('_list', '_delete');
                            var routeParams = {_locale: $routeParams._locale};
                            routeParams[service.routingIdField] = entry.id;
                            var route = angularConfig.getSymfonyRoute(deleteRoutingKey, routeParams);
                            $http.delete(route).success(function (response) {
                                $scope.restCrudSaving = false;
                                $scope.query(1);
                            }).error(function () {
                                $scope.restCrudSaving = false;
                            })
                        };

                        $scope.save = function (entry) {
                            $scope.restCrudSaving = true;
                            var saveRoutingKey = apiRoutingKey;
                            saveRoutingKey = saveRoutingKey.replace('_list', '_save');
                            var routeParams = {_locale: $routeParams._locale};
                            var route = angularConfig.getSymfonyRoute(saveRoutingKey, routeParams);
                            console.debug(route);
                            $http.post(route, {data: entry}).success(function (response) {
                                $scope.query(1);
                                $('#restCurdForm').find('.modal').modal('hide');
                                $scope.restCrudSaving = false;
                            }).error(function () {
                                $scope.restCrudSaving = false;
                            })
                        };

                        $scope.assignData = function (response) {
                            if (response.success) {
                                $.each(response, function (key, value) {
                                    $scope[key] = value;
                                });
                            }
                        };

                        $scope.query(1);
                        $anchorScroll();


                    } else {
                        console.debug('No Api Route found for: ' + routingKey)
                    }
                } else {
                    console.debug('No configured angular route found for: ' + pattern)
                }
            };
            return service;
        }
    ]);
})(window, window.angular);