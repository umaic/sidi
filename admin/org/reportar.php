<script>
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
		getDataV1('comboBoxMunicipio','ajax_data.php?object=comboBoxMunicipio&multiple=21&id_deptos='+id_deptos,'comboBoxMunicipio')
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
		if(confirm("No hay filtros, desea reportar todas las Organizaciones?")){
			document.getElementById('todas').value = 1;
		}
		else	return false;
	}
	else{
		return true;
	}

}
</script>
<div id="div_bcg" class="div_bcg"></div>
<div id="mapa" class="div_mapa">
	<table id="table_mapa" cellspacing='0' cellpadding='5' class='div_mapa'>
		<tr class='bcg_0000000_FFFFFF'>
			<td><font class='texto_FFCC00_12'>Filtro: Ubicación Geográfica</font></td>
			<td align='right'><a href='#' onclick="showDiv('mapa','ocultar'); return false;">X</a></td></tr>
		</tr>
		<tr>
			<td>
				<object classid="clsid:d27cdb6e-ae6d-11cf-96b8-444553540000" codebase="http://fpdownload.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=8,0,0,0" width="700" height="570" id="gral" align="middle">
				<param name="allowScriptAccess" value="sameDomain" />
				<param name="movie" value="../consulta/swf/mapa_i.swf" />
				<param name="quality" value="high" />
				<param name="bgcolor" value="#ffffff" />
				<embed src="../consulta/swf/mapa_i.swf" width="700" height="570" align="middle" quality="high" bgcolor="#ffffff" name="gral" allowScriptAccess="sameDomain" type="application/x-shockwave-flash" pluginspage="http://www.macromedia.com/go/getflashplayer" />
				</object>
			</td>

		</tr>
	</table>
</div>

<?
//INICIALIZACION DE VARIABLES
$org_dao = New OrganizacionDAO();
$tipo_org = New TipoOrganizacionDAO();
$sector = New SectorDAO();
$enfoque = New EnfoqueDAO();
$poblacion = New PoblacionDAO();
$depto_dao = New DeptoDAO();
$reporte = 1;

if (isset($_GET["reporte"])){
	$reporte = $_GET["reporte"];
}

//SUBMIT
if (isset($_POST["accion"])){
	$accion = $_POST["accion"];
	$reporte = $_POST["reporte"];
	
	//CONTEO
	if ($reporte == 1){
		$depto = 1;
		$mpio = $_POST["mpio"];
		$cual = $_POST["cual"];
		if ($mpio == 1)	$depto = 0;
	
		$filtros = array('depto' => $depto,
						 'mpio' => $mpio,
						 'cual' => $cual);
	}
	//REPORTE-DIRECTORIO
	else if ($reporte == 2){
	
		$filtros = array();
	}
					 
	$org_dao->ReporteAdmin($reporte,$filtros);
	die;
}




?>

<form action="index.php?accion=reportar_admin" method="POST">
<table cellspacing="1" cellpadding="5" class="tabla_consulta" width="850" align="center">
	<tr><td align='center' class='titulo_lista'>REPORTES ADMINISTRATIVOS DE ORGANIZACIONES</td></tr>
	<tr><td>&nbsp;</td></tr>

