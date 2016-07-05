<?
//LIBRERIAS
include_once("consulta/lib/libs_mapa_i.php");
include_once("admin/lib/common/graphic.class.php");

//INICIALIZACION DE VARIABLES
$depto_dao = New DeptoDAO();
$municipio_vo = New Municipio();
$municipio_dao = New MunicipioDAO();
$org_dao = New OrganizacionDAO();
$proy_dao = New ProyectoDAO();
$eve_dao = New EventoDAO();
$dato_sectorial_dao = New DatoSectorialDAO();
$desplazamiento_dao = New DesplazamientoDAO();
$mina_dao = New MinaDAO();

$id_depto = Array();
$num_deptos = 0;
if (isset($_GET['id_depto'])){
	$id_depto_s = split(",",$_GET['id_depto']);
	$id_depto = $id_depto_s[0];
	$num_deptos = count($id_depto_s);
	$id_depto = $id_depto_s;
	$id_depto_url = $id_depto;
}

$vista = 1;
if (isset($_GET["vista"]) && $_GET["vista"] == 2){
	if ($_GET["vista"] == 2){
		$vista = 2;
	}
}

//MINIFICHA
if (isset($_POST["minificha_x"])){
	$id_muns = "";
	if (isset($_POST["id_muns"]))	$id_muns = $_POST["id_muns"][0];
	$sissh = New SisshDAO();
	$sissh->Minificha($_POST["id_depto"][0],$id_muns);
	die;
}

//REPORTE EXCEL - PDF
if (isset($_POST["reportar"])){

	//ORGS
	if ($_POST['id_orgs'] != ""){
		$org_dao->ReporteOrganizacion($_POST['id_orgs'],$_POST['pdf'],$_POST['basico']);
	}
	//PROYS
	if ($_POST['id_proyectos'] != ""){
		$proy_dao->ReporteProyecto($_POST['id_proyectos'],$_POST['pdf'],$_POST['basico']);
	}
	//EVENTOS
	if ($_POST['id_eventos'] != ""){
		$eve_dao->ReporteEvento($_POST['id_eventos'],$_POST['pdf'],$_POST['basico']);
	}
	//DATOS
	if ($_POST['id_datos'] != ""){
		$dato_sectorial_dao->ReporteDatoSectorial($_POST['id_datos'],$_POST['pdf'],$_POST['basico'],$_POST['dato_para']);
	}
	//DESPLAZAMIENTO
	if ($_POST['id_desplazamientos'] != ""){
		$desplazamiento_dao->ReporteDesplazamiento($_POST['id_desplazamientos'],$_POST['pdf'],$_POST['basico'],$_POST['dato_para']);
	}
	//MINA
	if ($_POST['id_minas'] != ""){
		$mina_dao->ReporteMina($_POST['id_minas'],$_POST['pdf'],$_POST['basico']);
	}
}

?>

