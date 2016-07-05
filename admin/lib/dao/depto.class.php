<?
/**
 * DAO de Depto
 *
 * Contiene los métodos de la clase Depto
 * @author Ruben A. Rojas C.
 */

Class DeptoDAO {

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
	function DeptoDAO (){
		//$this->conn = MysqlDb::getInstance();
		$this->conn = MysqlDb::getInstance();
		$this->tabla = "departamento";
		$this->columna_id = "ID_DEPTO";
		$this->columna_nombre = "NOM_DEPTO";
		$this->columna_order = "NOM_DEPTO";
		$this->num_reg_pag = 20;
		$this->url = "index.php?accion=listar&class=DeptoDAO&method=ListarTabla&param=";
	}

	/**
	 * Consulta los datos de una Depto
	 * @access public
	 * @param int $id ID del Depto
	 * @return VO
	 */
	function Get($id){
		$sql = "SELECT * FROM ".$this->tabla." WHERE ".$this->columna_id." = '$id'";
		$rs = $this->conn->OpenRecordset($sql);
		$row_rs = $this->conn->FetchObject($rs);

		//Crea un VO
		$depto_vo = New Depto();

		//Carga el VO
		$depto_vo = $this->GetFromResult($depto_vo,$row_rs);

		//Retorna el VO
		return $depto_vo;
	}

    /**
     * Consulta el nombre
     * @access public
     * @param int $id ID
     * @return string
     */
    function GetName($id){
        $sql = "SELECT ".$this->columna_nombre." FROM ".$this->tabla." WHERE ".$this->columna_id." = '$id'";
        $rs = $this->conn->OpenRecordset($sql);
        $row_rs = $this->conn->FetchRow($rs);

        //Retorna el VO
        return $row_rs[0];
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
			$vo = New Depto();
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
	 * Lista los Depto que cumplen la condición en el formato dado
	 * @access public
	 * @param string $formato Formato en el que se listarán los Depto, puede ser Tabla o ComboSelect
	 * @param int $valor_combo ID del Depto que será selccionado cuando el formato es ComboSelect
	 * @param string $condicion Condición que deben cumplir los Depto y que se agrega en el SQL statement.
	 */
	function ListarCombo($formato,$valor_combo,$condicion){
		$arr = $this->GetAllArray($condicion);
		$num_arr = count($arr);

		for($a=0;$a<$num_arr;$a++){
			$this->Imprimir($arr[$a],$formato,$valor_combo);
		}
	}

	/**
	 * Lista los Deptos en una Tabla
	 * @access public
	 */
	function ListarTabla($condicion){

		include_once ("lib/common/layout.class.php");

		$layout = new Layout();

		$layout->adminGrid(array('nombre' => array('titulo' => 'Nombre')),
						   array('id_pais' => array('dao' => 'PaisDao', 'nom' => 'nombre', 'titulo' => 'pais', 'filtro' => true))
						   );

	}


	/**
	 * Imprime en pantalla los datos del Depto
	 * @access public
	 * @param object $vo Depto que se va a imprimir
	 * @param string $formato Formato en el que se listarán los Depto, puede ser Tabla o ComboSelect
	 * @param int $valor_combo ID del Depto que será selccionado cuando el formato es ComboSelect
	 */
	function Imprimir($vo,$formato,$valor_combo){

		$v_c_a = is_array($valor_combo);

		if ($formato == 'combo'){
			if ($valor_combo == "" && $valor_combo != 0)
				echo "<option value='".$vo->id."'>".$vo->nombre."</option>";
			else{
				echo "<option value='".$vo->id."'";

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
	 * Carga un VO de Depto con los datos de la consulta
	 * @access public
	 * @param object $vo VO de Depto que se va a recibir los datos
	 * @param object $Resultset Resource de la consulta
	 * @return object $vo VO de Depto con los datos
	 */
	function GetFromResult ($vo,$Result){

		$vo->id = $Result->{$this->columna_id};
		$vo->nombre = $Result->{$this->columna_nombre};
		$vo->id_pais = $Result->ID_PAIS;
		$vo->centroide_dd = $Result->CENTROIDE_DD;
		$vo->extent_dd = $Result->EXTENT_DD;

		return $vo;
	}

	/**
	 * Inserta un Depto en la B.D.
	 * @access public
	 * @param object $vo VO de Depto que se va a insertar
	 */
	function Insertar($vo){

		//CONSULTA SI YA EXISTE
		$vos = $this->GetAllArray($this->columna_id." = '$vo->id' OR ".$this->columna_nombre." = '".$vo->nombre."'");
		if (count($vos) == 0){
			$sql =  "INSERT INTO ".$this->tabla." (".$this->columna_id.",".$this->columna_nombre.",ID_PAIS) VALUES ('".$vo->id."','".$vo->nombre."','".$vo->id_pais."')";
			$this->conn->Execute($sql);

			echo "Registro insertado con &eacute;xito!";
		}
		else{
			echo "Error - Existe un registro con el mismo ID o nombre";
		}
	}

	/**
	 * Actualiza un Depto en la B.D.
	 * @access public
	 * @param object $u_d_s_vo VO de Depto que se va a actualizar
	 */
	function Actualizar($vo){
		$sql =  "UPDATE ".$this->tabla." SET ";
		$sql .= $this->columna_id." = '".$vo->id."',";
		$sql .= $this->columna_nombre." = '".$vo->nombre."',";
		$sql .= "ID_PAIS = '".$vo->id_pais."'";
		$sql .= " WHERE ".$this->columna_id." = '".$vo->id."'";

		$this->conn->Execute($sql);

	}

	/**
	 * Borra un Depto en la B.D.
	 * @access public
	 * @param int $id ID del Depto que se va a borrar de la B.D
	 */
	function Borrar($id){

		//BORRA
		$sql = "DELETE FROM ".$this->tabla." WHERE ".$this->columna_id." = '".$id."'";
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

		$tabla_rel = 'municipio';

		$sql = "SELECT sum($this->columna_id) FROM $tabla_rel WHERE ".$this->columna_id." = ".$id;
		$rs = $this->conn->OpenRecordset($sql);
		$row = $this->conn->FetchRow($rs);

		$r = ($row[0] > 0) ? true : false;

		return $r;

	}

}

?>
