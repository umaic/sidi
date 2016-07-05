function tn(event, cls) {
    $j(event.target).parents('div.' + cls).find(':checkbox').each(
        function () {
            $j(this).attr('checked', !$j(this).attr('checked'));
            $j(this).parent('div.checkbox').toggleClass('selected');
        });
}
function listarMpios(event, id_depto, id_div, inner){

    var tmpio;
    var sd = $j('#cob_no_nac').find('div.deptos');
    var dm = $j('div.mpios:first');
    var hm = dm.height();
    
    var clone = false;
    
    $j('div.mpios').each(function () { $j(this).hide(); });
    if ($j('div#mpio_' + id_depto).length == 0) {
        clone = true;
        sm = dm.clone(true);
        sm.attr('id', 'mpio_' + id_depto);

        sm.find('div#' + inner).show();
        sm.find('div#' + inner).attr('id', inner + '_' + id_depto);
        inner += '_' + id_depto;
        
        // Titulo mpio
        sm.find('h2').html(sm.find('h2').html() + ' ' + $j(event.target).parents('div.checkbox').find('label').html());
    } 
    else {
       sm =  $j('div#mpio_' + id_depto);
    }
    
    // Todos en amarillo
    $j("#cobertura div.checkbox").each(function(){ 
        var ch = $j(this).find("input:checkbox");
        $j(this).removeClass("selected_m") 
    });

    $j('#' + id_div).addClass('selected_m');
    
    var td = sd.offset().top;
    
    // Check de top con top de deptos
    var tm = $j(event.target).offset().top - (hm / 2);
    
    tmpio = (tm < td) ? td : tm;

    // Check de top con bottom of div content
    var bd = td +  sd.height();
    var bm = tm + hm;
    
    if (bd < bm) {
        tmpio -= bm - bd;
    }
    
    sm.css('top', tmpio);
    sm.css('left', sm.css('left') + 10);
    
    if (clone) {
        $j('div.mpios:last').after(sm.show('fast'));
        getDataV1('checkboxMpios4w','ajax_data.php?object=checkboxMpios4w&id_deptos='+id_depto, inner)
    } 
    else {
        sm.show();
    }
}

function closeMpios(e) {
    $j(e.target).parents('div.mpios').hide();
}

function listarTemasHijos(id_papa,display){

    document.getElementById('hijo_'+id_papa).style.display = display;
    document.getElementById('nieto_'+id_papa).style.display = display;

}

function benfOtroCual(){

    var otro_obj = document.getElementById('benf_otro_cual');

    if (id_pob == '44' && chk == true){
        otro_obj.style.display = '';
    }
    else{
        otro_obj.style.display = 'none';
    }
}

function showTemaNieto(check,id){
    var p_papa = document.getElementById('p_nieto_papa_'+id);

    if (check){
        p_papa.style.display = '';
    }
    else{
        p_papa.style.display = 'none';
    }
}
function buscarOcurr(e, inom, iid, inner){

    texto = document.getElementById(inom).value;

	var c = 'ocurrenciasOrg4wA'
    if (inner == 'ocurr_con') {
        c = 'ocurrenciasCon4wA';
    }

	keyNum = e.keyCode;
		
	if (keyNum == 8){  //Backspace
		texto = texto.slice(0, -1);  //Borra el ultimo caracter
	}
	else{
		keyChar = String.fromCharCode(keyNum);
		texto +=  keyChar;
	}
	if (texto.length > 1){
		document.getElementById(inner).style.display = 'block';
		getDataV1(c,'ajax_data.php?object='+c+'&s='+texto+'&inom='+inom+'&iid='+iid+'&inner='+inner, inner);
	}
	
}
function setValuesOcurr(id, iid, nom, inom, inner) {

    document.getElementById(iid).value = id;
    document.getElementById(inom).value = nom;
    document.getElementById(inner).style.display = 'none';

    if (inner != 'ocurr_org_con') {
        resumenEv(iid, true);
    }

}

