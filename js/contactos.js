$(function() {
    $('div.o').click(function() { 
        var chk = ($(this).find('input').attr('checked')) ? '' : 'checked';
        $(this).find('input').attr('checked',chk);

    });

    $('#lista_search').keyup(function() {
        filterList('div.o', $(this).val());
    });

    $('a.filter_letter').click(function() {
        filterList('div.o', '^' + $(this).text());
    });

    $('a.filter_reset').click(function() { 
        $('div.o').show();    
    });

    // Search org
    // Live search
    $('#org_search').keyup(function(e) {
        
        var s = $("#org_search").val();
        var html = '<div>No se encontraron organizaciones';

        if (s.length > 1) {
            $.ajax({
                url: '/sissh/admin/ajax_data.php?object=ocurrenciasOrgJson&s=' + s,
                success: function(json) {
                    $divr = $('#org_search_results');
                    
                    var num = json.length;

                    if (num > 0) {
                    
                        html = '<div>' + num + ' Organizaciones..</div>';
                        html += '<table><tr><th>Nombre</th><th>Sigla</th><th>Sede</th></tr>';
                        
                        $.each(json, function(i,o) { 
                            html += '<tr id='+o.id+'><td class="n">' + o.nom + '</td><td>'+o.sig+'</td><td>'+o.mun+'</td></tr>';
                        });
                        
                        html += '</table>';
                        
                        $divr.find('tr').live('click', function(){
                            $('#org_id').val($(this).attr('id'));
                            $('#org_nom').text($(this).find('td.n').text());
                            $divr.hide();
                        });
                    }

                    $divr.html(html);
                    $divr.show(); 

                }
            });
        }
    });

    // Crear opciones en cargo, encabezado, etc
    $('div.crear_col').each(function() { 
        $(this).click(function() { 
            var col_id = $(this).attr('data-col-id');
            
            $('#div_col_val_' + col_id).toggle();
            return false;
        });
    });

    $('a.crear_col').click(function() { 
        var _id = $('this').attr('id');

        if ($('#' + _id + '_val').val() != '') {
            
            var col_id = $(this).attr('data-col-id');
            var error = false;
            var val = $('#crear_' + col_id + '_val').val(); 
            var $combo = $('#' + col_id + '_opcion');

            // Verifica que no exista la opcion
            $combo.find('option').each(function() { 
                if (val.toLowerCase() == $(this).text().toLowerCase()) {
                    error = true;
                }
            });

            if (error) {
                alert('La opci\xf3n que esta intentando crear existe!');

                return false;
            }

            $.ajax({
                url: 'ajax_data.php?object=crearContactoColOpcion&id_col='+ col_id +'&val='+val,
                success: function(id) {
                    $combo.append('<option value="'+id+'">'+val+'</option>');
                    $combo.val(id);
                    $('#div_col_val_' + col_id).hide();
                }
            });
        }

        return false;        
    });

    $('form').submit(function() { 
        var error = false;
        var msg = "Errores:\n\n";

        if ($('#nombre').val() == '') {
            error = true;
            msg += "- Nombre: es obligatorio \n";
        }
        
        if ($('#apellido').val() == '') {
            error = true;
            msg += "- Apellido: es obligatorio \n";
        }
        
        if ($('#email').val() == '') {
            error = true;
            msg += "- Email: es obligatorio \n";
        }
        
        if ($('#id_mun').val() == '') {
            error = true;
            msg += "- Municipio: es obligatorio \n";
        }
        
        if ($('#org_id').val() == 0) {
            error = true;
            msg += "- Organizaci\xf3 a la que pertenece: es obligatorio \n";
        }

        if ($('#email').val() != '') {
                var emailReg = /^([\w-\.]+@([\w-]+\.)+[\w-]{2,4})?$/;
         
                if(!emailReg.test($('#email').val())) {
                    error = true;
                    msg += "- Email: formato inv\xe1lido \n";
                }
        }

        if (error) {
            alert(msg);

            return false;
        }


    })
});

function filterList(sel, val) {
    
    var re = new RegExp(val, "i"); // "i" means it's case-insensitive
    
    $(sel).show().filter(function() {
        return !re.test($(this).text());
    }).hide();
};

// Lista municipios
function lM(combo_depto){
    var id_deptos = $('#' + combo_depto).val();
    getDataV1('comboBoxMunicipio','ajax_data.php?object=comboBoxMunicipio&multiple=0&titulo=0&separador_depto=0&id_name=id_mun&id_deptos='+id_deptos,'comboBoxMunicipio')
}

