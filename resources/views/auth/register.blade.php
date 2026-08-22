<!DOCTYPE html>
{{-- app()->getLocale()은 현재 애플리케이션 locale을 읽어 HTML 언어 속성에 반영한다 --}}
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    {{-- config()는 config/app.php의 애플리케이션 이름을 읽는다 --}}
    <title>회원가입 | {{ config('app.name', 'Bmazon') }}</title>
    {{-- @vite는 Vite가 빌드한 CSS와 JavaScript 자산을 페이지에 연결한다 --}}
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-slate-100 text-slate-900">
    <main class="flex min-h-screen items-center justify-center px-4 py-12">
        <section class="w-full max-w-2xl rounded-3xl bg-white p-8 shadow-xl shadow-slate-200 sm:p-10">
            <div class="mb-8 text-center">
                <p class="mb-3 text-sm font-semibold uppercase text-indigo-600">Bmazon</p>
                <h1 class="text-3xl font-bold tracking-tight">계정 만들기</h1>
                <p class="mt-3 text-sm text-slate-500">
                    이미 계정이 있으신가요?
                    {{-- route()는 URL을 직접 작성하지 않고 이름이 login인 라우트의 URL을 생성한다 --}}
                    <a href="{{ route('login') }}" class="font-semibold text-indigo-600 hover:text-indigo-500">로그인</a>
                </p>
            </div>

            <form method="POST" action="{{ route('register') }}" class="space-y-6">
                {{-- @csrf는 세션의 CSRF 토큰을 폼에 넣어 위조된 POST 요청을 막는다 --}}
                @csrf

                <div>
                    <label for="name" class="mb-2 block text-sm font-semibold text-slate-700">이름</label>
                    {{-- old()는 검증 실패 후 세션에 잠시 저장된 이전 입력값을 다시 보여준다 --}}
                    <input
                        id="name"
                        name="name"
                        type="text"
                        value="{{ old('name') }}"
                        required
                        autofocus
                        autocomplete="name"
                        class="w-full rounded-xl border border-slate-300 px-4 py-3 outline-none transition focus:border-indigo-500 focus:ring-4 focus:ring-indigo-100"
                        placeholder="이름을 입력하세요"
                    >
                    {{-- @error는 세션의 검증 오류에서 name 필드를 찾아 오류 메시지를 출력한다 --}}
                    @error('name')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="email" class="mb-2 block text-sm font-semibold text-slate-700">이메일</label>
                    <input
                        id="email"
                        name="email"
                        type="email"
                        value="{{ old('email') }}"
                        required
                        autocomplete="email"
                        class="w-full rounded-xl border border-slate-300 px-4 py-3 outline-none transition focus:border-indigo-500 focus:ring-4 focus:ring-indigo-100"
                        placeholder="you@example.com"
                    >
                    @error('email')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="grid gap-6 sm:grid-cols-2">
                    <div>
                        <label for="password" class="mb-2 block text-sm font-semibold text-slate-700">비밀번호</label>
                        <input
                            id="password"
                            name="password"
                            type="password"
                            required
                            autocomplete="new-password"
                            class="w-full rounded-xl border border-slate-300 px-4 py-3 outline-none transition focus:border-indigo-500 focus:ring-4 focus:ring-indigo-100"
                            placeholder="비밀번호"
                        >
                        @error('password')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="password_confirmation" class="mb-2 block text-sm font-semibold text-slate-700">비밀번호 확인</label>
                        <input
                            id="password_confirmation"
                            name="password_confirmation"
                            type="password"
                            required
                            autocomplete="new-password"
                            class="w-full rounded-xl border border-slate-300 px-4 py-3 outline-none transition focus:border-indigo-500 focus:ring-4 focus:ring-indigo-100"
                            placeholder="비밀번호 재입력"
                        >
                        @error('password_confirmation')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <fieldset>
                    <legend class="mb-3 text-sm font-semibold text-slate-700">계정 유형</legend>
                    <div class="grid gap-3 sm:grid-cols-2">
                        <label class="flex cursor-pointer items-start gap-3 rounded-2xl border border-slate-200 p-4 transition has-[:checked]:border-indigo-500 has-[:checked]:bg-indigo-50">
                            <input
                                type="radio"
                                name="user_type"
                                value="buyer"
                                {{-- @checked는 조건이 참일 때 radio에 checked 속성을 출력한다 --}}
                                @checked(old('user_type', 'buyer') === 'buyer')
                                required
                                class="mt-1 accent-indigo-600"
                            >
                            <span>
                                <span class="block font-semibold">Buyer</span>
                                <span class="mt-1 block text-sm text-slate-500">상품을 둘러보고 구매합니다.</span>
                            </span>
                        </label>

                        <label class="flex cursor-pointer items-start gap-3 rounded-2xl border border-slate-200 p-4 transition has-[:checked]:border-indigo-500 has-[:checked]:bg-indigo-50">
                            <input
                                type="radio"
                                name="user_type"
                                value="seller"
                                @checked(old('user_type') === 'seller')
                                required
                                class="mt-1 accent-indigo-600"
                            >
                            <span>
                                <span class="block font-semibold">Seller</span>
                                <span class="mt-1 block text-sm text-slate-500">상품을 등록하고 판매합니다.</span>
                            </span>
                        </label>
                    </div>
                    @error('user_type')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </fieldset>

                <button
                    type="submit"
                    class="w-full rounded-xl bg-indigo-600 px-4 py-3 font-semibold text-white transition hover:bg-indigo-700 focus:outline-none focus:ring-4 focus:ring-indigo-200"
                >
                    회원가입
                </button>
            </form>
        </section>
    </main>
</body>
</html>
