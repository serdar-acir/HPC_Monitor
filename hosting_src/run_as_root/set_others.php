<?php
/////////////////////////////////////////////
//////written by serdar acir/////////////////
//////serdar.acir@sabanciuniv.edu////////////
/////////////////////////////////////////////
error_reporting(E_ERROR | E_PARSE);
$filename = "/var/www/html/hpc.sabanciuniv.edu/run_as_root/do_all";
if (! file_exists($filename) ) {
    echo "The file $filename does not exist\n"; exit;
}

require "../mysql_ops.php";
connect_db_tr ($database);
$ilk_silis = 1;
$report_time = date('Y-m-d H:i:s', time());

$allfiles = scandir(getcwd());
foreach ($allfiles as $file) {
 if (strstr($file, '.txt')) {
  $file_name = substr($file, 0, -4);
  $who_data = explode("_", $file_name);
  $cluster_name = $who_data[0];
  $node_name = $who_data[1];
  $category = $who_data[3];
  if ($ilk_silis == 1) { delete_generic ("hwtable", NULL, NULL, NULL, NULL, NULL, NULL, NULL); $ilk_silis = 0; }
  if (get_generic2 ("hwtable", $cluster_name, "node_name", $node_name, "category", "hw_ref_diskmaxMBs", NULL, NULL, $category) == "yok") { insert_hwtable ($cluster_name, $report_time, $node_name, "hw_ref_diskmaxMBs", 0, "hw_id"); insert_hwtable ($cluster_name, $report_time, $node_name, "hw_ref_homemaxMbs", 0, "hw_id"); }
  $pointer = ""; $filesize = 0; $filetext = "";
  $pointer = fopen( $file, "r" );
  if( $pointer == false ) { echo ( "Error in opening file:$file\n" ); } else echo ".\n";
  $filesize = filesize( $file );
  $filetext = fread( $pointer, $filesize );
  fclose( $pointer );
  unlink ( $file );
  $filetext = check_format ($cluster_name, $node_name, $category, $filetext);
  insert_hwtable ($cluster_name, $report_time, $node_name, $category, $filetext, "hw_id");
 }
}
disconnect_db_tr ();
unlink ( $filename );

function check_format ($cluster_name, $node_name, $category, $filetext) {
if ($category == "memory" AND stristr ($filetext, "Device-32") ) { //if1 start
 $filetext_tmp = explode("Device-1", $filetext);
 return $filetext_tmp[0]."Virtual RAM";
} //if1 end

return $filetext;
}

?>
