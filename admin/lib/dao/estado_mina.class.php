<?
/**
 * DAO de EstadoMina
 *
 * Contiene los m�todos de la clase EstadoMina 
 * @author Ruben A. Rojas C.
 */

Class EstadoMinaDAO {

	/**
	 * Conexi�n a la base de datos
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
	 * Crea la conexi�n a la base de datos
	 * @access public
	 */	
	function EstadoMinaDAO (){
		$this->conn = MysqlDb::getInstance();
		$this->tabla = "estado_mina";
		$this->columna_id = "ID_ESTADO_MINA";
		$this->columna_nombre = "NOMBRE_ESTADO_MINA";
		$this->columna_order = "NOMBRE_ESTADO_MINA";
	}

	/**
	 * Consulta los datos de una EstadoMina
	 * @access public
	 * @param int $id ID del EstadoMina
	 * @return VO
	 */	
	function Get($id){
		$sql = "SELECT * FROM ".$this->tabla." WHERE ".$this->columna_id." = ".$id;
		$rs = $this->conn->OpenRecordset($sql);
		$row_rs = $this->conn->FetchObject($rs);

		//Crea un VO
		$estado_mina_vo = New EstadoMina();

		//Carga el VO
		$estado_mina_vo = $this->GetFromResult($estado_mina_vo,$row_rs);

		//Retorna el VO
		return $estado_mina_vo;
	}

	/**
	 * Consulta Vos
	 * @access public
	 * @param string $condicion Condici�n que deben cumplir los Tema y que se agrega en el SQL statement.
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
			$vo = New EstadoMina();
			//Carga el VO
			$vo = $this->GetFromResult($vo,$row_rs);
			//Carga el arreglo
			$array[] = $vo;
		}  
		//Retorna el Arreglo de VO
		return $array;
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
	 * Lista los EstadoMina que cumplen la condici�n en el formato dado
	 * @access public
	 * @param string $formato Formato en el que se listar�n los EstadoMina, puede ser Tabla o ComboSelect
	 * @param int $valor_combo ID del EstadoMina que ser� selccionado cuando el formato es ComboSelect
	 * @param string $estado Condici�n que deben cumplir los EstadoMina y que se agrega en el SQL statement.
	 */			
	function ListarCombo($formato,$valor_combo,$estado){
		$arr = $this->GetAllArray($estado);
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
	 * Lista los EstadoMina en una Tabla
	 * @access public
	 */			
	function ListarTabla($estado){

		include_once ("lib/common/layout.class.php");

		$layout = new Layout();

		$layout->adminGrid(array('nombre' => array ('titulo' => 'Nombre')));
	}

	/**
	 * Carga un VO de EstadoMina con los datos de la consulta
	 * @access public
	 * @param object $vo VO de EstadoMina que se va a recibir los datos
	 * @param object $Resultset Resource de la consulta
	 * @return object $vo VO de EstadoMina con los datos
	 */			
	function GetFromResult ($vo,$Result){

		$vo->id = $Result->{$this->columna_id};
		$vo->nombre = $Result->{$this->columna_nombre};

		return $vo;
	}

	/**
	 * Inserta un EstadoMina en la B.D.
	 * @access public
	 * @param object $estado_mina_vo VO de EstadoMina que se va a insertar
	 */		
	function Insertar($estado_mina_vo){
		//CONSULTA SI YA EXISTE
		$cat_a = $this->GetAllArray($this->columna_nombre." = '".$estado_mina_vo->nombre."'");
		if (count($cat_a) == 0){
			$sql =  "INSERT INTO ".$this->tabla." (".$this->columna_nombre.") VALUES ('".$estado_mina_vo->nombre."')";
			$this->conn->Execute($sql);
			echo "Registro insertado con &eacute;xito!";
		}
		else{
			echo "Error - Existe un registro con el mismo nombre";
		}

	}

	/**
	 * Actualiza un EstadoMina en la B.D.
	 * @access public
	 * @param object $estado_mina_vo VO de EstadoMina que se va a actualizar
	 */		
	function Actualizar($estado_mina_vo){
		$sql =  "UPDATE ".$this->tabla." SET ";
		$sql .= $this->columna_nombre." = '".$estado_mina_vo->nombre."'";
		$sql .= " WHERE ".$this->columna_id." = ".$estado_mina_vo->id;

		$this->conn->Execute($sql);

	}

	/**
	 * Borra un EstadoMina en la B.D.
	 * @access public
	 * @param int $id ID del EstadoMina que se va a borrar de la B.D
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

		$tabla_rel = 'victima';
		$col_id = 'id_estado';
		
		$sql = "SELECT sum($col_id) FROM $tabla_rel WHERE $col_id = $id";
		$rs = $this->conn->OpenRecordset($sql);
		$row = $this->conn->FetchRow($rs);

		$r = ($row[0] > 0) ? true : false;

		return $r;

	}
}

?>