# 진행 상황

## 인증 도메인

- [x] `routes/auth.php` 작성
- [x] `routes/web.php`에서 `routes/auth.php` 연결
- [x] `app/Http/Requests/Auth/LoginRequest.php` 작성
- [x] `app/Http/Controllers/Auth/AuthenticatedSessionController.php` 작성
- [x] `app/Http/Controllers/Auth/RegisteredUserController.php` 작성
- [x] `database/migrations/0001_01_01_000000_create_users_table.php`에 `user_type` 추가
- [x] `resources/views/auth/register.blade.php` 작성
- [x] `resources/views/auth/login.blade.php` 작성
- [x] `resources/views/auth/forgot-password.blade.php` 작성
- [x] `resources/views/auth/reset-password.blade.php` 작성
- [x] `resources/views/auth/verify-email.blade.php` 작성
- [x] `resources/views/auth/confirm-password.blade.php` 작성
- [x] `lang/ko/validation.php` 추가
- [x] `lang/ko/auth.php` 추가
- [x] `lang/ko/passwords.php` 추가
- [x] `app/Http/Controllers/Auth/PasswordResetLinkController.php` 작성
- [x] `app/Http/Controllers/Auth/NewPasswordController.php` 작성
- [x] `app/Http/Controllers/Auth/EmailVerificationPromptController.php` 작성
- [x] `app/Http/Controllers/Auth/VerifyEmailController.php` 작성
- [x] `app/Http/Controllers/Auth/EmailVerificationNotificationController.php` 작성
- [x] `app/Http/Controllers/Auth/ConfirmablePasswordController.php` 작성
- [ ] 비밀번호 재설정과 이메일 인증 Controller 구현
- [ ] 인증 뷰 구현
- [ ] 인증 테스트와 동작 검증
- [ ] 관리자 인증 구현

## 최소 대시보드

- [x] `User/Buyer` 대시보드 라우트와 화면 작성
- [x] `User/Seller` 대시보드 라우트와 화면 작성
- [x] Buyer·Seller 대시보드에 `auth`, `verified` 미들웨어 적용
- [ ] TODO: Seller 도메인에서 역할 미들웨어를 구현한 뒤 Buyer·Seller Controller의 임시 `user_type` 분기 교체

## 최근 확인

- `php -l routes/web.php` 통과
- `php -l app/Http/Requests/Auth/LoginRequest.php` 통과
- `php -l app/Http/Controllers/Auth/RegisteredUserController.php` 통과
- 테스트는 아직 구현되지 않은 `EmailVerificationPromptController` 때문에 라우트
  로딩 단계에서 확인 필요
