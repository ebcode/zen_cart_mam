<?php
//ini_set('display_errors',1);

//var_dump($_POST);
//var_dump($_SESSION);
//echo "<br><br>";
//die();

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



//echo "<br>ref=".$_GET['ref'];
if($_GET['ref']==FILENAME_CHECKOUT_SHIPPING){
	$q = "select * from " . TABLE_MULTIPLE_ADDRESSES_MOD . " where customers_id = " . $_SESSION['customer_id'];
	
	$rs = mysql_query($q) or die(mysql_error());
	
	if(!mysql_num_rows($rs)){
	
		$q = "insert into " . TABLE_MULTIPLE_ADDRESSES_MOD ." values(".$_SESSION['customer_id'].")";
		//echo "<br>q=$q";
		$db->Execute($q);
		
	}
	
	if(isset($_SESSION['multiple_addresses_tmp'])){
		$_SESSION['multiple_addresses']=$_SESSION['multiple_addresses_tmp']; //restore 
	}
	
}

if($_GET['hm']){
	die('hm');
}

require(DIR_WS_MODULES . zen_get_module_directory('require_languages.php'));

//var_dump($_SESSION);

//$products = $_SESSION['cart']->get_products();

$products = $_SESSION['cart']->get_products();

//var_dump($_SESSION['cart']->contents);

//var_dump($products);

//echo "<br>array_keys=".var_dump(array_keys($_SESSION['cart']->contents));

$product_ids = array_keys($_SESSION['cart']->contents);

//var_dump($product_ids);

foreach($product_ids as $k => $v){
	//$product_ids[$k]=(int)$v; //for using $multiple_addresses[$product_ids[$i]] index
    $product_ids[$k]=$v;
}

$num_products = count($products);



$shipToSingleAddressLink = zen_href_link(FILENAME_CHECKOUT_SHIPPING.'&ref='.FILENAME_CHECKOUT_MULTIPLE_ADDRESSES, '', 'SSL');

//var_dump($products);
//get addresses from address book
$addresses_query = "SELECT address_book_id, entry_title, entry_firstname as firstname, entry_lastname as lastname,
                           entry_company as company, entry_street_address as street_address,
                           entry_suburb as suburb, entry_city as city, entry_postcode as postcode,
                           entry_state as state, entry_zone_id as zone_id, entry_country_id as country_id
                    FROM   " . TABLE_ADDRESS_BOOK . "
                    WHERE  customers_id = :customersID
                    ORDER BY firstname, lastname";

$addresses_query = $db->bindVars($addresses_query, ':customersID', $_SESSION['customer_id'], 'integer');
$addresses = $db->Execute($addresses_query);

$num_address_book_entries=$addresses->RecordCount();

while (!$addresses->EOF) {
  $format_id = zen_get_address_format_id($addresses->fields['country_id']);

  $addressArray[] = array('firstname'=>$addresses->fields['firstname'],
  'lastname'=>$addresses->fields['lastname'],
  'address_book_id'=>$addresses->fields['address_book_id'],
  'format_id'=>$format_id,
  'address'=>$addresses->fields,
  'entry_title'=>$addreses->fields['entry_title']);

  $addresses->MoveNext();
  
}

//build address book select boxes

function address_select_box($input_id,$selected_id){
	global $addressArray;	
	$address_book_select = '<select name="address_book_id_'.$input_id.'[]"><option value=""> - select - </option>';
	foreach($addressArray as $address){  //determine selected from session/database?
		
        

        $address_book_select .= '<option value="'.$address['address']['address_book_id'].'"';
		
		if($address['address']['address_book_id']==$selected_id){
			$address_book_select .= ' selected="selected" ';
		}
		
		$address_book_select .= '>'.
		$address['address']['entry_title'].'</option>';
	}
	$address_book_select .='</select>';
	return $address_book_select;
}

$address_select = array();

//build default address boxes

