function inicializarTextos(){
	document.getElementById('id_depto').value = new Array('0');
}

function validarComboM(ob){
	selected = new Array();
	for (var i = 0; i < ob.options.length; i++){
		if (ob.options[ i ].selected)
		selected.push(ob.options[ i ].value);
	}

	if (selected.length == 0){
		return false;
	}
	else{
		return true;
	}
}

function validar_criterios_basico(){

	var error = 1;

	if (document.getElementById('id_depto').value != ""){
		error = 0;
	}

	if (document.getElementById('id_muns').value != ""){
		error = 0;
	}

	if (error == 1){
		alert("Seleccione la Ubicación Geográfica");
		return false;
	}
	else{
		document.getElementById("basico").value = 1;
		return true;
	}
}
function validar_criterios(){

	var error = 1;
	var error_que = 0;
	var error_sede = 0;
	var filtros = Array('id_depto');

	if (document.getElementById('id_depto').value != ""){
		error = 0;
	}

	if (document.getElementById('id_muns').value != ""){
		error = 0;
	}

	error = 1;
	ques = Array('que_dato_resumen','que_dato_general','que_dato','que_dato_d_natural','que_dato_s_publicos','que_desplazamiento','que_mina');
	for(q=0;q<ques.length;q++){
		if (document.getElementById(ques[q]).checked == true){
			error = 0;
		}
	}

	if (error == 1){
		alert("Seleccione alguna consulta");
		return false;
	}

	else if (error == 1 && error_que == 0){
		alert("Seleccione algún Departamento o Municipio");
		return false;
	}

	else if (error == 0 && error_que == 1){
		alert("Seleccione lo que desea consultar (Organizaciones, Proeyctos, Eventos, Datos Sectoriales, Datos de Desplazamiento, Eventos con Mina)");
		return false;
	}



	//DATOS SECTORIALES
	if (document.getElementById('que_dato').checked == true){

		reporte = document.getElementById('id_reporte').options[document.getElementById('id_reporte').selectedIndex].value;

		if (reporte == 1){
			id_cabecera = document.getElementById('id_dato_cabecera').options[document.getElementById('id_dato_cabecera').selectedIndex].value;
			id_resto = document.getElementById('id_dato_resto').options[document.getElementById('id_dato_resto').selectedIndex].value;

			if (id_cabecera == id_resto){
				alert("Los Datos asociados a población en Cabecera y población en el resto no pueden ser el mismo");
				return false;
			}
		}

	}
	//DESPLAZAMIENTO
	if (document.getElementById('que_desplazamiento').checked == true){
		msg = "";

		if (document.getElementById('ini_desplazamiento').value == '')	msg += "- Periodo: Año Inicial\n";
		if (document.getElementById('fin_desplazamiento').value == '')	msg += "- Periodo: Año Final\n";

		if (msg != ''){
			alert('Los siguientes campos son requeridos \n\n'+msg);
			return false;
		}

		reporte = document.getElementById('id_reporte').options[document.getElementById('id_reporte').selectedIndex].value;

		num_chk = 0;
		for (i=0;i<document.getElementById('num_fuentes').value;i++){
			if (document.getElementById('fuente_'+i).checked == true){
				num_chk++;
			}
		}

		if (reporte == 1 || reporte == 2){
			if (num_chk > 2){
				alert('Solo se pueden seleccionar 2 fuentes como máximo para el reporte');
				return false;
			}
		}
		else{

			if (num_chk > 1){
				alert('Solo se puede seleccionar 1 fuente como máximo para el reporte');
				return false;
			}
		}

	}
	if (document.getElementById('que_mina').checked == true){
		msg = "";

		if (document.getElementById('ini_mina').value == '')	msg += "- Periodo: Año Inicial\n";
		if (document.getElementById('fin_mina').value == '')	msg += "- Periodo: Año Final\n";

		if (msg != ''){
			alert('Los siguientes campos son requeridos \n\n'+msg);
			return false;
		}
		num_chk = 0;
		for (i=0;i<document.getElementById('num_edades').value;i++){
			if (document.getElementById('edad_'+i).checked == true){
				num_chk++;
			}
		}

		if (num_chk > 2){
			alert('Solo se pueden seleccionar 2 edades como máximo para el reporte');
			return false;
		}
	}

	else{
		return true;
	}

}


