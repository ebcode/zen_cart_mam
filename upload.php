<?php
error_reporting(E_ALL);
ini_set('display_errors',1);
ini_set('error_log','my_file.log');

ini_set('max_execution_time', '180');

ini_set('max_input_time', '180');

ini_set('memory_limit', '1024M');

require('cart_admin/includes/application_top.php');

$err = 0;
//get latest timestamp from meets.
$pwd = `pwd`;

$uploaddir = trim($pwd).'/images/';

if($_FILES) {
//0000

$file_name = $_FILES['img_file']['name'];

//replace spaces w/ underscores
$file_name = str_replace(' ', '_', $file_name);

$file_location = $uploaddir . $file_name;

if (move_uploaded_file($_FILES['img_file']['tmp_name'], $file_location)) {
    //echo "alright!";
    
    $js_location = 'images/' . $file_name;
    
    //get dimensions of image
    $img_size = getimagesize($file_location);
    
    //resize the image if it's huge
    if($img_size[0] > 1000 | $img_size[1] > 1000){
        
        echo ">";
    
        require('SimpleImage.php');
        $image = new SimpleImage();
        

        $image->load($file_location);
        $image->resizeToWidth(800);
        $image->save($file_location);

        echo "?";
        
        $img_size = getimagesize($file_location);

    }

    ?><script>
    window.parent.changeImg('<?php echo $js_location; ?>',<?php echo $img_size[0]; ?>,<?php echo $img_size[1]; ?>);    
    </script>
    <?php
}

} else {

    if(isset($_GET['offset_x'])){
        
        $offset_x = (int) $_GET['offset_x'];
        $offset_y = (int) $_GET['offset_y'];
        $height = (int) $_GET['height'];
        $width = (int) $_GET['width'];
        
        $img = explode('?',$_GET['img_name']);    
        $img = $img[0];  //remove ? query after image

        //resize img
        $new_im = imagecreatetruecolor ( $width , $height );
        
        //GET FILE EXTENSION
        $file_ext = explode(".",$img);
        $file_ext = strtolower($file_ext[1]);
        
        switch ($file_ext){
            case 'jpg':
            case 'jpeg':
                $original = imagecreatefromjpeg('images/'.$img);
                break;
            case 'png':
                $original = imagecreatefrompng('images/'.$img);
                break;
            case 'gif':
                $original = imagecreatefromgif('images/'.$img);
                break;
        }
        
        

        imagecopyresized($new_im, $original, 0, 0, $offset_x, $offset_y, $width, $height, $width, $height);        
         
        //imagejpeg($new_im, $uploaddir.$img, 90);
        
        echo "<br> file_ext = " . $file_ext;

        switch ($file_ext){
            case 'jpg':
            case 'jpeg':
                echo "<br> writing to " . $uploaddir.$img;
                imagejpeg($new_im, $uploaddir.$img, 90) or die('could not write image');
                break;
            case 'png':
                imagepng($new_im, $uploaddir.$img) or die('could not write image');
                break;
            case 'gif':
                imagegif($new_im, $uploaddir.$img) or die('could not write image');
                break;
        }
        
        $js_location = 'images/' . $img;

        ?><script>
    window.parent.changeImg('<?php echo $js_location; ?>',<?php echo $width; ?>,<?php echo $height; ?>);    
    </script>
    <?php

    }  else if(isset($_GET['resize_width'])) {
        
        require('SimpleImage.php');
        $image = new SimpleImage();
        
        // $img = $_GET['img_name_2'];
        $img = explode('?',$_GET['img_name_2']);    
        $img = $img[0];  //remove ? query after image

        $image->load('./images/'.$img);
        $image->resizeToWidth((int)$_GET['resize_width']);
        $image->save('./images/'.$img);
        
        $img_size = getimagesize('./images/'.$img);
        
        $js_location = 'images/' . $img;

        ?>
        <script>
    window.parent.changeImg('<?php echo $js_location; ?>',<?php echo $img_size[0]; ?>,<?php echo $img_size[1]; ?>);    
    </script>
        <?php
    }
}

echo "OK";

?>