if(isset($_SESSION['multiple_addresses'])){
	
	for($i=0;$i<$num_products;$i++){
		
		$how_many_addresses = $_SESSION['multiple_addresses'][$product_ids[$i]]['how_many_addresses'];
		
		//echo "<br>";
		//var_dump($_SESSION['multiple_addresses'][$product_ids[$i]]['how_many_addresses']);
		//echo "<br>";
		
		//echo "<br>i=$i, how many = $how_many_addresses, product_id = ".$product_ids[$i];
		//loop through addresses and quantities
		
		if(!$how_many_addresses){
			$address_select[] = address_select_box($i, $addressArray[0]['address_book_id']);
		} else {
			for($j=0;$j<$how_many_addresses;$j++){
	
				if($how_many_addresses>1){
					
					$this_address = $_SESSION['multiple_addresses'][$product_ids[$i]]['addresses'][$j]['address_book_id'];					
					
				} else {
					
					$this_address = $_SESSION['multiple_addresses'][$product_ids[$i]]['addresses']['address_book_id'];
	
				}
			
				$address_select[] = address_select_box($i, $this_address);		
			}
		}
		
		
	}
	
} else {
	
	for($i=0;$i<$num_products;$i++){
		$address_select[] = address_select_box($i, $addressArray[0]['address_book_id']);
	}

}


//var_dump($address_select);

$address_select_tmp=array();

