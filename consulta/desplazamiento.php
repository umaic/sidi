<?
//LIBRERIAS
include_once("consulta/lib/libs_desplazamiento.php");

//INICIALIZACION DE VARIABLES
$desplazamiento = New DesplazamientoDAO();
$depto_dao = New DeptoDAO();
$municipio_vo = New Municipio();
$municipio_dao = New MunicipioDAO();
$clase = New ClaseDesplazamientoDAO();
$tipo = New TipoDesplazamientoDAO();
$fuente = New FuenteDAO();
$periodo = New PeriodoDAO();
$poblacion = New PoblacionDAO();
$mes = array("","Ene","Feb","Mar","Abr","May","Jun","Jul","Ago","Sep","Oct","Nov","Dic");

$id_depto = Array();
$num_deptos = 0;
if (isset($_GET['id_depto'])){
	$id_depto_s = split(",",$_GET['id_depto']);
	$id_depto = $id_depto_s[0];
	$num_deptos = count($id_depto_s);
	$id_depto = $id_depto_s;
	$id_depto_url = $id_depto;
}

$dato_para = 1;
$chk_dato_para = Array(""," selected ");
if (isset($_GET['dato_para']) && $_GET['dato_para'] == 2){
  $dato_para = 2;
  $chk_dato_para = Array(""," selected ");
}

//ACCION DE LA FORMA
if (isset($_POST["submit"])){
  $desplazamiento->Reportar();
}

//REPORTE EXCEL
if (isset($_POST["reportar"])){
  $desplazamiento->ReporteDesplazamiento($_POST['id_desplazamientos'],$_POST['pdf'],$_POST['basico'],$_POST['dato_para']);
}
//VER DETALLES
if (isset($_GET["method"]) && $_GET["method"] == "Ver"){
  $desplazamiento->Ver($_GET["param"]);
  die;
}

?>
<script src="admin/js/ajax.js"></script>
<script src="admin/js/general.js"></script>
<script>
function listarMunicipios(combo_depto){

	//Se cambia el nivel a minucipal
	document.getElementById('dato_para').value = 2;
	
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
		getDataV1('comboBoxMunicipio','admin/ajax_data.php?object=comboBoxMunicipio&multiple=10&id_deptos='+id_deptos,'comboBoxMunicipio')
	}
}

function unHide(td,img){
  if (document.getElementById(td).style.display == 'none'){
	  document.getElementById(td).style.display = '';
	  //document.getElementById(img).src = 'images/flecha_down.gif';
  }
  else{
	  document.getElementById(td).style.display = 'none';
	 // document.getElementById(img).src = 'images/flecha.gif';
  }
  
  return false;
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

  	var filtros = Array('id_tipo','id_clase','id_fuente','id_periodo','id_poblacion','id_depto');

  	for (f=0;f<filtros.length;f++){
	  	if (validarComboM(document.getElementById(filtros[f]))){
		    error = 0;
		}
	}

  	if (error == 1){
  		if (!checkInputChecked(document.forms[0].aaaa_periodo)){
		    alert("Seleccione algún criterio");
		    return false;
  		}
  		else{
  			return true;
  		}
	}
	else{
		return true;
	}

}

function limpiarFiltros(){

  	var filtros = Array('id_tipo','id_clase','id_fuente','id_periodo','id_poblacion','id_depto');

  	for (f=0;f<filtros.length;f++){
	    ob = document.getElementById(filtros[f]);
	    for (var i = 0; i < ob.options.length; i++){
		   ob.options[ i ].selected = false;
		}
	}

	alert('Se limpiaron los filtros.');
}