function clone(cls) {
    var rei = /[0-35-9]+/g;
    var _l = $j('.' + cls + ':first').html();
    var num = $j('.' + cls).length - 1;
    var l = _l.replace(rei, num);
    
    $j('.' + cls + ':last').after('<div class="' + cls + ' ' + cls + '_' + num + '">' + l + '</div>');
}

function removeClone(cls, i) {
    
    if (confirm('Esta seguro?')) {
        if (i == 0) {
            removef(cls);
        }
        else {
            $j('.' + cls).remove();
        }
    }
    else {
        return false;
    }
    
}
function removef(cls) {
    if (cls == 'don_0') {
        $j('.' + cls).find('#id_orgs_d_0').val('');
        $j('.' + cls).find('#nom_org_d_0').val('');
        $j('.' + cls).find('#valor_org_d_0').val('');
        $j('.' + cls).find('#codigo_org_d_0').val('');
    }
    else if (cls == 'imp_0') {
        $j('.' + cls).find('#id_orgs_s_0').val('');
        $j('.' + cls).find('#nom_org_s_0').val('');
    }
}
function filterMpios(event) {
    
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
    $j(event.target).parents('div.mpios').find('div.checkbox').each(
        function () {
            $j(this).show();
            if (!$j(this).find('label').html().match(re)) {
                $j(this).hide();
            }
        });
    
}
// Lista municipios
function lM(combo_depto){
    var id_deptos = $j('#' + combo_depto).val();
    getDataV1('comboBoxMunicipio','ajax_data.php?object=comboBoxMunicipio&multiple=0&titulo=0&separador_depto=0&id_name=id_mun_sede&id_deptos='+id_deptos,'comboBoxMunicipio')
}

function chkc(addE) {
    
    // checkbox cobertura deptos
    $j("div.deptos div.checkbox").each(function(){ 
        var d = $j(this);
        var ch = $j(this).find("input:checkbox");
        
        if (ch.attr("checked")){ 
            d.addClass("selected") 
            d.find('div.listar').show();
        }
        else {
            d.removeClass('selected');
            d.find('div.listar').hide();
        }
        
        if (addE) {
            ch.click(function() {
                d.toggleClass('selected');
                d.removeClass('selected_m');
                d.find('div.listar').toggle();
                $j('div#mpio_' + ch.val()).toggle();
            });
        }
    });
    
    // checkbox cobertura muns
    $j("div.mpios div.checkbox").each(function(){ 
        var d = $j(this);
        var ch = $j(this).find("input:checkbox");
        
        if (ch.attr("checked")){ 
            d.addClass("selected") 
            d.find('div.listar').show();
        }
    });
}

function checkDeptoLista(divipola, chk) {

    $j('#d_' + divipola.substr(0,2)).prop('checked', chk);
    chkc(false);
    $j('#a_' + divipola.substr(0,2)).click();
    resumenEv('cobertura', true) ;
}
function checkMpioLista() {

    $j('.map_mun input:checkbox').each(function(){
        $j('#mun_' + $j(this).val()).attr('checked', $j(this).attr('checked'));
     });

     $j('div.mpios').hide();
    chkc(false);
}

function chkt() {
    
    // checkbox temas
    $j("div#tema tr.tema").each(function(){ 
        var d = $j(this);
        var p = d.parents('div.left');
        var ch = $j(this).find("input:checkbox");
        var inp = $j(this).find("input:text");
        
        // Defin.
        var dd = d.find('div.tdef:first');
        d.hover(function() { dd.show() },
                function() { dd.hide() }
                );
        
        if (ch.attr("checked")){ 
            d.addClass("selected") 
        };
        
        ch.click(function() {
            d.toggleClass('selected');
            // Borra presupuesto 
            inp.val('');
        });
         
        // Sin check de tema principal 
        /*
        ch.click(function() {
            if (ch.attr('checked')) {
                // Tema principal si es el primer checked
                if ($j('div#tema .cl').find('input:checked').length == 1) {
                    d.find('input:radio').attr('checked', 'checked');
                }
            }
            else {
                d.find('input:radio').attr('checked', false);
            }
        });
        */
    });
    
    // Checkbox temas hijos
    $j(".chk_th").click(function() {
        
        p_id = $j(this).attr("data-p");
        
        var $p = $j('#' + p_id);
        
        c = ($j.find('[data-p="'+p_id+'"]:checked').length == 0) ? false : true;

        $p.attr('checked', c);
    });
}

