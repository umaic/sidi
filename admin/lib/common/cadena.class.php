<?
/**
 * Clase para manejo de Cadenas
 *
 * Contiene los métodos de la clase Cadena
 * @author Ruben A. Rojas C.
 */


Class Cadena {
	
	/**
	* Extrae la parte de una cadena que se encuentra entre un Tag
	* @access public
	* @param string $string Cadena donde buscar
	* @param string $tag_ini Caracter inicial del Tag
	* @param string $tag_fin Caracter final del Tag
	* @return array Arreglo con los contenidos del tag
	*/
	function getContentTag($string,$tag_ini,$tag_fin){
		$elements = array();
		$tags = 1;
		$e = 0;
		$fin_string = strlen($string);
		$string_tmp = $string;
		while ($tags > 0){
			$ini = strpos($string_tmp,$tag_ini);
			$fin = strpos($string_tmp,$tag_fin);

			if ($ini !== false && $fin !== false){
				$ele = substr($string_tmp,$ini+1,$fin-1-$ini);
				$elements[$e] = $ele;		
				$string_tmp = substr($string_tmp,$fin+1,$fin_string-$fin-1);
				
				$tags = 1;
				$e++;
			}
			else{
				$tags = 0;
			}
		
		}
		return $elements;
	}
}

?>