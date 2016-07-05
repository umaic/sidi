<?
//LIBRERIAS
include_once ('js/calendar/calendar.php');

//INICIALIZACION DE VARIABLES
$calendar = new DHTML_Calendar('js/calendar/','es', 'calendar-win2k-1', false);
$calendar->load_files();
$fuente_dao = New FuenteDAO();

if (isset($_GET["accion"])){
	$accion = $_GET["accion"];
}
else if (isset($_POST["accion"])){
	$accion = $_POST["accion"];
}

?>

<form action="<?=$_SERVER['PHP_SELF']?>" method="POST" enctype="multipart/form-data">
<table cellspacing="1" cellpadding="5" class="tabla_consulta" width="600" align="center">
	<tr><td align='center' class='titulo_lista'>ACTUALZIAR FECHA DE CORTE DATOS DESPLAZAMIENTO</td></tr>
	<tr><td>&nbsp;</td></tr>
	<tr><td><b>Fuente</b>&nbsp;<select name="id_fuente" class="select">
	<?
	$fuente_dao->ListarCombo('combo','','');
	?>
	</select></td>
	</tr>
	<tr><td><b>Fecha de Corte</b>&nbsp;
			<? $calendar->make_input_field(
			// calendar options go here; see the documentation and/or calendar-setup.js
			array('firstDay'       => 1, // show Monday first
			     'ifFormat'       => '%Y-%m-%d',
			     'timeFormat'     => '12'),
			// field attributes go here
			array('class'       => 'textfield',
				  'size'		=> '10',
				  //'value'		=> '200-1-1',
			      'name'        => 'f_corte'));

			?>
		</td>
	</tr>
	<tr><td>&nbsp;</td></tr>
	<tr>
	  <td align='center'>
		  <input type="hidden" name="accion" value="<?=$accion?>" />
		  <input type="submit" name="submit" value="Actualizar" class="boton" onclick="return validar_forma('f-calendar-field-1,Fecha de Corte','');" />
	  </td>
	</tr>
</table>
</form>