# 학습 정리

## 라우트 파일과 미들웨어

- `routes/web.php`에서 `require __DIR__.'/auth.php';`를 사용하면 인증 라우트를
  별도 파일로 분리해 등록할 수 있다. `__DIR__`는 현재 PHP 파일의 디렉터리다.
- `guest` 미들웨어는 비로그인 사용자만 접근하게 하고, `auth` 미들웨어는 로그인한
  사용자만 접근하게 한다.
- `signed`는 Laravel이 서명한 URL인지 확인하고, `throttle:6,1`은 1분에 6회까지만
  요청을 허용한다.
- Controller에 `__invoke()` 하나만 정의하면 클래스 자체를 라우트 action으로 지정할
  수 있다. 한 URL만 처리하는 Controller에 사용한다.

## Form Request와 로그인 시도 제한

- Form Request는 Controller 밖에서 요청 권한과 입력값을 검증하는 Laravel 클래스다.
  `authorize()`는 권한을, `rules()`는 필드별 검증 규칙을 정의한다.
- `RateLimiter`는 지정한 키의 요청 횟수를 제한하는 Facade다. 로그인에서는 이메일과
  IP 주소를 조합한 키로 실패 횟수를 기록한다.
- 실패하면 `RateLimiter::hit()`이 횟수를 늘리고, 성공하면 `clear()`가 횟수를
  초기화한다. 제한을 넘으면 `Lockout` 이벤트를 발생시킨다.

## 세션 기반 로그인

- 현재 `web` guard는 `session` 드라이버를 사용한다. Guard는 인증 상태를 관리하는
  Laravel 객체이고, `web`은 브라우저용 기본 Guard 이름이다.
- `Auth::attempt()`는 이메일로 사용자를 찾고 입력 비밀번호를 저장된 해시와 비교한다.
  일치하면 세션에 User 모델 전체가 아니라 사용자의 기본 키인 `users.id`를 기록한다.
- 인증 정보는 세션의 `login_web_{SessionGuard 클래스 해시}` 키에 저장된다. 다음 요청에서
  Guard는 이 ID로 `users` 테이블을 다시 조회해 `auth()->user()`를 반환한다.
- 현재 `SESSION_DRIVER=database`이므로 브라우저 쿠키에는 세션 ID가, MySQL의
  `sessions` 테이블에는 그 세션 ID에 연결된 데이터가 저장된다.
- 로그인 성공 시 Laravel은 세션 ID를 재생성해 기존 세션 ID 재사용을 막는다.
  `remember`가 참이면 별도로 로그인 유지용 쿠키도 발급한다.
- `Auth::guard('web')->logout()`은 web guard의 인증 상태를 해제한다. 이어서
  `invalidate()`로 세션을 폐기하고 `regenerateToken()`으로 CSRF 토큰을 새로 만든다.

## 회원가입, 모델, 이벤트

- `$fillable`은 `User::create()` 같은 대량 할당에서 저장을 허용할 필드를 정하는
  Eloquent 모델 속성이다. `$hidden`은 모델을 배열이나 JSON으로 바꿀 때 숨길 필드다.
- `Hash::make()`는 평문 비밀번호를 해시로 바꿔 데이터베이스에 저장한다.
- `Registered`는 `Illuminate\Auth\Events\Registered`에 포함된 Laravel 기본 인증
  이벤트다. `event(new Registered($user))`는 회원가입 완료 사실을 Listener에 알린다.

### 이메일 인증

`MustVerifyEmail`이라는 이름은 같지만 namespace와 역할이 다른 두 대상이 있다.

| 대상 | 종류 | 역할 |
|---|---|---|
| `Illuminate\Contracts\Auth\MustVerifyEmail` | interface | Laravel에 이메일 인증 대상 User임을 선언한다 |
| `Illuminate\Auth\MustVerifyEmail` | trait | 이메일 인증에 필요한 실제 메서드를 제공한다 |

- interface는 메서드 본문이 없는 PHP 선언이다. App User에
  `implements MustVerifyEmail`를 적으면 다음 검사 결과가 `true`가 된다.

  ```php
  $user instanceof MustVerifyEmail
  ```

  Laravel의 기본 `Registered` Listener와 `verified` 미들웨어는 바로 이 검사 결과로
  이메일 인증 기능을 적용할지 결정한다.

  ```php
  // 회원가입 뒤 기본 Listener의 판단
  if ($user instanceof MustVerifyEmail && ! $user->hasVerifiedEmail()) {
      $user->sendEmailVerificationNotification();
  }

  // verified 미들웨어의 판단
  if ($user instanceof MustVerifyEmail && ! $user->hasVerifiedEmail()) {
      // verification.notice로 redirect
  }
  ```

  interface를 구현하지 않으면 첫 조건이 `false`이므로, 자동 인증 메일을 보내지 않고
  `verified` 미들웨어도 인증 여부를 검사하지 않은 채 다음 라우트로 통과시킨다.
- trait은 `hasVerifiedEmail()`, `markEmailAsVerified()`,
  `sendEmailVerificationNotification()`의 실제 코드를 제공한다. User의 부모 클래스인
  `Illuminate\Foundation\Auth\User`가 이 trait을 사용하므로 User는 이 메서드를 상속받는다.
- 따라서 trait으로부터 물려받은 메서드를 Controller에서 직접 호출하는 것은 interface 없이도
  가능하다. 그러나 interface가 없으면 Laravel은 해당 User를 이메일 인증 대상으로 인식하지
  않으므로 자동 발송과 `verified` 접근 제한을 적용하지 않는다.

`MustVerifyEmail` interface를 구현한 User는 회원가입부터 보호 라우트 접근까지 다음 흐름이 연결된다.

