
var angularForumRouting = {
    
    routingData: {
        
        forum_index: {
            'url': ['/forum/', '/'],
            'api': 'symbb_api_forum_data',
            'template': 'symbb_template_default_angular',
            'templateParam': { file: 'forumList'},
            'controller': 'ForumCtrl'
        },
        forum_show_main: {
            'url': ['/forum/'],
            'api': 'symbb_api_forum_data',
            'template': 'symbb_template_default_angular',
            'templateParam': { file: 'forumList'},
            'controller': 'ForumCtrl'
        },
        forum_show:  {
            'url': ['/forum/:id/:name/'],
            'api': 'symbb_api_forum_data',
            'template': 'symbb_template_default_angular',
            'templateParam': { file: 'forumList'},
            'controller': 'ForumCtrl'
        },
        forum_topic_show:  {
            'url': ['/topic/:id/:name/'],
            'api': 'symbb_api_topic_data',
            'template': 'symbb_template_default_angular',
            'templateParam': { file: 'forumTopicShow'},
            'controller': 'ForumTopicShowCtrl'
        },
        forum_topic_create:  {
            'url': ['/forum/:id/topic/new/'],
            'api': 'symbb_api_topic_data',
            'template': 'symbb_template_default_angular_form',
            'templateParam': { file: 'topic'},
            'controller': 'ForumTopicCreateCtrl'
        },
        forum_post_create:  {
            'url': ['/topic/:topic/post/new'],
            'api': 'symbb_api_post_data',
            'template': 'symbb_template_default_angular_form',
            'templateParam': { file: 'post'},
            'controller': 'ForumPostEditCtrl'
        },
        forum_post_quote:  {
            'url': ['/post/:topic/post/quote/:quoteId'],
            'api': 'symbb_api_post_data',
            'template': 'symbb_template_default_angular_form',
            'templateParam': { file: 'post'},
            'controller': 'ForumPostEditCtrl'
        },
        forum_post_edit:  {
            'url': ['/post/:id/edit'],
            'api': 'symbb_api_post_data',
            'template': 'symbb_template_default_angular_form',
            'templateParam': { file: 'post'},
            'controller': 'ForumPostEditCtrl'
        },
             
             
        // only API Calls! ( not frontend! )
        forum_ignore:  {
            'api': 'symbb_api_forum_ignore'
        },
        forum_unignore:  {
            'api': 'symbb_api_forum_unignore'
        },
        forum_mark_as_read:  {
            'api': 'symbb_api_forum_mark_as_read'
        },  
        forum_topic_save:  {
            'api': 'symbb_api_topic_save'
        },
        forum_topic_list:  {
            'api': 'symbb_api_topic_list',
        }, 
        forum_post_list:  {
            'api': 'symbb_api_post_list',
        }, 
        forum_post_upload_image:  {
            'api': 'symbb_api_post_upload_image'
        },
        forum_post_save:  {
            'api': 'symbb_api_post_save'
        },
        forum_post_delete:  {
            'api': 'symbb_api_post_delete'
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
app.factory('ScrollPagination', function($http) {
    
  var ScrollPagination = function(route, routeParams, items, page, total) {
      
    if(!page){
        page = 1;
    }
      
    this.items = items;
    this.busy = false;
    this.page = page;
    this.routeParams = routeParams;
    this.end = false;
    this.count = items.length;
    this.totalCount = total;
    this.route = route;
    
    if(this.count >= this.totalCount) {
       this.end = true;
    }
    
  };

  ScrollPagination.prototype.nextPage = function() {
      
    if (this.busy || this.end) return;
    
    this.busy = true;
        
    this.page = this.page + 1;

    this.routeParams.page = this.page;

    var url = angularConfig.getSymfonyApiRoute(this.route);
    url = url + '?id='+this.routeParams.id;
    url = url + '&page='+this.routeParams.page;
    $http.get(url).success(function(data) {
      
      var items = data.items;
      
      this.totalCount = data.total;
      
      for (var i = 0; i < items.length; i++) {
        this.items.push(items[i]);
        this.count++;
      }

      if(!items.length || items.length <= 0 || this.count >= this.totalCount ){
          this.end = true;
      }

      this.busy = false;
      
    }.bind(this));
  };

  return ScrollPagination;
});