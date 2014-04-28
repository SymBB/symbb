
var angularBBCodeRouting = {
    
    routingData: {
        bbcode_default: {
            'template': 'symbb_bbcode_template_angular_default',
            'templateParam': { file: 'bbcode'}
        },
        bbcode_signature: {
            'template': 'symbb_bbcode_template_angular_signature',
            'templateParam': { file: 'bbcode'}
        }
    }
    
};

// add routing to main routing
$.each(angularBBCodeRouting.routingData, function(key, value){
    angularConfig.routingData[key] = value;
});