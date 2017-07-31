<?
session_start();

header("Content-type: image/png");

//LIBRERIAS
include_once("../admin/lib/common/mysqldb.class.php");	
include_once("../admin/lib/common/postgresdb.class.php");	
include_once("../admin/lib/common/mapserver.class.php");

include_once("../admin/lib/dao/depto.class.php");
include_once("../admin/lib/dao/municipio.class.php");

include_once("../admin/lib/model/depto.class.php");
include_once("../admin/lib/model/municipio.class.php");

//CONF
include("../admin/config.php");


//INICIALIZACION DE VARIABLES
$mapserver = New Mapserver();
$depto_dao = New DeptoDAO();
$mpio_dao = New MunicipioDAO();

$case = $_GET["case"];
$id_depto_filtro = $_GET["id_depto_filtro"];
$op_depto = $_GET["op_depto"];

// path defaults
$map_path = $mapserver->map_path;
$img_path = $conf['mapserver']['dir_cache_consulta'];  //Para consulta se usa dir cache para guardar 
$font_path = $mapserver->font_path;

$image_name = $img_path.'/'.'consulta_'.$id_depto_filtro.'.png';

//Filtro depto
$cond = "";
if ($id_depto_filtro > 0){
	$cond = "id_depto = '$id_depto_filtro'";	
	//$filter_gis = "substr(codane2,0,3) = '$id_depto_filtro'";
	$depto_filtro_vo = $depto_dao->Get($id_depto_filtro);
	
	$image_name = ($op_depto == 1) ? $img_path.'/'.'depto_'.$id_depto_filtro.'.png' : $img_path.'/mpio_'.$id_depto_filtro.'.png';
}



