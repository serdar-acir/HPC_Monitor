#!/bin/bash
####################################
##written by: serdar acir
##serdar.acir@sabanciuniv.edu
####################################
#if [ -z "$HOME_IP" ]; then HOME_IP=$1; fi
HOME_IP=$1;
home_nw_speed=$(iperf3 -c $HOME_IP -t 5)
thatLine=`echo "${home_nw_speed}" | sed -n '7 p'`
echo "DELIMITORX $thatLine"
