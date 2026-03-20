<?php
declare(strict_types=1);

error_reporting(E_ALL);
ini_set('display_errors', '1');

require "mysql_ops.php";
connect_db_tr($database);

$cluster_name = $_POST['cluster_name'] ?? $_GET['cluster_name'] ?? $_COOKIE['clname'] ?? '';
$mylink = '';
$query_ek3 = '';
$nothing_selected = '';

if ($cluster_name === '') {
    $randomIndex = array_rand($clusters);
    $cluster_name = $clusters[$randomIndex];
    $nothing_selected = 'selected';
}

setcookie("clname", (string)$cluster_name, 0);

$cl_index = array_search($cluster_name, $clusters, true);
if ($cl_index !== false && isset($descs[$cl_index])) {
    $desc = $descs[$cl_index];
} else {
    $desc = "";
}

$cluster_name_i = strtoupper(str_replace("HPC", '', (string)$cluster_name));
$random = mt_rand(1, 9999999);
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta http-equiv="refresh" content="600">
<meta charset="UTF-8">
<link rel="stylesheet" href="styles.css?random=<?php echo $random; ?>">
<title>HPC Monitor</title>
</head>

<body>
<div class="page">

<?php
$adm_link = '<span class="admin-link"><a href="/admin.php">admin</a></span>';

echo "<div class='topbar'>
        <div class='title-wrap'>
            <h3>" . htmlspecialchars($cluster_name_i, ENT_QUOTES, 'UTF-8') . " Monitor</h3>
            <div class='subtitle'>$adm_link >>> hello User</div>
        </div>
      </div>";

$enson_report_time = get_that_generic(
    "temp_monitoringtable",
    $cluster_name,
    "limit",
    "time",
    "report_time",
    "id DESC"
);

if ($enson_report_time === "yok" || $enson_report_time === null || $enson_report_time === '') {
    $result_generic = "yok";
    $nrows = 0;
} else {
    $result_generic = get_all_generic(
        "temp_monitoringtable",
        $cluster_name,
        "report_time",
        $enson_report_time,
        "node_name",
        $query_ek3
    );
    $nrows = ($result_generic === "yok") ? 0 : mysqli_num_rows($result_generic);
}

/* initialize arrays */
$node_name = $node_stat = $node_stat_extra = $cpu_usage = $cpu_usage_extra = [];
$ram_usage = $ram_usage_extra = $disk_usage = $disk_usage_extra = [];
$nw_usage = $nw_usage_extra = $gpu_usage = $gpu_adet = [];
$gpu_name = $gpu_utilization = $gpu_usage_pieces = [];
$disk_max = $home_max = [];

