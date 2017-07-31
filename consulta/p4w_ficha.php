<?
require_once('admin/lib/common/tcpdf/config/lang/eng.php');
require_once('admin/lib/common/tcpdf/tcpdf.php');
require_once('admin/lib/model/depto.class.php');
require_once('admin/lib/dao/depto.class.php');

$conn = $this->conn;
$font_name = 'Helvetica';

function resize($imagen,$max_w=70,$max_h=45){
	
	/*
	$max_w = 70;  //mm
	$max_h = 45;  //mm
	*/

	$size = getimagesize($imagen);
	$w = $size[0];
	$h = $size[1];

	//Horizontal
	if ($w > $h){
		$factor = $max_w / $w;
		$resize['w'] = $max_w;
		$resize['h'] = $factor * $h;
	}
	else{
		$factor = $max_h / $h;
		$resize['w'] = $factor * $w;
		$resize['h'] = $max_h;
	}

	return $resize;
}

function addRecHeader($pdf,$ubi){

	$rgb = array('azul_o' => array(0,102,255),
			 'azul_c' => array(0,103,255)	
			);

	$font_size = 18;
	$font_name = 'helvetica';
	$padding = 5;

	//Rectangulo superior derecho
	$w_r = $pdf->GetStringWidth($ubi,$font_name,'B',$font_size) + ($padding * 2);
	$h_r = 14;
	$x_r = $pdf->getPageWidth() - $w_r;
	$y_r = 0;

	$pdf->Rect($x_r,$y_r,$w_r,$h_r,'F',array(),$rgb['azul_o']);
	$pdf->SetFont($font_name, 'B', $font_size);
	$pdf->SetTextColor(255,255,255);
	$pdf->Text($x_r + $padding, ($y_r + $h_r)/2 + 2, $ubi);
}

function check($imagenes,$path_images){
	$rtn = 0;
	foreach ($imagenes as $img){
		$img_f = $path_images.'/w_'.$img.'.png';
		
		if (file_exists($img_f))	$rtn = 1;
	}

	return $rtn;
}

// extend TCPF with custom functions
class MYPDF extends TCPDF {
    
    // Colored table
    public function ColoredTable($header,$data,$w) {
        // Colors, line width and bold font
        $this->SetFillColor(0,152,255);
        $this->SetTextColor(255);
        $this->SetDrawColor(204,204,204);
        $this->SetLineWidth(0.1);
        $this->SetFont('', 'B');
		
		$cell_h = 3;
        
		// Header
		$this->SetFont('', '', 7);
        for($i = 0; $i < count($header); $i++){
        	$this->Cell($w[$i], $cell_h, $header[$i], 1, 0, 'C', 1);
		}

        $this->Ln();
        
		// Color and font restoration
        $this->SetFillColor(224, 235, 255);
        $this->SetTextColor(0);
        $this->SetFont('','',6);

		//Este padding es horizontal
		$this->SetCellPadding(1);

        // Data
        $fill = 0;
		foreach($data as $row) {

            // Alto igual para todas las celdas para q no se monten las de 
            // abajo con las de arriba
            $nl = max(array($this->getNumLines($row[0], $w[0]), $this->getNumLines($row[2], $w[2])));
            $ch = $cell_h*$nl;

            $ch += 3;
            
            foreach($row as $v=>$valor){
                if ($v == 0)	$valor = $this->UTF8ToLatin1($valor);
                

            	//$this->Cell($w[$v], $cell_h*$numlines, $valor, 'LR', 0, $centrado[$v], $fill);
            	$this->MultiCell($w[$v], $ch, $valor, 'LR', 'L', $fill, 0);
			}
            
			$this->Ln();
            $fill=!$fill;
        }

		//Espacio en blanco al final de la tabla
		$this->Cell(array_sum($w), 0, '','T',0); 
    }
}


//Nacional
if (!$fdepto){
	$id_ubicacion = '00';
	$dato_para = 3;
	$sigla_ubi = 'Nal';
	$subtitulo = '4W PERFIL NACIONAL';
	$ubicacion_titulo = 'Colombia';
}
else if ($fdepto && !$fmun){
    $depto_dao = new DeptoDAO();
	$id_ubicacion = $id_depto;
	$ubicacion = $depto_dao->Get($id_ubicacion);
	$dato_para = 1;
	$sigla_ubi = 'Nal';
    $subtitulo = '4W PERFIL DEPARTAMENTAL';
	$ubicacion_titulo = $ubicacion->nombre;
}
else {
    $id_ubicacion = $id_mpio;
    $ubicacion = $mun_dao->Get($id_ubicacion);
    $dato_para = 2;
    $depto_ubicacion = $depto_dao->Get($ubicacion->id_depto);
    $sigla_ubi = 'Deptal';
    $subtitulo = 'PERFIL MUNICIPAL';
	$ubicacion_titulo = $ubicacion->nombre;
}


// create new PDF document
$pdf = new MYPDF('L', PDF_UNIT, PDF_PAGE_FORMAT, false, 'UTF-8', false);

// set document information
$pdf->SetCreator('UMAIC SIDI');
$pdf->SetAuthor('UMAIC Colombia');
$pdf->SetTitle(ucfirst(strtolower($subtitulo)).' '.$ubicacion_titulo);

// set default header data
$pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, 'OCHA COLOMBIA', $subtitulo);

//Footer
$hoy = getdate();
$meses = array('','Enero','Febrero','Marzo','Abril','Mayo','Junio','Julio','Agosto','Septiembre','Octubre','Noviembre','Diciembre');
$aaaa = $hoy['year'];
$dd = $hoy['mday'];
$mes = $meses[$hoy['mon']];

