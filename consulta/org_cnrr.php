<?
//LIBRERIAS
include_once("consulta/lib/libs_org.php");

//INICIALIZACION DE VARIABLES
$depto_dao = New DeptoDAO();
$municipio_vo = New Municipio();
$municipio_dao = New MunicipioDAO();
$tipo_org = New TipoOrganizacionDAO();
$sector = New SectorDAO();
$enfoque = New EnfoqueDAO();
$poblacion = New PoblacionDAO();
$org = New OrganizacionDAO();
$method = "";

$id_depto = Array();
$num_deptos = 0;
if (isset($_GET['id_depto'])){
	$id_depto_s = split(",",$_GET['id_depto']);
	$id_depto = $id_depto_s[0];
	$num_deptos = count($id_depto_s);
	$id_depto = $id_depto_s;
	$id_depto_url = $id_depto;
}

//ACCION DE LA FORMA
if (isset($_POST["submit"])){
	$org->Reportar();
}

//REPORTE EXCEL
if (isset($_POST["reportar"])){
	$org->ReporteOrganizacion($_POST['id_orgs'],$_POST['pdf'],$_POST['basico']);
}

//VER DETALLES
if (isset($_GET["method"]) && $_GET["method"] == "Ver"){
	$org->Ver($_GET["param"]);
	die;
}

$display_consulta = '';
$display_grafica = 'none';
if (isset($_GET["method"])){
	$method = $_GET["method"];
	$display_consulta = 'none';
	$display_grafica = '';
}
?>
<script src="admin/js/ajax.js"></script>
<script>
function unHide(td,img){
	if (document.getElementById(td).style.display == 'none'){
		document.getElementById(td).style.display = '';
		document.getElementById(img).src = 'images/flecha_down.gif';
	}
	else{
		document.getElementById(td).style.display = 'none';
		document.getElementById(img).src = 'images/flecha.gif';
	}
}
function validarComboM(ob){
	selected = new Array();
	for (var i = 0; i < ob.options.length; i++){
		if (ob.options[ i ].selected)
		selected.push(ob.options[ i ].value);
	}

	if (selected.length == 0){
		return false;
	}
	else{
		return true;
	}
}
function validar_criterios(){

	var error = 1;
	var filtros = Array('id_tipo_org','id_sector','id_enfoque','id_poblacion');


	for (f=0;f<filtros.length;f++){
	  	if (validarComboM(document.getElementById(filtros[f]))){
		    error = 0;
		}
	}

	if (document.getElementById('id_depto').value == ""){
		document.getElementById('id_depto').disabled = true;
	}
	
  	if (error == 1){
	    //alert("Seleccione algún criterio");
	    if(confirm("No hay filtros, desea consultar todas las Organizaciones?")){
	    	document.getElementById('todas').value = 1;
	    }
	    else	return false;
	}
	else{
	  return true;
	}
		
	/*for (f=0;f<filtros.length;f++){
		if (validarComboM(document.getElementById(filtros[f]))){
			error = 0;
		}
	}

	if (error == 1){
		alert("Seleccione algún criterio");
		return false;
	}

	error = 1;
	if (document.getElementById('id_depto').value != ""){
		error = 0;
	}

	if (document.getElementById('id_muns').value != ""){
		error = 0;
	}

	if (error == 1){
		alert("Seleccione la Ubicación en el mapa");
		return false;
	}
	else{
		return true;
	}*/

}

function limpiarFiltros(){

	var filtros = Array('id_tipo_org','id_sector','id_enfoque','id_poblacion');

	for (f=0;f<filtros.length;f++){
		ob = document.getElementById(filtros[f]);
		for (var i = 0; i < ob.options.length; i++){
			ob.options[ i ].selected = false;
		}
	}

	alert('Se limpiaron los filtros.');
}