function showDiv (div_id,accion){
	tabla = document.getElementById("table_" + div_id);
	div = document.getElementById(div_id);

	if (accion == 'mostrar'){
		left = (docwidth() - 950 )/2 + 210;
		div.style.left = left+'px';
		div.style.top = '70px';
		tabla.style.visibility = 'visible';

	}
	else{
		tabla.style.visibility = 'hidden';

		if (div_id == 'mapa'){
			//Coloca los datos de la ubicacion
			//getData('ubicacion');
		}
	}
}
function asignarVariablesH(id_depto,id_mun){
	document.getElementById('id_depto').disabled = false;
	document.getElementById('id_depto').value = id_depto;
	document.getElementById('id_muns').disabled = true;
	document.getElementById('id_muns').value = '';
	
	document.getElementById('nombreDepto').innerHTML = '';
	document.getElementById('nombreMpio').innerHTML = '';
	document.getElementById('separador_depto_mpio').style.display = 'none';
	
	//COLOCA EL TITULO DEL DEPTO
	getDataV1('nombreDepto','admin/ajax_data.php?object=nombreDepto&id_depto='+id_depto,'nombreDepto');
	
	if (id_mun != 0){
		document.getElementById('id_muns').disabled = false;
		document.getElementById('id_muns').value = id_mun;
		
		document.getElementById('separador_depto_mpio').style.display = '';
		//COLOCA EL TITULO DEL MPIO
		getDataV1('nombreMpio','admin/ajax_data.php?object=nombreMpio&id_mpio='+id_mun,'nombreMpio');
		
		//Selecciona el mpio en el combo
		if (document.getElementById('id_mun_depto') != undefined){
			document.getElementById('id_mun_depto').value = id_mun;
		}
		
	}
	
	//Elimina la opcion de Departamentos del combo del reporte 4 de Desplazamiento
	var combo_reporte_4_despla = document.getElementById('dato_para_reporte_4_despla');
	
	combo_reporte_4_despla.options[1] = null;

	//Elimina la opcion de Departamentos del combo del reporte 5 de Mina
	var combo_reporte_5_mina = document.getElementById('dato_para_reporte_5_mina');
	
	combo_reporte_5_mina.options[1] = null;

	//Elimina la opcion de Departamentos del combo del reporte 4 de Datos
	var combo_reporte_4_dato = document.getElementById('dato_para_reporte_4_dato');
	
	combo_reporte_4_dato.options[1] = null;

}

function mostrarOpciones(entidad,chk){

	if (chk == true)	document.getElementById('op_'+entidad).style.display = '';
	else				document.getElementById('op_'+entidad).style.display = 'none';

}

function mostrarFiltrosMina(filtro){
	var filtros = Array('sexo','condicion','estado','edad','rep_5','rep_1');

	for (i=0;i<filtros.length;i++){
		document.getElementById('table_'+filtros[i]).style.display = 'none';
	}

	document.getElementById('table_'+filtro).style.display = '';
	
	//oculta el ejex para reporte 4
	document.getElementById('detalle_periodo_despla').value = 'aaaa';
	if (filtro == 'rep_5'){
		document.getElementById('tr_detalle_periodo_mina').style.display = 'none';
	}
	else{
		document.getElementById('tr_detalle_periodo_mina').style.display = '';
	}
}

function setTextoFuentes(id_reporte){

	num_f = document.getElementById('num_fuentes').value;
	for (i=0;i<num_f;i++){

		if (i < (num_f - 1)){
			document.getElementById('fuente_'+i).checked = false;
		}
	}

	texto = Array('','Máx 2 fuentes en el reporte','Máx 2 fuentes en el reporte','Máx 1 fuente en el reporte');
	titulo = Array('','Número total de Desplazados','Número de Nuevos Desplazados','Número de Desplazados Registrados');

	document.getElementById('texto_fuente').innerHTML = '[ <b>' + texto[id_reporte] + '</b> ]';
	document.getElementById('titulo_grafica').value = titulo[id_reporte];
}


