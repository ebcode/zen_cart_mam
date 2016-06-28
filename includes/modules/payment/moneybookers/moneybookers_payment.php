<?php
/**
 * Mediaflex Sp. z O.O.
 * @author krystian.kuczek@mediaflex.pl
 * @package Moneybookers Payment Plugin
 */

include_once((IS_ADMIN_FLAG === true ? DIR_FS_CATALOG_LANGUAGES : DIR_WS_LANGUAGES) . $_SESSION['language'] . '/modules/payment/moneybookers.php');
include_once((IS_ADMIN_FLAG === true ? DIR_FS_CATALOG_MODULES : DIR_WS_MODULES) . 'payment/moneybookers/moneybookers_curl.php');

/**
 * Description of moneybookers_payment
 *
 * @author krun
 */
class moneybookers_payment extends base {
	/**
	 * Prefix głównego modułu
	 * @var string
	 */
	var $_prefix = 'MAIN_PREFIX_NOT_SET_';
	var $_mainPrefix = 'MODULE_PAYMENT_MONEYBOOKERS_';

	var $_moneybookersUrl;
	var $_notifyUrl;
	var $_cancelUrl;
	var $_returnUrl;

	/**
	 * string repesenting the payment method
 	 *
	 * @var string
	 */
	var $code;
	/**
	 * $title is the displayed name for this payment method
	 *
	 * @var string
	 */
	var $var;
	/**
	 * $description is a soft name for this payment method
 	 *
	 * @var string
	 */
	var $description;
	/**
	 * $enabled determines whether this module shows or not... in catalog.
	 *
	 * @var boolean
	 */
	var $enabled;

	function moneybookers_payment() {
		global $messageStack, $order;
		
		$this->_notifyUrl = zen_href_link('ipn_main_handler.php', '', 'SSL',false,false,true);
		$this->_cancelUrl = zen_href_link(FILENAME_CHECKOUT_PAYMENT, '', 'SSL');
		$this->_moneybookersUrl = zen_href_link('payment_moneybookers');

		try {
			if ((int)$this->_get('ORDER_STATUS_ID') > 0) {
				$this->order_status = $this->_get('ORDER_STATUS_ID');
			}

			$this->sort_order = $this->_get('SORT_ORDER');

			if (!$this->checkMainEnabled()) {
				$this->title .= '<b style="color: red"> (Main module is not configured)</b>';
				$this->enabled = false;
			} else {
				$this->enabled = $this->_get('STATUS');
			}
		} catch (Exception $e) {
			$this->enabled = false;
		}

		if (is_object($order)) {
			$this->update_status();
		}


		$this->form_action_url = $this->_moneybookersUrl;

//		if (IS_ADMIN_FLAG === true) $this->tableCheckup();
	}

	function _get($index) {
		if (defined($this->_prefix . $index)) {
			return constant($this->_prefix . $index);
		} else {
			throw new Exception("Bad constant '$this->_prefix$index'");
		}
	}

	function _defined($index) {
		return defined($this->_prefix . $index);
	}

	function _mainGet($index) {
		if (defined($this->_mainPrefix . $index)) {
			return constant($this->_mainPrefix . $index);
		} else {
			throw new Exception("Bad constant '$this->_mainPrefix$index'");
		}
	}

	function _mainDefined($index) {
		return defined($this->_mainPrefix . $index);
	}

	/**
	 * Funkcja sprawdzająca czy moduł jest zainstalowany
	 *
	 * @return bool
	 */
	function check() {
		global $db;

		if (!isset($this->_check)) {
			$check_query = $db->Execute("select configuration_value from " . TABLE_CONFIGURATION . " where configuration_key = '" . $this->_prefix . "STATUS' OR configuration_key = '" . $this->_mainPrefix . "STATUS'");
			$this->_check = ($check_query->RecordCount() == 2);
		}

		return $this->_check;
	}

