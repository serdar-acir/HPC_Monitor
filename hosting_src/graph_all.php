<?php
error_reporting(E_ERROR | E_PARSE);
//ini_set('memory_limit','2048M');
ini_set('memory_limit','-1');
ini_set('display_errors', 'On');
set_time_limit(0);
$debug = 0; //1 ise script ile çalıştır
if ($debug == 1) { $_GET['cl'] = "SakuraHPC"; $_GET['cn'] = "cn01"; $_GET['param'] = "cpu_usage"; $_GET['ref_time'] = "2023-02-20 18:49:14"; }
require_once "mysql_ops.php";
require_once ('jpgraph/src/jpgraph.php');
require_once ('jpgraph/src/jpgraph_line.php');
connect_db_tr ($database);

$cluster_name = $_GET['cl']; if ($cluster_name == NULL) $cluster_name = $_POST['cl'];
$param = $_GET['param']; if ($param == NULL) $param = $_POST['param'];
$ref_time = $_GET['ref_time']; if ($ref_time == NULL) $ref_time = $_POST['ref_time'];
$param = "cpu_usage";
$ref_time = "2021-01-01 00:00:00";

$subset_counter=0;
$result_generic = get_all_generic ("monitoringtable", "$cluster_name", '', '', "report_time", $query_ek3);
$nrows = mysqli_num_rows($result_generic);
//if ($nrows > 1000) $atla = round($nrows/1000); else $atla = 1; //toplamda aralıklı olarak 1000 adetten fazla veri istemiyoruz.
//for ($i=1; $i<=round($nrows / $atla); $i++) { //for1 start
for ($i=1; $i<=$nrows; $i++) { //for1 start
 //for ($j=1; $j<=$atla; $j++) { $row19=mysqli_fetch_array($result_generic ,MYSQLI_ASSOC); }//atla adedini atla
 $row19=mysqli_fetch_array($result_generic ,MYSQLI_ASSOC);
 $report_time[$i]= $row19['report_time']; if ($i==1) $baslangic_time = $report_time[$i]; else if ($i==$nrows) $bitis_time = $report_time[$i];
 if ($report_time[$i] != $report_time[$i-1]) { //degisti start
  $usage[$m]= round ($ara_toplam / $subset_counter); $m++;
  if ($row19["$param"]==NULL) $row19["$param"]=0; if ($row19["node_stat"]!="idle") $row19["$param"]=100; $ara_toplam = $row19["$param"]; $subset_counter=1;
 } //degisti end
 else { //degismedi start
  if ($row19["$param"]==NULL) $row19["$param"]=0; if ($row19["node_stat"]!="idle") $row19["$param"]=100; $ara_toplam = $ara_toplam + $row19["$param"]; $subset_counter++;
 } //degismedi end
// if ($debug == 1) echo "$param: ".$row19[$param]."\n";
} //for1 end
//echo "nrows:$nrows<br>sampled: ";
//echo count($usage); exit;

if ($param == "cpu_usage") { $title = "CPU Utilization for $cluster_name for all nodes"; $ort_ek ="Start: $baslangic_time\nEnd: $bitis_time"; $aYMin=0; $aYMax=100; $aXMin=0; $aXMax=$m; }

$graph = new Graph(1600,800,"auto");
$graph->SetClipping();
$graph->SetScale('linlin', $aYMin, $aYMax, $aXMin, $aXMax);
$theme_class=new UniversalTheme;
$graph->SetTheme($theme_class);
$graph->img->SetAntiAliasing(false);
$graph->title->Set("$title");
$graph->SetBox(false);
$graph->SetMargin(30,10,40,20);
$graph->img->SetAntiAliasing();
$graph->yaxis->HideZeroLabel();
$graph->yaxis->HideLine(false);
$graph->yaxis->HideTicks(false,false);
$graph->xgrid->Show();
$graph->xgrid->SetLineStyle("solid");
$graph->xaxis->SetLabelAlign('right','center','right');
$graph->xgrid->SetColor('#E3E3E3');
$p1 = new LinePlot($usage);
$graph->Add($p1);
$p1->SetColor("#6495ED");
$p1->SetLegend("$i adet veri\n$ort_ek");
$p1->SetWeight(3);
$p1->SetStyle("solid");
$graph->legend->SetFrameWeight(1);
if ($debug == 1) { echo "$i adet veri\n"; }
else $graph->Stroke();

disconnect_db_tr ();
?>



