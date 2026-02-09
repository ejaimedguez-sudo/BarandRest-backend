<?php
$k='ssh-ed25519 AAAAC3NzaC1lZDI1NTE5AAAAIAD/rbpq7G60k+qp5hmVteW5f+Zj7pRV3A/eVgQmgDtZ barandrest@COACH-LALO74-1770588319';
list($t,$b,$c)=explode(' ',$k,3);
$raw=base64_decode($b);
$fp=base64_encode(hash('sha256',$raw,true));
$fp=rtrim($fp,'=');
echo 'SHA256:'.$fp.PHP_EOL;
