var bounds;
var map;

var last_uri_segment = location.href.substr(location.href.lastIndexOf('/')+1);
var res_number;
if (last_uri_segment.substr(0, 3) === 'VIC') {
    res_number = last_uri_segment;
}

var map2;

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
        


    map.addLayers([capad]);
    
    map.setCenter(new OpenLayers.LonLat(16165659.235725,-4361228.5852319),6);

}
// sets the HTML provided into the nodelist element
function setHTML(response){
    document.getElementById('nodelist').innerHTML = response.responseText;
};
