<?php

date_default_timezone_set('Europe/Moscow');

$privateKey = file_get_contents(__DIR__.'/rs256/jwtRS256.key');

$publicKey = file_get_contents(__DIR__.'/rs256/jwtRS256.key.pub');

$key = 'secret';
$iss = '';
$aud = '';
$iat = time();
$exp = $iat + (2 * 60 * 60);