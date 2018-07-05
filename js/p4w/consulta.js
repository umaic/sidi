var base = 'sissh';
var map;
var pL;
var fromProjection;
var toProjection;
var af = 'ajax_data.php';
var url = af + '?object=getProysMapa4w';
var urlr = af + '?object=reportesConteo4w';
var urll = urlj = url;
var urlmap = 'http://' + document.location.host + '/' + base + '/consulta/mapserver_p4w.php?';
var urlm = urlmap;
var urlficha = '/' + base + '/perfiles/4w_ficha_';
var url_filtros = af + '?object=setFiltrosProysMapa4w';
var features;
var strategies;
var _strategies;
var Style;
var mtipo;
var clear = true;
var request = true;
var filtro = 'periodo';
var h = new Date();
var y = h.getFullYear();
var id_filtro = y;
var nump_list = 15;
var mradius = 0;
var markerRadius = 5;
var id_depto = '00';
var rm_id_depto = false;
var id_tema = id_org = 0;
var periodo = '';
var myCenter;
//var myZoom = 6;
var myZoom = 0;
var zoomDepto = false;
var txt_total_proyectos = 'Total de proyectos en Colombia';
var html_proys;
var srp = -1;
var inter = -1;
var si;
var grupo = '';

$j(function(){
    url += '&si=' + si;
});

function initMap(c) {

    mtipo = c;
    fromProjection = new OpenLayers.Projection('EPSG:4326'); // World Geodetic System 1984 projection (lon/lat)
    toProjection = new OpenLayers.Projection('EPSG:900913'); // WGS84 OSM/Google Mercator projection (meters)
    OpenLayers.ImgPath = "images/openlayers/";
    strategies = [new OpenLayers.Strategy.Cluster({ distance: 50})];

    map = new OpenLayers.Map({
        div: "map",
        displayProjection: toProjection,
        //controls: [], // Para eliminar zoom con wheel and drag
        //units: "m",
        //maxResolution: auto,
        theme: 'style/openlayers/theme/default/style.min.css',
    });

    addBaseLayer(c);

    //map.addControl(new OpenLayers.Control.PanZoomBar());

    var loadingpanel = new OpenLayers.Control.LoadingPanel();
    map.addControl(loadingpanel);


}

function onPopupClose(evt) {
    selectControl.unselect(selectedFeature);
}

function onFeatureUnselect(feature) {
    /*
    map.removePopup(feature.popup);
    feature.popup.destroy();
    feature.popup = null;
    */
}

function onFeatureSelect(feature) {

    clear = true;

    var ids = '';
    for (i in feature.cluster) {
        if (i > 0) {
            ids += ',';
        }

        ids += feature.cluster[i].attributes.desc;
    }

    urll = addURLParameter(url, [['f', 'lista'], ['c', 'proy'],['id', ids],['ini', 0]]);
    addProysToList(urll);

    // Activa segundo tab
    $j('#tabs').tabs("enable", 2);

    if (i < nump_list) {
        $j('.masp').hide();
    }

}
function setDeptoCenter(lon, lat){
    // create a lat/lon object
    var myPoint = new OpenLayers.LonLat(lon, lat);
    myPoint.transform(fromProjection, map.getProjectionObject());
    // display the map centered on a latitude and longitude (Google zoom levels)
    myCenter = myPoint;
    zoomDepto = true;
    //map.setCenter(myPoint,map.getZoom() + 2);
}

