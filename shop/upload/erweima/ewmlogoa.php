
<?php
include ('upload/erweima/phpqrcode.php');
$value = "http://zhaowei.ppcom.net";
//echo $value;
$errorCorrectionLevel = 'L';
$matrixPointSize = 10;
QRcode::png ( $value, 'upload/erweima/ewm.png', $errorCorrectionLevel, $matrixPointSize, 2 );
$logo = 'upload/erweima/emwlogo.gif';
$QR = 'upload/erweima/ewm.png';
if ($logo !== FALSE) {
    $QR = imagecreatefromstring ( file_get_contents ( $QR ) );
    $logo = imagecreatefromstring ( file_get_contents ( $logo ) );
    $QR_width = imagesx ( $QR );
    $QR_height = imagesy ( $QR );
    $logo_width = imagesx ( $logo );
    $logo_height = imagesy ( $logo );
    $logo_qr_width = $QR_width / 5;
    $scale = $logo_width / $logo_qr_width;
    $logo_qr_height = $logo_height / $scale;
    $from_width = ($QR_width - $logo_qr_width) / 2;
    imagecopyresampled ( $QR, $logo, $from_width, $from_width, 0, 0, $logo_qr_width, $logo_qr_height, $logo_width, $logo_height );
}
imagepng ( $QR, 'upload/erweima/ewmlogo.png' );
?>
<center>
<img src="ewmlogo.png"></center><br>


