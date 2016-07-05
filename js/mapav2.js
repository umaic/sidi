$(function(){ 
    
    var resolutions= [
                      9783.939619140625,
                      4891.9698095703125,
                      2445.9849047851562, 1222.9924523925781,
                      611.4962261962891, 305.74811309814453, 152.87405654907226,
                      76.43702827453613, 38.218514137268066, 19.109257068634033,
                     ];
    
    var centroColombia = ol.proj.transform(
        [-70.963384, 3.370786], 'EPSG:4326', 'EPSG:3857');

    var view = new ol.View({
        center: centroColombia,
        zoom: 1,
        resolutions: resolutions,
        //extent: [-8599122, -471155, -7441396, 1505171]

    });
    
    var textFill = new ol.style.Fill({
        color: '#333333'
    });
    var textStroke = new ol.style.Stroke({
        color: 'rgba(0, 0, 0, 0.6)',
        width: 2
    });

    var container = document.getElementById('popup');
    var content = document.getElementById('popup-content');

    /**
     * Create an overlay to anchor the popup to the map.
     */
    var overlay = new ol.Overlay(/** @type {olx.OverlayOptions} */ ({
        element: container,
        autoPan: true,
        autoPanAnimation: {
            duration: 250
        }
    }));

    $('.select2').select2();


    map = new ol.Map({
        layers: [
            new ol.layer.Vector({
                source: new ol.source.Vector({
                    url: 'static/topojson/deptos_topo.json',
                    format: new ol.format.TopoJSON()
                }),
                style: function(feature, resolution) {
                    styleObj = {
                        stroke: new ol.style.Stroke({color: '#cccccc', width: 1}),
                        fill: new ol.style.Stroke({color: '#fafafa', width: 1}),
                        /*
                        text: new ol.style.Text({
                            text: feature.getProperties().admin1Name,
                            fill: textFill,
                            //stroke: textStroke
                        })
                        */
                    }
                    return [new ol.style.Style(styleObj)]
                  }
            })
        ],
        target: document.getElementById('map'),
        overlays: [overlay],
        controls: ol.control.defaults({
            attributionOptions: /** @type {olx.control.AttributionOptions} */ ({
                collapsible: false
            })
        }),
        view: view
    });
    
    var highlightStyleCache = {};

    var featureOverlay = new ol.layer.Vector({
        source: new ol.source.Vector(),
        map: map,
        style: function(feature, resolution) {
          var text = resolution < 5000 ? feature.get('name') : '';
          if (!highlightStyleCache[text]) {
            highlightStyleCache[text] = new ol.style.Style({
              stroke: new ol.style.Stroke({
                color: '#0044AA',
                width: 1
              }),
              fill: new ol.style.Fill({
                color: 'rgba(0,102,255,0.1)'
              }),
              text: new ol.style.Text({
                font: '12px Calibri,sans-serif',
                text: text,
                fill: new ol.style.Fill({
                  color: '#000'
                }),
                stroke: new ol.style.Stroke({
                  color: '#f00',
                  width: 3
                })
              })
            });
          }
          return highlightStyleCache[text];
        }
      });

    
    var cursor = '';
    var highlight;
    var displayFeatureInfo = function(evt) {

        var pixel = map.getEventPixel(evt.originalEvent);

        var feature = map.forEachFeatureAtPixel(pixel, function(feature) {
            var coordinate = evt.coordinate;
            var hdms = ol.coordinate.toStringHDMS(ol.proj.transform(
                        coordinate, 'EPSG:3857', 'EPSG:4326'));

            content.innerHTML = feature.getProperties().admin1Name;
            overlay.setPosition(coordinate);
            return feature;
        });

        if (feature !== highlight) {
            if (highlight) {
                featureOverlay.getSource().removeFeature(highlight);
            }
            if (feature) {
                featureOverlay.getSource().addFeature(feature);
                cursor = 'pointer';
            }
            else {
                overlay.setPosition(undefined);
                cursor = '';
            }
            highlight = feature;
        }
        
        map.getTarget().style.cursor = cursor;

    };

    map.on('pointermove', function(evt) {
        if (evt.dragging) {
            return;
        }
        displayFeatureInfo(evt);
    });
    
    map.on('click', function(evt) {
        
        var feature = map.forEachFeatureAtPixel(evt.pixel,
                function(feature, layer) {
                    return feature;
                });

        if (feature) {
            onFeatureSelect(feature.getId());
        }
    });

    
    /*
    map.on("pointermove", function (evt) {
        var hit = this.forEachFeatureAtPixel(evt.pixel,
                function(feature, layer) {
                    var coordinate = evt.coordinate;
                    var hdms = ol.coordinate.toStringHDMS(ol.proj.transform(
                                coordinate, 'EPSG:3857', 'EPSG:4326'));

                    content.innerHTML = feature.getProperties().admin1Name;
                    overlay.setPosition(coordinate);
                    return true;
                }); 
        if (hit) {
            this.getTarget().style.cursor = 'pointer';
        } else {
            this.getTarget().style.cursor = '';
        }

    });   
    */

});

function onFeatureSelect(id) {
    $('#id_deptos').val(id).trigger("change");

    $.ajax({
        url: 'admin/ajax_data.php?object=comboBoxMunicipio&id_name=id_mun_depto&multiple=0&separador_depto=0&titulo=0&id_deptos=' + id,
        success: function(html){ 
            $('#div_mun').html(html);

            setTimeout(function(){ $('#id_mun_depto').select2() }, 200);
            
        }
    });
    

}
