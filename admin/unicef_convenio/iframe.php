<?php 
session_start();

if (!isset($_GET['accion'])) die;
else    $accion = $_GET['accion'];

// Librerias
include('../lib/libs_unicef_convenio.php');
$accion = '';

if (isset($_GET["accion"])){
	$accion = $_GET["accion"];
}
else if (isset($_POST["accion"])){
	$accion = $_POST["accion"];
}

//INICIALIZACION DE VARIABLES
$convenio_dao = New UnicefConvenioDAO();
$convenio_vo = New UnicefConvenio();
$funcionario_dao = new UnicefFuncionarioDAO();
$fuente_dao = new UnicefFuentePbaDAO();
$estado_dao = new UnicefEstadoDAO();
$socio_dao = new UnicefSocioDAO();
$donante_dao = new UnicefDonanteDAO();
$actividad_dao = new UnicefActividadAwpDAO();
$depto_dao = new DeptoDAO();
$municipio_dao = new MunicipioDAO();


// Se define en /admin/unicef_proyecto/get_nodes.php
$id_node_papa = $_SESSION['id_node_papa_click'];
$id_actividad = (isset($_GET["id_papa"])) ? $_GET["id_papa"] : 0;
//$id_papa = $actividad_dao->GetFieldValue($id_actividad,'id_actividad');
$id_papa = $id_actividad;
$presupuesto_cop = '';
$presupuesto_ex = '';
//$donantes = $donante_dao->GetAllArray('','','');
$id_depto = array();
$id_mun = array();
$display_depto = 'none';
$display_link_muns = 'none';
$deptos = $depto_dao->GetAllArray('','','');
$fuentes = $fuente_dao->GetAllArray('','','');
$num_avances = 1;
$id = '';

//Caso de Actualizacion
if ($accion == 'actualizar'){
	$id = $_GET["id"];
    $convenio_vo = $convenio_dao->Get($id);
    $codigo = $convenio_vo->codigo;
    
    if ($convenio_vo->cobertura != 'N'){
        
        $display_depto = '';
        $id_depto = $convenio_vo->id_depto;

        if ($convenio_vo->cobertura == 'M'){
            $id_mun = $convenio_vo->id_mun;
            $display_link_muns = '';
        }
    }
    
    if ($convenio_vo->numero_avances > 0)
        $num_avances = $convenio_vo->numero_avances;
}
?>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
<head>
<link href='/sissh/style/consulta_unicef.css' rel='stylesheet' type='text/css' />
<link href='../style/tree-ext3.css' rel='stylesheet' type='text/css' />
<link href='../style/ext-all.css' rel='stylesheet' type='text/css' />
<script type="text/javascript" src="../js/general.js"></script>
<script type="text/javascript" src="../js/ext_3/tree-adv.js"></script>
<script type="text/javascript" src="../js/ext_3/ext-base.js"></script>
<script type="text/javascript" src="../js/ext_3/ext-all.js"></script>
<script type="text/javascript" src="../js/ext_3/ext-lang-es.js"></script>
<style type="text/css">
body{
    background-color: #dfe8f6;
    font-size: 12px;
    text-align: left;
    margin: 0px;
}
.textfield_ind{
    background-color: #dfe8f6;
    border: 0;
    padding: 3px;
}
.new-tab{
    background-image: url(../../images/unicef/tabs.gif) !important;
}
}
</style>

<script type="text/javascript">

Ext.onReady(function(){

    var tabs = new Ext.TabPanel({
        renderTo:'tabs_cpd',
        resizeTabs:true, // turn on tab resizing
        minTabWidth: 115,
        tabWidth:135,
        enableTabScroll:true,
        width:800,
        height:520,
        defaults: {autoScroll:true},
        plain: true,
        activeTab: 0
    });

    // tab generation code
    var index = 0;
    while(index < <?php echo $num_avances ?>){
        addTab();
    }
    function addTab(){
        tabs.add({
            title: 'Avance ' + (++index),
            iconCls: 'tabs',
            autoLoad: 'tab_body.php?i='+index+'&accion=<?=$accion?>&id=<?=$id?>',
            closable:true
        }).show();
    }

    new Ext.Button({
        text: 'Agregar Avance',
        handler: addTab,
        iconCls:'new-tab'
    }).render(document.body, 'tabs_cpd');
});

