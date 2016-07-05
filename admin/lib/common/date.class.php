<?php
class Date {

	var $mes_corto;

	/**
	 * Constructor
	 * Define las variables
	 * @access public
	 */
	function Date(){
		$this->mes_corto = Array ("","Ene","Feb","Mar","Abr","May","Jun","Jul","Ago","Sep","Oct","Nov","Dic");
		$this->mes_largo = array("","Enero","Febrero","Marzo","Abril","Mayo","Junio","Julio","Agosto","Septiembre","Octubre","Novimebre","Diciembre");
	}


	/**
	 * Cambia el formato de la fecha
	 * @access public
	 * @param string $fecha Fecha a Formatear
	 * @param int $format_int Formato en el que viene dada la fecha
	 * @param int $format_out Formato al que se debe convertir la fecha
	 * @param string $separador Caracter que separa los elementos de la fecha, ej. '-' para 5-Feb-2009
	 * @return string Fecha Formateada
	 */
	function Format($fecha,$format_in,$format_out,$separador='-'){
		//Letras mayúsculas para nombre de dia o mes
		//Letras minúsculas para número de dia o mes
		if ($format_in == "aaaa-mm-dd"){

			//05-Jul-2006
			if ($format_out == "dd-MC-aaaa"){
				$fecha = explode("-",$fecha);
				$fecha_r = $fecha[2]."-".$this->mes_corto[intval($fecha[1])]."-".$fecha[0];
			}
			
			//05-Julio-2006
			if ($format_out == "dd".$separador."MM".$separador."aaaa"){
				$fecha = explode("$separador",$fecha);
				$fecha_r = $fecha[2].$separador.$this->mes_largo[intval($fecha[1])].$separador.$fecha[0];
			}
		}

		return $fecha_r;
	}

	/**
	 * Calcula la diferencia en días de dFecFin - dFecIni  (dFecFin = AAAA-MM-DD)
	 * @access public
	 * @param string $fecha_1 aaaa-mm-dd
	 * @param string $fecha_2 aaaa-mm-dd
	 * @param string $caso dias,meses,etc
	 * @return int Diferencia
	 */
	function RestarFechas($dFecIni,$dFecFin,$caso='dias'){

		$dFecIni = explode("-",$dFecIni);
		$dFecFin = explode("-",$dFecFin);

		$date1 = mktime(0,0,0,$dFecIni[1], $dFecIni[2], $dFecIni[0]);
		$date2 = mktime(0,0,0,$dFecFin[1], $dFecFin[2], $dFecFin[0]);
        
        $segundos = abs($date2 - $date1);
        
        switch ($caso){
            case 'dias':
                $denominador = 60 * 60 * 24;
            break;

            case 'meses':
                $denominador = 60 * 60 * 24 * 30;
            break;
        }

		return floor($segundos / $denominador);
	}

	/**
	 * Calcula la fecha inicio y final de una semana apartir de fecha_desde. Ej. Una semana atras desde hoy
	 * @access public
	 * @param string $fecha_desde  Fecha apartir de la cual se cuentan semanas (Formato aaaa-mm-dd)
	 * @param int $semanas Semanas a contar
	 * @return array Fecha Fecha['ini'] y Fecha['fin']
	 */
	function getFechasSemana($fecha_desde,$semanas){

		$fecha_desde_s = explode("-",$fecha_desde);

		$desde_unix = mktime(0,0,0,$fecha_desde_s[1], $fecha_desde_s[2], $fecha_desde_s[0]);

		$desde_f = getdate($desde_unix);

		$diadelasemana = $desde_f["wday"];

		$dias_menos = $diadelasemana  + ($semanas - 1)*7;

		$fecha_fin_semana_unix = mktime(0,0,0,$fecha_desde_s[1], $fecha_desde_s[2] - $dias_menos - 1, $fecha_desde_s[0]);
		$fecha_ini_semana_unix = mktime(0,0,0,$fecha_desde_s[1], $fecha_desde_s[2] - $dias_menos - 7, $fecha_desde_s[0]);

		$fecha_fin_semana = date('Y-m-d',$fecha_fin_semana_unix);
		$fecha_ini_semana = date('Y-m-d',$fecha_ini_semana_unix);

		return array('fin' => $fecha_fin_semana, 'ini' => $fecha_ini_semana );

	}

	/**
	 * Suma un valor dado en meses o dias a una fecha y retorna la fecha en formato aaaa-mm-dd
	 * @access public
	 * @param string $fecha  Fecha inicial, formato aaaa-mm-dd
	 * @param int $valor_sumar
	 * @param string $unidad, dia, mes
	 * @return string $f_fin, en formato aaaa-mm-dd
	 */
	function sumValorFecha($fecha,$valor_sumar,$unidad){

		$fecha_desde_s = explode("-",$fecha);

		$desde_unix = mktime(0,0,0,$fecha_desde_s[1], $fecha_desde_s[2], $fecha_desde_s[0]);

		switch ($unidad){
			case 'dia':
				$fecha_fin_unix = mktime(0,0,0,$fecha_desde_s[1], $fecha_desde_s[2] + $valor_sumar, $fecha_desde_s[0]);
				break;

			case 'mes':
				$fecha_fin_unix = mktime(0,0,0,$fecha_desde_s[1] + $valor_sumar , $fecha_desde_s[2], $fecha_desde_s[0]);
				break;
		}

		$fecha_fin = date('Y-m-d',$fecha_fin_unix);

		return $fecha_fin; 
	
	}

	/**
	 * Retorna la fecha actual, en aaaa, mm y dd
	 * @access public
	 * @return array $hoy
	 */
	function hoy(){

		$hoy_s = date();

		$hoy['aaaa'] = date("Y");
		$hoy['mm'] = date("m");
		$hoy['dd'] = date("d");
	}

}
?>
