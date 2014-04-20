var angularForumListener = function($routeProvider){
        angularForumRouting.createAngularRouting($routeProvider);
};

// add listener to enable the routings
angularConfig.routingListener[angularConfig.routingListener.length] = angularForumListener;