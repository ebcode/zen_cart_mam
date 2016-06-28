<?php
/**
 * Mediaflex Sp. z O.O.
 * @author krystian.kuczek@mediaflex.pl
 * @package Moneybookers Payment Plugin
 */

// This should be first line of the script:
$zco_notifier->notify('NOTIFY_HEADER_START_MONEYBOOKERS_PAYMENT');

foreach ($_POST as $key => $val) {
	$tab[] = $key . "=" . $val;
}

$get = implode("&amp;", $tab);

require(DIR_WS_MODULES . zen_get_module_directory('require_languages.php'));
$breadcrumb->add(NAVBAR_TITLE_1, zen_href_link(FILENAME_CHECKOUT_SHIPPING, '', 'SSL'));
$breadcrumb->add(NAVBAR_TITLE_2);

// This should be last line of the script:
$zco_notifier->notify('NOTIFY_HEADER_END_MONEYBOOKERS_PAYMENT');
?>