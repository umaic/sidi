<script type="text/javascript" src="js/p4w/insert.js"></script>
<script type="text/javascript">
var uploader;

$j(function() {
    $j(document).click(function(event) {
        if (!$j(event.target).hasClass('ocurrencia')) {
             $j(".ocurrencia").hide();
        }
    });


});
</script>

<div id="alim_importar">    
    <div id="cols" class="left">
        <h1>Importaci&oacute;n de proyectos:</h1>
        <p><b>El archivo de texto que va a importar debe estar separado por '|' (barra vertical), los textos no deben
        estar entre comillas y debe tener la siguiente estructura:</b></p>
        <p>La dos primeras filas deben tener los titulos de las columnas.  
        La segunda fila debe tener los siguientes titulos obligatoriamente:
        (Las filas con informaci&oacute;n, de la tercera en adelante solo deben tener obligatorio las marcadas con * 
    <div class="wiki"><img src="images/p4w/qm.png" />
        <a href="http://www.colombiassh.org/gtmi/wiki/index.php/4W" target="_blank">Wiki</a>
    </div>
            <fieldset>
                <legend>Informaci&oacute;n B&aacute;sica</legend>
                <ul>
                    <li><b>* Columna A</b>: C&oacute;digo Interno del proyecto</li>
                    <li><b>* Columna B</b>: Tipo de proyecto</li>
                    <li><b>* Columna C</b>: Nombre del proyecto</li>
                    <li><b>* Columna D</b>: Descripci&oacute;n de la ayuda</li>
                </ul>
            </fieldset>
            <fieldset>
                <legend>Organizaci&oacute;n encargada</legend>
                <ul>
                    <li><b>* Columna E</b>: Sigla</li>
                    <li><b>* Columna F</b>: Nombre</li>
                    <li><b>* Columna G</b>: Tipo</li>
                </ul>
            </fieldset>
            <fieldset>
                <legend>Implementador</legend>
                <ul>
                    <li><b>Columna H</b>: Sigla</li>
                    <li><b>* Columna I</b>: Nombre</li>
                    <li><b>* Columna J</b>: Tipo</li>
                </ul>
            </fieldset>
            <fieldset>
                <legend>Sector Humanitario</legend>
                <ul>
                    <li><b>* Columna K</b>: Sector: debe ser el nombre del tema Cluster</li>
                </ul>
            </fieldset>
            <fieldset>
                <legend>Resultados Esperados UNDAF</legend>
                <ul>
                    <li><b>* Columna L</b>: Resultados UNDAF separados por guón (-)</li>
                </ul>
            </fieldset>
            <fieldset>
                <legend>Tiempo de ejecución</legend>
                <ul>
                    <li><b>* Columna M</b>: Fecha de inicio  (A&ntilde;o/Mes/Dia)
                    </li>
                    <li>
                        <b>* Columna N</b>: Fecha de finalizaci&oacute;n (A&ntilde;o/Mes/Dia)
                    </li>
                    <li>* <b>Columna O</b>: Tiempo de ejecuci&oacute;n: n&uacute;mero en meses</li>
                    <li><b>Columna P</b>: Estado del proyecto</li>
                    <li>* <b>Columna Q</b>: Presupuesto USD$: Valor entero sin moneda ni separadores</li>
                </ul>
            </fieldset>
            <fieldset>
                <legend>Donante</legend>
                <ul>
                    <li><b>Columna R</b>: Donante (Fuente de los recursos)</li>
                    <li><b>Columna S</b>: Donante Monto USD</li>
                    <li><b>Columna T</b>: Fecha de adjudicación de recursos</li>
                </ul>
            </fieldset>
            <fieldset>
                <legend>HRP</legend>
                    <li><b>Columna U</b>: El proyecto hace parte del Plan Humanitario de Respuesta? (0 o 1)</li>
                </ul>
            </fieldset>
            <fieldset>
                <legend>Contacto en terreno</legend>
                <ul>
                    <li>* <b>Columna V</b>: Responsable (nombres y apellidos)</li>
                    <li>* <b>Columna W</b>: Email</li>
                    <li>* <b>Columna X</b>: Celular</li>
                </ul>
            </fieldset>
            <fieldset>
                <legend>Poblaci&oacute;n beneficiaria</legend>
                <ul>
                    <li>* <b>Columna Y</b>: Total</li>
                    <li><b>Columna Z</b>: Total Mujeres</li>
                    <li><b>Columna AA</b>: Mujeres 0-5 a&ntilde;os</li>
                    <li><b>Columna AB</b>: Mujeres 6-18 a&ntilde;os</li>
                    <li><b>Columna AC</b>: Mujeres 18-64 a&ntilde;os</li>
                    <li><b>Columna AD></b>: Mujeres 65+ a&ntilde;os</li>
                    <li><b>Columna AE</b>: Total Hombres</li>
                    <li><b>Columna AF</b>: Hombres 0-5 a&ntilde;os</li>
                    <li><b>Columna AG</b>: Hombres 6-18 a&ntilde;os</li>
                    <li><b>Columna AH</b>: Hombres 18-64 a&ntilde;os</li>
                    <li><b>Columna AI</b>: Hombres 65+ a&ntilde;os</li>
                </ul>
            </fieldset>
            <fieldset>
                <legend>Beneficiarios Indirectos</legend>
                <ul>
                    <li><b>Columna AJ</b>: Total</li>
                    <li><b>Columna AK</b>: Total Mujeres</li>
                    <li><b>Columna AL</b>: Total Hombres</li>
                </ul>
            </fieldset>
            <fieldset>
                <legend>Cobertura</legend>
                <ul>
                    <li>* <b>Columna AM</b>: Divipola de 5 digitos separados por comas</li>
                    <li><b>Columna AN</b>: Nombre del Departamento</li>
                    <li><b>Columna AO</b>: Nombre del Municipio</li>
                    <li><b>Columna AP</b>: Latitud en grados decimales</li>
                    <li><b>Columna AQ</b>: Longitud</li>
                </ul>
            </fieldset>
            <fieldset>
                <legend>Interagencialidad</legend>
                <ul>
                    <li>* <b>Columna AR</b>: Es proyecto interagencial?</li>
                </ul>
            </fieldset>
            <fieldset>
                <legend>Cash Based Transfer</legend>
                <ul>
                    <li>* <b>Columna AS</b>: Modalidad de Asistencia</li>
                    <li><b>Columna AT</b>: Mecanismo de entrega</li>
                    <li><b>Columna AU</b>: Frecuencia de distribución</li>
                    <li><b>Columna AV</b>: Valor por persona (USD)</li>
                </ul>
            </fieldset>

        </p>
    </div>	
    <div id="prs" class="left">
        <p>Descargue la &uacute;ltima versi&oacute;n de la matriz de captura donde est&aacute; el formato a diligenciar y el listado de Organizaciones, Contactos, Departamentos y Municipios para que use el nombre exacto en las columnas de: Organizaci&oacute;n encargada,
        Operador, Contacto en terreno, Sector, Cobertura del archivo que va a importar</p>
        <p><img src="../images/p4w/save.png">&nbsp;<a href="../formato4w.xlsx">Formato captura</a></p>
        <p>
            <h1>Importar en la base de datos?</h1>
            <br />La opci&oacute;n Si <b>solamente</b> aparece cuando no hay errores al procesar el archivo &nbsp;No&nbsp;<input type="radio" id="" name="insertar_db" value="0" checked />&nbsp;
            <span id="insertar_bd_si" class="hide">Si&nbsp;<input type="radio" id="" name="insertar_db" value="1" /></span>
        </p>
        <hr>
        <p>Los proyectos que tengan el mismo <b>c&oacute;digo</b>, la misma <b>Organizaci &oacute;n encargada 
            (Sigla-Nombre-Tipo)</b>, la misma <b>fecha de inicio</b> y la misma <b>localizaci&oacute;n</b> no ser&aacute;n
            importados dado que se consideran como duplicidad
        </p>
        <div id="file-uploader">		
        </div>
        <div id="check">
            <h2></h2>
            <div id="rsu"></div>
            <div id="btn_co">
                <b>ORGANIZACIONES</b><br />
                Busque aqu&iacute; las organizaciones que no existen, intente con nombres, siglas tanto en 
                espa&ntilde;ol como en ingles, si la encuentra actualice el archivo a importar,
                si efectivamente no existen use el siguiente bot&oacute;n para crearlas<br />
                <input type="hidden" id="id_org" name="id_org" />
                <textarea type="text" id="nom_org" name="nom_org" 
                   class="textfield tlarge" onkeydown="buscarOcurr(event, 'nom_org', '', 'ocurr_org');"></textarea>
                   
                <div id="ocurr_org" class="ocurrencia"></div>
                <br />
                    <a href="#" onclick="if (confirm('Est\xe1 seguro que la Organizaci\xf3n no existe en el sistema? Intente con todas las opciones posibles')) 
                                                 {addWindowIU('org','insertarOrg4w','');}return false;" class="boton icon insertar">
                            Organizaci&oacute;n</a>
            </div>
            <div id="msg"></div>
        </div>
    </div>
    
    <script>        
        function createUploader(){            
                uploader = new qq.FileUploader({
                element: document.getElementById('file-uploader'),
                //debug: true,
                action: 'ajax_data.php?object=importarP4w',
                onComplete: function(s,d,r) { 
                    
                    $j('#check').show();
                    $j('#check h2').html(r.h2);
                    $j('#check #rsu').html(r.rsu);
                    $j('#check #msg').html(r.msg);

                    // Habilita el Si de importar en la base de datos
                    if ($j('input[name="insertar_db"]:checked').val() == 0) {
                        
                        var pe = $j('#rsu').find('.error').html();

                        if (pe == 'Proyectos con errores : 0') {
                            $j('#insertar_bd_si').show();

                            $j('#check #btn_co').hide();
                            $j('#check h2').html('');
                            $j('#check #rsu').html('');
                            $j('#check #msg').html('El archivo no ha presentado errores en el procesamiento, selecciones Si en la pregunta: Insertar en la base de datos y vuelva a cargarlo');
                        }
                        else if (pe == 'Proyectos con errores : -1') {
                            $j('#check #rsu').html('');
                            $j('#check #btn_co').hide();
                        }
                    }
                    else {
                        $j('#check h2').html('');
                        $j('#check #rsu').html(r.rsu);
                        $j('#check #btn_co, .error, #msg').hide();
                    }
                    
                },
                onSubmit : function(id, fileName){
                    $j('#check').hide();
                    uploader.setParams({
                       insertar_db:  $j('input[name="insertar_db"]:checked').val()
                    });
                }
            });           
        }
        
        // in your app create uploader as soon as the DOM is ready
        // don't wait for the window to load  
        window.onload = createUploader;     
    </script> 
</div>
