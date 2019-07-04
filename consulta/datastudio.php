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

<? if (isset($_GET["reporting"])){ ?>


<table align='center' cellspacing="0" cellpadding="0" border="0" width="60%">
    <tr><td>
            <iframe width="100%" height="870" src="https://datastudio.google.com/embed/reporting/<?php echo $_GET["reporting"] ?>" frameborder="0" style="border:0" allowfullscreen></iframe>
    </td></tr>
</table>
<? } ?>

		</td>
	</tr>
</table>
<input id='tab_hidden' name='tab_hidden' type="hidden" value=<?=$tab?>>


