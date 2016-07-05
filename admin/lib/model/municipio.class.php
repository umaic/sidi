<?
/**
* Maneja todas las propiedades del Objeto Municipio
* Valores de Objeto VO
* @author Ruben A. Rojas C.
*/

Class Municipio {

  /**
  * Identificador
  * @var int
  */
	var $id;
	
  /**
  * ID del departamento
  * @var int
  */	
	var $id_depto;

  /**
  * Nombre del Municipio
  * @var string
  */	
	var $nombre;
	
  /**
  * Cantidad de Manzanas del Municipio
  * @var int
  */	
	var $manzanas;

  /**
  * Acto Administrativo mediante el cual se creo el municipio	
  * @var int
  */	
	var $acto_admin;
	
  /**
  * Año de creación del Municipio	
  * @var text
  */	
	var $nacimiento;
  
  /**
  * Longitude	
  * @var text
  */	
	var $longitude;
  
  /**
  * Latitude	
  * @var text
  */	
	var $latitude;
}

?>
