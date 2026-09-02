# ios-iap-verification-php

This PHP Class helps you verify Apple IAP receipt-data on your PHP backend.

## v2.0.0 - Important Upgrade Notes

This release (v2.0.0) contains breaking changes requiring a minimum of PHP 8.0 and updates the HTTP client dependency to Guzzle 7.

Highlights:
- PHP 8.0+ required (strict types and typed properties used)
- Guzzle 7 is the supported HTTP client
- validateReceipt() now returns the decoded Apple response as an associative array
- If a production verification receives status 21007 (sandbox receipt sent to production), the library will automatically retry against the sandbox endpoint
- Network and JSON errors now throw RuntimeException — callers should catch exceptions

## Installation

It's recommended that you use [Composer](https://getcomposer.org/) to install this library.

```bash
$ composer require leehom1988/ios-iap-verification-php
```

This will install this library and all required dependencies. This library requires PHP 8.0.0 or newer.

## Usage (PHP 8+)

```php
<?php
require_once 'vendor/autoload.php';

use LeeHom\AppleInAppPurchaseVerification;

$receiptData = 'MIXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX'; // base64 receipt-data from device
$sharedSecret = 'baXXXXXXXXXXXXXXXXXXXXXXXXX'; // optional for subscriptions

try {
    // third parameter: true = sandbox, false = production, null = auto (default)
    $verifier = new AppleInAppPurchaseVerification($receiptData, $sharedSecret /*, null */);
    $result = $verifier->validateReceipt(); // returns associative array

    if (isset($result['status']) && (int)$result['status'] === 0) {
        // verification success — inspect $result['receipt'] or $result['latest_receipt_info']
        echo "Verified\n";
    } else {
        // handle other status codes
        echo 'Apple status: ' . ($result['status'] ?? 'N/A') . "\n";
    }
} catch (\RuntimeException $e) {
    // network or parse errors
    echo 'Verification failed: ' . $e->getMessage() . "\n";
}
```

## Status codes

Refer to Apple's documentation for status codes. Common ones:

- 0 — The receipt is valid.
- 21000 — The App Store could not read the JSON object you provided.
- 21002 — The data in the receipt-data property was malformed or missing.
- 21003 — The receipt could not be authenticated.
- 21004 — The shared secret you provided does not match the shared secret on file for your account.
- 21005 — The receipt server is not currently available.
- 21006 — This receipt is valid but the subscription has expired.
- 21007 — This receipt is from the test environment, but it was sent to the production environment for verification.
- 21008 — This receipt is from the production environment, but it was sent to the test environment for verification.

For more details, see Apple's documentation: https://developer.apple.com/documentation/appstorereceipts/verifyreceipt

## Author

- Name: [LeeHom](https://diandian.iamleehom.com/)
- Email: lh411937409@gmail.com
