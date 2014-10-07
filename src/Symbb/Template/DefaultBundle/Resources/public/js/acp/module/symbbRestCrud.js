(function(window, angular, undefined) {
    'use strict';
    var symbbRestCrudMod;
    angular.module('symbb-rest-crud', ['ng', 'ngRoute', 'ngResource']).
        factory('$symbbRestCrud', ["$http", "$routeParams", "$anchorScroll", "$route",
        function ($http, $routeParams, $anchorScroll, $route) {
            var service = {
                entityIdField: 'id',
                routingIdField: 'id',
                parentIdField: null,
                beforeSave: null,
                beforeEdit: null
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

                        $scope.create = function (parent) {
                            var entry = {};
                            if(service.parentIdField && parent){
                                entry[service.parentIdField] = parent[service.entityIdField];
                            }
                            $scope.edit(entry);
                        };

                        $scope.edit = function (entry) {
                            if(service.beforeEdit){
                                entry = service.beforeEdit(entry);
                            }
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
                            if(!$scope.restCrudSaving){
                                if(confirm("Are you sure!?")){
                                    $scope.restCrudSaving = true;
                                    var deleteRoutingKey = apiRoutingKey;
                                    deleteRoutingKey = deleteRoutingKey.replace('_list', '_delete');
                                    var routeParams = {_locale: $routeParams._locale};
                                    routeParams[service.routingIdField] = entry.id;
                                    var route = angularConfig.getSymfonyRoute(deleteRoutingKey, routeParams);
                                    $http.delete(route).success(function (response) {
                                        if(response.success) {
                                            $scope.query(1);
                                        }
                                        $scope.restCrudSaving = false;
                                    }).error(function () {
                                        $scope.restCrudSaving = false;
                                    })
                                }
                            }
                        };

                        $scope.save = function (entry) {
                            if(!$scope.restCrudSaving) {
                                if(service.beforeSave){
                                    entry = service.beforeSave(entry);
                                }
                                $scope.restCrudSaving = true;
                                var saveRoutingKey = apiRoutingKey;
                                saveRoutingKey = saveRoutingKey.replace('_list', '_save');
                                var routeParams = {_locale: $routeParams._locale};
                                var route = angularConfig.getSymfonyRoute(saveRoutingKey, routeParams);
                                $http.post(route, {data: entry}).success(function (response) {
                                    if(response.success){
                                        $scope.query(1);
                                        $('#restCurdForm').find('.modal').modal('hide');
                                    }
                                    $scope.restCrudSaving = false;
                                }).error(function () {
                                    $scope.restCrudSaving = false;
                                })
                            }
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