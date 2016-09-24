var base_url = 'https://vicflora.rbg.vic.gov.au/dev';
var uri = location.href.substr(base_url.length + 1).split('/');
if (uri[uri.length - 1].indexOf('?') > -1) {
    uri[uri.length - 1] = uri[uri.length - 1].substr(0, uri[uri.length - 1].indexOf('?'));
}
var query_string = location.href.substr(location.href.indexOf('?') + 1);
var qstring;

var thumbnailBaseUrl = 'https://data.rbg.vic.gov.au/images/T/library/';
var previewBaseUrl = 'https://data.rbg.vic.gov.au/images/P/library/';

$(function() {
    if (query_string.indexOf('fq=end_or_higher_taxon%3Aend') !== -1) {
        $('#excludeHigherTaxa').prop('checked', true);
    }
    
    topMenu();
    $(window).on('resize', function() {
        topMenu();
    });
    
    // carousel on home page
    $('.carousel').carousel({
        interval: false
    });
    
    // auto-complete for search
    $('#term, input[name=q]').autocomplete({
        source: base_url + '/autocomplete/autocompleteName',
        minLength: 2
    });
    
    // buttons
    //$('input[type=submit], button').button();
    
    // submit search form button
    $('.submit').click(function(event) {
        event.preventDefault();
        //$('form').submit();
        var q = 'q=*:*';
        var form = $(this).parents('form').eq(0);
        if (form.find('[name=q]').eq(0).val()) {
            var q = 'q=' + form.find('[name=q]').eq(0).val();
        }
        
        var query = q;
        
        var filters = $('.facets input[type=hidden]');
        if (filters.length > 0) {
            var arr = [];
            filters.each(function() {
                var name = $(this).attr('name');
                var val = $(this).val();
                arr.push('fq=' + encodeURI(name + ':' + decodeURI(val)));
            });
            var fq = arr.join('&');
            
            query += '&' + fq;
        }
        
        if ($('#excludeHigherTaxa').prop('checked') && query.indexOf('fq=end_or_higher_taxon%3Aend') === -1  && query.indexOf('fq=end_or_higher_taxon:end') === -1) {
            query += '&fq=end_or_higher_taxon:end';
        }
        
        location.href = base_url + '/flora/search?' + query;
    });
    
    // query result navigation icons
    $('.query-result-nav a').hover(
        function() {
            $(this).addClass('ui-state-hover');
        }, 
        function() {
            $( this ).removeClass('ui-state-hover');
        }
    );
        
    $('.query-result-nav a').click(function(event) {
        event.preventDefault();
        var href = $(this).attr('href');
        var symb = $('#symbol').val();
        if (symb) {
            href += '&' + 'show_symbol=' + symb;
        }
        if ($('#excludeHigherTaxa').prop('checked') && href.indexOf('fq=end_or_higher_taxon%3Aend') === -1  && href.indexOf('fq=end_or_higher_taxon:end') === -1) {
            href += '&fq=end_or_higher_taxon%3Aend';
        }
        location.href = href;
        
    });
        
    $('.symbols').each(function() {
        var emptyspan = $('<span />', {
            'class': 'empty',
            'text': ' ',
        });
        emptyspan.appendTo($(this));
    });
    
    symbols();
    $('#symbol').change(function(event) {
        symbols();
    });



    facets();

    
    $('.query').on('click', '.clear', function( event ) {
        var name = $(event.target).parent('li').attr('data-vicflora-facet-name');
        $('input[name=' + name + ']').remove();
        $('form').submit();
    });
    
    $('.query').on('click', '.clear-all a', function() {
        var term = $('input[name=term]').val();
            var get = "";
        if (term.length > 0) {
            get = '?term=' + term; 
        }
        
        location.href = base_url + '/flora/search' + get;
    });
    
    
    
    $('#tabs').tabs();
    
    /* downloads */
    var checkedboxes = $('.download-fields input:checkbox:checked');
    var arr = [];
    checkedboxes.each(function() {
        arr.push($(this).val());
    var qstring = query_string + '&fl=' + arr.join(',');    });

    qstring = qstring + '&filename=' + $('.download-filename input').val();
    qstring = qstring + '&filetype=' + $('.download-filetype input:radio:checked').val();
    $('.download-submit a').attr('href', base_url + '/flora/download?' + qstring);
    
    $('.download-fields').on('change', 'input:checkbox', function(event) {
        var checkedboxes = $('.download-fields input:checkbox:checked');
        var arr = [];
        checkedboxes.each(function() {
            arr.push($(this).val());
        });
        var qstring = query_string + '&fl=' + arr.join(',');
        qstring = qstring + '&filetype=' + $('.download-filetype input:radio:checked').val();
        qstring = qstring + '&filename=' + $('.download-filename input').val();
        $('.download-submit a').attr('href', base_url + '/flora/download?' + qstring);
    });
    
    $('.download-filename').on('change', 'input', function(event) {
        var checkedboxes = $('.download-fields input:checkbox:checked');
        var arr = [];
        checkedboxes.each(function() {
            arr.push($(this).val());
        });
        var qstring = query_string + '&fl=' + arr.join(',');
        qstring = qstring + '&filename=' + $('.download-filename input').val();
        qstring = qstring + '&filetype=' + $('.download-filetype input:radio:checked').val();
        $('.download-submit a').attr('href', base_url + '/flora/download?' + qstring);
    });

    $('.download-filetype').on('change', 'input:radio', function(event) {
        var checkedboxes = $('.download-fields input:checkbox:checked');
        var arr = [];
        checkedboxes.each(function() {
            arr.push($(this).val());
        });
        var qstring = query_string + '&fl=' + arr.join(',');
        qstring = qstring + '&filename=' + $('.download-filename input').val();
        qstring = qstring + '&filetype=' + $('.download-filetype input:radio:checked').val();
        $('.download-submit a').attr('href', base_url + '/flora/download?' + qstring);
    });
    
    /*
     * Detail page tabs
     */
    $('#detail-page-tab a:first').tab('show');
    var url = document.location.toString();
    if ($('.nav-tabs').length > 0 && url.match('#tab-')) {
        $('.nav-tabs a[href=#'+url.split('#')[1]+']').tab('show');
    } 
    
    /*
     * Scientific names
     */
    $('.scientific_name').each(function() {
        var name = $(this).html();
        if (name.indexOf(' subsp. ') !== -1) {
            var species = name.substring(0,name.indexOf(' subsp.'));
            var subspeciesEpithet = name.substring(name.indexOf(' subsp. ')+7);
            $(this).html(species).after(' subsp. <span class="scientific_name">' + subspeciesEpithet + '</span>');
        }
        if (name.indexOf(' var. ') !== -1) {
            var species = name.substring(0,name.indexOf(' var.'));
            var subspeciesEpithet = name.substring(name.indexOf(' var. ')+5);
            $(this).html(species).after(' var. <span class="scientific_name">' + subspeciesEpithet + '</span>');
        }
        if (name.indexOf(' f. ') !== -1) {
            var species = name.substring(0,name.indexOf(' f.'));
            var subspeciesEpithet = name.substring(name.indexOf(' f. ')+3);
            $(this).html(species).after(' f. <span class="scientific_name">' + subspeciesEpithet + '</span>');
        }
    });
    
    if (uri[1] === 'glossary') {
        glossary();
    }
    
    /*
     * Thumbnails
     */
    $('a[data-toggle=tab][aria-controls=images]').on('shown.bs.tab', function(e) {
        thumbnails();
    });
    
    /*
     * Hero image
     */
    $('.hero-image img').click(function(e) {
        $('[href=#tab-images]').tab('show');
    });
    
    $('.profile-map img').click(function(e) {
        $('[href=#tab-distribution]').tab('show');
    });
    
    
    /*
     * SVG maps
     * 
     */
    var reg = /taxon\/([0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12})/;
    if (reg.test(location.href)) {
        var guid = reg.exec(location.href)[1];
        var svgWidth = 480;
        $.getJSON(base_url + '/flora/svg_map/' + guid + '/avh_distribution/png/' + svgWidth, function (item) {
            $('#svg-avhdistribution').html('<img src="' + item.src + '" alt="' + item.alt + '" usemap="#vicflora_bioregion" />');
        });
        $.ajax({
            url: base_url + '/flora/imageMap/' + svgWidth,
            dataType: 'html',
            success: function(data) {
                $('#svg-avhdistribution').append(data);
            }
        });
            
        var url = base_url + '/ajax/bioregion_legend/' + guid + '/establishment_means';
        console.log(url);
        $.getJSON(base_url + '/ajax/bioregion_legend/' + guid + '/establishment_means', function(data) {
            console.log(data);
            var items = [];
            var headerrow = '<tr>';
            headerrow += '<th>&nbsp;</th>';
            headerrow += '<th>Bioregion</th>';
            headerrow += '<th>Occurrence status</th>';
            headerrow += '<th>Establishment means</th>';
            headerrow += '</tr>';
            items.push(headerrow);
            $.each(data, function(index, item) {
                if (item.occurrence_status === 'absent') {
                    item.colour = '#e9e9e9;';
                }

                var row = '<tr>';
                row += '<td><span class="legend-symbol" style="background-color:' + item.colour + '">&nbsp;</span></td>';
                row += '<td>' + item.sub_name_7 + '</td>';
                row += '<td>' + item.occurrence_status + '</td>';
                row += '<td>' + item.establishment_means + '</td>';
                row += '</tr>';
                items.push(row);
            });
            $('table.bioregions').html(items.join(''));
        });
        
        
        /*
         * Siblings
         */
        $('#nav-siblings, #nav-children').change(function(e) {
            var val = $(this).val();
            if (val.length > 0) {
                var href = base_url + '/flora/taxon/' + val;
                if (href !== location.href) {
                    location.href = href;
                }
            }
        });
    }
    
    if (location.href.indexOf('/flora/bioregions') > -1) {
        $('[name=vicflora_bioregion]').on('click', 'area', function(e) {
            e.preventDefault();
            var title = $(this).attr('title');
            $('#info').html('<h3>' + title + '</h3>');
            $.getJSON(base_url + '/ajax/bioregionInfo/' + title, function (item) {
                $('#info').append(item.Description);
                $('#info').append('<p><b>Source:</b> <a href="http://www.depi.vic.gov.au/environment-and-wildlife/biodiversity/evc-benchmarks#' + 
                        item.DepiCode.toLowerCase() + '" target="_blank">http://www.depi.vic.gov.au/environment-and-wildlife/biodiversity/evc-benchmarks#' +
                        item.DepiCode.toLowerCase() + '</a></p>');
                $('#info').append('<p>' + '<a class="btn btn-primary" href="' + base_url + '/flora/bioregions/' + item.FovNaturalRegion.toLowerCase().replace(/ /g, '-') + '">More info.</a> '
                        + '<a class="btn btn-primary" href="' + base_url + '/flora/search?q=*:*&fq=ibra_7_subregion%3A' + encodeURIComponent('"' + item.Name + '"') +
                        '">Find taxa in ' + item.Name + '</a></p>');
            });
        });
    }
    
    if (location.href.indexOf('/st/') > -1) {
        $('.sixteen.columns>h2').before('<div class="edit-static"><a href="' + location.href + '/_edit">Edit</a></div>');
    }
    
    $('.legend button').on('click', function() {
        $(this).hide();
        $('.legend img, .legend .fa-remove').show();
    });
    
    $('.legend .fa-remove').on('click', function() {
        $('.legend img, .legend .fa-remove').hide();
        $('.legend button').show();
    });
    
});

