<?
//LIBRERIAS
include_once("consulta/lib/libs_mapa_i.php");
include_once("admin/lib/common/archivo.class.php");
include_once("admin/lib/dao/log.class.php");

//log fisico para ataques
$log = new LogUsuarioDAO();
$log->insertarLogFisico('mapserver_perfil_resumido');
// fin log fisico

//INICIALIZACION DE VARIABLES
$clase_dao = New ClaseDesplazamientoDAO();
$fuente_dao = New FuenteDAO();
$tipo_dao = New TipoDesplazamientoDAO();
$desplazamiento_dao = New DesplazamientoDAO();
$cat_dao = New CategoriaDatoSectorDAO();
$dato_sectorial_dao = New DatoSectorialDAO();
$periodo_dao = New PeriodoDAO();
$depto_dao = New DeptoDAO();
$mpio_dao = New MunicipioDAO();

$mes = array("","Ene","Feb","Mar","Abr","May","Jun","Jul","Ago","Sep","Oct","Nov","Dic");

//Sidih
if (!isset($_GET["id_depto"])){
	$id_depto = 0;
	$sidih = 1;
	$style_sidih = '';
	$style_website = 'none';
}
//Web Site
else{ 
	//check id_depto sea numero de 2 cifras
	$id_depto = $_GET["id_depto"];
	$ids = $depto_dao->GetAllArrayID('');
	if (ereg("[0-9]{2}",$id_depto) && in_array($id_depto,$ids)){
		$sidih = 0;
		$style_sidih = 'none';
		$style_website = '';
		
		$depto_vo = $depto_dao->get($id_depto);
		
		$ubicacion = $depto_vo->nombre;
	}
	else{
		die("(_|_)");
	}
}
?>

<html>
<head>
<title>SIDI UMAIC - Colombia</title>
<link href="style/consulta.css" rel="stylesheet" type="text/css" />

<script src="js/mscross-1.1.9.js" type="text/javascript"></script>
<script src="js/ajax.js" type="text/javascript"></script>
<script src="js/mapserver.js" type="text/javascript"></script>
<script>
var server = "http://<?=$_SERVER["SERVER_NAME"] ?>/";
var extent_org = '-161112.1,1653895,-469146,1386463';
var id_hidden = 'map_extent';   //Input hidden con el valor del extent
function init(){

	var id_depto_filtro = document.getElementById('id_depto_filtro').value;
	
	if (id_depto_filtro == 0){
		alert("Seleccione un departamento");
		return false;
	}
	
	var map_extent = parseMapExtent(id_hidden);
	
		
	//Vacia el div del mapa por si tiene alguno
	document.getElementById('map_tag').innerHTML = '';
	
	//oculta div ini
	document.getElementById('ini').style.display = 'none';
		
	myMap = new msMap(document.getElementById('map_tag'),'perfil');
	myMap.setCgi(server + 'sissh/consulta/mapserver_mpio.php');
	myMap.setArgs('case=perfil&id_depto_filtro='+id_depto_filtro);
	myMap.setFullExtent(map_extent[0],map_extent[1],map_extent[2],map_extent[3]); // (xmin,xmax,ymin,ymax)

	myMap.redraw();
	//myMap.debug();
	
}

function swapTabs(caso){

	var tabs = Array('resumen','desplazamiento','mina','irsh','acc_belicas');
	
	for(i=0;i<tabs.length;i++){
		document.getElementById(tabs[i]).style.display='none';
		document.getElementById('img_' + tabs[i]).src = 'images/mscross/tabs/'+tabs[i]+'_off.gif'
	}
	
	document.getElementById(caso).style.display = '';
	document.getElementById('img_'+caso).src = 'images/mscross/tabs/'+caso+'.gif'

}

function listarMunicipios(id_depto){
	if (id_depto > 0){
		document.getElementById('ubicar_mpio').style.display = '';
		getDataV1('comboBoxMunicipio','ajax_data.php?object=comboBoxMunicipio&titulo=0&separador_depto=0&multiple=0&id_deptos='+id_depto,'comboBoxMunicipio')
	}
}

