/**
 * Created by NKlaze on 10/04/2015.
 *
 * $.fn.keybase([action], options);
 *
 * var options = {
 *   baseUrl: "http://keybase.rbg.vic.gov.au/ws/keyJSON",
 *   ajaxDataType: "json",
 *   key: 2672,
 *   title: true,
 *   titleDiv: ".keybase-key-title",
 *   source: true,
 *   sourceDiv: ".keybase-key-source",
 *   cssClass: {
 *     currentNode: "keybase-player-currentnode",
 *     path: "keybase-player-path",
 *     remainingItems: "keybase-player-remainingitems",
 *     discardedItems: "keybase-player-discardeditems",
 *     stepBack: "keybase-player-stepback",
 *     startOver: "keybase-player-startover"
 *   },
 *   playerDiv: "#keybase-player",
 *   currentNodeDisplay: function(node, currentNodeDiv),
 *   discardedItemsDisplay: function(items, itemsDiv),
 *   pathDisplay: function(path, pathDiv),
 *   playerEvents: function(),
 *   playerWindow: function(),
 *   remainingItemsDisplay: function(items, itemsDiv),
 *   resultDisplay: function(result, resultDiv)
 * };
 *
 * Possible actions are 'player', 'indentedKey' and 'bracketedKey'. Action is optional: if no action is given, 'player'
 * is assumed.
 *
 * There are default values for all options except 'key'. 'key' may also be replaced with a 'data' object, when another
 * web service or file is being used. When a file is used 'data' should be an empty object or not defined at all and
 * 'baseURL' should contain the entire path to the file. 'ajaxDataType' can be either 'json' (default) or 'jsonp'.
 */

    var json;
var nested_sets;
var settings;
var bracketed_key;

