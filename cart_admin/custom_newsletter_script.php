<?php

ini_set('error_log', 'newsletter_log');
ini_set('display_errors', E_ALL);
error_reporting(true);



define('IS_ADMIN_FLAG',1);

require('newsletters.php');

if(file_exists('news_up')){
    echo "send!";
}