<?
session_start();
set_time_limit(0);


//CHECK PARAMETERS
if (count($_GET) == 0)	die;
else if (isset($_GET["case"]) && !in_array($_GET["case"],array("desplazamiento","dato_sectorial","org","evento_c","perfil","proyecto_undaf")))	die;

header("Content-type: image/png");

//LIBRERIAS
include_once("../admin/lib/common/mysqldb.class.php");	
include_once("../admin/lib/common/postgresdb.class.php");	
include("../admin/lib/common/mapserver.class.php");
include("../admin/lib/common/cadena.class.php");
include("../admin/lib/dao/factory.class.php");
include("../admin/config.php");

//FUNCIONES INTERNAS
function borrarCeros($array){ 
	$salir = 0;
	while ($salir == 0){
		if ($valores_mpio[0] == 0)	array_shift($valores_mpio);
		else						$salir = 1;
	}
}


//INICIALIZACION DE VARIABLES
$case = $_GET["case"];
$mapserver = New Mapserver();

// Ahora usando Factory pattern

$depto_dao = FactoryDAO::factory('depto');
$mpio_dao = FactoryDAO::factory('municipio');
$sissh = FactoryDAO::factory('sissh');

switch ($case){
	case 'desplazamiento':
		$desplazamiento_dao = FactoryDAO::factory('desplazamiento');
		$fuente_dao = FactoryDAO::factory('fuente');
		$tipo_dao = FactoryDAO::factory('tipo_desplazamiento');
		$clase_dao = FactoryDAO::factory('clase_desplazamiento');
		$periodo_dao = FactoryDAO::factory('periodo');
		$d_s_dao = FactoryDAO::factory('dato_sectorial');
	break;

	case 'dato_sectorial':
		$d_s_dao = FactoryDAO::factory('dato_sectorial');
		$unidad_dao = FactoryDAO::factory('u_d_s');
		$contacto_dao = FactoryDAO::factory('contacto');
	break;
	
	case 'org':
		$org_dao = FactoryDAO::factory('org');
		$sector_dao = FactoryDAO::factory('sector');
		$tipo_org_dao = FactoryDAO::factory('tipo_org');
		$pob_dao = FactoryDAO::factory('poblacion');
		$enfoque_dao = FactoryDAO::factory('enfoque');
	break;
	
	case 'evento_c':
		$evento_c_dao = FactoryDAO::factory('evento_c');
		$cat_evento_c_dao = FactoryDAO::factory('cat_evento_c');
		$scat_evento_c_dao = FactoryDAO::factory('subcat_evento_c');
		$pob_dao = FactoryDAO::factory('poblacion');
	break;
	
	case 'proyecto_undaf':
		$proy_dao = FactoryDAO::factory('proyecto');
		$tema_dao = FactoryDAO::factory('tema');
		$org_dao = FactoryDAO::factory('org');
	break;

}

$drawConv = 0;
$drawLegend = 1;
$arraySteps = array();
$una_org = 0;
$extent_orig = 0;  //Todo el mapa
$draw_cero = 0;   //Colcar convencion de cero - color blanco
$map_ref = (isset($_GET["map_ref"])) ? 1 : 0;
$variacion = (isset($_GET["variacion"])) ? $_GET["variacion"] : 0;
$tasa = (isset($_GET["tasa"])) ? $_GET["tasa"] : 0;
$print = (isset($_GET["print"])) ? $_GET["print"] : 0;

// path defaults
$map_path = $mapserver->map_path;
$img_path = $mapserver->img_path;
$font_path = $mapserver->font_path;

// Directorio cache mapas tematicos
$img_path_cache_tematico = $conf['mapserver']['dir_cache_tematico'];


//Variable de extent que viene desde mscross
if (isset($_GET['mapext'])) {
	
	$extent_get = $_GET['mapext'];
	if ($extent_get == "-161112,-462196,1653895,1386463")	$extent_orig = 1;
	
	$extent = split(" ", $extent_get);
	$mapsize = split(" ", $_GET['mapsize']);
	$width_img = $mapsize[0];
	$height_img = $mapsize[1];
}

//Filtro depto
$id_depto_filtro = $_GET["id_depto_filtro"];
$cond = "";
if ($id_depto_filtro > 0){
	$cond = "id_depto = '$id_depto_filtro'";	
	//$filter_gis = "substr(codane2,0,3) = '$id_depto_filtro'";
	$depto_filtro_vo = $depto_dao->Get($id_depto_filtro);
	
	//$image_name = ($op_depto == 1) ? $img_path.'/'.'depto_'.$id_depto_filtro.'.png' : $img_path.'/mpio_'.$id_depto_filtro.'.png';
}

//LOG, si no es mapa de referencia y si no viene de web site
if ($map_ref == 0 && isset($_SESSION["id_usuario_s"])){
	$log = FactoryDAO::factory('log');
	
	$_SESSION['m_e'] = "mapa";
	
	$caso = $case;
	if ($print == 1)	$caso .= "_print";
	
	$log->RegistrarFrontendMapa($id_depto_filtro,$caso);
}


	
//NOMBRES MPIO
$vos = $mpio_dao->GetAllArray($cond);
foreach ($vos as $vo){
	$mpios[] = $vo->id;
	$mpios_nombre[$vo->id] = $vo->nombre;
}

//$mpios = $mpio_dao->GetAllArrayID($cond,"");

$num_mpios = count($mpios);
if ($num_mpios > 1)	$drawConv = 1;

if ($print == 0){
	$tag_font_label = "label";
	$size_font_label = 6;  //Tamaño en pixeles
}
else{
	$tag_font_label = "Arial";
	$size_font_label = 14;  //Tamaño en pixeles
}

// Default click point
$clickx = $width_img / 2;
$clicky = $height_img / 2;

$clkpoint = ms_newPointObj();
$old_extent = ms_newRectObj();

$max_extent = ms_newRectObj();
$max_extent->setextent(-161112, -462196, 1653895, 1386463);

$mapObj = ms_newMapObj("");
$mapObj->set("name","SIDI UMAIC Colombia");
$mapObj->imagecolor->setRGB(255,255,255);
$mapObj->setFontSet($font_path);
$mapObj->setSize($width_img,$height_img);
$mapObj->set("shapepath",$_SERVER['DOCUMENT_ROOT']."/sissh/images/shapes/");
$mapObj->set("resolution",300);

$outputformat = ($print == 0) ? "png" : "png24";

$mapObj->selectOutputFormat($outputformat);

//PNG8
$mapObj->outputformat->setOption("name","png");
$mapObj->outputformat->setOption("DRIVER","GD/PNG");
$mapObj->outputformat->setOption("EXTENSION","png");
$mapObj->outputformat->setOption("IMAGEMODE",MS_IMAGEMODE_PC256);
$mapObj->outputformat->setOption("MIMETYPE","image/png");

//PNG24
$mapObj->outputformat->setOption("name","png24");
$mapObj->outputformat->setOption("DRIVER","GD/PNG");
$mapObj->outputformat->setOption("EXTENSION","png");
$mapObj->outputformat->setOption("IMAGEMODE",MS_IMAGEMODE_RGB);
$mapObj->outputformat->setOption("MIMETYPE","image/png");

// Set the map to the extent retrieved from the form
$mapObj->setExtent($extent[0],$extent[1],$extent[2],$extent[3]);

// Save this extent as a rectObj, we need it to zoom.

$old_extent->setextent($extent[0],$extent[1],$extent[2],$extent[3]);
$clkpoint->setXY($clickx,$clicky);

$mapObj->web->set("imagepath",$img_path);
$mapObj->web->set("imageurl","/tmp/");

	
//MPIOS
$layerObjMpio = ms_newLayerObj($mapObj);
$layerObjMpio->set("name","mpios");
$layerObjMpio->set("status",MS_ON);
$layerObjMpio->set("type",MS_LAYER_POLYGON);
//$layerObjMpio->set("connectiontype",MS_POSTGIS);
//$layerObjMpio->set("connection",$mapserver->conn);
//$layerObjMpio->set("data","the_geom FROM mpio");
$layerObjMpio->set("data","colmun3corregido.shp");
//$layerObjMpio->set("data","colmunwgs84.dbf");
$layerObjMpio->set("classitem","CODANE2");
$layerObjMpio->set("transparency",MS_GD_ALPHA);

	//DEPTOS
