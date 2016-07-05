<?
/**
 * DAO de Minificha
 *
 * Contiene los métodos de la clase Minificha
 * @author Ruben A. Rojas C.
 */

Class MinifichaDAO {

	/**
	 * Conexión a la base de datos
	 * @var object
	 */
	var $conn;


	/**
	 * Constructor
	 * Crea la conexión a la base de datos
	 * @access public
	 */
	function MinifichaDAO (){
		$this->conn = MysqlDb::getInstance();
	}

	/**
	 * Consulta las opciones a mostrar para cada módulo de la minificha
	 * @access public
	 * @param string $id_modulo
	 * @param string $id_submodulo
	 * @return  array $id_opciones
	 */
	function Get($id_modulo,$id_submodulo){

		$mods = array();
		$mods_grafica = array();

		switch ($id_modulo){
			//General
			case 1:
				$d = 0;
				$sql = "SELECT ID_MIN_GENERAL FROM minificha_general WHERE ACTIVO_MIN_GENERAL = 1";
				$rs = $this->conn->OpenRecordset($sql);
				while ($row_rs = $this->conn->FetchRow($rs)){
					$mods[$d] = $row_rs[0];
					$d++;
				}
				break;
				//Datos Sectoriales generales
			case 2:
				$m = 0;
				$sql = "SELECT ID_MIN_D_S, TIPO_GRA_MIN_D_S FROM minificha_datos_sectoriales WHERE ACTIVO_MIN_D_S = 1";
				$rs = $this->conn->OpenRecordset($sql);
				while ($row_rs = $this->conn->FetchRow($rs)){
					$mods[$m] = $row_rs[0];
					$mods_grafica[$m] = $row_rs[1];
					$m++;
				}
				break;
				//Desplazamiento
			case 3:
				$m = 0;
				$sql = "SELECT ID_MIN_DES, TIPO_GRA_MIN_DES FROM minificha_desplazamiento WHERE ACTIVO_MIN_DES = 1";
				$rs = $this->conn->OpenRecordset($sql);
				while ($row_rs = $this->conn->FetchRow($rs)){
					$mods[$m] = $row_rs[0];
					$mods_grafica[$m] = $row_rs[1];
					$m++;
				}
				break;
				//Minas
			case 4:
				$m = 0;
				$sql = "SELECT ID_MIN_MINA, TIPO_GRA_MIN_MINA FROM minificha_mina WHERE ACTIVO_MIN_MINA = 1";
				$rs = $this->conn->OpenRecordset($sql);
				while ($row_rs = $this->conn->FetchRow($rs)){
					$mods[$m] = $row_rs[0];
					$mods_grafica[$m] = $row_rs[1];
					$m++;
				}
				break;
				//IRH
			case 5:
				$m = 0;
				$sql = "SELECT ID_MIN_S_HUMA, TIPO_GRA_MIN_S_HUMA FROM minificha_s_huma WHERE ACTIVO_MIN_S_HUMA = 1";
				$rs = $this->conn->OpenRecordset($sql);
				while ($row_rs = $this->conn->FetchRow($rs)){
					$mods[$m] = $row_rs[0];
					$mods_grafica[$m] = $row_rs[1];
					$m++;
				}
				break;
				//ORG
			case 6:
				$m = 0;
				$sql = "SELECT ID_MIN_ORG, TIPO_GRA_MIN_ORG FROM minificha_org WHERE ACTIVO_MIN_ORG = 1";
				$rs = $this->conn->OpenRecordset($sql);
				while ($row_rs = $this->conn->FetchRow($rs)){
					$mods[$m] = $row_rs[0];
					$mods_grafica[$m] = $row_rs[1];
					$m++;
				}
				break;
		}

		if ($id_submodulo > 0){
			$mods = array();
			switch ($id_submodulo){
				//Datos tabla resumen
				case 1:
					$d = 0;
					$sql = "SELECT ID_DATO FROM minificha_datos_resumen";
					$rs = $this->conn->OpenRecordset($sql);
					while ($row_rs = $this->conn->FetchRow($rs)){
						$mods[$d] = $row_rs[0];
						$d++;
					}
					break;

					//Sexo Mina
				case 41:
					$d = 0;
					$sql = "SELECT ID_SEXO FROM minificha_sexo_mina";
					$rs = $this->conn->OpenRecordset($sql);
					while ($row_rs = $this->conn->FetchRow($rs)){
						$mods[$d] = $row_rs[0];
						$d++;
					}
					break;

					//Condiciones Mina
				case 42:
					$d = 0;
					$sql = "SELECT ID_CONDICION_MINA FROM minificha_condicion_mina";
					$rs = $this->conn->OpenRecordset($sql);
					while ($row_rs = $this->conn->FetchRow($rs)){
						$mods[$d] = $row_rs[0];
						$d++;
					}
					break;

					//Enfermedades
				case 9:
					$d = 0;
					$sql = "SELECT ID_DATO FROM minificha_enfermedades";
					$rs = $this->conn->OpenRecordset($sql);
					while ($row_rs = $this->conn->FetchRow($rs)){
						$mods[$d] = $row_rs[0];
						$d++;
					}
					break;
			}
		}

		return array('mods' => $mods,'mods_grafica' => $mods_grafica);
	}

	/**
	 * Actualiza la información que debe incluir la minificha
	 * @access public
	 * @param int $id_modulo Grupo
	 * @param array $mods Modulos seleccionados
	 * @param array $submods SubModulos seleccionados
	 */
	function UpdateInfoMinificha($id_modulo,$mods,$submods){

		$d_s_dao = New DatoSectorialDAO();

		switch ($id_modulo){
			//General
			case 1:

				$sql = "UPDATE minificha_general SET ACTIVO_MIN_GENERAL = 0";
				$this->conn->Execute($sql);

				foreach ($mods as $mod){

					$sql = "UPDATE minificha_general SET ACTIVO_MIN_GENERAL = 1 WHERE ID_MIN_GENERAL =$mod";
					$this->conn->Execute($sql);

					//Datos Resumen
					if ($mod == 1){

						$sql = "DELETE FROM minificha_datos_resumen WHERE id_cate = ".$_POST['id_categoria'];
						$this->conn->Execute($sql);

						foreach ($submods as $s => $submod){
							$dato = $d_s_dao->Get($submod);
							
							
							$sql = "INSERT INTO minificha_datos_resumen (ID_DATO,ID_CATE) VALUES ($submod,$dato->id_cat)";
							$this->conn->Execute($sql);
						}
					}
				}
				break;
				//Datos Sectoriales generales
			case 2:

				$sql = "UPDATE minificha_datos_sectoriales SET ACTIVO_MIN_D_S = 0";
				$this->conn->Execute($sql);

				foreach ($mods as $mod){
					$sql = "UPDATE minificha_datos_sectoriales SET ACTIVO_MIN_D_S = 1, TIPO_GRA_MIN_D_S = '".$_POST["grafica_$mod"]."' WHERE ID_MIN_D_S =$mod";
					$this->conn->Execute($sql);

					//Enfermedades
					if ($mod == 9){
						if (count($submods) > 0){
							$sql = "TRUNCATE minificha_enfermedades";
							$this->conn->Execute($sql);
							foreach ($submods as $submod){
								$sql = "INSERT INTO minificha_enfermedades (ID_DATO) VALUES ($submod)";
								$this->conn->Execute($sql);
							}
						}
					}
				}
				break;
				//Desplazamiento
			case 3:

				$sql = "UPDATE minificha_desplazamiento SET ACTIVO_MIN_DES = 0";
				$this->conn->Execute($sql);

				foreach ($mods as $mod){
					$sql = "UPDATE minificha_desplazamiento SET ACTIVO_MIN_DES = 1, TIPO_GRA_MIN_DES = '".$_POST["grafica_$mod"]."' WHERE ID_MIN_DES =$mod";
					$this->conn->Execute($sql);
				}
				break;
				//Minas
			case 4:

				$sql = "UPDATE minificha_mina SET ACTIVO_MIN_MINA = 0";
				$this->conn->Execute($sql);

				foreach ($mods as $mod){
					$sql = "UPDATE minificha_mina SET ACTIVO_MIN_MINA = 1, TIPO_GRA_MIN_MINA = '".$_POST["grafica_$mod"]."' WHERE ID_MIN_MINA =$mod";
					$this->conn->Execute($sql);
					
					//Mina
					if ($mod == 1){
						
						if (isset($_POST["sexo_mina"])){
							$sql = "TRUNCATE minificha_sexo_mina";
							$this->conn->Execute($sql);
						}
					
						$submods = (isset($_POST["submods_1"])) ? $_POST["submods_1"] : array();
						foreach ($submods as $submod){

							$sql = "INSERT INTO minificha_sexo_mina (ID_SEXO) VALUES ($submod)";
							$this->conn->Execute($sql);
						}
					}

					//Condiciones
					if ($mod == 2){
						
						if (isset($_POST["condicion_mina"])){
							$sql = "TRUNCATE minificha_condicion_mina";
							$this->conn->Execute($sql);
						}
						
						$submods = (isset($_POST["submods_2"])) ? $_POST["submods_2"] : array();
						foreach ($submods as $s => $submod){
							
							$sql = "INSERT INTO minificha_condicion_mina (ID_CONDICION_MINA) VALUES ($submod)";
							$this->conn->Execute($sql);
						}
					}
				}
				break;

				//IRH
			case 5:
				$sql = "UPDATE minificha_s_huma SET ACTIVO_MIN_S_HUMA = 0";
				$this->conn->Execute($sql);

				foreach ($mods as $mod){
					$sql = "UPDATE minificha_s_huma SET ACTIVO_MIN_S_HUMA = 1, TIPO_GRA_MIN_S_HUMA = '".$_POST["grafica_$mod"]."' WHERE ID_MIN_S_HUMA =$mod";
					$this->conn->Execute($sql);
				}
				break;

				//IRH
			case 6:
				$sql = "UPDATE minificha_org SET ACTIVO_MIN_ORG = 0";
				$this->conn->Execute($sql);

				foreach ($mods as $mod){
					$sql = "UPDATE minificha_org SET ACTIVO_MIN_ORG = 1, TIPO_GRA_MIN_ORG = '".$_POST["grafica_$mod"]."' WHERE ID_MIN_ORG =$mod";
					$this->conn->Execute($sql);
				}
				break;

		}

		echo "Perfil actualizado con &eacute;xito";
	}
	
	/**
	* Coloca el orden de las categorias
	* @access public
	* @param string $key  Arreglo con los ids
	*/	
	function setOrder($key,$caso){
    
        $orden = 1;
        foreach ($key as $id) {
 
            $sql = "UPDATE minificha_datos_resumen SET orden_$caso = $orden WHERE id_$caso = $id";
			echo $sql;
        	$this->conn->Execute($sql);
        	
            $orden++;
        }
    }	
}

?>
