<?
/**
* Maneja todas las propiedades del Objeto Region
* Valores de Objeto VO
* @author Ruben A. Rojas C.
*/

Class Region {

  /**
  * Identificador
  * @var int
  */
	var $id;
	
  /**
  * Nombre de la Region
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
	var $id_muns = Array();
	
  /**
  * Nombre de los municipios que forman la region
  * @var Array
  */	
	var $nom_muns;
	
	
	
}

?>