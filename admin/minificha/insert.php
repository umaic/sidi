<?
//INICIALIZACION DE VARIABLES
$minificha_dao = New MinifichaDAO();
$condicion_mina_dao = New CondicionMinaDAO();
$edad = New EdadDAO();
$d_s_dao = New DatoSectorialDAO();
$desplazamiento_dao = New DesplazamientoDAO();
$cat_dao = New CategoriaDatoSectorDAO();
$sexo_dao = New SexoDAO();
$conn = MysqlDb::getInstance();

$sql = "SELECT nom_min_ds FROM minificha_datos_sectoriales";
$rs = $conn->OpenRecordset($sql);
while ($row_rs = $conn->FetchRow($rs)){
	$nom_mod_datos_generales[] = $row_rs[0];	
}

$sql = "SELECT nom_min_mina FROM minificha_mina";
$rs = $conn->OpenRecordset($sql);
while ($row_rs = $conn->FetchRow($rs)){
	$nom_mod_mina[] = $row_rs[0];	
}

$sql = "SELECT nom_min_des FROM minificha_desplazamiento";
$rs = $conn->OpenRecordset($sql);
while ($row_rs = $conn->FetchRow($rs)){
	$nom_mod_desplazamiento[] = $row_rs[0];	
}

$sql = "SELECT nom_min_org FROM minificha_org";
$rs = $conn->OpenRecordset($sql);
while ($row_rs = $conn->FetchRow($rs)){
	$nom_mod_orgs[] = $row_rs[0];	
}

$nom_mod_irh = array("Indice de Riesgo de Situación Humanitaria",
					 "Subindice Capacidades",
					 "Subindice Conflicto",
					 "Subindice Económico",
					 "Subindice Social",
					 "IRSH Nueva Metodolog&iacute;a",
					 "Subindice de Amenaza",
					 "Subindice de Vulnerabilidad");
								
if (isset($_GET["accion"])){
	$accion = $_GET["accion"];
}
else if (isset($_POST["accion"])){
	$accion = $_POST["accion"];
}

$chk_mod = array("","","","","","","");
$chk_mod_grafica = array("barras"=>"","barras_h"=>"","barras_a"=>"","lineas"=>"","torta"=>"", "histograma" => "", "barras_t" => "", "barras_a_t" => "", "barras_t_o" => "", "lineas_t" => "", "histograma_l" => "", "histograma_l_t" => "", "piramide" => "");

$id_modulo = 0;
if (isset($_GET["id_modulo"])){
	$id_modulo = $_GET["id_modulo"];
	$mods_t = $minificha_dao->Get($id_modulo,0);
	$mods = $mods_t['mods'];
	$mods_grafica = $mods_t['mods_grafica'];
	$chk_mod[$id_modulo] = "selected";
}
$id_submodulo = 0;
if (isset($_GET["id_submodulo"])){
	$id_submodulo = $_GET["id_submodulo"];
	$datos_submodulo_t = $minificha_dao->Get($id_modulo,$id_submodulo);
	$datos_submodulo = $datos_submodulo_t['mods'];
}

