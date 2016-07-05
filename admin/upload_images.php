<?php
//SEGURIDAD
include_once("seguridad.php");

//LIBRERIAS
include_once("lib/common/archivo.class.php");

//VARIABLES
$archivo = New Archivo();
$dir_url = "";


if (isset($_POST["submit"])){
  //REGISTRA EL NOMBRE DEL ARCHIVO TEMPORAL
	$file_tmp = $_FILES['file']['tmp_name'];
	$file_nombre = $_FILES['file']['name'];
	$field = $_POST["field"];
	$dir = $_POST["dir"];

	$path = realpath("../".$dir)."/".$file_nombre;
	@copy($file_tmp,$path);
	
	$f =$dir."/".$file_nombre;

	//ASIGNA EL VALOR AL CAMPO DEL FORMULARIO
	echo "<script>";
  echo "opener.document.getElementById('".$field."').value = '".$f."';";
	echo "window.close()";
	echo "</script>";
	die;
}

$dir = "images/".$_GET["dir"];
$field = $_GET["field"];

?>

<html>
<head>
<title>Upload Image</title>
<link href="../style/input.css" rel="stylesheet" type="text/css">
</head>
<body>
<br>
<form action="upload_images.php" method="POST" enctype="multipart/form-data">
<div id="cont">
<table cellspacing="0" cellpadding="5" align='center' class="tabla_consulta">
	<tr class="titulo_lista"><td colspan="2">Seleccionar la Imagen de Mi PC</td></tr>
	<tr>
		<td>Archivo: </td>
		<td><input type="file" name="file" style="width:500" class="textfield"/></td>
	</tr>
	<tr><td>&nbsp;</td></tr>
	<tr>
		<td align='center' colspan='2'>
		<input type="hidden" name="field" value="<?=$field?>" />
		<input type="hidden" name="dir" value="<?=$dir?>" />
		<input type="submit" name="submit" value="Upload" class="boton" style="width:100" onclick="if(document.getElementById('file').value == ''){alert('Seleccione algún archivo!');return false;}" />
		</td>
	</tr>
	<tr><td align="right" colspan="2"><img src="images/back.gif">&nbsp;<a href="javascript:history.back(1)">Regresar</a></td></tr>

</table>
</div>
</body>
</html>