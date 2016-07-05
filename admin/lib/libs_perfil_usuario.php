<?

$fl = dirname($_SERVER['SCRIPT_NAME']);

//COMMON
include_once($_SERVER['DOCUMENT_ROOT'].$fl."/lib/common/mysqldb.class.php");

//MODEL
include_once($_SERVER['DOCUMENT_ROOT'].$fl."/lib/model/perfil_usuario.class.php");
include_once($_SERVER['DOCUMENT_ROOT'].$fl."/lib/model/tipo_usuario.class.php");
include_once($_SERVER['DOCUMENT_ROOT'].$fl."/lib/model/modulo.class.php");

//DAO
include_once($_SERVER['DOCUMENT_ROOT'].$fl."/lib/dao/perfil_usuario.class.php");
include_once($_SERVER['DOCUMENT_ROOT'].$fl."/lib/dao/tipo_usuario.class.php");
include_once($_SERVER['DOCUMENT_ROOT'].$fl."/lib/dao/modulo.class.php");

?>
