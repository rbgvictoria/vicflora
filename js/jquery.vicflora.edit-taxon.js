var base_url = location.href.substr(0, location.href.indexOf('/', location.href.indexOf('vicflora')));

$(function() {
    /*
     * Configuration for context menu to enable opening of the edit form from
     * the search and browse classification pages.
     */
    
    $('.name-entry .name').each(function() {
        var items = {};
        items.edit = {name: "Edit", callback: function (key, opt) {
                window.open($(this).attr('href').replace('flora/taxon', 'admin/edittaxon'), '_self');
            }};
        
        if ($(this).find('.add-child').length > 0) {
            items.addchild = {name: "Add child", callback: function (key, opt) {
                window,open($(this).attr('href').replace('flora/taxon', 'admin/addchild'), '_self');
            }};
        }
        
        $(this).contextMenu({
            selector: 'a',
            items: items
        });
        
    });
    
    /*
     * Make the sections on the form collapsible
     */
    $('.edit-form-section h4').each(function() {
        $(this).prepend('<span class="ui-icon ui-icon-triangle-1-s"></span>');
        $(this).css('cursor', 'default');
    });
    
    $('.edit-form-section').on('click', 'h4 .ui-icon', function( event ) {
        if ($(event.target).hasClass('ui-icon-triangle-1-e')) {
            $(event.target).parent('h4').nextAll().show();
            $(event.target).removeClass('ui-icon-triangle-1-e').addClass('ui-icon-triangle-1-s');
        }
        else {
            $(event.target).parent().nextAll().hide();
            $(event.target).removeClass('ui-icon-triangle-1-s').addClass('ui-icon-triangle-1-e');
        }
    });
    /*
     * Indicate on the form that the value of a field has changed from what is in the database
     */
    $('.edit-taxon-form').on('change', 'input, select, textarea', function( event ) {
        var name = $(event.target).attr('name');
        if (name !== undefined) {
            if (name.indexOf('[') > -1) {
                var old = $('input[name="' + name.replace('[', '_old[') + '"]');
            }
            else {
                var old = $('input[name=' + name + '_old]');
            }
            if (old.length) {
                $(event.target).next('.undo').remove();
                if ($(event.target).val() != old.val()) {
                    $(event.target).addClass('has-changed');
                }
                else {
                    $(event.target).removeClass('has-changed');
                }
            }
        }
    });
    
    
    /*
     * Change parent for genera and higher taxa
     */
    $('#change-parent').on('click', 'a', function( event ) {
        event.preventDefault();
        $(this).hide();
        $('#new_parent').show();
        var button = $('<div/>', {
            'class': 'col-md-2'
        }).append('<button class="btn btn-default">OK</button>');
        $('#change-parent').append(button);
    });
    
    $('#new_parent').autocomplete({
        source: base_url + '/autocomplete/autocomplete_parent/' + $('#taxon_tree_def_item_id').val(),
        minLength: 2
    });
    
    $('#change-parent').on('click', 'button', function(e) {
        e.preventDefault();
        parent();
    });
    
    /*
     * 
     */
    $('#assign_to_taxon').on('click', 'a', function( event ) {
        event.preventDefault();
        $(this).hide();
        $('#new_taxon').show();
        var button = $('<button />', {
            'html': 'OK'
        });
        button.insertBefore('#new_accepted_name');
    });
    
    $('#new_taxon').autocomplete({
        source: base_url + '/autocomplete/autocompleteAcceptedName',
        minLength: 2
    });
    
    $('#assign_to_taxon').on('click', 'button', function(event) {
        event.preventDefault();
        newTaxon();
    });

    
    /*
     * 
     */
    $('.edit-taxon-form').on('change', 'input[name=name], select[name=taxon_tree_def_item_id]', function( event ) {
        fullName();
    });
    
    /*
     * 
     */
    $('.edit-form-section').on('click', '#add_attribute', function( event ) {
        var html = $('<p />', {
            'class': 'clearfix'
        });
        
        var options = ['Naturalised status', 'VROT'];
        
        var already = [];
        
        $(this).parents('.edit-form-section').eq(0).find('label').each(function() {
            already.push($(this).text());
        });
        $(this).parents('.edit-form-section').eq(0).find('.taxon-attribute-select').each(function() {
            already.push($(this).val());
        });
        
        if (options.length > already.length) {
            var remaining = [];
            var select = $('<select />', {
                'name': 'taxon_attribute[' + already.length + ']',
                'id': 'taxon_attribute_' + already.length,
                'class': 'taxon-attribute-select three columns',
            });
            $.each(options, function(index, value) {
                if (already.indexOf(value)) {
                    remaining.push(value);
                    $('<option />', {
                        'value': value,
                        'text': value
                    }).appendTo(select);
                }
            });

            html.append(select);
            
            if (remaining.length === 1) {
                var strvalue = attrValueDropdown(remaining[0], already.length);
                html.append(strvalue);
            }

            $(this).parents('p').eq(0).before(html);
            
            if (remaining.length === 1) {
                $('#add_attribute').remove();
            }
        }
        
    });
    
    
    /*
     * New accepted name
     */
    
    // open the new accepted name dialog
    /*$('#new_accepted_name').on('click', function() {
        $('#new_accepted_name_dialog').dialog({
            height: 'auto',
            width: 'auto',
            buttons: [ 
                { text: "Ok", click: function() {
                        accepted_name();
                        $( this ).dialog( "close" ); 
                    } 
                } 
            ] ,
            appendTo: '#status'
        });
    });*/
    
    // auto-complete for new accepted name field
    $('#nn_name').autocomplete({
        source: base_url + '/autocomplete/autocompleteAcceptedName',
        minLength: 2,
        appendTo: '#status'
    });
    
    $('#acceptedNameModal').on('click', 'button#save-new-accepted-name', function(e) {
        accepted_name();
        $('#acceptedNameModal').modal('hide');
    });

    // undo accepted name change
    $('.edit-taxon-form').on('click', '.undo-accepted', function( event ) {
        undo_accepted_name_change();
    });
    
    
    /*
     * Profile
     */
    $('.profile-as').each(function() {
        $(this).prepend('<span class="ui-icon ui-icon-triangle-1-e"></span>');
        $(this).parent().children('.profile-text, .profile-source').hide();
    });
    
    $('.profile').on('click', '.ui-icon', function(event) {
        if ($(event.target).hasClass('ui-icon-triangle-1-e')) {
            $(event.target).parent().parent().children().show();
            $(event.target).removeClass('ui-icon-triangle-1-e').addClass('ui-icon-triangle-1-s');
        }
        else {
            $(event.target).parent().parent().children('.profile-text, .profile-source').hide();
            $(event.target).removeClass('ui-icon-triangle-1-s').addClass('ui-icon-triangle-1-e');
        }
    });
    
    $('.profile-editor textarea').ckeditor();
    

    /* 
     * Common names 
     */
    $('.new-common-name-row').on('click', 'a', function(e) {
        e.preventDefault();
        var n = $('.common-names tbody tr').length;
        var html = '<td><input type="text" name="common_name[' + n + ']"/></td>' +
                '<td><input type="checkbox" name="preferred[' + n + ']" value="1"/></td>' +
                '<td><input type="text" name="usage[' + n + ']"/></td>' +
                '<td><input type="checkbox" name="delete[' + n + ']" value="1"/></td>';
        $('<tr />', {
            'html': html
        }).appendTo('.common-names tbody');
    });
    
    $('.common-names').on('change', '[name^=preferred]', function(e) {
        if ($(e.target).prop('checked') === true) {
            $('.common-names [name^=preferred]').removeAttr('checked');
            $(e.target).prop('checked', true);
        }
    });
    
    /*
     * APNI match
     */
    $('.apni-name-match').on('click', '[name^=apni_match_verified]', function(e) {
        //if ($(e.target).prop('checked') === true) {
            $('.apni-name-match [name^=apni_match_verified]').prop('checked', false);
            $('.apni-name-match [name^=apni_delete]').prop('checked', true);
            $(e.target).parents('tr').eq(0).find('[name^=apni_delete]').eq(0).prop('checked', false);
            
            var rindex = $(e.target).prop('name').substr($(e.target).prop('name')+1).replace(']', '');
            
            var newmatchtype;
            if ($(e.target).parents('tr').eq(0).children('td').eq(1).text().trim() === $('#full_name_display').val() + " " + $('[name=author]').val()) {
                newmatchtype = 'FullNameWithAuthors';
            }
            else {
                newmatchtype = 'FullName';
            }
            
            var html = $('<input />', {
                "name": 'apni_match_type[' + rindex + ']',
                "value": newmatchtype
            });
            $(e.target).parents('tr').eq(0).children('td').eq(2).html(html);
                    
        //}
        $(e.target).prop('checked', true);
    });
    
    $('.apni-manual').click(function() {
        if ($('.apni-name-match [name=apni_no]').length === 0) {
            var numrows = $('.apni-name-match tbody tr').length;

            var cells = '<td><input type="text" name="apni_no" style="width:60px;"/></td>';
            cells += '<td>&nbsp;</td>';
            cells += '<td>&nbsp;</td>';
            cells += '<td><input type="checkbox" name="apni_match_verified[' + numrows + ']" value="1"/></td>';
            cells += '<td><input type="checkbox" name="apni_delete[' + numrows + ']" value="1"/></td>';

            $('<tr />', {
                "html": cells
            }).appendTo('.apni-name-match tbody');
        }
    });
    
    $('.apni-name-match').on('change', '[name=apni_no]', function(e) {
        var url = base_url + '/wscurl/search_nsl_name_id/' + encodeURIComponent($(e.target).val());
        $.getJSON(url, function(data) {
            var apni = data[0];
            var apni_name_field = $('<input />' , {
                "name": "apni_fullnamewithauthors",
                "value": apni.dcterms_title,
                "style": "width:99%"
            });
            $(e.target).parents('tr').eq(0).children('td').eq(1).html(apni_name_field);
            
            var rindex = $('[name=apni_no]').parents('tr').eq(0).find('[name^=apni_match_verified]')
                    .eq(0).prop('name').substr($('[name=apni_no]').parents('tr').eq(0)
                    .find('[name^=apni_match_verified]').eq(0).prop('name').indexOf('[')+1).replace(']','');
            
            var newmatchtype;
            if (apni.dcterms_title === $('#full_name_display').val() + " " + $('[name=author]').val()) {
                newmatchtype = 'FullNameWithAuthors';
            }
            else {
                newmatchtype = 'FullName';
            }
            var apni_match_type_field = $('<input />' , {
                "name": 'apni_match_type[' + rindex + ']',
                "value": newmatchtype
            });
            $(e.target).parents('tr').eq(0).children('td').eq(2).html(apni_match_type_field);
            
            $('.apni-name-match [name^=apni_match_verified]').prop('checked', false);
            $(e.target).parents('tr').eq(0).find('[name^=apni_match_verified]').prop('checked', true);
            
            $('.apni-name-match [name^=apni_delete]').prop('checked', true);
            $(e.target).parents('tr').eq(0).find('[name^=apni_delete]').prop('checked', false);
            
            
        });
    });
    
    $('.edit-form-section').on('click', '.find-in-apni', function() {
        var img = '<div class="loading"><img src="' + base_url + '/css/images/ajax-loader.gif" alt="Loading..." height="16" width="16" /></div>';
        $('#find-in-apni-dialog').dialog({
            height: 'auto',
            width: 'auto',
            buttons: [ 
                { text: "Ok", click: function() {
                        if ($('[name^=apni_accept]:checked').length > 0) {
                            get_apni_name();
                        }
                        $('#find-in-apni-dialog').html(img);
                        $( this ).dialog( "close" );
                    } 
                } 
            ] ,
            appendTo: '#status'
        });
        
        $('#find-in-apni-dialog').html(img);
        
        var url = base_url + '/wscurl/search_nsl_name/' + encodeURI($('#full_name_display').val());
        $.getJSON(url, function(data) {
            var items = [];
            
            $.each(data, function(index, item) {
                
                if (Object.getOwnPropertyNames(item).length > 0) {
                    if (item.dataset === 'APNI') {
                        var row = '';
                        row += '<tr>';
                        row += '<td><a href="https://www.anbg.gov.au/cgi-bin/apni?taxon_id=' + item.uri.substr(item.uri.lastIndexOf('/') + 1) + '" target="_blank">' + item.uri.substr(item.uri.lastIndexOf('/') + 1) + '</a></td>';
                        row += '<td>' + item.dcterms_title + '</td>';
                        row += '<td><input type="checkbox" name="apni_accept[' + item.uri.substr(item.uri.lastIndexOf('/') + 1) + ']" /></td>';
                        row += '</tr>';
                        items.push(row);
                    }
                }
            });
            
            var html = '';
            if (items.length > 0) {
                html = '<table class="apni-name-match"><thead><tr><th>APNI no.</th><th>Full name with authors</th><th>Accept</th></tr></thead><tbody>';
                for (i = 0; i < items.length; i++) {
                    html += items[i];
                }
                html += '</tbody></table>';
            }
            else {
                html = '<h3>No matches were found</h3>';
            }
            $('#find-in-apni-dialog').html(html);
        });
    });
    
    $('#find-in-apni-dialog').on('change', '[name^=apni_accept]', function(e) {
        if ($(e.target).prop('checked') === true) {
            $('[name^=apni_accept]').prop('checked', false);
            $(e.target).prop('checked', true);
        }
    });
    
    /*
     * Edit distribution
     */
    var reg = /editdistribution\/([0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12})/;
    if (reg.test(location.href)) {
        var guid = reg.exec(location.href)[1];
        var url = base_url + '/ajax/ibra_map/' + guid + '/svg/establishment_means';
        $('#svg-editbioregions').load(base_url + '/ajax/ibra_map/' + guid + '/svg/establishment_means');
    };
    
    /*
     * WYSIWYG editor for taxon remarks
     */
    //$('#taxon_remarks').ckeditor();
    if ($('#taxon_remarks').length) {
        CKEDITOR.replace("taxon_remarks", {
            customConfig: base_url + '/js/ckeditor.taxon_remarks.config.js'
        });
    }
    
    /*
     * WYSIWYG editor for glossary definition
     */
    if ($('#term_definition').length) {
        console.log(base_url);
        CKEDITOR.replace("term_definition", {
            customConfig: base_url + '/js/ckeditor.glossary.config.js'
        });
    }
    
    $('[name^=related_term]').autocomplete({
        source: base_url + '/autocomplete/autocomplete_glossary_term',
        minLength: 2
    });
    
    /*$( "[name^=related_term]" ).on( "autocompletechange", function( event, ui ) {
        var parent_row = $(this).parents('tr').eq(0);
        if (parent_row.find('[name^=related_term]').eq(0).val() && parent_row.find('[name^=rel_type]').eq(0).val()) {
            addGlossaryTermRelationship();
        }
    } );
    $('#glossary_relationships').on('change', '[name^=rel_type]', function(e) {
        var parent_row = $(this).parents('tr').eq(0);
        if (parent_row.find('[name^=related_term]').eq(0).val() && parent_row.find('[name^=rel_type]').eq(0).val()) {
            addGlossaryTermRelationship();
        }
    });*/
    
    $('#glossary_relationships').on('click', '.rel-add', function(e) {
        e.preventDefault();
        var parent_row = $(this).parents('tr').eq(0);
        if (parent_row.find('[name^=related_term]').eq(0).val() && parent_row.find('[name^=rel_type]').eq(0).val()) {
            addGlossaryTermRelationship();
        }
    });
    
    $('#glossary_relationships').on('click', '.rel-delete', function(e) {
        e.preventDefault();
        var parentRow = $(this).parents('tr').eq(0);
        parentRow.hide();
        
        if (parentRow.find('[name^=rel_id]').length) {
            var name = parentRow.find('[name^=rel_id]').eq(0).attr('name');
            var termID = parentRow.find('[name^=rel_id]').eq(0).val();
            
            console.log(name);
            name = name.replace('id', 'delete');
            $('form').append('<input type="hidden" name="' + name + '" value="' + termID + '" />');
        }
    });
    
    $('.term-delete').click(function(e) {
        e.preventDefault();
        $('#deleteGlossaryTermModal').modal();
    });
    
    /*
     * Create distribution map
     */
    $('#create_distribution_map, #add_occurrences').on('click', function() {
        var reg = /([0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12})/;
        if (reg.test(location.href)) {
            $(this).after('<span style="display:inline-block;margin-left:6px;margin-top:3px;color:#7F9457"><i class="fa fa-spinner fa-spin fa-lg"></i></span>');
            var guid = reg.exec(location.href)[1];
            var url = base_url + '/ajax/create_distribution_map/' + guid;
            var postData = {};
            if ($('[name=ala_scientific_name]').length > 0) {
                postData.ala_scientific_name = $('[name=ala_scientific_name]').val();
            }
            postData.ala_unprocessed_scientific_name = $('[name=ala_unprocessed_scientific_name]').val();
            console.log(postData);
            $.ajax({
                url: url,
                method: 'POST',
                data: postData,
                success: function(data) {
                    location.reload();
                }
            });
        }
    });
    
    /*
     * Edit distribution tabs
     */
    
    $('#edit-distribution-tabs ul').on('click', 'a', function(event) {
        event.preventDefault();
        $(event.target).tab('show');
    });
    
    /*
     * Accept map updates
     */
    
    $('.accept-all').click(function() {
        $(this).after('<span style="display:inline-block;margin-left:6px;margin-top:3px;color:#7F9457"><i class="fa fa-spinner fa-spin fa-lg"></i></span>');
        var postData = {};
        var reg = /([0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12})/;
        if (reg.test(location.href)) {
            postData.taxon_id = reg.exec(location.href)[1];
        }
        var url = base_url + '/ajax/accept_map_updates';
        $.ajax({
            url: url,
            method: 'POST',
            data: postData,
            success: function(data) {
                location.reload();
            }
        });
    });
});

