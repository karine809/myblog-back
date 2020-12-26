<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api');
    }
    public function newblog(Request $request){
        $rules = [
            'title' => 'required',
            'name' =>'required',
            'description' => 'required'
        ];
        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {

            $errors = $validator->errors();

            return response($errors, 419);
        } else {
            Post::create([
                'title' => $request->title,
                'name' => $request->name,
                'description' => $request->description,
                'photo'=> json_encode($request->photo),
                'user_id' => auth()->user()->id
            ]);

            return response('success', 200);
        }
    }
    public function myblogs(Request $request){
        if(isset($request->search_text)){
            $myposts =Post::where("user_id", auth()->user()->id)->where('name', 'LIKE', '%'.$request->search_text.'%')->orwhere('title', 'LIKE', '%'.$request->search_text.'%')->get();

        } else {
            $myposts =Post::where("user_id", auth()->user()->id)->get();
        }

        return $myposts;
    }
    public function myblog(Request $request,$id){
        $post = Post::find($id);

        return $post;
    }
    public function editblog(Request $request, $id){
        $post = Post::find($id);
        $post->update([
            'title' => $request->title,
            'name' => $request->name,
            'description' => $request->description
        ]);
        return $post;
    }

    public function deleteblog($id){
        Post::where("id", $id)->delete();

        return 'success';
    }
    public function allblogs(){
        $allblogs = Post::all();

        return $allblogs;
    }
    public function allbloggers(){
        $bloggers=User::where('user_type', 'blogger')->get();

            return $bloggers;
    }
    public function lockuser(Request $request, $id){
        $user = User::find($id);
        $user->user_locked = true;
        $user->save();
        return response('success', 200);
    }
    public function unlockuser(Request $request, $id){
        $user = User::find($id);
        $user->user_locked = false;
        $user->save();
        return response('success', 200);
    }



}
