<?php

namespace App\Http\Controllers;

use App\Models\AncientText;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\JsonResponse;

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
    public function show(Request $request, int $ancient_text): View
    {
        $text = AncientText::where('team_id', auth()->user()->current_team_id)
        ->findOrFail($ancient_text);
            
        // 每页 10 行
        $paginatedContent = $text->getPaginatedContent();
        
        // 获取当前页码，默认为第 1 页
        $currentPage = $request->query('page', 1);
        $currentPage = max(1, (int)$currentPage); // 确保页码至少为 1
        
        // 总页数
        $totalPages = count($paginatedContent);
        
        // 当前页内容
        $currentPageContent = $paginatedContent[$currentPage - 1] ?? [];
            
        return view('back.ancient-text.show', compact('text', 'currentPageContent', 'currentPage', 'totalPages'));
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
            'show_on_homepage' => 'boolean',
        ]);
        
        // 如果设置了当前古籍为首页展示，则取消其他古籍的首页展示设置
        if ($request->has('show_on_homepage') && $request->show_on_homepage) {
            AncientText::where('team_id', auth()->user()->current_team_id)
                ->update(['show_on_homepage' => false]);
        }
        
        $validated['team_id'] = auth()->user()->current_team_id;
        $validated['show_on_homepage'] = $request->has('show_on_homepage') ? true : false;
        
        AncientText::create($validated);
        
        return redirect()->route('ancient-text.index')
            ->with('success', __('app.created_successfully'));
    }
    
    // /**
    //  * 更新古籍
    //  */
    // public function update(Request $request, int $id): RedirectResponse
    // {
    //     $text = AncientText::where('team_id', auth()->user()->current_team_id)
    //         ->findOrFail($id);
            
    //     $validated = $request->validate([
    //         'title' => 'required|string|max:255',
    //         'content' => 'required|string',
    //         'volume' => 'required|integer|min:1',
    //         'page' => 'required|integer|min:1',
    //         'show_on_homepage' => 'boolean',
    //     ]);
        
    //     // 如果设置了当前古籍为首页展示，则取消其他古籍的首页展示设置
    //     if ($request->has('show_on_homepage') && $request->show_on_homepage) {
    //         AncientText::where('team_id', auth()->user()->current_team_id)
    //             ->where('id', '!=', $id)
    //             ->update(['show_on_homepage' => false]);
    //     }
        
    //     $validated['show_on_homepage'] = $request->has('show_on_homepage') ? true : false;
        
    //     $text->update($validated);
        
    //     return redirect()->route('ancient-text.show', $text->id)
    //         ->with('success', __('app.updated_successfully'));
    // }
    
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
    /**
     * 预览内容（AJAX 端点）
     */
    public function preview(Request $request): JsonResponse
    {
        $content = $request->input('content');
        if (empty($content)) {
            return response()->json(['paginatedContent' => []]);
        }

        // 创建一个临时的 AncientText 实例来处理分页
        $text = new AncientText();
        $text->content = $content;
        $paginatedContent = $text->getPaginatedContent();

        return response()->json(['paginatedContent' => $paginatedContent]);
    }
}