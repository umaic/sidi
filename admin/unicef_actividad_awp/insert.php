<?
//INICIALIZACION DE VARIABLES
$actividad_dao = New UnicefActividadAwpDAO();
$actividad_vo = New UnicefActividadAwp();
$estado_dao = new UnicefEstadoDAO();
$producto_dao = new UnicefProductoCpapDAO();
$tema_undaf_dao = new TemaDAO();
$periodo_dao = new UnicefPeriodoDAO();

// Se define en /admin/unicef_proyecto/get_nodes.php
$id_node_papa = $_SESSION['id_node_papa_click'];

$accion = '';
if (isset($_GET["accion"])){
	$accion = $_GET["accion"];
}
else if (isset($_POST["accion"])){
	$accion = $_POST["accion"];
}

$id_producto = (isset($_GET["id_papa"])) ? $_GET["id_papa"] : 0;
//$id_node_papa = "id=$id_vo&id_papa=$id&accion=actualizar&m_e=".$data[$caso]['m_e']."&caso=".$data[$caso]['hijo'];
$id_node_papa = $_SESSION['id_node_papa_click'];
$id_papa = $producto_dao->GetFieldValue($id_producto,'id_resultado');
$periodo = $periodo_dao->GetAllArray('activo=1');
$aaaa_ini = $periodo[0]->aaaa_ini;
$aaaa_fin = $periodo[0]->aaaa_fin;

$actividad_vo->id_estado = 1;

if ($accion == 'insertar'){
    $codigo_papa = $producto_dao->getFieldValue($id_producto,'codigo');
    $num_hermanos = $actividad_dao->numRecords("id_producto = $id_producto AND aaaa = ".$_SESSION['aaaa']);
    $codigo = $codigo_papa."_".($num_hermanos + 1);
}

//Caso de Actualizacion
else if ($accion == 'actualizar'){
	$id = $_GET["id"];
    $actividad_vo = $actividad_dao->Get($id);
    $codigo = $actividad_vo->codigo;
}

?>

<form method="POST" onsubmit="submitForm(event,'<?=$id_node_papa?>');return false;">
	<table class="tabla_insertar">
        <?php
        if ($accion == 'actualizar' && !$actividad_dao->checkForeignKeys($id)){
            ?>
            <tr><td align="right">
				<a class="boton_new" href='#' onclick="if(confirm('Est&aacute; seguro que desea borrar esta Actividad?')){borrarRegistro('UnicefActividadAwpDAO','<?=$id?>','<?=$id_node_papa?>')}else{return false};"><img src='images/unicef/delete.gif' border='0' title='Borrar' />&nbsp;Borrar esta Actividad AWP</a>&nbsp;
            </td></tr>
        <? } ?>
		<tr>
			<td><label>Producto CPAP al que pertenece</label></td>
		</tr>
        <tr>
            <td>
                <select name="id_producto" class="textfield" style="font-size:14px;width:800px;font-weight:normal;padding:0">
                    <?php $producto_dao->ListarCombo('combo',$id_producto,"id_resultado=$id_papa"); ?>
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
                        if ($a == $actividad_vo->aaaa)    echo ' selected ';
                        echo ">$a</option>";
                    }
                    ?>
                </select>
            </td>
        </tr>
        <tr><td>&nbsp;</td></tr>
		<tr>
			<td><label>Actividad AWP</label></td>
		</tr>
		<tr>
			<td><textarea id="nombre" name="nombre" style="width:800px;height:50px" class="textfield"><?=$actividad_vo->nombre;?></textarea></td>
		</tr>
        <tr><td>&nbsp;</td></tr>
        <tr><td><label>C&oacute;digo de la Actividad Awp</td></tr>
        <tr><td><input type="text" id="codigo" name="codigo" class="textfield" style="width:400px" value="<?=$codigo?>" /></td> </tr> 
        <tr><td>&nbsp;</td></tr>
        <tr><td><label>Estado de la Actividad AWP</td></tr>
        <tr><td>
                <select id="id_estado" name="id_estado" class="textfield" style="width:400px;font-size:24px;font-weight:normal">
                    <option></option> 
                    <?php $estado_dao->ListarCombo('combo',$actividad_vo->id_estado,''); ?> 
                </select>
            </td> 
        </tr>
        <tr><td>&nbsp;</td></tr>
        <tr><td><label>UNDAF AREA PRIORITARIA</td></tr>
        <tr>
            <td>
				<select name="id_tema_undaf_1" class="textfield">
				    <option value="0">Seleccione alguno...</option>
                    <?php $tema_undaf_dao->ListarCombo('combo',$actividad_vo->id_tema_undaf_1,'id_papa=0'); ?>
                </select>
            </td>
        </tr>
        <tr><td>&nbsp;</td></tr>
        <tr><td><label>UNDAF OUTCOME</td></tr>
        <tr>
            <td>
				<select name="id_tema_undaf_2" class="textfield">
				    <option value="0">Seleccione alguno...</option>
                    <?php $tema_undaf_dao->ListarCombo('combo',$actividad_vo->id_tema_undaf_2,'id_papa=0'); ?>
                </select>
            </td>
        </tr>
        <tr><td>&nbsp;</td></tr>
        <tr><td><label>UNDAF OUTPUT</td></tr>
        <tr>
            <td>
				<select name="id_tema_undaf_3" class="textfield">
				    <option value="0">Seleccione alguno...</option>
                    <?php $tema_undaf_dao->ListarCombo('combo',$actividad_vo->id_tema_undaf_3,'id_papa=0'); ?>
                </select>
            </td>
        </tr>
        <tr><td>&nbsp;</td></tr>
		<tr>
		  <td colspan="2" align='center'>
			  <br>
				<input type="hidden" name="accion" value="<?=$accion?>" />
			    <input type="hidden" name="id" value="<?=$actividad_vo->id;?>" />
				<input type="submit" name="submit" value="Aceptar" class="boton" onclick="return validar_forma('nombre,Nombre,id_estado,Estado','');" />
			</td>
		</tr>
	</table>
</form>	
