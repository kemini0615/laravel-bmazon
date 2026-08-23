<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\View\View;

class PasswordResetLinkController extends Controller
{
    // 1. 비밀번호 재설정 링크를 요청하는 화면을 출력한다
    public function create(): View
    {
        return view('auth.forgot-password');
    }

    // 1. 요청에서 이메일을 받아 필수값이면서 이메일 형식인지 검증한다
    // 2. Password Facade를 통해 해당 사용자에게 재설정 링크를 전송한다
    // 3. 전송 성공 시 상태 메시지를 이전 화면에 저장한다
    // 4. 실패 시 입력 이메일과 오류 메시지를 이전 화면으로 돌려보낸다
    public function store(Request $request): RedirectResponse
    {
        // 이메일이 비어 있거나 형식이 잘못된 경우 오류를 세션에 저장한다
        $request->validate([
            'email' => ['required', 'email'],
        ]);

        // Password Broker는 비밀번호 재설정 토큰 관리, 알림 전송을 config/auth.php의 passwords 설정에 따라 처리하는 Laravel 기능이다
        $status = Password::sendResetLink(
            // only($key)은 요청 전체에서 email 키와 값만 뽑아 [키 => 값] 배열로 반환한다
            $request->only('email')
        );

        // Password Broker는 알림 성공 시 passwords.sent를, 실패 시 passwords.user 또는 passwords.throttled를 반환한다
        return $status == Password::RESET_LINK_SENT
                    ? back()
                        ->with('status', __($status))
                    : back()
                        ->withInput($request->only('email')) // withInput()은 email 배열을 _old_input 세션 플래시에 저장해 old('email')이 읽게 한다
                        ->withErrors(['email' => __($status)]); // withErrors()는 email 필드 오류를 errors 세션 플래시에 저장해 @error('email')이 읽게 한다
    }
}
