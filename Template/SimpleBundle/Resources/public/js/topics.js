$(function() {
 $('.symbb_topic .row.body .media .userblock').each(function(key, div){
        var height = $( div ).parent().height();
        $( div ).css("height", height);
    });
});