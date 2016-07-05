<?php
$pathLib = '../admin/lib/';
$pathModel = $pathLib . 'model/';
$pathDao = $pathLib . 'dao/';

include_once($pathLib . "common/mysqldb.class.php");
include_once($pathLib . "common/cadena.class.php");

//MODEL
include_once($pathModel . "evento_c.class.php");
include_once($pathModel . "evento.class.php");
include_once($pathModel . "municipio.class.php");
include_once($pathModel . "depto.class.php");
include_once($pathModel . "actor.class.php");
include_once($pathModel . "cat_evento_c.class.php");
include_once($pathModel . "subcat_evento_c.class.php");
include_once($pathModel . "fuente_evento_c.class.php");
include_once($pathModel . "subfuente_evento_c.class.php");
include_once($pathModel . "edad.class.php");
include_once($pathModel . "rango_edad.class.php");
include_once($pathModel . "sexo.class.php");
include_once($pathModel . "condicion_mina.class.php");
include_once($pathModel . "subcondicion.class.php");
include_once($pathModel . "estado_mina.class.php");
include_once($pathModel . "etnia.class.php");
include_once($pathModel . "subetnia.class.php");
include_once($pathModel . "ocupacion.class.php");
include_once($pathModel . "dato_sectorial.class.php");

//DAO
include_once($pathDao . "evento_c.class.php");
include_once($pathDao . "evento.class.php");
include_once($pathDao . "municipio.class.php");
include_once($pathDao . "depto.class.php");
include_once($pathDao . "actor.class.php");
include_once($pathDao . "cat_evento_c.class.php");
include_once($pathDao . "subcat_evento_c.class.php");
include_once($pathDao . "fuente_evento_c.class.php");
include_once($pathDao . "subfuente_evento_c.class.php");
include_once($pathDao . "edad.class.php");
include_once($pathDao . "rango_edad.class.php");
include_once($pathDao . "sexo.class.php");
include_once($pathDao . "condicion_mina.class.php");
include_once($pathDao . "subcondicion.class.php");
include_once($pathDao . "estado_mina.class.php");
include_once($pathDao . "etnia.class.php");
include_once($pathDao . "subetnia.class.php");
include_once($pathDao . "ocupacion.class.php");
include_once($pathDao . "dato_sectorial.class.php");

// Replacing Microsoft Windows smart quotes, as sgaston demonstrated on 2006-02-13, I replace all other Microsoft Windows characters
function win_replace($str){
	$str = str_replace(chr(130), ',', $str);    // baseline single quote
	$str = str_replace(chr(131), 'NLG', $str);  // florin
	$str = str_replace(chr(132), '"', $str);    // baseline double quote
	$str = str_replace(chr(133), '...', $str);  // ellipsis
	$str = str_replace(chr(134), '**', $str);   // dagger (a second footnote)
	$str = str_replace(chr(135), '***', $str);  // double dagger (a third footnote)
	$str = str_replace(chr(136), '^', $str);    // circumflex accent
	$str = str_replace(chr(137), 'o/oo', $str); // permile
	$str = str_replace(chr(138), 'Sh', $str);   // S Hacek
	$str = str_replace(chr(139), '<', $str);    // left single guillemet
	$str = str_replace(chr(140), 'OE', $str);   // OE ligature
	$str = str_replace(chr(145), "'", $str);    // left single quote
	$str = str_replace(chr(146), "'", $str);    // right single quote
	$str = str_replace(chr(147), '"', $str);    // left double quote
	$str = str_replace(chr(148), '"', $str);    // right double quote
	$str = str_replace(chr(149), '-', $str);    // bullet
	$str = str_replace(chr(150), '-', $str);    // endash
	$str = str_replace(chr(151), '--', $str);   // emdash
	$str = str_replace(chr(152), '~', $str);    // tilde accent
	$str = str_replace(chr(153), '(TM)', $str); // trademark ligature
	$str = str_replace(chr(154), 'sh', $str);   // s Hacek
	$str = str_replace(chr(155), '>', $str);    // right single guillemet
	$str = str_replace(chr(156), 'oe', $str);   // oe ligature
	$str = str_replace(chr(159), 'Y', $str);    // Y Dieresis

	return $str;
}
?>
