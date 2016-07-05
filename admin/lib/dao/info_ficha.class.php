<?
/**
 * DAO de InfoFicha
 *
 * Contiene los métodos de la clase InfoFicha 
 * @author Ruben A. Rojas C.
 */

Class InfoFichaDAO {

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
	 * Número de Registros en Pantalla para ListarTAbla
	 * @var string
	 */
	var $num_reg_pag;

	/**
	 * Constructor
	 * Crea la conexión a la base de datos
	 * @access public
	 */	
	function InfoFichaDAO (){
		$this->conn = MysqlDb::getInstance();
		$this->tabla = "info_ficha_grafica";
		$this->columna_id = "ID_FICHA";
	}

	/**
	 * Consulta los datos de los InfoFicha que cumplen una condición
	 * @access public
	 * @param string $condicion Condición que deben cumplir los InfoFicha y que se agrega en el SQL statement.
	 * @return array Arreglo de VOs
	 */	
	function GetAllArray($condicion){
		$c = 0;
		$sql = "SELECT * FROM ".$this->tabla."";
		if ($condicion != ""){
			$sql .= " WHERE ".$condicion;
		}
		$rs = $this->conn->OpenRecordset($sql);
		$row_rs = $this->conn->FetchObject($rs);
		//Crea un VO
		$vo = New InfoFicha();
		//Carga el VO
		$vo = $this->GetFromResult($vo,$row_rs);

		//Retorna el Arreglo de VO
		return $vo;
	}

	/**
	 * Carga un VO de InfoFicha con los datos de la consulta
	 * @access public
	 * @param object $vo VO de InfoFicha que se va a recibir los datos
	 * @param object $Resultset Resource de la consulta
	 * @return object $vo VO de InfoFicha con los datos
	 */			
	function GetFromResult ($vo,$Result){

		$vo->texto = $Result->HTML_TEXT;

		return $vo;
	}


	/**
	 * Actualiza un InfoFicha en la B.D.
	 * @access public
	 * @param object $info_ficha_vo VO de InfoFicha que se va a actualizar
	 */		
	function Actualizar($info_ficha_vo){
		$sql =  "UPDATE ".$this->tabla." SET ";
		$sql .= "html_text = '".$info_ficha_vo->texto."'";
		$sql .= " WHERE modulo = '".$info_ficha_vo->modulo."'";
		
		$this->conn->Execute($sql);
	}

}

?>