function addBaseLayer(c){

    myZoom = 0;
    switch(c) {

        case 'f':

            map.maxExtent = new OpenLayers.Bounds(
                //-9099122, -471155, -7441396, 1505171  // Colombia con San Andrés
                -8099122, -471155, -7441396, 1505171  // Colombia con San Andrés
            );

            map.projection = toProjection;

            if (map.getLayersByName('Openstreetmap').length == 0) {
                //var ly = new OpenLayers.Layer.OSM("Openstreetmap");

                var resolutions= [
                  2445.9849047851562, 1222.9924523925781,
                  611.4962261962891, 305.74811309814453, 152.87405654907226,
                  76.43702827453613, 38.218514137268066, 19.109257068634033,
                  9.554628534317017, 4.777314267158508, 2.388657133579254,
                 ];

                var ly = new OpenLayers.Layer.XYZ(
                        "Openstreetmap",
                        [
                            'https://api.mapbox.com/styles/v1/mapbox/streets-v8/tiles/${z}/${x}/${y}?access_token=pk.eyJ1IjoicmF0YmlrZXIiLCJhIjoiY2loejFyM3B4MDQwcHRnbTF5MWlmOHJuNCJ9.H5A3WGVx60EdqY0hMzIMKg'
                        ],
                        {
                            transitionEffect: "resize",
                            zoomOffset: 6,
                            sphericalMercator: true,
                            resolutions: resolutions
                        }
                    );

                map.addLayer(ly);

                var wms_departamentos = new OpenLayers.Layer.WMS("Departamentos",
                    "https://geonode.umaic.org/geoserver/wms",
                    {
                        layers: "geonode:col_admbnda_adm1_igac_ochal",
                        transparent: true
                    }, {
                        opacity: 0.5,
                        singleTile: true
                    });
                map.addLayer(wms_departamentos);


            }
            else{
                //console.log(map.getLayersByName('Openstreetmap')[0]);
                map.getLayersByName('Openstreetmap')[0].setVisibility(true);
            }

            map.setBaseLayer(map.getLayersByName('Openstreetmap')[0]);

            var style = new OpenLayers.Style(
                {
                    pointRadius: "${radius}",
                    fillColor: "#133ADA",
                    label: "${label}",
                    fontColor: "#ffffff",
                    fillOpacity: "0.8",
                    strokeColor: "#6999FF",
                    strokeWidth: "5",
                    strokeOpacity: 0.5,
                    fontSize: 10
                },
                {
                    context: {
                       label: function(feature) {
                            if (feature.cluster) {

                                ids = countFeaturesCluster(feature);

                                return (ids.length > 1) ? ids.length : '';
                            }
                        },
                        width: function(feature) { return (feature.cluster) ? 2 : 1; },
                        count: function(feature)
                        {
                            if (feature.attributes.count < 2)
                            {
                                return 2 * markerRadius;
                            }
                            else if (feature.attributes.count == 2)
                            {
                                return (Math.min(feature.attributes.count, 7) + 1) *
                                (markerRadius * 0.8);
                            }
                            else
                            {
                                return (Math.min(feature.attributes.count, 7) + 1) *
                                (markerRadius * 0.6);
                            }
                        },
                        radius: function(feature)
                        {
                            //feature_count = feature.attributes.count;
                            //feature_count = feature.attributes.label*1;
                            ids = countFeaturesCluster(feature);

                            feature_count = (ids.length > 1) ? ids.length : 1;

                            if (feature_count > 500) {
                                return markerRadius * 7;
                            }
                            else if (feature_count > 400) {
                                return markerRadius * 6.4;
                            }
                            else if (feature_count > 300) {
                                return markerRadius * 6.1;
                            }
                            else if (feature_count > 200) {
                                return markerRadius * 5.8;
                            }
                            else if (feature_count > 100) {
                                return markerRadius * 5.3;
                            }
                            else if (feature_count > 90) {
                                return markerRadius * 4.8;
                            }
                            else if (feature_count > 80) {
                                return markerRadius * 4.5;
                            }
                            else if (feature_count > 70) {
                                return markerRadius * 4.1;
                            }
                            else if (feature_count > 60) {
                                return markerRadius * 3.8;
                            }
                            else if (feature_count > 50) {
                                return markerRadius * 3.5;
                            }
                            else if (feature_count > 40) {
                                return markerRadius * 3.2;
                            }
                            else if (feature_count > 30) {
                                return markerRadius * 2.9;
                            }
                            else if (feature_count > 20) {
                                return markerRadius * 2.6;
                            }
                            else if (feature_count > 10) {
                                return markerRadius * 2.3;
                            }
                            else {
                                return markerRadius * 2;
                            }
                        },
                    }
                }
            );

            Styles = new OpenLayers.StyleMap({
                    "default": style,
                    "select":{
                                fillColor: "#8aeeef",
                                strokeColor: "#32a8a9"
                            }
                    });

        break;
        case 't':
            map.maxExtent = new OpenLayers.Bounds(
               -81.7227,-4.21785,-66.8755,13.3892
            );
            map.projection = fromProjection;
        break;
    }

    myCenter = map.maxExtent.getCenterLonLat();
    //addProysToMap(c, filtro, id_filtro);

}

