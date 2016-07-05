<script type="text/javascript">

Ext.onReady(function(){

var menu_admin = new Ext.menu.Menu({
                            id : 'menu_admin',
                            items : [
                                        {text: 'Donantes',href:'?m_g=admin&m_e=unicef_donante&accion=listar&class=UnicefDonanteDAO&method=ListarTabla',iconCls:'donante'},
                                        {text:'Fuente PBA',href:"?m_g=admin&m_e=unicef_fuente_pba&accion=listar&class=UnicefFuentePbaDAO&method=ListarTabla",iconCls:'fuente'},
                                        {text:'Funcionarios',href:"?m_g=admin&m_e=unicef_funcionario&accion=listar&class=UnicefFuncionarioDAO&method=ListarTabla",iconCls:'funcionario'},
                                        {text:'Indicadores',href:"?m_g=admin&m_e=unicef_indicador&accion=listar&class=UnicefIndicadorDAO&method=ListarTabla",iconCls:'indicador'},
                                        {text:'Socios',href:"?m_g=admin&m_e=unicef_socio&accion=listar&class=UnicefSocioDAO&method=ListarTabla",iconCls:'socio'},
                                        {text:'Usuarios',href:"?m_g=admin&m_e=unicef_usuario&accion=listar&class=UsuarioDAO&method=UnicefListarTabla",iconCls:'usuario'}
                                    ]
                        });

var menu_alim = new Ext.menu.Menu({
                            id : 'menu_alim',
                            items : [
                                {text:'Supervivencia y desarrollo infantil',href:"?m_g=alimentacion&id_c=1",iconCls:'cp1'},
                                {text:'Educaci&oacute;n con Calidad, Desarrollo del Adolescente y Prevenci&oacute;n del VIH/SIDA',href:"?m_g=alimentacion&id_c=2",iconCls:'cp2'},
                                {text:'Protecci&oacute;n y Acci&oacute;n Humanitaria',href:"?m_g=alimentacion&id_c=3",iconCls:'cp3'},
                                {text:'Pol&iacute;ticas P&uacute;blicas Basadas en Evidencia',href:"?m_g=alimentacion&id_c=4",iconCls:'cp4'}
                            
                                    ]
});

var tb = new Ext.Toolbar({
                           items : [
                                    {text:'&nbsp;&nbsp;'},
                                    {   
                                        text:'Home', 
                                        iconCls:'home',
                                        handler: function () { location.href='?m_e=home'; }
                                    },
                                    <?php
                                    // Admin Proyecto
                                    if ($_SESSION['id_tipo_usuario_s'] == 31){
                                        echo "{ text:'Administraci&oacute;n', menu: menu_admin, iconCls:'admin' },";
                                    }
                                    
									echo "{ text:'Alimentaci&oacute;n', menu: menu_alim, iconCls:'alim'},";
                                    ?>
                                    {
                                        text:'Salir',
                                        iconCls:'salir',
                                        handler: function(){ location.href = 't/index_unicef.php?accion=logout'; }
                                    }
                                    ] 

                        });

tb.render('navgral_menu');

});


</script>
