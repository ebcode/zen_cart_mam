<?php

ini_set('error_log', 'newsletter_log');
error_reporting(0);
ini_set('display_errors', 0);
//error_reporting(E_ALL);


//echo "?";

touch('cart_admin/tmp/cron_log');
$fh_log = fopen('cart_admin/tmp/cron_log', 'a+');

//if file doesn't exist exit
if(!file_exists('cart_admin/tmp/news_up')){
    fwrite($fh_log, "cart_admin/tmp/news_up does not exist\n");
    //don't write anything to the log, this will run often
    // and will just spam the log
    fclose($fh_log);
    exit;
}


$file = file_get_contents('cart_admin/tmp/news_up');

$file_vars = explode("\n", $file);

//$file_vars[0] = first argument to limit
//$file_vars[1] = newsletter id
//$file_vars[2] = timestamp

$limit = $file_vars[0];
$newsletter_id = $file_vars[1];
$audience_selected = $file_vars[2];
$last_run_timestamp = $file_vars[3];
$num_emails_sent = isset($file_vars[4])?$file_vars[4]:0;


$increment = 20;  //sending 20 per minute

//only run if a certain time has passed since the last send
$time = time();

if($time - $last_run_timestamp > 30){ //wait one minute at least
    //run 

    $date = date("F j, Y, g:i a"); 

    fwrite($fh_log, "sending emails : $date \n");
    
    @unlink('cart_admin/tmp/news_up'); //remove file

    define('IS_ADMIN_FLAG',1);

    require_once('cart_admin/includes/application_top.php');
    
    ini_set('error_log', 'newsletter_log');
    ini_set('display_errors', 0);
    error_reporting(E_ALL);
    
    require_once('cart_admin/includes/classes/object_info.php');
    
    require_once('includes/functions/audience.php');
    
    //define ('ADMIN_EXTRA_EMAIL_FORMAT', 'HTML');
    //define ('EMAIL_USE_HTML', 'true');

/*
require_once('cart_admin/newsletters.php');
*/

//echo "!";

//$nID = zen_db_prepare_input($_GET['nID']);

//$nID = '5';
    
    //echo "<br> newsletter_id = $nID";

   
    
    
    $rs = mysql_query("select newsletters_id, title, content, content_html, module                                from " . TABLE_NEWSLETTERS . "                               where newsletters_id = '" . (int)$newsletter_id . "'") or die(mysql_error());

    $row = mysql_fetch_array($rs);

    //echo "<br> html = " .$row['content_html']; 


    $newsletter = $db->Execute("select newsletters_id, title, content, content_html, module                                from " . TABLE_NEWSLETTERS . "                               where newsletters_id = '" . (int)$newsletter_id . "'");
    

    $nInfo = new objectInfo($newsletter->fields);
/*
include(DIR_WS_LANGUAGES . $_SESSION['language'] . '/modules/newsletters/' . $nInfo->module . substr($PHP_SELF, strrpos($PHP_SELF, '.')));
include(DIR_WS_MODULES . 'newsletters/' . $nInfo->module . substr($PHP_SELF, strrpos($PHP_SELF, '.')));
*/
    //$module_name = $nInfo->module;

    //$module = new $module_name($nInfo->title, $nInfo->content, $nInfo->content_html, 'elibird');

    //$i = $module->send($nInfo->newsletters_id);

    //$audience_select = get_audience_sql_query('elibird', 'newsletters');
      
    //var_dump($audience_select);

    $headers = "From: Anything In A Basket <sales@anythinginabasket.com>\r\n";
    $headers .= "Reply-To: sales@anythinginabasket.com\r\n";
    $headers .= "MIME-Version: 1.0\r\n";
    $headers .= "Content-Type: text/html; charset=ISO-8859-1\r\n";
    

//need to replace this with custom query so that I can 
// fill in the LIMIT statement with variables.
//$audience = $db->Execute($audience_select['query_string']);

//get file contents set in cart_admin/newsletters.php


//where active='1'
//custom query 
    //grab query from query_builder
    
    $q = "select query_string from query_builder where query_name = '$audience_selected'";
        
    //echo "<br>" .  __LINE__ . " ran q: $q<br>";

//fwrite($fh_log, "\n" . __LINE__ . " ran the query: $q\n");    

$query = $db->Execute( $q );

$query_string = $query->fields['query_string'];

$q = $query_string . " limit $limit,$increment";

//echo "<br>" .  __LINE__ . " ran q: $q<br>";

//fwrite($fh_log, "\n" . __LINE__ . " ran the query: $q\n"); 

/*
    $q = "select * from `newsletter_emails` where active='1'  order by email_id limit $limit,$increment ";
  */
      
$audience = $db->Execute( $q );
    fwrite($fh_log, "ran the query: $q\n");    


      $records = $audience->RecordCount();
      
    /* if ($records==0) return 0; */
    //when no records are returned, we've reached the end of the list.
    // remove the file and exit
        if ($records==0){
            fwrite($fh_log, "End of records, sent all emails\n");    
            @unlink('cart_admin/tmp/news_up');
            exit;
        }

    $i=0;

      while (!$audience->EOF) {
            $i++;
            //sleep(20);  //throttle email to send less than 250 emails per hour,
                        // per inmotionhosting limit
            //sleep(18);
            
          //$html_msg['EMAIL_FIRST_NAME'] = $audience->fields['customers_firstname'];
          //$html_msg['EMAIL_LAST_NAME']  = $audience->fields['customers_lastname'];
          $html_msg['EMAIL_MESSAGE_HTML'] = $nInfo->content_html;
          //zen_mail($audience->fields['customers_firstname'] . ' ' .
		// $audience->fields['customers_lastname'], $audience->fields['customers_email_address'],
		// $this->title, $this->content, STORE_NAME, EMAIL_FROM, $html_msg, 'newsletters');
            
            //echo "<BR> content_html = " . $nInfo->content_html;
        
            //don't mail just yet
        
            mail($audience->fields['customers_email_address'], $nInfo->title, $row['content_html'], $headers);
       /* 
	zen_mail($audience->fields['customers_firstname'] . ' ' .
                 $audience->fields['customers_lastname'], $audience->fields['customers_email_address'], 
                 $nInfo->title, $row['content_html'], STORE_NAME, EMAIL_FROM, $row['content_html'], 'newsletters');
*/

            fwrite($fh_log, "sent: {$audience->fields['customers_email_address']}\n");
            fwrite($fh_log, "# ". ($num_emails_sent+$i). "\n");
            /*
                zen_mail($audience->fields['customers_firstname'] . ' ' . $audience->fields['customers_lastname'], $audience->fields['customers_email_address'], $nInfo->title, $row['content_html'], STORE_NAME, EMAIL_FROM, $html_msg, 'newsletters');
              */  
              //echo zen_image(DIR_WS_ICONS . 'tick.gif', $audience->fields['customers_email_address']);
        
              //force output to the screen to show status indicator each time a message is sent...
              if (function_exists('ob_flush')) @ob_flush();
              @flush();
        
              $audience->MoveNext();
          }
          
    //after we've sent the emails, write the log
    // ... (no error checking, if we can't write to the log, we're screwed)
    //
    
    touch('cart_admin/tmp/news_up');
    $fh = fopen('cart_admin/tmp/news_up', 'w+');
    fwrite($fh, ($limit+$increment)."\n".$newsletter_id."\n".$audience_selected."\n".time()."\n".($num_emails_sent+$i));


        /*
      $newsletter_id = zen_db_prepare_input($newsletter_id);
      $db->Execute("update " . TABLE_NEWSLETTERS . "
                    set date_sent = now(), status = '1'
                    where newsletters_id = '" . zen_db_input($nID) . "'");
        */


} else {
    //dont run
    fwrite($fh_log, "not enough time passed \n");
    //echo "<br>not enough time passed";
}


fclose($fh_log);

//echo "!";
