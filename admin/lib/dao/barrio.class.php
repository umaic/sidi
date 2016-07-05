<?
/**
 * DAO de Barrio
 *
 * Contiene los métodos de la clase Barrio 
 * @author Ruben A. Rojas C.
 */

Class BarrioDAO {

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
	function BarrioDAO (){
		$this->conn = MysqlDb::getInstance();
		$this->tabla = "barrio";
		$this->columna_id = "ID_BARRIO";
		$this->columna_nombre = "NOM_BARRIO";
		$this->columna_order = "NOM_BARRIO";

	}

	/**
	 * Consulta los datos de una Barrio
	 * @access public
	 * @param int $id ID del Barrio
	 * @return VO
	 */	
	function Get($id){
		$sql = "SELECT * FROM ".$this->tabla." WHERE ".$this->columna_id." = ".$id;
		$rs = $this->conn->OpenRecordset($sql);
		$row_rs = $this->conn->FetchObject($rs);

		//Crea un VO
		$depto_vo = New Barrio();

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
			$vo = New Barrio();
			//Carga el VO
			$vo = $this->GetFromResult($vo,$row_rs);
			//Carga el arreglo
			$array[] = $vo;
		}  
		//Retorna el Arreglo de VO
		return $array;
	}

	/**
	 * Lista los Barrio que cumplen la condición en el formato dado
	 * @access public
	 * @param string $formato Formato en el que se listarán los Barrio, puede ser Tabla o ComboSelect
	 * @param int $valor_combo ID del Barrio que será selccionado cuando el formato es ComboSelect
	 * @param string $condicion Condición que deben cumplir los Barrio y que se agrega en el SQL statement.
	 */			
	function ListarCombo($formato,$valor_combo,$condicion){
		$arr = $this->GetAllArray($condicion);
		$num_arr = count($arr);

		for($a=0;$a<$num_arr;$a++){
			$this->Imprimir($arr[$a],$formato,$valor_combo);
		}
	}

	/**
	 * Lista los Barrios en una Tabla
	 * @access public
	 */			
	function ListarTabla($condicion){

		include_once ("lib/common/layout.class.php");
		
		$layout = new Layout();
		$depto_dao = New DeptoDAO();
		$mun_dao = New MunicipioDAO();
		$pob_dao = New PobladoDAO();
		$comuna_dao = New ComunaDAO();
		$condicion = '';

		$id_depto = '05';
		if (isset($_GET["id_depto"]) && $_GET["id_depto"] != ""){
			$id_depto = $_GET["id_depto"];

			$id_muns = $mun_dao->GetAllArrayID('ID_DEPTO ='.$id_depto,'');

			$m = 0;
			foreach ($id_muns as $id){
				$id_muns[$m] = "'".$id_muns[$m]."'";
				$m++;
			}
			$condicion = "ID_MUN IN (".implode(",",$id_muns).")";
		}

		$id_mun = 0;
		if (isset($_GET["id_mun"]) && $_GET["id_mun"] != ""){
			$id_mun = $_GET["id_mun"];

			$condicion = "ID_MUN = '".$id_mun."'";
		}

		$id_pob = 0;
		if (isset($_GET["id_pob"]) && $_GET["id_pob"] != ""){
			$id_pob = $_GET["id_pob"];

			$condicion = "ID_POB = '".$id_pob."'";
		}

		$id_comuna = 0;
		if (isset($_GET["id_comuna"]) && $_GET["id_comuna"] != ""){
			$id_comuna = $_GET["id_comuna"];

			$condicion = "ID_COMUNA = '".$id_comuna."'";
		}

		$arr = $this->GetAllArray($condicion);

		echo "<table align='center' class='tabla_lista' width='750' align='center'>
			<tr>
			<td colspan='6'>
			<b>Filtrar por Departamento</b>&nbsp;<select nane='id_depto' class='select' onchange=\"refreshTab('index_parser.php?accion=listar&class=".$_GET["class"]."&method=ListarTabla&param=&id_depto='+this.value)\">
			<option value=''>Todos</option>";
		$depto_dao->ListarCombo('combo',$id_depto,'');
		echo "</select>&nbsp;
		<b>Filtrar por Municipio</b>&nbsp;
		<select nane='id_mun' class='select' onchange=\"refreshTab('index_parser.php?accion=listar&class=".$_GET["class"]."&method=ListarTabla&param=&id_depto=".$id_depto."&id_mun='+this.value)\">
			<option value=''>---</option>";
		$mun_dao->ListarCombo('combo',$id_mun,'ID_DEPTO = '.$id_depto);
		echo "</select><br><br>
			<b>Filtrar por Poblado</b>&nbsp;
		<select nane='id_pob' class='select' onchange=\"refreshTab('index_parser.php?accion=listar&class=".$_GET["class"]."&method=ListarTabla&param=&id_depto=".$id_depto."&id_mun=".$id_mun."&id_pob='+this.value)\">
			<option value=''>---</option>";
		$pob_dao->ListarCombo('combo',$id_pob,'ID_MUN = '.$id_mun);
		echo "</select>&nbsp;
		<b>Filtrar por Comuna</b>&nbsp;
		<select nane='id_comuna' class='select' onchange=\"refreshTab('index_parser.php?accion=listar&class=".$_GET["class"]."&method=ListarTabla&param=&id_depto=".$id_depto."&id_mun=".$id_mun."&id_pob=".$id_pob."&id_comuna='+this.value)\">
			<option value=''>---</option>";
		$comuna_dao->ListarCombo('combo',$id_comuna,'ID_POB = '.$id_pob);
		echo "</select></td></tr>";

		$layout->adminGridLayout($arr,
								array('nombre' => array('titulo' => 'Nombre')),
						   		array('id_mun' => array('dao' => 'MunicipioDAO', 'nom' => 'nombre', 'titulo' => 'Municipio', 'filtro' => false),
						   			  'id_pob' => array('dao' => 'PobladoDAO', 'nom' => 'nombre', 'titulo' => 'Poblado', 'filtro' => false),
						   			  'id_comuna' => array('dao' => 'ComunaDAO', 'nom' => 'nombre', 'titulo' => 'Comuna', 'filtro' => false)),
								array()
								);
		echo "</table>";
	}

	/**
	 * Imprime en pantalla los datos del Barrio
	 * @access public
	 * @param object $vo Barrio que se va a imprimir
	 * @param string $formato Formato en el que se listarán los Barrio, puede ser Tabla o ComboSelect
	 * @param int $valor_combo ID del Barrio que será selccionado cuando el formato es ComboSelect
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
	 * Carga un VO de Barrio con los datos de la consulta
	 * @access public
	 * @param object $vo VO de Barrio que se va a recibir los datos
	 * @param object $Resultset Resource de la consulta
	 * @return object $vo VO de Barrio con los datos
	 */			
	function GetFromResult ($vo,$Result){

		$vo->id = $Result->{$this->columna_id};
		$vo->nombre = $Result->{$this->columna_nombre};

		$vo->id_mun = $Result->ID_MUN;
		$vo->id_pob = $Result->ID_POB;
		$vo->id_comuna = $Result->ID_COMUNA;

		return $vo;
	}

	/**
	 * Inserta un Barrio en la B.D.
	 * @access public
	 * @param object $vo VO de Barrio que se va a insertar
	 */		
	function Insertar($vo){
		//CONSULTA SI YA EXISTE
		$cat_a = $this->GetAllArray($this->columna_nombre." = '".$vo->nombre."'");
		if (count($cat_a) == 0){
			$sql =  "INSERT INTO ".$this->tabla." (".$this->columna_id.",".$this->columna_nombre.",ID_MUN,ID_POB,ID_COMUNA) VALUES ('".$vo->id."','".$vo->nombre."','".$vo->id_mun."','".$vo->id_pob."','".$vo->id_comuna."')";
			$this->conn->Execute($sql);
			
			echo "Registro insertado con &eacute;xito!";
		}
		else{
			echo "Error - Existe un registro con el mismo ID o nombre";
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
		$sql .= "ID_POB = '".$vo->id_pob."',";
		$sql .= "ID_COMUNA = '".$vo->id_comuna."'";
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
