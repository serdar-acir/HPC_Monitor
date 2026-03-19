<?php
$debug = 0; //1 ise script ile çalıştır
error_reporting(E_ERROR | E_PARSE);
require_once "mysql_ops.php";
require_once ('jpgraph/src/jpgraph.php');
require_once ('jpgraph/src/jpgraph_line.php');
connect_db_tr ($database);

$cluster_name = $_GET['cl']; if ($cluster_name == NULL) $cluster_name = $_POST['cl'];
$node_name = $_GET['cn']; if ($node_name == NULL) $node_name = $_POST['cn'];
$param = $_GET['param']; if ($param == NULL) $param = $_POST['param'];
$ref_time = $_GET['ref_time']; if ($ref_time == NULL) $ref_time = $_POST['ref_time'];

$result_generic = get_all_generic ("monitoringtable", "$cluster_name", "node_name", $node_name, "report_time DESC", '');
$nrows = mysqli_num_rows($result_generic);
$usage_max=0; $total_usage=0;
for ($i=1; $i<=$nrows; $i++) { //for1 start
 $row19=mysqli_fetch_array($result_generic ,MYSQLI_ASSOC);
 if ($row19['report_time'] < $ref_time ) { $i--;  break; }
 $report_time[$i]= $row19['report_time'];
 $usage[$i]= $row19["$param"]; if ($usage[$i]==NULL) $usage[$i]=0;
 if ($param == "mem_available") { $usage[$i]=sprintf ("%.2f", $usage[$i]/1000000); $total_mem = sprintf ("%.2f", $row19['mem_total']/1000000); }
 else if ($param == "nw_speed_Mbs") { $usage[$i]=sprintf ("%.2f", $usage[$i]/1000); }
 if ($usage_max > $usage[$i]) $usage_max=$usage[$i]; 
 $total_usage=$total_usage+$usage[$i];
// if ($debug == 1) echo "$param: ".$row19[$param]."\n";
} //for1 end
$usage = array_reverse($usage);

if ($param == "cpu_usage") { $title = "CPU Utilization"; $ort_ek =""; $aYMin=0; $aYMax=100; $aXMin=0; $aXMax=$i; }
else if ($param == "ram_usage") { $title = "RAM Utilization"; $ort_ek =""; $aYMin=0; $aYMax=100; $aXMin=0; $aXMax=$i; }
else if ($param == "mem_available") { $title = "Available Memory (GB)\nTotal Memory: $total_mem GB"; $ort_ek ="GB"; $aYMin=0; $aYMax=$usage_max; $aXMin=0; $aXMax=$i; }
else if ($param == "swap_used") { $title = "Swap Space Used (GB)"; $ort_ek ="GB"; $aYMin=0; $aYMax=$usage_max; $aXMin=0; $aXMax=$i; }
else if ($param == "disk_write_MBs") { $title = "Disk Write Speed (MB/s)"; $ort_ek ="MB/s"; $aYMin=0; $aYMax=$usage_max; $aXMin=0; $aXMax=$i;}
else if ($param == "nw_speed_Mbs") { $title = "Network Transfer Speed (Gbit/s)"; $ort_ek ="Gbits/s"; $aYMin=0; $aYMax=$usage_max; $aXMin=0; $aXMax=$i; }
$ortalama = sprintf ("%.2f", $total_usage / $i);

$graph = new Graph(400,200,"auto");
$graph->SetClipping();
$graph->SetScale('linlin', $aYMin, $aYMax, $aXMin, $aXMax);
$theme_class=new UniversalTheme;
$graph->SetTheme($theme_class);
$graph->img->SetAntiAliasing(false);
$graph->title->Set("$title");
$graph->SetBox(false);
//$graph->SetMargin(30,10,40,20);
$graph->img->SetAntiAliasing();
$graph->yaxis->HideZeroLabel();
$graph->yaxis->HideLine(false);
$graph->yaxis->HideTicks(false,false);
$graph->xgrid->Show();
$graph->xgrid->SetLineStyle("solid");
$graph->xaxis->SetLabelAlign('right','center','right');
$graph->xaxis->SetTickLabels($report_time_short);
$graph->xgrid->SetColor('#E3E3E3');
$p1 = new LinePlot($usage);
$graph->Add($p1);
$p1->SetColor("#6495ED");
$p1->SetLegend("$i adet veri\nOrtalama: $ortalama $ort_ek");
$graph->legend->SetFrameWeight(1);
if ($debug == 1) { echo "$i adet veri\nusage_max=$usage_max\nortalama=$ortalama\n"; }
else $graph->Stroke();

disconnect_db_tr ();
?>



