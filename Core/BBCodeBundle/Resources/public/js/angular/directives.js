symbbControllers.directive('symbbEditorDefault', ['$timeout', function($timeout) {
    return {
        restrict: 'E',
        transclude: true,
        replace: false,
        templateUrl: angularConfig.getSymfonyTemplateRoute('bbcode_default'),
        link: function(scope, elm, attrs) {
            $timeout(function(){
                $(elm).find('.symbb_bbcode_btn').each(function(key, btn){
                    $( btn).click(function() {
                        var element = $(elm).find('textarea')[0];
                        
                        var tagCode = $(btn).data('tag-code');
                        
                        if (document.selection) {
                            var sel = document.selection.createRange();
                            sel.text = tagCode.replace('{0}', sel.text);
                        } else if (element.selectionStart || element.selectionStart == '0') {
                            var startPos = element.selectionStart;
                            var endPos = element.selectionEnd;
                            element.value = element.value.substring(0, startPos) + tagCode.replace('{0}', element.value.substring(startPos, endPos)) + element.value.substring(endPos, element.value.length);
                        } else {
                            element.focus();
                            element.value += tagCode.replace('{0}', '');
                        }
                        
                        $(element).change();
                    });
                });
            }, 0);
        }
    };
}]);