<?
//INICIALIZACION DE VARIABLES
$depto_dao = New DeptoDAO();
$municipio_vo = New Municipio();
$municipio_dao = New MunicipioDAO();

if (isset($_GET["accion"])){
	$accion = $_GET["accion"];
}
else if (isset($_POST["accion"])){
	$accion = $_POST["accion"];
}

////CLASS
if (isset($_POST["class"])){
  $class = $_POST['class'];
}
else if (isset($_GET["class"])){
  $class = $_GET['class'];
}
	
////METHOD
if (isset($_POST["method"])){
  $method = $_POST['method'];
}
else if (isset($_GET["method"])){
  $method = $_GET['method'];
}
	
////PARAM
if (isset($_POST["param"])){
  $param = $_POST['param'];
}
else if (isset($_GET["method"])){
  $param = $_GET['param'];
}
$id_depto = Array();
$num_deptos = 0;
if (isset($_GET['id_depto'])){
	$id_depto_s = split(",",$_GET['id_depto']);
	$id_depto = $id_depto_s[0];
	$num_deptos = count($id_depto_s);
	$id_depto = $id_depto_s;
	$id_depto_url = $id_depto;
}

if ($accion == "listar"){
	?>
    <table align='center' cellspacing="1" cellpadding="3" border="0">
    	<tr><td align='center' colspan='2'><b>PROYECTOS</b></td></tr>
    	<tr><td>&nbsp;</td></tr>
    	<tr>
    		<!-- UBICACION -->
    		<td width="500" valign="top">
    		    <form action="index.php" method="POST">
    			<table align='center' cellspacing="1" cellpadding="0" border="0">
					<tr>
						<!-- DEPARTAMENTO - MUNICIPIO : INICIO -->
						<td align="center">
							<table cellspacing="1" cellpadding="5" class="tabla_consulta" width="400">
								<tr><td align='center' class='titulo_lista'>Consulta por cobertura geográfica</td></tr>
								<tr><td><b>Seleccione el Departamento</b></td></tr>
								<tr>
									<td>
										<select id="id_depto" name="id_depto[]"  multiple size="8" class="select">
											<?
											//DEPTO
											$depto_dao->ListarCombo('combo',$id_depto,'');
											?>
										</select>&nbsp;[ Use la tecla Ctrl para sel. varios ]
									</td>
								</tr>
								<? if (!isset($_GET['id_depto'])){ ?>								
								<tr><td>&nbsp;</td></tr>
								<tr>
								  <td align='center'>
									  <input type="hidden" name="class" value="<?=$_GET['class']?>" />
									  <input type="hidden" name="method" value="<?=$_GET['method']?>" />
									  <input type="hidden" name="param" value="<?=$_GET['param']?>" />
									  <input type="hidden" name="accion" value="<?=$accion?>" />
									  <input type="hidden" name="id_muns[]" value="0" />
									  <input type="submit" name="consultar" value="Consultar Proyectos" class="boton" onclick="return validarComboMultiple(document.getElementById('id_depto'));" />	
								  </td>
								</tr>
								<tr><td>&nbsp;</td></tr>
								<tr><td><img src="../images/flecha.gif">&nbsp;<a href="#" onclick="enviar_deptos('index.php?accion=listar&class=ProyectoDAO&method=Listar&param=');return false;">Listar Muncipios del Depto. para refinar la consulta</td></tr>

								<? } ?>

								<? if (isset($_GET['id_depto']) || $accion == "actualizar"){ ?>
								<tr><td align='center'>&nbsp;</td></tr>
								<tr><td><b>Seleccione el Municipio</b></td></tr>
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
						</td>
						<!-- DEPARTAMENTO - MUNICIPIO : FIN -->
					</tr>
					<? if (isset($_GET['id_depto'])){ ?>				
						<tr><td>&nbsp;</td></tr>
						<tr><td align='center'>
							<input type="hidden" name="class" value="<?=$class;?>" />
							<input type="hidden" name="method" value="<?=$method;?>" />
							<input type="hidden" name="param" value="<?=$param;?>" />
							<input type="hidden" name="accion" value="<?=$accion?>" />
							<input type="submit" name="consultar" value="Consultar Proyectos" class="boton" onclick="return validarComboMultiple(document.getElementById('id_muns'));" />	
						</td></tr>					
					<? } ?>    			
    			</table>
    			</form>
    		</td>
    		<!-- BUSCAR -->
    		<? if (!isset($_GET['id_depto'])){ ?>
    		<td valign='top' width="400">
    		    <form action="index.php" method="POST">
    			<table align='center' cellspacing="1" cellpadding="0" border="0" >
					<tr>
						<td align="center" width="50%">
							<table width="99%" cellspacing="1" cellpadding="5" border="0" class="tabla_consulta">
								<tr><td align="center" colspan="2" class='titulo_lista'><b>Busqueda por palabra</b></td></tr>							
								<tr><td align='center' colspan="2">&nbsp;</td></tr> 
								<tr>
									<td align='right'>Palabra</td>
									<td><input type='text' id='criterio' name='criterio' class='textfield' /></td>
								</tr>
								<tr>
									<td align='right'>Buscar en</td>
									<td>
										<select id='criterio_col' name='criterio_col' class='select'>
										  <option value='NOM_PROY'>Nombre</option>
										  <option value='ID_ESTP'>Estado</option>
										</select>
									</td>
								</tr>
								<tr><td>&nbsp;</td></tr>
								<tr><td>&nbsp;</td></tr>
								<tr><td>&nbsp;</td></tr>
								<tr><td align='center' colspan="2">
										<input type="hidden" name="class" value="<?=$class;?>" />
										<input type="hidden" name="method" value="<?=$method;?>" />
										<input type="hidden" name="param" value="<?=$param;?>" />
										<input type="hidden" name="accion" value="<?=$accion?>" />
										<input type="submit" name="buscar" value="Buscar Proyectos" class="boton" onclick="return validar_forma('criterio,Palabra','')" />	
								</td></tr>	
								<tr><td>&nbsp;</td></tr>
								<tr><td>&nbsp;</td></tr>
							</table>
						</td>
					</tr>
    			</table>
    			</form>
    		</td>
		<? } ?>
		</tr>
    	<tr>
    		<!-- CONSULTA POR FECHAS -->
    		<? if (!isset($_GET['id_depto'])){ 
			  
			$calendar = new DHTML_Calendar('js/calendar/','es', 'calendar-win2k-1', false);
			$calendar->load_files();  
			?>
    		<td valign='top' width="500">
    		    <form action="index.php" method="POST">
    			<table align='center' cellspacing="1" cellpadding="0" border="0" >
					<tr>
						<td align="center" width="50%">
							<table width="400" cellspacing="1" cellpadding="5" border="0" class="tabla_consulta">
								<tr><td align="center" colspan="2" class='titulo_lista'><b>Busqueda por fecha del proyecto</b></td></tr>							
								<tr><td align='center' colspan="2">&nbsp;</td></tr> 
								<tr>
								  <td>
								  	Fecha de inicio
								  </td>
								  <td>
									<? $calendar->make_input_field(
					               // calendar options go here; see the documentation and/or calendar-setup.js
					               array('firstDay'       => 1, // show Monday first
					                     'ifFormat'       => '%Y-%m-%d',
					                     'timeFormat'     => '12'),
					               // field attributes go here
					               array('class'       => 'textfield',
					                     'name'        => 'f_ini')); 
															 
					      			?>
					      		  </td>
					      		</tr>
					      		<tr>
					      		  <td>
					  				Fecha de finalización
					  			  </td>
					  			  <td>
					  				<? $calendar->make_input_field(
					                 // calendar options go here; see the documentation and/or calendar-setup.js
					                 array('firstDay'       => 1, // show Monday first
					                       'ifFormat'       => '%Y-%m-%d',
					                       'timeFormat'     => '12'),
					                 // field attributes go here
					                 array('class'       => 'textfield',
					                       'name'        => 'f_fin'));
					    			?>
								</td></tr>
								<tr><td align='center' colspan="2">
										<input type="hidden" name="class" value="<?=$class;?>" />
										<input type="hidden" name="method" value="<?=$method;?>" />
										<input type="hidden" name="param" value="<?=$param;?>" />
										<input type="hidden" name="accion" value="<?=$accion?>" />
										<input type="submit" name="buscar" value="Consulta Proyectos" class="boton" onclick="return validar_forma('f-calendar-field-1,Fecha de inicio,f-calendar-field-2,Fecha de finalización','')" />	
								</td></tr>	
								<tr><td>&nbsp;</td></tr>
								<tr><td>&nbsp;</td></tr>
							</table>
						</td>
					</tr>
    			</table>
    			</form>
    		</td>
    		<?}?>
    	</tr>
	</table>
</form>
<?
}
?>	
