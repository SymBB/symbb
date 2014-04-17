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
            $scope.topicList = data.topicList;
            $scope.hasForumList = data.hasForumList;
            $scope.hasCategoryList = data.hasCategoryList;
            $scope.hasTopicList = data.hasTopicList;
            $scope.access = data.access;
            $scope.createBreadcrumb(data.breadcrumbItems);
        });
        
    }
]);

symbbControllers.directive('symbbBreadcrumb', function() {
    return {
        restrict: 'E',
        replace: true,
        template: '<ol class="breadcrumb"></ol>',
        link: function(scope, elm, attrs) {
            scope.createBreadcrumb = function(items) {
                $(elm[0]).html('<li><div class="avatar avatar_mini"><img src="'+symbbUser.avatar+'" /></div></li>');
                var spacer = '<span class="spacer">/</span>';
                var count = 0;
                $.each(items, function(key, value){
                    count++;
                });
                var i = 0;
                $.each(items, function(key, value){
                    if(i === count - 1){
                        spacer = '';
                    }
                    var route = 'index';
                    var params = {};
                    if(value.type === 'forum'){
                        route = 'forum_list';
                        params = {id: value.id, name: value.name};
                    }
                    var path = angularConfig.getAngularRoute(route, params);
                    $('<li><a href="#'+path+'">'+value.name+'</a>'+spacer+'</li>').appendTo($(elm[0]));
                    i++;
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
            if(attrs.paramName){
                params.name = attrs.paramName;
            }
            var path = angularConfig.getAngularRoute(attrs.symbbLink, params);
            $(element[0]).children('a').attr('href', '#'+path);
            element.href = path;
        }
    };
}).directive('symbbJsLink', function() {
    return {
        restrict: 'A',
        transclude: false,
        replace: false,
        link: function(scope, element, attrs) {
            $(element[0]).click(function() {
                var params = {};
                if(attrs.paramId){
                    params.id = attrs.paramId;
                }
                if(attrs.paramName){
                    params.name = attrs.paramName;
                }
                console.debug(params);
                angularConfig.goTo(attrs.symbbJsLink, params);
            });
        }
    };
});



