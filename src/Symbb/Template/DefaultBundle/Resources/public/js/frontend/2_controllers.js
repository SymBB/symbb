symbbControllers.controller('ForumCtrl', ['$scope', '$http', '$routeParams', '$timeout', '$anchorScroll', '$cookieStore', 'ScrollPagination',
    function ($scope, $http, $routeParams, $timeout, $anchorScroll, $cookieStore, ScrollPagination) {

        var forumId = 0
        if ($routeParams && $routeParams.id) {
            forumId = $routeParams.id;
        }
        var pagenumber = 1;
        if ($routeParams && $routeParams.page) {
            pagenumber = $routeParams.page;
        }
        var route = angularConfig.getSymfonyRoute('symbb_api_forum_data', {id: forumId, page: pagenumber});
        $http.get(route).success(function (data) {
            $timeout(textMatchOneLine, 0);
            $.each(data, function (key, value) {
                $scope[key] = value;
            });
            if (data.forum.isForum) {
                $scope.searchPagination = new ScrollPagination('symbb_api_forum_topic_list', {forum: forumId}, 'topics');
            }
        });
        defaultForumListStuff($scope, $cookieStore, $anchorScroll);
    }
]).controller('ForumSearchCtrl', ['$scope', '$http', '$timeout', '$anchorScroll', '$cookieStore', '$routeParams', 'ScrollPagination',
    function ($scope, $http, $timeout, $anchorScroll, $cookieStore, $routeParams, ScrollPagination) {
        $scope.searchPagination = new ScrollPagination('symbb_api_post_search', {}, 'entries');
        defaultForumListStuff($scope, $cookieStore, $anchorScroll);
    }
]).controller('ForumTopicShowCtrl', ['$scope', '$http', '$routeParams', '$timeout', '$anchorScroll',
    function ($scope, $http, $routeParams, $timeout, $anchorScroll) {
        var id = $routeParams.id

        var pagenumber = 1;
        if ($routeParams && $routeParams.page) {
            pagenumber = $routeParams.page;
        }
        var route = angularConfig.getSymfonyRoute('symbb_api_topic_data', {id: id, 'page': pagenumber});
        $http.get(route).success(function (data) {
            $.each(data, function (key, value) {
                $scope[key] = value;
            });
        });

        $anchorScroll();
    }
]).controller('ForumTopicEditCtrl', ['$scope', '$http', '$routeParams', '$fileUploader', '$injector', '$location', '$anchorScroll', '$timeout',
    function ($scope, $http, $routeParams, $fileUploader, $injector, $location, $anchorScroll, $timeout) {
        var forumId = $routeParams.forum
        var topicId = $routeParams.id
        var route = angularConfig.getSymfonyRoute('symbb_api_topic_data', {forum: forumId, id: topicId});
        $http.get(route).success(function (data) {

            $scope.topic = {};
            $scope.forum = {};

            $.each(data, function (key, value) {
                $scope[key] = value;
            });

            $scope.master = {};

            $scope.update = function (topic) {
                $scope.master = angular.copy(topic);
                $http.post(angularConfig.getSymfonyRoute('symbb_api_topic_save', {}), $scope.master).success(function (data) {
                    if (data.success) {
                        angularConfig.goTo($timeout, $location, 'symbb_forum_topic_show', {
                            id: data.id,
                            name: 'new',
                            page: 1
                        });
                    }
                });
            };

            $scope.reset = function () {
                $scope.topic = angular.copy($scope.master);
            };

            symbbAngularUtils.createPostUploader($scope, $fileUploader, $scope.topic.mainPost, $injector)

        });

        $anchorScroll();
    }
]).controller('ForumPostEditCtrl', ['$scope', '$http', '$routeParams', '$fileUploader', '$injector', '$location', '$anchorScroll', '$timeout',
    function ($scope, $http, $routeParams, $fileUploader, $injector, $location, $anchorScroll, $timeout) {

        var route = angularConfig.getSymfonyRoute('symbb_api_post_data', $routeParams);
        $http.get(route).success(function (data) {

            $scope.post = {};

            $.each(data, function (key, value) {
                $scope[key] = value;
            });

            $scope.master = {};

            $scope.update = function (post) {
                $scope.master = angular.copy(post);
                $http.post(angularConfig.getSymfonyRoute('symbb_api_post_save'), $scope.master).success(function (data) {
                    if (data.success) {
                        angularConfig.goTo($timeout, $location, 'symbb_forum_topic_show', {
                            id: $scope.master.topic.id,
                            name: $scope.master.topic.seo.name,
                            page: 'last'
                        });
                    }
                });
            };

            $scope.reset = function () {
                $scope.post = angular.copy($scope.master);
            };

            symbbAngularUtils.createPostUploader($scope, $fileUploader, $scope.post, $injector)
        });

        $anchorScroll();
    }
]);


function defaultForumListStuff($scope, $cookieStore, $anchorScroll) {
    $anchorScroll();
}


// USER Bundle

