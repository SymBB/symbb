symbbControllers.controller('NewsListCtrl', ["$scope", "$symbbRestCrud", "$routeParams", "$http",
    function ($scope, $symbbRestCrud, $routeParams, $http) {
        var service = new $symbbRestCrud();
        service.init($scope);
        $(".symbb_editor").each(function (key, element) {
            var textarea = $(element).find("textarea");
            var height = 300;
            if($(element).hasClass("small")){
                var height = 150;
            }
            var editor = $(textarea).editable(
                {
                    inlineMode: false,
                    autosave: true,
                    autosaveInterval: 500,
                    toolbarFixed: false,
                    theme: 'gray',
                    minHeight: height
                }
            );
            $(textarea).on('editable.beforeSave', function (e, editor) {
                var html = editor.getHTML();
                $(textarea).html(html);
                //dont send save request we will put the text into the from field
                return false;
            });
        });
    }
]);
