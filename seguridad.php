<? 
session_start();
include_once("admin/lib/common/sessionusuario.class.php");

$s = New SessionUsuario();
$s->ValidarSession();
?>