var glossary = function() {
    var hash = location.hash;
    if (!hash) {
        if (uri.length > 2) {
            hash = '#' + uri[2];
        }
        else {
            hash = '#a';
        }
    }

    var term = hash.substr(1).toLowerCase();
    getGlossaryTerms(term);
    
    $('#glossary-first-letter').on('click', 'a', function(e) {
        e.preventDefault();
        var href = $(this).attr('href');
        var first = href.substr(href.indexOf('#') + 1);
        getGlossaryTerms(first);
    });
    
    $('#term-list').on('click', 'a', function(e) {
        e.preventDefault();
        var container = $(this).parent('li');
        if (container.children('.definition').length === 0) {
            var term = $(this).attr('href').substr(1);
            getGlossaryDefinition(term, container);
        }
        else {
            container.children('.definition').remove();
            container.children('.fa').removeClass('fa-caret-down').addClass('fa-caret-right');
        }
    });
    
    $('#definition, #term-list').on('click', 'a.glossary-link', function(e) {
        e.preventDefault();
        var term = $(this).attr('href').substr(1);
        getGlossaryTerms(term);
    });
    
    $(window).on('resize', function() {
        var term = $('#term-list li.active a').attr('href').substr(1);
        var index = $('#term-list li').index($('#term-list li.active'));
        var container = $('#term-list li:eq(' + index + ')');
        getGlossaryTerms(term);
        
    });

};

