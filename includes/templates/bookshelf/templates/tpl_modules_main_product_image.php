<?php
/**
 * Module Template
 *
 * @package templateSystem
 * @copyright Copyright 2003-2005 Zen Cart Development Team
 * @copyright Portions Copyright 2003 osCommerce
 * @license http://www.zen-cart.com/license/2_0.txt GNU Public License V2.0
 * @version $Id: tpl_modules_main_product_image.php 3208 2006-03-19 16:48:57Z birdbrain $
 */
?>
<?php require(DIR_WS_MODULES . zen_get_module_directory(FILENAME_MAIN_PRODUCT_IMAGE)); ?> 
<div id="productMainImage" class="centeredContent back">
	<div style="float:left; margin:2px;">
<?php // bof Zen Lightbox 2008-12-15 aclarke ?>
<?php
if (ZEN_LIGHTBOX_STATUS == 'true') {
  if (ZEN_LIGHTBOX_GALLERY_MODE == 'true' && ZEN_LIGHTBOX_GALLERY_MAIN_IMAGE == 'true') {
    $rel = 'lightbox-g';
  } else {
    $rel = 'lightbox';
  }
?>
<script language="javascript" type="text/javascript"><!--
document.write('<?php echo '<a href="' . zen_lightbox($products_image_large, addslashes($products_name), LARGE_IMAGE_WIDTH, LARGE_IMAGE_HEIGHT) . '" rel="' . $rel . '" title="' . addslashes($products_image_label) . '">' . zen_image($products_image_medium, addslashes($products_name), MEDIUM_IMAGE_WIDTH, MEDIUM_IMAGE_HEIGHT) . '<br /><span class="imgLink">' . $products_image_label . '</span></a>'; ?>');
//--></script>
<?php } else { ?>
<?php // eof Zen Lightbox 2008-12-15 aclarke ?>
<script language="javascript" type="text/javascript"><!--
document.write('<?php echo '<a href="javascript:popupWindow(\\\'' . zen_href_link(FILENAME_POPUP_IMAGE, 'pID=' . $_GET['products_id']) . '\\\')">' . zen_image($products_image_medium, addslashes($products_name), MEDIUM_IMAGE_WIDTH, MEDIUM_IMAGE_HEIGHT) . '<br /><span class="imgLink">' . $products_image_label . '</span></a>'; ?>');
//--></script>
<?php // bof Zen Lightbox 2008-12-15 aclarke ?>
<?php } ?>
<?php // eof Zen Lightbox 2008-12-15 aclarke ?>
<noscript>
<?php
  echo '<a href="' . zen_href_link(FILENAME_POPUP_IMAGE, 'pID=' . $_GET['products_id']) . '" target="_blank">' . zen_image($products_image_medium, $products_name, MEDIUM_IMAGE_WIDTH, MEDIUM_IMAGE_HEIGHT) . '<br /><span class="imgLink">' . TEXT_CLICK_TO_ENLARGE . '</span></a>';
?>
</noscript>
</div>


<?php 
if($products_image_2!=''){
	?>
	<div style="float:left;margin:2px;">
	<?php //echo zen_image('./images/'.$products_image_2, addslashes($products_name), MEDIUM_IMAGE_WIDTH, MEDIUM_IMAGE_HEIGHT); ?>
	<script language="javascript" type="text/javascript"><!--
document.write('<?php echo '<a href="' . zen_lightbox('./images/'.$products_image_2, addslashes($products_name), LARGE_IMAGE_WIDTH, LARGE_IMAGE_HEIGHT) . '" rel="' . $rel . '" title="' . addslashes($products_image_2_label) . '">' . zen_image('./images/'.$products_image_2, addslashes($products_name), MEDIUM_IMAGE_WIDTH, MEDIUM_IMAGE_HEIGHT) . '<br /><span class="imgLink">' . $products_image_2_label . '</span></a>'; ?>');
//--></script>
	</div>
	<?php
}
?>

