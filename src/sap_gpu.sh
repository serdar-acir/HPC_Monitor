#!/bin/bash
####################################
##written by: serdar acir
##serdar.acir@sabanciuniv.edu
####################################
gpus_available=$(nvidia-smi --query-gpu=name,utilization.gpu --format=csv)
if [[ $gpus_available == *"devices"* ]]; then
  gpus_available=$(ssh sacir@$HOSTNAME nvidia-smi --query-gpu=name,utilization.gpu --format=csv)
fi
echo "SERDAR $gpus_available"
