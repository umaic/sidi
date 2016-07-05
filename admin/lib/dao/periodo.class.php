<?
/**
 * DAO de Periodo
 *
 * Contiene los métodos de la clase Periodo
 * @author Ruben A. Rojas C.
 */

Class PeriodoDAO {

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
	function PeriodoDAO (){
		$this->conn = MysqlDb::getInstance();
		$this->tabla = "periodo";
		$this->columna_id = "CONS_PERIO";
		$this->columna_nombre = "DESC_PERIO";
		$this->columna_order = "ORDEN";
	}

	/**
	 * Consulta los datos de una Periodo
	 * @access public
	 * @param int $id ID del Periodo
	 * @return VO
	 */
	function Get($id){
		$sql = "SELECT * FROM ".$this->tabla." WHERE ".$this->columna_id." = ".$id;
		$rs = $this->conn->OpenRecordset($sql);
		$row_rs = $this->conn->FetchObject($rs);

		//Crea un VO
		$periodo_vo = New Periodo();

		//Carga el VO
		$periodo_vo = $this->GetFromResult($periodo_vo,$row_rs);

		//Retorna el VO
		return $periodo_vo;
	}


	/**
	 * Retorna los ID de los periodos n años atras
	 * @access public
	 * @param  int $n Numero de años hacia atras
	 * @return array $aaaa
	 */
	function GetIDAtras($n){

		$fecha = getdate();

		$a_actual = $fecha['year'];

		//CONSULTA LOS ID DE LOS PERIODOS QUE CORRESPONDEN A LOS ULTIMOS 10 AÑOS
		$ul = 0;
		$a = $a_actual;
		while ($ul <= $n){
			$per = $this->GetAllArray("DESC_PERIO like '%".$a."%'");

			if (count($per) > 0){
				$i = 0;
				foreach ($per as $p){
					$id_periodo[$a][$i] = $p->id;
					$i++;
				}
				$ul++;
			}

			$a--;
		}

		$id_periodo = array_reverse($id_periodo,1);

		return $id_periodo;
	}

	/**
	 * Retorna el ID de un periodo dado el nombre
	 * @access public
	 * @param  string $nombre
	 * @return int $id
	 */
	function GetIDbyNombre($nombre){

		$sql = "SELECT CONS_PERIO FROM ".$this->tabla." WHERE DESC_PERIO = '$nombre'";
		$rs = $this->conn->OpenRecordset($sql);

		if ($this->conn->RowCount($rs) > 0){
			$row_rs = $this->conn->FetchRow($rs);
			return $row_rs[0];
		}
		else{
			return 0;
		}
	}

	/**
	 * Retorna el ID de un periodo dado el año y el mes en número
	 * @access public
	 * @param  int $aaaa
	 * @param  int $mes
	 * @return int $id
	 */
	function GetIDbyMesyAAAA($mes,$aaaa){

		$mes_t = array("","Enero","Febrero","Marzo","Abril","Mayo","Junio","Julio","Agosto","Septiembre","Octubre","Noviembre","Diciembre");

		$nombre = $mes_t[$mes]." ".$aaaa;		

		return $this->GetIDbyNombre($nombre);
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
			$vo = New Periodo();
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
	 * Lista los Periodo que cumplen la condición en el formato dado
	 * @access public
	 * @param string $formato Formato en el que se listarán los Periodo, puede ser Tabla o ComboSelect
	 * @param int $valor_combo ID del Periodo que será selccionado cuando el formato es ComboSelect
	 * @param string $condicion Condición que deben cumplir los Periodo y que se agrega en el SQL statement.
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
	 * Imprime en pantalla los datos del Periodo
	 * @access public
	 * @param object $vo Periodo que se va a imprimir
	 * @param string $formato Formato en el que se listarán los Periodo, puede ser Tabla o ComboSelect
	 * @param int $valor_combo ID del Periodo que será selccionado cuando el formato es ComboSelect
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
	 * Carga un VO de Periodo con los datos de la consulta
	 * @access public
	 * @param object $vo VO de Periodo que se va a recibir los datos
	 * @param object $Resultset Resource de la consulta
	 * @return object $vo VO de Periodo con los datos
	 */
	function GetFromResult ($vo,$Result){

		$vo->id = $Result->{$this->columna_id};
		$vo->nombre = $Result->{$this->columna_nombre};

		return $vo;
	}

	/**
	 * Inserta un Periodo en la B.D.
	 * @access public
	 * @param object $periodo_vo VO de Periodo que se va a insertar
	 */
	function Insertar($periodo_vo){
		//CONSULTA SI YA EXISTE
		$cat_a = $this->GetAllArray($this->columna_nombre." = '".$periodo_vo->nombre."'");
		if (count($cat_a) == 0){

			//Siguiente orden
			$sql = "SELECT max(orden) from $this->tabla";
			$rs = $this->conn->OpenRecordset($sql);
			$row = $this->conn->FetchRow($rs);
			$orden = $row[0] + 1;

			$sql =  "INSERT INTO ".$this->tabla." (".$this->columna_nombre.",orden) VALUES ('".$periodo_vo->nombre."',$orden)";
			$this->conn->Execute($sql);

			echo "Registro insertado con &eacute;xito!";
		}
		else{
			echo "Error - Existe un registro con el mismo nombre";
		}

	}

	/**
	 * Actualiza un Periodo en la B.D.
	 * @access public
	 * @param object $periodo_vo VO de Periodo que se va a actualizar
	 */
	function Actualizar($periodo_vo){
		$sql =  "UPDATE ".$this->tabla." SET ";
		$sql .= $this->columna_nombre." = '".$periodo_vo->nombre."'";
		$sql .= " WHERE ".$this->columna_id." = ".$periodo_vo->id;

		$this->conn->Execute($sql);

	}

	/**
	 * Borra un Periodo en la B.D.
	 * @access public
	 * @param int $id ID del Periodo que se va a borrar de la B.D
	 */
	function Borrar($id){

		//BORRA
		$sql = "DELETE FROM ".$this->tabla." WHERE ".$this->columna_id." = ".$id;
		$this->conn->Execute($sql);

	}

	/**
	 * Coloca el orden de los elementos
	 * @access public
	 * @param string $lista
	 */	
	function setOrder($key)
	{
		$orden = 1;

		foreach ($key as $id) {

			$sql = "UPDATE $this->tabla SET orden = $orden WHERE $this->columna_id = $id";
			$this->conn->Execute($sql);

			$orden++;
		}
	}	
	
	/**
	 * Check de llaves foraneas, para permitir acciones como borrar
	 * @access public
	 * @param int $id ID del registro a consultar
	 */	
	function checkForeignKeys($id){

		$tabla_rel = 'registro';

		$sql = "SELECT sum(id_perio) FROM $tabla_rel WHERE id_perio = ".$id;
		$rs = $this->conn->OpenRecordset($sql);
		$row = $this->conn->FetchRow($rs);

		$r = ($row[0] > 0) ? true : false;

		return $r;

	}
}


/**
 * Ajax de Periodos de Desplazamiento
 *
 * Contiene los metodos para Ajax de la clase Periodo
 * @author Ruben A. Rojas C.
 */

Class PeriodoAjax extends PeriodoDAO {


}

?>
