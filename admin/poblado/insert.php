<?
//INICIALIZACION DE VARIABLES
$poblado_dao = New PobladoDAO();
$poblado_vo = New Poblado();
$depto_dao = New DeptoDAO();
$mun_dao = New MunicipioDAO();

if (isset($_GET["accion"])){
	$accion = $_GET["accion"];
}
else if (isset($_POST["accion"])){
	$accion = $_POST["accion"];
}


$id_depto = '';
if (isset($_GET["id_depto"])){
  $id_depto = $_GET["id_depto"];
}

$readonly = "";
//Caso de Actualizacion
if (isset($_GET["accion"]) && $_GET["accion"] == "actualizar"){
	$id = $_GET["id"];
  	$poblado_vo = $poblado_dao->Get($id);
  	$readonly = " readonly ";
  	
  	$mun = $mun_dao->Get($poblado_vo->id_mun);
  	$id_depto = $mun->id_depto;
}

?>

<form method="POST" onsubmit="submitForm(event);return false;">
	<table class="tabla_insertar">
		<tr>
			<td align="right">Departamento</td>
			<td>
				<select id="id_depto" class="select" onchange="listarMunicipios('id_depto',0,0,0,'id_mun')">
				<option value=''>Seleccione alguno...</option>
				<? $depto_dao->ListarCombo('combo',$id_depto,''); ?>
				</select>
			</td>
		</tr>
		<tr>
			<td align='right'>Municipio</td><td id='comboBoxMunicipio'>
			<? 
			if ($accion == 'actualizar'){
				echo "<select id='id_mun' name='id_mun' class='select'>";
				$mun_dao->ListarCombo('combo',$poblado_vo->id_mun,'ID_DEPTO='.$id_depto);
				echo "</select>";
			}
			else{
				echo "<input type='hidden' id='id_mun' value=''>";
			}
			?>
			</td>
		</tr>	
		<tr><td align="right">ID</td><td><input type="text" id="id" name="id" size="10" value="<?=$poblado_vo->id;?>" class="textfield" <?=$readonly;?> /></td></tr>
		<tr><td align="right">Nombre</td><td><input type="text" id="nombre" name="nombre" size="40" value="<?=$poblado_vo->nombre;?>" class="textfield" /></td></tr>
		<tr><td align="right">Clase de Poblado</td><td><input type="text" id="clase" name="clase" size="40" value="<?=$poblado_vo->clase;?>" class="textfield" /></td></tr>
		<tr><td align="right">A&ntilde;o de creaci&oacute;n</td><td><input type="text" id="nacimiento" name="nacimiento" size="40" value="<?=$poblado_vo->nacimiento;?>" class="textfield" /></td></tr>
		<tr><td align="right">Acto Administrativo mediante <br>el cual se creo el poblado</td><td><textarea id="acto_admin" name="acto_admin" cols="40" rows="5" class="textfield"><?=$poblado_vo->acto_admin;?></textarea></td></tr>
		<tr>
		  <td colspan="2" align='center'>
			  <br>
				<input type="hidden" name="accion" value="<?=$accion?>" />
				<input type="submit" name="submit" value="Aceptar" class="boton" onclick="return validar_forma('id_mun,Municipio,id,ID,nombre,Nombre','');" />
			</td>
		</tr>
	</table>
</form>	
