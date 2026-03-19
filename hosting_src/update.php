<?php
/////////////////////////////////////////////
//////written by serdar acir/////////////////
//////serdar.acir@sabanciuniv.edu////////////
/////////////////////////////////////////////
error_reporting(E_ERROR | E_PARSE);
require_once "mysql_ops.php";
connect_db_tr ($database);
extract(array_map("htmlspecialchars", $_POST), EXTR_OVERWRITE, "form_");
$current_time = date('Y-m-d H:i:s', time());

if ($cluster_name == NULL OR $node_name == NULL) {
 echo "Please press the BACK button to complete the missing information. (1:$cluster_name 2: $node_name)";
 disconnect_db_tr (); exit; 
}

foreach($_POST as $key => $value) {
 update_generic2 ("hwtable", $cluster_name, "veri", $value, "node_name", $node_name, "category", $key);
}
update_generic2 ("hwtable", $cluster_name, "report_time", $current_time, "node_name", $node_name, NULL, NULL);

disconnect_db_tr ();
if ($message_ek == NULL) $message_ek = "<br><font color=darkgreen>$cluster_name -> $node_name updated!</font><br>";
header("Location: admin.php?message=$message_ek&cluster_name=$cluster_name&node_name=$node_name");
exit();
?>

