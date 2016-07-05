<?
include_once("../admin/lib/common/mysqldb.class.php");
include_once("../admin/lib/common/archivo.class.php");

include_once("../admin/lib/dao/sissh.class.php");

$dao = New SisshDAO();

$dao->borrarCache();

echo "<font style='font-family:Verdana'>Cache Borrado....¬¬</font>";

?>
