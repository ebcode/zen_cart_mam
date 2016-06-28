<?php
/**
 * Checkout Process Page
 *
 * @package page
 * @copyright Copyright 2003-2010 Zen Cart Development Team
 * @copyright Portions Copyright 2003 osCommerce
 * @license http://www.zen-cart.com/license/2_0.txt GNU Public License V2.0
 * @version $Id: header_php.php 16907 2010-07-15 19:38:33Z drbyte $
 */
 
// This should be first line of the script:
  $zco_notifier->notify('NOTIFY_HEADER_START_CHECKOUT_PROCESS');

//require(DIR_WS_CLASSES . 'order.php');
//$order = new order;

//get gift messages and put in session.  adding these on the success page to associate with order_id
/*
unset($_SESSION['gift_msg']);
for ($i=0, $n=sizeof($order->products); $i<$n; $i++) {

	for ($j=0; $j<$order->products[$i]['qty']; $j++){
		
		$msg = zen_db_prepare_input($_POST[((int)$order->products[$i]['id']).'_'.$j]);
		//echo "<br>msg=$msg";
		
		$msg = array('products_id'=>(int)$order->products[$i]['id'], 'msg'=>$msg);
		$_SESSION['gift_msg'][]=$msg;
	}
} 
*/
$frm_err=0;

/*
unset($_SESSION['gift_msg']);
unset($_SESSION['phn']);
foreach($_POST as $k => $v){
		
	//echo "strpos = ". strpos($k, 'sg_');
		
	if(strpos($k, 'sg_')==1){
		
		//echo "<br>k = $k<br>";
		
		$hm = explode('_',$k);
		$msg = array('products_id'=>$hm[1],'msg'=>zen_db_prepare_input($v));
		
		//echo "<br>msg=".$msg['msg'];
		
		$msg['msg'] = trim($msg['msg']);
		
		$_SESSION['gift_msg'][]=$msg;
	
		if($msg['msg']==''){
			$frm_err=1;
		}
		

	}

	if(strpos($k, 'hn_')==1){
		//echo "phone";
		
		//echo "<br>k = $k<br>";
		
		$hm = explode('_',$k);
		$msg = array('products_id'=>$hm[1],'phn'=>zen_db_prepare_input($v));
		
		//echo "phn = ".$msg['phn'];

		//echo "<br>msg=".$msg['msg'];
		
		$msg['phn'] = trim($msg['phn']);
		
		$_SESSION['phn'][]=$msg;
	
		if($msg['phn']==''){
			$frm_err=1;
		}
	}

}
*/

//echo "<br>frm_err=$frm_err";

//die('?');

if($frm_err){
	//$messageStack->add('checkout_confirmation', $error['error'], 'Please enter a gift message and phone # for each basket in your order.');
	zen_redirect(zen_href_link(FILENAME_CHECKOUT_CONFIRMATION, 'msg_err=1', 'SSL'));
}

/*Multiple Addresses Mod
 * 
*/
if(isset($_POST['btn_back_x'])){
    
        zen_redirect(zen_href_link(FILENAME_CHECKOUT_PAYMENT, '', 'SSL'));
    
}


//die();

//var_dump($_SESSION['gift_msg']);
//die('?');

  require(DIR_WS_MODULES . zen_get_module_directory('checkout_process.php'));

  //die('in checkout_process/header_php.php');
// load the after_process function from the payment modules
  $payment_modules->after_process();

  $_SESSION['cart']->reset(true);

// unregister session variables used during checkout
  unset($_SESSION['sendto']);
  unset($_SESSION['billto']);
  unset($_SESSION['shipping']);
  unset($_SESSION['payment']);
  unset($_SESSION['comments']);
  $order_total_modules->clear_posts();//ICW ADDED FOR CREDIT CLASS SYSTEM
	
  //Multiple Addresses Mod
	unset($_SESSION['multiple_addresses']);
	unset($_SESSION['multiple_addresses_grand_total']);
	unset($_SESSION['multiple_addresses_tmp']);
	unset($_SESSION['shipping_tmp']);
	$q = "delete from " . TABLE_MULTIPLE_ADDRESSES_MOD ." where customers_id = ".$_SESSION['customer_id'];
	$db->Execute($q);
	
  // This should be before the zen_redirect:
  $zco_notifier->notify('NOTIFY_HEADER_END_CHECKOUT_PROCESS');

  zen_redirect(zen_href_link(FILENAME_CHECKOUT_SUCCESS, (isset($_GET['action']) && $_GET['action'] == 'confirm' ? 'action=confirm' : ''), 'SSL'));

  require(DIR_WS_INCLUDES . 'application_bottom.php');
