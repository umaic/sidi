<?
/**
 * DAO de ContactoCol
 *
 * Contiene los métodos de la clase ContactoCol 
 * @author Ruben A. Rojas C.
 */

Class ContactoColDAO {

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
	function ContactoColDAO (){
		$this->conn = MysqlDb::getInstance();
		$this->tabla = "contacto_col";
		$this->columna_id = "ID_CONTACTO_COL";
		$this->columna_nombre = "NOM_CONTACTO_COL";
		$this->columna_order = "NOM_CONTACTO_COL";
	}

	/**
	 * Consulta los datos de una ContactoCol
	 * @access public
	 * @param int $id ID del ContactoCol
	 * @return VO
	 */	
	function Get($id){
		$sql = "SELECT * FROM ".$this->tabla." WHERE ".$this->columna_id." = ".$id;
		$rs = $this->conn->OpenRecordset($sql);
		$row_rs = $this->conn->FetchObject($rs);

		//Crea un VO
		$contacto_col_vo = New ContactoCol();

		//Carga el VO
		$contacto_col_vo = $this->GetFromResult($contacto_col_vo,$row_rs);

		//Retorna el VO
		return $contacto_col_vo;
	}

	/**
	 * Consulta los datos de los Tema que cumplen una condición
	 * @access public
	 * @param string $condicion Condición que deben cumplir los Tema y que se agrega en el SQL statement.
	 * @param string $limit Limit en el SQL
	 * @param string $order by Order by en el SQL 
	 * @return array Arreglo de VOs
	 */	
	function GetAllArray($condicion,$limit='',$order_by=''){
		
		$sql = "SELECT * FROM ".$this->tabla;
		if ($condicion != ""){
			$sql .= " WHERE ".$condicion;
		}

		//ORDER
		if ($order_by != ""){
			$sql .= " ORDER BY ".$order_by;
		}
		else{
			$sql .= " ORDER BY ".$this->columna_order;
		}

		//LIMIT
		if ($limit != ""){
			$sql .= " LIMIT ".$limit;
		}

		$array = Array();

		$rs = $this->conn->OpenRecordset($sql);
		while ($row_rs = $this->conn->FetchObject($rs)){
			//Crea un VO
			$vo = New ContactoCol();
			//Carga el VO
			$vo = $this->GetFromResult($vo,$row_rs);
			//Carga el arreglo
			$array[] = $vo;
		}  
		//Retorna el Arreglo de VO
		return $array;
    }

	/**
	 * Lista los ContactoCol que cumplen la condición en el formato dado
	 * @access public
	 * @param string $formato Formato en el que se listarán los ContactoCol, puede ser Tabla o ComboSelect
	 * @param int $valor_combo ID del ContactoCol que será selccionado cuando el formato es ComboSelect
	 * @param string $condicion Condición que deben cumplir los ContactoCol y que se agrega en el SQL statement.
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
	 * Lista los ContactoCol en una Tabla
	 * @access public
	 */			
	function ListarTabla($condicion){
	
		include_once ("lib/common/layout.class.php");

		$layout = new Layout();

		$layout->adminGrid(array('nombre' => array ('titulo' => 'Nombre')));

	}

	/**
	 * Carga un VO de ContactoCol con los datos de la consulta
	 * @access public
	 * @param object $vo VO de ContactoCol que se va a recibir los datos
	 * @param object $Resultset Resource de la consulta
	 * @return object $vo VO de ContactoCol con los datos
	 */			
	function GetFromResult ($vo,$Result){
		
		$vo->id = $Result->{$this->columna_id};
		$vo->nombre = $Result->{$this->columna_nombre};

		return $vo;
	}

	/**
	 * Inserta un ContactoCol en la B.D.
	 * @access public
	 * @param object $contacto_col_vo VO de ContactoCol que se va a insertar
	 */		
	function Insertar($contacto_col_vo){
		//CONSULTA SI YA EXISTE
		$cat_a = $this->GetAllArray($this->columna_nombre." = '".$contacto_col_vo->nombre."'");
		if (count($cat_a) == 0){
			$sql =  "INSERT INTO ".$this->tabla." (".$this->columna_nombre.") VALUES ('".$contacto_col_vo->nombre."')";
			$this->conn->Execute($sql);

			echo "Registro insertado con éxito!";
		}
		else{
			echo "Error - Existe un registro con el mismo nombre";
		}
	}

	/**
	 * Actualiza un ContactoCol en la B.D.
	 * @access public
	 * @param object $contacto_col_vo VO de ContactoCol que se va a actualizar
	 */		
	function Actualizar($contacto_col_vo){
		$sql =  "UPDATE ".$this->tabla." SET ";
		$sql .= $this->columna_nombre." = '".$contacto_col_vo->nombre."'";
		$sql .= " WHERE ".$this->columna_id." = ".$contacto_col_vo->id;

		$this->conn->Execute($sql);

	}

	/**
	 * Borra un ContactoCol en la B.D.
	 * @access public
	 * @param int $id ID del ContactoCol que se va a borrar de la B.D
	 */	
	function Borrar($id){

		//BORRA
		$sql = "DELETE FROM ".$this->tabla." WHERE ".$this->columna_id." = ".$id;
		$this->conn->Execute($sql);

	}

	/**
	 * Consulta si existe informacion asociada a la caracteristica
	 * @access public
	 * @param int $id ID del ContactoCol que se va a borrar de la B.D
	 * @return boolean $check
	 */	
	function checkForeignKeys($id){

		$sql = "SELECT $this->columna_id FROM contacto_opcion_valor WHERE $this->columna_id = $id";
		$rs = $this->conn->OpenRecordset($sql);

		$sql_op = "SELECT $this->columna_id FROM contacto_col_opcion WHERE $this->columna_id = $id";
		$rs_op = $this->conn->OpenRecordset($sql_op);

		if ($this->conn->RowCount($rs) == 0 && $this->conn->RowCount($rs_op) == 0)	return false;
		else																		return true;
	}
}

?>
