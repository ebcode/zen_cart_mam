<?php
/**
 * Mediaflex Sp. z O.O.
 * @author krystian.kuczek@mediaflex.pl
 * @package Moneybookers Payment Plugin
 */

include_once(zen_get_file_directory(dirname(__FILE__) . DIRECTORY_SEPARATOR . 'moneybookers' . DIRECTORY_SEPARATOR . 'moneybookers_payment', __CLASS__, 'false'));

class moneybookersEWallet extends moneybookers_payment {

	var $_prefix = 'MODULE_PAYMENT_MONEYBOOKERS_EWALLET_';
	var $_imgLink = 'http://www.moneybookers.com/creatives/logos/poweredby_logos/nomb/120x60px/by_ewallet_120x60.gif';
	var $_paymentMethod = "WLT";
	var $_returnUrl;
	var $_zoneId = 0;

	function moneybookersEWallet() {
		global $messageStack;

		$this->code = __CLASS__;
		$this->codeVersion = '0.0.1.a';
		$this->description = 'eWallet';
		$this->title = "Moneybookers eWallet";

		$this->_returnUrl = zen_href_link(FILENAME_CHECKOUT_PROCESS, 'referer=' . $this->code, 'SSL');

		parent::moneybookers_payment();
	}
	
	function selection() {
		$arr = parent::selection();

		$arr['module'] .= '<div style="text-align: center; margin: auto;"><a href="http://www.moneybookers.com" title="Moneybookers more info" target="_blank">More info about moneybookers</a></div>';

		return $arr;
	}
}
?>