function chkb() {
    
    // checkbox beneficiarios
    $j("div#benef div.checkbox").each(function(){ 
        var d = $j(this);
        var ch = $j(this).find("input:checkbox");
        var dv = $j(this).find("div.cant");
        var tx = $j(this).find("input:text");
        
        if (ch.attr("checked")){ 
            d.addClass("selected") 
        };
        
        ch.click(function() {
            d.toggleClass('selected');
            dv.toggle();
            tx.attr('disabled', !ch.attr('checked'));
            tx.focus();
    
            if ($j('#bd_44').attr('checked') || $j('#bi_44').attr('checked')) {
                $j('#benf_otro_cual').show();
            }
            else {
                $j('#benf_otro_cual').hide();
            }

        });
    });
}

function resumen() {
    
    var id;
    var ok;
    var ul_ib = $j('#resumen ul#uib');
    $j('div#info_basica .ri').each(function () {
        id = $j(this).attr('id');
        var inp = $j(this);
        
        ul_ib.append('<li id="r_'+id+'" class="'+id+'">'+$j('label[for='+id+']').html()+'</li>');
        
        // Click en label de resumen, lleva al campo
        $j('#r_'+id).click(function() { acTab(0); inp.focus(); }  );
        
        $j(this).change(function(){
            resumenEv($j(this).attr('id'), ($j(this).val() != '') ? true : false) ;
        });
    });

    $j('div#tema input:checkbox').each(function () {
        $j(this).click(function(){
            resumenEv('cl', true) ;
        });
    });
    
    /*
    $j('div#tema div.acc input:checkbox').each(function () {
        $j(this).click(function(){
            resumenEv('acc', true) ;
        });
    });
    */
    
    $j('div#tema div.undaf input:checkbox').each(function () {
        $j(this).click(function(){
            resumenEv('undaf', $j(this).is(':checked')) ;
        });
    });
    
    $j('#bdtotal').focusout(function(){
            // Check cantidad
            ok = ($j(this).val().length > 0) ? true : false;
            resumenEv('benef', ok) ;
    });
    
    $j('div.deptos input:checkbox').each(function () {
        $j(this).click(function(){
            resumenEv('cobertura', $j(this).is(':checked')) ;
        });
    });

    $j('#cobertura_nal_proy').change(function(){ 
        var ok = ($j(this).val() == '1') ? true : false;
            
        resumenEv('cobertura', ok) ;

    });

}

function resumenEv(id, ok) {
    if (ok) {
        $j('#r_' + id).addClass('ok');
    }
    else {
        $j('#r_' + id).removeClass('ok');
    }

    enaGuadar();
}

function resumenActualizar() {
    $j('#resumen li').each(function(){ $j(this).addClass('ok'); });
    $j('#submit').removeProp('disabled');
    $j('#resumen').hide('slow');
}

function enaGuadar() {
    var ok =  true;
    $j('#resumen li').each(function(){ if (!$j(this).hasClass('ok')) ok = false; });

    if (ok) {
        $j('#submit').removeProp('disabled');
        $j('#resumen').hide('slow');
    }
    else {
        $j('#submit').prop('disabled', 'disabled');
        $j('#resumen').show('fast');
    }

    // Coloca si_proy dependiendo de los temas marcados
    var si_proy = [];

    if ($j(':checkbox.tema_4w:checked').length > 0) {
        si_proy.push('4w');
    }
    
    if ($j(':checkbox.tema_des:checked').length > 0) {
        si_proy.push('des');
    }

    $j('#si_proy').val(si_proy.join('-'));

}

