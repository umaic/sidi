<?
//INICIALIZACION DE VARIABLES
$cat_dao = New CategoriaDatoSectorDAO();
$conn = MysqlDb::getInstance();

$sql = "SELECT DISTINCT ID_CATE FROM minificha_datos_resumen ORDER BY ORDEN_CATE";
$rs = $conn->OpenRecordset($sql);
while ($row_rs = $conn->FetchRow($rs)){
	$cats[] = $cat_dao->Get($row_rs[0]);
}

?>
<style type="text/css">
.sortable-list li {
    border : 1px solid #000000;
    cursor : move;
    margin : 2px 0 2px 0;
    padding : 3px;
    background : #FFFFFF;
    width : 200px;
	list-style: none;
}

* {
	font-family: Verdana, Arial, Helvetica, sans-serif;
	font-size: 11px;
}
</style>
<link type="text/css" rel="stylesheet" href="../style/admin.css">
<script src="../admin/js/scriptaculous-js-1.8.1/lib/prototype.js"></script>
<script type="text/javascript">

var id_cat_selected = 1;

var handlerFunc = function(t) {
	//alert(t.responseText);
}

var errFunc = function(t) {
	alert('Error ' + t.status + ' -- ' + t.statusText);
}

function updateOrder()
{
	var options = {
					method : 'post',
					parameters : Sortable.serialize('item_list'),
					onSuccess:handlerFunc, onFailure:errFunc
				  };

	new Ajax.Request('../admin/ajax_data.php?object=setOrdenPerfil', options);
}

function updateOrderDato()
{
	var options = {
					method : 'post',
					parameters : Sortable.serialize('item_sublist_'+id_cat_selected),
					onSuccess:handlerFunc, onFailure:errFunc
				  };

	new Ajax.Request('../admin/ajax_data.php?object=setOrdenPerfilDato', options);
}

function showHide(){
	var combo_cat = document.getElementById('id_cat');
	var id_cat = combo_cat.options[combo_cat.selectedIndex].value;

	<?
	foreach ($cats as $cat){
		echo "document.getElementById('item_sublist_$cat->id').style.display = 'none';";
	}
	?>
	
	id_cat_selected = id_cat;

	var obj = document.getElementById('item_sublist_'+id_cat);

	if (obj.style.display == ''){
		obj.style.display = 'none';
	}
	else{
		obj.style.display = '';
	}
}
</script>
<script src="../admin/js/scriptaculous-js-1.8.1/src/scriptaculous.js"></script>
<form method="POST" onsubmit="submitForm(event);return false;">
  <table border="0" cellpadding="3" cellspacing="1" width="70%" align="center" class='tabla_insertar'>
	  <tr class='titulo_lista'><td align="center" colspan=3><b>Ordenar Categorias del Resumen del Perfil</b></td></tr>
	  <tr><td>&nbsp;</td></tr>
	  <tr><td>1. Primero ordene las categorias arrastrando y soltando la categoria en la posici&oacute;n que desee, el sistema autom&aacute;ticamente actualizar&aacute; el orden</td></tr>
	  <tr>
	  	<td>
			2. Una vez ordenadas las categorias, puede ordenar los datos de: <select class='select' id='id_cat'>
			<?
			foreach ($cats as $cat) { 
				echo "<option value=$cat->id>$cat->nombre</option>";
			}
			?>
			</select>&nbsp; <input type='button' value='Mostrar' class='boton' onclick="showHide()">
		</td>
	</tr>
	  <tr>
	  	<td align="center">
			<ul id="item_list" class="sortable-list">
				<?php
				foreach ($cats as $cat) { 
					echo "<li id=\"item_$cat->id\">$cat->nombre</li>";

					echo "<ul id='item_sublist_$cat->id' style='display:none' class='sortable_list'>";
					$sql = "SELECT id_dato, nom_dato FROM minificha_datos_resumen as m JOIN dato_sector USING(id_dato) WHERE m.id_cate = $cat->id ORDER BY ORDEN_DATO";
					$rs = $conn->OpenRecordset($sql);
					while ($row = $conn->FetchRow($rs)){
						echo "<li id=\"item_$row[0]\">$row[1]</li>";
					}

					echo "</ul>";
					?>
					<script>	
						Sortable.create('item_sublist_<?=$cat->id?>', { onUpdate : updateOrderDato });
					</script>
					<?
				}
				?>
			</ul>				
			<script>	
				Sortable.create('item_list', { onUpdate : updateOrder });
			</script>

	</table>
</form>
