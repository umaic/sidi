<?
/**
 * Maneja todas las propiedades del Objeto EspacioUsuario
 * Valores de Objeto VO
 * @author Ruben A. Rojas C.
 */

Class EspacioUsuario {

	/**
	 * Identificador
	 * @var int
	 */
	var $id;

	/**
	 * ID del Tipo de Usuario
	 * @var Array
	 */
	var $id_tipo_usuario;

	/**
	 * ID de los Módulos a los que tiene acceso el tipo de usuario
	 * @var Array
	 */
	var $id_espacio = Array();

}

?>
