<?
/**
 * DAO de Cnrr
 *
 * Contiene los métodos de la clase Cnrr 
 * @author Ruben A. Rojas C.
 */

Class CnrrDAO {

	/**
	* Conexión a la base de datos
	* @var object 
	*/
	var $conn;

	/**
	* Nombre de la Tabla en la Base de Datos
	* @var string
	*/
	var $tabla;

	/**
	* Nombre de la columna ID de la Tabla en la Base de Datos
	* @var string
	*/
	var $columna_id;

	/**
	* Nombre de la columna Nombre de la Tabla en la Base de Datos
	* @var string
	*/
	var $columna_nombre;

	/**
	* Nombre de la columna para ordenar el RecordSet
	* @var string
	*/
	var $columna_order;

	/**
	* Número de Registros en Pantalla para ListarTAbla
	* @var string
	*/
	var $num_reg_pag;

	/**
	* URL para redireccionar después de Insertar, Actualizar o Borrar
	* @var string
	*/
	var $url;

	/**
  * Constructor
	* Crea la conexión a la base de datos
  * @access public
  */	
	function CnrrDAO (){
		$this->conn = MysqlDb::getInstance();
		$this->tabla = "cnrr";
		$this->columna_id = "ID_EDAD";
		$this->columna_nombre = "NOMBRE_EDAD";
		$this->columna_order = "NOMBRE_EDAD";
		$this->num_reg_pag = 20;
		$this->url = "";
		
		$this->enfoque_dao = New EnfoqueDAO();
		$this->poblacion_dao = New PoblacionDAO();
		$this->sector_dao = New SectorDAO();
		$this->tipo_dao = New TipoOrganizacionDAO();
		
	}

	/**
  * Consulta los datos de una Cnrr
  * @access public
  * @param int $id ID del Cnrr
  * @return VO
  */	
	function Get(){
		
		$enfs = $this->enfoque_dao->GetAllArray('CNRR = 1');
		$enfs_ocha = $this->enfoque_dao->GetAllArray('OCHA = 1');
		$pobs = $this->poblacion_dao->GetAllArray('CNRR = 1','','');
		$pobs_ocha = $this->poblacion_dao->GetAllArray('OCHA = 1','','');
		$sectores = $this->sector_dao->GetAllArray('CNRR = 1');
		$sectores_ocha = $this->sector_dao->GetAllArray('OCHA = 1');
		$tipos = $this->tipo_dao->GetAllArray('CNRR = 1');
		$tipos_ocha = $this->tipo_dao->GetAllArray('OCHA = 1');
		
		$cnrr = New Cnrr();
		
		$i = 0;
		foreach ($enfs as $vo){
			$cnrr->id_enfoques[$i] = $vo->id;
			$i++;
		}
		
		$i = 0;
		foreach ($enfs_ocha as $vo){
			$cnrr->id_enfoques_ocha[$i] = $vo->id;
			$i++;
		}

		$i = 0;
		foreach ($pobs as $vo){
			$cnrr->id_poblaciones[$i] = $vo->id;
			$i++;
		}

		$i = 0;
		foreach ($pobs_ocha as $vo){
			$cnrr->id_poblaciones_ocha[$i] = $vo->id;
			$i++;
		}

		$i = 0;
		foreach ($sectores as $vo){
			$cnrr->id_sectores[$i] = $vo->id;
			$i++;
		}

		$i = 0;
		foreach ($sectores_ocha as $vo){
			$cnrr->id_sectores_ocha[$i] = $vo->id;
			$i++;
		}

		$i = 0;
		foreach ($tipos as $vo){
			$cnrr->id_tipos[$i] = $vo->id;
			$i++;
		}

		$i = 0;
		foreach ($tipos_ocha as $vo){
			$cnrr->id_tipos_ocha[$i] = $vo->id;
			$i++;
		}
		
		//Retorna el VO
		return $cnrr;
	}

	/**
  * Lista las entidades a administrar
  * @access public
  */			
	function ListarTablaEPST($condicion){
		
		$enfs = $this->enfoque_dao->GetAllArray('');
		$pobs = $this->poblacion_dao->GetAllArray('','','');
		$sectores = $this->sector_dao->GetAllArray('');
		$tipos = $this->tipo_dao->GetAllArray('');

		$arr = $this->Get();
		
		$objs_hide = "tabla_enfoque,tabla_poblacion,tabla_sector,tabla_tipo";
		
		?>
		<form action='<?=$_SERVER['PHP_SELF']?>' method='POST'>
		<table cellpadding="5" cellspacing="1" align="center">
			<tr class='titulo_lista'><td align="center">ADMINISTRACION OCHA - CNRR</td></tr>
			<tr><td>Seleccione la entidad que desea administrar: &nbsp;<a href="#" onclick="showHide('tabla_enfoque','<?=$objs_hide?>');">Enfoque</a> | <a href="#" onclick="showHide('tabla_poblacion','<?=$objs_hide?>');">Población</a> | <a href="#" onclick="showHide('tabla_sector','<?=$objs_hide?>');">Sector</a> | <a href="#" onclick="showHide('tabla_tipo','<?=$objs_hide?>');">Tipo Organización</a></td></tr>
			<tr><td>Seleccionar  <a href="#" onclick="selectAllCheckbox()">Todos</a></td></tr>
			
		</table>		
		<br>
		<table cellpadding="5" cellspacing="1" id="tabla_enfoque" align="center" width="400">
			<tr class='titulo_lista'><td>Enfoque</td><td width='90'>OCHA</td><td width='90'>CNRR</td></tr>
			<?
			foreach ($enfs as $vo){
				in_array($vo->id,$arr->id_enfoques) ?	$checked = "checked" : $checked = "";
				in_array($vo->id,$arr->id_enfoques_ocha) ?	$checked_ocha = "checked" : $checked_ocha = "";

				echo "<tr class='fila_lista'><td>".$vo->nombre_es."</td><td><input type='checkbox' name='id_enfoques_ocha[]' ".$checked_ocha." value=".$vo->id."></td><td><input type='checkbox' name='id_enfoques[]' ".$checked." value=".$vo->id."></td></tr>";
			}
			?>
		</table>
		<table cellpadding="5" cellspacing="1" style="display:none" id="tabla_poblacion" align="center" width="400">
			<tr class='titulo_lista'><td>Población</td><td width='90'>OCHA</td><td width='90'>CNRR</td></tr>
			<?
			foreach ($pobs as $vo){
				in_array($vo->id,$arr->id_poblaciones) ?	$checked = "checked" : $checked = "";
				in_array($vo->id,$arr->id_poblaciones_ocha) ?	$checked_ocha = "checked" : $checked_ocha = "";

				echo "<tr class='fila_lista'><td>".$vo->nombre_es."</td><td><input type='checkbox' name='id_poblaciones_ocha[]' ".$checked_ocha." value=".$vo->id."></td><td><input type='checkbox' name='id_poblaciones[]' ".$checked." value=".$vo->id."></td></tr>";
			}
			?>
		</table>
		<table cellpadding="5" cellspacing="1" style="display:none" id="tabla_sector" align="center" width="400">
			<tr class='titulo_lista'><td>Sector</td><td width='90'>OCHA</td><td width='90'>CNRR</td></tr>
			<?
			foreach ($sectores as $vo){
				
				in_array($vo->id,$arr->id_sectores) ?	$checked = "checked" : $checked = "";
				in_array($vo->id,$arr->id_sectores_ocha) ?	$checked_ocha = "checked" : $checked_ocha = "";
				
				echo "<tr class='fila_lista'><td>".$vo->nombre_es."</td><td><input type='checkbox' name='id_sectores_ocha[]' ".$checked_ocha." value=".$vo->id."></td><td><input type='checkbox' name='id_sectores[]' ".$checked." value=".$vo->id."></td></tr>";
			}
			?>
		</table>
		<table cellpadding="5" cellspacing="1" style="display:none" id="tabla_tipo" align="center" width="400">
			<tr class='titulo_lista'><td>Tipo Organización</td><td width='90'>OCHA</td><td width='90'>CNRR</td></tr>
			<?
			foreach ($tipos as $vo){
				
				in_array($vo->id,$arr->id_tipos) ?	$checked = "checked" : $checked = "";
				in_array($vo->id,$arr->id_tipos_ocha) ?	$checked_ocha = "checked" : $checked_ocha = "";
				
				echo "<tr class='fila_lista'><td>".$vo->nombre_es."</td><td><input type='checkbox' name='id_tipos_ocha[]' ".$checked_ocha." value=".$vo->id."></td><td><input type='checkbox' name='id_tipos[]' ".$checked." value=".$vo->id."></td></tr>";
			}
			?>
		</table>
		<table cellpadding="5" cellspacing="1" align="center" width="400" border="0">
			<tr>
				<td align="center">
					<input type='hidden' name="accion" value="actualizar">
					<input type="submit" value="Aceptar" name="submit">
				</td>
			</tr>
		</table>		
		</form>
		<?

	}

	/**
  * Lista las entidades a administrar
  * @access public
  */			
	function ListarTablaPerfil($condicion){
		
		
		$modulo_dao = New ModuloDAO();
		$tipo_usuario_dao = New TipoUsuarioDAO();
	  
		if (isset($_GET["id_tipo_usuario"])){
			$id_tipo_usuario = $_GET["id_tipo_usuario"]; 
		}	
		
		echo "<form action='".$_SERVER['PHP_SELF']."' method='POST'>";
		echo "<table width='700' align='center' cellspacing='1' cellpadding='3'>";
		echo "<tr><td>&nbsp;</td></tr>";
		echo "<tr class='titulo_lista'><td width='400'>Tipo de Usuario</td>";
		echo "<td><select name='id_tipo_usuario' class='select' onchange=\"location.href='".$_SERVER['PHP_SELF']."?accion=".$_GET["accion"]."&class=".$_GET["class"]."&method=".$_GET["method"]."&param=&id_tipo_usuario='+this.value;\">";
		echo "<option value='0'>Seleccione alguno</option>";
		$tipo_usuario_dao->ListarCombo('combo',$id_tipo_usuario,'CNRR = 1');
		echo "</select>";
		echo"</tr>";
		
		if (isset($_GET["id_tipo_usuario"])){
		  	
		  	$p = 0;
			$sql = "SELECT * FROM perfil_usuario_cnrr WHERE ID_TIPO_USUARIO = ".$id_tipo_usuario;
			$rs = $this->conn->OpenRecordset($sql);
			$row_rs = $this->conn->FetchObject($rs);
			
			
			$row_rs->ADMIN_ORG == 1 ? $chk_admin_org = " checked " : $chk_admin_org = "";
			$row_rs->ALIMENTACION_ORG == 1 ? $chk_alimentacion_org = " checked " : $chk_alimentacion_org = "";
			$row_rs->CONSULTA_ORG == 1 ? $chk_consulta_org = " checked " : $chk_consulta_org = "";
			$row_rs->VER_CONTACTO_ORG == 1 ? $chk_ver_contacto_org = " checked " : $chk_ver_contacto_org = "";
			
			echo "<tr class='fila_lista'><td>Administrar Organizaciones</td><td><input type='checkbox' name='admin_org' value=1 ".$chk_admin_org."></td></tr>";
			echo "<tr class='fila_lista'><td>Alimentar Organizaciones</td><td><input type='checkbox' name='alimentacion_org' value=1 ".$chk_alimentacion_org."></td></tr>";
			echo "<tr class='fila_lista'><td>Consultar Organizaciones y <b>Ver</b> datos de contácto</td><td><input type='checkbox' name='ver_contacto_org' value=1 ".$chk_ver_contacto_org."></td></tr>";
			echo "<tr class='fila_lista'><td>Consultar Organizaciones  y <b>NO Ver</b> datos de contácto</td><td><input type='checkbox' name='consulta_org' value=1 ".$chk_consulta_org."></td></tr>";
				
			echo "<tr><td>&nbsp;</td></tr>";
			echo "<tr><td colspan='2' align='center'>";
			echo "<input type='hidden' name='accion' value='actualizarPerfil' /><input type='submit' name='submit' value='Actualizar' class='boton' onclick=\"return validateCheckboxInput(document.forms[0],'El Tipo de Usuario seleccionado no va a tener acceso a ningún módulo, esta seguro?')\" />";
		}
		echo "</table></form>";			
	}


	/**
	* Actualiza los enfoques, poblacione, sectores y tipos de organizacion para CNRR
	* @param  VO Objeto CNRR
	* @access public
	*/			
	function Actualizar($vo){
		
		//ENFOQUES
		$sql = "UPDATE enfoque SET CNRR = 0,OCHA = 0";
		$this->conn->Execute($sql);

		foreach ($vo->id_enfoques as $id) {
			$sql = "UPDATE enfoque SET CNRR = 1 WHERE ID_ENF = ".$id;
			$this->conn->Execute($sql);
		}
		
		foreach ($vo->id_enfoques_ocha as $id) {
			$sql = "UPDATE enfoque SET OCHA = 1 WHERE ID_ENF = ".$id;
			$this->conn->Execute($sql);
		}

		//POBLACIONES
		$sql = "UPDATE poblacion SET CNRR = 0,OCHA = 0";
		$this->conn->Execute($sql);

		foreach ($vo->id_poblaciones as $id) {
			$sql = "UPDATE poblacion SET CNRR = 1 WHERE ID_POBLA = ".$id;
			$this->conn->Execute($sql);
		}

		foreach ($vo->id_poblaciones_ocha as $id) {
			$sql = "UPDATE poblacion SET OCHA = 1 WHERE ID_POBLA = ".$id;
			$this->conn->Execute($sql);
		}

		//SECTORES
		$sql = "UPDATE sector SET CNRR = 0,OCHA = 0";
		$this->conn->Execute($sql);

		foreach ($vo->id_sectores as $id) {
			$sql = "UPDATE sector SET CNRR = 1 WHERE ID_COMP = ".$id;
			$this->conn->Execute($sql);
		}

		foreach ($vo->id_sectores_ocha as $id) {
			$sql = "UPDATE sector SET OCHA = 1 WHERE ID_COMP = ".$id;
			$this->conn->Execute($sql);
		}

		//TIPO DE ORGANIZACION
		$sql = "UPDATE tipo_org SET CNRR = 0,OCHA = 0";
		$this->conn->Execute($sql);

		foreach ($vo->id_tipos as $id) {
			$sql = "UPDATE tipo_org SET CNRR = 1 WHERE ID_TIPO = ".$id;
			$this->conn->Execute($sql);
		}
		
		foreach ($vo->id_tipos_ocha as $id) {
			$sql = "UPDATE tipo_org SET OCHA = 1 WHERE ID_TIPO = ".$id;
			$this->conn->Execute($sql);
		}

		$this->url = "index.php?accion=listar&class=CnrrDAO&method=ListarTablaEPST&param=";

		?>
	  	<script>
	  	alert("Operación realizada con éxito!");
	  	location.href = '<?=$this->url;?>';	
	  	</script>
	  	<?
	}
	
	/**
	* Actualiza el perfil de tipo de usuario CNRR
	* @param  int $id_tipo Tipo de Usuario
	* @param  int $admin_org Acceso a administrar Orgs
	* @param  int $alimentacion_org Acceso a alimentar Orgs
	* @param  int $consulta_org Acceso a consultar Orgs
	* @param  int $ver_contacto_org Acceso a consultar Orgs y Ver datos de contácto de Orgs
	* @access public
	*/			
	function ActualizarPerfil($id_tipo,$admin_org,$alimentacion_org,$consulta_org,$ver_contacto_org){
		
		$sql = "DELETE FROM perfil_usuario_cnrr WHERE ID_TIPO_USUARIO = ".$id_tipo;
		$this->conn->Execute($sql);
				
		$sql = "INSERT INTO perfil_usuario_cnrr (ADMIN_ORG,ALIMENTACION_ORG,CONSULTA_ORG,VER_CONTACTO_ORG,ID_TIPO_USUARIO) VALUES (".$admin_org.",".$alimentacion_org.",".$consulta_org.",".$ver_contacto_org.",".$id_tipo.")";
		$this->conn->Execute($sql);

		$this->url = "index.php?accion=listar&class=CnrrDAO&method=ListarTablaPerfil&param=";

		?>
	  	<script>
	  	alert("Operación realizada con éxito!");
	  	location.href = '<?=$this->url;?>';	
	  	</script>
	  	<?

	}
	
}

?>