# 진행 상황

## 인증 도메인 — 완료

- [x] 사용자 회원가입, 로그인, 로그아웃
- [x] 비밀번호 재설정과 비밀번호 재확인
- [x] 이메일 인증과 `verified` 접근 제어
- [x] 인증 뷰와 한국어 검증·인증 번역
- [x] Buyer·Seller 최소 대시보드
- [ ] TODO: Seller 도메인에서 역할 미들웨어를 구현한 뒤 Buyer·Seller Controller의 임시 `user_type` 분기 교체

## Public / Guest 도메인 — 진행 중

### 홈 — 완료

- [x] 임시 홈 응답을 실제 `HomeController@index` 라우트로 교체
- [x] 홈 데이터 Controller와 최소 Tailwind 뷰 작성
- [x] 홈 의존 `Slider`, `HeroBanner`, `PopularCategory`, `FlashSale`, `ProductSection` Migration과 모델 작성

### 상품 기반 — 진행 중

- [x] `categories` Migration과 `Category` 모델 작성
- [x] Product 선행 `Store`, `Brand` Migration과 모델 작성
- [x] `products`, `category_product` Migration과 `Product` 기본 모델 작성
- [x] 현재 모델의 Eloquent 관계와 기본 외래 키 정의 점검
- [x] Product 이미지·리뷰 Migration, 모델, 관계 작성
- [ ] 상품 상세 구현 시 Product 옵션 관계 작성

### 남은 Public / Guest 기능

- [ ] 상품 목록·상세, 벤더 목록, 플래시세일, 문의
