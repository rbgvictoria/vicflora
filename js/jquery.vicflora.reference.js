/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */


$(function() {
    
    $('#reference-search').autocomplete({
        source: base_url + '/reference/reference_lookup_autocomplete',
        minLength: 2,
        focus: function( event, ui ) {
            $( "#reference-search" ).val( ui.item.label );
            return false;
        },
        select: function( event, ui ) {
            $( "#reference-id" ).val( ui.item.value );
            if ($('#reference-list').length) {
                $( "#reference-search" ).val( ui.item.label );
                $( "#reference-list" ).html("<p><b>" + ui.item.label + '</b> <a href="' + 
                        base_url + '/reference/show/' + ui.item.value + 
                        '" title="Show"><i class="fa fa-search"></i></a><br/>' + 
                        ui.item.description + "</p>");
            }
            else {
                createTaxonReference(ui.item.value);
            }
            return false;
        }
    })
    .autocomplete("instance")._renderItem = function(ul, item) {
        ul.addClass('vicflora-reference-lookup-autocomplete-list');
        return $( "<li>" )
            .append( "<a><b>" + item.label + "</b><br>" + item.description + "</a>" )
            .appendTo( ul );
    };
    
    $('#source-search').autocomplete({
        source: base_url + '/reference/reference_lookup_autocomplete',
        minLength: 2,
        focus: function( event, ui ) {
            $( "#source-search" ).val( ui.item.label );
            return false;
        },
        select: function( event, ui ) {
            $( "#source-id" ).val( ui.item.value );
            $( "#source-search" ).val( ui.item.label );
            $( "#source-desc" ).html("<p><b>" + ui.item.label + '</b> <a href="' + 
                    base_url + '/reference/show/' + ui.item.value + 
                    '" title="Show"><i class="fa fa-search"></i></a><br/>' + 
                    ui.item.description + "</p>");
            return false;
        }
    }).autocomplete("instance")._renderItem = function(ul, item) {
        ul.addClass('vicflora-reference-lookup-autocomplete-list');
        return $( "<li>" )
            .append( "<a><b>" + item.label + "</b><br>" + item.description + "</a>" )
            .appendTo( ul );
    };
    
    $('#reference-first-letter a').click(function(e) {
        e.preventDefault();
        var url = $(this).attr('href');
        $.ajax({
            url: url,
            success: function(data) {
                items = [];
                $.each(data, function(index, item) {
                    var html = "<p><b>" + item.label + '</b> <a href="' + base_url + '/reference/show/' + item.value + '" title="Show"><i class="fa fa-search"></i></a><br/>' + 
                            item.description + "</p>";
                    items.push(html);
                });
                if (items.length > 0) {
                    $('#reference-list').html(items.join(''));
                }
                else {
                    $('#reference-list').html('No references found...');
                }
            }
        });
    });
    
    $('#taxon-references').on('click', 'a.delete-taxon-reference', function(e) {
        e.preventDefault();
        var reference = $(this).parent('p');
        var url = $(this).attr('href');
        var reg = /([0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12})/;
        var data = {
            reference_id: $(this).attr('data-vicflora-reference-id'),
            taxon_id: reg.exec(location.href)[1]
        };
        $.ajax({
            url: url,
            data: data,
            method: 'POST',
            success: function(data) {
                console.log(data);
                if (data) {
                    reference.remove();
                }
            }
        });
    });
    
    var createTaxonReference = function(id) {
        var reg = /([0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12})/;
        var data = {
            reference_id: id,
            taxon_id: reg.exec(location.href)[1]
        };
        console.log(data);
        $.ajax({
            url: base_url + '/reference/create_taxon_reference_ajax/',
            method: 'POST',
            data: data,
            success: function(item) {
                var html = "<p><b>" + item.label + '</b> <a href="' + base_url + 
                        '/reference/show/' + item.value + '" title="Show"><i class="fa fa-search"></i></a> ' + 
                        '<a class="delete-taxon-reference" href="' + base_url + '/reference/delete_taxon_reference_ajax/" data-vicflora-reference-id="' + item.value + '"><i class="fa fa-trash"></i></a>' + ' <br/>' +
                        item.description + "</p>";
                $('#taxon-references').append(html);
                $('#reference-search').val('');
            },
            error: function(jqXHR, textStatus, errorThrown) {
                console.log(jqXHR);
                console.log(textStatus);
                console.log(errorThrown);
            }
        });
    };
    
    
});