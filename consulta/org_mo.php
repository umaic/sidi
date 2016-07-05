<?
//LIBRERIAS
include_once("consulta/lib/libs_proyecto.php");

//CONSULTA LOS PROYECTOS ASOCIADOS A LA ORG. DEL USUARIO POR ESTADO
$proy_dao = new ProyectoDAO();
$org_dao = new OrganizacionDAO();
$sector_dao = New SectorDAO();
$pob_dao = new PoblacionDAO();

//CASO
$caso = (isset($_GET["caso"])) ? $_GET["caso"] : '';

?>
<script src="t/js/tabber.js" type="text/javascript"></script>
<script src="js/ajax.js" type="text/javascript"></script>
<script src="js/roundies_0.0.1a-min.js" type="text/javascript"></script>
<script type="text/javascript">
    DD_roundies.addRule('.tit_home_org', 8);
    DD_roundies.addRule('.consulta_proy_body', 8);
    DD_roundies.addRule('.consulta_proy_boton', 8);
    DD_roundies.addRule('.consulta_proy_boton_conteo', 8);
    DD_roundies.addRule('.consulta_proy_filtro', 8);
    DD_roundies.addRule('#proy_gen', 8);

function asignarVariablesH(id_depto,id_mun){
	document.getElementById('id_depto').disabled = false;
	document.getElementById('id_depto').value = id_depto;
	document.getElementById('id_muns').disabled = true;
	document.getElementById('id_muns').value = '';
	
	document.getElementById('nombreDepto').innerHTML = '';
	document.getElementById('nombreMpio').innerHTML = '';
	document.getElementById('separador_depto_mpio').style.display = 'none';
	
	//COLOCA EL TITULO DEL DEPTO
	getDataV1('nombreDepto','admin/ajax_data.php?object=nombreDepto&id_depto='+id_depto,'nombreDepto');
	
	if (id_mun != 0){
		document.getElementById('id_muns').disabled = false;
		document.getElementById('id_muns').value = id_mun;
		
		document.getElementById('separador_depto_mpio').style.display = '';
		//COLOCA EL TITULO DEL MPIO
		getDataV1('nombreMpio','admin/ajax_data.php?object=nombreMpio&id_mpio='+id_mun,'nombreMpio');

		//Desactiva la opcion de grafica y mapa por cobertura
		/*
		if ('<?=$_GET["caso"]?>' == 'cobertura'){
			document.getElementById('link_grafica').disabled = true;
			document.getElementById('link_mapa').disabled = true;
		}
		else{
			document.getElementById('link_grafica').disabled = false;
			document.getElementById('link_mapa').disabled = false;
		}
		*/
	}
	
	//Selecciona el mpio en el combo
	if (document.getElementById('id_mun_depto') != undefined){
		document.getElementById('id_mun_depto').value = id_mun;
	}

	//Coloca la ubicacion en el campo de filtro por cobertura
	var nom_depto_cobertura = document.getElementById('nombreDepto_filtro');
	if (nom_depto_cobertura != undefined){
		setTimeout("document.getElementById('nombreDepto_filtro').innerHTML = document.getElementById('nombreDepto').innerHTML; document.getElementById('nombreMpio_filtro').innerHTML = ': ' + document.getElementById('nombreMpio').innerHTML; ",500);
	}

	

}
function accionOrg(chart,accion,filtro){
	
	var ubicacion = 0;
	var depto = 0;
	var id_filtro = 0;
	var id_div_ajax = 'proy_gen';
	
	//Div donde se muestran los resultados
	var obj_result = document.getElementById(id_div_ajax);
	
	if (filtro != 'listado_completo'){
		ubicacion = 0;  // Para toda Colombia
		depto = 2;

		id_depto = document.getElementById('id_depto').value;
		id_muns = document.getElementById('id_muns').value;

		if (id_depto != 0){
			ubicacion = id_depto;
			depto = 1;
		}
		if (id_muns != ''){
			ubicacion = id_muns;
			depto = 0;
		}

		if (id_depto != 0 && id_depto != 20 && id_depto != 47){
			alert("Recuerde que los Departamentos solo pueden ser Cesar o Magdalena");
			return false;
		}
	}	

	if (filtro != 'listado_completo' && filtro!='conteo_presupuesto'){
		
		var id_filtro = getRadioCheck(document.getElementsByName('id_filtro'));

		
		switch (filtro){
			case 'sector':
				var txt_alert = "Seleccione alg\xfan Sector";
			break;
			case 'poblacion':
				var txt_alert = "Seleccione alg\xfan tipo de poblaci\xf3n beneficiaria ";
			break;
			case 'agencia':
				var txt_alert = "Seleccione alguna agencia ejecutora ";
			break;
		}

		if (filtro == 'cobertura'){
			//txt_alert = 'Seleccione alguna ubicaci\xf3n en el mapa';
			//var combo_ag = document.getElementById('id_org');
			//var id_filtro = combo_ag.options[combo_ag.selectedIndex].value;

			var id_filtro = 0;
		}

		if ((accion == 'reporteOnLineOrgMO' || accion == 'reportePdfOrgMO' || accion == 'mapaOrgMO') && id_filtro == undefined){
			alert(txt_alert);
			return false;
		}
	}
	else if (filtro == 'conteo_presupuesto'){
		var combo_moneda = document.getElementById('id_moneda');
		id_filtro = 1;   //Moneda USD
		if (combo_moneda != undefined){
			id_filtro = combo_moneda.options[combo_moneda.selectedIndex].value;
		}

		//Intervalos de usuario
		var input_intervalos = document.getElementById('intervalos');
		var intervalos = '';
		if (input_intervalos != undefined && input_intervalos.value != ''){
			intervalos = input_intervalos.value;

			patron = /[^a-zA-Z\.]/;
			if (!patron.test(intervalos)){
				alert("Rangos no v\xe1lidos, recuerde que los limites deben estar separados por coma (,)");
				input_intervalos.focus();
				return false;
			}
		}

		//Fix height del div de resultados
		obj_result.style.height = 'auto';
	}

	var params = 'object='+accion+'&filtro='+filtro+'&id_filtro='+id_filtro+'&depto='+depto+'&ubicacion='+ubicacion+'&chart='+chart;
	
	if (accion == 'mapaOrgMO'){
		//Muestra result
		var obj_result = document.getElementById(id_div_ajax);
		obj_result.style.display = '';
		
		var iframe = document.getElementById('iframe_mapa');
		var iframe_src = 'mapa.php?case=org&filtro='+filtro+'&id_filtro='+id_filtro+'&id_depto_filtro='+ubicacion+'&id_org=';

		if (iframe == undefined){
			obj_result.innerHTML = '<iframe src='+iframe_src+' id="iframe_mapa" frameborder="0" width="100%" height="100%" style="background: #fff3c4;"></iframe>';	
		}
		else{
			iframe.style.display = '';
			iframe.src = iframe_src;
		}
	}
	else if(accion == 'reportePdfOrgMO'){
		location.href='download_pdf.php?c=4&'+params;
	}
	else{
		//Muestra result
		var obj_result = document.getElementById(id_div_ajax);
		obj_result.style.display = '';
	
		if (filtro == 'conteo_presupuesto'){
			params += '&intervalos='+intervalos;
		}

		getDataV1('OrgMOGen','t/ajax_data.php?' + params,'proy_gen');
	}
}

