<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Models\Product;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    public function pay(Request $request)
    {
        $product = Product::findOrFail($request->product_id);

        $api = 'test';
        $amount = $product->price;
        $redirect = 'http://localhost/tahlil-va-tarahi/public/payment/verify';
        $result = $this->send($api, $amount, $redirect);
        $result = json_decode($result);

        if (isset($result->status) && !$result->status) {
            return response()->json(['error' => $result->errorMessage], 500);
        }

        $payment = new Payment();
        $payment->token = $result->token;
        $payment->product_id = $request->product_id;
        $payment->user_id = auth()->id();
        $payment->save();

        return response()->json(['link' => "https://pay.ir/pg/$result->token"]);
    }

    public function verify(Request $request)
    {
        $api = 'test';
        $token = $request->token;
        $result = json_decode($this->myVerify($api, $token));

        return response()->json($result);

    }

    public function send($api, $amount, $redirect)
    {
        return $this->curl_post('https://pay.ir/pg/send', [
            'api' => $api,
            'amount' => $amount,
            'redirect' => $redirect,
        ]);
    }

    public function myVerify($api, $token)
    {
        return $this->curl_post('https://pay.ir/pg/verify', [
            'api' => $api,
            'token' => $token,
        ]);
    }

    public function curl_post($url, $params)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($params));
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
        ]);
        $res = curl_exec($ch);
        curl_close($ch);

        return $res;
    }
}
