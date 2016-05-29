<?php

$dev = $argv[1];
$smart = exec("smartctl -a ${dev}", $array_result);

foreach ($array_result as $row) {
    if (strpos($row, "Offline_Uncorrectable") !== false) {
        $ou = $row;
        break;
    }
}

$ou = explode("|", preg_replace("/\\s/", "|", $ou));
$ou_value = (int)$ou[7];
$ou_thresh = (int)$ou[13];
echo $ou_result = $ou_value <= $ou_thresh ? -1 : 0;