function addGlossaryTermRelationship() {
    //Add new row
    var index = $('#glossary_relationships table>tbody>tr').length;
    var html = '<tr>';
    html += '<td><select name="rel_type[' + index + ']" class="form-control"></select></td>';
    html += '<td><input name="related_term[' + index + ']" class="form-control" /></td>';
    html += '<td><button class="rel-add button button-default">Add row</button></td>';
    html += '</tr>';
    $('#glossary_relationships table>tbody').append(html);
    
    var options = $('#glossary_relationships table>tbody select:eq(0) option').clone();
    options.removeProp('selected');
    
    $('#glossary_relationships table>tbody select:eq(' + index + ')').append(options);
    $('#glossary_relationships table>tbody tr:eq(' + (index-1) + ') td:eq(2)').html('&nbsp;');
    
    $('#glossary_relationships table>tbody tr:eq(' + index + ') [name^=related_term]').autocomplete({
        source: base_url + '/autocomplete/autocomplete_glossary_term',
        minLength: 2
    });
    
}

function fullName() {
        var fullname;
        var parentname = $('input[name=parent_name]').val();
        var taxontreedefitemid = $('select[name=taxon_tree_def_item_id]').val();
        var name = $('input[name=name]').val();
        switch(taxontreedefitemid) {
            case '13':
              fullname = parentname + ' ' + name;
              break;
            case '14':
              fullname = parentname + ' subsp. ' + name;
              break;
            case '15':
              fullname = parentname + ' var. ' + name;
              break;
            case '17':
              fullname = parentname + ' f. ' + name;
              break;
            case '21':
              fullname = parentname + ' nothovar. ' + name;
              break;
            case '22':
              fullname = parentname + ' nothosubsp. ' + name;
              break;
            default:
              fullname = name;
        }
        $('input[name=full_name]').val(fullname);
        $('input#full_name_display').val(fullname);
        
        if ($('.find-in-apni').length === 0) {
            var span = $('<span />', {
                "class": 'find-in-apni',
                "html": 'Find in APNI'
            });
            $('#full_name_display').parents('p').eq(0).append(span);
        }
}

