<!-- EVENTOS CONFLICTO -->
	<tr>
		<td id="evento_c" class="td_outer" style="display:<?=$style_display['evento_c']?>" colspan="10">
			<table cellpadding="3" border="0" width="100%">
				<tr>
					<td colspan='4'>
						<?
						$info_ficha = $info_ficha_dao->GetAllArray("modulo = 'evento_c'");
						?>
<!--						<img src='images/pwd.png' border=0>&nbsp;<a href='#' onmouseover="TagToTip('div_info_desplazamiento',SHADOW, true,BGCOLOR, '#f1f1f1',FONTCOLOR,'#000000',TITLEALIGN,'center',BORDERCOLOR,'#0099ff',CLOSEBTN,true,STICKY,true,CLOSEBTNTEXT,'Cerrar',TITLEBGCOLOR,'#0099ff',CLOSEBTNCOLORS, ['', '#ffffff', '', '#0066ff'],WIDTH,400,SHADOWWIDTH,3)" onmouseout="UnTip()">VER INFORMACION</a>-->
							<div id="div_info_evento_c" class="instruccion" style="height:100px"><img src='images/pwd.png' border=0>&nbsp;<font class='titulo_instruccion'>INFORMACION</font><br><?=$info_ficha->texto?></div>
					</td>
