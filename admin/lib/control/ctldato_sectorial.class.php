<?
/**
 * Maneja todas las acciones de administración de DatoSectorials
 *
 * @author Ruben A. Rojas C.
 */

class ControladorPagina {

	/**
	 * VO de DatoSectorial
	 * @var object
	 */
	var $dato_sectorial;

	/**
	 * Variable para el manejo de la clase DatoSectorialDAO
	 * @var object
	 */
	var $dato_sectorial_dao;

	/**
	 * Constructor
	 * Crea la conexión a la base de datos y ejecuta la accion
	 * @access public
	 * @param string $accion Variable que indica la accion a realizar
	 */
	function ControladorPagina($accion) {

		$this->dato_sectorial_dao = new DatoSectorialDAO();

		if ($accion == 'insertar') {
			$this->parseForm();
			$this->dato_sectorial_dao->Insertar($this->dato_sectorial);
		}
		else if ($accion == 'insertarDatoValor') {

			$dato_para = $_POST["dato_para"];
			$v = 0;
			foreach($_POST["valor_dato"] as $valor){
				$this->dato_sectorial->valor = $valor;
				$this->dato_sectorial->id = $_POST["id_dato"];


				if ($dato_para == 1){
					$id_depto = split(",",$_POST["id_depto"]);
					$this->dato_sectorial->id_depto = $id_depto[$v];

				}
				else if ($dato_para == 2){
					$id_mun = split(",",$_POST["id_mun"]);
					$this->dato_sectorial->id_mun = $id_mun[$v];

				}
				else if ($dato_para == 3){
					$id_pob = split(",",$_POST["id_pob"]);
					$this->dato_sectorial->id_pob = $id_pob[$v];
				}

				$this->dato_sectorial->id_unidad = $_POST["id_unidad"];
				$this->dato_sectorial->fecha_ini = $_POST["f_ini"];
				$this->dato_sectorial->fecha_fin = $_POST["f_fin"];

				$this->dato_sectorial_dao->InsertarDatoValor($this->dato_sectorial,$dato_para);
				$v++;
			}
			?>
				<script>
				alert("Dato insertado con éxito");
			location.href = 'index.php?accion=actualizarDatoValor';
			</script>
				<?
		}
		else if ($accion == 'actualizarDatoValor') {

			$dato_para = $_POST["dato_para"];
			$v = 0;
			foreach($_POST["valor_dato"] as $valor){
				$this->dato_sectorial->valor = $valor;
				$this->dato_sectorial->id = $_POST["id_dato"];


				if ($dato_para == 1){
					$id_depto = split(",",$_POST["id_depto"]);
					$this->dato_sectorial->id_depto = $id_depto[$v];

				}
				else if ($dato_para == 2){
					$id_mun = split(",",$_POST["id_mun"]);
					$this->dato_sectorial->id_mun = $id_mun[$v];

				}
				else if ($dato_para == 3){
					$id_pob = split(",",$_POST["id_pob"]);
					$this->dato_sectorial->id_pob = $id_pob[$v];
				}

				$this->dato_sectorial->id_unidad = $_POST["id_unidad"];
				$this->dato_sectorial->fecha_ini = $_POST["f_ini"];
				$this->dato_sectorial->fecha_fin = $_POST["f_fin"];

				$this->dato_sectorial_dao->ActualizarDatoValor($this->dato_sectorial,$dato_para);
				$v++;
			}

			?>
				<script>
				alert("Dato actualizado con éxito");
			location.href = 'index.php?accion=actualizarDatoValor';
			</script>
				<?

		}
		else if ($accion == 'actualizar'){
			$this->parseForm();
			$this->dato_sectorial_dao->Actualizar($this->dato_sectorial);
		}
		else if ($accion == 'borrar') {
			$this->dato_sectorial_dao->Borrar($_GET["id"]);
		}
		else if ($accion == 'importar') {
			$this->dato_sectorial_dao->ImportarCSV($_FILES['archivo_csv'],$_POST["id_dato"],$_POST["dato_para"],$_POST["id_unidad"],$_POST["f_ini"],$_POST["f_fin"]);
		}
	}

	/**
	 * Realiza el Parse de las variables de la forma y las asigna al VO de DatoSectorial (variable de clase)
	 * @access public
	 */
	function parseForm() {
		if (isset($_POST["id"])){
			$this->dato_sectorial->id = $_POST["id"];
		}
		$this->dato_sectorial->id_cat = (isset($_POST["id_cat"])) ? $_POST["id_cat"] : 0;
		$this->dato_sectorial->id_contacto = $_POST["id_contacto"];
		$this->dato_sectorial->id_sector = $_POST["id_sector"];
		$this->dato_sectorial->tipo_calc_nal = $_POST["tipo_calc_nal"];
		$this->dato_sectorial->tipo_calc_deptal = $_POST["tipo_calc_deptal"];
		//$this->dato_sectorial->tipo_calc_reg= $_POST["tipo_calc_reg"];
		//$this->dato_sectorial->formula_calc_reg= $_POST["formula_calc_reg"];

		$this->dato_sectorial->nombre = "";
		if (isset($_POST["nombre"])){
			$this->dato_sectorial->nombre = $_POST["nombre"];
		}

		$this->dato_sectorial->definicion = "";
		if (isset($_POST["definicion"])){
			$this->dato_sectorial->definicion = $_POST["definicion"];
		}

		$this->dato_sectorial->desagreg_geo = "";
		if (isset($_POST["desagreg_geo"])){
			$this->dato_sectorial->desagreg_geo = $_POST["desagreg_geo"];
		}

		$this->dato_sectorial->formula = "";
		if (isset($_POST["formula"]) && $_POST["calculado"] == 1){
			$this->dato_sectorial->formula = $_POST["formula"];
			$this->dato_sectorial->id_unidad = $_POST["id_unidad"];
		}
	}
}
?>
