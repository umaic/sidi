<?php
/**
*
*   PowerGraphic
*   version 1.0
*
*
*
* Author: Carlos Reche
* E-mail: carlosreche@yahoo.com
* Sorocaba, SP - Brazil
*
* Created: Sep 20, 2004
* Last Modification: Sep 20, 2004
*
*
*
*  Authors' comments:
*
*  PowerGraphic creates 6 different types of graphics with how many parameters you want. You can
*  change the appearance of the graphics in 3 different skins, and you can still cross data from 2
*  graphics in only 1! It's a powerful script, and I recommend you read all the instructions
*  to learn how to use all of this features. Don't worry, it's very simple to use it.
*
*  This script is free. Please keep the credits.
*
* Modified by Ruben Rojas - Colombia - 2007
*
*/
//session_start();
//$PG = new PowerGraphic;

if (isset($_GET["dir"])){
	$PG->create_graphic_minificha(1);
}

class PowerGraphic {

	var $x;
	var $y;
	var $z;

	var $title;
	var $axis_x;
	var $axis_y;
	var $graphic_1;
	var $graphic_2;
	var $type;
	var $skin;
	var $credits;
	var $latin_notation;
	var $filename;
	var $decimals;  //Si los números de los ejes llevan decimales
	var $texto_1;  //Texto extra que va en el copyright
	var $texto_extra;  //Texto extra que va en la gráfica

	var $width;
	var $height;
	var $height_title;
	var $width_title;
	var $alternate_x;
	var $type_font_title;
	var $type_font_other;
	var $type_font_table_data;
	var $type_font_copyright;
	var $pie; //Footer de la imagen


	var $total_parameters;
	var $sum_total;
	var $biggest_value;
	var $biggest_parameter;
	var $available_types;
	
	var $posx_labels_y; // Posicion de los labels del eje y para barras-lineas y sus variantes
	
	var $graphic_area_x1;
	var $graphic_area_x2;
	
	var $border;


	function PowerGraphic(){
		
		$this->x = $this->y = $this->z = array();

		$this->biggest_x        = NULL;
		$this->biggest_y        = NULL;
		$this->smallest_y        = 0;
		$this->alternate_x      = false;
		$this->graphic_2_exists = false;
		$this->total_parameters = 0;
		$this->sum_total        = 1;
		$this->type_font_title = 10; //px
		$this->type_font_other = 2;
		$this->type_font_table_data = 6;  //px
 		$this->type_font_copyright = 2;
 		$this->size_font_table_data_legend = 6;
 		$this->boder = 0;
 		
 		$path_to_fonts = $_SERVER['DOCUMENT_ROOT'].'/sissh/consulta/fonts/';
 		
		$this->font_ttf = $path_to_fonts.'stan0757.ttf';
		$this->size_ttf = 6;
		
		//$this->font_ttf_legend = 'consulta/fonts/stan0755';
		$this->font_ttf_legend = $path_to_fonts.'PIXELADE';
		$this->size_ttf_legend = 9.8;
		
		//$this->font_ttf_axis_labels = 'consulta/fonts/stan0755';
		$this->font_ttf_axis_labels = $path_to_fonts.'PIXELADE';
		$this->size_ttf_axis_labels = 9.8;

		$this->font_ttf_title = $path_to_fonts.'px_sans_nouveaux.ttf';
		$this->size_ttf_title = 6;
		
		//$this->font_ttf_table_data_legend = 'consulta/fonts/stan0755';
		$this->font_ttf_table_data_legend = $path_to_fonts.'PIXELADE';
		$this->size_ttf_table_data_legend = 9.8;

		$this->font_ttf_table_data = $path_to_fonts.'stan0757.ttf';
		$this->size_ttf_table_data = 6;
		
		$this->type_font_copyright = $path_to_fonts.'PIXELADE';
		$this->size_font_copyright = 9.8;
		
		$this->title     = (isset($_GET['title']))     ? $_GET['title']     : "";
		$this->axis_x    = (isset($_GET['axis_x']))    ? $_GET['axis_x']    : "";
		$this->axis_y    = (isset($_GET['axis_y']))    ? $_GET['axis_y']    : "";
		$this->graphic_1 = (isset($_GET['graphic_1'])) ? $_GET['graphic_1'] : "";
		$this->graphic_2 = (isset($_GET['graphic_2'])) ? $_GET['graphic_2'] : "";
		$this->type      = (isset($_GET['type']))      ? $_GET['type']      : 1;
		$this->skin      = (isset($_GET['skin']))      ? $_GET['skin']      : 1;
		$this->credits        = ((isset($_GET['credits'])) && ($_GET['credits'] == 1))               ? true : false;
		//$this->latin_notation = ((isset($_GET['latin_notation'])) && ($_GET['latin_notation'] == 1)) ? true : false;
		$this->latin_notation = true;
		$this->decimals      = (isset($_GET['decimals']))      ? $_GET['decimals']      : 1;
		$this->texto_1      = (isset($_GET['texto_1']))      ? $_GET['texto_1']      : "";
		$this->texto_extra      = (isset($_GET['texto_extra']))      ? $_GET['texto_extra']      : "";
		$this->escala      = (isset($_GET['escala']))      ? $_GET['escala']      : 1;  //Escala de la gráfica, entre 0 y 1

		$this->legend_exists        = (preg_match("(/5|6/)", $this->type)) ? true : false;
		$this->biggest_graphic_name = (strlen($this->graphic_1) > strlen($this->graphic_2)) ? $this->graphic_1 : $this->graphic_2;
		$this->height_title         = (!empty($this->title)) ? ($this->string_height(5) + 15) : 0;
		$this->width_title         = ($this->title != '') ? $this->string_width(($this->title), $this->font_ttf_title,$this->size_ttf_title) : 0;
		
		$this->space_between_dots   = 30;
		$this->higher_value         = 0;
		$this->higher_value_str     = 0;
		$this->lower_value         = 0;
		$this->lower_value_str     = 0;

		$this->num_params_legend = 11;  //Número de parámetros en la leyenda del tipo = 8 - Orgs

		$this->width               = 0;
		$this->height              = 0;
		$this->graphic_area_width  = 0;
		$this->graphic_area_height = 0;
		$this->graphic_area_x1     = 30;
		$this->graphic_area_y1     = 20 + $this->height_title;
		$this->graphic_area_x2     = $this->graphic_area_x1 + $this->graphic_area_width;
		$this->graphic_area_y2     = $this->graphic_area_y1 + $this->graphic_area_height;
		$this->legend_box_width = 0;
		
		$this->available_types = array(
		1 => 'Vertical Bars',
		2 => 'Horizontal Bars',
		3 => 'Dots',
		4 => 'Lines',
		5 => 'Pie',
		6 => 'Donut',
		7 => 'Barras acumuladas',
		8 => 'Vertical Bars con leyenda, tipo Orgs',
		9 => 'Histograma Barras',
		10 => 'Barras verticales con tabla resumen de valores debajo de eje x',
		11 => 'Lineas con tabla resumen de valores debajo de eje x',
		12 => 'Histograma Lineas',
		13 => 'Barras acumuladas con tabla resumen debajo del eje x',
		14 => 'Histograma Lineas con tabla resumen debajo del eje x',
		15 => 'Piramide'
		);
		$this->available_skins = array(
		1 => 'Office',
		2 => 'Matrix',
		3 => 'Spring',
		);

	}

	function start_minificha($parameters)
	{
		$this->legend_exists        = (preg_match("(/5|6/)", $this->type)) ? true : false;
		$this->biggest_graphic_name = (strlen($this->graphic_1) > strlen($this->graphic_2)) ? $this->graphic_1 : $this->graphic_2;
		$this->height_title         = (!empty($this->title)) ? ($this->string_height(5) + 15) : 0;
		$this->width_title         = ($this->title != '') ? $this->string_width(($this->title), $this->font_ttf_title,$this->size_ttf_title) : 0;

		$this->space_between_dots   = 30;
		$this->higher_value         = 0;
		$this->higher_value_str     = 0;

		$this->width               = 0;
		$this->height              = 0;
		$this->graphic_area_width  = 0;
		$this->graphic_area_height = 0;
		$this->graphic_area_x1     = 0;
		$this->graphic_area_y1     = 20 + $this->height_title;
		$this->graphic_area_x2     = $this->graphic_area_x1 + $this->graphic_area_width;
		$this->graphic_area_y2     = $this->graphic_area_y1 + $this->graphic_area_height;

		// Defines array $temp
		foreach ($parameters as $parameter => $value)
		{
			if (preg_match("/^x\d+$/i", $parameter))
			{
				if (strtolower($parameter{0}) == 'x')
				{
					if (empty($value))
					{
						continue;
					}
 
					if (strpos($value,"|") === false){
						if (strlen($value) > strlen($this->biggest_x)){
							$this->biggest_x = $value;
						}
					}
					else{
						$value_tmp = explode("|",$value);
						foreach ($value_tmp as $val){
							if (strlen($val) > strlen($this->biggest_x)){
								$this->biggest_x = $val;
							}
						}
					}
					
					$num        = substr($parameter, 1, (strlen($parameter)-1) );
					$temp[$num] = $value;

					if ((!empty($parameters['z'.$num])) && (preg_match("(/1|2|3|4|7|9|12|10/)", $this->type)))
					{
						$this->graphic_2_exists = true;
					}
				}
			}
		}

		$i = 0;
		$chk_doble_linea = 0;
		// Defines arrays $this->x, $this->y and $this->z (if exists)
		foreach ($temp as $index => $parameter)
		{
			$this->x[$i] = $parameter;
			$this->y[$i] = 0;
			
			if ($chk_doble_linea == 0){
				if (strpos($this->x[$i],"|") === false){
					$this->labels_x_doble_linea = 0;
				}
				else{
					$this->labels_x_doble_linea = 1;
					$chk_doble_linea = 1;
				}
			}			

			if (!empty($parameters['y'.$index]))
			{
				$this->y[$i] = $parameters['y'.$index];

				if ($parameters['y'.$index] > $this->biggest_y)
				{
					$this->biggest_y = number_format(round($parameters['y'.$index], 1), 1, ".", "");
				}
				if ($parameters['y'.$index] < $this->smallest_y){
					$this->smallest_y = number_format(round($parameters['y'.$index], 1), 1, ".", "");
				}
			}

			if ($this->graphic_2_exists){
				
				$value       = (isset($parameters['z'.$index])) ? $parameters['z'.$index] : 0;
				$this->z[$i] = $value;

				//BARRA ACUMULADA
				if ($this->type == 7 || $this->type == 13){
					$value += $this->y[$i];  //Suma la barra anterior
					if ($value > $this->biggest_y){
						$this->biggest_y = number_format(round($value, 1), 1, ".", "");
					}
				}
				else{
					if ($value > $this->biggest_y)
					{
						$this->biggest_y = number_format(round($value, 1), 1, ".", "");
					}
					if ($value < $this->smallest_y){
						$this->smallest_y = number_format(round($value, 1), 1, ".", "");
					}
				}
			}

			unset($temp[$index]);
			$i++;
		}

		if (($this->graphic_2_exists == true)  &&  ((!empty($this->graphic_1)) || (!empty($this->graphic_2)))){
			$this->legend_exists = true;
		}

		$this->offset_x_ini = 15;
		$this->total_parameters    = count($this->x);
		$this->sum_total           = array_sum($this->y);

		$this->calculate_higher_value();
		$this->calculate_lower_value();

		//ESPACIO ENTRE BARRAS
		$this->ancho_barra = 10;
		$this->space_between_bars   = 30;
		switch ($this->type){
			//Barras verticales
			case 1:
				if ($this->graphic_2_exists == true){
					$this->space_between_bars   = 15;
					$this->ancho_barra = 7;
				}
				else{
					$this->space_between_bars   = 25;
					$this->ancho_barra = 10;
				}
				//Check del ancho del valor mas largo
				$len_value = $this->string_width(number_format($this->higher_value,0,"","."),$this->font_ttf_table_data);
				if ( $len_value >= $this->space_between_bars){
					$this->space_between_bars = $len_value + 2;
				}
				break;
			case 10:
				if ($this->graphic_2_exists == true){
					$this->space_between_bars   = 33;
					$this->ancho_barra = 7;
				}
				else{
					$this->space_between_bars   = 30;
					$this->ancho_barra = 10;
				}
				//Check del ancho del valor mas largo
				$len_value = $this->string_width(number_format($this->higher_value,0,"","."),$this->font_ttf_table_data);
				if ( $len_value >= $this->space_between_bars && $this->labels_x_doble_linea == 0){
					$this->space_between_bars = $len_value + 8;
				}
				break;
			//Barrras acumuladas con t. resumen	
			case 13:
				if ($this->graphic_2_exists == true){
					$this->space_between_bars   = 33;
					$this->ancho_barra = 7;
				}
				else{
					$this->space_between_bars   = 30;
					$this->ancho_barra = 10;
				}
				//Check del ancho del valor mas largo
				$len_value = $this->string_width(number_format($this->higher_value,0,"","."),$this->font_ttf_table_data);
				if ( $len_value >= $this->space_between_bars && $this->labels_x_doble_linea == 0){
					$this->space_between_bars = $len_value + 8;
				}
				break;
				
				//Lineas con tabla resumen
			case 11:
				$this->space_between_bars = $this->space_between_dots;
				//Check del ancho del valor mas largo
				$len_value = $this->string_width(number_format($this->higher_value,0,"","."),$this->font_ttf_table_data);
				if ( $len_value  >= $this->space_between_bars && $this->labels_x_doble_linea == 0){
					$this->space_between_bars = $len_value + 5;
					$this->space_between_dots = $len_value + 5;
				}
				break;
				//Histograma
			case 9:
				if ($this->graphic_2_exists == true){
					$this->space_between_bars   = 12;
					$this->ancho_barra = 7;
				}
				else{
					$this->space_between_bars   = 25;
					$this->ancho_barra = 10;
				}
				break;
				//Barras verticales con leyenda
			case 8:
				$this->space_between_bars   = 12;
				$this->ancho_barra = 10;
				break;
				//Barras horizontales
			case 2:
				if ($this->graphic_2_exists == true){
					$this->space_between_bars = 16;
					$this->ancho_barra = 10;
				}
				else{
					$this->space_between_bars = 20;
					$this->ancho_barra = 10;
				}
				
				
				//Si es menor de 4, aumenta el ancho de la barra para que no se vea tan pequeña
				if (count($this->y) < 5){
					$this->space_between_bars = 30;
					$this->ancho_barra = 15;
				}
				
				break;
				//Barras acumuladas verticales
			case 7:
				$this->space_between_bars   = 30;
				break;
			//Histograma lineas tabla resumen
			case 14:
				//Check del ancho de la etiqueta x mas larga
				$this->space_between_bars = $this->space_between_dots;
				$len_value = $this->string_width($this->biggest_x,$this->font_ttf_table_data);
				if ( $len_value > $this->space_between_dots && $this->labels_x_doble_linea == 0){
					$this->space_between_bars = $len_value + 4;
					$this->space_between_dots = $len_value + 4;
				}
				
				//Check del valor mas largo
				$len_value = $this->string_width(number_format($this->higher_value,0,"","."),$this->font_ttf_table_data);
				if ( $len_value  >= $this->space_between_bars && $this->labels_x_doble_linea == 0){
					$this->space_between_bars = $len_value + 4;
					$this->space_between_dots = $len_value + 4;
				}
			break;

			//Piramide
			case 15:
				$this->space_between_bars = 0;
				$this->ancho_barra = 15;
			break;
		}

		if (!preg_match("/^(3|4|7|10|11|13|14)$/", $this->type))	$this->space_between_bars += ($this->graphic_2_exists == true) ? 10 : 0;

		$this->calculate_width_minificha();
		$this->calculate_height_minificha();
		
		$this->escalar();
		
		$this->posx_labels_y = ($this->graphic_area_x1 - ($this->string_width($value,$this->font_ttf_table_data))-10);

	}

