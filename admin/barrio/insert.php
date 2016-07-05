<?
//INICIALIZACION DE VARIABLES
$barrio_dao = New BarrioDAO();
$barrio_vo = New Barrio();
$depto_dao = New DeptoDAO();
$mun_dao = New MunicipioDAO();
$pob_dao = New PobladoDAO();
$comuna_dao = New ComunaDAO();

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

$id_pob = 0;
if (isset($_GET["id_pob"])){
	$id_pob = $_GET["id_pob"];
}

$readonly = "";
//Caso de Actualizacion
if (isset($_GET["accion"]) && $_GET["accion"] == "actualizar"){
	$id = $_GET["id"];
	$barrio_vo = $barrio_dao->Get($id);
	$readonly = " readonly ";

	$mun = $mun_dao->Get($barrio_vo->id_mun);
	$id_depto = $mun->id_depto;
	$id_mun = $mun->id;
	$id_pob = $barrio_vo->id_pob;
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

			echo "<tr><td align='right'>Poblado</td><td><select name='id_pob' class='select' onchange=\"refreshWindow('index_parser.php?accion=insertar&id_depto=".$id_depto."&id_mun=".$id_mun."&id_pob='+this.value)\">";
			echo "<option value=''>Seleccione alguno...</option>";
			$pob_dao->ListarCombo('combo',$id_pob,"ID_MUN='".$id_mun."'");
			echo "</select>";
		}				
		if (isset($_GET["id_pob"]) || $accion == "actualizar"){

			echo "<tr><td align='right'>Comuna</td><td><select name='id_comuna' class='select' onchange=\"document.getElementById('id').value=this.value+'xxxx'\">";
			echo "<option value=''>Seleccione alguno...</option>";
			$comuna_dao->ListarCombo('combo',$barrio_vo->id_comuna,"ID_POB='".$id_pob."'");
			echo "</select>";
		?>
		<tr><td align="right">ID</td><td><input type="text" id="id" name="id" size="14" value="<?=$barrio_vo->id;?>" class="textfield" <?=$readonly;?> /></td></tr>
		<tr><td align="right">Nombre</td><td><input type="text" id="nombre" name="nombre" size="40" value="<?=$barrio_vo->nombre;?>" class="textfield" /></td></tr>
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
