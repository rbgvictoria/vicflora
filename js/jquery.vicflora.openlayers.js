var bounds;
var map;

var last_uri_segment = location.href.substr(location.href.lastIndexOf('/')+1);
var res_number;
if (last_uri_segment.substr(0, 3) === 'VIC') {
    res_number = last_uri_segment;
}

var map2;
var taxonid = location.href.substr(location.href.indexOf('editdistribution')+17);

$(function() {
    init();
    init2();
});

function init() {
    bounds = new OpenLayers.Bounds(
        140.962539672852, -39.1591949462891,
        149.976165771484, -34.0069198608398
    );
        
    map = new OpenLayers.Map('capad_map', {
            projection: 'EPSG:3857',
            displayProjection: 'EPSG:4326',
            maxScale: 6933486.650500195
    });

    map.addControl(new OpenLayers.Control.LayerSwitcher());
    /*map.addControl(
        new OpenLayers.Control.MousePosition({
            div: document.getElementById("mouse-position"),
            prefix: 'coordinates: ',
            separator: '°E ',
            suffix: '°N',
            numDigits: 5,
            emptyString: ''
        })
    );*/
    map.addControl(
        new OpenLayers.Control.Navigation({
            dragPanOptions: {
                enableKinetic: true
            }
        })
    );
    
    google_hybrid = new OpenLayers.Layer.Google(
        "Google Hybrid", {
            type: google.maps.MapTypeId.HYBRID, 
            numZoomLevels: 20
        }
    );

    google_streets = new OpenLayers.Layer.Google(
        "Google Streets", {
            numZoomLevels: 20
        }
    );


    boundaries = new OpenLayers.Layer.WMS(
        'boundaries',
        'http://data.rbg.vic.gov.au/geoserver/vicflora/wms',
        {
            layers: 'vicflora:vic_boundaries',
            transparent:true
        },
        {
            isBaseLayer: false,
            displayInLayerSwitcher: true,
            opacity: 1
        }
    );
    
    capad = new OpenLayers.Layer.WMS(
        'CAPAD Protected Area',
        'http://data.rbg.vic.gov.au/geoserver/vicflora/wms',
        {
            layers: 'vicflora:vicflora_capad',
            styles: 'green_polygon',
            transparent:true
        },
        {
            isBaseLayer: false,
            displayInLayerSwitcher: true,
            opacity: 0.5
        }
    );
    
    map.events.register("click", map, function(e) {
        var position = map.getLonLatFromPixel(e.xy);

        url = base_url + '/ajax/capad_from_map_point/' + position.lon.toFixed(5) + '/' + position.lat.toFixed(5);
        $.getJSON(url, function(data) {
            var html;
            
            html = '<table class="capad-areas">';
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
            
        });

      OpenLayers.Util.getElement("nodelist").innerHTML = 
            position.lon.toFixed(3) + ', ' + position.lat.toFixed(3);

    });
        


    map.addLayers([google_streets, google_hybrid, capad]);
    if (typeof res_number !== undefined) {
        capad_selected = new OpenLayers.Layer.WMS(
            'Selected park or reserve',
            'http://data.rbg.vic.gov.au/geoserver/vicflora/wms',
            {
                layers: 'vicflora:vicflora_capad',
                styles: 'capad_selected',
                transparent: true,
                cql_filter: "res_number='" + res_number + "'"
            },
            {
                isBaseLayer: false,
                displayInLayerSwitcher: true,
                opacity: 1
            }
        );
        map.addLayers([capad_selected]);    
    }
    
    map.setCenter(new OpenLayers.LonLat(16165659.235725,-4361228.5852319),6);

}

