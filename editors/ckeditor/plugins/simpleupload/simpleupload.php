<?php

//require_once('config.php');

$img_folder = 'images';

$save_location = $_SERVER["DOCUMENT_ROOT"]."/".$img_folder."/";

if($_FILES) {
//0000
    
    $funcNum = $_GET['CKEditorFuncNum'] ;

    $file_name = $_FILES['upload']['name'];

    $ptrn = '/[^A-Za-z0-9\.]/';
    $file_name = preg_replace($ptrn, '_', $file_name);
    
    $file_location = $save_location . $file_name;
    
    $message = 'moving to ' . $file_location;  

    $url = '/'.$img_folder.'/'.$file_name;
    if (move_uploaded_file($_FILES['upload']['tmp_name'], $file_location)) {
     
    echo "<script type='text/javascript'>window.parent.CKEDITOR.tools.callFunction($funcNum, '$url', '$message');</script>";

    }

}

?>