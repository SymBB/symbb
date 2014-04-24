
var angularForumExtensionSurveyRouting = {
    
    routingData: {
        extension_survey_vote:  {
            'api': 'symbb_api_extension_survey_vote'
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
$.each(angularForumExtensionSurveyRouting.routingData, function(key, value){
    angularConfig.routingData[key] = value;
});
