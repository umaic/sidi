<!-- MINA -->
	<tr>
		<td id="mina" class="td_outer" style="display:<?=$style_display['mina']?>" colspan="10">
		<!--<td id="desplazamiento" class="td_outer" style="display:none">-->
			<table cellpadding="3" border="0" width="100%">
				<tr>
					<td colspan='4'>
						<?
						$info_ficha = $info_ficha_dao->GetAllArray("modulo = 'mina'");
						?>
<!--						<img src='images/pwd.png' border=0>&nbsp;<a href='#' onmouseover="TagToTip('div_info_desplazamiento',SHADOW, true,BGCOLOR, '#f1f1f1',FONTCOLOR,'#000000',TITLEALIGN,'center',BORDERCOLOR,'#0099ff',CLOSEBTN,true,STICKY,true,CLOSEBTNTEXT,'Cerrar',TITLEBGCOLOR,'#0099ff',CLOSEBTNCOLORS, ['', '#ffffff', '', '#0066ff'],WIDTH,400,SHADOWWIDTH,3)" onmouseout="UnTip()">VER INFORMACION</a>-->
							<div id="div_info_mina" class="instruccion" style="height:70px"><img src='images/pwd.png' border=0>&nbsp;<font class='titulo_instruccion'>INFORMACION</font><br><?=$info_ficha->texto?></div>
					</td>
