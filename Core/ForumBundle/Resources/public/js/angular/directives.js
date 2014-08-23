symbbControllers.directive('symbbTopicMove', ['$http', '$timeout', function($http, $timeout) {
    return {
        restrict: 'E',
        replace: true,
        templateUrl: angularConfig.getSymfonyTemplateRoute('forum_topic_move'),
        link: function(scope, elm, attrs) {
            scope.id = attrs.paramId;
            $(elm).click(function(){
                var dialog = $(this).find('.modal')
                $(dialog).find('.btn-move').click(function(){
                    var select = $(dialog).find('select');
                    var forumId = $(select).val();
                    var topicId = attrs.paramId;
                    $http.post(angularConfig.getSymfonyApiRoute('forum_topic_move', {forum: forumId, id:topicId})).success(function(data) {

                    });
                    $(dialog).modal('hide');
                });
            })
        }
    };
}]);