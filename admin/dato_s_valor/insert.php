<?
//LIBRERIAS
include_once ('js/calendar/calendar.php');

//INICIALIZACION DE VARIABLES
$dato_vo = New DatoSectorial();
$dato_dao = New DatoSectorialDAO();
$u_d_s_dao = New UnidadDatoSectorDAO();
$cat_d_s_dao = New CategoriaDatoSectorDAO();
$sector_dao = New SectorDAO();
$contacto_dao = New ContactoDAO();
$depto_dao = New DeptoDAO();
$mun_dao = New MunicipioDAO();
$pob_dao = New PobladoDAO();
$calendar = new DHTML_Calendar('js/calendar/','es', 'calendar-win2k-1', false);
$calendar->load_files();

$accion = "";
if (isset($_GET["accion"])){
	$accion = $_GET["accion"];
}
else if (isset($_POST["accion"])){
	$accion = $_POST["accion"];
}

$id_dato = 0;
if (isset($_GET["id_dato"])){
  $id_dato = $_GET["id_dato"];
}

$dato_para = 0;
$nom_para = Array("Departamentos","Municipios","Poblados");
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
$id_pob = Array();
if (isset($_GET['id_pob'])){
	$id_pob_s = split(",",$_GET['id_pob']);
	$id_pob = $id_pob_s;
	$id_pob_url = $_GET['id_pob'];
}
if (isset($_GET['id_pob_back'])){
	$id_pob_s = split(",",$_GET['id_pob_back']);
	$id_pob = $id_pob_s;
	$id_pob_url = $_GET['id_pob_back'];
}

