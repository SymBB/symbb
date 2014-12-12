$(function() {
    $('.sortable_table').sortable({
        axis: 'y',
        placeholder: "ui-state-highlight",
        handle: ".mover" ,
        stop: function (event, ui) {
            var data = $(this).sortable('serialize');
            var url = $(this).data('url');
            // POST to server using $.post or $.ajax
            $.ajax({
                data: data,
                type: 'POST',
                url: url
            });
        }
    });
});