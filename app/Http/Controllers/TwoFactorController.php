<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\User; 
use Illuminate\Support\Facades\Auth; 
use Validator;


class TwoFactorController extends Controller
{
    // public function __construct()
    // {
    //     $this->middleware('two_factor');
    // }

    public function verifyTwoFactor()
    {
        // $request->validate([
        //     '2fa' => 'required',
        // ]);

        if(Auth::attempt(['2fa' => request('2fa')])){             
            $user = Auth::user();
            $user->token_2fa_expiry = \Carbon\Carbon::now()->addMinutes(config('session.lifetime'));
            $user->save();       
            $success['token'] =  $user->createToken('MyApp')-> accessToken; 
            return response()->json(['success' => $success], 200);
        } else {
            return response()->json(['error' => 'something went wrong'], 401);
        }
    }
}
