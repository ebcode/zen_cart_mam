<?php
/**
 * checkout_payment header_php.php
 *
 * @package page
 * @copyright Copyright 2003-2007 Zen Cart Development Team
 * @copyright Portions Copyright 2003 osCommerce
 * @license http://www.zen-cart.com/license/2_0.txt GNU Public License V2.0m
 * @version $Id: header_php.php 16397 2010-05-26 11:21:22Z drbyte $
 */

//echo "<br>session shipping: ";

$_SESSION['cart']->save_contents();

//echo "<br> comments = " .$_SESSION['comments'];

//var_dump($_SESSION);
//$_SESSION['cart'][0]['qty']=1;
$product_ids = array_keys($_SESSION['cart']->contents);

//$_SESSION['cart']->contents[$product_ids[0]]['qty']=1;
//var_dump($_SESSION['cart']->contents);

//var_dump($_SESSION['multiple_addresses']); 
 
// This should be first line of the script:
$zco_notifier->notify('NOTIFY_HEADER_START_CHECKOUT_PAYMENT');

// if there is nothing in the customers cart, redirect them to the shopping cart page
if ($_SESSION['cart']->count_contents() <= 0) {
    zen_redirect(zen_href_link(FILENAME_TIME_OUT));
}

// if the customer is not logged on, redirect them to the login page
  if (!$_SESSION['customer_id']) {
    $_SESSION['navigation']->set_snapshot();
    zen_redirect(zen_href_link(FILENAME_LOGIN, '', 'SSL'));
  } else {
    // validate customer
    if (zen_get_customer_validate_session($_SESSION['customer_id']) == false) {
      $_SESSION['navigation']->set_snapshot();
      zen_redirect(zen_href_link(FILENAME_LOGIN, '', 'SSL'));
    }
  }

// if no shipping method has been selected, redirect the customer to the shipping method selection page
if (!$_SESSION['shipping']) {
  zen_redirect(zen_href_link(FILENAME_CHECKOUT_SHIPPING, '', 'SSL'));
}
if (isset($_SESSION['shipping']['id']) && $_SESSION['shipping']['id'] == 'free_free' && defined('MODULE_ORDER_TOTAL_SHIPPING_FREE_SHIPPING_OVER') && $_SESSION['cart']->show_total() < MODULE_ORDER_TOTAL_SHIPPING_FREE_SHIPPING_OVER) {
  zen_redirect(zen_href_link(FILENAME_CHECKOUT_SHIPPING, '', 'SSL'));
}

// avoid hack attempts during the checkout procedure by checking the internal cartID
if (isset($_SESSION['cart']->cartID) && $_SESSION['cartID']) {
  if ($_SESSION['cart']->cartID != $_SESSION['cartID']) {
    zen_redirect(zen_href_link(FILENAME_CHECKOUT_SHIPPING, '', 'SSL'));
  }
}

// Stock Check
if ( (STOCK_CHECK == 'true') && (STOCK_ALLOW_CHECKOUT != 'true') ) {
  $products = $_SESSION['cart']->get_products();
  for ($i=0, $n=sizeof($products); $i<$n; $i++) {
    if (zen_check_stock($products[$i]['id'], $products[$i]['quantity'])) {
      zen_redirect(zen_href_link(FILENAME_SHOPPING_CART));
      break;
    }
  }
}

// get coupon code
if ($_SESSION['cc_id']) {
  $discount_coupon_query = "SELECT coupon_code
                            FROM " . TABLE_COUPONS . "
                            WHERE coupon_id = :couponID";

  $discount_coupon_query = $db->bindVars($discount_coupon_query, ':couponID', $_SESSION['cc_id'], 'integer');
  $discount_coupon = $db->Execute($discount_coupon_query);
}

