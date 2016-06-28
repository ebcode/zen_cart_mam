<?php
/**
 * Mediaflex Sp. z O.O.
 * @author krystian.kuczek@mediaflex.pl
 * @package Moneybookers Payment Plugin
 */

include_once(zen_get_file_directory(dirname(__FILE__) . DIRECTORY_SEPARATOR . 'moneybookers' . DIRECTORY_SEPARATOR . 'moneybookers_payment', __CLASS__, 'false'));

class moneybookersMaestro extends moneybookers_payment {

	var $_prefix = 'MODULE_PAYMENT_MONEYBOOKERS_MAESTRO_';
	var $_imgLink = 'http://www.moneybookers.com/creatives/logos/poweredby_logos/nomb/120x60px/maestro.gif';
	var $_paymentMethod = "MAE";
	var $_returnUrl;
	var $_zoneId = 10014;

	function moneybookersMaestro() {
		global $messageStack;

		$this->_returnUrl = zen_href_link(FILENAME_CHECKOUT_PROCESS, 'referer=' . __CLASS__, 'SSL');

		$this->code = __CLASS__;
		$this->codeVersion = '0.0.1.a';
		$this->description = 'Maestro';
		$this->title = "Maestro (powered by moneybookers) - Spain, United Kingdom, Austria";

		parent::moneybookers_payment();
	}
}
?>