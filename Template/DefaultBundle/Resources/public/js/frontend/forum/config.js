var symbbUserLang = navigator.language || navigator.userLanguage; 

var angularConfig = {
    
    goTo: function(route, params, urlKey){
        console.debug(1);
        window.location.href=this.getAngularRoute(route, params, urlKey);
    },
    
    routingData: {
        'index': {
            'url': ['/forum', '/:lang/forum', '/:lang', '/'],
            'template': 'symbb_template_default_angular',
            'templateParam': { file: 'forumList'},
            'controller': 'ForumCtrl'
        },
        'forum_list_main': {
            'url': ['/forum'],
            'template': 'symbb_template_default_angular',
            'templateParam': { file: 'forumList'},
            'controller': 'ForumCtrl'
        },
        'forum_list':  {
            'url': ['/forum/:id'],
            'template': 'symbb_template_default_angular',
            'templateParam': { file: 'forumList'},
            'controller': 'ForumCtrl'
        }
    },
  
    getSymfonyRoute: function(route, params){
        if(!params){
            params = {};
        }
        var realParams = this.routingData[route]['templateParam'];
        $.each(params, function(key, value){
            realParams[key] = value;
        });
        realParams._locale = symbbUserLang;
        var routePath = Routing.generate(this.routingData[route]['template'], realParams);
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
                $routeProvider.when(angularConfig.getAngularRoute(key, {}, urlKey), { 
                  templateUrl: angularConfig.getSymfonyRoute(key),
                  controller: angularConfig.getAngularController(key)
                });  
            });
            
        });
        
    }
};