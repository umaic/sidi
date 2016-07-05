<?
/**
 * DAO de Sissh
 *
 * Contiene los metodos de la clase Sissh
 * @author Ruben A. Rojas C.
 */

Class SisshDAO {

	/**
	* Conexion a la base de datos
	* @var object
	*/
	var $conn;

	/**
	* Directorio donde se guarda cache
	* @var string
	*/
	var $dir_cache_perfil;

	/**
	* Constructor
	* Crea la conexion a la base de datos
	* @access public
	*/
	function SisshDAO (){
		$this->conn = MysqlDb::getInstance();
		$this->dir_cache_static = $_SERVER["DOCUMENT_ROOT"]."/sissh/static/";  //relativo a sissh/index.php
		$this->dir_cache_perfil = $_SERVER["DOCUMENT_ROOT"]."/sissh/perfiles";  //relativo a sissh/index.php
		$this->dir_cache_resumen_desplazamiento = $_SERVER["DOCUMENT_ROOT"]."/sissh/consulta/resumen/desplazamiento";  //relativo a sissh/index.php
		$this->dir_images_perfil = "consulta/pdf";  //relativo a sissh/index.php
		$this->dir_cache_images_perfil = $_SERVER["DOCUMENT_ROOT"]."/sissh/consulta/pdf";  //relativo a sissh/index.php
		$this->url_images = "consulta/pdf";  //relativo a sissh/index.php

	}

    /**
    * Minificha V2
    * @access public
    * @param string $id_depto
    * @param string $id_mpio
    */
    function MinifichaV2($id_depto,$id_mpio,$formato){

        set_time_limit(0);

        $meses = array('','Enero','Febrero','Marzo','Abril','Mayo','Junio','Julio','Agosto','Septiembre','Octubre','Noviembre','Diciembre');

        //LIBRERIAS
        $depto_dao = New DeptoDAO();
        $mun_dao = New MunicipioDAO();
        $archivo = New Archivo();
        $periodo_dao = New PeriodoDAO();
        $fuente_des_dao = New FuenteDAO();
        $tipo_des_dao = New TipoDesplazamientoDAO();
        $des_dao = New DesplazamientoDAO();
        $sexo_dao = New SexoDAO();
        $condicion_dao = New CondicionMinaDAO();
        $estado_mina_dao = New EstadoMinaDAO();
        $edad_dao = New EdadDAO();
        $tipo_org_dao = New TipoOrganizacionDAO();
        $sector_dao = New SectorDAO();
        $sub_fuente_dao = New SubFuenteEventoConflictoDAO();
        $d_s_dao = New DatoSectorialDAO();
        $tema_dao = New TemaDAO();
        $org_dao = New OrganizacionDAO();

        $monitor_url = 'http://monitor.local';
		$sidih_url = 'http://sidih.local/sissh';

        // Estilos
        $bg_gris = '#f1f1f1';

        //INICIALIZACION DE VARIABLES
        $file = New Archivo();
        $_SESSION["pdfcode"] = "";

        //Nacional
        if ($id_depto == 0 && $id_mpio == 0){
            $id_ubicacion = '00';
            $dato_para = 3;

            $nombre_ubicacion = 'Perfil nacional';
        }
        else{
            $id_ubicacion = $id_depto;
            $ubicacion = $depto_dao->Get($id_ubicacion);
            $depto_ubicacion = $ubicacion;
            $dato_para = 1;
            $depto_nombre = $ubicacion->nombre;

            $nombre_ubicacion = strtoupper($depto_nombre). ': Perfil departamental';

            if ($id_mpio != 0){
                $id_ubicacion = $id_mpio;
                $ubicacion = $mun_dao->Get($id_ubicacion);
                $dato_para = 2;
                $depto_ubicacion = $depto_dao->Get($ubicacion->id_depto);

                $nombre_ubicacion = strtoupper($ubicacion->nombre.' - '.$depto_nombre).': Perfil departamental';
            }
        }

        //Para almacenar cache html y pdf
        $path_file = $this->dir_cache_perfil."/perfil_$id_ubicacion";

        //Para almacenar las imagenes por ubicacion
        $path_images = $this->dir_images_perfil."/$id_ubicacion";
        $real_path_images = $_SERVER["DOCUMENT_ROOT"]."/sissh/".$path_images;

        //url de las imagenes, dir completa, para que funcione cuando se incluye desde otro servidor
        $url_images = $this->url_images."/$id_ubicacion";

        //check si existe el directorio
        if (!$archivo->Existe($real_path_images)){
            $archivo->crearDirectorio($real_path_images);
        }

        //MANEJO DE CACHE
        //$cache_file = "$path_file.htm";
        $cache_file = "$path_file.json";

        $gen_perfil = $this->siGenerarPerfil($cache_file);
        //$gen_perfil = 1;
        //
        if ($gen_perfil == 0) {
           return json_decode(file_get_contents($cache_file), true);
        }
        else {

            // DATOS DE AFECTACION DESDE MONITOR

            // Equivalencia divipola => violencia_armada.state.id
            $states = array('05' => 1, '08' => 2, '11' => 3, '13' => 4, '15' => 5, '17' => 6, '18' => 7,
                '19' => 8, '20' => 9, '23' => 10, '25' => 11, '27' => 12, '41' => 13,
                '44' => 14, '47' => 15, '50' => 16, '52' => 17, '54' => 18, '63' => 19, '66' => 20, '68' => 21,
                '70' => 22, '73' => 23, '76' => 24, '81' => 25
                );

            $state_id = $states[$id_ubicacion];

            // Periodo, despues del primer trimestre se hace el a�o actual
            // de lo contrario todo el a�o anterior

            $y = date('Y');
            $y_ant = $y - 1;
            $primer_dia = strtotime(date('Y-01-01'));
            $hoy = time();

            if (ceil(($hoy - $primer_dia)/86400) < 90) {
                $ini = strtotime(date($y_ant.'-01-01'));
                $fin = strtotime(date($y_ant.'-12-31'));
                $y_txt = $y_ant;
                $m_txt = 12;
                $d_txt = 31;
            }
            else {
                $ini = $primer_dia;
                $fin = $hoy;
                $y_txt = $y;
                $m_txt = date('n');
                $d_txt = date('j');
            }

            $periodo_txt = '1 Enero a '.$d_txt.' '.$meses[$m_txt].' '.$y_txt;

            $url = $monitor_url."/getIncidentesPortal/$ini/$fin/0/0/$state_id";

            $ch = curl_init();

            curl_setopt($ch, CURLOPT_HEADER, 0);
            curl_setopt($ch, CURLOPT_TIMEOUT, 10);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); //Set curl to return the data instead of printing it to the browser.
            curl_setopt($ch, CURLOPT_URL, $url);

            $afectacion = json_decode(curl_exec($ch));
            curl_close($ch);

            // DATOS DE 4W CARGADOS DESDE API DE SIDIH
            $url = $sidih_url."/api/4w.php?mod=totales&_c=s1dicol-api&yyyy=$y_txt";
			//echo $url;

            $ch = curl_init();

            curl_setopt($ch, CURLOPT_HEADER, 0);
            curl_setopt($ch, CURLOPT_TIMEOUT, 10);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); //Set curl to return the data instead of printing it to the browser.
            curl_setopt($ch, CURLOPT_URL, $url);

            $respuesta = json_decode(curl_exec($ch), true);
            curl_close($ch);

            /********** INICIO PERFIL **************/

            //Espacio entre una grafica en una td y la otra para el PDF
            $space_td_pdf = 5;

            //MAPA INICIAL
            if ($dato_para == 2){
                $vo = $mun_dao->Get($id_ubicacion);

                //Check si existe el mapa del mpio
                if ($archivo->Existe('images/minificha/'.$id_ubicacion.'.png')){
                    $mapa_inicial = "$id_ubicacion.png";
                }
                else{
                    $id_ubicacion_depto = $vo->id_depto;
                    $mapa_inicial = "$id_ubicacion_depto.png";
                }
            }
            else{
                $mapa_inicial = "$id_ubicacion.png";
            }

            //Carga el contenido del cache
            if ($gen_perfil == 0 && $formato == 'html'){

                $fp = $archivo->Abrir($cache_file,'r');

                echo $archivo->LeerEnString($fp,$cache_file);
            }
            else{
                //Fecha
                $hoy = getdate();
                $fecha = $hoy['mday']."-".$hoy['mon']."-".$hoy['year'];

                /*** CONTEXTO GENERAL **/
                $id_datos = array('Demografia' => array(array(1,2,3,345), true, false),
                                'Geograf&iacute;a' => array(array(589), true, false),
                                'Indices compuestos' => array(array(707,704,703), true, false)
                                );

                $generales['iz'] = $this->htmlDatosV2($id_datos, $dato_para, $ubicacion, $depto_ubicacion);

                // Derecha
                $datos = array(3 => 'poblacion',
                                311 => 'afros',
                                130 => 'indigenas',
                                308 => 'sin_etnia'
                            );

                /*
                $id_dato = 3;
                $fecha_val = $d_s_dao->GetMaxFecha($id_dato);
                $valor = $d_s_dao->GetValorToReport($id_dato,$id_ubicacion,$fecha_val['ini'],$fecha_val['fin'],$dato_para);
                $poblacion = $val['valor'];

                // Porcentaje hombres
                $id_dato = 694;
                $fecha_val = $d_s_dao->GetMaxFecha($id_dato);
                $valor = $d_s_dao->GetValorToReport($id_dato,$id_ubicacion,$fecha_val['ini'],$fecha_val['fin'],$dato_para);
                $hombres = $val['valor'];
                $hombres_procentaje = ($hombres / $poblacion ) * 100;

                // Porcentaje mujeres
                $id_dato = 695;
                $fecha_val = $d_s_dao->GetMaxFecha($id_dato);
                $valor = $d_s_dao->GetValorToReport($id_dato,$id_ubicacion,$fecha_val['ini'],$fecha_val['fin'],$dato_para);
                $mujeres = $val['valor'];
                $mujeres_procentaje = ($mujeres / $poblacion ) * 100;
                */

                /*** CONTEXTO HUMANITARIO **/
                $id_datos = array('Indicador compuesto' => array(array(705,707), true, false)
                                );

                $hum_necesidades = $this->htmlDatosV2($id_datos, $dato_para, $ubicacion, $depto_ubicacion);



                // Impacto Humanitario
                $id_datos = array('' => array(array(587,642,675,384), false, true));
                $hum_impacto_violencia = $this->htmlDatosV2($id_datos, $dato_para, $ubicacion, $depto_ubicacion,'Datos generales');

                $hum_impacto_violencia .= '
                    <div>
                    <h3 style="margin-bottom:0 !important;">
                    Total personas afectadas: '.number_format($afectacion->t->ec,0,'',',').'
                    </h3>
                    <small><i>'.$periodo_txt.'</i></small>
                    </div>
                    <div class="row">
                    <div class="col-md-5">';

                if (!empty($afectacion->charts[2]->data)) {

                    $hum_impacto_violencia .= '
                    <!-- Totales por caracterizacion -->
                    <h4>G&eacute;nero</h4>
                    <table class="table table-bordered">';

                    $v = array('Masculino' => 0,
                                'Femenino' => 0,
                               'Sin dato' => 0);

                    foreach ($afectacion->charts[2]->data as $d) {

                        list($t, $n) = $d;

                        if ($t == 'Masculino') {
                            $v['Masculino'] = $n;
                        }
                        else if ($t == 'Femenino') {
                            $v['Femenino'] += $n;
                        }
                        else {
                            $v['Sin dato'] += $n;
                        }
                    }

                    foreach($v as $t => $n) {
                        $n = number_format($n,0,'',',');

                        $hum_impacto_violencia .= '<tr>
                            <td>&nbsp;&nbsp;&nbsp;'.$t.'</td>
                            <td>'.$n.'</td>
                            </tr>';
                    }

                    $hum_impacto_violencia .= '</table>';
                }

                if (!empty($afectacion->charts[3]->data)) {

                    $hum_impacto_violencia .= '<h4>Grupo et&aacute;reo</h4><table class="table table-bordered">';

                    foreach ($afectacion->charts[3]->data as $d) {

                        list($t, $n) = $d;

                        if ($t == 'Desconocido') {
                            $v['Sin dato'] = $n;
                        }

                        $n = number_format($n,0,'',',');

                        $hum_impacto_violencia .= '<tr>
                            <td>&nbsp;&nbsp;&nbsp;'.$t.'</td>
                            <td>'.$n.'</td>
                            </tr>';
                    }

                    $hum_impacto_violencia .= '</table>';
                }

                if (!empty($afectacion->charts[1]->data)) {

                    $hum_impacto_violencia .= '<h4>Minorias &eacute;tnicas</h4><table class="table table-bordered">';

                    // Recorre el arreglo y extrae Ind, Afr, Otros y sin dato
                    $v = array('AfroColombiano' => 0,
                                'Indigena' => 0,
                                'Otro' => 0,
                               'Sin dato' => 0);
                    foreach ($afectacion->charts[1]->data as $d) {

                        list($t, $n) = $d;

                        if ($t == 'AfroColombiano') {
                            $v['AfroColombiano'] = $n;
                        }
                        else if ($t == utf8_encode('Ind�gena')) {
                            $v['Indigena'] = $n;
                        }
                        else if ($t == 'Otro') {
                            $v['Otro'] += $n;
                        }
                        else {
                            $v['Sin dato'] += $n;
                        }
                    }

                    foreach($v as $t => $n) {
                        $n = number_format($n,0,'',',');

                        $hum_impacto_violencia .= '<tr>
                                <td>&nbsp;&nbsp;&nbsp;'.$t.'</td>
                                <td>'.$n.'</td>
                                </tr>';
                    }

                    $hum_impacto_violencia .= '</table>';

                }

                $hum_impacto_violencia .= '</div>
                    <div class="col-md-7">
                    <h4>Total por categor&iacute;a, top 5</h4>
                    <table class="table table-bordered">';

                        $s = 0;
                        for ($i=0;$i<5;$i++) {
                            $n = $afectacion->rsms_ec[$i]->n;
                            $hum_impacto_violencia .= '<tr>
                                    <td>'.$afectacion->rsms_ec[$i]->t.'</td>
                                    <td>'.number_format($n,0,'',',').'</td>
                                    </tr>';
                            $s += $n;
                        }

                        $hum_impacto_violencia .= '</table>
                        </div></div>';

                $html = '
                <!-- Desastres naturales -->
                <div>
                <h3 style="margin-bottom:0 !important;">
                Total personas afectadas: '.number_format($afectacion->t->dn,0,'',',').'
                </h3>
                <small><i>'.$periodo_txt.'</i></small>
                </div>
                <br /><h4>Total por categor&iacute;a, top 5</h4>
                <table class="table table-bordered">';

                $s = 0;
                for ($i=0;$i<5;$i++) {
                    $r = $afectacion->rsms_dn[$i];
                    $n = $r->n;
                    $html .= '<tr>
                            <td>'.$r->t.'</td>
                            <td>'.number_format($n,0,'',',').'</td>
                            </tr>';
                    $s += $n;
                }

                $html .= '</table>';

                $hum_impacto_desastres = $html;

                $hum_respuesta = '<h4>Proyectos ejecutados '.$y_txt.'</h4>
                <table class="table table-bordered">';

                $block =  "<div style='margin-bottom: 10px'><div class='left'><img src='http://sidi.umaic.org/sissh/images/ocha_icons/%s.png' style='float:left'></div>
                        <div><span style='font-size:18px;'><b>%s</b></span><span style='font-size:11px;'>&nbsp;&nbsp;(EHP: %s)</span></div>
                        <div><span font-size:11x;>%s</span></div></div>";


                $hum_respuesta .= "<tr><td>".sprintf($block, 'reporting_red', $respuesta['proyectos'], $respuesta['proyectos_ehp'], 'N&uacute;mero total de proyectos')."</td></tr>".
                        "<tr><td>".sprintf($block, 'affected_population_red', number_format($respuesta['beneficiarios'],0,'',','), number_format($respuesta['beneficiarios_ehp'],0,'',','), 'N&uacute;mero de beneficiarios directos')."</td></tr>".
                        "<tr><td>".sprintf($block, 'fund_red', number_format($respuesta['presupuesto'],0,'',','), number_format($respuesta['presupuesto_ehp'],0,'',','), 'Presupuesto U$')."</td></tr>".
                        "<tr><td>".sprintf($block, 'house_red', number_format($respuesta['organizaciones'],0,'',','), number_format($respuesta['organizaciones_ehp'],0,'',','), 'Total de organizaciones ejecutoras')."</td></tr>";

                $hum_respuesta .= '</table>';

                $hum_respuesta .= '<h4>Top Cluster/Sector por presupuesto</h4>
                <table class="table table-bordered">';

                arsort($respuesta['presupuesto_tema']);
                $pts = array_slice($respuesta['presupuesto_tema'],0,5,true);

                $i = 1;
                foreach($pts as $tema_id => $pt) {
                    $tema_nom = $tema_dao->GetName($tema_id);
                    $ptp = (!empty($pt)) ? number_format(($respuesta['presupuesto'] / $pt) * 100,2,'',',') : 0;

                    $hum_respuesta .= "<tr><td>$i. $tema_nom</td><td>".$ptp."<br />USD ".number_format($pt,0,'',',')."</td></tr>";

                    $i++;
                }

                $hum_respuesta .= '</table>';

                $hum_respuesta .= '<h4>Top donantes por presupuesto</h4>
                <table class="table table-bordered">';

                arsort($respuesta['presupuesto_donante']);
                $pds = array_slice($respuesta['presupuesto_donante'],0,5,true);
                $i = 1;
                foreach($pds as $donante_id => $pd) {

                    $donante_nom = ($donante_id > 0) ? $org_dao->GetName($donante_id) : 'Sin dato donante';

                    $pdp = (!empty($pd)) ? number_format(($respuesta['presupuesto'] / $pd) * 100,2,'.') : 0;

                    $hum_respuesta .= "<tr><td>$i. $donante_nom</td><td>".$pdp."<br />USD ".number_format($pd,0,'',',')."</td></tr>";

                    $i++;
                }

                $hum_respuesta .= '</table>';

                $info = compact('nombre_ubicacion',
                    'generales',
                    'hum_necesidades',
                    'hum_impacto_desastres',
                    'hum_impacto_violencia',
                    'hum_respuesta');

                //file_put_contents($cache_file, json_encode($info));

                return $info;

                die;
            }

            /*** DATOS SECTORIALES GENERALES ***/
            if (count(array_intersect(array(6,7,8,9,10),$mod_d_s)) > 0){

                //VARIABLES DE UBICACION DE GRAFICAS
                $y_enfermedades = 270;

                echo "<tr><td><a name=\"datos_generales\" href=\"#top\">^ Subir</a></td></tr>";
                echo "<tr><td class='pagina_minificha'>";
                echo $header_html;

                echo "<table cellpadding='0' cellspacing='0' width='920'>";
                echo "<tr><td class='titulo_pag_minificha'><b>Datos sectoriales generales</b></td><td align='right'><b>$ubicacion->nombre</b></td></tr>";
                echo "<tr><td colspan='2' class='linea_minificha'><img src='/sissh/images/spacer.png' height=1></td></tr>";
                echo "<tr><td>&nbsp;</td></tr>";

                echo "</table>";
                echo "<br><br><div align='center'>[ P&aacute;gina 2 - Datos Sectoriales Generales ]</div>";
            }

            /*** INFORMACION DEMOGRAFICA ***/
            if (in_array(1,$mod_d_s) || in_array(2,$mod_d_s) || in_array(3,$mod_d_s) || in_array(4,$mod_d_s) || in_array(5,$mod_d_s)){
                echo "</td></tr><tr><td>&nbsp;</td></tr>";
                echo "<tr><td><a name=\"informacion_demografica\" href=\"#top\">^ Subir</a></td></tr>";
                echo "<tr><td class='pagina_minificha'>";
                echo $header_html;
                echo "<table cellpadding='0' cellspacing='0' width='920' border=0>";
                echo "<tr><td class='titulo_pag_minificha'><b>Informaci&oacute;n Demogr&aacute;fica</b></td><td align='right'><b>$ubicacion->nombre</b></td></tr>";
                echo "<tr><td colspan='2' class='linea_minificha'><img src='images/spacer.gif' height='1'></td></tr>";

                $alto_rect = 25;

                //Cuadro de Fuente y poblaci�n total
                //POBLACION TOTAL
                //CONSULTA EL PERIODO MAS RECIENTE DEL VALOR DEL DATO
                $id_dato_pob = 3;
                $fecha_val = $d_s_dao->GetMaxFecha($id_dato_pob);
                $val = $d_s_dao->GetValorToReport($id_dato_pob,$id_ubicacion,$fecha_val['ini'],$fecha_val['fin'],$dato_para);
                $total_poblacion = $val['valor'];
                $total_poblacion = number_format($total_poblacion);

                echo "<tr><td align='right' colspan=2 bgcolor='#E1E1E1'>
                            <table cellpadding=0 cellspacing=0 width=460>
                                <tr><td align='right'><b>Total $ubicacion->nombre: $total_poblacion</b></td></tr>
                                <tr><td align='right'><b><b>Fuente: DANE, Censo 2005</b></td></tr>
                            </table>
                        </td></tr>";

                echo "<tr><td>&nbsp;</td></tr>";

                //echo "</table></td>";

                echo "</tr>";
                //echo "<tr><td colspan='2' class='linea_minificha'><img src='images/spacer.gif' height='1'></td></tr>";
                echo "<tr><td>&nbsp;</td></tr>";
                echo "<tr>";

                //PIRAMIDE POBLACIONAL
                if (in_array(11,$mod_d_s)){

                    //Poblacion hombres
                    //$id_dato_h = array(464,467,468,470,472,474,476,478,480);
                    $id_dato_h = array(464,467,468,696,697,698,699,700,480);

                    //Poblacion mujeres
                    $id_dato_m = array(465,466,469,471,473,475,477,479,481);

                    //Rangos de edad
                    $redad = array('0-9','10-19','20-29','30-39','40-49','50-59','60-69','70-79','80+');

                    $PG = new PowerGraphic;
                    $PG->title = "Pir�mide Poblacional";
                    $PG->type = $tipo_grafica[$mod_d_s_tipo_g[11]];
                    $PG->skin      = 1;
                    $PG->axis_y = "Personas";
                    $PG->axis_x = "Grupos de Edad";
                    $PG->graphic_1 = "Hombres";
                    $PG->graphic_2 = "Mujeres";
                    $PG->border = 0;
                    $PG->texto_1 = " | Fuente Dane - Censo General $a_val";

                    $miles = 0;
                    $j = 0;
                    for($i=(count($id_dato_h)-1);$i>=0;$i--){

                        $PG->x[$j] = $redad[$i];

                        $id = $id_dato_h[$i];
                        //CONSULTA EL PERIODO MAS RECIENTE DEL VALOR DEL DATO
                        $fecha = $d_s_dao->GetMaxFecha($id);

                        $val = $d_s_dao->GetValorToReport($id,$id_ubicacion,$fecha['ini'],$fecha['fin'],$dato_para);
                        $valor = $val['valor'];

                        if ($valor == 'N.D.')	$valor = 0;

                        //Si exite un valor en el rango de los millones, pasa todo a miles
                        if (strlen(intval($valor)) > 7){
                            $valor = strlen($valor);
                            $valor /= 1000;
                            $miles = 1;
                        }

                        $PG->y[$j] = $valor;

                        //CONSULTA EL PERIODO MAS RECIENTE DEL VALOR DEL DATO
                        $id = $id_dato_m[$i];
                        //$fecha = $d_s_dao->GetMaxFecha($id);
                        $val = $d_s_dao->GetValorToReport($id,$id_ubicacion,$fecha['ini'],$fecha['fin'],$dato_para);

                        $valor = $val['valor'];
                        if ($valor == 'N.D.')	$valor = 0;

                        //Si exite un valor en el rango de los millones, pasa todo a miles
                        if (strlen(intval($valor)) > 7){
                            $valor /= 1000;
                            $miles = 1;
                        }

                        $PG->z[$j] = $valor;

                        $j++;

                    }

                    if (max($PG->y) > 0 || max($PG->z) > 0){

                        if ($miles == 1)	$PG->axis_y .= " (en miles)";

                        $img = $PG->create_graphic_minificha();
                        $width_img_2_11 = imagesx($img);
                        $img_b = $PG->fill_background($img,255,255,255);
                        imagepng($img_b,$path_images.'/2_11.png');

                        $img_web = $PG->draw_copyright($img);
                        $img_web = $PG->fill_background($img_web,250,250,250);
                        imagepng($img_web,$path_images.'/w_2_11.png');

                        //echo "<td class='td_grafica_borde_derecho'><img src='$url_images/w_2_11.png' border=0 /></td>";
                        echo "<td align='center'><img src='$url_images/w_2_11.png' border=0 /></td>";

                        //$img = $PG->create_graphic_minificha();

                        //$pdf->addPngFromFile("$path_images/2_11.png",$left,30,$width_img_2_11);
                        imagedestroy($img);
                    }

                    else{
                        echo "<td align='center'><br><b>Piramide Poblacional</b><br><br>No hay Datos disponibles</td>";
                    }
                }

                echo "</tr>";
                echo "</table>";
                echo "<br><br><div align='center'>[ P&aacute;gina 3 - Informaci&oacute;n Demogr&aacute;fica ]</div>";
            }



            echo "</tr></table>";
            echo "<br><br><div align='center'>[ P&aacute;gina 6 - Accidentes con Mina ]</div>";
            echo "</td></tr>";

            ?>

            </table>
            <?

            $content_cache = '<html xmlns="http://www.w3.org/1999/xhtml">
                            <head>
                            <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
                            <title>Sistema de Informaci&oacute;n Central OCHA - Colombia</title>
                            <link href="/sissh/style/consulta.css" rel="stylesheet" type="text/css" />
                            </head><body><div id="cont">';

            $content_cache .= '<br>
                                <table cellpadding="5" cellspacing="1">';


            $content_cache .= ob_get_contents()."</div></body></html>";


            //ARCHIVO CACHE
            $dir_cache_perfil = "perfiles/";
            $path_file = "$dir_cache_perfil/perfil_$id_ubicacion";

            //if ($formato == 'html'){
            if ($formato == 'html' && $id_ubicacion != '00'){
                //ob_end_flush();
            }
            else{
                //ob_end_clean();
            }

            //$pdfcode = //$pdf->ezOutput();

            if ($id_ubicacion != '00'){
                $this->createFileCache($content_cache,"$path_file.htm");
                //$this->createFileCache(//$pdfcode,"$path_file.pdf");
            }

            if ($formato == 'pdf') include('consulta/perfil.php');

        }  //Fin generar perfil - no cache
        //}
    }

    /**
     * Html datos V2
     *
     * @access public
     * @param string $id_datos
     */
    function htmlDatosV2($id_datos, $dato_para, $ubicacion, $depto_ubicacion, $titulo = ''){

        $d_s_dao = New DatoSectorialDAO();
        $fuente_dao = New ContactoDAO();
        $cat_dao = New CategoriaDatoSectorDAO();
        $id_ubicacion = $ubicacion->id;

        $html = '';
        $html = '<table class="table table-bordered"><tr>';


        $html .= "<th>".strtoupper($titulo)."</th>
                 <th>".$ubicacion->nombre."</th>";

        if ($dato_para == 2){
            $html .= "<th>".$depto_ubicacion->nombre."</th>";
        }

        $html .= "<th>Nacional</th></tr>";

        foreach ($id_datos as $cat_titulo => $datos) {

            list($datos_m, $categoria, $varias_fuentes) = $datos;

            $cat = ($categoria) ? strtoupper($cat_titulo) : '';

            foreach ($datos_m as $id_dato){
                $dato = $d_s_dao->Get($id_dato);

                $fuente_n = '';
                if (!empty($dato->id_contacto)) {
                    $fuente = $fuente_dao->Get($dato->id_contacto);
                    if (!empty($fuente)) {
                        $fuente_n = $fuente->nombre;
                    }
                }

                //CONSULTA EL PERIODO MAS RECIENTE DEL VALOR DEL DATO
                $fecha_val = $d_s_dao->GetMaxFecha($id_dato);

                //VALOR DATO
                $val = $d_s_dao->GetValorToReport($id_dato,$id_ubicacion,$fecha_val['ini'],$fecha_val['fin'],$dato_para);
                $valor = $val['valor'];
                $id_unidad = $val['id_unidad'];
                //APLICA FORMATO
                $valor = $d_s_dao->formatValor($id_unidad,$valor);

                //VALOR DEPARTAMENTAL CUANDO ES UN MPIO
                if ($dato_para == 2){
                    $val = $d_s_dao->GetValorToReport($id_dato,$depto_ubicacion->id,$fecha_val['ini'],$fecha_val['fin'],1);
                    $valor_nacional = $val['valor'];
                    $id_unidad = $val['id_unidad'];

                    //APLICA FORMATO
                    $valor_depto = $d_s_dao->formatValor($id_unidad,$valor_nacional);
                }

                //VALOR DATO NACIONAL
                $val = $d_s_dao->GetValorToReport($id_dato,$id_ubicacion,$fecha_val['ini'],$fecha_val['fin'],3);
                $valor_nacional = $val['valor'];
                $id_unidad = $val['id_unidad'];

                //APLICA FORMATO
                $valor_nacional = $d_s_dao->formatValor($id_unidad,$valor_nacional);

                //FECHA
                $fecha = $d_s_dao->GetMaxFecha($id_dato);
                $a = explode("-",$fecha["fin"]);

                $html .= "<tr><td><b>".$dato->nombre."</b>
                    <br /><small><i>Fuente: ".$fuente_n.",
                    A&ntilde;o:". $a[0]."</i></small></td>";

                $html .= "<td align='right'>$valor</td>";
                if ($dato_para == 2)	$html .= "<td align='right'>$valor_depto</td>";
                $html .= "<td align='right'>$valor_nacional</td>";

                $html .= "</tr>";
            }

        }

        $html .= '</table>';

        return $html;
    }

	/**
	* Minificha
	* @access public
	* @param string $id_depto
	* @param string $id_mpio
	*/
	function Minificha($id_depto,$id_mpio,$formato){

		set_time_limit(0);

		//LIBRERIAS
		$depto_dao = New DeptoDAO();
		$mun_dao = New MunicipioDAO();
		$d_s_dao = New DatoSectorialDAO();
		$fuente_dao = New ContactoDAO();
		$cat_dao = New CategoriaDatoSectorDAO();
		$archivo = New Archivo();
		$periodo_dao = New PeriodoDAO();
		$fuente_des_dao = New FuenteDAO();
		$tipo_des_dao = New TipoDesplazamientoDAO();
		$des_dao = New DesplazamientoDAO();
		$sexo_dao = New SexoDAO();
		$condicion_dao = New CondicionMinaDAO();
		$estado_mina_dao = New EstadoMinaDAO();
		$edad_dao = New EdadDAO();
		$mina_dao = New MinaDAO();
		$tipo_org_dao = New TipoOrganizacionDAO();
		$sector_dao = New SectorDAO();
		$org_dao = New OrganizacionDAO();
		$sub_fuente_dao = New SubFuenteEventoConflictoDAO();

		//INICIALIZACION DE VARIABLES
		$file = New Archivo();
		$tipo_grafica = array(
								'barras' => 1,
								'barras_h' => 2,
								'puntos' => 3,
								'lineas' => 4,
								'torta' => 5,
								'dona' => 6,
								'barras_a' => 7,
								'barras_t_o' => 8,
								'histograma' => 9,
								'barras_t' => 10,
								'lineas_t' => 11,
								'histograma_l' => 12,
								'barras_a_t' => 13,
								'histograma_l_t' => 14,
								'piramide' => 15
						);
		$arr = array("y","z");
		$_SESSION["pdfcode"] = "";

		//Nacional
		if ($id_depto == 0 && $id_mpio == 0){
			$id_ubicacion = '00';
			$dato_para = 3;
		}
		else{
			$id_ubicacion = $id_depto;
			$ubicacion = $depto_dao->Get($id_ubicacion);
			$dato_para = 1;
			if ($id_mpio != 0){
				$id_ubicacion = $id_mpio;
				$ubicacion = $mun_dao->Get($id_ubicacion);
				$dato_para = 2;
				$depto_ubicacion = $depto_dao->Get($ubicacion->id_depto);
			}

		}
		//Para almacenar cache html y pdf
		$path_file = $this->dir_cache_perfil."/perfil_$id_ubicacion";

		//Para almacenar las imagenes por ubicacion
		$path_images = $this->dir_images_perfil."/$id_ubicacion";
		$real_path_images = $_SERVER["DOCUMENT_ROOT"]."/sissh/".$path_images;

		//url de las imagenes, dir completa, para que funcione cuando se incluye desde otro servidor
		$url_images = $this->url_images."/$id_ubicacion";

		//check si existe el directorio
		if (!$archivo->Existe($real_path_images)){
			$archivo->crearDirectorio($real_path_images);
		}

		//MANEJO DE CACHE
		$cache_file = "$path_file.htm";

		$gen_perfil = $this->siGenerarPerfil($cache_file);
		//$gen_perfil = 1;

		/** CARGA LOS MODULOS QUE COMPONEN EL PERFIL ***/
		//1. MODULOS GENERALES
		$mod_general = array();
		$m = 0;
		$sql = "SELECT ID_MIN_GENERAL FROM minificha_general WHERE ACTIVO_MIN_GENERAL = 1";
		$rs = $this->conn->OpenRecordset($sql);
		while ($row_rs = $this->conn->FetchRow($rs)){
			$mod_general[$m] = $row_rs[0];
			$m++;
		}

		//SI ESTA ACTIVO RESUMEN SE CONSULTAN LOS DATOS A MOSTRAR
		if (in_array(1,$mod_general)){
			$d = 0;
			$id_cate = 0;
			$sql = "SELECT ID_CATE, ID_DATO FROM minificha_datos_resumen ORDER BY ORDEN_CATE,ORDEN_DATO";
			$rs = $this->conn->OpenRecordset($sql);
			while ($row_rs = $this->conn->FetchRow($rs)){
				if ($id_cate != $row_rs[0]){
					$id_cate = $row_rs[0];
				}

				$id_datos_resumen[$id_cate][$d] = $row_rs[1];
				$d++;
			}
		}

		//2. DATOS SECORIALES
		$mod_d_s = array();
		$m = 0;
		$sql = "SELECT ID_MIN_D_S, TIPO_GRA_MIN_D_S FROM minificha_datos_sectoriales WHERE ACTIVO_MIN_D_S = 1";
		$rs = $this->conn->OpenRecordset($sql);
		while ($row_rs = $this->conn->FetchRow($rs)){
			$mod_d_s[$m] = $row_rs[0];
			$mod_d_s_tipo_g[$row_rs[0]] = $row_rs[1];
			$m++;
		}

		//SI ESTA ACTIVO ENFERMEDADES SE CONSULTAN LOS DATOS A MOSTRAR
		if (in_array(9,$mod_d_s)){
			$d = 0;
			$sql = "SELECT ID_DATO FROM minificha_enfermedades";
			$rs = $this->conn->OpenRecordset($sql);
			while ($row_rs = $this->conn->FetchRow($rs)){
				$id_enfermedades[$d] = $row_rs[0];
				$d++;
			}
		}

		//3. DESPLAZAMIENTO
		$mod_des = array();
		$m = 0;
		$sql = "SELECT ID_MIN_DES, TIPO_GRA_MIN_DES FROM minificha_desplazamiento WHERE ACTIVO_MIN_DES = 1";
		$rs = $this->conn->OpenRecordset($sql);
		while ($row_rs = $this->conn->FetchRow($rs)){
			$mod_des[$m] = $row_rs[0];
			$mod_des_tipo_g[$row_rs[0]] = $row_rs[1];
			$m++;
		}

		//4. MINA
		$mod_mina = array();
		$m = 0;
		$sql = "SELECT ID_MIN_MINA, TIPO_GRA_MIN_MINA FROM minificha_mina WHERE ACTIVO_MIN_MINA = 1";
		$rs = $this->conn->OpenRecordset($sql);
		while ($row_rs = $this->conn->FetchRow($rs)){
			$mod_mina[$m] = $row_rs[0];
			$mod_mina_tipo_g[$row_rs[0]] = $row_rs[1];
			$m++;
		}

		//5. IRH
		$mod_irh = array();
		$sql = "SELECT ID_MIN_S_HUMA, TIPO_GRA_MIN_S_HUMA FROM minificha_s_huma WHERE ACTIVO_MIN_S_HUMA = 1";
		$rs = $this->conn->OpenRecordset($sql);
		while ($row_rs = $this->conn->FetchRow($rs)){
			$mod_irh[] = $row_rs[0];
			$mod_irh_tipo_g[$row_rs[0]] = $row_rs[1];
		}

		//6. ORGS
		$mod_org = array();
		$m = 0;
		$sql = "SELECT ID_MIN_ORG, TIPO_GRA_MIN_ORG FROM minificha_org WHERE ACTIVO_MIN_ORG = 1";
		$rs = $this->conn->OpenRecordset($sql);
		while ($row_rs = $this->conn->FetchRow($rs)){
			$mod_org[$m] = $row_rs[0];
			$mod_org_tipo_g[$row_rs[0]] = $row_rs[1];
			$m++;
		}

		//SE CONSULTAN LOS SEXOS A MOSTRAR SI ESTA ACTIVA LA GRAFICA
		if (in_array(1,$mod_mina)){
			$d = 0;
			$sql = "SELECT ID_SEXO FROM minificha_sexo_mina";
			$rs = $this->conn->OpenRecordset($sql);
			while ($row_rs = $this->conn->FetchRow($rs)){
				$id_sexos[$d] = $row_rs[0];
				$d++;
			}
		}

		//SE CONSULTAN LAS CONDICIONES A MOSTRAR SI ESTA ACTIVA LA GRAFICA
		if (in_array(2,$mod_mina)){
			$d = 0;
			$sql = "SELECT ID_CONDICION_MINA FROM minificha_condicion_mina";
			$rs = $this->conn->OpenRecordset($sql);
			while ($row_rs = $this->conn->FetchRow($rs)){
				$id_condiciones[$d] = $row_rs[0];
				$d++;
			}
		}

		//SE CONSULTAN LOS GRUPOS DE EDAD A MOSTRAR SI ESTA ACTIVA LA GRAFICA
		if (in_array(4,$mod_mina)){
			$d = 0;
			$sql = "SELECT ID_EDAD FROM minificha_edad_mina";
			$rs = $this->conn->OpenRecordset($sql);
			while ($row_rs = $this->conn->FetchRow($rs)){
				$id_edades[$d] = $row_rs[0];
				$d++;
			}
		}

		/********** INICIO PERFIL **************/

		//Espacio entre una grafica en una td y la otra para el PDF
		$space_td_pdf = 5;

		//MAPA INICIAL
		if ($dato_para == 2){
			$vo = $mun_dao->Get($id_ubicacion);

			//Check si existe el mapa del mpio
			if ($archivo->Existe('images/minificha/'.$id_ubicacion.'.png')){
				$mapa_inicial = "$id_ubicacion.png";
			}
			else{
				$id_ubicacion_depto = $vo->id_depto;
				$mapa_inicial = "$id_ubicacion_depto.png";
			}
		}
		else{
			$mapa_inicial = "$id_ubicacion.png";
		}

		//MARGENES PARA EL PDF
		$top = 80;
		$bottom = 30;
		$pie = 20;
		$left = 20;
		$right = 20;
		$page_width = 792;
		$page_height = 612;

		$header_html = '<table width="100%">
							<tr>
								<td align="left" class="header_minificha"><img src="/sissh/images/minificha/logo.jpg">&nbsp;Oficina para la Coordinaci�n de Asuntos Humanitarios | OCHA</td>
								<td align="right" class="header_minificha">PERFIL</td>
							</tr>
						</table><br>';

		if ($formato == 'html'){

			?>
			<table cellpadding="5" cellspacing="1" width="1000">
				<tr class='pathway'>
					<td colspan=4>
						&nbsp;<img src='images/user-home.png'>&nbsp;<a href='index.php?m_g=consulta&m_e=home'>Home</a>
						&gt; <a href='index.php?m_g=consulta&m_e=minificha&accion=generar&class=Minificha'>P&eacute;rfil Geogr&aacute;fico</a>
						&gt; P&eacute;rfil <?= $ubicacion->nombre ?>
					</td>
				</tr>
				<tr>
					<td align="center">
							<?
							if ($id_mpio == ''){
								echo "<select class='select' onchange=\"if (this.value!=''){location.href='index.php?m_e=minificha&accion=generar&class=Minificha&id_depto_minificha='+this.value+'&formato=$formato';}\">";
								echo "<option value=''>Cambiar Ubicaci&oacute;n</option>";
								$depto_dao->ListarCombo('combo','','');
								echo "</select>";
							}
							else {
								echo "<select class='select' onchange=\"if (this.value!=''){location.href='index.php?m_e=minificha&accion=generar&class=Minificha&id_depto_minificha=$id_depto&id_mun_minificha='+this.value+'&formato=$formato';}\">";
								echo "<option value=''>Cambiar Ubicaci&oacute;n</option>";
								$mun_dao->ListarCombo('combo','','id_depto='.$id_depto);
								echo "</select>";
							}
							?>
						&nbsp;
						<!-- <img src="images/consulta/generar_pdf.gif" border=0> <a href="download_pdf.php?c=1&ubi=<?=$ubicacion->nombre?>">Descargar PDF</a> -->
						<img src="images/consulta/generar_pdf.gif" border=0> <a href="download_pdf.php?c=2&id_depto=<?=$id_depto ?>&id_mun=<?=$id_mpio ?>">Descargar PDF</a>

					</td>
				</tr>

			<?
		}

		//Carga el contenido del cache
		if ($gen_perfil == 0 && $formato == 'html'){

			$fp = $archivo->Abrir($cache_file,'r');

			echo $archivo->LeerEnString($fp,$cache_file);
		}
		else{
			//INICIO BUFFERING
			ob_start();
			?>
				<tr>
					<td class="titulo_pagina_minificha" align="center">
						PERFIL <?=$ubicacion->nombre?>
					</td>
				</tr>
				<tr>
					<td><a name="top">Ir a</a>:
						<a href="#resumen">Resumen</a> |
						<a href="#datos_generales">Datos sectoriales generales</a> |
						<a href="#informacion_demografica">Informaci&oacute;n Demogr&aacute;fica</a> |
						<a href="#vulnerabilidad">Vulnerabilidad</a> |
						<a href="#desplazamiento">Desplazamiento</a> |
						<a href="#mina">Accidentes con Mina</a> |
						<a href="#irh">I. Riesgo Humanitario</a> |
						<a href="#org">Organizaciones</a>
					</td>
				</tr>
			</table>
			<br>
			<table cellpadding="3" cellspacing="0" width="800">

			<?
			//INIT PDF
			//$pdf =& new Cezpdf();

			//Dimension Mapa
			$size = getimagesize("images/minificha/$mapa_inicial");

			if ($size[0] > $size[1]){
				$width = 700;
				$height = 495;
				$x_mapa = 40;
				$y_mapa = $bottom + 20;
			}
			else{
				$width =380;
				$height = 537;
				$x_mapa = 200;
			}

			//MAPA DEPTO
			echo "<tr><td class='pagina_minificha' align='center'>$header_html<img src=\"/sissh/images/minificha/$mapa_inicial\" width=700 height=495></td></tr>";

			//$pdf->selectFont('admin/lib/common/PDFfonts/Helvetica.afm');
			//$pdf -> ezSetMargins($top,$bottom,$left,$right);

			// Coloca el encabezdo y el pie a todas las p�ginas
			//$all = $pdf->openObject();
			//$pdf->saveState();
			//$pdf->addJpegFromFile("images/minificha/logo.jpg",$left,570,36);
			//$pdf->addText(60,573,10,'Oficina para la Coordinaci�n de Asuntos Humanitarios | OCHA ');
			//$pdf->addText(720,573,10,'PERFIL');

			//Fecha
			$hoy = getdate();
			$fecha = $hoy['mday']."-".$hoy['mon']."-".$hoy['year'];
			////$pdf->addText($left,$pie,12,$fecha);

			//$pdf->restoreState();
			//$pdf->closeObject();
			//$pdf->addObject($all,'all');

			//Paginacion
			//$pdf->ezStartPageNumbers(780,$pie,12,'left','{PAGENUM}',1);

			//$x_titulo_ubicacion = $page_width - $pdf->getTextWidth(14,$ubicacion->nombre) - $right - 6;

			//MAPA DEPTO
			//$pdf->addPngFromFile("images/minificha/$mapa_inicial",$x_mapa,$y_mapa,$width);

			//**********************
			//RESTO DE PAGINAS
			//**********************


			/*** TABLA RESUMEN **/
			if (in_array(1,$mod_general)){
				?>
				<tr><td>&nbsp;</td></tr>
				<tr><td><a name="resumen" href="#top">^ Subir</a></td></tr>
				<tr><td class="pagina_minificha"><?=$header_html?>
				<br>
				<table cellpadding="5" cellspacing="1" width='800' class='tabla_minificha_resumen' align="center">
					<tr>
						<td><b>Indicadores Sectoriales</b></td>
						<td width="65"><b><?=$ubicacion->nombre?></b></td>
						<?
						if ($dato_para == 2){
							echo "<td width='65'><b>$depto_ubicacion->nombre</b></td>";
						}
						?>
						<td width="65"><b>Nacional</b></td>
						<td><b>Fuente</b></td>
						<td><b>A&ntilde;o</b></td>
					</tr>
				<?
				//$pdf->ezNewPage();
				//$pdf->addText($left,550,14,'<b>Resumen</b>');
				//$pdf->addText($x_titulo_ubicacion,550,14,"<b>$ubicacion->nombre</b>");
				//$pdf->setLineStyle(1);
				//$pdf->line($left,540,$page_width - $left,540);			//Superior

                /*
				$title['indicador'] = '<b>Indicadores Sectoriales</b>';
				$title['ubicacion'] = "<b>$ubicacion->nombre</b>";
				if ($dato_para == 2)	$title['depto_ubicacion'] = "<b>$depto_ubicacion->nombre</b>";
				$title['nacional'] = '<b>Nacional</b>';
				$title['fuente'] = '<b>Fuente</b>';
                $title['aaaa'] = '<b>A�o</b>';
                */

				$fila = 0;
				foreach ($id_datos_resumen as $categoria => $datos_m){

					$cat = $cat_dao->Get($categoria);
					echo "<tr><td><b>$cat->nombre</b></td><td></td><td></td><td></td><td></td>";
					if ($dato_para == 2)	echo "<td></td>";
					echo "</tr>";
	                /*
					$data[$fila]['indicador'] = "<b>$cat->nombre</b>";
					$data[$fila]['ubicacion'] = "";
					$data[$fila]['nacional'] = "";
					$data[$fila]['fuente'] = "";
					$data[$fila]['aaaa'] = "";
					if ($dato_para == 2){
						$data[$fila]['depto_ubicacion'] = "";
					}
                    */
					$fila++;

					foreach ($datos_m as $id_dato){
						$dato = $d_s_dao->Get($id_dato);

                        $fuente_n = '';
                        if (!empty($dato->id_contacto)) {
                            $fuente = $fuente_dao->Get($dato->id_contacto);
                            if (!empty($fuente)) {
                                $fuente_n = $fuente->nombre;
                            }
                        }

						//CONSULTA EL PERIODO MAS RECIENTE DEL VALOR DEL DATO
						$fecha_val = $d_s_dao->GetMaxFecha($id_dato);

						//VALOR DATO
						$val = $d_s_dao->GetValorToReport($id_dato,$id_ubicacion,$fecha_val['ini'],$fecha_val['fin'],$dato_para);
						$valor = $val['valor'];
						$id_unidad = $val['id_unidad'];
						//APLICA FORMATO
						$valor = $d_s_dao->formatValor($id_unidad,$valor);

						//VALOR DEPARTAMENTAL CUANDO ES UN MPIO
						if ($dato_para == 2){
							$val = $d_s_dao->GetValorToReport($id_dato,$depto_ubicacion->id,$fecha_val['ini'],$fecha_val['fin'],1);
							$valor_nacional = $val['valor'];
							$id_unidad = $val['id_unidad'];

							//APLICA FORMATO
							$valor_depto = $d_s_dao->formatValor($id_unidad,$valor_nacional);
						}

						//VALOR DATO NACIONAL
						$val = $d_s_dao->GetValorToReport($id_dato,$id_ubicacion,$fecha_val['ini'],$fecha_val['fin'],3);
						$valor_nacional = $val['valor'];
						$id_unidad = $val['id_unidad'];

						//APLICA FORMATO
						$valor_nacional = $d_s_dao->formatValor($id_unidad,$valor_nacional);

						//FECHA
						$fecha = $d_s_dao->GetMaxFecha($id_dato);
						$a = split("-",$fecha["fin"]);

						echo "<tr><td>$dato->nombre</td>";
						echo "<td align='right'>$valor</td>";
						if ($dato_para == 2)	echo "<td align='right'>$valor_depto</td>";
						echo "<td align='right'>$valor_nacional</td>";
						echo "<td>$fuente_n</td>";
						echo "<td>$a[0]</td></tr>";

                        /*
						$data[$fila]['indicador'] = $dato->nombre;
						$data[$fila]['fuente'] = $fuente_n;
						$data[$fila]['ubicacion'] = $valor;
						$data[$fila]['nacional'] = $valor_nacional;
						$data[$fila]['aaaa'] = $a[0];
                        */
						//if ($dato_para == 2)	$data[$fila]['depto_ubicacion'] = $valor_depto;

						$fila++;
					}
					$fila++;
				}

				echo "</table>";
				echo "<br><br><div align='center'>[ P&aacute;gina 1 - Tabla Resumen ]</div>";

				$options = Array('showLines' => 2, 'shaded' => 0, 'width' => 800, 'fontSize'=>7, 'cols'=>array('indicador'=>array('width'=>250),'ubicacion'=>array('width'=>80,'justification'=>'center'),'nacional'=>array('width'=>80,'justification'=>'center'),'fuente'=>array('width'=>200),'aaaa'=>array('width'=>30)));

				if ($dato_para == 2)	$options['cols']['depto_ubicacion'] = array('width'=>80,'justification'=>'center');
				//$pdf->ezTable($data,$title,'',$options);

				echo "</td></tr><tr><td>&nbsp;</td></tr>";
			}

			/*** DATOS SECTORIALES GENERALES ***/
			if (count(array_intersect(array(6,7,8,9,10),$mod_d_s)) > 0){

				//VARIABLES DE UBICACION DE GRAFICAS
				$y_enfermedades = 270;

				echo "<tr><td><a name=\"datos_generales\" href=\"#top\">^ Subir</a></td></tr>";
				echo "<tr><td class='pagina_minificha'>";
				echo $header_html;

				echo "<table cellpadding='0' cellspacing='0' width='920'>";
				echo "<tr><td class='titulo_pag_minificha'><b>Datos sectoriales generales</b></td><td align='right'><b>$ubicacion->nombre</b></td></tr>";
				echo "<tr><td colspan='2' class='linea_minificha'><img src='/sissh/images/spacer.png' height=1></td></tr>";
				echo "<tr><td>&nbsp;</td></tr>";

				//$pdf->ezNewPage();
				//$pdf->addText($left,550,14,'<b>Datos sectoriales generales</b>');
				//$pdf->addText($x_titulo_ubicacion,550,14,"<b>$ubicacion->nombre</b>");
				//$pdf->setLineStyle(1);
				//$pdf->line($left,540,$page_width - $left,540);			//Superior
				////$pdf->line($left + 350,540,$left+350,300);	//Mitad Vertical
				////$pdf->line($left,235,$left + 350,235);			//Mitad Horizontal Iz
				////$pdf->line($left + 350,235 - $alto_rect,$page_width - $left,235 - $alto_rect);	//Mitad Horizontal Der


				echo "<tr>";
				//CRECIMIENTO PIB
				if (in_array(6,$mod_d_s)){

					//GRAFICA
					$PG = new PowerGraphic;
					$PG->title     = 'Crecimiento PIB';
					$PG->axis_x    = 'A�o';
					$PG->axis_y    = '% Tasa Crec. PIB';
					$PG->skin      = 1;
					$PG->type      = $tipo_grafica[$mod_d_s_tipo_g[6]];
					$PG->border = 0;
					$PG->decimals   = 2;
					$PG->graphic_1 = 'Nacional';
					$PG->graphic_2 = $ubicacion->nombre;
					$PG->border = 0;
					$PG->texto_1 = " | Fuente Dane - Cuentas Nacionales";

					//DATO PIB
					$id_dato_pib_total = 132;

					//CONSULTA EL RANGO DEL PERIODO DEL DATO
					$fecha_max_pib_total = $d_s_dao->GetMaxFecha($id_dato_pib_total);
					//$fecha_min_pib_total = $d_s_dao->GetMinFecha($id_dato_pib_total);

					$f_max_ini = split("-",$fecha_max_pib_total['ini']);
					//$f_min_ini = split("-",$fecha_min_pib_total['ini']);

					if ($f_max_ini[0] != ''){
						$a_max = $f_max_ini[0];
						$a_min = $a_max - 9;

						$d = 0;
						for($a=$a_min;$a<$a_max;$a++){

							$p_ant_ini = "$a-01-01";
							$p_ant_fin = "$a-12-31";

							$aa = $a+1;
							$p_corr_ini = "$aa-01-01";
							$p_corr_fin = "$aa-12-31";

							if ($dato_para != 3){
								//VALOR DATO ACTUAL
								$val = $d_s_dao->GetValorToReport($id_dato_pib_total,$id_ubicacion,$p_corr_ini,$p_corr_fin,$dato_para);
								$valor = $val['valor'];

								//VALOR DATO ANTERIOR
								$val = $d_s_dao->GetValorToReport($id_dato_pib_total,$id_ubicacion,$p_ant_ini,$p_ant_fin,$dato_para);
								$valor_ant = $val['valor'];

								if ($valor == 0)	$val = 0.1;

								//SE APLICA VARIACION
								$valor_minificha = ($valor_ant != 0) ? (($valor - $valor_ant)/($valor_ant)) * 100 : 0;
							}

							//VALOR DATO NACIONAL ACTUAL
							$val = $d_s_dao->GetValorToReport($id_dato_pib_total,$id_ubicacion,$p_corr_ini,$p_corr_fin,3,1);
							$valor_nacional = $val['valor'];

							//VALOR DATO NACIONAL ANTERIOR
							$val = $d_s_dao->GetValorToReport($id_dato_pib_total,$id_ubicacion,$p_ant_ini,$p_ant_fin,3,1);
							$valor_nacional_ant = $val['valor'];

							if ($valor_ant == 0)	$val_ant = 0.1;

							//SE APLICA VARIACION
							$valor_minificha_nacional = ($valor_nacional_ant != 0) ? (($valor_nacional - $valor_nacional_ant)/($valor_nacional_ant)) * 100 : 0;

							$PG->x[$d] = $aa."|".$a;
							$PG->y[$d] = $valor_minificha_nacional;
							if ($valor > 0)	$PG->z[$d] = $valor_minificha;

							$d++;
						}

						$dato_vo = $d_s_dao->Get($id_dato_pib_total);

                        $fuente_n = '';
                        if (!empty($dato_vo->id_contacto)) {
                            $fuente_vo = $fuente_dao->Get($dato_vo->id_contacto);
                            $fuente_n = $fuente_vo->nombre;
                        }

						$PG->texto_1 = " |  $fuente_n";

						//SI NO TIENE VALORES NEGATIVOS GRAFICA LINEAS CON TABLA RESUMEN
						$y_png = 220;
						if (min($PG->y) > 0 && min($PG->z) > 0){
							$PG->type = 11;
							$y_png = 280;
						}

						//echo "<tr><td><img src='/sissh/admin/lib/common/graphic.class.php?".$PG->create_query_string()."' border=0 /></td>";

						$img = $PG->create_graphic_minificha();
						$width_img_1_1 = imagesx($img);
						$img_b = $PG->fill_background($img,255,255,255);
						imagepng($img_b,$path_images.'/1_1.png');

						$img_web = $PG->draw_copyright($img);
						$img_web = $PG->fill_background($img_web,250,250,250);
						imagepng($img_web,$path_images.'/w_1_1.png');

						echo "<td align='center'><img src='$url_images/w_1_1.png' border=0 /></td>";



						//$pdf->addPngFromFile("$path_images/1_1.png",$left+400,$y_png,$width_img_1_1);
						imagedestroy($img);
					}
					else{
						echo "<td align='center'><br><b>Crecimiento PIB</b><br><br>No hay Datos disponibles</td>";
					}
				}

				//DISTRIBUCION PIB DEPARTAMENTAL POR SECTOR
				/*
				if (in_array(7,$mod_d_s) && $id_mpio == 0){

					//VALOR DEL PIB TOTAL
					$val = $d_s_dao->GetValorToReport($id_dato_pib_total,$id_ubicacion,$fecha_max_pib_total['ini'],$fecha_max_pib_total['fin'],3);
					$valor_pib_total = $val['valor'];


					//CATEGORIAS DE LOS SECTORES DE PIB
					$arr_id_cat = array(17,18,19,20,21,22,23,24,25,26,27,28,29,30,31,32,33,34,35);

					foreach ($arr_id_cat as $id_cat){
						$arr_id_datos = $d_s_dao->GetAllArrayID("ID_CATE = $id_cat","","");

						$valor_sector[$id_cat] = 0;
						foreach ($arr_id_datos as $id_d_sector){
							//CONSULTA EL PERIODO MAS RECIENTE DEL VALOR DEL DATO
							$fecha_val = $d_s_dao->GetMaxFecha($id_d_sector);

							$val = $d_s_dao->GetValorToReport($id_d_sector,$id_ubicacion,$fecha_val['ini'],$fecha_val['fin'],$dato_para);
							$valor_sector[$id_cat] += $val['valor'];

						}
					}
					//ORDENA DE MAYOR A MENOR
					arsort($valor_sector);

					//SOLO SE GRAFICA SI EXISTEN VALORES
					if (max($valor_sector) > 0){

						$PG = new PowerGraphic;
						$PG->title = ($dato_para == 1) ? "Distribuci�n PIB Departamental" : "Distribuci�n PIB Municipal";
						$PG->type = $tipo_grafica[$mod_d_s_tipo_g[7]];
						$PG->skin      = 1;
						$PG->border = 0;
						if ($dato_para == 2)	$PG->texto_extra   = "Datos calculados con base en el Producto Interno|Bruto departamental del DANE, tomando como|referencia el peso poblacional";

						$s = 0;
						$otros = 0;
						foreach ($valor_sector as $id_cat => $valor){

							if ($s < 4){
								$categoria = $cat_dao->Get($id_cat);
								$PG->x[$s] = $categoria->nombre;
								$PG->y[$s] = $valor;
							}
							else{
								$otros += $valor;
							}
							$s++;
						}
						$PG->x[4] = 'Otros';
						$PG->y[4] = $otros;

						$PG->texto_1 = " | $fuente_vo->nombre ";

						//echo "<td><img src='/sissh/admin/lib/common/graphic.class.php?".$PG->create_query_string()."' border=0 /></td></tr>";

						$img = $PG->create_graphic_minificha();

						$width_img_1_2 = imagesx($img);
						$img_b = $PG->fill_background($img,255,255,255);
						imagepng($img_b,$path_images.'/1_2.png');

						$img_web = $PG->draw_copyright($img);
						$img_web = $PG->fill_background($img_web);
						imagepng($img_web,$path_images.'/w_1_2.png');

						echo "<td><img src='$url_images/w_1_2.png' border=0 /></td></tr>";

						//$pdf->addPngFromFile("$path_images/1_2.png",$left,330,$width_img_1_2);
						imagedestroy($img);
					}
					else{
						echo "<td>No se puede graficar PIB por sector, <br>porque no existen datos para ningun sector en el periodo ".$fecha_val['ini']." -". $fecha_val['fin']." de $ubicacion->nombre";
					}
					echo "<tr>";

				}
	*/
				//ENFERMEDADES
				if (in_array(9,$mod_d_s)){

					//GRAFICA
					$PG = new PowerGraphic;
					$PG->title = "Tasas Reportadas de Enfermedades por 100,000";
					$PG->axis_y    = 'Tasa';
					$PG->skin      = 1;
					$PG->type      = $tipo_grafica[$mod_d_s_tipo_g[9]];
					$PG->border = 0;
					$PG->graphic_1 = 'Nacional';
					if ($dato_para != 3) $PG->graphic_2 = $ubicacion->nombre;
					$PG->border = 0;
					//$PG->escala = 0.8;

					$d = 0;
					foreach($id_enfermedades as $id_dato){
						$dato = $d_s_dao->Get($id_dato);

						//CONSULTA EL PERIODO MAS RECIENTE DEL VALOR DEL DATO
						$fecha_val = $d_s_dao->GetMaxFecha($id_dato);
						$id_dato_pob = 3;

						if ($dato_para != 3){
							//CONSULTA EL TOTAL DE LA POBLACION EN EL MISMO PERIODO DEL DATO PARA LA UBIACION
							$val = $d_s_dao->GetValorToReport($id_dato_pob,$id_ubicacion,$fecha_val['ini'],$fecha_val['fin'],$dato_para);
							$total_poblacion = $val['valor'];

							//VALOR DATO
							$val = $d_s_dao->GetValorToReport($id_dato,$id_ubicacion,$fecha_val['ini'],$fecha_val['fin'],$dato_para);
							$valor = ($val['valor']/$total_poblacion) * 100000;

							if ($valor > 0)	$PG->z[$d] = $valor;
						}

						//CONSULTA EL TOTAL DE LA POBLACION EN EL MISMO PERIODO DEL DATO PARA LA UBIACION
						$val = $d_s_dao->GetValorToReport($id_dato_pob,$id_ubicacion,$fecha_val['ini'],$fecha_val['fin'],3,0);
						$total_poblacion_nacional = $val['valor'];

						//VALOR DATO NACIONAL
						$val = $d_s_dao->GetValorToReport($id_dato,$id_ubicacion,$fecha_val['ini'],$fecha_val['fin'],3,2);
						$valor_nacional = ($val['valor']/$total_poblacion_nacional) * 100000;

						$PG->x[$d] = $dato->nombre;
						$PG->y[$d] = $valor_nacional;
						$d++;
					}

                    $fuente_n = '';
                    if (!empty($dato->id_contacto)) {
                        $fuente_vo = $fuente_dao->Get($dato->id_contacto);
                        $fuente_n = $fuente_vo->nombre;
                    }

					$PG->texto_1 = " | $fuente_n ";

					//echo "<td colspan=2 align='center'><img src='/sissh/admin/lib/common/graphic.class.php?".$PG->create_query_string()."' border=0 /></td></tr>";


					$img = $PG->create_graphic_minificha();
					$img_b = $PG->fill_background($img,255,255,255);
					imagepng($img_b,$path_images.'/1_3.png');

					$img_web = $PG->draw_copyright($img);
					$img_web = $PG->fill_background($img_web,250,250,250);
					imagepng($img_web,$path_images.'/w_1_3.png');

					echo "<td colspan=2 align='center'><img src='$url_images/w_1_3.png' border=0 /></td></tr>";


					//$pdf->addPngFromFile("$path_images/1_3.png",$left+20,10,imagesx($img));
					imagedestroy($img);
				}

				echo "<tr><td>&nbsp;</td></tr>";
				//Tasa de Asistencia Escolar
				if (in_array(10,$mod_d_s)){
					//PARA Acci�n Social
					$fuente = $fuente_des_dao->Get(2);
					$tipos = $tipo_des_dao->GetAllArray('');

					//GRAFICA
					$PG = new PowerGraphic;
					$PG->title     = 'Tasa de Asistencia Escolar (Edades Escolares)';
					$PG->axis_x    = 'A�o';
					$PG->axis_y    = '';
					$PG->skin      = 1;
					$PG->type      = $tipo_grafica[$mod_d_s_tipo_g[10]];
					$PG->border = 0;
					$PG->decimals   = 2;
					$PG->graphic_1 = "% de Inasistencia";
					$PG->graphic_2 = "% de Asistencia";
					$PG->border = 0;
					$PG->texto_1 = " | Fuente Dane - Censo General 2005";

					//CONSULTA EL PERIODO MAS RECIENTE DEL VALOR DEL DATO
					$id_dato_3_4 = 497;
					$fecha_val = $d_s_dao->GetMaxFecha($id_dato_3_4);

					$val = $d_s_dao->GetValorToReport($id_dato_3_4,$id_ubicacion,$fecha_val['ini'],$fecha_val['fin'],$dato_para);
					$valor = $d_s_dao->formatValor($val['id_unidad'],$val['valor'],1);

					$PG->x[0] = '3 a 4 a�os';
					$PG->y[0] = $valor;
					$PG->z[0] = 100 -$valor;

					//CONSULTA EL PERIODO MAS RECIENTE DEL VALOR DEL DATO
					$id_dato_5_6 = 496;
					$fecha_val = $d_s_dao->GetMaxFecha($id_dato_5_6);

					$val = $d_s_dao->GetValorToReport($id_dato_5_6,$id_ubicacion,$fecha_val['ini'],$fecha_val['fin'],$dato_para);
					$valor = $d_s_dao->formatValor($val['id_unidad'],$val['valor'],1);

					$PG->x[1] = '5 a 6 a�os';
					$PG->y[1] = $valor;
					$PG->z[1] = 100 -$valor;

					//CONSULTA EL PERIODO MAS RECIENTE DEL VALOR DEL DATO
					$id_dato_7_11 = 498;
					$fecha_val = $d_s_dao->GetMaxFecha($id_dato_7_11);

					$val = $d_s_dao->GetValorToReport($id_dato_7_11,$id_ubicacion,$fecha_val['ini'],$fecha_val['fin'],$dato_para);
					$valor = $d_s_dao->formatValor($val['id_unidad'],$val['valor'],1);

					$PG->x[2] = '7 a 11 a�os';
					$PG->y[2] = $valor;
					$PG->z[2] = 100 -$valor;

					//CONSULTA EL PERIODO MAS RECIENTE DEL VALOR DEL DATO
					$id_dato_12_15 = 499;
					$fecha_val = $d_s_dao->GetMaxFecha($id_dato_12_15);

					$val = $d_s_dao->GetValorToReport($id_dato_12_15,$id_ubicacion,$fecha_val['ini'],$fecha_val['fin'],$dato_para);
					$valor = $d_s_dao->formatValor($val['id_unidad'],$val['valor'],1);

					$PG->x[3] = '12 a 15 a�os';
					$PG->y[3] = $valor;
					$PG->z[3] = 100 -$valor;

					//CONSULTA EL PERIODO MAS RECIENTE DEL VALOR DEL DATO
					$id_dato_16_17 = 500;
					$fecha_val = $d_s_dao->GetMaxFecha($id_dato_16_17);

					$val = $d_s_dao->GetValorToReport($id_dato_16_17,$id_ubicacion,$fecha_val['ini'],$fecha_val['fin'],$dato_para);
					$valor = $d_s_dao->formatValor($val['id_unidad'],$val['valor'],1);

					$PG->x[4] = '16 a 17 a�os';
					$PG->y[4] = $valor;
					$PG->z[4] = 100 -$valor;

					//echo "<td class='td_grafica_borde_derecho'><img src='/sissh/admin/lib/common/graphic.class.php?".$PG->create_query_string()."' border=0 /></td>";

					$img = $PG->create_graphic_minificha();
					$img_b = $PG->fill_background($img,255,255,255);
					imagepng($img_b,$path_images.'/1_10.png');

					$img_web = $PG->draw_copyright($img);
					$img_web = $PG->fill_background($img_web,250,250,250);
					imagepng($img_web,$path_images.'/w_1_10.png');

					echo "<td align='center'><img src='$url_images/w_1_10.png' border=0 /></td>";

					//$img = $PG->create_graphic_minificha();
					$width_img_1_10 = imagesx($img);
					////$pdf->addImage($img,$left,100,$width_img);

					//$pdf->addPngFromFile("$path_images/1_10.png",$left,270,$width_img_1_10);
					imagedestroy($img);
				}
				/*echo "<td colspan='2' class='linea_minificha'><img src='images/spacer.gif' height='1'></td></tr>";
				echo "<tr>";

				//$pdf->ezNewPage();
				//$pdf->addText($left,550,14,'<b>Datos sectoriales generales</b>');
				//$pdf->addText($x_titulo_ubicacion,550,14,"<b>$ubicacion->nombre</b>");
				//$pdf->setLineStyle(1);
				//$pdf->line($left,540,$page_width - $left,540);			//Superior

				//PUNTAJE DE ALUMNOS ICFES
				if (in_array(8,$mod_d_s)){

				}

				echo "</tr>";*/
				echo "</table>";
				echo "<br><br><div align='center'>[ P&aacute;gina 2 - Datos Sectoriales Generales ]</div>";
			}

			/*** INFORMACION DEMOGRAFICA ***/
			if (in_array(1,$mod_d_s) || in_array(2,$mod_d_s) || in_array(3,$mod_d_s) || in_array(4,$mod_d_s) || in_array(5,$mod_d_s)){
				echo "</td></tr><tr><td>&nbsp;</td></tr>";
				echo "<tr><td><a name=\"informacion_demografica\" href=\"#top\">^ Subir</a></td></tr>";
				echo "<tr><td class='pagina_minificha'>";
				echo $header_html;
				echo "<table cellpadding='0' cellspacing='0' width='920' border=0>";
				echo "<tr><td class='titulo_pag_minificha'><b>Informaci&oacute;n Demogr&aacute;fica</b></td><td align='right'><b>$ubicacion->nombre</b></td></tr>";
				echo "<tr><td colspan='2' class='linea_minificha'><img src='images/spacer.gif' height='1'></td></tr>";

				$alto_rect = 25;

				//$pdf->ezNewPage();
				//$pdf->addText($left,550,14,'<b>Informaci�n Demogr�fica</b>');
				//$pdf->addText($x_titulo_ubicacion,550,14,"<b>$ubicacion->nombre</b>");

				//$pdf->setLineStyle(1);
				//$pdf->line($left,540,$page_width - $left,540);			//Superior
				//$pdf->line($left + 320,540,$left+320,300);	//Mitad Vertical Sup
				//$pdf->line($left + 400,300,$left+400,$bottom);	//Mitad Vertical Inf
				//$pdf->line($left,300,$page_width - $left,300);			//Mitad Horizontal


				//Cuadro de Fuente y poblaci�n total
				//POBLACION TOTAL
				//CONSULTA EL PERIODO MAS RECIENTE DEL VALOR DEL DATO
				$id_dato_pob = 3;
				$fecha_val = $d_s_dao->GetMaxFecha($id_dato_pob);
				$val = $d_s_dao->GetValorToReport($id_dato_pob,$id_ubicacion,$fecha_val['ini'],$fecha_val['fin'],$dato_para);
				$total_poblacion = $val['valor'];
				$total_poblacion = number_format($total_poblacion);

				//$pdf->setColor(0.9,0.9,0.9);
				//$pdf->filledRectangle($left + 350 + 1,540 - $alto_rect - 1,$page_width - $right - $left - 350,$alto_rect);
				//$pdf->setColor(0,0,0);
				$x_text = $page_width - $right - //$pdf->getTextWidth(10,"Total $ubicacion->nombre: ".$total_poblacion) - 5;
				//$pdf->addText($x_text,530,10,"Total $ubicacion->nombre: ".$total_poblacion);
				$x_text = $page_width - $right - 128;
				//$pdf->addText($x_text,520,10,"Fuente: DANE, Censo 2005");

				//echo "<tr>";
				//echo "<td class='td_grafica_borde_derecho'><table cellspacing=0 cellpadding=0>";
				//echo "<td align='center'><table cellspacing=0 cellpadding=0>";

				echo "<tr><td align='right' colspan=2 bgcolor='#E1E1E1'>
							<table cellpadding=0 cellspacing=0 width=460>
								<tr><td align='right'><b>Total $ubicacion->nombre: $total_poblacion</b></td></tr>
								<tr><td align='right'><b><b>Fuente: DANE, Censo 2005</b></td></tr>
							</table>
						</td></tr>";

				echo "<tr><td>&nbsp;</td></tr>";

				//DISTRIBUCION DEMOGRAFICA POR GRUPOS ETAREOS
				if (in_array(3,$mod_d_s)){
					//echo "<tr><td class='td_grafica_borde_derecho'></td></tr>";
					echo "<tr><td></td></tr>";
				}

				//DISTRIBUCION DEMOGRAFICA CABECERAS MUNICIPALES
				if (in_array(1,$mod_d_s)){

					$id_dato_cabecera = 1;
					$id_dato_resto = 2;

					//CONSULTA EL PERIODO MAS RECIENTE DEL VALOR DEL DATO
					$fecha_val = $d_s_dao->GetMaxFecha($id_dato_cabecera);
					$fecha_val_resto = $d_s_dao->GetMaxFecha($id_dato_resto);

					//A�o del periodo mas reciente
					$f_tmp = split("-",$fecha_val['ini']);
					$a_val = $f_tmp[0];

					$PG = new PowerGraphic;
					$PG->title = "Distribuci�n demogr�fica cabeceras municipales";
					$PG->type = $tipo_grafica[$mod_d_s_tipo_g[1]];
					$PG->skin      = 1;
					$PG->border = 0;

					$val = $d_s_dao->GetValorToReport($id_dato_cabecera,$id_ubicacion,$fecha_val['ini'],$fecha_val['fin'],$dato_para);
					$PG->x[0] = 'Cabeceras';
					$PG->y[0] = $val['valor'];

					$val = $d_s_dao->GetValorToReport($id_dato_resto,$id_ubicacion,$fecha_val_resto['ini'],$fecha_val_resto['fin'],$dato_para);
					$PG->x[1] = 'Resto';
					$PG->y[1] = $val['valor'];

					$dato_vo = $d_s_dao->Get($id_dato_cabecera);
                    $fuente_n = '';
                    if (!empty($dato_vo->id_contacto)) {
                        $fuente_vo = $fuente_dao->Get($dato_vo->id_contacto);
                        $fuente_n = $fuente_vo->nombre;
                    }
					$PG->texto_1 = " | Fuente: $fuente_n - Censo General $a_val";

					//echo "<tr><td><img src='/sissh/admin/lib/common/graphic.class.php?".$PG->create_query_string()."' border=0 /></td></tr>";

					$img = $PG->create_graphic_minificha();
					$width_img_2_3 = imagesx($img);
					$img_b = $PG->fill_background($img,255,255,255);
					imagepng($img_b,$path_images.'/2_3.png');

					$img_web = $PG->draw_copyright($img);
					$img_web = $PG->fill_background($img_web,250,250,250);
					imagepng($img_web,$path_images.'/w_2_3.png');

					echo "<tr><td align='center'><img src='$url_images/w_2_3.png' border=0 /></td>";

					//$pdf->addPngFromFile("$path_images/2_3.png",$left + 30,320,$width_img_2_3);
					imagedestroy($img);

				}
				//echo "</table></td>";


				//DISTRIBUCION DEMOGRAFICA POR GRUPOS ETNICOS
				if (in_array(2,$mod_d_s)){

					//Distribuci�n demogr�fica por grupos etnicos
					$id_dato_pob_indigena = 130;
					$id_dato_pob_negro = 311;
					$id_dato_pob_resto = 308;
					$id_otros = array(306,309,310,307);
					$space_td_pdf = 100;

					//CONSULTA EL PERIODO MAS RECIENTE DEL VALOR DEL DATO
					$fecha_val_ind = $d_s_dao->GetMaxFecha($id_dato_pob_indigena);
					$fecha_val_resto = $d_s_dao->GetMaxFecha($id_dato_pob_resto);

					//A�o del periodo mas reciente
					$f_tmp = split("-",$fecha_val_ind['ini']);
					$a_val_ind = $f_tmp[0];

					$PG = new PowerGraphic;
					$PG->title = "   Distribuci�n demogr�fica por grupos Etnicos";
					$PG->type = $tipo_grafica[$mod_d_s_tipo_g[2]];
					$PG->skin      = 1;
					$PG->border = 0;

					$dato_130 = $d_s_dao->get($id_dato_pob_indigena);

					$val = $d_s_dao->GetValorToReport($id_dato_pob_indigena,$id_ubicacion,$fecha_val_ind['ini'],$fecha_val_ind['fin'],$dato_para);
					$PG->x[0] = $dato_130->nombre;
					$PG->y[0] = $val['valor'];

					$dato_311 = $d_s_dao->get($id_dato_pob_negro);

					$val = $d_s_dao->GetValorToReport($id_dato_pob_negro,$id_ubicacion,$fecha_val_ind['ini'],$fecha_val_ind['fin'],$dato_para);
					$PG->x[1] = $dato_311->nombre;
					$PG->y[1] = $val['valor'];

					$dato_308 = $d_s_dao->get($id_dato_pob_resto);

					$val = $d_s_dao->GetValorToReport($id_dato_pob_resto,$id_ubicacion,$fecha_val_ind['ini'],$fecha_val_ind['fin'],$dato_para);
					$PG->x[2] = $dato_308->nombre;
					$PG->y[2] = $val['valor'];

					$val_otros = 0;
					foreach ($id_otros as $id_o){
						$val = $d_s_dao->GetValorToReport($id_o,$id_ubicacion,$fecha_val_resto['ini'],$fecha_val_resto['fin'],$dato_para);
						$val_otros += $val['valor'];
					}

					$PG->x[3] = 'Otro';
					$PG->y[3] = $val_otros;

					$dato_vo = $d_s_dao->Get($id_dato_pob_indigena);
                    $fuente_n = '';
                    if (!empty($dato_vo->id_contacto)) {
                        $fuente_vo = $fuente_dao->Get($dato_vo->id_contacto);
                        $fuente_n = $fuente_vo->nombre;
                    }
					$PG->texto_1 = " | Fuente: $fuente_n - Censo General $a_val_ind";

					$img = $PG->create_graphic_minificha();
					$width_img_2_2 = imagesx($img);
					$img_b = $PG->fill_background($img,255,255,255);
					imagepng($img_b,$path_images.'/2_2.png');

					$img_web = $PG->draw_copyright($img);
					$img_web = $PG->fill_background($img_web,250,250,250);
					imagepng($img_web,$path_images.'/w_2_2.png');

					echo "<td align='center'><img src='$url_images/w_2_2.png' border=0 /></td></tr>";
					//echo "		</table></td>";


					//$pdf->addPngFromFile("$path_images/2_2.png",$left + $width_img_2_3 + $space_td_pdf,350 - $alto_rect,$width_img_2_2);
					imagedestroy($img);

				}

				echo "</tr>";
				//echo "<tr><td colspan='2' class='linea_minificha'><img src='images/spacer.gif' height='1'></td></tr>";
				echo "<tr><td>&nbsp;</td></tr>";
				echo "<tr>";

				//PIRAMIDE POBLACIONAL
				if (in_array(11,$mod_d_s)){

					//Poblacion hombres
					//$id_dato_h = array(464,467,468,470,472,474,476,478,480);
                    $id_dato_h = array(464,467,468,696,697,698,699,700,480);

					//Poblacion mujeres
					$id_dato_m = array(465,466,469,471,473,475,477,479,481);

					//Rangos de edad
					$redad = array('0-9','10-19','20-29','30-39','40-49','50-59','60-69','70-79','80+');

					$PG = new PowerGraphic;
					$PG->title = "Pir�mide Poblacional";
					$PG->type = $tipo_grafica[$mod_d_s_tipo_g[11]];
					$PG->skin      = 1;
					$PG->axis_y = "Personas";
					$PG->axis_x = "Grupos de Edad";
					$PG->graphic_1 = "Hombres";
					$PG->graphic_2 = "Mujeres";
					$PG->border = 0;
					$PG->texto_1 = " | Fuente Dane - Censo General $a_val";

					$miles = 0;
					$j = 0;
					for($i=(count($id_dato_h)-1);$i>=0;$i--){

						$PG->x[$j] = $redad[$i];

						$id = $id_dato_h[$i];
						//CONSULTA EL PERIODO MAS RECIENTE DEL VALOR DEL DATO
						$fecha = $d_s_dao->GetMaxFecha($id);

						$val = $d_s_dao->GetValorToReport($id,$id_ubicacion,$fecha['ini'],$fecha['fin'],$dato_para);
						$valor = $val['valor'];

						if ($valor == 'N.D.')	$valor = 0;

						//Si exite un valor en el rango de los millones, pasa todo a miles
						if (strlen(intval($valor)) > 7){
							$valor = strlen($valor);
							$valor /= 1000;
							$miles = 1;
						}

						$PG->y[$j] = $valor;

						//CONSULTA EL PERIODO MAS RECIENTE DEL VALOR DEL DATO
						$id = $id_dato_m[$i];
						//$fecha = $d_s_dao->GetMaxFecha($id);
						$val = $d_s_dao->GetValorToReport($id,$id_ubicacion,$fecha['ini'],$fecha['fin'],$dato_para);

						$valor = $val['valor'];
						if ($valor == 'N.D.')	$valor = 0;

						//Si exite un valor en el rango de los millones, pasa todo a miles
						if (strlen(intval($valor)) > 7){
							$valor /= 1000;
							$miles = 1;
						}

						$PG->z[$j] = $valor;

						$j++;

					}

					if (max($PG->y) > 0 || max($PG->z) > 0){

						if ($miles == 1)	$PG->axis_y .= " (en miles)";

						$img = $PG->create_graphic_minificha();
						$width_img_2_11 = imagesx($img);
						$img_b = $PG->fill_background($img,255,255,255);
						imagepng($img_b,$path_images.'/2_11.png');

						$img_web = $PG->draw_copyright($img);
						$img_web = $PG->fill_background($img_web,250,250,250);
						imagepng($img_web,$path_images.'/w_2_11.png');

						//echo "<td class='td_grafica_borde_derecho'><img src='$url_images/w_2_11.png' border=0 /></td>";
						echo "<td align='center'><img src='$url_images/w_2_11.png' border=0 /></td>";

						//$img = $PG->create_graphic_minificha();

						//$pdf->addPngFromFile("$path_images/2_11.png",$left,30,$width_img_2_11);
						imagedestroy($img);
					}

					else{
						echo "<td align='center'><br><b>Piramide Poblacional</b><br><br>No hay Datos disponibles</td>";
					}
				}

				echo "</tr>";
				echo "</table>";
				echo "<br><br><div align='center'>[ P&aacute;gina 3 - Informaci&oacute;n Demogr&aacute;fica ]</div>";
			}

			/*** VULNERABILIDAD ***/
			if (in_array(4,$mod_d_s) || in_array(5,$mod_d_s) || in_array(12,$mod_d_s)){
				echo "</td></tr><tr><td>&nbsp;</td></tr>";
				echo "<tr><td><a name=\"vulnerabilidad\" href=\"#top\">^ Subir</a></td></tr>";
				echo "<tr><td class='pagina_minificha'>";
				echo $header_html;
				echo "<table cellpadding='0' cellspacing='0' width='920' border=0>";
				echo "<tr><td class='titulo_pag_minificha'><b>Vulnerabilidad</b></td><td align='right'><b>$ubicacion->nombre</b></td></tr>";
				echo "<tr><td colspan='2' class='linea_minificha'><img src='images/spacer.gif' height='1'></td></tr>";

				$alto_rect = 25;

				//$pdf->ezNewPage();
				//$pdf->addText($left,550,14,'<b>Vulnerabilidad</b>');
				//$pdf->addText($x_titulo_ubicacion,550,14,"<b>$ubicacion->nombre</b>");
				//$pdf->setLineStyle(1);
				//$pdf->line($left,540,$page_width - $left,540);			//Superior
				//$pdf->line($left + 320,540,$left+320,300);	//Mitad Vertical Sup
				//$pdf->line($left + 350,300,$left+350,$bottom);	//Mitad Vertical Inf
				//$pdf->line($left,300,$page_width - $left,300);			//Mitad Horizontal

				//Cuadro de Fuente y poblaci�n total
				//POBLACION TOTAL
				//CONSULTA EL PERIODO MAS RECIENTE DEL VALOR DEL DATO
				$id_dato_pob = 3;
				$fecha_val = $d_s_dao->GetMaxFecha($id_dato_pob);
				$val = $d_s_dao->GetValorToReport($id_dato_pob,$id_ubicacion,$fecha_val['ini'],$fecha_val['fin'],$dato_para);
				$total_poblacion = $val['valor'];
				$total_poblacion = number_format($total_poblacion);

				//A�o del periodo mas reciente
				$f_tmp = split("-",$fecha_val['ini']);
				$a_val = $f_tmp[0];

				//$pdf->setColor(0.9,0.9,0.9);
				//$pdf->filledRectangle($left + 350 + 1,540 - $alto_rect - 1,$page_width - $right - $left - 350,$alto_rect);
				//$pdf->setColor(0,0,0);
				$x_text = $page_width - $right - //$pdf->getTextWidth(10,"Total $ubicacion->nombre: ".$total_poblacion) - 5;
				//$pdf->addText($x_text,530,10,"Total $ubicacion->nombre: ".$total_poblacion);
				$x_text = $page_width - $right - 128;
				//$pdf->addText($x_text,520,10,"Fuente: DANE, Censo $a_val");

				echo "<tr><td>&nbsp;</td></tr>";
				echo "<tr>";

				//VULNERABILIDAD VIVIENDAS URBANAS EN SISBEN
				if (in_array(4,$mod_d_s)){

					// Vulnerabilidad Viviendas Urbanas en SISBEN
					$id_dato_ninguna = 153;
					$id_dato_deslizamiento = 150;
					$id_dato_inundacion = 147;
					$id_dato_avalancha = 144;

					//CONSULTA EL PERIODO MAS RECIENTE DEL VALOR DEL DATO
					$fecha_val_ninguna = $d_s_dao->GetMaxFecha($id_dato_ninguna);
					$fecha_val_deslizamiento = $d_s_dao->GetMaxFecha($id_dato_deslizamiento);
					$fecha_val_inundacion = $d_s_dao->GetMaxFecha($id_dato_inundacion);
					$fecha_val_avalancha = $d_s_dao->GetMaxFecha($id_dato_avalancha);

					$PG = new PowerGraphic;
					$PG->title = "Vulnerabilidad Viviendas Urbanas en SISBEN";
					$PG->type = $tipo_grafica[$mod_d_s_tipo_g[4]];
					$PG->border = 0;
					$PG->border = 0;

					$val = $d_s_dao->GetValorToReport($id_dato_ninguna,$id_ubicacion,$fecha_val_ninguna['ini'],$fecha_val_ninguna['fin'],$dato_para);
					$valor_ninguna = $val['valor'];
					$PG->x[0] = 'Ninguna';
					$PG->y[0] = $valor_ninguna;

					$val = $d_s_dao->GetValorToReport($id_dato_deslizamiento,$id_ubicacion,$fecha_val_deslizamiento['ini'],$fecha_val_deslizamiento['fin'],$dato_para);
					$valor_deslizamiento = $val['valor'];
					$PG->x[1] = 'Deslizamiento';
					$PG->y[1] = $valor_deslizamiento;

					$val = $d_s_dao->GetValorToReport($id_dato_inundacion,$id_ubicacion,$fecha_val_inundacion['ini'],$fecha_val_inundacion['fin'],$dato_para);
					$valor_inundacion = $val['valor'];
					$PG->x[2] = 'Inundacion';
					$PG->y[2] = $valor_inundacion;

					$val = $d_s_dao->GetValorToReport($id_dato_avalancha,$id_ubicacion,$fecha_val_avalancha['ini'],$fecha_val_avalancha['fin'],$dato_para);
					$valor_avalancha = $val['valor'];
					$PG->x[3] = 'Avalancha';
					$PG->y[3] = $valor_avalancha;

					$dato_vo = $d_s_dao->Get($id_dato_ninguna);
                    $fuente_n = '';
                    if (!empty($dato_vo->id_contacto)) {
                        $fuente_vo = $fuente_dao->Get($dato_vo->id_contacto);
                        $fuente_n = $fuente_vo->nombre;
                    }
					$PG->texto_1 = " | Fuente: $fuente_n";

					if ($valor_ninguna > 0 && $valor_deslizamiento > 0 && $valor_inundacion > 0 && $valor_avalancha > 0){
						//echo "<td class='td_grafica_borde_derecho'><img src='/sissh/admin/lib/common/graphic.class.php?".$PG->create_query_string()."' border=0 /></td>";

						$img = $PG->create_graphic_minificha();
						$width_img_2_4 = imagesx($img);
						$img_b = $PG->fill_background($img,255,255,255);
						imagepng($img_b,$path_images.'/2_4.png');

						$img_web = $PG->draw_copyright($img);
						$img_web = $PG->fill_background($img_web,250,250,250);
						imagepng($img_web,$path_images.'/w_2_4.png');

						//echo "<td class='td_grafica_borde_derecho'><img src='$url_images/w_2_4.png' border=0 /></td>";
						echo "<td align='center'><img src='$url_images/w_2_4.png' border=0 /></td>";

						//$img = $PG->create_graphic_minificha();

						//$pdf->addPngFromFile("$path_images/2_4.png",$left,350,$width_img_2_4);
						imagedestroy($img);
					}
					else{
						$width_img_2_4 = 0;
						$space_td_pdf = 0;
						echo "<td align='center'><br><b>Vulnerabilidad Viviendas Urbanas en SISBEN</b><br><br>No hay Datos disponibles</td>";
					}

				}

				//VULNERABILIDAD VIVIENDAS RURALES EN SISBEN
				if (in_array(5,$mod_d_s)){

					$id_dato_ninguna = 152;
					$id_dato_deslizamiento = 149;
					$id_dato_inundacion = 146;
					$id_dato_avalancha = 143;

					//CONSULTA EL PERIODO MAS RECIENTE DEL VALOR DEL DATO
					$fecha_val_ninguna = $d_s_dao->GetMaxFecha($id_dato_ninguna);
					$fecha_val_deslizamiento = $d_s_dao->GetMaxFecha($id_dato_deslizamiento);
					$fecha_val_inundacion = $d_s_dao->GetMaxFecha($id_dato_inundacion);
					$fecha_val_avalancha = $d_s_dao->GetMaxFecha($id_dato_avalancha);

					$PG = new PowerGraphic;
					$PG->title = "Vulnerabilidad Viviendas Rurales en SISBEN";
					$PG->type = $tipo_grafica[$mod_d_s_tipo_g[5]];
					$PG->skin      = 1;
					$PG->border = 0;

					$val = $d_s_dao->GetValorToReport($id_dato_ninguna,$id_ubicacion,$fecha_val_ninguna['ini'],$fecha_val_ninguna['fin'],$dato_para);
					$valor_ninguna = $val['valor'];
					$PG->x[0] = 'Ninguna';
					$PG->y[0] = $valor_ninguna;

					$val = $d_s_dao->GetValorToReport($id_dato_deslizamiento,$id_ubicacion,$fecha_val_deslizamiento['ini'],$fecha_val_deslizamiento['fin'],$dato_para);
					$valor_deslizamiento = $val['valor'];
					$PG->x[1] = 'Deslizamiento';
					$PG->y[1] = $valor_deslizamiento;

					$val = $d_s_dao->GetValorToReport($id_dato_inundacion,$id_ubicacion,$fecha_val_inundacion['ini'],$fecha_val_inundacion['fin'],$dato_para);
					$valor_inundacion = $val['valor'];
					$PG->x[2] = 'Inundacion';
					$PG->y[2] = $valor_inundacion;

					$val = $d_s_dao->GetValorToReport($id_dato_avalancha,$id_ubicacion,$fecha_val_avalancha['ini'],$fecha_val_avalancha['fin'],$dato_para);
					$valor_avalancha = $val['valor'];
					$PG->x[3] = 'Avalancha';
					$PG->y[3] = $valor_avalancha;

					/*$PG->x[4] = 'Otro';
					$PG->y[4] = 100 - $valor_avalancha - $valor_deslizamiento - $valor_inundacion - $valor_nacional - $valor_ninguna;
					*/

					$dato_vo = $d_s_dao->Get($id_dato_ninguna);
                    $fuente_n = '';
                    if (!empty($dato_vo->id_contacto)) {
                        $fuente_vo = $fuente_dao->Get($dato_vo->id_contacto);
                        $fuente_n = $fuente_vo->nombre;
                    }
					$PG->texto_1 = " | Fuente: $fuente_n";

					if ($valor_ninguna > 0 && $valor_deslizamiento > 0 && $valor_inundacion > 0 && $valor_avalancha > 0){
						//echo "<td><img src='/sissh/admin/lib/common/graphic.class.php?".$PG->create_query_string()."' border=0 /></td>";

						$img = $PG->create_graphic_minificha();
						$width_img_2_5 = imagesx($img);
						$img_b = $PG->fill_background($img,255,255,255);
						imagepng($img_b,$path_images.'/2_5.png');

						$img_web = $PG->draw_copyright($img);
						$img_web = $PG->fill_background($img_web,250,250,250);
						imagepng($img_web,$path_images.'/w_2_5.png');

						echo "<td><img src='$url_images/w_2_5.png' border=0 /></td>";

						//$pdf->addPngFromFile("$path_images/2_5.png",$left + $width_img_2_4 + $space_td_pdf,350,$width_img_2_5);
						imagedestroy($img);
					}
					else{
						echo "<td align='center'><br><b>Vulnerabilidad Viviendas Rurales en SISBEN</b><br><br>No hay Datos disponibles</td>";
					}
				}

				echo "</tr>";
				echo "<tr><td>&nbsp;</td></tr>";
				echo "<tr>";

				//COBERTURAS SERVICIOS PUBLICOS
				if (in_array(12,$mod_d_s)){

					$PG = new PowerGraphic;
					$PG->title = "Coberturas Servicios P�blicos Viviendas";
					$PG->type = $tipo_grafica[$mod_d_s_tipo_g[12]];
					$PG->skin      = 1;
					$PG->border = 0;
					$PG->axis_y = '% Viviendas';
					$PG->graphic_1 = 'DANE';
					$PG->graphic_2 = 'Sisben';
					$PG->decimals = 2;


					//DANE
					//Cobertura Servicio Alcantarillado = 348
					//Cobertura Servicio de Acueducto = 505
					//Cobertura Servicio Energ�a El�ctrica = 364
					//Cobertura Servicio Telef�nico = 383

					//SISBEN
					//Cobertura Vivienda SISBEN con Alcantarillado = 504
					//Cobertura Vivienda SISBEN con Servicio de Acueducto = 322
					//Cobertura Vivienda SISBEN con Servicio Energ�a El�ctrica = 337
					//Cobertura Vivienda SISBEN con Servicio Telef�nico = 545

					$id_dato_dane = array(348,505,364,383);
					$id_dato_sisben = array(504,322,337,545);
					$servicio = array("Alcantarillado","Acueducto","Energ�a El�ctrica","Tel�fono");

					for($i=0;$i<count($id_dato_dane);$i++){

						$PG->x[$i] = $servicio[$i];

						$id = $id_dato_dane[$i];

						//CONSULTA EL PERIODO MAS RECIENTE DEL VALOR DEL DATO
						$fecha = $d_s_dao->GetMaxFecha($id);

						$val = $d_s_dao->GetValorToReport($id,$id_ubicacion,$fecha['ini'],$fecha['fin'],$dato_para);

						if ($val['valor'] != 'N.D.'){
							$valor = $val['valor'];
							$valor = $d_s_dao->formatValor($val['id_unidad'],$valor,0);
							$PG->y[$i] = $valor;
						}

						//CONSULTA EL PERIODO MAS RECIENTE DEL VALOR DEL DATO
						$id = $id_dato_sisben[$i];
						$fecha = $d_s_dao->GetMaxFecha($id);
						$val = $d_s_dao->GetValorToReport($id,$id_ubicacion,$fecha['ini'],$fecha['fin'],$dato_para);

						if ($val['valor'] != 'N.D.'){
							$valor = $val['valor'];
							$valor = $d_s_dao->formatValor($val['id_unidad'],$valor,0);
							$PG->z[$i] = $valor;
						}
					}

					if (max($PG->y) > 0 || max($PG->z) > 0){

						$img = $PG->create_graphic_minificha();
						$width_img_2_12 = imagesx($img);
						$img_b = $PG->fill_background($img,255,255,255);
						imagepng($img_b,$path_images.'/2_12.png');

						$img_web = $PG->draw_copyright($img);
						$img_web = $PG->fill_background($img_web,250,250,250);
						imagepng($img_web,$path_images.'/w_2_12.png');

						echo "<td align='center'><img src='$url_images/w_2_12.png' border=0 /></td>";

						//$img = $PG->create_graphic_minificha();

						//$pdf->addPngFromFile("$path_images/2_12.png",$left,20,$width_img_2_12);
						imagedestroy($img);
					}

				}

				echo "</tr>";

				echo "</table>";
				echo "<br><br><div align='center'>[ P&aacute;gina 3 - Informaci&oacute;n Demogr&aacute;fica ]</div>";
			}

			/*** DESPLAZAMIENTO ULTIMOS N A�OS ***/
			if (in_array(1,$mod_des) || in_array(2,$mod_des) || in_array(3,$mod_des) || in_array(4,$mod_des)){

				$num_a = 7;
	//			$id_periodos = $periodo_dao->GetIDAtras($num_a);
				$fecha = getdate();
				$a_actual = $fecha['year'];
				$a_ini = $a_actual - $num_a;

	//			$anios = array_keys($id_periodos);
				$offset_td = 70;

				$f_c_1 = $des_dao->GetFechaCorte(2,'letra');
				$f_c_2 = $des_dao->GetFechaCorte(1,'letra');

				$perfil_fuente_desplazamiento = "Fuente: U.A.R.I.V Fecha de corte $f_c_1, CODHES fecha de corte $f_c_2 ";

				$perfil_titulo_desplazamiento = "Desplazamiento $a_ini - $a_actual";

				echo "</td></tr><tr><td>&nbsp;</td></tr>";
				echo "<tr><td><a name=\"desplazamiento\" href=\"#top\">^ Subir</a></td></tr>";
				echo "<tr><td class='pagina_minificha'>";
				echo $header_html;
				echo "<br>";

				echo "<table cellpadding='3' cellspacing='0' width='920' border=0>";
				echo "<tr><td class='titulo_pag_minificha'><b>$perfil_titulo_desplazamiento</b></td><td align='right'><b>$ubicacion->nombre</b></td></tr>";
				echo "<tr><td colspan='2' class='linea_minificha'><img src='images/spacer.gif' height='1'></td></tr>";
				echo "<tr><td colspan='2' bgcolor='#E1E1E1'><b>$perfil_fuente_desplazamiento</b></td></tr>";

				//$pdf->ezNewPage();
				//$pdf->addText($left,550,14,"<b>$perfil_titulo_desplazamiento</b>");
				//$pdf->addText($x_titulo_ubicacion,550,14,"<b>$ubicacion->nombre</b>");
				//$pdf->setLineStyle(1);
				//$pdf->line($left,540,$page_width - $right,540);  		//Superior
				////$pdf->line($left + 370,535,$left+370,$bottom);			//Mitad vertical
				////$pdf->line($left,270,$left + 370,270);					//Mitad horizontal Iz
				////$pdf->line($left + 370,270,$page_width - $right,270);	//Mitad horizontal Der

				$alto_rect = 20;

				//$pdf->setColor(0.9,0.9,0.9);
				//$pdf->filledRectangle($left,540 - $alto_rect - 1,$page_width - $right - $left,$alto_rect);
				//$pdf->setColor(0,0,0);
				$x_text = $page_width - $right - 420;
				//$pdf->addText($x_text,525,10,"<b>$fuentes_txt</b>");

				echo "<tr><td>&nbsp;</td></tr>";

				echo "<tr>";

				//RECEPCION REGISTRADA POR FUENTE
				if (in_array(1,$mod_des)){

					//PARA CODHES Y Acci�n Social
					$fuentes = $fuente_des_dao->GetAllArray('ID_FUEDES IN (1,2)');
					$tipos = $tipo_des_dao->GetAllArray('');

					//GRAFICA
					$PG = new PowerGraphic;
					$PG->title     = 'Recepci�n registrada por fuente';
					$PG->axis_x    = 'A�o';
					$PG->axis_y    = 'Personas';
					$PG->skin      = 1;
					$PG->type      = $tipo_grafica[$mod_des_tipo_g[1]];
					$PG->border = 0;
					$PG->decimals   = 0;

					$f = 1;
					foreach ($fuentes as $fuente){
						if ($fuente->id == 2)	$fuente->nombre = "U.A.R.I.V";
						eval("\$PG->graphic_".$f." = '".$fuente->nombre."';");
						$f++;
					}

					$exp = 0;
					$aa = 0;
	//				foreach($anios as $a){
					for($a=$a_ini;$a<=$a_actual;$a++){
						$PG->x[$aa] = $a;

						$f = 0;
						$f_corte = "| Corte ";
						foreach ($fuentes as $fuente){

                            if ($fuente->id == 2)	$fuente->nombre = "U.A.R.I.V";

							$valor = 0;
							foreach ($tipos as $tipo){
								//foreach ($id_periodos[$a] as $id_periodo){
									$valor += $des_dao->GetValorToReportTotalAAAA($exp,$fuente->id,$tipo->id,$a,$id_ubicacion,$dato_para);
								//}
							}
							eval("\$PG->".$arr[$f]."[".$aa."] = ".$valor.";");

							$f_c = $des_dao->GetFechaCorte($fuente->id,'letra');
							$f_corte .= " : $fuente->nombre $f_c";

							$f++;
						}
						$aa++;
					}

					$PG->texto_1   = $f_corte;

					//echo "<td class='td_grafica_borde_derecho'><img src='/sissh/admin/lib/common/graphic.class.php?".$PG->create_query_string()."' border=0 /></td>";
					$img = $PG->create_graphic_minificha();
					$width_img_3_1 = imagesx($img);
					$img_b = $PG->fill_background($img,255,255,255);
					imagepng($img_b,$path_images.'/3_1.png');

					$img_web = $PG->draw_copyright($img);
					$img_web = $PG->fill_background($img_web,250,250,250);
					imagepng($img_web,$path_images.'/w_3_1.png');

					//echo "<td class='td_grafica_borde_derecho'><img src='$url_images/w_3_1.png' border=0 /></td>";
					echo "<td align='center'><img src='$url_images/w_3_1.png' border=0 /></td>";

					//$pdf->addPngFromFile("$path_images/3_1.png",$left,255,$width_img_3_1);
					imagedestroy($img);
				}

				//RECEPCION REGISTRADA CODHES
				if (in_array(2,$mod_des)){
					//PARA CODHES
					$fuente = $fuente_des_dao->Get(1);
					$tipos = $tipo_des_dao->GetAllArray('id_tipo_despla=3');

					//GRAFICA
					$PG = new PowerGraphic;
					$PG->title     = 'Estimado Llegadas CODHES';
					$PG->axis_x    = 'A�o';
					$PG->axis_y    = 'Personas';
					$PG->skin      = 1;
					$PG->type      = $tipo_grafica[$mod_des_tipo_g[2]];
					$PG->border = 0;
					$PG->decimals   = 0;
					$PG->graphic_1 = "CODHES";

					$exp = 0;
					$aa = 0;
	//				foreach($anios as $a){
					for($a=$a_ini;$a<=$a_actual;$a++){
						$PG->x[$aa] = $a;

						$valor = 0;
						foreach ($tipos as $tipo){
	//						foreach ($id_periodos[$a] as $id_periodo){
								$valor += $des_dao->GetValorToReportTotalAAAA($exp,$fuente->id,$tipo->id,$a,$id_ubicacion,$dato_para);
	//						}
						}
						eval("\$PG->y[".$aa."] = ".$valor.";");

						$aa++;
					}

					$PG->texto_1 = " | $fuente->nombre Corte: $f_c_2";

					//echo "<td><img src='/sissh/admin/lib/common/graphic.class.php?".$PG->create_query_string()."' border=0 /></td>";

					$img = $PG->create_graphic_minificha();
					$width_img_3_2 = imagesx($img);
					$img_b = $PG->fill_background($img,255,255,255);
					imagepng($img_b,$path_images.'/3_2.png');

					$img_web = $PG->draw_copyright($img);
					$img_web = $PG->fill_background($img_web,250,250,250);
					imagepng($img_web,$path_images.'/w_3_2.png');

					echo "<td><img src='$url_images/w_3_2.png' border=0 /></td>";

					$offset_td = 5;

					$x_image = $left + $width_img_3_1 + $space_td_pdf + $offset_td;
					if ($x_image < ($page_width/2))	$x_image = ($page_width/2);
					//$pdf->addPngFromFile("$path_images/3_2.png",$x_image,270,$width_img_3_2);
					imagedestroy($img);
				}

				echo "</tr>";
				//echo "<tr><td colspan='2' class='linea_minificha'><img src='images/spacer.gif' height='1'></td></tr>";
				echo "<tr><td>&nbsp;</td></tr>";
				echo "<tr>";

				//RECEPCION REGISTRADA Acci�n Social
				if (in_array(3,$mod_des)){
					//PARA Acci�n Social
					$fuente = $fuente_des_dao->Get(2);
					$tipos = $tipo_des_dao->GetAllArray('id_tipo_despla NOT IN (3)');

					//GRAFICA
					$PG = new PowerGraphic;
					$PG->title     = 'Recepci�n registrada U.A.R.I.V';
					$PG->axis_x    = 'A�o';
					$PG->axis_y    = 'Personas';
					$PG->skin      = 1;
					$PG->type      = $tipo_grafica[$mod_des_tipo_g[3]];
					$PG->border = 0;
					$PG->decimals   = 0;

					$f = 1;
					foreach ($tipos as $tipo){
						eval("\$PG->graphic_".$f." = '".$tipo->nombre."';");
						$f++;
					}

					$exp = 0;
					$aa = 0;
					for($a=$a_ini;$a<=$a_actual;$a++){
						$PG->x[$aa] = $a;

						$t = 0;
						foreach ($tipos as $tipo){
							$valor = 0;
							$valor += $des_dao->GetValorToReportTotalAAAA($exp,$fuente->id,$tipo->id,$a,$id_ubicacion,$dato_para);
							eval("\$PG->".$arr[$t]."[".$aa."] = ".$valor.";");
							$t++;
						}
						$aa++;
					}

					$PG->texto_1 = " | $fuente->nombre Corte: $f_c_1";

					//echo "<td class='td_grafica_borde_derecho'><img src='/sissh/admin/lib/common/graphic.class.php?".$PG->create_query_string()."' border=0 /></td>";

					$img = $PG->create_graphic_minificha();
					$width_img_3_3 = imagesx($img);
					$img_b = $PG->fill_background($img,255,255,255);
					imagepng($img_b,$path_images.'/3_3.png');

					$img_web = $PG->draw_copyright($img);
					$img_web = $PG->fill_background($img_web,250,250,250);
					imagepng($img_web,$path_images.'/w_3_3.png');

					//echo "<td class='td_grafica_borde_derecho'><img src='$url_images/w_3_3.png' border=0 /></td>";
					echo "<td align='center'><img src='$url_images/w_3_3.png' border=0 /></td>";

					//$pdf->addPngFromFile("$path_images/3_3.png",$left,20,$width_img_3_3);
					imagedestroy($img);
				}

				//EXPULSION REGISTRADA (Acci�n Social)
				if (in_array(4,$mod_des)){
					//PARA Acci�n Social
					$fuente = $fuente_des_dao->Get(2);
					$tipos = $tipo_des_dao->GetAllArray('id_tipo_despla NOT IN (3)');

					//GRAFICA
					$PG = new PowerGraphic;
					$PG->title     = 'Expulsi�n registrada U.A.R.I.V';
					$PG->axis_x    = 'A�o';
					$PG->axis_y    = 'Personas';
					$PG->skin      = 1;
					$PG->type      = $tipo_grafica[$mod_des_tipo_g[4]];
					$PG->border = 0;
					$PG->decimals   = 0;
					$PG->graphic_1 = "Acci�n Social";

					$f = 1;
					foreach ($tipos as $tipo){
						eval("\$PG->graphic_".$f." = '".$tipo->nombre."';");
						$f++;
					}

					$exp = 1;
					$aa = 0;
	//				foreach($anios as $a){
					for($a=$a_ini;$a<=$a_actual;$a++){
						$PG->x[$aa] = $a;

						$t = 0;
						foreach ($tipos as $tipo){
							$valor = 0;
	//						foreach ($id_periodos[$a] as $id_periodo){
								$valor += $des_dao->GetValorToReportTotalAAAA($exp,$fuente->id,$tipo->id,$a,$id_ubicacion,$dato_para);
	//						}
							eval("\$PG->".$arr[$t]."[".$aa."] = ".$valor.";");
							$t++;
						}
						//eval("\$PG->y[".$aa."] = ".$valor.";");

						$aa++;
					}

					$PG->texto_1 = " | $fuente->nombre Corte: $f_c_1";

					//echo "<td><img src='/sissh/admin/lib/common/graphic.class.php?".$PG->create_query_string()."' border=0 /></td>";
					$img = $PG->create_graphic_minificha();
					$img_b = $PG->fill_background($img,255,255,255);
					imagepng($img_b,$path_images.'/3_4.png');

					$img_web = $PG->draw_copyright($img);
					$img_web = $PG->fill_background($img_web,250,250,250);
					imagepng($img_web,$path_images.'/w_3_4.png');

					echo "<td><img src='$url_images/w_3_4.png' border=0 /></td>";

					$offset_td = 5;
					$space_td_pdf = 5;
					//$img = $PG->create_graphic_minificha();
					$width_img_3_4 = imagesx($img);
					////$pdf->addImage($img,$left + $width_img + $space_td_pdf,100,$width_img);

					$x_image = $left + $width_img_3_4 + $space_td_pdf + $offset_td;
					if ($x_image < ($page_width/2))	$x_image = ($page_width/2);

					//$pdf->addPngFromFile("$path_images/3_4.png",$x_image,20,$width_img_3_4);
					imagedestroy($img);
				}

				echo "</tr>";
				echo "</table>";

				echo "<br><br><div align='center'>[ P&aacute;gina 4 - Desplazamiento ]</div>";
				echo "</td></tr><tr><td>&nbsp;</td></tr>";
				echo "<tr><td><a name=\"desplazamiento_2\" href=\"#top\">^ Subir</a></td></tr>";
				echo "<tr><td class='pagina_minificha'>";
				echo $header_html;

				echo "<table cellpadding='5' cellspacing='0' width='920' border=0>";
				echo "<tr><td class='titulo_pag_minificha'><b>$perfil_titulo_desplazamiento</b></td><td align='right'><b>$ubicacion->nombre</b></td></tr>";
				echo "<tr><td colspan='2' class='linea_minificha'><img src='images/spacer.gif' height='1'></td></tr>";
				echo "<tr><td colspan='2' bgcolor='#E1E1E1'><b>$perfil_fuente_desplazamiento</b></td></tr>";

				//$num_a = $a_actual - 1994;
				$aaaa_sin_fecha = 1899;
				$ini_acumulado = 1997;
				$num_a = 7;

				//$id_periodos = $periodo_dao->GetIDAtras($num_a);
				//$anios = array_keys($id_periodos);

				$offset_td = 80;
				//$pdf->ezNewPage();
				//$pdf->addText($left,550,14,"<b>$perfil_titulo_desplazamiento</b>");
				//$pdf->addText($x_titulo_ubicacion,550,14,"<b>$ubicacion->nombre</b>");
				//$pdf->setLineStyle(1);
				//$pdf->line($left,540,$page_width - $right,540);			//Superior
				////$pdf->line($left + 370,535,$left+370,$bottom);			//Medio Vertical
				////$pdf->line($left,270,$left + 370,270);					//Medio Horizontal Iz
				////$pdf->line($left + 370,270,$page_width - $right,270);	//Medio Horizontal Der

				//Cuadro de Fuente y poblaci�n total
				//CONSULTA EL PERIODO MAS RECIENTE DEL VALOR DEL DATO
				$alto_rect = 20;

				//$pdf->setColor(0.9,0.9,0.9);
				//$pdf->filledRectangle($left,540 - $alto_rect - 1,$page_width - $right - $left,$alto_rect);
				//$pdf->setColor(0,0,0);
				$x_text = $page_width - $right - 420;
				//$pdf->addText($x_text,525,10,"<b>$fuentes_txt</b>");

				echo "<tr><td>&nbsp;</td></tr>";
				echo "<tr>";


				//ACUMULADO RECEPCION REGISTRADA CODHES
				if (in_array(5,$mod_des)){
					$fuente = $fuente_des_dao->Get(1);
					$tipos = $tipo_des_dao->GetAllArray('id_tipo_despla=3');

					//GRAFICA
					$PG = new PowerGraphic;
					$PG->title     = 'Estimado Llegadas Acumulado CODHES';
					$PG->axis_x    = 'A�o';
					$PG->axis_y    = 'Personas';
					$PG->skin      = 1;
					$PG->type      = $tipo_grafica[$mod_des_tipo_g[5]];
					$PG->border = 0;
					$PG->decimals   = 0;
					$PG->graphic_1 = "CODHES";

					$exp = 0;
					$aa = 0;
					$valor = 0;

					for ($a=$ini_acumulado;$a<$a_ini;$a++){
						//SUMA LA CANTIDAD DEL PERIODO SIN FECHA, PARA QUE LOS TOTALES SEAN IGUALES
						$valor += $des_dao->GetValorToReportTotalAAAA($exp,$fuente->id,0,$a,$id_ubicacion,$dato_para);

						if ($a == $ini_acumulado){
							$valor += $des_dao->GetValorToReportTotalAAAA($exp,$fuente->id,0,$aaaa_sin_fecha,$id_ubicacion,$dato_para);
						}
					}

					for($a=$a_ini;$a<=$a_actual;$a++){
						$PG->x[$aa] = $a;

						foreach ($tipos as $tipo){
							$valor += $des_dao->GetValorToReportTotalAAAA($exp,$fuente->id,$tipo->id,$a,$id_ubicacion,$dato_para);
						}
						eval("\$PG->y[".$aa."] = ".$valor.";");

						$aa++;
					}

					$PG->texto_1 = " | $fuente->nombre Corte: $f_c_1";

					//echo "<td class='td_grafica_borde_derecho'><img src='/sissh/admin/lib/common/graphic.class.php?".$PG->create_query_string()."' border=0 /></td>";

					$img = $PG->create_graphic_minificha();
					$width_img_4_1 = imagesx($img);
					$img_b = $PG->fill_background($img,255,255,255);
					imagepng($img_b,$path_images.'/4_1.png');

					$img_web = $PG->draw_copyright($img);
					$img_web = $PG->fill_background($img_web,250,250,250);
					imagepng($img_web,$path_images.'/w_4_1.png');

					//echo "<td class='td_grafica_borde_derecho'><img src='$url_images/w_4_1.png' border=0 /></td>";
					echo "<td align='center'><img src='$url_images/w_4_1.png' border=0 /></td>";

					//$pdf->addPngFromFile("$path_images/4_1.png",$left,270,$width_img_4_1);
					imagedestroy($img);

				}

				//ACUMULADO RECEPCION REGISTRADA Acci�n Social
				if (in_array(6,$mod_des)){
					$fuente = $fuente_des_dao->Get(2);
					$tipos = $tipo_des_dao->GetAllArray('id_tipo_despla NOT IN (3)');

					//GRAFICA
					$PG = new PowerGraphic;
					$PG->title     = 'Recepci�n registrada Acumulado Acci�n Social';
					$PG->axis_x    = 'A�o';
					$PG->axis_y    = 'Personas';
					$PG->skin      = 1;
					$PG->type      = $tipo_grafica[$mod_des_tipo_g[6]];
					$PG->border = 0;
					$PG->decimals   = 0;
					$PG->graphic_1 = "A. Social";

					$exp = 0;
					$aa = 0;
					$valor = 0;

					for ($a=$ini_acumulado;$a<$a_ini;$a++){
						//SUMA LA CANTIDAD DEL PERIODO SIN FECHA, PARA QUE LOS TOTALES SEAN IGUALES
						$valor += $des_dao->GetValorToReportTotalAAAA($exp,$fuente->id,0,$a,$id_ubicacion,$dato_para);

						if ($a == $ini_acumulado){
							$valor += $des_dao->GetValorToReportTotalAAAA($exp,$fuente->id,0,$aaaa_sin_fecha,$id_ubicacion,$dato_para);
						}
					}

					for($a=$a_ini;$a<=$a_actual;$a++){
						$PG->x[$aa] = $a;

						foreach ($tipos as $tipo){
							$valor += $des_dao->GetValorToReportTotalAAAA($exp,$fuente->id,$tipo->id,$a,$id_ubicacion,$dato_para);
						}
						eval("\$PG->y[".$aa."] = ".$valor.";");

						$aa++;
					}

					$PG->texto_1 = " | $fuente->nombre Corte: $f_c_2";

					//echo "<td><img src='/sissh/admin/lib/common/graphic.class.php?".$PG->create_query_string()."' border=0 /></td>";
					$img = $PG->create_graphic_minificha();
					$img_b = $PG->fill_background($img,255,255,255);
					imagepng($img_b,$path_images.'/4_2.png');

					$img_web = $PG->draw_copyright($img);
					$img_web = $PG->fill_background($img_web,250,250,250);
					imagepng($img_web,$path_images.'/w_4_2.png');

					echo "<td><img src='$url_images/w_4_2.png' border=0 /></td>";

					$offset_td = 10;
					$width_img_4_2 = imagesx($img);

					$x_image = $left + $width_img_4_1 + $space_td_pdf + $offset_td;
					if ($x_image < ($page_width/2))	$x_image = ($page_width/2);

					//$pdf->addPngFromFile("$path_images/4_2.png",$x_image,270,$width_img_4_2);
					imagedestroy($img);
				}

				echo "</tr>";
				//echo "<tr><td colspan='2' class='linea_minificha'><img src='images/spacer.gif' height='1'></td></tr>";
				echo "<tr><td>&nbsp;</td></tr>";
				echo "<tr>";

				//ACUMULADO RECEPCION REGISTRADA POR FUENTE
				if (in_array(7,$mod_des)){
					//PARA CODHES Y Acci�n Social
					$fuentes = $fuente_des_dao->GetAllArray('ID_FUEDES IN (1,2)');
					$tipos = $tipo_des_dao->GetAllArray('');

					//GRAFICA
					$PG = new PowerGraphic;
					$PG->title     = 'Recepci�n registrada Acumulada por fuente';
					$PG->axis_x    = 'A�o';
					$PG->axis_y    = 'Personas';
					$PG->skin      = 1;
					$PG->type      = $tipo_grafica[$mod_des_tipo_g[7]];
					$PG->border = 0;
					$PG->decimals   = 0;

					$f = 1;
					foreach ($fuentes as $fuente){
						if ($fuente->nombre == 'Acci�n Social')	$fuente->nombre = "A. Social";

						eval("\$PG->graphic_".$f." = '".$fuente->nombre."';");
						$f++;

						$valor_t[$fuente->id] = 0;

						for ($a=$ini_acumulado;$a<$a_ini;$a++){
							//SUMA LA CANTIDAD DEL PERIODO SIN FECHA, PARA QUE LOS TOTALES SEAN IGUALES
							$valor_t[$fuente->id] += $des_dao->GetValorToReportTotalAAAA($exp,$fuente->id,0,$a,$id_ubicacion,$dato_para);

							if ($a == $ini_acumulado){
								$valor_t[$fuente->id] += $des_dao->GetValorToReportTotalAAAA($exp,$fuente->id,0,$aaaa_sin_fecha,$id_ubicacion,$dato_para);
							}
						}
					}

					$exp = 0;
					$aa = 0;
					$f_corte = " | Corte";
					for($a=$a_ini;$a<=$a_actual;$a++){
						$PG->x[$aa] = $a;
						$f = 0;

						foreach ($fuentes as $fuente){
							foreach ($tipos as $tipo){
								$valor_t[$fuente->id] += $des_dao->GetValorToReportTotalAAAA($exp,$fuente->id,$tipo->id,$a,$id_ubicacion,$dato_para);
							}
							eval("\$PG->".$arr[$f]."[".$aa."] = ".$valor_t[$fuente->id].";");

							if ($a == $a_ini){
								$f_c = $des_dao->GetFechaCorte($fuente->id,'letra');
								$f_corte .= " : $fuente->nombre $f_c";
							}

							$f++;
						}
						$aa++;
					}

					$PG->texto_1 = "$f_corte";

					//echo "<td class='td_grafica_borde_derecho'><img src='/sissh/admin/lib/common/graphic.class.php?".$PG->create_query_string()."' border=0 /></td>";

					$img = $PG->create_graphic_minificha();
					$width_img_4_3 = imagesx($img);
					$img_b = $PG->fill_background($img,255,255,255);
					imagepng($img_b,$path_images.'/4_3.png');

					$img_web = $PG->draw_copyright($img);
					$img_web = $PG->fill_background($img_web,250,250,250);
					imagepng($img_web,$path_images.'/w_4_3.png');

					//echo "<td class='td_grafica_borde_derecho'><img src='$url_images/w_4_3.png' border=0 /></td>";
					echo "<td align='center'><img src='$url_images/w_4_3.png' border=0 /></td>";

					//$pdf->addPngFromFile("$path_images/4_3.png",$left,20,$width_img_4_3);
					imagedestroy($img);
				}

				//ACUMULADO EXPULSION REGISTRADA
				if (in_array(8,$mod_des)){
					//PARA Acci�n Social
					$fuente = $fuente_des_dao->Get(2);
					$tipos = $tipo_des_dao->GetAllArray('id_tipo_despla NOT IN (3)');

					//GRAFICA
					$PG = new PowerGraphic;
					$PG->title     = 'Expulsi�n registrada Acumulado Acci�n Social';
					$PG->axis_x    = 'A�o';
					$PG->axis_y    = 'Personas';
					$PG->skin      = 1;
					$PG->type      = $tipo_grafica[$mod_des_tipo_g[8]];
					$PG->border = 0;
					$PG->decimals   = 0;
					$PG->graphic_1 = "A. Social";

					$exp = 1;
					$aa = 0;
					$valor = 0;

					for ($a=$ini_acumulado;$a<$a_ini;$a++){

						$valor += $des_dao->GetValorToReportTotalAAAA($exp,$fuente->id,0,$a,$id_ubicacion,$dato_para);

						//SUMA LA CANTIDAD DEL PERIODO SIN FECHA, PARA QUE LOS TOTALES SEAN IGUALES
						if ($a == $ini_acumulado){
							$valor += $des_dao->GetValorToReportTotalAAAA($exp,$fuente->id,0,$aaaa_sin_fecha,$id_ubicacion,$dato_para);
						}
					}

					for($a=$a_ini;$a<=$a_actual;$a++){
						$PG->x[$aa] = $a;

						foreach ($tipos as $tipo){
							$valor += $des_dao->GetValorToReportTotalAAAA($exp,$fuente->id,$tipo->id,$a,$id_ubicacion,$dato_para);
						}
						eval("\$PG->y[".$aa."] = ".$valor.";");

						$aa++;
					}

					$PG->texto_1 = " | $fuente->nombre Corte: $f_c_2";

					//echo "<td><img src='/sissh/admin/lib/common/graphic.class.php?".$PG->create_query_string()."' border=0 /></td>";

					$img = $PG->create_graphic_minificha();
					$img_b = $PG->fill_background($img,255,255,255);
					imagepng($img_b,$path_images.'/4_4.png');

					$img_web = $PG->draw_copyright($img);
					$img_web = $PG->fill_background($img_web,250,250,250);
					imagepng($img_web,$path_images.'/w_4_4.png');

					echo "<td><img src='$url_images/w_4_4.png' border=0 /></td>";

					$offset_td = 10;
					//$img = $PG->create_graphic_minificha();
					$width_img_4_4 = imagesx($img);
					////$pdf->addImage($img,$left + $width_img + $space_td_pdf,100,$width_img);

					$x_image = $left + $width_img_4_3 + $space_td_pdf + $offset_td;
					if ($x_image < ($page_width/2))	$x_image = ($page_width/2);


					//$pdf->addPngFromFile("$path_images/4_4.png",$x_image,20,$width_img_4_4);
					imagedestroy($img);
				}

				echo "</tr></table>";
				echo "<br><br><div align='center'>[ P&aacute;gina 5 - Desplazamiento ]</div>";

			}

			/*** MINAS ***/
			if (in_array(1,$mod_mina) || in_array(2,$mod_mina) || in_array(3,$mod_mina) || in_array(4,$mod_mina)){

				$minmax = $mina_dao->GetMinMaxFecha();
				$offset_td = 60;
				// Numero de a�os para que la gr�fica no se salga del pdf
				//$num_a = 5;

				//$a_ini = $minmax['min'];
				//Por ahora se deja fijo desde el 2000
				//$a_ini = 2000;  //Se usa el mismo de desplazamiento
				$a_fin = $minmax['max'];
				//$a_ini = $a_fin - $num_a;

				$hay_valor_mina_condicion = 0;
				$hay_valor_mina_sexo = 0;
				$hay_valor_mina_edad = 0;
				$hay_valor_mina_estado = 0;

				$sfuente_vo = $sub_fuente_dao->get(78);
				$fuente = $sfuente_vo->nombre;

				$perfil_titulo_mina = "Accidentes con Mina $a_ini - $a_fin";
				$perfil_fuente_mina = "Fuente: $fuente";

				//INICIO BUFFERING
				ob_start();

				echo "</td></tr><tr><td>&nbsp;</td></tr>";
				echo "<tr><td><a name=\"mina\" href=\"#top\">^ Subir</a></td></tr>";
				echo "<tr><td class='pagina_minificha'>";
				echo $header_html;

				echo "<table cellpadding='5' cellspacing='0' width='920' border=0>";
				echo "<tr><td class='titulo_pag_minificha'><b>$perfil_titulo_mina</b></td><td align='right'><b>$ubicacion->nombre</b></td></tr>";
				echo "<tr><td colspan='2' class='linea_minificha'><img src='images/spacer.gif' height='1'></td></tr>";
				echo "<tr><td colspan='2' bgcolor='#E1E1E1'><b>$perfil_fuente_mina</b></td></tr>";

				echo "<tr><td>&nbsp;</td></tr>";

				echo "<tr>";

				//EVENTOS POR A�O Y POR SEXO
				if (in_array(1,$mod_mina)){

					//GRAFICA
					$PG = new PowerGraphic;
					$PG->title     = "Accidente con Mina por sexo";
					$PG->axis_x    = 'A�o';
					$PG->axis_y    = 'Personas';
					$PG->skin      = 1;
					$PG->type      = $tipo_grafica[$mod_mina_tipo_g[1]];
					$PG->border = 0;
					$PG->decimals   = 0;
					$PG->texto_1 = " | $fuente";

					//Valores
					$aa = 0;
					for ($a=$a_ini;$a<=$a_fin;$a++){

						$PG->x[$aa] = $a;

						$num = 0;
						$e = 0;
						foreach ($id_sexos as $id_sexo){

							$sexo = $sexo_dao->Get($id_sexo);
							$f = $e + 1;
							eval("\$PG->graphic_".$f." = '".$sexo->nombre."';");

							$condicion = "ID_SEXO = $id_sexo AND YEAR(FECHA_REG_EVEN) = '$a'";
							$num = $mina_dao->GetValor($condicion,$id_ubicacion,$dato_para);
							eval("\$PG->".$arr[$e]."[".$aa."] = ".$num.";");

							if ($num > 0)	$hay_valor_mina_sexo = 1;

							$e++;
						}
						$aa++;
					}

					//echo "<td class='td_grafica_borde_derecho'><img src='/sissh/admin/lib/common/graphic.class.php?".$PG->create_query_string()."' border=0 /></td>";
					if ($hay_valor_mina_sexo == 1){
						$img = $PG->create_graphic_minificha();
						$img_b = $PG->fill_background($img,255,255,255);
						imagepng($img_b,$path_images.'/5_1.png');

						$img_web = $PG->draw_copyright($img);
						$img_web = $PG->fill_background($img_web,250,250,250);
						imagepng($img_web,$path_images.'/w_5_1.png');

						//echo "<td class='td_grafica_borde_derecho'><img src='$url_images/w_5_1.png' border=0 /></td>";
						echo "<td align='center'><img src='$url_images/w_5_1.png' border=0 /></td>";

						$width_img_5_1 = imagesx($img);

						imagedestroy($img);
					}
				}

				//EVENTOS POR A�O Y POR CONDICION
				if (in_array(2,$mod_mina)){

					//GRAFICA
					$PG = new PowerGraphic;
					$PG->title     = "Accidentes con Mina por condici�n";
					$PG->axis_x    = 'A�o';
					$PG->axis_y    = 'Personas';
					$PG->skin      = 1;
					$PG->type      = $tipo_grafica[$mod_mina_tipo_g[2]];
					$PG->border = 0;
					$PG->decimals   = 0;
					$PG->texto_1 = " | $fuente";

					//Valores
					$aa = 0;
					for ($a=$a_ini;$a<=$a_fin;$a++){

						$PG->x[$aa] = $a;

						$e = 0;
						foreach ($id_condiciones as $id_condicion){

							$condicion = $condicion_dao->Get($id_condicion);
							$f = $e + 1;
							eval("\$PG->graphic_".$f." = '".$condicion->nombre."';");

							$cond_sql = "ID_CONDICION = $id_condicion AND YEAR(FECHA_REG_EVEN) = '$a'";
							$num = $mina_dao->GetValor($cond_sql,$id_ubicacion,$dato_para);
							eval("\$PG->".$arr[$e]."[".$aa."] = ".$num.";");

							if ($num > 0)	$hay_valor_mina_condicion = 1;

							$e++;
						}
						$aa++;
					}
					//echo "<td align='center'><img src='/sissh/admin/lib/common/graphic.class.php?".$PG->create_query_string()."' border=0 /></td>";

					if ($hay_valor_mina_condicion == 1){
						$img = $PG->create_graphic_minificha();
						$img_b = $PG->fill_background($img,255,255,255);
						imagepng($img_b,$path_images.'/5_2.png');

						$img_web = $PG->draw_copyright($img);
						$img_web = $PG->fill_background($img_web,250,250,250);
						imagepng($img_web,$path_images.'/w_5_2.png');

						echo "<td><img src='$url_images/w_5_2.png' border=0 /></td>";

						$offset_td = 30;
						$width_img_5_2 = imagesx($img);

						////$pdf->addPngFromFile("$path_images/5_2.png",$left + 350 + $offset_td,280,$width_img_5_2);
						imagedestroy($img);
					}
				}

				echo "</tr>";

				//Linea para dividir la fila
				if (in_array(3,$mod_mina) && in_array(4,$mod_mina)){
					//echo "<tr><td colspan='2' class='linea_minificha'><img src='images/spacer.gif' height='1'></td></tr>";
				}

				echo "<tr><td>&nbsp;</td></tr>";

				echo "<tr>";
				//EVENTOS POR A�O Y POR ESTADO
				if (in_array(3,$mod_mina)){

					//Se consultan solo 2 estados
					$estados = $estado_mina_dao->GetAllArray('ID_ESTADO_MINA IN (1,2)');

					//GRAFICA
					$PG = new PowerGraphic;
					$PG->title     = "Accidentes con Mina por estado";
					$PG->axis_x    = 'A�o';
					$PG->axis_y    = 'Personas';
					$PG->skin      = 1;
					$PG->type      = $tipo_grafica[$mod_mina_tipo_g[3]];
					$PG->border = 0;
					$PG->decimals   = 0;
					$PG->texto_1 = " | $fuente";

					//Valores
					$aa = 0;
					for ($a=$a_ini;$a<=$a_fin;$a++){

						$PG->x[$aa] = $a;

						$num = 0;
						$e = 0;
						foreach ($estados as $estado){
							$f = $e + 1;
							eval("\$PG->graphic_".$f." = '".$estado->nombre."';");

	//						$condicion = "ID_ESTADO_MINA = $estado->id AND YEAR(FECHA) = '$a'";
							$condicion = "ID_ESTADO = $estado->id AND YEAR(FECHA_REG_EVEN) = '$a'";
							$num = $mina_dao->GetValor($condicion,$id_ubicacion,$dato_para);

	//						echo "arreglor".$arr[$e];
							eval("\$PG->".$arr[$e]."[".$aa."] = ".$num.";");

							if ($num > 0)	$hay_valor_mina_estado = 1;

							$e++;
						}
						$aa++;
					}

					//echo "<td class='td_grafica_borde_derecho'><img src='/sissh/admin/lib/common/graphic.class.php?".$PG->create_query_string()."' border=0 /></td>";

					if ($hay_valor_mina_estado == 1){
						$img = $PG->create_graphic_minificha();
						$img_b = $PG->fill_background($img,255,255,255);
						imagepng($img_b,$path_images.'/5_3.png');

						$img_web = $PG->draw_copyright($img);
						$img_web = $PG->fill_background($img_web,250,250,250);
						imagepng($img_web,$path_images.'/w_5_3.png');

						//echo "<td class='td_grafica_borde_derecho'><img src='$url_images/w_5_3.png' border=0 /></td>";
						echo "<td align='center'><img src='$url_images/w_5_3.png' border=0 /></td>";

						//$img = $PG->create_graphic_minificha();
						$width_img_5_3 = imagesx($img);
						////$pdf->addImage($img,$left,100,$width_img);
						////$pdf->addPngFromFile("$path_images/5_3.png",$left,20,$width_img_5_3);
						imagedestroy($img);
					}
				}

				//EVENTOS POR A�O Y POR GRUPO EDAD
				if (in_array(4,$mod_mina)){
					//GRAFICA
					$PG = new PowerGraphic;
					$PG->title     = "Accidentes con Mina por grupo de edad";
					$PG->axis_x    = 'A�o';
					$PG->axis_y    = 'Personas';
					$PG->skin      = 1;
					$PG->type      = $tipo_grafica[$mod_mina_tipo_g[4]];
					$PG->border = 0;
					$PG->decimals   = 0;
					$PG->texto_1 = " | $fuente";

					//Valores
					$aa = 0;
					for ($a=$a_ini;$a<=$a_fin;$a++){

						$PG->x[$aa] = $a;

						$e = 0;
						foreach ($id_edades as $id_edad){

							$edad = $edad_dao->Get($id_edad);
							$f = $e + 1;
							eval("\$PG->graphic_".$f." = '".$edad->nombre."';");

	//						$cond_sql = "ID_EDAD = $id_edad AND YEAR(FECHA) = '$a'";
							$cond_sql = "ID_EDAD = $id_edad AND YEAR(FECHA_REG_EVEN) = '$a'";
							$num = $mina_dao->GetValor($cond_sql,$id_ubicacion,$dato_para);
							eval("\$PG->".$arr[$e]."[".$aa."] = ".$num.";");

							if ($num > 0)	$hay_valor_mina_edad = 1;

							$e++;
						}
						$aa++;
					}
					//echo "<td align='center'><img src='/sissh/admin/lib/common/graphic.class.php?".$PG->create_query_string()."' border=0 /></td>";

					if ($hay_valor_mina_edad == 1){
						$img = $PG->create_graphic_minificha();
						$img_b = $PG->fill_background($img,255,255,255);
						imagepng($img_b,$path_images.'/5_4.png');

						$img_web = $PG->draw_copyright($img);
						$img_web = $PG->fill_background($img_web,250,250,250);
						imagepng($img_web,$path_images.'/w_5_4.png');

						echo "<td><img src='$url_images/w_5_4.png' border=0 /></td>";

						$offset_td = 30;
						//$img = $PG->create_graphic_minificha();
						$width_img_5_4 = imagesx($img_b);
						////$pdf->addImage($img,$left + $width_img + $space_td_pdf,100,$width_img);

						////$pdf->addPngFromFile("$path_images/5_4.png",$left + 350 + $offset_td,20,$width_img_5_4);
						imagedestroy($img);
					}
				}
			}

			echo "</tr></table>";
			echo "<br><br><div align='center'>[ P&aacute;gina 6 - Accidentes con Mina ]</div>";
			echo "</td></tr>";

			//SI HAY VALORES DE MINA SE MUESTRA
			if ($hay_valor_mina_condicion > 0 || $hay_valor_mina_edad > 0 || $hay_valor_mina_estado > 0 || $hay_valor_mina_sexo > 0){

				ob_end_flush();

				//$pdf->ezNewPage();
				//$pdf->addText($left,550,14,"<b>Accidentes con Mina ". $a_ini." - ".$a_fin."</b>");
				//$pdf->addText($x_titulo_ubicacion,550,14,"<b>$ubicacion->nombre</b>");
				//$pdf->setLineStyle(1);
				//$pdf->line($left,540,$page_width - $right,540);			//Superior

				$alto_rect = 20;

				//$pdf->setColor(0.9,0.9,0.9);
				//$pdf->filledRectangle($left,540 - $alto_rect - 1,$page_width - $right - $left,$alto_rect);
				//$pdf->setColor(0,0,0);
				$x_text = $page_width - $right - 260;
				//$pdf->addText($x_text,525,10,"<b>Fuente: $fuente </b>");


				if ($hay_valor_mina_edad > 0){
					//$pdf->addPngFromFile("$path_images/5_4.png",$left + 350 + $offset_td,0,$width_img_5_4);
				}
				if ($hay_valor_mina_estado > 0){
					//$pdf->addPngFromFile("$path_images/5_3.png",$left,0,$width_img_5_3);
				}
				if ($hay_valor_mina_condicion > 0){
					//$pdf->addPngFromFile("$path_images/5_2.png",$left + 350 + $offset_td,260,$width_img_5_2);
				}
				if ($hay_valor_mina_sexo > 0){
					//$pdf->addPngFromFile("$path_images/5_1.png",$left,260,$width_img_5_1);
				}
			}
			else{
				ob_end_clean();
			}
			/*** INDICE DE RIESGO HUMANITARIO ***/
			if (isset($mod_irh[0])){

				function top10($arrs_id,$id_dato,$dato_para,$id_ubicacion,$fecha_val){
					//INICIALIZA VARIABLES
					$d_s_dao = New DatoSectorialDAO();
					$mun_dao = New MunicipioDAO();

					//SI ES PERFIL MUNICIPAL, COLOCAL EL MPIO EN PRIMERA POSICION
					$_10_mpios_mun = array();
					$_10_mpios = array();
					if ($dato_para == 2){
						$val = $d_s_dao->GetValorToReport($id_dato,$id_ubicacion,$fecha_val['ini'],$fecha_val['fin'],2);
						$valor = $val['valor'];

						if ($valor != 'N.D.')	$_10_mpios_mun[$mun_dao->GetName($id_ubicacion)] = $valor;
					}
					foreach($arrs_id as $id_mun){

						$val = $d_s_dao->GetValorToReport($id_dato,$id_mun,$fecha_val['ini'],$fecha_val['fin'],2);
						$valor = $val['valor'];
						if ($valor != 'N.D.')	$_10_mpios[$mun_dao->GetName($id_mun)] = $valor;

					}

					//ORDENA DE MAYOR A MENOR
					arsort($_10_mpios);
					$arr_todos = array_merge($_10_mpios_mun,$_10_mpios);

					return $arr_todos;
				}

				echo "</td></tr><tr><td>&nbsp;</td></tr>";
				echo "<tr><td><a name=\"irh\" href=\"#top\">^ Subir</a></td></tr>";
				echo "<tr><td class='pagina_minificha'>";
				echo $header_html;

				echo "<table cellpadding='0' cellspacing='0' width='920' border=0>";
				echo "<tr><td class='titulo_pag_minificha'><b>Indice de Riesgo de Situaci�n Humanitaria</b></td><td align='right'><b>$ubicacion->nombre</b></td></tr>";
				echo "<tr><td colspan='2' class='linea_minificha'><img src='images/spacer.gif' height='1'></td></tr>";

				//$pdf->ezNewPage();
				//$pdf->addText($left,550,14,"<b>Indice de Riesgo de Situaci�n Humanitaria </b>");
				//$pdf->addText($x_titulo_ubicacion,550,14,"<b>$ubicacion->nombre</b>");
				//$pdf->setLineStyle(1);
				//$pdf->line($left,540,$page_width - $right,540);			//Superior
				////$pdf->line($left + 370,535,$left+370,$bottom);			//Medio Vertical
				////$pdf->line($left,280,$left + 370,280);					// Medio Horizontal Iz
				////$pdf->line($left + 370,280,$page_width - $right,280);	// Medio Horizontal Der

				echo "<tr><td>&nbsp;</td></tr>";

				echo "<tr>";

				if ($dato_para == 1){
					$arrs_id = $mun_dao->GetAllArrayID("ID_DEPTO = '$id_ubicacion'",'');
				}
				else if ($dato_para == 2){
					$arrs_id = $mun_dao->GetAllArrayID("ID_DEPTO = '$id_ubicacion_depto' AND ID_MUN <> $id_ubicacion",'');
				}
				else{
					$arrs_id = $mun_dao->GetAllArrayID('','');
				}

				// Variables
				$num_comp = 5;
				$irsh_titulos = array('',
									  'Indice de Riesgo de S. Humanitaria',
									  'Subindice Capacidades',
									  'Subindice Conflicto',
									  'Subindice Econ�mico',
									  'Subindice Social',
									  'Indice de Riesgo de S. Humanitaria',
									  'Subindice Amenaza',
									  'Subinidce Vulnerabilidad'
									  );
				$irsh_id_datos = array('',232,235,236,234,233,588,589,590);
				$irsh_escalas = array('',1,1,0.8,0.8,0.8,1,1,1);
				$irsh_html_open = array('','<tr><td>','<td>','<tr><td>','<td>','<td>','<tr><td>','&nbsp;','&nbsp;');
				$irsh_html_close = array('','</td>','</td></tr><tr><td>&nbsp;</td></tr>','</td>','</td>','</td></tr><tr><td>&nbsp;</td></tr>','&nbsp;','&nbsp;','</td></tr>');

				foreach ($mod_irh as $id_mod_irh){

					$id_dato = $irsh_id_datos[$id_mod_irh];

					//CONSULTA EL PERIODO MAS RECIENTE DEL VALOR DEL DATO
					$fecha_val = $d_s_dao->GetMaxFecha($id_dato);

					$PG = new PowerGraphic;
					$PG->title = $irsh_titulos[$id_mod_irh];
					$PG->axis_y    = '%';
					$PG->skin      = 1;
					$PG->type      = $tipo_grafica[$mod_irh_tipo_g[$id_mod_irh]];
					$PG->border = 0;
					$PG->escala = $irsh_escalas[$id_mod_irh];
					$PG->decimals   = 3;

					$_10_mpios = top10($arrs_id,$id_dato,$dato_para,$id_ubicacion,$fecha_val);

					$num = 0;
					foreach($_10_mpios as $nom_mpio => $valor){
						if ($num < $num_comp){
							$PG->x[$num] = ucwords(strtolower($nom_mpio));
							$PG->y[$num] = $valor;
							$num++;
						}
						else{
							break;
						}
					}

					$img_name = "8_$id_mod_irh.png";
					$img_file = "$path_images/$img_name";
					$img_web_file = "$path_images/w_$img_name";

					$img = $PG->create_graphic_minificha();
					$img_b = $PG->fill_background($img,255,255,255);
					imagepng($img_b,$img_file);

					$img_web = $PG->draw_copyright($img);
					$img_web = $PG->fill_background($img_web,250,250,250);
					imagepng($img_web,$img_web_file);

					echo $irsh_html_open[$id_mod_irh]."<img src='$url_images/$img_name' border=0 />".$irsh_html_close[$id_mod_irh];

					//$width_img_8_1 = imagesx($img);
					//$height_img_8_1 = imagesy($img);

					imagedestroy($img);
				}
				echo "</tr></table>";
				echo "<br><br><div align='center'>[ P&aacute;gina 7 - Indice de Riesgo de Situacion Humanitaria ]</div>";
				echo "</td></tr>";

			}

			/*** ORGS ***/
			if (in_array(1,$mod_org) || in_array(2,$mod_org) || in_array(3,$mod_org) || in_array(4,$mod_org)){

				$dato_para_org = 1;
				if ($dato_para == 2)	$dato_para_org = 0;
				if ($dato_para == 3)	$dato_para_org = 2;

				//ID TIPOS ORGS
				//Internacional
				$id_int = "4,5,2,3,11";

				//Nacional-Estado
				$id_nac_est = "17,10,16";

				echo "</td></tr><tr><td>&nbsp;</td></tr>";
				echo "<tr><td><a name=\"org\" href=\"#top\">^ Subir</a></td></tr>";
				echo "<tr><td class='pagina_minificha'>";
				echo $header_html;

				echo "<table cellpadding='0' cellspacing='0' width='920' border=0>";
				echo "<tr><td class='titulo_pag_minificha'><b>Organizaciones</b></td><td align='right'><b>$ubicacion->nombre</b></td></tr>";
				echo "<tr><td colspan='2' class='linea_minificha'><img src='images/spacer.gif' height='1'></td></tr>";

				//$pdf->ezNewPage();
				//$pdf->addText($left,550,14,"<b>Organizaciones ". $a_ini." - ".$a_fin."</b>");
				//$pdf->addText($x_titulo_ubicacion,550,14,"<b>$ubicacion->nombre</b>");
				//$pdf->setLineStyle(1);
				//$pdf->line($left,540,$page_width - $right,540);			//Superior
				////$pdf->line($left + 370,535,$left+370,$bottom);			//Medio Vertical
				////$pdf->line($left,280,$left + 370,280);					// Medio Horizontal Iz
				////$pdf->line($left + 370,280,$page_width - $right,280);	// Medio Horizontal Der

				echo "<tr><td>&nbsp;</td></tr>";

				echo "<tr>";

				//ORGS POR TIPO
				if (in_array(1,$mod_org)){

					$PG = new PowerGraphic;
					$PG->title = "Organizaciones por Tipo";
					$PG->skin      = 1;
					$PG->type      = $tipo_grafica[$mod_org_tipo_g[1]];
					$PG->border = 0;
					$PG->decimals   = 0;
					$PG->graphic_1 = "Organizaciones";
					$f = 0;

					//Internacional
					$arrs_int = $tipo_org_dao->GetAllArray("id_tipo IN ($id_int)");

					//Numero
					$num_total_int = 0;
					foreach($arrs_int as $arr){
						$num_orgs = $org_dao->numOrgsConteo('tipo',$arr->id,$dato_para_org,$id_ubicacion,'','');
						$num_total_int += $num_orgs['total'];
					}

					$PG->x[0] = "Internacional";
					$PG->y[0] = $num_total_int;

					//Nacional-Estado
					$arrs_nac_est = $tipo_org_dao->GetAllArray("id_tipo IN ($id_nac_est)");

					//Numero
					$num_total_nac_est = 0;
					foreach($arrs_nac_est as $arr){
						$num_orgs = $org_dao->numOrgsConteo('tipo',$arr->id,$dato_para_org,$id_ubicacion,'','');
						$num_total_nac_est += $num_orgs['total'];
					}

					$PG->x[1] = "Nacional-Estado";
					$PG->y[1] = $num_total_nac_est;

					//Nacional-Sociedad Civil
					$arrs_nac_soc = $tipo_org_dao->GetAllArray("id_tipo NOT IN (".$id_int.",".$id_nac_est.")");

					//Numero
					$num_total_nac_soc = 0;
					foreach($arrs_nac_soc as $arr){
						$num_orgs = $org_dao->numOrgsConteo('tipo',$arr->id,$dato_para_org,$id_ubicacion,'','');
						$num_total_nac_soc += $num_orgs['total'];
					}

					$PG->x[2] = "Nacional-Sociedad Civil";
					$PG->y[2] = $num_total_nac_soc;

					/*foreach($arrs as $arr){
						$num_orgs = $org_dao->numOrgsConteo('tipo',$arr->id,$dato_para_org,$id_ubicacion,'','');

						$PG->x[$f] = $arr->nombre_es;
						$PG->y[$f] = $num_orgs['total'];
						$f++;
					}*/

					//echo "<td class='td_grafica_borde_derecho'><img src='/sissh/admin/lib/common/graphic.class.php?".$PG->create_query_string()."' border=0 /></td>";

					$img = $PG->create_graphic_minificha();
					$img_b = $PG->fill_background($img,255,255,255);
					imagepng($img_b,$path_images.'/6_1.png');

					$img_web = $PG->draw_copyright($img);
					$img_web = $PG->fill_background($img_web,250,250,250);
					imagepng($img_web,$path_images.'/w_6_1.png');

					//echo "<td class='td_grafica_borde_derecho'><img src='$url_images/w_6_1.png' border=0 /></td>";
					echo "<td align='center'><img src='$url_images/w_6_1.png' border=0 /></td>";

					//$img = $PG->create_graphic_minificha();
					$width_img_6_1 = imagesx($img);

					//$pdf->addPngFromFile("$path_images/6_1.png",$left,280,$width_img_6_1);
					imagedestroy($img);
				}

				//INTERNACIONAL
				if (in_array(2,$mod_org)){
					$arrs = $sector_dao->GetAllArray('');

					$PG = new PowerGraphic;
					$PG->title = "Internacional";
					$PG->skin      = 1;
					$PG->type      = $tipo_grafica[$mod_org_tipo_g[2]];
					$PG->border = 0;
					$PG->decimals   = 0;

					//Internacional
					$arrs_int = $tipo_org_dao->GetAllArray("id_tipo IN ($id_int)");
					$f = 0;
					foreach($arrs_int as $arr){
						$num_orgs = $org_dao->numOrgsConteo('tipo',$arr->id,$dato_para_org,$id_ubicacion,'','');

						$PG->x[$f] = $arr->nombre_es;
						$PG->y[$f] = $num_orgs['total'];

						$f++;
					}

					//echo "<td><img src='/sissh/admin/lib/common/graphic.class.php?".$PG->create_query_string()."' border=0 /></td></tr>";

					$img = $PG->create_graphic_minificha();
					$img_b = $PG->fill_background($img,255,255,255);
					imagepng($img_b,$path_images.'/6_2.png');

					$img_web = $PG->draw_copyright($img);
					$img_web = $PG->fill_background($img_web,250,250,250);
					imagepng($img_web,$path_images.'/w_6_2.png');

					echo "<td align='center'><img src='$url_images/w_6_2.png' border=0 /></td></tr>";

					//$img = $PG->create_graphic_minificha();
					$width_img_6_2 = imagesx($img);


					//$pdf->addPngFromFile("$path_images/6_2.png",$left + $width_img_6_1,360,$width_img_6_2);
					imagedestroy($img);
				}

				echo "</tr>";
				//echo "<tr><td colspan='2' class='linea_minificha'><img src='images/spacer.gif' height='1'></td></tr>";
				echo "<tr><td>&nbsp;</td></tr>";
				echo "<tr>";

				//NACIONAL-ESTADO
				if (in_array(3,$mod_org)){

					$PG = new PowerGraphic;
					$PG->title = "Nacional-Estado";
					$PG->skin      = 1;
					$PG->type      = $tipo_grafica[$mod_org_tipo_g[3]];
					$PG->border = 0;
					$PG->decimals   = 0;

					//Internacional
					$arrs_nac_est = $tipo_org_dao->GetAllArray("id_tipo IN ($id_nac_est)");
					$f = 0;
					foreach($arrs_nac_est as $arr){
						$num_orgs = $org_dao->numOrgsConteo('tipo',$arr->id,$dato_para_org,$id_ubicacion,'','');

						$PG->x[$f] = $arr->nombre_es;
						$PG->y[$f] = $num_orgs['total'];

						$f++;
					}

					//echo "<td class='td_grafica_borde_derecho'><img src='/sissh/admin/lib/common/graphic.class.php?".$PG->create_query_string()."' border=0 /></td>";

					$img = $PG->create_graphic_minificha();
					$img_b = $PG->fill_background($img,255,255,255);
					imagepng($img_b,$path_images.'/6_3.png');

					$img_web = $PG->draw_copyright($img);
					$img_web = $PG->fill_background($img_web,250,250,250);
					imagepng($img_web,$path_images.'/w_6_3.png');

					//echo "<td class='td_grafica_borde_derecho'><img src='$url_images/w_6_3.png' border=0 /></td>";
					echo "<td align='center'><img src='$url_images/w_6_3.png' border=0 /></td>";

					//$img = $PG->create_graphic_minificha();
					$width_img_6_3 = imagesx($img);

					//$pdf->addPngFromFile("$path_images/6_3.png",$left,80,$width_img_6_3);
					imagedestroy($img);
				}

				//NACIONAL-SOC. CIVIL
				if (in_array(4,$mod_org)){

					$PG = new PowerGraphic;
					$PG->title = "Nacional-Sociedad Civil";
					$PG->skin      = 1;
					$PG->type      = $tipo_grafica[$mod_org_tipo_g[4]];
					$PG->border = 0;
					$PG->decimals   = 0;

					//CONSULTAR ID DE TIPOS QUE TIENEN SECTOR PRIVADO PARA AGRUPAR
					$arrs_priv = $tipo_org_dao->GetAllArrayID("nomb_tipo_es REGEXP 'privado'");
					$id_priv = implode(",",$arrs_priv);

					$arrs_nac_soc = $tipo_org_dao->GetAllArray("id_tipo NOT IN (".$id_int.",".$id_nac_est.",".$id_priv.")");
					$f = 0;
					foreach($arrs_nac_soc as $arr){
						$num_orgs = $org_dao->numOrgsConteo('tipo',$arr->id,$dato_para_org,$id_ubicacion,'','');

						$PG->x[$f] = $arr->nombre_es;
						$PG->y[$f] = $num_orgs['total'];

						$f++;
					}

					//PRIVADO
					$arrs_priv = $tipo_org_dao->GetAllArray("id_tipo IN ($id_priv)");
					$num_total_priv = 0;
					foreach($arrs_priv as $arr){
						$num_orgs = $org_dao->numOrgsConteo('tipo',$arr->id,$dato_para_org,$id_ubicacion,'','');
						$num_total_priv += $num_orgs['total'];
					}

					$PG->x[$f] = "Sector Privado";
					$PG->y[$f] = $num_total_priv;

					//echo "<td><img src='/sissh/admin/lib/common/graphic.class.php?".$PG->create_query_string()."' border=0 /></td></tr>";

					$img = $PG->create_graphic_minificha();
					$img_b = $PG->fill_background($img,255,255,255);
					imagepng($img_b,$path_images.'/6_4.png');

					$img_web = $PG->draw_copyright($img);
					$img_web = $PG->fill_background($img_web,250,250,250);
					imagepng($img_web,$path_images.'/w_6_4.png');

					echo "<td align='center'><img src='$url_images/w_6_4.png' border=0 /></td>";

					//$img = $PG->create_graphic_minificha();
					$width_img_6_4 = imagesx($img);

					//$pdf->addPngFromFile("$path_images/6_4.png",$left + $width_img_6_3 +30,20,$width_img_6_4);
					imagedestroy($img);
				}
				echo "</tr>";

				//ORGS POR SECTOR
				/*if (in_array(2,$mod_org)){
					$arrs = $sector_dao->GetAllArray('');

					$PG = new PowerGraphic;
					$PG->title = "Organizaciones por Sector";
					$PG->skin      = 1;
					$PG->type      = $tipo_grafica[$mod_org_tipo_g[2]];
					$PG->border = 0;
					$PG->decimals   = 0;

					$f = 0;
					foreach($arrs as $arr){
						$num_orgs = $org_dao->numOrgsConteo('sector',$arr->id,$dato_para_org,$id_ubicacion,'','');

						$PG->x[$f] = $arr->nombre_es;
						$PG->y[$f] = $num_orgs['total'];
						$f++;
					}
					echo "<td><img src='/sissh/admin/lib/common/graphic.class.php?".$PG->create_query_string()."' border=0 /></td></tr>";

					$img = $PG->create_graphic_minificha();
					$width_img_6_2 = imagesx($img);
					imagepng($img,$path_images.'/6_2.png');
					//$pdf->addPngFromFile("$path_images/6_2.png",$left,20,$width_img_6_2);
					imagedestroy($img);
				}
				//ORGS POR MPIO
				if (in_array(3,$mod_org)){
					$arrs_id = $mun_dao->GetAllArrayID("ID_DEPTO = '$id_ubicacion'",'');

					$PG = new PowerGraphic;
					$PG->title = "Organizaciones por Municipio";
					$PG->skin      = 1;
					$PG->type      = 8;
					$PG->border = 0;
					$f = 0;
					foreach($arrs_id as $id_mun){

						$num_orgs = $org_dao->numOrgsConteo('municipio',0,2,$id_mun,'','');
						$PG->x[$f] = $mun_dao->GetName($id_mun);
						$PG->y[$f] = $num_orgs['total'];
						$f++;

					}

					echo "<tr><td><img src='/sissh/admin/lib/common/graphic.class.php?".$PG->create_query_string()."' border=0 /></td></tr>";

					$img = $PG->create_graphic_minificha();
					$width_img_6_3 = imagesx($img);
					imagepng($img,$path_images.'/6_3.png');
					//$pdf->addPngFromFile("$path_images/6_3.png",$left,280,$width_img_6_3);
					imagedestroy($img);

				}*/

				echo "</tr></table>";
				echo "<br><br><div align='center'>[ P&aacute;gina 8 - Organizaciones ]</div>";
				echo "</td></tr>";

			}

			?>

			</table>
			<?

			$content_cache = '<html xmlns="http://www.w3.org/1999/xhtml">
							<head>
							<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
							<title>Sistema de Informaci&oacute;n Central OCHA - Colombia</title>
							<link href="/sissh/style/consulta.css" rel="stylesheet" type="text/css" />
							</head><body><div id="cont">';

			$content_cache .= '<br>
								<table cellpadding="5" cellspacing="1">';


			$content_cache .= ob_get_contents()."</div></body></html>";


			//ARCHIVO CACHE
			$dir_cache_perfil = "perfiles/";
			$path_file = "$dir_cache_perfil/perfil_$id_ubicacion";

			//if ($formato == 'html'){
			if ($formato == 'html' && $id_ubicacion != '00'){
				ob_end_flush();
			}
			else{
				ob_end_clean();
			}

			//$pdfcode = //$pdf->ezOutput();

			if ($id_ubicacion != '00'){
				$this->createFileCache($content_cache,"$path_file.htm");
				//$this->createFileCache(//$pdfcode,"$path_file.pdf");
			}

			if ($formato == 'pdf') include('consulta/perfil.php');

		}  //Fin generar perfil - no cache
		//}
	}

	/**
	* Create File cache
	* @access public
	* @param string $file_content
	* @param string $nom_file
	*/
	function createFileCache($file_content,$nom_file){

		$file = New Archivo();

		//CREA UN ARCHIVO LOCAL
		$nom_archivo = $nom_file;

		$fp = $file->Abrir($nom_archivo,'w+');
		$file->Escribir($fp,$file_content);
		$file->Cerrar($fp);
	}

    /**
	* Get file cache
	* @access public
	* @param string $nom_file
	*/
	function getFileCache($nom_file){

		$file = New Archivo();

        $fp = $file->Abrir($nom_file,'r');
		return $file->LeerEnString($fp,$nom_file);
	}

	/**
	* Define si se debe generar el perfil, comparando fecha de modificacion de cache
	* @access public
	* @param string $cache_file Nombre del archivo del perfil
	* @return boolean $gen_perfil
	*/
	function siGenerarPerfil($cache_file){

		include("admin/config.php");

		$archivo = New Archivo();

		$dias_cache_ok = $conf['perfil']['dias_cache']; // => en config.php

		$gen_perfil = 0;

		if ($archivo->Existe($cache_file)){
			//Check del tiempo del cache, si es mayor a 7 dias, genera un nuevo archivo
            $dias = (time() - $archivo->fechaModificacion($cache_file))/60/60/24;

			if ($dias > $dias_cache_ok) $gen_perfil = 1;
		}
		else	$gen_perfil = 1;

		return $gen_perfil;
	}

    /**
	* Check si existe un archivo static
	* @access public
    *
    * @param string $pat_file  Path absoluto
    *
	* @return boolean $e
	*/
	function existsFileCache($path_file){

		$archivo = New Archivo();

		return $archivo->Existe($path_file);
	}

    /**
	* Define si se debe generar una pagina estatica
	* @access public
    *
    * @param string $modulo
    * @param string $static_file Nombre de la pagina est�tica
	* @return boolean $gen
	*/
	function siGenerarStaticByTime($modulo,$static_file){

		include("admin/config.php");

		$archivo = New Archivo();

        $cache_file =  $conf['static_cache'].$static_file; // => en config.php
		$horas_cache = $conf[$modulo]['horas_cache']; // => en config.php

		$gen = false;

		if ($archivo->Existe($cache_file)){
			//Check del tiempo del cache, si es mayor a 7 dias, genera un nuevo archivo
            $horas = (time() - $archivo->fechaModificacion($cache_file))/60/60;

			if ($horas > $horas_cache) $gen = true;
		}
		else	$gen = true;

		return $gen;
	}

	/**
	* Borra todos los archivos cache de perfil, se usa al importar datos sectoriales
	*/
	function borrarCache(){
		$archivo = New Archivo();

		$archivo->borrarContenidoDirectorio($this->dir_cache_perfil);
		$archivo->borrarContenidoDirectorio($this->dir_cache_images_perfil);

	}

    /**
	* Borra todos los archivos cache de paginas estaticas
	*/
	function borrarCacheStatic($dir){
		$archivo = New Archivo();

		$archivo->borrarContenidoDirectorio($this->dir_cache_static.$dir);

	}

	/**
	* Borra todos los archivos cache de perfil, se usa al importar Desplazamiento
	*/
	function borrarCacheImportarDesplazamiento(){
		$archivo = New Archivo();

		$dir = $this->dir_cache_perfil;
		$archivo->borrarContenidoDirectorio($dir);
		$archivo->borrarContenidoDirectorio($this->dir_cache_images_perfil);

		$dir = $this->dir_cache_resumen_desplazamiento;
		$archivo->borrarContenidoDirectorio($dir);

	}

	/**
	* Borra cache de mapa, se usa al importar datos sectoriales o desplazamiento o crear organizaciones
	* @access public
	* @param string $caso dato_sector o desplazamiento
	* @param int $id ID de: Dato Sectorial o Fuente de Desplazamiento
	*/
	function borrarCacheMapa($caso,$id){

		switch ($caso){

			case 'dato_sector':
				$sql = "DELETE FROM cache_mapa_dato_sector WHERE id_dato = $id";
			break;

			case 'dato_sector_id':
				$sql = "DELETE FROM cache_mapa_dato_sector WHERE id_cache_mapa_dato_sector = $id";
			break;

			case 'desplazamiento':
				$sql = "DELETE FROM ocha_sissh.cache_mapa_desplazamiento WHERE id_fuedes = $id";
			break;

			case 'desplazamiento_id':
				$sql = "DELETE FROM cache_mapa_desplazamiento WHERE id_cache_mapa_desplazamiento = $id";
			break;

			case 'org_id':
				$sql = "DELETE FROM cache_mapa_org WHERE id_cache_mapa_org = $id";
			break;

			case 'proy_id':
				$sql = "DELETE FROM cache_mapa_proy WHERE id_cache_mapa_proy = $id";
			break;

		}

		$this->conn->Execute($sql);

	}

	/**
	* Crea y consulta cache de mapa de desplazamiento
	* @access public
	* @param int $id_fuente ID de la fuente
	* @param int $id_clase ID de la clase
	* @param string $id_tipo ID de los tipos separados por coma, aplica solo para accion social
	* @param string $id_periodo IDs de los periodos separados por coma
	* @param int $variacion Calculo de Variacion, 1-0
	* @param int $tasa Calculo de Tasa x 100000 habitantes, 1-0
	* @param string $ubicacion ID del departamento o 0 para Nacional
	* @param string $extent Extension usada para el mapa
	* @return int $id ID de cache
	*/
	function opCacheMapaDesplazamiento($caso,$id_fuente,$id_clase,$id_tipo,$id_periodo,$variacion,$tasa,$ubicacion,$extent){

		$tabla = 'cache_mapa_desplazamiento';
		$col_id = 'id_cache_mapa_desplazamiento';

		switch ($caso){
			case 'insertar':
				$sql = "INSERT INTO $tabla (id_fuedes,id_clase_despla,id_tipo,periodo,variacion,tasa,ubicacion,extent,fecha) VALUES
													  ($id_fuente,$id_clase,'$id_tipo','$id_periodo',$variacion,$tasa,'$ubicacion','$extent',now())";
				$this->conn->Execute($sql);
			break;

			case 'get':

				$id_cache = 0;

				$sql = "SELECT $col_id FROM $tabla WHERE
						id_fuedes = $id_fuente AND
						id_clase_despla = $id_clase AND
						id_tipo = '$id_tipo' AND
						periodo = '$id_periodo' AND
						variacion = $variacion AND
						tasa = $tasa AND
						ubicacion = '$ubicacion' AND
						extent = '$extent'";

				$rs = $this->conn->OpenRecordset($sql);

				if ($this->conn->RowCount($rs) > 0){

					$row = $this->conn->FetchRow($rs);
					$id_cache = $row[0];

					include($_SERVER['DOCUMENT_ROOT'].'/sissh/admin/config.php');

					// Directorio cache mapas tematicos
					$img_path_cache_tematico = $conf['mapserver']['dir_cache_tematico'];
					$img_cache = "mapa_desplazamiento_$id_cache.png";

					// Check si existe la imagen
					if (!file_exists($img_path_cache_tematico.'/'.$img_cache)){
						$this->borrarCacheMapa('desplazamiento_id',$id_cache);
						$id_cache = 0;
					}
				}

				return $id_cache;
			break;
		}
	}

	/**
	* Crea y consulta cache de mapa de desplazamiento
	* @access public
	* @return int $max_id
	*/

	function maxCacheMapaDesplazamiento(){
		$sql = "SELECT MAX(id_cache_mapa_desplazamiento) FROM cache_mapa_desplazamiento";
		$rs = $this->conn->OpenRecordset($sql);
		$row = $this->conn->FetchRow($rs);

		$max_id = $row[0];

		return $max_id;
	}

	/**
	* Crea y consulta cache de mapa de datos sectoriales
	* @access public
	* @param string $caso Insertar o consultar
	* @param int $id_dato ID del dato
	* @param string $id_periodo IDs de los periodos separados por coma
	* @param int $variacion Calculo de Variacion, 1-0
	* @param int $tasa Calculo de Tasa x 100000 habitantes, 1-0
	* @param string $ubicacion ID del departamento o 0 para Nacional
	* @param string $extent Extension usada para el mapa
	* @return int $id ID de cache
	*/
	function opCacheMapaDatoSector($caso,$id_dato,$id_periodo,$variacion,$tasa,$ubicacion,$extent){

		$tabla = 'cache_mapa_dato_sector';
		$col_id = 'id_cache_mapa_dato_sector';

		switch ($caso){
			case 'insertar':
				$sql = "INSERT INTO $tabla (id_dato,periodo,variacion,tasa,ubicacion,extent,fecha) VALUES
													  ($id_dato,'$id_periodo',$variacion,$tasa,'$ubicacion','$extent',now())";
				$this->conn->Execute($sql);
			break;

			case 'get':

				$id_cache = 0;

				$sql = "SELECT $col_id FROM $tabla WHERE
						id_dato = $id_dato AND
						periodo = '$id_periodo' AND
						variacion = $variacion AND
						tasa = $tasa AND
						ubicacion = '$ubicacion' AND
						extent = '$extent'";

				$rs = $this->conn->OpenRecordset($sql);

				if ($this->conn->RowCount($rs) > 0){

					$row = $this->conn->FetchRow($rs);
					$id_cache = $row[0];

					include($_SERVER['DOCUMENT_ROOT'].'/sissh/admin/config.php');

					// Directorio cache mapas tematicos
					$img_path_cache_tematico = $conf['mapserver']['dir_cache_tematico'];
					$img_cache = "mapa_dato_sector_$id_cache.png";

					// Check si existe la imagen
					if (!file_exists($img_path_cache_tematico.'/'.$img_cache)){
						$this->borrarCacheMapa('dato_sector_id',$id_cache);
						$id_cache = 0;
					}
				}

				return $id_cache;
			break;
		}
	}

	/**
	* Crea y consulta cache de mapa de dato sectorial
	* @access public
	* @return int $max_id
	*/
	function maxCacheMapaDatoSector(){
		$sql = "SELECT MAX(id_cache_mapa_dato_sector) FROM cache_mapa_dato_sector";
		$rs = $this->conn->OpenRecordset($sql);
		$row = $this->conn->FetchRow($rs);

		$max_id = $row[0];

		return $max_id;
	}

	/**
	* Crea y consulta cache de mapa de orgs
	* @access public
	* @param string $caso Insertar o consultar
	* @param string $caso_org Caso espec�fico al generar el mapa: tipo, sector, enfoque, poblacion, una_org
	* @param int $id ID de la org o ID del tipo o ID del sector o ID del enfoque o ID de la poblacion
	* @param string $ubicacion ID del departamento o 0 para Nacional
	* @param string $extent Extension usada para el mapa
	* @return int $id ID de cache
	*/
	function opCacheMapaOrg($caso,$caso_org,$id,$ubicacion,$extent){

		$tabla = 'cache_mapa_org';
		$col_id = 'id_cache_mapa_org';

		$col = array('una_org' => 'id_org',
				'tipo' => 'id_tipo',
				'sector' => 'id_comp',
				'enfoque' => 'id_enf',
				'poblacion' => 'id_pobla',
				);

		switch ($caso){
			case 'insertar':
				if ($caso_org != 'municipio') $sql = "INSERT INTO $tabla ($col[$caso_org],ubicacion,extent,fecha) VALUES ($id,'$ubicacion','$extent',now())";
				else						  $sql = "INSERT INTO $tabla (ubicacion,extent,fecha) VALUES ('$ubicacion','$extent',now())";

				$this->conn->Execute($sql);

			break;

			case 'get':

				$id_cache = 0;

				$sql = "SELECT $col_id FROM $tabla WHERE ";
				if ($caso_org != 'municipio')	$sql .= "$col[$caso_org] = $id AND ";
				$sql .= "ubicacion = '$ubicacion' AND extent = '$extent'";

				$rs = $this->conn->OpenRecordset($sql);

				if ($this->conn->RowCount($rs) > 0){

					$row = $this->conn->FetchRow($rs);
					$id_cache = $row[0];

					include($_SERVER['DOCUMENT_ROOT'].'/sissh/admin/config.php');

					// Directorio cache mapas tematicos
					$img_path_cache_tematico = $conf['mapserver']['dir_cache_tematico'];
					$img_cache = "mapa_org_$id_cache.png";

					// Check si existe la imagen
					if (!file_exists($img_path_cache_tematico.'/'.$img_cache)){
						$this->borrarCacheMapa('org_id',$id_cache);
						$id_cache = 0;
					}
				}

				return $id_cache;
			break;
		}
	}

	/**
	* Crea y consulta cache de mapa de org
	* @access public
	* @return int $max_id
	*/
	function maxCacheMapaOrg(){
		$sql = "SELECT MAX(id_cache_mapa_org) FROM cache_mapa_org";
		$rs = $this->conn->OpenRecordset($sql);
		$row = $this->conn->FetchRow($rs);

		$max_id = $row[0];

		return $max_id;
	}

	/**
	* Crea y consulta cache de mapa de proyectos
	* @access public
	* @param string $caso Insertar o consultar
	* @param string $filtro Insertar o consultar
	* @param int $id ID del proyecto, tema o agencia
	* @param string $ubicacion ID del departamento o 0 para Nacional
	* @param string $extent Extension usada para el mapa
	* @return int $id ID de cache
	*/
	function opCacheMapaProy($caso,$filtro,$id,$ubicacion,$extent){

		$tabla = 'cache_mapa_proy';
		$col_id = 'id_cache_mapa_proy';

		$col = array('tema' => 'id_tema',
					'agencia' => 'id_org',
					'un_proy' => 'id_proy'
					);

		switch ($caso){
			case 'insertar':

				if ($filtro != 'cobertura') $sql = "INSERT INTO $tabla ($col[$filtro],ubicacion,extent,fecha) VALUES ($id,'$ubicacion','$extent',now())";
				else						$sql = "INSERT INTO $tabla (ubicacion,extent,fecha) VALUES ('$ubicacion','$extent',now())";

				$this->conn->Execute($sql);

			break;

			case 'get':

				$id_cache = 0;

				$sql = "SELECT $col_id FROM $tabla WHERE ";
				if ($filtro != 'cobertura')	$sql .= "$col[$filtro] = $id AND ";
                else                        $sql .= 'id_proy is null AND id_org IS NULL AND id_tema IS NULL AND ';
				$sql .= "ubicacion = '$ubicacion' AND extent = '$extent'";

				$rs = $this->conn->OpenRecordset($sql);

				if ($this->conn->RowCount($rs) > 0){

					$row = $this->conn->FetchRow($rs);
					$id_cache = $row[0];

					include($_SERVER['DOCUMENT_ROOT'].'/sissh/admin/config.php');

					// Directorio cache mapas tematicos
					$img_path_cache_tematico = $conf['mapserver']['dir_cache_tematico'];
					$img_cache = "mapa_proy_$id_cache.png";

					// Check si existe la imagen
					if (!file_exists($img_path_cache_tematico.'/'.$img_cache)){
						$this->borrarCacheMapa('proy_id',$id_cache);
						$id_cache = 0;
					}
				}

				return $id_cache;
			break;
		}
	}

	/**
	* Crea y consulta cache de mapa de dato sectorial
	* @access public
	* @return int $max_id
	*/
	function maxCacheMapaProy(){
		$sql = "SELECT MAX(id_cache_mapa_proy) FROM cache_mapa_proy";
		$rs = $this->conn->OpenRecordset($sql);
		$row = $this->conn->FetchRow($rs);

		$max_id = $row[0];

		return $max_id;
	}
}

?>