function ubiMpio(){

		var id = document.getElementById("id_muns").value;

		if (id == 0){
			alert("Seleccione un municipio");
			return false;
		}

		//funcion en /js/mapserver.js
		setExtentByMpio(id,"map_extent");
		
		setTimeout("init()",500);
}

<?
if ($sidih == 0){
	?>

	function generarMinificha(){
		
		var id_depto = '<?=$id_depto?>';
		var id_mun = document.getElementById('id_muns').value;
		
		var id = id_depto;
		if (id_mun != '')	id = id_mun; 
		
		//funcion en js/ajax.js
		getDataV1('generarPerfilOnlineWebSite','ajax_data.php?object=generarPerfilOnlineWebSite&id_depto='+id_depto+'&id_mun='+id_mun+'&formato=html','generando');
		
		return false;
		
	}
	
	function rediretToPerfilHTML(){
		var id_depto = '<?=$id_depto?>';
		var id_mun = document.getElementById('id_muns').value;
		
		var id = id_depto;
		if (id_mun != '')	id = id_mun;
		 
		location.href='ver_perfil_html.php?id='+id;
	}
	
	function generarMinifichaPDF(){
	
		var id_depto = '<?=$id_depto?>';
		var id_mun = document.getElementById('id_muns').value;
		
		if (id_mun == '')	id_mun =0;
	
		//c=2; genera minificha y pdf
		location.href = 'download_pdf.php?c=2&id_depto='+id_depto+'&id_mun='+id_mun;
		
		return false;
	}
	
	function selectMpio(){
		var combo_mun = document.getElementById('id_muns'); 
		var id_mun = combo_mun.value;
		
		if (id_mun == ''){
			alert("Seleccione un municipio");
			return false;
		}
		else{
			var nom_mun = combo_mun.options[combo_mun.selectedIndex].text;
			var txt_ubicacion = document.getElementById('txt_ubicacion');
			var txt_link_online = document.getElementById('link_online');
			var txt_link_pdf = document.getElementById('link_pdf');
			
			txt_ubicacion.innerHTML = "<?=$ubicacion ?> / <font class='ubicacion_actual'>"+nom_mun+"</font>";
			txt_link_online.innerHTML = 'Consultar Perfil en l&iacute;nea de ' + nom_mun + ' (<?=$ubicacion ?>)';
			txt_link_pdf.innerHTML = 'Descargar Perfil en PDF de ' + nom_mun + ' (<?=$ubicacion ?>)';
		}
		
		ubiMpio();
	
		return false;	
	}
 <?} ?>
</script>

</head>

<?
//Sidih
if (!isset($_GET["id_depto"])){
	?>
	<body>
<?
}
//Web Site
else{
	?>
	<body onLoad="setExtentByDepto('<?=$id_depto?>',id_hidden);setTimeout('init()',500);" >
	<?
}
?>


<!-- EXTENT PARA ENVIAR A MSCROSS DE TODO COLOMBIA-->
<input type="hidden" id="map_extent" value="-161112.1,1653895,-469146,1386463"> 

<!-- DIV PARA VER TODA LA INFO -->
<div id="div_info" class="div_all_info_perfil">
</div>

<!-- IMAGEN INICIAL -->
<div id="ini" style="position:absolute; top:25px; left:3px; width: 65px; height: 73px;">
	<img src="images/consulta/home_mapa_perfil_resumido.gif">
