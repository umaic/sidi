<?
//SEGURIDAD
include_once 'seguridad.php';
include_once 'lib/libs_perfil_usuario.php';
include_once 'lib/dao/log.class.php';

//LOGOUT
if (isset($_GET["accion"]) && $_GET["accion"] == "logout"){
	$s = New SessionUsuario();
	$s->Logout('index_unicef.php');
	die;
}
?></div>
<!-- CONTENIDO : FIN--></div>
<!-- <div id="cierre"><a href="index.php?accion=logout">Salir</a></div> -->
</body>
</html>