//handle form submission
if(count($_POST)){

	$form_errors=0;	
	$address_select_index=0;
	
	//loop through products
	 for($i=0;$i<$num_products;$i++){
		
		 //how many addresses to ship to?
		$how_many_addresses = isset($_POST['how_many_addresses_'.$i])?(int)$_POST['how_many_addresses_'.$i]:1;
		
		//echo "<br>how many = $how_many_addresses";
		
		if($how_many_addresses < 1){
			//$form_errors[]='more addresses than product quantity';			
			$messageStack->add('checkout_multiple_addresses', ERROR_ZERO_ADDRESSES, 'error');		
			$form_errors=1;
			//break;		
		}

		//may not ship to more addresses than quantity of products
		if($how_many_addresses > $products[$i]['quantity']){
			//$form_errors[]='more addresses than product quantity';
			
			$messageStack->add('checkout_multiple_addresses', ERROR_MORE_ADDRESSES_THAN_PRODUCT_QUANTITY, 'error');
			$form_errors=1;
			
		}

		//may not ship to more addresses than # of address book entries
		if($how_many_addresses > $num_address_book_entries){
			//echo "ERROR?";
			//$form_errors[]='more addresses than address book entries';
			$messageStack->add('checkout_multiple_addresses', ERROR_MORE_ADDRESSES_THAN_ADDRESS_BOOK_ENTRIES, 'error');
			$form_errors=1;
		}

		$details=array('how_many_addresses'=>$how_many_addresses);
		unset($which_addresses);
		$which_addresses=array();
		$running_total=0;
		$reset=0;
		
		if($_SESSION['multiple_addresses'][$product_ids[$i]]['how_many_addresses']!=$how_many_addresses){
			//echo "<br>reset quantities<br>";
			$reset=1;
		}
		
		$these_addresses=array();
		
		 //loop through addresses and quantities
		   for($j=0;$j<$how_many_addresses;$j++){
		   	
			  $this_address = (int)$_POST['address_book_id_'.$i][$j];  //address book id
			  $this_quantity = isset($_POST['qty_'.$i][$j])?(int)$_POST['qty_'.$i][$j]:1; 
			 
			 //echo "<br>i=$i, j=$j, this_address=$this_address";
			 
			  if($j>0){  //auto-select quantities and addresseses from address book
			  	
				if(!isset($_POST['qty_'.$i][$j]) || $reset){
					$running_total-=1;
					$details['addresses'][0]['qty']-=1;
					//echo "prev qty = ".$details['addresses'][0]['qty'];
					$this_quantity =1;
					
				}
				
				if(!isset($_POST['address_book_id_'.$i][$j]) || $reset){

					for($k=0;$k<$num_address_book_entries;$k++){
						$z=0;
						do{
						 	$this_address = $addressArray[$z]['address_book_id'];
						 	$z++;
						} while(in_array($this_address,$these_addresses) && $z<3);
					}				 
				}

			  } else {
			  	if($reset){
			  		$this_quantity = $products[$i]['quantity'];
			  	}
			  	
			  	 //if(!isset($_POST['qty_'.$i][$j])){
					//$running_total-=1;
					//$details['addresses'][0]['qty']-=1;
					//echo "prev qty = ".$details['addresses'][0]['qty'];
					//$this_quantity =1;
				 //}
				 
			  }	
			  
			  $running_total += $this_quantity;
			  
			  $which_addresses['address_book_id']=$this_address;
			  $which_addresses['qty']=$this_quantity;
			  
		  	  $which_addresses['shipping_method']='';
			  $which_addresses['shipping_module_code']='';
			  $which_addresses['cost']='';
			  $which_addresses['shipping_method_title']='';
			
			  
			  //check for saved shipping methods in $_SESSION
			  //echo "<br>shipping method = ".$_SESSION['multiple_addresses'][$product_ids[$i]]['addresses'][$j]['shipping_method'];
			  
			 if(isset($_SESSION['multiple_addresses'][$product_ids[$i]]['addresses'][$j]['shipping_method'])){
			 	
				//echo "<br>saving shipping methods, etc...";
			 	$which_addresses['shipping_method']=$_SESSION['multiple_addresses'][$product_ids[$i]]['addresses'][$j]['shipping_method'];
				$which_addresses['shipping_module_code']=$_SESSION['multiple_addresses'][$product_ids[$i]]['addresses'][$j]['shipping_module_code'];
				$which_addresses['cost']=$_SESSION['multiple_addresses'][$product_ids[$i]]['addresses'][$j]['cost'];
				$which_addresses['shipping_method_title']=$_SESSION['multiple_addresses'][$product_ids[$i]]['addresses'][$j]['shipping_method_title'];
				
			 }
			  
			  //var_dump($which_addresses);
			  
			  
			  $details['addresses'][]=$which_addresses;
			  
			  //echo "<br>this_address=$this_address<br>";
			  //echo "<br>this_quantity=$this_quantity<br>";
			  
			  //build pre-selected address select boxes here
			  $address_select_tmp[$address_select_index] = address_select_box($i, $this_address);
		  	  $address_select_index+=1;
			  
			  $these_addresses[]=$this_address;
		   }
		  
		  if($running_total != $products[$i]['quantity']){
		  	//echo "<br>running_total=$running_total and quantity=".$products[$i]['quantity']."<br>";
			  
		  	//echo "<BR><BR>I=$i<br>";
		  	
		  	$messageStack->add('checkout_multiple_addresses', ERROR_QUANTITY_MISMATCH, 'error');
			$form_errors=1;
		  }
		  
	  	//if($form_errors){
		//	break;			
		//}
		  
		  $multiple_addresses_tmp[$product_ids[$i]] = $details;
		  
	 }

	if($form_errors){
		$multiple_addresses = $_SESSION['multiple_addresses'];  //reset to sane values		
		
	} else {
		$multiple_addresses=$multiple_addresses_tmp;
		$address_select =	$address_select_tmp;
		
		//var_dump($multiple_addresses);
		//die();
	}

	//var_dump($address_select);

} else {  //no form submission, set default form values in $multiple_addresses array
	
	//echo "don't process form<br>";
	//unset($_SESSION['multiple_addresses']);
	
	if(isset($_SESSION['multiple_addresses'])){
			
		//echo "<br><b>session multiple addresses is set</b><br>";
			
		$multiple_addresses = $_SESSION['multiple_addresses'];
		
		//var_dump($multiple_addresses);
		
	} else {	
		$multiple_addresses = array();
			
		for($i=0;$i<$num_products;$i++){
			
			$details=array('how_many_addresses'=>1);
			
			$which_addresses=array();
			//$this_address = $addressArray[0]['address_book_id'];
			
			$which_addresses['address_book_id']=$addressArray[0]['address_book_id'];
			$which_addresses['qty']=$products[$i]['quantity'];	
			
			$details['addresses']=$which_addresses;
			
			//$address_select[] = address_select_box($i, $addressArray[0]['address_book_id']);
			
			$multiple_addresses[$product_ids[$i]] = $details;
		}
		
	}
	
}

if(count($form_errors)){
	
	
}

