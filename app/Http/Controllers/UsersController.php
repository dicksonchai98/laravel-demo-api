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
    $data['is_admin'] = false;
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
        $auth_user = request()->get('auth_user')->first();
        if($auth_user['is_admin']){
            return response(['data'=>User::get()]);
            // return  User::all();
        }
        return response(['message'=>'Unauthorized user']);
    }

    public function show($id){
        $auth_user = request()->get('auth_user')->first();
        $user = User::find($id);

        if($auth_user['is_admin']){
            if(!is_null($user)){
                return response(['data'=>$user]);
            }else{
                return response(['message'=>'User not found!']);
            }
        }elseif($auth_user['id']==$id){
            return response(['data'=>$user]);
        }else{
            return response(['message'=>'Unauthorized!']);
        }
    }

    public function update(Request $request,$id){
        $rules=[
            'name'=>'string|min:2|max:255',
            'email'=>'email|max:255|unique:users,email,'.$id,
            'password'=>'string|min:6|max:12|confirmed'
        ];
        $validator = Validator::make($request->all(),$rules);

        if($validator->fails()){
            return response(['message'=>$validator->errors()]);
        }
        $auth_user = request()->get('auth_user')->first();
        $user = User::find($id);

        if(!is_null($user)){
            if($auth_user['id']==$id){
                $user->update($request->only(['name','email','password']));
                return response(['data'=>$user]);
            }else{
                return response(['message'=>'Unauthorized user!']);
            }
        }
        return response(['message'=>'User not found!']);
    }

    public function destroy($id){
        $user = User::find($id);
        $auth_user = request()->get('auth_user')->first();
        if(is_null($user)){
            return response(['message'=>'User not found!']);
        }
        if($auth_user['id']==$id){
            $user->delete();
            return response(['message'=>'User deleted!']);
        }
        return response(['message'=>'Unauthorized user!']);
    }
}   