<!-- ORGANIZACIONES -->
	<tr>
		<td id="org" class="td_outer" colspan="10" style="display:<?=$style_display['org']?>">
			<table cellpadding="3" cellspacing="0" width="100%" border="0">
				<tr><td><img src="images/spacer.gif" height="10"></td> </tr>
				<tr>
					<td width='520' valign='top'>
						<table cellpadding="2" border="0" class='table_filtro_gra_resumen'>
							<tr><td class="titulo_filtro">&nbsp;<img src="images/gra_resumen/fl_filtro.gif">&nbsp;Reporte</td></tr>
							<tr><td><input type="radio" id="graficar_por_sector" name="graficar_por" value="sector" checked>&nbsp;Organizaciones por Sector</td></tr>
							<tr>
								<td>
									<input type="radio" id="graficar_por_tipo" name="graficar_por" value="sector">&nbsp;Organizaciones por Tipo
									&nbsp;
									<select id="tipo_papa" class="select">
										<option value="0">Todos</option>
										<option value="1">Internacional</option>
										<option value="2">Nacional-Estado</option>
										<option value="3">Nacional-Sociedad Civil</option>
									</select>
									
								</td>
							</tr>
							<tr><td><input type="radio" id="graficar_por_poblacion" name="graficar_por" value="sector">&nbsp;Organizaciones por Tipo Poblaci&oacute;n Beneficiaria
							<!--<br><input type="radio" name="graficar_por" value="sector">Organizaciones por Enfoque<br>-->
							</td></tr>
							<tr><td>&nbsp;</td></tr>
							<tr>
								<td align="center">
									<input type="button" value="Generar Gráfica" name="submit_org" class="boton" onclick="graficar('bar')">
									&nbsp;&nbsp;<input type="button" id="boton_regresar" value="Seleccionar Ubicación" class="boton" style="display:none" onclick="document.getElementById('td_mapa').style.display=''">
								</td>
							</tr>
						</table>					
					</td>
					<td valign="top">
						<table>
							<tr>
								<td>
								<?
									$info_ficha = $info_ficha_dao->GetAllArray("modulo = 'org'");
									?>
<!--									<img src='images/pwd.png' border=0>&nbsp;<a href='#' onmouseover="TagToTip('div_info_org',SHADOW,true,WIDTH,300,SHADOWWIDTH,3,BGCOLOR, '#f1f1f1',FONTCOLOR,'#000000',TITLEALIGN,'center',BORDERCOLOR,'#0099ff',CLOSEBTN,true,STICKY,true,CLOSEBTNTEXT,'Cerrar',TITLEBGCOLOR,'#0099ff',CLOSEBTNCOLORS, ['', '#ffffff', '', '#0066ff'])" onmouseout="UnTip()">VER INFORMACION</a>-->
									<div class="instruccion" style="height:130px"><img src='images/pwd.png' border=0>&nbsp;<font class='titulo_instruccion'>INFORMACION</font><br><?=$info_ficha->texto?></div>
								</td>
<!--								<div id="div_info_org" style="display:none"><?=$info_ficha->texto?></div>-->
							</tr>
						</table>
					</td>
				</tr>
<!--				<tr><td class="nota_bcg" colspan="2">:: No olvide que luego de generada la gráfica, el sistema presenta la opci&oacute;n de generar un reporte detallado de la consulta, para esto, use el bot&oacute;n <b>Generar Reporte</b> que se mostrar&aacute; bajo la gr&aacute;fica una vez sea generada ::</tr>-->
				<tr><td>&nbsp;</td></tr>
				<tr><td id="graficaConteoOrg" colspan=2 class="td_grafica_bcg" style="display:none"></td></tr>
			</table>
		</td>
	</tr>