<?php
/////////////////////////////////////////////
//////serdar.acir@sabanciuniv.edu////////////
/////////////////////////////////////////////
error_reporting(E_ERROR | E_PARSE);
connect_db_tr ($database);

$cluster_name = $_GET['cl']; if ($cluster_name == NULL) $cluster_name = $_POST['cl']; if ($cluster_name == NULL) $cluster_name = $argv[1];
$start_date = $_GET['start_date']; if ($start_date == NULL) $start_date = $_POST['start_date']; if ($start_date == NULL) $start_date = $argv[2]; $start_date = $start_date . " 00:00:00";
$end_date = $_GET['end_date']; if ($end_date == NULL) $end_date = $_POST['end_date']; if ($end_date == NULL) $end_date = $argv[3]; $end_date = $end_date . " 23:59:59";
//$cluster_name="TosunHPC"; $start_date = "2023-04-30 00:00:00"; $end_date = "2023-04-30 23:59:59";

$result_generic2 = get_all_generic ("monitoringtable", "$cluster_name", "", "", "report_time", "node_stat!='down' AND report_time >= '$start_date' AND report_time <= '$end_date'");
$nrows = mysqli_num_rows($result_generic2);
if ($nrows <= 1) { echo "veri yok!"; exit; }
for ($i=1; $i<=$nrows; $i++) { //forall start
 $row19=mysqli_fetch_array($result_generic2 ,MYSQLI_ASSOC);
 extract ($row19);
 if ($node_name == "login" OR $node_name == "monitor01" OR $node_name == "monitor02") continue;
 $top_processes = preg_replace("/[[:blank:]]+/"," ",$top_processes);
 $top_processes_arr = explode("\n", $top_processes);
 $top_processes_arr_line1 = explode(" ", $top_processes_arr[1]); 
 if ($top_processes_arr_line1[1] == "R") { $nodes[$node_name][$top_processes_arr_line1[0]] = $nodes[$node_name][$top_processes_arr_line1[0]] + $top_processes_arr_line1[2]; }
 $top_processes_arr_line2 = explode(" ", $top_processes_arr[2]);
 if ($top_processes_arr_line2[1] == "R") { $nodes[$node_name][$top_processes_arr_line2[0]] = $nodes[$node_name][$top_processes_arr_line2[0]] + $top_processes_arr_line2[2];}
} //forall end

ksort($nodes);
$keys = array_keys($nodes);
for ($i = 0; $i < count($nodes); $i++) {
    foreach( $nodes[$keys[$i]] as $key => $value) {
        arsort ($nodes[$keys[$i]]);
        $nodes[$keys[$i]][total] = $nodes[$keys[$i]][total] + $value;
    }
}

// print_r($nodes); exit;

for ($i = 0; $i < count($nodes); $i++) {
    //echo "Node ".$keys[$i].": ";
    if ($i % 2 == 0) $ek = "style='background-color: lightgrey'"; 
    echo "<tr $ek><th width=30%>Node ".$keys[$i]."</th><td align=left>";
    foreach( $nodes[$keys[$i]] as $key => $value) {
        $oran = round ((100 * $value) / $nodes[$keys[$i]][total]);
        if ($key == "total" OR $key == "root" OR $key == "snmp" OR $oran == 0) continue;
	//echo "%"."$oran ($key) ";
        echo "%$oran ($key) ";
    }
   echo "</td></tr>";
}

disconnect_db_tr ();
exit;

?>

