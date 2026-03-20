<?php
declare(strict_types=1);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>HPC Setup</title>
    <?php $random = mt_rand(1,9999999); ?>
    <link rel="stylesheet" href="styles.css?random=<?php echo $random; ?>">
    <style>
        .setup-shell {
            max-width: 980px;
            margin: 0 auto;
        }
        .setup-card {
            background: rgba(18,26,43,.92);
            border: 1px solid var(--border);
            border-radius: 14px;
            box-shadow: var(--shadow);
            padding: 24px;
        }
        .setup-grid {
            display: grid;
            grid-template-columns: 220px 1fr;
            gap: 14px 18px;
            align-items: center;
        }
        .setup-grid label {
            color: var(--muted);
            font-weight: 700;
        }
        .setup-grid input {
            width: 100%;
            padding: 11px 12px;
            border-radius: 10px;
            border: 1px solid var(--border);
            background: var(--panel-2);
            color: var(--text);
            outline: none;
        }
        .setup-grid input:focus {
            border-color: var(--accent);
        }
        .section-title {
            margin: 22px 0 14px;
            font-size: 18px;
            font-weight: 700;
            color: var(--text);
        }
        .cluster-group {
            margin-bottom: 16px;
            padding: 16px;
            background: rgba(255,255,255,.02);
            border: 1px solid var(--border);
            border-radius: 12px;
        }
        .cluster-group-inner {
            display: grid;
            grid-template-columns: 220px 1fr;
            gap: 14px 18px;
            align-items: center;
        }
        .cluster-index {
            margin-bottom: 12px;
            font-size: 13px;
            font-weight: 700;
            color: var(--muted);
            text-transform: uppercase;
            letter-spacing: .04em;
        }
        .button-row {
            display: flex;
            gap: 12px;
            flex-wrap: wrap;
            margin-top: 20px;
        }
        .btn-primary,
        .btn-secondary {
            border-radius: 10px;
            padding: 11px 16px;
            font-weight: 700;
            cursor: pointer;
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
        .helper {
            margin-top: 8px;
            color: var(--muted);
            font-size: 13px;
        }
        @media (max-width: 760px) {
            .setup-grid,
            .cluster-group-inner {
                grid-template-columns: 1fr;
            }
        }
    </style>
    <script>
        function addCluster() {
            var container = document.getElementById("cluster-container");
            var clusterCount = container.getElementsByClassName("cluster-group").length;

            var newClusterGroup = document.createElement("div");
            newClusterGroup.className = "cluster-group";
            newClusterGroup.innerHTML = `
                <div class="cluster-index">Cluster ${clusterCount + 1}</div>
                <div class="cluster-group-inner">
                    <label for="cluster_name_${clusterCount}">HPC Cluster Name:</label>
                    <input type="text" name="clusters[]" id="cluster_name_${clusterCount}" required>

                    <label for="cluster_desc_${clusterCount}">Description:</label>
                    <input type="text" name="descs[]" id="cluster_desc_${clusterCount}" required>
                </div>
            `;
            container.appendChild(newClusterGroup);
        }
    </script>
</head>
<body>
    <div class="page">
        <div class="setup-shell">
            <div class="topbar">
                <div class="title-wrap">
                    <h3>HPC Setup</h3>
                    <div class="subtitle">Initial configuration for clusters and database connectivity</div>
                </div>
            </div>

            <div class="setup-card">
                <form action="setup_f.php" method="post">
                    <div class="section-title">General Settings</div>
                    <div class="setup-grid">
                        <label for="timezone">Default Timezone:</label>
                        <div>
                            <input type="text" name="timezone" id="timezone" value="Europe/Istanbul" required>
                            <div class="helper">Use a valid timezone string such as Europe/Istanbul.</div>
                        </div>
                    </div>

                    <div class="section-title">HPC Clusters</div>
                    <div id="cluster-container">
                        <div class="cluster-group">
                            <div class="cluster-index">Cluster 1</div>
                            <div class="cluster-group-inner">
                                <label for="cluster_name_0">HPC Cluster Name:</label>
                                <input type="text" name="clusters[]" id="cluster_name_0" required>

                                <label for="cluster_desc_0">Description:</label>
                                <input type="text" name="descs[]" id="cluster_desc_0" required>
                            </div>
                        </div>
                    </div>

                    <div class="button-row">
                        <button type="button" class="btn-secondary" onclick="addCluster()">Add Another Cluster</button>
                    </div>

                    <div class="section-title">Database Configuration</div>
                    <div class="setup-grid">
                        <label for="database">Database Name:</label>
                        <input type="text" name="database" id="database" placeholder="database_name" required>

                        <label for="host">Host:</label>
                        <input type="text" name="host" id="host" placeholder="localhost" required>

                        <label for="user">Username:</label>
                        <input type="text" name="user" id="user" placeholder="username" required>

                        <label for="password">Password:</label>
                        <input type="password" name="password" id="password" placeholder="password" required>
                    </div>

                    <div class="button-row">
                        <input type="submit" class="btn-primary" value="Submit">
                    </div>
                </form>
            </div>
        </div>
    </div>
</body>
</html>
