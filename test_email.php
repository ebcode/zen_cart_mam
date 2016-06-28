<?php

ini_set('display_errors',1);
error_reporting(E_ALL);

require('includes/application_top.php');

require_once('includes/functions/functions_email.php');


zen_mail('Eli bird ' , 'elibird@gmail.com', 'subject', 'test', 'store name', 'sales@anythinginabasket.com', '<a href="">test</a>', 'newsletters');

echo "<br> ok ";