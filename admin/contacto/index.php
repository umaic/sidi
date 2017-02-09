<link href="//cdnjs.cloudflare.com/ajax/libs/select2/4.0.0/css/select2.min.css" rel="stylesheet" />
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.4/css/bootstrap.min.css">
<link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/font-awesome/4.3.0/css/font-awesome.min.css">
<link rel="stylesheet" href="../style/contactos.css">

<script src="../js/jquery-1.11.0.min.js"></script>
<script src="//cdnjs.cloudflare.com/ajax/libs/select2/4.0.0/js/select2.min.js"></script>
<script type='text/javascript'>

var li = '';
function aplicarFiltro(lif){

    var id_esp = 0;
    var id_org = 0;
    var id_mun = 0;

    if (lif != undefined) {
        li = lif;
    }

    id_esp = $('#id_espacio').val();
    id_org = $('#id_org').val();
    id_mun = $('#id_mun').val();

    var order_by = $('#order_by').val();
    var nombre = $('#nombre').val();

    var url = $('#url').val() + '&li=' + li;

    if (id_esp != '')	url += '&id_esp='+id_esp;
    if (id_org != '')	url += '&id_org='+id_org;
    if (id_mun != '')	url += '&id_mun='+id_mun;
    if (nombre != '')	url += '&n='+nombre;
    if (order_by != 'nom_con')	url += '&order_by='+order_by;

    url += '&jefes=' + $('input[name=jefes]:checked').val();

    location.href = url;

    return false;
}

$(function(){

    $("#id_mun").select2({
        placeholder: 'Filtrar por departamento o ciudad',
        allowClear: true
    });

    $("#id_espacio").select2({
        placeholder: 'Filtrar por espacio',
        allowClear: true
    });

    $("#id_org").select2({
        placeholder: 'Filtrar por organizacion',
        allowClear: true
    });

    $('.select2').on("select2:select", function (e) { aplicarFiltro(); });
    $('.select2').on("select2:unselect", function (e) { $('#' + e.target.id).val(undefined);aplicarFiltro(); });

    $('#textarea_all').click(function(){

        $('#email_string').select();

        return false;
    });

    $('#jefe_refrescar').click(function(){
        aplicarFiltro();
    });
})

