<?
//INICIALIZACION DE VARIABLES
$dato_vo = New DatoSectorial();
$dato_dao = New DatoSectorialDAO();
$u_d_s_dao = New UnidadDatoSectorDAO();
$cat_d_s_dao = New CategoriaDatoSectorDAO();
$sector_dao = New SectorDAO();
$contacto_dao = New ContactoDAO();
$cadena = New Cadena();

$calendar = new DHTML_Calendar('js/calendar/','es', 'calendar-win2k-2', false);
$calendar->load_files();
$chk_desa = array('' => ' selected ', 'departamental' => '', 'municipal' => '');
$chk_valor_nal = array('manual' => '', 'suma_mpio' => '', 'suma_depto' => '');
$chk_valor_deptal = array('manual' => '', 'suma_mpio' => '');

if (isset($_GET["accion"])){
	$accion = $_GET["accion"];
}
else if (isset($_POST["accion"])){
	$accion = $_POST["accion"];
}

$id_sector = 0;
if (isset($_GET["id_sector"])){
	$id_sector = $_GET["id_sector"];
	$dato_vo->id_sector = $id_sector;
}

$chk_calculado = array('no' => ' checked ','si' => '');
$display_calculado = 'none';
$lista_datos = "";
//Caso de Actualizacion
if (isset($_GET["accion"]) && $_GET["accion"] == "actualizar"){
	$id = $_GET["id"];
	$dato_vo = $dato_dao->Get($id);
	
	$chk_desa[$dato_vo->desagreg_geo] = ' selected ';
	$chk_valor_nal[$dato_vo->tipo_calc_nal] = ' selected ';
	$chk_valor_deptal[$dato_vo->tipo_calc_deptal] = ' selected ';

	if ($dato_vo->formula != ""){
		$chk_calculado['si'] = ' checked ';
		$chk_calculado['no'] = '  ';
		$display_calculado = '';
	}

	$id_datos = $cadena->getContentTag($dato_vo->formula,'[',']');

	foreach ($id_datos as $id_dato){
		$dato_tmp = $dato_dao->Get($id_dato);
		($lista_datos == "") ? $lista_datos = "[$id_dato] = $dato_tmp->nombre" : $lista_datos .= "<br>[$id_dato] = $dato_tmp->nombre";
	}
}

