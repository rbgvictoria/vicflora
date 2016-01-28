var base_url = location.href.substr(0, location.href.indexOf('/', location.href.indexOf('vicflora')));
var query_string = location.href.substr(location.href.indexOf('?') + 1);
var qstring;

$(function() {
    var query_string = location.href.substr(location.href.indexOf('?') + 1);
    
    if (query_string.indexOf('fq=end_or_higher_taxon%3Aend') !== -1) {
        $('#excludeHigherTaxa').prop('checked', true);
    }
    
    // auto-complete for search
    $('#term, input[name=q]').autocomplete({
        source: base_url + '/autocomplete/autocompleteName',
        minLength: 2
    });
    
    // buttons
    //$('input[type=submit], button').button();
    
    // submit search form button
    $('button.submit').click(function() {
        event.preventDefault();
        //$('form').submit();
        var q = 'q=*:*';
        if ($('#search input[name=q]').val()) {
            var q = 'q=' + $('#search input[name=q]').val();
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


    /*
     * 
     */
    
    $('<span/>', {'class':"glyphicon glyphicon-triangle-bottom"}).appendTo('.query h3, .facets h3');
        
    /*
     * 
     */
    $('.facets h4').each(function() {
        $(this).prepend('<span class="glyphicon glyphicon-triangle-bottom"></span>').next('ul');
        $(this).css('cursor', 'default');
    });
    
    $('.query, .facets').on('click', 'h3', function( event ) {
        if ($(event.target).find('span').eq(0).hasClass('glyphicon-triangle-right')) {
            $(event.target).nextAll('.content').show();
            $(event.target).find('span').removeClass('glyphicon-triangle-right').addClass('glyphicon-triangle-bottom');
        }
        else {
            $(event.target).nextAll('.content').hide();
            $(event.target).find('span').removeClass('glyphicon-triangle-bottom').addClass('glyphicon-triangle-right');
        }
    });
    
    $('.query, .facets').on('click', 'h3 span.glyphicon', function( event ) {
        if ($(event.target).hasClass('glyphicon-triangle-right')) {
            $(event.target).parent().nextAll('.content').show();
            $(event.target).removeClass('glyphicon-triangle-right').addClass('glyphicon-triangle-bottom');
        }
        else {
            $(event.target).parent().nextAll('.content').hide();
            $(event.target).removeClass('glyphicon-triangle-bottom').addClass('glyphicon-triangle-right');
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
        var containingDiv = $(event.target).parent('li').parent('ul').parent('.facet');
        if (containingDiv.length == 0) {
            containingDiv = $(event.target).parent('li').parent('ul').parent('li').parent('ul').parent('.facet');
        }
        containingDiv.find('input:hidden').remove();
        var checkedboxes = containingDiv.find('input:checkbox:checked');
        if (checkedboxes.length > 0) {
            var arr = [];
            checkedboxes.each(function() {
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

            var name = containingDiv.attr('data-vicflora-facet-name');

            containingDiv.append('<input type="hidden" name="' + name + '" value="' + encodeURI(str) + '"/>');
        }
        
        var icon = containingDiv.find('.apply-filter');
        if (icon.length == 0) {
            containingDiv.children('h4').append('<a class="apply-filter" href="#">Apply</a>');
        }
    });
    
    $('.facets').on('click', '.apply-filter', function( event ) {
        event.preventDefault();
        //$('form').submit();
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

    /*$('.extended-query').on('change', 'input:radio', function( event ) {
        $('form').submit();
    });*/
    
    /*
     * Collapse and expand collapsible facets
     */
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
    
    
    //$('a.more').button();
    
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
    
    
    /*
     * GLOSSARY
     * 
     */
    
    var reg = /flora\/glossary/;
    if (reg.test(location.href)) {
        var hash = location.hash;
        if (!hash) {
            hash = '#a';
        }
        
        var term = hash.substr(1).toLowerCase();
        getGlossaryTerms(term);
        
    }
    
    $('#glossary-first-letter').on('click', 'a', function(e) {
        e.preventDefault();
        var href = $(this).attr('href');
        var first = href.substr(href.indexOf('#') + 1);
        getGlossaryTerms(first);
    });
    
    $('#term-list').on('click', 'a', function(e) {
        e.preventDefault();
        var term = $(this).attr('href').substr(1);
        getGlossaryDefinition(term);
    });
    
    $('#definition').on('click', 'a.glossary-link', function(e) {
        e.preventDefault();
        var term = $(this).attr('href').substr(1);
        getGlossaryTerms(term);
    });
    
    $('#definition').on('click', '.thumb', function(e) {
        e.preventDefault();
        var href = $(this).attr('href');
        $.colorbox({
            href: href,
            rel: "thumbnail",
            //opacity: 0.40, 
            //transition: 'none', 
            speed: 0,
            width: "95%",
            height: '95%'
        });
    });

    
    /*
     * Thumbnails
     */
    $('a[data-toggle=tab][aria-controls=images]').on('shown.bs.tab', function(e) {
        thumbnails();
    });
    
    /*
     * Hero image
     */
    var divWidth = $('.profile-rigth-pane').width();
    var imgWidth = $('.hero-image img').attr('width');
    var imgHeight = $('.hero-image img').attr('height');
    $('.hero-image>div').css('width', (divWidth) + 'px');
    if (imgWidth > divWidth - 10 || imgHeight > divWidth - 10) {
        if (imgWidth > imgHeight) {
            var newHeight = imgHeight * ((divWidth - 10) / imgWidth);
            var newWidth = divWidth - 10;
        }
        else {
            var newWidth = imgWidth * ((divWidth-10) / imgHeight);
            var newHeight = divWidth - 10;
        }
        $('.hero-image img').css({'width': newWidth + 'px', 'height': newHeight + 'px'});
    }
    
    $('.hero-image img').click(function(e) {
        $('[href=#tab-images]').tab('show');
    });
    
    var mapWidth = $('.profile-map img').attr('width');
    var mapHeight = $('.profile-map img').attr('height');
    if (mapWidth > divWidth - 2) {
        if (mapWidth > mapHeight) {
            var newHeight = (mapHeight * (divWidth / mapWidth)) - 2;
            var newWidth = divWidth - 2;
        }
        else {
            var newWidth = (mapWidth * (divWidth / mapHeight)) - 2;
            var newHeight = divWidth - 2;
        }
        $('.profile-map img').css({'width': newWidth + 'px', 'height': newHeight + 'px'});
    }
    
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
        base_url = location.href.substr(0, location.href.indexOf('/', location.href.indexOf('vicflora')));
        $.getJSON(base_url + '/flora/svg_map/' + guid + '/avh_distribution/img/' + svgWidth, function (item) {
            $('#svg-avhdistribution').html('<img src="' + item.src + '" alt="' + item.alt + '" usemap="#vicflora_bioregion" />');
        });
        $.ajax({
            url: base_url + '/flora/imageMap/' + svgWidth,
            dataType: 'html',
            success: function(data) {
                $('#svg-avhdistribution').append(data);
            }
        });
        
        $.getJSON(base_url + '/ajax/bioregion_legend/' + guid + '/establishment_means', function(data) {
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
        
        
        /*$('.bioregion-toggle').on('click', 'a', function(e) {
            e.preventDefault();
            if ($(this).parent().hasClass('bioregion')) {
                if ( Modernizr.svg ) {
                    $('#svg-bioregions').load(base_url + '/flora/svg_map/' + guid + '/bioregions/svg/bioregion');
                }
                else {
                    $.getJSON(base_url + '/flora/svg_map/' + guid + '/bioregions/img/bioregion', function (item) {
                        $('#svg-bioregions').html('<img src="' + item.src + '" alt="' + item.alt + '" />');
                    });
                }
                $(this).parent().removeClass('bioregion').addClass('establishment-means').html('<a href="">Colour by establishment means</a>');
                $.getJSON(base_url + '/ajax/bioregion_legend/' + guid + '/bioregion', function(data) {
                    var items = [];
                    var headerrow = '<tr>';
                    headerrow += '<th>&nbsp;</th>';
                    headerrow += '<th>Bioregion</th>';
                    headerrow += '<th>Occurrence status</th>';
                    headerrow += '<th>Establishment means</th>';
                    headerrow += '</tr>';
                    items.push(headerrow);

                    $.each(data, function(index, item) {
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
            }
            else {
                if ( Modernizr.svg ) {
                    $('#svg-bioregions').load(base_url + '/flora/svg_map/' + guid + '/bioregions/svg/establishment_means');
                }
                else {
                    $.getJSON(base_url + '/flora/svg_map/' + guid + '/bioregions/img/establishment_means', function (item) {
                        $('#svg-bioregions').html('<img src="' + item.src + '" alt="' + item.alt + '" />');
                    });
                }
                $(this).parent().removeClass('establishment_means').addClass('bioregion').html('<a href="">Colour by bioregion</a>');
                $.getJSON(base_url + '/ajax/bioregion_legend/' + guid + '/establishment_means', function(data) {
                    var items = [];
                    var headerrow = '<tr>';
                    headerrow += '<th>&nbsp;</th>';
                    headerrow += '<th>Bioregion</th>';
                    headerrow += '<th>Occurrence status</th>';
                    headerrow += '<th>Establishment means</th>';
                    headerrow += '</tr>';
                    items.push(headerrow);

                    $.each(data, function(index, item) {
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
            }
        });*/
        
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
        base_url = location.href.substr(0, location.href.indexOf('/', location.href.indexOf('vicflora')));
        $('[name=vicflora_bioregion]').on('click', 'area', function(e) {
            e.preventDefault();
            var title = $(this).attr('title');
            $('#info').html('<h3>' + title + '</h3>');
            $.getJSON(base_url + '/ajax/bioregionInfo/' + title, function (item) {
                $('#info').append(item.Description);
                $('#info').append('<p><b>Source:</b> <a href="http://www.depi.vic.gov.au/environment-and-wildlife/biodiversity/evc-benchmarks#' + 
                        item.DepiCode.toLowerCase() + '" target="_blank">http://www.depi.vic.gov.au/environment-and-wildlife/biodiversity/evc-benchmarks#' +
                        item.DepiCode.toLowerCase() + '</a></p>');
                $('#info').append('<p>' + '<span class="btn btn-default"><a href="' + base_url + '/flora/bioregions/' + item.FovNaturalRegion.toLowerCase().replace(/ /g, '-') + '">More info.</a></span>'
                        + '<span class="btn btn-default"><a href="' + base_url + '/flora/search?q=*:*&fq=ibra_7_subregion%3A' + encodeURIComponent('"' + item.Name + '"') +
                        '">Find taxa in ' + item.Name + '</a></span></p>');
            });
        });
    }
    
    if (location.href.indexOf('/st/') > -1) {
        $('.sixteen.columns>h2').before('<div class="edit-static"><a href="' + location.href + '/_edit">Edit</a></div>');
    }
});

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

function getGlossaryTerms(term) {
    firstletter = term.substr(0,1).toLowerCase();
    var url = base_url + '/ajax/glossary_terms/' + firstletter;
    $.getJSON(url, function(data) {
        var items = [];
        $.each(data, function(index, item) {
            var option = '<li><a href="#' + encodeURIComponent(item.term) + '">' + item.term + '</a></li>';
            items.push(option);
        });
        $('#term-list').html('<ul>' + items.join('') + '</ul>');
        
        $('#glossary-first-letter a').each(function() {
            $(this).removeClass('active');
            var href = $(this).attr('href');
            if (href.toLowerCase().indexOf('#' + firstletter) !== -1) {
                $(this).addClass('active');
            }
        });
        
        getGlossaryDefinition(encodeURIComponent((term.length > 1) ? term : data[0].term));
        
        $('#term-list ul').css("height", $(window).height()-$('footer').height()-$('#term-list').offset().top-40 + 'px');
        
        $('#glossary-terms').css("height", $('footer').offset().top-$('#glossary-terms').offset().top)+'px';
    });
}

function getGlossaryDefinition(term) {
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
        var html = '<h2>' + item.term + '</h2>';
        if (item.definition) {
            html += '<p>' + item.definition + '</p>';
        }
        var rels = [];
        if (item.relationships.length) {
            $.each(item.relationships, function(index, rel) {
                var relationship = '<div class="row">';
                relationship += '<div class="col-md-6 col-lg-5"><span class="glossary-rel-type">' + relTypes[rel.relationshipType] + '</span></div>';
                relationship += '<div class="col-md-4 col-lg-3"><a href="#' + rel.relatedTerm + '" class="glossary-link">' + rel.relatedTerm + '</a></div></div>';
                rels.push(relationship);
            })
            html += '<div class="glossary-relationships">' + rels.join('') + '</div>';
        }
        if (item.thumbnails.length) {
            var imgs = [];
            $.each(item.thumbnails, function(index, thumb) {
                var img = '<div class="col-xs-6 col-sm-4 col-md-3">';
                img += '<a href="' + thumb.imageUrl + '" class="thumbnail thumb">';
                if (index === 0) {
                    img += '<span><img src="' + thumb.thumbnailUrl + '" onload="thumbnails()"/></span>';
                }
                else {
                    img += '<span><img src="' + thumb.thumbnailUrl + '"/></span>';
                }
                
                img += '</a>';
                img += '</div>';
                imgs.push(img);
            });
            html += '<div class="row thumbnail-row">' + imgs.join('') + '</div>';
        }
        
        $('#definition').html(html);
        
        if ($('.edit-glossary-term').length) {
            var ref = $('.edit-glossary-term').attr('href');
            ref = ref.substr(0, ref.lastIndexOf('/')+1) + item.termID;
            console.log(ref);
            $('.edit-glossary-term').attr('href', ref);
        }
    });
    
    $('#term-list li').each(function() {
        $(this).removeClass('active');
        href = $(this).children('a').eq(0).attr('href');
        
        if (href.substr(1) === term) {
            $(this).addClass('active');
        }
    });
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