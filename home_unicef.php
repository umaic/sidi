<script type="text/javascript">
// Layout
Ext.onReady(function(){
    var nor = new Ext.Panel({
        region : "north", 
        contentEl : 'top-layout',
        title : "", 
        baseCls: 'menu-top',
        margins: { top: 148, right: 0, bottom: 0, left: 0 },
//        cmargins: { top: 122, right: 0, bottom: 0, left: 0 }, 
        closable : false, 
        height : 85
    });

    var center = new Ext.Panel({
        region : 'center',
        id : 'center_panel',
        margins : '3 3 3 3',
        autoScroll: true,
        autoLoad : 'consulta/unicef.php?caso=donde'
    });

    var viewport = new Ext.Viewport({
        layout : 'border',
        id : 'unicef-viewport',
        dafaults : {autoScroll: true},
        items : [center, nor ]
    });

});


function changeCenterContent(caso){

	var center_panel = Ext.getCmp('center_panel');

    center_panel.getUpdater().update({url : 'consulta/unicef.php?caso='+caso});

    // Aplica estilo
    var filtros = ['que','donde','socio','donante'];
    var class_name;
    for(var i=0;i<filtros.length;i++){
        class_name = (filtros[i] == caso) ? 'top-layout-visited' : '';
        document.getElementById('td_menu_top_' + filtros[i]).className = class_name;
    }

}

function changeProyEje(caso){
    
    var proyectado_td_obj = document.getElementById('proyectado_td');
    var proy_eje_hidden_obj = document.getElementById('proy_eje');
    var ejecutado_td_obj = document.getElementById('ejecutado_td');
    var proyectado_fecha_tr_obj = document.getElementById('proyectado_fecha');
    var ejecutado_fecha_tr_obj = document.getElementById('ejecutado_fecha');

    proyectado_td_obj.className = 'selected';
    proy_eje_hidden_obj.value = 'proyectado';
    ejecutado_td_obj.className = 'unselected';
    proyectado_fecha_tr_obj.style.display = '';
    ejecutado_fecha_tr_obj.style.display = 'none';

    if (caso == 'eje'){
        proyectado_td_obj.className = 'unselected';
        ejecutado_td_obj.className = 'selected';
        proyectado_fecha_tr_obj.style.display = 'none';
        ejecutado_fecha_tr_obj.style.display = '';
        proy_eje_hidden_obj.value = 'ejecutado';
    }
}

function changeFiltroP(caso){
    
    var objs = ['comps','mtsp','odm','mtsp_key'];
    var class_name_a;
    var class_name_filtro;

    for(var i=0;i<objs.length;i++){
        
        class_name_filtro_a = 'unselected_top';
        class_name_filtro = 'unselected';
        
        if (caso == objs[i]){
            class_name_filtro_a = 'selected_top';
            class_name_filtro = 'selected';
        }

        document.getElementById(objs[i] + '_filtro_a').className = class_name_filtro_a;
        document.getElementById(objs[i] + '_filtro').className = class_name_filtro;
    }

    document.getElementById('filtro').value = caso;
}

