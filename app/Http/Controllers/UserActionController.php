<?php

namespace App\Http\Controllers;
use App\Models\Account;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

use Illuminate\Http\Request;

class UserActionController extends Controller
{
    public function getAccount(){

    }
    public function createAccount(Request $request){

        try{

            $this->validate($request,[
                'userId' => 'required',
                'name' => 'required',
            ]);
             //save user account number
             $accountNumber  = date('ydis').rand(11, 99);
            $account = Account::create([
            'user_id' => $request->userId,
            'name' => $request->name,
            'account_number' => $accountNumber,
            ]);
            return response()->json([
                'result' => true,
                'message' => ('Account Created successful')
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => $th->getMessage()
            ], 500);
        }
    }

    public function transfer(Request $request){
        try {
            //Validated
        $this->validate($request,[
            'userId' => 'required',
            'acctNumber' => 'required',
            'amount' => 'required',
        ]);
        $user = User::find($request->userId);
        $reciever = Account::where('account_number', $request->acctNumber)->first();
        $b = User::where('id', $reciever->user_id)->first();
        $account = Account::where('user_id', $request->userId)->first();
        
        if ($account->account_number !== $request->acctNumber){
            $count = Account::where('account_number',$request->acctNumber)->get();
            
            if (count($count)>0){
                if ($user->balance >= $request->amount){

                    $user->balance -= $request->amount;
                    $user->save(); 

                    $b->balance += $request->amount;
                    $b->save();


                    return response()->json([
                        'result' => true,
                        'message' => ('Transaction was successful')
                    ]);
                } else {
                    return response()->json([
                        'result' => false,
                        'message' => ('Insuficient Balance')
                    ]);
                }

            } else{
                return response()->json([
                    'result' => false,
                    'message' => ('Invalid Account Number')
                ]);
            }
        }else{
            return response()->json([
                'result' => false,
                'message' => ('Sorry Something went wrong')
            ]);
        }
    } catch (\Throwable $th) {
        return response()->json([
            'status' => false,
            'message' => $th->getMessage()
        ], 500);
    }
    }

    public function fund (Request $request){
        try {
        $this->validate($request,[
            'userId' => 'required',
            'amount' => 'required',
        ]);

        $user = User::find($request->userId);

        $user->balance += $request->amount;
        $user->save();

        return response()->json([
            'result' => true,
            'message' => ('Account has been funded')
        ]);
    } catch (\Throwable $th) {
        return response()->json([
            'status' => false,
            'message' => $th->getMessage()
        ], 500);
    }
    }
    
    public function withdraw (Request $request){
        try{

        $this->validate($request,[
            'userId' => 'required',
            'amount' => 'required',
        ]);

        $user = User::find($request->userId);

        if ($user->balance >= $request->amount){

            $user->balance -= $request->amount;
            $user->save(); 

            return response()->json([
                'result' => true,
                'message' => ('Withdrawal was successful')
            ]);
        } else {
            return response()->json([
                'result' => false,
                'message' => ('Insuficient Balance')
            ]);
        }
    } catch (\Throwable $th) {
        return response()->json([
            'status' => false,
            'message' => $th->getMessage()
        ], 500);
    }
    }
    
}
