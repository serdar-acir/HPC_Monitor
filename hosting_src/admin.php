<!--/////////////////////////////////////////////-->
<!--//////written by:////////////////////////////-->
<!--//////serdar.acir@sabanciuniv.edu////////////-->
<!--/////////////////////////////////////////////-->
<!DOCTYPE html> <html> <head> <body> 
<?php
error_reporting(E_ERROR | E_PARSE);
require_once "mysql_ops.php";
connect_db_tr ($database);
echo "<table><tr><td><h3><a href=\"" . (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' ? "https" : "http") . "://" . $_SERVER['HTTP_HOST'] . "/admin.php?$mylink\">Benchmark Monitoring Tool - Admin</a> - <font size=-3><i><a href=\"" . (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' ? "https" : "http") . "://" . $_SERVER['HTTP_HOST'] . "/?$mylink\">monitor</a></i></font><br>" . $_GET['message'] . "</h3></td><td>";

$cluster_name = $_POST['cluster_name']; if ($cluster_name == NULL) $cluster_name = $_GET['cluster_name'];
$node_name = $_POST['node_name']; if ($node_name == NULL) $node_name = $_GET['node_name'];
if ($cluster_name == '') $new_ek1 = "disabled"; else if ($node_name == "new") $new_ek1 = "selected";
if ($cluster_name != NULL) { $cluster_name_word = $cluster_name."_selected";  $$cluster_name_word = "selected"; } else $nothing_selected = "selected";
echo "<form method=\"post\"><tr>
    <select name='cluster_name' onchange='if(this.value != 0) { this.form.submit(); }'>
        <option value='' disabled " . (empty($selected_cluster) ? "selected" : "") . ">HPC Clusters</option>";
foreach ($clusters as $cluster) {
    $selected = ($selected_cluster == $cluster) ? "selected" : "";
    echo "<option value='$cluster' $selected>$cluster</option>";
}
echo "
</form>
</td><td>
";

if ($node_name == NULL) $nothing_selected = "selected";
$result_generic = get_all_generic ("hwtable", $cluster_name, NULL, NULL, "node_name", '');
$nrows = mysqli_num_rows($result_generic);
echo "<form method=\"post\">
    <select name='node_name' onchange='if(this.value != 0) { this.form.submit(); }'> 
    <option $nothing_selected disabled>Node</option>";
for ($i=1; $i<=$nrows; $i++) { //for1 start
 $row19=mysqli_fetch_array($result_generic ,MYSQLI_ASSOC);
 if ($onceki_node_name == $row19['node_name']) continue;
 $onceki_node_name = $row19['node_name'];
 if ($row19['node_name'] == $node_name) $node_selected ="selected"; else $node_selected ="";
 echo "  <option value='".$row19['node_name']."' $node_selected>".$row19['node_name']."</option>
";
} //for1 end
echo "
    </select>
    <input type=\"hidden\" id=\"cluster_name\" name=\"cluster_name\" value=\"$cluster_name\">
    </form>
</td></tr></table>
";

if ($node_name == NULL) { disconnect_db_tr (); exit; }
$result_generic2 = get_all_generic ("hwtable", $cluster_name, "node_name", $node_name, "hw_id", '');
$nrows = mysqli_num_rows($result_generic2);
echo "<table>
<form action=\"update.php\" method=\"POST\" onsubmit=\"myButton.disabled = true; return true;\">";

for ($i=1; $i<=$nrows; $i++) { //for2 start
 $row19=mysqli_fetch_array($result_generic2 ,MYSQLI_ASSOC);
 extract ($row19);
 echo "<tr align=left><td width=15%><b>$category</b></td><td><div class=\"wrapper\"><textarea id=\"$category\" name=\"$category\" rows=\"10\" cols=\"50\">$veri</textarea></div></td></tr>";
} //for2 end

echo "<tr><td></td><td align=right><input type=\"submit\" name=\"myButton\" value=\"Submit\"></td></tr>";
$current_time = date('Y-m-d H:i:s', time());
echo "<input type=\"hidden\" id=\"current_time\" name=\"current_time\" value=\"$current_time\">";
if ($node_name != NULL AND $node_name != "new") {
 echo "<input type=\"hidden\" id=\"cluster_name\" name=\"cluster_name\" value=\"$cluster_name\">
 <input type=\"hidden\" id=\"node_name\" name=\"node_name\" value=\"$node_name\">";
}

echo "</form></table>";
echo "<i>Last entry time: $report_time</i><br>";
echo "<br>==========================<br>";

disconnect_db_tr ();

?>


</body>
<style>
body {background-color: powderblue;}
h1   {color: blue;}
p    {color: red;}
table {
  table-layout: fixed;
  width: 70%;
}

table th {
  border-bottom: 1px solid #e9e9e9;
}

table thead td,
td {
  border-left: 1px solid #f2f2f2;
  border-right: 1px solid #d5d5d5;
  background: #ddd repeat-x scroll 0 100%;
  font-weight: bold;
  text-align: left;
}

table tr td,
td {
  border: 1px solid #D5D5D5;
  padding: 5px;
}

table th:hover {
  background: #fcfcfc;
}

table th ul.actions {
  margin: 0;
}

table th ul.actions li {
  display: inline;
  margin-right: 3px;
}

        .wrapper {
            padding:0 0px;
            margin: 0px 0;
            background-color: #0f9d58;
        }
 
        textarea {
            font-size: 13px;
            width: 100%;
        }
</style>

</html>

