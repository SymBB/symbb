symbbControllers.controller('UcpCtrl', ['$scope', '$http',
    function($scope, $http) {
        var route = angularConfig.getSymfonyApiRoute('user_ucp', {});
        $http.get(route).success(function(data) {

            $.each(data, function(key, value) {
                $scope[key] = value;
            });

            $scope.master = {};

            $scope.update = function(user) {
                $scope.master = angular.copy(user);
                $http.post(angularConfig.getSymfonyApiRoute('user_ucp_save', {}), $scope.master).success(function(data) {
                    if (data.success) {
                        angularConfig.goTo($location, 'user_ucp', {id: data.id, name: $scope.master.name});
                    }
                });
            };

            $scope.reset = function() {
                $scope.user = angular.copy($scope.master);
            };
        });
    }
]);