<?
session_start();
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>SIDI - API GRAFICAS</title>
<link href="style/consulta.css" rel="stylesheet" type="text/css" />
<script src="t/js/ajax.js"></script>
</head>

<body onload="document.getElementById('loading').style.display='none'">
<div id="cont">
	<div id="loading"><img src="images/ajax/loading_ind.gif" alt="" />&nbsp;Generando.....</div>
	<div id='rta'>
        <? 
        $dir = $_SERVER["DOCUMENT_ROOT"].dirname($_SERVER['SCRIPT_NAME']).'/admin';
        include ("admin/ajax_data.php"); 
        ?>
	</div>
</div>
</body>
</html>
