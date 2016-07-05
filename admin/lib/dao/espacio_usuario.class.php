<?
/**
 * DAO de EspacioUsuario
 *
 * Contiene los métodos de la clase EspacioUsuario
 * @author Ruben A. Rojas C.
 */

Class EspacioUsuarioDAO {

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
	function EspacioUsuarioDAO (){
		$this->conn = MysqlDb::getInstance();
		$this->tabla = "espacio_tipo_usuario";
		$this->columna_id = "";
		$this->columna_nombre = "";
		$this->columna_order = "NOM_ESP";
	}

	/**
	 * Consulta los datos de una EspacioUsuario
	 * @access public
	 * @param int $id ID del EspacioUsuario
	 * @return VO
	 */
	function Get($id){
		$sql = "SELECT * FROM ".$this->tabla." WHERE ".$this->columna_id." = ".$id;
		$rs = $this->conn->OpenRecordset($sql);
		$row_rs = $this->conn->FetchObject($rs);

		//Crea un VO
		$espacio_usuario_vo = New EspacioUsuario();

		//Carga el VO
		$espacio_usuario_vo = $this->GetFromResult($espacio_usuario_vo,$row_rs);

		//Retorna el VO
		return $espacio_usuario_vo;
	}

	/**
	 * Consulta los datos de los EspacioUsuario que cumplen una condición
	 * @access public
	 * @param string $condicion Condición que deben cumplir los EspacioUsuario y que se agrega en el SQL statement.
	 * @return array Arreglo de VOs
	 */
	function GetAllArray($condicion){
		//Crea un VO
		$vo = New EspacioUsuario();

		$espacio_dao = New EspacioDAO();

		$sql = "SELECT * FROM ".$this->tabla." LEFT JOIN espacio USING(id_esp) ";
		if ($condicion != ""){
			$sql .= " WHERE ".$condicion;
		}

		$sql .= " ORDER BY ".$this->columna_order;
		//echo $sql;

		$rs = $this->conn->OpenRecordset($sql);
		while ($row_rs = $this->conn->FetchObject($rs)){
			$id = $row_rs->ID_ESP;
			$vo->id_espacio[] = $id;
		}

		//Retorna el Arreglo de VO
		return $vo;
	}


	/**
	 * Lista los EspacioUsuario en una Tabla
	 * @access public
	 */
	function ListarTabla($condicion){

		$espacio_dao = New EspacioDAO();
		$tipo_usuario_dao = New TipoUsuarioDAO();

		$id_tipo_usuario = 0;
		if (isset($_GET["id_tipo_usuario"])){
			$id_tipo_usuario = $_GET["id_tipo_usuario"];
		}

		echo "<form onsubmit=\"submitForm(event);return false\" method='POST'>";
		echo "<table width='50%' align='center' class='tabla_lista'>";
		echo "<tr><td>Tipo de Usuario</td>";
		echo "<td><select name='id_tipo_usuario' class='select' onchange=\"refreshTab('index_parser.php?accion=listar&class=EspacioUsuarioDAO&method=ListarTabla&param=&id_tipo_usuario='+this.value)\">";
		echo "<option value='0'>Seleccione alguno</option>";
		$tipo_usuario_dao->ListarCombo('combo',$id_tipo_usuario,'CNRR = 0');
		echo "</select>";
		echo"</tr>";

		if (isset($_GET["id_tipo_usuario"])){

			$condicion = "ID_TIPO_USUARIO = $id_tipo_usuario";
			$espacio = $this->GetAllArray($condicion);

			$arr = $espacio_dao->GetAllArray('id_papa = 0');

			echo "<tr><td>&nbsp;</td></tr>
				<tr class='titulo_lista'>
				<td>Espacio</td>
				<td align='center'>Acceso</td>
				</tr>";

			foreach ($arr as $vo){

				echo "<tr class='fila_lista'>";
				echo "<td>".$vo->nombre."</a></td>";

				$chk = "";
				if (in_array($vo->id,$espacio->id_espacio))	$chk = " checked ";

				echo "<td align='center'><input type='checkbox' name='id_espacio[]' value='".$vo->id."' ".$chk."></td>";
				echo "</tr>";

				$this->ListarTablaHijos($vo->id,"",$id_tipo_usuario);
			}
			echo "<tr><td>&nbsp;</td></tr>";
			echo "<tr><td colspan='2' align='center'>";
			echo "<input type='hidden' name='accion' value='actualizar' /><input type='submit' name='submit' value='Actualizar' class='boton' onclick=\"return validateCheckboxInput(document.forms[0],'El Tipo de Usuario seleccionado no va a tener acceso a ningún módulo, esta seguro?')\" />";
		}
		echo "</table></form>";
	}

	/**
	 * Lista las items en una Tabla
	 * @access public
	 */			
	function ListarTablaHijos($id_papa,$tab,$id_tipo_usuario){

		$espacio_dao = New EspacioDAO();
		$tab .= "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";

		$condicion = "ID_TIPO_USUARIO = $id_tipo_usuario";
		$espacio = $this->GetAllArray($condicion);

		$arr = $espacio_dao->GetAllArray("id_papa = $id_papa");
		$num_arr = count($arr);

		foreach($arr as $vo){

			echo "<tr class='fila_lista'>";
			echo "<td>$tab".$vo->nombre."</a></td>";

			$chk = "";
			if (in_array($vo->id,$espacio->id_espacio))	$chk = " checked ";

			echo "<td align='center'><input type='checkbox' name='id_espacio[]' value='".$vo->id."' ".$chk."></td>";
			echo "</tr>";

			$this->ListarTablaHijos($vo->id,$tab,$id_tipo_usuario);

		}
	}

	/**
	 * Inserta un EspacioUsuario en la B.D.
	 * @access public
	 * @param object $espacio_usuario_vo VO de EspacioUsuario que se va a insertar
	 */
	function Insertar($vo){

		foreach($vo->id_espacio as $id_e){
			$sql =  "INSERT INTO ".$this->tabla." (ID_TIPO_USUARIO,ID_ESP) VALUES (".$vo->id_tipo_usuario.",".$id_e.")";
			$this->conn->Execute($sql);
		}
	}

	/**
	 * Actualiza un EspacioUsuario en la B.D.
	 * @access public
	 * @param object $espacio_usuario_vo VO de EspacioUsuario que se va a actualizar
	 */
	function Actualizar($espacio_usuario_vo){

		$this->Borrar($espacio_usuario_vo->id_tipo_usuario);
		$this->Insertar($espacio_usuario_vo);

	}

	/**
	 * Borra un Espacio en la B.D.
	 * @access public
	 * @param int $id ID del Espacio que se va a borrar de la B.D
	 */
	function Borrar($id){

		//BORRA
		$sql = "DELETE FROM ".$this->tabla." WHERE ID_TIPO_USUARIO = ".$id;
		$this->conn->Execute($sql);
	}

}

?>
