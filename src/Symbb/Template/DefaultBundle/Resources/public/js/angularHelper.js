// class to manage Routings based on the Configuration of the Backend
// some helper methods
var angularConfig = {

    goTo: function($timeout, $location, route, params, urlKey){
        var routing = this.getAngularRoute(route, params, urlKey, true);
        $timeout(function() {
            $location.path(routing);
        }, 0 );
        //$scope = angular.element(document).scope(); // this is the came as $rootScope
        //$scope.$apply(); // so this also has no effect
    },

    angularRoutes: [],

    getSymfonyRoute: function(route, params){
        if(!params){
            params = {};
        }
        params._locale = symbbUser.lang;
        var routePath =  Routing.generate(route, params);
        return routePath;
    },

    getSymfonyApiRoute: function(route, params){
        var routePath =  '';
        if(this.angularRoutes[route] && this.angularRoutes[route]['api']){
            if(!params){
                params = {};
            }
            var realParams = this.angularRoutes[route]['api']['params'];
            if(!realParams){
                realParams = {};
            }
            $.each(params, function(key, value){
                realParams[key] = value;
            });
            realParams._locale = symbbUser.lang;
            routePath = Routing.generate(this.angularRoutes[route]['api']['route'], realParams);
        }
        return routePath;
    },

    getSymfonyApiRouteKey: function(route, params){
        var routePath =  '';
        if(this.angularRoutes[route] && this.angularRoutes[route]['api']){
            routePath = this.angularRoutes[route]['api']['route']
        }
        return routePath;
    },

    getSymfonyTemplateRoute: function(route, params){
        var routePath =  '';
        if(this.angularRoutes[route] && this.angularRoutes[route]['template']){
            if(!params){
                params = {};
            }
            var realParams = this.angularRoutes[route]['template']['params'];
            if(!realParams){
                realParams = {};
            }
            $.each(params, function(key, value){
                realParams[key] = value;
            });
            realParams._locale = symbbUser.lang;
            routePath = Routing.generate(this.angularRoutes[route]['template']['route'], realParams);
        }
        return routePath;
    },

    getAngularController: function(route){
        return this.angularRoutes[route]['controller'];
    },

    getAngularRoute: function(route, params, urlKey, removeHost){

        if(!params){
            params = {};
        }

        params._locale = symbbUser.lang;

        if(!urlKey){
            urlKey = 0;
        }
        if(this.angularRoutes[route]){
            var routePath = this.angularRoutes[route]['pattern'];
            routePath = routePath[urlKey];
            $.each(this.angularRoutes[route]['defaults'], function(key, value){
                if(!params[key]){
                    params[key] = value;
                }
            });
            $.each(params, function(key, value){
                routePath = routePath.replace(':'+key, value);
            });
            if(!removeHost){
                routePath = 'http://'+window.location.host+routePath;
            }
        } else {
            console.debug('Route not found! -> '+route);
            console.debug(this.angularRoutes);
        }


        return routePath;
    },

    createAngularRouting: function($routeProvider){
        $.each(this.angularRoutes, function(key, value){
            if(value.pattern){
                $.each(value.pattern, function(urlKey, urlValue){
                    if(value.controller){
                        $routeProvider.when(urlValue, {
                            templateUrl: angularConfig.getSymfonyTemplateRoute(key),
                            controller: angularConfig.getAngularController(key)
                        });
                    }
                });
            }
        });
    },

    getRoutingKeyBasedOnPattern: function(pattern){
        var finalKey = '';
        $.each(this.angularRoutes, function(key, value){
            if(value.pattern){
                $.each(value.pattern, function(urlKey, urlValue){
                    if( urlValue == pattern){
                        finalKey = key;
                    }
                });
            }
        });
        return finalKey;
    }
};


// Change Template Symbold
// create dynamicly the Routing based on the provided Data
app.config(['$routeProvider', '$interpolateProvider', '$httpProvider', '$locationProvider',
    function($routeProvider, $interpolateProvider, $httpProvider, $locationProvider) {

        //changeing because of twig
        $interpolateProvider.startSymbol('[[').endSymbol(']]');
        //html5 pushState
        $locationProvider.html5Mode(true);

        angularConfig.createAngularRouting($routeProvider);
        //angularConfig.configHook($routeProvider, $interpolateProvider, $httpProvider, $locationProvider);

        // Add the interceptor to the $httpProvider.
        $httpProvider.interceptors.push('symbbApiHttpInterceptor');

    }]
);

