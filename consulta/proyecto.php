<?
//LIBRERIAS
include_once("consulta/lib/libs_proyecto.php");

//INICIALIZACION DE VARIABLES
$proyecto = New ProyectoDAO();
$depto_dao = New DeptoDAO();
$municipio_vo = New Municipio();
$municipio_dao = New MunicipioDAO();
$sector = New SectorDAO();
$poblacion = New PoblacionDAO();
$org = New OrganizacionDAO();
$estado= New EstadoProyectoDAO();
$tema= New TemaDAO();
$calendar = new DHTML_Calendar('admin/js/calendar/','es', 'calendar-win2k-1', false);
$calendar->load_files();

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
  $proyecto->Reportar();
}

//REPORTE EXCEL
if (isset($_POST["reportar"])){
  $proyecto->ReporteProyecto($_POST['id_proyectos'],$_POST['pdf'],$_POST['basico']);
}
//VER DETALLES
if (isset($_GET["method"]) && $_GET["method"] == "Ver"){
  $proyecto->Ver($_GET["param"]);
  die;
}

?>

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

  	var filtros = Array('id_estado','id_sector','id_tema','id_poblacion','id_depto');

  	for (f=0;f<filtros.length;f++){
	  	if (validarComboM(document.getElementById(filtros[f]))){
		    error = 0;
		}
	}

	if (document.getElementById('f-calendar-field-1').value != "" && document.getElementById('f-calendar-field-2').value != ""){
	    error = 0;
	}

	if (document.getElementById('id_orgs').value != ""){
	    error = 0;
	}

  	if (error == 1){
	    alert("Seleccione algún criterio");
	    return false;
	}
	else{
	  return true;
	}

}

function limpiarFiltros(){

  	var filtros = Array('id_estado','id_sector','id_tema','id_poblacion','id_depto');

  	for (f=0;f<filtros.length;f++){
	    ob = document.getElementById(filtros[f]);
	    for (var i = 0; i < ob.options.length; i++){
		   ob.options[ i ].selected = false;
		}
	}

	document.getElementById('f-calendar-field-1').value = "";
	document.getElementById('f-calendar-field-2').value = "";
	document.getElementById('id_orgs').value = "";

	//ORGS
	var num_options = document.getElementById('donantes').options.length;
	for(o=0;o<num_options;o++){
		document.getElementById('donantes').options[o] = null;
	}

	alert('Se limpiaron los filtros.');
}

