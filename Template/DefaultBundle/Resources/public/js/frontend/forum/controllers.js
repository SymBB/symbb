var symbbControllers = angular.module('symbbControllers', []);

symbbControllers.controller('ForumCtrl', ['$scope', '$http', '$routeParams',
    function($scope, $http, $routeParams) {
        
        var forumId = 0
        if($routeParams && $routeParams.id){
            forumId = $routeParams.id
        }
        var route = angularConfig.getSymfonyApiRoute('forum_list', { parent: forumId });
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
]).controller('ForumTopicShowCtrl', ['$scope', '$http', '$routeParams',
    function($scope, $http, $routeParams) {
        var forumId = $routeParams.id
        var route = angularConfig.getSymfonyApiRoute('forum_topic_list', { forum: forumId });
        $http.get(route).success(function(data) {

        });
    }
]).controller('ForumTopicCreateCtrl', ['$scope', '$http', '$routeParams',
    function($scope, $http, $routeParams) {
        var forumId = $routeParams.id
        var route = angularConfig.getSymfonyApiRoute('forum_topic_list', { forum: forumId });
        $http.get(route).success(function(data) {

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
                angularConfig.goTo(attrs.symbbJsLink, params);
            });
        }
    };
}).directive('symbbTooltip', ['$timeout', function(timer) {
    return {
        restrict: 'A',
        transclude: false,
        replace: false,
        link: function(scope, element, attrs) {
            var tooltip = function(){
                $(element).tooltip();
            }
            timer(tooltip, 0)
        }
    };
}]).directive('symbbRequest', ['$http', function($http) {
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
                console.debug(attrs.symbbRequest);
                $http.get(angularConfig.getSymfonyApiRoute(attrs.symbbRequest, params)).success(function(data) {
                    console.debug(data);
                });
            });
        }
    };
}]);   ;



