<?
//INICIALIZACION DE VARIABLES
$cnrr_dao = New CnrrDAO();
$cnrr_vo = New Cnrr();

if (isset($_GET["accion"])){
	$accion = $_GET["accion"];
}
else if (isset($_POST["accion"])){
	$accion = $_POST["accion"];
}

//Caso de Actualizacion
if (isset($_GET["accion"]) && $_GET["accion"] == "actualizar"){
	$id = $_GET["id"];
  $cnrr_vo = $cnrr_dao->Get($id);
}

?>

	  <form action="<?=$_SERVER['PHP_SELF']?>" method="POST">
		  <table border="0" cellpadding="3" cellspacing="1" width="70%" align="center">
			  <tr><td align="center"><b>Actualizar Entidaddes para CNRR</b></td></tr>
			</table>
			<br>
			<table border="0" cellpadding="5" cellspacing="1" width="70%" align="center">
				<tr><td align="right">Nombre</td><td><input type="text" id="nombre" name="nombre" size="40" value="<?=$cnrr_vo->nombre;?>" class="textfield" /></td></tr>
				<tr>
				  <td colspan="2" align='center'>
					  <br>
						<input type="hidden" name="accion" value="<?=$accion?>" />
					<input type="hidden" name="id" value="<?=$cnrr_vo->id;?>" />									
						<input type="submit" name="submit" value="Aceptar" class="boton" onclick="return validar_forma('nombre,Nombre','');" />
					</td>
				</tr>
			</table>
		</form>	