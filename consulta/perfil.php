<?
require_once('admin/lib/common/tcpdf/config/lang/eng.php');
require_once('admin/lib/common/tcpdf/tcpdf.php');
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
		$centrado = (count($data[0]) == 4) ? array('L','R','L','C') : array('L','R','R','L','C');
		foreach($data as $row) {
			foreach($row as $v=>$valor){
				if ($v == 0)	$valor = $this->UTF8ToLatin1($valor);
            	$this->Cell($w[$v], $cell_h, $valor, 'LR', 0, $centrado[$v], $fill);
			}
            
			$this->Ln();
            $fill=!$fill;
        }

		//Espacio en blanco al final de la tabla
		$this->Cell(array_sum($w), 0, '','T',0); 
    }
}


//Nacional
if ($id_depto == 0 && $id_mpio == 0){
	$id_ubicacion = '00';
	$dato_para = 3;
	$sigla_ubi = 'Nal';
	$subtitulo = 'PERFIL NACIONAL';
	$ubicacion_titulo = 'Colombia';
}
else{
	$id_ubicacion = $id_depto;
	$ubicacion = $depto_dao->Get($id_ubicacion);
	$dato_para = 1;
	$sigla_ubi = 'Nal';
	$subtitulo = 'PERFIL DEPARTAMENTAL';
	if ($id_mpio != 0){
		$id_ubicacion = $id_mpio;
		$ubicacion = $mun_dao->Get($id_ubicacion);
		$dato_para = 2;
		$depto_ubicacion = $depto_dao->Get($ubicacion->id_depto);
		$sigla_ubi = 'Deptal';
		$subtitulo = 'PERFIL MUNICIPAL';
	}
	
	$ubicacion_titulo = $ubicacion->nombre;
}


// create new PDF document
$pdf = new MYPDF('L', PDF_UNIT, PDF_PAGE_FORMAT, false, 'UTF-8', false);

// set document information
$pdf->SetCreator('SIDI UMAIC');
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
if ($dato_para != 3){
	//Dimension Mapa
	$size = getimagesize("images/minificha/$mapa_inicial");

	$pdf->AddPage();
	addRecHeader($pdf,$ubicacion_titulo);

	$height_mapa = $pdf->getPageHeight() - ($margin_top + $margin_bottom);
	$width_mapa = $size[0]*$height_mapa/$size[1];

	$x_mapa = ($pdf->getPageWidth() - $width_mapa) / 2;
	$y_mapa = $margin_top;
	$pdf->Image("images/minificha/$mapa_inicial", $x_mapa , $y_mapa,'', $height_mapa, '', '', '', true); 
}


$pdf->AddPage();
addRecHeader($pdf,$ubicacion_titulo);

//Titulo pagina 1 - Datos generales
$font_size = 24;
$title_pag_1 = 'Datos Generales';
$pdf->SetFont($font_name, 'B', $font_size);

//Calcula el x para el texto
$x_title = ($pdf->getPageWidth() / 2) - ($pdf->GetStringWidth($title_pag_1,'','B') / 2);
$pdf->SetTextColor(0,0,0);
$pdf->Text($x_title, 15, $title_pag_1); 

//Column titles
$header = ($dato_para == 3) ? array('Indicador', 'Valor', 'Fuente', utf8_decode('Año')) : array('Indicador', 'Valor', 'Valor '.$sigla_ubi, 'Fuente', utf8_decode('Año'));
$num_cols = count($header);

//Definimos el tipo de letra de la tabla, para poder calcular los anchos de las columnas con esa fuente
$pdf->SetFont('','',6);

