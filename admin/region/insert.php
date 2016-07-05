<?
//INICIALIZACION DE VARIABLES
$region_dao = New RegionDAO();
$region_vo = New Region();
$depto_dao = New DeptoDAO();
$mun_dao = New MunicipioDAO();
$boton_sig = "Siguiente";

if (isset($_GET["accion"])){
	$accion = $_GET["accion"];
}
else if (isset($_POST["accion"])){
	$accion = $_POST["accion"];
}


$id_depto = -1;
if (isset($_GET['id_depto'])){
    $id_depto_s = $_GET['id_depto'];
	$id_depto = split(",",$_GET['id_depto']);
    $num_deptos = count($id_depto);
}

$readonly = "";
//Caso de Actualizacion
if (isset($_GET["accion"]) && $_GET["accion"] == "actualizar"){
	$id = $_GET["id"];
  	$region_vo = $region_dao->Get($id);
  	$readonly = " readonly ";
  	$boton_sig = "Refrescar Municipios";
	
	if (!isset($_GET['id_depto'])){
		$id_depto = $region_vo->id_deptos;
	}
	
	$num_deptos = count($id_depto);
}
?>

<form method="POST" onsubmit="submitForm(event);return false;">
	<table class="tabla_insertar">
				<tr>
					<td valign="top">
						Seleccione los departamentos<br /><br />
						<select id="id_depto" name="id_depto[]" multiple size="18" class="select">
							<?
							//DEPTO
							$depto_dao->ListarCombo('combo',$id_depto,"id_depto != '00'");
							?>
						</select>
						<br /><br />
						<? if (!isset($_GET["id_depto"])){ ?> 
								<input type="button" value="<?=$boton_sig;?>" class="boton" onclick="enviar_deptos_ajax('index_parser.php?accion=<?=$accion?>&class=RegionDAO&method=Listar&param=&id=<?=$region_vo->id?>');return false;"><br>&nbsp;
						<? } ?>
					</td>
				<? if (isset($_GET['id_depto']) || $accion == "actualizar"){ ?>
					<td valign="top">
						Seleccione los municipios<br /><br />
						<select id="id_mun" name="id_muns[]" multiple size="18" class="select">
							<?
							//MUNICIPIO
							foreach($id_depto as $id){
								$depto = $depto_dao->Get($id);
								$muns = $mun_dao->GetAllArray('ID_DEPTO ='.$id);
								echo "<option value='' disabled>-------- ".$depto->nombre." --------</option>";
								foreach ($muns as $mun){
								  echo "<option value='".$mun->id."'";
								  if (in_array($mun->id,$region_vo->id_muns))	echo " selected ";
								  echo ">".$mun->nombre."</option>";
								}
								
							} 
							?>
						</select>
					</td>
				</tr>
				
			<tr><td align="center" colspan="2">Nombre de la Regi&oacute;n&nbsp;<input type="text" id="nombre" name="nombre" size="40" value="<?=$region_vo->nombre;?>" class="textfield" /></td></tr>
			<tr>
			  <td colspan="2" align='center'>
				  <br>
					<input type="hidden" name="id" value="<? echo $region_vo->id; ?>">
					<input type="hidden" name="accion" value="<?=$accion?>" />
					<input type="submit" name="submit" value="Aceptar" class="boton" onclick="return validar_forma('id_mun,Municipio (s),nombre,Nombre','');" />
				</td>
			</tr>
		<?
		}
		?>
	</table>
</form>	
