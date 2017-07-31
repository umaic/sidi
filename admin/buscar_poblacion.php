<?
//SEGURIDAD
include_once 'seguridad.php';

//LIBRERIAS
include_once 'lib/libs_poblacion.php';

//INICIALIZACION DE VARIABLES
$poblacion_dao = New PoblacionDAO();
$buscar = 0;
$op = 0;
$order_by = '';
$arr = Array();
$sel_col_orden = Array("NOM_POBLA_ES" => "","NOM_POBLA_IN" => "");
$sel_dir_orden = Array("ASC" => "","DESC" => "");
$col_orden_url = "";
$dir_orden_url = "";
$criterio = "";
$criterio_col = "";

$num_reg_pag = 50;
$num_arr_total = $poblacion_dao->numRecords('');


//OPCIONES
if (isset($_GET["op"]) && $_GET["op"] == 1){
	$op = 1; 
}
else if (isset($_GET["op"]) && $_GET["op"] == 2){
  $buscar = 1;
  $condicion = '';
  $op = 2;
}

if (isset($_POST["buscar"])){
  $buscar = 1;
}
else if (isset($_GET["buscar"])){
  $buscar = $_GET["buscar"];
}

if (isset($_POST["criterio_col"])){
	$criterio_col = $_POST['criterio_col'];
}
else if (isset($_GET["criterio_col"])){
	$criterio_col = $_GET['criterio_col'];
}

if (isset($_POST["criterio"])){
	$criterio = $_POST['criterio'];
}
else if (isset($_GET["criterio"])){
	$criterio = $_GET['criterio'];
}

//ACCION DE LA FORMA DE BUSQUEDA
if ($buscar == 1 && $op == 1){
	$condicion = $criterio_col." LIKE '%".$criterio."%'";
}

//ORDENAMIENTO
if (isset($_GET["col_orden"]) && $_GET["col_orden"] != ""){
	$col_orden_url = $_GET["col_orden"];
	$dir_orden_url = $_GET["dir_orden"];
  
	$sel_col_orden[$_GET["col_orden"]] = " selected ";
	$sel_dir_orden[$_GET["dir_orden"]] = " selected ";
	
	$col_orden_url = $_GET["col_orden"];
	$dir_orden_url = $_GET["dir_orden"];
  
    $order_by = $_GET["col_orden"]." ".$_GET["dir_orden"];
}

//PAGINACION	
$pag_url = 1;
$inicio = 0;
if (isset($_GET['page']) && $_GET['page'] > 1){
	$pag_url = $_GET['page'];
	$inicio = ($pag_url-1)*$num_reg_pag;
}

$fin = $inicio + $num_reg_pag;
if ($fin > $num_arr_total){
  $fin = $num_arr_total;
}

if ($buscar == 1 || $op == 2){		

	$limit = $inicio.",".$fin; 
	$arr = $poblacion_dao->GetAllArray($condicion,$limit,$order_by);
	$num_arr = count($arr);
}


