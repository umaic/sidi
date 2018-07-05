<?
Class Archivo {

	var $path;

	function SetPath($path){
		$this->path = $path;
	}

	function Guardar($archivo){
		if (@copy($archivo,$this->path)){
			return true;
		}
		else
			return false;
	}

	function Abrir($archivo,$permiso='r+'){
		return fopen($archivo,$permiso);
	}

	function Escribir($fp,$texto){
		return fwrite($fp,$texto);
	}

	function Borrar($archivo){
		unlink($archivo);
	}

	function Cerrar($fp){
		fclose($fp);
	}

	function LeerEnArreglo($fp){
		$array = array();
		$f = 0;
		while (!feof($fp)){
			$array[$f] = fgets($fp, 4096);
			$f++;
		}
		return $array;
	}

	function LeerLineaX($fp,$cual_linea){
		$array = array();
		$f = 0;
		while (!feof($fp)){
			if ($cual_linea - 1 == $f){
				return fgets($fp);
			}
			$f++;
		}
	}


	function LeerEnString($fp,$archivo){
		$contenido = "";
		$contenido = fread ($fp, filesize ($archivo));
		return $contenido;
	}

	//$dir = Path físico del directorio
	//retorna un arreglo con los nombres de los archivos, exceptuado el . y ..
	function ListarDirectorioEnArreglo($dir){
		$array = array();
		if($handle=opendir($dir)){
			$f = 0;
			while ($file = readdir($handle)) {
				if ($file != "." && $file != ".." && is_file($dir."/".$file)){
					$array[$f] = $file;
					$f++;
				}
			}
			closedir($handle);

			sort($array);
			return $array;
		}
		else{
			echo "Error al abrir el directorio raíz";
		}
	}

	//$raiz = Path físico de la raíz
	//$condicion = nombres de directorios separados por coma ",", que no se van a mostrar
	//retorna un arreglo con los nombres de direcorios
	function ListarRaizEnArreglo($raiz,$condicion){
		$array = array();
		$condicion = split(",",$condicion);
		if($handle=opendir($raiz)){
			$f = 0;
			while ($dir = readdir($handle)) {
				if ($dir != "." && $dir != ".." && is_dir($raiz."/".$dir) && !in_array($dir,$condicion)){
					$array[$f] = $dir;
					$f++;
				}
			}
			closedir($handle);
		}
		return $array;
	}

	function PermisoEscritura($file){
		return is_writeable($file);
	}

	function Existe($filename){
		return file_exists($filename);
	}

	function crearDirectorio($path){
		mkdir($path);
	}

	function copiar($origen,$destino){
		copy($origen,$destino);
	}

	//Fecha de modificacion, si no existe devuelve 0
	function fechaModificacion($archivo){

		return filemtime($archivo);

	}

    function delTree($dir) {
        $files = array_diff(scandir($dir), array('.','..'));
        foreach ($files as $file) {
          //echo "$dir/$file <br />";
          (is_dir("$dir/$file")) ? $this->delTree("$dir/$file") : unlink("$dir/$file");
        }
        return rmdir($dir);
    }
	//Borra el contenido de un directorio
	function borrarContenidoDirectorio($dir){

		if($handle=opendir($dir)){
            while (false !== ($file = readdir($handle))) {
   				if ($file != "." && $file != ".." && is_file($dir."/".$file)){
                    //echo "$file <br />";
					unlink($dir."/".$file);
				}
                else if ($file != "." && $file != ".." && is_dir("$dir/$file")) {
                    //echo "$dir/$file <br />";
                    $this->delTree("$dir/$file");
                }

			}
			closedir($handle);

		}
		else{
			echo "Error al abrir el directorio raíz";
		}
	}
}
?>
