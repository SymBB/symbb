(function($) {

    var SymbbQuote = function(element)
    {
        var elem    = $(element);
        
        elem.click(function() {
            var text = '[quote=\"'+elem.data('user')+'\"]'+elem.data('quote')+'[/quote]';
            document.getElementById('post_text').innerHTML = text; 
            window.location.href='#symbb_bbcode_editor';
        });

    };

    $.fn.SymbbQuote = function()
    {
        return this.each(function()
        {
            var element = $(this);

            // Return early if this element already has a plugin instance
            if (element.data('SymbbQuote'))
                return;

            var myplugin = new SymbbQuote(this);

            // Store plugin object in this element's data
            element.data('SymbbQuote', myplugin);
        });
    };

})(jQuery);


$(document).ready(function() {
    $('.quote').SymbbQuote();
});