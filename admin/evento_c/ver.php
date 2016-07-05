<?
//LIBRERIAS
include("libs_evento_c.php");

//INICIALIZACION DE VARIABLES
$evento_vo = New EventoConflicto();
$evento_dao = New EventoConflictoDAO();
$municipio_vo = New Municipio();
$municipio_dao = New MunicipioDAO();
$depto_vo = New Depto();
$depto_dao = New DeptoDAO();
$actor_vo = New Actor();
$actor_dao = New ActorDAO();
$fuente_vo = New FuenteEventoConflicto();
$fuente_dao = New FuenteEventoConflictoDAO();
$subfuente_vo = New SubFuenteEventoConflicto();
$subfuente_dao = New SubFuenteEventoConflictoDAO();
$cat_vo = New CatEventoConflicto();
$cat_dao = New CatEventoConflictoDAO();
$subcat_vo = New SubCatEventoConflicto();
$subcat_dao = New SubCatEventoConflictoDAO();
$edad_dao = New EdadDAO();
$redad_dao = New RangoEdadDAO();
$estado_dao = New EstadoMinaDAO();
$condicion_dao = New CondicionMinaDAO();
$scondicion_dao = New SubCondicionDAO();
$sexo_dao = New SexoDAO();
$etnia_dao = New EtniaDAO();
$setnia_dao = New SubEtniaDAO();
$ocupacion_dao = New OcupacionDAO();


$id = $_GET["id"];
$evento_vo = $evento_dao->Get($id);

//Descripciones
$desc_evento = $evento_dao->getDescripcionEvento($id);

//Fuentes
$fuentes = $evento_dao->getFuenteEvento($id);
$num_fuentes = $fuentes['num'];

//Localizaciones
$locas = $evento_dao->getLocalizacionEvento($id);
$num_locas = $locas['num'];

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>SIDIH :: Detalle de Evento</title>
<link href="../../style/input.css" rel="stylesheet" type="text/css" />

<script src="../js/tabber.js"></script>
<script>
function showDivVictimas(id){
	div = document.getElementById('div_'+id);
	
	if (div.style.display == 'none'){
		div.style.display = '';
	}
	else{
		div.style.display = 'none';
	}
}
</script>
<body>
<script src="../../js/wz_tooltip.js"></script>
<div id="cont">
<!--DIV DE BUSQUEDA DE OCURRENCIAS DE SUBSUBACTORES -->
<div id="buscar_hijo" style="display:none;z-index:10;position:absolute;background-color:#efefef;width:280px;border:1px solid #000000">
	<br>&nbsp;&nbsp;<input type="text" id='s' name='s' class='textfield' size="25" onkeydown="document.getElementById('ocurrenciasActorHijo').style.display='';getDataV1('ocurrenciasActorHijo','ajax_data.php?object=ocurrenciasActor&numero_fila='+numero+'&case=hijo&s='+this.value,'ocurrenciasActorHijo')">
	<br><br><input type='radio' id="comience" name="donde" value='comience' checked>Que <b>comience</b> con&nbsp;<input type='radio' id="contenga" name="donde" value='contenga'>Que <b>contenga</b> a
	<br><br>
	<div id='ocurrenciasActorHijo' class='ocurrenciasActor' style='display:none'></div>
</div>

<!--DIV ADICIONAR VICTIMAS -->
<?
	
