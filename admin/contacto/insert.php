<?
//INICIALIZACION DE VARIABLES
$contacto_dao = New ContactoDAO();
$contacto_vo = New Contacto();
$org_dao = new OrganizacionDAO();
$espacio_dao = new EspacioDAO();
$espacio_usuario_dao = new EspacioUsuarioDAO();
$contacto_col = new ContactoColDAO();
$contacto_col_op = new ContactoColOpDAO();
$depto_dao = new DeptoDAO();
$mun_dao = new MunicipioDAO();
$id_car = 0;
$id_org = 0;
$nom_org = '--';
$actualizar = false;
$id_depto = 0;
$id_mun = 0;

if (isset($_GET["accion"])){
	$accion = $_GET["accion"];
}
else if (isset($_POST["accion"])){
	$accion = $_POST["accion"];
}

//Caso de Actualizacion
if (isset($_GET["accion"]) && $_GET["accion"] == "actualizar"){
	$id = $_GET["id"];
	$contacto_vo = $contacto_dao->Get($id);
    $id_org = $contacto_vo->id_org[0];

    if (!empty($contacto_vo->id_mun)) {
        $mun = $mun_dao->Get($contacto_vo->id_mun);
        $id_mun = $contacto_vo->id_mun;
        $id_depto = $mun->id_depto;
    }
    $id_depto = $mun->id_depto;
    $nom_org = $org_dao->GetFieldValue($id_org,'nom_org');
    $actualizar = true;
}

?>
<script type="text/javascript" src="../js/jquery-1.4.2.min.js"></script>
<script type="text/javascript" src="../js/contactos.js"> </script>
<style type="text/css">
    label {
        display: block;
    }
</style>
<link type="text/css" rel="stylesheet" href="../style/contactos.css"></style>

