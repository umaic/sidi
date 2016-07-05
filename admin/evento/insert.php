<?
//INICIALIZACION DE VARIABLES
$evento_vo = New Evento();
$evento_dao = New EventoDAO();
$municipio_vo = New Municipio();
$municipio_dao = New MunicipioDAO();
$depto_vo = New Depto();
$depto_dao = New DeptoDAO();
$region_vo = New Region();
$region_dao = New RegionDAO();
$poblado_vo = New Poblado();
$poblado_dao = New PobladoDAO();
$resguardo_vo = New Resguardo();
$resguardo_dao = New ResguardoDAO();
$parque_nat_vo = New ParqueNat();
$parque_nat_dao = New ParqueNatDAO();
$div_afro_vo = New DivAfro();
$div_afro_dao = New DivAfroDAO();
$actor_vo = New Actor();
$actor_dao = New ActorDAO();
$tipo_evento_dao = New TipoEventoDAO();
$tipo_evento_vo = New TipoEvento();
$cat_tipo_evento_dao = New CatTipoEventoDAO();
$cat_tipo_evento_vo = New CatTipoEvento();
$cons_hum_dao = New ConsHumDAO();
$cons_hum_vo = New ConsHum();
$riesgo_hum_dao = New RiesgoHumDAO();
$riesgo_hum_vo = New RiesgoHum();
$conn = MysqlDb::getInstance();

$error_cobertura = 1;

if (isset($_GET["accion"])){
	$accion = $_GET["accion"];
}
else if (isset($_POST["accion"])){
	$accion = $_POST["accion"];
}

$fecha = getdate();
$evento_vo->fecha_registro = $fecha["year"]."-".$fecha["mon"]."-".$fecha["mday"];

$calendar = new DHTML_Calendar('js/calendar/','es', 'calendar-win2k-1', false);
$calendar->load_files();

$id_depto = "";
$id_depto_s = "";
$id_cat = 0;
$chk_conf = "";
if (isset($_GET['id_depto'])){
    $id_depto_s = $_GET['id_depto'];
	$id_depto = split(",",$_GET['id_depto']);
    $num_deptos = count($id_depto);
}

if (isset($_GET['id_mun'])){
    $id_mun_s = $_GET['id_mun'];
	$id_mun = split(",",$_GET['id_mun']);
	$evento_vo->id_muns = $id_mun;
}

$id_tipo = "";
$id_tipo_s = "";
if (isset($_GET['id_tipo'])){
    $id_tipo_s = $_GET['id_tipo'];
	$id_tipo = split(",",$_GET['id_tipo']);
    $num_tipos = count($id_tipo);
}

if (isset($_GET['id_cat'])){
	$id_cat = $_GET['id_cat'];
}


//Caso de Actualizacion
if (isset($_GET["accion"]) && $_GET["accion"] == "actualizar"){
	$id = $_GET["id"];
	$evento_vo = $evento_dao->Get($id);

	if (!isset($_GET["id_depto"])){
	  	$id_depto = $evento_vo->id_deptos;
	  	$id_depto_s = implode(",",$id_depto);
	}

	if (!isset($_GET["id_tipo"])){
	  	$id_tipo = $evento_vo->id_tipo;
	  	$id_tipo_s = implode(",",$id_tipo);
	}


	$id_cat = $evento_vo->id_cat;
	if ($evento_vo->conf == 1)	 $chk_conf = "checked";

	$num_deptos = count($id_depto);
}


//PASO 4. COBERTURA GEOGRAFICA
if (isset($_POST["submit"])){
	$id = $evento_dao->GetMaxID();

	$evento_vo = $evento_dao->Get($id);
	$id_depto = $evento_vo->id_deptos;
	$num_deptos = count($id_depto);
}

?>

<script>
function enviar_cat(url_href){
  selected = new Array();
	ob = document.getElementById('id_mun');
	for (var i = 0; i < ob.options.length; i++){
	  if (ob.options[ i ].selected)
		  selected.push(ob.options[ i ].value);
	}
	var url = selected.join(",");

  	location.href = url_href+'&id_mun='+url;
}

function enviar_tipo(url_href){
  selected = new Array();
	ob = document.getElementById('id_tipo');
	for (var i = 0; i < ob.options.length; i++){
	  if (ob.options[ i ].selected)
		  selected.push(ob.options[ i ].value);
	}
	var url = selected.join(",");

	if (selected.length == 0){
	  alert("Debe seleccionar algún Tipo");
	}
	else{
  	location.href = url_href+'&id_tipo='+url;
	}
}
</script>

