<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request; 
use App\Http\Controllers\Controller; 
use App\User; 
use Illuminate\Support\Facades\Auth; 
use Validator;
use Mail;

class UserController extends Controller 
{
    public $successStatus = 200;/** 
     * login api 
     * 
     * @return \Illuminate\Http\Response 
     */ 
    public function login(){ 
        $tok = 0;
        $getcode = '';
        if(Auth::attempt(['email' => request('email'), 'password' => request('password')])){ 
            $user = Auth::user(); 
            $id = $user->_id;
            // $this->twofactor($id);
            if ($user->token_2fa_expiry == null) {
                //User::create(['token_2fa_expiry' => \Carbon\Carbon::now()]);
                $user->push('token_2fa_expiry', \Carbon\Carbon::now());
            }
            else {
                $user->token_2fa_expiry = \Carbon\Carbon::now();
                $user->save();
            }
            $tok = mt_rand(10000,99999);
            //echo $tok;
            if ($user->token_2fa == null) {
                //User::create(['token_2fa' => mt_rand(10000,99999)]);
                $user->push('token_2fa', (string)$tok);
            }
            else {
                
                // $user->token_2fa = $tok;
                // $user->save();   
                $user->update(['token_2fa' => (string)$tok]);
            }
            //echo $tok;
            // foreach ($tok as $bo) {
            //     $getcode .= $bo;
            // }
        //     $to_name = $user->name;
        // $to_email = $user->email;
        $data = array('name'=>$user->name, 'body' => (string)$tok, 'email' => $user->email);
        
        Mail::send([], $data, function($message) use ($data) {

            $message->to($data['email'], $data['name'])

                    ->subject('Code security')
                    ->setBody($data['body']);
        });
            // $success['token'] =  $user->createToken('MyApp')-> accessToken; 
             //return response()->json(['success' => 'porco'], $this-> successStatus); 
        //     $client = new \GuzzleHttp\Client();
        // $request = $client->get('http://localhost:8000/api/2fa');
        // $response = $request->getBody();
        // return $response;
        return response()->json(['success' => 'tok save'], 200); 
    //     $client = new \GuzzleHttp\Client();
    // //$body['name'] = "Testing";
    // $url = "http://localhost:8000/api/2fa";
    // $response = $client->request("POST", $url, ['form_params' => $user]);
    // $response = $client->send($response);
    // return $response;
        //return redirect('/api/2fa'); 
        } 
        else{ 
            return response()->json(['error'=>'Unauthorised'], 401); 
        } 
    }

    public function twofactor() {
        $user = User::find('5e5b7ff02b8adb1ff5791382');
        //$proov = request('2fa');
        //echo gettype($proov);
        if ($user->token_2fa == null) {
            return response()->json(['error' => 'something went wrong'], 401);
        }
        if(request('2fa') == $user->token_2fa){             
            
            // $user->token_2fa_expiry = \Carbon\Carbon::now()->addMinutes(config('session.lifetime'));
            // $user->save();
            $user->update(['token_2fa_expiry' => \Carbon\Carbon::now()->addMinutes(config('session.lifetime'))]);       
            $success['token'] =  $user->createToken('MyApp')-> accessToken; 
            $user->unset('token_2fa');
            return response()->json(['success' => $success], 200);
        } else {
            return response()->json(['error' => 'something went wrong'], 401);
        }
    }
/** 
     * Register api 
     * 
     * @return \Illuminate\Http\Response 
     */ 
    public function register(Request $request) 
    { 
        $validator = Validator::make($request->all(), [ 
            'name' => 'required', 
            'email' => 'required|email', 
            'password' => 'required', 
            'c_password' => 'required|same:password', 
        ]);
if ($validator->fails()) { 
            return response()->json(['error'=>$validator->errors()], 401);            
        }
$input = $request->all(); 
        $input['password'] = bcrypt($input['password']); 
        $user = User::create($input); 
        $success['token'] =  $user->createToken('MyApp')-> accessToken; 
        $success['name'] =  $user->name;
return response()->json(['success'=>$success], $this-> successStatus); 
    }

    public function logout() 
    {   
        if (Auth::check()) {
            Auth::user()->token()->revoke();
            return response()->json(['success' =>'logout_success'],200); 
        }
        else {
            return response()->json(['error' =>'something went wrong'], 500);
        }
    }

/** 
     * details api 
     * 
     * @return \Illuminate\Http\Response 
     */ 
    public function details() 
    { 
        $user = Auth::user(); 
        return response()->json(['success' => $user], $this-> successStatus); 
    } 

    public function getProov() {
        $client = new \GuzzleHttp\Client();
        $request = $client->get('http://159.65.168.219/ghost.php?function=viewerrorlog');
        $response = $request->getBody();
        return $response;
    }
}