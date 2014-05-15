symbbControllers.controller('UcpCtrl', ['$scope', '$http', '$location',
    function($scope, $http, $location) {
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
                    }
                });
            };

            $scope.reset = function() {
                $scope.user = angular.copy($scope.master);
            };
        });
    }
]).controller('UserlistCtrl', ['$scope', '$http', '$location', '$routeParams',
    function($scope, $http, $location, $routeParams) {
        var pagenumber = 1
        if ($routeParams && $routeParams.page) {
            pagenumber = $routeParams.page;
        }
        var route = angularConfig.getSymfonyApiRoute('userlist', {page: pagenumber});
        $http.get(route).success(function(data) {
            $.each(data, function(key, value) {
                $scope[key] = value;
            });
        });
    }
]);