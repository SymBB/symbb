symbbControllers.controller('UserListCtrl', ["$scope", "$symbbRestCrud", "$http",
    function($scope, $symbbRestCrud, $http) {

        var service = new $symbbRestCrud();

        var groupsRoute = angularConfig.getSymfonyRoute('symbb_backend_api_user_group_list');
        $scope.groupSelectItems = [];
        $http.get(groupsRoute).success(function(response){
            if(response.success){
                $.each(response.data, function(key, value){
                    value.selected = false;
                    $scope.groupSelectItems[$scope.groupSelectItems.length] = value;
                });
            }
        });

        service.routingIdField = 'user';
        service.beforeSave = function(entry){
            entry.groups = [];
            $.each($scope.groupSelectItems, function(key, value){
                if(value.selected){
                    entry.groups[entry.groups.length] = value.id;
                    entry.groups[entry.groups.length] = value.id;
                }
            });
            return entry;
        };
        service.beforeEdit = function(entry){
            $.each($scope.groupSelectItems, function(key, value){
                $scope.groupSelectItems[key].selected = false;
                if(entry.groups && entry.groups.length > 0){
                    $.each(entry.groups, function(key2, value2){
                        if(value.id === value2){
                            $scope.groupSelectItems[key].selected = true;
                        }
                    });
                }

            });
            return entry;
        };
        service.init($scope);
    }
]);
