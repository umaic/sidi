<?
//LIBRERIAS
include_once("consulta/lib/libs_proyecto.php");

//CONSULTA LOS PROYECTOS ASOCIADOS A LA ORG. DEL USUARIO POR ESTADO
$proy_dao = new ProyectoDAO();
$org_dao = new OrganizacionDAO();
$tema_dao = New TemaDAO();
$pob_dao = new PoblacionDAO();

//CASO
$caso = (isset($_GET["caso"])) ? $_GET["caso"] : '';

?>
<script src="t/js/tabber.js" type="text/javascript"></script>
<script src="js/ajax.js" type="text/javascript"></script>
<script src="js/roundies_0.0.1a-min.js" type="text/javascript"></script>
<script type="text/javascript">
    DD_roundies.addRule('.tit_reporte', 8);
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
		if ('<?=$_GET["caso"]?>' == 'cobertura'){
			document.getElementById('link_grafica').disabled = true;
			document.getElementById('link_mapa').disabled = true;
		}
		else{
			document.getElementById('link_grafica').disabled = false;
			document.getElementById('link_mapa').disabled = false;
		}
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
function accionProyecto(chart,accion,filtro){
	
	var ubicacion = 0;
	var depto = 0;
	var id_filtro = 0;
	var id_div_ajax = 'proy_gen';
	
	//Div donde se muestran los resultados
	var obj_result = document.getElementById(id_div_ajax);
	
	if (filtro != 'listado_completo'){
		ubicacion = 0;  // Para toda Colombia
		depto = 2;

		var id_depto = document.getElementById('id_depto').value;
		var id_muns = document.getElementById('id_muns').value;

		if (id_depto != 0){
			ubicacion = id_depto;
			depto = 1;
		}
		if (id_muns != ''){
			ubicacion = id_muns;
			depto = 0;
		}
	}	

	if (filtro != 'listado_completo' && filtro!='conteo_presupuesto'){
		
		var id_filtro = getRadioCheck(document.getElementsByName('id_filtro'));

		
		switch (filtro){
			case 'tema':
				var txt_alert = "Seleccione alg\xfan Tema";
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
			var combo_ag = document.getElementById('id_org');
			var id_filtro = combo_ag.options[combo_ag.selectedIndex].value;
		}

		if ((accion == 'reporteOnLineProyectoUndaf' || accion == 'reportePdfProyectoUndaf' || accion == 'mapaProyectoUndaf' ) && id_filtro == undefined){
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
	
	if (accion == 'mapaProyectoUndaf'){
		//Muestra result
		var obj_result = document.getElementById(id_div_ajax);
		obj_result.style.display = '';
		
		var iframe = document.getElementById('iframe_mapa');
		var iframe_src = 'mapa.php?case=proyecto_undaf&filtro='+filtro+'&id_filtro='+id_filtro+'&id_depto_filtro='+ubicacion+'&id_proy=';

		if (iframe == undefined){
			obj_result.innerHTML = '<iframe src='+iframe_src+' id="iframe_mapa" frameborder="0" width="100%" height="100%" style="background: #fff3c4;"></iframe>';	
		}
		else{
			iframe.style.display = '';
			iframe.src = iframe_src;
		}
	}
	else if(accion == 'reportePdfProyectoUndaf'){
		location.href='download_pdf.php?c=4&'+params;
	}
	else{
		//Muestra result
		var obj_result = document.getElementById(id_div_ajax);
		obj_result.style.display = '';
	
		if (filtro == 'conteo_presupuesto'){
			params += '&intervalos='+intervalos;
		}

		getDataV1('proyGen','t/ajax_data.php?' + params,'proy_gen');
	}
	/*
	switch (accion){
		case 'reporteOnLineProyectoUndaf':
			getDataV1('proyGen','t/ajax_data.php?' + params,'proy_gen');
		break;

		case 'graficaProyectoUndaf':
			getDataV1('proyGen','t/ajax_data.php?' + params,'proy_gen');
		break;

		case 'mapaProyectoUndaf':
			var iframe = document.getElementById('iframe_mapa');
			var iframe_src = 'mapa.php?case=proyecto_undaf&filtro='+filtro+'&id_filtro='+id_filtro+'&id_depto_filtro='+ubicacion+'&id_proy=';

			if (iframe == undefined){
				obj_result.innerHTML = '<iframe src='+iframe_src+' id="iframe_mapa" frameborder="0" width="100%" height="100%" style="background: #fff3c4;"></iframe>';	
			}
			else{
				iframe.style.display = '';
				iframe.src = iframe_src;
			}
			
		break;
	}
	*/

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
			<div class="tit_reporte">SISTEMA DE CONSULTA DE INICIATIVAS, PROYECTOS Y PROGRAMAS</div><br />
			<ul id="links">
				<?
				$link_op = '?m_e=proyecto_undaf&m_g=consulta&accion=consultar&class=ProyectoUndaf';
				?>
				<li>&nbsp;&nbsp;&raquo;&nbsp;<a href='?m_e=home&caso=listado_completo'>Base de datos completa</a></li>
				<li>&raquo;&nbsp;<a href='<?=$link_op?>&caso=agencia'>Consulta por agencia</a></li>
				<li>&raquo;&nbsp;<a href='<?=$link_op?>&caso=tema'>Consulta por tem&aacute;tica</a></li>
				<li>&raquo;&nbsp;<a href='<?=$link_op?>&caso=cobertura'>Reporte por territorio</a></li>
				<li>&raquo;&nbsp;<a href='<?=$link_op?>&caso=conteo'>Reportes de conteo</a></li>
				<li>&nbsp;<img src="images/undaf/consulta/back.gif">&nbsp;<a href='#' onclick="history.back()">Regresar</a>&nbsp;</li>
			</ul>

			<?
			if ($caso != 'listado_completo'){
				echo '<div class="consulta_proy_body">';
				
				switch($caso){
					case 'tema':
						
						echo "<h2>Seleccione el tema del proyecto&nbsp;</h2>";
						echo "<div class='consulta_proy_filtro'>";
							
						$id_c_undaf = 1;
						$t_undaf = $tema_dao->GetAllArray("id_clasificacion = $id_c_undaf AND id_papa=0");

						foreach($t_undaf as $t){
							echo "<p class='filtro_proy'>&nbsp;<input type='radio' name='id_filtro' value='$t->id'>&nbsp;$t->nombre</p>";
						}
							
						echo "</div>";
					break;
					
					case 'poblacion':
						
						echo "<h2>Seleccione la poblaci&oacute;n beneficiaria del proyecto&nbsp;</h2>";
						echo "<div class='consulta_proy_filtro'>";
							
						$pobs = $pob_dao->GetAllArray("","","");

						foreach($pobs as $pob){
							echo "<p class='filtro_proy'>&nbsp;<input type='radio' name='id_filtro' value='$pob->id'>&nbsp;$pob->nombre_es</p>";
						}
							
						echo "</div>";
					break;
					
					case 'agencia':
						
						echo "<h2>Seleccione la Agencia ejecutora</h2>";
						echo "<div class='consulta_proy_filtro'>";
							
						$papas = $org_dao->GetAllArrayID('id_tipo=4 AND (id_org_papa=0 OR id_org=id_org_papa)','','');
						foreach($papas as $id_papa){

							$num_papa = count($proy_dao->GetIDByEjecutor(array($id_papa),'','',''));

							$nom_papa = $org_dao->GetName($id_papa);
							$num_txt = "( $num_papa Proys )";
							$nom_papa = $org_dao->GetName($id_papa).' - '.$org_dao->GetFieldValue($id_papa,'sig_org');
							
							if ($num_papa > 0){
								echo "<p class='filtro_proy'>";
								echo "<input type='radio' name='id_filtro' value='$id_papa'>&nbsp;";
								echo $nom_papa."<br />".$num_txt."</p>";
							}
							

							$hijos = $org_dao->GetAllArrayID('id_org_papa='.$id_papa,'','');

							foreach($hijos as $id){
								
								$num_hijo = count($proy_dao->GetIDByEjecutor(array($id),'','',''));
								$num_txt = "( $num_hijo Proys )";
								$nom_hijo = $org_dao->GetName($id);
								
								
								if ($num_hijo > 0){
									echo "<p class='filtro_proy'>&nbsp;&nbsp;l__&nbsp;&nbsp;";
									echo "<input type='radio' name='id_filtro' value='$id'>&nbsp;";
									echo $nom_hijo."<br />".$num_txt."</p>";
								}
								
							}
						}
                                                /*
						$orgs = $org_dao->GetAllArray("id_tipo = 4","","");

						foreach($orgs as $org){
							//echo "<p class='filtro_proy'>&nbsp;<input type='radio' name='id_filtro' value='$org->id'>&nbsp;$org->nom</p>";
						}
                                                */
							
						echo "</div>";
					break;

					case 'cobertura':
						
						echo "<h2>Reporte por cobertura&nbsp;</h2>";
						echo "<div><b>Filtrar por Agencia</b>:&nbsp;";
						echo "<select id='id_org' class='select'>";
						echo "<option value=0>Seleccione alguna...</option>";
						$papas = $org_dao->GetAllArrayID('id_tipo=4 AND id_org_papa=0','','');
						foreach($papas as $id_papa){

							$num_papa = count($proy_dao->GetIDByEjecutor(array($id_papa)));
							$num_txt = '';
							$style = '';
							if ($num_papa > 0){

								$nom_papa = $org_dao->GetName($id_papa);
								echo "<option value=$id_papa >".$num_txt.$nom_papa."</option>";

								$hijos = $org_dao->GetAllArrayID('id_org_papa='.$id_papa,'','');

								foreach($hijos as $id){
									$num_hijo = count($proy_dao->GetIDByEjecutor(array($id)));
									$num_txt = '';
									$style = '';
									if ($num_hijo > 0){
										$nom_hijo = $org_dao->GetName($id);
										echo "<option value=$id>&nbsp;&nbsp;l__&nbsp;".$num_txt.$nom_hijo."</option>";
									}	
								}
							}
						}

						echo "</select><br />&nbsp;<br />";
						//echo "</select><br /><font class='nota'>Se resaltan las agencias con proyectos</font>&nbsp;<br />&nbsp;<br />";
						echo "<div class='consulta_proy_filtro'>Seleccione la ubicaci&oacute;n geogr&aacute;fica en el mapa <br /> inferior y use alguna opci&oacute;n de la derecha";
						echo "<div class='consulta_proy_ubi_txt'><h2>Proyectos con Cobertura:<br /><span id='nombreDepto_filtro'>Nacional</span><span id='nombreMpio_filtro'></span></h2></div>";
						echo "</div>";
						echo "</div>";
					break;
				}
				if (in_array($caso,array('tema','poblacion','cobertura','agencia'))){
					?>
					<div class='float_left'>
						<ul>
							<!--<li class="consulta_proy_boton"><img src="images/undaf/consulta/reporte.png" /><span><a href='#' onclick="accionProyecto('bar','listadoProyectoUndaf','<?=$caso?>');return false;">Generar listado</a></span></li>-->
							<li class="consulta_proy_boton">
								<img src="images/undaf/consulta/reporte.png" /><span><a href='#' onclick="accionProyecto('bar','reporteOnLineProyectoUndaf','<?=$caso?>');return false;">Generar reporte en l&iacute;nea</a></span>
							</li>
							<li id='boton_pdf' class="consulta_proy_boton"><img src="images/undaf/consulta/pdf.png" /><span><a href='#' onclick="accionProyecto('bar','reportePdfProyectoUndaf','<?=$caso?>');return false;">Descargar reporte PDF</a></span></li>
							<li class="consulta_proy_boton"><img src="images/undaf/consulta/grafica.png" /><span><a href='#' onclick="accionProyecto('bar','graficaProyectoUndaf','<?=$caso?>');return false;" id="link_grafica">Generar Gr&aacute;fica</a></span></li>
							<li class="consulta_proy_boton"><img src="images/undaf/consulta/mapa.png" /><span><a href='#' onclick="accionProyecto('bar','mapaProyectoUndaf','<?=$caso?>');return false;" id="link_mapa">Generar Mapa</a></span></li>
						</ul>	

					</div>
					<div class='float_left'>
						<ul class="consulta_proy_boton_ayuda">
							<li>
								<span>Use esta opci&oacute;n para generar un reporte de los proyectos que cumplan con el criterio seleccionado</span>
							</li>
							<li>
								<span>Igual que la anterior solo que genera un reporte PDF con las fichas completas para cada uno de los proyectos</span>
							</li>
							<li>
								<span><br />Use esta opci&oacute;n para generar una gr&aacute;fica del n&uacute;mero de proyectos por <?=str_replace("poblacion","poblaci&oacute;n",$caso)?></span>
							</li>
							<li>
								<span><br />Use esta opci&oacute;n para generar un mapa de conteo de proyectos por municipios</span>
							</li>
						</ul>	

					</div>
				<?
				}
				else if ($caso == 'conteo'){
					?>
					<div class='float_left'>
						<ul>
							<li class="consulta_proy_boton_conteo">
								<img src="images/undaf/consulta/presupuesto.png" /><span><a href='#' onclick="accionProyecto('bar','reporteOnLineConteoProyectoUndaf','conteo_presupuesto');return false;">Cantidad de proyectos por presupuesto</a></span>
							</li>
						</ul>	

					</div>
					<div class='float_left'>
						<ul class="consulta_proy_boton_ayuda">
							<li>
								<span>Use esta opci&oacute;n para generar un reporte de la cantidad de proyectos por rango de presupuesto</span>
							</li>
						</ul>	
					</div>
				<?

				}
				?>
				<!--<div id="proy_gen" style="display:none;"><iframe id="iframe_mapa" frameborder="0" width="100%" height="100%" style="display:none;background: #fff3c4;"></iframe></div>-->
				<div id="proy_gen" style="display:none;"></div>
				<div class="consulta_proy_ubi_txt">
					<h2>Ubicaci&oacute;n Geogr&aacute;fica: 
						<span id='nombreDepto'>Todo Colombia</span><span id='separador_depto_mpio' style="display:none">&nbsp;--&gt;&nbsp;</span><span id='nombreMpio'></span>
					</h2>
					<p>
						Puede seleccionar en el mapa un Departemento o un Municipio el cual ser&aacute; usado como filtro de ubicaci&oacute;n
						geogr&aacute;fica para las opciones de Reporte, Gr&aacute;fica y Mapa.<br />
					</p>
					<p class="nota">
						<br />&raquo; Para seleccionar un Departamento, primero haga click sobre la opci&oacute;n <b>Departamento</b> en el mapa y luego haga click sobre el departamento deseado
					<br />
						&raquo; Para seleccionar un Municipio, primero haga click sobre la opci&oacute;n <b>Municipio</b> en el mapa, luego haga click sobre el departamento y luego seleccione el
						municipio deseado ubicandolo en el mapa o seleccionadolo del listado que aparecer&aacute;
					</p>
				</div>
				<div>
					<? include("mapa_consulta.php") ?>
					<input type="hidden" id="id_depto" name="id_depto[]">
					<input type="hidden" id="id_muns" name="id_muns[]" disabled>
				</div>
			<?
			}
			else{
				echo '<div id="proy_gen" style="display:none"></div>';
				?>
				<script>accionProyecto('bar','listadoCompletoProyectoUndaf','<?=$caso?>')</script>
				<?

			}
			?>


			<!--<div id="reporte_proy" class="tabber">
				<div class="tabbertab">
					<h2>Por Tema</h2>
					<div>lero</div>

				</div>
				<div class="tabbertab">
					<h2>Por Cobertura</h2>
					<div>lero cob</div>

				</div>
			</div>-->
		</li>
	</ul>
</div>
<div id="home_bottom"></div>