// if no billing destination address was selected, use the customers own address as default
if (!$_SESSION['billto']) {
  $_SESSION['billto'] = $_SESSION['customer_default_address_id'];
} else {
  // verify the selected billing address
  $check_address_query = "SELECT count(*) AS total FROM " . TABLE_ADDRESS_BOOK . "
                          WHERE customers_id = :customersID
                          AND address_book_id = :addressBookID";

  $check_address_query = $db->bindVars($check_address_query, ':customersID', $_SESSION['customer_id'], 'integer');
  $check_address_query = $db->bindVars($check_address_query, ':addressBookID', $_SESSION['billto'], 'integer');
  $check_address = $db->Execute($check_address_query);

  if ($check_address->fields['total'] != '1') {
    $_SESSION['billto'] = $_SESSION['customer_default_address_id'];
    $_SESSION['payment'] = '';
  }
}


require(DIR_WS_CLASSES . 'order.php');
require(DIR_WS_CLASSES . 'shipping.php');
require(DIR_WS_CLASSES . 'order_total.php');

//$order = new order;

// Load the selected shipping module(needed to calculate tax correctly)
//$shipping_modules = new shipping($_SESSION['shipping']);

//$order_total_modules = new order_total;
//$order_total_modules->collect_posts();
//$order_total_modules->pre_confirmation_check();

//Multiple Addresses Mod

//psuedo-code for building up order_totals for each address
/**
 * 
 * 0. loop through $multiple_addresses array to drill down into each address
 * 1. set all relevant $_SESSION variables for each address, ex: delivery, shipping, cost
 * 2. create order object for that address: $order = new order;
 * 3. create order_total object and call the methods below
 * 4. collect totals output in array: $output[] = $order_total_modules->output();
 */

if(!isset($_SESSION['multiple_addresses'])){
$order = new order;

//Load the selected shipping module(needed to calculate tax correctly)
$shipping_modules = new shipping($_SESSION['shipping']);

$order_total_modules = new order_total;
$order_total_modules->collect_posts();
$order_total_modules->pre_confirmation_check();

$order_totals = $order_total_modules->process();
$order_totals_output[] = $order_total_modules->output();

} else {
 
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
				
				//echo "<br>quantity=".$quantity_for_this_address;
				
				$order = new order;
				$shipping_modules = new shipping($_SESSION['shipping']);
				
				//var_dump($order);
				
				$order_total_modules = new order_total;
				$order_total_modules->collect_posts();
				$order_total_modules->pre_confirmation_check();
				
				$order_totals = $order_total_modules->process();
				$order_totals_output[] = $order_total_modules->output();
				
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
				
				//echo "<br>quantity=".$quantity_for_this_address;
				
				$order = new order;
				$shipping_modules = new shipping($_SESSION['shipping']);
				
				$order_total_modules = new order_total;
				$order_total_modules->collect_posts();
				$order_total_modules->pre_confirmation_check();
				
				$order_totals = $order_total_modules->process();
				$order_totals_output[] = $order_total_modules->output();
				
				$_SESSION['cart']->contents[$product_ids[$k]]['qty']=0;
		 }
		$k++;
	}
 


//var_dump($_SESSION['cart']->contents);

//echo "<br>grand_total = ".$_SESSION['multiple_addresses_grand_total'];

}

//$order_totals = $order_total_modules->process();
//$order_totals_output = $order_total_modules->output();

$_SESSION['cart']->revert_contents();

//  $_SESSION['comments'] = '';
$comments = $_SESSION['comments'];

$total_weight = $_SESSION['cart']->show_weight();
$total_count = $_SESSION['cart']->count_contents();

// load all enabled payment modules
require(DIR_WS_CLASSES . 'payment.php');
$payment_modules = new payment;
$flagOnSubmit = sizeof($payment_modules->selection());


require(DIR_WS_MODULES . zen_get_module_directory('require_languages.php'));

if (isset($_GET['payment_error']) && is_object(${$_GET['payment_error']}) && ($error = ${$_GET['payment_error']}->get_error())) {
  $messageStack->add('checkout_payment', $error['error'], 'error');
}

$breadcrumb->add(NAVBAR_TITLE_1, zen_href_link(FILENAME_CHECKOUT_SHIPPING, '', 'SSL'));
$breadcrumb->add(NAVBAR_TITLE_2);




// This should be last line of the script:
$zco_notifier->notify('NOTIFY_HEADER_END_CHECKOUT_PAYMENT');
?>