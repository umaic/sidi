<?
//LIBRERIAS
include_once("consulta/lib/libs_dato_sectorial.php");

//INICIALIZACION DE VARIABLES
$dato_sectorial_dao = New DatoSectorialDAO();
$depto_dao = New DeptoDAO();
$municipio_dao = New MunicipioDAO();
$sector_dao = New SectorDAO();
$cat_dao = New CategoriaDatoSectorDAO();
$dato_dao = New DatoSectorialDAO();
$calendar = new DHTML_Calendar('admin/js/calendar/','es', 'calendar-win2k-1', false);
$calendar->load_files();
$id_cats_dato = $cat_dao->GetAllArray('');


$dato_para = 1;
$chk_dato_para = Array(""," selected ");
if (isset($_GET['dato_para']) && $_GET['dato_para'] == 2){
  $dato_para = 2;
  $chk_dato_para = Array(""," selected ");
}

//ACCION DE LA FORMA
if (isset($_POST["submit"])){
  $dato_sectorial_dao->Reportar();
  die;
}

//REPORTE EXCEL
if (isset($_POST["reportar"])){
  $dato_sectorial_dao->ReporteDatoSectorial($_POST['id_datos'],$_POST['pdf'],$_POST['basico'],$_POST['dato_para']);
}

//VER DETALLES
if (isset($_GET["method"]) && $_GET["method"] == "VerDato"){
  $tmp = split(",",$_GET["param"]);
  $dato_sectorial_dao->VerDato($tmp[0],$tmp[1],$tmp[2]);
}




?>
<script src="admin/js/ajax.js"></script>
<script type="text/javascript" src="js/filterlist.js"></script>
<script>

var num_datos_repo = 0;
var id_td_parent = "td_datos_seleccionados";


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

	if (document.getElementById("id_dato").value == ''){
		alert("Seleccione algún Dato Sectorial");
		return false;
	}
}

function limpiarFiltros(){

  	var filtros = Array('id_dato','id_depto');

  	for (f=0;f<filtros.length;f++){
  	    if (document.getElementById(filtros[f])){
		    ob = document.getElementById(filtros[f]);
		    for (var i = 0; i < ob.options.length; i++){
			   ob.options[ i ].selected = false;
			}
		}
	}

	alert('Se limpiaron los filtros.');
}

function mostrarFiltros(accion){
  	var filtros = Array('dato','cobertura');

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
		getDataV1('comboBoxMunicipio','admin/ajax_data.php?object=comboBoxMunicipio&multiple=17&id_deptos='+id_deptos,'comboBoxMunicipio')
	}
}

function getDefinicionDato(id_dato,innerobject){
	
	document.getElementById(innerobject).style.display = '';
	
	getDataV1('','admin/ajax_data.php?object=getDefinicionDatoSectorial&id_dato='+id_dato,innerobject);
}

function getPeriodosDato(id_dato,innerobject){
	
	document.getElementById(innerobject).style.display = '';
	
	if (id_dato != ""){
		getDataV1('getAniosDatoSectorial','admin/ajax_data.php?object=getPeriodosDatoSectorial&box_name=ini_fin_dato_'+num_datos_repo+'&formato=H&checked=checked&num_items_f_c=5&id_dato='+id_dato,innerobject);
	}
}

function addDatoToReport(){

	var combo_dato = document.getElementById("id_dato_sel");
	var id_dato_sel = combo_dato.value;
	var id_datos_hidden = document.getElementById("id_dato");
	var num_dato_lista_hidden = document.getElementById("num_dato_lista");
	var chk_id = 1;

	//crea el arreglo con las desagregaciones geograficas, para hacer check
	var des_geo = new Array();
	
	<?
	$datos = $dato_sectorial_dao->GetAllArray('','','');
	foreach ($datos as $vo){
		$desa = ($vo->desagreg_geo == 'departamental') ? 1 : 2;
		echo "des_geo[$vo->id] = $desa ;";	
	}
	?>

	//check de la desagregacion, valor del combo 1=deptal, 2=mpal
	var desa_combo = document.getElementById("dato_para").value;

	if (desa_combo != des_geo[id_dato_sel]){
		chk_id = 0;
		
		des_geo_txt =  (des_geo[id_dato_sel] == 1) ? 'Departamental' : 'Municipal';
		alert('El dato seleccionado solo está disponible a nivel ' + des_geo_txt);
	}

	//check que no este el dato en la lista
	var id_split = id_datos_hidden.value.split(',');
	
	for (i=0;i<id_split.length;i++){
		if (id_split[i] == id_dato_sel)	chk_id = 0;
	}
	
	if (id_dato_sel != '' && chk_id == 1){

		num_datos_repo++;		
		
		var nom_dato_sel = combo_dato.options[combo_dato.selectedIndex].text;
		var td_parent = document.getElementById(id_td_parent);
		var new_div = document.createElement('div');
		
		if (id_datos_hidden.value == ''){
			id_datos_hidden.value = id_dato_sel;
			num_dato_lista_hidden.value = num_datos_repo;
		}
		else{
			id_datos_hidden.value += ',' + id_dato_sel;
			num_dato_lista_hidden.value += ',' + num_datos_repo;
		}
			
		//oculta la info inicial
		document.getElementById("info_ini").style.display = 'none';
		
		new_div.id = 'div_dato_' + num_datos_repo;
		new_div.className = 'div_dato_sectorial_to_report';
		
		var html = '<b>Dato: </b><u>' + nom_dato_sel.toUpperCase() + '</u>&nbsp;&nbsp;[ <a href="#" onclick="return eliminarDatoToReport(\''+new_div.id+'\',\''+id_dato_sel+'\')">Eliminar del reporte</a> ]<br>';
		var id_obj_def = 'def_dato_' + num_datos_repo;
		var id_obj_per = 'per_dato_' + num_datos_repo;
		 
		html += '<b>Definici&oacute;n</b>: <span class="nota" id="' + id_obj_def + '"></span><br>';
		html += '<b>Seleccione los periodos: </b> <span class="nota" id="' + id_obj_per + '"></span>';
		
		new_div.innerHTML = html;
		
		td_parent.appendChild(new_div);
		
		setTimeout("getDefinicionDato('"+id_dato_sel+"','"+id_obj_def+"')",100);
		setTimeout("getPeriodosDato('"+id_dato_sel+"','"+id_obj_per+"')",100);
	}
	
}

