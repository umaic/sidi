<?php

if (!isset($_GET['i'])) die;
else    $ind = $_GET['i'];

if (!isset($_GET['accion'])) die;
else    $accion = $_GET['accion'];

include('../lib/libs_unicef_resultado.php');

$resultado_dao = New UnicefResultadoDAO();
$resultado_vo = New UnicefResultado();
$indicador_dao = new UnicefIndicadorDAO();
$periodo_dao = new UnicefPeriodoDAO();

$periodo = $periodo_dao->GetAllArray('activo=1');
$aaaa_ini = $periodo[0]->aaaa_ini;
$aaaa_fin = $periodo[0]->aaaa_fin;

$ind_sql = $ind - 1;

$id_indicador = 0;
$l_b = '';
if ($accion == 'actualizar'){
	$id = $_GET["id"];
    $resultado_vo = $resultado_dao->Get($id);

    $id_indicador = (isset($resultado_vo->id_indicador[$ind_sql])) ? $resultado_vo->id_indicador[$ind_sql] : 0;
    if (isset($resultado_vo->linea_base[$ind_sql]))   $l_b = $resultado_vo->linea_base[$ind_sql]; 
}
?>
<br />
<table cellpadding="0" cellspacing="0" border="0" align="center">
<tr><td><label>Indicador <?php echo $ind ?></td></tr>
<tr><td>
        <select id="id_indicador" name="id_indicador[]" class="textfield_ind" style="width:750px;font-size:11px;font-weight:normal">
            <option value="0"></option>';
                <?php
                $indicador_dao->ListarCombo('combo',$id_indicador,'resultado=1');
                ?>

        </select>
    </td> 
</tr>
<tr><td>&nbsp;</td></tr>
<tr><td><label>Linea de Base</td></tr>
<tr><td><textarea id="linea_base" name="linea_base_<?=$ind_sql?>" style="width:750px;height:50px;" class="textfield_ind"><?=$l_b?></textarea></td></tr>

<?php
for ($a=$aaaa_ini;$a<=$aaaa_fin;$a++){

    $valor = isset($resultado_vo->indicador_valor[$id_indicador][$a]) ? $resultado_vo->indicador_valor[$id_indicador][$a] : '';
    
    echo '<tr><td>&nbsp;</td></tr>';
    echo "<tr><td><label>Valor indicador $ind $a</td></tr>";
    echo '<tr><td><input type="text" name="indicador_valor_'.$a.'_'.$ind_sql.'" style="width:400px" class="textfield_ind" value="'.$valor.'" /></td></tr>';
}
?>
</table>
    

