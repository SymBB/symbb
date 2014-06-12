symbbControllers.controller('ForumCtrl', ['$scope', '$http', '$routeParams', '$timeout', '$anchorScroll', '$cookieStore', 'ScrollPagination',
    function($scope, $http, $routeParams, $timeout, $anchorScroll, $cookieStore, ScrollPagination) {
        var forumId = 0
        if ($routeParams && $routeParams.id) {
            forumId = $routeParams.id;
        }
        var pagenumber = 1;
        if ($routeParams && $routeParams.page) {
            pagenumber = $routeParams.page;
        }
        var route = angularConfig.getSymfonyApiRoute('forum_show', {id: forumId, page: pagenumber});
        $http.get(route).success(function(data) {
            $timeout(textMatchOneLine, 0);
            $.each(data, function(key, value) {
                $scope[key] = value;
            });
            if(data.forum.isForum){
                $scope.searchPagination = new ScrollPagination('forum_topic_list', {forum: forumId}, 'topics');
            }
        });
        defaultForumListStuff($scope, $cookieStore, $anchorScroll);
    }
]).controller('ForumSearchCtrl', ['$scope', '$http', '$timeout', '$anchorScroll', '$cookieStore', '$routeParams', 'ScrollPagination',
    function($scope, $http, $timeout, $anchorScroll, $cookieStore, $routeParams, ScrollPagination) {
        $scope.searchPagination = new ScrollPagination('forum_search', {}, 'entries');
        defaultForumListStuff($scope, $cookieStore, $anchorScroll);
    }
]).controller('ForumTopicShowCtrl', ['$scope', '$http', '$routeParams', '$timeout', '$anchorScroll',
    function($scope, $http, $routeParams, $timeout, $anchorScroll) {
        var id = $routeParams.id
        
        var pagenumber = 1;
        if ($routeParams && $routeParams.page) {
            pagenumber = $routeParams.page;
        }
        var route = angularConfig.getSymfonyApiRoute('forum_topic_show', {id: id, 'page': pagenumber});
        $http.get(route).success(function(data) {
            $.each(data, function(key, value) {
                $scope[key] = value;
            });
            $timeout(function(){
                $('.symbb_topic .row.body .media .userblock').each(function(key, div){
                    var height = $( div ).parent().height();
                    $( div ).css("height", height);
                });
            }, 0);
            
        });
        
        $anchorScroll();
    }
]).controller('ForumTopicCreateCtrl', ['$scope', '$http', '$routeParams', '$fileUploader', '$injector', '$location', '$anchorScroll',
    function($scope, $http, $routeParams, $fileUploader, $injector, $location, $anchorScroll) {
        var forumId = $routeParams.id
        var route = angularConfig.getSymfonyApiRoute('forum_topic_create', {forum: forumId});
        $http.get(route).success(function(data) {

            $scope.topic = {};
            $scope.forum = {};

            $.each(data, function(key, value) {
                $scope[key] = value;
            });

            $scope.master = {};

            $scope.update = function(topic) {
                $scope.master = angular.copy(topic);
                $http.post(angularConfig.getSymfonyApiRoute('forum_topic_save', {}), $scope.master).success(function(data) {
                    if (data.success) {
                        angularConfig.goTo($location, 'forum_topic_show', {id: data.id, name: 'new', page:1});
                    }
                });
            };

            $scope.reset = function() {
                $scope.topic = angular.copy($scope.master);
            };
            
            symbbAngularUtils.createPostUploader($scope, $fileUploader, $scope.topic.mainPost, $injector)
            
        });

        $anchorScroll();
    }
]).controller('ForumPostEditCtrl', ['$scope', '$http', '$routeParams', '$fileUploader', '$injector', '$location', '$anchorScroll',
    function($scope, $http, $routeParams, $fileUploader, $injector, $location, $anchorScroll) {

        var route = angularConfig.getSymfonyApiRoute('forum_post_edit', $routeParams);
        $http.get(route).success(function(data) {

            $scope.post = {};

            $.each(data, function(key, value) {
                $scope[key] = value;
            });

            $scope.master = {};

            $scope.update = function(post) {
                $scope.master = angular.copy(post);
                $http.post(angularConfig.getSymfonyApiRoute('forum_post_save'), $scope.master).success(function(data) {
                    if (data.success) {
                        angularConfig.goTo($location, 'forum_topic_show', {id: $scope.master.topic.id, name: $scope.master.topic.seo.name, page:'last'});
                    }
                });
            };

            $scope.reset = function() {
                $scope.post = angular.copy($scope.master);
            };
            
            symbbAngularUtils.createPostUploader($scope, $fileUploader, $scope.post, $injector)
        });

        $anchorScroll();
    }
]);



function defaultForumListStuff($scope, $cookieStore, $anchorScroll){
    $anchorScroll();
}