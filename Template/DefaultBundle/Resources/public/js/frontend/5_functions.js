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
                    params = {id: value.id, name: value.seoName};
                } else if(value.type === 'topic'){
                    route = 'forum_topic_show';
                    params = {id: value.id, name: value.seoName};
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
    },
            
    createPostUploader: function($scope, $fileUploader, $scopeObject, $injector){

        // Creates a uploader
        var uploader = $scope.uploader = $fileUploader.create({
            scope: $scope,
            url: angularConfig.getSymfonyApiRoute('forum_post_upload_image'),
            method: 'POST',
            formData: [{id: $scope.post.id}]
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
            return '|jpg|png|jpeg|bmp|gif|'.indexOf(type) !== -1;
        });

        uploader.bind('complete', function (event, xhr, item, response) {
            response = symbbAngularUtils.checkResponse(response, $injector);
            if(response.files){
                $.each(response.files, function(key, value) {
                    $scopeObject.files[$scopeObject.files.length] = value.url;
                    item.file.path = value.url;
                    item.file.url = 'http://'+window.location.host+value.url;
                });
            }
        }); 


        $scope.bbcode = {
            insertUploadImage: function(item){
                var element = $('.symbb_editor textarea')[0];
                var tagCode = '[IMG]'+item.file.path+'[/IMG]';
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