function mostrarFiltros(accion){
	var filtros = Array('tipo_org','sector','enfoque','poblacion');

	for (f=0;f<filtros.length;f++){
		if (document.getElementById('td_'+filtros[f]).style.display == 'none'){
			document.getElementById('td_'+filtros[f]).style.display = '';
			document.getElementById('img_'+filtros[f]).src = 'images/flecha_down.gif';
		}
		else{
			document.getElementById('td_'+filtros[f]).style.display = 'none';
			document.getElementById('img_'+filtros[f]).src = 'images/flecha.gif';
		}
	}
}

function addUrl(){
	var url = "";
	var filtros = Array("id_tipo_org","id_sector","id_enfoque","id_poblacion");

	for(f=0;f<filtros.length;f++){
		ele = document.getElementById(filtros[f]);

		if (ele != null && ele.value != ""){
			url += "&" + filtros[f] + "=" + ele.value;
		}
	}

	return url;
}
function showMenu(opcion){

	document.getElementById('consulta').style.display = 'none';
	document.getElementById('grafica').style.display = 'none';

	document.getElementById(opcion).style.display = '';
}

</script>
<?
if (!isset($_POST["submit"]) && !isset($_POST["id_orgs"])){
  ?>

<form action="<?=$_SERVER['PHP_SELF']?>" method="POST">
<table align='center' cellspacing="0" cellpadding="0" border="0" width="940">
	<tr>
		<td>
			<table cellpadding="0" cellspacing="0" border="0">
			<tr>
				<td class='td_menu_naranja' width="100"><img src="images/cnrr/flecha.gif">&nbsp;<a href="#" onclick="showMenu('consulta');return false;">Consultas</a></td>
				<td valign="top" width="7" class='td_menu_naranja'><img src="images/cnrr/esquina2.gif"></td>
				<td><img src="images/spacer.gif"></td>
				<td class='td_menu_naranja' width="100"><img src="images/cnrr/flecha.gif">&nbsp;<a href="#" onclick="showMenu('grafica');return false;">Gráficas</a></td>
				<td valign="top" width="7" class='td_menu_naranja'><img src="images/cnrr/esquina2.gif"></td>
			</tr>
			</table>
		</td>
	</tr>
	<tr>
		<td id='consulta' style="display:<?=$display_consulta?>">
			<table cellpadding="0" cellspacing="0" width="940">
				<tr><td align='center' class='titulo_lista' colspan=2>CONSULTA DE ORGANIZACIONES</td></tr>
				<tr><td>&nbsp;</td></tr>
				<tr><td colspan="2"><table cellpadding="5">
				<tr><td colspan="2">Puede realizar una <b>búsqueda rápida</b> digitando el nombre o la sigla en el siguiente campo:<td></tr>
				<tr><td><img src="images/consulta/busqueda_rapida.gif"></td><td>
					<select id='case' class='select'>
						<option value='nombre'>Nombre</option>
						<option value='sigla'>Sigla</option>
					</select>&nbsp;&nbsp;
					<input type="text" id='s' name='s' class='textfield' size="35" onkeydown="document.getElementById('mapa_flash').src='';document.getElementById('ocurrenciasOrg').style.display='';getDataV1('ocurrenciasOrg','admin/ajax_data.php?object=ocurrenciasOrg&case='+document.getElementById('case').options[document.getElementById('case').selectedIndex].value+'&s='+this.value+'&busqueda=1','ocurrenciasOrg')">
					&nbsp;&nbsp;<input type='radio' id="comience" name="donde" value='comience' checked>Que <b>comience</b> con&nbsp;<input type='radio' id="contenga" name="donde" value='contenga'>Que <b>contenga</b> a
					</td></tr>
			  		<tr>
			  			<td colspan="2">
			  				<div id='ocurrenciasOrg' class='ocurrenciasOrg' style='display:none'></div>
			  		</td></tr></table>
				</td></tr>
				<tr><td colspan=2 class="td_dotted_top">&nbsp;</td></tr>
				<tr><td colspan=2>
					Puede consultar Organizaciones usando uno o varios de los siguiente filtros. Haga click sobre el título para desplegarlo.<br><br>
					En cada opción puede seleccionar uno o varios criterios usando la tecla Ctrl y el click izquierdo del mouse.
					<br>&nbsp;
				</td></tr>
				<tr>
					<td valign="top">
						<table border="0" class="tabla_consulta" cellspacing="1" cellpadding="5" width="240">
							<tr class='titulo_lista'><td><b>Filtros de Consulta</b></td></tr>
							<tr>
								<td>
									<img src="images/consulta/clean.gif">&nbsp;&nbsp;<a href="#" onclick="limpiarFiltros();">Limpiar</a>
									&nbsp;&nbsp;<img src="images/consulta/expandir.gif">&nbsp;&nbsp;<a href="#" onclick="mostrarFiltros('mostrar');">Mostrar</a> | <a href="#" onclick="mostrarFiltros('ocultar');">Ocultar</a>
								</td>
							</tr>
							<tr><td><img id="img_tipo_org" src="images/flecha.gif">&nbsp;&nbsp;<a href="#" onclick="unHide('td_tipo_org','img_tipo_org');return false;"><b>Filtrar por Tipo de Organización</b></a></td></tr>
							<?
							$display = 'none';
							$id_tipo_org = 0;
							if (isset($_GET['id_tipo_org'])){
								$display = '';
								$id_tipo_org = $_GET['id_tipo_org'];
							}
							?>
							<tr>
								<td id="td_tipo_org" style='display:<?=$display?>'>&nbsp;&nbsp;&nbsp;&nbsp;
									<select id="id_tipo_org" name="id_tipo_org[]" multiple size="8" class="select">
										<? $tipo_org->ListarCombo('',$id_tipo_org,'CNRR = 1'); ?>
									</select>
								</td>
							</tr>
							<tr><td><img id="img_sector" src="images/flecha.gif">&nbsp;&nbsp;<a href="#" onclick="unHide('td_sector','img_sector');return false;"><b>Filtrar por Sector</b></a></td></tr>
							<?
							$display = 'none';
							$id_sector = 0;
							if (isset($_GET['id_sector'])){
								$display = '';
								$id_sector = $_GET['id_sector'];
							}
							?>
							<tr>
								<td id="td_sector" style='display:<?=$display?>'>&nbsp;&nbsp;&nbsp;&nbsp;
									<select id="id_sector" name="id_sector[]" multiple size="12" class="select">
										<? $sector->ListarCombo('',$id_sector,'CNRR = 1'); ?>
									</select>
								</td>
							</tr>
							<tr><td><img id="img_enfoque" src="images/flecha.gif">&nbsp;&nbsp;<a href="#" onclick="unHide('td_enfoque','img_enfoque');return false;"><b>Filtrar por Enfoque</b></a></td></tr>
							<?
							$display = 'none';
							$id_enfoque = 0;
							if (isset($_GET['id_enfoque'])){
								$display = '';
								$id_enfoque = $_GET['id_enfoque'];
							}
							?>
							<tr>
								<td id="td_enfoque" style='display:<?=$display?>'>&nbsp;&nbsp;&nbsp;&nbsp;
									<select id="id_enfoque" name="id_enfoque[]" multiple size="5" class="select">
										<? $enfoque->ListarCombo('',$id_enfoque,'CNRR = 1'); ?>
									</select>
								</td>
							</tr>
							<tr><td><img id="img_poblacion" src="images/flecha.gif">&nbsp;&nbsp;<a href="#" onclick="unHide('td_poblacion','img_poblacion');return false;"><b>Filtrar por Población Objetivo</b></a></td></tr>
							<?
							$display = 'none';
							$id_poblacion = 0;
							if (isset($_GET['id_poblacion'])){
								$display = '';
								$id_poblacion = $_GET['id_poblacion'];
							}
							?>
							<tr>
								<td id="td_poblacion" style='display:<?=$display?>'>&nbsp;&nbsp;&nbsp;&nbsp;
									<select id="id_poblacion" name="id_poblacion[]" multiple size="12" class="select">
										<? $poblacion->ListarCombo('',$id_poblacion,'CNRR = 1'); ?>
									</select>
								</td>
							</tr>
						</table><br><br><div align="center">
								<input type="hidden" name="accion" value="consultar" />
								<input type="hidden" name="sede" value="1" />
								<input type="hidden" name="cobertura" value="1" />
								<input type="hidden" id="id_depto" name="id_depto[]">
								<input type="hidden" id="id_muns" name="id_muns[]" disabled>
								<input type="hidden" id="todas" name="todas" value="0" />
								<input type="submit" name="submit" value="Consultar Organizaciones" onclick="return validar_criterios()" class="boton" />
								</span>
					</td>
					<!-- MAPA -->
					<td valign="top" align="right">
						<table cellspacing="1" cellpadding="5" border="0" width="690" class="tabla_consulta">
							<tr class="titulo_lista"><td><b>Seleccione la Ubicaci&oacute;n</b></td></tr>
							<tr>
							  <td><iframe id='mapa_flash' src="consulta/swf/mapa_i_cnrr.html" height="545" width="690" frameborder="0"></iframe></td>
							</tr>
						</table>
					</td>
				</tr>
			</table>
		</td>
		<td id='grafica' style="display:<?=$display_grafica?>">
			<table cellpadding="0" cellspacing="0" width="940">
				<tr><td align='center' class='titulo_lista' colspan=2>GRAFICAS DE ORGANIZACIONES</td></tr>
				<tr><td>&nbsp;</td></tr>
				<tr><td><img src="images/cnrr/flecha.gif">&nbsp;<a href="index_cnrr.php?method=graficaConteo">Gráfica por Tipo, Población, Enfoque o Sector para una Ubicación</a></td></tr>
				<tr><td><img src="images/cnrr/flecha.gif">&nbsp;<a href="index_cnrr.php?method=graficaConteoDeptoMpio">Gráfica por Departamento o Municipio para un Tipo, Población, Enfoque o Sector</a></td></tr>
				<tr><td>&nbsp;</td></tr>
				<tr>
					<td>
						<?
						if ($method != "")	include('grafica_org_cnrr.php');
						?>
					</td>
				</tr>
			</table>
		</td>
	</tr>

</table>
</form>
<?
}