function graficar(chart){

	//Oculta Mapa
	//document.getElementById('<?=$vista?>').style.display = 'none';

	//Muestra td result
	document.getElementById('graficaConteoOrg').style.display = '';

	//Muestra boton de mapa
	//document.getElementById('boton_regresar').style.display = '';

	ubicacion = 0;  // Para toda Colombia
	depto = 2;

	if (document.getElementById('graficar_por_sector').checked == true)		graficar_por = 'sector';
	if (document.getElementById('graficar_por_tipo').checked == true)			graficar_por = 'tipo';
	if (document.getElementById('graficar_por_poblacion').checked == true)	graficar_por = 'poblacion';
	
	tipo_papa = document.getElementById('tipo_papa').value;

	id_depto = document.getElementById('id_depto').value;
	id_muns = document.getElementById('id_muns').value;
	cnrr = 2;
	consulta_social = 2;

	if (id_depto != 0){
		ubicacion = id_depto;
		depto = 1;
	}
	if (id_muns != ''){
		ubicacion = id_muns;
		depto = 0;
	}

	var params = 'object=graficaConteoOrg&graficar_por='+graficar_por+'&depto='+depto+'&ubicacion='+ubicacion+'&cnrr='+cnrr+'&consulta_social='+consulta_social+'&chart='+chart+'&tipo_papa='+tipo_papa;
	document.getElementById('debug_info').value = 'http://<?=$_SERVER["SERVER_NAME"]?>/sissh/api_grafica.php?' + params;

	getDataV1('graficaConteoOrg','admin/ajax_data.php?' + params,'graficaConteoOrg');
}
function generarListadoOrgs(){

	//Muestra td result
	document.getElementById('listadoConteoOrg').style.display = '';


	ubicacion = 0;  // Para toda Colombia
	depto = 2;

	if (document.getElementById('graficar_por_sector').checked == true)		graficar_por = 'sector';
	if (document.getElementById('graficar_por_tipo').checked == true)			graficar_por = 'tipo';
	if (document.getElementById('graficar_por_poblacion').checked == true)	graficar_por = 'poblacion';

	id_depto = document.getElementById('id_depto').value;
	id_muns = document.getElementById('id_muns').value;
	cnrr = 2;
	consulta_social = 2;

	if (id_depto != 0){
		ubicacion = id_depto;
		depto = 1;
	}
	if (id_muns != ''){
		ubicacion = id_muns;
		depto = 0;
	}

	getDataV1('listadoConteoOrg','admin/ajax_data.php?object=listadoConteoOrg&graficar_por='+graficar_por+'&depto='+depto+'&ubicacion='+ubicacion+'&cnrr='+cnrr+'&consulta_social='+consulta_social,'listadoConteoOrg');
}
function graficarDesplazamiento(chart){

	//Reporte
	reportes = document.forms[0].reporte;
	for (i=0;i<reportes.length;i++){
		if(reportes[i].checked)	reporte = reportes[i].value;
	}

	//Periodo
	f_ini = document.getElementById('ini_desplazamiento').value;
	f_fin = document.getElementById('fin_desplazamiento').value;

	//Clase
	if (reporte != 5){
		exp_rec = 0;
		if (document.getElementById('exp_rec_exp').checked == true)			exp_rec = 1;
		if (document.getElementById('exp_rec_rec').checked == true)			exp_rec = 2;
		
		if (exp_rec == 0){
			alert('Seleccione la Clase de Desplazamiento');
			return false;
		}
	}
	else{
		exp_rec = 1; //no importa el valor, porque no se usa en el reporte
	}

	//eje x
	var ejex = document.getElementById('detalle_periodo_despla').value;

	//fuentes
	if (reporte == 3 || reporte == 4){
		var combo_fuentes = document.getElementsByName('fuentes_radio');
		var id_fuentes = getRadioCheck(combo_fuentes);
		var input_codhes = document.getElementById('fuente_despla_1_radio');
	}
	else if (reporte == 5){
		var id_fuentes = 2;  //Sipod
	}
	else{
		var combo_fuentes = document.getElementsByName('fuentes');
		var id_fuentes = getOptionsCheckBox(combo_fuentes);
		var input_codhes = document.getElementById('fuente_despla_1');
	}

	if (reporte != 5 && reporte != 6 && reporte != 7 && !checkInputChecked(combo_fuentes)){
		alert('Seleccione Fuente');
		return false;
	}

	//Validacion de Clase de Desplazamiento
	if (reporte != 5){
		if (input_codhes.checked == true){
			if ( exp_rec == 0 || exp_rec == 1){
				document.getElementById('exp_rec_rec').checked = true;
				alert('Para la fuente CODHES, la Clase de Desplazamiento debe ser Recepción/Estimado Llegadas');
			}
		}
	}
		
	//dato para reporte 4
	var dato_para_reporte_4_despla = document.getElementById('dato_para_reporte_4_despla').value;
	
	//Validación
	msg = "";

	if (f_ini == '')	msg += "- Periodo: Año Inicial\n";
	if (f_fin == '')	msg += "- Periodo: Año Final\n";

	if (parseInt(f_ini) > parseInt(f_fin)){
		alert("Año Inicial debe ser menor que Año Final");
		return false;
	}
	
	if (msg != ''){
		alert('Los siguientes campos son requeridos \n\n'+msg);
		return false;
	}

	//Muestra td result
	document.getElementById('graficaDesplazamiento').style.display = '';

	ubicacion = 0;  // Para toda Colombia
	depto = 2;

	id_depto = document.getElementById('id_depto').value;
	id_muns = document.getElementById('id_muns').value;

	if (id_depto != 0){
		ubicacion = id_depto;
		depto = 1;
	}
	if (id_muns != ''){
		ubicacion = id_muns;
		depto = 0;
	}

	var params = 'object=GraficaResumenDesplazamiento&reporte='+reporte+'&depto='+depto+'&ubicacion='+ubicacion+'&exp_rec='+exp_rec+'&f_ini='+f_ini+'&f_fin='+f_fin+'&fuentes='+id_fuentes+'&chart='+chart+'&ejex='+ejex;
		params += '&dato_para_reporte_4_despla='+dato_para_reporte_4_despla;
	
	// getDataV1('graficaDesplazamiento',ajax_script + '?' + params,'graficaDesplazamiento');
    //
    $.ajax({
        url: ajax_script + '?' + params,
        success: function(html){ 

            $('#graficaDesplazamiento').html(html);

            $('#highchart').highcharts({
            data: {
                table: 'tablaDesplazamiento'
            },
            chart: {
                type: 'column'
            },
            title: {
                text: $('#tablaDesplazamiento').data('titulo')
            },
            yAxis: {
                allowDecimals: false,
                title: {
                    text: $('#tablaDesplazamiento').data('ejey')
                }
            },
            tooltip: {
                formatter: function () {
                    return '<b>' + this.series.name + '</b><br/>' +
                        this.point.y + ' ' + this.point.name.toLowerCase();
                }
            }
    });
        }
    });
}
function generarReporteDesplazamiento(){

	//Reporte
	reportes = document.forms[0].reporte;
	for (i=0;i<reportes.length;i++){
		if(reportes[i].checked)	reporte = reportes[i].value;
	}

	//Periodo
	f_ini = document.getElementById('ini_desplazamiento').value;
	f_fin = document.getElementById('fin_desplazamiento').value;

	//Clase
	if (reporte != 5){
		exp_rec = 0;
		if (document.getElementById('exp_rec_exp').checked == true)			exp_rec = 1;
		if (document.getElementById('exp_rec_rec').checked == true)			exp_rec = 2;
		
		//Validacion de Clase de Desplazamiento
		if (document.getElementById('fuente_despla_1').checked == true){
			if ( exp_rec == 0 || exp_rec == 1){
				document.getElementById('exp_rec_rec').checked = true;
				alert('Para la fuente CODHES, la Clase de Desplazamiento debe ser Recepción/Estimado Llegadas');
			}
		}
		else{
			if (exp_rec == 0){
				alert('Seleccione la Clase de Desplazamiento');
				return false;
			}
		}		
	}
	else{
		exp_rec = 1; //no importa el valor, porque no se usa en el reporte
	}

	//eje x
	ejex = document.getElementById('detalle_periodo_despla').value;

	//fuentes
	if (reporte == 3){
		var combo_fuentes = document.getElementsByName('fuentes_radio');
		var id_fuentes = getRadioCheck(combo_fuentes);
	}
	else if (reporte == 5){
		var id_fuentes = 2;  //Sipod
	}
	else{
		var combo_fuentes = document.getElementsByName('fuentes');
		var id_fuentes = getOptionsCheckBox(combo_fuentes);
	}

	ubicacion = 0;  // Para toda Colombia
	depto = 2;

	id_depto = document.getElementById('id_depto').value;
	id_muns = document.getElementById('id_muns').value;

	if (id_depto != 0){
		ubicacion = id_depto;
		depto = 1;
	}
	if (id_muns != ''){
		ubicacion = id_muns;
		depto = 0;
	}

	//Opcione de reporte para nacional
	//Reporte
	tipo_nal = 0;
	if (depto == 2){
		tipo_nal = getRadioCheck(document.getElementsByName('tipo_nal'));
	}

	var params = 'object=reporteGraResumenDesplazamiento&reporte='+reporte+'&depto='+depto+'&ubicacion='+ubicacion+'&exp_rec='+exp_rec+'&f_ini='+f_ini+'&f_fin='+f_fin+'&fuentes='+id_fuentes+'&ejex='+ejex+'&tipo_nal='+tipo_nal;
	
	document.getElementById('debug_info').value = server + '/sissh/api_grafica.php?' + params;

	getDataV1('reporteGraResumenDesplazamiento',ajax_script + '?' + params,'reporteGraResumenDesplazamiento');
}

