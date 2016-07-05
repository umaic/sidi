<?
/**
 * DAO de UnidadDatoSector
 *
 * Contiene los métodos de la clase UnidadDatoSector 
 * @author Ruben A. Rojas C.
 */

Class UnidadDatoSectorDAO {

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
	function UnidadDatoSectorDAO (){
		$this->conn = MysqlDb::getInstance();
		$this->tabla = "unidad_dato";
		$this->columna_id = "ID_UNIDAD";
		$this->columna_nombre = "NOM_UNIDAD";
		$this->columna_order = "NOM_UNIDAD";
	}

	/**
	 * Consulta los datos de una UnidadDatoSector
	 * @access public
	 * @param int $id ID del UnidadDatoSector
	 * @return VO
	 */	
	function Get($id){
		$sql = "SELECT * FROM ".$this->tabla." WHERE ".$this->columna_id." = ".$id;
		$rs = $this->conn->OpenRecordset($sql);
		$row_rs = $this->conn->FetchObject($rs);

		//Crea un VO
		$u_d_s_vo = New UnidadDatoSector();

		//Carga el VO
		$u_d_s_vo = $this->GetFromResult($u_d_s_vo,$row_rs);

		//Retorna el VO
		return $u_d_s_vo;
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
		
		$c = 0;
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
			$vo = New UnidadDatoSector();
			//Carga el VO
			$vo = $this->GetFromResult($vo,$row_rs);
			//Carga el arreglo
			$array[] = $vo;
		}  
		//Retorna el Arreglo de VO
		return $array;
	}

	/**
	 * Lista los UnidadDatoSector que cumplen la condición en el formato dado
	 * @access public
	 * @param string $formato Formato en el que se listarán los UnidadDatoSector, puede ser Tabla o ComboSelect
	 * @param int $valor_combo ID del UnidadDatoSector que será selccionado cuando el formato es ComboSelect
	 * @param string $condicion Condición que deben cumplir los UnidadDatoSector y que se agrega en el SQL statement.
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

		$layout->adminGrid(array('nombre' => array ('titulo' => 'Nombre')));
	}

	/**
	 * Imprime en pantalla los datos del UnidadDatoSector
	 * @access public
	 * @param object $vo UnidadDatoSector que se va a imprimir
	 * @param string $formato Formato en el que se listarán los UnidadDatoSector, puede ser Tabla o ComboSelect
	 * @param int $valor_combo ID del UnidadDatoSector que será selccionado cuando el formato es ComboSelect
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
	 * Carga un VO de UnidadDatoSector con los datos de la consulta
	 * @access public
	 * @param object $vo VO de UnidadDatoSector que se va a recibir los datos
	 * @param object $Resultset Resource de la consulta
	 * @return object $vo VO de UnidadDatoSector con los datos
	 */			
	function GetFromResult ($vo,$Result){
		
		$vo->id = $Result->{$this->columna_id};
		$vo->nombre = $Result->{$this->columna_nombre};

		return $vo;
	}

	/**
	 * Inserta un UnidadDatoSector en la B.D.
	 * @access public
	 * @param object $u_d_s_vo VO de UnidadDatoSector que se va a insertar
	 */		
	function Insertar($u_d_s_vo){
		//CONSULTA SI YA EXISTE
		$cat_a = $this->GetAllArray($this->columna_nombre." = '".$u_d_s_vo->nombre."'");
		if (count($cat_a) == 0){
			$sql =  "INSERT INTO ".$this->tabla." (".$this->columna_nombre.") VALUES ('".$u_d_s_vo->nombre."')";
			$this->conn->Execute($sql);

			echo "Registro insertado con &eacute;xito!";
		}
		else{
			echo "Error - Existe un registro con el mismo nombre";
		}

	}

	/**
	 * Actualiza un UnidadDatoSector en la B.D.
	 * @access public
	 * @param object $u_d_s_vo VO de UnidadDatoSector que se va a actualizar
	 */		
	function Actualizar($u_d_s_vo){
		$sql =  "UPDATE ".$this->tabla." SET ";
		$sql .= $this->columna_nombre." = '".$u_d_s_vo->nombre."'";
		$sql .= " WHERE ".$this->columna_id." = ".$u_d_s_vo->id;

		$this->conn->Execute($sql);

	}

	/**
	 * Borra un UnidadDatoSector en la B.D.
	 * @access public
	 * @param int $id ID del UnidadDatoSector que se va a borrar de la B.D
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

		$tabla_rel = 'dato_sector';

		$sql = "SELECT sum($this->columna_id) FROM $tabla_rel WHERE ".$this->columna_id." = ".$id;
		$rs = $this->conn->OpenRecordset($sql);
		$row = $this->conn->FetchRow($rs);

		$r = ($row[0] > 0) ? true : false;

		return $r;

	}
}

?>
