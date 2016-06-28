<?php
/**
 * Header code file for the Account Newsletters page - To change customers Newsletter options
 *
 * @package page
 * @copyright Copyright 2003-2006 Zen Cart Development Team
 * @copyright Portions Copyright 2003 osCommerce
 * @license http://www.zen-cart.com/license/2_0.txt GNU Public License V2.0
 * @version $Id: header_php.php 3162 2006-03-11 01:39:16Z drbyte $
 */
if (!$_SESSION['customer_id']) {
  $_SESSION['navigation']->set_snapshot();
  zen_redirect(zen_href_link(FILENAME_LOGIN, '', 'SSL'));
}

require(DIR_WS_MODULES . zen_get_module_directory('require_languages.php'));

$newsletter_query = "SELECT customers_newsletter
                     FROM   " . TABLE_CUSTOMERS . "
                     WHERE  customers_id = :customersID";

$newsletter_query = $db->bindVars($newsletter_query, ':customersID',$_SESSION['customer_id'], 'integer');
$newsletter = $db->Execute($newsletter_query);

if (isset($_POST['action']) && ($_POST['action'] == 'process')) {
  if (isset($_POST['newsletter_general']) && is_numeric($_POST['newsletter_general'])) {
    $newsletter_general = zen_db_prepare_input($_POST['newsletter_general']);
  } else {
    $newsletter_general = '0';
  }

  if ($newsletter_general != $newsletter->fields['customers_newsletter']) {
    $newsletter_general = (($newsletter->fields['customers_newsletter'] == '1') ? '0' : '1');

    $sql = "UPDATE " . TABLE_CUSTOMERS . "
            SET    customers_newsletter = :customersNewsletter
            WHERE  customers_id = :customersID";

    $sql = $db->bindVars($sql, ':customersID',$_SESSION['customer_id'], 'integer');
    $sql = $db->bindVars($sql, ':customersNewsletter',$newsletter_general, 'integer');
    $db->Execute($sql);
  }
    //--elibird 9/28/2014
  //get email for this account to update newsletter_emails table
  $email_query = "select customers_email_address from ".TABLE_CUSTOMERS . " where customers_id = '".$_SESSION['customer_id']."'";

    if ($email = $db->Execute($email_query))
    {
        $email_address = $email->fields['customers_email_address'];
        $db->Execute("update newsletter_emails set active = '$newsletter_general' where email = '$email_address'");
        
         $email_in_table = $db->Execute("select email from newsletter_emails 
                                where email = '$email_address'");

        if($email_in_table->RecordCount() == 0){ //if email not in newsletter_emails table, add it
            $fname = mysql_real_escape_string($_SESSION['customer_first_name']);
            $lname = mysql_real_escape_string($_SESSION['customer_last_name']);

            $db->Execute("insert into newsletter_emails (email, active, fname, lname) values  ('$email_address', '$newsletter_general', '$fname', '$lname')");
        }

    } 
    


  $messageStack->add_session('account', SUCCESS_NEWSLETTER_UPDATED, 'success');

  zen_redirect(zen_href_link(FILENAME_ACCOUNT, '', 'SSL'));
}

$breadcrumb->add(NAVBAR_TITLE_1, zen_href_link(FILENAME_ACCOUNT, '', 'SSL'));
$breadcrumb->add(NAVBAR_TITLE_2);
?>