var getGlossaryTerms = function(term) {
    firstletter = term.substr(0,1).toLowerCase();
    var url = base_url + '/ajax/glossary_terms/' + firstletter;
    $.getJSON(url, function(data) {
        var items = [];
        var liIndex = 0;
        $.each(data, function(index, item) {
            if (term.length > 1) {
                if (encodeURIComponent(item.term) === term) {
                    liIndex = index;
                }
            }
            var option = '<li><a href="#' + encodeURIComponent(item.term) + '">' + item.term + '</a></li>';
            items.push(option);
        });
        $('#term-list').html('<ul>' + items.join('') + '</ul>');
        $('#term-list li').prepend('<i class="fa fa-caret-right"></i> ');
        
        $('#glossary-first-letter a').each(function() {
            $(this).removeClass('active');
            var href = $(this).attr('href');
            if (href.toLowerCase().indexOf('#' + firstletter) !== -1) {
                $(this).addClass('active');
            }
        });
        
        var container = $('#term-list li:eq(' + liIndex + ')');
        if (data.length) {
            getGlossaryDefinition(encodeURIComponent((term.length > 1) ? term : data[0].term), container);
        }
        var viewportWidth = $(window).width();
        if (viewportWidth >= 768) {
            $('#term-list ul').css("height", $(window).height()-$('footer').height()-$('#term-list').offset().top-40 + 'px');
            $('#glossary-terms').css("height", $('footer').offset().top-$('#glossary-terms').offset().top)+'px';
        }
    });
};

