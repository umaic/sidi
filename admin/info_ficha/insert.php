<?
//LIBRERIAS
include_once("fckeditor/fckeditor.php");

//INICIALIZACION DE VARIABLES
$dao = New InfoFichaDAO();
$vo = New InfoFicha();

$chk = array('org' => '',
			 'desplazamiento' => '',
			 'mina' => '',
			 'dato_sectorial' => '',
			 'evento_c' => '');

if (isset($_GET["modulo"]) && $_GET["modulo"] != ''){
	$modulo = $_GET["modulo"]; 
	$chk[$modulo] = ' selected ';
}
?>

<form method="POST" onsubmit="submitForm(event);return false;">
  <table width="750" align="center" class="tabla_insertar">
	<tr>
		<td align="center">
			Seleccione el m&oacute;dulo&nbsp;
			<select name='modulo' class='select' onchange="refreshTab('index_parser.php?m_e=info_ficha&accion=actualizar&modulo='+this.value)">
			<option value=''>---</option>
			<option value='org' <?=$chk['org']?> >Organizaciones</option>
			<option value='desplazamiento' <?=$chk['desplazamiento']?>>Desplazamiento</option>
			<option value='mina' <?=$chk['mina']?>>Accidentes con Mina</option>
			<option value='dato_sectorial' <?=$chk['dato_sectorial']?>>Datos Sectoriales</option>
			<option value='evento_c' <?=$chk['evento_c']?>>Eventos del Conflicto</option>
			</select>		
		</td>
	</tr>
	<?
	if (isset($_GET["modulo"]) && $_GET["modulo"] != ''){
		$arr = $dao->GetAllArray("modulo = '".$modulo."'");
		
		$oFCKeditor_intro = new FCKeditor('texto') ;
		$oFCKeditor_intro->BasePath = 'fckeditor/';
		$oFCKeditor_intro->Width  = '700';
		$oFCKeditor_intro->Height = '350';
		$oFCKeditor_intro->Value = $arr->texto;
		
		?>
		<tr>
			<td align="center">
				<? $oFCKeditor_intro->Create() ; ?>
			</td>
		</tr>
		<tr>
		  <td colspan="2" align='center'>
			  <br>
				<input type="hidden" name="accion" value="actualizar" />
				<!-- Para usar con AJAX, es necesario actualizar el valor del hidden con el valor actual del editor FCK antes de enviar la forma -->
				<input type="submit" name="submit" value="Aceptar" onclick="document.getElementById('texto').value = FCKeditorAPI.GetInstance('texto').GetXHTML() " class="boton" />
			</td>
		</tr>
		<tr><td>&nbsp;</td></tr>
		<?
	}
	?>

</table>
</form>	
