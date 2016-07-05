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
DD_roundies.addRule('.body_home_consulta', 8);
DD_roundies.addRule('.body_home_consulta_todo', 8);
DD_roundies.addRule('.body_home_consulta_insert', 8);

function exportarListado(){
	location.href='export_data.php?case=org&pdf=2&basico=2&nombre_archivo=orgs_sociales';
}
</script>
<div id="home_top"></div>
<div id="home">
	<ul id="home_item">
		<?
		//ALIMENTACION
		if ($accion == 'alimentar'){ ?>
			<li class="item" id="home_org">
				<div style="margin-left:750px;display:block;width:200px;"><img src="images/mapp_oea/home/insertar.png">&nbsp;<a href="t/index_mo.php?m_e=org&accion=insertar_mo">Crear Organizaci&oacute;n</a></div>
				<div class="tit_home_org">PROYECTO MAPEO DE ORGANIZACIONES SOCIALES</div>
				<?=$div_del?>
				<div id="home_org_list">
					<?
					$letra_ini = (isset($_GET["l"])) ? $_GET["l"] : ''; 
					$org_dao->listarOrgHomeTabs($letra_ini);
					?>
				</div>
			</li>
		<?
		}
		else{
			$link_op = '?m_e=mo&m_g=consulta&accion=consultar&class=OrganizacionMO';
			?>
			<li class="item" id="home_consulta">
				<div class="tit_home_org">PROYECTO MAPEO DE ORGANIZACIONES SOCIALES</div>
				<div style='width:930px;margin:0 auto 0 auto;'>
					<div class="body_home_consulta" style='margin-left:10px'>
						<a href='<?=$link_op?>&caso=sector'>Consultar por Sector</a>
							<br /><br />
							<span class='nota' style='margin-left:10px;display:block'>
								Esta opci&oacute;n le permite generar reportes, gr&aacute;ficas y mapas filtrando por el sector de la organizaci&oacute;n
							</span>	
					</div>
					<div class="body_home_consulta" style='margin-left:10px'>
						<a href='<?=$link_op?>&caso=poblacion'>Consultar por Beneficiario</a>
							<br /><br />
							<span class='nota' style='margin-left:10px;display:block'>
								Esta opci&oacute;n le permite generar reportes, gr&aacute;ficas y mapas filtrando por la poblaci&oacute;n beneficiaria de la organizaci&oacute;n
							</span>	
					</div>
					<div class="body_home_consulta" style='margin-left:10px'>
						<a href='<?=$link_op?>&caso=cobertura'>Consultar por Cobertura</a>
							<br /><br />
							<span class='nota' style='margin-left:10px;display:block'>
								Esta opci&oacute;n le permite generar reportes, gr&aacute;ficas y mapas filtrando por cobertura de la organizaci&oacute;n
							</span>	
					</div>
					<div class="body_home_consulta_todo" style='margin-left:10px'>
						<a href='#' onclick="exportarListado();return false;">Consultar base de datos completa</a>
							<br /><br />
							<span class='nota' style='margin-left:10px;display:block'>
								Esta opci&oacute;n le permite generar el listado completo de las organizaciones registradas en el sistema
							</span>	
					</div>
					<div class="body_home_consulta_insert" style='margin-left:10px'>
						<a href='index_mo.php?m_e=home&accion=alimentar'>Alimentar Organizaciones</a>
							<br /><br />
							<span class='nota' style='margin-left:10px;display:block'>
								Esta opci&oacute;n le permite crear, actualizar y eliminar organizaciones del sistema 
							</span>	
					</div>
					<div style="height:50px;clear:both">&nbsp;</div>
				</div>
				<?
				}
				?>
			</li>
	</ul>
</div>
<div id="home_bottom"></div>
