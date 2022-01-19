<?php

use App\Models\Payment;
use App\Models\Product;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Route;

Route::get('/payment/verify', function (Request $request) {
    $response = Http::withToken('token')->post(
        'http://localhost:8000/api/payment/verify',
        [
            'token' => $request->token,
        ]
    );

    $response = $response->json();

    $payment = Payment::where('token', $request->token)->firstOrFail();

    $payment->update([
        'status' => 1,
        'trans_id' => $response['transId'],
    ]);

    $user = User::FindOrFail($payment->user_id);
    $product = Product::FindOrFail($payment->product_id);

    try {
        $product->users()->save($user);
    } catch (\Throwable $th) {
        die(
        '<h2 align="center" style="margin-top=30px">.فایل قبلا خریداری شده است</h2>'
        );
    }

    if (isset($response['status'])) {
        if ($response['status'] == 1) {
            echo "<h1 align='center'>تراکنش با موفقیت انجام شد</h1>";
            echo "<h2 align='center'>کد پیگیری : ";
            echo $response['transId'];
            echo '</h2>';
        } else {
            echo "<h1 align='center'>تراکنش با خطا مواجه شد</h1>";
        }
    } else {
        if ($response['status'] == 0) {
            echo "<h1 align='center'>تراکنش با خطا مواجه شد</h1>";
        }
    }
})->name('callback');
