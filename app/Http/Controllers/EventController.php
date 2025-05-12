<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Event;
use Illuminate\Support\Facades\Auth;

class EventController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // 检查用户是否已登录
        if (!Auth::check()) {
            return redirect()->route('login');
        }
        
        // 获取当前用户的团队ID
        $teamId = Auth::user()->currentTeam->id;
        
        // 只获取当前团队的事件
        $events = Event::where('team_id', $teamId)
                      ->orderBy('date', 'asc')
                      ->get();
                      
        return view('events.events', compact('events'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // 检查用户是否已登录
        if (!Auth::check()) {
            return redirect()->route('login');
        }
        
        // 检查权限
        if (!Auth::user()->hasPermission('event:create')) {
            abort(403, '您没有权限执行此操作');
        }
        
        return view('events.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // 检查用户是否已登录
        if (!Auth::check()) {
            return redirect()->route('login');
        }
        
        // 检查权限
        if (!Auth::user()->hasPermission('event:create')) {
            abort(403, '您没有权限执行此操作');
        }
        
        $request->validate([
            'date' => 'required|date',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        // 获取当前用户的团队ID
        $teamId = Auth::user()->currentTeam->id;
        
        // 创建事件并关联到当前团队
        Event::create([
            'date' => $request->date,
            'title' => $request->title,
            'description' => $request->description,
            'team_id' => $teamId,
        ]);
        
        return redirect()->route('home')->with('success', '事件创建成功！');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
