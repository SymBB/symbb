symbbControllers.directive('symbbBreadcrumb', function () {
    return {
        restrict: 'E',
        replace: true,
        transclude: true,
        template: '<div class="" ><ol class="breadcrumb" ng-transclude></ol></div>',
        link: function (scope, elm, attrs) {
            symbbAngularUtils.breadcrumbElement = $(elm[0]).find('ol');
        }
    };
}).directive('symbbSfLink', function () {
    return {
        restrict: 'A',
        transclude: false,
        replace: false,
        link: function (scope, element, attrs) {

            var aTag = element[0];
            if (element[0].tagName !== "A") {
                var aTag = $('<a></a>');
                $(element[0]).children().each(function (key, element) {
                    aTag.append(element);
                });
                var html = $(element[0]).html();
                $(element[0]).html('');
                aTag.prepend(html);
                $(element[0]).append(aTag);
            }

            var params = prepareParams(attrs);
            var path = angularConfig.getSymfonyRoute(attrs.symbbSfLink, params);
            $(aTag).attr('href', path);
            if (attrs.target) {
                $(aTag).attr('target', attrs.target);
            }
        }
    };
}).directive('symbbLink', function () {
    return {
        restrict: 'A',
        transclude: false,
        replace: false,
        link: function (scope, element, attrs) {

            var aTag = element[0];
            if (element[0].tagName !== "A") {
                var aTag = $('<a></a>');
                $(element[0]).children().each(function (key, element) {
                    aTag.append(element);
                });
                var html = $(element[0]).html();
                $(element[0]).html('');
                aTag.prepend(html);
                $(element[0]).append(aTag);
            }

            var params = prepareParams(attrs);
            var path = angularConfig.getAngularRoute(attrs.symbbLink, params);
            $(aTag).attr('href', path);
            if (attrs.target) {
                $(aTag).attr('target', attrs.target);
            }
        }
    };
}).directive('symbbJsLink', ['$location', '$timeout', function ($location, $timeout) {
    return {
        restrict: 'A',
        replace: false,
        link: function (scope, element, attrs) {
            $(element[0]).addClass('pointer');
            $(element[0]).click(function () {
                var params = prepareParams(attrs);
                angularConfig.goTo($timeout, $location, attrs.symbbJsLink, params);
                return false;
            });
        }
    };
}]).directive('symbbDeleteRequest', ['$http', function ($http) {
    return {
        restrict: 'A',
        transclude: false,
        replace: false,
        link: function (scope, element, attrs) {
            $(element[0]).click(function () {
                if (confirm(attrs.message)) {
                    var params = prepareParams(attrs);
                    $http({
                        method: 'delete',
                        url: angularConfig.getSymfonyRoute(attrs.symbbDeleteRequest, params)
                    }).success(function (data) {

                    });
                }
                return false;
            });
        }
    };
}]).directive('symbbTooltip', ['$timeout', function (timer) {
    return {
        restrict: 'A',
        transclude: false,
        replace: false,
        link: function (scope, element, attrs) {
            var tooltip = function () {
                $(element).tooltip();
            }
            timer(tooltip, 0)
        }
    };
}]).directive('symbbFormTabs', ['$timeout', function (timer) {
    return {
        restrict: 'A',
        transclude: false,
        replace: false,
        link: function (scope, element, attrs) {
            timer(symbbTabs, 0);
        }
    };
}]).directive('symbbRequest', ['$http', '$route', function ($http, $route) {
    return {
        restrict: 'A',
        transclude: false,
        replace: false,
        link: function (scope, element, attrs) {
            $(element[0]).click(function () {
                var params = prepareParams(attrs);
                $http.post(angularConfig.getSymfonyRoute(attrs.symbbRequest, params)).success(function (data) {

                });
            });
        }
    };
}]).directive('pagination', ['$http', '$route', '$location', '$timeout', function ($http, $route, $location, $timeout) {
    return {
        restrict: 'E',
        replace: true,
        template: '<ul class="pagination"><li><a href="#" ng-click="paginateBack()">«</a></li><li ng-repeat="page in paginationData.pagesInRange" ng-class=\'(page==paginationData.current) ? "active" : "inactive"\'><a href="#" ng-click="paginate(page)">[[page]]</a></li><li><a href="#" ng-click="paginateNext()">»</a></li></ul>',
        link: function (scope, element, attrs) {


            scope.paginate = function (pagenumber) {
                if (!pagenumber) {
                    pagenumber = 1;
                } else {
                    pagenumber = parseInt(pagenumber);
                }
                var startPage = scope.paginationData.startPage;
                var endPage = scope.paginationData.endPage;
                if (pagenumber < startPage) {
                    pagenumber = startPage;
                }
                if (pagenumber > endPage) {
                    pagenumber = endPage;
                }
                $timeout(function () {
                    var params = prepareParams(attrs);
                    params.page = pagenumber;
                    angularConfig.goTo($timeout, $location, attrs.route, params);
                }, 0);

            };
            scope.paginateNext = function () {
                $timeout(function () {
                    var params = prepareParams(attrs);
                    params.page = scope.paginationData.next;
                    angularConfig.goTo($timeout, $location, attrs.route, params);
                }, 0);

            };
            scope.paginateBack = function () {
                var current = scope.paginationData.current;
                var startPage = scope.paginationData.startPage;
                var back = parseInt(current) - 1;
                if (back < startPage) {
                    back = startPage;
                }
                $timeout(function () {
                    var params = prepareParams(attrs);
                    params.page = back;
                    angularConfig.goTo($timeout, $location, attrs.route, params);
                }, 0);
            };
        }
    };
}]).directive('symbbRestPagination', ['$http', '$route', '$location', '$timeout', function ($http, $route, $location, $timeout) {
    return {
        restrict: 'E',
        replace: true,
        template: '<ul class="pagination"><li><a href="#" ng-click="paginateBack()">«</a></li><li ng-repeat="page in paginationData.pagesInRange" ng-class=\'(page==paginationData.current) ? "active" : "inactive"\'><a href="#" ng-click="paginate(page)">[[page]]</a></li><li><a href="#" ng-click="paginateNext()">»</a></li></ul>',
        link: function (scope, element, attrs) {

            scope.paginate = function (pagenumber) {
                if (!pagenumber) {
                    pagenumber = 1;
                } else {
                    pagenumber = parseInt(pagenumber);
                }
                var startPage = scope.paginationData.startPage;
                var endPage = scope.paginationData.endPage;
                if (pagenumber < startPage) {
                    pagenumber = startPage;
                }
                if (pagenumber > endPage) {
                    pagenumber = endPage;
                }
                $timeout(function () {
                    var params = prepareParams(attrs);
                    var route = angularConfig.getSymfonyRoute(attrs.route, params);
                    $http.get(route + '?page=' + pagenumber).success(function (response) {
                        if (response.success) {
                            $.each(response, function (key, value) {
                                scope[key] = value;
                            });
                        }
                    });
                }, 0);
            };
            scope.paginateNext = function () {
                $timeout(function () {
                    scope.paginate(scope.paginationData.next);
                }, 0);
            };
            scope.paginateBack = function () {
                var current = scope.paginationData.current;
                var startPage = scope.paginationData.startPage;
                var back = parseInt(current) - 1;
                if (back < startPage) {
                    back = startPage;
                }
                $timeout(function () {
                    scope.paginate(back);
                }, 0);
            };
        }
    };
}]).directive('ngBindHtmlUnsafe', [function () {
    return function (scope, element, attr) {
        element.addClass('ng-binding').data('$binding', attr.ngBindHtmlUnsafe);
        scope.$watch(attr.ngBindHtmlUnsafe, function ngBindHtmlUnsafeWatchAction(value) {
            element.html(value || '');
        });
    }
}]).directive('symbbTopicList', ['$http', '$timeout', function ($http, $timeout) {
    return {
        restrict: 'E',
        replace: false,
        transclude: true,
        templateUrl: angularConfig.getSymfonyTemplateRoute('forum_topic_list'),
        link: function (scope, element, attrs) {
            if (!scope.topicListStatus) {
                scope.topicListStatus = [];
            }
            if (!scope.emptyTopicList) {
                scope.emptyTopicList = [];
            }
            if (!scope.topicList) {
                scope.topicList = [];
            }

            scope.topicListLoading = false;

            $timeout(function () {
                $('#symbbShowTopicList_' + attrs.paramForum).click(function () {

                    if (!scope.topicListStatus[attrs.paramForum]) {
                        scope.topicListStatus[attrs.paramForum] = true;
                        scope.topicListLoading = true;
                        scope.emptyTopicList[attrs.paramForum] = 0;
                        $http.get(angularConfig.getSymfonyRoute('symbb_api_forum_topic_list', {
                            forum: attrs.paramForum,
                            page: attrs.paramPage
                        })).success(function (data) {

                            scope.topicList[attrs.paramForum] = data.topics;

                            if (data.topics.length <= 0) {
                                scope.emptyTopicList[attrs.paramForum] = 1;
                            } else {
                                scope.emptyTopicList[attrs.paramForum] = 0;
                            }
                            scope.topicListLoading = false;
                        });
                    } else {
                        $timeout(function () {
                            scope.topicList[attrs.paramForum] = [];
                            scope.topicListStatus[attrs.paramForum] = false;
                        })
                    }
                });
            });
        }
    };
}]).directive('symbbBreadcrumbMini', ['$http', '$route', function ($http, $route) {
    return {
        restrict: 'E',
        replace: true,
        template: '<ol class="breadcrumb_mini"></ol>',
        link: function (scope, element, attrs) {
            var spacer = '<span class="glyphicon glyphicon-chevron-right"></span>';
            var elementWithBreadcrumbName = attrs.objectname;
            if (scope[elementWithBreadcrumbName] && scope[elementWithBreadcrumbName].breadcrumb) {
                var count = scope[elementWithBreadcrumbName].breadcrumb.length;
                var i = 1;
                $(scope[elementWithBreadcrumbName].breadcrumb).each(function (key, entry) {
                    if (i === count) {
                        spacer = "";
                    }
                    symbbAngularUtils.createBreadcrumbLi(entry, spacer).appendTo($(element));
                    i++;
                });
            }
        }
    };
}]);

symbbControllers.directive('symbbModalForm', ['$http', '$timeout', function ($http, $timeout) {
    return {
        restrict: 'E',
        transclude: true,
        scope: {},
        template: '<div class="modal fade bs-example-modal-sm" data-backdrop="false" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="false"> <div class="modal-dialog"><div class="modal-content"><div class="modal-header"><button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button><h4 class="modal-title" id="myModalLabel"></h4></div><div class="modal-body" ng-transclude ></div><div class="modal-footer"><button type="button" class="btn btn-primary modal-button"></button></div></div></div></div>',
        link: function (scope, elm, attrs) {
            $timeout(function () {
                var headerText = attrs.headerText;
                var buttonText = attrs.buttonText;
                $(elm).find('.modal-button').html(buttonText);
                $(elm).find('.modal-title').html(headerText);
            }, 0);
        }
    };
}]);

function prepareParams(attrs) {
    var params = {};
    $.each(attrs, function (key, value) {
        if (key.match(/^param/)) {
            var newKey = key.replace('param', '');
            var newKey = newKey.substr(0, 1).toLowerCase() + newKey.substr(1);
            params[newKey] = value;
        }
    });
    return params;
}