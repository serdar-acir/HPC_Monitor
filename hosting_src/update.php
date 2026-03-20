<?php
declare(strict_types=1);

/////////////////////////////////////////////
//////written by serdar acir/////////////////
//////serdaracir5@gmail.com//////////////////
/////////////////////////////////////////////

error_reporting(E_ERROR | E_PARSE);
require_once "mysql_ops.php";
connect_db_tr($database);

$post_escaped = array_map("htmlspecialchars", $_POST);
extract($post_escaped, EXTR_OVERWRITE, "form_");

$current_time = date('Y-m-d H:i:s', time());

if (($cluster_name ?? null) == NULL OR ($node_name ?? null) == NULL) {
    echo "Please press the BACK button to complete the missing information. (1:$cluster_name 2: $node_name)";
    disconnect_db_tr();
    exit;
}

foreach ($_POST as $key => $value) {
    update_generic2("hwtable", $cluster_name, "veri", $value, "node_name", $node_name, "category", $key);
}
update_generic2("hwtable", $cluster_name, "report_time", $current_time, "node_name", $node_name, NULL, NULL);

disconnect_db_tr();

if (($message_ek ?? null) == NULL) $message_ek = "<br><font color=darkgreen>$cluster_name -> $node_name updated!</font><br>";

header("Location: admin.php?message=" . urlencode($message_ek) . "&cluster_name=" . urlencode($cluster_name) . "&node_name=" . urlencode($node_name));
exit();
?>
