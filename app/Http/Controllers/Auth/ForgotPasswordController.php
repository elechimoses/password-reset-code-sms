<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ForgotPasswordController extends Controller
{
        public function __invoke(Request $request)
    {
        $data = $request->validate([
            'phone' => 'required|numeric|exists:users',
        ]);
        

        // Delete all old code that user send before.
        ResetCodePassword::where('phone', $request->phone)->delete();

        // Generate random code
        $data['code'] = mt_rand(100000, 999999);

        // Create a new code
        $codeData = ResetCodePassword::create($data);
        $message = 'Password Reset Code:' .$codeData->code. '.It will expire in 30 minutes';
        // Send sms to user
           try {
  
            $account_sid = getenv("TWILIO_SID");
            $auth_token = getenv("TWILIO_AUTH_TOKEN");
            $twilio_number = getenv("TWILIO_NUMBER");
            $number = "+234.$request->phone";
            $client = new Client($account_sid, $auth_token);
            $client->messages->create($number, [
                'from' => $twilio_number, 
                'body' => $message]);
  
            Session::flash('message', "Reset code sent.");
            return redirect()->route('password.reset');
   
        } catch (Exception $e) {
            dd("Error: ". $e->getMessage());
        }
        
    }
}