	function create_graphic_minificha($stdout=0)
	{

		$arr = explode("&",$this->create_query_string());
		foreach ($arr as $ar){
			$p = explode("=",$ar);

			$parameters[$p[0]] = $p[1];
		}

		$this->start_minificha($parameters);
		
		$this->img = imagecreatetruecolor($this->width, $this->height);
		//Imagen transparente
		imagesavealpha($this->img, true);
		//Antialias para lineas
		//imageAntiAlias($this->img,1);
		imagealphablending ($this->img, true);
		
		$trans_colour = imagecolorallocatealpha($this->img, 255, 255, 255, 127);
    	imagefill($this->img, 0, 0, $trans_colour);
    
		$this->load_color_palette();

		// Draw title
		if (!empty($this->title))
		{
			$center = ($this->width / 2) - ($this->string_width($this->title, $this->font_ttf_title) / 2);
			//imagestring($this->img, $this->type_font_title, $center - 10, 2, $this->title, $this->color['title']);
			$this->writeText($this->size_ttf_title, $center, 10, $this->title, $this->color['title'],$this->font_ttf_title);
		}


		// Draw axis and background lines for "vertical bars", "dots" and "lines"
		if (preg_match("/^(1|3|4|7|8|9|12|10|11|13|14)$/", $this->type))
		{
			

			$higher_value_y    = $this->graphic_area_y1 + (0.1 * $this->graphic_area_height);
			$higher_value_size = 0.9 * $this->graphic_area_height;

			$lower_value_y    = $this->graphic_area_y1 + $this->graphic_area_y2 + (0.1 * $this->graphic_area_height);
			$lower_value_size = 0.9 * $this->graphic_area_height;

			$less  = 7 * strlen($this->higher_value_str);

			/*********************************/
			/* LINEAS DE DIVISION POSITIVA   */
			/*********************************/
			//Primera línea de division, de arriba hacia abajo
			imageline($this->img, $this->graphic_area_x1, $higher_value_y, $this->graphic_area_x2, $higher_value_y, $this->color['bg_lines']);
			//imagestring($this->img, $this->type_font_other, ($this->graphic_area_x1 - ($this->string_width($this->higher_value_str,$this->type_font_other))-10), ($higher_value_y-7), $this->higher_value_str, $this->color['axis_values']);
			$this->writeText($this->size_ttf_axis_labels, ($this->graphic_area_x1 - ($this->string_width($this->higher_value_str,$this->font_ttf_axis_labels))-10), ($higher_value_y), $this->higher_value_str, $this->color['axis_values'],$this->font_ttf_axis_labels);

			//Lineas de división
			for ($i = 1; $i < 10; $i++)
			{
				$dec_y = $i * ($higher_value_size / 10);
				$x1 = $this->graphic_area_x1;
				$y1 = $this->graphic_area_y2 - $dec_y;
				$x2 = $this->graphic_area_x2;
				$y2 = $this->graphic_area_y2 - $dec_y;
				
				imageline($this->img, $x1, $y1, $x2, $y2, $this->color['bg_lines']);
				if ($i % 2 == 0) {
					$value = $this->number_formated($this->higher_value * $i / 10);
					$less = 7 * strlen($value);
					$posx_labels_y = ($x1 - ($this->string_width($value,$this->font_ttf_axis_labels))-10);
					
					//imagestring($this->img, $this->type_font_table_data, $posx_labels_y, ($y2-7), $value, $this->color['axis_values']);
					$this->writeText($this->size_ttf_axis_labels, $posx_labels_y, ($y2), $value, $this->color['axis_values'],$this->font_ttf_axis_labels);
				}
			}

			if ($this->type == 9 || $this->type == 12 || $this->type == 14){
				/*********************************/
				/* LINEAS DE DIVISION NEGATIVA   */
				/*********************************/

				$dec_y = ($lower_value_size / 10);
				$this->graphic_area_y3 = $this->graphic_area_y2 + $this->graphic_area_height - $dec_y;

				//Primera línea de division, de abajo hacia arriba
				imageline($this->img, $this->graphic_area_x1, $this->graphic_area_y3, $this->graphic_area_x2, $this->graphic_area_y3, $this->color['bg_lines']);
				
				//imagestring($this->img, $this->type_font_other, ($x1 - ($this->string_width(-1*$this->lower_value_str,$this->type_font_other))-10), ($this->graphic_area_y3-7), -1*$this->lower_value_str, $this->color['axis_values']);
				$this->writeText($this->size_ttf_axis_labels, ($x1 - ($this->string_width(-1*$this->lower_value_str,$this->font_ttf_table_data))-10), ($this->graphic_area_y3), -1*$this->lower_value_str, $this->color['axis_values'],$this->font_ttf_axis_labels);

				//Lineas de división
				$ii = 9;
				for ($i = 1; $i < 10; $i++){
					$dec_y = $i * ($lower_value_size / 10);
					$y1 = $this->graphic_area_y3 - $dec_y;
					$y2 = $this->graphic_area_y3 - $dec_y;
					
					imageline($this->img, $x1, $y1, $x2, $y2, $this->color['bg_lines']);
					if ($i % 2 == 0) {
						$value = $this->number_formated(-1*$this->lower_value * $ii / 10);
						$less = 7 * strlen($value);
						
						$posx_labels_y = ($x1 - ($this->string_width($value,$this->font_ttf_axis_labels))-10);
						
						//imagestring($this->img, $this->type_font_other, $posx_labels_y, ($y2-7), $value, $this->color['axis_values']);
						$this->writeText($this->size_ttf_axis_labels, $posx_labels_y, ($y2), $value, $this->color['axis_values'],$this->font_ttf_axis_labels);
					}

					$ii--;
				}
			}

			if ($this->type != 9 && $this->type != 12 && $this->type != 14){
				// Axis X
				if (!preg_match("/^(10|11)$/", $this->type)){
					//imagestring($this->img, $this->type_font_other, $this->graphic_area_x2+10, $this->graphic_area_y2+3, $this->axis_x, $this->color['title']);
					$this->writeText($this->size_ttf_axis_labels, $this->graphic_area_x2+10, $this->graphic_area_y2+3, $this->axis_x, $this->color['title'],$this->font_ttf_axis_labels);
				}
				imageline($this->img, $this->graphic_area_x1, $this->graphic_area_y2, $this->graphic_area_x2, $this->graphic_area_y2, $this->color['axis_line']);

				// Axis Y
				//imagestring($this->img, $this->type_font_other, 20, $this->graphic_area_y1-20, $this->axis_y, $this->color['title']);
				$this->writeText($this->size_ttf_axis_labels, 20, $this->graphic_area_y1-20, $this->axis_y, $this->color['title'],$this->font_ttf_axis_labels);
				imageline($this->img, $this->graphic_area_x1, $this->graphic_area_y1, $this->graphic_area_x1, $this->graphic_area_y2, $this->color['axis_line']);

			}
			else{
				//el 0
				imageline($this->img, $this->graphic_area_x1, $this->graphic_area_y2, $this->graphic_area_x2, $this->graphic_area_y2, $this->color['axis_line']);

				// Axis X
				//imagestring($this->img, $this->type_font_other, $this->graphic_area_x2+10, $this->graphic_area_y3+3, $this->axis_x, $this->color['title']);
				$this->writeText($this->size_ttf_axis_labels, $this->graphic_area_x2+10, $this->graphic_area_y3+3, $this->axis_x, $this->color['title'],$this->font_ttf_axis_labels);
				imageline($this->img, $this->graphic_area_x1, $this->graphic_area_y3+15, $this->graphic_area_x2, $this->graphic_area_y3+15, $this->color['axis_line']);

				// Axis Y
				//imagestring($this->img, $this->type_font_other, 20, $this->graphic_area_y1-20, $this->axis_y, $this->color['title']);
				$this->writeText($this->size_ttf_axis_labels, 20, $this->graphic_area_y1-20, $this->axis_y, $this->color['title'],$this->font_ttf_axis_labels);
				imageline($this->img, $this->graphic_area_x1, $this->graphic_area_y1, $this->graphic_area_x1, $this->graphic_area_y3+15, $this->color['axis_line']);

			}

			if ($this->type == 8){
				$this->draw_legend();
			}

			else if ($this->type == 10 || $this->type == 11 || $this->type == 13 || $this->type == 14){
				$this->draw_table_data();
			}

			if ($this->legend_exists == true && $this->type != 10 && $this->type != 11 && $this->type != 13 && $this->type != 14)
			{
				$this->draw_legend();
			}			
		}


		// Draw axis and background lines for "horizontal bars"
		else if ($this->type == 2){
			
			$offset_label_y = 15;
			
			if ($this->legend_exists == true)
			{
				$this->draw_legend();
			}

			$higher_value_x    = $this->graphic_area_x2 - (0.2 * $this->graphic_area_width);
			$higher_value_size = 0.8 * $this->graphic_area_width;

			/*
			imageline($this->img, ($this->graphic_area_x1+$higher_value_size), $this->graphic_area_y1, ($this->graphic_area_x1+$higher_value_size), $this->graphic_area_y2, $this->color['bg_lines']);
			//imagestring($this->img, $this->type_font_table_data, (($this->graphic_area_x1+$higher_value_size) - ($this->string_width($this->higher_value, $this->type_font_other)/2)), ($this->graphic_area_y2+2), $this->higher_value_str, $this->color['axis_values']);
			$this->writeText($this->size_ttf_axis_labels, (($this->graphic_area_x1+$higher_value_size) - ($this->string_width($this->higher_value, $this->font_ttf_axis_labels,$this->size_ttf_axis_labels)/2)), ($this->graphic_area_y2+10), $this->higher_value_str, $this->color['axis_values'],$this->font_ttf_axis_labels);

			for ($i = 1, $alt = 15; $i < 10; $i++)
			{
				$dec_x = number_format(round($i * ($higher_value_size  / 10), 1), 1, ".", "");

				imageline($this->img, ($this->graphic_area_x1+$dec_x), $this->graphic_area_y1, ($this->graphic_area_x1+$dec_x), $this->graphic_area_y2, $this->color['bg_lines']);
				if ($i % 2 == 0) {
					$alt   = (strlen($this->biggest_y) > 4 && $alt != 15) ? 20 : 10;
					$value = $this->number_formated($this->higher_value * $i / 10);
					//imagestring($this->img, $this->type_font_table_data, (($this->graphic_area_x1+$dec_x) - ($this->string_width($this->higher_value, $this->type_font_other)/2)), ($this->graphic_area_y2+$alt), $value, $this->color['axis_values']);
					$this->writeText($this->size_ttf_axis_labels, (($this->graphic_area_x1+$dec_x) - ($this->string_width($this->higher_value, $this->font_ttf_axis_labels,$this->size_ttf_axis_labels)/2)), ($this->graphic_area_y2+$alt), $value, $this->color['axis_values'],$this->font_ttf_axis_labels);
				}
			}

			*/
			// Axis X
			//imagestring($this->img, $this->type_font_other, ($this->graphic_area_x2+10), ($this->graphic_area_y2+3), $this->axis_y, $this->color['title']);
			$this->writeText($this->size_ttf_axis_labels, ($this->graphic_area_x2+10), ($this->graphic_area_y2+3), $this->axis_y, $this->color['title'],$this->font_ttf_axis_labels);
			imageline($this->img, $this->graphic_area_x1, $this->graphic_area_y2, $this->graphic_area_x2, $this->graphic_area_y2, $this->color['axis_line']);
			
			// Axis Y
			//imagestring($this->img, $this->type_font_other, 20, ($this->graphic_area_y1-20), $this->axis_x, $this->color['title']);
			$this->writeText($this->size_ttf_axis_labels, 20, ($this->graphic_area_y1), $this->axis_x, $this->color['title'],$this->font_ttf_axis_labels);
			imageline($this->img, $this->graphic_area_x1, $this->graphic_area_y1, $this->graphic_area_x1, $this->graphic_area_y2, $this->color['axis_line']);
			
			//Labels x
			$num_labels = 5;
			
			for($l=1;$l<=$num_labels;$l++){
				$val_lab = intval(($this->higher_value / $num_labels) * ($l));
				
				$digits   = strlen($val_lab);
		
				$precision = -1*($digits -2);
		
				$val_lab = ceil($val_lab * pow (10, $precision) )/ pow (10, $precision);
				
				//Linea de valor
				$x = $this->graphic_area_x1 + ($higher_value_size / $num_labels) * ($l);
				imageline($this->img, $x, $this->graphic_area_y2, $x, $this->graphic_area_y2 + 2, $this->color['axis_line']);

				//Labels x parte derecha, justo en la mitad de la linea
				$x -= $this->string_width(($val_lab), $this->font_ttf_legend,$this->size_ttf_axis_labels) / 2;
				$this->writeText($this->size_ttf_axis_labels, $x, ($this->graphic_area_y2 + $offset_label_y), $val_lab, $this->color['title'],$this->font_ttf_axis_labels);
				
			}
		}

		// Ejes Piramide
		else if ($this->type == 15){
			
			$this->cero = ($this->graphic_area_width / 2) + $this->graphic_area_x1;
			$offset_label_y = 15;
			$offset_legend = 2;

			$higher_value_x    = $this->graphic_area_x2 - (0.2 * $this->graphic_area_width);
			$higher_value_size = 0.9 * $this->graphic_area_width/2;

			//Leyenda Derecha
			$x = $this->cero + 40;
			$y = $this->graphic_area_y1 - $offset_legend; 
			$alto_rec = 10;
			imagefilledrectangle($this->img, $x, $y, ($x+$alto_rec), ($y+$alto_rec), $this->color['bars']);
			imagerectangle($this->img, $x, $y, ($x+$alto_rec), ($y+$alto_rec), $this->color['axis_line']);
			$this->writeText($this->size_ttf_legend, $x + $alto_rec + 5, $y + $alto_rec, $this->graphic_1, $this->color['title'],$this->font_ttf_legend);
			
			//Leyenda Izquierda
			$x = $this->graphic_area_x1 + 40;
			$y = $this->graphic_area_y1 - $offset_legend; 
			$alto_rec = 10;
			imagefilledrectangle($this->img, $x, $y, ($x+$alto_rec), ($y+$alto_rec), $this->color['bars_2']);
			imagerectangle($this->img, $x, $y, ($x+$alto_rec), ($y+$alto_rec), $this->color['axis_line']);
			$this->writeText($this->size_ttf_legend, $x + $alto_rec + 5, $y + $alto_rec, $this->graphic_2, $this->color['title'],$this->font_ttf_legend);
			
			// Eje Axis X
			$x = $this->cero - $this->string_width($this->axis_y, $this->font_ttf_legend,$this->size_ttf_axis_labels) / 2;
			
			$this->writeText($this->size_ttf_axis_labels, $x, ($this->graphic_area_y2 + $offset_label_y*2), $this->axis_y, $this->color['title'],$this->font_ttf_axis_labels);
			imageline($this->img, $this->graphic_area_x1, $this->graphic_area_y2, $this->graphic_area_x2, $this->graphic_area_y2, $this->color['axis_line']);
			
			// Labels Axis Y
			$this->writeText($this->size_ttf_axis_labels, 20, ($this->graphic_area_y1 -10), $this->axis_x, $this->color['title'],$this->font_ttf_axis_labels);
			imageline($this->img, $this->graphic_area_x1, $this->graphic_area_y1, $this->graphic_area_x1, $this->graphic_area_y2, $this->color['axis_line']);
			
			//Labels ejex
			$this->writeText($this->size_ttf_axis_labels, $this->cero, ($this->graphic_area_y2 + $offset_label_y), 0, $this->color['title'],$this->font_ttf_axis_labels);
			
			$num_labels = 3;
			
			for($l=1;$l<=$num_labels;$l++){
				$val_lab = intval(($this->higher_value / $num_labels) * ($l));
				
				$digits   = strlen($val_lab);
		
				$precision = -1*($digits -2);
		
				$val_lab = ceil($val_lab * pow (10, $precision) )/ pow (10, $precision);
				
				//Linea de valor
				$x = $this->cero + ($higher_value_size / $num_labels) * ($l);
				imageline($this->img, $x, $this->graphic_area_y2, $x, $this->graphic_area_y2 + 2, $this->color['axis_line']);

				//Labels x parte derecha, justo en la mitad de la linea
				$x -= $this->string_width(($val_lab), $this->font_ttf_legend,$this->size_ttf_axis_labels) / 2;
				$this->writeText($this->size_ttf_axis_labels, $x, ($this->graphic_area_y2 + $offset_label_y), $val_lab, $this->color['title'],$this->font_ttf_axis_labels);
				
				//Linea sobre el valor
				$x = $this->cero - ($higher_value_size / $num_labels) * ($l);
				imageline($this->img, $x, $this->graphic_area_y2, $x, $this->graphic_area_y2 + 2, $this->color['axis_line']);
				
				//Labels x parte izquierda, justo en la mitad de la linea
				$x -= $this->string_width(($val_lab), $this->font_ttf_legend,$this->size_ttf_axis_labels) / 2;				
				$this->writeText($this->size_ttf_axis_labels, $x, ($this->graphic_area_y2 + $offset_label_y), $val_lab, $this->color['title'],$this->font_ttf_axis_labels);
				
			}
		}
		
		// Draw legend box for "pie" or "donut"
		else if (preg_match("/^(5|6)$/", $this->type))
		{
			$this->draw_legend();
		}



		/**
        * Draw graphic: VERTICAL BARS
        */
		if ($this->type == 1 || $this->type == 9 || $this->type == 10)
		{
			$num = 1;
			//$x   = $this->graphic_area_x1 + ($this->space_between_bars/2);
			$x   = $this->graphic_area_x1 + (($this->space_between_bars-$this->ancho_barra)/2);

			//($this->type == 1) ? $ancho_barra = 15 : $ancho_barra = 15;

			foreach ($this->x as $i => $parameter)
			{
				if (isset($this->z[$i])) {
					if ($this->z[$i] > 0){
						$size = round($this->z[$i] * $higher_value_size / $this->higher_value);
						$x1   = $x + $this->ancho_barra + 1;
						$y1   = ($this->graphic_area_y2 - $size);
						$x2   = $x1 + $this->ancho_barra;
						$y2   = $this->graphic_area_y2 - 1;
						
						imagefilledrectangle($this->img, $x1, $y1, $x2, $y2, $this->color['bars_2']);
					}
					else if ($this->z[$i] < 0){
						$size = round(-1*$this->z[$i] * $lower_value_size / $this->lower_value);
						$x1   = $x + $this->ancho_barra + 1;
						$y2   = ($this->graphic_area_y2 + $size) + 1;
						$x2   = $x1 + $this->ancho_barra;
						$y1   = $this->graphic_area_y2 + 1;
						
						imagefilledrectangle($this->img, $x1, $y1, $x2, $y2, $this->color['bars_2']);
					}

					
				}

				if ($this->y[$i] > 0){
					$size = round($this->y[$i] * $higher_value_size / $this->higher_value);
					$alt  = (($num % 2 == 0) && (strlen($this->biggest_x) > 5)) ? 15 : 2;
					$x1   = $x;
					$y1   = ($this->graphic_area_y2 - $size) + 1;
					$x2   = $x1 + $this->ancho_barra;
					$y2   = $this->graphic_area_y2 - 1;
					
					imagefilledrectangle($this->img, $x1, $y1, $x2, $y2, $this->color['bars']);
				}
				else if ($this->y[$i] < 0){
					$size = round(-1*$this->y[$i] * $lower_value_size / $this->lower_value);
					$alt  = (($num % 2 == 0) && (strlen($this->biggest_x) > 5)) ? 15 : 2;
					$x1   = $x;
					$y2   = ($this->graphic_area_y2 + $size) + 1;
					$x2   = $x1 + $this->ancho_barra;
					$y1   = $this->graphic_area_y2 + 1;
					
					imagefilledrectangle($this->img, $x1, $y1, $x2, $y2, $this->color['bars']);
				}

				$x   += $this->space_between_bars;
				$num++;

				if ($this->type == 1){
					//imagestring($this->img, $this->type_font_other, ((($x1+$x2)/2) - (strlen($parameter)*7/2)), ($y2+$alt+2), $parameter, $this->color['axis_values']);
					$this->writeText($this->size_ttf_axis_labels, ((($x1+$x2)/2) - (strlen($parameter)*7/2)), ($y2+$alt+2), $parameter, $this->color['axis_values'],$this->font_ttf_axis_labels);
				}
				else if ($this->type != 10){
					//imagestring($this->img, $this->type_font_other, ((($x1+$x2)/2) - (strlen($parameter)*7/2)), ($this->graphic_area_y3+15+$alt+2), $parameter, $this->color['axis_values']);
					$this->writeText($this->size_ttf_axis_labels, ((($x1+$x2)/2) - (strlen($parameter)*7/2)), ($this->graphic_area_y3+15+$alt+2), $parameter, $this->color['axis_values'],$this->font_ttf_axis_labels);
				}
			}
		}


		/**
        * Draw graphic: VERTICAL BARS ACUMULADAS
        */
		if ($this->type == 7 || $this->type == 13)
		{
			$num = 1;
			//$x   = $this->graphic_area_x1 + 20;
			$x   = $this->graphic_area_x1 + (($this->space_between_bars-$this->ancho_barra)/2);
			//$ancho_barra = 20;

			foreach ($this->x as $i => $parameter){
				
				if (isset($this->z[$i])) {
					
					if ($this->y[$i] > 0){
						$size = round(($this->z[$i] + $this->y[$i]) * $higher_value_size / $this->higher_value);
						//$x1   = $x + 20;
						$x1   = $x;
						$z1   = ($this->graphic_area_y2 - $size);
						$x2   = $x1 + $this->ancho_barra;
						$z2   = $this->graphic_area_y2 - 1;
	
						/*imageline($this->img, ($x1+1), ($y1-1), $x2, ($y1-1), $this->color['bars_2_shadow']);
						imageline($this->img, ($x2+1), ($y1-1), ($x2+1), $y2, $this->color['bars_2_shadow']);
						imageline($this->img, ($x2+2), ($y1-1), ($x2+2), $y2, $this->color['bars_2_shadow']);*/
						imagefilledrectangle($this->img, $x1, $z1, $x2, $z2, $this->color['bars']);
					}

					if ($this->z[$i] > 0){
						$size = round($this->z[$i] * $higher_value_size / $this->higher_value);
						//$x1   = $x + 20;
						$x1   = $x;
						$z1   = ($this->graphic_area_y2 - $size);
						$x2   = $x1 + $this->ancho_barra;
						$z2   = $this->graphic_area_y2 - 1;
	
						/*imageline($this->img, ($x1+1), ($y1-1), $x2, ($y1-1), $this->color['bars_2_shadow']);
						imageline($this->img, ($x2+1), ($y1-1), ($x2+1), $y2, $this->color['bars_2_shadow']);
						imageline($this->img, ($x2+2), ($y1-1), ($x2+2), $y2, $this->color['bars_2_shadow']);*/
						imagefilledrectangle($this->img, $x1, $z1, $x2, $z2, $this->color['bars_2']);
					}
				}
				$size = round($this->y[$i] * $higher_value_size / $this->higher_value);
				$alt  = (($num % 2 == 0) && (strlen($this->biggest_x) > 5)) ? 15 : 2;
				$x1   = $x;
				$y1   = ($this->graphic_area_y2 - $size);
				$x2   = $x1 + $this->ancho_barra;
				$y2   = $this->graphic_area_y2 - 1;
				$x   += $this->space_between_bars;
				$num++;

				/*imageline($this->img, ($x1+1), ($y1-1), $x2, ($y1-1), $this->color['bars_shadow']);
				imageline($this->img, ($x2+1), ($y1-1), ($x2+1), $y2, $this->color['bars_shadow']);
				imageline($this->img, ($x2+2), ($y1-1), ($x2+2), $y2, $this->color['bars_shadow']);*/
				if (!isset($this->z[$i])){
					imagefilledrectangle($this->img, $x1, $y1, $x2, $y2, $this->color['bars']);
				}


				if ($this->type != 13){
					//imagestring($this->img, $this->type_font_other, ((($x1+$x2)/2) - (strlen($parameter)*7/2)), ($y2+$alt+2), $parameter, $this->color['axis_values']);
					$this->writeText($this->size_ttf_axis_labels, ((($x1+$x2)/2) - (strlen($parameter)*7/2)), ($y2+$alt+2), $parameter, $this->color['axis_values'],$this->font_ttf_axis_labels);
				}
			}
		}

		/**
        * Draw graphic: Barras Verticales con leyenda al estilo Torta
        */
		if ($this->type == 8)
		{
			$num = 1;
			$x   = $this->graphic_area_x1 + 20;

			$ancho_barra = $this->ancho_barra;

			foreach ($this->x as $i => $parameter){

				$size = round($this->y[$i] * $higher_value_size / $this->higher_value);
				$alt  = (($num % 2 == 0) && (strlen($this->biggest_x) > 5)) ? 15 : 2;
				$x1   = $x;
				$y1   = ($this->graphic_area_y2 - $size) + 1;
				$x2   = $x1 + $ancho_barra;
				$y2   = $this->graphic_area_y2 - 1;
				$x   += $this->space_between_bars;
				$num++;

				$color = $this->color["8_color_$i"];

				/*imageline($this->img, ($x1+1), ($y1-1), $x2, ($y1-1), $this->color['bars_shadow']);
				imageline($this->img, ($x2+1), ($y1-1), ($x2+1), $y2, $this->color['bars_shadow']);
				imageline($this->img, ($x2+2), ($y1-1), ($x2+2), $y2, $this->color['bars_shadow']);*/
				imagefilledrectangle($this->img, $x1, $y1, $x2, $y2, $color);
				//imagestring($this->img, $this->type_font_other, ((($x1+$x2)/2) - (strlen($parameter)*7/2)), ($y2+$alt+2), $parameter, $this->color[$color]);
			}
		}

		/**
        * Draw graphic: HORIZONTAL BARS
        */
		else if ($this->type == 2)
		{
			$y = 10;   //ESPACIO ENTRE EL EJE Y Y LA PRIMERA BARRA
			$ancho_barra  = $this->ancho_barra;
			$size_pixeles_font = 6;   //No se usa $this->size_ttf_axis_labels, porque esa fuente usa tamaño 9.8, pero en realidad son 6 px
			$offset_txt = ($ancho_barra - $size_pixeles_font)/2 + $size_pixeles_font;
			
			foreach ($this->x as $i => $parameter){
				if (isset($this->z[$i])) {
					$size = round($this->z[$i] * $higher_value_size / $this->higher_value);
					$x1   = $this->graphic_area_x1 + 1;
					$y1   = $this->graphic_area_y1 + $y + $ancho_barra;
					$x2   = $x1 + $size;
					$y2   = $y1 + $ancho_barra;
					
					//Barra Horizontal
					imagefilledrectangle($this->img, $x1, $y1, $x2, $y2, $this->color['bars_2']);
					
					//Texto en frente de la barra
					$this->writeText($this->size_ttf_axis_labels, ($x2+7), ($y1+$offset_txt), $this->number_formated($this->z[$i], 2), $this->color['bars_2_shadow'],$this->font_ttf_axis_labels);
				}

				$size = round(($this->y[$i] / $this->higher_value) * $higher_value_size);
				$x1   = $this->graphic_area_x1 + 1;
				$y1   = $this->graphic_area_y1 + $y;
				$x2   = $x1 + $size;
				$y2   = $y1 + $ancho_barra;
				$y   += $this->space_between_bars;
				
				//Barra Horizontal
				imagefilledrectangle($this->img, $x1, $y1, $x2, $y2, $this->color['bars']);
				
				//Texto en frente de la barra
				$this->writeText($this->size_ttf_axis_labels, ($x2+7), ($y1+$offset_txt), $this->number_formated($this->y[$i], 2), $this->color['bars_shadow'],$this->font_ttf_axis_labels);

				//$font_labels = ($this->escala == 1) ? $this->size_ttf_axis_labels : $this->size_ttf_axis_labels;
				$font_labels = $this->font_ttf_axis_labels;
				$size_fonts_labels = $this->size_ttf_axis_labels;
				
				$posx_labels_y = ($x1 - ($this->string_width($parameter,$font_labels,$size_fonts_labels))-5);
				
				//Labels eje y
				//imagestring($this->img, $font_labels, $posx_labels_y, ($y1+2), $parameter, $this->color['axis_values']);
				$this->writeText($size_fonts_labels, $posx_labels_y, ($y1+12), $parameter, $this->color['axis_values'],$font_labels);
			}
		}

		
        // PIRAMIDE
		else if ($this->type == 15){
			
			$y = 0;   //ESPACIO ENTRE EL EJE Y Y LA PRIMERA BARRA
			$ancho_barra  = $this->ancho_barra;
			
			$cero = $this->cero; 

			foreach ($this->x as $i => $parameter){
				
				if (isset($this->z[$i])) {
					//$size = round($this->z[$i] * $higher_value_size / $this->higher_value);
					$size = round(($this->z[$i] / $this->higher_value) * $higher_value_size);
					$x1   = $cero - $size;
					$y1   = $this->graphic_area_y1 + $y + $ancho_barra;
					$x2   = $cero;
					$y2   = $y1 + $ancho_barra;
					
					imagefilledrectangle($this->img, $x1, $y1, $x2, $y2, $this->color['bars_2']);
					imagerectangle($this->img, $x1, $y1, $x2, $y2, $this->color['axis_line']);
					
				}

				$size = round(($this->y[$i] / $this->higher_value) * $higher_value_size);
				$x1   = $cero;
				$y1   = $this->graphic_area_y1 + $y + $ancho_barra;
				$x2   = $cero + $size;
				$y2   = $y1 + $ancho_barra;
				
				$y   += $this->ancho_barra;
				
				imagefilledrectangle($this->img, $x1, $y1, $x2, $y2, $this->color['bars']);
				imagerectangle($this->img, $x1, $y1, $x2, $y2, $this->color['axis_line']);
				
				//$font_labels = ($this->escala == 1) ? $this->size_ttf_axis_labels : $this->size_ttf_axis_labels;
				$font_labels = $this->font_ttf_axis_labels;
				$size_fonts_labels = $this->size_ttf_axis_labels;
				
				$posx_labels_y = ($this->graphic_area_x1 - ($this->string_width($parameter,$font_labels,$size_fonts_labels))-5);
				
				//Labels eje y
				//imagestring($this->img, $font_labels, $posx_labels_y, ($y1+2), $parameter, $this->color['axis_values']);
				$this->writeText($size_fonts_labels, $posx_labels_y, ($y1+12), $parameter, $this->color['axis_values'],$font_labels);
			}
		}

		/**
        * Draw graphic: DOTS or LINE
        */
		else if (preg_match("/^(3|4|11|12|14)$/", $this->type))
		{

			$x[0] = $this->graphic_area_x1+1;
			$x_offset = ($this->space_between_dots / 2);  //Distancia entre el 0 y el primero punto en el eje x

			foreach ($this->x as $i => $parameter)
			{
				if ($this->graphic_2_exists == true) {
					$size  = round($this->z[$i] * $higher_value_size / $this->higher_value);
					$z[$i] = $this->graphic_area_y2 - $size;
				}

				$alt   = (($i % 2 == 0) && (strlen($this->biggest_x) > 5)) ? 15 : 2;
				$size  = round($this->y[$i] * $higher_value_size / $this->higher_value);
				$y[$i] = $this->graphic_area_y2 - $size;

				//Labels eje x
				if ($this->type == 4){
					//imagestring($this->img, $this->type_font_other, ($x[$i] - (strlen($parameter)*7/2 )) + $x_offset, ($this->graphic_area_y2+$alt+2), $parameter, $this->color['axis_values']);
					$this->writeText($this->size_ttf_axis_labels, ($x[$i] - (strlen($parameter)*7/2 )) + $x_offset, ($this->graphic_area_y2+$alt+2), $parameter, $this->color['axis_values'],$this->font_ttf_axis_labels);
				}
				else if ($this->type == 12){
					//imagestring($this->img, $this->type_font_other, ($x[$i] - (strlen($parameter)*7/2 )) + $x_offset, ($this->graphic_area_y3+15+$alt+2), $parameter, $this->color['axis_values']);
					$this->writeText($this->size_ttf_axis_labels, ($x[$i] - (strlen($parameter)*7/2 )) + $x_offset, ($this->graphic_area_y3+15+$alt+2), $parameter, $this->color['axis_values'],$this->font_ttf_axis_labels);
				}

				$x[$i+1] = $x[$i] + $this->space_between_dots;
			}

			foreach ($x as $i => $value_x)
			{
				if ($this->graphic_2_exists == true)
				{
					if (isset($z[$i+1])) {
						// Draw lines
						if ($this->type == 4 || $this->type == 11 || $this->type == 12 || $this->type == 14)
						{
							//imageline($this->img, $x[$i]+$x_offset, $z[$i], $x[$i+1]+$x_offset, $z[$i+1], $this->color['line_2']);
							//imageline($this->img, $x[$i]+$x_offset, ($z[$i]+1), $x[$i+1]+$x_offset, ($z[$i+1]+1), $this->color['line_2']);
							
							imageSmoothAlphaLine ($this->img, $x[$i]+$x_offset, $z[$i], $x[$i+1]+$x_offset, $z[$i+1], 0, 102, 255, 0);
							//imageSmoothAlphaLine ($this->img, $x[$i]+$x_offset, ($z[$i]+1), $x[$i+1]+$x_offset, ($z[$i+1]+1), 0, 102, 255, 0);
							
						}
						imagefilledrectangle($this->img, $x[$i]-1+$x_offset, $z[$i]-1, $x[$i]+2+$x_offset, $z[$i]+2, $this->color['line_2']);
					}
					else { // Draw last dot
						imagefilledrectangle($this->img, $x[$i-1]-1+$x_offset, $z[$i-1]-1, $x[$i-1]+2+$x_offset, $z[$i-1]+2, $this->color['line_2']);
					}
				}

				if (count($y) > 1)
				{
					if (isset($y[$i+1])) {
						// Draw lines
						if ($this->type == 4 || $this->type == 11 || $this->type == 12 || $this->type == 14)
						{
							//imageline($this->img, $x[$i]+$x_offset, $y[$i], $x[$i+1]+$x_offset, $y[$i+1], $this->color['line']);
							//imageline($this->img, $x[$i]+$x_offset, ($y[$i]+1), $x[$i+1]+$x_offset, ($y[$i+1]+1), $this->color['line']);
							
							imageSmoothAlphaLine ($this->img, $x[$i]+$x_offset, $y[$i], $x[$i+1]+$x_offset, $y[$i+1], 170, 170, 170, 0);
							//imageSmoothAlphaLine ($this->img, $x[$i]+$x_offset, ($y[$i]+1), $x[$i+1]+$x_offset, ($y[$i+1]+1), 210, 210, 255, 0);
							
						}
						imagefilledrectangle($this->img, $x[$i]-1+$x_offset, $y[$i]-1, $x[$i]+2+$x_offset, $y[$i]+2, $this->color['line']);
					}
					else { // Draw last dot
						imagefilledrectangle($this->img, $x[$i-1]-1+$x_offset, $y[$i-1]-1, $x[$i-1]+2+$x_offset, $y[$i-1]+2, $this->color['line']);
					}
				}

			}
		}


		/**
        * Draw graphic: PIE or DONUT
        */
		else if (preg_match("/^(5|6)$/", $this->type))
		{
			
			$center_x = ($this->graphic_area_x1 + $this->graphic_area_x2) / 2;
			$center_y = ($this->graphic_area_y1 + $this->graphic_area_y2) / 2;
			
			$width    = $this->graphic_area_width;
			$height   = $this->graphic_area_height;
			$sizes    = array();

			foreach ($this->x as $i => $parameter)
			{
				if ($this->y[$i] < 0.01)	$this->y[$i] = 0.01;
				
				$size    = $this->y[$i] * 360 / $this->sum_total;
				$sizes[] = $size;
			}
			
			$color_a['arc_1']        = array( 0, 102, 255, 0);
			$color_a['arc_2']        = array( 73, 146, 255, 0);
			$color_a['arc_3']        = array( 152, 193, 255, 0);
			$color_a['arc_4']        = array( 230, 240, 255, 0);
			$color_a['arc_5']        = array( 222, 223, 225, 0);
			$color_a['arc_6']        = array( 202, 203, 205, 0);
			$color_a['arc_7']        = array( 188, 197, 210, 0);
			$color_a['arc_8']        = array( 149, 149, 149, 0);
			$color_a['arc_9']        = array( 83, 95, 111, 0);
			$color_a['arc_1_shadow'] = array(  19, 78, 166, 0);
			$color_a['arc_2_shadow'] = array(  70, 108,  165, 0);
			$color_a['arc_3_shadow'] = array( 142, 169, 210, 0);
			$color_a['arc_4_shadow'] = array( 220, 220, 220, 0);
			$color_a['arc_5_shadow'] = array( 202, 203, 205, 0);
			$color_a['arc_6_shadow'] = array( 181, 182, 184, 0);
			$color_a['arc_7_shadow'] = array( 144, 145, 147, 0);
			$color_a['arc_8_shadow'] = array( 112, 112, 112, 0);
			$color_a['arc_9_shadow'] = array( 59, 64, 72, 0);
			
			// Draw PIE
			if ($this->type == 5)
			{
				$shadow_height = 15;
				
				// Draw shadow
				//Con el script de antialias arc, toca pintar las sombra en sentido inverso para los arcos cuyo start este entre 270 y 90 grados
				//Para los que estan entre 90 y 270, se pinta normal
				$sum_start = 0;
				$i = count($sizes)-1;
				$hem_ori = 1;
				while ($hem_ori == 1 && $i >= 0){
					$num_color = $i + 1;
					$size = $sizes[$i];
					
					if ($num_color > 9) {
						$num_color -= 9;
					}
					$color = 'arc_' . $num_color . '_shadow';

					$start = 90 - $sum_start - $size;
					
					if ($start < -90)	$hem_ori = 0;
					
					$start_r = $start / 180.0 * M_PI;
					$size_r = $size / 180.0 * M_PI;
					
					for ($j = $shadow_height;$j> 0;$j--){
						imageSmoothArc ( $this->img, $center_x, ($center_y+$j), $width,$height, $color_a[$color],$start_r , ($start_r+$size_r));
					}
					
					$sum_start += $size;

					$i--;
				}
				
				// Draw shadow
				//Para los que estan entre 90 y 270, se pinta normal
				$sum_start = 0;
				$i = 0;
				$hem_occ = 1;
				$start = 90;
				while ($hem_occ == 1 && $i < count($sizes)){
					$num_color = $i + 1;
					$size = $sizes[$i];
					
					if ($num_color > 9) {
						$num_color -= 9;
					}
					$color = 'arc_' . $num_color . '_shadow';

					if ($start + $size > 270)	$hem_occ = 0;
						
					$start_r = $start / 180.0 * M_PI;
					$size_r = $size / 180.0 * M_PI;
					
					for ($j = $shadow_height;$j> 0;$j--){
						imageSmoothArc ( $this->img, $center_x, ($center_y+$j), $width,$height, $color_a[$color],$start_r , ($start_r+$size_r));
					}
					
					$start += $size;
					
					$i++;
				}
			
				$start = 90;

				// Draw pieces
				foreach ($sizes as $i => $size)
				{
					$num_color = $i + 1;
					while ($num_color > 9) {
						$num_color -= 9;
					}
					$color = 'arc_' . $num_color;
					
					$start_r = number_format($start / 180.0 * M_PI,2);  //2 decimales para que no existan errores de sobreposicion
					$size_r = number_format($size / 180.0 * M_PI,2);
					
					//imagefilledarc($this->img, $center_x, $center_y, ($width+2), ($height+2), $start, ($start+$size), $this->color[$color], IMG_ARC_EDGED);
					imageSmoothArc ( $this->img, $center_x, $center_y, ($width),($height), $color_a[$color],$start_r , ($start_r+$size_r));
					$start += $size;
				}
			}

			// Draw DONUT
			else if ($this->type == 6)
			{
				foreach ($sizes as $i => $size)
				{
					$num_color = $i + 1;
					while ($num_color > 7) {
						$num_color -= 5;
					}
					$color        = 'arc_' . $num_color;
					$color_shadow = 'arc_' . $num_color . '_shadow';
					imagefilledarc($this->img, $center_x, $center_y, $width, $height, $start, ($start+$size), $this->color[$color], IMG_ARC_PIE);
					$start += $size;
				}
				imagefilledarc($this->img, $center_x, $center_y, 100, 100, 0, 360, $this->color['background'], IMG_ARC_PIE);
				imagearc($this->img, $center_x, $center_y, 100, 100, 0, 360, $this->color['bg_legend']);
				imagearc($this->img, $center_x, $center_y, ($width+1), ($height+1), 0, 360, $this->color['bg_legend']);
			}
		}

		//Texto Extra
		if ($this->texto_extra != ""){
			$this->drawTextExtra(155,144);
		}

		if ($this->credits == true) {
			$this->draw_credits();
		}
		
		if ($stdout == 1){
			//$this->writeText($this->size_font_copyright, 20, $this->height - 5, $this->pie, $this->color['copyright'],$this->type_font_copyright);
			//header("Content-type: image/png");
			//imagepng($this->img);
		}

		if ($this->border == 1) {
			$this->draw_border();
		}
		
		return $this->img;

		//imagedestroy($this->img);
		//exit;
	}

