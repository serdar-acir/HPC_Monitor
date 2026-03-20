<?php
declare(strict_types=1);

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
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Setup Complete</title>
    <?php $random = mt_rand(1,9999999); ?>
    <link rel="stylesheet" href="styles.css?random=<?php echo $random; ?>">
    <style>
        .setup-shell {
            max-width: 900px;
            margin: 0 auto;
        }
        .result-card {
            background: rgba(18,26,43,.92);
            border: 1px solid var(--border);
            border-radius: 14px;
            box-shadow: var(--shadow);
            padding: 28px;
        }
        .success-title {
            font-size: 22px;
            font-weight: 700;
            margin-bottom: 10px;
        }
        .success-text {
            color: var(--muted);
            margin-bottom: 24px;
        }
        .summary-table {
            width: 100%;
            border-collapse: collapse;
        }
        .summary-table td {
            padding: 12px 14px;
            border-bottom: 1px solid rgba(38,53,82,.8);
            vertical-align: top;
        }
        .summary-table td:first-child {
            width: 220px;
            color: var(--muted);
            font-weight: 700;
        }
        .cluster-list {
            margin: 0;
            padding-left: 18px;
        }
        .cluster-list li {
            margin: 6px 0;
        }
        .button-row {
            margin-top: 24px;
            display: flex;
            gap: 12px;
            flex-wrap: wrap;
        }
        .btn-primary,
        .btn-secondary {
            display: inline-block;
            border-radius: 10px;
            padding: 11px 16px;
            font-weight: 700;
            text-decoration: none;
            border: 1px solid #35507c;
        }
        .btn-primary {
            background: linear-gradient(180deg, #28406a, #203252);
            color: #fff;
        }
        .btn-secondary {
            background: transparent;
            color: var(--text);
        }
    </style>
</head>
<body>
    <div class="page">
        <div class="setup-shell">
            <div class="topbar">
                <div class="title-wrap">
                    <h3>HPC Setup Completed</h3>
                    <div class="subtitle">Configuration file created and database tables initialized</div>
                </div>
            </div>

            <div class="result-card">
                <div class="success-title">Setup finished successfully</div>
                <div class="success-text">HPC.config was created and the required database tables were set up.</div>

                <table class="summary-table">
                    <tr>
                        <td>Timezone</td>
                        <td><?php echo htmlspecialchars($timezone, ENT_QUOTES, 'UTF-8'); ?></td>
                    </tr>
                    <tr>
                        <td>Database</td>
                        <td><?php echo htmlspecialchars($database, ENT_QUOTES, 'UTF-8'); ?></td>
                    </tr>
                    <tr>
                        <td>Host</td>
                        <td><?php echo htmlspecialchars($host, ENT_QUOTES, 'UTF-8'); ?></td>
                    </tr>
                    <tr>
                        <td>Username</td>
                        <td><?php echo htmlspecialchars($user, ENT_QUOTES, 'UTF-8'); ?></td>
                    </tr>
                    <tr>
                        <td>Configured Clusters</td>
                        <td>
                            <ul class="cluster-list">
                                <?php foreach ($clusters as $i => $cluster_name): ?>
                                    <li>
                                        <strong><?php echo htmlspecialchars((string)$cluster_name, ENT_QUOTES, 'UTF-8'); ?></strong>
                                        <?php if (isset($descs[$i])): ?>
                                            — <?php echo htmlspecialchars((string)$descs[$i], ENT_QUOTES, 'UTF-8'); ?>
                                        <?php endif; ?>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        </td>
                    </tr>
                </table>

                <div class="button-row">
                    <a class="btn-primary" href="index.php">Open Monitor</a>
                    <a class="btn-secondary" href="admin.php">Open Admin</a>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
<?php
$connection->close();
?>```
