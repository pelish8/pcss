<?php
require 'libs/pelish8/pcss/Pcss.php';

$pcss = new pelish8\pcss\Pcss([
    'baseUrl' => __DIR__ . '/css',
    'debug' => true,
    'css.minify' => true
]);
    
$pcss->setHeaders();

echo $pcss->getCss();