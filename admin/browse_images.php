<?php
//SEGURIDAD
include_once("seguridad.php");
//LIBRERIAS
include_once("lib/common/archivo.class.php");

//VARIABLES
$archivo = New Archivo();
$dir_url = "";
$path = "../images";
if (isset($_GET["dir"])){
  $dir_url = $_GET["dir"];
	$path .= $dir_url;
}

$dirs = $archivo->ListarRaizEnArreglo(realpath($path),'');
$num_dirs = count($dirs);
$field = $_GET["field"];
?>
<html>
<head>
<title>Buscar Imágen</title>
<link href="../style/input.css" rel="stylesheet" type="text/css">
<script>
function send_image(image,field){
  opener.document.getElementById(field).value = 'images'+image;
  window.close();
}
</script>
</head>
<body>
<div id="cont">
<table cellspacing="0" cellpadding="5" align='center' class="tabla_consulta">
	<tr class="titulo_lista"><td colspan="2">Seleccionar la imagen del servidor</td></tr>
<tr>
  <td><b>Directorio</b>&nbsp;
	  <select name="dir" class="select" onchange="location.href='browse_images.php?field=<?=$field?>&dir='+this.value" class="input">
		    <option>---Select----</option>
				<?
				for($d=0;$d<$num_dirs;$d++){
				  echo "<option value='".$dir_url."/".$dirs[$d]."'";
					echo ">".$dirs[$d]."</option>";
				}
				?>	
	  </select>
	  <? if ($path != "../images"){  ?>
			&nbsp;<a href="javascript:history.back(1);">Subir</a></td>
	  <? } ?>
</tr>
<?
	
  $files = $archivo->ListarDirectorioEnArreglo(realpath($path));
	$num_files = count($files);
  ?>
  <tr>
    <td colspan='2'>
		  <table cellpadding="4" cellspacing="1">
			  <tr><td align='center' colspan='3'><b>Archivos</b></td></tr>
  		<?
  		for($f=0;$f<$num_files;$f++){
  		  echo "<tr class='fila_lista'>";
				echo "<td><img src='images/ver_imagen.png'>&nbsp;<a href='#' onclick=\"window.open('".$path."/".$files[$f]."','','left=50,top=100,width=300,height=300');return false;\">Ver</a></td>";
				echo "<td>".$files[$f]."</td>";
				echo "<td><a href='#' onclick=\"send_image('".$dir_url."/".$files[$f]."','".$field."')\">Seleccionar --></a></td>";
				echo "</tr>";
  		}
  		?>		
		</td>
</table>
</div>
</body>
</html>
	
