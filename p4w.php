<?php 
// Archivo de 4W para incluir en sitios externos
session_start();

$_SESSION['undaf'] = 0;

if (empty($_SESSION['id_tipo_usuario_s'])) {
    $_SESSION['id_tipo_usuario_s'] = 18; // consulta externa
}

// Validacion token
if (empty($_GET['x'])) {
    die;
}
// Token para portal salahumanitaria.co
else if ($_GET['x'] == '!5tgbHU8765*') {

    $web_externa = true;
?>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Sistema de Informaci&oacute;n Central OCHA - Colombia</title>

<link href="style/brand.css" rel="stylesheet" type="text/css" />
<link href="style/consulta.min.css" rel="stylesheet" type="text/css" />
<link href="style/p4w_consulta.css" rel="stylesheet" type="text/css" />
<link href="style/jquery-ui.min.css" rel="stylesheet" type="text/css" />
<script type="text/javascript" src="js/jquery-1.11.0.min.js"></script>
<script type="text/javascript" src="js/jquery-ui-1.10.4.min.js"></script>
<script type="text/javascript">
$j = jQuery.noConflict();
</script>
</head>
<body>
<?php
$html = (empty($_GET['html'])) ? array() : explode(',',$_GET['html']); 

$brand = (in_array('brand', $html));
    
if ($brand) {

?>
<h1 class="info">Sistema de Informaci&oacute;n  Central &ndash; OCHA &ndash; Naciones Unidas &ndash; Colombia</h1>
<a href="http://www.salahumanitaria.co" target="_blank" class="brand">
    <div id="brand"></div>
</a>
<div id="cabecera">
    <div id="ir_a">
        <select onchange="location.href='/sissh/index.php?accion=consultar&' + this.value">
            <option selected>Ir a</option>
            <optgroup label="Reportes">
            <option value="m_e=dato_sector&class=DatoSectorialDAO">Datos Sectoriales</option>
            <option value="m_e=desplazamiento&class=DesplazamientoDAO">Desplazamiento</option>
            <option value="m_e=evento_c&class=EventoConflictoDAO">Eventos del Conflicto</option>
            <option value="m_e=dato_sector&class=DatoSectorialDAO&method=ReportarMetadatos">Metadatos</option>
            <option value="m_e=org&class=OrganizacionDAO">Organizaciones</option>
            </optgroup>
        </select>
    </div>
</div>
<?php
}

if (!empty($_GET['titulo'])) {
    echo '<div id="titulo_general">'.$_GET['titulo'].'</div>';
}
?>
<div id="cont">
<?php
include('consulta/p4w.php');
}
?>
</div>
</body>
</html>