$layerObj = ms_newLayerObj($mapObj);
$layerObj->set("name","deptos");
$layerObj->set("status",MS_ON);
//$layerObj->set("type",MS_LAYER_LINE);
$layerObj->set("type",MS_LAYER_POLYGON);

//$layerObj->set("connectiontype",MS_POSTGIS);
//$layerObj->set("connection",$mapserver->conn);
//$layerObj->set("data","the_geom FROM depto");
$layerObj->set("data","COLDPTO3.shp");
//$layerObj->set("data","COLDEPTOWGS84.shp");
$layerObj->set("classitem","CODANE2");
//Si es un depto, le coloca nombres a los vecinos
if ($id_depto_filtro > 0){
	$layerObj->set("labelitem","departamen");
}

if ($id_depto_filtro > 0){
	//$layerObjMpio->setFilter($filter_gis);
	$layerObjMpio->set("filteritem","departamen");
	
	//Elimina tildes
	$depto_nombre = strtoupper(strtr($depto_filtro_vo->nombre,"áéíóúñ","aeioun"));
	
	//Reemplaza Valle del Cauca por Valle
	if (strtolower($depto_filtro_vo->nombre) == 'valle del cauca'){
		$depto_nombre = "VALLE";
	}
	
	$layerObjMpio->setFilter($depto_nombre);
}

//Coloca labels si es el map principal no el de referencia
if ($map_ref == 0){
	$layerObjMpio->set("labelitem","municipio");
}
//$layerObjMpio->set("filteritem","departamen");
//$layerObjMpio->setFilter("/ANTIOQUIA/");

//Color para la outline de vecinos
$r_vecino = 50;
$g_vecino = 50;
$b_vecino = 50;

// Vecinos
$layerObj_vecino = ms_newLayerObj($mapObj);
$layerObj_vecino->set("name","vecinos");
$layerObj_vecino->set("status",MS_ON);
$layerObj_vecino->set("type",MS_LAYER_POLYGON);
$layerObj_vecino->set("data","vecinos.shp");
$layerObj_vecino->set("labelitem","name1_");

//Estilo Vecinos
$classObj = ms_newClassObj($layerObj_vecino);
$styleObj =  ms_newStyleObj($classObj);
$styleObj->color->setRGB(225,225,225);
$styleObj->outlinecolor->setRGB($r_vecino,$g_vecino,$b_vecino);
$styleObj->set("antialias",MS_TRUE);

//$classObj->label->set("type",MS_TRUETYPE);
//$classObj->label->set("position",MS_UR);
//$classObj->label->color->setRGB(0,0,0);
//$classObj->label->outlinecolor->setRGB(255,255,255);
//$classObj->label->set("antialias",MS_TRUE);
//$classObj->label->set("font",$tag_font_label);
//$classObj->label->set("size",$size_font_label);  //Tamaño en pixeles

