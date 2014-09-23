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
                $scope.deleteItem = function (item) {

                };

                $scope.saveNavigation = function () {
                    if (parseInt($scope.$parent.navigationForm.site) > 0) {
                        var route = angularConfig.getSymfonyRoute('symbb_backend_api_site_navigation_save', $routeParams);
                        $http.post(route, {data: $scope.$parent.navigationForm}).success(function (data) {
                            if (data.success) {
                                $scope.data[$scope.data.length] = $scope.$parent.navigationForm;
                            }
                        });
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
                        $http.post(route, {data: $scope.$parent.navigationItemForm}).success(function (data) {
                            if (data.success) {
                                if($scope.$parent.navigationItemForm.id > 0){
                                    item = $scope.$parent.navigationItemForm;
                                } else if($scope.$parent.navigationItemForm.parentItemId > 0) {
                                    parentItem.children[parentItem.children.length] = $scope.$parent.navigationItemForm;
                                } else {
                                    navigation.items[navigation.items.length] = $scope.$parent.navigationItemForm;
                                }
                            }
                            $scope.saveItemInProgress = false;
                        });
                    }
                };

                // add the new item to the current scope data
                $scope.addItemToParent = function(item, items){
                    console.debug(item);
                    console.debug(items);
                    if(items){
                        $.each(items, function(key, currItem){
                            if(!currItem.children){
                                currItem.children = [];
                            }
                            if(currItem.id == item.parentItemId){
                                items[key].children[currItem.children.length] = item;
                            } else if(currItem.children){
                                items[key].children = $scope.addItemToParent(item, currItem.children);
                            }
                        });
                    } else {
                        items = [];
                    }
                    return items;
                };

                // replace the scopedata with the new data
                $scope.searchAndReplaceItem = function(item, items){
                    $.each(items, function(key, currItem){
                        if(currItem.id == item.id){
                            items[key] = item;
                        } else if(currItem.children){
                            items[key].children = $scope.searchAndReplaceItem(item, currItem.children);
                        }
                    });
                    return items;
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
