symbbControllers.controller('ApiCrudListCtrl', ["$scope", "$symbbRestCrud",
    function($scope, $symbbRestCrud) {
        $symbbRestCrud.init($scope);
    }
]);
