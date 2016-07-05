<?php
/**
* Maneja todas las propiedades del Objeto UnicefResultado
* Valores de Objeto VO
* @author Ruben A. Rojas C.
*/

Class UnicefResultado {

	/**
	* ID
	* @var int
	*/
	var $id;
	
	/**
	* ID del sub_componente
	* @var int
	*/
	var $id_sub_componente;

	/**
	* ID del periodo
	* @var int
	*/
	var $id_periodo;

	/**
	* ID del indicador
	* @var array
	*/
	var $id_indicador = array();

	/**
	* Titulo del resultado
	* @var string
	*/
	var $nombre;

	/**
	* Código del resultado CPD
	* @var string
	*/
	var $codigo;

	/**
	* Linea de base, el valor es constante para el periodo
	* @var array
	*/
	var $linea_base = array();
	
    /**
	* Valores del indicador en los años del periodo
	* @var array
	*/
	var $indicador_valor = array();


}

?>
