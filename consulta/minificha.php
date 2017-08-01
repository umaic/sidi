<?php
//LIBRERIAS
include_once("consulta/lib/libs_mapa_i.php");
include_once("t/lib/common/date.class.php");
include_once("t/lib/common/graphic.class.php");
include_once("t/lib/common/imageSmoothArc.php");
include_once("t/lib/common/imageSmoothLine.php");

//INICIALIZACION DE VARIABLES
$depto_dao = New DeptoDAO();
$sissh = New SisshDAO();
$date = new Date();

//MINIFICHA
if (isset($_POST["minificha"])){
	$id_muns = "";
	if (isset($_POST["id_muns"]))	$id_muns = $_POST["id_muns"][0];
	
    $info = $sissh->Minificha($_POST["id_depto"][0],$id_muns,'html');
	die;
}

//MINIFICHA, CAMBIO DE UBIACION YA GENERADA LA MINIFICHA
if (isset($_GET["id_depto_minificha"]) || isset($_GET["id_mun_minificha"])){
	$id_depto = (isset($_GET["id_depto_minificha"])) ? $_GET["id_depto_minificha"] : '';
	$id_mun = (isset($_GET["id_mun_minificha"])) ? $_GET["id_mun_minificha"] : '';
	
	$sissh->Minificha($id_depto,$id_mun,$_GET["formato"]);
	die;
}

//GENERA AUTOMATICAMENTE LA MINIFICHA PARA EL DEPTO QUE VIENE POR URL PARA GUEST
if (isset($_GET["minificha_guest"])){
	$id_muns = "";
	if (isset($_GET["id_muns"]))	$id_muns = $_GET["id_muns"][0];
	$sissh->Minificha($_GET["id_depto"],$id_muns);
	die;
}

$flash = (isset($_GET["flash"])) ? $_GET["flash"] : 1;
?>

<script>
function generarMinificha(){

	var error = 1
	if (document.getElementById('id_depto').value != ""){
		error = 0;
	}

	if (document.getElementById('id_muns').value != ""){
		error = 0;
	}

	if (error == 1){
		alert("Seleccione algun Departamento o Municipio");
	}
	else{
		document.forms[0].submit();
	}

	return false;
}

function generarMinifichaNacionalPDF(){

	//c=2; genera minificha y pdf
	location.href = 'download_pdf.php?c=2';
	
	return false;
}
function generarMinifichaPDF(){

	var id_depto = document.getElementById('id_depto').value;
	var id_mun = document.getElementById('id_muns').value;

	//c=2; genera minificha y pdf
	location.href = 'download_pdf.php?c=2&id_depto='+id_depto+'&id_mun='+id_mun;
	
	return false;
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
		getDataV1('comboBoxMunicipio','t/ajax_data.php?object=comboBoxMunicipio&multiple=17&id_deptos='+id_deptos,'comboBoxMunicipio')
	}
}
function asignarVariablesH(id_depto,id_mun){
	document.getElementById('id_depto').disabled = false;
	document.getElementById('id_depto').value = id_depto;
	document.getElementById('id_muns').disabled = true;
	document.getElementById('id_muns').value = '';
	
	document.getElementById('nombreDepto').innerHTML = '';
	document.getElementById('nombreMpio').innerHTML = '';
	document.getElementById('separador_depto_mpio').style.display = 'none';
	
	//COLOCA EL TITULO DEL DEPTO
	getDataV1('nombreDepto','t/ajax_data.php?object=nombreDepto&id_depto='+id_depto,'nombreDepto');
	
	if (id_mun != 0){
		document.getElementById('id_muns').disabled = false;
		document.getElementById('id_muns').value = id_mun;
		
		document.getElementById('separador_depto_mpio').style.display = '';
		//COLOCA EL TITULO DEL MPIO
		getDataV1('nombreMpio','t/ajax_data.php?object=nombreMpio&id_mpio='+id_mun,'nombreMpio');
	}
	
	//Selecciona el mpio en el combo
	if (document.getElementById('id_mun_depto') != undefined){
		document.getElementById('id_mun_depto').value = id_mun;
	}

}
</script>
<script src="t/js/ajax.js"></script>