<script>
function validarComboM(ob){
	selected = new Array();
	for (var i = 0; i < ob.options.length; i++){
		if (ob.options[ i ].selected)
		selected.push(ob.options[ i ].value);
	}

	if (selected.length == 0){
		return false;
	}
	else{
		return true;
	}
}
function validar_criterios(){

	var error = 1;
	var error_que = 0;
	var error_sede = 0;
	var filtros = Array('id_depto');

	if (document.getElementById('id_depto').value != ""){
		error = 0;
	}

	if (document.getElementById('id_muns').value != ""){
		error = 0;
	}

	if (document.getElementById('que_org').checked == false && document.getElementById('que_proy').checked == false && document.getElementById('que_eve').checked == false && document.getElementById('que_dato').checked == false && document.getElementById('que_desplazamiento').checked == false && document.getElementById('que_mina').checked == false){
		error_que = 1;
	}
	if (document.getElementById('que_org').checked == true){
		if (document.getElementById('sede').checked == false && document.getElementById('cobertura').checked == false){
			error_sede = 1;
		}
	}

	if (error == 1 && error_que == 1){
		alert("Seleccione algun Departamento y lo que desea consultar (Organizaciones, Proeyctos, Eventos, Datos Sectoriales, Datos de Desplazamiento)");
		return false;
	}

	else if (error == 1 && error_que == 0){
		alert("Seleccione algun Departamento o Municipio");
		return false;
	}

	else if (error == 0 && error_que == 1){
		alert("Seleccione lo que desea consultar (Organizaciones, Proeyctos, Eventos, Datos Sectoriales, Datos de Desplazamiento, Eventos con Mina)");
		return false;
	}

	else if (error == 0 && error_que == 0 && error_sede == 1){
		alert("Especifique si la Organizacion debe tener Sede o Cobertura en la Ubicación seleccionada");
		return false;
	}

	else{
		return true;
	}

}
function generarMinificha(){

	var error = 1
	if (document.getElementById('id_depto').value != ""){
		error = 0;
	}

	if (document.getElementById('id_muns').value != ""){
		error = 0;
	}

	else if (error == 1){
		alert("Seleccione algun Departamento o Municipio");
		return false;
	}

}
</script>

