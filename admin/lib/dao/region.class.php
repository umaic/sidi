<?
/**
 * DAO de Region
 *
 * Contiene los métodos de la clase Region 
 * @author Ruben A. Rojas C.
 */

Class RegionDAO {

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
	function RegionDAO (){
		$this->conn = MysqlDb::getInstance();
		$this->tabla = "region";
		$this->columna_id = "ID_REG";
		$this->columna_nombre = "NOM_REG";
		$this->columna_order = "NOM_REG";
		$this->num_reg_pag = 50;
		$this->url = "index.php?accion=listar&class=RegionDAO&method=ListarTabla&param=";
	}

	/**
  * Consulta los datos de una Region
  * @access public
  * @param int $id ID del Region
  * @return VO
  */	
	function Get($id){
		$sql = "SELECT * FROM ".$this->tabla." WHERE ".$this->columna_id." = ".$id;
		$rs = $this->conn->OpenRecordset($sql);
		$row_rs = $this->conn->FetchObject($rs);

		//Crea un VO
		$region_vo = New Region();

		//Carga el VO
		$region_vo = $this->GetFromResult($region_vo,$row_rs);

		//Retorna el VO
		return $region_vo;
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
			$vo = New Region();
			//Carga el VO
			$vo = $this->GetFromResult($vo,$row_rs);
			//Carga el arreglo
			$array[] = $vo;
		}  
		//Retorna el Arreglo de VO
		return $array;
	}

	/**
  * Lista los Region que cumplen la condición en el formato dado
  * @access public
  * @param string $formato Formato en el que se listarán los Region, puede ser Tabla o ComboSelect
	* @param int $valor_combo ID del Region que será selccionado cuando el formato es ComboSelect
  * @param string $condicion Condición que deben cumplir los Region y que se agrega en el SQL statement.
  */			
	function ListarCombo($formato,$valor_combo,$condicion){
		$arr = $this->GetAllArray($condicion);
		$num_arr = count($arr);

		for($a=0;$a<$num_arr;$a++){
			$this->Imprimir($arr[$a],$formato,$valor_combo);
		}
	}

	/**
  * Lista las Regiones en una Tabla
  * @access public
  */			
	function ListarTabla($condicion){

		include_once ("lib/common/layout.class.php");

		$layout = new Layout();

		$layout->adminGrid(array('nombre' => array('titulo' => 'Nombre')),array(),array('checkForeignKeys' => false));
	}

	/**
  * Imprime en pantalla los datos del Region
  * @access public
  * @param object $vo Region que se va a imprimir
  * @param string $formato Formato en el que se listarán los Region, puede ser Tabla o ComboSelect
	* @param int $valor_combo ID del Region que será selccionado cuando el formato es ComboSelect
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
  * Carga un VO de Region con los datos de la consulta
  * @access public
  * @param object $vo VO de Region que se va a recibir los datos
  * @param object $Resultset Resource de la consulta
	* @return object $vo VO de Region con los datos
  */			
	function GetFromResult ($vo,$Result){
		
		$vo->id = $Result->{$this->columna_id};
		$vo->nombre = $Result->{$this->columna_nombre};

		//CONSULTA LOS MUNICIPIOS
		$id_muns = Array();
		$nom_muns = Array();
		$id_deptos = Array();

		$sql = "SELECT mun_reg.ID_MUN, NOM_MUN, ID_DEPTO FROM mun_reg INNER JOIN municipio USING (ID_MUN) WHERE ID_REG = ".$vo->id;
		//echo $sql;
		$rs = $this->conn->OpenRecordset($sql);
		while ($row_rs = $this->conn->FetchRow($rs)){
			array_push($id_muns,$row_rs[0]);
			array_push($nom_muns,$row_rs[1]);
			if (!in_array($row_rs[2],$id_deptos)){
				array_push($id_deptos,$row_rs[2]);
			}
		}

		$vo->id_muns = $id_muns;
		$vo->id_deptos = $id_deptos;
		$vo->nom_muns = $nom_muns;

		return $vo;
	}

	/**
	* Inserta un Region en la B.D.
	* @access public
	* @param object $region_vo VO de Region que se va a insertar
	*/		
	function Insertar($region_vo){

		//CONSULTA SI YA EXISTE
		$cat_a = $this->GetAllArray("NOM_REG = '".$region_vo->nombre."'");
		if (count($cat_a) == 0){
			//INSERTA EL NOMBRE DE LA REGION
			$sql =  "INSERT INTO region (NOM_REG) VALUES ('".$region_vo->nombre."')";
			$this->conn->Execute($sql);

			$id_reg = $this->conn->GetGeneratedID();

			//INSERTA LOS MUNICIPIOS QUE FORMAN LA REGION
			$this->InsertarMpios($id_reg,$region_vo->id_muns);
			
			//$this->updateShapePostGis();

			echo "Registro insertado con &eacute;xito!";
		}
		else{
			echo "Error - Existe un registro con el mismo ID o nombre";
		}
	}

	/**
	* Inserta los mpios de una Region en la B.D.
	* @access public
	* @param id $id_region 
	* @param array $id_mpios 
	*/		
	function InsertarMpios($id_reg,$id_mpios){

		//INSERTA LOS MUNICIPIOS QUE FORMAN LA REGION
		foreach($id_mpios as $id){
			$sql = "INSERT INTO mun_reg (ID_MUN,ID_REG) VALUES ('$id',".$id_reg.")";
			$this->conn->Execute($sql);
		}
	}
	
	/**
  * Actualiza un Region en la B.D.
  * @access public
  * @param object $region_vo VO de Region que se va a actualizar
  */		
	function Actualizar($vo){
		$sql =  "UPDATE ".$this->tabla." SET ";
		$sql .= $this->columna_nombre." = '".$vo->nombre."'";
		$sql .= " WHERE ".$this->columna_id." = ".$vo->id;

		$this->conn->Execute($sql);

		//BORRA MUNICPIOS DE LA REGION
		$this->BorrarMpios($vo->id);

		//INSERTA LOS MUNICIPIOS QUE FORMAN LA REGION
		$this->InsertarMpios($vo->id,$vo->id_muns);

		//$this->updateShapePostGis();
		
	}

	/**
	* Borra un Region en la B.D.
	* @access public
	* @param int $id ID del Region que se va a borrar de la B.D
	*/	
	function Borrar($id){

		//BORRA MUNICPIOS DE LA REGION
		$this->BorrarMpios($id);

		//BORRA REGION
		$sql = "DELETE FROM ".$this->tabla." WHERE ID_REG = ".$id;
		$this->conn->Execute($sql);
		
		//$this->updateShapePostGis();
	}
	
	/**
	* Borra los mpios de una Region en la B.D.
	* @access public
	* @param int $id ID del Region que se va a borrar de la B.D
	*/	
	function BorrarMpios($id){

		//BORRA MUNICPIOS DE LA REGION
		$sql = "DELETE FROM mun_reg WHERE ID_REG = ".$id;
		$this->conn->Execute($sql);

	}
	

	/**
	* Actualiza la tabla región de Postgres y el archivo region.shp
	* @access public
	*/	
	function updateShapePostGis(){
		
		$sql = "TRUNCATE region";
		$this->pg_conn->Execute($sql); 
		
		$regs = $this->GetAllArray('id_reg=1');
		
		foreach ($regs as $reg){
			foreach ($reg->id_muns as $i=>$id_mun){
				if ($i == 0)	$cond = "'$id_mun'";
				else			$cond .= ",'$id_mun'";
			}

			$sql = "INSERT INTO region (the_geom,shape_leng,shape_area,region,id_mysql) values (
			(SELECT st_union(the_geom) FROM mpio WHERE codane2 in ($cond)),
			(SELECT length(st_union(the_geom)) FROM mpio WHERE codane2 in ($cond)),
			(SELECT area(st_union(the_geom)) FROM mpio WHERE codane2 in ($cond)),
			'$reg->nombre',$reg->id)";
			
			echo $sql;
			$this->pg_conn->Execute($sql);
		}
		
		exec("pgsql2shp -f ".$_SERVER["DOCUMENT_ROOT"]."/sissh/images/shapes/region.shp -u postgres sissh region");
	}
	
}

?>
