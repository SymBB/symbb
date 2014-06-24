var refresh = function(data, route){
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

    checkResponse: function(data, $injector){

        var $route = $injector.get('$route');
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
        
        return data;
    },

    createBreadcrumbLi: function(item, spacer){
        var route = 'forum_index';
        var params = {};
        if(item.type === 'forum'){
            route = 'forum_show';
            params = {id: item.id, name: item.seoName};
        } else if(item.type === 'topic'){
            route = 'forum_topic_show';
            params = {id: item.id, name: item.seoName};
        }  else if(item.type === 'home'){
            route = 'forum_index';
        } else {
            console.debug(item);
        }
        if(item){
            var path = angularConfig.getAngularRoute(route, params);
            return $('<li><a href="'+path+'">'+item.name+'</a>'+spacer+'</li>');
        }
    },
            
    createBreadcrumnb: function(items){

        $(symbbAngularUtils.breadcrumbElement).html("");
        $(symbbAngularUtils.breadcrumbElement).prepend($('<li><div class="avatar avatar_mini"><img src="'+symbbUser.avatar+'" /></div></li>'));
        var spacer = '<span class="glyphicon glyphicon-chevron-right"></span>';
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
            var item = symbbAngularUtils.createBreadcrumbLi(value, spacer);
            if(item){
                $(symbbAngularUtils.breadcrumbElement).append(item);
            }
            i++;
        });
    },
            
    createPostUploader: function($scope, $fileUploader, $scopeObject, $injector){

        // Creates a uploader
        var uploader = $scope.uploader = $fileUploader.create({
            scope: $scope,
            url: angularConfig.getSymfonyApiRoute('forum_post_upload_image'),
            method: 'POST',
            formData: [{id: $scopeObject.id}]
        });

        $.each($scopeObject.files, function(key, value) {                
            var item = {
                file: {
                    name: value,
                    path: value,
                    url: 'http://'+window.location.host+value
                },
                progress: 100,
                isUploaded: true,
                isSuccess: true
            };
            uploader.queue.push(item);
            item.remove = function() {
                uploader.removeFromQueue(this);
            };
            uploader.progress = 100;
        });

        // ADDING FILTERS
        // Images only
        uploader.filters.push(function(item /*{File|HTMLInputElement}*/) {
            var type = uploader.isHTML5 ? item.type : '/' + item.value.slice(item.value.lastIndexOf('.') + 1);
            type = '|' + type.toLowerCase().slice(type.lastIndexOf('/') + 1) + '|';
            return '|jpg|png|jpeg|bmp|gif|txt|pdf|doc|plain|'.indexOf(type) !== -1;
        });

        uploader.bind('complete', function (event, xhr, item, response) {
            response = symbbAngularUtils.checkResponse(response, $injector); 
            if(response.files){
                $.each(response.files, function(key, value) {
                    $scopeObject.files[$scopeObject.files.length] = value.url;
                    item.path = value.url;
                    item.url = 'http://'+window.location.host+value.url;
                });
            }
        }); 


        $scope.bbcode = {
            insertUploadImage: function(item){
                var element = $('.symbb_editor textarea')[0];
                if(
                    item.file.type === 'image/jpeg' ||
                    item.file.type === 'image/jpg' ||
                    item.file.type === 'image/png' ||
                    item.file.type === 'image/gif' ||
                    item.file.type === 'image/bmp'
                ){
                    var tagCode = '[IMG]'+item.path+'[/IMG]';
                } else {
                    var tagCode = '[LINK=http://'+item.path+']'+item.file.name+'[/LINK]';
                }

                if (document.selection) {
                    element.focus();
                    var sel = document.selection.createRange();
                    sel.text = tagCode.replace('{0}', sel.text);
                    $scopeObject.rawText = element.value;
                } else if (element.selectionStart || element.selectionStart == '0') {
                    element.focus();
                    var startPos = element.selectionStart;
                    var endPos = element.selectionEnd;
                    element.value = element.value.substring(0, startPos) + tagCode.replace('{0}', element.value.substring(startPos, endPos)) + element.value.substring(endPos, element.value.length);
                    $scopeObject.rawText = element.value;
                } else {
                    element.value += tagCode.replace('{0}', '');
                    $scopeObject.rawText = element.value;
                }
           }
        };
    }
    
};

// Topic constructor function to encapsulate HTTP and pagination logic
app.factory('ScrollPagination', function($http) {
    
  var ScrollPagination = function(route, routeParams, itemsKey) {

    if(!itemsKey){
        itemsKey = 'items';
    }
      
    this.items = [];
    this.busy = false;
    this.page = 0;
    this.routeParams = routeParams;
    this.end = false;
    this.count = 0;
    this.lastPage = 99;
    this.route = route;
    this.itemsKey = itemsKey;
    
    if(this.page == this.lastPage) {
       this.end = true;
    }

    this.nextPage();
    
  };

  ScrollPagination.prototype.nextPage = function() {
      
    if (this.busy || this.end) return;
    
    this.busy = true;
        
    this.page = parseInt(this.page) + 1;

    this.routeParams.page = this.page;

    var url = angularConfig.getSymfonyApiRoute(this.route, this.routeParams);

    $http.get(url).success(function(data) {
      
      var items = data[this.itemsKey];

      this.page = data.paginationData.current;
      this.lastPage = data.paginationData.endPage;

      for (var i = 0; i < items.length; i++) {
        if(items[i].flags){
            items[i].tmp = [];
            items[i].tmp.css = '';
            $.each( items[i].flags, function( key, value ) {
                items[i].tmp.css += value.type+' ';
            });
            this.items.push(items[i]);
        }
        this.count++;
      }

      if(!items.length || items.length <= 0 || this.page == this.lastPage ){
          this.end = true;
      }

      this.busy = false;
      
    }.bind(this));
  };

  return ScrollPagination;
});