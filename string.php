<?php

require 'libs/pelish8/pcss/CssMinifier.php';
$start = microtime(true);

$m = new pelish8\pcss\CssMinifier('/*

asda
sd
as
d
a


*/


.color /*asdasd*/ {
    color: white;
    border: solid 1px red;
}
.asda .asdasd, div {
    asdasd: #asdas;  asda: adsas;
}
#sale #sale div,                    #asdw{
    asd: asd;
    /*sale
    
    
    
    
    AAAAAA
    
    
    */
}
h1 {
  animation-duration: 3s;
  animation-name: slidein;
}

@keyframes slidein {
  from {
    margin-left: 100%;
    width: 300%
  }

  to {
    margin-left: 0%;
    width: 100%;
  }
}

');
echo '<pre>';
echo $m->getMinified();
echo "<br>" . (microtime(true) - $start) . ' ms';