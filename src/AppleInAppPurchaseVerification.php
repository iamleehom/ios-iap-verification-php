<?php
/**
 * Created By: LeeHom
 * File Name: AppleInAppPurchaseVerification.php
 * Created Date: 2018-08-17 10:20
 * Version: 1.0.3
 */

namespace LeeHom;

class AppleInAppPurchaseVerification
{
    //SandBox Verify URL
    const SANDBOX_URL = 'https://sandbox.itunes.apple.com/verifyReceipt';
    //Production Verify URL
    const PRODUCTION_URL = 'https://buy.itunes.apple.com/verifyReceipt';

    //the apple Returned the receipt-data
    private $receiptData;
    //if your IAP is not a subscription,let it empty string(like this:''),else use you own password
    private $password = '';
    //use SandBox for verify or not, true:sandbox false:production
    private $sandbox = true;
    //Verify URL,No need to care
    private $requestUrl;

    /**
     * AppleInAppPurchaseVerification constructor.
     *
     * @param $receiptData
     * @param $password
     * @param $sandbox
     */
    public function __construct($receiptData, $password, $sandbox)
    {
        $this->receiptData = $receiptData;
        $this->password    = $password;
        $this->sandbox     = $sandbox;
        if ($this->sandbox == true) {
            $this->requestUrl = $this::SANDBOX_URL;
        } else {
            $this->requestUrl = $this::PRODUCTION_URL;
        }
    }

    /**
     * encode request param
     *
     * @return string
     */
    private function encodeRequest()
    {
        if ($this->password == '') {
            return json_encode(['receipt-data' => $this->receiptData]);
        } else {
            return json_encode(['receipt-data' => $this->receiptData, 'password' => $this->password]);
        }
    }

    /**
     * decode response
     *
     * @param $response
     * @return mixed
     */
    private function decodeResponse($response)
    {
        return json_decode($response);
    }

    /**
     * initiate validation request
     *
     * @return mixed
     * @throws \Exception
     */
    private function makeRequest()
    {
        $ch = curl_init($this->requestUrl);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $this->encodeRequest());
        $response = curl_exec($ch);
        $errNo    = curl_errno($ch);
        $errMsg   = curl_error($ch);
        curl_close($ch);
        if ($errNo != 0) {
            throw new \Exception($errMsg, $errNo);
        }

        return $response;
    }

    /**
     * verify the apple validation results
     *
     * @return mixed
     * @throws \Exception
     */
    public function validateReceipt()
    {
        $response        = $this->makeRequest();
        $decodedResponse = $this->decodeResponse($response);
        if (!isset($decodedResponse->status) || $decodedResponse->status != 0) {
            $responseResult = 'Invalid receipt. Status code: ' . (!empty($decodedResponse->status) ? $decodedResponse->status : 'N/A');
        } elseif (!is_object($decodedResponse)) {
            $responseResult = 'Invalid response data';
        } else {
            $responseResult = $response;
        }

        return $responseResult;
    }
}
