symbbControllers.controller('ForumListCtrl', ["$scope", "$symbbRestCrud", "$routeParams", "$http",
    function ($scope, $symbbRestCrud, $routeParams, $http) {


        var service = new $symbbRestCrud();
        service.parentIdField = 'parent';
        service.routingIdField = 'forum';
        service.treeSortable = true;
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


        $scope.access = function (node) {

            $scope.accessEntry = {
                forumFrom: 0,
                forumTo: node.id,
                set: 0,
                group: 0,
                childs: 0
            };


            $('#accessFormForumList').html("");
            var option = $('<option>', {html: ' ', value: 0});
            $('#accessFormForumList').append(option);

            $scope.addForumAsSelectOption($scope.data, $('#accessFormForumList'), "");

            $('#accessForm').find('.modal').modal('show');
            $('#accessForm').find('.modal-button').unbind("click");
            $('#accessForm').find('.modal-button').click(function () {
                if (
                    $scope.accessEntry.forumFrom > 0 &&
                    $scope.accessEntry.forumTo > 0 &&
                    $scope.accessEntry.group > 0 &&
                    $scope.accessEntry.set == 0
                ) {
                    var routeParams = {_locale: $routeParams._locale};
                    var route = angularConfig.getSymfonyRoute('symbb_backend_api_forum_copy_access', routeParams);
                    $http.post(route, {data: $scope.accessEntry}).success(function (response) {
                        if (response.success) {
                            $('#accessForm').find('.modal').modal('hide');
                        }
                        $scope.restCrudSaving = false;
                    }).error(function () {
                        $scope.restCrudSaving = false;
                    })
                } else if (
                    $scope.accessEntry.forumFrom == 0 &&
                    $scope.accessEntry.forumTo > 0 &&
                    $scope.accessEntry.group > 0 &&
                    $scope.accessEntry.set > 0
                ) {
                    var routeParams = {_locale: $routeParams._locale};
                    var route = angularConfig.getSymfonyRoute('symbb_backend_api_forum_apply_access_set', routeParams);
                    $http.post(route, {data: $scope.accessEntry}).success(function (response) {
                        if (response.success) {
                            $('#accessForm').find('.modal').modal('hide');
                        }
                        $scope.restCrudSaving = false;
                    }).error(function () {
                        $scope.restCrudSaving = false;
                    })
                }
            });
        }
    }
]);
