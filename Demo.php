<?php
/**
 * Created By: LeeHom
 * File Name: Demo.php
 * Created Date: 2017-03-10 14:55
 */
require_once 'src/AppleIAP/AppleInAppPurchaseVerification.php';
use AppleIAP\AppleInAppPurchaseVerification;

//the receipt-data from apple
$receiptData = 'MIXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX';
//the password you own,if your IAP is not a subscription,let it empty string(like this:''),else use you own password
$password = 'baXXXXXXXXXXXXXXXXXXXXXXXXX';

$appleIAP = new AppleInAppPurchaseVerification($receiptData, $password, true);
$result = $appleIAP->validateReceipt();
echo $result;