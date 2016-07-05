<?
/**
 * DAO de ContactoColOp
 *
 * Contiene los métodos de la clase ContactoColOp
 * @author Ruben A. Rojas C.
 */

Class ContactoColOpDAO {

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
	function ContactoColOpDAO (){
		$this->conn = MysqlDb::getInstance();
		$this->tabla = "contacto_col_opcion";
		$this->columna_id = "ID_CONTACTO_COL_OPCION";
		$this->columna_nombre = "NOM_CONTACTO_COL_OPCION";
		$this->columna_order = "NOM_CONTACTO_COL_OPCION";
	}

	/**
	 * Consulta los datos de una ContactoColOp
	 * @access public
	 * @param int $id ID del ContactoColOp
	 * @return VO
	 */
	function Get($id){
		$sql = "SELECT * FROM ".$this->tabla." WHERE ".$this->columna_id." = ".$id;
		$rs = $this->conn->OpenRecordset($sql);
		$row_rs = $this->conn->FetchObject($rs);

		//Crea un VO
		$vo = New ContactoColOp();

		//Carga el VO
		$vo = $this->GetFromResult($vo,$row_rs);

		//Retorna el VO
		return $vo;
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
			$vo = New ContactoColOp();
			//Carga el VO
			$vo = $this->GetFromResult($vo,$row_rs);
			//Carga el arreglo
			$array[] = $vo;
		}  
		//Retorna el Arreglo de VO
		return $array;
    }

    /**
     * Consulta los ID de las opciones que cumplen una condición
     * @access public
     * @param string $condicion Condicion que deben cumplir los Organizacion y que se agrega en el SQL statement.
     * @return array Arreglo
     */
    function GetAllArrayID($condicion,$limit,$order_by){

		$sql = "SELECT ".$this->columna_id." FROM ".$this->tabla;
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
		while ($row_rs = $this->conn->FetchRow($rs)){
			//Carga el arreglo
			$array[] = $row_rs[0];
		}
		//Retorna el Arreglo de VO
		return $array;
	}

	/**
	 * Lista los ContactoColOp que cumplen la condición en el formato dado
	 * @access public
	 * @param string $formato Formato en el que se listarán los ContactoColOp, puede ser Tabla o ComboSelect
	 * @param int $valor_combo ID del ContactoColOp que será selccionado cuando el formato es ComboSelect
	 * @param string $condicion Condición que deben cumplir los ContactoColOp y que se agrega en el SQL statement.
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
	 * Lista los TipoEvento en una Tabla
	 * @access public
	 */
	function ListarTabla($condicion){

		include_once ("lib/common/layout.class.php");

		$layout = new Layout();

		$layout->adminGrid(array('nombre' => array('titulo' => 'Nombre')),
						   array('id_contacto_col' => array('dao' => 'ContactoColDAO', 'nom' => 'nombre', 'titulo' => 'Caracter&iacute;sitica', 'filtro' => true)));
	}

	/**
	 * Imprime en pantalla los datos del ContactoColOp
	 * @access public
	 * @param object $vo ContactoColOp que se va a imprimir
	 * @param string $formato Formato en el que se listarán los ContactoColOp, puede ser Tabla o ComboSelect
	 * @param int $valor_combo ID del ContactoColOp que será selccionado cuando el formato es ComboSelect
	 */
	function Imprimir($vo,$formato,$valor_combo){

		$v_c_a = is_array($valor_combo);

		if ($formato == 'combo'){
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
	 * Carga un VO de ContactoColOp con los datos de la consulta
	 * @access public
	 * @param object $vo VO de ContactoColOp que se va a recibir los datos
	 * @param object $Resultset Resource de la consulta
	 * @return object $vo VO de ContactoColOp con los datos
	 */
	function GetFromResult ($vo,$Result){

		$vo->id = $Result->{$this->columna_id};
		$vo->nombre = $Result->{$this->columna_nombre};
		$vo->id_contacto_col = $Result->ID_CONTACTO_COL;

		return $vo;
	}

	/**
	 * Inserta un ContactoColOp en la B.D.
	 * @access public
	 * @param object $vo VO de ContactoColOp que se va a insertar
	 */
	function Insertar($vo){
		//CONSULTA SI YA EXISTE
		$cat_a = $this->GetAllArray($this->columna_nombre." = '".$vo->nombre."' AND id_contacto_col = $vo->id_contacto_col");
		if (count($cat_a) == 0){
			$sql =  "INSERT INTO ".$this->tabla." (".$this->columna_nombre.",ID_CONTACTO_COL) VALUES ('".$vo->nombre."',".$vo->id_contacto_col.")";
			$this->conn->Execute($sql);

			echo "Registro insertado con éxito!";
		}
		else{
			echo "Error - Existe un registro con el mismo nombre";
		}
	}

	/**
	 * Actualiza un ContactoColOp en la B.D.
	 * @access public
	 * @param object $vo VO de ContactoColOp que se va a actualizar
	 */
	function Actualizar($vo){
		$sql =  "UPDATE ".$this->tabla." SET ";
		$sql .= $this->columna_nombre." = '".$vo->nombre."',";
		$sql .= "ID_CONTACTO_COL = '".$vo->id_contacto_col."'";
		$sql .= " WHERE ".$this->columna_id." = ".$vo->id;

		$this->conn->Execute($sql);

	}

	/**
	 * Borra un ContactoColOp en la B.D.
	 * @access public
	 * @param int $id ID del ContactoColOp que se va a borrar de la B.D
	 */
	function Borrar($id){

		//BORRA
		$sql = "DELETE FROM ".$this->tabla." WHERE ".$this->columna_id." = ".$id;
		$this->conn->Execute($sql);

	}

	/**
	 * Retorna el numero de Registros
	 * @access public
	 * @return int
	 */
	function numRecords($condicion){
		$sql = "SELECT count(".$this->columna_id.") as num FROM ".$this->tabla;
		if ($condicion != ""){
			$sql .= " WHERE ".$condicion;
		}
		$rs = $this->conn->OpenRecordset($sql);
		$row_rs = $this->conn->FetchRow($rs);

		return $row_rs[0];
	}

	/**
	 * Check de llaves foraneas, para permitir acciones como borrar
	 * @access public
	 * @param int $id ID del registro a consultar
	 */	
	function checkForeignKeys($id){

		$tabla_rel = 'contacto_opcion_valor';

		$sql = "SELECT sum($this->columna_id) FROM $tabla_rel WHERE ".$this->columna_id." = ".$id;
		$rs = $this->conn->OpenRecordset($sql);
		$row = $this->conn->FetchRow($rs);

		$r = ($row[0] > 0) ? true : false;

		return $r;

	}
}

/**
 * Ajax
 *
 * Contiene los metodos para Ajax de la clase ContactoColOp
 * @author Ruben A. Rojas C.
 */

Class ContactoColOpAjax extends ContactoColOpDAO {

	/**
	 * Inserta un ContactoColOp en la B.D.
	 * @access public
	 * @param object $vo VO de ContactoColOp que se va a insertar
	 */
	function Insertar($vo){
		//CONSULTA SI YA EXISTE
		$obj = $this->GetAllArray($this->columna_nombre." = '".$vo->nombre."' AND id_contacto_col = $vo->id_contacto_col");
		if (count($obj) == 0){
			$sql =  "INSERT INTO ".$this->tabla." (".$this->columna_nombre.",ID_CONTACTO_COL) VALUES ('".$vo->nombre."',".$vo->id_contacto_col.")";
			$this->conn->Execute($sql);
            
            return $this->conn->GetGeneratedID();
		}
	}
}
?>
