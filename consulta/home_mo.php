<?
//LIBRERIAS
include_once("consulta/lib/libs_org.php");

$org_dao = new OrganizacionDAO();
$div_del = '';

//ACCIONES
$accion = (isset($_GET["accion"])) ? $_GET["accion"] : '';

//BORRAR
if ($accion == 'borrar' && is_numeric($_GET["param"])){
	$id = $_GET["param"];

	$org_dao->borrarOrgMO($id);

	$div_del = "<div class='alert'>La Organizaci&oacute;n se elimin&oacute; con &eacute;xito.</div>";
}

//Alerta insert y update
if ($accion == 'insertar'){
	$div_del = "<div class='alert'>La Organizaci&oacute;n se insert&oacute; con &eacute;xito.</div>";
}
if ($accion == 'insertar_error'){
	$div_del = "<div class='alert'>Existe una Organizaci&oacute;n con el mismo nombre,  por favor revisese en el siguiente listado.</div>";
}
if ($accion == 'actualizar'){
	$div_del = "<div class='alert'>La Organizaci&oacute;n se actualiz&oacute; con &eacute;xito.</div>";
}
?>
<script src="t/js/tabber.js" type="text/javascript"></script>
<script src="js/general.js" type="text/javascript"></script>
<script src="js/ajax.js" type="text/javascript"></script>
<script src="js/filter_ul_list.js" type="text/javascript"></script>
<script src="js/roundies_0.0.1a-min.js" type="text/javascript"></script>
<script>
DD_roundies.addRule('.body_home_org', 8);
DD_roundies.addRule('.tit_home_org', 8);
DD_roundies.addRule('.tit_home_rep', 8);
DD_roundies.addRule('.alert', 8);

function exportarListado(){
	location.href='export_data.php?case=org&pdf=2&basico=2&nombre_archivo=orgs_sociales';
}
</script>
<div id="home_top"></div>
<div id="home">
	<ul id="home_item">
		<li class="item" id="home_org">
			<ul id="links">
				<li><img src="images/mapp_oea/home/insertar.png">&nbsp;<a href="t/index_mo.php?m_e=org&accion=insertar_mo">Crear Organizaci&oacute;n</a></li>
			</ul>
			<div class="tit_home_org">PROYECTO MAPEO DE ORGANIZACIONES SOCIALES</div>
			<?=$div_del?>
			<div id="home_org_list">
				<?
				$letra_ini = (isset($_GET["l"])) ? $_GET["l"] : 'A'; 
				$org_dao->listarOrgHomeTabs($letra_ini);
				?>
			</div>
			
		</li>
		<!--
		<li class="item" id="home_org">
			<div class="tit_home_org">Reportes</div>
			<div class="body_home_org">
				<p>&raquo;&nbsp;<a href='?m_e=orgecto_undaf&m_g=consulta&accion=consultar&class=ProyectoUndaf&caso=listado_completo'>Listado completo</a></p>
				<p>&raquo;&nbsp;<a href='?m_e=orgecto_undaf&m_g=consulta&accion=consultar&class=ProyectoUndaf&caso=tema'>Reporte por tema</a></p>
				<p>&raquo;&nbsp;<a href='?m_e=orgecto_undaf&m_g=consulta&accion=consultar&class=ProyectoUndaf&caso=cobertura'>Reporte por cobertura</a></p>
			</div>
			
			<div class="tit_home_org">Organizaci&oacute;n</div>
			<div class="body_home_org">
 				<p>No olvide mantener actualizada la información de su Organización</p>
				<p><img src="images/undaf/home/actualizar.png"><a href='t/index_undaf.php?m_e=org&accion=actualizar&id=<?=$_SESSION["id_org"]?>'>Actualizar</a></p>
			</div>
		</li>
		-->
	</ul>
</div>
<div id="home_bottom"></div>
