<?
/**
 * DAO de EventoConflicto
 *
 * Contiene los métodos de la clase EventoConflicto 
 * @author Ruben A. Rojas C.
 */

Class EventoConflictoDAO {

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
	* Número de Registros en Pantalla para ListarTAbla
	* @var string
	*/
	var $num_reg_pag;

	/**
  * Constructor
	* Crea la conexión a la base de datos
  * @access public
  */	
	function EventoConflictoDAO (){
		$this->conn = MysqlDb::getInstance();
		$this->tabla = "evento_c";
		$this->columna_id = "ID_EVEN";
		$this->columna_nombre = "SINTESIS_EVEN";
		$this->columna_order = "FECHA_REG_EVEN";
		$this->num_reg_pag = 40;
//		$this->url = "index.php?accion=listar&class=EventoConflictoDAO&method=ListarTabla&param=";
		$this->url = "index.php?m_e=evento_c&accion=insertar";
	}

	/**
  * Consulta los datos de una EventoConflicto
  * @access public
  * @param int $id ID del EventoConflicto
  * @return VO
  */	
	function Get($id){
		$sql = "SELECT * FROM ".$this->tabla." WHERE ".$this->columna_id." = ".$id;
		$rs = $this->conn->OpenRecordset($sql);
		$row_rs = $this->conn->FetchObject($rs);

		//Crea un VO
		$depto_vo = New EventoConflicto();

		//Carga el VO
		$depto_vo = $this->GetFromResult($depto_vo,$row_rs);

		//Retorna el VO
		return $depto_vo;
	}

	/**
  * Retorna el max ID
  * @access public
  * @return int
  */	
	function GetMaxID(){
		
		$sql = "SELECT max($this->columna_id) as maxid FROM ".$this->tabla;
		$rs = $this->conn->OpenRecordset($sql);
		if($row_rs = $this->conn->FetchRow($rs)){
			return $row_rs[0];
		}
		else{
			return 0;
		}
	}
	
	/**
	* Retorna el max(numero de actores) que tienen los eventos
	* @access public
	* @return int
	*/	
	function getMaxActoresEvento(){
		$sql = "SELECT count(actor_descevento.id_actor) as num FROM actor_descevento INNER JOIN actor on actor_descevento.id_actor = actor.id_actor WHERE nivel=1 GROUP BY id_deseven ORDER BY num DESC LIMIT 0,1";
		$rs = $this->conn->OpenRecordset($sql);
		$row_rs = $this->conn->FetchRow($rs);
		return $row_rs[0];
	}

	/**
	* Retorna el max(numero de victimas) que tienen los eventos
	* @access public
	* @return int
	*/	
	function getMaxVictimasEvento(){
		$sql = "SELECT count(id_deseven) as num FROM victima GROUP BY id_deseven ORDER BY num DESC LIMIT 0,1";
		$rs = $this->conn->OpenRecordset($sql);
		$row_rs = $this->conn->FetchRow($rs);
		return $row_rs[0];
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
  * Consulta los datos de los EventoConflicto que cumplen una condición
  * @access public
  * @param string $condicion Condición que deben cumplir los EventoConflicto y que se agrega en el SQL statement.
  * @return array Arreglo de VOs
  */	
	function GetAllArray($condicion){
		$c = 0;
		$sql = "SELECT * FROM ".$this->tabla;
		if ($condicion != ""){
			$sql .= " WHERE ".$condicion;
		}
		$sql .= " ORDER BY ".$this->columna_order;

		$array = Array();

		$rs = $this->conn->OpenRecordset($sql);
		while ($row_rs = $this->conn->FetchObject($rs)){
			//Crea un VO
			$vo = New EventoConflicto();
			//Carga el VO
			$vo = $this->GetFromResult($vo,$row_rs);
			//Carga el arreglo
			$array[$c] = $vo;
			$c++;
		}
		//Retorna el Arreglo de VO
		return $array;
	}

	/**
  * Lista los EventoConflicto que cumplen la condición en el formato dado
  * @access public
  * @param string $formato Formato en el que se listarán los EventoConflicto, puede ser Tabla o ComboSelect
	* @param int $valor_combo ID del EventoConflicto que será selccionado cuando el formato es ComboSelect
  * @param string $condicion Condición que deben cumplir los EventoConflicto y que se agrega en el SQL statement.
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
  * Lista los TipoEventoConflicto en una Tabla
  * @access public
  */			
	function ListarTabla(){
		
		$cat_dao = new CatEventoConflictoDAO();

		////CLASS
		if (isset($_POST["class"])){
			$class = $_POST['class'];
		}
		else if (isset($_GET["class"])){
			$class = $_GET['class'];
		}

		////METHOD
		if (isset($_POST["method"])){
			$method = $_POST['method'];
		}
		else if (isset($_GET["method"])){
			$method = $_GET['method'];
		}

		////PARAM
		if (isset($_POST["param"])){
			$param = $_POST['param'];
		}
		else if (isset($_GET["method"])){
			$param = $_GET['param'];
		}

		////FECHA INICIAL
		$f_ini = "";
		if (isset($_POST["f_ini"])){
			$f_ini = $_POST['f_ini'];
			$_SESSION['f_ini'] = $f_ini;
		}
		else if (isset($_GET["f_ini"])){
			$f_ini = $_GET['f_ini'];
			$_SESSION['f_ini'] = $f_ini;
		}

		////FECHA FINAL
		$f_fin = "";
		if (isset($_POST["f_fin"])){
			$f_fin = $_POST['f_fin'];
			$_SESSION['f_fin'] = $f_fin;
		}
		else if (isset($_GET["f_fin"])){
			$f_fin = $_GET['f_fin'];
			$_SESSION['f_fin'] = $f_fin;
		}
		
		//ID
		$id = "";
		if (isset($_POST["id"])){
			$id = $_POST['id'];
			$_SESSION['id'] = $id;
		}
		else if (isset($_GET["id"])){
			$id = $_GET['id'];
			$_SESSION['id'] = $id;
		}
		
		if ($f_ini != "" && $f_fin != ""){
			$where = "FECHA_REG_EVEN BETWEEN '".$f_ini."' AND '".$f_fin."'";
			$texto_filtro = "<b>Listado de Eventos que ocurrieron entre $f_ini y $f_fin</b>";
		}
		else if ($id != ""){
			$where = "id_even = $id";
			$texto_filtro = "";
		}
		
		$arr = $this->GetAllArray($where);
		$num_arr = count($arr);

		
		echo "<table width='750' align='center' cellspacing='1' cellpadding='3'>
    			<tr><td>&nbsp;</td></tr>
    			<tr><td colspan='5'>$texto_filtro</td></tr>
          <tr class='titulo_lista'>
        	  <td>ID</td>
        	  <td>Categor&iacute;a</td>
        	  <td width='500'>Evento</td>
        	  <td align='center'>Fecha Reg.</td>
			  <td align='center'># ".$num_arr."</td>
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

			echo "<td><div align='justify'>".$arr[$p]->id."</div></td>";
			//Categoria
			if ($arr[$p]->id_cat != ""){
				$cat = $cat_dao->Get($arr[$p]->id_cat);
				echo "<td align='center'>".$cat->nombre."</td>";
			}
			else{
				echo "<td>-</td>";
			}

			echo "<td><div align='justify'>".$arr[$p]->sintesis."</div></td>";
			echo "<td align='center'>".$arr[$p]->fecha_evento."</td>";
			echo "<td align='center'>
				<a href='#' onclick=\"window.open('evento_c/ver.php?id=".$arr[$p]->id."','','top=100,left=100,height=600,width=1024,scrollbars=1');return false;\">Ver</a><br>
				<a href='index.php?accion=actualizar&id=".$arr[$p]->id."'>Editar</a><br>
				<a href='index.php?accion=borrar&class=".$class."&method=Borrar&param=".$arr[$p]->id."' onclick=\"return confirm('Está seguro que desea borrar el Evento?');\">Borrar</a>
				</td>";

			echo "</tr>";
		}

		echo "<tr><td>&nbsp;</td></tr>";
		//PAGINACION
		if ($num_arr > $this->num_reg_pag){

			$num_pages = ceil($num_arr/$this->num_reg_pag);
			echo "<tr><td colspan='5' align='center'>";

			echo "Ir a la página:&nbsp;<select onchange=\"location.href='index.php?f_ini=".$f_ini."&f_fin=".$f_fin."&accion=listar&class=".$class."&method=".$method."&param=".$param."&page='+this.value\" class='select'>";
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
  * Imprime en pantalla los datos del EventoConflicto
  * @access public
  * @param object $vo EventoConflicto que se va a imprimir
  * @param string $formato Formato en el que se listarán los EventoConflicto, puede ser Tabla o ComboSelect
	* @param int $valor_combo ID del EventoConflicto que será selccionado cuando el formato es ComboSelect
  */			
	function Imprimir($vo,$formato,$valor_combo){

		if ($formato == 'combo'){
			if ($valor_combo == "" && $valor_combo != 0)
			echo "<option value=".$vo->id.">".$vo->nombre."</option>";
			else{
				echo "<option value=".$vo->id;
				if ($valor_combo == $vo->id)
				echo " selected ";
				echo ">".$vo->nombre."</option>";
			}
		}
	}

	/**
  * Carga un VO de EventoConflicto con los datos de la consulta
  * @access public
  * @param object $vo VO de EventoConflicto que se va a recibir los datos
  * @param object $Resultset Resource de la consulta
	* @return object $vo VO de EventoConflicto con los datos
  */			
	function GetFromResult ($vo,$Result){

		$vo->id = $Result->ID_EVEN;
		$vo->fecha_evento = $Result->FECHA_REG_EVEN;
		$vo->sintesis = $Result->SINTESIS_EVEN;
		
		//Categoria
		$sql = "SELECT id_cateven FROM subcat_even INNER JOIN descripcion_evento ON subcat_even.id_scateven = descripcion_evento.id_scateven WHERE id_even = $vo->id";
		$rs = $this->conn->OpenRecordset($sql);
		$row = $this->conn->FetchRow($rs);
		
		$vo->id_cat = $row[0];

		return $vo;

	}

	/**
	* Consulta los actores de un evento
	* @access public
	* @param int $id_evento ID deL evento
	* @return array Arreglo de Id de los mpios, y arreglo de lugares
	*/			
	function getLocalizacionEvento ($id_evento){
		
		$arr = array();
		$arr_lugar = array();
		
		$l = 0;
		$sql = "SELECT ID_MUN,LUGAR FROM evento_localizacion WHERE ID_EVEN = $id_evento";
		$rs = $this->conn->OpenRecordset($sql);
		while ($row_rs = $this->conn->FetchRow($rs)){
			array_push($arr,$row_rs[0]);
			array_push($arr_lugar,$row_rs[1]);
			
			$l++;
		}
		return array("mpios" => $arr,"lugar" => $arr_lugar,"num"=>$l);
	}	
	
	/**
	* Consulta los actores de un evento
	* @access public
	* @param int $id_desevento ID de la descripcion del evento
	* @param int $nivel Nivel de profunidad en el árbol genialógico
	* @return array Arreglo de Id de los actores, y arreglo de nombres
	*/			
	function getActorEvento ($id_desevento,$nivel=1){
		
		$actor_dao = New ActorDAO();
		
		$arr = array();
		$arr_nom = array();
		
		$sql = "SELECT actor_descevento.ID_ACTOR FROM actor_descevento INNER JOIN actor ON actor_descevento.id_actor = actor.id_actor WHERE ID_DESEVEN = $id_desevento AND nivel = $nivel";
		$rs = $this->conn->OpenRecordset($sql);
		while ($row_rs = $this->conn->FetchRow($rs)){
			array_push($arr,$row_rs[0]);
			$actor = $actor_dao->Get($row_rs[0]);
			array_push($arr_nom,$actor->nombre);
		}
		return array("id" => $arr,"nombre" => $arr_nom);
	}
	
	/**
	* Consulta las fuentes de un evento
	* @access public
	* @param int $id_evento ID del evento
	* @return array Arreglo de Id de las fuentes y arreglo de los nombre
	*/			
	function getFuenteEvento ($id_evento){
		
		$sfuente_dao = New SubFuenteEventoConflictoDAO();
		$fuente_dao = New FuenteEventoConflictoDAO();
		
		$arr_id_fuente = array();
		$arr_id_sfuente = array();
		$arr_nom_fuente = array();
		$arr_nom_sfuente = array();
		$arr_fecha = array();
		$arr_medio = array();
		$arr_desc = array();
		
		$f = 0;
		$sql = "SELECT * FROM fuen_evento WHERE ID_EVEN = $id_evento";
		$rs = $this->conn->OpenRecordset($sql);
		while ($row_rs = $this->conn->FetchObject($rs)){
			array_push($arr_id_sfuente,$row_rs->ID_SFUEVEN);
			if ($row_rs->ID_SFUEVEN > 0){
				$sfuente = $sfuente_dao->Get($row_rs->ID_SFUEVEN);
				array_push($arr_nom_sfuente,$sfuente->nombre);
				array_push($arr_id_fuente,$sfuente->id_fuente);
			
				$fuente = $fuente_dao->Get($sfuente->id_fuente);
				array_push($arr_nom_fuente,$fuente->nombre);
			}
			else{
				array_push($arr_nom_sfuente,"");
				array_push($arr_id_fuente,0);
				array_push($arr_nom_fuente,"");
			}
			array_push($arr_desc,$row_rs->DESEVEN_FUENTE);
			array_push($arr_fecha,$row_rs->FECHA_FUENTE);
			array_push($arr_medio,$row_rs->REFER_FUENTE);
			
			$f++;
		}
		return array("id_fuente" => $arr_id_fuente,
					 "nom_fuente" => $arr_nom_fuente,
					 "id_sfuente" => $arr_id_sfuente,
					 "nom_sfuente" => $arr_nom_sfuente,
					 "desc" => $arr_desc,
					 "fecha" => $arr_fecha,
					 "medio" => $arr_medio,
					 "num"=>$f);
	}	
	
	/**
	* Consulta las descripciones de un evento
	* @access public
	* @param int $id_evento ID del evento
	* @return array Arreglo con las variables
	*/			
	function getDescripcionEvento ($id_evento){
		
		$scat_dao = New SubCatEventoConflictoDAO();
		$cat_dao = New CatEventoConflictoDAO();
		
		$arr_id = array();
		$arr_id_cat = array();
		$arr_nom_cat = array();
		$arr_id_scat = array();
		$arr_nom_scat = array();
		$arr_victimas = array();
		
		$vict_total = 0;
		$n = 0;
		$sql = "SELECT * FROM descripcion_evento WHERE ID_EVEN = $id_evento";
		$rs = $this->conn->OpenRecordset($sql);
		while ($row_rs = $this->conn->FetchObject($rs)){
			array_push($arr_id,$row_rs->ID_DESEVEN);
			
			$scat = $scat_dao->get($row_rs->ID_SCATEVEN);
			array_push($arr_id_scat,$row_rs->ID_SCATEVEN);
			array_push($arr_nom_scat,$scat->nombre);
			
			array_push($arr_id_cat,$scat->id_cat);
			$vo = $cat_dao->Get($scat->id_cat);
			array_push($arr_nom_cat,$vo->nombre);
			
			$sql = "SELECT count(id_victima) FROM victima WHERE id_deseven = ".$row_rs->ID_DESEVEN;
			$rs_v = $this->conn->OpenRecordset($sql);
			$row_rs_v = $this->conn->FetchRow($rs_v);
			array_push($arr_victimas,$row_rs_v[0]);
			$vict_total += $row_rs_v[0];
			
			$n++;
		}
		return array("id" => $arr_id, 
					 "id_cat" => $arr_id_cat,
					 "nom_cat" => $arr_nom_cat,
					 "id_scat" => $arr_id_scat, 
					 "nom_scat" => $arr_nom_scat, 
					 "num" => $n, 
					 "num_victimas" => $arr_victimas, 
					 "num_victimas_total" => $vict_total);
	}
	
	/**
	* Consulta las victimas por descripción
	* @access public
	* @param int $id_deseven ID de la descripción evento
	* @return array Arreglo con las variables
	*/			
	function getVictimaDescripcionEvento ($id_deseven){
		
		$scat_dao = New SubCatEventoConflictoDAO();
		$setnia_dao = New SubEtniaDAO();
		$edad_dao = New EdadDAO();
		$rango_edad_dao = New RangoEdadDAO();
		$estado_dao = New EstadoMinaDAO();
		$condicion_dao = New CondicionMinaDAO();
		$subcondicion_dao = New SubCondicionDAO();
		$sexo_dao = New SexoDAO();
		$etnia_dao = New EtniaDAO();
		$sub_etnia_dao = New SubEtniaDAO();
		$subetnia_dao = New SubetniaDAO();
		$ocupacion_dao = New OcupacionDAO();
		
		$arr_cant = array();
		$arr_id_edad = array();
		$arr_nom_edad = array();
		$arr_id_redad = array();
		$arr_nom_redad = array();
		$arr_id_sexo = array();
		$arr_nom_sexo = array();
		$arr_id_etnia = array();
		$arr_nom_etnia = array();
		$arr_id_setnia = array();
		$arr_nom_setnia = array();
		$arr_id_condicion = array();
		$arr_nom_condicion = array();
		$arr_id_scondicion = array();
		$arr_nom_scondicion = array();
		$arr_id_estado = array();
		$arr_nom_estado = array();
		$arr_id_ocupacion = array();
		$arr_nom_ocupacion = array();

		$v = 0;
		$sql = "SELECT * FROM victima WHERE ID_DESEVEN = $id_deseven";
		$rs = $this->conn->OpenRecordset($sql);
		while ($row_rs = $this->conn->FetchObject($rs)){
			
			array_push($arr_cant,$row_rs->CANT_VICTIMA);
			
			array_push($arr_id_edad,$row_rs->ID_EDAD);
			if ($row_rs->ID_EDAD > 0){
				$vo = $edad_dao->Get($row_rs->ID_EDAD);
				array_push($arr_nom_edad,$vo->nombre);
			}
			else{
				array_push($arr_nom_edad,"");
			}
			
			array_push($arr_id_redad,$row_rs->ID_RANED);
			if ($row_rs->ID_RANED > 0){
				$vo = $rango_edad_dao->Get($row_rs->ID_RANED);
				array_push($arr_nom_redad,$vo->nombre);
			}
			else{
				array_push($arr_nom_redad,"");
			}
			
			array_push($arr_id_setnia,$row_rs->ID_SUBETNIA);
			if ($row_rs->ID_SUBETNIA > 0){
				$setnia = $setnia_dao->Get($row_rs->ID_SUBETNIA);
				
				array_push($arr_nom_setnia,$setnia->nombre);
				
				array_push($arr_id_etnia,$setnia->id_etnia);
				$vo = $etnia_dao->Get($setnia->id_etnia);
				array_push($arr_nom_etnia,$vo->nombre);
			}
			else{
				array_push($arr_nom_etnia,"");
			}
			
			array_push($arr_id_sexo,$row_rs->ID_SEXO);
			if ($row_rs->ID_SEXO > 0){
				$vo = $sexo_dao->Get($row_rs->ID_SEXO);
				array_push($arr_nom_sexo,$vo->nombre);
			}
			else{
				array_push($arr_nom_sexo,"");
			}
			
			array_push($arr_id_estado,$row_rs->ID_ESTADO);
			if ($row_rs->ID_ESTADO > 0){
				$vo = $estado_dao->Get($row_rs->ID_ESTADO);
				array_push($arr_nom_estado,$vo->nombre);
			}
			else{
				array_push($arr_nom_estado,"");
			}
			
			array_push($arr_id_ocupacion,$row_rs->ID_OCUPACION);
			if ($row_rs->ID_OCUPACION > 0){
				$vo = $ocupacion_dao->Get($row_rs->ID_OCUPACION);
				array_push($arr_nom_ocupacion,$vo->nombre);
			}
			else{
				array_push($arr_nom_ocupacion,"");
			}
			
			array_push($arr_id_condicion,$row_rs->ID_CONDICION);
			if ($row_rs->ID_CONDICION > 0){
				$vo = $condicion_dao->Get($row_rs->ID_CONDICION);
				array_push($arr_nom_condicion,$vo->nombre);
			}
			else{
				array_push($arr_nom_condicion,"");
			}
			
			array_push($arr_id_scondicion,$row_rs->ID_SUBCONDICION);
			if ($row_rs->ID_SUBCONDICION > 0){
				$vo = $subcondicion_dao->Get($row_rs->ID_SUBCONDICION);
				array_push($arr_nom_scondicion,$vo->nombre);
			}
			else{
				array_push($arr_nom_scondicion,"");
			}
			
			$v++;
			
		}
		
		return array("cant" => $arr_cant,
					 "edad" => $arr_id_edad,
					 "nom_edad" => $arr_nom_edad,
					 "redad" => $arr_id_redad,
					 "nom_redad" => $arr_nom_redad,
					 "setnia" => $arr_id_setnia,
					 "nom_setnia" => $arr_nom_setnia,
					 "etnia" => $arr_id_etnia,
					 "nom_etnia" => $arr_nom_etnia,
					 "sexo" => $arr_id_sexo,
					 "nom_sexo" => $arr_nom_sexo,
					 "estado" => $arr_id_estado,
					 "nom_estado" => $arr_nom_estado,
					 "ocupacion" => $arr_id_ocupacion,
					 "nom_ocupacion" => $arr_nom_ocupacion,
					 "condicion" => $arr_id_condicion,
					 "nom_condicion" => $arr_nom_condicion,
					 "scondicion" => $arr_id_scondicion,
					 "nom_scondicion" => $arr_nom_scondicion,
					 "num" => $v
					 );
	}	
	
	/**
	* Inserta un EventoConflicto en la B.D.
	* @access public
	* @param object $evento_vo VO de EventoConflicto que se va a insertar
	* @param int $alert Muestra la alerta JS
	* @param array $num_vict_desc Número de víctimas por descripción
	* @param array $num_actores_desc Número de actores por descripción
	* @param array $num_subactores_desc Número de sub actores por descripción
	* @param array $num_subsubactores_desc Número de sub sub actores por descripción
	*/		
	function Insertar($evento_vo,$alert=0,$num_vict_desc,$num_actores_desc,$num_subactores_desc,$num_subsubactores_desc){
		//DATOS DEL EVENTO
		$sql =  "INSERT INTO ".$this->tabla." (SINTESIS_EVEN,FECHA_REG_EVEN,FECHA_ING_EVEN)";
		//$sql .= " VALUES ('".$evento_vo->sintesis."','".$evento_vo->fecha_evento."',now())";
		$sql .= " VALUES ('".$evento_vo->sintesis."','".$evento_vo->fecha_evento."','".$evento_vo->fecha_ingreso."')";
		
		//echo "$sql<br>";
		
		$this->conn->Execute($sql);
		
		$id_evento = $this->conn->GetGeneratedID();

		$this->InsertarTablasUnion($evento_vo,$id_evento,$num_vict_desc,$num_actores_desc,$num_subactores_desc,$num_subsubactores_desc);
		
//		die();

		if ($alert == 1){
			?>
	    	<script>
	    		alert("Evento insertado con éxito!");
	    		location.href="<?=$this->url;?>";
	    	</script>
	 	  	<?
		}
	}

	/**
	* Inserta las tablas de union para el EventoConflicto en la B.D.
	* @access public
	* @param object $depto_vo VO de EventoConflicto que se va a insertar
	* @param array $num_vict_desc Número de víctimas por descripción
	* @param array $num_actores_desc Número de actores por descripción
	* @param array $num_subactores_desc Número de sub actores por descripción
	* @param array $num_subsubactores_desc Número de sub sub actores por descripción
	*/		
	function InsertarTablasUnion($evento_vo,$id_evento,$num_vict_desc,$num_actores_desc,$num_subactores_desc,$num_subsubactores_desc){

		$actor_dao = New ActorDAO();
		
		//DESCRIPCION EVENTO
		$arr = $evento_vo->id_cat;
		
		$a = 0;
		$num_victimas_acumulado = 0;
		$num_actores_acumulado = 0;
		$num_subactores_acumulado = 0;
		$num_subsubactores_acumulado = 0;
		foreach ($arr as $ar){
			
			$sql = "INSERT INTO descripcion_evento (ID_SCATEVEN,ID_EVEN) VALUES (".$evento_vo->id_subcat[$a].",$id_evento)";
			$this->conn->Execute($sql);

			//echo("Descripcion $sql<br>");
			
			$id_desceven = $this->conn->GetGeneratedID();
			
			//VICTIMAS
			$numero_victimas_x_desc = $num_vict_desc[$a] + 1;
			$hasta = $num_victimas_acumulado + $numero_victimas_x_desc;
			for($d=$num_victimas_acumulado;$d<$hasta;$d++){
			
				if (isset($evento_vo->num_victimas[$d]) && $evento_vo->num_victimas[$d] != ""){
					
					$num_victimas = $evento_vo->num_victimas[$d];
					
					$id_sub_cond = (!isset($evento_vo->id_subcondicion[$d])) ? 0 : $evento_vo->id_subcondicion[$d];
					$id_sub_etnia = (!isset($evento_vo->id_subetnia[$d])) ? 0 : $evento_vo->id_subetnia[$d];
					
					$sql = "INSERT INTO victima
							(ID_SEXO,ID_EDAD,ID_RANED,ID_CONDICION,ID_SUBCONDICION,ID_ESTADO,ID_DESEVEN,ID_SUBETNIA,ID_OCUPACION,CANT_VICTIMA) VALUES 
							(".$evento_vo->id_sexo[$d].",".$evento_vo->id_edad[$d].",".$evento_vo->id_rango_edad[$d].",".$evento_vo->id_condicion[$d].",".$id_sub_cond.",".$evento_vo->id_estado[$d].",$id_desceven,".$id_sub_etnia.",".$evento_vo->id_ocupacion[$d].",$num_victimas)";
					//echo $sql;
//					echo("Victimas$sql<br>");
					$this->conn->Execute($sql);
					
				}
				
			}
			
			$num_victimas_acumulado += $numero_victimas_x_desc;
			
			//ACTORES
			$numero_actores_x_desc = $num_actores_desc[$a];
			$hasta = $num_actores_acumulado + $numero_actores_x_desc;
			for($i=$num_actores_acumulado;$i<$hasta;$i++){
				$ar = $evento_vo->id_actor[$i];
				if ($ar != ''){
					
					//$cod_interno = $actor_dao->getCodigoInterno($ar);
					
					//$sql = "INSERT INTO actor_descevento (ID_ACTOR,ID_DESEVEN,COD_INTERNO_ACTOR) VALUES (".$ar.",".$id_desceven.",$cod_interno)";
					$sql = "INSERT INTO actor_descevento (ID_ACTOR,ID_DESEVEN) VALUES (".$ar.",".$id_desceven.")";
//					echo "Actor $i Descripcion $a:$sql<br>";
					$this->conn->Execute($sql);
				}
			}
			
			$num_actores_acumulado += $numero_actores_x_desc;
			
			//SUB ACTORES
			$numero_subactores_x_desc = $num_subactores_desc[$a];
			$hasta = $num_subactores_acumulado + $numero_subactores_x_desc;
			for($i=$num_subactores_acumulado;$i<$hasta;$i++){
				$ar = $evento_vo->id_subactor[$i];
				if ($ar != ''){
					$sql = "INSERT INTO actor_descevento (ID_ACTOR,ID_DESEVEN) VALUES (".$ar.",".$id_desceven.")";
//					echo "Sub Actor $i Descripcion $a:$sql<br>";
					$this->conn->Execute($sql);
				}
			}
			
			$num_subactores_acumulado += $numero_subactores_x_desc;
			
			//SUB SUB ACTORES
			$numero_subsubactores_x_desc = $num_subsubactores_desc[$a];
			$hasta = $num_subsubactores_acumulado + $numero_subsubactores_x_desc;
			for($i=$num_subsubactores_acumulado;$i<$hasta;$i++){
				$ar = $evento_vo->id_subsubactor[$i];
				if ($ar != ''){
					$sql = "INSERT INTO actor_descevento (ID_ACTOR,ID_DESEVEN) VALUES (".$ar.",".$id_desceven.")";
//					echo "Sub Sub Actor $i Descripcion $a:$sql<br>";
					$this->conn->Execute($sql);
				}
			}
			
			$num_subsubactores_acumulado += $numero_subsubactores_x_desc;
			
			
			$a++;
		}
		
		//FUENTE EVENTO
		$arr = $evento_vo->id_subfuente;
		
		$a = 0;
		foreach ($arr as $ar){
			$sql = "INSERT INTO fuen_evento 
					(ID_SFUEVEN,ID_EVEN,FECHA_FUENTE,DESEVEN_FUENTE,REFER_FUENTE) VALUES 
					($ar,$id_evento,'".$evento_vo->fecha_fuente[$a]."','".$evento_vo->desc_fuente[$a]."','".$evento_vo->refer_fuente[$a]."')";
			
//			echo $sql;
			$this->conn->Execute($sql);
			
			$a++;
		}
		
		//LOCALIZACION
		$arr = $evento_vo->id_mun;
		$a = 0;
		foreach ($arr as $ar){
			$sql = "INSERT INTO evento_localizacion 
					(ID_MUN,ID_EVEN,LUGAR) VALUES 
					('$ar',$id_evento,'".$evento_vo->lugar[$a]."')";
			
//			echo "Mun:$sql<br>";
			$this->conn->Execute($sql);
			
			$a++;
			
		}
	}


	/**
	* Actualiza un EventoConflicto en la B.D.
	* @access public
	* @param object $evento_vo VO de EventoConflicto que se va a insertar
	* @param int $alert Muestra la alerta JS
	* @param array $num_vict_desc Número de víctimas por descripción
	* @param array $num_actores_desc Número de actores por descripción
	* @param array $num_subactores_desc Número de sub actores por descripción
	* @param array $num_subsubactores_desc Número de sub sub actores por descripción
	*/		
	function Actualizar($evento_vo,$alert=0,$num_vict_desc,$num_actores_desc,$num_subactores_desc,$num_subsubactores_desc){
		//DATOS DEL EVENTO
		$sql =  "UPDATE ".$this->tabla." SET
										SINTESIS_EVEN = '".$evento_vo->sintesis."',
										FECHA_REG_EVEN = '".$evento_vo->fecha_evento."' WHERE id_even = ".$evento_vo->id;
		//echo "$sql<br>";
		
		$this->conn->Execute($sql);
		
		$id_evento = $evento_vo->id;

		$this->BorrarTablasUnion($id_evento);
		$this->InsertarTablasUnion($evento_vo,$id_evento,$num_vict_desc,$num_actores_desc,$num_subactores_desc,$num_subsubactores_desc);
		
//		die();

		if ($alert == 1){
			?>
	    	<script>
	    		alert("Evento actualizado con éxito!");
	    		location.href="index.php?f_ini=<?=$_SESSION['f_ini']?>&f_fin=<?=$_SESSION['f_fin']?>&class=EventoConflictoDAO&method=ListarTabla&param=&accion=listar&consultar=Consultar";
	    	</script>
	 	  	<?
		}
	}

	/**
  * Borra un EventoConflicto en la B.D.
  * @access public
  * @param int $id ID del EventoConflicto que se va a borrar de la B.D
  */	
	function Borrar($id){

		//BORRA TABLAS DE UNION
		$this->BorrarTablasUnion($id);

		//BORRA EL EVENTO
		$sql = "DELETE FROM ".$this->tabla." WHERE ".$this->columna_id." = ".$id;
		$this->conn->Execute($sql);

	}

	/**
  * Borra las tablas de union de un EventoConflicto en la B.D.
  * @access public
  * @param int $id ID del EventoConflicto que se va a borrar de la B.D
  */	
	function BorrarTablasUnion($id){

		$sql = "SELECT id_deseven FROM descripcion_evento WHERE ".$this->columna_id." = ".$id;
		$rs = $this->conn->OpenRecordset($sql);
		while ($row = $this->conn->FetchRow($rs)){
			//ACTOR
			$sql = "DELETE FROM actor_descevento WHERE id_deseven = ".$row[0];
			$this->conn->Execute($sql);
	
			//VICTIMA
			$sql = "DELETE FROM victima WHERE id_deseven = ".$row[0];
			$this->conn->Execute($sql);
		}
		
		//DESCRIPCION
		$sql = "DELETE FROM descripcion_evento WHERE ".$this->columna_id." = ".$id;
		$this->conn->Execute($sql);
		

		//LOCALIZACION
		$sql = "DELETE FROM evento_localizacion WHERE ".$this->columna_id." = ".$id;
		$this->conn->Execute($sql);

		//FUENTE
		$sql = "DELETE FROM fuen_evento WHERE ".$this->columna_id." = ".$id;
		$this->conn->Execute($sql);
		
	}

	/**
  * Muestra la Información completa de una Organización
  * @access public
  * @param id $id Id de la Proyecto
  */			
	function Ver($id){

		//INICIALIZACION DE VARIABLES
		$depto_dao = New DeptoDAO();
		$mun_dao = New MunicipioDAO();
		$tipo_dao = New TipoEventoConflictoDAO();
		$actor_dao = New ActorDAO();
		$cat_tipo_dao = New CatTipoEventoConflictoDAO();
		$cons_hum_dao = New ConsHumDAO();
		$cons_hum_vo = New ConsHum();
		$riesgo_hum_dao = New RiesgoHumDAO();
		$riesgo_hum_vo = New RiesgoHum();


		//CONSULTA LA INFO DE LA ORG.
		$arr_vo = $this->Get($id);

		echo "<table cellspacing=1 cellpadding=3 class='tabla_consulta' border=0 align='center'>";
		echo "<tr class='titulo_lista'><td align='center' colspan='6'>INFORMACION DEL EVENTO</td></tr>";

		echo "<tr><td class='tabla_consulta'><b>Departamento</b></td>";
		echo "<td class='tabla_consulta'>";
		$z = 0;
		foreach($arr_vo->id_deptos as $id){
			$vo = $depto_dao->Get($id);

			echo "- ".$vo->nombre."<br>";
			$z++;
		}
		echo "</td></tr>";

		echo "<tr><td class='tabla_consulta'><b>Municipio</b></td>";
		echo "<td class='tabla_consulta'>";
		$z = 0;
		foreach($arr_vo->id_muns as $id){
			$vo = $mun_dao->Get($id);

			echo "- ".$vo->nombre."<br>";
			$z++;
		}
		echo "</td></tr>";

		echo "<tr><td class='tabla_consulta' width='150'><b>Lugar</b></td><td class='tabla_consulta' width='500'>".$arr_vo->lugar."</td></tr>";

		echo "<tr><td class='tabla_consulta'><b>Tipo de EventoConflicto</b></td>";
		echo "<td class='tabla_consulta'>";
		$z = 0;
		foreach($arr_vo->id_tipo as $id){
			$vo = $tipo_dao->Get($id);

			echo "- ".$vo->nombre."<br>";
			$z++;
		}
		echo "</td></tr>";

		echo "<tr><td class='tabla_consulta'><b>Actores</b></td>";
		echo "<td class='tabla_consulta'>";
		$z = 0;
		foreach($arr_vo->id_actores as $id){
			$vo = $actor_dao->Get($id);

			echo "- ".$vo->nombre."<br>";
			$z++;
		}
		echo "</td></tr>";

		echo "<tr><td class='tabla_consulta'><b>Consecuencias Humanitarias</b></td>";
		echo "<td class='tabla_consulta'>";
		$z = 0;
		foreach($arr_vo->id_cons as $id){
			$vo = $cons_hum_dao->Get($id);

			echo "- ".$vo->nombre."<br>";
			$z++;
		}
		echo "</td></tr>";

		echo "<tr><td class='tabla_consulta'><b>Riesgos Humanitarios</b></td>";
		echo "<td class='tabla_consulta'>";
		$z = 0;
		foreach($arr_vo->id_riesgos as $id){
			$vo = $riesgo_hum_dao->Get($id);

			echo "- ".$vo->nombre."<br>";
			$z++;
		}
		echo "</td></tr>";

		echo "<tr><td class='tabla_consulta' width='150'><b>Descripción</b></td><td class='tabla_consulta' width='500'>".$arr_vo->desc."</td></tr>";
		echo "<tr><td class='tabla_consulta' width='150'><b>Fecha de registro</b></td><td class='tabla_consulta' width='500'>".$arr_vo->fecha_registro."</td></tr>";

		echo "</table>";

	}

	/**
	* Lista los EventoConflictos en una Tabla
	* @access public
	*/			
	function Reportar(){

		set_time_limit(0);

		//INICIALIZACION DE VARIABLES
		$evento_vo = New EventoConflicto();
		$evento_dao = New EventoConflictoDAO();
		$municipio_vo = New Municipio();
		$municipio_dao = New MunicipioDAO();
		$depto_vo = New Depto();
		$depto_dao = New DeptoDAO();
		$actor_vo = New Actor();
		$actor_dao = New ActorDAO();
		$fuente_vo = New FuenteEventoConflicto();
		$fuente_dao = New FuenteEventoConflictoDAO();
		$subfuente_vo = New SubFuenteEventoConflicto();
		$subfuente_dao = New SubFuenteEventoConflictoDAO();
		$cat_vo = New CatEventoConflicto();
		$cat_dao = New CatEventoConflictoDAO();
		$subcat_vo = New SubCatEventoConflicto();
		$subcat_dao = New SubCatEventoConflictoDAO();
		$edad_dao = New EdadDAO();
		$rango_edad_dao = New RangoEdadDAO();
		$estado_dao = New EstadoMinaDAO();
		$condicion_dao = New CondicionMinaDAO();
		$subcondicion_dao = New SubCondicionDAO();
		$sexo_dao = New SexoDAO();
		$etnia_dao = New EtniaDAO();
		$sub_etnia_dao = New SubEtniaDAO();
		$subetnia_dao = New SubetniaDAO();
		$ocupacion_dao = New OcupacionDAO();
		
		$filtro_cat = 0;
		$filtro_sexo = 0;
		$filtro_condicion = 0;
		$filtro_subcondicion = 0;
		$filtro_edad = 0;
		$filtro_redad = 0;
		$filtro_etnia = 0;
		$filtro_subetnia = 0;
		$filtro_ocupacion = 0;
		$filtro_estado = 0;
		$filtro_fecha = 0;

		$reporte = $_POST["reporte"];
		
		$tit_reporte = array(1 => "Conteo de eventos por Categoría/Subcategoría",
							 2 => "Conteo de eventos por confrontaciones entre dos actores",
							 3 => "Conteo de eventos por periodo de tiempo",
							 6 => "Cantidad de víctimas",
							 7 => "Reporte General de Eventos");
		

		if (isset($_POST["f_ini"]) && $_POST["f_ini"][0] != ''){
			$filtro_fecha = 1;	
		}
		
		$fecha_ini = $_POST["f_ini"];
		$fecha_fin = $_POST["f_fin"];
		
		$nivel_localizacion = $_POST["nivel_localizacion"];
		
		//ACTORES
		$id_actor1 = (isset($_POST["id_actor1"])) ? $_POST["id_actor1"] : array();
		$id_actor2 = (isset($_POST["id_actor2"])) ? $_POST["id_actor2"] : array();

		
		//CAT-SUBCAT
		if (isset($_POST["id_cat"])){

			$filtro_cat = 1;
			$id_cat = $_POST["id_cat"];
			$id_cat_s = implode(",",$id_cat);
			
			$cats = $cat_dao->GetAllArray("id_cateven in ($id_cat_s)");
			
			if (isset($_POST["id_subcat"])){
				$id_subcat = $_POST["id_subcat"];
				$id_subcat_s = implode(",",$id_subcat);
				
				$subtipos = $subcat_dao->GetAllArray("ID_SCATEVEN IN ($id_subcat_s)");
			}
			else{
				$condicion_cat = "ID_CATEVEN IN ($id_cat_s)";
				$vo_cats = $cat_dao->GetAllArray($condicion_cat);
				
				$subtipos = $subcat_dao->GetAllArray($condicion_cat);
				$id_subcat_s = implode(",",$subcat_dao->GetAllArrayID("ID_CATEVEN IN ($id_cat_s)"));
			}
		}
		else{
			$cats = $cat_dao->GetAllArray('');
			$subtipos = $subcat_dao->GetAllArray('');
		}

		if ($reporte == 1){
			$sql_eventos = "SELECT DISTINCT evento_localizacion.id_mun FROM evento_localizacion INNER JOIN evento_c ON evento_localizacion.id_even = evento_c.id_even INNER JOIN municipio ON evento_localizacion.id_mun = municipio.id_mun INNER JOIN departamento ON municipio.id_depto = departamento.id_depto";
			
			if ($filtro_fecha == 1){
				$sql_eventos .= " AND fecha_reg_even BETWEEN '$fecha_ini[0]' AND '$fecha_fin[0]'";
			}
			
 			$sql_eventos .= " ORDER BY nom_depto";
					
			$rs = $this->conn->OpenRecordset($sql_eventos);
			$m = 0;
			while ($row_rs = $this->conn->FetchRow($rs)){
				$id_ubi[$m] = $row_rs[0];
				$m++;
			}
		}
		else if ($reporte == 2){
			
			$sql_eventos = "SELECT DISTINCT evento_localizacion.id_mun FROM evento_localizacion INNER JOIN evento_c ON evento_localizacion.id_even = evento_c.id_even INNER JOIN municipio ON evento_localizacion.id_mun = municipio.id_mun INNER JOIN departamento ON municipio.id_depto = departamento.id_depto";
			
			if ($filtro_fecha == 1){
				$sql_eventos .= " AND fecha_reg_even BETWEEN '$fecha_ini[0]' AND '$fecha_fin[0]'";
			}
			
 			$sql_eventos .= " ORDER BY nom_depto";
					
			$rs = $this->conn->OpenRecordset($sql_eventos);
			$m = 0;
			while ($row_rs = $this->conn->FetchRow($rs)){
				$id_ubi[$m] = $row_rs[0];
				$m++;
			}
		}
		else if ($reporte == 3){
			
			$sql_eventos = "SELECT DISTINCT evento_localizacion.id_mun FROM evento_localizacion INNER JOIN evento_c ON evento_localizacion.id_even = evento_c.id_even INNER JOIN municipio ON evento_localizacion.id_mun = municipio.id_mun INNER JOIN departamento ON municipio.id_depto = departamento.id_depto";
			
 			$sql_eventos .= " ORDER BY nom_depto";
					
			$rs = $this->conn->OpenRecordset($sql_eventos);
			$m = 0;
			while ($row_rs = $this->conn->FetchRow($rs)){
				$id_ubi[$m] = $row_rs[0];
				$m++;
			}
		}
		//CONTEO DE VICTIMAS
		else if ($reporte == 6 || $reporte == 7){
			
			$cat_victima_localizacion = $_POST["cat_victima_localizacion"];
			
			//Filtro de sexo
			if (isset($_POST["id_sexo"])){
				$id = $_POST["id_sexo"];
				$id_sexos = implode(",",$id);
				$sexos = $sexo_dao->GetAllArray("id_sexo IN ($id_sexos)");
				$filtro_sexo = 1;
				
				//Para usar en el sql del reporte 7 - reporte general
				$cond_sql_sexo = " AND id_sexo IN ($id_sexos)";
			}
			else{
				$sexos = $sexo_dao->GetAllArray("");
			}
			
			//Filtro de condicion
			if (isset($_POST["id_condicion"])){
				$filtro_condicion = 1;
				$id = $_POST["id_condicion"];
				$id_condiciones = implode(",",$id);
				
				$cond = "id_condicion_mina IN ($id_condiciones)";
				$condiciones = $condicion_dao->GetAllArray($cond);
				
				//Para usar en el sql del reporte 7 - reporte general
				$cond_sql_condicion = " AND id_condicion IN ($id_condiciones)";
				
				if (isset($_POST["id_subcondicion"])){
					$filtro_subcondicion = 1;
					$id = $_POST["id_subcondicion"];
					$id_subcondiciones = implode(",",$id);
					
					//Para usar en el sql del reporte 7 - reporte general
					$cond_sql_subcondicion = " AND id_subcondicion IN ($id_subcondiciones)";
					
					$subcondiciones = $subcondicion_dao->GetAllArray("id_subcondicion IN ($id_subcondiciones)");
				}
				else{
					$subcondiciones = $subcondicion_dao->GetAllArray("id_condicion IN ($id_condiciones)");
				}
			}
			else{
				$condiciones = $condicion_dao->GetAllArray('');
				$subcondiciones = $subcondicion_dao->GetAllArray('');
			}
			
			//Filtro de edad
			if (isset($_POST["id_edad"])){
				$filtro_edad = 1;
				$id = $_POST["id_edad"];
				$id_edades = implode(",",$id);
				
				$cond = "id_edad IN ($id_edades)";
				$edades = $edad_dao->GetAllArray($cond);
				
				//Para usar en el sql del reporte 7 - reporte general
				$cond_sql_edad = " AND id_edad IN ($id_edades)";
				
				if (isset($_POST["id_rango_edad"])){
					$filtro_redad = 1;
					$id = $_POST["id_rango_edad"];
					$id_rango_edades = implode(",",$id);
					
					//Para usar en el sql del reporte 7 - reporte general
					$cond_sql_redad = " AND id_raned IN ($id_rango_edades)";
					
					$rangos_edades = $rango_edad_dao->GetAllArray("id_raned IN ($id_rango_edades)");
				}
				else{
					$rangos_edades = $rango_edad_dao->GetAllArray($cond);
				}
			}
			else{
				$edades = $edad_dao->GetAllArray('');
				$rangos_edades = $rango_edad_dao->GetAllArray('');
			}
			
			//Filtro de etnia
			if (isset($_POST["id_etnia"])){
				$filtro_etnia = 1;
				$id = $_POST["id_etnia"];
				$id_etnias = implode(",",$id);
				
				$cond = "id_etnia IN ($id_etnias)";
				$etnias = $etnia_dao->GetAllArray($cond);
				
				if (isset($_POST["id_subetnia"])){
					$filtro_subetnia = 1;
					$id = $_POST["id_subetnia"];
					$id_subetnias = implode(",",$id);
					
					//Para usar en el sql del reporte 7 - reporte general
					$cond_sql_setnia = " AND id_subetnia IN ($id_subetnias)";
					
					$subetnias = $sub_etnia_dao->GetAllArray("id_etnia IN ($id_subetnias)");
				}
				else{
					$subetnias = $sub_etnia_dao->GetAllArray($cond);
					$id_subetnias = implode(",",$sub_etnia_dao->GetAllArrayID($cond));
				}
			}
			else{
				$etnias = $etnia_dao->GetAllArray('');
				$subetnias = $sub_etnia_dao->GetAllArray('');
			}
			
			//Filtro de estado
			if (isset($_POST["id_estado"])){
				$id = $_POST["id_estado"];
				$id_estados = implode(",",$id);
				$estados = $estado_dao->GetAllArray("id_estado_mina IN ($id_estados)");
				$filtro_estado = 1;
				
				//Para usar en el sql del reporte 7 - reporte general
				$cond_sql_estado = " AND id_estado IN ($id_estados)";
				
			}
			else{
				$estados = $estado_dao->GetAllArray("");
			}
			
			//Filtro de ocupacion
			if (isset($_POST["id_ocupacion"])){
				$id = $_POST["id_ocupacion"];
				$id_ocupaciones = implode(",",$id);
				$ocupaciones = $ocupacion_dao->GetAllArray("id_ocupacion IN ($id_ocupaciones)");
				$filtro_ocupacion = 1;
				
				//Para usar en el sql del reporte 7 - reporte general
				$cond_sql_ocupacion = " AND id_ocupacion IN ($id_ocupaciones)";
				
			}
			else{
				$ocupaciones = $ocupacion_dao->GetAllArray("");
			}
			
			if ($reporte == 6){
			
				$sql_eventos = "SELECT DISTINCT evento_localizacion.id_mun FROM evento_localizacion INNER JOIN evento_c ON evento_localizacion.id_even = evento_c.id_even INNER JOIN municipio ON evento_localizacion.id_mun = municipio.id_mun INNER JOIN departamento ON municipio.id_depto = departamento.id_depto";
				
				if ($filtro_fecha == 1){
					$sql_eventos .= " AND fecha_reg_even BETWEEN '$fecha_ini[0]' AND '$fecha_fin[0]'";
				}
				
	 			$sql_eventos .= " ORDER BY nom_depto";
						
				$rs = $this->conn->OpenRecordset($sql_eventos);
				$m = 0;
				while ($row_rs = $this->conn->FetchRow($rs)){
					$id_ubi[$m] = $row_rs[0];
					$m++;
				}
			}
			else {
				$sql_eventos = "SELECT * FROM evento_c INNER JOIN descripcion_evento ON evento_c.id_even = descripcion_evento.id_even INNER JOIN evento_localizacion ON evento_c.id_even = evento_localizacion.id_even INNER JOIN victima ON victima.id_deseven = descripcion_evento.id_deseven WHERE 1=1 ";
	
				$cond_eventos = "";
				if (isset($_POST["id_cat"])){
					$cond_eventos .= " AND id_scateven IN ($id_subcat_s)";
				}
				
				if (isset($_POST["id_depto"]) && !isset($_POST["id_muns"])){
					$id_depto = $_POST["id_depto"];
					$d = 0;
					foreach ($id_depto as $id_d){
						$id_depto[$d] = "'".$id_d."'";
						$d++;
					}
					$id_depto_s = implode(",",$id_depto);
					
					$id_muns_s = $municipio_dao->GetIDWhere($id_depto_s);
					
					$cond_eventos .= " AND id_mun IN ($id_muns_s)";
				}
				else if (isset($_POST["id_depto"]) && isset($_POST["id_muns"])){
					$arr_id_u_g = Array();
		
					$id_muns = $_POST["id_muns"];
					$m = 0;
					foreach ($id_muns as $id_m){
						$id_muns[$m] = "'".$id_m."'";
						$m++;
					}
		
					$id_muns_s = implode(",",$id_muns);
					
					$cond_eventos .= " AND id_mun IN ($id_muns_s)";
				}
				
				if ($filtro_fecha == 1){
					$cond_eventos .= " AND fecha_reg_even BETWEEN '$fecha_ini[0]' AND '$fecha_fin[0]'";
				}
				
				//Filtros Victimas
				if ($filtro_sexo == 1)	$cond_eventos .= $cond_sql_sexo;
				if ($filtro_condicion == 1)	$cond_eventos .= $cond_sql_condicion;
				if ($filtro_subcondicion == 1)	$cond_eventos .= $cond_sql_subcondicion;
				if ($filtro_edad == 1)	$cond_eventos .= $cond_sql_edad;
				if ($filtro_redad == 1)	$cond_eventos .= $cond_sql_redad;
				if ($filtro_subetnia == 1)	$cond_eventos .= $cond_sql_setnia;
				if ($filtro_estado == 1)	$cond_eventos .= $cond_sql_estado;
				if ($filtro_ocupacion == 1)	$cond_eventos .= $cond_sql_ocupacion;
				
					
				$sql_eventos .= $cond_eventos ." ORDER BY fecha_reg_even AND id_mun DESC";
				
				//echo $sql_eventos;
				$m = 1;  //Temporal para que entre en el if
			}
		}
		else if ($reporte == 4 || $reporte == 5){
			$m = 1;			
		}


		echo "<table align='center' cellspacing='1' cellpadding='3' border=0 width='100%'>";
		if ($m > 0){
			echo "<tr><td><a href='javascript:history.back(-1)'><img src='images/back.gif' border=0>&nbsp;Regresar</a>";
			
			if ($reporte != 7)	echo "&nbsp;<a href=\"#\" onclick=\"document.getElementById('pdf').value = 2;reportStream('evento_c');return false;\"><img src='images/consulta/excel.gif' border=0 class='TipExport' title='Exportar a Excel::Se genera un archivo descargable en formato xls'>&nbsp;Exportar a Excel</a></td>";
			
			echo "</tr>";
		}
		echo "<tr><td align='center' class='titulo_lista' colspan=30>CONSULTA DE EVENTOS</td></tr>";
		echo "<tr><td><b>REPORTE: ".strtoupper($tit_reporte[$reporte])."</b></td></tr>";
		
		if (isset($_POST["id_cat"]) || $filtro_fecha == 1 || isset($_POST["id_depto"]) || $filtro_condicion == 1
			|| $filtro_edad == 1 || $filtro_estado == 1 || $filtro_etnia == 1 || $filtro_ocupacion == 1){
		
			echo "<tr><td colspan=5>Consulta realizada aplicando los siguientes filtros:</td>";
			echo "<tr><td colspan=5>";
	
			//TITULO DE CATEGORIA
			if (isset($_POST["id_cat"])){
				echo "<img src='images/flecha.gif'> Categor&iacute;a: ";
				$t = 0;
				foreach($id_cat as $id){
					$vo  = $cat_dao->Get($id);
					if ($t == 0)	echo "<b>".$vo->nombre."</b>";
					else			echo ", <b>".$vo->nombre."</b>";
					$t++;
				}
				echo "<br>";
			}
			//FECHA
			if ($filtro_fecha == 1){
				echo "<img src='images/flecha.gif'> Fecha Desde: <b>".$fecha_ini[0]."</b> -- Fecha Hasta: <b>".$fecha_fin[0]."</b>";
				echo "<br>";
			}
			if ($filtro_sexo == 1){
				echo "<img src='images/flecha.gif'> Sexo: ";
				$t = 0;
				foreach ($sexos as $vo){
					echo ($t == 0) ? "<b>".$vo->nombre."</b>" : ", <b>".$vo->nombre."</b>";
					echo "<br>";
					$t++;
				}
			}
			if ($filtro_condicion == 1){
				echo "<img src='images/flecha.gif'> Condici&oacute;n: ";
				$t = 0;
				foreach ($condiciones as $vo){
					echo ($t == 0) ? "<b>".$vo->nombre."</b>" : ", <b>".$vo->nombre."</b>";
					echo "<br>";
					$t++;
				}
			}
			if ($filtro_subcondicion == 1){
				echo "<img src='images/flecha.gif'> Subcondici&oacute;n: ";
				$t = 0;
				foreach ($subcondiciones as $vo){
					echo ($t == 0) ? "<b>".$vo->nombre."</b>" : ", <b>".$vo->nombre."</b>";
					echo "<br>";
					$t++;
				}
			}
			if ($filtro_edad == 1){
				echo "<img src='images/flecha.gif'> Grupo Etareo: ";
				$t = 0;
				foreach ($edades as $vo){
					echo ($t == 0) ? "<b>".$vo->nombre."</b>" : ", <b>".$vo->nombre."</b>";
					echo "<br>";
					$t++;
				}
			}
			if ($filtro_redad == 1){
				echo "<img src='images/flecha.gif'> Rango de Edad: ";
				$t = 0;
				foreach ($rangos_edades as $vo){
					echo ($t == 0) ? "<b>".$vo->nombre."</b>" : ", <b>".$vo->nombre."</b>";
					echo "<br>";
					$t++;
				}
			}
			if ($filtro_estado == 1){
				echo "<img src='images/flecha.gif'> Estado: ";
				$t = 0;
				foreach ($estados as $vo){
					echo ($t == 0) ? "<b>".$vo->nombre."</b>" : ", <b>".$vo->nombre."</b>";
					echo "<br>";
					$t++;
				}
			}
			if ($filtro_etnia == 1){
				echo "<img src='images/flecha.gif'> Grupo Poblacional: ";
				$t = 0;
				foreach ($etnias as $vo){
					echo ($t == 0) ? "<b>".$vo->nombre."</b>" : ", <b>".$vo->nombre."</b>";
					echo "<br>";
					$t++;
				}
			}
			if ($filtro_subetnia == 1){
				echo "<img src='images/flecha.gif'> Etnia: ";
				$t = 0;
				foreach ($subetnias as $vo){
					echo ($t == 0) ? "<b>".$vo->nombre."</b>" : ", <b>".$vo->nombre."</b>";
					echo "<br>";
					$t++;
				}
			}
			if ($filtro_ocupacion == 1){
				echo "<img src='images/flecha.gif'> Ocupaci&oacute;n: ";
				$t = 0;
				foreach ($ocupaciones as $vo){
					echo ($t == 0) ? "<b>".$vo->nombre."</b>" : ", <b>".$vo->nombre."</b>";
					echo "<br>";
					$t++;
				}
			}
			//TITULO DE DEPTO
			if (isset($_POST["id_depto"])){
				echo "<img src='images/flecha.gif'> Departamento: ";
				$t = 0;
				foreach($_POST["id_depto"] as $id_t){
					$vo  = $depto_dao->Get($id_t);
					if ($t == 0)	echo "<b>".$vo->nombre."</b>";
					else			echo ", <b>".$vo->nombre."</b>";
					$t++;
				}
				echo "<br>";
			}
			//TITULO DE MPIO
			if (isset($_POST["id_muns"])){
				echo "<img src='images/flecha.gif'> Municipio: ";
				$t = 0;
				foreach($_POST["id_muns"] as $id_t){
					$vo  = $municipio_dao->Get($id_t);
					if ($t == 0)	echo "<b>".$vo->nombre."</b>";
					else			echo ", <b>".$vo->nombre."</b>";
					$t++;
				}
				echo "<br>";
			}
			echo "</td>";
		}

		echo '</tr></table>';
		
		echo '<div style="overflow:auto; margin-top:5px; padding:3px; height:350px;>';
		//echo '<code>';
		
		ob_start();
		
		?>
		<STYLE TYPE="text/css"><!--
		.excel_celda_texto {mso-style-parent:text; mso-number-format:"\@";white-space:normal;}
		--></STYLE>
		<?
		if ($m > 0){

			echo "<tr><td colspan=3><table class='tabla_reportelist'>";
			
			if ($reporte == 1 || $reporte == 2 || $reporte == 3 || $reporte == 6){
				
				$cols_loca = ($nivel_localizacion == 'deptal') ? 2 : 4;

				echo "<tr class='titulo_lista'><td class='titulo_lista_localizacion_evento' colspan=$cols_loca>LOCALIZACION GEOGRAFICA</td>";
				
				if ($reporte == 1){
					foreach ($cats as $vo){
						$num_subcats = $subcat_dao->numRecords("id_cateven = $vo->id");
						echo "<td align='center' colspan=$num_subcats>$vo->nombre</td>";
					}
				}
				
				else if ($reporte == 6){
					switch ($cat_victima_localizacion){
						case 'no':
							foreach ($cats as $vo){
								$num_subcats = $subcat_dao->numRecords("id_cateven = $vo->id");
								echo "<td align='center' colspan=$num_subcats>$vo->nombre</td>";
							}
						break;
						case 'sexo':
							$num = $sexo_dao->numRecords('');
							echo "<td align='center' colspan=$num>SEXO</td>";
						break;
						case 'condicion':
							foreach ($condiciones as $vo){
								$num = $subcondicion_dao->numRecords("id_condicion = $vo->id");
								echo "<td align='center' colspan=$num>$vo->nombre</td>";
							}
						break;
						case 'edad':
							foreach ($edades as $vo){
								$num = $rango_edad_dao->numRecords("id_edad = $vo->id");
								echo "<td align='center' colspan=$num>$vo->nombre</td>";
							}
						break;
						case 'ocupacion':
							$num = $ocupacion_dao->numRecords('');
							echo "<td align='center' colspan=$num>OCUPACION</td>";
						break;
						case 'estado':
							$num = $estado_dao->numRecords('');
							echo "<td align='center' colspan=$num>ESTADO</td>";
						break;
						case 'etnia':
							foreach ($etnias as $vo){
								$num = $subetnia_dao->numRecords("id_etnia = $vo->id");
								echo "<td align='center' colspan=$num>$vo->nombre</td>";
							}
						break;
					}
				}
					
				echo "</tr>";
				
				if ($nivel_localizacion == 'deptal'){
					echo "<tr class='fila_lista'><td class='titulo_lista_localizacion_evento'>COD. DEPTO</td><td class='titulo_lista_localizacion_evento'>Departamento</td>";
				}
				else{
					echo "<tr class='fila_lista'><td class='titulo_lista_localizacion_evento'>COD. DEPTO</td><td class='titulo_lista_localizacion_evento'>Departamento</td><td class='titulo_lista_localizacion_evento'>COD. MPIO</td><td class='titulo_lista_localizacion_evento'>Municipio</td>";
				}
			}
				
			//DETALLE POR PERIODO
			if ($reporte == 4 || $reporte == 5){
				echo "<tr class='titulo_lista'><td><b>PERIODO</b></td>";
			}
			
			
			//LOC_GEOGRAFICA - CAT-SUBCAT
			if ($reporte == 1){
				foreach ($subtipos as $vo){
					echo "<td align='center'>$vo->nombre</td>";
				}
			}
			//LOC_GEOGRAFICA - ACTORES
			else if ($reporte == 2 || $reporte == 5){
				$a = 0;
				foreach ($id_actor1 as $id_actor){
					$vo = $actor_dao->Get($id_actor);
					$vo1 = $actor_dao->Get($id_actor2[$a]);
					echo "<td align='center'><b>$vo->nombre</b> - <b>$vo1->nombre</b></td>";
					
					$a++;
				}
			}
			//LOC_GEOGRAFICA - PERIODOS
			else if ($reporte == 3){
				$a = 0;
				foreach ($fecha_ini as $f_ini){
					echo "<td align='center'><b>$f_ini</b> - <b>$fecha_fin[$a]</b></td>";
					
					$a++;
				}
			}
			//PERIODO POR CATEGORIA
			else if ($reporte == 4){
				
				if ($filtro_cat == 0){
					$vo_cats = $cat_dao->GetAllArray('');
				}
				foreach($vo_cats as $vo){
					echo "<td align='center'>$vo->nombre</td>";
				}
			}
			
			else if ($reporte == 6){
				switch ($cat_victima_localizacion){
					case 'no':
						foreach ($subtipos as $vo){
							echo "<td align='center'>$vo->nombre</td>";
						}
					break;
					case 'sexo':
						foreach ($sexos as $vo){
							echo "<td align='center'>$vo->nombre</td>";
						}
					break;
					case 'condicion':
						foreach ($subcondiciones as $vo){
							echo "<td align='center'>$vo->nombre</td>";
						}
					break;
					case 'edad':
						foreach ($rangos_edades as $vo){
							echo "<td align='center'>$vo->nombre</td>";
						}
					break;
					case 'etnia':
						foreach ($subetnias as $vo){
							echo "<td align='center'>$vo->nombre</td>";
						}
					break;
					case 'estado':
						foreach ($estados as $vo){
							echo "<td align='center'>$vo->nombre</td>";
						}
					break;
					case 'ocupacion':
						foreach ($ocupaciones as $vo){
							echo "<td align='center'>$vo->nombre</td>";
						}
					break;
					
				}
			}
			else if ($reporte == 7){
				$this->ReportarDepuracion($cond_eventos);
				die;
			}
			
			echo "<tr>";
			
			
			if ($reporte == 1 || $reporte == 2 || $reporte == 3 || $reporte == 6){
			
				$hay = 1;
				$id_depto_ant = 0;
				foreach ($id_ubi as $id_ubi){
					
					$id_depto = substr($id_ubi,0,2);
					
					$esta = (!isset($_POST["id_muns"]) && !isset($_POST["id_depto"])) ? 1 : 0;
					
					if (isset($_POST["id_muns"]) && in_array($id_ubi,$_POST["id_muns"])){
						$esta = 1;
					}
					else if (isset($_POST["id_depto"])){
						
						if (in_array($id_depto,$_POST["id_depto"])){
							$esta = 1;
						}
					}
					
					if ($esta == 1){
						
						$depto = $depto_dao->Get($id_depto);
						$mun = $municipio_dao->Get($id_ubi);
						
						$id_depto_linea = $depto->id;
						
						if ($nivel_localizacion == 'mpal'){
							echo "<tr class='fila_lista'>";
							echo "<td class='excel_celda_texto'>$depto->id</td>";
							echo "<td>$depto->nombre</td>";
							echo "<td class='excel_celda_texto'>$mun->id</td>";
							echo "<td>$mun->nombre</td>";
						}
												
						//CAT-SUBCAT
						if ($reporte == 1){
							foreach ($subtipos as $vo){
								
								if ($id_depto_linea != $id_depto_ant){
									$total[$id_depto][$vo->id] = 0;
								}
								
								$num_eventos = $this->numEventosReporte($id_ubi,$vo->id,0,$fecha_ini[0],$fecha_fin[0]);
								if ($nivel_localizacion == 'mpal'){
									echo "<td align='center'>$num_eventos</td>";
								}
								else {
									$total[$id_depto][$vo->id] += $num_eventos;
								}
							}
						}
						//CONFRONTACION DE ACTORES
						else if ($reporte == 2){
							$a = 0;
							foreach ($id_actor1 as $id_actor){
								$vo = $actor_dao->Get($id_actor);
								$vo1 = $actor_dao->Get($id_actor2[$a]);
								
								if ($id_depto_linea != $id_depto_ant){
									$total[$id_depto][$vo->id] = 0;
								}
								
								$num_eventos = $this->numEventosReporte($id_ubi,0,"$vo->id,$vo1->id",$fecha_ini[0],$fecha_fin[0]);
								
								if ($nivel_localizacion == 'mpal'){
									echo "<td align='center'>$num_eventos</td>";
								}
								else {
									$total[$id_depto][$vo->id] += $num_eventos;
								}
								
								$a++;
							}
						}
						//POR PERIODO DE TIEMPO
						else if ($reporte == 3){
							$a = 0;
							foreach ($fecha_ini as $f_ini){
								
								if ($id_depto_linea != $id_depto_ant){
									$total[$id_depto][$a] = 0;
								}
								
								$num_eventos = $this->numEventosReporte($id_ubi,0,0,$f_ini,$fecha_fin[$a]);
								
								if ($nivel_localizacion == 'mpal'){
									echo "<td align='center'>$num_eventos</td>";
								}
								else {
									$total[$id_depto][$a] += $num_eventos;
								}
								
								$a++;
							}
						}
						
						//CONTEO DE VICTIMAS
						else if ($reporte == 6){
							
							$a = 0;
							$filtros['id_mun'] = $id_ubi;
							$filtros['f_ini'] = $fecha_ini[0];
							$filtros['f_fin'] = $fecha_fin[0];

							if ($cat_victima_localizacion == 'no'){
								
								if ($filtro_sexo == 1)	$filtros['id_sexo'] = $id_sexos;
								if ($filtro_condicion == 1)	$filtros['id_condicion'] = $id_condiciones;
								if ($filtro_subcondicion == 1)	$filtros['id_subcondicion'] = $id_subcondiciones;
								if ($filtro_edad == 1)	$filtros['id_edad'] = $id_edades;
								if ($filtro_redad == 1)	$filtros['id_rango_edad'] = $id_rango_edades;
								if ($filtro_etnia == 1)	$filtros['id_etnia'] = $id_etniaes;
								if ($filtro_subetnia == 1)	$filtros['id_subetnia'] = $id_subetnias;
								if ($filtro_estado == 1)	$filtros['id_estado'] = $id_estados;
								if ($filtro_ocupacion == 1)	$filtros['id_ocupacion'] = $id_ocupaciones;
								
								foreach ($subtipos as $vo){

									if ($id_depto_linea != $id_depto_ant){
										$total[$id_depto][$vo->id] = 0;
									}
									
									$filtros['id_scat'] = $vo->id;
									
									$num_victimas = $this->numVictimasReporte($id_ubi,$filtros);
									
									if ($nivel_localizacion == 'mpal'){
										echo "<td align='center'>$num_victimas</td>";
									}
									else {
										$total[$id_depto][$vo->id] += $num_victimas;
									}
									
									$a++;
								}
							}
							
							else if ($cat_victima_localizacion == 'sexo'){
								foreach ($sexos as $vo){
									
									if ($id_depto_linea != $id_depto_ant){
										$total[$id_depto][$vo->id] = 0;
									}
									
									$filtros['id_sexo'] = $vo->id;
									
									$num_victimas = $this->numVictimasReporte($id_ubi,$filtros);
									if ($nivel_localizacion == 'mpal'){
										echo "<td align='center'>$num_victimas</td>";
									}
									else {
										$total[$id_depto][$vo->id] += $num_victimas;
									}
								}
							}
							
							else if ($cat_victima_localizacion == 'condicion'){
								foreach ($subcondiciones as $vo){
									
									if ($id_depto_linea != $id_depto_ant){
										$total[$id_depto][$vo->id] = 0;
									}
									
									$filtros['id_subcondicion'] = $vo->id;
									
									$num_victimas = $this->numVictimasReporte($id_ubi,$filtros);
									if ($nivel_localizacion == 'mpal'){
										echo "<td align='center'>$num_victimas</td>";
									}
									else {
										$total[$id_depto][$vo->id] += $num_victimas;
									}
								}
							}
							
							else if ($cat_victima_localizacion == 'etnia'){
								foreach ($subetnias as $vo){
									
									if ($id_depto_linea != $id_depto_ant){
										$total[$id_depto][$vo->id] = 0;
									}
									
									$filtros['id_subetnia'] = $vo->id;
									
									$num_victimas = $this->numVictimasReporte($id_ubi,$filtros);
									if ($nivel_localizacion == 'mpal'){
										echo "<td align='center'>$num_victimas</td>";
									}
									else {
										$total[$id_depto][$vo->id] += $num_victimas;
									}
								}
							}
							
							else if ($cat_victima_localizacion == 'edad'){
								foreach ($rangos_edades as $vo){
									
									if ($id_depto_linea != $id_depto_ant){
										$total[$id_depto][$vo->id] = 0;
									}
									
									$filtros['id_rango_edad'] = $vo->id;
									
									$num_victimas = $this->numVictimasReporte($id_ubi,$filtros);
									if ($nivel_localizacion == 'mpal'){
										echo "<td align='center'>$num_victimas</td>";
									}
									else {
										$total[$id_depto][$vo->id] += $num_victimas;
									}
								}
							}
							
							else if ($cat_victima_localizacion == 'estado'){
								foreach ($estados as $vo){
									
									if ($id_depto_linea != $id_depto_ant){
										$total[$id_depto][$vo->id] = 0;
									}
									
									$filtros['id_estado'] = $vo->id;
									
									$num_victimas = $this->numVictimasReporte($id_ubi,$filtros);
									if ($nivel_localizacion == 'mpal'){
										echo "<td align='center'>$num_victimas</td>";
									}
									else {
										$total[$id_depto][$vo->id] += $num_victimas;
									}
								}
							}
							
							else if ($cat_victima_localizacion == 'ocupacion'){
								foreach ($ocupaciones as $vo){
									
									if ($id_depto_linea != $id_depto_ant){
										$total[$id_depto][$vo->id] = 0;
									}
									
									$filtros['id_ocupacion'] = $vo->id;
									
									$num_victimas = $this->numVictimasReporte($id_ubi,$filtros);
									if ($nivel_localizacion == 'mpal'){
										echo "<td align='center'>$num_victimas</td>";
									}
									else {
										$total[$id_depto][$vo->id] += $num_victimas;
									}
								}
							}
						}
						
						$id_depto_ant = $id_depto_linea;
						
						$hay++;
						
						echo "</tr>";
						
					}
				}
				
				if ($nivel_localizacion == 'deptal'){
					
					foreach ($total as $id_depto => $valores){
						
						$depto = $depto_dao->Get($id_depto);
						echo "<tr class='fila_lista'>";
						echo "<td class='excel_celda_texto'>$depto->id</td>";
						echo "<td>$depto->nombre</td>";
					
					//TOTAL PARA DEPTO
						//CAT-SUBCAT
						if ($reporte == 1){
							foreach ($subtipos as $vo){
								echo "<td align='center'>".$valores[$vo->id]."</td>";
							}
						}
						else if ($reporte == 2){
							foreach ($id_actor1 as $id_actor){
								$vo = $actor_dao->Get($id_actor);
								echo "<td align='center'>".$valores[$vo->id]."</td>";
							}
						}
						else if ($reporte == 3){
							$a = 0;
							foreach ($fecha_ini as $f_ini){
								echo "<td align='center'>".$valores[$a]."</td>";
								
								$a++;
							}
						}
						
						//CONTEO DE VICTIMAS
						else if ($reporte == 6){
							
							$a = 0;
							$filtros['id_mun'] = $id_ubi;
							$filtros['f_ini'] = $fecha_ini[0];
							$filtros['f_fin'] = $fecha_fin[0];

							if ($cat_victima_localizacion == 'no'){
								foreach ($subtipos as $vo){
									echo "<td align='center'>".$valores[$vo->id]."</td>";
								}
							}
							else if ($cat_victima_localizacion == 'sexo'){
								foreach ($sexos as $vo){
									echo "<td align='center'>".$valores[$vo->id]."</td>";
								}
							}
							
							else if ($cat_victima_localizacion == 'condicion'){
								foreach ($subcondiciones as $vo){
									echo "<td align='center'>".$valores[$vo->id]."</td>";
								}
							}
							
							else if ($cat_victima_localizacion == 'etnia'){
								foreach ($subetnias as $vo){
									echo "<td align='center'>".$valores[$vo->id]."</td>";
								}
							}
							
							else if ($cat_victima_localizacion == 'edad'){
								foreach ($rangos_edades as $vo){
									echo "<td align='center'>".$valores[$vo->id]."</td>";
								}
							}
							
							else if ($cat_victima_localizacion == 'estado'){
								foreach ($estados as $vo){
									echo "<td align='center'>".$valores[$vo->id]."</td>";
								}
							}
							
							else if ($cat_victima_localizacion == 'ocupacion'){
								foreach ($ocupaciones as $vo){
									echo "<td align='center'>".$valores[$vo->id]."</td>";
								}
							}
						}
						
						echo "</tr>";
					}
				}
				
				
				if ($hay == 1){
					echo "<tr><td align='center' colspan='10'><br><b>NO SE ENCONTRARON EVENTOS</b></td></tr>";
				}
			}
			//DETALLE POR PERIODO
			if ($reporte == 4 || $reporte == 5){
				
				$a = 0;
				foreach ($fecha_ini as $f_ini){
					
					echo "<tr class='fila_lista'><td>$f_ini - $fecha_fin[$a]</td>";
					
					//PERIODO POR CATEGORIA
					if ($reporte == 4){
						foreach ($vo_cats as $vo){
							$num_eventos = $this->numEventosReporte(0,$vo->id,0,$f_ini,$fecha_fin[$a]);
							echo "<td align='center'>$num_eventos</td>";
						}
					}
					//PERIODO POR ACTORES
					else if ($reporte == 5){
							$a = 0;
							foreach ($id_actor1 as $id_actor){
								$vo = $actor_dao->Get($id_actor);
								$vo1 = $actor_dao->Get($id_actor2[$a]);
								$num_eventos = $this->numEventosReporte(0,0,"$vo->id,$vo1->id",$f_ini,$fecha_fin[$a]);
								echo "<td>$num_eventos</td>";
								
								$a++;
							}
						}
					
					$a++;	
				}
				
			}
			
			echo "</table>";
			
			$buffer_content = ob_get_contents();
			ob_end_flush();

			echo '</div>';
			
			$_SESSION["evento_c_xls"] = "<table border=1>".$buffer_content."</table>";
			echo "<input type='hidden' id='pdf' name='pdf'>";
//			echo "</form>";
		}
		else{
			echo "<tr><td align='center'><br><b>NO SE ENCONTRARON EVENTOS</b></td></tr>";
			echo "<tr><td align='center'><br><a href='javascript:history.back();'>Regresar</a></td></tr>";
			die;
		}
	}

	/**
	* Lista los Eventos en Excel para depuracion
	* @param string $sql Condicion SQL cuando se ejecuta este reporte desde el método Reportar()
	* @access public
	*/			
	function ReportarDepuracion($sql=''){

		set_time_limit(0);

		//INICIALIZACION DE VARIABLES
		$evento_vo = New EventoConflicto();
		$evento_dao = New EventoConflictoDAO();
		$municipio_vo = New Municipio();
		$municipio_dao = New MunicipioDAO();
		$depto_vo = New Depto();
		$depto_dao = New DeptoDAO();
		$actor_vo = New Actor();
		$actor_dao = New ActorDAO();
		$fuente_vo = New FuenteEventoConflicto();
		$fuente_dao = New FuenteEventoConflictoDAO();
		$subfuente_vo = New SubFuenteEventoConflicto();
		$subfuente_dao = New SubFuenteEventoConflictoDAO();
		$cat_vo = New CatEventoConflicto();
		$cat_dao = New CatEventoConflictoDAO();
		$subcat_vo = New SubCatEventoConflicto();
		$subcat_dao = New SubCatEventoConflictoDAO();
		$edad_dao = New EdadDAO();
		$rango_edad_dao = New RangoEdadDAO();
		$estado_dao = New EstadoMinaDAO();
		$condicion_dao = New CondicionMinaDAO();
		$subcondicion_dao = New SubCondicionDAO();
		$sexo_dao = New SexoDAO();
		$etnia_dao = New EtniaDAO();
		$sub_etnia_dao = New SubEtniaDAO();
		$subetnia_dao = New SubetniaDAO();
		$ocupacion_dao = New OcupacionDAO();
		$archivo = New Archivo();
		
		$f_ini = (isset($_POST["f_ini"])) ? $_POST["f_ini"] : "";
		$f_fin = (isset($_POST["f_fin"])) ? $_POST["f_fin"] : "";
							 
		if ($f_ini != '' && $f_fin != ''){
			$filtro_fecha = 1;	
		}
		
		$fecha_ini = $_POST["f_ini"];
		$fecha_fin = $_POST["f_fin"];
		
		if ($sql == ''){
			$sql_eventos = "SELECT DISTINCT evento_c.ID_EVEN, FECHA_REG_EVEN, FECHA_ING_EVEN,SINTESIS_EVEN FROM evento_c INNER JOIN descripcion_evento ON evento_c.id_even = descripcion_evento.id_even INNER JOIN evento_localizacion ON evento_c.id_even = evento_localizacion.id_even INNER JOIN victima ON victima.id_deseven = descripcion_evento.id_deseven";
	
			if ($filtro_fecha == 1){
				$sql_eventos .= " WHERE fecha_reg_even BETWEEN '$fecha_ini' AND '$fecha_fin'";
			}
				
			$sql_eventos .= " ORDER BY fecha_reg_even AND id_mun DESC";
		}
		else{
			$sql_eventos = "SELECT DISTINCT evento_c.ID_EVEN, FECHA_REG_EVEN, FECHA_ING_EVEN,SINTESIS_EVEN FROM evento_c INNER JOIN descripcion_evento ON evento_c.id_even = descripcion_evento.id_even INNER JOIN evento_localizacion ON evento_c.id_even = evento_localizacion.id_even INNER JOIN victima ON victima.id_deseven = descripcion_evento.id_deseven
							WHERE 1=1 $sql";
			//$sql_eventos = "SELECT * FROM evento_c WHERE 1=1 $cond_sql";
		}
		
		//die($sql_eventos);
		
		//echo $sql_eventos;
		$rs_eventos = $this->conn->OpenRecordset($sql_eventos);
		$m = $this->conn->RowCount($rs_eventos);
		
		echo "<table align='center' cellspacing='1' cellpadding='3' border=0 width='100%'>";
		
		?>
		<STYLE TYPE="text/css"><!--
		.excel_celda_texto {mso-style-parent:text; mso-number-format:"\@";white-space:normal;}
		--></STYLE>
		<?
		if ($m > 0){
			
			$cols_actores = $this->getMaxActoresEvento();
			$cols_victimas = $this->getMaxVictimasEvento();
			
			$content = "<tr><td colspan=3>";
			
			$content .= "<table class='tabla_reportelist' border=1><tr>";
			if ($sql == '')	$content .= "<td>ID del Evento</td>";
			$content .= "<td>FECHA_ENTRADA_REGISTRO</td>
					<td>FECHA EVENTO</td>";
			
			if ($sql == '')	$content .= "<td>RESUMEN_EVENTO</td>";
			
			$content .= "<td>CATEGORIA_1</td>
					<td>SUBCATEGORIA_1</td>
					<td>CATEGORIA_2</td>
					<td>SUBCATEGORIA_2</td>";
			
			for ($cl=1;$cl<=$cols_actores;$cl++){
				$content .= "<td>ACTOR/PRESUNTO_PERPETRADOR_$cl</td>
					<td>SUB_ACTOR/PRESUNTO_PERPETRADOR_$cl</td>
					<td>SUB_SUB_ACTOR/PRESUNTO_PERPETRADOR_$cl</td>";
			}
			
			for ($cl=1;$cl<=$cols_victimas;$cl++){
				$content .= "<td>VICTIMAS_$cl</td>
					<td>GRUPO_ETAREO_$cl</td>
					<td>CONDICIÓN_$cl</td>
					<td>SUB_CONDICION_$cl</td>
					<td>GRUPO_POBLACIONAL_$cl</td>
					<td>OCUPACION_$cl</td>
					<td>RANGO_DE_EDAD_$cl</td>
					<td>ESTADO_$cl</td>
					<td>SEXO_$cl</td>
					<td>SUB_ETNIA_$cl</td>";
			}
			
			if ($sql == '') $content .= "<td>TIPO_DE_FUENTE</td>";
			
			$content .= "<td>FUENTE</td>
					<td>DESCRIPCIÓN</td>
					<td>FECHA_FUENTE</td>
					<td>REFERENCIA</td>
					<td>DEPARTAMENTO</td>
					<td>COD_DANE_DEPTO</td>
					<td>MUNICIPIO</td>
					<td>COD_DANE_MPIO</td>
					<td>LUGAR</td>";
			
			$content .= "<tr>";

			$r = 0;
			while ($row_rs = $this->conn->FetchObject($rs_eventos)){
				
				$id = $row_rs->ID_EVEN;

				//Descripciones
				$desc_evento = $evento_dao->getDescripcionEvento($id);
				
				//Fuentes
				$fuentes = $evento_dao->getFuenteEvento($id);
				$num_fuentes = $fuentes['num'];
				
				//Localizaciones
				$locas = $evento_dao->getLocalizacionEvento($id);
				$num_locas = $locas['num'];
				
					
				$content .= "<tr>";
				
				if ($sql == '')	$content .= "<td>$row_rs->ID_EVEN</td>";
				
				$content .= "<td>$row_rs->FECHA_ING_EVEN</td>";
				$content .= "<td>$row_rs->FECHA_REG_EVEN</td>";
				if ($sql == '')	$content .= "<td>$row_rs->SINTESIS_EVEN</td>";
				
				for ($i=0;$i<2;$i++){
					if (isset($desc_evento['id_cat'][$i])){
						$content .= "<td>".$desc_evento['nom_cat'][$i]."</td>";
						$content .= "<td>".$desc_evento['nom_scat'][$i]."</td>";
					}
					else{
						for($v=0;$v<2;$v++){
							$content .= "<td></td>";
						}
					}
				}

				$celdas = 0;
				$num_cols_actor_total = $cols_actores*3;
				for ($i=0;$i<$num_cols_actor_total;$i++){
					if (isset($desc_evento['id'][$i])){
						$id_desevento = $desc_evento['id'][$i];
						
						//ACTORES
						$array_actores = $this->getActorEvento($id_desevento,1);
						$actores = $array_actores["nombre"];
		
						//SUB ACTORES
						$array_actores = $this->getActorEvento($id_desevento,2);
						$sub_actores = $array_actores["nombre"];
		
						//SUB SUB ACTORES
						$array_actores = $this->getActorEvento($id_desevento,3);
						$sub_sub_actores = $array_actores["nombre"];

						if (isset($actores[$i])){
							$content .= "<td>".$actores[0]."</td>";
						}
						else{
							$content .= "<td></td>";
						}
						if (isset($sub_actores[$i])){
							$content .= "<td>".$sub_actores[0]."</td>";
						}
						else{
							$content .= "<td></td>";
						}
						if (isset($sub_sub_actores[$i])){
							$content .= "<td>".$sub_sub_actores[0]."</td>";
						}
						else{
							$content .= "<td></td>";
						}
						
						$celdas += 3;
					}
				}
				
				//Completa las celdas de actores
				for ($c=$celdas;$c<$num_cols_actor_total;$c++){
//					$content .= "<td>Llenando A</td>";
					$content .= "<td></td>";
				}

				$celdas = 0;
				$num_cols_vict_total = 10*$cols_victimas;
				foreach($desc_evento['id'] as $id_deseven){	
					$victimas = $evento_dao->getVictimaDescripcionEvento($id_deseven);
					$num_vict_x_desc = $victimas['num'];
		
					if ($celdas < $num_cols_vict_total){
						if ($num_vict_x_desc > 0){
	
							for ($i=0;$i<$num_vict_x_desc;$i++){
			
								if ($celdas < $num_cols_vict_total){
									$content .= "<td>".$victimas['cant'][$i]."</td>";
									
									if (isset($victimas['nom_edad'][$i])){
										$content .= "<td>".$victimas['nom_edad'][$i]."</td>";
									}
									else{
										$content .= "<td></td>";
									}
									
									if (isset($victimas['nom_condicion'][$i])){
										$content .= "<td>".$victimas['nom_condicion'][$i]."</td>";
									}
									else{
										$content .= "<td></td>";
									}
									
									if (isset($victimas['nom_scondicion'][$i])){
										$content .= "<td>".$victimas['nom_scondicion'][$i]."</td>";
									}
									else{
										$content .= "<td></td>";
									}
						
									if (isset($victimas['nom_etnia'][$i])){
										$content .= "<td>".$victimas['nom_etnia'][$i]."</td>";
									}
									else{
										$content .= "<td></td>";
									}
		
									if (isset($victimas['nom_ocupacion'][$i])){
										$content .= "<td>".$victimas['nom_ocupacion'][$i]."</td>";
									}
									else{
										$content .= "<td></td>";
									}
		
									if (isset($victimas['nom_redad'][$i])){
										$content .= "<td>".$victimas['nom_redad'][$i]."</td>";
									}
									else{
										$content .= "<td></td>";
									}
									
									if (isset($victimas['nom_estado'][$i])){
										$content .= "<td>".$victimas['nom_estado'][$i]."</td>";
									}
									else{
										$content .= "<td></td>";
									}
									if (isset($victimas['nom_sexo'][$i])){
										$content .= "<td>".$victimas['nom_sexo'][$i]."</td>";
									}
									else{
										$content .= "<td></td>";
									}
									if (isset($victimas['nom_setnia'][$i])){
										$content .= "<td>".$victimas['nom_setnia'][$i]."</td>";
									}
									else{
										$content .= "<td></td>";
									}
									
									$celdas += 10;
								}
							}
						}
						else{
							for($v=0;$v<10;$v++){
								$content .= "<td></td>";
								$celdas++;
							}
						}
					}
				}
				
				//Completa las celdas de victimas
				for ($c=$celdas;$c<$num_cols_vict_total;$c++){
//					$content .= "<td>Llenando V</td>";
					$content .= "<td></td>";
				}
				
				$cols_fuente = ($sql == '') ? 5 : 4;
				if (isset($fuentes['nom_fuente'][0])){
					if ($sql == ''){
						$content .= "<td>".$fuentes['nom_fuente'][0]."</td>";
					}
					$content .= "<td>".$fuentes['nom_sfuente'][0]."</td>";
					$content .= "<td>".$fuentes['desc'][0]."</td>";
					$content .= "<td>".$fuentes['fecha'][0]."</td>";
					$content .= "<td>".$fuentes['medio'][0]."</td>";
				}
				else{
					for($v=0;$v<$cols_fuente;$v++){
						$content .= "<td></td>";
					}
				}
				
				$id_mun = $locas['mpios'][0];
				if ($id_mun > 0){
					$mun = $municipio_dao->Get($id_mun);
					$depto = $depto_dao->Get($mun->id_depto);
					
					$content .= "<td>$depto->nombre</td>";
					$content .= "<td class=\"excel_celda_texto\">$depto->id</td>";
					$content .= "<td>$mun->nombre</td>";
					$content .= "<td class='excel_celda_texto'>$mun->id</td>";
					$content .= "<td>".$locas['lugar'][0]."</td>";
				}
				else{
					for($v=0;$v<5;$v++){
						$content .= "<td></td>";
					}
				}
				
				$content .= "</tr>";
				$r++;
			}
		}
		
		else{
			echo "<tr><td align='center'><br><b>NO SE ENCONTRARON EVENTOS</b></td></tr>";
			echo "<tr><td align='center'><br><a href='javascript:history.back();'>Regresar</a></td></tr>";
			die;
		}
		
		//$buffer_content = ob_get_contents();
		
		//ob_end_clean();
//		ob_end_flush();

		//$archivo->Borrar($file_zip);
		exec("zip -j ".$_SERVER["DOCUMENT_ROOT"]."/sissh/admin/evento_c/reporte_eventos.zip ".$_SERVER["DOCUMENT_ROOT"]."/sissh/admin/evento_c/reporte_eventos.xls");
		
		//echo "zip -j ".$_SERVER["DOCUMENT_ROOT"]."/sissh/admin/evento_c/reporte_eventos.zip ".$_SERVER["DOCUMENT_ROOT"]."/sissh/admin/evento_c/reporte_eventos.xls";
		
		$file = $_SERVER['DOCUMENT_ROOT']."/sissh/admin/evento_c/reporte_eventos.xls";
		$file_zip = $_SERVER['DOCUMENT_ROOT']."/sissh/admin/evento_c/reporte_eventos.zip";
		$fp = $archivo->Abrir($file,"w+");
		$archivo->Escribir($fp,$content);
		$archivo->Cerrar($fp);
		
		$size = ceil(filesize($file) / 1000);
		$size_zip = ceil(filesize($file_zip) / 1000);
		
		echo "<tr><td><img src='/sissh/admin/images/excel.gif'>&nbsp;<a href='/sissh/admin/evento_c/reporte_eventos.xls'>Descargar Archivo XLS</a>&nbsp;[ Tamaño: ".$size." kB ] [ <b>$m Eventos Reportados</b> ]";
		echo "<tr><td><img src='/sissh/admin/images/zip.png'>&nbsp;<a href='/sissh/admin/evento_c/reporte_eventos.zip'>Descargar Archivo ZIP</a>&nbsp;[ Tamaño: ".$size_zip." kB ]";
			
		echo "</table>";
	}

	
	/******************************************************************************
	* Número de Eventos aplicando los filtros
	* @param $id_mun
	* @param $id_subcat
	* @param $id_actor
	* @param $fecha_ini
	* @param $fecha_fin
	* @access public
	*******************************************************************************/
	function numEventosReporte($id_mun,$id_subcat,$id_actor,$fecha_ini,$fecha_fin){
		
		$filtro_fecha = ($fecha_ini != "" && $fecha_fin != "") ? 1 : 0;
		
		if ($id_subcat > 0 && $id_actor == 0){
			$sql = "SELECT count(evento_localizacion.id_even) FROM descripcion_evento INNER JOIN evento_localizacion ON descripcion_evento.id_even = evento_localizacion.id_even INNER JOIN evento_c ON evento_localizacion.id_even = evento_c.id_even WHERE id_scateven = $id_subcat AND id_mun = $id_mun";
			
			if ($filtro_fecha == 1){
				$sql .= " AND fecha_reg_even BETWEEN '$fecha_ini' AND '$fecha_fin'";
			}
//			echo $sql;
			$rs = $this->conn->OpenRecordset($sql);
			$row_rs = $this->conn->FetchRow($rs);
			
			return $row_rs[0];
			
		}
		else if ($id_subcat == 0 && $id_actor == 0){
			$sql = "SELECT count(evento_c.id_even) FROM evento_c INNER JOIN evento_localizacion ON evento_localizacion.id_even = evento_c.id_even WHERE ";

			if ($id_mun > 0){
				$sql .= "id_mun = ".$id_mun;
				
				if ($filtro_fecha == 1){
					$sql .= " AND ";	
				}
			}
			
			if ($filtro_fecha == 1){
				$sql .= " fecha_reg_even BETWEEN '$fecha_ini' AND '$fecha_fin'";
			}
//			echo $sql;

			$rs = $this->conn->OpenRecordset($sql);
			$row_rs = $this->conn->FetchRow($rs);
			
			return $row_rs[0];
			
		}
		else if ($id_subcat == 0 && $id_actor > 0){
			$sql = "SELECT evento_c.id_even FROM actor_descevento INNER JOIN evento_localizacion ON actor_descevento.id_deseven = evento_localizacion.id_even INNER JOIN evento_c ON evento_localizacion.id_even = evento_c.id_even WHERE id_actor IN ($id_actor) AND id_mun = $id_mun";
//			echo $sql;
			if ($filtro_fecha == 1){
				$sql .= " AND fecha_reg_even BETWEEN '$fecha_ini' AND '$fecha_fin'";
			}
//			echo $sql;
			$rs = $this->conn->OpenRecordset($sql);
			
			//Si id_actor es del tipo combinacion (id1,id2), cuenta los eventos que tienen todos los actores
			if (count(split("[,]",$id_actor)) > 1){
				$a = 0;
				$id_even_a = array();
				$id_even_unique = array();
				while ($row_rs = $this->conn->FetchRow($rs)){
					$id_even_a[$a] = $row_rs[0];
					$a++;
				}
				if (count($id_even_a) > 0){
					//Elimina repetidos
					$id_even_unique = array_unique($id_even_a);
					
					//Resta la longitud de ambos arreglos para saber los actores que cumplen
					$num = count($id_even_a) - count($id_even_unique);
				}
				else $num = 0;
				
				return $num;
				
			}
			
			else{
				$row_rs = $this->conn->FetchRow($rs);
				
				return $row_rs[0];
			}
			
		}

	}
	
	/***************************************************************************************
	* Número de Victimas aplicando filtros
	* @param string $id_mun
	* @param array $filtros  Arreglo de filtros, las claves son los nombres del filtro
	* @access public
	****************************************************************************************/
	function numVictimasReporte($id_mun,$filtros){

		$filtro_fecha = (isset($filtros['f_ini']) && isset($filtros['f_fin']) && $filtros['f_ini'] != '' && $filtros['f_fin'] != '') ? 1 : 0;
		
		$sql = "SELECT sum(victima.cant_victima) FROM victima INNER JOIN descripcion_evento ON victima.id_deseven = descripcion_evento.id_deseven INNER JOIN evento_localizacion ON descripcion_evento.id_even = evento_localizacion.id_even INNER JOIN evento_c ON evento_localizacion.id_even = evento_c.id_even WHERE 1=1 ";
			
		if ($filtro_fecha == 1){
			$sql .= " AND fecha_reg_even BETWEEN '".$filtros['f_ini']."' AND '".$filtros['f_fin']."'";
		}
		
		if (isset($filtros['id_mun'])){
			$sql .= " AND id_mun = '$id_mun'";
		}

		if (isset($filtros['id_scat'])){
			$sql .= " AND id_scateven = ".$filtros['id_scat'];
		}
		
		if (isset($filtros['id_sexo'])){
			$sql .= " AND victima.id_sexo IN (".$filtros['id_sexo'].")";
		}
		
		if (isset($filtros['id_condicion'])){
			$sql .= " AND victima.id_condicion IN (".$filtros['id_condicion'].")";
		}
		
		if (isset($filtros['id_subcondicion'])){
			$sql .= " AND victima.id_subcondicion IN (".$filtros['id_subcondicion'].")";
		}
		
		if (isset($filtros['id_subetnia'])){
			$sql .= " AND victima.id_subetnia IN (".$filtros['id_subetnia'].")";
		}
		
		if (isset($filtros['id_edad'])){
			$sql .= " AND victima.id_edad IN (".$filtros['id_edad'].")";
		}
		
		if (isset($filtros['id_rango_edad'])){
			$sql .= " AND victima.id_raned IN (".$filtros['id_rango_edad'].")";
		}
		
		if (isset($filtros['id_estado'])){
			$sql .= " AND victima.id_estado IN (".$filtros['id_estado'].")";
		}
		
		if (isset($filtros['id_ocupacion'])){
			$sql .= " AND victima.id_ocupacion IN (".$filtros['id_ocupacion'].")";
		}

		
		//echo $sql;
		$rs = $this->conn->OpenRecordset($sql);
		$row_rs = $this->conn->FetchRow($rs);
		
		if (is_null($row_rs[0])) return 0;
		else	return $row_rs[0];
		
	}
	
	/******************************************************************************
	* Reporte PDF - EXCEL
	* @param Array $id_eventos Id de los EventoConflictos a Reportar
	* @param Int $formato PDF o Excel
	* @param Int $basico 1 = Básico - 2 = Detallado
	* @access public
	*******************************************************************************/
	function ReporteEventoConflicto($id_eventos,$formato,$basico){

		//INICIALIZACION DE VARIABLES
		$depto_dao = New DeptoDAO();
		$mun_dao = New MunicipioDAO();
		$tipo_dao = New TipoEventoConflictoDAO();
		$actor_dao = New ActorDAO();
		$cat_dao = New CatTipoEventoConflictoDAO();
		$cons_hum_dao = New ConsHumDAO();
		$cons_hum_vo = New ConsHum();
		$riesgo_hum_dao = New RiesgoHumDAO();
		$riesgo_hum_vo = New RiesgoHum();
		$region_dao = New RegionDAO();
		$poblado_dao = New PobladoDAO();
		$resguardo_dao = New ResguardoDAO();
		$parque_nat_dao = New ParqueNatDAO();
		$div_afro_dao = New DivAfroDAO();
		$cats = $cat_dao->GetAllArray('');
		$file = New Archivo();

		$arr_id = split("[,]",$id_eventos);


		if ($formato == 1){

			$pdf =& new Cezpdf();

			$pdf -> ezSetMargins(80,70,20,20);
			$pdf->selectFont('admin/lib/common/PDFfonts/Helvetica.afm');

			// Coloca el logo y el pie en todas las páginas
			$all = $pdf->openObject();
			$pdf->saveState();
			$img_att = getimagesize('images/logos/enc_reporte_semanal.jpg');
			$pdf->addPngFromFile('images/logos/enc_reporte_semanal.png',700,550,$img_att[0]/2,$img_att[1]/2);

			$pdf->addText(300,580,14,'<b>Sala de Situación Humanitaria</b>');

			if ($basico == 1){
				$pdf->addText(350,560,12,'Listado de EventoConflictos');
			}

			if ($basico == 2){
				$pdf->setLineStyle(1);
				$pdf->line(50,535,740,535);
				$pdf->line(50,530,740,530);
			}

			$pdf->addText(330,30,8,'Sala de Situación Humanitaria - Naciones Unidas');

			$pdf->restoreState();
			$pdf->closeObject();
			$pdf->addObject($all,'all');

			$pdf->ezSetDy(0);

			//FORMATO BASICO
			if ($basico == 1){

				$c = 0;
				$arr = Array();
				foreach ($arr_id as $id){
					//Carga el VO
					$vo = $this->Get($id);
					//Carga el arreglo
					$arr[$c] = $vo;
					$c++;
				}

				$num_arr = count($arr);

				//CLASIFICA LOS EVENTOS POR CATEGORIAS
				$c = 0;
				foreach ($cats as $cat_vo){
					$e = 0;
					foreach($arr as $eve){
						if ($eve->id_cat == $cat_vo->id){
							$arr_c[$c][$e] = $eve;
							$e++;
						}
					}
					$c++;
				}


				////SE MUESTRAN LOS EVENTOS POR CATEGORIA
				$c = 0;
				foreach ($cats as $cat_vo){

					//VERIFICA SI EXISTEN EVENTOS EN LA CATEGORIA
					$tiene = 0;
					foreach($arr as $eve){
						if ($eve->id_cat == $cat_vo->id){
							$tiene = 1;
						}
					}

					////TITULO DE LA CATEGORIA
					if ($tiene == 1){

						$data = Array(Array('title'=>'<b>'.$cat_vo->nombre.'</b>'));
						$options = Array('showLines' => 0,'width' => 750, 'cols'=>array('title' => array('justification'=>'center')));
						$pdf->ezTable($data,Array('title'=>''),'',$options);

						$title = Array('depto' => '<b>Departamento</b>',
						'mun'   => '<b>Municipio</b>',
						'lugar'   => '<b>Lugar</b>',
						't_evento'   => '<b>Tipo de EventoConflicto</b>',
						'actor'   => '<b>Actores</b>',
						'cons'   => '<b>Consecuencias Humanitarias</b>',
						'riesg'   => '<b>Riesgos Humanitarios</b>',
						'desc'   => '<b>Descripción</b>',
						'fecha'   => '<b>Fecha registro</b>');

						$f = 0;
						foreach($arr_c[$c] as $arr_vo){

							////DEPARTAMENTOS
							$z=0;
							$tmp = "";
							foreach($arr_vo->id_deptos as $id){
								$vo = $depto_dao->Get($id);
								if ($z==0)  $tmp = $vo->nombre;
								else				$tmp .= ", ".$vo->nombre;
								$z++;
							}
							$data[$f]['depto'] = $tmp;

							////MUNICIPIOS
							$z=0;
							$tmp = "";
							foreach($arr_vo->id_muns as $id){
								$vo = $mun_dao->Get($id);
								if ($z==0)  $tmp = $vo->nombre;
								else				$tmp .= ", ".$vo->nombre;
								$z++;
							}
							$data[$f]['mun'] = $tmp;

							////LUGAR
							$data[$f]['lugar'] = $arr_vo->lugar;

							////TIPO DE EVENTOS
							$z=0;
							$tmp = "";
							foreach($arr_vo->id_tipo as $id){
								$vo = $tipo_dao->Get($id);
								if ($z==0)  $tmp = $vo->nombre;
								else				$tmp .= ", ".$vo->nombre;
								$z++;
							}
							$data[$f]['t_evento'] = $tmp;

							////ACTORES
							$z=0;
							$tmp = "";
							foreach($arr_vo->id_actores as $id){
								$vo = $actor_dao->Get($id);
								if ($z==0)  $tmp = $vo->nombre;
								else				$tmp .= ", ".$vo->nombre;
								$z++;
							}
							$data[$f]['actor'] = $tmp;

							////CONS. HUM
							$z=0;
							$tmp = "";
							foreach($arr_vo->id_cons as $id){
								$vo = $cons_hum_dao->Get($id);
								if ($z==0)  $tmp = $vo->nombre;
								else				$tmp .= ", ".$vo->nombre;
								$z++;
							}

							////Descripción de las consecuencias
							if ($arr_vo->desc_cons_hum != "")
							$tmp .= " - ".$arr_vo->desc_cons_hum;

							$data[$f]['cons'] = $tmp;

							////RIESG. HUM
							$z=0;
							$tmp = "";
							foreach($arr_vo->id_riesgos as $id){
								$vo = $riesgo_hum_dao->Get($id);
								if ($z==0)  $tmp = $vo->nombre;
								else				$tmp .= ", ".$vo->nombre;
								$z++;
							}
							////Descripción de los riegos
							if ($arr_vo->desc_riesg_hum != "")
							$tmp .= " - ".$arr_vo->desc_riesg_hum;

							$data[$f]['riesg'] = $tmp;

							////DESCRIPCION
							$data[$f]['desc'] = $arr_vo->desc;

							////FECHA DE REGISTRO
							$data[$f]['fecha'] = $arr_vo->fecha_registro;

							$f++;
						}
					}
					$c++;

					$options = Array('showLines' => 2, 'shaded' => 0, 'width' => 750, 'fontSize'=>8, 'cols'=>array('desc'=>array('width'=>200,'justification'=>'full'),'lugar'=>array('width'=>80),'fecha'=>array('width'=>60)));
					$pdf->ezTable($data,$title,'',$options);

				}
			}

			//MUESTRA EN EL NAVEGADOR EL PDF
			//$pdf->ezStream();

			//CREA UN ARCHIVO PDF PARA BAJAR
			$nom_archivo = 'consulta/csv/evento.pdf';
			$file = New Archivo();
			$fp = $file->Abrir($nom_archivo,'wb');
			$pdfcode = $pdf->ezOutput();
			$file->Escribir($fp,$pdfcode);
			$file->Cerrar($fp);

			?>
			<table align='center' cellspacing="1" cellpadding="3" border="0" width="750">
				<tr><td>&nbsp;</td></tr>
				<tr><td align='center' class='titulo_lista' colspan=2>REPORTAR EVENTOS EN FORMATO PDF</td></tr>
				<tr><td>&nbsp;</td></tr>
				<tr><td colspan=2>
					Se ha generado correctamente el archivo PDF de EventoConflictos.<br><br>
					Para salvarlo use el botón derecho del mouse y la opción Guardar destino como sobre el siguiente link: <a href='<?=$nom_archivo;?>'>Archivo PDF</a>
				</td></tr>
			</table>
			<?

		}
		//EXCEL
		else if ($formato == 2){

			$fp = $file->Abrir('consulta/csv/evento.txt','w');

			$c = 0;
			$arr = Array();
			foreach ($arr_id as $id){
				//Carga el VO
				$vo = $this->Get($id);
				//Carga el arreglo
				$arr[$c] = $vo;
				$c++;
			}

			$num_arr = count($arr);

			//CLASIFICA LOS EVENTOS POR CATEGORIAS
			/*$c = 0;
			foreach ($cats as $cat_vo){
			$e = 0;
			foreach($arr as $eve){
			if ($eve->id_cat == $cat_vo->id){
			$arr_c[$c][$e] = $eve;
			$e++;
			}
			}
			$c++;
			}*/


			$linea = "ID_EVENTO|COD_DEPTO|DEPARTAMENTO|COD_MPIO|MUNICIPIO|LUGAR|CATEGORIA|TIPO DE EVENTO|SUBTIPO DE EVENTO|ACTORES|CONSECUENCIAS HUMANITARIAS|RIESGOS HUMANITARIOS|DESCRIPCION|FUENTE|FECHA DE REGISTRO|FECHA DEL EVENTO\n";
			$file->Escribir($fp,$linea);

			$f = 0;
			foreach($arr as $arr_vo){
				$linea = "";

				//REGISTRO POR MUNICIPIO
				if (count($arr_vo->id_muns) > 0){
					foreach($arr_vo->id_muns as $id_mun){
						$mun = $mun_dao->Get($id_mun);
						$depto = $depto_dao->Get($mun->id_depto);

						////ID EVENTO
						$linea .= $arr_vo->id;

						////COD. DEPARTAMENTO
						$linea .= "|".$depto->id;

						////DEPARTAMENTO
						$linea .= "|".$depto->nombre;

						////COD. MPIO
						$linea .= "|".$mun->id;

						////MUNICIPIO
						$linea .= "|".$mun->nombre;

						////LUGAR
						$arr_vo->lugar = str_replace("\r\n","",$arr_vo->lugar);
						$linea .= "|".$vo->lugar;

						////CATEGORIA
						$vo = $cat_dao->Get($arr_vo->id_cat);
						$linea .= "|".$vo->nombre;

						////TIPO DE EVENTOS
						$z=0;
						$tmp_papa = "";
						$tmp_hijo = "";
						foreach($arr_vo->id_tipo as $id){
							$vo = $tipo_dao->Get($id);
							//ES PAPA
							if ($vo->id_papa == 0){
								if ($z==0)  $tmp_papa = $vo->nombre;
								else		$tmp_papa .= ",".$vo->nombre;
							}
							//ES HIJO
							else {
								if ($z==0)  $tmp_hijo = $vo->nombre;
								else		$tmp_hijo .= ",".$vo->nombre;
							}

							$z++;
						}
						$linea .= "|".$tmp_papa;
						$linea .= "|".$tmp_hijo;

						////ACTORES
						$z=0;
						$tmp = "";
						foreach($arr_vo->id_actores as $id){
							$vo = $actor_dao->Get($id);
							if ($z==0)  $tmp = $vo->nombre;
							else		$tmp .= ",".$vo->nombre;
							$z++;
						}
						$linea .= "|".$tmp;

						////CONS. HUM
						$z=0;
						$tmp = "";
						foreach($arr_vo->id_cons as $id){
							$vo = $cons_hum_dao->Get($id);
							if ($z==0)  $tmp = $vo->nombre;
							else				$tmp .= ",".$vo->nombre;
							$z++;
						}

						////Descripción de las consecuencias
						if ($arr_vo->desc_cons_hum != ""){
							//ELIMINA EL CARACTER DE NUEVA LINEA QUE EL USUARIO INGRESA EN EL TEXTAREA
							$arr_vo->desc_cons_hum = str_replace("\r\n","",$arr_vo->desc_cons_hum);


							if ($tmp == "")	$tmp .= $arr_vo->desc_cons_hum;
							else			$tmp .= " - ".$arr_vo->desc_cons_hum;
						}
						$linea .= "|".$tmp;

						////RIESG. HUM
						$z=0;
						$tmp = "";
						foreach($arr_vo->id_riesgos as $id){
							$vo = $riesgo_hum_dao->Get($id);
							if ($z==0)  $tmp = $vo->nombre;
							else				$tmp .= ",".$vo->nombre;
							$z++;
						}
						////Descripción de los riegos
						if ($arr_vo->desc_riesg_hum != ""){
							//ELIMINA EL CARACTER DE NUEVA LINEA QUE EL USUARIO INGRESA EN EL TEXTAREA
							$arr_vo->desc_riesg_hum = str_replace("\r\n","",$arr_vo->desc_riesg_hum);

							if ($tmp == "")	$tmp .= $arr_vo->desc_riesg_hum;
							else			$tmp .= " - ".$arr_vo->desc_riesg_hum;
						}
						$linea .= "|".$tmp;

						////DESCRIPCION
						//ELIMINA EL CARACTER DE NUEVA LINEA QUE EL USUARIO INGRESA EN EL TEXTAREA
						$arr_vo->desc = str_replace("\r\n","",$arr_vo->desc);

						$linea .= "|".$arr_vo->desc;

						////FUENTE
						$linea .= "|".$arr_vo->fuente;

						////FECHA DE REGISTRO
						$linea .= "|".$arr_vo->fecha_registro;

						////FECHA DEL EVENTO
						$linea .= "|".$arr_vo->fecha_evento;

						$linea .= "\n";
					}
				}  //FIN: EVENTO TIENE MUNICIPIOS

				//REGISTRO POR DEPARTAMENTO
				else if (count($arr_vo->id_muns) == 0 && count($arr_vo->id_deptos) > 0){
					foreach($arr_vo->id_deptos as $id_depto){

						$depto = $depto_dao->Get($id_depto);

						////ID EVENTO
						$linea .= $arr_vo->id;

						////COD. DEPARTAMENTO
						$linea .= "|".$depto->id;

						////DEPARTAMENTO
						$linea .= "|".$depto->nombre;

						////COD. MPIO
						$linea .= "|";

						////MUNICIPIO
						$linea .= "|";

						////LUGAR
						$arr_vo->lugar = str_replace("\r\n","",$arr_vo->lugar);
						$linea .= "|".$vo->lugar;

						////CATEGORIA
						$vo = $cat_dao->Get($arr_vo->id_cat);
						$linea .= "|".$vo->nombre;

						////TIPO DE EVENTOS
						$z=0;
						$tmp_papa = "";
						$tmp_hijo = "";
						foreach($arr_vo->id_tipo as $id){
							$vo = $tipo_dao->Get($id);
							//ES PAPA
							if ($vo->id_papa == 0){
								if ($z==0)  $tmp_papa = $vo->nombre;
								else		$tmp_papa .= ",".$vo->nombre;
							}
							//ES HIJO
							else {
								if ($z==0)  $tmp_hijo = $vo->nombre;
								else		$tmp_hijo .= ",".$vo->nombre;
							}

							$z++;
						}
						$linea .= "|".$tmp_papa;
						$linea .= "|".$tmp_hijo;

						////ACTORES
						$z=0;
						$tmp = "";
						foreach($arr_vo->id_actores as $id){
							$vo = $actor_dao->Get($id);
							if ($z==0)  $tmp = $vo->nombre;
							else				$tmp .= ",".$vo->nombre;
							$z++;
						}
						$linea .= "|".$tmp;

						////CONS. HUM
						$z=0;
						$tmp = "";
						foreach($arr_vo->id_cons as $id){
							$vo = $cons_hum_dao->Get($id);
							if ($z==0)  $tmp = $vo->nombre;
							else				$tmp .= ",".$vo->nombre;
							$z++;
						}

						////Descripción de las consecuencias
						if ($arr_vo->desc_cons_hum != ""){
							//ELIMINA EL CARACTER DE NUEVA LINEA QUE EL USUARIO INGRESA EN EL TEXTAREA
							$arr_vo->desc_cons_hum = str_replace("\r\n","",$arr_vo->desc_cons_hum);


							if ($tmp == "")	$tmp .= $arr_vo->desc_cons_hum;
							else			$tmp .= " - ".$arr_vo->desc_cons_hum;
						}
						$linea .= "|".$tmp;

						////RIESG. HUM
						$z=0;
						$tmp = "";
						foreach($arr_vo->id_riesgos as $id){
							$vo = $riesgo_hum_dao->Get($id);
							if ($z==0)  $tmp = $vo->nombre;
							else				$tmp .= ",".$vo->nombre;
							$z++;
						}
						////Descripción de los riegos
						if ($arr_vo->desc_riesg_hum != ""){
							//ELIMINA EL CARACTER DE NUEVA LINEA QUE EL USUARIO INGRESA EN EL TEXTAREA
							$arr_vo->desc_riesg_hum = str_replace("\r\n","",$arr_vo->desc_riesg_hum);

							if ($tmp == "")	$tmp .= $arr_vo->desc_riesg_hum;
							else			$tmp .= " - ".$arr_vo->desc_riesg_hum;
						}
						$linea .= "|".$tmp;

						////DESCRIPCION
						//ELIMINA EL CARACTER DE NUEVA LINEA QUE EL USUARIO INGRESA EN EL TEXTAREA
						$arr_vo->desc = str_replace("\r\n","",$arr_vo->desc);

						$linea .= "|".$arr_vo->desc;

						////FUENTE
						$linea .= "|".$arr_vo->fuente;

						////FECHA DE REGISTRO
						$linea .= "|".$arr_vo->fecha_registro;

						////FECHA DEL EVENTO
						$linea .= "|".$arr_vo->fecha_evento;

						$linea .= "\n";
					}
				}  //FIN: EVENTO TIENE DEPARTAMENTO

				//REGISTRO EVENTO
				else if (count($arr_vo->id_muns) == 0 && count($arr_vo->id_deptos) == 0){

					////ID EVENTO
					$linea = $arr_vo->id;

					////COD. DEPARTAMENTO
					$linea .= "|";

					////DEPARTAMENTO
					$linea .= "|";

					////COD. MPIO
					$linea .= "|";

					////MUNICIPIO
					$linea .= "|";

					////LUGAR
					$linea .= "|".$arr_vo->lugar;

					////CATEGORIA
					$vo = $cat_dao->Get($arr_vo->id_cat);
					$linea .= "|".$vo->nombre;

					////TIPO DE EVENTOS
					$z=0;
					$tmp_papa = "";
					$tmp_hijo = "";
					foreach($arr_vo->id_tipo as $id){
						$vo = $tipo_dao->Get($id);
						//ES PAPA
						if ($vo->id_papa == 0){
							if ($z==0)  $tmp_papa = $vo->nombre;
							else		$tmp_papa .= ",".$vo->nombre;
						}
						//ES HIJO
						else {
							if ($z==0)  $tmp_hijo = $vo->nombre;
							else		$tmp_hijo .= ",".$vo->nombre;
						}

						$z++;
					}
					$linea .= "|".$tmp_papa;
					$linea .= "|".$tmp_hijo;

					////ACTORES
					$z=0;
					$tmp = "";
					foreach($arr_vo->id_actores as $id){
						$vo = $actor_dao->Get($id);
						if ($z==0)  $tmp = $vo->nombre;
						else				$tmp .= ",".$vo->nombre;
						$z++;
					}
					$linea .= "|".$tmp;

					////CONS. HUM
					$z=0;
					$tmp = "";
					foreach($arr_vo->id_cons as $id){
						$vo = $cons_hum_dao->Get($id);
						if ($z==0)  $tmp = $vo->nombre;
						else				$tmp .= ",".$vo->nombre;
						$z++;
					}

					////Descripción de las consecuencias
					if ($arr_vo->desc_cons_hum != ""){
						//ELIMINA EL CARACTER DE NUEVA LINEA QUE EL USUARIO INGRESA EN EL TEXTAREA
						$arr_vo->desc_cons_hum = str_replace("\r\n","",$arr_vo->desc_cons_hum);


						if ($tmp == "")	$tmp .= $arr_vo->desc_cons_hum;
						else			$tmp .= " - ".$arr_vo->desc_cons_hum;
					}
					$linea .= "|".$tmp;

					////RIESG. HUM
					$z=0;
					$tmp = "";
					foreach($arr_vo->id_riesgos as $id){
						$vo = $riesgo_hum_dao->Get($id);
						if ($z==0)  $tmp = $vo->nombre;
						else				$tmp .= ",".$vo->nombre;
						$z++;
					}
					////Descripción de los riegos
					if ($arr_vo->desc_riesg_hum != ""){
						//ELIMINA EL CARACTER DE NUEVA LINEA QUE EL USUARIO INGRESA EN EL TEXTAREA
						$arr_vo->desc_riesg_hum = str_replace("\r\n","",$arr_vo->desc_riesg_hum);

						if ($tmp == "")	$tmp .= $arr_vo->desc_riesg_hum;
						else			$tmp .= " - ".$arr_vo->desc_riesg_hum;
					}
					$linea .= "|".$tmp;

					////DESCRIPCION
					//ELIMINA EL CARACTER DE NUEVA LINEA QUE EL USUARIO INGRESA EN EL TEXTAREA
					$arr_vo->desc = str_replace("\r\n","",$arr_vo->desc);

					$linea .= "|".$arr_vo->desc;

					////FUENTE
					$linea .= "|".$arr_vo->fuente;

					////FECHA DE REGISTRO
					$linea .= "|".$arr_vo->fecha_registro;

					////FECHA DE EVENTO
					$linea .= "|".$arr_vo->fecha_evento;

					$linea .= "\n";

				}  //FIN: EVENTO TIENE DEPARTAMENTO
				$f++;
				$file->Escribir($fp,$linea);
			}
			$file->Cerrar($fp);

			?>
			<table align='center' cellspacing="1" cellpadding="3" border="0" width="750">
				<tr><td>&nbsp;</td></tr>
				<tr><td align='center' class='titulo_lista' colspan=2>REPORTAR EVENTOS EN FORMATO TXT (Excel)</td></tr>
				<tr><td>&nbsp;</td></tr>
				<tr><td colspan=2>
					Se ha generado correctamente el archivo TXT de EventoConflictos.<br><br>
					Para salvarlo use el botón derecho del mouse y la opción Guardar destino como sobre el siguiente link: <a href='consulta/csv/evento.txt'>Archivo TXT</a>
				</td></tr>
			</table>
			<?
		}
	}

	/**
	* Lista los Proyectos en una Tabla
	* @access public
	*/			
	function ReportarMapaI(){
		$cat_dao = New CatTipoEventoConflictoDAO();
		$depto_dao = New DeptoDAO();
		$mun_dao = New MunicipioDAO();
		$tipo_evento_dao = New TipoEventoConflictoDAO();
		$actor_dao = New ActorDAO();
		$cons_hum_dao = New ConsHumDAO();
		$riesgo_hum_dao = New RiesgoHumDAO();
		$cats = $cat_dao->GetAllArray('');
		$arr_id = Array();

		//UBIACION GEOGRAFICA
		if (isset($_POST["id_depto"]) && !isset($_POST["id_muns"])){

			$id_depto = $_POST['id_depto'];

			$m = 0;
			foreach ($id_depto as $id){
				$id_depto_s[$m] = "'".$id."'";
				$m++;
			}
			$id_depto_s = implode(",",$id_depto_s);

			$sql = "SELECT evento.ID_EVENTO FROM depto_evento INNER JOIN evento ON depto_evento.ID_EVENTO = evento.ID_EVENTO WHERE ID_DEPTO IN (".$id_depto_s.")";

			$sql .= " ORDER BY evento.ID_EVENTO ASC";

			$arr_id_u_g = Array();
			$i = 0;
			$rs = $this->conn->OpenRecordset($sql);
			while ($row_rs = $this->conn->FetchRow($rs)){
				$arr_id[$i] = $row_rs[0];
				$i++;
			}
		}

		//MUNICIPIO
		else if (isset($_POST["id_muns"])){

			$id_mun = $_POST['id_muns'];

			$m = 0;
			foreach ($id_mun as $id){
				$id_mun_s[$m] = "'".$id."'";
				$m++;
			}
			$id_mun_s = implode(",",$id_mun_s);

			$sql = "SELECT evento.ID_EVENTO FROM mun_evento INNER JOIN evento ON mun_evento.ID_EVENTO = evento.ID_EVENTO WHERE ID_MUN IN (".$id_mun_s.")";

			$sql .= " ORDER BY evento.ID_EVENTO ASC";

			$arr_id_u_g = Array();
			$i = 0;
			$rs = $this->conn->OpenRecordset($sql);
			while ($row_rs = $this->conn->FetchRow($rs)){
				$arr_id[$i] = $row_rs[0];
				$i++;
			}
		}

		$c = 0;
		$arr = Array();
		foreach ($arr_id as $id){
			//Carga el VO
			$vo = $this->Get($id);
			//Carga el arreglo
			$arr[$c] = $vo;
			$c++;
		}

		$num_arr = count($arr);

		echo "<table align='center' cellspacing='1' cellpadding='3' width='750'>";
		echo "<tr><td>&nbsp;</td></tr>";
		if ($num_arr > 0 && !isset($_POST["que_org"]) && !isset($_POST["que_eve"])){
			echo "<tr><td colspan='7' align='right'>Exportar a: <input type='image' src='images/consulta/generar_pdf.gif' onclick=\"document.getElementById('pdf_eve').value = 1;\">&nbsp;&nbsp;<input type='image' src='images/consulta/excel.gif' onclick=\"document.getElementById('pdf_eve').value = 2;\"></td>";
		}

		echo "<tr><td align='center' class='titulo_lista' colspan=7>EVENTOS QUE HAN SUCEDIDO EN : ";
		//TITULO DE DEPTO
		if (isset($_POST["id_depto"]) && !isset($_POST["id_muns"])){
			$t = 0;
			foreach($_POST["id_depto"] as $id_t){
				$vo  = $depto_dao->Get($id_t);
				if ($t == 0)	echo "<b>".$vo->nombre."</b>";
				else			echo ", <b>".$vo->nombre."</b>";
				$t++;
			}
			echo "<br>";
		}
		//TITULO DE MPIO
		if (isset($_POST["id_muns"])){
			$t = 0;
			foreach($id_mun as $id_t){
				$vo  = $mun_dao->Get($id_t);
				if ($t == 0)	echo "<b>".$vo->nombre."</b>";
				else			echo ", <b>".$vo->nombre."</b>";
				$t++;
			}
			echo "<br>";
		}
		echo "</td>";
		echo "</td></tr>";


		if ($num_arr > 0){

			//CLASIFICA LOS EVENTOS POR CATEGORIAS
			$c = 0;
			foreach ($cats as $cat_vo){
				$e = 0;
				foreach($arr as $eve){
					if ($eve->id_cat == $cat_vo->id){
						$arr_c[$c][$e] = $eve;
						$e++;
					}
				}
				$c++;
			}


			////SE MUESTRAN LOS EVENTOS POR CATEGORIA
			$c = 0;
			foreach ($cats as $cat_vo){

				//VERIFICA SI EXISTEN EVENTOS EN LA CATEGORIA
				$tiene = 0;
				foreach($arr as $eve){
					if ($eve->id_cat == $cat_vo->id){
						$tiene = 1;
					}
				}

				////TITULO DE LA CATEGORIA
				if ($tiene == 1){
					echo "<tr><td colspan='5'><br><b>Categoria del EventoConflicto: ".$cat_vo->nombre."</b></td></tr>
							<tr class='titulo_lista'>
							<td align='center' width='70'><b>Departamento</b></td>
							<td align='center' width='70'><b>Municipio</b></td>
							<td align='center' width='100'><b>Tipo de EventoConflicto</b></td>
							<td align='center' width='10'><b>Actores</b></td>
							<td align='center'><b>Descripción</b></td>
							<td align='center' width='70'><b>Fecha registro</b></td>
							<td align='center' width='80'>Registros: ".$num_arr."</td>
							</tr>";

					$p = 0;
					foreach($arr_c[$c] as $arr_vo){
						echo "<tr class='fila_lista'>";

						////DEPARTAMENTOS
						echo "<td>";
						$z=0;
						foreach($arr_vo->id_deptos as $id){

							$vo = $depto_dao->Get($id);

							/*$img = '../images/mapas/depto/'.$id.'.gif';
							$size = getimagesize($img);
							$width = $size[0]*0.2;
							$height = $size[1]*0.2;*/

							//if ($z==0)  echo "<img src='../images/mapas/depto/".$id.".gif' width='".$width."' height='".$height."'>".$vo->nombre;
							if ($z==0)  echo $vo->nombre;
							else				echo "<br> ".$vo->nombre;
							$z++;
						}
						echo "</td>";

						////MUNICIPIOS
						echo "<td>";
						$z=0;
						foreach($arr_vo->id_muns as $id){
							$vo = $mun_dao->Get($id);
							if ($z==0)  echo $vo->nombre;
							else				echo ", ".$vo->nombre;
							$z++;
						}
						echo "</td>";


						////TIPO DE EVENTOS
						echo "<td>";
						$z=0;
						foreach($arr_vo->id_tipo as $id){
							$vo = $tipo_evento_dao->Get($id);
							if ($z==0)  echo $vo->nombre;
							else				echo ", ".$vo->nombre;
							$z++;
						}
						echo "</td>";

						////ACTORES
						echo "<td>";
						$z=0;
						foreach($arr_vo->id_actores as $id){
							$vo = $actor_dao->Get($id);
							if ($z==0)  echo $vo->nombre;
							else				echo ", ".$vo->nombre;
							$z++;
						}
						echo "</td>";


						////DESCRIPCION
						echo "<td><div align='justify'>".$arr_vo->desc."</div></td>";

						////FECHA DE REGISTRO
						echo "<td>".$arr_vo->fecha_registro."</td>";
						echo "<td><a href='#' onclick=\"window.open('index.php?accion=consultar&class=EventoConflictoDAO&method=Ver&param=".$arr_vo->id."','','top=30,left=30,height=750,width=750,scrollbars=1');return false;\">Detalles</a></td>";

						echo "</tr>";

						$p++;
					}
				}
				$c++;
			}
			echo "<input type='hidden' name='id_eventos' value='".implode(",",$arr_id)."'>";
			echo "<input type='hidden' id='que_eve' name='que_eve' value='1'>";
		}
		else{
			echo "<tr><td align='center'><br><b>NO SE ENCONTRARON EVENTOS</b></td></tr>";
		}
		echo "</table>";
	}

}

class EventoConflictoAjax extends EventoConflictoDAO {
	
	/**
	* Gráfica de Eventos C
	* @access public
	* @param $reporte int Reporte
	* @param  $num_records int Numero de Mpios o Deptos a listar en los reportes 1,2
	* @param  $depto int
	* @param  $ubicacion
	* @param  $f_ini string Fecha Inicial
	* @param  $f_fin string Fecha Final
	* @param  $chart string Tipo de gráfica
	* @param  $filtros array Arreglo con los filtros que se pueden aplicar
	* @return int
	*/	
	function GraficaResumenEventoC($reporte,$num_records,$depto,$ubicacion,$f_ini,$f_fin,$chart,$filtros) {

		//LIBRERIAS
		require_once "lib/libs_evento_c.php";
		
		//INICIALIZACION DE VARIABLES
		$depto_dao = New DeptoDAO();
		$mun_dao = New MunicipioDAO();
		$cat_dao = New CatEventoConflictoDAO();
		$subcat_dao = New SubCatEventoConflictoDAO();
				
		$chk_num_records = array(10 => '',11 => '', 12 => '',13 => '',14 => '',15 => '');
		$chk_num_records[$num_records] = ' selected ';
		$title_reporte = array("","Número de eventos por Municipio",
							   "Número de eventos por Departamento",
								"Número de eventos por Mes",
							   "Número de eventos por Tipo de acción",
							   "Presuntos Actores Participantes");
		
		$title = $title_reporte[$reporte];

		//Nombre ubicación
		$nom_ubi = "Nacional";
		if ($depto == 1){
			$ubi = $depto_dao->Get($ubicacion);
			$nom_ubi = $ubi->nombre;
		}
		else if ($depto == 0){
			$ubi = $mun_dao->Get($ubicacion);
			$nom_ubi = $ubi->nombre;
		}
		
		//CAT-SUBCAT
		$filtro_cat = 0;
		if ($filtros['id_cat'] != ''){

			$filtro_cat = 1;
			$id_cat = $filtros['id_cat'];
			
			if ($filtros['id_scat'] != ''){
				$id_subcat = $filtros['id_scat'];
			}
			else{
				$condicion_cat = "ID_CATEVEN IN ($id_cat)";
				$vo_cats = $cat_dao->GetAllArray($condicion_cat);
				
				$id_subcat = implode(",",$subcat_dao->GetAllArrayID("ID_CATEVEN IN ($id_cat)"));
			}
		}
		
		echo "<table cellpadding=5 width='100%' border=0>";
		echo "<tr><td>Localizaci&oacute;n Geogr&aacute;fica: <b>$nom_ubi</b></td></tr>";
		
		switch ($reporte) {
			
			//Número de eventos por Municipio
			case 1:
					echo "<tr>
							<td valign='top'>
								<table border=0 class='tabla_grafica_conteo' cellpadding=4 cellspacing=1 width='250'>
									<tr class='titulo_tabla_conteo'><td align='center'>Municipio</td><td align='center' colspan='2'>N&uacute;mero de eventos</td></tr>";
					
				$sql = "SELECT count(evento_localizacion.id_mun) as num, evento_localizacion.id_mun FROM evento_c 
						INNER JOIN evento_localizacion ON evento_c.id_even = evento_localizacion.id_even
						INNER JOIN municipio ON evento_localizacion.id_mun = municipio.id_mun
						INNER JOIN descripcion_evento ON evento_c.id_even = descripcion_evento.id_even
						WHERE 1 = 1";

				//CAT-SUBCAT
				if ($filtro_cat == 1){
					$sql .= " AND id_scateven IN ($id_subcat)";
				}
				
				//FILTRO UBICACION
				if ($ubicacion != 0){
					//Depto
					if ($depto == 1){
						$sql .= "  AND id_depto = $ubicacion";
					}
				}
				
				$sql .= " GROUP BY evento_localizacion.id_mun ORDER BY num DESC LIMIT 0,$num_records";
				
				//echo $sql;
				$rs = $this->conn->OpenRecordset($sql);
				
				while ($row = $this->conn->FetchObject($rs)){
					
					$mun = $mun_dao->Get($row->id_mun);
					$valores_x[] = $mun->nombre;
					$valores_y[] = $row->num;
					
					echo "<tr class='fila_tabla_conteo'><td>$mun->nombre</td>";
					echo "<td align='right'>$row->num</td>";
					echo "</tr>";
				}
				
				echo "</table>";
				
				//Si no viene de API lo muestra
				if (!isset($_GET["api"])){
					echo "<br>Listar 
								  <select onchange=\"graficarEventoC('bar',this.value)\" class='select'>
								  	<option value=10 ".$chk_num_records[10].">10</option>
								  	<option value=11 ".$chk_num_records[11].">11</option>
								  	<option value=12 ".$chk_num_records[12].">12</option>
								  	<option value=13 ".$chk_num_records[13].">13</option>
								  	<option value=14 ".$chk_num_records[14].">14</option>
								  	<option value=15 ".$chk_num_records[15].">15</option>
								  </select> Municipios";
					
					echo "</td>";
				}
				
				echo "<td>";
					/********************************************************************************
					//PARA GRAFICA OPEN CHART
					/*******************************************************************************/
					$chk_chart = array('bar' => '', 'bar_3d' => '', 'line' => '');
					$chk_chart[$chart] = ' selected ';
					$font_size_key  =10;
					$font_size_x_label = 8;
					$font_size_y_label = 8;
					
					echo "<td align='center' valign='top'><table>";
					echo "<tr><td align='left'>";
					
					//Si no viene de API lo muestra
					if (!isset($_GET["api"])){
						echo "Tipo de Gr&aacute;fica:&nbsp;
								<select onchange=\"graficarEventoC(this.value,$num_records)\" class='select'>
									<option value='bar' ".$chk_chart['bar'].">Barras</option>
									<option value='bar_3d' ".$chk_chart['bar_3d'].">Barras 3D</option>
									<option value='line' ".$chk_chart['line'].">Lineas</option>

									
						</select>&nbsp;&nbsp;::&nbsp;&nbsp;";
					}
						
					echo "Si desea guardar la imagen haga click sobre el icono <img src='images/save.png'>
						</td>
					</tr>
					<tr><td class='tabla_grafica_conteo' colspan=1 width='600' bgcolor='#F0F0F0' align='center'><br>";
					
					//Eje x
					$i = 0;
					foreach ($valores_x as $x){
						if ($i == 0)	$ejex = "'".utf8_encode($x)."'";
						else			$ejex .= ",'".utf8_encode($x)."'";
						
						$i++;
					}
			
					//Estilos para bar y bar3D
					$chart_style = array('bar' => array('alpha' => 90, 'color' => array('#0066ff','#639F45','')),
										 'bar_3d' => array('alpha' => 90,'color' => array('#0066ff','#639F45','')),
										 'line' => array('alpha' => 90,'color' => array('#0066ff','#639F45','')));
					
					//Variable de sesion que va a ser el nomnre dela grafica al guardar
					$_SESSION["titulo_grafica"] = $title;
					
					$path = 'admin/lib/common/open-flash-chart/';
					$path_in = 'lib/common/open-flash-chart/';
			
					include ("$path_in/php-ofc-library/sidihChart.php");
					$g = new sidihChart();
					
					$content = "<?
					include_once('admin/lib/common/open-flash-chart/php-ofc-library/sidihChart.php' );
					
					\$g = new sidihChart();
					
					\$g->title('".utf8_encode($title)."');
					
					// label each point with its value
					\$g->set_x_labels( array(".$ejex.") );
					\$g->set_x_label_style( $font_size_x_label, '#000000',2);";
					
					
					if ($chart == 'bar_3d'){
						$content .= "\$g->set_x_axis_3d(6);";
						$content .= "\$g->x_axis_colour('#dddddd','#FFFFFF');";
					}
					
					$f = 1;
					$max_y = 0;
						
					if ($chart == 'bar' || $chart == 'bar_3d'){
						$content .= "\$".$chart." = new $chart(".$chart_style[$chart]['alpha'].", '".$chart_style[$chart]['color'][0]."' );\n";
						$content .= "\$".$chart."->data = array(".implode(",",$valores_y).");\n";
						$content .= "\$g->data_sets[] = \$".$chart.";";
					}
					else if ($chart == 'line'){
						$content .= "\$g->set_data(array(".implode(",",$valores_y)."));\n";
						$content .= "\$g->".$chart."_dot(1,3,'".$chart_style[$chart]['color'][0]."','',$font_size_key);\n";
					}
					
					$max_y = $g->maxY(max($valores_y));
					
					$content .= "
					\$g->set_tool_tip( '#x_label# <br> #val# Eventos' );		
					\$g->set_y_max( ".$max_y." );
					\$g->y_label_steps(5);
					//Espacio para el footer de la imagen con el logo - Toco con x_legend vacio, jejeje
					\$g->set_x_legend('".utf8_encode("Municipios")."\n\n\n',12);
					
					\$g->set_y_legend('".utf8_encode('Número de Eventos')."',12);
					
					\$g->set_num_decimals(0);
					
					// display the data
					echo \$g->render();
					?>";
					
					//MODIFICA EL ARCHIVO DE DATOS
					$archivo = New Archivo();
					$fp = $archivo->Abrir('../chart-data.php','w+');
					
					$archivo->Escribir($fp,$content);
					$archivo->Cerrar($fp);
			
					//IE Fix
					//Variable para que IE cargue el nuevo archivo de datos y cambie el tipo de grafica con los nuevos valores
					$nocache = time();
					include_once $path_in.'php-ofc-library/open_flash_chart_object.php';
					open_flash_chart_object( 500, 350, 'chart-data.php?nocache='.$nocache,false );

				echo "</td>";
				echo "</tr>";

			break;
			
			//Número de eventos por Departamento
			case 2:
					echo "<tr>
							<td valign='top'>
								<table border=0 class='tabla_grafica_conteo' cellpadding=4 cellspacing=1 width='250'>
									<tr class='titulo_tabla_conteo'><td align='center'>Departamento</td><td align='center' colspan='2'>N&uacute;mero de eventos</td></tr>";
					
				$sql = "SELECT count(municipio.id_depto) as num, municipio.id_depto FROM evento_c 
						INNER JOIN evento_localizacion ON evento_c.id_even = evento_localizacion.id_even 
						INNER JOIN municipio ON evento_localizacion.id_mun = municipio.id_mun
						INNER JOIN descripcion_evento ON evento_c.id_even = descripcion_evento.id_even
						WHERE 1 = 1 ";

				//CAT-SUBCAT
				if ($filtro_cat == 1){
					$sql .= " AND id_scateven IN ($id_subcat)";
				}
				
				
				//FILTRO UBICACION
				if ($ubicacion != 0){
					//Depto
					if ($depto == 1){
						$sql .= "  AND id_depto = $ubicacion";
					}
				}
				
				$sql .= " GROUP BY municipio.id_depto ORDER BY num DESC LIMIT 0,$num_records";
				$rs = $this->conn->OpenRecordset($sql);
				
				while ($row = $this->conn->FetchObject($rs)){
					
					$depto = $depto_dao->Get($row->id_depto);
					$valores_x[] = $depto->nombre;
					$valores_y[] = $row->num;
					
					echo "<tr class='fila_tabla_conteo'><td>$depto->nombre</td>";
					echo "<td align='right'>$row->num</td>";
					echo "</tr>";
				}
				
				echo "</table>";
				echo "<br>Listar 
							  <select onchange=\"graficarEventoC('bar',this.value)\" class='select'>
							  	<option value=10 ".$chk_num_records[10].">10</option>
							  	<option value=11 ".$chk_num_records[11].">11</option>
							  	<option value=12 ".$chk_num_records[12].">12</option>
							  	<option value=13 ".$chk_num_records[13].">13</option>
							  	<option value=14 ".$chk_num_records[14].">14</option>
							  	<option value=15 ".$chk_num_records[15].">15</option>
							  </select> Departamentos";
				
				echo "</td>";
				
				echo "<td>";
					/********************************************************************************
					//PARA GRAFICA OPEN CHART
					/*******************************************************************************/
					$chk_chart = array('bar' => '', 'bar_3d' => '', 'line' => '');
					$chk_chart[$chart] = ' selected ';
					$font_size_key  =10;
					$font_size_x_label = 8;
					$font_size_y_label = 8;
					
					echo "<td align='center' valign='top'><table>
					<tr>
						<td align='left'>Tipo de Gr&aacute;fica:&nbsp;
							<select onchange=\"graficarEventoC(this.value,$num_records)\" class='select'>
								<option value='bar' ".$chk_chart['bar'].">Barras</option>
								<option value='bar_3d' ".$chk_chart['bar_3d'].">Barras 3D</option>";
					
								//if ($reporte == 1){
									echo "<option value='line' ".$chk_chart['line'].">Lineas</option>";
								//}
								
					echo "</select>
					&nbsp;&nbsp;::&nbsp;&nbsp;Si desea guardar la imagen haga click sobre el icono <img src='images/save.png'>
						</td>
					</tr>
					<tr><td class='tabla_grafica_conteo' colspan=1 width='600' bgcolor='#F0F0F0' align='center'><br>";
					
					//Eje x
					$i = 0;
					foreach ($valores_x as $x){
						if ($i == 0)	$ejex = "'".utf8_encode($x)."'";
						else			$ejex .= ",'".utf8_encode($x)."'";
						
						$i++;
					}
			
					//Estilos para bar y bar3D
					$chart_style = array('bar' => array('alpha' => 90, 'color' => array('#0066ff','#639F45','')),
										 'bar_3d' => array('alpha' => 90,'color' => array('#0066ff','#639F45','')),
										 'line' => array('alpha' => 90,'color' => array('#0066ff','#639F45','')));
					
					//Variable de sesion que va a ser el nomnre dela grafica al guardar
					$_SESSION["titulo_grafica"] = $title;
					
					$path = 'admin/lib/common/open-flash-chart/';
					$path_in = 'lib/common/open-flash-chart/';
			
					include ("$path_in/php-ofc-library/sidihChart.php");
					$g = new sidihChart();
					
					$content = "<?
					include_once('admin/lib/common/open-flash-chart/php-ofc-library/sidihChart.php' );
					
					\$g = new sidihChart();
					
					\$g->title('".utf8_encode($title)."');
					
					// label each point with its value
					\$g->set_x_labels( array(".$ejex.") );
					\$g->set_x_label_style( $font_size_x_label, '#000000',2);";
					
					
					if ($chart == 'bar_3d'){
						$content .= "\$g->set_x_axis_3d(6);";
						$content .= "\$g->x_axis_colour('#dddddd','#FFFFFF');";
					}
					
					$f = 1;
					$max_y = 0;
						
					if ($chart == 'bar' || $chart == 'bar_3d'){
						$content .= "\$".$chart." = new $chart(".$chart_style[$chart]['alpha'].", '".$chart_style[$chart]['color'][0]."' );\n";
						$content .= "\$".$chart."->data = array(".implode(",",$valores_y).");\n";
						$content .= "\$g->data_sets[] = \$".$chart.";";
					}
					else if ($chart == 'line'){
						$content .= "\$g->set_data(array(".implode(",",$valores_y)."));\n";
						$content .= "\$g->".$chart."_dot(1,3,'".$chart_style[$chart]['color'][0]."','',$font_size_key);\n";
					}
					
					$max_y = $g->maxY(max($valores_y));
					
					$content .= "
					\$g->set_tool_tip( '#x_label# <br> #val# Eventos' );		
					\$g->set_y_max( ".$max_y." );
					\$g->y_label_steps(5);
					//Espacio para el footer de la imagen con el logo - Toco con x_legend vacio, jejeje
					\$g->set_x_legend('".utf8_encode("Departamentos")."\n\n\n',12);
					
					\$g->set_y_legend('".utf8_encode('Número de Eventos')."',12);
					
					\$g->set_num_decimals(0);
					
					// display the data
					echo \$g->render();
					?>";
					
					//MODIFICA EL ARCHIVO DE DATOS
					$archivo = New Archivo();
					$fp = $archivo->Abrir('../chart-data.php','w+');
					
					$archivo->Escribir($fp,$content);
					$archivo->Cerrar($fp);
			
					//IE Fix
					//Variable para que IE cargue el nuevo archivo de datos y cambie el tipo de grafica con los nuevos valores
					$nocache = time();
					include_once $path_in.'php-ofc-library/open_flash_chart_object.php';
					open_flash_chart_object( 500, 350, 'chart-data.php?nocache='.$nocache,false );

				echo "</td>";
				echo "</tr>";

			break;

			//Número de eventos por Mes
			case 3:
				
				$mes_a = array("","Enero","Febrero","Marzo","Abril","Mayo","Junio","Julio","Agosto","Septiembre","Octubre","Noviembre","Diciembre");
					echo "<tr>
							<td valign='top'>
								<table border=0 class='tabla_grafica_conteo' cellpadding=4 cellspacing=1 width='250'>
									<tr class='titulo_tabla_conteo'><td align='center'>Mes</td><td align='center' colspan='2'>N&uacute;mero de eventos</td></tr>";
					
				$sql = "SELECT count(fecha_reg_even) as num, MONTH(fecha_reg_even) as mes, YEAR(fecha_reg_even) as aaaa FROM evento_c 
						INNER JOIN evento_localizacion ON evento_c.id_even = evento_localizacion.id_even 
						INNER JOIN municipio ON evento_localizacion.id_mun = municipio.id_mun
						INNER JOIN descripcion_evento ON evento_c.id_even = descripcion_evento.id_even
						WHERE fecha_reg_even <> '0000-00-00' ";

				//CAT-SUBCAT
				if ($filtro_cat == 1){
					$sql .= " AND id_scateven IN ($id_subcat)";
				}
				
				//FILTRO UBICACION
				if ($ubicacion != 0){
					//Depto
					if ($depto == 1){
						$sql .= "  AND id_depto = $ubicacion";
					}
					else if($depto == 0){
						$sql .= "  AND evento_localizacion.id_mun = $ubicacion";
					}
				}
				
				$sql .= " GROUP BY mes ORDER BY fecha_reg_even";
				$rs = $this->conn->OpenRecordset($sql);
				
				//echo $sql;
				
				while ($row = $this->conn->FetchObject($rs)){

					$valores_x[] = $mes_a[$row->mes]." ".$row->aaaa;
					$valores_y[] = $row->num;
					
					echo "<tr class='fila_tabla_conteo'><td>".$mes_a[$row->mes]." ".$row->aaaa."</td>";
					echo "<td align='right'>$row->num</td>";
					echo "</tr>";
				}
				
				echo "</table>";
				echo "</td>";
				
				echo "<td>";
				
				if (count($valores_x) > 1){
					/********************************************************************************
					//PARA GRAFICA OPEN CHART
					/*******************************************************************************/
					$chk_chart = array('bar' => '', 'bar_3d' => '', 'line' => '');
					$chk_chart[$chart] = ' selected ';
					$font_size_key  =10;
					$font_size_x_label = 8;
					$font_size_y_label = 8;
					
					echo "<td align='center' valign='top'><table>
					<tr><td class='tabla_grafica_conteo' colspan=1 width='600' bgcolor='#F0F0F0' align='center'><br>";
					
					//Eje x
					$i = 0;
					foreach ($valores_x as $x){
						if ($i == 0)	$ejex = "'".utf8_encode($x)."'";
						else			$ejex .= ",'".utf8_encode($x)."'";
						
						$i++;
					}
			
					//Estilos para bar y bar3D
					$chart_style = array('bar' => array('alpha' => 90, 'color' => array('#0066ff','#639F45','')),
										 'bar_3d' => array('alpha' => 90,'color' => array('#0066ff','#639F45','')),
										 'line' => array('alpha' => 90,'color' => array('#0066ff','#639F45','')));
					
					//Variable de sesion que va a ser el nomnre dela grafica al guardar
					$_SESSION["titulo_grafica"] = $title;
					
					$path = 'admin/lib/common/open-flash-chart/';
					$path_in = 'lib/common/open-flash-chart/';
			
					include ("$path_in/php-ofc-library/sidihChart.php");
					$g = new sidihChart();
					
					$content = "<?
					include_once('admin/lib/common/open-flash-chart/php-ofc-library/sidihChart.php' );
					
					\$g = new sidihChart();
					
					\$g->title('".utf8_encode($title)."');
					
					// label each point with its value
					\$g->set_x_labels( array(".$ejex.") );
					\$g->set_x_label_style( $font_size_x_label, '#000000',2);";
					
					
					if ($chart == 'bar_3d'){
						$content .= "\$g->set_x_axis_3d(6);";
						$content .= "\$g->x_axis_colour('#dddddd','#FFFFFF');";
					}
					
					$f = 1;
					$max_y = 0;
						
					if ($chart == 'bar' || $chart == 'bar_3d'){
						$content .= "\$".$chart." = new $chart(".$chart_style[$chart]['alpha'].", '".$chart_style[$chart]['color'][0]."' );\n";
						$content .= "\$".$chart."->data = array(".implode(",",$valores_y).");\n";
						$content .= "\$g->data_sets[] = \$".$chart.";";
					}
					else if ($chart == 'line'){
						$content .= "\$g->set_data(array(".implode(",",$valores_y)."));\n";
						$content .= "\$g->".$chart."_dot(1,3,'".$chart_style[$chart]['color'][0]."','',$font_size_key);\n";
					}
					
					$max_y = $g->maxY(max($valores_y));
					
					$content .= "
					\$g->set_tool_tip( '#x_label# <br> #val# Eventos' );		
					\$g->set_y_max( ".$max_y." );
					\$g->y_label_steps(5);
					//Espacio para el footer de la imagen con el logo - Toco con x_legend vacio, jejeje
					\$g->set_x_legend('".utf8_encode("Mes")."\n\n\n',12);
					
					\$g->set_y_legend('".utf8_encode('Número de Eventos')."',12);
					
					\$g->set_num_decimals(0);
					
					// display the data
					echo \$g->render();
					?>";
					
					//MODIFICA EL ARCHIVO DE DATOS
					$archivo = New Archivo();
					$fp = $archivo->Abrir('../chart-data.php','w+');
					
					$archivo->Escribir($fp,$content);
					$archivo->Cerrar($fp);
			
					//IE Fix
					//Variable para que IE cargue el nuevo archivo de datos y cambie el tipo de grafica con los nuevos valores
					$nocache = time();
					include_once $path_in.'php-ofc-library/open_flash_chart_object.php';
					open_flash_chart_object( 500, 350, 'chart-data.php?nocache='.$nocache,false );

				}
				echo "</td>";
				echo "</tr>";

			break;

			//Número de eventos por Subcategoría (Tipo de acción)
			case 4:
				
				echo "<tr>
						<td valign='top'>
							<table border=0 class='tabla_grafica_conteo' cellpadding=4 cellspacing=1 width='250'>
								<tr class='titulo_tabla_conteo'><td align='center'>Mes</td><td align='center' colspan='2'>N&uacute;mero de eventos</td></tr>";
					
				$sql = "SELECT count(evento_c.id_even) as num, id_scateven FROM evento_c 
						INNER JOIN evento_localizacion ON evento_c.id_even = evento_localizacion.id_even 
						INNER JOIN municipio ON evento_localizacion.id_mun = municipio.id_mun
						INNER JOIN descripcion_evento ON evento_c.id_even = descripcion_evento.id_even
						WHERE 1=1 ";

				//CAT-SUBCAT
				if ($filtro_cat == 1){
					$sql .= " AND id_scateven IN ($id_subcat)";
				}
				
				//FILTRO UBICACION
				if ($ubicacion != 0){
					//Depto
					if ($depto == 1){
						$sql .= "  AND id_depto = $ubicacion";
					}
					else if($depto == 0){
						$sql .= "  AND evento_localizacion.id_mun = $ubicacion";
					}
					
				}
				
				$sql .= " GROUP BY id_scateven ORDER BY num DESC";
				$rs = $this->conn->OpenRecordset($sql);
				
				//echo $sql;
				
				while ($row = $this->conn->FetchObject($rs)){

					$vo = $subcat_dao->Get($row->id_scateven);
					$valores_x[] = $vo->nombre;
					$valores_y[] = $row->num;
					
					echo "<tr class='fila_tabla_conteo'><td>".$vo->nombre."</td>";
					echo "<td align='right'>$row->num</td>";
					echo "</tr>";
				}
				
				echo "</table>";
				echo "</td>";
				
				echo "<td>";
				
				if (count($valores_x) > 1){
					/********************************************************************************
					//PARA GRAFICA OPEN CHART
					/*******************************************************************************/
					$chk_chart = array('bar' => '', 'pie' => '');
					$chk_chart[$chart] = ' selected ';
					$font_size_key  =10;
					$font_size_x_label = 8;
					$font_size_y_label = 8;
					
					echo "<td align='center' valign='top'><table>
					<tr>
						<td align='left'>Tipo de Gr&aacute;fica:&nbsp;
							<select onchange=\"graficarEventoC(this.value,$num_records)\" class='select'>
								<option value='bar' ".$chk_chart['bar'].">Barras</option>
								<option value='pie' ".$chk_chart['pie'].">Torta</option>
							</select>
					&nbsp;&nbsp;::&nbsp;&nbsp;Si desea guardar la imagen haga click sobre el icono <img src='images/save.png'>
						</td>
					</tr>";
										
					echo "<td align='center' valign='top'><table>
					<tr><td class='tabla_grafica_conteo' colspan=1 width='600' bgcolor='#F0F0F0' align='center'><br>";
					
					//Eje x
					$i = 0;
					foreach ($valores_x as $x){
						
						$x_tmp = split(" ",$x);
						
						if (count($x_tmp) > 1)	$x = $x_tmp[0]." ".$x_tmp[1]."...";
						else					$x = $x_tmp[0];
						
						if ($i == 0)	$ejex = "'".utf8_encode($x)."'";
						else			$ejex .= ",'".utf8_encode($x)."'";
						
						$i++;
					}
			
					//Estilos para bar y bar3D
					$chart_style = array('bar' => array('alpha' => 90, 'color' => array('#0066ff','#639F45','')));
					
					//Variable de sesion que va a ser el nomnre dela grafica al guardar
					$_SESSION["titulo_grafica"] = $title;
					
					$path = 'admin/lib/common/open-flash-chart/';
					$path_in = 'lib/common/open-flash-chart/';
			
					include ("$path_in/php-ofc-library/sidihChart.php");
					$g = new sidihChart();
					
					$content = "<?
					include_once('admin/lib/common/open-flash-chart/php-ofc-library/sidihChart.php' );
					
					\$g = new sidihChart();
					
					\$g->title('".utf8_encode($title)."');
					
					// label each point with its value
					\$g->set_x_labels( array(".$ejex.") );
					\$g->set_x_label_style( $font_size_x_label, '#000000',2);";
					
					$max_y = $g->maxY(max($valores_y));
					
					if ($chart == 'pie'){
						$content .= "\$g->pie(100,'#CCCCCC','{font-size: 10px; color: #000000;');\n
								 \$g->pie_values( array(".implode(",",$valores_y)."), array($ejex) );
								 \$g->pie_slice_colours( array('#0066ff','#99CC00','#ffcc00') );";
						
						
						$content .= "
						\$g->set_tool_tip( '#x_label# <br> #val# Eventos' );		
						
						//Espacio para el footer de la imagen con el logo - Toco con x_legend vacio, jejeje
						\$g->set_x_legend('".utf8_encode("Mes")."\n\n\n',12);
						\$g->set_num_decimals(0);";
					}
					else{
						$content .= "\$".$chart." = new $chart(".$chart_style[$chart]['alpha'].", '".$chart_style[$chart]['color'][0]."' );\n";
						$content .= "\$".$chart."->data = array(".implode(",",$valores_y).");\n";
						$content .= "\$g->data_sets[] = \$".$chart.";";
					
						$max_y = $g->maxY(max($valores_y));
						
						$content .= "
						\$g->set_tool_tip( '#x_label# <br> #val# Eventos' );		
						\$g->set_y_max( ".$max_y." );
						\$g->y_label_steps(5);
						//Espacio para el footer de la imagen con el logo - Toco con x_legend vacio, jejeje
						\$g->set_x_legend('".utf8_encode("Mes")."\n\n\n',12);
						
						\$g->set_y_legend('".utf8_encode('Número de Eventos')."',12);
						
						\$g->set_num_decimals(0);";
					}
					
					// display the data
					$content .= "echo \$g->render();
					?>";
					
					//MODIFICA EL ARCHIVO DE DATOS
					$archivo = New Archivo();
					$fp = $archivo->Abrir('../chart-data.php','w+');
					
					$archivo->Escribir($fp,$content);
					$archivo->Cerrar($fp);
			
					//IE Fix
					//Variable para que IE cargue el nuevo archivo de datos y cambie el tipo de grafica con los nuevos valores
					$nocache = time();
					include_once $path_in.'php-ofc-library/open_flash_chart_object.php';
					open_flash_chart_object( 500, 350, 'chart-data.php?nocache='.$nocache,false );

				}
				echo "</td>";
				echo "</tr>";

			break;
			
		}
		
		echo "</table>";
		
	}
	
}

?>