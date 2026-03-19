<?php
/////////////////////////////////////////////
//////written by serdar acir/////////////////
//////serdar.acir@sabanciuniv.edu////////////
/////////////////////////////////////////////
ini_set('memory_limit','-1');
include "HPC.config";

function connect_db_tr ($database)
{
global $connection, $host, $user, $password;
$connection=mysqli_connect($host,$user,$password,$database);
if (mysqli_connect_errno()) {
  echo "Failed to connect to MySQL: " . mysqli_connect_error();
  exit();
}
mysqli_query($connection, 'SET NAMES utf8');
mysqli_set_charset($connection, "utf8mb4");
return 1;
}
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
function disconnect_db_tr ()
{
global $connection;
if (!$close_now=mysqli_close($connection)) {
                $that_err = mysqli_errno($connection) . ": " . mysqli_error($connection) . "\n";
                echo $that_err;
}
return 1;
}
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
function get_generic ($tablename, $cluster_name, $key, $value, $return_par) {
global $connection;
$query_generic ="SELECT * from $tablename where cluster_name='$cluster_name' AND $key='$value'";
if (!$result_generic =mysqli_query($connection, $query_generic)){
        $that_err = mysqli_errno($connection) . ": " . mysqli_error($connection) . "\n";
        echo "$return_par ::: $query_generic ::: $that_err";
}
$nrows = mysqli_num_rows($result_generic);
if ($nrows < 1){
return ("yok");
}
else {
$row19=mysqli_fetch_array($result_generic ,MYSQLI_ASSOC);
$tra_type = "$return_par";
return ($row19[$tra_type]);
}
}
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
function get_generic2 ($tablename, $cluster_name, $key1, $value1, $key2, $value2, $key3, $value3, $return_par) {
global $connection;
if ($key1 != NULL) $add1 = " and $key1='$value1'";
if ($key2 != NULL) $add2 = " and $key2='$value2'";
if ($key3 != NULL) $add3 = " and $key3='$value3'";
$query_generic ="SELECT * from $tablename where cluster_name='$cluster_name' $add1 $add2 $add3";
//echo $query_generic."<br>";
if (!$result_generic =mysqli_query($connection, $query_generic)){
        $that_err = mysqli_errno($connection) . ": " . mysqli_error($connection) . "\n";
        echo "$return_par ::: $query_generic ::: $that_err";
}
$nrows = mysqli_num_rows($result_generic);
if ($nrows < 1){
return ("yok");
}
else {
$row19=mysqli_fetch_array($result_generic ,MYSQLI_ASSOC);
$tra_type = "$return_par";
return ($row19[$tra_type]);
}
}
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
function get_all_generic ($tablename, $cluster_name, $key, $value, $order_by, $query_ek3) {
global $connection;
if ($key != NULL) $query_ek1 = "AND $key='$value'";
if ($query_ek3 != '') $query_ek3 = "AND $query_ek3";
if ($order_by != NULL) $query_ek2 = "order by $order_by";
$query_generic ="SELECT * from $tablename where cluster_name='$cluster_name' $query_ek1 $query_ek3 $query_ek2";
//echo $query_generic;
if (!$result_generic =mysqli_query($connection, $query_generic)){
        $that_err = mysqli_errno($connection) . ": " . mysqli_error($connection) . "\n";
        echo "$return_par ::: $query_generic ::: $that_err";
}
$nrows = mysqli_num_rows($result_generic);
if ($nrows < 1){
return ("yok");
}
else {
return ($result_generic);
}
}
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
function get_that_generic ($tablename, $cluster_name, $key, $value, $return_par, $order_by) {
global $connection;
if ($order_by != NULL) $query_ek2 = "order by $order_by";
if ($key != NULL) $query_ek1 = "AND $key='$value'";
if ($key == "limit" and $value == "time") { //index sayfasına özel gelen veri adedini kısıtlamak için
// $today_start = date('Y-m-d').' 00:00:00';
// $query_ek1 = "AND report_time >='$today_start'";
$query_ek1 = "";
$query_ek2 = $query_ek2." LIMIT 1";
}
$query_generic ="SELECT * from $tablename WHERE cluster_name='$cluster_name' $query_ek1 $query_ek2";
//echo "$query_generic<br>";
if (!$result_generic =mysqli_query($connection, $query_generic)){
        $that_err = mysqli_errno($connection) . ": " . mysqli_error($connection) . "\n";
        echo "$return_par ::: $query_generic ::: $that_err";
}
$nrows = mysqli_num_rows($result_generic);
//echo "nrows: $nrows<br>";
if ($nrows < 1){
return ("yok");
}
else {
$row19=mysqli_fetch_array($result_generic ,MYSQLI_ASSOC);
$tra_type = "$return_par";
return ($row19[$tra_type]);
}
}
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
function delete_generic ($tablename, $cluster_name, $key1, $value1, $key2, $value2, $key3, $value3)
{
global $connection;
if ($cluster_name != NULL) $addcl = " WHERE cluster_name='$cluster_name'";
if ($key1 != NULL) $add1 = " and $key1='$value1'";
if ($key2 != NULL) $add2 = " and $key2='$value2'";
if ($key3 != NULL) $add3 = " and $key3='$value3'";
$query_delete="DELETE FROM $tablename $addcl $add1 $add2 $add3";
$result_delete=mysqli_query($connection,$query_delete);
return 1;
}
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
function insert_generic ($tablename, $cluster_name, $report_time, $node_name, $node_stat, $cpu_usage, $mem_available, $mem_total, $swap_used, $ram_usage, $disk_write_MBs, $nw_speed_Mbs, $gpu_usage, $top_processes) {
global $connection;
$insert_braker = "INSERT INTO $tablename (cluster_name, report_time, node_name, node_stat, cpu_usage, mem_available, mem_total, swap_used, ram_usage, disk_write_MBs, nw_speed_Mbs, gpu_usage, top_processes) VALUES ('$cluster_name', '$report_time', '$node_name', '$node_stat', '$cpu_usage', '$mem_available', '$mem_total', '$swap_used', '$ram_usage', '$disk_write_MBs', '$nw_speed_Mbs', '$gpu_usage', '$top_processes')";
if ( !$insert = mysqli_query($connection,$insert_braker ) ) {
        $that_err = mysqli_errno($connection) . ": " . mysqli_error($connection) . "\n";
        echo "$insert_braker ::: $that_err";
  }
return 1;
}
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
function insert_generic2 ($tablename, $cluster_name, $report_time, $return_param) {
global $connection;
$insert_braker2 = "INSERT INTO $tablename (cluster_name, report_time) VALUES ('$cluster_name', '$report_time')";
if ( !$insert2 = mysqli_query($connection,$insert_braker2 ) ) {
        $that_err2 = mysqli_errno($connection) . ": " . mysqli_error($connection) . "\n";
        echo "$insert_braker2 ::: $that_err2";
  }
$last_id = get_that_generic ($tablename, $cluster_name, NULL, NULL, $return_param, "$return_param DESC");
return $last_id;
}
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
function insert_hwtable ($cluster_name, $report_time, $node_name, $category, $veri, $return_param) {
global $connection;
$insert_braker2 = "INSERT INTO hwtable (cluster_name, report_time, node_name, category, veri) VALUES ('$cluster_name', '$report_time', '$node_name', '$category', '$veri')";
if ( !$insert2 = mysqli_query($connection,$insert_braker2 ) ) {
        $that_err2 = mysqli_errno($connection) . ": " . mysqli_error($connection) . "\n";
        echo "$insert_braker2 ::: $that_err2";
  }
$last_id = get_that_generic ("hwtable", $cluster_name, NULL, NULL, $return_param, "$return_param DESC");
return $last_id;
}
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
function update_generic ($tablename, $cluster_name, $key, $value, $key2, $value2)
{
global $connection;
$update_query = "UPDATE $tablename SET $key2='$value2' WHERE cluster_name='$cluster_name' AND $key='$value'";
if ( !$update= mysqli_query($connection,$update_query) ) {
        $that_err = mysqli_errno($connection) . ": " . mysqli_error($connection) . "\n";
        echo "$insert_braker ::: $that_err";
  }
return 1;
}
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
function update_generic2 ($tablename, $cluster_name, $key, $value, $key2, $value2, $key3, $value3)
{
global $connection;
if ($key2 != NULL) $add2 = " and $key2='$value2'";
if ($key3 != NULL) $add3 = " and $key3='$value3'";
$update_query = "UPDATE $tablename SET $key='$value' WHERE cluster_name='$cluster_name' $add2 $add3";
if ( !$update= mysqli_query($connection,$update_query) ) {
        $that_err = mysqli_errno($connection) . ": " . mysqli_error($connection) . "\n";
        echo "$insert_braker ::: $that_err";
  }
return 1;
}
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
function get_distinc_rows ($tablename, $cluster_name, $return_par) {
global $connection;
$query_generic ="SELECT distinct($return_par) from $tablename where cluster_name='$cluster_name' order by '$return_par'";
if (!$result_generic =mysqli_query($connection, $query_generic)){
        $that_err = mysqli_errno($connection) . ": " . mysqli_error($connection) . "\n";
        echo "$return_par ::: $query_generic ::: $that_err";
}
$nrows = mysqli_num_rows($result_generic);
if ($nrows < 1){
return ("yok");
}
else { //else start
$return_par_array="";
for ($i=1; $i<=$nrows; $i++) { //for start
 $row19=mysqli_fetch_array($result_generic ,MYSQLI_ASSOC);
 $return_par_array = $return_par_array.$row19[$return_par];
 if ($i < $nrows) $return_par_array = $return_par_array.", ";
 } //for end
} //else end
return $return_par_array;
}
function node_status_desc ($input) {
if ($input == "alloc") return "The node has been allocated to one or more jobs.";
else if ($input == "alloc+") return "The node is allocated to one or more active jobs plus one or more jobs are in the process of COMPLETING.";
else if ($input == "comp") return "All jobs associated with this node are in the process of COMPLETING. This node state will be removed when all of the job's processes have terminated and the Slurm epilog program (if any) has terminated.";
else if ($input == "down") return "The node is unavailable for use. Slurm can automatically place nodes in this state if some failure occurs. System administrators may also explicitly place nodes in this state. If a node resumes normal operation, Slurm can automatically return it to service.";
else if ($input == "drain") return "The node is unavailable for use per system administrator request.";
else if ($input == "drng") return "The node is currently executing a job, but will not be allocated additional jobs. The node state will be changed to state DRAINED when the last job on it completes. Nodes enter this state per system administrator request.";
else if ($input == "fail") return "The node is expected to fail soon and is unavailable for use per system administrator request.";
else if ($input == "failg") return "The node is currently executing a job, but is expected to fail soon and is unavailable for use per system administrator request.";
else if ($input == "futr") return "The node is currently not fully configured, but expected to be available at some point in the indefinite future for use.";
else if ($input == "inv") return "The node did not register correctly with the controller. This happens when a node registers with less resources than configured in the slurm.conf file. The node will clear from this state with a valid registration";
else if ($input == "maint") return "The node is currently in a reservation with a flag value of -maintenance-.";
else if ($input == "REBOOT_ISSUED") return "A reboot request has been sent to the agent configured to handle this request.";
else if ($input == "REBOOT_REQUESTED") return "A request to reboot this node has been made, but hasn't been handled yet.";
else if ($input == "npc") return "Network Performance Counters associated with this node are in use, rendering this node as not usable for any other jobs";
else if ($input == "plnd") return "The node is planned by the backfill scheduler for a higher priority job.";
else if ($input == "pow_dn") return "The node is pending power down.";
else if ($input == "POWERED_DOWN") return "The node is currently powered down and not capable of running any jobs.";
else if ($input == "POWERING_DOWN") return "The node is in the process of powering down and not capable of running any jobs.";
else if ($input == "pow_up") return "The node is in the process of being powered up.";
else if ($input == "resv") return "The node is in an advanced reservation and not generally available.";
else if ($input == "unk") return "The Slurm controller has just started and the node's state has not yet been determined.";
else return "The node is not allocated to any jobs and is available for use OR The node has some of its CPUs ALLOCATED while others are IDLE.";
}
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
?>
