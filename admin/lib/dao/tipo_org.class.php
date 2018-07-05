<?
/**
 * DAO de TipoOrganizacion
 *
 * Contiene los métodos de la clase TipoOrganizacion
 * @author Ruben A. Rojas C.
 */

Class TipoOrganizacionDAO {

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
	function TipoOrganizacionDAO (){
		$this->conn = MysqlDb::getInstance();
		$this->tabla = "tipo_org";
		$this->columna_id = "ID_TIPO";
		$this->columna_nombre = "NOMB_TIPO_ES";
		$this->columna_order = "NOMB_TIPO_ES";
	}

	/**
	 * Consulta los datos de una TipoOrganizacion
	 * @access public
	 * @param int $id ID del TipoOrganizacion
	 * @return VO
	 */
	function Get($id){
		$sql = "SELECT * FROM ".$this->tabla." WHERE ".$this->columna_id." = ".$id;
		$rs = $this->conn->OpenRecordset($sql);
		$row_rs = $this->conn->FetchObject($rs);

		//Crea un VO
		$tipo_org_vo = New TipoOrganizacion();

		//Carga el VO
		$tipo_org_vo = $this->GetFromResult($tipo_org_vo,$row_rs);

		//Retorna el VO
		return $tipo_org_vo;
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
			$vo = New TipoOrganizacion();
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
	 * Lista los TipoOrganizacion que cumplen la condición en el formato dado
	 * @access public
	 * @param string $formato Formato en el que se listarán los TipoOrganizacion, puede ser Tabla o ComboSelect
	 * @param int $valor_combo ID del TipoOrganizacion que será selccionado cuando el formato es ComboSelect
	 * @param string $condicion Condición que deben cumplir los TipoOrganizacion y que se agrega en el SQL statement.
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
	 * Lista los TipoOrganizacion en una Tabla
	 * @access public
	 */
	function ListarTabla($condicion){

		include_once ("lib/common/layout.class.php");

		$layout = new Layout();

		$layout->adminGrid(array('nombre_es' => array ('titulo' => 'N-Nombre en Espa&ntilde;ol'), 'nombre_in' => array('titulo' => 'Nombre en Ingl&eacute;s')));

	}

	/**
	 * Reportar
	 * @access public
	 */
	function Reportar(){

		$arr = $this->GetAllArray("");

		echo "<table align='center' width='500' cellspacing='1' cellpadding='3'>
			<tr><td>&nbsp;</td></tr>
			<tr class='titulo_lista'><td align='center' colspan=4><b>TIPO DE ORGANIZACIONES EN EL SISTEMA</b></td></tr>
			<tr><td>&nbsp;</td></tr>";

		$v = 0;
		foreach($arr as $vo){

			$style = "";
			if (fmod($v+1,2) == 0)  $style = "fila_lista";
			echo "<tr class='".$style."'>";
			echo "<td>".$vo->nombre_es."</td>";
			echo "</tr>";
			$v++;
		}

		echo "</table>";
	}

	/**
	 * Carga un VO de TipoOrganizacion con los datos de la consulta
	 * @access public
	 * @param object $vo VO de TipoOrganizacion que se va a recibir los datos
	 * @param object $Resultset Resource de la consulta
	 * @return object $vo VO de TipoOrganizacion con los datos
	 */
	function GetFromResult ($vo,$Result){


		$vo->id = $Result->{$this->columna_id};
		$vo->nombre_es = $Result->{$this->columna_nombre};
		$vo->nombre_in = $Result->NOMB_TIPO_IN;

		return $vo;
	}

	/**
	 * Inserta un TipoOrganizacion en la B.D.
	 * @access public
	 * @param object $tipo_org_vo VO de TipoOrganizacion que se va a insertar
	 */
	function Insertar($tipo_org_vo){
		//CONSULTA SI YA EXISTE
		$cat_a = $this->GetAllArray($this->columna_nombre." = '".$tipo_org_vo->nombre_es."'");
		if (count($cat_a) == 0){
			$sql =  "INSERT INTO ".$this->tabla." (".$this->columna_nombre.",NOMB_TIPO_IN) VALUES ('".$tipo_org_vo->nombre_es."','".$tipo_org_vo->nombre_in."')";
			$this->conn->Execute($sql);

			echo "Registro insertado con &eacute;xito!";
		}
		else{
			echo "Error - Existe un registro con el mismo nombre";
		}
	}

	/**
	 * Actualiza un TipoOrganizacion en la B.D.
	 * @access public
	 * @param object $tipo_org_vo VO de TipoOrganizacion que se va a actualizar
	 */
	function Actualizar($tipo_org_vo){
		$sql =  "UPDATE ".$this->tabla." SET ";
		$sql .= $this->columna_nombre." = '".$tipo_org_vo->nombre_es."',";
		$sql .= "NOMB_TIPO_IN = '".$tipo_org_vo->nombre_in."'";

		$sql .= " WHERE ".$this->columna_id." = ".$tipo_org_vo->id;

		$this->conn->Execute($sql);

	}

	/**
	 * Borra un TipoOrganizacion en la B.D.
	 * @access public
	 * @param int $id ID del TipoOrganizacion que se va a borrar de la B.D
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

		$tabla_rel = 'organizacion';

		$sql = "SELECT sum($this->columna_id) FROM $tabla_rel WHERE ".$this->columna_id." = ".$id;
		$rs = $this->conn->OpenRecordset($sql);
		$row = $this->conn->FetchRow($rs);

		$r = ($row[0] > 0) ? true : false;

		return $r;

	}
}

?>
