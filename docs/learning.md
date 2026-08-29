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

## 비밀번호 재확인

- `Auth::guard('web')->validate($credentials)`는 입력한 인증 정보만 검증하고 새 로그인
  세션을 만들지 않는다. 이미 로그인한 사용자가 민감한 작업 전에 현재 비밀번호를 다시
  확인할 때 사용한다.
- 확인에 성공하면 `auth.password_confirmed_at` 세션 키에 Unix timestamp를 저장한다.
  `password.confirm` 미들웨어는 이 시각과 현재 시각의 차이가 설정된 유효 시간을 넘었는지
  계산하고, 넘었다면 `password.confirm` 라우트로 redirect한다.

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

## Migration과 Eloquent 관계

- Migration은 PHP 코드로 테이블·컬럼·외래 키를 만들고 변경하는 파일이다. `Schema::create('products', function (Blueprint $table) { ... })`의 콜백 안에서 `$table`로 컬럼을 정의한다.
- `$table->foreignId('store_id')`는 `store_id` unsigned BIGINT 컬럼만 만든다. 메서드 이름에 `foreign`이 포함되지만, `foreignId()`만으로는 데이터베이스 외래 키 제약이 생성되지 않는다.
- 이어지는 `constrained()`가 참조 테이블의 `id`를 가리키는 실제 외래 키 제약을 추가한다. 인자를 생략하면 `store_id`에서 `stores`를 추론하고, `constrained('categories')`처럼 인자를 전달하면 참조 테이블을 직접 지정한다.
- `nullable()`은 NULL을 허용한다. 예를 들어 최상위 카테고리는 부모가 없으므로 `parent_id`가 NULL일 수 있다.
- `parent_id`는 이름만으로 `parents` 테이블을 추론하므로, 같은 `categories` 테이블을 가리키게 하려면 `constrained('categories')`처럼 테이블명을 지정한다.

```php
$table->foreignId('parent_id')
    ->nullable()
    ->constrained('categories');
```

- `cascadeOnDelete()`는 참조 대상 행을 실제 DELETE할 때 연결된 행도 함께 DELETE한다. `nullOnDelete()`는 연결된 행을 남기고 외래 키 값만 NULL로 바꾼다.
- Eloquent 관계 메서드는 모델 사이를 조회하는 쿼리 규칙이다. 외래 키 제약을 만드는 문법이 아니므로 Migration에도 따로 외래 키를 작성해야 한다.

```php
// 현재 Category의 parent_id로 부모 Category 한 건을 조회한다
public function parent(): BelongsTo
{
    return $this->belongsTo(self::class, 'parent_id');
}

// 다른 Category 중 parent_id가 현재 Category의 id인 여러 건을 조회한다
public function children(): HasMany
{
    return $this->hasMany(self::class, 'parent_id');
}
```

- `belongsTo()`는 현재 모델에 외래 키가 있을 때 사용한다. 위 `parent()`에서는 현재 Category의 `parent_id`를 사용한다.
- `hasOne()`과 `hasMany()`는 연결된 다른 테이블에 현재 모델을 가리키는 외래 키가 있을 때 사용한다. 결과가 한 건이면 `hasOne()`, 여러 건이면 `hasMany()`다.
- `belongsToMany()`는 중간 테이블(pivot table)을 거치는 다대다 관계다. `Product::categories()`는 기본 규칙에 따라 `category_product`의 `product_id`, `category_id`를 사용한다.
- `hasManyThrough()`는 중간 모델을 한 번 거치는 관계다. `User::products()`는 User → Store → Product 순서로 상품을 조회한다.
- `$table->softDeletesDatetime()`는 `deleted_at` 컬럼을 만든다. 모델에서 `use SoftDeletes`를 선언하면 `delete()`는 행을 실제로 지우지 않고 `deleted_at`에 시각을 저장한다. 따라서 Soft Delete에는 `cascadeOnDelete()`가 실행되지 않는다.

## Laravel 라우팅

- `Route` Facade는 요청 URL과 그 요청을 처리할 로직을 연결하는 Laravel 라우팅 기능이다.
- `Route::get('/', $action)`은 브라우저가 `/` URL에 GET 요청을 보냈을 때 실행할 처리 로직을 등록한다.
- `->name('home.index')`는 라우트에 이름을 붙인다. 이후 URL을 직접 작성하지 않고 `route('home.index')`로 홈 URL을 생성할 수 있다.

## Eloquent 대량 할당과 Attribute Casting

- 모델의 `$guarded = []`는 Eloquent 대량 할당에서 막을 컬럼이 없다는 뜻이다. 요청 검증을 마친 설정 배열 전체를 모델에 저장할 때 사용할 수 있지만, 검증되지 않은 요청 전체를 전달하면 위험하므로 주의해야 한다.
- `$casts = ['categories' => 'array']`는 데이터베이스 JSON 문자열을 읽을 때 PHP 배열로 변환하고, 배열을 저장할 때 다시 JSON으로 변환한다.

