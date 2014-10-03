symbbControllers.controller('UserFieldListCtrl', ["$scope", "$symbbRestCrud",
    function($scope, $symbbRestCrud) {
        $symbbRestCrud.routingIdField = 'field';
        $symbbRestCrud.init($scope);
    }
]);
