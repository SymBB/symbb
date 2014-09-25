symbbControllers.controller('SiteNavigationListCtrl', ['$scope', '$http', '$routeParams', '$anchorScroll', '$route',
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
                });

                $scope.changeSelectedSite = function () {
                    $scope.currSiteData = null;
                    $.each($scope.data, function (key, elm) {
                        if (parseInt(elm.id) == parseInt($scope.currSite)) {
                            $scope.currSiteData = elm;
                        }
                    });
                };

                $scope.newNavigation = function () {
                    $scope.$parent.navigationForm = {key: 'main', 'site': $scope.currSite};
                    $('#naviForm').find('.modal').modal('show');
                    $('#naviForm').find('.modal-button').unbind("click");
                    $('#naviForm').find('.modal-button').click(function () {
                        $scope.saveNavigation();
                    });
                };

                $scope.editNavigation = function (navi) {
                    navi.site = $scope.currSite;
                    $scope.$parent.navigationForm = navi;
                    $('#naviForm').find('.modal').modal('show');
                    $('#naviForm').find('.modal-button').unbind("click");
                    $('#naviForm').find('.modal-button').click(function () {
                        $scope.saveNavigation();
                    });
                };

                $scope.deleteNavigation = function (navi) {
                    var route = angularConfig.getSymfonyRoute('symbb_backend_api_site_navigation_delete', $routeParams);
                    $http.delete(route+'?data='+navi.id).success(function (response) {
                        if (response.success) {
                            $.each($scope.data, function(key, site){
                                var newNavis = [];
                                $.each(site.navigations, function(key2, currNavi){
                                    if(currNavi.id && navi.id && currNavi.id == navi.id){
                                        //$scope.data[key]['navigations'][key2] = null;
                                    } else {
                                        newNavis[newNavis.length] = currNavi;
                                    }
                                });
                                $scope.data[key]['navigations'] = newNavis;
                            });
                        }
                        $scope.saveItemInProgress = false;
                    });
                };

                $scope.newItem = function (navigation, parentItem) {
                    $scope.$parent.navigationItemForm = {
                        type: 'link',
                        navigationId: navigation.id
                    };
                    if(parentItem){
                        $scope.$parent.navigationItemForm.parentItemId = parentItem.id;
                    }
                    $('#naviItemForm').find('.modal').modal('show');
                    $('#naviItemForm').find('.modal-button').unbind("click");
                    $('#naviItemForm').find('.modal-button').bind( "click", {
                        'parentItem': parentItem,
                        'navigation': navigation
                    }, (function (event) {
                        $scope.saveItem(event.data.navigation, event.data.parentItem, {});
                    }));
                };
                $scope.editItem = function (navigation, item) {
                    $scope.$parent.navigationItemForm = item;
                    $('#naviItemForm').find('.modal').modal('show');
                    $('#naviItemForm').find('.modal-button').unbind("click");
                    $('#naviItemForm').find('.modal-button').bind( "click", {
                        'item': item,
                        'navigation': navigation
                    }, (function (event) {
                        $scope.saveItem(event.data.navigation, event.data.item.parentItem, event.data.item);
                    }));
                };
                $scope.deleteItem = function (navigation, item, parentItem) {
                    var route = angularConfig.getSymfonyRoute('symbb_backend_api_site_navigation_item_delete', $routeParams);
                    $http.delete(route+'?data='+item.id).success(function (response) {
                        if (response.success) {
                            if(!parentItem || item.id == parentItem.id){
                                newItems = [];
                                $.each(navigation.items, function(key, elem){
                                    if(elem.id != item.id){
                                        newItems[newItems.length] = elem;
                                    }
                                });
                                navigation.items = newItems;
                            } else {
                                newItems = [];
                                if(parentItem.children){
                                    $.each(parentItem.children, function(key, elem){
                                        if(elem.id != item.id){
                                            newItems[newItems.length] = elem;
                                        }
                                    });
                                }
                                parentItem.children = newItems;
                            }
                            item = null;
                        }
                        $scope.saveItemInProgress = false;
                    });
                };
                $scope.saveNavigationInProgress = false;
                $scope.saveNavigation = function (site) {
                    if (parseInt($scope.$parent.navigationForm.site) > 0 && !$scope.saveNavigationInProgress) {
                        $scope.saveNavigationInProgress = true;
                        var route = angularConfig.getSymfonyRoute('symbb_backend_api_site_navigation_save', $routeParams);
                        $http.post(route, {data: $scope.$parent.navigationForm}).success(function (response) {
                            if (response.success) {
                                $.each($scope.data, function(key, site){
                                    if(site.id == $scope.currSite){
                                        $scope.$parent.navigationForm.id = response.data.id;
                                        length = $scope.data[key]['navigations'].length;
                                        $scope.data[key]['navigations'][length] = $scope.$parent.navigationForm;
                                    }
                                });
                            }
                        });
                        $scope.saveNavigationInProgress = false;
                        $('#naviForm').find('.modal').modal('hide');
                    }
                };

                $scope.saveItemInProgress = false;
                $scope.saveItem = function (navigation, parentItem, item) {

                    if (
                            (
                                parseInt($scope.$parent.navigationItemForm.navigationId) > 0 ||
                                parseInt($scope.$parent.navigationItemForm.id) > 0
                            ) &&
                            !$scope.saveItemInProgress
                    ) {
                        $scope.saveItemInProgress = true;
                        var route = angularConfig.getSymfonyRoute('symbb_backend_api_site_navigation_item_save', $routeParams);
                        $http.post(route, {data: $scope.$parent.navigationItemForm}).success(function (response) {
                            if (response.success) {
                                if($scope.$parent.navigationItemForm.id > 0){
                                    item = response.data;
                                } else if(parentItem && parentItem.id > 0) {
                                    if(!parentItem.children){
                                        parentItem.children = [];
                                    }
                                    parentItem.children[parentItem.children.length] = response.data;
                                } else {
                                    if(!navigation.items){
                                        navigation.items = [];
                                    }
                                    navigation.items[navigation.items.length] = response.data;
                                }
                            }
                            $scope.saveItemInProgress = false;
                            $('#naviItemForm').find('.modal').modal('hide');
                        });
                    }
                };

                $anchorScroll();
            } else {
                console.debug('No Api Route found for: ' + routingKey)
            }
        } else {
            console.debug('No configured angular route found for: ' + pattern)
        }
    }
]);