var getGlossaryDefinition = function(term, container) {
    var inline = false;
    var viewportWidth = $(window).width();
    if (viewportWidth < 768) {
        inline = true;
    }
    var relTypes = {
        "isRelatedTo(cf.)": "Is related to",
        "hasAdjective": "Has adjective",
        "isAdjectiveOf": "Is adjective of",
        "hasAbbreviation": "Has abbreviation",
        "isAbbreviationOf": "Is abbreviation of",
        "hasSynonym": "Has synonym",
        "hasExactSynonym": "Has exact synonym",
        "hasMoreInclusiveSynonym": "Has more inclusive synonym",
        "hasLessInclusiveSynonym": "Has less inclusive synonym",
        "hasPartiallyOverlappingSynonym": "Has partially overlapping synonym",
        "hasApproximatelyEqualSynonym": "Has approximately equal synonym",
        "hasPlural": "Has plural",
        "isPluralOf": "Is plural of",
        "hasVariation": "Has variation",
        "isVariationOf": "Is variation of",
        "hasSingular": "Has singular",
        "isSingularOf": "Is singular of",
        "isOpposedTo": "Is opposed to",
        "hasTranslation": "Has translation",
        "isTranslationOf": "Is translation of"
    };
    var url = base_url + '/ajax/glossary_definition/' +  term;
    $.getJSON(url, function(item) {
        var html = '';
        if (!inline) {
            var html = '<h2>' + item.term + '</h2>';
        }
        if (item.definition) {
            html += item.definition;
        }
        var rels = [];
        if (item.relationships.length) {
            $.each(item.relationships, function(index, rel) {
                var relationship = '<div class="row">';
                relationship += '<div class="col-xs-6 col-lg-5"><span class="glossary-rel-type">' + relTypes[rel.relationshipType] + '</span></div>';
                relationship += '<div class="col-xs-6 col-md-4 text-right"><a href="#' + rel.relatedTerm + '" class="glossary-link">' + rel.relatedTerm + '</a></div></div>';
                rels.push(relationship);
            });
            html += '<div class="glossary-relationships">';
            if (!inline || item.definition) {
                html += '<h4>Relationships</h4>';
            }
            html += rels.join('') + '</div>';
        }
        if (item.thumbnails.length) {
            var imgs = [];
            $.each(item.thumbnails, function(index, thumb) {
                var img = '<span class="glossary-image">';
                img += '<a href="' + previewBaseUrl + thumb.id + '?b=' 
                        + (thumb.width > thumb.height ? thumb.width : thumb.height) 
                        + '" data-size="' + thumb.width + 'x' + thumb.height + '" data-alt="' +  thumb.alt + '" data-caption="' + thumb.caption + '">';
                //img += '<span><img src="' + thumbnailBaseUrl + thumb.id + '" onload="thumbnails()" /></span>';
                img += '<i class="fa fa-picture-o fa-2x"></i>';
                img += '</a>';
                img += '</span>';
                imgs.push(img);
            });
            html += '<div class="glossary-image-gallery">' + imgs.join('') + '</div>';
        }
        
        if (inline) {
            $('.definition').remove();
            $('li .fa').removeClass('fa-caret-right').removeClass('fa-caret-bottom').addClass('fa-caret-right');
            $('<div/>', {
                'class': 'definition',
                'html': html
            }).appendTo(container);
            container.children('.fa').removeClass('fa-caret-right').addClass('fa-caret-down');
            if (!item.definition) {
                container.find('.glossary-relationships').eq(0).css('margin-top', '0');
            }
            
            if (container.offset().top > $(window).height()) {
                $('html, body').animate({
                    scrollTop: container.offset().top
                }, 1000);
            }
        }
        else {
            $('#definition').html(html);
        }
        
        if ($('.edit-glossary-term').length) {
            var ref = $('.edit-glossary-term').attr('href');
            ref = ref.substr(0, ref.lastIndexOf('/')+1) + item.termID;
            $('.edit-glossary-term').attr('href', ref);
        }
        swipe();
    });
    
    $('#term-list li').each(function() {
        $(this).removeClass('active');
        href = $(this).children('a').eq(0).attr('href');
        
        if (href.substr(1) === term) {
            $(this).addClass('active');
        }
    });
};