function countFeaturesCluster(feature) {
    var ids = [];
    for (var i=0; i < feature.cluster.length; i++) {
        var co = true;
        for (var ii=0; ii < ids.length; ii++) {
            if (feature.cluster[i].attributes.desc == ids[ii]) {
                co = false;
            }
        }

        if (co) {
            ids.push(feature.cluster[i].attributes.desc);
        }
    }

    if (ids.length > mradius) {
        mradius = ids.length;
    }
    return ids;
}
function addMapserverLayer(f,i) {

    var id_proy = urld = '';


    // Depto
    if (id_depto != '00' && !rm_id_depto) {
        urld = '&id_depto_filtro=' + id_depto;
    }

    if (map.layers.length > 0) {
        if (map.getLayersByName('Openstreetmap').length > 0) {
            map.getLayersByName('Openstreetmap')[0].setVisibility(false);
        }
        if (map.getLayersByName('Proyectos').length > 0) {
            map.getLayersByName('Proyectos')[0].setVisibility(false);
        }
        if (request) {
            if (map.getLayersByName('Consulta').length > 0) {
                map.removeLayer(map.getLayersByName('Consulta')[0], false);
            }
        }

        if (f == 'proy') {
            id_proy = i;
        }
    }


    if (request || map.getLayersByName('Consulta').length == 0) {
        if (f != undefined && f != 'todos' && i != undefined && f != '' && i != '') {
            urlm = addURLParameter(urlm, [['filtro', f],['id_filtro', i], ['id_proy', id_proy]]);
        }

        var lym = new OpenLayers.Layer.WMS('Consulta',
            urlm + urld,
            { layers: 'mpios'},
            { singleTile: true, ratio: 1}
            );

        map.addLayer(lym);
        map.setBaseLayer(lym);
    }
    else{
        var wmsl = map.getLayersByName('Consulta')[0];
        map.setBaseLayer(wmsl);
    }

}

function addProysToMap(c, k, v) {

    //if (k == 'todos') {
    url = addURLParameter(url, [['si', si]]);
    urlmap = addURLParameter(urlmap, [['si', si]]);
    if (grupo != '') {
        url = addURLParameter(url, [['grupo', grupo]]);
        urlmap = addURLParameter(urlmap, [['grupo', grupo]]);
    }

    urlm = urlmap;
    urlj = urll = url;

    // Tipo de periodo
    /*
    if (k == 'periodo') {
        urll = addURLParameter(urll, [['c', k]]);
        urll = addURLParameter(urll, [['periodo_que', getPeriodoQue()]]);
    }
    */

    urlj = urll;

    filtro = k;
    id_filtro = v;

    urll = addURLParameter(urll, [['f', 'lista']]);
    if (k != undefined && v != undefined && k != '' && v != '') {
        urll = addURLParameter(urll, [['c', k],['id', v],['ini', 0]]);
    }

    // Depto
    if (id_depto != '00' && !rm_id_depto) {
        urll = addURLParameter(urll, [['id_depto_filtro', id_depto ]]);
    }

    // SRP
    urll = addURLParameter(urll, [['srp', srp]]);

    // Interagencial
    urll = addURLParameter(urll, [['inter', inter]]);

    //map.setCenter(map.maxExtent.getCenterLonLat(), 6);
    myZoom = 0;
    if (zoomDepto) {
        myZoom = 2;
    }
    map.setCenter(myCenter, myZoom);

    if (k != 'proy') {
        addProysToList(urll);
    }

    switch(c) {
        case 'f':
            if (request || map.getLayersByName('Proyectos').length == 0) {

                urlj = addURLParameter(urlj, [['f', 'mapa']]);
                if (k != undefined && v != undefined && k != '' && v != '') {
                    urlj = addURLParameter(urlj, [['c', k],['id', v]]);
                }

                // Depto
                if (id_depto != '00' && !rm_id_depto) {
                    urlj = addURLParameter(urlj, [['id_depto_filtro', id_depto]]);
                }

                // SRP
                urlj = addURLParameter(urlj, [['srp', srp]]);

                // Interagencial
                urlj = addURLParameter(urlj, [['inter', inter]]);

                $j.getJSON(urlj, function(json){
                    var geojson = new OpenLayers.Format.GeoJSON({
                        'internalProjection': toProjection,
                        'externalProjection': fromProjection});

                    features =  geojson.read(json);
                    addLayerpL();

                    // Depto
                    if (id_depto != '00') {
                        //setDeptoCenter(id_depto, xy_depto);
                    }
                });
            }
            else {
                map.getLayersByName('Proyectos')[0].setVisibility(true);
            }
        break;

        case 't':
            addMapserverLayer(k, v);
        break;
    }

}

