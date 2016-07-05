<?
//LIBRERIAS
include_once ('../admin/js/calendar/calendar.php');
//COMMON
include_once("../admin/lib/common/mysqldb.class.php");
include_once("../admin/lib/common/archivo.class.php");
include_once("../admin/lib/control/ctldesplazamiento.class.php");

//MODEL
include_once("../admin/lib/model/desplazamiento.class.php");
include_once("../admin/lib/model/tipo_desplazamiento.class.php");
include_once("../admin/lib/model/clase_desplazamiento.class.php");
include_once("../admin/lib/model/periodo.class.php");
include_once("../admin/lib/model/municipio.class.php");
include_once("../admin/lib/model/depto.class.php");
include_once("../admin/lib/model/region.class.php");
include_once("../admin/lib/model/poblado.class.php");
include_once("../admin/lib/model/contacto.class.php");
include_once("../admin/lib/model/fuente.class.php");
include_once("../admin/lib/model/poblacion.class.php");

//DAO
include_once("../admin/lib/dao/desplazamiento.class.php");
include_once("../admin/lib/dao/tipo_desplazamiento.class.php");
include_once("../admin/lib/dao/clase_desplazamiento.class.php");
include_once("../admin/lib/dao/municipio.class.php");
include_once("../admin/lib/dao/depto.class.php");
include_once("../admin/lib/dao/region.class.php");
include_once("../admin/lib/dao/poblado.class.php");
include_once("../admin/lib/dao/contacto.class.php");
include_once("../admin/lib/dao/periodo.class.php");
include_once("../admin/lib/dao/fuente.class.php");
include_once("../admin/lib/dao/poblacion.class.php");

//INICIALIZACION DE VARIABLES
$fuente_dao = New FuenteDAO();
$clase_dao = New ClaseDesplazamientoDAO();
$tipo_dao = New TipoDesplazamientoDAO();
$periodo_dao = New PeriodoDAO();
$calendar = new DHTML_Calendar('../admin/js/calendar/','es', 'calendar-win2k-1', false);
$calendar->load_files();
$des_dao = New DesplazamientoDAO();

if (isset($_POST["submit"])){
	$des_dao->ImportarAccionSocial($_FILES['archivo_csv'],$_POST["id_clase"],$_POST["id_tipo"],$_POST["id_fuente"],$_POST["acc"],$_POST["f_corte"]);

}

?>

<form action="<?=$_SERVER['PHP_SELF']?>" method="POST" enctype="multipart/form-data">
<table cellspacing="1" cellpadding="5" class="tabla_consulta" width="600" align="center">
	<tr><td align='center' colspan="2" class='titulo_lista'>IMPORTAR DATOS DE DESPLAZAMIENTO SIPOD (ACCION SOCIAL)</td></tr>
	<tr><td>&nbsp;</td></tr>
	<tr>
		<td><b>Seleccione la clase</b>
		<td>
			<select name="id_clase" class="select">
			<? $clase_dao->ListarCombo('combo','','id_clase_despla IN (1,2)'); ?>
			</select>
		</td>
	</tr>
	<tr>
		<td><b>Seleccione el tipo</b></td>
		<td>
			<select name="id_tipo" class="select">
			<? $tipo_dao->ListarCombo('combo','',''); ?>
			</select>
		</td>
	</tr>
	<tr>
		<td><b>Seleccione la fuente</b></td>
		<td>
			<select name="id_fuente" class="select">
			<? $fuente_dao->ListarCombo('combo','','id_fuedes=2'); ?>
			</select>
		</td>
	</tr>
	<tr>
		<td><b>Fecha de Corte</b></td>
		<td>
			<? $calendar->make_input_field(
			// calendar options go here; see the documentation and/or calendar-setup.js
			array('firstDay'       => 1, // show Monday first
			     'ifFormat'       => '%Y-%m-%d',
			     'timeFormat'     => '12'),
			// field attributes go here
			array('class'       => 'textfield',
				  'size'		=> '10',
				  'value'		=> '2008-09-30',
			      'name'        => 'f_corte'));

			?>
		</td>
	</tr>	<tr>
		<td colspan="2"><input type='radio' name='acc' value='borrar' checked>&nbsp;Borrar los registros existentes para esta Clase-Tipo-Fuente y cargar nueva info
			<br><input type='radio' name='acc' value='adicionar'>&nbsp;Adicionar registros</td></tr>
	<tr><td>&nbsp;</td></tr>
	<tr><td><b>Seleccione el archivo CSV</b></td></tr>
	<tr>
		<td colspan="2">
			<input id="archivo_csv" name="archivo_csv" type="file" class="textfield" size="60">
		</td>
	</tr>
	<tr><td>&nbsp;</td></tr>
	<tr>
		<td colspan="2">
			* No olvide revisar que la carpeta consulta/resumen/desplazamiento/ quede vac&iacute;a (eliminado cache) 
			luedo de realizada la importaci&oacute;n, el sistema realiza esta operaci&oacute;n autom&aacute;ticamente
		</td>
	</tr>
	<tr><td>&nbsp;</td></tr>
	<tr>
	  <td align='center' colspan="2">
		  <input type="hidden" name="accion" value="<?=$accion?>" />
		  <input type="hidden" id="id_periodo_h" name="id_periodo_h" />
		  <!--input type="button" name="submit" value="Importar" class="boton" onclick="preCarga();" />-->
		  <!--<input type="submit" name="submit" value="Importar" class="boton" onclick="return validar_forma('f-calendar-field-1,Fecha de Corte,archivo_csv,Archivo CSV','')" />-->
          Este formulario se reemplaz&oacute; por el <a href="/sissh/tmp_scripts/importar_accion_social.php">siguiente -></a>, esto para hacerlo menos tedioso el tener que cargar archivo por archivo (son 8)
		  </td>
	</tr>
</table>
</form>
