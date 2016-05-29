<?php

$dev = $argv[1];
$smart = exec("smartctl -a ${dev}", $array_result);

foreach ($array_result as $row) {
    if (strpos($row, "Reallocated_Event_Count") !== false) {
        $rec = $row;
        break;
    }
}

$rec = explode("|", preg_replace("/\\s/", "|", $rec));
$rec_value = (int)$rec[5];
$rec_thresh = (int)$rec[11];
echo $rec_value <= $rec_thresh ? -1 : 0;