symbbControllers.controller('UcpCtrl', ['$scope', '$http', '$location',
    function($scope, $http, $location) {
        var route = angularConfig.getSymfonyRoute('symbb_api_ucp_data', {});
        $http.get(route).success(function(data) {

            $.each(data, function(key, value) {
                $scope[key] = value;
            });

            $scope.master = {};

            $scope.update = function(user) {
                $scope.master = angular.copy(user);
                $http.post(angularConfig.getSymfonyRoute('symbb_api_user_ucp_save', {}), $scope.master).success(function(data) {
                    if (data.success) {
                    }
                });
            };

            $scope.reset = function() {
                $scope.user = angular.copy($scope.master);
            };
        });
    }
]).controller('UcpSecurityCtrl', ['$scope', '$http', '$location',
    function($scope, $http, $location) {
        createUcpUserDataStuff($scope, $http, $location);
    }
]);


function createUcpUserDataStuff($scope, $http, $location){
    var route = angularConfig.getSymfonyRoute('symbb_api_ucp_data', {});
    $http.get(route).success(function(data) {

        $.each(data, function(key, value) {
            $scope[key] = value;
        });

        $scope.master = {};

        $scope.update = function(user) {
            $scope.master = angular.copy(user);
            $http.post(angularConfig.getSymfonyRoute('symbb_api_user_ucp_save', {}), $scope.master).success(function(data) {
                if (data.success) {
                }
            });
        };

        $scope.reset = function() {
            $scope.user = angular.copy($scope.master);
        };
    });
}