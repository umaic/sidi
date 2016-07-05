<?
//INICIALIZACION DE VARIABLES
$proyecto_vo = New Proyecto();
$proyecto_dao = New ProyectoDAO();
$org_vo = New Organizacion();
$org_dao = New OrganizacionDAO();
$municipio_vo = New Municipio();
$municipio_dao = New MunicipioDAO();
$depto_vo = New Depto();
$depto_dao = New DeptoDAO();
$region_vo = New Region();
$region_dao = New RegionDAO();
$poblado_vo = New Poblado();
$poblado_dao = New PobladoDAO();
$resguardo_vo = New Resguardo();
$resguardo_dao = New ResguardoDAO();
$parque_nat_vo = New ParqueNat();
$parque_nat_dao = New ParqueNatDAO();
$div_afro_vo = New DivAfro();
$div_afro_dao = New DivAfroDAO();
$sector_dao = New SectorDAO();
$sector_vo = New Sector();
$enfoque_dao = New EnfoqueDAO();
$enfoque_vo = New Enfoque();
$poblacion_dao = New PoblacionDAO();
$poblacion_vo = New Poblacion();
$estado_dao = New EstadoProyectoDAO();
$estado_vo = New EstadoProyecto();
$tema_dao = New TemaDAO();
$tema_vo = New Tema();
$contacto_dao = New ContactoDAO();
$contacto_vo = New Contacto();
$moneda_dao = New MonedaDAO();
$moneda_vo = New Moneda();
$calendar = new DHTML_Calendar('js/calendar/','es', 'calendar-win2k-2', false);
$calendar->load_files();

$conn = MysqlDb::getInstance();

if (isset($_GET["accion"])){
    $accion = $_GET["accion"];
}
else if (isset($_POST["accion"])){
    $accion = $_POST["accion"];
}

$id_depto = Array();
$num_deptos = 0;
$id_cat = 0;
$chk_conf = "";
$id_orgs_d = "";
$id_orgs_e = "";
$id_orgs_s = "";
$display_secuencia_1 = 'none';
$display_secuencia_1_1 = 'none';
$display_secuencia_2 = 'none';
$display_socios_un = 'none';
$selected_joint = '';
$selected_mou = '';
$selected_ind = '';
$costo_proy = '';
$costo_proy_ind = '';

//Moneda, por defecto U$
$proyecto_vo->id_mon = 1;

//Caso de Actualizacion
if ($accion == "actualizar" && !isset($_POST["submit"])){
    $id = $_GET["id"];
    $proyecto_vo = $proyecto_dao->Get($id);

    $id_depto = $proyecto_vo->id_deptos;
    $id_muns = $proyecto_vo->id_muns;

    $id_orgs_d = implode("|",$proyecto_vo->id_orgs_d);
    //$id_orgs_d_valor_ap = implode("|",$proyecto_vo->id_orgs_d_valor_ap);
    $id_orgs_e = implode("|",$proyecto_vo->id_orgs_e);
    $id_orgs_s = implode("|",$proyecto_vo->id_orgs_s);
    $id_orgs_coor = implode("|",$proyecto_vo->id_orgs_coor);

    $id_beneficiarios = implode("|",$proyecto_vo->id_beneficiarios);
    //$id_beneficiarios_cant = implode("|",$proyecto_vo->cant_per);

    if ($proyecto_vo->inicio_proy == "0000-00-00")	$proyecto_vo->fecha_ini = "";

    // Presupuesto por marco de proyecto
    if ($proyecto_vo->joint_programme_proy == 1){
        $display_secuencia_1 = '';
        $selected_joint = ' selected ';
        $display_secuencia_1_1 = '';
        $display_socios_un = '';
        $costo_proy = $proyecto_vo->costo_proy;
    }

    if ($proyecto_vo->mou_proy == 1){
        $display_secuencia_1 = '';
        $selected_mou = ' selected ';
        $display_secuencia_1_1 = '';
        $display_socios_un = '';
        $costo_proy = $proyecto_vo->costo_proy;
    }
    
    
    if ($proyecto_vo->interv_ind_proy == 1){
        $display_secuencia_2 = '';
        $selected_ind = ' selected ';
        $costo_proy_ind = $proyecto_vo->costo_proy;
    }
    
}


?>

