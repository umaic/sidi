<?
/**
 * Maneja todas las propiedades del Objeto Tema
 * Valores de Objeto VO
 * @author Ruben A. Rojas C.
 */

Class Tema {

    /**
     * Identificador
     * @var int
     */
    var $id;

    /**
     * Nombre del Tema
     * @var string
     */	
    var $nombre;

    /**
    * Clasificacion a la que pertenece el tema
    * @var int
    */
    var $id_clasificacion;

    /**
    * Relación multinivel
	* @var int
    */
    var $id_papa;

    /**
     * Definición del Tema
     * @var string
     */	
    var $def;
}

?>
