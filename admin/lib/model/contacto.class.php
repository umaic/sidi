<?
/**
 * Maneja todas las propiedades del Objeto Contacto
 * Valores de Objeto VO
 * @author Ruben A. Rojas C.
 */

Class Contacto {

	/**
	 * Identificador
	 * @var int
	 */
	var $id;

	/**
	 * Nombre del Contacto
	 * @var string
	 */
	var $nombre;

	/**
	 * Apellido del Contacto
	 * @var string
	 */
	var $apellido;

	/**
	 * Tel�fono del Contacto
	 * @var string
	 */
	var $tel;

	/**
	 * Celular del Contacto
	 * @var string
	 */
	var $cel;

	/**
	 * Fax del Contacto
	 * @var string
	 */
	var $fax;

	/**
	 * Email del Contacto
	 * @var string
	 */
	var $email;

	/**
	 * Skype y otros contactos
	 * @var string
	 */
	var $social;

	/**
	 * Espacios a los que pertenece el contacto
	 * @var array
	 */
	var $id_espacio = array();

	/**
	 * Caracteristicas extras del contacto, arreglo asociativo: array(id_contacto_col => valor);
	 * @var array
	 */
	var $caracteristicas = array();

    /**
	 * Fecha de creacion
	 * @var date
	 */
	var $creac;

    /**
	 * Fecha de actualizacion
	 * @var date
	 */
	var $actua;

    /**
	 * Ciudad
	 * @var id_mun
	 */
	var $id_mun;
}

?>