if (isset($_POST["id_orgs"]) && !isset($_POST["reportar"])){

	if ($_POST['pdf'] == 1){
		//echo "<form action='admin/reporte_pdf.php' method='POST' target='_blank'>";
		echo "<form action='".$_SERVER['PHP_SELF']."?m_e=org&accion=consultar&class=OrganizacionDAO' method='POST'>";
		$t = "PDF";
	}
	else{
		echo "<form action='".$_SERVER['PHP_SELF']."?m_e=org&accion=consultar&class=OrganizacionDAO' method='POST'>";
		$t = "CSV (Excel)";
	}
    ?>
	<table align='center' cellspacing="1" cellpadding="3" border="0" width="750">
		<tr><td align='center' class='titulo_lista' colspan=2>REPORTAR ORGANIZACIONES EN FORMATO <?=$t?></td></tr>
		<tr><td>&nbsp;</td></tr>
		<tr><td colspan=2>
			Seleccione el formato del reporte:<br>&nbsp;
		</td></tr>
		<tr><td><input type="radio" name="basico" value="1" checked>&nbsp;<b>Reporte Básico</b>: Muestra los datos básico de la Organización (Nombre,Sigla,Tipo,Cobertura)</td></tr>
		<tr><td><input type="radio" name="basico" value="2">&nbsp;<b>Reporte Detallado</b>: Muestra toda la información de la Organización</td></tr>
		<tr><td>&nbsp;</td></tr>
		<tr><td align='center'>
			<input type="hidden" name="pdf" value="<?=$_POST['pdf']?>" />
			<input type="hidden" name="id_orgs" value="<?=$_POST['id_orgs']?>" />
			<input type="hidden" name="class" value="OrganizacionDAO" />
			<input type="hidden" name="method" value="ReporteOrganizacion" />
			<input type="submit" name="reportar" value="Siguiente" class="boton" />
		</td></tr>
	</table>
	</form>
<?
}

?>