function attrValueDropdown(attribute, index) {
    var values, labels;
    switch(attribute) {
        case 'Naturalised status':
            values = ['native', 'introduced'];
            labels = ['native', 'introduced'];
            break;
            
        case 'VROT':
            values = ['x, e, v, r, k'];
            labels = ['x – extinct', 'e – endangered', 'v – vulnerable', 'r – rare', 'k – unknown'];
            break;
            
        default:
            break;
    }
    
    var select = $('<select />', {
        'name': 'taxon_attribute_value[' + index + ']',
        'id': 'taxon_attribute_value_' + index,
        'class': 'three columns' 
    });
    
    $('<option />', {
       'value': false,
       'text': ''
    }).appendTo(select);
   
    $.each(values, function(index, value) {
        $('<option />', {
            'value': value,
            'text': labels[index]
        }).appendTo(select);
    });
    
    return select;
}

function accepted_name() {
    var fullname = $('#nn_name').val();
    fullname = encodeURI(fullname).replace('(', '%28').replace(')', '%29');
    var url = base_url + '/ajax/new_name/' + fullname;
    
    $('.undo-accepted').remove();
    
    if ($('#nn_type').val() === 'accepted') {
        $('#taxonomic_status_display').val('accepted');
        $('[name=taxonomic_status]').val('accepted');
        $('[name=accepted_name_id]').val($('[name=taxon_id]').val());
        $('#accepted_name').html('');
    }
    else {
        if (['homotypic synonym', 'heterotypic synonym', 'synonym', 'misapplication'].indexOf($('#nn_type').val()) !== -1) {
            $.getJSON(url, function(data) {
                $('input[name=accepted_name_id]').val(data.TaxonID);
                var author = '';
                if (data.Author) {
                    author = ' ' + data.Author;
                }
                var html = '<span class="eleven columns">Currently accepted name: '
                    + '<span class="currentname"><span class="namebit">'
                    + data.FullName.replace(' subsp. ', '</span> subsp. <span class="namebit">').replace(' var. ', '</span> var. <span class="namebit">').replace(' f. ', '</span> f. <span class="namebit">')
                    + '</span>' + author + '</span></span>';
                $('#accepted_name').html(html);
                $('#nn_name').val(null);
                $('#taxonomic_status_display').val($('#nn_type').val());
                $('[name=taxonomic_status]').val($('#nn_type').val());
                $('#nn_type').val(false);
            });
        }
        else {
            $('#taxonomic_status_display').val('');
            $('[name=taxonomic_status]').val('');
            $('[name=accepted_name_id]').val('');
            $('#accepted_name').html('');
        }
    }
    
    if($('#nn_source').val()) {
        var s = $('#nn_source').val();
        $('#accepted_name>span').append(', <i>cf.</i> ' + s);
        var source = $('<input />', {
            'type': 'hidden',
            'name': 'accepted_name_source',
            'value': s
        });
        
        $('form.edit-taxon-form').append(source);
    }
    
    var undo = $('<span />', {
        'class': 'undo-accepted one column',
        'text': 'undo'
    });
    undo.insertAfter('#taxonomic_status_display');
}

