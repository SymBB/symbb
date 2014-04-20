
var angularForumRouting = {
    
    routingData: {
        
        forum_index: {
            'url': ['/forum/', '/'],
            'api': 'symbb_api_forum_list',
            'template': 'symbb_template_default_angular',
            'templateParam': { file: 'forumList'},
            'controller': 'ForumCtrl'
        },
        forum_list_main: {
            'url': ['/forum/'],
            'api': 'symbb_api_forum_list',
            'template': 'symbb_template_default_angular',
            'templateParam': { file: 'forumList'},
            'controller': 'ForumCtrl'
        },
        forum_list:  {
            'url': ['/forum/:id/:name/'],
            'api': 'symbb_api_forum_list',
            'template': 'symbb_template_default_angular',
            'templateParam': { file: 'forumList'},
            'controller': 'ForumCtrl'
        },
        forum_ignore:  {
            'api': 'symbb_api_forum_ignore'
        },
        forum_unignore:  {
            'api': 'symbb_api_forum_unignore'
        },
        forum_mark_as_read:  {
            'api': 'symbb_api_forum_mark_as_read'
        },
        forum_topic_show:  {
            'url': ['/topic/:id/:name/'],
            'api': 'symbb_api_forum_topic_show',
            'template': 'symbb_template_default_angular',
            'templateParam': { file: 'forumTopicShow'},
            'controller': 'ForumTopicShowCtrl'
        },
        forum_topic_post_list:  {
            'api': 'symbb_api_forum_topic_post_list',
        },
        forum_topic_create:  {
            'url': ['/forum/:id/topic/new/'],
            'api': 'symbb_api_forum_topic_create',
            'template': 'symbb_template_default_angular',
            'templateParam': { file: 'forumTopicCreate'},
            'controller': 'ForumTopicCreateCtrl'
        }
                
    },
    
    createAngularRouting: function($routeProvider){

        $.each(this.routingData, function(key, value){
            if(value.url){
                $.each(value.url, function(urlKey, urlValue){
                    if(value.controller){
                        $routeProvider.when(angularConfig.getAngularRoute(key, {}, urlKey), { 
                            templateUrl: angularConfig.getSymfonyTemplateRoute(key),
                            controller: angularConfig.getAngularController(key)
                        }); 
                    }
                });
            }
        });
        
    }
    
};

// add routing to main routing
$.each(angularForumRouting.routingData, function(key, value){
    angularConfig.routingData[key] = value;
});

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