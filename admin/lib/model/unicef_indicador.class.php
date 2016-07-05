<?php
/**
* Maneja todas las propiedades del Objeto UnicefIndicador
* Valores de Objeto VO
* @author Ruben A. Rojas C.
*/

Class UnicefIndicador {
    
    /**
    * ID
    * @var int
    */
    var $id;

    /**
    * Nombre del Indicador
    * @var string
    */
    var $nombre;

    /**
    * Especifica si el indicador aplica para resultados
    * @var int
    */
    var $resultado;

    /**
    * Especifica si el indicador aplica para Productos CPAP
    * @var int
    */
    var $producto_cpap;

}

?>
