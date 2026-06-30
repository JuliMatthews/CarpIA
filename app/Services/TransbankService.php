<?php

namespace App\Services;

use Transbank\Webpay\WebpayPlus\Transaction;
use Transbank\Webpay\Options;

class TransbankService
{
    private Transaction $transaction;

    public function __construct()
    {
        $env = config('transbank.environment', 'integration');
        $environment = $env === 'production'
            ? Options::ENVIRONMENT_PRODUCTION
            : Options::ENVIRONMENT_INTEGRATION;

        $option = new Options(
            config('transbank.webpay.api_key'),
            config('transbank.webpay.commerce_code'),
            $environment
        );

        $this->transaction = new Transaction($option);
    }

    public function createTransaction(string $buyOrder, string $sessionId, int $amount, string $returnUrl): array
    {
        $response = $this->transaction->create($buyOrder, $sessionId, $amount, $returnUrl);

        return [
            'token' => $response->getToken(),
            'url' => $response->getUrl(),
        ];
    }

    public function commitTransaction(string $token): array
    {
        $response = $this->transaction->commit($token);

        return [
            'status' => $response->getStatus(),
            'authorization_code' => $response->getAuthorizationCode(),
            'amount' => $response->getAmount(),
            'buy_order' => $response->getBuyOrder(),
            'card_detail' => $response->getCardDetail(),
            'response_code' => $response->getResponseCode(),
            'transaction_date' => $response->getTransactionDate(),
        ];
    }
}
