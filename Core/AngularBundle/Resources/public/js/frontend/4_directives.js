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
}]);