//unset($_SESSION['multiple_addresses']);
$_SESSION['multiple_addresses']=$multiple_addresses;

//var_dump($_SESSION['multiple_addresses']);

//should have sane values in $multiple_addresses array, save these to db

//check if these values have been saved yet
//$q = "select * from ". TABLE_MULTIPLE_ADDRESSES_MOD . " where customers_id = ".$_SESSION['customer_id'];
//echo "q=$q<br>";

//clear out existing baskets from orders_products_multiple_addresses

//.... FIX THIS!!!  ...may not need fixing after all
$q = "delete from ". TABLE_MULTIPLE_ADDRESSES_CUSTOMERS_BASKETS_PRODUCTS_ORDERS . " where customers_id = ".$_SESSION['customer_id'] ." and orders_id is NULL";
$db->Execute($q);

$q = "select customers_basket_id, products_id from ".TABLE_CUSTOMERS_BASKET. " where customers_id = ".$_SESSION['customer_id'];

$rs = $db->Execute($q);

$customer_baskets=array();

while(!$rs->EOF){
	
	$customer_baskets[] = $rs->fields;
	$rs->moveNext();
}

//build orders_products_multiple_addresses query  

$q='';
$i=0;

foreach($customer_baskets as $basket){

//var_dump($multiple_addresses[$product_ids[$i]]['addresses'][0]);
	
//echo "<br>count=".count(($multiple_addresses[$product_ids[$i]]['addresses']));

//echo "<br>address=".$multiple_addresses[$product_ids[$i]]['addresses']['address_book_id'];

//if(!isset($multiple_addresses[$product_ids[$i]]['addresses']['address_book_id'])){

if($multiple_addresses[$product_ids[$i]]['how_many_addresses']>1){
	
	foreach($multiple_addresses[$product_ids[$i]]['addresses'] as $address){
		
		$q = "insert into ".TABLE_MULTIPLE_ADDRESSES_CUSTOMERS_BASKETS_PRODUCTS_ORDERS . 
" (customers_basket_id, customers_id, products_id, address_book_id, products_quantity_for_this_address, shipping_method, ".
"shipping_module_code, shipping_method_title, cost) values ".
"('".$basket['customers_basket_id']."','".$_SESSION['customer_id']."', '".$product_ids[$i]."', ".
"'".$address['address_book_id']."','".$address['qty']."','".$address['shipping_method']."','"
.$address['shipping_module_code']."','".$address['shipping_method_title']."','".$address['cost']."')";

$db->Execute($q);
		//echo "394 q:$q<br>";
	}
	
} else {
		
	$q = "insert into ".TABLE_MULTIPLE_ADDRESSES_CUSTOMERS_BASKETS_PRODUCTS_ORDERS . 
" (customers_basket_id, customers_id, products_id, address_book_id, products_quantity_for_this_address, shipping_method, ".
"shipping_module_code, shipping_method_title, cost) values ".
"('".$basket['customers_basket_id']."','".$_SESSION['customer_id']."', '".$product_ids[$i]."', ".
"'".$multiple_addresses[$product_ids[$i]]['addresses'][0]['address_book_id']."','".$multiple_addresses[$product_ids[$i]]['addresses'][0]['qty'].
"','".$multiple_addresses[$product_ids[$i]]['addresses'][0]['shipping_method']."','".
$multiple_addresses[$product_ids[$i]]['addresses'][0]['shipping_module_code']."','".
$multiple_addresses[$product_ids[$i]]['addresses'][0]['shipping_method_title']."','".
$multiple_addresses[$product_ids[$i]]['addresses'][0]['cost']."')";

$db->Execute($q);
	//echo "404 q:$q<br>";
}

/*	
}	else {
	$q = "insert into ".TABLE_ORDERS_PRODUCTS_MULTIPLE_ADDRESSES . 
" (customers_basket_id, customers_id, products_id, address_book_id, products_quantity_for_this_address) values ".
"('".$basket['customers_basket_id']."','".$_SESSION['customer_id']."', '".$basket['products_id']."', ".
"'".$multiple_addresses[$product_ids[$i]]['addresses']['address_book_id']."','".$multiple_addresses[$product_ids[$i]]['addresses']['qty']."')";
$db->Execute($q);

echo "<br>407 q=$q<br>";
}
	*/

$i++;

}

