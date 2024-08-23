#!/bin/bash
####################################
##written by: serdar acir
##serdar.acir@sabanciuniv.edu
####################################
echo $[100-$(vmstat 1 2|tail -1|awk '{print $15}')]
