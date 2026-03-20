<?php
declare(strict_types=1);
error_reporting(E_ERROR | E_PARSE);
require_once "mysql_ops.php";
connect_db_tr($database);
?>
<!--/////////////////////////////////////////////-->
<!--//////written by:////////////////////////////-->
<!--//////serdaracir5@gmail.com//////////////////-->
<!--/////////////////////////////////////////////-->
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Benchmark Monitoring Tool - Admin</title>
<?php $random = mt_rand(1,9999999); ?>
<link rel="stylesheet" href="styles.css?random=<?php echo $random; ?>">
<style>
.admin-table {
  width: 100%;
}
.admin-table td,
.admin-table th {
  vertical-align: top;
}
.admin-form-table {
  width: 100%;
}
.admin-form-table td {
  padding: 10px;
}
.wrapper {
  padding: 0;
  margin: 0;
}
textarea {
  font-size: 13px;
  width: 100%;
  min-height: 180px;
  background: var(--panel-2);
  color: var(--text);
  border: 1px solid var(--border);
  border-radius: 10px;
  padding: 12px;
  resize: vertical;
}
.submit-row {
  text-align: right;
}
input[type="submit"] {
  background: linear-gradient(180deg, #28406a, #203252);
  color: #fff;
  border: 1px solid #35507c;
  border-radius: 10px;
  padding: 10px 16px;
  cursor: pointer;
  font-weight: 700;
}
input[type="submit"]:hover {
  filter: brightness(1.08);
}
.message-box {
  margin-top: 6px;
  color: var(--muted);
  font-size: 14px;
}
</style>
</head>
<body>
<div class="page">

<?php
$scheme = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? "https" : "http";
$host = $_SERVER['HTTP_HOST'] ?? 'localhost';
$message = $_GET['message'] ?? '';

echo "<div class=\"topbar\">
        <div class=\"title-wrap\">
          <h3><a href=\"" . $scheme . "://" . $host . "/admin.php?$mylink\">Benchmark Monitoring Tool - Admin</a></h3>
          <div class=\"subtitle\"><a href=\"" . $scheme . "://" . $host . "/?$mylink\">monitor</a></div>";
if ($message != '') {
    echo "<div class=\"message-box\">$message</div>";
}
echo "   </div>
      </div>";

$cluster_name = $_POST['cluster_name'] ?? null; if ($cluster_name == NULL) $cluster_name = $_GET['cluster_name'] ?? null;
$node_name = $_POST['node_name'] ?? null; if ($node_name == NULL) $node_name = $_GET['node_name'] ?? null;

if ($cluster_name == '') $new_ek1 = "disabled"; else if ($node_name == "new") $new_ek1 = "selected";
if ($cluster_name != NULL) { $cluster_name_word = $cluster_name."_selected"; $$cluster_name_word = "selected"; } else $nothing_selected = "selected";

echo "<div class=\"controls\"><table class=\"admin-table\"><tr><td>
<form method=\"post\">
<select name='cluster_name' onchange='if(this.value != 0) { this.form.submit(); }'>
<option value='' disabled " . (empty($selected_cluster) ? "selected" : "") . ">HPC Clusters</option>";

foreach ($clusters as $cluster) {
    $selected = ($selected_cluster == $cluster) ? "selected" : "";
    echo "<option value='$cluster' $selected>$cluster</option>";
}

echo "</select>
</form>
</td><td>";

if ($node_name == NULL) $nothing_selected = "selected";
$result_generic = get_all_generic("hwtable", $cluster_name, NULL, NULL, "node_name", '');
$nrows = mysqli_num_rows($result_generic);

echo "<form method=\"post\">
<select name='node_name' onchange='if(this.value != 0) { this.form.submit(); }'>
<option $nothing_selected disabled>Node</option>";

$onceki_node_name = null;
for ($i=1; $i<=$nrows; $i++) {
    $row19 = mysqli_fetch_array($result_generic, MYSQLI_ASSOC);
    if ($onceki_node_name == $row19['node_name']) continue;
    $onceki_node_name = $row19['node_name'];
    if ($row19['node_name'] == $node_name) $node_selected ="selected"; else $node_selected ="";
    echo "<option value='".$row19['node_name']."' $node_selected>".$row19['node_name']."</option>";
}

echo "</select>
<input type=\"hidden\" id=\"cluster_name\" name=\"cluster_name\" value=\"$cluster_name\">
</form>
</td></tr></table></div>";

if ($node_name == NULL) { disconnect_db_tr(); exit; }

$result_generic2 = get_all_generic("hwtable", $cluster_name, "node_name", $node_name, "hw_id", '');
$nrows = mysqli_num_rows($result_generic2);

echo "<div class=\"card\"><table class=\"admin-form-table\">
<form action=\"update.php\" method=\"POST\" onsubmit=\"myButton.disabled = true; return true;\">";

$report_time = '';
for ($i=1; $i<=$nrows; $i++) {
    $row19 = mysqli_fetch_array($result_generic2, MYSQLI_ASSOC);
    extract($row19);
    echo "<tr align=left><td width=15%><b>$category</b></td><td><div class=\"wrapper\"><textarea id=\"$category\" name=\"$category\" rows=\"10\" cols=\"50\">$veri</textarea></div></td></tr>";
}

echo "<tr><td></td><td class=\"submit-row\"><input type=\"submit\" name=\"myButton\" value=\"Submit\"></td></tr>";

$current_time = date('Y-m-d H:i:s', time());
echo "<input type=\"hidden\" id=\"current_time\" name=\"current_time\" value=\"$current_time\">";

if ($node_name != NULL AND $node_name != "new") {
    echo "<input type=\"hidden\" id=\"cluster_name\" name=\"cluster_name\" value=\"$cluster_name\">
    <input type=\"hidden\" id=\"node_name\" name=\"node_name\" value=\"$node_name\">";
}

echo "</form></table></div>";
echo "<div class=\"foot\"><i>Last entry time: $report_time</i></div>";
echo "<hr class=\"sep\">";

disconnect_db_tr();
?>

</div>
</body>
</html>
