<?
//INICIALIZACION DE VARIABLES
$org_vo = New OrganizacionMO();
$org_dao = New OrganizacionDAO();
$municipio_vo = New Municipio();
$municipio_dao = New MunicipioDAO();
$depto_vo = New Depto();
$depto_dao = New DeptoDAO();
$region_vo = New Region();
$region_dao = New RegionDAO();
$poblado_vo = New Poblado();
$poblado_dao = New PobladoDAO();
$resguardo_vo = New Resguardo();
$resguardo_dao = New ResguardoDAO();
$parque_nat_vo = New ParqueNat();
$parque_nat_dao = New ParqueNatDAO();
$div_afro_vo = New DivAfro();
$div_afro_dao = New DivAfroDAO();
$tipo_org_dao = New TipoOrganizacionDAO();
$tipo_org_vo = New TipoOrganizacion();
$sector_dao = New SectorDAO();
$sector_vo = New Sector();
$enfoque_dao = New EnfoqueDAO();
$enfoque_vo = New Enfoque();
$poblacion_dao = New PoblacionDAO();
$poblacion_vo = New Poblacion();
$conn = MysqlDb::getInstance();

$sectores = $sector_dao->GetAllArray('ID_COMP NOT IN (14,21)');
$poblaciones = $poblacion_dao->GetAllArray('','','');
$verifi = 0;

if (isset($_GET["accion"])){
	$accion = $_GET["accion"];
}
else if (isset($_POST["accion"])){
	$accion = $_POST["accion"];
}

$condicion = 'MAPP_OEA = 1';

//Cesar = 20
$id_depto = 20;
$num_deptos = 0;
$id_cat = 0;
$chk_conf = "";
if (isset($_GET['id_depto'])){
	$id_depto_s = split(",",$_GET['id_depto']);

	$num_deptos = count($id_depto_s);

	if($num_deptos == 1)
	$id_depto = $id_depto_s[0];
	else
	$id_depto = $id_depto_s;
}

//VIENE DE LA VERIFICACION
if (isset($_GET["nombre"])){
	//$_SESSION["nombre_org"] = urlencode($_GET["nombre"]);
	$_SESSION["nombre_org"] = $_GET["nombre"];

	$org_vo->nom = urldecode($_GET["nombre"]);
	$verifi = 1;
}

if (isset($_GET["verifi"])){
	$verifi = 1;
	$org_vo->nom = stripslashes($_SESSION["nombre_org"]);
}

//Caso de Actualizacion
$id = 0;
if ($accion == "actualizar_mo" && !isset($_POST["submit"])){

	$id = $_GET["id"];
	$org_vo = $org_dao->Get($id);

	//lOCK LA ORG
	$org_dao->lockOrgMO($id);

	//DEPTO SEDE
	$mun_sede = $municipio_dao->Get($org_vo->id_mun_sede);
	if (!isset($_GET["actualizar_mo_cobertura"]) && !isset($_GET["id_depto"])){
		$id_depto = $mun_sede->id_depto;
	}
	else if (isset($_GET["id_depto"]) && isset($_GET["depto_ch"])){
		$id_depto_s = split(",",$_GET['id_depto']);
		$org_vo->id_deptos = $id_depto_s;
		$id_depto = $id_depto_s[0];
	}
	else{
		$id_depto = $org_vo->id_deptos;
	}

	$id_donantes = implode("|",$org_vo->id_donantes);
	//NACIMIENTO
	if ($org_vo->naci == 0)	$org_vo->naci = "";

	$num_deptos = count($id_depto);
}

?>