//Anchos iniciales de las columnas
for ($col=0;$col<$num_cols;$col++){
	$w_col[$col] = $pdf->GetStringWidth($header[$col],'','B');
}
$fila = 0;
$data = array();
foreach ($id_datos_resumen as $categoria => $datos_m){

	foreach ($datos_m as $id_dato){

		$dato = $d_s_dao->Get($id_dato);
		$fuente = $fuente_dao->Get($dato->id_contacto);

		//CONSULTA EL PERIODO MAS RECIENTE DEL VALOR DEL DATO
		$fecha_val = $d_s_dao->GetMaxFecha($id_dato);
		$a = split("-",$fecha_val["fin"]);

		//VALOR DATO NACIONAL
		$val = $d_s_dao->GetValorToReport($id_dato,0,$fecha_val['ini'],$fecha_val['fin'],3);
		$valor_nacional = $val['valor'];
		$id_unidad = $val['id_unidad'];
		
		//APLICA FORMATO
		$valor_nacional = $d_s_dao->formatValor($id_unidad,$valor_nacional);


		if ($dato_para == 3){
			$data[$fila][] = array($dato->nombre,$valor_nacional,$fuente->nombre,$a[0]);
		}
		else{
			//VALOR DATO
			$val = $d_s_dao->GetValorToReport($id_dato,$id_ubicacion,$fecha_val['ini'],$fecha_val['fin'],$dato_para);
			$valor = $val['valor'];
			$id_unidad = $val['id_unidad'];
			//APLICA FORMATO
			$valor = $d_s_dao->formatValor($id_unidad,$valor);
			
			if ($dato_para == 2){
				$val = $d_s_dao->GetValorToReport($id_dato,$depto_ubicacion->id,$fecha_val['ini'],$fecha_val['fin'],1);
				$valor_nacional = $val['valor'];
				$id_unidad = $val['id_unidad'];

				//APLICA FORMATO
				$valor_nacional = $d_s_dao->formatValor($id_unidad,$valor_nacional); 
				// El nombre de la variable es $valor_nacional, pero realmente es un valor departamental,
				// es para no tener que crear un caso extra en la siguiente linea
			}
			
			$data[$fila][] = array($dato->nombre,$valor,$valor_nacional,$fuente->nombre,$a[0]);
		}

		//Calculamos los anchos de los textos para cada categoria, para hacer las tablas lo mas ajustadas posibles
		//Indicador
		if ($pdf->GetStringWidth($dato->nombre) > $w_col[0]){
			$w_col[0] = $pdf->GetStringWidth($dato->nombre); 
		}

		//Valor
		if ($dato_para == 3){
			if ($pdf->GetStringWidth($valor_nacional) > $w_col[1]) $w_col[1] = $pdf->GetStringWidth($valor_nacional); 
			$next_col = 2;
		}
		else{
			if ($pdf->GetStringWidth($valor) > $w_col[1]) $w_col[1] = $pdf->GetStringWidth($valor); 
			if ($pdf->GetStringWidth($valor_nacional) > $w_col[2]) $w_col[2] = $pdf->GetStringWidth($valor_nacional); 
			$next_col = 3;
		}

		//Fuente
		if ($pdf->GetStringWidth($fuente->nombre) > $w_col[$next_col]) $w_col[$next_col] = $pdf->GetStringWidth($fuente->nombre); 

	}

	$fila++;
}

// Ancho para año
$width_cols[$fila][$num_cols-1] = 10;

// Ajustamos el padding derecho al ancho de cada columna
$cellpadding = 3;
for ($col=0;$col<$num_cols;$col++){
	$w_col[$col] += $cellpadding;
}

// Numero de imagenes en columnas
$num_col_images = 2;

// Colocamos el color de la fuente para las tablas
$pdf->SetTextColor(0,0,0);

$y_0 = $pdf->GetY() + 5;
$y_1 = $y_0;

$imagenes = array('2_11','1_3','1_10','1_1','2_12','2_4');
$images_space = 2;  //Espacio horizontal entre dos imagenes

