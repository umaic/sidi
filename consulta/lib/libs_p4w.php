<?
//COMMON
include_once($_SERVER['DOCUMENT_ROOT']."/sissh/admin/lib/common/mysqldb.class.php");
include_once($_SERVER['DOCUMENT_ROOT']."/sissh/admin/lib/control/ctlproyecto.class.php");
include_once ($_SERVER['DOCUMENT_ROOT']."/sissh/admin/js/calendar/calendar.php");
include_once($_SERVER['DOCUMENT_ROOT']."/sissh/admin/lib/common/archivo.class.php");
include_once($_SERVER['DOCUMENT_ROOT']."/sissh/admin/lib/common/class.ezpdf.php");


//MODEL
include_once($_SERVER['DOCUMENT_ROOT']."/sissh/admin/lib/model/p4w.class.php");
include_once($_SERVER['DOCUMENT_ROOT']."/sissh/admin/lib/model/moneda.class.php");
include_once($_SERVER['DOCUMENT_ROOT']."/sissh/admin/lib/model/municipio.class.php");
include_once($_SERVER['DOCUMENT_ROOT']."/sissh/admin/lib/model/depto.class.php");
include_once($_SERVER['DOCUMENT_ROOT']."/sissh/admin/lib/model/estado_proyecto.class.php");
include_once($_SERVER['DOCUMENT_ROOT']."/sissh/admin/lib/model/contacto.class.php");
include_once($_SERVER['DOCUMENT_ROOT']."/sissh/admin/lib/model/tipo_vinculorgpro.class.php");
include_once($_SERVER['DOCUMENT_ROOT']."/sissh/admin/lib/model/org.class.php");
include_once($_SERVER['DOCUMENT_ROOT']."/sissh/admin/lib/model/sector.class.php");
include_once($_SERVER['DOCUMENT_ROOT']."/sissh/admin/lib/model/tema.class.php");

//DAO
include_once($_SERVER['DOCUMENT_ROOT']."/sissh/admin/lib/dao/sissh.class.php");
include_once($_SERVER['DOCUMENT_ROOT']."/sissh/admin/lib/dao/p4w.class.php");
include_once($_SERVER['DOCUMENT_ROOT']."/sissh/admin/lib/dao/moneda.class.php");
include_once($_SERVER['DOCUMENT_ROOT']."/sissh/admin/lib/dao/estado_proyecto.class.php");
include_once($_SERVER['DOCUMENT_ROOT']."/sissh/admin/lib/dao/municipio.class.php");
include_once($_SERVER['DOCUMENT_ROOT']."/sissh/admin/lib/dao/depto.class.php");
include_once($_SERVER['DOCUMENT_ROOT']."/sissh/admin/lib/dao/contacto.class.php");
include_once($_SERVER['DOCUMENT_ROOT']."/sissh/admin/lib/dao/tipo_vinculorgpro.class.php");
include_once($_SERVER['DOCUMENT_ROOT']."/sissh/admin/lib/dao/org.class.php");
include_once($_SERVER['DOCUMENT_ROOT']."/sissh/admin/lib/dao/sector.class.php");
include_once($_SERVER['DOCUMENT_ROOT']."/sissh/admin/lib/dao/tema.class.php");
include_once($_SERVER['DOCUMENT_ROOT']."/sissh/admin/lib/dao/factory.class.php");

?>
