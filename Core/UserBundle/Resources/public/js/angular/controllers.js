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
]).controller('UserlistCtrl', ['$scope', '$http', '$location', '$routeParams',
    function($scope, $http, $location, $routeParams) {
        var pagenumber = 1
        if ($routeParams && $routeParams.page) {
            pagenumber = $routeParams.page;
        }
        var route = angularConfig.getSymfonyRoute('symbb_api_userlist', {page: pagenumber});
        $http.get(route).success(function(data) {
            $.each(data, function(key, value) {
                $scope[key] = value;
            });
        });
    }
]).controller('UserProfileCtrl', ['$scope', '$http', '$location', '$routeParams',
    function($scope, $http, $location, $routeParams) {
        var route = angularConfig.getSymfonyRoute('symbb_api_user_data', {id: $routeParams.id});
        $http.get(route).success(function(data) {
            $.each(data, function(key, value) {
                $scope[key] = value;
            });
        });
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