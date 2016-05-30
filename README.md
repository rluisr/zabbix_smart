S.M.A.R.T-for-Zabbix
====================
Zabbix で S.M.A.R.T を監視するスクリプト  
こういうのシェルスクリプトでもできるんだろうなぁ... 分からねえなあ...

## Description
Zabbix で S.M.A.R.T の値は見れてもそれが異常か判断か判別するスクリプトです。  
多くの HDD にはあるであろう __Reallocated_Event_Count__ , __Current_Pending_Sector__ を見て  
__Worst__ or __Value__ __>__ __Threshold__ となっていれば異常と判断するようにしてます。

## Requirement 
* PHP => 5.0.0  
* smartmontools

## Usage
* $1 = 0 or 1  
 * 0 = 値を１つ取得する  
 * 1 = 総合的に判断して異常か判断する
* $2 = ex) sda
* $3 = Temp, Current_Pending_Sector etc...(smartctlで取得できるものが使えます)

## Mode
$1 = 1 のとき出力された数字が  
0 = 正常  
-1 = 異常  
となります。

## Setup
#### zabbix_agentd.conf
    +UserParameter=hdd.smart[*],/usr/bin/php /path/to/smart.php $1 $2 $3
    +AllowRoot=1

#### Test ( on Zabbix server )
    $ zabbix_get -s <ip> -k hdd.smart[0,sda,Temp]
    $ 44
    
    $ zabbix_get -s <ip> -k hdd.smart[1,sda]
    $ 0

## Licence

[MIT](https://github.com/tcnksm/tool/blob/master/LICENCE)

## Author

[rluisr](https://github.com/rluisr)