</script>
<?php
//Text area para crear los email con comas
$value_textarea = (isset($_SESSION['string_email_contacto'])) ? $_SESSION['string_email_contacto'] : '';
?>
<div class="consulta container">
    <input type="hidden" id="url" value="<?php echo $url; ?>" />

    <div class="pull-left">
        <h1>Lista de contactos&nbsp; </h1>
    </div>
    <div class="pull-left">
        <h3> ( <?php echo $num_arr; ?> registros )</h3>
    </div>
    <div class="pull-right"><br />
        <a href='index.php?m_e=contacto&accion=insertar' class="btn btn-sm btn-primary"><i class="fa fa-plus-circle"></i> Crear nuevo cont&aacute;cto</a>
        <a href='#' class="btn btn-primary btn-sm" onclick="location.href='../export_data.php?case=xls_session&nombre_archivo=contactos';return false;"><i class="fa fa-file-excel-o"></i> Exportar listado</a>
        <a href='../OCHA_formato_contactos.xls' class="btn btn-sm btn-primary"><i class="fa fa-download"></i> Formato para captura</a>
        <a href='index.php?m_e=contacto&accion=sincro_mailchimp' class="btn btn-sm btn-primary"><i class="fa fa-cloud-upload"></i> Sincronizar con Mailchimp</a>
    </div>
    <div class="clearfix"></div>
    <br />
    <div class="row">
        <!-- Filtros -->
        <div class="col-md-4">
            <form class="form-inline">
                <h2>Incluir Jefes?</h2>
                <label class="radio-inline">
                <input type="radio" name="jefes" id="jefe_no" value="0" <?php echo $jefes_chk['no'] ?> > No
                </label>
                <label class="radio-inline">
                    <input type="radio" name="jefes" id="jefe_si" value="1" <?php echo $jefes_chk['si'] ?>> Si
                </label>
                &nbsp;<a href="#" id="jefe_refrescar" class="btn btn-success btn-xs"><i class="fa fa-rotate-right"></i> Refrescar</a>
                <h3>Buscar por nombre</h3>
                <div class="form-group">
                    <input type='text' class='form-control' id='nombre' value='<?php echo $n ?>'>
                </div>
                <button type='button' class="btn btn-primary" onclick='aplicarFiltro()'>Buscar</button>
            </form>
            <form>
            <h3>Filtros</h3>
            <!--  CIUDAD -->
            <div>
                <select id='id_mun' class='select2'><option></option>
                    <?php
                    foreach($ciudades as $depto => $ciudad) {

                        list($id_depto, $nom_depto) = explode('-',$depto);

                        echo '<option value="'.$id_depto.'" data-dm="depto"><b>=== '.$nom_depto.' ===</b></option>';

                        $cds = explode(',', $ciudad);

                        foreach($cds as $cd) {
                            $c = explode('|', $cd);

                            $selected = ($id_mun == $c[0]) ? 'selected' : '';
                            echo '<option value="'.$c[0].'" '.$selected.'>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'.$c[1].'</option>';
                        }
                    }
                    ?>
                </select>
            </div>
            <br />
            <!-- ESPACIO -->
            <div>
                <select id='id_espacio' class='select2'><option></option>
                <?php
                foreach ($espacios_x_tipo->id_espacio as $id_esp_l){
                    $espacio = $espacio_dao->get($id_esp_l);
                    if (!empty($espacio->id)) {
                        echo "<option value='$espacio->id'";
                        if ($espacio->id == $id_esp)	echo " selected ";
                        echo ">".$espacio->nombre."</option>";
                    }
                }
                ?>
                </select>
            </div>
            <br />
            <div>
                <!-- ORGANIZACION -->
                <?php
                if (count($id_orgs) > 0){
                    echo "<select id='id_org' class='select2'><option></option>";
                    echo "<option value=0>Todas</option>";
                    $org_dao->ListarCombo('combo',$id_org,"id_org IN (".implode(",",$id_orgs).")");

                    echo "</select></td></tr>";
                }
                ?>
            </div>
            <h3>Env&iacute;o a emails</h3>

            <p>Marque los emails que desee o click
                <a href='#' class='btn btn-warning btn-xs' onclick="return addEmailToContacto(0,0,'email_string');return false;">aqu&iacute;</a>
                para pasar todos los contacto de la lista a formato email
            </p>
            <div><a href="#" id="textarea_all"><i class="fa fa-file-text"></i> Seleccionar todo el texto</a></div>
            <textarea class='form-control' id='email_string' wrap='physical' rows="10"><?php echo $value_textarea ?></textarea>
            </form>
        </div>

        <!-- Lista de contactos -->
        <div class="col-md-8">
            <?php
            if ($num_arr > 0){
                ?>
                <form class="form">
                <!-- ORDENAR POR -->
                <div class="pull-left">
                    <?php
                    //INDICE
                    $this->indiceListaContactos($letras_ini,$letra_inicial);
                    ?>
                </div>
                <div class="pull-right">
                        <select id='order_by' class='form-control' onchange="aplicarFiltro()">
                            <?php
                            foreach($orders as $val=>$order) {
                                $_sel = isset($sel_order[$val]) ? $sel_order[$val] : '';
                                echo "<option value='$val' ".$_sel.">$order</option>";
                            }
                            ?>
                        </select>
                </div>
                <div class="clearfix"></div>
                </form>
            <?php
            }
            else {
                echo '<div>La b&uacute;squeda no arroj&oacute; resultados, <a href="'.$url.'">Listar todos</a></div>';
            }
            ?>
            <div class='lista'>
                <?php
                $xls = "<tr>
                <td>Nombre</td>
                <td>Apellido</td>
                <td>Organizaci&oacute;n</td>
                <td>Direcci&oacute;n</td>
                <td>Ciudad</td>
                <td>Teléfono</td>
                <td>Celular</td>
                <td>Fax</td>|
                <td>Email</td>
                <td>Skype</td>
                <td>Fecha actualizacion</td>";

                //CARACTERISITCAS
                foreach($caracts as $contacto_col){
                    $xls .= "<td>$contacto_col->nombre</td>";
                }

                // Espacios
                $xls .= '<td>Espacios de coordinacion</td>';
                $xls .= "</tr>";

                foreach($arr as $p=>$contacto){

                    //Organizacion
                    $nom_org = '';
                    $sig_org = '';
                    $actua = (empty($contacto->actua)) ? '--' : $contacto->actua;

                    if (isset($contacto->id_org[0])){
                        $id_org = $contacto->id_org[0];
                        if (is_numeric($id_org) && $id_org > 0){
                            $nom_org = $org_dao->GetName($id_org);
                            $sig_org = $org_dao->GetFieldValue($id_org,'sig_org');
                            $dir_org = $org_dao->GetFieldValue($id_org,'dir_org');

                            if (empty($sig_org)) {
                                $sig_org = substr($nom_org,0,30).'...';
                            }
                        }
                    }

                    // Ciudad
                    $nom_mun = '';
                    if (!empty($contacto->id_mun)) {
                        $nom_mun = $mun_dao->GetName($contacto->id_mun);
                    }

                    $xls .= "<tr>
                            <td>$contacto->nombre</td>
                            <td>$contacto->apellido</td>
                            <td>$nom_org</td>
                            <td>$dir_org</td>
                            <td>$nom_mun</td>
                            <td>$contacto->tel</td>
                            <td>$contacto->cel</td>
                            <td>$contacto->fax</td>
                            <td>$contacto->email</td>
                            <td>$contacto->social</td>
                            <td>$actua</td>";
                    //addEmailToContacto, funcion js en admin/general.js

                    $row = false;
                    if (fmod($p, 2) == 0) {
                        echo "<div class='row'><div class='row-same-height full-height'>";
                        $row = true;
                    }

                    echo "<div class='col-md-6 col-md-height full-height contacto'><div class='full-height'>
                            <div class='right'>
                                <input type='checkbox' value='$contacto->email' name='check_email' onclick=\"return addEmailToContacto(this.value, this.checked,'email_string')\">
                                <a href='index.php?accion=borrar&class=".$_GET["class"]."&method=Borrar&param=".$contacto->id."' onclick=\"return confirm('Está seguro que desea borrar el Contácto: ".$arr[$p]->nombre."');\">
                                    <img src='images/trash.png' border='0' />
                                </a>
                            </div>
                               <a href='".$_SERVER['PHP_SELF']."?accion=actualizar&id=".$contacto->id."' class='n'>".$contacto->nombre." ".$contacto->apellido."</a>
                               <br />";

                               //CARACTERISITCAS
                                foreach($caracts as $contacto_col){
                                    if (isset($contacto->caracteristicas[$contacto_col->id])){
                                        $nom_op = $contacto_col_op->Get($contacto->caracteristicas[$contacto_col->id]);
                                        echo $contacto_col->nombre.": ".$nom_op->nombre."<br />";
                                        $xls .= "<td>".$nom_op->nombre."</td>";
                                    }
                                    else{
                                        $xls .= "<td></td>";
                                    }
                                }


                               echo "<br />
                               Organizaci&oacute;n: <a href='#' onclick=\"window.open('ver.php?class=OrganizacionDAO&method=Ver&param=".$id_org."','','top=30,left=30,height=900,width=900,scrollbars=1');return false\">".$sig_org."</a>
                               <br />
                               Email:<b><a href='mailto:".$contacto->email."'>".$contacto->email."</a></b>
                               <br />
                               Skype: <b>".$contacto->social."</b>
                               <br />
                               Tel: <b>".$contacto->tel."</b>
                               <br />
                               Cel: <b>".$contacto->cel."</b>
                               <br />
                               Fax: <b>".$contacto->fax."</b>
                               <br />
                               Ciudad: <b>".$nom_mun."</b>
                               <br />
                               Ultima actualizaci&oacute;n: <b>$actua</b>
                       </div></div>
                       ";

                        if ($p%2 == 1) {
                           echo "</div></div>";
                       }

                       // Espacios
                       $esps = $this->getContactoEspacios($contacto->id);
                       $xls .= '<td>'.implode('|', $esps['noms']).'</td>';

                    $xls .=	"</tr>";
                }
                ?>
            </div>
        </div>
    </div>
</div>

<?php

$_SESSION["xls"] = "<table border=1>$xls</table>";

?>
