<?
session_start();

// Redirecciona si no es umaic.org
if (strpos($_SERVER['SERVER_NAME'], 'umaic') === false &&
    strpos($_SERVER['SERVER_NAME'], '192') === false &&
    strpos($_SERVER['SERVER_NAME'], 'local') === false) {
    header('Location: http://sidi.umaic.org');
}

include_once("admin/lib/common/mysqldb.class.php");
include_once("admin/lib/common/sessionusuario.class.php");

//MODEL
include_once("admin/lib/model/org.class.php");
include_once("admin/lib/model/org.class.php");
include_once("admin/lib/model/desplazamiento.class.php");
include_once("admin/lib/model/evento_c.class.php");

//DAO
include_once("admin/lib/dao/org.class.php");
include_once("admin/lib/dao/log.class.php");
include_once("admin/lib/dao/desplazamiento.class.php");
include_once("admin/lib/dao/evento_c.class.php");

//INICIALIZCION DE VARIABLES
$org_dao = New OrganizacionDAO();
$log_dao = New LogUsuarioDAO();
$des_dao = New DesplazamientoDAO();
$evento_c_dao = New EventoConflictoDAO();
$conn = MysqlDb::getInstance();
//$conn = MysqlDb::getInstance();

//REGISTRA EL MODULO GENERAL
if (!isset($_SESSION["m_g"]) || $_SESSION["m_g"] == ""){

	$_SESSION["m_g"] = (isset($_GET["m_g"])) ? $_GET["m_g"] : "";
}

//Valores novedades
//Orgs, se definen nuevas las organizaciones creadas los ultimos n meses
$f_ini = date("Y-m-d", strtotime('-1 month'));  // ultimo mes
//$f_ini = date("Y-m-d",mktime(0, 0, 0, date("m")-$n, date("d"),   date("Y")));
$f_fin = date("Y")."-".date("m")."-".date("d");
$new_orgs = $log_dao->getNumAdmin('org','insertar',$f_ini,$f_fin);
//Eventos del conflicto
$n = 7;  //dias
$f_ini = date("Y-m-d", strtotime("-$n days"));  // 7 dias
$new_eventos_c = $evento_c_dao->numRecords("fecha_ing_even BETWEEN '$f_ini' AND now()");
//Desplazamiento
//Fecha de corte de Accion Social
$fecha_corte_a_s = $des_dao->GetFechaCorte(2,'letra');

//Datos sectoriales, se consultan los 5 ultimos datos actualizados con sus periodos
$n = 4;
$meses = array ("","Ene","Feb","Mar","Abr","May","Jun","Jul","Ago","Sep","Oct","Nov","Dic");
$sql = "SELECT nom_dato,ini_valda,fin_valda FROM valor_dato as v JOIN dato_sector USING (id_dato) GROUP BY v.id_dato ORDER BY v.id_dato DESC LIMIT 0,$n";
$rs = $conn->OpenRecordset($sql);
while ($row = $conn->FetchObject($rs)){
	$fin = explode("-",$row->fin_valda);
	//$f_fin = 1*$fin[2]." ".$meses[$fin[1]]." ".$fin[0];
	$a_fin = $fin[0];

	$d_s[] = $a_fin.". ".$row->nom_dato;
}
//ACCION DE LA FORMA
if (isset($_POST["submit"])){

	$login = $_POST["login"];
	$pass = md5($_POST["password"]);

	//log fisico para ataques
	if ($login != 'rubas'){
		include_once("admin/lib/dao/log.class.php");

		$log = new LogUsuarioDAO();
		$log->insertarLogFisico('login',"$login|$pass");
	}
	// fin log fisico

	//$pag_exito = "index.php?m_e=mapa_i&accion=consultar&class=MapaI";
	$pag_exito = "index.php?m_e=home";

	$vu = New SessionUsuario();
	$vu->ValidarUsuario($login,$pass,$pag_exito,'login.php',0);

}

$title = "Sistema Integrado de Informaci&oacute;n Transversal de Colombia";

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title><?php echo $title ?></title>
<link href="style/general.css" rel="stylesheet" type="text/css" />
<link href="style/consulta.css" rel="stylesheet" type="text/css" />
<link href="style/login.css" rel="stylesheet" type="text/css" />
<link href="favicon.ico" rel="shortcut icon" type="image/x-icon" />
</head>

<body onload="document.getElementById('login').focus()">

<?php include('include/header.php') ?>

