<!-- DATOS SECTORIALES -->
	<tr>
		<td id="datos" class="td_outer" style="display:<?=$style_display['datos']?>" colspan="10">
		<!--<td id="desplazamiento" class="td_outer" style="display:none">-->
			<table cellpadding="3" border="0" width="100%">
				<tr> <td><img src="images/spacer.gif" height="10"></td> </tr>
				<tr>
					<td valign="top">
						<table cellpadding="2" border="0" class='table_filtro_gra_resumen'>
							<tr><td class="titulo_filtro">&nbsp;<img src="images/gra_resumen/fl_filtro.gif">&nbsp;Reporte</td></tr>
							<tr><td><input type="radio" id="reporte_dato_1" name="reporte_dato" value="1" checked onclick="document.getElementById('dato_reporte_1').style.display='';document.getElementById('dato_reporte_2').style.display='none';document.getElementById('getAniosDatoSectorial_2').style.display='none';">&nbsp;Valor por a&ntilde;os para un solo Dato</td></tr>
							<tr><td><input type="radio" id="reporte_dato_2" name="reporte_dato" value="2" onclick="document.getElementById('dato_reporte_2').style.display='';document.getElementById('dato_reporte_1').style.display='none';document.getElementById('getAniosDatoSectorial').style.display='none';">&nbsp;Comparación entre varios Datos Sectoriales</td></tr>
							<tr><td><input type="radio" id="reporte_dato_3" name="reporte_dato" value="3" onclick="document.getElementById('dato_reporte_1').style.display='';document.getElementById('dato_reporte_2').style.display='none';document.getElementById('getAniosDatoSectorial_2').style.display='none';">&nbsp;Consultar <b>valor</b> de Dato Sectorial</td></tr>
							<tr>
								<td>
									<input type="radio" id="reporte_dato_4" name="reporte_dato" value="4" onclick="document.getElementById('dato_reporte_1').style.display='';document.getElementById('dato_reporte_2').style.display='none';document.getElementById('getAniosDatoSectorial_2').style.display='none';">&nbsp;Los 10
									&nbsp;
									<select id='dato_para_reporte_4_dato' class='select'>
										<option value='mpio'>Municipios</option>
										<option value='depto'>Departamentos</option>
									</select>&nbsp; con mayor valor en el dato seleccionado</td></tr>
						</table>
					</td>
					<td valign="top">
						<table>
							<tr>
								<td>
								<?
									$info_ficha = $info_ficha_dao->GetAllArray("modulo = 'dato_sectorial'");
									?>
<!--									<img src='images/pwd.png' border=0>&nbsp;<a href='#' onmouseover="TagToTip('div_info_org',SHADOW,true,WIDTH,300,SHADOWWIDTH,3,BGCOLOR, '#f1f1f1',FONTCOLOR,'#000000',TITLEALIGN,'center',BORDERCOLOR,'#0099ff',CLOSEBTN,true,STICKY,true,CLOSEBTNTEXT,'Cerrar',TITLEBGCOLOR,'#0099ff',CLOSEBTNCOLORS, ['', '#ffffff', '', '#0066ff'])" onmouseout="UnTip()">VER INFORMACION</a>-->
									<div class="instruccion" style="height:100px"><img src='images/pwd.png' border=0>&nbsp;<font class='titulo_instruccion'>INFORMACION</font><br><?=$info_ficha->texto?></div>
								</td>
