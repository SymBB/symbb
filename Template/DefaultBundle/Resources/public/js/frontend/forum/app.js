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
        
        angularConfig.createAngularRouting($routeProvider);
        
        
    }]
);

// Topic constructor function to encapsulate HTTP and pagination logic
symbbApp.factory('Topics', function($http) {
    
  var Topics = function() {
    this.posts = [];
    this.busy = false;
    this.page = 1;
    this.id = 0;
    this.end = false;
  };

  Topics.prototype.nextPage = function() {
    if (this.busy || this.end) return;
    this.busy = true;

    var url = angularConfig.getSymfonyApiRoute('forum_topic_post_list',  {id: this.id, page: this.page + 1});
    $http.get(url).success(function(data) {
      var posts = data;
      for (var i = 0; i < posts.length; i++) {
        this.posts.push(posts[i]);
      }
      if(posts.length <= 0){
          this.end = true;
      }
      this.page = this.page + 1;
      this.busy = false;
    }.bind(this));
  };

  return Topics;
});