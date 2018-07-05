<?
//INICIALIZACION DE VARIABLES
$p_vo = New P4w();
$proyecto_dao = New P4wDAO();
$org_vo = New Organizacion();
$org_dao = New OrganizacionDAO();
$depto_vo = New Depto();
$depto_dao = New DeptoDAO();
$mun_dao = New MunicipioDAO();
$sector_dao = New SectorDAO();
$sector_vo = New Sector();
$enfoque_dao = New EnfoqueDAO();
$enfoque_vo = New Enfoque();
$poblacion_dao = New PoblacionDAO();
$poblacion_vo = New Poblacion();
$estado_dao = New EstadoProyectoDAO();
$estado_vo = New EstadoProyecto();
$tema_dao = New TemaDAO();
$tema_vo = New Tema();
$con_dao = New ContactoDAO();
$moneda_dao = New MonedaDAO();
$moneda_vo = New Moneda();
$tipo_proyecto_dao = New TipoProyectoDAO();
$emergencia_dao = New EmergenciaDAO();
$emergencia_vo = New Emergencia();
$cbt_ma_dao = New ModalidadAsistenciaDAO();
$cbt_me_dao = New MecanismoEntregaDAO();

$conn = MysqlDb::getInstance();

if (isset($_GET["accion"])){
    $accion = $_GET["accion"];
}
else if (isset($_POST["accion"])){
    $accion = $_POST["accion"];
}

$ver = (!empty($_GET['ver'])) ? true : false;

$id_depto = Array();
$num_deptos = 0;
$id_cat = 0;
$chk_conf = "";
$id_orgs_d = ""; //Organización donante
$id_orgs_e = ""; //Organización ejecutora
$id_orgs_s = ""; //Organización implementadora
$id_orgs_b = ""; //Organización beneficiaria
$display_socios_un = 'none';
$costo_proy = '';
$costo_proy_ind = '';
$costo_proy1 = '';
$costo_proy2 = '';
$costo_proy3 = '';
$costo_proy4 = '';
$costo_proy5 = '';
$tsubmit = 'Validar Proyecto';

$si_proy = $_SESSION['si_proy'];
//$sector_title_tab = array('4w' => 'SECTOR', 'undaf-des-paz' => 'RESULTADOS');

//Moneda, por defecto U$
$p_vo->id_mon = 1;

//Caso de Actualizacion
if ($accion == "actualizar" && !isset($_POST["submit"])){
    $id = $_GET["id"];
    $p_vo = $proyecto_dao->Get($id);

    $id_depto = $p_vo->id_deptos;
    $id_muns = $p_vo->id_muns;

    $id_orgs_d = implode("|",$p_vo->id_orgs_d);
    $id_orgs_e = implode("|",$p_vo->id_orgs_e);
    $id_orgs_s = implode("|",$p_vo->id_orgs_s);
	$id_orgs_b = implode("|",$p_vo->id_orgs_b);
    $id_orgs_coor = implode("|",$p_vo->id_orgs_coor);

    $id_beneficiarios = implode("|",$p_vo->id_beneficiarios);
    //$id_beneficiarios_cant = implode("|",$p_vo->cant_per);

    if ($p_vo->inicio_proy == "0000-00-00")	$p_vo->fecha_ini = "";
	if ($p_vo->ofar == "0000-00-00")	$p_vo->ofar = "";

}
?>
<script type="text/javascript" src="js/tabber.js"></script>
<script type="text/javascript" src="js/p4w/insert.js"></script>
<script type="text/javascript" src="js/p4w/jquery.constantfooter.js"></script>
<script type="text/javascript" src="../js/openlayers.js"></script>
<script type="text/javascript" src="../js/OpenStreetMap.js"></script>
<script type="text/javascript">

function doEmergencias() {
    var $a = $j('#calbergues');
    var $o = $j('#cnal, #cob_opts, #cob_rules');
    
    var id_e = $j('#id_emergencia').val();

    // Titulo de emergencia
    $j('#h1_emergencia').html($j(this).find('option:selected').html());

    if (id_e == 1) {
        $a.show();
        $o.hide();
    }
    else {
        $o.show();
        $a.hide();

        // Desmarca depto y mun
        var id_depto = $j('#id_albergue').find('option:selected').closest('optgroup').data('depto');
        var $d = $j('#d_' + id_depto);

        $d.prop('checked', false);
        
        $j('#lista_mpios_' + id_depto).find('input:checkbox').prop('checked', false);
    }
}

function doAlbergues(v) {
    
    var $a = $j('#albergues');
    var $o = $j('#cob_no_nac');
    
    // Titulo de emergencia
    $j('#h1_emergencia').html($j('#id_emergencia').find('option:selected').html());

    if (v == 1) {
        $a.show();
        $o.hide();
    }
    else {
        $o.show();
        $a.hide();
    }
}

