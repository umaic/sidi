<?
//INICIALIZACION DE VARIABLES
$contacto_dao = New ContactoDatoSectorDAO();
$contacto_vo = New Contacto();
$org_dao = new OrganizacionDAO();
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
	if (isset($contacto_vo->id_org[0]))	$id_org = $contacto_vo->id_org[0];
}

?>
<script type="text/javascript" src="../js/filterlist.js"> </script>

<form method="POST" onsubmit="submitForm(event);return false;">
	<table border="0" cellpadding="5" cellspacing="1" align="center">
		<tr><td align="right" width="300">Nombres</td><td><input type="text" id="nombre" name="nombre" size="40" value="<?=$contacto_vo->nombre;?>" class="textfield" /></td></tr>
		<tr><td align="right" width="300">Apellidos</td><td><input type="text" id="apellido" name="apellido" size="40" value="<?=$contacto_vo->apellido;?>" class="textfield" /></td></tr>
		<tr><td align="right">Tel&eacute;fono directo</td><td><input type="text" id="tel" name="tel" size="40" value="<?=$contacto_vo->tel;?>" class="textfield" /></td></tr>
		<tr><td align="right">Celular</td><td><input type="text" id="cel" name="cel" size="40" value="<?=$contacto_vo->cel;?>" class="textfield" /></td></tr>
		<tr><td align="right">Fax</td><td><input type="text" id="fax" name="fax" size="40" value="<?=$contacto_vo->fax;?>" class="textfield" /></td></tr>
		<tr><td align="right">Email</td><td><input type="text" id="email" name="email" size="40" value="<?=$contacto_vo->email;?>" class="textfield" /></td></tr>
		<tr><td>&nbsp;</td></tr>
		<tr><td colspan="2" height="1" class="td_dotted_top" colspan="3"><img src="images/spacer.gif" height="1"></tr>
		<tr>
			<td align="right"><b>Organizaci&oacute;n a la que <br />pertenece el cont&aacute;cto</b></td>
			<td>
				<select id='id_org' name='id_org[]' class='select' style="width:500px" size="10">
					<? $org_dao->ListarCombo('combo',$id_org,''); ?>
				</select>
				
				<!-- Filtrar datos sectoriales -->
				<script type="text/javascript">
				var myfilter = new filterlist(document.getElementById('id_org'));
				</script>
			</td>
		</tr>
		<tr>
			<td>&nbsp;</td>
			<td>
			<b>Filtrar lista por letra inicial</b><br />
				<A HREF="javascript:myfilter.reset()" TITLE="Clear the filter">Todos</A>&nbsp;|
				<A HREF="javascript:myfilter.set('^A')" TITLE="Show items starting with A">A</A>
				<A HREF="javascript:myfilter.set('^B')" TITLE="Show items starting with B">B</A>
				<A HREF="javascript:myfilter.set('^C')" TITLE="Show items starting with C">C</A>
				<A HREF="javascript:myfilter.set('^D')" TITLE="Show items starting with D">D</A>
				<A HREF="javascript:myfilter.set('^E')" TITLE="Show items starting with E">E</A>
				<A HREF="javascript:myfilter.set('^F')" TITLE="Show items starting with F">F</A>
				<A HREF="javascript:myfilter.set('^G')" TITLE="Show items starting with G">G</A>
				<A HREF="javascript:myfilter.set('^H')" TITLE="Show items starting with H">H</A>
				<A HREF="javascript:myfilter.set('^I')" TITLE="Show items starting with I">I</A>
				<A HREF="javascript:myfilter.set('^J')" TITLE="Show items starting with J">J</A>
				<A HREF="javascript:myfilter.set('^K')" TITLE="Show items starting with K">K</A>
				<A HREF="javascript:myfilter.set('^L')" TITLE="Show items starting with L">L</A>
				<A HREF="javascript:myfilter.set('^M')" TITLE="Show items starting with M">M</A>
				<A HREF="javascript:myfilter.set('^N')" TITLE="Show items starting with N">N</A>
				<A HREF="javascript:myfilter.set('^O')" TITLE="Show items starting with O">O</A>
				<A HREF="javascript:myfilter.set('^P')" TITLE="Show items starting with P">P</A>
				<A HREF="javascript:myfilter.set('^Q')" TITLE="Show items starting with Q">Q</A>
				<A HREF="javascript:myfilter.set('^R')" TITLE="Show items starting with R">R</A>
				<A HREF="javascript:myfilter.set('^S')" TITLE="Show items starting with S">S</A>
				<A HREF="javascript:myfilter.set('^T')" TITLE="Show items starting with T">T</A>
				<A HREF="javascript:myfilter.set('^U')" TITLE="Show items starting with U">U</A>
				<A HREF="javascript:myfilter.set('^V')" TITLE="Show items starting with V">V</A>
				<A HREF="javascript:myfilter.set('^W')" TITLE="Show items starting with W">W</A>
				<A HREF="javascript:myfilter.set('^X')" TITLE="Show items starting with X">X</A>
				<A HREF="javascript:myfilter.set('^Y')" TITLE="Show items starting with Y">Y</A>
				<A HREF="javascript:myfilter.set('^Z')" TITLE="Show items starting with Z">Z</A>
			</td>
		</tr>
		<tr>
			<td>&nbsp;</td>
			<td>
				<b>Filtrar lista por palabra</b>:&nbsp;
				<input id="regexp" name="regexp" onKeyUp="myfilter.set(this.value)" class="textfield" size="50">	
			</td>
		</tr>
		<tr><td>&nbsp;</td></tr>
		<tr><td height="1" colspan="2" class="td_dotted_top" colspan="3"><img src="images/spacer.gif" height="1"></tr>
		<tr>
		  <td colspan="2" align='center'>
			  <br>
				<input type="hidden" name="id_espacio[]" value="37" />
				<input type="hidden" name="accion" value="<?=$accion?>" />
				<input type="hidden" name="id" value="<?=$contacto_vo->id;?>" />									
				<input type="submit" name="submit" value="Aceptar" class="boton" onclick="return validar_forma('nombre,Nombres,tel,Teléfono directo',document.getElementById('email').value);" />
			</td>
		</tr>
	</table>
</form>	
