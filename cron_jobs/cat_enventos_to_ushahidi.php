<?
include_once("../admin/lib/common/mysqldb.class.php");

include_once("../admin/lib/model/cat_evento_c.class.php");
include_once("../admin/lib/model/subcat_evento_c.class.php");

//DAO
include_once("../admin/lib/dao/cat_evento_c.class.php");
include_once("../admin/lib/dao/subcat_evento_c.class.php");

$conn = MysqlDb::getInstance();
$cat_dao = New CatEventoConflictoDAO();
$subcat_dao = New SubCatEventoConflictoDAO();
$db_cesar = 'sihcesar';
$cats = $cat_dao->GetAllArray('');

function randColor() {
    $letters = "1234567890ABCDEF";
    $str = '';
    for($i=0;$i<6;$i++)
    {
        $pos = rand(0,15);
        $str .= $letters[$pos];
    }
    return $str;
}

foreach ($cats as $cat){
    $locale = 'es_CO';
    $title = $cat->nombre;
    $color = randColor();
    
    $sql = "INSERT INTO $db_cesar.category (parent_id,locale,category_title,category_description,category_color,category_visible) VALUES (0,'$locale','$title','$title','$color',1)";
    $conn->Execute($sql);
    $id_papa = $conn->GetGeneratedID();
    
    $id_cat_to_id_cat[$cat->id] = $id_papa;

    // Sub Cats
    $subs = $subcat_dao->GetAllArray("id_cateven = $cat->id");

    foreach ($subs as $sub){
        $title = $sub->nombre;
        $color = randColor();

        $sql = "INSERT INTO $db_cesar.category (parent_id,locale,category_title,category_description,category_color,category_visible) VALUES ($id_papa,'$locale','$title','$title','$color',1)";
        $conn->Execute($sql);
    
        $id = $conn->GetGeneratedID();
        
        $id_scat_to_id_scat[$sub->id] = $id;
    }
}

var_export($id_cat_to_id_cat);
var_export($id_scat_to_id_scat);
?>
