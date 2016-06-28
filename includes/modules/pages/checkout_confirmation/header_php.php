<?php
/**
 * checkout_confirmation header_php.php
 *
 * @package page
 * @copyright Copyright 2003-2010 Zen Cart Development Team
 * @copyright Portions Copyright 2003 osCommerce
 * @license http://www.zen-cart.com/license/2_0.txt GNU Public License V2.0
 * @version $Id: header_php.php 16397 2010-05-26 11:21:22Z drbyte $
 */
//ini_set('display_errors',1);

//echo "<br> comments = " .$_SESSION['comments'];

//$_SESSION['comments'] = 'TEST TEST TEST';

//echo "<br> comments = " .$_SESSION['comments'] . ' on line: ' . __LINE__;

//var_dump($_SESSION);
$product_ids = array_keys($_SESSION['cart']->contents);  //Multiple Addresses Mod

// This should be first line of the script:
$zco_notifier->notify('NOTIFY_HEADER_START_CHECKOUT_CONFIRMATION');

// if there is nothing in the customers cart, redirect them to the shopping cart page
if ($_SESSION['cart']->count_contents() <= 0) {
    zen_redirect(zen_href_link(FILENAME_TIME_OUT));
}

// if the customer is not logged on, redirect them to the login page
  if (!$_SESSION['customer_id']) {
    $_SESSION['navigation']->set_snapshot(array('mode' => 'SSL', 'page' => FILENAME_CHECKOUT_PAYMENT));
    zen_redirect(zen_href_link(FILENAME_LOGIN, '', 'SSL'));
  } else {
    // validate customer
    if (zen_get_customer_validate_session($_SESSION['customer_id']) == false) {
      $_SESSION['navigation']->set_snapshot();
      zen_redirect(zen_href_link(FILENAME_LOGIN, '', 'SSL'));
    }
  }

//echo "<br> comments = " .$_SESSION['comments'] . ' on line: ' . __LINE__;

// avoid hack attempts during the checkout procedure by checking the internal cartID
if (isset($_SESSION['cart']->cartID) && $_SESSION['cartID']) {
  if ($_SESSION['cart']->cartID != $_SESSION['cartID']) {
    zen_redirect(zen_href_link(FILENAME_CHECKOUT_SHIPPING, '', 'SSL'));
  }
}

// if no shipping method has been selected, redirect the customer to the shipping method selection page
if (!$_SESSION['shipping']) {
  zen_redirect(zen_href_link(FILENAME_CHECKOUT_SHIPPING, '', 'SSL'));
}
if (isset($_SESSION['shipping']['id']) && $_SESSION['shipping']['id'] == 'free_free' && defined('MODULE_ORDER_TOTAL_SHIPPING_FREE_SHIPPING_OVER') && $_SESSION['cart']->show_total() < MODULE_ORDER_TOTAL_SHIPPING_FREE_SHIPPING_OVER) {
  zen_redirect(zen_href_link(FILENAME_CHECKOUT_SHIPPING, '', 'SSL'));
}

//echo "<br> comments = " .$_SESSION['comments'] . ' on line: ' . __LINE__;

if (isset($_POST['payment'])) $_SESSION['payment'] = $_POST['payment'];


//$_SESSION['comments'] = zen_db_prepare_input($_POST['comments']);

//echo "<br> comments = " .$_SESSION['comments'] . ' on line: ' . __LINE__;

//'checkout_payment_discounts'
//zen_redirect(zen_href_link(FILENAME_CHECKOUT_PAYMENT, '', 'SSL'));


if (DISPLAY_CONDITIONS_ON_CHECKOUT == 'true') {
  if (!isset($_POST['conditions']) || ($_POST['conditions'] != '1')) {
    $messageStack->add_session('checkout_payment', ERROR_CONDITIONS_NOT_ACCEPTED, 'error');
  }
}
//echo $messageStack->size('checkout_payment');

