<?php
/////////////////////////////////////////////
//////written by serdar acir/////////////////
//////serdar.acir@sabanciuniv.edu////////////
/////////////////////////////////////////////
error_reporting(E_ERROR | E_PARSE);
require "mysql_ops.php";
connect_db_tr ($database);
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
foreach ($clusters as $cluster_name) {

    // Query for getting max disk and network speed for specific nodes
    $query_generic = "SELECT node_name, MAX(CAST(disk_write_MBs AS DECIMAL)) AS disk_max, MAX(CAST(nw_speed_Mbs AS DECIMAL)) AS nw_max 
                      FROM monitoringtable 
                      WHERE cluster_name='$cluster_name' 
                      LIMIT 1";

    if (!$result_generic = mysqli_query($connection, $query_generic)) {
        $that_err = mysqli_errno($connection) . ": " . mysqli_error($connection) . "\n";
        echo "$return_par ::: $query_generic ::: $that_err 817277812\n";
    }

    $nrows = mysqli_num_rows($result_generic);
    if ($nrows < 1) {
        echo "Error: No rows found for $cluster_name\n";
    } else {
        $row19 = mysqli_fetch_array($result_generic, MYSQLI_ASSOC);
        echo "$cluster_name : hw_ref_diskmaxMBs: " . $row19['disk_max'] . " hw_ref_homemaxMbs: " . $row19['nw_max'] . "\n";
        update_generic2("hwtable", $cluster_name, "veri", $row19['disk_max'], "node_name", $row19['node_name'], "category", "hw_ref_diskmaxMBs");
        update_generic2("hwtable", $cluster_name, "veri", $row19['nw_max'], "node_name", $row19['node_name'], "category", "hw_ref_homemaxMbs");
    }

    // Query for getting max disk and network speed for all nodes except specific ones (without node_name filtering)
    $query_generic = "SELECT MAX(CAST(disk_write_MBs AS DECIMAL)) AS disk_max, MAX(CAST(nw_speed_Mbs AS DECIMAL)) AS nw_max 
                      FROM monitoringtable 
                      WHERE cluster_name='$cluster_name' 
                      LIMIT 1";

    if (!$result_generic = mysqli_query($connection, $query_generic)) {
        $that_err = mysqli_errno($connection) . ": " . mysqli_error($connection) . "\n";
        echo "$return_par ::: $query_generic ::: $that_err 817277813\n";
    }

    $nrows = mysqli_num_rows($result_generic);
    if ($nrows < 1) {
        echo "Error: No rows found for $cluster_name\n";
    } else {
        $row19 = mysqli_fetch_array($result_generic, MYSQLI_ASSOC);
        echo "$cluster_name compute nodes: hw_ref_diskmaxMBs: " . $row19['disk_max'] . " hw_ref_homemaxMbs: " . $row19['nw_max'] . "\n";
        updatenot_generic2("hwtable", $cluster_name, "veri", $row19['disk_max'], "node_name", $row19['node_name'], "category", "hw_ref_diskmaxMBs");
        updatenot_generic2("hwtable", $cluster_name, "veri", $row19['nw_max'], "node_name", $row19['node_name'], "category", "hw_ref_homemaxMbs");
    }
}
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
function updatenot_generic2 ($tablename, $cluster_name, $key, $value, $key2, $value2, $key3, $value3)
{
global $connection;
$update_query = "UPDATE $tablename SET $key='$value' WHERE cluster_name='$cluster_name' AND $key2!='$value2' AND $key3='$value3'";
//echo "$update_query<br>";
if ( !$update= mysqli_query($connection,$update_query) ) {
        $that_err = mysqli_errno($connection) . ": " . mysqli_error($connection) . "\n";
        echo "$update_query ::: $that_err";
  }
return 1;
}
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
function updatelike_generic ($tablename, $cluster_name, $key, $value, $key1, $value1, $key2, $value2, $key3, $value3)
{
global $connection;
$update_query = "UPDATE $tablename SET $key='$value' WHERE cluster_name='$cluster_name' AND $key1!='$value1' AND $key2 like '$value2%' and $key3='$value3'";
if ( !$update= mysqli_query($connection,$update_query) ) {
        $that_err = mysqli_errno($connection) . ": " . mysqli_error($connection) . "\n";
        echo "$update_query ::: $that_err";
  }
return 1;
}
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
function updatenotlike_generic ($tablename, $cluster_name, $key, $value, $key1, $value1, $key2, $value2, $key3, $value3, $key4, $value4)
{
global $connection;
$update_query = "UPDATE $tablename SET $key='$value' WHERE cluster_name='$cluster_name' AND $key1!='$value1' AND $key2!='$value2' AND $key3!='$value3' and $key4='$value4'";
if ( !$update= mysqli_query($connection,$update_query) ) {
        $that_err = mysqli_errno($connection) . ": " . mysqli_error($connection) . "\n";
        echo "$update_query ::: $that_err";
  }
return 1;
}
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////



disconnect_db_tr ();
?>
