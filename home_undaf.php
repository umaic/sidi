<?
//LIBRERIAS
include_once("consulta/lib/libs_proyecto.php");

//CONSULTA LOS PROYECTOS ASOCIADOS A LA ORG. DEL USUARIO POR ESTADO
$proy_dao = new ProyectoDAO();
$org_dao = new OrganizacionDAO();

//ACCIONES
$accion = (isset($_GET["accion"])) ? $_GET["accion"] : '';

//BORRAR
if ($accion == 'borrar' && is_numeric($_GET["id"])){
	$id = $_GET["id"];

	$proy_dao->Borrar($id);
}

//CASO
$caso = (isset($_GET["caso"])) ? $_GET["caso"] : '';

//FILTROS
$id_org = 0;
$f_t = 0;
$f_d = 0;
$f_a = 0;
$f_li = 0;

if (isset($_GET["id_t"]) && $_GET["id_t"] != ''){
	$id_t= $_GET["id_t"];
	$f_t = 1;
}

if (isset($_GET["id_a"]) && $_GET["id_a"] != ''){
	$id_a= $_GET["id_a"];
	$f_a = 1;
}
if (isset($_GET["id_d"]) && $_GET["id_d"] != ''){
	$id_d= $_GET["id_d"];
	$f_d = 1;
}
if (isset($_GET["li"]) && $_GET["li"] != ''){
	$li= $_GET["li"];
	$f_li = 1;
}
?>
<script src="t/js/tabber.js" type="text/javascript"></script>
<script src="t/js/jquery-1.2.6.pack.js" type="text/javascript"></script>
<script src="t/js/jquery.dimensions.js" type="text/javascript"></script>
<script src="js/general.js" type="text/javascript"></script>
<script src="js/ajax.js" type="text/javascript"></script>
<script src="js/filter_ul_list.js" type="text/javascript"></script>
<script src="js/roundies_0.0.1a-min.js" type="text/javascript"></script>
<script>
DD_roundies.addRule('.body_home_org', 8);
DD_roundies.addRule('.tit_home_org', 8);
DD_roundies.addRule('.tit_home_proy', 8);
DD_roundies.addRule('.tit_home_consulta', 8);
DD_roundies.addRule('.body_home_consulta', 8);
DD_roundies.addRule('.body_home_consulta_bottom', 8);


function mapaProyectoCobertura(id_proy,accion){	
	
	obj_fondo = document.getElementById('div_bcg');

	if (accion == 'mostrar'){
	
		if (obj_fondo == undefined){
			//Fondo negro
			fondo = document.createElement('div');

			fondo.id = 'div_bcg';
			fondo.style.background = "#6B6B6B";
			fondo.style.opacity = 0.50;
			fondo.style.filter = 'alpha(opacity=50)';
			fondo.style.width = docwidth()+'px';
			fondo.style.height = docheight()+'px';
			fondo.style.zIndex = 5;
			fondo.style.position = 'absolute';
			fondo.style.top = 0;
			fondo.style.left = 0;
			document.body.appendChild(fondo);
		}
		else{
			var proy_gen = document.getElementById('mapa_gen');
			if (proy_gen != undefined) document.body.removeChild(proy_gen);

			obj_fondo.style.display = '';
		} 

		var w_div = 900;
		var h_div = 670;

		//Valor de left para que siempre salga en el centro
		left_v = (docwidth() - w_div) / 2;
		top_v = 5;

		var div = document.createElement('div');
		div.id = 'mapa_gen';
		div.style.zIndex = 500;
		div.style.position = 'fixed';
		div.style.top = top_v + 'px';
		div.style.left = left_v + 'px';
		div.style.width = w_div;
		div.style.height = h_div;
		
		var iframe_src = 'mapa.php?case=proyecto_undaf&filtro=0&id_filtro=0&id_depto_filtro=0&id_proy='+id_proy;
		div.innerHTML = '<p>&nbsp;&nbsp;<a href="#" onclick="mapaProyectoCobertura(0,\'ocultar\')">X Cerrar Mapa</a></p><iframe src='+iframe_src+' id="iframe_mapa" frameborder="0" width="'+w_div+'" height="'+h_div+'" style="background: #fff3c4;"></iframe>';	
		
		document.body.appendChild(div);
	}
	else{
		obj_fondo.style.display = 'none';
		document.getElementById('mapa_gen').style.display = 'none';
	}
}

var archivo_export = '';
function listado(id_estado,filename,id_ajax){
	var params = 'object=reporteOnLineProyectoUndaf&filtro=estado&id_filtro='+id_estado+'&depto=2&ubicacion=&show_html=0';
	getDataV1('listado_home_proy_undaf','t/ajax_data.php?' + params,id_ajax);
	archivo_export = filename;
}		

function redirectToExport(){
	location.href = 'export_data.php?case=xls_session&nombre_archivo=proyectos_'+archivo_export;
}

