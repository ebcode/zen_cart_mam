<?php



require('includes/application_top.php');

error_reporting(E_ALL);
ini_set('display_errors', 1);

$limit = isset($_GET['p'])?(int)$_GET['p']:0;

$limit = $limit*10;

$end_limit = $limit + 10;

//get count 
$q1 = "select count(*) from order_logs";

$rs1 = mysql_query($q1) or die(mysql_error());

$row = mysql_fetch_array($rs1);
$count = $row[0];
//echo "count= $count";

$pages = floor($count/10);

echo "Pages: ";
for($i=0;$i<$pages;$i++){
    $link = "./show_log.php?p=".$i;
    echo '<a href="'.$link.'">'.($i+1).'</a> | ';
}

$q = "select * from order_logs order by order_log_id desc limit $limit, $end_limit"; 

echo "q = $q<br>";

$rs = mysql_query($q) or die(mysql_error());

$i=0;
while ($row = mysql_fetch_array($rs)){
    
echo "<table>";

    $log = (unserialize($row['order_log']));
/*
    echo "<br>" . print_r($log);
    echo "<br>";
*/
    echo "<br>record# " . $row['order_log_id'] ."<br><table border=1>";
    
    //customer id
    echo "<tr><td>customer id</td><td>{$row['customers_id']}</td></tr>";
    foreach($log as $k => $v){
        
        if(is_array($v)){
            echo "is array";
            echo "<tr><td>". $k . "</td></tr>";
            //write_table($v);
            
        } else if(is_object($v)){
            echo "is object";
            //$arr = get_object_vars($v);
            //write_table($arr);
            
        } else {
            echo "<tr><td>$k</td><td>$v</td></tr>";
        }
    }

    echo "</table>";
}


function write_table($s){
    echo "write_table function";
    foreach($s as $k => $v){
        if(is_array($v)){
            echo "<tr><td>". $k . "</td></tr>";
            write_table($v);
        } else {
            echo "<tr><td>$k</td><td>$v</td></tr>";
        }
    }

}