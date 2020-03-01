<?php

namespace App\Http\Middleware;

use Closure;
//use Auth;
use Twilio;

use Mail;
use Illuminate\Support\Facades\Auth; 
use Validator;
use App\User; 



class TwoFactorVerify
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
       

        
        $user = Auth::user();
        dd($user->name);
        if($user->token_2fa_expiry > \Carbon\Carbon::now()){
            // return response()->json(['error' => 'code not sent'], 401);
            // die();
            return $next($request);
        } 
        
        if ($user->token_2fa == null) {
            //User::create(['token_2fa' => mt_rand(10000,99999)]);
            $user->push('token_2fa', mt_rand(10000,99999));
        }
        else {
            $user->token_2fa = mt_rand(10000,99999);
            $user->save();   
        }

             

        // This is the twilio way
        //Twilio::message($user->phone_number, 'Two Factor Code: ' . $user->token_2fa);

        // If you want to use email instead just 
        // send an email to the user here ..


        $to_name = $user->name;
        $to_email = $user->email;
        $data = array('name'=>'Ogbonna', 'body' => $user->token_2fa);
        // Mail::send('emails.mail', $data, function($message) use ($to_name, $to_email) {
        //     $message->to($to_email, $to_name)->subject('Laravel Test Mail');
        //     $message->from('SENDER_EMAIL_ADDRESS','Test Mail');
        // });
        Mail::send('emails.email', $data, function($message) {

            $message->to($to_email, $to_name)

                    ->subject('Code security');
        });
         
        // return response()->json(['success' => 'code sent check your phone'], 200); 
        //return response()->json(['success' => 'code sent check your phone'], 200);
        return redirect('/2fa');
    
    }
}
