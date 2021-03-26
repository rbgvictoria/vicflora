/* 
 * Copyright 2016 Royal Botanic Gardens Victoria.
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *      http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

var screen;

$(function(){
    if (uri[1] === 'key' && typeof uri[2] !== 'undefined') {
        var keyID = parseInt(uri[2]);
        keyBaseConfig();
        screen = screenSize();
        showKey(keyID);
    }
});

var screenSize = function() {
    var viewportWidth = $(window).width();
    var screenSmMin = 768, screenMdMin = 992, screenLgMin = 1200;
    
    if (viewportWidth < screenSmMin) {
        return 'xs';
    }
    else if (viewportWidth < screenMdMin) {
        return 'sm';
    }
    else if (viewportWidth < screenLgMin) {
        return 'md';
    }
    else {
        return 'lg';
    }
};

var keyBaseConfig = function() {
    //$.fn.keybase.defaults.playerWindow = keybasePlayerWindowBootstrap();
};


var showKey = function(keyID) {
    $('.keybase-link a').attr('href', 'https://keybase.rbg.vic.gov.au/keys/show/' + keyID);
    var wsUrl = 'https://data.rbg.vic.gov.au/keybase-ws';

    $.fn.keybase({
        baseUrl: wsUrl + "/ws/key_get",
        playerDiv: '#keybase-player',
        key: keyID,
        title: true,
        keyTitle: keybaseTitleDisplay,
        reset: true,
        resizePlayerWindow: resizePlayerWindow,
        remainingItemsDisplay: remainingItemsDisplay,
        discardedItemsDisplay: discardedItemsDisplay,
        onJson: vicfloraOnJson,
        onLoad: vicfloraOnLoad
    });

    $('.nav-tabs').on('click', 'a[href=#tab_bracketed]', function (event ) {
        bracketedKey();
    });

    $(document).on('click', '.breadcrumbs a', function(e) {
        e.preventDefault();
        var keyID = $(e.target).attr('href');
        if (keyID.indexOf('/') > -1) {
            keyID = keyID.substr(keyID.lastIndexOf('/') + 1);
        }
        $('.keybase-link a').attr('href', 'https://keybase.rbg.vic.gov.au/keys/show/' + keyID);
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

var vicfloraOnJson = function() {
    if ($('.key-title').text().length === 0) {
        getTitle();
        getBreadCrumb();
    }
};

var vicfloraOnLoad = function() {
    var settings = $.fn.keybase.getters.settings();
    responsivePlayerWindow();
};

var responsivePlayerWindow = function() {
    if (screen === 'xs') {
        $('.keybase-player-window>div>div:eq(0)').addClass('active');
        $('.keybase-player-window>div>div:gt(0)>div').hide();
        var playerWindowHeight = 0;
        $('.keybase-player-window>div>div').css({height: 'auto'}).each(function() {
            playerWindowHeight += $(this).height();
        });
        $('.keybase-player-window').height(playerWindowHeight);
        $('.keybase-player-window>div>div:eq(0)>h3').prepend('<i class="fa fa-caret-down"></i>&nbsp;');
        $('.keybase-player-window>div>div:eq(2)>h3').prepend('<i class="fa fa-caret-right"></i>&nbsp;');
        
        $('.keybase-player-window>div>div').on('click', 'h3', function() {
            if ($(this).parents('div').eq(0).children('div').text().length) {
                $('.keybase-player-window>div>div>div').hide();
                $('.keybase-player-window>div>div>h3>.fa').removeClass('fa-caret-right').removeClass('fa-caret-down').addClass('fa-caret-right');
                $(this).parents('div').eq(0).children('div').show();
                $(this).children('.fa').removeClass('fa-caret-right').addClass('fa-caret-down');
            }
        });

        $('[href=#tab_player], [href=#tab_bracketed]').on('hide.bs.tab', function() {
            var tabHeight = $('.vicflora-tab').height();
            $(this).off('shown.bs.tab').on('shown.bs.tab', function() {
                $('.vicflora-tab').height(tabHeight);
                $('.keybase-player-window>div>div>h3').each(function() {
                    $(this).children('.fa:gt(0)').remove();
                });
            });
        });
        
        //$('.keybase-player-window').height($('.keybase-player-window>div>div').height()*4);
    }
};

var getTitle = function() {
    var json = $.fn.keybase.getters.jsonKey();
    
    var title = 'Key to the ';
    switch (json.rank) {
        case null: 
            title += json.key_name.charAt(0).toLowerCase() + json.key_name.slice(1);
            break;
        case 'family':
            title += 'families of ' + json.key_name.charAt(0).toLowerCase() + json.key_name.slice(1);
            break;
        case 'genus':
            title += 'genera of ' + json.key_name;
            break;
        case 'species':
            title += 'species of <i>' + json.key_name + '</i>';
            break;
        case 'subspecies':
            title += 'subspecies of <i>' + json.key_name + '</i>';
            break;
        case 'variety':
            title += 'varieties of <i>' + json.key_name + '</i>';
            break;
        case 'forma':
            title += 'forms of <i>' + json.key_name + '</i>';
            break;
    }
    $('.key-title').html(title);
};

var getBreadCrumb = function() {
    var json = $.fn.keybase.getters.jsonKey();
    var tscope = json.taxonomic_scope;
    var crumbs = json.breadcrumbs;
    
    if (tscope.url) {
        var li = $('<li/>').append('<a href="' + tscope.url + '">' + tscope.item_name + '</a>').appendTo($('ol.breadcrumb'));
    }
    else {
        if (crumbs.length) {
            var href = base_url + '/flora/key/' + crumbs[crumbs.length-1].key_id;
            var li = $('<li/>').append('<a href="' + href + '">' + '<i class="fa fa-arrow-left"></i></a> ' + crumbs[crumbs.length-1].key_name).appendTo($('ol.breadcrumb'));
        }
    }
};

var bracketedKey = function () {
    $.fn.keybase('bracketedKey', {
        bracketedKeyDiv: '#keybase-bracketed'
    });
};

var keybaseTitleDisplay = function(json) {
    var title = json.key_title;
    $('.key-title').html(title);
};

var remainingItemsDisplay = function(items, itemsDiv) {
    var list = keybaseItemsDisplay(items);
    $(itemsDiv).eq(0).children('h3').eq(0).html(getPrefix() + 'Remaining items (' + items.length + ')');
    $(itemsDiv).eq(0).children('div').eq(0).html('<ul>' + list.join('') + '</ul>');
};

var discardedItemsDisplay = function(items, itemsDiv) {
    var list = keybaseItemsDisplay(items);
    $(itemsDiv).eq(0).children('h3').eq(0).html(getPrefix() + 'Discarded items (' + items.length + ')');
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
            entity += '<a href="' + item.to_key + '"><span class="keybase-player-tokey"><i class="fa fa-arrow-right"></i></span></a>';
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
                entity += '<a href="' + item.link_to_key + '"><span class="keybase-player-tokey"><i class="fa fa-arrow-right"></i></span></a>';
            }
        }
        entity += '</li>';
        list.push(entity);
    });
    return list;
};

var getPrefix = function(itemsDiv) {
    var symbol = '';
    if (screen === 'xs') {
        if ($(itemsDiv).hasClass('active')) {
            symbol = '<i class="fa fa-caret-down"></i> ';
        }
        else {
            symbol = '<i class="fa fa-caret-right"></i> ';
        }
    }
    return symbol;
};

$.fn.keybase.defaults.bracketedKeyDisplay = function() {
    var json = $.fn.keybase.getters.jsonKey();
    var settings = $.fn.keybase.getters.settings();
    var bracketed_key = $.fn.keybase.getters.bracketedKey();

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
                var item = JSPath.apply('.items{.item_id=="' + toItem.item_id + '"}', json)[0];
                html += '<span class="keybase-to-item">';
                if (item.url) {
                    var guid = item.url.substr(item.url.lastIndexOf('/')+1);
                    html += '<a href="' + base_url + '/flora/taxon/' + guid + '">' + item.item_name + '</a>';
                }
                else {
                    html += item.item_name;
                }
                if (item.to_key && !item.url) {
                    html += '<a href="' + item.to_key + '"><span class="keybase-player-tokey"><i class="fa fa-arrow-right"></i></span></a>';
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
                        html += '<a href="' + item.link_to_key + '"><span class="keybase-player-tokey"><i class="fa fa-arrow-right"></i></span></a>';
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
    $('.keybase-bracketed-key-filter').remove();
};

var resizePlayerWindow = function() {
    var settings = $.fn.keybase.getters.settings();
    
    var viewportWidth = $(window).width();
    var minScreenWidth = 768;
    
    if (viewportWidth >= minScreenWidth) {
        $('.keybase-player-window').css({
            'position': 'relative'
        });
    
        $('.keybase-player-drag-leftright').css({
            'width': '6px',
            'height': '100%'
        });
        $('.keybase-player-drag-updown').css({
            'height': '6px',
            'width': '100%'
        });
        $('.keybase-player-leftpane, .keybase-player-rightpane').css({
            'height': '100%',
            'position': 'absolute',
            'top': '0px'
        });

        $('.keybase-player-leftpane').css({
            'width': (($('.keybase-player-window').width()*0.67) - 3) + 'px',
            'left': '0px'
        });
        $('.keybase-player-drag-leftright').css({
            'position': 'absolute',
            'top': '0px',
            'left': $('.keybase-player-leftpane').width() + 'px'
        });
        $('.keybase-player-rightpane').css({
            'width': (($('.keybase-player-window').width() * 0.33) - 3) + 'px',
            'left': ($('.keybase-player-leftpane').width() + 6) + 'px'
        });

        $('.keybase-player-drag-updown').css({
            'position': 'absolute',
            'top': (($('.keybase-player-window').height() * 0.5) - 3) + 'px'
        });
        $('.' + settings.cssClass.path + ', .' + settings.cssClass.discardedItems).css({
            'top': (($('.keybase-player-window').height() * 0.5) + 3) + 'px'
        });
    }
    else {
        $('.keybase-player-window>div>div').css({position: 'static'});
        $('.keybase-player-window').height($('.keybase-player-window>div>div').height()*4);
    }
};

