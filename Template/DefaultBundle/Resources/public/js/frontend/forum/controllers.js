symbbControllers.controller('ForumCtrl', ['$scope', '$http', '$routeParams', '$timeout', 'ScrollPagination',
    function($scope, $http, $routeParams, $timeout, ScrollPagination) {
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

    }
]).controller('ForumTopicShowCtrl', ['$scope', '$http', '$routeParams', 'ScrollPagination',
    function($scope, $http, $routeParams, ScrollPagination) {
        var id = $routeParams.id
        var route = angularConfig.getSymfonyApiRoute('forum_topic_show', {id: id});
        $http.get(route).success(function(data) {
            $.each(data, function(key, value) {
                $scope[key] = value;
            });
            $scope.postPagination = new ScrollPagination('forum_post_list', {id: $scope.topic.id}, $scope.topic.posts, 1, $scope.topic.count.post);
        });
    }
]).controller('ForumTopicCreateCtrl', ['$scope', '$http', '$routeParams', '$fileUploader', '$injector',
    function($scope, $http, $routeParams, $fileUploader, $injector) {
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
                $http.post(angularConfig.getSymfonyApiRoute('forum_topic_save', {forumId: $scope.forum.id}), $scope.master).success(function(data) {
                    if (data.success) {
                        angularConfig.goTo('forum_topic_show', {id: data.id, name: $scope.master.name});
                    }
                });
            };

            $scope.reset = function() {
                $scope.topic = angular.copy($scope.master);
            };
            
            // Creates a uploader
            var uploader = $scope.uploader = $fileUploader.create({
                scope: $scope,
                url: angularConfig.getSymfonyApiRoute('forum_post_upload_image'),
                method: 'POST',
                formData: {id: 0}
            });
            
            $.each($scope.topic.mainPost.files, function(key, value) {
                var item = {
                    file: {
                        name: value
                    },
                    progress: 100,
                    isUploaded: true,
                    isSuccess: true
                };
                uploader.queue.push(item);
                item.remove = function() {
                    uploader.removeFromQueue(this);
                };
                uploader.progress = 100;
            });
            
            // ADDING FILTERS
            // Images only
            uploader.filters.push(function(item /*{File|HTMLInputElement}*/) {
                var type = uploader.isHTML5 ? item.type : '/' + item.value.slice(item.value.lastIndexOf('.') + 1);
                type = '|' + type.toLowerCase().slice(type.lastIndexOf('/') + 1) + '|';
                return '|jpg|png|jpeg|bmp|gif|'.indexOf(type) !== -1;
            });
            
            uploader.bind('complete', function (event, xhr, item, response) {
                response = symbbAngularUtils.checkResponse(response, $injector);
                if(response.files){
                    $.each(response.files, function(key, value) {
                        $scope.topic.mainPost.files[$scope.topic.mainPost.files.length] = value.url;
                    });
                }
            }); 
            
        });

    }
]).controller('ForumPostEditCtrl', ['$scope', '$http', '$routeParams', '$fileUploader', '$injector',
    function($scope, $http, $routeParams, $fileUploader, $injector) {

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
                        angularConfig.goTo('forum_topic_show', {id: $scope.master.topic.id, name: $scope.master.topic.name});
                    }
                });
            };

            $scope.reset = function() {
                $scope.post = angular.copy($scope.master);
            };
            
            // Creates a uploader
            var uploader = $scope.uploader = $fileUploader.create({
                scope: $scope,
                url: angularConfig.getSymfonyApiRoute('forum_post_upload_image'),
                method: 'POST',
                formData: {id: $scope.post.id}
            });
            
            $.each($scope.post.files, function(key, value) {
                var item = {
                    file: {
                        name: value
                    },
                    progress: 100,
                    isUploaded: true,
                    isSuccess: true
                };
                uploader.queue.push(item);
                item.remove = function() {
                    uploader.removeFromQueue(this);
                };
                uploader.progress = 100;
            });
            
            // ADDING FILTERS
            // Images only
            uploader.filters.push(function(item /*{File|HTMLInputElement}*/) {
                var type = uploader.isHTML5 ? item.type : '/' + item.value.slice(item.value.lastIndexOf('.') + 1);
                type = '|' + type.toLowerCase().slice(type.lastIndexOf('/') + 1) + '|';
                return '|jpg|png|jpeg|bmp|gif|'.indexOf(type) !== -1;
            });
            
            uploader.bind('complete', function (event, xhr, item, response) {
                response = symbbAngularUtils.checkResponse(response, $injector);
                if(response.files){
                    $.each(response.files, function(key, value) {
                        $scope.post.files[$scope.post.files.length] = value.url;
                    });
                }
            }); 
            
        });

    }
]);

