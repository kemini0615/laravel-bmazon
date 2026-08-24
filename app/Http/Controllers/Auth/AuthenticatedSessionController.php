<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    // 1. 로그인 화면을 출력한다
    // 2. Blade 뷰 이름을 반환해 사용자가 로그인 폼을 볼 수 있게 한다
    public function create(): View
    {
        return view('auth.login');
    }

    // 1. LoginRequest에서 입력값 검증과 인증 시도를 수행한다
    // 2. 인증 성공 후 세션 ID를 재생성해 세션 고정 공격을 방지한다
    // 3. Seller 사용자는 Seller 대시보드로, Buyer 사용자는 Buyer 대시보드로 이동시킨다
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();

        // 로그인 후 세션 ID를 바꾸어 기존 세션 ID를 재사용하지 않는다
        $request->session()->regenerate();

        if (auth('web')->user()->user_type === 'seller') {
            // redirect()->intended()는 로그인 전에 접근하려던 URL이 있으면 그곳으로, 없으면 전달한 URL로 이동한다
            return redirect()->intended(route('seller.dashboard', absolute: false));
        }

        // Buyer도 같은 방식으로 buyer.dashboard라는 이름의 라우트 URL로 이동한다
        return redirect()->intended(route('buyer.dashboard', absolute: false));
    }

    // 1. web guard에서 현재 사용자를 로그아웃시킨다
    // 2. 현재 세션을 무효화한다
    // 3. CSRF 토큰을 재생성해 이전 세션의 요청 토큰을 폐기한다
    public function destroy(Request $request): RedirectResponse
    {
        // 인증 상태를 제거한다
        Auth::guard('web')->logout();

        // 현재 세션을 폐기한다
        $request->session()->invalidate();

        // CSRF 토큰을 새로 만든다
        $request->session()->regenerateToken();

        // 로그아웃 후 홈 URL로 이동하는 응답을 만든다
        return redirect('/');
    }
}
