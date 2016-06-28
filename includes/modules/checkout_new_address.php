<?php
/**
 * checkout_new_address.php
 *
 * @package modules
 * @copyright Copyright 2003-2007 Zen Cart Development Team
 * @copyright Portions Copyright 2003 osCommerce
 * @license http://www.zen-cart.com/license/2_0.txt GNU Public License V2.0
 * @version $Id: checkout_new_address.php 6772 2007-08-21 12:33:29Z drbyte $
 */
// This should be first line of the script:

//var_dump($_POST);
//die();

function array_empty($a){ // elibird 8/6/2014
    $empty = 1;
    foreach($a as $v){
        if(trim($v) != ''){
            $empty = 0;
        }
    }
    return $empty;
}

$zco_notifier->notify('NOTIFY_MODULE_START_CHECKOUT_NEW_ADDRESS');

//need to make sure something is checked here, else redirect back to this page

if (!defined('IS_ADMIN_FLAG')) {
  die('Illegal Access');
}
/**
 * Set some defaults
 */
  $process = false;
  $zone_name = '';
  $entry_state_has_zones = '';
  $error_state_input = false;
  $state = '';
  $zone_id = 0;
  $error = false;



//how many products?  --- only show add address button if multiple products
$how_many = $_SESSION['cart']->count_contents();

//echo "<br> how many = $how_many";

