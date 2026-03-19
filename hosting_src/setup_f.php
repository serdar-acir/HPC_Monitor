<?php
// Collect form data
$timezone = $_POST['timezone'];
$clusters = $_POST['clusters'];
$descs = $_POST['descs'];
$database = $_POST['database'];
$host = $_POST['host'];
$user = $_POST['user'];
$password = $_POST['password'];

// Validate the existence of at least one cluster name and description
if (count($clusters) < 1 || count($descs) < 1) {
    die("Error: At least one cluster name and description must be provided.");
}

// Create the HPC.config file
$config_content = "<?php\n";
$config_content .= "//General configuration\n";
$config_content .= "date_default_timezone_set('$timezone');\n";
$config_content .= "\$clusters = " . var_export($clusters, true) . ";\n";
$config_content .= "\$descs = " . var_export($descs, true) . ";\n\n";
$config_content .= "//Database configuration\n";
$config_content .= "\$database=\"$database\";\n";
$config_content .= "\$host=\"$host\";\n";
$config_content .= "\$user=\"$user\";\n";
$config_content .= "\$password=\"$password\";\n";
$config_content .= "?>";

if (file_put_contents('HPC.config', $config_content) === false) {
    die("Error: Could not write to HPC.config.");
}

// Connect to MySQL database
$connection = new mysqli($host, $user, $password);

if ($connection->connect_error) {
    die("Connection failed: " . $connection->connect_error);
}

// Check if database exists
$db_selected = mysqli_select_db($connection, $database);
if (!$db_selected) {
    die("Error: Database not found.");
}

// SQL statements to create the required tables
$tables = [
    "CREATE TABLE IF NOT EXISTS hwtable (
        hw_id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        report_time DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
        cluster_name VARCHAR(20) DEFAULT 'UNKNOWN',
        node_name VARCHAR(10) DEFAULT 'UNKNOWN',
        category VARCHAR(20) DEFAULT 'UNKNOWN',
        veri VARCHAR(5000) DEFAULT 'UNKNOWN'
    )",
    
    "CREATE TABLE IF NOT EXISTS monitoringtable (
        id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        cluster_name VARCHAR(20) DEFAULT NULL,
        report_time DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
        node_name VARCHAR(10) DEFAULT 'UNKNOWN',
        node_stat VARCHAR(10) DEFAULT NULL,
        cpu_usage VARCHAR(10) DEFAULT 'UNKNOWN',
        mem_available VARCHAR(10) DEFAULT 'UNKNOWN',
        mem_total VARCHAR(10) DEFAULT 'UNKNOWN',
        swap_used VARCHAR(10) DEFAULT 'UNKNOWN',
        ram_usage VARCHAR(10) DEFAULT 'UNKNOWN',
        disk_write_MBs VARCHAR(10) DEFAULT NULL,
        nw_speed_Mbs VARCHAR(10) DEFAULT NULL,
        gpu_usage VARCHAR(250) DEFAULT NULL,
        top_processes VARCHAR(5000) DEFAULT NULL,
        retired ENUM('yes','no') DEFAULT 'no'
    )",
    
    "CREATE TABLE IF NOT EXISTS temp_monitoringtable (
        id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        cluster_name VARCHAR(20) DEFAULT NULL,
        report_time DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
        node_name VARCHAR(10) DEFAULT 'UNKNOWN',
        node_stat VARCHAR(10) DEFAULT NULL,
        cpu_usage VARCHAR(10) DEFAULT 'UNKNOWN',
        mem_available VARCHAR(10) DEFAULT 'UNKNOWN',
        mem_total VARCHAR(10) DEFAULT 'UNKNOWN',
        swap_used VARCHAR(10) DEFAULT 'UNKNOWN',
        ram_usage VARCHAR(10) DEFAULT 'UNKNOWN',
        disk_write_MBs VARCHAR(10) DEFAULT NULL,
        nw_speed_Mbs VARCHAR(10) DEFAULT NULL,
        gpu_usage VARCHAR(250) DEFAULT NULL,
        top_processes VARCHAR(5000) DEFAULT NULL,
        retired ENUM('yes','no') DEFAULT 'no'
    )"
];

foreach ($tables as $query) {
    if (!$connection->query($query)) {
        die("Error creating table: " . $connection->error);
    }
}

echo "HPC.config created successfully and database tables set up.";
$connection->close();
?>