function changeParametersDesplazamiento(reporte,num_fuentes){
	
	document.getElementById('td_clase_desplazamiento').style.display = '';
	
	//Cambia de checkbox a radio la opcion de fuentes
	var input_fuentes = document.getElementsByName('fuentes');
	for(i=1;i<=num_fuentes;i++){
		if (reporte == 4){
			document.getElementById('fuentes_despla_radio').style.display = '';
			document.getElementById('fuentes_despla_check').style.display = 'none';
		}
		else if (reporte == 1 || reporte ==2){
			document.getElementById('fuentes_despla_radio').style.display = 'none';
			document.getElementById('fuentes_despla_check').style.display = '';
		}
		else if (reporte == 5){
			document.getElementById('fuentes_despla_radio').style.display = 'none';
			document.getElementById('fuentes_despla_check').style.display = 'none';
			document.getElementById('td_clase_desplazamiento').style.display = 'none';
		}
		else if (reporte == 3 || reporte == 6 || reporte == 7){
			document.getElementById('fuentes_despla_radio').style.display = 'none';
			document.getElementById('fuentes_despla_check').style.display = 'none';
			document.getElementById('td_clase_desplazamiento').style.display = '';
			
			// Check Accion social
			document.getElementById('fuente_despla_2_radio').checked = true;
		}
			
	}
	
	//oculta el ejex para reporte 4
	document.getElementById('detalle_periodo_despla').value = 'aaaa';
	if (reporte == 4){
		document.getElementById('tr_detalle_periodo_despla').style.display = 'none';
	}
	else{
		document.getElementById('tr_detalle_periodo_despla').style.display = '';
	}
}

