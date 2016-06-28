<?php

//error_reporting(E_ALL);
//ini_set('display_errors', 'On');

/**
 * checkout_success header_php.php
 *
 * @package page
 * @copyright Copyright 2003-2010 Zen Cart Development Team
 * @copyright Portions Copyright 2003 osCommerce
 * @license http://www.zen-cart.com/license/2_0.txt GNU Public License V2.0
 * @version $Id: header_php.php 16909 2010-07-15 20:02:37Z drbyte $
 */

//echo "?????";


// This should be first line of the script:
$zco_notifier->notify('NOTIFY_HEADER_START_CHECKOUT_SUCCESS');

// if the customer is not logged on, redirect them to the shopping cart page
if (!$_SESSION['customer_id']) {
  zen_redirect(zen_href_link(FILENAME_TIME_OUT));
}

if (!isset($_GET['action']) || (isset($_GET['action']) && $_GET['action'] != 'confirm')) {
    

/**
 * module to process a completed checkout
 *
 * @package procedureCheckout
 * @copyright Copyright 2003-2010 Zen Cart Development Team
 * @copyright Portions Copyright 2003 osCommerce
 * @license http://www.zen-cart.com/license/2_0.txt GNU Public License V2.0
 * @version $Id: checkout_process.php 17462 2010-09-05 06:23:35Z drbyte $
 */

//die('in checkout_process.php');
$time = microtime(true);

if (!defined('IS_ADMIN_FLAG')) {
  die('Illegal Access');
}

$zco_notifier->notify('NOTIFY_CHECKOUT_PROCESS_BEGIN');

require(DIR_WS_MODULES . zen_get_module_directory('require_languages.php'));

// if the customer is not logged on, redirect them to the time out page
  if (!$_SESSION['customer_id']) {
    zen_redirect(zen_href_link(FILENAME_TIME_OUT));
  } else {
    // validate customer
    if (zen_get_customer_validate_session($_SESSION['customer_id']) == false) {
      $_SESSION['navigation']->set_snapshot(array('mode' => 'SSL', 'page' => FILENAME_CHECKOUT_SHIPPING));
      zen_redirect(zen_href_link(FILENAME_LOGIN, '', 'SSL'));
    }
  }

// confirm where link came from
if (!strstr($_SERVER['HTTP_REFERER'], FILENAME_CHECKOUT_CONFIRMATION)) {
  //    zen_redirect(zen_href_link(FILENAME_CHECKOUT_PAYMENT,'','SSL'));
}

// BEGIN CC SLAM PREVENTION
if (!isset($_SESSION['payment_attempt'])) $_SESSION['payment_attempt'] = 0;
$_SESSION['payment_attempt']++;
$zco_notifier->notify('NOTIFY_CHECKOUT_SLAMMING_ALERT');
if ($_SESSION['payment_attempt'] > 3) {
  $zco_notifier->notify('NOTIFY_CHECKOUT_SLAMMING_LOCKOUT');
  $_SESSION['cart']->reset(TRUE);
  zen_session_destroy();
  zen_redirect(zen_href_link(FILENAME_TIME_OUT));
}
// END CC SLAM PREVENTION

if (!isset($credit_covers)) $credit_covers = FALSE;

// load selected payment module
require(DIR_WS_CLASSES . 'payment.php');
$payment_modules = new payment($_SESSION['payment']);

// load the selected shipping module

require(DIR_WS_CLASSES . 'shipping.php');
require(DIR_WS_CLASSES . 'order.php');
require(DIR_WS_CLASSES . 'order_total.php');

/*
$shipping_modules = new shipping($_SESSION['shipping']);
$order = new order;
$order_total_modules = new order_total;


// prevent 0-entry orders from being generated/spoofed
if (sizeof($order->products) < 1) {
  zen_redirect(zen_href_link(FILENAME_SHOPPING_CART));
}

$zco_notifier->notify('NOTIFY_CHECKOUT_PROCESS_BEFORE_ORDER_TOTALS_PRE_CONFIRMATION_CHECK');
if (strpos($GLOBALS[$_SESSION['payment']]->code, 'paypal') !== 0) {
  $order_totals = $order_total_modules->pre_confirmation_check();
}
if ($credit_covers === TRUE)
{
    $order->info['payment_method'] = $order->info['payment_module_code'] = '';
}
$zco_notifier->notify('NOTIFY_CHECKOUT_PROCESS_BEFORE_ORDER_TOTALS_PROCESS');
$order_totals = $order_total_modules->process();
$zco_notifier->notify('NOTIFY_CHECKOUT_PROCESS_AFTER_ORDER_TOTALS_PROCESS');

*/

//Multiple Addresses Mod



if(!isset($_SESSION['multiple_addresses'])){
$order = new order;

//Load the selected shipping module(needed to calculate tax correctly)
$shipping_modules = new shipping($_SESSION['shipping']);

$order_total_modules = new order_total;
$order_total_modules->collect_posts();
$order_total_modules->pre_confirmation_check();

$zco_notifier->notify('NOTIFY_CHECKOUT_PROCESS_BEFORE_ORDER_TOTALS_PROCESS');
$order_totals = $order_total_modules->process();
$zco_notifier->notify('NOTIFY_CHECKOUT_PROCESS_AFTER_ORDER_TOTALS_PROCESS');

//$order_totals_output[] = $order_total_modules->output();

} else {
$product_ids = array_keys($_SESSION['cart']->contents);
$_SESSION['cart']->save_contents();
  
//set all cart quantities to 0
foreach($_SESSION['cart']->contents as $k=>$v){
    $_SESSION['cart']->contents[$k]['qty']=0;
    //$v['qty']=1;
    //var_dump($v);
}

//$_SESSION['cart']->contents[$product_ids[0]]['qty']=2;

//var_dump($shipping_tmp); 

//$_SESSION['shipping']=$shipping_tmp;
$_SESSION['multiple_addresses_grand_total']=0;
$k=0;
 //using this every time:  -- maybe it should go in a class?

$zco_notifier->notify('NOTIFY_CHECKOUT_PROCESS_BEFORE_ORDER_TOTALS_PROCESS');

    foreach ($_SESSION['multiple_addresses'] as $product_id=>$address){
 
         //echo "<br>product_id=$product_id";
         
         $how_many = $address['how_many_addresses'];
         
         if ($how_many>1){
            for($i=0;$i<$how_many;$i++){
                $address_book_id = $address['addresses'][$i]['address_book_id'];
                $quantity_for_this_address = $address['addresses'][$i]['qty'];
                $shipping_cost = $address['addresses'][$i]['cost'];
                $shipping_method = $address['addresses'][$i]['shipping_method'];
                $shipping_module_code = $address['addresses'][$i]['shipping_module_code'];
                $shipping_method_title = $address['addresses'][$i]['shipping_method_title'];
                    
                $shipping_tmp = array('id'=>$shipping_module_code.'_'.$shiping_method,
                    'title'=>$shipping_method_title,
                    'cost'=>$shipping_cost,
                    'module'=>$shipping_module_code);
                
                $_SESSION['shipping']=$shipping_tmp;
                $_SESSION['sendto']=$address_book_id;
                $_SESSION['cart']->contents[$product_ids[$k]]['qty']=$quantity_for_this_address;
                
                $order = new order;
                $shipping_modules = new shipping($_SESSION['shipping']);
                
                $order_total_modules = new order_total;
                $order_total_modules->collect_posts();
                $order_total_modules->pre_confirmation_check();
                
                $order_totals[] = $order_total_modules->process();
                //$order_totals_output[] = $order_total_modules->output();
                
                //set quantity back to 0
                $_SESSION['cart']->contents[$product_ids[$k]]['qty']=0;
                
            }
         } else {
                $address_book_id = $address['addresses'][0]['address_book_id'];
                $quantity_for_this_address = $address['addresses'][0]['qty'];
                $shipping_cost = $address['addresses'][0]['cost'];
                $shipping_method = $address['addresses'][0]['shipping_method'];
                $shipping_module_code = $address['addresses'][0]['shipping_module_code'];
                $shipping_method_title = $address['addresses'][0]['shipping_method_title'];
                
                $shipping_tmp = array('id'=>$shipping_module_code.'_'.$shiping_method,
                    'title'=>$shipping_method_title,
                    'cost'=>$shipping_cost,
                    'module'=>$shipping_module_code);
                
                $_SESSION['shipping']=$shipping_tmp;
                $_SESSION['sendto']=$address_book_id;
                            
                $_SESSION['cart']->contents[$product_ids[$k]]['qty']=$quantity_for_this_address;
                
                $order = new order;
                $shipping_modules = new shipping($_SESSION['shipping']);
                
                $order_total_modules = new order_total;
                $order_total_modules->collect_posts();
                $order_total_modules->pre_confirmation_check();
                
                $order_totals[] = $order_total_modules->process();
                //$order_totals_output[] = $order_total_modules->output();
                
                $_SESSION['cart']->contents[$product_ids[$k]]['qty']=0;
         }
        $k++;
    }

$zco_notifier->notify('NOTIFY_CHECKOUT_PROCESS_AFTER_ORDER_TOTALS_PROCESS');
$_SESSION['cart']->revert_contents();

//recreate the order
$order = new order;

}

if (!isset($_SESSION['payment']) && $credit_covers === FALSE) {
  zen_redirect(zen_href_link(FILENAME_DEFAULT));
}

// load the before_process function from the payment modules
$payment_modules->before_process();
$zco_notifier->notify('NOTIFY_CHECKOUT_PROCESS_AFTER_PAYMENT_MODULES_BEFOREPROCESS');
// create the order record
$insert_id = $order->create($order_totals, 2);
$zco_notifier->notify('NOTIFY_CHECKOUT_PROCESS_AFTER_ORDER_CREATE');


if(isset($_SESSION['multiple_addresses'])){
//update the multiple_addresses table with this order_id
$q = "update ". TABLE_MULTIPLE_ADDRESSES_CUSTOMERS_BASKETS_PRODUCTS_ORDERS. 
     " set orders_id = '$insert_id' where customers_id = ".$_SESSION['customer_id']." and orders_id is NULL";
//die($q);

$db->Execute($q);  //Multiple Addresses Mod
}

$payment_modules->after_order_create($insert_id);
$zco_notifier->notify('NOTIFY_CHECKOUT_PROCESS_AFTER_PAYMENT_MODULES_AFTER_ORDER_CREATE');
// store the product info to the order
$order->create_add_products($insert_id);
$_SESSION['order_number_created'] = $insert_id;
$zco_notifier->notify('NOTIFY_CHECKOUT_PROCESS_AFTER_ORDER_CREATE_ADD_PRODUCTS');
//send email notifications


$order->send_order_email($insert_id, 2);


$zco_notifier->notify('NOTIFY_CHECKOUT_PROCESS_AFTER_SEND_ORDER_EMAIL');

// clear slamming protection since payment was accepted
if (isset($_SESSION['payment_attempt'])) unset($_SESSION['payment_attempt']);

/**
 * Calculate order amount for display purposes on checkout-success page as well as adword campaigns etc
 * Takes the product subtotal and subtracts all credits from it
 */
  $ototal = $order_subtotal = $credits_applied = 0;
  for ($i=0, $n=sizeof($order_totals); $i<$n; $i++) {
    if ($order_totals[$i]['code'] == 'ot_subtotal') $order_subtotal = $order_totals[$i]['value'];
    if ($$order_totals[$i]['code']->credit_class == true) $credits_applied += $order_totals[$i]['value'];
    if ($order_totals[$i]['code'] == 'ot_total') $ototal = $order_totals[$i]['value'];
    if ($order_totals[$i]['code'] == 'ot_tax') $otax = $order_totals[$i]['value'];
    if ($order_totals[$i]['code'] == 'ot_shipping') $oshipping = $order_totals[$i]['value'];
  }
  $commissionable_order = ($order_subtotal - $credits_applied);
  $commissionable_order_formatted = $currencies->format($commissionable_order);
  $_SESSION['order_summary']['order_number'] = $insert_id;
  $_SESSION['order_summary']['order_subtotal'] = $order_subtotal;
  $_SESSION['order_summary']['credits_applied'] = $credits_applied;
  $_SESSION['order_summary']['order_total'] = $ototal;
  $_SESSION['order_summary']['commissionable_order'] = $commissionable_order;
  $_SESSION['order_summary']['commissionable_order_formatted'] = $commissionable_order_formatted;
  $_SESSION['order_summary']['coupon_code'] = $order->info['coupon_code'];
  $_SESSION['order_summary']['currency_code'] = $order->info['currency'];
  $_SESSION['order_summary']['currency_value'] = $order->info['currency_value'];
  $_SESSION['order_summary']['payment_module_code'] = $order->info['payment_module_code'];
  $_SESSION['order_summary']['shipping_method'] = $order->info['shipping_method'];
  $_SESSION['order_summary']['orders_status'] = $order->info['orders_status'];
  $_SESSION['order_summary']['tax'] = $otax;
  $_SESSION['order_summary']['shipping'] = $oshipping;
  $zco_notifier->notify('NOTIFY_CHECKOUT_PROCESS_HANDLE_AFFILIATES');

$time2 = microtime(true);

$delta_time = $time2 - $time;

//echo "<br> script time = $delta_time<br>";

$_SESSION['script_time'] = $delta_time;




/*
    $notify_string='';
    if (isset($_GET['action']) && ($_GET['action'] == 'update')) {
      $notify_string = 'action=notify&';
      $notify = $_POST['notify'];
    
      if (is_array($notify)) {
        for ($i=0, $n=sizeof($notify); $i<$n; $i++) {
          $notify_string .= 'notify[]=' . $notify[$i] . '&';
        }
        if (strlen($notify_string) > 0) $notify_string = substr($notify_string, 0, -1);
      }
      if ($notify_string == 'action=notify&') {
          zen_redirect(zen_href_link(FILENAME_CHECKOUT_SUCCESS, '', 'SSL'));
      } else {
        zen_redirect(zen_href_link(FILENAME_DEFAULT, $notify_string));
      }
    }
  */
  
    require(DIR_WS_MODULES . zen_get_module_directory('require_languages.php'));
    $breadcrumb->add(NAVBAR_TITLE_1);
    $breadcrumb->add(NAVBAR_TITLE_2);
    
    // find out the last order number generated for this customer account
    $orders_query = "SELECT * FROM " . TABLE_ORDERS . "
                     WHERE customers_id = :customersID
                     ORDER BY date_purchased DESC LIMIT 1";
    $orders_query = $db->bindVars($orders_query, ':customersID', $_SESSION['customer_id'], 'integer');
    $orders = $db->Execute($orders_query);
    $orders_id = $orders->fields['orders_id'];
    
    //insert gift messages into db with orders_id  --ebird
    
    foreach($_SESSION['gift_msg'] as $msg){
    	$msg_query = "INSERT INTO gift_msg (orders_id, products_id, msg) values ('$orders_id','".$msg['products_id']."','".mysql_real_escape_string($msg['msg'])."')";
    	//echo "<br>q = $msg_query";
    	$db->Execute($msg_query);
    	
    }
    unset($_SESSION['gift_msg']);
    
    foreach($_SESSION['phn'] as $msg){
    	$msg_query = "INSERT INTO gift_phn (orders_id, products_id, phn) values ('$orders_id','".$msg['products_id']."','".mysql_real_escape_string($msg['phn'])."')";
    	//echo "<br>q = $msg_query";
    	$db->Execute($msg_query);
    	
    }
    unset($_SESSION['phn']);
    

    //byob
    unset($_SESSION['byob']);  // --ebird
    unset($_SESSION['byob_id']);  // --ebird
    
    unset($_SESSION['shipping_set']);
    unset($_SESSION['multiple_addresses']);
        

    // use order-id generated by the actual order process
    // this uses the SESSION orders_id, or if doesn't exist, grabs most recent order # for this cust (needed for paypal et al).
    // Needs reworking in v1.4 for checkout-rewrite
    $zv_orders_id = (isset($_SESSION['order_number_created']) && $_SESSION['order_number_created'] >= 1) ? $_SESSION['order_number_created'] : $orders_id;
    $orders_id = $zv_orders_id;
    $order_summary = $_SESSION['order_summary'];
    unset($_SESSION['order_summary']);
    unset($_SESSION['order_number_created']);
    
    // prepare list of product-notifications for this customer
    $global_query = "SELECT global_product_notifications
                     FROM " . TABLE_CUSTOMERS_INFO . "
                     WHERE customers_info_id = :customersID";
    
    $global_query = $db->bindVars($global_query, ':customersID', $_SESSION['customer_id'], 'integer');
    $global = $db->Execute($global_query);
    $flag_global_notifications = $global->fields['global_product_notifications'];
    
    if ($flag_global_notifications != '1') {
    
      $products_array = array();
      $counter = 0;
    
      $products_query = "SELECT products_id, products_name
                         FROM " . TABLE_ORDERS_PRODUCTS . "
                         WHERE orders_id = :ordersID
                         ORDER BY products_name";
    
      $products_query = $db->bindVars($products_query, ':ordersID', $orders_id, 'integer');
      $products = $db->Execute($products_query);
    
      while (!$products->EOF) {
        $notificationsArray[] = array('counter'=>$counter,
                                      'products_id'=>$products->fields['products_id'],
                                      'products_name'=>$products->fields['products_name']);
        $counter++;
        $products->MoveNext();
      }
    }
    
      $flag_show_products_notification = (CUSTOMERS_PRODUCTS_NOTIFICATION_STATUS == '1' and sizeof($notificationsArray)>0 and $flag_global_notifications != '1') ? true : false ;
    
      $products_displayed = array();
    
    
      $gv_query = "SELECT amount
                   FROM " . TABLE_COUPON_GV_CUSTOMER . "
                   WHERE customer_id = :customersID ";
    
      $gv_query = $db->bindVars($gv_query, ':customersID', $_SESSION['customer_id'], 'integer');
      $gv_result = $db->Execute($gv_query);
    
      if ($gv_result->fields['amount'] > 0 ) {
        $customer_has_gv_balance = true;
        $customer_gv_balance = $currencies->format($gv_result->fields['amount']);
      }
    
    
    // include template specific file name defines
    $define_page = zen_get_file_directory(DIR_WS_LANGUAGES . $_SESSION['language'] . '/html_includes/', FILENAME_DEFINE_CHECKOUT_SUCCESS, 'false');


$_SESSION['cart']->reset(true); //empty shopping cart

} else {
  echo '<html><head>';
  echo '<script type="text/javascript">
<!--
theTimer = 0;
timeOut = 12;

function submit_form()
{
  theTimer = setTimeout("submit_form();", 100);
  if (timeOut > 0) {
    timeOut -= 1;
  }
  else
  {
    clearTimeout(theTimer);
    document.getElementById("submitbutton").disabled = true;
    document.forms.formpost.submit();
  }
}
function continueClick()
{
  clearTimeout(theTimer);
  return true;
}

submit_form();
//-->
</script>' . "\n" . '</head>';
  echo '<body style="text-align: center; min-width: 600px;">' . "\n" . '<div style="text-align: center;  width: 600px;  margin-left: auto;  margin-right: auto; margin-top:20%;"><p>This page will automatically redirect you back to ' . STORE_NAME . ' for your order confirmation details.<br />If you are not redirected within 5 seconds, please click the button below to continue.</p>';
  echo "\n" . '<form action="' . zen_href_link(FILENAME_CHECKOUT_SUCCESS, zen_get_all_get_params(array('action')), 'SSL', false) . '" method="post" name="formpost" />' . "\n";
  reset($_POST);
  while (list($key, $value) = each($_POST)) {
    if (!is_array($_POST[$key])) {
      echo zen_draw_hidden_field($key, htmlspecialchars(stripslashes($value))) . "\n";
    }
  }
  echo "\n" . '<input type="submit" class="submitbutton" id="submitbutton" value=" Continue " onclick="continueClick()" />' . "\n";
  echo '</form></div></body></html>';
  exit();
}

// This should be last line of the script:
$zco_notifier->notify('NOTIFY_HEADER_END_CHECKOUT_SUCCESS');
