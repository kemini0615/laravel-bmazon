<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class EmailVerificationPromptController extends Controller
{
    // 1. 현재 로그인 사용자의 이메일 인증 완료 여부를 확인한다
    // 2. 이미 인증했다면 사용자 유형에 맞는 대시보드로 이동한다
    // 3. 아직 인증하지 않았다면 이메일 인증 안내 화면을 출력한다
    public function __invoke(Request $request): RedirectResponse|View
    {
        // user()는 auth 미들웨어가 인증한 현재 User 모델을 반환한다
        $user = $request->user();

        // hasVerifiedEmail()은 email_verified_at 값으로 인증 여부를 확인한다
        if (! $user->hasVerifiedEmail()) {
            return view('auth.verify-email');
        }

        if ($user->user_type === 'seller') {
            return redirect()->intended(route('seller.dashboard', absolute: false));
        }

        return redirect()->intended(route('buyer.dashboard', absolute: false));
    }
}
