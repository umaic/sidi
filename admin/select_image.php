<?php
//SEGURIDAD
include_once("seguridad.php");

?>

<html>
<head>
<title>Upload Image</title>
<link href="../style/input.css" rel="stylesheet" type="text/css">
</head>
<body>
<br>
<div id="cont">
<table cellspacing="0" cellpadding="5" align='center' class="tabla_consulta">
	<tr class="titulo_lista"><td>Seleccionar Imagen</td></tr>
	<tr><td><b>Opción 1</b>: <a href="upload_images.php?field=<?=$_GET['field'];?>&dir=<?=$_GET['dir'];?>">Seleccionar una imagen de MI PC</a></td></tr>
	<tr><td><b>Opción 2</b>: <a href="browse_images.php?field=<?=$_GET['field'];?>">Seleccionar una imagen del servidor</a></td></tr>
</table>
</div>
</body>
</html>