function mostrarFiltros(accion){
  	var filtros = Array('tipo','clase','fuente','periodo','poblacion','cobertura');

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
	var filtros = Array("id_tipo","id_clase","id_fuente","id_periodo","id_poblacion");

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
if (!isset($_POST["submit"]) && !isset($_POST["id_desplazamientos"])){
  ?>

<form action="index.php?m_e=desplazamiento&accion=consultar&class=DesplazamientoDAO" method="POST">
<table align='center' cellspacing="1" cellpadding="3" border="0" width="850">
	<tr class='pathway'>
		<td colspan=4>
			&nbsp;<img src='images/user-home.png'>&nbsp;<a href='index.php?m_g=consulta&m_e=home'>Home</a> &gt; <a href="index.php?m_g=consulta">Reportes</a> &gt; Desplazamiento
		</td>
	</tr>
	<tr><td>&nbsp;</td></tr>
	<tr><td colspan=2>
		Puede consultar Datos de Desplazamientos usando uno o varios de los siguiente filtros.
		En cada opciï¿½n puede seleccionar uno o varios criterios usando la tecla Ctrl y el click izquierdo del mouse.
		<br>&nbsp;
	</td></tr>
	<tr>
		<td class="important_evento" colspan="2">
			<table>
				<tr>
					<td><img src="images/consulta/evento/important.png"></td>
					<td>
						<b>Consultar Datos de Desplazamiento a nivel</b>:&nbsp;
						<select id="dato_para" name="dato_para" class="select" onchange="mostrarMun(this.value)">
							<option value=1 <?=$chk_dato_para[0]?>>Departamental</option>
							<option value=2 <?=$chk_dato_para[1]?>>Municipal</option>
						</select>
					</td>
				</tr>
			</table>
		</td>
	</tr>
	<tr><td>&nbsp;</td></tr>
	<tr><td><img src="images/consulta/clean.gif">&nbsp;&nbsp;<a href="#" onclick="limpiarFiltros();">Limpiar Filtros</a></td></tr>
	<tr>
		<td valign="top">
			<table cellspacing="0">
				<tr>
					<td class="titulo_filtro">&nbsp;<img src="images/gra_resumen/fl_filtro.gif">&nbsp;Tipo</td>
					<td rowspan="5"><img src="images/spacer.gif" width="15"></td>
					<td class="titulo_filtro" >&nbsp;<img src="images/gra_resumen/fl_filtro.gif">&nbsp;Fuente</td>
				</tr>
				<tr>
					<td>
						<select id="id_tipo" name="id_tipo[]" multiple size="3" class="select" style="width:180px">
							<? $tipo->ListarCombo('',$id_tipo,''); ?>
						</select>
					</td>
					<td valign="top">
						<select id="id_fuente" name="id_fuente[]" multiple size="3" class="select" style="width:180px">
							<? $fuente->ListarCombo('',$id_fuente,''); ?>
						</select>
					</td>
				</tr>
				<tr>
					<!--<td class="titulo_filtro">&nbsp;<img src="images/gra_resumen/fl_filtro.gif">&nbsp;Poblaci&oacute;n</td>-->
					<td class="titulo_filtro">&nbsp;<img src="images/gra_resumen/fl_filtro.gif">&nbsp;Clase</td>
				</tr>
                <tr>
                    <!--
					<td>
						<select id="id_poblacion" name="id_poblacion[]" multiple size="8" class="select" style="width:180px">
							<? $poblacion->ListarCombo('',$id_poblacion,''); ?>
						</select>
                    </td>
                    -->
					<td valign="top">
						<select id="id_clase" name="id_clase[]" multiple size="3" class="select" style="width:180px">
							<? $clase->ListarCombo('',$id_clase,''); ?>
						</select><br><br>
						<font class="nota">
							<b>Acci&oacute;n Social</b> : Expulsi&oacute;n y Recepci&oacute;n<br>
							<b>CODHES</b> : Estimado LLegadas
						</font>
					</td>
					
				</tr>
				<tr>
					<td colspan="3" class="titulo_filtro">&nbsp;<img src="images/gra_resumen/fl_filtro.gif">&nbsp;Periodo</td>
				</tr>
				<tr>
					<td colspan="3">
						<table>
							<tr>
								<td><img src="images/pwd.png"></td>
								<td><b>Fechas de Corte</b></td>
								<?
								$fuentes = $fuente->GetAllArray('');
								$f = 0;
								foreach ($fuentes as $fuente){
									$f_corte = $desplazamiento->GetFechaCorte($fuente->id);
									$f_corte = split("-",$f_corte);
									$txt_fecha_corte = $f_corte[2]." ".$mes[$f_corte[1]*1]." ".$f_corte[0];
		
									echo "<td><font class='nota'>[ <b>".$fuente->nombre."</b>: $txt_fecha_corte ]</font></td>";
									$f++;
								}
								?>
								
							</tr>
						</table>
						<br /	>
						<?
						$date = getdate();
						$a_actual = $date["year"];
						$j = 0;
						for ($i=1997;$i<=$a_actual;$i++){
							if (fmod($j,8) == 0 && $j > 0)	echo "&nbsp;&nbsp;&nbsp;<a href='#' onclick=\"selectAllCheckboxObj(document.getElementsByName('aaaa_periodo[]'));return false;\">&#171;&nbsp;Todos</a><br /><br />";
							echo "<input type='checkbox' id='aaaa_periodo' name='aaaa_periodo[]' value=$i>".$i."&nbsp;&nbsp;";
							$j++;
						}
						?>
					</td>
				</tr>
				<tr><td>&nbsp;</td></tr>
				<tr>
					<td colspan="3">
						<table>
							<tr>
								<td width="24"><img src="images/more.png"></td>
								<td>
									<a href="#" onclick="unHide('td_periodo','img_periodo');return false;"><b>Filtrar por un periodo mas detallado</b></a><br>
								</td>
							</tr>
							<tr>
								<td colspan="2">
									<font class="nota">Si desea, puede consultar periodos mensuales, trimestrales o semestrales.  Si usa esta opci&oacute;n, no marque ningun a&ntilde;o en la parte superior</font>
								</td>
							</tr>
						</table>
					</td>
				<tr>
					<td id="td_periodo" style='display:none' colspan="3">
						<table>
							<tr>
								<td class="titulo_filtro">&nbsp;<img src="images/gra_resumen/fl_filtro.gif">&nbsp;Mensual</td>
								<td rowspan="5"><img src="images/spacer.gif" width="15"></td>
								<td class="titulo_filtro">&nbsp;<img src="images/gra_resumen/fl_filtro.gif">&nbsp;Trimestral</td>
								<td rowspan="5"><img src="images/spacer.gif" width="15"></td>
								<td class="titulo_filtro">&nbsp;<img src="images/gra_resumen/fl_filtro.gif">&nbsp;Semestral</td>
							</tr>
							<tr>
								<td>
									<select id="id_periodo" name="id_periodo[]" multiple size="10" class="select" style="width:110px">
										<? $periodo->ListarCombo('',$id_periodo,"desc_perio REGEXP 'enero|febrero|marzo|abril|mayo|junio|julio|agosto|septiembre|octubre|noviembre|diciembre'"); ?>
									</select>
								</td>
								<td>
									<select id="id_periodo" name="id_periodo[]" multiple size="10" class="select" style="width:110px">
										<? $periodo->ListarCombo('',$id_periodo,"desc_perio REGEXP 'trim'"); ?>
									</select>
								</td>
								<td>
									<select id="id_periodo" name="id_periodo[]" multiple size="10" class="select" style="width:120px">
										<? $periodo->ListarCombo('',$id_periodo,"desc_perio REGEXP 'semestre'"); ?>
									</select>
								</td>
							</tr>
						</table>
					</td>
				</tr>

			</table>
		</td>
		<td valign="top">
			<table border="0">
				<tr><td colspan="2" class="titulo_filtro" width="250">&nbsp;<img src="images/gra_resumen/fl_filtro.gif">&nbsp;Ubicaci&oacute;n Geogr&aacute;fica</td></tr>
				<tr><td class="important_evento" colspan="2">
					<table>
						<tr>
							<td><img src="images/consulta/evento/important.png"></td>
							<td>
								<input type="radio" name="exp_rec" value="1" checked>Expulsor&nbsp;<input type="radio" name="exp_rec" value="2">Receptor&nbsp;
							</td>
						</tr>
						<tr><td colspan="2"><font class="nota">Si selecciona <b>Estimado Llegadas</b> o <b>Recepci&oacute;n</b> en clase de desplazamiento,
								debe seleccionar aqu&iacute; la opci&oacute;n <b>Receptor</b></font></td></tr>
					</table>
				</td></tr>
				<tr>
					<td width="200">
						<table>
							<tr>
								<td><b>Departamento</b><br>
									<select id="id_depto" name="id_depto[]"  multiple size="10" class="select">
										<?
										//DEPTO
										$depto_dao->ListarCombo('combo',$id_depto,'');
										?>
									</select><br><br>
									<img src="images/consulta/mostrar_combo.png">&nbsp;<a href="#" onclick="listarMunicipios('id_depto');return false;">Listar Muncipios</a>
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
	<tr><td>&nbsp;</td></tr>
	<tr><td align='center' colspan="2">
		<input type="hidden" name="accion" value="consultar" />
		<input type="submit" name="submit" value="Consultar Datos" onclick="return validar_criterios()" class="boton" />
	</td></tr>
	</table>
</form>
<?
}

if (isset($_POST["id_desplazamientos"]) && !isset($_POST["reportar"])){

	if ($_POST['pdf'] == 1){
		//echo "<form action='admin/reporte_pdf.php' method='POST' target='_blank'>";
		echo "<form action='index.php?m_e=desplazamiento&accion=consultar&class=DesplazamientoDAO' method='POST'>";
		$t = "PDF";
	}
	else{
		echo "<form action='index.php?m_e=desplazamiento&accion=consultar&class=DesplazamientoDAO' method='POST'>";
		$t = "CSV (Excel)";
	}
    ?>
	<table align='center' cellspacing="1" cellpadding="3" border="0" width="750">
		<tr><td align='center' class='titulo_lista' colspan=2>REPORTAR DATOS DE DESPLAZAMIENTOS EN FORMATO <?=$t?></td></tr>
		<tr><td>&nbsp;</td></tr>
		<tr><td colspan=2>
			Seleccione el formato del reporte:<br>&nbsp;
		</td></tr>
		<tr><td><input type="radio" name="basico" value="1" checked>&nbsp;<b>Reporte Estï¿½ndar</b>: Muestra la informaciï¿½n del Dato de Desplazamiento</td></tr>
		<tr><td>&nbsp;</td></tr>
		<tr><td align='center'>
			<input type="hidden" name="pdf" value="<?=$_POST['pdf']?>" />
			<input type="hidden" name="dato_para" value="<?=$_POST['dato_para']?>" />
			<input type="hidden" name="id_desplazamientos" value="<?=$_POST['id_desplazamientos']?>" />
			<input type="hidden" name="class" value="DesplazamientoDAO" />
			<input type="hidden" name="method" value="ReporteDesplazamiento" />
			<input type="submit" name="reportar" value="Siguiente" class="boton" />
		</td></tr>
	</table>
	</form>
<?
  }
?>
