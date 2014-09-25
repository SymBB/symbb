symbbControllers.directive('symbbEditorDefault', ['$timeout', function($timeout) {
    return {
        restrict: 'E',
        transclude: true,
        replace: false,
        template: '<div class="symbb_bbcodes symbb_header_bg"><div class="symbb_bbcodes_group btn-group"></div><div class="clear"></div></div><div class="symbb_editor" ng-transclude></div><div class="preview"></div>',
        link: function(scope, elm, attrs) {
            $timeout(function(){
                BBCodeEditor.createEditor(elm);
            }, 0);
        }
    };
}]);