	function calculate_width_minificha(){
		
		//GRAFICAS CON TABLA RESUMEN : CHECK DEL ANCHO DE LOS LABELS EJEX
		if (preg_match("/^(10|11|13|14)$/", $this->type)){

			$ancho_x_label_biggest = $this->string_width($this->biggest_x,$this->font_ttf_axis_labels,$this->size_ttf_axis_labels);

			if ($ancho_x_label_biggest > $this->space_between_bars){
				$factor = 0.5;
				$this->space_between_bars = $ancho_x_label_biggest;
				$this->ancho_barra = $factor*$ancho_x_label_biggest;
				
			}
			
		}		

		switch ($this->type){
			// Vertical bars
			case 1:
				$offset_box_legend = 22;
				$this->legend_box_width   = ($this->legend_exists == true) ? ($this->string_width($this->biggest_graphic_name, $this->font_ttf_legend,$this->size_ttf_axis_labels) + $offset_box_legend) : 0;
				$this->graphic_area_width = ($this->space_between_bars * $this->total_parameters) + 10;
				$this->graphic_area_x1   += $this->string_width(($this->higher_value_str), $this->font_ttf_legend,$this->size_ttf_axis_labels);
				$this->width += $this->graphic_area_x1 + 20;
				$this->width += ($this->legend_exists == true) ? 50 : (($this->string_width(($this->axis_x), $this->font_ttf_legend,$this->size_ttf_axis_labels)) + 10);
				break;

				// Histograma
			case 9:
				$this->legend_box_width   = ($this->legend_exists == true) ? ($this->string_width($this->biggest_graphic_name, $this->font_ttf_legend,$this->size_ttf_axis_labels) + 25) : 0;
				$this->graphic_area_width = ($this->space_between_bars * $this->total_parameters);
				$this->graphic_area_x1   += $this->string_width(($this->higher_value_str), $this->font_ttf_legend,$this->size_ttf_axis_labels);
				$this->width += $this->graphic_area_x1 + 20;
				$this->width += ($this->legend_exists == true) ? 50 : (($this->string_width(($this->axis_x), $this->font_ttf_legend,$this->size_ttf_axis_labels)) + 10);
				break;

				// Barras verticales con tabla resumen
			case 10:
				//$this->graphic_area_width = ($this->space_between_bars * $this->total_parameters) + 30;
				$this->graphic_area_width = ($this->space_between_bars * $this->total_parameters);
				$this->graphic_area_x1   += $this->string_width(($this->higher_value_str), $this->font_ttf_legend,$this->size_ttf_axis_labels);
				$this->width += $this->graphic_area_x1 + 0;
				$this->width += $this->string_width($this->axis_x,$this->font_ttf_legend,$this->size_ttf_axis_labels);
				break;

				// Barras Acumuladas
			case 7:
				$offset_box_legend = 22;
				$this->legend_box_width   = ($this->legend_exists == true) ? ($this->string_width($this->biggest_graphic_name, $this->font_ttf_legend,$this->size_ttf_axis_labels) + $offset_box_legend) : 0;
				$this->graphic_area_width = ($this->space_between_bars * $this->total_parameters) + 10;
				$this->graphic_area_x1   += $this->string_width(($this->higher_value_str), $this->font_ttf_legend,$this->size_ttf_axis_labels);
				$this->width += $this->graphic_area_x1 + 20;
				$this->width += ($this->legend_exists == true) ? 50 : (($this->string_width(($this->axis_x), $this->font_ttf_legend,$this->size_ttf_axis_labels)) + 10);
				break;

				// Barras Acumuladas con tabla resumen
			case 13:
				$this->graphic_area_width = ($this->space_between_bars * $this->total_parameters);
				$this->graphic_area_x1   += $this->string_width(($this->higher_value_str), $this->font_ttf_legend,$this->size_ttf_axis_labels);
				$this->width += $this->graphic_area_x1 + 0;
				$this->width += ($this->string_width(($this->axis_x), $this->font_ttf_legend,$this->size_ttf_axis_labels) + 0);
				break;

				// Vertical bars con leyenda
			case 8:
				$col = round($this->total_parameters / $this->num_params_legend);
				$this->legend_box_width   = $col * ($this->string_width($this->biggest_x, $this->font_ttf_legend,$this->size_ttf_axis_labels)) + 45;

				$this->graphic_area_width = ($this->space_between_bars * $this->total_parameters) + 30;
				$this->graphic_area_x1   += $this->string_width(($this->higher_value_str), $this->font_ttf_legend,$this->size_ttf_axis_labels);
				$this->width += $this->graphic_area_x1 + 20;
				$this->width += 50;
				break;

			// Horizontal bars
			case 2:
				$offset_box_legend = 22;
				$this->legend_box_width   = ($this->legend_exists == true) ? ($this->string_width($this->biggest_graphic_name, $this->font_ttf_legend,$this->size_ttf_legend,$this->size_ttf_axis_labels) + $offset_box_legend) : 0;
				//$this->graphic_area_width = ($this->string_width($this->higher_value_str, $this->font_ttf_legend,$this->size_ttf_axis_labels) > 50) ? (5 * ($this->string_width($this->higher_value_str, $this->font_ttf_legend,$this->size_ttf_axis_labels)) * 0.85) : 200;
				$this->graphic_area_width = 150;
				$this->graphic_area_x1 += $this->string_width(($this->biggest_x), $this->font_ttf_legend,$this->size_ttf_axis_labels) + 10;
				$this->width += ($this->legend_exists == true) ? 10 : ($this->string_width(($this->axis_y), $this->font_ttf_legend,$this->size_ttf_axis_labels) + 30);
				$this->width += $this->graphic_area_x1;
				
				break;

			// Piramide
			case 15:
				//$offset_box_legend = 10;
				//$this->legend_box_width   = ($this->legend_exists == true) ? ($this->string_width($this->biggest_graphic_name, $this->font_ttf_legend,$this->size_ttf_legend,$this->size_ttf_axis_labels) + $offset_box_legend) : 0;
				//$this->graphic_area_width = ($this->string_width($this->higher_value_str, $this->font_ttf_legend,$this->size_ttf_axis_labels) > 50) ? (5 * ($this->string_width($this->higher_value_str, $this->font_ttf_legend,$this->size_ttf_axis_labels)) * 0.85) : 200;
				$this->graphic_area_width = 150*2;
				$this->graphic_area_x1 += $this->string_width(($this->biggest_x), $this->font_ttf_legend,$this->size_ttf_axis_labels) + 10;
				//$this->width += ($this->legend_exists == true) ? 10 : ($this->string_width(($this->axis_y), $this->font_ttf_legend,$this->size_ttf_axis_labels) + 30);
				$this->width += $this->graphic_area_x1;
				$this->width += 50;
				
				break;
				
				// Dots
			case 3:
				$offset_box_legend = 25;
				$this->legend_box_width   = ($this->legend_exists == true) ? ($this->string_width($this->biggest_graphic_name, $this->font_ttf_legend,$this->size_ttf_legend,$this->size_ttf_axis_labels) + $offset_box_legend) : 0;
				$this->graphic_area_width = ($this->space_between_dots * $this->total_parameters) - 10;
				$this->graphic_area_x1   += $this->string_width(($this->higher_value_str), $this->font_ttf_legend,$this->size_ttf_axis_labels);
				$this->width += $this->graphic_area_x1 + 20;
				$this->width += ($this->legend_exists == true) ? 40 : (($this->string_width(($this->axis_x), $this->font_ttf_legend,$this->size_ttf_axis_labels)) + 10);
				break;

				// Lines
			case 4:
				$offset_box_legend = 30;
				$this->legend_box_width   = ($this->legend_exists == true) ? ($this->string_width($this->biggest_graphic_name, $this->font_ttf_legend,$this->size_ttf_legend) + $offset_box_legend) : 0;
				$this->graphic_area_width = ($this->space_between_dots * $this->total_parameters) - 10;
				$this->graphic_area_x1   += $this->string_width(($this->higher_value_str), $this->font_ttf_legend,$this->size_ttf_legend);
				$this->width += $this->graphic_area_x1 + 20;
				$this->width += ($this->legend_exists == true) ? 40 : (($this->string_width(($this->axis_x), $this->font_ttf_legend,$this->size_ttf_axis_labels)) + 10);
				break;

				// Histograma de lineas
			case 12:
				$offset_box_legend = 30;
				$this->legend_box_width   = ($this->legend_exists == true) ? ($this->string_width($this->biggest_graphic_name, $this->font_ttf_legend,$this->size_ttf_legend) + $offset_box_legend) : 0;
				$this->graphic_area_width = ($this->space_between_dots * $this->total_parameters) - 10;
				$this->graphic_area_x1   += $this->string_width(($this->higher_value_str), $this->font_ttf_legend,$this->size_ttf_legend);
				$this->width += $this->graphic_area_x1 + 20;
				$this->width += ($this->legend_exists == true) ? 40 : (($this->string_width(($this->axis_x), $this->font_ttf_legend,$this->size_ttf_axis_labels)) + 10);
				break;

				// Lines con tabla resumen
			case 11:
				$this->graphic_area_width = ($this->space_between_bars * $this->total_parameters);
				$this->graphic_area_x1   += $this->string_width(($this->higher_value_str), $this->font_ttf_legend,$this->size_ttf_legend);
				$this->width += $this->graphic_area_x1 + 0;

				break;
			// Histograma de lineas con tabla resumen
			case 14:
				$this->graphic_area_width = ($this->space_between_dots * $this->total_parameters) - 5;
				$this->graphic_area_x1   += $this->string_width(($this->higher_value_str), $this->font_ttf_legend,$this->size_ttf_legend);
				$this->width += $this->graphic_area_x1 + 0;
				$this->width += (($this->string_width(($this->axis_x), $this->font_ttf_legend,$this->size_ttf_axis_labels)) + 10);
				break;
				// Pie
			case 5:
				$offset_box_legend = 75;
				$this->legend_box_width   = $this->string_width($this->biggest_x, $this->font_ttf_legend,$this->size_ttf_legend) + $offset_box_legend;
				$this->graphic_area_width = 140;
				$this->width += 10;
				break;

				// Donut
			case 6:
				$this->legend_box_width   = $this->string_width($this->biggest_x, $this->font_ttf_legend,$this->size_ttf_legend) + 85;
				$this->graphic_area_width = 180;
				$this->width += 90;
				break;
		}

		$this->width += $this->graphic_area_width;
		
		if (!preg_match("/^(1|3|4|7|9|12|10|11|13)$/", $this->type))	$this->width += $this->legend_box_width;
		
		//GRAFICAS CON TABLA RESUMEN : CHECK DEL ANCHO DE LA LEYENDA
		if (preg_match("/^(10|11|13|14)$/", $this->type)){
			$l_a = $this->graphic_1;
			if ($this->graphic_2_exists && strlen($this->graphic_2) > strlen($this->graphic_1))	$l_a = $this->graphic_2;

			$ancho_legend = $this->string_width($l_a,$this->font_ttf_table_data_legend,$this->size_ttf_table_data_legend);

			$this->graphic_area_x1 = $ancho_legend + 15;
			$this->width += $ancho_legend + 15;
		}

		//BORRA EL ESPACIO EN BLANCO FINAL
		//$this->width -= ($this->legend_exists == true) ? 45 : 20;

		//echo "--- $this->width_title xxx $this->width";
		
		//SI EL TITULO ES MAS LARGO QUE LA GRAFICA
		$title_check = 0;
		if ($this->width < $this->width_title){
			$this->width = $this->width_title;
			$title_check = 1;
		}

		//GRAFICA CON COPYRIGHT
		
		$this->pie = "OCHA Colombia".$this->texto_1;
		$width_copy = $this->string_width($this->pie,$this->type_font_copyright,$this->size_font_copyright);
		if ($this->width < $width_copy && $title_check == 0){
			$this->width = $width_copy;
			$this->x_pie = 0;
		}
		
		$this->graphic_area_x2 = $this->graphic_area_x1 + $this->graphic_area_width;

		//DISTANCIA ENTRE GRAFICA Y LEGEND
		if (preg_match("/^(1|3|4|7|9|12)$/", $this->type))	$this->legend_box_x1   = $this->graphic_area_x2 - $this->legend_box_width;
		else 									$this->legend_box_x1   = $this->graphic_area_x2 + 5;
		$this->legend_box_x2   = $this->legend_box_x1 + $this->legend_box_width;
		
	}
	