function acTab(id) {
    document.getElementById('tab4w').tabber.tabShow(id);
}

var map;
var markersP;
var fromProjection;
var toProjection;
function initMap() {

    fromProjection = new OpenLayers.Projection('EPSG:4326'); // World Geodetic System 1984 projection (lon/lat) 
    toProjection = new OpenLayers.Projection('EPSG:900913'); // WGS84 OSM/Google Mercator projection (meters) 
    OpenLayers.ImgPath = "../images/openlayers/";
    var extent = new OpenLayers.Bounds(-20037508.34,-20037508.34,20037508.34,20037508.34);
    
    var options = {
        units: "m",
        numZoomLevels: 16,
        controls:[],
        projection: toProjection,
        theme: '../style/openlayers/theme/default/style.min.css',
        'displayProjection': fromProjection
    };

    map = new OpenLayers.Map('map', options);

    map_layer = new OpenLayers.Layer.OSM.Mapnik("openstreetmap", {
        sphericalMercator: true,
        //maxExtent: extent 
        });

    map.addLayer(map_layer);
        map_layer = new OpenLayers.Layer.Google("google", {
            sphericalMercator: true,
            //maxExtent: extent,
            visibility: false
            });
        
    // Add the layer to the map object
    //map.addLayer(map_layer);
    
    map.addControl(new OpenLayers.Control.Navigation());
    map.addControl(new OpenLayers.Control.PanZoom());
    map.addControl(new OpenLayers.Control.MousePosition(
            { div: 	document.getElementById('mapMousePosition'), numdigits: 5 
        }));
    //map.addControl(new OpenLayers.Control.Scale('mapScale'));
    //map.addControl(new OpenLayers.Control.ScaleLine());
    
    // Create the markers layer
    var markers = new OpenLayers.Layer.Markers("Markers");
    map.addLayer(markers);
    
    // Capa para puntos procesados
    markersP = new OpenLayers.Layer.Markers("MarkersP");
    map.addLayer(markersP);
    
    // create a lat/lon object
    var myPoint = new OpenLayers.LonLat(-74.0833333, 4.6);
    myPoint.transform(fromProjection, map.getProjectionObject());
    
    // display the map centered on a latitude and longitude (Google zoom levels)
    map.setCenter(myPoint, 6);
    
    // Detect Map Clicks
    map.events.register("click", map, function(e){
        var lonlat = map.getLonLatFromViewPortPx(e.xy);
        markers.clearMarkers();
        markers.addMarker(new OpenLayers.Marker(lonlat));
        
        $j("#lonl").attr("value", lonlat.lon);
        $j("#latl").attr("value", lonlat.lat);
        
        lonlat.transform(toProjection,fromProjection);	
        $j("#lat").attr("value", lonlat.lat);
        $j("#lon").attr("value", lonlat.lon);
        
    });

    // Hide map
    $j('div#cmapa').hide();

}

function addMarkerP(lon, lat) {
    var size = new OpenLayers.Size(21,25);
    var offset = new OpenLayers.Pixel(-(size.w/2), -size.h);
    var icon = new OpenLayers.Icon('http://www.openlayers.org/dev/img/marker-gold.png', size, offset);

    markersP.addMarker(new OpenLayers.Marker(new OpenLayers.LonLat(lon, lat).transform(fromProjection, map.getProjectionObject()), icon.clone()));  

}

// Longitude y Latitude en grados
function removeMarkerP(lon, lat) {
    var ms = markersP.markers;

    var mp = new OpenLayers.LonLat(lon, lat);
    mp.transform(fromProjection, toProjection);

    for(var i=0; i<ms.length; i++) {
        if (ms[i].lonlat.lon == mp.lon && ms[i].lonlat.lat == mp.lat) {
            ms[i].destroy();
        }
    }
}

function shM(lon, lat, s, divipola) {
    (s) ? addMarkerP(lon, lat) : removeMarkerP(lon, lat);

    checkMpioLista(divipola, s);
}