<script type="text/javascript" src="js/tabber.js"></script>
<script type="text/javascript">
function listarMunicipios(combo_depto){

    selected = new Array();
    ob = document.getElementById(combo_depto);
    for (var i = 0; i < ob.options.length; i++){
        if (ob.options[ i ].selected)
            selected.push(ob.options[ i ].value);
    }
    var id_deptos = selected.join(",");

    if (selected.length == 0){
        alert("Debe seleccionar algún departamento");
    }
    else{
        getDataV1('comboBoxMunicipio','ajax_data.php?object=comboBoxMunicipio&multiple=21&id_deptos='+id_deptos,'comboBoxMunicipio')
    }
}

function AddOption1(text,value,combo){

    combo.options[combo.options.length] = new Option(text, value);

}
function listarTemasHijos(id_papa,display){

    document.getElementById('hijo_'+id_papa).style.display = display;
    document.getElementById('nieto_'+id_papa).style.display = display;

}

function validar(){

    if (validar_forma('nom_proy,Nombre o descripción,id_estp,Estado,duracion_proy,Duración del proyecto','')){

        //Validar presupuesto proyecto dependiendo el marco
        var obj_marco_proy = document.getElementById('marco_proy');
        var marco_proy = obj_marco_proy.value;
        var id_costo_proy;
        var costo_proy;

        if (marco_proy == 3)    id_costo_proy = 'costo_proy_ind';
        else                    id_costo_proy = 'costo_proy';

        costo_proy = document.getElementById(id_costo_proy).value;

        if (costo_proy == ''){
            alert('Falta definir el presupuesto total del proyecto');
            return false;
        }
        //Check la suma de los aportes de las agencias coordinadas vs el total del presupuesto si no es Agencia Independiente
        if (marco_proy < 3){ 
            var obj_id_orgs_coor = document.getElementsByName('id_orgs_coor[]');
            if (checkInputChecked(obj_id_orgs_coor)){
                var total = 0;

                var id_orgs_coor = getOptionsCheckBox(obj_id_orgs_coor).split(',');
                for(var i=0;i<id_orgs_coor.length;i++){
                    total += parseInt(document.getElementById('id_orgs_coor_valor_ap_'+id_orgs_coor[i]).value);
                }
            }

            if (total < costo_proy){
                alert('El TOTAL del presupuesto es mayor que los aportes de las agencias de trabajo coordinado, no olvide que debe especificar el aporte de su agencia, por favor verifique');
                return false;
            }
            else if (total > costo_proy){
                alert('El TOTAL del presupuesto es menor que los aportes de las agencias de trabajo coordinado, por favor verifique los valores asignados a cada agencia incluyendo la suya');
                return false;
            }
        }
    }
    else{
        return false;
    }
}

function benfOtroCual(id_pob,chk){

    var otro_obj = document.getElementById('benf_otro_cual');

    if (id_pob == '44' && chk == true){
        otro_obj.style.display = '';
    }
    else{
        otro_obj.style.display = 'none';
    }
}

function showTemaNieto(check,id){
    var p_papa = document.getElementById('p_nieto_papa_'+id);

    if (check){
        p_papa.style.display = '';
    }
    else{
        p_papa.style.display = 'none';
    }
}
function secuenciaPresupuesto(marco){

    if (marco == 1 || marco == 2){
        var secuencia = new Array('secuencia_1','secuencia_2','secuencia_1_1','secuencia_1_2','socios_un');
        for(i=0;i<secuencia.length;i++){
            document.getElementById(secuencia[i]).style.display = 'none';
        }

        document.getElementById('secuencia_1').style.display = '';
        document.getElementById('lider_proy').selectedIndex = 0;

    }

    else if (marco == 3){	
        var secuencia = new Array('secuencia_1','secuencia_2','secuencia_1_1','secuencia_1_2','socios_un');
        for(i=0;i<secuencia.length;i++){
            document.getElementById(secuencia[i]).style.display = 'none';
        }

        document.getElementById('secuencia_2').style.display = '';		
    }
    else if (marco == '1_1'){
        var secuencia = new Array('2','1_1','1_2');
        for(i=0;i<secuencia.length;i++){
            document.getElementById('secuencia_'+secuencia[i]).style.display = 'none';
        }

        document.getElementById('submit').style.display = '';		
        document.getElementById('secuencia_1_1').style.display = '';		
        document.getElementById('socios_un').style.display = '';		
    }
    else if (marco == '1_2'){
        // Deshabilita el boton de enviar y muestra texto

        var secuencia = new Array('2','1_1','1_2');
        for(i=0;i<secuencia.length;i++){
            document.getElementById('secuencia_'+secuencia[i]).style.display = 'none';
        }

        document.getElementById('submit').style.display = 'none';
        document.getElementById('secuencia_1_2').style.display = '';		
        document.getElementById('socios_un').style.display = 'none';
    }
}
</script>