var facets = function() {
    $('<span/>', {'class':"glyphicon glyphicon-triangle-right"}).appendTo('.query h3, .facets h3');
        
    $('.facets h4').each(function() {
        $(this).prepend('<span class="glyphicon glyphicon-triangle-bottom"></span>').next('ul');
        $(this).css('cursor', 'default');
    });
    
    expandCollapseFacets();
    $(window).on('resize', function() {
        expandCollapseFacets();
    });
    
    $('.query, .facets').on('click', 'h3', function( event ) {
        var type = $(event.target).parents('.query, .facets').eq(0).attr('class');
        if ($('.' + type + ' .content').is(':visible')) {
            collapseFacets(type);
        }
        else {
            expandFacets(type);
        }
    });
    
    $('.facets').on('click', 'h4', function( event ){
        if ($(event.target).children('span').hasClass('glyphicon-triangle-right')) {
            $(event.target).nextAll().show();
            $(event.target).parent().children().show();
            $(event.target).children('span').removeClass('glyphicon-triangle-right').addClass('glyphicon-triangle-bottom');
        }
        else {
            $(event.target).nextAll().hide();
            $(event.target).children('span').removeClass('glyphicon-triangle-bottom').addClass('glyphicon-triangle-right');
        }
    });
    
    $('.facets').on('click', 'h4>span.glyphicon', function( event ){
        if ($(event.target).hasClass('glyphicon-triangle-right')) {
            $(event.target).parent('h4').next('ul').show();
            $(event.target).parent().parent().children().show();
            $(event.target).removeClass('glyphicon-triangle-right').addClass('glyphicon-triangle-bottom')
        }
        else {
            $(event.target).parent('h4').nextAll().hide();
            $(event.target).parents('.facet').eq(0).find('.facet-footer').hide();
            $(event.target).removeClass('glyphicon-triangle-bottom').addClass('glyphicon-triangle-right')
        }
    });
    
    $('.facets').on('change', 'input:checkbox', function( event ) {
        var containingDiv = $(event.target).parents('.facet').eq(0).clone();
        containingDiv.find('input:hidden').remove();
        var name = containingDiv.attr('data-vicflora-facet-name');
        var checked = $(event.target).parents('.facet').eq(0).find('input:checkbox:checked');
        var unchecked = $(event.target).parents('.facet').eq(0).find('input:checkbox:not(:checked)');
        
        var blanksChecked = checked.filter(':not([value]), [value=]').length;
        
        if (checked.length > 0) {
            
            if (blanksChecked) {
                name = '-' + name;
                if (checked.length === 1) {
                    str = '*';
                }
                else {
                    var arr = [];
                    unchecked.each(function() {
                        var val = $(this).val();
                        if (val.indexOf(' ')>0) {
                            val = '"' + val + '"';
                        }
                        val = val.replace('[', '\\[');
                        val = val.replace(']', '\\]');
                        arr.push(val);
                    });
                    var str = arr.join(' OR ');
                    if (arr.length > 1) {
                        str = '(' + str + ')';
                    }
                }
            }
            else {
                var arr = [];
                checked.each(function() {
                    var val = $(this).val();
                    if (val.indexOf(' ')>0) {
                        val = '"' + val + '"';
                    }
                    val = val.replace('[', '\\[');
                    val = val.replace(']', '\\]');
                    arr.push(val);
                });
                var str = arr.join(' OR ');
                if (arr.length > 1) {
                    str = '(' + str + ')';
                }
            }
            
            $(event.target).parents('.facet').eq(0).find(':hidden').remove();
            $(event.target).parents('.facet').eq(0)
                    .append('<input type="hidden" name="' + name + '" value="' + encodeURI(str) + '"/>');
        }
        
        
        
        
        var icon = $(event.target).parents('.facet').eq(0).find('.apply-filter');
        if (icon.length === 0) {
            $(event.target).parents('.facet').eq(0).children('h4')
                    .append('<a class="apply-filter btn btn-primary" href="#">Apply</a>');
        }
        if (checked.length === 0) {
            $(event.target).parents('.facet').eq(0).find('.apply-filter').remove();
        }
    });
    
    $('.facets').on('click', '.apply-filter', function( event ) {
        event.preventDefault();
        search();
    });
    
    $('#excludeHigherTaxa').change(function(e) {
        search();
    });
    
    $('.facets, .query').on('click', 'a[href^=http]', function( event ) {
        event.preventDefault();
        var href = $(this).attr('href');
        if ($('#excludeHigherTaxa').prop('checked') && href.indexOf('fq=end_or_higher_taxon%3Aend') === -1) {
            href += '&fq=end_or_higher_taxon%3Aend';
        }
        location.href = href;
    });
    
    expandCollapseFacetField();
};

