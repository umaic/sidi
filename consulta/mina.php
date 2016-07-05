<?
//LIBRERIAS
include_once("consulta/lib/libs_mina.php");

//INICIALIZACION DE VARIABLES
$mina = New MinaDAO();
$depto_dao = New DeptoDAO();
$municipio_vo = New Municipio();
$municipio_dao = New MunicipioDAO();
$condicion_dao = New CondicionMinaDAO();
$estado_dao = New EstadoMinaDAO();
$sexo_dao = New SexoDAO();
$edad_dao = New EdadDAO();
$tipo_eve_dao = New TipoEventoDAO();
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
  $mina->Reportar();
}

//REPORTE EXCEL
if (isset($_POST["reportar"])){
  $mina->ReporteMina($_POST['id_minas'],$_POST['pdf'],$_POST['basico']);
}
//VER DETALLES
if (isset($_GET["method"]) && $_GET["method"] == "Ver"){
  $mina->Ver($_GET["param"]);
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

  	var filtros = Array('id_tipo','id_condicion','id_estado','id_edad','id_sexo','id_depto');

  	for (f=0;f<filtros.length;f++){
	  	if (validarComboM(document.getElementById(filtros[f]))){
		    error = 0;
		}
	}

	if (document.getElementById('f-calendar-field-1').value != "" && document.getElementById('f-calendar-field-2').value != ""){
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

  	var filtros = Array('id_tipo','id_condicion','id_estado','id_edad','id_sexo','id_depto');

  	for (f=0;f<filtros.length;f++){
	    ob = document.getElementById(filtros[f]);
	    for (var i = 0; i < ob.options.length; i++){
		   ob.options[ i ].selected = false;
		}
	}

	alert('Se limpiaron los filtros.');
}

function mostrarFiltros(accion){
  	var filtros = Array('tipo','condicion','estado','edad','sexo','cobertura');

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
function mostrarMun(dato_para){
  if (dato_para == 2){
      if (document.getElementById('link_mun')){
	      document.getElementById('link_mun').style.display = '';
	  }
  }
}
function addUrl(){
	var url = "";
	var filtros = Array("id_tipo","id_condicion","id_estado","id_edad","id_sexo");

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
if (!isset($_POST["submit"]) && !isset($_POST["id_minas"])){
  ?>

<form action="index.php?m_e=mina&accion=consultar&class=MinaDAO" method="POST">
<table align='center' cellspacing="1" cellpadding="3" border="0" width="750">
	<tr><td align='center' class='titulo_lista' colspan=2>CONSULTA DE DATOS DE EVENTOS POR MINAS</td></tr>
	<tr><td>&nbsp;</td></tr>
	<tr><td colspan=2>
		Puede consultar Datos de Eventos por Minas usando uno o varios de los siguiente filtros. Haga click sobre el título para desplegarlo.<br><br>
		En cada opción puede seleccionar uno o varios criterios usando la tecla Ctrl y el click izquierdo del mouse.
		<br>&nbsp;
	</td></tr>
	<tr><td><img src="images/consulta/clean.gif">&nbsp;&nbsp;<a href="#" onclick="limpiarFiltros();">Limpiar Filtros</a>&nbsp;&nbsp;<img src="images/consulta/expandir.gif">&nbsp;&nbsp;<a href="#" onclick="mostrarFiltros('mostrar');">Mostrar</a> | <a href="#" onclick="mostrarFiltros('ocultar');">Ocultar</a> Todos los Filtros</a></td></tr>
	<tr><td>&nbsp;</td></tr>
	<tr><td><b>Especifique el rango de fecha de la consulta de eventos, o deje los campos vacíos para no aplicar este filtro</b></td></tr>
	<tr>
		<td id="td_fecha">
			Fecha desde&nbsp;
			<? $calendar->make_input_field(
			// calendar options go here; see the documentation and/or calendar-setup.js
			array('firstDay'       => 1, // show Monday first
			     'ifFormat'       => '%Y-%m-%d',
			     'timeFormat'     => '12'),
			// field attributes go here
			array('class'       => 'textfield',
			      'name'        => 'f_ini'));

			?>&nbsp;&nbsp;&nbsp;&nbsp;
			Fecha hasta&nbsp;
			<? $calendar->make_input_field(
			 // calendar options go here; see the documentation and/or calendar-setup.js
			 array('firstDay'       => 1, // show Monday first
			       'ifFormat'       => '%Y-%m-%d',
			       'timeFormat'     => '12'),
			 // field attributes go here
			 array('class'       => 'textfield',
			       'name'        => 'f_fin'));
			?>
		</td>
	</tr>
	<tr><td>&nbsp;</td></tr>
	<tr><td><img id="img_tipo" src="images/flecha.gif">&nbsp;&nbsp;<a href="#" onclick="unHide('td_tipo','img_tipo');return false;"><b>Filtrar por Tipo de Evento</b></a></td></tr>
	<?
	$display = 'none';
	$id_tipo = 0;
	if (isset($_GET['id_tipo'])){
	  	$display = '';
	  	$id_tipo = $_GET['id_tipo'];
	}
	?>
	<tr>
		<td id="td_tipo" style='display:<?=$display?>'>&nbsp;&nbsp;&nbsp;&nbsp;
			<select id="id_tipo" name="id_tipo[]" multiple size="2" class="select">
				<? $tipo_eve_dao->ListarCombo('combo',$id_tipo,'ID_TIPO_EVE IN (107,109)'); ?>
			</select>
		</td>
	</tr>
	<tr><td><img id="img_condicion" src="images/flecha.gif">&nbsp;&nbsp;<a href="#" onclick="unHide('td_condicion','img_condicion');return false;"><b>Filtrar por Condición</b></a></td></tr>
	<?
	$display = 'none';
	$id_condicion = 0;
	if (isset($_GET['id_condicion'])){
	  	$display = '';
	  	$id_condicion = $_GET['id_condicion'];
	}
	?>
	<tr>
		<td id="td_condicion" style='display:<?=$display?>'>&nbsp;&nbsp;&nbsp;&nbsp;
			<select id="id_condicion" name="id_condicion[]" multiple size="4" class="select">
				<? $condicion_dao->ListarCombo('combo',$id_condicion,''); ?>
			</select>
		</td>
	</tr>
	<tr><td><img id="img_estado" src="images/flecha.gif">&nbsp;&nbsp;<a href="#" onclick="unHide('td_estado','img_estado');return false;"><b>Filtrar por Estado</b></a></td></tr>
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
			<select id="id_estado" name="id_estado[]" multiple size="2" class="select">
				<? $estado_dao->ListarCombo('combo',$id_estado,''); ?>
			</select>
		</td>
	</tr>
	<tr><td><img id="img_edad" src="images/flecha.gif">&nbsp;&nbsp;<a href="#" onclick="unHide('td_edad','img_edad');return false;"><b>Filtrar por Edad</b></a></td></tr>
	<?
	$display = 'none';
	$id_edad = 0;
	if (isset($_GET['id_edad'])){
	  	$display = '';
	  	$id_edad = $_GET['id_edad'];
	}
	?>
	<tr>
		<td id="td_edad" style='display:<?=$display?>'>&nbsp;&nbsp;&nbsp;&nbsp;
			<select id="id_edad" name="id_edad[]" multiple size="3" class="select">
				<? $edad_dao->ListarCombo('combo',$id_edad,''); ?>
			</select>
		</td>
	</tr>
	<tr><td><img id="img_sexo" src="images/flecha.gif">&nbsp;&nbsp;<a href="#" onclick="unHide('td_sexo','img_sexo');return false;"><b>Filtrar por Sexo</b></a></td></tr>
	<?
	$display = 'none';
	$id_sexo = 0;
	if (isset($_GET['id_sexo'])){
	  	$display = '';
	  	$id_sexo = $_GET['id_sexo'];
	}
	?>
	<tr>
		<td id="td_sexo" style='display:<?=$display?>'>&nbsp;&nbsp;&nbsp;&nbsp;
			<select id="id_sexo" name="id_sexo[]" multiple size="2" class="select">
				<? $sexo_dao->ListarCombo('combo',$id_sexo,''); ?>
			</select>
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
				<tr><td><b>Seleccione el Departamento</b>
				</td></tr>
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
				  <td id="link_mun">
				  <a href="#" onclick="enviar_deptos('index.php?accion=consultar&class=MinaDAO&method=Listar' + addUrl());return false;">Listar Muncipios del Departamento</a>
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
		<input type="submit" name="submit" value="Consultar Datos" onclick="return validar_criterios()" class="boton" />
	</td></tr>
	</table>
</form>
<?
}

if (isset($_POST["id_minas"]) && !isset($_POST["reportar"])){

	if ($_POST['pdf'] == 1){
		//echo "<form action='admin/reporte_pdf.php' method='POST' target='_blank'>";
		echo "<form action='index.php?m_e=mina&accion=consultar&class=MinaDAO' method='POST'>";
		$t = "PDF";
	}
	else{
		echo "<form action='index.php?m_e=mina&accion=consultar&class=MinaDAO' method='POST'>";
		$t = "CSV (Excel)";
	}
    ?>
	<table align='center' cellspacing="1" cellpadding="3" border="0" width="750">
		<tr><td align='center' class='titulo_lista' colspan=2>REPORTAR EVENTOS CON MINA EN FORMATO <?=$t?></td></tr>
		<tr><td>&nbsp;</td></tr>
		<tr><td colspan=2>
			Seleccione el formato del reporte:<br>&nbsp;
		</td></tr>
		<tr><td><input type="radio" name="basico" value="1" checked>&nbsp;<b>Reporte Estándar</b>: Muestra la información del Evento con Mina</td></tr>
		<tr><td>&nbsp;</td></tr>
		<tr><td align='center'>
			<input type="hidden" name="pdf" value="<?=$_POST['pdf']?>" />
			<input type="hidden" name="dato_para" value="<?=$_POST['dato_para']?>" />
			<input type="hidden" name="id_minas" value="<?=$_POST['id_minas']?>" />
			<input type="hidden" name="class" value="MinaDAO" />
			<input type="hidden" name="method" value="ReporteMina" />
			<input type="submit" name="reportar" value="Siguiente" class="boton" />
		</td></tr>
	</table>
	</form>
<?
  }
?>