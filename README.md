# Apple In App Purchase Verification With PHP
[![Latest Stable Version](https://poser.pugx.org/leehom1988/ios-iap-verification-php/version)](https://packagist.org/packages/leehom1988/ios-iap-verification-php)
[![Total Downloads](https://poser.pugx.org/leehom1988/ios-iap-verification-php/downloads)](https://packagist.org/packages/leehom1988/ios-iap-verification-php)
[![Latest Unstable Version](https://poser.pugx.org/leehom1988/ios-iap-verification-php/v/unstable)](//packagist.org/packages/leehom1988/ios-iap-verification-php)
[![License](https://poser.pugx.org/leehom1988/ios-iap-verification-php/license)](https://packagist.org/packages/leehom1988/ios-iap-verification-php) [![Join the chat at https://gitter.im/ios-iap-verification/Lobby](https://badges.gitter.im/ios-iap-verification/Lobby.svg)](https://gitter.im/ios-iap-verification/Lobby?utm_source=badge&utm_medium=badge&utm_campaign=pr-badge&utm_content=badge)

This PHP Class is help you to verification your Apple IAP receipt-data if your server is developed by PHP.

## Installation

It's recommended that you use [Composer](https://getcomposer.org/) to install this library.

```bash
$ composer require leehom1988/ios-iap-verification-php
```

This will install this library and all required dependencies. This library requires PHP 5.6.0 or newer.

## Usage(English)

Create an Demo.php file with the following contents:

```php
<?php
require_once 'vendor/autoload.php';

use LeeHom\AppleInAppPurchaseVerification;

// The receipt-data from apple
$receiptData = 'MIXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX';
// The password your own, if your IAP is not a subscription, let it empty string(like this:''), else use your own password
$password = 'baXXXXXXXXXXXXXXXXXXXXXXXXX';

$appleIAP = new AppleInAppPurchaseVerification($receiptData, $password, true);
$result = $appleIAP->validateReceipt();
echo $result;
```

## Usage(中文)

创建一个名为Demo.php的文件，将以下内容拷贝至Demo.php文件中:

```php
<?php
require_once 'vendor/autoload.php';

use LeeHom\AppleInAppPurchaseVerification;

// apple返回的支付参数 receipt-data
$receiptData = 'MIXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX';
// 如果产品类型为订阅型的，请使用校验需要的密码；如果不是订阅型的产品，请将其置为空字符串
$password = 'baXXXXXXXXXXXXXXXXXXXXXXXXX';

$appleIAP = new AppleInAppPurchaseVerification($receiptData, $password, true);
$result = $appleIAP->validateReceipt();
echo $result;
```

## View results:
- Use PHP CLI, in the terminal windows, type the following contents:
```bash
$ php Demo.php start
```
- Directly access to your Demo.php file in your browser.

## Status codes:
- 21000
The App Store could not read the JSON object you provided.
- 21002
The data in the receipt-data property was malformed or missing.
- 21003
The receipt could not be authenticated.
- 21004
The shared secret you provided does not match the shared secret on file for your account.
Only returned for iOS 6 style transaction receipts for auto-renewable subscriptions.
- 21005
The receipt server is not currently available.
- 21006
This receipt is valid but the subscription has expired. When this status code is returned to your server, the receipt data is also decoded and returned as part of the response.
Only returned for iOS 6 style transaction receipts for auto-renewable subscriptions.
- 21007
This receipt is from the test environment, but it was sent to the production environment for verification. Send it to the test environment instead.
- 21008
This receipt is from the production environment, but it was sent to the test environment for verification. Send it to the production environment instead.

For Status codes more information, see [Apple Receipt Validation Programming Guide](https://developer.apple.com/library/content/releasenotes/General/ValidateAppStoreReceipt/Chapters/ValidateRemotely.html#//apple_ref/doc/uid/TP40010573-CH104-SW1).

## Author:
If you have any question,Please be easy to contact me:
- Name: [LeeHom](https://diandian.iamleehom.com/)
- Email: lh411937409@gmail.com
- [Website](https://diandian.iamleehom.com/)

Hope it can help You, Just Enjoy It! 😁😁😁😁
- (If you like it, Star it!️⭐⭐️)
- (喜欢就给个星星吧!⭐️⭐)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
