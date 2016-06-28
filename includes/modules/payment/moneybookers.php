<?php
/**
 * Mediaflex Sp. z O.O.
 * @author krystian.kuczek@mediaflex.pl
 * @package Moneybookers Payment Plugin
 */

include_once((IS_ADMIN_FLAG === true ? DIR_FS_CATALOG_LANGUAGES : DIR_WS_LANGUAGES) . $_SESSION['language'] . '/modules/payment/moneybookers.php');

include_once((IS_ADMIN_FLAG === true ? DIR_FS_CATALOG_MODULES : DIR_WS_MODULES) . 'payment/moneybookers/moneybookers_functions.php');

class moneybookers extends base {

	var $_prefix = 'MODULE_PAYMENT_MONEYBOOKERS_';
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
  var $title;
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
  /**
    * constructor
    *
    * @param int $paypal_ipn_id
    * @return paypal
    */
  
	function moneybookers() {
		global $messageStack;

		$this->code = 'moneybookers';
		$this->codeVersion = '0.0.1.a';
		$this->description = MODULE_PAYMENT_MONEYBOOKERS_TEXT_WELCOME;
		$this->title = ($this->check() ? "<b>" : "") . MODULE_PAYMENT_MONEYBOOKERS_TEXT_MAIN_MODULE . (!$this->check() ? " - <b>" . MONDULE_PAYMENT_MONEYBOOKERS_TEXT_REQUIRED : "") . "</b>";

		try {
			if ((int)$this->_get('ORDER_STATUS_ID') > 0) {
				$this->order_status = $this->_get('ORDER_STATUS_ID');
			}

			$this->sort_order = $this->_get('SORT_ORDER');

			if ($this->_defined('STATUS') && (!$this->_defined('EMAIL') || !$this->_get('EMAIL') || !$this->_defined('SECRET_WORD') || !$this->_defined('ID') || !$this->_get('ID'))) {
				$this->title .= '<b style="color: red"> (Module is not configured)</b>';
				$this->enabled = false;
			} else {
				$this->enabled = $this->_get('STATUS');
			}
		} catch (Exception $e) {
			$this->enabled = false;
		}
		
//		if (IS_ADMIN_FLAG === true) $this->tableCheckup();
	}

	/**
	 * Funkcja sprawdzająca czy moduł jest zainstalowany
	 *
	 * @return bool
	 */
	function check() {
		global $db;
//    if (IS_ADMIN_FLAG === true) {
//      global $sniffer;
//      if ($sniffer->field_exists(TABLE_PAYPAL, 'zen_order_id'))  $db->Execute("ALTER TABLE " . TABLE_PAYPAL . " CHANGE COLUMN zen_order_id order_id int(11) NOT NULL default '0'");
//    }
		if (!isset($this->_check)) {
			$check_query = $db->Execute("select configuration_value from " . TABLE_CONFIGURATION . " where configuration_key = '" . $this->_prefix . "STATUS'");
			$this->_check = $check_query->RecordCount();
		}
		
		return $this->_check;
	}

	function keys() {
		 $keys_list = array(
			$this->_prefix . 'STATUS',
			$this->_prefix . 'SORT_ORDER',
			$this->_prefix . 'INFO_MAIL',
			$this->_prefix . 'ORDER_STATUS_ID',
			$this->_prefix . 'PROCESSING_ORDER_STATUS_ID',
			$this->_prefix . 'EMAIL',
			$this->_prefix . 'ID',
			$this->_prefix . 'SECRET_WORD',
			);

		 return $keys_list;
	}

