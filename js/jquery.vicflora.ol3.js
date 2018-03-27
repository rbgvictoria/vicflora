var checklist;
$(function() {
    EditMap();
    
    /*
     * CHECKLIST
     */
    if (location.href.indexOf('/flora/checklist') > -1) {
        checklist = new Checklist();
        var resNumber;
        var last_uri_segment = location.href.substr(location.href.lastIndexOf('/', location.href.length - 1) + 1);
        if (last_uri_segment.substr(0, 3) === 'VIC') {
            resNumber = last_uri_segment;
        }
        if (typeof resNumber !== undefined) {
            checklist.getSelectedLayer(resNumber);
            checklist.pageSize = 50;
            checklist.getChecklist(resNumber);
        }
    }

});

var EditMap = function() {
    var map;
    var cql_filter;
    var taxonid = location.href.substr(location.href.indexOf('editdistribution')+17);
    var extent = [140.994506835938,-39.1984825134277,150.001251220703,-36.8084373474121];
    
    var boundaries = new ol.layer.Tile({
        title: "State boundaries",
        source: new ol.source.TileWMS({
          url: 'https://data.rbg.vic.gov.au/geoserver/vicflora/wms',
          params: {
              LAYERS: 'vicflora:cst_vic', 
              TRANSPARENT: true,
              CQL_FILTER: "FEAT_CODE IN ('mainland','island')"
          },
          serverType: 'geoserver'
        }),
        opacity: 0.5
    });
    
    var boundaries2 = new ol.layer.Tile({
        title: "State boundaries dup",
        source: new ol.source.TileWMS({
          url: 'https://data.rbg.vic.gov.au/geoserver/vicflora/wms',
          params: {
              LAYERS: 'vicflora:cst_vic', 
              TRANSPARENT: true,
              STYLES: 'polygon_no-fill_grey-outline',
              CQL_FILTER: "FEAT_CODE IN ('mainland','island')"
          },
          serverType: 'geoserver'
        }),
        opacity: 1
    });
    
    var bioregions = new ol.layer.Tile({
        title: "Victorian bioregions",
        source: new ol.source.TileWMS({
          url: 'https://data.rbg.vic.gov.au/geoserver/vicflora/wms',
          params: {
              LAYERS: 'vicflora:vicflora_bioregion', 
              TRANSPARENT: true,
              STYLES: 'polygon_no-fill_grey-outline'
          },
          serverType: 'geoserver'
        }),
        opacity: 1
    });
    
    var bioregionsEstablismentMeans = new ol.layer.Tile({
        title: "Victorian bioregions according to establishment means",
        source: new ol.source.TileWMS({
          url: 'https://data.rbg.vic.gov.au/geoserver/vicflora/wms',
          params: {
              LAYERS: 'vicflora:distribution_bioregion_view', 
              TRANSPARENT: true,
              CQL_FILTER: "taxon_id='" + taxonid + "'"
          },
          serverType: 'geoserver'
        }),
        opacity: 1
    });
    
    var occurrences = new ol.layer.Tile({
        title: "Occurrences for taxon",
        source: new ol.source.TileWMS({
          url: 'https://data.rbg.vic.gov.au/geoserver/vicflora/wms',
          params: {
              LAYERS: 'vicflora:occurrence_view', 
              TRANSPARENT: true,
              CQL_FILTER: "accepted_name_usage_id='" + taxonid + "'"
          },
          serverType: 'geoserver'
        }),
        opacity: 1
    });
    
    var outliers = new ol.layer.Tile({
        title: "Outliers for taxon",
        source: new ol.source.TileWMS({
          url: 'https://data.rbg.vic.gov.au/geoserver/vicflora/wms',
          params: {
              LAYERS: 'vicflora:outlier_view', 
              TRANSPARENT: true,
              CQL_FILTER: "taxon_id='" + taxonid + "'"
          },
          serverType: 'geoserver'
        }),
        opacity: 1,
        visible: false
    });
    
    var view = new ol.View({
        projection: 'EPSG:4326',
        center: [145.48689270019, -36.50931930542],
        extent: extent,
        zoom: 7
    });
    
    map = new ol.Map({
      target: 'edit_distribution_map',
      layers: [boundaries, bioregionsEstablismentMeans, bioregions, occurrences, boundaries2, outliers],
      view: view
    });
    
    $('#show_outliers').on('change', function() {
        if ($(this).prop('checked')) {
            outliers.setVisible(true);
        }
        else {
            outliers.setVisible(false);
        }
    });
    
    map.on('click', function(event) {
        url = base_url + '/ajax/occurrences_from_point/' + taxonid + '/' + event.coordinate[0] + '/' + event.coordinate[1];
        $.getJSON(url, function(data) {
            var html;
            html = '<h3>Occurrence records</h3>';
            html += '<table class="occurrences-at-point table table-bordered table-condensed">';
            html += '<tr><th>ALA record number</th><th>Catalogue number</th><th>Longitude</th><th>Latitude</th><th>Bioregion</th><th>Occurrence status</th><th>Establishment means</th></tr>';
            
            if (data.length > 0) {
                $.each(data, function(index, item) {
                    var reg = item.sub_name_7 !== null ? item.sub_name_7 : '&nbsp;';
                    var est = item.establishment_means !== null ? item.establishment_means : '';
                    var occ = item.occurrence_status !== null ? item.occurrence_status : '';
                    
                    html += '<tr>';
                    if (!isNaN(item.catalogNumber)) {
                        html += '<td><a href="http://biocache.ala.org.au/occurrence/' + item.uuid + '" target="_blank">' + item.uuid + '</a></td>';
                        html += '<td>VBA ' + item.catalog_number + '</td>';
                    }
                    else {
                        html += '<td><a href="http://avh.ala.org.au/occurrence/' + item.uuid + '" target="_blank">' + item.uuid + '</a></td>';
                        html += '<td>' + item.catalog_number + '</td>';
                    }
                    html += '<td>' + item.decimal_longitude + '</td>';
                    html += '<td>' + item.decimal_latitude + '</td>';
                    html += '<td>' + reg + '</td>';
                    
                    html += '<td>';
                    html += '<input type="hidden" name="occ-uuid[' + index + ']" value="' + item.uuid + '"/>';
                    html += '<select name="occ_occurrence_status[' + index + ']" class="form-control input-sm">';
                    html += '<option value="">&nbsp;</option>';
                    html += '<option value="present" ';
                    if (occ === 'present') {
                        html += 'selected';
                    }
                    html += '>present</option>';
                    
                    html += '<option value="endemic" ';
                    if (occ === 'endemic') {
                        html += 'selected';
                    }
                    html += '>endemic</option>';
                    
                    html += '<option value="absent" ';
                    if (occ === 'absent') {
                        html += 'selected';
                    }
                    html += '>absent</option>';
                    
                    html += '<option value="extinct" ';
                    if (occ === 'extinct') {
                        html += 'selected';
                    }
                    html += '>extinct</option>';
                    
                    html += '<option value="doubtful" ';
                    if (occ === 'doubtful') {
                        html += 'selected';
                    }
                    html += '>doubtful</option>';
                    
                    html += '</select>';
                    html += '</td>';
                    
                    html += '<td>';
                    html += '<select name="occ_establishment_means[' + index + ']" class="form-control input-sm">';
                    html += '<option value="">&nbsp;</option>';
                    html += '<option value="native" ';
                    if (est === 'native') {
                        html += 'selected';
                    }
                    html += '>native</option>';
                    
                    html += '<option value="introduced" ';
                    if (est === 'introduced') {
                        html += 'selected';
                    }
                    html += '>introduced</option>';
                    
                    html += '<option value="naturalised" ';
                    if (est === 'naturalised') {
                        html += 'selected';
                    }
                    html += '>naturalised</option>';
                    
                    html += '<option value="cultivated" ';
                    if (est === 'cultivated') {
                        html += 'selected';
                    }
                    html += '>cultivated</option>';
                    
                    html += '<option value="uncertain" ';
                    if (est === 'uncertain') {
                        html += 'selected';
                    }
                    html += '>uncertain</option>';
                    
                    html += '</select>';
                    html += '</td>';
                    //html += '<td>' + est + '</td>';
                    html += '</tr>';
                });
            }
            
            html+= '</table>';
            
            $('#nodelist').html(html);
            
            $('[name^=occ_occurrence_status').on('change', function(e) {
                var occ = $(this).val();
                var uuid = $(this).parents('tr').eq(0).find('[name^=occ-uuid]').eq(0).val();
                $.ajax({
                    url: base_url + '/ajax/update_occurrence',
                    method: 'POST',
                    data: {
                        "uuid": uuid,
                        "occurrence_status": occ
                    },
                    success: function (data) {
                        console.log(data);
                        regions = [];
                        $('table.bioregions [name^=sub_code_7]').each(function() {
                            regions.push($(this).val());
                        });
                        console.log(regions);
                        var index = regions.indexOf(data.region);
                        if ($('table.bioregions [name^="occurrence_status["]').eq(index).val() !== data.occurrence_status) {
                            $('table.bioregions [name^="occurrence_status["]').eq(index).val(data.occurrence_status).css('background-color', '#ffff00');
                        }
                        if ($('#reload').length === 0) {
                            $('#nodelist').append('<div class="text-right"><button id="reload" class="btn btn-default">Reload</button></div>');
                            $('#reload').on('click', function() {
                                location.reload();
                            });
                        }
                    }
                });
            });
            
            $('[name^=occ_establishment_means').on('change', function(e) {
                var est = $(this).val();
                var uuid = $(this).parents('tr').eq(0).find('[name^=occ-uuid]').eq(0).val();
                $.ajax({
                    url: base_url + '/ajax/update_occurrence',
                    method: 'POST',
                    data: {
                        "uuid": uuid,
                        "establishment_means": est
                    },
                    success: function (data) {
                        regions = [];
                        $('table.bioregions [name^=sub_code_7]').each(function() {
                            regions.push($(this).val());
                        });
                        var index = regions.indexOf(data.region);
                        if ($('table.bioregions [name^="establishment_means["]').eq(index).val() !== data.establishment_means) {
                            $('table.bioregions [name^="establishment_means["]').eq(index).val(data.establishment_means).css('background-color', '#ffff00');
                        }
                        if ($('#reload').length === 0) {
                            $('#nodelist').append('<div class="text-right"><button id="reload" class="btn btn-default">Reload</button></div>');
                            $('#reload').on('click', function() {
                                location.reload();
                            });
                        }
                    }
                });
            });
        });
    });
};

