symbbControllers.controller('NewsCategoryListCtrl', ["$scope", "$symbbRestCrud", "$routeParams", "$http",
    function ($scope, $symbbRestCrud, $routeParams, $http) {
        var service = new $symbbRestCrud();
        service.afterAssignData = function($scope, response){
            if($scope.forumList){
                $scope.addForumAsSelectOption($scope.forumList, $('#targetForumList'));
            }
        };
        service.routingIdField = 'category';
        service.init($scope);
        service.afterOpenEdit = function(entry){
            $('.chosen-select').val(entry.targetForum);
            $('.chosen-select').trigger('chosen:updated');
        };
        service.beforeSave = function(entry){
            entry.targetForum = $('.chosen-select').val();
            return entry;
        };
        $scope.addForumAsSelectOption = function (list, select) {
            $.each(list, function (key, element) {
                var option = $('<option>', {html: element.name, value: element.id});
                if(element.type != "forum"){
                    $(option).attr('disabled', 'disabled');
                }
                $(select).append(option);
            });
        };

    }
]);
