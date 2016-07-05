<?
include_once("admin/lib/common/mysqldb.class.php");

$conn = MysqlDb::getInstance();
?>
<script src="admin/js/general.js"></script>
<script src="admin/js/ajax.js"></script>
<script>

function generarMinificha(){
	
	<?
	//DEPTOS
	$sql = "SELECT id_depto FROM departamento where id_depto <> '00'";
	$rs = $conn->OpenRecordset($sql);
	while ($row_rs = $conn->FetchRow($rs)){
		//funcion en js/ajax.js
		echo "getDataV1('generarTodosLosPerfiles','ajax_data.php?object=generarPerfilOnlineWebSite&id_depto=".$row_rs[0]."&id_mun=&formato=html','generando');";
	
	}
	
	//MPIOS
	$sql = "SELECT id_mun, id_depto FROM municipio";
	$rs = $conn->OpenRecordset($sql);
	while ($row_rs = $conn->FetchRow($rs)){
		//funcion en js/ajax.js
		echo "getDataV1('generarPerfilOnlineWebSite','ajax_data.php?object=generarPerfilOnlineWebSite&id_depto=".$row_rs[1]."&id_mun=".$row_rs[0]."&formato=html','generando');";
	
	}
	
	?>
	return false;
	
}

</script>


<input type='button' value='generar todos los perfiles municipales' onclick='generarMinificha()'>

<div id="generando"></div>