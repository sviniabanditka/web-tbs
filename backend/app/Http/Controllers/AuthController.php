<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\LoginCode;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Mail\SendLoginCode; // Mailable класс

class AuthController extends Controller
{
    public function sendLoginCode(Request $request)
    {
        $request->validate(['email' => 'required|email']);

        $user = User::firstOrCreate([
            'email' => $request->email
        ], [
            'username' => User::generateUsername($request->email)
        ]);
        $code = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);

        LoginCode::create([
            'user_id' => $user->id,
            'code' => $code,
            'expires_at' => now()->addMinutes(15)
        ]);
        Mail::to($user->email)->send(new SendLoginCode($code));

        return response()->json(['message' => 'Code was sent to email address.']);
    }

    public function loginWithCode(Request $request)
    {
        $request->validate([
            'email' => 'required|email|max:255',
            'code' => 'required|digits:6'
        ]);

        $user = User::where('email', $request->email)->firstOrFail();
        $loginCode = $user->loginCodes()
            ->where('code', $request->code)
            ->valid()
            ->first();

        if (!$loginCode) {
            return response()->json(['error' => 'Wrong code!'], 401);
        }

        $loginCode->update(['used_at' => now()]);

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'access_token' => $token,
            'token_type' => 'Bearer'
        ]);
    }

    public function getUser(Request $request)
    {
        return $request->user();
    }
}
