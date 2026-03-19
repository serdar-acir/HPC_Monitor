<!--/////////////////////////////////////////////-->
<!--//////written by:////////////////////////////-->
<!--//////serdar.acir@sabanciuniv.edu////////////-->
<!--/////////////////////////////////////////////-->
<!DOCTYPE html>
<html>
<head>
<meta name="viewport" content="width=device-width, initial-scale=1">
<?php $random = mt_rand(1,9999999); ?>
<link rel="stylesheet" href="styles.css?random=<?php echo $random; ?>">
</head>
<body>

<?php
error_reporting(E_ERROR | E_PARSE);
require "mysql_ops.php";
connect_db_tr ($database);

$cluster_name = $_GET['cl']; if ($cluster_name == NULL) $cluster_name = $_POST['cl'];
$node_name = $_GET['cn']; if ($node_name == NULL) $node_name = $_POST['cn'];
$ref_time_w = $_POST['ref_time_w']; if ($ref_time_w == NULL) $ref_time_w = $_GET['ref_time_w'];
$iparam = $_POST['iparam']; if ($iparam == NULL) $iparam = $_GET['iparam'];
if (isset($_GET['a'])) { $mylink="a"; } else $mylink="";

if ($cluster_name == '') { $cluster_name="SakuraHPC"; } 
if ($node_name == '') { $node_name="cn01"; } 
if ($ref_time_w != NULL) { $ref_time_word = $ref_time_w."_selected";  $$ref_time_word = "selected"; } else { $ref_time_w = "minutes60"; $minutes60_selected = "selected"; }
$ref_time_num = str_replace("minutes", '', $ref_time_w); $ref_time = date("Y-m-d H:i:s", strtotime("$ref_time_num minutes ago")); 
if ($iparam != NULL) { $iparam_word = $iparam."_selected";  $$iparam_word = "selected"; } else { $iparam = "all"; $all_selected = "selected"; }
$adm_link = "<font size=-3><i><a href=\"admin.php?$mylink\">admin</a></i></font>";
$that_hw = get_all_hardware ($cluster_name, $node_name);

$cluster_name_i = strtoupper (str_replace("HPC", '', $cluster_name));
echo "<table><tr><td><h3><a href=\"?$mylink&cluster_name=$cluster_name\">$cluster_name_i :::  ".strtoupper($node_name)." - Benchmark Details</a> - $adm_link - </h3></td><td>
<form method=\"post\">
    <select name='ref_time_w' onchange='if(this.value != 0) { this.form.submit(); }'>
         <option $nothing_selected disabled>Time Reference</option>
         <option value='minutes5' $minutes5_selected>5 minutes</option>
         <option value='minutes15' $minutes15_selected>15 minutes</option>
         <option value='minutes60' $minutes60_selected>1 hour</option>
         <option value='minutes180' $minutes180_selected>3 hours</option>
         <option value='minutes360' $minutes360_selected>6 hours</option>
         <option value='minutes720' $minutes720_selected>12 hours</option>
         <option value='minutes1440' $minutes1440_selected>1 day</option>
         <option value='minutes4320' $minutes4320_selected>3 days</option>
         <option value='minutes10080' $minutes10080_selected>1 week</option>
         <option value='minutes20160' $minutes20160_selected>2 weeks</option>
         <option value='minutes43200' $minutes43200_selected>1 month</option>
         <option value='minutes131040' $minutes131040_selected>3 months</option>
         <option value='minutes263520' $minutes263520_selected>6 months</option>
         <option value='minutes527040' $minutes527040_selected>1 year</option>
         <option value='minutes1054080' $minutes1054080_selected>2 years</option>
         <option value='minutes1581120' $minutes1581120_selected>3 years</option>
    </select>
<input type=\"hidden\" id=\"iparam\" name=\"iparam\" value=\"$iparam\">
</form> </td>
<td><form method=\"post\">
    <select name='iparam' onchange='if(this.value != 0) { this.form.submit(); }'>
         <option $iparam_nothing_selected disabled>Parameter</option>
         <option value='all' $all_selected>All Graphs</option>
         <option value='cpu_usage' $cpu_usage_selected>CPU Utilization</option>
         <option value='ram_usage' $ram_usage_selected>RAM Utilization</option>
         <option value='disk_write_MBs' $disk_write_MBs_selected>Disk Write Speed</option>
         <option value='nw_speed_Mbs' $nw_speed_Mbs_selected>Network Speed</option>
         <option value='gpu_usage' $gpu_usage_selected>GPU Usage</option>
    </select>
