<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Validation\Rules;
use App\Models\User;
Use Auth;

class RegisterController extends Controller
{
    public function create()
    {
        return view('auth.register');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => ['required'],
            'password' => ['required','confirmed', Rules\Password::defaults() ],
            'phone' => ['required', 'numeric'],
        ]);

        $user = User::create([
            'name' => $request->name,
            'password' => $request->password,
            'phone' => $request->phone,
        ]);

        Auth::login($user);

        return redirect('dashboard');
    }
}
