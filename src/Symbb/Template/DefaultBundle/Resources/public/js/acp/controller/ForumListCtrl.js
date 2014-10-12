symbbControllers.controller('ForumListCtrl', ["$scope", "$symbbRestCrud",
    function($scope, $symbbRestCrud) {
        var service = new $symbbRestCrud();
        service.parentIdField = 'parent';
        service.routingIdField = 'parent';
        service.init($scope);
    }
]);
