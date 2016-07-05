<?
/**
 * DAO de SubCondicion
 *
 * Contiene los métodos de la clase SubCondicion 
 * @author Ruben A. Rojas C.
 */

Class SubCondicionDAO {

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
	function SubCondicionDAO (){
		$this->conn = MysqlDb::getInstance();
		$this->tabla = "sub_condicion";
		$this->columna_id = "ID_SUBCONDICION";
		$this->columna_nombre = "NOM_SUBCONDICION";
		$this->columna_order = "NOM_SUBCONDICION";
	}

	/**
	 * Consulta los datos de una SubCondicion
	 * @access public
	 * @param int $id ID del SubCondicion
	 * @return VO
	 */	
	function Get($id){
		$sql = "SELECT * FROM ".$this->tabla." WHERE ".$this->columna_id." = ".$id;
		$rs = $this->conn->OpenRecordset($sql);
		$row_rs = $this->conn->FetchObject($rs);

		//Crea un VO
		$subcondicion_vo = New SubCondicion();

		//Carga el VO
		$subcondicion_vo = $this->GetFromResult($subcondicion_vo,$row_rs);

		//Retorna el VO
		return $subcondicion_vo;
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
			$vo = New SubCondicion();
			//Carga el VO
			$vo = $this->GetFromResult($vo,$row_rs);
			//Carga el arreglo
			$array[] = $vo;
		}  
		//Retorna el Arreglo de VO
		return $array;
	}

	/**
	 * Consulta los ID de las Subcategorias que cumplen una condición
	 * @access public
	 * @param string $condicion Condición
	 * @return array Arreglo de IDs
	 */	
	function GetAllArrayID($condicion){
		$c = 0;
		$sql = "SELECT $this->columna_id FROM ".$this->tabla."";
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
		//Retorna el Arreglo de VO
		return $array;
	}

	/**
	 * Lista los SubCondicion que cumplen la condición en el formato dado
	 * @access public
	 * @param string $formato Formato en el que se listarán los SubCondicion, puede ser Tabla o ComboSelect
	 * @param int $valor_combo ID del SubCondicion que será selccionado cuando el formato es ComboSelect
	 * @param string $condicion Condición que deben cumplir los SubCondicion y que se agrega en el SQL statement.
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
	 * Lista los SubCondicion en una Tabla
	 * @access public
	 */			
	function ListarTabla($condicion){

		include_once ("lib/common/layout.class.php");

		$layout = new Layout();

		$layout->adminGrid(array('nombre' => array('titulo' => 'Nombre')),
				array('id_condicion' => array('dao' => 'CondicionMinaDao', 'nom' => 'nombre', 'titulo' => 'Condici&oacute;n', 'filtro' => true)));

	}

	/**
	 * Carga un VO de SubCondicion con los datos de la consulta
	 * @access public
	 * @param object $vo VO de SubCondicion que se va a recibir los datos
	 * @param object $Resultset Resource de la consulta
	 * @return object $vo VO de SubCondicion con los datos
	 */			
	function GetFromResult ($vo,$Result){

		$vo->id = $Result->{$this->columna_id};
		$vo->nombre = $Result->{$this->columna_nombre};
		$vo->id_condicion = $Result->ID_CONDICION;

		return $vo;
	}

	/**
	 * Inserta un SubCondicion en la B.D.
	 * @access public
	 * @param object $subcondicion_vo VO de SubCondicion que se va a insertar
	 */		
	function Insertar($subcondicion_vo){
		//CONSULTA SI YA EXISTE
		$vo_t = $this->GetAllArray($this->columna_nombre." = '".$subcondicion_vo->nombre."'");
		if (count($vo_t) == 0){
			$sql =  "INSERT INTO ".$this->tabla." (".$this->columna_nombre.",ID_CONDICION) VALUES ('".$subcondicion_vo->nombre."',$subcondicion_vo->id_condicion)";
			$this->conn->Execute($sql);
			echo "Registro insertado con &eacute;xito!";
		}
		else{
			echo "Error - Existe un registro con el mismo nombre";
		}

	}

	/**
	 * Actualiza un SubCondicion en la B.D.
	 * @access public
	 * @param object $subcondicion_vo VO de SubCondicion que se va a actualizar
	 */		
	function Actualizar($subcondicion_vo){
		$sql =  "UPDATE ".$this->tabla." SET ";
		$sql .= $this->columna_nombre." = '".$subcondicion_vo->nombre."',";
		$sql .= " ID_CONDICION = ".$subcondicion_vo->id_condicion;
		$sql .= " WHERE ".$this->columna_id." = ".$subcondicion_vo->id;

		$this->conn->Execute($sql);

	}

	/**
	 * Borra un SubCondicion en la B.D.
	 * @access public
	 * @param int $id ID del SubCondicion que se va a borrar de la B.D
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

		$tabla_rel = 'victima';
		$col_id = $this->columna_id;
		
		$sql = "SELECT sum($col_id) FROM $tabla_rel WHERE $col_id = $id";
		$rs = $this->conn->OpenRecordset($sql);
		$row = $this->conn->FetchRow($rs);

		$r = ($row[0] > 0) ? true : false;

		return $r;

	}
}

/**
 * Ajax de Subcategorias
 *
 * Contiene los metodos para Ajax de la clase SubCondicion
 * @author Ruben A. Rojas C.
 */

Class SubCondicionAjax extends SubCondicionDAO {

	/**
	 * Lista ComboBox de subcondicions
	 * @access public
	 * @param string $id_condicion ID de la Condicion
	 * @param int $multiple 0 = comboBox no es multiple, > 0 comboBox es multiple de size = $multiple
	 * @param int $titulo 1 = Mostrar titulo
	 * @param string $id_field Id para el combo
	 */
	function comboBoxSubcondicion($id_condicion,$multiple=0,$titulo=0,$separador=0,$id_field='id_subcondicion'){

		//LIBRERIAS
		include_once("lib/model/subcondicion.class.php");
		include_once("lib/model/condicion_mina.class.php");
		include_once("lib/dao/condicion_mina.class.php");

		//INICIALIZACION VARIABLES
		$condicion_dao = New CondicionMinaDAO();

		$num = $this->numRecords("id_condicion IN ($id_condicion)");

		if ($num > 0){

			$condicions = $condicion_dao->GetAllArray("ID_CONDICION_MINA IN ($id_condicion)");


			if ($titulo == 1)	echo "<b>Subcondiciones</b><br>";

			if ($multiple == 0){
				echo "<select id='$id_field' name='id_subcondicion[]' class='select'>";
				echo "<option value=0>[ Seleccione ]</option>";
			}
			else{
				//if ($num < $multiple)	$multiple = $num;
				echo "<select id='$id_field' name='id_subcondicion[]' class='select' multiple size=$multiple>";
			}


			foreach ($condicions as $condicion) {
				$vos = $this->GetAllArray("ID_CONDICION = $condicion->id");

				if ($separador == 1)	echo "<option value='' disabled>--- ".$condicion->nombre." ---</option>";
				foreach ($vos as $vo){
					echo "<option value='".$vo->id."'>".$vo->nombre."</option>";
				}
			}

			echo "</select>";
		}
		else{
			echo "<b>* No hay Info *</b><input type='hidden' name='id_subcondicion[]' value=0>";
		}
	}		
}

?>