	function calculate_height_minificha()
	{
		switch ($this->type)
		{
			// Vertical bars
			case 1:
				$this->legend_box_height   = ($this->graphic_2_exists == true) ? 40 : 0;
				$this->graphic_area_height = 150;
				$this->height += 65;
				break;

				// Histograma
			case 9:
				$this->legend_box_height   = ($this->graphic_2_exists == true) ? 40 : 0;
				$this->graphic_area_height = 80;
				break;
				// Histograma Lineas
			case 12:
				$this->legend_box_height   = ($this->graphic_2_exists == true) ? 40 : 0;
				$this->graphic_area_height = 80;
				break;

			// Histograma Líneas Tabla resumen
			case 14:
				$this->legend_box_height   = 0;
				$this->table_data_box_height   = ($this->graphic_2_exists == true) ? 40 : 20;
				$this->graphic_area_height = 80;
				$this->height += $this->table_data_box_height;
				break;
				
				// Barras Veticales con tabla resumen
			case 10:
				$this->legend_box_height   = 0;
				$this->table_data_box_height   = ($this->graphic_2_exists == true) ? 40 : 20;
				$this->graphic_area_height = 140;
				$this->height += 45 + $this->table_data_box_height;
				break;

				// Barras Acumuladas
			case 7:
				$this->legend_box_height   = ($this->graphic_2_exists == true) ? 40 : 0;
				$this->graphic_area_height = 150;
				$this->height += 65;
				break;
				
				// Barras Acumuladas con tabla resumen
			case 13:
				$this->legend_box_height   = 0;
				$this->table_data_box_height   = ($this->graphic_2_exists == true) ? 40 : 20;
				$this->graphic_area_height = 140;
				$this->height += 45 + $this->table_data_box_height;
				break;
				

				// Vertical bars con leyenda
			case 8:
				$this->legend_box_height   = (!empty($this->axis_x)) ? 30 : 5;
				//$this->legend_box_height  += (14 * $this->total_parameters);
				$this->legend_box_height  += (14 * $this->num_params_legend);
				$this->graphic_area_height = 150;
				$this->height += 65;
				break;

				// Horizontal bars
			case 2:
				$this->legend_box_height   = ($this->graphic_2_exists == true) ? 40 : 0;
				$this->graphic_area_height = ($this->space_between_bars * $this->total_parameters) + 10;
				$this->height += 65;
				break;

				// Piramide
			case 15:
				$this->legend_box_height   = 10;
				$this->graphic_area_height = ($this->ancho_barra * $this->total_parameters) + $this->ancho_barra;
				$this->height += 65;
				break;
				
				// Dots
			case 3:
				$this->legend_box_height   = ($this->graphic_2_exists == true) ? 40 : 0;
				$this->graphic_area_height = 150;
				$this->height += 65;
				break;

				// Lines
			case 4:
				$this->legend_box_height   = ($this->graphic_2_exists == true) ? 40 : 0;
				$this->graphic_area_height = 150;
				$this->height += 65;
				break;

				// Lineas con tabla resumen
			case 11:
				$this->legend_box_height   = 0;
				$this->table_data_box_height   = ($this->graphic_2_exists == true) ? 40 : 20;
				$this->graphic_area_height = 140;
				$this->height += 45 + $this->table_data_box_height;
				break;

				// Pie
			case 5:
				$this->legend_box_height   = (!empty($this->axis_x)) ? 30 : 5;
				$this->legend_box_height  += (14 * $this->total_parameters);
				//$this->graphic_area_height = 150;
				$this->graphic_area_height = 70;
				$this->height += 50;
				break;

				// Donut
			case 6:
				$this->legend_box_height   = (!empty($this->axis_x)) ? 30 : 5;
				$this->legend_box_height  += (14 * $this->total_parameters);
				$this->graphic_area_height = 180;
				$this->height += 50;
				break;
		}

		$this->height += $this->height_title;
		$this->height += ($this->legend_box_height > $this->graphic_area_height) ? ($this->legend_box_height - $this->graphic_area_height) : 0;
		$this->height += $this->graphic_area_height;

		if ($this->type == 9 || $this->type == 12 || $this->type == 14){
			$this->height += 2*$this->graphic_area_height;
		}
		
		//Aumenta para el copyright
		//if ($this->texto_1 != ''){
			$this->height += 10;
		//}
		
		$this->graphic_area_y2 = $this->graphic_area_y1 + $this->graphic_area_height;
		if (preg_match("/^(1|3|4|7|9|12)$/", $this->type))	$this->legend_box_y1   = $this->graphic_area_y1 - 30;
		else 									$this->legend_box_y1   = $this->graphic_area_y1 + 10;
		$this->legend_box_y2   = $this->legend_box_y1 + $this->legend_box_height;
		
	}

