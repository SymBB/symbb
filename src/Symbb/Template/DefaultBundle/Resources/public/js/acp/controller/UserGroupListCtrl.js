symbbControllers.controller('UserGroupListCtrl', ["$scope", "$symbbRestCrud",
    function($scope, $symbbRestCrud) {
        $symbbRestCrud.routingIdField = 'group';
        $symbbRestCrud.init($scope);
    }
]);
