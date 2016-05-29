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

/**
 * 現在の値またはワースト値が閾値を下回ることがあれば、データのバックアップやハードディスクの交換など必要な処置
 * 「現在の値」(Value)、「閾値」(Threshold)、「ワースト値」(Worst)
 */
error_reporting(0);

/* Mode */
$mode = $argv[0];
/* Device */
$dev = $argv[2];
/* item */
$item = $argv[3];

if ($mode == 0) {
    $exec = exec("smartctl -A /dev/{$dev} | grep $item | awk '{print $10}'");
    echo $exec;
    exit;

} elseif ($mode == 1) {
    /* Reallocated Sectors Count */
    $RSC = exec("smartctl -A /dev/{$dev} | grep 'Reallocated_Sector_Ct' | awk '{print $10}'");

}