</div>
<table border="0">
	<tr>
		<td>
			<!-- MAPA -->
			<div style="overflow:auto;width:510px;height:520px">
				<div id="map_tag" style="width: 500px; height: 510px; border:1px solid #CCCCCC; z-index:1"></div>
			</div>
			
			<!-- OPCIONES PARA SIDI -->
			<div style="background-color:#D9D9D9;display:<?=$style_sidih ?>">
				<table cellpadding=0 border=0>
					<tr style="font-size:12px">
						<td><img src="images/mscross/important.png"></td>
						<td>
							<b>Filtrar por Departamento</b>
							<select id='id_depto_filtro' class='select' onchange="setExtentByDepto(this.value,'map_extent')">
								<option value=0>Todo Colombia</option>
								<? $depto_dao->ListarCombo('combo',$id_depto,"id_depto <> '00'"); ?>
							</select>
						</td>
						<td>&nbsp;&nbsp;&nbsp;</td>
						<td align='right'>
							<input type='button' value="Generar Mapa" onclick="init();listarMunicipios(document.getElementById('id_depto_filtro').value);" class='boton'>
						</td>
					</tr>
					<tr id="ubicar_mpio" style="display:none">
						<td>&nbsp;</td>
						<td align="right">
							<table>
								<tr>
									<td style="font-size:11px"><b>Municipio</b></td>
									<td id="comboBoxMunicipio"></td>
								</tr>
							</table>
						</td>
						<td colspan="2"><a href="#" onclick="ubiMpio()">&#187;&nbsp;Ubicar en el mapa</a></td>
					</tr>
				</table>
			</div>
		</td>
		<?
		if ($sidih == 1){
		?>
			<td valign="top">
				<table class="opciones_map" cellspacing="2" border=0>
					<tr>
						<td>
							<table>
								<tr>
									<td>
										<b>INSTRUCCIONES</b>:
										Seleccione el departamento en la parte inferior (si no hay ninguno seleccionado ), y click en el bot&oacute;n Generar Mapa;
										una vez generado el mapa haga click sobre el municipio para obtener el perfil.<br><br>
										Si prefiere, puede ubicar el municipio r&aacute;pidamente con la opci&oacute;n
										que aparecerá en la parte inferior. 
										<br><br>
										Para generar el perfil después de haber usado alguna opción de zoom, haga click sobre <img src='images/mscross/icn_alpha_button_identify.png'>
										y luego sobre el municipio.
										<br><br>
										<b>OPCIONES SOBRE EL MAPA</b>
									</td>
								</tr>
							</table>  
						</td>
					</tr>
					<tr>
						<td>
							<table>
								<tr>
									<td valign="top"><img src="images/mscross/icn_alpha_button_identify.png"></td>
									<td><b>Perfil</b><br>Click sobre un Municipio para generar el perfil</td>
								</tr>
							</table>
						</td>
					</tr>
					<tr>
						<td>
							<table>
								<tr>
									<td valign="top"><img src="images/mscross/icn_alpha_button_fullExtent.png"></td>
									<td><b>Mapa Completo</b></td>
								</tr>
							</table>
						</td>
					</tr>
					<tr>
						<td>
							<table>
								<tr>
									<td valign="top"><img src="images/mscross/icn_alpha_button_pan.png"></td>
									<td><b>Paneo</b><br>Arrastre el mapa con el mouse</td>
								</tr>
							</table>
						</td>
					</tr>
					<tr>
						<td>
							<table>
								<tr>
									<td valign="top"><img src="images/mscross/icn_alpha_button_zoombox.png"></td>
									<td><b>Zoom Area</b><br>Seleccione el area con el mouse</td>
								</tr>
							</table>
						</td>
					</tr>
					<tr>
						<td>
							<table>
								<tr>
									<td valign="top"><img src="images/mscross/icn_alpha_button_zoomIn.png"></td>
									<td><b>Zoom In</b><br>Acerca el mapa</td>
								</tr>
							</table>
						</td>
					</tr>
					<tr>
						<td>
							<table>
								<tr>
									<td valign="top"><img src="images/mscross/icn_alpha_button_zoomOut.png"></td>
									<td><b>Zoom Out</b><br>Aleja el mapa</td>
								</tr>
							</table>
						</td>
					</tr>
					<!--
					<tr>
						<td>
							<table>
								<tr>
									<td valign="top"><img src="images/mscross/ooo-calc.png"></td>
									<td><b>Exportar</b><br>Descargar la informaci&oacute;n para hoja de calculo </td>
								</tr>
							</table>
						</td>
					</tr>
				--></table>
			</td>
		<?
		}
		else{
			?>
			<td valign="top">
			<!-- OPCIONES PARA WEB SITE -->
				<table>
					<tr>
						<td class="opciones_map">
							<table width=450>
								<tr>
									<td >
										<b>INSTRUCCIONES</b>:
										Puede generar el perfil resumido de un municipio  haciendo click sobre este en el mapa o si prefiere puede consultar el perfil
										completo usando los links de la parte inferior, inicialmente est&aacute; disponible para <?=$ubicacion ?>, si desea el perfil completo
										para un municipio, seleccionelo de la lista y haga click sobre el link Cambiar ubicaci&oacute;n.
									</td>
								</tr>
							</table>
						</td>
					</tr>
					<tr>
						<td class="pathway_perfil_mapserver">Ubicaci&oacute;n seleccionada: <span id="txt_ubicacion"><font class="ubicacion_actual"><?=$ubicacion ?></font></span></td>
					</tr>
				</table>
				<table cellpadding="3" border=0 class="tabla_opciones_perfil_resumido_website">
					<tr>
						<td>
							<img src="images/mundo.png" border=0 >&nbsp;<a href='#' onclick="return generarMinificha()" id="link_online">Consultar Perfil en l&iacute;nea de <?=$ubicacion ?></a>
							&nbsp;&nbsp;&nbsp;<span id="generando"></span>
						</td>
					<tr>
						<td>
							<img src="images/consulta/generar_pdf.gif" border=0>&nbsp;<a href='#' onclick="return generarMinifichaPDF()" id='link_pdf'>Descargar Perfil en PDF de <?=$ubicacion ?></a>
						</td>
					</tr>
					<tr>
						<td class="nota_gris">
							Para consultar un municipio seleccionelo del siguiente listado
						</td>
					<tr>
						<td>
							<select id="id_muns" class='select'>
								<option value="">[ Municipios ]</option>
								<?
								$mpio_dao->ListarCombo('combo','','id_depto='.$id_depto);
								?>
							</select>
							<img alt="" src="images/ir.png">&nbsp;<a href="#" onclick="return selectMpio()">Cambiar ubicaci&oacute;n</a></td>
					</tr>
				</table>
				<br>
				<table class="opciones_map">
					<tr>
						<td class="ubicacion_actual">AYUDA</td>
					</tr>
					<tr>
						<td colspan="2">
							<table width="450">
								<tr>
									<td>
										Para generar el perfil resumido después de haber usado alguna opción de zoom, haga click sobre <img src='images/mscross/icn_alpha_button_identify.png'>
										y luego sobre el municipio.
										<br><br>
										<b>OPCIONES SOBRE EL MAPA</b>
									</td>
								</tr>
							</table>  
						</td>
					</tr>
					<tr>
						<td>
							<table>
								<tr>
									<td>
										<table>
											<tr>
												<td valign="top"><img src="images/mscross/icn_alpha_button_identify.png"></td>
												<td><b>Perfil</b><br>Click sobre un Municipio para generar el perfil</td>
											</tr>
										</table>
									</td>
								</tr>
								<tr>
									<td>
										<table>
											<tr>
												<td valign="top"><img src="images/mscross/icn_alpha_button_fullExtent.png"></td>
												<td><b>Mapa Completo</b></td>
											</tr>
										</table>
									</td>
								</tr>
								<tr>
									<td>
										<table>
											<tr>
												<td valign="top"><img src="images/mscross/icn_alpha_button_pan.png"></td>
												<td><b>Paneo</b><br>Arrastre el mapa con el mouse</td>
											</tr>
										</table>
									</td>
								</tr>
							</table>
						</td>
						<td>
							<table>
								<tr>
									<td>
										<table>
											<tr>
												<td valign="top"><img src="images/mscross/icn_alpha_button_zoombox.png"></td>
												<td><b>Zoom Area</b><br>Seleccione el area con el mouse</td>
											</tr>
										</table>
									</td>
								</tr>
								<tr>
									<td>
										<table>
											<tr>
												<td valign="top"><img src="images/mscross/icn_alpha_button_zoomIn.png"></td>
												<td><b>Zoom In</b><br>Acerca el mapa</td>
											</tr>
										</table>
									</td>
								</tr>
								<tr>
									<td>
										<table>
											<tr>
												<td valign="top"><img src="images/mscross/icn_alpha_button_zoomOut.png"></td>
												<td><b>Zoom Out</b><br>Aleja el mapa</td>
											</tr>
										</table>
									</td>
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
</body>
</html>