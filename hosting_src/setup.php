<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>HPC Setup</title>
    <script>
        function addCluster() {
            var container = document.getElementById("cluster-container");
            var clusterCount = container.getElementsByClassName("cluster-group").length;
            
            var newClusterGroup = document.createElement("div");
            newClusterGroup.className = "cluster-group";
            newClusterGroup.innerHTML = `
                <label for="cluster_name_${clusterCount}">HPC Cluster Name:</label>
                <input type="text" name="clusters[]" id="cluster_name_${clusterCount}" required>
                <label for="cluster_desc_${clusterCount}">Description:</label>
                <input type="text" name="descs[]" id="cluster_desc_${clusterCount}" required>
            `;
            container.appendChild(newClusterGroup);
        }
    </script>
</head>
<body>
    <h2>HPC Setup</h2>
    <form action="setup_f.php" method="post">
        <label for="timezone">Default Timezone:</label>
        <input type="text" name="timezone" id="timezone" value="Europe/Istanbul" required><br><br>

        <div id="cluster-container">
            <div class="cluster-group">
                <label for="cluster_name_0">HPC Cluster Name:</label>
                <input type="text" name="clusters[]" id="cluster_name_0" required>
                <label for="cluster_desc_0">Description:</label>
                <input type="text" name="descs[]" id="cluster_desc_0" required>
            </div>
        </div>
        <button type="button" onclick="addCluster()">Add Another Cluster</button><br><br>

        <h3>Database Configuration</h3>
        <label for="database">Database Name:</label>
        <input type="text" name="database" id="database" placeholder="database_name" required><br><br>
        
        <label for="host">Host:</label>
        <input type="text" name="host" id="host" placeholder="localhost" required><br><br>
        
        <label for="user">Username:</label>
        <input type="text" name="user" id="user" placeholder="username" required><br><br>
        
        <label for="password">Password:</label>
        <input type="password" name="password" id="password" placeholder="password" required><br><br>
        
        <input type="submit" value="Submit">
    </form>
</body>
</html>