$d = 0;
$v = 0;
$top =150;
foreach($desc_evento['id'] as $id_deseven){	
	
	$dd = $d + 1;
	
	$victimas = $evento_dao->getVictimaDescripcionEvento($id_deseven);
	$num_vict_x_desc = $victimas['num'];
	
	if ($num_vict_x_desc > 0){
		
		$height = 210 * $num_vict_x_desc;
		$height_i = 220 * $num_vict_x_desc - 5*$num_vict_x_desc;
		
		?>
		<div id="div_victimas_<?=$d?>" style="display:none;z-index:10;position:absolute;width:800px;height:<?=$height?>px;top:<?=$top?>px;left:150px">
		<?			
		
		for ($i=0;$i<$num_vict_x_desc;$i++){
			//$top += 420;
			?>
				<table class="tabla_input_victima" cellpadding="5" cellspacing="0" id="tabla_vict_<?=$d?>_<?=$i?>;">
					<?
					if ($i == 0) { ?>
						<tr class="titulo_lista_victima">
							<td colspan="3" align="center">VICTIMAS DEL EVENTO</td>
							<td align="right"><a href='#' onclick='showDivVictimas("victimas_<?=$d?>");return false;'>Cerrar</a></td>
						</tr>
					<? } ?>
					<tr>
						<td><b>Cantidad</b></td>
						<td><?=$victimas['cant'][$i]?></td>
					</tr>
					<tr>
						<td><b>Grupo Etareo</b></td>
						<td>
							<?
							echo $victimas['nom_edad'][$i];
							?>
						</td>
						<td><b>Rango de Edad</b></td>
						<td id="comboBoxRangoEdad<?=$v?>">
							<?
							echo $victimas['nom_redad'][$i];
							?>
						</td>
					</tr>
					<tr>
						<td><b>Condici&oacute;n</b></td>
						<td>
							<?
							echo $victimas['nom_condicion'][$i];
							?>
						</td>
						<td><b>Estado</b></td>
						<td>
							<?
							echo $victimas['nom_estado'][$i];
							?>
						</td>
					</tr>
					<tr>
						<td><b>Sub Condici&oacute;n</b></td>
						<td id="comboBoxSubcondicion<?=$v?>">
							<?
							echo $victimas['nom_scondicion'][$i];
							?>	
						</td>
						
						<td><b>Sexo</b></td>
						<td>
							<?
							echo $victimas['nom_sexo'][$i];
							?>
						</td>
					</tr>
					<tr>
						<td><b>G. Poblacional</b></td>
						<td>
							<?
							echo $victimas['nom_etnia'][$i];
							?>
						</td>
						<td><b>Sub Etnia</b></td>
						<td id="comboBoxSubetnia<?=$v?>">
							<?
								echo $victimas['nom_setnia'][$i];
							?>
						</td>
					</tr>
					<tr>
						<td><b>Ocupaci&oacute;n</b></td>
						<td>
							<?
							echo $victimas['nom_ocupacion'][$i];
							?>
						</td>
					</tr>
				</table>
			<?
			$v++;
		}
		echo "</div>";
	}
	
	$top += 200;	
	
	$d++;
	
	

}	