function addProysToList(url) {

    $j.ajax({
        url: url,
        success: function(html) {

            if (!clear) {
                html = $j('#proys > #c').html() + html
            }

            $j('#proys > #c').html(html);

            $j('.masp').show();
            if ($j('#proys div.row_proy').length < 15 || (String($j('#proys div.row_proy').length).substr(-1) != '0' && String($j('#proys div.row_proy').length).substr(-1) != '5')) {
                $j('.masp').hide();
            }

            changeTotales();

            // Eventos
            //$j('#proys').find('div.row').each(function(){

            //});
        }
    });
}

function addLayerpL() {


    if (map.getLayersByName('Proyectos').length == 1) {
        map.removeLayer(pL);
    }
    pL = new OpenLayers.Layer.Vector('Proyectos',
        {
        styleMap: Styles,
        strategies: strategies }
        );

    map.addLayer(pL);

    // Recalcula maximo radio si se ha aplicado algun filtro
    if (mradius > 0) {
        mradius = 0;
        for(var i=0; i<features.length;i++) {
            if (features[i].attributes.count > mradius) {
                mradius = features[i].attributes.count;
            }
        }
    }

    pL.addFeatures(features);

    highlightControl = new OpenLayers.Control.SelectFeature(pL,
        {
            hover: true,
            highlightOnly: true,
        });

    map.addControl(highlightControl);
    highlightControl.activate();

    var selectCtrl = new OpenLayers.Control.SelectFeature(pL,
        { clickout: true,
          onSelect: onFeatureSelect,
          onUnselect: onFeatureUnselect
        }
    );

    map.addControl(selectCtrl);
    selectCtrl.activate();

}

function addFiltertoList(h1, h2, c, id, filter_map) {
console.log(h1);console.log(h2);console.log(c);console.log(id);
    var append = true;
    var ide = 'div_tt_' + c;
    var id_span = ide + '_span_' + id;
    var cual = 0;
    var go = true;

    if (filter_map === undefined) {
        filter_map = true;
    }

    h1 = ' ' + h1;

    // Cambia cluster po resultado
    if (h2 == 'Cluster') {
        if ($j('a[rel=cluster]').html().indexOf('Resultado') !== false) {
            h2 = 'Resultado';
        }
    }

    s = ' <img src="images/p4w/remove.gif" /> ';


    $j('#titulo').find('h1').each(function(i) {
        if ($j(this).html() == h1) {
            go = false;
        }
    });

    if (go) {

        $j('#titulo').find('h2').each(function(i) {
            if ($j(this).html() == h2) {
                append = false;
                cual = i;
                h2 = '';
            }
        });

        var span = '<span id="'+id_span+'" data-c="'+c+'" data-id="'+id+'" data-divp="'+ide+'" class="eliminar">'+s+'</span>';

        if ($j('#titulo > div.tt').find('h1').html() == txt_total_proyectos) {
            $j('#titulo').find('h1').html('');
            $j('#titulo').find('h2').html('');
        }

        var span_html = span + '<h1>' + h1 + '</h1>';

        var html = '';
        var $titulo = $j('#titulo');

        if (append) {

            var $div = $titulo.find('div.tt:first').clone();
            html = '<h2>'+h2+'</h2>' + span_html;

            $div.attr('id', ide);
            $div.html(html);
            $div.appendTo('#titulo');
        }
        else {
            html = span_html;
            $j('#' + ide).append(html);
        }

        // Guarda filtro en sesion
        var url_filtros_c = url_filtros;
        if (c == 'periodo') {
            url_filtros_c = addURLParameter(url_filtros_c, [['c', c]]);
            url_filtros_c = addURLParameter(url_filtros_c, [['periodo_que', getPeriodoQue()]]);
        }

        addFilterSession(c,id,url_filtros_c);

        // Click en eliminar el filtro
        $j('span#' + id_span).click(function(){

            clear = true;
            request = true;

            if (c == 'id_depto_filtro') {
                rm_id_depto = true;

                $j('#depto').val('00|0,0');
            }

            var _id = $j(this).attr('data-id');
            var _c = $j(this).attr('data-c');
            var _divp = $j(this).attr('data-divp');

            removeFilterSession(_c,_id);

            if (_c == 'id_depto_filtro') {
                zoomDepto = false;
            }

            $j(this).next().remove();  // Remueve h1
            $j(this).remove(); // Remueve span

            var $div_parent = $j('#' + _divp);
            if ($div_parent.find('h1').length == 0) {
                $div_parent.remove();
            }

            if ($j('#titulo').find('div.tt').length == 1) {
                $j('#titulo').find('h1').html(txt_total_proyectos);
                $j('#titulo').find('h2').html('');
                $j('#titulo').find('span').html('');
            }

            filterMapLive();

        });

        if (filter_map) {
            filterMapLive();
        }
    }
}

