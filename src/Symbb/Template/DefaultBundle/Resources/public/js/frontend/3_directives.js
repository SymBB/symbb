symbbControllers.directive('symbbTopicMove', ['$http', '$timeout', function($http, $timeout) {
    return {
        restrict: 'E',
        replace: true,
        templateUrl: angularConfig.getSymfonyRoute('symbb_template_forum_angular', {file: 'moveTopic'}),
        link: function(scope, elm, attrs) {
            scope.id = attrs.paramId;
            $(elm).click(function(){
                var dialog = $(this).find('.modal')
                $(dialog).find('.btn-move').click(function(){
                    var select = $(dialog).find('select');
                    var forumId = $(select).val();
                    var topicId = attrs.paramId;
                    $http.post(angularConfig.getSymfonyRoute('symbb_api_topic_move', {forum: forumId, id:topicId})).success(function(data) {

                    });
                    $(dialog).modal('hide');
                });
            })
        }
    };
}]);

symbbControllers.directive('symbbUserAutocomplete', ['$http', '$timeout', function($http, $timeout) {
    return {
        restrict: 'E',
        replace: false,
        require: 'ngModel',
        link: function(scope, elm, attrs, ngModel) {
            var searchurl = angularConfig.getSymfonyRoute('symbb_api_user_search');
            $(elm).select2({
                multiple: true,
                minimumInputLength: 3,
                ajax: {
                    url: searchurl,
                    dataType: 'json',
                    cache: true,
                    quietMillis: 300,
                    data: function (term, page) {
                        return {
                            q: term,
                            limit: 10,
                            page: page
                        };
                    },
                    results: function (data, page) {
                        var more = (page * 10) < data.paginationData.totalCount;
                        var finalData = [];
                        $(data.entries).each(function(k, element){
                            finalData[finalData.length] = element;
                        })
                        return {results: finalData, more: more};
                    }
                },
                matcher: function(data){
                    console.debug(data);
                },
                formatSelection: function(state){
                    return "<span class='avatar_mini'><img class='' src='" + state.avatar + "'/></span> " + state.username +'' ;
                },
                formatResult: function(state){
                    return "<span class='avatar_mini'><img class='' src='" + state.avatar + "'/></span> " + state.username +'' ;
                },
                escapeMarkup: function (m) {
                    console.debug(m);
                    return m;
                } // we do not want to escape markup since we are displaying html in results
            });
            $(elm).on('change', function(e){
                ngModel.$setViewValue(e.val);
            });
        }
    };
}]);