if (isset($_POST['action']) && ($_POST['action'] == 'submit')) {

$_SESSION['shipping_set'] = 1;
//die('f = ' . (array_empty($_POST['firstname'])));

  // process a new address

//echo "<br> Gonna process form!!";
//var_dump($_POST['city']);
//die('<br>ok?');

$hold_me = 0;
$hold_me_max = 0;
$post_holder = array();
$unique_keys = array();

$sql_data_array_holder = array();

unset($_SESSION['address_count']);

foreach($_POST as $k => $v){ //loop through arrays and set each $k to $v in $_POST so that arrays become scalars
    if(!in_array($k,$unique_keys)){
        if($k != 'address'){
            array_push($unique_keys, $k);
            //echo "<br> pushing $k to unique_keys";
        }
    }
    $hold_me = 0;
    foreach($v as $k2 => $value){
        //echo "<br> checking $k2, $value";
            
        if($k !== 'address'){
            //echo "<br>k = $k!!!!";
            $post_holder[$hold_me][$k] = $value;
            $hold_me += 1;
            //echo "<br>$hold_me_max < $hold_me";
            if($hold_me_max < $hold_me){
                $hold_me_max = $hold_me;
            }
        }
    }
    
}

$_SESSION['address_count'] = $hold_me_max;

//echo "<br> session[address_count] = " . $hold_me_max;

//var_dump($post_holder);

unset($_SESSION['new_address']);
$_SESSION['new_address'] = array();

$address_checkboxes_checked = 0;

if(isset($_POST['address'])  && array_empty($_POST['firstname'])){



    $address_checkboxes_checked = count($_POST['address']);
    //die('address[] submitted = ' . $address_checkbox_submit);
    
    if($address_checkboxes_checked >1){ //multiple addresses
        
        if($how_many > 1){
            zen_redirect(zen_href_link(FILENAME_CHECKOUT_MULTIPLE_ADDRESSES, '', 'SSL'));
        } else {
            $q = "delete from " . TABLE_MULTIPLE_ADDRESSES_MOD ." where customers_id = ".$_SESSION['customer_id'];
            $db->Execute($q);
            $_SESSION['multiple_addresses_tmp']=$_SESSION['multiple_addresses']; //save for later
            unset($_SESSION['multiple_addresses']); 
            $_SESSION['sendto'] = $_POST['address'][0];
            zen_redirect(zen_href_link(FILENAME_CHECKOUT_SHIPPING, '', 'SSL'));
        }

    } else {
        $q = "delete from " . TABLE_MULTIPLE_ADDRESSES_MOD ." where customers_id = ".$_SESSION['customer_id'];
    $db->Execute($q);
        $_SESSION['multiple_addresses_tmp']=$_SESSION['multiple_addresses']; //save for later
        unset($_SESSION['multiple_addresses']); 
        $_SESSION['sendto'] = $_POST['address'][0];
        zen_redirect(zen_href_link(FILENAME_CHECKOUT_SHIPPING, '', 'SSL'));
    }

} else {

$address_checkboxes_checked = count($_POST['address']);

//echo "<br> addresses checked = " . $address_checkboxes_checked;

//echo "<br> hold_me_max = " . $hold_me_max;


for($i=0;$i<$hold_me_max;$i++){
    
    $_SESSION['new_address'][$i] = $post_holder[$i];

    foreach($unique_keys as $v){
        unset($_POST[$v]);
        $_POST[$v] = $post_holder[$i][$v];
        //echo "<br> assigning post[$v] = " .$_POST[$v]; 
        
        //echo "<BR> POST:: " . $v ." = " . $_POST[$v];
        
    }   

//}

//die('OK?');

  if (zen_not_null($_POST['firstname']) && zen_not_null($_POST['lastname']) && zen_not_null($_POST['street_address'])) {
    $process = true;
    if (ACCOUNT_GENDER == 'true') $gender = zen_db_prepare_input($_POST['gender']);
    if (ACCOUNT_COMPANY == 'true') $company = zen_db_prepare_input($_POST['company']);
    $firstname = zen_db_prepare_input($_POST['firstname']);
    
    //echo "<br>"  . __LINE__ . ": post[lastname] = " . $_POST['lastname'];

    $lastname = zen_db_prepare_input($_POST['lastname']);
    $street_address = zen_db_prepare_input($_POST['street_address']);
    if (ACCOUNT_SUBURB == 'true') $suburb = zen_db_prepare_input($_POST['suburb']);
    $postcode = zen_db_prepare_input($_POST['postcode']);
    $city = zen_db_prepare_input($_POST['city']);
    if (ACCOUNT_STATE == 'true') {
      $state = zen_db_prepare_input($_POST['state']);
      if (isset($_POST['zone_id'])) {
        $zone_id = zen_db_prepare_input($_POST['zone_id']);
      } else {
        $zone_id = false;
      }
    }
    $country = zen_db_prepare_input($_POST['zone_country_id']);
//echo ' I SEE: country=' . $country . '&nbsp;&nbsp;&nbsp;state=' . $state . '&nbsp;&nbsp;&nbsp;zone_id=' . $zone_id;
    /*
    if (ACCOUNT_GENDER == 'true') {
      if ( ($gender != 'm') && ($gender != 'f') ) {
        $error = true;
        $messageStack->add('checkout_address', ENTRY_GENDER_ERROR);
      }
    }
    */
    if (strlen($firstname) < ENTRY_FIRST_NAME_MIN_LENGTH) {
      $error = true;
      $messageStack->add('checkout_address', ENTRY_FIRST_NAME_ERROR);
    }

    if (strlen($lastname) < ENTRY_LAST_NAME_MIN_LENGTH) {
      $error = true;
        //echo "<br> STRLEN(LASTNAME) = " . strlen($lastname);
      $messageStack->add('checkout_address', ENTRY_LAST_NAME_ERROR);
    }

    if (strlen($street_address) < ENTRY_STREET_ADDRESS_MIN_LENGTH) {
      $error = true;
      $messageStack->add('checkout_address', ENTRY_STREET_ADDRESS_ERROR);
    }

    if (strlen($city) < ENTRY_CITY_MIN_LENGTH) {
      $error = true;
      $messageStack->add('checkout_address', ENTRY_CITY_ERROR);
    }

    if (ACCOUNT_STATE == 'true') {
      $check_query = "SELECT count(*) AS total
                      FROM " . TABLE_ZONES . "
                      WHERE zone_country_id = :zoneCountryID";
      $check_query = $db->bindVars($check_query, ':zoneCountryID', $country, 'integer');
      $check = $db->Execute($check_query);
      $entry_state_has_zones = ($check->fields['total'] > 0);
      if ($entry_state_has_zones == true) {
      $zone_query = "SELECT distinct zone_id, zone_name, zone_code
                       FROM " . TABLE_ZONES . "
                       WHERE zone_country_id = :zoneCountryID
                       AND " . 
                     ((trim($state) != '' && $zone_id == 0) ? "(upper(zone_name) like ':zoneState%' OR upper(zone_code) like '%:zoneState%') OR " : "") .
                      "zone_id = :zoneID
                       ORDER BY zone_code ASC, zone_name";

        $zone_query = $db->bindVars($zone_query, ':zoneCountryID', $country, 'integer');
        $zone_query = $db->bindVars($zone_query, ':zoneState', strtoupper($state), 'noquotestring');
        $zone_query = $db->bindVars($zone_query, ':zoneID', $zone_id, 'integer');
        $zone = $db->Execute($zone_query);

      //look for an exact match on zone ISO code
      $found_exact_iso_match = ($zone->RecordCount() == 1);
      if ($zone->RecordCount() > 1) {
        while (!$zone->EOF && !$found_exact_iso_match) {
          if (strtoupper($zone->fields['zone_code']) == strtoupper($state) ) {
            $found_exact_iso_match = true;
            continue;
          }
          $zone->MoveNext();
        }
      }

      if ($found_exact_iso_match) {
        $zone_id = $zone->fields['zone_id'];
        $zone_name = $zone->fields['zone_name'];
      } else {
        $error = true;
        $error_state_input = true;
        $messageStack->add('checkout_address', ENTRY_STATE_ERROR_SELECT);
      }
    } else {
      if (strlen($state) < ENTRY_STATE_MIN_LENGTH) {
        $error = true;
        $error_state_input = true;
        $messageStack->add('checkout_address', ENTRY_STATE_ERROR);
      }
    }
  }

    if (strlen($postcode) < ENTRY_POSTCODE_MIN_LENGTH) {
      $error = true;
      $messageStack->add('checkout_address', ENTRY_POST_CODE_ERROR);
    }

    if ( (is_numeric($country) == false) || ($country < 1) ) {
      $error = true;
      $messageStack->add('checkout_address', ENTRY_COUNTRY_ERROR);
    }

    if ($error == false) {
      $sql_data_array = array(array('fieldName'=>'customers_id', 'value'=>$_SESSION['customer_id'], 'type'=>'integer'),
                              array('fieldName'=>'entry_firstname', 'value'=>$firstname, 'type'=>'string'),
                              array('fieldName'=>'entry_lastname','value'=>$lastname, 'type'=>'string'),
                              array('fieldName'=>'entry_street_address','value'=>$street_address, 'type'=>'string'),
                              array('fieldName'=>'entry_postcode', 'value'=>$postcode, 'type'=>'string'),
                              array('fieldName'=>'entry_city', 'value'=>$city, 'type'=>'string'),
                              array('fieldName'=>'entry_country_id', 'value'=>$country, 'type'=>'integer')
      );

      if (ACCOUNT_GENDER == 'true') $sql_data_array[] = array('fieldName'=>'entry_gender', 'value'=>$gender, 'type'=>'enum:m|f');
      if (ACCOUNT_COMPANY == 'true') $sql_data_array[] = array('fieldName'=>'entry_company', 'value'=>$company, 'type'=>'string');
      if (ACCOUNT_SUBURB == 'true') $sql_data_array[] = array('fieldName'=>'entry_suburb', 'value'=>$suburb, 'type'=>'string');
      if (ACCOUNT_STATE == 'true') {
        if ($zone_id > 0) {
          $sql_data_array[] = array('fieldName'=>'entry_zone_id', 'value'=>$zone_id, 'type'=>'integer');
          $sql_data_array[] = array('fieldName'=>'entry_state', 'value'=>'', 'type'=>'string');
        } else {
          $sql_data_array[] = array('fieldName'=>'entry_zone_id', 'value'=>0, 'type'=>'integer');
          $sql_data_array[] = array('fieldName'=>'entry_state', 'value'=>$state, 'type'=>'string');
        }
      }
        
      // don't insert individual addresses until no errors are returned,
      /// --- so, DEFER this action until after the loop, saving the
      // $sql_data_array in some other array
      //$db->perform(TABLE_ADDRESS_BOOK, $sql_data_array);
      $sql_data_array_holder[] = $sql_data_array;

      $zco_notifier->notify('NOTIFY_MODULE_CHECKOUT_ADDED_ADDRESS_BOOK_RECORD', array_merge(array('address_id' => $db->Insert_ID() ), $sql_data_array));
      switch($addressType) {
        case 'billto':
        $_SESSION['billto'] = $db->Insert_ID();
        $_SESSION['payment'] = '';
        zen_redirect(zen_href_link(FILENAME_CHECKOUT_PAYMENT, '', 'SSL'));
        break;
        case 'shipto':
        //no longer redirecting, need to insert multiple addresses at once.
        
        $_SESSION['sendto'] = $db->Insert_ID();
        $_SESSION['shipping'] = '';
            //echo "ok!";
            //die();
        //zen_redirect(zen_href_link(FILENAME_CHECKOUT_SHIPPING, '', 'SSL'));
    
        break;
      }
    }
  }

  /* elseif (isset($_POST['address'])) {
    switch($addressType) {
      case 'billto':
      $reset_payment = false;
      if ($_SESSION['billto']) {
        if ($_SESSION['billto'] != $_POST['address']) {
          if ($_SESSION['payment']) {
            $reset_payment = true;
          }
        }
      }
      $_SESSION['billto'] = $_POST['address'];

      $check_address_query = "SELECT count(*) AS total
                              FROM " . TABLE_ADDRESS_BOOK . "
                              WHERE customers_id = :customersID
                              AND address_book_id = :addressBookID";

      $check_address_query = $db->bindVars($check_address_query, ':customersID', $_SESSION['customer_id'], 'integer');
      $check_address_query = $db->bindVars($check_address_query, ':addressBookID', $_SESSION['billto'], 'integer');
      $check_address = $db->Execute($check_address_query);

      if ($check_address->fields['total'] == '1') {
        if ($reset_payment == true) $_SESSION['payment'] = '';
        zen_redirect(zen_href_link(FILENAME_CHECKOUT_PAYMENT, '', 'SSL'));
      } else {
        $_SESSION['billto'] = '';
      }
      // no addresses to select from - customer decided to keep the current assigned address
      break;
      case 'shipto':
      $reset_shipping = false;
      if ($_SESSION['sendto']) {
        if ($_SESSION['sendto'] != $_POST['address']) {
          if ($_SESSION['shipping']) {
            $reset_shipping = true;
          }
        }
      }
     $_SESSION['sendto'] = $_POST['address'];
      $check_address_query = "SELECT count(*) AS total
                              FROM " . TABLE_ADDRESS_BOOK . "
                              WHERE customers_id = :customersID
                              AND address_book_id = :addressBookID";

      $check_address_query = $db->bindVars($check_address_query, ':customersID', $_SESSION['customer_id'], 'integer');
      $check_address_query = $db->bindVars($check_address_query, ':addressBookID', $_SESSION['sendto'], 'integer');
      $check_address = $db->Execute($check_address_query);
      if ($check_address->fields['total'] == '1') {
        if ($reset_shipping == true) $_SESSION['shipping'] = '';
        zen_redirect(zen_href_link(FILENAME_CHECKOUT_SHIPPING, '', 'SSL'));
      } else {
        $_SESSION['sendto'] = '';
      }
      break;
    }
  } 
*/

else {
    //nothing set.  if addressType == shipto, then redirect to this page
    // with error

    //die('nothing set');

    switch($addressType) {
      case 'billto':
      $_SESSION['billto'] = $_SESSION['customer_default_address_id'];
      zen_redirect(zen_href_link(FILENAME_CHECKOUT_PAYMENT, '', 'SSL'));
      break;
      case 'shipto':
        
        $error = true;
        $messageStack->add('checkout_address', ENTRY_SELECT_ADDRESS);

      
      //$_SESSION['sendto'] = $_SESSION['customer_default_address_id'];
      //zen_redirect(zen_href_link(FILENAME_CHECKOUT_SHIPPING, '', 'SSL'));
      

      break;
    }
    
  }


}    //end post assignment loop here <--
    //if no error, then insert addresses into db and redirect

} //if isset (POST['address']) 


if($error == false){
    foreach($sql_data_array_holder as $v){
        //echo "<br> doing db insert";
        $db->perform(TABLE_ADDRESS_BOOK, $v);
        $_SESSION['sendto'] = $db->Insert_ID();
    }
    
    unset($_SESSION['new_address']);  //remove set session addresses when 
        /// database insert is successful

    //die('hold_me_max=' . $hold_me_max . ', addresses checked='.$address_checkboxes_checked);

    //redirect to multiple addresses if multiple addresses were chosen
    if($hold_me_max == 1 && $address_checkboxes_checked ==0){
        //die('redirect to checkout_shipping');
        zen_redirect(zen_href_link(FILENAME_CHECKOUT_SHIPPING, '', 'SSL'));
    } else {
        if($hold_me_max > 0 && $address_checkboxes_checked > 0){
        //die('redirect to checkout_multiple');
            if($how_many > 1){    
                zen_redirect(zen_href_link(FILENAME_CHECKOUT_MULTIPLE_ADDRESSES, '', 'SSL'));
            } else {
                zen_redirect(zen_href_link(FILENAME_CHECKOUT_SHIPPING, '', 'SSL'));
            }
        } else {
            zen_redirect(zen_href_link(FILENAME_CHECKOUT_SHIPPING, '', 'SSL'));
        }
    }
}

} //IF isset post['action']



//echo "<br>everything ok out there?";

/*
 * Set flags for template use:
 */
  $selected_country = (isset($_POST['zone_country_id']) && $_POST['zone_country_id'] != '') ? $country : SHOW_CREATE_ACCOUNT_DEFAULT_COUNTRY;
  $flag_show_pulldown_states = ((($process == true || $entry_state_has_zones == true) && $zone_name == '') || ACCOUNT_STATE_DRAW_INITIAL_DROPDOWN == 'true' || $error_state_input) ? true : false;
  $state = ($flag_show_pulldown_states) ? $state : $zone_name;
  $state_field_label = ($flag_show_pulldown_states) ? '' : ENTRY_STATE;

// This should be last line of the script:
$zco_notifier->notify('NOTIFY_MODULE_END_CHECKOUT_NEW_ADDRESS');
?>