//Coloca en la variable hidden map_extent, el nuevo valor de extent para el depto
function setExtentByDepto(id_depto,id_hidden){
	
	if (id_depto > 0){
		
		if (document.getElementById('btn_generar_despla') != undefined){
			//Desactiva el boton de generar
			document.getElementById('btn_generar_despla').disabled = 'true';
			document.getElementById('btn_generar_despla').value = 'Cambiando Depto..';
		}
	
		//funcion en js/ajax.js
		getDataToMapserver('ajax_data.php?object=getExtentByDepto&id_depto='+id_depto,id_hidden);
		
	}
	else{
		//var extent_org = '-161112.1,1653895,-469146,1386463';  Definida en los archivos donde se use esta función
		document.getElementById(id_hidden).value = extent_org;
	}
}

//Coloca en la variable hidden map_extent, el nuevo valor de extent para el depto, caso UNICEF que recarga auto el mapa
function setExtentByDeptoUnicef(id_depto,id_hidden){
	
	if (id_depto > 0){
		
		if (document.getElementById('btn_generar_despla') != undefined){
			//Desactiva el boton de generar
			document.getElementById('btn_generar_despla').disabled = 'true';
			document.getElementById('btn_generar_despla').value = 'Cambiando Depto..';
		}
	
		//funcion en js/ajax.js
		getDataToMapserver('ajax_data.php?object=getExtentByDepto&id_depto='+id_depto,id_hidden);
		
	}
	else{
		//var extent_org = '-161112.1,1653895,-469146,1386463';  Definida en los archivos donde se use esta función
		document.getElementById(id_hidden).value = extent_org;
	}

    // mapaProyecto definida en /consulta/unicef_mapserver_ajax.php
    setTimeout('mapaProyecto()',500);
}

//Coloca en la variable hidden map_extent, el nuevo valor de extent para el mpio
function setExtentByMpio(id,id_hidden){
	
	var extent_orig = document.getElementById(id_hidden).value;
	
	if (id > 0){
		
		if (document.getElementById('btn_generar_despla') != undefined){
			//Desactiva el boton de generar
			document.getElementById('btn_generar_despla').disabled = 'true';
			document.getElementById('btn_generar_despla').value = 'Cambiando Municipio..';
		}
	
		//funcion en js/ajax.js
		getDataToMapserver('ajax_data.php?object=getExtentByMpio&id_mpio='+id+'&extent_orig='+extent_orig,id_hidden);
		
	}
	else{
		//var extent_org = '-161112.1,1653895,-469146,1386463';  Definida en los archivos donde se use esta función
		document.getElementById(id_hidden).value = extent_org;
	}
}

//Parse el valor del extent del hidden para mscross, de string a arreglo
function parseMapExtent(id_hidden){

	//var extent_org = '-161112.1,1653895,-469146,1386463';  => asi debe ser el formato del hidden
	
	//(xmin,xmax,ymin,ymax)
	var map_extent = document.getElementById(id_hidden).value.split(",");
	
	//String to Float para que no genere NAN en extent
	map_extent[0] = parseFloat(map_extent[0]);
	map_extent[1] = parseFloat(map_extent[1]);
	map_extent[2] = parseFloat(map_extent[2]);
	map_extent[3] = parseFloat(map_extent[3]);
	
	return map_extent;

}

//Parse el valor del extent del hidden para mscross, de string a arreglo
function parseMapExtentPrint(id_hidden){

	//var extent_org = '-161112.1,1653895,-469146,1386463';  => asi debe ser el formato del hidden
	
	//(xmin,xmax,ymin,ymax)
	var map_extent = opener.document.getElementById(id_hidden).value.split(",");
	
    //String to Float para que no genere NAN en extent
	map_extent[0] = parseFloat(map_extent[0]);
	map_extent[1] = parseFloat(map_extent[1]);
	map_extent[2] = parseFloat(map_extent[2]);
	map_extent[3] = parseFloat(map_extent[3]);
	
	return map_extent;

}