$id_cat = (isset($_GET["id_cat"])) ? $_GET["id_cat"] : 0;
?>
<form method="POST" onsubmit="submitForm(event);return false;">
  <table border="0" align="center" class="tabla_lista">
	  <tr>
		  <td width="150" align="right"><b>M&oacute;dulo</b></td>
			<td>
    			<select name="id_modulo" class="select" onchange="refreshTab('<?=$_SERVER['PHP_SELF']?>?accion=<?=$accion?>&id_modulo='+this.value)">
    				<option value="0" <?=$chk_mod[0]?>>Seleccione alguno</option>
    				<option value="1" <?=$chk_mod[1]?>>General</option>
    				<option value="2" <?=$chk_mod[2]?>>Datos Sectoriales Generales & Demografía</option>
    				<option value="3" <?=$chk_mod[3]?>>Desplazamiento</option>
    				<option value="4" <?=$chk_mod[4]?>>Accidentes con Mina</option>
    				<option value="5" <?=$chk_mod[5]?>>Indice de Riesgo de Situación Humanitaria</option>
    				<option value="6" <?=$chk_mod[6]?>>Organizaciones</option>
				</select>
			</td>
		</tr>
		<?
		if ($id_modulo > 0){
			$chk = "";
			switch ($id_modulo){
				//General
				case 1:
					if (in_array(1,$mods))	$chk = "checked";
					echo "<tr><td align='right'><input type='checkbox' name='mod[]' value=1 $chk></td><td>Tabla Resumen [ <a href='#' onclick=\"refreshTab('".$_SERVER['PHP_SELF']."?accion=$accion&id_modulo=$id_modulo&id_submodulo=1'); return false;\">Seleccionar Datos Sectoriales</a> ]</td></tr>";
					if ($id_submodulo == 1){
						?>
						<tr>
							<td>&nbsp;</td>
							<td align="center">
								<table cellpadding="3">
									<tr>
										<td><b>Categoria</b></td>
										<td>
											<select name="id_categoria" class="select" onchange="refreshTab('<?=$_SERVER['PHP_SELF']?>?accion=<?=$accion?>&id_modulo=<?=$id_modulo?>&id_submodulo=1&id_cat='+this.value)">
												<option>Seleccione alguna</option>
												<?
												$cat_dao->ListarCombo('combo',$id_cat,'');
												?>
											</select>
										</td>
									</tr>
									<?
									if ($id_cat > 0){
										$cat = $cat_dao->Get($id_cat);
										echo "<tr><td id='td_cat_$cat->id' colspan=2 align='center'><table>";

										$datos_s = $d_s_dao->GetAllArray("ID_CATE = $cat->id",'','');

										foreach ($datos_s as $dato){
											$chk = "";
											if (in_array($dato->id,$datos_submodulo))	$chk = "checked";
											echo "<tr><td align='right'><input type='checkbox' name='submods[]' value=$dato->id $chk><td align='left'>$dato->nombre</td></tr>";
										}

										echo "</table></td></tr>";
									}
									?>
								</table>
							</td>
						</tr>
						<?
					}
				break;

				//Datos Sec. Generales
				case 2:
					$m = 1;
					$s = 0;
					foreach ($nom_mod_datos_generales as $nom){
						$chk = "";
						foreach ($chk_mod_grafica as $i => $value){
							$chk_mod_grafica[$i] = "";
						}
						
						if (in_array($m,$mods)){
							$chk = "checked";
							$chk_mod_grafica[$mods_grafica[$s]] = 'selected';
							$s++;
						}

						echo "<tr>
							<td align='right'><input type='checkbox' name='mod[]' value=$m $chk></td>
							<td>
								$nom<br>";
								if ($m == 9) echo "[ <a href='#' onclick=\"refreshTab('".$_SERVER['PHP_SELF']."?accion=$accion&id_modulo=$id_modulo&id_submodulo=9');return false;\">Seleccionar Enfermedades</a> ]";
							echo "</td>
							<td>
								<select name='grafica_$m' class='select'>
									<option value='barras'". $chk_mod_grafica['barras'].">Barras</option>
									<option value='barras_t'". $chk_mod_grafica['barras_t'].">Barras con Tabla Resumen</option>
									<option value='barras_h'". $chk_mod_grafica['barras_h'].">Barras Horizontales</option>
									<option value='barras_a'". $chk_mod_grafica['barras_a'].">Barras Acumuladas</option>
									<option value='barras_a_t'". $chk_mod_grafica['barras_a_t'].">Barras Acumuladas con tabla resumen</option>
									<option value='histograma'". $chk_mod_grafica['histograma'].">Histograma barras</option>
									<option value='histograma_l'". $chk_mod_grafica['histograma_l'].">Histograma lineas</option>
									<option value='histograma_l_t'". $chk_mod_grafica['histograma_l_t'].">Histograma lineas T. Resumen</option>
									<option value='lineas'". $chk_mod_grafica['lineas'].">Lineas</option>
									<option value='lineas_t'". $chk_mod_grafica['lineas_t'].">Lineas con Tabla Resumen</option>
									<option value='piramide'". $chk_mod_grafica['piramide'].">Piramide</option>
									<option value='torta'". $chk_mod_grafica['torta'].">Torta</option>
								</select>
							</td>
							</tr>";

							//Enfermedades
							if ($id_submodulo == 9 && $m == 9){
								?>
								<tr>
									<td>&nbsp;</td>
									<td align="center">
										<table cellpadding="3">
											<?
											$datos_s = $d_s_dao->GetAllArray('ID_CATE=4','','');
											foreach ($datos_s as $dato){
												$chk = "";
												if (in_array($dato->id,$datos_submodulo))	$chk = "checked";
												echo "<tr><td align='right'><input type='checkbox' name='submods[]' value=$dato->id $chk><td>$dato->nombre</td></tr>";
											}
											?>
										</table>
									</td>
								</tr>
								<?
							}
						$m++;
					}
				break;

				//Desplazamiento
				case 3:
					$m = 1;
					$s = 0;
					foreach ($nom_mod_desplazamiento as $nom){
						$chk = "";
						foreach ($chk_mod_grafica as $i => $value){
							$chk_mod_grafica[$i] = "";
						}
						
						if (in_array($m,$mods)){
							$chk = "checked";
							$chk_mod_grafica[$mods_grafica[$s]] = 'selected';
							$s++;
						}

						echo "<tr>
							<td align='right'><input type='checkbox' name='mod[]' value=$m $chk></td>
							<td>
								$nom<br>";
								if ($m == 9) echo "[ <a href=\"".$_SERVER['PHP_SELF']."?accion=$accion&id_modulo=$id_modulo&id_submodulo=9\">Seleccionar Enfermedades</a> ]";
							echo "</td>
							<td>
								<select name='grafica_$m' class='select'>
									<option value='barras'". $chk_mod_grafica['barras'].">Barras</option>
									<option value='barras_h'". $chk_mod_grafica['barras_h'].">Barras Horizontales</option>
									<option value='barras_a'". $chk_mod_grafica['barras_a'].">Barras Acumuladas</option>
									<option value='barras_a_t'". $chk_mod_grafica['barras_a_t'].">Barras Acumuladas con tabla resumen</option>
									<option value='barras_t'". $chk_mod_grafica['barras_t'].">Barras con Tabla Resumen</option>
									<option value='lineas'". $chk_mod_grafica['lineas'].">Lineas</option>
									<option value='lineas_t'". $chk_mod_grafica['lineas_t'].">Lineas con Tabla Resumen</option>
									<option value='torta'". $chk_mod_grafica['torta'].">Torta</option>
								</select>
							</td>
							</tr>";

						$m++;
					}
				break;

				//Minas
				case 4:
					$m = 1;
					$s = 0;
					foreach ($nom_mod_mina as $nom){
						$chk = "";
						foreach ($chk_mod_grafica as $i => $value){
							$chk_mod_grafica[$i] = "";
						}
						
						//$chk_mod_grafica = array("barras"=>"","barras_h"=>"","barras_a"=>"","lineas"=>"","torta"=>"");
						if (in_array($m,$mods)){
							$chk = "checked";
							$chk_mod_grafica[$mods_grafica[$s]] = 'selected';
							$s++;
						}

						echo "<tr>
							<td align='right'><input type='checkbox' name='mod[]' value=$m $chk></td>
							<td>
								$nom<br>";

								if ($m == 1) echo "[ <a href='#' onclick=\"refreshTab('".$_SERVER['PHP_SELF']."?accion=$accion&id_modulo=$id_modulo&id_submodulo=41'); return false;\">Seleccionar Sexos</a> ]";
								if ($m == 2) echo "[ <a href='#' onclick=\"refreshTab('".$_SERVER['PHP_SELF']."?accion=$accion&id_modulo=$id_modulo&id_submodulo=42');return false;\">Seleccionar Condiciones</a> ]";
							echo "</td>
							<td>
								<select name='grafica_$m' class='select'>
									<option value='barras'". $chk_mod_grafica['barras'].">Barras</option>
									<option value='barras_t'". $chk_mod_grafica['barras_t'].">Barras con tabla resumen</option>
									<option value='barras_h'". $chk_mod_grafica['barras_h'].">Barras Horizontales</option>
									<option value='barras_a'". $chk_mod_grafica['barras_a'].">Barras Acumuladas</option>
									<option value='barras_a_t'". $chk_mod_grafica['barras_a_t'].">Barras Acumuladas con tabla resumen</option>
									<option value='lineas'". $chk_mod_grafica['lineas'].">Lineas</option>
									<option value='lineas_t'". $chk_mod_grafica['lineas_t'].">Lineas con Tabla Resumen</option>
									<option value='torta'". $chk_mod_grafica['torta'].">Torta</option>
								</select>
							</td>
							</tr>";

							//Sexos
							if ($id_submodulo == 41 && $m == 1){
								?>
								<tr><td colspan="2" align="center">** <b>Marque solo 2 opciones</b> **</td></tr>
								<tr>
									<td>&nbsp;</td>
									<td align="center">
										<table cellpadding="3">
											<?
											$sexos = $sexo_dao->GetAllArray('');
											foreach ($sexos as $sexo){
												$chk = "";
												if (in_array($sexo->id,$datos_submodulo))	$chk = "checked";
												echo "<tr><td align='right'><input type='checkbox' name='submods_1[]' value=$sexo->id $chk><td>$sexo->nombre</td></tr>";
											}
											?>
										</table>
									</td>
								</tr>
								<input type='hidden' name='sexo_mina' value=1>
								<?
							}

							//Condiciones
							if ($id_submodulo == 42 && $m == 2){
								?>
								<tr><td colspan="2" align="center">** <b>Marque solo 2 opciones</b> **</td></tr>
								<tr>
									<td>&nbsp;</td>
									<td align="center">
										<table cellpadding="3">
											<?
											$condiciones = $condicion_mina_dao->GetAllArray('');
											foreach ($condiciones as $cond){
												$chk = "";
												if (in_array($cond->id,$datos_submodulo))	$chk = "checked";
												echo "<tr><td align='right'><input type='checkbox' name='submods_2[]' value=$cond->id $chk><td>$cond->nombre</td></tr>";
											}
											?>
										</table>
									</td>
								</tr>
								<input type='hidden' name='condicion_mina' value=1>
								<?
							}

						$m++;
					}
				break;

				//IRH
				case 5:
					$m = 1;
					$s = 0;
					foreach ($nom_mod_irh as $nom){
						$chk = "";
						foreach ($chk_mod_grafica as $i => $value){
							$chk_mod_grafica[$i] = "";
						}
						
						if (in_array($m,$mods)){
							$chk = "checked";
							$chk_mod_grafica[$mods_grafica[$s]] = 'selected';
							$s++;
						}

						echo "<tr>
							<td align='right'><input type='checkbox' name='mod[]' value=$m $chk></td>
							<td>
								$nom<br>";
							echo "</td>
							<td>
								<select name='grafica_$m' class='select'>
									<option value='barras_t_o'". $chk_mod_grafica['barras_t_o'].">Barras Tipo Org</option>
									<option value='barras_h'". $chk_mod_grafica['barras_h'].">Barras Horizontales</option>
								</select>
							</td>
							</tr>";

						$m++;
					}
				break;
								
				//Orgs
				case 6:
					$m = 1;
					$s = 0;
					foreach ($nom_mod_orgs as $nom){
						$chk = "";
						foreach ($chk_mod_grafica as $i => $value){
							$chk_mod_grafica[$i] = "";
						}
						
						if (in_array($m,$mods)){
							$chk = "checked";
							$chk_mod_grafica[$mods_grafica[$s]] = 'selected';
							$s++;
						}

						echo "<tr>
							<td align='right'><input type='checkbox' name='mod[]' value=$m $chk></td>
							<td>
								$nom<br>";
							echo "</td>
							<td>
								<select name='grafica_$m' class='select'>
									<option value='barras_t'". $chk_mod_grafica['barras_t'].">Barras con tabla resumen</option>
									<option value='torta'". $chk_mod_grafica['torta'].">Torta</option>

								</select>
							</td>
							</tr>";

						$m++;
					}
				break;				
			}
			?>
				<tr>
				  <td colspan="2" align='center'>
					  <br>
						<input type="hidden" name="accion" value="<?=$accion?>" />
						<input type="submit" name="submit" value="Aceptar" class="boton"" />
					</td>
				</tr>
			<?
		}
		?>
	</table>
</form>
