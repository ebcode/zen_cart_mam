<?php

require('includes/application_top.php');

$fh = fopen('contact_export_utf8.csv', 'r');

$i =0 ;
while($row = fgetcsv($fh, 9999, "\t")){

$i++;

if($i==1) continue;

/*
$f_name = mysql_real_escape_string($row[0]);
$l_name = mysql_real_escape_string($row[1]);
$email = mysql_real_escape_string($row[24]);
*/

//$f_name = ($row[0]);

$f_name = str_replace('"', "", $row[0]);

$f_name = str_replace("\0", "", $f_name);

$l_name = str_replace('"', "", $row[1]);

$l_name = str_replace("\0", '', $l_name);

$l_name = str_replace("'", "\'", $l_name);

$email = str_replace('"', "", $row[24]);

$email = str_replace("\0", '', $email);

if(strpos($email,'@') == false){ //try other column
    echo "<br> email at Y = ''";
    //$email = mysql_real_escape_string($row[4]);
    $email = str_replace('"', "", $row[4]);
    $email = str_replace("\0", '', $email);
    //var_dump($row);
    echo "<br> new email = '$email'";
}

if(trim($row[5]) == 'Unsubscribed') continue; //don't add unsubscribed users

//echo "<br>$f_name $email";

$q = "insert into newsletter_emails (fname, lname, email) values ('$f_name','$l_name','$email')";

echo "<br>row: $i ::  q = $q";

mysql_query($q) or die(mysql_error());


//if($i > 10) break;


}

echo "<br>OK";