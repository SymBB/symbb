symbbControllers.controller('MessageListCtrl', ['$scope', '$http', '$routeParams', '$anchorScroll',
    function($scope, $http, $routeParams, $anchorScroll) {
        var pagenumber = 1;
        if ($routeParams && $routeParams.page) {
            pagenumber = $routeParams.page;
        }
        var route = angularConfig.getSymfonyApiRoute('message_list', {'page': pagenumber});
        $http.get(route).success(function(data) {
            $.each(data, function(key, value) {
                $scope[key] = value;
            });
        });
        $anchorScroll();
    }
]);