function eliminarDatoToReport(id_div,id_dato){
	
	var td_parent = document.getElementById(id_td_parent);
	var div = document.getElementById(id_div);
	var id_datos_hidden = document.getElementById("id_dato");
	var num_dato_lista_hidden = document.getElementById("num_dato_lista");
	
	td_parent.removeChild(div);
	num_datos_repo--;
	
	var id_split = id_datos_hidden.value.split(',');
	var num_split = num_dato_lista_hidden.value.split(',');
	
	//Elimina el id del hidden
	var ids = "";
	var nums = "";
	for (i=0;i<id_split.length;i++){
		if (id_split[i] != id_dato){
			if (ids == ''){
				ids = id_split[i];
				nums = num_split[i];
			}
			else{
				ids += ',' + id_split[i];
				nums += ',' + num_split[i];
			}
		}	
	}
	
	id_datos_hidden.value = ids;
	num_dato_lista_hidden.value = nums;
	
	//alert(nums);
	//coloca las instrucciones iniciales si no hay datos seleccionados
	if (ids == '')	document.getElementById("info_ini").style.display = '';
	
	return false;
}
</script>
<?
if (!isset($_GET["method"])){
	if (!isset($_POST["submit"]) && !isset($_POST["id_datos"])){
	  ?>

	<form action="index.php?m_e=dato_sectorial&accion=consultar&class=DatoSectorialDAO" method="POST">
	<table align='center' cellspacing="1" cellpadding="5" border="0" width="900">
		<tr class='pathway'>
			<td colspan=4>
				&nbsp;<img src='images/user-home.png'>&nbsp;<a href='index.php?m_g=consulta&m_e=home'>Inicio</a> &gt; <a href="index.php?m_g=consulta">Reportes</a> &gt; Datos Sectoriales
			</td>
		</tr>
		<tr>
			<td class="important_evento" colspan="2">
				<table>
					<tr>
						<td><img src="images/consulta/evento/important.png"></td>
						<td>
							<b>Consultar Datos Sectoriales a nivel</b>:&nbsp;
							<select id="dato_para" name="dato_para" class="select">
								<option value=1 <?=$chk_dato_para[0]?>>Departamental</option>
								<option value=2 <?=$chk_dato_para[1]?>>Municipal</option>
							</select>
						</td>
					</tr>
				</table>
			</td>
		</tr>
		<tr><td>&nbsp;</td></tr>
		<!--		
		<tr>
			<td>
				<img src="images/consulta/clean.gif">&nbsp;&nbsp;<a href="#" onclick="limpiarFiltros();return false;">Limpiar Filtros</a>
			</td>
		</tr>
		-->
		<tr>
			<td class="titulo_filtro" width="490">&nbsp;<img src="images/gra_resumen/fl_filtro.gif">&nbsp;Datos Sectoriales en el sistema</td>
		</tr>
		<tr>
			<td>
				<select id="id_dato_sel" class="select" size="1" style="width:560px">
				<?
				foreach ($id_cats_dato as $cate){
					echo "<option value='' style='background:#CCCCCC;color:#FFFFFF;' disabled>----------- Categoria: $cate->nombre -----------</option>";
					$dato_sectorial_dao->ListarCombo('combo','','ID_CATE = '.$cate->id);
					
				}
				?>
				</select>

				<!-- Filtrar datos sectoriales -->
				<script type="text/javascript">
				var myfilter = new filterlist(document.getElementById('id_dato_sel'));
				</script>
				&nbsp;
				<input type="button" value="Agregar al reporte &#187;" class="boton" onclick="addDatoToReport()">
			</td>
		</tr>
		<tr>
			<td class='nota'>
				<b>Filtrar lista por letra inicial</b>
				&nbsp;
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
				&nbsp;
				<b>Filtrar lista por palabra</b>:&nbsp;
				<input id="regexp" name="regexp" onKeyUp="myfilter.set(this.value)" class="textfield" style="width:179px">
			</td>
		</tr>
		<tr><td>&nbsp;</td></tr>				
		<tr>
			<td class="titulo_filtro">&nbsp;<img src="images/gra_resumen/fl_filtro.gif">&nbsp;Datos seleccionados para reporte</td>
		</tr>
		<tr>
			<td style="height:100px;" id="td_datos_seleccionados" valign="top">
				<div id="info_ini" class="nota">
				Seleccione los datos que desea reportar, para esto, busque el dato en el listado de la parte superior<br>
				y click en el bot&oacute;n Agregar al reporte, puede agregar cuantos datos desee.
				</div>
			</td>
		</tr>
		<tr><td>&nbsp;</td></tr>
		<tr>
			<td>
				<table border="0" width="900" cellpadding=3 cellspacing=1>
					<tr>
						<td class="titulo_filtro" width="410">&nbsp;<img src="images/gra_resumen/fl_filtro.gif">&nbsp;Ubicaci&oacute;n Geogr&aacute;fica</td>
						<td class="titulo_filtro" >&nbsp;<img src="images/gra_resumen/fl_filtro.gif">&nbsp;Filtro Específico</td>
					</tr>
					<tr>
						<td>
							<table width="410" border="0">
								<tr>
									<td><img src="images/spacer.gif" width="10"></td>
									<td width="200">
										<table>
											<tr>
												<td><b>Departamento</b><br>
													<select id="id_depto" name="id_depto[]"  multiple size="17" class="select">
														<?
														//DEPTO
														$depto_dao->ListarCombo('combo',$id_depto,'');
														?>
													</select><br><br>
													<img src='images/ir.png'>&nbsp;<a href="#" onclick="listarMunicipios('id_depto');return false;">Listar Muncipios</a>
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
						<td id='filtro_valor_dato' valign='top'>
							<font class="nota">Si desea, puede especificar la condici&oacute;n que quiere que cumpla el valor del Dato Sectorial seleccionado en la parte superior</font>
							<br><br>
							<b>Valor Dato</b>&nbsp;
							<select name='condicion_filtro_dato' class="select" onchange="if (this.value == 'entre'){document.getElementById('td_entre').style.display=''}else{document.getElementById('td_entre').style.display='none'}">
								<option value='mayor'>Mayor que</option>
								<option value='menor'>Menor que</option>
								<option value='mayor_igual'>Mayor o igual que</option>
								<option value='menor_igual'>Menor o igual que</option>
								<option value='entre'>Entre</option>
							</select>&nbsp;
							<input type="text" name='filtro_dato' class="textfield" size="6">
							<span id='td_entre' style="display:none">&nbsp;y&nbsp;<input type="text" name='filtro_dato_entre' class="textfield" size="10"></span>
							 (El valor debe ser un número)
						</td>
					</tr>
				</table>
			</td>
		</tr>
		<tr><td>&nbsp;</td></tr>
		<tr><td align='center'>
			<input type="hidden" name="accion" value="consultar" />
			<input type="hidden" id="id_dato" name="id_dato" value="" />
			<input type="hidden" id="num_dato_lista" name="num_dato_lista" value="" />
			<input type="submit" name="submit" value="Consultar Datos" onclick="return validar_criterios()" class="boton" />
		</td></tr>
		</table>
	</form>
	<?
	}

	if (isset($_POST["id_datos"]) && !isset($_POST["reportar"])){

		if ($_POST['pdf'] == 1){
			//echo "<form action='admin/reporte_pdf.php' method='POST' target='_blank'>";
			echo "<form action='index.php?m_e=dato_sectorial&accion=consultar&class=DatoSectorialDAO' method='POST'>";
			$t = "PDF";
		}
		else{
			echo "<form action='index.php?m_e=dato_sectorial&accion=consultar&class=DatoSectorialDAO' method='POST'>";
			$t = "CSV (Excel)";
		}
	    ?>
		<table align='center' cellspacing="1" cellpadding="3" border="0" width="750">
			<tr><td align='center' class='titulo_lista' colspan=2>REPORTAR DATOS SECTORIALES EN FORMATO <?=$t?></td></tr>
			<tr><td>&nbsp;</td></tr>
			<tr><td colspan=2>
				Seleccione el formato del reporte:<br>&nbsp;
			</td></tr>
			<tr><td><input type="radio" name="basico" value="1" checked>&nbsp;<b>Reporte Básico</b></td></tr>
			<tr><td>&nbsp;</td></tr>
			<tr><td align='center'>
				<input type="hidden" name="pdf" value="<?=$_POST['pdf']?>" />
				<input type="hidden" name="id_datos" value="<?=$_POST['id_datos']?>" />
				<input type="hidden" name="dato_para" value="<?=$_POST['dato_para']?>" />
				<input type="hidden" name="class" value="DatoSectorialDAO" />
				<input type="hidden" name="method" value="ReporteDatoSectorial" />
				<input type="submit" name="reportar" value="Siguiente" class="boton" />
			</td></tr>
		</table>
		</form>
	<?
	}
}

?>
