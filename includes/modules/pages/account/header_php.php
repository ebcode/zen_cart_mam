<?php
/**
 * Header code file for the customer's Account page
 *
 * @package page
 * @copyright Copyright 2003-2006 Zen Cart Development Team
 * @copyright Portions Copyright 2003 osCommerce
 * @license http://www.zen-cart.com/license/2_0.txt GNU Public License V2.0
 * @version $Id: header_php.php 4824 2006-10-23 21:01:28Z drbyte $
 */
// This should be first line of the script:
$zco_notifier->notify('NOTIFY_HEADER_START_ACCOUNT');
$customer_has_gv_balance = false;
$customer_gv_balance = false;

if (!$_SESSION['customer_id']) {
  $_SESSION['navigation']->set_snapshot();
  zen_redirect(zen_href_link(FILENAME_LOGIN, '', 'SSL'));
}
$gv_query = "SELECT amount
             FROM " . TABLE_COUPON_GV_CUSTOMER . "
             WHERE customer_id = :customersID";

$gv_query = $db->bindVars($gv_query, ':customersID', $_SESSION['customer_id'], 'integer');
$gv_result = $db->Execute($gv_query);

if ($gv_result->RecordCount() && $gv_result->fields['amount'] > 0 ) {
  $customer_has_gv_balance = true;
  $customer_gv_balance = $currencies->format($gv_result->fields['amount']);
}

require(DIR_WS_MODULES . zen_get_module_directory('require_languages.php'));

$breadcrumb->add(NAVBAR_TITLE);

/*
$orders_query = "SELECT o.orders_id, o.date_purchased, o.delivery_name,
                        o.delivery_country, o.billing_name, o.billing_country,
                        ot.text as order_total, s.orders_status_name
                 FROM   " . TABLE_ORDERS . " o, " . TABLE_ORDERS_TOTAL . "  ot, " . TABLE_ORDERS_STATUS . " s
                 WHERE  o.customers_id = :customersID
                 AND    o.orders_id = ot.orders_id
                 AND    ot.class = 'ot_total'
                 AND    o.orders_status = s.orders_status_id
                 AND   s.language_id = :languagesID
                 ORDER BY orders_id DESC LIMIT 3";
 *
 */

//Multiple Addresses Mod
$orders_query = "SELECT o.orders_id, o.date_purchased, o.delivery_name,
                        o.delivery_country, o.billing_name, o.billing_country,
                        o.multiple_addresses, o.order_total, 
                         s.orders_status_name
                 FROM   " . TABLE_ORDERS . " o, " . TABLE_ORDERS_STATUS . " s
                 WHERE  o.customers_id = :customersID
                 AND    o.orders_status = s.orders_status_id
                 AND   s.language_id = :languagesID group by o.orders_id
                 ORDER BY orders_id DESC LIMIT 3";
 
$orders_query = $db->bindVars($orders_query, ':customersID', $_SESSION['customer_id'], 'integer');
$orders_query = $db->bindVars($orders_query, ':languagesID', $_SESSION['languages_id'], 'integer');
$orders = $db->Execute($orders_query);

$ordersArray = array();
while (!$orders->EOF) {
  if (zen_not_null($orders->fields['delivery_name'])) {
    $order_name = $orders->fields['delivery_name'];
    $order_country = $orders->fields['delivery_country'];
  } else {
    $order_name = $orders->fields['billing_name'];
    $order_country = $orders->fields['billing_country'];
  }
  
  $multiple_address_details=array();
  
  if($orders->fields['multiple_addresses']){
  	/*
  	$q = "select ma.address_book_id, a.entry_title from " . TABLE_MULTIPLE_ADDRESSES_CUSTOMERS_BASKETS_PRODUCTS_ORDERS . " ma left join ".
  	TABLE_ADDRESS_BOOK." a on ma.address_book_id = a.address_book_id where orders_id = ".
  		$orders->fields['orders_id'];
  	*/
  	$q = "select ma.multiple_addresses_customers_baskets_products_orders_id, ma.customers_id, ma.orders_products_id,
	      		ma.orders_id, ma.products_id, ma.products_quantity_for_this_address, ma.shipping_method, ma.shipping_module_code,
	      		ma.cost, ma.shipping_method_title, ma.entry_company as company, ma.entry_firstname as firstname,
	      		ma.entry_lastname as lastname, ma.entry_street_address as street_address, ma.entry_suburb as suburb,
	      		ma.entry_postcode as postcode, ma.entry_city as city, ma.entry_state as state, ma.entry_country_id as country_id,
	      		ma.entry_zone_id as zone_id, pr.products_name 
	                                       from " . TABLE_MULTIPLE_ADDRESSES_CUSTOMERS_BASKETS_PRODUCTS_ORDERS . " ma  left join ". TABLE_PRODUCTS_DESCRIPTION." pr on pr.products_id = ma.products_id  
	                                       where ma.orders_id = '" . $orders->fields['orders_id'] . "'
	                                       order by multiple_addresses_customers_baskets_products_orders_id";
  	
  		
  		
  		
  	$addresses = $db->Execute($q);
	
	while (!$addresses->EOF) {
		
		//var_dump($addresses);
		$multiple_address_details[]=$addresses->fields;
		$addresses->MoveNext();
		
	}
  }
  
  $ordersArray[] = array('orders_id'=>$orders->fields['orders_id'],
  'date_purchased'=>$orders->fields['date_purchased'],
  'order_name'=>$order_name,
  'order_country'=>$order_country,
  'orders_status_name'=>$orders->fields['orders_status_name'],
  'order_total'=>$currencies->format($orders->fields['order_total']),
  'multiple_addresses'=>$orders->fields['multiple_addresses'], //Multiple Addresses Mod
  'multiple_address_details'=>$multiple_address_details,
  'orders_products_multiple_addresses_id'=>$orders->fields['orders_products_multiple_addresses_id']
  );

  $orders->MoveNext();
}

// This should be last line of the script:
$zco_notifier->notify('NOTIFY_HEADER_END_ACCOUNT');
?>