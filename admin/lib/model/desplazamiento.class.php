<?
/**
* Maneja todas las propiedades del Objeto Desplazamiento
* Valores de Objeto VO
* @author Ruben A. Rojas C.
*/

Class Desplazamiento {

  /**
  * Identificador
  * @var int
  */
	var $id;

  /**
  * Etnia
  * @var int
  */
	var $id_poblacion;

  /**
  * Periodo
  * @var int
  */
	var $id_periodo;

  /**
  * Tipo
  * @var int
  */
	var $id_tipo;

  /**
  * Clase
  * @var int
  */
	var $id_clase;

  /**
  * Contácto
  * @var int
  */
	var $id_contacto;

  /**
  * Municipio Expulsor
  * @var int
  */
	var $id_mun_exp;

  /**
  * Departamento Expulsor
  * @var int
  */
	var $id_depto_exp;

  /**
  * Municipio Receptor
  * @var int
  */
	var $id_mun_rec;

  /**
  * Departamento Receptor
  * @var int
  */
	var $id_depto_rec;

  /**
  * Cantidad de Desplazamientos
  * @var int
  */
	var $cantidad;
    
    /**
     * Cantidad de personas desplazadas, 
     * 1 persona puede desplzarse varias veces
    * @var int
    */
      var $personas;

  /**
  * Fecha de Corte
  * @var string
  */
	var $f_corte;
}

?>
