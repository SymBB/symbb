var symbbUserLang = navigator.language || navigator.userLanguage; 

var symbbApp = angular.module('symbbApp', [
    'ngRoute',
    'ngCookies',
    'ngAnimate',
    'chieffancypants.loadingBar',
    'symbbControllers'
]);

symbbApp.config(['$routeProvider', '$interpolateProvider',
    function($routeProvider, $interpolateProvider) {
        
        $interpolateProvider.startSymbol('[[').endSymbol(']]');
        
        $routeProvider.
                when('/forum', { 
            templateUrl: Routing.generate('symbb_template_default_angular', { file: 'forumList' , '_locale': symbbUserLang}),
            controller: 'ForumCtrl'
        }).
                otherwise({
            redirectTo: '/forum'
        });
    }]
);

