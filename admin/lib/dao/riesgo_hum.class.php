<?
/**
 * DAO de RiesgoHum
 *
 * Contiene los métodos de la clase RiesgoHum 
 * @author Ruben A. Rojas C.
 */

Class RiesgoHumDAO {

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
	function RiesgoHumDAO (){
		$this->conn = MysqlDb::getInstance();
		$this->tabla = "riesg_hum";
		$this->columna_id = "ID_RIESG_HUM";
		$this->columna_nombre = "NOM_RIESG_HUM";
		$this->columna_order = "NOM_RIESG_HUM";
	}

	/**
	 * Consulta los datos de una RiesgoHum
	 * @access public
	 * @param int $id ID del RiesgoHum
	 * @return VO
	 */	
	function Get($id){
		$sql = "SELECT * FROM ".$this->tabla." WHERE ".$this->columna_id." = ".$id;
		$rs = $this->conn->OpenRecordset($sql);
		$row_rs = $this->conn->FetchObject($rs);

		//Crea un VO
		$riesgo_hum_vo = New RiesgoHum();

		//Carga el VO
		$riesgo_hum_vo = $this->GetFromResult($riesgo_hum_vo,$row_rs);

		//Retorna el VO
		return $riesgo_hum_vo;
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
			$vo = New RiesgoHum();
			//Carga el VO
			$vo = $this->GetFromResult($vo,$row_rs);
			//Carga el arreglo
			$array[] = $vo;
		}  
		//Retorna el Arreglo de VO
		return $array;
	}

	/**
	 * Lista los RiesgoHum que cumplen la condición en el formato dado
	 * @access public
	 * @param string $formato Formato en el que se listarán los RiesgoHum, puede ser Tabla o ComboSelect
	 * @param int $valor_combo ID del RiesgoHum que será selccionado cuando el formato es ComboSelect
	 * @param string $condicion Condición que deben cumplir los RiesgoHum y que se agrega en el SQL statement.
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
	 * Reportar
	 * @access public
	 */			
	function Reportar(){

		$arr = $this->GetAllArray("");

		echo "<table align='center' width='500' cellspacing='1' cellpadding='3'>
			<tr><td>&nbsp;</td></tr>
			<tr class='titulo_lista'><td align='center' colspan=4><b>RIESGOS HUMANITARIOS EN EL SISTEMA</b></td></tr>
			<tr><td>&nbsp;</td></tr>";

		$v = 0;
		foreach($arr as $vo){

			$style = "";
			if (fmod($v+1,2) == 0)  $style = "fila_lista";
			echo "<tr class='".$style."'>";
			echo "<td>".$vo->nombre."</td>";
			echo "</tr>";
			$v++;
		}

		echo "</table>";
	}

	/**
	 * Imprime en pantalla los datos del RiesgoHum
	 * @access public
	 * @param object $vo RiesgoHum que se va a imprimir
	 * @param string $formato Formato en el que se listarán los RiesgoHum, puede ser Tabla o ComboSelect
	 * @param int $valor_combo ID del RiesgoHum que será selccionado cuando el formato es ComboSelect
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
	 * Carga un VO de RiesgoHum con los datos de la consulta
	 * @access public
	 * @param object $vo VO de RiesgoHum que se va a recibir los datos
	 * @param object $Resultset Resource de la consulta
	 * @return object $vo VO de RiesgoHum con los datos
	 */			
	function GetFromResult ($vo,$Result){

		$vo->id = $Result->{$this->columna_id};
		$vo->nombre = $Result->{$this->columna_nombre};
		$vo->icono = $Result->ICN_RIESG_HUM;

		return $vo;
	}

	/**
	 * Inserta un RiesgoHum en la B.D.
	 * @access public
	 * @param object $riesgo_hum_vo VO de RiesgoHum que se va a insertar
	 */		
	function Insertar($riesgo_hum_vo){
		//CONSULTA SI YA EXISTE
		$cat_a = $this->GetAllArray($this->columna_nombre." = '".$riesgo_hum_vo->nombre."'");
		if (count($cat_a) == 0){
			$sql =  "INSERT INTO ".$this->tabla." (".$this->columna_nombre.",ICN_RIESG_HUM) VALUES ('".$riesgo_hum_vo->nombre."','".$riesgo_hum_vo->icono."')";
			$this->conn->Execute($sql);

			echo "Registro insertado con &eacute;xito!";
		}
		else{
			echo "Error - Existe un registro con el mismo nombre";
		}
	}

	/**
	 * Actualiza un RiesgoHum en la B.D.
	 * @access public
	 * @param object $riesgo_hum_vo VO de RiesgoHum que se va a actualizar
	 */		
	function Actualizar($riesgo_hum_vo){
		$sql =  "UPDATE ".$this->tabla." SET ";
		$sql .= $this->columna_nombre." = '".$riesgo_hum_vo->nombre."',";
		$sql .= "ICN_RIESG_HUM = '".$riesgo_hum_vo->icono."'";
		$sql .= " WHERE ".$this->columna_id." = ".$riesgo_hum_vo->id;

		$this->conn->Execute($sql);

	}

	/**
	 * Borra un RiesgoHum en la B.D.
	 * @access public
	 * @param int $id ID del RiesgoHum que se va a borrar de la B.D
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

		$tabla_rel = 'evento_riesgo';

		$sql = "SELECT sum($this->columna_id) FROM $tabla_rel WHERE ".$this->columna_id." = ".$id;
		$rs = $this->conn->OpenRecordset($sql);
		$row = $this->conn->FetchRow($rs);

		$r = ($row[0] > 0) ? true : false;

		return $r;

	}
}

?>
