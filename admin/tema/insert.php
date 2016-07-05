<?
//INICIALIZACION DE VARIABLES
$tema_dao = New TemaDAO();
$tema_vo = New Tema();
$clas_dao = New ClasificacionDAO();

if (isset($_GET["accion"])){
	$accion = $_GET["accion"];
}
else if (isset($_POST["accion"])){
	$accion = $_POST["accion"];
}

//Caso de Actualizacion
if (isset($_GET["accion"]) && $_GET["accion"] == "actualizar"){
	$id = $_GET["id"];
  $tema_vo = $tema_dao->Get($id);
}

?>

<form method="POST" onsubmit="submitForm(event);return false;">
    <table class="tabla_insertar">
	<tr>
	    <td align="right">Clasificaci&oacute;n</td>
	    <td>
		<select name="id_clasificacion" class="select">
		    <?
		    $clas_dao->ListarCombo("",$tema_vo->id_clasificacion,"");
		    ?>
		</select>
	    </td>
	</tr>
	<tr>
	    <td align="right">Pertenece a</td>
	    <td>
		<select name="id_papa" class="select">
			<option value=0></option>
		    <?
		    $tema_dao->ListarCombo("combo",$tema_vo->id_papa,"id_papa=0");
		    ?>
		</select>
	    </td>
	</tr>
	<tr><td align="right">Nombre</td><td><input type="text" id="nombre" name="nombre" size="40" value="<?=$tema_vo->nombre;?>" class="textfield" /></td></tr>
	<tr><td align="right">Definici&oacute;n</td><td><textarea  id="def" name="def" class="textfield" style="width:600px;height:170px" /> <?=$tema_vo->def;?></textarea></tr>
	<tr>
	  <td colspan="2" align='center'>
		  <br>
			<input type="hidden" name="accion" value="<?=$accion?>" />
		<input type="hidden" name="id" value="<?=$tema_vo->id;?>" />									
			<input type="submit" name="submit" value="Aceptar" class="boton" onclick="return validar_forma('nombre,Nombre','');" />
		</td>
	</tr>
    </table>
</form>	
