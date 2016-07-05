<?
/**
 * DAO de Tema
 *
 * Contiene los m�todos de la clase Tema
 * @author Ruben A. Rojas C.
 */

Class TemaDAO {

	/**
	 * Conexi�n a la base de datos
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
	 * Crea la conexi�n a la base de datos
	 * @access public
	 */
	function TemaDAO (){
		$this->conn = MysqlDb::getInstance();
		$this->tabla = "tema";
		$this->columna_id = "ID_TEMA";
		$this->columna_nombre = "NOM_TEMA";
		$this->columna_order = "NOM_TEMA";
	}

	/**
	 * Consulta los datos de una Tema
	 * @access public
	 * @param int $id ID del Tema
	 * @return VO
	 */
	function Get($id){
		$sql = "SELECT * FROM ".$this->tabla." WHERE ".$this->columna_id." = ".$id;
		$rs = $this->conn->OpenRecordset($sql);
		$row_rs = $this->conn->FetchObject($rs);

		//Crea un VO
		$tema_vo = New Tema();

		//Carga el VO
		$tema_vo = $this->GetFromResult($tema_vo,$row_rs);

		//Retorna el VO
		return $tema_vo;
	}

    /**
     * Consulta el nombre
     * @access public
     * @param int $id ID
     * @return string
     */
    function GetName($id){
        $sql = "SELECT ".$this->columna_nombre." FROM ".$this->tabla." WHERE ".$this->columna_id." = '$id'";
        $rs = $this->conn->OpenRecordset($sql);
        $row_rs = $this->conn->FetchRow($rs);

        //Retorna el VO
        return $row_rs[0];
    }

	/**
	* Consulta el valor de un field
	* @access public
	* @param int $id ID
	* @param string $field Field
	* @return VO
	*/
	function GetFieldValue($id,$field){
		$sql = "SELECT ".$field." FROM ".$this->tabla." WHERE ".$this->columna_id." = ".$id;
		$rs = $this->conn->OpenRecordset($sql);
		$row_rs = $this->conn->FetchRow($rs);

		//Retorna el VO
		return $row_rs[0];
	}

	/**
	 * Consulta los datos de los Tema que cumplen una condici�n
	 * @access public
	 * @param string $condicion Condici�n que deben cumplir los Tema y que se agrega en el SQL statement.
	 * @param string $limit Limit en el SQL
	 * @param string $order by Order by en el SQL
	 * @return array Arreglo de VOs
	 */
	function GetAllArray($condicion,$limit='',$order_by=''){

		$c = 0;
		$sql = "SELECT * FROM ".$this->tabla;
		if ($condicion != ""){
			$sql .= " WHERE ".$condicion;
		}

		//ORDER
		if ($order_by != ""){
			$sql .= " ORDER BY ".$order_by;
		}
		else{
			$sql .= " ORDER BY ".$this->columna_order;
		}

		//LIMIT
		if ($limit != ""){
			$sql .= " LIMIT ".$limit;
		}

		$array = Array();

		$rs = $this->conn->OpenRecordset($sql);
		while ($row_rs = $this->conn->FetchObject($rs)){
			//Crea un VO
			$vo = New Tema();
			//Carga el VO
			$vo = $this->GetFromResult($vo,$row_rs);
			//Carga el arreglo
			$array[] = $vo;
		}
		//Retorna el Arreglo de VO
		return $array;
	}

	/**
	 * Consulta los datos de los Tema que cumplen una condici�n
	 * @access public
	 * @param string $condicion Condici�n que deben cumplir los Tema y que se agrega en el SQL statement.
	 * @return array Arreglo de VOs
	 */
	function GetAllArrayID($condicion){

		$sql = "SELECT $this->columna_id FROM ".$this->tabla."";

		if ($condicion != ""){
			$sql .= " WHERE ".$condicion;
		}

		$sql .= " ORDER BY ".$this->columna_order;

		$array = Array();

		$rs = $this->conn->OpenRecordset($sql);
		while ($row = $this->conn->FetchRow($rs)){
			$array[] = $row[0];
		}

		//Retorna el Arreglo de ID's
		return $array;
	}

	/**
	 * Lista los Tema que cumplen la condici�n en el formato dado
	 * @access public
	 * @param string $formato Formato en el que se listar�n los Tema, puede ser Tabla o ComboSelect
	 * @param int $valor_combo ID del Tema que ser� selccionado cuando el formato es ComboSelect
	 * @param string $condicion Condici�n que deben cumplir los Tema y que se agrega en el SQL statement.
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

				$this->ListarComboHijos($valor_combo,$vo->id,"");
			}
		}
	}

	/**
	 * Lista los Items que cumplen la condici�n en un ComboSelect
	 * @access public
	 * @param int $valor_combo ID del CatProducto que ser� selccionado cuando el formato es ComboSelect
	 * @param string $condicion Condici�n que deben cumplir los CatProducto y que se agrega en el SQL statement.
	 */
	function ListarComboHijos($valor_combo,$id_papa,$tab){

		$tab .= "&nbsp;&nbsp;&nbsp;&nbsp;";
		$v_c_a = is_array($valor_combo);

		$arr = $this->GetAllArray("id_papa=$id_papa","","");

		foreach ($arr as $vo){

			$nombre = $vo->nombre;

			if ($valor_combo == "" && $valor_combo != 0){
				echo "<option value=".$vo->id.">".$tab.$nombre."</option>";
			}
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

				echo ">".$tab.$nombre."</option>";
			}

			$this->ListarComboHijos($valor_combo,$vo->id,$tab);
		}
	}

	/**
	 * Lista los Tema en una Tabla
	 * @access public
	 */
	function ListarTabla($condicion){

		$clas_dao = New ClasificacionDAO();

		$url = "index_parser.php?accion=listar&class=".$_GET["class"]."&method=ListarTabla&param=";

		$id_c = 0;
		if (isset($_GET["id_c"]) && $_GET["id_c"] != ''){
			$id_c = $_GET["id_c"];
			$condicion = " id_clasificacion = ".$id_c;
		}

		$id_papa = 0;
		$condicion_0 = '';
		if (isset($_GET["id_papa"]) && $_GET["id_papa"] != ''){
			$id_papa = $_GET["id_papa"];

			$condicion_0 = "id_papa = $id_papa";

			if ($id_c != 0) $condicion_0 = " AND ".$condicion_0;
		}
		else{
			if ($id_c == 0) $condicion_0 = "id_papa = $id_papa";
		}

		$url_c = $url;
		if ($id_papa > 0){
			$url_c = "$url&id_papa=$id_papa";
		}

		$url_p = $url;
		if ($id_c > 0){
			$url_p = "$url&id_c=$id_c";
		}

		$condicion_1 = $condicion.$condicion_0;
		$arr = $this->GetAllArray($condicion_1);

		$condicion_num = ($id_papa > 0 || $id_c > 0) ? $condicion_1 : '';
		$num_arr = $this->numRecords($condicion_num);

		echo "<table align='center' class='tabla_lista' width='800'>
			<tr>
			<td colspan='3'>
			Filtrar por Clasificaci&oacute;n&nbsp;
			<select class='select' onchange=\"refreshTab('$url_c&id_c='+this.value)\">
			<option value=''>Todos</option>";

		$clas_dao->ListarCombo('combo',$id_c,'');

		echo "</select></td></tr>";

		echo "<tr>
			<td colspan='3'>
			Filtrar por Tema&nbsp;
			<select class='select' onchange=\"refreshTab('$url_p&id_papa='+this.value)\">
			<option value=''>Todos</option>";

			$this->ListarCombo('combo',$id_papa,"");

		echo "</select></td></tr>

			<tr>
				<td width='70'><img src='images/home/insertar.png'>&nbsp;<a href='#' onclick=\"addWindowIU('tema','insertar','');return false;\">Crear</a></td>
				<td align='right' colspan='2'>[$num_arr Registros]</td>
			</tr>";
		echo "<tr class='titulo_lista'>
			<td width='100' align='center'>ID</td>
			<td>Nombre</td>
			<td width='100' align='center'>Clasificaci&oacute;n</td>
			</tr>";

		foreach ($arr as $p=>$vo){
			echo "<tr class='fila_lista'>";
			echo "<td>";

			if (!$this->checkForeignKeys($vo->id))	echo "<a href='#'  onclick=\"if(confirm('Est� seguro que desea borrar el Tema: ".$vo->nombre."')){borrarRegistro('".$_GET["class"]."','".$vo->id."')}else{return false};\"><img src='images/trash.png' border='0' title='Borrar' /></a>&nbsp;";

			echo $vo->id."</td>";
			echo "<td><a href='#' onclick=\"addWindowIU('tema','actualizar',".$vo->id.");return false;\">".$vo->nombre."</a></td>";

			//CLASIFICACION
			$cl = $clas_dao->Get($arr[$p]->id_clasificacion);
			echo "<td align='center'>".$cl->nombre."</td>";

			echo "</tr>";

			if ($vo->id_papa == 0){
				$this->ListarTablaHijos($vo->id,'',$condicion);
			}
		}

		echo "</table>";
	}

	/**
	 * Lista los Tema Hijos en una Tabla
	 * @access public
	 */
	function ListarTablaHijos($id_papa,$tab,$condicion){

		$clas_dao = New ClasificacionDAO();

		$tab .= "&nbsp;&nbsp;&nbsp;&nbsp;";
		$condicion_0 = ($condicion != '') ? $condicion." AND id_papa = $id_papa" : "id_papa = $id_papa";
		$arr = $this->GetAllArray($condicion_0);

		foreach ($arr as $p=>$vo){
			echo "<tr class='fila_lista'>";
			echo "<td>";

			if (!$this->checkForeignKeys($vo->id))	echo "<a href='#'  onclick=\"if(confirm('Est� seguro que desea borrar el Tema: ".$vo->nombre."')){borrarRegistro('".$_GET["class"]."','".$vo->id."')}else{return false};\"><img src='images/trash.png' border='0' title='Borrar' /></a>&nbsp;";

			echo $vo->id."</td>";
			echo "<td>$tab<a href='#' onclick=\"addWindowIU('tema','actualizar',".$vo->id.");return false;\">".$vo->nombre."</a></td>";

			//CLASIFICACION
			$cl = $clas_dao->Get($arr[$p]->id_clasificacion);
			echo "<td align='center'>".$cl->nombre."</td>";

			echo "</tr>";

			$this->ListarTablaHijos($vo->id,$tab,$condicion);
		}
	}

	/**
	 * Carga un VO de Tema con los datos de la consulta
	 * @access public
	 * @param object $vo VO de Tema que se va a recibir los datos
	 * @param object $Resultset Resource de la consulta
	 * @return object $vo VO de Tema con los datos
	 */
	function GetFromResult ($vo,$Result){

		$vo->id = $Result->{$this->columna_id};
		$vo->nombre = $Result->{$this->columna_nombre};
		$vo->id_clasificacion = $Result->ID_CLASIFICACION;
		$vo->id_papa = $Result->ID_PAPA;
		$vo->def = $Result->DEFINICION_TEMA;

		return $vo;
	}

	/**
	 * Inserta un Tema en la B.D.
	 * @access public
	 * @param object $tema_vo VO de Tema que se va a insertar
	 */
	function Insertar($tema_vo){
		//CONSULTA SI YA EXISTE
		$cat_a = $this->GetAllArray($this->columna_nombre." = '".$tema_vo->nombre."'");
		if (count($cat_a) == 0){
			$sql =  "INSERT INTO ".$this->tabla." (".$this->columna_nombre.",id_clasificacion,id_papa, definicion_tema)
                    VALUES ('".$tema_vo->nombre."',$tema_vo->id_clasificacion,$tema_vo->id_papa,'".$tema_vo->def."')";
			$this->conn->Execute($sql);

			echo "Registro insertado con &eacute;xito!";
		}
		else{
			echo "Error - Existe un registro con el mismo nombre";
		}

	}

	/**
	 * Actualiza un Tema en la B.D.
	 * @access public
	 * @param object $tema_vo VO de Tema que se va a actualizar
	 */
	function Actualizar($tema_vo){
		$sql =  "UPDATE ".$this->tabla." SET ";
		$sql .= $this->columna_nombre." = '".$tema_vo->nombre."',";
		$sql .= "definicion_tema = '$tema_vo->def',";
		$sql .= "id_clasificacion = $tema_vo->id_clasificacion,";
		$sql .= "id_papa = $tema_vo->id_papa";

		$sql .= " WHERE ".$this->columna_id." = ".$tema_vo->id;

		$this->conn->Execute($sql);

	}

	/**
	 * Borra un Tema en la B.D.
	 * @access public
	 * @param int $id ID del Tema que se va a borrar de la B.D
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

		$tabla_rel = 'proyecto_tema';

		$sql = "SELECT sum($this->columna_id) FROM $tabla_rel WHERE ".$this->columna_id." = ".$id;
		$rs = $this->conn->OpenRecordset($sql);
		$row = $this->conn->FetchRow($rs);

		$r = ($row[0] > 0) ? true : false;

		return $r;

	}

	/**
	 * Consulta el arbol dado un papa o descendiente y dada la direcicon, up-down, incluyendo al descendiente en el return
	 * @access public
	 * @param int $id Id del descendiente
	 * @param int $dir Direccion de consulta: up o down
	 * @param array $arbol Arreglo para ir almacenando el arbol
	 * @return array $arbol
	 */
	function getArbol($id,$dir='down',$arbol=array()){
		if ($dir == 'down'){
			$sql = "SELECT ".$this->columna_id." as id FROM ".$this->tabla." WHERE id_papa = $id";
		}
		else{
			$sql = "SELECT id_papa as id FROM ".$this->tabla." WHERE ".$this->columna_id." = $id";
		}

		$rs = $this->conn->OpenRecordset($sql);

		while ($row_rs = $this->conn->FetchObject($rs)){
			$arbol[] = $row_rs->id;
			$this->getArbol($row_rs->id,$dir,$arbol);
		}

		$arbol = array_reverse($arbol);
		$arbol[] = $id;
		return $arbol;
	}

	/**
	 * Consulta el arbol dado un papa o descendiente y dada la direcicon, up-down, incluyendo al descendiente en el return y le pone numeracion
	 * @access public
	 * @param int $id Id del descendiente
	 * @param array $arbol Arreglo para ir almacenando el arbol
	 * @return array $arbol
	 */
	function getArbolNombreNumerado($id_cabeza,$id,$arbol,$nivel,$num_hijo,$limit_nivel){

		if ($nivel < $limit_nivel){
			$nivel += 1;

			$vineta_cabeza = $id_cabeza;

			for($n=1;$n<$nivel;$n++){
				$vineta_cabeza .= ".".$num_hijo;
			}

			$num_hijo = 0;

			$sql = "SELECT $this->columna_id as id ,$this->columna_nombre as nombre FROM ".$this->tabla." WHERE id_papa = $id";
			//echo "$sql";

			$rs = $this->conn->OpenRecordset($sql);
			while ($row = $this->conn->FetchObject($rs)){

				$num_hijo += 1;

				$vineta = $vineta_cabeza.".".$num_hijo;
				$arbol["id"][] = $row->id;
				$arbol["nombre"][] = $vineta." ".$row->nombre;
				//echo "==>$vineta----nivel=$nivel----nombre=$row->nombre----num_array=".count($arbol)."<br>";
				$this->getArbolNombreNumerado($id_cabeza,$row->id,$arbol,$nivel,$num_hijo,$limit_nivel);
			}
		}

		return $arbol;
	}
}

