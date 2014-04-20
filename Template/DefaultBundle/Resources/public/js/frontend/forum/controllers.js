symbbControllers.controller('ForumCtrl', ['$scope', '$http', '$routeParams', '$timeout',
    function($scope, $http, $routeParams, $timeout) {
        var forumId = 0
        if($routeParams && $routeParams.id){
            forumId = $routeParams.id;
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
            $timeout(textMatchOneLine, 0);
        });
        
    }
]).controller('ForumTopicShowCtrl', ['$scope', '$http', '$routeParams', 'Topics',
    function($scope, $http, $routeParams, Topics) {
        var id = $routeParams.id
        var route = angularConfig.getSymfonyApiRoute('forum_topic_show', { id: id });
        $http.get(route).success(function(data) {
            $.each(data, function(key, value){
                $scope[key] = value;
            });
            $scope.topics = new Topics();
            $scope.topics.id = $scope.topic.id;
            $scope.topics.posts = $scope.topic.posts;
            $scope.topics.page = $scope.page;
        });
    }
]).controller('ForumTopicCreateCtrl', ['$scope', '$http', '$routeParams',
    function($scope, $http, $routeParams) {
        var forumId = $routeParams.id
        var route = angularConfig.getSymfonyApiRoute('forum_topic_create', { id: forumId });
        $http.get(route).success(function(data) {
            
            $scope.topic = {};
            $scope.forum = {};
            
            $.each(data, function(key, value){
                $scope[key] = value;
            });
            
            $scope.master = {};

            $scope.update = function(topic) {
                $scope.master = angular.copy(topic);
                $http.post(angularConfig.getSymfonyApiRoute('forum_topic_save', {forumId: $scope.forum.id}), $scope.master).success(function(data) {
                    if(data.success){
                        angularConfig.goTo('forum_topic_show', {id: data.id, name: $scope.master.name});
                    }
                });
            };

            $scope.reset = function() {
                $scope.topic = angular.copy($scope.master);
            };
        });
    }
]);