	/**
	 * Instalacja modułu moneybookers.
	 * 
	 */
	function install() {
		global $db;

		//Czyścimy
		$db->Execute("DELETE from " . TABLE_CONFIGURATION . " where configuration_key LIKE '" . $this->_prefix . "%'");

		//Na razie nic nie wstawiamy.
		$s = 0;
//		$this->_toConf($key, $value, $title, $description, $sortOrder, $setFunction, $useFunction)
		$this->_toConf('STATUS', 'True', MODULE_PAYMENT_MONEYBOOKERS_TEXT_ENABLED, MODULE_PAYMENT_MONEYBOOKERS_TEXT_ENABLED_DESC, $s++, 'zen_cfg_select_option(array(\'True\', \'False\'), ');
		$this->_toConf('INFO_MAIL', STORE_OWNER_EMAIL_ADDRESS, MODULE_PAYMENT_MONEYBOOKERS_TEXT_EMAIL_TO_MSG, MODULE_PAYMENT_MONEYBOOKERS_TEXT_EMAIL_TO_MSG_DESC, $s++, 'mb_cfg_info_email(');
		$this->_toConf('SORT_ORDER', 0, MODULE_PAYMENT_MONEYBOOKERS_TEXT_ORDEROFDISPLAY, MODULE_PAYMENT_MONEYBOOKERS_TEXT_ORDEROFDISPLAY_DESC, $s++);
		$this->_toConf('ORDER_STATUS_ID', 2, MODULE_PAYMENT_MONEYBOOKERS_TEXT_STATUS_ORDER, MODULE_PAYMENT_MONEYBOOKERS_TEXT_STATUS_ORDER_DESC, $s++, "zen_cfg_pull_down_order_statuses(", "zen_get_order_status_name");
		$this->_toConf('PROCESSING_ORDER_STATUS_ID', DEFAULT_ORDERS_STATUS_ID, MODULE_PAYMENT_MONEYBOOKERS_TEXT_STATUS_PENDING, MODULE_PAYMENT_MONEYBOOKERS_TEXT_STATUS_PENDING_DESC, $s++, "mb_cfg_pull_down_order_statuses(", "zen_get_order_status_name");
		$this->_toConf('EMAIL', STORE_OWNER_EMAIL_ADDRESS, MODULE_PAYMENT_MONEYBOOKERS_TEXT_EMAIL, MODULE_PAYMENT_MONEYBOOKERS_TEXT_EMAIL_DESC, $s++, 'mb_cfg_email(');
		$this->_toConf('SECRET_WORD', '', MODULE_PAYMENT_MONEYBOOKERS_TEXT_SECRET_WORD, MODULE_PAYMENT_MONEYBOOKERS_TEXT_SECRET_WORD_DESC, $s++, 'mb_cfg_scr_word(');
		$this->_toConf('ID', '', MODULE_PAYMENT_MONEYBOOKERS_TEXT_MONEYBOOKERS_ID, MODULE_PAYMENT_MONEYBOOKERS_TEXT_MONEYBOOKERS_ID_DESC, $s++, 'mb_cfg_ident(');
		
		$this->_addZone("Germany", 10000, 81);
		$this->_addZone("SoforZone", 10001, array(81, 14, 73, 222, 204));
		$this->_addZone('France', 10002, 73);
		$this->_addZone('Italy', 10003, 105);
		$this->_addZone('Poland', 10004, 170);
		$this->_addZone('Denmark', 10005, 57);
		$this->_addZone('Singapore', 10006, 188);
		$this->_addZone('Austria', 10007, 14);
		$this->_addZone('Bulgaria', 10008, 33);
		$this->_addZone('Netherlands', 10009, 150);
		$this->_addZone('Ireland', 10010, 103);
		$this->_addZone('Finland', 10011, 72);
		$this->_addZone('Australia', 10012, 13);
		$this->_addZone('Sweden', 10013, 203);
		$this->_addZone('MaestroZone', 10014, array(14, 195, 222));
		
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

//		var_dump($sql);
//		die();

		$db->Execute($sql);
	}

	public function _get($pos) {
		if (defined($this->_prefix . $pos)) {
			return constant($this->_prefix . $pos);
		} else {
			throw new Exception("Bad constant '$this->_prefix$pos'");
		}
	}

	public function _defined($pos) {
		return defined($this->_prefix . $pos);
	}

	function selection() {
		return false;
	}

	function javascript_validation() {
		return false;
	}

	function _addZone($country, $id, $countries) {
		global $db;

		$db->Execute("REPLACE INTO `" . TABLE_GEO_ZONES . "` (geo_zone_id, geo_zone_name, geo_zone_description, date_added) VALUES($id, '$country', 'Created By Moneybookers Plugin', NOW());");
		
		if (!is_array($countries)) {
			$countries = array($countries);
		}

		foreach ($countries as $c) {
			$db->Execute("INSERT INTO `" . TABLE_ZONES_TO_GEO_ZONES . "` (zone_country_id, zone_id, geo_zone_id, date_added) VALUES($c, 0, $id, NOW());");
		}

	}
}

/**
 * TODO:
 *  - Funkcje pomocnicze powinny być gdzieś wywalone, ale narazie nie mam zielonego pojęcia gdzie powinny uciec,
 *    a tutaj wydaje się być dla nich całkiem przyjazne miejsce.
 */


?>
