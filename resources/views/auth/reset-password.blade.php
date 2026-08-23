<!DOCTYPE html>
{{-- app()->getLocale()은 현재 애플리케이션 locale을 읽어 HTML 언어 속성에 반영한다 --}}
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    {{-- config()는 config/app.php의 애플리케이션 이름을 읽는다 --}}
    <title>새 비밀번호 설정 | {{ config('app.name', 'Bmazon') }}</title>
    {{-- @vite는 Vite가 빌드한 CSS와 JavaScript 자산을 페이지에 연결한다 --}}
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-slate-100 text-slate-900">
    <main class="flex min-h-screen items-center justify-center px-4 py-12">
        <section class="w-full max-w-md rounded-3xl bg-white p-8 shadow-xl shadow-slate-200 sm:p-10">
            <div class="mb-8 text-center">
                <p class="mb-3 text-sm font-semibold uppercase text-indigo-600">Bmazon</p>
                <h1 class="text-3xl font-bold tracking-tight">새 비밀번호 설정</h1>
                <p class="mt-3 text-sm leading-6 text-slate-500">새 비밀번호를 입력해 계정을 다시 보호하세요</p>
            </div>

            <form method="POST" action="{{ route('password.store') }}" class="space-y-6">
                {{-- @csrf는 세션의 CSRF 토큰을 폼에 넣어 위조된 POST 요청을 막는다 --}}
                @csrf

                {{-- $request->route('token')은 재설정 URL의 token 경로 값을 읽어 POST 요청에 함께 전송한다 --}}
                <input type="hidden" name="token" value="{{ $request->route('token') }}">

                <div>
                    <label for="email" class="mb-2 block text-sm font-semibold text-slate-700">이메일</label>
                    <input
                        id="email"
                        name="email"
                        type="email"
                        {{-- old()는 검증 실패 후 입력값을 우선 사용하고, 없으면 링크에 포함된 이메일을 기본값으로 사용한다 --}}
                        value="{{ old('email', $request->email) }}"
                        required
                        autofocus
                        autocomplete="email"
                        class="w-full rounded-xl border border-slate-300 px-4 py-3 outline-none transition focus:border-indigo-500 focus:ring-4 focus:ring-indigo-100"
                        placeholder="you@example.com"
                    >
                    {{-- @error는 세션의 오류에서 email 필드를 찾아 오류 메시지를 출력한다 --}}
                    @error('email')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="password" class="mb-2 block text-sm font-semibold text-slate-700">새 비밀번호</label>
                    <input
                        id="password"
                        name="password"
                        type="password"
                        required
                        autocomplete="new-password"
                        class="w-full rounded-xl border border-slate-300 px-4 py-3 outline-none transition focus:border-indigo-500 focus:ring-4 focus:ring-indigo-100"
                        placeholder="새 비밀번호"
                    >
                    @error('password')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="password_confirmation" class="mb-2 block text-sm font-semibold text-slate-700">새 비밀번호 확인</label>
                    <input
                        id="password_confirmation"
                        name="password_confirmation"
                        type="password"
                        required
                        autocomplete="new-password"
                        class="w-full rounded-xl border border-slate-300 px-4 py-3 outline-none transition focus:border-indigo-500 focus:ring-4 focus:ring-indigo-100"
                        placeholder="새 비밀번호를 다시 입력하세요"
                    >
                    @error('password_confirmation')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <button
                    type="submit"
                    class="w-full rounded-xl bg-indigo-600 px-4 py-3 font-semibold text-white transition hover:bg-indigo-700 focus:outline-none focus:ring-4 focus:ring-indigo-200"
                >
                    비밀번호 재설정
                </button>
            </form>
        </section>
    </main>
</body>
</html>
