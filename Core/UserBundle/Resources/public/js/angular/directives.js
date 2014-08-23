symbbControllers.directive('symbbUserAutocomplete', ['$http', '$timeout', function($http, $timeout) {
    return {
        restrict: 'E',
        replace: false,
        require: 'ngModel',
        link: function(scope, elm, attrs, ngModel) {
            var searchurl = angularConfig.getSymfonyApiRoute('user_search');
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

