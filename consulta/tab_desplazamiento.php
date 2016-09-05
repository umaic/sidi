<!-- DESPLAZAMIENTO -->
	<tr>
		<td id="desplazamiento" class="td_outer" style="display:<?=$style_display['desplazamiento']?>" colspan="10">
		<!--<td id="desplazamiento" class="td_outer" style="display:none">-->
			<table cellpadding="5" border="0" width="100%">
				<tr>
					<td colspan='4'>
						<?
						$info_ficha = $info_ficha_dao->GetAllArray("modulo = 'desplazamiento'");
						
						?>
							<div id='div_info_desplazamiento' class="instruccion" style="height:100px"><img src='images/pwd.png' border=0>&nbsp;<font class='titulo_instruccion'>AYUDA</font><br><?=$info_ficha->texto?></div>
					</td>
				</tr>
				<tr>
					<td>
						<table>
							<tr id="tr_ocultar_info_desplazamiento">
								<td><img src="images/ocultar.png"></td>
								<td><a href="#" onclick="document.getElementById('div_info_desplazamiento').style.display='none';document.getElementById('tr_mostrar_info_desplazamiento').style.display='';document.getElementById('tr_ocultar_info_desplazamiento').style.display='none';">Ocultar Ayuda</a>	
							</tr>
							<tr id="tr_mostrar_info_desplazamiento" style="display:none">
								<td><img src="images/mostrar.png"></td>
								<td><a href="#" onclick="document.getElementById('div_info_desplazamiento').style.display='';document.getElementById('tr_ocultar_info_desplazamiento').style.display='';document.getElementById('tr_mostrar_info_desplazamiento').style.display='none';">Mostrar Ayuda</a>	
							</tr>
						</table>
					</td>
				</tr>
				<?
				// aca fuentes para tener el numero y poderlo enviar en changeParameters
				$fuentes = $fuente_dao->GetAllArray('');
				$num_fuentes = count($fuentes);
				?>
				<tr>
					<td valign="top">
						<table cellpadding="2" border="0" class='table_filtro_gra_resumen'>
							<tr><td class="titulo_filtro" width="600">&nbsp;<img src="images/gra_resumen/fl_filtro.gif">&nbsp;Reporte</td></tr>
							<tr>
								<td>
									<input type="radio" id="reporte" name="id_reporte" value="2" checked onclick="changeParametersDesplazamiento(2,<?=$num_fuentes ?>)">&nbsp;
									Nuevos Desplazamientos
								</td>
							</tr>
                            <tr>
                                <td>
                                    <input type="radio" id="reporte" name="id_reporte" value="1" onclick="changeParametersDesplazamiento(1,<?=$num_fuentes ?>)">&nbsp;
                                    Desplazamiento acumulado
                                </td>
                            </tr>
							<tr>
								<td>
									<input type="radio" id="reporte" name="id_reporte" value="3" onclick="changeParametersDesplazamiento(3,<?=$num_fuentes ?>)">&nbsp;
									Nuevos Desplazamientos UARIV, <b>detalle Indivual y Masivo</b>
								</td>
							</tr>
							<tr>
								<td>
									<input type="radio" id="reporte" name="id_reporte" value="4" onclick="changeParametersDesplazamiento(4,<?=$num_fuentes ?>)">&nbsp;
									Los 10
									<!--<select id='num_reporte_4_despla' class='select'>-->
										<?
										/*
										for($i=10;$i<=20;$i++){
											echo "<option value=$i>$i</option>";	
										}
										*/
										?>
									<!-- </select> -->
									&nbsp;
									<select id='dato_para_reporte_4_despla' class='select'>
										<option value='mpio'>Municipios</option>
										<option value='depto'>Departamentos</option>
									</select>&nbsp; con mayor registro de nuevos desplazamientos
								</td>
							</tr>
							<tr>
								<td><input type="radio" id="reporte" name="id_reporte" value="5" onclick="changeParametersDesplazamiento(5,<?=$num_fuentes ?>);changeEjexDesplazamientoReporte5()">
									&nbsp;Comparaci&oacute;n entre declaraciones de expulsi&oacute;n y recepci&oacute;n
									<br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span class="nota_gris">Aplica solo para <?=$fuentes[0]->nombre ?> y hace referencia a nuevos desplazamientos</span>
								</td>
							</tr>
                            <tr><td>&nbsp;</td></tr>
                            <tr><td class="titulo_filtro" width="600">&nbsp;<img src="images/gra_resumen/fl_filtro.gif">&nbsp;Nuevos reportes</td></tr>
                            <tr>
                                <td>
                                    <input type="radio" id="reporte" name="id_reporte" value="6" onclick="changeParametersDesplazamiento(6,<?=$num_fuentes ?>)">&nbsp;
                                    Desplazamientos UARIV por <b>Etnia</b>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <input type="radio" id="reporte" name="id_reporte" value="7" onclick="changeParametersDesplazamiento(7,<?=$num_fuentes ?>)">&nbsp;
                                    Desplazamientos UARIV por <b>Género</b>
                                </td>
                            </tr>
						</table>
					</td>
					<td valign="top" id="fuentes_despla_check">
						<table cellpadding="2" border="0" class='table_filtro_gra_resumen'>
							<tr><td class="titulo_filtro" width="130">&nbsp;<img src="images/gra_resumen/fl_filtro.gif">&nbsp;Fuente</td></tr>
							<?
							
							foreach ($fuentes as $fuente){
                                //$f_corte = $desplazamiento_dao->GetFechaCorte($fuente->id,'letra');
                                $f_nom = ($fuente->nombre == 'CODHES') ? 'CODHES' : 'UARIV';
                                echo "<tr><td><input type='radio' value=".$fuente->id." id='fuente_despla_$fuente->id' name='fuentes' onclick=\"changeEjexDesplazamiento('checkbox')\">&nbsp;<b>".$f_nom."</b>
                                    </td></tr>";
                                    //<br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<font class='nota'>[ Corte: $f_corte ]</font>
							}
							?>						
						</table>
					</td>
					<td valign="top" id="fuentes_despla_radio" style="display:none">
						<table cellpadding="2" border="0" class='table_filtro_gra_resumen'>
							<tr><td class="titulo_filtro" width="130">&nbsp;<img src="images/gra_resumen/fl_filtro.gif">&nbsp;Fuente</td></tr>
							<?
							
							foreach ($fuentes as $fuente){
								//$f_corte = $desplazamiento_dao->GetFechaCorte($fuente->id,'letra');
                                echo "<tr><td><input type='radio' value=".$fuente->id." id='fuente_despla_".$fuente->id."_radio' name='fuentes_radio' onclick=\"changeEjexDesplazamiento('radio')\">&nbsp;<b>".$fuente->nombre."</b>
                                    </td></tr>";
                                //<br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<font class='nota'>[ Corte: $f_corte ]</font>
							}
							?>						
						</table>
					</td>
					<td valign="top" height="200">
						<table border="0">
							<tr><td class="titulo_filtro" colspan="2" width="300">&nbsp;<img src="images/gra_resumen/fl_filtro.gif">&nbsp;Periodo</td></tr>
							<tr>
								<td width="140">Año Inicial</td>
								<td>
									<select id='ini_desplazamiento' class='select'>
										<?
										$hoy = getdate();
										$a_actual = $hoy['year'];
										for($i=1985;$i<=$a_actual;$i++){
											echo "<option value=$i>$i</option>";	
										}
										?>
									</select>
									<!-- <input type="text" id="ini_desplazamiento" name="ini_desplazamiento" size="10" class="textfield">-->
								</td>
							</tr>
							<tr>
								<td>Año Final</td>
								<td>
									<select id='fin_desplazamiento' class='select'>
										<?
										$hoy = getdate();
										$a_actual = $hoy['year'];
										for($i=1997;$i<=$a_actual;$i++){
											echo "<option value=$i";
											if ($i == $a_actual)	echo " selected ";
											echo ">$i</option>";	
										}
										?>
									</select>
									<!-- <input type="text" id="fin_desplazamiento" name="fin_desplazamiento" size="10" class="textfield"> -->
								</td>
							</tr>
							<tr id='tr_detalle_periodo_despla'>
								<td>Graficar (eje x)</td>
								<td>
									<select id="detalle_periodo_despla" class="select" style="width:100px">
										<option value='aaaa'>A&ntilde;os</option>
									</select>
								</td>
							</tr>
						</table>
						<br>
						<table id="td_clase_desplazamiento">
							<tr><td class="titulo_filtro">&nbsp;<img src="images/gra_resumen/fl_filtro.gif">&nbsp;Clase de Desplazamiento</td></tr>
							<tr><td><input type="radio" id="exp_rec_exp" name="exp_rec" value="1" checked>&nbsp;Expulsi&oacute;n</td></tr>
							<tr><td><input type="radio" id="exp_rec_rec" name="exp_rec" value="2">&nbsp;Recepci&oacute;n/Estimado Llegadas</td></tr>
						</table>					
						<!--<a href="consulta/swf/mapa_i.html" title="Seleecione la Ubicación Geográfica" rel="gb_page_center[700,570]"><b>Seleccionar alg&uacute;n Departamento o Municipio</a>-->
					</td>
				</tr>
				<tr>
					<td colspan="4" align="center">
						<!--<a href="javascript:showDiv('mapa','mostrar');"><b>Seleccionar alg&uacute;n Departamento o Municipio</b></a>-->
						<input type="button" value="Generar Gráfica" name="submit_org" class="boton" onclick="graficarDesplazamiento('bar')">
						&nbsp;&nbsp;<input type="button" id="boton_regresar" value="Seleccionar Ubicación" class="boton" style="display:none" onclick="document.getElementById('td_mapa').style.display=''">
					</td>
				</tr>
<!--				<tr><td class="nota_bcg" colspan="4">:: No olvide que luego de generada la gráfica, el sistema presenta la opci&oacute;n de generar un reporte detallado de la consulta, para esto, use el bot&oacute;n <b>Generar Reporte</b> que se mostrar&aacute; bajo la gr&aacute;fica una vez sea generada ::</tr>-->
				<tr><td id="graficaDesplazamiento" colspan="4" style="display:none" class="td_grafica_bcg"></td></tr>
			</table>
		</td>
	</tr>
