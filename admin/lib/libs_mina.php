<?
$root = $_SERVER["DOCUMENT_ROOT"]."/sissh/admin";

//COMMON
include_once("$root/lib/common/mysqldb.class.php");
include_once("$root/lib/common/archivo.class.php");
include_once("$root/lib/control/ctlmina.class.php");

//MODEL
include_once("$root/lib/model/mina.class.php");
include_once("$root/lib/model/municipio.class.php");
include_once("$root/lib/model/depto.class.php");
include_once("$root/lib/model/condicion_mina.class.php");
include_once("$root/lib/model/estado_mina.class.php");
include_once("$root/lib/model/sexo.class.php");
include_once("$root/lib/model/edad.class.php");
include_once("$root/lib/model/tipo_evento.class.php");
include_once("$root/lib/model/actor.class.php");

//DAO
include_once("$root/lib/dao/mina.class.php");
include_once("$root/lib/dao/municipio.class.php");
include_once("$root/lib/dao/depto.class.php");
include_once("$root/lib/dao/condicion_mina.class.php");
include_once("$root/lib/dao/estado_mina.class.php");
include_once("$root/lib/dao/sexo.class.php");
include_once("$root/lib/dao/edad.class.php");
include_once("$root/lib/dao/tipo_evento.class.php");
include_once("$root/lib/dao/evento_c.class.php");
include_once("$root/lib/dao/actor.class.php");

?>