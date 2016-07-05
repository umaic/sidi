<?
/**
* Maneja todas las propiedades del Objeto Poblado
* Valores de Objeto VO
* @author Ruben A. Rojas C.
*/

Class Poblado {

  /**
  * Identificador
  * @var int
  */
	var $id;
	
  /**
  * Nombre del Poblado
  * @var string
  */	
	var $nombre;
	
  /**
  * ID de los municipios que forman la poblado
  * @var Int
  */	
	var $id_mun;
	
  /**
  * Clase de Poblado
  * @var String
  */	
	var $clase;
	
  /**
  * Año de Creación del Poblado
  * @var int
  */	
	var $nacimiento;

  /**
  * Acto Administrativo mediante el cual se creo el poblado
  * @var Text
  */	
	var $acto_admin;	
	
	
}

?>