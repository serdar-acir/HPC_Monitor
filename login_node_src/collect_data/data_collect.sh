#!/bin/bash
####################################
##written by: serdar acir
##serdar.acir@sabanciuniv.edu
####################################

source ../HPC1.config
COLLECT_DIR="$HOME/HPC_Monitor/root_version"
mkdir -p $COLLECT_DIR

collect_data() {
    local node=$1
    echo "Collecting data from: $node"
    ssh root@${node} "inxi -M > ${COLLECT_DIR}/TosunHPC_${node}_0_machine.txt"
    ssh root@${node} "inxi -C > ${COLLECT_DIR}/TosunHPC_${node}_1_cpu.txt"
    ssh root@${node} "inxi -m > ${COLLECT_DIR}/TosunHPC_${node}_2_memory.txt"
    ssh root@${node} "inxi -Gx > ${COLLECT_DIR}/TosunHPC_${node}_3_graphics.txt"
    ssh root@${node} "inxi -i > ${COLLECT_DIR}/TosunHPC_${node}_4_network.txt"
    ssh root@${node} "inxi -D > ${COLLECT_DIR}/TosunHPC_${node}_5_drives.txt"
    ssh root@${node} "inxi -p > ${COLLECT_DIR}/TosunHPC_${node}_6_partition.txt"
    ssh root@${node} "inxi -o > ${COLLECT_DIR}/TosunHPC_${node}_7_unmounted.txt"
    ssh root@${node} "inxi -L > ${COLLECT_DIR}/TosunHPC_${node}_8_logical.txt"
    ssh root@${node} "inxi -R > ${COLLECT_DIR}/TosunHPC_${node}_9_raid.txt"
    ssh root@${node} "inxi -S > ${COLLECT_DIR}/TosunHPC_${node}_10_system.txt"
    ssh root@${node} "inxi --slots > ${COLLECT_DIR}/TosunHPC_${node}_11_pci-slots.txt"
}

echo "Collecting data from login node"
inxi -M > ${COLLECT_DIR}/TosunHPC_login_0_machine.txt
inxi -C > ${COLLECT_DIR}/TosunHPC_login_1_cpu.txt
inxi -m > ${COLLECT_DIR}/TosunHPC_login_2_memory.txt
inxi -Gx > ${COLLECT_DIR}/TosunHPC_login_3_graphics.txt
inxi -i > ${COLLECT_DIR}/TosunHPC_login_4_network.txt
inxi -D > ${COLLECT_DIR}/TosunHPC_login_5_drives.txt
inxi -p > ${COLLECT_DIR}/TosunHPC_login_6_partition.txt
inxi -o > ${COLLECT_DIR}/TosunHPC_login_7_unmounted.txt
inxi -L > ${COLLECT_DIR}/TosunHPC_login_8_logical.txt
inxi -R > ${COLLECT_DIR}/TosunHPC_login_9_raid.txt
inxi -S > ${COLLECT_DIR}/TosunHPC_login_10_system.txt
inxi --slots > ${COLLECT_DIR}/TosunHPC_login_11_pci-slots.txt

for node in "${node_array[@]}"; do
    collect_data $node
done

scp ${COLLECT_DIR}/*.txt root@${mysql_host}:/var/www/html/run_as_root/
/bin/rm -f ${COLLECT_DIR}/*.txt
ssh root@${mysql_host} "touch /var/www/html/run_as_root/do_all"

