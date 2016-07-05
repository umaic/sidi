<?
/**
 * DAO de ReporteSemanal
 *
 * Contiene los métodos de la clase ReporteSemanal 
 * @author Ruben A. Rojas C.
 */

Class ReporteSemanalDAO {

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
	function ReporteSemanalDAO (){
		$this->conn = MysqlDb::getInstance();
		$this->tabla = "reporte_semanal";
		$this->columna_id = "id_rp_s";
		$this->columna_nombre = "titulo";
		$this->columna_order = "id_rp_s";
	}

	/**
	* Consulta los datos de una ReporteSemanal
	* @access public
	* @param int $id ID del ReporteSemanal
	* @return VO
	*/	
	function Get($id){
		$sql = "SELECT * FROM ".$this->tabla." WHERE ".$this->columna_id." = ".$id;
		$rs = $this->conn->OpenRecordset($sql);
		$row_rs = $this->conn->FetchObject($rs);

		//Crea un VO
		$reporte_semanal_vo = New ReporteSemanal();

		//Carga el VO
		$reporte_semanal_vo = $this->GetFromResult($reporte_semanal_vo,$row_rs);

		//Retorna el VO
		return $reporte_semanal_vo;
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
			$vo = New ReporteSemanal();
			//Carga el VO
			$vo = $this->GetFromResult($vo,$row_rs);
			//Carga el arreglo
			$array[] = $vo;
		}  
		//Retorna el Arreglo de VO
		return $array;
	}

	
	/**
  * Lista los registros en una Tabla
  * @access public
  */			
	function ListarTabla(){
		
        $a_fin = date('Y');
		$aaaa = (isset($_GET['aaaa'])) ? $_GET['aaaa'] : $a_fin;
        $condicion = "YEAR(f_ini) = $aaaa";
		$arr = $this->GetAllArray($condicion);

		echo "<table align='center' class='tabla_lista' width='800'>";
		
		echo "<tr><td colspan=3>";
		
		echo "Filtrar por A&ntilde;o :&nbsp;<select id='aaaa' onchange='?m_e=rp_s&accion=listar&class=ReporteSemanalDAO&method=ListarTabla&param=&aaaa=' + this.value class='select'>";
        for($i=2010;$i<=$a_fin;$i++){
            echo "<option value='$i'>$i</option>";
        }
        echo '</select>';
		
		echo "<tr class='titulo_lista'><td width='50' align='center'>ID</td><td>T&iacute;tulo</td></tr>";

		foreach ($arr as $p=>$vo){
			
			echo "<tr class='fila_lista'>";
			echo "<td>";
			echo "<a href='#'  onclick=\"if(confirm('Está seguro que desea borrar el reporte_semanal: ".$vo->titulo_esp."')){borrarRegistro('EspacioDAO','".$vo->id."')}else{return false};\"><img src='images/trash.png' border='0' title='Borrar' /></a>&nbsp;$vo->id</td>";
			echo "<td><a href='".$_SERVER['PHP_SELF']."?accion=actualizar&id=".$vo->id."'>".$vo->titulo_esp."</a></td>";

			echo "</tr>";
			
		}

		echo "</table>";
	}

    /**
     * Imprime en pantalla los datos del ReporteSemanal
     * @access public
     * @param object $vo ReporteSemanal que se va a imprimir
     * @param string $formato Formato en el que se listarán los ReporteSemanal, puede ser Tabla o ComboSelect
     * @param int $valor_combo ID del ReporteSemanal que será selccionado cuando el formato es ComboSelect
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
  * Carga un VO de ReporteSemanal con los datos de la consulta
  * @access public
  * @param object $vo VO de ReporteSemanal que se va a recibir los datos
  * @param object $Resultset Resource de la consulta
	* @return object $vo VO de ReporteSemanal con los datos
  */			
	function GetFromResult ($vo,$Result){

		$vo->id = $Result->{$this->columna_id};
        $vo->titulo_esp = $Result->titulo_esp;
        $vo->titulo_ing = $Result->titulo_ing;
        $vo->destacado_esp = $Result->destacado_esp;
        $vo->contenido_esp = $Result->contenido_esp;
        $vo->destacado_ing = $Result->destacado_ing;
        $vo->contenido_ing = $Result->contenido_ing;
        $vo->f_ini = $Result->f_ini;
        $vo->f_fin = $Result->f_fin;
        $vo->trend_f_ini = $Result->trend_f_ini;
		
        return $vo;
	}

	/**
  * Inserta un ReporteSemanal en la B.D.
  * @access public
  * @param object $reporte_semanal_vo VO de ReporteSemanal que se va a insertar
  */		
	function Insertar($vo){
		
        //CONSULTA SI YA EXISTE EL REPORTE EN LA SEMANA
		$tmp = $this->GetAllArray("f_ini = '$vo->f_ini'");

        if (count($tmp) == 0){
				
            $sql = "INSERT INTO $this->tabla (titulo_esp,titulo_ing,destacado_esp,contenido_esp,destacado_ing,contenido_ing,f_ini,f_fin,trend_f_ini) VALUES ('$vo->titulo_esp','$vo->titulo_ing','$vo->destacado_esp','$vo->contenido_esp','$vo->destacado_ing','$vo->contenido_ing','$vo->f_ini','$vo->f_fin','$vo->trend_f_ini')";                
            $this->conn->Execute($sql);

            $vo->id = $this->conn->GetGeneratedID();
            $this->genRP_S($vo);

            echo "Registro insertado con &eacute;xito!";
            ?>
            <script>
                alert("Reporte generado con \xe9xito!");
                location.href = '?m_e=rp_s&accion=listar&class=ReporteSemanalDAO&method=ListarTabla&param=';
            </script>
            <?
        }
        else{
            echo "Error - Existe un registro con el mismo nombre";
		}
        

	}

    /**
     * Actualiza un ReporteSemanal en la B.D.
     * @access public
     * @param object $reporte_semanal_vo VO de ReporteSemanal que se va a actualizar
     */		
    function Actualizar($vo){
        $sql = "UPDATE $this->tabla SET 
        titulo_esp = '$vo->titulo_esp',
        titulo_ing = '$vo->titulo_ing',
        destacado_esp = '$vo->destacado_esp',
        contenido_esp = '$vo->contenido_esp',
        destacado_ing = '$vo->destacado_ing',
        contenido_ing = '$vo->contenido_ing',
        f_ini = '$vo->f_ini',
        f_fin = '$vo->f_fin',
        trend_f_ini = '$vo->trend_f_ini' 

        WHERE $this->columna_id = $vo->id";
        $this->conn->Execute($sql);
        $this->genRP_S($vo);
            
        ?>
        <script>
            alert("Reporte actualizado con \xe9xito!");
            location.href = '?m_e=rp_s&accion=listar&class=ReporteSemanalDAO&method=ListarTabla&param=';
        </script>
        <?

    }

    /**
     * Borra un ReporteSemanal en la B.D.
     * @access public
     * @param int $id ID del ReporteSemanal que se va a borrar de la B.D
     */	
    function Borrar($id){

		//BORRA
		$sql = "DELETE FROM ".$this->tabla." WHERE ".$this->columna_id." = ".$id;
		$this->conn->Execute($sql);

	}

    /**
     * Genera los archivos para el ReporteSemanal en la B.D.
     * @access public
     * @param object $vo Reporte Object
     */	
    function genRP_S($vo){

        include($_SERVER['DOCUMENT_ROOT'].'/sissh/admin/lib/common/googleChart.php');
        $w_d = $_SERVER['DOCUMENT_ROOT'].'/sissh';

        $archivo = new Archivo();
        $depto_dao = new DeptoDAO();
        $tag_img_dep = '###dep###';
        $tag_img_cat = '###cat###';
        $tag_img_trend = '###trend###';
        $mes_corto_ing = array('','Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec');
        $mes_corto_esp = array('','Ene','Feb','Mar','Abr','May','Jun','Jul','Ago','Sep','Oct','Nov','Dic');
        $titulo_esp = $vo->titulo_esp;
        $titulo_ing = $vo->titulo_ing;
        
        $cat_pie = array(array(9,43), array(2), array(18), array('4'), array(21), array(18) );
        $cat_pie_esp = array('Homicidios','Ataques a población civil','Amenazas','APM/UXO/IEA Víctimas','Masacres','Herida/muerte de civil en a.a');
        $cat_pie_ing = array('Homicides','Attacks on civilians','Threads','APM/UXO/IEA Victims','Massacres','Civilians dead/injured in a.a');
        
        $cat_mapa = array(array(5,7,1,2,4,8,3,6), 
                        array(18,15,22,23,12,10,9,11,21,48,13,14,16,20,1719), 
                        array(28,29,27,25,24,26,30), 
                        array('47,31,32'), 
                        array(42,40,38,36,35,39,37,41), 
                        array(46,45,44,43,49));

        $cat_mapa_esp = array('Acciones Bélicas','Ataques a población civil','Ataque a objetivos ilícitos de guerra','APM/UXO/IEA Víctimas','Desplazamiento','Categorías Complementarias');
        $cat_mapa_ing = array('Armed Confrontation','Attacks on civilians','Attack','APM/UXO/IEA Victims','Mass Displacement','');
        $color_cat_mapa = array('#FF0000','#ff7f00','#a02860','#00bf32','#a61300','#FFDD00','#BFAC30');
        
        /* Parametros Google charts */
        $chart_w = 900;
        $chart_h = 300;
        $chart_w_i = 1100;
        $chart_h_i = 400;
        $font_size = 11;
        $title_font_size = 18;

		$filtro_fecha = " FECHA_REG_EVEN BETWEEN '$vo->f_ini' AND '$vo->f_fin'";

        /*********************************
        //Eventos por departamento
        ************************************/
        $chart_title_esp = 'Eventos por Departamento';
        $chart_title_ing = 'Events per Departament';
        $sql = "SELECT count(municipio.id_depto) as num, municipio.id_depto FROM evento_c 
                INNER JOIN evento_localizacion ON evento_c.id_even = evento_localizacion.id_even 
                INNER JOIN municipio ON evento_localizacion.id_mun = municipio.id_mun
                INNER JOIN descripcion_evento ON evento_c.id_even = descripcion_evento.id_even
                WHERE $filtro_fecha";

        $sql .= " GROUP BY municipio.id_depto ORDER BY num DESC";
        
        $draw_charts_esp = "data_dep.addColumn('string', 'Fecha');\ndata_dep.addColumn('number', 'Eventos');\n";
        $draw_charts_ing = "data_dep.addColumn('string', 'Date');\ndata_dep.addColumn('number', 'Events');\n";
        
        $rs = $this->conn->OpenRecordset($sql);
        $i = 0;
        $draw_charts = '';
        while ($row = $this->conn->FetchObject($rs)){
            $depto = $depto_dao->Get($row->id_depto);
            
            $label = str_replace(array('Norte de Santander','Valle del Cauca'),array('N. Santander','V. Cauca'),$depto->nombre);
            $label_i = $label;
            $label = utf8_encode($label);
            $data['values'][] = $row->num;
            
            $value_idx_2 = $label;
            $value_idx_0 = '';
            if (fmod($i,2) == 0){
                $value_idx_0 = $label;
                $value_idx_2 = '';
            }
            $data['labels_0'][] = $value_idx_0;
            $data['labels_2'][] = $value_idx_2;
            $draw_charts .= "data_dep.addRow(['$label_i',$row->num]);\n";
            $i++;
        }

        
        $max_y = 1.5*max($data['values']);
        $barChart = new gBarChart(900,300);
        $barChart->setTitle($chart_title_ing);
        $barChart->setTitleOptions('000000',20);
        $barChart->addDataSet($data['values']);
        $barChart->setVisibleAxes(array('x','y','x'));
        $barChart->addAxisLabel(0, $data['labels_0']);
        $barChart->addAxisLabel(2, $data['labels_2']);
        $barChart->setAxisLabelStyle(0,'000000',9);
        $barChart->setAxisLabelStyle(2,'000000',9);
        $barChart->addAxisRange(1,0,$max_y);
        $barChart->setDataRange(0,$max_y);
        $barChart->setGridLines(0,20,1,5);
        $barChart->addValueMarkers('N','000000',0,-1,11);
        ob_start();
        $barChart->getImgCode();
        $img_dep = ob_get_clean();

        
        /**** Interactive Chart ****/
        $draw_charts .= "var chart = new google.visualization.ColumnChart(document.getElementById('dep_div'));\n
        chart.draw(data_dep, { width: $chart_w_i, height: $chart_h_i, title: '###ttt###', fontSize: $font_size, legend: 'none', titleFontSize: $title_font_size });\n";

        $draw_charts_esp .= str_replace('###ttt###',$chart_title_esp,$draw_charts);
        $draw_charts_ing .= str_replace('###ttt###',$chart_title_ing,$draw_charts);
        
        /****************************
        //Eventos por categoria
        *****************************/
        $data = array();
        $chart_title_esp = 'Eventos por Categoría';
        $chart_title_ing = 'Events per Category';
        $draw_charts_esp .= "data_cat.addColumn('string', 'Categoria');\ndata_cat.addColumn('number', 'Eventos');\n";
        $draw_charts_ing .= "data_cat.addColumn('string', 'Category');\ndata_cat.addColumn('number', 'Events');\n";
        foreach ($cat_pie as $i => $id_scats){
            $sql_cat = "SELECT COUNT(ID_EVEN) AS num FROM evento_c
					    JOIN descripcion_evento USING(id_even)
					    WHERE id_scateven IN (".implode(',',$id_scats).") AND $filtro_fecha";

            $rs = $this->conn->OpenRecordset($sql_cat);
            $row = $this->conn->FetchObject($rs);
            $data['labels'][] = $cat_pie_ing[$i];
            $data['values'][] = $row->num;
            
            $tmp = "data_cat.addRow(['###lll###',$row->num]);\n";
            
            $draw_charts_esp .= str_replace('###lll###',$cat_pie_ing[$i],$tmp);
            $draw_charts_ing .= str_replace('###lll###',$cat_pie_esp[$i],$tmp);

        }
        
        $pieChart = new gPieChart(800,300);
        $pieChart->setTitle('Events per Category');
        $pieChart->setTitleOptions('000000',20);
        $pieChart->addDataSet($data['values']);
        $pieChart->setLabels($data['labels']);
        $pieChart->setColors(array('ffad00','133cac','bf9130','2b4281','a67000','062270','ffc140','476dd5'));
        ob_start();
        $pieChart->getImgCode();
        $img_cat = ob_get_clean();
        
        /**** Interactive Chart ****/
        $draw_charts = "var chart = new google.visualization.PieChart(document.getElementById('cat_div'));\n
        chart.draw(data_cat, { width: 1000, height: 500, title: '###ttt###', fontSize: $font_size, titleFontSize: $title_font_size });\n";
        
        $draw_charts_esp .= str_replace('###ttt###',$chart_title_esp,$draw_charts);
        $draw_charts_ing .= str_replace('###ttt###',$chart_title_ing,$draw_charts);

        /*********************
        // Trend Week
        **********************/
        $data = array();
        $chart_title_ing = 'Events - Weekly trend *';
        $chart_title_esp = 'Eventos - Tendencia semanal *';

        $draw_charts_esp .= "data_trend.addColumn('string', 'Fecha');\ndata_trend.addColumn('number', 'Eventos');\n";
        $draw_charts_ing .= "data_trend.addColumn('string', 'Date');\ndata_trend.addColumn('number', 'Events');\n";
        
        $sql_trend = "SELECT COUNT(ID_EVEN) AS num, MONTH(FECHA_REG_EVEN) AS mes, 
                        DAY(DATE_SUB(FECHA_REG_EVEN, INTERVAL DAYOFWEEK(FECHA_REG_EVEN) -2 DAY)) AS dia_ini, 
                        MONTH(DATE_SUB(FECHA_REG_EVEN, INTERVAL DAYOFWEEK(FECHA_REG_EVEN) -2 DAY)) AS mes_ini, 
                        DAY(DATE_SUB(FECHA_REG_EVEN, INTERVAL DAYOFWEEK(FECHA_REG_EVEN) -2 DAY) + INTERVAL 6 DAY) AS dia_fin, 
                        MONTH(DATE_SUB(FECHA_REG_EVEN, INTERVAL DAYOFWEEK(FECHA_REG_EVEN) -2 DAY) + INTERVAL 6 DAY) AS mes_fin 
                        FROM evento_c WHERE FECHA_REG_EVEN BETWEEN '$vo->trend_f_ini' AND '$vo->f_fin' GROUP BY WEEK(FECHA_REG_EVEN,3)";
        //echo $sql_trend;
        $i = 0;
        $rs = $this->conn->OpenRecordset($sql_trend);
        while ($row = $this->conn->FetchObject($rs)){
            $d_i = $row->dia_ini;
            $d_f = $row->dia_fin;
            $m_i = $mes_corto_ing[$row->mes_ini];
            $m_f = ($row->mes_fin > $row->mes_ini ) ? $mes_corto_ing[$row->mes_fin] : '';
            $label_ing = "$m_i $d_i-$m_f$d_f";
            $m_i = $mes_corto_esp[$row->mes_ini];
            $m_f = ($row->mes_fin > $row->mes_ini ) ? $mes_corto_esp[$row->mes_fin] : '';
            $label_esp = "$m_i $d_i-$m_f$d_f";
            
            $data['values'][$i] = $row->num;

            $value_idx_2 = $label;
            $value_idx_0 = '';
            if (fmod($i,2) == 0){
                $value_idx_0 = $label_ing;
                $value_idx_2 = '';
            }
            $data['labels_0'][] = $value_idx_0;
            $data['labels_2'][] = $value_idx_2;
            
            $tmp = "data_trend.addRow(['###lll###',$row->num]);\n";
            
            $draw_charts_esp .= str_replace('###lll###',$label_esp,$tmp);
            $draw_charts_ing .= str_replace('###lll###',$label_ing,$tmp);
            
            $i++;
        }
        
        /**** Image Chart ****/
        $max_y = 1.5*max($data['values']);
        $grid_step_x = 100 / $i;
        $grid_step_y = $max_y / 10;
        $lineChart = new gLineChart($chart_w,$chart_h);
        $lineChart->setTitle($chart_title_ing);
        $lineChart->setTitleOptions('000000',20);
        $lineChart->addDataSet($data['values']);
        $lineChart->setGridLines($grid_step_x,$grid_step_y,1,5);
        $lineChart->addAxisRange(1,0,$max_y);
        $lineChart->setDataRange(0,$max_y);
        $lineChart->setVisibleAxes(array('x','y','x'));
        $lineChart->addAxisLabel(0, $data['labels_0']);
        $lineChart->addAxisLabel(2, $data['labels_2']);
        $lineChart->addValueMarkers('N','000000',0,-1,11);
        $lineChart->setAxisLabelStyle(0,'000000',9);
        $lineChart->setAxisLabelStyle(1,'000000',11);
        $lineChart->setAxisLabelStyle(2,'000000',9);

        ob_start();
        $lineChart->getImgCode();
        $img_trend = ob_get_clean();
        
        /**** Interactive Chart ****/
        $draw_charts = "var chart = new google.visualization.LineChart(document.getElementById('trend_div'));\n
        chart.draw(data_trend, { width: $chart_w_i, height: $chart_h_i, title: '###ttt###', fontSize: $font_size, legend: 'none', titleFontSize: $title_font_size });\n";
        
        $draw_charts_esp .= str_replace('###ttt###',$chart_title_esp,$draw_charts);
        $draw_charts_ing .= str_replace('###ttt###',$chart_title_ing,$draw_charts);

        // Define destacados
        $destacado_esp = $vo->destacado_esp;
        $destacado_ing = $vo->destacado_ing;

        // Coloca las imagenes en el contenido

        $div_dep = "<div id='dep_div'></div>";
        $div_cat = "<div id='cat_div'></div>";
        $div_trend = "<div id='trend_div'></div>";
        $contenido_esp = str_replace(array($tag_img_dep,$tag_img_cat,$tag_img_trend),array($img_dep.$div_dep,$img_cat.$div_cat,$img_trend.$div_trend),$vo->contenido_esp);
        $contenido_ing = str_replace(array($tag_img_dep,$tag_img_cat,$tag_img_trend),array($img_dep.$div_dep,$img_cat.$div_cat,$img_trend.$div_trend),$vo->contenido_ing);

        // Crea directorio y archivos cache
        $w_d_id = "$w_d/rp_s/$vo->id";

        if (!$archivo->Existe($w_d_id))    $archivo->crearDirectorio($w_d_id);
        
        /****************************
        //Mapa
        *****************************/
        $radio = 5;
        foreach ($cat_mapa as $i => $id_scats){
            $map_json_a = array();
            $map_json_a['type'] = 'FeatureCollection';
            
            $sql_cat = "SELECT evento_c.id_even AS id,nom_mun,sintesis_even AS descripcion,x_map,y_map FROM evento_c
					    JOIN descripcion_evento USING(id_even) JOIN evento_localizacion USING (id_even)
                        JOIN municipio USING(id_mun)
					    WHERE id_scateven IN (".implode(',',$id_scats).") AND $filtro_fecha LIMIT 0,10";

            $rs = $this->conn->OpenRecordset($sql_cat);
            while ($row = $this->conn->FetchObject($rs)){

                $desc = utf8_encode($row->descripcion);
                $map_json_a['features'][] = array('type' => 'Feature',
                                                'properties' => array('color' => $color_cat_mapa[$i], 'total' => '', 'radius' => $radio, 'desc' => $desc),
                                                'geometry' => array('type' => 'point', 'coordinates' => array($row->x_map,$row->y_map)));

            } 
            
            $map_json = json_encode($map_json_a);
            $a = "map_json_cat_$i";
            $f = $archivo->Abrir("$w_d_id/$a.htm",'w+');
            $archivo->Escribir($f,$map_json);
            $archivo->Cerrar($f);
            
        }
        
        $archs = array('titulo_ing','titulo_esp','contenido_esp','contenido_ing','destacado_esp','destacado_ing','draw_charts_esp','draw_charts_ing');
        foreach ($archs as $a){
            $f = $archivo->Abrir("$w_d_id/$a.htm",'w+');
            $archivo->Escribir($f,$$a);
            $archivo->Cerrar($f);
        }
    }
}

?>