	function draw_legend()
	{
		$x1 = $this->legend_box_x1;
		$y1 = $this->legend_box_y1;
		$x2 = $this->legend_box_x2;
		$y2 = $this->legend_box_y2;

		$space_text = 15;  //Espacio entre el rectangulo del color y el texto
		
		$ajueste_y_fuente = 9;  //Ajuste en pixeles para que el texto quede alineado con el rectangulo

		//imagefilledrectangle($this->img, $x1, $y1, $x2, $y2, $this->color['bg_legend']);
		imagerectangle($this->img, $x1, $y1, $x2, $y2, $this->color['bg_legend']);
		$x = $x1 + 5;
		$y = $y1 + 5;


		// Draw legend values for VERTICAL BARS, HORIZONTAL BARS, DOTS and LINES
		if (preg_match("/^(1|2|3|4|7|9|12)$/", $this->type)){
			
			//echo $x1;
			$color_1 = (preg_match("/^(1|2|7|9|12)$/", $this->type)) ? $this->color['bars']   : $this->color['line'];
			$color_2 = (preg_match("/^(1|2|7|9|12)$/", $this->type)) ? $this->color['bars_2'] : $this->color['line_2'];

			imagefilledrectangle($this->img, $x, $y, ($x+10), ($y+10), $color_1);
			imagerectangle($this->img, $x, $y, ($x+10), ($y+10), $this->color['title']);
			//imagestring($this->img, $this->type_font_other, ($x+$space_text), ($y-2), $this->graphic_1, $this->color['axis_values']);
			$this->writeText($this->size_ttf_legend, ($x+$space_text), ($y+$ajueste_y_fuente), $this->graphic_1, $this->color['axis_values'],$this->font_ttf_legend);
			
			$y += 20;
			imagefilledrectangle($this->img, $x, $y, ($x+10), ($y+10), $color_2);
			imagerectangle($this->img, $x, $y, ($x+10), ($y+10), $this->color['title']);
			//imagestring($this->img, $this->type_font_other, ($x+$space_text), ($y-2), $this->graphic_2, $this->color['axis_values']);
			$this->writeText($this->size_ttf_legend, ($x+$space_text), ($y+$ajueste_y_fuente), $this->graphic_2, $this->color['axis_values'],$this->font_ttf_legend);
		}

		// Draw legend values for PIE or DONUT
		else if (preg_match("/^(5|6)$/", $this->type))
		{
			if (!empty($this->axis_x))
			{
				//imagestring($this->img, $this->type_font_other, ((($x1+$x2)/2) - (strlen($this->axis_x)*7/2)), $y, $this->axis_x, $this->color['title']);
				$this->writeText($this->size_ttf_legend, ((($x1+$x2)/2) - (strlen($this->axis_x)*7/2)), $y, $this->axis_x, $this->color['title'],$this->font_ttf_legend);
				$y += 25;
			}

			$num = 1;

			foreach ($this->x as $i => $parameter)
			{
				while ($num > 9) {
					$num -= 9;
				}
				$color = 'arc_' . $num;

				$percent = number_format(round(($this->y[$i] * 100 / $this->sum_total), 2), 2, ".", "") . ' %';
				$less    = (strlen($percent) * 7);

				/*if ($num != 1) {
				imageline($this->img, ($x1+15), ($y-2), ($x2-5), ($y-2), $this->color['bg_lines']);
				}*/

				imagefilledrectangle($this->img, $x, $y, ($x+10), ($y+10), $this->color[$color]);
				imagerectangle($this->img, $x, $y, ($x+10), ($y+10), $this->color['title']);
				//imagestring($this->img, $this->type_font_other, ($x+$space_text), ($y-2), $parameter, $this->color['axis_values']);
				$this->writeText($this->size_ttf_legend, ($x+$space_text), ($y+$ajueste_y_fuente), $parameter, $this->color['axis_values'],$this->font_ttf_legend);
				//imagestring($this->img, $this->type_font_other, ($x2-$less), ($y-2), $percent, $this->color['axis_values']);
				$this->writeText($this->size_ttf_legend, ($x2-$less), ($y+$ajueste_y_fuente), $percent, $this->color['axis_values'],$this->font_ttf_legend);
				$y += 14;
				$num++;
			}
		}

		// Draw legend values for Barras Verticales con leyenda
		else if ($this->type == 8)
		{
			if (!empty($this->axis_x))
			{
				//imagestring($this->img, $this->type_font_other, ((($x1+$x2)/2) - (strlen($this->axis_x)*7/2)), $y, $this->axis_x, $this->color['title']);
				$this->writeText($this->size_ttf_legend, ((($x1+$x2)/2) - (strlen($this->axis_x)*7/2)), $y, $this->axis_x, $this->color['title'],$this->font_ttf_legend);
				$y += 25;
			}

			$y_ini = $y;
			$x_ini = $x;
			$max_strlen = 0;
			$c = 0;
			$p = 0;
			foreach ($this->x as $i => $parameter)
			{
				$len_para = $this->string_width($this->biggest_x, $this->font_ttf_table_data);
				if ($len_para > $max_strlen)	$max_strlen = $len_para;

				if (($p % $this->num_params_legend) == 0){
					$y = $y_ini;
					$x = $c * $max_strlen + $x_ini + 20*$c;
					$c++;
				}

				imagefilledrectangle($this->img, $x, $y, ($x+10), ($y+10), $this->color["8_color_$i"]);
				imagerectangle($this->img, $x, $y, ($x+10), ($y+10), $this->color['title']);
				//imagestring($this->img, $this->type_font_other, ($x+$space_text), ($y-2), $parameter, $this->color['axis_values']);
				$this->writeText($this->size_ttf_legend, ($x+$space_text), ($y+$ajueste_y_fuente), $parameter, $this->color['axis_values'],$this->font_ttf_legend);
				$y += 14;

				$p++;
			}
		}
	}