<form action="index.php?m_e=minificha&accion=generar&class=Minificha" method="POST">
<table align='center' cellspacing="1" cellpadding="3" border="0">
	<tr class='pathway'><td colspan=4>&nbsp;<img src='images/user-home.png'>&nbsp;<a href='index.php?m_g=consulta&m_e=home'>Inicio</a> &gt; Perfil Geográfico</td></tr>
	<tr>
		<?
		if ($flash == 1){ ?>
			<td id='mapa_flash' width="800">
				<!--<img src="images/stop.gif">&nbsp;Si no puede ver el Mapa en Flash, use la siguiente opción para seleccionar la ubiación, <a href="index.php?m_e=minificha&accion=generar&class=Minificha&flash=0">click aquí</a>
				<br>
				-->
				<table cellspacing="1" cellpadding="2" border="0" width="700" class="_tabla_consulta">
					<tr>
						<td>
							<h1>Perfil Nacional (Nuevo)</h1>
							<img src="images/consulta/generar_pdf.gif" border=0>&nbsp;<a href='#' onclick="return generarMinifichaNacionalPDF()" id='btn_download_pdf'>Descargar Perfil Nacional</a>
							<?
							$path_file = $sissh->dir_cache_perfil."/perfil_00";
							$cache_file = "$path_file.pdf";
							if (file_exists($cache_file)){
								$fecha = $date->Format(date('Y n j',filemtime($cache_file)),'aaaa-mm-dd','dd MM aaaa',' ');
								echo "&nbsp;(Fecha: <i>$fecha</i>, este perfil se genera automáticamente cada 5 díasas)";
							}
							?>
						</td>
					</tr>
					<!--<tr><td>&nbsp;</td></tr>-->
					<tr><td><h1>Perfiles Departamentales y Municipales</h1></td></tr>
					<tr>
						<td id='instrucciones' style='display:none;background-color:#FFFABF;padding:10px;'>
							<img src="images/stop.gif">&nbsp;<b>INSTRUCCIONES</b> [<a href='#' onclick="document.getElementById('instrucciones').style.display='none'">cerrar</a>]<br><br>
							&raquo;&nbsp;<b>Perfil Departamental</b>: Click en la opción Departamento en el mapa y luego seleccionelo haciendo click sobre este.
							<br>
							&raquo;&nbsp;<b>Perfil Municipal</b>: Click en la opción Municipio en el mapa, seleccione un departamento y luego seleccione el municipio haciendo click sobre este o en el listado que aparecerá.
							<br><br>
							Una vez seleccionada la Ubicación, use alguna de las 2 opciones que aparecerán (Generar Perfil en línea o Descargar Perfil PDF)
						</td>
					</tr>
					<tr class="titulo_lista" style="height:25px;">
						<td><a name="ubi_rta"></a>
							<b>&nbsp;Ubicación seleccionada:&nbsp;</b>
							<span id='nombreDepto'> -- </span><span id='separador_depto_mpio' style="display:none">&nbsp;--&gt;&nbsp;</span><span id='nombreMpio'></span>
						</td>
					</tr>
					<tr class="titulo_lista" style="height:25px;display:none" id="btns_gen">
						<td>
							<img src="images/mundo.gif" border=0>&nbsp;<a href='#' onclick="return generarMinificha()" id='btn_gen_online'>Generar Perfil en línea</a>
							&nbsp;&nbsp;&nbsp;
							<img src="images/consulta/generar_pdf.gif" border=0>&nbsp;<a href='#' onclick="return generarMinifichaPDF()" id='btn_download_pdf'>Descargar Perfil PDF</a>
						</td>
					</tr>
					<tr>
						<td>
							<? include("mapa_consulta.php") ?>
							<input type="hidden" id="id_depto" name="id_depto[]">
							<input type="hidden" id="id_muns" name="id_muns[]" disabled>
						</td>
					</tr>
					<!--<tr>
					  <td>
					  	<iframe src="consulta/swf/mapa_i_perfil.html" height="600" width="750" frameborder="0"></iframe>
						<object classid="clsid:d27cdb6e-ae6d-11cf-96b8-444553540000" codebase="http://fpdownload.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=8,0,0,0" width="700" height="570" id="gral" >
						<param name="allowScriptAccess" value="sameDomain" />
						<param name="movie" value="consulta/swf/mapa_i.swf" />
						<param name="quality" value="high" />
						<param name="bgcolor" value="#ffffff" />
						<embed src="consulta/swf/mapa_i.swf" width="700" height="570"  quality="high" bgcolor="#ffffff" name="gral" allowScriptAccess="sameDomain" type="application/x-shockwave-flash" pluginspage="http://www.macromedia.com/go/getflashplayer" />
						</object>
						<input type="hidden" id="id_depto" name="id_depto[]">
						<input type="hidden" id="id_muns" name="id_muns[]" disabled>					  	
	
					  </td>
					</tr>
				--></table>
			</td>
		<?
		}
		else{ ?>
			<td id='mapa_html' align="left" width="800">
				<img src="images/stop.gif">&nbsp;Mostrar el Mapa en Flash, <a href="<?=$PHP_SELF?>?m_e=minificha&accion=generar&class=Minificha&flash=1">click aquí</a>
				<br><br>
				<p align='justify'><b>Instrucciones</b>: <br>
				&roquo;Seleccione un Departamento del siguiente listado, luego haga click en Listar Municipios o genere el Perfil del Departamento con la opción "Generar PERFIL" en la parte izquierda, o si lo prefiere seleccione un Municipio y genere el Perfil de ese Municipio.
				</p>
				<br>
				<table width="450" border="0">
					<tr>
						<td width="200">
							<table>
								<tr>
									<td><b>Departamento</b><br>
										<select id="id_depto" name="id_depto[]"  size="17" class="select">
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
									<td id="comboBoxMunicipio"><input type="hidden" id="id_muns" name="id_muns[]" disabled></td>
								</tr>
							</table>
						</td>
					</tr>
				</table>
			</td>
		<?
		}
		?>
	</tr>
	</table>
<input type="hidden" id="minificha" name="minificha" value="1">
</form>