function mostrarFiltros(accion){
  	var filtros = Array('estado','sector','tema','poblacion','fecha','cobertura');

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
	var filtros = Array("id_estado","id_sector","id_tema","id_poblacion");

	for(f=0;f<filtros.length;f++){
		ele = document.getElementById(filtros[f]);

		if (ele != null && ele.value != ""){
			url += "&" + filtros[f] + "=" + ele.value;
		}
	}

	return url;
}
</script>
<?
if (!isset($_POST["submit"]) && !isset($_POST["id_proyectos"])){
  ?>

<form action="index.php?m_e=proyecto&accion=consultar&class=ProyectoDAO" method="POST">
<table align='center' cellspacing="1" cellpadding="3" border="0">
	<tr><td align='center' class='titulo_lista' colspan=2>CONSULTA DE PROYECTOS</td></tr>
	<tr><td>&nbsp;</td></tr>
	<tr><td colspan=2>
		Puede consultar Proyectos usando uno o varios de los siguiente filtros. Haga click sobre el título para desplegarlo.<br><br>
		En cada opción puede seleccionar uno o varios criterios usando la tecla Ctrl y el click izquierdo del mouse.
		<br>&nbsp;
	</td></tr>
	<tr><td><img src="images/consulta/clean.gif">&nbsp;&nbsp;<a href="#" onclick="limpiarFiltros();">Limpiar Filtros</a>&nbsp;&nbsp;<img src="images/consulta/expandir.gif">&nbsp;&nbsp;<a href="#" onclick="mostrarFiltros('mostrar');">Mostrar</a> | <a href="#" onclick="mostrarFiltros('ocultar');">Ocultar</a> Todos los Filtros</a></td></tr>
	<tr><td>&nbsp;</td></tr>
	<!--<tr><td><img id="img_todas" src="images/flecha.gif">&nbsp;&nbsp;<a href="#" onclick="unHide('td_todas','img_todas');return false;"><b>Listar todas los Proyectos</b></a></td></tr>
	<tr>
		<td id="td_todas" style='display:none'>
		    &nbsp;&nbsp;&nbsp;&nbsp;<input type="radio" id="todas" name="todas" value="NOM_ORG">Organizadas por <b>nombre</b><br>
		    &nbsp;&nbsp;&nbsp;&nbsp;<input type="radio" id="todas" name="todas" value="SIG_ORG">Organizadas por <b>sigla</b><br>
		    &nbsp;&nbsp;&nbsp;&nbsp;<input type="radio" id="todas" name="todas" value="ID_TIPO_ORG">Organizadas por <b>tipo</b><br>
		</td>
	</tr>-->
	<tr><td><img id="img_estado" src="images/flecha.gif">&nbsp;&nbsp;<a href="#" onclick="unHide('td_estado','img_estado');return false;"><b>Filtrar por Estado de Proyecto</b></a></td></tr>
	<?
	$display = 'none';
	$id_estado = 0;
	if (isset($_GET['id_estado'])){
	  	$display = '';
	  	$id_estado = $_GET['id_estado'];
	}
	?>
	<tr>
		<td id="td_estado" style='display:<?=$display?>'>&nbsp;&nbsp;&nbsp;&nbsp;
			<select id="id_estado" name="id_estado[]" multiple size="3" class="select">
				<? $estado->ListarCombo('',$id_estado,''); ?>
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
				<? $sector->ListarCombo('',$id_sector,''); ?>
			</select>
		</td>
	</tr>
	<tr><td><img id="img_tema" src="images/flecha.gif">&nbsp;&nbsp;<a href="#" onclick="unHide('td_tema','img_tema');return false;"><b>Filtrar por Tema</b></a></td></tr>
	<?
	$display = 'none';
	$id_tema = 0;
	if (isset($_GET['id_tema'])){
	  	$display = '';
	  	$id_tema = $_GET['id_tema'];
	}
	?>
	<tr>
		<td id="td_tema" style='display:<?=$display?>'>&nbsp;&nbsp;&nbsp;&nbsp;
			<select id="id_tema" name="id_tema[]" multiple size="5" class="select">
				<? $tema->ListarCombo('',$id_tema,''); ?>
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
			<select id="id_poblacion" name="id_poblacion[]" multiple size="12" class="select" style="width:400px">
				<? $poblacion->ListarCombo('',$id_poblacion,''); ?>
			</select>
		</td>
	</tr>
	<tr><td><img id="img_fecha" src="images/flecha.gif">&nbsp;&nbsp;<a href="#" onclick="unHide('td_fecha','img_fecha')"><b>Filtrar por Fecha</b></a></td></tr>
	<tr>
		<td id="td_fecha" style='display:none'>&nbsp;&nbsp;&nbsp;&nbsp;
			Fecha de inicio&nbsp;
			<? $calendar->make_input_field(
			// calendar options go here; see the documentation and/or calendar-setup.js
			array('firstDay'       => 1, // show Monday first
			     'ifFormat'       => '%Y-%m-%d',
			     'timeFormat'     => '12'),
			// field attributes go here
			array('class'       => 'textfield',
			     'name'        => 'f_ini'));

			?>&nbsp;&nbsp;&nbsp;&nbsp;
			Fecha de finalización&nbsp;
			<? $calendar->make_input_field(
			 // calendar options go here; see the documentation and/or calendar-setup.js
			 array('firstDay'       => 1, // show Monday first
			       'ifFormat'       => '%Y-%m-%d',
			       'timeFormat'     => '12'),
			 // field attributes go here
			 array('class'       => 'textfield',
			       'name'        => 'f_fin'));
			?>
	</td></tr>
	<tr><td><img id="img_org" src="images/flecha.gif">&nbsp;&nbsp;<a href="#" onclick="unHide('td_org','img_org');return false;"><b>Filtrar por Organización Ejecutora o Donante</b></a></td></tr>
	<tr>
		<td id="td_org" style='display:none'>
			&nbsp;&nbsp;&nbsp;&nbsp;<input type="radio" name="donante" value="1" checked>&nbsp;Ejecutora<input type="radio" name="donante" value="2">&nbsp;Donante
			<br><br>&nbsp;&nbsp;&nbsp;&nbsp;
			<input type="hidden" id="id_orgs" name="id_orgs" />
			<select id="donantes" name="donantes[]" multiple size="1" class="select" style="width:300px" /></select>&nbsp;
			<input type="button" name="buscar_donante" value="Buscar Org." class="boton" onclick="window.open('admin/buscar_org.php?field_hidden=id_orgs&field_text=donantes&multiple=1&combo_extra=','','top=100,left=100,width=800,height=700');" />
			&nbsp;<input type="button" name="borrar_p" value="Borrar Org." class="boton" onclick="delete_option(document.getElementById('donantes'));CopiarOpcionesCombo(document.getElementById('donantes'),document.getElementById('id_orgs'));" />
		</td>
	</tr>
	<tr><td><img id="img_cobertura" src="images/flecha.gif">&nbsp;&nbsp;<a href="#" onclick="unHide('td_cobertura','img_cobertura');return false;"><b>Filtrar por Cobertura Geográfica</b></a></td></tr>
	<?
	$display = 'none';
	if (isset($_GET['id_depto']))	$display = '';
	?>
	<tr>
		<td id="td_cobertura" style='display:<?=$display?>'>
			<table cellspacing="0" cellpadding="0" border="0" width="95%" align="right">
				<tr><td>&nbsp;</td></tr>
				<tr><td><b>Seleccione el Departamento</b></td></tr>
				<tr><td>&nbsp;</td></tr>
				<tr>
					<td>
						<select id="id_depto" name="id_depto[]"  multiple size="8" class="select">
							<?
							//DEPTO
							$depto_dao->ListarCombo('combo',$id_depto,'');
							?>
						</select>
					</td>
				</tr>
				<tr><td>&nbsp;</td></tr>
				<tr>
				  <td>
				  <a href="#" onclick="enviar_deptos('index.php?accion=consultar&class=ProyectoDAO&method=Listar' + addUrl());return false;">Listar Muncipios del Depto. para refinar la consulta</a>
				  </td>
				</tr>
				<tr><td>&nbsp;</td></tr>

				<? if (isset($_GET['id_depto'])){ ?>
				<tr><td><b>Seleccione el Municipio</b></td></tr>
				<tr><td>&nbsp;</td></tr>
				<tr>
					<td>
						<select id="id_muns" name="id_muns[]" multiple size="8" class="select">
							<?
							//MUNICIPIO
							for($d=0;$d<$num_deptos;$d++){
							  $id = "'".$id_depto[$d]."'";
								$depto = $depto_dao->Get($id);
								$muns = $municipio_dao->GetAllArray('ID_DEPTO ='.$id);

								echo "<option value='' disabled>-------- ".$depto->nombre." --------</option>";
								foreach ($muns as $mun){
								  echo "<option value='".$mun->id."'>".$mun->nombre."</option>";
								}

							}
							?>
			  			</select>
					</td>
				</tr>
				<tr>
			    <? } ?>

			</table>
		</td>
	</tr>
	<tr><td>&nbsp;</td></tr>
	<tr><td>&nbsp;</td></tr>
	<tr><td align='center'>
		<input type="hidden" name="accion" value="consultar" />
		<input type="submit" name="submit" value="Consultar Proyectos" onclick="return validar_criterios()" class="boton" />
	</td></tr>
	</table>
</form>
<?
}