function changeEjexDesplazamiento(tipo_input_fuente){

	var combo = document.getElementById('detalle_periodo_despla');
	
	if (tipo_input_fuente == 'checkbox'){
		var id_codhes = 'fuente_despla_1'; 
		var id_sipod = 'fuente_despla_2'; 
	}
	else{
		var id_codhes = 'fuente_despla_1_radio'; 
		var id_sipod = 'fuente_despla_2_radio'; 
	}
		
	var check_codhes = document.getElementById(id_codhes);
	var check_sipod = document.getElementById(id_sipod);
	
	//Desde la 1 para dejar la opcion 0 que es años
	while(combo.options.length > 1){
		combo.options[combo.options.length - 1] = null;
	}
	
	//CODHES
	if (check_codhes.checked == true){
		AddOption('Trimestres','trim',combo);
	}
	
	if (check_sipod.checked == true){
		//check si ambas estan checked
		if (check_codhes.checked == false){
			AddOption('Semestres','sem',combo);
			AddOption('Trimestres','trim',combo);
			AddOption('Meses','mes',combo);
		}
	}
}

function changeEjexDesplazamientoReporte5(){

	var combo = document.getElementById('detalle_periodo_despla');
	
	AddOption('Semestres','sem',combo);
	AddOption('Trimestres','trim',combo);
	AddOption('Meses','mes',combo);
}

function graficarMina(){


	filtros_txt = Array('','Sexo','Condición','Estado','Edad');

	//Reporte
	reportes = document.forms[0].reporte_mina;
	for (i=0;i<reportes.length;i++){
		if(reportes[i].checked)	reporte = reportes[i].value;
	}

	//Periodo
	f_ini = document.getElementById('ini_mina').value;
	f_fin = document.getElementById('fin_mina').value;
	
	//Tipo Gráfica
	grafica = getRadioCheck(document.getElementsByName('grafica_mina'));
	
	//eje x
	ejex = document.getElementById('detalle_periodo_mina').value;
	
	//dato para reporte 5
	var dato_para_reporte_5_mina = document.getElementById('dato_para_reporte_5_mina').value;
	
	//acc o vic
	var acc_vic = 'vic';
	if (reporte == 6) acc_vic = document.getElementById('mina_acc_vic_' + reporte).value;
	
	//Validación
	msg = "";

	if (f_ini == '')	msg += "- Periodo: Año Inicial\n";
	if (f_fin == '')	msg += "- Periodo: Año Final\n";

	if (parseInt(f_ini) > parseInt(f_fin))
		msg += "Año Inicial debe ser menor que Año Final";

	if (msg != ''){
		alert('Los siguientes campos son requeridos \n\n'+msg);
		return false;
	}

	num_chk = 0;

	id_filtros = "";
	if (reporte != 5 && reporte != 6){
		if (reporte == 1)	filtros = document.forms[0].id_sexos;
		else if (reporte == 2)	filtros = document.forms[0].id_condiciones;
		else if (reporte == 3)	filtros = document.forms[0].id_estados;
		else if (reporte == 4)	filtros = document.forms[0].id_edades;
	
		for (i=0;i<filtros.length;i++){
			if (filtros[i].checked){
				num_chk++;
	
				//Arma el string de filtros
				if (id_filtros == "")	id_filtros = filtros[i].value;
				else					id_filtros += "|" + filtros[i].value;
			}
		}
	
		if(grafica != 5 && id_filtros == ""){
			alert('Seleccione algún(a) '+ filtros_txt[reporte]);
			return false;
		}
	}

	//Muestra td result
	document.getElementById('graficaMina').style.display = '';

	ubicacion = 0;  // Para toda Colombia
	depto = 2;
	id_depto = document.getElementById('id_depto').value;
	id_muns = document.getElementById('id_muns').value;

	if (id_depto != 0){
		ubicacion = id_depto;
		depto = 1;
	}
	if (id_muns != ''){
		ubicacion = id_muns;
		depto = 0;
	}
	
	var params = 'object=GraficaResumenMina&depto='+depto+'&ubicacion='+ubicacion+'&f_ini='+f_ini+'&f_fin='+f_fin+'&filtros='+id_filtros+'&reporte='+reporte+'&grafica='+grafica;
		params += '&ejex='+ejex+'&dato_para_reporte_5_mina='+dato_para_reporte_5_mina+'&acc_vic='+acc_vic;
		
	document.getElementById('debug_info').value = server + '/sissh/api_grafica.php?' + params;

	getDataV1('graficaMina',ajax_script + '?' + params,'graficaMina');
	
}
function generarReporteMina(){

	filtros_txt = Array('','Sexos','Condiciones','Estados','Edades');

	//Reporte
	reportes = document.forms[0].reporte_mina;
	for (i=0;i<reportes.length;i++){
		if(reportes[i].checked)	reporte = reportes[i].value;
	}

	//Periodo
	f_ini = document.getElementById('ini_mina').value;
	f_fin = document.getElementById('fin_mina').value;
	
	//eje x
	ejex = document.getElementById('detalle_periodo_mina').value;
	
	//dato para reporte 5
	var dato_para_reporte_5_mina = document.getElementById('dato_para_reporte_5_mina').value;
	
	//acc o vic
	var acc_vic = 'vic';
	if (reporte == 6) acc_vic = document.getElementById('mina_acc_vic_' + reporte).value;
	
	num_chk = 0;

	id_filtros = "";
	if (reporte != 5 && reporte != 6){
		if (reporte == 1)	filtros = document.forms[0].id_sexos;
		else if (reporte == 2)	filtros = document.forms[0].id_condiciones;
		else if (reporte == 3)	filtros = document.forms[0].id_estados;
		else if (reporte == 4)	filtros = document.forms[0].id_edades;
	
		for (i=0;i<filtros.length;i++){
			if (filtros[i].checked){
				num_chk++;
	
				//Arma el string de filtros
				if (id_filtros == "")	id_filtros = filtros[i].value;
				else					id_filtros += "|" + filtros[i].value;
			}
		}
	}

	ubicacion = 0;  // Para toda Colombia
	depto = 2;
	id_depto = document.getElementById('id_depto').value;
	id_muns = document.getElementById('id_muns').value;

	if (id_depto != 0){
		ubicacion = id_depto;
		depto = 1;
	}
	if (id_muns != ''){
		ubicacion = id_muns;
		depto = 0;
	}
	
	//Opcione de reporte para nacional
	//Reporte
	tipo_nal = 0;
	if (depto == 2){
		tipo_nal = getRadioCheck(document.getElementsByName('tipo_nal'));
	}

	var params = 'object=reporteGraResumenMina&depto='+depto+'&ubicacion='+ubicacion+'&f_ini='+f_ini+'&f_fin='+f_fin+'&filtros='+id_filtros+'&reporte='+reporte+'&tipo_nal='+tipo_nal;
		params += '&ejex='+ejex+'&dato_para_reporte_5_mina='+dato_para_reporte_5_mina+'&acc_vic='+acc_vic;
		
	document.getElementById('debug_info').value = server + '/sissh/api_grafica.php?' + params;

	getDataV1('reporteGraResumenMina',ajax_script + '?' + params,'reporteGraResumenMina');
	
}

