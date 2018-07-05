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

<pre>2. Sincronización de Espacios
2.1 Obtener la lista de espacios de sidi
2.2 Obtener la lista de categorias del grupo de mailchimp
2.3 Para cada espacio de SIDI
-->Verificar si existe la categoria en mailchimp. Si no está, se agrega a mailchimp
2.4 Para cada categoria de mailchimp
- Verificar que esté en SIDI. Si no está, se borra de mailchimp

3. Sincronización de Contactos
3.1 Obtener la lista de correos de sidi
3.2 Obtener la lista de correos de mailchimp
3.3 Para cada correo de sidi
- Verificar que este en mailchimp. Si no está, se agrega a mailchimp
3.4 Para cada correo de mailchimp
- Verificar que esté en SIDI. Si no está, se borra de mailchimp</pre>.