var refesh = function(scope, route){
    console.debug(scope);
    route.reload();
};

var textMatchOneLine = function(){
    console.debug(1);
    $(".textMatchOneLine").each(function() {
    console.debug(this);
        var jThis=$(this);
        var fontSize = parseInt(jThis.css("font-size"));
        for(var i=0; jThis.height() > (fontSize + 5) && i<30;i++)
        { 
            console.debug(jThis.height());
            console.debug(fontSize);
            fontSize--;
            jThis.css("font-size",fontSize+"px"); 
        }
    });
};