<!--								<div id="div_info_org" style="display:none"><?=$info_ficha->texto?></div>-->
							</tr>
						</table>
					</td>					
				</tr>
				<tr>
					<td valign="top" id='dato_reporte_1'>
						<table cellpadding="2" border="0" class='table_filtro_gra_resumen'>
							<tr><td class="titulo_filtro">&nbsp;<img src="images/gra_resumen/fl_filtro.gif">&nbsp;Dato Sectorial</td></tr>
							<tr>
								<td>
									<select id="id_dato_sectorial" name="id_dato_sectorial" class="select" onclick="if (this.value != '-1'){getAniosDato(this.value,'getAniosDatoSectorial');getDefinicionDato(this.value,'td_definicion_dato');}" size="10" style="width:560px">
									<?
									foreach ($id_cats_dato as $cate){
										echo "<option value='-1' style='background:#CCCCCC;color:#FFFFFF;' disabled>----------- Categoria: $cate->nombre -----------</option>";
										$dato_sectorial_dao->ListarCombo('combo','','ID_CATE = '.$cate->id);
										
									}
									?>
									</select>
									<!-- Filtrar datos sectoriales -->
									<script type="text/javascript">
									var myfilter = new filterlist(document.getElementById('id_dato_sectorial'));
									</script>
								</td>
							</tr>
				    		<tr><td><b>DEFINICION</b></td></tr>
	    					<tr><td><p id="td_definicion_dato" class="nota_gris" style="width:560px;">Se consulta al seleccionar un Dato</p></td></tr>
				    		<tr>
				    			<td>
				    			<b>Filtrar lista por letra inicial</b>&nbsp;
									<A HREF="javascript:myfilter.reset()" TITLE="Clear the filter">Todos</A>&nbsp;|
									<A HREF="javascript:myfilter.set('^A')" TITLE="Show items starting with A">A</A>
									<A HREF="javascript:myfilter.set('^B')" TITLE="Show items starting with B">B</A>
									<A HREF="javascript:myfilter.set('^C')" TITLE="Show items starting with C">C</A>
									<A HREF="javascript:myfilter.set('^D')" TITLE="Show items starting with D">D</A>
									<A HREF="javascript:myfilter.set('^E')" TITLE="Show items starting with E">E</A>
									<A HREF="javascript:myfilter.set('^F')" TITLE="Show items starting with F">F</A>
									<A HREF="javascript:myfilter.set('^G')" TITLE="Show items starting with G">G</A>
									<A HREF="javascript:myfilter.set('^H')" TITLE="Show items starting with H">H</A>
									<A HREF="javascript:myfilter.set('^I')" TITLE="Show items starting with I">I</A>
									<A HREF="javascript:myfilter.set('^J')" TITLE="Show items starting with J">J</A>
									<A HREF="javascript:myfilter.set('^K')" TITLE="Show items starting with K">K</A>
									<A HREF="javascript:myfilter.set('^L')" TITLE="Show items starting with L">L</A>
									<A HREF="javascript:myfilter.set('^M')" TITLE="Show items starting with M">M</A>
									<A HREF="javascript:myfilter.set('^N')" TITLE="Show items starting with N">N</A>
									<A HREF="javascript:myfilter.set('^O')" TITLE="Show items starting with O">O</A>
									<A HREF="javascript:myfilter.set('^P')" TITLE="Show items starting with P">P</A>
									<A HREF="javascript:myfilter.set('^Q')" TITLE="Show items starting with Q">Q</A>
									<A HREF="javascript:myfilter.set('^R')" TITLE="Show items starting with R">R</A>
									<A HREF="javascript:myfilter.set('^S')" TITLE="Show items starting with S">S</A>
									<A HREF="javascript:myfilter.set('^T')" TITLE="Show items starting with T">T</A>
									<A HREF="javascript:myfilter.set('^U')" TITLE="Show items starting with U">U</A>
									<A HREF="javascript:myfilter.set('^V')" TITLE="Show items starting with V">V</A>
									<A HREF="javascript:myfilter.set('^W')" TITLE="Show items starting with W">W</A>
									<A HREF="javascript:myfilter.set('^X')" TITLE="Show items starting with X">X</A>
									<A HREF="javascript:myfilter.set('^Y')" TITLE="Show items starting with Y">Y</A>
									<A HREF="javascript:myfilter.set('^Z')" TITLE="Show items starting with Z">Z</A>
				    			</td>
				    		</tr>
				    		<tr>
				    			<td>
									<b>Filtrar lista por palabra</b>:&nbsp;
									<input id="regexp" name="regexp" onKeyUp="myfilter.set(this.value)" class="textfield">	
				    			</td>
				    		</tr>
				    		<tr><td>&nbsp;</td></tr>
							</table>
					</td>
					<td valign="top" id="getAniosDatoSectorial">
						<!--<b>Periodo</b>: Año Inicial&nbsp;<input type="text" id="ini_dato" name="ini_dato" size="6" class="textfield">&nbsp;&nbsp;Año Final&nbsp;<input type="text" id="fin_dato" name="fin_dato" size="6" class="textfield">-->
					</td>
				</tr>
				<tr>
					<td valign="top" id='dato_reporte_2' style="display:none">
						<table cellpadding="2" border="0" class='table_filtro_gra_resumen'>
							<tr><td class="titulo_filtro">&nbsp;<img src="images/gra_resumen/fl_filtro.gif">&nbsp;Datos Sectoriales</td></tr>
							<tr><td>Seleccione mínimo 2 Datos de la misma categoria (Use la tecla Ctrl)</tr>
							<tr>
								<td>
									<select id="id_dato_reporte_2" name="id_dato_reporte_2" class="select" multiple size="20" onchange="if (this.value!='-1'){getAniosDato_reporte2('getAniosDatoSectorial_2');}">
									<?
									foreach ($id_cats_dato as $cate){
										echo "<option value='-1' style='background:#CCCCCC;color:#FFFFFF;' disabled>----------- Categoria: $cate->nombre -----------</option>";
										$dato_sectorial_dao->ListarCombo('combo','','ID_CATE = '.$cate->id);
										
									}
									?>
									</select>
								</td>
							</tr>
							</table>
					</td>
					<td valign="top" id="getAniosDatoSectorial_2"><br>
						<!--<b>Periodo</b>: Año Inicial&nbsp;<input type="text" id="ini_dato" name="ini_dato" size="6" class="textfield">&nbsp;&nbsp;Año Final&nbsp;<input type="text" id="fin_dato" name="fin_dato" size="6" class="textfield">-->
					</td>
				</tr>
				<tr><td>&nbsp;</td></tr>
				<tr>
					<td colspan="4" align="center">
						<input type="button" value="Generar Gráfica" name="submit_org" class="boton" onclick="graficarDatos('bar',0)">
						&nbsp;&nbsp;<input type="button" id="boton_regresar" value="Seleccionar Ubicación" class="boton" style="display:none" onclick="document.getElementById('td_mapa').style.display=''">
					</td>
				</tr>
				<tr><td id="graficaDatos" colspan="4" style="display:none" class="td_grafica_bcg"></td></tr>
			</table>
		</td>
	</tr>