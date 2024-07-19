<?php
$canvas = imagecreatetruecolor(1200, 630);
$backgroundColor = imagecolorallocate($canvas, 255, 255, 255);
$textColor       = imagecolorallocate($canvas, 0, 0, 0);
$subtextColor    = imagecolorallocate($canvas, 148, 148, 148);

imagefill($canvas, 0, 0, $backgroundColor);


$fontFile = './assets/fonts/matter/Matter-Regular.otf';

$title  = $page->title()->toString();
$title  = wordwrap($title, 30);
$subtitle  = "Project";
imagefttext($canvas, 92, 0, 32, 282, $textColor, $fontFile, $title);
imagefttext($canvas, 92, 0, 32, 144, $subtextColor, $fontFile, $subtitle);

// Place logo in the corner
$logoFile = './assets/images/logo-og.png';
$logo     = imagecreatefrompng($logoFile);

imagecopyresampled($canvas, $logo, 32, 488, 0, 0, 375, 110, imagesx($logo), imagesy($logo));

// Output image to the browser
imagepng($canvas);
imagedestroy($canvas);