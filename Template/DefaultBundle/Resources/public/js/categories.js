$(function() {
            
    calculateCategoryPositions();

    function calculateCategoryPositions(){
        var positions = [];
        $('.symbb_category').each(function(key, div){
            var position = $(div).position();
            positions[key] = position;
            var parentHeight  = $(div).parent().height();
            $(div).parent().css('height', parentHeight)
        });
        $('.symbb_category').each(function(key, div){
            var position = positions[key];
            $( div ).css("left", position.left);
            $( div ).css("top", position.top);
            $( div ).css("position", "absolute");
        });
    }

    $( window ).resize(function() {

        $('.symbb_category').each(function(key, div){
            $( div ).css("left", 0);
            $( div ).css("top", 0);
            $( div ).css("position", "relative");
            $(div).parent().css('height', "auto");
        });

         calculateCategoryPositions();
    });
});