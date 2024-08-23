<?php
/////////////////////////////////////////////
//////written by serdar acir/////////////////
//////serdar.acir@sabanciuniv.edu////////////
/////////////////////////////////////////////
error_reporting(E_ERROR | E_PARSE);
date_default_timezone_set('Europe/Istanbul');
require_once "mysql_ops2.php";
checkrun();
if ((int)shell_exec("ssh root@$home_ip 'pgrep iperf | wc -l'") < 3) {
    shell_exec("ssh root@$home_ip 'nohup iperf3 -s -D > /dev/null 2>&1 &'");
}
putenv("HOME_IP=$home_ip"); //to be used by sap_nw_home.sh
$max_nodes=count($node_array);
$node_status = check_node_status();
$vowels = array(",");
$mydir = getcwd();
////////////////HEADNODE
$i=0;
$node_stat[$i] = "idle";
$cpu_usage[$i] = shell_exec ("sh $mydir/../sap_cpu.sh 2>&1"); $cpu_usage[$i] = str_replace(array("\n", "\r"), '', $cpu_usage[$i]);
echo $node_array[$i]." CPU Usage: %".$cpu_usage[$i]."\n";
$ram_usage[$i] = shell_exec ("sh $mydir/../sap_ram.sh 2>&1"); $ram_usage[$i] = str_replace(array("\n", "\r"), '', $ram_usage[$i]);
$ram_usage_pieces2[$i] = explode("DELIMITORX ", $ram_usage[$i]); $ram_usage[$i] = $ram_usage_pieces2[$i][1]; $ram_usage_pieces[$i] = explode(" ", $ram_usage[$i]);
$ram_usage_ram_available[$i] = $ram_usage_pieces[$i][0]; $ram_usage_ram_total[$i] = $ram_usage_pieces[$i][1]; $ram_usage_swap_used[$i] = (int)$ram_usage_pieces[$i][2];
echo $node_array[$i]." RAM Available/Total/Swapused: ".$ram_usage_ram_available[$i]."/".$ram_usage_ram_total[$i]."/".$ram_usage_swap_used[$i]."\n";
$write_speed[$i] = shell_exec ("sh $mydir/../sap_disk.sh $mydir/../ 2>&1"); $write_speed[$i] = str_replace(array("\n", "\r"), '', $write_speed[$i]);
$write_speed_pieces1[$i] = explode("DELIMITORX ", $write_speed[$i]); $write_speed[$i] = $write_speed_pieces1[$i][1]; 
$write_speed_pieces2[$i] = explode(" ", $write_speed[$i]); $write_mbs[$i] = $write_speed_pieces2[$i][9]; $write_term[$i] = $write_speed_pieces2[$i][10];
if ($write_term[$i] == "GB/s") $write_mbs[$i] = $write_mbs[$i] * 1000;
echo $node_array[$i]." Disk Write Speed (MB/s): ".$write_mbs[$i]."\n";
$nw_speed[$i] = shell_exec ("sh $mydir/../sap_nw_home.sh $home_ip 2>&1"); $nw_speed[$i] = str_replace(array("\n", "\r"), '', $nw_speed[$i]);
if (stristr ($nw_speed[$i], "refused")) { $nw_mbs[$i] = "NA"; $msg_ek_nw[$i]=""; } 
else { //else start
 $nw_speed_pieces1[$i] = explode("DELIMITORX ", $nw_speed[$i]); $nw_speed[$i] = $nw_speed_pieces1[$i][1];
 $nw_speed[$i] = preg_replace('/\s+/', ' ',$nw_speed[$i]);
 $nw_speed_pieces2[$i] = explode(" ", $nw_speed[$i]); $nw_mbs[$i] = $nw_speed_pieces2[$i][6]; $nw_term[$i] = $nw_speed_pieces2[$i][7];
 if ($nw_term[$i] == "Gbits/sec") $nw_mbs[$i] = $nw_mbs[$i] * 1000;
} //else end
echo $node_array[$i]." Network Speed Mbits/sec: ".$nw_mbs[$i]."\n";
$gpu_word[$i]="NA";
$top_processes[$i] = shell_exec ("sh $mydir/../sap_process.sh 2>&1");
$top_processes_pieces1[$i] = explode("DELIMITORX ", $top_processes[$i]); $top_processes[$i] = $top_processes_pieces1[$i][1];
echo $node_array[$i]." Top Processes:".$top_processes[$i]."\n";
echo "=================\n";
////////////////CPU USAGE
for ($i=1; $i<$max_nodes; $i++) { //for1 start
 if ($node_status[$node_array[$i]] == "idle" OR $node_status[$node_array[$i]] == "mix" OR $node_status[$node_array[$i]] == "alloc") { //bigif start
  $node_stat[$i] = "idle";
  $cpu_usage[$i] = shell_exec ("ssh root@".$node_array[$i]." 'bash -s < $mydir/../sap_cpu.sh' 2>&1");
  $cpu_usage[$i] = str_replace(array("\n", "\r"), '', $cpu_usage[$i]);
  echo $node_array[$i]." CPU Usage: %".$cpu_usage[$i]."\n";
  ////////////////RAM USAGE
  $ram_usage[$i] = shell_exec ("ssh root@".$node_array[$i]." 'bash -s < $mydir/../sap_ram.sh' 2>&1");
  $ram_usage[$i] = str_replace(array("\n", "\r"), '', $ram_usage[$i]);
  $ram_usage_pieces2[$i] = explode("DELIMITORX ", $ram_usage[$i]); $ram_usage[$i] = $ram_usage_pieces2[$i][1];
  $ram_usage_pieces[$i] = explode(" ", $ram_usage[$i]);
  $ram_usage_ram_available[$i] = $ram_usage_pieces[$i][0];
  $ram_usage_ram_total[$i] = $ram_usage_pieces[$i][1];
  $ram_usage_swap_used[$i] = (int)$ram_usage_pieces[$i][2];
  echo $node_array[$i]." RAM Available/Total/Swapused: ".$ram_usage_ram_available[$i]."/".$ram_usage_ram_total[$i]."/".$ram_usage_swap_used[$i]."\n";
  ////////////////HOME DIRECTORY WRITE SPEED
//  $write_speed[$i] = shell_exec ("ssh root@".$node_array[$i]." 'bash -s < $mydir/../sap_disk.sh $mydir/../' 2>&1");
  $write_speed[$i] = str_replace(array("\n", "\r"), '', $write_speed[$i]);
  $write_speed_pieces1[$i] = explode("DELIMITORX ", $write_speed[$i]); $write_speed[$i] = $write_speed_pieces1[$i][1];
  $write_speed_pieces2[$i] = explode(" ", $write_speed[$i]); $write_mbs[$i] = $write_speed_pieces2[$i][9]; $write_term[$i] = $write_speed_pieces2[$i][10];
  if ($write_term[$i] == "GB/s") $write_mbs[$i] = $write_mbs[$i] * 1000; 
  echo $node_array[$i]." Disk Write Speed (MB/s): ".$write_mbs[$i]."\n";
  ////////////////HOME DIRECTORY NETWORK COMMUNICATION SPEED
  if ($cluster_name=="TosunHPC" AND ($node_array[$i] == "cn14" OR $node_array[$i] == "cn15" OR $node_array[$i] == "login")) $home_ip_this = $home_ip2; else $home_ip_this = $home_ip;
  $nw_speed[$i] = shell_exec ("ssh root@".$node_array[$i]." 'bash -s < $mydir/../sap_nw_home.sh $home_ip_this' 2>&1");
  $nw_speed[$i] = str_replace(array("\n", "\r"), '', $nw_speed[$i]);
  if (stristr ($nw_speed[$i], "refused")) { $nw_mbs[$i] = "NA"; $msg_ek_nw[$i]=""; }
  else { //else start
   $nw_speed_pieces1[$i] = explode("DELIMITORX ", $nw_speed[$i]); $nw_speed[$i] = $nw_speed_pieces1[$i][1];
   $nw_speed[$i] = preg_replace('/\s+/', ' ',$nw_speed[$i]);
   $nw_speed_pieces2[$i] = explode(" ", $nw_speed[$i]); $nw_mbs[$i] = $nw_speed_pieces2[$i][6]; $nw_term[$i] = $nw_speed_pieces2[$i][7];
   if ($nw_term[$i] == "Gbits/sec") $nw_mbs[$i] = $nw_mbs[$i] * 1000; 
  } //else end
  echo $node_array[$i]." Network Speed Mbits/sec: ".$nw_mbs[$i]."\n";
 if ($nw_mbs[$i] == NULL) $nw_mbs[$i]=$nw_speed[$i];
  ////////////////GPU USAGE
  $gpu_usage[$i] = shell_exec ("ssh root@".$node_array[$i]." 'bash -s < $mydir/../sap_gpu.sh' 2>&1");
  $gpu_usage[$i] = str_replace(array("\n", "\r"), '', $gpu_usage[$i]);
  $gpu_usage_pieces2[$i] = explode("[%]", $gpu_usage[$i]); $gpu_usage[$i] = $gpu_usage_pieces2[$i][1];
  $gpu_usage_pieces3[$i] = explode("%", $gpu_usage[$i]);
  $gpu_word[$i]="";
  foreach ($gpu_usage_pieces3[$i] as $key => $value) { //foreach start
   if (stristr($value, "NVIDIA") OR stristr($value, "Tesla") ) { //if2 start
    $gpu_line[$i] = explode(",", $value);
    $gpu_name[$i] = trim ( str_replace(" ", '_', $gpu_line[$i][0]));
    $gpu_utilization[$i] = trim( str_replace("%", '', $gpu_line[$i][1]));
    $gpu_word[$i]=$gpu_word[$i]."||".$gpu_name[$i].":".$gpu_utilization[$i];
   } //if2 end
  } //foreach end
  $gpu_word[$i]=substr($gpu_word[$i],2);
  if ( stristr($gpu_word[$i], "NVIDIA") === FALSE AND stristr($gpu_word[$i], "Tesla") === FALSE ) $gpu_word[$i]="NA";
  echo $node_array[$i]." GPUs/Utilization: ".$gpu_word[$i]."\n";
  ////////////////TOP PROCESSES
  $top_processes[$i] = shell_exec ("ssh root@".$node_array[$i]." 'bash -s < $mydir/../sap_process.sh' 2>&1");
  $top_processes_pieces1[$i] = explode("DELIMITORX ", $top_processes[$i]); $top_processes[$i] = $top_processes_pieces1[$i][1];
  echo $node_array[$i]." Top Processes:".$top_processes[$i]."\n";
  } //bigif end
  else {
   $node_stat[$i] = $node_status[$node_array[$i]];
  }
  echo "\n=================\n";
 } //for1 end

$report_time = date('Y-m-d H:i:s', time());
for ($i=0; $i<$max_nodes; $i++) { //for2 start
$write_mbs[$i] = "NA";
 $data = [
    'cluster_name' => "$cluster_name",
    'report_time' => "$report_time",
    'node_name' => "$node_array[$i]",
    'node_stat' => "$node_stat[$i]",
    'cpu_usage' => "$cpu_usage[$i]",
    'mem_available' => "$ram_usage_ram_available[$i]",
    'mem_total' => "$ram_usage_ram_total[$i]",
    'swap_used' => "$ram_usage_swap_used[$i]",
    'disk_write_MBs' => "$write_mbs[$i]",
    'nw_speed_Mbs' => "$nw_mbs[$i]",
    'gpu_usage' => "$gpu_word[$i]",
    'top_processes' => "$top_processes[$i]"
 ];
//print_r($data);
 $response = record("record.php", $data);
 if ($response != "ok") echo "$cluster_name : ".$node_array[$i]." : veri yazılamadı: $response\n";
} //for2 end


?>

