
<?php
include ('phpqrcode.php');
$value = "http://wangbao.ppcom.net";
//echo $value;
$errorCorrectionLevel = 'L';
$matrixPointSize = 10;
QRcode::png ( $value, 'ewm.png', $errorCorrectionLevel, $matrixPointSize, 2 );
$logo = 'emwlogo.gif';//需要显示在二维码中的Logo图像
$QR = 'ewm.png';
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
imagepng ( $QR, 'ewmlogo.png' );
?>
<img src="ewmlogo.png"><br>