<script src="js/ajax.js"></script>
<script>
function unSelectNombreDepto(){
	
	ob = document.getElementById('id_muns');
	
	for (var i = 0; i < ob.options.length; i++){
		if (ob.options[ i ].text.indexOf("--------") > -1)
			ob.options[ i ].selected = false;
	}
}
function buscarOrgs(){

	texto = document.getElementById('s').value;
	
	/*
	keyNum = e.keyCode;
		
	if (keyNum == 8){  //Backspace
		texto = texto.slice(0, -1);  //Borra el ultimo caracter
	}
	else{
		keyChar = String.fromCharCode(keyNum);
		texto +=  keyChar;
	}
	*/
	
	if (texto.length > 1){
		document.getElementById('ocurrenciasOrg').style.display='';
		getDataV1('ocurrenciasOrg','ajax_data.php?object=ocurrenciasOrg&case='+document.getElementById('case').options[document.getElementById('case').selectedIndex].value+'&s='+texto,'ocurrenciasOrg')
	}
	
	//El valor de donde, se coloca en js/ajax.js
}
function validar(){
	if (validar_forma('nombre,Nombre,id_tipo,Tipo,id_mun_sede,Municipio Sede,dir,Dirección,tel1,Teléfono 1','')){
		
		var error = '';
		var obj = document.getElementsByName('id_sectores[]');
		if (!checkInputChecked(obj)){
			error += '- Sector\n';
		}
		
		obj = document.getElementsByName('id_poblaciones[]');
		if (!checkInputChecked(obj)){
			error += '- Población Beneficiaria\n';
		}

		obj = document.getElementsByName('id_muns[]');
		if (!checkInputChecked(obj)){
			error += '- Cobertura Geográfica por municipio';
		}

		if (error != ''){
			alert("Los siguientes campos son requeridos:\n\n" + error);
			return false;
		}

		else{
			//Aviso de campos vacios
			var id_nr = Array('sigla','n_rep','t_rep','naci','pu_email','web','tel2','fax');
			var name_nr = Array('Sigla','Representante','Titulo Representante','Año de fundación en Colombia','Email','Página web','Teléfono 2','Fax');

			for (var c=0;c<id_nr.length;c++){
				obj = document.getElementById(id_nr[c]);
				
				
				if (obj.value == ''){
					error += "- " + name_nr[c] + '\n';
				}

			}
			
			//Check trabaja y conoce
			var  vacio = 1;
			obj = document.getElementsByName('org_conoce_nombre[]');
			for (c=0;c<obj.length;c++){
				if (obj.item(c).value != '')	vacio = 0;
			}

			if (vacio == 1)	error += '- ¿Cuáles otras organizaciones conoce?\n';

			vacio = 1;
			obj = document.getElementsByName('org_trabaja_nombre[]');
			for (c=0;c<obj.length;c++){
				if (obj.item(c).value != '')	vacio = 0;
			}

			if (vacio == 1)	error += '- ¿Con cuáles otras organizaciones trabaja? \n';
			
			if (error != ''){
				return confirm("Los siguientes campos NO son obligatorios pero estan vacios:\n\n" + error + "\n=======> Desea continuar? ");
			}
			else{
				<?
				//Alerta actualizar
				if ($accion == 'actualizar_mo'){
					echo "return confirm('Está seguro que desea modificar la información?');";
				}
				else	echo "return true";
				?>
			}
		}
	}
	else{
		return false;
	}
	
	
}
</script>
<form action="<?=$_SERVER['PHP_SELF']?>" method="POST">
	<table border="0" cellpadding="5" cellspacing="1" width="80%" align="center" class="tabla_consulta">
	  <!--<tr><td align="center" class="titulo_lista" colspan='2'><b><?=strtoupper($accion)?> ORGANIZACION</b></td></tr>-->
		<tr>
			<td>
				<table border="0" cellpadding="5" cellspacing="1" width="100%">

					<? if ($verifi == 0 && $accion == "insertar_mo"){ ?>
					<tr>
						<td colspan="2">
							<b>PASO 1: Verificación</b>
						</td>
					</tr>
					<tr>
						<td colspan="2">
							Digite el nombre o la sigla (seleccionar sigla en el combo) de la Organización que va a ingresar en el sistema y verifique que no existe
							mediante el listado que aparece debajo del campo a medida que va ingresando caracteres.
							Si necesita ingresar una Organización Sede, por favor, incluya el nombre de la sede en el título (ej. Ocha - Medellín).
						</td>
					</tr>
					<tr>
						<td>
							<table cellpadding="5">
								<tr>
									<td><img src="../images/consulta/busqueda_rapida.gif"></td>
									<td>
										<select id='case' class='select'>
											<option value='nombre'>Nombre</option>
											<option value='sigla'>Sigla</option>
										</select>&nbsp;&nbsp;
										<input type="text" id='s' name='s' class='textfield' size="30">
										&nbsp;&nbsp;<input type='radio' id="comience" name="donde" value='comience' checked>Que <b>comience</b> con
										&nbsp;<input type='radio' id="contenga" name="donde" value='contenga'>Que <b>contenga</b> a
										&nbsp;<input type="button" class="boton" value="Buscar" onclick="buscarOrgs()">
									</td>
								</tr>
								<tr>
									<td colspan="2">
										<div id='ocurrenciasOrg' class='ocurrenciasOrg' style='display:none'></div>
									</td>
								</tr>
							  </table>
						</td>
					</tr>
					<tr><td align="center"><input type="button" value="Siguiente" class="boton" onclick="location.href='<?=$_SERVER["SCRIPT_NAME"]?>?m_e=org&accion=insertar_mo&nombre='+document.getElementById('s').value"></tr>
					<? }
					else { ?>
					<table border="0" cellpadding="5" cellspacing="1" width="100%" align="center" class="tabla_input_org">
					<tr>
						<td class="titulo_lista" colspan="2">
							FICHA DE CAPTURA DE INFORMACION DE ORGANIZACIONES PRESENTES EN EL DEPARTAMENTO DEL 
							<?
							$style = "style=width:150px;";
							if ($accion == "insertar_mo"){
								?>
								<select id="id_depto" name="id_depto[]" class="select" onchange="enviar_deptos('index_mo.php?accion=insertar_mo&verifi=1');" <?=$style?>>
								<?
							}
							else if ($accion == "actualizar_mo"){
								?>
								<select id="id_depto" name="id_depto[]" class="select" onchange="enviar_deptos('index_mo.php?accion=actualizar_mo&paso_1=ok&id=<?=$id?>&depto_ch=1');" <?=$style?>>
								<?
							}
							 ?>
								<?
								//DEPTO
								//Cesar = 20 
								//Magdalena = 47
								$depto_dao->ListarCombo('combo',$id_depto,'id_depto IN (20,47)');
								?>
							</select>
						</td>
					</tr>
				</table>
			</td>
		</tr>
		<tr><td>Los campos marcados con (*) son obligatorios</td></tr>
		<tr>
			<!-- DATOS GENERALES : INICIO -->
			<td>
				<table border="0" cellpadding="5" cellspacing="1" width="100%" align="center" class="tabla_input_org">
					<!--<tr><td class="titulo_lista" colspan="6">2. DATOS GENERALES</td></tr>-->
					<tr>
						<td><b>Nombre (*)</b><br>[ <a href="index.php?accion=insertar_mo">Cambiar</a>]</td>
						<td colspan="6">
						<input type="text" name="nombre" id="nombre" class="textfield" value='<?=$org_vo->nom;?>' size="60" />
						</td>
					</tr>
					<tr>
					  <td><b>Sigla</b></td>
					  <td colspan="5">
							<!--<input type="text" name="sigla" id="sigla" class="textfield" value="<?=$org_vo->sig;?>" size="15" onkeydown="document.getElementById('ocurrenciasOrgSigla').style.display='';getDataV1('ocurrenciasOrg','ajax_data.php?object=ocurrenciasOrg&case=sigla&s='+this.value,'ocurrenciasOrgSigla')" />-->
							<input type="text" name="sigla" id="sigla" class="textfield" value="<?=$org_vo->sig;?>" size="15" />
						</td>
					  <!--<td><b>NIT</b></td>
					  <td colspan="3"><input type="text" name="nit" id="nit" class="textfield" value="<?=$org_vo->nit;?>" size="15" /></td>
						-->
					</tr>
					<tr>
						<td><b>Tipo (*)</b></td>
						<td>
						<select id="id_tipo" name="id_tipo" class="select">
						<option value=''>Seleccione alguno...</option>
						<?
						//TIPO
						$tipo_org_dao->ListarCombo('combo',$org_vo->id_tipo,'');
						?>
						</select>
						</td>
						<td><b>Sede (*)</b></td>
						<td colspan="3">							
							<select id="id_mun_sede" name="id_mun_sede" class="select">
							<option value=''>Seleccione alguno...</option>
							<?
							//MUNS
							$municipio_dao->ListarCombo('combo',$org_vo->id_mun_sede,"ID_DEPTO IN ('".$id_depto."')");
							?>
							</select>
						</td>
					</tr>
					<tr>
					  <td><b>Dirección (*)</b></td>
					  <td colspan="6"><input type="text" name="dir" id="dir" class="textfield" value="<?=$org_vo->dir;?>" size="60" /></td>
					</tr>
					<tr>
					  <td width="300"><b>Nombre del Representante</b></td>
					  <td><input type="text" name="n_rep" id="n_rep" class="textfield" size="30" value="<?=$org_vo->n_rep;?>" /></td>
					  <td><b>Cargo</b></td>
					  <td><input type="text" name="t_rep" id="t_rep" class="textfield" size="20" value="<?=$org_vo->t_rep;?>"  /></td>
					  <td><b>A&ntilde;o de fundaci&oacute;n en Colombia</b></td>
					  <td><input type="text" name="naci" id="naci" class="textfield" value="<?=$org_vo->naci;?>" size="6" onkeypress="return validarNum(event)" /></td>
					</tr>
					<tr>
					  <td><b>Email</b></td>
					  <td colspan='5'><input type="text" name="pu_email" id="pu_email" class="textfield" value="<?=$org_vo->pu_email;?>" size="60"/></td>
					</tr>
					<tr>
					  <td>
					  <b>Página Web</b></td>
					  <td colspan="6">
					  	<input type="text" name="web" id="web" class="textfield" value="<?=$org_vo->web;?>" size="60" />
						  <? if ($accion == "actualizar_mo"){ ?>
							  <a id="ver" href="<?=$org_vo->web;?>" target="_blank">&raquo;Abrir P&aacute;gina</a>&nbsp;&nbsp;&nbsp;&nbsp;
						<? } ?>
					  </td>
					</tr>
					<tr>
					  <td><b>Teléfono 1 (*)</b></td>
					  <td><input type="text" name="tel1" id="tel1" class="textfield" value="<?=$org_vo->tel1;?>" size="15" /></td>
					  <td><b>Teléfono 2</b></td>
					  <td><input type="text" name="tel2" id="tel2" class="textfield" value="<?=$org_vo->tel2;?>" size="15" /></td>
					  <td><b>Fax</b></td>
					  <td><input type="text" name="fax" id="fax" class="textfield" value="<?=$org_vo->fax;?>" size="10" /></td>
					</tr>
					<tr>
						<td><b>Sector (*)</b><br><br><font class="nota">(Seleccione el o los temas en los que trabaja la Organizaci&oacute;n)</font></td>
						<td colspan="5">
							<table border="0" width="100%">
								<tr>
									<?
									$s=0;
									foreach ($sectores as $vo){
										if(fmod($s,10) == 0)	echo "<td valign='top' align='left'>";
										
										$chk = (in_array($vo->id,$org_vo->id_sectores)) ? 'checked' : '';
										
										echo "<input type='checkbox' id='id_sectores' name='id_sectores[]' value='$vo->id' $chk>&nbsp;$vo->nombre_es<br>";
										
										$s++;
									}
									?>
								  </tr>
							</table>
						</td>
					</tr>
					<tr>
						<td><b>Poblaci&oacute;n Beneficiaria (*)</b><br><br><font class="nota">(Seleccione el o los tipos de población en los que su Organización focaliza sus actividades)</font></td>
						<td colspan="5">
							<table width="100%">
								<tr>
									<?
									$s=0;
									foreach ($poblaciones as $vo){
										if(fmod($s,10) == 0)	echo "<td valign='top' align='left'>";
										
										$chk = (in_array($vo->id,$org_vo->id_poblaciones)) ? 'checked' : '';
										
										echo "<input type='checkbox' id='id_poblaciones' name='id_poblaciones[]' value='$vo->id' $chk>&nbsp;$vo->nombre_es<br>";
										
										$s++;
										
									}
									?>
								  </tr>
							</table>
						</td>
					</tr>
					<tr>
						<td><b>Cobertura Geogr&aacute;fica por Municipio (*)</b><br><br><font class="nota">(Selección el o los municipios en los que la Organización tiene presencia)</font></td>
						<td colspan="5">
							<table width="100%">
								<input type='hidden' name="id_depto[]" value="<?=$id_depto?>">
								<tr>
									<?
									$muns = $municipio_dao->GetAllArray('id_depto='.$id_depto,'','');
									foreach ($muns as $s=>$vo){
										if(fmod($s,10) == 0)	echo "<td valign='top' align='left'>";
										
										$chk = (in_array($vo->id,$org_vo->id_muns)) ? 'checked' : '';
										
										echo "<input type='checkbox' id='id_muns' name='id_muns[]' value='$vo->id' $chk>&nbsp;$vo->nombre<br>";
									}
									?>
								  </tr>
							</table>
						</td>
					</tr>
					<tr>
						<td><b>Cobertura Geogr&aacute;fica por Centro Poblado</b><br><br><font class="nota">(Selección el o los centro poblados en los que la Organización tiene presencia)</font></td>
						<td colspan="5">
							<table width="100%">
								<tr>
									<?
									$poblados = $poblado_dao->GetAllArray("id_pob LIKE '$id_depto%' AND id_pob IN (20001000,20001001,20001002,20001003,20001005,20001006,20001007,20001009,20001010,20001011,20001012,20001013,20001014,20001018,20001022,20001024,20001025,20001026,20001027,20001031,20001032,20001034,20001036,20001037,20001039,20001040,20001041,20001042,20001043,47001000,47001001,47001002,47001006,47001009,47001010,47001011,47001013,47001022,47001023,47001024,47001025,47001026,47001027,47001028,47001029,47001031,47001032,47001033,47001034,47001035,47001036,47001037,47001038)",'','');
									foreach ($poblados as $s=>$vo){
										if(fmod($s,10) == 0)	echo "<td valign='top' align='left'>";
										
										$chk = (in_array($vo->id,$org_vo->id_poblados)) ? 'checked' : '';
										
										echo "<input type='checkbox' id='id_poblados' name='id_poblados[]' value='$vo->id' $chk>&nbsp;$vo->nombre<br>";
									}
									?>
								  </tr>
							</table>
						</td>
					</tr>
					<tr>
					  <td><b>Organización a la que pertenece<br><br><font class="nota">(Si es una sucursal, seleccione aqu&iacute; la sede principal)</font></b></td>
					  <td colspan="6">
						<input type="hidden" id="id_papa" name="id_papa" value="<?=$org_vo->id_papa;?>" />
						<select id="id_papa" name="id_papa" class="select" />
							<option value=''>Seleccione alguna...</option>
							<? $org_dao->ListarCombo('combo',$org_vo->id_papa,'id_org_papa=0'); ?>
						</select>

						</td>
					</tr>						  
					<tr>	
					  <td><b>Participa en algún espacio de coordinación?</b></td>
					  <td>
						<input type="radio" name="esp" <? if ($org_vo->esp_coor != "")  echo " checked "; ?>>&nbsp;Si&nbsp;<input type="radio" name="esp" <? if ($org_vo->esp_coor == "")  echo " checked "; ?>>&nbsp;No
					  </td>
					  <td colspan="4"><b>Si, Cual?</b><br><textarea id="esp_coor" name="esp_coor" cols="50" rows="5" class="area"><?=$org_vo->esp_coor?></textarea></td>
					</tr>						
					<tr>
						<td><b>¿Con cu&aacute;les otras organizaciones trabaja?</b></td>
						<td colspan="5" align='left'>
							<table cellpadding="3">
								<?
								for ($i=0;$i<5;$i++){
									$value_nombre = (isset($org_vo->org_trabaja_nombre[$i])) ? $org_vo->org_trabaja_nombre[$i] : '';
									$value_email = (isset($org_vo->org_trabaja_email[$i])) ? $org_vo->org_trabaja_email[$i] : '';
									$value_tel = (isset($org_vo->org_trabaja_tel[$i])) ? $org_vo->org_trabaja_tel[$i] : '';
									
									echo "<tr>
											<td>Nombre&nbsp;<input type='text' name='org_trabaja_nombre[]' class='textfield' value='$value_nombre' size='15'>
											<td>Email&nbsp;<input type='text' name='org_trabaja_email[]' class='textfield' value='$value_email' size='20'>
											<td>Tel&nbsp;<input type='text' name='org_trabaja_tel[]' class='textfield' value='$value_tel' size='20'>
										</tr>";
								}
								?>
							</table>
						</td>
					</tr>
					<tr>
						<td><b>¿Cu&aacute;les otras organizaciones conoce?</b></td>
						<td colspan="5" align='left'>
							<table cellpadding="3">
								<?
								for ($i=0;$i<5;$i++){
									$value_nombre = (isset($org_vo->org_conoce_nombre[$i])) ? $org_vo->org_conoce_nombre[$i] : '';
									$value_email = (isset($org_vo->org_conoce_email[$i])) ? $org_vo->org_conoce_email[$i] : '';
									$value_tel = (isset($org_vo->org_conoce_tel[$i])) ? $org_vo->org_conoce_tel[$i] : '';

									echo "<tr>
											<td>Nombre&nbsp;<input type='text' name='org_conoce_nombre[]' class='textfield' value='$value_nombre' size='15'>
											<td>Email&nbsp;<input type='text' name='org_conoce_email[]' class='textfield' value='$value_email' size='20'>
											<td>Tel&nbsp;<input type='text' name='org_conoce_tel[]' class='textfield' value='$value_tel' size='20'>
											
										</tr>";
								}
								?>
							</table>
						</td>
					</tr>
				</table>
			</td>
			<!-- DATOS GENERALES : FIN -->
		</tr>
		<tr>
			<td align='center'>
			  <input type="hidden" name="id" value="<? echo $id; ?>">
			  <input type="hidden" name="accion" value="<? echo $accion; ?>">
			  <input type="submit" name="submit" value="Aceptar" class="boton" onclick="return validar();">
			</td>
		</tr>
		<?
		}
		?>
</table>
</form>