<?
if (!isset($_POST["submit"]) && !isset($_POST["pdf"])){
  ?>

<form action="index.php?m_e=mapa_i&accion=consultar&class=MapaI" method="POST">
<table align='center' cellspacing="1" cellpadding="3" border="0">
	<tr><td align='center' class='titulo_lista' colspan=2>CONSULTA POR UBICACION GEOGRAFICA</td></tr>
	<tr><td>&nbsp;</td></tr>
	<tr>
		<td valign="top">
			<table cellspacing="1" cellpadding="5" border="0" width="100%" class="tabla_consulta">
				<tr class='titulo_lista'><td><b>Que desea consultar</b></td></tr>
				<? if (in_array(15,$perfil->id_modulo)){?>
					<tr class="bcg_F2F2F2_000000"><td><input id="que_org" type="checkbox" name="que[]" value="1">&nbsp;Organizaciones: <br><br>&nbsp;&nbsp;&nbsp;&nbsp&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type="checkbox" id="sede" name="sede">&nbsp;Sede&nbsp;&nbsp;<input type="checkbox" id="cobertura" name="cobertura">Cobertura&nbsp;</td></tr>
				<? } ?>
				<? if (in_array(16,$perfil->id_modulo)){?>
					<tr class="bcg_F2F2F2_000000"><td><input id="que_proy" type="checkbox" name="que[]" value="2">&nbsp;Proyectos</td></tr>
				<? } ?>
				<? if (in_array(17,$perfil->id_modulo)){?>
					<tr class="bcg_F2F2F2_000000"><td><input id="que_eve" type="checkbox" name="que[]" value="3">&nbsp;Eventos</td></tr>
				<? } ?>
				<? if (in_array(18,$perfil->id_modulo)){?>
					<tr class="bcg_F2F2F2_000000"><td><input id="que_dato" type="checkbox" name="que[]" value="4">&nbsp;Datos Sectoriales</td></tr>
				<? } ?>
				<? if (in_array(22,$perfil->id_modulo)){?>
					<tr class="bcg_F2F2F2_000000"><td><input id="que_desplazamiento" type="checkbox" name="que[]" value="5">&nbsp;Datos de Desplazamiento: <br><br>&nbsp;&nbsp;&nbsp;&nbsp&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type="checkbox" name="exp_rec[]" value="1" checked>&nbsp;&nbsp;Expulsor&nbsp;<input type="checkbox" name="exp_rec[]" value="2">Receptor&nbsp;</td></tr>
				<? } ?>
				<? if (in_array(24,$perfil->id_modulo)){?>
					<tr class="bcg_F2F2F2_000000"><td><input id="que_mina" type="checkbox" name="que[]" value="6">&nbsp;Eventos por Mina</td></tr>
				<? } ?>
			</table>
			<br><br><div align='center'>
				<input type="hidden" name="accion" value="consultar" />
				<input type="submit" name="submit" value="Siguiente" onclick="return validar_criterios()" class="boton" />
				</div>
				<!--<br><br><div align='center'>
				<input type="image" name="minificha" src="images/consulta/boton_minificha.jpg" border="0" onclick="return generarMinificha();" title="Esta opción le permite generar la Minificha de la Ubicación seleccionada" />
				</div>-->

		</td>

		<td id="td_cobertura">
			<table cellspacing="1" cellpadding="5" border="0" width="450" class="tabla_consulta">
			<?
			//MAPA INTERACTIVO
			if ($vista == 1){ ?>
				<tr class="titulo_lista"><td><b>Seleccione la Ubicaci&oacute;n</b></td></tr>
				<tr>
				  <td><iframe src="consulta/swf/mapa_i.html" height="600" width="750" frameborder="0"></iframe></td>
				</tr>
			</table>
			<input type="hidden" id="id_depto" name="id_depto[]">
			<input type="hidden" id="id_muns" name="id_muns[]" disabled>
			<?
			}
			else if ($vista == 2){ ?>

	  <tr class="titulo_lista"><td><b>Seleccione el Departamento</b></td></tr>
				<tr><td>&nbsp;</td></tr>
				<tr>
					<td>
						<select id="id_depto" name="id_depto[]"  multiple size="8" class="select">
							<?
							//DEPTO
							$depto_dao->ListarCombo('combo',$id_depto,'');
							?>
						</select>
					</td>
				</tr>
				<tr><td>&nbsp;</td></tr>
				<tr>
				  <td>
				  <a href="#" onclick="enviar_deptos('index.php?accion=consultar&class=MapaI');">Listar Muncipios del Depto. para refinar la consulta</a>
				  </td>
				</tr>
				<tr><td>&nbsp;</td></tr>

				<? if (isset($_GET['id_depto'])){ ?>
				<tr class="titulo_lista"><td><b>Seleccione el Municipio</b></td></tr>
				<tr><td>&nbsp;</td></tr>
				<tr>
					<td>
						<select id="id_muns" name="id_muns[]" multiple size="8" class="select">
							<?
							//MUNICIPIO
							for($d=0;$d<$num_deptos;$d++){
								$id = "'".$id_depto[$d]."'";
								$depto = $depto_dao->Get($id);
								$muns = $municipio_dao->GetAllArray('ID_DEPTO ='.$id);

								echo "<option value='' disabled>-------- ".$depto->nombre." --------</option>";
								foreach ($muns as $mun){
									echo "<option value='".$mun->id."'>".$mun->nombre."</option>";
								}

							}
							?>
			  			</select>
					</td>
				</tr>
				<tr>
			    <? } ?>

			</table>

			<? } ?>
		</td>
	</tr>
	</table>
</form>
<?
}
//REPORTE
if (isset($_POST["submit"])){
	$que = $_POST["que"];

	echo "<table align='center' cellspacing='0' cellpadding='0'>";
	if (in_array(1,$que)){
		echo "<tr><td>";
		//echo "<form action='index.php?m_e=org&accion=consultar&class=MapaI' method='POST'>";
		echo "<form action='consulta/export_data_org.php' method='POST' target='_blank'>";
		$org_dao->ReportarMapaI();
		echo "<input type='hidden' id='pdf_org' name='pdf'>";
		echo "</form>";
		echo "</td></tr>";
	}
	if (in_array(2,$que)){
		echo "<tr><td>";
		echo "<form action='index.php?m_e=proyecto&accion=consultar&class=MapaI' method='POST'>";
		$proy_dao->ReportarMapaI();
		echo "<input type='hidden' id='pdf_proy' name='pdf'>";
		echo "</form>";
		echo "</td></tr>";
	}
	if (in_array(3,$que)){
		echo "<tr><td>";
		echo "<form action='index.php?m_e=eve&accion=consultar&class=MapaI' method='POST'>";
		$eve_dao->ReportarMapaI();
		echo "<input type='hidden' id='pdf_eve' name='pdf'>";
		echo "</form>";
		echo "</td></tr>";
	}
	if (in_array(4,$que)){
		echo "<tr><td>";
		echo "<form action='index.php?m_e=dato_sectorial&accion=consultar&class=MapaI' method='POST'>";
		$dato_sectorial_dao->ReportarMapaI();
		echo "<input type='hidden' id='pdf_dato' name='pdf'>";
		echo "</form>";
		echo "</td></tr>";
	}
	if (in_array(5,$que)){
		echo "<tr><td>";
		echo "<form action='index.php?m_e=desplazamiento&accion=consultar&class=MapaI' method='POST'>";
		$desplazamiento_dao->ReportarMapaI();
		echo "<input type='hidden' id='pdf_desplazamiento' name='pdf'>";
		echo "</form>";
		echo "</td></tr>";
	}
	if (in_array(6,$que)){
		echo "<tr><td>";
		echo "<form action='index.php?m_e=mina&accion=consultar&class=MapaI' method='POST'>";
		$mina_dao->ReportarMapaI();
		echo "<input type='hidden' id='pdf_mina' name='pdf'>";
		echo "</form>";
		echo "</td></tr>";
	}
	echo "</table>";
}