$fila = 0;
$im = 0;
foreach ($id_datos_resumen as $categoria => $datos_m){

	$cat = $cat_dao->Get($categoria);

	$pdf->SetFont($font_name, 'B', 12);
	$pdf->SetCellPadding(0);
	$pdf->Cell(50, 4, $cat->nombre,0,2,'L',0);

	for ($col=0;$col<$num_cols;$col++){
		$width_cols[$fila][$col] = $w_col[$col];
	}
	
	// print colored table
	$pdf->ColoredTable($header,$data[$fila],$width_cols[$fila]);
	$pdf->Ln(2);

	if (fmod($fila,2) == 0 && $im < 6){
		$x_img_0 = array_sum($width_cols[$fila]) + 10;

		$img = $path_images.'/w_'.$imagenes[$im].'.png';
		if (file_exists($img)){
			$resize = resize($img);
			$w_0 = $resize['w'];
			$h_0 = $resize['h'];
			$pdf->Image($img, $x_img_0 , $y_0, $w_0, $h_0, '', '', '', true); 

			$y_0 += $resize['h'] + 2;
		}
		
		$img = $path_images.'/w_'.$imagenes[$im+1].'.png';
		if (file_exists($img)){
			$resize = resize($img);
			$w_1 = $resize['w'];
			$h_1 = $resize['h'];
			$pdf->Image($img, $x_img_0 + $w_0 + $images_space , $y_1,$w_1 , $h_1, '', '', '',true); 
			
			$y_1 += $resize['h'] + 2;
		}
		$im += 2;
	}
	$fila++;
}

//Pagina 2 - Desplazamiento, Mina , IRSH
$pdf->AddPage();
addRecHeader($pdf,$ubicacion_titulo);

// Y para colocar el titulo grande de las gráficas
$y_ini_titulo = ($dato_para == 3) ? 22 : 22;

// Alto del titulo grande de las graficas
$alto_titulo = ($dato_para == 3) ? 5 : 5;

// DESPLAZAMIENTO
$font_size = 20;
$title = $perfil_titulo_desplazamiento;
$pdf->SetFont($font_name, 'B', $font_size);

// Calcula el x para el texto
$pdf->SetTextColor(0,0,0);
$pdf->Text(30, $y_ini_titulo, $title); 

$pdf->SetFont($font_name, 'I', 10);
$pdf->Text($margin_left, $y_ini_titulo + $alto_titulo, $perfil_fuente_desplazamiento); 

//$imagenes = array('3_1','3_3','3_2','3_4');
$imagenes = array('3_1','3_2','3_3','3_4');
$y_0 = $pdf->GetY() + $alto_titulo * 2;
//$y_1 = $y_0;
$x_img_0 = $margin_left;

$show = 0;
$y_img = $y_0;
$h_max = 0;
for ($im=0;$im<count($imagenes);$im++){

		$img = $path_images.'/w_'.$imagenes[$im].'.png';
		
		// Nueva fila
		if ($show >= $num_col_images){
			$y_img = $y_tmp[$show - $num_col_images] + $images_space + $y_0;
		}

		// Nueva columna
		$x_img = (fmod($show,$num_col_images) == 0) ? $x_img_0 : $x_img_0 + $w + $images_space;
		
		$resize = resize($img);
		$w = $resize['w'];
        $h = $resize['h'];
        if ($h > $h_max ) {
            $h_max = $h;
        }
		$pdf->Image($img, $x_img , $y_img, $w, $h, '', '', '', true); 
		
		$y_tmp[$show] = $h;
		
		$show++;
}

$w_desplazamiento = $w_0 + $w_1;
$y_irsh = ($dato_para == 3) ? $y_img + $h + 10 : $y_ini_titulo;
$y_desplazamiento = $y_img + $h_max;
$y_mina = $y_ini_titulo;

