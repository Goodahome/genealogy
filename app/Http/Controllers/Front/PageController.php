<?php

declare(strict_types=1);

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Laravel\Jetstream\Jetstream;
use App\Models\Event;
use App\Models\AncientText;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

final class PageController extends Controller
{
    public function home(Request $request): View
    {
        try {
            // 检查用户是否已登录
            if (Auth::check()) {
                // 获取当前用户的团队ID
                $teamId = Auth::user()->currentTeam->id;
                
                // 只获取当前团队的事件
                $events = Event::where('team_id', $teamId)
                              ->orderBy('date', 'asc')
                              ->get();
                
                // 获取设置为在首页显示的古籍
                $homepageText = AncientText::where('team_id', $teamId)
                    ->where('show_on_homepage', true)
                    ->first();
                    
                // 如果没有设置首页显示的古籍，则获取最新的一篇
                if (!$homepageText) {
                    $homepageText = AncientText::where('team_id', $teamId)
                        ->latest()
                        ->first();
                }
                
                if ($homepageText) {
                    // 每页10行
                    $paginatedContent = $homepageText->getPaginatedContent();
                    
                    // 获取当前页码，默认为第1页
                    $currentPage = $request->query('page', 1);
                    $currentPage = max(1, (int)$currentPage);
                    
                    // 总页数
                    $totalPages = count($paginatedContent);
                    
                    // 当前页内容
                    $currentPageContent = $paginatedContent[$currentPage - 1] ?? [];
                } else {
                    $homepageText = null;
                    $currentPageContent = [];
                    $currentPage = 1;
                    $totalPages = 0;
                }
            } else {
                // 未登录用户不显示任何事件和古籍
                $events = collect();
                $homepageText = null;
                $currentPageContent = [];
                $currentPage = 1;
                $totalPages = 0;
            }
            
            return view('home', compact('events', 'homepageText', 'currentPageContent', 'currentPage', 'totalPages'));
        } catch (\Exception $e) {
            \Log::error('Home page error: ' . $e->getMessage());
            return view('home', [
                'events' => [],
                'homepageText' => null,
                'currentPageContent' => [],
                'currentPage' => 1,
                'totalPages' => 0,
                'error' => '无法加载内容，请稍后重试。',
            ]);
        }
    }

    public function about(): View
    {
        $aboutFile = Jetstream::localizedMarkdownPath(app()->getLocale() . '/' . 'about.md');

        return view('about', [
            'about' => Str::markdown(file_get_contents($aboutFile)),
        ]);
    }

    public function help(): View
    {
        $helpFile = Jetstream::localizedMarkdownPath('help.md');

        return view('help', [
            'help' => Str::markdown(file_get_contents($helpFile)),
        ]);
    }
}
