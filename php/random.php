<?php 




/**
 * 生成19位数的数字ID
 * 
 * 最垃圾的写法
 * (此乃本人2015-2016年所用写法)
 * 
 * @return [type] [description]
 */
function random19()
{
    $rec_id = 0;
    $rArr = range(0, 9);
    shuffle($rArr);

    $mtimeArr = explode(' ', microtime());
    $rec_id = $mtimeArr[1] . substr($mtimeArr[0], 2, 6) . $rArr[array_rand($rArr)] . $rArr[array_rand($rArr)] . $rArr[array_rand($rArr)];

    echo $rec_id;


}