if (isset($_POST["id_proyectos"]) && !isset($_POST["reportar"])){

	if ($_POST['pdf'] == 1){
		//echo "<form action='admin/reporte_pdf.php' method='POST' target='_blank'>";
		echo "<form action='index.php?m_e=proyecto&accion=consultar&class=ProyectoDAO' method='POST'>";
		$t = "PDF";
	}
	else{
		echo "<form action='index.php?m_e=proyecto&accion=consultar&class=ProyectoDAO' method='POST'>";
		$t = "CSV (Excel)";
	}
    ?>
	<table align='center' cellspacing="1" cellpadding="3" border="0">
		<tr><td align='center' class='titulo_lista' colspan=2>REPORTAR PROYECTOS EN FORMATO <?=$t?></td></tr>
		<tr><td>&nbsp;</td></tr>
		<tr><td colspan=2>
			Seleccione el formato del reporte:<br>&nbsp;
		</td></tr>
		<tr><td><input type="radio" name="basico" value="1" checked>&nbsp;<b>Reporte Básico</b>: Muestra los datos básico de la Proyecto (Nombre,Estado,Objetivo,Fecha)</td></tr>
		<tr><td><input type="radio" name="basico" value="2">&nbsp;<b>Reporte Detallado</b>: Muestra toda la información del Proyecto</td></tr>
		<tr><td>&nbsp;</td></tr>
		<tr><td align='center'>
			<input type="hidden" name="pdf" value="<?=$_POST['pdf']?>" />
			<input type="hidden" name="id_proyectos" value="<?=$_POST['id_proyectos']?>" />
			<input type="hidden" name="class" value="ProyectoDAO" />
			<input type="hidden" name="method" value="ReporteProyecto" />
			<input type="submit" name="reportar" value="Siguiente" class="boton" />
		</td></tr>
	</table>
	</form>
<?
  }
?>