function getParamFecha(proy_eje){
    
    var param_fecha = '';

    if (proy_eje == 'proyectado'){
        param_fecha = '&aaaa=' + getRadioCheck(document.getElementsByName('aaaa'));
    }
    else{
        // Fecha inicio
        var f_inicio_dia_ini = document.getElementById('ejecutado_fecha_inicio_dia_ini').value;
        var f_inicio_mes_ini = document.getElementById('ejecutado_fecha_inicio_mes_ini').value;
        var f_inicio_aaaa_ini = document.getElementById('ejecutado_fecha_inicio_aaaa_ini').value;

        var param_fecha_inicio_ini = '&fecha_inicio_ini=';
        if (f_inicio_dia_ini != '' && f_inicio_mes_ini != '' && f_inicio_aaaa_ini != ''){
            param_fecha_inicio_ini += f_inicio_aaaa_ini + '-' + f_inicio_mes_ini + '-' + f_inicio_dia_ini;
        }
        
        var f_inicio_dia_fin = document.getElementById('ejecutado_fecha_inicio_dia_fin').value;
        var f_inicio_mes_fin = document.getElementById('ejecutado_fecha_inicio_mes_fin').value;
        var f_inicio_aaaa_fin = document.getElementById('ejecutado_fecha_inicio_aaaa_fin').value;

        var param_fecha_inicio_fin = '&fecha_inicio_fin=';
        if (f_inicio_dia_fin != '' && f_inicio_mes_fin != '' && f_inicio_aaaa_fin != ''){

            // Check fecha fin > fecha ini
            if (param_fecha_inicio_ini == ''){
                alert('Especifique la primera fecha de Inicio');
                return;
            }
            else if (parseInt(f_inicio_aaaa_ini) > parseInt(f_inicio_aaaa_fin)){
                alert('El a\xf1o de la primera fecha de inicio no puede ser mayor que el a\xf1o de la segunda fecha!!');
                return;
            }
            else if (parseInt(f_inicio_aaaa_ini) == parseInt(f_inicio_aaaa_fin) && parseInt(f_inicio_mes_ini) > parseInt(f_inicio_mes_fin)){
                alert('El mes de la primera fecha de inicio no puede ser mayor que el mes de la segunda fecha!!');
                return;
            }
            param_fecha_inicio_fin += f_inicio_aaaa_fin + '-' + f_inicio_mes_fin + '-' + f_inicio_dia_fin;
        }
        
        // Fecha finalizacion
        var f_finalizacion_dia_ini = document.getElementById('ejecutado_fecha_finalizacion_dia_ini').value;
        var f_finalizacion_mes_ini = document.getElementById('ejecutado_fecha_finalizacion_mes_ini').value;
        var f_finalizacion_aaaa_ini = document.getElementById('ejecutado_fecha_finalizacion_aaaa_ini').value;

        var param_fecha_finalizacion_ini = '&fecha_finalizacion_ini=';
        if (f_finalizacion_dia_ini != '' && f_finalizacion_mes_ini != '' && f_finalizacion_aaaa_ini != ''){
            param_fecha_finalizacion_ini += f_finalizacion_aaaa_ini + '-' + f_finalizacion_mes_ini + '-' + f_finalizacion_dia_ini;
        }
        
        var f_finalizacion_dia_fin = document.getElementById('ejecutado_fecha_finalizacion_dia_fin').value;
        var f_finalizacion_mes_fin = document.getElementById('ejecutado_fecha_finalizacion_mes_fin').value;
        var f_finalizacion_aaaa_fin = document.getElementById('ejecutado_fecha_finalizacion_aaaa_fin').value;

        var param_fecha_finalizacion_fin = '&fecha_finalizacion_fin=';
        if (f_finalizacion_dia_fin != '' && f_finalizacion_mes_fin != '' && f_finalizacion_aaaa_fin != ''){
            // Check fecha fin > fecha ini
            if (param_fecha_finalizacion_ini == ''){
                alert('Especifique la primera fecha de Finalizaci\xf3n');
                return;
            }
            else if (parseInt(f_finalizacion_aaaa_ini) > parseInt(f_finalizacion_aaaa_fin)){
                alert('El a\xf1o de la primera fecha de finalizaci\xf3n no puede ser mayor que el a\xf1o de la segunda fecha!!');
                return;
            }
            else if (parseInt(f_finalizacion_aaaa_ini) == parseInt(f_finalizacion_aaaa_fin) && parseInt(f_finalizacion_mes_ini) > parseInt(f_finalizacion_mes_fin)){
                alert('El mes de la primera fecha de finalizaci\xf3n no puede ser mayor que el mes de la segunda fecha!!');
                return;
            }
            param_fecha_finalizacion_fin += f_finalizacion_aaaa_fin + '-' + f_finalizacion_mes_fin + '-' + f_finalizacion_dia_fin;
        }
        
        param_fecha = param_fecha_inicio_ini + param_fecha_inicio_fin + param_fecha_finalizacion_ini + param_fecha_finalizacion_fin;
    }

    return param_fecha;
}

function unique(arrayName){

    var newArray = new Array();
    
    label:for(var i=0; i<arrayName.length;i++ ){  
    
        for(var j=0; j<newArray.length;j++ ){
            if(newArray[j]==arrayName[i]) continue label;
        }

        newArray[newArray.length] = arrayName[i];
    }
    
    return newArray;
}

function parseOptionsFilter(filtro){
    
    var titulo = { 'comps':'Componente','odm':'ODM','mtsp':'MSTP-F.AREA','mtsp_key':'MTSP-KEY RESULT'};
    
    var id_filtro = getOptionsCheckBox(document.getElementsByName('id_' + filtro));

    if (id_filtro == ''){
        alert('Seleccione alg\xfan ' + titulo[filtro] + '!!');
        return false;
    }

    if (filtro != 'comps'){
    
        // Correspondencias con componentes
        var odm = ['','3,4','1,2,3,4','2,3,4','1,3','1,3','2,3','1,3','4'];
        var mtsp = ['','1','2','3','4'];
        var mtsp_key = ['','1,4','1,3','1,2,3,4','2,3,4'];
        
        if (filtro == 'odm')            equ = odm;
        else if (filtro == 'mtsp')      equ = mtsp;
        else if (filtro == 'mtsp_key')  equ = mtsp_key;

        var id_filtro_tmp = id_filtro.split(',');

        for (var i=0;i<id_filtro_tmp.length;i++){
            if (i == 0) id_filtro = equ[id_filtro_tmp[i]];
            else        id_filtro += ',' + equ[id_filtro_tmp[i]];

        }
        
        unique(id_filtro.split(',')).join(',');
    }
    
    return id_filtro;

}

