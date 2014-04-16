var symbbControllers = angular.module('symbbControllers', []);

symbbControllers.controller('ForumCtrl', ['$scope', '$http', '$routeParams',
    function($scope, $http, $routeParams) {
        
        var forumId = 0
        if($routeParams && $routeParams.id){
            forumId = $routeParams.id
        }
        var route = Routing.generate('symbb_api_forum_list', { parent: forumId, '_locale': symbbUserLang });
        
        $http.get(route).success(function(data) {
            $scope.forum = data.forum;
            $scope.forumList = data.forumList;
            $scope.categoryList = data.categoryList;
            $scope.forumListCheck = data.forumListCheck;
            $scope.createBreadcrumb(data.breadcrumbItems);
        });
        
    }
]).directive('symbbBreadcrumb', function() {
    return {
        restrict: 'E',
        replace: true,
        template: '<ol class="breadcrumb"></ol>',
        link: function(scope, elm, attrs) {
            scope.createBreadcrumb = function(items) {
                $(elm[0]).html('');
                $.each(items, function(key, value){
                    var route = 'index';
                    var params = {};
                    if(value.type == 'forum'){
                        route = 'forum_list';
                        params = {id: value.id};
                    }
                    var path = angularConfig.getAngularRoute(route, params);
                    $('<li><a href="#'+path+'">'+value.name+' › </a></li>').appendTo($(elm[0]));
                });
            };
        }
    };
}).directive('symbbLink', function() {
    return {
        restrict: 'A',
        transclude: true,
        template: '<a href="" ng-transclude></a>',
        link: function(scope, element, attrs) {
            var params = {};
            if(attrs.paramId){
                params.id = attrs.paramId;
            }
            var path = angularConfig.getAngularRoute(attrs.symbbLink, params);
            $(element[0]).children('a').attr('href', '#'+path);
            element.href = path;
        }
    };
});



