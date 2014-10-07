symbbControllers.controller('ForumListCtrl', ["$scope", "$symbbRestCrud",
    function($scope, $symbbRestCrud) {
        $symbbRestCrud.routingIdField = 'forum';
        $symbbRestCrud.init($scope);
    }
]);
