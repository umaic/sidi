<?
//INICIALIZACION DE VARIABLES
$fuente_pba_dao = New UnicefFuentePbaDAO();
$fuente_pba_vo = New UnicefFuentePba();
$donante_dao = new UnicefDonanteDAO();

if (isset($_GET["accion"])){
	$accion = $_GET["accion"];
}
else if (isset($_POST["accion"])){
	$accion = $_POST["accion"];
}

//Caso de Actualizacion
if (isset($_GET["accion"]) && $_GET["accion"] == "actualizar"){
    $id = $_GET["id"];
    $fuente_pba_vo = $fuente_pba_dao->Get($id);
}

?>

<form method="POST" onsubmit="submitForm(event);return false;">
	<table class="tabla_insertar">
		<tr><td><label>Donante</label></td></tr>
        <tr>
            <td>
                <select id="id_donante" name="id_donante" class="select">
                    <option value=''></option>
                    <?php $donante_dao->ListarCombo('combo',$fuente_pba_vo->id_donante,''); ?>
                </select>
            </td>
        </tr>
        <tr><td>&nbsp;</td></tr>

		<tr><td><label>Fuente PBA</label></td></tr>
        <tr><td><textarea id="nombre" name="nombre" style="width:650px;height:100px" class="textfield"><?=$fuente_pba_vo->nombre;?></textarea></td></tr>
        <tr><td>&nbsp;</td></tr>
		<tr>
		  <td colspan="2" align='center'>
			  <br>
				<input type="hidden" name="accion" value="<?=$accion?>" />
			<input type="hidden" name="id" value="<?=$fuente_pba_vo->id;?>" />									
				<input type="submit" name="submit" value="Aceptar" class="boton" onclick="return validar_forma('id_donante,Donante,nombre,Nombre','');" />
			</td>
		</tr>
	</table>
</form>	
