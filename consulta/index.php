<?
if(!isset($_SESSION["id_usuario_s"])){
	header("Location: ../login.php?m_g=consulta");
}

include_once("lib/libs_mapa_i.php");
?>
<script>
function changeBCG(id_elemento,accion,color){
	document.getElementById(id_elemento).style.background=color;
}
</script>
<table width='650' align='center' cellspacing='1' cellpadding='5'>
<tr>
	<!-- ORGS -->
	<?
	if (in_array(15,$perfil->id_modulo)){
		$org_dao = New OrganizacionDAO();
		$num = $org_dao->numRecords('')	;
		?>
		<td valign='top'>
			<table id="td_org" cellspacing='0' cellpadding='10' border="0" class="home_consulta">
				<tr>
					<td width=48><img src="images/consulta/icn_org.png"></td>
					<td>
						<h1><a href='index.php?m_e=org&accion=consultar&class=OrganizacionDAO'>Organizaciones</a></h1>[ <b><?=$num?> Registros</b> ]</td>
				</tr>
				<tr>
					<td colspan=2>
						Este m&oacute;dulo le permite realizar consultas de Organizaciones por:
						<br><br>
						&raquo;&nbsp; Tipo&nbsp;&raquo;&nbsp; Sector&nbsp;&raquo;&nbsp; Enfoque<br>
						&raquo;&nbsp; Poblaci&oacute;n&nbsp;&raquo;&nbsp; Cobertura Geográfica
					</td>
				</tr>
			</table>
		</td>
	<? } ?>
	<?
	if (in_array(22,$perfil->id_modulo)){	
		$desplazamiento_dao = New DesplazamientoDAO();
		$num = $desplazamiento_dao->numRecords('');
		?>
	<!-- DESPLAZAMIENTO -->
	<td valign='top'>
		<table id="td_org" cellspacing='0' cellpadding='10' border="0" class="home_consulta">
			<tr>
				<td width=48><img src="images/consulta/icn_despla.png"></td>
				<td>
					<h1><a href='index.php?m_e=desplazamiento&accion=consultar&class=DesplazamientoDAO'>Desplazamiento</a></h1>[ <b><?=$num?> Registros</b> ]</td>
			</tr>
			<tr>
				<td colspan=2>
					Este m&oacute;dulo le permite realizar consultas de Desplazamiento por:
					<br><br>
					&raquo;&nbsp; Tipo&nbsp;&raquo;&nbsp; Clase&nbsp;&raquo;&nbsp; Fuente&nbsp;&raquo;&nbsp; Periodo<br>
					&raquo;&nbsp; Poblaci&oacute;n&nbsp;&raquo;&nbsp; Cobertura Geográfica
				</td>
			</tr>
		</table>
	</td>
	<? } ?>
	</tr>
	<tr>
	<?
	if (in_array(18,$perfil->id_modulo)){
		$d_s_dao = New DatoSectorialDAO();
		$num_valores = $d_s_dao->numRecordsValores('');
		$num = $d_s_dao->numRecords('');

		?>
		<!-- DATO SECTORIAL -->
		<td valign='top'>
			<table id="td_org" cellspacing='0' cellpadding='10' border="0" class="home_consulta">
				<tr>
					<td width=48><img src="images/consulta/icn_d_s.png"></td>
					<td>
						<h1><a href='index.php?m_e=dato_sectorial&accion=consultar&class=DatoSectorialDAO'>Datos Sectoriales</a></h1>[ <b><?=$num?> Registros</b> ]</td>
				</tr>
				<tr>
					<td colspan=2>
						Este m&oacute;dulo le permite realizar consultas de Datos Sectoriales por:
						&raquo;&nbsp; Categor&iacute;a&nbsp;&raquo;&nbsp; Cobertura Geográfica
						<br><br>
						<h1><img src="images/consulta/icn_metadata.png"><a href="index.php?m_e=dato_sectorial&accion=consultar&class=DatoSectorialDAO&method=ReportarMetadatos">Metadatos</a></h1>
					</td>
				</tr>
			</table>
		</td>
	<? } ?>

	<?
	/*if (in_array(17,$perfil->id_modulo)){	 ?>
	<!-- EVENTOS -->
	<td valign='top'>
		<table cellspacing='1' cellpadding='3' class='tabla_consulta' width='237' height="250">
			<tr><td><a href='index.php?m_e=evento&accion=consultar&class=EventoDAO'><img src="images/consulta/boton_evento.jpg" border="0"></a></td></tr>
			<!--<tr><td class='titulo_lista'><a href='index.php?m_e=evento&accion=consultar&class=EventoDAO'>EVENTOS</a></td></tr>-->
			<tr>
				<td>
				Este m&oacute;dulo le permite realizar consultas de Eventos por:<br><br>
				- Tipo <br>
				- Actor <br>
				- Consecuencias Humanitarias <br>
				- Riesgos Humanitarios  <br><br>

				También puede consultar:<br><br>
				- <a href='index.php?accion=consultar&class=EventoDAO&method=ReporteDiario'>Informe Diario</a><br>
				- <a href='index.php?accion=consultar&class=EventoDAO&method=ReporteSemanal'>Informe Semanal</a>

				</td></tr>
		</table>
	</td>
	<? } */?>
	
	<!-- EVENTOS CONFLICTO -->
	<?
	if (in_array(31,$perfil->id_modulo)){	 
		$evento_dao = New EventoConflictoDAO();
		$num = $evento_dao->numRecords('');
		?>
	
		<td valign='top'>
			<table id="td_org" cellspacing='0' cellpadding='10' border="0" class="home_consulta">
				<tr>
					<td width=48><img src="images/consulta/icn_evento_c.png"></td>
					<td>
						<h1><a href='index.php?m_e=evento_c&accion=consultar&class=EventoConflictoDAO'>Eventos Conflicto</a></h1>[ <b><?=$num?> Registros</b> ]</td>
				</tr>
				<tr>
					<td colspan=2>
						Este m&oacute;dulo le permite realizar consultas de Eventos por:
						<br><br>
						&raquo;&nbsp; Tipo/Subtipo de Evento&nbsp;&raquo;&nbsp; Tipo/Subtipo de Actor<br>
						&raquo;&nbsp; Cobertura Geográfica
					</td>
				</tr>
			</table>
		</td>
	<? } ?>
	
	<?
	if (in_array(24,$perfil->id_modulo)){	 ?>
	<!-- MINAS -->
	<!--<td valign='top' align="right">
		<table cellspacing='0' cellpadding='0' width='237' height="250">
			<tr><td height="48"><a href='index.php?m_e=mina&accion=consultar&class=MinaDAO' onmouseover="changeBCG('td_mina','over','#EEF1FF')" onmouseout="changeBCG('td_mina','over','#FFFFFF')"><img src="images/consulta/boton_mina.jpg" border="0"></a></td></tr>
			<tr>
				<td id="td_mina" valign="top">
					<table cellpadding="3" cellspacing="3" class="home_consulta"  height="202">
						<tr>
							<td align='left'>
							Este m&oacute;dulo le permite consultar Eventos con Mina por:<br><br>
							&raquo;&nbsp; Tipo de Evento<br>
							&raquo;&nbsp; Condici&oacute;n <br>
							&raquo;&nbsp; Estado <br>
							&raquo;&nbsp; Edad  <br>
							&raquo;&nbsp; Sexo  <br>
							&raquo;&nbsp; Ubicaci&oacute;n Geográfica
							</td>
						</tr>
					</table>
				</td>
			</tr>
		</table>	
	</td>-->
	<? } ?>
	</tr>
	<tr>
	<!-- PROYECTOS -->
	<?
	if (in_array(16,$perfil->id_modulo)){
		$pro_dao = New ProyectoDAO();
		$num = $pro_dao->numRecords('');

		?>
		<!--<td valign='top'>
			<table id="td_org" cellspacing='0' cellpadding='10' border="0" class="home_consulta">
				<tr>
					<td width=48><img src="images/consulta/icn_proy.png"></td>
					<td>
						<h1><a href='index.php?m_e=proyecto&accion=consultar&class=ProyectoDAO'>Proyectos</a></h1>[ <b><?=$num?> Registros</b> ]</td>
				</tr>
				<tr>
					<td colspan=2>
						Este m&oacute;dulo le permite realizar consultas de Proyectos por:
						<br><br>
						&raquo;&nbsp; Tipo&nbsp;&raquo;&nbsp; Sector&nbsp;&raquo;&nbsp; Estado&nbsp;&raquo;&nbsp; Fecha<br>
						&raquo;&nbsp; Poblaci&oacute;n&nbsp;&raquo;&nbsp; Cobertura Geográfica
						<br>&nbsp;
					</td>
				</tr>
			</table>
		</td>-->
	<? } ?>
  </tr>
	<!-- MAPA INTERACTIVO -->
  <!--<tr>
	<td valign='top' colspan="2">
		<table cellspacing='1' cellpadding='3' class='tabla_consulta' width='300'>
			<tr><td><a href='index.php?m_e=mapa_i&accion=consultar&class=MapaI'><img src="images/consulta/boton_u_g.jpg" border="0"></a></td></tr>
			<tr>
				<td><p align="justify">
				Este m&oacute;dulo le permite realizar consultas de Organizaciones, Proyectos o Eventos por Departamento o Municipio
				</p></td></tr>
		</table>
	</td>
</tr>-->

</table>
