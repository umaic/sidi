<?php
/**
 * DAO de Donante
 *
 * Contiene los métodos de la clase Donante 
 * @author Ruben A. Rojas C.
 */

Class UnicefDonanteDAO {

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
	function UnicefDonanteDAO (){
		$this->conn = MysqlDb::getInstance();
		$this->tabla = "unicef_donante";
		$this->columna_id = "id_donante";
		$this->columna_nombre = "nombre";
		$this->columna_order = "nombre";
	}

	/**
	 * Consulta los datos de un Donante
	 * @access public
	 * @param int $id ID del Donante
	 * @return VO
	 */	
	function Get($id){
		$sql = "SELECT * FROM ".$this->tabla." WHERE ".$this->columna_id." = ".$id;
		$rs = $this->conn->OpenRecordset($sql);
		$row_rs = $this->conn->FetchObject($rs);

		//Crea un VO
		$vo = New UnicefDonante();

		//Carga el VO
		$vo = $this->GetFromResult($vo,$row_rs);

		//Retorna el VO
		return $vo;
	}

	/**
	 * Consulta Vos
	 * @access public
	 * @param string $condicion Condición que deben cumplir los Tema y que se agrega en el SQL statement.
	 * @param string $limit Limit en el SQL
	 * @param string $order by Order by en el SQL 
	 * @return array Arreglo de VOs
	 */	
	function GetAllArray($condicion,$limit='',$order_by=''){

		$sql = "SELECT * FROM ".$this->tabla;

		if ($condicion != "") $sql .= " WHERE ".$condicion;

		//ORDER
		$sql .= ($order_by != "") ?  " ORDER BY $order_by" : " ORDER BY ".$this->columna_order;

		//LIMIT
		if ($limit != "") $sql .= " LIMIT ".$limit;
		$array = Array();

		$rs = $this->conn->OpenRecordset($sql);
		while ($row_rs = $this->conn->FetchObject($rs)){
			//Crea un VO
			$vo = New UnicefDonante();
			//Carga el VO
			$vo = $this->GetFromResult($vo,$row_rs);
			//Carga el arreglo
			$array[] = $vo;
		}  
		//Retorna el Arreglo de VO
		return $array;
	}

	/**
	 * Consulta los datos de los Depto que cumplen una condición
	 * @access public
	 * @param string $condicion Condición que deben cumplir los Depto y que se agrega en el SQL statement.
	 * @return array Arreglo de ID
	 */
	function GetAllArrayID($condicion){

		$sql = "SELECT ".$this->columna_id." FROM ".$this->tabla."";

		if ($condicion != "") $sql .= " WHERE ".$condicion;

		$sql .= " ORDER BY ".$this->columna_order;

		$array = Array();

		$rs = $this->conn->OpenRecordset($sql);
		while ($row_rs = $this->conn->FetchRow($rs)){
			//Carga el arreglo
			$array[] = $row_rs[0];
		}

		//Retorna el Arreglo
		return $array;
	}

	/**
	 * Consulta las letras iniciales de Donantes existentes
	 * @access public
	 * @return array Arreglo de letras
	 */
	function getLetrasIndice(){
        
        $indice = array();

        $sql = "SELECT DISTINCT(LEFT($this->columna_nombre,1)) FROM $this->tabla ORDER By $this->columna_order";
        $rs = $this->conn->OpenRecordset($sql);
        while ($row = $this->conn->FetchRow($rs)){
            $indice[] = $row[0];
        }

        return $indice;
    }

	/**
	 * Lista los Donante que cumplen la condición en el formato dado
	 * @access public
	 * @param string $formato Formato en el que se listarán los Donante, puede ser Tabla o ComboSelect
	 * @param int $valor_combo ID del Donante que será selccionado cuando el formato es ComboSelect
	 * @param string $condicion Condición que deben cumplir los Donante y que se agrega en el SQL statement.
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
	 * Carga un VO de Donante con los datos de la consulta
	 * @access public
	 * @param object $vo VO de Donante que se va a recibir los datos
	 * @param object $Resultset Resource de la consulta
	 * @return object $vo VO de Donante con los datos
	 */			
	function GetFromResult ($vo,$Result){

		$vo->id = $Result->{$this->columna_id};
		$vo->nombre = $Result->{$this->columna_nombre};
		$vo->codigo = $Result->codigo;
	
        return $vo;
	}

	/**
	 * Lista los Moneda en una Tabla
	 * @access public
	 */			
	function ListarTabla($condicion){

		include_once ($_SERVER['DOCUMENT_ROOT']."/sissh/admin/lib/common/layout.class.php");

		$layout = new Layout();

		//$layout->adminGrid(array ('codigo' => array('titulo'=>'C&oacute;digo'), 'nombre' => array ('titulo' => 'Nombre')));
		$layout->adminGrid(array ('nombre' => array ('titulo' => 'Nombre'), 'codigo' => array('titulo'=>'C&oacute;digo')));
	}

	/**
	 * Inserta un Donante en la B.D.
	 * @access public
	 * @param object $vo VO de Donante que se va a insertar
	 */		
	function Insertar($vo){
		//CONSULTA SI YA EXISTE
		$a = $this->GetAllArray($this->columna_nombre." = '".$vo->nombre."'");
		if (count($a) == 0){
			$sql =  "INSERT INTO ".$this->tabla." (".$this->columna_nombre.",codigo) VALUES ('".$vo->nombre."','".$vo->codigo."')";
			$this->conn->Execute($sql);
			echo "Registro insertado con &eacute;xito!";
		}
		else{
			echo "Error - Existe una Fuente con el mismo nombre";
		}

	}

	/**
	 * Actualiza un Donante en la B.D.
	 * @access public
	 * @param object $vo VO de Donante que se va a actualizar
	 */		
	function Actualizar($vo){
		$sql =  "UPDATE ".$this->tabla." SET ";
		$sql .= $this->columna_nombre." = '".$vo->nombre."',";
		$sql .= "codigo = '".$vo->codigo."'";
		$sql .= " WHERE ".$this->columna_id." = ".$vo->id;

		$this->conn->Execute($sql);

	}

	/**
	 * Borra un Donante en la B.D.
	 * @access public
	 * @param int $id ID del Donante que se va a borrar de la B.D
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

		$tabla_rel = 'unicef_producto_awp_donante';
		$col_id = $this->columna_id;
		
		$sql = "SELECT count($col_id) FROM $tabla_rel WHERE $col_id = $id UNION SELECT count($col_id) FROM unicef_convenio_avance_donante WHERE $col_id = $id";
		$rs = $this->conn->OpenRecordset($sql);
		$row = $this->conn->FetchRow($rs);

		$r = ($row[0] > 0) ? true : false;

		return $r;

	}
}

?>
