<?
//INICIALIZACION DE VARIABLES
$despla = New Desplazamiento();
$despla_dao = New DesplazamientoDAO();
$tipo_dao = New TipoDesplazamientoDAO();
$clase_dao = New ClaseDesplazamientoDAO();
$periodo_dao = New PeriodoDAO();
$contacto_dao = New ContactoDAO();
$fuente_dao = New FuenteDAO();
$poblacion_dao = New PoblacionDAO();
$depto_dao = New DeptoDAO();
$mun_dao = New MunicipioDAO();

$accion = "";
if (isset($_GET["accion"])){
	$accion = $_GET["accion"];
}
else if (isset($_POST["accion"])){
	$accion = $_POST["accion"];
}

$dato_para = 0;
$nom_para = Array("Departamentos","Municipios");
$chk_para = Array (" checked ","",""); 
if (isset($_GET["dato_para"])){
  $dato_para = $_GET["dato_para"];
}
if (isset($_GET["dato_para_back"])){
  $dato_para = $_GET["dato_para_back"];
  $chk_para[$dato_para - 1] = " checked ";
}


$num_deptos = 0;
$id_depto = Array();
if (isset($_GET['id_depto'])){
	$id_depto_s = split(",",$_GET['id_depto']);
	$num_deptos = count($id_depto_s);
	$id_depto = $id_depto_s;
	$id_depto_url = $_GET['id_depto'];
}
if (isset($_GET['id_depto_back'])){
	$id_depto_s = split(",",$_GET['id_depto_back']);
	$num_deptos = count($id_depto_s);
	$id_depto = $id_depto_s;
	$id_depto_url = $_GET['id_depto_back'];
}
$id_mun = Array();
if (isset($_GET['id_mun'])){
	$id_mun_s = split(",",$_GET['id_mun']);
	$id_mun = $id_mun_s;
	$id_mun_url = $_GET['id_mun'];
}
if (isset($_GET['id_mun_back'])){
	$id_mun_s = split(",",$_GET['id_mun_back']);
	$id_mun = $id_mun_s;
	$id_mun_url = $_GET['id_mun_back'];
}

$titulo = "Insertar";
if ($accion == "actualizar"){
    $titulo = "Actualizar";
    
    $id = $_GET["id"];
    $despla = $despla_dao->Get($id);
    if ($_GET["dato_para"] == 1){
	    $id_depto_exp = $despla->id_depto_exp;
	    $id_depto_rec = $despla->id_depto_rec;
	}
    else if ($_GET["dato_para"] == 2){
        if ($despla->id_mun_exp != ""){
	      	$mun = $mun_dao->Get($despla->id_mun_exp);
		    $id_depto_exp = $mun->id_depto;
		}
		else{
		   $id_depto_exp = ""; 
		}

        if ($despla->id_mun_rec != ""){
	      	$mun = $mun_dao->Get($despla->id_mun_rec);
		    $id_depto_rec = $mun->id_depto;
		}
		else{
		   $id_depto_rec = ""; 
		}
	}
	
}
?>
<script>
function enviar_para(){
	var valor;
	
	if (document.getElementById('dato_para_1').checked == true){
	    valor = 1;
	}
	else if (document.getElementById('dato_para_2').checked == true){
	    valor = 2;
	}
	else {
	    valor = 3;
	}
	
	location.href = 'index.php?accion=<?=$accion?>&dato_para='+valor;
}

function enviar_depto_e_r(){
  	selected = new Array();
	ob = document.getElementById('id_depto_exp'); 
	for (var i = 0; i < ob.options.length; i++){ 
	  if (ob.options[ i ].selected) 
		  selected.push(ob.options[ i ].value);
	}
	var url_exp = selected.join(",");
	
  	selected = new Array();
	ob = document.getElementById('id_depto_rec'); 
	for (var i = 0; i < ob.options.length; i++){ 
	  if (ob.options[ i ].selected) 
		  selected.push(ob.options[ i ].value);
	}
	var url_rec = selected.join(",");
	
	location.href = "index.php?accion=<?=$accion?>&dato_para=<?=$dato_para?>&id_depto_exp="+url_exp+"&id_depto_rec="+url_rec;
}

function enviar_mun(url_href,combo){
  selected = new Array();
	ob = combo; 
	for (var i = 0; i < ob.options.length; i++){ 
	  if (ob.options[ i ].selected) 
		  selected.push(ob.options[ i ].value);
	}
	var url = selected.join(",");
	
	if (selected.length == 0){
	  alert("Debe seleccionar algún Municipio");
	}
	else{
  	location.href = url_href+'&id_mun='+url;
	}  
}

