<?php
$fp = popen('top -b -n 2 | grep -E "(Cpu|Mem)"',"r");//获取某一时刻系统cpu和内存使用情况
$rs = "";
while(!feof($fp)){
    $rs .= fread($fp,1024);
}
pclose($fp);
echo $rs.'<br>';
preg_match_all("/Cpu.*us\,/", $rs,$cpus);
var_dump($cpus[1]);
echo '<br>';
preg_match('/(\d|\.)+/', $cpus[1], $cpu); //cpu使用百分比
var_dump($cpu);
echo '<br>';
preg_match_all('/ \d+ used/', $rs,$cmems);
var_dump($cmems[3]);
echo '<br>';
preg_match('/\d+/', $cmems[3],$cmem); //内存使用量 k
var_dump($cmem);
$log = "$cpu[0]--$cmem[0],\r\n";
echo $log;
$logres = file_put_contents('./yali.log',$log,FILE_APPEND);