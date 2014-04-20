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
                    var route = 'forum_index';
                    var params = {};
                    if(value.type === 'forum'){
                        route = 'forum_list';
                        params = {id: value.id, name: value.name};
                    } else if(value.type === 'topic'){
                        route = 'forum_topic_show';
                        params = {id: value.id, name: value.name};
                    }  else if(value.type === 'home'){
                        route = 'forum_index';
                    } else {
                        console.debug(value);
                    }
                    var path = angularConfig.getAngularRoute(route, params);
                    $('<li><a href="#'+path+'">'+value.name+'</a>'+spacer+'</li>').appendTo($(elm[0]));
                    i++;
                });
            };
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
                    if(data.messages){
                        $.each(data.messages, function(key, value){
                            $('<div class="alert alert-'+value.type+'"><button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>'+value.message+'</div>').appendTo($('#symbbMessages'));
                        });
                    } 
                    if(data.callbacks){
                        $.each(data.callbacks, function(key, value){
                            // find object
                            var fn = window[value];

                            // is object a function?
                            if (typeof fn === "function") fn(scope, $route);
                        });
                    }
                });
            });
        }
    };
}]);   ;



