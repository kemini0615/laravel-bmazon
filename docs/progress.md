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
- [x] `lang/ko/validation.php` 추가
- [x] `lang/ko/auth.php` 추가
- [ ] 비밀번호 재설정과 이메일 인증 Controller 구현
- [ ] 인증 뷰 구현
- [ ] 인증 테스트와 동작 검증
- [ ] 관리자 인증 구현

## 최근 확인

- `php -l routes/web.php` 통과
- `php -l app/Http/Requests/Auth/LoginRequest.php` 통과
- `php -l app/Http/Controllers/Auth/RegisteredUserController.php` 통과
- 테스트는 아직 구현되지 않은 `EmailVerificationPromptController` 때문에 라우트
  로딩 단계에서 확인 필요