<form method="POST">
  <table border="0" cellpadding="3" cellspacing="1" align="center" width="90%">
		<tr class='pathway'>
			<td colspan=4>
				&nbsp;<img src='../images/user-home.png'>&nbsp;<a href='index.php?m_g=consulta&m_e=home'>Home</a> &gt; <a href="index.php?m_e=contacto&accion=listar&class=ContactoDAO&method=ListarTabla&param=">Contactos</a>
			</td>
		</tr>
      <tr class="titulo_lista"><td align="center">
        <b><? echo strtoupper($accion)?> CONTACTO</b>
        <div class="right">
            <img src="images/p4w/qm.png" height="16" width="16" />&nbsp;
            <a href="https://wiki.umaic.org/wiki/Base_de_datos_de_contactos" target="_blank">Ayuda</a>
            &nbsp;
        </div>
        </td></tr>
	</table>
	<table border="0" cellpadding="5" cellspacing="1" align="center" class="data">
        <tr>
            <td valign="top">
                <fieldset>
                    <h2 class="clear">Datos básicos</h2>
                    <label>Nombres (*)</label>
                    <input type="text" id="nombre" name="nombre" size="30" value="<?=$contacto_vo->nombre;?>" class="textfield" />
                    <label>Apellidos (*)</label>
                    <input type="text" id="apellido" name="apellido" size="30" value="<?=$contacto_vo->apellido;?>" class="textfield" />
                    <label>Teléfono directo</label>
                    <input type="text" id="tel" name="tel" size="30" value="<?=$contacto_vo->tel;?>" class="textfield" />
                    <label>Fax</label>
                    <input type="text" id="fax" name="fax" size="30" value="<?=$contacto_vo->fax;?>" class="textfield" />
                    <label>Celular</label>
                    <input type="text" id="cel" name="cel" size="30" value="<?=$contacto_vo->cel;?>" class="textfield" />
                    <label>Email (*)</label>
                    <input type="text" id="email" name="email" size="30" value="<?=$contacto_vo->email;?>" class="textfield" />
					<label>Skype u otros datos de contacto</label>
                    <input type="text" id="social" name="social" size="30" value="<?=$contacto_vo->social;?>" class="textfield" />
                    <label><br />Departamento (*)</label>
                    <select id="id_depto" name="id_depto[]" class="select" onchange="lM('id_depto');return false;">
                        <option value=''></option>
                        <?
                        //DEPTO
                        $depto_dao->ListarCombo('combo',$id_depto,'');
                        ?>
                    </select>
                    <label>Municipio (*)</label>
                    <span id="comboBoxMunicipio">
                        <select class="select" name="id_mun">
                            <option></option>
                            <?php
                            if ($actualizar) {
                                $mun_dao->ListarCombo('combo',$contacto_vo->id_mun,"id_depto=$id_depto");
                            }
                            ?>
                        </select>
                    </span>
                    <br /><br />
                    <div class="instruc">
                        Seleccione los siguientes campos, si no encuentra la opción,
                        puede crearla con el link + Crear opción junto al nombre
                    </div><br />
                    <?
                    //CARACTERISITCAS
                    $caracts = $contacto_col->GetAllArray('');
                    foreach($caracts as $contacto_col){
                        if (isset($_GET["accion"]) && $_GET["accion"] == "actualizar"){
                            $id_car = $contacto_vo->caracteristicas[$contacto_col->id];
                        }

                        $col_nom = $contacto_col->nombre;
                        $col_id = $contacto_col->id;

                        echo "<div class='left'><label>$col_nom</label></div>
                              <div class='right crear_col' data-col-id=$col_id><a href='#'>+ Crear opción</a></div>
                              <div class='clear'></div>
                              <div class='hide crear_col_val' id='div_col_val_$col_id'>
                              <input type='text' id='crear_".$col_id."_val' value='' class='textfield' placeholder='Escriba la nueva opción' />&nbsp;
                              <a href='#' class='crear_col' data-col-id=$col_id>Guardar</a>
                              </div>
                              <select id='".$col_id."_opcion' name='".$col_id."_opcion' class='select'><option value=''></option>";
                              $contacto_col_op->ListarCombo('combo',$id_car,"id_contacto_col=$contacto_col->id");
                        echo "</select><input type='hidden' name='id_contacto_col[]' value='$contacto_col->id'><br /><br />";
                    }
                    ?>
                </fieldset>
            </td>
            <td valign="top">
                <fieldset>
                    <h3 style="display:inline; ">Organización a la que pertenece (*)</h3>
                    [ <a href="?m_e=org&accion=insertar" target="_blank">Crear Organización</a> ]
                    <div class="instruc"><br>Busque la organización a la que pertenece el contacto, para ello, escriba el NOMBRE o la SIGLA en Español
						 o Inglés y seleccione la
                    Organización de la lista que aparecerá,. Si la busqueda no arroja resultados, utilice la opción, [ Crear Organización ],
                    creela y realice la busqueda de nuevo en el campo correspondiente. Tenga presente que al crear una organizacin
                    esta queda automáticamente creada en todo SIDI, por tanto es muy importante no crear una que ya exista!
                    </div>
                    <div style="margin:10px 0">
                        Buscar por nombre o sigla:&nbsp;
                        <input id="org_search" class="textfield" />
                        <div id="org_search_results" class="hide"></div>
                    </div>

                    <div style="margin:10px 0">
                        <b>Organización seleccionada</b>:&nbsp;
                        <input type="hidden" id="org_id" name="id_org[]" value="<?php echo $id_org ?>" />
                        <span id="org_nom"><?php echo $nom_org ?></span>
                    </div>

                <h3>Espacios / Listas de correo a las pertenece el contacto</h3>
                    <!--
                    <div style="margin:10px 0">
                        Filtrar lista por letra inicial&nbsp;
                        <a href="#" class="filter_reset">Todos</a>&nbsp;|
                        <?php
                        /*
                        for ($l=65;$l<91;$l++) {
                            echo '<a href="#" class="filter_letter">'.chr($l).'</a>&nbsp;';
                        }
                        */
                        ?>
                </div>-->
                <div style="margin:10px 0">
                    Buscar por nombre:&nbsp;
                    <input id="lista_search" class="textfield" size="50">
                </div>
                <div class="lista_list">
                            <?

                            function printEspaciosHijos($contacto_vo,$id_papa,$tab){

                                $espacio_dao = new EspacioDAO();
                                $espacio_usuario_dao = new EspacioUsuarioDAO();

                                $condicion = "id_tipo_usuario = ".$_SESSION["id_tipo_usuario_s"];
                                $espacios_x_tipo = $espacio_usuario_dao->GetAllArray($condicion);

                                $cond_c = "id_esp IN (".implode(",",$espacios_x_tipo->id_espacio).") AND id_papa=$id_papa";

                                $espacios = $espacio_dao->GetAllArray($cond_c);

                                $tab .= "&nbsp;&nbsp;&nbsp;";
                                foreach ($espacios as $e=>$esp){

                                    $chk = (in_array($esp->id,$contacto_vo->id_espacio)) ? 'checked' : '';

                                    echo "<div class='o'>$tab<input type='checkbox' name='id_espacio[]' value='$esp->id' $chk>&nbsp;$esp->nombre</div>";

                                    printEspaciosHijos($contacto_vo,$esp->id,'');
                                }
                            }

                            //CONSULTA LOS ESPACIOS POR TIPO DE USUARIO
                            $condicion = "id_tipo_usuario = ".$_SESSION["id_tipo_usuario_s"];
                            $espacios_x_tipo = $espacio_usuario_dao->GetAllArray($condicion);

                            if (count($espacios_x_tipo->id_espacio) > 0){
                                $cond_c = "id_esp IN (".implode(",",$espacios_x_tipo->id_espacio).") AND id_papa=0";

                                $espacios = $espacio_dao->GetAllArray($cond_c);


                                foreach ($espacios as $e=>$esp){

                                    $chk = (in_array($esp->id,$contacto_vo->id_espacio)) ? 'checked' : '';

                                    echo "<div class='o'><input type='checkbox' name='id_espacio[]' value='$esp->id' $chk>&nbsp;$esp->nombre</div>";


                                    printEspaciosHijos($contacto_vo,$esp->id,'');
                                }
                                $error = 0;
                            }
                            else{
                                $error = 1;
                                echo "<div>Su perfil de usuario no tiene habilitado ningún espacio, contacte al administrador</div>";
                            }
                            ?>
                        </div>
                </fieldset>
            </td>
        </tr>
			</td>
		</tr>
		<?
		if ($error == 0){ ?>
			<tr>
			  <td colspan="2" align='center'>
				  <br>
					<input type="hidden" name="accion" value="<?=$accion?>" />
				<input type="hidden" name="id" value="<?=$contacto_vo->id;?>" />
					<input type="submit" name="submit" value="Aceptar" class="boton" onclick="return validar_forma('nombre,Nombres,apellido,Apellidos,tel,Tel�fono directo,email,Email,id_org,Organizaci�n',document.getElementById('email').value);" />
				</td>
			</tr>
			<?
		}
		?>
	</table>
</form>