	function checkMainEnabled() {
		return ($this->_mainDefined('STATUS') && ($this->_mainDefined('EMAIL') && $this->_mainGet('EMAIL')) ||
				($this->_mainDefined('ID') && $this->_mainGet('ID')) && $this->_mainDefined('SECRET_WORD'));
	}

	function keys() {
		 $keys_list = array(
			$this->_prefix . 'STATUS',
			$this->_prefix . 'SORT_ORDER',
			$this->_prefix . 'ORDER_STATUS_ID',
			$this->_prefix . 'PROCESSING_ORDER_STATUS_ID',
			$this->_prefix . 'ZONE',
			$this->_prefix . 'TITLE'
			);

		 return $keys_list;
	}

	/**
	 * Instalacja modułu moneybookers.
	 *
	 */
	function install() {
		global $db, $messageStack;

		$langFile = '../' . zen_get_file_directory(DIR_WS_LANGUAGES . $_SESSION['language'] . '/modules/payment/', __CLASS__, 'false');
        if (@file_exists($langFile)) {
			include_once($langFile);
		}

		if (!$this->checkMainEnabled()) {
			$messageStack->add_session(sprintf(MODULE_PAYMENT_MONEYBOOKERS_TEXT_NO_MAIN_MODULE, zen_href_link('modules.php?set=payment&amp;module=moneybookers&amp;action=edit', '', 'NONSSL')));
			return 'failed';
		}
		
		$db->Execute("DELETE from " . TABLE_CONFIGURATION . " where configuration_key LIKE '" . $this->_prefix . "%'");

		$title = explode("(powered by", $this->title);
		$title = trim($title[0]);

		$s = 0;
//		$this->_toConf($key, $value, $title, $description, $sortOrder, $setFunction, $useFunction)
		$this->_toConf('STATUS',  'True', MODULE_PAYMENT_MONEYBOOKERS_TEXT_ENABLED, MODULE_PAYMENT_MONEYBOOKERS_TEXT_ENABLED_DESC, $s++, 'zen_cfg_select_option(array(\'True\', \'False\'), ');
		$this->_toConf('SORT_ORDER', 0, MODULE_PAYMENT_MONEYBOOKERS_TEXT_ORDEROFDISPLAY, MODULE_PAYMENT_MONEYBOOKERS_TEXT_ORDEROFDISPLAY_DESC, $s++);
		$this->_toConf('PROCESSING_ORDER_STATUS_ID', $this->_mainGet('PROCESSING_ORDER_STATUS_ID'), MODULE_PAYMENT_MONEYBOOKERS_TEXT_STATUS_PENDING, MODULE_PAYMENT_MONEYBOOKERS_TEXT_STATUS_PENDING_DESC, $s++, "zen_cfg_pull_down_order_statuses(", "zen_get_order_status_name");
		$this->_toConf('ZONE', $this->_zoneId, MODULE_PAYMENT_MONEYBOOKERS_TEXT_PAYMENTZONE, MODULE_PAYMENT_MONEYBOOKERS_TEXT_PAYMENTZONE_DESC, $s++, 'zen_cfg_pull_down_zone_classes(', 'zen_get_zone_class_title');
		$this->_toConf('ORDER_STATUS_ID', $this->_mainGet('ORDER_STATUS_ID'), MODULE_PAYMENT_MONEYBOOKERS_TEXT_STATUS_ORDER, MODULE_PAYMENT_MONEYBOOKERS_TEXT_STATUS_ORDER_DESC, $s++, "zen_cfg_pull_down_order_statuses(", "zen_get_order_status_name");
		$this->_toConf('TITLE', $title, MODULE_PAYMENT_MONEYBOOKERS_TEXT_TITLE, MODULE_PAYMENT_MONEYBOOKERS_TEXT_TITLE_DESC, $s++);
	}

	/**
	 * Usunięcie głównego modułu moneybookers.
	 *
	 * TODO: Trzeba sprawdzić czy moduły zależne nie są zainstalowane, jeśli są to nie pozowlić usunąć.
	 */
	function remove() {
		global $db;
		$db->Execute("delete from " . TABLE_CONFIGURATION . " where configuration_key LIKE '" . $this->_prefix . "%'");
		$this->notify('NOTIFY_PAYMENT_PAYPAL_UNINSTALLED');
	}