</script>

</head>

<body>
<br />
<form method="POST" onsubmit="submitFormIframe(event,'<?=$id_node_papa?>');return false;">
	<table class="tabla_insertar">
        <?php
        if ($accion == 'actualizar'){
            ?>
            <tr><td align="right">
				<a class="boton_new" href='#' onclick="if(confirm('Est&aacute; seguro que desea borrar este Convenio?')){borrarRegistroIframe('UnicefConvenioDAO','<?=$id?>','<?=$id_node_papa?>')}else{return false};"><img src='/sissh/images/unicef/delete.gif' border='0' title='Borrar' />&nbsp;Borrar este Convenio</a>&nbsp;
            </td></tr>
        <? } ?>
		<tr>
			<td><label>Actividad AWP a la que pertenece</label></td>
		</tr>
        <tr>
            <td>
                <select name="id_actividad" class="textfield" style="font-size:14px;width:800px;font-weight:normal;padding:0">
                    <?php $actividad_dao->ListarCombo('combo',$id_actividad,"id_actividad=$id_papa"); ?>
                </select>
            </td>
        </tr>
        <tr><td>&nbsp;</td></tr>
		<tr>
			<td><label>C&oacute;digo del Convenio</label></td>
		</tr>
        <tr><td><input type="text" id="codigo" name="codigo" class="textfield" style="width:400px" value="<?=$convenio_vo->codigo?>" />(C&oacute;digo alfa-n&uacute;merico)</td> </tr> 
        <tr><td>&nbsp;</td></tr>
        <tr><td><label>Objetivo del Convenio</td></tr>
		<tr>
			<td><textarea id="nombre" name="nombre" style="width:800px;height:90px" class="textfield"><?=$convenio_vo->nombre;?></textarea></td>
		</tr>
        <tr><td>&nbsp;</td></tr>
        <tr><td><label>Estado del Convenio</td></tr>
        <tr><td>
                <select id="id_estado" name="id_estado" class="textfield" style="width:400px;font-size:24px;font-weight:normal">
                    <option></option> 
                    <?php $estado_dao->ListarCombo('combo',$convenio_vo->id_estado,''); ?> 
                </select>
            </td> 
        </tr>
        <tr><td>&nbsp;</td></tr>
        <tr>
            <td class="fila_amarilla">
                <table width="100%">
                    <tr><td><label>Cobertura Geogr&aacute;fica</td></tr>
                    <tr><td>
                            <select id="cobertura" name="cobertura" class="textfield" style="width:400px;font-size:24px;font-weight:normal" onchange="if(this.value=='N' || this.value == ''){document.getElementById('tr_depto').style.display='none';}else{document.getElementById('tr_depto').style.display='';} if (this.value == 'M'){document.getElementById('link_muns').style.display='';document.getElementById('td_muns').style.display='';}else{document.getElementById('link_muns').style.display='none';document.getElementById('td_muns').style.display='none';}">
                                <option></option> 
                                <option value="N" <?php if($convenio_vo->cobertura == 'N')  echo ' selected ' ?>>Nacional</option>
                                <option value="D" <?php if($convenio_vo->cobertura == 'D')  echo ' selected ' ?>>Departamental</option>
                                <option value="M" <?php if($convenio_vo->cobertura == 'M')  echo ' selected ' ?>>Municipal</option>
                            </select>
                        </td> 
                    </tr>
                    <tr id='tr_depto' style="display:<?php echo $display_depto ?>">
                        <td>
                            <table cellpadding="5" cellspacing="10">
                                <tr>
                                <td>
                                    <b>Departamento</b><br>
                                    <div style="overflow:auto;height:200px;width:200px;background:#ffffff;border:1px solid #cccccc;padding:5px">
                                    <?
                                    //DEPTO
                                    foreach ($deptos as $depto){
                                        $tr_id = 'tr_depto_'.$depto->id;
                                        $chk = (in_array($depto->id,$id_depto)) ? ' checked ' : '';
                                        echo "<input type='checkbox' id='$tr_id' name='id_depto[]' value='$depto->id' $chk onclick=\"chkTR(this.checked,'$tr_id')\">&nbsp;$depto->nombre<br />";
                                    }
                                    ?>
                                    </div>
                                    <br />
                                    <span id="link_muns" style="display:<?=$display_link_muns?>">
                                    &raquo;&nbsp;<a href="#" onclick="listarMunicipiosCheckbox('id_depto[]',1,0,'id_mun');return false;">Listar Muncipios</a>
                                    </span>
                                </td>
                                <td valign="top" id="td_muns" style="display:<?=$display_link_muns?>">
                                    <b>Municipio</b><br />
                                    <div id="comboBoxMunicipio" style="overflow:auto;height:200px;width:200px;background:#ffffff;border:1px solid #cccccc;padding:5px">
                                        <?
                                        if ($accion == "actualizar" && $convenio_vo->cobertura == 'M'){

                                            foreach ($id_depto as $id_d){
                                                $depto = $depto_dao->Get($id_d);
                                                //echo $id_depto;
                                                $muns = $municipio_dao->GetAllArray("ID_DEPTO ='$id_d'");

                                                echo "-------- <b>".strtoupper($depto->nombre)."</b> --------<br />";
                                                
                                                foreach ($muns as $mun){
                                                    $chk = (in_array($mun->id,$id_mun)) ? ' checked ' : '';
                                                    echo "<input type='checkbox' id='$tr_id' name='id_mun[]' value='$mun->id' $chk>&nbsp;$mun->nombre<br />";
                                                }

                                                echo '<br />';
                                            }
                                        }
                                        ?>
                                </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
        <tr><td>&nbsp;</td></tr>
        <tr><td><label>Funcionario UNICEF responsable</td></tr>
        <tr><td>
                <select id="id_funcionario" name="id_funcionario" class="textfield" style="width:400px;font-size:24px;font-weight:normal">
                    <option></option>
                    <?php 
                    $funs = $funcionario_dao->GetAllArray('','','');
                    foreach ($funs as $fun){
                        echo "<option value='$fun->id'";
                        if ($convenio_vo->id_funcionario == $fun->id)   echo " selected ";
                        echo ">$fun->apellido $fun->nombre</option>";
                    }
                    ?>
                </select>
            </td> 
        </tr>
        <tr><td>&nbsp;</td></tr>
        <tr><td><label>Socio Implementador</td></tr>
        <tr>
            <td>
                <div class="div_multiple">
                    <table>
                        <?php
                        $socios = $socio_dao->GetAllArray('','','');
                        foreach($socios as $vo){
                            if (in_array($vo->id,$convenio_vo->id_socio_implementador)){
                                $chk = ' checked ';
                                $class_name = 'tr_chk';
                            }
                            else{
                                $chk = '';
                                $class_name = '';

                            }
                            $tr_id = 'tr_socio_'.$vo->id;
                            
                            echo '<tr id="'.$tr_id.'" class="'.$class_name.'"><td><input type="checkbox" name="id_socio_implementador[]" value="'.$vo->id.'" '.$chk.' onclick="chkTR(this.checked,\''.$tr_id.'\')"></td><td>'.$vo->nombre.'</td>';
                        }
                        ?>
                    </table>
                </div>
            </td> 
        </tr>
        <tr><td>&nbsp;</td></tr>
		<tr>
			<td><label>Aliados</label></td>
		</tr>
		<tr>
			<td><textarea name="aliados" style="width:800px;height:50px" class="textfield"><?=$convenio_vo->aliados;?></textarea></td>
		</tr>
        <tr><td>&nbsp;</td></tr>
        <tr>
            <td class="fila_verde">
                <table width="100%">
                    <tr>
                        <td>
                            <table width="100%">
                                <tr>
                                    <td width="50%"><label>Fecha de inicio del Convenio</td>
                                    <td width="50%"><label>Fecha de finalizaci&oacute;n del Convenio</td>
                                </tr>
                                <tr>
                                    <td>
                                        <input type="text" id="fecha_ini" name="fecha_ini" class="ExtDateField" style="width:100px" value="<?=$convenio_vo->fecha_ini?>" />&nbsp;(YYYY-MM-DD)
                                    </td>
                                    <td>
                                        <input type="text" id="fecha_fin" name="fecha_fin" class="ExtDateField" style="width:100px" value="<?=$convenio_vo->fecha_fin?>" />&nbsp;(YYYY-MM-DD)
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
        <tr><td>&nbsp;</td></tr>
        <tr>
            <td class="fila_roja">
                <table width="100%">
                    <tr>
                        <td>
                            <table width="100%">
                                <tr>
                                    <td width="50%"><label>Valor del Convenio $COP</td>
                                    <td>
                                        <label>Valor del Convenio </label>
                                        <select name="id_mon_ex" class="textfield" style="font-size:14px;font-weight:normal;padding:0">
                                            <option value="1" <?php if($convenio_vo->id_mon_ex == 1) echo 'selected'; ?>>U$</option>
                                            <option value="3" <?php if($convenio_vo->id_mon_ex == 3) echo 'selected'; ?>>Euros</option>
                                        </select>
                                    </td>
                                </tr>
                                <tr>
                                    <td><input type="text" name="presupuesto_cop" class="textfield" style="width:300px" value="<?php echo $convenio_vo->presupuesto_cop ?>" onkeypress="return validarNum(event)" /></td> 
                                    <td><input type="text" name="presupuesto_ex" class="textfield" style="width:300px" value="<?php echo $convenio_vo->presupuesto_ex ?>" onkeypress="return validarNum(event)" /></td> 
                                </tr>
                            </table>
                        </td>
                    </tr>
                    <tr><td>&nbsp;</td></tr>
                    <tr>
                        <td>
                            <table width="100%">
                                <tr>
                                    <td width="50%"><label>Aporte UNICEF $COP</td>
                                    <td>
                                        <label>Aporte UNICEF </label>
                                        <select name="id_mon_ex_aporte_unicef" class="textfield" style="font-size:14px;font-weight:normal;padding:0">
                                            <option value="1" <?php if($convenio_vo->id_mon_ex_aporte_unicef == 1) echo 'selected'; ?>>U$</option>
                                            <option value="3" <?php if($convenio_vo->id_mon_ex_aporte_unicef == 3) echo 'selected'; ?>>Euros</option>
                                        </select>
                                    </td>
                                </tr>
                                <tr>
                                    <td><input type="text" name="aporte_unicef_cop" class="textfield" style="width:300px" value="<?php echo $convenio_vo->aporte_unicef_cop ?>" onkeypress="return validarNum(event)" /></td> 
                                    <td><input type="text" name="aporte_unicef_ex" class="textfield" style="width:300px" value="<?php echo $convenio_vo->aporte_unicef_ex ?>" onkeypress="return validarNum(event)" /></td> 
                                </tr>
                            </table>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
        <tr><td>&nbsp;</td></tr>
        <tr><td><label>Avances programados para el Convenio</td></tr>
        <tr><td>&nbsp;</td></tr>
        <tr><td><div id="tabs_cpd"></div></td></tr>
        <tr><td>&nbsp;</td></tr>
        <tr>
            <td class="fila_amarilla">
                <table width="100%">
                    <tr>
                        <td>
                            <table width="100%">
                                <tr>
                                    <td width="50%"><label>Otros fondos ejecutados $COP</td>
                                    <td>
                                        <label>Otros fondos ejecutados </label>
                                        <select name="id_mon_ex_otros_fondos" class="textfield" style="font-size:14px;font-weight:normal;padding:0">
                                            <option value="1" <?php if($convenio_vo->id_mon_ex_otros_fondos == 1) echo 'selected'; ?>>U$</option>
                                            <option value="3" <?php if($convenio_vo->id_mon_ex_otros_fondos == 3) echo 'selected'; ?>>Euros</option>
                                        </select>
                                    </td>
                                </tr>
                                <tr>
                                    <td><input type="text" name="otros_fondos_cop" class="textfield" style="width:300px" value="<?php echo $convenio_vo->otros_fondos_cop ?>" onkeypress="return validarNum(event)" /></td> 
                                    <td><input type="text" name="otros_fondos_ex" class="textfield" style="width:300px" value="<?php echo $convenio_vo->otros_fondos_ex ?>" onkeypress="return validarNum(event)" /></td> 
                                </tr>
                            </table>
                        </td>
                    </tr>
                    <tr><td>&nbsp;</td></tr>
                    <tr>
                        <td>
                            <table width="100%">
                                <tr>
                                    <td><label>Fuente de los fondos UNICEF PBA</td>
                                    <!--<td><label>Donante</td>-->
                                </tr>
                                <tr>
                                    <td>
                                        <div class="div_multiple_mid">
                                            <table>
                                                <?php
                                                foreach($fuentes as $vo){
                                                    if (in_array($vo->id,$convenio_vo->id_fuente_otros_fondos)){
                                                        $chk = ' checked ';
                                                        $class_name = 'tr_chk';
                                                    }
                                                    else{
                                                        $chk = '';
                                                        $class_name = '';

                                                    }
                                                    $tr_id = 'tr_fuente_otros_fondos'.$vo->id;
                                                    
                                                    echo '<tr id="'.$tr_id.'" class="'.$class_name.'"><td width="15"><input type="checkbox" name="id_fuente_otros_fondos[]" value="'.$vo->id.'" '.$chk.' onclick="chkTR(this.checked,\''.$tr_id.'\')"></td><td>'.$vo->nombre.'</td>';
                                                }
                                                ?>
                                            </table>
                                        </div>
                                    </td>
                                    <!--
                                    <td>
                                        <div class="div_multiple_mid">
                                            <table>
                                                <?php
                                                foreach($donantes as $vo){
                                                    if (in_array($vo->id,$convenio_vo->id_donante_otros_fondos)){
                                                        $chk = ' checked ';
                                                        $class_name = 'tr_chk';
                                                    }
                                                    else{
                                                        $chk = '';
                                                        $class_name = '';

                                                    }
                                                    $tr_id = 'tr_donante_otros_fondos_'.$vo->id;
                                                    
                                                    echo '<tr id="'.$tr_id.'" class="'.$class_name.'"><td width="15"><input type="checkbox" name="id_donante_otros_fondos[]" value="'.$vo->id.'" '.$chk.' onclick="chkTR(this.checked,\''.$tr_id.'\')"></td><td>'.$vo->nombre.'</td>';
                                                }
                                                ?>
                                            </table>
                                        </div>
                                    </td> 
                                    -->
                                </tr>
                            </table>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
		<tr>
		  <td colspan="2" align='center'>
			  <br>
				<input type="hidden" name="accion" value="<?=$accion?>" />
			    <input type="hidden" name="id" value="<?=$convenio_vo->id;?>" />
			    <input type="hidden" name="id_actividad" value="<?=$id_actividad;?>" />
				<input type="submit" name="submit" value="Aceptar" class="boton" onclick="return validar_forma('nombre,Nombre,fecha_ini,Fecha de inicio,fecha_fin,Fecha de finalizacion','');" />
			</td>
		</tr>
	</table>
</form>	
