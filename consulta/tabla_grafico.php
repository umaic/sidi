<?
include_once("consulta/lib/libs_mapa_i.php");
include_once("admin/lib/common/graphic.class.php");

//INICIALIZACION DE VARIABLES
$depto_dao = New DeptoDAO();
$municipio_vo = New Municipio();
$municipio_dao = New MunicipioDAO();
$org_dao = New OrganizacionDAO();
$proy_dao = New ProyectoDAO();
$eve_dao = New EventoDAO();
$dato_sectorial_dao = New DatoSectorialDAO();
$desplazamiento_dao = New DesplazamientoDAO();
$mina_dao = New MinaDAO();
$fuente_dao = New FuenteDAO();
$edad_dao = New EdadDAO();
$sexo_dao = New SexoDAO();
$condicion_dao = New CondicionMinaDAO();
$estado_dao = New EstadoMinaDAO();
$cat_dato = New CategoriaDatoSectorDAO();
$info_ficha_dao = New InfoFichaDAO();
$cat_dao = New CatEventoConflictoDAO();

$id_cats_dato = $cat_dato->GetAllArray('');

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

$flash = (isset($_GET["flash"])) ? $_GET["flash"] : 1;
$vista = ($flash == 1) ? 'mapa_flash' : 'mapa_html';

$style_display = array('org'=>'none','desplazamiento'=>'none','mina'=>'none','datos'=>'none','evento_c'=>'none');
$tab = (isset($_GET["tab"])) ? $_GET["tab"] : 'evento_c';
$style_display[$tab] = '';
?>


<script type="text/javascript">
//IE Fix
//Variable para que IE no muestre el error JS al mostrar la gráfica usando AJAX, cualquier elemento sirve
var ie_chart = document.createElement("div");
var server = "http://<?=$_SERVER["SERVER_NAME"]?>";
var ajax_script = "admin/ajax_data.php"; 
</script>

<script type="text/javascript" src="admin/js/ajax.js"></script>
<script type="text/javascript" src="js/tabs_consulta.js"></script>
<script type="text/javascript" src="js/swfobject.js"></script>
<link type="text/css" rel="stylesheet" href="js/dhtmlgoodies_calendar/dhtmlgoodies_calendar.css" media="screen"></link>
<script type="text/javascript" src="js/dhtmlgoodies_calendar/dhtmlgoodies_calendar.js"></script>
<script type="text/javascript" src="js/filterlist.js"></script>
<script src="js/jquery-1.11.0.min.js"></script>
<script src="js/highcharts/highcharts.js"></script>
<script src="js/highcharts/modules/data.js"></script>
<script src="js/highcharts/modules/exporting.js"></script>

