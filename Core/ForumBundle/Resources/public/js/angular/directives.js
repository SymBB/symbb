symbbControllers.directive('symbbTopicMove', ['$http', '$timeout', function($http, $timeout) {
    return {
        restrict: 'A',
        replace: false,
        transclude: true,
        templateUrl: angularConfig.getSymfonyTemplateRoute('forum_topic_move'),
        link: function(scope, elm, attrs) {
            $(elm).click(function(){
                var dialog = $(this).find('.symbb_move_topic_dialog')
                var tmpDialog = dialog.clone();
                tmpDialog.attr('id', 'tmp_symbb_move_topic_dialog');
                $('body').append(tmpDialog);
                $(tmpDialog).find('.btn-move').click(function(){
                    var select = $(tmpDialog).find('select');
                    var forumId = $(select).val();
                    var topicId = attrs.paramId;
                    $http.post(angularConfig.getSymfonyApiRoute('forum_topic_move', {forum: forumId, id:topicId})).success(function(data) {
                        $(tmpDialog).dialog('close');
                    });
                });
                $(tmpDialog).dialog({
                    modal: true
                });
            })
        }
    };
}]);