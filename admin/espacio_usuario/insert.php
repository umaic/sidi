<?
//INICIALIZACION DE VARIABLES
$perfi_usuario_dao = New EspacioUsuarioDAO();
$perfi_usuario_vo = New EspacioUsuario();
$tipo_usuario_dao = New TipoUsuarioDAO();
$tipo_usuario_vo = New TipoUsuario();
$espacio_dao = New EspacioDAO();
$espacio_vo = New Espacio();

if (isset($_GET["accion"])){
	$accion = $_GET["accion"];
}
else if (isset($_POST["accion"])){
	$accion = $_POST["accion"];
}


$id_tipo_usaurio = 0;
if (isset($_GET["id_tipo_usaurio"])){
	$id_tipo_usaurio = $_GET["id_tipo_usaurio"];
	$perfi_usuario_vo = $perfi_usuario_dao->Get($id_tipo_usaurio);
}

?>

	  <form method="POST" onsubmit="submitForm(event);return false;">
		  <table border="0" cellpadding="3" cellspacing="1" width="70%" align="center">
			  <tr><td align="center"><b><?=ucfirst($accion)?> Tipo de Usuario</b></td></tr>
			</table>
			<br>
			<table class="tabla_insertar">
			  <tr>
				  <td width="150" align="right">Tipo de Usuario</td>
					<td>
            			<select name="id_tipo_usuario" class="select" onchange="location.href='<?=$_SERVER['PHP_SELF']?>?accion=<?=$accion?>&id_tipo_usuario='+this.value;">
							<option value="0">Seleccione alguno</option>
							<? $tipo_usuario_dao->ListarCombo('combo',$id_tipo_usuario,'CNRR = 0'); ?>						
						</select>
					</td>
				</tr>
				<?
				if (isset($_GET["id_tipo_usuario"])){
					$condicion = 'ID_TIPO_USUARIO = '.$id_tipo_usaurio;
					$espacio_usuario_dao->ListarTabla($condicion);
						?>
      				<tr>
      				  <td colspan="2" align='center'>
      					  <br>
      						<input type="hidden" name="accion" value="<?=$accion?>" />
    							<input type="hidden" name="id" value="<?=$perfi_usuario_vo->id;?>" />									
      						<input type="submit" name="submit" value="Aceptar" class="boton" onclick="return validar_forma('nombre,Nombre','');" />
      					</td>
      				</tr>
    				<?
				}
				?>
			</table>
		</form>	
