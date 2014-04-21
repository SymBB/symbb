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
        template: '<a href="" ng-transclude></a>',
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
            if(attrs.paramId){
                params.id = attrs.paramId;
            }
            if(attrs.paramName){
                params.name = attrs.paramName;
            }
            var path = angularConfig.getAngularRoute(attrs.symbbLink, params);
            $(element[0]).children('a').attr('href', angularConfig.getSymfonyRoute('symbb_forum_index')+'#'+path);
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
}]).directive('symbbFormTabs', ['$timeout', function(timer) {
    return {
        restrict: 'A',
        transclude: false,
        replace: false,
        link: function(scope, element, attrs) {
            timer(symbbTabs, 0)
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
                if(attrs.paramId){
                    params.id = attrs.paramId;
                }
                if(attrs.paramName){
                    params.name = attrs.paramName;
                }
                $http.post(angularConfig.getSymfonyApiRoute(attrs.symbbRequest, params)).success(function(data) {
                    
                });
            });
        }
    };
}]);   ;



