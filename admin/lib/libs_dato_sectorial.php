<?
$root = $_SERVER["DOCUMENT_ROOT"]."/sissh/admin";

//COMMON
include_once("$root/lib/common/mysqldb.class.php");
include_once("$root/lib/common/archivo.class.php");
include_once("$root/lib/common/cadena.class.php");
include_once("$root/lib/control/ctldato_sectorial.class.php");

//MODEL
include_once("$root/lib/model/dato_sectorial.class.php");
include_once("$root/lib/model/cat_d_s.class.php");
include_once("$root/lib/model/municipio.class.php");
include_once("$root/lib/model/depto.class.php");
include_once("$root/lib/model/u_d_s.class.php");
include_once("$root/lib/model/region.class.php");
include_once("$root/lib/model/poblado.class.php");
include_once("$root/lib/model/resguardo.class.php");
include_once("$root/lib/model/parque_nat.class.php");
include_once("$root/lib/model/div_afro.class.php");
include_once("$root/lib/model/sector.class.php");
include_once("$root/lib/model/contacto.class.php");

//DAO
include_once("$root/lib/dao/dato_sectorial.class.php");
include_once("$root/lib/dao/cat_d_s.class.php");
include_once("$root/lib/dao/municipio.class.php");
include_once("$root/lib/dao/depto.class.php");
include_once("$root/lib/dao/u_d_s.class.php");
include_once("$root/lib/dao/region.class.php");
include_once("$root/lib/dao/poblado.class.php");
include_once("$root/lib/dao/resguardo.class.php");
include_once("$root/lib/dao/parque_nat.class.php");
include_once("$root/lib/dao/div_afro.class.php");
include_once("$root/lib/dao/sector.class.php");
include_once("$root/lib/dao/contacto.class.php");
include_once("$root/lib/dao/sissh.class.php");


?>