if ($dato_para != 3){
	//Desplazamiento ACUMULADO
	$font_size = 20;
	$title = str_replace("Desplazamiento","Desplazamiento Acumulado",$perfil_titulo_desplazamiento);
	$pdf->SetFont($font_name, 'B', $font_size);
	$x_img_0 = $margin_left + $w_desplazamiento + $images_space*2;

	$pdf->SetTextColor(0,0,0);
	$pdf->Text($x_img_0 + 2, $y_ini_titulo, $title); 

	$pdf->SetFont($font_name, 'I', 10);
	$pdf->Text($x_img_0 + 5, $y_ini_titulo + $alto_titulo, $perfil_fuente_desplazamiento); 

	$imagenes = array('4_1','4_3','4_2','4_4');
	$y_0 = $y_ini_titulo + $alto_titulo * 2;
	//$y_1 = $y_0;
	$x_img_0 = $margin_left + $w_desplazamiento + $images_space*2;

	$show = 0;
    $y_img = $y_0;
    $h_max = 0;
	for ($im=0;$im<count($imagenes);$im++){

			$img = $path_images.'/w_'.$imagenes[$im].'.png';
			
			// Nueva fila
			if ($show >= $num_col_images){
				$y_img = $y_tmp[$show - $num_col_images] + $images_space + $y_0;
			}

			// Nueva columna
			$x_img = (fmod($show,$num_col_images) == 0) ? $x_img_0 : $x_img_0 + $w + $images_space;
			
			$resize = resize($img);
			$w = $resize['w'];
            $h = $resize['h'];
            if ($h > $h_max) {
                $h_max = $h;
            } 
			$pdf->Image($img, $x_img , $y_img, $w, $h, '', '', '', true); 
			
			$y_tmp[$show] = $h;
			
			$show++;
	}

	// Orgs
	$imagenes = array('6_1','6_4','6_2','6_3');
	if (check($imagenes,$path_images) == 1){
		$espacio = 10;
		$y_0 = (($y_img + $h_max) > $y_desplazamiento) ? ($y_img + $h_max) : $y_desplazamiento;
		
		// Si queda poco espacio para orgs, debe hacer un mayor resize
		$w_resize = 70;
		$h_resize = 45;
		if ($y_0 >= 144){
			$w_resize = 60;
			$h_resize = 35;
		}

		$y_0 += $espacio;
		$font_size = 20;
		$title = 'Organizaciones';
		$pdf->SetFont($font_name, 'B', $font_size);

		//Calcula el x para el texto
		$pdf->SetTextColor(0,0,0);
		$pdf->Text(130, $y_0, $title); 

		$y_0 += $alto_titulo;
		$y_1 = $y_0;
		$x_0 = $margin_left;

		for ($im=0;$im<count($imagenes);$im++){
			
			$img = $path_images.'/w_'.$imagenes[$im].'.png';
			if (file_exists($img)){
				$resize = resize($img,$w_resize,$h_resize);
				$w_0 = $resize['w'];
				$h_0 = $resize['h'];
				$pdf->Image($img, $x_0 , $y_0, $w_0, $h_0, '', '', '', true); 

				$x_0 += $w_0 + $images_space;
			}
		}
	}

	// Nueva pag
	$pdf->AddPage();
	addRecHeader($pdf,$ubicacion_titulo);
}

// MINA
$imagenes = ($dato_para == 3) ? array('5_1','5_3') : array('5_1','5_2','5_3','5_4');

//Check si existe alguna imagen para mostrar el titulo
if (check($imagenes,$path_images) == 1){
	$pdf->SetY($y_mina);
	$font_size = 20;
	$title = $perfil_titulo_mina;
	$pdf->SetFont($font_name, 'B', $font_size);
	$x_titulo_mina = ($dato_para == 3) ? 165 : 165;

	// Titulo
	$pdf->SetTextColor(0,0,0);
	$pdf->Text($x_titulo_mina, $y_mina, $title); 
	$w_perfil_fuente_mina = $pdf->GetStringWidth($title) + 5;

	// Fuente
	$pdf->SetFont($font_name, 'I', 12);
	$pdf->Text($x_titulo_mina + 5, $y_mina + $alto_titulo, $perfil_fuente_mina);

	$y_ini = $pdf->GetY() + $alto_titulo * 2;
	$y_0 = $y_ini;
	$y_1 = $y_0;
	$x_img_0 = $margin_left + $w_desplazamiento + $images_space*2;
	$y_img = $y_0;
	$x_img = $x_img_0;
	$show = 0;
	for ($im=0;$im<count($imagenes);$im++){
		
		$img = $path_images.'/w_'.$imagenes[$im].'.png';
		
		if (file_exists($img)){
			
			$resize = resize($img);
			$w = $resize['w'];
			$h = $resize['h'];
			
			// Nueva fila
			if ($show >= $num_col_images){
				$y_img = $y_tmp[$show - $num_col_images] + $images_space + $y_ini;
			}

			// Nueva columna
			if (fmod($show,$num_col_images) == 0){
				$x_img = $x_img_0;
				$y_1 += $h;
			}
			else{
				$x_img = $x_img_0 + $w + $images_space;
				$y_0 += $h;
			}
			
			$pdf->Image($img, $x_img , $y_img, $w, $h, '', '', '', true); 
			
			$y_tmp[$show] = $h;
			
			$show++;

		}
	}

	$y_org = max($y_0,$y_1) + 10;
}