$pdf->SetFooterData(utf8_decode("La información aquí dispuesta no refleja posición alguna de UMAIC. Este perfil fué generado por SIDI, el día $dd de $mes de $aaaa. Mayor detalle: https://umaic.org/sidi"));

// set header and footer fonts
$pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
$pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));

// set default monospaced font
$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

//$pdf->setPrintHeader(false);
//$pdf->setPrintFooter(false);

//set margins
$margin_top = 20;
$margin_bottom = 20;
$margin_left = 5;
$margin_right = 5;
$pdf->SetMargins($margin_left, $margin_top, $margin_right);

$pdf_margin_header = 5;
$pdf_margin_footer = 10;

$pdf->SetHeaderMargin($pdf_margin_header);
$pdf->SetFooterMargin($pdf_margin_footer);

//set auto page breaks
$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

// ---------------------------------------------------------
// add a page

//Mapa inicial si no es nacional
//Dimension Mapa
/*
$size = getimagesize($_SESSION["mapserver_img"]);

$pdf->AddPage();
addRecHeader($pdf,$ubicacion_titulo);

$height_mapa = $pdf->getPageHeight() - ($margin_top + $margin_bottom);
$width_mapa = $size[0]*$height_mapa/$size[1];

$x_mapa = ($pdf->getPageWidth() - $width_mapa) / 2;
$y_mapa = $margin_top;
$pdf->Image($_SESSION["mapserver_img"], $x_mapa , $y_mapa,'', $height_mapa, '', '', '', true); 


//Titulo pagina 0 - Mapa
$font_size = 24;
$title_pag_1 = 'Mapa de Proyectos Humanitarios';
$pdf->SetFont($font_name, 'B', $font_size);

//Calcula el x para el texto
$x_title = ($pdf->getPageWidth() / 2) - ($pdf->GetStringWidth($title_pag_1,'','B') / 2);
$pdf->SetTextColor(0,0,0);
$pdf->Text($x_title, 12, $title_pag_1); 

*/

// PAGINA 1
$pdf->AddPage();
addRecHeader($pdf,$ubicacion_titulo);

//Titulo pagina 1 - Proyectos por cluster
$font_size = 24;
$title_pag_1 = 'Proyectos por Cluster';
$pdf->SetFont($font_name, 'B', $font_size);

//Calcula el x para el texto
$x_title = ($pdf->getPageWidth() / 2) - ($pdf->GetStringWidth($title_pag_1,'','B') / 2);
$pdf->SetTextColor(0,0,0);
$pdf->Text($x_title, 15, $title_pag_1); 

//Column titles
$header = array('Proyecto', 'Ejecutor', 'Estado', 'Cobertura');
$num_cols = count($header);
// Anchos


$w_col =  ($fdepto) ? array(100,80,10,80) : array(160,80,10,20);

//Definimos el tipo de letra de la tabla, para poder calcular los anchos de las columnas con esa fuente
$pdf->SetFont('','',6);


$fila = 0;
$data = array();
foreach ($sectores as $id_s => $sect){
    $_t = 0;
	foreach ($pryspdf[$id_s] as $_p){

        $_n = $_p['n'];
        $_e = $_p['e'];
        $_est = $_p['est'];
        $_cb = $_p['cb'];

        //$data[$fila][] = array(utf8_decode($_p['n']), utf8_decode($_p['e']), utf8_decode($_p['cb']));
        $data[$fila][] = array($_p['n'], $_p['e'], $_p['est'], $_p['cb']);

        if ($pdf->GetStringWidth($_p['e']) > $w_col[1]){
			//$w_col[1] = $pdf->GetStringWidth($_p['e']); 
		}

        $_t++;
    }

    $total[$id_s] = $_t;

	$fila++;
}

// Ajustamos el padding derecho al ancho de cada columna
$cellpadding = 3;
$tbl_w = 0;
for ($col=0;$col<$num_cols;$col++){
    $w_col[$col] += $cellpadding;
    $tbl_w += $w_col[$col];
}

// Colocamos el color de la fuente para las tablas
$pdf->SetTextColor(0,0,0);

$y_0 = $pdf->GetY() + 5;
$y_1 = $y_0;

$fila = 0;
$im = 0;
foreach ($sectores as $id_s => $sect){

	$pdf->SetFont($font_name, 'B', 12);
    $pdf->SetCellPadding(0);
    $s_w = $pdf->GetStringWidth($sect);
	$pdf->Bookmark($sect, 0);
    $pdf->Cell($s_w, 4, $sect, 0, 0, 'L', 0);
    $txt = $total[$id_s].' Proyectos';
	$pdf->Cell($tbl_w - $s_w, 4, $txt, 0, 1, 'R', 0);

	for ($col=0;$col<$num_cols;$col++){
		$width_cols[$fila][$col] = $w_col[$col];
	}
	
	// print colored table
	$pdf->ColoredTable($header,$data[$fila],$width_cols[$fila]);
	$pdf->Ln();

	$fila++;
}

// ---------------------------------------------------------

//Close and output PDF document
$_n = '/sissh/perfiles';
$_f = '/4w_ficha_'.$id_ubicacion;
$dir_cache_perfil = $_SERVER["DOCUMENT_ROOT"].$_n;
$path_file = $dir_cache_perfil.$_f;
$cache_file = "$path_file.pdf";
$_SESSION['4w_ficha'] = $cache_file;
$_SESSION['4w_ficha_url'] = $_n.$_f.'.pdf';
$pdf->Output($cache_file, 'F');  //Crea archivo fisico
//$pdf->Output('Ficha_4W_'.$ubicacion_titulo.'.pdf', 'I');

//============================================================+
// END OF FILE                                                 
//============================================================+

?>
