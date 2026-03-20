<?php
declare(strict_types=1);

/////////////////////////////////////////////
//////written by serdar acir/////////////////
//////serdaracir5@gmail.com//////////////////
/////////////////////////////////////////////

error_reporting(E_ERROR | E_PARSE);

global $connection;
require "mysql_ops.php";
connect_db_tr($database);

/* Safe fetch (PHP8) */
$top_processes_raw = $_POST['top_processes'] ?? '';
$top_processes = mysqli_real_escape_string($connection, $top_processes_raw);

$cluster_name   = trim($_POST['cluster_name']   ?? '');
$report_time    = trim($_POST['report_time']    ?? '');
$node_name      = trim($_POST['node_name']      ?? '');
$node_stat      = trim($_POST['node_stat']      ?? '');
$cpu_usage      = trim($_POST['cpu_usage']      ?? '');
$mem_available  = trim($_POST['mem_available']  ?? '');
$mem_total      = trim($_POST['mem_total']      ?? '');
$swap_used      = trim($_POST['swap_used']      ?? '');
$disk_write_MBs = trim($_POST['disk_write_MBs'] ?? '');
$nw_speed_Mbs   = trim($_POST['nw_speed_Mbs']   ?? '');
$gpu_usage      = trim($_POST['gpu_usage']      ?? '');

$top_processes  = trim($top_processes);

/* Prevent division by zero (PHP8 strict safety) */
if ((float)$mem_total > 0) {
    $ram_usage = round(((($mem_total - $mem_available) / $mem_total) * 100));
} else {
    $ram_usage = 0;
}

/* Insert operations (unchanged logic) */
insert_generic("monitoringtable", $cluster_name, $report_time, $node_name, $node_stat, $cpu_usage, $mem_available, $mem_total, $swap_used, $ram_usage, $disk_write_MBs, $nw_speed_Mbs, $gpu_usage, $top_processes);

delete_old("temp_monitoringtable", $cluster_name);

insert_generic("temp_monitoringtable", $cluster_name, $report_time, $node_name, $node_stat, $cpu_usage, $mem_available, $mem_total, $swap_used, $ram_usage, $disk_write_MBs, $nw_speed_Mbs, $gpu_usage, $top_processes);

disconnect_db_tr();

echo "ok";
exit;

function delete_old($tablename, $cluster_name)
{
    global $connection;

    $query_latest_100 = "SELECT id FROM $tablename WHERE cluster_name='$cluster_name' ORDER BY report_time DESC LIMIT 100";
    $result_latest_100 = mysqli_query($connection, $query_latest_100);

    $ids_to_keep = [];

    if ($result_latest_100) {
        while ($row = mysqli_fetch_assoc($result_latest_100)) {
            $ids_to_keep[] = $row['id'];
        }
    }

    if (count($ids_to_keep) > 0) {
        $ids_string = implode(',', $ids_to_keep);
        $query_delete = "DELETE FROM $tablename WHERE cluster_name='$cluster_name' AND id NOT IN ($ids_string)";
        mysqli_query($connection, $query_delete);
    }

    return 1;
}
?>
