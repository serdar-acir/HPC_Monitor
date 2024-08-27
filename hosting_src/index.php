<?php
error_reporting(E_ERROR | E_PARSE);

require "mysql_ops.php";
connect_db_tr ($database);
?>

<!DOCTYPE html>
<html>
<head>
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta http-equiv="refresh" content="600">
<meta charset="UTF-8">
<?php $random = mt_rand(1,9999999); ?>
<link rel="stylesheet" href="styles.css?random=<?php echo $random; ?>">
</head>
<body>

<?php
$cluster_name = $_POST['cluster_name']; if ($cluster_name == NULL) $cluster_name = $_GET['cluster_name']; if ($cluster_name == NULL AND isset($_COOKIE['clname'])) $cluster_name = $_COOKIE['clname'];
if ($cluster_name == '') { $randomIndex = rand(0, 1); $cluster_name = $clusters[$randomIndex]; $nothing_selected = "selected"; }
else { $cluster_name_word = $cluster_name."_selected";  $$cluster_name_word = "selected"; setcookie("clname", $cluster_name, 0); }

$cl_index = array_search($cluster_name, $clusters);
if ($index !== false) { $desc = $descs[$index]; } else { $desc = ""; }

$adm_link = "<font size=-3><i><a href=\"/admin.php?$mylink\">admin</a></i></font>";

//en son report_time al
$cluster_name_i = strtoupper (str_replace("HPC", '', $cluster_name));
echo "<h3>$cluster_name_i Monitor - $adm_link >>> hello User</h3>";
$enson_report_time = get_that_generic ("temp_monitoringtable", $cluster_name, "limit", "time", "report_time", "id DESC");

$result_generic = get_all_generic ("temp_monitoringtable", "$cluster_name", "report_time", $enson_report_time, "node_name", $query_ek3);
$nrows = mysqli_num_rows($result_generic);
for ($i=1; $i<=$nrows; $i++) { //for1 start
 $row19=mysqli_fetch_array($result_generic ,MYSQLI_ASSOC);
 $node_name[$i]= $row19['node_name'];
 $node_stat[$i]= $row19['node_stat'];
 $node_stat_extra[$i]= node_status_desc ($row19['node_stat']);
 $cpu_usage[$i]= $row19['cpu_usage'];
 $cpu_usage_extra[$i]= "<b>Top two processes of the moment:</b><br>".nl2br($row19['top_processes']);
 $ram_usage[$i]= $row19['ram_usage'];
 $ram_usage_extra[$i]= "<b>Memory outlook of the moment:</b><br>"."mem_total: ".sprintf ("%.2f", $row19['mem_total']/1000000)." GB<br>mem_available. ".sprintf ("%.2f", $row19['mem_available']/1000000)." GB<br>swap_used: ".sprintf ("%.2f", $row19['swap_used']/1000000)." GB";
 $disk_max[$i] = get_generic2 ("hwtable", $cluster_name, "node_name", $node_name[$i],"category","hw_ref_diskmaxMBs",NULL,NULL, "veri");
 if (is_numeric($row19['disk_write_MBs'])) { $disk_usage[$i] = $row19['disk_write_MBs']; if ($disk_usage[$i] <0) $disk_usage[$i] = "-1"; }
 $disk_usage_extra[$i]= $disk_usage[$i]." MB/s is the maximum sequential write speed that can be achieved at this moment.<br>------<br><b>Top disk write speed observed so far:</b> ".$disk_max[$i]." MB/s";
 $home_max[$i] = sprintf('%.2f',get_generic2 ("hwtable", $cluster_name, "node_name", $node_name[$i], "category","hw_ref_homemaxMbs",NULL,NULL, "veri")/1000); if ($home_max[$i] == "yok") $home_max[$i] = 1;
 if (is_numeric($row19['nw_speed_Mbs'])) { $nw_usage[$i] = sprintf('%.2f',$row19['nw_speed_Mbs']/1000); if ($nw_usage[$i] <0) $nw_usage[$i] = "-1"; } 
 $nw_usage_extra[$i]= $nw_usage[$i]." Gb/s is the maximum network transfer speed that can be achieved at this moment.<br>------<br><b>Top network transfer speed observed so far:</b> ".$home_max[$i]." Gb/s";
 $gpu_usage[$i]= $row19['gpu_usage']; $gpu_usage_pieces[$i] = explode("||", $gpu_usage[$i]); $gpu_adet[$i]=0;
 foreach ($gpu_usage_pieces[$i] as $key => $value) { //foreach start
  if (stristr($value, "NVIDIA") OR stristr($value, "Tesla") ) { //if2 start
   $gpu_line[$i][$gpu_adet[$i]] = explode(":", $value);
   $gpu_name[$i][$gpu_adet[$i]] = trim ( str_replace(" ", '_', $gpu_line[$i][$gpu_adet[$i]][0]));
   $gpu_utilization[$i][$gpu_adet[$i]] = trim( str_replace("%", '', $gpu_line[$i][$gpu_adet[$i]][1]));
   $gpu_adet[$i]++;
  } //if2 end
 } //foreach end
} //for1 end