```text
회원가입
→ Registered 이벤트
→ Laravel 기본 Listener가 MustVerifyEmail 구현 User인지 확인
→ 인증 이메일 자동 발송

미인증 사용자가 verified 라우트 접근
→ verified 미들웨어가 MustVerifyEmail 구현 여부와 email_verified_at 확인
→ 미인증이면 verification.notice 라우트로 이동
```

- `EmailVerificationRequest`는 이메일 인증 링크에서 현재 사용자 ID와 `{id}`, 현재 이메일의
  SHA-1 해시와 `{hash}`가 일치하는지 확인하는 Form Request다.
- 라우트의 `signed` 미들웨어는 링크의 서명과 만료 여부를 확인한다.
- `sendEmailVerificationNotification()`은 현재 사용자에게 서명된 이메일 인증 링크를 전송한다.
- `markEmailAsVerified()`는 `email_verified_at`에 현재 시각을 저장한다. `Verified` 이벤트는
  이 인증 완료 사실을 Listener에 알린다.
- Migration은 데이터베이스 구조 변경을 코드로 기록한다. `users.user_type`은
  `buyer`, `seller`만 허용하는 enum이고 기본값은 `buyer`다.
- `Rules\Password::defaults()`는 애플리케이션의 기본 비밀번호 규칙을 적용한다.

## Blade 폼과 검증 오류

- `@csrf`는 POST 폼에 CSRF 토큰을 넣어 다른 사이트가 사용자를 사칭해 요청하는 것을
  막는다.
- `@vite([...])`는 Vite가 빌드한 CSS와 JavaScript 자산을 Blade 뷰에 연결한다.
- `@checked(조건)`은 조건이 참일 때 radio 또는 checkbox에 `checked` 속성을 출력한다.
- 검증 실패 후 Laravel은 redirect하며 이전 입력값과 오류를 세션 플래시 데이터로
  잠시 저장한다.

```php
[
    '_old_input' => [
        'name' => '입력한 이름',
        'email' => '입력한 이메일',
    ],
    'errors' => ViewErrorBag {
        'default' => MessageBag {
            'name' => ['이름은 필수입니다.'],
        },
    },
]
```

- `withInput()`은 입력값을 `_old_input`에 플래시하고, `old('name')`은
  `_old_input.name`을 읽어 폼에 다시 출력한다.
- `withErrors()`는 오류를 `errors`에 플래시한다. `ShareErrorsFromSession`
  미들웨어가 이를 모든 Blade 뷰의 `$errors` 변수로 공유한다.
- `@error('name')`은 `$errors`의 기본 오류 가방에서 `name` 오류가 있을 때만
  블록을 출력한다. 블록 안의 `$message`는 해당 필드의 첫 오류 메시지다.
- `back()`은 이전 URL로 redirect하는 응답을 만들고, `with('status', $value)`는
  상태 메시지를 다음 요청까지 세션 플래시에 저장한다.

## Laravel 번역

- `__($key)`와 `trans($key)`는 번역 키에 맞는 현재 locale의 문장을 반환한다.
  `__()`는 짧은 표기이고 `trans()`는 기존 표기다.
- 키의 첫 부분은 번역 파일 그룹이다. `auth.failed`는 `lang/ko/auth.php`의
  `failed`, `validation.required`는 `lang/ko/validation.php`의 `required`,
  `passwords.sent`는 `lang/ko/passwords.php`의 `sent`를 찾는다.
- 키를 찾지 못하면 Laravel은 보통 키 문자열 자체를 반환한다.
- Laravel 프레임워크는 `vendor/laravel/framework/.../lang/en`에 기본 영어
  인증 메시지를 제공한다. 한국어 메시지는 Composer가 관리하는 `vendor`가 아니라
  프로젝트의 `lang/ko`에 정의한다.

### 검증 언어 파일

- `lang/{locale}/validation.php`는 검증 규칙의 언어별 오류 메시지를 정의한다.
  `Request::validate()`와 Form Request 검증이 실패하면 Laravel이 이 파일을 자동으로
  조회하므로, 코드에서 `__('validation.required')`를 직접 호출할 필요는 없다.
- `:attribute`, `:max`, `:min`은 Laravel이 필드명과 검증 규칙 값으로 바꾸는 치환
  문자열이다. `attributes` 배열은 영어 필드명을 사람이 읽을 한국어 필드명으로 바꾼다.

## Password Broker

- Password Broker는 비밀번호 재설정 토큰 생성·저장·만료 확인·알림 전송을 처리하는
  Laravel 기능이다.
- `Password::sendResetLink($request->only('email'))`는 `['email' => '...']` 형태의
  배열을 받아 해당 이메일 사용자에게 재설정 링크를 보낸다.
- 결과는 `passwords.sent`, `passwords.user`, `passwords.throttled` 같은 상태 키다.
  `__($status)`는 이를 `lang/ko/passwords.php`의 한국어 메시지로 변환한다.
- `Password::reset()`은 토큰과 이메일을 확인한 경우에만 전달한 콜백을 실행한다.
  성공 상태 키는 `passwords.reset`이다.
- `$request->route('token')`은 현재 요청 URL의 `{token}` 경로 파라미터를 읽는다.
  비밀번호 재설정 폼은 이 값을 hidden input으로 다시 전송해 재설정 요청에 포함한다.
- `forceFill()`은 `$fillable` 제한을 거치지 않고 속성을 채운다. 비밀번호 재설정처럼
  인증 관련 필드를 명시적으로 갱신할 때 사용한다.
- 새 `remember_token`을 만들면 이전 로그인 유지 쿠키는 더 이상 유효하지 않게 된다.
- `PasswordReset`은 비밀번호 재설정 완료를 Listener에 알리는 Laravel 기본 인증 이벤트다.