function filterMapLive() {
    // Filtra el mapa live
    setTimeout(function(){
        addProysToMap(mtipo,'','');
        //changeTotales();
    }, 500);
}

function changeTitulo(h1, h2, c, id) {
    var append = true;
    var ide = 'div_tt_' + c;
    var cual = 0;

    s = '[ Eliminar Filtro ]';

    $j('#titulo, #resumen').show('slow');
    $j('#titulo').find('h2').each(function(i) {
        if ($j(this).html() == h2 || $j(this).html() == 'Proyectos') {
            append = false;
            cual = i;
        }
    });

    var _tobj = (append) ? $j('#titulo > div.tt:first').clone() : $j('#titulo > div.tt:eq(' + cual + ')');

    _tobj.find('h2').html(h2);
    _tobj.find('h1').html(h1);
    _tobj.find('span').html(s);
    _tobj.attr('id', ide);

    _tobj.find('span').click(function(){

        clear = true;
        request = true;

        if (c == 'id_depto_filtro') {
            rm_id_depto = true;
            $j('#depto').val('00|0,0');
            //addProysToMap(mtipo, '', '');
        }
        else {
            //addProysToMap(mtipo, '-' + c, id);
        }

        if ($j('#titulo > div.tt').length > 1) {
            $j('#' + ide).remove();
        }
        else {
            $j('#titulo').find('h1').html('Colombia');
            $j('#titulo').find('h2').html('Proyectos');
            $j('#titulo').find('span').html('');
        }

        //changeTotales();
    });

    if (append) {
        _tobj.appendTo('#titulo');
    }
}

//function changeTotales(c, id) {
function changeTotales() {
    $j('#npro_h2').html($j('#proys').find('#4w_np').val());
    $j('#norg_h2').html($j('#proys').find('#4w_no').val());
    $j('#nimp_h2').html($j('#proys').find('#4w_ni').val());
    $j('#nbenef_h2').html($j('#proys').find('#4w_nb').val());
    $j('#nbenef_h2').attr('title', 'Hombres:' + $j('#proys').find('#4w_nb_h').val() + ' Mujeres:' + $j('#proys').find('#4w_nb_m').val());
    $j('#npres_h2').html($j('#proys').find('#4w_npres').val());
    $j('#npres_gob_h2').html($j('#proys').find('#4w_npres_gob').val());
    $j('#npres_sin_donante_h2').html($j('#proys').find('#4w_npres_sin_donante').val());
    $j('#npres_total_h2').html($j('#proys').find('#4w_npres_total').val());

    var tops = ['cluster','donantes','deptos','ejecutoras'];

    for (var i=0; i<tops.length; i++) {

        t = tops[i];
        html = $j('textarea#resumen_top_' + t).val();
        $div = $j('#p4w').find('div#top_' + t);

        if (html != '') {
            $div.show();
            $div.html(html);
        }
        else {
            $div.hide();
        }
    }

    // Actualiza filtros
    if (request) {

        $j('div.f').not('div.f.c').each(function() {
            var id = $j(this).attr('id');
            var html = $j('div#fcu_' + id).html();

            // Si es filtro de departamento o municipio solo actualiza cuando hay datos
            if (id != 'departamento' && id != 'municipio') {
                $j('div#' + id).find('div.c').html(html);
            }
            else if ((id == 'departamento' || id == 'municipio') && html.indexOf('No existe') == -1) {
                $j('div#' + id).find('div.c').html(html);
            } 
        });

        addEventosFiltros();
    }
}

