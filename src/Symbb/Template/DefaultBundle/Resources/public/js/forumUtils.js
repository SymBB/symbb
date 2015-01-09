var symbbForum = {

    initEditor: function(){
        var editable = document.querySelector('.symbb-editable');
        aloha(editable);
    }

}

jQuery( document ).ready(function( $ ) {
    symbbForum.initEditor();
});