	function _toConf($key, $value, $title, $description, $sortOrder = null, $setFunction = null, $useFunction = null) {
		global $db;

		$key = $this->_prefix . $key;

		$sql = "INSERT INTO " . TABLE_CONFIGURATION . "(configuration_title, configuration_key, configuration_value, configuration_group_id, date_added, configuration_description";
		$val = "'" . mysql_real_escape_string($title) . "', '" . mysql_real_escape_string($key) . "', '" . mysql_real_escape_string($value) . "', 6, now(), '" .
					mysql_real_escape_string($description) . "'";

		if ($sortOrder !== null) {
			$sql .= ", sort_order";
			$val .= ", $sortOrder";
		}

		if ($setFunction !== null) {
			$sql .= ", set_function";
			$val .= ", '" . mysql_real_escape_string($setFunction) . "'";
		}

		if ($useFunction !== null) {
			$sql .= ", use_function";
			$val .= ", '" . mysql_real_escape_string($useFunction) . "'";
		}

		$sql .= ") VALUES(" . $val . ")";

		$db->Execute($sql);
	}

	function  __get($name) {
		if ($name == 'title') {
			$dbg = debug_backtrace();

			if (sizeof($dbg) == 1) {
				return '&nbsp;&nbsp;&nbsp;&nbsp; -> ' . $this->var['title'];
			} else {
				return $this->var['title'];
			}
		} else {
			return isset($this->var[$name]) ? $this->var[$name] : null;
		}
	}

	function __set($name, $value) {
		$this->var[$name] = $value;
	}

	function __isset($name) {
		return isset($this->var[$name]);
	}



