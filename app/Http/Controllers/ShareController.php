<?php

namespace App\Http\Controllers;

use App\Models\Person;
use App\Models\ShareToken;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Carbon\Carbon;

class ShareController extends Controller
{
    // 生成分享链接
    public function createShareLink(Request $request, Person $person)
    {
        $request->validate([
            'expires_in' => 'required|integer|min:1|max:30',
        ]);

        // 生成唯一令牌
        $token = Str::random(64);
        $expiresAt = Carbon::now()->addDays($request->expires_in);

        // 创建或更新分享令牌
        $shareToken = ShareToken::updateOrCreate(
            ['person_id' => $person->id, 'created_by' => auth()->id()],
            ['token' => $token, 'expires_at' => $expiresAt]
        );

        // 生成分享链接
        $shareLink = route('share.edit', ['token' => $token]);

        return response()->json([
            'share_link' => $shareLink,
            'expires_at' => $expiresAt->format('Y-m-d H:i:s'),
        ]);
    }

    // 显示分享编辑页面
    public function showSharedEdit(Request $request, $token)
    {
        $shareToken = ShareToken::where('token', $token)->firstOrFail();

        if (!$shareToken->isValid()) {
            abort(403, __('app.link_expired'));
        }

        // 重定向到人物编辑页面
        return redirect()->route('people.edit-profile', $shareToken->person_id);
    }

    // 处理分享编辑提交
    public function updateSharedPerson(Request $request, $token)
    {
        $shareToken = ShareToken::where('token', $token)->firstOrFail();

        // 检查令牌是否有效
        if (!$shareToken->isValid()) {
            abort(403, __('app.link_expired'));
        }

        $person = $shareToken->person;

        // 调用 PeopleController 的更新逻辑
        return app(PeopleController::class)->update($request, $person);
    }
}