echo "<table><tr><th width=5%>NODE</th><th width=5%>CPU Utilization %</th><th width=5%>Memory Utilization %</th><th width=5%>Storage Nw Speed</th><th width=35%>GPU Utilization %</th><th width=40% colspan=2>$desc</th></tr>";
for ($i=1; $i<=$nrows; $i++) { //for2 start
 if ($node_stat[$i] == "idle") { //bigif start
  echo "<tr><th><a href=\"details.php?$mylink&cl=$cluster_name&cn=".$node_name[$i]."\"><div class=\"tooltip\">".$node_name[$i]."</a><span class=\"tooltiptext\"></span></div> </th><td><div class=\"tooltip\">".coloring($cpu_usage[$i],"cpu_usage",0)."<span class=\"tooltiptext\">".$cpu_usage_extra[$i]."</span></div></td><td><div class=\"tooltip\">".coloring($ram_usage[$i],"ram_usage",0)."<span class=\"tooltiptext\">".$ram_usage_extra[$i]."</span></div></td><td><div class=\"tooltip\">".coloring($nw_usage[$i],"nw_usage",$home_max[$i])." Gb/s<span class=\"tooltiptext\">".$nw_usage_extra[$i]."</span></div></td><td>";

 if ($gpu_usage[$i] == "-1") echo coloring($gpu_usage[$i],"gpu_usage",0);
 else { //else5 start
  for ($j=0; $j<$gpu_adet[$i]; $j++) { //for2 start
   echo str_replace("NVIDIA_", '', $gpu_name[$i][$j])." : %".coloring($gpu_utilization[$i][$j],"gpu_usage",0); if ($gpu_name[$i][$j+1] != NULL) echo " || ";
  } //for2 end
 } //else5 end
 echo "</td>";
 } //bigif end
 else {
  echo "<tr><th><a href=\"details.php?$mylink&cl=$cluster_name&cn=".$node_name[$i]."\">".$node_name[$i]."</th><td colspan=4 class=\"blue\"><div class=\"tooltip\">".strtoupper($node_stat[$i])."<span class=\"tooltiptext\">".$node_stat_extra[$i]."</span></div></td>";
 }
 
$hw = get_hardware ($cluster_name, $node_name[$i],"hw");
echo "<td colspan=2><button class=\"collapsible\">".strtoupper($node_name[$i])." Hardware Specs</button><div class=\"content\">$hw</div></td></tr>";
} //for2 end
echo "</table>";
if ( (time() - strtotime($enson_report_time)) / 3600 >= 16) $enson_report_time = "<font color=red size=+1><b>$enson_report_time</b></font>";
echo "<i>Last sample time: $enson_report_time</i><br>";
echo "<br>==========================<br>";
echo "<table border=0 style=\"width:500px\"><form method=\"post\"><tr>
    <td style=\"width:200px\"><select name='cluster_name' onchange='if(this.value != 0) { this.form.submit(); }'>
        <option value='' disabled " . (empty($selected_cluster) ? "selected" : "") . ">HPC Clusters</option>";
foreach ($clusters as $cluster) {
    $selected = ($selected_cluster == $cluster) ? "selected" : "";
    echo "<option value='$cluster' $selected>$cluster</option>";
}

echo "</select></td> ";
 echo "    <td style=\"width:100px\"><font size=-1><i><a href=\"/graph_all.php?cl=$cluster_name&wn=all\">cluster statistics</a></i></font></td>";
echo "
</tr></form></table>
";

disconnect_db_tr ();

