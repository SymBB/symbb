$(document).ready(function() {
    
    var activeTab = new Array();
    var lastActivTab = new Array(); 
    var groups = new Array();
    
    $('.symbb_tab').each(function(key, tab){
        var tabKey = $(tab).data('tab');
        var tabgroupKey = 'default';
        if($(tab).data('tabgroup')){
            tabgroupKey = $(tab).data('tabgroup');
        }

        groups[groups.length] = tabgroupKey;
        if($(tab).parent().hasClass('active')){
            activeTab[tabgroupKey] = tabKey;
            lastActivTab[tabgroupKey] = tab;
        }
        $(tab).parent().click(function() {
            symBBToggleTab(tabKey, tabgroupKey);
            if(lastActivTab[tabgroupKey]){
                $(lastActivTab[tabgroupKey]).parent().removeClass('active');
            }
            $(tab).parent().addClass('active');
            lastActivTab[tabgroupKey] = tab;
        });
    });
    
    $(groups).each(function(key, groupkey){
        symBBToggleTab(activeTab[groupkey], groupkey);
    });
    
    
    function symBBToggleTab(currentKey, groupkey){
        $('.symbb_tabcontent').each(function(keyContent, content){
            var tabContentKey = $(content).data('tab');
            var tabgroupKey = 'default';
            if($(content).data('tabgroup')){
                tabgroupKey = $(content).data('tabgroup');
            }
            if(groupkey === tabgroupKey){
                if(currentKey !== tabContentKey){
                    $(content).hide();
                } else {
                    $(content).show();
                }
                $(content).find('.alert-danger').each(function(key, element){
                    $('.symbb_tab').each(function(key, tab){
                        var tabKey = $(tab).data('tab');
                        if(tabKey === tabContentKey){
                            $(tab).addClass('alert');
                            $(tab).addClass('alert-danger');
                        }
                    });
                });
            }
        });
    }
    
});