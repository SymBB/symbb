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
]).controller('MessageNewCtrl', ['$scope', '$http', '$routeParams', '$anchorScroll', '$location',
    function($scope, $http, $routeParams, $anchorScroll, $location) {
        $scope.message = {
            id: 0,
            subject: '',
            message: '',
            receivers: []
        }
        $scope.save = function(){
            $http.post(angularConfig.getSymfonyApiRoute('message_save', {}), $scope.message).success(function(data) {
                if (data.success) {
                    angularConfig.goTo($location, 'message_list');
                }
            });
        }
        $anchorScroll();
    }
]).controller('MessageShowCtrl', ['$scope', '$http', '$routeParams', '$anchorScroll',
    function($scope, $http, $routeParams, $anchorScroll) {
        var messageId = 0;
        if ($routeParams && $routeParams.id) {
            messageId = $routeParams.id;
        }
        var route = angularConfig.getSymfonyApiRoute('message_show', {'id': messageId});
        $http.get(route).success(function(data) {
            $.each(data, function(key, value) {
                $scope[key] = value;
            });
        });
        $anchorScroll();
    }
]);