//CACHE
if (file_exists($image_name)){
	$image = imagecreatefrompng($image_name);
}
else{
	//Estilo 5 colores para diferenciar
	$rgb = array("r" => array("255","255","0","204","153"),
				 "g" => array("153","204","204","153","153"),
				 "b" => array("51","0","255","255","255"));
	
	$num_colors = count($rgb["r"]);
	
	//Variable de extent que viene desde mscross
	if (isset($_GET['mapext'])) {              
		$extent = split(" ", $_GET['mapext']);
		$mapsize = split(" ", $_GET['mapsize']);
		$width_img = $mapsize[0];
		$height_img = $mapsize[1];
	}
	
	
	
	//NOMBRES DEPTOS
	$vos = $depto_dao->GetAllArray('');
	foreach ($vos as $vo){
		$deptos[] = $vo->id;
		$deptos_nombre[$vo->id] = $vo->nombre;
	}
	$num_deptos = count($deptos);
	
	//NOMBRES MPIO
	$vos = $mpio_dao->GetAllArray($cond);
	foreach ($vos as $vo){
		$mpios[] = $vo->id;
		$mpios_nombre[$vo->id] = $vo->nombre;
	}
	$num_mpios = count($mpios);
	
	$tag_font_label = "label";
	$size_font_label = 2;  //Tamaño en pixeles
	
	// Default click point
	$clickx = $width_img / 2;
	$clicky = ($height_img / 2);
	
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
	
	$outputformat = "png";
	//$outputformat = "imagemap";
	
	$mapObj->selectOutputFormat($outputformat);
	
	//PNG8
	$mapObj->outputformat->setOption("name","png");
	$mapObj->outputformat->setOption("DRIVER","GD/PNG");
	$mapObj->outputformat->setOption("EXTENSION","png");
	$mapObj->outputformat->setOption("IMAGEMODE",MS_IMAGEMODE_PC256);
	$mapObj->outputformat->setOption("MIMETYPE","image/png");
	
	// Set the map to the extent retrieved from the form
	$mapObj->setExtent($extent[0],$extent[1],$extent[2],$extent[3]);
	
	// Save this extent as a rectObj, we need it to zoom.
	
	$old_extent->setextent($extent[0],$extent[1],$extent[2],$extent[3]);
	
	$mapObj->web->set("imagepath",$img_path);
	$mapObj->web->set("imageurl","/tmp/");
	
	//MPIOS
	
	if ($id_depto_filtro > 0 && $op_depto == 0){
	
		$layerObjMpio = ms_newLayerObj($mapObj);
		$layerObjMpio->set("name","mpios");
		$layerObjMpio->set("status",MS_ON);
		$layerObjMpio->set("type",MS_LAYER_POLYGON);
		$layerObjMpio->set("data","colmun3corregido.shp");
		$layerObjMpio->set("classitem","codane2");
		
		
		//Elimina tildes
		$depto_nombre = strtoupper(strtr($depto_filtro_vo->nombre,"áéíóúñ","aeioun"));
		
		//Reemplaza Valle del Cauca por Valle
		if (strtolower($depto_filtro_vo->nombre) == 'valle del cauca'){
			$depto_nombre = "VALLE";
		}
	
		$layerObjMpio->set("filteritem","departamen");
		$layerObjMpio->setFilter($depto_nombre);
		$layerObjMpio->set("labelitem","municipio");
		
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
            $styleObj->set('width', 0.2);
			
			$classObj->label->set("type",MS_TRUETYPE);
			$classObj->label->set("font",$tag_font_label);
			$classObj->label->set("size",$size_font_label);  //Tamaño en pixeles
			
			$classObj->label->set("position",MS_CR);
			$classObj->label->set("antialias",MS_TRUE);
			$classObj->label->color->setRGB(0,0,0);
			$classObj->label->outlinecolor->setRGB(255,255,255);
			
			
		}		
	}
	
	//DEPTOS
	$layerObj = ms_newLayerObj($mapObj);
	$layerObj->set("name","deptos");
	$layerObj->set("status",MS_ON);
	
	$tipo_g = ($id_depto_filtro == 0 || $op_depto == 1) ? MS_LAYER_POLYGON : MS_LAYER_LINE;
	$layerObj->set("type",$tipo_g);
	
	$layerObj->set("data","COLDPTO3.shp");
	$layerObj->set("classitem","codane2");
	
	//Si es un depto, le coloca nombres a los vecinos
	if ($id_depto_filtro == 0){
		$layerObj->set("labelitem","departamen");
	}
	
	if ($id_depto_filtro == 0 || $op_depto == 0){
		$i=0;
		foreach ($deptos as $id){
			$deptos_intervalos[$i][] = $id;
			$i++;
		
			if ($i == $num_colors)	$i = 0;
		}
		
		if ($num_deptos < $num_colors)	$num_colors = $num_deptos;
		
		for ($i=0;$i<$num_colors;$i++){
		
			$classObj = ms_newClassObj($layerObj);
			$classObj->set("name","depto$i");
			$classObj->setExpression("/".implode("|",$deptos_intervalos[$i])."/");
					
			$styleObj =  ms_newStyleObj($classObj);
			$styleObj->color->setRGB($rgb["r"][$i],$rgb["g"][$i],$rgb["b"][$i]);
			$styleObj->outlinecolor->setRGB(204,204,204);
            $styleObj->set('width', 0.2);
			
			$classObj->label->set("type",MS_TRUETYPE);
			$classObj->label->set("font",$tag_font_label);
			$classObj->label->set("size",$size_font_label);  //Tamaño en pixeles
			
			$classObj->label->set("position",MS_CR);
			$classObj->label->set("antialias",MS_TRUE);
			$classObj->label->color->setRGB(0,0,0);
			$classObj->label->outlinecolor->setRGB(255,255,255);
			
			
		}
	}
	else{
			$classObj = ms_newClassObj($layerObj);
			$classObj->set("name","depto_fill");
			$classObj->setExpression("/".$id_depto_filtro."/");
					
			$styleObj =  ms_newStyleObj($classObj);
			$styleObj->color->setRGB($rgb["r"][0],$rgb["g"][0],$rgb["b"][0]);
			
			$classObj->label->set("type",MS_TRUETYPE);
			$classObj->label->set("font",$tag_font_label);
			$classObj->label->set("size",$size_font_label);  //Tamaño en pixeles
			
			$classObj->label->set("position",MS_CR);
			$classObj->label->set("antialias",MS_TRUE);
			$classObj->label->color->setRGB(0,0,0);
			$classObj->label->outlinecolor->setRGB(255,255,255);
	
			//Los demas deptos
			$classObj = ms_newClassObj($layerObj);
			$classObj->set("name","depto_line");
			$classObj->setExpression("('[codane2]' ne '$id_depto_filtro')");  
			
			$styleObj =  ms_newStyleObj($classObj);
			$styleObj->outlinecolor->setRGB(204,204,204);
            $styleObj->set('width', 0.2);
			
	}
	
	$imageObj = $mapObj->draw();
	//$image_name = $imageObj->saveWebImage();
	
	$imageObj->saveImage($image_name);
	
	//$path_to_img = $_SERVER['DOCUMENT_ROOT'].$image_name;
	$path_to_img = $image_name;
	
	$size = getimagesize($path_to_img);
	$im_tmp = imagecreatefrompng($path_to_img);
	
	// Convert the Image to PNG-24
	$image = imagecreatetruecolor($size[0],$size[1]);
	//imageantialias($image,1);
	imagecopy($image,$im_tmp,0,0,0,0,$size[0],$size[1]);
	imagedestroy($im_tmp);
	
	//$image = $mapserver->drawExtras($image);
}
//Salida al navegador
imagepng($image);

?>
