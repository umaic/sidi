<?
$root = $_SERVER["DOCUMENT_ROOT"]."/sissh/admin";

//COMMON
include_once($root."/lib/common/mysqldb.class.php");
include_once($root."/lib/common/archivo.class.php");
include_once($root."/lib/control/ctldesplazamiento.class.php");

//MODEL
include_once($root."/lib/model/desplazamiento.class.php");
include_once($root."/lib/model/tipo_desplazamiento.class.php");
include_once($root."/lib/model/clase_desplazamiento.class.php");
include_once($root."/lib/model/periodo.class.php");
include_once($root."/lib/model/municipio.class.php");
include_once($root."/lib/model/depto.class.php");
include_once($root."/lib/model/region.class.php");
include_once($root."/lib/model/poblado.class.php");
include_once($root."/lib/model/contacto.class.php");
include_once($root."/lib/model/fuente.class.php");
include_once($root."/lib/model/poblacion.class.php");

//DAO
include_once($root."/lib/dao/desplazamiento.class.php");
include_once($root."/lib/dao/tipo_desplazamiento.class.php");
include_once($root."/lib/dao/clase_desplazamiento.class.php");
include_once($root."/lib/dao/municipio.class.php");
include_once($root."/lib/dao/depto.class.php");
include_once($root."/lib/dao/region.class.php");
include_once($root."/lib/dao/poblado.class.php");
include_once($root."/lib/dao/contacto.class.php");
include_once($root."/lib/dao/periodo.class.php");
include_once($root."/lib/dao/fuente.class.php");
include_once($root."/lib/dao/poblacion.class.php");
include_once($root."/lib/dao/sissh.class.php");

?>