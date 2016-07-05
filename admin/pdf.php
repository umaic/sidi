<?php
include_once 'lib/libs_evento.php';
$dao = new EventoDAO();
$vo = $dao->Get(1);
include ('lib/common/class.ezpdf.php');
$pdf =& new Cezpdf();
$pdf->selectFont('lib/common/PDFfonts/Helvetica.afm');
$data = array(
array('num'=>1,'name'=>'gandalf','type'=>'wizard')
,array('num'=>2,'name'=>'bilbo','type'=>'hobbit','url'=>'http://www.ros.co.
nz/pdf/')
,array('num'=>3,'name'=>'frodo','type'=>'hobbit')
,array('num'=>4,'name'=>'saruman','type'=>'bad
dude','url'=>'http://sourceforge.net/projects/pdf-php')
,array('num'=>5,'name'=>'sauron','type'=>'really bad dude')
);
$pdf->ezTable($data);
$pdf->ezStream();
?>