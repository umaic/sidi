<?
/**
 * DAO de Poblado
 *
 * Contiene los métodos de la clase Poblado 
 * @author Ruben A. Rojas C.
 */

Class PobladoDAO {

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
	function PobladoDAO (){
		$this->conn = MysqlDb::getInstance();
		$this->tabla = "poblado";
		$this->columna_id = "ID_POB";
		$this->columna_nombre = "NOM_POB";
		$this->columna_order = "NOM_POB";
		$this->num_reg_pag = 50;
		$this->url = "index.php?accion=listar&class=PobladoDAO&method=ListarTabla&param=";

	}

	/**
  * Consulta los datos de una Poblado
  * @access public
  * @param int $id ID del Poblado
  * @return VO
  */	
	function Get($id){
		$sql = "SELECT * FROM ".$this->tabla." WHERE ".$this->columna_id." = ".$id;
		$rs = $this->conn->OpenRecordset($sql);
		$row_rs = $this->conn->FetchObject($rs);

		//Crea un VO
		$depto_vo = New Poblado();

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
  * Lista los Poblado que cumplen la condición en el formato dado
  * @access public
  * @param string $formato Formato en el que se listarán los Poblado, puede ser Tabla o ComboSelect
	* @param int $valor_combo ID del Poblado que será selccionado cuando el formato es ComboSelect
  * @param string $condicion Condición que deben cumplir los Poblado y que se agrega en el SQL statement.
  */			
	function ListarCombo($formato,$valor_combo,$condicion){
		$arr = $this->GetAllArray($condicion);
		$num_arr = count($arr);

		for($a=0;$a<$num_arr;$a++){
			$this->Imprimir($arr[$a],$formato,$valor_combo);
		}
	}

	/**
  * Lista los Poblados en una Tabla
  * @access public
  */			
	function ListarTabla($condicion){

		include_once ("lib/common/layout.class.php");
		
		$layout = new Layout();
		$depto_dao = New DeptoDAO();
		$mun_dao = New MunicipioDAO();

		$id_depto = '05';
		if (isset($_GET["id_depto"]) && $_GET["id_depto"] != ""){
			$id_depto = $_GET["id_depto"];
		}

		$id_muns = $mun_dao->GetAllArrayID('ID_DEPTO ='.$id_depto,'');

		$m = 0;
		foreach ($id_muns as $id){
			$id_muns[$m] = "'".$id_muns[$m]."'";
			$m++;
		}
		$condicion = "ID_MUN IN (".implode(",",$id_muns).")";
		
		$id_mun = 0;
		if (isset($_GET["id_mun"]) && $_GET["id_mun"] != ""){
			$id_mun = $_GET["id_mun"];

			$condicion = "ID_MUN = '".$id_mun."'";
		}

		$arr = $this->GetAllArray($condicion);

		echo "<table align='center' class='tabla_lista' width='800'>
		<tr>
	    <td colspan='6'><b>
	    Filtrar por Departamento</b>&nbsp;<select nane='id_depto' class='select' onchange=\"refreshTab('index_parser.php?m_e=poblado&accion=listar&class=".$_GET["class"]."&method=ListarTabla&param=&id_depto='+this.value)\">
			<option value=''>Todos</option>";
		$depto_dao->ListarCombo('combo',$id_depto,'');
		echo "</select></td></tr><tr><td colspan='6'>
	    <b>Filtrar por Municpio</b>&nbsp;
			<select nane='id_mun' class='select' onchange=\"refreshTab('index_parser.php?m_e=poblado&accion=listar&class=".$_GET["class"]."&method=ListarTabla&param=&id_depto=".$id_depto."&id_mun='+this.value)\">
			<option value=''>---</option>";
		$mun_dao->ListarCombo('combo',$id_mun,'ID_DEPTO = '.$id_depto);
		echo "</select></td></tr>";

		$layout->adminGridLayout($arr,array('nombre' => array('titulo' => 'Nombre'), 'clase' => array('titulo' => 'Clase'), 'nacimiento' => array('titulo' => 'A&ntilde;o de creaci&oacute;n')),
						   			array('id_mun' => array('dao' => 'MunicipioDAO', 'nom' => 'nombre', 'titulo' => 'Municipio', 'filtro' => true)),
								array()
								);

	}

	/**
  * Imprime en pantalla los datos del Poblado
  * @access public
  * @param object $vo Poblado que se va a imprimir
  * @param string $formato Formato en el que se listarán los Poblado, puede ser Tabla o ComboSelect
	* @param int $valor_combo ID del Poblado que será selccionado cuando el formato es ComboSelect
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
  * Carga un VO de Poblado con los datos de la consulta
  * @access public
  * @param object $vo VO de Poblado que se va a recibir los datos
  * @param object $Resultset Resource de la consulta
	* @return object $vo VO de Poblado con los datos
  */			
	function GetFromResult ($vo,$Result){
		
		$vo->id = $Result->{$this->columna_id};
		$vo->nombre = $Result->{$this->columna_nombre};

		$vo->id_mun = $Result->ID_MUN;
		$vo->clase = $Result->CLA_POB;
		$vo->acto_admin = $Result->ACTO_POB;
		$vo->nacimiento = $Result->NACI_POB;

		return $vo;
	}

	/**
  * Inserta un Poblado en la B.D.
  * @access public
  * @param object $vo VO de Poblado que se va a insertar
  */		
	function Insertar($vo){
		//CONSULTA SI YA EXISTE
		$cat_a = $this->GetAllArray($this->columna_nombre." = '".$vo->nombre."'");
		if (count($cat_a) == 0){
			$sql =  "INSERT INTO ".$this->tabla." (".$this->columna_id.",".$this->columna_nombre.",ID_MUN,CLA_POB,NACI_POB,ACTO_POB) VALUES ('".$vo->id."','".$vo->nombre."','".$vo->id_mun."','".$vo->clase."',".$vo->nacimiento.",'".$vo->acto_admin."')";

			$this->conn->Execute($sql);

			echo "Registro insertado con &eacute;xito!";
		}
		else{
			echo "Error - Existe un registro con el mismo nombre";
		}
}

	/**
  * Actualiza un Depto en la B.D.
  * @access public
  * @param object $vo VO de Depto que se va a actualizar
  */		
	function Actualizar($vo){
		$sql =  "UPDATE ".$this->tabla." SET ";
		$sql .= $this->columna_id." = '".$vo->id."',";
		$sql .= $this->columna_nombre." = '".$vo->nombre."',";
		$sql .= "ID_MUN = '".$vo->id_mun."',";
		$sql .= "CLA_POB = '".$vo->clase."',";
		$sql .= "NACI_POB = ".$vo->nacimiento.",";
		$sql .= "ACTO_POB = '".$vo->acto_admin."'";
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
}

?>
