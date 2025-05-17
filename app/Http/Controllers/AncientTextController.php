<?php

namespace App\Http\Controllers;

use App\Models\AncientText;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\JsonResponse;
use Laravel\Jetstream\Jetstream;

class AncientTextController extends Controller
{
    /**
     * 构造函数，添加权限检查
     */
    public function __construct()
    {
        // 移除中间件调用，改为在各方法中检查权限
    }

    /**
     * 将中文大写数字转换为阿拉伯数字
     */
    private function chineseToNumber($chinese)
    {
        $chineseNumbers = ['零' => 0, '壹' => 1, '贰' => 2, '叁' => 3, '肆' => 4, '伍' => 5, '陆' => 6, '柒' => 7, '捌' => 8, '玖' => 9];
        return $chineseNumbers[$chinese] ?? (int)$chinese;
    }

    /**
     * Display a listing of ancient texts.
     */
    public function index(): View
    {
        // 检查权限
        if (!auth()->user()->hasTeamPermission(auth()->user()->currentTeam, 'ancient-text:read')) {
            abort(403);
        }
        
        $texts = AncientText::where('team_id', auth()->user()->current_team_id)
            ->orderBy('title')
            ->orderBy('volume')
            ->orderBy('page')
            ->paginate(10);
            
        return view('back.ancient-text.index', compact('texts'));
    }
    
    /**
     * Show the form for creating a new ancient text.
     */
    public function create(): View
    {
        // 检查权限
        if (!auth()->user()->hasTeamPermission(auth()->user()->currentTeam, 'ancient-text:create')) {
            abort(403);
        }
        
        return view('back.ancient-text.create');
    }
    
    /**
     * Store a newly created ancient text.
     */
    public function store(Request $request): RedirectResponse
    {
        // 检查权限
        if (!auth()->user()->hasTeamPermission(auth()->user()->currentTeam, 'ancient-text:create')) {
            abort(403);
        }
        
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'volume' => 'required|string', // 改为字符串，接受中文大写数字
            'show_on_homepage' => 'boolean',
        ]);
        
        $validated['team_id'] = auth()->user()->current_team_id;
        $validated['show_on_homepage'] = $request->has('show_on_homepage');
        $validated['volume'] = $this->chineseToNumber($validated['volume']); // 转换为阿拉伯数字

        // 创建临时 AncientText 实例以计算总页数
        $text = new AncientText();
        $text->content = $validated['content'];
        $validated['page'] = $text->calculateTotalPages(); // 自动计算页数

        AncientText::create($validated);
        
        return redirect()->route('ancient-text.index')
            ->with('success', __('app.created_successfully'));
    }
    
    /**
     * Display the specified ancient text.
     */
    public function show(Request $request, int $id): View
    {
        // 检查权限
        if (!auth()->user()->hasTeamPermission(auth()->user()->currentTeam, 'ancient-text:read')) {
            abort(403);
        }
        
        $text = AncientText::where('team_id', auth()->user()->current_team_id)
            ->findOrFail($id);
            
        $paginatedContent = $text->getPaginatedContent(); // 使用默认值 17
        
        $currentPage = $request->query('page', 1);
        $currentPage = max(1, (int)$currentPage);
        
        $totalPages = count($paginatedContent);
        $currentPageContent = $paginatedContent[$currentPage - 1] ?? [];
            
        return view('back.ancient-text.show', compact('text', 'currentPageContent', 'currentPage', 'totalPages'));
    }
    
    /**
     * Show the form for editing the specified ancient text.
     */
    public function edit(int $id): View
    {
        // 检查权限
        if (!auth()->user()->hasTeamPermission(auth()->user()->currentTeam, 'ancient-text:update')) {
            abort(403);
        }
        
        $text = AncientText::where('team_id', auth()->user()->current_team_id)
            ->findOrFail($id);
            
        return view('back.ancient-text.edit', compact('text'));
    }
    
    /**
     * Update the specified ancient text.
     */
    public function update(Request $request, int $id): RedirectResponse
    {
        // 检查权限
        if (!auth()->user()->hasTeamPermission(auth()->user()->currentTeam, 'ancient-text:update')) {
            abort(403);
        }
        
        $text = AncientText::where('team_id', auth()->user()->current_team_id)
            ->findOrFail($id);
            
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'volume' => 'required|string', // 改为字符串，接受中文大写数字
            'show_on_homepage' => 'boolean',
        ]);
        
        $validated['show_on_homepage'] = $request->has('show_on_homepage');
        $validated['volume'] = $this->chineseToNumber($validated['volume']); // 转换为阿拉伯数字

        // 重新计算页数
        $text->content = $validated['content'];
        $validated['page'] = $text->calculateTotalPages(); // 自动计算页数

        $text->update($validated);
        
        return redirect()->route('ancient-text.show', $id)
            ->with('success', __('app.updated_successfully'));
    }
    
    /**
     * Remove the specified ancient text.
     */
    public function destroy(int $id): RedirectResponse
    {
        // 检查权限
        if (!auth()->user()->hasTeamPermission(auth()->user()->currentTeam, 'ancient-text:delete')) {
            abort(403);
        }
        
        $text = AncientText::where('team_id', auth()->user()->current_team_id)
            ->findOrFail($id);
            
        $text->delete();
        
        return redirect()->route('ancient-text.index')
            ->with('success', __('app.deleted_successfully'));
    }

    /**
     * Preview the content (AJAX endpoint).
     */
    public function preview(Request $request): JsonResponse
    {
        // 检查权限 - 预览功能可以与创建/更新权限关联
        if (!auth()->user()->hasTeamPermission(auth()->user()->currentTeam, 'ancient-text:create') && 
            !auth()->user()->hasTeamPermission(auth()->user()->currentTeam, 'ancient-text:update')) {
            abort(403);
        }
        
        $content = $request->input('content');
        $volume = $this->chineseToNumber($request->input('volume'));
        if (empty($content)) {
            return response()->json(['paginatedContent' => []]);
        }

        $text = new AncientText();
        $text->content = $content;
        $text->volume = $volume;
        $paginatedContent = $text->getPaginatedContent(); // 使用默认值 17

        return response()->json(['paginatedContent' => $paginatedContent]);
    }
}