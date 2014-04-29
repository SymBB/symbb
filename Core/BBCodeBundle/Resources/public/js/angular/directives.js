symbbControllers.directive('symbbEditorDefault', ['$timeout', function($timeout) {
    return {
        restrict: 'E',
        transclude: true,
        replace: false,
        templateUrl: angularConfig.getSymfonyTemplateRoute('bbcode_default'),
        link: function(scope, elm, attrs) {
            $timeout(function(){
                createBBEditor(elm);
            }, 0);
        }
    };
}]);