/**
 * Ajax de Temas
 *
 * Contiene los metodos para Ajax de la clase TemaDAO
 * @author Ruben A. Rojas C.
 */

Class TemaAjax extends TemaDAO {

	/**
	* Lista ComboBox de temas
	* @access public
	* @param string $id_papa ID de los papa, separados por coma
	* @param int $multiple 0 = comboBox no es multiple, > 0 comboBox es multiple de size = $multiple
	* @param string $titulo Si es diferente a vacio coloca titulo
	* @param int $separador 1 = Coloca el titulo del tema en las opciones
	*/
	function comboBoxTemaProyecto($id_papa,$multiple,$titulo,$separador,$id_name){

		//INICIALIZACION DE VARIABLES
		include_once($_SERVER["DOCUMENT_ROOT"]."/sissh/admin/lib/model/tema.class.php");

		//INICIALIZACION DE VARIABLES

		$id_papa = explode(",",$id_papa);

		$num = $this->numRecords("id_papa IN (".implode(",",$id_papa).")");
		echo $num;

		if ($num > 0){

			if ($titulo != '')	echo "<b>$titulo</b><br>";

			if ($multiple == 0){
				echo "<select id='$id_name' name='$id_name' class='select'>";
				echo "<option value=''>[ Seleccione ]</option>";
			}
			else{
				echo "<select id='$id_name' name='".$id_name."[]' class='select' multiple size=$multiple>";
			}

			foreach ($id_papa as $id){
				$vo = $this->Get($id);
				//echo $id_papa;
				$hijos = $this->GetAllArray("id_papa ='$id'");

				if ($separador == 1)	echo "<option value='' disabled>-------- ".$vo->nombre." --------</option>";
				foreach ($hijos as $hijo){
					echo "<option value='".$hijo->id."'>".$hijo->nombre."</option>";
				}
			}

			echo "</select>";
		}
		else{
			echo "<b>* No hay Info *</b>";
		}
	}

	/**
	* Lista de checkboxs de temas
	* @access public
	* @param string $id_papa ID de los papa, separados por coma
	* @param string $titulo Si es diferente a vacio coloca titulo
	* @param int $separador 1 = Coloca el titulo del tema en las opciones
	* @param string $id_name String para usarlo como name o id en el html object
	* @param link_subnivel $link_subnivel , Determina si se muestra el link para listar subtemas
	*/
	function checkBoxTemaProyecto($id_papa,$titulo,$separador,$id_name,$link_subnivel){

		//INICIALIZACION DE VARIABLES
		include_once($_SERVER["DOCUMENT_ROOT"]."/sissh/admin/lib/model/tema.class.php");

		//INICIALIZACION DE VARIABLES
		$id_papa_a = explode(",",$id_papa);

		$num = $this->numRecords("id_papa IN ($id_papa)");

		if ($num > 0){

			//Link para mostrar siguiente nivel
			$boton_link = "<input type='button' value='Listar Subtemas' onclick=\"listarTemasHijos('".$id_name."[]','id_subtema_$id_papa','nieto_$id_papa',0);return false;\" class='boton'><br /><br />";

			if ($link_subnivel == 1){
				echo $boton_link;
			}

			if ($titulo != '')	echo "<b>$titulo</b><br>";

			foreach ($id_papa_a as $id){

				$num_h = $this->numRecords("id_papa IN ($id)");

				if ($num_h > 0){
					$vo = $this->Get($id);
					$hijos = $this->GetAllArray("id_papa ='$id'");

					if ($separador == 1)	echo "<p class='tit_tema_insert'>$vo->nombre</p>";

					foreach ($hijos as $hijo){
						echo "<input type='checkbox' name='".$id_name."[]' value=$hijo->id>&nbsp;".$hijo->nombre."<br /><br />";
					}
				}
			}

			if ($link_subnivel == 1){
				echo $boton_link;
			}

		}
	}

}
?>
