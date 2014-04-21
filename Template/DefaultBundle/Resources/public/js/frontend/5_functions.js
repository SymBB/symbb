var refesh = function(data, route){
    route.reload();
};

var textMatchOneLine = function(){
    $(".textMatchOneLine").each(function() {
        var jThis=$(this);
        var fontSize = parseInt(jThis.css("font-size"));
        for(var i=0; jThis.height() > (fontSize + 5) && i<30;i++)
        { 
            fontSize--;
            jThis.css("font-size",fontSize+"px"); 
        }
    });
};

var symbbAngularUtils = {
    
    breadcrumbElement: null,
    
    checkResponse: function(response, $injector){

        var $route = $injector.get('$route');
        var data = response.data;
        var errors = false;
        
        if(data.messages){
            $.each(data.messages, function(key, value){
                if(value.type === 'error'){
                    errors = true;
                }
                var myMessage = $('<div class="alert alert-'+value.bootstrapType+'"><button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>'+value.message+'</div>');
                myMessage.appendTo($('#symbbMessages'));
                setTimeout(function(){myMessage.remove();}, 10000); 
            });
        } 
        
        if(data.callbacks){
            $.each(data.callbacks, function(key, value){
                // find object
                var fn = window[value];

                // is object a function?
                if (typeof fn === "function") fn(data, $route);
            });
        } 

        if(data.breadcrumbItems && data.breadcrumbItems.length > 0){
            this.createBreadcrumnb(data.breadcrumbItems);
        }
        
        return response;
    },
            
    createBreadcrumnb: function(items){
        if(this.breadcrumbElement){
            $(this.breadcrumbElement).html('<li><div class="avatar avatar_mini"><img src="'+symbbUser.avatar+'" /></div></li>');
            var spacer = '<span class="spacer">/</span>';
            var count = 0;
            $.each(items, function(key, value){
                count++;
            });
            var i = 0;
            var that = this;
            $.each(items, function(key, value){
                if(i === count - 1){
                    spacer = '';
                }
                var route = 'forum_index';
                var params = {};
                if(value.type === 'forum'){
                    route = 'forum_show';
                    params = {id: value.id, name: value.name};
                } else if(value.type === 'topic'){
                    route = 'forum_topic_show';
                    params = {id: value.id, name: value.name};
                }  else if(value.type === 'home'){
                    route = 'forum_index';
                } else {
                    console.debug(value);
                }

                var path = angularConfig.getAngularRoute(route, params);
                $('<li><a href="#'+path+'">'+value.name+'</a>'+spacer+'</li>').appendTo($(that.breadcrumbElement));
                i++;
            });
        }
    }
    
};