?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.poblacion/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.poblacion/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Sistema de Informaci&oacute;n Central OCHA - Colombia</title>
<link href='../images/input.css' rel='stylesheet' type='text/css' />
<script src="js/general.js"></script>
<script>
function enviar_formulario(input_hidden,input_text,multiple,combo_extra,id,nom){

	var num_options = opener.document.getElementById(input_text).options.length;
	
	if (multiple == 0){
		for(o=0;o<num_options;o++){
		  opener.document.getElementById(input_text).options[o] = null;
		}
	  	AddOption(nom,id,opener.document.getElementById(input_text));
		opener.document.getElementById(input_hidden).value = id;
		window.close();
	}
	else{
	  
	    //NUMERO DE PERSONAS
	  	if (combo_extra != "")
			var num_per = prompt("N�mero de personas beneficiadas en esta poblaci�n?",0);
	    
	  	in_array = 0;
		for(o=0;o<num_options;o++){
			if (id == opener.document.getElementById(input_text).options[o].value){
			  in_array = 1;
			}
		}
		
		if (in_array == 0){
		  	//AGREGA LA OPCION
		  	AddOption(nom,id,opener.document.getElementById(input_text));
		  	if (combo_extra != "")
			  	AddOption(num_per,num_per,opener.document.getElementById(input_text+combo_extra));
		  	
			  //COLOCA EL VALOR EN EL INPUT HIDDEN
			CopiarOpcionesCombo(opener.document.getElementById(input_text),opener.document.getElementById(input_hidden));
		  	if (combo_extra != "")
				CopiarOpcionesCombo(opener.document.getElementById(input_text+combo_extra),opener.document.getElementById(input_hidden+combo_extra));
			
			//RESIZE DEL COMBO
			resizeCombo(opener.document.getElementById(input_text));
		  	if (combo_extra != "")
				resizeCombo(opener.document.getElementById(input_text+combo_extra));
			
			if(!confirm('Poblaci�n agregada, desea agrear otra?')){
			  window.close();
			}
		}
		else{
		  alert('Ya existe la Poblaci�n');
		}
	}
}
</script>
</head>
<body>
<h1 class="info">SIDI UMAIC - Colombia</h1><h1 class="info">SIDI UMAIC - Colombia</h1>
<div id="cabecera"></div>
<!-- CONTENIDO : INICIO -->
<div id="cont">
	<br>
	<?
	//OPCIONES INICIALES
	if (!isset($_GET["op"]) && $buscar == 0){ ?>
		<table align="center" cellspaing="1" cellpadding="3" class="tabla_consulta">
			<tr><td class='titulo_lista' align='center'>BUSCAR POBLACION</td></tr>
			<tr>
				<td>
					<table cellpadding="5" cellspacing="1"> 
						<tr><td><b>Opci�n 1</b>: </td><td align="left"><a href="buscar_poblacion.php?<?=$_SERVER["QUERY_STRING"]?>&op=1">Buscar Por Palabra Clave</a></td></tr>
						<tr><td><b>Opci�n 2</b>: </td><td align="left"><a href="buscar_poblacion.php?<?=$_SERVER["QUERY_STRING"]?>&op=2">Listar todas las Poblaciones</a></td></tr>
					</table>
				</td>
			</tr>
		</table>
	<?
	}
	//BUSAR POR PALABRA
	else if ($op == 1  && $buscar == 0){ ?>
		<form action="buscar_poblacion.php?<?=$_SERVER["QUERY_STRING"]?>" method="POST">
		<table align='center' cellspacing="1" cellpadding="0" border="0" >
			<tr>
				<td align="center" width="50%">
					<table width="99%" cellspacing="1" cellpadding="5" border="0" class="tabla_consulta">
						<tr><td align="center" colspan="2" class='titulo_lista'><b>Busqueda de Poblaciones por palabra</b></td></tr>							
						<tr><td align='center' colspan="2">&nbsp;</td></tr> 
						<tr>
							<td align='right'>Palabra</td>
							<td><input type='text' id='criterio' name='criterio' class='textfield' size="40" /></td>
						</tr>
						<tr>
							<td align='right'>Buscar en</td>
							<td>
								<select id='criterio_col' name='criterio_col' class='select'>
								  <option value='NOM_POBLA_ES'>Nombre en Espa�ol</option>
								  <option value='NOM_POBLA_IN'>Nombre en Espa�ol</option>
								</select>
							</td>
						</tr>
						<tr><td>&nbsp;</td></tr>
						<tr>
							<td align='center' colspan="2">
								<input type="submit" name="buscar" value="Buscar Poblaciones" class="boton" onclick="return validar_forma('criterio,Palabra','')" />	
							</td>
						</tr>	
						<tr><td>&nbsp;</td></tr>
						<tr><td>&nbsp;</td></tr>
					</table>
				</td>
			</tr>
		</table>
		</form>  	
  	<?
	}
	else if ($buscar == 1){ ?>
		<?
		if ($op == 1){
		  	echo "<table cellspaing='1' cellpadding='3' align='center'>";
			echo "<tr>";
	    	echo "<td valign='top'>
					<table width='700' align='center' cellspacing='1' cellpadding='3' class='tabla_consulta'>
	    				<tr class='titulo_lista'><td align='center' colspan='2'>Resultado de la b�squeda de Poblaciones por palabra</td></tr>
						<tr><td>Palabra: <b>".$criterio."</b> - Buscando en: <b>";
						if ($criterio_col == "NOM_POBLA_ES")	echo "Nombre en Espa�ol";
						else										echo "Nombre en Ingl�s";
						echo "</b></td></tr>
					</table>";
			echo "</td>";
			echo "</tr>";
			echo "</table><br>";
		}
		echo "<table cellspaing='1' cellpadding='3' width='700' align='center'>";

		if ($num_arr > 1){
		  
			echo "<tr>
				<td colspan='5'><b>Ordenar listado por</b>:
					<select id='col_orden' name='col_orden' class='select'>
						<option value='NOM_POBLA_ES' ".$sel_col_orden["NOM_POBLA_ES"].">Nombre en Espa�ol</option>
						<option value='NOM_POBLA_IN' ".$sel_col_orden["NOM_POBLA_IN"].">Nombre en Ingl�s</option>
					</select>&nbsp;
					<select id='dir_orden' name='dir_orden' class='select'>
						<option value='ASC' ".$sel_dir_orden["ASC"].">Ascendente</option>
						<option value='DESC' ".$sel_dir_orden["DESC"].">Descendente</option>
					</select>&nbsp;
					<input type='button' value='Ordenar' class='boton' onclick=\"location.href='buscar_poblacion.php?field_hidden=".$_GET["field_hidden"]."&field_text=".$_GET["field_text"]."&multiple=".$_GET["multiple"]."&combo_extra=".$_GET["combo_extra"]."&op=".$op."&buscar=".$buscar."&criterio=".$criterio."&criterio_col=".$criterio_col."&page=".$pag_url."&col_orden='+document.getElementById('col_orden').value+'&dir_orden='+document.getElementById('dir_orden').value;\" \>
				</td>
			</tr>";
		}
		
		if ($num_arr > 0){
			echo"<tr class='titulo_lista'>
				<td>Nombre en Espa�ol</td>
				<td>Nombre en Ingl�s</td>";
				echo "<td align='center' width='150'>Registros: ".$num_arr_total."</td>
	    		</tr>";
									
			for($p=0;$p<$num_arr;$p++){
				$style = "";
		  		if (fmod($p+1,2) == 0)  $style = "fila_lista";
		
				//NOMBRE
				if ($arr[$p]->nombre_es != ""){
				  
					echo "<tr class='".$style."'>";
					echo "<td>".$arr[$p]->nombre_es."</td>";
					echo "<td>".$arr[$p]->nombre_in."</td>";
					echo "<td align='center'><input type='button' value='Enviar al Formulario' onclick=\"enviar_formulario('".$_GET["field_hidden"]."','".$_GET["field_text"]."','".$_GET["multiple"]."','".$_GET["combo_extra"]."','".$arr[$p]->id."','".$arr[$p]->nombre_es."');\" class='boton'></td>";
			  		echo "</tr>";
			  	}
			}
			
			echo "<tr><td>&nbsp;</td></tr>";
			
		  	//PAGINACION
		  	if ($num_arr_total > $num_reg_pag){
				 
				$num_pages = ceil($num_arr_total/$num_reg_pag); 
				echo "<tr><td colspan='5' align='center'>";
				
				echo "Ir a la p�gina:&nbsp;<select onchange=\"location.href='buscar_poblacion.php?field_hidden=".$_GET["field_hidden"]."&field_text=".$_GET["field_text"]."&multiple=".$_GET["multiple"]."&combo_extra=".$_GET["combo_extra"]."&op=".$_GET["op"]."&col_orden=".$col_orden_url."&dir_orden=".$dir_orden_url."&tipo=".$tipo_url."&page='+this.value\" class='select'>";
				for ($pa=1;$pa<=$num_pages;$pa++){
					echo " <option value='".$pa."'";
				if ($pa == $pag_url)	echo " selected ";
				echo ">".$pa."</option> ";
				}
				echo "</select>";
				echo "</td></tr>";
			}
		}
		else{
		    echo "<tr><td align='center'><br><b>NO SE ENCONTRARON POBLACIONES</b></td></tr>";
		    echo "<tr><td align='center'><br><a href='javascript:history.back(-1);'>Regresar</a></td></tr>";
		}
		?>	
		</table>
	 
	<?  
	}
	?>
</div>