function toggleCob(sh) {
    $j('.cob_opt').hide();
    $j('#c' + sh).show();
}

function toggle(ids) {
    for (id in ids) {
        $j('#' + ids[id]).toggle();
    }
}

function getMpioFromPoint(c) {
    
    var id;
    var vars = {};
    
    if (c == 'waypoints'){
        vars = { points : $j('#waypoints').val() };
        if ($j('#waypoints').val().length == 0) {
            $j('#waypoints').focus();
            alert('Ingrese los Way Points!!');
            return false;
        }
    }
    else {
        if ($j('#lon').val().length == 0) {
            $j('#lon').focus();
            alert('El valor de longitud es requerido');
            return false;
        }

        if ($j('#lon').val().length == 0) {
            $j('#lon').focus();
            alert('El valor de longitud es requerido');
            return false;
        }
        
        vars = { points : $j('#lon').val() + ',' + $j('#lat').val() };
    }

    
    $j('.pss').show();
    $j.post('ajax_data.php?object=getMpioFromPoint&c='+c,
        vars,
        function(data){
            if (data.success == 1) {
                var item;
                var lon = $j('#lon').val();
                var lat = $j('#lat').val();
                var id;

                for (var i=0; i< data.j.length; i++) {
                    
                    item = data.j[i];
                    id = 'mun_mapa_'+item.divipola;
                    
                    if (c == 'waypoints') {
                        lon = item.lon;
                        lat = item.lat;
                    }
                    
                    $j('#map_muns').append('<div class="checkbox map_mun"> ' + 
                                            '<input type="hidden" name="latitude['+item.divipola+']" value="'+item.lat+'"  /> ' + 
                                            '<input type="hidden" name="longitude['+item.divipola+']" value="'+item.lon+'"  /> ' + 
                                            '<input id="' + id + '" type="checkbox" onclick="shM(' + lon + ',' + lat + ', this.checked, ' + item.divipola+')" ' + 
                                            'value="' + item.divipola + '" name="id_muns[]" checked />&nbsp;' +
                                            '<label class="ch" for="' + id + '">' + item.label + '</label></div>');

                    addMarkerP(lon, lat);
                    checkDeptoLista(item.divipola, true);
                    $j('.pss').hide();
                }
            }
            else {
                $j('.pss').hide();
                alert('Hizo click dentro de Colombia?? Error al procesar datos! Intente nuevamente...');
            }
            

        }, 'json'
    );
} 

function getMpiosFromDivipola() {
    
    var id;
    var vars = {};
    
    vars = { divipolas : $j('#divipolas').val() };
    if ($j('#divipolas').val().length == 0) {
        $j('#divipolas').focus();
        alert('Ingrese los códigos divipola!!');
        return false;
    }

    
    $j('#pss').show();
    $j.post('ajax_data.php?object=getMpioFromDivipola',
        vars,
        function(data){
            if (data.success == 1) {
                var item;
                var id;

                for (var i=0; i< data.j.length; i++) {
                    
                    item = data.j[i];
                    id = 'mun_divipola_'+item.divipola;
                    
                    $j('#divipola_muns').append('<div class="checkbox divipola_mun"> ' + 
                                            '<input type="hidden" name="latitude['+item.divipola+']" value="'+item.lat+'"  /> ' + 
                                            '<input type="hidden" name="longitude['+item.divipola+']" value="'+item.lon+'"  /> ' + 
                                            '<input id="' + id + '" type="checkbox" ' + 
                                            'value="' + item.divipola + '" name="id_muns[]" checked />&nbsp;' +
                                            '<label class="ch" for="' + id + '">' + item.label + '</label></div>');

                    checkDeptoLista(item.divipola, true);
                    $j('#pss').hide();
                }
            }
            else {
                $j('#pss').hide();
                alert('Todos los c\xf3digos divipola estan mal!!!!!');
            }
            

        }, 'json'
    );
} 
