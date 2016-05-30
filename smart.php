<?php
error_reporting(0);

/* Mode */
$mode = $argv[1];
/* Device */
$dev = $argv[2];
/* item */
$item = $argv[3];

if ($mode == 0) {
    $exec = exec("smartctl -A /dev/{$dev} | grep '{$item}' | awk '{print $10}'");
    echo $exec;
    exit;

} elseif ($mode == 1) {
    /* Reallocated Sectors Count */
    $RSC_value = exec("smartctl -A /dev/{$dev} | grep 'Reallocated_Sector_Ct' | awk '{print $4}'");
    $RSC_worst = exec("smartctl -A /dev/{$dev} | grep 'Reallocated_Sector_Ct' | awk '{print $5}'");
    $RSC_thres = exec("smartctl -A /dev/{$dev} | grep 'Reallocated_Sector_Ct' | awk '{print $6}'");

    /* Current Pending Sector Count */
    $CPSC_value = exec("smartctl -A /dev/{$dev} | grep 'Current_Pending_Sector' | awk '{print $4}'");
    $CPSC_worst = exec("smartctl -A /dev/{$dev} | grep 'Current_Pending_Sector' | awk '{print $5}'");
    $CPSC_thres = exec("smartctl -A /dev/{$dev} | grep 'Current_Pending_Sector' | awk '{print $6}'");

    if ($RSC_thres >= $RSC_value || $RSC_thres >= $RSC_worst
        || $CPSC_thres >= $CPSC_value || $CPSC_thres >= $CPSC_worst
    ) {
        echo '-1';

    } else {
        echo '0';
    }
}