$titulo = "Insertar";
if ($accion == "actualizarDatoValor"){
    $titulo = "Actualizar";
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

	location.href = 'index.php?accion=<?=$accion?>&id_dato=<?=$id_dato;?>&dato_para='+valor;
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

function enviar_pob(url_href,combo){
  selected = new Array();
	ob = combo;
	for (var i = 0; i < ob.options.length; i++){
	  if (ob.options[ i ].selected)
		  selected.push(ob.options[ i ].value);
	}
	var url = selected.join(",");

	if (selected.length == 0){
	  alert("Debe seleccionar algún Poblado");
	}
	else{
  	location.href = url_href+'&id_pob='+url;
	}
}

</script>
<form name="form1" action="<?=$_SERVER['PHP_SELF']?>" method="POST">
  <table border="0" cellpadding="3" cellspacing="1" width="70%" align="center">
	  <tr><td align="center"><b><?=$titulo;?> Dato Sectorial</b></td></tr>
	</table>
	<br>
	<table border="0" cellpadding="5" cellspacing="1" width="70%" align="center">
		<tr><td align="right">Dato</td>
			<td><select name="id_dato" class="select" onchange="location.href='index.php?accion=<?=$accion?>&id_dato='+this.value"><option value=''>Seleccione alguno...</option><? $dato_dao->ListarCombo('combo',$id_dato,''); ?></select></td>
		</tr>
		<?
		if (isset($_GET["id_dato"])){
		    ?>
		    <tr>
				<td align="right">Dato Sectorial para:</td>
				<?
				if (!isset($_GET["dato_para"])){ ?>
					<td><input type="radio" id="dato_para_1" name="dato_para" value="1" <?=$chk_para[0]?>>&nbsp;Departamentos&nbsp;<input type="radio" id="dato_para_2" name="dato_para" value="2" <?=$chk_para[1]?>>&nbsp;Municipios&nbsp;<input type="radio" id="dato_para_3" name="dato_para" value="3" <?=$chk_para[2]?>>&nbsp;Poblados&nbsp;</td>
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
		}
		if (isset($_GET["dato_para"]) && !isset($_GET["id_depto"])){ ?>
		    <tr><td align="right">&nbsp;</td>
				<?
				if ($dato_para == 1){
					echo "<td>Seleccione los Departamentos<br><br><select id='id_depto' name='id_depto[]' multiple size='10' class='select'>";
					$depto_dao->ListarCombo('combo',$id_depto,'');
					echo "</select>&nbsp;<a href='#' onclick='selectAll(document.getElementById('id_depto'))'>Seleccionar todos</a>";
				}
				else{
				   echo "<td>Seleccione el Departamento<br><br><select id='id_depto' name='id_depto[]' class='select'>";
				   $depto_dao->ListarCombo('combo',$id_depto,'');
				   echo "</select>";
				}
				?>

					<?  ?>

				</td>
			</tr>
			<tr>
				<td colspan="2" align="center">
					<input type="button" value="Atras" onclick="location.href = 'index.php?accion=<?=$accion?>&id_dato=<?=$id_dato;?>&dato_para_back=<?=$dato_para;?>';" class="boton">
					<input type="button" value="Siguiente" onclick="enviar_deptos('index.php?accion=<?=$accion?>&id_dato=<?=$id_dato;?>&dato_para=<?=$dato_para;?>');" class="boton">
				</td>
			</tr>
		    <?
		}
		if (isset($_GET["id_depto"])){
			//Depto
			if ($dato_para == 1){

		        echo "<tr><td align='right'>&nbsp;</td></tr>";
		        echo "<tr><td align='center' colspan='2'><b>Valor del Dato Sectorial en:</b></td></tr>";

			    foreach ($id_depto as $id_d){
			        $vo = $depto_dao->Get($id_d);
					//Caso de Actualizacion
					if (isset($_GET["accion"]) && $_GET["accion"] == "actualizarDatoValor"){
						  $dato_vo = $dato_dao->GetValor("ID_DEPTO = '".$id_d."' AND ID_DATO = ".$id_dato,$dato_para);
					}
			        echo "<tr><td align='right'>".$vo->nombre."</td>";
					echo "<td><input type='text' name='valor_dato[]' class='textfield' value='".$dato_vo->valor."'></td>";
				}
			}
			//Mun
			else if ($dato_para == 2){

			  	if (!isset($_GET["id_mun"])){
			    ?>
			    <tr>
					<td align="center" colspan="2">
						<table width="70%" border=0>
								<?
								$d = 0;
								foreach ($id_depto as $id_d){
							        $depto = $depto_dao->Get($id_d);

									if (fmod($d,2) == 0)	echo "<tr><td>&nbsp;</td></tr><tr>";
									?>
										<td>Municipios de <?=$depto->nombre;?><br><br>
										<select id="id_mun" name="id_mun[]" multiple size="10" class="select">
											<? $mun_dao->ListarCombo('combo',$id_mun,'ID_DEPTO = '.$id_depto[0]); ?>
										</select>&nbsp;<a href="#" onclick="selectAll(document.getElementById('id_mun'))">Todos</a>
										</td>
									<?
									$d++;
								}
								?>
						</table>
				</tr>
				<tr>
					<td colspan="2" align="center">
						<input type="button" value="Atras" onclick="location.href = 'index.php?accion=<?=$accion?>&id_dato=<?=$id_dato;?>&dato_para=<?=$dato_para;?>&id_depto_back=<?=$id_depto_url;?>';" class="boton">
						<input type="button" value="Siguiente" onclick="enviar_mun('index.php?accion=<?=$accion?>&id_dato=<?=$id_dato;?>&dato_para=<?=$dato_para;?>&id_depto=<?=$id_depto_url;?>',document.getElementById('id_mun'));" class="boton">
					</td>
				</tr>
				<?
				}
			}
			//Poblado
			else if ($dato_para == 3){

			  	if (!isset($_GET["id_pob"])){
			    ?>
			    <tr>
					<td align="center" colspan="2">
						<table width="70%" border=0>
								<?
								$d = 0;
								foreach ($id_depto as $id_d){
							        $depto = $depto_dao->Get($id_d);

									if (fmod($d,2) == 0)	echo "<tr><td>&nbsp;</td></tr><tr>";

									$id_muns_pob = $mun_dao->GetAllArrayID('ID_DEPTO = '.$id_depto[0],'');
									$p = 0;
									foreach ($id_muns_pob as $id_p){
									  $id_muns_pob[$p] = "'".$id_p."'";
									  $p++;
									}
									$id_muns_pob = implode(",",$id_muns_pob);
									?>
										<td>Poblados de <?=$depto->nombre;?><br><br>
										<select id="id_pob" name="id_pob[]" multiple size="10" class="select">
											<? $pob_dao->ListarCombo('combo',$id_pob,"ID_MUN IN (".$id_muns_pob.")"); ?>
										</select>&nbsp;<a href="#" onclick="selectAll(document.getElementById('id_pob'))">Todos</a>
										</td>
									<?
									$d++;
								}
								?>
						</table>
				</tr>
				<tr>
					<td colspan="2" align="center">
						<input type="button" value="Atras" onclick="location.href = 'index.php?accion=<?=$accion?>&id_dato=<?=$id_dato;?>&dato_para=<?=$dato_para;?>&id_depto_back=<?=$id_depto_url;?>';" class="boton">
						<input type="button" value="Siguiente" onclick="enviar_pob('index.php?accion=<?=$accion?>&id_dato=<?=$id_dato;?>&dato_para=<?=$dato_para;?>&id_depto=<?=$id_depto_url;?>',document.getElementById('id_pob'));" class="boton">
					</td>
				</tr>
				<?
				}
			}
		}
		if (isset($_GET["id_mun"])){

		        echo "<tr><td align='right'>&nbsp;</td></tr>";

		        $dato_vo = $dato_dao->GetValor("ID_MUN = '".$id_mun[0]."' AND ID_DATO = ".$id_dato,$dato_para);

		        ?>
				<tr><td align="right">Unidad</td>
					<td>
						<select name="id_unidad" class="select">
						<? $u_d_s_dao->ListarCombo('combo',$dato_vo->id_unidad,''); ?>
						</select>
					</td>
				</tr>
				<tr><td align="right">Periodo</td>
					<td>
						Desde&nbsp;
						<?
						$calendar->make_input_field(
						// calendar options go here; see the documentation and/or calendar-setup.js
						array('firstDay'       => 1, // show Monday first
						     'ifFormat'       => '%Y-%m-%d',
						     'timeFormat'     => '12'),
						// field attributes go here
						array('class'       => 'textfield',
							  'size'		=> '10',
							  'value'		=> $dato_vo->fecha_ini,
						      'name'        => 'f_ini'));

						?>&nbsp;&nbsp;
						Hasta&nbsp;
						<? $calendar->make_input_field(
						 // calendar options go here; see the documentation and/or calendar-setup.js
						 array('firstDay'       => 1, // show Monday first
						       'ifFormat'       => '%Y-%m-%d',
						       'timeFormat'     => '12'),
						 // field attributes go here
						 array('class'       => 'textfield',
						 		'size'		=> '10',
						 		'value'		=> $dato_vo->fecha_fin,
						       'name'        => 'f_fin'));
						?>
					</td>
				</tr>
				<tr><td>&nbsp;</td></tr>
		        <?
		        echo "<tr><td align='center' colspan='2'><b>Valor del Dato Sectorial en:</b></td></tr>";

			    foreach ($id_mun as $id_d){
			        $vo = $mun_dao->Get($id_d);
			        $depto = $depto_dao->Get($id_depto[0]);

					//Caso de Actualizacion
					if (isset($_GET["accion"]) && $_GET["accion"] == "actualizarDatoValor"){
						  $dato_vo = $dato_dao->GetValor("ID_MUN = '".$id_d."' AND ID_DATO = ".$id_dato,$dato_para);
					}

			        echo "<tr><td align='right'>".$vo->nombre." (".$depto->nombre.")</td>";
					echo "<td><input type='text' name='valor_dato[]' class='textfield' value='".$dato_vo->valor."'></td>";

				}
		}
		if (isset($_GET["id_pob"])){
			$dato_vo = $dato_dao->GetValor("ID_POB = '".$id_pob[0]."' AND ID_DATO = ".$id_dato,$dato_para);
				?>
				<tr><td align="right">Unidad</td>
					<td>
						<select name="id_unidad" class="select">
						<? $u_d_s_dao->ListarCombo('combo',$dato_vo->id_unidad,''); ?>
						</select>
					</td>
				</tr>
				<tr><td align="right">Periodo</td>
					<td>
						Desde&nbsp;
						<?

						$calendar->make_input_field(
						// calendar options go here; see the documentation and/or calendar-setup.js
						array('firstDay'       => 1, // show Monday first
						     'ifFormat'       => '%Y-%m-%d',
						     'timeFormat'     => '12'),
						// field attributes go here
						array('class'       => 'textfield',
							  'size'		=> '10',
							  'value'		=> $dato_vo->fecha_ini,
						      'name'        => 'f_ini'));

						?>&nbsp;&nbsp;
						Hasta&nbsp;
						<? $calendar->make_input_field(
						 // calendar options go here; see the documentation and/or calendar-setup.js
						 array('firstDay'       => 1, // show Monday first
						       'ifFormat'       => '%Y-%m-%d',
						       'timeFormat'     => '12'),
						 // field attributes go here
						 array('class'       => 'textfield',
						 		'size'		=> '10',
						 		'value'		=> $dato_vo->fecha_fin,
						       'name'        => 'f_fin'));
						?>
					</td>
				</tr>
				<?
		        echo "<tr><td align='right'>&nbsp;</td></tr>";
		        echo "<tr><td align='center' colspan='2'><b>Valor del Dato Sectorial en:</b></td></tr>";

			    foreach ($id_pob as $id_d){
			        $vo = $pob_dao->Get($id_d);
			        $depto = $depto_dao->Get($id_depto[0]);

					if (isset($_GET["accion"]) && $_GET["accion"] == "actualizarDatoValor"){
						  $dato_vo = $dato_dao->GetValor("ID_POB = '".$id_d."' AND ID_DATO = ".$id_dato,$dato_para);
					}

			        echo "<tr><td align='right'>".$vo->nombre." (".$depto->nombre.")</td>";
					echo "<td><input type='text' name='valor_dato[]' class='textfield' value='".$dato_vo->valor."'></td>";
				}
		}
		?>
		<tr>
		  <td colspan="2" align='center'>
		  	<?
			  if ($dato_para == 1 && isset($_GET["id_depto"])){ ?>
				<input type="hidden" name="accion" value="<?=$accion?>" />
				<input type="hidden" name="id" value="<?=$dato_vo->id;?>" />
				<input type="hidden" name="dato_para" value="<?=$dato_para;?>" />
				<input type='hidden' name='id_depto' value="<?=$id_depto_url;?>">
				<input type="button" value="Atras" onclick="location.href = 'index.php?accion=<?=$accion?>&id_dato=<?=$id_dato;?>&dato_para=<?=$dato_para;?>&id_depto_back=<?=$id_depto_url;?>';" class="boton">
				<input type="submit" name="submit" value="Aceptar" class="boton" onclick="return validateTextInput(document.forms[0],'Falta el valor del Dato en algún Departamento');" />
				<?
			  }
			  if ($dato_para == 2 && isset($_GET["id_mun"])){ ?>
				<input type="hidden" name="accion" value="<?=$accion?>" />
				<input type="hidden" name="id" value="<?=$dato_vo->id;?>" />
				<input type="hidden" name="dato_para" value="<?=$dato_para;?>" />
				<input type='hidden' name='id_mun' value="<?=$id_mun_url;?>">
				<input type="button" value="Atras" onclick="location.href = 'index.php?accion=<?=$accion?>&id_dato=<?=$id_dato;?>&dato_para=<?=$dato_para;?>&id_depto=<?=$id_depto_url;?>&id_mun_back=<?=$id_mun_url;?>';" class="boton">
				<input type="submit" name="submit" value="Aceptar" class="boton" onclick="return validateTextInput(document.forms[0],'Falta el valor del Dato en algún Municipio');" />
				<?
			  }
			  if ($dato_para == 3 && isset($_GET["id_pob"])){ ?>
				<input type="hidden" name="accion" value="<?=$accion?>" />
				<input type="hidden" name="id" value="<?=$dato_vo->id;?>" />
				<input type="hidden" name="dato_para" value="<?=$dato_para;?>" />
				<input type='hidden' name='id_pob' value="<?=$id_pob_url;?>">
				<input type="button" value="Atras" onclick="location.href = 'index.php?accion=<?=$accion?>&id_dato=<?=$id_dato;?>&dato_para=<?=$dato_para;?>&id_depto=<?=$id_depto_url;?>&id_pob_back=<?=$id_pob_url;?>';" class="boton">
				<input type="submit" name="submit" value="Aceptar" class="boton" onclick="return validateTextInput(document.forms[0],'Falta el valor del Dato en algún Poblado');" />
				<?
			  }
			  ?>
			</td>
		</tr>
	</table>
</form>