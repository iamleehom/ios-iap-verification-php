<?php
declare(strict_types=1);

/**
 * Apple IAP verification helper
 * Upgraded for v2.0.0: PHP 8.0+ compatibility, Guzzle 7 support,
 * better error handling and sandbox retry per Apple's spec (status 21007).
 *
 * Created By: LeeHom
 * Updated Date: 2026-09-02
 */

namespace LeeHom;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;

class AppleInAppPurchaseVerification
{
    // App Version
    public const APP_VERSION = '2.0.0';

    // SandBox Verify URL
    public const SANDBOX_URL = 'https://sandbox.itunes.apple.com/verifyReceipt';

    // Production Verify URL
    public const PRODUCTION_URL = 'https://buy.itunes.apple.com/verifyReceipt';

    // the apple Returned the receipt-data
    private string $receiptData;

    // if your IAP is not a subscription, let it empty string, else use your shared secret
    private string $password = '';

    // optional: force sandbox (true) or production (false). null = follow retry logic (default)
    private ?bool $sandbox;

    // Verify URL
    private string $requestUrl;

    /**
     * Constructor
     *
     * @param string $receiptData base64 encoded receipt-data from device
     * @param string $password    shared secret for auto-renewable subscriptions (optional)
     * @param bool|null $sandbox  true = sandbox, false = production, null = auto (production then sandbox if needed)
     */
    public function __construct(string $receiptData, string $password = '', ?bool $sandbox = null)
    {
        $this->receiptData = $receiptData;
        $this->password = $password;
        $this->sandbox = $sandbox;
        $this->requestUrl = $this->resolveInitialUrl();
    }

    private function resolveInitialUrl(): string
    {
        if ($this->sandbox === true) {
            return self::SANDBOX_URL;
        }

        // default to production when sandbox is false or null (we may retry sandbox later on 21007)
        return self::PRODUCTION_URL;
    }

    /**
     * Prepare request payload
     */
    private function encodeRequest(): array
    {
        $payload = ['receipt-data' => $this->receiptData];

        if ($this->password !== '') {
            $payload['password'] = $this->password;
        }

        // recommended to include exclude-old-transactions for some server flows; leave optional for now
        return $payload;
    }

    /**
     * Low level HTTP request to Apple's verifyReceipt endpoint
     *
     * @throws GuzzleException
     */
    private function makeRequest(string $url): string
    {
        $client = new Client(['timeout' => 10.0]);

        $response = $client->request('POST', $url, [
            'json' => $this->encodeRequest(),
            'headers' => [
                'User-Agent' => 'ios-iap-verification-php/' . self::APP_VERSION,
                'Accept' => 'application/json',
            ],
        ]);

        return (string)$response->getBody();
    }

    /**
     * Validate receipt against Apple's servers.
     *
     * Behavior changes in v2.0.0:
     * - Requires PHP 8.0+
     * - Returns decoded response as associative array on success
     * - If production is used and Apple returns status 21007 (sandbox receipt sent to production),
     *   this automatically retries against the sandbox endpoint.
     * - Throws RuntimeException on network / JSON errors for clearer error handling.
     *
     * @return array Decoded Apple response (associative)
     * @throws \RuntimeException on error
     */
    public function validateReceipt(): array
    {
        try {
            $body = $this->makeRequest($this->requestUrl);
        } catch (GuzzleException $e) {
            throw new \RuntimeException('HTTP request to Apple failed: ' . $e->getMessage(), 0, $e);
        }

        $decoded = json_decode($body, true);
        if (!is_array($decoded)) {
            throw new \RuntimeException('Invalid JSON response from Apple');
        }

        // If Apple returns 21007 (this receipt is from the test environment but sent to production), retry sandbox
        if (isset($decoded['status']) && (int)$decoded['status'] === 21007 && $this->requestUrl === self::PRODUCTION_URL) {
            try {
                $body = $this->makeRequest(self::SANDBOX_URL);
            } catch (GuzzleException $e) {
                throw new \RuntimeException('HTTP request to Apple (sandbox retry) failed: ' . $e->getMessage(), 0, $e);
            }

            $decodedRetry = json_decode($body, true);
            if (!is_array($decodedRetry)) {
                throw new \RuntimeException('Invalid JSON response from Apple (sandbox retry)');
            }

            return $decodedRetry;
        }

        return $decoded;
    }
}
