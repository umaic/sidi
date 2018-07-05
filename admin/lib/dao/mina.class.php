<?
/**
 * DAO de Mina
 *
 * Contiene los métodos de la clase Mina
 * @author Ruben A. Rojas C.
 */

Class MinaDAO {

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
	* URL para redireccionar después de Insertar, Actualizar o Borrar
	* @var string
	*/
	var $url;

	/**
  * Constructor
	* Crea la conexión a la base de datos
  * @access public
  */
	function MinaDAO (){
		$this->conn = MysqlDb::getInstance();
		$this->tabla = "mina";
		$this->columna_id = "ID_MINA";
		$this->columna_nombre = "";
		$this->columna_order = "FECHA";
		$this->num_reg_pag = 10;
		$this->url = "index.php?accion=listar&class=MinaDAO&method=ListarTabla&param=";
		$this->dir_cache_resumen = $_SERVER["DOCUMENT_ROOT"]."/sissh/consulta/resumen/mina";
	}

	/**
  * Consulta los datos de una Mina
  * @access public
  * @param int $id ID del Mina
  * @return VO
  */
	function Get($id){
		$sql = "SELECT * FROM ".$this->tabla." WHERE ".$this->columna_id." = ".$id;
		$rs = $this->conn->OpenRecordset($sql);
		$row_rs = $this->conn->FetchObject($rs);

		//Crea un VO
		$vo = New Mina();

		//Carga el VO
		$vo = $this->GetFromResult($vo,$row_rs);

		//Retorna el VO
		return $vo;
	}

	/**
  * Retorna el max ID
  * @access public
  * @return int
  */
	function GetMaxID(){
		$sql = "SELECT max(ID_PROY) as maxid FROM ".$this->tabla;
		$rs = $this->conn->OpenRecordset($sql);
		if($row_rs = $this->conn->FetchRow($rs)){
			return $row_rs[0];
		}
		else{
			return 0;
		}
	}

	/**
  * Retorna el numero de Registros
  * @access public
  * @return int
  */
	function numRecords($condicion){
		$sql = "SELECT count(ID_PROY) as num FROM ".$this->tabla;
		if ($condicion != ""){
			$sql .= " WHERE ".$condicion;
		}
		$rs = $this->conn->OpenRecordset($sql);
		$row_rs = $this->conn->FetchRow($rs);

		return $row_rs[0];
	}

	/**
  * Consulta los datos de los Mina que cumplen una condición
  * @access public
  * @param string $condicion Condición que deben cumplir los Mina y que se agrega en el SQL statement.
  * @return array Arreglo de VOs
  */
	function GetAllArray($condicion,$limit,$order_by){
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
			$vo = New Mina();
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
	* Consulta el numero de victimas
	* @access public
	* @param int $condicion Condicion sin la parte de Ubicacion
	* @param int $dato_para
	* @return VO
	*/
	function GetValor($condicion,$id_ubicacion,$dato_para){

		$mun_dao = New MunicipioDAO();
		$valor = 0;

		$sql = "SELECT sum(victima.CANT_VICTIMA) FROM victima JOIN descripcion_evento USING(id_deseven) JOIN evento_c USING(id_even) JOIN evento_localizacion USING(id_even) WHERE $condicion";
		
		if ($dato_para == 1){

			$muns = $mun_dao->GetAllArrayID("ID_DEPTO = '$id_ubicacion'",'');
			$m_m = 0;
			foreach ($muns as $id){
				$m_m == 0 ? $id_muns = "'".$id."'" : $id_muns .= ",'".$id."'";
				$m_m++;
			}

			$sql .= " AND evento_localizacion.ID_MUN IN ($id_muns)";
		}
		else if ($dato_para == 2){
			$sql .= " AND evento_localizacion.ID_MUN = '$id_ubicacion'";
		}

		//CONDICION DEL TIPO DE EVENTO PARA MINA
		$sql .= " AND ID_SCATEVEN IN (31,32)";
		
		$rs = $this->conn->OpenRecordset($sql);
		if ($this->conn->RowCount($rs) > 0){
			$row_rs = $this->conn->FetchRow($rs);
			if (!is_null($row_rs[0]))	$valor = $row_rs[0];
		}

		return $valor;
	}

	/**
	* Consulta el numero de accidentes
	* @access public
	* @param int $condicion Condicion sin la parte de Ubicacion
	* @param int $dato_para
	* @return VO
	*/
	function GetValorAcc($condicion,$id_ubicacion,$dato_para){

		$mun_dao = New MunicipioDAO();
		$valor = 0;

		$sql = "SELECT count(evento_c.id_even) FROM descripcion_evento JOIN evento_c USING(id_even) JOIN evento_localizacion USING(id_even) WHERE $condicion";
		
		if ($dato_para == 1){

			$muns = $mun_dao->GetAllArrayID("ID_DEPTO ='$id_ubicacion'",'');
			$m_m = 0;
			foreach ($muns as $id){
				$m_m == 0 ? $id_muns = "'".$id."'" : $id_muns .= ",'".$id."'";
				$m_m++;
			}

			$sql .= " AND evento_localizacion.ID_MUN IN ($id_muns)";
		}
		else if ($dato_para == 2){
			$sql .= " AND evento_localizacion.ID_MUN = '$id_ubicacion'";
		}

		//CONDICION DEL TIPO DE EVENTO PARA MINA
		$sql .= " AND ID_SCATEVEN IN (31,32)";
		
		$rs = $this->conn->OpenRecordset($sql);
		if ($this->conn->RowCount($rs) > 0){
			$row_rs = $this->conn->FetchRow($rs);
			if (!is_null($row_rs[0]))	$valor = $row_rs[0];
		}

		return $valor;
	}
	
	
	/**
	* Consulta el año inicial y el mas reciente con información de minas
	* @access public
	* @return array $a $a = array('ini','fin')
	*/
	function GetMinMaxFecha(){


		//$sql = "SELECT max(year(FECHA)), min(year(FECHA)) FROM mina WHERE FECHA <> '0000-00-00'";
		$sql = "SELECT max(year(FECHA_REG_EVEN)), min(year(FECHA_REG_EVEN)) FROM evento_c '";
		$sql = "SELECT max(year(FECHA_REG_EVEN)), min(year(FECHA_REG_EVEN)) FROM evento_c JOIN descripcion_evento USING(id_even) ";
		
		//CONDICION DEL TIPO DE EVENTO PARA MINA
		$sql .= " WHERE ID_SCATEVEN IN (31,32)";
		
		$rs = $this->conn->OpenRecordset($sql);
		$row_rs = $this->conn->FetchRow($rs);
		$a['max'] = $row_rs[0];
		$a['min'] = $row_rs[1];

		return $a;
	}

	/**
	* Lista las Minaes en una Tabla
	* @access public
	*/
	function ListarTabla(){

		$depto_dao = New DeptoDAO();
		$mun_dao = New MunicipioDAO();
		$condicion_dao = New CondicionMinaDAO();
		$estado_dao = New EstadoMinaDAO();
		$sexo_dao = New SexoDAO();
		$edad_dao = New EdadDAO();
		$tipo_eve_dao = New TipoEventoDAO();

		$arr = Array();
		$num_arr = 0;

		$condicion = '';

		$id_depto_exp = -1;
		if (isset($_GET["id_depto_exp"]) && $_GET["id_depto_exp"] != -1){
			$id_depto_exp = $_GET["id_depto_exp"];
		}

		$id_mun_exp = -1;
		if (isset($_GET["id_mun_exp"]) && $_GET["id_mun_exp"] != -1){
			$id_mun_exp = $_GET["id_mun_exp"];
		}

		//CONDICION NIVEL DEPARTAMENTAL
		if (isset($_GET["id_depto_exp"]) && !isset($_GET["id_mun_exp"])){
			$muns = $mun_dao->GetAllArrayID("ID_DEPTO = '".$id_depto_exp."'",'');
			$m = 0;
			foreach ($muns as $id){
				$muns[$m] = "'".$id."'";
				$m++;
			}

			$id_muns = implode(",",$muns);
			$condicion = "ID_MUN IN (".$id_muns.")";
		}

		//CONDICION NIVEL MUNICIPAL
		if (isset($_GET["id_mun_exp"]) && $_GET["id_mun_exp"] != -1){
			$condicion = "ID_MUN = ".$id_mun_exp;
		}

		//QUERY
		$arr = $this->GetAllArray($condicion,'','');
		$num_arr = count($arr);

		echo "<table align='center' cellspacing='1' cellpadding='5' width='750'>
	    <tr><td>&nbsp;</td></tr>
	    <tr class='titulo_lista'><td align='center' colspan=9><b>LISTA DE EVENTOS CON MINA</b></td></tr>

		<tr>
	    <td colspan='9'>
	    Filtrar por Departamento &nbsp;<select name='id_depto_exp' class='select' onchange=\"location.href='index.php?accion=listar&class=".$_GET["class"]."&method=ListarTabla&param=&id_depto_exp='+this.value\">
			<option value=-1>Seleccione alguno...</option>";
		$depto_dao->ListarCombo('combo',$id_depto_exp,'');
		echo "</select>&nbsp;&nbsp;";

		//MUN EXP.
		if (isset($_GET["id_depto_exp"])){
			echo "Municipio &nbsp;<select name='id_mun_exp' class='select' onchange=\"location.href='index.php?accion=listar&class=".$_GET["class"]."&method=ListarTabla&param=&id_depto_exp=".$id_depto_exp."&id_mun_exp='+this.value\">
				<option value=-1>Seleccione alguno...</option>";
			$mun_dao->ListarCombo('combo',$id_mun_exp,'ID_DEPTO = '.$id_depto_exp);
			echo "</select></td>";
		}
		echo "</tr>

		<tr class='titulo_lista'>
		<td width='200'>Cod. Mpio</td>
		<td width='300'>Tipo Evento</td>
		<td width='300'>Fecha</td>
		<td width='300'>Estado</td>
		<td width='100'>Sexo</td>
		<td width='150'>Edad</td>
		<td width='150'>Condicion</td>
		<td width='150'>Cantidad</td>
	    </tr>";

		if (isset($_GET["id_depto_exp"]) || isset($_GET["id_depto_rec"])){

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
				echo "<td align='center'>".$arr[$p]->id_mun."</td>";

				//TIPO DE EVENTO
				$tipo = $tipo_eve_dao->Get($arr[$p]->id_tipo_eve);
				echo "<td align='center'>".$tipo->nombre."</td>";

				echo "<td align='center'>".$arr[$p]->fecha."</td>";

				//ESTADO
				$estado = $estado_dao->Get($arr[$p]->id_estado);
				echo "<td align='center'>".$estado->nombre."</td>";

				//SEXO
				$sexo = $sexo_dao->Get($arr[$p]->id_sexo);
				echo "<td align='center'>".$sexo->nombre."</td>";

				//EDAD
				$edad = $edad_dao->Get($arr[$p]->id_edad);
				echo "<td align='center'>".$edad->nombre."</td>";

				//CONDICION
				$condicion = $condicion_dao->Get($arr[$p]->id_condicion);
				echo "<td align='center'>".$condicion->nombre."</td>";

				echo "<td align='center'>".$arr[$p]->cantidad."</td>";

				echo "</tr>";
			}

			echo "<tr><td>&nbsp;</td></tr>";
			//PAGINACION
			if ($num_arr > $this->num_reg_pag){

				$num_pages = ceil($num_arr/$this->num_reg_pag);
				echo "<tr><td colspan='8' align='center'>";

				echo "Ir a la página:&nbsp;<select onchange=\"location.href='index.php?accion=listar&id_depto_exp=".$id_depto_exp."&id_mun_exp=".$id_mun_exp."&class=".$_GET["class"]."&method=".$_GET["method"]."&param=".$_GET["param"]."&page='+this.value\" class='select'>";
				for ($pa=1;$pa<=$num_pages;$pa++){
					echo " <option value='".$pa."'";
					if ($pa == $pag_url)	echo " selected ";
					echo ">".$pa."</option> ";
				}
				echo "</select>";
				echo "</td></tr>";
			}
		}
		echo "</table>";
	}

	/**
  * Imprime en pantalla los datos del Mina
  * @access public
  * @param object $vo Mina que se va a imprimir
  * @param string $formato Formato en el que se listarán los Mina, puede ser Tabla o ComboSelect
	* @param int $valor_combo ID del Mina que será selccionado cuando el formato es ComboSelect
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
	* Carga un VO de Mina con los datos de la consulta
	* @access public
	* @param object $vo VO de Mina que se va a recibir los datos
	* @param object $Resultset Resource de la consulta
	* @return object $vo VO de Mina con los datos
	*/
	function GetFromResult($vo,$Result){

		

		$vo->id = $Result->{$this->columna_id};

		$vo->id_tipo_eve = $Result->ID_TIPO_EVE;
		$vo->id_condicion = $Result->ID_CONDICION_MINA;
		$vo->id_estado = $Result->ID_ESTADO_MINA;
		$vo->id_sexo = $Result->ID_SEXO;
		$vo->id_edad = $Result->ID_EDAD;
		$vo->fecha = $Result->FECHA;
		$vo->cantidad = $Result->CANTIDAD;
		$vo->id_mun = $Result->ID_MUN;

		return $vo;

	}

	/**
  * Inserta el valor de un Dato de Mina en la B.D.
  * @access public
  * @param object $depto_vo VO de Mina que se va a insertar
  * @param int $dato_para A que corresponde el dato
  */
	function Insertar($mina_vo){

		$sql =  "INSERT INTO ".$this->tabla." (ID_EDAD,ID_ESTADO_MINA,ID_TIPO_EVE,ID_MUN,ID_SEXO,ID_CONDICION_MINA,CANTIDAD,FECHA) VALUES
									  		 (".$mina_vo->id_edad.",".$mina_vo->id_estado.",".$mina_vo->id_tipo_eve.",'".$mina_vo->id_mun."',".$mina_vo->id_sexo.",".$mina_vo->id_condicion.",".$mina_vo->cantidad.",'".$mina_vo->fecha."')";
		//echo $sql;
		$this->conn->Execute($sql);
	}

	/**
	* Borra un Mina en la B.D.
	* @access public
	* @param int $id ID del Mina que se va a borrar de la B.D
	*/
	function Borrar($id){

		//BORRA
		$sql = "DELETE FROM ".$this->tabla." WHERE ".$this->columna_id." = ".$id;
		$this->conn->Execute($sql);

		?>
		<script>
		alert("Registro eliminado con &eacute;xito!");
		location.href = '<?=$this->url;?>';
		</script>
		<?
	}


	/**
	* Vacía la Tabla
	* @access public
	*/
	function EmptyTable(){

		//BORRA
		$sql = "TRUNCATE ".$this->tabla;
		$this->conn->Execute($sql);

	}


	/**
	* Lista los Minas en una Tabla
	* @access public
	*/
	function Reportar(){

		//INICIALIZACION DE VARIABLES
		$depto_dao = New DeptoDAO();
		$municipio_dao = New MunicipioDAO();
		$condicion_dao = New CondicionMinaDAO();
		$estado_dao = New EstadoMinaDAO();
		$sexo_dao = New SexoDAO();
		$edad_dao = New EdadDAO();
		$tipo_eve_dao = New TipoEventoDAO();

		//SE CONSTRUYE EL SQL
		$condicion = "";
		$arreglos = "";

		//FECHA
		if ($_POST["f_ini"] != "" && $_POST["f_fin"] != ""){

			$arr_id_fecha = Array();

			$sql = "SELECT ID_MINA FROM mina WHERE FECHA between '".$_POST["f_ini"]."' AND '".$_POST["f_fin"]."' ORDER BY FECHA";
			$rs = $this->conn->OpenRecordset($sql);
			$i = 0;
			while ($row_rs = $this->conn->FetchRow($rs)){
				$arr_id_fecha[$i] = $row_rs[0];
				$i++;
			}

			$arreglos .= "\$arr_id_fecha";
		}


		//TIPO
		if (isset($_POST["id_tipo"])){

			$arr_id_tipo = Array();

			$id_tipo = $_POST['id_tipo'];
			$id_s = implode(",",$id_tipo);

			$sql = "SELECT ID_MINA FROM mina WHERE ID_TIPO_EVE IN (".$id_s.") ORDER BY FECHA";
			$rs = $this->conn->OpenRecordset($sql);
			$i = 0;
			while ($row_rs = $this->conn->FetchRow($rs)){
				$arr_id_tipo[$i] = $row_rs[0];
				$i++;
			}

			if ($arreglos == "")	$arreglos = "\$arr_id_tipo";
			else					$arreglos .= ",\$arr_id_tipo";

		}

		//CONDICION
		if (isset($_POST["id_condicion"])){

			$arr_id_condicion = Array();

			$id_condicion = $_POST['id_condicion'];
			$id_s = implode(",",$id_condicion);

			$sql = "SELECT ID_MINA FROM mina WHERE ID_CONDICION_MINA IN (".$id_s.") ORDER BY FECHA";
			$rs = $this->conn->OpenRecordset($sql);
			$i = 0;
			while ($row_rs = $this->conn->FetchRow($rs)){
				$arr_id_condicion[$i] = $row_rs[0];
				$i++;
			}

			if ($arreglos == "")	$arreglos = "\$arr_id_condicion";
			else					$arreglos .= ",\$arr_id_condicion";

		}

		//ESTADO
		if (isset($_POST["id_estado"])){

			$arr_id_estado = Array();

			$id_estado = $_POST['id_estado'];
			$id_s = implode(",",$id_estado);

			$sql = "SELECT ID_MINA FROM mina WHERE ID_ESTADO_MINA IN (".$id_s.") ORDER BY FECHA";
			$rs = $this->conn->OpenRecordset($sql);
			$i = 0;
			while ($row_rs = $this->conn->FetchRow($rs)){
				$arr_id_estado[$i] = $row_rs[0];
				$i++;
			}

			if ($arreglos == "")	$arreglos = "\$arr_id_estado";
			else					$arreglos .= ",\$arr_id_estado";

		}

		//EDAD
		if (isset($_POST["id_edad"])){

			$arr_id_edad = Array();

			$id_edad = $_POST['id_edad'];
			$id_s = implode(",",$id_edad);

			$sql = "SELECT ID_MINA FROM mina WHERE ID_EDAD IN (".$id_s.") ORDER BY FECHA";
			$rs = $this->conn->OpenRecordset($sql);
			$i = 0;
			while ($row_rs = $this->conn->FetchRow($rs)){
				$arr_id_edad[$i] = $row_rs[0];
				$i++;
			}

			if ($arreglos == "")	$arreglos = "\$arr_id_edad";
			else					$arreglos .= ",\$arr_id_edad";

		}

		//SEXO
		if (isset($_POST["id_sexo"])){

			$arr_id_sexo = Array();

			$id_sexo = $_POST['id_sexo'];
			$id_s = implode(",",$id_sexo);

			$sql = "SELECT ID_MINA FROM mina WHERE ID_SEXO IN (".$id_s.") ORDER BY FECHA";
			$rs = $this->conn->OpenRecordset($sql);
			$i = 0;
			while ($row_rs = $this->conn->FetchRow($rs)){
				$arr_id_sexo[$i] = $row_rs[0];
				$i++;
			}

			if ($arreglos == "")	$arreglos = "\$arr_id_sexo";
			else					$arreglos .= ",\$arr_id_sexo";

		}

		//UBIACION GEOGRAFICA
		if (isset($_POST["id_depto"]) && !isset($_POST["id_muns"])){

			$id_depto = $_POST['id_depto'];

			$m = 0;
			foreach ($id_depto as $id){
				$id_depto_s[$m] = "'".$id."'";
				$m++;
			}
			$id_depto_s = implode(",",$id_depto_s);

			$sql = "SELECT ID_MINA FROM mina INNER JOIN municipio ON mina.ID_MUN = municipio.ID_MUN WHERE ID_DEPTO IN (".$id_depto_s.") ORDER BY FECHA";

			$arr_id_u_g = Array();
			$i = 0;
			$rs = $this->conn->OpenRecordset($sql);
			while ($row_rs = $this->conn->FetchRow($rs)){
				$arr_id_u_g[$i] = $row_rs[0];
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

			$sql = "SELECT ID_MINA FROM mina WHERE ID_MUN IN (".$id_mun_s.") ORDER BY FECHA";

			$arr_id_u_g = Array();
			$i = 0;
			$rs = $this->conn->OpenRecordset($sql);
			while ($row_rs = $this->conn->FetchRow($rs)){
				$arr_id_u_g[$i] = $row_rs[0];
				$i++;
			}
		}

		if (isset($_POST["id_depto"])){

			if ($arreglos == "")	$arreglos = "\$arr_id_u_g";
			else					$arreglos .= ",\$arr_id_u_g";
		}

		//INTERSECCION DE LOS ARREGLOS PARA REALIZAR LA CONSULTA
		if (count(explode(",",$arreglos)) > 1 ){
			eval("\$arr_id = array_intersect($arreglos);");
		}
		else{
			eval("\$arr_id = $arreglos;");
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

		echo "<form action='index.php?m_e=mina&accion=consultar&class=MinaDAO' method='POST'>";
		echo "<table align='center' cellspacing='1' cellpadding='3' class='tabla_reportelist_outer'>";
		echo "<tr><td>&nbsp;</td></tr>";
		if ($num_arr > 0){
			echo "<tr><td colspan='8' align='right'>Exportar a: <input type='image' src='images/consulta/generar_pdf.gif' onclick=\"document.getElementById('pdf').value = 1;\">&nbsp;&nbsp;<input type='image' src='images/consulta/excel.gif' onclick=\"document.getElementById('pdf').value = 2;\"></td>";
		}
		echo "<tr><td align='center' class='titulo_lista' colspan=8>CONSULTA DE DATOS DE EVENTOS CON MINA</td></tr>";
		echo "<tr><td colspan=9>Consulta realizada aplicando los siguientes filtros:</td>";
		echo "<tr><td colspan=9>";

		//TITULO DE FECHA
		if ($_POST["f_ini"] != "" && $_POST["f_fin"] != ""){
			echo "<img src='images/flecha.gif'> Fecha Inicial: ".$_POST["f_ini"]." - Fecha Final: ".$_POST["f_fin"];
		}

		//TITULO DE TIPO
		if (isset($_POST["id_tipo"])){
			echo "<img src='images/flecha.gif'> Tipo de Evento: ";
			$t = 0;
			foreach($id_tipo as $id_t){
				$vo  = $tipo_eve_dao->Get($id_t);
				if ($t == 0)	echo "<b>".$vo->nombre."</b>";
				else			echo ", <b>".$vo->nombre."</b>";
				$t++;
			}
			echo "<br>";
		}

		//TITULO DE CONDICION
		if (isset($_POST["id_condicion"])){
			echo "<img src='images/flecha.gif'> Condición: ";
			$t = 0;
			foreach($id_condicion as $id_t){
				$vo  = $condicion_dao->Get($id_t);
				if ($t == 0)	echo "<b>".$vo->nombre."</b>";
				else			echo ", <b>".$vo->nombre."</b>";
				$t++;
			}
			echo "<br>";
		}

		//TITULO DE ESTADO
		if (isset($_POST["id_estado"])){
			echo "<img src='images/flecha.gif'> Estado: ";
			$t = 0;
			foreach($id_estado as $id_t){
				$vo  = $estado_dao->Get($id_t);
				if ($t == 0)	echo "<b>".$vo->nombre."</b>";
				else			echo ", <b>".$vo->nombre."</b>";
				$t++;
			}
			echo "<br>";
		}

		//TITULO DE EDAD
		if (isset($_POST["id_edad"])){
			echo "<img src='images/flecha.gif'> Edad: ";
			$t = 0;
			foreach($id_edad as $id_t){
				$vo  = $edad_dao->Get($id_t);
				if ($t == 0)	echo "<b>".$vo->nombre."</b>";
				else			echo ", <b>".$vo->nombre."</b>";
				$t++;
			}
			echo "<br>";
		}

		//TITULO DE SEXO
		if (isset($_POST["id_sexo"])){
			echo "<img src='images/flecha.gif'> Sexo: ";
			$t = 0;
			foreach($id_sexo as $id_t){
				$vo  = $sexo_dao->Get($id_t);
				if ($t == 0)	echo "<b>".$vo->nombre."</b>";
				else			echo ", <b>".$vo->nombre."</b>";
				$t++;
			}
			echo "<br>";
		}

		//TITULO DE DEPTO
		if (isset($_POST["id_depto"])){
			echo "<img src='images/flecha.gif'> Departamento ";
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
			echo "<img src='images/flecha.gif'> Municipio";

			$t = 0;
			foreach($id_mun as $id_t){
				$vo  = $municipio_dao->Get($id_t);
				if ($t == 0)	echo "<b>".$vo->nombre."</b>";
				else			echo ", <b>".$vo->nombre."</b>";
				$t++;
			}
			echo "<br>";
		}
		echo "</td></tr>";
		echo "<tr><td>&nbsp;</td></tr>";

		if ($num_arr > 0){
			echo "<tr><td colspan=3><table class='tabla_reportelist'>";
			echo "<tr class='titulo_lista'>
					<td width='200'>Municipio</td>
					<td width='250'>Tipo de Evento</td>
					<td width='200'>Fecha</td>
					<td width='150'>Estado</td>
					<td width='60'>Sexo</td>
					<td width='150'>Edad</td>
					<td width='150'>Condición</td>
					<td width='100'>Eventos</td>
			    </tr>";

			$ii = 0;
			for($p=0;$p<$num_arr;$p++){
				$style = "";
				if (fmod($p+1,2) == 0)  $style = "fila_lista";

				echo "<tr class='".$style."'>";

				//MPIO
				$mun = $municipio_dao->Get($arr[$p]->id_mun);
				echo "<td>".$mun->nombre."</td>";

				//TIPO
				$vo = $tipo_eve_dao->Get($arr[$p]->id_tipo_eve);
				echo "<td>".$vo->nombre."</td>";

				//FECHA
				echo "<td>".$arr[$p]->fecha."</td>";

				//ESTADO
				$vo = $estado_dao->Get($arr[$p]->id_estado);
				echo "<td>".$vo->nombre."</td>";

				//SEXO
				$vo = $sexo_dao->Get($arr[$p]->id_sexo);
				echo "<td>".$vo->nombre."</td>";

				//EDAD
				$vo = $edad_dao->Get($arr[$p]->id_edad);
				echo "<td>".$vo->nombre."</td>";

				//CONDICION
				$vo = $condicion_dao->Get($arr[$p]->id_condicion);
				echo "<td>".$vo->nombre."</td>";

				echo "<td>".$arr[$p]->cantidad."</td>";

			}
			echo "<tr><td>&nbsp;</td></tr>";
		}
		else{
			echo "<tr><td align='center'><br><b>NO SE ENCONTRARON EVENTOS CON MINAS</b></td></tr>";
			echo "<tr><td align='center'><br><a href='javascript:history.back(-1);'>Regresar</a></td></tr>";
			die;
		}

		echo "<input type='hidden' name='id_minas' value='".implode(",",$arr_id)."'>";
		echo "<input type='hidden' id='pdf' name='pdf'>";
		echo "</table>";
		echo "</form>";
	}


	/******************************************************************************
	* Reporte PDF - EXCEL
	* @param Array $id_minas Id de los Minas a Reportar
	* @param Int $formato PDF o Excel
	* @param Int $basico 1 = Básico - 2 = Detallado
	* @param Int $dato_para 1 = Dato en Departamento - 2 = Dato en Municipio
	* @access public
	*******************************************************************************/
	function ReporteMina($id_datos,$formato,$basico){

		//INICIALIZACION DE VARIABLES
		$depto_dao = New DeptoDAO();
		$municipio_dao = New MunicipioDAO();
		$condicion_dao = New CondicionMinaDAO();
		$estado_dao = New EstadoMinaDAO();
		$sexo_dao = New SexoDAO();
		$edad_dao = New EdadDAO();
		$tipo_eve_dao = New TipoEventoDAO();
		$file = New Archivo();

		$arr_id = explode(",",$id_datos);

		if ($formato == 1){

			$pdf = new Cezpdf();
			$pdf->selectFont('admin/lib/common/PDFfonts/Helvetica.afm');

			if ($basico == 1){
				$pdf -> ezSetMargins(80,70,20,20);
			}
			else{
				$pdf -> ezSetMargins(100,70,50,50);
			}


			// Coloca el logo y el pie en todas las páginas
			$all = $pdf->openObject();
			$pdf->saveState();
			$img_att = getimagesize('images/logos/enc_reporte_semanal.jpg');
			$pdf->addPngFromFile('images/logos/enc_reporte_semanal.png',700,550,$img_att[0]/2,$img_att[1]/2);

			$pdf->addText(300,550,14,'<b>Sala de Situación Humanitaria</b>');

			if ($basico == 1){
				$pdf->addText(300,530,12,'Listado de Eventos con de Mina');
			}

			$fecha = getdate();
			$fecha_hoy = $fecha["mday"]."/".$fecha["mon"]."/".$fecha["year"];

			$pdf->addText(370,510,12,$fecha_hoy);

			if ($basico == 2){
				$pdf->setLineStyle(1);
				$pdf->line(50,535,740,535);
				$pdf->line(50,530,740,530);
			}

			$pdf->restoreState();
			$pdf->closeObject();
			$pdf->addObject($all,'all');

			$pdf->ezSetDy(-30);

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

			//FORMATO BASICO
			if ($basico == 1){

				$title = Array('id_mun' => '<b>Cod. Mpio.</b>',
				'mun'   => '<b>Municipio</b>',
				'tipo'   => '<b>Tipo Evento</b>',
				'fecha'   => '<b>Fecha</b>',
				'estado'   => '<b>Estado</b>',
				'sexo'   => '<b>Sexo</b>',
				'edad'   => '<b>Edad</b>',
				'condicion'   => '<b>Condición</b>',
				'cantidad'   => '<b>Cantidad</b>',
				);

				for($p=0;$p<$num_arr;$p++){

					//ID MPIO
					$data[$p]['id_mun'] = $arr[$p]->id_mun;

					//MPIO
					$mun = $municipio_dao->Get($arr[$p]->id_mun);
					$data[$p]['mun'] = $mun->nombre;

					//TIPO
					$vo = $tipo_eve_dao->Get($arr[$p]->id_tipo_eve);
					$data[$p]['tipo'] = $vo->nombre;

					//FECHA
					$data[$p]['fecha'] = $arr[$p]->fecha;

					//ESTADO
					$vo = $estado_dao->Get($arr[$p]->id_estado);
					$data[$p]['estado'] = $vo->nombre;

					//SEXO
					$vo = $sexo_dao->Get($arr[$p]->id_sexo);
					$data[$p]['sexo'] = $vo->nombre;

					//EDAD
					$vo = $edad_dao->Get($arr[$p]->id_edad);
					$data[$p]['edad'] = $vo->nombre;

					//CONDICION
					$vo = $condicion_dao->Get($arr[$p]->id_condicion);
					$data[$p]['condicion'] = $vo->nombre;

					$data[$p]['cantidad'] = $arr[$p]->cantidad;
				}

				$options = Array('showLines' => 2, 'shaded' => 0, 'width' => 750, 'fontSize'=>8, 'cols'=>array('mun'=>array('width'=>150),'cantidad'=>array('width'=>70)));
				$pdf->ezTable($data,$title,'',$options);
			}

			//MUESTRA EN EL NAVEGADOR EL PDF
			//$pdf->ezStream();

			//CREA UN ARCHIVO PDF PARA BAJAR
			$nom_archivo = 'consulta/csv/mina.pdf';
			$file = New Archivo();
			$fp = $file->Abrir($nom_archivo,'wb');
			$pdfcode = $pdf->ezOutput();
			$file->Escribir($fp,$pdfcode);
			$file->Cerrar($fp);

			?>
			<table align='center' cellspacing="1" cellpadding="3" border="0" width="750">
				<tr><td>&nbsp;</td></tr>
				<tr><td align='center' class='titulo_lista' colspan=2>REPORTAR EVENTOS CON MINA EN FORMATO PDF</td></tr>
				<tr><td>&nbsp;</td></tr>
				<tr><td colspan=2>
					Se ha generado correctamente el archivo PDF de Eventos con Mina.<br><br>
					Para salvarlo use el botón derecho del mouse y la opción Guardar destino como sobre el siguiente link: <a href='<?=$nom_archivo;?>'>Archivo PDF</a>
				</td></tr>
			</table>
			<?

		}
		//EXCEL
		else if ($formato == 2){

			$fp = $file->Abrir('consulta/csv/mina.csv','w');

			if ($basico == 1){
				$tit = "COD. MPIO,MUNICIPIO,TIPO EVENTO,FECHA,ESTADO,SEXO,EDAD,CONDICION,EVENTOS";
			}

			//ENCABEZADO
			$file->Escribir($fp,$tit."\n");

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

			for($p=0;$p<$num_arr;$p++){

				//ID MPIO
				$linea = $arr[$p]->id_mun;

				//MPIO
				$mun = $municipio_dao->Get($arr[$p]->id_mun);
				$linea .= ",".$mun->nombre;

				//TIPO
				$vo = $tipo_eve_dao->Get($arr[$p]->id_tipo_eve);
				$linea .= ",".$vo->nombre;

				//FECHA
				$linea .= ",".$arr[$p]->fecha;

				//ESTADO
				$vo = $estado_dao->Get($arr[$p]->id_estado);
				$linea .= ",".$vo->nombre;

				//SEXO
				$vo = $sexo_dao->Get($arr[$p]->id_sexo);
				$linea .= ",".$vo->nombre;

				//EDAD
				$vo = $edad_dao->Get($arr[$p]->id_edad);
				$linea .= ",".$vo->nombre;

				//CONDICION
				$vo = $condicion_dao->Get($arr[$p]->id_condicion);
				$linea .= ",".$vo->nombre;

				$linea .= ",".$arr[$p]->cantidad;

				$file->Escribir($fp,$linea."\n");
			}
			$file->Cerrar($fp);

			?>
			<table align='center' cellspacing="1" cellpadding="3" border="0" width="750">
				<tr><td>&nbsp;</td></tr>
				<tr><td align='center' class='titulo_lista' colspan=2>REPORTAR EVENTOS CON MINA EN FORMATO CSV (Excel)</td></tr>
				<tr><td>&nbsp;</td></tr>
				<tr><td colspan=2>
					Se ha generado correctamente el archivo CSV de Eventos con Mina.<br><br>
					Para salvarlo use el botón derecho del mouse y la opción Guardar destino como sobre el siguiente link: <a href='consulta/csv/mina.csv'>Archivo CSV</a>
				</td></tr>
			</table>
			<?
		}
	}


	/**
	* Lista los Datos en una Tabla
	* @access public
	*/
	function ReportarMapaI(){

		//INICIALIZACION DE VARIABLES
		$depto_dao = New DeptoDAO();
		$municipio_dao = New MunicipioDAO();
		$condicion_dao = New CondicionMinaDAO();
		$estado_dao = New EstadoMinaDAO();
		$sexo_dao = New SexoDAO();
		$edad_dao = New EdadDAO();
		$tipo_eve_dao = New TipoEventoDAO();

		//SE CONSTRUYE EL SQL
		$condicion = "";
		$arreglos = "";

		//UBIACION GEOGRAFICA
		if (isset($_POST["id_depto"]) && !isset($_POST["id_muns"])){

			$id_depto = $_POST['id_depto'];

			$m = 0;
			foreach ($id_depto as $id){
				$id_depto_s[$m] = "'".$id."'";
				$m++;
			}
			$id_depto_s = implode(",",$id_depto_s);

			$sql = "SELECT ID_MINA FROM mina INNER JOIN municipio ON mina.ID_MUN = municipio.ID_MUN WHERE ID_DEPTO IN (".$id_depto_s.") ORDER BY FECHA";

			$arr_id_u_g = Array();
			$i = 0;
			$rs = $this->conn->OpenRecordset($sql);
			while ($row_rs = $this->conn->FetchRow($rs)){
				$arr_id_u_g[$i] = $row_rs[0];
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

			$sql = "SELECT ID_MINA FROM mina WHERE ID_MUN IN (".$id_mun_s.") ORDER BY FECHA";

			$arr_id_u_g = Array();
			$i = 0;
			$rs = $this->conn->OpenRecordset($sql);
			while ($row_rs = $this->conn->FetchRow($rs)){
				$arr_id_u_g[$i] = $row_rs[0];
				$i++;
			}
		}

		if (isset($_POST["id_depto"])){

			if ($arreglos == "")	$arreglos = "\$arr_id_u_g";
			else					$arreglos .= ",\$arr_id_u_g";
		}

		//INTERSECCION DE LOS ARREGLOS PARA REALIZAR LA CONSULTA
		if (count(explode(",",$arreglos)) > 1 ){
			eval("\$arr_id = array_intersect($arreglos);");
		}
		else{
			eval("\$arr_id = $arreglos;");
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
		if ($num_arr > 0){
			echo "<tr><td colspan='8' align='right'>Exportar a: <input type='image' src='images/consulta/generar_pdf.gif' onclick=\"document.getElementById('pdf_mina').value = 1;\">&nbsp;&nbsp;<input type='image' src='images/consulta/excel.gif' onclick=\"document.getElementById('pdf_mina').value = 2;\"></td>";
		}
		echo "<tr><td align='center' class='titulo_lista' colspan=8>CONSULTA DE EVENTOS CON MINA EN: ";

		//TITULO DE DEPTO
		if (isset($_POST["id_depto"])){
			echo "<img src='images/flecha.gif'> Departamento ";
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
			echo "<img src='images/flecha.gif'> Municipio";

			$t = 0;
			foreach($id_mun as $id_t){
				$vo  = $municipio_dao->Get($id_t);
				if ($t == 0)	echo "<b>".$vo->nombre."</b>";
				else			echo ", <b>".$vo->nombre."</b>";
				$t++;
			}
			echo "<br>";
		}
		echo "</td></tr>";
		echo "<tr><td>&nbsp;</td></tr>";

		if ($num_arr > 0){

			echo "<tr class='titulo_lista'>
					<td width='200'>Municipio</td>
					<td width='250'>Tipo de Evento</td>
					<td width='200'>Fecha</td>
					<td width='150'>Estado</td>
					<td width='60'>Sexo</td>
					<td width='150'>Edad</td>
					<td width='150'>Condición</td>
					<td width='100'>Eventos</td>
			    </tr>";

			$ii = 0;
			for($p=0;$p<$num_arr;$p++){
				$style = "";
				if (fmod($p+1,2) == 0)  $style = "fila_lista";

				echo "<tr class='".$style."'>";

				//MPIO
				$mun = $municipio_dao->Get($arr[$p]->id_mun);
				echo "<td>".$mun->nombre."</td>";

				//TIPO
				$vo = $tipo_eve_dao->Get($arr[$p]->id_tipo_eve);
				echo "<td>".$vo->nombre."</td>";

				//FECHA
				echo "<td>".$arr[$p]->fecha."</td>";

				//ESTADO
				$vo = $estado_dao->Get($arr[$p]->id_estado);
				echo "<td>".$vo->nombre."</td>";

				//SEXO
				$vo = $sexo_dao->Get($arr[$p]->id_sexo);
				echo "<td>".$vo->nombre."</td>";

				//EDAD
				$vo = $edad_dao->Get($arr[$p]->id_edad);
				echo "<td>".$vo->nombre."</td>";

				//CONDICION
				$vo = $condicion_dao->Get($arr[$p]->id_condicion);
				echo "<td>".$vo->nombre."</td>";

				echo "<td>".$arr[$p]->cantidad."</td>";

			}
			echo "<tr><td>&nbsp;</td></tr>";
		}
		else{
			echo "<tr><td align='center'><br><b>NO SE ENCONTRARON EVENTOS CON MINAS</b></td></tr>";
		}

		echo "<input type='hidden' name='id_minas' value='".implode(",",$arr_id)."'>";
		echo "<input type='hidden' id='que_mina' name='que_mina' value='1'>";
		echo "</table>";

	}

	/**
	* Importa los registros de eventos con minas
	* @access public
	* @param file $userfile Archivo CSV a importar
	* @param Int $operacion Borrar=1 - Agregar=2
	* @param String $separador Caracter separador de columnas
	*/
	function ImportarCSV($userfile,$operacion,$separador){

		$archivo = New Archivo();
		$mun_dao = New MunicipioDAO();
		$condicion_dao = New CondicionMinaDAO();
		$estado_dao = New EstadoMinaDAO();
		$sexo_dao = New SexoDAO();
		$edad_dao = New EdadDAO();
		$tipo_eve_dao = New TipoEventoDAO();
		$evento_dao = New EventoConflictoDAO();

		//Arreglos de DAtos
		//Tipo Evento
		$arr_t_e = Array("map" => 31, "muse" => 32);
		
		//Estados
		$tmp = $estado_dao->GetAllArray('');
		foreach($tmp as $vo){
			$arr_e_m[strtolower($vo->nombre)] = $vo->id;
		}

		//Condicion
		$tmp = $condicion_dao->GetAllArray('');
		foreach($tmp as $vo){
			$arr_c_m[strtolower($vo->nombre)] = $vo->id;

		}

		//Edad
		$tmp = $edad_dao->GetAllArray('');
		foreach($tmp as $vo){
			$arr_edad[strtolower($vo->nombre)] = $vo->id;
		}
		//Sexo
		$tmp = $sexo_dao->GetAllArray('');
		foreach($tmp as $vo){
			$arr_sexo[strtolower($vo->nombre)] = $vo->id;
		}

		$file_tmp = $userfile['tmp_name'];
		$file_nombre = $userfile['name'];

		$path = "mina/csv/".$file_nombre;

		$archivo->SetPath($path);
		$archivo->Guardar($file_tmp);

		$fp = $archivo->Abrir($path,'r');
		$cont_archivo = $archivo->LeerEnArreglo($fp);
		$archivo->Cerrar($fp);
		$num_rep = count($cont_archivo);

		$linea_tmp = $cont_archivo[1];
		$linea_tmp = explode("$separador",$linea_tmp);

		$num_cols_file = count($linea_tmp);
		$num_cols_form = 8;

		if ($num_cols_file != $num_cols_form){
		    ?>
		    <script>
		    alert("El número de columnas del archivo CSV no es correcto, deben existir 8 columnas");
		    location.href = 'index.php?accion=importar';
		    </script>
		    <?
		}


		if ($num_rep > 0){
			if ($operacion == 1){
				//VACIA LA TABLA
				$this->EmptyTable();
			}
		}
		$ex = 0;
		for($r=1;$r<$num_rep;$r++){
			$linea = $cont_archivo[$r];

			$linea = explode("$separador",$linea);

			if (count($linea > 0) && $linea[0] != ""){

				//MPIO
				$mina->id_mun =  array($linea[0]);

				$mina->id_cat = array(4); //Uso de explosivos remanentes de guerra
				$mina->id_subcat = array($arr_t_e[strtolower($linea[1])]);
				
				$mina->id_fuente = array(6); //Entidad Pública
				$mina->id_subfuente = array(78); //Observatorio de minas de la vicepresidencia


				//FECHA
				if ($linea[2] != ""){
					//ELIMINA LA HORA SI EXISTE
					$f_tmp = explode(" ",$linea[2]);
					$f_tmp = $f_tmp[0];

					$f_tmp = explode("/",$f_tmp);
					$dd = $f_tmp[0];
					$mm = $f_tmp[1];

					//SI EL FORMATO ESTA MAL EN EL ORDEN DE DIA Y MES SE INVIERTE
					if ($mm > 12){
						$dd = $f_tmp[1];
						$mm = $f_tmp[0];
					}
					$aaaa = $f_tmp[2];

					//ALGUNOS AÑOS VIENEN DE 2 DIGITOS - TODOS LOS AÑOS SON MAYORES A 2000
					if ($aaaa < 100){
						$aaaa += 2000;
					}
					$fecha = $aaaa."-".$mm."-".$dd;
				}
				else{
					$fecha = "1111-11-11";
				}
				
				$mina->fecha_evento = $fecha;
				$mina->fecha_fuente = array($fecha);

				//ID ESTADO
				$col = $linea[3];
				$mina->id_estado = 1;
				if (isset($arr_e_m[strtolower($col)])){
					$mina->id_estado = array($arr_e_m[strtolower($col)]);
				}

				//ID SEXO
				$col = $linea[4];
				$mina->id_sexo = 1;
				if (isset($arr_sexo[strtolower($col)])){
					$mina->id_sexo = array($arr_sexo[strtolower($col)]);
				}

				//ID EDAD
				$col = $linea[5];
				$mina->id_edad = 1;
				if (isset($arr_edad[strtolower($col)])){
					$mina->id_edad = array($arr_edad[strtolower($col)]);
					
					//Mayor de Edad
					if ($mina->id_edad[0] == 2){
						$mina->id_rango_edad = array(11); //Sin Info
					}
					//Menor de Edad
					else if ($mina->id_edad[0] == 3){
						$mina->id_rango_edad = array(10); //Sin Info
					}
				}

				//ID CONDICION
				$col = $linea[6];
				$mina->id_condicion = 1;
				if (isset($arr_c_m[strtolower($col)])){
					$mina->id_condicion = array($arr_c_m[strtolower($col)]);
				}

				//CANIDAD
				$mina->num_victimas = array($linea[7]);				
				
				$mina->id_subcondicion = array(0);
				$mina->id_ocupacion = array(0);
				$mina->id_etnia = array(0);
				$mina->id_subetnia = array(0);
				$mina->id_actor = array(20); //Sin Determinar
				
				$mina->desc_fuente = array("");
				$mina->refer_fuente = array("");
				
				$mina->lugar = array("");
				
				$mina->sintesis = "";

				//INSERTA EL REGISTRO
				$evento_dao->Insertar($mina,0,1,0,0,0);
				$ex++;
			}
		}
		echo "<script>";
		echo "alert('Se cargaron : ".$ex." Registros.');";
		//echo "location.href='index.php?m_e=mina&accion=listar&class=MinaDAO&method=ListarTabla&param=';";
		echo "</script>";
	}
}

/**
 * Ajax de Mina
 *
 * Contiene los metodos para Ajax de la clase Mina
 * @author Ruben A. Rojas C.
 */

Class MinaAjax extends MinaDAO {

	/**
	* Lista los Datos en una Tabla y Grafica los datos - GRAFICAS Y RESUMENES
	* @access public
	* @param Int $reporte Reporte
	* @param String $filtros Filtros
	* @param Int $depto Desagregacion geografica, 0 = Mpal 1 = Deptal 2 = Nacional
	* @param String $ubicacion Id de la Ubicacion
	* @param String $f_ini	Año Inicial
	* @param String $f_fin	Año Final
	* @param Int $grafica	Tipo de gráfica
	* @param string $ejex Años o Meses en el ejex
	* @param string $dato_para_reporte_5 Municipios o Departamentos
	* @param string $acc_vic Numero de Accidentes o Numero de Victimas
	*/
	function GraficaResumenMina($reporte,$filtros,$depto,$ubicacion,$f_ini,$f_fin,$grafica,$ejex,$dato_para_reporte_5,$acc_vic){

		require_once $_SERVER["DOCUMENT_ROOT"]."/sissh/admin/lib/common/graphic.class.php";
		require_once $_SERVER["DOCUMENT_ROOT"]."/sissh/admin/lib/libs_mina.php";

		//INICIALIZACION DE VARIABLES
		$depto_dao = New DeptoDAO();
		$municipio_dao = New MunicipioDAO();
		$edad_dao = New EdadDAO();
		$edad_dao = New EdadDAO();
		$sexo_dao = New SexoDAO();
		$condicion_dao = New CondicionMinaDAO();
		$estado_dao = New EstadoMinaDAO();

		$num_minas = array();
		$ini = $f_ini;
		$fin = $f_fin;
		$filtros_post  = $filtros;
		$title = ($acc_vic == 'acc') ? "Accidentes con Mina" : "Víctimas con Mina";

		//$mes_t = array("","Enero","Febrero","Marzo","Abril","Mayo","Junio","Julio","Agosto","Septiembre","Octubre","Noviembre","Diciembre");
		$mes_t = array("","Ene","Feb","Mar","Abr","May","Jun","Jul","Ago","Sep","Oct","Nov","Dic");
		$fields_table = array("","ID_SEXO","ID_CONDICION","ID_ESTADO","ID_EDAD","","1");
		$dato_para_reporte_5_txt = ($dato_para_reporte_5 == 'mpio') ? 'Municipios' : 'Departamentos';
		
		$where_mina = " AND id_scateven IN (31,32)";

		switch ($reporte){
			case 1:
				$arr = $sexo_dao->GetAllArray("ID_SEXO IN (".implode(",",$filtros_post).")");
				break;
			case 2:
				$arr = $condicion_dao->GetAllArray("ID_CONDICION_MINA IN (".implode(",",$filtros_post).")");
				break;
			case 3:
				$arr = $estado_dao->GetAllArray("ID_ESTADO_MINA IN (".implode(",",$filtros_post).")");
				break;
			case 4:
				$arr = $edad_dao->GetAllArray("ID_EDAD IN (".implode(",",$filtros_post).")");
				break;
			case 6:
				$arr[0]->id = 1;
				break;
		}

		$num_reg_rep_5 = 10;  //numero de municipios para el reporte 5
		
		$nom_ubi = "Nacional";
		if ($depto == 1){
			$ubi = $depto_dao->Get($ubicacion);
			$nom_ubi = $ubi->nombre;
		}
		else if ($depto == 0){
			$ubi = $municipio_dao->Get($ubicacion);
			$nom_ubi = $ubi->nombre;
		}

		//Determina si el reporte es por periodo
		$por_periodo = 0;
		$por_mpio = 0;
		if (in_array($reporte,array(1,2,3,4,6))){
			$por_periodo = 1;
		}
		else if ($reporte == 5){
			$por_mpio = 1;
		}
		
		if ($por_periodo == 1){
			if ($ejex == 'aaaa'){
				$ejex_title =  'Año';
				$ejex_angulo = 0;
			}
			else{
				$ejex_angulo = 2;
				switch ($ejex){
					case 'mes':
						$ejex_title = 'Mes';
					break;
				}
			}
			
			//PERIODOS - AÑOS o MESES o etc
			for ($a=$ini;$a<=$fin;$a++){
				if ($ejex == 'aaaa'){
					$periodo[] = $a;
					$periodo_text[] = $a;
				}
				else{
					switch ($ejex){
						case 'mes':
							$hoy = getdate();
							$mes_actual = $hoy['mon'];
							$a_actual = $hoy['year'];
							
							$m_fin = ($a == $a_actual) ? $mes_actual : 13;
							for($m=1;$m<$m_fin;$m++){
								$periodo[] = $m."_".$a;
								$periodo_text[] = $mes_t[$m]." ".substr($a,2,2);
							}
						break;
					}
				}
			}
			
		}
			
		//SE CONSTRUYE EL SQL
		$condicion = "";
		$arreglos = "";

		//UBIACION GEOGRAFICA
		if ($depto == 1){

			if ($por_periodo == 1){
				
				//for ($a=$ini;$a<=$fin;$a++){
				foreach($periodo as $per){
					
					if ($ejex == 'aaaa'){
						$a = $per;
					}
					else{
						switch ($ejex){
							case 'mes':
								$per_tmp = explode("_",$per);
								$m = $per_tmp[0];			
								$a = $per_tmp[1];			
							break;
						}
					}
				
					foreach ($arr as $vo){

						$cond = $fields_table[$reporte]." = ".$vo->id." AND YEAR(FECHA_REG_EVEN) = $a";
						
						if ($ejex == 'mes'){
							$cond .= " AND MONTH(FECHA_REG_EVEN) = $m";
						}
						
						$num_minas[$per][$vo->id] = ($acc_vic == 'vic') ? $this->GetValor($cond,$ubicacion,1) : $this->GetValorAcc($cond,$ubicacion,1);
					}
				}
			} //fin por periodo
			
			if ($por_mpio == 1){
				$ejex_title = $dato_para_reporte_5_txt;
				$ejex_angulo = 2;
			
				if ($reporte == 5){
					
					$sql = "SELECT sum(CANT_VICTIMA) as num, id_mun as id FROM descripcion_evento JOIN evento_c USING(id_even) JOIN evento_localizacion USING(id_even) JOIN victima USING(id_deseven)";
					$sql .= " WHERE YEAR(FECHA_REG_EVEN) BETWEEN $ini AND $fin";
					$sql .= $where_mina;
					$sql .= " GROUP BY id ORDER BY num DESC LIMIT 0,$num_reg_rep_5 ";

					$rs = $this->conn->OpenRecordset($sql);
					while ($row = $this->conn->FetchRow($rs)){
						$num_minas[$row[1]] = $row[0];
					}
				} //Fin reporte 5
			}//Fin reporte por mpio
		}
		
		//Nacional
		else if ($depto == 2){
			if ($por_periodo == 1){
				
				//for ($a=$ini;$a<=$fin;$a++){
				foreach($periodo as $per){
					
					if ($ejex == 'aaaa'){
						$a = $per;
					}
					else{
						switch ($ejex){
							case 'mes':
								$per_tmp = explode("_",$per);
								$m = $per_tmp[0];			
								$a = $per_tmp[1];			
							break;
						}
					}

					foreach ($arr as $vo){
	
						$cond = $fields_table[$reporte]." = ".$vo->id." AND YEAR(FECHA_REG_EVEN) = $a";
						
						if ($ejex == 'mes'){
							$cond .= " AND MONTH(FECHA_REG_EVEN) = $m";
						}
						
						$num_minas[$per][$vo->id] = ($acc_vic == 'vic') ? $this->GetValor($cond,'',0) : $this->GetValorAcc($cond,'',0);
					}
				}
			} //fin por periodo
			
			if ($por_mpio == 1){
				$ejex_title = $dato_para_reporte_5_txt;
				$ejex_angulo = 2;
			
				if ($reporte == 5){
					
					$col_geo = ($dato_para_reporte_5 == 'mpio') ? 'id_mun' : 'id_depto';
					
					$sql = "SELECT sum(CANT_VICTIMA) as num, $col_geo as id FROM descripcion_evento JOIN evento_c USING(id_even) JOIN evento_localizacion USING(id_even) JOIN victima USING(id_deseven)";
					
					if ($dato_para_reporte_5 == 'depto'){
						$sql .= " JOIN municipio USING(id_mun)";
					}
					
					$sql .= " WHERE YEAR(FECHA_REG_EVEN) BETWEEN $ini AND $fin";
					$sql .= $where_mina;
					$sql .= " GROUP BY id ORDER BY num DESC LIMIT 0,$num_reg_rep_5 ";
					
					$rs = $this->conn->OpenRecordset($sql);
					while ($row = $this->conn->FetchRow($rs)){
						$num_minas[$row[1]] = $row[0];
					}
			} //Fin reporte 5
			}//Fin reporte por mpio
		}

		//MUNICIPIO
		else {
			if ($por_periodo == 1){
				
				//for ($a=$ini;$a<=$fin;$a++){
				foreach($periodo as $per){
					
					if ($ejex == 'aaaa'){
						$a = $per;
					}
					else{
						switch ($ejex){
							case 'mes':
								$per_tmp = explode("_",$per);
								$m = $per_tmp[0];			
								$a = $per_tmp[1];			
							break;
						}
					}

					foreach ($arr as $vo){
	
						$cond = $fields_table[$reporte]." = ".$vo->id." AND YEAR(FECHA_REG_EVEN) = $a";
						
						if ($ejex == 'mes'){
							$cond .= " AND MONTH(FECHA_REG_EVEN) = $m";
						}
						
						$num_minas[$per][$vo->id] = ($acc_vic == 'vic') ? $this->GetValor($cond,$ubicacion,2) : $this->GetValorAcc($cond,$ubicacion,2);
					}
				}
			}  // fin por periodo
		}

		$num_arr = count($num_minas);

		//GRAFICA
		$si_grafica = 0;
		$PG = new PowerGraphic;

		$PG->title     = $title;
		$PG->skin      = 1;
		$PG->type      = $grafica;
		$PG->credits   = 0;

		$PG->axis_x    = $ejex_title;
		$PG->axis_y    = 'Personas';
				
		echo "<br>";
		echo "<table align='center' cellspacing='1' cellpadding='3' width='100%' border='0'>";
		$html = "<tr><td align='left'><img src='images/consulta/excel.gif'>&nbsp;<a href='consulta/excel.php?f=eventos_mina_".$nom_ubi."_sidih'>Exportar tabla a Hoja de c&aacute;lculo</a></td></tr>";
		$html .= "<tr><td>Localizaci&oacute;n Geogr&aacute;fica: <b>$nom_ubi</b></td></tr>";
		$html .= "<tr>
				<td valign='top'><div class='tabla_grafica_conteo' style='overflow:auto;width:250px'>
					<table border='0' cellpadding='4' cellspacing='1' width='250'>";
		

		//ESCRIBE TITULO
		$xls = "<table><tr><td>$title</td></tr>";

		if ($num_arr > 0){

			if ($por_periodo == 1){

				$html .= "<tr class='fila_tabla_conteo'><td>$ejex_title</td>";
				
				if ($reporte != 6){
					foreach ($arr as $vo){
						$html .= "<td><b>".$vo->nombre."</b></td>";
						$xls .= "<td><b>".$vo->nombre."</b></td>";
					}
				}
				else if ($reporte == 6){
					$html .= "<td align='center'><b>Num. V&iacute;ctimas</b></td>";
					$xls .= "<td><b>Num. V&iacute;ctimas</b></td>";
				}
	
				$html .= "</tr>";
				$xls .= "</tr>";
				
				foreach($periodo as $p=>$a){
					$html .= "<tr class='fila_tabla_conteo'><td>".$periodo_text[$p]."</td>";
					$xls .= "<tr class='fila_tabla_conteo'><td>".$periodo_text[$p]."</td>";
					
					$aa = 0;	
					if ($reporte != 6){
						$num = 0;
						$f = 1;
						foreach ($arr as $vo){
							$num = $num_minas[$a][$vo->id];
							
							$html .= "<td align='right'>".number_format($num)."</td>";
							$xls .= "<td align='right'>".$num."</td>";
							
							//Para grafica
							eval("\$PG->graphic_".$f." = '".$vo->nombre."';");
							$PG->data[$a][] = $num;
						
							if ($num > 0)	$si_grafica = 1;
						
							$f++;
						}
					}
					else if ($reporte == 6){
						
						$num = $num_minas[$a][1];
						
						$html .= "<td align='right'>".number_format($num)."</td>";
						$xls .= "<td align='right'>".$num."</td>";
						
						//Para grafica
						$PG->data[$a][] = $num;
					
						if ($num > 0)	$si_grafica = 1;
					}
	
					$PG->x[] = $periodo_text[$p];
					
					$aa++;
	
					$html .= "</tr>";
					$xls .= "</tr>";
	
				}
			} //fin por periodo
			if ($por_mpio == 1){
				$html .= "<tr class='fila_tabla_conteo'><td><b>Codigo</b></td><td><b>$dato_para_reporte_5_txt</b></td><td><b>Cantidad</b></td></tr>";
			
				foreach ($num_minas as $id=>$num){
					$vo = ($dato_para_reporte_5 == 'mpio') ? $municipio_dao->get($id) : $depto_dao->get($id);
				
					$html .= "<tr class='fila_tabla_conteo'><td align='right'>$id</td><td>$vo->nombre</td><td>".number_format($num)."</td></tr>";
					
					$PG->x[] = $vo->nombre;
					$PG->data[] = $num;
					if ($num > 0)	$si_grafica = 1;
				}
			}
			
			$_SESSION["xls"] = $xls;
		}
		else{
			$html .= "<tr><td align='center'><br><b>NO SE ENCONTRARON DATOS DE EVENTOS POR MINA</b></td></tr>";
		}

		// Valores API
		if (!isset($_GET['api']) || (isset($_GET['api']) && isset($_GET["tabla_api"]) && $_GET["tabla_api"] == 1)){
			echo $html;
		}
		
		echo "</table></div></td>";

		//Eje x
		foreach ($PG->x as $i=>$x){
			if ($i == 0)	$ejex = "'".utf8_encode($x)."'";
			else			$ejex .= ",'".utf8_encode($x)."'";
		}
		
		$path = $_SERVER["DOCUMENT_ROOT"]."/sissh/admin/lib/common/open-flash-chart";
		$path_in = $_SERVER["DOCUMENT_ROOT"]."/sissh/admin/lib/common/open-flash-chart/";
					
		/********************************************************************************
		//PARA GRAFICA OPEN CHART v2
		/*******************************************************************************/
		if (in_array($reporte,array(1,2,3,4)) && $grafica !=5 ){
		
			$content = "<? include_once('$path/php-ofc-library_v2/sidihChart.php' );\n";

			if ($por_periodo == 1){
				$content .= "\$bar_stack = new bar_stack();\n";
				
				$max_y = 0;
				foreach($PG->data as $bar_data){
					$content .= "\$bar_stack->append_stack(array(".implode(",",$bar_data)."));\n";
					
					if ($max_y < array_sum($bar_data))	$max_y = array_sum($bar_data);
				}
				
				$graph = 'bar_stack';
			}
	
			include ("$path_in/php-ofc-library_v2/sidihChart.php");
			
			$g = new sidihChart();
			$max_y = $g->maxY($max_y);
			
			$content .= "\$y = new y_axis();\n";
			$content .= "\$y->set_range( 0, $max_y, ($max_y/10) );\n";
			$content .= "\$y->set_colour('#FFFFFF');\n";
			$content .= "\$y->set_grid_colour('#FFFFFF');\n";
			
			$content .= "\$x = new x_axis();\n";
			$content .= "\$x->set_labels_from_array( array( $ejex ) );\n";
			$content .= "\$x->set_colour('#FFFFFF');\n";
			$content .= "\$x->set_grid_colour('#FFFFFF');\n";
			
			$content .= "\$chart = new sidihChart();\n";
			$content .= "\$title = new title( \"".utf8_encode($title)."\" );\n";
			$content .= "\$chart->set_title(\$title );\n";
			$content .= "\$chart->add_element( \$$graph );\n";
			$content .= "\$chart->set_x_axis( \$x );\n";
			$content .= "\$chart->add_y_axis( \$y );\n";
	
			$content .= "echo \$chart->toPrettyString();";
			
			$data_file = 'chart-data_v2.php';
			$path_openchart_flash_object = 'php-ofc-library_v2/open-flash-chart-object.php';
		}
		else{
		
			/********************************************************************************
			//PARA GRAFICA OPEN CHART V1
			/*******************************************************************************/
			$chk_chart = array('bar' => '');
			//$chk_chart[$chart] = ' selected ';
			$font_size_key  =10;
			$font_size_x_label = 8;
			$font_size_y_label = 8;
			$chart = 'bar';
			
			//Estilos 
			$chart_style = array('bar' => array('alpha' => 90,'color' => array('#0066ff','#639F45','#A7C8DE','#639F45','#639F45','#639F45','#EC3838')));
			
			//Variable de sesion que va a ser el nomnre dela grafica al guardar
			$_SESSION["titulo_grafica"] = $title;
			
			$content = "<?
			include_once('$path/php-ofc-library/sidihChart.php' );
			
			\$g = new sidihChart();
			
			\$g->title('".utf8_encode($title)."');
			
			// label each point with its value
			\$g->set_x_labels( array(".$ejex."));
			\$g->set_x_label_style( $font_size_x_label, '#000000',2);";
			
			$max_y = 0;
			
			if ($grafica != 5){
				
				//if ($por_mpio == 1){
				if ($reporte == 5){
					$data = implode(",",$PG->data);
					$max_y = max($PG->data);
				}
				else if ($reporte == 6){
					$d = 0;
					foreach ($PG->data as $dat){
						if ($d == 0)	$data = implode(",",$dat);
						else			$data .= ",".implode(",",$dat);
						
						if ($max_y < max($dat))	$max_y = max($dat);
						
						$d++;
					}
				}
				$content .= "\$bar = new bar(".$chart_style[$chart]['alpha'].", '".$chart_style[$chart]['color'][0]."' );\n";
				$content .= "\$bar->data = array(".$data.");\n";
				$content .= "\$g->data_sets[] = \$bar;";
					
				$content .= "\$g->set_tool_tip( '#x_label# <br> #val# Personas' );";
			}
			else{
				
				if ($por_periodo == 1){
					
					foreach($PG->data as $val_arr){
						$total[] = array_sum($val_arr);
					}					
					//Eje y
					$data = $total;
				}
				
				if ($por_mpio == 1){
					$data = $PG->data;
				}
				
				$ejey = implode(",",$data);
				
				$content .= "\$g->pie(100,'#CCCCCC','{font-size: 10px; color: #000000;');\n
							 \$g->pie_values( array($ejey), array($ejex) );
							 \$g->pie_slice_colours( array('#0066ff','#99CC00','#ffcc00') );";
				
				$content .= "\$g->set_tool_tip( '#x_label# <br> #val# Personas' );";
			}
			
			include ("$path_in/php-ofc-library/sidihChart.php");
			$g = new sidihChart();
			
			$max_y = $g->maxY($max_y);
			
			$content .= "
					
			\$g->set_y_max( ".$max_y." );
			\$g->y_label_steps(5);
			//Espacio para el footer de la imagen con el logo - Toco con x_legend vacio, jejeje
			\$g->set_x_legend('".utf8_encode($ejex_title)."\n\n\n',12);
			
			\$g->set_y_legend('Personas',12);
			
			\$g->set_num_decimals(0);
			
			// display the data
			echo \$g->render();
			?>";
			
			$data_file = 'chart-data.php';
			$path_openchart_flash_object = 'php-ofc-library/open_flash_chart_object.php';
		}
		
		//MODIFICA EL ARCHIVO DE DATOS
		$archivo = New Archivo();
		$fp = $archivo->Abrir($_SERVER["DOCUMENT_ROOT"]."/sissh/$data_file",'w+');
		
		$archivo->Escribir($fp,$content);
		$archivo->Cerrar($fp);

		//IE Fix
		//Variable para que IE cargue el nuevo archivo de datos y cambie el tipo de grafica con los nuevos valores
		$nocache = time();
		include_once $path_in.$path_openchart_flash_object;
		
		//SI HAY DATOS :: GRAFICA
		if ($si_grafica == 1 && $grafica == 5){
			
			// API
			if (isset($_GET['grafica_api']) && $_GET['grafica_api'] == 1){
				echo "<td align='center' valign='top'><table>
				<tr><td class='tabla_grafica_conteo' colspan=1 width='600' bgcolor='#F0F0F0' align='center' id='swf_chart'><br>";
				
				open_flash_chart_object( 500, 350, $data_file.'?nocache='.$nocache,false );
			}

			?>
			<!--<td valign="top">
				<table id="table_grafica" cellspacing='0' cellpadding='5'>
					<tr>
						<td><img src='admin/lib/common/graphic.class.php?<?=$PG->create_query_string()?>' border=1 /></td>
					</tr>
				</table>
			</td>-->
			</tr>
			</table>
			<?
		}
		else if ($si_grafica == 0){
			?>
			<td valign="top" width='600'>
				<table id="table_grafica" cellspacing='0' cellpadding='5'>
					<tr>
						<td><b>No se puede construir la gr&aacute;fica</b></td>
					</tr>
				</table>
			</td></tr>
			</table>
			<?
		}
		else{
			// API
			if (isset($_GET['grafica_api']) && $_GET['grafica_api'] == 1){
				echo "<td align='center' valign='top'><table>
				<tr><td class='tabla_grafica_conteo' colspan=1 width='600' bgcolor='#F0F0F0' align='center' id='swf_chart'><br>";
				open_flash_chart_object( 500, 350, $data_file.'?nocache='.$nocache,false);
			}
			?>
			<!--<td valign="top">
				<table id="table_grafica" cellspacing='0' cellpadding='5'>
					<tr>
						<td><img src='admin/lib/common/graphic.class.php?<?=$PG->create_query_string()?>' border=1 /></td>
					</tr>
				</table>
			</td>-->
			</tr>
			</table>
			<?
		}
		
		if (!isset($_GET["api"]) && $si_grafica == 1 && $reporte != 5){
			?>
		   <table><tr><td colspan="6">
			<br><br>		
			<input type='hidden' id='pdf' name='pdf'>
			<?
			echo "<input type='button' name='button' value='Generar Reporte' onclick=\"generarReporteMina();\" class='boton'>";
			//Opcion de Por Depto o Mpios para nacional
			if ($depto == 2){
				echo "&nbsp;&nbsp;<input type='radio' name='tipo_nal' value='deptos' checked>&nbsp;Listar todos los Departamentos&nbsp;&nbsp;&nbsp;
					  <input type='radio' name='tipo_nal' value='mpios'>&nbsp;Listar todos los Municipios&nbsp;<br>";
			}
			?>
			</td></tr></table>
			<br><br><span id='graResumenDesMsg'></span></td></tr>
			<tr><td id='reporteGraResumenMina' colspan='2'></td></tr></table>
			<?
		}
	}

	
	/**
	* Genera el reporte de Desplazamiento apartir de una gráfica - GRAFICAS Y RESUMENES
	* @access public
	* @param $reporte Reporte a mostrar
	* @param $exp_rec Clase de Desplazamiento. 1 = Expulsión 2 =Recepción
	* @param $filtros Filtros depende del reporte
	* @param $depto Desagregacion geografica 0 = Mpal 1 = Deptal 2 = Nacional
	* @param $ubicacion Id de la Ubicacion
	* @param $f_ini	Año Inicial
	* @param $f_fin	Año Final
	* @param $tipo_nal	Tipo de reporte nacional
	* @param string $ejex Años o Meses en el ejex
	* @param string $acc_vic Numero de Accidentes o Numero de Victimas
	*/
	function reporteGraResumenMina($reporte,$filtros,$depto,$ubicacion,$f_ini,$f_fin,$tipo_nal,$ejex,$acc_vic){

		set_time_limit(0);

		require_once $_SERVER["DOCUMENT_ROOT"]."/sissh/admin/lib/libs_mina.php";

		//INICIALIZACION DE VARIABLES
		$depto_dao = New DeptoDAO();
		$municipio_dao = New MunicipioDAO();
		$edad_dao = New EdadDAO();
		$edad_dao = New EdadDAO();
		$sexo_dao = New SexoDAO();
		$condicion_dao = New CondicionMinaDAO();
		$estado_dao = New EstadoMinaDAO();
		$archivo = New Archivo();
		$num_minas = array();
		$ini = $f_ini;
		$fin = $f_fin;
		$filtros_post  = $filtros;
		$title = "Accidentes con Mina";
		$cache = 1;   //1 para usar cache

		$fields_table = array("","ID_SEXO","ID_CONDICION","ID_ESTADO","ID_EDAD","",1);
		$mes_t = array("","Ene","Feb","Mar","Abr","May","Jun","Jul","Ago","Sep","Oct","Nov","Dic");
		$where_mina = " AND id_scateven IN (31,32)";

		$cache_idx = $reporte."_".implode("-",$filtros_post)."_".$depto."_".$ubicacion."_".$f_ini."_".$f_fin."_".$ejex."_".$tipo_nal."_".$acc_vic;
		
		$cache_file = $this->dir_cache_resumen."/".$cache_idx.".htm";
		$cache_file_xls = $this->dir_cache_resumen."/".$cache_idx."_xls.htm";

		//Manejo de Cache
		if ($archivo->Existe($cache_file) && $cache == 1){
			$fp = $archivo->Abrir($cache_file,'r');
			$fp_xls = $archivo->Abrir($cache_file_xls,'r');
			
			$html = $archivo->LeerEnString($fp,$cache_file);
			$xls = $archivo->LeerEnString($fp_xls,$cache_file_xls);
			
			$archivo->Cerrar($fp);
			$archivo->Cerrar($fp_xls);
		}
		else{
		
			switch ($reporte){
				case 1:
					$arr = $sexo_dao->GetAllArray("ID_SEXO IN (".implode(",",$filtros_post).")");
					break;
				case 2:
					$arr = $condicion_dao->GetAllArray("ID_CONDICION_MINA IN (".implode(",",$filtros_post).")");
					break;
				case 3:
					$arr = $estado_dao->GetAllArray("ID_ESTADO_MINA IN (".implode(",",$filtros_post).")");
					break;
				case 4:
					$arr = $edad_dao->GetAllArray("ID_EDAD IN (".implode(",",$filtros_post).")");
					break;
				case 6:
					$arr[0]->id = 1;
					break;
			}
			$num_arr = count($arr);
			
			//Determina si el reporte es por periodo
			$por_periodo = 0;
			$por_mpio = 0;
			if (in_array($reporte,array(1,2,3,4,6))){
				$por_periodo = 1;
			}
			else if ($reporte == 5){
				$por_mpio = 1;
				$num_arr = 1;
			}		
	
			if ($por_periodo == 1){
				if ($ejex == 'aaaa'){
					$ejex_title =  'Año';
					$ejex_angulo = 0;
				}
				else{
					$ejex_angulo = 2;
					switch ($ejex){
						case 'mes':
							$ejex_title = 'Mes';
						break;
					}
				}
				
				//PERIODOS - AÑOS o MESES o etc
				for ($a=$ini;$a<=$fin;$a++){
					if ($ejex == 'aaaa'){
						$periodo[] = $a;
						$periodo_text[] = $a;
					}
					else{
						switch ($ejex){
							case 'mes':
								$hoy = getdate();
								$mes_actual = $hoy['mon'];
								$a_actual = $hoy['year'];
								
								$m_fin = ($a == $a_actual) ? $mes_actual : 13;
								for($m=1;$m<$m_fin;$m++){
									$periodo[] = $m."_".$a;
									$periodo_text[] = $mes_t[$m]." ".substr($a,2,2);
								}
							break;
						}
					}
				}
				
			}
			
			$html ="<br><div style='overflow:auto;width:940px;height:500px;border:1px solid #E1E1E1;'>";
			$html .="<table align='center' class='tabla_reportelist_outer' border=0>";
			$html .="<tr><td>&nbsp;</td></tr>";
			$html .="<tr><td>Exportar a Hoja de C&aacute;lculo : ";
			$html .="<a href=\"#\" onclick=\"location.href='consulta/excel.php?f=reporte_eventos_con_mina_sidih';return false;\"><img src='images/consulta/excel.gif' border=0 title='Exportar a Excel'></a></td></tr>";	
			$html .="<tr><td colspan=3><table class='tabla_reportelist'>";
			
			$xls = '
			<STYLE TYPE="text/css"><!--
			.excel_celda_texto {mso-style-parent:text; mso-number-format:"\@";white-space:normal;}
			--></STYLE>';
			
			$xls .= "<table border=1>";
			
			$html .="<tr class='titulo_lista'><td></td><td></td>";
			$xls .="<tr><td></td><td></td>";
	
			foreach($periodo as $p=>$a){
				$html .="<td colspan='$num_arr' align='center'>$periodo_text[$p]</td>";
				$xls .= "<td colspan='$num_arr' align='center'>$periodo_text[$p]</td>";
			}
			$html .="</tr>";
			$xls .= "</tr>";
			
			$html .="<tr class='titulo_lista'><td>CODIGO</td><td>UBICACION</td>";
			$xls .= "<tr><td>CODIGO</td><td>UBICACION</td>";
	
			if ($por_periodo == 1){
				foreach($periodo as $per){
					if ($reporte != 6){
						foreach($arr as $vo){
							$html .="<td align='center'>$vo->nombre</td>";
							$xls .= "<td align='center'>$vo->nombre</td>";
						}
					}
					else if ($reporte == 6){
						$html .="<td align='center'><b>Num. V&iacute;ctimas</b></td>";
						$xls .= "<td><b>Num. V&iacute;ctimas</b></td>";
					}
				}
			}
			
			//UBIACION GEOGRAFICA
			if ($depto == 1){
	
				$id_depto_s = $ubicacion;
				$muns = $municipio_dao->GetAllArrayID("ID_DEPTO IN (".$id_depto_s.")",'');
	
				foreach($muns as $p=>$id_mun){
					
					$style = "";
					if (fmod($p+1,2) == 0)  $style = "fila_lista";
							
					$mun_vo = $municipio_dao->Get($id_mun);
					$html .="<tr class='$style'><td>$id_mun</td><td>$mun_vo->nombre</td>";
					$xls .= "<tr><td class='excel_celda_texto'>$id_mun</td><td>$mun_vo->nombre</td>";
					
					foreach($periodo as $per){
						
						if ($ejex == 'aaaa'){
							$a = $per;
						}
						else{
							switch ($ejex){
								case 'mes':
									$per_tmp = explode("_",$per);
									$m = $per_tmp[0];			
									$a = $per_tmp[1];			
								break;
							}
						}
						foreach ($arr as $vo){
		
							$cond = $fields_table[$reporte]." = ".$vo->id." AND YEAR(FECHA_REG_EVEN) = $a";
							
							if ($ejex == 'mes'){
								$cond .= " AND MONTH(FECHA_REG_EVEN) = $m";
							}
							
							$valor = ($acc_vic == 'vic') ? $this->GetValor($cond,$id_mun,2) : $this->GetValorAcc($cond,$id_mun,2);
							
							if ($valor > 0)	$html .="<td align='right'>".$valor."</td>";
							else			$html .="<td></td>";
							
							if ($valor > 0)	$xls .= "<td>".$valor."</td>";
							else			$xls .= "<td></td>";
						
						}
					}
					
					$html .="</tr>";
					$xls .= "</tr>";
				}
			}
			//Nacional
			else if ($depto == 2){
				
				if ($tipo_nal == 'deptos'){
					$id_deptos = $depto_dao->GetAllArrayID('');
					foreach ($id_deptos as $p=>$id_depto){
					
						$depto_vo = $depto_dao->Get($id_depto);
						
						$style = "";
						if (fmod($p+1,2) == 0)  $style = "fila_lista";
							
						$html .="<tr class='$style'><td>$id_depto</td><td>$depto_vo->nombre</td>";
						$xls .=  "<tr><td class='excel_celda_texto'>$id_depto</td><td>$depto_vo->nombre</td>";
						
						foreach($periodo as $per){
						
							if ($ejex == 'aaaa'){
								$a = $per;
							}
							else{
								switch ($ejex){
									case 'mes':
										$per_tmp = explode("_",$per);
										$m = $per_tmp[0];			
										$a = $per_tmp[1];			
									break;
								}
							}
							foreach ($arr as $vo){
			
								$valor = 0;
								$id_muns = $municipio_dao->GetAllArrayID("ID_DEPTO='$id_depto'",'');
								
								$cond = $fields_table[$reporte]." = ".$vo->id." AND YEAR(FECHA_REG_EVEN) = $a";
							
								if ($ejex == 'mes'){
									$cond .= " AND MONTH(FECHA_REG_EVEN) = $m";
								}
								
								$valor = ($acc_vic == 'vic') ? $this->GetValor($cond,$id_depto,1) : $this->GetValorAcc($cond,$id_depto,1);
							
								if ($valor > 0)	$html .="<td align='right'>".$valor."</td>";
								else			$html .="<td></td>";
								
								if ($valor > 0)	$xls .= "<td>".$valor."</td>";
								else			$xls .= "<td></td>";
								
							}
						}
					}
				}
				else{
					$muns = $municipio_dao->GetAllArrayID("","id_mun");
	
					foreach($muns as $p=>$id_mun){
						
						$style = "";
						if (fmod($p+1,2) == 0)  $style = "fila_lista";
						
						$mun_vo = $municipio_dao->Get($id_mun);
						$html .="<tr class='$style'><td>$id_mun</td><td>$mun_vo->nombre</td>";
						$xls .= "<tr><td class='excel_celda_texto'>$id_mun</td><td>$mun_vo->nombre</td>";
						
						foreach($periodo as $per){
							
							if ($ejex == 'aaaa'){
								$a = $per;
							}
							else{
								switch ($ejex){
									case 'mes':
										$per_tmp = explode("_",$per);
										$m = $per_tmp[0];			
										$a = $per_tmp[1];			
									break;
								}
							}
							foreach ($arr as $vo){
			
								$cond = $fields_table[$reporte]." = ".$vo->id." AND YEAR(FECHA_REG_EVEN) = $a";
								
								if ($ejex == 'mes'){
									$cond .= " AND MONTH(FECHA_REG_EVEN) = $m";
								}
								
								$valor = ($acc_vic == 'vic') ? $this->GetValor($cond,$id_mun,2) : $this->GetValorAcc($cond,$id_mun,2);
	
								if ($valor > 0)	$html .="<td align='right'>".$valor."</td>";
								else			$html .="<td></td>";
								
								if ($valor > 0)	$xls .= "<td>".$valor."</td>";
								else			$xls .= "<td></td>";
								
							}
						}
						
						$html .="</tr>";
						$xls .= "</tr>";
					}	
				}
	
			}
	
			//MUNICIPIO
			else {
				$id_mun = $ubicacion;
						
				$mun_vo = $municipio_dao->Get($id_mun);
				$html .="<tr class='fila_lista'><td>$id_mun</td><td>$mun_vo->nombre</td>";
				$xls .= "<tr><td class='excel_celda_texto'>$id_mun</td><td>$mun_vo->nombre</td>";
	
				foreach($periodo as $per){
							
					if ($ejex == 'aaaa'){
						$a = $per;
					}
					else{
						switch ($ejex){
							case 'mes':
								$per_tmp = explode("_",$per);
								$m = $per_tmp[0];			
								$a = $per_tmp[1];			
							break;
						}
					}
					foreach ($arr as $vo){
	
						$cond = $fields_table[$reporte]." = ".$vo->id." AND YEAR(FECHA_REG_EVEN) = $a";
								
						if ($ejex == 'mes'){
							$cond .= " AND MONTH(FECHA_REG_EVEN) = $m";
						}
						
						$valor = ($acc_vic == 'vic') ? $this->GetValor($cond,$id_mun,2) : $this->GetValorAcc($cond,$id_mun,2);
	
						if ($valor > 0)	$html .="<td align='right'>".$valor."</td>";
						else			$html .="<td></td>";
						
						if ($valor > 0)	$xls .= "<td>".$valor."</td>";
						else			$xls .= "<td></td>";
									
					}
				}
				
				$html .= "</tr>";
				$xls .= "</tr>";
				
			}
			
			$html .= "</table></div>";
			$xls .= "</table>";
					
			$this->creteFileCache($html,$cache_file);
			$this->creteFileCache($xls,$cache_file_xls);
		
		}
		
		echo $html;
		$_SESSION["xls"] = $xls;

	}
	
	/*
	* creteFile cache
	* @access public
	* @param string $file_content 
	* @param string $nom_file
	*/
	function creteFileCache($file_content,$nom_file){
		$file = New Archivo();
		
		//CREA UN ARCHIVO LOCAL
		$nom_archivo = $nom_file;
		
		$fp = $file->Abrir($nom_archivo,'w+');
		$file->Escribir($fp,$file_content);
		$file->Cerrar($fp);
	}	
}
?>
