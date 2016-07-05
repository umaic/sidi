<?
//INICIALIZACION DE VARIABLES
$org_vo = New Organizacion();
$org_dao = New OrganizacionDAO();
$tipo_org_dao = New TipoOrganizacionDAO();
$tipo_org_vo = New TipoOrganizacion();
$conn = MysqlDb::getInstance();
$depto_vo = New Depto();
$depto_dao = New DeptoDAO();


$condicion = '';

$id_depto = Array();
$num_deptos = 0;
$id_cat = 0;
$chk_conf = "";

?>

<form action="<?=$_SERVER['PHP_SELF']?>" method="POST">
	<table border="0" cellpadding="5" cellspacing="1" width="80%" align="center" class="tabla_org">
	  <tr><td align="center" colspan='2'><b>CREAR ORGANIZACION</b></td></tr>
			<tr>
				<!-- DATOS GENERALES : INICIO -->
				<td>
					<table border="0" cellpadding="5" cellspacing="1" width="100%" align="center" class="tabla_input_org">
						<tr>
							<td><b>Nombre</b></td>
							<td colspan="5">
					  		<input type="text" name="nombre" id="nombre" class="textfield" value='<?=$org_vo->nom;?>' size="80" />
						  	</td>
						</tr>
						<tr>
						  <td><b>Sigla</b></td>
						  <td>
						  		<!--<input type="text" name="sigla" id="sigla" class="textfield" value="<?=$org_vo->sig;?>" size="15" onkeydown="document.getElementById('ocurrenciasOrgSigla').style.display='';getDataV1('ocurrenciasOrg','ajax_data.php?object=ocurrenciasOrg&case=sigla&s='+this.value,'ocurrenciasOrgSigla')" />-->
						  		<input type="text" name="sigla" id="sigla" class="textfield" value="<?=$org_vo->sig;?>" size="15" />
						  	</td>
						</tr>
						<tr>
							<td><b>Tipo</b></td>
							<td>
							<select id="id_tipo" name="id_tipo" class="select">
							<option value=''>Seleccione alguno...</option>
							<?
							//TIPO
							$tipo_org_dao->ListarCombo('combo',$org_vo->id_tipo,$condicion);
							?>
							</select>
							</td>
				        </tr>
				    <tr>
                        <td><b>Sede</b></td>
                        <td>
					        <select id="id_depto" name="id_depto[]" class="select" onchange="lM('id_depto');return false;">
                                <option>Departamento</option>
                                <?
                                //DEPTO
                                $depto_dao->ListarCombo('combo',$id_depto,'');
                                ?>
                            </select>
                            <span id="comboBoxMunicipio"></span>
                        </td>
                </tr>
				<!-- DATOS GENERALES : FIN -->
			</tr>
			<tr>
				<td align='center' colspan="2">
					<input type="hidden" name="id" value="<? echo $id; ?>">
					<input type="hidden" name="id_usuario" value="<?php echo $_SESSION['id_usuario']; ?>">
				    <input type="hidden" name="4w" value="1">
				    <input type="hidden" name="accion" value="<? echo $accion; ?>">
				    <input type="submit" name="submit" value="Aceptar" class="boton" onclick="return validar_forma('id_mun_sede,Municipio Sede,id_tipo,Tipo,nombre,Nombre','')">
				</td>
			</tr>
</table>
</form>
