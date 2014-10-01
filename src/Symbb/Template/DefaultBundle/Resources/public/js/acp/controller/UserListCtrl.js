symbbControllers.controller('UserListCtrl', ["$scope", "$symbbRestCrud", "$http",
    function($scope, $symbbRestCrud, $http) {

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

        $symbbRestCrud.routingIdField = 'user';
        $symbbRestCrud.beforeSave = function(entry){
            entry.groups = [];
            $.each($scope.groupSelectItems, function(key, value){
                if(value.selected){
                    entry.groups[entry.groups.length] = value.id;
                    entry.groups[entry.groups.length] = value.id;
                }
            });
            return entry;
        };
        $symbbRestCrud.beforeEdit = function(entry){
            $.each($scope.groupSelectItems, function(key, value){
                $scope.groupSelectItems[key].selected = false;
                $.each(entry.groups, function(key2, value2){
                    if(value.id === value2){
                        $scope.groupSelectItems[key].selected = true;
                    }
                });

            });
            return entry;
        };
        $symbbRestCrud.init($scope);
    }
]);
