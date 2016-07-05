<?php
/**
* Maneja todas las propiedades del Objeto UnicefProductoCpap
* Valores de Objeto VO
* @author Ruben A. Rojas C.
*/

Class UnicefProductoCpap {

	/**
	* ID
	* @var int
	*/
	var $id;

	/**
	* ID resultado al que pertenece
	* @var int
	*/
	var $id_resultado;

	/**
	* ID indicador
	* @var int
	*/
	var $id_indicador;

	/**
	* Nombre del producto
	* @var string
	*/
	var $nombre;

	/**
	* Codigo
	* @var string
	*/
	var $codigo;
	
    /**
	* Linea Base
	* @var string
	*/
	var $linea_base;
	
    /**
	* Meta para el periodo
	* @var string
	*/
	var $meta;

	/**
	* Valores del indicador en los años del periodo
	* @var array
	*/
	var $indicador_valor = array();

	/**
	* Valores de la meta en los años del periodo
	* @var array
	*/
	var $meta_valor = array();
}

?>
