symbbControllers.controller('ForumCtrl', ['$scope', '$http', '$routeParams', '$timeout', 'ScrollPagination', '$anchorScroll', '$cookieStore',
    function($scope, $http, $routeParams, $timeout, ScrollPagination, $anchorScroll, $cookieStore) {
        var forumId = 0
        if ($routeParams && $routeParams.id) {
            forumId = $routeParams.id;
        }
        var route = angularConfig.getSymfonyApiRoute('forum_show', {id: forumId});
        $http.get(route).success(function(data) {
            $timeout(textMatchOneLine, 0);
            $.each(data, function(key, value) {
                $scope[key] = value;
            });
            $scope.topicPagination = new ScrollPagination('forum_topic_list', {id: $scope.forum.id}, $scope.topicList, 1, $scope.topicTotalCount);
        });
        defaultForumListStuff($scope, $cookieStore, $anchorScroll);
    }
]).controller('ForumNewestShowCtrl', ['$scope', '$http', '$routeParams', 'ScrollPagination', '$timeout', '$anchorScroll', '$cookieStore',
    function($scope, $http, $routeParams, ScrollPagination, $timeout, $anchorScroll, $cookieStore) {
        var route = angularConfig.getSymfonyApiRoute('forum_newest');
        $http.get(route).success(function(data) {
            $.each(data, function(key, value) {
                $scope[key] = value;
            });
            $scope.newestPagination = new ScrollPagination('forum_newest', {}, $scope.posts, 1, $scope.count.post, 'posts');
            $timeout(function(){
                $('.symbb_topic .row.body .media .userblock').each(function(key, div){
                    var height = $( div ).parent().height();
                    $( div ).css("height", height);
                });
            }, 0);
            
        });
        defaultForumListStuff($scope, $cookieStore, $anchorScroll);
    }
]).controller('ForumTopicShowCtrl', ['$scope', '$http', '$routeParams', 'ScrollPagination', '$timeout', '$anchorScroll',
    function($scope, $http, $routeParams, ScrollPagination, $timeout, $anchorScroll) {
        var id = $routeParams.id
        var route = angularConfig.getSymfonyApiRoute('forum_topic_show', {id: id});
        $http.get(route).success(function(data) {
            $.each(data, function(key, value) {
                $scope[key] = value;
            });
            $scope.postPagination = new ScrollPagination('forum_post_list', {id: $scope.topic.id}, $scope.topic.posts, 1, $scope.topic.count.post);
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
                $http.post(angularConfig.getSymfonyApiRoute('forum_topic_save', {forumId: $scope.topic.forum.id}), $scope.master).success(function(data) {
                    if (data.success) {
                        angularConfig.goTo($location, 'forum_topic_show', {id: data.id, name: $scope.master.name});
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
                        angularConfig.goTo($location, 'forum_topic_show', {id: $scope.master.topic.id, name: $scope.master.topic.name});
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
    
    var view = $cookieStore.get('symbb_forum_view');
  
    if(view !== 'card' && view !== 'table'){
        view = 'card';
        $cookieStore.put('symbb_forum_view', view);
    }

    $scope.currListView = view;
    $scope.changeView = function(){
        if($scope.currListView === 'table'){
            $scope.currListView = 'card';
        } else {
            $scope.currListView = 'table';
        }
        $cookieStore.put('symbb_forum_view', $scope.currListView);
    };
    
    $anchorScroll();
}