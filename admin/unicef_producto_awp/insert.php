<?
//INICIALIZACION DE VARIABLES
$producto_dao = New UnicefProductoAwpDAO();
$producto_vo = New UnicefProductoAwp();
$funcionario_dao = new UnicefFuncionarioDAO();
$fuente_dao = new UnicefFuentePbaDAO();
$actividad_dao = new UnicefActividadAwpDAO();
$depto_dao = new DeptoDAO();
$municipio_dao = new MunicipioDAO();
$socio_dao = new UnicefSocioDAO();
$donante_dao = new UnicefDonanteDAO();
$periodo_dao = new UnicefPeriodoDAO();
$presupuesto_desc_dao = new UnicefPresupuestoDescDAO();

$accion = '';
if (isset($_GET["accion"])){
	$accion = $_GET["accion"];
}
else if (isset($_POST["accion"])){
	$accion = $_POST["accion"];
}

// Se define en /admin/unicef_proyecto/get_nodes.php
$id_node_papa = $_SESSION['id_node_papa_click'];
$id_actividad = (isset($_GET["id_papa"])) ? $_GET["id_papa"] : 0;
$id_papa = $actividad_dao->GetFieldValue($id_actividad,'id_producto');
$id_depto = array();
$id_mun = array();
$display_depto = 'none';
$display_link_muns = 'none';
$deptos = $depto_dao->GetAllArray("id_depto <> '00'",'','');
$periodo = $periodo_dao->GetAllArray('activo=1');
$aaaa_ini = $periodo[0]->aaaa_ini;
$aaaa_fin = $periodo[0]->aaaa_fin;
$ascii_a = 97;

if ($accion == 'insertar'){
    $codigo_papa = $actividad_dao->getFieldValue($id_actividad,'codigo');
    $num_hermanos = $producto_dao->numRecords("id_actividad = $id_actividad AND aaaa = ".$_SESSION['aaaa']);
    $codigo = $codigo_papa."_".(chr($ascii_a + $num_hermanos));

}

//Caso de Actualizacion
else if ($accion == 'actualizar'){
	$id = $_GET["id"];
    $producto_vo = $producto_dao->Get($id);
    $codigo = $producto_vo->codigo;

    if (!in_array($producto_vo->cobertura,array('N','I','NA'))){
        
        $display_depto = '';
        $id_depto = $producto_vo->id_depto;

        if ($producto_vo->cobertura == 'M'){
            $id_mun = $producto_vo->id_mun;
            $display_link_muns = '';
        }
    }
}

$chk_indigena = ($producto_vo->indigena == 1) ? 'checked' : '';
$chk_afro = ($producto_vo->afro == 1) ? 'checked' : '';
$chk_equidad_genero = ($producto_vo->equidad_genero == 1) ? 'checked' : '';
$chk_participacion = ($producto_vo->participacion == 1) ? 'checked' : '';
$chk_movilizacion = ($producto_vo->movilizacion == 1) ? 'checked' : '';
$chk_prevencion = ($producto_vo->prevencion == 1) ? 'checked' : '';
$chk_cronograma_1_tri = ($producto_vo->cronograma_1_tri == 1) ? 'checked' : '';
$chk_cronograma_2_tri = ($producto_vo->cronograma_2_tri == 1) ? 'checked' : '';
$chk_cronograma_3_tri = ($producto_vo->cronograma_3_tri == 1) ? 'checked' : '';
$chk_cronograma_4_tri = ($producto_vo->cronograma_4_tri == 1) ? 'checked' : '';

$chk_funded = '';
$funded_td_display = 'none';
$unfunded_td_display = 'none';
if ($producto_vo->funded == 1){
    $chk_funded = 'checked';
    $funded_td_display = '';
}
$chk_unfunded = '';
if ($producto_vo->unfunded == 1){
    $chk_unfunded = 'checked';
    $unfunded_td_display = '';
}

