<!DOCTYPE html>
{{-- app()->getLocale()은 현재 애플리케이션 locale을 읽어 HTML 언어 속성에 반영한다 --}}
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    {{-- config()는 config/app.php의 애플리케이션 이름을 읽는다 --}}
    <title>로그인 | {{ config('app.name', 'Bmazon') }}</title>
    {{-- @vite는 Vite가 빌드한 CSS와 JavaScript 자산을 페이지에 연결한다 --}}
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-slate-100 text-slate-900">
    <main class="flex min-h-screen items-center justify-center px-4 py-12">
        <section class="w-full max-w-md rounded-3xl bg-white p-8 shadow-xl shadow-slate-200 sm:p-10">
            <div class="mb-8 text-center">
                <p class="mb-3 text-sm font-semibold uppercase text-indigo-600">Bmazon</p>
                <h1 class="text-3xl font-bold tracking-tight">다시 오신 것을 환영합니다</h1>
                <p class="mt-3 text-sm text-slate-500">
                    아직 계정이 없으신가요?
                    {{-- route()는 URL을 직접 작성하지 않고 이름이 register인 라우트의 URL을 생성한다 --}}
                    <a href="{{ route('register') }}" class="font-semibold text-indigo-600 hover:text-indigo-500">회원가입</a>
                </p>
            </div>

            {{-- session('status')는 이전 요청이 세션에 저장한 상태 메시지를 읽는다 --}}
            @if (session('status'))
                <div class="mb-6 rounded-xl bg-emerald-50 px-4 py-3 text-sm text-emerald-700" role="status">
                    {{ session('status') }}
                </div>
            @endif

            <form method="POST" action="{{ route('login') }}" class="space-y-6">
                {{-- @csrf는 세션의 CSRF 토큰을 폼에 넣어 위조된 POST 요청을 막는다 --}}
                @csrf

                <div>
                    <label for="email" class="mb-2 block text-sm font-semibold text-slate-700">이메일</label>
                    <input
                        id="email"
                        name="email"
                        type="email"
                        {{-- old()는 검증 실패 후 세션에 잠시 저장된 이전 이메일을 다시 보여준다 --}}
                        value="{{ old('email') }}"
                        required
                        autofocus
                        autocomplete="email"
                        class="w-full rounded-xl border border-slate-300 px-4 py-3 outline-none transition focus:border-indigo-500 focus:ring-4 focus:ring-indigo-100"
                        placeholder="you@example.com"
                    >
                    {{-- @error는 세션의 검증 오류에서 email 필드를 찾아 오류 메시지를 출력한다 --}}
                    @error('email')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <div class="mb-2 flex items-center justify-between gap-4">
                        <label for="password" class="block text-sm font-semibold text-slate-700">비밀번호</label>
                        <a href="{{ route('password.request') }}" class="text-sm font-semibold text-indigo-600 hover:text-indigo-500">비밀번호 찾기</a>
                    </div>
                    <input
                        id="password"
                        name="password"
                        type="password"
                        required
                        autocomplete="current-password"
                        class="w-full rounded-xl border border-slate-300 px-4 py-3 outline-none transition focus:border-indigo-500 focus:ring-4 focus:ring-indigo-100"
                        placeholder="비밀번호"
                    >
                    @error('password')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <label class="flex items-center gap-3 text-sm text-slate-600">
                    <input
                        id="remember"
                        name="remember"
                        type="checkbox"
                        value="1"
                        class="h-4 w-4 rounded border-slate-300 accent-indigo-600"
                    >
                    로그인 상태 유지
                </label>

                <button
                    type="submit"
                    class="w-full rounded-xl bg-indigo-600 px-4 py-3 font-semibold text-white transition hover:bg-indigo-700 focus:outline-none focus:ring-4 focus:ring-indigo-200"
                >
                    로그인
                </button>
            </form>
        </section>
    </main>
</body>
</html>
