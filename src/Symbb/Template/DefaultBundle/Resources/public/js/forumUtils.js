var symbbForum = {

    initEditor: function(){
        var editable = document.querySelector('.symbb-editable');
        aloha(editable);
    },

    submitTopicForm: function(topicId){
        var value = $('#topic_text_editor_'+topicId).html();
        console.debug(value);
        $('#topic_text_'+topicId).val(value);
    },

    prepareCollection: function(){
        var $collectionHolder;

        // setup an "add a tag" link
        var $addTagLink = $('<button class="btn btn-info"><span class="glyphicon glyphicon-plus"></span></button>');
        var $newLinkLi = $('<div></div>').append($addTagLink);

        // Get the ul that holds the collection of tags
        $collectionHolder = $('.symbb_collection');

        // add the "add a tag" anchor and li to the tags ul
        $collectionHolder.append($newLinkLi);

        // count the current form inputs we have (e.g. 2), use that as the new
        // index when inserting a new item (e.g. 2)
        $collectionHolder.data('index', $collectionHolder.find(':input').length);

        $addTagLink.on('click', function(e) {
            // prevent the link from creating a "#" on the URL
            e.preventDefault();

            // add a new tag form (see next code block)
            addTagForm($collectionHolder, $newLinkLi);
        });

        // add a delete link to all of the existing tag form li elements
        $collectionHolder.find('.symbb_collection_row').each(function() {
            addTagFormDeleteLink($(this));
        });

        function addTagFormDeleteLink($tagFormLi) {
            var $removeFormA = $('<button class="btn btn-danger"><span class="glyphicon glyphicon-minus"></span></button>');
            $tagFormLi.append($removeFormA);

            $removeFormA.on('click', function(e) {
                // prevent the link from creating a "#" on the URL
                e.preventDefault();

                // remove the li for the tag form
                $tagFormLi.remove();
            });
        }

        function addTagForm($collectionHolder, $newLinkLi) {
            // Get the data-prototype explained earlier
            var prototype = $collectionHolder.data('prototype');
            prototype = prototype.replace('<span class="col-sm-2 control-label"><label class="required">__name__label__</label></span>', "");

            // get the new index
            var index = $collectionHolder.data('index');

            // Replace '__name__' in the prototype's HTML to
            // instead be a number based on how many items we have
            var newForm = prototype.replace(/__name__/g, index);

            // increase the index with one for the next item
            $collectionHolder.data('index', index + 1);

            // Display the form in the page in an li, before the "Add a tag" link li
            var $newFormLi = $('<div class="symbb_collection_row"></div>').append(newForm);

            $newLinkLi.before($newFormLi);
        }
    }
}

jQuery( document ).ready(function( ) {
    //symbbForum.initEditor();
    symbbForum.prepareCollection();
});