function graficarDatos(chart,gen_reporte){
	
	if (gen_reporte == 0){
		var object = 'GraficaResumenDatos';
		var inner = 'graficaDatos';
	}
	else{
		var object = 'reporteGraResumenDatos';
		var inner = 'reporteGraResumenDesplazamiento';
	}
	
	//Reporte
	var reportes = document.forms[0].reporte_dato;
	for (i=0;i<reportes.length;i++){
		if(reportes[i].checked)	reporte = reportes[i].value;
	}
	
	//dato para reporte 4
	var dato_para_reporte_4_dato = document.getElementById('dato_para_reporte_4_dato').value;

	var id_periodos = getOptionsCheckBox(document.getElementsByName('aaaa'));
	if (id_periodos == '' && (reporte == 1 || reporte == 3 || reporte == 4)){
		alert('Selecciones el Periodo');
		return false;
	}
	
	if (reporte == 1 || reporte == 3 || reporte == 4){
		var combo_dato = document.getElementById('id_dato_sectorial');
		var id_dato = combo_dato.options[combo_dato.selectedIndex].value;
	}
	else if (reporte == 2){
		selected = new Array();
		ob = document.getElementById('id_dato_reporte_2');
		for (var i = 0; i < ob.options.length; i++){
			if (ob.options[ i ].selected)
			selected.push(ob.options[ i ].value);
		}
		var id_dato = selected.join(",");
	}
		
	//Muestra td result
	document.getElementById('graficaDatos').style.display = '';

	var ubicacion = 0;  // Para toda Colombia
	var depto = 3;
	var id_depto = document.getElementById('id_depto').value;
	var id_muns = document.getElementById('id_muns').value;
	if (id_depto != 0){
		ubicacion = id_depto;
		depto = 1;
	}
	if (id_muns != ''){
		ubicacion = id_muns;
		depto = 2;
	}

	var params = 'object=' + object + '&reporte='+reporte+'&depto='+depto+'&ubicacion='+ubicacion+'&id_periodos='+id_periodos+'&id_dato='+id_dato+'&chart='+chart;
	params += '&dato_para_reporte_4_dato='+dato_para_reporte_4_dato;
	
	if (gen_reporte == 1){
	    
	    tipo_nal = 0;
		//Opcione de reporte para nacional
		if (depto == 3){
			tipo_nal = getRadioCheck(document.getElementsByName('tipo_nal'));
		}
		
		var sep_decimal = document.getElementById('sep_decimal').value;
		
		params += '&tipo_nal=' + tipo_nal+'&sep_decimal=' + sep_decimal;
	}
	
	document.getElementById('debug_info').value = server + '/sissh/api_grafica.php?' + params;

	getDataV1('graficaDatos',ajax_script + '?' + params,inner);
}