function parent() {
    var parentname = $('#new_parent').val();
    var url = base_url + '/ajax/parent_by_name/' + parentname;
    $.getJSON(url, function(data) {
        if (data) {
            $('input[name=parent_name_old]').val($('input[parent_name]'));
            $('input[name=parent_name]').val(data.Name);
            $('input[name=parent_id_old]').val($('input[parent_id]'));
            $('input[name=parent_id]').val(data.TaxonID);
            $('#parent_display').val(data.Name);
            if (data.Name !== $('[name=parent_name_old]').val()) {
                $('#parent_display').addClass('has-changed');
            }
            else {
                $('#parent_display').removeClass('has-changed');
            }
        }
    });
    $('#new_parent').hide();
    $('#change-parent button').remove();
    $('#change-parent a').show();
}

function newTaxon() {
    var newtaxon = $('#new_taxon').val();
    if (newtaxon) {
        var url = base_url + '/ajax/taxon_by_name/' + newtaxon;
        $.getJSON(url, function(data) {
            if (data) {
                $('[name=new_accepted_id]').val(data.TaxonID);
                $('[name=new_accepted_name]').val(data.Name);
                $('#assign_to_taxon button').remove();
                $('#new_taxon').hide();
                $('#assign_to_taxon a').show();
                $('#new_accepted_name').html(data.Name);
            }
        });
    }
    else {
        alert('New name hasn\'t been filled in');
        $('#new_accepted_name').html('');
        $('#assign_to_taxon button').remove();
        $('#new_taxon').hide();
        $('#assign_to_taxon a').show();
        $('[name=new_accepted_id]').val('');
        $('[name=new_accepted_name]').val('');
    }
}

