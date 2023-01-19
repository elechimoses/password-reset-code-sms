<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class NewPasswordController extends Controller
{
         public function __invoke(Request $request)
    {
        $request->validate([
            'code' => 'required|string|exists:reset_code_passwords',
            'password' => 'required|string|min:6|confirmed',
        ]);

        // find the code
        $passwordReset = ResetCodePassword::firstWhere('code', $request->code);

        // check if it does not expired: the time is one hour
        if ($passwordReset->created_at > now()->addHour()) {
            $passwordReset->delete();
            return back()->with('message', 'Reset Code expired');
        }

        // find user's phone 
        $user = User::firstWhere('phone', $passwordReset->phone);

        // update user password
       
        $user->forceFill([
                    'password' => Hash::make($request->password),
                ])->save();

        // delete current code 
        $passwordReset->delete();
        
        Auth::login($user);

        return redirect(RouteServiceProvider::HOME);
    }
}