for ($i = 1; $i <= $nrows; $i++) {
    $row19 = mysqli_fetch_array($result_generic, MYSQLI_ASSOC);
    if (!$row19) {
        break;
    }

    $node_name[$i] = (string)($row19['node_name'] ?? '');
    $node_stat[$i] = (string)($row19['node_stat'] ?? '');
    $node_stat_extra[$i] = node_status_desc($node_stat[$i]);

    $cpu_usage[$i] = is_numeric($row19['cpu_usage'] ?? null) ? (string)$row19['cpu_usage'] : "-1";
    $cpu_usage_extra[$i] = "<b>Top two processes of the moment:</b><br>" . nl2br((string)($row19['top_processes'] ?? ''));

    $mem_total = is_numeric($row19['mem_total'] ?? null) ? (float)$row19['mem_total'] : 0.0;
    $mem_available = is_numeric($row19['mem_available'] ?? null) ? (float)$row19['mem_available'] : 0.0;
    $swap_used = is_numeric($row19['swap_used'] ?? null) ? (float)$row19['swap_used'] : 0.0;

    $ram_usage[$i] = is_numeric($row19['ram_usage'] ?? null) ? (string)$row19['ram_usage'] : "-1";
    $ram_usage_extra[$i] = "<b>Memory outlook:</b><br>"
        . "mem_total: " . sprintf("%.2f", $mem_total / 1000000) . " GB<br>"
        . "mem_available: " . sprintf("%.2f", $mem_available / 1000000) . " GB<br>"
        . "swap_used: " . sprintf("%.2f", $swap_used / 1000000) . " GB";

    $disk_max_raw = get_generic2(
        "hwtable",
        $cluster_name,
        "node_name",
        $node_name[$i],
        "category",
        "hw_ref_diskmaxMBs",
        null,
        null,
        "veri"
    );
    $disk_max[$i] = is_numeric($disk_max_raw) ? (float)$disk_max_raw : 0.0;

    $disk_usage[$i] = "-1";
    if (is_numeric($row19['disk_write_MBs'] ?? null)) {
        $disk_val = (float)$row19['disk_write_MBs'];
        if ($disk_val >= 0) {
            $disk_usage[$i] = (string)$disk_val;
        }
    }

    $disk_usage_extra[$i] = $disk_usage[$i] . " MB/s current.<br>------<br><b>Max observed:</b> " . $disk_max[$i] . " MB/s";

    $home_max_raw = get_generic2(
        "hwtable",
        $cluster_name,
        "node_name",
        $node_name[$i],
        "category",
        "hw_ref_homemaxMbs",
        null,
        null,
        "veri"
    );
    $home_max[$i] = is_numeric($home_max_raw) ? ((float)$home_max_raw / 1000) : 1.0;

    $nw_usage[$i] = "-1";
    if (is_numeric($row19['nw_speed_Mbs'] ?? null)) {
        $nw_val = (float)$row19['nw_speed_Mbs'] / 1000;
        if ($nw_val >= 0) {
            $nw_usage[$i] = sprintf('%.2f', $nw_val);
        }
    }

    $nw_usage_extra[$i] = $nw_usage[$i] . " Gb/s current.<br>------<br><b>Max observed:</b> " . $home_max[$i] . " Gb/s";

    $gpu_usage[$i] = (string)($row19['gpu_usage'] ?? '-1');
    $gpu_usage_pieces[$i] = explode("||", $gpu_usage[$i]);
    $gpu_adet[$i] = 0;
    $gpu_name[$i] = [];
    $gpu_utilization[$i] = [];

    foreach ($gpu_usage_pieces[$i] as $value) {
        if (stripos($value, "NVIDIA") !== false || stripos($value, "Tesla") !== false) {
            $gpu_line = explode(":", $value, 2);
            $gpu_name[$i][$gpu_adet[$i]] = trim(str_replace(" ", '_', (string)($gpu_line[0] ?? 'GPU')));
            $gpu_utilization[$i][$gpu_adet[$i]] = trim(str_replace("%", '', (string)($gpu_line[1] ?? '0')));
            $gpu_adet[$i]++;
        }
    }
}

echo "<div class='card'><table>
<tr>
<th>NODE</th>
<th>CPU %</th>
<th>RAM %</th>
<th>NET Gb/s</th>
<th>GPU %</th>
<th colspan='2'>" . htmlspecialchars($desc, ENT_QUOTES, 'UTF-8') . "</th>
</tr>";

