
jQuery(document).ready(function() {

    // Get the ul that holds the collection of tags
    $('.collection').each(function(key, collectionHolder){
        collectionHolder = $(collectionHolder);
        // count the current form inputs we have (e.g. 2), use that as the new
        // index when inserting a new item (e.g. 2)
        collectionHolder.data('index', collectionHolder.find('.collection_item').length);

        var addButton = collectionHolder.find('.collection_add');
        var prototype = collectionHolder.data('prototype');

        addButton.on('click', function(e) {
            // prevent the link from creating a "#" on the URL
            e.preventDefault();
            var index = collectionHolder.data('index');
            var newItem = $('<div class="collection_item"></div>');
            var prototypeCurrent = prototype.replace(/__name__/g, index);
            newItem.append($(prototypeCurrent));
            collectionHolder.prepend(newItem);
            collectionHolder.data('index', (index+1));
        });

        collectionHolder.find('.collection_item').each(function(key2, item){

            item = $(item);

            item.find('.collection_item_remove').each(function(key3, btn){

                $(btn).on('click', function(e) {
                    // prevent the link from creating a "#" on the URL
                    e.preventDefault();
                    item.remove();
                });

            });

        });

    });

});