$j(function() {
    
    $j('form').submit(function(event){ 

        // Valida presupuesto
        var pres = $j('#costo_proy').val()*1;
        if (pres > 150000000) {
            alert('El presupuesto es mayor a 150 millones USD, esta seguro? Recuerde que el presupusto es en dolares...');
            return false;
        } 

        // Aporte donantes menor o igual que presupuesto
        var total = 0;
        $j('.don').find('textarea.tshort').each(function(){ 
            total += $j(this).val()*1;
        });

        if (total > pres) {
            alert('El aporte de donantes no puede ser mayor al presupuesto, recuerde que todo debe estar en dolares');
            return false;
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

        // en js/p4W/ext3.js
        submitForm(event);

        return false;
    });
    <?php 
    if ($ver) {
        ?>
        //$j('.ver').each(function() { $j(this).hide; });
        $j('.ver').hide();
        $j(':input').attr('readonly', 'readonly');
        <?php
    }
    else {
        ?>
        $j('#inicio_proy, #fin_proy, #ofar').datepicker({dateFormat:'yy-mm-dd'});

        $j(document).click(function(event) {
            if (!$j(event.target).hasClass('ocurrencia')) {
                 $j(".ocurrencia").hide();
            }
        });
        
        $j('#cob_opts a').each(function(){ $j(this).click(function() { toggleCob($j(this).html().toLowerCase()); }) });
        $j('#benef input:text').each(function(){ $j(this).keypress(function(e){ return validarNum(e); }) });
 
        chkc(true);
        chkt();
        chkb();
        resumen();
        enaGuadar();
        // Activa el mapa si no tiene cobertura
        if ($j('#cobertura_nal_proy').val() == 0) {
            initMap();
        }
        
        $j("#resumen").constantfooter({showminimize: true });

    <?php 
        if ($accion == "actualizar" && !isset($_POST["submit"])) {
            ?>
            resumenActualizar();
            <?php
        }
    }
    ?>

    var $calbergues = $j('#cobertura_albergue');

    // Editar proyecto: info de emergencias
    doEmergencias();
    
    // Editar proyecto: info de albergues
    var v = ($j('#albergues').find('input:checked').length > 0) ? 1 : 0;

    $calbergues.val(v);
    doAlbergues(v);

    // Emergencia frontera colombo-venezolana albergues
    $j('#id_emergencia').change(function(){ 

        doEmergencias();

    });
    
    $calbergues.change(function(){ 
        
        doAlbergues($j(this).val());
    });

    $j('.albergue_chk').click(function(){ 

        var id_a = $j(this).val();
        var that = $j(this);
        var id_mun = new String(that.data('mun')); 
        var id_depto = that.data('depto');
        var $d = $j('#d_' + id_depto);
        var $mm = $j('#map_muns');

        var chk = that.prop('checked');

        if (chk) {
            
            checkDeptoLista(id_mun, true);

            // Tiempo espera carga municipios
            setTimeout(function(){ 
                var $m = $j('#mun_' + id_mun);
                $m.prop('checked', true);
            }, 1000);
            
            $mm.append( '<input type="hidden" name="latitude[' + id_mun + ']" value="' + that.data('lat') + '"  /> ' + 
                        '<input type="hidden" name="longitude[' + id_mun + ']" value="' + that.data('lon') + '"  /> ');
            
        }
        else {
            var $m = $j('#mun_' + id_mun);
            $d.prop('checked', false);
            $m.prop('checked', false);
            $mm.html('');
        }
            
    });
    
});

</script>

<form method="POST" action="" name="p4w">
<div id="alim_insertar">
    <div class="wiki right"><img src="images/p4w/qm.png" />
        <a href="https://wiki.umaic.org/wiki/4W" target="_blank">Wiki</a>
    </div>
    <div id="tab4w" class="clear tabber">
        <div class="tabbertab" id="info_basica">
            <h2>Informaci&oacute;n b&aacute;sica</h2><br>
            <div class="left rules">
                Para <b>Organizaci&oacute;n</b> ejecutora, Implementadores y Donantes, 
                escriba el <b>NOMBRE</b> o la 
                <b>SIGLA</b> en Espa&ntilde;ol o Ingl&eacute;s y seleccione la Organización de <br />
                la lista que aparecer&aacute;. Si la busqueda no arroja resultados, utilice el bot&oacute;n <br />
                de la esquina derecha, + Organizaci&oacute;n, cree la Organizaci&oacute;n y realice la busqueda <br />
                de nuevo en el campo correspondiente.  Para <b>Contacto</b> puede buscar por nombre, apellido o email.
            </div>
            <div id="crear" class="right">
                <ul>
                    <li><a href="#" onclick="if (confirm('Est\xe1 seguro que la Organizaci\xf3n no existe en el sistema? Intente con todas las opciones posibles')) 
                                                 {addWindowIU('org','insertarOrg4w','');}return false;" class="boton icon insertar">
                            Organizaci&oacute;n</a>
                    </li>
                    <li><a href="#" onclick="if (confirm('Est\xe1 seguro que el Contacto no existe en el sistema? Intente con todas las opciones posibles')) 
                                                 {addWindowIU('contacto','insertarCon4w','');}return false;" class="boton icon insertar">
                            Contacto</a>
                    </li>
                </ul>
            </div>
            <div class="clear"></div>
            <div class="left basico">
                <div class="field">
                    <b>ID</b> : <?php echo $p_vo->id_proy ?>
                </div>
                <div class="field">
                    <label for="id_estp">Estado</label>
                        <select id="id_estp" name="id_estp" class="select ri">
                        <option value=''>Seleccione alguno...</option>
                            <?
                            //ESTADO DEL PROYECTO
                            $estado_dao->ListarCombo('combo',$p_vo->id_estp,'');
                            ?>
                        </select>
                </div>
                <div class="field">
                    <label for="tip_proy">Tipo</label>
                    <select id="tip_proy" name="tip_proy" class="select ri">
                        <option value=''>Seleccione alguno...</option>
			            <?
			            //TIPO PROYECTO
			            $tipo_proyecto_dao->ListarCombo('combo',$p_vo->tip_proy,'');
			            ?>
                    </select>
                </div>
                <div class="field">
                    <label for="cod_proy">C&oacute;digo</label>
                        <input type="text" id="cod_proy" name="cod_proy" value="<?php echo $p_vo->cod_proy ?>" size="10" class="textfield ri" />
                </div>
                <div class="field">
                    <label for="inicio_proy">Fecha de inicio</label>
                        <input type="text" id="inicio_proy" name="inicio_proy" value="<?php echo $p_vo->inicio_proy ?>" size="10" class="textfield ri" />
                </div>
                <div class="nota">* Diligencie los meses o la <br />fecha de finalizaci&oacute;n</div>
                <div class="field">
                    <label for="duracion_proy">Duración en meses</label>
                    <input type="text" name="duracion_proy" id="duracion_proy" class="textfield ri" value="<?=$p_vo->duracion_proy;?>" size="5"  onkeypress="return validarFloat(event)"/>
                </div>
                <div class="field">
                    <label for="fin_proy">Fecha finalizaci&oacute;n</label>
                        <input type="text" id="fin_proy" name="fin_proy" value="<?php echo $p_vo->fin_proy ?>" size="10" class="textfield ri" />
                </div>
                <div class="field">
                    <label for="costo_proy">Presupuesto Total (USD)</label>
                        <input type="hidden" name="id_mon" value="1">
                        <input type="text" name="costo_proy" id="costo_proy" class="textfield ri" value="<? echo $p_vo->costo_proy;?>" size="15" 
                        onkeypress="return validarNum(event)"/>
                        <br /><span class="nota">Valor enterno sin comas ni puntos</span>
                </div>
                <div class="field">
                    <label for="ofar">Fecha adjudicación de Recursos</label>
                    <input type="text" id="ofar" name="ofar" value="<?php echo $p_vo->ofar ?>" size="10" class="textfield" />
                </div>
                <div class="field">
                    <label for="costo_proy1">Presupuesto Año 1 (USD)</label>
                    <input type="text" name="costo_proy1" id="costo_proy1" class="textfield" value="<? echo $p_vo->costo_proy1;?>" size="15"
                           onkeypress="return validarNum(event)"/>
                    <br /><span class="nota">Valor enterno sin comas ni puntos</span>
                </div>
                <div class="field">
                    <label for="costo_proy2">Presupuesto Año 2 (USD)</label>
                    <input type="text" name="costo_proy2" id="costo_proy2" class="textfield" value="<? echo $p_vo->costo_proy2;?>" size="15"
                           onkeypress="return validarNum(event)"/>
                    <br /><span class="nota">Valor enterno sin comas ni puntos</span>
                </div>
                <div class="field">
                    <label for="costo_proy3">Presupuesto Año 3 (USD)</label>
                    <input type="text" name="costo_proy3" id="costo_proy3" class="textfield" value="<? echo $p_vo->costo_proy3;?>" size="15"
                           onkeypress="return validarNum(event)"/>
                    <br /><span class="nota">Valor enterno sin comas ni puntos</span>
                </div>
                <div class="field">
                    <label for="costo_proy4">Presupuesto Año 4 (USD)</label>
                    <input type="text" name="costo_proy4" id="costo_proy4" class="textfield" value="<? echo $p_vo->costo_proy4;?>" size="15"
                           onkeypress="return validarNum(event)"/>
                    <br /><span class="nota">Valor enterno sin comas ni puntos</span>
                </div>
                <div class="field">
                    <label for="costo_proy5">Presupuesto Año 5 (USD)</label>
                    <input type="text" name="costo_proy5" id="costo_proy5" class="textfield" value="<? echo $p_vo->costo_proy5;?>" size="15"
                           onkeypress="return validarNum(event)"/>
                    <br /><span class="nota">Valor enterno sin comas ni puntos</span>
                </div>
            </div>
            <div class="left oth">
                <div class="field">
                <label for="id_emergencia">Emergencia a la que responde</label>
                    <select id="id_emergencia" name="id_emergencia" class="select ddlarge">
                    <option value=''>No aplica</option>
                        <?
                        // Emergencias
                        $emergencia_dao->ListarCombo('combo',$p_vo->id_emergencia,'');
                        ?>
                    </select>
            
                </div>
                <div class="field">
                    <?php 
                    // Alimentador ORG 4w y undaf
                    $readonly = $nom_org = $id_org = '';
                    $cls = 'ri';
                    $oc = "onkeydown=\"buscarOcurr(event, 'nom_org', 'id_org', 'ocurr_org');\"";
                    $org_name = '';
                    if ($_SESSION['id_tipo_usuario_s'] == 41
                        || $_SESSION['id_tipo_usuario_s'] == 27) {
                        $readonly = 'readonly';
                        $org_name = $_SESSION['nom_org'];
                        $id_org = $_SESSION['id_org'];
                        $cls = $oc = '';
                        $tsubmit = 'Guardar Proyecto';
                    }
                    ?>
                    <label for="id_org">Organizaci&oacute;n ejecutora</label>
                    <input type="hidden" id="id_org" name="id_orgs_e[]" value="<?php echo (empty($p_vo->id_orgs_e[0])) ? $id_org : $p_vo->id_orgs_e[0] ?>" class="<?php echo $cls ?>" />
                    <textarea type="text" id="nom_org" name="nom_org" <?php echo $readonly ?>
                    class="textfield tlarge" <?php echo $oc ?> ><?php echo (empty($p_vo->id_orgs_e[0])) ? $org_name : $org_dao->GetName($p_vo->id_orgs_e[0]); ?></textarea>
                    <div id="ocurr_org" class="ocurrencia"></div>
                </div>
                <div class="field">
                    <label for="nom_proy">Nombre del proyecto</label>
                    <textarea name="nom_proy" id="nom_proy" class="textfield ri tlarge nom_proy"><?=$p_vo->nom_proy;?></textarea>
                    <label for="des_proy">Descripci&oacute;n del proyecto</label>
                    <textarea name="des_proy" id="des_proy" class="textfield tlarge des_proy ri"><?=$p_vo->des_proy;?></textarea>
                </div>
                <div class="field">
                    <label for="id_con">Contacto en terreno</label>
                    <input type="hidden" id="id_con" name="id_con" value="<?php echo (!empty($p_vo->id_con)) ? $p_vo->id_con : '' ?>" class="ri" />
                    <textarea type="text" id="nom_con" name="nom_con" 
                    class="textfield tlarge" onkeydown="buscarOcurr(event, 'nom_con', 'id_con', 'ocurr_con');"><?php echo (!empty($p_vo->id_con)) ? $con_dao->GetName($p_vo->id_con) : ''; ?></textarea>
                    <div id="ocurr_con" class="ocurrencia ocurrencia_large"></div>
                </div>
                <div class="field">
                    <?php 
                    $srp_no = ($p_vo->srp_proy == 0) ? 'checked' : '';    
                    $srp_si = ($p_vo->srp_proy == 1) ? 'checked' : '';    
                    ?>
                    <label for="srp_proy">Hace parte del plan estrat&eacute;gico de respuesta</label>
                    <input type="radio" id="srp_no" name="srp_proy" value="0" <?php echo $srp_no ?> />&nbsp;<label for="srp_no" class="ch">No</label>&nbsp;
                    <input type="radio" id="srp_si" name="srp_proy" value="1" <?php echo $srp_si ?> />&nbsp;<label for="srp_si" class="ch">Si</label>&nbsp;
                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;(<a href="https://wiki.umaic.org/wiki/Plan_de_Respuesta_Humanitaria" target="_blank">SRP por sus siglas en ingl&eacute;s</a>)
                </div>
                <div class="field">
                    <?php
                    $inter_no = ($p_vo->inter == 0) ? 'checked' : '';
                    $inter_si = ($p_vo->inter == 1) ? 'checked' : '';
                    ?>
                    <label>¿Es un proyecto que cumple los requisitos de interagencialidad?</label>
                    <input type="radio" id="inter_no" name="inter" value="0" <?php echo $inter_no ?> />&nbsp;<label for="inter_no" class="ch">No</label>&nbsp;
                    <input type="radio" id="inter_si" name="inter" value="1"  <?php echo $inter_si ?>/>&nbsp;<label for="inter_si" class="ch">Si</label>&nbsp;
                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;(<a href="https://wiki.umaic.org/wiki/Proyecto_Interagencial" target="_blank">Criterios de interagencialidad</a>)
                </div>
                <div class="field">
                    <label for="soportes">URL soportes del proyecto</label>
                    <input type="text" name="soportes" id="soportes" class="textfield tlarge" value="<? echo $p_vo->soportes;?>" size="15"
                           onkeypress=""/>
                </div>
            </div>
            <div class="left oth">
                <div>Cash Based Transfer</div>
                <div class="field">
                    <label for="cbt_ma">Modalidad de Asistencia</label>
                    <select id="cbt_ma" name="cbt_ma" class="select">
                        <option value=''>Seleccione alguno...</option>
                        <?
                        //MODALIDAD DE ASISTENCIA
                        $cbt_ma_dao->ListarCombo('combo',$p_vo->cbt_ma,'');
                        ?>
                    </select>
                </div>
                <div class="field">
                    <label for="cbt_me">Mecanismo de Entrega</label>
                    <select id="cbt_me" name="cbt_me" class="select">
                        <option value=''>Seleccione alguno...</option>
                        <?
                        //MECANISMO DE ENTREGA
                        $cbt_me_dao->ListarCombo('combo',$p_vo->cbt_me,'');
                        ?>
                    </select>
                </div>
                <div class="field">
                    <label for="cbt_f">Frecuencia de Distribución</label>
                    <input type="text" id="cbt_f" name="cbt_f" value="<?php echo $p_vo->cbt_f ?>" size="10" class="textfield" />
                </div>
                <div class="field">
                    <label for="cbt_val">Valor por Persona (USD)</label>
                    <input type="text" id="cbt_val" name="cbt_val" value="<?php echo $p_vo->cbt_val ?>" size="10" class="textfield" />
                    <br /><span class="nota">Valor enterno sin comas ni puntos</span>
                </div>
                <hr>
                <div class="imps">
                    <label for="id_orgs_s_0">Implementadores</label>
                    <?php 
                    $html_imp = '
                    <div class="imp %s %s">
                        <input type="hidden" id="id_orgs_s_%d" name="id_orgs_s[]" class="%s" value="%s" />
                        <span class="mm">
                        <a href="#" onclick="clone(\'imp\'); return false;"><img src="images/p4w/plus.gif" /></a>
                        <a href="#" onclick="removeClone(\'imp_%s\',%s); return false;"><img src="images/p4w/minus.gif" /></a>
                        </span>
                        <textarea id="nom_org_s_%s" name="nom_org_s[]" class="textfield tlarge" 
                        onkeydown="buscarOcurr(event, \'nom_org_s_%d\', \'id_orgs_s_%d\', \'ocurr_org_s_%d\');" />%s</textarea>
                        <div id="ocurr_org_s_%d" class="ocurrencia"></div>
                    </div>';
                    
                    $i = 100;
                    
                    // html a clonar
                    echo sprintf($html_imp,'','hide',$i,'','',$i,$i,$i,$i,$i,$i,'',$i);
                    
                    // primer implementador
                    $i = 0;
                    $id_s = '';
                    $nom = '';
                    if (!empty($p_vo->id_orgs_s[0])) {
                        $id_s = $p_vo->id_orgs_s[0];
                        $nom = $org_dao->GetName($p_vo->id_orgs_s[0]);
                    }

                    echo sprintf($html_imp,"imp_$i",'clear',$i,'ri',$id_s,$i,$i,$i,$i,$i,$i,$nom,$i);

                    $num = count($p_vo->id_orgs_s);
                    for ($i=1;$i<$num;$i++) {
                        $id_s = $p_vo->id_orgs_s[$i];
                        $nom = $org_dao->GetName($id_s);	
                        
                        echo sprintf($html_imp,"imp_$i",'clear',$i,'',$id_s,$i,$i,$i,$i,$i,$i,$nom,$i);
                    }
                    ?>
                </div>
                <hr>
                <div class="imps">
                    <label class="left">Donantes</label>
                    <!-- label class="right">C&oacute;digo proy.</label -->
                    <label class="right">Aporte U$</label>

                    <?php 
                    $html_don = '
                    <div class="don %s %s">
                        <input type="hidden" id="id_orgs_d_%d" name="id_orgs_d[]" value="%d" />
                        <span class="mm">
                        <a href="#" onclick="clone(\'don\'); return false;"><img src="images/p4w/plus.gif" /></a> 
                        <a href="#" onclick="removeClone(\'don_%s\',%s); return false;"><img src="images/p4w/minus.gif" /></a> 
                        </span>
                        <textarea type="text" id="nom_org_d_%d" name="nom_org_d[]" class="textfield nom_org_d" 
                        onkeydown="buscarOcurr(event, \'nom_org_d_%d\', \'id_orgs_d_%d\', \'ocurr_org_d_%d\');" />%s</textarea>
                        <textarea type="text" id="valor_org_d_%d" name="valor_org_d_%d" class="textfield extra_org_d" onkeypress="return validarNum(event)">%s</textarea>
                        <!-- textarea type="text" id="codigo_org_d_%d" name="codigo_org_d_%d" class="textfield extra_org_d">%s</textarea -->
                        <div id="ocurr_org_d_%d" class="ocurrencia"></div>
                    </div>';

                    $i = 100;

                    // html a clonar
                    echo sprintf($html_don,'','hide',$i,'',$i,$i,$i,$i,$i,$i,'',$i,$i,'',$i,$i,'',$i);
                    
                    // primer donante 
                    $i = 0;
                    $id_d = '';
                    $d_n = '';
                    $d_v = '';
                    $d_c = '';
                    if (!empty($p_vo->id_orgs_d[0])) {
                        $id_d = $p_vo->id_orgs_d[0];
                        $d_n = $org_dao->GetName($p_vo->id_orgs_d[0]);
                        $d_v = $p_vo->id_orgs_d_valor_ap[$p_vo->id_orgs_d[0]];
                        $d_c = $p_vo->id_orgs_d_codigo[$p_vo->id_orgs_d[0]];
                    }

                    echo sprintf($html_don,"don_$i",'clear',$i,$id_d,$i,$i,$i,$i,$i,$i,$d_n,$i,$i,$d_v,$i,$i,$d_c,$i);
                    ?>
                    <?
                    $num = count($p_vo->id_orgs_d);
                    for ($i=1;$i<$num;$i++) {
                        
                        $id_d = $p_vo->id_orgs_d[$i];
                        $nom = $org_dao->GetName($id_d);	
                        
                        $d_v = $p_vo->id_orgs_d_valor_ap[$id_d];
                        $d_c = $p_vo->id_orgs_d_codigo[$id_d];

                        echo sprintf($html_don,"don_$i",'clear',$i,$id_d,$i,$i,$i,$i,$i,$i,$nom,$i,$i,$d_v,$i,$i,$d_c,$i);
                    }
                    ?>
                </div>
            </div>
            <div class="clear"></div>
        </div>
        <!-- INFORMACION GENERAL : FIN -->	

        <div class="tabbertab" id="tema">
            <h2>SECTOR / RESULTADO / ACUERDOS</h2><br>
            <div class="left">
            <div class="t">SECTOR / RESULTADO</div>
                <?php 
                //switch($si_proy) {
                    //case '4w': 
                ?>
                <table>
                    <tr>
                        <td width="50%" valign="top">
                            <table class="table_cluster">
                                <tr>
                                    <td class="empty"></td>
                                    <td>Es tema principal?</td>
                                    <td>Presupuesto USD <br /><span class="nota">Presupuesto solo para el sector</span></td>
                                </tr>
                            <?php 
                                $id_c = 2;
                                $ts = $tema_dao->GetAllArray("id_clasificacion = $id_c AND id_papa=0");
                                foreach($ts as $_t => $t) {
                                    $check = (array_key_exists($t->id,$p_vo->id_temas)) ? ' checked ' : '';
                                    $check_p = ($t->id == $p_vo->id_tema_p) ? ' checked ' : '';
                                    $hijos = $tema_dao->GetAllArray("id_papa = $t->id");

                                    $tema_pres = (empty($p_vo->temas_presupuesto[$t->id])) ? '' : $p_vo->temas_presupuesto[$t->id];
                                    
                                    echo '<tr class="tema"><td width="350">
                                            <div class="left">
                                               <input type="checkbox" id="t_'.$t->id.'" name="id_temas[]" value="'.$t->id.'" '.$check.' class="tema_4w" />
                                               <label for="t_'.$t->id.'" class="ch" style="width:100px;">'.$t->nombre.'</label>
                                            </div>';
                                    
                                    if (!empty($t->def)) {
                                        echo '<div class="tdef">
                                                <a href="#" onclick="$j(\'div#tip_'.$t->id.'\').toggle(\'slow\');return false;"
                                                    >Defin. &#187;
                                                </a>
                                              </div>';
                                    
                                        echo '<div class="clear"></div>';
                                        
                                        echo '
                                            <div class="tip" id="tip_'.$t->id.'">
                                                <div class="clear">'.$t->def.'</div>
                                            </div>
                                        ';
                                    }
                                        
                                    echo '</td>';

                                    if (count($hijos) == 0) {
                                        echo '
                                            <td align="center">
                                                <input type="radio" id="" name="id_tema_p" value="'.$t->id.'" '.$check_p.' />
                                            </td>
                                        ';
                                    }
                                    else {
                                        echo '<td></td>';
                                    }
                                    
                                    if (count($hijos) == 0) {
                                        // Presupuesto por cluster
                                        echo '<td align="center"><input type="text" size="15" name="tema_presupuesto_'.$t->id.'" value="'.$tema_pres.'" class="textfield" /></td>';
                                    }
                                    else {
                                        echo '<td></td>';
                                    }

                                    echo '</tr>';
                                    foreach ($hijos as $th){

                                        $check = (isset($p_vo->id_temas[$t->id]['hijos']) && in_array($th->id,$p_vo->id_temas[$t->id]['hijos'])) ? ' checked ' : '';
                                        $check_p = ($th->id == $p_vo->id_tema_p) ? ' checked ' : '';
                                        $tema_pres = (empty($p_vo->temas_presupuesto[$th->id])) ? '' : $p_vo->temas_presupuesto[$th->id];
                                        
                                        echo '<tr class="tema"><td><div class="checkbox h">
                                           <input type="checkbox" id="t_'.$th->id.'" name="id_temas[]" value="'.$th->id.'" '.$check.' data-p="t_'.$t->id.'" class="chk_th tema_4w" />
                                           <label for="t_'.$th->id.'" class="ch">'.$th->nombre.'</label></td>';

                                        /*
                                        echo '
                                            <div class="right">
                                                <input type="radio" id="" name="id_tema_p" value="'.$th->id.'" '.$check_p.' />
                                            </div>
                                        ';*/
                                        
                                        if (!empty($th->def)) {
                                            echo '<div class="tdef">
                                                <a href="#" onclick="$j(\'div#tip_'.$th->id.'\').toggle(\'slow\');return false;"
                                                    >Defin &#187;
                                                </a>
                                              </div>';
                                        
                                            echo '
                                                <div class="tip" id="tip_'.$th->id.'">
                                                    <div class="clear">'.$th->def.'</div>
                                                </div>
                                            ';
                                        }

                                        echo '</div></td>';
                                        echo '<td></td>';

                                        // Presupuesto por cluster
                                        echo '<td align="center"><input type="text" size="15" name="tema_presupuesto_'.$th->id.'" value="'.$tema_pres.'" class="textfield" /></td>';
                                            
                                        echo '</tr>';
                                    }
                                    echo '</div>';
                                }
                            ?>
                            </table>
                        </td>
                        <td width="50%" valign="top">
                    <?php
                    //break;
                    //case 'undaf-des-paz': ?>
                            <?php 
                            $id_c = 4;
                            $tsp = $tema_dao->GetAllArray("id_clasificacion = $id_c AND id_papa=0");
                            foreach($tsp as $tp) {
                                echo '<br /><h1>'.$tp->nombre.'</h1><br />
                                <table class="table_cluster">';
                                $ts = $tema_dao->GetAllArray("id_papa=".$tp->id);
                                foreach($ts as $_t => $t) {
                                    $check = (array_key_exists($t->id,$p_vo->id_temas)) ? ' checked ' : '';

                                    echo '<tr class="tema">
                                            <td width="400">
                                               <input type="checkbox" id="t_'.$t->id.'" name="id_temas[]" value="'.$t->id.'" '.$check.' class="tema_des" />
                                               <label for="t_'.$t->id.'" class="ch">'.$t->nombre.'</label>
                                               </td>
                                               <td>'.$t->def.'</td>
                                            </tr>';
                                }

                                echo '</table><br />';
                            }
                            ?>
                    <?php
                    //break;
                //}
                ?>
                    </td>
                </tr>
                </table>
                <div class="t">ACUERDOS DE PAZ CON LAS FARC</div>
                <table>
                    <tr>
                        <td width="50%" valign="top">
	                        <?php
	                        $id_c = 5;
	                        $tsp = $tema_dao->GetAllArray("id_clasificacion = $id_c AND id_papa=0");
	                        foreach($tsp as $tp1) {
		                        echo '<br /><b>'.$tp1->nombre.'</b>';
		                        $tx = $tema_dao->GetAllArray("id_papa=" . $tp1->id);
                                foreach($tx as $tp)
                                {
	                                echo '<br /><b>' . $tp->nombre . '</b>';
	                                echo '<table class="table_cluster">';
	                                $ts = $tema_dao->GetAllArray("id_papa=" . $tp->id);
	                                foreach ($ts as $_t => $t)
	                                {
		                                $check = (array_key_exists($t->id, $p_vo->id_temas)) ? ' checked ' : '';

		                                echo '<tr class="tema">
                                                <td width="400">
                                                   <input type="checkbox" id="t_' . $t->id . '" name="id_temas[]" value="' . $t->id . '" ' . $check . ' class="tema_des" />
                                                   <label for="t_' . $t->id . '" class="ch">' . $t->nombre . '</label>
                                                   </td>
                                                   <td>' . $t->def . '</td>
                                                </tr>';
	                                }

	                                echo '</table>';
                                }
	                        }
	                        ?>
                        </td>
                    </tr>
                </table>
            </div>
            <div class="clear"></div>
            <?php 
            if ($p_vo->si_proy == 'undaf') {
            
                ?>
                <div class="undaf">
                    <div class="t">Indicar el AREA UNDAF</div>
                    <?
                    $id_c_undaf = 1;
                    $t_undaf = $tema_dao->GetAllArray("id_clasificacion = $id_c_undaf AND id_papa=0");

                    foreach($t_undaf as $t){

                        if (array_key_exists($t->id,$p_vo->id_temas)){
                            $check = " checked ";
                            $disabled = " ";
                            //$txt = $p_vo->texto_extra_tema[$t->id];
                            $display_hijos = 'block';
                        }
                        else{
                            $check = "";
                            $disabled = " disabled ";
                            //$txt =  "OUTCOME/s -- productos UNDAF"; 
                            $display_hijos = $display_nietos = 'none';
                        }

                        echo "<div class='left te'>
                                <div>
                                    <input type='checkbox' id='id_tema_$t->id' name='id_temas[]' value='$t->id' 
                                        $check onclick=\"if (this.checked == true){ listarTemasHijos($t->id,'');} 
                                        else {listarTemasHijos($t->id,'none');}\" />
                                        &nbsp;<b>$t->nombre</b>
                                </div>
                                <div id='hijo_".$t->id."' class='sub_tema' style='display:$display_hijos'>";
                                    
                                    $hijos = $tema_dao->GetAllArray("id_papa = $t->id");
                                    
                                    foreach ($hijos as $hijo){
                                        $checked_hijo[$hijo->id] = '';
                                        if ($accion == 'actualizar' && isset($p_vo->id_temas[$t->id]["hijos"]) && in_array($hijo->id,$p_vo->id_temas[$t->id]["hijos"])) {
                                            $checked_hijo[$hijo->id] = ' checked ';
                                            $display_nietos = 'block';
                                        }
                                         
                                        echo "<input type='checkbox' name='id_tema_$t->id[]' value=$hijo->id ".$checked_hijo[$hijo->id];
                                        echo " onclick=\"showTemaNieto(this.checked,$hijo->id)\">&nbsp;".$hijo->nombre."<br /><br />";

                                    }
                                
                                echo "</div>
                                <div id='nieto_".$t->id."' class='sub_tema' style='display:$display_nietos'>";

                                    foreach ($hijos as $hijo){
                                    
                                        $display_nieto = ($checked_hijo[$hijo->id] == '') ? 'none' : '';
                                        echo "<div id='p_nieto_papa_$hijo->id' style='display:$display_nieto'><p class='tit_tema_insert'>$hijo->nombre</p>";
                                    
                                        $nietos = $tema_dao->GetAllArray("id_papa = $hijo->id");
                                    
                                        foreach ($nietos as $nieto){
                                            echo "<input type='checkbox' name='id_tema_$t->id[]' value=$nieto->id";

                                            if ($accion == 'actualizar' && isset($p_vo->id_temas[$t->id]["nietos"]) && in_array($nieto->id,$p_vo->id_temas[$t->id]["nietos"]))	echo " checked ";

                                            echo ">&nbsp;".$nieto->nombre."<br /><br />";
                                        }
                                        echo '</div>';
                                    }
                                
                                echo "</div>
                            </div>
                        ";
                    }
                ?>
                </div>
            <?php 
            }
            ?>
            <div class="clear"></div>
        </div>

        <div class="tabbertab" id="benef">
        <h2>Beneficiarios</h2><br>
            <div id="div_benef" class="center">
                <div id="directos" class="div_benef_b">
                    <div class="t">POBLACIONALES</div>
                    <div id="total_benf_proy">
                        <label for="btotal" class="ch">Total de beneficiarios</label>
                        <input type="text" class="textfield" id="bdtotal" name="benf_proy[d][total]" value="<?php echo (!empty($p_vo->benf_proy['d']['total'])) ? $p_vo->benf_proy['d']['total'] : '' ?>" class="textfield" />
                    </div>
                    <div class="bmujer bdetalle">
                        <div class="t mujer">
                            <label for="bdm_cant" class="ch">MUJERES</label>
                            <input type="text" class="textfield" id="bdm_cant" name="benf_proy[d][m][total]" value="<?php echo (!empty($p_vo->benf_proy['d']['m']['total'])) ? $p_vo->benf_proy['d']['m']['total'] : '' ?>" />
                        </div>
                        <div class="checkbox">
                            <label for="bdm1_cant" class="ch">0-5 A&ntilde;os</label>
                            <input type="text" class="textfield" id="bdm1_cant" name="benf_proy[d][m][1]" value="<?php echo (!empty($p_vo->benf_proy['d']['m'][1])) ? $p_vo->benf_proy['d']['m'][1] : '' ?>" />
                        </div>
                        <div class="checkbox">
                            <label for="bdm2_cant" class="ch">6-17 A&ntilde;os</label>
                            <input type="text" class="textfield" id="bdm2_cant" name="benf_proy[d][m][2]" value="<?php echo (!empty($p_vo->benf_proy['d']['m'][2])) ? $p_vo->benf_proy['d']['m'][2] : '' ?>" />
                        </div>
                        <div class="checkbox">
                            <label for="bdm3_cant" class="ch">18-64 A&ntilde;os</label>
                            <input type="text" class="textfield" id="bdm3_cant" name="benf_proy[d][m][3]" value="<?php echo (!empty($p_vo->benf_proy['d']['m'][3])) ? $p_vo->benf_proy['d']['m'][3] : '' ?>" />
                        </div>
                        <div class="checkbox">
                            <label for="bdm4_cant" class="ch">65+ A&ntilde;os</label>
                            <input type="text" class="textfield" id="bdm4_cant" name="benf_proy[d][m][4]" value="<?php echo (!empty($p_vo->benf_proy['d']['m'][4])) ? $p_vo->benf_proy['d']['m'][4] : '' ?>" />
                        </div>
                    </div>
                    <div class="bhombre bdetalle">
                        <div class="t hombre">
                            <label for="bdh_cant" class="ch">HOMBRES</label>
                            <input type="text" class="textfield" id="bdh_cant" name="benf_proy[d][h][total]" value="<?php echo (!empty($p_vo->benf_proy['d']['h']['total'])) ? $p_vo->benf_proy['d']['h']['total'] : '' ?>" />
                        </div>
                        <div class="checkbox">
                            <label for="bdh1_cant" class="ch">0-5 A&ntilde;os</label>
                            <input type="text" class="textfield" id="bdh1_cant" name="benf_proy[d][h][1]" value="<?php echo (!empty($p_vo->benf_proy['d']['h'][1])) ? $p_vo->benf_proy['d']['h'][1] : '' ?>" />
                        </div>
                        <div class="checkbox">
                            <label for="bdh2_cant" class="ch">6-17 A&ntilde;os</label>
                            <input type="text" class="textfield" id="bdh2_cant" name="benf_proy[d][h][2]" value="<?php echo (!empty($p_vo->benf_proy['d']['h'][2])) ? $p_vo->benf_proy['d']['h'][2] : '' ?>" />
                        </div>
                        <div class="checkbox">
                            <label for="bdh3_cant" class="ch">18-64 A&ntilde;os</label>
                            <input type="text" class="textfield" id="bdh3_cant" name="benf_proy[d][h][3]" value="<?php echo (!empty($p_vo->benf_proy['d']['h'][3])) ? $p_vo->benf_proy['d']['h'][3] : '' ?>" />
                        </div>
                        <div class="checkbox">
                            <label for="bdh4_cant" class="ch">65+ A&ntilde;os</label>
                            <input type="text" class="textfield" id="bdh4_cant" name="benf_proy[d][h][4]" value="<?php echo (!empty($p_vo->benf_proy['d']['h'][4])) ? $p_vo->benf_proy['d']['h'][4] : '' ?>" />
                        </div>
                    </div>
                    <div class="checkbox">
                        Víctimas del conflicto:
                        <input type="text" class="textfield" id="num_vic" name="num_vic" value="<?php echo (!empty($p_vo->num_vic)) ? $p_vo->num_vic : '' ?>" />
                    </div>
                    <div class="checkbox">
                        Afectados por desastres:
                        <input type="text" class="textfield" id="num_afe" name="num_afe" value="<?php echo (!empty($p_vo->num_afe)) ? $p_vo->num_afe : '' ?>" />
                    </div>
                    <div class="checkbox">
                        Desmovilizados / Reinsertados:
                        <input type="text" class="textfield" id="num_des" name="num_des" value="<?php echo (!empty($p_vo->num_des)) ? $p_vo->num_des : '' ?>" />
                    </div>
                    <div class="checkbox">
                        Afro-colombianos:
                        <input type="text" class="textfield" id="num_afr" name="num_afr" value="<?php echo (!empty($p_vo->num_afr)) ? $p_vo->num_afr : '' ?>" />
                    </div>
                    <div class="checkbox">
                        Indígenas:
                        <input type="text" class="textfield" id="num_ind" name="num_ind" value="<?php echo (!empty($p_vo->num_ind)) ? $p_vo->num_ind : '' ?>" />
                    </div>
                    <br />
                    <div class="clear">
                        Comentarios: Descripci&oacute;n de g&eacute;nero &eacute;tnico, 
                        et&aacute;reo, situaci&oacute;n de desplazamiento, etc (Opcional)<br />
                        <textarea name="cant_benf_proy" class="textfield"><?=$p_vo->cant_benf_proy?></textarea>
                    </div>
                </div>
                <div id="indirectos" class="div_benef_b">
                    <div class="t">INDIRECTOS</div>
                    <div id="total_benf_proy">
                        <label for="btotal" class="ch">Total de beneficiarios</label>
                        <input type="text" class="textfield" id="bitotal" name="benf_proy[i][total]" value="<?php echo (!empty($p_vo->benf_proy['i']['total'])) ? $p_vo->benf_proy['i']['total'] : '' ?>" class="textfield" />
                    </div>
                    <div id="bmujer" class="bdetalle">
                        <div class="t mujer">
                            <label for="bim_cant" class="ch">MUJERES</label>
                            <input type="text" class="textfield" id="bim_cant" name="benf_proy[i][m][total]" value="<?php echo (!empty($p_vo->benf_proy['i']['m']['total'])) ? $p_vo->benf_proy['i']['m']['total'] : '' ?>" />
                        </div>
                        <div class="checkbox">
                            <label for="bim1_cant" class="ch">0-5 A&ntilde;os</label>
                            <input type="text" class="textfield" id="bim1_cant" name="benf_proy[i][m][1]" value="<?php echo (!empty($p_vo->benf_proy['i']['m'][1])) ? $p_vo->benf_proy['i']['m'][1] : '' ?>" />
                        </div>
                        <div class="checkbox">
                            <label for="bim2_cant" class="ch">6-17 A&ntilde;os</label>
                            <input type="text" class="textfield" id="bim2_cant" name="benf_proy[i][m][2]" value="<?php echo (!empty($p_vo->benf_proy['i']['m'][2])) ? $p_vo->benf_proy['i']['m'][2] : '' ?>" />
                        </div>
                        <div class="checkbox">
                            <label for="bim3_cant" class="ch">18-64 A&ntilde;os</label>
                            <input type="text" class="textfield" id="bim3_cant" name="benf_proy[i][m][3]" value="<?php echo (!empty($p_vo->benf_proy['i']['m'][3])) ? $p_vo->benf_proy['i']['m'][3] : '' ?>" />
                        </div>
                        <div class="checkbox">
                            <label for="bim4_cant" class="ch">65+ A&ntilde;os</label>
                            <input type="text" class="textfield" id="bim4_cant" name="benf_proy[i][m][4]" value="<?php echo (!empty($p_vo->benf_proy['i']['m'][4])) ? $p_vo->benf_proy['i']['m'][4] : '' ?>" />
                        </div>
                    </div>
                    <div id="bmujer" class="bdetalle">
                        <div class="t hombre">
                            <label for="bih_cant" class="ch">HOMBRES</label>
                            <input type="text" class="textfield" id="bih_cant" name="benf_proy[i][h][total]" value="<?php echo (!empty($p_vo->benf_proy['i']['h']['total'])) ? $p_vo->benf_proy['i']['h']['total'] : '' ?>" />
                        </div>
                        <div class="checkbox">
                            <label for="bih1_cant" class="ch">0-5 A&ntilde;os</label>
                            <input type="text" class="textfield" id="bih1_cant" name="benf_proy[i][h][1]" value="<?php echo (!empty($p_vo->benf_proy['i']['h'][1])) ? $p_vo->benf_proy['i']['h'][1] : '' ?>" />
                        </div>
                        <div class="checkbox">
                            <label for="bih2_cant" class="ch">6-17 A&ntilde;os</label>
                            <input type="text" class="textfield" id="bih2_cant" name="benf_proy[i][h][2]" value="<?php echo (!empty($p_vo->benf_proy['i']['h'][2])) ? $p_vo->benf_proy['i']['h'][2] : '' ?>" />
                        </div>
                        <div class="checkbox">
                            <label for="bih3_cant" class="ch">18-64 A&ntilde;os</label>
                            <input type="text" class="textfield" id="bih3_cant" name="benf_proy[i][h][3]" value="<?php echo (!empty($p_vo->benf_proy['i']['h'][3])) ? $p_vo->benf_proy['i']['h'][3] : '' ?>" />
                        </div>
                        <div class="checkbox">
                            <label for="bih4_cant" class="ch">65+ A&ntilde;os</label>
                            <input type="text" class="textfield" id="bih4_cant" name="benf_proy[i][h][4]" value="<?php echo (!empty($p_vo->benf_proy['i']['h'][4])) ? $p_vo->benf_proy['i']['h'][4] : '' ?>" />
                        </div>
                    </div>
                </div>
                <div id="nopoblacionales" class="div_benef_b">
                    <div class="t">NO POBLACIONALES</div>
                    <div class="bens">
                        <label for="id_orgs_b_0">Organizaciones Beneficiarias</label>
		                <?php
		                $html_ben = '
                    <div class="ben %s %s">
                        <input type="hidden" id="id_orgs_b_%d" name="id_orgs_b[]" class="%s" value="%s" />
                        <span class="mm">
                        <a href="#" onclick="clone(\'ben\'); return false;"><img src="images/p4w/plus.gif" /></a>
                        <a href="#" onclick="removeClone(\'ben_%s\',%s); return false;"><img src="images/p4w/minus.gif" /></a>
                        </span>
                        <textarea id="nom_org_b_%s" name="nom_org_b[]" class="textfield tlarge" 
                        onkeydown="buscarOcurr(event, \'nom_org_b_%d\', \'id_orgs_b_%d\', \'ocurr_org_b_%d\');" />%s</textarea>
                        <div id="ocurr_org_b_%d" class="ocurrencia"></div>
                    </div>';

		                $i = 100;

		                // html a clonar
		                echo sprintf($html_ben,'','hide',$i,'','',$i,$i,$i,$i,$i,$i,'',$i);

		                // primer beneficiario
		                $i = 0;
		                $id_b = '';
		                $nom = '';
		                if (!empty($p_vo->id_orgs_b[0])) {
			                $id_b = $p_vo->id_orgs_b[0];
			                $nom = $org_dao->GetName($p_vo->id_orgs_b[0]);
		                }

		                echo sprintf($html_ben,"ben_$i",'clear',$i,'ri',$id_b,$i,$i,$i,$i,$i,$i,$nom,$i);

		                $num = count($p_vo->id_orgs_b);
		                for ($i=1;$i<$num;$i++) {
			                $id_b = $p_vo->id_orgs_b[$i];
			                $nom = $org_dao->GetName($id_b);

			                echo sprintf($html_ben,"ben_$i",'clear',$i,'',$id_b,$i,$i,$i,$i,$i,$i,$nom,$i);
		                }
		                ?>
                    </div>
                </div>
            </div>
            <div class="clear"></div>
        </div>

        <div class="tabbertab" id="cobertura">
            <h2>Cobertura geogr&aacute;fica</h2><br>
            <div>
                <?php
                $cls = ($p_vo->cobertura_nal_proy == 1) ? 'hide' : '';
                ?>
                <div id="cnal" class="left">
                    Este proyecto es de cobertura nacional&nbsp;
                    <select id="cobertura_nal_proy" name="cobertura_nal_proy" class="select" onchange="$j('#cob_no_nac, #cob_opts, #cob_rules').toggle()">
                        <option value=0 <? if ($p_vo->cobertura_nal_proy == 0)	echo " selected " ?>>No</option>
                        <option value=1 <? if ($p_vo->cobertura_nal_proy == 1)	echo " selected " ?>>Si</option>
                    </select>
                </div>
                <div id="cob_opts" class="right">
                    <a href="#" class="boton icon mapa right">Mapa</a>&nbsp;
                    <a href="#" class="boton icon process right">Divipolas</a>&nbsp;
                    <a href="#" onclick="checkMpioLista(); return false;" class="boton icon lista right">Lista</a>&nbsp;
                </div>
                <div id="cob_rules" class="rules clear">Para seleccionar la cobertura tiene varias opciones: <br />
                    1. Seleccione los departamentos de la lista, frente a cada departamento se mostrar&aacute;
                    la opci&oacute;n Mpios &#187;, useala para seleccionar los municipos<br />
                    2. Copie y pegue los c&oacute;digos DIVIPOLA (separados por coma) en el campo procesar c&oacute;digos DIVIPOLA y proceselos<br />
                    o 3. Se puede divertir seleccionado los puntos exactos en el mapa
                </div>
                <!-- Emergencia frontera colombo-venezolana -->
                <div id="calbergues" class="hide">
                    <h1 id="h1_emergencia"></h1>
                    <br />Este proyecto es de albergues&nbsp;
                    <select id="cobertura_albergue" name="cobertura_albergue" class="select">
                        <option value=0 <? if ($p_vo->cobertura_nal_proy == 0)	echo " selected " ?>>No</option>
                        <option value=1 <? if ($p_vo->cobertura_nal_proy == 1)	echo " selected " ?>>Si</option>
                    </select>
                </div>

                <div id="albergues" class="hide">
                    <br />
                    <?php
                    $deptos_nom = array(54 => 'Norte de Santander', 
                                    81 => 'Arauca');

                    $albergues = array(54 =>  array(
                        array('id'=>'5401','mun'=>'54874','lon'=>'-72.47175','lat'=>'7.835638889','nom'=>'Bella Vista'),
                        array('id'=>'5431','mun'=>'54001','lon'=>'-72.499026','lat'=>'7.899863','nom'=>'Casa Hogar'),
                        array('id'=>'5402','mun'=>'54001','lon'=>'-72.50236111','lat'=>'7.900583333','nom'=>'Centro de Migraciones'),
                        array('id'=>'5403','mun'=>'54001','lon'=>'-72.48855556','lat'=>'7.901055556','nom'=>'Colegio INEM'),
                        array('id'=>'5423','mun'=>'54001','lon'=>'-72.485701','lat'=>'7.883826','nom'=>'Colegio Misael Pastrana'),
                        array('id'=>'5404','mun'=>'54874','lon'=>'-72.47491667','lat'=>'7.812361111','nom'=>'Colegio Morichal'),
                        array('id'=>'5424','mun'=>'54874','lon'=>'-72.485701','lat'=>'7.883826','nom'=>'Coliseo La Parada'),
                        array('id'=>'5405','mun'=>'54001','lon'=>'-72.49038889','lat'=>'7.900777778','nom'=>'Coliseo Municipal'),
                        array('id'=>'5406','mun'=>'54001','lon'=>'-72.492','lat'=>'7.897','nom'=>'Fundaci&oacute;n Jes&uacute;s y Mar&iacute;a'),
                        array('id'=>'5432','mun'=>'54001','lon'=>'-72.499026','lat'=>'7.899863','nom'=>'Hotel &Uacute;nico'),
                        array('id'=>'5413','mun'=>'54001','lon'=>'-72.50741667','lat'=>'7.894222222','nom'=>'Hotel &Uacute;nico Internacional I'),
                        array('id'=>'5414','mun'=>'54001','lon'=>'-72.50894444','lat'=>'7.894305556','nom'=>'Hotel &Uacute;nico Internacional II'),
                        array('id'=>'5430','mun'=>'54001','lon'=>'-72.485701','lat'=>'7.883826','nom'=>'Hotel Acora'),
                        array('id'=>'5407','mun'=>'54001','lon'=>'-72.504','lat'=>'7.887166667','nom'=>'Hotel Amaru'),
                        array('id'=>'5408','mun'=>'54001','lon'=>'-72.50377778','lat'=>'7.891277778','nom'=>'Hotel Caravana'),
                        array('id'=>'5409','mun'=>'54874','lon'=>'-72.45591667','lat'=>'7.820416667','nom'=>'Hotel Franny'),
                        array('id'=>'5410','mun'=>'54001','lon'=>'-72.49411111','lat'=>'7.929444444','nom'=>'Hotel Hollywood'),
                        array('id'=>'5411','mun'=>'54001','lon'=>'-72.50838889','lat'=>'7.893388889','nom'=>'Hotel Perla del Norte'),
                        array('id'=>'5429','mun'=>'54001','lon'=>'-72.485701','lat'=>'7.883826','nom'=>'Hotel Savac'),
                        array('id'=>'5412','mun'=>'54001','lon'=>'-72.49305556','lat'=>'7.921277778','nom'=>'Hotel Sibarca'),
                        array('id'=>'5415','mun'=>'54874','lon'=>'-72.45569444','lat'=>'7.820472222','nom'=>'Hotel Uni&oacute;n Junior'),
                        array('id'=>'5428','mun'=>'54001','lon'=>'-72.485701','lat'=>'7.883826','nom'=>'Hotel Vasconia'),
                        array('id'=>'5416','mun'=>'54874','lon'=>'-72.45416667','lat'=>'7.818666667','nom'=>'Iglesia Cuadrangular'),
                        array('id'=>'5422','mun'=>'54001','lon'=>'-72.48347222','lat'=>'7.924638889','nom'=>'Interferias'),
                        array('id'=>'5417','mun'=>'54874','lon'=>'-72.47272222','lat'=>'7.787222222','nom'=>'Juan Fr&iacute;o'),
                        array('id'=>'5434','mun'=>'54874','lon'=>'-72.499026','lat'=>'7.899863','nom'=>'La Parada'),
                        array('id'=>'5433','mun'=>'54874','lon'=>'-72.499026','lat'=>'7.899863','nom'=>'Paroquia San Pedro Ap&oacute;stol'),
                        array('id'=>'5418','mun'=>'54001','lon'=>'-72.508','lat'=>'7.894361111','nom'=>'Residencia La Estaci&oacute;n'),
                        array('id'=>'5419','mun'=>'54001','lon'=>'-72.45252778','lat'=>'7.968166667','nom'=>'Santa Cecilia / San Faustino'),
                        array('id'=>'5425','mun'=>'54874','lon'=>'-72.485701','lat'=>'7.883826','nom'=>'Senderos de Paz '),
                        array('id'=>'5420','mun'=>'54001','lon'=>'-72.4875','lat'=>'7.899166667','nom'=>'Universidad Francisco de Paula Santander'),
                        array('id'=>'5421','mun'=>'54874','lon'=>'-72.46327778','lat'=>'7.830916667','nom'=>'Villa Antigua'),
                        array('id'=>'5427','mun'=>'54001','lon'=>'-72.485701','lat'=>'7.883826','nom'=>'Villa Graciela')
                    ),
                   81 => array(
                       array('id'=>'8101','mun'=>'81065','lon'=>'-71.42847222','lat'=>'7.033972222','nom'=>'Asojuntas')));

                    // Lista por departamento
                    foreach($albergues as $id_depto => $albs) {
                        
                        echo '<div class="deptos left">
                            <h2>'.$deptos_nom[$id_depto].'</h2>';

                        foreach($albs as $alb) {
                            $check = (in_array($alb['id'],$p_vo->id_albergues)) ? " checked " : "";

                            $id = 'albergue_'.$alb['id'];

                            echo '<br /><div class="checkbox">
                                <div>
                                <input type="checkbox" id="'.$id.'" name="id_albergues[]" value="'.$alb['id'].'" '.$check.' class="albergue_chk"
                                data-depto="'.$id_depto.'" data-mun="'.$alb['mun'].'" data-lon="'.$alb['lon'].'" data-lat="'.$alb['lat'].'"
                                />
                                <label for="'.$id.'" class="ch">'.$alb['nom'].'</label>
                                </div>
                                </div>';
                        }

                        echo '</div>';
                    }
?>
                </div>
                <!-- /Emergencia frontera colombo-venezolana -->
                <div><p>&nbsp;</p></div>
                <div id="cob_no_nac" class="<?php echo $cls ?>">
                    <div id="clista" class="clear cob_opt lista">
                        <div class="left deptos">
                            <h2>Departamento</h2>
                            <div class="right">
                                <a href="#" onclick="tn(event, 'deptos'); chkc(); $j('div.mpios').hide(); return false;">Todos/Ninguno</a>&nbsp;
                            </div>
                            <div class="clear"></div> 
                            <?php 
                            foreach($depto_dao->GetAllArray('') as $depto) {
                                
                                $check = (in_array($depto->id,$p_vo->id_deptos)) ? " checked " : "";
                                
                                echo '<div class="checkbox" id="depto_'.$depto->id.'">
                                        <div class="left">
                                            <input type="checkbox" id="d_'.$depto->id.'" name="id_deptos[]" value="'.$depto->id.'" '.$check.' />
                                            <label for="d_'.$depto->id.'" class="ch">'.$depto->nombre.'</label>
                                        </div>
                                        <div class="right listar hide">
                                            <a href="#" id="a_'.$depto->id.'" 
                                           onclick="listarMpios(event, \''.$depto->id.'\', \'depto_'.$depto->id.'\', \'lista_mpios\'); return false;">
                                           Mpios &#187;</a>
                                        </div>
                                        <div class="clear"></div>
                                      </div>';
                            }
                            ?>
                        </div>
                        <div class="left hide mpios" id="mpios">
                            <h2>Municipios</h2>
                            <div class="left">
                                <input type="text" id="" class="buscar" onkeydown="filterMpios(event)" />
                            </div>
                            <div class="left">
                                <a href="#" onclick="tn(event, 'mpios'); return false;">Todos-Ninguno</a>
                            </div>
                            <div class="right" ><a href="" onclick="closeMpios(event); return false;"><img src="images/p4w/close.png" /></a></div>
                            <div class="clear separador"></div>
                            <div id="lista_mpios"></div>
                        </div>

                        <?php
                        $rows = 10;
                        $cols = 6;


                        foreach($p_vo->id_deptos as $id_depto) {
                            ?>
                            <div class="left hide mpios" id="mpio_<?php echo $id_depto ?>">
                                <h2>Municipios <?php echo $depto_dao->GetName($id_depto) ?> </h2>
                                <div class="left">
                                    <input type="text" id="" class="buscar" onkeydown="filterMpios(event)" />
                                </div>
                                <div class="left">
                                    <a href="#" onclick="tn(event, 'mpios'); return false;">Todos/Ninguno</a>
                                </div>
                                <div class="right" ><a href="" onclick="closeMpios(event); return false;"><img src="images/p4w/close.png" /></a></div>
                                <div class="clear separador"></div>
                                <div id="lista_mpios">
                                    <?php
                                    $muns = $mun_dao->GetAllArray("ID_DEPTO ='$id_depto'");
                                    $num = count($muns);

                                    if ($num < $rows*$cols) {
                                        $rows = ceil($num / $cols);
                                    } 

                                    if ($num > $rows*$cols) {
                                        $rows = ceil($num / $cols);
                                    }
                                                                        foreach ($muns as $m => $mun){
                                        
                                        $check = (in_array($mun->id,$p_vo->id_muns)) ? " checked " : "";
                                        
                                        if (fmod($m, $rows) == 0 || $m == 0) {
                                            echo '<div class="left">';
                                        } 

                                        echo '<div class="checkbox col" id="mpio_'.$mun->id.'">
                                                <input type="checkbox" value="'.$mun->id.'" id="mun_'.$mun->id.'" name="id_muns[]" 
                                                 onclick="$j(\'#mpio_'.$mun->id.'\').toggleClass(\'selected\')" '.$check.' />
                                                <label for="mun_'.$mun->id.'" class="ch">'.$mun->nombre.'</div>';

                                        if ((fmod($m+1, $rows) == 0) || ($m+1 == $num)) {
                                            echo '</div>';
                                        } 
                                    }
                                    ?>
                                </div>
                            </div>
                            <?php
                            }
                            ?>
                    </div>
                    <div id="cdivipolas" class="clear cob_opt">
                        <div>
                            <label for="divipolas">Procesar c&oacute;digos DIVIPOLA</label>
                            <textarea id="divipolas" class="textfield">08573,05021,23678,25407,15218,47555,52540,25873,15753,52585 </textarea>
                        </div>
                        <div>
                            <a href="#" onclick="getMpiosFromDivipola();return false;" class="boton icon insertar">Procesar</a>
                        </div>
                        <div id="pssd" class="pss clear"><img src="../images/p4w/loading.gif">&nbsp;Procesando....</div>
                        <div id="divipola_muns" class="clear lista_muns"></div>
                    </div>
                    <div id="cmapa" class="clear div_cob">
                        <div id="map" class="left"></div>
                        <div id="map_process" class="left">
                            <div>
                                <a href="#" onclick="toggle(['pp', 'div_waypoints']); return false;">Proceasar punto a punto</a> | <a href="#" onclick="toggle(['pp', 'div_waypoints']); return false;">Procesar Way Points</a>
                            </div>
                            <div id="pp">
                                <div class="rules">
                                    <h3>Procesar punto a punto</h3>
                                    Esta opci&oacute;n le permite agregar la cobertura del proyecto <br />
                                    usando el mapa, para ello, marque un punto en el mapa, los valores <br />
                                    de Latitud y Longitud aparecer&aacute;n y luego procese el punto.  <br />
                                    Deberá aparecer el correspondiente muncicipio.  Repita este procedimiento <br />
                                    para todos los lugares.
                                </div>
                                <div class="left">
                                    <label for="lon">Longitud</label><input type="text" id="lon" name="" value="" class="textfield" />
                                    <input type="hidden" id="lonl" name="" value="" />
                                </div>
                                <div class="left">
                                    <label for="lat">Latitud</label><input type="text" id="lat" name="" value="" class="textfield" />
                                    <input type="hidden" id="latl" name="" value="" />
                                </div>
                                <div class="left">
                                    <a href="#" onclick="getMpioFromPoint('point');return false;" class="boton icon insertar">Procesar</a>
                                </div>
                            </div>
                            <div id="div_waypoints">
                                <div class="rules">Copie y pegue el conjunto de waypoints separador por coma,<br /> luego proceselos con el bot&oacute;n Procesar</div>
                                <div class="left">
                                    <label for="waypoints">Procesar Waypoints</label>
                                    <textarea id="waypoints" class="textfield">
-74.5, 8.25
-74.55, 4.6166667
-70.7616667, 7.0902778
-72.303548143821, 1.9898034945991</textarea>
                                </div>
                                <div class="clear">
                                    <a href="#" onclick="getMpioFromPoint('waypoints');return false;" class="boton icon insertar">Procesar</a>
                                </div>
                            </div>
                            <div id="pssm" class="pss clear"><img src="../images/p4w/loading.gif">&nbsp;Procesando....</div>
                            <div id="map_muns" class="clear lista_muns"></div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="clear"></div>
        </div>
        <div class="clear ver" align="center">
            <input type="hidden" name="id" value="<? echo $p_vo->id_proy; ?>" />
            <input type="hidden" name="accion" value="<? echo $accion; ?>" />
            <input type="hidden" id="si_proy" name="si_proy" value="<?php echo $si_proy ?>" />
            <input type="hidden" id="sigd" name="sigd" value="" />
            <input type="submit" id="submit" name="submit" value="<?php echo $tsubmit ?>" class="boton" disabled />
        </div>
     </div>   
    <div id="resumen" class="ver">
        <div><h2>Campos Obligatorios</h2></div>
        <div class="grp"><ul id="uib"> <!-- Elementos se crean con la funcion JS resumen() --> </ul> </div>
        <div class="left grp"> 
            <ul>
                <li id="r_cl" onclick="acTab(2)">Sector / Resultado</li>
                <!--<li id="r_acc" onclick="acTab(1)">DGR</li>
                <li id="r_undaf" onclick="acTab(1)">UNDAF</li>-->
            </ul> 
        </div>
        <div class="left grp">&nbsp;<ul><li id="r_benef" onclick="acTab(2)">Beneficiarios Directos</li></ul></div>
        <div class="left grp">&nbsp; <ul><li id="r_cobertura" onclick="acTab(3)">Cobertura geografica</li></ul></div>
    </div>
 </div>   
</form>
