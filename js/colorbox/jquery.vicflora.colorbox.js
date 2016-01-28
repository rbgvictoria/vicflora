$(function(){
    $(".colorbox_ajax").colorbox({
        rel: "thumb",
        //opacity: 0.40, 
        //transition: 'none', 
        speed: 0,
        width: "95%",
        height: '95%',
        onComplete: function() {
            $('#colorbox button[name="cancel"]').click(function(e) {
                e.preventDefault();
                $.colorbox.close();
            });
        }
    });
    
    $('a.colorbox_key').colorbox({
        href: $(this).attr('href'),
        speed: 0,
        width: '95%',
        height: '95%',
        onComplete: function() {
            colorboxKeyComplete($(this));
        },
        onClosed: function() {

        }
    });
    
    $('a.colorbox_mainkey').colorbox({
        href: $(this).attr('href'),
        speed: 0,
        width: '95%',
        height: '95%',
        onComplete: function() {
            colorboxKeyComplete($(this));
        },
        onClosed: function() {

        }
    });
    
    var colorboxKeyComplete = function(that) {
        $('.key-title').html(that.html());
        
        var href = that.attr('href');
        var keyID = href.substr(href.lastIndexOf('/') + 1);
        $('.keybase-link a').attr('href', 'http://keybase.rbg.vic.gov.au/keys/show/' + keyID);
        
        $.fn.keybase({
            playerDiv: '#keybase-player',
            key: keyID,
            title: false,
            reset: true,
            remainingItemsDisplay: remainingItemsDisplay,
            discardedItemsDisplay: discardedItemsDisplay
            //titleDiv: '.keybase-key-title',
            //sourceDiv: '.keybase-key-source'
        });

        $('.nav-tabs').on('click', 'a[href=#tab_bracketed]', function (event ) {
            bracketedKey();
        });
        
        $(document).on('click', '.keybase-player-tokey', function(e) {
            e.preventDefault();
            $('.breadcrumbs').append('<a href="' + json.key_id + '">' + json.key_name + '</span></a>');
            var keyID = $(e.target).parent('a').attr('href');
            if (keyID.indexOf('/') > -1) {
                keyID = keyID.substr(keyID.lastIndexOf('/') + 1);
            }
            $('.keybase-link a').attr('href', 'http://keybase.rbg.vic.gov.au/keys/show/' + keyID);
            $.fn.keybase({
                playerDiv: '#keybase-player',
                key: keyID,
                title: true,
                keyTitle: keybaseTitleDisplay,
                reset: true,
                remainingItemsDisplay: remainingItemsDisplay,
                discardedItemsDisplay: discardedItemsDisplay
            });
            bracketedKey();
        });
        
        $(document).on('click', '.breadcrumbs a', function(e) {
            e.preventDefault();
            var keyID = $(e.target).attr('href');
            if (keyID.indexOf('/') > -1) {
                keyID = keyID.substr(keyID.lastIndexOf('/') + 1);
            }
            $('.keybase-link a').attr('href', 'http://keybase.rbg.vic.gov.au/keys/show/' + keyID);
            $.fn.keybase({
                playerDiv: '#keybase-player',
                key: keyID,
                title: true,
                keyTitle: keybaseTitleDisplay,
                reset: true,
                remainingItemsDisplay: remainingItemsDisplay,
                discardedItemsDisplay: discardedItemsDisplay
            });
            bracketedKey();
            $(e.target).nextAll('a').remove().end().remove();
        });
    };
    
    var bracketedKey = function () {
        $.fn.keybase('bracketedKey', {
            bracketedKeyDiv: '#keybase-bracketed'
        });
    };
    
    var keybaseTitleDisplay = function(json) {
        var title = json.key_name;
        $('.key-title').html(title);
    };
    
    var remainingItemsDisplay = function(items, itemsDiv) {
        var list = keybaseItemsDisplay(items);
        $(itemsDiv).eq(0).children('h3').eq(0).html('Remaining items (' + items.length + ')');
        $(itemsDiv).eq(0).children('div').eq(0).html('<ul>' + list.join('') + '</ul>');
    };
    
    var discardedItemsDisplay = function(items, itemsDiv) {
        var list = keybaseItemsDisplay(items);
        $(itemsDiv).eq(0).children('h3').eq(0).html('Remaining items (' + items.length + ')');
        $(itemsDiv).eq(0).children('div').eq(0).html('<ul>' + list.join('') + '</ul>');
    };
    
    var keybaseItemsDisplay = function(items) {
        var list = [];
        $.each(items, function(index, item) {
            var entity;
            entity = '<li>';
            if (item.url) {
                var guid = item.url.substr(item.url.lastIndexOf('/')+1);
                entity += '<a href="' + base_url + '/flora/taxon/' + guid + '">' + item.item_name + '</a>';
            }
            else {
                entity += item.item_name;
            }
            if (item.to_key && !item.url) {
                entity += '<a href="' + item.to_key + '"><span class="keybase-player-tokey"></span></a>';
            }
            if (item.link_to_item_name) {
                entity += ' &gt; ';
                if (item.link_to_url) {
                    var guid = item.link_to_url.substr(item.link_to_url.lastIndexOf('/')+1);
                    entity += '<a href="' + base_url + '/flora/taxon/' + guid + '">' + item.link_to_item_name + '</a>';
                }
                else {
                    entity += item.link_to_item_name;
                }
                if (item.link_to_key && !item.link_to_url) {
                    entity += '<a href="' + item.link_to_key + '"><span class="keybase-player-tokey"></span></a>';
                }
            }
            entity += '</li>';
            list.push(entity);
        });
        return list;
    };
    
    /*$(".colorbox_load_image").colorbox({
        iframe: true,
        width: 800,
        height: 800
    });*/
    
});