<form method="POST" action="">
<div>
    <b>MAPEO DE ACTIVIDADES DE NACIONES UNIDAS EN COLOMBIA</b><br />
</div>
<div class="tabber" id="alimentacion">
    <div class="tabbertab">
	<!--<h2>&nbsp;<img src="images/proyecto/info_basica.gif" border="0"></h2><br>-->
	<h2>INFORMACION BASICA</h2><br>
	<table border="0" cellpadding="5" cellspacing="1" width="100%" align="center" class="input_proyecto">
	    <tr>
		<td colspan="2">
		    <b>1. Nombre o descripci&oacute;n</b><br />
		    <textarea style="width:720px;height:60px" name="nom_proy" id="nom_proy" class="textfield"><?=$proyecto_vo->nom_proy;?></textarea>
		</td>
		<td valign="top">
		    <b>2. Estado</b><br />
		    <select id="id_estp" name="id_estp" class="select">
			<option value=''>Seleccione alguno...</option>
			    <?
			    //ESTADO DEL PROYECTO
			    $estado_dao->ListarCombo('combo',$proyecto_vo->id_estp,'');
			    ?>
		    </select>
		</td>
	    </tr>
	    <tr>
		<td>
		    <b>3. Agencia Ejecutora</b><br />
		    <?
			//Perfil undaf-admin
			if ($_SESSION["id_tipo_usuario_s"] == 30 || $_SESSION["undaf"] == 0){
				$id_e = ($accion == 'actualizar') ? $proyecto_vo->id_orgs_e[0] : 0;
				$_SESSION["id_org"] = $id_e;

				echo "<select name='id_orgs_e' class='select' style='width:400px'>";
				$org_dao->ListarCombo('combo',$id_e,'id_tipo=4');
				echo "</select>";
			}
			else{
				echo $_SESSION["nom_org"]; 

				$id_e = $_SESSION["id_org"];
				echo '<input type="hidden" id="id_orgs_e" name="id_orgs_e" value="'.$id_e.'" />';
			}
			?>
		</td>
		<td valign="top">
		    <b>4. Fecha de inicio</b><br />
		
		    <? $calendar->make_input_field(
		    // calendar options go here; see the documentation and/or calendar-setup.js
		    array('firstDay'       => 1, // show Monday first
		    'showOthers'     => true,
		    'ifFormat'       => '%Y-%m-%d',
		    'timeFormat'     => '12'),
		    // field attributes go here
		    array('class'       => 'textfield',
		    'value'       => $proyecto_vo->inicio_proy,
		    'size'	 => 10,
		    'name'        => 'inicio_proy')); 
		    ?>
			<br />(YYYY-MM-DD)
		</td>
		<td valign="top">
		    <b>5. Duración en meses del proyecto</b><br />
		    <input type="text" name="duracion_proy" id="duracion_proy" class="textfield" value="<?=$proyecto_vo->duracion_proy;?>" size="5"  onkeypress="return validarFloat(event)"/>
			&nbsp;(ej. 12, 12.5)
		</td>
	    </tr>
                <tr>
                    <td valign="top">
                    <b>6. PRESUPUESTO</b>&nbsp;
                    <!-- Modena todo es US$ -->
                    <input type="hidden" name="id_mon" value="1">
                    <br /><br />
                    6.1&nbsp;En que marco se desarrolla el proyecto:&nbsp;
                    <select class="select" name="marco_proy" id="marco_proy" onchange="secuenciaPresupuesto(this.value)">
                        <option value="0"></option>
                        <option value="1" <?=$selected_joint?>>Joint Programme</option>
                        <option value="2" <?=$selected_mou?>>MOU</option>
                        <option value="3" <?=$selected_ind?>>Agencia independiente</option>
                    </select>
                    <span id="secuencia_1" style="display:<?=$display_secuencia_1?>">
                    <br /><br />
                    6.2&nbsp;En el marco seleccionado, su agencia es la <b>AGENCIA L&iacute;DER</b>?
                    <select class="select" id="lider_proy" name="lider_proy" onchange="secuenciaPresupuesto(this.value)">
                        <option value="0"></option>
                        <option value="1_1" <? if($accion == 'actualizar')  echo 'selected'; ?> >Si</option>
                        <option value="1_2">No</option>
                    </select>
                    </span>

                    <span id="secuencia_1_1" style="display:<?=$display_secuencia_1_1?>">
                    <br /><br />
                    6.3&nbsp;En su calidad de <b>agencia l&iacute;der</b> reporte aqu&iacute; el presupuesto <b>TOTAL</b> del proyecto:
                    <br /><br />&nbsp;&nbsp;<input type="text" name="costo_proy" id="costo_proy" class="textfield" value="<?=$costo_proy;?>" size="15" onkeypress="return validarNum(event)"/>
                    &nbsp;(Valor enterno en US$)
                    </span>
                    
                    <span id="secuencia_1_2" style="display:none">
                    <br /><br />
                    <b>LOS PROYECTOS SOLO PUEDEN SER DEFINIDOS POR LAS AGENCIAS L&iacute;DERES, NO ES POSIBLE CONTINUAR</b>
                    </span>

                    <span id="secuencia_2" style="display:<?=$display_secuencia_2?>">
                    <br /><br />
                    6.2&nbsp;Cu&aacute;l es el presupuesto del programa/proyecto/intervenci&oacute;n?
                    <br /><br />&nbsp;&nbsp;<input type="text" name="costo_proy_ind" id="costo_proy_ind" class="textfield" value="<?=$costo_proy_ind;?>" size="15"  onkeypress="return validarNum(event)"/>
                    &nbsp;(Valor enterno en US$, sin comas ni puntos)
                    </span>
                    
                    <br /><br />
                    <div id='socios_un' style="display:<?=$display_socios_un?>">
                    6.4&nbsp;Existe en este proyecto un trabajo coordinado con otras agencias del SNU?<br />
                    <br />
                    Seleccione la(s) agencias y especifique al frente el aporte en US$ de cada una. Tambi&eacute;n
                    debe seleccionar su agencia para poder identificar su aporte, pero solo en el caso de que exista
                    trabajo coordinado con otras, de lo contrario, el aporte de su agencia es el total del presupuesto y no
                    necesita especificarlo acá.
                    <div style="height:250px;width:350px;overflow:auto;margin:0 auto 0 auto;">
                    <table width="95%" cellspacing="1" cellpading="2" align="center">
                    <?
                    $snu = $org_dao->GetAllArray("id_tipo = 4 AND id_org_papa = 0",'','sig_org');
                    foreach ($snu as $org_snu){
                        $chk = '';
                        $valor_ap = '';
                        if (in_array($org_snu->id,$proyecto_vo->id_orgs_coor)){
                            $chk = 'checked';
                            $valor_ap = $proyecto_vo->id_orgs_coor_valor_ap[$org_snu->id];
                        }
                        echo "<tr><td width='150'><input type='checkbox' id='id_orgs_coor_$org_snu->id' name='id_orgs_coor[]' value='$org_snu->id' $chk>&nbsp;".$org_snu->sig."</td>";
                        echo "<td><input type='text' id='id_orgs_coor_valor_ap_$org_snu->id' name='id_orgs_coor_valor_ap[]' value='$valor_ap' class='textfield' size='15' onkeypress=\"return validarNum(event)\"></td></tr>";
                    }
                    ?>
                    </table></div></div>
		</td>
                
