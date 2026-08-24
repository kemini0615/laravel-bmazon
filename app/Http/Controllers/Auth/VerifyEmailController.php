<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Auth\Events\Verified;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\RedirectResponse;

class VerifyEmailController extends Controller
{
    // 1. URL 서명과 현재 사용자에 연결된 이메일 인증 링크인지 확인한다
    // 2. 이미 인증했다면 사용자 유형에 맞는 대시보드로 이동한다
    // 3. 아직 인증하지 않았다면 인증 시각을 저장하고 Verified 이벤트를 발생시킨다
    public function __invoke(EmailVerificationRequest $request): RedirectResponse
    {
        // EmailVerificationRequest는 현재 사용자 ID와 URL의 id, 현재 이메일 해시와 URL의 hash가 일치하는지 확인하는 Form Request다
        $user = $request->user();

        if ($user->hasVerifiedEmail()) {
            return redirect()->intended(
                route($user->user_type === 'seller' ? 'seller.dashboard' : 'buyer.dashboard', absolute: false).'?verified=1'
            );
        }

        // markEmailAsVerified()는 email_verified_at에 현재 시각을 저장해 이메일 인증 완료 상태로 만든다
        if ($user->markEmailAsVerified()) {
            // Verified는 이메일 인증 완료를 Listener에 알리는 Laravel 기본 인증 이벤트다
            event(new Verified($user));
        }

        return redirect()->intended(
            route($user->user_type === 'seller' ? 'seller.dashboard' : 'buyer.dashboard', absolute: false).'?verified=1'
        );
    }
}
