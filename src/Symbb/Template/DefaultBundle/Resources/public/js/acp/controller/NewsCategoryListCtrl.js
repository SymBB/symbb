symbbControllers.controller('NewsCategoryListCtrl', ["$scope", "$symbbRestCrud", "$routeParams", "$http",
    function ($scope, $symbbRestCrud, $routeParams, $http) {
        var service = new $symbbRestCrud();
        service.routingIdField = 'category';
        service.init($scope);
        $scope.addForumAsSelectOption = function (list, select, prefix) {
            $.each(list, function (key, element) {
                var option = $('<option>', {html: prefix + ' ' + element.name, value: element.id});
                $(select).append(option);
                if (element.children) {
                    $scope.addForumAsSelectOption(element.children, select, prefix + '-');
                }
            });
        };
    }
]);
