var index_file = '/sissh/admin/index_parser_unicef.php';

/* Muestra ventana con el formulario de Insert/Update */
function addWindowIU(m_e,accion,id){
	var url = index_file+'?m_e='+m_e+'&accion='+accion+'&id='+id;
	new Ext.Window({
				id			: 'windowIU',
				autoLoad 	: url,
				region 		: 'center',
				width		: 750,
				height		: 400,
				autoScroll	: true,
				closeable	: true,
				bodyCssClass: 'body',
				title		: 'Editar'
			}

			).show();

			return false;
}

/* Muestra ventana con el formulario de Insert/Update */
function addWindowIUTree(url){
	new Ext.Window({
				id			: 'windowIU',
				autoLoad 	: url,
				region 		: 'center',
				width		: 900,
				height		: 600,
				autoScroll	: true,
				closeable	: true,
				bodyCssClass: 'body',
				title		: 'Editar',
                modal       : true
			}

			).show();

			return false;
}

/*Procesa las formas */
function submitForm(e,id_node_papa){
	
    var form = (e.target) ? e.target : e.srcElement;

	Ext.Ajax.request({
						url		: index_file,
						method	: 'POST',
						form 	: form,
						headers : {
									'content' : 'text/html; charset=iso-8859-1'
						},
						success	: function (result,request){

									if (Ext.isDefined(Ext.getCmp('windowIU')))	Ext.getCmp('windowIU').close();

									// Alerta con minWidh para que no quede en 2 lineas el mensaje
									Ext.Msg.show({
										msg		:result.responseText,
										minWidth: 250,
										maxWidth: 400,
										buttons : Ext.Msg.OK
									});
								   
                                   // Reload el node papa
                                    if (Ext.getCmp('treePanel')){
                                        var treeCmp = Ext.getCmp('treePanel')
                                        treeCmp.getNodeById(id_node_papa).reload();
                                    }
                                    else{
                                        location.reload();
                                    }
								}
					});
}

/*Procesa las formas */
function submitFormIframe(e,id_node_papa){
	
    var form = (e.target) ? e.target : e.srcElement;

	Ext.Ajax.request({
						url		: index_file,
						method	: 'POST',
						form 	: form,
						headers : {
                            'content' : 'text/html; charset=iso-8859-1'
						},
						success	: function (result,request){

                            // Alerta con minWidh para que no quede en 2 lineas el mensaje
                            Ext.Msg.show({
                                msg		:result.responseText,
                                minWidth: 250,
                                maxWidth: 400,
                                buttons : Ext.Msg.OK
                            });
                           
                           // Reload el node papa
                            if (window.parent.Ext.getCmp('treePanel')){
                                var treeCmp = window.parent.Ext.getCmp('treePanel')
                                treeCmp.getNodeById(id_node_papa).reload();
                            }
                            else{
                                window.parent.location.reload();
                            }
                            
                            if (Ext.isDefined(window.parent.Ext.getCmp('windowIU'))) window.parent.Ext.getCmp('windowIU').close();
                        }
					});
}

/* Borra un registro de la parrilla */
function borrarRegistro(class_v,id,id_node_papa){

	Ext.Ajax.request({
						url		: index_file,
						method	: 'GET',
						params 	: { accion : 'borrar',
									class_v  : class_v,
									method : 'Borrar',
									param  : id
								  },
						success	: function (result,request){
									
									if (Ext.isDefined(Ext.getCmp('windowIU')))	Ext.getCmp('windowIU').close();
                                    
                                    // Alerta con minWidh para que no quede en 2 lineas el mensaje
									Ext.Msg.show({
										msg		:result.responseText,
										minWidth: 250,
										maxWidth: 400,
										buttons : Ext.Msg.OK
									});
                                    
                                   // Reload el node papa
                                    if (Ext.getCmp('treePanel')){
                                        var treeCmp = Ext.getCmp('treePanel')
                                        treeCmp.getNodeById(id_node_papa).reload();
                                    }
                                    else{
                                        location.reload();
                                    }
								}
					});
}

/* Borra un registro de la parrilla desde un iframe - caso indicadores */
function borrarRegistroIframe(class_v,id,id_node_papa){

	Ext.Ajax.request({
						url		: index_file,
						method	: 'GET',
						params 	: { accion : 'borrar',
									class_v  : class_v,
									method : 'Borrar',
									param  : id
								  },
						success	: function (result,request){
									
									if (Ext.isDefined(Ext.getCmp('windowIU')))	Ext.getCmp('windowIU').close();
                                    
                                    // Alerta con minWidh para que no quede en 2 lineas el mensaje
									Ext.Msg.show({
										msg		:result.responseText,
										minWidth: 250,
										maxWidth: 400,
										buttons : Ext.Msg.OK
									});
                           
                                   // Reload el node papa
                                    if (window.parent.Ext.getCmp('treePanel')){
                                        var treeCmp = window.parent.Ext.getCmp('treePanel')
                                        treeCmp.getNodeById(id_node_papa).reload();
                                    }
                                    else{
                                        window.parent.location.reload();
                                    }
                                    
                                    if (Ext.isDefined(window.parent.Ext.getCmp('windowIU'))) window.parent.Ext.getCmp('windowIU').close();
                                    
								}
					});
}

/* Refresca el contenido de un tab, dado los parametros del url */
function refreshTab(url){
	
	var tabsCmp = Ext.getCmp('tabPanel');
	
	tabsCmp.activeTab.getUpdater().update({ url: url });
}

/* Refresca el contenido de una window, dado los parametros del url */
function refreshWindow(url){
	
	var win = Ext.getCmp('windowIU');
	
	win.getUpdater().update({ url: url });
}

function refreshNode(id_node){

    if (Ext.getCmp('tabPanel')){
        var tabsCmp = Ext.getCmp('tabPanel')
        var treeCmp = Ext.getCmp(tabsCmp.activeTab.id);
        treeCmp.getNodeById(id_node).reload();
    }
}

