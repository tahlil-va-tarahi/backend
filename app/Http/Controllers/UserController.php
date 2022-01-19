<?php

namespace App\Http\Controllers;

use App\Http\Resources\PaymentResource;
use App\Http\Resources\ProductResource;
use App\Http\Resources\UserResource;
use App\Models\Payment;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(User::class, 'user');
    }

    /**
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        $users = User::latest('id')->paginate(20);

        return response()->json([
            'users' => UserResource::collection($users)
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param User $user
     * @return JsonResponse
     */
    public function show(User $user): JsonResponse
    {
        return response()->json([
            'user' => new UserResource($user),
            'products' => ProductResource::collection($user->products)
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param User $user
     * @return JsonResponse
     */
    public function update(Request $request, User $user): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'name' => 'max:16|min:3',
            'email' => ['email', Rule::unique('users')->ignore(auth()->id())],
        ]);

        if ($validator->fails()) {
            return response()->json($validator->getMessageBag(), 400);
        }

        $user->update($request->all());
        return response()->json([
            'message' => 'اطلاعات با موفقیت بروزرسانی شد.'
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param User $user
     */
    public function destroy(User $user)
    {
        $user->delete();

        return response()->json([
            'message' => 'اکانت با موفقیت پاک شد.'
        ], 202);
    }

    public function sales()
    {
        if(!auth()->user()->is_admin){
            return response()->json(['error' => 'اجازه دسترسی وجود ندارد.'],403);
        }

        $payments = Payment::latest('id')->paginate(20);
        $payments = PaymentResource::collection($payments);

        return response()->json([
            'payments' => $payments
        ]);
    }

}