?>
<div class="tabber">
	<div class="tabbertab">
		<h2>&nbsp;<img src="../images/evento_c/info_basica.gif" border="0"></h2><br>
		<table border="0" cellpadding="5" cellspacing="1" width="800" align="center">
			<tr class="titulo_lista"><td colspan="2" align="center">INFORMACION BASICA DEL EVENTO</td></tr>
		 	<tr>
				<td><b>ID del Evento:</b>&nbsp;<?=$id?></td>
				<td><b>Fecha Evento:</b>&nbsp;<?=$evento_vo->fecha_evento?>
				</td>
			</tr>
			<tr>
				<td align="left" colspan="2">
					<b>Resumen del Evento</b><br>
					<p><?=$evento_vo->sintesis?></p>
				</td>
			</tr>
		</table>
	</div>

	<div class="tabbertab" id="div_desc">
		<h2>&nbsp;<img src="../images/evento_c/desc.gif" border="0"></h2><br>
		<table border="0" cellpadding="5" cellspacing="1" width="950" align="center">
			<tr class="titulo_lista"><td colspan="2" align="center">DESCRIPCION DEL EVENTO</td></tr>
		</table>
		<?
		$d = 1;
		foreach ($desc_evento['id_cat'] as $id_cat){
			$dd = $d - 1;
			$id_de = $desc_evento['id'][$dd];
			
			
			$actores = $evento_dao->getActorEvento($id_de,1);
			$sactores = $evento_dao->getActorEvento($id_de,2);
			$ssactores = $evento_dao->getActorEvento($id_de,3);
			
			?>
			<br>
			<table border="0" cellpadding="5" cellspacing="1" width="950" id="tabla_desc_<?=$dd?>" align="center">
				<tr class="titulo_lista"><td colspan="3" align="center"><b>DESCRIPCION # <?=$d?></b></td></tr>			
				<tr>
					<td>&nbsp;</td>
					<td align="right" width="40%"><b>Categoria</b></td>
					<td>
						<?
						$vo = $cat_dao->Get($id_cat);
						echo $vo->nombre;
						?>
					</td>
				</tr>
				<tr>
					<td>
					<?
					$victimas = $evento_dao->getVictimaDescripcionEvento($id_de);
					$num_vict_x_desc = $victimas['num'];

					if ($num_vict_x_desc > 0){ ?>
						<a href="#" onClick="showDivVictimas('victimas_<?=$dd?>');return false;">Ver<br>V&iacute;ctimas</a></td>
					<?
						
					}
					else	echo "&nbsp;";
					?>
						
					<td align="right"><b>Subcategoria</td>
					<td id="comboBoxSubcategoria">
						<?
						$vo = $subcat_dao->Get($desc_evento['id_scat'][$dd]);
						echo $vo->nombre;
						?>
				</tr>
				<tr>
					<td>&nbsp;</td>
					<td align="right"><b>Actor/Presunto Perpretador</b></td>
					<td>
						<?
						foreach ($actores['nombre'] as $nombre){
							echo "-&nbsp;$nombre<br>";
						}
						?>
					</td>
				</tr>				
				<tr>
					<td>&nbsp;</td>
					<td align="right"><b>Sub Actor/Presunto Perpretador</b></td>
					<td>
						<?
						foreach ($sactores['nombre'] as $nombre){
							echo "-&nbsp;$nombre<br>";
						}
						?>
					</td>
				</tr>				
				<tr>
					<td>&nbsp;</td>
					<td align="right"><b>Sub-Sub Actor/Presunto Perpretador</b></td>
					<td>
						<?
						foreach ($ssactores['nombre'] as $nombre){
							echo "-&nbsp;$nombre<br>";
						}
						?>
					</td>
				</tr>				
			</table>
			<br>
			<?
			$d++;
		}
		?>

	</div>

     <div class="tabbertab">
		<h2>&nbsp;<img src="../images/evento_c/fuente.gif" border="0"></h2><br>
		<table border="0" cellpadding="5" cellspacing="2" id="tabla_fuente" align="center">
			<tr class="titulo_lista"><td colspan="6" align="center">FUENTE DEL EVENTO</td></tr>
			<tr>
				<td><img src="../images/spacer.gif" width="100" height="1"></td>
				<td><img src="../images/spacer.gif" width="180" height="1"></td>
				<td><img src="../images/spacer.gif" width="200" height="1"></td>
				<td><img src="../images/spacer.gif" width="130" height="1"></td>
				<td><img src="../images/spacer.gif" width="80" height="1"></td>
			</tr>
			<tr class="tabla_input_desc">
				<td align="center"><b>Tipo de Fuente</b></td>
				<td align="center"><b>Fuente</b></td>
				<td align="center"><b>Descripci&oacute;n del Evento</b>&nbsp;<img src="../images/icono_info.png" onmouseover="Tip('<b>Descripción del Evento</b><br>Descripción del evento según reporte de la fuente')" onmouseout="UnTip()"></td>
				<td align="center"><b>Fecha Fuente&nbsp;</b>&nbsp;<img src="../images/icono_info.png" onmouseover="Tip('<b>Fecha Fuente</b><br>Fecha del evento reportado por la fuente')" onmouseout="UnTip()"></td>
				<td align="center"><b>Medio</b>&nbsp;&nbsp;<img src="../images/icono_info.png" onmouseover="Tip('<b>Medio</b><br>Descripción del medio que uso para la publicación (Página, columna, etc)<br><br><i>(Nombre del artículo, Página, sección, link)</i>')" onmouseout="UnTip()"></td>
			</tr>
			<?
		
			$l = 0;
			foreach ($fuentes['id_fuente'] as $id_fuente){
				?>
				<tr class="tabla_input_desc">
					<td>
						<?
						$vo = $fuente_dao->Get($id_fuente);
						echo $vo->nombre;
						?>
					</td>
					<td id="comboBoxSubfuente">
						<?
						$vo = $subfuente_dao->Get($fuentes['id_sfuente'][$l]);
						echo $vo->nombre;
						?>
					</td>
					<td>
						<?=$fuentes['desc'][$l]?>
					</td>
					<td>
						<?=$fuentes['fecha'][$l]?>
					</td>
					<td>
						<?=$fuentes['medio'][$l]?>
					</td>
				</tr>
				<?
				$l++;
			}	
			?>
		</table>
     </div>

     <div class="tabbertab">
	  <table border="0" cellpadding="5" cellspacing="1" width="950" id="tabla_depto" align="center">
	  <h2>&nbsp;<img src="../images/evento_c/localizacion.gif" border="0"></h2><br>
	  	<tr class="titulo_lista"><td colspan="6" align="center">LOCALIZACION GEOGRAFICA DEL EVENTO</td></tr>
			<tr class="tabla_input_desc">
				<td width="25%"><b>Departamento</b></td>
				<td width="25%"><b>Municipio</b></td>
				<td width="30%"><b>Lugar</b></td>
			</tr>
			<?

			$l = 0;
			foreach ($locas['mpios'] as $id_mun){
				$mun = $municipio_dao->Get($id_mun);
				?>
				<tr class="tabla_input_desc">
					<td>
						<?
						$vo = $depto_dao->Get($mun->id_depto);
						echo $vo->nombre;
						?>
					</td>
					<td id="comboBoxMunicipio">
						<?
						echo $mun->nombre
						?>
					</td>
					<td><? if (isset($locas['lugar'][$l]))	echo $locas['lugar'][$l]; ?></td>
				</tr>					
				<?
				$l++;
			}
			?>
		</table>
     </div>
</div>
</body>
</html>