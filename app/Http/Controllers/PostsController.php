<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Post;
use App\Models\User;
use Illuminate\Support\Facades\Validator;


class PostsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return response()->json([
        Post::with('user:id,name','categories:name')->get()
        ]);
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $auth_user = request()->get('auth_user')->first();
        $rules=[
            'title'=>'required|string|min:2|max:255',
            'content'=>'required|string|min:2|max:500',
            'cat1'=>'required|integer',
            'cat2'=>'interger',
            'cat3'=>'interger'
        ];
        $validator = Validator::make($request->all(),$rules);
        if($validator->fails()){
            return response(['message'=>$validator->errors()]);
        }
        $data = $request->all();
        $data['user_id'] = $auth_user['id'];

        $post = Post::create($data);
        $cat = request(['cat1','cat2','cat3']);
        $categories = Category::find($cat);

        $post->categories()->attach($categories);
        return response(['data'=>$post,'categories'=>$post->categories()->get()]);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $post = Post::with(['user:id,name','categories:name'])->find($id);
        if(is_null($post)){
            return response(['message'=>'Posts are not found!']);
        }
        return response(['data'=>$post]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $rules=[
            'title'=>'string|min:2|max:255',
            'content'=>'string|min:2|max:500',
            'cat1'=>'required|integer',
            'cat2'=>'integer',
            'cat3'=>'integer'
        ];
        $validator = Validator::make($request->all(),$rules);
        if($validator->fails()){
            return response(['message'=>$validator->errors()]);
        }
        $auth_user = request()->get('auth_user')->first();
        $post = Post::find($id);
        if(is_null($post)){
            return response(['message'=>'Posts are not found!']);
        }elseif($auth_user['id'] == $post['user_id']){
            $post ->update($request->only(['title','content']));
            $post->categories()->detach();
            $cat = request(['cat1','cat2','cat3']);
            $categories = Category::find($cat);
            $post->categories()->attach('$categories');
            return response(['data'=>$post,'categories'=>$post->categories()->get()]);
        }else{
            return response(['message'=>'Unauthorized user!']);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $auth_user = request()->get('auth_user')->first();
        $post = Post::find($id);
        if(is_null($post)){
            return response(['message'=>'post not found!']);
        }
        if($auth_user['id'] == $post['user_id']){
            $post->categories()->detach();
            $post->delete();
            return response(['message'=>'post deleted!']);
        }
        return response(['message'=>'Unauthorized user!']);
    }

    public function userPosts($user_id){
        $user = User::find($user_id);
        if(!is_null($user)){
            return response(['data'=>$user->posts()->with('categories:name')->get()]);
        }
        return response(['message'=>'User not found!']);
    }
}