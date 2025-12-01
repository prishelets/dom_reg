<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Account;
use Illuminate\Support\Facades\Validator;

class AccountController extends Controller
{
    public function add_account(Request $request)
    {
        // Валидация
        $validator = Validator::make($request->all(), [
            // обязательные поля
            'email'    => 'required|string|max:255',
            'login'    => 'required|string|max:255',
            'password' => 'required|string|max:255',
            'email_login'    => 'required|string|max:255',
            'email_password' => 'required|string|max:255',
            
            // остальные — опциональные

            'proxy'          => 'nullable|string|max:255',
            'first_name'     => 'nullable|string|max:255',
            'last_name'      => 'nullable|string|max:255',
            'city'           => 'nullable|string|max:255',
            'address'        => 'nullable|string|max:255',
            'zip'            => 'nullable|string|max:50',
            'phone'          => 'nullable|string|max:100',
            'security_qa'    => 'nullable|string',
        ]);

        // ❌ Ошибка валидации
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors'  => $validator->errors(),
                'server_time' => now()->toDateTimeString(),
            ], 422);
        }

        $validated = $validator->validated();

        // Создание записи
        $account = Account::create(array_merge(
            $validated,
            [ 'status' => 'created' ]
        ));

        // ✔ Успех
        return response()->json([
            'success' => true,
            'id'      => $account->id,
            'message' => 'Account created successfully',
            'server_time' => now()->toDateTimeString(),
        ]);
    }
}
