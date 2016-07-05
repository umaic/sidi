<?
session_start();
set_time_limit(0);

//CHECK PARAMETERS
if (count($_GET) == 0)	die;

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
$mapserver = New Mapserver();
$use_cache = false;

// Ahora usando Factory pattern
$depto_dao = FactoryDAO::factory('depto');
$mpio_dao = FactoryDAO::factory('municipio');
$sissh = FactoryDAO::factory('sissh');
$proy_dao = FactoryDAO::factory('p4w');
$tema_dao = FactoryDAO::factory('tema');
$org_dao = FactoryDAO::factory('org');

$drawConv = 1;
$drawLegend = 1;
$arraySteps = array();
$una_org = 0;
$draw_cero = 0;   //Colcar convencion de cero - color blanco
$mdgd = 'deptal';
$print = (isset($_GET["print"])) ? $_GET["print"] : 0;
$id_filtro = (empty($_GET['id_filtro'])) ? 0 : $_GET["id_filtro"];
$filtro = (empty($_GET['filtro'])) ? '' : $_GET["filtro"];
$si_proy = $_GET['si'];
$un_proy = false;
//Filtro depto
$id_depto_filtro = isset($_GET["id_depto_filtro"]) ? $_GET["id_depto_filtro"] : 0;
$cond = "";
if ($id_depto_filtro > 0) {
    $mdgd = 'mpal';
	$cond = "id_depto = '$id_depto_filtro'";	
	$depto_filtro_vo = $depto_dao->Get($id_depto_filtro);
}

unset($_SESSION["id_tema_undaf"]);

if (in_array($filtro, array('ejecutora', 'donante', 'cobertura'))) {
    $sigla_org = $org_dao->GetFieldValue($id_filtro,'sig_org');
}
else if ($filtro == 'cluster' || $filtro == 'undaf') {
    $vo = $tema_dao->Get($id_filtro);
    //Graba el valor del tema para usarlo en el logo que se va a insertar en el mapa (mapserver.class.php->drawExtras())
    $_SESSION["id_tema_undaf"] = $id_filtro;
}

if (!empty($_GET["id_proy"])){
    $id_proy = $_GET["id_proy"];
    $un_proy = true;
    $caso = "1 Proy";
}

// path defaults
$map_path = $mapserver->map_path;
$img_path = $mapserver->img_path;
$font_path = $mapserver->font_path;

// Directorio cache mapas tematicos
$img_path_cache_tematico = $conf['mapserver']['dir_cache_tematico'];

	//$extent_get = $_GET['BBOX'];
	$extent_get = '-81.7227,-4.21785,-66.8755,13.3892';

    if (!empty($id_depto_filtro)) {
        $extent_get = $depto_filtro_vo->extent_dd;
    }

	$extent = explode(",", $extent_get);
	//$width_img = $_GET['WIDTH'];
	//$height_img = $_GET['HEIGHT'];
	$width_img = 800;
	$height_img = 816;
//LOG, si no es mapa de referencia y si no viene de web site
/*
if ($map_ref == 0 && isset($_SESSION["id_usuario_s"])){
	$log = FactoryDAO::factory('log');
	
	$_SESSION['m_e'] = "mapa";
	
	$caso = $case;
	if ($print == 1)	$caso .= "_print";
	
	$log->RegistrarFrontendMapa($id_depto_filtro,$caso);
}
*/


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
$max_extent->setextent(-81.5,-4.5,66.3,13.8);

