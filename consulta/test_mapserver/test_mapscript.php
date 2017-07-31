 <?php

    //dl('php_mapscript.so');
ms_ResetErrorList();
    $map_path="/srv/www/htdocs/sissh/consulta/test_mapserver/";
    $map = ms_newMapObj($map_path."first.map");
	$map->set("name","SIDI UMAIC Colombia");

	$outputformat = "png";
	
	$map->selectOutputFormat($outputformat);
$extent = array(-161112, -462196, 1653895, 1386463);

$map->setExtent($extent[0],$extent[1],$extent[2],$extent[3]);

	//PNG8
	//$map->outputformat->setOption("name","png");
	//$map->outputformat->validate();
/*
	$map->outputformat->setOption("DRIVER","AGG/PNG");
	$map->outputformat->setOption("EXTENSION","png");
	$map->outputformat->setOption("IMAGEMODE",MS_IMAGEMODE_PC256);
	$map->outputformat->setOption("MIMETYPE","image/png");
*/
	$map->imagecolor->setRGB(255,255,255);
//	$map->outputformat->setOption("name","png");

    $image=$map->draw();
    $image_url=$image->saveWebImage();
$error = ms_GetErrorObj();
while($error && $error->code != MS_NOERR)
{
  printf("Error in %s: %s<br>\n", $error->routine, $error->message);
  $error = $error->next();
}
    ?>

     <HTML>
      <HEAD>
          <TITLE>Example 1: Displaying a map</TITLE>
      </HEAD>
      <BODY>
          <IMG SRC=<?php echo $image_url; ?> >
      </BODY>
     </HTML>
