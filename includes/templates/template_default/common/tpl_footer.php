<?php
/**
 * Common Template - tpl_footer.php
 *
 * this file can be copied to /templates/your_template_dir/pagename<br />
 * example: to override the privacy page<br />
 * make a directory /templates/my_template/privacy<br />
 * copy /templates/templates_defaults/common/tpl_footer.php to /templates/my_template/privacy/tpl_footer.php<br />
 * to override the global settings and turn off the footer un-comment the following line:<br />
 * <br />
 * $flag_disable_footer = true;<br />
 *
 * @package templateSystem
 * @copyright Copyright 2003-2010 Zen Cart Development Team
 * @copyright Portions Copyright 2003 osCommerce
 * @license http://www.zen-cart.com/license/2_0.txt GNU Public License V2.0
 * @version $Id: tpl_footer.php 15511 2010-02-18 07:19:44Z drbyte $
 */
require(DIR_WS_MODULES . zen_get_module_directory('footer.php'));
?>

<?php
if (!isset($flag_disable_footer) || !$flag_disable_footer) {
?>

<!--bof-navigation display -->
<?php /*
<div id="navSuppWrapper">
<div id="navSupp">
<ul>
<li><?php echo '<a href="' . HTTP_SERVER . DIR_WS_CATALOG . '">'; ?><?php echo HEADER_TITLE_CATALOG; ?></a></li>
<?php if (EZPAGES_STATUS_FOOTER == '1' or (EZPAGES_STATUS_FOOTER == '2' and (strstr(EXCLUDE_ADMIN_IP_FOR_MAINTENANCE, $_SERVER['REMOTE_ADDR'])))) { ?>
<li><?php require($template->get_template_dir('tpl_ezpages_bar_footer.php',DIR_WS_TEMPLATE, $current_page_base,'templates'). '/tpl_ezpages_bar_footer.php'); ?></li>
<?php } ?>
</ul>
</div>
</div>
 * 
 */
 ?>
 <table border="0" cellpadding="0" cellspacing="0" style="background-color:#43201c; color: #fff; width:1000px; border-radius:5px;">
     <tr>
         <td><a href="">Our Guarantee</a><br>
             <a href="">About Us</a><br>
             <a href="">Corporate Orders</a><br>
             <a href="">Shipping & Delivery Info</a><br>
             <a href="">Local Delivery Areas</a><br>
             <a href="">Lisa's Bio</a><br>
         </td>
         <td><a href="">Home</a><br>
             <a href="">My Account</a><br>
             <a href="">Shopping Cart</a><br>
             <a href="">Contact Us</a><br>
             <a href="">Link Exchange</a><br>
             <a href="">Privacy Policy</a><br>
         </td>
         <td>
             <div align="center">
                 Categories
             </div>
             <div style="background-color:#fff; color: #43201c; border-radius:5px;">
                 <table cellpadding="0" cellspacing="0" border="0">
                     <tr>
                         <td><a href="">Browse All Gift Baskets</a><br>
                             <a href="">Build Your Own Basket</a><br>
                             <a href="">All Holiday Gift Baskets</a><br>
                             <a href="">Candy Platters &amp; Bouquets</a><br>
                             <a href="">Cookie Bouquets</a><br>
                         </td>
                         <td><a href="">Fresh Baked Cookies</a><br>
                             <a href="">$9.99 Sweet Greetings</a><br>
                             <a href="">Party Favors &amp; Centerpieces</a><br>
                             <a href="">Shop By Occasion</a><br>
                             <a href="">Graduation Gifts</a><br>
                         </td>
                     </tr>
                 </table>
             </div>
         </td>
     </tr>
 </table>
<!--eof-navigation display -->

<!--bof-ip address display -->
<?php
if (SHOW_FOOTER_IP == '1') {
?>
<div id="siteinfoIP"><?php echo TEXT_YOUR_IP_ADDRESS . '  ' . $_SERVER['REMOTE_ADDR']; ?></div>
<?php
}
?>
<!--eof-ip address display -->

<!--bof-banner #5 display -->
<?php
  if (SHOW_BANNERS_GROUP_SET5 != '' && $banner = zen_banner_exists('dynamic', SHOW_BANNERS_GROUP_SET5)) {
    if ($banner->RecordCount() > 0) {
?>
<div id="bannerFive" class="banners"><?php echo zen_display_banner('static', $banner); ?></div>
<?php
    }
  }
?>
<!--eof-banner #5 display -->

<!--bof- site copyright display -->
<div id="siteinfoLegal" class="legalCopyright"><?php echo FOOTER_TEXT_BODY; ?></div>
<!--eof- site copyright display -->

<?php
} // flag_disable_footer
?>