if ($map_ref == 0 && $case != 'perfil'){
	//Para la opcion de export data
	$porc = ($variacion == 1 || $tasa == 1)	? "%" : "";
	
  switch ($case){
	case 'desplazamiento':
		
		//$tq = 1000000;
		//$mt = microtime(true);	
		
		$id_fuente = $_GET["id_fuente"];
		$id_clase = $_GET["id_clase"];
		$id_tipo = explode(",",$_GET["id_tipo"]);
		$id_periodo = $_GET["id_periodo"];
		$id_tipo_periodo = $_GET["id_tipo_periodo"];

		// Si es CODHES tipo = 3
		if ($id_fuente == 1)	$id_tipo = array(3);
		
		// Cache
		$id_cache = $sissh->opCacheMapaDesplazamiento('get',$id_fuente,$id_clase,$_GET['id_tipo'],$id_periodo,$variacion,$tasa,$id_depto_filtro,$extent_get);
		if ($id_cache == 0){
			$sissh->opCacheMapaDesplazamiento('insertar',$id_fuente,$id_clase,$_GET['id_tipo'],$id_periodo,$variacion,$tasa,$id_depto_filtro,$extent_get);

			$max_id = $sissh->maxCacheMapaDesplazamiento();
			
			$img_cache = "mapa_desplazamiento_$max_id.png";
		}
		else{
            $img_cache = "$img_path_cache_tematico/mapa_desplazamiento_$id_cache.png";

            $_SESSION["mapserver_img"] = $img_cache;
            
            $image = imagecreatefrompng($img_cache);
			
			imagepng($image);
			
			exit;
		}

		$xls = "<tr><td><b>CODIGO</b></td><td><b>MUNICIPIO</b></td><td><b>VALOR $porc</b></td></tr>";
		
		foreach ($mpios as $id_mpio){
			
			$nom_mpio = $mpios_nombre[$id_mpio];
			
			if ($variacion == 1){
				$periodo = split(",",$id_periodo);
				
				//(incidencia periodo corriente-incidencia periodo anterior)/(incidencia periodo anterior)*100= % variación
				
				$p_corr = $periodo[1];
				$p_ant = $periodo[0];
				
				$val_corr = 0;
				$val_ant = 0;
				$corr_null = 1;
				$ant_null = 1;
				
				foreach($id_tipo as $id_t){
					
					$v = $desplazamiento_dao->getValorToMapa($id_mpio,2,$id_t,$id_clase,$id_fuente,$p_corr,$id_tipo_periodo);
					if (!is_null($v)){
						$val_corr += $v;
						$corr_null = 0;
					}
					
					$v = $desplazamiento_dao->getValorToMapa($id_mpio,2,$id_t,$id_clase,$id_fuente,$p_ant,$id_tipo_periodo);
					if (!is_null($v)){
						$val_ant += $v;
						$ant_null = 0;
					}
					
				}
				
				if ($corr_null == 0 || $ant_null == 0){
					//Si alguno de los 2 valores es 0, se reemplaza con 0.1 para mostrar variación
					if ($val_corr == 0)	$val_corr = 0.1;
					if ($val_ant == 0)	$val_ant = 0.1;
				
					$val = intval(($val_corr - $val_ant)/($val_ant)*100);
					
					//echo "Municipio:$id_mpio :: Valor = ".intval($val)."<br>";
					$valores_mpio_mpio[$id_mpio] =  $val;
					$valores_mpio[] =  $val;
					
					$xls .= "<tr><td>$id_mpio</td><td>$nom_mpio</td><td>$val</td></tr>";
					
					//Check de minimo steps en arreglo de valores
					if (!in_array($val,$arraySteps)){
						$arraySteps[] = $val;
					}
					
				}
			}
			else if ($tasa == 1){
					
					$val_despla = 0;
					$v_null = 1;
					foreach($id_tipo as $id_t){
						$v = $desplazamiento_dao->getValorToMapa($id_mpio,2,$id_t,$id_clase,$id_fuente,$id_periodo,$id_tipo_periodo);
						if (!is_null($v)){
							$val_despla += $v;
							$v_null = 0;
						}
					}
				
					//CONSULTA EL TOTAL DE LA POBLACION EN EL MISMO PERIODO DEL DATO PARA LA UBIACION
					$id_dato_pob = 3;
					$f_ini = "$id_periodo-1-1";
					$f_fin = "$id_periodo-12-31";
					$val = $d_s_dao->GetValorToReport($id_dato_pob,$id_mpio,$f_ini,$f_fin,2);
					$total_poblacion = $val['valor'];
					
					if ($total_poblacion > 0 && $total_poblacion != "N.D." && $v_null == 0){
						$val_tasa = intval(($val_despla/$total_poblacion)*100000);
						
						$valores_mpio[] =  $val_tasa;
						$valores_mpio_mpio[$id_mpio] =  $val_tasa;
						
						$xls .= "<tr><td>$id_mpio</td><td>$nom_mpio</td><td>$val_tasa</td></tr>";
						
						//Check de minimo steps en arreglo de valores
						if (!in_array($val_tasa,$arraySteps)){
							$arraySteps[] = $val_tasa;
						}
					}
			}
			else{
				
				$val = 0;
				$v_null = 1;
				foreach($id_tipo as $id_t){
					$v = $desplazamiento_dao->getValorToMapa($id_mpio,2,$id_t,$id_clase,$id_fuente,$id_periodo,$id_tipo_periodo);
					if (!is_null($v)){
						$val += $v;
						$v_null = 0;
					}
				}
				
				if ($v_null == 0){
					$valores_mpio_mpio[$id_mpio] =  $val;
					$xls .= "<tr><td>$id_mpio</td><td>$nom_mpio</td><td>$val</td></tr>";
			
					//Check de minimo steps en arreglo de valores
					if (!in_array($val,$arraySteps)){
						$arraySteps[] = $val;
					}
					$valores_mpio[] =  $val;
				}
		
			}
			//echo "Municipio:$id_mpio :: Valor = $val<br>";
			
		}
		
		$fuente = $fuente_dao->Get($id_fuente);
		$clase = $clase_dao->Get($id_clase);
		foreach($id_tipo as $t=>$id_t){
			$tipo = $tipo_dao->Get($id_t);
			if ($t == 0)	$tipo_tl = $tipo->nombre;
			else			$tipo_tl .= "-".$tipo->nombre;
		}
		
		if ($id_tipo_periodo != 'aaaa'){
			$periodo = $periodo_dao->GetAllArray("cons_perio IN ($id_periodo)");
			if (count($periodo) > 1)	$periodo_s = $periodo[0]->nombre."-".$periodo[count($periodo)-1]->nombre;
			else						$periodo_s = $periodo[0]->nombre;
		}
		else{
			$periodo = split(",",$id_periodo); 
			if (count($periodo) > 1)	$periodo_s = $periodo[0]."-".$periodo[count($periodo)-1];
			else						$periodo_s = $periodo[0];
		}

		//Acorta nombres, Semestre->Sem
		$periodo_s = str_replace("Semestre","Sem",$periodo_s);
		
		$legend = $clase->nombre." ".$tipo_tl;
		if ($variacion == 1)	$legend = "Variación ".$legend; 
		$legend_fuente = "Fuente: ".$fuente->nombre; 
		$legend_periodo = $periodo_s; 
		if ($tasa == 1)	$legend_periodo .= " Tasa por 100.000 habitantes"; 
		
		$tit_convencion = "Desplazamiento";

		//$et = microtime(true) - $mt;
		//echo "Elapsed time: " . number_format($et) . "s\n";
		//echo "QPS: " . ($tq / $et) . "\n";
		
	break;
	
	case 'dato_sectorial':
		$id_dato = $_GET["id_dato"];
		$aaaa = $_GET["aaaa"];
		$unidad_nombre = "";

		// Cache
		$id_cache = $sissh->opCacheMapaDatoSector('get',$id_dato,$aaaa,$variacion,$tasa,$id_depto_filtro,$extent_get);
		if ($id_cache == 0){
			$sissh->opCacheMapaDatoSector('insertar',$id_dato,$aaaa,$variacion,$tasa,$id_depto_filtro,$extent_get);

			$max_id = $sissh->maxCacheMapaDatoSector();
			
			$img_cache = "mapa_dato_sector_$max_id.png";
		}
		else{
            $img_cache = "$img_path_cache_tematico/mapa_dato_sector_$id_cache.png";

            $_SESSION["mapserver_img"] = $img_cache;
            
            $image = imagecreatefrompng($img_cache);

			imagepng($image);
			
			exit;
		}

		$xls = "<tr><td><b>CODIGO</b></td><td><b>MUNICIPIO</b></td><td><b>VALOR $porc</b></td></tr>";
		
		foreach ($mpios as $id_mpio){
			
			$nom_mpio = $mpios_nombre[$id_mpio];
			
			if ($variacion == 1){
				$periodo = split(",",$aaaa);
				
				//(incidencia periodo corriente-incidencia periodo anterior)/(incidencia periodo anterior)*100= % variación
				
				$a_corr = $periodo[1];
				$f_ini_corr = $a_corr."-1-1";
				$f_fin_corr = $a_corr."-12-31";
		
				$a_ant = $periodo[0];
				$f_ini_ant = $a_ant."-1-1";
				$f_fin_ant = $a_ant."-12-31";
				
				$val_corr = $d_s_dao->GetValorToReport($id_dato,$id_mpio,$f_ini_corr,$f_fin_corr,2);
				$val_ant = $d_s_dao->GetValorToReport($id_dato,$id_mpio,$f_ini_ant,$f_fin_ant,2);
				
				$val_corr = $val_corr['valor'];
				$val_ant = $val_ant['valor'];
				
				if ($val_corr != 'N.D.' || $val_ant != 'N.D.'){
					
					//Si alguno de los 2 valores es 0, se reemplaza con 0.1 para mostrar variación
					if ($val_corr == 'N.D.')	$val_corr = 0.1;
					if ($val_ant == 0 || $val_ant == 'N.D.')	$val_ant = 0.1;
						
					//$val = intval(($val_corr - $val_ant)/($val_ant)*100);
					$val = number_format(($val_corr - $val_ant)/($val_ant)*100,2);
					
					//echo "$id_mpio => Corr: $val_corr -- Ant: $val_ant === $val<br>";
					$valores_mpio[] =  $val;
					$valores_mpio_mpio[$id_mpio] =  $val;
					
					$xls .= "<tr><td>$id_mpio</td><td>$nom_mpio</td><td>$val</td></tr>";
					
					//Check de minimo steps en arreglo de valores
					if (!in_array($val,$arraySteps)){
						$arraySteps[] = $val;
					}
					
				}
			}
			else if ($tasa == 1){
				
				$f_ini = $aaaa."-1-1";
				$f_fin = $aaaa."-12-31";
			
				$val = $d_s_dao->GetValorToReport($id_dato,$id_mpio,$f_ini,$f_fin,2);
				$val_d_s = $val['valor'];
				
				//CONSULTA EL TOTAL DE LA POBLACION EN EL MISMO PERIODO DEL DATO PARA LA UBIACION
				$id_dato_pob = 3;
				$val = $d_s_dao->GetValorToReport($id_dato_pob,$id_mpio,$f_ini,$f_fin,2);
				$total_poblacion = $val['valor'];
				
				if ($total_poblacion > 0 && $total_poblacion != "N.D."){
					$val_tasa = intval(($val_d_s/$total_poblacion)*100000);
					
					$valores_mpio[] =  $val_tasa;
					$valores_mpio_mpio[$id_mpio] =  $val_tasa;
					
					$xls .= "<tr><td>$id_mpio</td><td>$nom_mpio</td><td>$val_tasa</td></tr>";
					
					//Check de minimo steps en arreglo de valores
					if (!in_array($val_tasa,$arraySteps)){
						$arraySteps[] = $val_tasa;
					}
				}
			}
			else{
				
				$periodo = split(",",$aaaa);
				$hy = 0;
				$valor = 0;
				foreach ($periodo as $aaaa_t){
					$f_ini = $aaaa_t."-1-1";
					$f_fin = $aaaa_t."-12-31";
					
					$val = $d_s_dao->GetValorToReport($id_dato,$id_mpio,$f_ini,$f_fin,2);
					
					if ($val['valor'] != 'N.D.'){
						$valor += $val['valor'];
						$hy = 1;
					}
					
				}
				
				if ($hy == 1){
					//APLICA FORMATO
					$id_unidad = $val['id_unidad'];
					
					$valor = $d_s_dao->formatValorToMapa($id_unidad,$valor,0);
					$valores_mpio_mpio[$id_mpio] =  $valor;
					$valores_mpio[] =  $valor;
					
					$xls .= "<tr><td>$id_mpio</td><td>$nom_mpio</td><td>$valor</td></tr>";
					
					//	Check de minimo steps en arreglo de valores
					if (!in_array($valor,$arraySteps)){
						$arraySteps[] = $valor;
					}
					
					$unidad_vo = $unidad_dao->get($id_unidad);
					$unidad_nombre = " ($unidad_vo->nombre)";						
				}
			
			}
		}
		
		$dato_vo = $d_s_dao->Get($id_dato);
		$fuente_vo = $contacto_dao->Get($dato_vo->id_contacto);
				
		$legend = $dato_vo->nombre.$unidad_nombre;
		if ($variacion == 1)	$legend = "Variación ".$legend;
		$legend_fuente = "Fuente: ".$fuente_vo->nombre; 
		$legend_periodo = $aaaa; 
		if ($tasa == 1)	$legend_periodo .= " Tasa por 100.000 habitantes"; 
		
		$tit_convencion = "Dato Sectorial";
		
	break;
	
	case 'org':
		$xls = "<tr><td><b>CODIGO</b></td><td><b>MUNICIPIO</b></td><td><b>Num. Orgs</b></td></tr>";
		$html = "<tr><td><b>CODIGO</b></td><td><b>MUNICIPIO</b></td><td><b>Num Orgs</b></td><td></td></tr>";
		
		//default municipio para el mapa de todas las orgs
		$caso = 'municipio';
		$id = 0;
		
		if ($_GET["id_tipo"] != ''){
			$id = $_GET["id_tipo"];
			$caso = 'tipo';
			$vo = $tipo_org_dao->Get($id);
		}
		
		if ($_GET["id_sector"] != ''){
			$id = $_GET["id_sector"];
			$caso = 'sector';
			$vo = $sector_dao->Get($id);
		}
		
		if ($_GET["id_pob"] != ''){
			$id = $_GET["id_pob"];
			$caso = 'poblacion';
			$vo = $pob_dao->Get($id);
		}
		
		if ($_GET["id_enfoque"] != ''){
			$id = $_GET["id_enfoque"];
			$caso = 'enfoque';
			$vo = $enfoque_dao->Get($id);
		}
		
		if ($_GET["id_org"] != ''){
			$id_org = $_GET["id_org"];
			$una_org = 1;
			$caso = "1 Org";
		}
		
		if ($una_org == 0){
			
			// Cache
			$id_cache = $sissh->opCacheMapaOrg('get',$caso,$id,$id_depto_filtro,$extent_get);
			if ($id_cache == 0){
				$sissh->opCacheMapaOrg('insertar',$caso,$id,$id_depto_filtro,$extent_get);

				$max_id = $sissh->maxCacheMapaOrg();
				
				$img_cache = "mapa_org_$max_id.png";
			}
			else{
				$img_cache = "$img_path_cache_tematico/mapa_org_$id_cache.png";

                $_SESSION["mapserver_img"] = $img_cache;
				
				$image = imagecreatefrompng($img_cache);
				
				imagepng($image);
				
				exit;
			}
			
			foreach ($mpios as $id_mpio){
				
				$mpio_vo = $mpio_dao->Get($id_mpio);
				$id_depto = $mpio_vo->id_depto;
				$nom_mpio = $mpios_nombre[$id_mpio];
				
				$valor = $org_dao->numOrgsConteo($caso,$id,0,$id_mpio,'','');
				$val = $valor['total'];
				$valores_mpio_mpio[$id_mpio] =  $val;
				$valores_mpio[] =  $val;
				
				$xls .= "<tr><td>$id_mpio</td><td>$nom_mpio</td><td>$val</td></tr>";
				$html .= "<tr><td>$id_mpio</td><td>$nom_mpio</td><td>$val</td><td><a href='#' onclick=\"reporteOrg('$id_depto','$id_mpio','id_".$caso."','$id');\">Listado</a></td></tr>";
				
				//Check de minimo steps en arreglo de valores
				if (!in_array($val,$arraySteps) && $val > 1){
					$arraySteps[] = $val;
				}
			}
			
			$legend = "Presencia Organizaciones";
			$legend_periodo = ($caso != 'municipio') ?  ucfirst($caso).": " : "Total"; 
			$legend_fuente = ($una_org == 0 && $caso != 'municipio') ? $vo->nombre_es : ""; 
			
			$tit_convencion = "Organizaciones";
			
		}
		else{
			// Cache
			$id_cache = $sissh->opCacheMapaOrg('get','una_org',$id_org,$id_depto_filtro,$extent_get);
			if ($id_cache == 0){
				$sissh->opCacheMapaOrg('insertar','una_org',$id_org,$id_depto_filtro,$extent_get);

				$max_id = $sissh->maxCacheMapaOrg();
				
				$img_cache = "mapa_org_$max_id.png";
			}
			else{
				
				$img_cache = "$img_path_cache_tematico/mapa_org_$id_cache.png";

                $_SESSION["mapserver_img"] = $img_cache;
				
				$image = imagecreatefrompng($img_cache);

				imagepng($image);
				
				exit;
			}

			$mpios = $org_dao->getMpiosSedeCobertura($id_org);
			foreach ($mpios as $id_mpio){
				
				$nom_mpio = $mpio_dao->GetName($id_mpio);
				$valores_mpio_mpio[$id_mpio] =  1;
				$valores_mpio[] =  1;
				
				$xls .= "<tr><td>$id_mpio</td><td>$nom_mpio</td><td>1</td></tr>";
				$html .= "<tr><td>$id_mpio</td><td>$nom_mpio</td><td>1</td></tr>";
				
			}
			
			if (count($valores_mpio) > 0)	$arraySteps = array(0,1);
			
			$drawConv = 0;
			$tit_convencion = "Organizaciones";
			$legend = "Presencia Organizaciones";
			
			$nom_org = $org_dao->GetName($id_org);
			
			//Corta el nombre de la org, para que quede en 2 lineas
			if (strlen($nom_org) > 30){
				$nom_org_wrap = wordwrap($nom_org,30,'|');
				$nom_tmp = split("[|]",$nom_org_wrap);
				
				$legend_periodo = $nom_tmp[0]; 
				$legend_fuente = $nom_tmp[1];
			}
			else{
				$legend_periodo = $nom_org; 
				$legend_fuente = "";
			}
		}
		
	break;
	
	case 'evento_c':

		$reporte = $_GET["reporte"];
		$id_cats = $_GET["id_cats"];
		$meses = array ("","Ene","Feb","Mar","Abr","May","Jun","Jul","Ago","Sep","Oct","Nov","Dic");
		
		if ($_GET["id_subcats"] == ''){
			$id_scats = $scat_evento_c_dao->GetAllArrayID("id_cateven IN ($id_cats)");
		}
		else{
			$id_scats = split(",",$_GET["id_subcats"]);
		}
		
		$f_ini = $_GET["f_ini"];
		$f_ini_s = split("[-]",$f_ini);
		
		$f_fin = $_GET["f_fin"];
		$f_fin_s = split("[-]",$f_fin);
		
		$id_actor = 0;  //Por ahora no está en los filtros
		
		$title_reporte = array("","NUMERO DE EVENTOS","NUMERO DE VICTIMAS");
		
		$xls = "<tr><td><b>CODIGO</b></td><td><b>MUNICIPIO</b></td><td><b>$title_reporte[$reporte]</b></td></tr>";
		
		foreach ($mpios as $id_mpio){
			
			$nom_mpio = $mpios_nombre[$id_mpio];
			
			$num = 0;
			foreach ($id_scats as $id_scat){
				if ($reporte == 1){
					$num += $evento_c_dao->numEventosReporte($id_mpio,$id_scat,$id_actor,$f_ini,$f_fin);
				}
				else{
					$num += $evento_c_dao->numVictimasReporte($id_mpio,array("id_mun"=>1,"f_ini"=>$f_ini,"f_fin"=>$f_fin,"id_scat"=>$id_scat));
				}
			}
			
			$valores_mpio_mpio[$id_mpio] =  $num;
			$valores_mpio[] =  $num;
			
			$xls .= "<tr><td>$id_mpio</td><td>$nom_mpio</td><td>$num</td></tr>";
			
			//Check de minimo steps en arreglo de valores
			if (!in_array($num,$arraySteps) && $num > 1){
				$arraySteps[] = $num;
			}
		}
		
		$legend = ucwords($title_reporte[$reporte]);
		$legend_periodo = "Desde: ".(1*$f_ini_s[2])." ".$meses[$f_ini_s[1]*1]." ".$f_ini_s[0]; 
		$legend_fuente = "Hasta: ".($f_fin_s[2]*1)." ".$meses[$f_fin_s[1]*1]." ".$f_fin_s[0]; 
		
		$tit_convencion = "Eventos del Conflicto";
		
	break;
	
	case 'proyecto_undaf':
		$xls = "<tr><td><b>CODIGO</b></td><td><b>MUNICIPIO</b></td><td><b>Num. Proyectos</b></td></tr>";
		$html = "<tr><td><b>CODIGO</b></td><td><b>MUNICIPIO</b></td><td><b>Num Proyectos</b></td><td></td></tr>";
		
		$id_filtro = $_GET["id_filtro"];
		$filtro = $_GET["filtro"];
		$un_proy = 0;
		
		unset($_SESSION["id_tema_undaf"]);

		switch ($filtro){
			case 'tema':
				$vo = $tema_dao->Get($id_filtro);
				//Graba el valor del tema para usarlo en el logo que se va a insertar en el mapa (mapserver.class.php->drawExtras())
				$_SESSION["id_tema_undaf"] = $id_filtro;
			break;
			case 'agencia';
				$sigla_org = $org_dao->GetFieldValue($id_filtro,'sig_org');
			break;
			case 'cobertura';
				$sigla_org = $org_dao->GetFieldValue($id_filtro,'sig_org');
			break;
			
		}

		if ($_GET["id_proy"] != ''){
			$id_proy = $_GET["id_proy"];
			$un_proy = 1;
			$caso = "1 Proy";
		}
		
		if ($un_proy == 0){
			
			// Cache
			$id_cache = $sissh->opCacheMapaProy('get',$filtro,$id_filtro,$id_depto_filtro,$extent_get);
			if ($id_cache == 0){
				$sissh->opCacheMapaProy('insertar',$filtro,$id_filtro,$id_depto_filtro,$extent_get);

				$max_id = $sissh->maxCacheMapaProy();
				
				$img_cache = "mapa_proy_$max_id.png";
			}
			else{
			
                $img_cache = "$img_path_cache_tematico/mapa_proy_$id_cache.png";

                $_SESSION["mapserver_img"] = $img_cache;

                $image = imagecreatefrompng($img_cache);

				imagepng($image);
				
				exit;
			}
			
			foreach ($mpios as $id_mpio){
				
				$mpio_vo = $mpio_dao->Get($id_mpio);
				$id_depto = $mpio_vo->id_depto;
				$nom_mpio = $mpios_nombre[$id_mpio];
				
				$valor = $proy_dao->numProyectos($filtro,$id_filtro,0,$id_mpio);
				$valores_mpio_mpio[$id_mpio] =  $valor;
				$valores_mpio[] =  $valor;
				//print_r($valores_mpio);
				$xls .= "<tr><td class=\"excel_celda_texto\">$id_mpio</td><td>$nom_mpio</td><td>$valor</td></tr>";
				$html .= "<tr><td>$id_mpio</td><td>$nom_mpio</td><td>$valor</td></tr>";
				
				//Check de minimo steps en arreglo de valores
				if (!in_array($valor,$arraySteps) && $valor > 1){
					$arraySteps[] = $valor;
				}
			}
			
			$legend = 'Cobertura Proyectos';
			$legend_periodo = ''; 
			$legend_fuente = ''; 
			
			if ($filtro == 'tema'){
				$legend_periodo = ucfirst($filtro); 
				$legend_fuente = $vo->nombre; 
			}
			else if ($filtro == 'agencia'){
				$legend_periodo = $sigla_org; 
			}
			else if ($filtro == 'cobertura'){
				$legend_periodo = $sigla_org; 
			}
			
			$tit_convencion = "Proyectos";
			
		}
		else{
			
			// Cache
			$id_cache = $sissh->opCacheMapaProy('get','un_proy',$id_proy,$id_depto_filtro,$extent_get);
			if ($id_cache == 0){
				$sissh->opCacheMapaProy('insertar','un_proy',$id_proy,$id_depto_filtro,$extent_get);

				$max_id = $sissh->maxCacheMapaProy();
				
				$img_cache = "mapa_proy_$max_id.png";
			}
			else{
				
				$img_cache = "$img_path_cache_tematico/mapa_proy_$id_cache.png";

                $_SESSION["mapserver_img"] = $img_cache;

                $image = imagecreatefrompng($img_cache);

				imagepng($image);
				
				exit;
			}
			
			$mpios = $proy_dao->getMpiosCobertura($id_proy);
			foreach ($mpios as $id_mpio){
				
				$nom_mpio = $mpio_dao->GetName($id_mpio);
				$valores_mpio_mpio[$id_mpio] =  1;
				$valores_mpio[] =  1;
				
				$xls .= "<tr><td>$id_mpio</td><td>$nom_mpio</td><td>1</td></tr>";
				$html .= "<tr><td>$id_mpio</td><td>$nom_mpio</td><td>1</td></tr>";
				
			}
			
			if (count($valores_mpio) > 0)	$arraySteps = array(0,1);
			
			$drawConv = 0;
			$tit_convencion = "Proyectos";
			$legend = "Presencia Proyectos";
			
			$nom = $proy_dao->GetName($id_proy);
			
			//Corta el nombre de la org, para que quede en 2 lineas
			if (strlen($nom) > 30){
				$nom_wrap = wordwrap($nom,30,'|');
				$nom_tmp = split("[|]",$nom_wrap);
				
				$legend_periodo = $nom_tmp[0]; 
				$legend_fuente = $nom_tmp[1];
			}
			else{
				$legend_periodo = $nom; 
				$legend_fuente = "";
			}
		}
		
	break;
  }

	//Check de minimo steps en arreglo de valores para hacer jenks y pintar el mapa
	if (count($arraySteps) == 0){
		
		$img = imagecreatefrompng($_SERVER["DOCUMENT_ROOT"]."/sissh/images/mscross/error_mapa_info.png");
		imagePng($img);
		die();
	
	}
	
	sort($valores_mpio);
	sort($arraySteps);
	
	//Elimina los valores que son 0 y los copia en un arreglo especial para mostrarlo con color blanco
	if ($variacion == 0){
		$salir = 0;
		while ($salir == 0){
			if ($valores_mpio[0] == 0){
				$draw_cero = 1;
				array_shift($valores_mpio);
			}
			else						$salir = 1;
		}
	}
	
	//print_r($valores_mpio);
	//print_r($arraySteps);
	
	//$method = 'intervalos';
	$method = 'jenks';
	$kclass = array();
	
	
	if (count($arraySteps) > 5){
		$numclass = 5;
	}
	else if (count($arraySteps) <= 5 && count($arraySteps) > 2){
	
		//No se debe colocar como limite de un intervalo un numero que es consecutivo del limite anterior, para evitar casos como 0 -1, 2 - 2
		for($a=0;$a<count($arraySteps)-1;$a++){
			//if (count($kclass) == 0 && $arraySteps[$a] > 0){
			if (count($kclass) == 0 && $arraySteps[$a] > 1){
				$kclass[] = array_search($arraySteps[$a],$valores_mpio);
			}
			else if ($arraySteps[$a] > 0 && count($kclass) > 0){
				if ($arraySteps[$a] - $arraySteps[$a-1] != 1){
					if(array_search($arraySteps[$a],$valores_mpio) !== false){
						$kclass[] = array_search($arraySteps[$a],$valores_mpio);
					}			
				}
			}
		}
		
		//Inserta el ultimo
		$kclass[] = array_search($arraySteps[count($arraySteps)-1],$valores_mpio);
		
		//if (in_array(0,$arraySteps))	$dif_uno++;
		//print_r($kclass);
		//echo "<br>";
		
		$numclass =  count($kclass);

	}
	else{
		$kclass[] = array_search($arraySteps[count($arraySteps)-1],$valores_mpio);
		$numclass = 1;
	}
	
	//Para variación se muestran los máximos intervalos posibles, que serian 4, el ultimo nop, porque no hay maximo
	if ($variacion == 1){
		if (max($valores_mpio) < 50.01)	$numclass = 4;
		else							$numclass = 5;
	}
	
	//Variable para almacenar los id de los mpios en cada intervalo
	$id_mpio_matrix  = array(array(),array(),array(),array(),array(),array());
	
	//Intervalos
	switch ($method){
		case 'intervalos':
			
			$min = min($valores_mpio);
			$max = max($valores_mpio);
			
			$factor = ceil((ceil($max) - ceil($min))/$numclass);
			//echo $factor;
			
			
			foreach ($valores_mpio_mpio as $id_mpio=>$valor){
				for($i=1;$i<$numclass;$i++){
					$sup = $min+($i*$factor);
					$inf = $min+($i-1)*$factor;
					if ($valor <= $sup && $valor >= $inf){
						$id_mpio_matrix[$i][] = $id_mpio;
						//echo "Valor=$valor----$id_mpio-----i=$i---min=$min<br>";
						$i = $numclass;
					}
				}
			}
		break;
		
		case 'jenks':
			
			if ($variacion == 1){
				//Los rangos son definidos
				$kclass = array(-50,-25,25,50);
				
				//Pero el mayor valor si es el mas alto
				$kclass[] = max($valores_mpio);
				
				foreach ($valores_mpio_mpio as $id_mpio=>$valor){
					for($i=1;$i<($numclass+1);$i++){
						//primer valor debe ser el primero de los datos
						if ($i == 1){
							$sup = $kclass[0];
							$inf = -100;
						}
						else{
							$k = $i - 1;
							$sup = $kclass[$k];
							$inf = $kclass[$k-1] + 0.01;
						}
						if ($valor <= $sup && $valor >= $inf){
							$id_mpio_matrix[$i][] = $id_mpio;
							//echo "Valor=$valor----$id_mpio-----i=$i---min=$min<br>";
							$i = $numclass + 1;
						}
					}
				}
			}
			else{
				if (count($kclass) == 0){
					$kclass = $mapserver->jenks($numclass,$valores_mpio);

					sort($kclass);  //Extrae el ultimo porque jenks los devuelve en orden inverso
					
					//Revisa si el primer limite es 0 y elimina un intervalo, esto se creo para Orgs, porque
					//existia el caso que jenks devolvia como limite el indice cuyo valores_mpio era cero entonces
					//queda un intervalo 0 - 0
					//Ahora como los valores de 0 no entran en los intervalos, tbn se elimina un intervalo cuando 
					//el indice cuyo valores_mpioes es 1
					if ($valores_mpio[$kclass[0]] == 0 || $valores_mpio[$kclass[0]] == 1){
						array_shift($kclass);  
						$numclass--;
					}
					
					$fix = 1;
					while ($fix == 1){
						//print_r($kclass);
						//echo "entro<br>";
						$fix = 0;
						//Revisa limites que sean consecutivos para evitar casos de 0-5,6-6
						for($a=0;$a<count($kclass)-1;$a++){
							if ($valores_mpio[$kclass[$a+1]] - $valores_mpio[$kclass[$a]] == 1 || $valores_mpio[$kclass[$a+1]] == $valores_mpio[$kclass[$a]]){
								if ($kclass[$a+1] + 1 < count($valores_mpio)){
									$kclass[$a+1]++;
									$fix = 1;
								}
								
							}
						}
						//echo "resulto<br>";
						//print_r($kclass);
					}
					
					//Si al aumentar los limites en 1, el ultimo limite es igual al penultimo, se debe eliminar el ultimo intervalo
					if ($valores_mpio[$kclass[count($kclass)-2]] == $valores_mpio[$kclass[count($kclass)-1]]){
						array_pop($kclass);  
						$numclass--;
					}

					//Si al aumentar los limites en 1, el penultimo + 1 es igual al ultimo, debe sumar 1 al penultimo y elminar el ultimo intervalo
					if (($valores_mpio[$kclass[count($kclass)-1]] - $valores_mpio[$kclass[count($kclass)-2]]) == 1){
						$kclass[count($kclass)-2] = $kclass[count($kclass)-1];
						array_pop($kclass);  
						$numclass--;
					}
					
				}
				
				//print_r($kclass);
				
				foreach ($valores_mpio_mpio as $id_mpio => $valor){
					for($i=1;$i<($numclass+1);$i++){
						
						//primer valor debe ser el primero de los datos
						/*if ($i == 1){
							$sup = $valores_mpio[$kclass[0]];
							$inf = $valores_mpio[0];
						}
						else{
							$k = $i - 1;
							$sup = $valores_mpio[$kclass[$k]];
							$inf = $valores_mpio[$kclass[$k-1]] + 1;
						}*/
						
						$limites = $mapserver->getInfSup($valores_mpio,$kclass,$i-1,$variacion); 
						$sup = $limites['sup'];
						$inf = $limites['inf'];
						
						if ($valor <= $sup && $valor >= $inf){
							$id_mpio_matrix[$i][] = $id_mpio;
							//echo "Valor=$valor----$id_mpio-----i=$i---min=$min<br>";
							$i = $numclass + 1;
						}
					}
				}
			}
			
		break;
	}
	
	//print_r($id_mpio_matrix);
		
	//Escala de colores para la plantilla de Desplazamieto
	$rgb = array('desplazamiento' => array("r" => array("245","245","245","245","245"),
								"g" => array("245","184","122","61","0"),
								"b" => array("0","0","0","0","0")),
				 'dato_sectorial' => array("r" => array("255","255","250","237","219"),
								"g" => array("204","158","114","67","0"),
								"b" => array("204","143","90","45","0")),
				 'org' => array("r" => array("215","177","112","69","2"),
								"g" => array("252","255","237","192","130"),
								"b" => array("216","179","115","72","5")),
				'evento_c' => array("r" => array("245","245","245","245","245"),
								"g" => array("245","184","122","61","0"),
								"b" => array("0","0","0","0","0")),
				 'proyecto_undaf' => array("r" => array("226","170","114","57","1"),
								"g" => array("239","200","160","121","81"),
								"b" => array("249","226","204","181","158"))
				);
	
	//Coloca la paleta de colores de acuerdo al tema undaf
	if ($case == 'proyecto_undaf' && isset($_SESSION["id_tema_undaf"])){
		//Equidad = azul claro
		if ($_SESSION["id_tema_undaf"] == 1){
			$rgb['proyecto_undaf'] = array("r" => array("226","170","114","57","1"),
								"g" => array("239","200","160","121","81"),
								"b" => array("249","226","204","181","158"));
		}
		//Social - azul oscuro
		else if ($_SESSION["id_tema_undaf"] == 3){
			$rgb['proyecto_undaf'] = array("r" => array("226","170","114","57","1"),
								"g" => array("239","200","160","121","81"),
								"b" => array("249","226","204","181","158"));
		}
		//Sostenible - rojo
		else if ($_SESSION["id_tema_undaf"] == 2){
			$rgb['proyecto_undaf'] = array("r" => array("255","248","241","233","226"),
								"g" => array("213","160","107","53","0"),
								"b" => array("218","170","122","74","26"));
		}
		//Paz - amarillo
		else if ($_SESSION["id_tema_undaf"] == 4){
			$rgb['proyecto_undaf'] = array("r" => array("255","255","255","254","254"),
								"g" => array("243","233","224","214","204"),
								"b" => array("192","144","96","48","0"));
		}
	}

	if ($variacion == 1){
		$rgb = array('desplazamiento' => array("r" => array("85","211","250","255","255"),
								"g" => array("255","255","250","190","0"),
								"b" => array("0","190","123","42","0")),
					 'dato_sectorial' => array("r" => array("85","211","250","255","255"),
								"g" => array("255","255","250","190","0"),
								"b" => array("0","190","123","42","0"))
					);
	}
	
	//Si es consulta de una sola org, se coloca el color del intervalo 1 mas notorio
	if ($una_org == 1){
		$rgb['org']['r'][0] = 255;
		$rgb['org']['g'][0] = 185;
		$rgb['org']['b'][0] = 0;
	}
	
	//Estilo para Deptos
	$classObj = ms_newClassObj($layerObj);
	$classObj->set("name","depto");
	$classObj->setExpression("/.*/");
	$styleObj =  ms_newStyleObj($classObj);
	//$styleObj->color->setRGB(240,240,240);
	$styleObj->outlinecolor->setRGB(50,50,50);
	$styleObj->set("antialias",MS_TRUE);
	
	for($i=1;$i<($numclass+1);$i++){
		
		$j = $i - 1;
		
		//Si filtro de depto, los otros se pintan blancos con outline
		if ($id_depto_filtro > 0){
			$classObj = ms_newClassObj($layerObj);
			$classObj->setExpression("/^$id_depto_filtro/");
			$styleObj =  ms_newStyleObj($classObj);
			$styleObj->outlinecolor->setRGB(102,102,102);
			$styleObj->set("antialias",MS_TRUE);
//			$classObj->label->set("type",MS_TRUETYPE);
//			$classObj->label->set("font",$tag_font_label);
//			$classObj->label->set("size",$size_font_label);  //Tamaño en pixeles
//			$classObj->label->set("position",MS_AUTO);
//			$classObj->label->color->setRGB(0,0,0);
//			$classObj->label->set("antialias",MS_TRUE);
		}
	
		
		//Estilo para Mpios
		if (count($id_mpio_matrix[$i]) > 0){
			//echo "Expresion = ".implode("|",$id_mpio_matrix[$i])."<br>";
			$classObj = ms_newClassObj($layerObjMpio);
			$classObj->set("name","intervalo$i");
			
			$classObj->setExpression("/".implode("|",$id_mpio_matrix[$i])."/");
			$styleObj = ms_newStyleObj($classObj);
			$styleObj->color->setRGB($rgb[$case]["r"][$j],$rgb[$case]["g"][$j],$rgb[$case]["b"][$j]);
			$styleObj->outlinecolor->setRGB(204,204,204);
			$styleObj->set("antialias",MS_TRUE);
			
			//Solo se le coloca nombre a los 3 ultimos del intervalo
			if ($i >= ceil($numclass/2)){
//				$classObj->label->set("type",MS_TRUETYPE);
//				$classObj->label->set("font",$tag_font_label);
//				$classObj->label->set("size",$size_font_label);  //Tamaño en pixeles
//
//				$classObj->label->set("position",MS_CC);
//				$classObj->label->set("antialias",MS_TRUE);
//				$classObj->label->color->setRGB(0,0,0);
//				$classObj->label->outlinecolor->setRGB(255,255,255);
			}
		}
	}
	
	
	//Estilo para los municipios que no estan en ningun intervalo, caso dato sectoriales con N.D.
	$classObj = ms_newClassObj($layerObjMpio);
	$classObj->set("name","blanco");
	$classObj->setExpression("/.*/");
	$styleObj =  ms_newStyleObj($classObj);
	$styleObj->color->setRGB(255,255,255);
	$styleObj->outlinecolor->setRGB(204,204,204);
	
	//ZOOM
	$zoom_factor = 1;
	
	$mapObj->zoomPoint($zoom_factor,$clkpoint,$mapObj->width,$mapObj->height,$old_extent,$max_extent);
	
	//SCALEBAR
	$mapObj->scalebar->set("status",MS_EMBED);
	$mapObj->scalebar->color->setRGB(0,0,0);
	$mapObj->scalebar->set("position",MS_UL);
	$mapObj->scalebar->set("intervals",3);
	$mapObj->scalebar->set("width",150);
	$mapObj->scalebar->set("height",5);
	$mapObj->scalebar->set("units",MS_KILOMETERS);
	$mapObj->scalebar->label->set("type",MS_TRUETYPE);
	$mapObj->scalebar->label->set("font",$tag_font_label);
	$mapObj->scalebar->label->set("size",$size_font_label);
	$mapObj->scalebar->label->set("position",MS_UC);
	$mapObj->scalebar->label->color->setRGB(0,0,0);
	//$mapObj->scalebar->label->outlinecolor->setRGB(255,255,255);
	
	$imageObj = $mapObj->draw();
	$image_name = $imageObj->saveWebImage();
	$image_url = $image_name;
	
	$path_to_img = $_SERVER['DOCUMENT_ROOT'].$image_name;

	$size = getimagesize($path_to_img);
	$im_tmp = imagecreatefrompng($path_to_img);

	// Convert the Image to PNG-24
	$image = imagecreatetruecolor($size[0],$size[1]);
	//imageantialias($image,1);
	imagecopy($image,$im_tmp,0,0,0,0,$size[0],$size[1]);
	imagedestroy($im_tmp);
	
	//Convenciones, se colocan cuando no es mapa referencia y cuando no es bogotá que tiene solo 1 mpio
	$x_ini = 10;
	$ancho = 0;
	$space_recs = 7;
	$color = imagecolorallocate($image,238,238,238);
	$color_out = imagecolorallocate($image,204,204,204);
	$blanco = imagecolorallocate($image,255,255,255);
	
	if ($print == 0){
		$font_ttf = 'fonts/copy0856';
		$size_font_ttf = 6;
		
		$font_ttf_titulo = 'fonts/copy0856';
		$size_font_ttf_titulo = 6;
		
		$font_ttf_legend = 'fonts/copy0856';
		$size_font_ttf_legend = 6;
	}
	else{
		$font_ttf = 'fonts/Arial';
		$size_font_ttf = 14;
		
		$font_ttf_titulo = 'fonts/Arial';
		$size_font_ttf_titulo = 14;
		
		$font_ttf_legend = 'fonts/Arial';
		$size_font_ttf_legend = 14;
	}
	
	$color_font = imagecolorallocate($image,0,0,0);
	
	if ($map_ref == 0 && $drawConv == 1){
	
		$x = $x_ini;
		
		$alto_rec = $size_font_ttf_legend;  //Rectangulo pequeño que tiene el color
		$space_text_rec = 10;  //Espacio entre rectangulo y texto de cada intervalo. horizontal
		
		$y_title = $size_font_ttf_titulo + 8;
		
		$filas_legenda = ($variacion == 0 && $draw_cero == 1) ?  $numclass + 1 : $numclass;
		
		$alto = 3*$space_recs + ($filas_legenda*$alto_rec) + $space_recs*($filas_legenda-1) + $y_title;   //Titulo + numero de intervalos + espacion entre rectangulos
		
		$y_ini = $height_img - $alto - 5;
		$y = $y_ini;
		
		//Calcula el texto mas largo de la convencion para colocarlo como ancho del rectangulo
		$arr = imagettfbbox($size_font_ttf,0,$font_ttf,strtoupper($tit_convencion));
		$w_er = $arr[2] - $arr[0];
		$h_er = $arr[1] - $arr[7];
		for($i=0;$i<$numclass;$i++){
			
			if ($variacion == 1 && $numclass == 1){
				
				$max_val = max($valores_mpio);
				$l = 0;
				foreach ($kclass as $lim){
					
					$limites = $mapserver->getInfSup($valores_mpio,$kclass,$i,$variacion); 
					$sup = $limites['sup'];
					$inf = $limites['inf'];
					
					if ($max_val > $inf && $max_val <= $sup){
						$i = $l;
					}
				}
			}
			
			$limites = $mapserver->getInfSup($valores_mpio,$kclass,$i,$variacion); 
			$sup = $limites['sup'];
			$inf = $limites['inf'];
			
			if ($variacion == 1){
				$inf .= "%";
				$sup .= "%";
			}
			
			$text = "$inf - $sup";
			
			$arr = imagettfbbox($size_font_ttf,0,$font_ttf,$text);
			$text_w = $arr[2] - $arr[0];
			if ($text_w > $w_er)	$w_er = $text_w;
		}
		
		//Ancho rectangulo
		$ancho = $w_er + $alto_rec + $x_ini + $space_text_rec;
		
		//Rectangulo gris
		imagefilledrectangle($image,$x_ini,$y_ini,$x+$ancho,$y+$alto,$color);
		imagerectangle($image,$x_ini,$y_ini,$x_ini+$ancho,$y_ini+$alto,$color_out);
		
		//Titulo
		imagettftext($image,$size_font_ttf_titulo,0,$x+10,$y+$y_title,$color_font,$font_ttf_titulo,strtoupper($tit_convencion));

		$x = 20;
		$y += $h_er;
		
		if ($variacion == 0 && $draw_cero == 1){
			
			$y += $alto_rec + $space_recs;
			
			//Convencion para el 0
			imagefilledrectangle($image,$x,$y,$x+$alto_rec,$y+$alto_rec,$blanco);
			
			$text = "0";
			$y_font = $y + $alto_rec;
			imagettftext($image,$size_font_ttf,0,$x+$alto_rec+$space_text_rec,$y_font,$color_font,$font_ttf,$text);
		}
		
		//$y += $space_recs;
		for($i=0;$i<$numclass;$i++){
			
			$limites = $mapserver->getInfSup($valores_mpio,$kclass,$i,$variacion); 
			$sup = $limites['sup'];
			$inf = $limites['inf'];
			
			if ($variacion == 1){
				$inf .= "%";
				$sup .= "%";
			}
			
			$y += $alto_rec + $space_recs;
			$y_font = $y + $alto_rec;
			$color_conv = imagecolorallocate($image,$rgb[$case]["r"][$i],$rgb[$case]["g"][$i],$rgb[$case]["b"][$i]);
			imagefilledrectangle($image,$x,$y,$x+$alto_rec,$y+$alto_rec,$color_conv);
			
			$text = "$inf - $sup";
			imagettftext($image,$size_font_ttf,0,$x+$alto_rec+$space_text_rec,$y_font,$color_font,$font_ttf,$text);
		}
	}
	
	//Leyenda
	if ($map_ref == 0 && $drawLegend == 1){
		
		$max_letras = 55;
		
		//El texto no puede ser mas ancho de 300px aprx. 55 letras
		$legend_wrap = wordwrap($legend,$max_letras,"|");
		$wrap = 1;
		//$arr = imagettfbbox($size_font_ttf,0,$font_ttf,$legend);
		//$legend_w = $arr[2] - $arr[0];
		
		$legend_split = split("[|]",$legend_wrap);
		$num_filas_legend = count($legend_split); 	
			
		$num_filas_legend_total = $num_filas_legend + 2;
		
		$x_ini_legend = $x_ini + $ancho + 5;
		$alto_legend = $size_font_ttf_legend * $num_filas_legend_total + $space_recs * ($num_filas_legend_total + 1);
		$y_ini_legend = $height_img - $alto_legend - 5;
		
		//CALCULA EL ANCHO DE LA LEYENDA
		$w_legend = 0;
		$textos_legend = array($legend_split[0],$legend_fuente,$legend_periodo);
		foreach ($textos_legend as $texto){
			$arr = imagettfbbox($size_font_ttf,0,$font_ttf,$texto);
			$text_w = $arr[2] - $arr[0];	
			if ($text_w > $w_legend)	$w_legend = $text_w;
		}
		
		$ancho_legend = $w_legend + 10;
		
		imagefilledrectangle($image,$x_ini_legend,$y_ini_legend,$x_ini_legend + $ancho_legend,$y_ini_legend + $alto_legend,$color);
		imagerectangle($image,$x_ini_legend,$y_ini_legend,$x_ini_legend + $ancho_legend,$y_ini_legend + $alto_legend,$color_out);
		
		foreach($legend_split as $ff=>$legend_fila){
			imagettftext($image,$size_font_ttf_legend,0,$x_ini_legend + 5,$y_ini_legend + ($ff + 1) * ($space_recs + $size_font_ttf_legend),$color_font,$font_ttf_legend,$legend_fila);
		}
		
		imagettftext($image,$size_font_ttf_legend,0,$x_ini_legend + 5,$y_ini_legend + ($num_filas_legend + 1) * ($space_recs + $size_font_ttf_legend),$color_font,$font_ttf_legend,$legend_periodo);
		imagettftext($image,$size_font_ttf_legend,0,$x_ini_legend + 5,$y_ini_legend + ($num_filas_legend + 2)*($space_recs + $size_font_ttf_legend),$color_font,$font_ttf_legend,$legend_fuente);

	}
	
	$image = $mapserver->drawExtras($image);
	$s_id = session_id();
	$filename = $_SERVER['DOCUMENT_ROOT']."/tmp/$s_id.png";
	$_SESSION["mapserver_img"] = $filename;
	
	$header_html = "<table cellspacing='1' cellpadding='3' width='100%'><tr><td colspan='3'>UMAIC COLOMBIA - SIDI</td></tr><tr><td colspan=3>$legend - $legend_periodo - $legend_fuente</td></tr>";
	$footer_html = "</table>";
	
	
	if ($case != 'org')	$html = $xls;
	
	$css_numero = '<STYLE TYPE="text/css"><!-- .excel_celda_texto {mso-style-parent:text; mso-number-format:"\@";white-space:normal;} --></STYLE>';
	$_SESSION["xls"] = $css_numero.$header_html.$xls.$footer_html;
	$_SESSION["html_info_mapserver"] = $header_html.$html.$footer_html;
	
	//Guarda el archivo de la imagen con los extras para la opción de guardar mapa
	imagepng($image,$filename);
	
	//Salida al navegador
	imagepng($image);

	//CACHE
	if ($id_cache == 0 && in_array($case,array('desplazamiento','org','dato_sectorial','proyecto_undaf'))){

		$image_name = "$img_path_cache_tematico/$img_cache";
		imagepng($image,$image_name);
        
        // Guarda el map file para usar en otras apps, i.e. ERF, guarda IRSH
        if ($case == 'dato_sectorial' && $id_dato == 588){
            $mapObj->save($_SERVER['DOCUMENT_ROOT']."/sissh/wms/irsh.map");
        }
	}

}
else if ($map_ref == 1 && $case != 'perfil'){
	//Estilo para Deptos
	$classObj = ms_newClassObj($layerObj);
	$classObj->set("name","depto");
	//$classObj->setExpression("/.*/");
	$styleObj =  ms_newStyleObj($classObj);
	$styleObj->outlinecolor->setRGB(102,102,102);
	
	$classObj = ms_newClassObj($layerObjMpio);
	$classObj->set("name","blanco");
	//$classObj->setExpression("/.*/");
	$styleObj =  ms_newStyleObj($classObj);
	$styleObj->color->setRGB(255,255,255);
	$styleObj->outlinecolor->setRGB(204,204,204);
	
	$imageObj = $mapObj->draw();
	$imageObj->saveImage('');
	
}

