<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class EmailVerificationNotificationController extends Controller
{
    // 1. 현재 사용자가 이미 이메일 인증을 마쳤는지 확인한다
    // 2. 인증을 마쳤다면 사용자 유형에 맞는 대시보드로 이동한다
    // 3. 미인증 사용자에게 새 인증 링크를 전송하고 성공 상태를 이전 화면에 저장한다
    public function store(Request $request): RedirectResponse
    {
        $user = $request->user();

        if ($user->hasVerifiedEmail()) {
            return redirect()->intended(
                route($user->user_type === 'seller' ? 'seller.dashboard' : 'buyer.dashboard', absolute: false)
            );
        }

        // sendEmailVerificationNotification()은 VerifyEmail Notification을 통해 서명된 이메일 인증 링크를 현재 사용자의 이메일로 전송한다
        $user->sendEmailVerificationNotification();

        // back()은 인증 안내 화면으로 돌아가고 with()는 verification-link-sent 상태값을 다음 요청까지 세션 플래시에 저장한다
        return back()->with('status', 'verification-link-sent');
    }
}
