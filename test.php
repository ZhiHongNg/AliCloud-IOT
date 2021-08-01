<?php
ini_set('memory_limit', '256M');
require_once __DIR__."/vendor/autoload.php";
use algorithm\BinarySearch;


use alicloudIotFramework\Iot;
Iot::init("32787548","8a4bedbe834f3423bdaa00aa76f7da02","a123umecKcfV80WT");
$token = Iot::request('/cloud/token','1.0.1',['grantType'=>"project","res"=>"a123umecKcfV80WT"]);
print_r($token);
exit;

