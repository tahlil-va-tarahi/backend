<?php

namespace App\Services\Payment;

class PaymentService
{
    public function pay(array $data)
    {
        $user = auth()->user();
        $apiKey = config('services.id_pay_api_key');

        $params = [
            'order_id' => $data['order_id'],
            'amount' => $data['amount'],
            'name' => $user->name,
            'mail' => $user->email,
            'callback' => route('payment.callback'),
        ];

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, 'https://api.idpay.ir/v1.1/payment');
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($params));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            "X-API-KEY: $apiKey",
            'X-SANDBOX: 1'
        ]);

        $result = curl_exec($ch);
        curl_close($ch);
        $result = json_decode($result, true);

        if (isset($result['error_code'])) {
            throw new \Exception($result['error_message']);
        }

//        return $result;
        return redirect()->away($result['link']);

    }

    public function verify()
    {
        $params = [
            'id' => 'd2e353189823079e1e4181772cff5292',
            'order_id' => '101',
        ];

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, 'https://api.idpay.ir/v1.1/payment/inquiry');
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($params));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/json',
            'X-API-KEY: 6a7f99eb-7c20-4412-a972-6dfb7cd253a4',
            'X-SANDBOX: 1',
        ));

        $result = curl_exec($ch);
        $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        var_dump($httpcode);
        var_dump($result);
    }
}
