<?php
//define('HM', 'hmmmm..');

//ini_set('display_errors',1);

$debug=0;  //speed up

//check is_multi

//$hm = array(0=>array(0=>1));

//$x = is_multi($hm);

//echo "<br>x=$x";

//var_dump($_SESSION);

/**
 * Checkout Shipping Page
 *
 * @package page
 * @copyright Copyright 2003-2009 Zen Cart Development Team
 * @copyright Portions Copyright 2003 osCommerce
 * @license http://www.zen-cart.com/license/2_0.txt GNU Public License V2.0
 * @version $Id: header_php.php 18007 2010-10-21 06:41:51Z drbyte $
 */
// This should be first line of the script:
  $zco_notifier->notify('NOTIFY_HEADER_START_CHECKOUT_SHIPPING');

  require(DIR_WS_MODULES . zen_get_module_directory('require_languages.php'));

  require_once(DIR_WS_CLASSES . 'http_client.php');

// if there is nothing in the customers cart, redirect them to the shopping cart page
  if ($_SESSION['cart']->count_contents() <= 0) {
    zen_redirect(zen_href_link(FILENAME_TIME_OUT));
  }

// if the customer is not logged on, redirect them to the login page
  if (!isset($_SESSION['customer_id']) || !$_SESSION['customer_id']) {
    $_SESSION['navigation']->set_snapshot();
    zen_redirect(zen_href_link(FILENAME_LOGIN, '', 'SSL'));
  } else {
    // validate customer
    if (zen_get_customer_validate_session($_SESSION['customer_id']) == false) {
      $_SESSION['navigation']->set_snapshot(array('mode' => 'SSL', 'page' => FILENAME_CHECKOUT_SHIPPING));
      zen_redirect(zen_href_link(FILENAME_LOGIN, '', 'SSL'));
    }
  }

