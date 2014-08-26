symbbControllers.directive('symbbExtensionShoutbox', ['$http', '$timeout', function($http, $timeout) {
    return {
        restrict: 'A',
        replace: false,
        link: function(scope, elm, attrs) {
            $timeout(function(){
                loadSymbbExtensionShoutboxData(scope, $http);
                setInterval(function() {
                    loadSymbbExtensionShoutboxData(scope, $http);
                }, 30000);
                scope.saveExtensionShoutboxMessage = function(){
                    $http.post(angularConfig.getSymfonyRoute('symbb_api_extension_shoutbox_save', {message: scope.extension.shoutbox.newMessage})).success(function(data) {
                        if(data.success){
                            loadSymbbExtensionShoutboxData(scope, $http);
                        }
                    });
                }
            }, 300); // make a timeout, if not it will be loaded before the forum itself...
        }
    };
}]);


function loadSymbbExtensionShoutboxData(scope, $http){
    $http.post(angularConfig.getSymfonyRoute('symbb_api_extension_shoutbox_list')).success(function(data) {
        if(!scope.extension){
            scope.extension = {}
        }
        if(!scope.extension.shoutbox){
            scope.extension.shoutbox = {}
        }
        scope.extension.shoutbox.entries = data.shoutboxEntries;
        $('.shoutbox_content').animate({
            scrollTop:  1000
        });
    });
}