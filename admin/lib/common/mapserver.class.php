<?
/**
 * Clase Mapserver
 *
 * Contiene los métodos de la clase Mapserver
 * @author Ruben A. Rojas C.
 */
Class Mapserver {

	/**
	 *
	 * @var string
	 */
	var $conn;

	/**
	 *
	 * @var object
	 */
	var $pgConn;
	
	/**
	* @var string
	* Path del mapa
	*/
	var $map_path;	

	/**
	* @var string
	* Path a la imagen temporal que se crea
	*/
	var $img_path;	

	/**
	* @var string
	* Path a las fuentes
	*/
	var $font_path;	

	/**
	* @var string
	* Máxima extensión del mapa
	*/
	var $max_extent;	
	
	/**
	 * Constructor
	 **/
	function Mapserver(){
		$this->conn = "host=localhost port=5432 dbname=sissh user=sissh password=mjuiokm";
		$this->pgConn = New PgDBConn();

		// path defaults
		$this->map_path = $_SERVER['DOCUMENT_ROOT']."/sissh/consulta/";
		$this->img_path = $_SERVER['DOCUMENT_ROOT']."/tmp";
		$this->font_path = $_SERVER['DOCUMENT_ROOT']."/sissh/consulta/fonts/fontset.txt";
		$this->max_extent = $this->max_extent;
	}

	/**
	 * Convierte de la imagen a coordenadas del mapa
	 * @access public
	 * @param int $width
	 * @param int $height
	 * @param PointObj $point Punto del click
	 * @param RectObj $ext Extension anterior
	 * @return array $pt Coordenadas en el mapa
	 */
	function img2map($width,$height,$point,$ext) {
		$minx = $ext->minx;
		$miny = $ext->miny;
		$maxx = $ext->maxx;
		$maxy = $ext->maxy;
		if ($point->x && $point->y){
			$x = $point->x;
			$y = $point->y;
			$dpp_x = ($maxx-$minx)/$width;
			$dpp_y = ($maxy-$miny)/$height;
			$x = $minx + $dpp_x*$x;
			$y = $maxy - $dpp_y*$y;
		}
		$pt[0] = $x;
		$pt[1] = $y;

		return $pt;
	}

	/**
	 * Calcula los rompimientos naturales con la optimizacion de Jenks
	 * @access public
	 * @param int $numclass Numero de Intervalos
	 * @param array $data
	 * @return array $kclass Indices dentro de $data que son los limites de cada intervalo
	 */
	function jenks($numclass,$data){
		$numdata = count($data);
			
		for($i=1;$i<=$numclass;$i++){
			$mat1[1][$i] = 1;
			$mat2[1][$i] = 0;
				
			for($j=2;$j<=$numdata;$j++){
				$mat2[$j][$i] = 1.8E100;  //Maximo float
			}
		}

		$v = 0;
		for($l=2;$l<=$numdata;$l++){
			$s1 = 0;
			$s2 = 0;
			$w = 0;
				
			for($m=1;$m<=$l;$m++){
				$i3 = $l - $m + 1;
				$val = $data[$i3-1];
				 
				$s2 += $val * $val;
				$s1 += $val;
				 
				$w++;
				$v = $s2 - ($s1 * $s1) / $w;
				$i4 = $i3 - 1;
				if ($i4 != 0) {
					for ($j = 2; $j <= $numclass; $j++) {
						if ($mat2[$l][$j] >= ($v + $mat2[$i4][$j-1])) {
							$mat1[$l][$j] = $i3;
							$mat2[$l][$j] = $v + $mat2[$i4][$j -1];

						}
					}
				}
			}
			$mat1[$l][1] = 1;
			$mat2[$l][1] = $v;
		}
	  
		$k = $numdata;
	  
		$kclass[$numclass - 1] = $numdata - 1;
	  
	  
		for ($j = $numclass; $j >= 2; $j--) {
			//echo "rank = " . $mat1[$k][$j];
			$id =  $mat1[$k][$j] - 2;
			//echo("val = " . $data[$id]);
			//System.out.println(mat2[k][j]);
			 
			 
			$kclass[$j - 2] = $id;
			 
			 
			$k = $mat1[$k][$j] - 1;
		}
	  
		return $kclass;

	}

	function jenks2($numclass,$data){
		$numdata = count($data);
		$mean = array_sum($data) / $numdata;

		$sdam = 0;
		for($i=0;$i<$numdata;$i++){
			$sdam += pow(($data[$i] - $mean),2);
		}

		//Comenzamos con el primer rompimiento, con lÃ­mite en el segundo elemento
		//Iteramos hasta que gfv no pueda crecer mÃ¡s

		$sdmc = 0;
		$gvf = 0;
		$gvf_ant = 0;
		$n_b = 1;
		$n_b_i = 0;
		while($exito == 0){
				
			$data_j = array_slice($data,$n_b_i,$n_b);
			$numdata_j = count($data_j);
			$mean_j = array_sum($data_j) / $numdata_j;
			for ($j=0;$j<$n_b;$j++){
				$sdmc += pow(($data_j[$j] - $mean_j),2);
			}
				
			$gvf = 1 - ($sdcm - $sdam);
				
			if ($gvf >= $gvf_ant){
				$gvf_ant = $gvf;
				$n_b++;
				echo "GVF = $gvf, Nb = $n_b <br>";
			}
			else{
				$exito = 1;
				echo "limite 1 = ".$n_b;
			}
		}
		die;
	}

	/**
	 * Coloca el logo de OCHA, el norte y demas extras
	 * @access public
	 * @param resource $img_mapa Image resource
	 * @return resource $img_mapa
	 */
	function drawExtras($img_mapa){
		
		$width_img = imagesx($img_mapa);
		
		//LOGO
		$img_logo = imagecreatefrompng($_SERVER["DOCUMENT_ROOT"].'/sissh/images/logos/ocha_mapserver.png');
		$width_logo = imagesx($img_logo);
		
		$left = $width_img - $width_logo - 40;
		$top = 5;

		//UNDAF
		if (isset($_SESSION["undaf"]) && $_SESSION["undaf"] == 1){
			$img_logo = imagecreatefrompng($_SERVER["DOCUMENT_ROOT"].'/sissh/images/undaf/mapa/logo_unidos.png');
			$width_logo = imagesx($img_logo);
			$left = 5;
			$top = 30;

			//Coloca el logo del tema UNDAF cuando la consulta es por tema
			if (isset($_SESSION["id_tema_undaf"])){
				$img_tema = imagecreatefrompng($_SERVER["DOCUMENT_ROOT"].'/sissh/images/undaf/mapa/'.$_SESSION["id_tema_undaf"].'.png');
				$width_tema = imagesx($img_tema);
				$height_tema = imagesy($img_tema);

				$left_tema = $width_img - $width_tema - 50;
				imagecopy($img_mapa,$img_tema,$left_tema,470,0,0,$width_tema,$height_tema);
			}

		}

		
		//MAPP-OEA
		if (isset($_SESSION["mapp_oea"]) && $_SESSION["mapp_oea"] == 1){
			//unimag
			$img_logo = imagecreatefrompng($_SERVER["DOCUMENT_ROOT"].'/sissh/images/mapp_oea/mapa/logo_unimag.png');
			$width_logo = imagesx($img_logo);
			$left = 5;
			$top = 30;

			$height_logo = imagesy($img_logo);
			imagecopy($img_mapa,$img_logo,$left,$top-2,0,0,$width_logo,$height_logo);
			
			//oea
			$img_logo = imagecreatefrompng($_SERVER["DOCUMENT_ROOT"].'/sissh/images/mapp_oea/mapa/logo_oea.png');
			$width_logo = imagesx($img_logo);
			$left += $width_logo + 20;
			
		}

		$height_logo = imagesy($img_logo);
		imagecopy($img_mapa,$img_logo,$left,$top,0,0,$width_logo,$height_logo);

		//NORTE
		$img_norte = imagecreatefromgif($_SERVER["DOCUMENT_ROOT"].'/sissh/images/mscross/norte.gif');

		//imagealphablending($img_norte, true);

		imagecopy($img_mapa,$img_norte,150,2,0,0,18,18);

		return $img_mapa;

	}

	/**
	 * Coloca el logo de Unicef, el norte y demas extras
	 * @access public
	 * @param resource $img_mapa Image resource
	 * @return resource $img_mapa
	 */
	function drawExtrasUnicef($img_mapa){
		
		$width_img = imagesx($img_mapa);
		
		//LOGO
		$img_logo = imagecreatefrompng($_SERVER["DOCUMENT_ROOT"].'/sissh/images/unicef/unicef_logo.png');
		$width_logo = imagesx($img_logo);
		
		$left = 2;
		$top = 31;

		$height_logo = imagesy($img_logo);
		imagecopy($img_mapa,$img_logo,$left,$top,0,0,$width_logo,$height_logo);

		//NORTE
		$img_norte = imagecreatefromgif($_SERVER["DOCUMENT_ROOT"].'/sissh/images/mscross/norte.gif');

		//imagealphablending($img_norte, true);

		imagecopy($img_mapa,$img_norte,150,2,0,0,18,18);

		return $img_mapa;

	}

	/**
	 * Retorna la extensión array(xmin,min,xmax,ymax) de un depto en la capa depto de la tabla
	 * @access public
	 * @param string $id_depto
	 * @return array $extent
	 */
	function getExtentEnvelopeDepto($id_depto){
		
		//INICIALIZACION DE VARIABLES
		$offset_envelope = 40000;    //Offset para que no quede el rectangulo justo en los limites del depto
		
		$sql = "SELECT Xmin(extent(the_geom)), YMin(extent(the_geom)), Xmax(extent(the_geom)), YMax(extent(the_geom)) FROM depto WHERE codane2 = '$id_depto'";
		$rs = $this->pgConn->OpenRecordset($sql);
		$row = $this->pgConn->FetchRow($rs);

		$extent = array($row[0],$row[1],$row[2],$row[3]);

		//Maxima extension (xmin,xmax,ymin,ymax)
		$extent_max = $this->max_extent;

		//Offset para que no quede el rectangulo justo en los limites del depto
		//Solo resta para los deptos que no estan al borde vertical, ej. La guajira y Amazonas, en x no importa, porque el mapa es mas alto que ancho
		if ($extent_max[2] < ($extent[1] - $offset_envelope) && $extent_max[3] > ($extent[3] + $offset_envelope)){
			$extent[0] -= $offset_envelope;   //xmin		
			$extent[1] -= $offset_envelope;   //ymin		
			$extent[2] += $offset_envelope;   //xmax		
			$extent[3] += $offset_envelope;   //ymax
		}		
		
		return array($extent[0],$extent[2],$extent[1],$extent[3]);    //Extent para mscross debe ser de la forma (xmin,xmax,ymin,ymax)
	}

	/**
	 * Retorna la extensión array(xmin,min,xmax,ymax) de una region, poblado, etc
	 * @access public
	 * @param string $id
	 * @param string $case
	 * @return array $extent
	 */
	function getExtentEnvelopeLoc($id,$case){
		
		//INICIALIZACION DE VARIABLES
		$offset_envelope = 40000;    //Offset para que no quede el rectangulo justo en los limites del depto
		
		switch ($case){
			case 'region':
				$tabla = "region";
				$col_id = "id_mysql";
			break;
		}
		
		$sql = "SELECT Xmin(extent(the_geom)), YMin(extent(the_geom)), Xmax(extent(the_geom)), YMax(extent(the_geom)) FROM $tabla WHERE $col_id = $id_depto";
		$rs = $this->pgConn->OpenRecordset($sql);
		$row = $this->pgConn->FetchRow($rs);

		$extent = array($row[0],$row[1],$row[2],$row[3]);

		//Maxima extension (xmin,xmax,ymin,ymax)
		$extent_max = $this->max_extent;

		//Offset para que no quede el rectangulo justo en los limites
		if ($extent_max[2] < ($extent[1] - $offset_envelope) && $extent_max[3] > ($extent[3] + $offset_envelope)){
			$extent[0] -= $offset_envelope;   //xmin		
			$extent[1] -= $offset_envelope;   //ymin		
			$extent[2] += $offset_envelope;   //xmax		
			$extent[3] += $offset_envelope;   //ymax
		}		
		
		return array($extent[0],$extent[2],$extent[1],$extent[3]);    //Extent para mscross debe ser de la forma (xmin,xmax,ymin,ymax)
	}
	
	
	/**
	 * Retorna la extensión array(xmin,min,xmax,ymax) de un mpio en la capa mpio de la tabla
	 * @access public
	 * @param string $id_mpio Divipola de 1 o varios municipios (regiones), si son varios, separados por ,
	 * @param string $extent_depto, extension del mapa inicial, bien sea colombia o un depto, xmin,xmax,ymin,ymax
	 * @return array $extent
	 */
	function getExtentEnvelopeMpio($id_mpio,$extent_orig){
		
		//INICIALIZACION DE VARIABLES
		$offset_envelope = 8000;    //Offset para que no quede el rectangulo justo en los limites del depto
		
		$id_tmp = explode(",",$id_mpio);
		foreach ($id_tmp as $i=>$id){
			if ($i == 0)	$id_mpio = "'$id'";
			else			$id_mpio .= ",'$id'";
		}
		
		$sql = "SELECT Xmin(extent(the_geom)), YMin(extent(the_geom)), Xmax(extent(the_geom)), YMax(extent(the_geom)) FROM mpio WHERE codane2 IN ($id_mpio)";
		$rs = $this->pgConn->OpenRecordset($sql);
		$row = $this->pgConn->FetchRow($rs);

		$extent = array($row[0],$row[1],$row[2],$row[3]);

		//Maxima extension (xmin,xmax,ymin,ymax)
		$extent_max = explode(",",$extent_orig);

		//Offset para que no quede el rectangulo justo en los limites del depto
		//Solo resta para los deptos que no estan al borde vertical, ej. La guajira y Amazonas, en x no importa, porque el mapa es mas alto que ancho
		if ($extent_max[2] < ($extent[1] - $offset_envelope) && $extent_max[3] > ($extent[3] + $offset_envelope)){
			$extent[0] -= $offset_envelope;   //xmin		
			$extent[1] -= $offset_envelope;   //ymin		
			$extent[2] += $offset_envelope;   //xmax		
			$extent[3] += $offset_envelope;   //ymax
		}		
		
		return array($extent[0],$extent[2],$extent[1],$extent[3]);    //Extent para mscross debe ser de la forma (xmin,xmax,ymin,ymax)
	}
	
    /**
	 * Dado un punto LON,LAT, se consulta el municipio
	 * @access public
	 * @param string $c, Point o Web Points
	 * @param array $points
	 * @return int $divipola
	 */
	function getMpioFromPoint($c, $points){
		
        // Informacion: 
        // La capa cargada a PostGIS de OCHA tiene proyeccion: EPSG:32618 - WGS 84 / UTM zone 18N
        // La capa OSM/Google tiene proyeccion 900913 = EPSG:97094 cuando es metros, 4326 cuando es grados LON/LAT

        $j = array();
        $success = 0;
        foreach($points as $p) {
            if (!empty($p)) {

                list($lon,$lat) =  explode(',', $p);
                
                $sql = "SELECT codane2, departamen || ' >> ' || municipio FROM mpio
                        WHERE ST_Within(ST_Transform(ST_PointFromText('POINT($lon $lat)', 4326), 32618), the_geom) IS NOT FALSE";

                $rs = $this->pgConn->OpenRecordset($sql);
                $row = $this->pgConn->FetchRow($rs);

                if (!empty($row)) {
                    $success = 1;
                    $j[] = array('divipola' => $row[0], 'label' => utf8_encode($row[1]), 'lon' => $lon, 'lat' => $lat);
                }
            } 
        }

        echo json_encode(compact('success', 'j'));
	}

	/*
	 * Retorna el valor inferior y superios de un intervalo dado los limites de natural brakes y el arreglo de valores
	 * @param array $valores
	 * @param array $kclass Arreglo calculado en jenks
	 * @param array $i iteración
	 * @param int $variacion Si se esta calculando variación
	 * @return array $limites Arreglo con los valores sup,inf
	 */
	function getInfSup($valores_mpio,$kclass,$i,$variacion){
		$inf = 0;
		if ($i == 0){
			$sup = ($variacion == 0) ? $valores_mpio[$kclass[0]] : $kclass[0];
			//$inf = ($variacion == 0) ? $valores_mpio[0] : -100;
			if ($variacion == 0){	
				$inf = (min($valores_mpio) < 1) ? min($valores_mpio) : 1;
			}
			else{
				 $inf = -100;
			}
			 
		}
		else{
			$sup = ($variacion == 0) ? $valores_mpio[$kclass[$i]] : $kclass[$i];
			
			if ($variacion == 0 && $valores_mpio[$kclass[$i -1]] > 1){
				$inf = $valores_mpio[$kclass[$i -1]] + 1;
			}
			else if ($variacion == 0 && $valores_mpio[$kclass[$i -1]] < 1){
				$inf = $valores_mpio[$kclass[$i -1]] + 0.01;
			}
			else if ($variacion == 1){
				$inf = $kclass[$i -1] + 0.01;	
			}
			 
		}
	
		return array('sup' => $sup, 'inf' => $inf);
		
	}
	
/*
	 * Retorna el indice del intervalo al que pertenece un valor, es para el caso de variacion 
	 * con un solo intervalo para mostrar el color correcto en las convenciones
	 * @param float $valor Valor
	 * @param array $kclass Arreglo calculado en jenks
	 * @param array $i iteración
	 * @return array $limites Arreglo con los valores sup,inf
	 */
	function getUnIntervaloVariacion($valor,$kclass,$i){
		
		$l = 0;
		foreach ($kclass as $lim){
			
			$limites = $this->getInfSup($valores_mpio,$kclass,$l,1); 
			$sup = $limites['sup'];
			$inf = $limites['inf'];
			
			if ($valor > $inf && $valor <= $sup){
				$i = $l;
			}
			
			$l++;
		}
		
		return $i;
	}
}

?>