/*Multiple Addresses Mod
 * 
*/
if(isset($_POST['btn_back_x'])){
	
	if(isset($_SESSION['multiple_addresses'])){
		zen_redirect(zen_href_link(FILENAME_CHECKOUT_MULTIPLE_SHIPMENTS, '', 'SSL'));
	} else {
		zen_redirect(zen_href_link(FILENAME_CHECKOUT_SHIPPING, '', 'SSL'));
	}
}

/*
 * 
 */

require(DIR_WS_CLASSES . 'order.php');
require(DIR_WS_CLASSES . 'shipping.php');
require(DIR_WS_CLASSES . 'order_total.php');

//$order = new order;
// load the selected shipping module

//$shipping_modules = new shipping($_SESSION['shipping']);




if(!isset($_SESSION['multiple_addresses'])){

$order = new order;

//Load the selected shipping module(needed to calculate tax correctly)
$shipping_modules = new shipping($_SESSION['shipping']);

$order_total_modules = new order_total;
$order_total_modules->collect_posts();
$order_total_modules->pre_confirmation_check();

$order_totals = $order_total_modules->process();
$order_totals_output[] = $order_total_modules->output();

//echo "<br> comments = " .$_SESSION['comments'] . ' on line: ' . __LINE__;

} else {

$_SESSION['cart']->save_contents();

//$_SESSION['cart']->contents[$product_ids[1]]['qty']=0;

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
				
				//echo "<br>quantity=$quantity_for_this_address";
				
				$order = new order;
				$shipping_modules = new shipping($_SESSION['shipping']);
				
				$order_total_modules = new order_total;
				$order_total_modules->collect_posts();
				$order_total_modules->pre_confirmation_check();
				
				$order_totals = $order_total_modules->process();
				$order_totals_output[] = $order_total_modules->output();
				
				//set quantity back to 0
				$_SESSION['cart']->contents[$product_ids[$k]]['qty']=0;
				
				$address_ids[] = $address_book_id;
				$shipping_method_titles[] = $shipping_method_title;
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
				
				//echo "<br>quantity=$quantity_for_this_address";
				
				$order = new order;
				$shipping_modules = new shipping($_SESSION['shipping']);
				
				$order_total_modules = new order_total;
				$order_total_modules->collect_posts();
				$order_total_modules->pre_confirmation_check();
				
				$order_totals = $order_total_modules->process();
				$order_totals_output[] = $order_total_modules->output();
				
				//set quantity back to 0
				$_SESSION['cart']->contents[$product_ids[$k]]['qty']=0;
				
				$address_ids[] = $address_book_id;
				$shipping_method_titles[] = $shipping_method_title;
		 }
		$k++;
	}
 
//var_dump($order_totals_output);


//var_dump($_SESSION['cart']->contents);

//echo "<br>grand_total = ".$_SESSION['multiple_addresses_grand_total'];

$_SESSION['cart']->revert_contents();

$order = new order;  //Multiple Addresses Mod

}

//$order = new order;  //Multiple Addresses Mod

//var_dump($_SESSION['cart']->contents);

//$order_total_modules = new order_total;
//$order_total_modules->collect_posts();
//$order_total_modules->pre_confirmation_check();

// load the selected payment module
require(DIR_WS_CLASSES . 'payment.php');

if (!isset($credit_covers)) $credit_covers = FALSE;

//echo 'credit covers'.$credit_covers;

if ($credit_covers) {
  unset($_SESSION['payment']);
  $_SESSION['payment'] = '';
}

//@debug echo ($credit_covers == true) ? 'TRUE' : 'FALSE';

$payment_modules = new payment($_SESSION['payment']);
$payment_modules->update_status();
if ( ($_SESSION['payment'] == '' || !is_object($$_SESSION['payment']) ) && $credit_covers === FALSE) {
  $messageStack->add_session('checkout_payment', ERROR_NO_PAYMENT_MODULE_SELECTED, 'error');
}