?>
<form method="POST" onsubmit="submitForm(event,'<?=$id_node_papa?>');return false;">
	<table class="tabla_insertar">
        <?php
        if ($accion == 'actualizar'){
            ?>
            <tr><td align="right">
				<a class="boton_new" href='#' onclick="if(confirm('Est&aacute; seguro que desea borrar este Producto?')){borrarRegistro('UnicefProductoAwpDAO','<?=$id?>','<?=$id_node_papa?>')}else{return false};"><img src='images/unicef/delete.gif' border='0' title='Borrar' />&nbsp;Borrar este Producto AWP</a>&nbsp;
            </td></tr>
        <? } ?>
		<tr>
			<td><label>Actividad AWP a la que pertenece</label></td>
		</tr>
        <tr>
            <td>
                <select name="id_actividad" class="textfield" style="font-size:14px;width:800px;font-weight:normal;padding:0">
                    <?php $actividad_dao->ListarCombo('combo',$id_actividad,"id_producto=$id_papa"); ?>
                </select>
            </td>
        </tr>
        <tr><td>&nbsp;</td></tr>
		<tr>
			<td><label>A&ntilde;o</label></td>
		</tr>
        <tr>
            <td>
                <select name="aaaa" class="textfield" style="font-size:24px;font-weight:normal;padding:0">
                    <?php 
                    for ($a=$aaaa_ini;$a<=$aaaa_fin;$a++){
                        echo "<option value=$a ";
                        if ($a == $producto_vo->aaaa)    echo ' selected ';
                        echo ">$a</option>";
                    }
                    ?>
                </select>
            </td>
        </tr>
        <tr><td>&nbsp;</td></tr>
		<tr>
			<td><label>Producto AWP</label></td>
		</tr>
		<tr>
			<td><textarea id="nombre" name="nombre" style="width:800px;height:50px" class="textfield"><?=$producto_vo->nombre;?></textarea></td>
		</tr>
        <tr><td>&nbsp;</td></tr>
        <tr><td><label>C&oacute;digo de la Producto AWP</td></tr>
        <tr><td><input type="text" id="codigo" name="codigo" class="textfield" style="width:400px" value="<?=$codigo?>" /></td> </tr> 
        <tr><td>&nbsp;</td></tr>
        <tr>
            <td class="fila_amarilla">
                <table width="100%">
                    <tr><td><label>Cobertura Geogr&aacute;fica</td></tr>
                    <tr><td>
                            <select id="cobertura" name="cobertura" class="textfield" style="width:400px;font-size:24px;font-weight:normal" onchange="if(this.value == 'N' || this.value == '' || this.value == 'I' || this.value == 'NA'){document.getElementById('tr_depto').style.display='none';}else{document.getElementById('tr_depto').style.display='';} if (this.value == 'M'){document.getElementById('link_muns').style.display='';document.getElementById('td_muns').style.display='';}else{document.getElementById('link_muns').style.display='none';document.getElementById('td_muns').style.display='none';}">
                                <option></option> 
                                <option value="N" <?php if($producto_vo->cobertura == 'N')  echo ' selected ' ?>>Nacional</option>
                                <option value="D" <?php if($producto_vo->cobertura == 'D')  echo ' selected ' ?>>Departamental</option>
                                <option value="M" <?php if($producto_vo->cobertura == 'M')  echo ' selected ' ?>>Municipal</option>
                                <option value="I" <?php if($producto_vo->cobertura == 'I')  echo ' selected ' ?>>Interno</option>
                                <option value="NA" <?php if($producto_vo->cobertura == 'NA')  echo ' selected ' ?>>No aplica</option>
                            </select>
                        </td> 
                    </tr>
                    <tr id='tr_depto' style="display:<?php echo $display_depto ?>">
                        <td>
                            <table cellpadding="5" cellspacing="10">
                                <tr>
                                <td>
                                    <b>Departamento</b><br />
                                    <div style="overflow:auto;height:200px;width:200px;background:#ffffff;border:1px solid #cccccc;padding:5px">
                                    <a href="#" onclick="sel11Deptos();return false;">Seleccionar los 11</a><br />
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
                                    <b>Municipio</b>&nbsp;<a href='#' onclick="selectAllCheckboxObj(document.getElementsByName('id_mun[]'));return false">Todos</a><br />
                                    <div id="comboBoxMunicipio" style="overflow:auto;height:200px;width:200px;background:#ffffff;border:1px solid #cccccc;padding:5px">
                                        <?
                                        if ($accion == "actualizar" && $producto_vo->cobertura == 'M'){

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
        <tr><td><label>Socio Implementador</td></tr>
        <tr>
            <td>
                <p>
                <?php 
                for ($i=$ascii_a;$i<($ascii_a+26);$i++){
                    $letra = chr($i);
                    echo '<a href="#'.$letra.'">'.strtoupper($letra).'</a>&nbsp;&nbsp;';
                }
                ?>
                </p>
                <div class="div_multiple">
                    <table>
                        <?php
                        $socios = $socio_dao->GetAllArray('','','');
                        $inicial = '';
                        foreach($socios as $vo){
                            if (in_array($vo->id,$producto_vo->id_socio_implementador)){
                                $chk = ' checked ';
                                $class_name = 'tr_chk';
                            }
                            else{
                                $chk = '';
                                $class_name = '';

                            }
                            $tr_id = 'tr_socio_'.$vo->id;
                            $inicial_new = strtolower(substr($vo->nombre,0,1));
                            
                            echo '<tr id="'.$tr_id.'" class="'.$class_name.'"><td>';
                            if ($inicial != $inicial_new)   echo '<a name="'.$inicial_new.'">';
                            echo '<input type="checkbox" name="id_socio_implementador[]" value="'.$vo->id.'" '.$chk.' onclick="chkTR(this.checked,\''.$tr_id.'\')"></td><td>'.$vo->nombre.'</td>';

                            $inicial = $inicial_new;
                        }
                        ?>
                    </table>
                </div>
            </td> 
        </tr>
        <tr><td>&nbsp;</td></tr>
        <tr><td><label>Cronograma</td></tr>
        <tr>
            <td style="font-size:18px;font-weight:normal">
                <input type="checkbox" name="cronograma_1_tri" <?=$chk_cronograma_1_tri?>>&nbsp;1. T&nbsp;
                <input type="checkbox" name="cronograma_2_tri" <?=$chk_cronograma_2_tri?>>&nbsp;2. T&nbsp;
                <input type="checkbox" name="cronograma_3_tri" <?=$chk_cronograma_3_tri?>>&nbsp;3. T&nbsp;
                <input type="checkbox" name="cronograma_4_tri" <?=$chk_cronograma_4_tri?>>&nbsp;4. T&nbsp;
            </td>
        </tr>
        <tr><td>&nbsp;</td></tr>
		<tr>
			<td><label>Aliados</label></td>
		</tr>
		<tr>
			<td><textarea name="aliados" style="width:800px;height:50px" class="textfield"><?=$producto_vo->aliados;?></textarea></td>
		</tr>
        <tr><td>&nbsp;</td></tr>
        <tr><td><label>Funcionario UNICEF responsable</td></tr>
        <tr><td>
                <div class="div_multiple_mid" style="height:200px">
                    <table>
                        <?php
                        $funcionarios = $funcionario_dao->GetAllArray('','','');
                        foreach($funcionarios as $vo){
                            if (in_array($vo->id,$producto_vo->id_funcionario)){
                                $chk = ' checked ';
                                $class_name = 'tr_chk';
                            }
                            else{
                                $chk = '';
                                $class_name = '';

                            }
                            $tr_id = 'tr_funcionario_'.$vo->id;
                            
                            echo '<tr id="'.$tr_id.'" class="'.$class_name.'"><td style="width:30px"><input type="checkbox" name="id_funcionario[]" value="'.$vo->id.'" '.$chk.' onclick="chkTR(this.checked,\''.$tr_id.'\')"></td><td>'.$vo->nombre.'</td>';
                        }
                        ?>
                    </table>
                </div>
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
                                    <td width="50%"><label>Presupuesto TOTAL $COP</td>
                                    <td>
                                        <label>Presupuesto TOTAL U$</label>
                                        <input type="hidden" name="id_mon_ex" value="1">
                                        <!--
                                        <select name="id_mon_ex" class="textfield" style="font-size:14px;font-weight:normal;padding:0">
                                            <option value="1" <?php if($producto_vo->id_mon_ex == 1) echo 'selected'; ?>>U$</option>
                                            <option value="3" <?php if($producto_vo->id_mon_ex == 3) echo 'selected'; ?>>Euros</option>
                                        </select>
                                        -->
                                    </td>
                                </tr>
                                <tr>
                                    <td><input type="text" id="presupuesto_cop" name="presupuesto_cop" class="textfield" style="width:300px" value="<?=$producto_vo->presupuesto_cop?>" onkeypress="return validarNum(event)" /></td> 
                                    <td><input type="text" id="presupuesto_ex" name="presupuesto_ex" class="textfield" style="width:300px" value="<?=$producto_vo->presupuesto_ex?>" onkeypress="return validarNum(event)" /></td> 
                                </tr>
                            </table>
                        </td>
                    </tr>
                    <tr><td>&nbsp;</td></tr>
                    <tr>
                        <td>
                            <table width="100%">
                                <tr>
                                    <td valign="top" style="width:400px">
                                        <table>
                                            <tr>
                                                <td><label>Fuente de los fondos <input type="checkbox" name="funded" <?php echo $chk_funded ?> onclick="if (this.checked == true){document.getElementById('fuente_funded_td').style.display=''}else{document.getElementById('fuente_funded_td').style.display='none'}">&nbsp;FUNDED</td>
                                            </tr>
                                            <tr>
                                                <td id="fuente_funded_td" style="display:<?php echo $funded_td_display ?>">
                                                    <div class="div_multiple_mid">
                                                        <table>
                                                            <tr><td width="150" colspan="2">&nbsp;</td><td>Valor aporte</td></tr>
                                                            <?php
                                                            $fuentes = $fuente_dao->GetAllArray('','','');
                                                            foreach($fuentes as $vo){
                                                                if (in_array($vo->id,$producto_vo->id_fuente_funded)){
                                                                    $chk = ' checked ';
                                                                    $class_name = 'tr_chk';
                                                                }
                                                                else{
                                                                    $chk = '';
                                                                    $class_name = '';

                                                                }
                                                                $tr_id = 'tr_fuente_unfunded_'.$vo->id;
                                                                $valor = (isset($producto_vo->fuente_funded_valor[$vo->id]) && $producto_vo->fuente_funded_valor[$vo->id] > 0) ? $producto_vo->fuente_funded_valor[$vo->id] : '' ;
                                                                echo '<tr id="'.$tr_id.'" class="'.$class_name.'">
                                                                        <td width="15"><input type="checkbox" name="id_fuente_funded[]" value="'.$vo->id.'" '.$chk.' onclick="chkTR(this.checked,\''.$tr_id.'\')"></td><td>'.$vo->nombre.'</td>
                                                                        <td><input type="text" name="fuente_funded_valor_'.$vo->id.'" value="'.$valor.'" class="textfield_azul"></td>';
                                                            }
                                                            ?>
                                                        </table>
                                                    </div>
                                                </td>
                                            </tr>
                                        </table>
                                    </td>
                                    <td valign="top" style="width:400px">
                                        <table>
                                            <tr>
                                                <td><label>Fuente de los fondos <input type="checkbox" name="unfunded" <?php echo $chk_unfunded ?> onclick="if (this.checked == true){document.getElementById('fuente_unfunded_td').style.display='';}else{document.getElementById('fuente_unfunded_td').style.display='none'}">&nbsp;UNFUNDED</td>
                                                <!--<td><label>Donante</td>-->
                                            </tr>
                                            <tr>
                                                <td id="fuente_unfunded_td" style="display:<?php echo $unfunded_td_display ?>">
                                                    <div class="div_multiple_mid">
                                                        <table>
                                                            <tr><td width="150" colspan="2">&nbsp;</td><td>Valor aporte</td></tr>
                                                            <?php
                                                            $fuentes = $fuente_dao->GetAllArray('','','');
                                                            foreach($fuentes as $vo){
                                                                if (in_array($vo->id,$producto_vo->id_fuente_unfunded)){
                                                                    $chk = ' checked ';
                                                                    $class_name = 'tr_chk';
                                                                }
                                                                else{
                                                                    $chk = '';
                                                                    $class_name = '';

                                                                }
                                                                $tr_id = 'tr_fuente_funded_'.$vo->id;
                                                                $valor = (isset($producto_vo->fuente_unfunded_valor[$vo->id]) && $producto_vo->fuente_unfunded_valor[$vo->id] > 0) ? $producto_vo->fuente_unfunded_valor[$vo->id] : '' ;
                                                                echo '<tr id="'.$tr_id.'" class="'.$class_name.'">
                                                                        <td width="15"><input type="checkbox" name="id_fuente_unfunded[]" value="'.$vo->id.'" '.$chk.' onclick="chkTR(this.checked,\''.$tr_id.'\')"></td><td>'.$vo->nombre.'</td>
                                                                        <td><input type="text" name="fuente_unfunded_valor_'.$vo->id.'" value="'.$valor.'" class="textfield_azul"></td>';
                                                            }
                                                            ?>
                                                        </table>
                                                    </div>
                                                </td>
                                                <!--
                                                <td>
                                                    <div class="div_multiple">
                                                        <table>
                                                            <?php
                                                            $donantes = $donante_dao->GetAllArray('','','');
                                                            foreach($donantes as $vo){
                                                                if (in_array($vo->id,$producto_vo->id_donante)){
                                                                    $chk = ' checked ';
                                                                    $class_name = 'tr_chk';
                                                                }
                                                                else{
                                                                    $chk = '';
                                                                    $class_name = '';

                                                                }
                                                                $tr_id = 'tr_donante_'.$vo->id;
                                                                
                                                                echo '<tr id="'.$tr_id.'" class="'.$class_name.'"><td width="15"><input type="checkbox" name="id_donante[]" value="'.$vo->id.'" '.$chk.' onclick="chkTR(this.checked,\''.$tr_id.'\')"></td><td>'.$vo->nombre.'</td>';
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
                    <tr><td>&nbsp;</td></tr>
                    <tr><td><label>Descripci&oacute;n</label></td></tr>
                    <tr>
                        <td>
                            <div class="div_multiple_mid" style="height:100px">
                                <table>
                                    <?php
                                    $presupuesto_descs = $presupuesto_desc_dao->GetAllArray('','','');
                                    foreach($presupuesto_descs as $vo){
                                        if (in_array($vo->id,$producto_vo->id_presupuesto_desc)){
                                            $chk = ' checked ';
                                            $class_name = 'tr_chk';
                                        }
                                        else{
                                            $chk = '';
                                            $class_name = '';

                                        }
                                        $tr_id = 'tr_presupuesto_desc_'.$vo->id;
                                        
                                        echo '<tr id="'.$tr_id.'" class="'.$class_name.'"><td style="width:30px"><input type="checkbox" name="id_presupuesto_desc[]" value="'.$vo->id.'" '.$chk.' onclick="chkTR(this.checked,\''.$tr_id.'\')"></td><td>'.$vo->nombre.'</td>';
                                    }
                                    ?>
                                </table>
                            </div>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
        <tr><td>&nbsp;</td></tr>
        <tr><td><label>Temas transversales</td></tr>
        <tr>
            <td style="font-size:18px;font-weight:normal">
                <input type="checkbox" name="indigena" <?=$chk_indigena?>>&nbsp;Ind&iacute;gena&nbsp;
                <input type="checkbox" name="afro" <?=$chk_afro?>>&nbsp;Afros&nbsp;
                <input type="checkbox" name="equidad_genero" <?=$chk_equidad_genero?>>&nbsp;Equidad de g&eacute;nero&nbsp;
                <input type="checkbox" name="participacion" <?=$chk_participacion?>>&nbsp;Participaci&oacute;n&nbsp;
                <input type="checkbox" name="movilizacion" <?=$chk_movilizacion?>>&nbsp;Movilizaci&oacute;n&nbsp;
                <input type="checkbox" name="prevencion" <?=$chk_prevencion?>>&nbsp;Prevenci&oacute;n&nbsp;
            </td>
        </tr>
		<tr>
		  <td colspan="2" align='center'>
			  <br>
				<input type="hidden" name="accion" value="<?=$accion?>" />
			    <input type="hidden" name="id" value="<?=$producto_vo->id;?>" />
				<input type="submit" name="submit" value="Aceptar" class="boton" onclick="return validar_forma('nombre,Nombre,cobertura,Cobertura,presupuesto_ex,Presupuesto Modena Extranjera','');" />
			</td>
		</tr>
	</table>
</form>	
