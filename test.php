<?php
ini_set('memory_limit', '256M');
require_once __DIR__."/vendor/autoload.php";
use algorithm\BinarySearch;


use alicloudIotFramework\Iot;
session_start();
Iot::init("32787548","8a4bedbe834f3423bdaa00aa76f7da02","a123umecKcfV80WT",'a1AhunuAUYJ');
$userList = Iot::getUserList();
foreach ($userList as $user){
    $deviceList = Iot::getUserBindDevice($user['identityId']);
    foreach ($deviceList['data'] as $device){
        $deviceAttribute = Iot::getDeviceAttribute($device['iotId']);
//        $deviceInfo = Iot::getDeviceInfo($device['iotId']);
//        $setDevie = Iot::sendMessageToDevice($device['iotId'],['Status'=>0]);
//        $setServeDevie = Iot::sendServeToDevice($device['iotId'],$device['deviceName'],'Status','{}');
        $setServeDevie = Iot::setDevice($device['iotId'],$device['deviceName'],['Switch'=>1]);
//        $setServeDevie = Iot::setDevice($device['iotId'],$device['deviceName'],['speed'=>150]);
        print_r($setServeDevie);
        exit;
    }
}


