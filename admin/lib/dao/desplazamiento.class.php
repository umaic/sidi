<?
/**
 * DAO de Desplazamiento
 *
 * Contiene los métodos de la clase Desplazamiento
 * @author Ruben A. Rojas C.
 */

Class DesplazamientoDAO {

	/**
	 * Conexion a la base de datos
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
	 * Numero de Registros en Pantalla para ListarTAbla
	 * @var string
	 */
	var $num_reg_pag;

	/**
	 * URL para redireccionar despues de Insertar, Actualizar o Borrar
	 * @var string
	 */
	var $url;

	/**
	 * Año que representa el periodo sin fecha
	 * @var int
	 */
	var $aaaa_sin_fecha;	

	/**
	 * Constructor
	 * Crea la conexion a la base de datos
	 * @access public
	 */
	function DesplazamientoDAO (){
		$this->conn = MysqlDb::getInstance();
		$this->tabla = "registro";
		$this->columna_id = "cons_sur";
		$this->columna_nombre = "";
		$this->columna_order = "cons_sur";
		$this->num_reg_pag = 50;
		$this->url = "index.php?accion=listar&class=DesplazamientoDAO&method=ListarTabla&param=";
		$this->aaaa_sin_fecha = 1899;
		$this->dir_cache_resumen = $_SERVER["DOCUMENT_ROOT"]."/sissh/consulta/resumen/desplazamiento";
	}

	/**
	 * Consulta los datos de una Desplazamiento
	 * @access public
	 * @param int $id ID del Desplazamiento
	 * @return VO
	 */
	function Get($id){
		$sql = "SELECT * FROM ".$this->tabla." WHERE ".$this->columna_id." = ".$id;
		$rs = $this->conn->OpenRecordset($sql);
		$row_rs = $this->conn->FetchObject($rs);

		//Crea un VO
		$vo = New Desplazamiento();

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
	 * Retorna la fecha de corte para una fuente
	 * @access public
	 * @param int id_fuente
	 * @param string format, numero=AAAA-MM-DD, letra= DD de Mes de AAAA
	 * @return string
	 */
	function GetFechaCorte($id_fuente,$format='numero'){

		$meses = array ("","Ene","Feb","Mar","Abr","May","Jun","Jul","Ago","Sep","Oct","Nov","Dic");

        $sql = "SELECT FECHA_CORTE FROM fuente_desplazamiento WHERE ID_FUEDES = $id_fuente";
		$rs = $this->conn->OpenRecordset($sql);
		if($row_rs = $this->conn->FetchRow($rs)){
			$fecha = $row_rs[0];

			if ($format == 'letra'){
				$f = explode("-",$fecha);

				$fecha = 1*$f[2]." ".$meses[$f[1]*1]." ".$f[0];
			}
			return $fecha;
		}
		else{
			return 0;
		}
	}

	/**
	 * Retorna los años en los que hay datos dada una fuente
	 * @access public
	 * @param int id_fuente
	 * @return array
	 */
	function getAAAAByFuente($id_fuente){

		$aaaa = array();

		$sql = "SELECT DISTINCT(aaaa) FROM registro_consulta_tmp WHERE id_fuente = $id_fuente";
		$rs = $this->conn->OpenRecordset($sql);
		while ($row = $this->conn->FetchRow($rs)){
			$aaaa[] = $row[0];
		}

		return $aaaa;

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
	 * Consulta los datos de los Desplazamiento que cumplen una condiciï¿½n
	 * @access public
	 * @param string $condicion Condiciï¿½n que deben cumplir los Desplazamiento y que se agrega en el SQL statement.
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
			$vo = New Desplazamiento();
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
	 * Consulta el valor de Desplazamiento para un periodo dado
	 * @access public
	 * @param int $exp 1=expulsion , 0=recepcion
	 * @param int $id_fuente
	 * @param int $id_tipo
	 * @param int $id_periodo
	 * @param int $id_ubicacion
	 * @param int $dato_para  1=Deptal, 2=Mpal, 3=Nal
     * @param string $personas Retorna personas
     *
	 * @return number
	 */
	function GetValorToReport($exp,$id_fuente,$id_tipo,$id_periodo,$id_ubicacion,$dato_para,$personas=''){

		$mun_dao = New MunicipioDAO();

		$corregimientos_vichada = "'99752','99760','99572'";  //Estos se le deben sumar al 99773 !!!

		$valor = 0;
		if ($dato_para == 1){

			if ($exp == 1){
				$sql = "SELECT VALOR,PERSONAS FROM registro WHERE ID_DEPTO_ID_DEPTO = '$id_ubicacion' AND ID_PERIO = $id_periodo AND ID_FUEDES = $id_fuente";
			}
			else{
				$sql = "SELECT VALOR,PERSONAS FROM registro WHERE ID_DEPTO = '$id_ubicacion' AND ID_PERIO = $id_periodo AND ID_FUEDES = $id_fuente";
			}

			//APLICA TIPO DE DESPLAZAMIENTO
			if ($id_tipo > 0){
				$sql .= " AND ID_TIPO_DESPLA = $id_tipo";
			}

			$rs = $this->conn->OpenRecordset($sql);
			$row_rs = $this->conn->Fetchrow($rs);
			$valor = $row_rs[0];

			if ($valor == 0){
				/*
				   $muns = ($id_ubicacion != '00') ? $mun_dao->GetAllArrayID("ID_DEPTO ='$id_ubicacion'",'') : array('00000');

				   foreach ($muns as $m_m=>$id){
				   $m_m == 0 ? $id_muns = "'".$id."'" : $id_muns .= ",'".$id."'";
				   }
				 */
				if ($exp == 1){
					$sql = "SELECT sum(VALOR), sum(PERSONAS) FROM registro WHERE SUBSTRING(id_mun_id_mun,1,2) = '$id_ubicacion' AND ID_PERIO = $id_periodo AND ID_FUEDES = ".$id_fuente;
				}
				else{
					$sql = "SELECT sum(VALOR), sum(PERSONAS) FROM registro WHERE SUBSTRING(id_mun,1,2) = '$id_ubicacion' AND ID_PERIO = $id_periodo AND ID_FUEDES = ".$id_fuente;
				}

				//APLICA TIPO DE DESPLAZAMIENTO
				if ($id_tipo > 0){
					$sql .= " AND ID_TIPO_DESPLA = $id_tipo";
				}
				
				//echo $sql;
				$rs = $this->conn->OpenRecordset($sql);
				$row_rs = $this->conn->FetchRow($rs);
				$valor = $row_rs[0];
			}

		}
		else if ($dato_para == 2){


			if ($id_ubicacion == '99773'){
				$id_ubicacion = "'$id_ubicacion',$corregimientos_vichada";
			}
			else{
				$id_ubicacion = "'$id_ubicacion'";
			}

			if ($exp == 1){
				$sql = "SELECT sum(VALOR), sum(PERSONAS) FROM registro WHERE ID_MUN_ID_MUN IN ($id_ubicacion) AND ID_PERIO = $id_periodo AND ID_FUEDES = ".$id_fuente;
			}
			else{
				$sql = "SELECT sum(VALOR), sum(PERSONAS) FROM registro WHERE ID_MUN IN  ($id_ubicacion) AND ID_PERIO = $id_periodo AND ID_FUEDES = ".$id_fuente;
			}

			//APLICA TIPO DE DESPLAZAMIENTO
			if ($id_tipo > 0){
				$sql .= " AND ID_TIPO_DESPLA = $id_tipo";
			}

			$rs = $this->conn->OpenRecordset($sql);
			$row_rs = $this->conn->Fetchrow($rs);
			$valor = $row_rs[0];

		}
		//VALOR NACIONAL
		else if ($dato_para == 3){
			if ($exp == 1){
				$sql = "SELECT sum(VALOR), sum(PERSONAS) FROM registro WHERE ID_MUN_ID_MUN IS NOT NULL AND ID_MUN_ID_MUN <> '' AND ID_PERIO = $id_periodo AND ID_FUEDES = ".$id_fuente;
			}
			else{
				$sql = "SELECT sum(VALOR), sum(PERSONAS) FROM registro WHERE ID_MUN IS NOT NULL AND ID_MUN <> '' AND ID_PERIO = $id_periodo AND ID_FUEDES = ".$id_fuente;
			}

			//APLICA TIPO DE DESPLAZAMIENTO
			if ($id_tipo > 0){
				$sql .= " AND ID_TIPO_DESPLA = $id_tipo";
			}

			$rs = $this->conn->OpenRecordset($sql);
			$row_rs = $this->conn->Fetchrow($rs);
            $valor = (empty($personas)) ? $row_rs[0] : $row_rs[1];

		}
		return $valor;
	}

	/**
	 * Consulta el valor de Desplazamiento para un año dado (consulta a la tabla registro_consulta_tmp)
	 * @access public
	 * @param int $exp Clase de Desplzamiento: 1=Expulsion, 2=Recepcion
	 * @param int $id_fuente
	 * @param int $id_tipo Tipo de Desplazamiento: Individual o Masivo
	 * @param int $aaaa Año
	 * @param int $id_ubicacion
     * @param int $dato_para Tipo localizacion: 1=Deptal, 2=Mpal, 3=Nal
     * @param string $personas Retorna personas
     *
	 * @return int $valor;
	 */
	function GetValorToReportTotalAAAA($exp,$id_fuente,$id_tipo,$aaaa,$id_ubicacion,$dato_para,$personas=''){

		$mun_dao = New MunicipioDAO();

		$valor = 0;

		$rec = ($exp == 1) ? 0 : 1;

		$sql = "SELECT sum(VALOR), sum(PERSONAS) FROM registro_consulta_tmp WHERE ID_FUENTE = $id_fuente AND AAAA = $aaaa AND REC = $rec AND EXP = $exp";

		//DEPARTAMENTAL
		if ($dato_para == 1){
			$sql .= " AND ID_DEPTO = '$id_ubicacion' AND ID_MUN IS NULL";
		}

		else if ($dato_para == 2){
			$sql .= " AND ID_MUN = '$id_ubicacion'";
		}

		//VALOR NACIONAL
		else if ($dato_para == 3){
			$sql .= " AND ID_MUN IS NULL";
		}

		//APLICA TIPO DE DESPLAZAMIENTO
		if ($id_tipo > 0){
			$sql .= " AND ID_TIPO = $id_tipo";
		}

		//echo $sql;

		$rs = $this->conn->OpenRecordset($sql);
        $row_rs = $this->conn->Fetchrow($rs);

		$valor = (empty($personas)) ? $row_rs[0] : $row_rs[1];

		return $valor;
	}

	/**
	 * Lista los Desplazamiento que cumplen la condiciï¿½n en el formato dado
	 * @access public
	 * @param string $formato Formato en el que se listarï¿½n los Desplazamiento, puede ser Tabla o ComboSelect
	 * @param int $valor_combo ID del Desplazamiento que serï¿½ selccionado cuando el formato es ComboSelect
	 * @param string $condicion Condiciï¿½n que deben cumplir los Desplazamiento y que se agrega en el SQL statement.
	 */
	function ListarCombo($formato,$valor_combo,$condicion){
		$arr = $this->GetAllArray($condicion,'','');
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
	 * Lista las Desplazamientoes en una Tabla
	 * @access public
	 */
	function ListarTabla(){

		$depto_dao = New DeptoDAO();
		$mun_dao = New MunicipioDAO();
		$tipo_dao = New TipoDesplazamientoDAO();
		$clase_dao = New ClaseDesplazamientoDAO();
		$periodo_dao = New PeriodoDAO();
		$contacto_dao = New ContactoDAO();
		$fuente_dao = New FuenteDAO();
		$poblacion_dao = New PoblacionDAO();
		$arr = Array();
		$num_arr = 0;

		$condicion = '';

		$dato_para = 1;
		$dato_para_chk = Array("selected","");
		if (isset($_GET["dato_para"]) && $_GET["dato_para"] == 2){
			$dato_para = 2;
			$dato_para_chk[1] = "selected";
		}

		$id_depto_exp = -1;
		if (isset($_GET["id_depto_exp"]) && $_GET["id_depto_exp"] != -1){
			$id_depto_exp = $_GET["id_depto_exp"];
		}

		$id_depto_rec = -1;
		if (isset($_GET["id_depto_rec"]) && $_GET["id_depto_rec"] != -1){
			$id_depto_rec = $_GET["id_depto_rec"];
		}

		$id_mun_exp = -1;
		if (isset($_GET["id_mun_exp"]) && $_GET["id_mun_exp"] != -1){
			$id_mun_exp = $_GET["id_mun_exp"];
		}

		$id_mun_rec = -1;
		if (isset($_GET["id_mun_rec"]) && $_GET["id_mun_rec"] != -1){
			$id_mun_rec = $_GET["id_mun_rec"];
		}


		//CONDICION NIVEL DEPARTAMENTAL
		if (isset($_GET["id_depto_exp"]) && $_GET["id_mun_exp"] == -1 && $_GET["id_mun_rec"] == -1 && $dato_para == 1){
			$condicion = "ID_DEPTO_ID_DEPTO = '".$id_depto_exp."'";
		}

		if (isset($_GET["id_depto_rec"]) && $_GET["id_mun_exp"] == -1 && $_GET["id_mun_rec"] == -1 && $dato_para == 1){
			if ($condicion == '')	$condicion = "ID_DEPTO = '".$id_depto_rec."'";
			else					$condicion .= " AND ID_DEPTO = '".$id_depto_rec."'";
		}


		//CONDICION NIVEL MUNICIPAL
		if (isset($_GET["id_mun_exp"]) && $_GET["id_mun_exp"] != -1 && $dato_para == 2){
			$condicion = "ID_MUN_ID_MUN = '".$id_mun_exp."'";
		}
		if (isset($_GET["id_mun_rec"]) && $_GET["id_mun_rec"] != -1 && $dato_para == 2){
			if ($condicion == '')	$condicion = "ID_MUN = '".$id_mun_rec."'";
			else					$condicion .= " AND ID_MUN = '".$id_mun_rec."'";
		}

		//QUERY
		if ($dato_para == 1){
			if (isset($_GET["id_depto_exp"]) || isset($_GET["id_depto_rec"])){
				$arr = $this->GetAllArray($condicion,'','');
				$num_arr = count($arr);
			}
		}

		if ($dato_para == 2){
			if ($_GET["id_mun_exp"] != -1 || $_GET["id_mun_rec"] != -1){
				$arr = $this->GetAllArray($condicion,'','');
				$num_arr = count($arr);
			}
		}

		echo "<table align='center' cellspacing='1' cellpadding='5' width='750'>
			<tr><td>&nbsp;</td></tr>
			<tr class='titulo_lista'><td align='center' colspan=10><b>LISTA DE DATOS DE DESPLAZAMIENTO</b></td></tr>

			<tr>
			<td colspan='6'>
			Filtrar a nivel: <select id='dato_para' name='dato_para' class='select'>
			<option value=1 ".$dato_para_chk[0].">Departamental</option>
			<option value=2 ".$dato_para_chk[1].">Municipal</option>
			</select></td></tr>

			<tr>
			<td colspan='10'>
			Filtrar por Departamento <b>Expulsor</b>&nbsp;<select name='id_depto_exp' class='select' onchange=\"location.href='index.php?accion=listar&class=".$_GET["class"]."&method=ListarTabla&param=&id_mun_exp=".$id_mun_exp."&id_mun_rec=".$id_mun_rec."&dato_para='+document.getElementById('dato_para').value+'&id_depto_exp='+this.value\">
			<option value=-1>Seleccione alguno...</option>";
		$depto_dao->ListarCombo('combo',$id_depto_exp,'');
		echo "</select>&nbsp;&nbsp;";

		//MUN EXP.
		if (isset($_GET["id_depto_exp"]) && $dato_para == 2){
			echo "Municipio <b>Expulsor</b>&nbsp;<select name='id_mun_exp' class='select' onchange=\"location.href='index.php?accion=listar&class=".$_GET["class"]."&method=ListarTabla&param=&id_mun_rec=".$id_mun_rec."&dato_para='+document.getElementById('dato_para').value+'&id_depto_exp=".$id_depto_exp."&id_depto_rec=".$id_depto_rec."&id_mun_exp='+this.value\">
				<option value=-1>Seleccione alguno...</option>";
			$mun_dao->ListarCombo('combo',$id_mun_exp,'ID_DEPTO = '.$id_depto_exp);
			echo "</select></td>";
		}
		echo "</tr>

			<tr>
			<td colspan='10'>
			Filtrar por Departamento <b>Receptor</b>&nbsp;<select name='id_depto_rec' class='select' onchange=\"location.href='index.php?accion=listar&class=".$_GET["class"]."&method=ListarTabla&param=&id_mun_exp=".$id_mun_exp."&id_mun_rec=".$id_mun_rec."&dato_para='+document.getElementById('dato_para').value+'&id_depto_exp=".$id_depto_exp."&id_depto_rec='+this.value\">
			<option value=-1>Seleccione alguno...</option>";
		$depto_dao->ListarCombo('combo',$id_depto_rec,'');
		echo "</select>&nbsp;&nbsp;";

		//MUN REC.
		if (isset($_GET["id_depto_rec"])  && $dato_para == 2){
			echo "Municipio <b>Receptor</b>&nbsp;<select name='id_mun_exp' class='select' onchange=\"location.href='index.php?accion=listar&class=".$_GET["class"]."&method=ListarTabla&param=&id_mun_exp=".$id_mun_exp."&dato_para='+document.getElementById('dato_para').value+'&id_depto_exp=".$id_depto_exp."&id_depto_rec=".$id_depto_rec."&id_mun_rec='+this.value\">
				<option value=-1>Seleccione alguno...</option>";
			$mun_dao->ListarCombo('combo',$id_mun_rec,'ID_DEPTO = '.$id_depto_rec);
			echo "</select></td>";
		}

		echo "</tr>

			<tr class='titulo_lista'>
			<td width='50' align='center'>ID</td>";

		//NIVEL DEPARTAMENTAL
		if ($dato_para == 1){
			echo "<td>Depto. Expulsor</td>";
			echo "<td>Depto. Receptor</td>";
		}

		//NIVEL MUNICIPAL
		if ($dato_para == 2){
			echo "<td>Mpio. Expulsor</td>";
			echo "<td>Mpio. Receptor</td>";
		}

		echo "<td width='200'>Tipo</td>
			<td width='300'>Clase</td>
			<td width='300'>Fuente</td>
			<td width='300'>Periodo</td>
			<td width='300'>Poblaciï¿½n</td>
			<td width='150'>Cantidad</td>
			<td width='300' lign='center'>Registros: ".$num_arr."</td>
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
				echo "<td align='center'>".$arr[$p]->id."</td>";

				//NIVEL DEPARTAMENTAL
				if ($dato_para == 1){
					$depto = $depto_dao->Get($arr[$p]->id_depto_exp);
					echo "<td>".$depto->nombre."</td>";

					$depto = $depto_dao->Get($arr[$p]->id_depto_rec);
					echo "<td>".$depto->nombre."</td>";
				}

				//NIVEL MUNICIPAL
				if ($dato_para == 2){
					if ($arr[$p]->id_mun_exp != "" && $arr[$p]->id_mun_exp != 0){
						$vo = $mun_dao->Get($arr[$p]->id_mun_exp);
						echo "<td>".$vo->nombre."</td>";
					}
					else{
						echo "<td>&nbsp;</td>";
					}
					if ($arr[$p]->id_mun_rec != "" && $arr[$p]->id_mun_rec != 0){
						$vo = $mun_dao->Get($arr[$p]->id_mun_rec);
						echo "<td>".$vo->nombre."</td>";
					}
					else{
						echo "<td>&nbsp;</td>";
					}
				}

				//TIPO
				$vo = $tipo_dao->Get($arr[$p]->id_tipo);
				echo "<td>".$vo->nombre."</td>";

				//CLASE
				$vo = $clase_dao->Get($arr[$p]->id_clase);
				echo "<td>".$vo->nombre."</td>";

				//FUENTE
				$vo = $fuente_dao->Get($arr[$p]->id_fuente);
				echo "<td>".$vo->nombre."</td>";

				//PERIODO
				$vo = $periodo_dao->Get($arr[$p]->id_periodo);
				echo "<td>".$vo->nombre."</td>";

				//POBLACION
				$vo = $poblacion_dao->Get($arr[$p]->id_poblacion);
				echo "<td>".$vo->nombre_es."</td>";

				echo "<td>".$arr[$p]->cantidad."</td>";

				echo "<td>- <a href='".$_SERVER['PHP_SELF']."?accion=actualizar&id=".$arr[$p]->id."&dato_para=".$dato_para."'>Modificar</a><br>- <a href='index.php?accion=borrar&class=".$_GET["class"]."&method=Borrar&param=".$arr[$p]->id."' onclick=\"return confirm('Estï¿½ seguro que desea borrar el Dato?');\">Borrar</a></td>";
				echo "</tr>";
			}

			echo "<tr><td>&nbsp;</td></tr>";
			//PAGINACION
			if ($num_arr > $this->num_reg_pag){

				$num_pages = ceil($num_arr/$this->num_reg_pag);
				echo "<tr><td colspan='9' align='center'>";

				echo "Ir a la pï¿½gina:&nbsp;<select onchange=\"location.href='index.php?accion=listar&dato_para=".$dato_para."&id_depto_exp=".$id_depto_exp."&id_depto_rec=".$id_depto_rec."&id_mun_rec=".$id_mun_rec."&id_mun_exp=".$id_mun_exp."&class=".$_GET["class"]."&method=".$_GET["method"]."&param=".$_GET["param"]."&page='+this.value\" class='select'>";
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
	 * Imprime en pantalla los datos del Desplazamiento
	 * @access public
	 * @param object $vo Desplazamiento que se va a imprimir
	 * @param string $formato Formato en el que se listarï¿½n los Desplazamiento, puede ser Tabla o ComboSelect
	 * @param int $valor_combo ID del Desplazamiento que serï¿½ selccionado cuando el formato es ComboSelect
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
	 * Carga un VO de Desplazamiento con los datos de la consulta
	 * @access public
	 * @param object $vo VO de Desplazamiento que se va a recibir los datos
	 * @param object $Resultset Resource de la consulta
	 * @return object $vo VO de Desplazamiento con los datos
	 */
	function GetFromResult($vo,$Result){
		
		$vo->id = $Result->CONS_SUR;

		$vo->id_tipo = $Result->ID_TIPO_DESPLA;
		$vo->id_mun_rec = $Result->ID_MUN;
		$vo->id_depto_rec = $Result->ID_DEPTO;
		$vo->id_clase = $Result->ID_CLASE_DESPLA;
		$vo->id_mun_exp = $Result->ID_MUN_ID_MUN;
		$vo->id_fuente = $Result->ID_FUEDES;
		$vo->id_periodo = $Result->ID_PERIO;
		$vo->id_poblacion = $Result->ID_POBLA;
		$vo->id_contacto = $Result->ID_CONP;
		$vo->id_depto_exp = $Result->ID_DEPTO_ID_DEPTO;
		$vo->cantidad = $Result->VALOR;

		return $vo;

	}

	/**
	 * Inserta el valor de un Dato de Desplazamiento en la B.D.
	 * @access public
	 * @param object $depto_vo VO de Desplazamiento que se va a insertar
	 * @param int $dato_para A que corresponde el dato
	 */
	function Insertar($desplazamiento_vo,$dato_para){

		//DEPTO
		if ($dato_para == 1){
			$sql =  "INSERT INTO registro (ID_TIPO_DESPLA,ID_DEPTO,ID_CLASE_DESPLA,ID_FUEDES,ID_PERIO,ID_POBLA,ID_CONP,ID_DEPTO_ID_DEPTO,VALOR) VALUES
				(".$desplazamiento_vo->id_tipo.",'".$desplazamiento_vo->id_depto_rec."',".$desplazamiento_vo->id_clase.",".$desplazamiento_vo->id_fuente.",".$desplazamiento_vo->id_periodo.",".$desplazamiento_vo->id_poblacion.",".$desplazamiento_vo->id_contacto.",'".$desplazamiento_vo->id_depto_exp."',".$desplazamiento_vo->cantidad.")";
			//echo $sql;
			$this->conn->Execute($sql);
		}
		//MUN
		if ($dato_para == 2){
			$sql =  "INSERT INTO registro (ID_TIPO_DESPLA,ID_MUN,ID_CLASE_DESPLA,ID_MUN_ID_MUN,ID_FUEDES,ID_PERIO,ID_POBLA,ID_CONP,VALOR) VALUES
				(".$desplazamiento_vo->id_tipo.",'".$desplazamiento_vo->id_mun_rec."',".$desplazamiento_vo->id_clase.",'".$desplazamiento_vo->id_mun_exp."',".$desplazamiento_vo->id_fuente.",".$desplazamiento_vo->id_periodo.",".$desplazamiento_vo->id_poblacion.",".$desplazamiento_vo->id_contacto.",".$desplazamiento_vo->cantidad.")";
			//echo $sql;
			$this->conn->Execute($sql);
		}

		?>
			<script>
			alert("Registro insertado con ï¿½xito!");
		location.href = '<?=$this->url;?>';
		</script>
			<?

	}


	/**
	 * Actualiza el valor de un Dato de Desplazamiento en la B.D.
	 * @access public
	 * @param object $depto_vo VO de Desplazamiento que se va a insertar
	 * @param int $dato_para A que corresponde el dato
	 */
	function Actualizar($desplazamiento_vo,$dato_para){

		//DEPTO
		if ($dato_para == 1){

			$sql =  "UPDATE ".$this->tabla." SET
				ID_TIPO_DESPLA = ".$desplazamiento_vo->id_tipo.",
							   ID_DEPTO = '".$desplazamiento_vo->id_depto_rec."',
							   ID_CLASE_DESPLA = ".$desplazamiento_vo->id_clase.",
							   ID_FUEDES = ".$desplazamiento_vo->id_fuente.",
							   ID_PERIO = ".$desplazamiento_vo->id_periodo.",
							   ID_POBLA = ".$desplazamiento_vo->id_poblacion.",
							   ID_CONP = ".$desplazamiento_vo->id_contacto.",
							   ID_DEPTO_ID_DEPTO = '".$desplazamiento_vo->id_depto_exp."',
							   VALOR = ".$desplazamiento_vo->cantidad."

								   WHERE ".$this->columna_id." = ".$desplazamiento_vo->id;

			//echo $sql;
			$this->conn->Execute($sql);
		}
		//MUN
		if ($dato_para == 2){

			$sql =  "UPDATE ".$this->tabla." SET
				ID_TIPO_DESPLA = ".$desplazamiento_vo->id_tipo.",
							   ID_MUN = '".$desplazamiento_vo->id_mun_rec."',
							   ID_CLASE_DESPLA = ".$desplazamiento_vo->id_clase.",
							   ID_MUN_ID_MUN = '".$desplazamiento_vo->id_mun_exp."',
							   ID_FUEDES = ".$desplazamiento_vo->id_fuente.",
							   ID_PERIO = ".$desplazamiento_vo->id_periodo.",
							   ID_POBLA = ".$desplazamiento_vo->id_poblacion.",
							   ID_CONP = ".$desplazamiento_vo->id_contacto.",
							   VALOR = ".$desplazamiento_vo->cantidad."

								   WHERE ".$this->columna_id." = ".$desplazamiento_vo->id;

			//echo $sql;
			$this->conn->Execute($sql);
		}

		?>
			<script>
			alert("Registro actualizado con ï¿½xito!");
		location.href = '<?=$this->url;?>';
		</script>
			<?

	}

	/**
	 * Borra un Desplazamiento en la B.D.
	 * @access public
	 * @param int $id ID del Desplazamiento que se va a borrar de la B.D
	 */
	function Borrar($id){

		//BORRA
		$sql = "DELETE FROM ".$this->tabla." WHERE ".$this->columna_id." = ".$id;
		$this->conn->Execute($sql);

		?>
			<script>
			alert("Registro eliminado con ï¿½xito!");
		location.href = '<?=$this->url;?>';
		</script>
			<?
	}


	/**
	 * Lista los Desplazamientos en una Tabla
	 * @access public
	 */
	function Reportar(){

		set_time_limit(0);
		ini_set ( "memory_limit", "64M");

		//INICIALIZACION DE VARIABLES
		$depto_dao = New DeptoDAO();
		$municipio_dao = New MunicipioDAO();
		$clase_dao = New ClaseDesplazamientoDAO();
		$tipo_dao = New TipoDesplazamientoDAO();
		$fuente_dao = New FuenteDAO();
		$periodo_dao = New PeriodoDAO();
		$poblacion_dao = New PoblacionDAO();
		$archivo = New Archivo();
		$id_desplazamientos = array();

		$exp_rec = $_POST["exp_rec"];
		$dato_para = $_POST["dato_para"];
		$tit_dato_para = "Departamental";
		if ($dato_para == 2)	$tit_dato_para = "Municipal";

		$num_periodos_to_file = 3;

		//SE CONSTRUYE EL SQL
		$condicion = "";
		$arreglos = "";
		$arr_id = array();

		//TIPO
		if (isset($_POST["id_tipo"])){

			$id_tipo = $_POST['id_tipo'];
			$id_s = implode(",",$id_tipo);

			$condicion = "ID_TIPO_DESPLA IN ($id_s)";
		}

		//CLASE
		$num_clases = 0;
		if (isset($_POST["id_clase"])){

			$arr_id_clase = Array();

			$id_clase = $_POST['id_clase'];
			$num_clases = count($id_clase);
			$id_s = implode(",",$id_clase);

			if ($condicion != "")	$condicion .= " AND ";
			$condicion .= "ID_CLASE_DESPLA IN (".$id_s.")";
		}

		//FUENTE
		$num_fuentes = 0;
		if (isset($_POST["id_fuente"])){

			$id_fuente = $_POST['id_fuente'];
			$num_fuentes = count($id_fuente);
			$id_s = implode(",",$id_fuente);

			if ($condicion != "")	$condicion .= " AND ";
			$condicion .= "ID_FUEDES IN (".$id_s.")";

		}

		//PERIODO AÑOS
		if (isset($_POST["aaaa_periodo"])){

			$num_periodos = count($_POST["aaaa_periodo"]);
			$id_s = '';
			foreach ($_POST["aaaa_periodo"] as $p=>$aaaa){

				$cond_t = "desc_perio REGEXP 'enero $aaaa|febrero $aaaa|marzo $aaaa|abril $aaaa|mayo $aaaa|junio $aaaa|julio $aaaa|agosto $aaaa|septiembre $aaaa|octubre $aaaa|noviembre $aaaa|diciembre $aaaa'";

				$perios = $periodo_dao->GetAllArrayID($cond_t);

				if (count($perios) > 0){
					if ($p == 0){
						$id_s = implode(",",$perios);
					}
					else{
						$id_s .= ",".implode(",",$perios);
					}
				}
			}

			if ($id_s != ""){
				if ($condicion != "")	$condicion .= " AND ";
				$condicion .= "ID_PERIO IN (".$id_s.")";
			}

		}

		//PERIODO
		if (isset($_POST["id_periodo"])){

			$num_periodos = count($_POST["id_periodo"]);
			$id_periodo = $_POST['id_periodo'];
			$id_s = implode(",",$id_periodo);

			if ($condicion != "")	$condicion .= " AND ";
			$condicion .= "ID_PERIO IN (".$id_s.")";

		}

		//POBLACION
		if (isset($_POST["id_poblacion"])){

			$id_poblacion = $_POST['id_poblacion'];
			$id_s = implode(",",$id_poblacion);

			if ($condicion != "")	$condicion .= " AND ";
			$condicion .= "ID_POBLA IN (".$id_s.")";

		}

		//UBIACION GEOGRAFICA
		$nacional = 1;
		if (isset($_POST["id_depto"]) && !isset($_POST["id_muns"]) && !in_array('00',$_POST["id_depto"])){

			$id_depto = $_POST['id_depto'];
			$nacional = 0;

			$m = 0;
			foreach ($id_depto as $id){
				$id_depto_s[$m] = "'".$id."'";
				$m++;
			}
			$id_depto_s = implode(",",$id_depto_s);

			if ($condicion != "")	$condicion .= " AND ";

			if ($dato_para == 1){
				if ($exp_rec == 1)  $condicion .= "ID_DEPTO_ID_DEPTO IN (".$id_depto_s.")";
				else if ($exp_rec == 2)	$condicion .= "ID_DEPTO IN (".$id_depto_s.")";
			}
			else{

				foreach ($id_depto as $id){
					$muns = $municipio_dao->GetAllArrayID("ID_DEPTO ='$id'",'');
					$m_m = 0;
					foreach ($muns as $id){
						$m_m == 0 ? $id_muns = "'".$id."'" : $id_muns .= ",'".$id."'";
						$m_m++;
					}
				}

				if ($exp_rec == 1)	$condicion .= "ID_MUN_ID_MUN IN (".$id_muns.")";
				else if ($exp_rec == 2)	$condicion .= "ID_MUN IN (".$id_muns.")";
			}

		}

		//MUNICIPIO
		else if (isset($_POST["id_muns"])){

			$id_mun = $_POST['id_muns'];
			$nacional = 0;

			$m = 0;
			foreach ($id_mun as $id){
				$id_mun_s[$m] = "'".$id."'";
				$m++;
			}
			$id_mun_s = implode(",",$id_mun_s);

			if ($condicion != "")	$condicion .= " AND ";
			if ($exp_rec == 1)	$condicion .= "ID_MUN_ID_MUN IN (".$id_mun_s.")";
			else if ($exp_rec == 2)	$condicion .= "ID_MUN IN (".$id_mun_s.")";

		}

		$to_file = ($num_periodos >= $num_periodos_to_file || $num_fuentes > 1 || $nacional == 1) ? 1 : 0;

		//Si se selecciona mas de 3 periodos, se muestras archivos para descargar
		if ($to_file == 1){
			$file = $_SERVER['DOCUMENT_ROOT']."/sissh/admin/desplazamiento/reporte_desplazamiento.xls";
			$file_zip = $_SERVER['DOCUMENT_ROOT']."/sissh/admin/desplazamiento/reporte_desplazamiento.zip";

			$fp = $archivo->Abrir($file,"w+");

		}

		$sql = "SELECT r.* FROM registro as r JOIN periodo as p ON r.id_perio = p.cons_perio";
		if ($condicion != '')	$sql .= " WHERE $condicion";
		$sql .= " ORDER BY ";

		if ($exp_rec == 1)	$sql .= "ID_MUN_ID_MUN";
		else if ($exp_rec == 2)	$sql .= "ID_MUN";

		$sql .= ",orden";

		$rs = $this->conn->OpenRecordset("$sql");
		$num_arr = $this->conn->RowCount($rs);

		echo "<form action='index.php?m_e=desplazamiento&accion=consultar&class=DesplazamientoDAO' method='POST'>";
		echo "<table align='center' cellspacing='1' cellpadding='3' class='tabla_reportelist_outer' border=0>";
		echo "<tr><td>&nbsp;</td></tr>";
		if ($num_arr > 0){
			echo "<tr>
				<td><a href='javascript:history.back(-1)'><img src='images/back.gif' border=0>&nbsp;Regresar</a></td>";
			if ($to_file == 0){
				echo "<td align='right'>Exportar a:&nbsp;<a href=\"#\" onclick=\"document.getElementById('pdf').value = 1;reportStream('desplazamiento');return false;\"><img src='images/consulta/generar_pdf.gif' border=0  title='Exportar a PDF::Genera la actual consulta en PDF'></a>&nbsp;&nbsp;<a href=\"#\" onclick=\"document.getElementById('pdf').value = 2;reportStream('desplazamiento');return false;\"\"><img src='images/consulta/excel.gif' border=0 title='Exportar a Excel::Genera la actual consulta en EXCEL'></a></td>";
			}
		}

		echo "<tr><td align='center' class='titulo_lista' colspan=2>CONSULTA DE DATOS DE DESPLAZAMIENTO</td></tr>";
		echo "<tr><td colspan=9>Consulta realizada a nivel <b>".$tit_dato_para."</b> aplicando los siguientes filtros:</td>";
		echo "<tr><td colspan=9>";

		//TITULO DE TIPO
		if (isset($_POST["id_tipo"])){
			echo "<img src='images/flecha.gif'> Tipo de Desplazamiento: ";
			$t = 0;
			foreach($id_tipo as $id_t){
				$vo  = $tipo_dao->Get($id_t);
				if ($t == 0)	echo "<b>".$vo->nombre."</b>";
				else			echo ", <b>".$vo->nombre."</b>";
				$t++;
			}
			echo "<br>";
		}

		//TITULO DE CLASE
		if (isset($_POST["id_clase"])){
			echo "<img src='images/flecha.gif'> Clase de Desplazamiento: ";
			$t = 0;
			foreach($id_clase as $id_t){
				$vo  = $clase_dao->Get($id_t);
				if ($t == 0)	echo "<b>".$vo->nombre."</b>";
				else			echo ", <b>".$vo->nombre."</b>";
				$t++;
			}
			echo "<br>";
		}

		//TITULO DE FUENTE
		if (isset($_POST["id_fuente"])){
			echo "<img src='images/flecha.gif'> Fuente: ";
			$t = 0;
			foreach($id_fuente as $id_t){
				$vo  = $fuente_dao->Get($id_t);
				if ($t == 0)	echo "<b>".$vo->nombre."</b>";
				else			echo ", <b>".$vo->nombre."</b>";
				$t++;
			}
			echo "<br>";
		}

		//TITULO DE PERIODO
		if (isset($_POST["id_periodo"])){
			echo "<img src='images/flecha.gif'> Periodo: ";
			$t = 0;
			foreach($id_periodo as $id_t){
				$vo  = $periodo_dao->Get($id_t);
				if ($t == 0)	echo "<b>".$vo->nombre."</b>";
				else			echo ", <b>".$vo->nombre."</b>";
				$t++;
			}
			echo "<br>";
		}

		//TITULO DE POBLACION
		if (isset($_POST["id_poblacion"])){
			echo "<img src='images/flecha.gif'> Poblaci&oacute;n: ";
			$t = 0;
			foreach($id_poblacion as $id_t){
				$vo  = $poblacion_dao->Get($id_t);
				if ($t == 0)	echo "<b>".$vo->nombre."</b>";
				else			echo ", <b>".$vo->nombre."</b>";
				$t++;
			}
			echo "<br>";
		}

		//TITULO DE DEPTO
		if (isset($_POST["id_depto"])){
			echo "<img src='images/flecha.gif'> Departamento ";
			if ($dato_para == 1 && $exp_rec == 1)	echo " Expulsor: ";
			else if ($dato_para == 1 && $exp_rec == 2)	echo " Receptor: ";

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
			if ($dato_para == 2 && $exp_rec == 1)	echo " Expulsor: ";
			else if ($dato_para == 2 && $exp_rec == 2)	echo " Receptor :";

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

			if ($to_file == 0){
				echo "<tr><td colspan=2><table class='tabla_reportelist'>";
				echo "<tr class='titulo_lista'>";
				echo $fila = "";
			}
			else{

				$fila = '<STYLE TYPE="text/css"><!--.excel_celda_texto {mso-style-parent:text; mso-number-format:"\@";white-space:normal;}--></STYLE>';

				$fila .= "<table border='1'><tr>";
				echo "<tr><td colspan=2><table><tr>";
			}

			//NIVEL DEPARTAMENTAL
			if ($dato_para == 1){
				$fila .= "<td>Codigo</td>";
				$fila .= "<td>Depto. Expulsor</td>";
				$fila .= "<td>Codigo</td>";
				$fila .= "<td>Depto. Receptor</td>";
			}

			//NIVEL MUNICIPAL
			if ($dato_para == 2){
				$fila .= "<td>Codigo </td>";
				$fila .= "<td>Mpio. Expulsor</td>";
				$fila .= "<td>Codigo</td>";
				$fila .= "<td>Mpio. Receptor</td>";
			}

			$fila .= "<td width='100'>Tipo</td>
				<td width='100'>Fuente</td>
				<td width='300'>Periodo</td>
				<td width='400'>Poblaci&oacute;n</td>
				<td width='150'>Cantidad</td>
				</tr>";

			if ($to_file == 0)	echo $fila;
			else				$archivo->Escribir($fp,$fila);

			//DEPTOS
			$vos = $depto_dao->GetAllArray('');
			foreach ($vos as $vo){
				$deptos[$vo->id] = $vo->nombre;
			}

			//MPIO
			$vos = $municipio_dao->GetAllArray('');
			foreach ($vos as $vo){
				$mpios[$vo->id] = $vo->nombre;
			}
			//Agrega el nacional a mpios
			$mpios['00000'] = 'Nacional';

			//TIPOS
			$vos = $tipo_dao->GetAllArray('');
			foreach ($vos as $vo){
				$tipos[$vo->id] = $vo->nombre;
			}

			//FUENTE
			$vos = $fuente_dao->GetAllArray('');
			foreach ($vos as $vo){
				$fuentes[$vo->id] = $vo->nombre;
			}

			//POBLACION
			$vos = $poblacion_dao->GetAllArray('','','');
			foreach ($vos as $vo){
				$poblaciones[$vo->id] = $vo->nombre_es;
			}

			//PERIODO
			$vos = $periodo_dao->GetAllArray('');
			foreach ($vos as $vo){
				$periodos[$vo->id] = $vo->nombre;
			}

			$ii = 0;
			$hay = 0;
			$p = 0;
			while ($row_rs = $this->conn->FetchObject($rs)){
				$style = "";
				if (fmod($p+1,2) == 0)  $style = "fila_lista";

				if ($dato_para == 1 && isset($row_rs->ID_DEPTO_ID_DEPTO) && isset($row_rs->ID_DEPTO) && $row_rs->VALOR != ""){

					$fila = "";
					if ($to_file == 0)	echo "<tr class='".$style."'>";
					else 				$archivo->Escribir($fp,"<tr>");

					$class_excel = ($to_file == 1) ? "class=\"excel_celda_texto\"" : "";

					$fila .= "<td $class_excel>".$row_rs->ID_DEPTO_ID_DEPTO."</td>";
					$fila .= "<td>".$deptos[$row_rs->ID_DEPTO_ID_DEPTO]."</td>";

					$fila .= "<td $class_excel>".$row_rs->ID_DEPTO."</td>";
					$fila .= "<td>".$deptos[$row_rs->ID_DEPTO]."</td>";

					//TIPO
					$fila .= "<td>".$tipos[$row_rs->ID_TIPO_DESPLA]."</td>";

					//FUENTE
					$fila .= "<td>".$fuentes[$row_rs->ID_FUEDES]."</td>";

					//PERIODO
					$fila .= "<td>".$periodos[$row_rs->ID_PERIO]."</td>";

					//POBLACION
					$vo = $poblacion_dao->Get($row_rs->ID_POBLA);
					$fila .= "<td>".$poblaciones[$row_rs->ID_POBLA]."</td>";

					$fila .= "<td>".$row_rs->VALOR."</td>";

					$id_desplazamientos[$ii] = $row_rs->CONS_SUR;
					$ii++;
					$hay = 1;
				}

				if ($dato_para == 2 && !is_null($row_rs->ID_MUN_ID_MUN) && !is_null($row_rs->ID_MUN) && $row_rs->VALOR != ""){

					$fila = "";
					if ($to_file == 0)	echo "<tr class='".$style."'>";
					else 				$archivo->Escribir($fp,"<tr>");

					$class_excel = ($to_file == 1) ? "class=\"excel_celda_texto\"" : "";

					if ($row_rs->ID_MUN_ID_MUN != ""){

						$nom_mpio = (isset($mpios[$row_rs->ID_MUN_ID_MUN])) ? $mpios[$row_rs->ID_MUN_ID_MUN] : "";

						$fila .= "<td $class_excel>".$row_rs->ID_MUN_ID_MUN."</td>";
						$fila .= "<td>".$nom_mpio."</td>";
					}
					else{
						$fila .= "<td>&nbsp;</td>";
						$fila .= "<td>&nbsp;</td>";
					}

					if ($row_rs->ID_MUN != ""){
						$nom_mpio = (isset($mpios[$row_rs->ID_MUN])) ? $mpios[$row_rs->ID_MUN] : "";

						$fila .= "<td $class_excel>".$row_rs->ID_MUN."</td>";
						$fila .= "<td>".$nom_mpio."</td>";
					}
					else{
						$fila .= "<td>&nbsp;</td>";
						$fila .= "<td>&nbsp;</td>";
					}

					//TIPO
					$fila .= "<td>".$tipos[$row_rs->ID_TIPO_DESPLA]."</td>";

					//FUENTE
					$fila .= "<td>".$fuentes[$row_rs->ID_FUEDES]."</td>";

					//PERIODO
					$fila .= "<td>".$periodos[$row_rs->ID_PERIO]."</td>";

					//POBLACION
					$fila .= "<td>".$poblaciones[$row_rs->ID_POBLA]."</td>";

					$fila .= "<td>".$row_rs->VALOR."</td>";

					$id_desplazamientos[$ii] = $row_rs->CONS_SUR;
					$ii++;
					$hay = 1;
				}

				$fila .= "</tr>";
				$p++;

				if ($to_file == 0)	echo $fila;
				else{
					$archivo->Escribir($fp,$fila);
					$fila = "";
				}

			}
			if ($hay == 0){
				echo "<tr><td>&nbsp;</td></tr>";
				echo "<tr><td colspan='7' align='center'><b>*** NO HAY DATOS ***</b></td></tr>";
			}
			else{
				if ($to_file == 0){

					//VARIABLE DE SESION QUE SE USA PARA EXPORTAR A EXCEL Y PDF EN EL ARCHIVO EXPORT_DATA.PHP
					$_SESSION["id_desplazamientos"] = $id_desplazamientos;

					echo "<input type='hidden' name='id_desplazamientos' value='".implode(",",$id_desplazamientos)."'>";
					echo "<input type='hidden' id='dato_para' name='dato_para' value=".$dato_para.">";
					echo "<input type='hidden' id='pdf' name='pdf'>";
				}
				else{

					//Cierra archivo hoja de calculo
					$archivo->Cerrar($fp);

					exec("zip -j ".$_SERVER["DOCUMENT_ROOT"]."/sissh/admin/desplazamiento/reporte_desplazamiento.zip ".$_SERVER["DOCUMENT_ROOT"]."/sissh/admin/desplazamiento/reporte_desplazamiento.xls");

					$size = ceil(filesize($file) / 1000);
					$size_zip = ceil(filesize($file_zip) / 1000);

					echo "<tr><td><img src='/sissh/admin/images/excel.gif'>&nbsp;<a href='/sissh/admin/desplazamiento/reporte_desplazamiento.xls'>Descargar Hoja de C&aacute;lculo</a>&nbsp;[ Tamaño: ".$size." kB ] [ <b>$p Registros</b> ]";
					if ($p > 10)	echo "<br><br><font class='nota'>Recuerde que el máximo número de filas que puede manejar Microsoft Excel es 65536, si el reporte tiene un número de registros mayor a este es aconsejable que genere 2 reportes dividiendo los periodos</font>";
					echo "</td></tr>";
					echo "<tr><td><img src='/sissh/admin/images/zip.png'>&nbsp;<a href='/sissh/admin/desplazamiento/reporte_desplazamiento.zip'>Descargar Archivo ZIP</a>&nbsp;[ Tamaño: ".$size_zip." kB ]</td></tr>";
				}
			}

			echo "<tr><td>&nbsp;</td></tr>";
		}
		else{
			echo "<tr><td align='center'><br><b>NO SE ENCONTRARON DATOS DE DESPLAZAMIENTO</b></td></tr>";
			echo "<tr><td align='center'><br><a href='javascript:history.back(-1);'>Regresar</a></td></tr>";
			die;
		}

		echo "</table>";
		echo "</form>";
	}


	/******************************************************************************
	 * Reporte PDF - EXCEL
	 * @param Array $id_desplazamientos Id de los Desplazamientos a Reportar
	 * @param Int $formato PDF o Excel
	 * @param Int $basico 1 = Bï¿½sico - 2 = Detallado
	 * @param Int $dato_para 1 = Dato en Departamento - 2 = Dato en Municipio
	 * @param Int $stream 0 = Link a archivo fï¿½sico 1 = Opcion Download
	 * @access public
	 *******************************************************************************/
	function ReporteDesplazamiento($ids,$formato,$basico,$dato_para,$stream=0){	

		//INICIALIZACION DE VARIABLES
		$depto_dao = New DeptoDAO();
		$municipio_dao = New MunicipioDAO();
		$clase_dao = New ClaseDesplazamientoDAO();
		$tipo_dao = New TipoDesplazamientoDAO();
		$clase_dao = New ClaseDesplazamientoDAO();
		$fuente_dao = New FuenteDAO();
		$periodo_dao = New PeriodoDAO();
		$poblacion_dao = New PoblacionDAO();
		$file = New Archivo();

		$arr_id = explode(",",$ids);

		//DEPTOS
		$vos = $depto_dao->GetAllArray('');
		foreach ($vos as $vo){
			$deptos[$vo->id] = $vo->nombre;
		}

		//MPIO
		$vos = $municipio_dao->GetAllArray('');
		foreach ($vos as $vo){
			$mpios[$vo->id] = $vo->nombre;
		}

		//TIPOS
		$vos = $tipo_dao->GetAllArray('');
		foreach ($vos as $vo){
			$tipos[$vo->id] = $vo->nombre;
		}

		//FUENTE
		$vos = $fuente_dao->GetAllArray('');
		foreach ($vos as $vo){
			$fuentes[$vo->id] = $vo->nombre;
		}

		//CLASES
		$vos = $clase_dao->GetAllArray('');
		foreach ($vos as $vo){
			$clases[$vo->id] = $vo->nombre;
		}

		//POBLACION
		$vos = $poblacion_dao->GetAllArray('','','');
		foreach ($vos as $vo){
			$poblaciones[$vo->id] = $vo->nombre_es;
		}

		//PERIODO
		$vos = $periodo_dao->GetAllArray('');
		foreach ($vos as $vo){
			$periodos[$vo->id] = $vo->nombre;
		}

		if ($formato == 1){

			$pdf = new Cezpdf();
			$pdf->selectFont('admin/lib/common/PDFfonts/Helvetica.afm');

			if ($basico == 1){
				$pdf -> ezSetMargins(80,70,20,20);
			}
			else{
				$pdf -> ezSetMargins(100,70,50,50);
			}


			// Coloca el logo y el pie en todas las paginas
			$all = $pdf->openObject();
			$pdf->saveState();
			$img_att = getimagesize('images/logos/enc_reporte_semanal.jpg');
			$pdf->addPngFromFile('images/logos/enc_reporte_semanal.png',700,550,$img_att[0]/2,$img_att[1]/2);

			$pdf->addText(300,550,14,'<b>Sala de Situación Humanitaria</b>');

			$fecha = getdate();
			$fecha_hoy = $fecha["mday"]."/".$fecha["mon"]."/".$fecha["year"];

			if ($basico == 1){
				$pdf->addText(270,535,12,'Listado de Datos de Desplazamiento - '.$fecha_hoy);
			}
			if ($basico == 2){
				$pdf->setLineStyle(1);
				$pdf->line(50,535,740,535);
				$pdf->line(50,530,740,530);
			}

			$pdf->restoreState();
			$pdf->closeObject();
			$pdf->addObject($all,'all');

			$pdf->ezSetDy(-10);

			/*$c = 0;
			  $arr = Array();
			  foreach ($arr_id as $id){
			//Carga el VO
			$vo = $this->Get($id);

			//Carga el arreglo
			$arr[$c] = $vo;
			$c++;
			}

			$num_arr = count($arr);
			 */

			$sql = "SELECT * FROM registro WHERE CONS_SUR IN ($ids)";
			$rs = $this->conn->OpenRecordset("$sql");
			$num_arr = $this->conn->RowCount($rs);

			//FORMATO BASICO
			if ($basico == 1){

				//NIVEL DEPARTAMENTAL
				if ($dato_para == 1){
					$title = Array('depto_exp' => '<b>Depto. Expulsor</b>',
							'depto_rec'   => '<b>Depto. Receptor</b>',
							'tipo'   => '<b>Tipo</b>',
							'clase'   => '<b>Clase</b>',
							'fuente'   => '<b>Fuente</b>',
							'periodo'   => '<b>Periodo</b>',
							'poblacion'   => '<b>Población</b>',
							'cantidad'   => '<b>Cantidad</b>'
							);
				}

				//NIVEL MUNICIPAL
				if ($dato_para == 2){
					$title = Array('mun_exp' => '<b>Mun. Expulsor</b>',
							'mun_rec'   => '<b>Mun. Receptor</b>',
							'tipo'   => '<b>Tipo</b>',
							'clase'   => '<b>Clase</b>',
							'fuente'   => '<b>Fuente</b>',
							'periodo'   => '<b>Periodo</b>',
							'poblacion'   => '<b>Población</b>',
							'cantidad'   => '<b>Cantidad</b>'
							);
				}

				//for($p=0;$p<$num_arr;$p++){
				$p = 0;
				while ($row_rs = $this->conn->FetchObject($rs)){

					if ($dato_para == 1){
						//$depto = $depto_dao->Get($arr[$p]->id_depto_exp);
						$data[$p]['depto_exp'] = $deptos[$row_rs->ID_DEPTO_ID_DEPTO];
						//$depto = $depto_dao->Get($arr[$p]->id_depto_rec);
						$data[$p]['depto_rec'] = $deptos[$row_rs->ID_DEPTO];
					}

					else if ($dato_para == 2){
						if ($row_rs->ID_MUN_ID_MUN != "" && $row_rs->ID_MUN_ID_MUN != 0){
							//$mun = $municipio_dao->Get($row_rs->id_mun_exp);
							$data[$p]['mun_exp'] = $mpios[$row_rs->ID_MUN_ID_MUN];
						}
						else{
							$data[$p]['mun_exp'] = "";
						}

						if ($row_rs->ID_MUN != "" && $row_rs->ID_MUN != 0){
							//$mun = $municipio_dao->Get($row_rs->id_mun_rec);
							$data[$p]['mun_rec'] = $mpios[$row_rs->ID_MUN];
						}
						else{
							$data[$p]['mun_rec'] = "";
						}


					}

					//TIPO
					//$vo = $tipo_dao->Get($row_rs->id_tipo);
					$data[$p]['tipo'] = $tipos[$row_rs->ID_TIPO_DESPLA];

					//CLASE
					//$vo = $clase_dao->Get($row_rs->id_clase);
					$data[$p]['clase'] = $clases[$row_rs->ID_CLASE_DESPLA];

					//FUENTE
					//$vo = $fuente_dao->Get($row_rs->id_fuente);
					$data[$p]['fuente'] = $fuentes[$row_rs->ID_FUEDES];

					//PERIODO
					//$vo = $periodo_dao->Get($row_rs->id_periodo);
					$data[$p]['periodo'] = $periodos[$row_rs->ID_PERIO];

					//POBLACION
					//$vo = $poblacion_dao->Get($row_rs->id_poblacion);
					$data[$p]['poblacion'] = $poblaciones[$row_rs->ID_POBLA];

					$data[$p]['cantidad'] = $row_rs->VALOR;

					$p++;

				}

				$options = Array('showLines' => 2, 'shaded' => 0, 'width' => 750, 'fontSize'=>8, 'cols'=>array('nombre'=>array('width'=>250),'valor'=>array('width'=>100)));
				$pdf->ezTable($data,$title,'',$options);
			}

			//MUESTRA EN EL NAVEGADOR EL PDF
			if ($stream == 1){
				echo $pdf->ezOutput();
				//$pdf->ezStream();
			}
			else{

				//CREA UN ARCHIVO PDF PARA BAJAR
				$nom_archivo = 'consulta/csv/desplazamiento.pdf';
				$file = New Archivo();
				$fp = $file->Abrir($nom_archivo,'wb');
				$pdfcode = $pdf->ezOutput();
				$file->Escribir($fp,$pdfcode);
				$file->Cerrar($fp);

				?>
					<table align='center' cellspacing="1" cellpadding="3" border="0" width="750">
					<tr><td>&nbsp;</td></tr>
					<tr><td align='center' class='titulo_lista' colspan=2>REPORTAR DATOS DE DESPLAZAMIENTO EN FORMATO PDF</td></tr>
					<tr><td>&nbsp;</td></tr>
					<tr><td colspan=2>
					Se ha generado correctamente el archivo PDF de Datos de Desplazamiento.<br><br>
					Para salvarlo use el botón derecho del mouse y la opción Guardar destino como sobre el siguiente link: <a href='<?=$nom_archivo;?>'>Archivo PDF</a>
					</td></tr>
					</table>
					<?
			}

		}
		//EXCEL
		else if ($formato == 2){

			if ($basico == 1){
				//NIVEL DEPARTAMENTAL
				if ($dato_para == 1){
					$tit = "CODIGO,DEPTO. EXPULSOR,CODIGO,DEPTO. RECEPTOR,TIPO,CLASE,FUENTE,PERIODO,POBLACION,CANTIDAD";
					$xls = "<tr><td>".str_replace(",","</td><td>",$tit)."</td></tr>";					
				}

				//NIVEL MUNICIPAL
				if ($dato_para == 2){
					$tit = "CODIGO,MUN. EXPULSOR,CODIGO,MUN. RECEPTOR,TIPO,CLASE,FUENTE,PERIODO,POBLACION,CANTIDAD";
					$xls = "<tr><td>".str_replace(",","</td><td>",$tit)."</td></tr>";					
				}
			}

			//ENCABEZADO
			if ($stream == 0){
				$fp = $file->Abrir('consulta/csv/desplazamiento.csv','w');
				$file->Escribir($fp,$tit."\n");
			}

			$sql = "SELECT * FROM registro WHERE CONS_SUR IN ($ids)";
			$rs = $this->conn->OpenRecordset("$sql");
			$num_arr = $this->conn->RowCount($rs);

			$p = 0;
			$que = Array(",","\r\n");
			$con = Array(" "," ");
			while ($row_rs = $this->conn->FetchObject($rs)){

				if ($dato_para == 1){
					$linea = $row_rs->ID_DEPTO_ID_DEPTO;
					$linea .= ",".$deptos[$row_rs->ID_DEPTO_ID_DEPTO];

					$linea .= ",".$row_rs->ID_DEPTO;
					$linea .= ",".$deptos[$row_rs->ID_DEPTO];
				}

				else if ($dato_para == 2){

					if ($row_rs->ID_MUN_ID_MUN != "" && $row_rs->ID_MUN_ID_MUN != 0){
						$linea = $row_rs->ID_MUN_ID_MUN;

						if (isset($mpios[$row_rs->ID_MUN_ID_MUN])){
							$linea .= ",".$mpios[$row_rs->ID_MUN_ID_MUN];
						}
						else{
							$linea .= ",";
						}
					}
					else{
						$linea = ",,";
					}

					if ($row_rs->ID_MUN != "" && $row_rs->ID_MUN != 0){
						$linea .= ",".$row_rs->ID_MUN;

						if (isset($mpios[$row_rs->ID_MUN])){
							$linea .= ",".$mpios[$row_rs->ID_MUN];
						}
						else{
							$linea .= ",";
						}
					}
					else{
						$linea .= ",,";
					}

				}

				//TIPO
				//$vo = $tipo_dao->Get($row_rs->id_tipo);
				$linea .= ",".$tipos[$row_rs->ID_TIPO_DESPLA];

				//CLASE
				//$vo = $clase_dao->Get($row_rs->id_clase);
				$linea .= ",".$clases[$row_rs->ID_CLASE_DESPLA];

				//FUENTE
				//$vo = $fuente_dao->Get($row_rs->id_fuente);
				$linea .= ",".$fuentes[$row_rs->ID_FUEDES];

				//PERIODO
				//$vo = $periodo_dao->Get($row_rs->id_periodo);
				$linea .= ",".$periodos[$row_rs->ID_PERIO];

				//POBLACION
				//$vo = $poblacion_dao->Get($row_rs->id_poblacion);
				$linea .= ",".$poblaciones[$row_rs->ID_POBLA];

				$linea .= ",".$row_rs->VALOR;

				if ($stream==0)	$file->Escribir($fp,$linea."\n");
				else 			$xls .= "<tr><td class='excel_celda_texto'>".str_replace(",","</td><td class='excel_celda_texto'>",$linea)."</td></tr>";

				$p++;
			}

			if ($stream==0){
				$file->Cerrar($fp);
				?>
					<table align='center' cellspacing="1" cellpadding="3" border="0" width="750">
					<tr><td>&nbsp;</td></tr>
					<tr><td align='center' class='titulo_lista' colspan=2>REPORTAR DATOS DE DESPLAZAMIENTO EN FORMATO CSV (Excel)</td></tr>
					<tr><td>&nbsp;</td></tr>
					<tr><td colspan=2>
					Se ha generado correctamente el archivo CSV de Datos de Desplazamiento.<br><br>
					Para salvarlo use el bot&oacute;n derecho del mouse y la opci&oacute;n Guardar destino como sobre el siguiente link: <a href='consulta/csv/desplazamiento.csv'>Archivo CSV</a>
					</td></tr>
					</table>
					<?
			}
			else{
				echo '<STYLE TYPE="text/css"><!--
					.excel_celda_texto {mso-style-parent:text; mso-number-format:"\@";white-space:normal;}
				--></STYLE>
					<table border=1>'.$xls.'</table>';
			}
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
		$clase_dao = New ClaseDesplazamientoDAO();
		$tipo_dao = New TipoDesplazamientoDAO();
		$fuente_dao = New FuenteDAO();
		$periodo_dao = New PeriodoDAO();
		$poblacion_dao = New PoblacionDAO();

		$exp_rec = $_POST["exp_rec"];
		$exp = 0;
		$rec = 0;
		if (in_array(1,$exp_rec))	$exp = 1;
		if (in_array(2,$exp_rec))	$rec = 1;

		$dato_para = 1;
		if (isset($_POST["id_muns"]))	$dato_para = 2;

		$tit_dato_para = "Departamental";
		if ($dato_para == 2)	$tit_dato_para = "Municipal";

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

			if ($exp == 1 && $rec == 0){
				$sql = "SELECT CONS_SUR FROM registro WHERE ID_DEPTO_ID_DEPTO IN (".$id_depto_s.") ORDER BY ID_TIPO_DESPLA";
			}
			else if ($rec == 1 && $exp == 0){
				$sql = "SELECT CONS_SUR FROM registro WHERE ID_DEPTO IN (".$id_depto_s.") ORDER BY ID_TIPO_DESPLA";
			}
			else if ($rec == 1 && $exp == 1){
				$sql = "SELECT CONS_SUR FROM registro WHERE ID_DEPTO_ID_DEPTO IN (".$id_depto_s.") ORDER BY ID_TIPO_DESPLA";
			}

			$arr_id_u_g = Array();
			$i = 0;
			$rs = $this->conn->OpenRecordset($sql);
			while ($row_rs = $this->conn->FetchRow($rs)){
				$arr_id_u_g[$i] = $row_rs[0];
				$i++;
			}

			//SI NO EXISTE DATO DEL DEPARTAMENTO LISTA TODOS LOS MUNICIPIOS
			if ($i == 0){
				$dato_para = 2;
				$muns = $municipio_dao->GetAllArrayID("ID_DEPTO IN (".$id_depto_s.")",'');
				$m_m = 0;
				foreach ($muns as $id){
					$muns[$m_m] = "'".$id."'";
					$m_m++;
				}

				$id_muns = implode(",",$muns);

				if ($exp == 1 && $rec == 0){
					$sql = "SELECT CONS_SUR FROM registro WHERE ID_MUN_ID_MUN IN (".$id_muns.") ORDER BY ID_TIPO_DESPLA";
				}
				else if ($rec == 1 && $exp == 0){
					$sql = "SELECT CONS_SUR FROM registro WHERE ID_MUN IN (".$id_muns.") ORDER BY ID_TIPO_DESPLA";
				}

				$arr_id_u_g = Array();
				$i = 0;
				$rs = $this->conn->OpenRecordset($sql);
				while ($row_rs = $this->conn->FetchRow($rs)){
					$arr_id_u_g[$i] = $row_rs[0];
					$i++;
				}

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

			if ($exp == 1 && $rec == 0){
				$sql = "SELECT CONS_SUR FROM registro WHERE ID_MUN_ID_MUN IN (".$id_muns.") ORDER BY ID_TIPO_DESPLA";
			}
			else if ($rec == 1 && $exp == 0){
				$sql = "SELECT CONS_SUR FROM registro WHERE ID_MUN IN (".$id_muns.") ORDER BY ID_TIPO_DESPLA";
			}

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
			echo "<tr><td colspan='8' align='right'>Exportar a: <input type='image' src='images/consulta/generar_pdf.gif' onclick=\"document.getElementById('pdf_desplazamiento').value = 1;\">&nbsp;&nbsp;<input type='image' src='images/consulta/excel.gif' onclick=\"document.getElementById('pdf_desplazamiento').value = 2;\"></td>";
		}
		echo "<tr><td align='center' class='titulo_lista' colspan=8>CONSULTA DE DATOS DE DESPLAZAMIENTO EN: ";


		//TITULO DE DEPTO
		if (isset($_POST["id_depto"])){
			//if ($dato_para == 1 && $exp_rec == 1)	echo " Expulsor: ";
			//else if ($dato_para == 1 && $exp_rec == 2)	echo " Receptor: ";

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
			//if ($dato_para == 2 && $exp_rec == 1)	echo " Expulsor: ";
			//else if ($dato_para == 2 && $exp_rec == 2)	echo " Receptor :";

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

		if ($num_arr > 0){

			echo "<tr class='titulo_lista'>";
			//NIVEL DEPARTAMENTAL
			if ($dato_para == 1){
				echo "<td>Depto. Expulsor</td>";
				echo "<td>Depto. Receptor</td>";
			}

			//NIVEL MUNICIPAL
			if ($dato_para == 2){
				echo "<td>Mpio. Expulsor</td>";
				echo "<td>Mpio. Receptor</td>";
			}

			echo "<td width='100'>Tipo</td>
				<td width='100'>Clase</td>
				<td width='100'>Fuente</td>
				<td width='300'>Periodo</td>
				<td width='400'>Poblaciï¿½n</td>
				<td width='150'>Cantidad</td>
				</tr>";

			$ii = 0;
			for($p=0;$p<$num_arr;$p++){
				$style = "";
				if (fmod($p+1,2) == 0)  $style = "fila_lista";

				if ($dato_para == 1 && isset($arr[$p]->id_depto_exp) && isset($arr[$p]->id_depto_rec)){

					echo "<tr class='".$style."'>";

					$depto = $depto_dao->Get($arr[$p]->id_depto_exp);
					echo "<td>".$depto->nombre."</td>";

					$depto = $depto_dao->Get($arr[$p]->id_depto_rec);
					echo "<td>".$depto->nombre."</td>";

					//TIPO
					$vo = $tipo_dao->Get($arr[$p]->id_tipo);
					echo "<td>".$vo->nombre."</td>";

					//CLASE
					$vo = $clase_dao->Get($arr[$p]->id_clase);
					echo "<td>".$vo->nombre."</td>";

					//FUENTE
					$vo = $fuente_dao->Get($arr[$p]->id_fuente);
					echo "<td>".$vo->nombre."</td>";

					//PERIODO
					$vo = $periodo_dao->Get($arr[$p]->id_periodo);
					echo "<td>".$vo->nombre."</td>";

					//POBLACION
					$vo = $poblacion_dao->Get($arr[$p]->id_poblacion);
					echo "<td>".$vo->nombre_es."</td>";

					echo "<td>".$arr[$p]->cantidad."</td>";

					$id_desplazamientos[$ii] = $arr[$p]->id;
					$ii++;
				}
				if ($dato_para == 2){

					echo "<tr class='".$style."'>";

					if ($arr[$p]->id_mun_exp != "" && $arr[$p]->id_mun_exp != 0){
						$vo = $municipio_dao->Get($arr[$p]->id_mun_exp);
						echo "<td>".$vo->nombre."</td>";
					}
					else{
						echo "<td>&nbsp;</td>";
					}

					if ($arr[$p]->id_mun_rec != "" && $arr[$p]->id_mun_rec != 0){
						$vo = $municipio_dao->Get($arr[$p]->id_mun_rec);
						echo "<td>".$vo->nombre."</td>";
					}
					else{
						echo "<td>&nbsp;</td>";
					}

					//TIPO
					$vo = $tipo_dao->Get($arr[$p]->id_tipo);
					echo "<td>".$vo->nombre."</td>";

					//CLASE
					$vo = $clase_dao->Get($arr[$p]->id_clase);
					echo "<td>".$vo->nombre."</td>";

					//FUENTE
					$vo = $fuente_dao->Get($arr[$p]->id_fuente);
					echo "<td>".$vo->nombre."</td>";

					//PERIODO
					$vo = $periodo_dao->Get($arr[$p]->id_periodo);
					echo "<td>".$vo->nombre."</td>";

					//POBLACION
					$vo = $poblacion_dao->Get($arr[$p]->id_poblacion);
					echo "<td>".$vo->nombre_es."</td>";

					echo "<td>".$arr[$p]->cantidad."</td>";

					$id_desplazamientos[$ii] = $arr[$p]->id;
					$ii++;
				}
			}
			echo "<tr><td>&nbsp;</td></tr>";
		}
		else{
			echo "<tr><td align='center'><br><b>NO SE ENCONTRARON DATOS DE DESPLAZAMIENTO</b></td></tr>";
		}

		echo "<input type='hidden' name='id_desplazamientos' value='".implode(",",$arr_id)."'>";
		echo "<input type='hidden' id='que_desplazamiento' name='que_desplazamiento' value='1'>";
		echo "<input type='hidden' id='dato_para' name='dato_para' value=".$dato_para.">";
		echo "</table>";
	}

	/**
	 * Obtiene el valor de desplazamiento para mapas
	 * @access public
	 * @param  string $id_ubicacion
	 * @param  int $dato_para
	 * @param  array $id_tipo
	 * @param  int $id_clase
	 * @param  int $id_fuente
	 * @param  string $id_periodo id de los periodos separados por coma
	 * @param  array $id_tipo_periodo
	 * @param  int $valor
	 */
	function getValorToMapa($id_ubicacion,$dato_para,$id_tipo,$id_clase,$id_fuente,$id_periodo,$id_tipo_periodo){

		//INICIALIZACION DE VARIABLES
		$depto_dao = New DeptoDAO();
		$municipio_dao = New MunicipioDAO();
		$clase_dao = New ClaseDesplazamientoDAO();
		$tipo_dao = New TipoDesplazamientoDAO();
		$fuente_dao = New FuenteDAO();
		$periodo_dao = New PeriodoDAO();

		$exp = ($id_clase == 1) ? 1 : 0;

		if ($id_tipo_periodo == 'aaaa'){
			$valor = 0;

			$per_tmp = explode(",",$id_periodo);
			foreach ($per_tmp as $aaaa){
				$valor += $this->GetValorToReportTotalAAAA($exp,$id_fuente,$id_tipo,$aaaa,$id_ubicacion,$dato_para);
				//echo "$id_ubicacion = $valor<br>";
			}
		}
		else{
			//SE CONSTRUYE EL SQL
			$condicion = "ID_TIPO_DESPLA IN ($id_tipo)";

			$id_clase = $id_clase;
			if ($condicion != "")	$condicion .= " AND ";

			$condicion .= "ID_CLASE_DESPLA IN (".$id_clase.")";

			$id_fuente = $id_fuente;

			if ($condicion != "")	$condicion .= " AND ";

			$condicion .= " id_fuedes IN (".$id_fuente.")";

			$id_s = $_GET['id_periodo'];

			if ($condicion != "")	$condicion .= " AND ";
			$condicion .= "ID_PERIO IN (".$id_s.")";

			if ($dato_para == 1){

				if ($exp == 1){
					$sql = "SELECT VALOR FROM registro WHERE ID_DEPTO_ID_DEPTO = '$id_ubicacion' AND $condicion";
				}
				else{
					$sql = "SELECT VALOR FROM registro WHERE ID_DEPTO = '$id_ubicacion' AND $condicion";
				}

				$rs = $this->conn->OpenRecordset($sql);
				$row_rs = $this->conn->Fetchrow($rs);
				$valor = $row_rs[0];

				if ($valor == 0){
					$muns = $mun_dao->GetAllArrayID("ID_DEPTO ='$id_ubicacion'",'');
					$m_m = 0;
					foreach ($muns as $id){
						$m_m == 0 ? $id_muns = "'".$id."'" : $id_muns .= ",'".$id."'";
						$m_m++;
					}

					if ($exp == 1){
						$sql = "SELECT sum(VALOR) FROM registro WHERE ID_MUN_ID_MUN IN (".$id_muns.") AND $condicion";
					}
					else{
						$sql = "SELECT sum(VALOR) FROM registro WHERE ID_MUN IN (".$id_muns.") AND $condicion";
					}

					$rs = $this->conn->OpenRecordset($sql);
					$row_rs = $this->conn->FetchRow($rs);
					$valor = $row_rs[0];
				}

			}
			else if ($dato_para == 2){

				if ($exp == 1){
					$sql = "SELECT sum(VALOR) FROM registro WHERE ID_MUN_ID_MUN = '$id_ubicacion' AND $condicion";
				}
				else{
					$sql = "SELECT sum(VALOR) FROM registro WHERE ID_MUN = '$id_ubicacion' AND $condicion";
				}

				$rs = $this->conn->OpenRecordset($sql);
				$row_rs = $this->conn->Fetchrow($rs);
				$valor = $row_rs[0];
			}
		}

		//echo $sql;

		/*if (is_null($valor)){
		  $valor = 0;
		  }*/
		/*else if ($valor == ''){
		  die("$id_ubicacion=$valor $id_tipo_periodo vacio en $sql");
		  }*/

		return $valor;

	}

	/**
	 * Importa los registros de datos de Desplazamiento
	 * @access public
	 * @param file $userfile Archivo CSV a importar
	 * @param int $id_clase
	 * @param int $id_tipo
	 * @param int $id_fuente
	 * @param string $accion
	 * @param int $id_periodo_s
	 * @param string $f_corte
	 */
	function ImportarCSV($userfile,$id_clase,$id_tipo,$id_fuente,$accion,$id_periodo_s,$f_corte){

		$db = 'ocha_sissh_despla_import';

		$archivo = New Archivo();
		$mun_dao = New MunicipioDAO();
		$depto_dao = New DeptoDAO();
		$tipo_dao = New TipoDesplazamientoDAO();
		$clase_dao = New ClaseDesplazamientoDAO();
		$periodo_dao = New PeriodoDAO();
		$desplazamiento = New Desplazamiento();

		$sissh = New SisshDAO();

		//Borra el cache de perfiles, para reflejar los nuevos valores
		$sissh->borrarCacheImportarDesplazamiento();
		$sissh->borrarCacheMapa('desplazamiento',$id_fuente);

		$id_periodo = explode("|",$id_periodo_s);

		//TIPO
		$desplazamiento->id_tipo = $id_tipo;

		//CLASE
		$desplazamiento->id_clase = $id_clase;

		//FUENTE
		$desplazamiento->id_fuente = $id_fuente;

		//FECHA DE CORTE
		$desplazamiento->f_corte = $f_corte;

		$file_tmp = $userfile['tmp_name'];
		$file_nombre = $userfile['name'];

		$path = "desplazamiento/csv/".$file_nombre;

		$archivo->SetPath($path);
		$archivo->Guardar($file_tmp);

		$fp = $archivo->Abrir($path,'r');
		$linea_x = $archivo->LeerLineaX($fp,1);
		$linea_x_split = explode(",",$linea_x);
		$num_cols_file = count($linea_x_split);

		//CHECK DE NUMERO DE COLUMNAS SELECCIONADAS Y LAS DEL ARCHIVO
		if ($num_cols_file - 1 != count($id_periodo)){
			?>
			<script>
				alert('El número de periodos seleccionados (<?=count($id_periodo)?>) no concuerda con el número de columnas en el archivo(<?=$num_cols_file - 1?>)');
				location.href='index.php?m_e=desplazamiento&accion=importar&id_periodos=<?=$id_periodo_s?>';
			</script>
			<?

			die;
		}

		//COLOCA EL PUNTERO AL COMIENZO DEL ARCHIVO
		fseek($fp,0);
		$cont_archivo = $archivo->LeerEnArreglo($fp);
		$archivo->Cerrar($fp);
		$num_rep = count($cont_archivo);
        
        // copia toda la tabla de periodos
        $sql = "TRUNCATE ocha_sissh_despla_import.periodo";
        $this->conn->Execute($sql);
        
        $sql = "INSERT INTO ocha_sissh_despla_import.periodo SELECT * FROM ocha_sissh.periodo";
        $this->conn->Execute($sql);

		//ACCION DE BORRAR
		if ($accion == "borrar"){
			$sql = "DELETE FROM $db.registro WHERE ID_CLASE_DESPLA = $id_clase AND ID_TIPO_DESPLA = $id_tipo AND ID_FUEDES = $id_fuente";
			//echo $sql;
			$this->conn->Execute($sql);
		}
        
        // Actualiza fecha de corte en fuente
        $sql = "UPDATE ocha_sissh.fuente_desplazamiento SET FECHA_CORTE = '$f_corte' WHERE ID_FUEDES = $id_fuente";
		$this->conn->Execute($sql);

		$ex = 0;
		//COMIENZA DESDE LA SEGUNDA LINEA
		for($r=1;$r<$num_rep;$r++){
			$linea = $cont_archivo[$r];

			$linea = explode(",",$linea);

			if (count($linea > 0) && $linea[0] != ""){

				//RECEPCION O EXPULSION ?
				if ($desplazamiento->id_clase == 1){    //EXPULSION
					//MPIOS
					$desplazamiento->id_mun_exp =  $linea[0];
					$desplazamiento->id_mun_rec =  "";
				}
				else if ($desplazamiento->id_clase > 1){    //RECEPCION
					//MPIOS
					$desplazamiento->id_mun_rec =  $linea[0];
					$desplazamiento->id_mun_exp =  "";
				}

				//POBLACION
				$desplazamiento->id_poblacion = 43;   //Desplazados

				//CONTACTO
				$desplazamiento->id_contacto = 0;

				//CANIDADES
				$c = 1;   //COLUMNA DESDE COMIENZAN LOS VALORES
				foreach ($id_periodo as $id_p){
					//PERIODO
					$desplazamiento->id_periodo = $id_p;

					//CANTIDAD
					$cant = ereg_replace("[^0-9]", "", $linea[$c]); 
					//var_dump($cant);
					if ($cant != ""){
						$desplazamiento->cantidad = $cant;

						//INSERTA EL REGISTRO
						$sql =  "INSERT INTO $db.registro (ID_TIPO_DESPLA,ID_MUN,ID_CLASE_DESPLA,ID_MUN_ID_MUN,ID_FUEDES,ID_PERIO,ID_POBLA,ID_CONP,VALOR,FECHA_CORTE) VALUES
							(".$desplazamiento->id_tipo.",'".$desplazamiento->id_mun_rec."',".$desplazamiento->id_clase.",'".$desplazamiento->id_mun_exp."',".$desplazamiento->id_fuente.",".$desplazamiento->id_periodo.",".$desplazamiento->id_poblacion.",".$desplazamiento->id_contacto.",".$desplazamiento->cantidad.",'".$desplazamiento->f_corte."')";
						
						//die($sql);
						
						$this->conn->Execute($sql);
					}

					$c++;
				}
			}
			$ex++;
		}
		echo "<script>";
		echo "alert('Se cargaron : ".$ex." Registros.');";
		//echo "location.href='index.php?m_e=desplazamiento&accion=importar';";
		echo "</script>";
	}

	/**
	 * Importa los registros de datos de Desplazamiento exclusivamente para el archivo de accion social
	 * @access public
	 * @param file $userfile Archivo CSV a importar
	 * @param int $id_clase
	 * @param int $id_tipo
	 * @param int $id_fuente
	 * @param string $accion
	 * @param string $f_corte
	 */
	function ImportarAccionSocial($userfile,$id_clase,$id_tipo,$id_fuente,$accion,$f_corte){

		set_time_limit(0);

		$simbolo_csv = ",";

		$archivo = New Archivo();
		$mun_dao = New MunicipioDAO();
		$depto_dao = New DeptoDAO();
		$tipo_dao = New TipoDesplazamientoDAO();
		$clase_dao = New ClaseDesplazamientoDAO();
		$periodo_dao = New PeriodoDAO();
		$desplazamiento = new Desplazamiento;

		$sissh = New SisshDAO();

		//Borra el cache de perfiles, para reflejar los nuevos valores
		$sissh->borrarCacheImportarDesplazamiento();
		$sissh->borrarCacheMapa('desplazamiento',$id_fuente);

		$meses = Array("", "Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio", "Julio", "Agosto", "Septiembre", "Octubre", "Noviembre", "Diciembre");
		$trimestres = Array("ITRIM","IITRIM","IIITRIM","IVTRIM");
		$semestres = Array("I Semestre","II Semestre");

		//TIPO
		$desplazamiento->id_tipo = $id_tipo;

		//CLASE
		$desplazamiento->id_clase = $id_clase;

		//FUENTE
		$desplazamiento->id_fuente = $id_fuente;

		//FECHA DE CORTE
		$desplazamiento->f_corte = $f_corte;

		$file_tmp = $userfile['tmp_name'];
		$file_nombre = $userfile['name'];

		$path = "../admin/desplazamiento/csv/".$file_nombre;

		$archivo->SetPath($path);
		$archivo->Guardar($file_tmp);

		$fp = $archivo->Abrir($path,'r');
		$cont_archivo = $archivo->LeerEnArreglo($fp);
		$archivo->Cerrar($fp);
		$num_rep = count($cont_archivo);

		$fila_aaaa = explode("$simbolo_csv",$cont_archivo[0]);

		$anios = array_unique($fila_aaaa);

		//ELIMINA LAS 2 PRIMERAS COLUMNAS, LA VACIA Y EL 1899
		array_shift($anios);
		array_shift($anios);

		//ELIMINA EL ULTIMO AÑO, QUE ESTA REPETIDO Y CON UN CARACTER RARO AL FINAL
		array_pop($anios);

		$fila_mes = explode("$simbolo_csv",$cont_archivo[1]);

		$linea_tmp = $cont_archivo[2];
		$linea_tmp = explode("$simbolo_csv",$linea_tmp);

		$num_cols_file = count($linea_tmp);

		//CONSTRUYE EL ARREGLO DE PERIODOS
		$id_periodo = array();

		for($c=1;$c<$num_cols_file;$c++){
			$aaaa = $fila_aaaa[$c];

			if ($aaaa == 1899){
				array_push($id_periodo,66);
			}
			else{
				$num_mes = trim($fila_mes[$c]);
				if ($num_mes > 0){
					$mes = $meses[$num_mes];
					$periodo = $mes." ".$aaaa;
					$id_p = $periodo_dao->GetIDbyNombre(trim($periodo));
					array_push($id_periodo,$id_p);
				}
			}

			//echo "Periodo: $periodo - ID:$id_p - Mes numero: ".$fila_mes[$c]."---- <br>";		

		}
		//		print_r($id_periodo);
		//		die(print_r($id_periodo));

		//ACCION DE BORRAR
		if ($accion == "borrar"){
			$sql = "DELETE FROM registro WHERE ID_CLASE_DESPLA = $id_clase AND ID_TIPO_DESPLA = $id_tipo AND ID_FUEDES = $id_fuente";
			//die($sql);
			$this->conn->Execute($sql,0);
		}

        // Actualiza fecha de corte en fuente
        $sql = "UPDATE ocha_sissh.fuente_desplazamiento SET FECHA_CORTE = '$f_corte' WHERE ID_FUEDES = $id_fuente";
		$this->conn->Execute($sql);
        
        $ex = 0;
		$aaaa_ant = 0;
		$a = 0;
		$exp = 0;
		//COMIENZA DESDE LA TERCERA LINEA
		for($r=2;$r<$num_rep;$r++){
			$linea = $cont_archivo[$r];

            echo "Procesando $file_nombre, fila # $r";

			$linea = explode("$simbolo_csv",$linea);

			if (count($linea > 0) && $linea[0] != ""){

				$id_mun = $linea[0];

                    $periodos[1899] = 66;
                }
                else{
				//Check del municipio a importar
				if (count($mun_dao->GetAllArrayID("id_mun = $id_mun","")) == 0){
					echo "*************Municipio no existe en sidih : $id_mun<br>";
				}

				//RECEPCION O EXPULSION ?
				if ($desplazamiento->id_clase == 1){    //EXPULSION
					$exp = 1;
					//MPIOS
					$desplazamiento->id_mun_exp =  $id_mun;
					$desplazamiento->id_mun_rec =  "";
				}
				else if ($desplazamiento->id_clase > 1){    //RECEPCION
					//MPIOS
					$desplazamiento->id_mun_rec =  $id_mun;
					$desplazamiento->id_mun_exp =  "";
				}

				//POBLACION
				$desplazamiento->id_poblacion = 43;   //Desplazados

				//CONTACTO
				$desplazamiento->id_contacto = 0;
                
                //CANIDADES
				$c = 1;   //COLUMNA DESDE COMIENZAN LOS VALORES
				foreach ($id_periodo as $id_p){
					$desplazamiento->id_periodo = $id_p;

					//CANTIDAD
					if (strlen(trim($linea[$c])) > 0){
						$cant = trim($linea[$c]);
						$desplazamiento->cantidad = $cant;

						//INSERTA EL REGISTRO
						$sql =  "INSERT INTO registro (ID_TIPO_DESPLA,ID_MUN,ID_CLASE_DESPLA,ID_MUN_ID_MUN,ID_FUEDES,ID_PERIO,ID_POBLA,ID_CONP,VALOR,FECHA_CORTE) VALUES
							(".$desplazamiento->id_tipo.",'".$desplazamiento->id_mun_rec."',".$desplazamiento->id_clase.",'".$desplazamiento->id_mun_exp."',".$desplazamiento->id_fuente.",".$desplazamiento->id_periodo.",".$desplazamiento->id_poblacion.",".$desplazamiento->id_contacto.",".$desplazamiento->cantidad.",'".$desplazamiento->f_corte."')";

						$periodo = $periodo_dao->Get($id_p);

						//echo "Inserta $id_mun $id_p= $periodo->nombre : $cant <br>";
						$this->conn->Execute($sql,0);
					}
					$c++;
				}

				//REALIZA LAS SUMATORIAS DE TRIM Y SEM
				foreach($anios as $aaaa){
					$valor_trim = 0;
					$valor_sem = 0;
					$trim = 0;
					$sem = 0;
					for($me=1;$me<13;$me++){

						$mes = $meses[$me];
						$periodo = $mes." ".$aaaa;
						//echo $periodo;
						$desplazamiento->id_periodo = $periodo_dao->GetIDbyNombre($periodo);

						$valor_mes = $this->GetValorToReport($exp,$id_fuente,$id_tipo,$desplazamiento->id_periodo,$id_mun,2);
						//if ($id_mun == '19075') echo "Valor mes $id_mun $periodo $desplazamiento->id_periodo en trim y sem para Exp=$exp = $valor_mes<br>";
						$valor_trim += $valor_mes;
						$valor_sem += $valor_mes; 

						if (fmod($me,3) == 0){
							if ($valor_trim > 0){
								$periodo = $trimestres[$trim]." ".$aaaa;
								$id_p = $periodo_dao->GetIDbyNombre($periodo);

								$desplazamiento->id_periodo = $id_p;

								if ($id_p > 0){

									$desplazamiento->cantidad = $valor_trim;

									//INSERTA EL REGISTRO
									$sql =  "INSERT INTO registro (ID_TIPO_DESPLA,ID_MUN,ID_CLASE_DESPLA,ID_MUN_ID_MUN,ID_FUEDES,ID_PERIO,ID_POBLA,ID_CONP,VALOR,FECHA_CORTE) VALUES
										(".$desplazamiento->id_tipo.",'".$desplazamiento->id_mun_rec."',".$desplazamiento->id_clase.",'".$desplazamiento->id_mun_exp."',".$desplazamiento->id_fuente.",".$desplazamiento->id_periodo.",".$desplazamiento->id_poblacion.",".$desplazamiento->id_contacto.",".$desplazamiento->cantidad.",'".$desplazamiento->f_corte."')";
									
									//echo "Inserta trim: $sql Periodo: $periodo <br>";
									//die;

									$this->conn->Execute($sql,0);
									
								}
							}

							if (fmod($me,6) == 0){
								if ($valor_sem > 0){
									$periodo = $semestres[$sem]." ".$aaaa;
									$id_p = $periodo_dao->GetIDbyNombre($periodo);

									$desplazamiento->id_periodo = $id_p;

									if ($id_p > 0){
										$desplazamiento->cantidad = $valor_sem;

										//INSERTA EL REGISTRO
										$sql =  "INSERT INTO registro (ID_TIPO_DESPLA,ID_MUN,ID_CLASE_DESPLA,ID_MUN_ID_MUN,ID_FUEDES,ID_PERIO,ID_POBLA,ID_CONP,VALOR,FECHA_CORTE) VALUES
											(".$desplazamiento->id_tipo.",'".$desplazamiento->id_mun_rec."',".$desplazamiento->id_clase.",'".$desplazamiento->id_mun_exp."',".$desplazamiento->id_fuente.",".$desplazamiento->id_periodo.",".$desplazamiento->id_poblacion.",".$desplazamiento->id_contacto.",".$desplazamiento->cantidad.",'".$desplazamiento->f_corte."')";
										//										echo "Inserta sem: $sql Periodo: $periodo<br>";
										$this->conn->Execute($sql,0);
									}
								}

								$valor_sem = 0;
								$sem++;

							}

							$valor_trim = 0;
							$trim++;
						}
					}

					//if ($aaaa == 1997)	die;
					//if ($id_mun == '05001')	die;
				}				
			}

			//echo "Listo para: $id_mun <br>";

			//			if ($r == 4)	die;

			$ex++;
		}	



		echo "<script>";
		echo "alert('Se cargaron : ".$ex." Registros.');";
		echo "</script>";

        return 1;  // Variable usada para cargar el siguiente archivo cuando termina la carga de 1 archivo, sino, error
	}
    
    /**
     * 2015 - Importa los registros de datos de Desplazamiento exclusivamente para el archivo procesado de UARIV - 2015
     * @access public
     * @param file $userfile Archivo CSV a importar
     * @param int $id_clase
     * @param int $id_tipo
     * @param int $id_fuente
     * @param string $accion
     * @param string $f_corte
     */
    function ImportarUARIV($userfile,$id_clase,$id_tipo,$id_fuente,$accion,$f_corte){

        set_time_limit(0);

        $simbolo_csv = ",";

        $archivo = New Archivo();
        $mun_dao = New MunicipioDAO();
        $depto_dao = New DeptoDAO();
        $tipo_dao = New TipoDesplazamientoDAO();
        $clase_dao = New ClaseDesplazamientoDAO();
        $periodo_dao = New PeriodoDAO();
        $desplazamiento = new Desplazamiento;

        $sissh = New SisshDAO();

        //Borra el cache de perfiles, para reflejar los nuevos valores
        $sissh->borrarCacheImportarDesplazamiento();
        $sissh->borrarCacheMapa('desplazamiento',$id_fuente);

        $meses = Array("", "Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio", "Julio", "Agosto", "Septiembre", "Octubre", "Noviembre", "Diciembre");
        $trimestres = Array("ITRIM","IITRIM","IIITRIM","IVTRIM");
        $semestres = Array("I Semestre","II Semestre");

        //TIPO
        $desplazamiento->id_tipo = $id_tipo;

        //CLASE
        $desplazamiento->id_clase = $id_clase;

        //FUENTE
        $desplazamiento->id_fuente = $id_fuente;

        //FECHA DE CORTE
        $desplazamiento->f_corte = $f_corte;

        $file_tmp = $userfile['tmp_name'];
        $file_nombre = $userfile['name'];

        $path = "../admin/desplazamiento/csv/".$file_nombre;

        $archivo->SetPath($path);
        $archivo->Guardar($file_tmp);

        $fp = $archivo->Abrir($path,'r');
        $cont_archivo = $archivo->LeerEnArreglo($fp);
        $archivo->Cerrar($fp);
        $num_rep = count($cont_archivo);

        $periodos = array();
        $anios = array();

        //ACCION DE BORRAR
        if ($accion == "borrar"){
            $sql = "DELETE FROM registro WHERE ID_CLASE_DESPLA = $id_clase AND ID_TIPO_DESPLA = $id_tipo AND ID_FUEDES = $id_fuente";
            //die($sql);
            $this->conn->Execute($sql,0);
        }

        // Actualiza fecha de corte en fuente
        $sql = "UPDATE ocha_sissh.fuente_desplazamiento SET FECHA_CORTE = '$f_corte' WHERE ID_FUEDES = $id_fuente";
        $this->conn->Execute($sql);
        
        $ex = 0;
        $aaaa_ant = 0;
        $a = 0;
        $exp = 0;
        
        //COMIENZA DESDE LA SEGUNDA LINEA
        for($r=1;$r<$num_rep;$r++){

            //echo "Procesando $file_nombre, fila # $r";

            $linea = explode("$simbolo_csv",$cont_archivo[$r]);

            if (count($linea > 0) && $linea[0] != ""){

                $id_mun = $linea[0];

                //Check del municipio a importar
                if (count($mun_dao->GetAllArrayID("id_mun = '$id_mun'","")) == 0){
                    echo "*************Municipio no existe en sidih : $id_mun<br>";
                }

                //RECEPCION O EXPULSION ?
                if ($desplazamiento->id_clase == 1){    //EXPULSION
                    $exp = 1;
                    //MPIOS
                    $desplazamiento->id_mun_exp =  $id_mun;
                    $desplazamiento->id_mun_rec =  "";
                }
                else if ($desplazamiento->id_clase > 1){    //RECEPCION
                    //MPIOS
                    $desplazamiento->id_mun_rec =  $id_mun;
                    $desplazamiento->id_mun_exp =  "";
                }

                //POBLACION
                $desplazamiento->id_poblacion = 43;   //Desplazados

                //CONTACTO
                $desplazamiento->id_contacto = 0;

                $aaaa = $linea[1];
                $mes_num = $linea[2];
                $cant = $linea[3];
                $personas = $linea[4];

                if ($aaaa < 1985){
                    $desplazamiento->id_periodo = 66;
                }
                else{
                    if (!in_array($aaaa, $anios)) {
                        $anios[] = $aaaa;
                    }
                    
                    if ($mes_num > 0){
                        $mes = $meses[$mes_num];
                        $periodo = $mes." ".$aaaa;

                        if (!isset($periodos[$periodo])) {
                            $id_p = $periodo_dao->GetIDbyNombre(trim($periodo));
                            if (!empty($id_p)) {
                                $periodos[$periodo] = $id_p;
                            }
                        }
                        
                        $desplazamiento->id_periodo = $periodos[$periodo];
                    }
                }

                if (isset($desplazamiento->id_periodo) && !empty($cant)) {

                    $desplazamiento->cantidad = $cant;
                    $desplazamiento->personas = $personas;

                    //INSERTA EL REGISTRO
                    $sql =  "INSERT INTO registro 
                            (ID_TIPO_DESPLA,ID_MUN,ID_CLASE_DESPLA,ID_MUN_ID_MUN,ID_FUEDES,ID_PERIO,ID_POBLA,ID_CONP,VALOR,FECHA_CORTE,PERSONAS) VALUES
                            (".$desplazamiento->id_tipo.",'".$desplazamiento->id_mun_rec."',".$desplazamiento->id_clase.",'".$desplazamiento->id_mun_exp."',".
                            $desplazamiento->id_fuente.",".$desplazamiento->id_periodo.",".$desplazamiento->id_poblacion.",".$desplazamiento->id_contacto.",".
                            $desplazamiento->cantidad.",'".$desplazamiento->f_corte."',".$desplazamiento->personas.")";

                    //echo "Inserta $id_mun $id_p= $periodo->nombre : $cant <br>";
                    $this->conn->Execute($sql,0);
                    
                    $ex++;
                }
            }
        }

        //REALIZA LAS SUMATORIAS DE TRIM Y SEM
        foreach($anios as $aaaa){
            $valor_trim = 0;
            $valor_sem = 0;
            $trim = 0;
            $sem = 0;
            for($me=1;$me<13;$me++){

                $mes = $meses[$me];
                $periodo = $mes." ".$aaaa;
                //echo $periodo;
                $desplazamiento->id_periodo = $periodo_dao->GetIDbyNombre($periodo);

                $valor_mes = $this->GetValorToReport($exp,$id_fuente,$id_tipo,$desplazamiento->id_periodo,$id_mun,2);
                $valor_trim += $valor_mes;
                $valor_sem += $valor_mes; 

                if (fmod($me,3) == 0){
                    if ($valor_trim > 0){
                        $periodo = $trimestres[$trim]." ".$aaaa;
                        $id_p = $periodo_dao->GetIDbyNombre($periodo);

                        $desplazamiento->id_periodo = $id_p;

                        if ($id_p > 0){

                            $desplazamiento->cantidad = $valor_trim;

                            //INSERTA EL REGISTRO
                            $sql =  "INSERT INTO registro (ID_TIPO_DESPLA,ID_MUN,ID_CLASE_DESPLA,ID_MUN_ID_MUN,ID_FUEDES,ID_PERIO,ID_POBLA,ID_CONP,VALOR,FECHA_CORTE) VALUES
                                (".$desplazamiento->id_tipo.",'".$desplazamiento->id_mun_rec."',".$desplazamiento->id_clase.",'".$desplazamiento->id_mun_exp."',".$desplazamiento->id_fuente.",".$desplazamiento->id_periodo.",".$desplazamiento->id_poblacion.",".$desplazamiento->id_contacto.",".$desplazamiento->cantidad.",'".$desplazamiento->f_corte."')";
                            
                            //echo "Inserta trim: $sql Periodo: $periodo <br>";
                            //die;

                            $this->conn->Execute($sql,0);
                            
                        }
                    }

                    if (fmod($me,6) == 0){
                        if ($valor_sem > 0){
                            $periodo = $semestres[$sem]." ".$aaaa;
                            $id_p = $periodo_dao->GetIDbyNombre($periodo);

                            $desplazamiento->id_periodo = $id_p;

                            if ($id_p > 0){
                                $desplazamiento->cantidad = $valor_sem;

                                //INSERTA EL REGISTRO
                                $sql =  "INSERT INTO registro (ID_TIPO_DESPLA,ID_MUN,ID_CLASE_DESPLA,ID_MUN_ID_MUN,ID_FUEDES,ID_PERIO,ID_POBLA,ID_CONP,VALOR,FECHA_CORTE) VALUES
                                    (".$desplazamiento->id_tipo.",'".$desplazamiento->id_mun_rec."',".$desplazamiento->id_clase.",'".$desplazamiento->id_mun_exp."',".$desplazamiento->id_fuente.",".$desplazamiento->id_periodo.",".$desplazamiento->id_poblacion.",".$desplazamiento->id_contacto.",".$desplazamiento->cantidad.",'".$desplazamiento->f_corte."')";
                                //										echo "Inserta sem: $sql Periodo: $periodo<br>";
                                $this->conn->Execute($sql,0);
                            }
                        }

                        $valor_sem = 0;
                        $sem++;

                    }

                    $valor_trim = 0;
                    $trim++;
                }
            }
        }				

        echo "<script>";
        echo "alert('Se cargaron : ".$ex." Registros.');";
        echo "</script>";

        return 1;  // Variable usada para cargar el siguiente archivo cuando termina la carga de 1 archivo, sino, error
    }

	/**
	 * Actualiza el valor de la fecha de corte
	 * @access public
	 * @param string $f_corte
	 * @param int $id_fuente
	 */
	function updateFechaCorte($f_corte,$id_fuente){
        
        $sql = "UPDATE registro SET FECHA_CORTE = '$f_corte' WHERE ID_FUEDES = $id_fuente";
		$this->conn->Execute($sql);
        
        $sql = "UPDATE fuente_desplazamiento SET FECHA_CORTE = '$f_corte' WHERE ID_FUEDES = $id_fuente";
		$this->conn->Execute($sql);

		echo "<script>";
		echo "alert('Fecha de corte actualizada con éxito');";
		echo "location.href='index.php?m_e=desplazamiento&accion=fechaCorte';";
		echo "</script>";
	}
}


/**
 * Ajax de Desplazamiento
 *
 * Contiene los metodos para Ajax de la clase Desplazamiento
 * @author Ruben A. Rojas C.
 */

Class DesplazamientoAjax extends DesplazamientoDAO {

	/**
	 * Lista los Datos en una Tabla y Grafica los datos - GRAFICAS Y RESUMENES
	 * @access public
	 * @param $reporte Reporte a mostrar
	 * @param $exp_rec Clase de Desplazamiento. 1 = Expulsion, 2 =Recepcion
	 * @param array $fuentes Fuentes para el reporte
	 * @param $depto Desagregacion geografica 0 = Mpal 1 = Deptal 2 = Nacional
	 * @param $ubicacion Id de la Ubicacion
	 * @param $f_ini	Año Inicial
	 * @param $f_fin	Año Final
	 * @param string $chart Tipo de grafica
	 * @param string $ejex Años o Meses en el ejex
	 * @param string $dato_para_reporte_4_despla Municipios o Departamentos
	 */
	function GraficaResumenDesplazamiento($reporte,$exp_rec,$fuentes,$depto,$ubicacion,$f_ini,$f_fin,$chart,$ejex,$dato_para_reporte_4_despla){

		require_once $_SERVER["DOCUMENT_ROOT"]."/sissh/admin/lib/common/graphic.class.php";
		require_once $_SERVER["DOCUMENT_ROOT"]."/sissh/admin/lib/libs_desplazamiento.php";

		//INICIALIZACION DE VARIABLES
		$depto_dao = New DeptoDAO();
		$municipio_dao = New MunicipioDAO();
		$clase_dao = New ClaseDesplazamientoDAO();
		$tipo_dao = New TipoDesplazamientoDAO();
		$fuente_dao = New FuenteDAO();
		$periodo_dao = New PeriodoDAO();
		$poblacion_dao = New PoblacionDAO();
		$num_desplazados = array();
		$arr = array("y","z");
		$mes = array("","Ene","Feb","Mar","Abr","May","Jun","Jul","Ago","Sep","Oct","Nov","Dic");

		//TIPOS DE DESPLAZAMIENTO
		$tipos = $tipo_dao->GetAllArray('');

		$exp = 0;
		$rec = 0;
		if ($exp_rec == 1)	$exp = 1;
		if ($exp_rec == 2)	$rec = 1;

		$ini = $f_ini;
		$fin = $f_fin;

		if ($depto == 2)	$dato_para = 3;
		else if ($depto == 0)	$dato_para = 2;
		else $dato_para = 1;
        
        $nom_ubi = "Nacional";
        if ($depto == 1){
            $ubi = $depto_dao->Get($ubicacion);
            $nom_ubi = $ubi->nombre;
        }
        else if ($depto == 0){
            $ubi = $municipio_dao->Get($ubicacion);
            $nom_ubi = $ubi->nombre;
        }
        
        $dato_para_reporte_4_despla_txt = ($dato_para_reporte_4_despla == 'mpio') ? 'Municipios' : 'Departamentos';
        
        $titles = array("",
                "Desplazamiento acumulado",
                "Nuevos Desplazamientos",
                "Nuevos Desplazamientos por Fuente",
                "$dato_para_reporte_4_despla_txt con mayor desplazamiento",
                "Expulsión - Recepción",
                "Nuevos Desplazamientos UARIV por Etnia",
                "Nuevos Desplazamientos UARIV por Género"
                );
        $title = $titles[$reporte];

        
        // Reportes 2015
        if ($reporte > 5) {
            
            $dir_csv = '/sissh/static/desplazamiento/';

            if ($exp == 1) {
                $dir_csv .= 'expulsion_';
            }
            else {
                $dir_csv .= 'recepcion_';
            }

            switch($reporte) {
                case '6':
                    $caso = 'etnia';
                break;
                case '7':
                    $caso = 'genero';
                break;
            }

            $file_csv = $dir_csv.$caso.'_';
            $reporte_csv = $file_csv;

            // Nacional
            if ($depto == 2) {
                $file_csv .= 'nacional';
                $reporte_csv .= 'departamental';
            }
            // Departamental
            else if ($depto == 1) {
                $file_csv .= 'departamental_'.$ubicacion;
                $reporte_csv .= 'municipal_'.$ubicacion;
            }
            
            $file_csv .= '_sidih.csv';
            $reporte_csv .= '_'.$f_ini.'_'.$f_fin.'_sidih.csv';
            
            $file_csv_path = $_SERVER["DOCUMENT_ROOT"].$file_csv;
            $reporte_csv_path = $_SERVER["DOCUMENT_ROOT"].$reporte_csv;
            
            if (!file_exists($reporte_csv_path)) {
                $html = $reporte_csv.'No hay datos';
            }
            else {

                $style = "font-size: 12px !important;";

                $html = "<table align='center' cellspacing='1' cellpadding='3' width='100%' border=0>";

                $html .= "<tr><td>Localizaci&oacute;n Geogr&aacute;fica: <b>$nom_ubi</b>
                            &nbsp;&nbsp;&nbsp;<img src='images/consulta/excel.gif'>&nbsp;<a href='export_data.php?nombre_archivo=desplazamiento_".$nom_ubi."_sidih&csv_path=$file_csv&csv2xls=1'>Descargar tabla</a></td>";
                $html .= "<tr>
                    <td valign='top'>
                    <table border=0 id='tablaDesplazamiento' class='tabla_grafica_conteo' cellpadding=4 
                    cellspacing=1 width='500' height='400' data-ejey='Desplazamientos' data-titulo='$title'>";


                $f = fopen($file_csv_path, "r");
                $l = 0;
                $head = $body = '';
                while (($line = fgetcsv($f)) !== false) {

                    // Esta en los años seleccionados
                    $show = ($l == 0 || ($l > 0 && $line[0] >= $f_ini && $line[0] <= $f_fin )) ? true : false;

                    if ($show) {
                        if ($l == 0) {
                            $head .= '<tr>';
                        }
                        else {
                            $body .= "<tr class='fila_tabla_conteo'>";
                        }
                        
                        foreach ($line as $c => $cell) {
                            
                            if ($l == 0) {
                                $head .= "<th style='$style'>".htmlspecialchars($cell)."</th>";
                            }
                            else {
                                $tag = ($c == 0) ? 'th' : 'td';
                                $body .= "<$tag style='$style'>" . htmlspecialchars($cell) . "</$tag>";
                            }
                        }
                    }
                    
                    $head .= '</tr>';
                    $body .= '</tr>';

                    $l++;
                }
            
                $html .= "<thead>$head</thead>";
                $html .= "<tbody>$body</tbody>";
                
                fclose($f);

                // Td para grafica
                $html .= "</table></td><td id='highchart' width='500' height='400'></td></tr>";
                $html .= "</table>";
                
                $html .= "<br /><br />";

                // Reporte deptos o mpios
                // Nacional
                if ($depto == 2) {
                    $html .= "<div class='left'><img src='images/consulta/excel.gif'>&nbsp;<a href='export_data.php?nombre_archivo=desplazamiento_".$nom_ubi."_sidih&csv_path=$reporte_csv&csv2xls=1'>Descargar reporte por departamentos</a>&nbsp;&nbsp;&nbsp;&nbsp;</div>";
                }
                // Departamental
                else if ($depto == 1) {
                    $html .= "<div class='left'><img src='images/consulta/excel.gif'>&nbsp;<a href='export_data.php?nombre_archivo=desplazamiento_".$nom_ubi."_sidih&csv_path=$reporte_csv&csv2xls=1'>Descargar reporte por municipios</a></div>";
                }
                
                $html .= "<br /><br />&nbsp;";

                /*
                // Nacional
                if ($depto == 2) {
                    $ubi_dao = $depto_dao;
                }    
                // Departamental
                else if ($depto == 1) {
                    $ubi_dao = $municipio_dao;
                }

                $html .= "<div><img src='images/consulta/excel.gif'>&nbsp;<a href='export_data.php?nombre_archivo=desplazamiento_".$nom_ubi."_sidih&csv_path=$reporte_csv&csv2xls=1'>Exportar tabla a Hoja de c&aacute;lculo</a></div>";
                $html .= '<table class="tabla_reportelist_outer">';
                
                //die($_SERVER["DOCUMENT_ROOT"].$reporte_csv);
                $f = fopen($_SERVER["DOCUMENT_ROOT"].$reporte_csv, "r");

                $l = 0;
                $show = array();
                while (($line = fgetcsv($f)) !== false) {

                    $html .= '<tr>';
                    
                    foreach ($line as $c => $cell) {
                        
                        // Esta en los años seleccionados
                        if ($l == 0) {

                            if ($c == 0) {
                                $html .= "<td style='$style'>Ubicación</td>";
                            }
                            else {
                                preg_match('/(\d+)_/',$cell,$matches);
                                $y = $matches[1];

                                if (($y >= $f_ini && $y <= $f_fin )) {
                                    $html .= "<td style='$style'>" . htmlspecialchars($cell) . "</td>";
                                    $show[] = $c;
                                }
                            }
                        }
                        else {
                            if ($c == 0) {
                                $ubi = $ubi_dao->Get($cell);

                                if (empty($ubi->nombre)) {
                                    $ubi_nom_reporte = '---';
                                }
                                else {
                                    $ubi_nom_reporte = $ubi->nombre;
                                }

                                $html .= "<td style='$style'>" . $ubi_nom_reporte . "</td>";
                            }
                            else {
                                if (in_array($c, $show)) {
                                    $html .= "<td style='$style'>" . htmlspecialchars($cell) . "</td>";
                                } 
                            }
                        }
                    }
                    
                    $html .= '</tr>';
                    $l++;
                }
                
                fclose($f);

                 */
            }

            echo $html;

        }
        else {
            // Otros
            //Determina si el reporte es por periodo
            $por_periodo = 0;
            $por_mpio = 0;
            if (in_array($reporte,array(1,2,3,5))){
                $por_periodo = 1;
            }
            else if ($reporte == 4){
                $por_mpio = 1;
            }



            //inicio pie
            if ($exp == 1) $pie = "Expulsión | " ;
            else{
                if (count($fuentes) == 2)		$pie = "Recepción/Estimado Llegadas | ";
                else if ($fuentes[0] == 1)	$pie = "Estimado Llegadas | ";
                else							$pie = "Recepción | ";
            }

            foreach ($fuentes as $f=>$id_fuente){
                $fuente = $fuente_dao->Get($id_fuente);

                if ($f > 0)	$pie .= "- ";

                $pie .= $fuente->nombre;
            }

            if ($por_mpio){

                $ini_fin = ($ini == $fin) ? $ini : "$ini - $fin";			
                $pie .= " | $ini_fin";
            }


            //SI EL REPORTE ES DE TOTAL (ACUMULADO) SE CONSTRUYE LA MATRIZ DESDE EL AÑO 1980
            if ($reporte == 1){
                $ini = 1985;
                $ini_display = $f_ini;
            }
            else{
                $ini_display = $ini;
            }

            //PERIODOS - AÑOS o MESES o etc
            for ($a=$ini;$a<=$fin;$a++){

                if ($ejex == 'aaaa'){

                    $periodo[] = $a;
                    if ($a >= $f_ini) $periodo_display[] = $a;
                    else			  $periodo_before[] = $a;
                }
                else{

                    $hoy = getdate();
                    $a_actual = $hoy['year'];

                    $m_fin = 12;
                    $t_fin = 4;
                    $s_fin = 2;

                    //Si es el ultimo año, debe parar en el mes de la mayor fecha de corte 
                    if ($a == $a_actual){
                        $m_fin = 0;
                        foreach ($fuentes as $id_fuente){
                            $f_c = explode("-",$this->GetFechaCorte($id_fuente));
                            if (count($f_c) == 3 && $f_c[0] == $a_actual){
                                $m_c = $f_c[1];


                                if ($m_c > $m_fin)	$m_fin = $m_c;
                            }
                        }

                        //trim
                        $t_fin = floor($m_fin / 3);

                        //sem
                        $s_fin = floor($m_fin / 6);
                    }

                    switch ($ejex){

                        case 'mes':
                            for($m=1;$m<=$m_fin;$m++){
                                $periodo[] = $periodo_dao->GetIDbyMesyAAAA($m,$a);				
                                if ($a >= $f_ini) $periodo_display[] = $periodo_dao->GetIDbyMesyAAAA($m,$a);	
                                else			  $periodo_before[] = $periodo_dao->GetIDbyMesyAAAA($m,$a);	
                            }

                            break;

                        case 'trim':
                            $trims = array("I","II","III","IV");

                            for($t=0;$t<$t_fin;$t++){
                                $nom_trim = $trims[$t]."TRIM $a";
                                $periodo[] = $periodo_dao->GetIDbyNombre($nom_trim);				
                                if ($a >= $f_ini) $periodo_display[] = $periodo_dao->GetIDbyNombre($nom_trim);	
                                else			  $periodo_before[] = $periodo_dao->GetIDbyNombre($nom_trim);
                            }
                            break;

                        case 'sem':
                            $sems = array("I","II");

                            for($s=0;$s<$s_fin;$s++){
                                $nom = $sems[$s]." Semestre $a";
                                $periodo[] = $periodo_dao->GetIDbyNombre($nom);				
                                if ($a >= $f_ini) $periodo_display[] = $periodo_dao->GetIDbyNombre($nom);	
                                else			  $periodo_before[] = $periodo_dao->GetIDbyNombre($nom);
                            }
                            break;

                    }
                }
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

                        case 'trim':
                            $ejex_title = 'Trimestre';
                            break;

                        case 'sem':
                            $ejex_title = 'Semestre';
                            break;

                    }
                }
                
                foreach ($periodo as $a){
                    foreach ($fuentes as $id_fuente){
                        foreach ($tipos as $tipo){
                            //eje x = años
                            if ($ejex == 'aaaa'){
                                $valor = $this->GetValorToReportTotalAAAA($exp,$id_fuente,$tipo->id,$a,$ubicacion,$dato_para);
                                $personas = $this->GetValorToReportTotalAAAA($exp,$id_fuente,$tipo->id,$a,$ubicacion,$dato_para,'personas');

                                if ($reporte == 5)	$valor_rec = $this->GetValorToReportTotalAAAA(0,$id_fuente,$tipo->id,$a,$ubicacion,$dato_para);
                            }
                            else{
                                $valor = $this->GetValorToReport($exp,$id_fuente,$tipo->id,$a,$ubicacion,$dato_para);
                                $personas = $this->GetValorToReport($exp,$id_fuente,$tipo->id,$a,$ubicacion,$dato_para,'personas');
                                if ($reporte == 5)	$valor_rec = $this->GetValorToReport(0,$id_fuente,$tipo->id,$a,$ubicacion,$dato_para);
                            }

                            $num_desplazados[$a][$id_fuente][$tipo->id] = $valor;
                            $num_personas[$a][$id_fuente][$tipo->id] = $personas;
                            if ($reporte == 5)	$num_desplazados_rec[$a][$id_fuente][$tipo->id] = $valor_rec;
                        }
                    }
                }

                /*
                //UBIACION GEOGRAFICA
                if ($depto == 1){
                    foreach ($periodo as $a){
                        foreach ($fuentes as $id_fuente){
                            foreach ($tipos as $tipo){
                                //eje x = años
                                if ($ejex == 'aaaa'){
                                    $valor = $this->GetValorToReportTotalAAAA($exp,$id_fuente,$tipo->id,$a,$ubicacion,1);
                                    if ($reporte == 5)	$valor_rec = $this->GetValorToReportTotalAAAA(0,$id_fuente,$tipo->id,$a,$ubicacion,1);
                                }
                                else{
                                    $valor = $this->GetValorToReport($exp,$id_fuente,$tipo->id,$a,$ubicacion,1);
                                    if ($reporte == 5)	$valor_rec = $this->GetValorToReport(0,$id_fuente,$tipo->id,$a,$ubicacion,1);
                                }

                                $num_desplazados[$a][$id_fuente][$tipo->id] = $valor;
                                if ($reporte == 5)	$num_desplazados_rec[$a][$id_fuente][$tipo->id] = $valor_rec;
                            }
                        }
                    }
                }
                else if ($depto == 2){
                    //NACIONAL
                    foreach ($periodo as $a){
                        foreach ($fuentes as $id_fuente){
                            foreach ($tipos as $tipo){
                                //eje x = años
                                if ($ejex == 'aaaa'){
                                    $valor = $this->GetValorToReportTotalAAAA($exp,$id_fuente,$tipo->id,$a,0,3);
                                    $personas = $this->GetValorToReportTotalAAAA($exp,$id_fuente,$tipo->id,$a,0,3,'personas');
                                    if ($reporte == 5)	$valor_rec = $this->GetValorToReportTotalAAAA(0,$id_fuente,$tipo->id,$a,0,3);
                                }
                                else{
                                    $valor = $this->GetValorToReport($exp,$id_fuente,$tipo->id,$a,0,3);
                                    $personas = $this->GetValorToReport($exp,$id_fuente,$tipo->id,$a,0,3,'personas');
                                    if ($reporte == 5)	$valor_rec = $this->GetValorToReport(0,$id_fuente,$tipo->id,$a,0,3);
                                }
                                $num_desplazados[$a][$id_fuente][$tipo->id] = $valor;
                                $num_personas[$a][$id_fuente][$tipo->id] = $personas;
                                if ($reporte == 5)	$num_desplazados_rec[$a][$id_fuente][$tipo->id] = $valor_rec;
                            }
                        }
                    }
                }

                //MUNICIPIO
                else {
                    foreach ($periodo as $a){
                        foreach ($fuentes as $id_fuente){
                            foreach ($tipos as $tipo){
                                //eje x = años
                                if ($ejex == 'aaaa'){
                                    $valor = $this->GetValorToReportTotalAAAA($exp,$id_fuente,$tipo->id,$a,$ubicacion,2);
                                    if ($reporte == 5)	$valor_rec = $this->GetValorToReportTotalAAAA(0,$id_fuente,$tipo->id,$a,$ubicacion,2);
                                }
                                else{
                                    $valor = $this->GetValorToReport($exp,$id_fuente,$tipo->id,$a,$ubicacion,2);
                                    if ($reporte == 5)	$valor_rec = $this->GetValorToReport(0,$id_fuente,$tipo->id,$a,$ubicacion,2);
                                }

                                $num_desplazados[$a][$id_fuente][$tipo->id] = $valor;
                                if ($reporte == 5)	$num_desplazados_rec[$a][$id_fuente][$tipo->id] = $valor_rec;
                            }
                        }

                    }
                }
                */
            } //Fin reporte por periodo

            if ($por_mpio == 1){
                $ejex_title = $dato_para_reporte_4_despla_txt;
                $ejex_angulo = 2;

                if ($reporte == 4){

                    $col_geo = ($dato_para_reporte_4_despla == 'mpio') ? 'id_mun' : 'id_depto';

                    $sql = "SELECT sum(valor) as v, $col_geo as i FROM registro_consulta_tmp as r";

                    if ($exp == 1){
                        $sql .= "  WHERE exp = 1";
                    }
                    else{
                        $sql .= " WHERE rec = 1";
                    }

                    $sql .= " AND aaaa IN (".implode(",",$periodo).") AND id_fuente = $fuentes[0]";

                    //UBIACION GEOGRAFICA
                    if ($depto == 1){
                        $sql .= " AND id_depto = '$ubicacion'";
                    }

                    if ($dato_para_reporte_4_despla == 'mpio'){
                        $sql .= " AND id_mun IS NOT NULL";
                    }
                    else{
                        $sql .= " AND id_mun IS NULL";
                    }

                    $sql .= " GROUP BY i ORDER BY v DESC LIMIT 0,10";
                    //echo $sql;
                    $rs = $this->conn->OpenRecordset($sql);
                    while ($row = $this->conn->FetchRow($rs)){
                        $num_desplazados[$row[1]] = $row[0];
                    }
                } //Fin reporte 4
            }//Fin reporte por mpio

            $num_arr = count($num_desplazados);

            //GRAFICA
            $PG = new PowerGraphic;
            $PG->title     = $title;
            $PG->axis_x    = $ejex_title;
            $PG->axis_y    = 'Personas';
            $PG->skin      = 1;
            $PG->credits   = 0;
            echo "<br>";
            echo "<table align='center' cellspacing='1' cellpadding='3' width='100%' border=0>";

            $html = "<tr><td align='left'><img src='images/consulta/excel.gif'>&nbsp;<a href='consulta/excel.php?f=desplazamiento_".$nom_ubi."_sidih'>Exportar tabla a Hoja de c&aacute;lculo</a></td></tr>";

            $html .= "<tr><td>Localizaci&oacute;n Geogr&aacute;fica: <b>$nom_ubi</b></td></tr>";
            $html .= "<tr>
                <td valign='top'>
                <table border=0 class='tabla_grafica_conteo' cellpadding=4 cellspacing=1 width='350'>";

            //ESCRIBE TITULO
            $xls = "<table><tr><td>$title</td></tr>";


            if ($num_arr > 0){

                if ($reporte == 1){
                    //Suma los años desde 1980 hasta el seleccionado para el reporte
                    //for ($a=$ini;$a<$ini_display;$a++){
                    foreach($periodo_before as $a){
                        foreach ($fuentes as $id_fuente){
                            $total_tipos = 0;
                            foreach ($tipos as $tipo){
                                if(isset($num_desplazados[$a][$id_fuente][$tipo->id])){
                                    $total_tipos += $num_desplazados[$a][$id_fuente][$tipo->id];
                                }
                            }
                            ($a == $periodo[0]) ?  $num_ini[$id_fuente] = $total_tipos : $num_ini[$id_fuente] += $total_tipos;

                            //SUMA LA CANTIDAD DEL PERIODO SIN FECHA, PARA QUE LOS TOTALES SEAN IGUALES
                            if ($a == $periodo[0]){
                                $num_ini[$id_fuente] += $this->GetValorToReportTotalAAAA($exp,$id_fuente,0,$this->aaaa_sin_fecha,$ubicacion,$dato_para);
                            }
                        }
                    }
                }

                switch ($reporte){
                    case 1 :
                        $html .= "<tr class='titulo_tabla_conteo'><td>$ejex_title</td>";
                        $xls .="<tr><td>$ejex_title</td>";

                        $f = 1;
                        foreach ($fuentes as $id_fuente){
                            $fuente = $fuente_dao->Get($id_fuente);
                            $html .= "<td align='center'><b>".$fuente->nombre."</b></td>";
                            $xls .= "<td align='center'><b>".$fuente->nombre."</b></td>";

                            $txt_dp .= '<td>Desplazamientos</td><td>Personas</td>';

                            //eval("\$PG->graphic_".$f." = '".$fuente->nombre."';");
                            $PG->{'graphic_'.$f} = $fuente->nombre;

                            $f++;
                        }
                        $html .= "</tr>";
                        $xls .= "</tr>";

                        break;
                    case 2:
                        $html .= "<tr class='titulo_tabla_conteo'><td>$ejex_title</td>";
                        $xls .="<tr><td>$ejex_title</td>";
                        $f = 1;
                        $txt_dp = '';
                        foreach ($fuentes as $id_fuente){
                            $fuente = $fuente_dao->Get($id_fuente);
                            $html .= "<td align='center' colspan='2'><b>".$fuente->nombre."</b></td>";
                            $xls .= "<td align='center' colspan='2'><b>".$fuente->nombre."</b></td>";
                            
                            $txt_dp .= '<td></td><td>Desplazamientos</td><td>Personas</td>';

                            //eval("\$PG->graphic_".$f." = '".$fuente->nombre."';");
                            $PG->{'graphic_'.$f} = $fuente->nombre;


                            $f++;
                        }
                        $html .= "</tr>";
                        $xls .= "</tr>";

                        // Texto despla - personas
                        $txt_dp = "<tr>$txt_dp</tr>";
                        $html .= $txt_dp;
                        $xls .= $txt_dp;

                        break;
                    case 3:
                        // Recalcula tipos solo para accion social
                        
                        //TIPOS DE DESPLAZAMIENTO
                        $tipos = $tipo_dao->GetAllArray('id_tipo_despla not in (3)');


                        $html .= "<tr class='titulo_tabla_conteo'><td>$ejex_title</td>";
                        $xls .="<tr><td>$ejex_title</td>";
                        $f = 1;
                        foreach ($tipos as $tipo){
                            $html .= "<td align='center'><b>".$tipo->nombre."</b></td>";
                            $xls .= "<td align='center'><b>".$tipo->nombre."</b></td>";


                            //eval("\$PG->graphic_".$f." = '".$tipo->nombre."';");
                            $PG->{'graphic_'.$f} = $tipo->nombre;

                            $f++;
                        }
                        $html .= "</tr>";
                        $xls .= "</tr>";

                        break;

                    case 4:
                        $html .= "<tr class='titulo_tabla_conteo'><td align='center'><b>C&oacute;digo</b></td><td>$ejex_title</td><td align='center'><b>Cantidad</b></td></tr>";
                        $xls .="<tr class='titulo_tabla_conteo'><td align='center'><b>C&oacute;digo</b></td><td>$ejex_title</td><td align='center'><b>Cantidad</b></td></tr>";

                        break;

                    case 5:
                        $html .= "<tr class='titulo_tabla_conteo'><td>$ejex_title</td>";
                        $xls .="<tr><td>$ejex_title</td>";

                        $html .= "<td align='center'><b>Expulsi&oacute;n</b></td>";
                        $xls .= "<td align='center'><b>Expulsi&oacute;n</b></td>";
                        $PG->graphic_1 = "Expulsión";

                        $html .= "<td align='center'><b>Recepci&oacute;n</b></td>";
                        $xls .= "<td align='center'><b>Recepci&oacute;n</b></td>";
                        $PG->graphic_1 = "Recepción";

                        $html .= "</tr>";
                        $xls .= "</tr>";


                        break;
                }

                if ($por_periodo == 1){

                    $ii = 0;
                    $aa = 0;

                    //Si es acumulado coloca la fila antes de ....
                    if ($reporte == 1){
                        $html .= "<tr class='fila_tabla_conteo'><td>Antes de $f_ini</td>";	
                        $xls .= "<tr><td>Antes de $f_ini</td>";	

                        foreach ($fuentes as $f=>$id_fuente){
                            $html .= "<td align='right'>".number_format($num_ini[$id_fuente])."</td>";
                            $xls .= "<td>".number_format($num_ini[$id_fuente],0,"","")."</td>";

                            $PG->x[$aa] = "<-$f_ini";	

                            //eval("\$PG->".$arr[$f]."[".$aa."] = ".$num_ini[$id_fuente].";");
                            $PG->{$arr[$f]}[$aa] = $num_ini[$id_fuente];
                        }

                        $aa ++;

                        $html .= "</tr>";
                        $xls .= "</tr>";
                    }

                    //for ($a=$ini_display;$a<=$fin;$a++){
                    foreach ($periodo_display as $a){

                        if ($ejex == 'aaaa'){
                            $per = $a;
                        }
                        else{
                            $periodo_vo = $periodo_dao->Get($a);	
                            $per = $periodo_vo->nombre;
                        }

                        $html .= "<tr class='fila_tabla_conteo'>";
                        $html .= "<td>".$per."</td>";
                        $xls .= "<tr><td>".$per."</td>";

                        $PG->x[$aa] = $per;

                        $num_desplazados_total[$a] = 0;
                        switch ($reporte){

                            //Acumulado
                            case 1 :

                                $f = 0;
                                foreach ($fuentes as $id_fuente){
                                    if(isset($num_desplazados[$a][$id_fuente])){
                                        $num = 0;
                                        foreach ($tipos as $tipo){
                                            if(isset($num_desplazados[$a][$id_fuente][$tipo->id])){
                                                $num += $num_desplazados[$a][$id_fuente][$tipo->id];
                                            }
                                        }

                                        ($aa == 1) ? $acumulado[$id_fuente] = $num + $num_ini[$id_fuente] : $acumulado[$id_fuente] += $num;

                                        $num_desplazados_total[$a] = $num;

                                        $html .= "<td align='right'>".number_format($acumulado[$id_fuente])."</td>";
                                        $xls .= "<td align='right'>".number_format($acumulado[$id_fuente],0,"","")."</td>";

                                        //eval("\$PG->".$arr[$f]."[".$aa."] = ".$acumulado[$id_fuente].";");
                                        $PG->{$arr[$f]}[$aa] = $acumulado[$id_fuente];

                                    }
                                    else{
                                        $html .= "<td></td>";
                                        $xls .= "<td></td>";
                                    }

                                    $f++;
                                }
                                break;

                                //Nuevos Desplazamientos
                            case 2:
                                $f = 0;
                                foreach ($fuentes as $id_fuente){
                                    if(isset($num_desplazados[$a][$id_fuente])){
                                        $num = 0;
                                        $personas = 0;
                                        $nd = 1;
                                        foreach ($tipos as $tipo){
                                            if(isset($num_desplazados[$a][$id_fuente][$tipo->id])){
                                                $num += $num_desplazados[$a][$id_fuente][$tipo->id];
                                                $personas += $num_personas[$a][$id_fuente][$tipo->id];
                                                $nd = 0;
                                            }
                                        }

                                        if ($nd == 0){
                                            $num_desplazados_total[$a] = $num;
                                            $html .= "<td align='right'>".number_format($num)."</td>";
                                            $html .= "<td align='right'>".number_format($personas)."</td>";
                                            $xls .= "<td align='right'>".number_format($num,0,"","")."</td>";

                                            //eval("\$PG->".$arr[$f]."[".$aa."] = ".$num.";");
                                            $PG->{$arr[$f]}[$aa] = $num;
                                        }
                                        else{
                                            $num = "N.D.";

                                            //eval("\$PG->".$arr[$f]."[".$aa."] = 0;");
                                            $PG->{$arr[$f]}[$aa] = 0;

                                            $html .= "<td align='right'>".$num."</td>";
                                            $html .= "<td align='right'>".$num."</td>";
                                            $xls .= "<td align='right'>".$num."</td>";
                                        }

                                    }
                                    else{
                                        $html .= "<td></td>";
                                        $xls .= "<td></td>";
                                    }

                                    $f++;
                                }
                                break;
                            case 3:
                                $f = 0;
                                foreach ($fuentes as $id_fuente){
                                    if(isset($num_desplazados[$a][$id_fuente])){

                                        foreach ($tipos as $tipo){
                                            $num = 0;
                                            if(isset($num_desplazados[$a][$id_fuente][$tipo->id])){
                                                $num = $num_desplazados[$a][$id_fuente][$tipo->id];
                                            }
                                            $html .= "<td align='right'>".number_format($num)."</td>";
                                            $xls .= "<td align='right'>".number_format($num,0,"","")."</td>";
                                            $num_desplazados_total[$a] += $num;

                                            //eval("\$PG->".$arr[$f]."[".$aa."] = ".$num.";");
                                            $PG->{$arr[$f]}[$aa] = $num;

                                            $f++;
                                        }

                                    }
                                    else{
                                        $html .= "<td></td>";
                                        $xls .= "<td></td>";
                                    }
                                }
                                break;

                            case 5:
                                $id_fuente = 2;

                                for($f=0;$f<2;$f++){

                                    $matrix = ($f == 0) ? $num_desplazados : $num_desplazados_rec;

                                    if(isset($matrix[$a][$id_fuente])){
                                        $num = 0;
                                        $nd = 1;
                                        foreach ($tipos as $tipo){
                                            if(isset($matrix[$a][$id_fuente][$tipo->id])){
                                                $num += $matrix[$a][$id_fuente][$tipo->id];
                                                $nd = 0;
                                            }
                                        }

                                        if ($nd == 0){
                                            $matrix_total[$a] = $num;
                                            $html .= "<td align='right'>".number_format($num)."</td>";
                                            $xls .= "<td align='right'>".number_format($num,0,"","")."</td>";

                                            //eval("\$PG->".$arr[$f]."[".$aa."] = ".$num.";");
                                            $PG->{$arr[$f]}[$aa] = $num;
                                        }
                                        else{
                                            $num = "N.D.";

                                            //eval("\$PG->".$arr[$f]."[".$aa."] = 0;");
                                            $PG->{$arr[$f]}[$aa] = 0;

                                            $html .= "<td align='right'>".$num."</td>";
                                            $xls .= "<td align='right'>".$num."</td>";
                                        }

                                    }
                                    else{
                                        $html .= "<td></td>";
                                        $xls .= "<td></td>";
                                    }
                                }

                                break;
                        }

                        $html .= "</tr>";

                        $aa++;
                    }
                }

                if ($por_mpio == 1){
                    switch ($reporte){
                        case 4 :
                            foreach ($num_desplazados as $id=>$num){

                                $vo = ($dato_para_reporte_4_despla == 'mpio') ? $municipio_dao->get($id) : $depto_dao->get($id);

                                $PG->x[] = $vo->nombre;

                                $html .= "<tr class='fila_tabla_conteo'><td>$id</td><td>".$vo->nombre."</td><td align='right'>".number_format($num)."</td></tr>";
                                $xls .= "<tr class='fila_tabla_conteo'><td>$id</td><td>".$vo->nombre."</td><td align='right'>".number_format($num,0,"","")."</td></tr>";

                                $PG->y[] = $num;
                            }
                            break;
                    }

                }

                $_SESSION["xls"] = $xls;
            }
            else {
                $html .= "<tr><td align='center'><br><b>NO SE ENCONTRARON DATOS DE DESPLAZAMIENTO</b></td></tr>";
            }
		
            // Valores API
            if (!isset($_GET['api']) || (isset($_GET['api']) && isset($_GET["tabla_api"]) && $_GET["tabla_api"] == 1)){
                echo $html;
            }

            echo "</table>";
            echo "</td>";
            
            echo '<td>';
            
            /********************************************************************************
            //PARA GRAFICA OPEN CHART
            /*******************************************************************************/
            if (!isset($_GET['api']) || (isset($_GET['api']) && isset($_GET['grafica_api']) && $_GET['grafica_api'] == 1)){
                $chk_chart = array('bar' => '', 'bar_3d' => '', 'line' => '');
                $chk_chart[$chart] = ' selected ';
                $font_size_key  =10;
                $font_size_x_label = 8;
                $font_size_y_label = 8;

                echo "<td align='center' valign='top'><table>
                    <tr><td align='left'>";

                //Si no hay datos no grafica
                if (count($PG->y) == 0 || max($PG->y) == 0){
                    echo "</td></tr></table></td></tr></table>";
                    die;
                }

                //Si no viene de API lo muestra
                if (!isset($_GET["api"])){
                    echo "Tipo de Gr&aacute;fica:&nbsp;
                    <select onchange=\"graficarDesplazamiento(this.value)\" class='select'>
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
                foreach ($PG->x as $x){
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

                $path = $_SERVER["DOCUMENT_ROOT"].'/sissh/admin/lib/common/open-flash-chart/';
                $path_in = $_SERVER["DOCUMENT_ROOT"].'/sissh/admin/lib/common/open-flash-chart/';

                include ("$path_in/php-ofc-library/sidihChart.php");
                $g = new sidihChart();

                $content = "<?
                    include_once('admin/lib/common/open-flash-chart/php-ofc-library/sidihChart.php' );

                \$g = new sidihChart();

                \$g->title('".utf8_encode($title)."');

                // label each point with its value
                \$g->set_x_labels( array(".$ejex.") );
                \$g->set_x_label_style( $font_size_x_label, '#000000',$ejex_angulo);";


                if ($chart == 'bar_3d'){
                    $content .= "\$g->set_x_axis_3d(6);";
                    $content .= "\$g->x_axis_colour('#dddddd','#FFFFFF');";
                }

                $f = 1;
                $max_y = 0;
                if ($reporte == 3){
                    $ids = $tipos;
                }
                else if ($reporte == 5){
                    $ids = array(0,1);
                    $reporte5 = array ("Expulsión","Recepción");
                }
                else{
                    $ids = $fuentes;
                }
                $txt_fecha_corte = "Fecha de Corte: ";
                foreach ($ids as $id){
                    $ff = $f - 1;

                    if ($reporte == 3){
                        $vo = $id;
                    }
                    else if ($reporte == 5){
                        $vo->nombre = $reporte5[$id];
                    }
                    else{
                        $vo = $fuente_dao->Get($id);
                        $f_corte = $this->GetFechaCorte($vo->id);
                        $f_corte = explode("-",$f_corte);
                        $txt_fecha_corte = $f_corte[2]." ".$mes[$f_corte[1]*1]." ".$f_corte[0];
                    }


                    if ($chart == 'bar' || $chart == 'bar_3d'){
                        $content .= "\$".$chart."_$f = new $chart(".$chart_style[$chart]['alpha'].", '".$chart_style[$chart]['color'][$ff]."' );\n";

                        if ($reporte == 3){
                            $content .= "\$".$chart."_".$f."->key('".utf8_encode($vo->nombre)."', $font_size_key);\n";
                        }
                        else if ($reporte == 5){
                            $content .= "\$".$chart."_".$f."->key('".utf8_encode($vo->nombre)."', $font_size_key);\n";
                        }
                        else{
                            $content .= "\$".$chart."_".$f."->key('".utf8_encode($vo->nombre)."\nCorte: $txt_fecha_corte', $font_size_key );\n";
                        }

                        $content .= "\$".$chart."_".$f."->data = array(".implode(",",$PG->$arr[$ff]).");\n";
                        $content .= "\$g->data_sets[] = \$".$chart."_$f;";
                    }
                    else if ($chart == 'line'){
                        $content .= "\$g->set_data(array(".implode(",",$PG->$arr[$ff])."));\n";
                        $content .= "\$g->".$chart."_dot(1,3,'".$chart_style[$chart]['color'][$ff]."','".utf8_encode($vo->nombre)."\nCorte: $txt_fecha_corte',$font_size_key);\n";
                    }

                    if ($max_y < max($PG->$arr[$ff]))	$max_y = max($PG->$arr[$ff]);

                    $f++;
                }

                $max_y = $g->maxY($max_y);

                $content .= "
                    \$g->set_tool_tip( '#key#: #x_label# <br> #val# Personas' );		
                \$g->set_y_max( ".$max_y." );
                \$g->y_label_steps(5);
                //Espacio para el footer de la imagen con el logo - Toco con x_legend vacio, jejeje
                \$g->set_x_legend('".utf8_encode($ejex_title)."\n\n".utf8_encode($pie)."\n',11);

                \$g->set_y_legend('Personas',11);

                \$g->set_num_decimals(0);

                // display the data
                echo \$g->render();
                ?>";

                //MODIFICA EL ARCHIVO DE DATOS
                $archivo = New Archivo();
                $fp = $archivo->Abrir($_SERVER["DOCUMENT_ROOT"].'/sissh/chart-data.php','w+');

                $archivo->Escribir($fp,$content);
                $archivo->Cerrar($fp);

                //IE Fix
                //Variable para que IE cargue el nuevo archivo de datos y cambie el tipo de grafica con los nuevos valores
                $nocache = time();
                include_once $path_in.'php-ofc-library/open_flash_chart_object.php';
                open_flash_chart_object( 500, 350, 'chart-data.php?nocache='.$nocache,false );
                ?>
                <!-- Grafica HTML -->
                <!--<td>
                <table id="table_grafica" cellspacing='0' cellpadding='5'>
                <tr>
                <td><img src='admin/lib/common/graphic.class.php?<?=$PG->create_query_string()?>' border=1 /></td>
                </tr>
                </table>
                </td>-->
                </td></tr>
            <?
            }
            //Si no viene de API lo muestra
            if (!isset($_GET["api"]) && !in_array($reporte,array(4,5))){  ?>
                <tr>
                    <td align='center'><br><br><input type='button' name='button' value='Generar Reporte' onclick="generarReporteDesplazamiento();" class='boton'>
                    <?
                    //Opcion nacional
                    if ($depto == 2){
                        echo "&nbsp;&nbsp;<input type='radio' name='tipo_nal' value='deptos' checked>&nbsp;Listar todos los Departamentos&nbsp;&nbsp;&nbsp;
                        <input type='radio' name='tipo_nal' value='mpios'>&nbsp;Listar todos los Municipios&nbsp;<br>";
                    }

            ?>
            </td>
            </tr>
            <? } ?>
            </table></td></tr>
            <tr><td id='reporteGraResumenDesplazamiento' colspan='3'></td>
            </tr>
            </table>

            <br><br>
            <!--
            <input type='hidden' id='pdf' name='pdf'>
            <input type='hidden' id='id_desplazamientos' name='id_desplazamientos' value='".implode(",",$id_orgs)."'>
            -->
            <br><br><span id='graResumenDesMsg'></span>		

		<?
        }

	}

	/**
	 * Genera el reporte de Desplazamiento apartir de una grafica - GRAFICAS Y RESUMENES
	 * @access public
	 * @param int $reporte Reporte a mostrar
	 * @param int $exp_rec Clase de Desplazamiento. 1 = Expulsion 2 =Recepcion
	 * @param array $fuentes Fuentes para el reporte
	 * @param int $depto Desagregacion geografica 0 = Mpal 1 = Deptal 2 = Nacional
	 * @param string $ubicacion Id de la Ubicacion
	 * @param int $f_ini	Año Inicial
	 * @param int $f_fin	Año Final
	 * @param string $ejex Años o Meses en el ejex
	 * @param string $tipo_nal	Tipo de reporte nacional
	 */
	function reporteGraResumenDesplazamiento($reporte,$exp_rec,$fuentes,$depto,$ubicacion,$f_ini,$f_fin,$ejex,$tipo_nal){

		set_time_limit(0);

		include_once ($_SERVER["DOCUMENT_ROOT"]."/sissh/admin/lib/libs_desplazamiento.php");

		//INICIALIZACION DE VARIABLES
		$depto_dao = New DeptoDAO();
		$municipio_dao = New MunicipioDAO();
		$clase_dao = New ClaseDesplazamientoDAO();
		$tipo_dao = New TipoDesplazamientoDAO();
		$fuente_dao = New FuenteDAO();
		$periodo_dao = New PeriodoDAO();
		$poblacion_dao = New PoblacionDAO();
		$archivo = New Archivo();
		$num_desplazados = array();
		$num_fuentes = count($fuentes);
		$cache = 1;   //1 para usar cache

		$exp = 0;
		$rec = 0;
		if ($exp_rec == 1)	$exp = 1;
		if ($exp_rec == 2)	$rec = 1;
		$ini = $f_ini;
		$fin = $f_fin;

		$titles = array("","Acumulado por A&ntilde;os","Nuevos Desplazamientos","Desplazados por Fuente");
		$title = $titles[$reporte];

		$tipos_g = array(0,7,4,1);
		$tipo_grafica = $tipos_g[$reporte];

		$cache_idx = $reporte."_".$exp_rec."_".implode("-",$fuentes)."_".$depto."_".$ubicacion."_".$f_ini."_".$f_fin."_".$ejex."_".$tipo_nal;

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
			$nom_ubi = "Nacional";
			if ($depto == 1){
				$ubi = $depto_dao->Get($ubicacion);
				$nom_ubi = $ubi->nombre;
			}
			else if ($depto == 0){
				$ubi = $municipio_dao->Get($ubicacion);
				$nom_ubi = $ubi->nombre;
			}

			//SI EL REPORTE ES DE TOTAL (ACUMULATIVO) SE CONSTRUYE LA MATRIZ DESDE EL AÑO 1980

			if ($reporte == 1){
				$ini = 1985;
				$ini_display = $f_ini;
			}
			else{
				$ini_display = $ini;
			}

			//TIPOS DE DESPLAZAMIENTO
			$tipos = $tipo_dao->GetAllArray('');
			$num_tipos = count($tipos);

			//PERIODOS - AÑOS o MESES o etc
			for ($a=$ini;$a<=$fin;$a++){

				if ($ejex == 'aaaa'){

					$periodo[] = $a;
					if ($a >= $f_ini) $periodo_display[] = $a;
					else			  $periodo_before[] = $a;
				}
				else{

					$hoy = getdate();
					$a_actual = $hoy['year'];

					$m_fin = 12;
					$t_fin = 4;
					$s_fin = 2;

					//Si es el ultimo año, debe parar en el mes de la mayor fecha de corte 
					if ($a == $a_actual){
						$m_fin = 0;
						foreach ($fuentes as $id_fuente){
							$f_c = explode("-",$this->GetFechaCorte($id_fuente));
							if (count($f_c) == 3 && $f_c[0] == $a_actual){
								$m_c = $f_c[1];


								if ($m_c > $m_fin)	$m_fin = $m_c;
							}
						}

						//trim
						$t_fin = floor($m_fin / 3);

						//sem
						$s_fin = floor($m_fin / 6);
					}

					switch ($ejex){

						case 'mes':
							for($m=1;$m<=$m_fin;$m++){
								$periodo[] = $periodo_dao->GetIDbyMesyAAAA($m,$a);				
								if ($a >= $f_ini) $periodo_display[] = $periodo_dao->GetIDbyMesyAAAA($m,$a);	
								else			  $periodo_before[] = $periodo_dao->GetIDbyMesyAAAA($m,$a);	
							}

							break;

						case 'trim':
							$trims = array("I","II","III","IV");

							for($t=0;$t<$t_fin;$t++){
								$nom_trim = $trims[$t]."TRIM $a";
								$periodo[] = $periodo_dao->GetIDbyNombre($nom_trim);				
								if ($a >= $f_ini) $periodo_display[] = $periodo_dao->GetIDbyNombre($nom_trim);	
								else			  $periodo_before[] = $periodo_dao->GetIDbyNombre($nom_trim);
							}
							break;

						case 'sem':
							$sems = array("I","II");

							for($s=0;$s<$s_fin;$s++){
								$nom = $sems[$s]." Semestre $a";
								$periodo[] = $periodo_dao->GetIDbyNombre($nom);				
								if ($a >= $f_ini) $periodo_display[] = $periodo_dao->GetIDbyNombre($nom);	
								else			  $periodo_before[] = $periodo_dao->GetIDbyNombre($nom);
							}
							break;

					}
				}
			}	

			//SE CONSTRUYE EL SQL
			$condicion = "";
			$arreglos = "";

			$id_deptos = $depto_dao->GetAllArrayID("");

			$html ="<br><div style='overflow:auto;width:940px;height:500px;border:1px solid #E1E1E1;'>";
			$html .="<table align='center' class='tabla_reportelist_outer' border=0>";
			$html .="<tr><td>&nbsp;</td></tr>";
			$html .="<tr><td align='left'>Exportar a Hoja de C&aacute;lculo : ";
			$html .="<a href=\"#\" onclick=\"location.href='consulta/excel.php?f=reporte_desplazamiento_sidih';return false;\"\"><img src='images/consulta/excel.gif' border=0 title='Exportar a Excel'></a></td></tr>";	
			$html .="<tr><td colspan=3><table class='tabla_reportelist'>";

			$xls = '
				<STYLE TYPE="text/css"><!--
				.excel_celda_texto {mso-style-parent:text; mso-number-format:"\@";white-space:normal;}
			--></STYLE>';

			$xls .= "<table border=1>";

			switch ($reporte){
				case 1 :

					$html .="<tr class='titulo_lista'><td>&nbsp;</td><td>&nbsp;</td>";
					$xls .="<tr><td></td><td></td>";

					//Acumulado coloca la fila antes de ....
					$html .="<td colspan='$num_fuentes' align='center'>Antes de $f_ini</td>";	
					$xls .= "<td colspan='$num_fuentes'>Antes de $f_ini</td>";	

					foreach ($periodo_display as $a){
						if ($ejex == 'aaaa'){
							$per = $a;
						}
						else{
							$periodo_vo = $periodo_dao->Get($a);	
							$per = $periodo_vo->nombre;
						}
						$html .="<td colspan='$num_fuentes' align='center'>$per</td>";
						$xls .= "<td colspan='$num_fuentes' align='center'>$per</td>";
					}

					$html .="</tr>";
					$xls .= "</tr>";

					$html .="<tr class='titulo_lista'><td>CODIGO</td><td>UBICACION</td>";
					$xls .= "<tr class='titulo_lista'><td>CODIGO</td><td>UBICACION</td>";

					//Acumulado coloca la fila antes de ....
					foreach ($fuentes as $id_fuente){
						$fuente = $fuente_dao->Get($id_fuente);
						$html .="<td align='center'><b>".$fuente->nombre."</b></td>";
						$xls .= "<td align='center'><b>".$fuente->nombre."</b></td>";

					}

					foreach ($periodo_display as $a){
						foreach ($fuentes as $id_fuente){
							$fuente = $fuente_dao->Get($id_fuente);
							$html .="<td align='center'><b>".$fuente->nombre."</b></td>";
							$xls .= "<td align='center'><b>".$fuente->nombre."</b></td>";

						}
					}
					$html .="</tr>";
					$xls .= "</tr>";

					//NACIONAL
					if ($depto == 2){

						if ($tipo_nal == 'deptos'){

							foreach ($id_deptos as $p=>$id_depto){

								if ($id_depto != '00')	$depto_vo = $depto_dao->Get($id_depto);
								else					$depto_vo->nombre = 'Nacional';

								$style = "";
								if (fmod($p+1,2) == 0)  $style = "fila_lista";

								$html .="<tr class='$style'><td>".$id_depto."</td>";
								$html .="<td>".$depto_vo->nombre."</td>";

								$xls .= "<tr><td class='excel_celda_texto'>".$id_depto."</td>";
								$xls .= "<td>".$depto_vo->nombre."</td>";

								//calcula la columna antes de ....
								foreach ($periodo_before as $per=>$a){
									foreach ($fuentes as $id_fuente){

										if ($a == $periodo_before[0]){
											$valor_antes[$id_depto][$id_fuente] = $this->GetValorToReportTotalAAAA($exp,$id_fuente,0,$this->aaaa_sin_fecha,$id_depto,1);
										}
										//eje x = años
										if ($ejex == 'aaaa'){
											$valor_tmp = $this->GetValorToReportTotalAAAA($exp,$id_fuente,0,$a,$id_depto,1);
										}
										else{
											$valor_tmp = $this->GetValorToReport($exp,$id_fuente,0,$a,$id_depto,1);
										}

										$valor_antes[$id_depto][$id_fuente] += $valor_tmp;

									}
								}

								//muestra la columna antes de ....
								foreach ($fuentes as $f=>$id_fuente){

									if ($p == 0) $valor_total[0][$f] = 0;

									$num_antes_de = $valor_antes[$id_depto][$id_fuente];
									$valor_total[0][$f] += $num_antes_de; 

									$html .="<td align='right'>".number_format($num_antes_de)."</td>";
									$xls .= "<td>".$num_antes_de."</td>";
								}

								$per = 1;
								foreach ($periodo_display as $a){
									foreach ($fuentes as $f=>$id_fuente){

										if ($p == 0) $valor_total[$per][$f] = 0;

										if ($a == $periodo_display[0]){
											$valor[$id_depto][$id_fuente] = $valor_antes[$id_depto][$id_fuente];
										}

										//eje x = años
										if ($ejex == 'aaaa'){
											$valor_tmp = $this->GetValorToReportTotalAAAA($exp,$id_fuente,0,$a,$id_depto,1);
										}
										else{
											$valor_tmp = $this->GetValorToReport($exp,$id_fuente,0,$a,$id_depto,1);
										}

										$valor[$id_depto][$id_fuente] += $valor_tmp;
										$valor_total[$per][$f] += $valor[$id_depto][$id_fuente];

										$html .="<td align='right'>".number_format($valor[$id_depto][$id_fuente])."</td>";
										$xls .= "<td align='right'>".$valor[$id_depto][$id_fuente]."</td>";
									}

									$per++;
								}
								$html .="</tr>";
								$xls .= "</tr>";
							}

							//Total
							$html .= "<tr class='titulo_lista'><td></td><td>Total</td>";
							$xls .= "<tr><td></td><td>Total</td>";

							foreach ($valor_total as $total_arr){
								foreach ($total_arr as $valor){
									$valor_xls = $valor;
									$valor = number_format($valor);

									$html .= "<td align='right'>$valor</td>";
									$xls .= "<td align='right'>$valor</td>";
								}
							}	
							$html .= "</tr>";
							$xls .= "</tr>";

						}
						else{

							//Agrega el 00000. que si esta en la tabla registro
							$muns = $municipio_dao->GetAllArrayID('','id_mun');
							array_push($muns,'00000');

							foreach($muns as $p=>$id_mun){
								$mun_vo = $municipio_dao->Get($id_mun);

								if ($id_mun != '00000')	$mun_vo = $municipio_dao->Get($id_mun);
								else					$mun_vo->nombre = 'Nacional';

								$style = "";
								if (fmod($p+1,2) == 0)  $style = "fila_lista";

								$html .="<tr class='$style'><td>".$id_mun."</td>";
								$html .="<td>".$mun_vo->nombre."</td>";

								$xls .= "<tr><td class='excel_celda_texto'>".$id_mun."</td>";
								$xls .= "<td>".$mun_vo->nombre."</td>";

								//calcula la columna antes de ....
								foreach ($periodo_before as $a){
									foreach ($fuentes as $id_fuente){

										if ($a == $periodo_before[0]){
											$valor_antes[$id_mun][$id_fuente] = $this->GetValorToReportTotalAAAA($exp,$id_fuente,0,$this->aaaa_sin_fecha,$id_mun,2);
										}
										//eje x = años
										if ($ejex == 'aaaa'){
											$valor_tmp = $this->GetValorToReportTotalAAAA($exp,$id_fuente,0,$a,$id_mun,2);
										}
										else{
											$valor_tmp = $this->GetValorToReport($exp,$id_fuente,0,$a,$id_mun,2);
										}

										$valor_antes[$id_mun][$id_fuente] += $valor_tmp;
									}
								}

								//muestra la columna antes de ....
								foreach ($fuentes as $f=>$id_fuente){

									if ($p == 0) $valor_total[0][$f] = 0;

									$num_antes_de = $valor_antes[$id_mun][$id_fuente]; 
									$valor_total[0][$f] += $num_antes_de;

									$html .="<td align='right'>".number_format($num_antes_de)."</td>";
									$xls .= "<td>".$num_antes_de."</td>";
								}

								$per = 1;
								foreach ($periodo_display as $a){
									foreach ($fuentes as $f=>$id_fuente){

										if ($p == 0) $valor_total[$per][$f] = 0;

										if ($a == $periodo_display[0]){
											$valor[$id_mun][$id_fuente] = $valor_antes[$id_mun][$id_fuente];
										}

										//eje x = años
										if ($ejex == 'aaaa'){
											$valor_tmp = $this->GetValorToReportTotalAAAA($exp,$id_fuente,0,$a,$id_mun,2);
										}
										else{
											$valor_tmp = $this->GetValorToReport($exp,$id_fuente,0,$a,$id_mun,2);
										}

										$valor[$id_mun][$id_fuente] += $valor_tmp;
										$valor_total[$per][$f] += $valor[$id_mun][$id_fuente];

										$html .="<td align='right'>".number_format($valor[$id_mun][$id_fuente])."</td>";
										$xls .= "<td align='right'>".$valor[$id_mun][$id_fuente]."</td>";
									}
									$per++;
								}
								$html .="</tr>";
								$xls .= "</tr>";
							}

							//Total
							$html .= "<tr class='titulo_lista'><td></td><td>Total</td>";
							$xls .= "<tr><td></td><td>Total</td>";

							foreach ($valor_total as $total_arr){
								foreach ($total_arr as $valor){
									$valor_xls = $valor;
									$valor = number_format($valor);

									$html .= "<td align='right'>$valor</td>";
									$xls .= "<td align='right'>$valor</td>";
								}
							}	
							$html .= "</tr>";
							$xls .= "</tr>";

						}


					}
					//DEPTO
					if ($depto == 1){
						$id_muns = $municipio_dao->GetAllArrayID('ID_DEPTO='.$ubicacion,'');
						foreach ($id_muns as $p=>$id_mun){
							$mun_vo = $municipio_dao->Get($id_mun);

							$style = "";
							if (fmod($p+1,2) == 0)  $style = "fila_lista";

							$html .="<tr class='$style'><td>".$mun_vo->id."</td>";
							$html .="<td>".$mun_vo->nombre."</td>";

							$xls .= "<tr><td class='excel_celda_texto'>".$mun_vo->id."</td>";
							$xls .= "<td>".$mun_vo->nombre."</td>";

							//calcula la columna antes de ....
							foreach ($periodo_before as $a){
								foreach ($fuentes as $id_fuente){

									if ($a == $periodo_before[0]){
										$valor_antes[$id_mun][$id_fuente] = $this->GetValorToReportTotalAAAA($exp,$id_fuente,0,$this->aaaa_sin_fecha,$id_mun,2);
									}
									//eje x = años
									if ($ejex == 'aaaa'){
										$valor_tmp = $this->GetValorToReportTotalAAAA($exp,$id_fuente,0,$a,$id_mun,2);
									}
									else{
										$valor_tmp = $this->GetValorToReport($exp,$id_fuente,0,$a,$id_mun,2);
									}

									$valor_antes[$id_mun][$id_fuente] += $valor_tmp;
								}
							}

							//muestra la columna antes de ....
							foreach ($fuentes as $f=>$id_fuente){

								if ($p == 0) $valor_total[0][$f] = 0;

								$num_antes_de = $valor_antes[$id_mun][$id_fuente];
								$valor_total[0][$f] += $num_antes_de; 

								$html .="<td align='right'>".number_format($num_antes_de)."</td>";
								$xls .= "<td>".$num_antes_de."</td>";
							}

							$per = 1;
							foreach ($periodo_display as $a){
								foreach ($fuentes as $f=>$id_fuente){

									if ($p == 0) $valor_total[$per][$f] = 0;

									if ($a == $periodo_display[0]){
										$valor[$id_mun][$id_fuente] = $valor_antes[$id_mun][$id_fuente];
									}

									//eje x = años
									if ($ejex == 'aaaa'){
										$valor_tmp = $this->GetValorToReportTotalAAAA($exp,$id_fuente,0,$a,$id_mun,2);
									}
									else{
										$valor_tmp = $this->GetValorToReport($exp,$id_fuente,0,$a,$id_mun,2);
									}

									$valor[$id_mun][$id_fuente] += $valor_tmp;
									$valor_total[$per][$f] += $valor[$id_mun][$id_fuente];

									$html .="<td align='right'>".number_format($valor[$id_mun][$id_fuente])."</td>";
									$xls .= "<td align='right'>".$valor[$id_mun][$id_fuente]."</td>";
								}
								$per++;
							}

							$html .="</tr>";
							$xls .= "</tr>";
						}

						//Total
						$html .= "<tr class='titulo_lista'><td></td><td>Total</td>";
						$xls .= "<tr><td></td><td>Total</td>";

						foreach ($valor_total as $total_arr){
							foreach ($total_arr as $valor){
								$valor_xls = $valor;
								$valor = number_format($valor);

								$html .= "<td align='right'>$valor</td>";
								$xls .= "<td align='right'>$valor</td>";
							}
						}	
						$html .= "</tr>";
						$xls .= "</tr>";
					}
					//MPIO
					if ($depto == 0){

						$id_mun = $ubicacion;
						$mun_vo = $municipio_dao->Get($id_mun);

						$html .="<tr><td>".$mun_vo->id."</td>";
						$html .="<td>".$mun_vo->nombre."</td>";

						$xls .= "<tr><td class='excel_celda_texto'>".$mun_vo->id."</td>";
						$xls .= "<td>".$mun_vo->nombre."</td>";

						//muestra la columna antes de ....
						foreach ($fuentes as $id_fuente){
							$num_antes_de = $this->GetValorToReportTotalAAAA($exp,$id_fuente,0,$this->aaaa_sin_fecha,$id_mun,2);

							$html .="<td align='right'>".number_format($num_antes_de)."</td>";
							$xls .= "<td>".number_format($num_antes_de,0,"","")."</td>";
						}

						foreach ($periodo as $a){

							$f = 0;
							foreach ($fuentes as $id_fuente){

								if ($a == $periodo[0]){
									$valor[$id_mun][$id_fuente] = $this->GetValorToReportTotalAAAA($exp,$id_fuente,0,$this->aaaa_sin_fecha,$id_mun,2);
								}

								//eje x = años
								if ($ejex == 'aaaa'){
									$valor_tmp = $this->GetValorToReportTotalAAAA($exp,$id_fuente,0,$a,$id_mun,2);
								}
								else{
									$valor_tmp = $this->GetValorToReport($exp,$id_fuente,0,$a,$id_mun,2);
								}

								$valor[$id_mun][$id_fuente] += $valor_tmp;

								if (in_array($a,$periodo_display)){
									$html .="<td align='right'>".number_format($valor[$id_mun][$id_fuente])."</td>";
									$xls .= "<td align='right'>".number_format($valor[$id_mun][$id_fuente],0,"","")."</td>";
								}

								$f++;
							}
						}

						$html .="</tr>";
						$xls .= "</tr>";
					}

					$html .="</table>";
					$xls .= "</table>";

					break;
				case 2:
					$html .="<tr class='titulo_lista'><td></td><td></td>";
					$xls .="<tr><td></td><td></td>";

					foreach ($periodo_display as $a){
						if ($ejex == 'aaaa'){
							$per = $a;
						}
						else{
							$periodo_vo = $periodo_dao->Get($a);	
							$per = $periodo_vo->nombre;
						}
						$html .="<td colspan='$num_fuentes' align='center'>$per</td>";
						$xls .= "<td colspan='$num_fuentes' align='center'>$per</td>";
					}

					$html .="</tr>";
					$xls .= "</tr>";

					$html .="<tr class='titulo_lista'><td></td><td></td>";
					$xls .= "<tr><td></td><td></td>";

					foreach ($periodo_display as $a){

						foreach ($fuentes as $id_fuente){
							$fuente = $fuente_dao->Get($id_fuente);
							$html .="<td align='center'><b>".$fuente->nombre."</b></td>";
							$xls .= "<td align='center'><b>".$fuente->nombre."</b></td>";

						}
					}
					$html .="</tr>";
					$xls .= "</tr>";

					//NACIONAL
					if ($depto == 2){
						if ($tipo_nal == 'deptos'){

							foreach ($id_deptos as $p=>$id_depto){
								$depto_vo = $depto_dao->Get($id_depto);

								$style = "";
								if (fmod($p+1,2) == 0)  $style = "fila_lista";

								$html .="<tr class='$style'><td>".$id_depto."</td>";
								$html .="<td>".$depto_vo->nombre."</td>";

								$xls .= "<tr><td class='excel_celda_texto'>".$id_depto."</td>";
								$xls .= "<td>".$depto_vo->nombre."</td>";

								foreach ($periodo as $per=>$a){	
									foreach ($fuentes as $f=>$id_fuente){
										if ($p == 0) $valor_total[$per][$f] = 0;
										//eje x = años
										if ($ejex == 'aaaa'){
											$valor_tmp = $this->GetValorToReportTotalAAAA($exp,$id_fuente,0,$a,$id_depto,1);
										}
										else{
											$valor_tmp = $this->GetValorToReport($exp,$id_fuente,0,$a,$id_depto,1);
										}

										if (!is_null($valor_tmp)){
											$valor_total[$per][$f] += $valor_tmp;
											$valor_xls = $valor_tmp;
											$valor = number_format($valor_tmp);
										}
										else{
											$valor = 'N.D';
											$valor_xls = '';
										}

										$html .="<td align='right'>$valor</td>";
										$xls .= "<td align='right'>$valor_xls</td>";
									}
								}

								$html .="</tr>";
								$xls .= "</tr>";
							}

							//Total
							$html .= "<tr class='titulo_lista'><td></td><td>Total</td>";
							$xls .= "<tr><td></td><td>Total</td>";

							foreach ($valor_total as $total_arr){
								foreach ($total_arr as $valor){
									$valor_xls = $valor;
									$valor = number_format($valor);

									$html .= "<td align='right'>$valor</td>";
									$xls .= "<td align='right'>$valor</td>";
								}
							}	
							$html .= "</tr>";
							$xls .= "</tr>";
						}
						else{
							$muns = $municipio_dao->GetAllArrayID("",'id_mun');
							//Agrega el 00000. que si esta en la tabla registro
							array_push($muns,'00000');

							foreach($muns as $p=>$id_mun){
								$mun_vo = $municipio_dao->Get($id_mun);

								if ($id_mun != '00000')	$mun_vo = $municipio_dao->Get($id_mun);
								else					$mun_vo->nombre = 'Nacional';

								$style = "";
								if (fmod($p+1,2) == 0)  $style = "fila_lista";

								$html .="<tr class='$style'><td>".$id_mun."</td>";
								$html .="<td>".$mun_vo->nombre."</td>";

								$xls .= "<tr><td class='excel_celda_texto'>".$id_mun."</td>";
								$xls .= "<td>".$mun_vo->nombre."</td>";

								foreach ($periodo as $per=>$a){
									foreach ($fuentes as $f=>$id_fuente){
										if ($p == 0) $valor_total[$per][$f] = 0;

										//eje x = años
										if ($ejex == 'aaaa'){
											$valor_tmp = $this->GetValorToReportTotalAAAA($exp,$id_fuente,0,$a,$id_mun,2);
										}
										else{
											$valor_tmp = $this->GetValorToReport($exp,$id_fuente,0,$a,$id_mun,2);
										}

										if (!is_null($valor_tmp)){
											$valor_total[$per][$f] += $valor_tmp;
											$valor_xls = $valor_tmp;
											$valor = number_format($valor_tmp);

										}
										else{
											$valor = 'N.D';
											$valor_xls = '';
										}

										$html .="<td align='right'>$valor</td>";
										$xls .= "<td align='right'>$valor_xls</td>";

									}
								}

								$html .="</tr>";
								$xls .= "</tr>";	
							}

							//Total
							$html .= "<tr class='titulo_lista'><td></td><td>Total</td>";
							$xls .= "<tr><td></td><td>Total</td>";

							foreach ($valor_total as $total_arr){
								foreach ($total_arr as $valor){
									$valor_xls = $valor;
									$valor = number_format($valor);

									$html .= "<td align='right'>$valor</td>";
									$xls .= "<td align='right'>$valor</td>";
								}
							}	
							$html .= "</tr>";
							$xls .= "</tr>";							
						}
					}
					//DEPTO
					if ($depto == 1){
						$id_muns = $municipio_dao->GetAllArrayID('ID_DEPTO='.$ubicacion,'');
						$p = 0;
						foreach ($id_muns as $p=>$id_mun){
							$mun_vo = $municipio_dao->Get($id_mun);

							$style = "";
							if (fmod($p+1,2) == 0)  $style = "fila_lista";

							$html .="<tr class='$style'><td>".$id_mun."</td>";
							$html .="<td>".$mun_vo->nombre."</td>";

							$xls .= "<tr><td class='excel_celda_texto'>".$id_mun."</td>";
							$xls .= "<td>".$mun_vo->nombre."</td>";

							foreach ($periodo as $per=>$a){	
								foreach ($fuentes as $f=>$id_fuente){
									if ($p == 0) $valor_total[$per][$f] = 0;

									//eje x = años
									if ($ejex == 'aaaa'){
										$valor_tmp = $this->GetValorToReportTotalAAAA($exp,$id_fuente,0,$a,$id_mun,2);
									}
									else{
										$valor_tmp = $this->GetValorToReport($exp,$id_fuente,0,$a,$id_mun,2);
									}

									if (!is_null($valor_tmp)){
										$valor_total[$per][$f] += $valor_tmp;
										$valor = number_format($valor_tmp);
										$valor_xls = $valor_tmp;
									}
									else{
										$valor = 'N.D';
										$valor_xls = '';
									}

									$html .="<td align='right'>$valor</td>";
									$xls .= "<td align='right'>$valor_xls</td>";
								}
							}
							$p++;

							$html .="</tr>";
							$xls .= "</tr>";
						}

						//Total
						$html .= "<tr class='titulo_lista'><td></td><td>Total</td>";
						$xls .= "<tr><td></td><td>Total</td>";

						foreach ($valor_total as $total_arr){
							foreach ($total_arr as $valor){
								$valor_xls = $valor;
								$valor = number_format($valor);

								$html .= "<td align='right'>$valor</td>";
								$xls .= "<td align='right'>$valor</td>";
							}
						}	
						$html .= "</tr>";
						$xls .= "</tr>";

					}
					//MPIO
					if ($depto == 0){

						$id_mun = $ubicacion;
						$mun_vo = $municipio_dao->Get($id_mun);

						$style = "";
						$html .="<tr><td class='$style'>".$mun_vo->nombre."</td>";
						$xls .= "<tr><td class='excel_celda_texto'>$id_mun</td>><td>".$mun_vo->nombre."</td>";

						foreach ($periodo as $a){
							foreach ($fuentes as $id_fuente){

								//eje x = años
								if ($ejex == 'aaaa'){
									$valor_tmp = $this->GetValorToReportTotalAAAA($exp,$id_fuente,0,$a,$id_mun,2);
								}
								else{
									$valor_tmp = $this->GetValorToReport($exp,$id_fuente,0,$a,$id_mun,2);
								}

								if (!is_null($valor_tmp)){
									$valor = number_format($valor_tmp);
									$valor_xls = number_format($valor_tmp,0,"","");
								}
								else{
									$valor = 'N.D';
									$valor_xls = '';
								}

								$html .="<td align='right'>$valor</td>";
								$xls .= "<td align='right'>$valor_xls</td>";

							}
						}

						$html .="</tr>";
						$xls .= "</tr>";
					}

					$html .="</table>";
					$xls .= "</table>";

					break;

				case 3:

					//Para el reporte 3, solo se selecciona 1 fuente
					$id_fuente = $fuentes[0];

					$html .="<tr class='titulo_lista'><td></td><td></td>";
					$xls .="<tr><td></td><td></td>";

					foreach ($periodo_display as $a){
						if ($ejex == 'aaaa'){
							$per = $a;
						}
						else{
							$periodo_vo = $periodo_dao->Get($a);	
							$per = $periodo_vo->nombre;
						}
						$html .="<td colspan='$num_tipos' align='center'>$per</td>";
						$xls .= "<td colspan='$num_tipos' align='center'>$per</td>";
					}
					$html .="</tr>";
					$xls .= "</tr>";

					$html .="<tr class='titulo_lista'><td></td><td></td>";
					$xls .= "<tr class='titulo_lista'><td></td><td></td>";

					foreach ($periodo_display as $a){	

						foreach ($tipos as $vo){
							$html .="<td align='center'><b>".$vo->nombre."</b></td>";
							$xls .= "<td align='center'><b>".$vo->nombre."</b></td>";

						}
					}
					$html .="</tr>";
					$xls .= "</tr>";

					//NACIONAL
					if ($depto == 2){

						if ($tipo_nal == 'deptos'){
							foreach ($id_deptos as $p=>$id_depto){
								$depto_vo = $depto_dao->Get($id_depto);

								$style = "";
								if (fmod($p+1,2) == 0)  $style = "fila_lista";

								$html .="<tr class='$style'><td>".$id_depto."</td>";
								$html .="<td>".$depto_vo->nombre."</td>";

								$xls .= "<tr><td class='excel_celda_texto'>".$id_depto."</td>";
								$xls .= "<td>".$depto_vo->nombre."</td>";

								foreach ($periodo as $per=>$a){
									foreach ($tipos as $f=>$tipo){

										if ($p == 0) $valor_total[$per][$f] = 0;

										//eje x = años
										if ($ejex == 'aaaa'){
											$valor_tmp = $this->GetValorToReportTotalAAAA($exp,$id_fuente,$tipo->id,$a,$id_depto,1);
										}
										else{
											$valor_tmp = $this->GetValorToReport($exp,$id_fuente,$tipo->id,$a,$id_depto,1);
										}

										if (!is_null($valor_tmp)){
											$valor_total[$per][$f] += $valor_tmp;
											$valor = number_format($valor_tmp);
											$valor_xls = $valor_tmp;
										}
										else{
											$valor = 'N.D';
											$valor_xls = '';
										}

										$html .="<td align='right'>$valor</td>";
										$xls .= "<td align='right'>$valor_xls</td>";

									}
								}
								$html .="</tr>";
								$xls .= "</tr>";
							}

							//Total
							$html .= "<tr class='titulo_lista'><td></td><td>Total</td>";
							$xls .= "<tr><td></td><td>Total</td>";

							foreach ($valor_total as $total_arr){
								foreach ($total_arr as $valor){
									$valor_xls = $valor;
									$valor = number_format($valor);

									$html .= "<td align='right'>$valor</td>";
									$xls .= "<td align='right'>$valor_xls</td>";
								}
							}	
							$html .= "</tr>";
							$xls .= "</tr>";							
						}
						else{
							$muns = $municipio_dao->GetAllArrayID("",'id_mun');
							//Agrega el 00000. que si esta en la tabla registro
							array_push($muns,'00000');

							foreach($muns as $p=>$id_mun){
								$mun_vo = $municipio_dao->Get($id_mun);

								if ($id_mun != '00000')	$mun_vo = $municipio_dao->Get($id_mun);
								else					$mun_vo->nombre = 'Nacional';

								$style = "";
								if (fmod($p+1,2) == 0)  $style = "fila_lista";

								$html .="<tr class='$style'><td>".$id_mun."</td>";
								$html .="<td>".$mun_vo->nombre."</td>";

								$xls .= "<tr><td class='excel_celda_texto'>".$id_mun."</td>";
								$xls .= "<td>".$mun_vo->nombre."</td>";

								foreach ($periodo as $per=>$a){
									foreach ($tipos as $f=>$tipo){

										if ($p == 0) $valor_total[$per][$f] = 0;

										//eje x = años
										if ($ejex == 'aaaa'){
											$valor_tmp = $this->GetValorToReportTotalAAAA($exp,$id_fuente,$tipo->id,$a,$id_mun,2);
										}
										else{
											$valor_tmp = $this->GetValorToReport($exp,$id_fuente,$tipo->id,$a,$id_mun,2);
										}

										if (!is_null($valor_tmp)){
											$valor_total[$per][$f] += $valor_tmp;
											$valor = number_format($valor_tmp);
											$valor_xls = $valor_tmp;
										}
										else{
											$valor = 'N.D';
											$valor_xls = '';
										}

										$html .="<td align='right'>$valor</td>";
										$xls .= "<td align='right'>$valor_xls</td>";

									}
								}
								$html .="</tr>";
								$xls .= "</tr>";
							}

							//Total
							$html .= "<tr class='titulo_lista'><td></td><td>Total</td>";
							$xls .= "<tr><td></td><td>Total</td>";

							foreach ($valor_total as $total_arr){
								foreach ($total_arr as $valor){
									$valor_xls = $valor;
									$valor = number_format($valor);

									$html .= "<td align='right'>$valor</td>";
									$xls .= "<td align='right'>$valor_xls</td>";
								}
							}	
							$html .= "</tr>";
							$xls .= "</tr>";							
						}

					}
					//DEPTO
					if ($depto == 1){
						$id_muns = $municipio_dao->GetAllArrayID('ID_DEPTO='.$ubicacion,'');
						foreach ($id_muns as $p=>$id_mun){
							$mun_vo = $municipio_dao->Get($id_mun);

							$style = "";
							if (fmod($p+1,2) == 0)  $style = "fila_lista";

							$html .="<tr class='$style'><td>".$id_mun."</td>";
							$html .="<td>".$mun_vo->nombre."</td>";

							$xls .= "<tr><td class='excel_celda_texto'>".$id_mun."</td>";
							$xls .= "<td>".$mun_vo->nombre."</td>";

							foreach ($periodo as $per=>$a){
								foreach ($tipos as $f=>$tipo){

									if ($p == 0) $valor_total[$per][$f] = 0;

									//eje x = años
									if ($ejex == 'aaaa'){
										$valor_tmp = $this->GetValorToReportTotalAAAA($exp,$id_fuente,$tipo->id,$a,$id_mun,2);
									}
									else{
										$valor_tmp = $this->GetValorToReport($exp,$id_fuente,$tipo->id,$a,$id_mun,2);
									}

									if (!is_null($valor_tmp)){
										$valor_total[$per][$f] += $valor_tmp;
										$valor = number_format($valor_tmp);
										$valor_xls = $valor_tmp;
									}
									else{
										$valor = 'N.D';
										$valor_xls = '';
									}

									$html .="<td align='right'>$valor</td>";
									$xls .= "<td align='right'>$valor_xls</td>";

								}
							}

							$html .="</tr>";
							$xls .= "</tr>";
						}

						//Total
						$html .= "<tr class='titulo_lista'><td></td><td>Total</td>";
						$xls .= "<tr><td></td><td>Total</td>";

						foreach ($valor_total as $total_arr){
							foreach ($total_arr as $valor){
								$valor_xls = $valor;
								$valor = number_format($valor);

								$html .= "<td align='right'>$valor</td>";
								$xls .= "<td align='right'>$valor_xls</td>";
							}
						}	
						$html .= "</tr>";
						$xls .= "</tr>";
					}
					//MPIO
					if ($depto == 0){

						$id_mun = $ubicacion;
						$mun_vo = $municipio_dao->Get($id_mun);

						$style = "";
						$html .="<tr class='$style'><td>".$mun_vo->nombre."</td>";
						$xls .= "<tr><td class='excel_celda_texto'>$id_mun</td><td>".$mun_vo->nombre."</td>";

						foreach ($periodo as $a){
							foreach ($tipos as $tipo){

								//eje x = años
								if ($ejex == 'aaaa'){
									$valor_tmp = $this->GetValorToReportTotalAAAA($exp,$id_fuente,$tipo->id,$a,$id_mun,2);
								}
								else{
									$valor_tmp = $this->GetValorToReport($exp,$id_fuente,$tipo->id,$a,$id_mun,2);
								}

								if (!is_null($valor_tmp)){
									$valor = number_format($valor_tmp);
									$valor_xls = number_format($valor_tmp,0,"","");
								}
								else{
									$valor = 'N.D';
									$valor_xls = '';
								}

								$html .="<td align='right'>$valor</td>";
								$xls .= "<td align='right'>$valor_xls</td>";

							}
						}

						$html .="</tr>";
						$xls .= "</tr>";
					}

					$html .="</table>";
					$xls .= "</table>";

					break;

			}

			$html .="</table>";

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

	/**
	 * Realiza el check de los datos a importar
	 * @access public
	 * @param file $periodos String separados por | de los ID de los periodos
	 */
	function PreCargaCSV($periodos){

		//LIBRERIAS
		require_once "lib/dao/periodo.class.php";
		require_once "lib/model/periodo.class.php";

		$periodo_dao = New PeriodoDAO();
		$periodos = explode("|",$periodos);
		echo "<table cellpadding=5 cellspacing=1 height=500><tr>";
		echo "<td>MPIO</td>";
		foreach ($periodos as $per){
			$periodo = $periodo_dao->Get($per);
			echo "<td>$periodo->nombre</td>";
		}
		echo "</tr></table>";
	}

}
?>