if (isset($_POST["pdf"]) && !isset($_POST["reportar"])){

	$_POST['pdf'] == 1 ? $t = "PDF" : $t = "CSV (Excel)";

	/*if ($_POST['pdf'] == 1){
	//echo "<form action='admin/reporte_pdf.php' method='POST' target='_blank'>";
	echo "<form action='index.php?m_e=mapa_i&accion=consultar&class=OrganizacionDAO' method='POST'>";
	$t = "PDF";
	}*/

	$id_orgs = "";
	if (isset($_POST["que_org"])){
		$id_orgs = $_POST["id_orgs"];
		echo "<form action='index.php?m_e=org&accion=consultar&class=OrganizacionDAO' method='POST'>";
		/*if ($_POST['pdf'] == 2){
		//echo "<form action='index.php?m_e=org&accion=consultar&class=MapaI' method='POST'>";
		$t = "CSV (Excel)";
		}*/
	}

	$id_proyectos = "";
	if (isset($_POST["que_proy"])){
		$id_proyectos = $_POST["id_proyectos"];
		echo "<form action='index.php?m_e=org&accion=consultar&class=ProyectoDAO' method='POST'>";

		/*if ($_POST['pdf'] == 2){
		echo "<form action='index.php?m_e=proyecto&accion=consultar&class=MapaI' method='POST'>";
		$t = "CSV (Excel)";
		}*/
	}

	$id_eventos = "";
	if (isset($_POST["que_eve"])){
		$id_eventos = $_POST["id_eventos"];
		echo "<form action='index.php?m_e=org&accion=consultar&class=EventoDAO' method='POST'>";
		/*if ($_POST['pdf'] == 2){
		echo "<form action='index.php?m_e=eve&accion=consultar&class=MapaI' method='POST'>";
		$t = "CSV (Excel)";
		}*/
	}

	$id_datos = "";
	if (isset($_POST["id_datos"])){
		$id_datos = $_POST["id_datos"];
		echo "<form action='index.php?m_e=org&accion=consultar&class=DatoSectorialDAO' method='POST'>";

		/*if ($_POST['pdf'] == 2){
		echo "<form action='index.php?m_e=dato_sectorial&accion=consultar&class=MapaI' method='POST'>";
		$t = "CSV (Excel)";
		}*/
	}

	$id_desplazamientos = "";
	$dato_para = "";
	if (isset($_POST["id_desplazamientos"])){
		$id_desplazamientos = $_POST["id_desplazamientos"];
		$dato_para = $_POST["dato_para"];
		echo "<form action='index.php?m_e=org&accion=consultar&class=DesplazamientoDAO' method='POST'>";
		/*if ($_POST['pdf'] == 2){
		echo "<form action='index.php?m_e=desplazamiento&accion=consultar&class=MapaI' method='POST'>";
		$t = "CSV (Excel)";
		}*/
	}
	$id_minas = "";
	if (isset($_POST["id_minas"])){
		$id_minas = $_POST["id_minas"];
		echo "<form action='index.php?m_e=org&accion=consultar&class=MinaDAO' method='POST'>";
		/*if ($_POST['pdf'] == 2){
		echo "<form action='index.php?m_e=mina&accion=consultar&class=MapaI' method='POST'>";
		$t = "CSV (Excel)";
		}*/
	}

    ?>
	<table align='center' cellspacing="1" cellpadding="3" border="0" width="750">
		<tr><td align='center' class='titulo_lista' colspan=2>REPORTE EN FORMATO <?=$t?></td></tr>
		<tr><td>&nbsp;</td></tr>
		<tr><td colspan=2>
			Seleccione el formato del reporte:<br>&nbsp;
		</td></tr>
		<tr>
		  <td>
		  <?
		  if (isset($_POST["que_org"])){
		  	echo "<input type='radio' name='basico' value='1' checked>&nbsp;<b>Reporte B&aacute;sico:</b>: Muestra los datos b&aacute;sico de la Organizaci&oacute;n (Nombre,Sigla,Tipo,Cobertura)";
		  }
		  if (isset($_POST["que_proy"])){
		  	echo "<input type='radio' name='basico' value='1' checked>&nbsp;<b>Reporte B&aacute;sico</b>: Muestra los datos b&aacute;sico de la Proyecto (Nombre,Estado,Objetivo,Fecha)";
		  }
		  if (isset($_POST["que_eve"])){
		  	echo "<input type='radio' name='basico' value='1' checked>&nbsp;<b>Reporte Est&aacute;ndar</b>";
		  }
		  if (isset($_POST["que_dato"])){
		  	echo "<input type='radio' name='basico' value='1' checked>&nbsp;<b>Reporte Est&aacute;ndar</b>";
		  }
		  if (isset($_POST["que_desplazamiento"])){
		  	echo "<input type='radio' name='basico' value='1' checked>&nbsp;<b>Reporte Est&aacute;ndar</b>";
		  }
		  if (isset($_POST["que_mina"])){
		  	echo "<input type='radio' name='basico' value='1' checked>&nbsp;<b>Reporte Est&aacute;ndar</b>";
		  }
		  ?>
		  </td>
		</tr>
		<?
		if (isset($_POST["que_org"]) || isset($_POST["que_proy"])){
		?>
			<tr><td><input type="radio" name="basico" value="2">&nbsp;<b>Reporte Detallado</b>: Muestra toda la informaci&oacute;n</td></tr>
		<? } ?>
		<tr><td>&nbsp;</td></tr>
		<tr><td align='center'>
			<input type="hidden" name="pdf" value="<?=$_POST['pdf']?>" />
			<input type="hidden" name="id_orgs" value="<?=$id_orgs?>" />
			<input type="hidden" name="id_proyectos" value="<?=$id_proyectos?>" />
			<input type="hidden" name="id_eventos" value="<?=$id_eventos?>" />
			<input type="hidden" name="id_datos" value="<?=$id_datos?>" />
			<input type="hidden" name="id_desplazamientos" value="<?=$id_desplazamientos?>" />
			<input type="hidden" name="id_minas" value="<?=$id_minas?>" />
			<input type="hidden" name="dato_para" value="<?=$_POST['dato_para']?>" />
			<!--<input type="hidden" name="class" value="MapaI" />
			<input type="hidden" name="method" value="ReporteMapaI" />-->
			<input type="submit" name="reportar" value="Siguiente" class="boton" />
		</td></tr>
	</table>
	</form>
<?
}

?>