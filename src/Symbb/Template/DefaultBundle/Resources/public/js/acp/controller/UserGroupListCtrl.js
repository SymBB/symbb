symbbControllers.controller('UserGroupListCtrl', ["$scope", "$symbbRestCrud",
    function($scope, $symbbRestCrud) {
        var service = new $symbbRestCrud();
        service.routingIdField = 'group';
        service.init($scope);
    }
]);
