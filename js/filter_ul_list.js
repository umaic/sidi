function filtrarUL(pattern,id_ul,id_show_number,texto){
	var ul_obj = document.getElementById(id_ul);
	var flags, regexp;

	//Sin caso sensitivo
	flags = 'i';

	//Muestra el buscando
	if (id_show_number != ''){
		var show_number_obj = document.getElementById(id_show_number);
		show_number_obj.innerHTML = "<img src='images/ajax/loading_ind.gif'>&nbsp;Filtrando...";
	}
	
	if (ul_obj == undefined){
		alert("No existe el objeto con id = " + id_ul);
		return false;
	}


	regexp = new RegExp(pattern, flags);
	var li_objs = ul_obj.childNodes;
	var ocurrencias = 0;

	for (var i = 0; i < li_objs.length; i++){
		if (!regexp.test(li_objs[i].innerHTML))	li_objs[i].style.display = 'none';
		else{
			li_objs[i].style.display = '';
			ocurrencias++;
		}
	}

	//Muestra el numero de ocurrencias
	if (id_show_number != ''){
		if (ocurrencias > 0){
			show_number_obj.innerHTML = ocurrencias + texto ;
		}
		else{
			show_number_obj.innerHTML = '';
		}
	}
}