var expandCollapseFacets = function() {
    var viewportWidth = $(window).width();
    if (viewportWidth >= 992) {
        if ($('.facets .content').is(':hidden')) {
            expandFacets('facets');
        }
        if ($('.query .content').is(':hidden')) {
            expandFacets('query');
        }
    }
    else {
        if ($('.facets .content').is(':visible')) {
            collapseFacets('facets');
        }
        if ($('.query .content').is(':visible')) {
            collapseFacets('query');
        }
    }
};

var collapseFacets = function(type) {
    $('.' + type + ' .content').hide();
    $('.' + type + ' h3>span').removeClass('glyphicon-triangle-bottom').addClass('glyphicon-triangle-right');
};

var expandFacets = function(type) {
    $('.' + type + ' .content').show();
    $('.' + type + ' h3>span').removeClass('glyphicon-triangle-right').addClass('glyphicon-triangle-bottom');
};
    
/**
 * expandCollapseFacets function
 * 
 * Collapse and expand collapsible facets
 * 
 * @returns {undefined}
 */

var expandCollapseFacetField = function() {
    $('.facets .collapsible').each(function() {
        var items = $(this).find('li');
        if (items.length > 3) {
            $(this).find('li').eq(2).nextAll().hide();
            $(this).children('ul').after('<div class="facet-footer"><a class="more" href="">More</a></div>');
        }
        
        if ($(this).find('h4 span').eq(0).hasClass('glyphicon-triangle-right')) {
            $(this).find('.facet-footer').eq(0).hide();
        }
    });
    
    $('.facets').on('click', 'a.more', function( event ) {
        event.preventDefault();
        $(event.target).parent('div').prev('ul').children('li').show();
        $(event.target).removeClass('more').addClass('fewer').html('Fewer');
    });
    
    $('.facets').on('click', 'a.fewer', function( event ) {
        event.preventDefault();
        $(event.target).parent('div').prev('ul').children('li').eq(2).nextAll().hide();
        $(event.target).removeClass('fewer').addClass('more').html('More');
    });
};

