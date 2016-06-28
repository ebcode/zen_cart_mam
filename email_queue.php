<?php

/**
 * Load common library stuff 
 */
require('includes/application_top.php');
require('includes/languages/english/checkout_process.php');
error_reporting(E_ALL);
ini_set('display_errors', 1);

//zen_mail('eli bird', 'elibird@gmail.com', 'order confirmation test', 'order text', 'Anything', 'sales@anythinginabasket.com', array(), 'checkout', array());
$a = array();

echo "<br> opening: " .  DIR_FS_CATALOG.'email_queue' . "<br>";

if ($handle = opendir(DIR_FS_CATALOG.'email_queue')) {
    echo "Directory handle: $handle\n";
    echo "Entries:\n";

    /* This is the correct way to loop over the directory. */
    while (false !== ($entry = readdir($handle))) {
        
        if($entry != '.' && $entry!='..'){
        echo "$entry\n";
            require('email_queue/'.$entry);
            @unlink('email_queue/'.$entry);
        }
        
    }

    echo "<br> emails array to send: <br>";
    echo "<pre>";
    var_dump($a);
    echo "</pre>";

    foreach($a as $v){
      
        $email_array = unserialize($v);

        zen_mail($email_array[0], $email_array[1], $email_array[2], $email_array[3], $email_array[4], $email_array[5], $email_array[6], $email_array[7], $email_array[8]);
              
         echo "<pre>";
        var_dump($email_array);
        echo "</pre>";
        
    }

    closedir($handle);
}