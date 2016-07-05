<?
//INICIALIZACION DE VARIABLES
$comuna_dao = New ComunaDAO();
$comuna_vo = New Comuna();
$depto_dao = New DeptoDAO();
$mun_dao = New MunicipioDAO();
$pob_dao = New PobladoDAO();

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

$id_mun = 0;
if (isset($_GET["id_mun"])){
	$id_mun = $_GET["id_mun"];
}

$readonly = "";
//Caso de Actualizacion
if (isset($_GET["accion"]) && $_GET["accion"] == "actualizar"){
	$id = $_GET["id"];
	$comuna_vo = $comuna_dao->Get($id);
	$readonly = " readonly ";

	$mun = $mun_dao->Get($comuna_vo->id_mun);
	$id_depto = $mun->id_depto;
	$id_mun = $mun->id;
}

?>

<form method="POST" onsubmit="submitForm(event);return false;">
	<table class="tabla_insertar">
		<tr>
			<td align="right">Departamento</td>
			<td>
				<select name="id_depto" class="select" onchange="refreshWindow('index_parser.php?accion=insertar&id_depto='+this.value)">
				<option value=''>Seleccione alguno...</option>
				<? $depto_dao->ListarCombo('combo',$id_depto,''); ?>
				</select>
			</td>
		</tr>
		<?
		if (isset($_GET["id_depto"]) || $accion == "actualizar"){
			echo "<tr><td align='right'>Municipio</td><td><select id='id_mun' name='id_mun' class='select' onchange=\"refreshWindow('index_parser.php?accion=insertar&id_depto=".$id_depto."&id_mun='+this.value)\">";
			echo "<option value=''>Seleccione alguno...</option>";
			$mun_dao->ListarCombo('combo',$id_mun,'ID_DEPTO='.$id_depto);
			echo "</select>";
		}
		if (isset($_GET["id_mun"]) || $accion == "actualizar"){

			echo "<tr><td align='right'>Poblado</td><td><select name='id_pob' class='select' onchange=\"document.getElementById('id').value=this.value+'xx'\">";
			echo "<option value=''>Seleccione alguno...</option>";
			$pob_dao->ListarCombo('combo',$comuna_vo->id_pob,"ID_MUN='".$id_mun."'");
			echo "</select>";
		?>
		<tr><td align="right">ID</td><td><input type="text" id="id" name="id" size="10" value="<?=$comuna_vo->id;?>" class="textfield" <?=$readonly;?> /></td></tr>
		<tr><td align="right">Nombre</td><td><input type="text" id="nombre" name="nombre" size="40" value="<?=$comuna_vo->nombre;?>" class="textfield" /></td></tr>
		<tr>
		  <td colspan="2" align='center'>
			  <br>
				<input type="hidden" name="accion" value="<?=$accion?>" />
				<input type="submit" name="submit" value="Aceptar" class="boton" onclick="return validar_forma('id,ID,nombre,Nombre','');" />
			</td>
		</tr>
		<?
		}
		?>
	</table>
</form>	
