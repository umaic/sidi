<?php
session_start();

header("Content-Type: image/png");
header("Content-Disposition: attachment; filename=mapa_SIDIH-OCHA.png");

$image_name = $_SESSION["mapserver_img"];

$image = imagecreatefrompng($image_name);

imagePng($image);
?>
