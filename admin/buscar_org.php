<?
session_start();
//LIBRERIAS
include_once 'lib/libs_org.php';

//INICIALIZACION DE VARIABLES
$org_dao = New OrganizacionDAO();
$tipo_dao = New TipoOrganizacionDAO();
$buscar = 0;
$op = 0;
$order_by = '';
$arr = Array();
$sel_col_orden = Array("NOM_ORG" => "", "SIG_ORG" => "");
$sel_dir_orden = Array("ASC" => "","DESC" => "");
$col_orden_url = "";
$dir_orden_url = "";
$tipo_url = "";
$criterio = "";
$criterio_col = "";

$num_reg_pag = 50;
$num_arr_total = $org_dao->numRecords('');

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

//FILTRO POR TIPO
if (isset($_GET["tipo"]) && $_GET["tipo"] != ""){
	$tipo_url = $_GET["tipo"];
	
	if ($condicion != '')
	    $condicion .= " AND ID_TIPO = ".$tipo_url;
	else
		$condicion = " ID_TIPO = ".$tipo_url;
		
	$num_arr_total = $org_dao->numRecords($condicion);
}


if ($buscar == 1 || $op == 2){				
	$limit = $inicio.",".$fin; 
	$arr = $org_dao->GetAllArray($condicion,$limit,$order_by);
	$num_arr = count($arr);
}