$mapObj = ms_newMapObj("");
$mapObj->set("name","SIDIH Ocha Colombia");
$mapObj->imagecolor->setRGB(255,255,255);
$mapObj->setFontSet($font_path);
$mapObj->setSize($width_img,$height_img);
$mapObj->set("shapepath",$_SERVER["DOCUMENT_ROOT"]."/sissh/images/shapes/");
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
if ($mdgd == 'mpal' || $un_proy) {
    //NOMBRES MPIO
    $locs['dao'] = $mpio_dao;

    $layerObjMpio = ms_newLayerObj($mapObj);
    $layerObjMpio->set("name","mpios");
    $layerObjMpio->set("status",MS_ON);
    $layerObjMpio->set("type",MS_LAYER_POLYGON);
    $layerObjMpio->set("data","municipios_dd.shp");
    $layerObjMpio->set("classitem","id_espacia");
    //$layerObjMpio->set("transparency",MS_GD_ALPHA);
    $layerObjMpio->set("labelitem","nom_munici");
    
    $locs['mdgd'] = 0;
    $locs['layer'] = 'layerObjMpio';
}
else {
    $locs['dao'] = $depto_dao;
    $locs['mdgd'] = 1;
    $locs['layer'] = 'layerObj';

}
    
$vos = $locs['dao']->GetAllArray($cond);
foreach ($vos as $vo){
    $locs['ids'][] = $vo->id;
    $locs['nombre'][$vo->id] = $vo->nombre;
}
    
if (count($vos) > 1)	$drawConv = 1;

//DEPTOS
$layerObj = ms_newLayerObj($mapObj);
$layerObj->set("name","deptos");
$layerObj->set("status",MS_ON);
$layerObj->set("type",MS_LAYER_POLYGON);
//$layerObj->set("data","COLDPTO3.shp");
$layerObj->set("data","depto_dd.shp");
$layerObj->set("classitem","id_espacia");
$layerObj->set("labelitem","nombre_dpt");

if ($id_depto_filtro > 0){
    //Si es un depto, le coloca nombres a los vecinos
	$layerObj->set("labelitem","nombre_dpt");
	$layerObjMpio->set("filteritem","cod_depto");
	$layerObjMpio->setFilter($id_depto_filtro);
}

//Color para la outline de vecinos
$r_vecino = 50;
$g_vecino = 50;
$b_vecino = 50;

// Vecinos
/*
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

$classObj->label->set("type",MS_TRUETYPE);
$classObj->label->set("position",MS_UR);
$classObj->label->color->setRGB(0,0,0);
$classObj->label->outlinecolor->setRGB(255,255,255);
$classObj->label->set("antialias",MS_TRUE);
$classObj->label->set("font",$tag_font_label);
$classObj->label->set("size",$size_font_label);  //Tamaño en pixeles
$xls = "<tr><td><b>CODIGO</b></td><td><b>MUNICIPIO</b></td><td><b>Num. Proyectos</b></td></tr>";
$html = "<tr><td><b>CODIGO</b></td><td><b>MUNICIPIO</b></td><td><b>Num Proyectos</b></td><td></td></tr>";
*/