function addEventosGrupos() {

    $j('.ingresar').click(function(){

        var id = $j(this).attr('id');

        // Filtro inicial
        addFiltertoList('Vigentes en: ' + y, 'Periodo', 'periodo', y, false);

        // 4w tiene combo de grupos arriba-derecha, si es global populada en p4w.php
        if (si == '4w') {
            $j('#m_grupos_select').val(id);
            grupo = id;
            //grupoProys(si);
        }
        else {

            ida = id.split(',');
            fa = $j(this).data('filtro').split(',');

            if ($j("#div_tt_cluster").length > 0) {
                removeFilterSession("cluster", 0);
                $j("#div_tt_cluster").remove()
            }
            if (fa[0] != "des") {
                for (i in ida) {
                    addFiltertoList(fa[i], "Resultado", "cluster", ida[i], false)
                }
            }
        }

        filterMapLive();

        $j('div#grupos').hide();
        $j('#todo, #map').show();

    });
}

function grupoProys(sip) {

    request = clear = true;

    si = sip;
    /*
    //url = addURLParameter(url, [['si', sip]]);
    $j('.f_grupo').removeClass('active');
    $j('.f_grupo img').hide();
    $j('#' + sel).addClass('active');
    $j('#' + sel + ' img ').show();
    */
    addProysToMap(mtipo,'','');
}

function clickTodos() {
    $j('.masp').show();
    addProysToMap(mtipo,'todos','1');
}