function getSocioDonante(){

    var filtro = '';
    var id_filtro = '';

    if (document.getElementById('id_socio')){
        var id_socio = getRadioCheck(document.getElementsByName('id_socio'));
        if (id_socio == undefined){
            alert('Seleccione alg\xfan Socio');
            return false;
        }
        else{
            filtro += '-socio';
            id_filtro += '-' + id_socio;
        }
    }
    if (document.getElementById('id_donante')){
        var id_donante = getRadioCheck(document.getElementsByName('id_donante'));
        if (id_donante == undefined){
            alert('Seleccione alg\xfan Donante');
            return false;
        }
        else{
            filtro += '-donante';
            id_filtro += '-' + id_donante;
        }
    }

    return {'filtro':filtro,'id_filtro':id_filtro};
}

function mostrarReporte(mapa){

	//var url = 'unicef_reporte.php?caso='+caso;
	var url = 'unicef_reporte.php?';
    var w_width = 950;
    var w_height = 550;

    // Filtros
    
    var filtro = document.getElementById('filtro').value;
    var id_filtro = parseOptionsFilter(filtro);

    if (!id_filtro) return false;

    // Realizada la equivalencia de filtros, caso es solo comps
    filtro = 'comps';

    //Socios - Donantes
    var socio_don = getSocioDonante();
    if (!socio_don) return false;

    filtro += socio_don.filtro;
    id_filtro += socio_don.id_filtro;

    var proy_eje = document.getElementById('proy_eje').value;
    var title_window = (proy_eje == 'proyectado') ? 'AWP' : 'Convenios' ;

    if (mapa == 1){

        url = 'unicef_mapa.php?';
    
        var mdgd = (document.getElementById('mdgd_deptal').checked) ? 'deptal' : 'mpal' ;

        url += 'mdgd=' + mdgd + '&';
    }

    url += 'filtro=' + filtro + '&id_filtro=' + id_filtro + '&proy_eje=' + proy_eje;
    
    var param_fecha = getParamFecha(proy_eje);

    url += param_fecha;
	
    new Ext.Window({
				id			: 'windowIU',
				//autoLoad 	: url,
                html        : '<iframe src="'+url+'" id="iframe_mapa" frameborder="0" width="'+w_width+'" height="'+w_height+'"></iframe>',
				region 		: 'center',
				width		: w_width+20,
				height		: w_height+33,
				autoScroll	: true,
				closeable	: true,
				bodyCssClass: 'body',
                maximizable : true,
				title		: title_window + ' UNICEF',
                modal       : true
			}

			).show();

			return false;
}
function indiceFiltro(letra,filtro,indice){
    
    indice = indice.split(',');
    // Clear
    for (var c=0;c<indice.length;c++){
        var s_char = indice[c];

        document.getElementById('ul_filtro_' + filtro + '_' + s_char).style.display = 'none';
        document.getElementById('inicial_filtro_' + filtro + '_' + s_char).className = 'inicial_unselected';
    }

    document.getElementById('ul_filtro_' + filtro + '_' + letra).style.display = '';
    document.getElementById('inicial_filtro_' + filtro + '_' + letra).className = 'inicial_selected';

}
</script>

<div id="top-layout" class="x-hide-display">
    <table cellpadding="0" cellspacing="0" border="0" align="center">
        <tr>    
            <td><img src="images/unicef/home/donde.png" width="32" height="32"></td><td width="110" id="td_menu_top_donde" class="top-layout-visited"><a href="#" onclick="changeCenterContent('donde')">DONDE</a></td>
            <td><img src="images/unicef/home/que.png" width="32" height="32"></td><td width="80" id="td_menu_top_que"><a href="#" onclick="changeCenterContent('que')">QUE</a></td>
            <td><img src="images/unicef/home/quien.png" width="32" height="32"></td><td width="128" id="td_menu_top_socio"><a href="#" onclick="changeCenterContent('socio')">SOCIOS</a></td>
            <td><img src="images/unicef/home/recursos.png" width="32" height="32"></td><td width="150" id="td_menu_top_donante"><a href="#" onclick="changeCenterContent('donante')">DONANTES</p></a></td>
        </tr>
    </table>
</div> 
