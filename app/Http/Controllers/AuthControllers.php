<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Helpers\Helper;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\RequestStack;
use Illuminate\Support\Facades\Crypt;

class AuthControllers extends Controller
{

    //
    public function __construct()
    {
        $this->middleware('auth')->except('login', 'forgotRequest', 'forgot', 'authenticate');
    }

    public function username()
    {
        return 'username';
    }

    public function login(Request $request)
    {
        $var = ['nav' => 'login', 'subNav' => 'login', 'title' => 'Portal Apps'];

        if (Auth::check()) {
            return redirect('dashboard');
        }
        return view('auth.login', $var);
    }
    // sent FCM
    public function authenticate(Request $request)
    {

        // Validation
        $vMessage = config('global.vMessage'); //get global validation messages
        $validator = Validator::make($request->only('email', 'password'), [
            'email' => 'bail|required|email',
            'password' => 'required',
        ], [
            'email.required' => 'Wajib Memasukkan Email',
            'email.email' => 'Format email salah',
            'password.required' => 'Wajib Memasukkan Kata Sandi'
        ]);
        $valid = Helper::validationFail($validator);
        if (!is_null($valid)) {
            // return response()->json($valid); //return if not valid
            return redirect('login')->with('status', $valid);
        }
        // if ($validator->fails()) {
        //     $messages = $validator->errors()->messages();
        //     $msg = "";
        //     foreach ($messages as $key => $message) {
        //         foreach ($message as $value) {
        //             $msg .= (count($messages) == 1) ? $key . ": " . $value : $key . ": " . $value . "\n";
        //         }
        //     }
        //     $result = (object) [
        //         'http_code' => 200,
        //         'status' => 'error',
        //         'code' => 'E009',
        //         'message' => $msg,
        //     ];
        //     return redirect('login')->with('status', $result);
        // }

        $email = $request->email;
        $password = $request->password;
        $remember = $request->remember;
        $checkAuth = Helper::Auth($email, $password, 'email');

        $result = (object) $checkAuth;
        // dd($result);
        if ($result->status == 'success') {
            if (Auth::attempt(['email' => $email, 'password' => $password], $remember)) {
                return redirect('dashboard')
                    ->with('status', $result)
                    ->with('logging', true);
            }
        }
        return redirect('login')->with('status', $result);
    }

    public function authLoginSwitch(Request $request){
        $data = Crypt::decrypt($request->param);
        // dd(Crypt::decrypt($data));
        if(Auth::loginUsingId($data['userId'])){
            return redirect('sales/create?cartCode='.$data['cartNumber']);
        } else {
            return redirect('/');
        }
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('dashboard'); // mengatasi preventback dan route dashboard terdapat midleware auth
    }

    public function forgot()
    {
        return view('auth.forgot');
    }


    public function forgotRequest(Request $request)
    {
        return view('auth.forgot');
    }


    public function recover($hash)
    {
        return view('auth.recover');
    }


    public function recoverRequest(Request $request)
    {
        return view('auth.forgot');
    }

    public function saveToken(Request $request)
    {
        // auth()->user()->update(['device_token'=>$request->token]);
        try {
            DB::table('users')
            ->where('id',Auth::id())
            ->update([
                'device_key' => $request->tokenFCM
            ]);
        } catch (\Throwable $th) {
            //throw $th;
            return response()->json([$th->getMessage()]);
        }
        return response()->json(['token saved successfully.']);
    }

    public function sendNotif(Request $request)
    {
     //firebaseToken berisi seluruh user yang memiliki device_token. jadi notifnya akan dikirmkan ke semua user
            //jika kalian ingin mengirim notif ke user tertentu batasi query dibawah ini, bisa berdasarkan id atau kondisi tertentu
            
            $firebaseToken = User::whereNotNull('device_key')->pluck('device_key')->all();
            // dd($firebaseToken);
            $SERVER_API_KEY = env('FIREBASE_SERVER_KEY');
    
            $data = [
                "registration_ids" => $firebaseToken,
                "notification" => [
                    "title" => '$request->title',
                    "body" => '$request->body',
                    "icon" => 'https://cdn.pixabay.com/photo/2016/05/24/16/48/mountains-1412683_960_720.png',
                    "content_available" => true,
                    "priority" => "high",
                    "click_action" => "http://127.0.0.1:8000/sales/edit/SO220330-0003",
                ]
            ];
            $dataString = json_encode($data);
    
            $headers = [
                'Authorization: key=' . $SERVER_API_KEY,
                'Content-Type: application/json',
            ];
    
            $ch = curl_init();
    
            curl_setopt($ch, CURLOPT_URL, 'https://fcm.googleapis.com/fcm/send');
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $dataString);
    
            $response = curl_exec($ch);
            
            // dd($response);   
            // return redirect('/ping');
    }
}
