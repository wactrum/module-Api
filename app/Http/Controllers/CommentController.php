<?php

namespace App\Http\Controllers;

use App\Comment;
use Illuminate\Http\Request;
use Auth;
use Validator;

class CommentController extends Controller
{
    /**
     * Добавление коментария
     */
    public function store($post, Request $request)
    {
        $request->request->add(['postId' => $post]);
        //Если пользователь авторизован
        if(Auth::guard('api')->user()){
            $request->request->add(['author' => 'admin']);
            $validator = Validator::make($request->all(), [
                'comment' => 'required|max:255',
            ]);
        }
        else{
            $validator = Validator::make($request->all(), [
                'author' => 'required|',
                'comment' => 'required|max:255',
            ]);
        }
        //Если есть ошибки вернуть их с ошибкой
        if ($validator->fails()) {
            return response()->json([
                'status'=>false,
                'message'=>$validator->messages()
            ], 400)->setStatusCode(400,'Creating error');
        }
        Comment::create($request->all());
        return response()->json([
            'status'=>true
        ],201)->setStatusCode(201,'Successful creation');
    }

    /**
     * Удаление коментария
     */
    public function delete($post, $comment){
        Comment::where([
            ['postId',$post],
            ['id',$comment]
        ])->delete();
        return response()->json([
            'status'=>true
        ], 201)->setStatusCode(201,'Successful delete');
    }
}