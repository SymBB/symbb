var symbbForum = {

    initEditor: function (uploadPath, lang) {
        $(".symbb_editor").each(function (key, element) {
            var textarea = $(element).find("textarea");
            parentId = $(element).data("id");
            parentId = parseInt(parentId);
            forum = $(parent).data("forum");
            forum = parseInt(forum);
            var height = 300;
            if($(element).hasClass("small")){
                var height = 150;
            }
            var editor = $(textarea).editable(
                {
                    inlineMode: false,
                    imageUploadURL: uploadPath,
                    imageUploadParams: {'id': parentId, 'forum': forum},
                    language: lang,
                    autosave: true,
                    autosaveInterval: 500,
                    toolbarFixed: false,
                    theme: 'gray',
                    minHeight: height
                }
            );
            $(textarea).on('editable.beforeSave', function (e, editor) {
                var html = editor.getHTML();
                $(textarea).html(html);
                //dont send save request we will put the text into the from field
                return false;
            });
        });
    },

    saveEditor: function (bSubmit) {
        var editorTextareas = $(".symbb_editor textarea");
        $(editorTextareas).each(function (key, element) {
            $(element).editable("sync");
            if (bSubmit) {
                $(element).closest("form").submit();
                return true;
            }
        });
        return true;
    },

    prepareCollection: function () {
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

        $addTagLink.on('click', function (e) {
            // prevent the link from creating a "#" on the URL
            e.preventDefault();

            // add a new tag form (see next code block)
            addTagForm($collectionHolder, $newLinkLi);
        });

        // add a delete link to all of the existing tag form li elements
        $collectionHolder.find('.symbb_collection_row').each(function () {
            addTagFormDeleteLink($(this));
        });

        function addTagFormDeleteLink($tagFormLi) {
            var $removeFormA = $('<button class="btn btn-danger"><span class="glyphicon glyphicon-minus"></span></button>');
            $tagFormLi.append($removeFormA);

            $removeFormA.on('click', function (e) {
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
};