	/**
	 * Button processu
	 */
	function process_button() {
		global $db, $order, $currencies, $currency;

		$curr = ($_SESSION['currency'] ? $_SESSION['currency'] : 'EUR');

		$curl = new moneybookers_curl();
		$curl->SessionStart($data);

		$this->totalsum = $order->info['total'];
		
		if(isset($_SESSION['multiple_addresses_grand_total'])){  //Multiple Addresses Mod
			$this->totalsum = $_SESSION['multiple_addresses_grand_total'];
		}
		
		$this->transaction_amount = ($this->totalsum * $currencies->get_value($curr));

		$table = array(
			//Merchant
			'pay_to_email' => $this->_mainGet('EMAIL'),
			'recipient_description' => STORE_NAME,
//			'transaction_id' => md5($_SESSION['order_number_created'] . $order->customer['email_address']), //WARNING: Nie jestem pewien czy to w 100% unikalne jest, ale tak mi się wydaje
			'language' => 'EN', //FIXME

			//Przekierowania
			'return_url' => $this->_returnUrl,
			'return_url_text' => MODULE_PAYMENT_MONEYBOOKERS_RETURN_URL_TEXT,
			'return_url_target' => '_parent',
			'cancel_url' => $this->_cancelUrl,
			'cancel_url_target' => '_parent',
			
			//Customer
			'pay_for_email' => $order->customer['email_address'],
			'firstname' => html_entity_decode(replace_accents($order->customer['firstname'])),
			'lastname' => html_entity_decode(replace_accents($order->customer['lastname'])),
			'address' => html_entity_decode(replace_accents($order->customer['street_address'])),
			'address2' => html_entity_decode(replace_accents($order->customer['street_address'])),
			'city' => html_entity_decode(replace_accents($order->customer['city'])),
			'state' => html_entity_decode(replace_accents($order->customer['state'])),
			'postal_code' => html_entity_decode(replace_accents(str_replace('-', '', $order->customer['postcode']))),
			'country' => $order->customer['country']['iso_code_3'],
			'pay_from_email' => $order->customer['email_address'],

			//Price
			'amount' => number_format($this->transaction_amount, $currencies->get_decimal_places($curr)),
			'currency' => $curr,

			'status_url' => 'mailto: ' . $this->_mainGet('INFO_MAIL'),

			"merchant_fields" => "referring_platform",
			"referring_platform" => "zencart",
		);

		//Products
		$x = 2;
		$z = 1;
		foreach ($order->products as $product) {
			$table["amount{$x}_description"] = $product['name'];
			$table["amount{$x}"] = number_format($product['price'], $currencies->get_decimal_places($curr)) . " $curr";
			$table["detail{$z}_description"] = $product['name'];
			$table["detail{$z}_text"] = number_format($product['price'], $currencies->get_decimal_places($curr)) . " $curr";
			$x++;
			$z++;
		}

		$table["amount{$x}_description"] = MODULE_PAYMENT_MONEYBOOKERS_TEXT_SUBTOTAL;
		$table["amount{$x}"] = $order->info['subtotal'] . " $curr";
		$table["detail{$x}_description"] = MODULE_PAYMENT_MONEYBOOKERS_TEXT_SUBTOTAL;
		$table["detail{$x}_text"] = $order->info['subtotal'] . " $curr";
		$x++;
		$z++;
		
		//Tax
		foreach ($order->info['tax_groups'] as $key => $v) {
			if ($v != 0) {
				$table["amount{$x}_description"] = $key;
				$table["amount{$x}"] = number_format($v, $currencies->get_decimal_places($curr)) . " $curr";
				$table["detail{$z}_description"] = $key;
				$table["detail{$z}_text"] = number_format($v, $currencies->get_decimal_places($curr)) . " $curr";
				$x++;
				$z++;
			}
		}

		if (is_array($this->_paymentMethod)) {
			$table['payment_methods'] = implode(",", $this->_paymentMethod) . ',';
		} else if (is_string($this->_paymentMethod)) {

			$table['hide_login'] = 1;
			$paymentMethod = trim($this->_paymentMethod);

			if (substr($paymentMethod, strlen($paymentMethod)-1, 1) != ',') {
				$paymentMethod .= ',';
			}

			$table['payment_methods'] = $paymentMethod;
		}

		foreach ($table as $key => $val) {
			$data[$key] = html_entity_decode(iconv(trim(CHARSET), 'utf-8', $val), ENT_QUOTES, 'utf-8');
		}

		$sid = $curl->SessionStart($data);

		$table = array('sid' => $sid);
		
		$ret = '';
		foreach ($table as $key => $val) {
			$ret .= zen_draw_hidden_field($key, $val);
		}

		return $ret;
	}

	function selection() {
		return array('id' => $this->code,
			'module' => $this->_get('TITLE') . '
				<div style="text-align: center; margin: auto;"><img src="' . $this->_imgLink . '" alt="' . $this->_get('TITLE') . '" /></div>
				',
			);
	}

	function javascript_validation() {
		return false;
	}

	/**
	 * Przed potwierdzeniem
	 */
	function pre_confirmation_check() {
	}

	/**
	 * Po potwierdzeniu
	 */
	function confirmation() {
	}

	function before_process() {
		return false;
	}

	function after_process() {
	}

	/**
	 * calculate zone matches and flag settings to determine whether this module should display to customers or not
	 *
	 */
	function update_status() {
		global $order, $db;

		if ( ($this->enabled == true) && ((int)$this->_get('ZONE') > 0) ) {
			$check_flag = false;
			$check = $db->Execute("select zone_id from " . TABLE_ZONES_TO_GEO_ZONES . " where geo_zone_id = '" . $this->_get('ZONE') . "' and zone_country_id = '" . $order->billing['country']['id'] . "' order by zone_id");

			while (!$check->EOF) {
				if ($check->fields['zone_id'] < 1) {
					$check_flag = true;
					break;
				} elseif ($check->fields['zone_id'] == $order->billing['zone_id']) {
					$check_flag = true;
					break;
				}
				$check->MoveNext();
			}

			if ($check_flag == false) {
				$this->enabled = false;
			}
		}
	}
}
?>