function graficarEventoC(chart,num_records){
	
	//Reporte
	obj = document.getElementsByName("reporte_evento_c");
	reporte = getRadioCheck(obj);
	
	if (reporte == 3){
		chart = 'line';
	}
	
	obj_f_ini = document.getElementById('date1_ini');
	obj_f_fin = document.getElementById('date1_fin');
	
	ubicacion = 0;  // Para toda Colombia
	depto = 3;
	id_depto = document.getElementById('id_depto').value;
	id_muns = document.getElementById('id_muns').value;
	
	id_cat = getOptionsCheck(document.getElementById('id_cat'));
	id_scat = '';
	if (document.getElementById('id_subcat')){
		id_scat = getOptionsCheck(document.getElementById('id_subcat'));
	}

	//alert("Cat" + id_cat + "|SCat:" + id_scat);

	if (obj_f_ini.value == '' && obj_f_fin.value == ''){
		alert('Selecciones el Periodo');
		obj_f_ini.focus();
		return false;
	}
	
	//Reporte 1 no puede seleccionar Mpio
	if (reporte == 1 && id_muns != ""){
		alert("Este reporte es solo a nivel Nacional o Departamental");
		return false;
	} 
	
	//Reporte 2 no puede seleccionar Mpio
	else if (reporte == 2 && id_depto != 0){
		alert("Este reporte es solo a nivel Nacional");
		return false;
	} 
	
	//Muestra td result
	document.getElementById('graficaEventoC').style.display = '';


	if (id_depto != 0){
		ubicacion = id_depto;
		depto = 1;
	}
	if (id_muns != ''){
		ubicacion = id_muns;
		depto = 0;
	}
	
	var params = 'object=GraficaResumenEventoC&reporte='+reporte+'&depto='+depto+'&ubicacion='+ubicacion+'&f_ini='+obj_f_ini.value+'&f_fin='+obj_f_fin.value+'&id_cat='+id_cat+'&id_scat='+id_scat+'&num_records='+num_records+'&chart='+chart;
	document.getElementById('debug_info').value = server + '/sissh/api_grafica.php?' + params;
	 
	getDataV1('graficaEventoC', ajax_script + '?' + params,'graficaEventoC');
}