/**
 * topMenu function
 * 
 * Create top-menu dropdown for small and medium screens
 * 
 * @returns {undefined}
 */

var topMenu = function() {
    var viewportWidth = $(window).width();
    var listLength = $('#navbar .navbar-nav>li').length;
    if (viewportWidth >= 768 && viewportWidth < 1200 && listLength > 5) {
        $('#navbar .navbar-nav>li.dropdown>ul').prepend($('#navbar .navbar-nav>li:not(:last-child):gt(3)'));
        $('#navbar .navbar-nav>li.dropdown>a').html('More <span class="caret"></span>');
    }
    if ((viewportWidth < 768 || viewportWidth >= 1200) && listLength === 5) {
        $('#navbar .navbar-nav>li>ul>li:lt(3)').insertBefore($('.navbar li.dropdown'));
        $('#navbar .navbar-nav>li.dropdown>a').html('Help <span class="caret"></span>');
    }
};

function search() {
    var q = 'q=*:*';
    if ($('input[name=q]').eq(1).val()) {
        q = 'q=' + $('input[name=q]').eq(1).val();
    }
    qstring = q;

    var filters = $('.facets input[type=hidden]');
    if (filters.length > 0) {
        var arr = [];
        filters.each(function() {
            var name = $(this).attr('name');
            var val = $(this).val();
            arr.push('fq=' + encodeURI(name + ':' + decodeURI(val)));
        });
        var fq = arr.join('&');
        qstring += '&' + fq;
    }

    if ($('#excludeHigherTaxa').prop('checked')) {
        qstring += '&fq=end_or_higher_taxon:end';
    }

    location.href = base_url + '/flora/search?' + qstring;
}

function symbols() {
    $('.symbols span').hide();
    if ($('#symbol').val()!=='0') {
        var symbol = $('#symbol').val();
        $('.symbols').show();
        $('.name-entry').each(function() {
            if ($(this).find('.name').eq(0).hasClass('thirteen')) {
                $(this).find('.name').eq(0).removeClass('thirteen').addClass('twelve');
            }
            if ($(this).find('.name').eq(0).hasClass('sixteen')) {
                $(this).find('.name').eq(0).removeClass('sixteen').addClass('fifteen');
            }
            $(this).find('.name').eq(0).removeClass('alpha');
            
            if ($(this).find('.accepted-name').eq(0).hasClass('sixteen')) {
                $(this).find('.accepted-name').eq(0).removeClass('sixteen').addClass('fifteen').addClass('offset-by-one');
            }
            if ($(this).find('.symbols span.' + symbol).length > 0) {
                $(this).find('.symbols span.' + symbol).eq(0).css('display', 'inline-block');
            }
            else {
                $(this).find('.symbols span.empty').eq(0).css('display', 'inline-block');
            }
        });
    }
    else {
        $('.symbols').hide();
        $('.name-entry').each(function() {
            if ($(this).find('.name').eq(0).hasClass('twelve')) {
                $(this).find('.name').eq(0).removeClass('twelve').addClass('thirteen');
            }
            if ($(this).find('.name').eq(0).hasClass('fifteen')) {
                $(this).find('.name').eq(0).removeClass('fifteen').addClass('sixteen');
            }
            $(this).find('.name').eq(0).removeClass('alpha').addClass('alpha');
            if ($(this).find('.accepted-name').eq(0).hasClass('fifteen')) {
                $(this).find('.accepted-name').eq(0).removeClass('fifteen').addClass('sixteen').removeClass('offset-by-one');
            }
        });
    }
}