// check every Request for API Errors/Messages
app.factory('symbbApiHttpInterceptor', function($q, $injector) {
    return {
        // On request success
        request: function(config) {
            // console.log(config); // Contains the data about the request before it is sent.
            // Return the config or wrap it in a promise if blank.
            symbbAngularUtils.startLoading($injector.get('$rootScope'));
            return config || $q.when(config);
        },
        // On request failure
        requestError: function(rejection) {
            // console.log(rejection); // Contains the data about the error on the request.
            // Return the promise rejection.
            return $q.reject(rejection);
        },
        // On response success
        response: function(response) {
            if(typeof response.data  === 'object'){
                response.data = symbbAngularUtils.checkResponse(response.data, $injector);
            }
            symbbAngularUtils.cancelLoading($injector.get('$rootScope'));
            // Return the response or promise.
            return response || $q.when(response);
        },
        // On response failture
        responseError: function(rejection) {
            symbbAngularUtils.checkResponse(rejection, $injector);
            //console.log(rejection); // Contains the data about the error.
            // Return the promise rejection.
            symbbAngularUtils.cancelLoading($injector.get('$rootScope'));
            return $q.reject(rejection);
        }
    };
});


// Default Controller for Api and co
var symbbControllers = angular.module('symbbControllers', []);

//default controller
symbbControllers.controller('DefaultApiCtrl', ['$scope', '$http', '$routeParams', '$anchorScroll', '$route',
    function($scope, $http, $routeParams, $anchorScroll, $route) {
        var pattern = $route.current.$$route.originalPath;
        var routingKey = angularConfig.getRoutingKeyBasedOnPattern(pattern);
        if(routingKey){
            var route = angularConfig.getSymfonyApiRoute(routingKey, $routeParams);
            if(route){
                $http.get(route).success(function(data) {
                    $.each(data, function(key, value) {
                        $scope[key] = value;
                    });
                });
                $anchorScroll();
            } else {
                console.debug('No Api Route found for: '+routingKey)
            }
        } else {
            console.debug('No configured angular route found for: '+pattern)
        }
    }
]);

symbbControllers.controller('DefaultCtrl', ['$scope', '$http', '$routeParams', '$anchorScroll', '$route',
    function($scope, $http, $routeParams, $anchorScroll, $route) {
        var pattern = $route.current.$$route.originalPath;
        var routingKey = angularConfig.getRoutingKeyBasedOnPattern(pattern);
        if(routingKey){
            $anchorScroll();
        } else {
            console.debug('No configured angular route found for: '+pattern)
        }
    }
]);


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

    startLoading: function($scope){
        $scope.symbbLoading = true;
        if(!$scope.symbbLoadings){
            $scope.symbbLoadings = [];
        }
        $scope.symbbLoadings[$scope.symbbLoadings.length] = true;
    },

    cancelLoading: function($scope){

        if($scope.symbbLoadings){
            $scope.symbbLoadings.shift();
            if($scope.symbbLoadings.length <= 0){
                $scope.symbbLoading = false;
            }
        } else {
            $scope.symbbLoading = false;
        }


    },

    checkResponse: function(data, $injector){

        var $route = $injector.get('$route');
        var errors = false;
        if(data.status === 500){
            data.messages = [
                {
                    type: 'error',
                    bootstrapType: "danger",
                    message: "Server error!"
                }
            ];
        }
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
        var route = 'symbb_forum_index';
        var params = {};
        if(item.type === 'forum'){
            route = 'symbb_forum_show';
            params = {id: item.id, name: item.seoName};
        } else if(item.type === 'topic'){
            route = 'symbb_forum_topic_show';
            params = {id: item.id, name: item.seoName};
        }  else if(item.type === 'home'){
            route = 'symbb_forum_index';
        } else if(item.type === 'message_home'){
            route = 'symbb_message_list';
        }   else if(item.type === 'message'){
            route = 'symbb_message_show';
            params = {id: item.id};
        } else {
            console.debug(item);
        }
        if(item){
            var path = angularConfig.getAngularRoute(route, params);
            return $('<li><a href="'+path+'">'+item.name+'</a>'+spacer+'</li>');
        }
    },

    createBreadcrumnb: function(items){

        $(symbbAngularUtils.breadcrumbElement).find("li").each(function(key, element){
            if(!$(element).hasClass('pull-right')){
                $(element).remove();
            }
        });
        var spacer = '';
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
            url: angularConfig.getSymfonyRoute('symbb_api_post_upload_image'),
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

        var url = angularConfig.getSymfonyRoute(this.route, this.routeParams);

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