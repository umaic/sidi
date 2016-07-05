<?
if (isset($_GET["m_g"]) && !isset($_GET["m_e"])){
	switch ($_GET["m_g"]){
	  case "consulta": ?>
		<div align="justify" style="padding:15px">
			La opción Reportes del Sistema de Información, le permite sacar listados por cualquiera de los módulos del Sistema, aplicando diferentes filtros (localización geográfica, rango de fechas, tema, demografía).
			<br><br>
			Después de preparar su propio reporte, ud. podrá exportarlo a formato PDF o EXCEL.
		</div>
		<?
		break;
	}
}

switch ($_SESSION["m_e"]){
	case "org": ?>
		  <div style='width:85%;margin:10px'>
	      La Base de Datos de organizaciones contiene información sobre las organizaciones que trabajan
		  en el campo humanitario en el país y/o con colombianos refugiados en el exterior.
		  Entre la información recopilada se encuentran los perfiles de las organizaciones,
		  los campos en los que trabajan, y los departamentos (o países) que cubren.
	    </div>
	<?
	break;

	case "tabla_grafico" : ?>
		<div style='width:85%;margin:10px'>
			<b>	Reporte Básico</b>: Esta opción generará el formato predeterminado de la minificha <br><br>
			<b>	Reporte Avanzado</b>: Esta opción permite generar tablas y gráficas de consutlas específicas<br>
		</div>
	<?
	break;

	case "grafica_org":  ?>
		<br>
		<ul id="menulat">
		      <li>GRAFICAS DE ORGANIZACIONES</li>
	    </ul>
	    <ul id="menulat">
	      <li><a href="index.php?m_e=grafica_org&accion=graficar&class=OrganizacionDAO&method=graficaConteo">Gráfica por Tipo, Población, Enfoque o Sector para una Ubicación</a></li>
	      <li><a href="index.php?m_e=grafica_org&accion=graficar&class=OrganizacionDAO&method=graficaConteoDeptoMpio">Gráfica por Departamento o Municipio para un Tipo, Población, Enfoque o Sector</a></li>
	    </ul>
	    <?
	break;

	case 'minificha': ?>
		<div style="padding:15px">
			<b>¿Qué es un perfil geográfico?</b>
			<br><br>
			Es una complicación de cifras estadísticas básicas provenientes de diferentes fuentes tanto del Gobierno como algunas ONGs.
			<br><br>
			También encontrará un mapa geográfico básico de la zona geográfica seleccionada.
			<br><br>
			Este producto es útil para conocer la información básica de un departamento o municipio.
			<br>
			Próximamente podrá encontrarlo por regiones.
			<br><br>
			<img src="images/stop.gif">&nbsp;<a href='#' onclick="document.getElementById('instrucciones').style.display=''">Ver instrucciones</a>			
			<!-- <br><br>
			<a href="#" onclick="return generarMinificha();"><img src="images/consulta/boton_minificha.jpg" border="0" title="Esta opción le permite generar la Minificha de la Ubicación seleccionada" onmouseover="this.src='images/consulta/boton_minificha.jpg'" onmouseout="this.src='images/consulta/boton_minificha.jpg'" /></a>
			 -->
		</div>
		<?
	break;
}


?>
