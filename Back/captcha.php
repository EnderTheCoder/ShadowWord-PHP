<?php
header('Content-type:image/jpeg');
session_start();
$width = 120;
$height = 30;
$string = '';
$img = imagecreatetruecolor($width, $height);
$arr = array('a', 'b', 'c', 'd', 'e', 'f', 'g', 'h', 'i', 'j', 'k', 'l', 'm', 'n', 'o', 'p', 'q', 'r', 's', 't', 'u', 'v', 'w', 'x', 'y', 'z', '0', '1', '2', '3', '4', '5', '6', '7', '8', '9');
$colorBg = imagecolorallocate($img, rand(200, 255), rand(200, 255), rand(200, 255));
imagefill($img, 0, 0, $colorBg);
for ($m = 0; $m <= 100; $m++) {
    $pointcolor = imagecolorallocate($img, rand(0, 255), rand(0, 255), rand(0, 255));
    imagesetpixel($img, rand(0, $width - 1), rand(0, $height - 1), $pointcolor);
}
for ($i = 0; $i <= 4; $i++) {
    $linecolor = imagecolorallocate($img, rand(0, 255), rand(0, 255), rand(0, 255));
    imageline($img, rand(0, $width), rand(0, $height), rand(0, $width), rand(0, $height), $linecolor);
}
for ($i = 0; $i < 4; $i++) {
    $string .= $arr[rand(0, count($arr) - 1)];
}
$_SESSION['captcha'] = $string;
$colorString = imagecolorallocate($img, rand(10, 100), rand(10, 100), rand(10, 100));//文本
imagestring($img, 5, rand(0, $width - 36), rand(0, $height - 15), $string, $colorString);
imagejpeg($img);
imagedestroy($img);