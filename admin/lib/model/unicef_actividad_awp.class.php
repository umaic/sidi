<?php
/**
* Maneja todas las propiedades del Objeto UnicefActividadAwp
* Valores de Objeto VO
* @author Ruben A. Rojas C.
*/

Class UnicefActividadAwp {
	
	/**
	* ID
	* @var int
	*/
	var $id;

	/**
	* ID del producto CPAP
	* @var int
	*/
	var $id_producto;

	/**
	* UNDAF AREA PRIORITARIA
	* @var int
	*/
	var $id_tema_undaf_1;

	/**
	* UNDAF OUTCOME
	* @var int
	*/
	var $id_tema_undaf_2;

	/**
	* UNDAF OUTPUT
	* @var int
	*/
	var $id_tema_undaf_3;

	/**
	* ID de estado
	* @var int
	*/
	var $id_estado;

	/**
	* Nombre de la actividad
	* @var string
	*/
	var $nombre;

	/**
	* Codigo de la actividad
	* @var string
	*/
	var $codigo;

    /**
	 * Año de planeación
	 * @var int
	 */
	var $aaaa;
}

?>
