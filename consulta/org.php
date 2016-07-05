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

/*
$id_depto = Array();
$num_deptos = 0;
if (isset($_GET['id_depto'])){
	$id_depto_s = split(",",$_GET['id_depto']);
	$id_depto = $id_depto_s[0];
	$num_deptos = count($id_depto_s);
	$id_depto = $id_depto_s;
	$id_depto_url = $id_depto;
}
*/

//ACCION DE LA FORMA
if (isset($_POST["submit"]) || isset($_GET["submit"])){
	$org->Reportar();
	die();
}

//REPORTE EXCEL - PDF
if (isset($_POST["reportar"])){
	$org->ReporteOrganizacion($_POST['id_orgs'],$_POST['pdf'],$_POST['basico']);
}

//VER DETALLES
if (isset($_GET["method"]) && $_GET["method"] == "Ver"){
	$org->Ver($_GET["param"]);
	die;
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
	var filtros = Array('id_tipo_org','id_sector','id_enfoque','id_poblacion','id_depto');

	for (f=0;f<filtros.length;f++){
		if (validarComboM(document.getElementById(filtros[f]))){
			error = 0;
		}
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

}

function limpiarFiltros(){

	var filtros = Array('id_tipo_org','id_sector','id_enfoque','id_poblacion','id_depto');

	for (f=0;f<filtros.length;f++){
		ob = document.getElementById(filtros[f]);
		for (var i = 0; i < ob.options.length; i++){
			ob.options[ i ].selected = false;
		}
	}

	alert('Se limpiaron los filtros.');
}

function mostrarFiltros(accion){
	var filtros = Array('tipo_org','sector','enfoque','poblacion','cobertura');

	for (f=0;f<filtros.length;f++){
		if (accion == 'mostrar'){
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
function listarMunicipios(combo_depto){

	selected = new Array();
	ob = document.getElementById(combo_depto);
	for (var i = 0; i < ob.options.length; i++){
		if (ob.options[ i ].selected)
		selected.push(ob.options[ i ].value);
	}
	var id_deptos = selected.join(",");

	if (selected.length == 0){
		alert("Debe seleccionar algún departamento");
	}
	else{
		getDataV1('comboBoxMunicipio','admin/ajax_data.php?object=comboBoxMunicipio&multiple=21&id_deptos='+id_deptos,'comboBoxMunicipio')
	}
}
function buscarOrgs(e){

	texto = document.getElementById('s').value;
	
	keyNum = e.keyCode;
		
	if (keyNum == 8){  //Backspace
		texto = texto.slice(0, -1);  //Borra el ultimo caracter
	}
	else{
		keyChar = String.fromCharCode(keyNum);
		texto +=  keyChar;
	}
	
	if (texto.length > 1){
		document.getElementById('table_filtros').style.display='none';
		document.getElementById('ocurrenciasOrg').style.display='';
		getDataV1('ocurrenciasOrg','admin/ajax_data.php?object=ocurrenciasOrg&case='+document.getElementById('case').options[document.getElementById('case').selectedIndex].value+'&s='+texto+'&busqueda=1','ocurrenciasOrg')
	}
	
	//El valor de donde, se coloca en js/ajax.js
}
</script>
<?
if (!isset($_POST["submit"]) && !isset($_POST["id_orgs"])){
	?>
	<form action="index.php" method="POST">
	<table align='center' cellspacing="1" cellpadding="3" border="0">
		<tr class='pathway'>
			<td colspan=4>
				&nbsp;<img src='images/user-home.png'>&nbsp;<a href='index.php?m_g=consulta&m_e=home'>Home</a> &gt; <a href="index.php?m_g=consulta">Reportes</a> &gt; Organizaciones
			</td>
		</tr>
		<tr><td><table cellpadding="5">
		<tr><td colspan="2">1. Puede realizar una <b>búsqueda rápida</b> digitando el nombre o la sigla en el siguiente campo:<td></tr>
		<tr><td><img src="images/consulta/busqueda_rapida.gif"></td><td>
			<select id='case' class='select'>
				<option value='nombre'>Nombre</option>
				<option value='sigla'>Sigla</option>
			</select>&nbsp;&nbsp;
			<input type="text" id='s' name='s' class='textfield' size="35" onkeydown="buscarOrgs(event);">
			&nbsp;&nbsp;<input type='radio' id="comience" name="donde" value='comience' checked>Que <b>comience</b> con&nbsp;<input type='radio' id="contenga" name="donde" value='contenga'>Que <b>contenga</b> a
			</td></tr>
	  		<tr><td colspan="2"><div id='ocurrenciasOrg' class='ocurrenciasOrg' style="display:none"></div>
	  		</td></tr></table>
		</td></tr>
		<tr><td colspan=2 class="td_dotted_top">&nbsp;</td></tr>
		<tr><td colspan=2><p align='justify'>
			2. Puede consultar Organizaciones usando uno o varios de los siguiente filtros.
			En cada opción puede seleccionar uno o varios <br> criterios usando la tecla Ctrl y el click izquierdo del mouse.</p>
		</td></tr>
		<tr><td><img src="images/consulta/clean.gif">&nbsp;&nbsp;<a href="#" onclick="limpiarFiltros();return false;">Limpiar Filtros</a></td></tr>
		<tr><td id='table_filtros'><table>
			<tr>
				<td valign="top">
					<table cellspacing="0">
						<tr>
							<td class="titulo_filtro" width="220">&nbsp;<img src="images/gra_resumen/fl_filtro.gif">&nbsp;Tipo</td>
							<td rowspan="5"><img src="images/spacer.gif" width="15"></td>
							<td class="titulo_filtro" width="220">&nbsp;<img src="images/gra_resumen/fl_filtro.gif">&nbsp;Sector</td>
						</tr>
						<tr>
							<td id="td_tipo_org">
								<select id="id_tipo_org" name="id_tipo_org[]" multiple size="12" class="select" style="width:220px">
									<? $tipo_org->ListarCombo('',$id_tipo_org,''); ?>
								</select>
							</td>
							<td id="td_sector">
								<select id="id_sector" name="id_sector[]" multiple size="12" class="select" style="width:220px">
									<? $sector->ListarCombo('',$id_sector,''); ?>
								</select>
							</td>
							
						</tr>
						<tr><td><img src="images/spacer.gif" height="5"></td></tr>
						<tr>
							<td class="titulo_filtro">&nbsp;<img src="images/gra_resumen/fl_filtro.gif">&nbsp;Enfoque</td>
							<td class="titulo_filtro">&nbsp;<img src="images/gra_resumen/fl_filtro.gif">&nbsp;Poblaci&oacute;n</td>
						</tr>
						<tr>
							<td id="td_enfoque">
								<select id="id_enfoque" name="id_enfoque[]" multiple size="12" class="select" style="width:220px">
									<? $enfoque->ListarCombo('',$id_enfoque,''); ?>
								</select>
							</td>
							<td id="td_poblacion">
								<select id="id_poblacion" name="id_poblacion[]" multiple size="12" class="select" style="width:220px">
									<? $poblacion->ListarCombo('',$id_poblacion,''); ?>
								</select>
							</td>
						</tr>
					</table>
				</td>
				<td><img src="images/spacer.gif" width="20"></td>
				<td valign="top">
					<table width="100%" border="0">
						<tr><td colspan="2" class="titulo_filtro" width="410">&nbsp;<img src="images/gra_resumen/fl_filtro.gif">&nbsp;Ubicaci&oacute;n Geogr&aacute;fica</td></tr>
						 <tr><td>Sede&nbsp;<input type="checkbox" name="sede" checked>&nbsp;Cobertura&nbsp;<input type="checkbox" name="cobertura" checked></td></tr>
						<tr>
							<td width="200">
								<table>
									<tr>
										<td><b>Departamento</b><br>
											<select id="id_depto" name="id_depto[]"  multiple size="21" class="select">
												<?
												//DEPTO
												$depto_dao->ListarCombo('combo',$id_depto,'');
												?>
											</select><br><br>
											<img alt="" src="images/ir.png">&nbsp;<a href="#" onclick="listarMunicipios('id_depto');return false;">Listar Muncipios</a>
										</td>
									</tr>
								</table>
							</td>
							<td width="200" valign="top">
								<table>
									<tr>
										<td id="comboBoxMunicipio"></td>
									</tr>
								</table>
							</td>
						</tr>
					</table>						
				</td>
			</tr>
			</table>
			</td>
		</tr>
		<tr><td>&nbsp;</td></tr>
		<tr><td align='center'>
			<input type="hidden" name="accion" value="consultar" />
			<input type="hidden" id="todas" name="todas" value="0" />
			<input type="hidden" name="m_e" value="org" />
			<input type="hidden" name="accion" value="consultar" />
			<input type="hidden" name="class" value="OrganizacionDAO" />
			<input type="submit" name="submit" value="Consultar Organizaciones" onclick="return validar_criterios()" class="boton" />
		</td></tr>
		</table>
	</form>
	<?
}

if (isset($_POST["id_orgs"]) && !isset($_POST["reportar"])){

	if ($_POST['pdf'] == 1){
		//echo "<form action='admin/reporte_pdf.php' method='POST' target='_blank'>";
		echo "<form action='index.php?m_e=org&accion=consultar&class=OrganizacionDAO' method='POST'>";
		$t = "PDF";
	}
	else{
		echo "<form action='index.php?m_e=org&accion=consultar&class=OrganizacionDAO' method='POST'>";
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
		<? if ($_POST["todas"] == 0) { ?>
				<tr><td><input type="radio" name="basico" value="2">&nbsp;<b>Reporte Detallado</b>: Muestra toda la información de la Organización</td></tr>
		<? } ?>
		<tr><td>&nbsp;</td></tr>
		<tr><td align='center'>
			<input type="hidden" name="pdf" value="<?=$_POST['pdf']?>" />
			<input type="hidden" name="id_orgs" value="<?=$_POST['id_orgs']?>" />
			<!--<input type="hidden" name="class" value="OrganizacionDAO" />
			<input type="hidden" name="method" value="ReporteOrganizacion" />-->
			<input type="submit" name="reportar" value="Siguiente" class="boton" />
		</td></tr>
	</table>
	</form>
<?
}

?>
