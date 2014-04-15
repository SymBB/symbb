var symbbControllers = angular.module('symbbControllers', []);

symbbControllers.controller('ForumCtrl', ['$scope', '$http',
    function($scope, $http) {
        $http.get(Routing.generate('symbb_api_forum_list', { parent: 0, '_locale': symbbUserLang })).success(function(data) {
            $scope.forum = data.forum;
            $scope.forumList = data.forumList;
            $scope.categoryList = data.categoryList;
            $scope.forumListCheck = data.forumListCheck;
            
        });
    }
]).directive('ForumListCardDirective', function() {
    return {
        templateUrl: Routing.generate('symbb_template_default_angular', { file: 'forumListCard' , '_locale': symbbUserLang})
    };
});

