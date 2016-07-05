<?
//INICIALIZACION DE VARIABLES
$evento_vo = New EventoConflicto();
$evento_dao = New EventoConflictoDAO();
$accion = $_GET["accion"];
$fecha = getdate();
$f_ini = "";
$f_fin = $fecha["year"]."-".$fecha["mon"]."-".$fecha["mday"];
$calendar = new DHTML_Calendar('js/calendar/','es', 'calendar-win2k-1', false);
$calendar->load_files();

if ($accion == "listar"){						 
  ?>
	  <form action="index.php" method="GET">
    <table width="80%" align='center' cellspacing="1" cellpadding="5" border="0" class="tabla_consulta">
			<tr><td class="titulo_lista" align="center">Consulta de Evento de Conflicto por Fecha de Registro</td></tr>
			<tr><td><b>Buscar por ID</b></td></tr>
			<tr>
				<td>
					ID&nbsp;<input type="text" name="id" id="id" class="textfield" size="10">
					<input type="submit" name="consultar" value="Consultar" class="boton" onclick="return validar_forma('id,ID','')" />	
					&nbsp;ID es el <b>n&uacute;mero</b> consecutivo que el sistema asigna luego de insertar el evento
				</td>
			</tr>
			<tr><td><b>Buscar por Fecha de Registro</b>, tenga en cuenta que la fecha suministrada ac&aacute; ser&aacute; buscada en el campo fecha del evento y <b>no</b> fecha de ingreso en el sistema</td></tr>
			<tr>
			  <td colspan='3'>
				Fecha Inicio
				<? $calendar->make_input_field(
               // calendar options go here; see the documentation and/or calendar-setup.js
               array('firstDay'       => 1, // show Monday first
                     'ifFormat'       => '%Y-%m-%d',
                     'timeFormat'     => '12'),
               // field attributes go here
               array('class'       => 'textfield',
							       'value'       => $f_ini,
                     'name'        => 'f_ini')); 
										 
      		?>
  				Fecha Fin
  				<? $calendar->make_input_field(
                 // calendar options go here; see the documentation and/or calendar-setup.js
                 array('firstDay'       => 1, // show Monday first
                       'ifFormat'       => '%Y-%m-%d',
                       'timeFormat'     => '12'),
                 // field attributes go here
                 array('class'       => 'textfield',
  							 			 'value'       => $f_fin,							 
                       'name'        => 'f_fin'));
    		?>
				<input type="hidden" name="class" value="<?=$_GET['class']?>" />
				<input type="hidden" name="method" value="<?=$_GET['method']?>" />
				<input type="hidden" name="param" value="<?=$_GET['param']?>" />
				<input type="hidden" name="accion" value="<?=$accion?>" />
				<input type="submit" name="consultar" value="Consultar" class="boton" onclick="return validar_forma('f-calendar-field-1,Fecha Inicio,f-calendar-field-2,Fecha Fin','')" />	
			</td></tr>
			<tr><td>&nbsp;</td></tr>
    </table>
		</form>
<?
}
else if ($accion == "reportar" && $method == "ReporteDiario"){
  ?>
	  <form action="index_consulta.php" method="POST">
    <table width="80%" align='center' cellspacing="1" cellpadding="3" border="0">
			<tr>
			  <td colspan='3'>
				Fecha Inicio:
				<? $calendar->make_input_field(
               // calendar options go here; see the documentation and/or calendar-setup.js
               array('firstDay'       => 1, // show Monday first
                     'ifFormat'       => '%Y-%m-%d',
                     'timeFormat'     => '12'),
               // field attributes go here
               array('class'       => 'textfield',
							       'value'       => $f_ini,
                     'name'        => 'f_ini')); 
										 
      		?>
				<input type="hidden" name="class" value="<?=$_GET['class']?>" />
				<input type="hidden" name="method" value="<?=$_GET['method']?>" />
				<input type="hidden" name="accion" value="<?=$accion?>" />
				<input type="submit" name="consultar" value="Consultar" class="boton" />	
			</td></tr>
    </table>
		</form>
<?
}
else if ($accion == "reportar" && $method == "ReporteSemanal"){
	?>
	<form action="index_consulta.php" method="POST">
    <table width="80%" align='center' cellspacing="1" cellpadding="3" border="0">
			<tr>
			  <td colspan='3'>
				Fecha Inicio:
				<? $calendar->make_input_field(
               // calendar options go here; see the documentation and/or calendar-setup.js
               array('firstDay'       => 1, // show Monday first
                     'ifFormat'       => '%Y-%m-%d',
                     'timeFormat'     => '12'),
               // field attributes go here
               array('class'       => 'textfield',
							       'value'       => $f_ini,
                     'name'        => 'f_ini')); 
					?>
  				Fecha Fin:
  				<? $calendar->make_input_field(
                 // calendar options go here; see the documentation and/or calendar-setup.js
                 array('firstDay'       => 1, // show Monday first
                       'ifFormat'       => '%Y-%m-%d',
                       'timeFormat'     => '12'),
                 // field attributes go here
                 array('class'       => 'textfield',
  							 			 'value'       => $f_fin,							 
                       'name'        => 'f_fin'));
    		?>										 
				<input type="hidden" name="class" value="<?=$_GET['class']?>" />
				<input type="hidden" name="method" value="<?=$_GET['method']?>" />
				<input type="hidden" name="accion" value="<?=$accion?>" />
				<input type="submit" name="consultar" value="Consultar" class="boton" />	
			</td></tr>
    </table>
		</form>
<?
}
?>	