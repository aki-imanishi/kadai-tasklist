<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Task; 

class TasksController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
     
     
     
     // getでmessages/にアクセスされた場合の「一覧表示処理」
    public function index()
    {
        if (\Auth::check()) { // 認証済みの場合
             // 認証済みユーザを取得
            $user = \Auth::user();
            
            // task一覧を取得
            $tasks = $user->tasks()->get(); //ユーザのtaskのみ取得
            
            // task一覧をviewで表示
            return view('tasks.index', [ //tasks.indexはbladeファイル名
                'tasks' => $tasks, //$tasksには取得したtaskデータが入っている。これを'tasks'に代入。'tasks'はbladeファイル内で$tasksとして使用できる。
            ]);
            
        }
         
         //loginしていなかったらwelcome.blade.phpにリダイレクト
         return view('welcome');
         
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $task = new Task;

        // メッセージ作成ビューを表示
        return view('tasks.create', [
            'task' => $task,
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
        // バリデーション
        $request->validate([
            'status' => 'required|max:10',  
            'content' => 'required|max:255',
        ]);
        
        // 認証済みユーザ（閲覧者）のtaskとして作成（リクエストされた値をもとに作成）
        $request->user()->tasks()->create([
            'status' => $request->status,
            'content' => $request->content,
        ]);

        // トップページへリダイレクトさせる
        return redirect('/');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        // idの値でtaskを検索して取得
        $task = Task::findOrFail($id);
        
        if (\Auth::id() === $task->user_id) {
            // task詳細ビューでそれを表示
            return view('tasks.show', [
                'task' => $task,
            ]);
        } else {
            return redirect('/');
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        // idの値でメッセージを検索して取得
        $task = Task::findOrFail($id);
        
        if (\Auth::id() === $task->user_id) {
            // メッセージ編集ビューでそれを表示
            return view('tasks.edit', [
                'task' => $task,
            ]);
        } else {
            return redirect('/');
        }
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
        // バリデーション
        $request->validate([
            'status' => 'required|max:10',   
            'content' => 'required|max:255',
        ]);
        
        // idの値でメッセージを検索して取得
        $task = Task::findOrFail($id);
        // メッセージを更新
        $task->status = $request->status;
        $task->content = $request->content;
        $task->save();

        // トップページへリダイレクトさせる
        return redirect('/');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        // idの値でメッセージを検索して取得
        $task = Task::findOrFail($id);
        
        // 認証済みユーザ（閲覧者）がその投稿の所有者である場合は、taskを削除
        if (\Auth::id() === $task->user_id) {
            $task->delete();
        }

        // トップページへリダイレクトさせる
        return redirect('/');
    }
}
