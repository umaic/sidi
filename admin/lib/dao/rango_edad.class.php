<?
/**
 * DAO de RangoEdad
 *
 * Contiene los métodos de la clase RangoEdad 
 * @author Ruben A. Rojas C.
 */

Class RangoEdadDAO {

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
	function RangoEdadDAO (){
		$this->conn = MysqlDb::getInstance();
		$this->tabla = "rango_edad";
		$this->columna_id = "ID_RANED";
		$this->columna_nombre = "NOM_RANED";
		$this->columna_order = "NOM_RANED";
	}

	/**
	 * Consulta los datos de una RangoEdad
	 * @access public
	 * @param int $id ID del RangoEdad
	 * @return VO
	 */	
	function Get($id){
		$sql = "SELECT * FROM ".$this->tabla." WHERE ".$this->columna_id." = ".$id;
		$rs = $this->conn->OpenRecordset($sql);
		$row_rs = $this->conn->FetchObject($rs);

		//Crea un VO
		$rango_edad_vo = New RangoEdad();

		//Carga el VO
		$rango_edad_vo = $this->GetFromResult($rango_edad_vo,$row_rs);

		//Retorna el VO
		return $rango_edad_vo;
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
			$vo = New RangoEdad();
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
	 * Lista los RangoEdad que cumplen la condición en el formato dado
	 * @access public
	 * @param string $formato Formato en el que se listarán los RangoEdad, puede ser Tabla o ComboSelect
	 * @param int $valor_combo ID del RangoEdad que será selccionado cuando el formato es ComboSelect
	 * @param string $edad Condición que deben cumplir los RangoEdad y que se agrega en el SQL statement.
	 */			
	function ListarCombo($formato,$valor_combo,$edad){
		$arr = $this->GetAllArray($edad);
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
	 * Lista los RangoEdad en una Tabla
	 * @access public
	 */			
	function ListarTabla($edad){
		
		include_once ("lib/common/layout.class.php");

		$layout = new Layout();

		$layout->adminGrid(array('nombre' => array('titulo' => 'Nombre')),
						   array('id_edad' => array('dao' => 'EdadDao', 'nom' => 'nombre', 'titulo' => 'Grupo Etareo', 'filtro' => true)));

	}

	/**
	 * Carga un VO de RangoEdad con los datos de la consulta
	 * @access public
	 * @param object $vo VO de RangoEdad que se va a recibir los datos
	 * @param object $Resultset Resource de la consulta
	 * @return object $vo VO de RangoEdad con los datos
	 */			
	function GetFromResult ($vo,$Result){

		$vo->id = $Result->{$this->columna_id};
		$vo->nombre = $Result->{$this->columna_nombre};
		$vo->id_edad = $Result->ID_EDAD;

		return $vo;
	}

	/**
	 * Inserta un RangoEdad en la B.D.
	 * @access public
	 * @param object $rango_edad_vo VO de RangoEdad que se va a insertar
	 */		
	function Insertar($rango_edad_vo){
		//CONSULTA SI YA EXISTE
		$vo_t = $this->GetAllArray($this->columna_nombre." = '".$rango_edad_vo->nombre."'");
		if (count($vo_t) == 0){
			$sql =  "INSERT INTO ".$this->tabla." (".$this->columna_nombre.",ID_EDAD) VALUES ('".$rango_edad_vo->nombre."',$rango_edad_vo->id_edad)";
			$this->conn->Execute($sql);
			echo "Registro insertado con &eacute;xito!";
		}
		else{
			echo "Error - Existe un registro con el mismo nombre";
		}

	}

	/**
	 * Actualiza un RangoEdad en la B.D.
	 * @access public
	 * @param object $rango_edad_vo VO de RangoEdad que se va a actualizar
	 */		
	function Actualizar($rango_edad_vo){
		$sql =  "UPDATE ".$this->tabla." SET ";
		$sql .= $this->columna_nombre." = '".$rango_edad_vo->nombre."',";
		$sql .= " ID_EDAD = ".$rango_edad_vo->id_edad;
		$sql .= " WHERE ".$this->columna_id." = ".$rango_edad_vo->id;

		$this->conn->Execute($sql);

	}

	/**
	 * Borra un RangoEdad en la B.D.
	 * @access public
	 * @param int $id ID del RangoEdad que se va a borrar de la B.D
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
 * Contiene los metodos para Ajax de la clase RangoEdad
 * @author Ruben A. Rojas C.
 */

Class RangoEdadAjax extends RangoEdadDAO {

	/**
	 * Lista ComboBox de subcategorias
	 * @access public
	 * @param string $id_edad ID de la Estado
	 * @param int $multiple 0 = comboBox no es multiple, > 0 comboBox es multiple de size = $multiple
	 * @param int $titulo 1 = Mostrar titulo
	 * @param string $id_field Id para el combo
	 */
	function comboBoxRangoEdad($id_edad,$multiple=0,$titulo=0,$separador=0,$id_field='id_rango_edad'){

		//LIBRERIAS
		include_once("lib/model/rango_edad.class.php");
		include_once("lib/model/edad.class.php");
		include_once("lib/dao/edad.class.php");

		//INICIALIZACION VARIABLES
		$edad_dao = New EdadDAO();

		$num = $this->numRecords("ID_EDAD IN ($id_edad)");

		if ($num > 0){

			$edads = $edad_dao->GetAllArray("ID_EDAD IN ($id_edad)");


			if ($titulo == 1)	echo "<b>Subedads</b><br>";

			if ($multiple == 0){
				echo "<select id='$id_field' name='id_rango_edad[]' class='select'>";
				echo "<option value=0>[ Seleccione ]</option>";
			}
			else{
				//if ($num < $multiple)	$multiple = $num;
				echo "<select id='$id_field' name='id_rango_edad[]' class='select' multiple size=$multiple>";
			}


			foreach ($edads as $edad) {
				$vos = $this->GetAllArray("ID_EDAD = $edad->id");

				if ($separador == 1)	echo "<option value='' disabled>--- ".$edad->nombre." ---</option>";
				foreach ($vos as $vo){
					echo "<option value='".$vo->id."'>".$vo->nombre."</option>";
				}
			}

			echo "</select>";
		}
		else{
			echo "<b>* No hay Info *</b><input type='hidden' name='id_rango_edad[]' value=0>";
		}
	}		
}

?>
