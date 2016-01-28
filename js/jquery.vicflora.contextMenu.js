$(function(){
    $.contextMenu({
        selector: '.name-entry a .currentname',
        callback: function(key, options) {
            var parent = $(this).parents('a').attr('href');
            var m = key + ": " + parent;
            window.console && console.log(m) || alert(m); 
        },
        items: {
            "edit": {name: "Edit", callback: function (key, opt) {
                location.href = $(this).parents('a').attr('href').replace('taxon', 'edittaxon');
            }},
            "addchild": {name: "Add child", callback: function (key, opt) {
                location.href = $(this).parents('a').attr('href').replace('taxon', 'addchild');
            }},
        }
    });
    
});