	function draw_table_data(){

		$alto_fila = 16;
		$alto_rect_legend = 5;
		$space_text = $alto_rect_legend + 3;  //Espacio entre el rectangulo del color y el texto
		
		$y_ini = $this->graphic_area_y2;
		//HISTOGRAMA LINEAS CON TABLA RESUMEN
		if ($this->type == 14){
			$y_ini = $this->graphic_area_y3 + 15;	
		}
		
		$y = $y_ini + $alto_fila;
		
		$x1   = $this->graphic_area_x1 + 30;
		$alto_laterales = $alto_fila;
		$xy_string = $y_ini + ($alto_fila/2) + ($this->string_height($this->size_ttf_table_data_legend)/2);
		$yy_string = $y + ($alto_fila/2) + ($this->string_height($this->size_ttf_table_data_legend)/2);
		$yz_string = $y + ($alto_fila/2) + ($this->string_height($this->size_ttf_table_data_legend)/2) + $alto_fila;
		$yy_string_legend = $y + ($alto_fila/2) + ($this->string_height($this->size_ttf_table_data_legend)/2);
		$yz_string_legend = $yy_string_legend + $alto_fila;
		
		$y_rec_legend = $y + (($alto_fila - $alto_rect_legend)/2); 

		//Hace el check de cada label de x, para buscar el caracter |, y hacer 2 lineas
		if ($this->labels_x_doble_linea == 1){
			$yy_string += $alto_fila;
			$yz_string += $alto_fila;
			$yy_string_legend += $alto_fila;
			$yz_string_legend += $alto_fila;
			$y_rec_legend += $alto_fila;
		}

		
		//**** LEYENDA *** //
		$color_1 = $this->color['bars'];
		$color_2 = $this->color['bars_2'];
		$x = 0;
		$xx_legend = $x + 3;

		imagefilledrectangle($this->img, $xx_legend, $y_rec_legend, ($xx_legend+$alto_rect_legend), ($y_rec_legend+$alto_rect_legend), $color_1);
		imagerectangle($this->img, $xx_legend, $y_rec_legend, ($xx_legend+$alto_rect_legend), ($y_rec_legend+$alto_rect_legend), $this->color['title']);

		//imagestring($this->img, $this->type_font_table_data_legend, ($xx_legend+$space_text), $yy_string_legend, $this->graphic_1, $this->color['axis_values']);
		$this->writeText($this->size_ttf_table_data_legend, ($xx_legend+$space_text), $yy_string_legend, $this->graphic_1, $this->color['axis_values'],$this->font_ttf_table_data_legend);

		$num_graphics = 1;
		if ($this->graphic_2_exists == true){
			switch ($this->type){
				case 10:
					$num_graphics = 2;
				break;
				case 14:
					$num_graphics = 1;
				break;
			}

			if ($this->labels_x_doble_linea == 0){
				imageline($this->img, $x, $y + $alto_fila*2, $this->graphic_area_x2, $y + $alto_fila*2, $this->color['axis_line']);
			}
			else{
				imageline($this->img, $x, $y + $alto_fila*3, $this->graphic_area_x2, $y + $alto_fila*3, $this->color['axis_line']);
			}

			$y_rec_legend += $alto_fila;
			imagefilledrectangle($this->img, $xx_legend, $y_rec_legend, ($xx_legend+$alto_rect_legend), ($y_rec_legend+$alto_rect_legend), $color_2);
			imagerectangle($this->img, $xx_legend, $y_rec_legend, ($xx_legend+$alto_rect_legend), ($y_rec_legend+$alto_rect_legend), $this->color['title']);
			//imagestring($this->img, $this->type_font_table_data_legend, ($xx_legend+$space_text), ($yz_string_legend), $this->graphic_2, $this->color['axis_values']);
			$this->writeText($this->size_ttf_table_data_legend, ($xx_legend+$space_text), ($yz_string_legend), $this->graphic_2, $this->color['axis_values'],$this->font_ttf_table_data_legend);

			$alto_laterales = $alto_fila * 2;
		}

		//**** LEYENDA ***** //

		//Hace el check de cada label de x, para buscar el caracter |, y hacer 2 lineas
		if ($this->labels_x_doble_linea == 0){
			//LIENAS DE DIVISION DE LA TABLA HORIZONTALES
			imageline($this->img, $x, $y + $alto_fila, $this->graphic_area_x2, $y + $alto_fila, $this->color['axis_line']);
			imageline($this->img, $x, $y, $this->graphic_area_x2, $y, $this->color['axis_line']);
	
			//LIENAS DE DIVISION DE LA TABLA VERTICALES
			imageline($this->img, $this->graphic_area_x1, $y_ini , $this->graphic_area_x1, $y_ini + $alto_laterales + $alto_fila, $this->color['axis_line']);
			imageline($this->img, $this->graphic_area_x2, $y_ini , $this->graphic_area_x2, $y_ini + $alto_laterales + $alto_fila, $this->color['axis_line']);
	
			//LATERALES
			imageline($this->img, $x, $y, $x, $y + $alto_laterales, $this->color['axis_line']); //Iz
			imageline($this->img, $this->graphic_area_x2, $y, $this->graphic_area_x2, $y + $alto_laterales, $this->color['axis_line']); //Der
		}
		else{
			//LIENAS DE DIVISION DE LA TABLA HORIZONTALES
			imageline($this->img, $x, $y + $alto_fila*2, $this->graphic_area_x2, $y + $alto_fila*2, $this->color['axis_line']);
			imageline($this->img, $x, $y + $alto_fila, $this->graphic_area_x2, $y + $alto_fila, $this->color['axis_line']);
	
			//LIENAS DE DIVISION DE LA TABLA VERTICALES
			imageline($this->img, $this->graphic_area_x1, $y_ini , $this->graphic_area_x1, $y_ini + $alto_laterales + $alto_fila*2, $this->color['axis_line']);
			imageline($this->img, $this->graphic_area_x2, $y_ini , $this->graphic_area_x2, $y_ini + $alto_laterales + $alto_fila*2, $this->color['axis_line']);
	
			//LATERALES
			imageline($this->img, $x, $y + $alto_fila, $x, $y + $alto_laterales + $alto_fila, $this->color['axis_line']); //Iz
			imageline($this->img, $this->graphic_area_x2, $y, $this->graphic_area_x2, $y + $alto_laterales + $alto_fila, $this->color['axis_line']); //Der
			
		}

		//$x_linea_div_v = $this->graphic_area_x1 + $this->space_between_bars/2;
		$ajuste_x_font = 1;
		$p = 0;
		$x_linea_div_v = $this->graphic_area_x1 + (($this->space_between_bars-$this->ancho_barra*$num_graphics)/2) + ($this->ancho_barra*$num_graphics) + (($this->space_between_bars-$this->ancho_barra*$num_graphics)/2);
		$x_string_iz = $this->graphic_area_x1;
		foreach ($this->x as $i => $parameter)
		{

			$x2 = $x1 + $this->ancho_barra;
			if ($p > 0)	$x_linea_div_v += $this->space_between_bars;
			
			//Labels X
			//Hace el check de cada label de x, para buscar el caracter |, y hacer 2 lineas
			if ($this->labels_x_doble_linea == 1){
				$labels = explode("|",$this->x[$i]);
				$x_string = $ajuste_x_font +$x_string_iz + (($x_linea_div_v - $x_string_iz) - $this->string_width($labels[0],$this->font_ttf_table_data) ) / 2 ;
				//imagestring($this->img, $this->type_font_table_data, $x_string, $xy_string, $labels[0], $this->color['axis_values']);
				//imagestring($this->img, $this->type_font_table_data, $x_string, $xy_string+$alto_fila, $labels[1], $this->color['axis_values']);
				
				$this->writeText($this->size_ttf_table_data, $x_string, $xy_string, $labels[0], $this->color['axis_values'],$this->font_ttf_table_data);
				$this->writeText($this->size_ttf_table_data, $x_string, $xy_string+$alto_fila, $labels[1], $this->color['axis_values'],$this->font_ttf_table_data);
			}
			else{
				$x_string = $ajuste_x_font + $x_string_iz + (($x_linea_div_v - $x_string_iz) - $this->string_width($this->x[$i],$this->font_ttf_table_data) ) / 2;
				//imagestring($this->img, $this->type_font_table_data, $x_string, $xy_string, $this->x[$i], $this->color['axis_values']);
				$this->writeText($this->size_ttf_table_data, $x_string, $xy_string, $this->x[$i], $this->color['axis_values'],$this->font_ttf_table_data);
			}

			$x_string = $ajuste_x_font + $x_string_iz + (($x_linea_div_v - $x_string_iz) - $this->string_width(number_format($this->y[$i],$this->decimals,",","."),$this->font_ttf_table_data) ) / 2;
			//imagestring($this->img, $this->type_font_table_data, $x_string, $yy_string, number_format($this->y[$i],$this->decimals,",","."), $this->color['axis_values']);
			$this->writeText($this->size_ttf_table_data, $x_string, $yy_string, number_format($this->y[$i],$this->decimals,",",".")."", $this->color['axis_values'],$this->font_ttf_table_data);

			//if (isset($this->z[$i])) {
			if ($this->graphic_2_exists == true){
				$y2 = $y + $alto_fila;
				$x_string = $ajuste_x_font + $x_string_iz + (($x_linea_div_v - $x_string_iz) - $this->string_width(number_format($this->z[$i],$this->decimals,",","."),$this->font_ttf_table_data) ) / 2;
				//imagestring($this->img, $this->type_font_table_data, $x_string, $yz_string, number_format($this->z[$i],$this->decimals,",","."), $this->color['axis_values']);
				$this->writeText($this->size_ttf_table_data, $x_string, $yz_string, number_format($this->z[$i],$this->decimals,",","."), $this->color['axis_values'],$this->font_ttf_table_data);
			}

			//LIENAS DE DIVISION DE LA TABLA VERTICALES
			if ($p < (count($this->x) - 1)){
				if ($this->labels_x_doble_linea == 0){
					imageline($this->img, $x_linea_div_v, $y_ini , $x_linea_div_v, $y_ini + $alto_laterales + $alto_fila, $this->color['axis_line']);
				}
				else{
					imageline($this->img, $x_linea_div_v, $y_ini , $x_linea_div_v, $y_ini + $alto_laterales + $alto_fila*2, $this->color['axis_line']);
				}
			}

			$x1	+= $this->space_between_bars;
			$x_string_iz = $x_linea_div_v;
			
			$p++;
		}
	}
	
