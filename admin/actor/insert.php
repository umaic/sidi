<?
//INICIALIZACION DE VARIABLES
$actor_dao = New ActorDAO();
$actor_vo = New Actor();
$papa_vo = New Actor();
$abuelo_vo = New Actor();

if (isset($_GET["accion"])){
	$accion = $_GET["accion"];
}

//Caso de Actualizacion
if (isset($_GET["accion"]) && $_GET["accion"] == "actualizar"){
	$id = $_GET["id"];
	$actor_vo = $actor_dao->Get($id);
	
	$id_abuelo = 0;
	
	if ($actor_vo->nivel == 2){
		$abuelo_vo = $actor_dao->Get($actor_vo->id_papa);
		$id_abuelo = $abuelo_vo->id;
	}
	else if ($actor_vo->nivel == 3){
		$papa_vo = $actor_dao->Get($actor_vo->id_papa);
		$abuelo_vo = $actor_dao->Get($papa_vo->id_papa);
		$id_abuelo = $abuelo_vo->id;
	}
	
}

?>

<form method="POST" onsubmit="submitForm(event);return false;">
	<table border="0" cellpadding="5" cellspacing="1" width="70%" align="center" class="tabla_insertar">
		<tr>
			<td align="right" width="40%">Abuelo</td>
			<td>
				<?
				if ($accion == 'insertar'){ ?>
					<select id="abuelo" name="abuelo" class="select" onchange="getDataV1('','ajax_data.php?object=comboBoxActorInsertar&onchange=1&name_field=papa&multiple=0&separador=0&id_papa='+this.value,'comboBoxSubactor')">
				<?
				}
				else{ ?>
					<select id="abuelo" name="abuelo" class="select">
				<? } ?>
					<option value=''>No aplica</option>
					<? $actor_dao->ListarCombo('combo',$id_abuelo,'NIVEL=1'); ?>
				</select>
			</td>
		</tr>
		<tr>
			<td align="right">Papa</td>
			<td id="comboBoxSubactor">
				<select id="papa" name="papa" class="select">
					<option value=''>No aplica</option>
					<? 
					if ($accion == 'actualizar')
					$actor_dao->ListarCombo('combo',$papa_vo->id,"id_papa = $id_abuelo AND NIVEL=2"); ?>
				</select>
			</td>
		</tr>
		<tr><td align="right">ID</td><td><input type="text" id="cod_interno" name="cod_interno" size="20" value="<?=$actor_vo->cod_interno;?>" class="textfield" /></td></tr>
		<tr><td align="right">Nombre</td><td><input type="text" id="nombre" name="nombre" size="50" value='<?=$actor_vo->nombre;?>' class="textfield" /></td></tr>
		<tr>
		  <td colspan="2" align='center'>
			  <br>
				<input type="hidden" name="accion" value="<?=$accion?>" />
				<input type="hidden" name="id" value="<?=$actor_vo->id;?>" />
				<input type="submit" name="submit" value="Aceptar" class="boton" onclick="return validar_forma('cod_interno,ID,nombre,Nombre','');" />
			</td>
		</tr>
	</table>
	<br>
	<table class="tabla_insertar" align="center" cellspacing="1" cellpadding="3" width="70%">
		<tr>
			<td align="center" colspan="3"><b>EJEMPLO</b></td>
		</tr>
		<tr class="titulo_lista">
			<td>Actor</td>
			<td>Abuelo</td>
			<td>Papa</td>
		</tr>
		<tr>
			<td>Ejercito Nacional</td>
			<td>No aplica</td>
			<td>No aplica</td>
		</tr>
		<tr>
			<td>COMANDO ESPECIAL EJÉRCITO</td>
			<td>Ejercito Nacional</td>
			<td>No aplica</td>
		</tr>
		<tr>
			<td>Batallón de contraguerrillas N° 66 " Capitán Valentín García"</td>
			<td>Ejercito Nacional</td>
			<td>COMANDO ESPECIAL EJÉRCITO</td>
		</tr>
	</table>
</form>	
