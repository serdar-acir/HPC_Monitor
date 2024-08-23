<?php
/////////////////////////////////////////////
//////written by serdar acir/////////////////
//////serdar.acir@sabanciuniv.edu////////////
/////////////////////////////////////////////

$cluster_name=get_cluster_name();
require "../".$cluster_name.".config";
//echo "cluster_name: $cluster_name\n"; exit;
date_default_timezone_set('Europe/Istanbul');
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
function record($endpoint, $data = []){
    global $recording_host;
    $curl = curl_init();
    curl_setopt_array($curl, array(
      CURLOPT_URL => $recording_host . $endpoint,
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_MAXREDIRS => 10,
      CURLOPT_TIMEOUT => 0,
      CURLOPT_FOLLOWLOCATION => true,
      CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
      CURLOPT_CUSTOMREQUEST => 'POST',
      CURLOPT_POSTFIELDS => $data,
//      CURLOPT_VERBOSE => 1,
    ));
    $response = curl_exec($curl);
var_dump ($response);
    curl_close($curl);
    return ($response);
}
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
function get_cluster_name (){
$mydir = getcwd();
//$allfiles = scandir('./');
$allfiles = scandir("$mydir/../");
foreach ($allfiles as $file) {
 if (strstr($file, '.config')) {
  return substr($file, 0, -7);
 }
}
echo ".config file not found. exiting.\n";
exit;
}
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
function checkrun() {

// The file to store our process file
define('PROCESS_FILE', 'process2.pid');
// Check if I'm already running and kill myself off if I am
$pid_running = false;
if (file_exists(PROCESS_FILE)) {
    $data = file(PROCESS_FILE);
    foreach ($data as $pid) {
        $pid = (int)$pid;
        if ($pid > 0 && file_exists('/proc/' . $pid)) {
            $pid_running = $pid;
            break;
        }
    }
}
if ($pid_running && $pid_running != getmypid()) {
    if (file_exists(PROCESS_FILE)) {
        file_put_contents(PROCESS_FILE, $pid);
    }
    echo "Already running as pid: $pid\n";
    exit;
} else {
    // Make sure file has just me in it
    file_put_contents(PROCESS_FILE, getmypid());
    echo "Written pid: ".getmypid()."\n";
}
}
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
function check_node_status (){
$all_down_list = shell_exec ("sinfo -h -o \"%o %6t\" 2>&1");
$all_down_list2 = shell_exec ("sinfo -h -o \"%o %6t\" 2>&1");
if ($all_down_list != $all_down_list2) { sleep(2); $all_down_list = shell_exec ("sinfo -h -o \"%o %6t\" 2>&1"); }
$nodes = explode("\n", trim($all_down_list));
foreach ($nodes as $node) { //foreach start
 $pieces = explode(" ", $node);
 $pieces[1] = str_replace("+", '', $pieces[1]);
 $pieces[1] = str_replace("-", '', $pieces[1]);
 $pieces[1] = str_replace("*", '', $pieces[1]);
 $node_status[$pieces[0]] = $pieces[1];
} //foreach end
return $node_status;
}
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
function clean_db($cluster_name,$mysql_host,$mysql_user,$mysql_pwd) {
global $connection;
$connection=mysqli_connect($mysql_host,$mysql_user,$mysql_pwd,"slurm_acct_db");
if (mysqli_connect_errno()) {
  echo "Failed to connect to MySQL: " . mysqli_connect_error();
  exit();
}
mysqli_query($connection, 'SET NAMES utf8');
mysqli_set_charset($connection, "utf8");
$query_delete1="DELETE FROM ".$cluster_name."_job_table WHERE job_name like 'sap_%'";
$result_delete1=mysqli_query($connection,$query_delete1);
$query_delete2="DELETE FROM ".$cluster_name."_step_table WHERE step_name like 'sap_%'";
$result_delete2=mysqli_query($connection,$query_delete2);

if (!$close_now=mysqli_close($connection)) {
                $that_err = mysqli_errno($connection) . ": " . mysqli_error($connection) . "\n";
                echo $that_err;
}
return 1;
}
?>