<!--					<div id="div_info_desplazamiento" style="display:none"><?=$info_ficha->texto?></div>-->
				</tr>
				<tr>
					<td>
						<table>
							<tr id='tr_ocultar_info_mina'>
								<td><img src="images/ocultar.png"></td>
								<td><a href="#" onclick="document.getElementById('div_info_mina').style.display='none';document.getElementById('tr_mostrar_info_mina').style.display='';document.getElementById('tr_ocultar_info_mina').style.display='none';">Ocultar Informaci&oacute;n</a>	
							</tr>
							<tr id='tr_mostrar_info_mina' style="display:none">
								<td><img src="images/mostrar.png"></td>
								<td><a href="#" onclick="document.getElementById('div_info_mina').style.display='';document.getElementById('tr_ocultar_info_mina').style.display='';document.getElementById('tr_mostrar_info_mina').style.display='none';">Mostrar Informaci&oacute;n</a>
							</tr>
						</table>
					</td>
				</tr>
				<tr>
					<td valign="top" width="300">
						<table cellpadding="2" border="0" class='table_filtro_gra_resumen'>
							<tr><td class="titulo_filtro">&nbsp;<img src="images/gra_resumen/fl_filtro.gif">&nbsp;Reporte</td></tr>
							<tr>
								<td>
									<input type="radio" id="reporte_mina_6" name="reporte_mina" value="6" onclick="mostrarFiltrosMina('rep_1')" checked>&nbsp;
									<select id="mina_acc_vic_6" class="select">
										<option value="acc">Accidentes</option>
										<option value="vic" selected>V&iacute;ctimas</option>
									</select>&nbsp;con Mina total
								</td>
							</tr>
							<tr>
								<td>
									<input type="radio" id="reporte_mina_1" name="reporte_mina" value="1" onclick="mostrarFiltrosMina('sexo')" checked>&nbsp;V&iacute;ctimas&nbsp;con Mina por sexo
								</td>
							</tr>
							<tr><td><input type="radio" id="reporte_mina_2" name="reporte_mina" value="2" onclick="mostrarFiltrosMina('condicion')">&nbsp;V&iacute;ctimas con Mina por condici&oacute;n</td></tr>
							<tr><td><input type="radio" id="reporte_mina_3" name="reporte_mina" value="3" onclick="mostrarFiltrosMina('estado')">&nbsp;V&iacute;ctimas con Mina por estado</td></tr>
							<tr><td><input type="radio" id="reporte_mina_4" name="reporte_mina" value="4" onclick="mostrarFiltrosMina('edad')">&nbsp;V&iacute;ctimas con Mina por grupo de edad</td></tr>
							<tr>
								<td>
									<input type="radio" id="reporte_mina_5" name="reporte_mina" value="5" onclick="mostrarFiltrosMina('rep_5')">&nbsp;
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
									<select id='dato_para_reporte_5_mina' class='select'>
										<option value='mpio'>Municipios</option>
										<option value='depto'>Departamentos</option>
									</select>&nbsp; con mayor registro de v&iacute;ctimas
								</td>
							</tr>
							
						</table>
					</td>
					<td width="200" height="150" valign="top">
						<table id="table_sexo" width="100%">
							<tr><td class="titulo_filtro">&nbsp;<img src="images/gra_resumen/fl_filtro.gif">&nbsp;Sexo</td></tr>
							<?
							$arr = $sexo_dao->GetAllArray('');
							$f = 0;
							foreach ($arr as $vo){
								echo "<tr><td><input type='checkbox' value=".$vo->id." id='id_sexos' name='filtros[]'>&nbsp;".$vo->nombre."</td></tr>";
								$f++;
							}
							?>
						</table>
						<table id="table_condicion"  width="100%" style="display:none">
							<tr><td class="titulo_filtro">&nbsp;<img src="images/gra_resumen/fl_filtro.gif">&nbsp;Condici&oacute;n</td></tr>
							<tr><td><div style='overflow:auto;height:120px'><table>
								<?
								$arr = $condicion_dao->GetAllArray('');
								$f = 0;
								foreach ($arr as $vo){
									echo "<tr><td><input type='checkbox' value=".$vo->id." id='id_condiciones' name='filtros[]'>&nbsp;".$vo->nombre."</td></tr>";
									$f++;
								}
								?>
								</table></div></td></tr>
						</table>
						<table id="table_estado"  width="100%" style="display:none">
							<tr><td class="titulo_filtro">&nbsp;<img src="images/gra_resumen/fl_filtro.gif">&nbsp;Estado</td></tr>
							<?
							//$arr = $estado_dao->GetAllArray('ID_ESTADO_MINA IN (1,2)');
							$arr = $estado_dao->GetAllArray('');
							$f = 0;
							foreach ($arr as $vo){
								echo "<tr><td><input type='checkbox' value=".$vo->id." id='id_estados' name='filtros[]'>&nbsp;".$vo->nombre."</td></tr>";
								$f++;
							}
							?>
						</table>
						<table id="table_edad"  width="100%" style="display:none">
							<tr><td class="titulo_filtro">&nbsp;<img src="images/gra_resumen/fl_filtro.gif">&nbsp;Edad</td></tr>
							<?
							$edades = $edad_dao->GetAllArray('');
							$f = 0;
							foreach ($edades as $edad){
								echo "<tr><td><input type='checkbox' value=".$edad->id." id='id_edades' name='edades[]'>&nbsp;".$edad->nombre."</td></tr>";
								$f++;
							}
							?>
						</table>
						<table id="table_rep_5"  width="100%" style="display:none">
							<tr><td class="titulo_filtro">&nbsp;<img src="images/gra_resumen/fl_filtro.gif">&nbsp;No aplica</td></tr>
							<tr><td>--</td></tr>
						</table>
						<table id="table_rep_1"  width="100%" style="display:none">
							<tr><td class="titulo_filtro">&nbsp;<img src="images/gra_resumen/fl_filtro.gif">&nbsp;No aplica</td></tr>
							<tr><td>--</td></tr>
						</table>
					</td>
					<td valign="top">
						<table cellpadding="2" border="0" class='table_filtro_gra_resumen' width="250">
							<tr><td class="titulo_filtro" colspan="2">&nbsp;<img src="images/gra_resumen/fl_filtro.gif">&nbsp;Periodo</td></tr>
							<tr>
								<td width="180">Año Inicial</td>
								<td>
									<select id='ini_mina' class='select'>
										<?
										$hoy = getdate();
										$a_actual = $hoy['year'];
										for($i=1990;$i<=$a_actual;$i++){
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
									<select id='fin_mina' class='select'>
										<?
										$hoy = getdate();
										$a_actual = $hoy['year'];
										for($i=1995;$i<=$a_actual;$i++){
											echo "<option value=$i";
											if ($i == $a_actual)	echo " selected ";
											echo ">$i</option>";	
										}
										?>
									</select>
									<!-- <input type="text" id="fin_desplazamiento" name="fin_desplazamiento" size="10" class="textfield"> -->
								</td>
							</tr>
							<tr id='tr_detalle_periodo_mina'>
								<td>Graficar (eje x)</td>
								<td>
									<select id="detalle_periodo_mina" class="select" style="width:100px">
										<option value='aaaa'>A&ntilde;os</option>
										<option value='mes'>Mes</option>
									</select>
								</td>
							</tr>
						</table>					
					</td>
					<td valign="top">
						<table cellpadding="2" border="0" class='table_filtro_gra_resumen' width="170">
							<tr><td class="titulo_filtro" colspan="2">&nbsp;<img src="images/gra_resumen/fl_filtro.gif">&nbsp;Gr&aacute;fica</td></tr>
							<tr><td>Barras por años</td><td><input type="radio" id="grafica_mina" name="grafica_mina" value=7 checked></td></tr>
							<tr><td>Torta</td><td><input type="radio" id="grafica_mina" name="grafica_mina" value=5></td></tr>
							<!--<tr>
								<td colspan="2"><p align='justify'><b>Nota</b>: el periodo usado para construir la gr&aacute;fica tipo Torta es: <b>A&ntilde;o Inicial</b></p></td>
							</tr>-->
						</table>					
					</td>					
				</tr>
				<tr>
					<td colspan="4" align="center">
						<!--<a href="javascript:showDiv('mapa','mostrar');"><b>Seleccionar alg&uacute;n Departamento o Municipio</b></a>-->
						<input type="button" value="Generar Gráfica" name="submit_org" class="boton" onclick="graficarMina()">
						&nbsp;&nbsp;<input type="button" id="boton_regresar" value="Seleccionar Ubicación" class="boton" style="display:none" onclick="document.getElementById('td_mapa').style.display=''">
					</td>
				</tr>
<!--				<tr><td class="nota_bcg" colspan="4">:: No olvide que luego de generada la gráfica, el sistema presenta la opci&oacute;n de generar un reporte detallado de la consulta, para esto, use el bot&oacute;n <b>Generar Reporte</b> que se mostrar&aacute; bajo la gr&aacute;fica una vez sea generada ::</tr>-->
				<tr>
					<td colspan="4" id="graficaMina" style="display:none" class="td_grafica_bcg"></td>
				</tr>
			</table>
		</td>
	</tr>