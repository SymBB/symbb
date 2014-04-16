

var symbbApp = angular.module('symbbApp', [
    'ngRoute',
    'ngCookies',
    'ngAnimate',
    'chieffancypants.loadingBar',
    'symbbControllers'
]);

symbbApp.config(['$routeProvider', '$interpolateProvider',
    function($routeProvider, $interpolateProvider) {
        
        //changeing because of twig
        $interpolateProvider.startSymbol('[[').endSymbol(']]');
        
        angularConfig.createAngularRouting($routeProvider);
        
        
    }]
);