## Database Seeder와 updateOrCreate

- Seeder는 개발·학습·테스트에 필요한 초기 데이터를 코드로 반복 생성하는 Laravel 기능이다. 기본 `DatabaseSeeder::run()`은 `php artisan db:seed` 실행 시 호출된다.
- `WithoutModelEvents` Trait은 Seeder 실행 중 Eloquent 모델 이벤트와 연결된 Listener가 실행되지 않게 한다. 초기 데이터 생성 과정에서 알림 같은 부수 효과를 방지할 때 사용한다.
- `updateOrCreate($conditions, $values)`는 첫 번째 배열 조건과 일치하는 모델이 있으면 두 번째 배열 값으로 갱신하고, 없으면 두 배열을 합친 값으로 새 모델을 만든다. 이메일을 조건으로 사용하면 Seeder를 여러 번 실행해도 같은 계정이 중복 생성되지 않는다.
- User 모델의 `'password' => 'hashed'` cast는 Seeder가 전달한 평문 비밀번호도 저장 전에 자동으로 해시한다.
- `forceFill()`은 모델의 `$fillable` 제한을 우회해 속성을 채운다. 이번 Seeder에서는 외부 요청값이 아니라 코드에 명시한 `email_verified_at`을 인증 완료 상태로 저장하기 위해 사용한다.

## Eloquent Eager Loading과 관계 집계

- `with('primaryImage')`는 Product를 조회할 때 대표 이미지 관계도 미리 가져오는 eager loading이다. 반복문에서 상품마다 관계 쿼리를 실행하는 N+1 문제를 방지한다.
- `withCount('products')`는 `products` 관계의 모델 전체를 가져오지 않고 개수만 계산해 `{관계명}_count` 속성으로 추가한다. 따라서 Category에서는 `$category->products_count`로 연결 상품 수를 읽는다.

```php
$category = Category::withCount('products')->first();

// Category 속성 예시
// id: 1
// name: "전자제품"
// products_count: 25
```

- `withAvg('reviews', 'rating')`는 `reviews` 관계의 `rating` 평균을 계산해 `{관계명}_avg_{컬럼명}` 속성으로 추가한다. 따라서 Product에서는 `$product->reviews_avg_rating`으로 평균 평점을 읽는다. 리뷰가 없으면 이 값은 `null`이다.

```php
$product = Product::withAvg('reviews', 'rating')->first();

// Product 속성 예시
// id: 10
// name: "키보드"
// reviews_avg_rating: 4.5
```

- `with('products')`는 Product 모델들을 실제 관계 데이터로 가져오지만, `withCount('products')`는 개수만 가져온다. 목록 화면에서 상품 내용은 필요 없고 개수만 표시할 때 `withCount()`가 더 적합하다.
- `withCount()`와 `withAvg()`는 각 모델마다 반복 쿼리를 실행하지 않고 집계값을 기본 조회에 포함하므로 N+1 문제를 피하면서 개수와 평균을 표시할 수 있다.

## Eloquent 관계 조건

- `whereHas('reviews')`는 `reviews` 관계가 하나 이상 존재하는 Product만 조회한다. 관계 모델을 가져오는 것이 아니라 관계의 존재 여부로 Product를 필터링한다.

```php
// 리뷰가 하나 이상 있는 상품만 조회한다
$products = Product::whereHas('reviews')->get();
```

- 두 번째 인자로 콜백을 전달하면 관계가 존재하면서 콜백의 조건도 만족해야 한다. 아래 코드는 5점 리뷰가 최소 하나 있는 상품을 조회한다. 모든 리뷰가 5점이어야 한다는 뜻은 아니다.

```php
$products = Product::whereHas('reviews', function ($query) {
    $query->where('rating', 5);
})->get();
```

- `whereHas()`는 부모 모델을 관계 조건으로 필터링하고, `with()`는 관계 데이터를 실제로 가져온다. 필터링한 상품과 리뷰 데이터가 모두 필요하면 두 메서드를 함께 사용한다.

```php
$products = Product::with('reviews')
    ->whereHas('reviews', function ($query) {
        $query->where('rating', 5);
    })
    ->get();
```

- `whereHas('categories', $callback)`도 같은 원리로, 콜백 조건을 만족하는 Category가 하나 이상 연결된 Product만 조회한다.

## Eloquent 동적 Where

- `whereIsFeatured(true)` 같은 Eloquent 동적 where는 메서드 이름의 `IsFeatured`를 `is_featured` 컬럼으로 변환해 조건을 적용한다.

## Query Builder 결과 제한과 정렬

- `get()` 전에 사용하는 `take(15)`는 SQL의 `LIMIT 15`처럼 데이터베이스 조회 결과를 최대 15개로 제한하며, Query Builder의 `limit(15)`와 같은 역할을 한다.
- `latest()`는 기본적으로 `created_at`을 내림차순 정렬해 최근 생성된 행부터 조회한다.
