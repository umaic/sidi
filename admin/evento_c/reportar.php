<?
//INICIALIZACION DE VARIABLES
$evento_vo = New EventoConflicto();
$evento_dao = New EventoConflictoDAO();
$accion = $_GET["accion"];

$f_ini = (isset($_POST["f_ini"])) ? $_POST["f_ini"][0] : "";
$f_fin = (isset($_POST["f_fin"])) ? $_POST["f_fin"][0] : "";

?>
<link type="text/css" rel="stylesheet" href="../js/dhtmlgoodies_calendar/dhtmlgoodies_calendar.css" media="screen"></link>
<script type="text/javascript" src="../js/dhtmlgoodies_calendar/dhtmlgoodies_calendar.js"></script>
<script>
function checkFiltro(){
	var f_ini_O = document.getElementById('f_ini');
	var f_fin_O = document.getElementById('f_fin');
	
	if (f_ini_O.value == "" && f_fin_O.value == ""){
		return confirm("No se ha aplicado filtro de fecha, desea reportar TODOS los eventos del sistema?");
	}
	else if (f_ini_O.value != "" && f_fin_O.value == ""){
		alert("Es necesario la Fecha Fin");
		f_fin_O.focus();
		return false;
	}
	else if (f_ini_O.value == "" && f_fin_O.value != ""){
		alert("Es necesario la Fecha Inicio");
		f_ini_O.focus();
		return false;
	}
}
</script>

<form action="index.php?m_e=evento_c&accion=reportar_admin" method="POST">
<table width="80%" align='center' cellspacing="1" cellpadding="5" border="0" class="tabla_consulta">
	<tr><td class="titulo_lista" align="center">Reporte para depuración de Eventos</td></tr>
	<tr><td>&nbsp;</td></tr>
	<tr>
	  <td colspan='3'>
		Fecha Inicio:&nbsp;
		<input type="text" id="f_ini" name="f_ini[]" class="textfield" size="12" value="<?=$f_ini?>">
		<a href="#" onclick="displayCalendar(document.getElementById('f_ini'),'yyyy-mm-dd',this);return false;"><img src="../images/calendar.png" border="0"></a>
		&nbsp;&nbsp;
		Fecha Fin:
		<input type="text" id="f_fin" name="f_fin[]" class="textfield" size="12" value="<?=$f_fin?>">
		<a href="#" onclick="displayCalendar(document.getElementById('f_fin'),'yyyy-mm-dd',this);return false;"><img src="../images/calendar.png" border="0"></a>
		&nbsp;&nbsp;
		<input type="hidden" name="accion" value="<?=$accion?>" />
		<input type="submit" name="consultar" value="Consultar" class="boton" onclick="return checkFiltro()" />	
	</td></tr>
	<tr><td>&nbsp;</td></tr>
	<tr>
		<td>
			<?
			//SUBMIT FORM
			if (isset($_POST["consultar"])){
				$evento_dao->ReportarDepuracion();
			}
			?>
		</td>
	</tr>
</table>
</form>
