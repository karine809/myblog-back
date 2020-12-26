<?php

namespace App\Http\Controllers;

use App\Models\User;


use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    /**
     * Create a new AuthController instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['login','signup','me','verifiyemail']]);
    }

    /**
     * Get a JWT via given credentials.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function login()
    {

        $credentials = request(['email', 'password']);

        if (! $token = auth()->attempt($credentials)) {
            return response()->json(['error' => 'Unauthorized'], 401);
        } else  if (auth()->user()->user_locked) {
            return response()->json(['error' => 'user locked'], 401);
        } else if (!auth()->user()->email_verified) {
            return response()->json(['error' => 'email not verified'], 401);
        }
        return $this->respondWithToken($token);
    }


    public function signup(Request  $request){
            $rules = [
                'firstname' => 'required|max:20|string',
                'lastname' => 'required|max:20|string',
                'email' => 'required|email:rfc,dns|unique:users',
                'password' => 'required|min:6|string',
                'confirm_password' => 'required|same:password'
            ];

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                $errors = $validator->errors();
                return response($errors, 419);
            } else {

                $email_token = base64_encode($request->email . time());

                User::create([
                    'firstname' => $request->firstname,
                    'lastname' => $request->lastname,
                    'email' => $request->email,
                    'password' => Hash::make($request->password),
                    'email_verification_token' => $email_token

                ]);
                //Send email

                $verificationUrl =' http://localhost:8080/?#/verifiyemail?token=' . $email_token;
                Log::info('Send email to ' . $request->email . ' verifyUrl' . $verificationUrl);

                return response('success', 200);
            }
        }



    /**
     * Get the authenticated User.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function me()
    {
        return response()->json(auth()->user());
    }


    public function verifiyemail(Request  $request){

        if (!empty($request['token'])){
           $find_users = User::where("email_verification_token", $request['token'])->get();

           if (count($find_users)==1)
           {
               $find_users[0]->email_verified = 1;
               $find_users[0]->save();
               return response('success', 200);
           }
           else
           {
               return response('error', 500);
           }
    }
        else{
            return response('error', 500);
        }
//            $verify_token->email_verification_token=$request->email_verification_token
       // $verify_token.email_verified=1;
      //  $verify_token->save();


    }


    /**
     * Log the user out (Invalidate the token).
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout()
    {
        auth()->logout();

        return response()->json(['message' => 'Successfully logged out']);
    }

    /**
     * Refresh a token.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function refresh()
    {
        return $this->respondWithToken(auth()->refresh());
    }

    /**
     * Get the token array structure.
     *
     * @param  string $token
     *
     * @return \Illuminate\Http\JsonResponse
     */
    protected function respondWithToken($token)
    {
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth()->factory()->getTTL() * 60
        ]);
    }
}
