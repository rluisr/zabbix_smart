<?php

$dev = $argv[1];
$smart = exec("smartctl -a ${dev}", $array_result);

foreach ($array_result as $row) {
    if (strpos($row, "Current_Pending_Sector") !== false) {
        $cps = $row;
        break;
    }
}

$cps = explode("|", preg_replace("/\\s/", "|", $cps));
$cps_value = (int)$cps[6];
$cps_thresh = (int)$cps[12];
echo $cps_result = $cps_value <= $cps_thresh ? -1 : 0;