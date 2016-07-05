<?
/**
 * DAO de Evento
 *
 * Contiene los métodos de la clase Evento 
 * @author Ruben A. Rojas C.
 */

Class EventoDAO {

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
	function EventoDAO (){
		$this->conn = MysqlDb::getInstance();
		$this->tabla = "evento";
		$this->columna_id = "ID_EVENTO";
		$this->columna_nombre = "DESC_EVENTO";
		$this->columna_order = "FECHA_REGISTRO";
		$this->num_reg_pag = 50;
		$this->url = "index.php?accion=listar&class=EventoDAO&method=ListarTabla&param=";
	}

	/**
  * Consulta los datos de una Evento
  * @access public
  * @param int $id ID del Evento
  * @return VO
  */	
	function Get($id){
		$sql = "SELECT * FROM ".$this->tabla." WHERE ".$this->columna_id." = ".$id;
		$rs = $this->conn->OpenRecordset($sql);
		$row_rs = $this->conn->FetchObject($rs);

		//Crea un VO
		$depto_vo = New Evento();

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
		$sql = "SELECT max(ID_EVENTO) as maxid FROM ".$this->tabla;
		$rs = $this->conn->OpenRecordset($sql);
		if($row_rs = $this->conn->FetchRow($rs)){
			return $row_rs[0];
		}
		else{
			return 0;
		}
	}

	/**
	 * Consulta Vos
	 * @access public
	 * @param string $condicion Condición que deben cumplir los registros y que se agrega en el SQL statement.
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
			$vo = New Evento();
			//Carga el VO
			$vo = $this->GetFromResult($vo,$row_rs);
			//Carga el arreglo
			$array[] = $vo;
		}  
		//Retorna el Arreglo de VO
		return $array;
	}

	/**
  * Lista los Evento que cumplen la condición en el formato dado
  * @access public
  * @param string $formato Formato en el que se listarán los Evento, puede ser Tabla o ComboSelect
	* @param int $valor_combo ID del Evento que será selccionado cuando el formato es ComboSelect
  * @param string $condicion Condición que deben cumplir los Evento y que se agrega en el SQL statement.
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
  * Lista los TipoEvento en una Tabla
  * @access public
  */			
	function ListarTabla($condicion){
		
		$arr = $this->GetAllArray($condicion,'','fecha_registro DESC');
		$num_arr = count($arr);

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
		if (isset($_POST["f_ini"])){
			$f_ini = $_POST['f_ini'];
		}
		else if (isset($_GET["f_ini"])){
			$f_ini = $_GET['f_ini'];
		}

		////FECHA FINAL
		if (isset($_POST["f_fin"])){
			$f_fin = $_POST['f_fin'];
		}
		else if (isset($_GET["f_fin"])){
			$f_fin = $_GET['f_fin'];
		}


		echo "<table width='95%' align='center' cellspacing='1' cellpadding='3'>
    			<tr><td>&nbsp;</td></tr>
          <tr class='titulo_lista'>
        	  <td width='500'>Evento</td>
        		<td align='center' width='100'>Fecha Reg.</td>
				<td align='center' width='100'>".$num_arr." Registros</td>
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

			echo "<td><div align='justify'>".$arr[$p]->desc."</div></td>";
			echo "<td align='center'>".$arr[$p]->fecha_registro."</td>";
			echo "<td align='center'><a href='".$_SERVER['PHP_SELF']."?accion=actualizar&id=".$arr[$p]->id."'>Modificar</a> | <a href='index.php?accion=borrar&class=".$class."&method=Borrar&param=".$arr[$p]->id."' onclick=\"return confirm('Está seguro que desea borrar el Evento?');\">Borrar</a></td>";

			echo "</tr>";
		}

		echo "<tr><td>&nbsp;</td></tr>";
		//PAGINACION
		if ($num_arr > $this->num_reg_pag){

			$num_pages = ceil($num_arr/$this->num_reg_pag);
			echo "<tr><td colspan='2' align='center'>";

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
  * Reporte Diario en formato HTML
	* @param $fecha string Fecha de consulta para el reporte diario
  * @access public
  */			
	function ReporteDiarioHTML($fecha){
		$cat_dao = New CatTipoEventoDAO();
		$cats = $cat_dao->GetAllArray('');
		$depto_dao = New DeptoDAO();
		$mun_dao = New MunicipioDAO();
		$tipo_evento_dao = New TipoEventoDAO();
		$actor_dao = New ActorDAO();
		$cons_hum_dao = New ConsHumDAO();
		$riesgo_hum_dao = New RiesgoHumDAO();

		$fecha_tmp = split("-",$fecha);
		$fecha_tit = date("M d Y", mktime(0, 0, 0, $fecha_tmp[1], $fecha_tmp[2], $fecha_tmp[0]));

		echo "<table width='100%' align='center' cellspacing='1' cellpadding='3'>
					<tr><td>&nbsp;</td><td align='center'><b>Informe Diario</b> - <b>".$fecha_tit."</b></td></tr>
					<tr><td width='12%'><img src='/sissh/images/consulta/generar_pdf.gif' border='0'>&nbsp;<a href='/sissh/admin/reporte_pdf.php?class=EventoDAO&method=ReporteDiarioPDF&param=".$fecha."' target='blank'>Generar PDF</a></td><td align='center' class='titulo_lista'><b>USO INTERNO</b></td></tr>
					<tr><td>&nbsp;</td><td align='center'><b>La información contenida no refleja la opinión del Sistema de las Naciones Unidas</b></td></tr>
					<tr><td>&nbsp;</td><td>&nbsp;</td></tr></table>";

		////SE MUESTRAN LOS EVENTOS POR CATEGORIA
		foreach ($cats as $cat_vo){
			$where = "FECHA_REGISTRO = '".$fecha."' AND ID_CAT=".$cat_vo->id;
			$arr = $this->GetAllArray($where);
			$num_arr = count($arr);

			////TITULO DE LA CATEGORIA
			if ($num_arr > 0){
				echo "<table align='center' cellspacing='1' cellpadding='3'>";
				echo "<tr><td><b>".$cat_vo->nombre."</b></td></tr>";
				echo "</table>";

				echo "<table align='center' cellspacing='1' cellpadding='3' class='tabla_reporte'>
    					<tr class='titulo_lista'>
  						  <td align='center' width='70'><b>Departamento</b></td>
  							<td align='center' width='70'><b>Municipio</b></td>
  							<td align='center' width='100'><b>Lugar</b></td>
  							<td align='center' width='100'><b>Tipo de Evento</b></td>
  							<td align='center' width='10'><b>Actores</b></td>
  							<td align='center' width='100'><b>Consecuencias Humanitarias</b></td>
  							<td align='center' width='70'><b>Riesgos Humanitarios</b></td>
  							<td align='center' width='200'><b>Descripción</b></td>
  							<td align='center' width='70'><b>Fecha registro</b></td>
  						</tr>";

				foreach($arr as $arr_vo){
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

					////LUGAR
					echo "<td>".$arr_vo->lugar."</td>";

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

					////CONS. HUM
					echo "<td>";
					$z=0;
					foreach($arr_vo->id_cons as $id){
						$vo = $cons_hum_dao->Get($id);
						if ($z==0)  echo $vo->nombre;
						else				echo ", ".$vo->nombre;

						$z++;
					}
					////Descripción de las consecuencias
					if ($arr_vo->desc_cons_hum != "")
					echo " - ".$arr_vo->desc_cons_hum;

					echo "</td>";

					////RIESG. HUM
					echo "<td>";
					$z=0;
					foreach($arr_vo->id_riesgos as $id){
						$vo = $riesgo_hum_dao->Get($id);
						if ($z==0)  echo $vo->nombre;
						else				echo ", ".$vo->nombre;

						$z++;
					}
					////Descripción de los riegos
					if ($arr_vo->desc_riesg_hum != "")
					echo " - ".$arr_vo->desc_riesg_hum;

					echo "</td>";

					////DESCRIPCION
					echo "<td><div align='justify'>".$arr_vo->desc."</div></td>";

					////FECHA DE REGISTRO
					echo "<td>".$arr_vo->fecha_registro."</td>";

					echo "</tr>";
				}
				echo "</table><br>";
			}
		}
	}

	/**
  * Reporte Diario en PDF
	* @param Array $text Arreglo con los datos que se muestran en la tabla del informe
  * @access public
  */			
	function ReporteDiarioPDF($fecha){
		include_once 'lib/common/class.ezpdf.php';

		$pdf = new Cezpdf();
		$pdf->selectFont('lib/common/PDFfonts/Helvetica.afm');
		$options = Array('showLines' => 0,'width' => 750, 'cols'=>array('title' => array('justification'=>'center')));

		$cat_dao = New CatTipoEventoDAO();
		$cats = $cat_dao->GetAllArray('');
		$depto_dao = New DeptoDAO();
		$mun_dao = New MunicipioDAO();
		$tipo_evento_dao = New TipoEventoDAO();
		$actor_dao = New ActorDAO();
		$cons_hum_dao = New ConsHumDAO();
		$riesgo_hum_dao = New RiesgoHumDAO();

		$fecha_tmp = split("-",$fecha);
		$fecha_tit = date("M d Y", mktime(0, 0, 0, $fecha_tmp[1], $fecha_tmp[2], $fecha_tmp[0]));

		$data = Array(
		Array('title'=>'<b>Informe Diario</b> - <b>'.$fecha_tit.'</b>'),
		Array('title'=>'<b>USO INTERNO</b>'),
		Array('title'=>'<b>La información contenida no refleja la opinión del Sistema de las Naciones Unidas</b>')
		);

		$pdf->ezTable($data,Array('title'=>''),'',$options);

		////SE MUESTRAN LOS EVENTOS POR CATEGORIA
		foreach ($cats as $cat_vo){
			$where = "FECHA_REGISTRO = '".$fecha."' AND ID_CAT=".$cat_vo->id;
			$arr = $this->GetAllArray($where);
			$num_arr = count($arr);

			////TITULO DE LA CATEGORIA
			if ($num_arr > 0){
				$data = Array(Array('title'=>'<b>'.$cat_vo->nombre.'</b>'));
				$options = Array('showLines' => 0,'width' => 750, 'cols'=>array('title' => array('justification'=>'center')));
				$pdf->ezTable($data,Array('title'=>''),'',$options);

				$title = Array('depto' => '<b>Departamento</b>',
				'mun'   => '<b>Municipio</b>',
				'lugar'   => '<b>Lugar</b>',
				't_evento'   => '<b>Tipo de Evento</b>',
				'actor'   => '<b>Actores</b>',
				'cons'   => '<b>Consecuencias Humanitarias</b>',
				'riesg'   => '<b>Riesgos Humanitarios</b>',
				'desc'   => '<b>Descripción</b>',
				'fecha'   => '<b>Fecha registro</b>');

				$f = 0;
				foreach($arr as $arr_vo){

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
						$vo = $tipo_evento_dao->Get($id);
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
			$options = Array('showLines' => 2, 'shaded' => 0, 'width' => 750, 'fontSize'=>8, 'cols'=>array('desc'=>array('width'=>200,'justification'=>'full'),'lugar'=>array('width'=>80),'fecha'=>array('width'=>60)));
			$pdf->ezTable($data,$title,'',$options);
		}

		//MUESTRA EL PDF
		$pdf->ezStream();
	}

	/**
  * Reporte Diario en formato HTML
	* @param $fecha_ini string Fecha Inicial de consulta para el reporte diario
	* @param $fecha_fin string Fecha Final de consulta para el reporte diario
  * @access public
  */			
	function ReporteSemanalHTML($fecha_ini,$fecha_fin){

		$where = "FECHA_REGISTRO BETWEEN '".$fecha_ini."' AND '".$fecha_fin."'";

		$fecha_tmp_ini = split("-",$fecha_ini);
		$mes_ini_txt = date("F", mktime(0, 0, 0, $fecha_tmp_ini[1], $fecha_tmp_ini[2], $fecha_tmp_ini[0]));
		$fecha_tmp_fin = split("-",$fecha_fin);

		$id_depto_url = 0;
		//DEPARTAMENTO SELECCIONADO
		if (isset($_GET["id_depto"])){
			$id_depto_url = $_GET["id_depto"];

			//CONSULTA LOS MUNICIPIOS POR EVENTO POR CATEGORIA
			$sql_c_1 = "SELECT DISTINCT ID_MUN FROM mun_evento INNER JOIN evento ON evento.ID_EVENTO = mun_evento.ID_EVENTO WHERE ".$where." AND ID_CAT = 1";
			$rs_c_1 = $this->conn->OpenRecordset($sql_c_1);
			$c_1 = 0;
			while ($row_rs_c_1 = $this->conn->FetchRow($rs_c_1)){
				$muns_cat_1[$c_1] = $row_rs_c_1[0];
				$c_1++;
			}

			$sql_c_2 = "SELECT DISTINCT ID_MUN FROM mun_evento INNER JOIN evento ON evento.ID_EVENTO = mun_evento.ID_EVENTO WHERE ".$where." AND ID_CAT = 2";
			$rs_c_2 = $this->conn->OpenRecordset($sql_c_2);
			$c_2 = 0;
			while ($row_rs_c_2 = $this->conn->FetchRow($rs_c_2)){
				$muns_cat_2[$c_2] = $row_rs_c_2[0];
				$c_2++;
			}

			$muns_cat_3 = array_intersect($muns_cat_1,$muns_cat_2);


			//CONSULTA LOS MUNICIPIOS DEL DEPARTAMENTO SELECCIONADO QUE TIENEN EVENTOS
			$sql_m = "SELECT DISTINCT municipio.ID_MUN, municipio.NOM_MUN FROM municipio INNER JOIN mun_evento ON municipio.ID_MUN = mun_evento.ID_MUN INNER JOIN evento ON evento.ID_EVENTO = mun_evento.ID_EVENTO  WHERE ID_DEPTO = ".$id_depto_url." AND ".$where." ORDER BY NOM_MUN ASC";
			$rs_m = $this->conn->OpenRecordset($sql_m);
			$m = 0;
			$id_muns = Array();
			$nom_muns = Array();
			while ($row_rs_m = $this->conn->FetchRow($rs_m)){
				$id_muns[$m] = $row_rs_m[0];
				$nom_muns[$m] = $row_rs_m[1];

				$m++;
			}
		}

		//MUNICIPIO SELECCIONADO
		if (isset($_GET["id_mun"])){
			$id_mun_url = $_GET["id_mun"];

			$sql_e = "SELECT evento.ID_EVENTO FROM evento INNER JOIN mun_evento ON evento.ID_EVENTO = mun_evento.ID_EVENTO WHERE ".$where." AND ID_MUN = ".$id_mun_url;
			$rs_e = $this->conn->OpenRecordset($sql_e);
			$e = 0;
			while ($row_rs_e = $this->conn->FetchRow($rs_e)){
				$eventos[$e] = $row_rs_e[0];
				$e++;
			}

			//CONSULTA EL NOMBRE DEL MUNICIPIO
			$mun_dao = New MunicipioDAO();
			$tipo_dao = New TipoeventoDAO();
			$mun = $mun_dao->Get($id_mun_url);
			$nom_mun_url = $mun->nombre;

		}

		//CONSULTA LOS DEPARTAMENTOS POR EVENTO POR CATEGORIA
		$sql_c_1 = "SELECT DISTINCT ID_DEPTO FROM depto_evento INNER JOIN evento ON evento.ID_EVENTO = depto_evento.ID_EVENTO WHERE ".$where." AND ID_CAT = 1";
		$rs_c_1 = $this->conn->OpenRecordset($sql_c_1);
		$c_1 = 0;
		while ($row_rs_c_1 = $this->conn->FetchRow($rs_c_1)){
			$deptos_cat_1[$c_1] = $row_rs_c_1[0];
			$c_1++;
		}

		$sql_c_2 = "SELECT DISTINCT ID_DEPTO FROM depto_evento INNER JOIN evento ON evento.ID_EVENTO = depto_evento.ID_EVENTO WHERE ".$where." AND ID_CAT = 2";
		$rs_c_2 = $this->conn->OpenRecordset($sql_c_2);
		$c_2 = 0;
		while ($row_rs_c_2 = $this->conn->FetchRow($rs_c_2)){
			$deptos_cat_2[$c_2] = $row_rs_c_2[0];
			$c_2++;
		}

		$deptos_cat_3 = array_intersect($deptos_cat_1,$deptos_cat_2);

		//CONSULTA LOS DEPARTAMENTOS EN LOS QUE OCURRIERON EVENTOS
		$sql_d = "SELECT DISTINCT departamento.ID_DEPTO, departamento.NOM_DEPTO FROM departamento INNER JOIN depto_evento ON departamento.ID_DEPTO = depto_evento.ID_DEPTO INNER JOIN evento ON evento.ID_EVENTO = depto_evento.ID_EVENTO  WHERE ".$where." ORDER BY NOM_DEPTO ASC";
		$rs_d = $this->conn->OpenRecordset($sql_d);
		$d = 0;
		while ($row_rs_d = $this->conn->FetchRow($rs_d)){
			$id_deptos[$d] = $row_rs_d[0];
			$nom_deptos[$d] = $row_rs_d[1];
			$d++;
		}

		echo "<table border=0 width='100%'>
  		   <tr>
  			   <td valign='top' align='center' colspan='2'>
    						 <span class='titulo_reporte_semanal'>INFORME SEMANAL -
    						 ".$mes_ini_txt." ".$fecha_tmp_ini[2]." a ".$fecha_tmp_fin[2]." de 
    						 ".$fecha_tmp_ini[0]."
  					</td>
  				</tr>
			 </table>
			 <br><br>
			 <table border=0>	
  			 <tr>
  			   <td height='660' valign='top'>
					   <table height='100%' border=0> ";

		echo "<tr><td align='center'><img src='/sissh/images/consulta/generar_pdf.gif' border='0'>&nbsp;<a href='/sissh/admin/reporte_pdf.php?class=EventoDAO&method=ReporteSemanalPDF&param=&fecha_ini=".$fecha_ini."&fecha_fin=".$fecha_fin."' target='blank'>Generar PDF</a></td></tr>
								<tr>
								  <td valign='top' align='center'>
									  <table class='titulo_lista' cellpadding=10 width='300'>
										  <tr><td align='center'>Departamentos donde se presentaron eventos</td></tr>
											<tr bgcolor='#FFFFFF'>
											  <td align='center'>
												<select id='id_depto' name='id_depto' class='select'>
      									  <option value=''>Seleccione alguno</option>"; 

		$d = 0;
		foreach ($id_deptos as $id_d){
			echo "<option value='".$id_d."'";
			if ($id_d == $id_depto_url)   echo " selected ";
			echo ">".$nom_deptos[$d]."</option>";
			$d++;
		}

		echo "</select>
											&nbsp;<input type='button' value='Consultar' class='boton' onclick=\"if (document.getElementById('id_depto').value != ''){location.href='index.php?accion=consultar&class=EventoDAO&method=ReporteSemanal&f_ini=".$fecha_ini."&f_fin=".$fecha_fin."&id_depto='+document.getElementById('id_depto').value}\"></td></tr>";

		if (isset($_GET["id_depto"]) || isset($_GET["id_mun"])){
			//MAPA DEL DEPARTAMENTO CON LOS MUNICIPIOS RESALTADOS
			echo "<tr><td height='270' bgcolor='#FFFFFF'>";

			foreach ($id_muns as $id_mun){
				//CONSULTA EN QUE CATEGORIA ESTA EL EVENTO
				$id_cat_e = 1;
				if (in_array($id_mun,$muns_cat_2)){
					$id_cat_e = 2;
				}
				if (in_array($id_mun,$muns_cat_3)){
					$id_cat_e = 3;
				}
				echo "<div class='mapa_iz'>";
				echo "<img src='/sissh/images/mapas/depto/".$id_mun."_".$id_cat_e.".png'>";
				echo "</div>";
			}
			echo "</td></tr>";
		}


		//CONSULTA LOS MUNICIPIOS DEL DEPARTAMENTO QUE TIENEN EVENTOS
		if (isset($_GET["id_depto"])){

			if (count($id_muns) > 0){
				echo "<tr bgcolor='#FFFFFF'><td><table cellpadding='5' cellspacing='1'>";
			}
			for($m=0;$m<count($id_muns);$m++){
				echo "<tr class='fila_lista'><td><img src='/sissh/images/flecha.gif'>&nbsp;".$nom_muns[$m]."</td><td>[ <a href='index.php?accion=consultar&class=EventoDAO&method=ReporteSemanal&f_ini=".$fecha_ini."&f_fin=".$fecha_fin."&id_depto=".$id_depto_url."&id_mun=".$id_muns[$m]."'>Ver Eventos</a> ]</td></tr>";
			}
			if (count($id_muns) > 0){
				echo "</table></td></tr>";
			}
		}
		echo "</table>
									</td>
								</tr>
								<tr>
								  <td valign='bottom'>
									  <div align='justify'>
										  Naciones Unidas Colombia<br>
											La información contenida en este informe es para uso<br> 
											exclusivo del Sistema de las naciones Unidas, no esta <br>
											permitida su redistribución o publicación
											<br><br>
											La información contenida no refleja la opinión del <br>
											Sistema de Naciones Unidas.
										</div>
									</td>
								</tr>
							</table>
					 </td>
  			   <td valign='top' align='center'>";
		//MAPA INICIAL DE COLOMBIA CON LOS DEPARTAMENTOS RESALTADOS
		if (!isset($_GET["id_depto"]) && !isset($_GET["id_mun"])){
			$d = 0;
			foreach ($id_deptos as $id_depto){
				//DIFERENTE A NACIONAL
				if ($id_depto != '00'){
					//CONSULTA EN QUE CATEGORIA ESTA EL EVENTO
					$id_cat_e = 1;
					if (in_array($id_depto,$deptos_cat_2)){
						$id_cat_e = 2;
					}
					if (in_array($id_depto,$deptos_cat_3)){
						$id_cat_e = 3;
					}
					echo "<div class='mapa_centro'>";
					echo "<img src='../images/mapas/nac/".$id_depto."_".$id_cat_e.".png'>";
					echo "</div>";
				}
				$d++;
			}
		}
		else if (isset($_GET["id_mun"])){

			//CONSULTA EN QUE CATEGORIA ESTA EL EVENTO
			$id_cat_e = 1;
			if (in_array($id_mun_url,$muns_cat_2)){
				$id_cat_e = 2;
			}
			if (in_array($id_mun_url,$muns_cat_3)){
				$id_cat_e = 3;
			}

			echo "<table align='center' border=0 cellpadding='3' align='center'>";
			echo "<tr><td align='center' width='100'><img src='../images/mapas/mun/".$id_mun_url."_".$id_cat_e.".png'></td>";
			echo "<td align='left'><span class='titulo_reporte_semanal'>EVENTOS EN: ".$nom_mun_url."</span></td></tr>";
			echo "</table>";
			echo "<table align='center' width='100%' border=0 cellpadding='3'>";

			//LISTA LOS EVENTOS DEL MUNICIPIO
			echo "<tr><td>&nbsp;</td></tr>";
			echo "<tr class='titulo_lista'><td width='70'></td><td align='center'>Evento</td><td align='center'>Fecha</td></tr>";
			$d = 0;
			foreach ($eventos as $id_eve){
				$eve = $this->Get($id_eve);

				echo "<tr>";
				if (count($eve->id_tipo) >= 1){
					$tipo = $tipo_dao->Get($eve->id_tipo[0]);
					echo "<td><img src='../images/tipo_evento/".strtolower($tipo->nombre).".png'></td>";
				}
				else{
					echo "<td>&nbsp;</td>";
				}
				echo "<td align='center' width='85%'><div align='justify'>".$eve->desc."</div></td><td align='center'>".$eve->fecha_registro."</td></tr>";

			}
		}

		echo "</td>
  				 
  			 </tr>
  			 </table>";

	}

	/******************************************************************************
	* Reporte Diario en formato PDF
	* @param $fecha_ini string Fecha Inicial de consulta para el reporte diario
	* @param $fecha_fin string Fecha Final de consulta para el reporte diario
	* @access public
	*******************************************************************************/
	function ReporteSemanalPDF($fecha_ini,$fecha_fin){
		include_once 'lib/common/class.ezpdf.php';

		$pdf = new Cezpdf();
		$tipo_dao = New TipoeventoDAO();
		$mun_dao = New MunicipioDAO();

		$pdf -> ezSetMargins(50,50,50,50);
		$pdf->selectFont('lib/common/PDFfonts/Helvetica.afm');

		// Coloca el logo y el pie en todas las páginas
		$all = $pdf->openObject();
		$pdf->saveState();
		$pdf->ezImage('../images/logos/enc_reporte_semanal.jpg',0,65,'none','left');


		$pdf->addText(50,103,8,'Naciones Unidas Colombia');
		$pdf->addText(50,88,8,'La información contenida en este informe es para uso');
		$pdf->addText(50,81,8,'exclusivo del Sistema de las naciones Unidas, no esta');
		$pdf->addText(50,73,8,'permitida su redistribución o publicación.');
		$pdf->addText(50,58,8,'La información contenida no refleja la opinión del');
		$pdf->addText(50,50,8,'Sistema de Naciones Unidas.');

		$pdf->restoreState();
		$pdf->closeObject();
		$pdf->addObject($all,'all');

		$pdf->ezSetDy(0);

		$where = "FECHA_REGISTRO BETWEEN '".$fecha_ini."' AND '".$fecha_fin."'";

		$fecha_tmp_ini = split("-",$fecha_ini);
		$mes_ini_txt = date("F", mktime(0, 0, 0, $fecha_tmp_ini[1], $fecha_tmp_ini[2], $fecha_tmp_ini[0]));
		$fecha_tmp_fin = split("-",$fecha_fin);

		//CONSULTA LOS DEPARTAMENTOS EN LOS QUE OCURRIERON EVENTOS
		$sql_d = "SELECT DISTINCT departamento.ID_DEPTO, departamento.NOM_DEPTO FROM departamento INNER JOIN depto_evento ON departamento.ID_DEPTO = depto_evento.ID_DEPTO INNER JOIN evento ON evento.ID_EVENTO = depto_evento.ID_EVENTO  WHERE ".$where." ORDER BY NOM_DEPTO ASC";
		$rs_d = $this->conn->OpenRecordset($sql_d);
		$d = 0;
		while ($row_rs_d = $this->conn->FetchRow($rs_d)){
			$id_deptos[$d] = $row_rs_d[0];
			$nom_deptos[$d] = $row_rs_d[1];
			$d++;
		}

		//CONSULTA LOS MUNICIPIOS POR EVENTO POR CATEGORIA
		$sql_c_1 = "SELECT DISTINCT ID_MUN FROM mun_evento INNER JOIN evento ON evento.ID_EVENTO = mun_evento.ID_EVENTO WHERE ".$where." AND ID_CAT = 1";
		$rs_c_1 = $this->conn->OpenRecordset($sql_c_1);
		$c_1 = 0;
		while ($row_rs_c_1 = $this->conn->FetchRow($rs_c_1)){
			$muns_cat_1[$c_1] = $row_rs_c_1[0];
			$c_1++;
		}

		$sql_c_2 = "SELECT DISTINCT ID_MUN FROM mun_evento INNER JOIN evento ON evento.ID_EVENTO = mun_evento.ID_EVENTO WHERE ".$where." AND ID_CAT = 2";
		$rs_c_2 = $this->conn->OpenRecordset($sql_c_2);
		$c_2 = 0;
		while ($row_rs_c_2 = $this->conn->FetchRow($rs_c_2)){
			$muns_cat_2[$c_2] = $row_rs_c_2[0];
			$c_2++;
		}

		$muns_cat_3 = array_intersect($muns_cat_1,$muns_cat_2);

		//CONSULTA LOS DEPARTAMENTOS POR EVENTO POR CATEGORIA
		$sql_c_1 = "SELECT DISTINCT ID_DEPTO FROM depto_evento INNER JOIN evento ON evento.ID_EVENTO = depto_evento.ID_EVENTO WHERE ".$where." AND ID_CAT = 1";
		$rs_c_1 = $this->conn->OpenRecordset($sql_c_1);
		$c_1 = 0;
		while ($row_rs_c_1 = $this->conn->FetchRow($rs_c_1)){
			$deptos_cat_1[$c_1] = $row_rs_c_1[0];
			$c_1++;
		}

		$sql_c_2 = "SELECT DISTINCT ID_DEPTO FROM depto_evento INNER JOIN evento ON evento.ID_EVENTO = depto_evento.ID_EVENTO WHERE ".$where." AND ID_CAT = 2";
		$rs_c_2 = $this->conn->OpenRecordset($sql_c_2);
		$c_2 = 0;
		while ($row_rs_c_2 = $this->conn->FetchRow($rs_c_2)){
			$deptos_cat_2[$c_2] = $row_rs_c_2[0];
			$c_2++;
		}

		$deptos_cat_3 = array_intersect($deptos_cat_1,$deptos_cat_2);


		$pdf->setColor(0,0.6,1);
		$pdf->ezText("<i>INFORME SEMANAL</i>",24,array('justification'=>'left'));
		$pdf->ezText("<i>".$mes_ini_txt." ".$fecha_tmp_ini[2]." a ".$fecha_tmp_fin[2]." de</i>",24,array('justification'=>'left'));
		$pdf->ezText("<i>".$fecha_tmp_ini[0]."</i>",24,array('justification'=>'left'));

		//MAPA INICIAL DE COLOMBIA CON LOS DEPARTAMENTOS RESALTADOS
		$d = 0;
		foreach ($id_deptos as $id_depto){
			//DIFERENTE A NACIONAL
			if ($id_depto != '00'){
				//CONSULTA EN QUE CATEGORIA ESTA EL EVENTO
				$id_cat_e = 1;
				if (in_array($id_depto,$deptos_cat_2)){
					$id_cat_e = 2;
				}
				if (in_array($id_depto,$deptos_cat_3)){
					$id_cat_e = 3;
				}

				$img  = '../images/mapas/nac/'.$id_depto.'_'.$id_cat_e.'.png';
				$img_att = getimagesize($img);
				$pdf->addPngFromFile($img,330,50,$img_att[0]/1.2,$img_att[1]/1.2);

			}
			$d++;
		}

		//COLOR NREGRO PARA EL PIE DE PAGINA
		$pdf->setColor(0,0,0);

		//*************************************************************
		//SE MUESTRAN LOS EVENTO DE COLOMBIA  : INICIO
		//*************************************************************

		$sql_e = "SELECT evento.ID_EVENTO FROM evento INNER JOIN depto_evento ON evento.ID_EVENTO = depto_evento.ID_EVENTO WHERE ".$where." AND ID_DEPTO = '00'";
		$rs_e = $this->conn->OpenRecordset($sql_e);
		$e = 0;
		while ($row_rs_e = $this->conn->FetchRow($rs_e)){

			//SE MUESTRA SOLO 5 EVENTOS POR PAGINA
			if (fmod($e,5) == 0){
				$pdf->ezNewPage();
				$pdf->setColor(0,0.6,1);
				$pdf->addText(270,505,24,'<i>COLOMBIA</i>');

				$img  = '../images/mapas/nac/00.png';
				$img_att = getimagesize($img);
				$pdf->addPngFromFile($img,600,430,$img_att[0],$img_att[1]);
				$pdf->ezSetY(400);

				$pdf->setColor(0,0,0);

			}

			$eve = $this->Get($row_rs_e[0]);

			if (count($eve->id_tipo) > 1){
				$tipo = $tipo_dao->Get($eve->id_tipo[0]);
				$pdf->ezImage('../images/tipo_evento/'.strtolower($tipo->nombre).'.png');
			}

			$pdf->ezText($eve->desc,12,array('justification'=>'full'));
			$pdf->ezText('',12);

			$e++;
		}

		//*************************************************************
		//SE MUESTRAN LOS EVENTO DE COLOMBIA  : FIN
		//*************************************************************


		//*************************************************************
		//SE MUESTRAN LOS EVENTO DE LOS DEMAS DEPARTAMENTOS  : INICIO
		//*************************************************************

		//CONSULTA LOS DEPARTAMENTOS EN LOS QUE OCURRIERON EVENTOS
		$sql_d = "SELECT DISTINCT departamento.ID_DEPTO, departamento.NOM_DEPTO FROM departamento INNER JOIN depto_evento ON departamento.ID_DEPTO = depto_evento.ID_DEPTO INNER JOIN evento ON evento.ID_EVENTO = depto_evento.ID_EVENTO  WHERE ".$where." ORDER BY NOM_DEPTO ASC";
		$rs_d = $this->conn->OpenRecordset($sql_d);
		$d = 0;
		while ($row_rs_d = $this->conn->FetchRow($rs_d)){
			$id_depto = $row_rs_d[0];
			$nom_depto = $row_rs_d[1];

			if ($id_depto != '00'){

				//CONSULTA LOS MUNICIPIOS DEL DEPARTAMENTO SELECCIONADO QUE TIENEN EVENTOS
				$sql_m = "SELECT DISTINCT municipio.ID_MUN, municipio.NOM_MUN FROM municipio INNER JOIN mun_evento ON municipio.ID_MUN = mun_evento.ID_MUN INNER JOIN evento ON evento.ID_EVENTO = mun_evento.ID_EVENTO  WHERE ID_DEPTO = ".$id_depto." AND ".$where." ORDER BY NOM_MUN ASC";
				$rs_m = $this->conn->OpenRecordset($sql_m);
				$m = 0;
				$id_muns = Array();
				$nom_muns = Array();
				while ($row_rs_m = $this->conn->FetchRow($rs_m)){
					$id_muns[$m] = $row_rs_m[0];
					$nom_muns[$m] = $row_rs_m[1];

					$m++;
				}

				//LISTA LOS EVENTOS POR MUNICIPIO
				$m = 0;
				foreach ($id_muns as $id_mun){
					$sql_e = "SELECT evento.ID_EVENTO FROM evento INNER JOIN mun_evento ON evento.ID_EVENTO = mun_evento.ID_EVENTO WHERE ".$where." AND ID_MUN = ".$id_mun;
					$rs_e = $this->conn->OpenRecordset($sql_e);
					$e = 0;
					while ($row_rs_e = $this->conn->FetchRow($rs_e)){

						//SE MUESTRA SOLO 5 EVENTOS POR PAGINA x MUNICIPIO
						if (fmod($e,5) == 0){

							//TITULO DEL DEPARTAMENTO
							$pdf->ezNewPage();
							$pdf->setColor(0,0.6,1);
							$pdf->addText(270,505,24,'<i>'.$nom_depto.'</i>');

							$x = 792 - 50 - 285;  //Valor Promerio de x
							$y = 612 - 30 - 235;  //Valor Promerio de y

							//LA IMAGEN DEL DEPARTAMENTO EN LA ESQUINA SUPERIOR DERECHA CON LOS MUNICIPIOS RESALTADOS
							foreach ($id_muns as $id_mun_t){
								//CONSULTA EN QUE CATEGORIA ESTA EL EVENTO
								$id_cat_e = 1;
								if (in_array($id_mun_t,$muns_cat_2)){
									$id_cat_e = 2;
								}
								if (in_array($id_mun_t,$muns_cat_3)){
									$id_cat_e = 3;
								}

								$img  = '../images/mapas/depto/'.$id_mun_t.'_'.$id_cat_e.'.png';
								if (file_exists($img)){
									$img_att = getimagesize($img);

									//COORDENADA X,Y = ESQUINA INFERIOR IZQUIERDA DE LA IMAGEN
									$x = 792 - 50 - $img_att[0];
									$y = 612 - 30 - $img_att[1];

									$pdf->addPngFromFile($img,$x,$y,$img_att[0],$img_att[1]);
								}
							}

							$pdf->ezSetY(330);

							//IMAGEN DEL MUNICIPIO
							$id_cat_e = 1;
							if (in_array($id_mun,$muns_cat_2)){
								$id_cat_e = 2;
							}
							if (in_array($id_mun,$muns_cat_3)){
								$id_cat_e = 3;
							}

							//COORDENADA X,Y PARA EL TITULO DEL MUNICIPIO
							$x_mun = 120;
							$y_mun = 400;

							$img  = '../images/mapas/mun/'.$id_mun.'_'.$id_cat_e.'.png';
							if (file_exists($img)){
								$img_att = getimagesize($img);
								$pdf->addPngFromFile($img,50,400,$img_att[0],$img_att[1]);

								$x_mun = 50 + $img_att[0] + 20;
							}

							$pdf->setColor(0,0.6,1);
							$pdf->addText($x_mun,$y_mun,24,'MUNICIPIO: <i>'.$nom_muns[$m].'</i>');

							$pdf->setColor(0,0,0);

							$x_img_tipo = 50;
							$y_img_tipo = $y - 20 ;
							$y_desc = $y_img_tipo;

						}

						$x_desc = 50;

						//DATOS DEL EVENTO
						$eve = $this->Get($row_rs_e[0]);

						if (count($eve->id_tipo) >= 1){
							$tipo = $tipo_dao->Get($eve->id_tipo[0]);

							$img  = '../images/tipo_evento/'.strtolower($tipo->nombre).'.png';
							if (file_exists($img)){
								$img_att = getimagesize($img);

								//COORDENADAS PARA EL TEXTO DEL EVENTO
								$x_desc += $img_att[0] + 30;

								$y_desc = $y_img_tipo;
								$y_img_tipo -= $img_att[1];

								$pdf->addPngFromFile($img,$x_img_tipo,$y_img_tipo,$img_att[0],$img_att[1]);

							}
						}

						//$pdf->addText($x_desc,$y_desc,12,$eve->desc);
						$pdf->ezSetY($y_desc);
						$pdf->ezText($eve->desc.$y_desc,12,array('aleft'=>$x_desc,'justification'=>'full'));
						$pdf->ezText('',12);
						$pdf->ezSetY($y_desc - 100);
						//$y_desc -= 100;

						$e++;
					}
					$m++;
				}
				//*************************************************************
				//SE MUESTRAN LOS EVENTO DE LOS DEMAS DEPARTAMENTOS  : FIN
				//*************************************************************
			}
		}
		$pdf->ezStream();
		die;

	}


	/**
  * Imprime en pantalla los datos del Evento
  * @access public
  * @param object $vo Evento que se va a imprimir
  * @param string $formato Formato en el que se listarán los Evento, puede ser Tabla o ComboSelect
	* @param int $valor_combo ID del Evento que será selccionado cuando el formato es ComboSelect
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
  * Carga un VO de Evento con los datos de la consulta
  * @access public
  * @param object $vo VO de Evento que se va a recibir los datos
  * @param object $Resultset Resource de la consulta
	* @return object $vo VO de Evento con los datos
  */			
	function GetFromResult ($vo,$Result){

		$vo->id = $Result->{$this->columna_id};
		$vo->fecha_evento = $Result->FECHA_EVENTO;
		$vo->fecha_registro = $Result->FECHA_REGISTRO;
		$vo->desc = $Result->DESC_EVENTO;
		$vo->desc_cons_hum = $Result->DESC_CONSHUM;
		$vo->desc_riesg_hum = $Result->DESC_RIESGHUM;
		$vo->lugar = $Result->LUGAR_EVENTO;
		$vo->conf = $Result->CONF_EVENTO;
		$vo->fuente = $Result->FUENTE;
		$vo->id_cat = $Result->ID_CAT;

		$id = $vo->id;

		//TIPO EVENTO
		$arr = Array();
		$sql_s = "SELECT ID_TIPO_EVE FROM evento_tipo_evento WHERE ID_EVENTO = ".$id;
		$rs_s = $this->conn->OpenRecordset($sql_s);
		while ($row_rs_s = $this->conn->FetchRow($rs_s)){
			array_push($arr,$row_rs_s[0]);
		}
		$vo->id_tipo = $arr;

		//CONSECUENCIAS H.
		$arr = Array();
		$sql_s = "SELECT ID_CONS_HUM FROM evento_conshum WHERE ID_EVENTO = ".$id;
		$rs_s = $this->conn->OpenRecordset($sql_s);
		while ($row_rs_s = $this->conn->FetchRow($rs_s)){
			array_push($arr,$row_rs_s[0]);
		}
		$vo->id_cons = $arr;

		//RIESGOS H.
		$arr = Array();
		$sql_s = "SELECT ID_RIESG_HUM FROM evento_riesgo WHERE ID_EVENTO = ".$id;
		$rs_s = $this->conn->OpenRecordset($sql_s);
		while ($row_rs_s = $this->conn->FetchRow($rs_s)){
			array_push($arr,$row_rs_s[0]);
		}
		$vo->id_riesgos = $arr;


		//DEPARTAMENTOS
		$arr = Array();
		$sql_s = "SELECT ID_DEPTO FROM depto_evento WHERE ID_EVENTO = ".$id;
		$rs_s = $this->conn->OpenRecordset($sql_s);
		while ($row_rs_s = $this->conn->FetchRow($rs_s)){
			array_push($arr,$row_rs_s[0]);
		}
		$vo->id_deptos = $arr;

		//MUNICIPIOS
		$arr = Array();
		$sql_s = "SELECT ID_MUN FROM mun_evento WHERE ID_EVENTO = ".$id;
		$rs_s = $this->conn->OpenRecordset($sql_s);
		while ($row_rs_s = $this->conn->FetchRow($rs_s)){
			array_push($arr,$row_rs_s[0]);
		}
		$vo->id_muns = $arr;

		//REGIONES
		$arr = Array();
		$sql_s = "SELECT ID_REG FROM reg_evento WHERE ID_EVENTO = ".$id;
		$rs_s = $this->conn->OpenRecordset($sql_s);
		while ($row_rs_s = $this->conn->FetchRow($rs_s)){
			array_push($arr,$row_rs_s[0]);
		}
		$vo->id_regiones = $arr;

		//POBLADOS
		$arr = Array();
		$sql_s = "SELECT ID_POB FROM poblado_evento WHERE ID_EVENTO = ".$id;
		$rs_s = $this->conn->OpenRecordset($sql_s);
		while ($row_rs_s = $this->conn->FetchRow($rs_s)){
			array_push($arr,$row_rs_s[0]);
		}
		$vo->id_poblados = $arr;

		//RESGUARDOS
		$arr = Array();
		$sql_s = "SELECT ID_RESGUADRO FROM resg_evento WHERE ID_EVENTO = ".$id;
		$rs_s = $this->conn->OpenRecordset($sql_s);
		while ($row_rs_s = $this->conn->FetchRow($rs_s)){
			array_push($arr,$row_rs_s[0]);
		}
		$vo->id_resguardos = $arr;

		//PARQUES
		$arr = Array();
		$sql_s = "SELECT ID_PAR_NAT FROM par_nat_evento WHERE ID_EVENTO = ".$id;
		$rs_s = $this->conn->OpenRecordset($sql_s);
		while ($row_rs_s = $this->conn->FetchRow($rs_s)){
			array_push($arr,$row_rs_s[0]);
		}
		$vo->id_parques = $arr;

		//DIV. AFRO
		$arr = Array();
		$sql_s = "SELECT ID_DIV_AFRO FROM div_afro_evento WHERE ID_EVENTO = ".$id;
		$rs_s = $this->conn->OpenRecordset($sql_s);
		while ($row_rs_s = $this->conn->FetchRow($rs_s)){
			array_push($arr,$row_rs_s[0]);
		}
		$vo->id_divisiones_afro = $arr;

		//ACTORES
		$arr = Array();
		$sql_s = "SELECT ID_ACTOR FROM actor_evento WHERE ID_EVENTO = ".$id;
		$rs_s = $this->conn->OpenRecordset($sql_s);
		while ($row_rs_s = $this->conn->FetchRow($rs_s)){
			array_push($arr,$row_rs_s[0]);
		}
		$vo->id_actores = $arr;

		return $vo;

	}

	/**
  * Inserta un Evento en la B.D.
  * @access public
  * @param object $depto_vo VO de Evento que se va a insertar
  */		
	function Insertar($evento_vo){
		//DATOS DEL EVENTO
		$sql =  "INSERT INTO ".$this->tabla." (FECHA_EVENTO,DESC_EVENTO,DESC_CONSHUM,DESC_RIESGHUM,LUGAR_EVENTO,CONF_EVENTO,FUENTE,ID_CAT,FECHA_REGISTRO)";
		$sql .= " VALUES ('".$evento_vo->fecha_evento."','".$evento_vo->desc."','".$evento_vo->desc_cons_hum."','".$evento_vo->desc_riesg_hum."','".$evento_vo->lugar."',".$evento_vo->conf.",'".$evento_vo->fuente."',".$evento_vo->id_cat.",'".$evento_vo->fecha_registro."')";

		$this->conn->Execute($sql);
		$id_evento = $this->conn->GetGeneratedID();

		$this->InsertarTablasUnion($evento_vo,$id_evento);

    	?>
    	<script>
    	if (!confirm("Evento insertado con éxito, desea definir la Ubicación Geográfica del evento en Regiones y Poblados?")){
    		location.href="<?=$this->url;?>";
    	}
    	</script>
    	<?
	}

	/**
  * Inserta las tablas de union para el Evento en la B.D.
  * @access public
  * @param object $depto_vo VO de Evento que se va a insertar
  */		
	function InsertarTablasUnion($evento_vo,$id_evento){

		//TIPO EVENTO
		$arr = $evento_vo->id_tipo;
		$num_arr = count($arr);

		for($m=0;$m<$num_arr;$m++){
			$sql = "INSERT INTO evento_tipo_evento (ID_TIPO_EVE,ID_EVENTO) VALUES (".$arr[$m].",".$id_evento.")";
			$this->conn->Execute($sql);
		}

		//CONSECUENCIAS H.
		$arr = $evento_vo->id_cons;
		$num_arr = count($arr);

		for($m=0;$m<$num_arr;$m++){
			$sql = "INSERT INTO evento_conshum (ID_CONS_HUM,ID_EVENTO) VALUES (".$arr[$m].",".$id_evento.")";
			$this->conn->Execute($sql);
		}

		//RIESGOS H.
		$arr = $evento_vo->id_riesgos;
		$num_arr = count($arr);

		for($m=0;$m<$num_arr;$m++){
			$sql = "INSERT INTO evento_riesgo (ID_RIESG_HUM,ID_EVENTO) VALUES (".$arr[$m].",".$id_evento.")";
			$this->conn->Execute($sql);
		}

		//DEPTOS
		$arr = $evento_vo->id_deptos;
		$num_arr = count($arr);

		for($m=0;$m<$num_arr;$m++){
			$sql = "INSERT INTO depto_evento (ID_DEPTO,ID_EVENTO) VALUES ('".$arr[$m]."',".$id_evento.")";
			$this->conn->Execute($sql);
		}

		//MUNICPIOS
		$arr = $evento_vo->id_muns;
		$num_arr = count($arr);

		for($m=0;$m<$num_arr;$m++){
			$sql = "INSERT INTO mun_evento (ID_MUN,ID_EVENTO) VALUES ('".$arr[$m]."',".$id_evento.")";
			//echo $sql;
			$this->conn->Execute($sql);
		}

		//ACTORES
		$arr = $evento_vo->id_actores;
		$num_arr = count($arr);

		for($m=0;$m<$num_arr;$m++){
			$sql = "INSERT INTO actor_evento (ID_ACTOR,ID_EVENTO) VALUES (".$arr[$m].",".$id_evento.")";
			$this->conn->Execute($sql);
		}
	}

	/**
	* Inserta las tablas de union de cobertura del Evento en la B.D.
	* @access public
	* @param object $evento_vo VO de Organizacion que se va a insertar
	* @param int $id_evento ID de la Organizacion que se acaba de insertar
	*/		
	function InsertarTablasUnionCobertura($evento_vo,$id_evento,$opcion){

		if ($opcion == 2 || $opcion == 5){

			//REGIONES
			$arr = $evento_vo->id_regiones;
			$num_arr = count($arr);


			for($m=0;$m<$num_arr;$m++){
				$sql = "INSERT INTO reg_evento (ID_REG,ID_EVENTO) VALUES (".$arr[$m].",".$id_evento.")";
				$this->conn->Execute($sql);
			}

			//POBLADOS
			$arr = $evento_vo->id_poblados;
			$num_arr = count($arr);

			for($m=0;$m<$num_arr;$m++){
				$sql = "INSERT INTO poblado_evento (ID_POB,ID_EVENTO) VALUES ('".$arr[$m]."',".$id_evento.")";
				$this->conn->Execute($sql);
				//echo $sql;
			}
		}
		else if ($opcion == 4){

			//RESGUARDOS
			$arr = $evento_vo->id_resguardos;
			$num_arr = count($arr);

			for($m=0;$m<$num_arr;$m++){
				$sql = "INSERT INTO resg_evento (ID_RESGUADRO,ID_EVENTO) VALUES (".$arr[$m].",".$id_evento.")";
				$this->conn->Execute($sql);
				//echo $sql;
			}

			//PARQUES
			$arr = $evento_vo->id_parques;
			$num_arr = count($arr);

			for($m=0;$m<$num_arr;$m++){
				$sql = "INSERT INTO par_nat_evento (ID_PAR_NAT,ID_EVENTO) VALUES (".$arr[$m].",".$id_evento.")";
				$this->conn->Execute($sql);
				//echo $sql;
			}

			//DIV. AFRO
			$arr = $evento_vo->id_divisiones_afro;
			$num_arr = count($arr);

			for($m=0;$m<$num_arr;$m++){
				$sql = "INSERT INTO div_afro_evento (ID_DIV_AFRO,ID_EVENTO) VALUES (".$arr[$m].",".$id_evento.")";
				$this->conn->Execute($sql);
				//echo $sql;
			}
		}

	}


	/**
  * Actualiza un Evento en la B.D.
  * @access public
  * @param object $depto_vo VO de Evento que se va a actualizar
  */		
	function Actualizar($evento_vo){
		$sql =  "UPDATE ".$this->tabla." SET";
		$sql .= " FECHA_EVENTO = '".$evento_vo->fecha_evento."',";
		$sql .= " FECHA_REGISTRO = '".$evento_vo->fecha_registro."',";
		$sql .= " DESC_EVENTO = '".$evento_vo->desc."',";
		$sql .= " DESC_CONSHUM = '".$evento_vo->desc_cons_hum."',";
		$sql .= " DESC_RIESGHUM = '".$evento_vo->desc_riesg_hum."',";
		$sql .= " LUGAR_EVENTO = '".$evento_vo->lugar."',";
		$sql .= " CONF_EVENTO = ".$evento_vo->conf.",";
		$sql .= " FUENTE = '".$evento_vo->fuente."',";
		$sql .= " ID_CAT = ".$evento_vo->id_cat;
		$sql .= " WHERE ".$this->columna_id." = ".$evento_vo->id;

		$this->conn->Execute($sql);

		$this->BorrarTablasUnion($evento_vo->id);

		$this->InsertarTablasUnion($evento_vo,$evento_vo->id);

		?>
  	<script>
  	alert("Registro actualizado con &eacute;xito!");
  	</script>
  	<?
	}

	/**
	* Actualiza la cobertura geográfica de una Evento en la B.D.
	* @access public
	* @param object $depto_vo VO de Evento que se va a actualizar
	*/		
	function ActualizarCobertura($evento_vo,$paso){

		$this->BorrarTablasUnionCobertura($evento_vo->id,$paso);
		$this->InsertarTablasUnionCobertura($evento_vo,$evento_vo->id,$paso);

		if ($paso == 2){
			?>
			<script>
			alert("Cobertura Geográfica (Poblado - Región) registrada con &eacute;xito!");
			</script>
			<?
		}
		if ($paso == 3){
			?>
			<script>
			alert("Cobertura Geográfica (Departamento - Municipio) registrada con &eacute;xito!");
			location.href = '<?=$this->url;?>';
			</script>
			<?
		}
		else if ($paso == 4){
			?>
			<script>
			alert("Cobertura Geográfica (Parque Natural, Resguardo o Divison Afro) registrada con &eacute;xito!");
			location.href = '<?=$this->url;?>';
			</script>
			<?
		}
		else if ($paso == 5){
			?>
			<script>
			alert("Cobertura Geográfica (Poblado - Región) registrada con &eacute;xito!");
			location.href = '<?=$this->url;?>';
			</script>
			<?
		}
	}

	/**
  * Borra un Evento en la B.D.
  * @access public
  * @param int $id ID del Evento que se va a borrar de la B.D
  */	
	function Borrar($id,$opcion){

		//BORRA TABLAS DE UNION
		$this->BorrarTablasUnion($id);
		$this->BorrarTablasUnionCobertura($id,$opcion);

		//BORRA EL EVENTO
		$sql = "DELETE FROM ".$this->tabla." WHERE ".$this->columna_id." = ".$id;
		$this->conn->Execute($sql);

    ?>
    <script>
    alert("Registro eliminado con éxito!");
    location.href = '<?=$this->url;?>';
    </script>
    <?
	}

	/**
  * Borra las tablas de union de un Evento en la B.D.
  * @access public
  * @param int $id ID del Evento que se va a borrar de la B.D
  */	
	function BorrarTablasUnion($id){

		//TIPO EVENTO
		$sql = "DELETE FROM evento_tipo_evento WHERE ".$this->columna_id." = ".$id;
		$this->conn->Execute($sql);

		//CONSECUENCIAS H.
		$sql = "DELETE FROM evento_conshum WHERE ".$this->columna_id." = ".$id;
		$this->conn->Execute($sql);

		//RIESGO H.
		$sql = "DELETE FROM evento_riesgo WHERE ".$this->columna_id." = ".$id;
		$this->conn->Execute($sql);

		//DEPTOS
		$sql = "DELETE FROM depto_evento WHERE ".$this->columna_id." = ".$id;
		$this->conn->Execute($sql);

		//MUNICPIOS
		$sql = "DELETE FROM mun_evento WHERE ".$this->columna_id." = ".$id;
		$this->conn->Execute($sql);

		//ACTORES
		$sql = "DELETE FROM actor_evento WHERE ".$this->columna_id." = ".$id;
		$this->conn->Execute($sql);

	}

	/**
  * Borra las tablas de union de un Evento en la B.D.
  * @access public
  * @param int $id ID del Evento que se va a borrar de la B.D
  */	
	function BorrarTablasUnionCobertura($id,$opcion){

		if ($opcion == 2 || $opcion == 0 || $opcion == 5){

			//REGIONES
			$sql = "DELETE FROM reg_evento WHERE ".$this->columna_id." = ".$id;
			$this->conn->Execute($sql);

			//POBLADOS
			$sql = "DELETE FROM poblado_evento WHERE ".$this->columna_id." = ".$id;
			$this->conn->Execute($sql);
			//echo $sql;
		}
		if ($opcion == 4 || $opcion == 0){

			//RESGUARDOS
			$sql = "DELETE FROM resg_evento WHERE ".$this->columna_id." = ".$id;
			$this->conn->Execute($sql);

			//PARQUES
			$sql = "DELETE FROM par_nat_evento WHERE ".$this->columna_id." = ".$id;
			$this->conn->Execute($sql);

			//DIV. AFRO
			$sql = "DELETE FROM div_afro_evento WHERE ".$this->columna_id." = ".$id;
			$this->conn->Execute($sql);
		}
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
		$tipo_dao = New TipoEventoDAO();
		$actor_dao = New ActorDAO();
		$cat_tipo_dao = New CatTipoEventoDAO();
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

		echo "<tr><td class='tabla_consulta'><b>Tipo de Evento</b></td>";
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
	* Lista los Eventos en una Tabla
	* @access public
	*/			
	function Reportar(){

		set_time_limit(0);
		ini_set("memory_limit","16M");

		$cat_dao = New CatTipoEventoDAO();
		$depto_dao = New DeptoDAO();
		$mun_dao = New MunicipioDAO();
		$tipo_evento_dao = New TipoEventoDAO();
		$actor_dao = New ActorDAO();
		$cons_hum_dao = New ConsHumDAO();
		$riesgo_hum_dao = New RiesgoHumDAO();
		$cats = $cat_dao->GetAllArray('');

		$condicion = "";
		$arreglos = "";
		//FECHA
		if (isset($_POST["f_ini"]) && $_POST["f_ini"] != ""){
			$f_ini = $_POST['f_ini'];
			$f_final = $_POST['f_fin'];

			$condicion = "FECHA_EVENTO >= '".$f_ini."' AND FECHA_EVENTO <= '".$f_final."'";

			$arr_id_est_fecha = Array();

			$sql = "SELECT ID_EVENTO FROM evento WHERE ".$condicion;
			$rs = $this->conn->OpenRecordset($sql);
			$i = 0;
			while ($row_rs = $this->conn->FetchRow($rs)){
				$arr_id_est_fecha[$i] = $row_rs[0];
				$i++;
			}

			$arreglos .= "\$arr_id_est_fecha";
		}

		//TIPO
		if (isset($_POST["id_tipo"])){

			$arr_id_tipo = Array();
			$cats = $cat_dao->GetAllArray('ID_CAT_TIPO_EVE = '.$_POST["id_cat"]);

			$id_tipo = $_POST['id_tipo'];
			$id_s = implode(",",$id_tipo);

			$sql = "SELECT ID_EVENTO FROM evento_tipo_evento WHERE ID_TIPO_EVE IN (".$id_s.")";
			$rs = $this->conn->OpenRecordset($sql);
			$i = 0;
			while ($row_rs = $this->conn->FetchRow($rs)){
				$arr_id_tipo[$i] = $row_rs[0];
				$i++;
			}

			if ($arreglos == "")	$arreglos = "\$arr_id_tipo";
			else					$arreglos .= ",\$arr_id_tipo";
		}

		//ACTOR
		if (isset($_POST["id_actor"])){

			$arr_id_actor = Array();

			$id_actor = $_POST['id_actor'];
			$id_s = implode(",",$id_actor);

			$sql = "SELECT ID_EVENTO FROM actor_evento WHERE ID_ACTOR IN (".$id_s.")";
			$rs = $this->conn->OpenRecordset($sql);
			$i = 0;
			while ($row_rs = $this->conn->FetchRow($rs)){
				$arr_id_actor[$i] = $row_rs[0];
				$i++;
			}

			if ($arreglos == "")	$arreglos = "\$arr_id_actor";
			else					$arreglos .= ",\$arr_id_actor";
		}

		//CONS
		if (isset($_POST["id_cons"])){
			$arr_id_cons = Array();

			$id_cons = $_POST['id_cons'];
			$id_s = implode(",",$id_cons);

			$sql = "SELECT ID_EVENTO FROM evento_conshum WHERE ID_CONS_HUM IN (".$id_s.")";
			$rs = $this->conn->OpenRecordset($sql);
			$i = 0;
			while ($row_rs = $this->conn->FetchRow($rs)){
				$arr_id_cons[$i] = $row_rs[0];
				$i++;
			}

			if ($arreglos == "")	$arreglos = "\$arr_id_cons";
			else					$arreglos .= ",\$arr_id_cons";

		}

		//RIESGOS
		if (isset($_POST["id_riesgo"])){

			$arr_id_riesgo = Array();

			$id_riesgo = $_POST['id_riesgo'];
			$id_s = implode(",",$id_riesgo);

			$sql = "SELECT ID_EVENTO FROM evento_riesgo WHERE ID_RIESG_HUM IN (".$id_s.")";
			$rs = $this->conn->OpenRecordset($sql);
			$i = 0;
			while ($row_rs = $this->conn->FetchRow($rs)){
				$id_riesgo[$i] = $row_rs[0];
				$i++;
			}

			if ($arreglos == "")	$arreglos = "\$arr_id_riesgo";
			else					$arreglos .= ",\$arr_id_riesgo";

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

			$sql = "SELECT evento.ID_EVENTO FROM depto_evento INNER JOIN evento ON depto_evento.ID_EVENTO = evento.ID_EVENTO WHERE ID_DEPTO IN (".$id_depto_s.")";

			$sql .= " ORDER BY evento.ID_EVENTO ASC";

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

			$sql = "SELECT evento.ID_EVENTO FROM mun_evento INNER JOIN evento ON mun_evento.ID_EVENTO = evento.ID_EVENTO WHERE ID_MUN IN (".$id_mun_s.")";

			$sql .= " ORDER BY evento.ID_EVENTO ASC";

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

		if (count(split("[,]",$arreglos)) > 1 ){
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

		echo "<form action='index.php?m_e=evento&accion=consultar&class=EventoDAO' method='POST'>";
		echo "<table align='center' cellspacing='1' cellpadding='3'>";
		echo "<tr><td>&nbsp;</td></tr>";
		if ($num_arr > 0){
			echo "<tr><td colspan='8' align='right'>Exportar a: <input type='image' src='images/consulta/generar_pdf.gif' onclick=\"document.getElementById('pdf').value = 1;\">&nbsp;&nbsp;<input type='image' src='images/consulta/excel.gif' onclick=\"document.getElementById('pdf').value = 2;\"></td>";
		}
		echo "<tr><td align='center' class='titulo_lista' colspan=8>CONSULTA DE EVENTOS</td></tr>";
		echo "<tr><td colspan=5>Consulta realizada aplicando los siguientes filtros:</td>";
		echo "<tr><td colspan=5>";

		//TITULO DE TIPO
		if (isset($_POST["id_tipo"])){
			echo "<img src='images/flecha.gif'> Tipo de Evento: ";
			$t = 0;
			foreach($id_tipo as $id_t){
				$vo  = $tipo_evento_dao->Get($id_t);
				if ($t == 0)	echo "<b>".$vo->nombre."</b>";
				else			echo ", <b>".$vo->nombre."</b>";
				$t++;
			}
			echo "<br>";
		}
		//TITULO DE ACTOR
		if (isset($_POST["id_actor"])){
			echo "<img src='images/flecha.gif'> Actor: ";
			$t = 0;
			foreach($id_actor as $id_t){
				$vo  = $actor_dao->Get($id_t);
				if ($t == 0)	echo "<b>".$vo->nombre."</b>";
				else			echo ", <b>".$vo->nombre."</b>";
				$t++;
			}
			echo "<br>";
		}
		//CONS
		if (isset($_POST["id_cons"])){
			echo "<img src='images/flecha.gif'> Cosecuencia Humanitaria: ";
			$t = 0;
			foreach($id_cons as $id_t){
				$vo  = $cons_hum_dao->Get($id_t);
				if ($t == 0)	echo "<b>".$vo->nombre."</b>";
				else			echo ", <b>".$vo->nombre."</b>";
				$t++;
			}
			echo "<br>";
		}
		//RIESGO
		if (isset($_POST["id_riesgo"])){
			echo "<img src='images/flecha.gif'> Riesgo Humanitario: ";
			$t = 0;
			foreach($id_riesgo as $id_t){
				$vo  = $riesgo_hum_dao->Get($id_t);
				if ($t == 0)	echo "<b>".$vo->nombre."</b>";
				else			echo ", <b>".$vo->nombre."</b>";
				$t++;
			}
			echo "<br>";
		}

		//FECHA
		if (isset($_POST["f_ini"]) && $_POST["f_ini"] != ""){
			echo "<img src='images/flecha.gif'> Fecha Desde: <b>".$_POST["f_ini"]."</b> -- Fecha Hasta: <b>".$_POST["f_fin"]."</b>";
			echo "<br>";
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
			foreach($id_mun as $id_t){
				$vo  = $mun_dao->Get($id_t);
				if ($t == 0)	echo "<b>".$vo->nombre."</b>";
				else			echo ", <b>".$vo->nombre."</b>";
				$t++;
			}
			echo "<br>";
		}
		echo "</td>";

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
					echo "<tr><td colspan='5'><br><b>Categoria del Evento: ".$cat_vo->nombre."</b></td></tr>
							<tr class='titulo_lista'>
							<td align='center' width='70'><b>Departamento</b></td>
							<td align='center' width='70'><b>Municipio</b></td>
							<td align='center' width='100'><b>Tipo de Evento</b></td>
							<td align='center' width='10'><b>Actores</b></td>
							<td align='center'><b>Descripción</b></td>
							<td align='center' width='70'><b>Fecha evento</b></td>
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

						////FECHA DEL EVENTO
						echo "<td>".$arr_vo->fecha_evento."</td>";

						////FECHA DE REGISTRO
						echo "<td>".$arr_vo->fecha_registro."</td>";
						echo "<td><a href='#' onclick=\"window.open('index2.php?accion=consultar&class=EventoDAO&method=Ver&param=".$arr_vo->id."','','top=30,left=30,height=750,width=750,scrollbars=1');return false;\">Detalles</a></td>";
						echo "</tr>";

						$p++;
					}
				}
				$c++;
			}
			echo "<input type='hidden' name='id_eventos' value='".implode(",",$arr_id)."'>";
			echo "<input type='hidden' id='pdf' name='pdf'>";
			echo "<input type='hidden' id='basico' name='basico' value='1'>";
			echo "</table>";
			echo "</form>";
		}
		else{
			echo "<tr><td align='center'><br><b>NO SE ENCONTRARON EVENTOS</b></td></tr>";
			echo "<tr><td align='center'><br><a href='javascript:history.back();'>Regresar</a></td></tr>";
			die;
		}
	}
	/******************************************************************************
	* Reporte PDF - EXCEL
	* @param Array $id_eventos Id de los Eventos a Reportar
	* @param Int $formato PDF o Excel
	* @param Int $basico 1 = Básico - 2 = Detallado
	* @access public
	*******************************************************************************/
	function ReporteEvento($id_eventos,$formato,$basico){

		//INICIALIZACION DE VARIABLES
		$depto_dao = New DeptoDAO();
		$mun_dao = New MunicipioDAO();
		$tipo_dao = New TipoEventoDAO();
		$actor_dao = New ActorDAO();
		$cat_dao = New CatTipoEventoDAO();
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

			$pdf = new Cezpdf();

			$pdf -> ezSetMargins(80,70,20,20);
			$pdf->selectFont('admin/lib/common/PDFfonts/Helvetica.afm');

			// Coloca el logo y el pie en todas las páginas
			$all = $pdf->openObject();
			$pdf->saveState();
			$img_att = getimagesize('images/logos/enc_reporte_semanal.jpg');
			$pdf->addPngFromFile('images/logos/enc_reporte_semanal.png',700,550,$img_att[0]/2,$img_att[1]/2);

			$pdf->addText(300,580,14,'<b>Sala de Situación Humanitaria</b>');

			if ($basico == 1){
				$pdf->addText(350,560,12,'Listado de Eventos');
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
						't_evento'   => '<b>Tipo de Evento</b>',
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
					Se ha generado correctamente el archivo PDF de Eventos.<br><br>
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
					Se ha generado correctamente el archivo TXT de Eventos.<br><br>
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
		$cat_dao = New CatTipoEventoDAO();
		$depto_dao = New DeptoDAO();
		$mun_dao = New MunicipioDAO();
		$tipo_evento_dao = New TipoEventoDAO();
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
					echo "<tr><td colspan='5'><br><b>Categoria del Evento: ".$cat_vo->nombre."</b></td></tr>
							<tr class='titulo_lista'>
							<td align='center' width='70'><b>Departamento</b></td>
							<td align='center' width='70'><b>Municipio</b></td>
							<td align='center' width='100'><b>Tipo de Evento</b></td>
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
						echo "<td><a href='#' onclick=\"window.open('index.php?accion=consultar&class=EventoDAO&method=Ver&param=".$arr_vo->id."','','top=30,left=30,height=750,width=750,scrollbars=1');return false;\">Detalles</a></td>";

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

?>