else if ($map_ref == 0 && $case == 'perfil'){
	//Estilo para Deptos
	$classObj = ms_newClassObj($layerObj);
	$classObj->set("name","depto");
	//$classObj->setExpression("/.*/");
	$styleObj =  ms_newStyleObj($classObj);
	$styleObj->outlinecolor->setRGB(204,204,204);
	
	//Estilo para Mpios, 5 colores para diferenciar
	//Escala de colores para la plantilla de Desplazamieto
	$rgb = array("r" => array("255","255","0","204","153"),
				 "g" => array("153","204","204","153","153"),
				 "b" => array("51","0","255","255","255"));
	
	
	$i=0;

	$num_colors = count($rgb["r"]);
	foreach ($mpios as $id){
		$mpios_intervalos[$i][] = $id;
		$i++;

		if ($i == $num_colors)	$i = 0;
	}
	
	if ($num_mpios < $num_colors)	$num_colors = $num_mpios;
	
	for ($i=0;$i<$num_colors;$i++){

		$classObj = ms_newClassObj($layerObjMpio);
		$classObj->set("name","mpio$i");
		$classObj->setExpression("/".implode("|",$mpios_intervalos[$i])."/");
				
		$styleObj =  ms_newStyleObj($classObj);
		$styleObj->color->setRGB($rgb["r"][$i],$rgb["g"][$i],$rgb["b"][$i]);
		$styleObj->outlinecolor->setRGB(204,204,204);
		
//		$classObj->label->set("type",MS_TRUETYPE);
//		$classObj->label->set("font",$tag_font_label);
//		$classObj->label->set("size",$size_font_label);  //Tamaño en pixeles
//
//		$classObj->label->set("position",MS_CC);
//		$classObj->label->set("antialias",MS_TRUE);
//		$classObj->label->color->setRGB(0,0,0);
//		$classObj->label->outlinecolor->setRGB(255,255,255);
		
		
	}	

	//ZOOM
	$zoom_factor = 1;
	$mapObj->zoomPoint($zoom_factor,$clkpoint,$mapObj->width,$mapObj->height,$old_extent);
		
	//SCALEBAR
	$mapObj->scalebar->set("status",MS_EMBED);
	$mapObj->scalebar->color->setRGB(0,0,0);
	$mapObj->scalebar->set("position",MS_UL);
	$mapObj->scalebar->set("intervals",3);
	$mapObj->scalebar->set("width",150);
	$mapObj->scalebar->set("height",5);
	$mapObj->scalebar->set("units",MS_KILOMETERS);
	$mapObj->scalebar->label->set("type",MS_TRUETYPE);
	$mapObj->scalebar->label->set("font",$tag_font_label);
	$mapObj->scalebar->label->set("size",$size_font_label);
	$mapObj->scalebar->label->set("position",MS_UC);
	$mapObj->scalebar->label->color->setRGB(0,0,0);
	//$mapObj->scalebar->label->outlinecolor->setRGB(255,255,255);
	
	$imageObj = $mapObj->draw();
	$image_name = $imageObj->saveWebImage();
	$image_url = $image_name;
	
	$path_to_img = $_SERVER['DOCUMENT_ROOT'].$image_name;

	$size = getimagesize($path_to_img);
	$im_tmp = imagecreatefrompng($path_to_img);

	// Convert the Image to PNG-24
	$image = imagecreatetruecolor($size[0],$size[1]);
	//imageantialias($image,1);
	imagecopy($image,$im_tmp,0,0,0,0,$size[0],$size[1]);
	imagedestroy($im_tmp);

	$image = $mapserver->drawExtras($image);
	
	//Salida al navegador
	imagepng($image);
}

?>