if (!$un_proy){
    
    // Cache
    //$id_cache = $sissh->opCacheMapaProy('get',$filtro,$id_filtro,$id_depto_filtro,$extent_get);
    $id_cache = 0;
    if ($id_cache == 0 || !$use_cache){
        //$sissh->opCacheMapaProy('insertar',$filtro,$id_filtro,$id_depto_filtro,$extent_get);

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
    
    //foreach ($mpios as $id_mpio){
    foreach ($locs['ids'] as $id){
        
        //$mpio_vo = $mpio_dao->Get($id_mpio);
        //$id_depto = $mpio_vo->id_depto;
        $nom_mpio = $locs['nombre'][$id];
        
        $valor = $proy_dao->numProyectos($filtro,$id_filtro,$locs['mdgd'],$id,'');
        $valores_mpio_mpio[$id] =  $valor;
        $valores_mpio[] =  $valor;
        //print_r($valores_mpio);
        //$xls .= "<tr><td class=\"excel_celda_texto\">$id</td><td>$nom_mpio</td><td>$valor</td></tr>";
        //$html .= "<tr><td>$id</td><td>$nom_mpio</td><td>$valor</td></tr>";
        
        //Check de minimo steps en arreglo de valores
        if (!in_array($valor,$arraySteps) && $valor > 1){
            $arraySteps[] = $valor;
        }
    }
    
    $legend = 'Cobertura Proyectos';
    $legend_periodo = ''; 
    $legend_fuente = ''; 
    
    if ($filtro == 'ejecutora' || $filtro == 'donante') {
        $legend_periodo = $sigla_org; 
    }
    else if ($filtro == 'cluster' || $filtro == 'undaf') {
        $legend_periodo = ucfirst($filtro); 
        $legend_fuente = $tema_dao->Get($id_filtro)->nombre; 
    }
    else if ($filtro == 'cobertura') {
        $legend_periodo = $sigla_org; 
    } 
    
    $tit_convencion = "Proyectos";
    
}
else{
    
    // Cache
    $id_cache = $sissh->opCacheMapaProy('get','un_proy',$id_proy,$id_depto_filtro,$extent_get);
    if ($id_cache == 0 || !$use_cache){
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
    foreach ($mpios['ids'] as $id_mpio){
        
        $nom_mpio = $mpio_dao->GetName($id_mpio);
        $valores_mpio_mpio[$id_mpio] =  1;
        $valores_mpio[] =  1;
        
        //$xls .= "<tr><td>$id_mpio</td><td>$nom_mpio</td><td>1</td></tr>";
        //$html .= "<tr><td>$id_mpio</td><td>$nom_mpio</td><td>1</td></tr>";
        
    }
    
    if (count($valores_mpio) > 0)	$arraySteps = array(0,1);
    
    $drawConv = 0;
    $tit_convencion = "Proyectos";
    $legend = "Presencia Proyectos";
    
    $nom = $proy_dao->GetName($id_proy);
    
    //Corta el nombre de la org, para que quede en 2 lineas
    if (strlen($nom) > 30){
        $nom_wrap = wordwrap($nom,30,'|');
        $nom_tmp = explode("|",$nom_wrap);
        
        $legend_periodo = $nom_tmp[0]; 
        $legend_fuente = $nom_tmp[1];
    }
    else{
        $legend_periodo = $nom; 
        $legend_fuente = "";
    }
}
    
//Check de minimo steps en arreglo de valores para hacer jenks y pintar el mapa
if (count($arraySteps) == 0){
    
    $img = imagecreatefrompng($_SERVER["DOCUMENT_ROOT"]."/sissh/images/mscross/error_mapa_info.png");
    imagePng($img);
    die();

}

sort($valores_mpio);
sort($arraySteps);

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
                
                $limites = $mapserver->getInfSup($valores_mpio,$kclass,$i-1,0); 
                $sup = $limites['sup'];
                $inf = $limites['inf'];
                
                if ($valor <= $sup && $valor >= $inf){
                    $id_mpio_matrix[$i][] = $id_mpio;
                    //echo "Valor=$valor----$id_mpio-----i=$i---min=$min<br>";
                    $i = $numclass + 1;
                }
            }
        }
        
    break;
}

//print_r($id_mpio_matrix);
    
$rgb = array("r" => array("226","170","42","0","1"),
            "g" => array("239","200","127","85","46"),
            "b" => array("249","226","255","212","179")
            );

//Coloca la paleta de colores de acuerdo al tema undaf
//Equidad = azul claro
/*
if ($_SESSION["id_tema_undaf"] == 1){
    $rgb = array("r" => array("226","170","114","57","1"),
                        "g" => array("239","200","160","121","81"),
                        "b" => array("249","226","204","181","158"));
}
//Social - azul oscuro
else if ($_SESSION["id_tema_undaf"] == 3){
    $rgb = array("r" => array("226","170","114","57","1"),
                        "g" => array("239","200","160","121","81"),
                        "b" => array("249","226","204","181","158"));
}
//Sostenible - rojo
else if ($_SESSION["id_tema_undaf"] == 2){
    $rgb = array("r" => array("255","248","241","233","226"),
                        "g" => array("213","160","107","53","0"),
                        "b" => array("218","170","122","74","26"));
}
//Paz - amarillo
else if ($_SESSION["id_tema_undaf"] == 4){
    $rgb = array("r" => array("255","255","255","254","254"),
                        "g" => array("243","233","224","214","204"),
                        "b" => array("192","144","96","48","0"));
}
*/
if ($mdgd == 'mpal') {
    //Estilo para Deptos
    $classObj = ms_newClassObj($layerObj);
    $classObj->set("name","depto");
    $classObj->setExpression("/.*/");
    $styleObj =  ms_newStyleObj($classObj);
    //$styleObj->color->setRGB(240,240,240);
    $styleObj->outlinecolor->setRGB(50,50,50);
    $styleObj->set("antialias",MS_TRUE);
    $styleObj->set('width', 0.1);
}
    
