<?
//INICIALIZACION DE VARIABLES
$evento_vo = New Evento();
$evento_dao = New EventoDAO();
$accion = $_GET["accion"];
$fecha = getdate();
$f_ini = "";
$f_fin = $fecha["year"]."-".$fecha["mon"]."-".$fecha["mday"];
$calendar = new DHTML_Calendar('js/calendar/','es', 'calendar-win2k-1', false);
$calendar->load_files();

  ?>
	  <form action="index.php" method="POST">
    <table width="80%" align='center' cellspacing="1" cellpadding="5" border="0" class="tabla_consulta">
			<tr><td class="titulo_lista" align="center">Consulta de Repores Semanales</td></tr>
			<tr><td>&nbsp;</td></tr>
			<tr>
			  <td colspan='3'>
				A&ntilde;o:
                <select name="aaaa">
                    <?php 
                    $a_fin = date('Y');
                    for($i=2010;$i<$a_fin;$i++){
                        echo "<option value='$i'>$i</option>";
                    ?>
                </select>
				<input type="hidden" name="class" value="<?=$_GET['class']?>" />
				<input type="hidden" name="method" value="<?=$_GET['method']?>" />
				<input type="hidden" name="param" value="<?=$_GET['param']?>" />
				<input type="hidden" name="accion" value="<?=$accion?>" />
				<input type="submit" name="consultar" value="Consultar" class="boton" onclick="return validar_forma('f-calendar-field-1,Fecha Inicio,f-calendar-field-2,Fecha Fin','')" />	
			</td></tr>
			<tr><td>&nbsp;</td></tr>
    </table>
</form>
