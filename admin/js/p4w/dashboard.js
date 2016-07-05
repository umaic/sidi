function fOrgsDashboard(e, c){

    var texto = $j(e.target).find('option:selected').val();
    var cs = 'sig';

    if (c == 'nom') {
        texto = e.target.value;
        keyNum = e.keyCode;
        cs = 'nom';
	
        if (keyNum == 8){  //Backspace
            texto = texto.slice(0, -1);  //Borra el ultimo caracter
        }
        else{
            keyChar = String.fromCharCode(keyNum);
            texto +=  keyChar;
        }
    }
    else if (c == 'order') {
        if (texto.length > 1){
            insertParam('order', texto);
        }
    } 

    $j('#filtro_nom').addClass('loading');

	if (texto.length > 1){
        
        var re = new RegExp(texto, 'i');

        $j('.grid').each(function(){
            if ($j(this).find('.' + cs).html().match(re)) {
                $j(this).show();
            }
            else {
                $j(this).hide();
            }
        });
	}
    else {
        $j('.grid').each(function(){ $j(this).show(); });
    }
    
    $j('#filtro_nom').removeClass('loading');

    totalp();
    
    hideShowButton();
}

function hideShowButton() {
    var fr = 50;

    $j('#masp').show();
    if ($j('.prj:visible').length < fr) {
        $j('#masp').hide();
    } 
}

function fSigla() {
    var oS = $j('#filtro_sig');
    var options = oS.find('option:not(:first)');
    var used = [];

    $j(options).each(function(){ used[$j(this).text()] = 1 });

    $j('.sig').each(
        function() {
            
            var ht = $j(this).html();

            if (used[ht] == undefined) {
                oS.append($j("<option />").val(ht).text(ht));
                used[ht] = 1;
            }
        }
    );
    
    // Keep track of the selected option.
    var selectedValue = oS.val(); 
    
    // Sort by name
    oS.html($j("#filtro_sig option").sort(function (a, b) {
        return a.text == b.text ? 0 : a.text < b.text ? -1 : 1;
    }));

    oS.val(selectedValue);
    

}
function dashboardScrollLoad() {
    
    var t = getURLParameter('t');
    var undaf = getURLParameter('undaf');
    var order = getURLParameter('order');
    $j('div#scrollLoader').show();
    $j.post('ajax_data.php?object=dashboardScrollLodaer4w&undaf=' + undaf + '&t=' + t + '&sr=' + $j(".prj").length,  
        function(data){
            if (data != "") {
                $j(".prj:last").after(data);       
                fSigla();
                
                // Oculta el boton de mas proyectos
                if (String($j(".prj").length).substr(-1) != '0' && String($j(".prj").length).substr(-1) != '5') {
                    $j('#masp').hide();
                } 

                crud();
            }

            $j('div#scrollLoader').hide();
        });

    totalp();
};

function borrarP(id) {
    if (confirm('Esta seguro?')) {
        $j.post('ajax_data.php?object=borrar&class=P4wDAO&method=Borrar&param=' + id,  
            function(data){
                if (data.success == 1) {
                    $j('td#' + id).parent('tr.grid').hide('fast'); 
                }
                else {
                    alert('No se puede borrar el proyecto');
                }
            }, 'json'
        );
    }

    totalp();
}

function crud() {
    $j('tr.grid').each(function(){
        var ops = $j(this).find('.ops a');
        $j(this).hover(function(){ ops.show() }, function(){ ops.hide() });
        $j(this).find('td.nom').click(function(){ location.href='?accion=actualizar&id=' + $j(this).attr('id'); });
      });
}

function totalp() {
    $j('#ttp').html($j('.prj:visible').length);
}

$j(function(){
    
    // Crud
    crud();
    
    // Filtro sigla
    fSigla();

    if (!getURLParameter('order')) {   // ! busca vacios, null, undefined, false
        $j('#order').val(getURLParameter('order'));
    }

    // Mas proyectos
    $j('#masp').click(function(){ dashboardScrollLoad() });
    $j('#todos').click(function(){ location.href += '&sr=t'; return false; });
    
    if (String($j(".prj").length).substr(-1) != 0) {
        $j('#masp').hide();
    }

    // Coloca numero total de proys en []
    totalp();

    // Buscar
    $j('#buscar_btn').click(function(){ 
        
        var error = '';
        
        if ($j('#buscar_codigo').val() != '') {
            location.href += '&codigo=' + $j('#buscar_codigo').val();
        }
        else if ($j('#buscar_encargado').val() != '') {
            location.href += '&encargado=' + $j('#buscar_encargado').val();
        }
        else if ($j('#buscar_donante').val() != '') {
            location.href += '&donante=' + $j('#buscar_donante').val();
        }
        else {
            error += 'Digite el codigo o seleccione un donante';
        }
        
        if (error != '') {
            alert(error);
        } 

        return false;
    });
    
    $j("#buscar_encargado").select2({
        placeholder: 'Encargado',
        allowClear: true
    });
    
    $j("#buscar_donante").select2({
        placeholder: 'Donante',
        allowClear: true
    });

});
