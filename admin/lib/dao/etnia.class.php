<?
/**
 * DAO de Etnia
 *
 * Contiene los métodos de la clase Etnia 
 * @author Ruben A. Rojas C.
 */

Class EtniaDAO {

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
	function EtniaDAO (){
		$this->conn = MysqlDb::getInstance();
		$this->tabla = "etnia";
		$this->columna_id = "ID_ETNIA";
		$this->columna_nombre = "DESC_ETNIA";
		$this->columna_order = "DESC_ETNIA";
	}

	/**
	 * Consulta los datos de una Etnia
	 * @access public
	 * @param int $id ID del Etnia
	 * @return VO
	 */	
	function Get($id){
		$sql = "SELECT * FROM ".$this->tabla." WHERE ".$this->columna_id." = ".$id;
		$rs = $this->conn->OpenRecordset($sql);
		$row_rs = $this->conn->FetchObject($rs);

		//Crea un VO
		$etnia_vo = New Etnia();

		//Carga el VO
		$etnia_vo = $this->GetFromResult($etnia_vo,$row_rs);

		//Retorna el VO
		return $etnia_vo;
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
			$vo = New Etnia();
			//Carga el VO
			$vo = $this->GetFromResult($vo,$row_rs);
			//Carga el arreglo
			$array[] = $vo;
		}  
		//Retorna el Arreglo de VO
		return $array;
	}

	/**
	 * Lista los Etnia que cumplen la condición en el formato dado
	 * @access public
	 * @param string $formato Formato en el que se listarán los Etnia, puede ser Tabla o ComboSelect
	 * @param int $valor_combo ID del Etnia que será selccionado cuando el formato es ComboSelect
	 * @param string $condicion Condición que deben cumplir los Etnia y que se agrega en el SQL statement.
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
	 * Lista los Etnia en una Tabla
	 * @access public
	 */			
	function ListarTabla($condicion){

		include_once ("lib/common/layout.class.php");

		$layout = new Layout();

		$layout->adminGrid(array('nombre' => array ('titulo' => 'Nombre')));
	}

	/**
	 * Carga un VO de Etnia con los datos de la consulta
	 * @access public
	 * @param object $vo VO de Etnia que se va a recibir los datos
	 * @param object $Resultset Resource de la consulta
	 * @return object $vo VO de Etnia con los datos
	 */			
	function GetFromResult ($vo,$Result){

		$vo->id = $Result->{$this->columna_id};
		$vo->nombre = $Result->{$this->columna_nombre};

		return $vo;
	}

	/**
	 * Inserta un Etnia en la B.D.
	 * @access public
	 * @param object $etnia_vo VO de Etnia que se va a insertar
	 */		
	function Insertar($etnia_vo){
		//CONSULTA SI YA EXISTE
		$cat_a = $this->GetAllArray($this->columna_nombre." = '".$etnia_vo->nombre."'");
		if (count($cat_a) == 0){
			$sql =  "INSERT INTO ".$this->tabla." (".$this->columna_nombre.") VALUES ('".$etnia_vo->nombre."')";
			$this->conn->Execute($sql);

			// Lo crea en MONITOR
            $sql_monitor = "INSERT INTO victim_ethnic_group (ethnic_group) VALUES ('".$etnia_vo->nombre."')";
            $my_monitor = mysqli_connect('192.168.1.3','sissh','mjuiokm2017','violencia_armada');
            mysqli_real_query($my_monitor,$sql_monitor);

			echo "Registro insertado con &eacute;xito!";
		}
		else{
			echo "Error - Existe un registro con el mismo nombre";
		}

	}

	/**
	 * Actualiza un Etnia en la B.D.
	 * @access public
	 * @param object $etnia_vo VO de Etnia que se va a actualizar
	 */		
	function Actualizar($etnia_vo){
		$sql =  "UPDATE ".$this->tabla." SET ";
		$sql .= $this->columna_nombre." = '".$etnia_vo->nombre."'";
		$sql .= " WHERE ".$this->columna_id." = ".$etnia_vo->id;

		$this->conn->Execute($sql);

	}

	/**
	 * Borra un Etnia en la B.D.
	 * @access public
	 * @param int $id ID del Etnia que se va a borrar de la B.D
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

		$tabla_rel = 'sub_etnia';
		$col_id = $this->columna_id;
		
		$sql = "SELECT sum($col_id) FROM $tabla_rel WHERE $col_id = $id";
		$rs = $this->conn->OpenRecordset($sql);
		$row = $this->conn->FetchRow($rs);

		$r = ($row[0] > 0) ? true : false;

		return $r;

	}
}

?>
