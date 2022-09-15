<?php

namespace App\Http\Controllers;
use App\Models\User;
use App\Models\Account;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    public function register (Request $request){

       
        $accountNumber  = date('ydis').rand(11, 99);
        try {
            //Validated
            $validateUser = Validator::make($request->all(), 
            [
                'name' => 'required',
                'email' => 'required|email|unique:users,email',
                'password' => 'required'
            ]);
 
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password)
        ]);

        //save user account number
        $account = Account::create([
            'user_id' => $user->id,
            'name' => $user->name,
            'account_number' => $accountNumber,
        ]);
       
        $token = $user->createToken('AuthApp')->accessToken;
 
        return response()->json(['token' => $token], 200);
    } catch (\Throwable $th) {
        return response()->json([
            'status' => false,
            'message' => $th->getMessage()
        ], 500);
    }
    }

    public function login (Request $request){
        $user = [
            'email' => $request->email,
            'password' => $request->password
        ];
 
        if (auth()->attempt($user)) {
            $token = auth()->user()->createToken('AuthApp')->accessToken;
            return response()->json(['token' => $token], 200);
        } else {
            return response()->json(['error' => 'Unauthorised'], 401);
        }
    } 
    
    public function logout(Request $request)
    {
        $request->user()->token()->revoke();
        return response()->json([
            'result' => true,
            'message' => ('Successfully logged out')
        ]);
    }
    
}