<form action="<?=$_SERVER['PHP_SELF']?>" method="POST">
	<table border="0" cellpadding="5" cellspacing="1" width="750" align="center" class="tabla_consulta">
	  <tr><td align="center" class="titulo_lista" colspan='2'><b><?=strtoupper($accion)?> EVENTO</b></td></tr>
		<? if (!isset($_POST["submit"]) && !isset($_GET["actualizar_cobertura"])){ ?>
			<tr>
				<!-- LOCALIZACION : INICIO -->
				<td>
					<table border="0" cellpadding="5" cellspacing="1" width="100%" align="center" class="tabla_consulta">
						<tr><td class="titulo_lista" colspan="2">1. DEPARTAMENTO (S) DONDE SUCEDIO EL EVENTO</td></tr>
						<tr>
							<td width="300"><b>Departamento</b></td>
				     		<td>
							 	<select id="id_depto" name="id_depto[]" multiple size="10" class="select">
									<?
					    			//DEPTO
					    			$depto_dao->ListarCombo('combo',$id_depto,'');
					    			?>
				    			</select>
			  				</td>
			  			</tr>
			  			<tr><td>&nbsp;</td></tr>
			  			<? if (!isset($_GET["id_depto"])){
						    if ($accion == "insertar"){ ?>
					  			<tr>
					  				<td align="center" colspan="2">
					  					<input type="button" value="Siguiente" class="boton" onclick="enviar_deptos('index.php?accion=<?=$accion?>&class=EventoDAO&method=Listar&param=');return false;">
					  				</td>
					  			</tr>
					  			<?
							}
							else if ($accion == "actualizar"){
								?>
					  			<tr>
					  				<td align="center" colspan="2">
					  					<input type="button" value="Siguiente" class="boton" onclick="enviar_deptos('index.php?accion=<?=$accion?>&class=EventoDAO&method=Listar&param=&id=<?=$id?>');return false;">
					  				</td>
					  			</tr>
					  			<?
							}
							?>
			  			<? } ?>
				    	<? if (isset($_GET['id_depto']) || $accion == "actualizar"){ ?>
			  			<tr>
							<td><b>Municipio</b></td>
							<td>
								<select id="id_mun" name="id_muns[]" multiple size="10" class="select">
									<?
									//MUNICIPIO
									for($d=0;$d<$num_deptos;$d++){
										$id_d = $id_depto[$d];
										$depto = $depto_dao->Get($id_d);
										$muns = $municipio_dao->GetAllArray('ID_DEPTO ='.$id_d);
										echo "<option value='' disabled>-------- ".$depto->nombre." --------</option>";
										foreach ($muns as $mun){
										  echo "<option value='".$mun->id."'";
										  if (in_array($mun->id,$evento_vo->id_muns))	echo " selected ";
										  echo ">".$mun->nombre."</option>";
										}

									}
									?>
								</select>
							</td>
				        </tr>
					</table>
				</td>
				<!-- LOCALIZACION : FIN -->
			</tr>
			<tr>
				<td>
					<table border="0" cellpadding="5" cellspacing="1" width="100%" align="center" class="tabla_consulta">
						<tr><td class="titulo_lista" colspan="2">1. DATOS BASICOS</td></tr>
						<tr>
						  <td width="300">
						  	<b>Categoria</b>
						  </td>
						  <td>
							  <select id="id_cat" name="id_cat" class="select" onchange="enviar_cat('index.php?accion=insertar&id_depto=<?=$id_depto_s?>&id_cat='+this.value)">
							  <option value="">Seleccione alguna...</option>
							  <?
								//CATEGOTIA
								$cat_tipo_evento_dao->ListarCombo('combo',$id_cat,'');
								?>
								</select>
							</td>
						</tr>
							<? if (isset($_GET['id_cat']) || $accion == "actualizar"){ ?>
								<tr>
									<td>
										<b>Tipo</b>
									</td>
									<td>
										<select id="id_tipo" name="id_tipo[]" multiple size="8" class="select">
										<?
										//TIPO
										$tipo_evento_dao->ListarCombo('combo',$id_tipo,'ID_CAT_TIPO_EVE='.$id_cat.' AND ID_TIPOEVE_ID_TIPO_EVE = 0');
										?>
										</select>
									</td>
								</tr>
								<?
								//if (!isset($_GET["id_tipo"])){
									if ($accion == "insertar"){ ?>
							  			<tr>
							  				<td align="center" colspan="2">
							  					<input type="button" value="Siguiente" class="boton" onclick="enviar_tipo('index.php?accion=<?=$accion?>&id_depto=<?=$id_depto_s?>&&id_mun=<?=$id_mun_s?>&id_cat=<?=$id_cat?>');return false;">
							  				</td>
							  			</tr>
							  			<?
									}
									else if ($accion == "actualizar"){
										?>
							  			<tr>
							  				<td align="center" colspan="2">
							  					<input type="button" value="Siguiente" class="boton" onclick="enviar_tipo('index.php?accion=<?=$accion?>&id_depto=<?=$id_depto_s?>&id=<?=$id?>&id_cat=<?=$id_cat?>');return false;">
							  				</td>
							  			</tr>
							  			<?
									}
								//}
								if (isset($_GET['id_tipo']) || $accion == "actualizar"){ ?>
								<tr>
									<td>
										<b>Sub Tipo</b>
									</td>
									<?
									$sub_tipos = $tipo_evento_dao->GetAllArray('ID_TIPOEVE_ID_TIPO_EVE IN('.$id_tipo_s.')');
									$num_sub_tipos = count($sub_tipos);

									if ($num_sub_tipos > 0){
										?>
										<td>
											<select id="id_sub_tipo" name="id_sub_tipo[]" multiple size="<?=$num_sub_tipos?>" class="select">
											<?
											//SUB_TIPO
											$tipo_evento_dao->ListarCombo('combo',$evento_vo->id_tipo,'ID_TIPOEVE_ID_TIPO_EVE IN('.$id_tipo_s.')');
											?>
											</select>
										</td>
										<?
									}
									else { ?>

									  <td><b>No Existe Sub Tipo</b><input type="hidden" name="id_sub_tipo[]"></td>
									  <?
									} ?>
								</tr>
								<tr>
									<?
									//ACTORES PARA EVENTOS DE TIPO CRISIS H.
									if ($id_cat == 1){ ?>
									<td>
										<b>Actor (es)</b>
									</td>
									<td>
										<select name="id_actores[]" multiple size="8" class="select">
										<?
										//ACTORES
										$actor_dao->ListarCombo('combo',$evento_vo->id_actores,'');
										?>
										</select>
									</td>
									<? } ?>
								</tr>
								<tr>
									<td>
										<b>Descripción del Evento</b>
									</td>
									<td>
										<textarea rows="15" cols="90" id="desc" name="desc" class="area"><?=$evento_vo->desc;?></textarea>
									</td>
								</tr>
								<tr>
								  <td><b>Fecha en la que sucedió el evento </b>
											</td>
								  <td>
											<? $calendar->make_input_field(
								       // calendar options go here; see the documentation and/or calendar-setup.js
								       array('firstDay'       => 1, // show Monday first
								             'ifFormat'       => '%Y-%m-%d',
								             'timeFormat'     => '12'),
								       // field attributes go here
								       array('class'       => 'textfield',
														       'value'			 => $evento_vo->fecha_evento,
								             'name'        => 'fecha_evento'));
											?>
									</td>
								</tr>
								<tr>
								  <td><b>Fecha de registro del evento </b>
											</td>
								  <td>
											<? $calendar->make_input_field(
								       // calendar options go here; see the documentation and/or calendar-setup.js
								       array('firstDay'       => 1, // show Monday first
								             'ifFormat'       => '%Y-%m-%d',
								             'timeFormat'     => '12'),
								       // field attributes go here
								       array('class'       => 'textfield',
														       'value'	=> $evento_vo->fecha_registro,
								             'name'        => 'fecha_registro'));
											?>
									</td>
								</tr>
								<tr>
								  <td><b>Fuente de Información</b></td>
								  <td>
									  <input type="text" id="fuente" name="fuente" size="30" value="<?=$evento_vo->fuente;?>" class="textfield" />
									</td>
								</tr>
								<tr>
								  <td><b>Lugar</b></td>
								  <td>
									  <input type="text" id="lugar" name="lugar" size="30" value="<?=$evento_vo->lugar;?>" class="textfield" />
									</td>
								</tr>
								<tr>
								  <td><b>El Evento está confirmado?</b></td>
								  <td>
									  <input type="checkbox" id="conf" name="conf" value="1" <?=$chk_conf?>/>
									</td>
								</tr>
						</table>
					</td>
				</tr>
				<!-- DATOS GENERALES : FIN -->

				<!-- CONSECUENCIAS : INICIO -->
				<tr>
					<td>
						<table border="0" cellpadding="5" cellspacing="1" width="100%" align="center" class="tabla_consulta">
							<tr><td class="titulo_lista" colspan="2">3. CONSECUENCIAS HUMANITARIAS</td></tr>
							<tr>
							  <td width="300">
							  	<b>Consecuencia (S)</b>
							  </td>
							  <td>
								  <select name="id_cons[]" multiple size="8" class="select">
								  <?
									//CONSECUENCIAS HUM.
									$cons_hum_dao->ListarCombo('combo',$evento_vo->id_cons,'');
									?>
									</select>
								</td>
							<tr>
							  <td>
							  	<b>DESCRIPCIÓN</b>
							  </td>
							  <td>
								  <textarea rows="10" cols="60" name="desc_cons_hum" class="area"><?=$evento_vo->desc_cons_hum;?></textarea>
								</td>
							</tr>
						</table>
					</td>
				</tr>
				<!-- RIESGOS : INICIO -->
				<tr>
					<td>
						<table border="0" cellpadding="5" cellspacing="1" width="100%" align="center" class="tabla_consulta">
							<tr><td class="titulo_lista" colspan="2">3. RIESGOS HUMANITARIOS</td></tr>
							<tr>
							  <td width="300">
							  	<b>Riesgo (S)</b>
							  </td>
							  <td>
								  <select name="id_riesgos[]" multiple size="8" class="select">
								  <?
									//RIESGOS HUM.
									$riesgo_hum_dao->ListarCombo('combo',$evento_vo->id_riesgos,'');
									?>
									</select>
								</td>
							</tr>
							<tr>
							  <td>
							  	<b>DESCRIPCIÓN</b>
							  </td>
							  <td>
								  <textarea rows="10" cols="60" name="desc_riesg_hum" class="area"><?=$evento_vo->desc_riesg_hum;?></textarea>
							  </td>
							</tr>

						</table>
					</td>
				</tr>
				<!-- ULTIMO PASO: DEFINIR COBERTURA GEOGRAFICA, SI ES ACTUALIZACION DEBE MOSTRAR EL LINK -->
				<? if ($accion == "actualizar" && !isset($_GET["actualizar_cobertura"])){ ?>
					<tr>
						<!-- COBERTURA GEOGRAFICA : INICIO -->
						<td>
							<table border="0" cellpadding="5" cellspacing="1" width="100%" align="center" class="tabla_consulta">
								<tr><td class="titulo_lista" colspan="2">4. COBERTURA GEOGRAFICA : <a href="index.php?<?=$_SERVER['QUERY_STRING']?>&actualizar_cobertura=1">Modificar</a></td></tr>
							</table>
						</td>
						<!-- COBERTURA GEOGRAFICA : FIN -->
					</tr>
				<? } ?>
			</table>
			<table width="75%"  border="0" align="center" cellpadding="0" cellspacing="0">
		      	<tr><td>&nbsp;</td></tr>
		         <tr>
		           <td align='center'>
		        		  <input type="hidden" name="id" value="<? echo $id; ?>">
		              <input type="hidden" name="accion" value="<? echo $accion; ?>">
		              <input type="submit" name="submit" value="Aceptar" class="boton" onclick="return validar_forma('id_tipo,Tipo,desc,Descripción del Evento','','')">
		           </td>
		         </tr>
		    </table>
			<?
		  }  // IF ID_TIPO
		}	//IF ID_CAT
	   } // IF ID_DEPTO o ACTUALIZAR
	  } // IF !ISSET SUBMIT

	// ULTIMO PASO: DEFINIR COBERTURA GEOGRAFICA
	else if (isset($_POST["submit"]) || isset($_GET["actualizar_cobertura"])){
	  	$accion = "actualizar";

		if (!isset($_POST["opcion_f"])){ ?>
			<tr>
				<td>
					<table border="0" cellpadding="5" cellspacing="1"  align="center" class="tabla_consulta">
						 <tr><td class="titulo_lista" colspan="2">4. UBICACION GEOGRAFICA EN POBLADO O REGION</td></tr>
						  <tr>
								<td>POBLADO</td>
								<td>
									<?
				          			//POBLADO
				          			if (isset($_POST['id_depto'])){
										$id_depto = $_POST['id_depto'];
										$id_depto_post = implode(",",$id_depto);
									}
									else{
									    $id_depto = $evento_vo->id_deptos;
									}

									$d = 0;
									foreach ($id_depto as $id_d){
									  $id_depto[$d] = "'".$id_d."'";
									  $d++;
									}
									$id_depto = implode(",",$id_depto);

				          			$sql = "SELECT ID_POB, NOM_POB FROM poblado INNER JOIN municipio ON poblado.ID_MUN = municipio.ID_MUN WHERE ID_DEPTO IN (".$id_depto.") ORDER BY NOM_POB";
									$rs = $conn->OpenRecordset($sql);
									if ($conn->RowCount($rs) > 0){
										$error_cobertura = 0;
									  ?>
										<select name="id_poblados[]" multiple size="8" class="select">
									  	<?
										  while ($row_rs = $conn->FetchRow($rs)){
									    	echo "<option value=".$row_rs[0];
											if (in_array($row_rs[0],$evento_vo->id_poblados))  echo " selected ";
											echo ">".$row_rs[1]."</option>";
										}
					          			?>
					          			</select>
					          		<?}
									  else{
									    echo "No hay Poblados definidos en el (los) Departamento (s) seleciconado (s).";

									}?>
								</td>
							</tr>
							 <tr>
								<td>REGION</td>
								<td>
								  <?
				          			//REGION
				          			$sql = "SELECT DISTINCT region.ID_REG, NOM_REG FROM region INNER JOIN mun_reg ON region.ID_REG = mun_reg.ID_REG INNER JOIN municipio ON mun_reg.ID_MUN = municipio.ID_MUN WHERE ID_DEPTO IN (".$id_depto.")";
									$rs = $conn->OpenRecordset($sql);
									if ($conn->RowCount($rs) > 0){
									  $error_cobertura = 0;
									  ?>
										<select name="id_regiones[]" multiple size="<?=$conn->RowCount?>" class="select">
									  	<?
										  while ($row_rs = $conn->FetchRow($rs)){
									    	echo "<option value=".$row_rs[0];
											if (in_array($row_rs[0],$evento_vo->id_regiones))  echo " selected ";
											echo ">".$row_rs[1]."</option>";
										}
					          			?>
					          			</select>
					          		<?}
									  else{
									    echo "No hay Regiones definidas en el (los) Departamento (s) seleciconado (s).";

									}?>
								</td>
							</tr>
							<tr>
								<td colspan='2'>
									<br><b>Siguiente paso</b>:<br><br>
									<input type="radio" name="opcion_f" value="2">&nbsp; Definir Ubicación Geográfica en Parque Natural, Resguardo o Divison Afro<br>
									<input type="radio" name="opcion_f" value="5" checked>&nbsp; Finalizar
									<input type="hidden" value="<?=$id_depto_post?>" name="id_depto" />
								</td>
							</tr>
					</table>
				</td>
			</tr>
		<?
		 }
		else if (isset($_POST["opcion_f"]) && $_POST["opcion_f"] == 2){
			?>
			<tr>
				<td>
					<table border="0" cellpadding="5" cellspacing="1" width="100%" align="center" class="tabla_consulta">

				<tr><td class="titulo_lista" colspan="2">4. COBERTURA GEOGRAFICA EN RESGUARDO, PARQUE NATURAL O DIVISION AFRO</td></tr>
				<tr>
					<td width="200">PARQUE NATURAL</td>
					<td>
						<?
	          			//PARQUE NATURAL
	          			if (isset($_POST['id_depto'])){
							$id_depto = $_POST['id_depto'];
							$id_depto_post = implode(",",$id_depto);
						}
						else{
						    $id_depto = $evento_vo->id_deptos;
						}


						$d = 0;
						foreach ($id_depto as $id_d){
						  $id_depto[$d] = "'".$id_d."'";
						  $d++;
						}

						$id_depto = implode(",",$id_depto);

	          			$sql = "SELECT DISTINCT parque_natural.ID_PAR_NAT, NOM_PAR_NAT FROM parque_natural INNER JOIN par_nat_mun ON parque_natural.ID_PAR_NAT = par_nat_mun.ID_PAR_NAT INNER JOIN municipio ON par_nat_mun.ID_MUN = municipio.ID_MUN WHERE ID_DEPTO IN (".$id_depto.")";
						$rs = $conn->OpenRecordset($sql);
						if ($conn->RowCount($rs) > 0){
						  $error_cobertura = 0;
						  ?>
							<select name="id_parques[]" multiple size="8" class="select">
						  	<?
							  while ($row_rs = $conn->FetchRow($rs)){
						    	echo "<option value=".$row_rs[0];
								if (in_array($row_rs[0],$evento_vo->id_parques))  echo " selected ";
								echo ">".$row_rs[1]."</option>";
							}
		          			?>
		          			</select>
		          		<?}
						  else{
						    echo "No hay Parques Naturales definidos en el (los) Departamento (s) seleciconado (s).";

						}?>
					</td>
				</tr>
				<tr>
					<td>RESGUARDO</td>
	        		<td>
					<?
	          			//RESGUARDO
	          			$sql = "SELECT DISTINCT resguardo.ID_RESGUADRO, NOM_RESGUARDO FROM resguardo INNER JOIN res_mun ON resguardo.ID_RESGUADRO = res_mun.ID_RESGUADRO INNER JOIN municipio ON res_mun.ID_MUN = municipio.ID_MUN WHERE ID_DEPTO IN (".$id_depto.")";
						$rs = $conn->OpenRecordset($sql);
						if ($conn->RowCount($rs) > 0){
						  $error_cobertura = 0;
						  ?>
							<select name="id_resguardos[]" multiple size="8" class="select">
						  	<?
							  while ($row_rs = $conn->FetchRow($rs)){
						    	echo "<option value=".$row_rs[0];
								if (in_array($row_rs[0],$evento_vo->id_resguardos))  echo " selected ";
								echo ">".$row_rs[1]."</option>";
							}
		          			?>
		          			</select>
		          		<?}
						  else{
						    echo "No hay Resguardos definidos en el (los) Departamento (s) seleciconado (s).";
						  }?>
				</td>
				</tr>
				<tr>
					<td>DIVISION AFRO</td>
	        		<td>
	        			<?
						//DIVISION AFRO
	          			$sql = "SELECT DISTINCT division_afro.ID_DIV_AFRO, NOM_DIV_AFRO FROM division_afro INNER JOIN div_afro_mun ON division_afro.ID_DIV_AFRO = div_afro_mun.ID_DIV_AFRO INNER JOIN municipio ON div_afro_mun.ID_MUN = municipio.ID_MUN WHERE ID_DEPTO IN (".$id_depto.")";
	          			//echo $sql;
						$rs = $conn->OpenRecordset($sql);
						if ($conn->RowCount($rs) > 0){
						  $error_cobertura = 0;
						  ?>
							<select name="id_divisiones_afro[]" multiple size="8" class="select">
						  	<?
							  while ($row_rs = $conn->FetchRow($rs)){
						    	echo "<option value=".$row_rs[0];
								if (in_array($row_rs[0],$evento_vo->id_divisiones_afro))  echo " selected ";
								echo">".$row_rs[1]."</option>";
							}
		          			?>
		          			</select>
		          		<?}
						  else{
						    echo "No hay Resguardos definidos en el (los) Departamento (s) seleciconado (s).";
						  }
						?>
					</td>
				</tr>
				<input type="hidden" value="4" name="opcion_f" />
		<?}
		if (isset($_POST["submit"]) || $accion == "actualizar"){
			if ($error_cobertura == 0){
				?>
				<!-- COBERTURA GEOGRAFICA : FIN -->
				</tr>
				<tr>
		           <td align='center' colspan="2">
		        		<input type="hidden" name="id" value="<? echo $id; ?>">
		              <input type="hidden" name="accion" value="<? echo $accion; ?>">
		              <input type="hidden" name="actualizar_cobertura" value="1">
		              <input type="submit" name="submit" value="Aceptar" class="boton">
		           </td>
	     		</tr>
     	<?}
		 	}?>
			<? } ?>
    </table>
    </form>