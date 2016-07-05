<?php

if (!isset($_GET['i'])) die;
else    $ind = $_GET['i'];

if (!isset($_GET['accion'])) die;
else    $accion = $_GET['accion'];

include('../lib/libs_unicef_producto_cpap.php');

$producto_dao = New UnicefProductoCpapDAO();
$producto_vo = New UnicefProductoCpap();
$indicador_dao = new UnicefIndicadorDAO();
$periodo_dao = new UnicefPeriodoDAO();

$periodo = $periodo_dao->GetAllArray('activo=1');
$aaaa_ini = $periodo[0]->aaaa_ini;
$aaaa_fin = $periodo[0]->aaaa_fin;

$ind_sql = $ind - 1;

$id_indicador = 0;
$l_b = '';
$meta = '';
if ($accion == 'actualizar'){
	$id = $_GET["id"];
    $producto_vo = $producto_dao->Get($id);
    
    $id_indicador = (isset($producto_vo->id_indicador[$ind_sql])) ? $producto_vo->id_indicador[$ind_sql] : 0;
    if (isset($producto_vo->linea_base[$ind_sql]))   $l_b = $producto_vo->linea_base[$ind_sql]; 
    if (isset($producto_vo->meta[$ind_sql]))   $meta = $producto_vo->meta[$ind_sql]; 
}
?>
<br />
<table cellpadding="0" cellspacing="0" border="0" align="center">
<tr><td><label>Indicador <?php echo $ind ?></td></tr>
<tr><td>
        <select id="id_indicador" name="id_indicador[]" class="textfield_ind" style="width:750px;font-size:11px;font-weight:normal">
            <option value="0"></option>';
                <?php
                $indicador_dao->ListarCombo('combo',$id_indicador,'producto_cpap=1');
                ?>

        </select>
    </td> 
</tr>
<tr><td>&nbsp;</td></tr>
<tr><td><label>Linea de Base</td></tr>
<tr><td><textarea id="linea_base" name="linea_base_<?=$ind_sql?>" style="width:750px;height:50px;" class="textfield_ind"><?=$l_b?></textarea></td></tr>
<tr><td>&nbsp;</td></tr>
<tr><td><label>Meta <?php echo $aaaa_ini.'-'.$aaaa_fin; ?></td></tr>
<tr><td><textarea id="meta" name="meta_<?php echo $ind_sql ?>" style="width:750px;height:50px" class="textfield_ind"><?=$meta?></textarea></td></tr>
<tr><td><table width="100%">
<?php
for ($a=$aaaa_ini;$a<=$aaaa_fin;$a++){

    $valor = isset($producto_vo->indicador_valor[$id_indicador][$a]) ? $producto_vo->indicador_valor[$id_indicador][$a] : '';
    $valor_meta = isset($producto_vo->meta_valor[$id_indicador][$a]) ? $producto_vo->meta_valor[$id_indicador][$a] : '';
    
    echo "<tr><td><label>Meta $a<br />";
    echo '<textarea name="meta_valor_'.$a.'_'.$ind_sql.'" style="width:350px;height:80px;" class="textfield_ind">'.$valor_meta.'</textarea>';
    
    echo "<td><label>Valor indicador observado $a<br />";
    echo '<textarea name="indicador_valor_'.$a.'_'.$ind_sql.'" style="width:350px;height:80px;" class="textfield_ind">'.$valor.'</textarea>';
}
?>
</table></td></tr>
</table>
    