?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Sistema de Informaci&oacute;n Central OCHA - Colombia</title>
<?
//CSS
switch ($_SESSION["m_g"]){
	case "admin":
		echo "<link href='../style/admin.css' rel='stylesheet' type='text/css' />";
	break;
	case "alimentacion":
		echo "<link href='../style/input.css' rel='stylesheet' type='text/css' />";
	break;
	case "consulta":
		echo "<link href='../style/consulta.css' rel='stylesheet' type='text/css' />";
	break;
	default:

		echo "<link href='../style/consulta.css' rel='stylesheet' type='text/css' />";
	break;
}
?>
<script src="js/general.js"></script>
<script>
function enviar_formulario(input_hidden,input_text,multiple,combo_extra,id,nom){
	var num_options = opener.document.getElementById(input_text).options.length;
	if (multiple == 0){
		for(o=0;o<num_options;o++){
		  opener.document.getElementById(input_text).options[o] = null;
		}
	  	opener.AddOption1(nom,id,opener.document.getElementById(input_text));
		opener.document.getElementById(input_hidden).value = id;
		window.close();
	}
	else{
	  
	    //VALOR APORTE
	  	if (combo_extra != "")
			var num_per = prompt("Valor del aporte de esta Organización al proyecto",0);
			
	  	in_array = 0;
		for(o=0;o<num_options;o++){
			if (id == opener.document.getElementById(input_text).options[o].value){
			  in_array = 1;
			}
		}
		
		if (in_array == 0){
			
			var combo = opener.document.getElementById(input_text);
			var combo_hidden = opener.document.getElementById(input_hidden);
			
		  	//AGREGA LA OPCION
		  	opener.AddOption1(nom,id,combo);

		  	if (combo_extra != "")
			  	opener.AddOption1(num_per,num_per,opener.document.getElementById(input_text+combo_extra));
		  	
		  	//COLOCA EL VALOR EN EL INPUT HIDDEN
			CopiarOpcionesCombo(combo,combo_hidden);
		  	if (combo_extra != "")
				CopiarOpcionesCombo(opener.document.getElementById(input_text+combo_extra),opener.document.getElementById(input_hidden+combo_extra));

			//RESIZE DEL COMBO
			resizeCombo(opener.document.getElementById(input_text));
		  	if (combo_extra != "")
				resizeCombo(opener.document.getElementById(input_text+combo_extra));
			
			if(!confirm('Organización agregada, desea agregar otra?')){
			  window.close();
			}
		}
		else{
		  alert('Ya existe la Organización');
		}
	}
}
</script>
</head>
<body>
<h1 class="info">Sistema de Informaci&oacute;n  Central &ndash; OCHA &ndash; Naciones Unidas &ndash; Colombia</h1><h1 class="info">Sistema de Informaci&oacute;n  Central &ndash; OCHA &ndash; Naciones Unidas &ndash; Colombia</h1>
<div id="cabecera"></div>
<!-- CONTENIDO : INICIO -->
<div id="cont">
	<br>
	<?
	//OPCIONES INICIALES
	if (!isset($_GET["op"]) && $buscar == 0){ ?>
		<table align="center" cellspaing="1" cellpadding="3" class="tabla_consulta">
			<tr><td class='titulo_lista' align='center'>BUSCAR ORGANIZACION</td></tr>
			<tr>
				<td>
					<table cellpadding="5" cellspacing="1"> 
						<tr><td><b>Opción 1</b>: </td><td align="left"><a href="buscar_org.php?<?=$_SERVER["QUERY_STRING"]?>&op=1">Buscar Por Palabra Clave</a></td></tr>
						<tr><td><b>Opción 2</b>: </td><td align="left"><a href="buscar_org.php?<?=$_SERVER["QUERY_STRING"]?>&op=2">Listar todas las Organizaciones</a></td></tr>
					</table>
				</td>
			</tr>
		</table>
	<?
	}
	//BUSAR POR PALABRA
	else if (isset($_GET["op"]) && $op == 1  && $buscar == 0){ ?>
		<form action="buscar_org.php?<?=$_SERVER["QUERY_STRING"]?>" method="POST">
		<table align='center' cellspacing="1" cellpadding="0" border="0" >
			<tr>
				<td align="center" width="50%">
					<table width="99%" cellspacing="1" cellpadding="5" border="0" class="tabla_consulta">
						<tr><td align="center" colspan="2" class='titulo_lista'><b>Busqueda de Organizaciones por palabra</b></td></tr>							
						<tr><td align='center' colspan="2">&nbsp;</td></tr> 
						<tr>
							<td align='right'>Palabra</td>
							<td><input type='text' id='criterio' name='criterio' class='textfield' size="40" /></td>
						</tr>
						<tr>
							<td align='right'>Buscar en</td>
							<td>
								<select id='criterio_col' name='criterio_col' class='select'>
								  <option value='NOM_ORG'>Nombre</option>
								  <option value='SIG_ORG'>Sigla</option>
								</select>
							</td>
						</tr>
						<tr><td>&nbsp;</td></tr>
						<tr>
							<td align='center' colspan="2">
								<input type="submit" name="buscar" value="Buscar Organizaciones" class="boton" onclick="return validar_forma('criterio,Palabra','')" />	
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
	    				<tr class='titulo_lista'><td align='center' colspan='2'>Resultado de la búsqueda de Organizaciones por palabra</td></tr>
						<tr><td>Palabra: <b>".$criterio."</b> - Buscando en: <b>";
						if ($criterio_col == "SIG_ORG")	echo "Sigla";
						else										echo "Nombre";
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
						<option value='NOM_ORG' ".$sel_col_orden["NOM_ORG"].">Nombre</option>
						<option value='SIG_ORG' ".$sel_col_orden["SIG_ORG"].">Sigla</option>
					</select>&nbsp;
					<select id='dir_orden' name='dir_orden' class='select'>
						<option value='ASC' ".$sel_dir_orden["ASC"].">Ascendente</option>
						<option value='DESC' ".$sel_dir_orden["DESC"].">Descendente</option>
					</select>&nbsp;
					<input type='button' value='Ordenar' class='boton' onclick=\"location.href='buscar_org.php?field_hidden=".$_GET["field_hidden"]."&field_text=".$_GET["field_text"]."&multiple=".$_GET["multiple"]."&combo_extra=".$_GET["combo_extra"]."&op=".$op."&tipo=".$tipo_url."&buscar=".$buscar."&criterio=".$criterio."&criterio_col=".$criterio_col."&page=".$pag_url."&col_orden='+document.getElementById('col_orden').value+'&dir_orden='+document.getElementById('dir_orden').value;\" \>
				</td>
			</tr>
			<tr>
				<td colspan='5'><b>Filtrar listado por Tipo de Organización</b>:
					<select id= 'tipo' name='tipo' class='select'>";
						$tipo_dao->ListarCombo('combo',$tipo_url,'');
			echo "</select>&nbsp;
					<input type='button' value='Filtrar' class='boton' onclick=\"location.href='buscar_org.php?field_hidden=".$_GET["field_hidden"]."&field_text=".$_GET["field_text"]."&multiple=".$_GET["multiple"]."&combo_extra=".$_GET["combo_extra"]."&op=".$op."&tipo=".$tipo_url."&buscar=".$buscar."&criterio=".$criterio."&criterio_col=".$criterio_col."&page=".$pag_url."&col_orden=".$col_orden_url."&dir_orden=".$dir_orden_url."&tipo='+document.getElementById('tipo').value;\" \>
				</td>
			</tr>";
			
		}
		
		if ($num_arr > 0){
			echo"<tr class='titulo_lista'>
				<td>Nombre</td>
				<td>Sigla</td>
				<td>Tipo</td>";
				echo "<td align='center' width='150'>Registros: ".$num_arr_total."</td>
	    		</tr>";
									
			for($p=0;$p<$num_arr;$p++){
				$style = "";
		  		if (fmod($p+1,2) == 0)  $style = "fila_lista";
		
				//NOMBRE
				if ($arr[$p]->nom != ""){
				  
					//NOMBRE DEL TIPO DE ORGANIZACION
			  		$tipo = $tipo_dao->Get($arr[$p]->id_tipo);
			  		$nom_tipo = $tipo->nombre_es;			  
			  		
					echo "<tr class='".$style."'>";
					echo "<td>".$arr[$p]->nom."</td>";
					echo "<td>".$arr[$p]->sig."</td>";
					echo "<td>".$nom_tipo."</td>";
					echo "<td align='center'><input type='button' value='Enviar al Formulario' onclick=\"enviar_formulario('".$_GET["field_hidden"]."','".$_GET["field_text"]."','".$_GET["multiple"]."','".$_GET["combo_extra"]."','".$arr[$p]->id."','".$arr[$p]->nom."');\" class='boton'></td>";
			  		echo "</tr>";
			  	}
			}
			
			echo "<tr><td>&nbsp;</td></tr>";
			
		  	//PAGINACION
		  	if ($num_arr_total > $num_reg_pag){
				 
				$num_pages = ceil($num_arr_total/$num_reg_pag); 
				echo "<tr><td colspan='5' align='center'>";
				
				echo "Ir a la página:&nbsp;<select onchange=\"location.href='buscar_org.php?field_hidden=".$_GET["field_hidden"]."&field_text=".$_GET["field_text"]."&multiple=".$_GET["multiple"]."&combo_extra=".$_GET["combo_extra"]."&op=".$_GET["op"]."&col_orden=".$col_orden_url."&dir_orden=".$dir_orden_url."&tipo=".$tipo_url."&page='+this.value\" class='select'>";
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
		    echo "<tr><td align='center'><br><b>NO SE ENCONTRARON ORGANIZACIONES</b></td></tr>";
		    echo "<tr><td align='center'><br><a href='javascript:history.back(-1);'>Regresar</a></td></tr>";
		}		
		?>	
		</table>
	 
	<?  
	}
	?>
</div>