function addEventos(c) {

    addEventosFiltros()

    // Link refrescar
    $j('#applyFiltros').click(function(){

        grupo = $j("#m_grupos_select").val();
        addProysToMap(mtipo,'','');
        changeTotales();

        return false;
    });

    $j('#btn_srp').click(function(){

        $div = $j(this).closest('div');
        if ($div.hasClass('srp_off')) {
            srp = 1;
            $div.removeClass('srp_off');
            $div.addClass('srp_on');

            // Filtro a session
            addFilterSession('srp',1,url_filtros);
        }
        else {
            srp = -1;
            $div.addClass('srp_off');
            $div.removeClass('srp_on');
            removeFilterSession('srp',1);
        }


        addProysToMap(mtipo,'','');
        changeTotales();

        return false;
    });

    $j('#btn_inter').click(function(){

        $div = $j(this).closest('div');
        if ($div.hasClass('inter_off')) {
            inter = 1;
            $div.removeClass('inter_off');
            $div.addClass('inter_on');

            // Filtro a session
            addFilterSession('inter',1,url_filtros);
        }
        else {
            inter = -1;
            $div.addClass('inter_off');
            $div.removeClass('inter_on');
            removeFilterSession('inter',1);
        }


        addProysToMap(mtipo,'','');
        changeTotales();

        return false;
    });

    // Link todos derecha
    $j('#todos').click(function(){
        clickTodos();
    });

    // Orden lista derecha
    $j('#proys_order').change(function(){
        urll = addURLParameter(urll, [['order', $j(this).val()]]);
        addProysToList(urll);
    });

    // Numero proys lista derecha
    $j('#proys_limit').click(function(){
        urll = addURLParameter(urll, [['limit', 'no']]);
        addProysToList(urll);
    });

    // Mas proyectos
    $j('.masp').show();
    $j('.masp > .boton').click(function(){
        clear = false;
        urll = addURLParameter(urll, [['ini', $j('#proys').find('div.row_proy').length]]);
        addProysToList(urll);
    });

    // CSV Proyectos derecha
    //$j('#proys .pdf').click(function(){ location.href = af + '?object=reporteProyectos4w'; });
    $j('#proys .csv').click(function(){
        location.href = '/sissh/export_data.php?csv2xls&nombre_archivo=proyectos_4w&csv_path=/sissh/static/4w/proyectos_4w.csv';
    });

//    $j('#proys .pdf').click(function(){ location.href = af + '?object=fichaProyectos4w'; });

    // Hide all filtros
    $j('#filtros div.g a').click(function(){
        var sel = '#' + $j(this).attr('rel');
        $j('div.f').not(sel).hide();
        $j(sel).slideToggle('slow');
    });

    // Hide filtro
    $j('#fmenu ul li.f a').each(function(){
        $j(this).click(function(){
            $j('div.f').each(function() {
                $j(this).hide();
            });

            $j('div#' + $j(this).attr('rel')).show();
        });
    });

    // Show reportes
    $j('div#reportes a.ft').click(function() {

        var $c = $j(this).parent('div#reportes').find('div.c');
        var $m = $c.find('div.row:contains("Municipal")');

        // Si no hay filtro de departamento no muestra opcion de municipios
        /*
        if ($j('#titulo').find(':contains("Departamento")').length == 0) {
            $m.hide();
        }
        else {
            $m.show();
        }
        */

        $c.toggle('slow')
    });

    $j('div#reportes').find('div.col').each(function() {
        var id = $j(this).attr('id');
        $j(this).find('div.fila').each(function(){
            $j(this).click(function(){
                $j(this).parents('div.col').find('div.fila').removeClass('selected');
                $j(this).toggleClass('selected');

                // Check para mostrar boton generar
                checkParamsReporte();
            });
        })
    });

    // Tipo de mapa
    $j('.mapa_f').click(function(){

        mtipo = 'f';

        $j('.mapa_t').removeClass('active');
        $j(this).addClass('active');
        $j('#map').addClass('mapfull');

        addBaseLayer(mtipo);
        $j('#mapas_o').find('.save').hide('slow');
    });

    $j('.mapa_t').click(function(){
        mtipo = 't';
        $j('.mapa_f').removeClass('active');
        $j(this).addClass('active');
        $j('#map').removeClass('mapfull');
        addBaseLayer(mtipo);
        $j('#mapas_o').find('.save').show('slow');
    });


    // Guardar mapa
    $j('#mapas_o .save').click(function(){ location.href = 'consulta/mapserver_download_img.php' });

    // Cerrar
    $j('.close').click(function(){ $j(this).parent('div.p').hide() });

    // Nuevo menu filtros
    $j('#footer div.g a.ft').each(function(){
        $j(this).click(function(){ $j('#' + $j(this).attr('rel')).slideToggle('slow'); });

    });

    // Muestra detalle del proyecto
    $j('#proys').on('click', 'div.t', function(){

        clear = true;
        request = true;
        var pid = $j(this).attr('id');

        $div_c = $j('#proys > #c');

        html_proys = $div_c.html();
        addProysToMap(mtipo, 'proy', pid);
        $div_c.html($j('div#ficha_' + pid).html());
        $j('.masp').hide();
        $j('.proys_order').hide();

        // Agrega opción regresar en la lista
        $div_c.prepend('<div><br /><a id="regresar_lista" onClick="regresarLista()" href="#">&laquo; Regresar a la lista</a></div>');

    });

    $j('#proys').on('click', 'span.s', function(){
        clear = true;
        request = true;
        var pid = $j(this).attr('id');
        addProysToMap(mtipo, 'ejecutora', pid)
        changeTitulo($j(this).html(), 'Ejecutora');
    });

    $j('#proys').on('click', 'span.tema', function(){
        clear = true;
        request = true;
        var pid = $j(this).attr('id');
        var _c = $j(this).attr('class').split(' ')[1];
        var _cl = [0, 'si', 'cluster'];
        var _ct = [0, 'UNDAF', 'Cluster'];

        addProysToMap(mtipo, _cl[_c], pid)
        changeTitulo($j(this).html(), _ct[_c]);
    });
}

