
var angularBBCodeRouting = {
    
    routingData: {
        bbcode: {
            'template': 'symbb_bbcode_template_angular',
            'templateParam': { file: 'bbcode'}
        }
    }
    
};

// add routing to main routing
$.each(angularForumRouting.routingData, function(key, value){
    angularConfig.routingData[key] = value;
});