function graficarDeptoMpio(id_ubicacion,checked){

	depto = 0;
	ubicacion = 0;
	id_datos_off = "";

	if (checked == true)	checked = 1;
	else if (checked == false)	checked = 0;

	combo_por = document.getElementById('graficar_por');
	graficar_por = combo_por.options[combo_por.selectedIndex].value;

	if (graficar_por == ''){
		alert('Seleccione el filtro');
		return false;
	}

	combo_filtro = document.getElementById('filtro_graficar_por');
	filtro_graficar_por = combo_filtro.options[combo_filtro.selectedIndex].value;

	id_depto = document.getElementById('id_depto').value;
	id_muns = document.getElementById('id_muns').value;

	cnrr = 2;
	consulta_social = 2;

	if (id_depto != ''){
		ubicacion = id_depto;
		depto = 1;
	}

	sede = 1;
	if (document.getElementById('sede')){
		if (document.getElementById('sede').checked == false)	sede = 0;
	}

	cobertura = 1;
	if (document.getElementById('cobertura')){
		if (document.getElementById('cobertura').checked == false)	cobertura = 0;
	}

	if (sede == 0 && cobertura == 0){
		alert('Se debe graficar Sede o Cobertura!');
		return false;
	}

	if (id_muns != ''){
		alert('La gráfica es a nivel Nacional o Departamental');
		return false;
	}
	if (filtro_graficar_por == ''){
		alert('Seleccione '+graficar_por);
		return false;
	}
	else{
		getDataV1('graficaConteoOrgDeptopMpio',ajax_script + '?object=graficaConteoOrgDeptoMpio&graficar_por='+graficar_por+'&filtro_graficar_por='+filtro_graficar_por+'&depto='+depto+'&ubicacion='+ubicacion+'&id_ubicacion='+id_ubicacion+'&checked='+checked+'&sede='+sede+'&cobertura='+cobertura+'&cnrr='+cnrr+'&consulta_social='+consulta_social,'graficaConteoOrg');
	}
}
function getAniosDato(id_dato,innerobject){
	
	//Reporte
	reportes = document.forms[0].reporte_dato;
	for (i=0;i<reportes.length;i++){
		if(reportes[i].checked)	reporte = reportes[i].value;
	}
	
	
	document.getElementById(innerobject).style.display = '';
	
	document.getElementById('graficaDatos').innerHTML = '';
	document.getElementById('getAniosDatoSectorial').innerHTML = '';
	document.getElementById('getAniosDatoSectorial_2').innerHTML = '';
	
	if (id_dato != ""){
		getDataV1('getAniosDatoSectorial',ajax_script + '?object=getAniosDatoSectorial&id_dato='+id_dato+'&reporte='+reporte,innerobject);
	}
}
function getAniosDato_reporte2(innerobject){
	
	//Reporte
	reportes = document.forms[0].reporte_dato;
	for (i=0;i<reportes.length;i++){
		if(reportes[i].checked)	reporte = reportes[i].value;
	}
	
	document.getElementById(innerobject).style.display = '';
	document.getElementById('graficaDatos').innerHTML = '';
	document.getElementById('getAniosDatoSectorial').innerHTML = '';
	document.getElementById('getAniosDatoSectorial_2').innerHTML = '';
	
	selected = new Array();
	ob = document.getElementById('id_dato_reporte_2');
	for (var i = 0; i < ob.options.length; i++){
		if (ob.options[ i ].selected && ob.options[ i ].value != "")
			selected.push(ob.options[ i ].value);
	}
	
	if (selected.length > 1){
		var id_datos = selected.join(",");
			
		getDataV1('getAniosDatoSectorial',ajax_script + '?object=getAniosDatoSectorial_reporte2&id_datos='+id_datos+'&reporte='+reporte,innerobject);
	}
}
function tabs(tab_name){
	
	//Muestra Mapa
	//document.getElementById('<?=$vista?>').style.display = '';	
	document.getElementById('mapa_flash').style.display = '';	
	
	tabs_total = Array("org","desplazamiento","mina","datos","evento_c");
	path = 'images/gra_resumen/';
	for(t=0;t<tabs_total.length;t++){
		document.getElementById(tabs_total[t]).style.display = 'none';
		document.getElementById('img_'+tabs_total[t]).src = path + 'menu_'+tabs_total[t]+'.jpg';
	}

	document.getElementById(tab_name).style.display = '';
	document.getElementById('img_'+tab_name).src = path + 'menu_'+tab_name+'_in.jpg';
	
	document.getElementById('tab_hidden').value = tab_name;
}

function listarMunicipios(combo_depto){

	selected = new Array();
	ob = document.getElementById(combo_depto);
	for (var i = 0; i < ob.options.length; i++){
		if (ob.options[ i ].selected)
		selected.push(ob.options[ i ].value);
	}
	var id_deptos = selected.join(",");

	if (selected.length == 0){
		alert("Debe seleccionar algún departamento");
	}
	else{
		getDataV1('comboBoxMunicipio',ajax_script + '?object=comboBoxMunicipio&multiple=17&id_deptos='+id_deptos,'comboBoxMunicipio')
	}
}

function listarSubtipos(id_combo_cat){

	selected = new Array();
	ob = document.getElementById(id_combo_cat);
	for (var i = 0; i < ob.options.length; i++){
		if (ob.options[ i ].selected)
		selected.push(ob.options[ i ].value);
	}
	var id_cats = selected.join(",");

	if (selected.length == 0){
		alert("Debe seleccionar alguna categoría");
	}
	else{
		getDataV1('comboBoxSubcategoria',ajax_script + '?object=comboBoxSubcategoria&multiple=10&separador=1&id_cat='+id_cats,'comboBoxSubcategoria')
	}
}

function changeBCG(r){

	for (i=1;i<5;i++){
		obj = document.getElementById('td_reporte_evento_c_'+i);
		obj.style.background = "#ffffff";
	
		if (i == r){
			obj.style.background = "#FFDD76";
		}
	}
	
	//Reporte 4 oculta el link de listar subcat
	if (r == 4){
		document.getElementById('link_a_subcat').style.display = 'none';
		document.getElementById('comboBoxSubcategoria').innerHTML = 'No aplica pera el reporte seleccionado';
	}
	else{
		document.getElementById('link_a_subcat').style.display = '';
		document.getElementById('comboBoxSubcategoria').innerHTML = 'Seleccione alguna categoria y use la opción Listar';
	}
}

function getDefinicionDato(id_dato,innerobject){
	
	document.getElementById(innerobject).style.display = '';
	
	getDataV1('',ajax_script + '?object=getDefinicionDatoSectorial&id_dato='+id_dato,innerobject);
}