function coloring ($input_value, $kat, $max_value) {
 if ($input_value == "-1") return "<font color=grey>NA</font>";
 if ($kat == "disk_usage" OR $kat == "nw_usage") { //if1 start
  $percentage = round(($input_value * 100) / $max_value);
  if ($percentage >= 90) return "<font color=green>$input_value</font>";
  else if ($percentage > 75) return "<font color=black>$input_value</font>";
  else if ($percentage > 50) return "<font color=darkblue>$input_value</font>";
  else return "<font color=red>$input_value</font>";
 } //if1 end
 else { //else start
  if ($input_value < 50) return "<font color=green>$input_value</font>";
  else if ($input_value < 75) return "<font color=black>$input_value</font>";
  else if ($input_value < 95) return "<font color=darkblue>$input_value</font>";
  else return "<font color=red>$input_value</font>";
 } //else end
}

function get_hardware ($cluster_name, $node_name, $type) {
global $connection;
$result_generic2 = get_all_generic ("hwtable", "$cluster_name", "node_name", $node_name, "hw_id", '');
if ($result_generic2 == "yok") $nrows = 0;
else $nrows = mysqli_num_rows($result_generic2);
if ($nrows <= 1) return "teknik hata!";
for ($i=1; $i<=$nrows; $i++) { //forall start
 $row19=mysqli_fetch_array($result_generic2 ,MYSQLI_ASSOC);
  $hw_list = $hw_list."<div class=\"tooltip\"><font size=+2>".$row19['category']."</font><span class=\"tooltiptext\">".nl2br(nl2br($row19['veri']))."</span></div><br>
";
} //forall end

if ($type == "hw") { //if2 start
 return "$hw_list<br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br>";
} //if2 end
}


function listformat ($list, $category) {
 $list = trim($list);
 $parca = explode(":", $list); $sayi = count($parca);
 if ($parca[0] == "Machine") {$data= "<b>Server Information</b><br>"; for ($i=1;$i<=$sayi; $i++) $data=$data.$parca[$i]."<br>"; }
 else if ($parca[0] == "System") {$data= "<b>OS Information</b><br>"; for ($i=1;$i<=$sayi; $i++) $data=$data.$parca[$i]."<br>"; }
 else if ($parca[0] == "PCI Slots") {$data= "<b>PCI Slots</b><br>"; for ($i=1;$i<=$sayi; $i++) $data=$data.$parca[$i]."<br>"; }
 else if ($parca[0] == "CPU") {$data= "<b>CPU Information</b><br>"; for ($i=1;$i<=$sayi; $i++) $data=$data.$parca[$i]."<br>"; }
 else if ($parca[0] == "Memory") {$data= "<b>Memory Information</b><br>"; for ($i=1;$i<=$sayi; $i++) $data=$data.$parca[$i]."<br>"; }
 else if ($parca[0] == "Graphics") {$data= "<b>GPU Information</b><br>"; for ($i=1;$i<=$sayi; $i++) $data=$data.$parca[$i]."<br>"; }
 else if ($parca[0] == "Network") {$data= "<b>Network Information</b><br>"; for ($i=1;$i<=$sayi; $i++) $data=$data.$parca[$i]."<br>"; }
 else if ($parca[0] == "Drives") {$data= "<b>Drive Information</b><br>"; for ($i=1;$i<=$sayi; $i++) $data=$data.$parca[$i]."<br>"; }
 else if ($parca[0] == "Partition") {$data= "<b>Partitions</b><br>"; for ($i=1;$i<=$sayi; $i++) $data=$data.$parca[$i]."<br>"; }
 else if ($parca[0] == "Unmounted") {$data= "<b>Unmounted Partitions</b><br>"; for ($i=1;$i<=$sayi; $i++) $data=$data.$parca[$i]."<br>"; }
 else if ($parca[0] == "Logical") {$data= "<b>Logical Volumes</b><br>"; for ($i=1;$i<=$sayi; $i++) $data=$data.$parca[$i]."<br>"; }
 else if ($parca[0] == "RAID") {$data= "<b>RAID Controllers</b><br>"; for ($i=1;$i<=$sayi; $i++) $data=$data.$parca[$i]."<br>"; }

 return $data;
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
<!--/////////////////////////////////////////////-->
<!--//////for questions://///////////////////////-->
<!--//////serdar.acir@sabanciuniv.edu////////////-->
<!--/////////////////////////////////////////////-->
</html>

