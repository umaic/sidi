<?
/**
 * DAO de Resguardo
 *
 * Contiene los métodos de la clase Resguardo 
 * @author Ruben A. Rojas C.
 */

Class ResguardoDAO {

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
	function ResguardoDAO (){
		$this->conn = MysqlDb::getInstance();
		$this->tabla = "resguardo";
		$this->columna_id = "ID_RESGUADRO";
		$this->columna_nombre = "NOM_RESGUARDO";
		$this->columna_order = "NOM_RESGUARDO";
	}

	/**
	 * Consulta los datos de una Resguardo
	 * @access public
	 * @param int $id ID del Resguardo
	 * @return VO
	 */	
	function Get($id){
		$sql = "SELECT * FROM ".$this->tabla." WHERE ".$this->columna_id." = ".$id;
		$rs = $this->conn->OpenRecordset($sql);
		$row_rs = $this->conn->FetchObject($rs);

		//Crea un VO
		$depto_vo = New Resguardo();

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
			$vo = New Resguardo();
			//Carga el VO
			$vo = $this->GetFromResult($vo,$row_rs);
			//Carga el arreglo
			$array[] = $vo;
		}  
		//Retorna el Arreglo de VO
		return $array;
	}

	/**
	 * Lista los Resguardo que cumplen la condición en el formato dado
	 * @access public
	 * @param string $formato Formato en el que se listarán los Resguardo, puede ser Tabla o ComboSelect
	 * @param int $valor_combo ID del Resguardo que será selccionado cuando el formato es ComboSelect
	 * @param string $condicion Condición que deben cumplir los Resguardo y que se agrega en el SQL statement.
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

		$region_dao = New DeptoDAO();
		$mun_dao = New MunicipioDAO();

		$arr = $this->GetAllArray($condicion);
		$num_arr = count($arr);

		echo "<table align='center' cellspacing='1' cellpadding='3' width='750' align='center'>
			<tr><td>&nbsp;</td></tr>
			<tr class='titulo_lista'><td align='center' colspan=6><b>RESGUARDOS</b></td></tr>

			<tr class='titulo_lista'>
			<td width='150' align='center'>ID</td>
			<td>Nombre</td>
			<td align='center' width='100'>Registros: ".$num_arr."</td>
			</tr>";

		//PAGINACION
		$inicio = 0;
		$pag_url = 1;
		if (isset($_GET['page']) && $_GET['page'] > 1){
			$pag_url = $_GET['page'];
			$inicio = ($pag_url-1)*$this->num_reg_pag;
		}
		$fin = $inicio + $this->num_reg_pag;
		if ($fin > $num_arr){
			$fin = $num_arr;
		}

		for($p=$inicio;$p<$fin;$p++){
			$style = "";
			if (fmod($p+1,2) == 0)  $style = "fila_lista";
			echo "<tr class='".$style."'>";
			echo "<td align='center'>".$arr[$p]->id."</td>";
			echo "<td><a href='".$_SERVER['PHP_SELF']."?accion=actualizar&id=".$arr[$p]->id."'>".$arr[$p]->nombre."</a></td>";
			echo "<td align='center'><a href='index.php?accion=borrar&class=".$_GET["class"]."&method=Borrar&param=".$arr[$p]->id."' onclick=\"return confirm('Está seguro que desea borrar el Región: ".$arr[$p]->nombre."');\">Borrar</a></td>";
			echo "</tr>";
		}

		echo "<tr><td>&nbsp;</td></tr>";
		//PAGINACION
		if ($num_arr > $this->num_reg_pag){

			$num_pages = ceil($num_arr/$this->num_reg_pag);
			echo "<tr><td colspan='2' align='center'>";

			echo "Ir a la página:&nbsp;<select onchange=\"location.href='index.php?accion=listar&class=".$_GET["class"]."&method=".$_GET["method"]."&param=".$_GET["param"]."&page='+this.value\" class='select'>";
			for ($pa=1;$pa<=$num_pages;$pa++){
				echo " <option value='".$pa."'";
				if ($pa == $pag_url)	echo " selected ";
				echo ">".$pa."</option> ";
			}
			echo "</select>";
			echo "</td></tr>";
		}
		echo "</table>";
	}

	/**
	 * Imprime en pantalla los datos del Resguardo
	 * @access public
	 * @param object $vo Resguardo que se va a imprimir
	 * @param string $formato Formato en el que se listarán los Resguardo, puede ser Tabla o ComboSelect
	 * @param int $valor_combo ID del Resguardo que será selccionado cuando el formato es ComboSelect
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
	 * Carga un VO de Resguardo con los datos de la consulta
	 * @access public
	 * @param object $vo VO de Resguardo que se va a recibir los datos
	 * @param object $Resultset Resource de la consulta
	 * @return object $vo VO de Resguardo con los datos
	 */			
	function GetFromResult ($vo,$Result){

		$vo->id = $Result->{$this->columna_id};
		$vo->nombre = $Result->{$this->columna_nombre};
		$vo->area = $Result->AREA_RESGUARDO;
		$vo->poblacion = $Result->POBLACION_RESGUARDO;
		$vo->familias = $Result->FAMILIAS_RESGUARDO;
		$vo->documento = $Result->DOCUMENTO_RESGUARDO;

		$id = $vo->id;

		//CONSULTA LOS MUNICIPIOS
		$vo->id_muns = Array();
		$vo->nom_muns = Array();

		$sql = "SELECT r_m.ID_MUN, NOM_MUN FROM resg_mun as r_m JOIN municipio USING(ID_MUN) WHERE ".$this->columna_id." = ".$id;
		$rs = $this->conn->OpenRecordset($sql);
		while ($row_rs = $this->conn->FetchRow($rs)){
			$vo->id_muns[] = $row_rs[0];
			$vo->nom_muns[] = $row_rs[1];
		}

		//CONSULTA LOS DEPTOS
		$vo->id_deptos = Array();

		$sql = "SELECT ID_DEPTO FROM resg_depto WHERE ".$this->columna_id." = ".$id;
		$rs = $this->conn->OpenRecordset($sql);
		while ($row_rs = $this->conn->FetchRow($rs)){
			$vo->id_deptos[] = $row_rs[0];
		}

		//CONSULTA LAS SUBETNIAS
		$vo->id_subetnias = Array();

		$sql = "SELECT ID_SUBETNIA FROM resg_subetnia WHERE ".$this->columna_id." = ".$id;
		$rs = $this->conn->OpenRecordset($sql);
		while ($row_rs = $this->conn->FetchRow($rs)){
			$vo->id_subetnias[] = $row_rs[0];
		}

		return $vo;
	}

	/**
	 * Inserta un Resguardo en la B.D.
	 * @access public
	 * @param object $vo VO de Resguardo que se va a insertar
	 */		
	function Insertar($vo){

		//CONSULTA SI YA EXISTE
		$cat_a = $this->GetAllArray("NOM_REG = '".$vo->nombre."'");
		if (count($cat_a) == 0){
			//INSERTA EL NOMBRE DE LA REGION
			$sql =  "INSERT INTO resguardo (NOM_RESGUARDO,AREA_RESGUARDO,POBLACION_RESGUARDO,FAMILIAS_RESGUADO,DOCUMENTO_RESGUARDO) VALUES ('".$vo->nombre."',$vo->area,$vo->poblacion,$vo->familias,'$vo->documento')";
			$this->conn->Execute($sql);

			$id_reg = $this->conn->GetGeneratedID();
			$vo->id = $id_reg;

			$this->InsertarTablasUnion($vo);


			echo "Registro insertado con &eacute;xito!";
		}
		else{
			echo "Error - Existe un registro con el mismo ID o nombre";
		}
	}

	/**
	 * Inserta un las tablas de union de Resguardo en la B.D.
	 * @access public
	 * @param object $vo VO de Resguardo que se va a insertar
	 */		
	function InsertarTablasUnion($vo){

		//INSERTA LOS MUNICIPIOS
		foreach($vo->id_muns as $id_mun){
			$sql = "INSERT INTO resg_mun (ID_MUN,$this->columna_id) VALUES ($id_mun,$vo->id)";
			$this->conn->Execute($sql);
		}

		//INSERTA LOS DEPTOS
		foreach($vo->id_deptos as $id_depto){
			$sql = "INSERT INTO resg_depto (ID_DEPTO,$this->columna_id) VALUES ($id_depto,$vo->id)";
			$this->conn->Execute($sql);
		}

		//INSERTA LAS SUBETNIAS
		foreach($vo->id_subetnias as $id_s){
			$sql = "INSERT INTO resg_subetnia (ID_SUBETNIA,$this->columna_id) VALUES ($id_s,$vo->id)";
			$this->conn->Execute($sql);
		}
	}

	/**
	 * Actualiza un Resguardo en la B.D.
	 * @access public
	 * @param object $vo VO de Resguardo que se va a actualizar
	 */		
	function Actualizar($vo){

		$sql =  "UPDATE $this->tabla SET";
		$sql .= " NOM_RESGUARDO = '$vo->nombre',";
		$sql .= " AREA_RESGUARDO = $vo->area,";
		$sql .= " POBLACION_RESGUARDO = $vo->poblacion,";
		$sql .= " FAMILIAS_RESGUARDO = $vo->familias,";
		$sql .= " DOCUMENTO_RESGUARDO = $vo->documento";

		$sql .= " WHERE $this->columna_id = ".$vo->id;

		$this->conn->Execute($sql);
		$this->BorrarTablasUnion($vo->id);
		$this->InsertarTablasUnion($vo);

	}

	/**
	 * Borra un Resguardo en la B.D.
	 * @access public
	 * @param int $id ID del Resguardo que se va a borrar de la B.D
	 */	
	function Borrar($id){

		//BORRA REGION
		$sql = "DELETE FROM ".$this->tabla." WHERE ID_REG = ".$id;
		$this->conn->Execute($sql);

	}

	/**
	 * Borra las tabla de union de Resguardo en la B.D.
	 * @access public
	 * @param int $id ID del Resguardo que se va a borrar de la B.D
	 */	
	function BorrarTablasUnion($id){

		//BORRA MUNS
		$sql = "DELETE FROM resg_mun WHERE ID_REG = ".$id;
		$this->conn->Execute($sql);

		//BORRA DEPTOS
		$sql = "DELETE FROM resg_depto WHERE ID_REG = ".$id;
		$this->conn->Execute($sql);

		//BORRA SUBETNIAS
		$sql = "DELETE FROM resg_subetnia WHERE ID_REG = ".$id;
		$this->conn->Execute($sql);

	}

}

?>
