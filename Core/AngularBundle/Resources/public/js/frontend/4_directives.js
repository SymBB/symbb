symbbControllers.directive('symbbBreadcrumb', function() {
    return {
        restrict: 'E',
        replace: true,
        template: '<ol class="breadcrumb"></ol>',
        link: function(scope, elm, attrs) {
            symbbAngularUtils.breadcrumbElement = elm[0];
        }
    };
}).directive('symbbSfLink', function() {
    return {
        restrict: 'A',
        transclude: true,
        template: '<a href="" target="_self" ng-transclude></a>',
        link: function(scope, element, attrs) {
            var params = {};
            if(attrs.paramId){
                params.id = attrs.paramId;
            }
            if(attrs.paramName){
                params.name = attrs.paramName;
            }
            var path = angularConfig.getSymfonyRoute(attrs.symbbSfLink, params);
            $(element[0]).children('a').attr('href', path);
        }
    };
}).directive('symbbLink', function() {
    return {
        restrict: 'A',
        transclude: true,
        template: '<a href="" ng-transclude></a>',
        link: function(scope, element, attrs) {
            var params = {};
            $.each(attrs, function(key, value){
                if(key.match(/^param/)){
                        var newKey = key.replace('param', '');
                        var newKey = newKey.substr(0, 1).toLowerCase() + newKey.substr(1);
                        params[newKey] = value;
                    }
            });
            var path = angularConfig.getAngularRoute(attrs.symbbLink, params);
            $(element[0]).children('a').attr('href', path);
            if(attrs.target){
                $(element[0]).children('a').attr('target', attrs.target);
            }
        }
    };
}).directive('symbbJsLink', ['$location', function($location) {
    return {
        restrict: 'A',
        transclude: false,
        replace: false,
        link: function(scope, element, attrs) {
            $(element[0]).click(function() {
                var params = {};
                $.each(attrs, function(key, value){
                    if(key.match(/^param/)){
                        var newKey = key.replace('param', '');
                        var newKey = newKey.substr(0, 1).toLowerCase() + newKey.substr(1);
                        params[newKey] = value;
                    }
                });
                angularConfig.goTo($location, attrs.symbbJsLink, params);
                return false;
            });
        }
    };
}]).directive('symbbDeleteRequest', ['$http', function($http) {
    return {
        restrict: 'A',
        transclude: false,
        replace: false,
        link: function(scope, element, attrs) {
            $(element[0]).click(function() {
                if(confirm(attrs.message)){
                    var params = {};
                    $.each(attrs, function(key, value){
                        if(key.match(/^param/)){
                            var newKey = key.replace('param', '');
                            var newKey = newKey.substr(0, 1).toLowerCase() + newKey.substr(1);
                            params[newKey] = value;
                        }
                    });
                    $http({method: 'delete', url: angularConfig.getSymfonyApiRoute(attrs.symbbDeleteRequest, params)}).success(function(data) {
                        
                    });
                }
                return false;
            });
        }
    };
}]).directive('symbbTooltip', ['$timeout', function(timer) {
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
}]).directive('symbbFormTabs', ['$timeout', function(timer) {
    return {
        restrict: 'A',
        transclude: false,
        replace: false,
        link: function(scope, element, attrs) {
            timer(symbbTabs, 0);
        }
    };
}]).directive('symbbRequest', ['$http', '$route', function($http, $route) {
    return {
        restrict: 'A',
        transclude: false,
        replace: false,
        link: function(scope, element, attrs) {
            $(element[0]).click(function() {
                var params = {};
                $.each(attrs, function(key, value){
                    if(key.match(/^param/)){
                        var newKey = key.replace('param', '');
                        var newKey = newKey.substr(0, 1).toLowerCase() + newKey.substr(1);
                        params[newKey] = value;
                    }
                });
                $http.post(angularConfig.getSymfonyApiRoute(attrs.symbbRequest, params)).success(function(data) {
                    
                });
            });
        }
    };
}]).directive('pagination', ['$http', '$route', '$location', '$timeout', function($http, $route, $location, $timeout) {
    return {
        restrict: 'E',
        replace: true,
        template: '<ul class="pagination"><li><a href="#" ng-click="paginateBack()">«</a></li><li ng-repeat="page in paginationData.pagesInRange"><a href="#" ng-click="paginate(page)">[[page]]</a></li><li><a href="#" ng-click="paginateNext()">»</a></li></ul>',
        link: function(scope, element, attrs) {
    
            scope.paginate = function(pagenumber){
                if(!pagenumber){
                    pagenumber = 1;
                } else {
                    pagenumber = parseInt(pagenumber);
                }
                var startPage = scope.paginationData.startPage;
                var endPage = scope.paginationData.endPage;
                if(pagenumber < startPage){
                    pagenumber = startPage;
                }
                if(pagenumber > endPage){
                    pagenumber = endPage;
                }
                $timeout(function(){
                   angularConfig.goTo($location, attrs.route, {page: pagenumber}); 
                }, 0 );

            };
            scope.paginateNext = function(){
                var current = scope.paginationData.current;
                var endPage = scope.paginationData.endPage;
                var next = parseInt(current) + 1;
                if(next > endPage){
                    next = endPage;
                }
                $timeout(function(){
                   angularConfig.goTo($location, attrs.route, {page: next});
                }, 0 );

            };
            scope.paginateBack = function(){
                var current = scope.paginationData.current;
                var startPage = scope.paginationData.startPage;
                var back = parseInt(current) - 1;
                if(back < startPage){
                    back = startPage;
                }
                $timeout(function(){
                   angularConfig.goTo($location, attrs.route, {page: back});
                }, 0 );

            };
        }
    };
}]);