<?
if (!isset($_POST["submit"]) && !isset($_POST["pdf"])){
  ?>

<form action="index.php?m_e=mapa_i&accion=consultar&class=MapaI" method="POST">
<table align='center' cellspacing="0" cellpadding="0" border="0" width="950">
	<!-- <tr><td align='left' class='titulo_pagina' colspan=8><img src="images/home/gra_resum_small.jpg">&nbsp;Gráficas y Resumenes</td></tr> -->
	<tr class='pathway'>
		<td colspan=4>
			&nbsp;<img src='images/user-home.png'>&nbsp;<a href='https://sidi.umaic.org'>Inicio</a> &gt; Gráficas y Resúmenes
		</td>
		<td colspan=6 align='right'>
			<input type='hidden' id="debug_info">
			&nbsp;<a href='#' style="color:#FFFFFF;" onclick="prompt('Url',document.getElementById('debug_info').value);">Debug_Info</a>
		</td>
	</tr>
	<tr>
		<td width="165"><img src="images/spacer.gif" width="1" height="1"></td>
		<td width="5"><img src="images/spacer.gif" width="1" height="1"></td>
		<td width="174"><img src="images/spacer.gif" width="1" height="1"></td>
		<td width="5"><img src="images/spacer.gif" width="1" height="1"></td>
		<td width="215"><img src="images/spacer.gif" width="1" height="1"></td>
		<td width="5"><img src="images/spacer.gif" width="1" height="1"></td>
		<td width="186"><img src="images/spacer.gif" width="1" height="1"></td>
		<td>&nbsp;</td>
	</tr>
	<tr>
		<?
		$ini = '';
		if (in_array(31,$perfil->id_modulo)){
			if ($tab != 'evento_c'){
				$img = "menu_evento_c.jpg";
			}
			else{
				$img = "menu_evento_c_in.jpg";
			}
			
			echo '<td><a href="#" onclick="tabs(\'evento_c\');return false;"><img src="images/gra_resumen/'.$img.'" id="img_evento_c" border=0></a>';
            
            $ini = 'evento_c';
		}
		if (in_array(18,$perfil->id_modulo)){
			if ($ini != '' && $tab != 'datos'){
				$img = "menu_datos.jpg";
			}
			else{
				$img = "menu_datos_in.jpg";
			}
			
			echo '<td><img src="images/gra_resumen/menu_linea_bottom.jpg">';
			echo '<td><a href="#" onclick="tabs(\'datos\');return false;"><img src="images/gra_resumen/menu_datos.jpg" id="img_datos" border=0></a>';
		}
		if (in_array(15,$perfil->id_modulo)){
			if ($ini != '' && $tab != 'org'){
				$img = "menu_org.jpg";
			}
			else{
				$img = "menu_org_in.jpg";
			}
			
			echo '<td><img src="images/gra_resumen/menu_linea_bottom.jpg">';
			echo '<td><a href="#" onclick="tabs(\'org\');return false;"><img src="images/gra_resumen/menu_org.jpg" id="img_org" border=0></a>';
			
		}
		if (in_array(22,$perfil->id_modulo)){
			if ($ini != '' && $tab != 'desplazamiento'){
				$img = "menu_desplazamiento.jpg";
				
			}
			else{
				$img = "menu_desplazamiento_in.jpg";
			}
			
			echo '<td><img src="images/gra_resumen/menu_linea_bottom.jpg">';
			echo '<td width="174"><a href="#" onclick="tabs(\'desplazamiento\');return false;"><img src="images/gra_resumen/'.$img.'" id="img_desplazamiento" border=0></a>';
			
			$ini = 'desplazamiento';
		}
		if (in_array(24,$perfil->id_modulo) || in_array(31,$perfil->id_modulo)){
			if ($ini != '' && $tab != 'mina'){
				$img = "menu_mina.jpg";
			}
			else{
				$img = "menu_mina_in.jpg";
			}
			
			echo '<td><img src="images/gra_resumen/menu_linea_bottom.jpg">';
			echo '<td><a href="#" onclick="tabs(\'mina\');return false;"><img src="images/gra_resumen/menu_mina.jpg" id="img_mina" border=0></a>';

			$ini = 'mina';
		}
		/*if (in_array(16,$perfil->id_modulo)){
			echo '<a href="javascript:mostrar(orgs)">Proyectos</a>';
		}*/
		echo "<td class='menu_td_fin'><img src='images/spacer.gif' height=37 width='60'></td>";
		?>
	</tr>	
	<? include ("consulta/tab_org.php") ?>
	<? include ("consulta/tab_desplazamiento.php") ?>
	<? include ("consulta/tab_mina.php") ?>
	<? include ("consulta/tab_dato.php") ?>
	<? include ("consulta/tab_evento_c.php") ?>
	
	
	
	<tr><td class="td_dotted_CCCCCC" colspan="10"><img src="images/spacer.gif" height="1"  width="958"></tr>
	<tr>
	<?
		if ($flash == 1){ ?>
			<td id="mapa_flash" colspan="10" class="td_outer_bottom">
				<!--
				<br>
				&nbsp;<img src="images/stop.gif">&nbsp;Si no puede ver el Mapa en Flash, use la siguiente opción para seleccionar la ubiación, <a href="#" onclick="location.href='index.php?m_e=tabla_grafico&accion=consultar&class=TablaGrafico&flash=0&tab='+document.getElementById('tab_hidden').value;return false;">click aquí</a>
				<br><br>
				-->
				<table cellpadding="3" cellspacing="0" width="100%">
					<tr>
						<td>
							<img src="images/stop.gif">&nbsp;<a href='#' onclick="document.getElementById('instrucciones').style.display='';return false;">Ver instrucciones</a>
						</td>
					</tr>
					<tr>
						<td id='instrucciones' style='display:none;background-color:#FFFABF;padding:10px;'>
							<b>INSTRUCCIONES</b> [<a href='#' onclick="document.getElementById('instrucciones').style.display='none';return false;">cerrar</a>]<br><br>
							&raquo;&nbsp;<b>Consulta Departamental</b>: Click en la opción Departamento en el mapa y luego seleccionelo haciendo click sobre este.
							<br>
							&raquo;&nbsp;<b>Consulta Municipal</b>: Click en la opción Municipio en el mapa, seleccione un departamento y luego seleccione el municipio haciendo click sobre este o en el listado que aparecerá.
						</td>
					</tr>	
					<tr class="titulo_lista">
						<td height="30"><a name="ubi_rta"></a>
							<img src='images/mundo.png'>&nbsp;<b>UBICACION GEOGRAFICA DE LA CONSULTA</b>:&nbsp;
							<span id='nombreDepto'>Nacional</span><span id='separador_depto_mpio' style="display:none">&nbsp;-->&nbsp;</span><span id='nombreMpio'></span>
						</td>
					</tr>
					<tr>
						<td align='center'>
							<? include("mapa_consulta.php") ?>
							<input type="hidden" id="id_depto" name="id_depto[]">
							<input type="hidden" id="id_muns" name="id_muns[]" disabled>
						</td>
					</tr>
					<!-- <tr>
					  <td align="center">
					    
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
					</tr>-->
				</table>
			</td>
			<?
		}
		else{ ?>
			<td id='mapa_html' colspan="10" align="left" class="td_outer_bottom"><br>
				&nbsp;<img src="images/stop.gif">&nbsp;Mostrar el Mapa en Flash, <a href="#" onclick="location.href='index.php?m_e=tabla_grafico&accion=consultar&class=TablaGrafico&flash=1&tab='+document.getElementById('tab_hidden').value;return false;">click aquí</a>
				<br><br>
				<table cellpadding="3" cellspacing="0" width="100%" border="0">
					<tr>
						<td id='instrucciones' style='display:none;background-color;#FFFABF;padding:10px;'>
							<? include("consulta/inst_mapa_consulta.php") ?>
						</td>
					</tr>
					<tr class="titulo_lista">
						<td height="30" colspan="2">
							<b>Seleccione la Ubicación</b>
						</td>
					</tr>
					<tr>
						<td colspan="2">
							<p align='justify'><b>Instrucciones</b>: Seleccione un Departamento del siguiente listado, luego haga click en Listar Municipios o realice la consulta para el Departamento con la opción "Generar" en la parte superior, o si lo prefiere seleccione un Municipio y genere la consulta para ese Municipio.</p>
						</td>
					</tr>
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
										<a href="#" onclick="listarMunicipios('id_depto');return false;">Listar Muncipios</a>
									</td>
								</tr>
							</table>
						</td>
						<td valign="top">
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

	<? } ?>
			</table>
		</td>
	</tr>
</table>
<input id='tab_hidden' name='tab_hidden' type="hidden" value=<?=$tab?>>
</form>
<script>//inicializarTextos();</script>
