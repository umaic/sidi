<?
/**
 * DAO de TipoUsuario
 *
 * Contiene los métodos de la clase TipoUsuario 
 * @author Ruben A. Rojas C.
 */

Class TipoUsuarioDAO {

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
	function TipoUsuarioDAO (){
		$this->conn = MysqlDb::getInstance();
		$this->tabla = "tipo_usuario";
		$this->columna_id = "ID_TIPO_USUARIO";
		$this->columna_nombre = "NOM_TIPO";
		$this->columna_order = "NOM_TIPO";
	}

	/**
	 * Consulta los datos de una TipoUsuario
	 * @access public
	 * @param int $id ID del TipoUsuario
	 * @return VO
	 */	
	function Get($id){
		$sql = "SELECT * FROM ".$this->tabla." WHERE ".$this->columna_id." = ".$id;
		$rs = $this->conn->OpenRecordset($sql);
		$row_rs = $this->conn->FetchObject($rs);

		//Crea un VO
		$tipo_usuario_vo = New TipoUsuario();

		//Carga el VO
		$tipo_usuario_vo = $this->GetFromResult($tipo_usuario_vo,$row_rs);

		//Retorna el VO
		return $tipo_usuario_vo;
	}

	/**
	 * Consulta los datos de los TipoUsuario que cumplen una condición
	 * @access public
	 * @param string $condicion Condición que deben cumplir los TipoUsuario y que se agrega en el SQL statement.
	 * @return array Arreglo de VOs
	 */	
	function GetAllArray($condicion){
		$c = 0;
		$sql = "SELECT * FROM ".$this->tabla."";
		if ($condicion != ""){
			$sql .= " WHERE ".$condicion;
		}
		$sql .= " ORDER BY ".$this->columna_order;

		$array = Array();

		$rs = $this->conn->OpenRecordset($sql);
		while ($row_rs = $this->conn->FetchObject($rs)){
			//Crea un VO
			$vo = New TipoUsuario();
			//Carga el VO
			$vo = $this->GetFromResult($vo,$row_rs);
			//Carga el arreglo
			$array[$c] = $vo;
			$c++;
		}
		//Retorna el Arreglo de VO
		return $array;
	}

	/**
	 * Lista los TipoUsuario que cumplen la condición en el formato dado
	 * @access public
	 * @param string $formato Formato en el que se listarán los TipoUsuario, puede ser Tabla o ComboSelect
	 * @param int $valor_combo ID del TipoUsuario que será selccionado cuando el formato es ComboSelect
	 * @param string $condicion Condición que deben cumplir los TipoUsuario y que se agrega en el SQL statement.
	 */			
	function ListarCombo($formato,$valor_combo,$condicion){
		$arr = $this->GetAllArray($condicion);
		$num_arr = count($arr);
		$v_c_a = is_array($valor_combo);

		for($a=0;$a<$num_arr;$a++){
			$vo = $arr[$a];

			if ($valor_combo == "" && $valor_combo != 0)
				echo "<option value=".$vo->id.">".$vo->nombre."</option>";
			else{
				echo "<option value=".$vo->id;

				if (!$v_c_a){
					if ($valor_combo == $vo->id)
						echo " selected ";
				}
				else{
					if (in_array($vo->id,$valor_combo))
						echo " selected ";
				}

				echo ">".$vo->nombre."</option>";
			}
		}
	}

	/**
	 * Lista los TipoUsuario en una Tabla
	 * @access public
	 */			
	function ListarTabla($condicion){

		include_once ("lib/common/layout.class.php");

		$layout = new Layout();

		$layout->adminGrid(array ('nombre' => array ('titulo' => 'Nombre')));
	}

	/**
	 * Carga un VO de TipoUsuario con los datos de la consulta
	 * @access public
	 * @param object $vo VO de TipoUsuario que se va a recibir los datos
	 * @param object $Resultset Resource de la consulta
	 * @return object $vo VO de TipoUsuario con los datos
	 */			
	function GetFromResult ($vo,$Result){
		$vo->id = $Result->ID_TIPO_USUARIO;
		$vo->nombre = $Result->NOM_TIPO;
		$vo->cnrr = $Result->CNRR;

		return $vo;
	}

	/**
	 * Inserta un TipoUsuario en la B.D.
	 * @access public
	 * @param object $tipo_usuario_vo VO de TipoUsuario que se va a insertar
	 */		
	function Insertar($tipo_usuario_vo){
		//CONSULTA SI YA EXISTE
		$cat_a = $this->GetAllArray($this->columna_nombre." = '".$tipo_usuario_vo->nombre."'");
		if (count($cat_a) == 0){
			$sql =  "INSERT INTO ".$this->tabla." (".$this->columna_nombre.",CNRR) VALUES ('".$tipo_usuario_vo->nombre."',".$tipo_usuario_vo->cnrr.")";
			$this->conn->Execute($sql);

			echo "Registro insertado con &eacute;xito!";
		}
		else{
			echo "Error - Existe un registro con el mismo nombre";

		}
	}

	/**
	 * Actualiza un TipoUsuario en la B.D.
	 * @access public
	 * @param object $tipo_usuario_vo VO de TipoUsuario que se va a actualizar
	 */		
	function Actualizar($tipo_usuario_vo){
		$sql =  "UPDATE ".$this->tabla." SET ";
		$sql .= $this->columna_nombre." = '".$tipo_usuario_vo->nombre."',";
		$sql .= "CNRR = ".$tipo_usuario_vo->cnrr."";
		$sql .= " WHERE ".$this->columna_id." = ".$tipo_usuario_vo->id;

		$this->conn->Execute($sql);

	}

	/**
	 * Borra un TipoUsuario en la B.D.
	 * @access public
	 * @param int $id ID del TipoUsuario que se va a borrar de la B.D
	 */	
	function Borrar($id){

		//BORRA
		$sql = "DELETE FROM ".$this->tabla." WHERE ".$this->columna_id." = ".$id;
		$this->conn->Execute($sql);

	}

	/**
	 * Check de llaves foraneas, para permitir acciones como borrar
	 * @access public
	 * @param int $id ID del registro a consultar
	 */	
	function checkForeignKeys($id){

		$tabla_rel = 'usuario';

		$sql = "SELECT sum($this->columna_id) FROM $tabla_rel WHERE ".$this->columna_id." = ".$id;
		$rs = $this->conn->OpenRecordset($sql);
		$row = $this->conn->FetchRow($rs);

		$r = ($row[0] > 0) ? true : false;

		return $r;

	}
}

?>
