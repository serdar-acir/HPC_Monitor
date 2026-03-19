#!/bin/bash
####################################
## written by: serdar acir
## serdar.acir@sabanciuniv.edu
####################################

# Ensures the script is run with Bash
if [ -z "$BASH_VERSION" ]; then
    echo "This script requires Bash to run."
    exit 1
fi

config_file=(../*.config)
first_config="${config_file[0]}"
if [ ! -f "$first_config" ]; then
    echo "Config file not found."
    exit 1
fi

# Execute PHP script to extract cluster name and node array
IFS=$'\n' read -r -d '' -a output < <(php extract_config.php || echo "Failed to execute PHP.")
cluster_name="${output[0]}"  # First line is cluster name
node_array=("${output[@]:1}")  # Remaining lines are node names

collect_data() {
    local node=$1
    echo "Collecting data from: $node"
    if [[ "$node" =~ ^(login|headnode|monitor)$ ]]; then
        # Run commands directly for specific nodes
        inxi -M > "${cluster_name}_${node}_0_machine.txt"
        inxi -C > "${cluster_name}_${node}_1_cpu.txt"
        inxi -m > "${cluster_name}_${node}_2_memory.txt"
        inxi -Gx > "${cluster_name}_${node}_3_graphics.txt"
        inxi -i > "${cluster_name}_${node}_4_network.txt"
        inxi -D > "${cluster_name}_${node}_5_drives.txt"
        inxi -p > "${cluster_name}_${node}_6_partition.txt"
        inxi -o > "${cluster_name}_${node}_7_unmounted.txt"
        inxi -L > "${cluster_name}_${node}_8_logical.txt"
        inxi -R > "${cluster_name}_${node}_9_raid.txt"
        inxi -S > "${cluster_name}_${node}_10_system.txt"
        inxi --slots > "${cluster_name}_${node}_11_pci-slots.txt"
    else
        # Use SSH for other nodes
ssh root@"$node" "inxi -M" > "${cluster_name}_${node}_0_machine.txt"
ssh root@"$node" "inxi -C" > "${cluster_name}_${node}_1_cpu.txt"
ssh root@"$node" "inxi -m" > "${cluster_name}_${node}_2_memory.txt"
ssh root@"$node" "inxi -Gx" > "${cluster_name}_${node}_3_graphics.txt"
ssh root@"$node" "inxi -i" > "${cluster_name}_${node}_4_network.txt"
ssh root@"$node" "inxi -D" > "${cluster_name}_${node}_5_drives.txt"
ssh root@"$node" "inxi -p" > "${cluster_name}_${node}_6_partition.txt"
ssh root@"$node" "inxi -o" > "${cluster_name}_${node}_7_unmounted.txt"
ssh root@"$node" "inxi -L" > "${cluster_name}_${node}_8_logical.txt"
ssh root@"$node" "inxi -R" > "${cluster_name}_${node}_9_raid.txt"
ssh root@"$node" "inxi -S" > "${cluster_name}_${node}_10_system.txt"
ssh root@"$node" "inxi --slots" > "${cluster_name}_${node}_11_pci-slots.txt"
    fi
}

for node in "${node_array[@]}"; do
    collect_data "$node"
done

