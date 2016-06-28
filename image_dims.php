<?php

$img = urldecode($_GET['img']);

$img = explode('?', $img);
$img = $img[0];

echo json_encode(getimagesize($img));