<?php
switch ($reporte){
	//Listado de Reportes
	case 1:
		?>
		<tr>
			<td>
				<table cellspacing="1" cellpadding="5" width="700">
					<tr><td class='titulo_lista'>1. Conteo de Organizaciones</td></tr>
					<tr><td>Reporta el número de organizaciones por municipio de cada Tipo, Sector, Enfoque y Población</td></tr>
					<tr>
						<td>
							<b>Reportar por</b>&nbsp;
							<select name="mpio" class="select">
								<option value=0>Depto</option>
								<option value=1>Mpio</option>
							</select>&nbsp;&nbsp;
							<b>Discriminar por</b>&nbsp;
							<select name="cual" class="select">
								<option value="tipo">Tipo</option>
								<option value="sector">Sector</option>
								<option value="enfoque">Enfoque</option>
								<option value="poblacion">Población</option>
							</select>&nbsp;

						</td>
					</tr>
					<tr><td><img id="img_tipo_org" src="../images/flecha.gif">&nbsp;&nbsp;<a href="#" onclick="unHide('td_tipo_org','img_tipo_org');return false;"><b>Filtrar por Tipo de Organización</b></a></td></tr>
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
								<? $tipo_org->ListarCombo('',$id_tipo_org,''); ?>
							</select>
						</td>
					</tr>
					<tr><td><img id="img_sector" src="../images/flecha.gif">&nbsp;&nbsp;<a href="#" onclick="unHide('td_sector','img_sector');return false;"><b>Filtrar por Sector</b></a></td></tr>
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
								<? $sector->ListarCombo('',$id_sector,''); ?>
							</select>
						</td>
					</tr>
					<tr><td><img id="img_enfoque" src="../images/flecha.gif">&nbsp;&nbsp;<a href="#" onclick="unHide('td_enfoque','img_enfoque');return false;"><b>Filtrar por Enfoque</b></a></td></tr>
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
								<? $enfoque->ListarCombo('',$id_enfoque,''); ?>
							</select>
						</td>
					</tr>
					<tr><td><img id="img_poblacion" src="../images/flecha.gif">&nbsp;&nbsp;<a href="#" onclick="unHide('td_poblacion','img_poblacion');return false;"><b>Filtrar por Población Objetivo</b></a></td></tr>
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
							<select id="id_poblacion" name="id_poblacion[]" multiple size="12" class="select" style="width:400px">
								<? $poblacion->ListarCombo('',$id_poblacion,''); ?>
							</select>
						</td>
					</tr>
					<!--<tr><td><img id="img_cobertura" src="../images/flecha.gif">&nbsp;&nbsp;<a href="#" onclick="unHide('td_cobertura','img_cobertura');return false;"><b>Filtrar por Ubicación Geográfica</b></a></td></tr>-->
					<tr>
						<td>
							<img id="img_cobertura" src="../images/flecha.gif">&nbsp;&nbsp;<a href="javascript:showDiv('mapa','mostrar');"><b>Filtrar por Ubicación Geográfica</b></a>
							<span id="texto_ubicacion">Cargando Flash....</span>
						</td>
					</tr>
					<tr><td><img src="../images/flecha.gif">&nbsp;&nbsp;CNRR&nbsp;<input type="radio" name="cnrr" value=0 >No&nbsp;<input type="radio" name="cnrr" value=1 >Si</td></tr>
					<tr><td><img src="../images/flecha.gif">&nbsp;&nbsp;Consulta Social&nbsp;<input type="radio" name="consulta_social" value=0>No&nbsp;<input type="radio" name="consulta_social" value=1>Si</td></tr>
					<tr><td>&nbsp;</td></tr>
					<input type="hidden" name="accion" value="reportar_admin">
					<input type="hidden" name="reporte" value="1">
					<input type="hidden" name="sede" value="1" />
					<input type="hidden" name="cobertura" value="1" />
					<input type="hidden" id="id_depto" name="id_depto[]" disabled>
					<input type="hidden" id="id_muns" name="id_muns[]" disabled>
					<tr>
						<td align="center">
							<img src="../images/ir_reporte.gif">&nbsp;<a href="#" onclick="document.forms[0].submit();return false;">Reportar</a>
							&nbsp;<input type="radio" name="format" value='html'>Ver tabla detallada &nbsp;<input type="radio" name="format" value='csv' checked>Generar archivo
						</td>
					</tr>
				</table>
			</td>
		</tr>
		<?php
	break;
	 //Listado-Directorio
	case 2:
		?>
		<tr><td class='titulo_lista'>2. Listo de Organizaciones por Sector, Enfoque y Cobertura</td></tr>
		<tr><td>Genera un listado de las organizaciones ordenadas alfab&eacute;ticamente</td></tr>
		<tr><td id='table_filtros'><table>
			<tr>
				<td valign="top">
					<table cellspacing="0">
						<tr>
							<td class="titulo_filtro" width="220">&nbsp;<img src="../images/gra_resumen/fl_filtro.gif">&nbsp;Tipo</td>
							<td rowspan="5"><img src="images/spacer.gif" width="15"></td>
							<td class="titulo_filtro" width="220">&nbsp;<img src="../images/gra_resumen/fl_filtro.gif">&nbsp;Sector</td>
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
							<td class="titulo_filtro">&nbsp;<img src="../images/gra_resumen/fl_filtro.gif">&nbsp;Enfoque</td>
							<td class="titulo_filtro">&nbsp;<img src="../images/gra_resumen/fl_filtro.gif">&nbsp;Poblaci&oacute;n</td>
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
<!--						 <tr><td>Sede&nbsp;<input type="checkbox" name="sede" checked>&nbsp;Cobertura&nbsp;<input type="checkbox" name="cobertura"></td></tr>-->
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
											<a href="#" onclick="listarMunicipios('id_depto');return false;">Listar Muncipios</a>
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
			<tr>
				<td align="center" colspan="2">
					<input type="hidden" name="accion" value="reportar_admin">
					<input type="hidden" name="reporte" value="2">
					<input type="submit" value="Reportar" class="boton" onclick="return validar_criterios();">
					<input type="hidden" id="todas" name="todas" value="0" />
				</td>
			</tr>
		</table>
		<?
	break;
}
	?>
</table>
</form>


