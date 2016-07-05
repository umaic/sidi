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
var features;
var strategies;
var _strategies;
var Style;
var mtipo;
var clear = true;
var request = false;
var filtro = id_filtro = '';
var nump_list = 15;
var mradius = 0;
var id_depto = '00';
var rm_id_depto = false;
var si = '4w_ehp';
var id_tema = id_org = 0;
var periodo = '';

url += '&si=' + si;

function initMap(c) {
   
    $j('#loading').show();
    addEventos();
    
    mtipo = c;
    fromProjection = new OpenLayers.Projection('EPSG:4326'); // World Geodetic System 1984 projection (lon/lat) 
    toProjection = new OpenLayers.Projection('EPSG:900913'); // WGS84 OSM/Google Mercator projection (meters) 
    OpenLayers.ImgPath = "images/openlayers/";
    strategies = [new OpenLayers.Strategy.Cluster()];

    map = new OpenLayers.Map({
        div: "map",
        displayProjection: toProjection, 
        controls: [], 
        //units: "m",
        //maxResolution: auto,
        theme: null
    });
    

    addBaseLayer(c);

    map.addControl(new OpenLayers.Control.PanZoomBar());
    /*
    map.addControl(new OpenLayers.Control.MousePosition(
				{
					div: document.getElementById('mapMousePosition'),
					numdigits: 5
				}));
    //var switcher =  new OpenLayers.Control.LayerSwitcher();
    //map.addControl(switcher);
    map.addControl(new OpenLayers.Control.Navigation({'zoomWheelEnabled': false}));

    */
    controls = map.getControlsByClass('OpenLayers.Control.Navigation');
    for(var i=0;i<controls.length; ++i) {
        controls[i].disableZoomWheel();
    }
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
    
    if (i < nump_list) {
        $j('.masp').hide();
    } 

}
function setDeptoCenter(lon, lat){
    // create a lat/lon object
    var myPoint = new OpenLayers.LonLat(lon, lat);
    myPoint.transform(fromProjection, map.getProjectionObject());
    
    // display the map centered on a latitude and longitude (Google zoom levels)
    map.setCenter(myPoint, 8);
}

