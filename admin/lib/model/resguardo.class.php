<?
/**
* Maneja todas las propiedades del Objeto Resguardo
* Valores de Objeto VO
* @author Ruben A. Rojas C.
*/

Class Resguardo {

  /**
  * Identificador
  * @var int
  */
	var $id;
	
  /**
  * Nombre de la Resguardo
  * @var string
  */	
	var $nombre;
	
  /**
  * ID de los departamentos que forman la region
  * @var Array
  */	
	var $id_deptos;

  /**
  * ID de los municipios que forman la region
  * @var Array
  */	
	var $id_muns;
	
  /**
  * Nombre de los municipios que forman la region
  * @var Array
  */	
	var $nom_muns;	
	
}

?>