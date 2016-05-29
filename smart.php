<?php
/**
 * SMARTで異常があるかどうか調べる。
 * Zabbixで使いやすいように -1 or 0 で出力するようにしてます。
 *
 * Zabbix_agentd.conf
 * UserParameter=hdd.smart[*],/bin/php /zabbix_smart/smart.php $1
 *
 * サーバー側で zabbix_get -s <エージェントIP> -k hdd.smart[sda] を投げて
 * -1 or 0 が返ってくれば正常に動作してます。
 *
 * 見てる項目は Reallocated_Event_Count, Offline_Uncorrectable, Current_Pending_Sector
 * どれか１つでも、THRESH > VALUE となっていれば異常と判断
 * -1 なら 異常あり
 *  0 なら 問題なし
 *
 * https://luispc.com/
 */
error_reporting(0);

$dev = $argv[1];
$smart = exec("smartctl -a /dev/${dev}", $array_result);

foreach ($array_result as $row) {
    if (strpos($row, "Reallocated_Event_Count") !== false) {
        $rec = $row;
    } elseif (strpos($row, "Offline_Uncorrectable") !== false) {
        $ou = $row;
    } elseif (strpos($row, "Current_Pending_Sector") !== false) {
        $cps = $row;
    }
    if (isset($rec) || isset($ou) && isset($cps)) {
        break;
    }
}

$rec = explode("|", preg_replace("/\\s/", "|", $rec));
$rec_value = (int)$rec[5];
$rec_thresh = (int)$rec[11];

$ou = explode("|", preg_replace("/\\s/", "|", $ou));
$ou_value = (int)$ou[7];
$ou_thresh = (int)$ou[13];

$cps = explode("|", preg_replace("/\\s/", "|", $cps));
$cps_value = (int)$cps[6];
$cps_thresh = (int)$cps[12];

if ($rec_thresh >= $rec_value || $ou_thresh >= $ou_value || $cps_thresh >= $cps_value) {
    echo -1;
} else {
    echo 0;
}
