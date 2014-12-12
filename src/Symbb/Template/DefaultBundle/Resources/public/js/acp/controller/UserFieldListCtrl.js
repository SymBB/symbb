symbbControllers.controller('UserFieldListCtrl', ["$scope", "$symbbRestCrud",
    function($scope, $symbbRestCrud) {
        var service = new $symbbRestCrud();
        service.routingIdField = 'field';
        service.init($scope);
    }
]);
