
jQuery(document).ready(function() {

    // Get the ul that holds the collection of tags
    $('.collection').each(function(key, collectionHolder){
        collectionHolder = $(collectionHolder);
        // count the current form inputs we have (e.g. 2), use that as the new
        // index when inserting a new item (e.g. 2)
        collectionHolder.data('index', collectionHolder.find('.collection_item').length);


        setObservers(collectionHolder);
        
    });
    
    function setObservers(collectionHolder){
        
        
        var prototype = collectionHolder.data('prototype');
        var removeTitle = collectionHolder.data('remove-title');
        var removeIcon = collectionHolder.data('remove-icon');
        var additionalBtns = collectionHolder.data('additional-btns');
        
        if(!removeTitle){
            removeTitle = ''
        }
        
        if(!removeIcon){
            removeIcon = 'glyphicon-remove-sign'
        }
        
        collectionHolder.find('.collection_add').each(function(key2, btn){

            $(btn).unbind('click');
            $(btn).on('click', function(e) {
                // prevent the link from creating a "#" on the URL
                e.preventDefault();
                var index = collectionHolder.data('index');
                var newItem = $('<div class="collection_item"></div>');
                var buttons = $('<div class="collection_item_buttons"></div>');
                buttons.append(createBtn(removeIcon, removeTitle,'return false;'))
                if(additionalBtns){
                    additionalBtns = additionalBtns.replace('{index}', index + 1);
                    buttons.append($(additionalBtns))
                }
                var prototypeCurrent = prototype.replace(/__name__/g, index);
                newItem.append($(prototypeCurrent));
                newItem.append(buttons);
                collectionHolder.append(newItem);
                collectionHolder.data('index', (index+1));
                setObservers(collectionHolder);
                
                $('.symbb_editor').bbcodeEditorReload();
            });

        });

        collectionHolder.find('.collection_item').each(function(key2, item){

            item = $(item);

            item.find('.collection_item_remove').each(function(key3, btn){
                
                $(btn).unbind('click');
                $(btn).on('click', function(e) {
                    // prevent the link from creating a "#" on the URL
                    e.preventDefault();
                    item.remove();
                });

            });

        });
    }
    
    function createBtn (iconClass, title, action){
        if(!iconClass){
            var iconClass = "glyphicon-remove-sign";
        }
        if(!action){
            var action = "return false;";
        }
        if(!title){
            var title = "";
        }
        return $('<div class="symbb_bbcode_btn" title="'+title+'"><span class="glyphicon '+iconClass+' collection_item_remove" onclick="'+action+'" title="'+title+'"></span></div>');
    }

});
