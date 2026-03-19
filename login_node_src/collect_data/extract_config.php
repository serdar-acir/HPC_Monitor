<?php
chdir(".."); // Change directory to access the config file correctly
$mydir = getcwd();
$allFiles = scandir($mydir); // Ensure you're scanning the correct directory

$cluster_name = ""; // Initialize to prevent undefined variable errors

// Loop through all files to find the first .config file
foreach ($allFiles as $file) {
    if (strstr($file, '.config')) {
        $cluster_name = substr($file, 0, -7); // Strip the last 7 characters to remove '.config'
        break; // Exit the loop once the first config file is found
    }
}

// Check if a valid cluster name was found before requiring the config file
if (!empty($cluster_name)) {
    require $cluster_name . ".config"; // Include the cluster-specific config file
} else {
    echo "No valid config file found.";
    exit; // Stop execution if no config file is found
}

chdir("collect_data"); // Change back to the original directory
echo $cluster_name . "\n";

// Check if $node_array is defined and output the node names
if (isset($node_array) && is_array($node_array)) {
    foreach ($node_array as $node) {
        echo $node . "\n";
    }
} else {
    echo "Node array not set or incorrect format.";
    exit; // Stop execution if node array is not correct
}
?>