(function( $ ) {
    //var settings;

    //var json;
    var rootNodeID;
    var next_id;
    //var nested_sets;
    var keyNodes;
    var current_node;

    var currentNodeElem;
    var pathElem;
    var remainingItemsElem;
    var discardedItemsElem;

    var filter_items = [];
    var filter_leads = [];
    var filter_parents = [];

    var indented_key;
    var indentedKeyHtml;


    $.fn.keybase = function() {
        elem = this;

        var options;
        if (arguments.length === 1) {
            options = arguments[0];
        }

        if (arguments.length === 2) {
            var action = arguments[0];
            options = arguments[1];
        }

        settings = $.extend(true, {}, $.fn.keybase.defaults, settings, options);

        var data;
        if (settings.data) {
            data = settings.data;
        }
        else {
            if (settings.key) {
                data = {key_id: settings.key};
            }
            else {
                data = false;
            }
        }

        if (!json || settings.reset === true) {
            var contentType ="application/x-www-form-urlencoded; charset=utf-8";

            if(window.XDomainRequest) //for IE8,IE9
                contentType = "text/plain";

            $.ajax({
                url: settings.baseUrl,
                data: {key_id: settings.key},
                dataType: settings.ajaxDataType,
                contentType: contentType,
                success:function(data){
                    json = data;

                    if (settings.title) {
                        settings.keyTitle(json);
                    }

                    if (settings.source) {
                        settings.keySource(json.source);
                    }

                    if ((!action || action === 'player') && !$('.keybase-player-window').length) {
                        settings.playerWindow();
                    }

                    // root node
                    rootNodeID = json.first_step.root_node_id;
                    next_id = rootNodeID;

                    nestedSets();
                    getNodes();
                    nextCouplet();
                    settings.playerEvents();
                    
                    if (action === "indentedKey") {
                        indentedKey();
                        $.fn.keybase.defaults.indentedKeyDisplay(json);
                    }

                    if (action === "bracketedKey") {
                        bracketedKey();
                        $.fn.keybase.defaults.bracketedKeyDisplay(json);
                    }
                },
                error:function(jqXHR,textStatus,errorThrown) {
                    alert("You can not send Cross Domain AJAX requests: "+errorThrown);
                }
            });

        }
        
        else {
            if (settings.title) {
                settings.keyTitle(json);
            }

            if (settings.source) {
                settings.keySource(json.source);
            }

            if ((!action || action === 'player') && !$('.keybase-player-window').length) {
                settings.playerWindow();
            }

            // root node
            rootNodeID = json.first_step.root_node_id;
            next_id = rootNodeID;

            nestedSets();
            getNodes();
            nextCouplet();
            settings.playerEvents();

            if (action === "indentedKey") {
                indentedKey();
                $.fn.keybase.defaults.indentedKeyDisplay(json);
            }

            if (action === "bracketedKey") {
                bracketedKey();
                $.fn.keybase.defaults.bracketedKeyDisplay(json);
            }
        }
    };

    $.fn.keybase.defaults = {
        baseUrl: "http://keybase.rbg.vic.gov.au/ws/keyJSON",
        ajaxDataType: 'json',
        title: true,
        titleDiv: '.keybase-key-title',
        source: true,
        sourceDiv: '.keybase-key-source',
        cssClass: {
            currentNode: 'keybase-player-currentnode',
            path: 'keybase-player-path',
            remainingItems: 'keybase-player-remainingitems',
            discardedItems: 'keybase-player-discardeditems',
            stepBack: 'keybase-player-stepback',
            startOver: 'keybase-player-startover'
        },
        reset: false
    };


    /*
     * KeyBase Player display functions
     * Can be overridden by user.
     */

    /**
     * playerEvents function
     *
     * Sets up the events that make the KeyBase Player work. Note: events need to be removed first, 
     * so you can run consecutive keys in the same page.
     */
    $.fn.keybase.defaults.playerEvents = function() {
        $('.' + settings.cssClass.currentNode).off('click', 'a', currentNodeHandler);
        $('.' + settings.cssClass.currentNode).on('click', 'a', currentNodeHandler);

        $('.' + settings.cssClass.path).off('click', 'a', stepBackHandler);
        $('.' + settings.cssClass.path).on('click', 'a', stepBackHandler);

        $('.' + settings.cssClass.stepBack).off('click', 'a', stepBackHandler);
        $('.' + settings.cssClass.stepBack).on('click', 'a', stepBackHandler);

        $('.' + settings.cssClass.startOver).off('click', 'a.first-node', startOverHandler);
        $('.' + settings.cssClass.startOver).on('click', 'a.first-node', startOverHandler);
    };
    
    /*
     * currentNodeHandler
     * 
     * Finds the next couplet when a lead of the current node is clicked.
     * 
     * @param {type} event
     * @returns {undefined}
     */
    var currentNodeHandler = function( event ) {
        event.preventDefault();
        next_id = $(event.target).attr('href').replace("#l_", "");
        nextCouplet();
    };
    
    /**
     * stepBackHandler
     * 
     * Finds the previous couplet when the 'step back' button is clicked, or, when a
     * lead in the path is clicked, finds the couplet the lead is in.
     * 
     * @param {type} event
     * @returns {undefined}
     */
    var stepBackHandler = function( event ) {
            event.preventDefault();
            var lead_id = $(event.target).attr('href').replace("#l_", "");
            next_id = getParent(lead_id);
            nextCouplet();
    };
    
    /**
     * startOverHandler
     * 
     * Restarts the key player at the first couplet.
     * 
     * @param {type} event
     * @returns {undefined}
     */
    var startOverHandler = function( event ) {
        event.preventDefault();
        next_id = $(event.target).attr('href').replace("#l_", "");
        nextCouplet();
    };

    /**
     * playerWindow function
     *
     * Sets up the window the KeyBase Player runs in. Can be overridden by the user. Any user-defined playerWindow
     * function either must set up containers (divs) for the current node, path, remaining items and discarded items,
     * or they must be already defined in the HTML.
     */
    $.fn.keybase.defaults.playerWindow = function() {
        $('<div>', {class: 'keybase-player-window'}).appendTo(settings.playerDiv);

        if (!$('.keybase-player-window').height()) {
            var panelHeight = $('.keybase-player-window').width() * 0.67;
            $('.keybase-player-window').css('height', panelHeight + 'px');
        }

        var css = {
            'position': 'absolute',
            'height': (($('.keybase-player-window').height()*0.5) - 3) + 'px',
            'width': '100%'
        };

        $('<div>', {class: 'keybase-player-leftpane'}).appendTo('.keybase-player-window');
        currentNodeElem = $('<div>', {class: settings.cssClass.currentNode}).css(css).appendTo('.keybase-player-leftpane');
        $('<div>', {class: 'keybase-player-drag-updown'}).appendTo('.keybase-player-leftpane');
        pathElem = $('<div>', {class: settings.cssClass.path}).css(css).appendTo('.keybase-player-leftpane');
        $('<div>', {class: 'keybase-player-drag-leftright'}).appendTo('.keybase-player-window');
        $('<div>', {class: 'keybase-player-rightpane'}).appendTo('.keybase-player-window');
        remainingItemsElem = $('<div>', {class: settings.cssClass.remainingItems}).css(css).appendTo('.keybase-player-rightpane');
        $('<div>', {class: 'keybase-player-drag-updown'}).appendTo('.keybase-player-rightpane');
        discardedItemsElem = $('<div>', {class: settings.cssClass.discardedItems}).css(css).appendTo('.keybase-player-rightpane');


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

        $('<h3>', {html: 'Current node'}).appendTo(currentNodeElem);
        $('<h3>', {html: 'Path'}).appendTo(pathElem);
        $('<h3>', {html: 'Remaining taxa (<span class="keybase-num-remaining"></span>)'}).appendTo(remainingItemsElem);
        $('<h3>', {html: 'Discarded taxa (<span class="keybase-num-discarded"></span>)'}).appendTo(discardedItemsElem);

        $('<div>').appendTo(currentNodeElem);
        $('<div>').appendTo(pathElem);
        $('<div>').appendTo(remainingItemsElem);
        $('<div>').appendTo(discardedItemsElem);

        currentNodeElem.children('div').css('height', (currentNodeElem.height() -
            currentNodeElem.children('h3').height() -
            (parseInt(currentNodeElem.children('h3').css('padding-top'))*2)) + 'px');
        pathElem.children('div').css('height', (pathElem.height() -
            pathElem.children('h3').height() -
            (parseInt(pathElem.children('h3').css('padding-top'))*2)) + 'px');
        remainingItemsElem.children('div').css('height', (remainingItemsElem.height() -
            remainingItemsElem.children('h3').height() -
            (parseInt(remainingItemsElem.children('h3').css('padding-top'))*2)) + 'px');
        discardedItemsElem.children('div').css('height', (discardedItemsElem.height() -
            discardedItemsElem.children('h3').height() -
            (parseInt(discardedItemsElem.children('h3').css('padding-top'))*2)) + 'px');


        // KeyBase Player menu
        $('<span>', {class: 'keybase-player-menu'}).appendTo('.' + settings.cssClass.currentNode + ' h3');
        $('<span>', {class: settings.cssClass.startOver}).appendTo('.keybase-player-menu');
        $('<a>', {href: '#'}).appendTo('.' + settings.cssClass.startOver);
        $('<span>', {class: settings.cssClass.stepBack}).appendTo('.keybase-player-menu');
        $('<a>', {href: '#'}).appendTo('.' + settings.cssClass.stepBack);

        $('body').on('click', '.keybase-player-filter a', function(e) {
            e.preventDefault();
            settings.localFilter();
        });

        // Local filter button
        $('<span>', {class: 'keybase-player-menu'}).appendTo('.' + settings.cssClass.remainingItems + ' h3');
        $('<span>', {class: 'keybase-player-filter'}).appendTo('.' + settings.cssClass.remainingItems + ' .keybase-player-menu');
        $('<a>', {href: '#'}).appendTo('.keybase-player-filter');


        // Resize Player panes
        var position;
        $('.keybase-player-window .keybase-player-drag-leftright').mousedown(function(e){
            e.preventDefault();
            position = $('.keybase-player-window').offset();
            $(document).mousemove(function(e){
                if (e.pageX > position.left+190 &&
                    e.pageX < position.left+$('.keybase-player-window').width()-190) {
                    $('.keybase-player-leftpane').css("width",e.pageX-position.left);
                    $('.keybase-player-drag-leftright').css('left', e.pageX-position.left);
                    $('.keybase-player-rightpane').css({"left": e.pageX-position.left+6,
                        "width": ($('.keybase-player-window').width()-$('.keybase-player-leftpane').width()-6) + 'px'});
                    //$('.keybase-player-leftpane>div').css('width', $('.keybase-player-leftpane').width()-2);
                }
            })
        });

        $('.keybase-player-leftpane .keybase-player-drag-updown').mousedown(function(e) {
            e.preventDefault();
            position = $('.keybase-player-leftpane').offset();
            $(document).mousemove(function(e) {
                if (e.pageY > position.top+29
                    && e.pageY < position.top+$('.keybase-player-window').height()-32) {
                    $('.keybase-player-leftpane .keybase-player-drag-updown').css('top', e.pageY-position.top+2);
                    currentNodeElem.css("height", e.pageY-position.top);
                    pathElem.css({'top': e.pageY-position.top+5,
                        'height': ($('.keybase-player-window').height()-currentNodeElem.height()-6) + 'px'});
                    currentNodeElem.children('div').css('height', (currentNodeElem.height() -
                        currentNodeElem.children('h3').height() -
                        (parseInt(currentNodeElem.children('h3').css('padding-top'))*2)) + 'px');
                    pathElem.children('div').css('height', (pathElem.height() -
                        pathElem.children('h3').height() -
                        (parseInt(pathElem.children('h3').css('padding-top'))*2)) + 'px');
                    if (pathElem.children('div').height() < 5) {
                        pathElem.children('div').css('overflow', 'hidden').children().hide();
                    }
                    else {
                        pathElem.children('div').css('overflow', 'auto').children().show();
                    }
                }
            })
        });

        $('.keybase-player-rightpane .keybase-player-drag-updown').mousedown(function(e) {
            e.preventDefault();
            position = $('.keybase-player-rightpane').offset();
            $(document).mousemove(function(e) {
                if (e.pageY > position.top+29
                    && e.pageY < position.top+$('.keybase-player-window').height()-32) {
                    $('.keybase-player-rightpane .keybase-player-drag-updown').css('top', e.pageY-position.top+2);
                    remainingItemsElem.css("height", e.pageY-position.top);
                    discardedItemsElem.css({'top': e.pageY-position.top+5,
                        'height': ($('.keybase-player-window').height() -
                            remainingItemsElem.height()-6) + 'px'});
                    remainingItemsElem.children('div').css('height', (remainingItemsElem.height() -
                        remainingItemsElem.children('h3').height() -
                        (parseInt(remainingItemsElem.children('h3').css('padding-top'))*2)) + 'px');
                    discardedItemsElem.children('div').css('height', (discardedItemsElem.height() -
                        discardedItemsElem.children('h3').height() -
                        (parseInt(discardedItemsElem.children('h3').css('padding-top'))*2)) + 'px');
                    if (discardedItemsElem.children('div').height() < 5) {
                        discardedItemsElem.children('div').css('overflow', 'hidden').children().hide();
                    }
                    else {
                        discardedItemsElem.children('div').css('overflow', 'auto').children().show();
                    }
                }
            })
        });

        $(document).mouseup(function(e){
            $(document).unbind('mousemove');
        })
    };

    /**
     * currentNodeDisplay function
     *
     * Displays the current node.
     *
     * @param node
     * @param currentNodeDiv
     */
    $.fn.keybase.defaults.currentNodeDisplay = function(node, currentNodeDiv) {
        var leads = [];
        $.each(node, function(index, item) {
            var lead = '<li><a href="#l_' + item.lead_id + '">' + item.lead_text + '</li>';
            leads.push(lead);
        });
        $(currentNodeDiv).eq(0).children('div').eq(0).html('<ul>' + leads.join('') + '</ul>');
    };

    /**
     * resultDisplay function
     *
     * Displays the determination result.
     *
     * @param result
     * @param resultDiv
     */
    $.fn.keybase.defaults.resultDisplay = function(result, resultDiv) {
        if (result[0].url) {
            var text = '<a href="' + result[0].url + '">' + result[0].item_name + '</a>';
        }
        else {
            var text = result[0].item_name;
        }
        $(resultDiv).eq(0).children('div').eq(0).html('<div class="keybase-player-result">Result: <b>' + text + '</b></div>');
    };

    /**
     * pathDisplay function
     *
     * Displays the path followed to get to the current couplet (or result).
     *
     * @param path
     * @param pathDiv
     */
    $.fn.keybase.defaults.pathDisplay = function(path, pathDiv) {
        var leads = [];
        $.each(path, function(index, item) {
            if (!filter_parents.length || filter_parents[item.parent_id] > 1) {
                var lead = '<li><a href="#l_' + item.lead_id + '">' + item.lead_text + '</li>';
                leads.push(lead);
            }
         });
        $(pathDiv).eq(0).children('div').eq(0).html('<ol>' + leads.join('') + '</ol>');
    };


    /**
     * remainingItemsDisplay functions
     *
     * Displays the remaining items.
     *
     * @param items
     * @param itemsDiv
     */
    $.fn.keybase.defaults.remainingItemsDisplay = function(items, itemsDiv) {
        var list = itemsDisplay(items);
        $(itemsDiv).eq(0).children('h3').eq(0).html('Remaining items (' + items.length + ')');
        $(itemsDiv).eq(0).children('div').eq(0).html('<ul>' + list.join('') + '</ul>');
    };
    
    /**
     * discardedItemsDisplay function
     *
     * Displays the discarded items.
     *
     * @param items
     * @param itemsDiv
     */
    $.fn.keybase.defaults.discardedItemsDisplay = function(items, itemsDiv) {
        var list = itemsDisplay(items);
        $(itemsDiv).eq(0).children('h3').eq(0).html('Discarded items (' + items.length + ')');
        $(itemsDiv).eq(0).children('div').eq(0).html('<ul>' + list.join('') + '</ul>');
    };

    /*
     * itemsDisplay function
     * 
     * @param {type} items
     * @returns {undefined}
     */
    var itemsDisplay = function(items) {
        var list = [];
        $.each(items, function(index, item) {
            var entity;
            entity = '<li>';
            if (item.url) {
                entity += '<a href="' + item.url + '">' + item.item_name + '</a>';
            }
            else {
                entity += item.item_name;
            }
            if (item.to_key) {
                entity += '<a href="/keybase/key/show/' + item.to_key + '"><span class="keybase-player-tokey"></span></a>';
            }
            if (item.link_to_item_name) {
                entity += ': ';
                if (item.link_to_url) {
                    entity += '<a href="' + item.link_to_url + '">' + item.link_to_item_name + '</a>';
                }
                else {
                    entity += item.link_to_item_name;
                }
                if (item.link_to_key) {
                    entity += '<a href="/keybase/key/show/' + item.link_to_key + '"><span class="keybase-player-tokey"></span></a>';
                }
            }
            entity += '</li>';
            list.push(entity);
        });
        return list;
    };


    /**
     * function keyTitle
     *
     * Displays the title of the key.
     */
    $.fn.keybase.defaults.keyTitle = function(json) {
        if (!$(settings.titleDiv).length) {
            if (settings.titleDiv.substr(0, 1) === '#') {
                $('<div>', {id: settings.titleDiv.substr(1)}).appendTo(settings.playerDiv);
            }
            else {
                $('<div>', {class: settings.titleDiv.substr(1)}).appendTo(settings.playerDiv);
            }
        }

        $('<div>', {class: 'keybase-project-icon'})
            .append('<img src="' + json.project.project_icon + '" alt="" />')
            .appendTo(settings.titleDiv);

        $('<h1>', {
            style: "float:left;",
            html: '<a href="/keybase/project/show/' + json.project.project_id + '"<span class="keybase-project-name">' + json.project.project_name + '</span></a>: <span class="keybase-key-name">' + json.key_name + '</span>'
        }).appendTo(settings.titleDiv);

    };

    /**
     * function keySource
     *
     * Displays the source publication of the key.
     *
     * @param source
     */
    $.fn.keybase.defaults.keySource = function(source) {
        var str;
        if (source.author && source.publication_year && source.title) {
            if (source.is_modified) {
                str = 'Modified from: ';
            }
            else {
                str = 'From: ';
            }

            str += '<b>' + source.author + '</b> (' + source.publication_year + '). ';
            if (source.journal) {
                str += source.title + '. <i>' + source.journal + '</i>';
                if (source.series) {
                    str += ', ser. ' + source.series;
                }
                str += ' <b>' + source.volume + '</b>';
                if (source.part) {
                    str += '(' + source.part + ')';
                }
                str += ': ' + source.page + '.';
            }
            else {
                if (source.in_title) {
                    str += source.title + '. In: ';
                    if (source.in_author) {
                        str += source.in_author + ', ';
                    }
                    str += '<i>' + source.in_title + '</i>';
                    if (source.volume) {
                        str += ' <b>' + source.volume + '</b>';
                    }
                    if (source.page) {
                        str += ', pp. ' + source.page;
                    }
                    str += '.';
                    if (source.publisher) {
                        str += ' ' + source.publisher;
                        if (source.place_of_publication) {
                            str += ',';
                        }
                        else {
                            str += '.';
                        }
                    }
                    if (source.place_of_publication) {
                        str += ' ' + source.place_of_publication + '.';
                    }
                }
            }
        }

        if (!$(settings.sourceDiv).length) {
            if (settings.sourceDiv.substr(0, 1) === '#') {
                $('<div>', {id: settings.sourceDiv.substr(1)}).appendTo(settings.playerDiv);
            }
            else {
                $('<div>', {class: settings.sourceDiv.substr(1)}).appendTo(settings.playerDiv);
            }
        }
        $(settings.sourceDiv).html(str);
    };

    /*
     * KeyBase processing functions
     */

    /**
     * function nestedSets
     *
     * Creates the nested sets that are necessary to run or reproduce the key. Initiates the getNode function, which
     * will do the heavy lifting and when the getNode function has traversed all the nodes creates the nested set for
     * the root node.
     */
    var nestedSets = function() {
        nested_sets = [];

        i = 1;
        getNode(rootNodeID);

        nested_sets.sort(function(a, b) {
            return a.left - b.left;
        });

        json.first_step.left = 1;
        json.first_step.right = Math.max.apply(Math, JSPath.apply('.right', nested_sets));
    };

    /**
     * function getNode
     *
     * Work horse for the nestedSets function: traverses the key by finding the leads for which the parent ID is the
     * ID for the current lead at each step.
     *
     * @param parentID
     */
    var getNode = function(parentID) {
        var items = JSPath.apply('.leads{.parent_id==' + parentID + '}', json);
        $.each(items, function( index, item ) {
            i++;
            item.left = i;
            if (!item.item) {
                getNode(item.lead_id);
            }
            item.right = i;
            nested_sets.push(item);
        });
    };

    var getNodes = function() {
        var parent_ids = JSPath.apply('.parent_id', nested_sets);
        var ids = [];
        keyNodes = [];
        var i = 1;

        $.each(parent_ids, function(index, item) {
            if (ids.indexOf(item) === -1) {
                ids.push(item);
                var node = {
                    "parent_id": item,
                    "node_number": i
                };
                keyNodes.push(node);
                i++;
            }
        });
    };

    /**
     * function nextCouplet
     *
     * Main function for the KeyBase Player. Calls the processing functions that return the current node, path followed
     * to get to the current node, and remaining and discarded items as Javascript Objects and then the display
     * functions that will turn the Javascript into human readable HTML. Display functions can be overridden by the user
     * when the KeyBase function is called.
     */
    var nextCouplet = function() {
        if (next_id === rootNodeID) {
            left = json.first_step.left;
            right = json.first_step.right;
        }
        else {
            var curnode = JSPath.apply('.leads{.lead_id === "'+  next_id + '"}', json);
            left = curnode[0].left;
            right = curnode[0].right;
        }

        // Current node
        currentNode(next_id);
        if (current_node.length > 0) {
            settings.currentNodeDisplay(current_node, '.' + settings.cssClass.currentNode);
        }
        else {
            var result = getResult();
            settings.resultDisplay(result, '.' + settings.cssClass.currentNode);
        }
        $('.keybase-player-startover:eq(0)>a:eq(0)').attr('href', '#l_' + rootNodeID);
        $('.keybase-player-stepback:eq(0)>a:eq(0)').attr('href', '#l_' + next_id);

        // Path
        var path = getPath();
        settings.pathDisplay(path, '.' + settings.cssClass.path);

        // Remaining and discarded items
        var remaining_items = auxRemaining();
        var items = remainingItems(remaining_items);
        settings.remainingItemsDisplay(items.remaining, '.' + settings.cssClass.remainingItems);
        settings.discardedItemsDisplay(items.discarded, '.' + settings.cssClass.discardedItems);
    };

    /**
     * function currentNode
     *
     * @param parentID
     * @returns {*}
     */
    var currentNode = function(parentID) {
        if (filter_leads.length === 0) {
            current_node = JSPath.apply('.leads{.parent_id === "' + parentID + '"}', json);
        }
        else {
            var path = '.leads{.parent_id == "' + parentID + '"}{.lead_id==$lead_id}';
            var node = JSPath.apply(path, json, {lead_id: filter_leads});
            if (node.length === 1) {
                next_id = node[0].lead_id;
                currentNode(next_id);
            }
            else {
                current_node = node;
            }
        }
    };

    /**
     * function getResult
     *
     * Gets the determination result when the currentNode is not a couplet, but a taxon.
     *
     * @returns {*}
     */
    var getResult = function() {
        var item_id = JSPath.apply('.leads{.lead_id == "'+  next_id + '"}.item', json)[0];
        return JSPath.apply('.items{.item_id == "'+  item_id + '"}', json);
    };

    /**
     * function getPath
     *
     * Gets the path followed to get to the current node.
     *
     * @returns {*}
     */
    var getPath = function() {
        return JSPath.apply('.leads{.left <= ' + left + ' && .right >= ' + right + '}', json);
    };

    /**
     * function auxRemaining
     *
     * Help function that gets all the items that are still in play
     *
     * @returns {*}
     */
    var auxRemaining = function() {
        return JSPath.apply('.leads{.item && .left >= ' + left + ' && .right <= ' + right + '}.item', json);
    };

    /**
     * function remainingItems
     *
     * Splits the items into remaining and discarded items
     *
     * @param aux_remaining
     * @returns {{remaining: Array, discarded: Array}}
     */
    var remainingItems = function(aux_remaining) {
        var remaining_items = [];
        var discarded_items = [];

        var items;
        if (filter_items.length === 0) {
            items = json.items;
        }
        else {
            var path = '.items{.item_id===$item_id}';
            items = JSPath.apply(path, json, {item_id: filter_items});
        }

        $.each(items, function(index, item) {
            if (aux_remaining.indexOf(item.item_id) > -1) {
                remaining_items.push(item);
            }
            else {
                discarded_items.push(item);
            }
        })

        var items = {
            remaining: remaining_items,
            discarded: discarded_items
        };
        return items;
    };


    /**
     * function getParent
     *
     * Gets the parent ID for the lead (based on the lead ID). Used to get the lead ID to initiate the trace back
     * functions.
     *
     * @param leadID
     * @returns {*}
     */
    var getParent = function(leadID) {
        return JSPath.apply('.leads{.lead_id === "'+  leadID + '"}.parent_id[0]', json);
    };


    /**
     * function indentedKey
     *
     * Initiates the indentedKeyNode function that creates the indented key. At the moment there is no display function
     * for the indented key yet and the display depends on the Dynatree widget.
     */
    var indentedKey = function() {
        indented_key = [];
        var root = {};
        root.title = json.key_name;
        root.isFolder = true;
        root.expand = true;
        root.children = [];

        indentedKeyNode(rootNodeID, root);
        indented_key.push(root);
    };

    $.fn.keybase.defaults.indentedKeyDisplay = function(json) {
        indentedKeyHtml = '<div class="keybase-indented-key">';
        displayIndentedKeyCouplet(indented_key[0].children[0]);

        indentedKeyHtml += '</div> <!-- /.keybase-indented-key -->';

        $(settings.indentedKeyDiv).html(indentedKeyHtml);
    };

    var displayIndentedKeyCouplet = function(couplet) {
        var leads = couplet.children;
        indentedKeyHtml += '<ul class="keybase-couplet">';
        for (var i = 0; i < leads.length; i++) {
            var lead = leads[i];
            indentedKeyHtml += '<li>';
            indentedKeyHtml += '<div class="keybase-lead">';
            indentedKeyHtml += '<span class="keybase-from-node">' + lead.fromNode + '</span>';
            indentedKeyHtml += '<span class="keybase-lead-text">' + lead.title + '</span>';
            var child = lead.children[0];
            if (child.title === "Couplet") {
                indentedKeyHtml += '</div> <!-- /.keybase-lead -->';
                displayIndentedKeyCouplet(child);
            }
            else {
                var item = JSPath.apply('.items{.item_id==' + child.children[0].item_id + '}', json)[0];
                indentedKeyHtml += '<span class="keybase-to-item">';
                if (item.url) {
                    indentedKeyHtml += '<a href="' + item.url + '">' + item.item_name + '</a>';
                }
                else {
                    indentedKeyHtml += item.item_name;
                }
                if (item.to_key) {
                    indentedKeyHtml += '<a href="' + item.to_key + '"><span class="keybase-player-tokey"></span></a>';
                }

                if (item.link_to_id) {
                    indentedKeyHtml += ': ';
                    if (item.link_to_url) {
                        indentedKeyHtml += '<a href="' + item.link_to_url + '">' + item.link_to_item_name + '</a>';
                    }
                    else {
                        indentedKeyHtml += item.link_to_item_name;
                    }
                    if (item.link_to_key) {
                        indentedKeyHtml += '<a href="' + item.link_to_key + '"><span class="keybase-player-tokey"></span></a>';
                    }

                }

                indentedKeyHtml += '</span> <!-- /.keybase-to-item -->';
                indentedKeyHtml += '</div> <!-- /.keybase-lead -->';
            }
            indentedKeyHtml += '</li>';
        }

        indentedKeyHtml += '</ul> <!-- /.keybase-couplet -->';
    };

    /**
     * function indentedKeyNode
     *
     * Creates the indented key nodes.
     *
     * @param parent_id
     * @param parent
     */
    var indentedKeyNode = function(parent_id, parent) {
        var children = JSPath('.{.parent_id==' + parent_id + '}', nested_sets);
        parent.children = [];
        if (children.length > 0) {
            var couplet = {};
            couplet.title = "Couplet";
            couplet.isFolder = true;
            couplet.expand = true;
            couplet.children = [];
            parent.children[0] = couplet;

            //parent.children = children;
            $.each(children, function(index, lead) {
                var child = $.extend({}, lead);

                child.fromNode = JSPath.apply('.{.parent_id ==="' +  child.parent_id+ '"}', keyNodes)[0].node_number;
                //delete child.parent_id;

                var toNode = JSPath.apply('.{.parent_id ==="' +  child.lead_id+ '"}', keyNodes);
                if (toNode.length > 0) {
                    child.toNode = toNode[0].node_number;
                }
                //delete child.lead_id;

                child.title = lead.lead_text;
                child.href = "#" + lead.lead_id;
                child.expand = true;
                if (child.item == null) {
                    delete child.item;
                }
                //delete child.lead_text;
                delete child.left;
                delete child.right;


                couplet.children[index] = child;
                indentedKeyNode(lead.lead_id, child);
            });
        }
        else {
            var taxa = {};
            taxa.title = "Item";
            taxa.isFolder = true;
            taxa.children = [];

            var taxon = {};
            taxon.item_id = parent.item;
            taxon.title = JSPath.apply('.items{.item_id==' + parent.item + '}.item_name', json)[0];
            taxa.children[0] = taxon;
            //alert (taxon.title);
            delete parent.item;
            taxa.expand = true;
            parent.children[0] = taxa;
        }
    };

    /**
     * function bracketedKey
     *
     * Creates the bracketed key. At the moment there is no display function for the bracketed key yet and the display
     * depends on the Dynatree widget, which is not very suitable for the purpose.
     */
    var bracketedKey = function() {
        bracketed_key = [];
        parent_ids = JSPath.apply('.parent_id', nested_sets);
        var nodes = [];

        var root = {};
        root.title = json.key_name;
        root.isFolder = true;
        root.expand = true;
        root.children = [];

        $.each(parent_ids, function (index, parent) {
            if (nodes.indexOf(parent) == -1) {
                nodes.push(parent);

                var couplet = {};
                couplet.title = "Couplet";
                couplet.isFolder = true;
                couplet.expand = true;
                couplet.children = [];

                leads = JSPath.apply('.{.parent_id==' + parent + '}', nested_sets);
                $.each(leads, function(index, lead) {
                    var l = {};
                    l.fromNode = JSPath.apply('.{.parent_id ==="' +  lead.parent_id+ '"}', keyNodes)[0].node_number;
                    //delete child.parent_id;

                    var toNode = JSPath.apply('.{.parent_id ==="' +  lead.lead_id+ '"}', keyNodes);
                    if (toNode.length > 0) {
                        l.toNode = toNode[0].node_number;
                    }
                    //delete child.lead_id;
                    l.parent_id = lead.parent_id;
                    l.lead_id = lead.lead_id;

                    l.title = lead.lead_text;
                    l.href = '#' + lead.lead_id;
                    l.expand = true;
                    l.children = [];
                    if (lead.item != null) {
                        var items = {};
                        items.title = "Item";
                        items.isFolder = true;
                        items.expand = true;
                        items.children = [];
                        var item = {};
                        item.item_id = lead.item;
                        item.title = JSPath.apply('.items{.item_id==' + lead.item + '}.item_name', json)[0];
                        item.expand = true;
                        items.children.push(item);
                        l.children.push(items);
                    }
                    couplet.children.push(l);
                });
                root.children.push(couplet);

            }
        });
        bracketed_key.push(root);
    };

    $.fn.keybase.defaults.bracketedKeyDisplay = function(json) {
        /*$(settings.bracketedKeyDiv).dynatree({
            children: bracketed_key,
            data: {mode: "all"},
            expand: true
        });*/
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
                html += '<span class="keybase-lead-text">' + lead.title + '</span>';
                if (lead.toNode !== undefined) {
                    html += '<span class="keybase-to-node"><a href="#l_' + lead.lead_id + '">' + lead.toNode + '</a></span>';
                }
                else {
                    var toItem = items[0].children[0];
                    var item = JSPath.apply('.items{.item_id==' + toItem.item_id + '}', json)[0];
                    html += '<span class="keybase-to-item">';
                    if (item.url) {
                        html += '<a href="' + item.url + '">' + item.item_name + '</a>';
                    }
                    else {
                        html += item.item_name;
                    }
                    if (item.to_key) {
                        html += '<a href="' + item.to_key + '"><span class="keybase-player-tokey"></span></a>';
                    }

                    if (item.link_to_id) {
                        html += ': ';
                        if (item.link_to_url) {
                            html += '<a href="' + item.link_to_url + '">' + item.link_to_item_name + '</a>';
                        }
                        else {
                            html += item.link_to_item_name;
                        }
                        if (item.link_to_key) {
                            html += '<a href="' + item.link_to_key + '"><span class="keybase-player-tokey"></span></a>';
                        }

                    }

                    html += '</span> <!-- /.to-item -->';
                }
                html += '</div> <!-- /.keybase-lead -->';
            }
            html += '</div> <!-- /.keybase-couplet -->';

        }
        html += '</div> <!-- /.keybase-bracketed_key -->';
        $(settings.bracketedKeyDiv).html(html);

        /*$('.keybase-bracketed-key .keybase-lead').each(function() {
            var divHeight = $(this).height();
            $(this).find('.keybase-from-node').eq(0).css({'height': divHeight + 'px'});
        });*/
    };


    /*
     * Filter functions
     */

    /**
     * function localFilter
     *
     * Sets up the window for the local filter, as well as the functionality.
     */
    $.fn.keybase.defaults.localFilter = function() {

        var localFilter = $('<div>', {class: 'keybase-local-filter'});
        var divIncluded = $('<div>');
        var divExcluded = $('<div>');
        var labelIncluded = $('<label>', {for: 'initems', html: 'Included items'});
        var labelExcluded = $('<label>', {for: 'outitems', html: 'Excluded items'});
        var selectIncluded = $('<select>', {name:"includedItems", id: 'initems', multiple:"multiple"});
        var selectExcluded = $('<select>', {name:"excludedItems", id: 'outitems', multiple:"multiple"});

        incl = [];
        excl = [];
        $.each(json.items, function(index, item) {
            var name = item.item_name;
            name += (item.link_to_item_name) ? ': ' + item.link_to_item_name : '';
            var opt = '<option value="' + item.item_id + '">' + name + '</option>';
            if (filter_items.length === 0 || filter_items.indexOf(item.item_id) !== -1) {
                incl.push(opt);
            }
            else {
                excl.push(opt);
            }
        });

        selectIncluded.append(incl.join(''));
        sortOptions('includedItems');
        if (excl) {
            selectExcluded.append(excl.join(''));
            sortOptions('excludedItems');
        }

        var buttonDiv = $('<div>', {class: 'keybase-filter-buttons'});
        buttonDiv.append('<span><button class="keybase-filter-button-excl"></button></span>').
            append('<span><button class="keybase-filter-button-excl-all"></button></span>').
            append('<span><button class="keybase-filter-button-incl"></button></span>').
            append('<span><button class="keybase-filter-button-incl-all"></button></span>');

        var footerDiv = $('<div>', {class: 'keybase-local-filter-footer'});
        footerDiv.append('<button class="keybase-local-filter-cancel">Cancel</button>').
            append('<button class="keybase-local-filter-ok">OK</button>');

        divIncluded.append(labelIncluded).append(selectIncluded);
        divExcluded.append(labelExcluded).append(selectExcluded);

        localFilter.append(divIncluded).append(buttonDiv).append(divExcluded).append(footerDiv);

        keybaseLightBox(localFilter);

        boxHeaders();

        $('.keybase-filter-button-excl').click(function(e) {
            $('[name=includedItems]>option:selected').each(function() {
                $(this).remove();
                $(this).removeAttr('selected')
                $('[name=excludedItems]').append($(this));
            });
            sortOptions('excludedItems');
            filterItems();
            boxHeaders();
        });

        $('.keybase-filter-button-excl-all').click(function(e) {
            $('[name=includedItems]>option').each(function() {
                $(this).remove();
                $(this).removeAttr('selected')
                $('[name=excludedItems]').append($(this));
            });
            sortOptions('excludedItems');
            filterItems();
            boxHeaders();
        });

        $('.keybase-filter-button-incl').click(function(e) {
            $('[name=excludedItems]>option:selected').each(function() {
                $(this).remove();
                $(this).removeAttr('selected')
                $('[name=includedItems]').append($(this));
            });
            sortOptions('includedItems');
            filterItems();
            boxHeaders();
        });

        $('.keybase-filter-button-incl-all').click(function(e) {
            $('[name=excludedItems]>option').each(function() {
                $(this).remove();
                $(this).removeAttr('selected')
                $('[name=includedItems]').append($(this));
            });
            sortOptions('includedItems');
            filterItems();
            boxHeaders();
        });

        $('.keybase-local-filter-cancel').click(function() {
            closeKeybaseLightbox();
        });

        $('.keybase-local-filter-ok').click(function() {
            closeKeybaseLightbox();
            if (filter_items.length) {
                setFilter();
            }
            else {
                removeFilter();
            }
        });
    };

    /**
     * function keybaseLightBox
     *
     * LightBox for the local filter.
     *
     * @param content
     */
    var keybaseLightBox = function(content) {
        // add lightbox/shadow <div/>'s if not previously added
        if($('#keybase-lightbox').size() == 0){
            var theLightbox = $('<div id="keybase-lightbox"/>');
            theLightbox.append(
                '<div class="keybase-lightbox-header">' +
                    '<i class="keybase-lightbox-close fa fa-close pull-right fa-border"></i>' +
                    '</div>').
                append('<div class="keybase-lightbox-content"></div>').
                append('<div class="keybase-lightbox-footer"></div>');

            var theShadow = $('<div id="keybase-lightbox-shadow"/>');
            theShadow.css('height', $(document).height() + 'px');
            $(theShadow).click(function(e){
                closeKeybaseLightbox();
            });

            $('body').append(theShadow);
            $('body').append(theLightbox);
            $(document).keydown(function (e) {
                if (e.keyCode == 27) {
                    closeKeybaseLightbox();
                }
            });
            $('.keybase-lightbox-close').click(function() {
                closeKeybaseLightbox();
            });
        }

        // remove any previously added content
        $('.keybase-lightbox-content').empty();

        // insert HTML content
        if(content != null){
            $('.keybase-lightbox-content').append(content);
        }

        // move the lightbox to the current window top + 100px
        $('#keybase-lightbox').css('top', $(window).scrollTop() + 100 + 'px');

        // display the lightbox
        $('#keybase-lightbox').show();
        $('#keybase-lightbox-shadow').show();

    };

    /**
     * function closeKeybaseLightbox
     *
     * Closes the light box.
     */
    var closeKeybaseLightbox = function() {
        // hide lightbox and shadow <div/>'s
        $('#keybase-lightbox').hide();
        $('#keybase-lightbox-shadow').hide();

        // remove contents of lightbox in case a video or other content is actively playing
        $('.keybase-lightbox-content').empty();
    };

    /**
     * function sortOptions
     *
     * Orders the options for the included and excluded items in the local filter.
     *
     * @param select
     */
    var sortOptions = function(select) {
        var options = $('[name=' + select + ']>option');
        options.sort(function(a,b) {
            if (a.text > b.text) return 1;
            else if (a.text < b.text) return -1;
            else return 0
        });
        $('[name=' + select + ']').html(options);
    };

    /**
     * function boxHeaders
     *
     * Sets the labels for the included and excluded items selects in the local filter. Label includes
     * the number of options in the select.
     */
    var boxHeaders = function() {
        $('label[for="initems"]').text('Included taxa (' + $('#initems>option').length + ')');
        $('label[for="outitems"]').text('Excluded taxa (' + $('#outitems>option').length + ')');
    };

    /**
     * function filterItems
     *
     * Creates an array of item IDs for items to filter on, based on the options in the Included items select.
     */
    var filterItems = function() {
        filter_items = [];
        var initems = $('[name=includedItems]>option');
        var outitems = $('[name=excludedItems]>option');
        if (initems.length && outitems.length) {
            $('[name=includedItems]>option').each(function () {
                filter_items.push($(this).val());
            });
        }
    };

    /**
     * function setFilter
     *
     * Calls the function that creates the filtered key and restarts the key.
     */
    var setFilter = function() {
        filterLeads();
        next_id = rootNodeID;
        nextCouplet();
        if ($('.keybase-player-filter-remove').length === 0) {
            remainingItemsElem.children('h3').children('.keybase-player-menu').append('<span class="keybase-player-filter-remove"><a href="#"></a></span>');
            $('.keybase-player-filter-remove').click(function(e) {
                e.preventDefault();
                removeFilter();
            });
        }
    };

    /**
     * function filterLeads
     *
     * Creates the array with leads that need to be followed to key out all items in the filter.
     */
    var filterLeads = function() {
        var path = '.{.item==$item}.left';
        var filter_nodes = JSPath.apply(path, nested_sets, {item: filter_items});

        filter_leads = [];
        var parents = [];
        $.each(filter_nodes, function(index, node) {
            var leads = JSPath.apply('.{.left <= '+ node + '}{.right >= ' + node + '}', nested_sets);
            $.each(leads, function(ind, lead) {
                if (filter_leads.indexOf(lead.lead_id) === -1) {
                    filter_leads.push(lead.lead_id);
                    parents.push(lead.parent_id);
                }
            });
        });

        // get object with parent_ids and their number of occurrences in the filtered set
        filter_parents = parents.reduce(function (acc, curr) {
            if (typeof acc[curr] == 'undefined') {
                acc[curr] = 1;
            } else {
                acc[curr] += 1;
            }
            return acc;
        }, {});

    };

    /**
     * function removeFilter
     *
     * Destroys the filter.
     */
    var removeFilter = function() {
        filter_items = [];
        filter_leads = [];
        filter_parents = [];
        next_id = rootNodeID;
        nextCouplet();
        $('.keybase-player-filter-remove').remove();
    };

}( jQuery ));
