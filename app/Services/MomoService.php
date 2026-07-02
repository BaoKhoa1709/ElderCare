<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use RuntimeException;

class MomoService
{
    public function __construct(
        private string $partnerCode = '',
        private string $accessKey = '',
        private string $secretKey = ''
    ) {}

    public function createSandboxMomoLink(string $bookingId, string $amount): string
    {
        $partnerCode = $this->partnerCode ?: env('MOMO_PARTNER_CODE', '');
        $accessKey = $this->accessKey ?: env('MOMO_ACCESS_KEY', '');
        $secretKey = $this->secretKey ?: env('MOMO_SECRET_KEY', '');

        $orderId = Str::uuid()->toString();
        $requestId = Str::uuid()->toString();
        $orderInfo = 'Thanh toan don hang ' . $bookingId;
        $extraData = '';
        $ipnUrl = '';
        $redirectUrl = '';
        $requestType = 'captureWallet';
        $amountStr = $this->formatAmount($amount);

        $rawSignature = "accessKey={$accessKey}&amount={$amountStr}&extraData={$extraData}&ipnUrl={$ipnUrl}&orderId={$orderId}&orderInfo={$orderInfo}&partnerCode={$partnerCode}&redirectUrl={$redirectUrl}&requestId={$requestId}&requestType={$requestType}";
        $signature = hash_hmac('sha256', $rawSignature, $secretKey);

        $response = Http::asJson()->post('https://test-payment.momo.vn/v2/gateway/api/create', [
            'partnerCode' => $partnerCode,
            'accessKey' => $accessKey,
            'requestId' => $requestId,
            'amount' => $amountStr,
            'orderId' => $orderId,
            'orderInfo' => $orderInfo,
            'redirectUrl' => $redirectUrl,
            'ipnUrl' => $ipnUrl,
            'extraData' => $extraData,
            'requestType' => $requestType,
            'signature' => $signature,
            'lang' => 'vi',
        ]);

        Log::info('MoMo response: ' . $response->body());

        $body = $response->json();

        if (empty($body['payUrl'])) {
            throw new RuntimeException('MoMo payUrl missing: ' . $response->body());
        }

        return $body['payUrl'];
    }

    private function formatAmount(string $amount): string
    {
        return bcadd((string) $amount, '0', 0);
    }
}
