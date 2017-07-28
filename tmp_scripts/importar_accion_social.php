<?
set_time_limit(0);

//LIBRERIAS
include_once ('../admin/js/calendar/calendar.php');
//COMMON
include_once("../admin/lib/common/mysqldb_despla_import.class.php");
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
include_once("../admin/lib/dao/sissh.class.php");

//INICIALIZACION DE VARIABLES
$fuente_dao = New FuenteDAO();
$clase_dao = New ClaseDesplazamientoDAO();
$tipo_dao = New TipoDesplazamientoDAO();
$periodo_dao = New PeriodoDAO();
$calendar = new DHTML_Calendar('../admin/js/calendar/','es', 'calendar-win2k-1', false);
$calendar->load_files();
$des_dao = New DesplazamientoDAO();
$archivo = New Archivo();

$dir_tmp = realpath('sipod_csv');
$archivos = $archivo->ListarDirectorioEnArreglo($dir_tmp);

$conn = MysqlDB::getInstance();

// Copiar periodo y vaciar las 2 tablas a usar en la base de datos temporal y borrar la información de Acción social en la principal

$sql = "TRUNCATE sidi_despla_import.periodo";
$conn->Execute($sql);
$sql = "INSERT INTO sidi_despla_import.periodo (cons_perio,desc_perio,orden) SELECT cons_perio,desc_perio,orden FROM sidi.periodo";
$conn->Execute($sql);
$sql = "TRUNCATE sidi_despla_import.registro";
$conn->Execute($sql);

if (isset($_GET["submit"])){

    $next = 0;
	foreach($archivos as $f => $file){
		$userfile['tmp_name'] = realpath('sipod_csv/'.$file); 
		$userfile['name'] = $file; 
		$id_clase = (strpos(strtolower($file),'expulsion') !== false) ? 1 : 2;
		$id_tipo = (strpos(strtolower($file),'individual') !== false) ? 1 : 2;
		$id_fuente = 2;
		$accion = 'adicionar';
		$f_corte = $_GET["f_corte"];
		
		echo "tmp_name=".$userfile['tmp_name']."<br />";
		echo "name=".$userfile['name']."<br />";
		echo "id_clase=$id_clase<br />";
		echo "id_tipo=$id_tipo<br />";
		echo "fecha corte=$f_corte<br />";

        if ($next == 1 || $f == 0){
           $next = 0;
		   $next = $des_dao->ImportarUARIV($userfile,$id_clase,$id_tipo,$id_fuente,$accion,$f_corte);
        }
	}
    
    echo "<br />El siguiente paso es Totalizar, esto toma tiempo...., <a href='../cron_jobs/registro_consulta_tmp.php'>click aqui</a>";
    die;
}

?>

<form action="<?=$_SERVER['PHP_SELF']?>" method="GET">
<table cellspacing="1" cellpadding="5" class="tabla_consulta" width="600" align="center">
	<tr><td align='center' colspan="2" class='titulo_lista'>IMPORTAR DATOS DE DESPLAZAMIENTO SIPOD (ACCION SOCIAL)</td></tr>
	<tr><td>&nbsp;</td></tr>
	<tr>
		<td>
			Archivos a importar (carpeta: tmp_scripts/sipod_csv):<br />
			<?
			foreach ($archivos as $file){
				echo "$file<br />";
			}
			?>
		</td>
	</tr>
	<tr><td>Fecha de Corte: <input type="text" value="YYYY-MM-DD" name="f_corte">
	<tr><td><input type="submit" name="submit" value="Importar">
</table>
</form>
