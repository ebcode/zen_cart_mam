<?php

require("./SimpleImage.php");


//   include('SimpleImage.php');

touch("what what");

$image = new SimpleImage();

if($h = opendir("./")){
 echo "<br>handle=$h";
 echo "<br>entries:<br>";
	while(false!== ($entry = readdir($h))){
		$width=0;
		echo "$entry<br>";
		//chmod($entry, 666);
		if($entry!='.' && $entry!='..'){
			/*
			if('resized'==substr($entry,0,7)){
				echo "MV<BR>";
				//unlink(str_replace('resized_', '', $entry));
				$replace = str_replace('resized_', '', $entry);
				echo "r=$replace<br>";
			
				copy($entry, $replace);
				echo "OK<BR>";
			}*/
			
			if($fs=filesize($entry)){
				if(substr($entry, strlen($entry)-4,4)=='.jpg' || substr($entry, strlen($entry)-4,4)=='.JPG'){
					echo "JPG: $fs<br>";
					list($width, $height, $type, $attr) = getimagesize($entry);
					echo "w: $width, h: $height<br>";
					if($width>480){ //resize
						echo "RESIZING<br>";
						$image->load($entry);
						$image->resizeToWidth(480);
						$image->save($entry);
						echo "DONE.<br>";
					}
				}
			}
		}
	}	
}


/*
$image->load('extras 071.jpg');
$image->resizeToWidth(550);
$image->save('picture2.jpg');
*/

echo "<br>OK";