	function string_width($string, $font, $size=6) {
		
		//echo "Entro con fuente=$font --- string=$string --- size=$size<br>";
		$arr = imagettfbbox($size,0,$font,$string);
		
		return $arr[2] - $arr[0];  //x de inferior derecha - x de inferior izquierda
		
		/*$single_width = $size - 1;
		$num_espacios = strlen($string) - 1;
		
		$width = ($single_width*strlen($string)) + 2;
		
		//Ajusta si todo el string esta en mayusculas
		$ajuste = 0;
		for ($i=0;$i<strlen($string);$i++){
			$letra = $string[$i];
			if (ereg('^[A-Z]*$',$letra)){
				$ajuste++;
			}
		}
		
		return $width + $ajuste;*/
	}


	//Height con ttf fonts
	function string_height($size) {
		return $size;
	}

	function calculate_higher_value() {

		$digits   = strlen(round($this->biggest_y));
		$interval = pow(10, ($digits-1));
		
		$precision = ($this->biggest_y < 0) ? ($digits -2) : -1*($digits -2);
		
		//$this->higher_value     = round(($this->biggest_y - ($this->biggest_y % $interval) + $interval), 1);
		$this->higher_value     = ceil($this->biggest_y * pow (10, $precision) )/ pow (10, $precision);
		$this->higher_value_str = $this->number_formated($this->higher_value);
		
		//Si todos los valores son cero, pero se quiere graficar los ejes, higher_value debe ser 1, para evitar divisio por cero
		if ($this->higher_value == 0)	$this->higher_value = 1;
		
	}
	
