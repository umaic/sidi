<?
/**
* Maneja todas las propiedades del Objeto Barrio
* Valores de Objeto VO
* @author Ruben A. Rojas C.
*/

Class Barrio {

  /**
  * Identificador
  * @var int
  */
	var $id;
	
  /**
  * Nombre del Barrio
  * @var string
  */	
	var $nombre;
	
  /**
  * ID del mpio donde pertenece la comuna
  * @var Int
  */	
	var $id_mun;
	

  /**
  * ID del poblado al que pertenece la comuna
  * @var Int
  */	
	var $id_pob;

  /**
  * ID de la comuna
  * @var Int
  */	
	var $id_comuna;
	
}

?>