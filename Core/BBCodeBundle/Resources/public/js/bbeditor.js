var BBCodeEditor = {
    
    setData: [],
    
    regexTimeout: null,
    
    addBBCode: function(set, bbcode){
        if(!this.setData[set]){
            this.setData[set] = [];
        }
        bbcode.set = set;
        this.setData[set][this.setData[set].length] = bbcode;
    },
    
    prepareFontBtn: function(btn, parentDiv, bbcode){
        var btngroup = $('<div class="btn-group pull-left"></div>');
        var arrowBtn = $('<div class="btn btn-default btn-sm dropdown-toggle" data-toggle="dropdown"><span class="caret"></span><span class="sr-only">Toggle Dropdown</span></div>');
        var dropdown = $('<ul class="dropdown-menu" role="menu"></ul>');
        
        var sizeVerySmall = $('<li><a class="fontsize-verysmall">small 60%</a></li>');
        var sizeSmall = $('<li><a class="fontsize-small">small 80%</a></li>');
        var sizeBig = $('<li><a class="fontsize-big">big 120%</a></li>');
        var sizeVeryBig = $('<li><a class="fontsize-verybig">big 140%</a></li>');
        
        dropdown.append(sizeVerySmall);
        dropdown.append(sizeSmall);
        dropdown.append(sizeBig);
        dropdown.append(sizeVeryBig);
        
        btn.addClass('dropdown-toggle');
        btn.removeClass('symbb_bbcode_code');
        btngroup.append(btn);
        btngroup.append(arrowBtn);
        btngroup.append(dropdown);
        
        var regex = bbcode.button_regex;
        
        bbcode.button_regex = regex.replace('{1}', 'normal');
        this.prepareDefaultBtn(btn, parentDiv, bbcode);
        
        bbcode.button_regex = regex.replace('{1}', 'verysmall');
        this.prepareDefaultBtn(sizeVerySmall, parentDiv, bbcode);
        
        bbcode.button_regex = regex.replace('{1}', 'small');
        this.prepareDefaultBtn(sizeSmall, parentDiv, bbcode);
        
        bbcode.button_regex = regex.replace('{1}', 'big');
        this.prepareDefaultBtn(sizeBig, parentDiv, bbcode);
        
        bbcode.button_regex = regex.replace('{1}', 'verybig');
        this.prepareDefaultBtn(sizeVeryBig, parentDiv, bbcode);
        
        return btngroup;
    },

    prepareColorBtn: function(btn, parentDiv, bbcode){

        var regex = bbcode.button_regex;

        $(btn).colpick({
            flat: false,
            layout: 'hex',
            onSubmit: function(HSBObject, HEXString){
                bbcode.button_regex = regex.replace('{color}', '#'+HEXString);
                BBCodeEditor.executeBtn(bbcode.button_regex, parentDiv, bbcode);
            }
        });

        return btn;
    },
    
    
    prepareHeaderBtn: function(btn, parentDiv, bbcode){

        var btngroup = $('<div class="btn-group pull-left"></div>');
        var arrowBtn = $('<div class="btn btn-default btn-sm dropdown-toggle" data-toggle="dropdown"><span class="caret"></span><span class="sr-only">Toggle Dropdown</span></div>');
        var dropdown = $('<ul class="dropdown-menu" role="menu"></ul>');
        
        var header1 = $('<li><a><h1>H1</h1></a></li>');
        var header2 = $('<li><a><h2>H2</h2></a></li>');
        var header3 = $('<li><a><h3>H3</h3></a></li>');
        
        dropdown.append(header1);
        dropdown.append(header2);
        dropdown.append(header3);
        
        btn.addClass('dropdown-toggle');
        btn.removeClass('symbb_bbcode_code');
        btngroup.append(btn);
        btngroup.append(arrowBtn);
        btngroup.append(dropdown);
        
        var regex = bbcode.button_regex;
        
        bbcode.button_regex = regex.replace(/\{1\}/g, '1');
        this.prepareDefaultBtn(btn, parentDiv, bbcode);
        
        bbcode.button_regex = regex.replace(/\{1\}/g, '1');
        this.prepareDefaultBtn(header1, parentDiv, bbcode);
        
        bbcode.button_regex = regex.replace(/\{1\}/g, '2');
        this.prepareDefaultBtn(header2, parentDiv, bbcode);
        
        bbcode.button_regex = regex.replace(/\{1\}/g, '3');
        this.prepareDefaultBtn(header3, parentDiv, bbcode);
        
        return btngroup;
    },
    
    prepareDefaultBtn: function(btn, parentDiv, bbcode){
        var regex = bbcode.button_regex;
        btn.click(function(){
            BBCodeEditor.executeBtn(regex, parentDiv, bbcode);
        });
        return btn;
    },

    executeBtn: function(regex, parentDiv, bbcode){
        var element = $(parentDiv).find('textarea')[0];

        var tagCode = regex;

        if (document.selection) {
            var sel = document.selection.createRange();
            sel.text = tagCode.replace(/\{text\}/g, sel.text);
        } else if (element.selectionStart || element.selectionStart == '0') {
            var startPos = element.selectionStart;
            var endPos = element.selectionEnd;
            var insert = tagCode.replace(/\{text\}/g, element.value.substring(startPos, endPos));
            element.value = element.value.substring(0, startPos) + insert + element.value.substring(endPos, element.value.length);
            element.focus();
            element.selectionStart = endPos + tagCode.length - 3;
        } else {
            element.focus();
            element.value += tagCode.replace(/\{text\}/g, '');
        }
        $(element).change();

        BBCodeEditor.updatePreview(parentDiv, bbcode.set);
    },
    
    createEditor: function(parentDiv){

        var btnGroup = $(parentDiv).find('.symbb_bbcodes_group');
        var bbcodeSet = 'default';
        
        $(this.setData[bbcodeSet]).each(function(key, bbcode){
            var btn = $('<div>');
            btn.addClass('btn btn-default btn-sm symbb_bbcode_code');
            btn.data('tag-code', bbcode.buttonRegex);
            btn.attr('title', bbcode.name);
            if(bbcode.image !== ""){
                var btnImg = $('<img>');
                btnImg.attr('alt', bbcode.name);
                btnImg.attr('title', bbcode.name);
                btnImg.attr('src', bbcode.image);
                btn.append(btnImg);
            } else {
                btn.append(bbcode.name);
            }
            eval('btn = '+bbcode.js_function+'(btn, parentDiv, bbcode)');
            $(btnGroup).append(btn);
            BBCodeEditor.preparePreview(parentDiv, bbcodeSet);
        });
    },
    
    prepareUpdatePreview: function(parentDiv, set){
        if(BBCodeEditor.regexTimeout){
            clearTimeout(BBCodeEditor.regexTimeout);
            BBCodeEditor.regexTimeout = null;
        }
        BBCodeEditor.regexTimeout = setTimeout(function(){
            BBCodeEditor.updatePreview(parentDiv, set)
        }, 500);  
    },
    
    updatePreview: function(parentDiv, set){
        var html = $(parentDiv).find('textarea').val();
        $(BBCodeEditor.setData[set]).each(function(key, codes){
            $(codes).each(function(){
                try {
                    var pattern = codes.search_regex;
                    var temp = pattern[0];
                    var temp2 = pattern.split(temp);
                    var modifier = temp2[2];
                    var pattern = temp2[1];
                    modifier = modifier.replace('s', 'm');
                    modifier = modifier.replace('U', '');
                    if(!modifier.match('g')){
                        modifier = modifier + 'g';
                    }
                    pattern = pattern.replace('(?|', '(?:');
                    var regex = new RegExp(pattern, modifier);
                    html = html.replace(regex, codes.replace_regex);
                } catch(e){
                    console.debug(e);
                }
            });
        });
        html = html.replace(/\n/g, '<br />');
        $(parentDiv).find('.preview').html(html);
    },
    
    preparePreview: function(parentDiv, set){
        $(parentDiv).find('textarea').keyup(function() {
            BBCodeEditor.prepareUpdatePreview(parentDiv, set);
        });
    }
};

