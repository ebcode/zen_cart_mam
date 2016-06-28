<?php
error_reporting(E_ALL);
ini_set('display_errors',true);
ini_set('max_execution_time', 36000);

/*
$t = 1000;

for($i=0; $i<$t; $i++){


mail('elibird@gmail.com', 'test', 'hey');
echo '<br> s. ' . $i;

if (function_exists('ob_flush')) @ob_flush();
@flush();

sleep(4);

}
*/
$x = `pwd`;
$x = trim($x);
touch($x.'/news_up');

$fh2 = fopen($x.'/cart_admin/okay/news_up', 'w+') or die('could not open ' . $x.'/tmp/news_up');;
    fwrite($fh2, "0\n"."\n".time());
    
    echo " The newsletter has been scheduled for sending!!";
    fclose($fh2);