//echo"<br><br>multiple_addresses=<br>";
//var_dump($multiple_addresses);

$buttonUpdate = zen_image_submit(ICON_IMAGE_UPDATE, ICON_UPDATE_ALT);



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

// Stock Check -- maybe not on this page?
/*
  if ( (STOCK_CHECK == 'true') && (STOCK_ALLOW_CHECKOUT != 'true') ) {
    $products = $_SESSION['cart']->get_products();
    for ($i=0, $n=sizeof($products); $i<$n; $i++) {
      if (zen_check_stock($products[$i]['id'], $products[$i]['quantity'])) {
        zen_redirect(zen_href_link(FILENAME_SHOPPING_CART));
        break;
      }
    }
  }
*/  
  
  /*
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
  */
 
 
  require(DIR_WS_CLASSES . 'order.php');
  $order = new order;

// register a random ID in the session to check throughout the checkout procedure
// against alterations in the shopping cart contents
if (isset($_SESSION['cart']->cartID)) {
  if (!isset($_SESSION['cartID']) || $_SESSION['cart']->cartID != $_SESSION['cartID']) {
    $_SESSION['cartID'] = $_SESSION['cart']->cartID;
  }
} else {
  zen_redirect(zen_href_link(FILENAME_TIME_OUT));
}

// if the order contains only virtual products, forward the customer to the billing page as
// a shipping address is not needed
/*
  if ($order->content_type == 'virtual') {
    $_SESSION['shipping'] = 'free_free';
    $_SESSION['shipping']['title'] = 'free_free';
    $_SESSION['sendto'] = false;
    zen_redirect(zen_href_link(FILENAME_CHECKOUT_PAYMENT, '', 'SSL'));
  }
*/

  $total_weight = $_SESSION['cart']->show_weight();
  $total_count = $_SESSION['cart']->count_contents();

//  echo "total_weight=$total_weight<br>";

// load all enabled shipping modules
//not on this page

/*
  require(DIR_WS_CLASSES . 'shipping.php');
  $shipping_modules = new shipping;

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


/*
  if (isset($_SESSION['comments'])) {
    $comments = $_SESSION['comments'];
  }
*/


if (zen_not_null($_POST['comments'])) {
      $_SESSION['comments'] = zen_db_prepare_input($_POST['comments']);
    }
    $comments = $_SESSION['comments'];



/*

// process the selected shipping method
  if ( isset($_POST['action']) && ($_POST['action'] == 'process') ) {
    if (zen_not_null($_POST['comments'])) {
      $_SESSION['comments'] = zen_db_prepare_input($_POST['comments']);
    }
    $comments = $_SESSION['comments'];
    $quote = array();

    if ( (zen_count_shipping_modules() > 0) || ($free_shipping == true) ) {
      if ( (isset($_POST['shipping'])) && (strpos($_POST['shipping'], '_')) ) {
    
        // check to be sure submitted data hasn't been tampered with
         
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

 * 

*/
// get all available shipping quotes
//  $quotes = $shipping_modules->quote();

 
  
  //var_dump($quotes);

/*
  foreach($quotes as $k => $v){
  	echo "<br>";
	  var_dump($v);
	
  }
*/

// if no shipping method has been selected, automatically select the cheapest method.
// if the modules status was changed when none were available, to save on implementing
// a javascript force-selection method, also automatically select the cheapest shipping
// method if more than one module is now enabled
/*
  if ( !$_SESSION['shipping'] || ( $_SESSION['shipping'] && ($_SESSION['shipping'] == false) && (zen_count_shipping_modules() > 1) ) ) $_SESSION['shipping'] = $shipping_modules->cheapest();


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
 * 
 */

 
  //Multiple Addresses Mod
  $shipToMultipleAddressesLink = zen_href_link(FILENAME_CHECKOUT_MULTIPLE_ADDRESSES, '', 'SSL');

  $breadcrumb->add(NAVBAR_TITLE_1, zen_href_link(FILENAME_CHECKOUT_SHIPPING, '', 'SSL'));
  $breadcrumb->add(NAVBAR_TITLE_2);

