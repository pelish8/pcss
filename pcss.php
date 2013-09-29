<?php
require 'libs/pelish8/pcss/Pcss.php';
require 'libs/pelish8/pcss/CssParser.php';

$pcss = new pelish8\pcss\Pcss([
    'baseUrl' => __DIR__ . '/css',
    'debug' => true,
    'css.minify' => true
]);
    
$pcss->setHeaders();

$m = new pelish8\pcss\CssParser(
// echo $pcss->getCss()
    $pcss->getCss())
;

echo $m->getMinified();