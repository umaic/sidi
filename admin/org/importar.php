<?
//INICIALIZACION DE VARIABLES
$depto_dao = New DeptoDAO();
$sector_dao = New SectorDAO();
$enfoque_dao = New EnfoqueDAO();
$poblacion_dao = New PoblacionDAO();

if (isset($_GET["accion"])){
	$accion = $_GET["accion"];
}
else if (isset($_POST["accion"])){
	$accion = $_POST["accion"];
}

?>

<script>
function copiarCombos(){
	CopiarOpcionesCombo(document.getElementById('id_sector'),document.getElementById('id_sector_h'));
	CopiarOpcionesCombo(document.getElementById('id_enfoque'),document.getElementById('id_enfoque_h'));
	CopiarOpcionesCombo(document.getElementById('id_poblacion'),document.getElementById('id_poblacion_h'));
}
</script>
<form action="<?=$_SERVER['PHP_SELF']?>" method="POST" enctype="multipart/form-data">
<table cellspacing="1" cellpadding="5" class="tabla_consulta" width="600" align="center">
	<tr><td align='center' class='titulo_lista'>IMPORTAR ORGANIZACIONES</td></tr>
	<tr><td>&nbsp;</td></tr>
	<tr><td>1. Seleccione el Departamento al que pertenecen las Organizaciones a importar</td></tr>
	<tr>
		<td>
			<select id="id_depto" name="id_depto" class="select">
				<option value="">Seleccione alguno...</option>
				<?
				//DEPTO
				$depto_dao->ListarCombo('combo','','');
				?>
			</select>
		</td>
	</tr>
	<tr><td>&nbsp;</td></tr>
	<tr><td>2. Seleccione el archivo CSV</td></tr>
	<tr><td><input id="archivo_csv" name="archivo_csv" type="file" class="textfield" size="60"><br><br><a href="#" onclick="window.open('org/col_csv_help.htm','','top=100,left=200,width=800,height=500')">? Ver Columnas del archivo CSV </a></td></tr>
	<tr><td>&nbsp;</td></tr>
	<tr>
		<td>
			<table class="tabla_consulta" cellspacing="1" cellpadding="5">
				<tr><td class="titulo_lista" align="center" colspan="2">SINCRONIZACION DE SECTORES</td></tr>
				<tr><td colspan="2">Ordene los sectores de acuerdo al orden establecido en el formulario word de captura de información. Si es necesario cree nuevas opciones.</td></tr>		
				<tr>
					<td align="center">
						<select id="id_sector" name="id_sector" size="12" class="select">
							<?
							//SECTOR
							$sector_dao->ListarCombo('combo','','');
							?>
						</select>					
					</td>
					<td align="left">
					<input type="button" value="Mover Arriba" class="boton" onclick="moveOption(document.getElementById('id_sector'),'up','')"><br><br>
					<input type="button" value="Mover Abajo" class="boton" onclick="moveOption(document.getElementById('id_sector'),'down','')"><br><br>
					<input type="button" value="Crear Sector en la B.D" class="boton" onclick="location.href='index.php?m_e=sector&accion=insertar&return=1'"><br><br>
					<input type="button" value="Borrar Opción del listado" class="boton" onclick="borrarOpcionCombo(document.getElementById('id_sector'))">
					</td>
				</tr>					
				<tr><td>&nbsp;</td></tr>
			</table>
		</td>
	</tr>
	<tr>
		<td>
			<table class="tabla_consulta" cellspacing="1" cellpadding="5">
				<tr><td class="titulo_lista" align="center" colspan="2">SINCRONIZACION DE ENFOQUES</td></tr>
				<tr><td colspan="2">Ordene los enfoques de acuerdo al orden establecido en el formulario word de captura de información. Si es necesario cree nuevas opciones.</td></tr>		
				<tr>
					<td align="center">
						<select id="id_enfoque" name="id_enfoque" size="7" class="select">
							<?
							//ENFOQUE
							$enfoque_dao->ListarCombo('combo','','');
							?>
						</select>					
					</td>
					<td align="left">
					<input type="button" value="Mover Arriba" class="boton" onclick="moveOption(document.getElementById('id_enfoque'),'up','')"><br><br>
					<input type="button" value="Mover Abajo" class="boton" onclick="moveOption(document.getElementById('id_enfoque'),'down','')"><br><br>
					<input type="button" value="Crear Enfoque en la B.D" class="boton" onclick="location.href='index.php?m_e=enfoque&accion=insertar&return=1'"><br><br>
					<input type="button" value="Borrar Opción del listado" class="boton" onclick="borrarOpcionCombo(document.getElementById('id_enfoque'))">
					</td>
				</tr>					
				<tr><td>&nbsp;</td></tr>
			</table>
		</td>
	</tr>
	<tr>
		<td>
			<table class="tabla_consulta" cellspacing="1" cellpadding="5">
				<tr><td class="titulo_lista" align="center" colspan="2">SINCRONIZACION DE POBLACION</td></tr>
				<tr><td colspan="2">Ordene las poblaciones de acuerdo al orden establecido en el formulario word de captura de información. Si es necesario cree nuevas opciones.</td></tr>		
				<tr>
					<td align="center">
						<select id="id_poblacion" name="id_poblacion" size="13" class="select" style="width:300px">
							<?
							//POBLACION
							$poblacion_dao->ListarCombo('combo','','ID_POBLA > 32 ');
							?>
						</select>					
					</td>
					<td align="left">
					<input type="button" value="Mover Arriba" class="boton" onclick="moveOption(document.getElementById('id_poblacion'),'up','')"><br><br>
					<input type="button" value="Mover Abajo" class="boton" onclick="moveOption(document.getElementById('id_poblacion'),'down','')"><br><br>
					<input type="button" value="Crear Población en la B.D" class="boton" onclick="location.href='index.php?m_e=poblacion&accion=insertar&return=1'"><br><br>
					<input type="button" value="Borrar Opción del listado" class="boton" onclick="borrarOpcionCombo(document.getElementById('id_poblacion'))">
					</td>
				</tr>					
				<tr><td>&nbsp;</td></tr>
			</table>
		</td>
	</tr>

	<tr><td>&nbsp;</td></tr>	
	<tr><td><b>Nota:</b> Si el archivo CSV contiene Organizaciones que ya existen en el sistema, este módulo actualizará la información de la Organización de acuerdo a la información contenida en el archivo.</td></tr>
	<tr>
	  <td align='center'>
		  <input type="hidden" name="accion" value="<?=$accion?>" />
		  <input type="hidden" id="id_enfoque_h" name="id_enfoque_h" />
		  <input type="hidden" id="id_sector_h" name="id_sector_h" />
		  <input type="hidden" id="id_poblacion_h" name="id_poblacion_h" />		  		  
		  <input type="submit" name="submit" value="Importar" class="boton" onclick="copiarCombos();return validar_forma('id_depto,Departamento,archivo_csv,Archivo CSV','');" />	
	  </td>
	</tr>
</table>
</form>