?>
<form method="POST" onsubmit="submitForm(event);return false;">
	<table border="0" cellpadding="5" cellspacing="1" width="70%" align="center">
		<tr><td align="right" width="40%">Sector</td>
			<td>
				<select id="id_sector" name="id_sector" class="select" onchange="getDataV1('','ajax_data.php?object=comboBoxCategoriaDatoSectorialInsertUpdate&multiple=0&condicion=id_comp='+this.value,'comboBoxCategoria')">
				<option value="">Seleccione alguno...</option>
				<? $sector_dao->ListarCombo('combo',$dato_vo->id_sector,''); ?></select>
			</td>
		</tr>
		<tr>
			<td align="right">Categoría</td>
			<td id='comboBoxCategoria'>
				<?
				if ($accion == 'actualizar'){
					echo '<select name="id_cat" class="select">';
					$cat_d_s_dao->ListarCombo('combo',$dato_vo->id_cat,'ID_COMP = '.$dato_vo->id_sector);
					echo '</select>';
				}
				else{
					echo '----';
				}
				?>
			</td>
		</tr>
		<tr><td align="right">Fuente</td>
			<td><select name="id_contacto" class="select"><? $contacto_dao->ListarCombo('combo',$dato_vo->id_contacto,'id_esp=37'); ?></select></td>
		</tr>
		<!--<tr><td align="right">Unidad</td>
			<td><select name="id_unidad" class="select"><? $u_d_s_dao->ListarCombo('combo',$dato_vo->id_unidad,''); ?></select></td>
		</tr>-->
		<tr><td align="right">Nombre</td><td><input type="text" id="nombre" name="nombre" size="60" value="<?=$dato_vo->nombre;?>" class="textfield" /></td></tr>
		<tr><td align="right">Definici&oacute;n</td><td><textarea id="definicion" name="definicion" rows="5" cols="60" class="textfield"><?=$dato_vo->definicion;?></textarea></td></tr>
		<!--<tr>
		  <td align="right">Fecha de inicio de vigencia</td>
		  <td>
				<? $calendar->make_input_field(
				// calendar options go here; see the documentation and/or calendar-setup.js
				array('firstDay'       => 1, // show Monday first
				'showOthers'     => true,
				'ifFormat'       => '%Y-%m-%d',
				'timeFormat'     => '12'),
				// field attributes go here
				array('class'       => 'textfield',
				'value'       => $dato_vo->fecha_ini,
				'name'        => 'fecha_ini'));
				?>
			</td>
		</tr>
		<tr>
		  <td align="right">Fecha de fin de vigencia</td>
		  <td>
				<? $calendar->make_input_field(
				// calendar options go here; see the documentation and/or calendar-setup.js
				array('firstDay'       => 1, // show Monday first
				'showOthers'     => true,
				'ifFormat'       => '%Y-%m-%d',
				'timeFormat'     => '12'),
				// field attributes go here
				array('class'       => 'textfield',
				'value'       => $dato_vo->fecha_fin,
				'name'        => 'fecha_fin'));
				?>
			</td>
		</tr>-->
		<tr>
			<td align="right">Desagregación Geográfica</td>
			<td>
				<select name="desagreg_geo" class="select">
					<option value="" <?=$chk_desa['']?>>Por verificar</option>
					<option value="departamental" <?=$chk_desa['departamental']?>>Departamental</option>
					<option value="municipal" <?=$chk_desa['municipal']?>>Municipal</option>
				</select>
			</td>
		</tr>
		<tr>
			<td align="right">Valor Nacional</td>
			<td>
				<select name="tipo_calc_nal" class="select">
					<option value="manual" <?=$chk_valor_nal['manual']?>>Manual</option>
					<option value="suma_mpio" <?=$chk_valor_nal['suma_mpio']?>>Suma casos Municipales</option>
					<option value="suma_depto" <?=$chk_valor_nal['suma_depto']?>>Suma casos Departamentales</option>
				</select>
			</td>
		</tr>	    	
		<tr>
			<td align="right">Valor Departamental</td>
			<td>
				<select name="tipo_calc_deptal" class="select">
					<option value="manual" <?=$chk_valor_deptal['manual']?>>Manual</option>
					<option value="suma_mpio" <?=$chk_valor_deptal['suma_mpio']?>>Suma casos Municipales</option>
				</select>
			</td>
		</tr>	    	
		<tr>
			<td align="right">El datos es calculado</td>
			<td>
				<input type="radio" id="calculado_si" name="calculado" value=1 <?=$chk_calculado['si']?> onclick="document.getElementById('td_formula').style.display='';">&nbsp;Si&nbsp;&nbsp;
				<input type="radio" id="calculado_no" name="calculado" value=0 <?=$chk_calculado['no']?> onclick="document.getElementById('td_formula').style.display='none';">&nbsp;No
			</td>
		</tr>
		<tr>
			<td colspan="2" id='td_formula' style="display:<?=$display_calculado?>" class="tabla_consulta">
				<table align="center" cellpadding="0" cellspacing="0">
					<tr>
						<td colspan="2"><b>Es necesario indicar la Unidad del Dato</b>:
							<select name="id_unidad" class="select">
							<?
							$u_d_s_dao->ListarCombo('combo',$dato_vo->id_unidad,'');

							?>
							</select>
						</td>
					</tr>
					<tr><td>&nbsp;</td></tr>
					<tr>
						<td align="left">
						A continuaci&oacute;n escriba la f&oacute;rmula correspondiente.
						Inserte los Datos involucrados en le f&oacute;rmula seleccionando primero la Categoria y
						luego el dato, el dato se verá reflejado como [ID_DATO]</td>
					</tr>
					<tr><td>&nbsp;</td></tr>
					<tr>
						<td>
							<table>
								<tr>
									<td>
										<table>
											<tr>
												<td width="90"><b>Categoria</b></td>
												<td>
													<select id="id_cat_dato" name="id_cat_dato" class="select" onchange="getDataV1('comboBoxDatoSectorial','ajax_data.php?object=comboBoxDatoSectorial&multiple=0&condicion=ID_CATE='+this.value,'comboBoxDatoSectorial');document.getElementById('botonInsertarDato').style.display=''">
														<option value=''>[ Seleccione ]</option>
														<? $cat_d_s_dao->ListarCombo('','',''); ?>
													</select>
												</td>
											</tr>
										</table>
									</td>
								</tr>
								<tr><td id="comboBoxDatoSectorial"></td></tr>
								<tr><td colspan="2" align="left" id="botonInsertarDato" style="display:none"><input type="button" value="Agregar >>" class="boton" onclick="agragarDatoFormula()"></td></tr>
							</table>
						</td>
					</tr>
					<tr><td>&nbsp;</td></tr>
					<tr>
						<td>
							<table cellspacing=0 cellpadding=2>
								<tr>
									<td><b>F&oacute;rmula</b><br>
										<textarea id='formula' name='formula' style="width:400px;height:100px" class="textfield"><?=$dato_vo->formula?></textarea>
									</td>
									<td valign='bottom'>
										Ejemplo: Tasa de Desempleo = [238]/[134] 
										<br><br>
										<input type="button" value="Limpiar" class="boton" onclick="limpiarFormula()">
									</td>
								</tr>
							</table>
						</td>
					</tr>
					<tr>
						<td><span id="lista_datos"><?=$lista_datos?></span></td>
					</tr>
				</table>
			</td>
		</tr>
		<tr>
		  <td colspan="2" align='center'>
			  <br>
				<input type="hidden" name="accion" value="<?=$accion?>" />
				<input type="hidden" name="id" value="<?=$dato_vo->id;?>" />
				<input type="submit" name="submit" value="Aceptar" class="boton" onclick="return validar_forma('nombre,Nombre','');" />
			</td>
		</tr>
	</table>
</form>
