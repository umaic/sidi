<?
//INICIALIZACION DE VARIABLES
$org_dao = New OrganizacionDAO();

$caso = (isset($_GET["caso"])) ? $_GET["caso"] : 1;
$chk = array(1=>'checked',2=>'');
$chk[$caso] = 'checked';
?>

<script>
function chgURL(caso){
	location.href = 'index.php?m_e=org&accion=sincro_cnrr&class=OrganizacionDAO&method=ListarOrgSincronizarCNRR&param=&caso='+caso;
}

function validar(){
	if (validateCheckboxInputMsg(document.forms[0],'No ha seleccionado ninguna Organización!')){
		return confirm('Está seguro que desea sincronizar las Organizaciones seleccionadas?');
	}
	else 	return false;
}
</script>

<form action="<?=$_SERVER['PHP_SELF']?>?accion=sincro_cnrr" method="POST">
<table cellspacing="1" cellpadding="5" class="tabla_consulta" width="600" align="center">
	<tr><td align='center' class='titulo_lista'>SINCRONIZACION DE ORGANIZACIONES :: SIDI <-> CNRR</td></tr>
	<tr><td>&nbsp;</td></tr>
	<? 
	if (!isset($_POST["submit"])){
		echo '<tr><td><input type="radio" name="caso" value="1" onclick="chgURL(1)" '.$chk[1].'>Listar organizaciones que estan en SIDI y que NO est&aacute;n en CNRR (La comparaci&oacute;n se hace por nombre y sigla, y se tienen en cuenta solo las organizaciones marcadas como CNRR)</td></tr>';
			  //<tr><td><input type="radio" name="caso" value="2" onclick="chgURL(2)" '.$chk[2].'>Listar organizaciones que estan en CNRR y que NO est&aacute;n en SIDI</td></tr>
		echo ' <tr><td>';
				$org_dao->ListarOrgSincronizarCNRR(1); 
	}
	else{
		echo '<tr><td>';
		$org_dao->SincronizarCNRR($_POST["caso"]); 
	}
	?>
		</td>
	</tr>
</table>
</form>