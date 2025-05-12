<?php

declare(strict_types=1);

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Laravel\Jetstream\Jetstream;
use App\Models\Event;
use Illuminate\Support\Facades\Auth;

final class PageController extends Controller
{
    public function home(): View
    {
        // $homeFile = Jetstream::localizedMarkdownPath(app()->getLocale() . '/' . 'home.md');

        // return view('home', [
        //     'home' => Str::markdown(file_get_contents($homeFile)),
        // ]);
        try {
            $pdfUrl = asset('pdfs/zupu.pdf');
            
            // 检查用户是否已登录
            if (Auth::check()) {
                // 获取当前用户的团队ID
                $teamId = Auth::user()->currentTeam->id;
                
                // 只获取当前团队的事件
                $events = Event::where('team_id', $teamId)
                              ->orderBy('date', 'asc')
                              ->get();
            } else {
                // 未登录用户不显示任何事件
                $events = collect();
            }
            
            return view('home', compact('pdfUrl', 'events'));
        } catch (\Exception $e) {
            \Log::error('Home page error: ' . $e->getMessage());
            return view('home', [
                'pdfUrl' => null,
                'events' => [],
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