function addEventosFiltros() {
    var ts = [];
    ts['cluster'] = 'Cluster';
    ts['si'] = 'UNDAF';
    ts['ejecutora'] = 'Ejecutora';
    ts['implementadora'] = 'Implementadora';
    ts['donante'] = 'Donante';
    ts['estado'] = 'Estado';
    ts['periodo'] = 'Periodo';
    ts['departamento'] = 'Departamento';
    ts['municipio'] = 'Municipio';
    ts['acuerdo'] = 'Acuerdos de Paz';

    for (var id in ts) {
        $j('#filtros').find('div.f' + id).each(function(){
            $j(this).click(function() {

                clear = true;
                request = true;
                $j('div.row').removeClass('selected');
                $j(this).addClass('selected');

                var _i = $j(this).attr('class').split(' ')[1].substr(1);

                if (_i == 'departamento') {
                    var _i = 'id_depto_filtro';
                    var _id = _ii = '';

                    var h2 = 'Departamento';
                    var h1 =  $j(this).find('span.nom').html();

                    id_depto = $j(this).attr('id');

                    if (mtipo == 'f') {
                        setDeptoCenter($j(this).attr('lon'),$j(this).attr('lat'));
                    }

                    // Titulo Ficha
                    $j('#btn_fpdf > a').html('Ficha Departamental');

                    addFiltertoList(h1, h2, 'id_depto_filtro', id_depto);
                }
                else if (_i == 'municipio') {
                    var _i = 'id_mun_filtro';
                    var _id = _ii = '';

                    var h2 = 'Municipio';
                    var h1 =  $j(this).find('span.nom').html();

                    id_mun = $j(this).attr('id');

                    if (mtipo == 'f') {
                        setDeptoCenter($j(this).attr('lon'),$j(this).attr('lat'));
                    }
                    
                    addFiltertoList(h1, h2, 'id_mun_filtro', id_mun);
                }
                else {
                    zoomDepto = false;
                    var _i = $j(this).attr('class').split(' ')[1].substr(1);
                    var _id = $j(this).attr('id');
                    var h1 =  $j(this).find('span.nom').html();
                    if (_i == 'periodo') {
                        periodo = _id;
                        var _pq = {'i':'Inician','f':'Finalizan','v':'Vigentes'};
                        h1 = _pq[getPeriodoQue()] + ' en: ' + h1;
                    }

                    addFiltertoList(h1, ts[_i], _i, _id);

                }

                hideFiltros();
            })
        });
    }
}

function applyFiltros(mtipo,i,id,h1,h2,p1,p2) {
    addProysToMap(mtipo,i,id);
    changeTitulo(h1, h2, p1, p2);
}

function hideFiltros() {
    $j('#filtros div.f').slideUp('slow');
}
function checkParamsReporte() {

    var sub = false;
    var n = 0;
    var params = [['si', si]];

    $j('div#reportes').find('div#sub').hide();

    $j('div#reportes').find('div.col').each(function() {
        var id = $j(this).attr('id');
        $j(this).find('div.fila').each(function(){
            if ($j(this).hasClass('selected')) {
                n++;
                params.push([id.substr(1), $j(this).attr('title')]);
            }
        });

    });

    // si se han seleccionado filas,columnas y contar
    if (n >= 3) {

        urlr = addURLParameter(urlr, params);
        $j('div#reportes').find('div#sub > div.csv').unbind().click(function() {
                //location.href = urlr;
                var hh2 = $j('#reportes h3').html();
                $j('#reportes h3').html('<img src="images/p4w/loading.gif" />&nbsp;Generando reporte....</div>');
                $j.ajax(
                    {
                        url: urlr,
                        success: function() {
                            $j('#reportes h3').html(hh2);
                            location.href = '/sissh/export_data.php?csv2xls&nombre_archivo=4w_reporte_conteo&csv_path=/tmp/4w_reporte_conteo.csv';
                        }
                    });
        });
        $j('div#reportes').find('div#sub').show();
    }
}

function filterList(event, id) {

    texto = $j(event.target).val();

	keyNum = event.keyCode;

	if (keyNum == 8){  //Backspace
		texto = texto.slice(0, -1);  //Borra el ultimo caracter
	}
	else{
		keyChar = String.fromCharCode(keyNum);
		texto +=  keyChar;
	}

    var re = new RegExp(texto, 'i');
    $j(event.target).parents('div#' + id).find('div.fila').each(
        function () {
            $j(this).show();
            if (!$j(this).find('div.nom').text().match(re)) {
                $j(this).hide();
            }
        });
}

function getPeriodoQue() {
    return $j('input[name=periodo_que]:checked').val();
}

function regresarLista() {
    $j('#proys').find('#c').html(html_proys);

    return false;
}

function removeFilterSession(c,id) {
    // Elimina filtro en sesion
    $j.ajax({
        'url' : url_filtros + '&c=-' + c + '&id=' + id
    });
}

function addFilterSession(c,id, url) {
    $j.ajax({
        'url' : url + '&c=' + c + '&id=' + id
    });
}