for ($i = 1; $i <= $nrows; $i++) {
    if (!isset($node_name[$i])) {
        continue;
    }

    $details_link = "details.php?cl=" . urlencode($cluster_name) . "&cn=" . urlencode($node_name[$i]);

    if ($node_stat[$i] === "idle") {
        echo "<tr>
        <td><a href=\"" . htmlspecialchars($details_link, ENT_QUOTES, 'UTF-8') . "\">" . htmlspecialchars($node_name[$i], ENT_QUOTES, 'UTF-8') . "</a></td>

        <td><div class='tooltip'>" . coloring($cpu_usage[$i], "cpu_usage", 0) . "
        <span class='tooltiptext'>" . $cpu_usage_extra[$i] . "</span></div></td>

        <td><div class='tooltip'>" . coloring($ram_usage[$i], "ram_usage", 0) . "
        <span class='tooltiptext'>" . $ram_usage_extra[$i] . "</span></div></td>

        <td><div class='tooltip'>" . coloring($nw_usage[$i], "nw_usage", $home_max[$i]) . " Gb/s
        <span class='tooltiptext'>" . $nw_usage_extra[$i] . "</span></div></td>

        <td>";

        if ($gpu_usage[$i] === "-1") {
            echo coloring($gpu_usage[$i], "gpu_usage", 0);
        } else {
            for ($j = 0; $j < $gpu_adet[$i]; $j++) {
                $gpuLabel = str_replace("NVIDIA_", '', $gpu_name[$i][$j]);
                echo htmlspecialchars($gpuLabel, ENT_QUOTES, 'UTF-8') . " : %" . coloring($gpu_utilization[$i][$j], "gpu_usage", 0);
                if (isset($gpu_name[$i][$j + 1])) {
                    echo " || ";
                }
            }
        }

        echo "</td>";
    } else {
        echo "<tr>
        <td><a href=\"" . htmlspecialchars($details_link, ENT_QUOTES, 'UTF-8') . "\">" . htmlspecialchars($node_name[$i], ENT_QUOTES, 'UTF-8') . "</a></td>
        <td colspan='4' class='blue'>
        <div class='tooltip'>" . htmlspecialchars(strtoupper($node_stat[$i]), ENT_QUOTES, 'UTF-8') . "
        <span class='tooltiptext'>" . htmlspecialchars($node_stat_extra[$i], ENT_QUOTES, 'UTF-8') . "</span></div></td>";
    }

    $hw = get_hardware($cluster_name, $node_name[$i], "hw");

    echo "<td colspan='2'>
    <button class='collapsible link-like'>" . htmlspecialchars($node_name[$i], ENT_QUOTES, 'UTF-8') . " Hardware</button>
    <div class='content'>$hw</div>
    </td></tr>";
}

echo "</table></div>";

if ($enson_report_time !== "yok" && $enson_report_time !== null && $enson_report_time !== '') {
    $ts = strtotime((string)$enson_report_time);
    if ($ts !== false && ((time() - $ts) / 3600 >= 16)) {
        $enson_report_time = "<span class='stale'>" . htmlspecialchars((string)$enson_report_time, ENT_QUOTES, 'UTF-8') . "</span>";
    } else {
        $enson_report_time = htmlspecialchars((string)$enson_report_time, ENT_QUOTES, 'UTF-8');
    }
} else {
    $enson_report_time = "NA";
}

echo "<div class='foot'>Last sample time: $enson_report_time</div>";

echo "<hr class='sep'>";

echo "<div class='controls'>
<form method='post'>
<select name='cluster_name' onchange='this.form.submit()'>
<option value='' disabled $nothing_selected>HPC Clusters</option>";

foreach ($clusters as $cluster) {
    $selected = ($cluster_name === $cluster) ? "selected" : "";
    $cluster_esc = htmlspecialchars((string)$cluster, ENT_QUOTES, 'UTF-8');
    echo "<option value='$cluster_esc' $selected>$cluster_esc</option>";
}

$cluster_stats_link = "/graph_all.php?cl=" . urlencode($cluster_name) . "&wn=all";

echo "</select>
<a class='cluster-link' href=\"" . htmlspecialchars($cluster_stats_link, ENT_QUOTES, 'UTF-8') . "\">cluster statistics</a>
</form>
</div>";

disconnect_db_tr();

/* FUNCTIONS */

function coloring($input_value, $kat, $max_value)
{
    if ((string)$input_value === "-1") {
        return "<span style='color:grey'>NA</span>";
    }

    if ($kat === "disk_usage" || $kat === "nw_usage") {
        $input_num = is_numeric($input_value) ? (float)$input_value : -1;
        $max_num = is_numeric($max_value) ? (float)$max_value : 0.0;

        if ($input_num < 0) {
            return "<span style='color:grey'>NA</span>";
        }

        $percentage = $max_num > 0 ? round(($input_num * 100) / $max_num) : 0;

        if ($percentage >= 90) return "<span style='color:green'>$input_value</span>";
        if ($percentage > 75) return "<span style='color:white'>$input_value</span>";
        if ($percentage > 50) return "<span style='color:#5da0ff'>$input_value</span>";
        return "<span style='color:#ff6b6b'>$input_value</span>";
    }

    $input_num = is_numeric($input_value) ? (float)$input_value : -1;
    if ($input_num < 0) {
        return "<span style='color:grey'>NA</span>";
    }

    if ($input_num < 50) return "<span style='color:green'>$input_value</span>";
    if ($input_num < 75) return "<span style='color:white'>$input_value</span>";
    if ($input_num < 95) return "<span style='color:#5da0ff'>$input_value</span>";
    return "<span style='color:#ff6b6b'>$input_value</span>";
}