function generarReportePDF(){

	var id_depto = document.getElementById('id_depto').value;
	var id_mun = document.getElementById('id_muns').value;

	//c=2; genera minificha y pdf
	location.href = 'download_pdf.php?c=2&id_depto='+id_depto+'&id_mun='+id_mun;
	
	return false;
}

</script>
<div id="home_top"></div>
<div id="home">
	<ul id="home_item">
		<li class="item" id="filtro_proy">
			<div class="tit_home_org">SISTEMA DE CONSULTA DE ORGANIZACIONES SOCIALES</div><br />
			<ul id="links">
				<?
				$link_op = '?m_e=mo&m_g=consulta&accion=consultar&class=OrganizacionMO';
				?>
				<li>&raquo;&nbsp;<a href='<?=$link_op?>&caso=sector'>Consulta por sector</a></li>
				<li>&raquo;&nbsp;<a href='<?=$link_op?>&caso=poblacion'>Consulta por beneficiario</a></li>
				<li>&raquo;&nbsp;<a href='<?=$link_op?>&caso=cobertura'>Reporte por cobertura</a></li>
				<!--<li>&raquo;&nbsp;<a href='<?=$link_op?>&caso=conteo'>Reportes de conteo</a></li>-->
				<li>&nbsp;<img src="images/undaf/consulta/back.gif">&nbsp;<a href='#' onclick="history.back()">Regresar</a>&nbsp;</li>
			</ul>

			<?
			if ($caso != 'listado_completo'){
				echo '<div class="consulta_proy_body">';
				
				switch($caso){
					case 'sector':
						
						echo "<h2>Seleccione el sector de la organizaci&oacute;n&nbsp;</h2>";
						echo "<div class='consulta_proy_filtro'>";
							
						$secs = $sector_dao->GetAllArray("");

						foreach($secs as $s){
							echo "<p class='filtro_proy'>&nbsp;<input type='radio' name='id_filtro' value='$s->id'>&nbsp;$s->nombre_es</p>";
						}
							
						echo "</div>";
					break;
					
					case 'poblacion':
						
						echo "<h2>Seleccione la poblaci&oacute;n beneficiaria de la organizaci&oacute;n&nbsp;</h2>";
						echo "<div class='consulta_proy_filtro'>";
							
						$pobs = $pob_dao->GetAllArray("","","");

						foreach($pobs as $pob){
							echo "<p class='filtro_proy'>&nbsp;<input type='radio' name='id_filtro' value='$pob->id'>&nbsp;$pob->nombre_es</p>";
						}
							
						echo "</div>";
					break;
					
					case 'cobertura':
						
						echo "<h2>Reporte por cobertura&nbsp;</h2>";

						echo "</select><br />&nbsp;<br />";
						echo "<div class='consulta_proy_filtro'>Seleccione la ubicaci&oacute;n geogr&aacute;fica en el mapa <br /> inferior y use alguna opci&oacute;n de la derecha";
						echo "<div class='consulta_proy_ubi_txt'><h2>Organizaciones con Cobertura:<br /><span id='nombreDepto_filtro'>Nacional</span><span id='nombreMpio_filtro'></span></h2></div>";
						echo "</div>";
					break;
				}
				if (in_array($caso,array('sector','poblacion','cobertura'))){
					?>
					<div class='float_left'>
						<ul>
							<li class="consulta_proy_boton">
								<img src="images/undaf/consulta/reporte.png" /><span><a href='#' onclick="accionOrg('bar','reporteOnLineOrgMO','<?=$caso?>');return false;">Generar reporte en l&iacute;nea</a></span>
							</li>
							<!--
							<li id='boton_pdf' class="consulta_proy_boton"><img src="images/undaf/consulta/pdf.png" /><span><a href='#' onclick="accionOrg('bar','reportePdfOrgMO','<?=$caso?>');return false;">Descargar reporte PDF</a></span></li>
							<li class="consulta_proy_boton"><img src="images/undaf/consulta/grafica.png" /><span><a href='#' onclick="accionOrg('bar','graficaOrgMO','<?=$caso?>');return false;" id="link_grafica">Generar Gr&aacute;fica</a></span></li>
							<li class="consulta_proy_boton"><img src="images/undaf/consulta/mapa.png" /><span><a href='#' onclick="accionOrg('bar','mapaOrgMO','<?=$caso?>');return false;" id="link_mapa">Generar Mapa</a></span></li>
							-->
						</ul>	

					</div>
					<div class='float_left'>
						<ul class="consulta_proy_boton_ayuda">
							<li>
								<span>Use esta opci&oacute;n para generar un reporte de las organizaciones que cumplan con el criterio seleccionado</span>
							</li>
							<!--<li>
								<span>Igual que la anterior solo que genera un reporte PDF con las fichas completas para cada uno de las organizaciones</span>
							</li>
							<li>
								<span><br />Use esta opci&oacute;n para generar una gr&aacute;fica del n&uacute;mero de organizaciones por <?=str_replace("poblacion","poblaci&oacute;n",$caso)?></span>
							</li>
							<li>
								<span><br />Use esta opci&oacute;n para generar un mapa de conteo de organizaciones por municipios</span>
							</li>-->
						</ul>	

					</div>
				<?
				}
				else if ($caso == 'conteo'){
					?>
					<div class='float_left'>
						<ul>
							<li class="consulta_proy_boton_conteo">
								<img src="images/undaf/consulta/presupuesto.png" /><span><a href='#' onclick="accionOrg('bar','reporteOnLineConteoOrgMO','conteo_presupuesto');return false;">Cantidad de organizaciones por presupuesto</a></span>
							</li>
						</ul>	

					</div>
					<div class='float_left'>
						<ul class="consulta_proy_boton_ayuda">
							<li>
								<span>Use esta opci&oacute;n para generar un reporte de la cantidad de organizaciones por rango de presupuesto</span>
							</li>
						</ul>	
					</div>
				<?

				}
				?>
				<div style="height:10px;clear:both;"></div>
				<div id="proy_gen" style="display:none"><iframe id="iframe_mapa" frameborder="0" width="100%" height="100%" style="display:none;background: #fff3c4;"></iframe></div>
				
				<?
				if ($caso == 'cobertura'){ ?>

					<div class="consulta_proy_ubi_txt">
						<h2>Ubicaci&oacute;n Geogr&aacute;fica: 
							<span id='nombreDepto'>Todo Colombia</span><span id='separador_depto_mpio' style="display:none">&nbsp; --&gt;&nbsp;</span><span id='nombreMpio'></span>
						</h2>
						<p>
							Puede seleccionar en el mapa un Departemento (Cesar o Magdalena para este caso) o un Municipio el cual ser&aacute; usado como filtro de ubicaci&oacute;n
							geogr&aacute;fica para las opciones de Reporte, Gr&aacute;fica y Mapa.<br />
						</p>
						<p class="nota">
							<br />&raquo; Para seleccionar un Departamento, primero haga click sobre la opci&oacute;n <b>Departamento</b> en el mapa y luego haga click sobre el departamento deseado
						<br />
							&raquo; Para seleccionar un Municipio, primero haga click sobre la opci&oacute;n <b>Municipio</b> en el mapa, luego haga click sobre el departamento y luego seleccione el
							municipio deseado ubicandolo en el mapa o seleccionadolo del listado que aparecer&aacute;
						</p>
					</div>
					<div style="margin-left: 20px;">
						<? include("mapa_consulta.php") ?>
						<input type="hidden" id="id_depto" name="id_depto[]">
						<input type="hidden" id="id_muns" name="id_muns[]" disabled>
					</div>
				<?	
				}
				else{ ?>
					<input type="hidden" id="id_depto" name="id_depto[]">
					<input type="hidden" id="id_muns" name="id_muns[]" disabled>
				<? } ?>	
			<?
			}
			else{
				echo '<div id="proy_gen" style="display:none"></div>';
				?>
				<script>accionOrg('bar','listadoCompletoOrgMO','<?=$caso?>')</script>
				<?

			}
			?>
		</li>
	</ul>
</div>
<div id="home_bottom"></div>
