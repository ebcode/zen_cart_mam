<?php
/**
 * Mediaflex Sp. z O.O.
 * @author krystian.kuczek@mediaflex.pl
 * @package Moneybookers Payment Plugin
 */

include_once(zen_get_file_directory(dirname(__FILE__) . DIRECTORY_SEPARATOR . 'moneybookers' . DIRECTORY_SEPARATOR . 'moneybookers_payment', __CLASS__, 'false'));

class moneybookersPrzelewy24 extends moneybookers_payment {

	var $_prefix = 'MODULE_PAYMENT_MONEYBOOKERS_PRZELEWY24_';
	var $_imgLink = 'http://www.moneybookers.com/creatives/logos/poweredby_logos/nomb/120x60px/p24.gif';
	var $_paymentMethod = "PWY";
	var $_returnUrl;
	var $_zoneId = 10004;
  
	function moneybookersPrzelewy24() {
		global $messageStack;

		$this->code = __CLASS__;
		$this->codeVersion = '0.0.1.a';
		$this->description = 'All polish banks';
		$this->title = "Przelewy24 (powered by moneybookers) - Poland";

		$this->_returnUrl = zen_href_link(FILENAME_CHECKOUT_PROCESS, 'referer=' . $this->code, 'SSL');

		parent::moneybookers_payment();
	}
}

?>
