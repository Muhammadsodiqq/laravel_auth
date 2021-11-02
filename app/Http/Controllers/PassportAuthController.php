<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class PassportAuthController extends Controller
{
    public function register (Request $request) {
        try {

            $this->validate($request , [
                'name' => 'required|min:4',
                'email'=>'required|email',
                'password'=>'required|min:6',
            ]);
            $data = User::where("email",$request->email)->first();
            if($data){
                 throw new \Exception("this user already exists");
            }
            $user = User::create([
                'name' => $request->name,
                'email'=>$request->email,
                'password'=>bcrypt($request->password)
            ]);
            $token = $user->createToken('LaravelAuthApp')->accessToken;
            return response()->json([
                "ok"=>true,
                "token"=>$token,
            ],200);
        }catch (\Exception $err) {
            return response()->json(["ok"=>false,"message"=>$err->getMessage()]);
        }
    }

    public function login (Request $request) {
        try {
            $this->validate($request , [
                'email'=>'required|email',
                'password'=>'required|min:6',
            ]);
            $data = [
                'email' => $request->email,
                'password' => $request->password
            ];

            if(auth()->attempt($data)) {
                $token = auth()->user()->createToken('LaravelAuthApp')->accessToken;
                return response()->json(["ok"=>true,'token' => $token],200);
            }else {
                return response()->json(['message' => 'This user is Unauthorised'], 401);
            }
        }catch (\Exception $err) {
            return response()->json(["ok"=>false,"message"=>$err->getMessage()]);

        }
    }
}
