function showDiv (div_id,accion){
	tabla = document.getElementById("table_" + div_id);
	div = document.getElementById(div_id);
	if (accion == 'mostrar'){

		left = (docwidth() - 950 )/2 + 210;
		div.style.left = left+'px';
		div.style.top = '150px';
		tabla.style.visibility = 'visible';
	}
	else{
		tabla.style.visibility = 'hidden';

		//Coloca los datos de la ubicacion
		getData('ubicacion');
	}
}

function unHide(td,img){
	if (document.getElementById(td).style.display == 'none'){
		document.getElementById(td).style.display = '';
		document.getElementById(img).src = '../images/flecha_down.gif';
	}
	else{
		document.getElementById(td).style.display = 'none';
		document.getElementById(img).src = '../images/flecha.gif';
	}
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
function validar_criterios(){

	var error = 1;
	var filtros = Array('id_tipo_org','id_sector','id_enfoque','id_poblacion','id_depto');

	for (f=0;f<filtros.length;f++){
		if (validarComboM(document.getElementById(filtros[f]))){
			error = 0;
		}
	}

	if (error == 1){
		alert("Seleccione algún criterio");
		return false;
	}
	else{
		return true;
	}

}

function asignarVariablesH(id_depto,id_mun){
	parent.document.getElementById('id_depto').disabled = false;
	parent.document.getElementById('id_depto').value = id_depto;
	parent.document.getElementById('id_muns').disabled = true;
	parent.document.getElementById('id_muns').value = '';
	if (id_mun != 0){
		parent.document.getElementById('id_muns').disabled = false;
		parent.document.getElementById('id_muns').value = id_mun;
	}

}

function ShowHide(id_ele){
	obj = document.getElementById(id_ele);

	if (obj.style.display == 'none')	obj.style.display = '';
	else								obj.style.display = 'none';

}