<input type=\"hidden\" id=\"ref_time_w\" name=\"ref_time_w\" value=\"$ref_time_w\">
</form> </td></tr>
<tr><td colspan=3><button class=\"collapsible\">".strtoupper($cluster_name_i)." - ".strtoupper($node_name)." Hardware Information</button><div class=\"content\">$that_hw
</div></td></tr>
</table> ";

 echo "<table>";
 if ($iparam == "all" OR $iparam == "cpu_usage") echo "<tr><td width=10%>CPU Utilization</td><td colspan=3><img src=\"graph.php?cl=$cluster_name&cn=$node_name&ref_time=$ref_time&param=cpu_usage\" width=\"400\" height=\"200\" class=\"img\" /></td><tr>";
 if ($iparam == "all" OR $iparam == "ram_usage") echo "<tr><td width=10%>RAM Utilization</td><td><img src=\"graph.php?cl=$cluster_name&cn=$node_name&ref_time=$ref_time&param=ram_usage\" width=\"400\" height=\"200\" class=\"img\" /></td>
 <td><img src=\"graph.php?cl=$cluster_name&cn=$node_name&ref_time=$ref_time&param=mem_available\" width=\"400\" height=\"200\" class=\"img\" /></td>
 <td><img src=\"graph.php?cl=$cluster_name&cn=$node_name&ref_time=$ref_time&param=swap_used\" width=\"400\" height=\"200\" class=\"img\" /></td><tr>";
 if ($iparam == "all" OR $iparam == "disk_write_MBs") echo "<tr><td width=10%>Disk Write Speed</td><td colspan=3><img src=\"graph.php?cl=$cluster_name&cn=$node_name&ref_time=$ref_time&param=disk_write_MBs\" width=\"400\" height=\"200\" class=\"img\" /></td><tr>";
 if ($iparam == "all" OR $iparam == "nw_speed_Mbs") echo "<tr><td width=10%>Network Speed</td><td colspan=3><img src=\"graph.php?cl=$cluster_name&cn=$node_name&ref_time=$ref_time&param=nw_speed_Mbs\" width=\"400\" height=\"200\" class=\"img\" /></td><tr>";
 echo "</table>";

if ($iparam != "all") {
 $that_data = get_all_data ($cluster_name, $node_name, $ref_time, $iparam);
 echo "<table><tr><td><button class=\"collapsible\">".strtoupper($cluster_name_i)." - ".strtoupper($node_name)." ".strtoupper($iparam)." DATA</button><div class=\"content\">$that_data
</div></td></tr>
</table> ";
}

disconnect_db_tr ();


function get_all_hardware ($cluster_name, $node_name) {
global $connection;
$result_generic2 = get_all_generic ("hwtable", "$cluster_name", "node_name", $node_name, "hw_id", '');
$nrows = mysqli_num_rows($result_generic2);
if ($nrows <= 1) return "teknik hata!";
for ($i=1; $i<=$nrows; $i++) { //forall start
 $row19=mysqli_fetch_array($result_generic2 ,MYSQLI_ASSOC);
 extract ($row19);
 $message = $message."<br><b>$category</b>: <font color=darkgreen>".nl2br($veri)."</font>";
} //forall end

$message = "(last updated: $report_time)<br>".$message;
return $message;
}

function get_all_data ($cluster_name, $node_name, $ref_time, $iparam) {
global $connection;
$result_generic2 = get_all_generic ("monitoringtable", "$cluster_name", "node_name", $node_name, "report_time DESC", '');
$nrows = mysqli_num_rows($result_generic2);
if ($nrows <= 1) return "veri yok!";
for ($i=1; $i<=$nrows; $i++) { //forall start
 $row19=mysqli_fetch_array($result_generic2 ,MYSQLI_ASSOC);
 extract ($row19);
 if ($report_time >= $ref_time) { //iftime start
  if ($node_stat != "idle") $message = $message."<b>$report_time</b>: <font color=darkred>==> <b>".strtoupper($node_stat)."</b></font><br>";
  else { //not_idle start
   ${$iparam} = str_replace("||", " ---- ", ${$iparam});
   if ($iparam == "cpu_usage") $metric_ek=" %<br>".nl2br($top_processes);
   else if ($iparam == "ram_usage") $metric_ek=" % -------   ";
   else if ($iparam == "disk_write_MBs") $metric_ek=" MB/s";
   else if ($iparam == "nw_speed_Mbs") $metric_ek=" Mb/s";
   $message = $message."<b>$report_time</b>: <font color=darkgreen>==> ${$iparam}</font> $metric_ek";
   if ($iparam == "ram_usage") $message = $message."  <i><b>mem_available / mem_total: <font color=darkgreen>".sprintf ("%.2f", $mem_available/1000000)." / ".sprintf ("%.2f", $mem_total/1000000)." GB </font><b>swap_used:</b> <font color=darkgreen>".sprintf ("%.2f", $swap_used/1000000)." GB</font></i><br>";
   else $message = $message."<br>";
  } //not_idle end
 } //iftime end
} //forall end
return $message;
}

?>

<script>
var coll = document.getElementsByClassName("collapsible");
var i;

for (i = 0; i < coll.length; i++) {
  coll[i].addEventListener("click", function() {
    this.classList.toggle("active");
    var content = this.nextElementSibling;
    if (content.style.maxHeight){
      content.style.maxHeight = null;
    } else {
      content.style.maxHeight = content.scrollHeight + "px";
    }
  });
}
</script>


</body>
</html>