function addBaseLayer(c){
    
    switch(c) {
        case 'f':
            
            map.maxExtent = new OpenLayers.Bounds(
                -9181230, -896898, -7004304, 1602898  // Colombia con San Andrés
                //-8481230, -656898, -7004304, 1602898  // Colombia con San Andrés
            );
            
            map.projection = toProjection;

            if (map.getLayersByName('Openstreetmap').length == 0) {
                var ly = new OpenLayers.Layer.OSM("Openstreetmap");
                /*
                var ly = new OpenLayers.Layer.CloudMade("Openstreetmap", {
                    key: '312ed5af733d44eab283e525c00219dd',
                    styleId: 58492
                });
                */
                
                map.addLayer(ly);
            }
            else{
                map.getLayersByName('Openstreetmap')[0].setVisibility(true);
            }
            
            map.setBaseLayer(map.getLayersByName('Openstreetmap')[0]);

            var style = new OpenLayers.Style(
                {
                    pointRadius: "${radius}", 
                    fillColor: "#3366cc",
                    label: "${label}",
                    fontColor: "#ffffff",
                    fillOpacity: "0.8",
                    strokeColor: "#b8c5de",
                    strokeWidth: "${width}",
                    strokeOpacity: 0.8,
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
                        radius: function(feature) {
                            //return Math.min(feature.cluster.length, 2) + 5;
                            var rds = countFeaturesCluster(feature).length;
                            var mr = 8;  // Radio menor
                            var myr = 20; // Radio mayor
                            var rd = (rds*myr)/(mradius*0.8);  // Quitamos el 20% a mradius para equlibrar distribucion

                            if (rd < mr) {
                                rd = mr;
                            }
                            else if (rd > myr) {
                                rd = myr;
                            }
                            
                            return rd;

                            //return Math.min(Math.ceil(feature.cluster.length*(20/mradius)), 7);
                        }
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
    
    map.restrictedExtent = map.maxExtent;
    addProysToMap(c, filtro, id_filtro);
    
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
    //if ($j('#depto :selected').html() != 'Nacional') {
    if (id_depto != '00' && !rm_id_depto) {
        //urld = '&id_depto_filtro=' + $j('#depto').val().split('|')[0];
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
    urlm = urlmap;
    urlj = urll = url;

    filtro = k;
    id_filtro = v;

    switch(c) {
        case 'f':
            if (request || map.getLayersByName('Proyectos').length == 0) {
                        
                urlj = addURLParameter(urlj, [['f', 'mapa']]);
                if (k != undefined && v != undefined && k != '' && v != '') {
                    urlj = addURLParameter(urlj, [['c', k],['id', v]]);
                }
        
                // Depto 
                if (id_depto != '00' && !rm_id_depto) {
                    urlj += '&id_depto_filtro=' + id_depto;
                }

                $j.getJSON(urlj, function(json){
                    var geojson = new OpenLayers.Format.GeoJSON({
                        'internalProjection': toProjection,
                        'externalProjection': fromProjection});

                    features =  geojson.read(json);
                    addLayerpL();
                
                    // Depto 
                    if (id_depto != '00') {
                        setDeptoCenter(id_depto, xy_depto);
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

    urll = addURLParameter(urll, [['f', 'lista']]);
    if (k != undefined && v != undefined && k != '' && v != '') {
        urll = addURLParameter(urll, [['c', k],['id', v],['ini', 0]]);
    }
        
    // Depto 
    if (id_depto != '00' && !rm_id_depto) {
        urll = addURLParameter(urll, [['id_depto_filtro', id_depto ]]);
    }
    
    map.setCenter(map.maxExtent.getCenterLonLat(), 6);
    
    if (k != 'proy') {
        addProysToList(urll);
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
            if (String($j('#proys div.row_proy').length).substr(-1) != '0' && String($j('#proys div.row_proy').length).substr(-1) != '5') {
                $j('.masp').hide();
            } 

            changeTotales();

            // Eventos
            $j('#proys').find('div.row').each(function(){
                
                $j(this).find('div.t').click(function(){
                    
                    clear = true;
                    request = true;
                    var pid = $j(this).attr('id');
                    
                    addProysToMap(mtipo, 'proy', pid); 
                    $j('#proys > #c').html($j('div#ficha_' + pid).html());
                    $j('.masp').hide();
                    $j('.proys_order').hide();
                    $j('#titulo, #resumen').hide('slow');

                });

                $j(this).find('span.s').click(function(){ 
                    clear = true;
                    request = true;
                    var pid = $j(this).attr('id');
                    addProysToMap(mtipo, 'ejecutora', pid) 
                    changeTitulo($j(this).html(), 'Ejecutora');
                });

                $j(this).find('span.tema').click(function(){ 
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
            );
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
    
    $j('#loading').hide();
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
            addProysToMap(mtipo, '', '');
        }
        else {
            addProysToMap(mtipo, '-' + c, id);
        }

        if ($j('#titulo > div.tt').length > 1) {
            $j('#' + ide).remove();
        }
        else {
            $j('#titulo').find('h1').html('Colombia');
            $j('#titulo').find('h2').html('Proyectos');
            $j('#titulo').find('span').html('');
        }

        changeTotales();
    });

    if (append) {
        _tobj.appendTo('#titulo');
    }
}

//function changeTotales(c, id) {
function changeTotales() {
    $j('div.npro > h2').html($j('#proys').find('input[name="np"]').val());
    $j('div.norg > h2').html($j('#proys').find('input[name="no"]').val());
    $j('div.nbenef > h2').html($j('#proys').find('input[name="nb"]').val());

    // Actualiza filtros
    if (request) {
        
        //$j('div.f').not('div#ubicacion,div.f.c').each(function(){ 
        $j('div.f').not('div.f.c').each(function(){ 
            $j('div#' + $j(this).attr('id')).find('div.c').html($j('div#fcu_' + $j(this).attr('id')).html());
        });

        addEventosFiltros();
    }
}

function addEventosGrupos() {

    // Grupos
    $j('#grupo_ehp').click(function() {
        $j('div#grupos').hide();
        $j('#todo, #map').show();
    });
    $j('#grupo_undaf').click(function() {
        $j('div#grupos').hide();
        $j('#todo, #map').show();
        grupoProys('f_grupo_undaf', 'undaf');
    });
    $j('#grupo_otros').click(function() {
        $j('div#grupos').hide();
        $j('#todo, #map').show();
        grupoProys('f_grupo_otros', '4w_otros');
    });

    $j('#f_grupo_ehp').click(function() {
        if (!$j(this).hasClass('active')) {
            grupoProys('f_grupo_ehp', '4w_ehp');
        }
    });
    
    $j('#f_grupo_undaf').click(function() {
        if (!$j(this).hasClass('active')) {
            grupoProys('f_grupo_undaf', 'undaf');
        }
    });
    
    $j('#f_grupo_otros').click(function() {
        if (!$j(this).hasClass('active')) {
            grupoProys('f_grupo_otros', '4w_otros');
        }
    });
}

function grupoProys(sel, sip) {
    
    request = clear = true;
    
    si = sip;

    //url = addURLParameter(url, [['si', sip]]);
    $j('.f_grupo').removeClass('active');
    $j('.f_grupo img').hide();
    $j('#' + sel).addClass('active');
    $j('#' + sel + ' img ').show();
    addProysToMap(mtipo,'','');
}
   
function addEventos(c) {

    addEventosFiltros()
    
    // Link todos derecha
    $j('#todos').click(function(){ 
        $j('.masp').show();
        addProysToMap(mtipo,'todos','1'); 
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
    $j('#proys .csv').click(function(){ location.href = af + '?object=reporteProyectos4w'; });
    
    $j('#proys .pdf').click(function(){ location.href = af + '?object=fichaProyectos4w'; });

    // Ocultar lista de proyectos
    $j('#s_listap').find('div.menu').click(function() {
        $j('#titulo, #resumen, #proys, .tt').slideToggle(); 
    });
   
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
    $j('div#reportes a.ft').click(function() { $j(this).parent('div#reportes').find('div.c').toggle('slow') });

    $j('div#reportes').find('div.col').each(function() { 
        var id = $j(this).attr('id');
        $j(this).find('div.row').each(function(){ 
            $j(this).click(function(){
                $j(this).parents('div.col').find('div.row').removeClass('selected');
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

    // Slider presupuesto
    var maxp = 3000000;
    var pres = $j('.npres').find('h2').html() * 1;
    $j("#slider-range").slider({
			range: true,
			min: 0,
			max: maxp,
            step: 200000,
			values: [ 0, maxp ],
			slide: function( event, ui ) {
                var rg =  ui.values[0] + " - " + ui.values[1];
				$j("#amount").html(rg);
			},
			stop: function( event, ui ) {
                var rg =  ui.values[0] + " - " + ui.values[1];
                clear = true;
                addProysToMap(mtipo, 'pres', rg);
			}
		});
        $j("#amount").html( $j("#slider-range").slider("values", 0 ) +
			" - " + $j("#slider-range").slider("values", 1 ) );
}

function addEventosFiltros() {
    var ts = [];
    ts['cluster'] = 'Cluster';
    ts['si'] = 'UNDAF';
    ts['ejecutora'] = 'Ejecutora';
    ts['donante'] = 'Donante';
    ts['estado'] = 'Estado';
    ts['periodo'] = 'Periodo';
    ts['ubicacion'] = 'Departamento';

    for (var id in ts) {
        $j('#filtros').find('div.f' + id).each(function(){ 
            $j(this).click(function() {
                
                clear = true;
                request = true;
                $j('div.row').removeClass('selected');
                $j(this).addClass('selected');

                var _i = $j(this).attr('class').split(' ')[1].substr(1); 
                
                if (_i == 'ubicacion') {
                    var _i = 'id_depto_filtro';
                    var _id = _ii = '';
                    var _s = $j(this).attr('id').split('|');
                    var h2 = 'Municipio';

                    id_depto = _s[0];
                    xy_depto = _s[1];
                    
                    if (mtipo == 'f') {
                        _c = _s[1].split(',');
                        setDeptoCenter(_c[0], _c[1]); 
                    }

                    // Titulo Ficha 
                    $j('#btn_fpdf').html('Ficha Departamental');
                    addProysToMap(mtipo,'','');
                    changeTitulo($j(this).find('option:selected').html(), h2, 'id_depto_filtro', '');

                }
                else {
                    var _i = $j(this).attr('class').split(' ')[1].substr(1); 
                    var _id = $j(this).attr('id');
                    
                    if (_i == 'periodo') {
                        periodo = _id;
                    }

                    addProysToMap(mtipo, _i, _id);
                    changeTitulo($j(this).find('div.nom').html(), ts[_i], _i, _id);
                }
                    
                hideFiltros();
            })
        });
    }
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
        $j(this).find('div.row').each(function(){
            if ($j(this).hasClass('selected')) {
                n++;
                params.push([id.substr(1), $j(this).attr('title')]);
            }
        });
        
    });

    if (n >= 3) {
        
        // Filtro depto
        if (id_depto != '00') {
            params.push(['idu', id_depto]);
        }
        
        if (periodo != '') {
            params.push(['periodo', periodo]);
        }

        urlr = addURLParameter(urlr, params);
        $j('div#reportes').find('div#sub > div.csv').click(function() {
                //location.href = urlr;
                var hh2 = $j('#reportes h3').html();
                $j('#reportes h3').html('<img src="images/p4w/loading.gif" />&nbsp;Generando reporte....</div>');
                $j.ajax(
                    { 
                        url: urlr, 
                        success: function() { 
                            $j('#reportes h3').html(hh2);
                            location.href = '/tmp/4w_reporte_conteo.csv';
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
    $j(event.target).parents('div#' + id).find('div.row').each(
        function () {
            $j(this).show();
            if (!$j(this).find('div.nom').html().match(re)) {
                $j(this).hide();
            }
        });
}