<!--					<div id="div_info_desplazamiento" style="display:none"><?=$info_ficha->texto?></div>-->
				</tr>
				<tr>
					<td>
						<table>
							<tr id='tr_ocultar_info_evento_c'>
								<td><img src="images/ocultar.png"></td>
								<td><a href="#" onclick="document.getElementById('div_info_evento_c').style.display='none';document.getElementById('tr_mostrar_info_evento_c').style.display='';document.getElementById('tr_ocultar_info_evento_c').style.display='none';">Ocultar Informaci&oacute;n</a>
							</tr>
							<tr id='tr_mostrar_info_evento_c' style="display:none">
								<td><img src="images/mostrar.png"></td>
								<td><a href="#" onclick="document.getElementById('div_info_evento_c').style.display='';document.getElementById('tr_ocultar_info_evento_c').style.display='';document.getElementById('tr_mostrar_info_evento_c').style.display='none';">Mostrar Informaci&oacute;n</a>	
							</tr>
						</table>
					</td>
				</tr>
				<tr>
					<td valign="top" colspan="2">
						<table cellpadding="2" border="0" class='table_filtro_gra_resumen'>
							<tr><td class="titulo_filtro" colspan="2">&nbsp;<img src="images/gra_resumen/fl_filtro.gif">&nbsp;Reporte de # de eventos</td></tr>	
							<tr>
								<td colspan="2">
									<div>
										<table>
											<tr><td id='td_reporte_evento_c_1' style="background:#FFDD76;"><input type="radio" id="reporte_evento_c_1" name="reporte_evento_c" value="1" checked onclick="changeBCG(1)">&nbsp;Por Municipio<br>&nbsp;&nbsp;<font class="nota">[ Se listan en orden descendente los Mpios con m&aacute;s acciones ]</font></td></tr>
											<tr><td id='td_reporte_evento_c_2'><input type="radio" id="reporte_evento_c_2" name="reporte_evento_c" value="2" onclick="changeBCG(2)">&nbsp;Por Departamento<br>&nbsp;&nbsp;<font class="nota">[ Se listan en orden descendente los Deptos con m&aacute;s acciones ]</font></td></tr>
											<tr><td id='td_reporte_evento_c_3'><input type="radio" id="reporte_evento_c_3" name="reporte_evento_c" value="3" onclick="changeBCG(3)">&nbsp;Por Mes</font></td></tr>
											<tr><td id='td_reporte_evento_c_4'><input type="radio" id="reporte_evento_c_4" name="reporte_evento_c" value="4" onclick="changeBCG(4)">&nbsp;Por Subcategor&iacute;a (Tipo de acci&oacute;n)</td></tr>
											<tr><td id='td_reporte_evento_c_5'><input type="radio" id="reporte_evento_c_5" name="reporte_evento_c" value="5" onclick="changeBCG(5)">&nbsp;Por presuntos actores<br>&nbsp;&nbsp;</td></tr>
                                            <tr><td id='td_reporte_evento_c_7'><input type="radio" id="reporte_evento_c_7" name="reporte_evento_c" value="7" onclick="changeBCG(7)">&nbsp;Por confrontación de actores<br>&nbsp;&nbsp;</td></tr>
                                            <tr><td id='td_reporte_evento_c_8'><input type="radio" id="reporte_evento_c_8" name="reporte_evento_c" value="8" onclick="changeBCG(8)">&nbsp;Por grupo poblacional<br>&nbsp;&nbsp;</td></tr>
										</table>
									</div>
								</td>
							</tr>
							<tr><td>&nbsp;</td></tr>
                            <tr><td class="titulo_filtro" colspan="2">&nbsp;<img src="images/gra_resumen/fl_filtro.gif">&nbsp;Reporte de # de víctimas</td></tr>	
                            <tr>
                                <td colspan="2">
                                    <div>
                                        <table>
                                            <tr><td id='td_reporte_evento_c_6'><input type="radio" id="reporte_evento_c_6" name="reporte_evento_c" value="6" onclick="changeBCG(6)">&nbsp;Por Mes</font></td></tr>
                                            <tr><td id='td_reporte_evento_c_9'><input type="radio" id="reporte_evento_c_9" name="reporte_evento_c" value="9" onclick="changeBCG(9)">&nbsp;Por presuntos actores<br>&nbsp;&nbsp;</td></tr>
                                            <tr><td id='td_reporte_evento_c_10'><input type="radio" id="reporte_evento_c_10" name="reporte_evento_c" value="10" onclick="changeBCG(10)">&nbsp;Por confrontación de actores<br>&nbsp;&nbsp;</td></tr>
                                            <tr><td id='td_reporte_evento_c_11'><input type="radio" id="reporte_evento_c_11" name="reporte_evento_c" value="11" onclick="changeBCG(11)">&nbsp;Por grupo poblacional<br>&nbsp;&nbsp;</td></tr>
                                        </table>
                                    </div>
                                </td>
                            </tr>
						</table>
					</td>
					<td valign="top" colspan="2">
						<table cellpadding="2" border="0" class='table_filtro_gra_resumen'>
							<tr>
								<td>
									<table>
										<tr>
											<td class="titulo_filtro" width="220">&nbsp;&nbsp;Categor&iacute;a</td>
											<td rowspan="5"><img src="images/spacer.gif" width="15"></td>
											<td class="titulo_filtro" width="270">&nbsp;&nbsp;Subcategor&iacute;a</td>
										</tr>
										<tr>
											<td>
												<select id='id_cat' name='id_cat' size="10" class="select">
													<? $cat_dao->ListarCombo('combo',1,''); ?>
												</select><br><br>
												<span id="link_a_subcat"><img src="images/consulta/mostrar_combo.png">&nbsp;<a href="#" onclick="listarSubtipos('id_cat');return false;">Listar Subcategor&iacute;a</a></span>
											</td>
											<td id="comboBoxSubcategoria" valign="top">
												Seleccione alguna categoria y use la opción Listar<br><br>
											</td>
										</tr>
									</table>
								</td>
							</tr>
                            <tr><td>&nbsp;</td></tr>
                            <tr><td class="titulo_filtro" colspan="2">&nbsp;<img src="images/gra_resumen/fl_filtro.gif">&nbsp;Periodo</td></tr>
                            <tr>
                                <td>
                                    <input type="text" id="date1_ini" name="f_ini[]" class="textfield" size="12">
                                    <a href="#" onclick="displayCalendar(document.getElementById('date1_ini'),'yyyy-mm-dd',this);return false;"><img src="images/calendar.png" border="0"></a>
                                    &nbsp; 
                                    <input type="text" id="date1_fin" name="f_fin[]" class="textfield" size="12">
                                    <a href="#" onclick="displayCalendar(document.getElementById('date1_fin'),'yyyy-mm-dd',this);return false;"><img src="images/calendar.png" border="0"></a>
                                </td>
                            </tr>
                            <tr>
                                <td colspan="2">Especif&iacute;que el periodo de tiempo, es obligatorio</td>
                            </tr>
						</table>
					</td>
				</tr>
				<tr><td>&nbsp;</td></tr>
				<tr>
					<td colspan="4" align="center">
						<input type="button" value="Generar Gráfica" name="submit_org" class="boton" onclick="graficarEventoC('bar',10)">
						&nbsp;&nbsp;<input type="button" id="boton_regresar" value="Seleccionar Ubicación" class="boton" style="display:none" onclick="document.getElementById('td_mapa').style.display=''">
					</td>
				</tr>
				<tr><td id="graficaEventoC" colspan="4" style="display:none" class="td_grafica_bcg"></td></tr>
			</table>
		</td>
	</tr>