var Checklist = function() {
    var that = this;
    this.map;
    this.json;
    this.taxa;
    this.filters = {};
    this.pageSize = 50;
    this.orderBy = "scientificName";
    
    this.carto = new ol.layer.Tile({ 
        source: new ol.source.XYZ({ 
            url:'https://cartodb-basemaps-b.global.ssl.fastly.net/light_all/{z}/{x}/{y}.png',
            attributions: [new ol.Attribution({
                html: '© <a href="https://cartodb.com/attributions">CartoDB</a> ' +
                 '© <a href="https://www.openstreetmap.org/copyright">' +
                 'OpenStreetMap contributors</a>'
            })]
        })
    });

    this.boundaries = new ol.layer.Tile({
        title: "State boundaries",
        source: new ol.source.TileWMS({
          url: 'https://data.rbg.vic.gov.au/geoserver/vicflora/wms',
          params: {
              LAYERS: 'vicflora:vic_boundaries', 
              TRANSPARENT: true
          },
          serverType: 'geoserver'
        }),
        opacity: 1
    });
    
    this.capad = new ol.layer.Tile({
        title: "CAPAD 2012 Protected Areas",
        source: new ol.source.TileWMS({
          url: 'https://data.rbg.vic.gov.au/geoserver/vicflora/wms',
          params: {
              LAYERS: 'vicflora:vicflora_capad',
              STYLES: 'green_polygon',
              TRANSPARENT: true
          },
          serverType: 'geoserver'
        }),
        opacity: 0.5
    });
    
    this.view = new ol.View({
        projection: 'EPSG:3857',
        center: [16165659.235725,-4361228.5852319],
        zoom: 6
    });
    
    this.map = new ol.Map({
      target: 'capad_map',
      layers: [this.carto, this.capad, this.boundaries],
      view: this.view
    });
    
    this.map.on('click', function(event) {
        url = base_url + '/ajax/capad_from_map_point/' + event.coordinate[0] + '/' + event.coordinate[1];
        $.getJSON(url, function(data) {
            var html;
            
            html = '<table class="capad-areas table table-condensed table-bordered">';
            html += '<tr><th>Name</th><th>Type</th></tr>';
            
            if (data.length > 0) {
                $.each(data, function(index, item) {
                    html += '<tr>';
                    html += '<td><a href="' + base_url + '/flora/checklist/' + item.res_number + '">' + item.name + '</a></td>';
                    html += '<td>' + item.type + '</td>'
                    html += '</tr>';
                });
            }
            
            html+= '</table>';
            
            $('#nodelist').html(html);
            $('#nodelist a').on('click', function(e) {
                e.preventDefault();
                location.href = $(this).attr('href');
            });
        });
    });
    
    this.getSelectedLayer = function(resNumber) {
        capad_selected = new ol.layer.Tile({
            title: "Selected park or reserve",
            source: new ol.source.TileWMS({
              url: 'https://data.rbg.vic.gov.au/geoserver/vicflora/wms',
              params: {
                  LAYERS: 'vicflora:vicflora_capad',
                  STYLES: 'capad_selected',
                  TRANSPARENT: true,
                  CQL_FILTER: "res_number='" + resNumber + "'"
              },
              serverType: 'geoserver'
            }),
            opacity: 1
        });
        this.map.addLayer(capad_selected);
    };
    
    this.getChecklist = function(resNumber) {
        that.resNumber = resNumber;
        $('#facets').prepend('<span class="preparing"><i class="fa fa-spinner fa-spin fa-2x"></i> <b>Preparing checklist...</b></span>');
        
        var url = base_url + '/flora/ajaxChecklist/' + resNumber;
        $.ajax({
            url: url,
            success: function(data) {
                that.json = data;
                that.taxa = that.json.taxa;
                that.displayResult();
                that.getFacets();
            }
        });
    };
    
    this.displayResult = function(start) {
        var start = typeof start !== 'undefined' ? start : 0;
        $('#facets .preparing').remove();
        $('#checklist-result').html('');
        
        if (typeof that.taxa !== 'undefined') {
            var taxa = that.orderTaxa(that.taxa);
            /*$.each(taxa, function(index, item) {
                that.displayResultItem(item);
            });*/
            var end = (start + that.pageSize < taxa.length) ? start + that.pageSize : taxa.length;
            for (i = start; i < end; i++) {
                that.displayResultItem(taxa[i]);
            }
            that.getResultHeader(start);
            that.getResultFooter(start);
        }
    };
    
    this.displayResultItem = function(item) {
        var entry = $('<div/>', {class: "search-name-entry"}).appendTo('#checklist-result');
        var row = $('<div/>', {class: "row"}).appendTo(entry);
        var col = $('<div/>', {class: "col-lg-9 col-md-8"}).appendTo(row);
        var link = $('<a/>', {href: base_url + '/flora/taxon/' + item.id}).appendTo(col);
        var name = '<span class="namebit">' + item.scientificName + '</span>';
        $.each(['subsp.', 'var.', 'f.', 'nothosubsp.', 'nothovar.'], function(index, prefix) {
            name = name.replace(' ' + prefix + ' ', '</span> ' + prefix + '<span class="namebit">');
        });
        $('<span/>', {
            class: "currentname italic",
            html: name
        }).append(' ' + item.scientificNameAuthorship).appendTo(link);
        $('<div/>', {
            class: "fam col-lg-3 col-md-4 text-right",
            html: item.family
        }).appendTo(row);
        
        
        //$('#checklist-result').append(entry).append(row).append(col).append(link).append(name);
    };
    
    this.orderTaxa = function(taxa) {
        if (typeof taxa !== 'undefined') {
            taxa.sort(function(a, b) {
                if (a.scientificName > b.scientificName) {
                    return 1;
                }
                if (a.scientificName < b.scientificName) {
                    return -1;
                }
                return 0;
            });

            if (that.orderBy === 'family') {
                taxa.sort(function(a, b) {
                    if (a.family > b.family) {
                        return 1;
                    }
                    if (a.family < b.family) {
                        return -1;
                    }
                    return 0;
                });
            }
        }
        return taxa;
    };
    
    this.getFacets = function() {
        $('#facets h3').show();
        $('#facets .content').html('');
        var fields = ['subclass', 'superorder', 'order', 'family', 'occurrenceStatus', 'establishmentMeans'];
        var facets = [];
        $.each(fields, function(index, field) {
            var facet = {
                fieldName: field,
                fieldResult: that.getFacet(field)
            };
            facets.push(facet);
        });
        that.renderFacets(facets);
    };
    
    this.getFacet = function(field) {
        var items = JSPath.apply('.' + field, that.taxa);
        counts = that.countValues(items);
        var labels = counts[0];
        var values = counts[1];
        var fieldResult = [];
        $.each(labels, function(index, label) {
            var item = {
                'label': label,
                'value': values[index]
            };
            fieldResult.push(item);
        });
        return fieldResult;
    };
    
    this.renderFacets = function(facets) {
        that.showFilters();
        $.each(facets, function(index, facet) {
            that.renderFacet(facet);
        });
        $('.facets').off('click', '.apply-filter');
        $('.facets').on('click', '.apply-filter', function(event) {
            event.preventDefault();
            var field = $(this).parents('.facet').eq(0);
            var filter = that.getFilter(field);
            if (filter.values.length > 0) {
                that.applyFilter(filter);
            }
        });
        $('.facets').on('click', 'a', function(event) {
            event.preventDefault();
            if ($(this).parent('label').prev('input').prop('disabled') === false) {
                var field = $(this).parents('.facet').eq(0);
                var index = field.find('a').index($(this));
                field.find('input').each(function() {
                    $(this).removeAttr('checked');
                });
                field.find('input').eq(index).prop('checked', true);
                var filter = that.getFilter(field);
                if (filter.values.length > 0) {
                    that.applyFilter(filter);
                }
            }
        });
    };
    
    this.getFilter = function(field) {
        var checked = [];
        field.find('input:checked').each(function() {
            var value = $(this).val();
            checked.push(value);
        });
        var name = field.attr('data-vicflora-facet-name');
        var filter = {
            field: name,
            values: checked
        };
        return filter;
    };
    
    this.applyFilters = function() {
        console.log('Hello');
        $.each(that.filters, function(field, values) {
            var filter = {
                field: field,
                values: values
            };
            that.applyFilter(filter);
        })
    };
    
    this.applyFilter = function(filter) {
        that.filters[filter.field] = filter.values;
        var path = '.{.' + filter.field + ' === $values}';
        var taxa = JSPath.apply(path, that.taxa, {values: filter.values});
        that.taxa = taxa;
        that.displayResult();
        that.getFacets();
    };
    
    this.showFilters = function() {
        $('#filters').remove();
        if (!$.isEmptyObject(that.filters)) {
            var filterDiv = $('<div/>', {id: "filters"}).prependTo('#facets');
            var appliedFilterDiv = $('<div/>', {
                class: 'applied-filters',
                html: '<h4>Filters applied</h4>'
            }).appendTo(filterDiv);
            var ul = $('<ul/>').appendTo(appliedFilterDiv);
            
            $.each(that.filters, function(field, values) {
                var li = $('<li/>', {
                    class: "applied-filter",
                    'data-vicflora-filter': field,
                    html: '<b>' + field + ':</b> ' + values.join(', ')
                }).appendTo(ul);
                
                var link = $('<a/>', {
                    href: "#",
                    html: ' <i class="fa fa-times" aria-hidden="true"></i>',
                    title: "Remove filter"
                }).appendTo(li);
                
                link.on('click', function(event) {
                    event.preventDefault();
                    that.deleteFilter(field);
                });
            });
        }
    };
    
    this.deleteFilter = function(field) {
        delete that.filters[field];
        that.taxa = that.json.taxa;
        if ($.isEmptyObject(that.filters)) {
            $('#filters').remove();
            that.displayResult();
            that.getFacets();
        }
        else {
            that.applyFilters();
        }
    } 
    
    this.renderFacet = function(facet) {
        if (facet.fieldResult.length > 0) {
            var facetDiv = $('<div/>', {
                class: "facet collapsible",
                'data-vicflora-facet-name': facet.fieldName
            }).appendTo('#facets .content');

            var facetHeader = $('<h4/>', {
                html: that.facetHeader(facet.fieldName)
            }).prepend('<span class="glyphicon glyphicon-triangle-bottom"></span>').appendTo(facetDiv);

            var ul = $('<ul/>', {
                class: "form-group"
            }).appendTo(facetDiv);

            $.each(facet.fieldResult, function(index, item) {
                
                
                var li = $('<li/>', {
                    class: "checkbox"
                }).appendTo(ul);
                var checkbox = $('<input/>', {
                    type: "checkbox",
                    value: item.label
                }).appendTo(li);
                
                if (that.filters[facet.fieldName] !== undefined && 
                        that.filters[facet.fieldName].indexOf(item.label) > -1) {
                    checkbox.attr('checked', 'checked').attr('disabled', 'disabled');
                }

                var label = $('<label/>').appendTo(li);
                $('<a/>', {
                    href: "#",
                    html: item.label + ' (' + item.value + ')'
                }).appendTo(label);
            });

            if (facet.fieldResult.length > 3) {
                var listItems = ul.children('li');
                ul.children('li').each(function() {
                    if (listItems.index($(this)) > 2) {
                        $(this).hide();
                    }
                });
                var facetFooter = $('<div/>', {
                    class: "facet-footer"
                }).append('<a class="more">More</a>').appendTo(facetDiv);
            }
        }
    };
    
    this.facetHeader = function(str) {
        var re = /([A-Z])/g;
        var header = str.replace(re, ' $1');
        header = header.substring(0, 1).toUpperCase() + header.substring(1).toLowerCase();
        return header;
    };
    
    this.countValues = function(arr) {
        var a = [], b = [], prev;

        arr.sort();
        for ( var i = 0; i < arr.length; i++ ) {
            if ( arr[i] !== prev ) {
                a.push(arr[i]);
                b.push(1);
            } else {
                b[b.length-1]++;
            }
            prev = arr[i];
        }

        return [a, b];
    };
    
    this.getResultHeader = function(start) {
        var row = $('<div/>', {
            class: "row"
        }).prependTo('#checklist-result');

        var headerDiv = $('<div/>', {
            class: "query-result-header"
        }).appendTo(row);
        
        var sort = that.sortOrderDiv();
        $('<div/>', {
            class: "col-sm-3 pull-right"
        }).append(sort).appendTo(headerDiv);
        
        $('<div/>', {
            class: "num-matches col-sm-3",
            html: that.taxa.length + " matches"
        }).appendTo(headerDiv);
        
        var nav = that.getNav(start, 'header');
        $('<div/>', {
            class: "col-sm-6"
        }).append(nav).appendTo(headerDiv);
        
        $('<div/>', {
            class: "col-md-12"
        }).append('<button class="download btn btn-primary" data-href="' + base_url + '/flora/downloadChecklist/' + that.resNumber + '">Download checklist</button>').appendTo(headerDiv);
        
        $('button.download').on('click', function() {
           var href = $(this).data('href');
           location.href = href;
        });
    };
    
    this.getResultFooter = function(start) {
        var footerDiv = $('<div/>', {
            class: "query-result-footer"
        }).appendTo('#checklist-result');
        
        var nav = that.getNav(start, 'footer');
        nav.appendTo(footerDiv);
    };
    
    this.getNav = function(start, where) {
        var navDiv = $('<div/>', {
            class: "query-result-nav text-center"
        });
        
        var startIcon = '<i class="fa fa-fast-backward"></i>';
        var prevIcon = '<i class="fa fa-backward"></i>';
        var nextIcon = '<i class="fa fa-forward"></i>';
        var lastIcon = '<i class="fa fa-fast-forward"></i>';
        
        if (start > 0) {
            $('<a/>', {
                href: "#",
                class: "nav-start",
                "data-vicflora-checklist-nav-start": 0,
                "data-vicflora-checklist-nav-that.pageSize": that.pageSize
            }).append(startIcon).appendTo(navDiv);

            $('<a/>', {
                href: "#",
                class: "nav-prev",
                "data-vicflora-checklist-nav-start": (start - that.pageSize > 0) ? start - that.pageSize : 0,
                "data-vicflora-checklist-nav-that.pageSize": that.pageSize
            }).append(prevIcon).appendTo(navDiv);
        }
        else {
            navDiv.append(startIcon);
            navDiv.append(prevIcon);
        }
        
        $('<span/>', {
            class: "query-result-rows",
            html: (start + 1) + "–" + (start + that.pageSize < that.taxa.length ? start + that.pageSize : that.taxa.length)  + " of " + that.taxa.length
        }).appendTo(navDiv);
        
        if (start + that.pageSize < that.taxa.length) {
            $('<a/>', {
                href: "#",
                class: "nav-next",
                "data-vicflora-checklist-nav-start": start + that.pageSize,
                "data-vicflora-checklist-nav-that.pageSize": that.pageSize
            }).append(nextIcon).appendTo(navDiv);

            $('<a/>', {
                href: "#",
                class: "nav-last",
                "data-vicflora-checklist-nav-start": that.taxa.length - (that.taxa.length % that.pageSize),
                "data-vicflora-checklist-nav-that.pageSize": that.pageSize
            }).append(lastIcon).appendTo(navDiv);
        }
        else {
            navDiv.append(nextIcon);
            navDiv.append(lastIcon);
        }
        
        $('.query-result-' + where).off('click', 'a');
        $('.query-result-' + where).on('click', 'a', function(event) {
            event.preventDefault();
            var start = Number($(this).attr('data-vicflora-checklist-nav-start'));
            that.displayResult(start);
        });
        
        return navDiv;
    };
    
    this.sortOrderDiv = function() {
        var form = $('<div/>', {class: "form-horizontal sort-by"});
        $('<label/>', {class: "sr-only", html: "sort alphabetically by..."}).appendTo(form);
        var inputGroup = $('<div/>', {class: "input-group"}).appendTo(form);
        var select = $('<select/>', {
            class: "form-control input-sm"
        }).appendTo(inputGroup);
        var scientificNameOption = $('<option>', {
            value: "scientificName",
            html: "scientific name",
        }).appendTo(select);
        var familyOption = $('<option>', {
            value: "family",
            html: "family",
        }).appendTo(select);
        if (that.orderBy === "scientificName") {
            scientificNameOption.attr("selected", "selected");
        }
        else {
            familyOption.attr("selected", "selected");
        }
        select.on('change', function(event) {
            that.orderBy = $(this).val();
            that.displayResult();
        });
        $('<div/>', {class: "input-group-addon"})
                .append('<i class="fa fa-sort-alpha-asc" aria-hidden="true"></i>').appendTo(inputGroup);
        return form;
    };
};


