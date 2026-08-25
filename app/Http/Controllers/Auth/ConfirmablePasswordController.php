<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class ConfirmablePasswordController extends Controller
{
    // 1. 민감한 작업 전에 현재 비밀번호를 다시 입력받는 화면을 출력한다
    public function show(): View
    {
        return view('auth.confirm-password');
    }

    // 1. 현재 로그인 사용자의 이메일과 입력한 비밀번호가 일치하는지 다시 확인한다
    // 2. 일치하지 않으면 password 필드 오류를 반환한다
    // 3. 일치하면 세션에 비밀번호 확인 시각을 저장하고 사용자 유형에 맞는 대시보드로 이동한다
    public function store(Request $request): RedirectResponse
    {
        // Auth::guard('web')->validate()는 로그인 세션을 새로 만들지 않고 전달한 인증 정보만 검증한다
        if (! Auth::guard('web')->validate([
            // user()는 auth 미들웨어가 인증한 현재 User 모델을 반환해 다른 사용자의 이메일을 입력할 수 없게 한다
            'email' => $request->user()->email,
            'password' => $request->password,
        ])) {
            // ValidationException::withMessages()는 password 오류를 errors 세션 플래시에 저장해 @error('password')가 표시되게 한다
            throw ValidationException::withMessages([
                // __()는 lang/ko/auth.php의 password 번역 메시지를 가져온다
                'password' => __('auth.password'),
            ]);
        }

        // time()은 현재 Unix timestamp를 반환하고 password.confirm 미들웨어는 이 시각으로 비밀번호 재확인 유효 시간을 계산한다
        $request->session()->put('auth.password_confirmed_at', time());

        return redirect()->intended(
            route($request->user()->user_type === 'seller' ? 'seller.dashboard' : 'buyer.dashboard', absolute: false)
        );
    }
}
