<?php
/**
 * Mediaflex Sp. z O.O.
 * @author krystian.kuczek@mediaflex.pl
 * @package Moneybookers Payment Plugin
 */

include_once(zen_get_file_directory(dirname(__FILE__) . DIRECTORY_SEPARATOR . 'moneybookers' . DIRECTORY_SEPARATOR . 'moneybookers_payment', __CLASS__, 'false'));

class moneybookersEPay extends moneybookers_payment {

	var $_prefix = 'MODULE_PAYMENT_MONEYBOOKERS_EPAY_';
	var $_imgLink = 'http://www.moneybookers.com/creatives/logos/poweredby_logos/nomb/120x60px/epay.gif';
	var $_paymentMethod = "EPY";
	var $_returnUrl;
	var $_zoneId = 10008;
  
	function moneybookersEPay() {
		global $messageStack;

		$this->code = __CLASS__;
		$this->codeVersion = '0.0.1.a';
		$this->description = 'ePay';
		$this->title = "ePay (powered by moneybookers) - Bulgaria";

		$this->_returnUrl = zen_href_link(FILENAME_CHECKOUT_PROCESS, 'referer=' . $this->code, 'SSL');

		parent::moneybookers_payment();
	}
}
?>