<?php
$txt = $_GET['t'];

header("Content-Type: image/png");
$im = @imagecreate(75, 75)
    or die("Cannot Initialize new GD image stream");
$background_color = imagecolorallocate($im, 255, 255, 255);
$text_color = imagecolorallocate($im, 233, 14, 91);
imagestring($im, 3, 5, 25,  $txt, $text_color);
imagepng($im);
imagedestroy($im);
?>