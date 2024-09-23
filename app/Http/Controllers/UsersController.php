<?php

namespace App\Http\Controllers;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Validator;


class UsersController extends Controller
{
    public function register(Request $request){

    // $validated = $request->validate([
    //     'name' => 'required|',
    //     'email' => 'required|unique:users|max:255',
    //     'password' => 'required|min:6|max:12|confirmed',
    // ]);
        $validator = Validator::make($request->all(), [
        'name' => ['required'],
        'email' => ['required','unique:users','max:255'],
        'password' => ['required','min:6','max:12','confirmed'],
    ]);
      if($validator->fails()){
          return response()->json([
              'error' => true,
              'message' => $validator->errors()
          ]);
      }
    $data = $request->all();
    $data['password'] = Hash::make($data['password']);
    $data['is_admin'] = true;
    $data['api_token'] = Str::random(60);

    $user = User::create($data);

    return response(['data' => $user, 'api_token' => $user->api_token]);
    }

    public function login(Request $request){
        $email = $request->auth_email;
        $password = $request->auth_password;

        $user = User::where('email',$email)->first();
        if(!$user){
            return response(['message'=>'Login failed.Please check email id']);
        }
        if($user && !Hash::check($password,$user->password)){
            return response(['message'=>'Login failed.Please check password']);
        }

        $user->update(['api_token',Str::random(60)]);
        return response(['message'=>'Login successfully','api_token'=>$user->api_token]);
    }

    public function index(){
        // return response(['data'=>User::get()]);
      return  User::all();
    }

    public function show($id){
        $user = User::find($id);
        if(!is_null($user)){
            return response(['data'=>$user]);
        }
        return response()->json(['message'=>'User not found!'],404);
    }
}   