/*
$totalsDisplay = '';
switch (true) {
  case (SHOW_TOTALS_IN_CART == '1'):
  $totalsDisplay = TEXT_TOTAL_ITEMS . $_SESSION['cart']->count_contents() . TEXT_TOTAL_WEIGHT . $shipping_weight . TEXT_PRODUCT_WEIGHT_UNIT . TEXT_TOTAL_AMOUNT . $currencies->format($_SESSION['cart']->show_total());
  break;
  case (SHOW_TOTALS_IN_CART == '2'):
  $totalsDisplay = TEXT_TOTAL_ITEMS . $_SESSION['cart']->count_contents() . ($shipping_weight > 0 ? TEXT_TOTAL_WEIGHT . $shipping_weight . TEXT_PRODUCT_WEIGHT_UNIT : '') . TEXT_TOTAL_AMOUNT . $currencies->format($_SESSION['cart']->show_total());
  break;
  case (SHOW_TOTALS_IN_CART == '3'):
  $totalsDisplay = TEXT_TOTAL_ITEMS . $_SESSION['cart']->count_contents() . TEXT_TOTAL_AMOUNT . $currencies->format($_SESSION['cart']->show_total());
  break;
}
*/
//echo "totalsDisplay=$totalsDisplay<br>";

// testing/debugging
//  require(DIR_WS_MODULES . 'debug_blocks/shopping_cart_contents.php');

$flagHasCartContents = ($_SESSION['cart']->count_contents() > 0);
$cartShowTotal = $currencies->format($_SESSION['cart']->show_total());

$flagAnyOutOfStock = false;


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
  
  $buttonUpdate = zen_image_submit(ICON_IMAGE_UPDATE, ICON_UPDATE_MULTIPLE_ADDRESSES_ALT);
  
  
  $tmp =  zen_add_tax($products[$i]['final_price'],zen_get_tax_rate($products[$i]['tax_class_id']));
//  $productsPriceEach = $currencies->rateAdjusted($tmp);
//  $productsPriceTotal = $productsPriceEach * $products[$i]['quantity'];
  $productsPriceTotal = $currencies->display_price($products[$i]['final_price'], zen_get_tax_rate($products[$i]['tax_class_id']), $products[$i]['quantity']) . ($products[$i]['onetime_charges'] != 0 ? '<br />' . $currencies->display_price($products[$i]['onetime_charges'], zen_get_tax_rate($products[$i]['tax_class_id']), 1) : '');
  $productsPriceEach = $currencies->display_price($products[$i]['final_price'], zen_get_tax_rate($products[$i]['tax_class_id']), 1) . ($products[$i]['onetime_charges'] != 0 ? '<br />' . $currencies->display_price($products[$i]['onetime_charges'], zen_get_tax_rate($products[$i]['tax_class_id']), 1) : '');
//  $productsPriceTotal = $currencies->display_price($products[$i]['final_price'], zen_get_tax_rate($products[$i]['tax_class_id']), $products[$i]['quantity']) . ($products[$i]['onetime_charges'] != 0 ? '<br />' . $currencies->display_price($products[$i]['onetime_charges'], zen_get_tax_rate($products[$i]['tax_class_id']), 1) : '');
//  echo  $currencies->rateAdjusted($tmp);
  $productArray[$i] = array('attributeHiddenField'=>$attributeHiddenField,
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





//var_dump($addressArray);

// This should be last line of the script:
$zco_notifier->notify('NOTIFY_HEADER_END_CHECKOUT_SHIPPING');
 
/*
if(isset($_POST['btn_back_x'])){
	zen_redirect(zen_href_link(FILENAME_CHECKOUT_SHIPPING, '', 'SSL'));
}
*/

if(isset($_POST['btn_fwd_x']) && !$form_errors){
	zen_redirect(zen_href_link(FILENAME_CHECKOUT_MULTIPLE_SHIPMENTS, '', 'SSL'));
}

 
if(count($_POST)>0){
		
	//var_dump($_POST);
	
}
