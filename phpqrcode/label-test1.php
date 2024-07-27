<?php
require_once './phpqrcode/qrlib.php';
// Displays QR Code image to the browser
QRcode::png('PHP QR Code :)');
require_once __DIR__ . '/phpqrcode/qrlib.php';
// outputs image as PNG that can be refered to a HTML image 'src'
QRcode::png('PHP QR Code :)');
?>
show-qr-code-in-HTML-img/view.php
<img src="generate.php" />