$.fn.keybase.defaults.bracketedKeyDisplay = function(json) {
    var html = '<div class="keybase-bracketed-key">';
    var couplets = bracketed_key[0].children;
    for (var i = 0; i < couplets.length; i++)  {
        var couplet = couplets[i];
        var leads = couplet.children;
        html += '<div class="keybase-couplet" id="l_' + leads[0].parent_id + '">';
        //html += '<span class="test">' + JSON.stringify(couplet.children) + '</span>';
        for (var j = 0; j < leads.length; j++) {
            var lead = leads[j];
            var items = lead.children;
            html += '<div class="keybase-lead">';
            html += '<span class="keybase-from-node">' + lead.fromNode + '</span>';
            html += '<span class="keybase-lead-text">' + lead.title;
            if (lead.toNode !== undefined) {
                html += '<span class="keybase-to-node"><a href="#l_' + lead.lead_id + '">' + lead.toNode + '</a></span>';
            }
            else {
                var toItem = items[0].children[0];
                var item = JSPath.apply('.items{.item_id==' + toItem.item_id + '}', json)[0];
                html += '<span class="keybase-to-item">';
                if (item.url) {
                    var guid = item.url.substr(item.url.lastIndexOf('/')+1);
                    html += '<a href="' + base_url + '/flora/taxon/' + guid + '">' + item.item_name + '</a>';
                }
                else {
                    html += item.item_name;
                }
                if (item.to_key && !item.url) {
                    html += '<a href="' + item.to_key + '"><span class="keybase-player-tokey"></span></a>';
                }

                if (item.link_to_item_id) {
                    html += ' &gt; ';
                    if (item.link_to_url) {
                        var guid = item.link_to_url.substr(item.link_to_url.lastIndexOf('/')+1);
                        html += '<a href="' + base_url + '/flora/taxon/' + guid + '">' + item.link_to_item_name + '</a>';
                    }
                    else {
                        html += item.link_to_item_name;
                    }
                    if (item.link_to_key && !item.link_to_url) {
                        html += '<a href="' + item.link_to_key + '"><span class="keybase-player-tokey"></span></a>';
                    }

                }

                html += '</span> <!-- /.to-item -->';
            }
            html += '</span> <!-- /.keybase-lead-text -->';
            html += '</div> <!-- /.keybase-lead -->';
        }
        html += '</div> <!-- /.keybase-couplet -->';

    }
    html += '</div> <!-- /.keybase-bracketed_key -->';
    $(settings.bracketedKeyDiv).html(html);
};



