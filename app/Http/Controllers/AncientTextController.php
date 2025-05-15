<?php

namespace App\Http\Controllers;

use App\Models\AncientText;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class AncientTextController extends Controller
{
    /**
     * 显示古籍列表
     */
    public function index(): View
    {
        $texts = AncientText::where('team_id', auth()->user()->current_team_id)
            ->orderBy('title')
            ->orderBy('volume')
            ->orderBy('page')
            ->paginate(10);
            
        return view('back.ancient-text.index', compact('texts'));
    }
    
    /**
     * 显示单个古籍内容
     */
    public function show(int $id): View
    {
        $text = AncientText::where('team_id', auth()->user()->current_team_id)
            ->findOrFail($id);
            
        return view('back.ancient-text.show', compact('text'));
    }
    
    /**
     * 显示创建表单
     */
    public function create(): View
    {
        return view('back.ancient-text.create');
    }
    
    /**
     * 存储新古籍
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'volume' => 'required|integer|min:1',
            'page' => 'required|integer|min:1',
        ]);
        
        $validated['team_id'] = auth()->user()->current_team_id;
        
        AncientText::create($validated);
        
        return redirect()->route('ancient-text.index')
            ->with('success', __('app.created_successfully'));
    }
    
    /**
     * 显示编辑表单
     */
    public function edit(int $id): View
    {
        $text = AncientText::where('team_id', auth()->user()->current_team_id)
            ->findOrFail($id);
            
        return view('back.ancient-text.edit', compact('text'));
    }
    
    /**
     * 更新古籍内容
     */
    public function update(Request $request, int $id): RedirectResponse
    {
        $text = AncientText::where('team_id', auth()->user()->current_team_id)
            ->findOrFail($id);
            
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'volume' => 'required|integer|min:1',
            'page' => 'required|integer|min:1',
        ]);
        
        $text->update($validated);
        
        return redirect()->route('ancient-text.show', $id)
            ->with('success', __('app.updated_successfully'));
    }
    
    /**
     * 删除古籍
     */
    public function destroy(int $id): RedirectResponse
    {
        $text = AncientText::where('team_id', auth()->user()->current_team_id)
            ->findOrFail($id);
            
        $text->delete();
        
        return redirect()->route('ancient-text.index')
            ->with('success', __('app.deleted_successfully'));
    }
}