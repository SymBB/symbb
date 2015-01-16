symbbControllers.controller('ApiCrudListCtrl', ["$scope", "$symbbRestCrud",
    function ($scope, $symbbRestCrud) {
        var service = new $symbbRestCrud();
        service.init($scope);
    }
]);
