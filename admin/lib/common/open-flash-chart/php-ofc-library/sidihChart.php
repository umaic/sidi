<?php
$path = $_SERVER["DOCUMENT_ROOT"].'/sissh/admin/lib/common/open-flash-chart/';
include_once($path.'php-ofc-library/open-flash-chart.php' );

class sidihChart extends graph {

	function sidihChart(){
		// this calls the base object constructor
		parent::graph();

		// now override some of the default settings:

		// the new default title style will be:
		// #ff00CC = bright pink
		$this->title_style = '{font-size: 20px; color: #000000; padding-bottom:20px;}';

		$this->bg_colour = '#F0F0F0';
		$this->set_inner_background( '#DDEFFA', '#CBD7E6', 90 );

		$this->x_axis_colour( '#FFFFFF', '#FFFFFF' );
		$this->y_axis_colour( '#FFFFFF', '#FFFFFF' );

		//$this->set_bg_image('http://localhost/sissh/images/consulta/footer_grafica.png','right','bottom');
		$this->set_bg_image('/sissh/images/consulta/footer_grafica.png','right','bottom');

	}

	/**
	* Calcula el maximo y para la gráfica
	* @access public
	* @param float $max_y Maximo valor
	* @return int $valor
	*/

	function maxY($max_y){

		$base_m = pow(10,(strlen(ceil($max_y))) - 1);
		$max_y_ceil = $base_m*(ceil(($max_y)/$base_m));  //$v_max=$base_m*(ceil($v/$base_m));
		$max_y_floor = $base_m*(floor(($max_y)/$base_m));  //$v_max=$base_m*(ceil($v/$base_m));

		
		//Ajuste al máximo para evitar casos como: max_y=307950, y el max_y_ceil=400000
		$med = ($max_y_ceil - $max_y_floor)/2; 
		if (($max_y_floor + $med) > $max_y){
			$max_y = $max_y_floor + $med; 	
		}
		else{
			$max_y = $max_y_ceil;	
		}
		
		return $max_y;
	}
}

?>