var thumbnails = function() {
    var thumbnailWidth = $('.thumbnail').width();
    $('.thumbnail').css('height', (thumbnailWidth + 10) + 'px');
    $('.thumb span').width(thumbnailWidth);
    $('.thumb span').height(thumbnailWidth);

    $('.thumbnail img').each(function() {
        var imgWidth = $(this).width();
        var imgHeight = $(this).height();

        if (imgWidth > thumbnailWidth || imgHeight > thumbnailWidth) {
            if (imgWidth > imgHeight) {
                var displayHeight = imgHeight * (thumbnailWidth / imgWidth);
                var displayWidth = thumbnailWidth;
            }
            else {
                var displayWidth = imgWidth * (thumbnailWidth / imgHeight);
                var displayHeight = thumbnailWidth;
            }
            $(this).css({'width': displayWidth + 'px', 'height': displayHeight + 'px'});
        }
    });
};

/*
(function( $ ) {
    var methods = {
        facet: function( name ) {
            var base_url = location.href.substr(0, location.href.indexOf('vicflora_dev')+12);
            var query_string = location.href.substr(location.href.indexOf('?') + 1);
            url = base_url + '/ajax/facet/' + name + '?' + query_string;
            result = $.getJSON(url, function(data) {
                
                var html = '<div class="facet" data-vicflora-facet-name="'+ data.name + '">';
                html += '<h4>' + data.title + '</h4>';
                html += '<ul>';
                $.each(data.items, function(index, item) {
                    html += '<li>';
                    html += '<input type="checkbox" id="' + data.name + '_' + item.name + '" ';
                    html += 'value="' + item.name + '" ';
                    if (item.checked) {
                        html += 'checked="checked" ';
                    }
                    html += '/>';
                    html += '<label for="' + data.name + '_' + item.name +'">' + item.label + ' (' + item.count + ')</label>';
                    html += '</li>';
                });
                
                html += '</ul>';
                html += '</div>';
                

                $('.facets').append(html);
            });

            $('.facets').on('change', 'input:checkbox', function( event ) {
                //alert ('changed: ' + event.target.nodeName);
                var containingDiv = $(event.target).parent('li').parent('ul').parent('.facet');
                containingDiv.find('input:hidden').remove();
                var checkedboxes = containingDiv.find('input:checkbox:checked');
                if (checkedboxes.length > 0) {
                    var arr = [];
                    checkedboxes.each(function() {
                        arr.push($(this).val());
                    });
                    var str = arr.join();

                    var name = containingDiv.attr('data-vicflora-facet-name');

                    containingDiv.append('<input type="hidden" name="' + name + '" value="' + str + '"/>');
                }
                $('form').submit();
            });
        }
    };
  
    $.fn.Facets = function(method) {
        if ( methods[method] ) {
            return methods[ method ].apply( this, Array.prototype.slice.call( arguments, 1 ));
        }
        else if ( typeof method === 'object' || ! method ) {
            return methods.init.apply( this, arguments );
        }
        else {
            $.error( 'Method ' +  method + ' does not exist on jQuery.tooltip' );
        }    
    }
  
})( jQuery );
*/