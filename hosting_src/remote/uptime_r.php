<?php
/////////////////////////////////////////////
//////serdar.acir@sabanciuniv.edu////////////
/////////////////////////////////////////////
error_reporting(E_ERROR | E_PARSE);
require_once "../mysql_ops.php";
connect_db_tr ($database);

$cluster_name = $_GET['cl']; if ($cluster_name == NULL) $cluster_name = $_POST['cl']; if ($cluster_name == NULL) $cluster_name = $argv[1]; 
$start_date = $_GET['start_date']; if ($start_date == NULL) $start_date = $_POST['start_date']; if ($start_date == NULL) $start_date = $argv[2]; $start_date = $start_date . " 00:00:00";
$end_date = $_GET['end_date']; if ($end_date == NULL) $end_date = $_POST['end_date']; if ($end_date == NULL) $end_date = $argv[3]; $end_date = $end_date . " 23:59:59";

$result_generic2 = get_all_generic ("monitoringtable", "$cluster_name", "", "", "report_time", "node_stat!='down' AND report_time >= '$start_date' AND report_time <= '$end_date'");
$nrows = mysqli_num_rows($result_generic2);
if ($nrows <= 1) { echo "veri yok!"; exit; }
for ($i=1; $i<=$nrows; $i++) { //forall start
 $row19=mysqli_fetch_array($result_generic2 ,MYSQLI_ASSOC);
 extract ($row19);
 $itsname = "$node_name";
 $nodes[$itsname]++;
} //forall end

$maximum = max($nodes);
ksort($nodes);
//print_r($nodes);
foreach ($nodes as $node_name=>$value) {
 if ($node_name == "login" OR $node_name == "monitor01" OR $node_name == "monitor02") continue;
 $oran = round ((100 * $value) / $maximum, 2);
 $number++;
 if ($number % 2 == 0) $ek = "style='background-color: lightgrey'";
 echo "<tr $ek><th width=30%>Node $node_name</th><td align=center>%$oran</td></tr>";
// echo "Node $node_name : %$oran\n";
}
disconnect_db_tr ();

?>

