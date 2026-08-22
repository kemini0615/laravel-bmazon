<?php

namespace App\Http\Requests\Auth;

use Illuminate\Auth\Events\Lockout;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

// Form Request는 요청 검증과 요청 처리에 필요한 보조 메서드를 별도 클래스로 분리하는 Laravel 기능이다
// Controller를 작게 유지하고 입력 규칙을 재사용하기 위해 사용한다
class LoginRequest extends FormRequest
{
    // 1. 로그인 요청을 보낸 사용자가 이 Form Request를 사용할 수 있는지 판단한다
    // 2. 현재 로그인 여부와 관계없이 로그인 시도 자체는 허용한다
    public function authorize(): bool
    {
        return true;
    }

    // 1. 이메일과 비밀번호 입력값에 필요한 검증 규칙을 선언한다
    // 2. Controller가 별도의 검증 코드를 작성하지 않아도 Laravel이 이 규칙을 실행한다
    /**
     * @return array<string, array<int, string>>
     */
    public function rules(): array
    {
        return [
            'email' => ['required', 'string', 'email'],
            'password' => ['required', 'string'],
        ];
    }

    // 1. 현재 요청이 로그인 시도 제한을 초과했는지 확인한다
    // 2. 제한되지 않았다면 이메일과 비밀번호로 인증을 시도한다
    // 3. 인증에 실패하면 해당 IP와 이메일 조합의 실패 횟수를 증가시킨다
    // 4. 인증에 성공하면 실패 횟수를 초기화한다
    public function authenticate(): void
    {
        $this->ensureIsNotRateLimited();

        // Auth::attempt는 입력한 인증 정보가 일치하면 로그인 세션을 생성한다
        if (! Auth::attempt($this->only('email', 'password'), $this->boolean('remember'))) {
            // RateLimiter::hit()은 현재 throttle key의 실패 횟수를 1회 증가시킨다
            RateLimiter::hit($this->throttleKey());

            // ValidationException::withMessages()는 email 필드 오류를 errors 세션 플래시에 저장한다
            throw ValidationException::withMessages([
                // trans()는 auth.failed 키에 해당하는 현재 locale의 번역 메시지를 가져온다
                'email' => trans('auth.failed'),
            ]);
        }

        // RateLimiter::clear()는 인증에 성공한 throttle key의 실패 횟수를 초기화한다
        RateLimiter::clear($this->throttleKey());
    }

    // 1. 현재 로그인 시도가 5회를 초과했는지 확인한다
    // 2. 초과했다면 Lockout 이벤트를 발생시키고 남은 대기 시간을 오류로 반환한다
    // 3. 초과하지 않았다면 인증 로직이 계속 실행되도록 반환한다
    public function ensureIsNotRateLimited(): void
    {
        // RateLimiter::tooManyAttempts()는 throttle key가 허용 횟수 5회를 넘었는지 확인한다
        if (! RateLimiter::tooManyAttempts($this->throttleKey(), 5)) {
            return;
        }

        // Lockout 이벤트를 dispatch해 로그인 제한 사실을 Listener에 알린다
        event(new Lockout($this));

        // RateLimiter::availableIn()은 현재 throttle key가 다시 허용되기까지 남은 초를 반환한다
        $seconds = RateLimiter::availableIn($this->throttleKey());

        throw ValidationException::withMessages([
            // auth.throttle 번역문의 :seconds와 :minutes 자리에 아래 값을 넣는다
            'email' => trans('auth.throttle', [
                'seconds' => $seconds,
                'minutes' => ceil($seconds / 60),
            ]),
        ]);
    }

    // 1. 사용자가 입력한 이메일을 소문자로 통일한다
    // 2. 이메일과 현재 IP 주소를 조합해 사용자별 로그인 제한 키를 만든다
    // 3. 같은 이메일이라도 다른 IP의 요청과 제한을 분리한다
    public function throttleKey(): string
    {
        // Str::transliterate는 문자열을 RateLimiter가 사용할 수 있는 안정적인 형태로 변환한다
        return Str::transliterate(Str::lower($this->string('email')).'|'.$this->ip());
    }
}
