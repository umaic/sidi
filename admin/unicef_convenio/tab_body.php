<?php

if (!isset($_GET['i'])) die;
else    $ind = $_GET['i'];

if (!isset($_GET['accion'])) die;
else    $accion = $_GET['accion'];

include('../lib/libs_unicef_convenio.php');

$convenio_dao = New UnicefConvenioDAO();
$convenio_vo = New UnicefConvenio();
$funcionario_dao = new UnicefFuncionarioDAO();
$fuente_dao = new UnicefFuentePbaDAO();
$fuentes = $fuente_dao->GetAllArray('','','');

$i = $ind - 1;
$id_convenio = 0;
$fecha = '';
$valor_cop = '';
$valor_ex = '';
$id_mon_ex = 0;

//Caso de Actualizacion
if ($accion == 'actualizar'){
	$id_convenio = $_GET["id"];
    $convenio_vo = $convenio_dao->Get($id_convenio);
    $id_mon_ex = (isset($convenio_vo->id_mon_ex_avances[$i])) ? $convenio_vo->id_mon_ex_avances[$i] : 0;
    $valor_cop = (isset($convenio_vo->avances_cop[$i])) ? $convenio_vo->avances_cop[$i] : '';
    $valor_ex = (isset($convenio_vo->avances_ex[$i])) ? $convenio_vo->avances_ex[$i] : '';
    //$id_f = (isset($convenio_vo->id_fuente_avances[$i])) ? $convenio_vo->id_fuente_avances[$i] : '';
    //$id_d = (isset($convenio_vo->id_donante_avances[$i])) ? $convenio_vo->id_donante_avances[$i] : '';
    $fecha = (isset($convenio_vo->avances_fecha[$i])) ? $convenio_vo->avances_fecha[$i] : '';
}

?>
<tr>
    <td><br />
        <table width="100%">
            <tr>
                <td>
                    <table width="100%">
                        <tr>
                            <td colspan="2">
                                <label>Fecha del avance <?php echo $ind; ?></label>&nbsp;
                                <input type="text" name="avances_fecha[]" class="textfield_ind" style="width:100px" value="<?=$fecha?>" />
                                &nbsp;(YYYY-MM-DD)&nbsp;* Si define un avance es obligatorio colocar la fecha
                            </td>
                        </tr>
                        <tr><td>&nbsp;</td></tr>
                        <tr>
                            <td width="50%"><label>Valor del Aporte No. <?php echo $ind; ?> $COP</td>
                            <td>
                                <label>Valor del Aporte No. <?php echo $ind; ?></label>
                                <select name="id_mon_ex_avances[]" class="textfield_ind" style="font-size:14px;font-weight:normal;padding:0">
                                    <option value="1" <?php if($id_mon_ex == 1) echo 'selected'; ?>>U$</option>
                                    <option value="3" <?php if($id_mon_ex == 3) echo 'selected'; ?>>Euros</option>
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <td><input type="text" name="avances_cop[]" class="textfield_ind" style="width:300px" value="<?php echo $valor_cop ?>" onkeypress="return validarNum(event)" /></td> 
                            <td><input type="text" name="avances_ex[]" class="textfield_ind" style="width:300px" value="<?php echo $valor_ex ?>" onkeypress="return validarNum(event)" /></td> 
                        </tr>
                    </table>
                </td>
            </tr>
            <tr><td>&nbsp;</td></tr>
            <tr>
                <td>
                    <table width="100%">
                        <tr>
                            <td width="50%"><label>Fuente de los fondos UNICEF PBA</td>
                            <!--<td><label>Donante</td>-->
                        </tr>
                        <tr>
                            <td>
                                <div class="div_multiple_mid">
                                    <table>
                                        <?php
                                        $id_fuente_avance = $convenio_dao->getFuenteAvance($i,$id_convenio);
                                        foreach($fuentes as $vo){
                                            if (in_array($vo->id,$id_fuente_avance)){
                                                $chk = ' checked ';
                                                $class_name = 'tr_chk';
                                            }
                                            else{
                                                $chk = '';
                                                $class_name = '';

                                            }
                                            $tr_id = 'tr_fuente_avance_'.$i.'_'.$vo->id;
                                            
                                            echo '<tr id="'.$tr_id.'" class="'.$class_name.'"><td width="15"><input type="checkbox" name="id_fuente_avances_'.$i.'[]" value="'.$vo->id.'" '.$chk.' onclick="chkTR(this.checked,\''.$tr_id.'\')"></td><td>'.$vo->nombre.'</td>';
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
                                        $id_donante_avance = $convenio_dao->getDonanteAvance($i,$id_convenio);
                                        foreach($donantes as $vo){
                                            if (in_array($vo->id,$id_donante_avance)){
                                                $chk = ' checked ';
                                                $class_name = 'tr_chk';
                                            }
                                            else{
                                                $chk = '';
                                                $class_name = '';

                                            }
                                            $tr_id = 'tr_donante_avance_'.$i.'_'.$vo->id;
                                            
                                            echo '<tr id="'.$tr_id.'" class="'.$class_name.'"><td width="15"><input type="checkbox" name="id_donante_avances_'.$i.'[]" value="'.$vo->id.'" '.$chk.' onclick="chkTR(this.checked,\''.$tr_id.'\')"></td><td>'.$vo->nombre.'</td>';
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
