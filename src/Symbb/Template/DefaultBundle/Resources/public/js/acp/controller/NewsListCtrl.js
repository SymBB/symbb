symbbControllers.controller('NewsListCtrl', ["$scope", "$symbbRestCrud", "$routeParams", "$http",
    function ($scope, $symbbRestCrud, $routeParams, $http) {
        var service = new $symbbRestCrud();
        service.init($scope);
    }
]);