</script>
<form name="form1" action="<?=$_SERVER['PHP_SELF']?>" method="POST">
	<table border="0" cellpadding="5" cellspacing="1" width="700" align="center" class="tabla_consulta">
		<tr><td align="center" colspan="2" class="titulo_lista"><b><?=$titulo;?> Dato de Desplazamiento</b></td></tr>
		<tr><td>&nbsp;</td></tr>
	    <tr>
			<td align="right">Dato de Desplazamiento en:</td>
			<?
			if (!isset($_GET["dato_para"])){ ?>
				<td><input type="radio" id="dato_para_1" name="dato_para" value="1" <?=$chk_para[0]?>>&nbsp;Departamentos&nbsp;<input type="radio" id="dato_para_2" name="dato_para" value="2" <?=$chk_para[1]?>>&nbsp;Municipios&nbsp;</td>
				<tr><td colspan="2" align="center"><input type="button" value="Siguiente" onclick="enviar_para();" class="boton"></td></tr>
			<? 
			}
			else{ ?>
				<td><b><?=$nom_para[$dato_para - 1]?></b></td>
				<?
			}					 
		?>
		</tr>
	    <?
		if (isset($_GET["dato_para"])){ ?>
			<tr><td>&nbsp;</td></tr>
				<?
				if ($dato_para == 1){
					echo "<tr><td align='right'>Departamento <b>Expulsor</b></td><td><select id='id_depto_exp' name='id_depto_exp' class='select'>";  
					echo "<option value=''>No aplica</option>";
					$depto_dao->ListarCombo('combo',$despla->id_depto_exp,'');
					echo "</select></td></tr>";

					echo "<tr><td align='right'>Departamento <b>Receptor</b></td><td><select id='id_depto_rec' name='id_depto_rec' class='select'>";  
					echo "<option value=''>No aplica</option>";
					$depto_dao->ListarCombo('combo',$despla->id_depto_rec,'');
					echo "</select></td></tr>";

					?>
					<tr><td align="right" width="50%">Tipo de Desplazamiento</td>
						<td><select name="id_tipo" class="select" ><? $tipo_dao->ListarCombo('combo',$despla->id_tipo,''); ?></select></td>
					</tr>
					<tr><td align="right">Clase de Desplazamiento</td>
						<td>
							<select name="id_clase" class="select" >
								<option value=0>No aplica</option>
								<? $clase_dao->ListarCombo('combo',$despla->id_clase,''); ?>
							</select>
						</td>
					</tr>
					<tr><td align="right">Fuente</td>
						<td><select name="id_fuente" class="select" ><? $fuente_dao->ListarCombo('combo',$despla->id_fuente,''); ?></select></td>
					</tr>
					<tr><td align="right">Periodo</td>
						<td><select name="id_periodo" class="select" ><? $periodo_dao->ListarCombo('combo',$despla->id_periodo,''); ?></select></td>
					</tr>
					<tr><td align="right">Población</td>
						<td><select name="id_poblacion" class="select" style="width:400px"><? $poblacion_dao->ListarCombo('combo',$despla->id_poblacion,''); ?></select></td>
					</tr>
					<tr><td align="right">Contáctos</td>
						<td><select name="id_contacto" class="select" ><? $contacto_dao->ListarCombo('combo',$despla->id_contacto,''); ?></select></td>
					</tr>
					<tr><td align="right">Cantidad de Desplazados</td>
						<td><input type="text" id="cantidad" name="cantidad" class="textfield" size="15" value="<?=$despla->cantidad;?>"></td>
					</tr>
					<tr><td>&nbsp;</td></tr>
					<tr><td colspan="2" align="center">
						<input type="hidden" name="accion" value="<?=$accion?>" />
						<input type="hidden" name="id" value="<?=$despla->id;?>" />
						<input type="hidden" name="dato_para" value="<?=$dato_para;?>" />
						<input type="button" value="Atras" onclick="location.href = 'index.php?accion=<?=$accion?>';" class="boton">
						<input type="submit" name="submit" value="Aceptar" class="boton" onclick="return validar_forma('cantidad,Cantidad de Desplazados','');" />
					</td></tr>
					<?

				}
				else if (!isset($_GET["id_depto_exp"]) && $accion == "insertar"){
					echo "<tr><td align='right'>Departamento <b>Expulsor</b></td><td><select id='id_depto_exp' name='id_depto_exp' class='select'>";  
					echo "<option value=''>No aplica</option>";
					$depto_dao->ListarCombo('combo',$despla_vo->id_depto_exp,'');
					echo "</select></td></tr>";

					echo "<tr><td align='right'>Departamento <b>Receptor</b></td><td><select id='id_depto_rec' name='id_depto_rec' class='select'>";  
					echo "<option value=''>No aplica</option>";
					$depto_dao->ListarCombo('combo',$despla_vo->id_depto_rec,'');
					echo "</select></td></tr>";

					?>
					<tr><td>&nbsp;</td></tr>
					<tr>
						<td colspan="2" align="center">
							<input type="button" value="Atras" onclick="location.href = 'index.php?accion=<?=$accion?>&dato_para_back=<?=$dato_para;?>';" class="boton">
							<input type="button" value="Siguiente"  class="boton" onclick="enviar_depto_e_r();">
						</td>
					</tr>
					<?
				}
				?>
				</td>
			</tr>
		    <?
		}
		if (isset($_GET["id_depto_exp"]) || $accion == "actualizar"){

			if ($dato_para == 2){		  
			    if ($accion == "insertar"){
					$id_depto_exp = $_GET["id_depto_exp"];
				    $id_depto_rec = $_GET["id_depto_rec"];
				}
	
				echo "<tr><td align='right'>Municipio <b>Expulsor</b></td>";
				echo "<td><select id='id_mun_exp' name='id_mun_exp' class='select'>";
				if ($id_depto_exp != ""){
					$mun_dao->ListarCombo('combo',$despla->id_mun_exp,'ID_DEPTO = '.$id_depto_exp);
				}
				else{
				    echo "<option value=''>No aplica</option>";
				}
				echo "</select></td></tr>";
				
				echo "<tr><td align='right'>Municipio <b>Receptor</b></td><td><select id='id_mun_rec' name='id_mun_rec' class='select'>";  
				if ($id_depto_rec != ""){
					$mun_dao->ListarCombo('combo',$despla->id_mun_rec,'ID_DEPTO = '.$id_depto_rec);
				}
				else{
				    echo "<option value=''>No aplica</option>";
				}
				echo "</select></td></tr>";
				
				?>
				<tr><td align="right" width="50%">Tipo de Desplazamiento</td>
					<td><select name="id_tipo" class="select" ><? $tipo_dao->ListarCombo('combo',$despla->id_tipo,''); ?></select></td>
				</tr>
				<tr><td align="right">Clase de Desplazamiento</td>
					<td>
						<select name="id_clase" class="select" >
							<option value=0>No aplica</option>
							<? $clase_dao->ListarCombo('combo',$despla->id_clase,''); ?>
						</select>
					</td>
				</tr>
				<tr><td align="right">Fuente</td>
					<td><select name="id_fuente" class="select" ><? $fuente_dao->ListarCombo('combo',$despla->id_fuente,''); ?></select></td>
				</tr>
				<tr><td align="right">Periodo</td>
					<td><select name="id_periodo" class="select" ><? $periodo_dao->ListarCombo('combo',$despla->id_periodo,''); ?></select></td>
				</tr>
				<tr><td align="right">Población</td>
					<td><select name="id_poblacion" class="select" style="width:400px"><? $poblacion_dao->ListarCombo('combo',$despla->id_poblacion,''); ?></select></td>
				</tr>
				<tr><td align="right">Contáctos</td>
					<td><select name="id_contacto" class="select" ><? $contacto_dao->ListarCombo('combo',$despla->id_contacto,''); ?></select></td>
				</tr>
				<tr><td align="right">Cantidad de Desplazados</td>
					<td><input type="text" id="cantidad" name="cantidad" class="textfield" size="15" value="<?=$despla->cantidad;?>"></td>
				</tr>
				<tr><td>&nbsp;</td></tr>
				<tr><td colspan="2" align="center">
					<input type="hidden" name="accion" value="<?=$accion?>" />
					<input type="hidden" name="id" value="<?=$despla->id;?>" />
					<input type="hidden" name="dato_para" value="<?=$dato_para;?>" />
					<input type="button" value="Atras" onclick="location.href = 'index.php?accion=<?=$accion?>';" class="boton">
					<input type="submit" name="submit" value="Aceptar" class="boton" onclick="return validar_forma('cantidad,Cantidad de Desplazados','');" />
				</td></tr>
				<?
			}
		}
		?>
	</table>
</form>	