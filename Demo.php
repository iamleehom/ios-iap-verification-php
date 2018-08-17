<?php
/**
 * Created By: LeeHom
 * File Name: Demo.php
 * Created Date: 2018-08-17 10:55
 */
require_once 'src/AppleInAppPurchaseVerification.php';

use LeeHom\AppleInAppPurchaseVerification;

//the receipt-data from apple
$receiptData = 'MIXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX';
//the password you own,if your IAP is not a subscription,let it empty string(like this:''),else use you own password
$password = 'baXXXXXXXXXXXXXXXXXXXXXXXXX';

$appleIAP = new AppleInAppPurchaseVerification($receiptData, $password, true);
try {
    $result = $appleIAP->validateReceipt();
    echo $result;
} catch (Exception $e) {
    echo $e->getMessage();
}
