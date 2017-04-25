<?
//INICIALIZACION DE VARIABLES
$modalidad_asistencia_dao = New ModalidadAsistenciaDAO();
$modalidad_asistencia_vo = New ModalidadAsistencia();

if (isset($_GET["accion"])){
    $accion = $_GET["accion"];
}
else if (isset($_POST["accion"])){
    $accion = $_POST["accion"];
}

//Caso de Actualizacion
if (isset($_GET["accion"]) && $_GET["accion"] == "actualizar"){
    $id = $_GET["id"];
    $modalidad_asistencia_vo = $modalidad_asistencia_dao->Get($id);
}

?>

<form method="POST" onsubmit="submitForm(event);return false;">
    <table class="tabla_insertar">
        <tr><td align="right">Nombre</td><td><input type="text" id="nombre" name="nombre" size="40" value="<?=$modalidad_asistencia_vo->nombre;?>" class="textfield" /></td></tr>
        <tr>
            <td colspan="2" align='center'>
                <br>
                <input type="hidden" name="accion" value="<?=$accion?>" />
                <input type="hidden" name="id" value="<?=$modalidad_asistencia_vo->id;?>" />
                <input type="submit" name="submit" value="Aceptar" class="boton" onclick="return validar_forma('nombre,Nombre','');" />
            </td>
        </tr>
    </table>
</form>