//Si filtro de depto, los otros se pintan blancos con outline
//
/*
if ($id_depto_filtro > 0){
    $classObj = ms_newClassObj($layerObj);
    $classObj->setExpression("/^$id_depto_filtro/");
    $styleObj =  ms_newStyleObj($classObj);
    $styleObj->outlinecolor->setRGB(102,102,102);
    $styleObj->set("antialias",MS_TRUE);
    $classObj->label->set("type",MS_TRUETYPE);
    $classObj->label->set("font",$tag_font_label);
    $classObj->label->set("size",$size_font_label);  //Tamaño en pixeles
    $classObj->label->set("position",MS_AUTO);
    $classObj->label->color->setRGB(0,0,0);
    $classObj->label->set("antialias",MS_TRUE);
}
*/
for($i=1;$i<($numclass+1);$i++){
    
    $j = ($un_proy) ? count($rgb['r'])-1 : $i - 1;
    //Estilo para Mpios
    if (count($id_mpio_matrix[$i]) > 0){
        //echo "Expresion = ".implode("|",$id_mpio_matrix[$i])."<br>";
        $classObj = ms_newClassObj($$locs['layer']);
        $classObj->set("name","intervalo$i");
        
        // Reemplaza 05 y 05, por 5 y 8, vienen sin ceros desde IGAC
        $classObj->setExpression("/".str_replace(array('05','08'), array('5','8',),implode("|",$id_mpio_matrix[$i])."/"));
        $styleObj = ms_newStyleObj($classObj);
        $styleObj->color->setRGB($rgb["r"][$j],$rgb["g"][$j],$rgb["b"][$j]);
        $styleObj->outlinecolor->setRGB(204,204,204);
        $styleObj->set('width', 0.2);
        $styleObj->set("antialias",MS_TRUE);
        
        //Solo se le coloca nombre a los 3 ultimos del intervalo
        if ($i >= ceil($numclass/2)){
            $classObj->label->set("type",MS_TRUETYPE);
            $classObj->label->set("font",$tag_font_label);
            //$classObj->label->set("size",$size_font_label);  //Tamaño en pixeles
            $classObj->label->set("size", 1);  //Tamaño en pixeles
            
            $classObj->label->set("position",MS_CR);
            $classObj->label->set("antialias",MS_TRUE);
            $classObj->label->color->setRGB(0,0,0);
            $classObj->label->outlinecolor->setRGB(255,255,255);
        }
    }
}

//Estilo para los deptos/municipios que no estan en ningun intervalo, blancos
$classObj = ms_newClassObj($$locs['layer']);
$classObj->set("name","blanco");
$classObj->setExpression("/.*/");
$styleObj =  ms_newStyleObj($classObj);
$styleObj->color->setRGB(255,255,255);
$styleObj->outlinecolor->setRGB(204,204,204);
$styleObj->set('width', 0.2);

//ZOOM
$zoom_factor = 1;
$mapObj->zoomPoint($zoom_factor,$clkpoint,$mapObj->width,$mapObj->height,$old_extent,$max_extent);

//SCALEBAR
$mapObj->scalebar->set("status",MS_EMBED);
$mapObj->scalebar->color->setRGB(0,0,0);
$mapObj->scalebar->set("position",MS_LR);
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
$x_ini = 80;
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

