<?php
/**
 * load the system wide functions
 * see {@link  http://www.zen-cart.com/wiki/index.php/Developers_API_Tutorials#InitSystem wikitutorials} for more details.
 *
 * @package initSystem
 * @copyright Copyright 2003-2005 Zen Cart Development Team
 * @copyright Portions Copyright 2003 osCommerce
 * @license http://www.zen-cart.com/license/2_0.txt GNU Public License V2.0
 * @version $Id: init_general_funcs.php 2845 2006-01-13 06:49:15Z drbyte $
 */
if (!defined('IS_ADMIN_FLAG')) {
  die('Illegal Access');
}
/**
 * General Functions
 */
 
 ini_set('error_log', '/home/anythi19/php_errors');
 
 //echo "<br>" . __LINE__ . ": OK";
 //echo "<br> trying to require: " . DIR_WS_FUNCTIONS . 'functions_general.php';
 //echo `pwd`;
require(DIR_WS_FUNCTIONS . 'functions_general.php');
/**
 * html_output functions (href_links, input types etc)
 */
 
  //echo "<br>" . __LINE__ . ": OK";
require(DIR_WS_FUNCTIONS . 'html_output.php');
/**
 * basic email functions
 */
  //echo "<br>" . __LINE__ . ": OK";
require(DIR_WS_FUNCTIONS . 'functions_email.php');
/**
 * EZ-Pages functions
 */
  //echo "<br>" . __LINE__ . ": OK";
require(DIR_WS_FUNCTIONS . 'functions_ezpages.php');
/**
 * User Defined Functions
 */
  //echo "<br>" . __LINE__ . ": OK";
include(DIR_WS_MODULES . 'extra_functions.php');

