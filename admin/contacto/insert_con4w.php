<?
//INICIALIZACION DE VARIABLES
$contacto_dao = New ContactoDAO();
$contacto_vo = New Contacto();
$org_dao = new OrganizacionDAO();
$id_car = 0;
$id_org = 0;

if (isset($_GET["accion"])){
	$accion = $_GET["accion"];
}
else if (isset($_POST["accion"])){
	$accion = $_POST["accion"];
}

//Caso de Actualizacion
if (isset($_GET["accion"]) && $_GET["accion"] == "actualizar"){
	$id = $_GET["id"];
	$contacto_vo = $contacto_dao->Get($id);
	$id_car = $contacto_vo->caracteristicas[$contacto_col->id];
	$id_org = $contacto_vo->id_org[0];
}

?>
<form method="POST" onsubmit="submitForm(event);return false;" name="contacto">
  <table border="0" cellpadding="3" cellspacing="1" align="center" class="tabla_org">
	  <tr><td align="center"><b>Crear Contacto</b></td></tr>
	</table>
	<br>
    <table border="0" cellpadding="5" cellspacing="1" width="100%" align="center" class="tabla_org">

        <tr>
            <td width="50%">
                <table>
                    <tr><td align="right" width="300">Nombres</td><td><input type="text" id="nombre" name="nombre" size="20" value="<?=$contacto_vo->nombre;?>" class="textfield" /></td></tr>
                    <tr><td  align="right">Apellidos</td><td><input type="text" id="apellido" name="apellido" size="20" value="<?=$contacto_vo->apellido;?>" class="textfield" /></td></tr>
                    <tr><td align="right">Tel&eacute;fono directo</td><td><input type="text" id="tel" name="tel" size="20" value="<?=$contacto_vo->tel;?>" class="textfield" /></td></tr>
                </table>
            </td>
            <td>
                <table>
                    <tr><td>Email</td><td><input type="text" id="email" name="email" size="20" value="<?=$contacto_vo->email;?>" class="textfield" /></td></tr>
                    <tr><td>Celular</td><td><input type="text" id="cel" name="cel" size="20" value="<?=$contacto_vo->cel;?>" class="textfield" /></td></tr>
                    <tr><td>Fax</td><td><input type="text" id="fax" name="fax" size="20" value="<?=$contacto_vo->fax;?>" class="textfield" /></td></tr>
                </table>
            </td>
        </tr>
		<tr>
			<td align="center" colspan="2"><b>Organizaci&oacute;n a la que pertenece el cont&aacute;cto</b></td>
        </tr>
		<tr>
            <td colspan="2" align="center">
                <input type="hidden" id="id_org_con" name="id_org[]" />
                <textarea type="text" id="nom_org_con" name="nom_org" 
                class="textfield txlarge" onkeydown="buscarOcurr(event, 'nom_org_con', 'id_org_con', 'ocurr_org_con');"></textarea>
                <p>Busque por NOMBRE o SIGLA en Español o Inglés y seleccione la
                Organización de la lista que aparecerá</p>
            </td>
		</tr>
		<tr><td colspan="2" align="center" id="ocurr_org_con" class="ocurrencia relative">&nbsp;</td></tr>
        <tr>
          <td colspan="2" align='center'>
                <input type="hidden" name="accion" value="<? echo $accion; ?>">
                <input type="hidden" name="id" value="<?=$contacto_vo->id;?>" />									
                <input type="hidden" name="si_con" value="4w" />									
                <input type="submit" name="submit" value="Aceptar" class="boton" onclick="return validar_forma('nombre,Nombres,apellido,Apellidos,tel,Teléfono directo,email,Email,id_org_con,Organización',document.getElementById('email').value);" />
            </td>
        </tr>
	</table>
</form>	
