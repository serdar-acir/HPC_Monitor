#!/bin/bash
####################################
##written by: serdar acir
##serdar.acir@sabanciuniv.edu
####################################
mem_available=$(free | awk '{print $7}' | tail -2 | sed '2d' |paste -sd+ |bc)
mem_total=$(free | awk '{print $2}' | tail -2 | sed '2d' |paste -sd+ |bc)
swap_used=$(free | awk '{print $3}' | tail -3 | sed '2d' |paste -sd+ |bc)
echo "SERDAR $mem_available $mem_total $swap_used"
