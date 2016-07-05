<?
/**
 * DAO de Sector
 *
 * Contiene los métodos de la clase Sector
 * @author Ruben A. Rojas C.
 */

Class SectorDAO {

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
	function SectorDAO (){
		$this->conn = MysqlDb::getInstance();
		$this->tabla = "sector";
		$this->columna_id = "ID_COMP";
		$this->columna_nombre = "NOM_COMP_ES";
		$this->columna_order = "NOM_COMP_ES";
	}

	/**
	 * Consulta los datos de una Sector
	 * @access public
	 * @param int $id ID del Sector
	 * @return VO
	 */
	function Get($id){
		$sql = "SELECT * FROM ".$this->tabla." WHERE ".$this->columna_id." = ".$id;
		$rs = $this->conn->OpenRecordset($sql);
		$row_rs = $this->conn->FetchObject($rs);

		//Crea un VO
		$sector_vo = New Sector();

		//Carga el VO
		$sector_vo = $this->GetFromResult($sector_vo,$row_rs);

		//Retorna el VO
		return $sector_vo;
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
			$vo = New Sector();
			//Carga el VO
			$vo = $this->GetFromResult($vo,$row_rs);
			//Carga el arreglo
			$array[] = $vo;
		}  
		//Retorna el Arreglo de VO
		return $array;
	}

	/**
	 * Lista los Sector que cumplen la condición en el formato dado
	 * @access public
	 * @param string $formato Formato en el que se listarán los Sector, puede ser Tabla o ComboSelect
	 * @param int $valor_combo ID del Sector que será selccionado cuando el formato es ComboSelect
	 * @param string $condicion Condición que deben cumplir los Sector y que se agrega en el SQL statement.
	 */
	function ListarCombo($formato,$valor_combo,$condicion){
		$arr = $this->GetAllArray($condicion);
		$num_arr = count($arr);
		$v_c_a = is_array($valor_combo);

		for($a=0;$a<$num_arr;$a++){
			$vo = $arr[$a];

			if ($valor_combo == "" && $valor_combo != 0)
				echo "<option value=".$vo->id.">".$vo->nombre_es."</option>";
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

				echo ">".$vo->nombre_es."</option>";
			}
		}
	}

	/**
	 * Lista los Sector en una Tabla
	 * @access public
	 */
	function ListarTabla($condicion){

		include_once ("lib/common/layout.class.php");

		$layout = new Layout();

		$layout->adminGrid(array('nombre_es' => array ('titulo' => 'Nombre en Espa&ntilde;ol'), 'nombre_in' => array('titulo' => 'Nombre en Ingl&eacute;s'), 'def' => array('titulo' => 'Definici&oacute;n')));
	}

	/**
	 * Reportar
	 * @access public
	 */
	function Reportar(){

		$arr = $this->GetAllArray("");

		echo "<table align='center' class='tabla_lista' width='700'>
			<tr><td>&nbsp;</td></tr>
			<tr class='titulo_lista'><td align='center' colspan=4><b>SECTORES EN EL SISTEMA</b></td></tr>
			<tr><td>&nbsp;</td></tr>";

		foreach($arr as $vo){

			echo "<tr class='fila_lista'>";
			echo "<td>".$vo->nombre_es."</td>";
			echo "</tr>";
		}

		echo "</table>";
	}

	/**
	 * Carga un VO de Sector con los datos de la consulta
	 * @access public
	 * @param object $vo VO de Sector que se va a recibir los datos
	 * @param object $Resultset Resource de la consulta
	 * @return object $vo VO de Sector con los datos
	 */
	function GetFromResult ($vo,$Result){

		$vo->id = $Result->{$this->columna_id};
		$vo->nombre_es = $Result->{$this->columna_nombre};
		$vo->nombre_in = $Result->NOM_COMP_IN;
		$vo->def = $Result->DEF_COMP;

		return $vo;
	}

	/**
	 * Inserta un Sector en la B.D.
	 * @access public
	 * @param object $sector_vo VO de Sector que se va a insertar
	 */
	function Insertar($sector_vo){
		//CONSULTA SI YA EXISTE
		$cat_a = $this->GetAllArray($this->columna_nombre." = '".$sector_vo->nombre_es."'");
		if (count($cat_a) == 0){
			$sql =  "INSERT INTO ".$this->tabla." (".$this->columna_nombre.",NOM_COMP_IN,DEF_COMP) VALUES ('".$sector_vo->nombre_es."','".$sector_vo->nombre_in."','".$sector_vo->def."')";
			$this->conn->Execute($sql);

			//SE EJECUTA INSERT DIECTO
			if ($_POST["return"] == 0){
				echo "Registro insertado con &eacute;xito!";	
			}
			//SE EJECUTA INSERT DESDE IMPORT
			else{
				?>
				<script>
					location.href = 'index.php?m_e=org&accion=importar';
				</script>
				<?
			}
		}
		else{
			echo "Error - Existe un registro con el mismo nombre";
		}
	}

	/**
	 * Actualiza un Sector en la B.D.
	 * @access public
	 * @param object $sector_vo VO de Sector que se va a actualizar
	 */
	function Actualizar($sector_vo){
		$sql =  "UPDATE ".$this->tabla." SET ";
		$sql .= $this->columna_nombre." = '".$sector_vo->nombre_es."',";
		$sql .= "NOM_COMP_IN = '".$sector_vo->nombre_in."',";
		$sql .= "DEF_COMP = '".$sector_vo->def."'";

		$sql .= " WHERE ".$this->columna_id." = ".$sector_vo->id;

		$this->conn->Execute($sql);

	}

	/**
	 * Borra un Sector en la B.D.
	 * @access public
	 * @param int $id ID del Sector que se va a borrar de la B.D
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

		$tabla_rel = 'sector_org';

		$sql = "SELECT sum($this->columna_id) FROM $tabla_rel WHERE ".$this->columna_id." = ".$id;
		$rs = $this->conn->OpenRecordset($sql);
		$row = $this->conn->FetchRow($rs);

		$r = ($row[0] > 0) ? true : false;

		return $r;

	}
}

?>
