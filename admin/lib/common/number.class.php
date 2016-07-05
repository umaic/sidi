<?php
class Number {


	/**
	 * Constructor
	 * Define las variables
	 * @access public
	 */
	function Number(){
	}


	/**
	 * Cambia el formato de la fecha
	 * @access public
	 * @param string $fecha Fecha a Formatear
	 * @param int $format_int Formato en el que viene dada la fecha
	 * @param int $format_out Formato al que se debe convertir la fecha
	 * @return string Fecha Formateada
	 */
	function round0($valor){
		//Acerca al valor en ceros mas cercano, ej, 51600 => 52000
		$digits   = strlen(round($valor));
		$interval = pow(10, ($digits-1));

		$precision = -1*($digits -2);
		$valor = ceil($valor * pow (10, $precision) )/ pow (10, $precision);

		return $valor;
	}

}
?>
