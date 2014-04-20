var symbbApp = angular.module('symbbApp', [
    'ngRoute',
    'ngCookies',
    'ngAnimate',
    'ngSanitize',
    'chieffancypants.loadingBar',
    'infinite-scroll',
    'symbbControllers'
]);

symbbApp.config(['$routeProvider', '$interpolateProvider',
    function($routeProvider, $interpolateProvider) {
        
        //changeing because of twig
        $interpolateProvider.startSymbol('[[').endSymbol(']]');
        
        $.each(angularConfig.routingListener, function(key, listener){
            listener($routeProvider);
        });
        
    }]
);

var symbbControllers = angular.module('symbbControllers', []);