symbbControllers.controller('UcpCtrl', ['$scope', '$http', '$location',
    function ($scope, $http, $location) {
        var route = angularConfig.getSymfonyRoute('symbb_api_ucp_data', {});
        $http.get(route).success(function (data) {

            $.each(data, function (key, value) {
                $scope[key] = value;
            });

            $scope.master = {};

            $scope.update = function (user) {
                $scope.master = angular.copy(user);
                $http.post(angularConfig.getSymfonyRoute('symbb_api_user_ucp_save', {}), $scope.master).success(function (data) {
                    if (data.success) {
                    }
                });
            };

            $scope.reset = function () {
                $scope.user = angular.copy($scope.master);
            };
        });
    }
]).controller('UcpSecurityCtrl', ['$scope', '$http', '$location',
    function ($scope, $http, $location) {
        createUcpUserDataStuff($scope, $http, $location);
    }
]);


function createUcpUserDataStuff($scope, $http, $location) {
    var route = angularConfig.getSymfonyRoute('symbb_api_ucp_data', {});
    $http.get(route).success(function (data) {

        $.each(data, function (key, value) {
            $scope[key] = value;
        });

        $scope.master = {};

        $scope.update = function (user) {
            $scope.master = angular.copy(user);
            $http.post(angularConfig.getSymfonyRoute('symbb_api_user_ucp_save', {}), $scope.master).success(function (data) {
                if (data.success) {
                }
            });
        };

        $scope.reset = function () {
            $scope.user = angular.copy($scope.master);
        };
    });
}


// MESSAGE Bundle

symbbControllers.controller('MessageListCtrl', ['$scope', '$http', '$routeParams', '$anchorScroll',
    function ($scope, $http, $routeParams, $anchorScroll) {

        $scope.receivedPage = 1;
        $scope.sentPage = 1;

        $scope.getReceivedMessages = function (pagenumber) {
            $scope.loadingReceivedMessages = true;
            var route = angularConfig.getSymfonyRoute('symbb_api_message_list_received', {'page': pagenumber});
            $http.get(route).success(function (data) {
                $scope.entriesReceived = data.entries;
                $scope.paginationDataRecevied = data.paginationData;
                $scope.loadingReceivedMessages = false;
                if (data.user) {
                    $scope.user = data.user;
                }
            });
        };
        $scope.getSentMessages = function (pagenumber) {
            $scope.loadingSentMessages = true;
            var route = angularConfig.getSymfonyRoute('symbb_api_message_list_sent', {'page': pagenumber});
            $http.get(route).success(function (data) {
                $scope.entriesSent = data.entries;
                $scope.paginationDataSent = data.paginationData;
                $scope.loadingSentMessages = false;
                if (data.user) {
                    $scope.user = data.user;
                }
            });
        };

        $scope.paginateReceivedBack = function () {
            var current = $scope.paginateReceivedBack.current;
            var startPage = $scope.paginateReceivedBack.startPage;
            var page = parseInt(current) - 1;
            if (page < startPage) {
                page = startPage;
            }
            $scope.getReceivedMessages(page)
        };

        $scope.paginateSentNext = function () {
            var page = $scope.paginationDataSent.next
            $scope.getSentMessages(page)
        };

        $scope.paginateSentBack = function () {
            var current = $scope.paginateSentBack.current;
            var startPage = $scope.paginateSentBack.startPage;
            var page = parseInt(current) - 1;
            if (page < startPage) {
                page = startPage;
            }
            $scope.getSentMessages(page)
        };

        $scope.paginateReceivedNext = function () {
            var page = $scope.paginationDataRecevied.next
            $scope.getReceivedMessages(page)
        };

        $scope.getReceivedMessages($scope.receivedPage);
        $scope.getSentMessages($scope.sentPage);

        $anchorScroll();
    }
]).controller('MessageNewCtrl', ['$scope', '$http', '$routeParams', '$anchorScroll', '$location', '$timeout',
    function ($scope, $http, $routeParams, $anchorScroll, $location, $timeout) {
        $scope.message = {
            id: 0,
            subject: '',
            message: '',
            receivers: []
        }

        $scope.save = function () {
            symbbMessageSave($scope.message, $http, $timeout, $location);
        }

        $anchorScroll();
    }
]).controller('MessageShowCtrl', ['$scope', '$http', '$routeParams', '$anchorScroll', '$location', '$timeout',
    function ($scope, $http, $routeParams, $anchorScroll, $location, $timeout) {
        var messageId = 0;
        if ($routeParams && $routeParams.id) {
            messageId = $routeParams.id;
        }
        var route = angularConfig.getSymfonyRoute('symbb_api_message_data', {'id': messageId});
        $http.get(route).success(function (data) {
            $.each(data, function (key, value) {
                $scope[key] = value;
            });
            $scope.replyMessage = {
                id: 0,
                subject: '',
                message: '',
                receivers: [$scope.message.sender.id]
            }
        });
        $scope.sendReply = function () {
            symbbMessageSave($scope.replyMessage, $http, $timeout, $location);
        }
        $anchorScroll();
    }
]);


function symbbMessageSave(message, $http, $timeout, $location) {
    $http.post(angularConfig.getSymfonyRoute('symbb_api_message_save', {}), message).success(function (data) {
        if (data.success) {
            angularConfig.goTo($timeout, $location, 'symbb_message_list');
        }
    });
}