<?php 
if($products_image_3!=''){
	?>
	<div style="float:left;margin:2px;">
	<?php //echo zen_image('./images/'.$products_image_2, addslashes($products_name), MEDIUM_IMAGE_WIDTH, MEDIUM_IMAGE_HEIGHT); ?>
	<script language="javascript" type="text/javascript"><!--
document.write('<?php echo '<a href="' . zen_lightbox('./images/'.$products_image_3, addslashes($products_name), LARGE_IMAGE_WIDTH, LARGE_IMAGE_HEIGHT) . '" rel="' . $rel . '" title="' . addslashes($products_image_3_label) . '">' . zen_image('./images/'.$products_image_3, addslashes($products_name), MEDIUM_IMAGE_WIDTH, MEDIUM_IMAGE_HEIGHT) . '<br /><span class="imgLink">' . $products_image_3_label . '</span></a>'; ?>');
//--></script>
	</div>
	<?php
}
?>

<?php 
if($products_image_4!=''){
	?>
	<div style="float:left;margin:2px;">
	<?php //echo zen_image('./images/'.$products_image_2, addslashes($products_name), MEDIUM_IMAGE_WIDTH, MEDIUM_IMAGE_HEIGHT); ?>
	<script language="javascript" type="text/javascript"><!--
document.write('<?php echo '<a href="' . zen_lightbox('./images/'.$products_image_4, addslashes($products_name), LARGE_IMAGE_WIDTH, LARGE_IMAGE_HEIGHT) . '" rel="' . $rel . '" title="' . addslashes($products_image_4_label) . '">' . zen_image('./images/'.$products_image_4, addslashes($products_name), MEDIUM_IMAGE_WIDTH, MEDIUM_IMAGE_HEIGHT) . '<br /><span class="imgLink">' . $products_image_4_label . '</span></a>'; ?>');
//--></script>
	</div>
	<?php
}
?>

<?php 
if($products_image_5!=''){
	?>
	<div style="float:left;margin:2px;">
	<?php //echo zen_image('./images/'.$products_image_2, addslashes($products_name), MEDIUM_IMAGE_WIDTH, MEDIUM_IMAGE_HEIGHT); ?>
	<script language="javascript" type="text/javascript"><!--
document.write('<?php echo '<a href="' . zen_lightbox('./images/'.$products_image_5, addslashes($products_name), LARGE_IMAGE_WIDTH, LARGE_IMAGE_HEIGHT) . '" rel="' . $rel . '" title="' . addslashes($products_image_5_label) . '">' . zen_image('./images/'.$products_image_5, addslashes($products_name), MEDIUM_IMAGE_WIDTH, MEDIUM_IMAGE_HEIGHT) . '<br /><span class="imgLink">' . $products_image_5_label . '</span></a>'; ?>');
//--></script>
	</div>
	<?php
}
?>

<?php 
if($products_image_6!=''){
	?>
	<div style="float:left;margin:2px;">
	<?php //echo zen_image('./images/'.$products_image_2, addslashes($products_name), MEDIUM_IMAGE_WIDTH, MEDIUM_IMAGE_HEIGHT); ?>
	<script language="javascript" type="text/javascript"><!--
document.write('<?php echo '<a href="' . zen_lightbox('./images/'.$products_image_6, addslashes($products_name), LARGE_IMAGE_WIDTH, LARGE_IMAGE_HEIGHT) . '" rel="' . $rel . '" title="' . addslashes($products_image_6_label) . '">' . zen_image('./images/'.$products_image_6, addslashes($products_name), MEDIUM_IMAGE_WIDTH, MEDIUM_IMAGE_HEIGHT) . '<br /><span class="imgLink">' . $products_image_6_label . '</span></a>'; ?>');
//--></script>
	</div>
	<?php
}
?>


<div style="clear:left; font-size:13px; color:red;">
Due to basket design, <br>not all items are visible.
</div>
</div>
