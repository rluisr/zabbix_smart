<?php
/**
 * SMARTで異常があるかどうか調べる。
 * ( This script checks S.M.A.R.T for Zabbix. )
 *
 * === Setting ===
 * [zabbix_agentd.conf]
 * + UserParameter=hdd.smart[*],/usr/bin/php /path/to/smart.php $1 $2 $3
 * + AllowRoot=1
 *
 * === $1 - mode = 0 or 1 ===
 * [mode = 0]
 * １つの要素の値だけが欲しい場合
 * ( If you want one value. )
 *
 * = Example =
 * hdd.smart[0,sda,Temp]
 * Output : 44
 *
 * [mode = 1]
 * HDDに異常があるかどうかだけを知りたい場合
 * ( If you want to check all value. )
 *
 * 出力が
 * ( Output is
 * 0 = 正常 ( check ok )
 * 1 = 異常 ( unusual )
 *
 * = Example =
 * hdd.smart[1,sda]
 * Output : 0
 *
 * サーバー側で
 * (Test your zabbix server )
 *
 * zabbix_get -s <agent ip> -k hdd.smart[0,sda,Temp]
 * or
 * zabbix_get -s <agent ip> -k hdd.smart[1,sda]
 *
 * を投げて数値が返ってきたら正常に動作しています。
 * ( If you can get outputs, this script is working. )
 *
 * 見てる項目は Reallocated_Event_Count, Current_Pending_Sector
 * どれか１つでも | Worst or Value > Threshold | となっていれば異常と判断
 * ( This plugin see 'Reallocated_Event_Count, Current_Pending_Sector'.
 * Worst or Value > Threshold is ununsual. )
 *
 * https://luispc.com/
 */
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