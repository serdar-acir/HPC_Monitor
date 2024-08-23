#!/bin/bash
####################################
##written by: serdar acir
##serdar.acir@sabanciuniv.edu
####################################
top_processes=$(ps --sort=-%cpu -eo user,s,%cpu,%mem,comm,cmd | head -n 3 2>&1)
thatLine=`echo "${top_processes}"`
echo "DELIMITORX $thatLine"
