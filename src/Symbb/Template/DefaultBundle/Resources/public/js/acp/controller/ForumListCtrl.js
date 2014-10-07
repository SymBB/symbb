symbbControllers.controller('ForumListCtrl', ["$scope", "$symbbRestCrud",
    function($scope, $symbbRestCrud) {
        $symbbRestCrud.parentIdField = 'parent';
        $symbbRestCrud.routingIdField = 'forum';
        $symbbRestCrud.init($scope);
    }
]);