<div id="cuerpo">
  <div id="cont">
  	<div id="login_div">
        <div class="alert">
            <span class="closebtn" onclick="this.parentElement.style.display='none';">&times;</span>
            ¡Atención!: El día 15 de mayo de 2017 cambiaremos el mecanismo de inicio de sesión en SIDI. Ahora necesitarás iniciar sesión con el correo que registraste en lugar de usar el nombre de usuario.
        </div>
        <div><br /><br /></b></div>
        <div id="img_form">
        <div><h1 style="text-align: center;">Ingreso</h1></div>
            <div class="img_iz"><img src="images/lock_2.jpg" alt="" /></div>
            <div class="fields">
            <form action="<? echo $_SERVER['PHP_SELF']; ?>" method="post">
                <fieldset>
                    <ol>
                        <li>
                            <label for="login">Nombre de Usuario</label>
                            <input type="text" id="login" name="login" class="textfield" />
                        </li>
                        <li>
                            <label for="password">Contrase&ntilde;a</label>
                            <input type="password" id="password" name="password" class="textfield" />
                        </li>
                        <li>
                           <input type="hidden" name="m_g" value="<?=$_SESSION["m_g"]?>" />
                           <input type="submit" name="submit" value="Ingresar" class="boton" />
                        </li>
                    </ol>
                </fieldset>
                </form>
            </div>
           <div id="reg">
                <img src="images/reg_usuario.gif" alt="" /> <a href="registro.php">Registrarse como Usuario</a>
                <img src="images/spacer.gif" width="30" alt="" />
                <img src="images/reg_usuario.gif" alt="" /> <a href="contrasena.php">¿Olvid&oacute; su contrase&ntilde;a?</a>
                <br />
                <img src="images/reg_org.gif" alt="" /> <a href="registro_org.php">Registrar Informaci&oacute;n de su Organizaci&oacute;n</a>
            </div>
        </div>
      <div class="seccion">
        <table>
            <tr>
                <td>
                    <p id="que_es" align="justify">
                        El objetivo del Sistema de Informaci&oacute;n es recolectar, procesar y difundir  informaci&oacute;n humanitaria del pa&iacute;s.
                        <br /><br />
                        Aqu&iacute; podr&aacute; encontrar informaci&oacute;n b&aacute;sica estad&iacute;stica, presencia de organizaciones, eventos de accidentes
                         por Minas y Municiones sin Explotar, informaci&oacute;n de desplazamiento (diferentes fuentes).
                        <br /><br />
                        Esta informaci&oacute;n est&aacute; organizada por: Localizaci&oacute;n Geogr&aacute;fica (Departamento y/o Municipio),
                        Tema (Salud, Educaci&oacute;n, Bienestar Familiar, etc), Demograf&iacute;a (Ni&ntilde;os, Mujeres, Desplazados, etc) y Cronolog&iacute;a.
                    </p>
                </td>
                <td>
                    <iframe width="400" height="225" src="//www.youtube.com/embed/Zmqfyf9mRbM" frameborder="0" allowfullscreen></iframe>
                </td>
            </tr>
        </table>
      	<br />
      </div>
      <div id="logos_otros" class="seccion">
            <h2>Otros sistemas</h2>
            <div class="logo">
                <a href="http://monitor.umaic.org" target="_blank"><img src="images/umaic/logo_MONITOR.png" /></a>
            </div>
            <div class="logo">
                <a href="http://salahumanitaria.co" target="_blank"><img src="images/umaic/logo_SALA.png" /></a>
            </div>
            <div class="logo">
                <a href="http://wiki.umaic.org" target="_blank"><img src="images/umaic/logo_WIKI.png" /></a>
            </div>
            <div class="logo">
                <a href="http://geonode.umaic.org" target="_blank"><img src="images/umaic/logo_GEONODE.png" /></a>
            </div>
            <div class="clear"><p>&nbsp;</p></div>
        </div>
	</div>
    <div id="novedad_div">
        <div id="nd">
            <!--
            <div class="h">
                <h2>Nuevos m&oacute;dulos</h2>
                <ul>
                    <li>Perfiles municipales resumidos en el Sistema de Informaci&oacute;n Geogr&aacute;fico</li>
                    <li>Versi&oacute;n de Mapas Tem�ticos din&aacute;micos en alta resoluci�n para impresi&oacute;n</li>
                </ul>
                <h2>Desplazamiento</h2>
                <p>Actualizados los datos para la fuente Acci�n Social hasta el <?=$fecha_corte_a_s ?></p>
            </div>
            <div class="h">
                <h2>Eventos del Conflicto</h2>
                <p>Se han registrado <?=$new_eventos_c ?> nuevos eventos</p>
                <? if($new_orgs > 0){ ?>
                    <h2>Organizaciones</h2>
                    <p>Se han registrado <?=$new_orgs ?> nuevas organizaciones</p>
                <? } ?>
            </div>
            <div class="h">
                <h2>Datos Sectoriales</h2>
                <p>Se han actualizado los valores de los siguientes datos para el a�o indicado:
                <br />
                <ul>
                    <?
                    foreach ($d_s as $linea){
                        echo "<li>$linea</li>";
                    }
                    ?>
                </ul>
            </div>
            <div class="f">Este sitio requiere <a href="http://www.adobe.com/shockwave/download/download.cgi?P1_Prod_Version=ShockwaveFlash" target="_blank"><img src="images/flash.png" border="0" />&nbsp;Flash player</a> 9 o superior. Sugerimos <a href="http://www.firefox.com" target="_blank">
                <img src="images/firefox.png" border="0" /></a> para una &oacute;ptima visualizaci&oacute;n
            </div>
            -->
        </div>
    </div>
    <?php include('include/footer.php') ?>
  </div>
</div>
</body>
</html>
