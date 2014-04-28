symbbControllers.directive('symbbEditor', function() {
    return {
        restrict: 'E',
        replace: true,
        templateUrl: angularConfig.getSymfonyTemplateRoute('bbcode', {set: bbcode.set}),
        link: function(scope, elm, attrs) {
           
        }
    };
});