if ($drawConv == 1){

    $x = $x_ini;
    
    $alto_rec = $size_font_ttf_legend;  //Rectangulo pequeño que tiene el color
    $space_text_rec = 10;  //Espacio entre rectangulo y texto de cada intervalo. horizontal
    
    $y_title = $size_font_ttf_titulo + 8;
    
    $filas_legenda = ($draw_cero == 1) ?  $numclass + 1 : $numclass;
    
    $alto = 3*$space_recs + ($filas_legenda*$alto_rec) + $space_recs*($filas_legenda-1) + $y_title;   //Titulo + numero de intervalos + espacion entre rectangulos
    
    $y_ini = $height_img - $alto - 5;
    $y = $y_ini;
    
    //Calcula el texto mas largo de la convencion para colocarlo como ancho del rectangulo
    $arr = imagettfbbox($size_font_ttf,0,$font_ttf,strtoupper($tit_convencion));
    $w_er = $arr[2] - $arr[0];
    $h_er = $arr[1] - $arr[7];
    for($i=0;$i<$numclass;$i++){
        
        $limites = $mapserver->getInfSup($valores_mpio,$kclass,$i,0); 
        $sup = $limites['sup'];
        $inf = $limites['inf'];
        
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

    $x = 90;
    $y += $h_er;
    
    if ($draw_cero == 1){
        
        $y += $alto_rec + $space_recs;
        
        //Convencion para el 0
        imagefilledrectangle($image,$x,$y,$x+$alto_rec,$y+$alto_rec,$blanco);
        
        $text = "0";
        $y_font = $y + $alto_rec;
        imagettftext($image,$size_font_ttf,0,$x+$alto_rec+$space_text_rec,$y_font,$color_font,$font_ttf,$text);
    }
    
    //$y += $space_recs;
    for($i=0;$i<$numclass;$i++){
        
        $limites = $mapserver->getInfSup($valores_mpio,$kclass,$i,0); 
        $sup = $limites['sup'];
        $inf = $limites['inf'];

        $y += $alto_rec + $space_recs;
        $y_font = $y + $alto_rec;
        $color_conv = imagecolorallocate($image,$rgb["r"][$i],$rgb["g"][$i],$rgb["b"][$i]);
        imagefilledrectangle($image,$x,$y,$x+$alto_rec,$y+$alto_rec,$color_conv);
        
        $text = "$inf - $sup";
        imagettftext($image,$size_font_ttf,0,$x+$alto_rec+$space_text_rec,$y_font,$color_font,$font_ttf,$text);
    }
}

//Leyenda
if ($drawLegend == 1){
    
    $max_letras = 55;
    
    //El texto no puede ser mas ancho de 300px aprx. 55 letras
    $legend_wrap = wordwrap($legend,$max_letras,"|");
    $wrap = 1;
    //$arr = imagettfbbox($size_font_ttf,0,$font_ttf,$legend);
    //$legend_w = $arr[2] - $arr[0];
    
    $legend_split = explode("[|]",$legend_wrap);
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

//Salida al navegador
imagepng($image);

$image = $mapserver->drawExtras($image);
$s_id = session_id();
$filename = $_SERVER['DOCUMENT_ROOT']."/tmp/$s_id.png";
$_SESSION["mapserver_img"] = $filename;

//Guarda el archivo de la imagen con los extras para la opción de guardar mapa
imagepng($image,$filename);

/*
$header_html = "<table cellspacing='1' cellpadding='3' width='100%'><tr><td colspan='3'>OCHA COLOMBIA - SIDIH</td></tr><tr><td colspan=3>$legend - $legend_periodo - $legend_fuente</td></tr>";
$footer_html = "</table>";


$html = $xls;

$css_numero = '<STYLE TYPE="text/css"><!-- .excel_celda_texto {mso-style-parent:text; mso-number-format:"\@";white-space:normal;} --></STYLE>';
$_SESSION["xls"] = $css_numero.$header_html.$xls.$footer_html;
$_SESSION["html_info_mapserver"] = $header_html.$html.$footer_html;
*/



//CACHE
if ($id_cache == 0 || !$use_cache){

//    $image_name = "$img_path_cache_tematico/$img_cache";
//    imagepng($image,$image_name);
    
}

?>