function get_hardware($cluster_name, $node_name, $type)
{
    $result_generic2 = get_all_generic("hwtable", $cluster_name, "node_name", $node_name, "hw_id", '');
    if ($result_generic2 === "yok") {
        $nrows = 0;
    } else {
        $nrows = mysqli_num_rows($result_generic2);
    }

    if ($nrows < 1) {
        return "teknik hata!";
    }

    $hw_list = "";

    for ($i = 1; $i <= $nrows; $i++) {
        $row19 = mysqli_fetch_array($result_generic2, MYSQLI_ASSOC);
        if (!$row19) {
            break;
        }

        $category = htmlspecialchars((string)($row19['category'] ?? ''), ENT_QUOTES, 'UTF-8');
        $veri = nl2br(nl2br(htmlspecialchars((string)($row19['veri'] ?? ''), ENT_QUOTES, 'UTF-8')));

$hw_list .= "<div class='hw-item'>
  <div class='hw-title'><strong>".$row19['category']."</strong></div>
  <div class='hw-body'>".nl2br(htmlspecialchars($row19['veri'], ENT_QUOTES, 'UTF-8'))."</div>
</div>";

    }

    if ($type === "hw") {
        return $hw_list;
    }

    return $hw_list;
}

function listformat($list, $category)
{
    $list = trim((string)$list);
    $parca = explode(":", $list);
    $sayi = count($parca);
    $data = "";

    if (($parca[0] ?? '') === "Machine") {$data= "<b>Server Information</b><br>";}
    else if (($parca[0] ?? '') === "System") {$data= "<b>OS Information</b><br>";}
    else if (($parca[0] ?? '') === "PCI Slots") {$data= "<b>PCI Slots</b><br>";}
    else if (($parca[0] ?? '') === "CPU") {$data= "<b>CPU Information</b><br>";}
    else if (($parca[0] ?? '') === "Memory") {$data= "<b>Memory Information</b><br>";}
    else if (($parca[0] ?? '') === "Graphics") {$data= "<b>GPU Information</b><br>";}
    else if (($parca[0] ?? '') === "Network") {$data= "<b>Network Information</b><br>";}
    else if (($parca[0] ?? '') === "Drives") {$data= "<b>Drive Information</b><br>";}
    else if (($parca[0] ?? '') === "Partition") {$data= "<b>Partitions</b><br>";}
    else if (($parca[0] ?? '') === "Unmounted") {$data= "<b>Unmounted Partitions</b><br>";}
    else if (($parca[0] ?? '') === "Logical") {$data= "<b>Logical Volumes</b><br>";}
    else if (($parca[0] ?? '') === "RAID") {$data= "<b>RAID Controllers</b><br>";}

    for ($i = 1; $i <= $sayi; $i++) {
        if (isset($parca[$i])) {
            $data .= htmlspecialchars((string)$parca[$i], ENT_QUOTES, 'UTF-8') . "<br>";
        }
    }

    return $data;
}
?>

<script>
var coll = document.getElementsByClassName("collapsible");

for (var i = 0; i < coll.length; i++) {
  coll[i].addEventListener("click", function() {
    this.classList.toggle("active");
    var content = this.nextElementSibling;
    if (content.style.maxHeight) {
      content.style.maxHeight = null;
    } else {
      content.style.maxHeight = content.scrollHeight + "px";
    }
  });
}
</script>

</div>
</body>
</html>

