var symbbUserLang = navigator.language || navigator.userLanguage; 

var angularConfig = {
    
    goTo: function(route, params, urlKey){
        window.location.href='#'+this.getAngularRoute(route, params, urlKey);
    },
    
    routingData: {
        index: {
            'url': ['/forum', '/:lang/forum', '/:lang', '/'],
            'template': 'symbb_template_default_angular',
            'templateParam': { file: 'forumList'},
            'controller': 'ForumCtrl'
        },
        forum_list_main: {
            'url': ['/forum'],
            'template': 'symbb_template_default_angular',
            'templateParam': { file: 'forumList'},
            'controller': 'ForumCtrl'
        },
        forum_list:  {
            'url': ['/forum/:id/:name'],
            'template': 'symbb_template_default_angular',
            'templateParam': { file: 'forumList'},
            'controller': 'ForumCtrl'
        },
        forum_ignore:  {
            'url': ['topic/:id/ignore'],
            'controller': 'ForumIgnoreCtrl'
        },
        forum_unignore:  {
            'url': ['topic/:id/unignore'],
            'controller': 'ForumUnignoreCtrl'
        },
        forum_topic_list:  {
            'url': ['/forum/:id/:name/topics/:page'],
            'template': 'symbb_template_default_angular',
            'templateParam': { file: 'forumTopicList'},
            'controller': 'ForumTopicListCtrl'
        },
        forum_topic_show:  {
            'url': ['/topic/:id/:name'],
            'template': 'symbb_template_default_angular',
            'templateParam': { file: 'forumTopicShow'},
            'controller': 'ForumTopicShowCtrl'
        },
        forum_topic_create:  {
            'url': ['/forum/:id/topic/new'],
            'template': 'symbb_template_default_angular',
            'templateParam': { file: 'forumTopicShow'},
            'controller': 'ForumTopicCreateCtrl'
        },
        forum_topic_delete:  {
            'url': ['topic/:id/delete'],
            'controller': 'ForumTopicDeleteCtrl'
        }
    },
  
    getSymfonyTemplateRoute: function(route, params){
        var routePath =  '';
        if(this.routingData[route] && this.routingData[route]['template']){
            if(!params){
                params = {};
            }
            var realParams = this.routingData[route]['templateParam'];
            if(!realParams){
                realParams = {};
            }
            $.each(params, function(key, value){
                realParams[key] = value;
            });
            realParams._locale = symbbUserLang;
            routePath = Routing.generate(this.routingData[route]['template'], realParams);
        }
        return routePath;
    },
  
    getAngularController: function(route){
        return this.routingData[route]['controller'];
    },
  
    getAngularRoute: function(route, params, urlKey){

        if(!params){
            params = {};
        }
        if(!urlKey){
            urlKey = 0;    
        }
        var routePath = this.routingData[route]['url'];
        routePath = routePath[urlKey];
        $.each(params, function(key, value){
            routePath = routePath.replace(':'+key, value);
        });
        return routePath;
    },
    
    createAngularRouting: function($routeProvider){

        $.each(this.routingData, function(key, value){
            $.each(value.url, function(urlKey, urlValue){
                if(key){
                    $routeProvider.when(angularConfig.getAngularRoute(key, {}, urlKey), { 
                    templateUrl: angularConfig.getSymfonyTemplateRoute(key),
                    controller: angularConfig.getAngularController(key)
                  }); 
                } 
            });
            
        });
        
    }
};