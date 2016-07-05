<?
/**
 * Maneja todas las propiedades del Objeto Usuario
 * Valores de Objeto VO
 * @author Ruben A. Rojas C.
 */
Class Usuario {

    /**
     * Identificador
     * @var int
     */
    var $id;

    /**
     * ID del Tipo de Usuario
     * @var int
     */
    var $id_tipo;

    /**
     * Nombre completo del Usuario
     * @var string
     */	
    var $nombre;

    /**
     * Organizacion
     * @var string
     */	
    var $org = "";

    /**
     * Tel
     * @var string
     */	
    var $tel = "";

    /**
     * Punto de contacto en OCHA
     * @var string
     */	
    var $punto_contacto = "";	

    /**
     * Login del Usuario
     * @var string
     */	
    var $login;

    /**
     * Password del Usuario
     * @var string
     */	
    var $pass;

    /**
     * Email del Usuario
     * @var string
     */	
    var $email;

    /**
     * Activo
     * @var int
     */	
    var $activo;

    /**
     * ID de la org, cuando aplique para el tipo de usuario
     * @var int
     */	
    var $id_org;
    
    /**
     * ID del tema-cluster, tipo=42, 4w
     * @var int
     */	
    var $id_tema;

}

?>