// Validate Cart for checkout
  $_SESSION['valid_to_checkout'] = true;
  $_SESSION['cart']->get_products(true);
  if ($_SESSION['valid_to_checkout'] == false) {
    $messageStack->add('header', ERROR_CART_UPDATE, 'error');
    zen_redirect(zen_href_link(FILENAME_SHOPPING_CART));
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
// if no shipping destination address was selected, use the customers own address as default
  if (!$_SESSION['sendto']) {
    $_SESSION['sendto'] = $_SESSION['customer_default_address_id'];
  } else {
// verify the selected shipping address
    $check_address_query = "SELECT count(*) AS total
                            FROM   " . TABLE_ADDRESS_BOOK . "
                            WHERE  customers_id = :customersID
                            AND    address_book_id = :addressBookID";

    $check_address_query = $db->bindVars($check_address_query, ':customersID', $_SESSION['customer_id'], 'integer');
    $check_address_query = $db->bindVars($check_address_query, ':addressBookID', $_SESSION['sendto'], 'integer');
    $check_address = $db->Execute($check_address_query);

    if ($check_address->fields['total'] != '1') {
      $_SESSION['sendto'] = $_SESSION['customer_default_address_id'];
      $_SESSION['shipping'] = '';
    }
  }

  require(DIR_WS_CLASSES . 'order.php');
  
  
  $order = new order;  //--need to change the ['delivery'] object for each address in the order

  //echo "order=<br>";
  //var_dump($order);

// register a random ID in the session to check throughout the checkout procedure
// against alterations in the shopping cart contents
if (isset($_SESSION['cart']->cartID)) {
  if (!isset($_SESSION['cartID']) || $_SESSION['cart']->cartID != $_SESSION['cartID']) {
    $_SESSION['cartID'] = $_SESSION['cart']->cartID;
  }
} else {
		
  //die('');		
	
  zen_redirect(zen_href_link(FILENAME_TIME_OUT));
}

// if the order contains only virtual products, forward the customer to the billing page as
// a shipping address is not needed
  if ($order->content_type == 'virtual') {
    $_SESSION['shipping'] = 'free_free';
    $_SESSION['shipping']['title'] = 'free_free';
    $_SESSION['sendto'] = false;
    zen_redirect(zen_href_link(FILENAME_CHECKOUT_PAYMENT, '', 'SSL'));
  }


  // Should address-edit button be offered?
  $displayAddressEdit = (MAX_ADDRESS_BOOK_ENTRIES >= 2);

  // if shipping-edit button should be overridden, do so
  $editShippingButtonLink = zen_href_link(FILENAME_CHECKOUT_SHIPPING_ADDRESS, '', 'SSL');
  if (isset($_SESSION['payment']) && method_exists($$_SESSION['payment'], 'alterShippingEditButton')) {
    $theLink = $$_SESSION['payment']->alterShippingEditButton();
    if ($theLink) {
      $editShippingButtonLink = $theLink;
      $displayAddressEdit = true;
    }
  }

  //change button to use single shipping address

  //$shipToMultipleAddressesLink = zen_href_link(FILENAME_CHECKOUT_MULTIPLE_ADDRESSES.'&ref='.FILENAME_CHECKOUT_SHIPPING, '', 'SSL');  //Multiple Addresses Mod

  //$breadcrumb->add(NAVBAR_TITLE_1, zen_href_link(FILENAME_CHECKOUT_SHIPPING, '', 'SSL'));
  //$breadcrumb->add(NAVBAR_TITLE_2);

// This should be last line of the script:
  //$zco_notifier->notify('NOTIFY_HEADER_END_CHECKOUT_SHIPPING');



  $total_weight = $_SESSION['cart']->show_weight();
  $total_count = $_SESSION['cart']->count_contents();

// load all enabled shipping modules
  require(DIR_WS_CLASSES . 'shipping.php');
  $shipping_modules = new shipping;


//psuedo-code for multiple shipments:
/**
 *   1. Loop through products
 *   2. Loop through addresses
 *   3. Calculate weight for each address
 *   4. Assign $order->delivery variables
 *   5. Call $shipping->quotes() for each address
 */

 //move this into function
function get_shipping_address($address_book_id){
	global $db;
  $shipping_address_query = "select ab.address_book_id, ab.entry_firstname, ab.entry_lastname, ab.entry_company, ab.entry_phone,
                                    ab.entry_street_address, ab.entry_suburb, ab.entry_postcode,
                                    ab.entry_city, ab.entry_zone_id, z.zone_name, ab.entry_country_id,
                                    c.countries_id, c.countries_name, c.countries_iso_code_2,
                                    c.countries_iso_code_3, c.address_format_id, ab.entry_state
                                   from " . TABLE_ADDRESS_BOOK . " ab
                                   left join " . TABLE_ZONES . " z on (ab.entry_zone_id = z.zone_id)
                                   left join " . TABLE_COUNTRIES . " c on (ab.entry_country_id = c.countries_id)
                                   where ab.customers_id = '" . (int)$_SESSION['customer_id'] . "'
                                   and ab.address_book_id = '" . $address_book_id . "'";
 $shipping_address = $db->Execute($shipping_address_query);
 return $shipping_address;
}

function set_order_delivery($shipping_address){
	global $order;
	$order->delivery = array('address_book_id' => $shipping_address->fields['address_book_id'],
                                'firstname' => $shipping_address->fields['entry_firstname'],
	                            'lastname' => $shipping_address->fields['entry_lastname'],
	                            'phone' => $shipping_address->fields['entry_phone'],
	                            'company' => $shipping_address->fields['entry_company'],
	                            'street_address' => $shipping_address->fields['entry_street_address'],
	                            'suburb' => $shipping_address->fields['entry_suburb'],
	                            'city' => $shipping_address->fields['entry_city'],
	                            'postcode' => $shipping_address->fields['entry_postcode'],
	                            'state' => ((zen_not_null($shipping_address->fields['entry_state'])) ? $shipping_address->fields['entry_state'] : $shipping_address->fields['zone_name']),
	                            'zone_id' => $shipping_address->fields['entry_zone_id'],
	                            'country' => array('id' => $shipping_address->fields['countries_id'], 'title' => $shipping_address->fields['countries_name'], 'iso_code_2' => $shipping_address->fields['countries_iso_code_2'], 'iso_code_3' => $shipping_address->fields['countries_iso_code_3']),
	                            'country_id' => $shipping_address->fields['entry_country_id'],
	                            'format_id' => (int)$shipping_address->fields['address_format_id']);
}


function get_product_price($id){
    global $products;
    foreach($products as $p){
        if($p['id'] == $id){
            return $p['final_price'];
        }
    }
}


//loop through products

//$store $order->delivery;
$order_delivery_tmp = $order->delivery;

$all_quotes=array();
$product_address_ids=array();
$address_ids=array();
$product_ids=array();
$quantities=array();
$selected_shipping_methods=array();

//get products

$products=$_SESSION['cart']->get_products();


if(!count($_POST)){  // NO POST

	foreach ($_SESSION['multiple_addresses'] as $product_id=>$address){
    /*
    echo "<br> product_id = $product_id<br>";

    //echo "<br> PRICE = " . $products[$product_id]['final_price'];
    echo "<pre>";
    var_dump($products);
    echo "</pre>";

    echo "<BR> price = " . get_product_price($product_id);
    */
	//echo "<br>no post, product_id=$product_id";
	 
	$how_many = $address['how_many_addresses'];
	 
		if ($how_many>1){
	 	 
			for($i=0;$i<$how_many;$i++){
			 	$address_book_id = $address['addresses'][$i]['address_book_id'];
				$quantity_for_this_address = $address['addresses'][$i]['qty'];
				
                //echo "<br> quantity = $quantity_for_this_address";

				$shipping_module_code = $address['addresses'][$i]['shipping_module_code'];
				$shipping_method = $address['addresses'][$i]['shipping_method'];
				
				//echo "<br>book_id=$address_book_id, qty=$quantity_for_this_address";
				
				$product_address_id=$product_id.'_'.$address_book_id; //using this to name the shipping select boxes
				
				$product_address_ids[]=$product_address_id;
				
				$address_ids[] = $address_book_id;
				
				$product_ids[] = $product_id;
				
				$quantities[] = $quantity_for_this_address;
				
				$shipping_module_codes[]=$shipping_module_code;
				
				$shipping_methods[]=$shipping_method;
				
				$selected_shipping_methods[]=$shipping_module_code.'_'.$shipping_method;
				
				$shipping_address = get_shipping_address($address_book_id);
		
				//setting $order->delivery from order.php class
				
				set_order_delivery($shipping_address);
				/*
			    $order->delivery = array('firstname' => $shipping_address->fields['entry_firstname'],
			                            'lastname' => $shipping_address->fields['entry_lastname'],
			                            'company' => $shipping_address->fields['entry_company'],
			                            'street_address' => $shipping_address->fields['entry_street_address'],
			                            'suburb' => $shipping_address->fields['entry_suburb'],
			                            'city' => $shipping_address->fields['entry_city'],
			                            'postcode' => $shipping_address->fields['entry_postcode'],
			                            'state' => ((zen_not_null($shipping_address->fields['entry_state'])) ? $shipping_address->fields['entry_state'] : $shipping_address->fields['zone_name']),
			                            'zone_id' => $shipping_address->fields['entry_zone_id'],
			                            'country' => array('id' => $shipping_address->fields['countries_id'], 'title' => $shipping_address->fields['countries_name'], 'iso_code_2' => $shipping_address->fields['countries_iso_code_2'], 'iso_code_3' => $shipping_address->fields['countries_iso_code_3']),
			                            'country_id' => $shipping_address->fields['entry_country_id'],
			                            'format_id' => (int)$shipping_address->fields['address_format_id']);
				*/
				//echo "<br>order->delivery:<br>";
				//var_dump($order->delivery);
				
				//calculate weight
				$q = "select products_weight from ".TABLE_PRODUCTS. " where products_id = " . (int) $product_id;
                
                //echo "<br> q = $q";

				$rs = mysql_query($q);
				$row = mysql_fetch_array($rs);

				$weight = $row['products_weight'];

				$weight_for_this_address = $weight*$quantity_for_this_address;
				

                //echo "<br>weight = $weight_for_this_address";
				
				 $total_count = $quantity_for_this_address;
				 $shipping_weight= $weight_for_this_address;
				 
				 $total_weight=$weight_for_this_address;
				 

                //before we get the quote, set the order_total to
                // the quantity times the price for each quote
                $price = get_product_price($product_id);
                
                $total_price = $price * $quantity_for_this_address;
                $order->info['subtotal'] = $total_price;
                

				 if(!$debug){
				 	$all_quotes[] = $shipping_modules->quote();  //this is slow
				 }
				 
				 // echo "<br><br>address: $address_book_id quote:<br>";
				 ///var_dump($quotes);
				
				
			 }
		 
		} else {
		 	$address_book_id = $address['addresses'][0]['address_book_id'];
			$quantity_for_this_address = $address['addresses'][0]['qty'];
			
			$shipping_module_code = $address['addresses'][0]['shipping_module_code'];
			$shipping_method = $address['addresses'][0]['shipping_method'];
			
			//echo "<br>howmany=1, book_id=$address_book_id, qty=$quantity_for_this_address";
			
			$product_address_id=$product_id.'_'.$address_book_id; //using this to name the shipping select boxes	
			$product_address_ids[]=$product_address_id;
			
			$address_ids[] = $address_book_id;
			
			$product_ids[] = $product_id;
			
			$quantities[] = $quantity_for_this_address;
				
			$shipping_module_codes[]=$shipping_module_code;
				
			$shipping_methods[]=$shipping_method;			
			
			$selected_shipping_methods[]=$shipping_module_code.'_'.$shipping_method;
			
			$shipping_address = get_shipping_address($address_book_id);
		
			set_order_delivery($shipping_address);
			
			//setting $order->delivery from order.php class
			
			/*
		    $order->delivery = array('firstname' => $shipping_address->fields['entry_firstname'],
		                            'lastname' => $shipping_address->fields['entry_lastname'],
		                            'company' => $shipping_address->fields['entry_company'],
		                            'street_address' => $shipping_address->fields['entry_street_address'],
		                            'suburb' => $shipping_address->fields['entry_suburb'],
		                            'city' => $shipping_address->fields['entry_city'],
		                            'postcode' => $shipping_address->fields['entry_postcode'],
		                            'state' => ((zen_not_null($shipping_address->fields['entry_state'])) ? $shipping_address->fields['entry_state'] : $shipping_address->fields['zone_name']),
		                            'zone_id' => $shipping_address->fields['entry_zone_id'],
		                            'country' => array('id' => $shipping_address->fields['countries_id'], 'title' => $shipping_address->fields['countries_name'], 'iso_code_2' => $shipping_address->fields['countries_iso_code_2'], 'iso_code_3' => $shipping_address->fields['countries_iso_code_3']),
		                            'country_id' => $shipping_address->fields['entry_country_id'],
		                            'format_id' => (int)$shipping_address->fields['address_format_id']);
			*/
			//echo "<br>order->delivery:<br>";
			//var_dump($order->delivery);
			
			//calculate weight
			$q = "select products_weight from ".TABLE_PRODUCTS. " where products_id = " . (int) $product_id;
			 
            //echo "<br>q  = $q";

            $rs = mysql_query($q);
			$row = mysql_fetch_array($rs);
			$weight = $row['products_weight'];
			$weight_for_this_address = $weight*$quantity_for_this_address;
			//echo "<br>weight = $weight_for_this_address";
			
            //echo "<br>weight = $weight_for_this_address";

			 $total_count = $quantity_for_this_address;
			 $shipping_weight= $weight_for_this_address;
			 
			 $total_weight=$weight_for_this_address;
             
             $price = get_product_price($product_id);
             $total_price = $price * $quantity_for_this_address;
             $order->info['subtotal'] = $total_price;			 


			 if(!$debug){
			 	$all_quotes[] = $shipping_modules->quote();  //this is slow
			 }
			 //echo "<br><br>address: $address_book_id quote:<br>";
			 //var_dump($quotes);
			
			//var_dump($row);
		
		}
	
	}

//var_dump($all_quotes);

} else { //process POST
	
	$form_errors=0;
	
	//var_dump($_SESSION['multiple_addresses']);
	
	//var_dump($_POST['shipping']);
	
	//loop through shipping post array
	
	// --- STILL!!! needs error-handling
	
	//list($module, $method) = explode('_', $_POST['shipping'][0]);
	
	//$payment_module_code = (strpos($_POST['shipping'][0], '_') > 0 ? substr($_POST['shipping'][0], 0, strpos($this->info['shipping_module_code'], '_')) : $this->info['shipping_module_code']);
	
	//$j=0;
	//loop through multiple_addresses, assigning shipping methods from POST,
	// pretty much going through this loop every time for everything with this mod
	foreach ($_SESSION['multiple_addresses'] as $product_id=>$address){
 
		 //echo "<br>product_id=$product_id";
		 
		 $how_many = $address['how_many_addresses'];
		 
		 if ($how_many>1){
		 	for($i=0;$i<$how_many;$i++){
		 		
				$address_book_id = $address['addresses'][$i]['address_book_id'];
				$quantity_for_this_address = $address['addresses'][$i]['qty'];
				//echo "<br>book_id=$address_book_id, qty=$quantity_for_this_address";
				
				$product_address_id=$product_id.'_'.$address_book_id; //using this to name the shipping select boxes
				
				//echo "<br>product_address_id = $product_address_id";
				
				if(!($_POST['shipping_'.$product_address_id])){
					//error
					$messageStack->add('checkout_multiple_shipments', ERROR_MULTIPLE_SHIPMENTS_SELECT, 'error');
					//die('unset field');
					//zen_redirect(zen_href_link(FILENAME_CHECKOUT_MULTIPLE_SHIPMENTS, '', 'SSL'));
					$form_errors=1;
				}
				
				$shipping=mysql_real_escape_string($_POST['shipping_'.$product_address_id]);  //needs input filtering
				
				$product_address_ids[]=$product_address_id;
				
				$shipping_address = get_shipping_address($address_book_id);
		
				//setting $order->delivery from order.php class
				
				set_order_delivery($shipping_address);
				
		 		//echo "<br>post, shipping = ".$_POST['shipping_'.$product_address_id];
				
				//do the thing here
				list($module, $method) = explode('_', $shipping);
				
				//calculate weight
				$q = "select products_weight from ".TABLE_PRODUCTS. " where products_id = " . (int) $product_id;
				$rs = mysql_query($q);
				$row = mysql_fetch_array($rs);
				$weight = $row['products_weight'];
				$weight_for_this_address = $weight*$quantity_for_this_address;
				//echo "<br>weight = $weight_for_this_address";
				
				$total_count = $quantity_for_this_address;
				$shipping_weight= $weight_for_this_address;
				 
				$total_weight=$weight_for_this_address;
				
				
				$quote = $shipping_modules->quote($method, $module);
				
				//var_dump($quote);
				/// -----------change to multidimensional array
				//$_SESSION['shipping'] = array('id' => $_SESSION['shipping'],
                //                'title' => (($free_shipping == true) ?  $quote[0]['methods'][0]['title'] : $quote[0]['module'] . ' (' . $quote[0]['methods'][0]['title'] . ')'),
                //               'cost' => $quote[0]['methods'][0]['cost']);
				
				//$j+=1;
				//here we have the shipping info to put into the db
//die('!!!');
				$q = "update ". TABLE_MULTIPLE_ADDRESSES_CUSTOMERS_BASKETS_PRODUCTS_ORDERS . 
				" set shipping_method = '".$quote[0]['methods'][0]['id'] ."', shipping_module_code = '".$quote[0]['id'].
				"', cost = '".$quote[0]['methods'][0]['cost']."', shipping_method_title='".$quote[0]['methods'][0]['title'].
				"' where products_id = '$product_id' and address_book_id = '$address_book_id' ".
				" and customers_id = ". $_SESSION['customer_id']." and orders_id is NULL";
				
				//echo "<br>395: q=$q<br>";
				$db->Execute($q);
				
				//set $_SESSION shipping method here too
				$_SESSION['multiple_addresses'][$product_id]['addresses'][$i]['shipping_method'] = $quote[0]['methods'][0]['id'];
				$_SESSION['multiple_addresses'][$product_id]['addresses'][$i]['shipping_module_code'] = $quote[0]['id'];
				$_SESSION['multiple_addresses'][$product_id]['addresses'][$i]['cost'] = $quote[0]['methods'][0]['cost'];
				$_SESSION['multiple_addresses'][$product_id]['addresses'][$i]['shipping_method_title'] = $quote[0]['methods'][0]['title'];
			}
			
			
		 } else {
		 		
				//echo "how many > 2";
				//echo "<br> product_id = $product_id";
			 	$address_book_id = $address['addresses'][0]['address_book_id'];
			 	
				//echo "<br> address_book_id = $address_book_id";
				
				$quantity_for_this_address = $address['addresses'][0]['qty'];
				//echo "<br>howmany=1, book_id=$address_book_id, qty=$quantity_for_this_address";
				
				$product_address_id=$product_id.'_'.$address_book_id; //using this to name the shipping select boxes	
				
				//echo "<br>product_address_id = $product_address_id";
				
				$shipping=$_POST['shipping_'.$product_address_id];
				
				//echo "<br> shipping = $shipping";
				
				if(!($_POST['shipping_'.$product_address_id])){
					//error
					$messageStack->add('checkout_multiple_shipments', ERROR_MULTIPLE_SHIPMENTS_SELECT, 'error');
					//die('unset field');
					$form_errors=1;
					//zen_redirect(zen_href_link(FILENAME_CHECKOUT_MULTIPLE_SHIPMENTS, '', 'SSL'));
					//die('unset field');
				}
				
				$product_address_ids[]=$product_address_id;
			
				$shipping_address = get_shipping_address($address_book_id);
			
				set_order_delivery($shipping_address);

				//echo "<br>post, shipping = ".$_POST['shipping_'.$product_address_id];
				
				list($module, $method) = explode('_', $shipping);
				
				//calculate weight
				$q = "select products_weight from ".TABLE_PRODUCTS. " where products_id = " . (int) $product_id;
				$rs = mysql_query($q);
				$row = mysql_fetch_array($rs);
				$weight = $row['products_weight'];
				$weight_for_this_address = $weight*$quantity_for_this_address;
				//echo "<br>weight = $weight_for_this_address";
				
				$total_count = $quantity_for_this_address;
				$shipping_weight= $weight_for_this_address;
				 
				$total_weight=$weight_for_this_address;
				
				
				$quote = $shipping_modules->quote($method, $module);
				
				//var_dump($quote);
				
				//here we have the shipping info to put into the db
				$q = "update ". TABLE_MULTIPLE_ADDRESSES_CUSTOMERS_BASKETS_PRODUCTS_ORDERS . 
				" set shipping_method = '".$quote[0]['methods'][0]['id'] ."', shipping_module_code = '".$quote[0]['id'].
				"', cost = '".$quote[0]['methods'][0]['cost']."', shipping_method_title='".$quote[0]['methods'][0]['title'].
				"' where products_id = '$product_id'  ".
				" and customers_id = ". $_SESSION['customer_id']." and orders_id is NULL";
				
                //not sure if this next line is necessary in the above query::
            //and address_book_id = '$address_book_id'

				//echo "<br>442: q=$q<br>";
				
/*
 * 
 *               $_SESSION['shipping'] = array('id' => $_SESSION['shipping'],
                                'title' => (($free_shipping == true) ?  $quote[0]['methods'][0]['title'] : $quote[0]['module'] . ' (' . $quote[0]['methods'][0]['title'] . ')'),
                                'cost' => $quote[0]['methods'][0]['cost']);
 * 
 */ 
				
				//echo "q=$q";
				//should be ok
				$db->Execute($q);
				
				$_SESSION['multiple_addresses'][$product_id]['addresses'][0]['shipping_method'] = $quote[0]['methods'][0]['id'];
				$_SESSION['multiple_addresses'][$product_id]['addresses'][0]['shipping_module_code'] = $quote[0]['id'];
				$_SESSION['multiple_addresses'][$product_id]['addresses'][0]['cost'] = $quote[0]['methods'][0]['cost'];
				$_SESSION['multiple_addresses'][$product_id]['addresses'][0]['shipping_method_title'] = $quote[0]['methods'][0]['title'];
			//$j+=1;
		 }
	
	
	}
	
	//$_POST['shipping']=$_POST['shipping'][0];
	
	// process the selected shipping method
	/*
  if ( isset($_POST['action']) && ($_POST['action'] == 'process') ) {
    if (zen_not_null($_POST['comments'])) {
      $_SESSION['comments'] = zen_db_prepare_input($_POST['comments']);
    }
    $comments = $_SESSION['comments'];
    $quote = array();

    if ( (zen_count_shipping_modules() > 0) || ($free_shipping == true) ) {
      if ( (isset($_POST['shipping'])) && (strpos($_POST['shipping'], '_')) ) {
        
         //check to be sure submitted data hasn't been tampered with
         
        if ($_POST['shipping'] == 'free_free' && ($order->content_type != 'virtual' && !$pass)) {
          $quote['error'] = 'Invalid input. Please make another selection.';
        } else {
          $_SESSION['shipping'] = $_POST['shipping'];
        }

        list($module, $method) = explode('_', $_SESSION['shipping']);
        if ( is_object($$module) || ($_SESSION['shipping'] == 'free_free') ) {
          if ($_SESSION['shipping'] == 'free_free') {
            $quote[0]['methods'][0]['title'] = FREE_SHIPPING_TITLE;
            $quote[0]['methods'][0]['cost'] = '0';
          } else {
          	echo "<br>getting quote here<br>";
			echo "module = $module, method=$method<br>";
            $quote = $shipping_modules->quote($method, $module);
          }
          if (isset($quote['error'])) {
            $_SESSION['shipping'] = '';
          } else {
            if ( (isset($quote[0]['methods'][0]['title'])) && (isset($quote[0]['methods'][0]['cost'])) ) {
              	echo "<br>quote already set?<br>";
				var_dump($quote);
              	
              $_SESSION['shipping'] = array('id' => $_SESSION['shipping'],
                                'title' => (($free_shipping == true) ?  $quote[0]['methods'][0]['title'] : $quote[0]['module'] . ' (' . $quote[0]['methods'][0]['title'] . ')'),
                                'cost' => $quote[0]['methods'][0]['cost']);
			 
			 
			 echo "<br>would be redirecting here.<br>";
			 var_dump($_SESSION['shipping']);
             // zen_redirect(zen_href_link(FILENAME_CHECKOUT_PAYMENT, '', 'SSL'));
            }
          }
        } else {
          $_SESSION['shipping'] = false;
        }
      }
    } else {
      $_SESSION['shipping'] = false;

      //zen_redirect(zen_href_link(FILENAME_CHECKOUT_PAYMENT, '', 'SSL'));
    }
  }
	*/
	
	//die();
	
	
	//redirect to payment from here -- if no errors
	
	if(isset($_POST['btn_back_x'])){
            
        //die('btn_back_x is set');
    
		zen_redirect(zen_href_link(FILENAME_CHECKOUT_MULTIPLE_ADDRESSES, '', 'SSL'));
	} else {
		if(!$form_errors){
            
            //die('redirect to payment');
            $_SESSION['shipping'] = 'multiple_addresses';
			zen_redirect(zen_href_link(FILENAME_CHECKOUT_PAYMENT, '', 'SSL'));
		} else {
			
             //die('form errors is set???');

			//go through all the no post stuff again
			foreach ($_SESSION['multiple_addresses'] as $product_id=>$address){
	 
	//echo "<br>no post, product_id=$product_id";
	 
	$how_many = $address['how_many_addresses'];
	 

		if ($how_many>1){
	 	 
			for($i=0;$i<$how_many;$i++){

                

			 	$address_book_id = $address['addresses'][$i]['address_book_id'];
				$quantity_for_this_address = $address['addresses'][$i]['qty'];
				
				$shipping_module_code = $address['addresses'][$i]['shipping_module_code'];
				$shipping_method = $address['addresses'][$i]['shipping_method'];
				
				//echo "<br>book_id=$address_book_id, qty=$quantity_for_this_address";
				
				$product_address_id=$product_id.'_'.$address_book_id; //using this to name the shipping select boxes
				
				$product_address_ids[]=$product_address_id;
				
				$address_ids[] = $address_book_id;
				
				$product_ids[] = $product_id;
				
				$quantities[] = $quantity_for_this_address;
				
				$shipping_module_codes[]=$shipping_module_code;
				
				$shipping_methods[]=$shipping_method;
				
				$selected_shipping_methods[]=$shipping_module_code.'_'.$shipping_method;
				
				$shipping_address = get_shipping_address($address_book_id);
		
				//setting $order->delivery from order.php class
				
				set_order_delivery($shipping_address);
				/*
			    $order->delivery = array('firstname' => $shipping_address->fields['entry_firstname'],
			                            'lastname' => $shipping_address->fields['entry_lastname'],
			                            'company' => $shipping_address->fields['entry_company'],
			                            'street_address' => $shipping_address->fields['entry_street_address'],
			                            'suburb' => $shipping_address->fields['entry_suburb'],
			                            'city' => $shipping_address->fields['entry_city'],
			                            'postcode' => $shipping_address->fields['entry_postcode'],
			                            'state' => ((zen_not_null($shipping_address->fields['entry_state'])) ? $shipping_address->fields['entry_state'] : $shipping_address->fields['zone_name']),
			                            'zone_id' => $shipping_address->fields['entry_zone_id'],
			                            'country' => array('id' => $shipping_address->fields['countries_id'], 'title' => $shipping_address->fields['countries_name'], 'iso_code_2' => $shipping_address->fields['countries_iso_code_2'], 'iso_code_3' => $shipping_address->fields['countries_iso_code_3']),
			                            'country_id' => $shipping_address->fields['entry_country_id'],
			                            'format_id' => (int)$shipping_address->fields['address_format_id']);
				*/
				//echo "<br>order->delivery:<br>";
				//var_dump($order->delivery);
				
				//calculate weight
				$q = "select products_weight from ".TABLE_PRODUCTS. " where products_id = $product_id";
				$rs = mysql_query($q);
				$row = mysql_fetch_array($rs);
				$weight = $row['products_weight'];
				$weight_for_this_address = $weight*$quantity_for_this_address;
				//echo "<br>weight = $weight_for_this_address";
				
				 $total_count = $quantity_for_this_address;
				 $shipping_weight= $weight_for_this_address;
				 
				 $total_weight=$weight_for_this_address;
				 
                $price = get_product_price($product_id);
                $total_price = $price * $quantity_for_this_address;
                $order->info['subtotal'] = $total_price;

				 if(!$debug){
				 	$all_quotes[] = $shipping_modules->quote();  //this is slow
				 }
				 
				 // echo "<br><br>address: $address_book_id quote:<br>";
				 ///var_dump($quotes);
				
				
			 }
		 
		} else {
		 	$address_book_id = $address['addresses'][0]['address_book_id'];
			$quantity_for_this_address = $address['addresses'][0]['qty'];
			
			$shipping_module_code = $address['addresses'][0]['shipping_module_code'];
			$shipping_method = $address['addresses'][0]['shipping_method'];
			
			//echo "<br>howmany=1, book_id=$address_book_id, qty=$quantity_for_this_address";
			
			$product_address_id=$product_id.'_'.$address_book_id; //using this to name the shipping select boxes	
			$product_address_ids[]=$product_address_id;
			
			$address_ids[] = $address_book_id;
			
			$product_ids[] = $product_id;
			
			$quantities[] = $quantity_for_this_address;
				
			$shipping_module_codes[]=$shipping_module_code;
				
			$shipping_methods[]=$shipping_method;			
			
			$selected_shipping_methods[]=$shipping_module_code.'_'.$shipping_method;
			
			$shipping_address = get_shipping_address($address_book_id);
		
			set_order_delivery($shipping_address);
			
			//setting $order->delivery from order.php class
			
			/*
		    $order->delivery = array('firstname' => $shipping_address->fields['entry_firstname'],
		                            'lastname' => $shipping_address->fields['entry_lastname'],
		                            'company' => $shipping_address->fields['entry_company'],
		                            'street_address' => $shipping_address->fields['entry_street_address'],
		                            'suburb' => $shipping_address->fields['entry_suburb'],
		                            'city' => $shipping_address->fields['entry_city'],
		                            'postcode' => $shipping_address->fields['entry_postcode'],
		                            'state' => ((zen_not_null($shipping_address->fields['entry_state'])) ? $shipping_address->fields['entry_state'] : $shipping_address->fields['zone_name']),
		                            'zone_id' => $shipping_address->fields['entry_zone_id'],
		                            'country' => array('id' => $shipping_address->fields['countries_id'], 'title' => $shipping_address->fields['countries_name'], 'iso_code_2' => $shipping_address->fields['countries_iso_code_2'], 'iso_code_3' => $shipping_address->fields['countries_iso_code_3']),
		                            'country_id' => $shipping_address->fields['entry_country_id'],
		                            'format_id' => (int)$shipping_address->fields['address_format_id']);
			*/
			//echo "<br>order->delivery:<br>";
			//var_dump($order->delivery);
			
			//calculate weight
			$q = "select products_weight from ".TABLE_PRODUCTS. " where products_id = $product_id";
			$rs = mysql_query($q);
			$row = mysql_fetch_array($rs);
			$weight = $row['products_weight'];
			$weight_for_this_address = $weight*$quantity_for_this_address;
			//echo "<br>weight = $weight_for_this_address";
			
			 $total_count = $quantity_for_this_address;
			 $shipping_weight= $weight_for_this_address;
			 
			 $total_weight=$weight_for_this_address;
			 
             $price = get_product_price($product_id);
                $total_price = $price * $quantity_for_this_address;
                $order->info['subtotal'] = $total_price;
            
  

			 if(!$debug){
			 	$all_quotes[] = $shipping_modules->quote();  //this is slow
			 }
			 //echo "<br><br>address: $address_book_id quote:<br>";
			 //var_dump($quotes);
			
			//var_dump($row);
		
		}
	
	}
			
			
			
		}
	}

}  //if POST


$order->delivery = $order_delivery_tmp; //restore order




/* ----- not sure how to handle free shipping yet ----*/

/*
  $pass = true;
  if ( defined('MODULE_ORDER_TOTAL_SHIPPING_FREE_SHIPPING') && (MODULE_ORDER_TOTAL_SHIPPING_FREE_SHIPPING == 'true') ) {
    $pass = false;

    switch (MODULE_ORDER_TOTAL_SHIPPING_DESTINATION) {
      case 'national':
        if ($order->delivery['country_id'] == STORE_COUNTRY) {
          $pass = true;
        }
        break;
      case 'international':
        if ($order->delivery['country_id'] != STORE_COUNTRY) {
          $pass = true;
        }
        break;
      case 'both':
        $pass = true;
        break;
    }

    $free_shipping = false;
    if ( ($pass == true) && ($_SESSION['cart']->show_total() >= MODULE_ORDER_TOTAL_SHIPPING_FREE_SHIPPING_OVER) ) {
      $free_shipping = true;
    }
  } else {
    $free_shipping = false;
  }
*/

  require(DIR_WS_MODULES . zen_get_module_directory('require_languages.php'));
  
  
  //Multiple Addresses Mod, include checkout_steps files for checkout_* pages	
	
	//echo "<b>requiring: " .DIR_WS_MODULES . zen_get_module_directory('require_languages.php')."<br>";
	
  if (isset($_SESSION['comments'])) {
    $comments = $_SESSION['comments'];
  }




// process the selected shipping method
/*
  if ( isset($_POST['action']) && ($_POST['action'] == 'process') ) {
    if (zen_not_null($_POST['comments'])) {
      $_SESSION['comments'] = zen_db_prepare_input($_POST['comments']);
    }
    $comments = $_SESSION['comments'];
    $quote = array();

    if ( (zen_count_shipping_modules() > 0) || ($free_shipping == true) ) {
      if ( (isset($_POST['shipping'])) && (strpos($_POST['shipping'], '_')) ) {

 
      //check to be sure submitted data hasn't been tampered with
         
        if ($_POST['shipping'] == 'free_free' && ($order->content_type != 'virtual' && !$pass)) {
          $quote['error'] = 'Invalid input. Please make another selection.';
        } else {
          $_SESSION['shipping'] = $_POST['shipping'];
        }

        list($module, $method) = explode('_', $_SESSION['shipping']);
        if ( is_object($$module) || ($_SESSION['shipping'] == 'free_free') ) {
          if ($_SESSION['shipping'] == 'free_free') {
            $quote[0]['methods'][0]['title'] = FREE_SHIPPING_TITLE;
            $quote[0]['methods'][0]['cost'] = '0';
          } else {
            $quote = $shipping_modules->quote($method, $module);
          }
          if (isset($quote['error'])) {
            $_SESSION['shipping'] = '';
          } else {
            if ( (isset($quote[0]['methods'][0]['title'])) && (isset($quote[0]['methods'][0]['cost'])) ) {
              $_SESSION['shipping'] = array('id' => $_SESSION['shipping'],
                                'title' => (($free_shipping == true) ?  $quote[0]['methods'][0]['title'] : $quote[0]['module'] . ' (' . $quote[0]['methods'][0]['title'] . ')'),
                                'cost' => $quote[0]['methods'][0]['cost']);

              zen_redirect(zen_href_link(FILENAME_CHECKOUT_PAYMENT, '', 'SSL'));
            }
          }
        } else {
          $_SESSION['shipping'] = false;
        }
      }
    } else {
      $_SESSION['shipping'] = false;

      zen_redirect(zen_href_link(FILENAME_CHECKOUT_PAYMENT, '', 'SSL'));
    }
  }
*/

// get all available shipping quotes
//$shipping_weight=4;
//$total_weight=4;

 // $quotes = $shipping_modules->quote();

//var_dump($quotes);

// if no shipping method has been selected, automatically select the cheapest method.
// if the modules status was changed when none were available, to save on implementing
// a javascript force-selection method, also automatically select the cheapest shipping
// method if more than one module is now enabled
  if ( !$_SESSION['shipping'] || ( $_SESSION['shipping'] && ($_SESSION['shipping'] == false) && (zen_count_shipping_modules() > 1) ) ) $_SESSION['shipping'] = $shipping_modules->cheapest();



//echo "<br>products:";
//var_dump($products);

for ($i=0, $n=sizeof($products); $i<$n; $i++) {
  if (($i/2) == floor($i/2)) {
    $rowClass="rowEven";
  } else {
    $rowClass="rowOdd";
  }
  
  switch (true) {
    case (SHOW_SHOPPING_CART_DELETE == 1):
    $buttonDelete = true;
    $checkBoxDelete = false;
    break;
    case (SHOW_SHOPPING_CART_DELETE == 2):
    $buttonDelete = false;
    $checkBoxDelete = true;
    break;
    default:
    $buttonDelete = true;
    $checkBoxDelete = true;
    break;
    $cur_row++;
  } // end switch
  $attributeHiddenField = "";
  $attrArray = false;
  $productsName = $products[$i]['name'];
  
  
  /*
  // Push all attributes information in an array
  
  if (isset($products[$i]['attributes']) && is_array($products[$i]['attributes'])) {
    if (PRODUCTS_OPTIONS_SORT_ORDER=='0') {
      $options_order_by= ' ORDER BY LPAD(popt.products_options_sort_order,11,"0")';
    } else {
      $options_order_by= ' ORDER BY popt.products_options_name';
    }
	
	
    foreach ($products[$i]['attributes'] as $option => $value) {
      $attributes = "SELECT popt.products_options_name, poval.products_options_values_name, pa.options_values_price, pa.price_prefix
                     FROM " . TABLE_PRODUCTS_OPTIONS . " popt, " . TABLE_PRODUCTS_OPTIONS_VALUES . " poval, " . TABLE_PRODUCTS_ATTRIBUTES . " pa
                     WHERE pa.products_id = :productsID
                     AND pa.options_id = :optionsID
                     AND pa.options_id = popt.products_options_id
                     AND pa.options_values_id = :optionsValuesID
                     AND pa.options_values_id = poval.products_options_values_id
                     AND popt.language_id = :languageID
                     AND poval.language_id = :languageID " . $options_order_by;

      $attributes = $db->bindVars($attributes, ':productsID', $products[$i]['id'], 'integer');
      $attributes = $db->bindVars($attributes, ':optionsID', $option, 'integer');
      $attributes = $db->bindVars($attributes, ':optionsValuesID', $value, 'integer');
      $attributes = $db->bindVars($attributes, ':languageID', $_SESSION['languages_id'], 'integer');
      $attributes_values = $db->Execute($attributes);
      //clr 030714 determine if attribute is a text attribute and assign to $attr_value temporarily
      if ($value == PRODUCTS_OPTIONS_VALUES_TEXT_ID) {
        $attributeHiddenField .= zen_draw_hidden_field('id[' . $products[$i]['id'] . '][' . TEXT_PREFIX . $option . ']',  $products[$i]['attributes_values'][$option]);
        $attr_value = htmlspecialchars($products[$i]['attributes_values'][$option], ENT_COMPAT, CHARSET, TRUE);
      } else {
        $attributeHiddenField .= zen_draw_hidden_field('id[' . $products[$i]['id'] . '][' . $option . ']', $value);
        $attr_value = $attributes_values->fields['products_options_values_name'];
      }

      $attrArray[$option]['products_options_name'] = $attributes_values->fields['products_options_name'];
      $attrArray[$option]['options_values_id'] = $value;
      $attrArray[$option]['products_options_values_name'] = $attr_value;
      $attrArray[$option]['options_values_price'] = $attributes_values->fields['options_values_price'];
      $attrArray[$option]['price_prefix'] = $attributes_values->fields['price_prefix'];
    }
  } //end foreach [attributes]
  
  */
 
 /*
  
  if (STOCK_CHECK == 'true') {
    $flagStockCheck = zen_check_stock($products[$i]['id'], $products[$i]['quantity']);
    if ($flagStockCheck == true) {
      $flagAnyOutOfStock = true;
    }
  }
  */
  
  
  $linkProductsImage = zen_href_link(zen_get_info_page($products[$i]['id']), 'products_id=' . $products[$i]['id']);
  $linkProductsName = zen_href_link(zen_get_info_page($products[$i]['id']), 'products_id=' . $products[$i]['id']);
  $productsImage = (IMAGE_SHOPPING_CART_STATUS == 1 ? zen_image(DIR_WS_IMAGES . $products[$i]['image'], $products[$i]['name'], IMAGE_SHOPPING_CART_WIDTH, IMAGE_SHOPPING_CART_HEIGHT) : '');
  $show_products_quantity_max = zen_get_products_quantity_order_max($products[$i]['id']);
  $showFixedQuantity = (($show_products_quantity_max == 1 or zen_get_products_qty_box_status($products[$i]['id']) == 0) ? true : false);
//  $showFixedQuantityAmount = $products[$i]['quantity'] . zen_draw_hidden_field('products_id[]', $products[$i]['id']) . zen_draw_hidden_field('cart_quantity[]', 1);
//  $showFixedQuantityAmount = $products[$i]['quantity'] . zen_draw_hidden_field('cart_quantity[]', 1);
  $showFixedQuantityAmount = $products[$i]['quantity'] . zen_draw_hidden_field('cart_quantity[]', $products[$i]['quantity']);
  $showMinUnits = zen_get_products_quantity_min_units_display($products[$i]['id']);
  
  
  $quantityField = zen_draw_input_field('cart_quantity[]', $products[$i]['quantity'], 'size="4"');
  
  //$buttonUpdate = ((SHOW_SHOPPING_CART_UPDATE == 1 or SHOW_SHOPPING_CART_UPDATE == 3) ? zen_image_submit(ICON_IMAGE_UPDATE, ICON_UPDATE_ALT) : '') . zen_draw_hidden_field('products_id[]', $products[$i]['id']);
  
  //move to appropriate file
  define('ICON_UPDATE_MULTIPLE_ADDRESSES_ALT', 'Change the number of addresses to ship this product to by highlighting the number in the box, correcting the number and clicking this button.');
  
  //$buttonUpdate = zen_image_submit(ICON_IMAGE_UPDATE, ICON_UPDATE_MULTIPLE_ADDRESSES_ALT);
  
  
  $tmp =  zen_add_tax($products[$i]['final_price'],zen_get_tax_rate($products[$i]['tax_class_id']));
//  $productsPriceEach = $currencies->rateAdjusted($tmp);
//  $productsPriceTotal = $productsPriceEach * $products[$i]['quantity'];
  $productsPriceTotal = $currencies->display_price($products[$i]['final_price'], zen_get_tax_rate($products[$i]['tax_class_id']), $products[$i]['quantity']) . ($products[$i]['onetime_charges'] != 0 ? '<br />' . $currencies->display_price($products[$i]['onetime_charges'], zen_get_tax_rate($products[$i]['tax_class_id']), 1) : '');
  $productsPriceEach = $currencies->display_price($products[$i]['final_price'], zen_get_tax_rate($products[$i]['tax_class_id']), 1) . ($products[$i]['onetime_charges'] != 0 ? '<br />' . $currencies->display_price($products[$i]['onetime_charges'], zen_get_tax_rate($products[$i]['tax_class_id']), 1) : '');
//  $productsPriceTotal = $currencies->display_price($products[$i]['final_price'], zen_get_tax_rate($products[$i]['tax_class_id']), $products[$i]['quantity']) . ($products[$i]['onetime_charges'] != 0 ? '<br />' . $currencies->display_price($products[$i]['onetime_charges'], zen_get_tax_rate($products[$i]['tax_class_id']), 1) : '');
//  echo  $currencies->rateAdjusted($tmp);
  $productArray[(int)$products[$i]['id']] = array('attributeHiddenField'=>$attributeHiddenField,
                            'flagStockCheck'=>$flagStockCheck,
                            'flagShowFixedQuantity'=>$showFixedQuantity,
                            'linkProductsImage'=>$linkProductsImage,
                            'linkProductsName'=>$linkProductsName,
                            'productsImage'=>$productsImage,
                            'productsName'=>$productsName,
                            'showFixedQuantity'=>$showFixedQuantity,
                            'showFixedQuantityAmount'=>$showFixedQuantityAmount,
                            'showMinUnits'=>$showMinUnits,
                            'quantity'=>$products[$i]['quantity'],
                            'quantityField'=>$quantityField,
                            'buttonUpdate'=>$buttonUpdate,
                            'productsPrice'=>$productsPriceTotal,
                            'productsPriceEach'=>$productsPriceEach,
                            'rowClass'=>$rowClass,
                            'buttonDelete'=>$buttonDelete,
                            'checkBoxDelete'=>$checkBoxDelete,
                            'id'=>$products[$i]['id'],
                            'weight'=>$products[$i]['weight'],
                            'attributes'=>$attrArray);
} // end FOR loop


//var_dump($productArray);


$breadcrumb->add(NAVBAR_TITLE_1, zen_href_link(FILENAME_CHECKOUT_SHIPPING, '', 'SSL'));
  $breadcrumb->add(NAVBAR_TITLE_2);

?>