//var_dump($_POST);
// Stock Check
$flagAnyOutOfStock = false;
$stock_check = array();
if (STOCK_CHECK == 'true') {
  for ($i=0, $n=sizeof($order->products); $i<$n; $i++) {
    if ($stock_check[$i] = zen_check_stock($order->products[$i]['id'], $order->products[$i]['qty'])) {
      $flagAnyOutOfStock = true;
    }
  }
  // Out of Stock
  if ( (STOCK_ALLOW_CHECKOUT != 'true') && ($flagAnyOutOfStock == true) ) {
    zen_redirect(zen_href_link(FILENAME_SHOPPING_CART));
  }
}

// update customers_referral with $_SESSION['gv_id']
if ($_SESSION['cc_id']) {
  $discount_coupon_query = "SELECT coupon_code
                            FROM " . TABLE_COUPONS . "
                            WHERE coupon_id = :couponID";

  $discount_coupon_query = $db->bindVars($discount_coupon_query, ':couponID', $_SESSION['cc_id'], 'integer');
  $discount_coupon = $db->Execute($discount_coupon_query);

  $customers_referral_query = "SELECT customers_referral
                               FROM " . TABLE_CUSTOMERS . "
                               WHERE customers_id = :customersID";

  $customers_referral_query = $db->bindVars($customers_referral_query, ':customersID', $_SESSION['customer_id'], 'integer');
  $customers_referral = $db->Execute($customers_referral_query);

  // only use discount coupon if set by coupon
  if ($customers_referral->fields['customers_referral'] == '' and CUSTOMERS_REFERRAL_STATUS == 1) {
    $sql = "UPDATE " . TABLE_CUSTOMERS . "
            SET customers_referral = :customersReferral
            WHERE customers_id = :customersID";

    $sql = $db->bindVars($sql, ':customersID', $_SESSION['customer_id'], 'integer');
    $sql = $db->bindVars($sql, ':customersReferral', $discount_coupon->fields['coupon_code'], 'string');
    $db->Execute($sql);
  } else {
    // do not update referral was added before
  }
}



if (is_array($payment_modules->modules)) {
  $payment_modules->pre_confirmation_check();
}

if ($messageStack->size('checkout_payment') > 0) {
  zen_redirect(zen_href_link(FILENAME_CHECKOUT_PAYMENT, '', 'SSL'));
}
//echo $messageStack->size('checkout_payment');
//die('here');

if (isset($$_SESSION['payment']->form_action_url)) {
  $form_action_url = $$_SESSION['payment']->form_action_url;
} else {
  $form_action_url = zen_href_link(FILENAME_CHECKOUT_PROCESS, '', 'SSL');
}

// if shipping-edit button should be overridden, do so
if(!isset($_SESSION['multiple_addresses'])){
$editShippingButtonLink = zen_href_link(FILENAME_CHECKOUT_SHIPPING, '', 'SSL');
} else {
$editShippingButtonLink = zen_href_link(FILENAME_CHECKOUT_MULTIPLE_ADDRESSES, '', 'SSL');
}

if (method_exists($$_SESSION['payment'], 'alterShippingEditButton')) {
  $theLink = $$_SESSION['payment']->alterShippingEditButton();
  if ($theLink) $editShippingButtonLink = $theLink;
}
// deal with billing address edit button
$flagDisablePaymentAddressChange = false;
if (isset($$_SESSION['payment']->flagDisablePaymentAddressChange)) {
  $flagDisablePaymentAddressChange = $$_SESSION['payment']->flagDisablePaymentAddressChange;
}

require(DIR_WS_MODULES . zen_get_module_directory('require_languages.php'));
$breadcrumb->add(NAVBAR_TITLE_1, zen_href_link(FILENAME_CHECKOUT_SHIPPING, '', 'SSL'));
$breadcrumb->add(NAVBAR_TITLE_2);

// This should be last line of the script:
$zco_notifier->notify('NOTIFY_HEADER_END_CHECKOUT_CONFIRMATION');

//echo "<br>WTF #2 comments = " .$_SESSION['comments'];

//echo "OK";

?>