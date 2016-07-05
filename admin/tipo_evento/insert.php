<?
//INICIALIZACION DE VARIABLES
$tipo_evento_dao = New TipoEventoDAO();
$tipo_evento_vo = New TipoEvento();
$cat_tipo_evento_dao = New CatTipoEventoDAO();
$cat_tipo_evento_vo = New CatTipoEvento();

if (isset($_GET["accion"])){
	$accion = $_GET["accion"];
}
else if (isset($_POST["accion"])){
	$accion = $_POST["accion"];
}

$id_cat = 0;
if (isset($_GET["id_cat"])){
  $id_cat = $_GET["id_cat"];
}

//Caso de Actualizacion
if (isset($_GET["accion"]) && $_GET["accion"] == "actualizar"){
	$id = $_GET["id"];
	$tipo_evento_vo = $tipo_evento_dao->Get($id);

	$id_cat = $tipo_evento_vo->id_cat;
	if (isset($_GET["id_cat"])){
		$id_cat = $_GET["id_cat"];
	}
}

?>

<form method="POST" onsubmit="submitForm(event);return false;">
<table class="tabla_insertar">
  <tr>
	  <td width="150" align="right">Categoria</td>
		<td>
		<?
		if ($accion == "actualizar"){ ?>
		  <select name="id_cat" class="select" onchange="refreshWindow('index_parser.php?accion=<?=$accion?>&id=<?=$id?>&id_cat='+this.value);">
		<?
		}
		else{ ?>
			<select name="id_cat" class="select" onchange="refreshWindow('index_parser.php?accion=<?=$accion?>&id_cat='+this.value);">
		<?					
		}
		?>
			  <option value="0">Seleccione alguna</option>
				<? $cat_tipo_evento_dao->ListarCombo('combo',$id_cat,''); ?>						
			</select>
		</td>
	</tr>
	<?
	if (isset($_GET["id_cat"]) || $accion == "actualizar"){
	?>
		<tr>
		  <td align="right">Grupo</td>
			<td>
			  <select name="id_papa" class="select">
				  <option value="0">No Aplica</option>
					<? $tipo_evento_dao->ListarCombo('combo',$tipo_evento_vo->id_papa,'ID_TIPOEVE_ID_TIPO_EVE=0 AND ID_CAT_TIPO_EVE='.$id_cat); ?>						
				</select>
			</td>
		</tr>				
		<tr><td align="right">Nombre</td><td><input type="text" id="nombre" name="nombre" size="40" value="<?=$tipo_evento_vo->nombre;?>" class="textfield" /></td></tr>
		<tr>
			<td align="right">Icono</td>
			<td>
				<input type="text" id="icono" name="icono" size="40" value="<?=$riesgo_hum_vo->icono;?>" class="textfield" />
				<input type="button" name="to_images" value="Seleccionar Imagen" class="boton" onclick="window.open('select_image.php?field=icono&dir=riesgo_hum','','width=700,height=400,left=150,top=60,scrollbars=1,status=1')">
			</td>
		</tr>
		<?
		if ($accion == 'actualizar' && strlen($tipo_evento_vo->icono) > 0){ ?>
			<tr>
				<td align="right">Icono actual</td>
				<td>
					<img src='../<?=$tipo_evento_vo->icono?>'>
				</td>
			</tr>
		<?
		}
		?>
		<tr>
		  <td colspan="2" align='center'>
			  <br>
				<input type="hidden" name="accion" value="<?=$accion?>" />
					<input type="hidden" name="id" value="<?=$tipo_evento_vo->id;?>" />									
				<input type="submit" name="submit" value="Aceptar" class="boton" onclick="return validar_forma('nombre,Nombre','');" />
			</td>
		</tr>
		<?
	}
	?>
</table>
</form>	
