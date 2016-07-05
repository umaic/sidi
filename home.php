<?
if(!isset($_SESSION["id_usuario_s"])){
	header("Location: login.php?m_g=home");
}

include_once("consulta/lib/libs_mapa_i.php");


if (empty($_SESSION['footer_n'])) {
 
    $org_dao = New OrganizacionDAO();
    $num_orgs = $org_dao->numRecords('');

    $pro_dao = New ProyectoDAO();
    $num_proy = $pro_dao->numRecords('');

    $d_s_dao = New DatoSectorialDAO();
    $num_d_s_valores = $d_s_dao->numRecordsValores('');
    $num_d_s = $d_s_dao->numRecords('');

    $desplazamiento_dao = New DesplazamientoDAO();
    $num_des = $desplazamiento_dao->numRecords('');
    $meses = array ("","Ene","Feb","Mar","Abr","May","Jun","Jul","Ago","Sep","Oct","Nov","Dic");

    $fecha = explode("-",$desplazamiento_dao->GetFechaCorte(2));
    $fecha_corte_a_s = "$fecha[2] ".$meses[$fecha[1]*1]." $fecha[0]"; 

    $fecha = explode("-",$desplazamiento_dao->GetFechaCorte(1));
    $fecha_corte_codhes = "$fecha[2] ".$meses[$fecha[1]*1]." $fecha[0]";

    $evento_dao = New EventoConflictoDAO();
    $num_evento_c = $evento_dao->numRecords('');

    $_f = "$num_orgs <b>Organizaciones</b>
          &nbsp;|&nbsp;$num_proy <b>Proyectos</b>
          &nbsp;|&nbsp;$num_d_s_valores Registros de $num_d_s <b>Datos Sectoriales</b>
          &nbsp;|&nbsp;$num_evento_c Registros de <b>Eventos del Conflicto</b>
          <br><b>Desplazamiento</b>: $num_des Registros &nbsp;&raquo;&nbsp;Ultima Actualizaci&oacute;n: $fecha_corte_a_s para Acci&oacute;n Social | $fecha_corte_codhes para CODHES";
    
    $_SESSION['footer_n'] = $_f;
}
?>
<script>
function resaltar(ele,accion){
	obj = document.getElementById(ele);
	if (accion == 'over'){
		obj.style.background = '#F2F2F2';
	}
	else{
		obj.style.background = '#F9F9F9';
	}
}
</script>
<div id="cuerpo_home">
	<?
	include ("home_sidih.php");
	?>
</div>
