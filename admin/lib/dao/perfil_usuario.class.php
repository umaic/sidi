<?
/**
 * DAO de PerfilUsuario
 *
 * Contiene los métodos de la clase PerfilUsuario
 * @author Ruben A. Rojas C.
 */

Class PerfilUsuarioDAO {

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
	 * Constructor
	 * Crea la conexión a la base de datos
	 * @access public
	 */
	function PerfilUsuarioDAO (){
		$this->conn = MysqlDb::getInstance();
		$this->tabla = "modulo_tipo_usuario";
		$this->columna_id = "";
		$this->columna_nombre = "";
		$this->columna_order = "ID_MODULO";
	}

	/**
	 * Consulta los datos de una PerfilUsuario
	 * @access public
	 * @param int $id ID del PerfilUsuario
	 * @return VO
	 */
	function Get($id){
		$sql = "SELECT * FROM ".$this->tabla." WHERE ".$this->columna_id." = ".$id;
		$rs = $this->conn->OpenRecordset($sql);
		$row_rs = $this->conn->FetchObject($rs);

		//Crea un VO
		$perfil_usuario_vo = New PerfilUsuario();

		//Carga el VO
		$perfil_usuario_vo = $this->GetFromResult($perfil_usuario_vo,$row_rs);

		//Retorna el VO
		return $perfil_usuario_vo;
	}

	/**
	 * Consulta los datos de los PerfilUsuario que cumplen una condición
	 * @access public
	 * @param string $condicion Condición que deben cumplir los PerfilUsuario y que se agrega en el SQL statement.
	 * @return array Arreglo de VOs
	 */
	function GetAllArray($condicion){
		//Crea un VO
		$vo = New PerfilUsuario();

		$modulo_dao = New ModuloDAO();

		$c = 0;
		$sql = "SELECT * FROM ".$this->tabla."";
		if ($condicion != ""){
			$sql .= " WHERE ".$condicion;
		}
		$sql .= " ORDER BY ".$this->columna_order;


		$rs = $this->conn->OpenRecordset($sql);
		while ($row_rs = $this->conn->FetchObject($rs)){
			$id = $row_rs->ID_MODULO;
			//Carga el VO
			$vo->id_modulo[] = $id;

			//Consulta el modulo padre
			$modulo = $modulo_dao->Get($id);
			if (!in_array($modulo->id_papa,$vo->id_modulo_papa))	$vo->id_modulo_papa[] = $modulo->id_papa; 	 
		}
		//Retorna el Arreglo de VO
		return $vo;
	}


	/**
	 * Lista los PerfilUsuario en una Tabla
	 * @access public
	 */
	function ListarTabla($condicion){

		$modulo_dao = New ModuloDAO();
		$tipo_usuario_dao = New TipoUsuarioDAO();

		$id_tipo_usuario = (isset($_GET["id_tipo_usuario"])) ? $_GET["id_tipo_usuario"] : 0;
		
		echo "<form onsubmit='submitForm(event);return false' method='POST'>";
		echo "<table width='500' align='center' class='tabla_lista'>";
		echo "<tr><td>Tipo de Usuario</td>";
		echo "<td><select name='id_tipo_usuario' class='select' onchange=\"refreshTab('index_parser.php?accion=listar&class=PerfilUsuarioDAO&method=ListarTabla&param=&id_tipo_usuario='+this.value)\">";
		echo "<option value='0'>Seleccione alguno</option>";
		$tipo_usuario_dao->ListarCombo('combo',$id_tipo_usuario,'CNRR = 0');
		echo "</select>";
		echo"</tr>";

		if (isset($_GET["id_tipo_usuario"])){

			$condicion = "ID_TIPO_USUARIO = ".$id_tipo_usuario;
			$perfil = $this->GetAllArray($condicion);

			$arr = $modulo_dao->GetAllArray('ID_PAPA = 0');

			echo "<tr><td>&nbsp;</td></tr>
				<tr class='titulo_lista'>
				<td>M&oacute;dulo</td>
				<td align='center'>Acceso</td>
				</tr>";


			foreach($arr as $vo){	

				echo "<tr>";
				echo "<td><b>".$vo->nombre."</b></a></td>";
				echo "<td align='center'>&nbsp;</td>";
				echo "</tr>";

				//HIJOS
				$arr_h = $modulo_dao->GetAllArray('ID_PAPA = '.$vo->id);
				foreach($arr_h as $vo_h){

					$chk = "";
					if (in_array($vo_h->id,$perfil->id_modulo))	$chk = " checked ";

					echo "<tr class='fila_lista'>";
					echo "<td>&nbsp;&nbsp;&nbsp;- ".$vo_h->nombre."</a></td>";
					echo "<td align='center'><input type='checkbox' name='id_modulo_perfil[]' value='".$vo_h->id."' ".$chk."></td>";
					echo "</tr>";
				}
			}
			echo "<tr><td>&nbsp;</td></tr>";
			echo "<tr><td colspan='2' align='center'>";
			echo "<input type='hidden' name='accion' value='actualizar' /><input type='submit' name='submit' value='Actualizar' class='boton' onclick=\"return validateCheckboxInput(document.forms[0],'El Tipo de Usuario seleccionado no va a tener acceso a ningún módulo, esta seguro?')\" />";
		}
		echo "</table></form>";
	}

	/**
	 * Inserta un PerfilUsuario en la B.D.
	 * @access public
	 * @param object $perfil_usuario_vo VO de PerfilUsuario que se va a insertar
	 */
	function Insertar($perfil_usuario_vo){

		for($p=0;$p<count($perfil_usuario_vo->id_modulo);$p++){
			$sql =  "INSERT INTO ".$this->tabla." (ID_TIPO_USUARIO,ID_MODULO) VALUES (".$perfil_usuario_vo->id_tipo_usuario.",".$perfil_usuario_vo->id_modulo[$p].")";
			$this->conn->Execute($sql);
		}
	}

	/**
	 * Actualiza un PerfilUsuario en la B.D.
	 * @access public
	 * @param object $perfil_usuario_vo VO de PerfilUsuario que se va a actualizar
	 */
	function Actualizar($perfil_usuario_vo){

		$this->Borrar($perfil_usuario_vo->id_tipo_usuario);
		$this->Insertar($perfil_usuario_vo);

	}

	/**
	 * Borra un Perfil en la B.D.
	 * @access public
	 * @param int $id ID del Perfil que se va a borrar de la B.D
	 */
	function Borrar($id){

		//BORRA
		$sql = "DELETE FROM ".$this->tabla." WHERE ID_TIPO_USUARIO = ".$id;
		$this->conn->Execute($sql);
	}

}

?>