	function calculate_lower_value() {

		$smallest = -1 * $this->smallest_y;

		//$digits   = strlen(round($smallest)); //Se resta 1 del menos
		$digits   = strlen(round(abs($smallest)));
		$interval = pow(10, ($digits-1));
		$this->lower_value     = -1*(round(($smallest - ($smallest % $interval) + $interval), 1));
		$this->lower_value_str = $this->number_formated($this->lower_value);
		//echo "<br>intervalo: $interval smallest $this->smallest_y lower_value $this->lower_value";

	}

	function number_formated($number, $dec_size = 2)
	{
		$dec_size = $this->decimals;

		if ($this->latin_notation == true) {
			return number_format(round($number, $dec_size), $dec_size, ",", ".");
		}
		return number_format(round($number, $dec_size), $dec_size, ".", ",");
	}

	function number_float($number)
	{
		if ($this->latin_notation == true) {
			$number = str_replace(".", "", $number);
			return (float)str_replace(",", ".", $number);
		}
		return (float)str_replace(",", "", $number);
	}


	function drawTextExtra($x,$y){
		$txt = explode("|",$this->texto_extra);
		$font = $this->font_ttf_table_data;
		$dy = $this->string_height($font);
		foreach($txt as $tx){
			//imagestring($this->img,$font,$x,$y,$tx,$this->color['title']);
			imagestring($this->img,$font,$x,$y,$tx,$this->color['title']);
			$y += $dy;
		}
	}
	
	function draw_credits() {
		imagestring($this->img, 1, ($this->width-120), ($this->height-10), "Powered by", $this->color['title']);
	}

	function draw_copyright($img) {
		
		$img = $this->draw_border($img);
		$pie = "OCHA Colombia".$this->texto_1;
		$copyright = imagecolorallocate($img, 50, 50, 50);
		
		$x_pie = ($this->width / 2) - ($this->string_width($pie,$this->type_font_copyright,$this->size_font_copyright) / 2);
		
		imagettftext($img,$this->size_font_copyright,0,$x_pie,$this->height - 2,$copyright,$this->type_font_copyright,$pie);

		return $img;
	}
	
	function fill_background($img,$r,$g,$b) {
		
		$w = imagesx($img);
		$y = imagesy($img);
		
		$img_tmp = imagecreatetruecolor($w,$y);
		$fondo = imagecolorallocate($img_tmp, $r, $g, $b);
		
		imagefilledrectangle($img_tmp,0,0,$w,$y,$fondo);
		imagecopy($img_tmp,$img,0,0,0,0,$w,$y);
		
		return $img_tmp;
	}
	
	function draw_border($img){
		
		$margin = 15;
		$w = imagesx($img);
		$h = imagesy($img);
		
		$img_tmp = imagecreatetruecolor($w + $margin, $h + $margin);
		
		//Transparente
		imagesavealpha($img_tmp, true);
		$trans_colour = imagecolorallocatealpha($img_tmp, 255, 255, 255, 127);
    	imagefill($img_tmp, 0, 0, $trans_colour);
		
    	$borde = imagecolorallocate($img_tmp, 150, 150, 150);
		imagerectangle($img_tmp,0,0,$w + $margin - 1,$h + $margin - 1,$borde);
		imagecopy($img_tmp,$img,$margin/2,$margin/2,0,0,$w,$h);
		
		return $img_tmp;
		
	}
	
	function load_color_palette()
	{
		switch ($this->skin)
		{
			// Office
			case 1:
				$this->color['title']       = imagecolorallocate($this->img,   0,   0, 0);
				$this->color['background']  = imagecolorallocate($this->img, 250, 250, 250);
				$this->color['axis_values'] = imagecolorallocate($this->img,  0,  0,  0);
				$this->color['axis_line']   = imagecolorallocate($this->img, 0, 0, 0);
				$this->color['bg_lines']    = imagecolorallocate($this->img, 240, 240, 240);
				$this->color['bg_legend']   = imagecolorallocate($this->img, 0, 0, 0);
				$this->color['copyright']   = imagecolorallocate($this->img, 204, 204, 204);

				if (preg_match("/^(1|2|7|9|10|13|15)$/", $this->type))
				{
					$this->color['bars']          = imagecolorallocate($this->img, 210, 210, 255);
					$this->color['bars_shadow']   = imagecolorallocate($this->img,  50, 100, 150);
					$this->color['bars_2']        = imagecolorallocate($this->img, 0, 102, 255);
					$this->color['bars_2_shadow'] = imagecolorallocate($this->img, 120, 170,  70);
				}
				else if (preg_match("/^(3|4|11|12|14)$/", $this->type))
				{
					$this->color['line']   = imagecolorallocate($this->img, 210, 210, 255);
					$this->color['line_2'] = imagecolorallocate($this->img, 0, 102, 255);
					if ($this->type == 11 || $this->type == 12 || $this->type == 14){
						$this->color['bars']          = imagecolorallocate($this->img, 210, 210, 255);
						$this->color['bars_2']        = imagecolorallocate($this->img, 0, 102, 255);
					}
				}
				else if (preg_match("/^(5|6)$/", $this->type))
				{
					$this->color['arc_1']        = imagecolorallocate($this->img, 0, 102, 255);
					$this->color['arc_2']        = imagecolorallocate($this->img, 73, 146, 255);
					$this->color['arc_3']        = imagecolorallocate($this->img, 152, 193, 255);
					$this->color['arc_4']        = imagecolorallocate($this->img, 230, 240, 255);
					$this->color['arc_5']        = imagecolorallocate($this->img, 222, 223, 225);
					$this->color['arc_6']        = imagecolorallocate($this->img, 202, 203, 205);
					$this->color['arc_7']        = imagecolorallocate($this->img, 181, 182, 184);
					$this->color['arc_8']        = imagecolorallocate($this->img, 149, 149, 149);
					$this->color['arc_9']        = imagecolorallocate($this->img, 83, 95, 111);
					$this->color['arc_1_shadow'] = imagecolorallocate($this->img,  19, 78, 166);
					$this->color['arc_2_shadow'] = imagecolorallocate($this->img,  70, 108,  165);
					$this->color['arc_3_shadow'] = imagecolorallocate($this->img, 142, 169, 210);
					$this->color['arc_4_shadow'] = imagecolorallocate($this->img, 220, 220, 220);
					$this->color['arc_5_shadow'] = imagecolorallocate($this->img, 202, 203, 205);
					$this->color['arc_6_shadow'] = imagecolorallocate($this->img, 181, 182, 184);
					$this->color['arc_7_shadow'] = imagecolorallocate($this->img, 144, 145, 147);
					$this->color['arc_8_shadow'] = imagecolorallocate($this->img, 112, 112, 112);
					$this->color['arc_9_shadow'] = imagecolorallocate($this->img, 59, 64, 72);
				}
				else if($this->type == 8){
					foreach ($this->x as $i => $parameter){
						$color = imagecolorallocate($this->img, rand(0,255), rand(0,255), rand(0,255));
						if (!in_array($color,$this->color)){
							$this->color["8_color_$i"] = $color;
						}
					}
				}
				break;
		}

	}

	function reset_values() {
		$this->title     = NULL;
		$this->axis_x    = NULL;
		$this->axis_y    = NULL;
		$this->type      = NULL;
		$this->skin      = NULL;
		$this->graphic_1 = NULL;
		$this->graphic_2 = NULL;
		$this->credits   = NULL;
		$this->decimals   = NULL;
		$this->texto_1   = NULL;
		$this->texto_extra   = NULL;
		$this->escala   = NULL;

		$this->x = $this->y = $this->z = array();
	}

	function create_query_string_array($array) {
		if (!is_array($array)) {
			return false;
		}
		$query_string = array();
		foreach ($array as $parameter => $value) {
			//$query_string[] = urlencode($parameter) . '=' . urlencode($value);
			$query_string[] = $parameter . '=' . $value;
		}
		return implode("&", $query_string);
	}

	function create_query_string() {
		$graphic['title']     = $this->title;
		$graphic['axis_x']    = $this->axis_x;
		$graphic['axis_y']    = $this->axis_y;
		$graphic['type']      = $this->type;
		$graphic['skin']      = $this->skin;
		$graphic['graphic_1'] = $this->graphic_1;
		$graphic['graphic_2'] = $this->graphic_2;
		$graphic['credits']   = $this->credits;
		$graphic['decimals']   = $this->decimals;
		$graphic['texto_1']   = $this->texto_1;
		$graphic['texto_extra']   = $this->texto_extra;
		$graphic['escala']   = $this->escala;
		$graphic['border']   = $this->border;

		foreach ($this->x as $i => $x)
		{
			$graphic['x'.$i] = $x;
			if (isset($this->y[$i])) { $graphic['y'.$i] = $this->y[$i]; }
			if (isset($this->z[$i])) { $graphic['z'.$i] = $this->z[$i]; }
		}
		return $this->create_query_string_array($graphic);
	}
	
	function escalar(){
		
		//APLICA ESCALA
		$this->width = $this->width * $this->escala;
		//$this->graphic_area_x1 = $this->graphic_area_x1 * $this->escala;
		$this->graphic_area_width *= $this->escala;
		
		$this->height *= $this->escala;
		$this->graphic_area_y1 *= $this->escala;
		$this->graphic_area_y2 *= $this->escala;
		$this->graphic_area_height *= $this->escala;
		
		$this->ancho_barra *= $this->escala;
		$this->space_between_bars *= $this->escala;
		//$this->type_font_other = $this->type_font_table_data;
		$this->type_font_title = 12;
		
	}
	
	function writeText($size,$x,$y,$text,$color,$font=''){
		
		//echo "entro con texto=$text y size=$size<br>";
		$font_ttf = ($font != '') ? $font : $this->font_ttf;
		imagettftext($this->img,$size,0,$x,$y,$color,$font_ttf,$text);
	}

}


?>
