var index_file = 'index_parser.php';

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
			}

			).show();

			return false;
}

/* Borra un registro de la parrilla */
function borrarRegistro(class_v,id){

	var tabsCmp = Ext.getCmp('tabPanel');
	Ext.Ajax.request({
						url		: index_file,
						method	: 'GET',
						params 	: { accion : 'borrar',
									class  : class_v,
									method : 'Borrar',
									param  : id
								  },
						success	: function (result,request){
									//Refresh el listado, getUpdater funciona, porque el tab se cargo con autoLoad.
									
									// Alerta con minWidh para que no quede en 2 lineas el mensaje
									Ext.Msg.show({
										msg		:result.responseText,
										minWidth: 250,
										maxWidth: 400,
										buttons : Ext.Msg.OK
									});
									tabsCmp.activeTab.getUpdater().refresh();
								}
					});
}

/* Refresca el contenido de una window, dado los parametros del url */
function refreshWindow(url){
	
	var win = Ext.getCmp('windowIU');
	
	win.getUpdater().update({ url: url });
}


/* Muestra ventana con el contenido como parametro */
function addWindowAlert(html){
	new Ext.Window({
				id			: 'windowIU',
				html        : html,
				region 		: 'center',
				width		: 750,
				height		: 400,
				autoScroll	: true,
				closeable	: true,
				bodyCssClass: 'body',
			}

			).show();

			return false;
}

/*Procesa las formas */
function submitForm(e){
	var form = e.target;
	var f_elements = form.elements;
    var url = index_file;

    if (e.target.name != undefined) {
        url += '?m_e=' + e.target.name;
    }

    if (e.target.name == 'p4w') {
        document.getElementById('submit').value = 'Procesando....';
    }

	Ext.Ajax.request({
            url		: url,
            method	: 'POST',
            form 	: form,
            headers : {
                        'content' : 'text/html; charset=iso-8859-1'
            },
            success	: function (result,request){
                    //Refresh el listado, getUpdater funciona, porque el tab se cargo con autoLoad.

                    if (Ext.isDefined(Ext.getCmp('windowIU')))	Ext.getCmp('windowIU').close();

                    // Alerta con minWidh para que no quede en 2 lineas el mensaje
                    /*
                    Ext.Msg.show({
                        msg		:result.responseText,
                        id      : 'windowIU',
                        minWidth: 250,
                        maxWidth: 400,
                        //buttons : Ext.Msg.OK
                    });*/
                    
                    addWindowAlert(result.responseText);
                    if (e.target.name == 'p4w') {
                        document.getElementById('submit').value = 'Listo!';
                    } 
                    
                }
        });
}

/* Cierra ventana */
function closeWindow(){
	
    if (Ext.isDefined(Ext.getCmp('windowIU')))	Ext.getCmp('windowIU').close();
	
}