function init2() {
    bounds = new OpenLayers.Bounds(
                    140.994506835938, -39.1984825134277,
                    150.001251220703, -36.8084373474121
    );
        
    map2 = new OpenLayers.Map('edit_distribution_map', {
            controls: [],
            projection: 'EPSG:4326',
            displayProjection: 'EPSG:4326',
    });

    //map2.addControl(new OpenLayers.Control.LayerSwitcher());
    map2.addControl(
        new OpenLayers.Control.MousePosition({
            div: document.getElementById("mouse-position"),
            separator: '°E ',
            suffix: '°N',
            numDigits: 5,
            emptyString: ''
        })
    );
    
    /*map2.addControl(
        new OpenLayers.Control.Navigation({
            dragPanOptions: {
                enableKinetic: true
            }
        })
    );*/
    
    boundaries = new OpenLayers.Layer.WMS(
        'boundaries',
        'http://data.rbg.vic.gov.au/geoserver/vicflora/wms',
        {
            layers: 'vicflora:cst_vic',
            transparent:true,
            //styles: "polygon_no-fill_grey-outline",
            cql_filter: "FEAT_CODE IN ('mainland','island')"
        },
        {
            isBaseLayer: true,
            displayInLayerSwitcher: true,
            opacity: 0.5
        }
    );
    
    boundaries2 = new OpenLayers.Layer.WMS(
        'boundaries',
        'http://data.rbg.vic.gov.au/geoserver/vicflora/wms',
        {
            layers: 'vicflora:cst_vic',
            transparent:true,
            styles: 'polygon_no-fill_grey-outline',
            cql_filter: "FEAT_CODE IN ('mainland','island')"
        },
        {
            isBaseLayer: false,
            displayInLayerSwitcher: false,
            opacity: 1
        }
    );
    
    bioregions = new OpenLayers.Layer.WMS(
        'Victorian bioregions',
        'http://data.rbg.vic.gov.au/geoserver/vicflora/wms',
        {
            layers: 'vicflora:vicflora_bioregion',
            styles: 'polygon_no-fill_grey-outline',
            transparent:true
        },
        {
            isBaseLayer: false,
            displayInLayerSwitcher: true,
            opacity: 1.0
        }
    );
    
    est = new OpenLayers.Layer.WMS(
        'Victorian bioregions',
        'http://data.rbg.vic.gov.au/geoserver/vicflora/wms',
        {
            layers: 'vicflora:ibra_taxon_view',
            styles: 'polygon_establishment_means',
            cql_filter: "taxon_id='" + taxonid + "'",
            transparent:true
        },
        {
            isBaseLayer: false,
            displayInLayerSwitcher: true,
            opacity: 1.0
        }
    );
    
    occurrences = new OpenLayers.Layer.WMS(
        'vicflora:vicflora_occurrence',
        'http://data.rbg.vic.gov.au/geoserver/vicflora/wms',
        {
            layers: 'vicflora:vicflora_occurrence',
            //styles: 'point_establishment_means',
            cql_filter: "taxon_id='" + taxonid + "'",
            transparent:true
        },
        {
            isBaseLayer: false,
            displayInLayerSwitcher: true,
            opacity: 1.0
        }
    );
    
    map2.events.register("click", map, function(e) {
        var position = map2.getLonLatFromPixel(e.xy);

        url = base_url + '/ajax/occurrences_from_point/' + taxonid + '/' + position.lon.toFixed(5) + '/' + position.lat.toFixed(5);
        $.getJSON(url, function(data) {
            var html;
            html = '<h3>Occurrence records</h3>';
            html += '<table class="occurrences-at-point table table-bordered table-condensed">';
            html += '<tr><th>ALA record number</th><th>Catalogue number</th><th>Longitude</th><th>Latitude</th><th>Bioregion</th><th>Occurrence status</th><th>Establishment means</th></tr>';
            
            if (data.length > 0) {
                $.each(data, function(index, item) {
                    console.log(item);
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
                        html += 'checked';
                    }
                    html += '>present</option>';
                    
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
                        if ($('table.bioregions [name^="occurrence_status["]').eq(index).val() !== data.occurrenceStatus) {
                            $('table.bioregions [name^="occurrence_status["]').eq(index).val(data.occurrenceStatus).css('background-color', '#ffff00');
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
                        if ($('table.bioregions [name^="establishment_means["]').eq(index).val() !== data.establishmentMeans) {
                            $('table.bioregions [name^="establishment_means["]').eq(index).val(data.establishmentMeans).css('background-color', '#ffff00');
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

        OpenLayers.Util.getElement("nodelist").innerHTML = 
            position.lon.toFixed(3) + ', ' + position.lat.toFixed(3);

    });
        


    map2.addLayers([boundaries, est, bioregions, occurrences, boundaries2]);
    map2.setCenter(new OpenLayers.LonLat(145.48689270019, -36.50931930542),7);

}
// sets the HTML provided into the nodelist element
function setHTML(response){
    document.getElementById('nodelist').innerHTML = response.responseText;
};
