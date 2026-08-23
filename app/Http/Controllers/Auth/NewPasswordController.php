<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class NewPasswordController extends Controller
{
    // 1. 재설정 링크의 token을 포함한 요청을 뷰에 전달한다
    // 2. 뷰가 token과 이메일을 다음 비밀번호 재설정 요청에 함께 전송하게 한다
    public function create(Request $request): View
    {
        return view('auth.reset-password', ['request' => $request]);
    }

    // 1. 토큰, 이메일, 새 비밀번호와 비밀번호 확인값을 검증한다
    // 2. Password Broker에 재설정을 요청해 토큰과 사용자를 확인한다
    // 3. 유효한 경우에만 비밀번호와 로그인 유지 토큰을 갱신하고 PasswordReset 이벤트를 발생시킨다
    // 4. 성공하면 로그인 화면으로, 실패하면 이전 입력값과 오류를 이전 화면으로 돌려보낸다
    public function store(Request $request): RedirectResponse
    {
        // validate()는 검증 실패 시 오류를 errors 세션 플래시에 저장하고 이전 URL로 redirect한다
        $request->validate([
            'token' => ['required'],
            'email' => ['required', 'email'],
            // Rules\Password::defaults()는 애플리케이션에 설정된 비밀번호 규칙을 적용한다
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        // Password::reset()은 Password Broker가 토큰과 이메일(첫 번째 인자)을 확인한 경우에만 콜백(두 번째 인자)을 실행하게 한다
        $status = Password::reset(
            // only()는 재설정에 필요한 네 필드만 [키 => 값] 배열로 반환한다
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function (User $user) use ($request) {
                // forceFill()은 $fillable 제한을 거치지 않고 지정한 속성을 모델에 넣어 인증 관련 필드를 갱신한다
                $user->forceFill([
                    // Hash::make()는 새 평문 비밀번호를 데이터베이스에 저장하기 전에 해시로 변환한다
                    'password' => Hash::make($request->password),
                    // Str::random()은 기존 로그인 유지 쿠키를 무효화할 새 remember_token 값을 만든다
                    'remember_token' => Str::random(60),
                // save()는 변경된 모델 속성을 users 테이블에 저장한다
                ])->save();

                // PasswordReset은 비밀번호 재설정 완료를 Listener에 알리는 Laravel 기본 인증 이벤트다
                event(new PasswordReset($user));
            }
        );

        // Password::PASSWORD_RESET은 재설정 성공 상태 키 passwords.reset과 같은 값이다
        return $status == Password::PASSWORD_RESET
                    // route()는 login이라는 이름의 로그인 URL을 만들고 with()는 상태 메시지를 다음 요청까지 플래시한다
                    ? redirect()->route('login')->with('status', __($status))
                    // withInput()은 email 값만 _old_input에 플래시하고 withErrors()는 email 오류를 errors에 플래시한다
                    : back()->withInput($request->only('email'))
                        ->withErrors(['email' => __($status)]);
    }
}