function undo_accepted_name_change() {
    var id = $('input[name=accepted_name_id_old]').val();
    var url = base_url + '/ajax/accepted_name_by_id/' + id;
    
    $('input[name=accepted_name_id]').val(id);
    $('#taxonomic_status_display').val($('[name=taxonomic_status_old]').val());
    $('[name=taxonomic_status]').val($('[name=taxonomic_status_old]').val());
    $('.undo-accepted').remove();

    if (['homotypic synonym', 'heterotypic_synonym', 'synonym', 'misapplied'].indexOf($('[name=taxonomic_status_old]').val()) !== -1) {
        $.getJSON(url, function(data) {

            var author = '';
            if (data.Author) {
                author = ' ' + data.Author;
            }
            var html = '<span class="eleven columns">Currently accepted name: '
                + '<span class="currentname"><span class="namebit">'
                + data.FullName.replace(' subsp. ', '</span> subsp. <span class="namebit">').replace(' var. ', '</span> var. <span class="namebit">').replace(' f. ', '</span> f. <span class="namebit">')
                + '</span>' + author + '</span></span>';
            $('#accepted_name').html(html);
        });
    }
    else {
        $('#accepted_name').children().remove();
    }
}

function get_apni_name() {
    var id = $('[name^=apni_accept]:checked').parents('tr').eq(0).children('td').eq(0).children('a').eq(0).html();
    var url = base_url + '/wscurl/search_nsl_name_id/' + id;
    
    $.getJSON(url, function(data) {
        
        
        var item = data[0];
        if ($('[name=taxon_id]').val() === '') {
            $('[name=author]').val('').removeClass('.has-changed').next('.undo').remove();
            $('[name=in_author]').val('').removeClass('.has-changed').next('.undo').remove();
            $('[name=journal_or_book]').val('').removeClass('.has-changed').next('.undo').remove();
            $('[name=publication_year]').val('').removeClass('.has-changed').next('.undo').remove();
            $('[name=series]').val('').removeClass('.has-changed').next('.undo').remove();
            $('[name=edition]').val('').removeClass('.has-changed').next('.undo').remove();
            $('[name=volume]').val('').removeClass('.has-changed').next('.undo').remove();
            $('[name=part]').val('').removeClass('.has-changed').next('.undo').remove();
            $('[name=page]').val('').removeClass('.has-changed').next('.undo').remove();
            
            $('[name=author]').val(item.Authorship).addClass('has-changed').after('<span class="undo one column">undo</span>');
            $('[name=publication_year]').val(item.Year).addClass('has-changed').after('<span class="undo one column">undo</span>');
            $('[name=page]').val(item.MicroReference).addClass('has-changed').after('<span class="undo one column">undo</span>');

            if (typeof item.PublicationRef !== 'undefined' && typeof item.PublicationRef.dcterms_title !== 'undefined') {
                var breakpoint = item.PublicationRef.dcterms_title.indexOf('(' + item.Year + ')');
                var protologue = item.PublicationRef.dcterms_title.substr(breakpoint + item.Year.length + 3).trim().replace(/.$/, '');
                var inpoint = item.PublicationRef.dcterms_title.indexOf(' in ');
                if (inpoint > -1 && inpoint < breakpoint) {
                    var inauthor = item.PublicationRef.dcterms_title.substr(inpoint + 4, breakpoint - inpoint - 4).trim().replace(/,$/, '');
                    $('[name=in_author]').val(inauthor).addClass('has-changed').after('<span class="undo one column">undo</span>');
                }

                $('[name=journal_or_book]').val(protologue).addClass('has-changed').after('<span class="undo one column">undo</span>');
            }
        }
        var rowindex = $('.apni-name-match tbody tr').length;
        if ($('[name=apni_no]').length > 0) {
            rowindex = rowindex - 1;
        }
        
        var html = '<tr>';
        html += '<td><input name="apni_no" value="' + item.uri.substr(item.uri.lastIndexOf('/')+1) + '"/></td>';
        html += '<td><input name="apni_fullnamewithauthors" value="' + item.dcterms_title + '" style="width:400px;" /></td>';
        
        var newmatchtype;
        if (item.dcterms_title === $('#full_name_display').val() + " " + $('[name=author]').val()) {
            newmatchtype = 'FullNameWithAuthors';
        }
        else {
            newmatchtype = 'FullName';
        }
        html += '<td><input name=apni_match_type[' + rowindex + ']" value="' + newmatchtype + '" /></td>';
        
        $('.apni-name-match [name^=apni_match_verified]').prop('checked', false);
        $('.apni-name-match [name^=apni_delete]').prop('checked', true);
        
        html += '<td><input type="checkbox" name="apni_match_verified[' + rowindex + ']" checked="checked" /></td>';
        html += '<td><input type="checkbox" name="apni_delete[' + rowindex + ']" /></td>';
        html += '</tr>';
        
        $('.apni-name-match tbody').append(html);
    });
}