function fixHeightLista(){
	//Fix al alto del listado de proyectos
	var alto_header = 290;
	document.getElementById('home_proy_list').style.height = docheight() - 290 + 'px';
}
function filtroHome(id,filtro){
	var params = 'm_e=home';

	switch (filtro){
		case 'f_t':
			params += '&id_t='+id;
			<?
			if ($f_a == 1)	echo "params += '&id_a=$id_a';";
			if ($f_d == 1)	echo "params += '&id_d=$id_d';";
			if ($f_li == 1)	echo "params += '&li=$li';";
			?>
		break;
		case 'f_a':
			params += '&id_a='+id;
			<?
			if ($f_t == 1)	echo "params += '&id_t=$id_t';";
			if ($f_d == 1)	echo "params += '&id_d=$id_d';";
			if ($f_li == 1)	echo "params += '&li=$li';";
			?>
		break;
		case 'f_d':
			params += '&id_d='+id;
			<?
			if ($f_t == 1)	echo "params += '&id_t=$id_t';";
			if ($f_a == 1)	echo "params += '&id_a=$id_a';";
			if ($f_li == 1)	echo "params += '&li=$li';";
			?>
		break;
		case 'f_li':
			params += '&li='+id;
			<?
			if ($f_t == 1)	echo "params += '&id_t=$id_t';";
			if ($f_a == 1)	echo "params += '&id_a=$id_a';";
			if ($f_d == 1)	echo "params += '&id_d=$id_d';";
			?>
		break;
	}	

	location.href = 'index_undaf.php?'+params;
}
</script>
<div id="home_top"></div>
<div id="home">
	<ul id="home_item">
			<?
			//Home para alimentador y admin
			if ($_SESSION["id_tipo_usuario_s"] == 27 || $_SESSION["id_tipo_usuario_s"] == 30){
				?>
				<li class="item" id="home_proy">
					<div class="tit_home_proy">Iniciativas, proyectos y programas del Sistema de las Naciones Unidas en Colombia</div>
					<ul id="links">
                        <li>
                            <img src="images/undaf/home/insertar.png">&nbsp;
                            <!--<a href="t/index_undaf.php?m_e=proyecto&accion=insertar">Crear Proyecto</a>-->
                            <a href="t/index.php?m_g=consulta&m_e=p4w&accion=insertar" target="_blank">Crear Proyecto, recuerde que la alimentaci&oacute;n de UNDAF ahora se realiza en el 4W</a>
                        </li>
						<!--<li><img src="images/undaf/home/importar.png">&nbsp;<a href="t/index_undaf.php?m_e=proyecto&accion=importar">Importar archivo plano</a></li>-->
					</ul>
					<div id="home_proy_list" class="tabber">
					<?
					if (isset($_SESSION["id_org"]) && $_SESSION["id_tipo_usuario_s"] == 27){
						$id_org = $_SESSION["id_org"];
						$org_vo = $org_dao->Get($id_org);
					}
					else{
						if ($f_a != 0)	$id_org = $id_a;
					}
					
					$proy_dao->listarProyectoHomeTabs($id_org);
					?>
					</div>
				</li>
				<li class="item" id="home_org">
					<div class="tit_home_org">Reportes</div>
					<div class="body_home_org">
						<p>&raquo;&nbsp;<a href='?m_e=proyecto_undaf&m_g=consulta&accion=consultar&class=ProyectoUndaf&caso=listado_completo'>Listado completo</a></p>
						<p>&raquo;&nbsp;<a href='?m_e=proyecto_undaf&m_g=consulta&accion=consultar&class=ProyectoUndaf&caso=tema'>Reporte por tema</a></p>
						<p>&raquo;&nbsp;<a href='?m_e=proyecto_undaf&m_g=consulta&accion=consultar&class=ProyectoUndaf&caso=agencia'>Reporte por agencia</a></p>
						<p>&raquo;&nbsp;<a href='?m_e=proyecto_undaf&m_g=consulta&accion=consultar&class=ProyectoUndaf&caso=poblacion'>Reporte por Poblaci&oacute;n</a></p>
						<p>&raquo;&nbsp;<a href='?m_e=proyecto_undaf&m_g=consulta&accion=consultar&class=ProyectoUndaf&caso=cobertura'>Reporte por cobertura</a></p>
						<p>
							&raquo;&nbsp;<a href='?m_e=proyecto_undaf&m_g=consulta&accion=consultar&class=ProyectoUndaf&caso=conteo'>Reportes de conteo</a>
							<br />
							<span class='nota' style='margin-left:10px;display:block'>
								Podr&aacute; consultar la cantidad de proyectos y agencias por cantidad de presupuesto
								o por cantidad de beneficiarios
							</span>	
						</p>
					</div>
					
					<div class="tit_home_org">Organizaci&oacute;n</div>
					<?
					if ($_SESSION["id_tipo_usuario_s"] == 27){ 
						
						$size = getimagesize($org_vo->logo);
						$w = $size[0];
						$h = $size[1];

						if ($w > 180){
							$h = 180*$h/$w;
							$w = 180;
						}

						?>
						<div style="margin-bottom:15px;"><img src="<?=$org_vo->logo?>" width="<?=$w?>" height="<?=$h?>" /></div>
						<div class="body_home_org">
							<p>
								No olvide mantener actualizada la información de su Organización:
							</p>
							<p><img src="images/undaf/home/actualizar.png"><a href='t/index_undaf.php?m_e=org&accion=actualizar&id=<?=$_SESSION["id_org"]?>'>Actualizar</a></p>
						</div>
						<?
					}
					else{ ?>
						<div class="body_home_org">
							<p>
								No olvide mantener actualizada la información de las Agencias del SNU:
							</p>
							<select id='id_org_update' class='select'>
								<? $org_dao->ListarCombo('combo_sigla','','id_tipo=4') ?>
							</select/>
							<p><img src="images/undaf/home/actualizar.png"><a href='#' onclick="location.href='t/index_undaf.php?m_e=org&accion=actualizar&id='+document.getElementById('id_org_update').options[document.getElementById('id_org_update').selectedIndex].value;return false;">Actualizar</a></p>
						</div>
					<? } ?>
				</li>
				<?
			}
			//PERFIL DE CONSULTA EXTERNA
			else if ($_SESSION["id_tipo_usuario_s"] == 29){
				$link_op = '?m_e=proyecto_undaf&m_g=consulta&accion=consultar&class=ProyectoUndaf';
				?>
				<li class="item" id="home_consulta">
					<div class="tit_home_consulta">SISTEMA DE CONSULTA DE INICIATIVAS, PROYECTOS Y PROGRAMAS</div>
					<?
					if ($caso == ''){ ?>
						<div style='width:930px;margin:0 auto 0 auto;'>
							<div class="body_home_consulta">
								<a href='<?=$link_op?>&caso=agencia'>Consultar por Agencia</a>
									<br /><br />
									<span class='nota' style='margin-left:10px;display:block'>
										Esta opci&oacute;n le permite generar reportes, gr&aacute;ficas y mapas filtrando por la agencia ejecutra de la iniciativa, proyecto o programa
									</span>	
							</div>
							<div class="body_home_consulta" style='margin-left:10px'>
								<a href='<?=$link_op?>&caso=tema'>Consultar por Tem&aacute;tica</a>
									<br /><br />
									<span class='nota' style='margin-left:10px;display:block'>
										Esta opci&oacute;n le permite generar reportes, gr&aacute;ficas y mapas filtrando por el tema de la iniciativa, proyecto o programa
									</span>	
							</div>
							<div class="body_home_consulta" style='margin-left:10px'>
								<a href='<?=$link_op?>&caso=poblacion'>Consultar por Beneficiario</a>
									<br /><br />
									<span class='nota' style='margin-left:10px;display:block'>
										Esta opci&oacute;n le permite generar reportes, gr&aacute;ficas y mapas filtrando por la poblaci&oacute;n beneficiaria de la iniciativa, proyecto o programa
									</span>	
							</div>
							<div class="body_home_consulta" style='margin-left:10px'>
								<a href='<?=$link_op?>&caso=cobertura'>Consultar por Territorio</a>
									<br /><br />
									<span class='nota' style='margin-left:10px;display:block'>
										Esta opci&oacute;n le permite generar reportes, gr&aacute;ficas y mapas filtrando por cobertura de la iniciativa, proyecto o programa
									</span>	
							</div>
							<div class="body_home_consulta" style='margin-left:10px'>
								<a href='<?=$link_op?>&caso=conteo'>Reportes de conteo</a>
									<br /><br />
									<span class='nota' style='margin-left:10px;display:block'>
										Podr&aacute; consultar la cantidad de proyectos y agencias por cantidad de presupuesto
										o por cantidad de beneficiarios
									</span>	
							</div>
							<div style="clear:both"></div>
							<div class="body_home_consulta_bottom">
								<a href='index_undaf.php?m_e=home&caso=listado_completo'>CONSULTAR BASE DE DATOS COMPLETA</a>
							</div>
							<div>&nbsp;</div>
						</div>
					<?
					}
					else if ($caso == 'listado_completo'){
						?>
						<p>A continuaci&oacute;n se presenta el listado completo de iniciativas, proyectos y programas agrupados por el estado de ejecuci&oacute;n
						<div id="home_proy_list" class="tabber">
							<?
							$proy_dao->listarProyectoHomeTabs(0);
							?>
						</div>
						<?
					}
					?>
				</li>
				<?
			}
			?>
	</ul>
</div>
<div id="home_bottom"></div>
