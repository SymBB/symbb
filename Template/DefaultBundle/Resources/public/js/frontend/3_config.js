var symbbUserLang = navigator.language || navigator.userLanguage; 

var angularConfig = {
    
    goTo: function(route, params, urlKey){
        window.location.href=this.getSymfonyRoute('forum_index')+'#'+this.getAngularRoute(route, params, urlKey);
    },
    
    routingData: {},
    routingListener: [],
    
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
        if(!params){
            params = {};
        }
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
    }
};