<!--
<td valign="top">
	    		<b>Aporte Agencia US$</b><br />
	    		<textarea name="valor_aporte_donantes" id="valor_aporte_donantes" class="textfield" style="width:200px;height:30px" onkeypress="return validarNum(event)"><?=$proyecto_vo->valor_aporte_donantes;?></textarea>
				<br />&nbsp;(Valor enterno en dolares)
	    	</td>-->
	    	<td valign="top" colspan="2">
		    <b>Socios externos, contrapartes e implementadores</b><br />
		    <input type="hidden" id="id_orgs_s" name="id_orgs_s" value="<?=$id_orgs_s?>" />
		    <select id="socios" name="socios[]" multiple size="<?=count($proyecto_vo->id_orgs_s)?>" class="select" style="width:400px" />
                        <?
                        foreach($proyecto_vo->id_orgs_s as $id_s){
                            $nom = $org_dao->GetName($id_s);	
                            echo "<option value=".$id_s.">".$nom."</option>";
                        }
                        ?>
		    </select>
                    <br /><br /><input type="button" name="buscar_donante" value="Buscar Socio" class="boton" onclick="window.open('buscar_org.php?field_hidden=id_orgs_s&field_text=socios&multiple=1&combo_extra=','','top=100,left=100,width=800,height=700,scrollbars=1');" />
                    &nbsp;<input type="button" name="borrar_p" value="Borrar" class="boton" onclick="delete_option(document.getElementById('socios'));CopiarOpcionesCombo(document.getElementById('socios'),document.getElementById('id_orgs_s'));" />
                    <br /><br />
                    <b>Observaciones</b><br />
                    <textarea name="info_extra_socios" class="textfield" style="width:400px;height:50px"><?=$proyecto_vo->info_extra_socios ?></textarea>
                    <br /><br />
                    <b>Aporte Socios externos, contrapartes e implementadores</b><br />
                    <textarea name="valor_aporte_socios" id="valor_aporte_socios" class="textfield" style="width:200px;height:30px" onkeypress="return validarNum(event)"><?=$proyecto_vo->valor_aporte_socios;?></textarea>
                    &nbsp;(Valor enterno en US$)
                    <br />
	    	</td>
	    </tr>
	    <tr>
                <td valign="top" colspan="3">
                    <table cellpadding="10">
                        <tr>
                            <td>
                                <b>Donantes</b><br /><br />
                                <input type="hidden" id="id_orgs_d" name="id_orgs_d" value="<?=$id_orgs_d?>" />
                                <select id="donantes" name="donantes[]" multiple size="<?=count($proyecto_vo->id_orgs_d)?>" class="select" style="width:400px" />
                                    <?
                                    foreach($proyecto_vo->id_orgs_d as $id_dona){
                                    $nom = $org_dao->getname($id_dona);	
                                    echo "<option value=".$id_dona.">".$nom."</option>";
                                    }
                                    ?>
                                </select><br /><br /><input type="button" name="buscar_donante" value="Buscar donante" class="boton" onclick="window.open('buscar_org.php?field_hidden=id_orgs_d&field_text=donantes&multiple=1&combo_extra=','','top=100,left=100,width=800,height=700,scrollbars=1');" />
                                &nbsp;<input type="button" name="borrar_p" value="Borrar" class="boton" onclick="delete_option(document.getElementById('donantes'));copiaropcionescombo(document.getelementbyid('donantes'),document.getelementbyid('id_orgs_d'));" />
                            </td>
                            <td valign="top">	
                                <b>Observaciones</b><br /><br />
                                <textarea name="info_extra_donantes" class="textfield" style="width:400px;height:50px"><?=$proyecto_vo->info_extra_donantes ?></textarea>
                            </td>
                        </tr>
                    </table>	
                </td>
	    </tr>
	</table>
	</td>
	</tr>
	</table>
    </div>
    <!-- INFORMACION GENERAL : FIN -->	

    <div class="tabbertab" id="tema">
	<h2>TEMAS Y PROGRAMAS</h2><br>
	<table border="0" cellpadding="5" cellspacing="1" width="100%" align="center" class="input_proyecto">
	    <?
	    //Si la organizacion de la que es responsable el usuario es de tipo Agencia ONU id=4, muestra los temas UNDAF
	    //$id_tipo_un = 4;
	    //if((isset($_SESSION["id_tipo_org"]) && $_SESSION["id_tipo_org"] == $id_tipo_un) || $_SESSION["id_tipo_usuario_s"] == 30){ ?>
			<tr><td><b>Indicar el AREA UNDAF y  el/los OUTCOME/s y -de ser posible-  los productos UNDAF a los que apunte el proyecto/programa</b></td></tr>
			<?
			$id_c_undaf = 1;
			$t_undaf = $tema_dao->GetAllArray("id_clasificacion = $id_c_undaf AND id_papa=0");

			foreach($t_undaf as $t){

				if (array_key_exists($t->id,$proyecto_vo->id_temas)){
					$check = " checked ";
					$disabled = " ";
					$txt = $proyecto_vo->texto_extra_tema[$t->id];
					$display_hijos = 'block';
				}
				else{
					$check = "";
					$disabled = " disabled ";
					$txt =  "OUTCOME/s -- productos UNDAF"; 
					$display_hijos = 'none';
				}

				echo "<tr>
					<td valign='top'>
						<table>
							<tr>
								<td>
									<input type='checkbox' id='id_tema_$t->id' name='id_temas[]' value='$t->id' $check onclick=\"if (this.checked == true){ document.getElementById('texto_extra_tema_$t->id').disabled = false; document.getElementById('texto_extra_tema_$t->id').value = '';listarTemasHijos($t->id,'');} else {document.getElementById('texto_extra_tema_$t->id').disabled = true;listarTemasHijos($t->id,'none');}\">&nbsp;<b>$t->nombre</b>
									<br />
									<textarea id='texto_extra_tema_$t->id' name='texto_extra_tema_$t->id' style='width:400px;height:200px' class='textfield' $disabled>$txt</textarea>
								</td>
								<td><div id='hijo_".$t->id."' class='sub_tema' style='display:$display_hijos'>";
									echo "<p class='tit_tema_insert'>$t->nombre</p>";
									
									$hijos = $tema_dao->GetAllArray("id_papa = $t->id");
									
									foreach ($hijos as $hijo){
										$checked_hijo[$hijo->id] = ($accion == 'actualizar' && isset($proyecto_vo->id_temas[$t->id]["hijos"]) && in_array($hijo->id,$proyecto_vo->id_temas[$t->id]["hijos"])) ? ' checked ' : ''; 
										
										echo "<input type='checkbox' name='id_tema_$t->id[]' value=$hijo->id ".$checked_hijo[$hijo->id];
										echo " onclick=\"showTemaNieto(this.checked,$hijo->id)\">&nbsp;".$hijo->nombre."<br /><br />";
									}
								
								echo "</div></td>
								<td><div id='nieto_".$t->id."' class='sub_tema' style='display:$display_hijos'>";

									
									foreach ($hijos as $hijo){
									
										$display_nieto = ($checked_hijo[$hijo->id] == '') ? 'none' : '';
										echo "<div id='p_nieto_papa_$hijo->id' style='display:$display_nieto'><p class='tit_tema_insert'>$hijo->nombre</p>";
									
										$nietos = $tema_dao->GetAllArray("id_papa = $hijo->id");
									
										foreach ($nietos as $nieto){
											echo "<input type='checkbox' name='id_tema_$t->id[]' value=$nieto->id";

											if ($accion == 'actualizar' && isset($proyecto_vo->id_temas[$t->id]["nietos"]) && in_array($nieto->id,$proyecto_vo->id_temas[$t->id]["nietos"]))	echo " checked ";

											echo ">&nbsp;".$nieto->nombre."<br /><br />";
										}
										echo '</div>';
									}
								
								echo "</div></td>
							</tr>
						</table>
					</td>
				</tr>";
			}
	    //}
	    ?>
	</table>
    </div>

    <div class="tabbertab" id="tema">
	<h2>BENEFICIARIOS DE LOS PROYECTOS</h2><br>
	<table border="0" cellpadding="5" cellspacing="1" width="100%" align="center" class="input_proyecto">
		<tr>
			<td>
				<b>Beneficiarios DIRECTOS</b><br /><br />
				<?
				$pobs = $poblacion_dao->GetAllArray("","","");
				foreach($pobs as $pob){
					$check = (in_array($pob->id,$proyecto_vo->id_beneficiarios)) ? " checked " : "";
					echo "<input type='checkbox' name='id_beneficiarios[]' value='$pob->id' $check onclick=\"benfOtroCual($pob->id,this.checked)\">&nbsp;$pob->nombre_es<br />";
				}
				?>
			</td>
			<td>
				<b>Beneficiarios INDIRECTOS</b><br /><br />
				<?
				$pobs = $poblacion_dao->GetAllArray("","","");
				foreach($pobs as $pob){
					$check = (in_array($pob->id,$proyecto_vo->id_beneficiarios_indirectos)) ? " checked " : "";
					echo "<input type='checkbox' name='id_beneficiarios_indirectos[]' value='$pob->id' $check onclick=\"benfOtroCual($pob->id,this.checked)\">&nbsp;$pob->nombre_es<br />";
				}
				?>
			</td>
			<td valign="top">
				Cantidad de benificiarios directos del proyecto (opcional) genero, etnico, et&aacute;reo, situaci&oacute;n de desplazamiento, etc<br />
				<textarea name="cant_benf_proy"  style="width:500px;height:100px" class="textfield"><?=$proyecto_vo->cant_benf_proy?></textarea>
				<br /><br />
                                <?
                                $id_pob_otros = 44;
				$display_otro = (in_array($id_pob_otros,$proyecto_vo->id_beneficiarios) || in_array($id_pob_otros,$proyecto_vo->id_beneficiarios_indirectos)) ? '' : 'none';	
				?>
				<span id="benf_otro_cual" style="display:<?=$display_otro?>">
				Otros - Cual?
				<br />
				<textarea id="otro_cual_benf_proy" name="otro_cual_benf_proy" style="width:500px;height:100px;" class="textfield"><?=$proyecto_vo->otro_cual_benf_proy?></textarea>
				</span>
			</td>
	     </tr>
	</table>
    </div>


    <div class="tabbertab" id="cobertura">
	<h2>COBERTURA GEOGRAFICA</h2><br>
	<table border="0" cellpadding="5" cellspacing="1" width="100%" align="center" class="input_proyecto">
	     <tr>
		<td valign="top">
		    <b>Este proyecto es de cobertura nacional</b>&nbsp;
		    <select name="cobertura_nal_proy" class="select">
				<option value=0 <? if ($proyecto_vo->cobertura_nal_proy == 0)	echo " selected " ?>>No</option>
				<option value=1 <? if ($proyecto_vo->cobertura_nal_proy == 1)	echo " selected " ?>>Si</option>
		    </select><br /><br />
		    <font class="nota">Seleccione los Departamentos, use la tecla Ctrl para seleccionar varios, luego use la opción de Listar Municipios. Se listaran los municipios agrupados por los departamentos selccionados</font>
		    <table width="100%" border="0">
			<tr>
			    <td width="200">
				<table>
				    <tr>
					<td>
					    <b>Departamento</b><br>
					    <select id="id_depto" name="id_depto[]"  multiple size="21" class="select">
						<?
						//DEPTO
						$depto_dao->ListarCombo('combo',$id_depto,'');
						?>
					    </select><br /><br />
					    &raquo;&nbsp;<a href="#" onclick="listarMunicipios('id_depto');return false;">Listar Muncipios</a>
					</td>
				    </tr>
				</table>
			    </td>
			    <td valign="top">
				<table width="450">
				    <tr>
					<td id="comboBoxMunicipio">
					<?
					if ($accion == "actualizar"){

					    echo "<b>Municipio</b><br />";
					    echo "<select name='id_muns[]' class='select' multiple size='21'>";
					    foreach ($id_depto as $id_d){
							$depto = $depto_dao->Get($id_d);
							//echo $id_depto;
							$muns = $municipio_dao->GetAllArray("ID_DEPTO ='$id_d'");

							echo "<option value='' disabled>-------- ".$depto->nombre." --------</option>";
							foreach ($muns as $mun){
								echo "<option value='".$mun->id."'";
								if (in_array($mun->id,$id_muns))	echo " selected ";
								echo ">".$mun->nombre."</option>";
							}
					    }
					    echo "</select>";
					}
					?>
					</td>
				    </tr>
				</table>
			    </td>
			</tr>
		    </table>						
		</td>
		<!--
		<td valign="top">
		    <b>Oficina desde la que se cubre este proyecto</b><br />
		    <select name="id_orgs_cubre[]" class="select" style="width:450px" multiple size="20">
				<?
				//$id_o = $_SESSION["id_org"];
				//$org_dao->ListarCombo("combo",$proyecto_vo->id_orgs_cubre,"id_org_papa = $id_o OR id_org = $id_o");
				?>
			</select><br /><br />
		</td>-->
	    </tr>
	</table>
    </div>
    <br />
    <div align="center">
	<input type="hidden" name="id" value="<? echo $proyecto_vo->id_proy; ?>">
	<input type="hidden" name="accion" value="<? echo $accion; ?>">
	<input type="hidden" name="si_proy" value="undaf">
	<input type="submit" id="submit" name="submit" value="Aceptar" class="boton" onclick="return validar()">
    </div>
 </div>   
</form>
