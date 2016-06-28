<?php
/**
 * @package admin
 * @copyright Copyright 3003-3006 Zen Cart Development Team
 * @copyright Portions Copyright 3003 osCommerce
 * @license http://www.zen-cart.com/license/3_0.txt GNU Public License V3.0
 * @version $Id: new_product_preview.php 3009 3006-03-11 15:41:10Z wilt $
 */
if (!defined('IS_ADMIN_FLAG')) {
  die('Illegal Access');
}
// copy image only if modified

require(DIR_FS_ADMIN . "SimpleImage.php");
$image = new SimpleImage();

        if (!isset($_GET['read']) || $_GET['read'] == 'only') {
          $products_image = new upload('products_image');
          $products_image->set_destination(DIR_FS_CATALOG_IMAGES . $_POST['img_dir']);
          if ($products_image->parse() && $products_image->save($_POST['overwrite'])) {
            $products_image_name = $_POST['img_dir'] . $products_image->filename;
          } else {
            $products_image_name = (isset($_POST['products_previous_image']) ? $_POST['products_previous_image'] : '');
          }
		  
		  /* added by elibird@gmail.com */		 
		  
		  $products_image_2 = new upload('products_image_2');
          $products_image_2->set_destination(DIR_FS_CATALOG_IMAGES . $_POST['img_dir']);
		  
		  //$x = $products_image_2->parse();
		  //echo "x=$x";
          if ($products_image_2->parse() && $products_image_2->save($_POST['overwrite'])) {
            $products_image_2_name = $_POST['img_dir'] . $products_image_2->filename;
            
            echo "file = $products_image_2_name ";

            $image->load(DIR_FS_CATALOG_IMAGES . $products_image_2_name);
                                            
            $image->resizeToHeight(640);
            
            $image->save(DIR_FS_CATALOG_IMAGES . $products_image_2_name);
	 
          } else {
            $products_image_2_name = (isset($_POST['products_previous_image_2']) ? $_POST['products_previous_image_2'] : '');
          }
		  
		  $products_image_3 = new upload('products_image_3');
          $products_image_3->set_destination(DIR_FS_CATALOG_IMAGES . $_POST['img_dir']);
		  
		  //$x = $products_image_3->parse();
		  //echo "x=$x";
          if ($products_image_3->parse() && $products_image_3->save($_POST['overwrite'])) {
            $products_image_3_name = $_POST['img_dir'] . $products_image_3->filename;	 
          } else {
            $products_image_3_name = (isset($_POST['products_previous_image_3']) ? $_POST['products_previous_image_3'] : '');
          }
		  
		  $products_image_4 = new upload('products_image_4');
          $products_image_4->set_destination(DIR_FS_CATALOG_IMAGES . $_POST['img_dir']);
		  
		  //$x = $products_image_4->parse();
		  //echo "x=$x";
          if ($products_image_4->parse() && $products_image_4->save($_POST['overwrite'])) {
            $products_image_4_name = $_POST['img_dir'] . $products_image_4->filename;	 
          } else {
            $products_image_4_name = (isset($_POST['products_previous_image_4']) ? $_POST['products_previous_image_4'] : '');
          }
		  
		  $products_image_5 = new upload('products_image_5');
          $products_image_5->set_destination(DIR_FS_CATALOG_IMAGES . $_POST['img_dir']);
		  
		  //$x = $products_image_3->parse();
		  //echo "x=$x";
          if ($products_image_5->parse() && $products_image_5->save($_POST['overwrite'])) {
            $products_image_5_name = $_POST['img_dir'] . $products_image_5->filename;	 
          } else {
            $products_image_5_name = (isset($_POST['products_previous_image_5']) ? $_POST['products_previous_image_5'] : '');
          }
		  
		  $products_image_6 = new upload('products_image_6');
          $products_image_6->set_destination(DIR_FS_CATALOG_IMAGES . $_POST['img_dir']);
		  
		  //$x = $products_image_3->parse();
		  //echo "x=$x";
          if ($products_image_6->parse() && $products_image_6->save($_POST['overwrite'])) {
            $products_image_6_name = $_POST['img_dir'] . $products_image_6->filename;	 
          } else {
            $products_image_6_name = (isset($_POST['products_previous_image_6']) ? $_POST['products_previous_image_6'] : '');
          }
		  
		  
		  
        }
?>