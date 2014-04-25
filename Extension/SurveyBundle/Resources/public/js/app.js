var angularForumExtensionSurveyListener = function($routeProvider){
        angularForumExtensionSurveyRouting.createAngularRouting($routeProvider);
};

// add listener to enable the routings
angularConfig.routingListener[angularConfig.routingListener.length] = angularForumExtensionSurveyListener;