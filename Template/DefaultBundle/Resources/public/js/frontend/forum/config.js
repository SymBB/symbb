var symbbUserLang = navigator.language || navigator.userLanguage; 

var angularConfig = {
    
    goTo: function(route, params, urlKey){
        window.location.href='#'+this.getAngularRoute(route, params, urlKey);
    },
    
    routingData: {
        index: {
            'url': ['/forum', '/:lang/forum', '/:lang', '/'],
            'api': 'symbb_api_forum_list',
            'template': 'symbb_template_default_angular',
            'templateParam': { file: 'forumList'},
            'controller': 'ForumCtrl'
        },
        forum_list_main: {
            'url': ['/forum'],
            'api': 'symbb_api_forum_list',
            'template': 'symbb_template_default_angular',
            'templateParam': { file: 'forumList'},
            'controller': 'ForumCtrl'
        },
        forum_list:  {
            'url': ['/forum/:id/:name'],
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
            'url': ['/topic/:id/:name'],
            'api': 'symbb_api_forum_topic_show',
            'template': 'symbb_template_default_angular',
            'templateParam': { file: 'forumTopicShow'},
            'controller': 'ForumTopicShowCtrl'
        },
        forum_topic_post_list:  {
            'api': 'symbb_api_forum_topic_post_list',
        },
        forum_topic_create:  {
            'url': ['/forum/:id/topic/new'],
            'api': 'symbb_api_forum_topic_create',
            'template': 'symbb_template_default_angular',
            'templateParam': { file: 'forumTopicCreate'},
            'controller': 'ForumTopicCreateCtrl'
        },
                
    },
  
    getSymfonyApiRoute: function(route, params){
        var routePath =  '';
        if(this.routingData[route] && this.routingData[route]['api']){
            if(!params){
                params = {};
            }
            params._locale = symbbUserLang;
            routePath = Routing.generate(this.routingData[route]['api'], params);
        }
        return routePath;
    },
  
    getSymfonyRoute: function(route, params){
        params._locale = symbbUserLang;
        var routePath =  Routing.generate(route, params);
        return routePath;
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