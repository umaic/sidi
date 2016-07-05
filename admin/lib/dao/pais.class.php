<?
/**
 * DAO de Pais
 *
 * Contiene los métodos de la clase Pais
 * @author Ruben A. Rojas C.
 */

Class PaisDAO {

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
	function PaisDAO (){
		$this->conn = MysqlDb::getInstance();
		$this->tabla = "pais";
		$this->columna_id = "ID_PAIS";
		$this->columna_nombre = "NOM_PAIS";
		$this->columna_order = "NOM_PAIS";
		$this->num_reg_pag = 20;
		$this->url = "index.php?accion=listar&class=PaisDAO&method=ListarTabla&param=";
	}

	/**
  * Consulta los datos de una Pais
  * @access public
  * @param int $id ID del Pais
  * @return VO
  */
	function Get($id){

		$sql = "SELECT * FROM $this->tabla WHERE $this->columna_id = '$id'";
		$rs = $this->conn->OpenRecordset($sql);
		$row_rs = $this->conn->FetchObject($rs);

		//Crea un VO
		$depto_vo = New Pais();

		//Carga el VO
		$depto_vo = $this->GetFromResult($depto_vo,$row_rs);

		//Retorna el VO
		return $depto_vo;
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
			$vo = New Pais();
			//Carga el VO
			$vo = $this->GetFromResult($vo,$row_rs);
			//Carga el arreglo
			$array[] = $vo;
		}  
		//Retorna el Arreglo de VO
		return $array;
	}

	/**
	* Consulta los datos de los Pais que cumplen una condición
	* @access public
	* @param string $condicion Condición que deben cumplir los Pais y que se agrega en el SQL statement.
	* @return array Arreglo de ID
	*/
	function GetAllArrayID($condicion){
		//Crea un VO
		$vo = New Pais();
		$c = 0;
		$sql = "SELECT ".$this->columna_id." FROM ".$this->tabla."";
		if ($condicion != ""){
			$sql .= " WHERE ".$condicion;
		}
		$sql .= " ORDER BY ".$this->columna_order;

		$array = Array();

		$rs = $this->conn->OpenRecordset($sql);
		while ($row_rs = $this->conn->FetchRow($rs)){
			//Carga el arreglo
			$array[$c] = $row_rs[0];
			$c++;
		}
		//Retorna el Arreglo
		return $array;
	}

	/**
  * Lista los Pais que cumplen la condición en el formato dado
  * @access public
  * @param string $formato Formato en el que se listarán los Pais, puede ser Tabla o ComboSelect
	* @param int $valor_combo ID del Pais que será selccionado cuando el formato es ComboSelect
  * @param string $condicion Condición que deben cumplir los Pais y que se agrega en el SQL statement.
  */
	function ListarCombo($formato,$valor_combo,$condicion){
		$arr = $this->GetAllArray($condicion);
		$num_arr = count($arr);

		for($a=0;$a<$num_arr;$a++){
			$this->Imprimir($arr[$a],$formato,$valor_combo);
		}
	}

	/**
  * Lista los Paiss en una Tabla
  * @access public
  */
	function ListarTabla($condicion){
	
		include_once ("lib/common/layout.class.php");

		$layout = new Layout();

		$layout->adminGrid(array ('nombre' => array ('titulo' => 'Nombre')));
	
	}


	/**
  * Imprime en pantalla los datos del Pais
  * @access public
  * @param object $vo Pais que se va a imprimir
  * @param string $formato Formato en el que se listarán los Pais, puede ser Tabla o ComboSelect
	* @param int $valor_combo ID del Pais que será selccionado cuando el formato es ComboSelect
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
  * Carga un VO de Pais con los datos de la consulta
  * @access public
  * @param object $vo VO de Pais que se va a recibir los datos
  * @param object $Resultset Resource de la consulta
	* @return object $vo VO de Pais con los datos
  */
	function GetFromResult ($vo,$Result){
		
		$vo->id = $Result->ID_PAIS;
		$vo->nombre = $Result->NOM_PAIS;

		return $vo;
	}

	/**
  * Inserta un Pais en la B.D.
  * @access public
  * @param object $vo VO de Pais que se va a insertar
  */
	function Insertar($vo){
		//CONSULTA SI YA EXISTE
		$cat_a = $this->GetAllArray($this->columna_nombre." = '".$vo->nombre."'");
		if (count($cat_a) == 0){
			$sql =  "INSERT INTO ".$this->tabla." (".$this->columna_id.",".$this->columna_nombre.") VALUES ('".$vo->id."','".$vo->nombre."')";
			$this->conn->Execute($sql);
			echo "Registro insertado con &eacute;xito!";
		}
		else{
			echo "Error - Existe un registro con el mismo nombre";
		}
	}

	/**
  * Actualiza un Pais en la B.D.
  * @access public
  * @param object $u_d_s_vo VO de Pais que se va a actualizar
  */
	function Actualizar($vo){
		$sql =  "UPDATE ".$this->tabla." SET ";
		$sql .= $this->columna_id." = '".$vo->id."',";
		$sql .= $this->columna_nombre." = '".$vo->nombre."'";
		$sql .= " WHERE ".$this->columna_id." = '".$vo->id."'";

		$this->conn->Execute($sql);

	}

	/**
  * Borra un Pais en la B.D.
  * @access public
  * @param int $id ID del Pais que se va a borrar de la B.D
  */
	function Borrar($id){

		//BORRA
		$sql = "DELETE FROM ".$this->tabla." WHERE ".$this->columna_id." = '".$id."'";
		$this->conn->Execute($sql);

	}


	/**
	 * Check de llaves foraneas, para permitir acciones como borrar
	 * @access public
	 * @param int $id ID del registro a consultar
	 */	
	function checkForeignKeys($id){

		$tabla_rel = 'departamento';

		$sql = "SELECT sum(id_depto) FROM $tabla_rel WHERE ".$this->columna_id." = '$id'";
		$rs = $this->conn->OpenRecordset($sql);
		$row = $this->conn->FetchRow($rs);

		$r = ($row[0] > 0) ? true : false;

		return $r;

	}
}

?>
