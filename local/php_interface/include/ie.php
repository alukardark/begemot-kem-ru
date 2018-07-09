<?php 

//заглушки ie 6-10
$old_ie_array = [
    '6' => 'MSIE 6.0',
    '7' => 'MSIE 7.0',
    '8' => 'MSIE 8.0',
    '9' => 'MSIE 9.0',
    '10' => 'MSIE 10.0',
];

$sUserAgent = getUA();

foreach ($old_ie_array as $key => $ie) {
    $check = stripos($sUserAgent, $ie) ? $key : false;
    if ($check) {
        header("Location: /_ie/ie{$check}.html");
        exit;
    } 
}