//IRSH
$d_s_ajax = New DatoSectorialAjax();

$y_0 = $y_irsh;
$font_size = ($dato_para == 3) ? 12 : 20;
$title = 'Indice de Riesgo de S. Humanitaria (IRSH)';
$pdf->SetFont($font_name, 'B', $font_size);

//Calcula el x para el texto
$pdf->SetTextColor(0,0,0);
$pdf->SetX(17);
$pdf->SetY($y_0 - $alto_titulo);
$pdf->Cell(0,10,$title,0,1); 

//Definicion dato
if ($dato_para != 3) {
    $id_dato = 232;
    $def = substr($d_s_ajax->getDefinicionDatoSectorial($id_dato),0,290);
    $pdf->SetFont($font_name, '', 6);
    $pdf->MultiCell(145,0, $def,0,1,'L'); 
}

// Ahora con el nuevo irsh
$imagenes = ($dato_para == 3) ? array('8_6','8_7') : array('8_6','8_7','8_8');

$y_0 = $pdf->getY() + $alto_titulo;
$x_img_0 = $margin_left;
$show = 0;
$y_img = $y_0;
for ($im=0;$im<count($imagenes);$im++){

		$img = $path_images.'/w_'.$imagenes[$im].'.png';
		
		// Nueva fila
		if ($show >= $num_col_images){
			$y_img = $y_tmp[$show - $num_col_images] + $images_space + $y_0;
		}

		// Nueva columna
		$x_img = (fmod($show,$num_col_images) == 0) ? $x_img_0 : $x_img_0 + $w + $images_space;
		
		$resize = resize($img);
		$w = $resize['w'];
		$h = $resize['h'];
		$pdf->Image($img, $x_img , $y_img, $w, $h, '', '', '', true); 
		
		$y_tmp[$show] = $h;
		
		$show++;
}

if ($dato_para == 3){
	//Orgs
	$y_0 = $y_org;
	$font_size = 20;
	$title = 'Organizaciones';
	$pdf->SetFont($font_name, 'B', $font_size);

	//Calcula el x para el texto
	$pdf->SetTextColor(0,0,0);
	$pdf->Text(190, $y_0, $title); 

	$imagenes = array('6_1','6_4','6_2','6_3');
	$y_0 += $alto_titulo;
	//$y_1 = $y_0;
	$x_img_0 = $margin_left + $w_desplazamiento + $images_space*2;


	$show = 0;
	$y_img = $y_0;
	for ($im=0;$im<count($imagenes);$im++){

			$img = $path_images.'/w_'.$imagenes[$im].'.png';
			
			// Nueva fila
			if ($show >= $num_col_images){
				$y_img = $y_tmp[$show - $num_col_images] + $images_space + $y_0;
			}

			// Nueva columna
			$x_img = (fmod($show,$num_col_images) == 0) ? $x_img_0 : $x_img_0 + $w + $images_space;
			
			$resize = resize($img);
			$w = $resize['w'];
			$h = $resize['h'];
			$pdf->Image($img, $x_img , $y_img, $w, $h, '', '', '', true); 
			
			$y_tmp[$show] = $h;
			
			$show++;
	}
}

// ---------------------------------------------------------

//Close and output PDF document
$path_file = $this->dir_cache_perfil."/perfil_".$id_ubicacion;
$cache_file = "$path_file.pdf";
$pdf->Output($cache_file, 'F');  //Crea archivo fisico
//$pdf->Output('Perfil_'.$ubicacion_titulo.'.pdf', 'I');
header('Location: https://sidi.umaic.org/sissh/perfiles/perfil_'.$id_ubicacion.'.pdf');
//============================================================+
// END OF FILE                                                 
//============================================================+

?>
