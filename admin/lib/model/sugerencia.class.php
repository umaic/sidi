<?
/**
* Maneja todas las propiedades del Objeto Sugerencia
* Valores de Objeto VO
* @author Ruben A. Rojas C.
*/

Class Sugerencia {

	/**
	* Identificador
	* @var int
	*/
	var $id;

	/**
	* ID del usuario que envia la sugerencia
	* @var int
	*/
	var $id_usuario;

	/**
	* M�dulo al que corresponde la sugerencia
	* @var string
	*/
	var $modulo;

	/**
	* Texto de la sugerencia
	* @var string
	*/
	var $texto;

	/**
	* Fecha de envio de la sugerencia
	* @var string
	*/
	var $fecha;

}

?>
