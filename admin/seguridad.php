<? 
session_start();
include_once("lib/common/sessionusuario.class.php");

$s = New SessionUsuario();
$s->ValidarSession();
?>